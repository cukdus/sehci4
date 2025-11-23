<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AlterPinjamanAddKeterangan extends Migration
{
    public function up()
    {
        $fields = [
            'keterangan' => ['type' => 'TEXT', 'null' => true],
        ];
        $this->forge->addColumn('pinjaman', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('pinjaman', 'keterangan');
    }
}

