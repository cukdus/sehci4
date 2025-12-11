<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AlterUsersIdToIntForMythAuth extends Migration
{
    public function up()
    {
        $this->db->query('ALTER TABLE users MODIFY COLUMN id INT UNSIGNED NOT NULL');
        $this->db->query('UPDATE users SET id = id_user WHERE id IS NULL');
        $this->db->query('DROP TRIGGER IF EXISTS users_sync_id_after_insert');
        $this->db->query('CREATE TRIGGER users_sync_id_after_insert AFTER INSERT ON users FOR EACH ROW UPDATE users SET id = NEW.id_user WHERE id_user = NEW.id_user');
    }

    public function down()
    {
        $this->db->query('DROP TRIGGER IF EXISTS users_sync_id_after_insert');
        $this->db->query('ALTER TABLE users MODIFY COLUMN id BIGINT UNSIGNED NULL');
    }
}

