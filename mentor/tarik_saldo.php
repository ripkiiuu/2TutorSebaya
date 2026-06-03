<?php
include '../config/auth.php';
if ($_SESSION['role'] != 'mentor') exit;
include '../config/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_user = $_SESSION['id'];
    $q_m = mysqli_fetch_assoc(mysqli_query($conn, "SELECT id_mentor FROM mentor WHERE id_user='$id_user'"));
    $id_mentor = $q_m['id_mentor'];
    
    $jumlah = (int)$_POST['jumlah'];
    $metode_bank = mysqli_real_escape_string($conn, $_POST['metode_bank']);
    $nomor_rekening = mysqli_real_escape_string($conn, $_POST['nomor_rekening']);
    $info_rekening = $metode_bank . " - " . $nomor_rekening;
    
    // Validasi saldo
    $q_penghasilan = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(total_harga) as sum_harga FROM booking WHERE id_mentor='$id_mentor' AND status='selesai'"));
    $q_tarik = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(jumlah) as sum_tarik FROM penarikan WHERE id_mentor='$id_mentor' AND status IN ('menunggu', 'berhasil')"));
    $total_kotor = $q_penghasilan['sum_harga'] ? $q_penghasilan['sum_harga'] : 0;
    $total_ditarik = $q_tarik['sum_tarik'] ? $q_tarik['sum_tarik'] : 0;
    $saldo_aktif = $total_kotor - $total_ditarik;
    
    if ($jumlah <= 0 || $jumlah > $saldo_aktif) {
        echo "<script>alert('Nominal tidak valid atau saldo tidak cukup!'); window.location='dashboard.php';</script>";
        exit;
    }
    
    $insert = mysqli_query($conn, "INSERT INTO penarikan (id_mentor, jumlah, info_rekening) VALUES ('$id_mentor', '$jumlah', '$info_rekening')");
    
    if ($insert) {
        // Notifikasi untuk Admin (ID Admin = 1)
        $q_nama = mysqli_fetch_assoc(mysqli_query($conn, "SELECT nama FROM users WHERE id='$id_user'"));
        $pesan_admin = "Mentor (" . $q_nama['nama'] . ") mengajukan penarikan dana sebesar Rp " . number_format($jumlah, 0, ',', '.');
        // Asumsi admin ID adalah 1, jika admin ada di tabel users dengan role admin.
        $q_admin = mysqli_query($conn, "SELECT id FROM users WHERE role='admin' LIMIT 1");
        if(mysqli_num_rows($q_admin) > 0){
            $admin_id = mysqli_fetch_assoc($q_admin)['id'];
            mysqli_query($conn, "INSERT INTO notifikasi (id_user, pesan, ikon, link) VALUES ('$admin_id', '$pesan_admin', '💰', 'penarikan.php')");
        }
        
        echo "<script>alert('Pengajuan penarikan berhasil! Dana akan segera ditransfer oleh Admin.'); window.location='dashboard.php';</script>";
    } else {
        echo "<script>alert('Gagal memproses penarikan.'); window.location='dashboard.php';</script>";
    }
}
?>
