<?php
include '../config/auth.php';

if ($_SESSION['role'] != 'admin') {
    echo "<script>window.location='../logout.php';</script>";
    exit;
}

include '../config/koneksi.php';

// Menangkap proses klik tombol Setujui atau Tolak
if (isset($_GET['aksi']) && isset($_GET['id_mentor'])) {
    $aksi = $_GET['aksi'];
    $id_mentor = $_GET['id_mentor'];
    
    if ($aksi == 'setujui') {
        mysqli_query($conn, "UPDATE mentor SET status_verifikasi='approved' WHERE id_mentor='$id_mentor'");
        $pesan = "Mentor berhasil disetujui!";
    } elseif ($aksi == 'tolak') {
        mysqli_query($conn, "UPDATE mentor SET status_verifikasi='rejected' WHERE id_mentor='$id_mentor'");
        $pesan = "Mentor ditolak!";
    }
    
    echo "<script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire('Berhasil!', '$pesan', 'success').then(() => {
                window.location = 'dashboard.php';
            });
        });
    </script>";
}

// Mengambil data mentor yang masih pending
$query_pending = mysqli_query($conn, "SELECT u.nama, u.email, u.universitas, m.mata_kuliah, m.id_mentor 
                                    FROM users u 
                                    JOIN mentor m ON u.id = m.id_user 
                                    WHERE m.status_verifikasi = 'pending'");

// Mengambil total statistik untuk Bento Grid
$total_pengguna = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM users WHERE role != 'admin'"))['total'];
$mentor_aktif = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM mentor WHERE status_verifikasi = 'approved'"))['total'];

include '../templates/header.php';
?>

<div class="d-flex" style="background-color: #F8FAFC; min-height: 100vh;">
    
    <?php include '../templates/sidebar-admin.php'; ?>

    <div class="p-5" style="margin-left: 260px; width: 100%;">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold text-dark">Dashboard Admin</h2>
                <p class="text-muted">Kelola platform TutorSebaya</p>
            </div>
            <a href="#" class="btn text-white fw-bold px-4 py-2" style="background-color: #6f42c1; border-radius: 10px;">Export PDF</a>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="card border-0 text-white p-4 shadow-sm" style="background-color: #4f46e5; border-radius: 20px;">
                    <h2 class="fw-bold mb-0"><?php echo $total_pengguna; ?></h2>
                    <span style="font-size: 14px;">Total Pengguna</span>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 text-white p-4 shadow-sm" style="background-color: #9333ea; border-radius: 20px;">
                    <h2 class="fw-bold mb-0"><?php echo $mentor_aktif; ?></h2>
                    <span style="font-size: 14px;">Mentor Aktif</span>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 text-white p-4 shadow-sm" style="background-color: #0FA7A0; border-radius: 20px;">
                    <h2 class="fw-bold mb-0">Rp 0</h2>
                    <span style="font-size: 14px;">Total Transaksi</span>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 text-white p-4 shadow-sm" style="background-color: #FF5722; border-radius: 20px;">
                    <h2 class="fw-bold mb-0">0</h2>
                    <span style="font-size: 14px;">Sesi Bulan Ini</span>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-md-6">
                <div class="card border-0 shadow-sm p-4" style="border-radius: 20px; min-height: 350px;">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="fw-bold mb-0">Verifikasi Mentor</h5>
                        <span class="badge bg-warning text-dark rounded-pill"><?php echo mysqli_num_rows($query_pending); ?> Pending</span>
                    </div>
                    
                    <div class="d-flex flex-column gap-3">
                        <?php 
                        if(mysqli_num_rows($query_pending) > 0) {
                            while($row = mysqli_fetch_assoc($query_pending)) {
                        ?>
                            <div class="border rounded-4 p-3 shadow-sm">
                                <div class="d-flex align-items-center mb-2">
                                    <div class="text-white rounded-circle d-flex justify-content-center align-items-center fw-bold me-3" style="width: 40px; height: 40px; background-color: #6f42c1;">
                                        <?php echo strtoupper(substr($row['nama'], 0, 1)); ?>
                                    </div>
                                    <div>
                                        <h6 class="fw-bold mb-0"><?php echo $row['nama']; ?></h6>
                                        <small class="text-muted"><?php echo $row['email']; ?> • <?php echo $row['universitas']; ?></small>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <span class="badge bg-light text-primary border"><?php echo $row['mata_kuliah']; ?></span>
                                </div>
                                <div class="d-flex gap-2">
                                    <a href="?aksi=setujui&id_mentor=<?php echo $row['id_mentor']; ?>" class="btn btn-sm text-white w-50 fw-bold" style="background-color: #0FA7A0; border-radius: 8px;">✅ Setujui</a>
                                    <a href="?aksi=tolak&id_mentor=<?php echo $row['id_mentor']; ?>" class="btn btn-sm btn-outline-danger w-50 fw-bold" style="border-radius: 8px;">❌ Tolak</a>
                                </div>
                            </div>
                        <?php 
                            }
                        } else {
                            echo "<p class='text-center text-muted mt-4'>Semua mentor sudah diverifikasi.</p>";
                        }
                        ?>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card border-0 shadow-sm p-4" style="border-radius: 20px; min-height: 350px;">
                    <h5 class="fw-bold mb-4">Transaksi Terbaru</h5>
                    <div class="text-center text-muted mt-5">
                        <p>Belum ada transaksi masuk.</p>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<?php include '../templates/footer.php'; ?>