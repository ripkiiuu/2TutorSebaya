<?php
include '../config/auth.php';
if ($_SESSION['role'] != 'mentor') { echo "<script>window.location='../logout.php';</script>"; exit; }
include '../config/koneksi.php';

$id_user = $_SESSION['id'];
$id_mentor = mysqli_fetch_assoc(mysqli_query($conn, "SELECT id_mentor FROM mentor WHERE id_user='$id_user'"))['id_mentor'];

// Mengambil daftar unik siswa yang sudah pernah selesai bimbingan
$query_siswa = mysqli_query($conn, "SELECT DISTINCT u.nama, u.email, u.no_wa 
                                    FROM booking b 
                                    JOIN users u ON b.id_mahasiswa = u.id 
                                    WHERE b.id_mentor = '$id_mentor' AND b.status = 'selesai'");

include '../templates/header.php';
?>

<div class="d-flex" style="background-color: #F8FAFC; min-height: 100vh;">
    <?php include '../templates/sidebar-mentor.php'; ?>
    <div class="p-5" style="margin-left: 260px; width: 100%;">
        <h2 class="fw-bold mb-4">Siswa Saya 🎓</h2>
        <div class="card border-0 shadow-sm p-4" style="border-radius: 20px;">
            <table class="table align-middle">
                <thead>
                    <tr><th>Nama Siswa</th><th>Email</th><th>WhatsApp</th></tr>
                </thead>
                <tbody>
                    <?php 
                    if(mysqli_num_rows($query_siswa) > 0) {
                        while($row = mysqli_fetch_assoc($query_siswa)) { ?>
                        <tr>
                            <td><span class="fw-bold"><?php echo $row['nama']; ?></span></td>
                            <td><?php echo $row['email']; ?></td>
                            <td><a href="https://wa.me/<?php echo $row['no_wa']; ?>" target="_blank" class="text-success"><i class="bi bi-whatsapp"></i> <?php echo $row['no_wa']; ?></a></td>
                        </tr>
                    <?php } 
                    } else {
                        echo "<tr><td colspan='3' class='text-center text-muted'>Belum ada siswa yang menyelesaikan bimbingan.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php include '../templates/footer.php'; ?>