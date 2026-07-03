<?php
$host   = 'localhost';
$dbUser = 'root';
$dbPass = '';
$dbName = 'db_booking_restoran';

$conn = mysqli_connect($host, $dbUser, $dbPass, $dbName);

if (!$conn) {
    die('Koneksi database gagal: ' . mysqli_connect_error());
}

mysqli_set_charset($conn, 'utf8mb4');
