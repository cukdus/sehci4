<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\UserModel;

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

        if (! $this->validate($rules)) {
            $session->setFlashdata('error', 'Username dan password wajib diisi');
            return redirect()->back()->withInput();
        }

        $username = trim((string) $request->getPost('username'));
        $password = (string) $request->getPost('password');

        $userModel = new UserModel();
        $user = $userModel->getActiveByUsername($username);

        if (! $user) {
            $session->setFlashdata('error', 'Akun tidak ditemukan atau tidak aktif');
            return redirect()->back()->withInput();
        }

        if (! password_verify($password, $user['password_hash'])) {
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
}

