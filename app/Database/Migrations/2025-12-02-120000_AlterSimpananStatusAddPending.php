<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AlterSimpananStatusAddPending extends Migration
{
    public function up()
    {
        // Add 'pending' to ENUM for status, keep default 'aktif'
        $this->db->query(
            "ALTER TABLE simpanan MODIFY COLUMN status ENUM('pending','aktif','dicairkan','jatuh_tempo') NOT NULL DEFAULT 'aktif'"
        );
    }

    public function down()
    {
        // Remove 'pending' from ENUM (revert to previous)
        $this->db->query(
            "ALTER TABLE simpanan MODIFY COLUMN status ENUM('aktif','dicairkan','jatuh_tempo') NOT NULL DEFAULT 'aktif'"
        );
    }
}

