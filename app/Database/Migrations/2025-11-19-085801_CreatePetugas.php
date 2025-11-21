<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePetugas extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_petugas' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'nama_petugas' => ['type' => 'VARCHAR', 'constraint' => 100],
            'level' => ['type' => 'ENUM', 'constraint' => ['admin','kasir','pimpinan'], 'default' => 'kasir'],
        ]);
        $this->forge->addKey('id_petugas', true);
        $this->forge->createTable('petugas', true);
    }

    public function down()
    {
        $this->forge->dropTable('petugas', true);
    }
}