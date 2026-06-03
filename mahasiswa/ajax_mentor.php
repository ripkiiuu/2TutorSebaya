<?php
include '../config/koneksi.php';

$keyword = isset($_GET['q']) ? mysqli_real_escape_string($conn, $_GET['q']) : '';

$query_str = "SELECT u.nama, u.universitas, u.foto_profil, m.id_mentor, m.mata_kuliah, m.tarif, m.rating 
              FROM users u 
              JOIN mentor m ON u.id = m.id_user 
              WHERE m.status_verifikasi = 'approved'";

if (!empty($keyword)) {
    $query_str .= " AND (u.nama LIKE '%$keyword%' OR m.mata_kuliah LIKE '%$keyword%' OR u.universitas LIKE '%$keyword%')";
}

$query_mentor = mysqli_query($conn, $query_str);

if(mysqli_num_rows($query_mentor) > 0) {
    while($row = mysqli_fetch_assoc($query_mentor)) { 
?>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm p-4 h-100" style="border-radius: 12px;">
                <div class="d-flex align-items-center mb-3">
                    <?php if (!empty($row['foto_profil'])) { ?>
                        <img src="../assets/uploads/profil/<?php echo $row['foto_profil']; ?>" class="rounded-circle object-fit-cover shadow-sm" style="width: 50px; height: 50px;">
                    <?php } else { ?>
                        <div class="text-white rounded-circle d-flex justify-content-center align-items-center fw-bold shadow-sm" style="width: 50px; height: 50px; font-size: 20px; background-color: #1E3A8A;">
                            <?php echo strtoupper(substr(htmlspecialchars($row['nama'] ?? '?', ENT_QUOTES, 'UTF-8'), 0, 1)); ?>
                        </div>
                    <?php } ?>
                    <div class="ms-3">
                        <h5 class="fw-bold mb-0 text-dark"><?php echo htmlspecialchars($row['nama'] ?? 'Tanpa Nama', ENT_QUOTES, 'UTF-8'); ?></h5>
                        <span class="text-muted" style="font-size: 13px;"><i class="bi bi-building"></i> <?php echo htmlspecialchars($row['universitas'] ?? 'Universitas Tidak Diketahui', ENT_QUOTES, 'UTF-8'); ?></span>
                    </div>
                </div>
                
                <div class="mb-3">
                    <span class="badge bg-light text-primary border" style="padding: 8px 12px; border-radius: 6px; font-weight: 500;">
                        <i class="bi bi-book"></i> <?php echo htmlspecialchars($row['mata_kuliah'] ?? '-', ENT_QUOTES, 'UTF-8'); ?>
                    </span>
                </div>
                
                <div class="d-flex justify-content-between align-items-center mt-auto pt-3 border-top">
                    <div>
                        <span class="fw-bold" style="color: #1E3A8A; font-size: 18px;">Rp <?php echo number_format($row['tarif'] ?? 0, 0, ',', '.'); ?></span><span class="text-muted" style="font-size: 12px;">/jam</span>
                    </div>
                    <a href="detail_mentor.php?id=<?php echo $row['id_mentor']; ?>" class="btn text-white fw-bold px-3 py-1" style="background-color: #1E3A8A; border-radius: 8px; font-size: 14px;">Lihat Profil</a>
                </div>
            </div>
        </div>
<?php 
    }
} else {
    echo "<div class='col-12'><div class='alert border-0 shadow-sm text-center p-5 bg-white' style='border-radius: 12px;'>
            <h5 class='fw-bold text-muted mb-0'><i class='bi bi-search'></i> Mentor atau mata kuliah tidak ditemukan.</h5>
          </div></div>";
} 
?>
