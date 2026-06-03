<?php
include '../config/auth.php';

if ($_SESSION['role'] != 'mentor') {
    echo "<script>window.location='../logout.php';</script>";
    exit;
}

include '../config/koneksi.php';
$id_user = $_SESSION['id'];

$query_mentor = mysqli_query($conn, "SELECT id_mentor, tarif FROM mentor WHERE id_user='$id_user'");
$mentor_data = mysqli_fetch_assoc($query_mentor);
$id_mentor = $mentor_data['id_mentor'];
$tarif_mentor = $mentor_data['tarif'];

if (isset($_GET['aksi']) && isset($_GET['id_booking'])) {
    $aksi = mysqli_real_escape_string($conn, $_GET['aksi']);
    $id_booking = mysqli_real_escape_string($conn, $_GET['id_booking']);
    
    if ($aksi == 'terima') {
        $status_baru = 'disetujui';
        $pesan = 'Jadwal berhasil disetujui!';
    } elseif ($aksi == 'tolak') {
        $status_baru = 'ditolak';
        $pesan = 'Jadwal ditolak.';
    } elseif ($aksi == 'selesai') {
        $status_baru = 'selesai';
        $pesan = 'Sesi bimbingan telah diselesaikan!';
    }
    
    if(isset($status_baru) && mysqli_query($conn, "UPDATE booking SET status='$status_baru' WHERE id_booking='$id_booking'")) {
        // Notifikasi untuk mahasiswa
        $q_b = mysqli_fetch_assoc(mysqli_query($conn, "SELECT id_mahasiswa FROM booking WHERE id_booking='$id_booking'"));
        $id_mhs = $q_b['id_mahasiswa'];
        $q_mentor_nama = mysqli_fetch_assoc(mysqli_query($conn, "SELECT nama FROM users WHERE id='$id_user'"));
        $pesan_notif = "Mentor (" . $q_mentor_nama['nama'] . ") mengubah status jadwal Anda menjadi: " . $status_baru;
        mysqli_query($conn, "INSERT INTO notifikasi (id_user, pesan, ikon, link) VALUES ('$id_mhs', '$pesan_notif', '✅', 'jadwal.php')");

        echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire('Berhasil!', '$pesan', 'success').then(() => {
                    window.location = 'dashboard.php';
                });
            });
        </script>";
    }
}

$stat_siswa = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(DISTINCT id_mahasiswa) as total FROM booking WHERE id_mentor='$id_mentor' AND status IN ('disetujui', 'selesai')"))['total'];
$stat_sesi = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM booking WHERE id_mentor='$id_mentor' AND status='disetujui'"))['total'];
$stat_selesai = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM booking WHERE id_mentor='$id_mentor' AND status='selesai'"))['total'];

// Hitung Penghasilan (Berdasarkan durasi/total harga yang tersimpan)
$q_penghasilan = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(total_harga) as sum_harga FROM booking WHERE id_mentor='$id_mentor' AND status='selesai'"));
$total_kotor = $q_penghasilan['sum_harga'] ? $q_penghasilan['sum_harga'] : 0;

$q_tarik = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(jumlah) as sum_tarik FROM penarikan WHERE id_mentor='$id_mentor' AND status IN ('menunggu', 'berhasil')"));
$total_ditarik = $q_tarik['sum_tarik'] ? $q_tarik['sum_tarik'] : 0;

$total_penghasilan = $total_kotor - $total_ditarik;

$query_booking = mysqli_query($conn, "SELECT b.id_booking, b.tanggal, b.jam, b.bukti_bayar, u.nama, u.universitas 
                                    FROM booking b 
                                    JOIN users u ON b.id_mahasiswa = u.id 
                                    WHERE b.id_mentor = '$id_mentor' AND b.status = 'menunggu'");
$jumlah_booking = mysqli_num_rows($query_booking);

$query_jadwal = mysqli_query($conn, "SELECT b.id_booking, b.tanggal, b.jam, b.link_meet, b.catatan_mentor, u.nama as nama_mahasiswa 
                                    FROM booking b 
                                    JOIN users u ON b.id_mahasiswa = u.id 
                                    WHERE b.id_mentor = '$id_mentor' AND b.status = 'disetujui'
                                    ORDER BY b.tanggal ASC");

$query_ulasan_mentor = mysqli_query($conn, "SELECT u.rating, u.komentar, mhs.nama as nama_mahasiswa, mhs.foto_profil as foto_mhs 
                                            FROM ulasan u 
                                            JOIN users mhs ON u.id_mahasiswa = mhs.id 
                                            WHERE u.id_mentor = '$id_mentor' 
                                            ORDER BY u.id_ulasan DESC LIMIT 5");

include '../templates/header.php';
?>

<div class="d-flex" style="background-color: #F8FAFC; min-height: 100vh;">
    <?php include '../templates/sidebar-mentor.php'; ?>

    <div class="p-5" style="margin-left: 260px; width: 100%;">

        <div class="card border-0 shadow-sm mb-4 position-relative overflow-hidden" style="background: linear-gradient(135deg, #1E3A8A 0%, #0F172A 100%); border-radius: 16px;">
            <div class="card-body p-5 position-relative" style="z-index: 2;">
                <div class="row align-items-center">
                    <div class="col-md-8 text-white">
                        <h2 class="fw-bold mb-2">Selamat Datang, <?php echo htmlspecialchars($_SESSION['nama'], ENT_QUOTES, 'UTF-8'); ?>!</h2>
                        <p class="mb-0 text-white-50 fs-5">Siap berbagi ilmu hari ini? Jadwal bimbinganmu menanti.</p>
                    </div>
                    <div class="col-md-4 text-md-end mt-3 mt-md-0">
                        <span class="text-white-50 d-block mb-1" style="font-size: 14px;">Total Saldo Bisa Ditarik</span>
                        <h3 class="fw-bold text-success mb-2">Rp <?php echo number_format($total_penghasilan, 0, ',', '.'); ?></h3>
                        <?php if($total_penghasilan > 0): ?>
                            <button class="btn btn-sm text-white fw-bold shadow-sm px-3 py-2" data-bs-toggle="modal" data-bs-target="#tarikModal" style="background-color: #0FA7A0; border-radius: 8px;">
                                <i class="bi bi-cash-stack me-1"></i> Tarik Saldo
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <i class="bi bi-easel-fill position-absolute" style="font-size: 200px; color: rgba(255,255,255,0.05); top: -20px; right: 20px; z-index: 1;"></i>
        </div>
        
        <!-- Modal Tarik Saldo -->
        <div class="modal fade" id="tarikModal" tabindex="-1" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow" style="border-radius: 16px;">
              <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold">Penarikan Saldo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <form action="tarik_saldo.php" method="POST">
                  <div class="modal-body">
                    <div class="alert alert-info border-0" style="border-radius: 12px; font-size: 14px;">
                        <i class="bi bi-info-circle-fill me-2"></i> Saldo maksimal yang dapat ditarik: <b>Rp <?php echo number_format($total_penghasilan, 0, ',', '.'); ?></b>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-muted" style="font-size: 14px;">Nominal Penarikan (Rp)</label>
                        <input type="number" name="jumlah" class="form-control form-control-lg" max="<?php echo $total_penghasilan; ?>" required placeholder="Contoh: 150000" style="border-radius: 10px;">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-muted" style="font-size: 14px;">Metode Pencairan</label>
                        <select name="metode_bank" class="form-select form-select-lg" required style="border-radius: 10px;">
                            <option value="">-- Pilih Bank / E-Wallet --</option>
                            <option value="BCA">Bank BCA</option>
                            <option value="Mandiri">Bank Mandiri</option>
                            <option value="BNI">Bank BNI</option>
                            <option value="BRI">Bank BRI</option>
                            <option value="GoPay">GoPay</option>
                            <option value="OVO">OVO</option>
                            <option value="DANA">DANA</option>
                            <option value="ShopeePay">ShopeePay</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-muted" style="font-size: 14px;">Nomor Rekening / HP & Nama Pemilik</label>
                        <input type="text" name="nomor_rekening" class="form-control" required placeholder="Contoh: 123456789 a.n Eka..." style="border-radius: 10px;">
                    </div>
                  </div>
                  <div class="modal-footer border-top-0 pt-0">
                    <button type="submit" class="btn text-white w-100 py-3 fw-bold" style="background-color: #1E3A8A; border-radius: 12px;">Ajukan Penarikan</button>
                  </div>
              </form>
            </div>
          </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="card border-0 shadow-sm p-4 h-100 position-relative overflow-hidden" style="border-radius: 16px;">
                    <div class="position-absolute" style="top: -15px; right: -15px; font-size: 100px; color: rgba(30,58,138,0.04);">
                        <i class="bi bi-wallet2"></i>
                    </div>
                    <span class="text-muted fw-semibold mb-2" style="font-size: 14px; z-index: 2;">Total Penghasilan</span>
                    <h3 class="fw-bold mb-0" style="color: #1E3A8A; z-index: 2;">Rp <?php echo number_format($total_penghasilan, 0, ',', '.'); ?></h3>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm p-4 h-100 position-relative overflow-hidden" style="border-radius: 16px;">
                    <div class="position-absolute" style="top: -15px; right: -15px; font-size: 100px; color: rgba(30,58,138,0.04);">
                        <i class="bi bi-people-fill"></i>
                    </div>
                    <span class="text-muted fw-semibold mb-2" style="font-size: 14px; z-index: 2;">Total Siswa</span>
                    <h2 class="fw-bold mb-0" style="color: #1E3A8A; z-index: 2;"><?php echo $stat_siswa; ?></h2>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm p-4 h-100 position-relative overflow-hidden" style="border-radius: 16px;">
                    <div class="position-absolute" style="top: -15px; right: -15px; font-size: 100px; color: rgba(30,58,138,0.04);">
                        <i class="bi bi-calendar-event-fill"></i>
                    </div>
                    <span class="text-muted fw-semibold mb-2" style="font-size: 14px; z-index: 2;">Sesi Mendatang</span>
                    <h2 class="fw-bold mb-0" style="color: #1E3A8A; z-index: 2;"><?php echo $stat_sesi; ?></h2>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm p-4 h-100 position-relative overflow-hidden" style="border-radius: 16px;">
                    <div class="position-absolute" style="top: -15px; right: -15px; font-size: 100px; color: rgba(30,58,138,0.04);">
                        <i class="bi bi-check-circle-fill"></i>
                    </div>
                    <span class="text-muted fw-semibold mb-2" style="font-size: 14px; z-index: 2;">Sesi Selesai</span>
                    <h2 class="fw-bold mb-0" style="color: #1E3A8A; z-index: 2;"><?php echo $stat_selesai; ?></h2>
                </div>
            </div>
        </div>

        <div class="row g-4 mt-2">
            <div class="col-md-6">
                <div class="card border-0 shadow-sm p-5" style="border-radius: 16px; min-height: 350px;">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="fw-bold mb-0">Permintaan Booking</h5>
                        <?php if($jumlah_booking > 0): ?>
                            <span class="badge bg-warning text-dark rounded-pill"><?php echo $jumlah_booking; ?> Baru</span>
                        <?php endif; ?>
                    </div>
                    
                    <div class="d-flex flex-column gap-3">
                        <?php 
                        if($jumlah_booking > 0) {
                            while($row = mysqli_fetch_assoc($query_booking)) {
                        ?>
                            <div class="border rounded-3 p-3 shadow-sm bg-white border-start border-warning border-4">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <h6 class="fw-bold mb-0 text-dark"><?php echo $row['nama']; ?></h6>
                                    </div>
                                    <span class="badge bg-warning text-dark"><i class="bi bi-hourglass-split"></i> Menunggu</span>
                                </div>
                                <div class="mb-3 text-muted" style="font-size: 14px;">
                                    <i class="bi bi-calendar"></i> <?php echo date('d M Y', strtotime($row['tanggal'])); ?> &nbsp;
                                    <i class="bi bi-clock"></i> <?php echo $row['jam']; ?>
                                </div>
                                <?php if (!empty($row['bukti_bayar'])) { ?>
                                    <div class="mb-3 text-center">
                                        <a href="../assets/uploads/<?php echo $row['bukti_bayar']; ?>" target="_blank" class="btn btn-sm btn-info text-white w-100 fw-bold"><i class="bi bi-receipt"></i> Lihat Bukti Bayar</a>
                                    </div>
                                <?php } else { ?>
                                    <div class="mb-3">
                                        <span class="badge bg-secondary w-100"><i class="bi bi-hourglass-split"></i> Belum ada pembayaran</span>
                                    </div>
                                <?php } ?>
                                <div class="d-flex gap-2">
                                    <a href="?aksi=terima&id_booking=<?php echo $row['id_booking']; ?>" class="btn btn-sm text-white w-50 fw-bold" style="background-color: #1E3A8A;" onclick="return confirm('Yakin menerima jadwal ini?');">Terima</a>
                                    <a href="?aksi=tolak&id_booking=<?php echo $row['id_booking']; ?>" class="btn btn-sm btn-outline-danger w-50 fw-bold" onclick="return confirm('Yakin menolak jadwal ini?');">Tolak</a>
                                </div>
                            </div>
                        <?php 
                            }
                        } else {
                            echo "<div class='text-center text-muted mt-5 pt-3'>
                                    <i class='bi bi-inbox mb-3 d-block' style='font-size: 40px; color: #cbd5e1;'></i>
                                    <p class='mb-0'>Belum ada permintaan booking baru.</p>
                                </div>";
                        }
                        ?>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                        <div class="card border-0 shadow-sm p-5" style="border-radius: 16px; min-height: 350px;">
                    <h5 class="fw-bold mb-4">Jadwal Aktif</h5>
                    <div class="d-flex flex-column gap-3">
                        <?php 
                        if(mysqli_num_rows($query_jadwal) > 0) {
                            while($jadwal = mysqli_fetch_assoc($query_jadwal)) {
                        ?>
                            <div class="p-3 border rounded-3 shadow-sm bg-white border-start border-success border-4">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h6 class="fw-bold mb-0 text-dark"><?php echo $jadwal['nama_mahasiswa']; ?></h6>
                                    <a href="?aksi=selesai&id_booking=<?php echo $jadwal['id_booking']; ?>" class="btn btn-sm text-white fw-bold shadow-sm" style="background-color: #1E3A8A; border-radius: 8px; font-size: 12px;"><i class="bi bi-check-circle"></i> Tandai Selesai</a>
                                </div>
                                <div class="mt-2 mb-3 text-muted" style="font-size: 14px;">
                                    <i class="bi bi-calendar"></i> <?php echo date('d M Y', strtotime($jadwal['tanggal'])); ?> &nbsp;
                                    <i class="bi bi-clock"></i> <?php echo $jadwal['jam']; ?>
                                </div>
                                
                                <form action="update_sesi.php" method="POST" class="mb-3 border-top pt-2">
                                    <input type="hidden" name="id_booking" value="<?php echo $jadwal['id_booking']; ?>">
                                    <div class="mb-2">
                                        <label class="form-label text-muted" style="font-size: 12px;">Link GMeet / Zoom</label>
                                        <input type="url" name="link_meet" class="form-control form-control-sm" placeholder="https://meet.google.com/..." value="<?php echo htmlspecialchars($jadwal['link_meet'] ?? ''); ?>">
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label text-muted" style="font-size: 12px;">Catatan Tambahan (opsional)</label>
                                        <textarea name="catatan_mentor" class="form-control form-control-sm" rows="2" placeholder="Persiapkan buku catatan..."><?php echo htmlspecialchars($jadwal['catatan_mentor'] ?? ''); ?></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-sm btn-outline-primary w-100" style="font-size: 12px;">Simpan Info Sesi</button>
                                </form>
                            </div>
                        <?php 
                            }
                        } else {
                            echo '<div class="text-center text-muted mt-5 pt-3">
                                    <i class="bi bi-calendar-x mb-3 d-block" style="font-size: 40px; color: #cbd5e1;"></i>
                                    <p class="mb-0">Belum ada jadwal yang disetujui.</p>
                                </div>';
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Row Ulasan Terbaru -->
        <div class="row mt-4 mb-5">
            <div class="col-12">
                <div class="card border-0 shadow-sm p-5" style="border-radius: 16px;">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="fw-bold mb-0">Ulasan & Penilaian Mahasiswa</h5>
                    </div>
                    
                    <div class="row g-4">
                        <?php 
                        if(mysqli_num_rows($query_ulasan_mentor) > 0) {
                            while($ulasan = mysqli_fetch_assoc($query_ulasan_mentor)) {
                                $bintang = str_repeat('&#9733;', $ulasan['rating']) . str_repeat('&#9734;', 5 - $ulasan['rating']);
                        ?>
                            <div class="col-md-6">
                                <div class="border rounded-4 p-4 shadow-sm bg-white h-100 position-relative overflow-hidden">
                                    <div class="position-absolute" style="top: -15px; right: -15px; font-size: 80px; color: rgba(244, 161, 0, 0.05);">
                                        <i class="bi bi-star-fill"></i>
                                    </div>
                                    <div class="d-flex align-items-center mb-3" style="z-index: 2; position: relative;">
                                        <?php if (!empty($ulasan['foto_mhs'])) { ?>
                                            <img src="../assets/uploads/profil/<?php echo $ulasan['foto_mhs']; ?>" class="rounded-circle object-fit-cover me-3 shadow-sm" style="width: 50px; height: 50px;">
                                        <?php } else { ?>
                                            <div class="text-white rounded-circle d-flex justify-content-center align-items-center fw-bold me-3 shadow-sm" style="width: 50px; height: 50px; background-color: #0FA7A0; font-size: 20px;">
                                                <?php echo strtoupper(substr($ulasan['nama_mahasiswa'], 0, 1)); ?>
                                            </div>
                                        <?php } ?>
                                        <div>
                                            <h6 class="fw-bold mb-0 text-dark"><?php echo $ulasan['nama_mahasiswa']; ?></h6>
                                            <div style="color: #F4A100; font-size: 14px;" class="mt-1"><?php echo $bintang; ?></div>
                                        </div>
                                    </div>
                                    <p class="text-muted mb-0" style="font-style: italic; z-index: 2; position: relative;">"<?php echo htmlspecialchars($ulasan['komentar'], ENT_QUOTES, 'UTF-8'); ?>"</p>
                                </div>
                            </div>
                        <?php 
                            }
                        } else {
                            echo '<div class="col-12 text-center text-muted mt-3 mb-3">
                                    <i class="bi bi-star-half mb-3 d-block" style="font-size: 40px; color: #cbd5e1;"></i>
                                    <p class="mb-0">Kamu belum mendapatkan ulasan dari mahasiswa.</p>
                                  </div>';
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<?php include '../templates/footer.php'; ?>