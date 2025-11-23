<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class Anggota extends Controller
{
    public function index()
    {
        $session = session();
        $user = $session->get('user');
        if (! $session->get('isLoggedIn') || ! $user) {
            return redirect()->to('/login');
        }
        $role = $user['role'] ?? null;
        if (! in_array($role, ['anggota', 'anggota_petugas'], true)) {
            return redirect()->to('/login');
        }
        $content = view('anggota/Dashboard');
        return view('anggota/layout', [
            'content' => $content,
            'title' => 'Dashboard',
        ]);
    }
}

