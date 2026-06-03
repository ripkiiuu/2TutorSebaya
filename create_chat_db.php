<?php
include 'config/koneksi.php';

$query = "CREATE TABLE IF NOT EXISTS pesan (
    id_pesan INT AUTO_INCREMENT PRIMARY KEY,
    pengirim_id INT,
    penerima_id INT,
    isi_pesan TEXT,
    waktu DATETIME DEFAULT CURRENT_TIMESTAMP,
    dibaca TINYINT(1) DEFAULT 0
)";

if(mysqli_query($conn, $query)) {
    echo "Tabel pesan berhasil dibuat!";
} else {
    echo "Error: " . mysqli_error($conn);
}
?>
