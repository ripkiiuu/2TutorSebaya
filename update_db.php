<?php
include 'config/koneksi.php';

// Add total_harga column if not exists
$check = mysqli_query($conn, "SHOW COLUMNS FROM booking LIKE 'total_harga'");
if (mysqli_num_rows($check) == 0) {
    mysqli_query($conn, "ALTER TABLE booking ADD COLUMN total_harga INT DEFAULT 0 AFTER jam");
    echo "Column total_harga added. <br>";
}

// Update old data assuming 1 hour duration
mysqli_query($conn, "UPDATE booking b JOIN mentor m ON b.id_mentor = m.id_mentor SET b.total_harga = m.tarif WHERE b.total_harga = 0");
echo "Data updated. <br>";
?>
