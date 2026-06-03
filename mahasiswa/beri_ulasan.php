<?php
include '../config/auth.php';
if ($_SESSION['role'] != 'mahasiswa') {
    echo "<script>window.location='../logout.php';</script>";
    exit;
}

include '../config/koneksi.php';

if (!isset($_GET['id_booking']) || !isset($_GET['id_mentor'])) {
    echo "<script>window.location='riwayat.php';</script>";
    exit;
}

$id_booking = mysqli_real_escape_string($conn, $_GET['id_booking']);
$id_mentor = mysqli_real_escape_string($conn, $_GET['id_mentor']);
$id_mahasiswa = $_SESSION['id'];

$cek = mysqli_query($conn, "SELECT is_reviewed FROM booking WHERE id_booking='$id_booking' AND id_mahasiswa='$id_mahasiswa' AND status='selesai'");
if (mysqli_num_rows($cek) == 0) {
    die("Data tidak valid atau sesi belum selesai.");
}

$row = mysqli_fetch_assoc($cek);
if ($row['is_reviewed'] == 1) {
    echo "<script>alert('Anda sudah memberikan ulasan untuk sesi ini.'); window.location='riwayat.php';</script>";
    exit;
}

$query_mentor = mysqli_query($conn, "SELECT u.nama, u.foto_profil, m.mata_kuliah FROM mentor m JOIN users u ON m.id_user = u.id WHERE m.id_mentor = '$id_mentor'");
$mentor = mysqli_fetch_assoc($query_mentor);

if (isset($_POST['submit'])) {
    $rating = (int)$_POST['rating'];
    $komentar = mysqli_real_escape_string($conn, $_POST['komentar']);

    $query_insert = "INSERT INTO ulasan (id_booking, id_mentor, id_mahasiswa, rating, komentar) VALUES ('$id_booking', '$id_mentor', '$id_mahasiswa', '$rating', '$komentar')";
    if (mysqli_query($conn, $query_insert)) {
        mysqli_query($conn, "UPDATE booking SET is_reviewed=1 WHERE id_booking='$id_booking'");

        $q_avg = mysqli_query($conn, "SELECT AVG(rating) as avg_rating FROM ulasan WHERE id_mentor='$id_mentor'");
        $avg_data = mysqli_fetch_assoc($q_avg);
        $new_rating = round($avg_data['avg_rating'], 1);
        mysqli_query($conn, "UPDATE mentor SET rating='$new_rating' WHERE id_mentor='$id_mentor'");
        
        $q_mentor_user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT id_user FROM mentor WHERE id_mentor='$id_mentor'"));
        $mentor_user_id = $q_mentor_user['id_user'];
        $q_nama_mhs = mysqli_fetch_assoc(mysqli_query($conn, "SELECT nama FROM users WHERE id='$id_mahasiswa'"));
        
        $pesan_notif = $q_nama_mhs['nama'] . " memberikanmu ulasan " . $rating . " Bintang: \"" . substr($komentar, 0, 30) . "...\"";
        mysqli_query($conn, "INSERT INTO notifikasi (id_user, pesan, ikon, link) VALUES ('$mentor_user_id', '$pesan_notif', '⭐', 'dashboard.php')");
        
        echo "<script>
            alert('Terima kasih! Ulasan berhasil disimpan.');
            window.location='riwayat.php';
        </script>";
    } else {
        echo "<script>alert('Gagal menyimpan ulasan.');</script>";
    }
}

include '../templates/header.php';
?>

<div class="d-flex" style="background-color: #F8FAFC; min-height: 100vh;">
    <?php include '../templates/sidebar-mahasiswa.php'; ?>

    <div class="p-5" style="margin-left: 260px; width: 100%;">
        <a href="riwayat.php" class="text-decoration-none text-muted mb-4 d-inline-block fw-semibold">
            &larr; Kembali ke Riwayat
        </a>

        <div class="row justify-content-center">
            <div class="col-md-7">
                <div class="card border-0 shadow-sm p-5" style="border-radius: 12px;">
                    <div class="text-center mb-4">
                        <?php if (!empty($mentor['foto_profil'])) { ?>
                            <img src="../assets/uploads/profil/<?php echo $mentor['foto_profil']; ?>" class="rounded-circle object-fit-cover shadow-sm mx-auto mb-3" style="width: 70px; height: 70px;">
                        <?php } else { ?>
                            <div class="text-white rounded-circle d-flex justify-content-center align-items-center fw-bold shadow-sm mx-auto mb-3" style="width: 70px; height: 70px; font-size: 24px; background-color: #1E3A8A;">
                                <?php echo strtoupper(substr($mentor['nama'] ?? '?', 0, 1)); ?>
                            </div>
                        <?php } ?>
                        <h4 class="fw-bold mb-1">Beri Ulasan untuk <?php echo htmlspecialchars($mentor['nama'] ?? 'Mentor', ENT_QUOTES, 'UTF-8'); ?></h4>
                        <p class="text-muted">Mata Kuliah: <?php echo htmlspecialchars($mentor['mata_kuliah'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></p>
                    </div>

                    <form action="" method="POST">
                        <div class="mb-4 text-center">
                            <label class="form-label fw-bold d-block mb-3">Seberapa puas kamu dengan bimbingan ini?</label>
                            <div class="rating-css">
                                <style>
                                    .rating-css { display: flex; flex-direction: row-reverse; justify-content: center; position: relative; }
                                    .rating-css input { opacity: 0; position: absolute; pointer-events: none; }
                                    .rating-css label { font-size: 40px; color: #ddd; cursor: pointer; transition: 0.2s; margin: 0 5px; }
                                    .rating-css input:checked ~ label, .rating-css label:hover, .rating-css label:hover ~ label { color: #F4A100; }
                                </style>
                                <input type="radio" id="star5" name="rating" value="5" required />
                                <label for="star5" title="5 Stars">&#9733;</label>
                                <input type="radio" id="star4" name="rating" value="4" />
                                <label for="star4" title="4 Stars">&#9733;</label>
                                <input type="radio" id="star3" name="rating" value="3" />
                                <label for="star3" title="3 Stars">&#9733;</label>
                                <input type="radio" id="star2" name="rating" value="2" />
                                <label for="star2" title="2 Stars">&#9733;</label>
                                <input type="radio" id="star1" name="rating" value="1" />
                                <label for="star1" title="1 Star">&#9733;</label>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Komentar / Ulasan</label>
                            <textarea name="komentar" class="form-control" rows="4" placeholder="Ceritakan pengalaman belajarmu dengan mentor ini..." required style="border-radius: 12px;"></textarea>
                        </div>

                        <button type="submit" name="submit" class="btn w-100 text-white fw-bold py-3 shadow-sm" style="background-color: #1E3A8A; border-radius: 12px; font-size: 16px;">
                            Kirim Ulasan
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../templates/footer.php'; ?>
