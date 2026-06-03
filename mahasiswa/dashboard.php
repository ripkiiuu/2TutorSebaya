<?php
include '../config/auth.php';

if ($_SESSION['role'] != 'mahasiswa') {
    echo "<script>window.location='../logout.php';</script>";
    exit;
}

include '../config/koneksi.php';
$id_mahasiswa = $_SESSION['id'];

$stat_selesai = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM booking WHERE id_mahasiswa='$id_mahasiswa' AND status='selesai'"))['total'];
$stat_mendatang = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM booking WHERE id_mahasiswa='$id_mahasiswa' AND status='disetujui'"))['total'];
$stat_mentor = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(DISTINCT id_mentor) as total FROM booking WHERE id_mahasiswa='$id_mahasiswa' AND status='disetujui'"))['total'];

$query_pengeluaran = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(b.total_harga) as total FROM booking b WHERE b.id_mahasiswa='$id_mahasiswa' AND b.status='selesai'"));
$stat_pengeluaran = $query_pengeluaran['total'] ? $query_pengeluaran['total'] : 0;

$query_jadwal = mysqli_query($conn, "SELECT b.tanggal, b.jam, u.nama as nama_mentor, m.mata_kuliah 
                                    FROM booking b 
                                    JOIN mentor m ON b.id_mentor = m.id_mentor 
                                    JOIN users u ON m.id_user = u.id 
                                    WHERE b.id_mahasiswa='$id_mahasiswa' AND b.status='disetujui' 
                                    ORDER BY b.tanggal ASC");

include '../templates/header.php';
?>

<div class="d-flex" style="background-color: #F8FAFC; min-height: 100vh;">
    
    <?php include '../templates/sidebar-mahasiswa.php'; ?>

    <div class="p-5" style="margin-left: 260px; width: 100%;">

        <div class="card border-0 shadow-sm mb-4 position-relative overflow-hidden" style="background: linear-gradient(135deg, #1E3A8A 0%, #0F172A 100%); border-radius: 16px;">
            <div class="card-body p-5 position-relative" style="z-index: 2;">
                <div class="row align-items-center">
                    <div class="col-md-8 text-white">
                        <h2 class="fw-bold mb-2">Selamat Datang, <?php echo htmlspecialchars($_SESSION['nama'], ENT_QUOTES, 'UTF-8'); ?>!</h2>
                        <p class="mb-0 text-white-50 fs-5">Siap untuk meraih IPK impianmu? Mari mulai belajar hari ini.</p>
                    </div>
                    <div class="col-md-4 text-md-end mt-3 mt-md-0">
                        <a href="cari_mentor.php" class="btn btn-light fw-bold px-4 py-3 shadow-sm text-decoration-none" style="border-radius: 12px; color: #1E3A8A;">
                            <i class="bi bi-search me-2"></i> Cari Mentor Sekarang
                        </a>
                    </div>
                </div>
            </div>
            <i class="bi bi-mortarboard-fill position-absolute" style="font-size: 200px; color: rgba(255,255,255,0.05); top: -40px; right: 20px; z-index: 1;"></i>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="card border-0 bg-white p-4 shadow-sm h-100 position-relative overflow-hidden" style="border-radius: 16px;">
                    <div class="position-absolute" style="top: -15px; right: -15px; font-size: 100px; color: rgba(30,58,138,0.04);">
                        <i class="bi bi-check-circle-fill"></i>
                    </div>
                    <span class="text-muted fw-semibold mb-2" style="font-size: 14px; z-index: 2;">Sesi Selesai</span>
                    <h2 class="fw-bold mb-0" style="color: #1E3A8A; z-index: 2;"><?php echo $stat_selesai; ?></h2>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 bg-white p-4 shadow-sm h-100 position-relative overflow-hidden" style="border-radius: 16px;">
                    <div class="position-absolute" style="top: -15px; right: -15px; font-size: 100px; color: rgba(30,58,138,0.04);">
                        <i class="bi bi-calendar-event-fill"></i>
                    </div>
                    <span class="text-muted fw-semibold mb-2" style="font-size: 14px; z-index: 2;">Sesi Mendatang</span>
                    <h2 class="fw-bold mb-0" style="color: #1E3A8A; z-index: 2;"><?php echo $stat_mendatang; ?></h2>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 bg-white p-4 shadow-sm h-100 position-relative overflow-hidden" style="border-radius: 16px;">
                    <div class="position-absolute" style="top: -15px; right: -15px; font-size: 100px; color: rgba(30,58,138,0.04);">
                        <i class="bi bi-people-fill"></i>
                    </div>
                    <span class="text-muted fw-semibold mb-2" style="font-size: 14px; z-index: 2;">Mentor Saya</span>
                    <h2 class="fw-bold mb-0" style="color: #1E3A8A; z-index: 2;"><?php echo $stat_mentor; ?></h2>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 bg-white p-4 shadow-sm h-100 position-relative overflow-hidden" style="border-radius: 16px;">
                    <div class="position-absolute" style="top: -15px; right: -15px; font-size: 100px; color: rgba(30,58,138,0.04);">
                        <i class="bi bi-wallet-fill"></i>
                    </div>
                    <span class="text-muted fw-semibold mb-2" style="font-size: 14px; z-index: 2;">Total Pengeluaran</span>
                    <h3 class="fw-bold mb-0" style="color: #1E3A8A; z-index: 2;">Rp <?php echo number_format($stat_pengeluaran, 0, ',', '.'); ?></h3>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-md-6">
                <div class="card border-0 shadow-sm p-5" style="border-radius: 16px; min-height: 350px;">
                    <h5 class="fw-bold mb-4">Sesi Mendatang</h5>
                    
                    <div class="d-flex flex-column gap-3">
                        <?php 
                        if(mysqli_num_rows($query_jadwal) > 0) {
                            while($jadwal = mysqli_fetch_assoc($query_jadwal)) {
                        ?>
                            <div class="p-3 border rounded-3 shadow-sm bg-white border-start border-success border-4">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h6 class="fw-bold mb-0 text-dark"><?php echo htmlspecialchars($jadwal['nama_mentor'], ENT_QUOTES, 'UTF-8'); ?></h6>
                                    <span class="badge bg-success rounded-pill text-white" style="font-size: 11px;">Aktif</span>
                                </div>
                                <p class="text-muted small mb-3"><i class="bi bi-book"></i> Mata Kuliah: <b><?php echo htmlspecialchars($jadwal['mata_kuliah'], ENT_QUOTES, 'UTF-8'); ?></b></p>
                                <div class="text-muted" style="font-size: 14px;">
                                    <i class="bi bi-calendar"></i> <?php echo date('d M Y', strtotime($jadwal['tanggal'])); ?> &nbsp;
                                    <i class="bi bi-clock"></i> <?php echo $jadwal['jam']; ?>
                                </div>
                            </div>
                        <?php 
                            }
                        } else {
                            echo '<div class="text-center text-muted mt-5 pt-3">
                                    <i class="bi bi-calendar-x mb-3 d-block" style="font-size: 40px; color: #cbd5e1;"></i>
                                    <p class="mb-0">Belum ada sesi bimbingan yang dijadwalkan.</p>
                                </div>';
                        }
                        ?>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card border-0 shadow-sm p-5" style="border-radius: 16px; min-height: 350px;">
                    <h5 class="fw-bold mb-4">Riwayat Transaksi</h5>
                    <div class="text-center text-muted mt-5 pt-3">
                        <i class="bi bi-clock-history mb-3 d-block" style="font-size: 40px; color: #cbd5e1;"></i>
                        <p class="mb-0">Belum ada riwayat transaksi.</p>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<?php include '../templates/footer.php'; ?>