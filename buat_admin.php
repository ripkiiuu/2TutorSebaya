<?php
include 'config/koneksi.php';

$nama = 'Administrator';
$email = 'admin@tutorsebaya.com';
$password = password_hash('admin123', PASSWORD_DEFAULT); 
$role = 'admin';
$status = 'aktif';

$cek = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");

if(mysqli_num_rows($cek) == 0) {
    mysqli_query($conn, "INSERT INTO users (nama, email, password, role, status_akun) 
                        VALUES ('$nama', '$email', '$password', '$role', '$status')");
    echo "Akun Admin berhasil dibuat! Silakan hapus file ini demi keamanan.";
} else {
    echo "Akun Admin sudah ada di database.";
}
?>