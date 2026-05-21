<?php
include '../config/auth.php';
if ($_SESSION['role'] != 'mentor') { echo "<script>window.location='../logout.php';</script>"; exit; }
include '../config/koneksi.php';

$id_user = $_SESSION['id'];
$id_mentor = mysqli_fetch_assoc(mysqli_query($conn, "SELECT id_mentor FROM mentor WHERE id_user='$id_user'"))['id_mentor'];

$query = mysqli_query($conn, "SELECT b.*, u.nama as nama_mahasiswa 
                            FROM booking b 
                            JOIN users u ON b.id_mahasiswa = u.id 
                            WHERE b.id_mentor = '$id_mentor' 
                            ORDER BY b.tanggal DESC");

include '../templates/header.php';
?>

<div class="d-flex" style="background-color: #F8FAFC; min-height: 100vh;">
    <?php include '../templates/sidebar-mentor.php'; ?>
    <div class="p-5" style="margin-left: 260px; width: 100%;">
        <h2 class="fw-bold mb-4">Daftar Semua Jadwal</h2>
        <div class="card border-0 shadow-sm p-4" style="border-radius: 20px;">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>Mahasiswa</th><th>Tanggal</th><th>Jam</th><th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = mysqli_fetch_assoc($query)) { ?>
                    <tr>
                        <td><span class="fw-bold"><?php echo $row['nama_mahasiswa']; ?></span></td>
                        <td><?php echo date('d M Y', strtotime($row['tanggal'])); ?></td>
                        <td><?php echo $row['jam']; ?></td>
                        <td><span class="badge bg-<?php echo ($row['status']=='selesai' ? 'success' : ($row['status']=='disetujui' ? 'info' : 'warning')); ?>"><?php echo $row['status']; ?></span></td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php include '../templates/footer.php'; ?>