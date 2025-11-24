<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddFotoPetugasToUsers extends Migration
{
    public function up()
    {
        $fields = [
            'foto_petugas' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'default' => null,
            ],
        ];
        $this->forge->addColumn('users', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('users', 'foto_petugas');
    }
}
