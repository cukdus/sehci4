<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class SendWajibReminder extends BaseCommand
{
    protected $group = 'WAHA';
    protected $name = 'waha:send-wajib-reminders';
    protected $description = 'Mengirim pengingat simpanan wajib bulanan ke anggota aktif yang memiliki nomor anggota sesuai tanggal pengingat.';
    protected $usage = 'waha:send-wajib-reminders [--force]';

    protected $options = [
        '--force' => 'Tetap jalankan walau hari ini bukan tanggal atau sebelum jam pengingat.',
    ];

    public function run(array $params)
    {
        $force = CLI::getOption('force') !== null;
        $result = $this->execute($force, static function (string $message, string $color = 'yellow'): void {
            CLI::write($message, $color);
        });

        if (!($result['ok'] ?? false) && !empty($result['message'])) {
            CLI::write((string) $result['message'], 'red');
        }
    }

    public function execute(bool $force = false, ?callable $progress = null, string $mode = 'all', ?string $periodOverride = null): array
    {
        $db = \Config\Database::connect();

        $this->ensureSettingsTable($db);
        $this->ensureTemplateTable($db);
        $this->ensureReminderLogTable($db);

        $mode = strtolower(trim($mode));
        if (!in_array($mode, ['all', 'failed'], true)) {
            $mode = 'all';
        }
        $maxAttempts = 3;

        $settingRow = $db->table('settings')->where('key', 'wajib_reminder_day')->get()->getRowArray();
        $configuredDay = (int) ($settingRow['value'] ?? 10);
        $startTimeRow = $db->table('settings')->where('key', 'wajib_reminder_start_time')->get()->getRowArray();
        $configuredStartTime = trim((string) ($startTimeRow['value'] ?? '08:00'));
        if ($configuredDay < 1) {
            $configuredDay = 1;
        }
        if ($configuredDay > 31) {
            $configuredDay = 31;
        }
        if (!preg_match('/^(?:[01]\d|2[0-3]):[0-5]\d$/', $configuredStartTime)) {
            $configuredStartTime = '08:00';
        }

        $now = new \DateTimeImmutable();
        $today = $now->setTime(0, 0);

        $period = $today->format('Y-m');
        if (is_string($periodOverride) && preg_match('/^\d{4}-\d{2}$/', trim($periodOverride))) {
            $period = trim($periodOverride);
        }

        $periodBase = new \DateTimeImmutable($period . '-01');
        $periodBase = $periodBase->setTime(0, 0);
        $lastDayOfPeriodMonth = (int) $periodBase->format('t');
        $effectiveDay = min($configuredDay, $lastDayOfPeriodMonth);

        if ($mode === 'all' && !$force) {
            $lastDayOfCurrentMonth = (int) $today->format('t');
            $effectiveDayToday = min($configuredDay, $lastDayOfCurrentMonth);
            $currentDay = (int) $today->format('j');
            if ($currentDay !== $effectiveDayToday) {
                $message = 'Hari ini bukan tanggal pengingat. Dijadwalkan pada tanggal ' . $effectiveDayToday . ' tiap bulan.';
                $this->notifyProgress($progress, $message, 'yellow');
                return ['ok' => false, 'message' => $message, 'sent' => 0, 'failed' => 0, 'skipped' => 0];
            }
            [$startHour, $startMinute] = array_map('intval', explode(':', $configuredStartTime));
            $startAt = $today->setTime($startHour, $startMinute);
            if ($now < $startAt) {
                $message = 'Pengingat belum mulai. Jadwal kirim hari ini mulai pukul ' . $configuredStartTime . '.';
                $this->notifyProgress($progress, $message, 'yellow');
                return ['ok' => false, 'message' => $message, 'sent' => 0, 'failed' => 0, 'skipped' => 0];
            }
        }

        $feeRow = $db->table('settings')->where('key', 'fee_wajib')->get()->getRowArray();
        $feeWajib = (float) ($feeRow['value'] ?? 0);

        $templateRow = $db->table('waha_templates')->where('slug', 'wajib_reminder')->get()->getRowArray();
        $template = trim((string) ($templateRow['content'] ?? ''));
        if ($template === '') {
            $template = 'Halo {{nama}} ({{no_anggota}}), ini pengingat pembayaran simpanan wajib bulan {{bulan}} sebesar Rp {{jumlah_wajib}}. Mohon lakukan pembayaran sebelum {{tanggal_pengingat}}. Status keanggotaan: {{status}}.';
        }

        if ($mode === 'failed') {
            $failedRows = $db
                ->table('waha_reminder_logs')
                ->select('id_anggota')
                ->where('slug', 'wajib_reminder')
                ->where('period', $period)
                ->where('status', 'failed')
                ->where('attempts <', $maxAttempts)
                ->get()
                ->getResultArray();

            $failedIds = array_values(array_filter(array_map(static function ($r) {
                return (int) ($r['id_anggota'] ?? 0);
            }, $failedRows)));

            if ($failedIds === []) {
                $message = 'Tidak ada pesan gagal yang bisa diulang untuk periode ' . $period . '.';
                $this->notifyProgress($progress, $message, 'yellow');
                return ['ok' => false, 'message' => $message, 'sent' => 0, 'failed' => 0, 'skipped' => 0];
            }

            $anggotaRows = $db
                ->table('anggota')
                ->select('id_anggota, no_anggota, nama, no_telepon, status')
                ->where('status', 'aktif')
                ->where('no_anggota IS NOT NULL', null, false)
                ->where('TRIM(no_anggota) !=', '')
                ->whereIn('id_anggota', $failedIds)
                ->orderBy('nama', 'asc')
                ->get()
                ->getResultArray();
        } else {
            $anggotaRows = $db
                ->table('anggota')
                ->select('id_anggota, no_anggota, nama, no_telepon, status')
                ->where('status', 'aktif')
                ->where('no_anggota IS NOT NULL', null, false)
                ->where('TRIM(no_anggota) !=', '')
                ->orderBy('nama', 'asc')
                ->get()
                ->getResultArray();
        }

        if ($anggotaRows === []) {
            $message = 'Tidak ada anggota aktif yang memiliki nomor anggota untuk dikirimi pengingat.';
            $this->notifyProgress($progress, $message, 'yellow');
            return ['ok' => false, 'message' => $message, 'sent' => 0, 'failed' => 0, 'skipped' => 0];
        }

        $waha = service('waha');
        $sent = 0;
        $failed = 0;
        $skipped = 0;
        $attemptedMessages = 0;

        foreach ($anggotaRows as $anggota) {
            $existing = $db
                ->table('waha_reminder_logs')
                ->where('slug', 'wajib_reminder')
                ->where('period', $period)
                ->where('id_anggota', (int) $anggota['id_anggota'])
                ->get()
                ->getRowArray();

            if ($existing && ($existing['status'] ?? '') === 'sent') {
                $skipped++;
                continue;
            }
            if ($existing && ($existing['status'] ?? '') === 'failed' && (int) ($existing['attempts'] ?? 0) >= $maxAttempts) {
                $skipped++;
                continue;
            }

            $phone = $this->normalizePhone((string) ($anggota['no_telepon'] ?? ''));
            if ($phone === '') {
                $attempts = (int) ($existing['attempts'] ?? 0) + 1;
                $this->upsertLog($db, (int) $anggota['id_anggota'], $period, '', 'failed', 'Nomor telepon tidak tersedia atau tidak valid.', null, $attempts, date('Y-m-d H:i:s'));
                $failed++;
                continue;
            }

            $message = str_replace(
                ['{{nama}}', '{{no_anggota}}', '{{bulan}}', '{{jumlah_wajib}}', '{{tanggal_pengingat}}', '{{status}}'],
                [
                    (string) ($anggota['nama'] ?? ''),
                    (string) ($anggota['no_anggota'] ?? ''),
                    $this->formatIndoMonth($periodBase),
                    $this->formatCurrency($feeWajib),
                    $periodBase->setDate((int) $periodBase->format('Y'), (int) $periodBase->format('m'), $effectiveDay)->format('d-m-Y'),
                    (string) ($anggota['status'] ?? 'aktif'),
                ],
                $template
            );

            if ($attemptedMessages > 0) {
                $this->notifyProgress($progress, 'Menunggu 1 menit sebelum mengirim pesan berikutnya...', 'yellow');
                sleep(60);
            }
            $attemptedMessages++;

            $attempts = (int) ($existing['attempts'] ?? 0) + 1;
            $result = $waha->sendText($phone, $message);
            if (($result['ok'] ?? false) === true) {
                $this->upsertLog($db, (int) $anggota['id_anggota'], $period, $phone, 'sent', (string) ($result['body'] ?? ''), date('Y-m-d H:i:s'), $attempts, date('Y-m-d H:i:s'));
                $sent++;
                $this->notifyProgress($progress, 'Terkirim: ' . ($anggota['nama'] ?? '-') . ' (' . $phone . ')', 'green');
                continue;
            }

            $errorText = (string) ($result['error'] ?? $result['body'] ?? 'Gagal mengirim pesan');
            $this->upsertLog($db, (int) $anggota['id_anggota'], $period, $phone, 'failed', $errorText, null, $attempts, date('Y-m-d H:i:s'));
            log_message('error', 'WAHA wajib reminder failed for anggota ' . (int) $anggota['id_anggota'] . ': ' . $errorText);
            $failed++;
            $this->notifyProgress($progress, 'Gagal: ' . ($anggota['nama'] ?? '-') . ' (' . $phone . ') - ' . $errorText, 'red');
        }

        $summary = 'Selesai. Terkirim: ' . $sent . ', gagal: ' . $failed . ', dilewati: ' . $skipped . '.';
        $this->notifyProgress($progress, $summary, 'yellow');

        return [
            'ok' => true,
            'message' => $summary,
            'sent' => $sent,
            'failed' => $failed,
            'skipped' => $skipped,
            'total' => count($anggotaRows),
        ];
    }

    private function notifyProgress(?callable $progress, string $message, string $color = 'yellow'): void
    {
        if ($progress !== null) {
            $progress($message, $color);
        }
    }

    private function normalizePhone(string $phone): string
    {
        $digits = preg_replace('/\D+/', '', $phone) ?? '';
        if ($digits === '') {
            return '';
        }
        if (str_starts_with($digits, '0')) {
            return '62' . substr($digits, 1);
        }
        if (str_starts_with($digits, '62')) {
            return $digits;
        }

        return $digits;
    }

    private function formatCurrency(float $amount): string
    {
        return number_format($amount, 0, ',', '.');
    }

    private function formatIndoMonth(\DateTimeImmutable $date): string
    {
        $months = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember',
        ];

        return ($months[(int) $date->format('n')] ?? $date->format('F')) . ' ' . $date->format('Y');
    }

    private function upsertLog($db, int $idAnggota, string $period, string $phone, string $status, string $responseText, ?string $sentAt = null, ?int $attempts = null, ?string $lastAttemptAt = null): void
    {
        $payload = [
            'slug' => 'wajib_reminder',
            'period' => $period,
            'id_anggota' => $idAnggota,
            'phone' => $phone !== '' ? $phone : null,
            'status' => $status,
            'response_text' => $responseText,
            'sent_at' => $sentAt,
        ];
        if ($attempts !== null) {
            $payload['attempts'] = $attempts;
        }
        if ($lastAttemptAt !== null) {
            $payload['last_attempt_at'] = $lastAttemptAt;
        }

        $exists = $db
            ->table('waha_reminder_logs')
            ->where('slug', 'wajib_reminder')
            ->where('period', $period)
            ->where('id_anggota', $idAnggota)
            ->get()
            ->getRowArray();

        if ($exists) {
            $db
                ->table('waha_reminder_logs')
                ->where('id', $exists['id'])
                ->update($payload);
            return;
        }

        $db->table('waha_reminder_logs')->insert($payload);
    }

    private function ensureSettingsTable($db): void
    {
        if (in_array('settings', $db->listTables(), true)) {
            return;
        }

        $db->query('CREATE TABLE IF NOT EXISTS settings (
          id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
          `key` VARCHAR(100) NOT NULL,
          value TEXT DEFAULT NULL,
          updated_at TIMESTAMP NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
          PRIMARY KEY (id),
          UNIQUE KEY `key` (`key`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci');
    }

    private function ensureTemplateTable($db): void
    {
        if (in_array('waha_templates', $db->listTables(), true)) {
            return;
        }

        $db->query('CREATE TABLE IF NOT EXISTS waha_templates (
          id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
          slug VARCHAR(50) NOT NULL,
          content TEXT DEFAULT NULL,
          updated_at TIMESTAMP NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
          PRIMARY KEY (id),
          UNIQUE KEY slug (slug)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci');
    }

    private function ensureReminderLogTable($db): void
    {
        $tables = $db->listTables();
        if (!in_array('waha_reminder_logs', $tables, true)) {
            $db->query("CREATE TABLE IF NOT EXISTS waha_reminder_logs (
          id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
          slug VARCHAR(50) NOT NULL,
          period CHAR(7) NOT NULL,
          id_anggota INT(10) UNSIGNED NOT NULL,
          phone VARCHAR(30) DEFAULT NULL,
          status VARCHAR(20) NOT NULL DEFAULT 'pending',
          attempts INT(10) UNSIGNED NOT NULL DEFAULT 0,
          last_attempt_at DATETIME DEFAULT NULL,
          response_text TEXT DEFAULT NULL,
          sent_at DATETIME DEFAULT NULL,
          created_at TIMESTAMP NULL DEFAULT current_timestamp(),
          updated_at TIMESTAMP NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
          PRIMARY KEY (id),
          UNIQUE KEY uniq_slug_period_anggota (slug, period, id_anggota),
          KEY idx_slug_period_status (slug, period, status),
          KEY idx_id_anggota (id_anggota)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");
            return;
        }

        $fields = $db->getFieldNames('waha_reminder_logs');
        if (!in_array('attempts', $fields, true)) {
            $db->query('ALTER TABLE waha_reminder_logs ADD COLUMN attempts INT(10) UNSIGNED NOT NULL DEFAULT 0');
        }
        if (!in_array('last_attempt_at', $fields, true)) {
            $db->query('ALTER TABLE waha_reminder_logs ADD COLUMN last_attempt_at DATETIME DEFAULT NULL');
        }
    }
}
