<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AlterAnggotaNoAnggotaNullable extends Migration
{
    public function up()
    {
        $fields = [
            'no_anggota' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => true,
            ],
        ];
        $this->forge->modifyColumn('anggota', $fields);
    }

    public function down()
    {
        $fields = [
            'no_anggota' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => false,
            ],
        ];
        $this->forge->modifyColumn('anggota', $fields);
    }
}

