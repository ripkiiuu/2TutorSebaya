<?php
include '../config/auth.php';

if ($_SESSION['role'] != 'mahasiswa') {
    echo "<script>window.location='../logout.php';</script>";
    exit;
}

include '../config/koneksi.php';
$id_user = $_SESSION['id'];

$query = mysqli_query($conn, "SELECT * FROM users WHERE id = '$id_user'");
$data = mysqli_fetch_assoc($query);

if (isset($_POST['update_profil'])) {
    $no_wa = mysqli_real_escape_string($conn, $_POST['no_wa']);
    $universitas = mysqli_real_escape_string($conn, $_POST['universitas']);

    $foto_profil = $data['foto_profil'];

    if (isset($_FILES['foto_profil']) && $_FILES['foto_profil']['error'] == 0) {
        $allowed = array('jpg', 'jpeg', 'png', 'gif');
        $filename = $_FILES['foto_profil']['name'];
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        if (in_array(strtolower($ext), $allowed)) {
            $new_filename = 'mhs_' . $id_user . '_' . time() . '.' . $ext;
            $upload_dir = '../assets/uploads/profil/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            if (move_uploaded_file($_FILES['foto_profil']['tmp_name'], $upload_dir . $new_filename)) {
                $foto_profil = $new_filename;
            }
        }
    }

    $update = mysqli_query($conn, "UPDATE users SET no_wa='$no_wa', universitas='$universitas', foto_profil='$foto_profil' WHERE id='$id_user'");

    if ($update) {
        echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    title: 'Berhasil!',
                    text: 'Profil berhasil diperbarui.',
                    icon: 'success',
                    confirmButtonColor: '#1E3A8A'
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

        <div class="card border-0 shadow-sm p-4" style="border-radius: 12px;">
            <div class="text-center mb-4">
                <?php if(!empty($data['foto_profil'])) { ?>
                    <a href="#" data-bs-toggle="modal" data-bs-target="#photoModal">
                        <img src="../assets/uploads/profil/<?php echo $data['foto_profil']; ?>" class="rounded-circle object-fit-cover shadow-sm" style="width: 120px; height: 120px; border: 3px solid #1E3A8A; transition: 0.3s;" onmouseover="this.style.opacity='0.8'" onmouseout="this.style.opacity='1'">
                    </a>
                <?php } else { ?>
                    <div class="text-white rounded-circle d-flex justify-content-center align-items-center fw-bold shadow-sm mx-auto" style="width: 120px; height: 120px; font-size: 40px; background-color: #1E3A8A; border: 3px solid #e2e8f0;">
                        <?php echo strtoupper(substr($data['nama'], 0, 1)); ?>
                    </div>
                <?php } ?>
                <div class="mt-2 text-muted" style="font-size: 13px;">Klik foto untuk memperbesar</div>
            </div>
            
            <!-- Modal Foto -->
            <div class="modal fade" id="photoModal" tabindex="-1" aria-hidden="true">
              <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content bg-transparent border-0">
                  <div class="modal-header border-0">
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <div class="modal-body text-center">
                    <img src="../assets/uploads/profil/<?php echo $data['foto_profil']; ?>" class="img-fluid rounded shadow-lg" style="max-height: 80vh;">
                  </div>
                </div>
              </div>
            </div>

            <form action="" method="POST" enctype="multipart/form-data">
                <div class="row g-3">
                    <div class="col-12 mb-3">
                        <label class="form-label fw-semibold">Ubah Foto Profil</label>
                        <div class="drag-drop-area border border-2 border-primary rounded p-4 text-center position-relative" style="border-style: dashed !important; background-color: #eff6ff; cursor: pointer; transition: 0.3s;" id="drop-zone-profil">
                            <i class="bi bi-camera fs-2 text-primary"></i>
                            <p class="mb-0 text-muted mt-2" style="font-size: 14px;">Seret & Lepas foto profil baru ke sini, atau klik untuk memilih file</p>
                            <input type="file" name="foto_profil" class="file-input position-absolute w-100 h-100 start-0 top-0 opacity-0" accept="image/*" style="cursor: pointer;" onchange="handleFileProfil(this)">
                        </div>
                        <div id="file-name-profil" class="text-success fw-bold mt-2 text-center" style="font-size: 13px;"></div>
                    </div>
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
                        <button type="submit" name="update_profil" class="btn text-white fw-bold px-4 py-2" style="background-color: #1E3A8A; border-radius: 10px;">Simpan Perubahan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function handleFileProfil(input) {
    const fileNameElement = document.getElementById('file-name-profil');
    const dropZone = document.getElementById('drop-zone-profil');
    if (input.files && input.files[0]) {
        fileNameElement.innerHTML = '<i class="bi bi-check-circle-fill"></i> File siap diupload: ' + input.files[0].name;
        dropZone.style.backgroundColor = '#dbeafe';
    } else {
        fileNameElement.innerHTML = '';
        dropZone.style.backgroundColor = '#eff6ff';
    }
}
</script>

<?php include '../templates/footer.php'; ?>