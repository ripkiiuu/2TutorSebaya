<?php
include '../config/koneksi.php';
include '../config/auth.php'; 
include '../templates/header.php';
?>

<div class="d-flex">
    <?php include '../templates/sidebar-admin.php'; ?>
    
    <div class="p-5" style="margin-left: 260px; width: 100%;">
        <h2 class="fw-bold mb-4">Kelola Pengguna</h2>

        <div class="mb-4">
            <input type="text" id="searchInput" class="form-control" placeholder="Cari nama atau email di sini..." onkeyup="searchTable()">
        </div>

        <h5 class="fw-bold mt-4">Daftar Mahasiswa</h5>
        <table class="table bg-white shadow-sm mb-5">
            <thead>
            <tr>
                <th style="width: 40%;">Nama</th>
                <th style="width: 40%;">Email</th>
                <th style="width: 20%;">Aksi</th>
            </tr>
            </thead>
            <tbody>
                <?php
                $mhs = mysqli_query($conn, "SELECT * FROM users WHERE role='mahasiswa'");
                while($row = mysqli_fetch_assoc($mhs)) {
                    $id = $row['id'];
                    $nama = htmlspecialchars($row['nama'], ENT_QUOTES, 'UTF-8');
                    $email = htmlspecialchars($row['email'], ENT_QUOTES, 'UTF-8');
                    echo "
                    <tr>
                        <td>{$nama}</td>
                        <td>{$email}</td>
                        <td>
                            <a href='detailUser.php?id=$id' class='btn btn-info btn-sm'>
                                Detail
                            </a>

                            <a href='hapusUser.php?id=$id'
                            class='btn btn-danger btn-sm'
                            onclick=\"return confirm('Yakin ingin menghapus akun ini?')\">
                            Hapus
                            </a>
                        </td>
                    </tr>";
                }
                ?>
            </tbody>
        </table>

        <h5 class="fw-bold mt-4">Daftar Mentor</h5>
        <table class="table bg-white shadow-sm">
        <thead>
        <tr>
            <th style="width: 40%;">Nama</th>
            <th style="width: 40%;">Email</th>
            <th style="width: 20%;">Aksi</th>
        </tr>
        </thead>
            <tbody>
                <?php
                $mnt = mysqli_query($conn, "SELECT * FROM users WHERE role='mentor'");
                while($row = mysqli_fetch_assoc($mnt)) {
                    $id = $row['id'];
                    $nama = htmlspecialchars($row['nama'], ENT_QUOTES, 'UTF-8');
                    $email = htmlspecialchars($row['email'], ENT_QUOTES, 'UTF-8');

                    echo "
                    <tr>
                        <td>{$nama}</td>
                        <td>{$email}</td>
                        <td>
                            <a href='detailUser.php?id=$id'
                            class='btn btn-info btn-sm'>
                            Detail
                            </a>

                            <a href='hapusUser.php?id=$id'
                            class='btn btn-danger btn-sm'
                            onclick=\"return confirm('Yakin ingin menghapus akun ini?')\">
                            Hapus
                            </a>
                        </td>
                    </tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<script>
function searchTable() {
    let input = document.getElementById("searchInput");
    let filter = input.value.toLowerCase();
    let tables = document.querySelectorAll(".table");

    tables.forEach(table => {
        let tr = table.getElementsByTagName("tr");
        for (let i = 1; i < tr.length; i++) {
            let td = tr[i].getElementsByTagName("td");
            let found = false;
            for (let j = 0; j < td.length; j++) {
                if (td[j].innerText.toLowerCase().indexOf(filter) > -1) {
                    found = true;
                }
            }
            // Menggunakan d-none milik Bootstrap untuk menyembunyikan/menampilkan
            if (found) {
                tr[i].classList.remove('d-none');
            } else {
                tr[i].classList.add('d-none');
            }
        }
    });
}
</script>

<?php include '../templates/footer.php'; ?>