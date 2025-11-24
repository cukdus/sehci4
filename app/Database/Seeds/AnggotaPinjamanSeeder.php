<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class AnggotaPinjamanSeeder extends Seeder
{
    public function run()
    {
        $db = $this->db;
        $db->transStart();
        for ($i = 1; $i <= 100; $i++) {
            $noAnggota = 'AX' . str_pad((string) $i, 4, '0', STR_PAD_LEFT);
            $nama = 'Anggota ' . $i;
            $jk = $i % 2 === 0 ? 'Laki-laki' : 'Perempuan';
            $email = 'anggota' . $i . '@example.test';
            $db->table('anggota')->insert([
                'no_anggota' => $noAnggota,
                'nama' => $nama,
                'jenis_kelamin' => $jk,
                'alamat' => 'Alamat ' . $i,
                'no_telepon' => '08123' . str_pad((string) $i, 7, '0', STR_PAD_LEFT),
                'email' => $email,
                'tanggal_gabung' => date('Y-m-d'),
                'status' => 'aktif',
                'jenis_anggota' => 'aktif',
            ]);
            $idAnggota = $db->insertID();

            $permohonan = $i % 2 === 0;
            $tanggalPinjam = $permohonan ? null : date('Y-m-d', strtotime('-' . rand(0, 90) . ' days'));
            $jumlah = rand(1, 20) * 500000;
            $bunga = rand(10, 30) / 10;
            $jangka = [6, 12, 18, 24][rand(0, 3)];
            $status = $permohonan ? 'aktif' : (rand(0, 1) ? 'aktif' : 'lunas');
            $jaminan = 'Jaminan ' . $i;
            $keterangan = $permohonan ? 'Permohonan baru' : 'Disetujui';

            $db->table('pinjaman')->insert([
                'id_anggota' => $idAnggota,
                'jumlah_pinjaman' => $jumlah,
                'bunga' => $bunga,
                'tanggal_pinjam' => $tanggalPinjam,
                'jangka_waktu' => $jangka,
                'status' => $status,
                'jaminan' => $jaminan,
                'keterangan' => $keterangan,
            ]);
        }
        $db->transComplete();
    }
}

