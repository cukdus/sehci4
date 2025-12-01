<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddAlasanTolakBerhentiToAnggota extends Migration
{
    public function up()
    {
        $this->forge->addColumn('anggota', [
            'alasan_tolak_berhenti' => [
                'type' => 'TEXT',
                'null' => true,
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('anggota', 'alasan_tolak_berhenti');
    }
}

