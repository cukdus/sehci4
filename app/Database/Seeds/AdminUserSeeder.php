<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();
        $builder = $db->table('users');

        $username = 'admin';
        $password = 'admin123';
        $hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);

        $existing = $builder->where('username', $username)->get()->getRow();

        if ($existing) {
            return;
        }

        $builder->insert([
            'username'      => $username,
            'password_hash' => $hash,
            'role'          => 'admin',
            'id_anggota'    => null,
            'id_petugas'    => null,
            'status'        => 'aktif',
            'foto'          => null,
        ]);
    }
}

