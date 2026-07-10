<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class ProcessWahaReminderJobs extends BaseCommand
{
    protected $group = 'WAHA';
    protected $name = 'waha:process-reminder-jobs';
    protected $description = 'Memproses antrian job pengingat WAHA (waha_reminder_jobs). Cocok dijalankan via cron/scheduler setiap 1 menit.';
    protected $usage = 'waha:process-reminder-jobs [--max-jobs=1]';

    protected $options = [
        '--max-jobs' => 'Jumlah maksimal job yang diproses dalam sekali run (default: 1).',
    ];

    public function run(array $params)
    {
        $maxJobs = (int) (CLI::getOption('max-jobs') ?? 1);
        if ($maxJobs < 1) {
            $maxJobs = 1;
        }

        $lockPath = rtrim(WRITEPATH, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'locks';
        if (!is_dir($lockPath)) {
            @mkdir($lockPath, 0777, true);
        }
        $lockFile = $lockPath . DIRECTORY_SEPARATOR . 'waha_reminder_jobs.lock';

        $fp = @fopen($lockFile, 'c+');
        if (!$fp) {
            CLI::write('Tidak bisa membuat lock file: ' . $lockFile, 'red');
            return;
        }
        if (!flock($fp, LOCK_EX | LOCK_NB)) {
            CLI::write('Worker sudah berjalan (lock aktif).', 'yellow');
            fclose($fp);
            return;
        }

        try {
            $processed = $this->processJobs($maxJobs);
            CLI::write('Selesai. Job diproses: ' . $processed, 'green');
        } catch (\Throwable $e) {
            CLI::write('Worker error: ' . $e->getMessage(), 'red');
        } finally {
            flock($fp, LOCK_UN);
            fclose($fp);
        }
    }

    private function processJobs(int $maxJobs): int
    {
        $db = \Config\Database::connect();
        $this->ensureReminderJobsTable($db);

        $processed = 0;
        while ($processed < $maxJobs) {
            $job = $db
                ->table('waha_reminder_jobs')
                ->where('status', 'pending')
                ->orderBy('id', 'asc')
                ->get(1)
                ->getRowArray();

            if (!$job) {
                break;
            }

            $jobId = (int) ($job['id'] ?? 0);
            if ($jobId <= 0) {
                break;
            }

            $affected = $db
                ->table('waha_reminder_jobs')
                ->where('id', $jobId)
                ->where('status', 'pending')
                ->update([
                    'status' => 'processing',
                    'started_at' => date('Y-m-d H:i:s'),
                ]);

            if ($affected === false || $db->affectedRows() < 1) {
                continue;
            }

            $force = (bool) ((int) ($job['is_force'] ?? 0));
            $mode = (string) ($job['mode'] ?? 'all');
            $period = (string) ($job['period'] ?? '');

            try {
                $command = new \App\Commands\SendWajibReminder();
                $result = $command->execute($force, static function (string $message, string $color = 'yellow'): void {
                    CLI::write($message, $color);
                }, $mode, $period !== '' ? $period : null);

                $payload = [
                    'status' => 'finished',
                    'message' => (string) ($result['message'] ?? ''),
                    'total_target' => (int) ($result['total'] ?? 0),
                    'sent_count' => (int) ($result['sent'] ?? 0),
                    'failed_count' => (int) ($result['failed'] ?? 0),
                    'skipped_count' => (int) ($result['skipped'] ?? 0),
                    'finished_at' => date('Y-m-d H:i:s'),
                ];

                $db->table('waha_reminder_jobs')->where('id', $jobId)->update($payload);
            } catch (\Throwable $e) {
                $db->table('waha_reminder_jobs')->where('id', $jobId)->update([
                    'status' => 'failed',
                    'message' => $e->getMessage(),
                    'finished_at' => date('Y-m-d H:i:s'),
                ]);
                throw $e;
            }

            $processed++;
        }

        return $processed;
    }

    private function ensureReminderJobsTable($db): void
    {
        if (in_array('waha_reminder_jobs', $db->listTables(), true)) {
            return;
        }

        $db->query("CREATE TABLE IF NOT EXISTS waha_reminder_jobs (
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
    }
}
