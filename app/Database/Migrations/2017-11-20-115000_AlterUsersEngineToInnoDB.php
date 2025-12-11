<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AlterUsersEngineToInnoDB extends Migration
{
    public function up()
    {
        try {
            $tables = $this->db->listTables();
            if (in_array('users', $tables, true)) {
                $this->db->query('ALTER TABLE users ENGINE=InnoDB');
            }
        } catch (\Throwable $e) {
        }
    }

    public function down()
    {
        try {
            $tables = $this->db->listTables();
            if (in_array('users', $tables, true)) {
                $this->db->query('ALTER TABLE users ENGINE=MyISAM');
            }
        } catch (\Throwable $e) {
        }
    }
}

