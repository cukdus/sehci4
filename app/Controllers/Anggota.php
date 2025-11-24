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
}
