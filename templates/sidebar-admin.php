<?php
$halaman_sekarang = basename($_SERVER['PHP_SELF']);
?>
<div class="d-flex flex-column flex-shrink-0 p-4 bg-white shadow-sm" style="width: 260px; height: 100vh; position: fixed; z-index: 1000;">
    <a href="dashboard.php" class="d-flex align-items-center mb-1 text-decoration-none">
        <h4 class="fw-bold mb-0" style="color: #6f42c1;">AdminPanel</h4>
    </a>
    <span class="text-muted mb-4" style="font-size: 13px;">Sistem TutorSebaya</span>
    
    <ul class="nav nav-pills flex-column mb-auto gap-2">
        <li><a href="dashboard.php" class="nav-link fw-semibold <?php echo ($halaman_sekarang == 'dashboard.php') ? 'active text-white" style="background-color: #6f42c1; border-radius: 10px;"' : 'link-dark"'; ?>">Beranda</a></li>
        <li><a href="pengguna.php" class="nav-link fw-semibold <?php echo ($halaman_sekarang == 'pengguna.php') ? 'active text-white" style="background-color: #6f42c1; border-radius: 10px;"' : 'link-dark"'; ?>">Kelola Pengguna</a></li>
        <li><a href="verifikasi.php" class="nav-link fw-semibold <?php echo ($halaman_sekarang == 'verifikasi.php') ? 'active text-white" style="background-color: #6f42c1; border-radius: 10px;"' : 'link-dark"'; ?>">Verifikasi Mentor</a></li>
        <li><a href="transaksi.php" class="nav-link fw-semibold <?php echo ($halaman_sekarang == 'transaksi.php') ? 'active text-white" style="background-color: #6f42c1; border-radius: 10px;"' : 'link-dark"'; ?>">Transaksi</a></li>
    </ul>
    <hr>
    <a href="../logout.php" class="btn text-danger fw-bold w-100" style="background-color: #fee2e2; border-radius: 10px;">Keluar</a>
</div>