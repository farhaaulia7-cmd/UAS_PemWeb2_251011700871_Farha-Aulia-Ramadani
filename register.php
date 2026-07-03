<?php
require_once 'includes/functions.php';
require_once 'config/database.php';

if (isset($_SESSION['username'])) {
    header('Location: dashboard.php');
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitize($conn, $_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm_password'] ?? '';

    if ($username === '' || $password === '') {
        $error = 'Username dan password wajib diisi.';
    } elseif ($password !== $confirm) {
        $error = 'Konfirmasi password tidak sama.';
    } else {
        $check = mysqli_query($conn, "SELECT id FROM login WHERE username = '$username'");
        if (mysqli_num_rows($check) > 0) {
            $error = 'Username sudah digunakan.';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $query = "INSERT INTO login (username, password) VALUES ('$username', '$hash')";
            if (mysqli_query($conn, $query)) {
                $success = 'Akun berhasil dibuat. Silakan login.';
            } else {
                $error = 'Gagal membuat akun: ' . mysqli_error($conn);
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Register - Booking Restoran</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
<div class="auth-wrapper">
    <div class="auth-card">
        <h3 class="text-center mb-1">Resto<span>Book</span></h3>
        <p class="text-center text-muted mb-4">Buat akun baru</p>

        <?php if ($error): ?><div class="alert alert-danger py-2"><?= h($error) ?></div><?php endif; ?>
        <?php if ($success): ?><div class="alert alert-success py-2"><?= h($success) ?></div><?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Username</label>
                <input type="text" name="username" class="form-control" required autofocus>
            </div>
            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Konfirmasi Password</label>
                <input type="password" name="confirm_password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-black w-100">Register</button>
        </form>

        <p class="text-center mt-3 mb-0 small">
            Sudah punya akun? <a href="login.php" class="text-decoration-none" style="color:var(--pink)">Login</a>
        </p>
    </div>
</div>
</body>
</html>
