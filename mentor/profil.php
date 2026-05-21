<?php
include '../config/auth.php';

if ($_SESSION['role'] != 'mentor') {
    echo "<script>window.location='../logout.php';</script>";
    exit;
}

include '../config/koneksi.php';
$id_user = $_SESSION['id'];

// Mengambil data mentor dari database (Join tabel users dan mentor)
$query = mysqli_query($conn, "SELECT u.*, m.mata_kuliah, m.tarif, m.deskripsi 
                              FROM users u 
                              JOIN mentor m ON u.id = m.id_user 
                              WHERE u.id = '$id_user'");
$data = mysqli_fetch_assoc($query);

// Proses update profil
if (isset($_POST['update_profil'])) {
    $mata_kuliah = mysqli_real_escape_string($conn, $_POST['mata_kuliah']);
    $tarif       = mysqli_real_escape_string($conn, $_POST['tarif']);
    $deskripsi   = mysqli_real_escape_string($conn, $_POST['deskripsi']);
    $no_wa       = mysqli_real_escape_string($conn, $_POST['no_wa']);

    // Update data di tabel users
    mysqli_query($conn, "UPDATE users SET no_wa='$no_wa' WHERE id='$id_user'");
    
    // Update data di tabel mentor
    $update_mentor = mysqli_query($conn, "UPDATE mentor SET mata_kuliah='$mata_kuliah', tarif='$tarif', deskripsi='$deskripsi' WHERE id_user='$id_user'");

    if ($update_mentor) {
        echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    title: 'Berhasil!',
                    text: 'Profil berhasil diperbarui.',
                    icon: 'success',
                    confirmButtonColor: '#0FA7A0'
                }).then(() => {
                    window.location = 'profil.php';
                });
            });
        </script>";
    }
}

include '../templates/header.php';
?>

<div class="d-flex" style="background-color: #F8FAFC; min-height: 100vh;">
    
    <?php include '../templates/sidebar-mentor.php'; ?>

    <div class="p-5" style="margin-left: 260px; width: 100%;">
        <div class="mb-4">
            <h2 class="fw-bold text-dark">Profil Saya 👤</h2>
            <p class="text-muted">Lengkapi data diri agar mahasiswa bisa menemukanmu</p>
        </div>

        <div class="card border-0 shadow-sm p-4" style="border-radius: 20px;">
            <form action="" method="POST">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Nama Lengkap</label>
                        <input type="text" class="form-control" value="<?php echo $data['nama']; ?>" readonly style="background-color: #e9ecef;">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Email</label>
                        <input type="email" class="form-control" value="<?php echo $data['email']; ?>" readonly style="background-color: #e9ecef;">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Nomor WhatsApp</label>
                        <input type="number" name="no_wa" class="form-control" value="<?php echo $data['no_wa']; ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Tarif per Jam (Rp)</label>
                        <input type="number" name="tarif" class="form-control" value="<?php echo $data['tarif']; ?>" placeholder="Contoh: 50000" required>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label fw-semibold">Mata Kuliah yang Diajarkan</label>
                        <input type="text" name="mata_kuliah" class="form-control" value="<?php echo $data['mata_kuliah']; ?>" placeholder="Contoh: Pemrograman Web, Basis Data" required>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label fw-semibold">Deskripsi / Pengalaman</label>
                        <textarea name="deskripsi" class="form-control" rows="4" placeholder="Ceritakan singkat tentang pengalaman mengajarmu..."><?php echo $data['deskripsi']; ?></textarea>
                    </div>
                    <div class="col-12 mt-4">
                        <button type="submit" name="update_profil" class="btn text-white fw-bold px-4 py-2" style="background-color: #0FA7A0; border-radius: 10px;">Simpan Perubahan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include '../templates/footer.php'; ?>