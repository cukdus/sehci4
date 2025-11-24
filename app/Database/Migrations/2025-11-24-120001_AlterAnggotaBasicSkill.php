<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AlterAnggotaBasicSkill extends Migration
{
    public function up()
    {
        $fields = [
            'basic_skill' => [
                'name' => 'basic_skill',
                'type' => 'TEXT',
                'null' => true,
            ],
        ];
        $this->forge->modifyColumn('anggota', $fields);
    }

    public function down()
    {
        $fields = [
            'basic_skill' => [
                'name' => 'basic_skill',
                'type' => 'ENUM',
                'constraint' => [
                    'Management Accounting',
                    'Digital Marketing',
                    'Leadership',
                    'Ms. Office Program',
                    'Design Graphic Program',
                    'Other',
                ],
                'null' => true,
            ],
        ];
        $this->forge->modifyColumn('anggota', $fields);
    }
}

