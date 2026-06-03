<?php
include 'config/koneksi.php';
$query = "CREATE TABLE IF NOT EXISTS notifikasi (
    id_notif INT AUTO_INCREMENT PRIMARY KEY,
    id_user INT,
    pesan TEXT,
    ikon VARCHAR(50),
    link VARCHAR(255),
    is_read TINYINT(1) DEFAULT 0,
    waktu DATETIME DEFAULT CURRENT_TIMESTAMP
)";
if(mysqli_query($conn, $query)){
    echo "Tabel notifikasi berhasil dibuat";
} else {
    echo "Error: " . mysqli_error($conn);
}
?>
