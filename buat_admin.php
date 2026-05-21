<?php
include 'config/koneksi.php';

// Data Admin
$nama = 'Administrator';
$email = 'admin@tutorsebaya.com';
$password = password_hash('admin123', PASSWORD_DEFAULT); // Passwordnya admin123
$role = 'admin';
$status = 'aktif';

// Cek apakah email sudah ada
$cek = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");

if(mysqli_num_rows($cek) == 0) {
    // Masukkan ke database
    mysqli_query($conn, "INSERT INTO users (nama, email, password, role, status_akun) 
                        VALUES ('$nama', '$email', '$password', '$role', '$status')");
    echo "Akun Admin berhasil dibuat! Silakan hapus file ini demi keamanan.";
} else {
    echo "Akun Admin sudah ada di database.";
}
?>