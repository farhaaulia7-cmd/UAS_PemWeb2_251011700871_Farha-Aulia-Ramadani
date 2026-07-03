<?php
// $activePage harus di-set di halaman pemanggil, contoh: $activePage = 'dashboard';
$activePage = $activePage ?? '';
?>
<div class="sidebar">
    <div class="brand">Resto<span style="color:#fff">Book</span></div>
    <a href="dashboard.php" class="<?= $activePage === 'dashboard' ? 'active' : '' ?>">
        📋 Data Booking
    </a>
    <a href="report_pdf.php" target="_blank">
        🧾 Report PDF
    </a>
    <a href="logout.php" onclick="return confirm('Yakin ingin logout?');">
        🚪 Logout
    </a>
</div>
