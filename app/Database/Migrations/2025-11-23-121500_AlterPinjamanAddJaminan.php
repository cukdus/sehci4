<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AlterPinjamanAddJaminan extends Migration
{
    public function up()
    {
        $fields = [
            'jaminan' => ['type' => 'TEXT', 'null' => true],
        ];
        $this->forge->addColumn('pinjaman', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('pinjaman', 'jaminan');
    }
}

