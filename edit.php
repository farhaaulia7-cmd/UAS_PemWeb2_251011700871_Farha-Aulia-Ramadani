<?php
require_once 'includes/functions.php';
requireLogin();
require_once 'config/database.php';

$activePage = 'dashboard';
$error = '';

$id = (int)($_GET['id'] ?? 0);
$dataResult = mysqli_query($conn, "SELECT * FROM booking_restoran WHERE id = $id");

if (!$dataResult || mysqli_num_rows($dataResult) === 0) {
    $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Data tidak ditemukan.'];
    header('Location: dashboard.php');
    exit;
}
$data = mysqli_fetch_assoc($dataResult);

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
        [$gambarBaru, $uploadError] = uploadGambar('gambar');
        if ($uploadError) {
            $error = $uploadError;
        } else {
            $gambarField = $data['gambar'];
            if ($gambarBaru) {
                if (!empty($data['gambar']) && file_exists('assets/uploads/' . $data['gambar'])) {
                    unlink('assets/uploads/' . $data['gambar']);
                }
                $gambarField = $gambarBaru;
            }
            $gambarSql = $gambarField ? "'" . $gambarField . "'" : "NULL";

            $query = "UPDATE booking_restoran SET
                nama_pemesan = '$nama',
                no_telepon = '$telepon',
                tanggal_booking = '$tanggal',
                jam_booking = '$jam',
                jumlah_tamu = $tamu,
                nomor_meja = '$meja',
                catatan = '$catatan',
                gambar = $gambarSql
                WHERE id = $id";

            if (mysqli_query($conn, $query)) {
                $_SESSION['flash'] = ['type' => 'success', 'message' => 'Data booking berhasil diperbarui.'];
                header('Location: dashboard.php');
                exit;
            } else {
                $error = 'Gagal memperbarui data: ' . mysqli_error($conn);
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Data Booking</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>

<?php require 'includes/sidebar.php'; ?>

<div class="content-wrapper">
    <div class="topbar">
        <h5 class="mb-0">Edit Data Booking</h5>
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
                    <input type="text" name="nama_pemesan" class="form-control" value="<?= h($data['nama_pemesan']) ?>" required>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">No. Telepon</label>
                        <input type="text" name="no_telepon" class="form-control" value="<?= h($data['no_telepon']) ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Nomor Meja</label>
                        <input type="text" name="nomor_meja" class="form-control" value="<?= h($data['nomor_meja']) ?>" required>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Tanggal Booking</label>
                        <input type="date" name="tanggal_booking" class="form-control" value="<?= h($data['tanggal_booking']) ?>" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Jam Booking</label>
                        <input type="time" name="jam_booking" class="form-control" value="<?= h(substr($data['jam_booking'],0,5)) ?>" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Jumlah Tamu</label>
                        <input type="number" name="jumlah_tamu" min="1" class="form-control" value="<?= h($data['jumlah_tamu']) ?>" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Catatan</label>
                    <textarea name="catatan" class="form-control" rows="3"><?= h($data['catatan']) ?></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Gambar/File Saat Ini</label><br>
                    <?php if (!empty($data['gambar']) && file_exists('assets/uploads/' . $data['gambar'])): ?>
                        <img src="assets/uploads/<?= h($data['gambar']) ?>" class="thumb mb-2" style="width:90px;height:90px" alt="gambar">
                    <?php else: ?>
                        <div class="text-muted small mb-2">Belum ada gambar</div>
                    <?php endif; ?>
                    <label class="form-label">Ganti Gambar (opsional) - JPG/PNG/GIF max 2MB</label>
                    <input type="file" name="gambar" class="form-control" accept=".jpg,.jpeg,.png,.gif">
                </div>

                <button type="submit" class="btn btn-pink">Update</button>
                <a href="dashboard.php" class="btn btn-outline-secondary">Batal</a>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
