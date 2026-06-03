<?php
$halaman_sekarang = basename($_SERVER['PHP_SELF']);
?>

<div class="d-flex flex-column flex-shrink-0 p-4 bg-white shadow-sm" style="width: 260px; height: 100vh; position: fixed; z-index: 1000;">
    <a href="dashboard.php" class="d-flex align-items-center mb-1 text-decoration-none">
        <h4 class="fw-bold mb-0" style="color: #1E3A8A;">TutorSebaya</h4>
    </a>
    <span class="text-muted mb-4" style="font-size: 13px;">Dashboard Mahasiswa</span>
    
    <ul class="nav nav-pills flex-column mb-auto gap-2">
        <li class="nav-item">
            <a href="dashboard.php" class="nav-link fw-semibold <?php echo ($halaman_sekarang == 'dashboard.php') ? 'active text-white" style="background-color: #1E3A8A; border-radius: 10px;"' : 'link-dark"'; ?>">
                Beranda
            </a>
        </li>
        <li>
            <a href="jadwal.php" class="nav-link fw-semibold <?php echo ($halaman_sekarang == 'jadwal.php') ? 'active text-white" style="background-color: #1E3A8A; border-radius: 10px;"' : 'link-dark"'; ?>">
                Jadwal Saya
            </a>
        </li>
        <li>
            <a href="riwayat.php" class="nav-link fw-semibold <?php echo ($halaman_sekarang == 'riwayat.php') ? 'active text-white" style="background-color: #1E3A8A; border-radius: 10px;"' : 'link-dark"'; ?>">
                Riwayat
            </a>
        </li>
        <li>
            <a href="chat.php" class="nav-link fw-semibold <?php echo ($halaman_sekarang == 'chat.php') ? 'active text-white" style="background-color: #1E3A8A; border-radius: 10px;"' : 'link-dark"'; ?>">
                Pesan
            </a>
        </li>
        <li>
            <a href="profil.php" class="nav-link fw-semibold <?php echo ($halaman_sekarang == 'profil.php') ? 'active text-white" style="background-color: #1E3A8A; border-radius: 10px;"' : 'link-dark"'; ?>">
                Profil
            </a>
        </li>
    </ul>
    
    <?php include 'notifikasi.php'; ?>
    <hr>
    <a href="../logout.php" class="btn text-danger fw-bold w-100" style="background-color: #fee2e2; border-radius: 10px;">Keluar Akun</a>
</div>