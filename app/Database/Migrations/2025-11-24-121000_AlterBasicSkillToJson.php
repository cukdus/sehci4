<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AlterBasicSkillToJson extends Migration
{
    public function up()
    {
        try {
            $this->forge->modifyColumn('anggota', [
                'basic_skill' => [
                    'name' => 'basic_skill',
                    'type' => 'JSON',
                    'null' => true,
                ],
            ]);
            $db = \Config\Database::connect();
            $builder = $db->table('anggota');
            $rows = $builder->select('id_anggota, basic_skill')->get()->getResultArray();
            foreach ($rows as $row) {
                $id = (int) $row['id_anggota'];
                $val = (string) ($row['basic_skill'] ?? '');
                if ($val === '') {
                    continue;
                }
                $trim = trim($val);
                if (strlen($trim) > 0 && $trim[0] === '[') {
                    continue;
                }
                $parts = array_map('trim', explode(',', $trim));
                $parts = array_values(array_filter($parts, fn($v) => $v !== ''));
                $json = json_encode($parts, JSON_UNESCAPED_UNICODE);
                $builder->where('id_anggota', $id)->update(['basic_skill' => $json]);
            }
        } catch (\Throwable $e) {
            return;
        }
    }

    public function down()
    {
        $this->forge->modifyColumn('anggota', [
            'basic_skill' => [
                'name' => 'basic_skill',
                'type' => 'TEXT',
                'null' => true,
            ],
        ]);

        $db = \Config\Database::connect();
        $builder = $db->table('anggota');
        $rows = $builder->select('id_anggota, basic_skill')->get()->getResultArray();
        foreach ($rows as $row) {
            $id = (int) $row['id_anggota'];
            $val = (string) ($row['basic_skill'] ?? '');
            if ($val === '') {
                continue;
            }
            $decoded = json_decode($val, true);
            if (is_array($decoded)) {
                $text = implode(', ', array_map('trim', $decoded));
                $builder->where('id_anggota', $id)->update(['basic_skill' => $text]);
            }
        }
    }
}
