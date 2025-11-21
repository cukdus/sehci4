<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateUsers extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_user' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'username' => ['type' => 'VARCHAR', 'constraint' => 50],
            'password_hash' => ['type' => 'VARCHAR', 'constraint' => 255],
            'role' => ['type' => 'ENUM', 'constraint' => ['anggota','petugas','anggota_petugas','admin']],
            'id_anggota' => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'id_petugas' => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'status' => ['type' => 'ENUM', 'constraint' => ['aktif','nonaktif'], 'default' => 'aktif'],
        ]);
        $this->forge->addKey('id_user', true);
        $this->forge->addUniqueKey('username');
        $this->forge->addForeignKey('id_anggota', 'anggota', 'id_anggota', 'SET NULL', 'CASCADE');
        $this->forge->addForeignKey('id_petugas', 'petugas', 'id_petugas', 'SET NULL', 'CASCADE');
        $this->forge->createTable('users', true);
    }

    public function down()
    {
        $this->forge->dropTable('users', true);
    }
}