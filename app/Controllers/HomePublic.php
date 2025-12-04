<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class HomePublic extends Controller
{
    public function index()
    {
        $content = view('home/pages/landing');
        return view('home/layout', [
            'content' => $content,
            'bodyClass' => 'index-page',
            'bodyAttrs' => 'data-aos-easing="ease-in-out" data-aos-duration="600" data-aos-delay="0"',
            'headerClass' => 'fixed-top',
        ]);
    }

    public function page(string $slug)
    {
        $viewPath = 'home/pages/' . $slug;
        if (!is_file(APPPATH . 'Views/' . $viewPath . '.php')) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }
        $content = view($viewPath);
        $bodyClass = $slug === 'register' ? 'blog-details-page' : 'index-page';
        $bodyAttrs = $slug === 'register' ? '' : 'data-aos-easing="ease-in-out" data-aos-duration="600" data-aos-delay="0"';
        $headerClass = $slug === 'register' ? 'sticky-top' : 'fixed-top';
        return view('home/layout', [
            'content' => $content,
            'bodyClass' => $bodyClass,
            'bodyAttrs' => $bodyAttrs,
            'headerClass' => $headerClass,
        ]);
    }

    public function registerSubmit()
    {
        $request = $this->request;
        $session = session();
        $rules = [
            'fullName' => 'required|min_length[3]',
            'birthDate' => 'required',
            'email' => 'required|valid_email',
            'phone' => 'required',
            'agree' => 'required',
        ];
        if (!$this->validate($rules)) {
            $session->setFlashdata('error', 'Lengkapi formulir pendaftaran dengan benar');
            return redirect()->back()->withInput();
        }
        $nama = trim((string) $request->getPost('fullName'));
        $tgl = trim((string) $request->getPost('birthDate'));
        $email = trim((string) $request->getPost('email'));
        $phone = trim((string) $request->getPost('phone'));
        $minBirth = strtotime('-18 years');
        $birthTs = strtotime($tgl);
        if ($birthTs === false || $birthTs > $minBirth) {
            $session->setFlashdata('error', 'Usia minimal 18 tahun dari tanggal daftar');
            return redirect()->back()->withInput();
        }
        $db = \Config\Database::connect();
        try {
            $db->table('anggota')->insert([
                'no_anggota' => null,
                'nama' => $nama,
                'tanggal_lahir' => $tgl,
                'email' => $email,
                'no_telepon' => $phone,
                'status' => 'nonaktif',
                'jenis_anggota' => 'aktif',
                'tanggal_gabung' => null,
            ]);
            $idAnggota = (int) $db->insertID();
            $username = $phone;
            if ((int) $db->table('users')->where('username', $username)->countAllResults() > 0) {
                $session->setFlashdata('error', 'Nomor telepon sudah terdaftar');
                return redirect()->back()->withInput();
            }
            $db->table('users')->insert([
                'username' => $username,
                'password_hash' => null,
                'role' => 'anggota',
                'id_anggota' => $idAnggota,
                'status' => 'nonaktif',
            ]);

            $tables = $db->listTables();
            if (!in_array('user_activation', $tables, true)) {
                $db->query('CREATE TABLE IF NOT EXISTS user_activation (
                    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                    id_user INT UNSIGNED NOT NULL,
                    token VARCHAR(64) NOT NULL UNIQUE,
                    expires_at DATETIME NULL,
                    used_at DATETIME NULL,
                    INDEX(id_user)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4');
            }
            $idUser = (int) $db->table('users')->select('id_user')->where('id_anggota', $idAnggota)->orderBy('id_user', 'DESC')->limit(1)->get()->getRowArray()['id_user'];
            $token = bin2hex(random_bytes(24));
            $expire = date('Y-m-d H:i:s', time() + 3600);
            $db->table('user_activation')->insert([
                'id_user' => $idUser,
                'token' => $token,
                'expires_at' => $expire,
            ]);
            $base = rtrim(config('App')->baseURL, '/');
            $link = $base . '/activate/' . $token;
            $tplRow = $db->table('waha_templates')->where('slug', 'register')->get()->getRowArray();
            $tpl = (string) ($tplRow['content'] ?? '');
            if ($tpl === '') {
                $tpl = 'Halo {{nama}}, silakan aktivasi akun: {{link}}';
            }
            $msg = str_replace(['{{nama}}', '{{no_anggota}}', '{{link}}'], [$nama, '', $link], $tpl);
            try {
                $skip = (bool) (env('WAHA_SKIP_SSL_VERIFY') ?? false);
                $opts = [
                    'timeout' => 10,
                    'http_errors' => false,
                    'allow_redirects' => ['max' => 5, 'strict' => false, 'referer' => false],
                ];
                if ($skip) {
                    $opts['verify'] = false;
                }
                $client = \Config\Services::curlrequest($opts);
                $envUrl = (string) (env('WAHA_SEND_URL') ?: '');
                $base = (string) (env('WAHA_BASE_URL') ?: '');
                $sendUrl = $envUrl !== '' ? $envUrl : (rtrim($base, '/') !== '' ? rtrim($base, '/') . '/api/sendText' : '');
                $token = (string) (env('WAHA_TOKEN') ?: '');
                $sessionId = (string) (env('WAHA_SESSION') ?: 'default');
                $phoneNum = preg_replace('#\D+#', '', $phone);
                if ($phoneNum !== '' && str_starts_with($phoneNum, '0')) {
                    $phoneNum = '62' . substr($phoneNum, 1);
                }
                if ($phoneNum === '' || ($sendUrl === '' && $base === '')) {
                    log_message('error', 'WA send skipped: URL or phone invalid');
                } else {
                    $headers = ['Content-Type' => 'application/json'];
                    if ($token !== '') {
                        $headers['x-api-key'] = $token;
                        $headers['Authorization'] = 'Bearer ' . $token;
                    }
                    $targets = [];
                    if ($sendUrl !== '') {
                        $targets[] = $sendUrl;
                    } else {
                        $b = rtrim($base, '/');
                        if ($b !== '') {
                            $targets[] = $b . '/api/sendText';
                            $targets[] = $b . '/messages';
                        }
                    }
                    $ok = false;
                    foreach ($targets as $turl) {
                        $payload1 = ['phone' => $phoneNum, 'chatId' => $phoneNum . '@c.us', 'text' => $msg, 'session' => $sessionId];
                        $resp1 = $client->post($turl, ['headers' => $headers, 'json' => $payload1]);
                        $c1 = $resp1->getStatusCode();
                        if ($c1 >= 200 && $c1 < 300) {
                            $ok = true;
                            log_message('info', 'WA send success: status=' . $c1 . ' url=' . $turl);
                            break;
                        }
                        log_message('error', 'WA send failed A: status=' . $c1 . ' url=' . $turl . ' body=' . (string) $resp1->getBody());
                        $payload2 = ['to' => $phoneNum, 'chatId' => $phoneNum . '@c.us', 'text' => $msg, 'session' => $sessionId];
                        $resp2 = $client->post($turl, ['headers' => $headers, 'json' => $payload2]);
                        $c2 = $resp2->getStatusCode();
                        if ($c2 >= 200 && $c2 < 300) {
                            $ok = true;
                            log_message('info', 'WA send success: status=' . $c2 . ' url=' . $turl);
                            break;
                        }
                        log_message('error', 'WA send failed B: status=' . $c2 . ' url=' . $turl . ' body=' . (string) $resp2->getBody());
                        $payload3 = ['phone' => $phoneNum, 'chatId' => $phoneNum . '@c.us', 'message' => $msg, 'session' => $sessionId];
                        $resp3 = $client->post($turl, ['headers' => $headers, 'json' => $payload3]);
                        $c3 = $resp3->getStatusCode();
                        if ($c3 >= 200 && $c3 < 300) {
                            $ok = true;
                            log_message('info', 'WA send success: status=' . $c3 . ' url=' . $turl);
                            break;
                        }
                        log_message('error', 'WA send failed C: status=' . $c3 . ' url=' . $turl . ' body=' . (string) $resp3->getBody());
                        $payload4 = ['phone' => $phoneNum, 'chatId' => $phoneNum . '@c.us', 'message' => $msg, 'session' => $sessionId];
                        $resp4 = $client->post($turl, ['headers' => ['Content-Type' => 'application/x-www-form-urlencoded'] + $headers, 'form_params' => $payload4]);
                        $c4 = $resp4->getStatusCode();
                        if ($c4 >= 200 && $c4 < 300) {
                            $ok = true;
                            log_message('info', 'WA send success: status=' . $c4 . ' url=' . $turl);
                            break;
                        }
                        log_message('error', 'WA send failed D: status=' . $c4 . ' url=' . $turl . ' body=' . (string) $resp4->getBody());
                        if (str_ends_with($turl, '/messages')) {
                            $payload5 = ['chatId' => $phoneNum . '@c.us', 'body' => $msg, 'session' => $sessionId];
                            $resp5 = $client->post($turl, ['headers' => $headers, 'json' => $payload5]);
                            $c5 = $resp5->getStatusCode();
                            if ($c5 >= 200 && $c5 < 300) {
                                $ok = true;
                                log_message('info', 'WA send success: status=' . $c5 . ' url=' . $turl);
                                break;
                            }
                            log_message('error', 'WA send failed E: status=' . $c5 . ' url=' . $turl . ' body=' . (string) $resp5->getBody());
                        }
                    }
                    if (!$ok) {
                        log_message('error', 'WA send final failed for ' . $phoneNum);
                    }
                }
            } catch (\Throwable $e) {
                log_message('error', 'WA send exception: ' . $e->getMessage());
                $m = (string) $e->getMessage();
                if (stripos($m, 'SSL') !== false) {
                    try {
                        $opts2 = ['timeout' => 10, 'http_errors' => false, 'allow_redirects' => ['max' => 5, 'strict' => false, 'referer' => false], 'verify' => false];
                        $client2 = \Config\Services::curlrequest($opts2);
                        $headers2 = ['Content-Type' => 'application/json'];
                        if ($token !== '') {
                            $headers2['x-api-key'] = $token;
                            $headers2['Authorization'] = 'Bearer ' . $token;
                        }
                        $targets = [];
                        $s0 = $sendUrl !== '' ? $sendUrl : (rtrim($base, '/') !== '' ? rtrim($base, '/') . '/api/sendText' : '');
                        if ($s0 !== '') {
                            $targets[] = preg_replace('#^https://#', 'http://', $s0);
                            $targets[] = preg_replace('#^https://#', 'http://', rtrim($base, '/') . '/messages');
                        }
                        $ok2 = false;
                        foreach ($targets as $turl2) {
                            $payload1 = ['phone' => $phoneNum, 'chatId' => $phoneNum . '@c.us', 'text' => $msg, 'session' => $sessionId];
                            $r1 = $client2->post($turl2, ['headers' => $headers2, 'json' => $payload1]);
                            $c1 = $r1->getStatusCode();
                            if ($c1 >= 200 && $c1 < 300) {
                                $ok2 = true;
                                log_message('info', 'WA send success http: status=' . $c1 . ' url=' . $turl2);
                                break;
                            }
                            $payload2 = ['to' => $phoneNum, 'chatId' => $phoneNum . '@c.us', 'text' => $msg, 'session' => $sessionId];
                            $r2 = $client2->post($turl2, ['headers' => $headers2, 'json' => $payload2]);
                            $c2 = $r2->getStatusCode();
                            if ($c2 >= 200 && $c2 < 300) {
                                $ok2 = true;
                                log_message('info', 'WA send success http: status=' . $c2 . ' url=' . $turl2);
                                break;
                            }
                            log_message('error', 'WA http fallback failed: status=' . $c2 . ' url=' . $turl2 . ' body=' . (string) $r2->getBody());
                            $payload3 = ['phone' => $phoneNum, 'chatId' => $phoneNum . '@c.us', 'message' => $msg, 'session' => $sessionId];
                            $r3 = $client2->post($turl2, ['headers' => $headers2, 'json' => $payload3]);
                            $c3 = $r3->getStatusCode();
                            if ($c3 >= 200 && $c3 < 300) {
                                $ok2 = true;
                                log_message('info', 'WA send success http: status=' . $c3 . ' url=' . $turl2);
                                break;
                            }
                            log_message('error', 'WA http fallback failed C: status=' . $c3 . ' url=' . $turl2 . ' body=' . (string) $r3->getBody());
                            $payload4 = ['phone' => $phoneNum, 'chatId' => $phoneNum . '@c.us', 'message' => $msg, 'session' => $sessionId];
                            $r4 = $client2->post($turl2, ['headers' => ['Content-Type' => 'application/x-www-form-urlencoded'] + $headers2, 'form_params' => $payload4]);
                            $c4 = $r4->getStatusCode();
                            if ($c4 >= 200 && $c4 < 300) {
                                $ok2 = true;
                                log_message('info', 'WA send success http: status=' . $c4 . ' url=' . $turl2);
                                break;
                            }
                            log_message('error', 'WA http fallback failed D: status=' . $c4 . ' url=' . $turl2 . ' body=' . (string) $r4->getBody());
                            if (str_ends_with($turl2, '/messages')) {
                                $payload5 = ['chatId' => $phoneNum . '@c.us', 'body' => $msg, 'session' => $sessionId];
                                $r5 = $client2->post($turl2, ['headers' => $headers2, 'json' => $payload5]);
                                $c5 = $r5->getStatusCode();
                                if ($c5 >= 200 && $c5 < 300) {
                                    $ok2 = true;
                                    log_message('info', 'WA send success http: status=' . $c5 . ' url=' . $turl2);
                                    break;
                                }
                                log_message('error', 'WA http fallback failed E: status=' . $c5 . ' url=' . $turl2 . ' body=' . (string) $r5->getBody());
                            }
                        }
                        if (!$ok2) {
                            log_message('error', 'WA http fallback final failed');
                        }
                    } catch (\Throwable $e2) {
                        log_message('error', 'WA send http fallback exception: ' . $e2->getMessage());
                    }
                }
            }
            $session->setFlashdata('message', 'Pendaftaran berhasil. Link aktivasi telah dikirim ke WhatsApp: ' . $phone);
            return redirect()->to('/register');
        } catch (\Throwable $e) {
            $session->setFlashdata('error', 'Gagal mendaftar: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    public function activate(string $token)
    {
        $db = \Config\Database::connect();
        $row = $db->table('user_activation')->where('token', $token)->get()->getRowArray();
        if (!$row) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }
        $used = (string) ($row['used_at'] ?? '');
        $exp = (string) ($row['expires_at'] ?? '');
        if ($used !== '' || ($exp !== '' && strtotime($exp) < time())) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }
        return view('Auth/activate', ['token' => $token]);
    }

    public function activateSubmit(string $token)
    {
        $request = $this->request;
        $session = session();
        $rules = [
            'password' => 'required|min_length[6]',
            'confirm' => 'required|matches[password]',
        ];
        if (!$this->validate($rules)) {
            $session->setFlashdata('error', 'Password tidak valid');
            return redirect()->back()->withInput();
        }
        $db = \Config\Database::connect();
        $row = $db->table('user_activation')->where('token', $token)->get()->getRowArray();
        if (!$row) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }
        $used = (string) ($row['used_at'] ?? '');
        $exp = (string) ($row['expires_at'] ?? '');
        if ($used !== '' || ($exp !== '' && strtotime($exp) < time())) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }
        $idUser = (int) ($row['id_user'] ?? 0);
        $pwd = (string) $request->getPost('password');
        try {
            $db->table('users')->where('id_user', $idUser)->update([
                'password_hash' => password_hash($pwd, PASSWORD_DEFAULT),
                'status' => 'aktif',
            ]);
            $db->table('user_activation')->where('id', (int) $row['id'])->update(['used_at' => date('Y-m-d H:i:s')]);
            $session->setFlashdata('message', 'Password telah dibuat. Silakan login.');
            return redirect()->to('/login');
        } catch (\Throwable $e) {
            $session->setFlashdata('error', 'Gagal menyimpan password: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }
}
