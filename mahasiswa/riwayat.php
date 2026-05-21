<?php
include '../config/auth.php';

if ($_SESSION['role'] != 'mahasiswa') {
    echo "<script>window.location='../logout.php';</script>";
    exit;
}

include '../config/koneksi.php';
$id_mahasiswa = $_SESSION['id'];

// Ambil data riwayat yang statusnya 'selesai' atau 'ditolak'
// Diurutkan berdasarkan tanggal terbaru (DESC)
$query_riwayat = mysqli_query($conn, "SELECT b.tanggal, b.jam, b.status, u.nama as nama_mentor, m.mata_kuliah, m.tarif 
                                    FROM booking b 
                                    JOIN mentor m ON b.id_mentor = m.id_mentor 
                                    JOIN users u ON m.id_user = u.id 
                                    WHERE b.id_mahasiswa='$id_mahasiswa' AND b.status IN ('selesai', 'ditolak') 
                                    ORDER BY b.tanggal DESC");

include '../templates/header.php';
?>

<div class="d-flex" style="background-color: #F8FAFC; min-height: 100vh;">
    
    <?php include '../templates/sidebar-mahasiswa.php'; ?>

    <div class="p-5" style="margin-left: 260px; width: 100%;">
        <div class="mb-4">
            <h2 class="fw-bold text-dark">Riwayat Transaksi</h2>
            <p class="text-muted">Catatan semua sesi bimbingan yang telah selesai atau dibatalkan.</p>
        </div>

        <div class="row g-4">
            <?php 
            if(mysqli_num_rows($query_riwayat) > 0) {
                while($row = mysqli_fetch_assoc($query_riwayat)) { 
                    // Logika warna berdasarkan status
                    if ($row['status'] == 'selesai') {
                        $badge_color = 'bg-primary';
                        $badge_text = 'Sesi Selesai';
                        $card_border = 'border-primary';
                    } else {
                        $badge_color = 'bg-danger';
                        $badge_text = 'Ditolak / Batal';
                        $card_border = 'border-danger';
                    }
            ?>
                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm p-4 h-100 <?php echo $card_border; ?>" style="border-radius: 20px; border-left: 5px solid !important;">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="text-white rounded-circle d-flex justify-content-center align-items-center fw-bold shadow-sm" style="width: 50px; height: 50px; font-size: 20px; background-color: #cbd5e1;">
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
                                    <span class="text-muted" style="font-size: 14px;"><?php echo date('d M Y', strtotime($row['tanggal'])); ?></span>
                                    <span class="text-muted" style="font-size: 14px;"><?php echo $row['jam']; ?></span>
                                </div>
                                <div>
                                    <span class="fw-bold text-dark" style="font-size: 16px;">Rp <?php echo number_format($row['tarif'], 0, ',', '.'); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
            <?php 
                }
            } else {
                echo "<div class='col-12'>
                        <div class='alert border-0 shadow-sm text-center p-5' style='background-color: #fff; border-radius: 20px;'>
                            <h5 class='fw-bold text-muted mb-0'>Belum ada riwayat transaksi.</h5>
                        </div>
                    </div>";
            } 
            ?>
        </div>
    </div>
</div>

<?php include '../templates/footer.php'; ?>