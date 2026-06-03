<?php
include '../config/auth.php';
if ($_SESSION['role'] != 'mahasiswa') {
    echo "<script>window.location='../logout.php';</script>";
    exit;
}
include '../config/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['bukti'])) {
    $id_booking = mysqli_real_escape_string($conn, $_POST['id_booking']);
    $id_mahasiswa = $_SESSION['id'];

    $cek = mysqli_query($conn, "SELECT id_booking FROM booking WHERE id_booking='$id_booking' AND id_mahasiswa='$id_mahasiswa'");
    if (mysqli_num_rows($cek) == 0) {
        die("Data tidak ditemukan.");
    }
    
    $file_name = $_FILES['bukti']['name'];
    $file_tmp = $_FILES['bukti']['tmp_name'];

    $ext = pathinfo($file_name, PATHINFO_EXTENSION);
    $new_name = "bukti_" . $id_booking . "_" . time() . "." . $ext;

    $upload_dir = "../assets/uploads/";
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }
    
    if (move_uploaded_file($file_tmp, $upload_dir . $new_name)) {
        mysqli_query($conn, "UPDATE booking SET bukti_bayar='$new_name' WHERE id_booking='$id_booking'");
        
        $q_b = mysqli_fetch_assoc(mysqli_query($conn, "SELECT id_mentor FROM booking WHERE id_booking='$id_booking'"));
        $id_m = $q_b['id_mentor'];
        $q_mentor_user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT id_user FROM mentor WHERE id_mentor='$id_m'"));
        $mentor_user_id = $q_mentor_user['id_user'];
        $q_nama_pengirim = mysqli_fetch_assoc(mysqli_query($conn, "SELECT nama FROM users WHERE id='$id_mahasiswa'"));
        
        $pesan_notif = "Mahasiswa (" . $q_nama_pengirim['nama'] . ") telah mengunggah bukti pembayaran. Silakan periksa.";
        mysqli_query($conn, "INSERT INTO notifikasi (id_user, pesan, ikon, link) VALUES ('$mentor_user_id', '$pesan_notif', '💸', 'dashboard.php')");

        echo "<script>
            alert('Bukti pembayaran berhasil diupload! Menunggu konfirmasi mentor.');
            window.location='jadwal.php';
        </script>";
    } else {
        echo "<script>
            alert('Gagal mengupload bukti pembayaran.');
            window.location='jadwal.php';
        </script>";
    }
}
?>
