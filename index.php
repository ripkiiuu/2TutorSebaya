<?php include 'templates/header.php'; ?>


<nav class="navbar navbar-expand-lg navbar-light bg-white py-3 sticky-top shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold fs-4" style="color: #1E3A8A;" href="index.php">
            <i class="bi bi-mortarboard-fill me-2"></i>TutorSebaya
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto align-items-center">
                <li class="nav-item me-4">
                    <a class="nav-link fw-semibold text-dark" href="#features">Keunggulan</a>
                </li>
                <li class="nav-item me-4">
                    <a class="nav-link fw-semibold text-dark" href="login.php">Masuk</a>
                </li>
                <li class="nav-item">
                    <a class="btn btn-custom-primary py-2 px-4" style="padding: 10px 24px !important;" href="register.php">Daftar Sekarang</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<section class="hero-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 mb-5 mb-lg-0">
                <span class="badge bg-white text-primary px-3 py-2 rounded-pill shadow-sm mb-3 border fw-semibold" style="color: #1E3A8A !important; border-color: #bfdbfe !important;">
                    🌟 Platform Peer-Tutoring #1 Mahasiswa
                </span>
                <h1 class="hero-title">Belajar Lebih Mudah Bersama <span>Mentor Sebaya</span></h1>
                <p class="hero-subtitle">Tingkatkan pemahaman mata kuliahmu dengan bimbingan langsung dari mahasiswa berprestasi yang sudah menguasai materinya. Cepat, tepat, dan terjangkau.</p>
                <div class="d-flex flex-wrap gap-3">
                    <a href="register.php" class="btn btn-custom-primary text-decoration-none">Mulai Belajar Sekarang</a>
                    <a href="#features" class="btn btn-custom-outline text-decoration-none">Pelajari Lebih Lanjut</a>
                </div>
                <div class="mt-5 d-flex align-items-center">
                    <div class="d-flex me-3">
                        <img src="https://ui-avatars.com/api/?name=Andi&background=random" class="rounded-circle border border-2 border-white shadow-sm" style="width: 40px; margin-right: -15px;">
                        <img src="https://ui-avatars.com/api/?name=Budi&background=random" class="rounded-circle border border-2 border-white shadow-sm" style="width: 40px; margin-right: -15px;">
                        <img src="https://ui-avatars.com/api/?name=Citra&background=random" class="rounded-circle border border-2 border-white shadow-sm" style="width: 40px;">
                    </div>
                    <div class="text-muted" style="font-size: 14px;">
                        Dipercaya oleh <b>1.000+</b> mahasiswa aktif
                    </div>
                </div>
            </div>
            <div class="col-lg-6 text-center">
                <img src="https://images.unsplash.com/photo-1522202176988-66273c2fd55f?auto=format&fit=crop&w=800&q=80" alt="Students learning together" class="img-fluid hero-image w-100">
            </div>
        </div>
    </div>
</section>

<section class="stats-section">
    <div class="container">
        <div class="row text-center">
            <div class="col-md-4 mb-4 mb-md-0">
                <div class="stat-item">
                    <h3>500+</h3>
                    <p>Sesi Bimbingan Selesai</p>
                </div>
            </div>
            <div class="col-md-4 mb-4 mb-md-0">
                <div class="stat-item">
                    <h3>50+</h3>
                    <p>Mentor Terverifikasi</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-item">
                    <h3>4.9/5</h3>
                    <p>Rata-rata Ulasan</p>
                </div>
            </div>
        </div>
    </div>
</section>

<section id="features" class="features-section">
    <div class="container">
        <div class="text-center">
            <h2 class="section-title">Mengapa Memilih TutorSebaya?</h2>
            <p class="section-subtitle">Kami menyediakan ekosistem belajar yang suportif dan transparan agar kamu bisa mencapai nilai akademik terbaikmu.</p>
        </div>
        
        <div class="row g-4">

            <div class="col-lg-4 col-md-6">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="bi bi-shield-check"></i>
                    </div>
                    <h4 class="feature-title">Mentor Terverifikasi</h4>
                    <p class="feature-desc">Semua mentor telah melalui proses verifikasi ketat oleh admin kami untuk memastikan mereka benar-benar kompeten di bidangnya.</p>
                </div>
            </div>

            <div class="col-lg-4 col-md-6">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="bi bi-wallet2"></i>
                    </div>
                    <h4 class="feature-title">Harga Terjangkau</h4>
                    <p class="feature-desc">Karena mentornya adalah sesama mahasiswa, biaya bimbingan jauh lebih ramah di kantong dibandingkan tempat les konvensional.</p>
                </div>
            </div>

            <div class="col-lg-4 col-md-6">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="bi bi-calendar-event"></i>
                    </div>
                    <h4 class="feature-title">Jadwal Fleksibel</h4>
                    <p class="feature-desc">Atur sendiri waktu belajarmu bersama mentor. Sistem booking kami memudahkan kamu mencocokkan waktu luang yang kamu miliki.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="cta-section">
    <div class="container">
        <h2 class="cta-title">Siap Meningkatkan Nilai IPK-mu?</h2>
        <p class="cta-desc">Bergabunglah dengan ribuan mahasiswa lainnya dan temukan mentor yang paling cocok dengan gaya belajarmu hari ini.</p>
        <a href="register.php" class="btn btn-light px-5 py-3 fw-bold rounded-pill shadow" style="color: #1E3A8A; font-size: 1.1rem;">
            Buat Akun Gratis Sekarang
        </a>
    </div>
</section>

<footer class="footer">
    <div class="container">
        <p class="mb-0">&copy; <?php echo date('Y'); ?> TutorSebaya. All rights reserved. Platform Peer-Tutoring Terpercaya.</p>
    </div>
</footer>

<?php include 'templates/footer.php'; ?>