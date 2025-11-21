<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AlterAnggotaAddEmail extends Migration
{
    public function up()
    {
        $this->forge->addColumn('anggota', [
            'email' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
        ]);
        $this->db->query('ALTER TABLE anggota ADD UNIQUE KEY email (email)');
    }

    public function down()
    {
        $this->db->query('ALTER TABLE anggota DROP INDEX email');
        $this->forge->dropColumn('anggota', 'email');
    }
}