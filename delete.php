<?php
require_once 'includes/functions.php';
requireLogin();
require_once 'config/database.php';

$id = (int)($_GET['id'] ?? 0);

$result = mysqli_query($conn, "SELECT gambar FROM booking_restoran WHERE id = $id");
if ($result && mysqli_num_rows($result) === 1) {
    $row = mysqli_fetch_assoc($result);

    if (mysqli_query($conn, "DELETE FROM booking_restoran WHERE id = $id")) {
        if (!empty($row['gambar']) && file_exists('assets/uploads/' . $row['gambar'])) {
            unlink('assets/uploads/' . $row['gambar']);
        }
        $_SESSION['flash'] = ['type' => 'success', 'message' => 'Data booking berhasil dihapus.'];
    } else {
        $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Gagal menghapus data.'];
    }
} else {
    $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Data tidak ditemukan.'];
}

header('Location: dashboard.php');
exit;
