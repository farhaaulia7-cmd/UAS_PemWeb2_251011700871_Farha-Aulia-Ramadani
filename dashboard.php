<?php
require_once 'includes/functions.php';
requireLogin();
require_once 'config/database.php';

$activePage = 'dashboard';

$search = isset($_GET['search']) ? sanitize($conn, $_GET['search']) : '';

$query = "SELECT * FROM booking_restoran";
if ($search !== '') {
    $query .= " WHERE nama_pemesan LIKE '%$search%'
                OR no_telepon LIKE '%$search%'
                OR nomor_meja LIKE '%$search%'
                OR tanggal_booking LIKE '%$search%'";
}
$query .= " ORDER BY id DESC";
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data Booking Restoran</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>

<?php require 'includes/sidebar.php'; ?>

<div class="content-wrapper">
    <div class="topbar">
        <h5 class="mb-0">Data Booking Restoran</h5>
        <div>Welcome back, <strong style="color:var(--pink)"><?= h($_SESSION['username']) ?></strong></div>
    </div>

    <div class="p-4">

        <?php if (isset($_SESSION['flash'])): ?>
            <div class="alert alert-<?= h($_SESSION['flash']['type']) ?>">
                <?= h($_SESSION['flash']['message']) ?>
            </div>
            <?php unset($_SESSION['flash']); ?>
        <?php endif; ?>

        <div class="card card-custom p-3">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                <div>
                    <a href="create.php" class="btn btn-pink">+ Add Data</a>
                    <a href="report_pdf.php" target="_blank" class="btn btn-black">🧾 Report Data (PDF)</a>
                </div>
                <form method="GET" class="d-flex" role="search">
                    <input type="text" name="search" class="form-control me-2" placeholder="Cari nama, telepon, meja, tanggal..."
                           value="<?= h($search) ?>">
                    <button class="btn btn-pink" type="submit">Search</button>
                </form>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Pemesan</th>
                            <th>No. Telepon</th>
                            <th>Tanggal</th>
                            <th>Jam</th>
                            <th>Tamu</th>
                            <th>Meja</th>
                            <th>Gambar</th>
                            <th>Catatan</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if ($result && mysqli_num_rows($result) > 0): ?>
                        <?php $no = 1; while ($row = mysqli_fetch_assoc($result)): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= h($row['nama_pemesan']) ?></td>
                                <td><?= h($row['no_telepon']) ?></td>
                                <td><?= h($row['tanggal_booking']) ?></td>
                                <td><?= h(substr($row['jam_booking'], 0, 5)) ?></td>
                                <td><span class="badge badge-pink"><?= h($row['jumlah_tamu']) ?></span></td>
                                <td><?= h($row['nomor_meja']) ?></td>
                                <td>
                                    <?php if (!empty($row['gambar']) && file_exists('assets/uploads/' . $row['gambar'])): ?>
                                        <img src="assets/uploads/<?= h($row['gambar']) ?>" class="thumb" alt="gambar">
                                    <?php else: ?>
                                        <span class="text-muted small">-</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= h($row['catatan']) ?></td>
                                <td>
                                    <a href="edit.php?id=<?= (int)$row['id'] ?>" class="btn btn-sm btn-black">Edit</a>
                                    <a href="delete.php?id=<?= (int)$row['id'] ?>" class="btn btn-sm btn-danger"
                                       onclick="return confirm('Hapus data booking ini?');">Delete</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="10" class="text-center text-muted py-4">Belum ada data / data tidak ditemukan.</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
