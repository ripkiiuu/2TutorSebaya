<?php
include '../config/auth.php';
if ($_SESSION['role'] != 'mahasiswa') exit;
include '../config/koneksi.php';
include '../templates/header.php';

$id_user = $_SESSION['id'];
$lawan_id = isset($_GET['id']) ? mysqli_real_escape_string($conn, $_GET['id']) : 0;

// Ambil daftar mentor yang pernah di-booking
$query_kontak = mysqli_query($conn, "SELECT DISTINCT u.id, u.nama, u.foto_profil 
                                    FROM booking b 
                                    JOIN mentor m ON b.id_mentor = m.id_mentor 
                                    JOIN users u ON m.id_user = u.id 
                                    WHERE b.id_mahasiswa = '$id_user'");
                                    
$lawan_nama = "Pilih kontak untuk mulai chat";
if($lawan_id > 0) {
    $q_nama = mysqli_query($conn, "SELECT nama FROM users WHERE id='$lawan_id'");
    if($r = mysqli_fetch_assoc($q_nama)) $lawan_nama = $r['nama'];
    
    // Tandai notifikasi chat dari kontak ini sebagai dibaca
    mysqli_query($conn, "UPDATE notifikasi SET is_read=1 WHERE id_user='$id_user' AND link LIKE '%chat.php?id=$lawan_id%'");
}
?>
<div class="d-flex" style="background-color: #F8FAFC; min-height: 100vh;">
    <?php include '../templates/sidebar-mahasiswa.php'; ?>
    <div class="p-4" style="margin-left: 260px; width: 100%;">
        <div class="card border-0 shadow-sm" style="border-radius: 12px; height: 85vh; overflow: hidden;">
            <div class="row g-0 h-100">
                <div class="col-md-4 border-end bg-white h-100 overflow-auto">
                    <div class="p-3 border-bottom bg-light">
                        <h5 class="fw-bold mb-0" style="color: #1E3A8A;"><i class="bi bi-chat-dots"></i> Daftar Pesan</h5>
                    </div>
                    <div class="list-group list-group-flush">
                        <?php while($kontak = mysqli_fetch_assoc($query_kontak)) { ?>
                            <a href="?id=<?php echo $kontak['id']; ?>" class="list-group-item list-group-item-action py-3 <?php echo ($kontak['id']==$lawan_id)?'bg-light':''; ?>">
                                <div class="d-flex align-items-center">
                                    <?php if (!empty($kontak['foto_profil'])) { ?>
                                        <img src="../assets/uploads/profil/<?php echo $kontak['foto_profil']; ?>" class="rounded-circle me-3" style="width: 40px; height: 40px; object-fit: cover;">
                                    <?php } else { ?>
                                        <div class="text-white rounded-circle d-flex justify-content-center align-items-center fw-bold me-3" style="width: 40px; height: 40px; background-color: #1E3A8A;">
                                            <?php echo strtoupper(substr($kontak['nama'], 0, 1)); ?>
                                        </div>
                                    <?php } ?>
                                    <h6 class="mb-0 fw-bold"><?php echo $kontak['nama']; ?></h6>
                                </div>
                            </a>
                        <?php } ?>
                    </div>
                </div>
                <div class="col-md-8 h-100 d-flex flex-column bg-white">
                    <div class="p-3 border-bottom shadow-sm z-1">
                        <h5 class="mb-0 fw-bold"><?php echo $lawan_nama; ?></h5>
                    </div>
                    <?php if($lawan_id > 0) { ?>
                        <div class="p-4 flex-grow-1 overflow-auto" id="chat-box" style="background-color: #f1f5f9;">
                            <!-- Chat loads here -->
                        </div>
                        <div class="p-3 border-top bg-white">
                            <form id="form-chat" class="d-flex gap-2">
                                <input type="hidden" id="penerima_id" value="<?php echo $lawan_id; ?>">
                                <input type="text" id="isi_pesan" class="form-control form-control-lg border-0 shadow-sm" placeholder="Ketik pesan..." required style="border-radius: 20px; background: #f8fafc;">
                                <button type="submit" class="btn text-white shadow-sm px-4" style="background-color: #1E3A8A; border-radius: 20px;"><i class="bi bi-send-fill"></i></button>
                            </form>
                        </div>
                    <?php } else { ?>
                        <div class="d-flex flex-column justify-content-center align-items-center h-100 text-muted">
                            <i class="bi bi-chat-square-text" style="font-size: 60px; color: #cbd5e1;"></i>
                            <h5 class="mt-3">Pilih percakapan untuk memulai</h5>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if($lawan_id > 0) { ?>
<script>
    const chatBox = document.getElementById('chat-box');
    const formChat = document.getElementById('form-chat');
    const isiPesan = document.getElementById('isi_pesan');
    const penerimaId = document.getElementById('penerima_id').value;

    function fetchChat() {
        fetch('../api_chat.php?action=fetch&lawan_id=' + penerimaId)
            .then(res => res.json())
            .then(data => {
                chatBox.innerHTML = '';
                data.forEach(msg => {
                    let alignClass = msg.is_me ? 'text-end' : 'text-start';
                    let bgClass = msg.is_me ? 'bg-primary text-white' : 'bg-white text-dark';
                    let marginClass = msg.is_me ? 'ms-auto' : 'me-auto';
                    chatBox.innerHTML += `
                        <div class="${alignClass} mb-3">
                            <div class="d-inline-block p-3 shadow-sm ${bgClass} ${marginClass}" style="max-width: 75%; border-radius: 15px; text-align: left;">
                                ${msg.pesan}
                                <small class="d-block mt-1 opacity-75" style="font-size: 10px;">${msg.waktu}</small>
                            </div>
                        </div>`;
                });
                chatBox.scrollTop = chatBox.scrollHeight;
            });
    }

    formChat.addEventListener('submit', function(e) {
        e.preventDefault();
        let formData = new FormData();
        formData.append('penerima_id', penerimaId);
        formData.append('isi_pesan', isiPesan.value);

        fetch('../api_chat.php?action=send', {
            method: 'POST',
            body: formData
        }).then(() => {
            isiPesan.value = '';
            fetchChat();
        });
    });

    setInterval(fetchChat, 2000);
    fetchChat();
</script>
<?php } ?>
<?php include '../templates/footer.php'; ?>
