<?php
include '../config/koneksi.php';
include '../config/auth.php';
include '../templates/header.php';

if (!isset($_GET['id'])) {
    header("Location: pengguna.php");
    exit;
}

$id = (int) $_GET['id'];

$query = mysqli_query($conn, "SELECT * FROM users WHERE id = $id");

if(mysqli_num_rows($query) == 0){
    echo "User tidak ditemukan";
    exit;
}

$data = mysqli_fetch_assoc($query);

$mentor = mysqli_query($conn, "
    SELECT * 
    FROM mentor 
    WHERE id_user = $id
");

$dataMentor = mysqli_fetch_assoc($mentor);
?>

<div class="d-flex">
    <?php include '../templates/sidebar-admin.php'; ?>

    <div class="p-5" style="margin-left:260px; width:100%;">

        <h2 class="fw-bold mb-4">Detail Pengguna</h2>

        <div class="card shadow border-0">
            <div class="card-body p-4">

                <div class="row">

                    <div class="col-md-3 text-center">
                        
                        <?php
                        $foto = !empty($data['foto_profil'])
                            ? "../assets/uploads/profil/" . $data['foto_profil']
                            : "https://ui-avatars.com/api/?name=" . urlencode($data['nama']) . "&background=random";
                        ?>

                        <img src="<?= $foto; ?>"
                            class="rounded-circle border shadow"
                            width="150"
                            height="150"
                            style="object-fit: cover;">

                        <h4 class="mt-3">
                            <?= htmlspecialchars($data['nama']); ?>
                        </h4>

                        <span class="badge bg-primary">
                            <?= ucfirst($data['role']); ?>
                        </span>

                    </div>

                    <div class="col-md-9">

                        <h5 class="fw-bold mb-3">
                            Informasi Akun
                        </h5>

                        <table class="table table-bordered">

                            <tr>
                                <th width="250">ID Pengguna</th>
                                <td><?= $data['id']; ?></td>
                            </tr>

                            <tr>
                                <th>Nama Lengkap</th>
                                <td><?= htmlspecialchars($data['nama']); ?></td>
                            </tr>

                            <tr>
                                <th>Email</th>
                                <td><?= htmlspecialchars($data['email']); ?></td>
                            </tr>

                            <tr>
                                <th>Role</th>
                                <td><?= ucfirst($data['role']); ?></td>
                            </tr>

                            <tr>
                                <th>Status Akun</th>
                                <td>
                                    <span class="badge bg-success">
                                        Aktif
                                    </span>
                                </td>
                            </tr>

                        </table>

                    </div>

                </div>

            </div>
        </div>

        <?php if($data['role'] == 'mentor' && $dataMentor): ?>

        <div class="card shadow border-0 mt-4">
            <div class="card-body">

                <h4 class="fw-bold mb-3">
                    Informasi Mentor
                </h4>

                <table class="table table-bordered">

                    <tr>
                        <th width="250">Mata Kuliah</th>
                        <td><?= htmlspecialchars($dataMentor['mata_kuliah']); ?></td>
                    </tr>

                    <tr>
                        <th>Tarif Sesi</th>
                        <td>
                            Rp <?= number_format($dataMentor['tarif'],0,',','.'); ?>
                        </td>
                    </tr>

                    <tr>
                        <th>Status Verifikasi</th>
                        <td>
                            <?= ucfirst($dataMentor['status_verifikasi']); ?>
                        </td>
                    </tr>

                    <tr>
                        <th>Rating</th>
                        <td>
                            ⭐ <?= $dataMentor['rating']; ?>/5
                        </td>
                    </tr>

                    <tr>
                        <th>Deskripsi</th>
                        <td>
                            <?= nl2br(htmlspecialchars($dataMentor['deskripsi'])); ?>
                        </td>
                    </tr>

                </table>

            </div>
        </div>

        <?php endif; ?>

        <div class="mt-4">

            <a href="pengguna.php"
               class="btn btn-secondary">
               Kembali
            </a>

            <a href="hapusUser.php?id=<?= $data['id']; ?>"
               class="btn btn-danger"
               onclick="return confirm('Yakin ingin menghapus akun ini?')">
               Hapus Akun
            </a>

        </div>

    </div>
</div>

<?php include '../templates/footer.php'; ?>