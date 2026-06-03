<?php
session_start();
include 'config/koneksi.php';
include 'templates/header.php';

if (isset($_POST['register'])) {
    $nama        = mysqli_real_escape_string($conn, $_POST['nama']);
    $email       = mysqli_real_escape_string($conn, $_POST['email']);
    $no_wa       = mysqli_real_escape_string($conn, $_POST['no_wa']);
    $universitas = mysqli_real_escape_string($conn, $_POST['universitas']);
    $role        = $_POST['role'];

    $password    = password_hash($_POST['password'], PASSWORD_DEFAULT); 

    $cek_email = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");
    if (mysqli_num_rows($cek_email) > 0) {
        echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire('Gagal!', 'Email sudah terdaftar. Gunakan email lain.', 'error');
            });
        </script>";
    } else {
        // Masukkan data ke tabel users
        $query_insert = "INSERT INTO users (nama, email, password, no_wa, universitas, role) 
                        VALUES ('$nama', '$email', '$password', '$no_wa', '$universitas', '$role')";
        
        if (mysqli_query($conn, $query_insert)) {
            $id_user = mysqli_insert_id($conn); 

            if ($role == 'mentor') {
                mysqli_query($conn, "INSERT INTO mentor (id_user, status_verifikasi) VALUES ('$id_user', 'pending')");
            }

            echo "<script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        title: 'Berhasil!',
                        text: 'Akun kamu berhasil dibuat!',
                        icon: 'success',
                        confirmButtonColor: '#1E3A8A'
                    }).then((result) => {
                        window.location = 'login.php';
                    });
                });
            </script>";
        } else {
            echo "<script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire('Error!', 'Terjadi kesalahan sistem.', 'error');
                });
            </script>";
        }
    }
}
?>

<div class="container mt-5 mb-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm border-0" style="border-radius: 15px;">
                <div class="card-body p-5">
                    <h3 class="text-center fw-bold mb-4" style="color: #1E3A8A;">Buat Akun Baru</h3>
                    
                    <form action="" method="POST">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Mendaftar Sebagai</label>
                            <select name="role" class="form-select" required>
                                <option value="mahasiswa">Mahasiswa (Cari Mentor)</option>
                                <option value="mentor">Mentor (Mengajar)</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nama Lengkap</label>
                            <input type="text" name="nama" class="form-control" placeholder="Masukkan nama lengkap" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Email</label>
                            <input type="email" name="email" class="form-control" placeholder="nama@email.com" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nomor WhatsApp</label>
                            <input type="number" name="no_wa" class="form-control" placeholder="08123456789" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Universitas</label>
                            <input type="text" name="universitas" class="form-control" placeholder="Nama Universitas" required>
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Password</label>
                            <input type="password" id="password" name="password" class="form-control" placeholder="Minimal 8 karakter" required minlength="8">
                            <small id="password-feedback" class="text-danger d-none mt-1"><i class="bi bi-exclamation-circle"></i> Password terlalu pendek, minimal 8 karakter.</small>
                        </div>
                        <button type="submit" name="register" class="btn w-100 text-white fw-bold py-2 mb-3" style="background-color: #1E3A8A; border-radius: 10px;">Daftar Sekarang</button>
                    </form>
                    
                    <p class="text-center mb-0">Sudah punya akun? <a href="login.php" class="text-decoration-none" style="color: #1E3A8A; font-weight: bold;">Masuk</a></p>
                </div>
            </div>
        </div>
    </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const passwordInput = document.getElementById('password');
    const passwordFeedback = document.getElementById('password-feedback');

    passwordInput.addEventListener('input', function() {
        if (passwordInput.value.length > 0 && passwordInput.value.length < 8) {
            passwordFeedback.classList.remove('d-none');
            passwordInput.classList.add('is-invalid');
        } else {
            passwordFeedback.classList.add('d-none');
            passwordInput.classList.remove('is-invalid');
            if (passwordInput.value.length >= 8) {
                passwordInput.classList.add('is-valid');
            } else {
                passwordInput.classList.remove('is-valid');
            }
        }
    });
});
</script>

<?php include 'templates/footer.php'; ?>