<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class SimpananSeeder extends Seeder
{
    public function run()
    {
        $db = $this->db;
        $anggotaRows = $db->table('anggota')->select('id_anggota, tanggal_gabung')->get()->getResultArray();
        foreach ($anggotaRows as $a) {
            $id = (int) $a['id_anggota'];
            if ($id <= 0) {
                continue;
            }
            $existing = (int) $db->table('simpanan')->where('id_anggota', $id)->countAllResults();
            $toAdd = max(0, 10 - $existing);
            if ($toAdd <= 0) {
                continue;
            }
            for ($i = 1; $i <= $toAdd; $i++) {
                $jenisIndex = $i % 3;
                $jenis = $jenisIndex === 1 ? 'pokok' : ($jenisIndex === 2 ? 'wajib' : 'sukarela');
                $data = [
                    'id_anggota' => $id,
                    'jenis_simpanan' => $jenis,
                    'jumlah' => 0.00,
                    'tanggal_simpan' => date('Y-m-d', strtotime("-{$i} month")),
                    'status' => 'aktif',
                ];
                if ($jenis === 'pokok') {
                    $data['jumlah'] = 1000000.00;
                } elseif ($jenis === 'wajib') {
                    $data['jumlah'] = 100000.00;
                } else { // sukarela
                    $tipe = ($i % 2 === 0) ? 'berjangka' : 'biasa';
                    $data['tipe_sukarela'] = $tipe;
                    if ($tipe === 'berjangka') {
                        $data['jangka_waktu'] = rand(6, 24);
                    }
                    $steps = [250000, 300000, 350000, 400000, 500000, 750000, 1000000, 1500000, 2000000, 3000000];
                    $data['jumlah'] = (float) $steps[array_rand($steps)];
                }
                $db->table('simpanan')->insert($data);
            }
        }
    }
}

