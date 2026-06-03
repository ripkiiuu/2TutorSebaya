<?php
include '../config/auth.php';
if ($_SESSION['role'] != 'admin') exit;
include '../config/koneksi.php';

if (isset($_GET['aksi']) && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $aksi = $_GET['aksi'];
    
    if ($aksi == 'berhasil') {
        mysqli_query($conn, "UPDATE penarikan SET status='berhasil' WHERE id_penarikan='$id'");
        // Notif mentor
        $q = mysqli_fetch_assoc(mysqli_query($conn, "SELECT p.id_mentor, p.jumlah, u.id as id_user FROM penarikan p JOIN mentor m ON p.id_mentor=m.id_mentor JOIN users u ON m.id_user=u.id WHERE p.id_penarikan='$id'"));
        $id_user = $q['id_user'];
        $jumlah = $q['jumlah'];
        $pesan = "Penarikan sebesar Rp " . number_format($jumlah, 0, ',', '.') . " BERHASIL ditransfer ke rekeningmu!";
        mysqli_query($conn, "INSERT INTO notifikasi (id_user, pesan, ikon, link) VALUES ('$id_user', '$pesan', '💸', 'dashboard.php')");
        
        echo "<script>alert('Status penarikan berhasil diubah menjadi BERHASIL!'); window.location='penarikan.php';</script>";
    } elseif ($aksi == 'ditolak') {
        mysqli_query($conn, "UPDATE penarikan SET status='ditolak' WHERE id_penarikan='$id'");
        // Notif mentor
        $q = mysqli_fetch_assoc(mysqli_query($conn, "SELECT p.id_mentor, p.jumlah, u.id as id_user FROM penarikan p JOIN mentor m ON p.id_mentor=m.id_mentor JOIN users u ON m.id_user=u.id WHERE p.id_penarikan='$id'"));
        $id_user = $q['id_user'];
        $jumlah = $q['jumlah'];
        $pesan = "Penarikan sebesar Rp " . number_format($jumlah, 0, ',', '.') . " DITOLAK. Silakan periksa info rekeningmu atau hubungi Admin.";
        mysqli_query($conn, "INSERT INTO notifikasi (id_user, pesan, ikon, link) VALUES ('$id_user', '$pesan', '❌', 'dashboard.php')");

        echo "<script>alert('Status penarikan berhasil diubah menjadi DITOLAK!'); window.location='penarikan.php';</script>";
    }
}

$query = mysqli_query($conn, "SELECT p.*, u.nama, u.no_wa FROM penarikan p JOIN mentor m ON p.id_mentor = m.id_mentor JOIN users u ON m.id_user = u.id ORDER BY p.id_penarikan DESC");

include '../templates/header.php';
?>

<div class="d-flex" style="background-color: #F8FAFC; min-height: 100vh;">
    <?php include '../templates/sidebar-admin.php'; ?>

    <div class="p-5" style="margin-left: 260px; width: 100%;">
        <div class="mb-4">
            <h2 class="fw-bold text-dark">Kelola Penarikan Saldo 💸</h2>
            <p class="text-muted">Daftar permintaan pencairan dana dari Mentor.</p>
        </div>

        <div class="card border-0 shadow-sm p-4" style="border-radius: 16px;">
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Tanggal</th>
                            <th>Nama Mentor</th>
                            <th>No. WA</th>
                            <th>Nominal</th>
                            <th>Info Rekening</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        if(mysqli_num_rows($query) > 0) {
                            while($row = mysqli_fetch_assoc($query)) {
                                $badge = 'bg-warning text-dark';
                                if($row['status'] == 'berhasil') $badge = 'bg-success text-white';
                                if($row['status'] == 'ditolak') $badge = 'bg-danger text-white';
                        ?>
                        <tr>
                            <td><?php echo date('d M Y H:i', strtotime($row['tanggal'])); ?></td>
                            <td class="fw-bold"><?php echo htmlspecialchars($row['nama']); ?></td>
                            <td><?php echo htmlspecialchars($row['no_wa']); ?></td>
                            <td class="fw-bold text-primary">Rp <?php echo number_format($row['jumlah'], 0, ',', '.'); ?></td>
                            <td><?php echo htmlspecialchars($row['info_rekening']); ?></td>
                            <td><span class="badge <?php echo $badge; ?> px-2 py-1"><?php echo ucfirst($row['status']); ?></span></td>
                            <td>
                                <?php if($row['status'] == 'menunggu') { ?>
                                    <a href="?aksi=berhasil&id=<?php echo $row['id_penarikan']; ?>" class="btn btn-sm btn-success fw-bold me-1" onclick="return confirm('Yakin sudah ditransfer?');"><i class="bi bi-check2"></i> Selesai</a>
                                    <a href="?aksi=ditolak&id=<?php echo $row['id_penarikan']; ?>" class="btn btn-sm btn-danger fw-bold" onclick="return confirm('Yakin menolak penarikan ini?');"><i class="bi bi-x"></i> Tolak</a>
                                <?php } else { ?>
                                    <span class="text-muted"><i class="bi bi-dash"></i></span>
                                <?php } ?>
                            </td>
                        </tr>
                        <?php 
                            }
                        } else {
                            echo "<tr><td colspan='7' class='text-center py-4 text-muted'>Belum ada riwayat penarikan.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include '../templates/footer.php'; ?>
