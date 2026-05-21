<?php
session_start();

// Jika tidak ada sesi 'login', tendang kembali ke halaman login
if (!isset($_SESSION['login'])) {
    header("Location: ../login.php");
    exit;
}
?>