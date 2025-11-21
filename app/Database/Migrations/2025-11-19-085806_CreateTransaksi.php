<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTransaksi extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_transaksi' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'tanggal_transaksi' => ['type' => 'DATETIME', 'null' => true],
            'jenis_transaksi' => ['type' => 'ENUM', 'constraint' => ['simpanan', 'pinjaman', 'angsuran', 'penarikan']],
            'id_referensi' => ['type' => 'INT', 'unsigned' => true],
            'id_petugas' => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'keterangan' => ['type' => 'TEXT', 'null' => true],
        ]);
        $this->forge->addKey('id_transaksi', true);
        $this->forge->addKey('id_petugas');
        $this->forge->addForeignKey('id_petugas', 'petugas', 'id_petugas', 'SET NULL', 'CASCADE');
        $this->forge->createTable('transaksi', true);
    }

    public function down()
    {
        $this->forge->dropTable('transaksi', true);
    }
}
