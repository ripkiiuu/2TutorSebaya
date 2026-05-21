<?php
include '../config/auth.php';

if ($_SESSION['role'] != 'mahasiswa') {
    echo "<script>window.location='../logout.php';</script>";
    exit;
}

include '../config/koneksi.php';
include '../templates/header.php';

// Mengambil data mentor yang statusnya sudah 'approved' (Disetujui Admin)
$query_mentor = mysqli_query($conn, "SELECT u.nama, u.universitas, m.id_mentor, m.mata_kuliah, m.tarif, m.rating 
                                    FROM users u 
                                    JOIN mentor m ON u.id = m.id_user 
                                    WHERE m.status_verifikasi = 'approved'");
?>

<div class="d-flex" style="background-color: #F8FAFC; min-height: 100vh;">
    
    <?php include '../templates/sidebar-mahasiswa.php'; ?>

    <div class="p-5" style="margin-left: 260px; width: 100%;">
        <div class="mb-4">
            <h2 class="fw-bold text-dark">Cari Mentor Terbaikmu</h2>
            <p class="text-muted">Temukan mentor yang sesuai dengan kebutuhan belajarmu hari ini.</p>
        </div>

        <div class="row g-4">
            <?php 
            if(mysqli_num_rows($query_mentor) > 0) {
                while($row = mysqli_fetch_assoc($query_mentor)) { 
            ?>
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm p-4 h-100" style="border-radius: 20px;">
                            <div class="d-flex align-items-center mb-3">
                                <div class="text-white rounded-circle d-flex justify-content-center align-items-center fw-bold shadow-sm" style="width: 50px; height: 50px; font-size: 20px; background: linear-gradient(135deg, #0FA7A0, #20c997);">
                                    <?php echo strtoupper(substr($row['nama'], 0, 1)); ?>
                                </div>
                                <div class="ms-3">
                                    <h5 class="fw-bold mb-0 text-dark"><?php echo $row['nama']; ?></h5>
                                    <span class="text-muted" style="font-size: 13px;"><i class="bi bi-building"></i> <?php echo $row['universitas']; ?></span>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <span class="badge" style="background-color: #e0f2fe; color: #0284c7; padding: 8px 12px; border-radius: 8px; font-weight: 500;">
                                    <?php echo $row['mata_kuliah']; ?>
                                </span>
                            </div>
                            
                            <div class="d-flex justify-content-between align-items-center mt-auto pt-3 border-top">
                                <div>
                                    <span class="fw-bold" style="color: #0FA7A0; font-size: 18px;">Rp <?php echo number_format($row['tarif'], 0, ',', '.'); ?></span><span class="text-muted" style="font-size: 12px;">/jam</span>
                                </div>
                                <a href="detail_mentor.php?id=<?php echo $row['id_mentor']; ?>" class="btn text-white fw-bold px-3 py-1" style="background-color: #F4A100; border-radius: 8px; font-size: 14px;">Lihat Profil</a>
                            </div>
                        </div>
                    </div>
            <?php 
                }
            } else {
                echo "<div class='col-12'><div class='alert border-0 shadow-sm text-center p-5' style='background-color: #fff; border-radius: 20px;'>
                        <h5 class='fw-bold text-muted mb-0'>Belum ada mentor yang tersedia atau disetujui saat ini.</h5>
                    </div></div>";
            } 
            ?>
        </div>
    </div>
</div>

<?php include '../templates/footer.php'; ?>