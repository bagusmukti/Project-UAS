<?php
session_start();
include '../../config/koneksi.php';

// Cek session admin
// if (!isset($_SESSION['user_id']) || $_SESSION['level'] !== 'admin') {
//     header("Location: ../login_page.php");
//     exit();
// }

$id = $_GET['id'] ?? 0;

// Ambil data pengaduan
$query = "SELECT p.*, s.status, pp.answ_peng 
          FROM tbl_peng p
          LEFT JOIN tbl_proses_peng pp ON p.id = pp.id_peng
          LEFT JOIN tbl_status_peng s ON pp.id_status = s.id
          WHERE p.id = ?";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

// Handle form submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $status_id = $_POST['status'];
    $jawaban = $_POST['jawaban'];

    // Mulai transaksi
    $conn->begin_transaction();

    try {
        // Update atau insert ke tbl_proses_peng
        if ($data['status']) {
            $stmt = $conn->prepare("UPDATE tbl_proses_peng 
                                   SET id_status = ?, answ_peng = ? 
                                   WHERE id_peng = ?");
            $stmt->bind_param("isi", $status_id, $jawaban, $id);
        } else {
            $stmt = $conn->prepare("INSERT INTO tbl_proses_peng 
                                   (id_peng, id_status, answ_peng) 
                                   VALUES (?, ?, ?)");
            $stmt->bind_param("iis", $id, $status_id, $jawaban);
        }

        $stmt->execute();

        $conn->commit();
        $_SESSION['success'] = "Pengaduan berhasil diupdate!";
        header("Location: dashboard_admin.php");
        exit();
    } catch (Exception $e) {
        $conn->rollback();
        $_SESSION['error'] = "Gagal update: " . $e->getMessage();
    }
}

// Ambil daftar status
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
        <h2>Edit Pengaduan #<?= $id ?></h2>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert error"><?= $_SESSION['error'] ?></div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label>Status:</label>
                <select name="status" required>
                    <?php foreach ($statuses as $status): ?>
                        <option value="<?= $status['id'] ?>"
                            <?= ($data['status'] ?? '') === $status['status'] ? 'selected' : '' ?>>
                            <?= ucfirst($status['status']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Jawaban:</label>
                <textarea name="jawaban"><?= htmlspecialchars($data['answ_peng'] ?? '') ?></textarea>
            </div>

            <button type="submit">Simpan</button>
            <a href="dashboard_admin.php">Kembali</a>
        </form>
    </div>
</body>

</html>