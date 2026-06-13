<?php
include '../config/koneksi.php';
include '../config/auth.php';

if($_SESSION['role'] != 'admin'){
    die("Akses ditolak");
}

if(!isset($_GET['id'])){
    header("Location: pengguna.php");
    exit;
}

$id = (int) $_GET['id'];

/* hapus data mentor jika user tersebut mentor */
mysqli_query($conn, "DELETE FROM mentor WHERE id_user = $id");

/* hapus akun user */
mysqli_query($conn, "DELETE FROM users WHERE id = $id");

header("Location: pengguna.php");
exit;
?>