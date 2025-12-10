<?php

namespace App\Controllers;

use App\Models\UserModel;
use CodeIgniter\Controller;

class Auth extends Controller
{
    public function index()
    {
        return view('Auth/login');
    }

    public function login()
    {
        $request = $this->request;
        $session = session();

        $rules = [
            'username' => 'required',
            'password' => 'required',
        ];

        if (!$this->validate($rules)) {
            $session->setFlashdata('error', 'Username dan password wajib diisi');
            return redirect()->back()->withInput();
        }

        $username = trim((string) $request->getPost('username'));
        $password = (string) $request->getPost('password');

        $userModel = new UserModel();
        $user = $userModel->getActiveByUsername($username);

        if (!$user) {
            $session->setFlashdata('error', 'Akun tidak ditemukan atau tidak aktif');
            return redirect()->back()->withInput();
        }

        if (!password_verify($password, $user['password_hash'])) {
            $session->setFlashdata('error', 'Username atau password salah');
            return redirect()->back()->withInput();
        }

        $session->regenerate();
        $session->set([
            'isLoggedIn' => true,
            'user' => [
                'id_user' => $user['id_user'],
                'username' => $user['username'],
                'role' => $user['role'],
                'id_anggota' => $user['id_anggota'] ?? null,
                'id_petugas' => $user['id_petugas'] ?? null,
            ],
        ]);
        $session->setFlashdata('justLoggedIn', true);

        $role = $user['role'];
        if ($role === 'anggota') {
            return redirect()->to('/anggota');
        }

        return redirect()->to('/admin');
    }

    public function logout()
    {
        $session = session();
        $session->destroy();
        return redirect()->to('/login');
    }

    public function resend()
    {
        helper('url');
        $this->response->setHeader('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
        $this->response->setHeader('Pragma', 'no-cache');
        $this->response->setHeader('Expires', '0');
        return view('Auth/resend');
    }

    public function forgot()
    {
        helper('url');
        $this->response->setHeader('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
        $this->response->setHeader('Pragma', 'no-cache');
        $this->response->setHeader('Expires', '0');
        return view('Auth/forgot');
    }

    public function forgotSubmit()
    {
        $request = $this->request;
        $session = session();
        $username = trim((string) ($request->getPost('username') ?? ''));
        if ($username === '') {
            $session->setFlashdata('error', 'Masukkan username/no telepon/email untuk reset password.');
            $baseTo = rtrim(config('App')->baseURL, '/');
            return redirect()->to($baseTo . '/forgot', 303, 'auto')->withInput();
        }
        $db = \Config\Database::connect();
        $digits = preg_replace('#\D+#', '', $username);
        $user = $db->table('users')->where('username', $username)->get()->getRowArray();
        if (!$user && $digits !== '') {
            $user = $db->table('users')->where('username', $digits)->get()->getRowArray();
        }
        if (!$user) {
            $queryAng = $db->table('anggota')->groupStart()->where('email', $username)->orWhere('no_telepon', $username)->groupEnd();
            $altLocal = '';
            if ($digits !== '' && str_starts_with($digits, '62')) {
                $altLocal = '0' . substr($digits, 2);
            }
            if ($altLocal !== '') {
                $queryAng = $queryAng->orWhere('no_telepon', $altLocal);
            }
            if ($digits !== '') {
                $queryAng = $queryAng->orWhere('no_telepon', $digits);
            }
            $ang = $queryAng->get()->getRowArray();
            if ($ang) {
                $user = $db->table('users')->where('id_anggota', (int) ($ang['id_anggota'] ?? 0))->orderBy('id_user', 'DESC')->limit(1)->get()->getRowArray();
            }
        }
        if (!$user) {
            $session->setFlashdata('error', 'Akun tidak ditemukan.');
            $baseTo = rtrim(config('App')->baseURL, '/');
            return redirect()->to($baseTo . '/forgot', 303, 'auto')->withInput();
        }
        $idUser = (int) ($user['id_user'] ?? 0);
        if ($idUser <= 0) {
            $session->setFlashdata('error', 'Akun tidak valid.');
            $baseTo = rtrim(config('App')->baseURL, '/');
            return redirect()->to($baseTo . '/forgot', 303, 'auto')->withInput();
        }
        try {
            $active = $db->query('SELECT id, token, expires_at FROM user_activation WHERE id_user = ? AND used_at IS NULL AND (expires_at IS NULL OR expires_at > NOW()) ORDER BY id DESC LIMIT 1', [$idUser])->getRowArray();
        } catch (\Throwable $e) {
            $active = null;
        }
        if ($active) {
            $session->setFlashdata('message', 'link sebelumnya masih bisa dipakai');
            $baseTo = rtrim(config('App')->baseURL, '/');
            return redirect()->to($baseTo . '/forgot', 303, 'auto');
        }
        $token = bin2hex(random_bytes(24));
        $expire = date('Y-m-d H:i:s', time() + 3600);
        try {
            $db->query('UPDATE user_activation SET expires_at = ? WHERE id_user = ? AND used_at IS NULL', [date('Y-m-d H:i:s'), $idUser]);
            $db->table('user_activation')->insert([
                'id_user' => $idUser,
                'token' => $token,
                'expires_at' => $expire,
            ]);
        } catch (\Throwable $e) {
            $session->setFlashdata('error', 'Gagal membuat link reset: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
        $base = rtrim(config('App')->baseURL, '/');
        $link = $base . '/activate/' . $token;
        $nama = '';
        $noTelp = '';
        try {
            $idAnggota = (int) ($user['id_anggota'] ?? 0);
            if ($idAnggota > 0) {
                $ang = $db->table('anggota')->where('id_anggota', $idAnggota)->get()->getRowArray();
                if ($ang) {
                    $nama = trim((string) ($ang['nama'] ?? ''));
                    $noTelp = trim((string) ($ang['no_telepon'] ?? ''));
                }
            }
        } catch (\Throwable $e) {
        }
        $tplRow = $db->table('waha_templates')->where('slug', 'forgot')->get()->getRowArray();
        $tpl = (string) ($tplRow['content'] ?? '');
        if ($tpl === '') {
            $tpl = 'Halo {{nama}}, lakukan reset password: {{link}}';
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
            $baseUrl = (string) (env('WAHA_BASE_URL') ?: '');
            $sendUrl = $envUrl !== '' ? $envUrl : (rtrim($baseUrl, '/') !== '' ? rtrim($baseUrl, '/') . '/api/sendText' : '');
            $tokenW = (string) (env('WAHA_TOKEN') ?: '');
            $sessionId = (string) (env('WAHA_SESSION') ?: 'default');
            $phoneNum = preg_replace('#\D+#', '', $noTelp !== '' ? $noTelp : (string) ($user['username'] ?? ''));
            if ($phoneNum !== '' && str_starts_with($phoneNum, '0')) {
                $phoneNum = '62' . substr($phoneNum, 1);
            }
            if ($phoneNum !== '' && ($sendUrl !== '' || $baseUrl !== '')) {
                $headers = ['Content-Type' => 'application/json'];
                if ($tokenW !== '') {
                    $headers['x-api-key'] = $tokenW;
                    $headers['Authorization'] = 'Bearer ' . $tokenW;
                }
                $targets = [];
                if ($sendUrl !== '') {
                    $targets[] = $sendUrl;
                } else {
                    $b = rtrim($baseUrl, '/');
                    if ($b !== '') {
                        $targets[] = $b . '/api/sendText';
                        $targets[] = $b . '/messages';
                    }
                }
                $ok = false;
                foreach ($targets as $turl) {
                    $p1 = ['phone' => $phoneNum, 'chatId' => $phoneNum . '@c.us', 'text' => $msg, 'session' => $sessionId];
                    $r1 = $client->post($turl, ['headers' => $headers, 'json' => $p1]);
                    $c1 = $r1->getStatusCode();
                    if ($c1 >= 200 && $c1 < 300) {
                        $ok = true;
                        break;
                    }
                    $p2 = ['to' => $phoneNum, 'chatId' => $phoneNum . '@c.us', 'text' => $msg, 'session' => $sessionId];
                    $r2 = $client->post($turl, ['headers' => $headers, 'json' => $p2]);
                    $c2 = $r2->getStatusCode();
                    if ($c2 >= 200 && $c2 < 300) {
                        $ok = true;
                        break;
                    }
                    $p3 = ['phone' => $phoneNum, 'chatId' => $phoneNum . '@c.us', 'message' => $msg, 'session' => $sessionId];
                    $r3 = $client->post($turl, ['headers' => $headers, 'json' => $p3]);
                    $c3 = $r3->getStatusCode();
                    if ($c3 >= 200 && $c3 < 300) {
                        $ok = true;
                        break;
                    }
                    $p4 = ['phone' => $phoneNum, 'chatId' => $phoneNum . '@c.us', 'message' => $msg, 'session' => $sessionId];
                    $r4 = $client->post($turl, ['headers' => ['Content-Type' => 'application/x-www-form-urlencoded'] + $headers, 'form_params' => $p4]);
                    $c4 = $r4->getStatusCode();
                    if ($c4 >= 200 && $c4 < 300) {
                        $ok = true;
                        break;
                    }
                    if (str_ends_with($turl, '/messages')) {
                        $p5 = ['chatId' => $phoneNum . '@c.us', 'body' => $msg, 'session' => $sessionId];
                        $r5 = $client->post($turl, ['headers' => $headers, 'json' => $p5]);
                        $c5 = $r5->getStatusCode();
                        if ($c5 >= 200 && $c5 < 300) {
                            $ok = true;
                            break;
                        }
                    }
                }
            }
        } catch (\Throwable $e) {
            try {
                $opts2 = ['timeout' => 10, 'http_errors' => false, 'allow_redirects' => ['max' => 5, 'strict' => false, 'referer' => false], 'verify' => false];
                $client2 = \Config\Services::curlrequest($opts2);
                $headers2 = ['Content-Type' => 'application/json'];
                $tokenW2 = (string) (env('WAHA_TOKEN') ?: '');
                if ($tokenW2 !== '') {
                    $headers2['x-api-key'] = $tokenW2;
                    $headers2['Authorization'] = 'Bearer ' . $tokenW2;
                }
                $envUrl2 = (string) (env('WAHA_SEND_URL') ?: '');
                $baseUrl2 = (string) (env('WAHA_BASE_URL') ?: '');
                $s0 = $envUrl2 !== '' ? $envUrl2 : (rtrim($baseUrl2, '/') !== '' ? rtrim($baseUrl2, '/') . '/api/sendText' : '');
                $targets2 = [];
                if ($s0 !== '') {
                    $targets2[] = preg_replace('#^https://#', 'http://', $s0);
                    $targets2[] = preg_replace('#^https://#', 'http://', rtrim($baseUrl2, '/') . '/messages');
                }
                $phoneNum = preg_replace('#\D+#', '', (string) ($user['username'] ?? ''));
                $sessionId = (string) (env('WAHA_SESSION') ?: 'default');
                $msg = 'Reset Password: ' . ($base ?? '') . '/activate/' . ($token ?? '');
                foreach ($targets2 as $turl2) {
                    $p1 = ['phone' => $phoneNum, 'chatId' => $phoneNum . '@c.us', 'text' => $msg, 'session' => $sessionId];
                    $client2->post($turl2, ['headers' => $headers2, 'json' => $p1]);
                }
            } catch (\Throwable $e2) {
            }
        }
        $session->setFlashdata('message', 'Link reset password telah dikirim');
        $baseTo = rtrim(config('App')->baseURL, '/');
        return redirect()->to($baseTo . '/forgot', 303, 'auto');
    }
}
