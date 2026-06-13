-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               8.4.3 - MySQL Community Server - GPL
-- Server OS:                    Win64
-- HeidiSQL Version:             12.8.0.6908
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

-- Dumping data for table tutorsebaya.booking: ~4 rows (approximately)
INSERT INTO `booking` (`id_booking`, `id_mahasiswa`, `id_mentor`, `tanggal`, `jam`, `total_harga`, `status`, `bukti_bayar`, `link_meet`, `catatan_mentor`, `is_reviewed`) VALUES
	(1, 1, 1, '2026-06-03', '08:00 - 09:00', 50000, 'selesai', NULL, 'https://meet.google.com/ash-uwjn-ghh', 'Sampai bertemu nanti yaa!!!', 1),
	(2, 1, 1, '2026-06-04', '08:00 - 11:00', 50000, 'selesai', NULL, 'https://meet.google.com/cjx-ixeq-qnr', 'SEMANGATT!', 1),
	(3, 1, 2, '2026-06-04', '08:00 - 11:00', 300000, 'selesai', NULL, 'https://meet.google.com/cjx-ixeq-qnr', '3 JAM BANGET NIHH RIFQY?!!', 1),
	(4, 1, 2, '2026-06-05', '09:00 - 11:00', 200000, 'selesai', NULL, 'https://meet.google.com/cjx-ixeq-qnr', 'SAMPE KETEMU YA RIFQY!!!', 0);

-- Dumping data for table tutorsebaya.mentor: ~2 rows (approximately)
INSERT INTO `mentor` (`id_mentor`, `id_user`, `mata_kuliah`, `tarif`, `deskripsi`, `status_verifikasi`, `rating`) VALUES
	(1, 2, 'Pemrograman Web', 50000, 'Aku Jago Web Lho', 'approved', 5.0),
	(2, 4, 'Sistem Basis Data', 100000, 'Akulah si data science itu', 'approved', 5.0);

-- Dumping data for table tutorsebaya.notifikasi: ~9 rows (approximately)
INSERT INTO `notifikasi` (`id_notif`, `id_user`, `pesan`, `ikon`, `link`, `is_read`, `waktu`) VALUES
	(1, 4, 'Pesan baru dari Muhammad Rifqy Habibi: kak ratu saya mau be...', '💬', 'chat.php?id=1', 1, '2026-06-04 01:29:03'),
	(2, 1, 'Pesan baru dari Ade Ratu Safitri: bolehh...', '💬', 'chat.php?id=4', 1, '2026-06-04 01:29:27'),
	(3, 4, 'Pesan baru dari Muhammad Rifqy Habibi: ok mantap...', '💬', 'chat.php?id=1', 1, '2026-06-04 01:36:55'),
	(4, 3, 'Mentor (Eka) mengajukan penarikan dana sebesar Rp 100.000', '💰', 'penarikan.php', 0, '2026-06-04 01:53:33'),
	(5, 2, 'Penarikan sebesar Rp 100.000 BERHASIL ditransfer ke rekeningmu!', '💸', 'dashboard.php', 1, '2026-06-04 01:54:05'),
	(6, 4, 'Mahasiswa mengajukan booking baru pada 2026-06-05 (2 Jam). Menunggu konfirmasi Anda.', '📅', 'dashboard.php', 1, '2026-06-04 02:09:33'),
	(7, 4, 'Pesan baru dari Muhammad Rifqy Habibi: hi ratu...', '💬', 'chat.php?id=1', 1, '2026-06-04 02:10:00'),
	(8, 1, 'Mentor (Ade Ratu Safitri) mengubah status jadwal Anda menjadi: disetujui', '✅', 'jadwal.php', 0, '2026-06-04 02:10:32'),
	(9, 1, 'Mentor (Ade Ratu Safitri) mengubah status jadwal Anda menjadi: selesai', '✅', 'jadwal.php', 0, '2026-06-04 02:11:40');

-- Dumping data for table tutorsebaya.penarikan: ~1 rows (approximately)
INSERT INTO `penarikan` (`id_penarikan`, `id_mentor`, `jumlah`, `info_rekening`, `status`, `tanggal`) VALUES
	(1, 1, 100000, 'BRI - 473701033675538', 'berhasil', '2026-06-04 01:53:33');

-- Dumping data for table tutorsebaya.pesan: ~8 rows (approximately)
INSERT INTO `pesan` (`id_pesan`, `pengirim_id`, `penerima_id`, `isi_pesan`, `waktu`, `dibaca`) VALUES
	(1, 1, 2, 'Hai kak, saya mau order bisa?', '2026-06-04 01:08:49', 0),
	(2, 2, 1, 'bisa ya rifqy', '2026-06-04 01:09:21', 0),
	(3, 1, 2, 'oke kak makasi ya', '2026-06-04 01:16:10', 0),
	(4, 1, 4, 'hai kak ratu', '2026-06-04 01:16:43', 0),
	(5, 4, 1, 'haloo rifqy', '2026-06-04 01:18:43', 0),
	(6, 1, 4, 'kak ratu saya mau belajaar sbd dong', '2026-06-04 01:29:03', 0),
	(7, 4, 1, 'bolehh', '2026-06-04 01:29:27', 0),
	(8, 1, 4, 'ok mantap', '2026-06-04 01:36:55', 0),
	(9, 1, 4, 'hi ratu', '2026-06-04 02:10:00', 0);

-- Dumping data for table tutorsebaya.ulasan: ~2 rows (approximately)
INSERT INTO `ulasan` (`id_ulasan`, `id_booking`, `id_mentor`, `id_mahasiswa`, `rating`, `komentar`, `tanggal`) VALUES
	(1, 1, 1, 1, 5, 'KERENNN BANGET, CARA NGAJARNYA UNIK!', '2026-06-03 23:40:28'),
	(2, 2, 1, 1, 5, 'MAKIN KEREN AJA TUTOR INI\r\n', '2026-06-04 01:38:49'),
	(3, 3, 2, 1, 5, 'KEREN KAK RATU', '2026-06-04 01:38:59');

-- Dumping data for table tutorsebaya.users: ~4 rows (approximately)
INSERT INTO `users` (`id`, `nama`, `email`, `role`, `no_wa`, `universitas`, `password`, `status_akun`, `foto_profil`) VALUES
	(1, 'Muhammad Rifqy Habibi', 'rifqy@gmail.com', 'mahasiswa', '08123456789', 'Universitas Mataram', '$2y$12$GXosBA.YYKXYpvzORI5gVOewh57mcMLYXCJBzRMJSrQHwpV4BIlme', 'aktif', 'mhs_1_1780503553.jpeg'),
	(2, 'Eka', 'eka@gmail.com', 'mentor', '081775232136', 'Undikma', '$2y$12$GXosBA.YYKXYpvzORI5gVOewh57mcMLYXCJBzRMJSrQHwpV4BIlme', 'aktif', NULL),
	(3, 'Administrator', 'admin@tutorsebaya.com', 'admin', NULL, NULL, '$2y$12$GXosBA.YYKXYpvzORI5gVOewh57mcMLYXCJBzRMJSrQHwpV4BIlme', 'aktif', NULL),
	(4, 'Ade Ratu Safitri', 'ratu@gmail.com', 'mentor', '081773212356', 'Universitas Indonesia', '$2y$12$jY.BjZWkqopc2P6vvHsgZuFSkZ3TgQHktuOptX7ZVfioJpQpM9EO.', 'aktif', 'mntr_4_1780506876.jpeg');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
