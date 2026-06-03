<?php
include 'config/koneksi.php';

$res = mysqli_query($conn, "DESCRIBE booking");
while($row = mysqli_fetch_assoc($res)) {
    echo $row['Field'] . " - " . $row['Type'] . "\n";
}
?>
