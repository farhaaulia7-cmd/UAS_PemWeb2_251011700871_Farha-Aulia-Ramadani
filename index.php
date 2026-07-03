<?php
require_once 'includes/functions.php';
header('Location: ' . (isset($_SESSION['username']) ? 'dashboard.php' : 'login.php'));
exit;
