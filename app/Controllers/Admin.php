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
}
