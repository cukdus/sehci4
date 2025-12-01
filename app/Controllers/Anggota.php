<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class Anggota extends Controller
{
    public function index()
    {
        $session = session();
        $user = $session->get('user');
        if (!$session->get('isLoggedIn') || !$user) {
            return redirect()->to('/login');
        }
        $role = $user['role'] ?? null;
        if (!in_array($role, ['anggota', 'anggota_petugas'], true)) {
            return redirect()->to('/login');
        }
        $content = view('anggota/Dashboard');
        return view('anggota/layout', [
            'content' => $content,
            'title' => 'Dashboard',
        ]);
    }

    public function profil()
    {
        $session = session();
        $user = $session->get('user');
        if (!$session->get('isLoggedIn') || !$user) {
            return redirect()->to('/login');
        }
        $role = $user['role'] ?? null;
        if (!in_array($role, ['anggota', 'anggota_petugas'], true)) {
            return redirect()->to('/login');
        }

        $idAnggota = (int) ($user['id_anggota'] ?? 0);
        if ($idAnggota <= 0) {
            return redirect()->to('/anggota')->with('error', 'Profil anggota tidak tersedia');
        }

        $db = \Config\Database::connect();
        $anggota = $db->table('anggota')->where('id_anggota', $idAnggota)->get()->getRowArray();
        if (!$anggota) {
            return redirect()->to('/anggota')->with('error', 'Data anggota tidak ditemukan');
        }

        $content = view('anggota/profil/lihatprofil', ['anggota' => $anggota]);
        return view('anggota/layout', [
            'content' => $content,
            'title' => 'Profil Anggota',
        ]);
    }

    public function profilEdit()
    {
        $session = session();
        $user = $session->get('user');
        if (!$session->get('isLoggedIn') || !$user) {
            return redirect()->to('/login');
        }
        $role = $user['role'] ?? null;
        if (!in_array($role, ['anggota', 'anggota_petugas'], true)) {
            return redirect()->to('/login');
        }
        $idAnggota = (int) ($user['id_anggota'] ?? 0);
        if ($idAnggota <= 0) {
            return redirect()->to('/anggota')->with('error', 'Profil anggota tidak tersedia');
        }
        $db = \Config\Database::connect();
        $anggota = $db->table('anggota')->where('id_anggota', $idAnggota)->get()->getRowArray();
        if (!$anggota) {
            return redirect()->to('/anggota')->with('error', 'Data anggota tidak ditemukan');
        }
        $content = view('anggota/profil/editprofil', ['anggota' => $anggota]);
        return view('anggota/layout', [
            'content' => $content,
            'title' => 'Edit Profil Anggota',
        ]);
    }

    public function profilUpdate()
    {
        $session = session();
        $user = $session->get('user');
        if (!$session->get('isLoggedIn') || !$user) {
            return redirect()->to('/login');
        }
        $role = $user['role'] ?? null;
        if (!in_array($role, ['anggota', 'anggota_petugas'], true)) {
            return redirect()->to('/login');
        }

        $id = (int) ($this->request->getPost('id_anggota') ?? (int) ($user['id_anggota'] ?? 0));
        if ($id <= 0) {
            return redirect()->back()->with('error', 'ID anggota tidak valid');
        }

        $data = [
            'nama' => trim((string) $this->request->getPost('nama')),
            'jenis_kelamin' => trim((string) $this->request->getPost('jenis_kelamin')) ?: null,
            'tempat_lahir' => trim((string) $this->request->getPost('tempat_lahir')) ?: null,
            'tanggal_lahir' => trim((string) $this->request->getPost('tanggal_lahir')) ?: null,
            'alamat' => trim((string) $this->request->getPost('alamat')) ?: null,
            'no_telepon' => trim((string) $this->request->getPost('no_telepon')) ?: null,
            'no_ktp' => trim((string) $this->request->getPost('no_ktp')) ?: null,
            'no_kk' => trim((string) $this->request->getPost('no_kk')) ?: null,
            'no_npwp' => trim((string) $this->request->getPost('no_npwp')) ?: null,
            'pengalaman_kerja' => trim((string) $this->request->getPost('pengalaman_kerja')) ?: null,
            'pengalaman_organisasi' => trim((string) $this->request->getPost('pengalaman_organisasi')) ?: null,
            'email' => trim((string) $this->request->getPost('email')) ?: null,
        ];

        $skills = $this->request->getPost('basic_skill');
        if (!is_array($skills)) {
            $skills = $this->request->getPost('basic_skill[]');
        }
        if (!is_array($skills)) {
            $skills = [];
        }
        $skills = array_map('trim', $skills);
        $skills = array_filter($skills, function ($v) {
            return $v !== '' && strtolower($v) !== 'other';
        });
        $skills = array_values(array_unique($skills));
        $otherSkill = trim((string) $this->request->getPost('basic_skill_other'));
        if ($otherSkill !== '') {
            $skills[] = $otherSkill;
        }
        $data['basic_skill'] = !empty($skills) ? json_encode($skills, JSON_UNESCAPED_UNICODE) : null;

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
            $fields = $db->getFieldNames('anggota');
            $allowed = array_flip($fields);
            $dataFiltered = array_intersect_key($data, $allowed);
            $skippedBasicSkill = !in_array('basic_skill', $fields, true) && !empty($data['basic_skill']);
            $db->table('anggota')->where('id_anggota', $id)->update($dataFiltered);
            if (!empty($data['foto'])) {
                $db->table('users')->where('id_anggota', $id)->update(['foto' => $data['foto']]);
            }
            if ($skippedBasicSkill) {
                $session->setFlashdata('warning', 'Basic Skill tidak disimpan karena kolom tidak tersedia');
            }
            return redirect()->to('/anggota/profil')->with('message', 'Profil berhasil diperbarui');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Gagal memperbarui profil: ' . $e->getMessage());
        }
    }

    public function permohonanBerhenti()
    {
        $session = session();
        $user = $session->get('user');
        if (!$session->get('isLoggedIn') || !$user) {
            return redirect()->to('/login');
        }
        $role = $user['role'] ?? null;
        if (!in_array($role, ['anggota', 'anggota_petugas'], true)) {
            return redirect()->to('/login');
        }
        $idAnggota = (int) ($user['id_anggota'] ?? 0);
        if ($idAnggota <= 0) {
            return redirect()->to('/anggota')->with('error', 'Profil anggota tidak tersedia');
        }
        $alasan = trim((string) ($this->request->getPost('alasan') ?? ''));
        $db = \Config\Database::connect();
        try {
            $db->table('anggota')->where('id_anggota', $idAnggota)->update([
                'alasan_berhenti' => $alasan !== '' ? $alasan : null,
                'tanggal_berhenti' => date('Y-m-d'),
            ]);
            return redirect()->to('/anggota/profil')->with('message', 'Permohonan berhenti dikirim, menunggu persetujuan petugas');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Gagal mengajukan berhenti: ' . $e->getMessage());
        }
    }

    public function simpananPokok()
    {
        $session = session();
        $user = $session->get('user');
        if (!$session->get('isLoggedIn') || !$user) {
            return redirect()->to('/login');
        }
        $role = $user['role'] ?? null;
        if (!in_array($role, ['anggota', 'anggota_petugas'], true)) {
            return redirect()->to('/login');
        }
        $content = view('anggota/simpanan/pokok');
        return view('anggota/layout', [
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
        if (!in_array($role, ['anggota', 'anggota_petugas'], true)) {
            return redirect()->to('/login');
        }
        $content = view('anggota/simpanan/wajib');
        return view('anggota/layout', [
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
        if (!in_array($role, ['anggota', 'anggota_petugas'], true)) {
            return redirect()->to('/login');
        }
        $content = view('anggota/simpanan/sukarela');
        return view('anggota/layout', [
            'content' => $content,
            'title' => 'Simpanan Sukarela',
        ]);
    }

    public function hibah()
    {
        $session = session();
        $user = $session->get('user');
        if (!$session->get('isLoggedIn') || !$user) {
            return redirect()->to('/login');
        }
        $role = $user['role'] ?? null;
        if (!in_array($role, ['anggota', 'anggota_petugas'], true)) {
            return redirect()->to('/login');
        }
        $content = view('anggota/simpanan/hibah');
        return view('anggota/layout', [
            'content' => $content,
            'title' => 'Hibah',
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
        if (!in_array($role, ['anggota', 'anggota_petugas'], true)) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Forbidden']);
        }
        $idAnggota = (int) ($user['id_anggota'] ?? 0);
        $page = max(1, (int) ($this->request->getGet('page') ?? 1));
        $perPage = 25;
        $offset = ($page - 1) * $perPage;
        $db = \Config\Database::connect();
        $base = $db->table('simpanan')->where('id_anggota', $idAnggota)->where('jenis_simpanan', 'pokok');
        $totalItems = (int) $base->countAllResults();
        $sumAllRow = $db->table('simpanan')->selectSum('jumlah')->where('id_anggota', $idAnggota)->where('jenis_simpanan', 'pokok')->get()->getRowArray();
        $rows = $db->table('simpanan')->where('id_anggota', $idAnggota)->where('jenis_simpanan', 'pokok')->orderBy('tanggal_simpan', 'DESC')->limit($perPage, $offset)->get()->getResultArray();
        $sumPage = 0.0;
        foreach ($rows as $r) {
            $sumPage += (float) ($r['jumlah'] ?? 0);
        }
        $payload = [
            'data' => $rows,
            'meta' => [
                'page' => $page,
                'perPage' => $perPage,
                'totalItems' => $totalItems,
                'totalPages' => (int) ceil($totalItems / $perPage),
                'sumAll' => (float) ($sumAllRow['jumlah'] ?? 0),
                'sumPage' => $sumPage,
            ],
        ];
        return $this->response->setJSON($payload);
    }

    public function apiSimpananWajib()
    {
        $session = session();
        $user = $session->get('user');
        if (!$session->get('isLoggedIn') || !$user) {
            return $this->response->setStatusCode(401)->setJSON(['error' => 'Unauthorized']);
        }
        $role = $user['role'] ?? null;
        if (!in_array($role, ['anggota', 'anggota_petugas'], true)) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Forbidden']);
        }
        $idAnggota = (int) ($user['id_anggota'] ?? 0);
        $page = max(1, (int) ($this->request->getGet('page') ?? 1));
        $perPage = 25;
        $offset = ($page - 1) * $perPage;
        $db = \Config\Database::connect();
        $totalItems = (int) $db->table('simpanan')->where('id_anggota', $idAnggota)->where('jenis_simpanan', 'wajib')->countAllResults();
        $sumAllRow = $db->table('simpanan')->selectSum('jumlah')->where('id_anggota', $idAnggota)->where('jenis_simpanan', 'wajib')->get()->getRowArray();
        $rows = $db->table('simpanan')->where('id_anggota', $idAnggota)->where('jenis_simpanan', 'wajib')->orderBy('tanggal_simpan', 'DESC')->limit($perPage, $offset)->get()->getResultArray();
        $sumPage = 0.0;
        foreach ($rows as $r) {
            $sumPage += (float) ($r['jumlah'] ?? 0);
        }
        $payload = [
            'data' => $rows,
            'meta' => [
                'page' => $page,
                'perPage' => $perPage,
                'totalItems' => $totalItems,
                'totalPages' => (int) ceil($totalItems / $perPage),
                'sumAll' => (float) ($sumAllRow['jumlah'] ?? 0),
                'sumPage' => $sumPage,
            ],
        ];
        return $this->response->setJSON($payload);
    }

    public function apiSimpananSukarela()
    {
        $session = session();
        $user = $session->get('user');
        if (!$session->get('isLoggedIn') || !$user) {
            return $this->response->setStatusCode(401)->setJSON(['error' => 'Unauthorized']);
        }
        $role = $user['role'] ?? null;
        if (!in_array($role, ['anggota', 'anggota_petugas'], true)) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Forbidden']);
        }
        $idAnggota = (int) ($user['id_anggota'] ?? 0);
        $page = max(1, (int) ($this->request->getGet('page') ?? 1));
        $perPage = 25;
        $offset = ($page - 1) * $perPage;
        $db = \Config\Database::connect();
        $totalItems = (int) $db->table('simpanan')->where('id_anggota', $idAnggota)->where('jenis_simpanan', 'sukarela')->countAllResults();
        $sumAllRow = $db->table('simpanan')->selectSum('jumlah')->where('id_anggota', $idAnggota)->where('jenis_simpanan', 'sukarela')->get()->getRowArray();
        $rows = $db->table('simpanan')->where('id_anggota', $idAnggota)->where('jenis_simpanan', 'sukarela')->orderBy('tanggal_simpan', 'DESC')->limit($perPage, $offset)->get()->getResultArray();
        $sumPage = 0.0;
        foreach ($rows as $r) {
            $sumPage += (float) ($r['jumlah'] ?? 0);
        }
        $payload = [
            'data' => $rows,
            'meta' => [
                'page' => $page,
                'perPage' => $perPage,
                'totalItems' => $totalItems,
                'totalPages' => (int) ceil($totalItems / $perPage),
                'sumAll' => (float) ($sumAllRow['jumlah'] ?? 0),
                'sumPage' => $sumPage,
            ],
        ];
        return $this->response->setJSON($payload);
    }

    public function apiHibah()
    {
        $session = session();
        $user = $session->get('user');
        if (!$session->get('isLoggedIn') || !$user) {
            return $this->response->setStatusCode(401)->setJSON(['error' => 'Unauthorized']);
        }
        $role = $user['role'] ?? null;
        if (!in_array($role, ['anggota', 'anggota_petugas'], true)) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Forbidden']);
        }
        $idAnggota = (int) ($user['id_anggota'] ?? 0);
        $page = max(1, (int) ($this->request->getGet('page') ?? 1));
        $perPage = 25;
        $offset = ($page - 1) * $perPage;
        $db = \Config\Database::connect();
        $data = [];
        $totalItems = 0;
        $sumAll = 0.0;
        try {
            $tables = $db->listTables();
            if (in_array('hibah', $tables, true)) {
                $totalItems = (int) $db->table('hibah')->where('id_anggota', $idAnggota)->countAllResults();
                $sumRow = $db->table('hibah')->selectSum('jumlah')->where('id_anggota', $idAnggota)->get()->getRowArray();
                $sumAll = (float) ($sumRow['jumlah'] ?? 0);
                $data = $db->table('hibah')->where('id_anggota', $idAnggota)->orderBy('tanggal', 'DESC')->limit($perPage, $offset)->get()->getResultArray();
            }
        } catch (\Throwable $e) {
        }
        $sumPage = 0.0;
        foreach ($data as $r) {
            $sumPage += (float) ($r['jumlah'] ?? 0);
        }
        $payload = [
            'data' => $data,
            'meta' => [
                'page' => $page,
                'perPage' => $perPage,
                'totalItems' => $totalItems,
                'totalPages' => (int) ceil(($totalItems ?: 0) / $perPage),
                'sumAll' => $sumAll,
                'sumPage' => $sumPage,
            ],
        ];
        return $this->response->setJSON($payload);
    }
}
