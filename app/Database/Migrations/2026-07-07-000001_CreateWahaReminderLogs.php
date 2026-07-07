<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateWahaReminderLogs extends Migration
{
    public function up()
    {
        $this->db->query("CREATE TABLE IF NOT EXISTS waha_reminder_logs (
          id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
          slug VARCHAR(50) NOT NULL,
          period CHAR(7) NOT NULL,
          id_anggota INT(10) UNSIGNED NOT NULL,
          phone VARCHAR(30) DEFAULT NULL,
          status VARCHAR(20) NOT NULL DEFAULT 'pending',
          response_text TEXT DEFAULT NULL,
          sent_at DATETIME DEFAULT NULL,
          created_at TIMESTAMP NULL DEFAULT current_timestamp(),
          updated_at TIMESTAMP NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
          PRIMARY KEY (id),
          UNIQUE KEY uniq_slug_period_anggota (slug, period, id_anggota),
          KEY idx_slug_period_status (slug, period, status),
          KEY idx_id_anggota (id_anggota)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");
    }

    public function down()
    {
        $this->db->query('DROP TABLE IF EXISTS waha_reminder_logs');
    }
}

