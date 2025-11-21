<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSimpanan extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_simpanan' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'id_anggota' => ['type' => 'INT', 'unsigned' => true],
            'jenis_simpanan' => ['type' => 'ENUM', 'constraint' => ['pokok', 'wajib', 'sukarela']],
            'tipe_sukarela' => ['type' => 'ENUM', 'constraint' => ['biasa', 'berjangka'], 'null' => true],
            'jumlah' => ['type' => 'DECIMAL', 'constraint' => '15,2'],
            'tanggal_simpan' => ['type' => 'DATE', 'null' => true],
            'jangka_waktu' => ['type' => 'INT', 'null' => true],
            'status' => ['type' => 'ENUM', 'constraint' => ['aktif', 'dicairkan', 'jatuh_tempo'], 'default' => 'aktif'],
        ]);
        $this->forge->addKey('id_simpanan', true);
        $this->forge->addKey('id_anggota');
        $this->forge->addForeignKey('id_anggota', 'anggota', 'id_anggota', 'CASCADE', 'CASCADE');
        $this->forge->createTable('simpanan', true);

        if (!$this->db->fieldExists('tanggal_jatuh_tempo', 'simpanan')) {
            $this->db->query(
                'ALTER TABLE simpanan ADD COLUMN tanggal_jatuh_tempo DATE GENERATED ALWAYS AS (CASE WHEN jangka_waktu IS NOT NULL THEN DATE_ADD(tanggal_simpan, INTERVAL jangka_waktu MONTH) ELSE NULL END) STORED'
            );
        }
    }

    public function down()
    {
        $this->db->query('ALTER TABLE simpanan DROP COLUMN tanggal_jatuh_tempo');
        $this->forge->dropTable('simpanan', true);
    }
}
