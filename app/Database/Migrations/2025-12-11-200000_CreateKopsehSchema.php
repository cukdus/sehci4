<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateKopsehSchema extends Migration
{
    public function up()
    {
        $this->db->query("CREATE TABLE IF NOT EXISTS anggota (
            id_anggota INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
            no_anggota VARCHAR(20) DEFAULT NULL,
            nama VARCHAR(100) NOT NULL,
            jenis_kelamin ENUM('Laki-laki','Perempuan') DEFAULT NULL,
            tempat_lahir VARCHAR(100) DEFAULT NULL,
            tanggal_lahir DATE DEFAULT NULL,
            alamat TEXT DEFAULT NULL,
            no_telepon VARCHAR(20) DEFAULT NULL,
            no_ktp VARCHAR(30) DEFAULT NULL,
            no_kk VARCHAR(30) DEFAULT NULL,
            no_npwp VARCHAR(30) DEFAULT NULL,
            foto VARCHAR(255) DEFAULT NULL,
            tanggal_gabung DATE DEFAULT NULL,
            tanggal_berhenti DATE DEFAULT NULL,
            alasan_berhenti TEXT DEFAULT NULL,
            status ENUM('aktif','nonaktif') NOT NULL DEFAULT 'aktif',
            jenis_anggota ENUM('aktif','pasif') NOT NULL DEFAULT 'aktif',
            pengalaman_kerja TEXT DEFAULT NULL,
            pengalaman_organisasi TEXT DEFAULT NULL,
            email VARCHAR(100) DEFAULT NULL,
            basic_skill LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(basic_skill)),
            alasan_tolak_berhenti TEXT DEFAULT NULL,
            tanggal_tolak_berhenti DATE DEFAULT NULL,
            nama_ibu VARCHAR(100) DEFAULT NULL,
            PRIMARY KEY (id_anggota),
            UNIQUE KEY no_anggota (no_anggota),
            UNIQUE KEY no_ktp (no_ktp),
            UNIQUE KEY email (email)
        ) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");

        $this->db->query("CREATE TABLE IF NOT EXISTS angsuran (
            id_angsuran INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
            id_pinjaman INT(10) UNSIGNED NOT NULL,
            tanggal_bayar DATE DEFAULT NULL,
            jumlah_bayar DECIMAL(15,2) NOT NULL,
            denda DECIMAL(15,2) NOT NULL DEFAULT 0.00,
            PRIMARY KEY (id_angsuran),
            KEY id_pinjaman (id_pinjaman)
        ) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");

        $this->db->query("CREATE TABLE IF NOT EXISTS petugas (
            id_petugas INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
            nama_petugas VARCHAR(100) NOT NULL,
            level ENUM('admin','kasir','pimpinan') NOT NULL DEFAULT 'kasir',
            PRIMARY KEY (id_petugas)
        ) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");

        $this->db->query("CREATE TABLE IF NOT EXISTS pinjaman (
            id_pinjaman INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
            id_anggota INT(10) UNSIGNED NOT NULL,
            jumlah_pinjaman DECIMAL(15,2) NOT NULL,
            bunga DECIMAL(5,2) NOT NULL,
            tanggal_pinjam DATE DEFAULT NULL,
            jangka_waktu INT(11) NOT NULL,
            status ENUM('aktif','lunas','menunggak') NOT NULL DEFAULT 'aktif',
            keterangan TEXT DEFAULT NULL,
            jaminan TEXT DEFAULT NULL,
            PRIMARY KEY (id_pinjaman),
            KEY id_anggota (id_anggota)
        ) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");

        $this->db->query("CREATE TABLE IF NOT EXISTS settings (
            id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
            `key` VARCHAR(100) NOT NULL,
            `value` TEXT DEFAULT NULL,
            updated_at TIMESTAMP NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
            PRIMARY KEY (id),
            UNIQUE KEY `key` (`key`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci");

        $this->db->query("CREATE TABLE IF NOT EXISTS simpanan (
            id_simpanan INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
            id_anggota INT(10) UNSIGNED NOT NULL,
            jenis_simpanan ENUM('pokok','wajib','sukarela') NOT NULL,
            tipe_sukarela ENUM('biasa','berjangka') DEFAULT NULL,
            jumlah DECIMAL(15,2) NOT NULL,
            tanggal_simpan DATE DEFAULT NULL,
            jangka_waktu INT(11) DEFAULT NULL,
            status ENUM('pending','aktif','dicairkan','jatuh_tempo') NOT NULL DEFAULT 'aktif',
            tanggal_jatuh_tempo DATE GENERATED ALWAYS AS (CASE WHEN jangka_waktu IS NOT NULL THEN tanggal_simpan + INTERVAL jangka_waktu MONTH ELSE NULL END) STORED,
            PRIMARY KEY (id_simpanan),
            KEY id_anggota (id_anggota)
        ) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");

        $this->db->query("CREATE TABLE IF NOT EXISTS transaksi (
            id_transaksi INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
            tanggal_transaksi DATETIME DEFAULT NULL,
            jenis_transaksi ENUM('simpanan','pinjaman','angsuran','penarikan') NOT NULL,
            id_referensi INT(10) UNSIGNED NOT NULL,
            id_petugas INT(10) UNSIGNED DEFAULT NULL,
            keterangan TEXT DEFAULT NULL,
            PRIMARY KEY (id_transaksi),
            KEY id_petugas (id_petugas)
        ) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");

        $this->db->query("CREATE TABLE IF NOT EXISTS users (
            id_user INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
            username VARCHAR(50) NOT NULL,
            password_hash VARCHAR(255) DEFAULT NULL,
            role ENUM('anggota','petugas','anggota_petugas','admin') NOT NULL,
            id_anggota INT(10) UNSIGNED DEFAULT NULL,
            id_petugas INT(10) UNSIGNED DEFAULT NULL,
            status ENUM('aktif','nonaktif') NOT NULL DEFAULT 'aktif',
            foto VARCHAR(255) DEFAULT NULL,
            PRIMARY KEY (id_user),
            UNIQUE KEY username (username),
            KEY users_id_anggota_foreign (id_anggota),
            KEY users_id_petugas_foreign (id_petugas)
        ) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");

        $this->db->query("CREATE TABLE IF NOT EXISTS user_activation (
            id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
            id_user INT(10) UNSIGNED NOT NULL,
            token VARCHAR(64) NOT NULL,
            expires_at DATETIME DEFAULT NULL,
            used_at DATETIME DEFAULT NULL,
            PRIMARY KEY (id),
            UNIQUE KEY token (token),
            KEY id_user (id_user)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci");

        $this->db->query("CREATE TABLE IF NOT EXISTS waha_templates (
            id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
            slug VARCHAR(50) NOT NULL,
            content TEXT DEFAULT NULL,
            updated_at TIMESTAMP NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
            PRIMARY KEY (id),
            UNIQUE KEY slug (slug)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci");

        $this->db->query("CREATE TABLE IF NOT EXISTS auth_activation_attempts (
            id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
            ip_address VARCHAR(255) NOT NULL,
            user_agent VARCHAR(255) NOT NULL,
            token VARCHAR(255) DEFAULT NULL,
            created_at DATETIME NOT NULL,
            PRIMARY KEY (id)
        ) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");

        $this->db->query("CREATE TABLE IF NOT EXISTS auth_groups (
            id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
            name VARCHAR(255) NOT NULL,
            description VARCHAR(255) NOT NULL,
            PRIMARY KEY (id)
        ) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");

        $this->db->query("CREATE TABLE IF NOT EXISTS auth_groups_permissions (
            group_id INT(11) UNSIGNED NOT NULL DEFAULT 0,
            permission_id INT(11) UNSIGNED NOT NULL DEFAULT 0,
            KEY auth_groups_permissions_permission_id_foreign (permission_id),
            KEY group_id_permission_id (group_id,permission_id)
        ) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");

        $this->db->query("CREATE TABLE IF NOT EXISTS auth_groups_users (
            group_id INT(11) UNSIGNED NOT NULL DEFAULT 0,
            user_id INT(11) UNSIGNED NOT NULL DEFAULT 0,
            KEY auth_groups_users_user_id_foreign (user_id),
            KEY group_id_user_id (group_id,user_id)
        ) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");

        $this->db->query("CREATE TABLE IF NOT EXISTS auth_logins (
            id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
            ip_address VARCHAR(255) DEFAULT NULL,
            email VARCHAR(255) DEFAULT NULL,
            user_id INT(11) UNSIGNED DEFAULT NULL,
            date DATETIME NOT NULL,
            success TINYINT(1) NOT NULL,
            PRIMARY KEY (id),
            KEY email (email(250)),
            KEY user_id (user_id)
        ) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");

        $this->db->query("CREATE TABLE IF NOT EXISTS auth_permissions (
            id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
            name VARCHAR(255) NOT NULL,
            description VARCHAR(255) NOT NULL,
            PRIMARY KEY (id)
        ) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");

        $this->db->query("CREATE TABLE IF NOT EXISTS auth_reset_attempts (
            id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
            email VARCHAR(255) NOT NULL,
            ip_address VARCHAR(255) NOT NULL,
            user_agent VARCHAR(255) NOT NULL,
            token VARCHAR(255) DEFAULT NULL,
            created_at DATETIME NOT NULL,
            PRIMARY KEY (id)
        ) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");

        $this->db->query("CREATE TABLE IF NOT EXISTS auth_tokens (
            id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
            selector VARCHAR(255) NOT NULL,
            hashedValidator VARCHAR(255) NOT NULL,
            user_id INT(11) UNSIGNED NOT NULL,
            expires DATETIME NOT NULL,
            PRIMARY KEY (id),
            KEY auth_tokens_user_id_foreign (user_id),
            KEY selector (selector(250))
        ) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");

        $this->db->query("CREATE TABLE IF NOT EXISTS auth_users_permissions (
            user_id INT(11) UNSIGNED NOT NULL DEFAULT 0,
            permission_id INT(11) UNSIGNED NOT NULL DEFAULT 0,
            KEY auth_users_permissions_permission_id_foreign (permission_id),
            KEY user_id_permission_id (user_id,permission_id)
        ) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");

        $this->db->query("DROP VIEW IF EXISTS v_sisa_pinjaman");
        $this->db->query("CREATE ALGORITHM=UNDEFINED VIEW v_sisa_pinjaman AS SELECT p.id_pinjaman AS id_pinjaman, a.nama AS nama, p.jumlah_pinjaman AS jumlah_pinjaman, COALESCE(SUM(ang.jumlah_bayar),0) AS total_dibayar, p.jumlah_pinjaman-COALESCE(SUM(ang.jumlah_bayar),0) AS sisa_pinjaman FROM (pinjaman p JOIN anggota a ON a.id_anggota = p.id_anggota) LEFT JOIN angsuran ang ON ang.id_pinjaman = p.id_pinjaman GROUP BY p.id_pinjaman");

        $this->db->query("DROP VIEW IF EXISTS v_total_simpanan_per_anggota");
        $this->db->query("CREATE ALGORITHM=UNDEFINED VIEW v_total_simpanan_per_anggota AS SELECT a.id_anggota AS id_anggota, a.nama AS nama, SUM(s.jumlah) AS total_simpanan FROM anggota a LEFT JOIN simpanan s ON a.id_anggota = s.id_anggota GROUP BY a.id_anggota");
    }

    public function down()
    {
        $this->db->query("DROP VIEW IF EXISTS v_total_simpanan_per_anggota");
        $this->db->query("DROP VIEW IF EXISTS v_sisa_pinjaman");
        $this->db->query("DROP TABLE IF EXISTS auth_users_permissions");
        $this->db->query("DROP TABLE IF EXISTS auth_tokens");
        $this->db->query("DROP TABLE IF EXISTS auth_reset_attempts");
        $this->db->query("DROP TABLE IF EXISTS auth_permissions");
        $this->db->query("DROP TABLE IF EXISTS auth_logins");
        $this->db->query("DROP TABLE IF EXISTS auth_groups_users");
        $this->db->query("DROP TABLE IF EXISTS auth_groups_permissions");
        $this->db->query("DROP TABLE IF EXISTS auth_groups");
        $this->db->query("DROP TABLE IF EXISTS auth_activation_attempts");
        $this->db->query("DROP TABLE IF EXISTS waha_templates");
        $this->db->query("DROP TABLE IF EXISTS user_activation");
        $this->db->query("DROP TABLE IF EXISTS users");
        $this->db->query("DROP TABLE IF EXISTS transaksi");
        $this->db->query("DROP TABLE IF EXISTS simpanan");
        $this->db->query("DROP TABLE IF EXISTS settings");
        $this->db->query("DROP TABLE IF EXISTS pinjaman");
        $this->db->query("DROP TABLE IF EXISTS petugas");
        $this->db->query("DROP TABLE IF EXISTS angsuran");
        $this->db->query("DROP TABLE IF EXISTS anggota");
    }
}

