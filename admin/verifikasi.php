<?php
include '../config/auth.php';
include '../config/koneksi.php';

if(isset($_GET['approve'])) {
    $id = $_GET['approve'];
    mysqli_query($conn, "UPDATE mentor SET status_verifikasi='aktif' WHERE id_mentor='$id'");
    echo "<script>window.location='verifikasi.php';</script>";
}

$query = mysqli_query($conn, "SELECT m.*, u.nama FROM mentor m JOIN users u ON m.id_user = u.id WHERE m.status_verifikasi='pending'");

include '../templates/header.php';
?>

<div class="d-flex" style="background-color: #F8FAFC; min-height: 100vh;">
    <?php include '../templates/sidebar-admin.php'; ?>

    <div class="p-5" style="margin-left: 260px; width: 100%;">
        <h2 class="fw-bold mb-4">Verifikasi Mentor</h2>
        <div class="card border-0 shadow-sm p-4" style="border-radius: 20px;">
            <table class="table align-middle">
                <thead><tr><th>Nama Mentor</th><th>Mata Kuliah</th><th>Aksi</th></tr></thead>
                <tbody>
                    <?php while($row = mysqli_fetch_assoc($query)) { ?>
                    <tr>
                        <td><?= $row['nama'] ?></td>
                        <td><?= $row['mata_kuliah'] ?></td>
                        <td>
                            <a href="?approve=<?= $row['id_mentor'] ?>" class="btn btn-sm btn-success">Verifikasi</a>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php include '../templates/footer.php'; ?>