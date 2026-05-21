<?php
include '../config/auth.php';

if ($_SESSION['role'] != 'mentor') {
    echo "<script>window.location='../logout.php';</script>";
    exit;
}

include '../config/koneksi.php';
$id_user = $_SESSION['id'];

// Ambil ID Mentor & Tarif
$query_mentor = mysqli_query($conn, "SELECT id_mentor, tarif FROM mentor WHERE id_user='$id_user'");
$mentor_data = mysqli_fetch_assoc($query_mentor);
$id_mentor = $mentor_data['id_mentor'];
$tarif_mentor = $mentor_data['tarif'];

// Proses Terima / Tolak / Selesai
if (isset($_GET['aksi']) && isset($_GET['id_booking'])) {
    $aksi = $_GET['aksi'];
    $id_booking = $_GET['id_booking'];
    
    if ($aksi == 'terima') {
        $status_baru = 'disetujui';
        $pesan = 'Jadwal berhasil disetujui!';
    } elseif ($aksi == 'tolak') {
        $status_baru = 'ditolak';
        $pesan = 'Jadwal ditolak.';
    } elseif ($aksi == 'selesai') {
        $status_baru = 'selesai';
        $pesan = 'Sesi bimbingan telah diselesaikan!';
    }
    
    if(isset($status_baru) && mysqli_query($conn, "UPDATE booking SET status='$status_baru' WHERE id_booking='$id_booking'")) {
        echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire('Berhasil!', '$pesan', 'success').then(() => {
                    window.location = 'dashboard.php';
                });
            });
        </script>";
    }
}

// Data Statistik Bento Grid Mentor
$stat_siswa = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(DISTINCT id_mahasiswa) as total FROM booking WHERE id_mentor='$id_mentor' AND status IN ('disetujui', 'selesai')"))['total'];
$stat_sesi = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM booking WHERE id_mentor='$id_mentor' AND status='disetujui'"))['total'];
$stat_selesai = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM booking WHERE id_mentor='$id_mentor' AND status='selesai'"))['total'];

// Hitung Penghasilan (Jumlah sesi selesai dikali tarif mentor)
$total_penghasilan = $stat_selesai * $tarif_mentor;

// Data Booking "Menunggu"
$query_booking = mysqli_query($conn, "SELECT b.id_booking, b.tanggal, b.jam, u.nama, u.universitas 
                                      FROM booking b 
                                      JOIN users u ON b.id_mahasiswa = u.id 
                                      WHERE b.id_mentor = '$id_mentor' AND b.status = 'menunggu'");
$jumlah_booking = mysqli_num_rows($query_booking);

// Data Sesi Mendatang (yang sudah disetujui)
$query_jadwal = mysqli_query($conn, "SELECT b.id_booking, b.tanggal, b.jam, u.nama as nama_mahasiswa 
                                     FROM booking b 
                                     JOIN users u ON b.id_mahasiswa = u.id 
                                     WHERE b.id_mentor = '$id_mentor' AND b.status = 'disetujui'
                                     ORDER BY b.tanggal ASC");

include '../templates/header.php';
?>

<div class="d-flex" style="background-color: #F8FAFC; min-height: 100vh;">
    <?php include '../templates/sidebar-mentor.php'; ?>

    <div class="p-5" style="margin-left: 260px; width: 100%;">
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold text-dark">Selamat Datang, <?php echo $_SESSION['nama']; ?>! 👨‍🏫</h2>
                <p class="text-muted">Semangat mengajar hari ini</p>
            </div>
            <div class="text-end">
                <span class="text-muted d-block" style="font-size: 14px;">Total Saldo Bisa Ditarik</span>
                <h4 class="fw-bold text-success mb-0">Rp <?php echo number_format($total_penghasilan, 0, ',', '.'); ?></h4>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="card border-0 text-white p-4 shadow-sm" style="background-color: #FF5722; border-radius: 20px;">
                    <h3 class="fw-bold mb-0">Rp <?php echo number_format($total_penghasilan, 0, ',', '.'); ?></h3>
                    <span style="font-size: 14px;">Total Penghasilan</span>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 text-white p-4 shadow-sm" style="background-color: #0FA7A0; border-radius: 20px;">
                    <h2 class="fw-bold mb-0"><?php echo $stat_siswa; ?></h2>
                    <span style="font-size: 14px;">Total Siswa</span>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 text-white p-4 shadow-sm" style="background-color: #2563EB; border-radius: 20px;">
                    <h2 class="fw-bold mb-0"><?php echo $stat_sesi; ?></h2>
                    <span style="font-size: 14px;">Sesi Mendatang</span>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 text-white p-4 shadow-sm" style="background-color: #A855F7; border-radius: 20px;">
                    <h2 class="fw-bold mb-0"><?php echo $stat_selesai; ?></h2>
                    <span style="font-size: 14px;">Sesi Selesai</span>
                </div>
            </div>
        </div>

        <div class="row g-4 mt-2">
            <div class="col-md-6">
                <div class="card border-0 shadow-sm p-4" style="border-radius: 20px; min-height: 350px;">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="fw-bold mb-0">Permintaan Booking</h5>
                        <?php if($jumlah_booking > 0): ?>
                            <span class="badge bg-warning text-dark rounded-pill"><?php echo $jumlah_booking; ?> Baru</span>
                        <?php endif; ?>
                    </div>
                    
                    <div class="d-flex flex-column gap-3">
                        <?php 
                        if($jumlah_booking > 0) {
                            while($row = mysqli_fetch_assoc($query_booking)) {
                        ?>
                            <div class="border rounded-4 p-3 shadow-sm" style="background-color: #fffbeb;">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <h6 class="fw-bold mb-0 text-dark"><?php echo $row['nama']; ?></h6>
                                    </div>
                                    <span class="badge" style="background-color: #F4A100; color: white;">Menunggu</span>
                                </div>
                                <div class="mb-3">
                                    <span class="badge bg-white border text-primary px-2 py-1 me-1">📅 <?php echo date('d M Y', strtotime($row['tanggal'])); ?></span>
                                    <span class="badge bg-white border text-danger px-2 py-1">⏰ <?php echo $row['jam']; ?></span>
                                </div>
                                <div class="d-flex gap-2">
                                    <a href="?aksi=terima&id_booking=<?php echo $row['id_booking']; ?>" class="btn btn-sm text-white w-50 fw-bold" style="background-color: #0FA7A0;">Terima</a>
                                    <a href="?aksi=tolak&id_booking=<?php echo $row['id_booking']; ?>" class="btn btn-sm btn-outline-danger w-50 fw-bold">Tolak</a>
                                </div>
                            </div>
                        <?php 
                            }
                        } else {
                            echo "<div class='text-center text-muted mt-5'><p>Belum ada permintaan booking baru.</p></div>";
                        }
                        ?>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card border-0 shadow-sm p-4" style="border-radius: 20px; min-height: 350px;">
                    <h5 class="fw-bold mb-4">Jadwal Aktif</h5>
                    <div class="d-flex flex-column gap-3">
                        <?php 
                        if(mysqli_num_rows($query_jadwal) > 0) {
                            while($jadwal = mysqli_fetch_assoc($query_jadwal)) {
                        ?>
                            <div class="p-3 border rounded-4 shadow-sm" style="background-color: #f0fdf4;">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h6 class="fw-bold mb-0 text-dark"><?php echo $jadwal['nama_mahasiswa']; ?></h6>
                                    <a href="?aksi=selesai&id_booking=<?php echo $jadwal['id_booking']; ?>" class="btn btn-sm text-white fw-bold shadow-sm" style="background-color: #2563EB; border-radius: 8px; font-size: 12px;">✅ Tandai Selesai</a>
                                </div>
                                <div class="d-flex gap-2 mt-2">
                                    <span class="badge bg-white text-primary border px-2 py-1.5 rounded-3">📅 <?php echo date('d M Y', strtotime($jadwal['tanggal'])); ?></span>
                                    <span class="badge bg-white text-danger border px-2 py-1.5 rounded-3">⏰ <?php echo $jadwal['jam']; ?></span>
                                </div>
                            </div>
                        <?php 
                            }
                        } else {
                            echo '<div class="text-center text-muted mt-5"><p>Belum ada jadwal yang disetujui.</p></div>';
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<?php include '../templates/footer.php'; ?>