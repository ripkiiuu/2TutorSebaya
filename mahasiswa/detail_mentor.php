<?php
include '../config/auth.php';

if ($_SESSION['role'] != 'mahasiswa') {
    echo "<script>window.location='../logout.php';</script>";
    exit;
}

include '../config/koneksi.php';

if (!isset($_GET['id'])) {
    echo "<script>window.location='cari_mentor.php';</script>";
    exit;
}

$id_mentor = mysqli_real_escape_string($conn, $_GET['id']);
$id_mahasiswa = $_SESSION['id'];

$query = mysqli_query($conn, "SELECT u.nama, u.universitas, u.no_wa, u.foto_profil, m.mata_kuliah, m.tarif, m.deskripsi, m.rating 
                            FROM users u 
                            JOIN mentor m ON u.id = m.id_user 
                            WHERE m.id_mentor = '$id_mentor'");
$mentor = mysqli_fetch_assoc($query);

if (isset($_POST['booking'])) {
    $tanggal = mysqli_real_escape_string($conn, $_POST['tanggal']);
    $jam     = mysqli_real_escape_string($conn, $_POST['jam']);
    $durasi  = (int)$_POST['durasi'];
    $total_harga = $durasi * $mentor['tarif'];

    $query_booking = "INSERT INTO booking (id_mahasiswa, id_mentor, tanggal, jam, total_harga, status) 
                    VALUES ('$id_mahasiswa', '$id_mentor', '$tanggal', '$jam', '$total_harga', 'menunggu')";
    
    if (mysqli_query($conn, $query_booking)) {
        // Notifikasi untuk mentor
        $q_mentor_user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT id_user FROM mentor WHERE id_mentor='$id_mentor'"));
        $mentor_user_id = $q_mentor_user['id_user'];
        $pesan_notif = "Mahasiswa mengajukan booking baru pada " . $tanggal . " (" . $durasi . " Jam). Menunggu konfirmasi Anda.";
        mysqli_query($conn, "INSERT INTO notifikasi (id_user, pesan, ikon, link) VALUES ('$mentor_user_id', '$pesan_notif', '📅', 'dashboard.php')");

        echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    title: 'Booking Berhasil Terkirim!',
                    text: 'Silakan tunggu mentor menyetujui jadwalmu.',
                    icon: 'success',
                    confirmButtonColor: '#1E3A8A'
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
                <div class="card border-0 shadow-sm p-5 h-100" style="border-radius: 12px;">
                    <div class="d-flex align-items-center mb-4">
                        <?php if (!empty($mentor['foto_profil'])) { ?>
                            <img src="../assets/uploads/profil/<?php echo $mentor['foto_profil']; ?>" class="rounded-circle object-fit-cover shadow" style="width: 80px; height: 80px;">
                        <?php } else { ?>
                            <div class="text-white rounded-circle d-flex justify-content-center align-items-center fw-bold shadow" style="width: 80px; height: 80px; font-size: 30px; background-color: #1E3A8A;">
                                <?php echo strtoupper(substr($mentor['nama'], 0, 1)); ?>
                            </div>
                        <?php } ?>
                        <div class="ms-4">
                            <h3 class="fw-bold text-dark mb-1"><?php echo $mentor['nama']; ?></h3>
                            <span class="badge bg-light text-primary border" style="padding: 8px 12px; border-radius: 6px; font-weight: 500;">
                                <i class="bi bi-book"></i> <?php echo $mentor['mata_kuliah']; ?>
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
                <div class="card border-0 shadow-sm p-4 h-100 bg-white" style="border-radius: 12px;">
                    <h5 class="fw-bold text-center mb-4" style="color: #1E3A8A;"><i class="bi bi-calendar-check"></i> Atur Jadwal Bimbingan</h5>
                    
                    <div class="text-center mb-4 pb-3 border-bottom">
                        <span class="text-muted d-block mb-1">Total Biaya Bimbingan</span>
                        <h2 class="fw-bold mb-0" style="color: #1E3A8A;" id="total-harga-display">Rp <?php echo number_format($mentor['tarif'], 0, ',', '.'); ?></h2>
                        <input type="hidden" id="tarif-per-jam" value="<?php echo $mentor['tarif']; ?>">
                    </div>

                    <form action="" method="POST">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Pilih Tanggal</label>
                            <input type="date" name="tanggal" class="form-control" min="<?php echo date('Y-m-d'); ?>" required style="border-radius: 10px;">
                        </div>
                        <div class="row mb-4">
                            <div class="col-md-8">
                                <label class="form-label fw-semibold">Jam Mulai</label>
                                <select name="jam_mulai" id="jam_mulai" class="form-select" required style="border-radius: 10px;">
                                    <option value="" disabled selected>-- Pilih --</option>
                                    <option value="08:00">08:00</option>
                                    <option value="09:00">09:00</option>
                                    <option value="10:00">10:00</option>
                                    <option value="11:00">11:00</option>
                                    <option value="13:00">13:00</option>
                                    <option value="14:00">14:00</option>
                                    <option value="15:00">15:00</option>
                                    <option value="19:00">19:00</option>
                                    <option value="20:00">20:00</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Durasi (Jam)</label>
                                <input type="number" name="durasi" id="durasi" class="form-control" value="1" min="1" max="5" required style="border-radius: 10px;">
                            </div>
                        </div>
                        <input type="hidden" name="jam" id="jam_lengkap" value="">
                        <button type="submit" name="booking" class="btn w-100 text-white fw-bold py-3 shadow-sm" style="background-color: #1E3A8A; border-radius: 12px; font-size: 16px;">
                            Ajukan Booking
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <h4 class="fw-bold mt-5 mb-4">Ulasan Mahasiswa</h4>
        <?php
        $query_ulasan = mysqli_query($conn, "SELECT u.rating, u.komentar, user.nama, user.foto_profil FROM ulasan u JOIN users user ON u.id_mahasiswa = user.id WHERE u.id_mentor = '$id_mentor' ORDER BY u.id_ulasan DESC");
        
        if (mysqli_num_rows($query_ulasan) > 0) {
            echo '<div class="row g-3">';
            while($ulasan = mysqli_fetch_assoc($query_ulasan)){
                $bintang = str_repeat('&#9733;', $ulasan['rating']) . str_repeat('&#9734;', 5 - $ulasan['rating']);
                echo '<div class="col-md-6">
                        <div class="card border-0 shadow-sm p-3" style="border-radius: 12px;">
                            <div class="d-flex align-items-center mb-2">';
                if (!empty($ulasan['foto_profil'])) {
                    echo '<img src="../assets/uploads/profil/' . $ulasan['foto_profil'] . '" class="rounded-circle object-fit-cover me-3" style="width: 40px; height: 40px;">';
                } else {
                    echo '<div class="text-white rounded-circle d-flex justify-content-center align-items-center fw-bold me-3" style="width: 40px; height: 40px; background-color: #1E3A8A; font-size: 16px;">
                            ' . strtoupper(substr(htmlspecialchars($ulasan['nama'], ENT_QUOTES, 'UTF-8'), 0, 1)) . '
                        </div>';
                }
                echo '          <div>
                                    <h6 class="fw-bold mb-0 text-dark">' . htmlspecialchars($ulasan['nama'], ENT_QUOTES, 'UTF-8') . '</h6>
                                    <span style="color: #F4A100; font-size: 16px;">' . $bintang . '</span>
                                </div>
                            </div>
                            <p class="text-muted mb-0 pl-2">"' . htmlspecialchars($ulasan['komentar'], ENT_QUOTES, 'UTF-8') . '"</p>
                        </div>
                    </div>';
            }
            echo '</div>';
        } else {
            echo '<p class="text-muted"><i class="bi bi-info-circle"></i> Mentor ini belum memiliki ulasan dari mahasiswa.</p>';
        }
        ?>

    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const durasiInput = document.getElementById('durasi');
    const jamMulaiSelect = document.getElementById('jam_mulai');
    const jamLengkapInput = document.getElementById('jam_lengkap');
    const totalHargaDisplay = document.getElementById('total-harga-display');
    const tarifPerJam = parseInt(document.getElementById('tarif-per-jam').value);

    function hitungTotal() {
        const durasi = parseInt(durasiInput.value) || 1;
        const total = durasi * tarifPerJam;
        totalHargaDisplay.innerHTML = 'Rp ' + new Intl.NumberFormat('id-ID').format(total);
        
        if(jamMulaiSelect.value) {
            const jamAwalStr = jamMulaiSelect.value;
            const jamAwalNum = parseInt(jamAwalStr.split(':')[0]);
            const jamAkhirNum = jamAwalNum + durasi;
            const jamAkhirStr = (jamAkhirNum < 10 ? '0' : '') + jamAkhirNum + ':00';
            jamLengkapInput.value = jamAwalStr + ' - ' + jamAkhirStr;
        }
    }

    durasiInput.addEventListener('input', hitungTotal);
    jamMulaiSelect.addEventListener('change', hitungTotal);
});
</script>

<?php include '../templates/footer.php'; ?>