<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id_user';
    protected $allowedFields = ['username', 'password_hash', 'role', 'id_anggota', 'id_petugas', 'status'];
    protected $returnType = 'array';

    public function getActiveByUsername(string $username): ?array
    {
        return $this
            ->where('username', $username)
            ->where('status', 'aktif')
            ->get()
            ->getRowArray();
    }
}
