<?php
$id_user = $_SESSION['id'];
$q_notif = mysqli_query($conn, "SELECT * FROM notifikasi WHERE id_user='$id_user' ORDER BY waktu DESC LIMIT 10");
$q_unread = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as unread FROM notifikasi WHERE id_user='$id_user' AND is_read=0"))['unread'];
?>

<div style="position: fixed; top: 25px; right: 30px; z-index: 9999;">
    <div class="dropdown">
        <button class="btn btn-light rounded-circle shadow-sm position-relative border-0" type="button" id="notifDropdown" data-bs-toggle="dropdown" aria-expanded="false" style="width: 50px; height: 50px; font-size: 20px; color: #1E3A8A;">
            <i class="bi bi-bell-fill"></i>
            <?php if($q_unread > 0) { ?>
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 10px;">
                    <?php echo $q_unread; ?>
                </span>
            <?php } ?>
        </button>
        <ul class="dropdown-menu dropdown-menu-end shadow border-0" aria-labelledby="notifDropdown" style="width: 350px; border-radius: 12px; overflow: hidden; max-height: 400px; overflow-y: auto;">
            <li class="bg-light p-3 border-bottom d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold" style="color: #1E3A8A;">Notifikasi</h6>
                <?php if($q_unread > 0) { ?>
                    <button class="btn btn-sm btn-link text-decoration-none text-muted p-0" onclick="markAllRead()" style="font-size: 12px;">Tandai semua dibaca</button>
                <?php } ?>
            </li>
            
            <?php 
            if(mysqli_num_rows($q_notif) > 0) {
                while($n = mysqli_fetch_assoc($q_notif)) {
                    $bg = $n['is_read'] == 0 ? 'bg-primary bg-opacity-10' : 'bg-white';
            ?>
                <li>
                    <a class="dropdown-item py-3 border-bottom text-wrap <?php echo $bg; ?>" href="../baca_notif.php?id=<?php echo $n['id_notif']; ?>" style="font-size: 13px; line-height: 1.5;">
                        <div class="d-flex align-items-start">
                            <div class="text-primary me-3 fs-4"><?php echo $n['ikon']; ?></div>
                            <div>
                                <p class="mb-1 text-dark fw-semibold"><?php echo htmlspecialchars($n['pesan']); ?></p>
                                <small class="text-muted"><i class="bi bi-clock"></i> <?php echo date('d M H:i', strtotime($n['waktu'])); ?></small>
                            </div>
                        </div>
                    </a>
                </li>
            <?php 
                }
            } else { 
            ?>
                <li><p class="dropdown-item text-center text-muted py-4 mb-0">Belum ada notifikasi.</p></li>
            <?php } ?>
        </ul>
    </div>
</div>

<script>
function markAllRead() {
    fetch('../baca_notif.php?all=1')
    .then(res => res.text())
    .then(data => location.reload());
}
</script>
