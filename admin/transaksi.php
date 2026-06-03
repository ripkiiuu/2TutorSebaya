<?php
include '../config/auth.php';
include '../config/koneksi.php';
$query = mysqli_query($conn, "SELECT b.*, m.nama as nama_mahasiswa, n.nama as nama_mentor 
                            FROM booking b 
                            JOIN users m ON b.id_mahasiswa = m.id 
                            JOIN mentor mt ON b.id_mentor = mt.id_mentor 
                            JOIN users n ON mt.id_user = n.id");
include '../templates/header.php';
?>

<div class="d-flex" style="background-color: #F8FAFC; min-height: 100vh;">
    <?php include '../templates/sidebar-admin.php'; ?>

    <div class="p-5" style="margin-left: 260px; width: 100%;">
        <h2 class="fw-bold mb-4">Transaksi</h2>
        <div class="card border-0 shadow-sm p-4" style="border-radius: 20px;">
            <table class="table align-middle">
                <thead><tr><th>Mahasiswa</th><th>Mentor</th><th>Tanggal</th><th>Status</th></tr></thead>
                <tbody>
                    <?php while($row = mysqli_fetch_assoc($query)) { ?>
                    <tr>
                        <td><?= htmlspecialchars($row['nama_mahasiswa'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($row['nama_mentor'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($row['tanggal'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><span class="badge bg-info"><?= htmlspecialchars($row['status'], ENT_QUOTES, 'UTF-8') ?></span></td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php include '../templates/footer.php'; ?>