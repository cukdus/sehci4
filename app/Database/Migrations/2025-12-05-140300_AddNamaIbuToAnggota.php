<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddNamaIbuToAnggota extends Migration
{
    public function up()
    {
        $this->forge->addColumn('anggota', [
            'nama_ibu' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('anggota', 'nama_ibu');
    }
}

