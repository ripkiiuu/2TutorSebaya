<?php
session_start();
include 'config/koneksi.php';
include 'templates/header.php';

// Jika pengguna sudah login sebelumnya, langsung arahkan ke dashboard masing-masing
if (isset($_SESSION['login'])) {
    if ($_SESSION['role'] == 'admin') {
        header('Location: admin/dashboard.php');
    } else if ($_SESSION['role'] == 'mentor') {
        header('Location: mentor/dashboard.php');
    } else {
        header('Location: mahasiswa/dashboard.php');
    }
    exit;
}

// Menangkap proses submit form login
if (isset($_POST['login'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    // Cari data user berdasarkan email
    $query = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");
    
    // Jika email ditemukan
    if (mysqli_num_rows($query) === 1) {
        $data = mysqli_fetch_assoc($query);
        
        // Cek apakah akun dinonaktifkan oleh admin
        if ($data['status_akun'] == 'inactive') {
            echo "<script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire('Akses Ditolak!', 'Akun Anda telah dinonaktifkan oleh Admin.', 'error');
                });
            </script>";
        } else {
            // Verifikasi password (mencocokkan input dengan hash di database)
            if (password_verify($password, $data['password'])) {
                // Set Session untuk menandakan user berhasil login
                $_SESSION['login'] = true;
                $_SESSION['id'] = $data['id'];
                $_SESSION['nama'] = $data['nama'];
                $_SESSION['role'] = $data['role'];

                // Tampilkan notifikasi sukses lalu arahkan ke dashboard yang sesuai
                $redirect_url = "";
                if ($data['role'] == 'admin') $redirect_url = "admin/dashboard.php";
                else if ($data['role'] == 'mentor') $redirect_url = "mentor/dashboard.php";
                else $redirect_url = "mahasiswa/dashboard.php";

                echo "<script>
                    document.addEventListener('DOMContentLoaded', function() {
                        Swal.fire({
                            title: 'Login Berhasil!',
                            text: 'Selamat datang kembali, " . $data['nama'] . "!',
                            icon: 'success',
                            showConfirmButton: false,
                            timer: 1500
                        }).then(() => {
                            window.location = '$redirect_url';
                        });
                    });
                </script>";
            } else {
                // Jika password salah
                echo "<script>
                    document.addEventListener('DOMContentLoaded', function() {
                        Swal.fire('Gagal Login!', 'Password yang Anda masukkan salah.', 'error');
                    });
                </script>";
            }
        }
    } else {
        // Jika email tidak terdaftar
        echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire('Gagal Login!', 'Email tidak terdaftar.', 'error');
            });
        </script>";
    }
}
?>

<div class="container mt-5 mb-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow-sm border-0" style="border-radius: 15px;">
                <div class="card-body p-5">
                    <div class="text-center mb-4">
                        <h3 class="fw-bold" style="color: #0FA7A0;">Masuk ke Akun</h3>
                        <p class="text-muted">Masukkan kredensial untuk melanjutkan</p>
                    </div>
                    
                    <form action="" method="POST">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Email</label>
                            <input type="email" name="email" class="form-control" placeholder="nama@email.com" required>
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Password</label>
                            <input type="password" name="password" class="form-control" placeholder="Masukkan password" required>
                        </div>
                        <button type="submit" name="login" class="btn w-100 text-white fw-bold py-2 mb-3" style="background-color: #0FA7A0; border-radius: 10px;">Masuk</button>
                    </form>
                    
                    <p class="text-center mb-0">Belum punya akun? <a href="register.php" class="text-decoration-none" style="color: #F4A100; font-weight: bold;">Daftar Sekarang</a></p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'templates/footer.php'; ?>