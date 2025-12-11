<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class Admin extends Controller
{
    public function index()
    {
        $session = session();
        $user = $session->get('user');
        if (!$session->get('isLoggedIn') || !$user) {
            return redirect()->to('/login');
        }
        $role = $user['role'] ?? null;
        if (!in_array($role, ['petugas', 'admin', 'anggota_petugas'], true)) {
            return redirect()->to('/login');
        }
        $content = view('admin/Dashboard');
        return view('admin/layout', [
            'content' => $content,
            'title' => 'Dashboard',
        ]);
    }

    public function pinjaman()
    {
        $session = session();
        $user = $session->get('user');
        if (!$session->get('isLoggedIn') || !$user) {
            return redirect()->to('/login');
        }
        $role = $user['role'] ?? null;
        if (!in_array($role, ['petugas', 'admin', 'anggota_petugas'], true)) {
            return redirect()->to('/login');
        }

        $db = \Config\Database::connect();

        $permohonan = $db
            ->table('pinjaman')
            ->select('pinjaman.*, anggota.no_anggota, anggota.nama')
            ->join('anggota', 'anggota.id_anggota = pinjaman.id_anggota', 'left')
            ->where('pinjaman.tanggal_pinjam', null)
            ->orderBy('pinjaman.id_pinjaman', 'desc')
            ->get()
            ->getResultArray();

        $pinjaman = $db
            ->table('pinjaman')
            ->select('pinjaman.*, anggota.no_anggota, anggota.nama')
            ->join('anggota', 'anggota.id_anggota = pinjaman.id_anggota', 'left')
            ->where('pinjaman.tanggal_pinjam IS NOT NULL', null, false)
            ->orderBy('pinjaman.id_pinjaman', 'desc')
            ->get()
            ->getResultArray();

        $content = view('admin/transaksi/pinjaman', ['pinjaman' => $pinjaman, 'permohonan' => $permohonan]);
        return view('admin/layout', [
            'content' => $content,
            'title' => 'Pinjaman',
        ]);
    }

    public function approvePermohonan()
    {
        $session = session();
        $user = $session->get('user');
        if (!$session->get('isLoggedIn') || !$user) {
            return redirect()->to('/login');
        }
        $role = $user['role'] ?? null;
        if (!in_array($role, ['petugas', 'admin', 'anggota_petugas'], true)) {
            return redirect()->to('/login');
        }

        $id = (int) ($this->request->getPost('id_pinjaman') ?? 0);
        $keterangan = (string) ($this->request->getPost('keterangan') ?? '');
        if ($id <= 0) {
            return redirect()->back()->with('error', 'ID pinjaman tidak valid');
        }

        $db = \Config\Database::connect();
        $db
            ->table('pinjaman')
            ->where('id_pinjaman', $id)
            ->update([
                'tanggal_pinjam' => date('Y-m-d'),
                'keterangan' => $keterangan,
            ]);

        return redirect()->to('/admin/pinjaman')->with('message', 'Permohonan disetujui');
    }

    public function rejectPermohonan()
    {
        $session = session();
        $user = $session->get('user');
        if (!$session->get('isLoggedIn') || !$user) {
            return redirect()->to('/login');
        }
        $role = $user['role'] ?? null;
        if (!in_array($role, ['petugas', 'admin', 'anggota_petugas'], true)) {
            return redirect()->to('/login');
        }

        $id = (int) ($this->request->getPost('id_pinjaman') ?? 0);
        $keterangan = (string) ($this->request->getPost('keterangan') ?? '');
        if ($id <= 0) {
            return redirect()->back()->with('error', 'ID pinjaman tidak valid');
        }

        $db = \Config\Database::connect();
        $db
            ->table('pinjaman')
            ->where('id_pinjaman', $id)
            ->update([
                'keterangan' => $keterangan,
            ]);

        return redirect()->to('/admin/pinjaman')->with('message', 'Permohonan ditolak');
    }

    public function anggotaBerhenti()
    {
        $session = session();
        $user = $session->get('user');
        if (!$session->get('isLoggedIn') || !$user) {
            return redirect()->to('/login');
        }
        $role = $user['role'] ?? null;
        if (!in_array($role, ['petugas', 'admin', 'anggota_petugas'], true)) {
            return redirect()->to('/login');
        }
        $db = \Config\Database::connect();
        $pending = $db
            ->table('anggota')
            ->where('status', 'aktif')
            ->where('tanggal_berhenti IS NOT NULL', null, false)
            ->orderBy('tanggal_berhenti', 'DESC')
            ->get()
            ->getResultArray();
        $rejected = $db
            ->table('anggota')
            ->where('tanggal_tolak_berhenti IS NOT NULL', null, false)
            ->orderBy('id_anggota', 'DESC')
            ->get()
            ->getResultArray();
        $content = view('admin/anggota/berhenti', ['pending' => $pending, 'rejected' => $rejected]);
        return view('admin/layout', [
            'content' => $content,
            'title' => 'Permohonan Berhenti Anggota',
        ]);
    }

    public function approveBerhenti()
    {
        $session = session();
        $user = $session->get('user');
        if (!$session->get('isLoggedIn') || !$user) {
            return redirect()->to('/login');
        }
        $role = $user['role'] ?? null;
        if (!in_array($role, ['petugas', 'admin', 'anggota_petugas'], true)) {
            return redirect()->to('/login');
        }
        $id = (int) ($this->request->getPost('id_anggota') ?? 0);
        if ($id <= 0) {
            return redirect()->back()->with('error', 'ID anggota tidak valid');
        }
        $db = \Config\Database::connect();
        try {
            $db->table('anggota')->where('id_anggota', $id)->update([
                'status' => 'nonaktif',
                'tanggal_berhenti' => date('Y-m-d'),
            ]);
            $db->table('users')->where('id_anggota', $id)->update(['status' => 'nonaktif']);
            return redirect()->to('/admin/anggota/berhenti')->with('message', 'Permohonan berhenti disetujui');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Gagal menyetujui: ' . $e->getMessage());
        }
    }

    public function rejectBerhenti()
    {
        $session = session();
        $user = $session->get('user');
        if (!$session->get('isLoggedIn') || !$user) {
            return redirect()->to('/login');
        }
        $role = $user['role'] ?? null;
        if (!in_array($role, ['petugas', 'admin', 'anggota_petugas'], true)) {
            return redirect()->to('/login');
        }
        $id = (int) ($this->request->getPost('id_anggota') ?? 0);
        $alasanTolak = trim((string) ($this->request->getPost('alasan_tolak_berhenti') ?? ''));
        if ($id <= 0) {
            return redirect()->back()->with('error', 'ID anggota tidak valid');
        }
        $db = \Config\Database::connect();
        try {
            $db->table('anggota')->where('id_anggota', $id)->update([
                'tanggal_berhenti' => null,
                'alasan_berhenti' => null,
                'tanggal_tolak_berhenti' => date('Y-m-d'),
                'alasan_tolak_berhenti' => $alasanTolak !== '' ? $alasanTolak : null,
            ]);
            return redirect()->to('/admin/anggota/berhenti')->with('message', 'Permohonan berhenti ditolak');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Gagal menolak: ' . $e->getMessage());
        }
    }

    public function anggota()
    {
        $session = session();
        $user = $session->get('user');
        if (!$session->get('isLoggedIn') || !$user) {
            return redirect()->to('/login');
        }
        $role = $user['role'] ?? null;
        if (!in_array($role, ['petugas', 'admin', 'anggota_petugas'], true)) {
            return redirect()->to('/login');
        }

        $content = view('admin/anggota/dataanggota');
        return view('admin/layout', [
            'content' => $content,
            'title' => 'Data Anggota',
        ]);
    }

    public function anggotaTambah()
    {
        $session = session();
        $user = $session->get('user');
        if (!$session->get('isLoggedIn') || !$user) {
            return redirect()->to('/login');
        }
        $role = $user['role'] ?? null;
        if (!in_array($role, ['petugas', 'admin', 'anggota_petugas'], true)) {
            return redirect()->to('/login');
        }
        $content = view('admin/anggota/tambahanggota');
        return view('admin/layout', [
            'content' => $content,
            'title' => 'Tambah Anggota',
        ]);
    }

    public function anggotaEdit(int $id)
    {
        $session = session();
        $user = $session->get('user');
        if (!$session->get('isLoggedIn') || !$user) {
            return redirect()->to('/login');
        }
        $role = $user['role'] ?? null;
        if (!in_array($role, ['petugas', 'admin', 'anggota_petugas'], true)) {
            return redirect()->to('/login');
        }
        $db = \Config\Database::connect();
        $anggota = $db->table('anggota')->where('id_anggota', $id)->get()->getRowArray();
        if (!$anggota) {
            return redirect()->to('/admin/anggota')->with('error', 'Anggota tidak ditemukan');
        }
        $content = view('admin/anggota/editanggota', ['anggota' => $anggota]);
        return view('admin/layout', [
            'content' => $content,
            'title' => 'Edit Anggota',
        ]);
    }

    public function anggotaLihat(int $id)
    {
        $session = session();
        $user = $session->get('user');
        if (!$session->get('isLoggedIn') || !$user) {
            return redirect()->to('/login');
        }
        $role = $user['role'] ?? null;
        if (!in_array($role, ['petugas', 'admin', 'anggota_petugas'], true)) {
            return redirect()->to('/login');
        }
        $db = \Config\Database::connect();
        $anggota = $db->table('anggota')->where('id_anggota', $id)->get()->getRowArray();
        if (!$anggota) {
            return redirect()->to('/admin/anggota')->with('error', 'Anggota tidak ditemukan');
        }
        $content = view('admin/anggota/lihatanggota', ['anggota' => $anggota]);
        return view('admin/layout', [
            'content' => $content,
            'title' => 'Detail Anggota',
        ]);
    }

    public function anggotaLihatSimpan(int $id)
    {
        $session = session();
        $user = $session->get('user');
        if (!$session->get('isLoggedIn') || !$user) {
            return redirect()->to('/login');
        }
        $role = $user['role'] ?? null;
        if (!in_array($role, ['petugas', 'admin', 'anggota_petugas'], true)) {
            return redirect()->to('/login');
        }
        $db = \Config\Database::connect();
        $anggota = $db->table('anggota')->where('id_anggota', $id)->get()->getRowArray();
        if (!$anggota) {
            return redirect()->to('/admin/anggota')->with('error', 'Anggota tidak ditemukan');
        }
        $content = view('admin/anggota/lihatsimpan', ['anggota' => $anggota]);
        return view('admin/layout', [
            'content' => $content,
            'title' => 'Simpanan Anggota',
        ]);
    }

    public function anggotaLihatPinjam(int $id)
    {
        $session = session();
        $user = $session->get('user');
        if (!$session->get('isLoggedIn') || !$user) {
            return redirect()->to('/login');
        }
        $role = $user['role'] ?? null;
        if (!in_array($role, ['petugas', 'admin', 'anggota_petugas'], true)) {
            return redirect()->to('/login');
        }
        $db = \Config\Database::connect();
        $anggota = $db->table('anggota')->where('id_anggota', $id)->get()->getRowArray();
        if (!$anggota) {
            return redirect()->to('/admin/anggota')->with('error', 'Anggota tidak ditemukan');
        }
        $content = view('admin/anggota/lihatpinjam', ['anggota' => $anggota]);
        return view('admin/layout', [
            'content' => $content,
            'title' => 'Pinjaman Anggota',
        ]);
    }

    public function settingWaha()
    {
        $session = session();
        $user = $session->get('user');
        if (!$session->get('isLoggedIn') || !$user) {
            return redirect()->to('/login');
        }
        $role = $user['role'] ?? null;
        if (!in_array($role, ['admin', 'petugas', 'anggota_petugas'], true)) {
            return redirect()->to('/login');
        }
        $content = view('admin/setting/waha');
        return view('admin/layout', [
            'content' => $content,
            'title' => 'Setting WAHA',
        ]);
    }

    public function apiSettingWaha()
    {
        $session = session();
        $user = $session->get('user');
        if (!$session->get('isLoggedIn') || !$user) {
            return $this->response->setStatusCode(401)->setJSON(['error' => 'Unauthorized']);
        }
        $role = $user['role'] ?? null;
        if (!in_array($role, ['admin', 'petugas', 'anggota_petugas'], true)) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Forbidden']);
        }
        $db = \Config\Database::connect();
        $tables = $db->listTables();
        if (!in_array('waha_templates', $tables, true)) {
            $db->query('CREATE TABLE IF NOT EXISTS waha_templates (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                slug VARCHAR(50) NOT NULL UNIQUE,
                content TEXT NULL,
                updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4');
        }
        if (strtolower($this->request->getMethod()) === 'post') {
            $items = [
                ['slug' => 'register', 'content' => (string) $this->request->getPost('register')],
                ['slug' => 'wajib', 'content' => (string) $this->request->getPost('wajib')],
                ['slug' => 'sukarela', 'content' => (string) $this->request->getPost('sukarela')],
                ['slug' => 'forgot', 'content' => (string) $this->request->getPost('forgot')],
                ['slug' => 'daftar', 'content' => (string) $this->request->getPost('daftar')],
                ['slug' => 'status_anggota', 'content' => (string) $this->request->getPost('status_anggota')],
            ];
            foreach ($items as $it) {
                $exists = $db->table('waha_templates')->where('slug', $it['slug'])->get()->getRowArray();
                if ($exists) {
                    $db->table('waha_templates')->where('slug', $it['slug'])->update(['content' => $it['content']]);
                } else {
                    $db->table('waha_templates')->insert($it);
                }
            }
            return $this->response->setJSON(['ok' => true]);
        }
        $rows = $db->table('waha_templates')->get()->getResultArray();
        $map = ['register' => '', 'wajib' => '', 'sukarela' => '', 'forgot' => '', 'daftar' => '', 'status_anggota' => ''];
        foreach ($rows as $r) {
            $map[$r['slug']] = (string) ($r['content'] ?? '');
        }
        return $this->response->setJSON($map);
    }

    public function settingAdminData()
    {
        $session = session();
        $user = $session->get('user');
        if (!$session->get('isLoggedIn') || !$user) {
            return redirect()->to('/login');
        }
        $role = $user['role'] ?? null;
        if (!in_array($role, ['admin', 'petugas', 'anggota_petugas'], true)) {
            return redirect()->to('/login');
        }
        $content = view('admin/setting/admindata');
        return view('admin/layout', [
            'content' => $content,
            'title' => 'Data Admin',
        ]);
    }

    public function apiSettingAdminData()
    {
        $session = session();
        $user = $session->get('user');
        if (!$session->get('isLoggedIn') || !$user) {
            return $this->response->setStatusCode(401)->setJSON(['error' => 'Unauthorized']);
        }
        $role = $user['role'] ?? null;
        if (!in_array($role, ['admin', 'petugas', 'anggota_petugas'], true)) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Forbidden']);
        }
        $db = \Config\Database::connect();
        $rows = $db
            ->table('users u')
            ->select('u.id_user,u.username,u.role,u.status,u.foto,p.nama_petugas,p.level')
            ->join('petugas p', 'p.id_petugas = u.id_petugas', 'left')
            ->whereIn('u.role', ['admin', 'petugas'])
            ->orderBy('u.role', 'asc')
            ->orderBy('p.level', 'asc')
            ->get()
            ->getResultArray();
        return $this->response->setJSON(['data' => $rows]);
    }

    public function settingAdminAdd()
    {
        $session = session();
        $user = $session->get('user');
        if (!$session->get('isLoggedIn') || !$user) {
            return redirect()->to('/login');
        }
        $role = $user['role'] ?? null;
        if ($role !== 'admin') {
            return redirect()->to('/admin/setting/admin-data');
        }
        $content = view('admin/setting/addadmin');
        return view('admin/layout', [
            'content' => $content,
            'title' => 'Tambah Admin',
        ]);
    }

    public function settingAdminEdit(int $id)
    {
        $session = session();
        $user = $session->get('user');
        if (!$session->get('isLoggedIn') || !$user) {
            return redirect()->to('/login');
        }
        $role = (string) ($user['role'] ?? '');
        if (!in_array($role, ['admin', 'petugas'], true)) {
            return redirect()->to('/login');
        }
        $idUser = (int) ($user['id_user'] ?? 0);
        if ($role === 'petugas' && $idUser !== (int) $id) {
            return redirect()->to('/admin/setting/admin-data');
        }
        $db = \Config\Database::connect();
        $u = $db
            ->table('users u')
            ->select('u.id_user,u.username,u.role,u.status,u.foto,u.id_petugas,p.nama_petugas,p.level')
            ->join('petugas p', 'p.id_petugas = u.id_petugas', 'left')
            ->where('u.id_user', (int) $id)
            ->get()
            ->getRowArray();
        if (!$u) {
            return redirect()->to('/admin/setting/admin-data');
        }
        $content = view('admin/setting/editadmin', ['userRow' => $u]);
        return view('admin/layout', [
            'content' => $content,
            'title' => 'Edit Admin/Petugas',
        ]);
    }

    public function createAdmin()
    {
        $session = session();
        $user = $session->get('user');
        if (!$session->get('isLoggedIn') || !$user) {
            return redirect()->to('/login');
        }
        $role = (string) ($user['role'] ?? '');
        if ($role !== 'admin') {
            return redirect()->to('/admin/setting/admin-data');
        }
        $db = \Config\Database::connect();
        $username = trim((string) ($this->request->getPost('username') ?? ''));
        $userRole = trim((string) ($this->request->getPost('role') ?? 'petugas'));
        $status = trim((string) ($this->request->getPost('status') ?? 'aktif'));
        $namaPetugas = trim((string) ($this->request->getPost('nama_petugas') ?? ''));
        $password = (string) ($this->request->getPost('password') ?? '');
        $passwordConfirm = (string) ($this->request->getPost('password_confirm') ?? '');
        if ($username === '' || $password === '' || $passwordConfirm === '' || $password !== $passwordConfirm) {
            return redirect()->back()->with('error', 'Username dan password wajib serta harus cocok');
        }
        try {
            $exists = $db->table('users')->where('username', $username)->get()->getRowArray();
            if ($exists) {
                return redirect()->back()->with('error', 'Username sudah digunakan');
            }
            $fotoPath = null;
            $fotoBase64 = (string) ($this->request->getPost('foto_cropped') ?? '');
            $fotoFile = $this->request->getFile('foto');
            $uploadDir = FCPATH . 'uploads/admin';
            if (!is_dir($uploadDir)) {
                @mkdir($uploadDir, 0777, true);
            }
            if ($fotoBase64 !== '' && preg_match('#^data:image/\w+;base64,#', $fotoBase64)) {
                $fname = 'foto_' . time() . '_' . bin2hex(random_bytes(4)) . '.webp';
                $path = $uploadDir . DIRECTORY_SEPARATOR . $fname;
                $dataUri = substr($fotoBase64, strpos($fotoBase64, ',') + 1);
                $bin = base64_decode($dataUri);
                file_put_contents($path, $bin);
                $img = \Config\Services::image();
                $img->withFile($path)->resize(500, 500, true)->save($path, 80);
                $fotoPath = '/uploads/admin/' . $fname;
            } elseif ($fotoFile && $fotoFile->isValid() && !$fotoFile->hasMoved()) {
                if ($fotoFile->getSize() > 2 * 1024 * 1024) {
                    return redirect()->back()->with('error', 'Ukuran foto maksimal 2MB');
                }
                $ext = $fotoFile->getClientExtension();
                $tmp = 'tmp_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
                $fotoFile->move($uploadDir, $tmp);
                $tmpPath = $uploadDir . DIRECTORY_SEPARATOR . $tmp;
                $fname = 'foto_' . time() . '_' . bin2hex(random_bytes(4)) . '.webp';
                $path = $uploadDir . DIRECTORY_SEPARATOR . $fname;
                $img = \Config\Services::image();
                $img->withFile($tmpPath)->resize(500, 500, true)->save($path, 80);
                @unlink($tmpPath);
                $fotoPath = '/uploads/admin/' . $fname;
            }
            $idPetugas = null;
            if ($namaPetugas !== '') {
                $db->table('petugas')->insert(['nama_petugas' => $namaPetugas, 'level' => 'kasir']);
                $idPetugas = $db->insertID();
            }
            $db->table('users')->insert([
                'username' => $username,
                'password_hash' => password_hash($password, PASSWORD_DEFAULT),
                'role' => in_array($userRole, ['admin', 'petugas'], true) ? $userRole : 'petugas',
                'id_petugas' => $idPetugas,
                'status' => in_array($status, ['aktif', 'nonaktif'], true) ? $status : 'aktif',
                'foto' => $fotoPath,
            ]);
            return redirect()->to('/admin/setting/admin-data')->with('message', 'Admin/Petugas berhasil ditambahkan');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Gagal menambahkan admin/petugas: ' . $e->getMessage());
        }
    }

    public function updateAdmin(int $id)
    {
        $session = session();
        $user = $session->get('user');
        if (!$session->get('isLoggedIn') || !$user) {
            return redirect()->to('/login');
        }
        $role = (string) ($user['role'] ?? '');
        $idUser = (int) ($user['id_user'] ?? 0);
        if (!in_array($role, ['admin', 'petugas'], true)) {
            return redirect()->to('/login');
        }
        if ($role === 'petugas' && $idUser !== (int) $id) {
            return redirect()->to('/admin/setting/admin-data');
        }
        $db = \Config\Database::connect();
        try {
            $row = $db->table('users')->where('id_user', (int) $id)->get()->getRowArray();
            if (!$row) {
                return redirect()->to('/admin/setting/admin-data')->with('error', 'User tidak ditemukan');
            }
            $username = trim((string) ($this->request->getPost('username') ?? ''));
            $userRole = trim((string) ($this->request->getPost('role') ?? ''));
            $status = trim((string) ($this->request->getPost('status') ?? ''));
            $namaPetugas = trim((string) ($this->request->getPost('nama_petugas') ?? ''));
            $newPass = (string) ($this->request->getPost('new_password') ?? '');
            $newPass2 = (string) ($this->request->getPost('new_password_confirm') ?? '');
            $update = [];
            if ($username !== '') {
                $update['username'] = $username;
            }
            if ($role === 'admin' && in_array($userRole, ['admin', 'petugas'], true)) {
                $update['role'] = $userRole;
            }
            if ($role === 'admin' && in_array($status, ['aktif', 'nonaktif'], true)) {
                $update['status'] = $status;
            }
            if ($newPass !== '') {
                if ($newPass !== $newPass2 || strlen($newPass) < 8) {
                    return redirect()->back()->with('error', 'Password baru tidak valid atau tidak cocok');
                }
                $update['password_hash'] = password_hash($newPass, PASSWORD_DEFAULT);
            }
            $fotoPath = null;
            $fotoBase64 = (string) ($this->request->getPost('foto_cropped') ?? '');
            $fotoFile = $this->request->getFile('foto');
            $uploadDir = FCPATH . 'uploads/admin';
            if (!is_dir($uploadDir)) {
                @mkdir($uploadDir, 0777, true);
            }
            if ($fotoBase64 !== '' && preg_match('#^data:image/\w+;base64,#', $fotoBase64)) {
                $fname = 'foto_' . time() . '_' . bin2hex(random_bytes(4)) . '.webp';
                $path = $uploadDir . DIRECTORY_SEPARATOR . $fname;
                $dataUri = substr($fotoBase64, strpos($fotoBase64, ',') + 1);
                $bin = base64_decode($dataUri);
                file_put_contents($path, $bin);
                $img = \Config\Services::image();
                $img->withFile($path)->resize(500, 500, true)->save($path, 80);
                $fotoPath = '/uploads/admin/' . $fname;
            } elseif ($fotoFile && $fotoFile->isValid() && !$fotoFile->hasMoved()) {
                if ($fotoFile->getSize() > 2 * 1024 * 1024) {
                    return redirect()->back()->with('error', 'Ukuran foto maksimal 2MB');
                }
                $ext = $fotoFile->getClientExtension();
                $tmp = 'tmp_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
                $fotoFile->move($uploadDir, $tmp);
                $tmpPath = $uploadDir . DIRECTORY_SEPARATOR . $tmp;
                $fname = 'foto_' . time() . '_' . bin2hex(random_bytes(4)) . '.webp';
                $path = $uploadDir . DIRECTORY_SEPARATOR . $fname;
                $img = \Config\Services::image();
                $img->withFile($tmpPath)->resize(500, 500, true)->save($path, 80);
                @unlink($tmpPath);
                $fotoPath = '/uploads/admin/' . $fname;
            }
            if ($fotoPath) {
                $update['foto'] = $fotoPath;
            }
            if (!empty($update)) {
                $db->table('users')->where('id_user', (int) $id)->update($update);
            }
            if ($namaPetugas !== '') {
                $idPetugas = (int) ($row['id_petugas'] ?? 0);
                if ($idPetugas > 0) {
                    $db->table('petugas')->where('id_petugas', $idPetugas)->update(['nama_petugas' => $namaPetugas]);
                } else {
                    $db->table('petugas')->insert(['nama_petugas' => $namaPetugas, 'level' => 'kasir']);
                    $db->table('users')->where('id_user', (int) $id)->update(['id_petugas' => $db->insertID()]);
                }
            }
            return redirect()->to('/admin/setting/admin-data')->with('message', 'Admin/Petugas diperbarui');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Gagal memperbarui admin/petugas: ' . $e->getMessage());
        }
    }

    public function deleteAdmin(int $id)
    {
        $session = session();
        $user = $session->get('user');
        if (!$session->get('isLoggedIn') || !$user) {
            return $this->response->setStatusCode(401)->setJSON(['error' => 'Unauthorized']);
        }
        $role = (string) ($user['role'] ?? '');
        $idUser = (int) ($user['id_user'] ?? 0);
        if ($role !== 'admin') {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Forbidden']);
        }
        if ($idUser === (int) $id) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'Tidak bisa menghapus akun sendiri']);
        }
        $db = \Config\Database::connect();
        try {
            $exists = $db->table('users')->select('id_user')->where('id_user', (int) $id)->get()->getRowArray();
            if (!$exists) {
                return $this->response->setStatusCode(404)->setJSON(['error' => 'User tidak ditemukan']);
            }
            $db->table('users')->where('id_user', (int) $id)->delete();
            return $this->response->setJSON(['ok' => true]);
        } catch (\Throwable $e) {
            return $this->response->setStatusCode(500)->setJSON(['error' => 'Gagal menghapus: ' . $e->getMessage()]);
        }
    }

    public function apiSimpananConfig()
    {
        $session = session();
        $user = $session->get('user');
        if (!$session->get('isLoggedIn') || !$user) {
            return $this->response->setStatusCode(401)->setJSON(['error' => 'Unauthorized']);
        }
        $role = $user['role'] ?? null;
        if (!in_array($role, ['admin', 'petugas', 'anggota_petugas'], true)) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Forbidden']);
        }
        $db = \Config\Database::connect();
        $tables = $db->listTables();
        if (!in_array('settings', $tables, true)) {
            $db->query('CREATE TABLE IF NOT EXISTS settings (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                `key` VARCHAR(100) NOT NULL UNIQUE,
                `value` TEXT NULL,
                updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4');
        }
        if (strtolower($this->request->getMethod()) === 'post') {
            $feePokok = trim((string) ($this->request->getPost('fee_pokok') ?? ''));
            $feeWajib = trim((string) ($this->request->getPost('fee_wajib') ?? ''));
            $pairs = [
                ['key' => 'fee_pokok', 'value' => $feePokok],
                ['key' => 'fee_wajib', 'value' => $feeWajib],
            ];
            foreach ($pairs as $p) {
                $exists = $db->table('settings')->where('key', $p['key'])->get()->getRowArray();
                if ($exists) {
                    $db->table('settings')->where('key', $p['key'])->update(['value' => $p['value']]);
                } else {
                    $db->table('settings')->insert($p);
                }
            }
            return $this->response->setJSON(['ok' => true]);
        }
        $rows = $db->table('settings')->whereIn('key', ['fee_pokok', 'fee_wajib'])->get()->getResultArray();
        $map = ['fee_pokok' => '0', 'fee_wajib' => '0'];
        foreach ($rows as $r) {
            $map[$r['key']] = (string) ($r['value'] ?? '0');
        }
        return $this->response->setJSON($map);
    }

    public function apiSimpananAnggota(int $id)
    {
        $session = session();
        $user = $session->get('user');
        if (!$session->get('isLoggedIn') || !$user) {
            return $this->response->setStatusCode(401)->setJSON(['error' => 'Unauthorized']);
        }
        $role = $user['role'] ?? null;
        if (!in_array($role, ['petugas', 'admin', 'anggota_petugas'], true)) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Forbidden']);
        }
        $page = max(1, (int) ($this->request->getGet('page') ?? 1));
        $perPage = 25;
        $offset = ($page - 1) * $perPage;
        $db = \Config\Database::connect();
        $count = (int) $db->table('simpanan')->where('id_anggota', $id)->countAllResults();
        $sumRow = $db->table('simpanan')->selectSum('jumlah')->where('id_anggota', $id)->get()->getRowArray();
        $rows = $db
            ->table('simpanan')
            ->select('id_simpanan, tanggal_simpan, jenis_simpanan, tipe_sukarela, jumlah, status, tanggal_jatuh_tempo')
            ->where('id_anggota', $id)
            ->orderBy('tanggal_simpan', 'DESC')
            ->limit($perPage, $offset)
            ->get()
            ->getResultArray();
        $sumPage = 0.0;
        foreach ($rows as $r) {
            $sumPage += (float) ($r['jumlah'] ?? 0);
        }
        return $this->response->setJSON([
            'data' => $rows,
            'meta' => [
                'page' => $page,
                'perPage' => $perPage,
                'totalItems' => $count,
                'totalPages' => (int) ceil($count / $perPage),
                'sumAll' => (float) ($sumRow['jumlah'] ?? 0),
                'sumPage' => $sumPage,
            ],
        ]);
    }

    public function apiSimpananAnggotaSummary(int $id)
    {
        $session = session();
        $user = $session->get('user');
        if (!$session->get('isLoggedIn') || !$user) {
            return $this->response->setStatusCode(401)->setJSON(['error' => 'Unauthorized']);
        }
        $role = $user['role'] ?? null;
        if (!in_array($role, ['petugas', 'admin', 'anggota_petugas'], true)) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Forbidden']);
        }
        $db = \Config\Database::connect();
        $sumPokok = (float) ($db->table('simpanan')->selectSum('jumlah')->where('id_anggota', $id)->where('jenis_simpanan', 'pokok')->where('status', 'aktif')->get()->getRowArray()['jumlah'] ?? 0);
        $sumWajib = (float) ($db->table('simpanan')->selectSum('jumlah')->where('id_anggota', $id)->where('jenis_simpanan', 'wajib')->where('status', 'aktif')->get()->getRowArray()['jumlah'] ?? 0);
        $sumSukarela = (float) ($db->table('simpanan')->selectSum('jumlah')->where('id_anggota', $id)->where('jenis_simpanan', 'sukarela')->where('status', 'aktif')->get()->getRowArray()['jumlah'] ?? 0);
        return $this->response->setJSON([
            'sumPokok' => $sumPokok,
            'sumWajib' => $sumWajib,
            'sumSukarela' => $sumSukarela,
        ]);
    }

    public function anggotaData()
    {
        $session = session();
        $user = $session->get('user');
        if (!$session->get('isLoggedIn') || !$user) {
            return $this->response->setStatusCode(401)->setJSON(['error' => 'Unauthorized']);
        }
        $role = $user['role'] ?? null;
        if (!in_array($role, ['petugas', 'admin', 'anggota_petugas'], true)) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Forbidden']);
        }

        $db = \Config\Database::connect();
        $rows = $db
            ->table('anggota')
            ->select('id_anggota, no_anggota, nama, jenis_kelamin, alamat, no_telepon, email, status, jenis_anggota')
            ->orderBy('id_anggota', 'desc')
            ->get()
            ->getResultArray();
        return $this->response->setJSON(['data' => $rows]);
    }

    public function createAnggota()
    {
        $session = session();
        $user = $session->get('user');
        if (!$session->get('isLoggedIn') || !$user) {
            return redirect()->to('/login');
        }
        $role = $user['role'] ?? null;
        if (!in_array($role, ['petugas', 'admin', 'anggota_petugas'], true)) {
            return redirect()->to('/login');
        }

        $data = [
            'no_anggota' => trim((string) $this->request->getPost('no_anggota')),
            'nama' => trim((string) $this->request->getPost('nama')),
            'jenis_kelamin' => trim((string) $this->request->getPost('jenis_kelamin')) ?: null,
            'alamat' => trim((string) $this->request->getPost('alamat')) ?: null,
            'no_telepon' => trim((string) $this->request->getPost('no_telepon')) ?: null,
            'email' => trim((string) $this->request->getPost('email')) ?: null,
            'status' => trim((string) $this->request->getPost('status')) ?: 'aktif',
            'jenis_anggota' => trim((string) $this->request->getPost('jenis_anggota')) ?: 'aktif',
            'tanggal_gabung' => date('Y-m-d'),
        ];

        $fotoBase64 = (string) ($this->request->getPost('foto_cropped') ?? '');
        $fotoFile = $this->request->getFile('foto');
        $uploadDir = FCPATH . 'uploads/anggota';
        if (!is_dir($uploadDir)) {
            @mkdir($uploadDir, 0777, true);
        }
        if ($fotoBase64 !== '' && preg_match('#^data:image/\w+;base64,#', $fotoBase64)) {
            $fname = 'foto_' . time() . '_' . bin2hex(random_bytes(4)) . '.webp';
            $path = $uploadDir . DIRECTORY_SEPARATOR . $fname;
            $dataUri = substr($fotoBase64, strpos($fotoBase64, ',') + 1);
            $bin = base64_decode($dataUri);
            file_put_contents($path, $bin);
            $img = \Config\Services::image();
            $img->withFile($path)->resize(500, 500, true)->save($path, 80);
            $data['foto'] = '/uploads/anggota/' . $fname;
        } elseif ($fotoFile && $fotoFile->isValid() && !$fotoFile->hasMoved()) {
            if ($fotoFile->getSize() > 2 * 1024 * 1024) {
                return redirect()->back()->with('error', 'Ukuran foto maksimal 2MB');
            }
            $ext = $fotoFile->getClientExtension();
            $tmp = 'tmp_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
            $fotoFile->move($uploadDir, $tmp);
            $tmpPath = $uploadDir . DIRECTORY_SEPARATOR . $tmp;
            $fname = 'foto_' . time() . '_' . bin2hex(random_bytes(4)) . '.webp';
            $path = $uploadDir . DIRECTORY_SEPARATOR . $fname;
            $img = \Config\Services::image();
            $img->withFile($tmpPath)->resize(500, 500, true)->save($path, 70);
            @unlink($tmpPath);
            $data['foto'] = '/uploads/anggota/' . $fname;
        }

        if ($data['no_anggota'] === '' || $data['nama'] === '') {
            return redirect()->back()->with('error', 'No anggota dan nama wajib diisi');
        }

        $db = \Config\Database::connect();
        try {
            $db->table('anggota')->insert($data);
            $newId = (int) $db->insertID();
            if (!empty($data['foto']) && $newId > 0) {
                $db->table('users')->where('id_anggota', $newId)->update(['foto' => $data['foto']]);
            }
            return redirect()->to('/admin/anggota')->with('message', 'Anggota berhasil ditambahkan');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Gagal menambahkan anggota: ' . $e->getMessage());
        }
    }

    public function updateAnggota()
    {
        $session = session();
        $user = $session->get('user');
        if (!$session->get('isLoggedIn') || !$user) {
            return redirect()->to('/login');
        }
        $role = $user['role'] ?? null;
        if (!in_array($role, ['petugas', 'admin', 'anggota_petugas'], true)) {
            return redirect()->to('/login');
        }

        $id = (int) ($this->request->getPost('id_anggota') ?? 0);
        if ($id <= 0) {
            return redirect()->back()->with('error', 'ID anggota tidak valid');
        }

        $data = [
            'no_anggota' => trim((string) $this->request->getPost('no_anggota')),
            'nama' => trim((string) $this->request->getPost('nama')),
            'jenis_kelamin' => trim((string) $this->request->getPost('jenis_kelamin')) ?: null,
            'alamat' => trim((string) $this->request->getPost('alamat')) ?: null,
            'no_telepon' => trim((string) $this->request->getPost('no_telepon')) ?: null,
            'email' => trim((string) $this->request->getPost('email')) ?: null,
            'status' => trim((string) $this->request->getPost('status')) ?: 'aktif',
            'jenis_anggota' => trim((string) $this->request->getPost('jenis_anggota')) ?: 'aktif',
        ];

        $fotoBase64 = (string) ($this->request->getPost('foto_cropped') ?? '');
        $fotoFile = $this->request->getFile('foto');
        $uploadDir = FCPATH . 'uploads/anggota';
        if (!is_dir($uploadDir)) {
            @mkdir($uploadDir, 0777, true);
        }
        if ($fotoBase64 !== '' && preg_match('#^data:image/\w+;base64,#', $fotoBase64)) {
            $fname = 'foto_' . time() . '_' . bin2hex(random_bytes(4)) . '.webp';
            $path = $uploadDir . DIRECTORY_SEPARATOR . $fname;
            $dataUri = substr($fotoBase64, strpos($fotoBase64, ',') + 1);
            $bin = base64_decode($dataUri);
            file_put_contents($path, $bin);
            $img = \Config\Services::image();
            $img->withFile($path)->resize(500, 500, true)->save($path, 80);
            $data['foto'] = '/uploads/anggota/' . $fname;
        } elseif ($fotoFile && $fotoFile->isValid() && !$fotoFile->hasMoved()) {
            if ($fotoFile->getSize() > 2 * 1024 * 1024) {
                return redirect()->back()->with('error', 'Ukuran foto maksimal 2MB');
            }
            $ext = $fotoFile->getClientExtension();
            $tmp = 'tmp_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
            $fotoFile->move($uploadDir, $tmp);
            $tmpPath = $uploadDir . DIRECTORY_SEPARATOR . $tmp;
            $fname = 'foto_' . time() . '_' . bin2hex(random_bytes(4)) . '.webp';
            $path = $uploadDir . DIRECTORY_SEPARATOR . $fname;
            $img = \Config\Services::image();
            $img->withFile($tmpPath)->resize(500, 500, true)->save($path, 80);
            @unlink($tmpPath);
            $data['foto'] = '/uploads/anggota/' . $fname;
        }

        $db = \Config\Database::connect();
        try {
            $prev = $db->table('anggota')->where('id_anggota', $id)->get()->getRowArray();
            $newStatus = trim((string) ($data['status'] ?? ''));
            $currNo = (string) ($prev['no_anggota'] ?? '');
            $postNo = trim((string) ($data['no_anggota'] ?? ''));
            if ($newStatus === 'aktif' && $currNo === '' && $postNo === '') {
                $actDate = date('Y-m-d');
                $yy = date('y', strtotime($actDate));
                $mm = date('m', strtotime($actDate));
                $prefixYear = 'SEH' . $yy;  // reset nomor urut per tahun
                $maxRow = $db->query('SELECT MAX(CAST(SUBSTRING(no_anggota, 8, 4) AS UNSIGNED)) AS maxseq FROM anggota WHERE no_anggota LIKE ?', [$prefixYear . '%'])->getRowArray();
                $nextSeq = (int) ($maxRow['maxseq'] ?? 0) + 1;
                $data['no_anggota'] = 'SEH' . $yy . $mm . str_pad((string) $nextSeq, 4, '0', STR_PAD_LEFT);
                if (empty($prev['tanggal_gabung'])) {
                    $data['tanggal_gabung'] = $actDate;
                }
            }
            $db->table('anggota')->where('id_anggota', $id)->update($data);
            if (!empty($data['foto'])) {
                $db->table('users')->where('id_anggota', $id)->update(['foto' => $data['foto']]);
            }
            return redirect()->to('/admin/anggota')->with('message', 'Anggota diperbarui');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Gagal memperbarui anggota: ' . $e->getMessage());
        }
    }

    public function deleteAnggota()
    {
        $session = session();
        $user = $session->get('user');
        if (!$session->get('isLoggedIn') || !$user) {
            return redirect()->to('/login');
        }
        $role = $user['role'] ?? null;
        if (!in_array($role, ['petugas', 'admin', 'anggota_petugas'], true)) {
            return redirect()->to('/login');
        }

        $id = (int) ($this->request->getPost('id_anggota') ?? 0);
        if ($id <= 0) {
            return redirect()->back()->with('error', 'ID anggota tidak valid');
        }

        $db = \Config\Database::connect();
        try {
            $db->table('anggota')->where('id_anggota', $id)->delete();
            return redirect()->to('/admin/anggota')->with('message', 'Anggota dihapus');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Gagal menghapus anggota: ' . $e->getMessage());
        }
    }

    public function toggleAnggotaStatus()
    {
        $session = session();
        $user = $session->get('user');
        if (!$session->get('isLoggedIn') || !$user) {
            return $this->response->setStatusCode(401)->setJSON(['error' => 'Unauthorized']);
        }
        $role = $user['role'] ?? null;
        if (!in_array($role, ['petugas', 'admin', 'anggota_petugas'], true)) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Forbidden']);
        }
        $id = (int) ($this->request->getPost('id_anggota') ?? 0);
        $status = strtolower(trim((string) ($this->request->getPost('status') ?? '')));
        if ($id <= 0 || ($status !== 'aktif' && $status !== 'nonaktif')) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'Input tidak valid']);
        }
        $db = \Config\Database::connect();
        $row = $db->table('anggota')->where('id_anggota', $id)->get()->getRowArray();
        if (!$row) {
            return $this->response->setStatusCode(404)->setJSON(['error' => 'Data tidak ditemukan']);
        }
        $update = ['status' => $status];
        if ($status === 'aktif' && trim((string) ($row['no_anggota'] ?? '')) === '') {
            $actDate = date('Y-m-d');
            $yy = date('y', strtotime($actDate));
            $mm = date('m', strtotime($actDate));
            $prefixYear = 'SEH' . $yy;  // reset nomor urut per tahun
            $maxRow = $db->query('SELECT MAX(CAST(SUBSTRING(no_anggota, 8, 4) AS UNSIGNED)) AS maxseq FROM anggota WHERE no_anggota LIKE ?', [$prefixYear . '%'])->getRowArray();
            $nextSeq = (int) ($maxRow['maxseq'] ?? 0) + 1;
            $update['no_anggota'] = 'SEH' . $yy . $mm . str_pad((string) $nextSeq, 4, '0', STR_PAD_LEFT);
            if (empty($row['tanggal_gabung'])) {
                $update['tanggal_gabung'] = $actDate;
            }
        }
        $ok = $db->table('anggota')->where('id_anggota', $id)->update($update);
        if ($ok) {
            try {
                $anggotaRow = $db->table('anggota')->where('id_anggota', $id)->get()->getRowArray();
                $noTelp = (string) ($anggotaRow['no_telepon'] ?? '');
                $noAnggota = (string) ($anggotaRow['no_anggota'] ?? ($update['no_anggota'] ?? ''));
                $namaAnggota = (string) ($anggotaRow['nama'] ?? '');
                $tplRow = $db->table('waha_templates')->where('slug', 'status_anggota')->get()->getRowArray();
                $tpl = (string) ($tplRow['content'] ?? '');
                if ($tpl === '') {
                    $tpl = 'Halo {{nama}} ({{no_anggota}}), status keanggotaan anda: {{status}}.';
                }
                $repl = ['{{nama}}', '{{no_anggota}}', '{{status}}'];
                $vals = [$namaAnggota, $noAnggota, $status];
                $msg = str_replace($repl, $vals, $tpl);
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
                $phoneNum = preg_replace('#\D+#', '', $noTelp !== '' ? $noTelp : (string) ($user['username'] ?? ''));
                if ($phoneNum !== '' && str_starts_with($phoneNum, '0')) {
                    $phoneNum = '62' . substr($phoneNum, 1);
                }
                if ($phoneNum !== '' && ($sendUrl !== '' || $base !== '')) {
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
                    foreach ($targets as $turl) {
                        $payload = ['phone' => $phoneNum, 'chatId' => $phoneNum . '@c.us', 'text' => $msg, 'session' => $sessionId];
                        $resp = $client->post($turl, ['headers' => $headers, 'json' => $payload]);
                        $c = $resp->getStatusCode();
                        if ($c >= 200 && $c < 300) {
                            break;
                        }
                    }
                }
            } catch (\Throwable $e) {
                \log_message('error', 'WA status anggota send failed: ' . $e->getMessage());
            }
            return $this->response->setJSON(['success' => true]);
        }
        return $this->response->setStatusCode(500)->setJSON(['error' => 'Gagal memperbarui status']);
    }

    public function simpananPokok()
    {
        $session = session();
        $user = $session->get('user');
        if (!$session->get('isLoggedIn') || !$user) {
            return redirect()->to('/login');
        }
        $role = $user['role'] ?? null;
        if (!in_array($role, ['petugas', 'admin', 'anggota_petugas'], true)) {
            return redirect()->to('/login');
        }
        $content = view('admin/simpanan/pokok');
        return view('admin/layout', [
            'content' => $content,
            'title' => 'Simpanan Pokok',
        ]);
    }

    public function simpananWajib()
    {
        $session = session();
        $user = $session->get('user');
        if (!$session->get('isLoggedIn') || !$user) {
            return redirect()->to('/login');
        }
        $role = $user['role'] ?? null;
        if (!in_array($role, ['petugas', 'admin', 'anggota_petugas'], true)) {
            return redirect()->to('/login');
        }
        $content = view('admin/simpanan/wajib');
        return view('admin/layout', [
            'content' => $content,
            'title' => 'Simpanan Wajib',
        ]);
    }

    public function simpananSukarela()
    {
        $session = session();
        $user = $session->get('user');
        if (!$session->get('isLoggedIn') || !$user) {
            return redirect()->to('/login');
        }
        $role = $user['role'] ?? null;
        if (!in_array($role, ['petugas', 'admin', 'anggota_petugas'], true)) {
            return redirect()->to('/login');
        }
        $content = view('admin/simpanan/sukarela');
        return view('admin/layout', [
            'content' => $content,
            'title' => 'Simpanan Sukarela',
        ]);
    }

    public function simpananData()
    {
        $session = session();
        $user = $session->get('user');
        if (!$session->get('isLoggedIn') || !$user) {
            return redirect()->to('/login');
        }
        $role = $user['role'] ?? null;
        if (!in_array($role, ['petugas', 'admin', 'anggota_petugas'], true)) {
            return redirect()->to('/login');
        }
        $content = view('admin/simpanan/datasimpanan');
        return view('admin/layout', [
            'content' => $content,
            'title' => 'Data Simpanan',
        ]);
    }

    public function apiSimpananPokok()
    {
        $session = session();
        $user = $session->get('user');
        if (!$session->get('isLoggedIn') || !$user) {
            return $this->response->setStatusCode(401)->setJSON(['error' => 'Unauthorized']);
        }
        $role = $user['role'] ?? null;
        if (!in_array($role, ['petugas', 'admin', 'anggota_petugas'], true)) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Forbidden']);
        }
        $page = max(1, (int) ($this->request->getGet('page') ?? 1));
        $status = trim((string) ($this->request->getGet('status') ?? ''));
        $perPage = 25;
        $offset = ($page - 1) * $perPage;
        $db = \Config\Database::connect();
        $countBase = $db->table('simpanan')->where('jenis_simpanan', 'pokok');
        if ($status === 'aktif' || $status === 'pending') {
            $countBase = $countBase->where('status', $status);
        }
        $count = (int) $countBase->countAllResults();
        $paidCount = (int) $db->table('simpanan')->where('jenis_simpanan', 'pokok')->where('status', 'aktif')->countAllResults();
        $pendingCount = (int) $db->table('simpanan')->where('jenis_simpanan', 'pokok')->where('status', 'pending')->countAllResults();
        $sumRow = $db->table('simpanan')->selectSum('jumlah')->where('jenis_simpanan', 'pokok')->get()->getRowArray();
        $rowsBase = $db
            ->table('simpanan')
            ->select('simpanan.*, anggota.no_anggota, anggota.nama')
            ->join('anggota', 'anggota.id_anggota = simpanan.id_anggota', 'left')
            ->where('simpanan.jenis_simpanan', 'pokok')
            ->orderBy('simpanan.tanggal_simpan', 'DESC');
        if ($status === 'aktif' || $status === 'pending') {
            $rowsBase = $rowsBase->where('simpanan.status', $status);
        }
        $rows = $rowsBase->limit($perPage, $offset)->get()->getResultArray();
        $sumPage = 0.0;
        foreach ($rows as $r) {
            $sumPage += (float) ($r['jumlah'] ?? 0);
        }
        return $this->response->setJSON([
            'data' => $rows,
            'meta' => [
                'page' => $page,
                'perPage' => $perPage,
                'totalItems' => $count,
                'totalPages' => (int) ceil($count / $perPage),
                'sumAll' => (float) ($sumRow['jumlah'] ?? 0),
                'sumPage' => $sumPage,
                'paidCount' => $paidCount,
                'unpaidCount' => $pendingCount,
            ],
        ]);
    }

    public function apiSimpananWajib()
    {
        $session = session();
        $user = $session->get('user');
        if (!$session->get('isLoggedIn') || !$user) {
            return $this->response->setStatusCode(401)->setJSON(['error' => 'Unauthorized']);
        }
        $role = $user['role'] ?? null;
        if (!in_array($role, ['petugas', 'admin', 'anggota_petugas'], true)) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Forbidden']);
        }
        $page = max(1, (int) ($this->request->getGet('page') ?? 1));
        $status = trim((string) ($this->request->getGet('status') ?? ''));
        $perPage = 25;
        $offset = ($page - 1) * $perPage;
        $db = \Config\Database::connect();
        $countBase = $db->table('simpanan')->where('jenis_simpanan', 'wajib');
        if ($status === 'aktif' || $status === 'pending') {
            $countBase = $countBase->where('status', $status);
        }
        $count = (int) $countBase->countAllResults();
        $paidCount = (int) $db->table('simpanan')->where('jenis_simpanan', 'wajib')->where('status', 'aktif')->countAllResults();
        $pendingCount = (int) $db->table('simpanan')->where('jenis_simpanan', 'wajib')->where('status', 'pending')->countAllResults();
        $sumRow = $db->table('simpanan')->selectSum('jumlah')->where('jenis_simpanan', 'wajib')->get()->getRowArray();
        $rowsBase = $db
            ->table('simpanan')
            ->select('simpanan.*, anggota.no_anggota, anggota.nama')
            ->join('anggota', 'anggota.id_anggota = simpanan.id_anggota', 'left')
            ->where('simpanan.jenis_simpanan', 'wajib')
            ->orderBy('simpanan.tanggal_simpan', 'DESC');
        if ($status === 'aktif' || $status === 'pending') {
            $rowsBase = $rowsBase->where('simpanan.status', $status);
        }
        $rows = $rowsBase->limit($perPage, $offset)->get()->getResultArray();
        $sumPage = 0.0;
        foreach ($rows as $r) {
            $sumPage += (float) ($r['jumlah'] ?? 0);
        }
        return $this->response->setJSON([
            'data' => $rows,
            'meta' => [
                'page' => $page,
                'perPage' => $perPage,
                'totalItems' => $count,
                'totalPages' => (int) ceil($count / $perPage),
                'sumAll' => (float) ($sumRow['jumlah'] ?? 0),
                'sumPage' => $sumPage,
                'paidCount' => $paidCount,
                'unpaidCount' => $pendingCount,
            ],
        ]);
    }

    public function apiSimpananSukarela()
    {
        $session = session();
        $user = $session->get('user');
        if (!$session->get('isLoggedIn') || !$user) {
            return $this->response->setStatusCode(401)->setJSON(['error' => 'Unauthorized']);
        }
        $role = $user['role'] ?? null;
        if (!in_array($role, ['petugas', 'admin', 'anggota_petugas'], true)) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Forbidden']);
        }
        $page = max(1, (int) ($this->request->getGet('page') ?? 1));
        $status = trim((string) ($this->request->getGet('status') ?? ''));
        $perPage = 25;
        $offset = ($page - 1) * $perPage;
        $db = \Config\Database::connect();
        $countBase = $db->table('simpanan')->where('jenis_simpanan', 'sukarela');
        if ($status === 'aktif' || $status === 'pending') {
            $countBase = $countBase->where('status', $status);
        }
        $count = (int) $countBase->countAllResults();
        $paidCount = (int) $db->table('simpanan')->where('jenis_simpanan', 'sukarela')->where('status', 'aktif')->countAllResults();
        $pendingCount = (int) $db->table('simpanan')->where('jenis_simpanan', 'sukarela')->where('status', 'pending')->countAllResults();
        $sumRow = $db->table('simpanan')->selectSum('jumlah')->where('jenis_simpanan', 'sukarela')->get()->getRowArray();
        $rowsBase = $db
            ->table('simpanan')
            ->select('simpanan.*, anggota.no_anggota, anggota.nama')
            ->join('anggota', 'anggota.id_anggota = simpanan.id_anggota', 'left')
            ->where('simpanan.jenis_simpanan', 'sukarela')
            ->orderBy('simpanan.tanggal_simpan', 'DESC');
        if ($status === 'aktif' || $status === 'pending') {
            $rowsBase = $rowsBase->where('simpanan.status', $status);
        }
        $rows = $rowsBase->limit($perPage, $offset)->get()->getResultArray();
        $sumPage = 0.0;
        foreach ($rows as $r) {
            $sumPage += (float) ($r['jumlah'] ?? 0);
        }
        return $this->response->setJSON([
            'data' => $rows,
            'meta' => [
                'page' => $page,
                'perPage' => $perPage,
                'totalItems' => $count,
                'totalPages' => (int) ceil($count / $perPage),
                'sumAll' => (float) ($sumRow['jumlah'] ?? 0),
                'sumPage' => $sumPage,
                'paidCount' => $paidCount,
                'unpaidCount' => $pendingCount,
            ],
        ]);
    }

    public function apiSimpananData()
    {
        $session = session();
        $user = $session->get('user');
        if (!$session->get('isLoggedIn') || !$user) {
            return $this->response->setStatusCode(401)->setJSON(['error' => 'Unauthorized']);
        }
        $role = $user['role'] ?? null;
        if (!in_array($role, ['petugas', 'admin', 'anggota_petugas'], true)) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Forbidden']);
        }
        $page = max(1, (int) ($this->request->getGet('page') ?? 1));
        $perPage = 25;
        $offset = ($page - 1) * $perPage;
        $db = \Config\Database::connect();
        $base = $db->table('simpanan')->where('status !=', 'pending');
        $totalItems = (int) $base->countAllResults();
        $sumRow = $db->table('simpanan')->selectSum('jumlah')->where('status', 'aktif')->get()->getRowArray();
        $rows = $db
            ->table('simpanan')
            ->select('simpanan.*, anggota.no_anggota, anggota.nama')
            ->join('anggota', 'anggota.id_anggota = simpanan.id_anggota', 'left')
            ->where('simpanan.status !=', 'pending')
            ->orderBy('simpanan.tanggal_simpan', 'DESC')
            ->limit($perPage, $offset)
            ->get()
            ->getResultArray();
        $sumPage = 0.0;
        foreach ($rows as $r) {
            $sumPage += (float) ($r['jumlah'] ?? 0);
        }
        return $this->response->setJSON([
            'data' => $rows,
            'meta' => [
                'page' => $page,
                'perPage' => $perPage,
                'totalItems' => $totalItems,
                'totalPages' => (int) ceil(($totalItems ?: 0) / $perPage),
                'sumAll' => (float) ($sumRow['jumlah'] ?? 0),
                'sumPage' => $sumPage,
            ],
        ]);
    }

    public function apiSimpananSummary()
    {
        $session = session();
        $user = $session->get('user');
        if (!$session->get('isLoggedIn') || !$user) {
            return $this->response->setStatusCode(401)->setJSON(['error' => 'Unauthorized']);
        }
        $role = $user['role'] ?? null;
        if (!in_array($role, ['petugas', 'admin', 'anggota_petugas'], true)) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Forbidden']);
        }
        $db = \Config\Database::connect();
        $sumPokok = (float) ($db->table('simpanan')->selectSum('jumlah')->where('jenis_simpanan', 'pokok')->where('status', 'aktif')->get()->getRowArray()['jumlah'] ?? 0);
        $sumWajib = (float) ($db->table('simpanan')->selectSum('jumlah')->where('jenis_simpanan', 'wajib')->where('status', 'aktif')->get()->getRowArray()['jumlah'] ?? 0);
        $sumSukarela = (float) ($db->table('simpanan')->selectSum('jumlah')->where('jenis_simpanan', 'sukarela')->where('status', 'aktif')->get()->getRowArray()['jumlah'] ?? 0);
        return $this->response->setJSON([
            'sumPokok' => $sumPokok,
            'sumWajib' => $sumWajib,
            'sumSukarela' => $sumSukarela,
        ]);
    }

    public function activateSukarela()
    {
        $session = session();
        $user = $session->get('user');
        if (!$session->get('isLoggedIn') || !$user) {
            return redirect()->to('/login');
        }
        $role = $user['role'] ?? null;
        if (!in_array($role, ['petugas', 'admin', 'anggota_petugas'], true)) {
            return redirect()->to('/login');
        }
        $id = (int) ($this->request->getPost('id_simpanan') ?? 0);
        if ($id <= 0) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'ID tidak valid']);
        }
        $db = \Config\Database::connect();
        $row = $db->table('simpanan')->where('id_simpanan', $id)->get()->getRowArray();
        if (!$row || ($row['jenis_simpanan'] ?? '') !== 'sukarela') {
            return $this->response->setStatusCode(404)->setJSON(['error' => 'Data tidak ditemukan']);
        }
        if (($row['status'] ?? '') !== 'pending') {
            return $this->response->setStatusCode(409)->setJSON(['error' => 'Status bukan pending']);
        }
        $ok = $db->table('simpanan')->where('id_simpanan', $id)->update(['status' => 'aktif']);
        if ($ok) {
            return $this->response->setJSON(['success' => true]);
        }
        return $this->response->setStatusCode(500)->setJSON(['error' => 'Gagal mengaktifkan']);
    }

    public function toggleSukarelaStatus()
    {
        $session = session();
        $user = $session->get('user');
        if (!$session->get('isLoggedIn') || !$user) {
            return $this->response->setStatusCode(401)->setJSON(['error' => 'Unauthorized']);
        }
        $role = $user['role'] ?? null;
        if (!in_array($role, ['petugas', 'admin', 'anggota_petugas'], true)) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Forbidden']);
        }
        $id = (int) ($this->request->getPost('id_simpanan') ?? 0);
        $status = strtolower(trim((string) ($this->request->getPost('status') ?? '')));
        if ($id <= 0 || ($status !== 'aktif' && $status !== 'pending')) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'Input tidak valid']);
        }
        $db = \Config\Database::connect();
        $row = $db->table('simpanan')->where('id_simpanan', $id)->get()->getRowArray();
        if (!$row || ($row['jenis_simpanan'] ?? '') !== 'sukarela') {
            return $this->response->setStatusCode(404)->setJSON(['error' => 'Data tidak ditemukan']);
        }
        $ok = $db->table('simpanan')->where('id_simpanan', $id)->update(['status' => $status]);
        if ($ok) {
            return $this->response->setJSON(['success' => true]);
        }
        return $this->response->setStatusCode(500)->setJSON(['error' => 'Gagal memperbarui status']);
    }

    public function toggleWajibStatus()
    {
        $session = session();
        $user = $session->get('user');
        if (!$session->get('isLoggedIn') || !$user) {
            return $this->response->setStatusCode(401)->setJSON(['error' => 'Unauthorized']);
        }
        $role = $user['role'] ?? null;
        if (!in_array($role, ['petugas', 'admin', 'anggota_petugas'], true)) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Forbidden']);
        }
        $id = (int) ($this->request->getPost('id_simpanan') ?? 0);
        $status = strtolower(trim((string) ($this->request->getPost('status') ?? '')));
        if ($id <= 0 || ($status !== 'aktif' && $status !== 'pending')) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'Input tidak valid']);
        }
        $db = \Config\Database::connect();
        $row = $db->table('simpanan')->where('id_simpanan', $id)->get()->getRowArray();
        if (!$row || ($row['jenis_simpanan'] ?? '') !== 'wajib') {
            return $this->response->setStatusCode(404)->setJSON(['error' => 'Data tidak ditemukan']);
        }
        $ok = $db->table('simpanan')->where('id_simpanan', $id)->update(['status' => $status]);
        if ($ok) {
            return $this->response->setJSON(['success' => true]);
        }
        return $this->response->setStatusCode(500)->setJSON(['error' => 'Gagal memperbarui status']);
    }

    public function togglePokokStatus()
    {
        $session = session();
        $user = $session->get('user');
        if (!$session->get('isLoggedIn') || !$user) {
            return $this->response->setStatusCode(401)->setJSON(['error' => 'Unauthorized']);
        }
        $role = $user['role'] ?? null;
        if (!in_array($role, ['petugas', 'admin', 'anggota_petugas'], true)) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Forbidden']);
        }
        $id = (int) ($this->request->getPost('id_simpanan') ?? 0);
        $status = strtolower(trim((string) ($this->request->getPost('status') ?? '')));
        if ($id <= 0 || ($status !== 'aktif' && $status !== 'pending')) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'Input tidak valid']);
        }
        $db = \Config\Database::connect();
        $row = $db->table('simpanan')->where('id_simpanan', $id)->get()->getRowArray();
        if (!$row || ($row['jenis_simpanan'] ?? '') !== 'pokok') {
            return $this->response->setStatusCode(404)->setJSON(['error' => 'Data tidak ditemukan']);
        }
        $ok = $db->table('simpanan')->where('id_simpanan', $id)->update(['status' => $status]);
        if ($ok) {
            return $this->response->setJSON(['success' => true]);
        }
        return $this->response->setStatusCode(500)->setJSON(['error' => 'Gagal memperbarui status']);
    }
}
