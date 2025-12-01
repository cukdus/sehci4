<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddTanggalTolakBerhenti extends Migration
{
    public function up()
    {
        $this->forge->addColumn('anggota', [
            'tanggal_tolak_berhenti' => [
                'type' => 'DATE',
                'null' => true,
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('anggota', 'tanggal_tolak_berhenti');
    }
}

