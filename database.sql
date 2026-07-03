-- =========================================================
-- Database: uas_booking_restoran
-- UAS Pemrograman Web 2 - Data Booking Restoran (digit terakhir NIM = 1)
-- =========================================================

CREATE DATABASE IF NOT EXISTS uas_booking_restoran;
USE uas_booking_restoran;

-- Tabel untuk login
CREATE TABLE IF NOT EXISTS login (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- User default: username = admin | password = admin123
INSERT INTO login (username, password) VALUES
('admin', '$2y$12$cuQ.6yqkM6xPZixNDFdSX.Tm993dkK8A1YTsyQ361m3RV4ZESVFT.');

-- Tabel data entri: Booking Restoran
CREATE TABLE IF NOT EXISTS booking_restoran (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_pemesan VARCHAR(100) NOT NULL,
    no_telepon VARCHAR(20) NOT NULL,
    tanggal_booking DATE NOT NULL,
    jam_booking TIME NOT NULL,
    jumlah_tamu INT NOT NULL,
    nomor_meja VARCHAR(10) NOT NULL,
    catatan TEXT,
    gambar VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Contoh data (opsional)
INSERT INTO booking_restoran
(nama_pemesan, no_telepon, tanggal_booking, jam_booking, jumlah_tamu, nomor_meja, catatan, gambar)
VALUES
('Andi Saputra', '081234567890', '2026-07-10', '19:00:00', 4, 'A1', 'Dekat jendela', NULL),
('Siti Rahma', '082198765432', '2026-07-11', '18:30:00', 2, 'B3', 'Ulang tahun, minta lilin', NULL);
