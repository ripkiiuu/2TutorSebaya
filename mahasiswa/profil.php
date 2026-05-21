<?php
include '../config/auth.php';

if ($_SESSION['role'] != 'mahasiswa') {
    echo "<script>window.location='../logout.php';</script>";
    exit;
}

include '../config/koneksi.php';
$id_user = $_SESSION['id'];

// Ambil data mahasiswa
$query = mysqli_query($conn, "SELECT * FROM users WHERE id = '$id_user'");
$data = mysqli_fetch_assoc($query);

// Proses update profil
if (isset($_POST['update_profil'])) {
    $no_wa = mysqli_real_escape_string($conn, $_POST['no_wa']);
    $universitas = mysqli_real_escape_string($conn, $_POST['universitas']);

    $update = mysqli_query($conn, "UPDATE users SET no_wa='$no_wa', universitas='$universitas' WHERE id='$id_user'");

    if ($update) {
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
    
    <?php include '../templates/sidebar-mahasiswa.php'; ?>

    <div class="p-5" style="margin-left: 260px; width: 100%;">
        <div class="mb-4">
            <h2 class="fw-bold text-dark">Profil Saya</h2>
            <p class="text-muted">Kelola data diri dan informasi akunmu</p>
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
                        <label class="form-label fw-semibold">Asal Universitas</label>
                        <input type="text" name="universitas" class="form-control" value="<?php echo $data['universitas']; ?>" required>
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