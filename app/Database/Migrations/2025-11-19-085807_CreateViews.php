<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateViews extends Migration
{
    public function up()
    {
        $this->db->query(
            "CREATE OR REPLACE VIEW v_total_simpanan_per_anggota AS
            SELECT a.id_anggota, a.nama, SUM(s.jumlah) AS total_simpanan
            FROM anggota a
            LEFT JOIN simpanan s ON a.id_anggota = s.id_anggota
            GROUP BY a.id_anggota"
        );

        $this->db->query(
            "CREATE OR REPLACE VIEW v_sisa_pinjaman AS
            SELECT p.id_pinjaman, a.nama, p.jumlah_pinjaman,
                   COALESCE(SUM(ang.jumlah_bayar), 0) AS total_dibayar,
                   (p.jumlah_pinjaman - COALESCE(SUM(ang.jumlah_bayar), 0)) AS sisa_pinjaman
            FROM pinjaman p
            JOIN anggota a ON a.id_anggota = p.id_anggota
            LEFT JOIN angsuran ang ON ang.id_pinjaman = p.id_pinjaman
            GROUP BY p.id_pinjaman"
        );
    }

    public function down()
    {
        $this->db->query("DROP VIEW IF EXISTS v_sisa_pinjaman");
        $this->db->query("DROP VIEW IF EXISTS v_total_simpanan_per_anggota");
    }
}