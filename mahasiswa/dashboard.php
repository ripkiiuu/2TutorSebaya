<?php
include '../config/auth.php';

if ($_SESSION['role'] != 'mahasiswa') {
    echo "<script>window.location='../logout.php';</script>";
    exit;
}

include '../config/koneksi.php';
$id_mahasiswa = $_SESSION['id'];

// 1. Hitung statistik dinamis untuk Bento Grid
$stat_selesai = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM booking WHERE id_mahasiswa='$id_mahasiswa' AND status='selesai'"))['total'];
$stat_mendatang = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM booking WHERE id_mahasiswa='$id_mahasiswa' AND status='disetujui'"))['total'];
$stat_mentor = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(DISTINCT id_mentor) as total FROM booking WHERE id_mahasiswa='$id_mahasiswa' AND status='disetujui'"))['total'];

// 2. Ambil daftar sesi mendatang yang statusnya sudah 'disetujui'
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
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold text-dark">Selamat Datang, <?php echo $_SESSION['nama']; ?>! 👋</h2>
                <p class="text-muted">Semangat belajar hari ini</p>
            </div>
            <a href="cari_mentor.php" class="btn text-white fw-bold px-4 py-2" style="background-color: #F4A100; border-radius: 10px;">Cari Mentor</a>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="card border-0 text-white p-4 shadow-sm" style="background-color: #0FA7A0; border-radius: 20px;">
                    <h1 class="fw-bold mb-0"><?php echo $stat_selesai; ?></h1>
                    <span style="font-size: 14px;">Sesi Selesai</span>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 text-white p-4 shadow-sm" style="background-color: #F4A100; border-radius: 20px;">
                    <h1 class="fw-bold mb-0"><?php echo $stat_mendatang; ?></h1>
                    <span style="font-size: 14px;">Sesi Mendatang</span>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 text-white p-4 shadow-sm" style="background-color: #2563EB; border-radius: 20px;">
                    <h1 class="fw-bold mb-0"><?php echo $stat_mentor; ?></h1>
                    <span style="font-size: 14px;">Mentor Saya</span>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 text-white p-4 shadow-sm" style="background-color: #A855F7; border-radius: 20px;">
                    <h1 class="fw-bold mb-0">5.0</h1>
                    <span style="font-size: 14px;">Rating Rata-rata</span>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-md-6">
                <div class="card border-0 shadow-sm p-4" style="border-radius: 20px; min-height: 350px;">
                    <h5 class="fw-bold mb-4">Sesi Mendatang</h5>
                    
                    <div class="d-flex flex-column gap-3">
                        <?php 
                        if(mysqli_num_rows($query_jadwal) > 0) {
                            while($jadwal = mysqli_fetch_assoc($query_jadwal)) {
                        ?>
                            <div class="p-3 border rounded-4 shadow-sm" style="background-color: #f0fdf4;">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h6 class="fw-bold mb-0 text-dark"><?php echo $jadwal['nama_mentor']; ?></h6>
                                    <span class="badge bg-success rounded-pill text-white" style="font-size: 11px;">Aktif</span>
                                </div>
                                <p class="text-muted small mb-3"><i class="bi bi-book"></i> Mata Kuliah: <b><?php echo $jadwal['mata_kuliah']; ?></b></p>
                                <div class="d-flex gap-2">
                                    <span class="badge bg-white text-primary border px-2 py-1.5 rounded-3"><?php echo date('d M Y', strtotime($jadwal['tanggal'])); ?></span>
                                    <span class="badge bg-white text-danger border px-2 py-1.5 rounded-3"><?php echo $jadwal['jam']; ?></span>
                                </div>
                            </div>
                        <?php 
                            }
                        } else {
                            echo '<div class="text-center text-muted mt-5"><p>Belum ada sesi bimbingan yang dijadwalkan.</p></div>';
                        }
                        ?>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card border-0 shadow-sm p-4" style="border-radius: 20px; min-height: 350px;">
                    <h5 class="fw-bold mb-4">Riwayat Transaksi</h5>
                    <div class="text-center text-muted mt-5">
                        <p>Belum ada riwayat transaksi.</p>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<?php include '../templates/footer.php'; ?>