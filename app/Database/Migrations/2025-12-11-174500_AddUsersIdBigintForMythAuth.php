<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddUsersIdBigintForMythAuth extends Migration
{
    public function up()
    {
        $this->forge->addColumn('users', [
            'id' => ['type' => 'BIGINT', 'unsigned' => true, 'null' => true],
        ]);
        $this->db->query('UPDATE users SET id = id_user WHERE id IS NULL');
        $this->db->query('ALTER TABLE users ADD UNIQUE KEY id (id)');
        $this->db->query('DROP TRIGGER IF EXISTS users_sync_id_after_insert');
        $this->db->query('CREATE TRIGGER users_sync_id_after_insert AFTER INSERT ON users FOR EACH ROW UPDATE users SET id = NEW.id_user WHERE id_user = NEW.id_user');
    }

    public function down()
    {
        $this->db->query('DROP TRIGGER IF EXISTS users_sync_id_after_insert');
        $this->db->query('ALTER TABLE users DROP INDEX id');
        $this->forge->dropColumn('users', 'id');
    }
}

