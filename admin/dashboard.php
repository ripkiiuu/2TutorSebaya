<?php
include '../config/auth.php';

if ($_SESSION['role'] != 'admin') {
    echo "<script>window.location='../logout.php';</script>";
    exit;
}

include '../config/koneksi.php';

if (isset($_GET['aksi']) && isset($_GET['id_mentor'])) {
    $aksi = mysqli_real_escape_string($conn, $_GET['aksi']);
    $id_mentor = mysqli_real_escape_string($conn, $_GET['id_mentor']);
    
    if ($aksi == 'setujui') {
        mysqli_query($conn, "UPDATE mentor SET status_verifikasi='approved' WHERE id_mentor='$id_mentor'");
        $pesan = "Mentor berhasil disetujui!";
    } elseif ($aksi == 'tolak') {
        mysqli_query($conn, "UPDATE mentor SET status_verifikasi='rejected' WHERE id_mentor='$id_mentor'");
        $pesan = "Mentor ditolak!";
    }
    
    echo "<script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire('Berhasil!', '$pesan', 'success').then(() => {
                window.location = 'dashboard.php';
            });
        });
    </script>";
}

$query_pending = mysqli_query($conn, "SELECT u.nama, u.email, u.universitas, u.foto_profil, m.id_mentor, m.mata_kuliah 
                                    FROM mentor m 
                                    JOIN users u ON m.id_user = u.id 
                                    WHERE m.status_verifikasi='pending'");

$query_ulasan = mysqli_query($conn, "SELECT u.*, m.nama as nama_mentor, mhs.nama as nama_mahasiswa, mhs.foto_profil as foto_mhs 
                                    FROM ulasan u 
                                    JOIN mentor mt ON u.id_mentor = mt.id_mentor 
                                    JOIN users m ON mt.id_user = m.id 
                                    JOIN users mhs ON u.id_mahasiswa = mhs.id 
                                    ORDER BY u.id_ulasan DESC LIMIT 5");

$total_pengguna = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM users WHERE role != 'admin'"))['total'];
$mentor_aktif = mysqli_fetch_assoc(
    mysqli_query($conn, "
        SELECT COUNT(*) as total
        FROM mentor m
        INNER JOIN users u ON m.id_user = u.id
        WHERE m.status_verifikasi = 'approved'
    ")
)['total'];
$trx_result = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(total_harga) as summary FROM booking WHERE status='selesai'"));
$total_transaksi = $trx_result['summary'] ? $trx_result['summary'] : 0;
$sesi_bulan_ini = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM booking WHERE MONTH(tanggal) = MONTH(CURRENT_DATE()) AND YEAR(tanggal) = YEAR(CURRENT_DATE())"))['total'];

include '../templates/header.php';
?>

<div class="d-flex" style="background-color: #F8FAFC; min-height: 100vh;">
    
    <?php include '../templates/sidebar-admin.php'; ?>

    <div class="p-5" style="margin-left: 260px; width: 100%;">
        <div class="card border-0 shadow-sm mb-4 position-relative overflow-hidden" style="background: linear-gradient(135deg, #1E3A8A 0%, #0F172A 100%); border-radius: 16px;">
            <div class="card-body p-5 position-relative" style="z-index: 2;">
                <div class="row align-items-center">
                    <div class="col-md-8 text-white">
                        <h2 class="fw-bold mb-2">Dashboard Admin TutorSebaya</h2>
                        <p class="mb-0 text-white-50 fs-5">Pantau dan kelola seluruh aktivitas platform hari ini.</p>
                    </div>
                    <div class="col-md-4 text-md-end mt-3 mt-md-0">
                        <a href="#" class="btn btn-light fw-bold px-4 py-3 shadow-sm text-decoration-none" style="border-radius: 12px; color: #1E3A8A;">
                            <i class="bi bi-file-earmark-pdf me-2"></i> Unduh Laporan
                        </a>
                    </div>
                </div>
            </div>
            <i class="bi bi-shield-lock-fill position-absolute" style="font-size: 200px; color: rgba(255,255,255,0.05); top: -20px; right: 20px; z-index: 1;"></i>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="card border-0 bg-white p-4 shadow-sm h-100 position-relative overflow-hidden" style="border-radius: 16px;">
                    <div class="position-absolute" style="top: -15px; right: -15px; font-size: 100px; color: rgba(30,58,138,0.04);">
                        <i class="bi bi-people-fill"></i>
                    </div>
                    <span class="text-muted fw-semibold mb-2" style="font-size: 14px; z-index: 2;">Total Pengguna</span>
                    <h2 class="fw-bold mb-0" style="color: #1E3A8A; z-index: 2;"><?php echo $total_pengguna; ?></h2>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 bg-white p-4 shadow-sm h-100 position-relative overflow-hidden" style="border-radius: 16px;">
                    <div class="position-absolute" style="top: -15px; right: -15px; font-size: 100px; color: rgba(30,58,138,0.04);">
                        <i class="bi bi-person-badge-fill"></i>
                    </div>
                    <span class="text-muted fw-semibold mb-2" style="font-size: 14px; z-index: 2;">Mentor Aktif</span>
                    <h2 class="fw-bold mb-0" style="color: #1E3A8A; z-index: 2;"><?php echo $mentor_aktif; ?></h2>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 bg-white p-4 shadow-sm h-100 position-relative overflow-hidden" style="border-radius: 16px;">
                    <div class="position-absolute" style="top: -15px; right: -15px; font-size: 100px; color: rgba(30,58,138,0.04);">
                        <i class="bi bi-wallet2"></i>
                    </div>
                    <span class="text-muted fw-semibold mb-2" style="font-size: 14px; z-index: 2;">Total Transaksi</span>
                    <h3 class="fw-bold mb-0" style="color: #1E3A8A; z-index: 2;">Rp <?php echo number_format($total_transaksi, 0, ',', '.'); ?></h3>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 bg-white p-4 shadow-sm h-100 position-relative overflow-hidden" style="border-radius: 16px;">
                    <div class="position-absolute" style="top: -15px; right: -15px; font-size: 100px; color: rgba(30,58,138,0.04);">
                        <i class="bi bi-calendar-check-fill"></i>
                    </div>
                    <span class="text-muted fw-semibold mb-2" style="font-size: 14px; z-index: 2;">Sesi Bulan Ini</span>
                    <h2 class="fw-bold mb-0" style="color: #1E3A8A; z-index: 2;"><?php echo $sesi_bulan_ini; ?></h2>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-md-6">
                <div class="card border-0 shadow-sm p-5" style="border-radius: 16px; min-height: 350px;">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="fw-bold mb-0">Verifikasi Mentor</h5>
                        <span class="badge bg-warning text-dark rounded-pill"><?php echo mysqli_num_rows($query_pending); ?> Pending</span>
                    </div>
                    
                    <div class="d-flex flex-column gap-3">
                        <?php 
                        if(mysqli_num_rows($query_pending) > 0) {
                            while($row = mysqli_fetch_assoc($query_pending)) {
                        ?>
                            <div class="border rounded-4 p-3 shadow-sm">
                                <div class="d-flex align-items-center mb-2">
                                    <?php if (!empty($row['foto_profil'])) { ?>
                                        <img src="../assets/uploads/profil/<?php echo $row['foto_profil']; ?>" class="rounded-circle object-fit-cover me-3" style="width: 40px; height: 40px;">
                                    <?php } else { ?>
                                        <div class="text-white rounded-circle d-flex justify-content-center align-items-center fw-bold me-3" style="width: 40px; height: 40px; background-color: #1E3A8A;">
                                            <?php echo strtoupper(substr($row['nama'], 0, 1)); ?>
                                        </div>
                                    <?php } ?>
                                    <div>
                                        <h6 class="fw-bold mb-0"><?php echo $row['nama']; ?></h6>
                                        <small class="text-muted"><?php echo $row['email']; ?> • <?php echo $row['universitas']; ?></small>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <span class="badge bg-light text-primary border"><?php echo $row['mata_kuliah']; ?></span>
                                </div>
                                <div class="d-flex gap-2">
                                    <a href="?aksi=setujui&id_mentor=<?php echo $row['id_mentor']; ?>" class="btn btn-sm text-white w-50 fw-bold" style="background-color: #1E3A8A; border-radius: 8px;"><i class="bi bi-check-circle"></i> Setujui</a>
                                    <a href="?aksi=tolak&id_mentor=<?php echo $row['id_mentor']; ?>" class="btn btn-sm btn-outline-danger w-50 fw-bold" style="border-radius: 8px;"><i class="bi bi-x-circle"></i> Tolak</a>
                                </div>
                            </div>
                        <?php 
                            }
                        } else {
                            echo "<div class='text-center text-muted mt-5 pt-3'>
                                    <i class='bi bi-shield-check mb-3 d-block' style='font-size: 40px; color: #cbd5e1;'></i>
                                    <p class='mb-0'>Semua mentor sudah diverifikasi.</p>
                                </div>";
                        }
                        ?>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card border-0 shadow-sm p-5" style="border-radius: 16px; min-height: 350px;">
                    <h5 class="fw-bold mb-4">Ulasan Terbaru</h5>
                    <div class="d-flex flex-column gap-3">
                        <?php 
                        if(mysqli_num_rows($query_ulasan) > 0) {
                            while($ulasan = mysqli_fetch_assoc($query_ulasan)) {
                                $bintang = str_repeat('&#9733;', $ulasan['rating']) . str_repeat('&#9734;', 5 - $ulasan['rating']);
                        ?>
                            <div class="border rounded-4 p-3 shadow-sm bg-white border-start border-info border-4">
                                <div class="d-flex align-items-center mb-2">
                                    <?php if (!empty($ulasan['foto_mhs'])) { ?>
                                        <img src="../assets/uploads/profil/<?php echo $ulasan['foto_mhs']; ?>" class="rounded-circle object-fit-cover me-3 shadow-sm" style="width: 40px; height: 40px;">
                                    <?php } else { ?>
                                        <div class="text-white rounded-circle d-flex justify-content-center align-items-center fw-bold me-3 shadow-sm" style="width: 40px; height: 40px; background-color: #0FA7A0;">
                                            <?php echo strtoupper(substr($ulasan['nama_mahasiswa'], 0, 1)); ?>
                                        </div>
                                    <?php } ?>
                                    <div>
                                        <h6 class="fw-bold mb-0 text-dark"><?php echo $ulasan['nama_mahasiswa']; ?></h6>
                                        <small class="text-muted">Untuk Mentor: <b><?php echo $ulasan['nama_mentor']; ?></b></small>
                                    </div>
                                </div>
                                <div style="color: #F4A100; font-size: 14px;" class="mb-1"><?php echo $bintang; ?></div>
                                <p class="text-muted mb-0 small" style="font-style: italic;">"<?php echo htmlspecialchars($ulasan['komentar'], ENT_QUOTES, 'UTF-8'); ?>"</p>
                            </div>
                        <?php 
                            }
                        } else {
                            echo '<div class="text-center text-muted mt-5 pt-3">
                                    <i class="bi bi-star-half mb-3 d-block" style="font-size: 40px; color: #cbd5e1;"></i>
                                    <p class="mb-0">Belum ada ulasan masuk.</p>
                                </div>';
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<?php include '../templates/footer.php'; ?>