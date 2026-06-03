<?php
include 'config/koneksi.php';
$query = "CREATE TABLE IF NOT EXISTS penarikan (
    id_penarikan INT AUTO_INCREMENT PRIMARY KEY,
    id_mentor INT,
    jumlah INT,
    info_rekening VARCHAR(255),
    status ENUM('menunggu', 'berhasil', 'ditolak') DEFAULT 'menunggu',
    tanggal DATETIME DEFAULT CURRENT_TIMESTAMP
)";
if(mysqli_query($conn, $query)){
    echo "Tabel penarikan berhasil dibuat";
} else {
    echo "Error: " . mysqli_error($conn);
}
?>
