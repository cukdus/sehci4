<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAnggota extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_anggota' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'no_anggota' => ['type' => 'VARCHAR', 'constraint' => 20],
            'nama' => ['type' => 'VARCHAR', 'constraint' => 100],
            'jenis_kelamin' => ['type' => 'ENUM', 'constraint' => ['Laki-laki', 'Perempuan'], 'null' => true],
            'tempat_lahir' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'tanggal_lahir' => ['type' => 'DATE', 'null' => true],
            'alamat' => ['type' => 'TEXT', 'null' => true],
            'no_telepon' => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
            'no_ktp' => ['type' => 'VARCHAR', 'constraint' => 30, 'null' => true],
            'no_kk' => ['type' => 'VARCHAR', 'constraint' => 30, 'null' => true],
            'no_npwp' => ['type' => 'VARCHAR', 'constraint' => 30, 'null' => true],
            'foto' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'tanggal_gabung' => ['type' => 'DATE', 'null' => true],
            'tanggal_berhenti' => ['type' => 'DATE', 'null' => true],
            'alasan_berhenti' => ['type' => 'TEXT', 'null' => true],
            'status' => ['type' => 'ENUM', 'constraint' => ['aktif', 'nonaktif'], 'default' => 'aktif'],
            'jenis_anggota' => ['type' => 'ENUM', 'constraint' => ['aktif', 'pasif'], 'default' => 'aktif'],
            'basic_skill' => ['type' => 'ENUM', 'constraint' => ['Management Accounting', 'Digital Marketing', 'Leadership', 'Ms. Office Program', 'Design Graphic Program', 'Other'], 'null' => true],
            'pengalaman_kerja' => ['type' => 'TEXT', 'null' => true],
            'pengalaman_organisasi' => ['type' => 'TEXT', 'null' => true],
        ]);
        $this->forge->addKey('id_anggota', true);
        $this->forge->addUniqueKey('no_anggota');
        $this->forge->addUniqueKey('no_ktp');
        $this->forge->createTable('anggota', true);
    }

    public function down()
    {
        $this->forge->dropTable('anggota', true);
    }
}
