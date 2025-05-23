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

            $filename = basename($_FILES['fotoadmin']['name']);
            $target_file = $target_dir . $filename;

            // Validasi file (opsional)
            $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
            $allowed_types = array('jpg', 'jpeg', 'png', 'gif');

            if (!in_array($imageFileType, $allowed_types)) {
                throw new Exception("Hanya file JPG, JPEG, PNG & GIF yang diizinkan.");
            }

            if (move_uploaded_file($_FILES['fotoadmin']['tmp_name'], $target_file)) {
                $fotoadmin = $filename;
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
        $_SESSION['success'] = "Pengaduan berhasil diupdate!"; // Set session success message
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
    <style>
        .container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        textarea {
            width: 100%;
            height: 150px;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2>Edit Pengaduan #<?= htmlspecialchars($id) ?></h2>

        <!-- Tampilkan pesan sukses atau error -->
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert error"><?= htmlspecialchars($_SESSION['error']) ?></div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <!-- Tampilan pesan sukses -->
        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label>Status:</label>
                <select name="status" required>
                    <?php foreach ($statuses as $status): ?>
                        <option value="<?= htmlspecialchars($status['id']) ?>"
                            <?= ($data['status'] ?? '') === $status['status'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars(ucfirst($status['status'])) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Jawaban:</label>
                <textarea name="jawaban"><?= htmlspecialchars($data['answ_peng'] ?? '') ?></textarea>
            </div>

            <div class="form-group">
                <label for="fotoadmin">Unggah Bukti foto:</label><br>
                <input type="file" name="fotoadmin" accept="image/*"><br><br>
                <?php if (!empty($data['answ_foto'])): ?>
                    <p>Foto saat ini: <?= htmlspecialchars($data['answ_foto']) ?></p>
                    <img src="../../assets/uploaded_pics/<?= htmlspecialchars($data['answ_foto']) ?>" width="200">
                <?php endif; ?>
            </div>

            <button type="submit">Simpan</button>
            <a href="dashboard_admin.php">Kembali</a>
        </form>
    </div>
</body>

</html>