<?php
include '../config/auth.php';

if ($_SESSION['role'] != 'mahasiswa') {
    echo "<script>window.location='../logout.php';</script>";
    exit;
}

include '../config/koneksi.php';
$id_mahasiswa = $_SESSION['id'];

$query_jadwal = mysqli_query($conn, "SELECT b.*, u.nama as nama_mentor, u.no_wa, u.foto_profil, m.mata_kuliah, m.tarif 
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
                    if ($row['status'] == 'disetujui') {
                        $badge_color = 'bg-success';
                        $badge_text = '<i class="bi bi-check-circle"></i> Telah Disetujui';
                        $card_class = 'border-start border-success border-4';
                    } else {
                        $badge_color = 'bg-warning text-dark';
                        $badge_text = '<i class="bi bi-hourglass-split"></i> Menunggu Konfirmasi';
                        $card_class = 'border-start border-warning border-4';
                    }
            ?>
                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm p-4 h-100 bg-white <?php echo $card_class; ?>" style="border-radius: 12px;">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div class="d-flex align-items-center">
                                    <?php if (!empty($row['foto_profil'])) { ?>
                                        <img src="../assets/uploads/profil/<?php echo $row['foto_profil']; ?>" class="rounded-circle object-fit-cover shadow-sm" style="width: 50px; height: 50px;">
                                    <?php } else { ?>
                                        <div class="text-white rounded-circle d-flex justify-content-center align-items-center fw-bold shadow-sm" style="width: 50px; height: 50px; font-size: 20px; background-color: #1E3A8A;">
                                            <?php echo strtoupper(substr($row['nama_mentor'], 0, 1)); ?>
                                        </div>
                                    <?php } ?>
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
                                    <span class="fw-bold" style="color: #1E3A8A; font-size: 16px;">Rp <?php echo number_format($row['total_harga'], 0, ',', '.'); ?></span>
                                </div>
                            </div>
                            
                            <?php if ($row['status'] == 'disetujui') { ?>
                                <div class="mt-3 p-3 rounded" style="background-color: #f8fafc; border: 1px solid #e2e8f0;">
                                    <h6 class="fw-bold mb-2" style="font-size: 14px;"><i class="bi bi-info-circle"></i> Informasi Sesi</h6>
                                    <div class="d-flex gap-2 mb-2">
                                        <a href="https://wa.me/<?php echo preg_replace('/^0/', '62', $row['no_wa']); ?>" target="_blank" class="btn btn-sm btn-success fw-bold"><i class="bi bi-whatsapp"></i> Chat Mentor</a>
                                        <?php if (!empty($row['link_meet'])) { ?>
                                            <a href="<?php echo $row['link_meet']; ?>" target="_blank" class="btn btn-sm btn-primary fw-bold"><i class="bi bi-camera-video"></i> Link GMeet</a>
                                        <?php } ?>
                                    </div>
                                    <?php if (!empty($row['catatan_mentor'])) { ?>
                                        <p class="mb-0 text-muted" style="font-size: 13px;"><strong>Catatan:</strong> <?php echo nl2br(htmlspecialchars($row['catatan_mentor'])); ?></p>
                                    <?php } ?>
                                </div>
                            <?php } else if ($row['status'] == 'menunggu') { ?>
                                <div class="mt-3 p-3 rounded" style="background-color: #f8fafc; border: 1px dashed #cbd5e1;">
                                    <?php if (!empty($row['bukti_bayar'])) { ?>
                                        <span class="badge bg-info text-dark px-3 py-2"><i class="bi bi-check-circle"></i> Bukti Bayar Terkirim, Menunggu Konfirmasi</span>
                                    <?php } else { ?>
                                        <form action="upload_bukti.php" method="POST" enctype="multipart/form-data" class="upload-form">
                                            <input type="hidden" name="id_booking" value="<?php echo $row['id_booking']; ?>">
                                            <div class="drag-drop-area border border-2 border-primary rounded p-3 text-center mb-2 position-relative" style="border-style: dashed !important; background-color: #eff6ff; cursor: pointer; transition: 0.3s;" id="drop-zone-<?php echo $row['id_booking']; ?>">
                                                <i class="bi bi-cloud-arrow-up fs-2 text-primary"></i>
                                                <p class="mb-0 text-muted" style="font-size: 13px;">Seret & Lepas foto bukti transfer ke sini, atau klik untuk memilih file</p>
                                                <input type="file" name="bukti" class="file-input position-absolute w-100 h-100 start-0 top-0 opacity-0" required accept="image/*" style="cursor: pointer;" onchange="handleFile(this, '<?php echo $row['id_booking']; ?>')">
                                            </div>
                                            <div id="file-name-<?php echo $row['id_booking']; ?>" class="text-success fw-bold mb-2" style="font-size: 13px;"></div>
                                            <button type="submit" class="btn btn-sm text-white fw-bold w-100" style="background-color: #1E3A8A;">Upload Bukti</button>
                                        </form>
                                    <?php } ?>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
            <?php 
                }
            } else {
                echo "<div class='col-12'>
                        <div class='alert border-0 shadow-sm text-center p-5 bg-white' style='border-radius: 12px;'>
                            <h5 class='fw-bold text-muted mb-3'>Kamu belum memiliki jadwal bimbingan aktif.</h5>
                            <a href='cari_mentor.php' class='btn text-white fw-bold px-4 py-2' style='background-color: #1E3A8A; border-radius: 8px;'><i class='bi bi-search'></i> Cari Mentor Sekarang</a>
                        </div>
                    </div>";
            } 
            ?>
        </div>
    </div>
</div>

<script>
function handleFile(input, id) {
    const fileNameElement = document.getElementById('file-name-' + id);
    const dropZone = document.getElementById('drop-zone-' + id);
    if (input.files && input.files[0]) {
        fileNameElement.innerHTML = '<i class="bi bi-file-earmark-image"></i> ' + input.files[0].name;
        dropZone.style.backgroundColor = '#dbeafe';
    } else {
        fileNameElement.innerHTML = '';
        dropZone.style.backgroundColor = '#eff6ff';
    }
}
</script>

<?php include '../templates/footer.php'; ?>