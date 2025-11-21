<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAngsuran extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_angsuran' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'id_pinjaman' => ['type' => 'INT', 'unsigned' => true],
            'tanggal_bayar' => ['type' => 'DATE', 'null' => true],
            'jumlah_bayar' => ['type' => 'DECIMAL', 'constraint' => '15,2'],
            'denda' => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => '0.00'],
        ]);
        $this->forge->addKey('id_angsuran', true);
        $this->forge->addKey('id_pinjaman');
        $this->forge->addForeignKey('id_pinjaman', 'pinjaman', 'id_pinjaman', 'CASCADE', 'CASCADE');
        $this->forge->createTable('angsuran', true);
    }

    public function down()
    {
        $this->forge->dropTable('angsuran', true);
    }
}
