<?php
namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RenameFotoPetugasToFoto extends Migration
{
    public function up()
    {
        $fields = [
            'foto_petugas' => [
                'name'       => 'foto',
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'default'    => null,
            ],
        ];
        $this->forge->modifyColumn('users', $fields);
    }

    public function down()
    {
        $fields = [
            'foto' => [
                'name'       => 'foto_petugas',
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'default'    => null,
            ],
        ];
        $this->forge->modifyColumn('users', $fields);
    }
}

