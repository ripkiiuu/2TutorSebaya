<?php
include '../config/auth.php';

if ($_SESSION['role'] != 'mahasiswa') {
    echo "<script>window.location='../logout.php';</script>";
    exit;
}

include '../config/koneksi.php';

// Pastikan ada ID mentor di URL
if (!isset($_GET['id'])) {
    echo "<script>window.location='cari_mentor.php';</script>";
    exit;
}

$id_mentor = $_GET['id'];
$id_mahasiswa = $_SESSION['id']; // Mengambil ID user yang sedang login

// Tarik detail data mentor
$query = mysqli_query($conn, "SELECT u.nama, u.universitas, u.no_wa, m.mata_kuliah, m.tarif, m.deskripsi, m.rating 
                              FROM users u 
                              JOIN mentor m ON u.id = m.id_user 
                              WHERE m.id_mentor = '$id_mentor'");
$mentor = mysqli_fetch_assoc($query);

// Jika tombol booking ditekan
if (isset($_POST['booking'])) {
    $tanggal = mysqli_real_escape_string($conn, $_POST['tanggal']);
    $jam     = mysqli_real_escape_string($conn, $_POST['jam']);

    // Masukkan ke tabel booking dengan status default 'menunggu'
    $query_booking = "INSERT INTO booking (id_mahasiswa, id_mentor, tanggal, jam, status) 
                      VALUES ('$id_mahasiswa', '$id_mentor', '$tanggal', '$jam', 'menunggu')";
    
    if (mysqli_query($conn, $query_booking)) {
        echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    title: 'Booking Berhasil Terkirim!',
                    text: 'Silakan tunggu mentor menyetujui jadwalmu.',
                    icon: 'success',
                    confirmButtonColor: '#0FA7A0'
                }).then(() => {
                    window.location = 'dashboard.php';
                });
            });
        </script>";
    }
}

include '../templates/header.php';
?>

<div class="d-flex" style="background-color: #F8FAFC; min-height: 100vh;">
    
    <?php include '../templates/sidebar-mahasiswa.php'; ?>

    <div class="p-5" style="margin-left: 260px; width: 100%;">
        <a href="cari_mentor.php" class="text-decoration-none text-muted mb-4 d-inline-block fw-semibold">
            &larr; Kembali ke Pencarian
        </a>

        <div class="row g-4">
            <div class="col-md-7">
                <div class="card border-0 shadow-sm p-5 h-100" style="border-radius: 20px;">
                    <div class="d-flex align-items-center mb-4">
                        <div class="text-white rounded-circle d-flex justify-content-center align-items-center fw-bold shadow" style="width: 80px; height: 80px; font-size: 30px; background: linear-gradient(135deg, #0FA7A0, #20c997);">
                            <?php echo strtoupper(substr($mentor['nama'], 0, 1)); ?>
                        </div>
                        <div class="ms-4">
                            <h3 class="fw-bold text-dark mb-1"><?php echo $mentor['nama']; ?></h3>
                            <span class="badge" style="background-color: #e0f2fe; color: #0284c7; padding: 8px 12px; border-radius: 8px; font-weight: 500;">
                                <?php echo $mentor['mata_kuliah']; ?>
                            </span>
                        </div>
                    </div>
                    
                    <h6 class="fw-bold text-muted mb-2">Asal Universitas</h6>
                    <p class="mb-4 text-dark fw-semibold"><i class="bi bi-building"></i> <?php echo $mentor['universitas']; ?></p>

                    <h6 class="fw-bold text-muted mb-2">Tentang Mentor</h6>
                    <p class="text-secondary" style="line-height: 1.8;">
                        <?php echo nl2br($mentor['deskripsi'] ? $mentor['deskripsi'] : "Mentor ini belum menuliskan deskripsi pengalamannya."); ?>
                    </p>
                </div>
            </div>

            <div class="col-md-5">
                <div class="card border-0 shadow-sm p-4 h-100" style="border-radius: 20px; background: linear-gradient(180deg, #ffffff, #f0fdfa);">
                    <h5 class="fw-bold text-center mb-4" style="color: #0FA7A0;">Atur Jadwal Bimbingan</h5>
                    
                    <div class="text-center mb-4 pb-3 border-bottom">
                        <span class="text-muted d-block mb-1">Tarif Bimbingan</span>
                        <h2 class="fw-bold mb-0" style="color: #F4A100;">Rp <?php echo number_format($mentor['tarif'], 0, ',', '.'); ?> <span class="text-muted" style="font-size: 14px;">/jam</span></h2>
                    </div>

                    <form action="" method="POST">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Pilih Tanggal</label>
                            <input type="date" name="tanggal" class="form-control" min="<?php echo date('Y-m-d'); ?>" required style="border-radius: 10px;">
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Pilih Waktu</label>
                            <select name="jam" class="form-select" required style="border-radius: 10px;">
                                <option value="" disabled selected>-- Pilih Jam --</option>
                                <option value="08:00 - 09:00">08:00 - 09:00</option>
                                <option value="09:00 - 10:00">09:00 - 10:00</option>
                                <option value="10:00 - 11:00">10:00 - 11:00</option>
                                <option value="13:00 - 14:00">13:00 - 14:00</option>
                                <option value="14:00 - 15:00">14:00 - 15:00</option>
                                <option value="15:00 - 16:00">15:00 - 16:00</option>
                                <option value="19:00 - 20:00">19:00 - 20:00</option>
                                <option value="20:00 - 21:00">20:00 - 21:00</option>
                            </select>
                        </div>
                        <button type="submit" name="booking" class="btn w-100 text-white fw-bold py-3 shadow-sm" style="background-color: #0FA7A0; border-radius: 12px; font-size: 16px;">
                            Ajukan Booking
                        </button>
                    </form>
                </div>
            </div>
        </div>

    </div>
</div>

<?php include '../templates/footer.php'; ?>