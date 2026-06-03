<?php
session_start();
include 'config/koneksi.php';

if(!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit;
}

$id_user = $_SESSION['id'];

if(isset($_GET['all'])) {
    mysqli_query($conn, "UPDATE notifikasi SET is_read=1 WHERE id_user='$id_user'");
    echo "ok";
    exit;
}

if(isset($_GET['id'])) {
    $id_notif = mysqli_real_escape_string($conn, $_GET['id']);
    
    // Get link
    $q = mysqli_query($conn, "SELECT link FROM notifikasi WHERE id_notif='$id_notif' AND id_user='$id_user'");
    if(mysqli_num_rows($q) > 0) {
        $link = mysqli_fetch_assoc($q)['link'];
        mysqli_query($conn, "UPDATE notifikasi SET is_read=1 WHERE id_notif='$id_notif'");
        
        $role_dir = $_SESSION['role']; // 'mahasiswa' atau 'mentor'
        header("Location: " . $role_dir . "/" . $link);
        exit;
    }
}

// Fallback
if($_SESSION['role'] == 'mahasiswa') header("Location: mahasiswa/dashboard.php");
else if($_SESSION['role'] == 'mentor') header("Location: mentor/dashboard.php");
else header("Location: admin/dashboard.php");
?>
