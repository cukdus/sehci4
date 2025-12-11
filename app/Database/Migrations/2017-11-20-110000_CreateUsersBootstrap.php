<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateUsersBootstrap extends Migration
{
    public function up()
    {
        // Create minimal users table if missing, to satisfy Myth Auth FK to users.id
        $hasUsers = false;
        try {
            $tables = $this->db->listTables();
            $hasUsers = in_array('users', $tables, true);
        } catch (\Throwable $e) {
            $hasUsers = false;
        }

        if (! $hasUsers) {
            $this->forge->addField([
                'id_user' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
                'username' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => false],
                'password_hash' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
                'role' => ['type' => 'ENUM', 'constraint' => ['anggota','petugas','anggota_petugas','admin'], 'null' => false],
                'id_anggota' => ['type' => 'INT', 'unsigned' => true, 'null' => true],
                'id_petugas' => ['type' => 'INT', 'unsigned' => true, 'null' => true],
                'status' => ['type' => 'ENUM', 'constraint' => ['aktif','nonaktif'], 'default' => 'aktif'],
            ]);
            $this->forge->addKey('id_user', true);
            $this->forge->addUniqueKey('username');
            $this->forge->addKey('id_anggota');
            $this->forge->addKey('id_petugas');
            $this->forge->createTable('users', true);
        }
    }

    public function down()
    {
        // Do not drop users table here to avoid data loss; no-op
    }
}

