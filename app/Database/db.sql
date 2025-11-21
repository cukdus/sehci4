-- ============================================================
-- DATABASE: Koperasi Simpan Pinjam
-- Versi FINAL (Login Gabungan + Dashboard Dipisah)
-- ============================================================

CREATE DATABASE IF NOT EXISTS koperasi_simpan_pinjam;
USE koperasi_simpan_pinjam;

-- ============================================================
-- TABEL: ANGGOTA
-- ============================================================
CREATE TABLE anggota (
    id_anggota INT AUTO_INCREMENT PRIMARY KEY,
    no_anggota VARCHAR(20) UNIQUE NOT NULL,
    nama VARCHAR(100) NOT NULL,

    -- INFO PERSONAL
    jenis_kelamin ENUM('Laki-laki','Perempuan'),
    tempat_lahir VARCHAR(100),
    tanggal_lahir DATE,
    alamat TEXT,
    no_telepon VARCHAR(20),

    -- IDENTITAS
    no_ktp VARCHAR(30) UNIQUE,
    no_kk VARCHAR(30),
    no_npwp VARCHAR(30),

    -- FOTO
    foto VARCHAR(255),

    -- STATUS KEANGGOTAAN
    tanggal_gabung DATE DEFAULT CURRENT_DATE,
    tanggal_berhenti DATE DEFAULT NULL,
    alasan_berhenti TEXT,
    status ENUM('aktif','nonaktif') DEFAULT 'aktif',
    jenis_anggota ENUM('aktif', 'pasif') DEFAULT 'aktif',

    -- SKILL + PENGALAMAN
    basic_skill ENUM(
        'Management Accounting',
        'Digital Marketing',
        'Leadership',
        'Ms. Office Program',
        'Design Graphic Program',
        'Other'
    ) DEFAULT NULL,
    pengalaman_kerja TEXT,
    pengalaman_organisasi TEXT
) ENGINE=InnoDB;

-- ============================================================
-- TABEL: PETUGAS
-- (username/password berada di tabel users)
-- ============================================================
CREATE TABLE petugas (
    id_petugas INT AUTO_INCREMENT PRIMARY KEY,
    nama_petugas VARCHAR(100) NOT NULL,
    level ENUM('admin','kasir','pimpinan') DEFAULT 'kasir'
) ENGINE=InnoDB;

-- ============================================================
-- TABEL: USERS (LOGIN GABUNGAN)
-- digunakan oleh anggota, petugas, atau keduanya
-- ============================================================
CREATE TABLE users (
    id_user INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,

    role ENUM('anggota','petugas','anggota_petugas','admin') NOT NULL,

    id_anggota INT NULL,
    id_petugas INT NULL,
    status ENUM('aktif','nonaktif') DEFAULT 'aktif',

    FOREIGN KEY (id_anggota) REFERENCES anggota(id_anggota)
        ON DELETE SET NULL ON UPDATE CASCADE,
    FOREIGN KEY (id_petugas) REFERENCES petugas(id_petugas)
        ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB;

-- ============================================================
-- TABEL: SIMPANAN (TERM DEPOSIT READY)
-- ============================================================
CREATE TABLE simpanan (
    id_simpanan INT AUTO_INCREMENT PRIMARY KEY,
    id_anggota INT NOT NULL,

    jenis_simpanan ENUM('pokok','wajib','sukarela') NOT NULL,
    tipe_sukarela ENUM('biasa','berjangka') DEFAULT NULL,

    jumlah DECIMAL(15,2) NOT NULL,
    tanggal_simpan DATE DEFAULT CURRENT_DATE,
    jangka_waktu INT DEFAULT NULL, -- bulan

    tanggal_jatuh_tempo DATE GENERATED ALWAYS AS (
        CASE
            WHEN jangka_waktu IS NOT NULL THEN DATE_ADD(tanggal_simpan, INTERVAL jangka_waktu MONTH)
            ELSE NULL
        END
    ) STORED,

    status ENUM('aktif','dicairkan','jatuh_tempo') DEFAULT 'aktif',

    FOREIGN KEY (id_anggota) REFERENCES anggota(id_anggota)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

-- ============================================================
-- TABEL: PINJAMAN
-- ============================================================
CREATE TABLE pinjaman (
    id_pinjaman INT AUTO_INCREMENT PRIMARY KEY,
    id_anggota INT NOT NULL,
    jumlah_pinjaman DECIMAL(15,2) NOT NULL,
    bunga DECIMAL(5,2) NOT NULL,
    tanggal_pinjam DATE DEFAULT CURRENT_DATE,
    jangka_waktu INT NOT NULL,
    status ENUM('aktif','lunas','menunggak') DEFAULT 'aktif',

    FOREIGN KEY (id_anggota) REFERENCES anggota(id_anggota)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

-- ============================================================
-- TABEL: ANGSURAN
-- ============================================================
CREATE TABLE angsuran (
    id_angsuran INT AUTO_INCREMENT PRIMARY KEY,
    id_pinjaman INT NOT NULL,
    tanggal_bayar DATE DEFAULT CURRENT_DATE,
    jumlah_bayar DECIMAL(15,2) NOT NULL,
    denda DECIMAL(15,2) DEFAULT 0,

    FOREIGN KEY (id_pinjaman) REFERENCES pinjaman(id_pinjaman)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

-- ============================================================
-- TABEL: TRANSAKSI (LOG)
-- ============================================================
CREATE TABLE transaksi (
    id_transaksi INT AUTO_INCREMENT PRIMARY KEY,
    tanggal_transaksi DATETIME DEFAULT CURRENT_TIMESTAMP,
    jenis_transaksi ENUM('simpanan','pinjaman','angsuran','penarikan') NOT NULL,
    id_referensi INT NOT NULL,
    id_petugas INT,
    keterangan TEXT,

    FOREIGN KEY (id_petugas) REFERENCES petugas(id_petugas)
        ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB;

-- ============================================================
-- VIEW: TOTAL SIMPANAN PER ANGGOTA
-- ============================================================
CREATE OR REPLACE VIEW v_total_simpanan_per_anggota AS
SELECT 
    a.id_anggota,
    a.nama,
    SUM(s.jumlah) AS total_simpanan
FROM anggota a
LEFT JOIN simpanan s ON a.id_anggota = s.id_anggota
GROUP BY a.id_anggota;

-- ============================================================
-- VIEW: SISA PINJAMAN
-- ============================================================
CREATE OR REPLACE VIEW v_sisa_pinjaman AS
SELECT 
    p.id_pinjaman,
    a.nama,
    p.jumlah_pinjaman,
    COALESCE(SUM(ang.jumlah_bayar), 0) AS total_dibayar,
    (p.jumlah_pinjaman - COALESCE(SUM(ang.jumlah_bayar), 0)) AS sisa_pinjaman
FROM pinjaman p
JOIN anggota a ON a.id_anggota = p.id_anggota
LEFT JOIN angsuran ang ON ang.id_pinjaman = p.id_pinjaman
GROUP BY p.id_pinjaman;
