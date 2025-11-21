<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePinjaman extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_pinjaman' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'id_anggota' => ['type' => 'INT', 'unsigned' => true],
            'jumlah_pinjaman' => ['type' => 'DECIMAL', 'constraint' => '15,2'],
            'bunga' => ['type' => 'DECIMAL', 'constraint' => '5,2'],
            'tanggal_pinjam' => ['type' => 'DATE', 'null' => true],
            'jangka_waktu' => ['type' => 'INT'],
            'status' => ['type' => 'ENUM', 'constraint' => ['aktif','lunas','menunggak'], 'default' => 'aktif'],
        ]);
        $this->forge->addKey('id_pinjaman', true);
        $this->forge->addKey('id_anggota');
        $this->forge->addForeignKey('id_anggota', 'anggota', 'id_anggota', 'CASCADE', 'CASCADE');
        $this->forge->createTable('pinjaman', true);
    }

    public function down()
    {
        $this->forge->dropTable('pinjaman', true);
    }
}
