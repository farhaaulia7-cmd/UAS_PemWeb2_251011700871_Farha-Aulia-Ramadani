<?php
// Mulai session (dipakai di semua halaman)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Wajibkan user login sebelum mengakses halaman data entri
 */
function requireLogin() {
    if (!isset($_SESSION['username'])) {
        header('Location: login.php');
        exit;
    }
}

/**
 * Sanitasi input string sederhana untuk mencegah SQL Injection & XSS
 */
function sanitize($conn, $str) {
    return mysqli_real_escape_string($conn, trim($str));
}

/**
 * Escape output ke HTML untuk mencegah XSS saat menampilkan data
 */
function h($str) {
    return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * Proses upload gambar/file, mengembalikan nama file baru atau null jika tidak ada upload
 */
function uploadGambar($fileInputName, $uploadDir = 'assets/uploads/') {
    if (!isset($_FILES[$fileInputName]) || $_FILES[$fileInputName]['error'] === UPLOAD_ERR_NO_FILE) {
        return [null, null]; // tidak ada file diupload
    }

    $file = $_FILES[$fileInputName];

    if ($file['error'] !== UPLOAD_ERR_OK) {
        return [null, 'Terjadi kesalahan saat upload file.'];
    }

    $allowedExt = ['jpg', 'jpeg', 'png', 'gif'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

    if (!in_array($ext, $allowedExt)) {
        return [null, 'Format file tidak didukung. Gunakan JPG, JPEG, PNG, atau GIF.'];
    }

    if ($file['size'] > 2 * 1024 * 1024) { // 2MB
        return [null, 'Ukuran file maksimal 2MB.'];
    }

    $newName = 'booking_' . time() . '_' . uniqid() . '.' . $ext;
    $destination = $uploadDir . $newName;

    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    if (!move_uploaded_file($file['tmp_name'], $destination)) {
        return [null, 'Gagal menyimpan file ke server.'];
    }

    return [$newName, null];
}
