<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateWahaBroadcastLogs extends Migration
{
    public function up()
    {
        $this->db->query("CREATE TABLE IF NOT EXISTS waha_broadcasts (
          id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
          title VARCHAR(255) DEFAULT NULL,
          message TEXT DEFAULT NULL,
          created_by VARCHAR(100) DEFAULT NULL,
          total_target INT(10) UNSIGNED NOT NULL DEFAULT 0,
          sent_count INT(10) UNSIGNED NOT NULL DEFAULT 0,
          failed_count INT(10) UNSIGNED NOT NULL DEFAULT 0,
          skipped_count INT(10) UNSIGNED NOT NULL DEFAULT 0,
          status VARCHAR(20) NOT NULL DEFAULT 'processing',
          created_at TIMESTAMP NULL DEFAULT current_timestamp(),
          updated_at TIMESTAMP NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
          PRIMARY KEY (id),
          KEY idx_status_created_at (status, created_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");

        $this->db->query("CREATE TABLE IF NOT EXISTS waha_broadcast_logs (
          id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
          broadcast_id INT(10) UNSIGNED NOT NULL,
          id_anggota INT(10) UNSIGNED DEFAULT NULL,
          nama VARCHAR(255) DEFAULT NULL,
          no_anggota VARCHAR(20) DEFAULT NULL,
          phone VARCHAR(30) DEFAULT NULL,
          status VARCHAR(20) NOT NULL DEFAULT 'pending',
          response_text TEXT DEFAULT NULL,
          sent_at DATETIME DEFAULT NULL,
          created_at TIMESTAMP NULL DEFAULT current_timestamp(),
          PRIMARY KEY (id),
          KEY idx_broadcast_id_status (broadcast_id, status),
          KEY idx_anggota (id_anggota)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");
    }

    public function down()
    {
        $this->db->query('DROP TABLE IF EXISTS waha_broadcast_logs');
        $this->db->query('DROP TABLE IF EXISTS waha_broadcasts');
    }
}

