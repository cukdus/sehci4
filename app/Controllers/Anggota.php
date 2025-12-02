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

    public function simpananSukarelaTambah()
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
        $content = view('anggota/simpanan/tambahsukarela');
        return view('anggota/layout', [
            'content' => $content,
            'title' => 'Tambah Simpanan Sukarela',
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

    public function dataSimpanan()
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
        $content = view('anggota/simpanan/datasimpanan');
        return view('anggota/layout', [
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
        $tipe = trim((string) ($this->request->getGet('tipe') ?? ''));
        $perPage = 25;
        $offset = ($page - 1) * $perPage;
        $db = \Config\Database::connect();
        $base = $db->table('simpanan')->where('id_anggota', $idAnggota)->where('jenis_simpanan', 'sukarela');
        if ($tipe !== '') {
            $base = $base->where('tipe_sukarela', $tipe);
        }
        $totalItems = (int) $base->countAllResults();
        $sumAllQ = $db
            ->table('simpanan')
            ->selectSum('jumlah')
            ->where('id_anggota', $idAnggota)
            ->where('jenis_simpanan', 'sukarela')
            ->where('status !=', 'pending');
        if ($tipe !== '') {
            $sumAllQ = $sumAllQ->where('tipe_sukarela', $tipe);
        }
        $sumAllRow = $sumAllQ->get()->getRowArray();
        $rowsQ = $db->table('simpanan')->where('id_anggota', $idAnggota)->where('jenis_simpanan', 'sukarela');
        if ($tipe !== '') {
            $rowsQ = $rowsQ->where('tipe_sukarela', $tipe);
        }
        $rows = $rowsQ->orderBy('tanggal_simpan', 'DESC')->limit($perPage, $offset)->get()->getResultArray();
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

    public function apiSimpananData()
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
        $base = $db->table('simpanan')->where('id_anggota', $idAnggota)->where('status !=', 'pending');
        $totalItems = (int) $base->countAllResults();
        $sumRow = $db->table('simpanan')->selectSum('jumlah')->where('id_anggota', $idAnggota)->where('status !=', 'pending')->get()->getRowArray();
        $sumAll = (float) ($sumRow['jumlah'] ?? 0);
        $rows = $db
            ->table('simpanan')
            ->select('id_simpanan, tanggal_simpan, jenis_simpanan, jumlah, status')
            ->where('id_anggota', $idAnggota)
            ->where('status !=', 'pending')
            ->orderBy('tanggal_simpan', 'DESC')
            ->limit($perPage, $offset)
            ->get()
            ->getResultArray();
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
                'totalPages' => (int) ceil(($totalItems ?: 0) / $perPage),
                'sumAll' => $sumAll,
                'sumPage' => $sumPage,
            ],
        ];
        return $this->response->setJSON($payload);
    }

    public function apiSimpananSummary()
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
        $db = \Config\Database::connect();
        $sumPokok = (float) ($db->table('simpanan')->selectSum('jumlah')->where('id_anggota', $idAnggota)->where('jenis_simpanan', 'pokok')->where('status', 'aktif')->get()->getRowArray()['jumlah'] ?? 0);
        $sumWajib = (float) ($db->table('simpanan')->selectSum('jumlah')->where('id_anggota', $idAnggota)->where('jenis_simpanan', 'wajib')->where('status', 'aktif')->get()->getRowArray()['jumlah'] ?? 0);
        $sumSukarela = (float) ($db->table('simpanan')->selectSum('jumlah')->where('id_anggota', $idAnggota)->where('jenis_simpanan', 'sukarela')->where('status', 'aktif')->get()->getRowArray()['jumlah'] ?? 0);
        $sumPinjaman = 0.0;
        try {
            $tables = $db->listTables();
            if (in_array('pinjaman', $tables, true)) {
                $sumPinjaman = (float) ($db->table('pinjaman')->selectSum('jumlah_pinjaman')->where('id_anggota', $idAnggota)->get()->getRowArray()['jumlah_pinjaman'] ?? 0);
            }
        } catch (\Throwable $e) {
        }
        return $this->response->setJSON([
            'sumPokok' => $sumPokok,
            'sumWajib' => $sumWajib,
            'sumSukarela' => $sumSukarela,
            'sumPinjaman' => $sumPinjaman,
        ]);
    }

    public function apiPinjamanData()
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
        $totalItems = 0;
        $sumAll = 0.0;
        $rows = [];
        try {
            $tables = $db->listTables();
            if (in_array('pinjaman', $tables, true)) {
                $totalItems = (int) $db->table('pinjaman')->where('id_anggota', $idAnggota)->countAllResults();
                $sumRow = $db->table('pinjaman')->selectSum('jumlah_pinjaman')->where('id_anggota', $idAnggota)->get()->getRowArray();
                $sumAll = (float) ($sumRow['jumlah_pinjaman'] ?? 0);
                $rows = $db
                    ->table('pinjaman')
                    ->select('id_pinjaman, tanggal_pinjam, jumlah_pinjaman, status')
                    ->where('id_anggota', $idAnggota)
                    ->orderBy('tanggal_pinjam', 'DESC')
                    ->limit($perPage, $offset)
                    ->get()
                    ->getResultArray();
            }
        } catch (\Throwable $e) {
        }
        $sumPage = 0.0;
        foreach ($rows as $r) {
            $sumPage += (float) ($r['jumlah_pinjaman'] ?? 0);
        }
        $payload = [
            'data' => $rows,
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

    public function tambahSukarela()
    {
        $session = session();
        $user = $session->get('user');
        \log_message('info', 'tambahSukarela entered, method=' . $this->request->getMethod() . ', user=' . json_encode($user, JSON_UNESCAPED_SLASHES));
        if (!$session->get('isLoggedIn') || !$user) {
            return redirect()->to('/login');
        }
        $role = $user['role'] ?? null;
        if (!in_array($role, ['anggota', 'anggota_petugas'], true)) {
            return redirect()->to('/login');
        }
        if (strtolower($this->request->getMethod()) === 'post') {
            $idAnggota = (int) ($user['id_anggota'] ?? 0);
            $postIdAnggota = (int) ($this->request->getPost('id_anggota') ?? 0);
            if ($idAnggota <= 0 && $postIdAnggota > 0) {
                $idAnggota = $postIdAnggota;
            }
            if ($idAnggota <= 0) {
                $idUser = (int) ($user['id_user'] ?? 0);
                if ($idUser > 0) {
                    try {
                        $db = \Config\Database::connect();
                        $u = $db->table('users')->select('id_anggota')->where('id_user', $idUser)->get()->getRowArray();
                        if (!empty($u['id_anggota'])) {
                            $idAnggota = (int) $u['id_anggota'];
                            $user['id_anggota'] = $idAnggota;
                            $session->set('user', $user);
                        }
                    } catch (\Throwable $e) {
                        \log_message('error', 'Lookup id_anggota by id_user failed: ' . $e->getMessage());
                    }
                }
            }
            if ($idAnggota <= 0) {
                \log_message('info', 'Sukarela validation failed: id_anggota invalid');
                return redirect()->to('/anggota/simpanan/sukarela')->with('error', 'Akun tidak terkait data anggota.');
            }
            $tanggal = $this->request->getPost('tanggal_simpan');
            $jumlahRaw = trim((string) ($this->request->getPost('jumlah') ?? '0'));
            $jumlah = str_replace([',', ' '], ['', ''], $jumlahRaw);
            if ($jumlah === '' || !is_numeric($jumlah)) {
                $jumlah = '0';
            }
            $tipe = trim((string) ($this->request->getPost('tipe_sukarela') ?? ''));
            $jangka = (int) ($this->request->getPost('jangka_waktu') ?? 0);
            \log_message('info', 'Sukarela POST data: tanggal=' . ($tanggal ?: 'NULL') . ', jumlah=' . $jumlah . ', tipe=' . ($tipe ?: 'NULL'));
            if (!$tanggal || $jumlah <= 0 || $tipe === '' || ($tipe === 'berjangka' && $jangka <= 0)) {
                \log_message('info', 'Sukarela validation failed: tanggal=' . ($tanggal ?: 'NULL') . ', jumlah=' . $jumlah . ', tipe=' . ($tipe ?: 'NULL'));
                return redirect()->to('/anggota/simpanan/sukarela')->with('error', 'Tanggal, jumlah, dan tipe wajib diisi.');
            }
            $db = \Config\Database::connect();
            // Verifikasi konsistensi id_anggota terhadap user.id_user
            try {
                $idUser = (int) ($user['id_user'] ?? 0);
                if ($idUser > 0) {
                    $u = $db->table('users')->select('id_anggota')->where('id_user', $idUser)->get()->getRowArray();
                    if (!empty($u['id_anggota']) && (int) $u['id_anggota'] !== $idAnggota) {
                        $idAnggota = (int) $u['id_anggota'];
                        $user['id_anggota'] = $idAnggota;
                        $session->set('user', $user);
                    }
                }
            } catch (\Throwable $e) {
                \log_message('error', 'Verify id_anggota failed: ' . $e->getMessage());
            }
            try {
                $dbNameRow = $db->query('SELECT DATABASE() AS db')->getRowArray();
                $dbName = (string) ($dbNameRow['db'] ?? '');
                $tables = $db->listTables();
                $hasSimpanan = in_array('simpanan', $tables, true);
                \log_message('info', 'Insert sukarela start on DB=' . $dbName . ' tableExists=' . ($hasSimpanan ? 'yes' : 'no') . ' id_anggota=' . $idAnggota . ', tanggal=' . $tanggal . ', jumlah=' . $jumlah . ', tipe=' . $tipe);
                if (!$hasSimpanan) {
                    return redirect()->to('/anggota/simpanan/sukarela')->with('error', 'Tabel simpanan tidak ditemukan pada database ' . ($dbName ?: '(unknown)'));
                }
            } catch (\Throwable $e) {
                \log_message('error', 'DB diagnose failed: ' . $e->getMessage());
            }
            $dataInsert = [
                'id_anggota' => $idAnggota,
                'tanggal_simpan' => $tanggal,
                'jenis_simpanan' => 'sukarela',
                'jumlah' => number_format((float) $jumlah, 2, '.', ''),
                'status' => 'pending',
                'tipe_sukarela' => $tipe,
                'jangka_waktu' => $tipe === 'berjangka' ? $jangka : null,
            ];
            try {
                $fields = $db->getFieldNames('simpanan');
            } catch (\Throwable $e) {
                $fields = ['id_anggota', 'tanggal_simpan', 'jenis_simpanan', 'jumlah', 'status', 'tipe_sukarela', 'jangka_waktu'];
            }
            $allowed = array_flip($fields);
            $dataFiltered = array_intersect_key($dataInsert, $allowed);
            \log_message('info', 'Filtered insert payload: ' . json_encode($dataFiltered, JSON_UNESCAPED_SLASHES));
            try {
                $ok = $db->table('simpanan')->insert($dataFiltered);
            } catch (\Throwable $e) {
                \log_message('error', 'Insert sukarela exception: ' . $e->getMessage());
                $ok = false;
            }
            \log_message('info', 'Last query: ' . (string) $db->getLastQuery());
            if ($ok) {
                $newId = (int) $db->insertID();
                \log_message('info', 'Insert sukarela success id=' . $newId);
                return redirect()->to('/anggota/simpanan/sukarela')->with('success', 'Simpanan sukarela berhasil ditambahkan.');
            }
            $err = $db->error();
            \log_message('error', 'Insert sukarela failed: ' . json_encode($err, JSON_UNESCAPED_SLASHES));
            $msg = 'Gagal menyimpan.';
            if (!empty($err['message'])) {
                $msg .= ' ' . $err['message'];
            }
            return redirect()->to('/anggota/simpanan/sukarela')->with('error', $msg);
        }
        return redirect()->to('/anggota/simpanan/sukarela');
    }

    public function tambahWajib()
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
        if ($this->request->getMethod() !== 'post') {
            return redirect()->to('/anggota/simpanan/wajib');
        }
        $idAnggota = (int) ($user['id_anggota'] ?? 0);
        $tanggal = $this->request->getPost('tanggal_simpan');
        $jumlah = (float) ($this->request->getPost('jumlah') ?? 0);

        if (!$tanggal || $jumlah <= 0) {
            return redirect()->to('/anggota/simpanan/wajib')->with('error', 'Tanggal dan jumlah wajib diisi.');
        }
        $db = \Config\Database::connect();
        $ok = $db->table('simpanan')->insert([
            'id_anggota' => $idAnggota,
            'tanggal_simpan' => $tanggal,
            'jenis_simpanan' => 'wajib',
            'jumlah' => $jumlah,
            'status' => 'tercatat',
        ]);
        if ($ok) {
            return redirect()->to('/anggota/simpanan/wajib')->with('success', 'Simpanan wajib berhasil ditambahkan.');
        }
        return redirect()->to('/anggota/simpanan/wajib')->with('error', 'Gagal menyimpan.');
    }
}
