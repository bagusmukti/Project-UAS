<?php

// Mulai sesi
session_start();

// Cek apakah user admin sudah login
include '../../config/koneksi.php';

// Cek session admin
if (!isset($_SESSION['user_id']) || $_SESSION['level'] !== 'admin') {
    header("Location: ../login_page.php");
    exit();
}

// Ambil ID pengaduan dari URL
$id = $_GET['id'] ?? 0;

// Ambil data pengaduan
// Menggunakan LEFT JOIN untuk mendapatkan status pengaduan
$query = "SELECT p.*, s.status, pp.answ_peng, pp.answ_foto 
          FROM tbl_peng p
          LEFT JOIN tbl_proses_peng pp ON p.id = pp.id_peng
          LEFT JOIN tbl_status_peng s ON pp.id_status = s.id
          WHERE p.id = ?";

$stmt = $conn->prepare($query); // Menyiapkan statement
$stmt->bind_param("i", $id); // Mengikat parameter
$stmt->execute(); // Eksekusi query
$result = $stmt->get_result(); // Mendapatkan hasil
$data = $result->fetch_assoc(); // Mengambil data pengaduan

// Jika form disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $status_id = $_POST['status'];
    $jawaban = $_POST['jawaban'];
    $fotoadmin = $data['answ_foto'] ?? ''; // Gunakan foto yang sudah ada jika tidak diupdate

    $conn->begin_transaction(); // Memulai transaksi

    try {
        // Handle file upload
        if (!empty($_FILES['fotoadmin']['name'])) {
            $target_dir = "../../assets/uploaded_pics/"; // Ganti dengan direktori tujuan upload

            $extension = pathinfo($_FILES['fotoadmin']['name'], PATHINFO_EXTENSION);

            $new_filename = "balasan_laporan_" . $id . "." . $extension; // Nama file baru
            $target_file = $target_dir . $new_filename;

            // Validasi file (opsional)
            $imageFileType = strtolower($extension);
            $allowed_types = array('jpg', 'jpeg', 'png', 'gif');

            if (!in_array($imageFileType, $allowed_types)) {
                throw new Exception("Hanya file JPG, JPEG, PNG & GIF yang diizinkan.");
            }

            if (move_uploaded_file($_FILES['fotoadmin']['tmp_name'], $target_file)) {
                $fotoadmin = $new_filename;
            } else {
                throw new Exception("Gagal mengupload file!");
            }
        }

        // Update atau insert ke tbl_proses_peng
        if ($data['status']) {
            // Jika sudah ada status, update
            $stmt = $conn->prepare("UPDATE tbl_proses_peng 
                                   SET id_status = ?, answ_peng = ?, answ_foto = ?
                                   WHERE id_peng = ?");
            $stmt->bind_param("issi", $status_id, $jawaban, $fotoadmin, $id); // Perhatikan tipe data: i-integer, s-string
        } else {
            // Jika belum ada status, insert
            $stmt = $conn->prepare("INSERT INTO tbl_proses_peng 
                                   (id_peng, id_status, answ_peng, answ_foto) 
                                   VALUES (?, ?, ?, ?)");
            $stmt->bind_param("iiss", $id, $status_id, $jawaban, $fotoadmin); // Perhatikan tipe data
        }

        $stmt->execute(); // Eksekusi query

        $conn->commit(); // Commit transaksi
        $_SESSION['editadmin_success'] = "Pengaduan berhasil diupdate!"; // Set session success message
        header("Location: dashboard_admin.php"); // Redirect ke halaman dashboard
        exit(); // Keluar dari script
    } catch (Exception $e) {
        $conn->rollback(); // Rollback transaksi jika terjadi kesalahan
        $_SESSION['error'] = "Gagal update: " . $e->getMessage(); // Set session error message
    }
}

// Ambil semua status pengaduan
$statuses = $conn->query("SELECT * FROM tbl_status_peng")->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Pengaduan</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>

<body>
    <div class="form-user">
        <h2 class="h2-user">Edit Pengaduan #<?= htmlspecialchars($id) ?></h2>

        <div class="form-group">
            <label class="label-user">Isi Laporan:</label>
            <div class="original-complaint">
                <?= nl2br(htmlspecialchars($data['isi_lap'] ?? '')) ?>
            </div>
        </div>

        <div class="form-group">
            <label class="label-user">Foto Laporan:</label>
            <div class="original-photo">
                <?php if (!empty($data['foto'])): ?>
                    <img src="../../assets/uploaded_pics/<?= htmlspecialchars($data['foto']) ?>"
                        alt="Foto Aduan"
                        class="complaint-photo">
                <?php else: ?>
                    <p>Tidak ada foto yang diunggah</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Tampilan pesan sukses -->
        <form method="POST" enctype="multipart/form-data" class="form-split">
            <div class="form-column left">
                <div class="form-group">
                    <label class="label-user">Status:</label>
                    <select name="status" required>
                        <?php foreach ($statuses as $status): ?>
                            <option value="<?= htmlspecialchars($status['id']) ?>"
                                <?= ($data['status'] ?? '') === $status['status'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars(ucfirst($status['status'])) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group" style="padding-top: 20px;">
                    <label class="label-user" for="fotoadmin">Unggah Bukti foto:</label>
                    <input type="file" name="fotoadmin" accept="image/*">
                </div>
            </div>

            <div class="form-column right">
                <div class="form-group">
                    <label class="label-user">Jawaban:</label>
                    <textarea name="jawaban"><?= htmlspecialchars($data['answ_peng'] ?? '') ?></textarea>
                </div>

                <div class="form-group">
                    <?php if (!empty($data['answ_foto'])): ?>
                        <label class="label-user">Foto saat ini: <?= htmlspecialchars($data['answ_foto']) ?></label>
                        <div class="original-photo">
                            <img src="../../assets/uploaded_pics/<?= htmlspecialchars($data['answ_foto']) ?>" width="200">
                        </div>
                    <?php endif; ?>
                </div>

            </div>
            <div class="form-group">
                <button class="button-save" type="submit">Simpan</button>
            </div>
        </form>
        <div class="form-group" style="padding-top: 20px;">
            <a class="button-back" href="dashboard_admin.php">Kembali</a>
        </div>
    </div>
</body>

</html>