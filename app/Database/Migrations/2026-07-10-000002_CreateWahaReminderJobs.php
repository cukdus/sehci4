<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateWahaReminderJobs extends Migration
{
    public function up()
    {
        $this->db->query("CREATE TABLE IF NOT EXISTS waha_reminder_jobs (
          id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
          slug VARCHAR(50) NOT NULL,
          period CHAR(7) NOT NULL,
          mode VARCHAR(20) NOT NULL DEFAULT 'all',
          is_force TINYINT(1) NOT NULL DEFAULT 0,
          status VARCHAR(20) NOT NULL DEFAULT 'pending',
          created_by VARCHAR(100) DEFAULT NULL,
          message TEXT DEFAULT NULL,
          total_target INT(10) UNSIGNED NOT NULL DEFAULT 0,
          sent_count INT(10) UNSIGNED NOT NULL DEFAULT 0,
          failed_count INT(10) UNSIGNED NOT NULL DEFAULT 0,
          skipped_count INT(10) UNSIGNED NOT NULL DEFAULT 0,
          started_at DATETIME DEFAULT NULL,
          finished_at DATETIME DEFAULT NULL,
          created_at TIMESTAMP NULL DEFAULT current_timestamp(),
          updated_at TIMESTAMP NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
          PRIMARY KEY (id),
          KEY idx_slug_period_status (slug, period, status),
          KEY idx_status_created_at (status, created_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");

        if ($this->db->tableExists('waha_reminder_logs')) {
            if (!$this->db->fieldExists('attempts', 'waha_reminder_logs')) {
                $this->db->query('ALTER TABLE waha_reminder_logs ADD COLUMN attempts INT(10) UNSIGNED NOT NULL DEFAULT 0');
            }
            if (!$this->db->fieldExists('last_attempt_at', 'waha_reminder_logs')) {
                $this->db->query('ALTER TABLE waha_reminder_logs ADD COLUMN last_attempt_at DATETIME DEFAULT NULL');
            }
        }
    }

    public function down()
    {
        if ($this->db->tableExists('waha_reminder_logs')) {
            if ($this->db->fieldExists('attempts', 'waha_reminder_logs')) {
                $this->db->query('ALTER TABLE waha_reminder_logs DROP COLUMN attempts');
            }
            if ($this->db->fieldExists('last_attempt_at', 'waha_reminder_logs')) {
                $this->db->query('ALTER TABLE waha_reminder_logs DROP COLUMN last_attempt_at');
            }
        }

        $this->db->query('DROP TABLE IF EXISTS waha_reminder_jobs');
    }
}
