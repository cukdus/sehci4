<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddUsersIdBigintForMythAuth extends Migration
{
    public function up()
    {
        $hasId = false;
        try {
            $fields = $this->db->getFieldNames('users');
            $hasId = in_array('id', $fields, true);
        } catch (\Throwable $e) {
            $hasId = false;
        }

        if (!$hasId) {
            $this->forge->addColumn('users', [
                'id' => ['type' => 'BIGINT', 'unsigned' => true, 'null' => true],
            ]);
        }

        try {
            $this->db->query('UPDATE users SET id = id_user WHERE id IS NULL');
        } catch (\Throwable $e) {
        }

        try {
            $idx = $this->db->query("SHOW INDEX FROM users WHERE Key_name = 'users_id_unique'");
            $existsIdx = $idx && $idx->getRowArray();
            if (!$existsIdx) {
                $this->db->query('ALTER TABLE users ADD UNIQUE KEY users_id_unique (id)');
            }
        } catch (\Throwable $e) {
        }

        try {
            $this->db->query('DROP TRIGGER IF EXISTS users_sync_id_after_insert');
            $this->db->query('CREATE TRIGGER users_sync_id_after_insert AFTER INSERT ON users FOR EACH ROW UPDATE users SET id = NEW.id_user WHERE id_user = NEW.id_user');
        } catch (\Throwable $e) {
        }
    }

    public function down()
    {
        try {
            $this->db->query('DROP TRIGGER IF EXISTS users_sync_id_after_insert');
        } catch (\Throwable $e) {
        }
        try {
            $this->db->query('ALTER TABLE users DROP INDEX users_id_unique');
        } catch (\Throwable $e) {
        }
        try {
            $this->forge->dropColumn('users', 'id');
        } catch (\Throwable $e) {
        }
    }
}
