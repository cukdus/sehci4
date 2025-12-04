<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AlterUsersPasswordNullable extends Migration
{
    public function up()
    {
        $fields = [
            'password_hash' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
        ];
        $this->forge->modifyColumn('users', $fields);
    }

    public function down()
    {
        $fields = [
            'password_hash' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => false,
            ],
        ];
        $this->forge->modifyColumn('users', $fields);
    }
}

