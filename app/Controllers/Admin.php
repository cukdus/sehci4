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
}
