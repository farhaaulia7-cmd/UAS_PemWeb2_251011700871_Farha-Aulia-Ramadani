<?php
require_once 'includes/functions.php';
requireLogin();
require_once 'config/database.php';
require_once 'includes/SimplePDF.php';

$result = mysqli_query($conn, "SELECT * FROM booking_restoran ORDER BY tanggal_booking ASC, jam_booking ASC");

$pdf = new SimplePDF('L');

$pdf->title('Laporan Data Booking Restoran');
$pdf->subtitle('Dicetak pada: ' . date('d-m-Y H:i') . ' oleh: ' . ($_SESSION['username'] ?? '-'));

$colWidths = [30, 130, 100, 90, 60, 60, 60, 120, 100];
$headers   = ['No', 'Nama Pemesan', 'No. Telepon', 'Tanggal', 'Jam', 'Tamu', 'Meja', 'Catatan', 'Gambar'];

$pdf->row($headers, $colWidths, true, 10);
$pdf->tableHeaderLine();

$no = 1;
if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $pdf->row([
            $no++,
            $row['nama_pemesan'],
            $row['no_telepon'],
            $row['tanggal_booking'],
            substr($row['jam_booking'], 0, 5),
            $row['jumlah_tamu'],
            $row['nomor_meja'],
            $row['catatan'] ?: '-',
            $row['gambar'] ?: '-',
        ], $colWidths);
    }
} else {
    $pdf->row(['Tidak ada data booking.'], [400]);
}

$pdf->output('laporan_booking_restoran_' . date('Ymd_His') . '.pdf');
