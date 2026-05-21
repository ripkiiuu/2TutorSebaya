<?php
session_start();
include 'config/koneksi.php';
include 'templates/header.php';

// Menangkap proses submit form pendaftaran
if (isset($_POST['register'])) {
    $nama        = mysqli_real_escape_string($conn, $_POST['nama']);
    $email       = mysqli_real_escape_string($conn, $_POST['email']);
    $no_wa       = mysqli_real_escape_string($conn, $_POST['no_wa']);
    $universitas = mysqli_real_escape_string($conn, $_POST['universitas']);
    $role        = $_POST['role'];
    
    // Enkripsi password modern
    $password    = password_hash($_POST['password'], PASSWORD_DEFAULT); 

    // Cek apakah email sudah terdaftar sebelumnya
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
            $id_user = mysqli_insert_id($conn); // Mengambil ID user yang baru saja terbuat

            // Jika yang mendaftar adalah mentor, buatkan wadah datanya di tabel mentor (status pending)
            if ($role == 'mentor') {
                mysqli_query($conn, "INSERT INTO mentor (id_user, status_verifikasi) VALUES ('$id_user', 'pending')");
            }

            // Notifikasi sukses menggunakan SweetAlert
            echo "<script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        title: 'Berhasil!',
                        text: 'Akun kamu berhasil dibuat!',
                        icon: 'success',
                        confirmButtonColor: '#0FA7A0'
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
                    <h3 class="text-center fw-bold mb-4" style="color: #0FA7A0;">Buat Akun Baru</h3>
                    
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
                            <input type="password" name="password" class="form-control" placeholder="Minimal 8 karakter" required minlength="8">
                        </div>
                        <button type="submit" name="register" class="btn w-100 text-white fw-bold py-2 mb-3" style="background-color: #F4A100; border-radius: 10px;">Daftar Sekarang</button>
                    </form>
                    
                    <p class="text-center mb-0">Sudah punya akun? <a href="login.php" class="text-decoration-none" style="color: #0FA7A0; font-weight: bold;">Masuk</a></p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'templates/footer.php'; ?>