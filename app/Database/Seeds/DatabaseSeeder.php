<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $db = $this->db;
        $db->transStart();

        $db->table('anggota')->insert([
            'no_anggota' => 'A0001',
            'nama' => 'Budi Santoso',
            'jenis_kelamin' => 'Laki-laki',
            'tempat_lahir' => 'Jakarta',
            'tanggal_lahir' => date('Y-m-d', strtotime('1990-05-10')),
            'alamat' => 'Jl. Merdeka No. 1, Jakarta',
            'no_telepon' => '081234567890',
            'no_ktp' => '3171019000000001',
            'no_kk' => '3171019000000002',
            'no_npwp' => '12.345.678.9-012.345',
            'email' => 'budi@example.com',
            'tanggal_gabung' => date('Y-m-d'),
            'status' => 'aktif',
            'jenis_anggota' => 'aktif',
            'basic_skill' => 'Leadership',
            'pengalaman_kerja' => '5 tahun di bidang keuangan',
            'pengalaman_organisasi' => 'Ketua RT 2020-2022',
        ]);
        $idAnggota1 = $db->insertID();

        $db->table('anggota')->insert([
            'no_anggota' => 'A0002',
            'nama' => 'Siti Aminah',
            'jenis_kelamin' => 'Perempuan',
            'tempat_lahir' => 'Bandung',
            'tanggal_lahir' => date('Y-m-d', strtotime('1992-08-15')),
            'alamat' => 'Jl. Asia Afrika No. 10, Bandung',
            'no_telepon' => '081298765432',
            'no_ktp' => '3273019200000001',
            'no_kk' => '3273019200000002',
            'no_npwp' => '98.765.432.1-098.765',
            'email' => 'siti@example.com',
            'tanggal_gabung' => date('Y-m-d'),
            'status' => 'aktif',
            'jenis_anggota' => 'aktif',
            'basic_skill' => 'Digital Marketing',
            'pengalaman_kerja' => '3 tahun di bidang pemasaran',
            'pengalaman_organisasi' => 'Anggota Karang Taruna',
        ]);
        $idAnggota2 = $db->insertID();

        $db->table('petugas')->insert([
            'nama_petugas' => 'Admin Koperasi',
            'level' => 'admin',
        ]);
        $idPetugasAdmin = $db->insertID();

        $db->table('petugas')->insert([
            'nama_petugas' => 'Kasir Utama',
            'level' => 'kasir',
        ]);
        $idPetugasKasir = $db->insertID();

        $db->table('users')->insert([
            'username' => 'admin',
            'password_hash' => password_hash('admin123', PASSWORD_DEFAULT),
            'role' => 'admin',
            'id_petugas' => $idPetugasAdmin,
            'status' => 'aktif',
        ]);

        $db->table('users')->insert([
            'username' => 'kasir',
            'password_hash' => password_hash('kasir123', PASSWORD_DEFAULT),
            'role' => 'petugas',
            'id_petugas' => $idPetugasKasir,
            'status' => 'aktif',
        ]);

        $db->table('users')->insert([
            'username' => 'budi',
            'password_hash' => password_hash('budi123', PASSWORD_DEFAULT),
            'role' => 'anggota',
            'id_anggota' => $idAnggota1,
            'status' => 'aktif',
        ]);

        $db->table('users')->insert([
            'username' => 'siti',
            'password_hash' => password_hash('siti123', PASSWORD_DEFAULT),
            'role' => 'anggota',
            'id_anggota' => $idAnggota2,
            'status' => 'aktif',
        ]);

        $db->table('simpanan')->insert([
            'id_anggota' => $idAnggota1,
            'jenis_simpanan' => 'pokok',
            'jumlah' => 1000000.00,
            'tanggal_simpan' => date('Y-m-d'),
            'status' => 'aktif',
        ]);
        $idSimpanan1 = $db->insertID();

        $db->table('simpanan')->insert([
            'id_anggota' => $idAnggota2,
            'jenis_simpanan' => 'sukarela',
            'tipe_sukarela' => 'berjangka',
            'jumlah' => 5000000.00,
            'jangka_waktu' => 6,
            'tanggal_simpan' => date('Y-m-d'),
            'status' => 'aktif',
        ]);
        $idSimpanan2 = $db->insertID();

        $db->table('pinjaman')->insert([
            'id_anggota' => $idAnggota1,
            'jumlah_pinjaman' => 10000000.00,
            'bunga' => 1.5,
            'tanggal_pinjam' => date('Y-m-d'),
            'jangka_waktu' => 12,
            'status' => 'aktif',
        ]);
        $idPinjaman1 = $db->insertID();

        $db->table('angsuran')->insert([
            'id_pinjaman' => $idPinjaman1,
            'tanggal_bayar' => date('Y-m-d'),
            'jumlah_bayar' => 1000000.00,
            'denda' => 0.00,
        ]);
        $idAngsuran1 = $db->insertID();

        $db->table('transaksi')->insert([
            'jenis_transaksi' => 'simpanan',
            'id_referensi' => $idSimpanan1,
            'id_petugas' => $idPetugasKasir,
            'keterangan' => 'Setoran simpanan pokok',
        ]);

        $db->table('transaksi')->insert([
            'jenis_transaksi' => 'pinjaman',
            'id_referensi' => $idPinjaman1,
            'id_petugas' => $idPetugasKasir,
            'keterangan' => 'Pencairan pinjaman',
        ]);

        $db->table('transaksi')->insert([
            'jenis_transaksi' => 'angsuran',
            'id_referensi' => $idAngsuran1,
            'id_petugas' => $idPetugasKasir,
            'keterangan' => 'Pembayaran angsuran bulan pertama',
        ]);

        $db->transComplete();

        $this->call('AnggotaPinjamanSeeder');
    }
}
