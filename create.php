<?php
require_once 'includes/functions.php';
requireLogin();
require_once 'config/database.php';

$activePage = 'dashboard';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama    = sanitize($conn, $_POST['nama_pemesan'] ?? '');
    $telepon = sanitize($conn, $_POST['no_telepon'] ?? '');
    $tanggal = sanitize($conn, $_POST['tanggal_booking'] ?? '');
    $jam     = sanitize($conn, $_POST['jam_booking'] ?? '');
    $tamu    = (int)($_POST['jumlah_tamu'] ?? 0);
    $meja    = sanitize($conn, $_POST['nomor_meja'] ?? '');
    $catatan = sanitize($conn, $_POST['catatan'] ?? '');

    if ($nama === '' || $telepon === '' || $tanggal === '' || $jam === '' || $tamu <= 0 || $meja === '') {
        $error = 'Semua field wajib diisi dengan benar.';
    } else {
        [$gambar, $uploadError] = uploadGambar('gambar');
        if ($uploadError) {
            $error = $uploadError;
        } else {
            $gambarSql = $gambar ? "'" . $gambar . "'" : "NULL";
            $query = "INSERT INTO booking_restoran
                (nama_pemesan, no_telepon, tanggal_booking, jam_booking, jumlah_tamu, nomor_meja, catatan, gambar)
                VALUES
                ('$nama', '$telepon', '$tanggal', '$jam', $tamu, '$meja', '$catatan', $gambarSql)";

            if (mysqli_query($conn, $query)) {
                $_SESSION['flash'] = ['type' => 'success', 'message' => 'Data booking berhasil ditambahkan.'];
                header('Location: dashboard.php');
                exit;
            } else {
                $error = 'Gagal menyimpan data: ' . mysqli_error($conn);
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Data Booking</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>

<?php require 'includes/sidebar.php'; ?>

<div class="content-wrapper">
    <div class="topbar">
        <h5 class="mb-0">Tambah Data Booking</h5>
        <div>Welcome back, <strong style="color:var(--pink)"><?= h($_SESSION['username']) ?></strong></div>
    </div>

    <div class="p-4">
        <div class="card card-custom p-4" style="max-width:700px">
            <?php if ($error): ?>
                <div class="alert alert-danger"><?= h($error) ?></div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <label class="form-label">Nama Pemesan</label>
                    <input type="text" name="nama_pemesan" class="form-control" required>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">No. Telepon</label>
                        <input type="text" name="no_telepon" class="form-control" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Nomor Meja</label>
                        <input type="text" name="nomor_meja" class="form-control" required>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Tanggal Booking</label>
                        <input type="date" name="tanggal_booking" class="form-control" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Jam Booking</label>
                        <input type="time" name="jam_booking" class="form-control" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Jumlah Tamu</label>
                        <input type="number" name="jumlah_tamu" min="1" class="form-control" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Catatan</label>
                    <textarea name="catatan" class="form-control" rows="3"></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Upload Gambar/File (bukti/menu) - JPG/PNG/GIF max 2MB</label>
                    <input type="file" name="gambar" class="form-control" accept=".jpg,.jpeg,.png,.gif">
                </div>

                <button type="submit" class="btn btn-pink">Simpan</button>
                <a href="dashboard.php" class="btn btn-outline-secondary">Batal</a>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
