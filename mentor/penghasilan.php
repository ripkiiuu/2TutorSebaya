<?php
include '../config/auth.php';
if ($_SESSION['role'] != 'mentor') { echo "<script>window.location='../logout.php';</script>"; exit; }
include '../config/koneksi.php';

$id_user = $_SESSION['id'];
$data_mentor = mysqli_fetch_assoc(mysqli_query($conn, "SELECT id_mentor, tarif FROM mentor WHERE id_user='$id_user'"));
$id_mentor = $data_mentor['id_mentor'];
$tarif = $data_mentor['tarif'];

$query = mysqli_query($conn, "SELECT b.tanggal, b.jam, u.nama as nama_mahasiswa 
                            FROM booking b 
                            JOIN users u ON b.id_mahasiswa = u.id 
                            WHERE b.id_mentor = '$id_mentor' AND b.status = 'selesai'");

include '../templates/header.php';
?>

<div class="d-flex" style="background-color: #F8FAFC; min-height: 100vh;">
    <?php include '../templates/sidebar-mentor.php'; ?>
    <div class="p-5" style="margin-left: 260px; width: 100%;">
        <h2 class="fw-bold mb-4">Riwayat Penghasilan</h2>
        <div class="card border-0 shadow-sm p-4" style="border-radius: 20px;">
            <table class="table">
                <thead><tr><th>Tanggal</th><th>Siswa</th><th>Pendapatan</th></tr></thead>
                <tbody>
                    <?php while($row = mysqli_fetch_assoc($query)) { ?>
                    <tr>
                        <td><?php echo date('d M Y', strtotime($row['tanggal'])); ?></td>
                        <td><?php echo $row['nama_mahasiswa']; ?></td>
                        <td class="fw-bold text-success">Rp <?php echo number_format($tarif, 0, ',', '.'); ?></td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php include '../templates/footer.php'; ?>