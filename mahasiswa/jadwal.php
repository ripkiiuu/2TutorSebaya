<?php
include '../config/auth.php';

if ($_SESSION['role'] != 'mahasiswa') {
    echo "<script>window.location='../logout.php';</script>";
    exit;
}

include '../config/koneksi.php';
$id_mahasiswa = $_SESSION['id'];

// Ambil data jadwal yang statusnya 'menunggu' atau 'disetujui'
$query_jadwal = mysqli_query($conn, "SELECT b.tanggal, b.jam, b.status, u.nama as nama_mentor, m.mata_kuliah, m.tarif 
                                    FROM booking b 
                                    JOIN mentor m ON b.id_mentor = m.id_mentor 
                                    JOIN users u ON m.id_user = u.id 
                                    WHERE b.id_mahasiswa='$id_mahasiswa' AND b.status IN ('menunggu', 'disetujui') 
                                    ORDER BY b.tanggal ASC");

include '../templates/header.php';
?>

<div class="d-flex" style="background-color: #F8FAFC; min-height: 100vh;">
    
    <?php include '../templates/sidebar-mahasiswa.php'; ?>

    <div class="p-5" style="margin-left: 260px; width: 100%;">
        <div class="mb-4">
            <h2 class="fw-bold text-dark">Jadwal Saya</h2>
            <p class="text-muted">Pantau jadwal bimbingan belajarmu yang sedang aktif atau menunggu persetujuan.</p>
        </div>

        <div class="row g-4">
            <?php 
            if(mysqli_num_rows($query_jadwal) > 0) {
                while($row = mysqli_fetch_assoc($query_jadwal)) { 
                    // Logika warna berdasarkan status
                    if ($row['status'] == 'disetujui') {
                        $badge_color = 'bg-success';
                        $badge_text = 'Telah Disetujui';
                        $card_bg = '#f0fdf4'; // Warna hijau sangat muda
                    } else {
                        $badge_color = 'bg-warning text-dark';
                        $badge_text = 'Menunggu Konfirmasi';
                        $card_bg = '#fffbeb'; // Warna kuning sangat muda
                    }
            ?>
                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm p-4 h-100" style="border-radius: 20px; background-color: <?php echo $card_bg; ?>;">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="text-white rounded-circle d-flex justify-content-center align-items-center fw-bold shadow-sm" style="width: 50px; height: 50px; font-size: 20px; background: linear-gradient(135deg, #0FA7A0, #20c997);">
                                        <?php echo strtoupper(substr($row['nama_mentor'], 0, 1)); ?>
                                    </div>
                                    <div class="ms-3">
                                        <h5 class="fw-bold mb-0 text-dark"><?php echo $row['nama_mentor']; ?></h5>
                                        <span class="text-muted" style="font-size: 13px;"><i class="bi bi-book"></i> <?php echo $row['mata_kuliah']; ?></span>
                                    </div>
                                </div>
                                <span class="badge <?php echo $badge_color; ?> rounded-pill px-3 py-2 shadow-sm"><?php echo $badge_text; ?></span>
                            </div>
                            
                            <hr class="opacity-25">
                            
                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <div class="d-flex gap-2">
                                    <span class="badge bg-white text-primary border px-3 py-2 rounded-3 shadow-sm" style="font-size: 14px;"><?php echo date('d M Y', strtotime($row['tanggal'])); ?></span>
                                    <span class="badge bg-white text-danger border px-3 py-2 rounded-3 shadow-sm" style="font-size: 14px;"><?php echo $row['jam']; ?></span>
                                </div>
                                <div>
                                    <span class="fw-bold" style="color: #0FA7A0; font-size: 16px;">Rp <?php echo number_format($row['tarif'], 0, ',', '.'); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
            <?php 
                }
            } else {
                echo "<div class='col-12'>
                        <div class='alert border-0 shadow-sm text-center p-5' style='background-color: #fff; border-radius: 20px;'>
                            <h5 class='fw-bold text-muted mb-3'>Kamu belum memiliki jadwal bimbingan aktif.</h5>
                            <a href='cari_mentor.php' class='btn text-white fw-bold px-4 py-2' style='background-color: #F4A100; border-radius: 10px;'>Cari Mentor Sekarang</a>
                        </div>
                    </div>";
            } 
            ?>
        </div>
    </div>
</div>

<?php include '../templates/footer.php'; ?>