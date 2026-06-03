<?php
include '../config/auth.php';
if ($_SESSION['role'] != 'mentor') {
    echo "<script>window.location='../logout.php';</script>";
    exit;
}
include '../config/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_booking = mysqli_real_escape_string($conn, $_POST['id_booking']);
    $link_meet = mysqli_real_escape_string($conn, $_POST['link_meet']);
    $catatan_mentor = mysqli_real_escape_string($conn, $_POST['catatan_mentor']);
    $id_user = $_SESSION['id'];
    
    $mentor_data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT id_mentor FROM mentor WHERE id_user='$id_user'"));
    $id_mentor = $mentor_data['id_mentor'];
    
    $cek = mysqli_query($conn, "SELECT id_booking FROM booking WHERE id_booking='$id_booking' AND id_mentor='$id_mentor'");
    if (mysqli_num_rows($cek) > 0) {
        $update = mysqli_query($conn, "UPDATE booking SET link_meet='$link_meet', catatan_mentor='$catatan_mentor' WHERE id_booking='$id_booking'");
        if ($update) {
            echo "<script>
                alert('Informasi sesi berhasil diperbarui!');
                window.location='dashboard.php';
            </script>";
        } else {
            echo "<script>
                alert('Gagal memperbarui informasi sesi.');
                window.location='dashboard.php';
            </script>";
        }
    } else {
        die("Data tidak valid.");
    }
}
?>
