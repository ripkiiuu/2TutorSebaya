<?php
session_start();
session_destroy(); // Menghapus semua sesi login
header("Location: index.php"); // Mengembalikan user ke halaman awal
exit;
?>