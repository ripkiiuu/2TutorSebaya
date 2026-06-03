<?php
include 'config/koneksi.php';
session_start();

if (!isset($_SESSION['id'])) {
    die(json_encode(['status' => 'error', 'message' => 'Unauthorized']));
}

$id_user = $_SESSION['id'];
$action = isset($_GET['action']) ? $_GET['action'] : '';

if ($action == 'send') {
    $penerima_id = mysqli_real_escape_string($conn, $_POST['penerima_id']);
    $isi_pesan = mysqli_real_escape_string($conn, $_POST['isi_pesan']);
    
    if (!empty($isi_pesan)) {
        mysqli_query($conn, "INSERT INTO pesan (pengirim_id, penerima_id, isi_pesan) VALUES ('$id_user', '$penerima_id', '$isi_pesan')");
        
        $q_nama_pengirim = mysqli_fetch_assoc(mysqli_query($conn, "SELECT nama FROM users WHERE id='$id_user'"));
        $nama_pengirim = $q_nama_pengirim['nama'];
        $pesan_notif = "Pesan baru dari " . $nama_pengirim . ": " . substr($isi_pesan, 0, 20) . "...";
        mysqli_query($conn, "INSERT INTO notifikasi (id_user, pesan, ikon, link) VALUES ('$penerima_id', '$pesan_notif', '💬', 'chat.php?id=$id_user')");
        
        echo json_encode(['status' => 'success']);
    }
} 
elseif ($action == 'fetch') {
    $lawan_id = mysqli_real_escape_string($conn, $_GET['lawan_id']);
    
    $query = mysqli_query($conn, "SELECT * FROM pesan WHERE 
        (pengirim_id = '$id_user' AND penerima_id = '$lawan_id') OR 
        (pengirim_id = '$lawan_id' AND penerima_id = '$id_user') 
        ORDER BY waktu ASC");
        
    $messages = [];
    while($row = mysqli_fetch_assoc($query)) {
        $messages[] = [
            'is_me' => $row['pengirim_id'] == $id_user,
            'pesan' => htmlspecialchars($row['isi_pesan']),
            'waktu' => date('H:i', strtotime($row['waktu']))
        ];
    }
    echo json_encode($messages);
}
?>
