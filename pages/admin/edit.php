<?php
session_start();

include '../../config/koneksi.php';

// Cek session admin
if (!isset($_SESSION['user_id']) || $_SESSION['level'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

$id = $_GET['id'] ?? 0;

// Query untuk mengambil data pengaduan
$query = "SELECT 
            p.id,
            p.isi_lap,
            p.foto,
            pp.id_status,
            s.status,
            pp.answ_peng,
            pp.answ_foto,
            pp.id as proses_id
          FROM tbl_peng p
          LEFT JOIN tbl_proses_peng pp ON p.id = pp.id_peng
          LEFT JOIN tbl_status_peng s ON pp.id_status = s.id
          WHERE p.id = ?";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

// Cek apakah data ditemukan
if (!$data) {
    $_SESSION['error'] = "Data pengaduan tidak ditemukan";
    header("Location: dashboard_admin.php");
    exit();
}

// Set default values jika NULL
if (empty($data['id_status'])) {
    $data['id_status'] = 1; // Default status pertama
}
if (empty($data['status'])) {
    $data['status'] = 'menunggu';
}
if (empty($data['answ_peng'])) {
    $data['answ_peng'] = '';
}
if (empty($data['answ_foto'])) {
    $data['answ_foto'] = '';
}

// Proses form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $status_id = $_POST['status'];
    $jawaban = $_POST['jawaban'];
    $isi_lap = $_POST['isi_lap'];
    $fotoadmin = $data['answ_foto']; // Gunakan foto lama sebagai default
    $is_edit = 1; // Tandai sebagai sudah di-edit

    $conn->begin_transaction();

    try {
        // Handle file upload jika ada
        if (!empty($_FILES['fotoadmin']['name'])) {
            $target_dir = "../../assets/uploaded_pics/";
            $extension = pathinfo($_FILES['fotoadmin']['name'], PATHINFO_EXTENSION);
            $new_filename = "balasan_laporan_" . $id . "." . $extension;
            $target_file = $target_dir . $new_filename;

            if (move_uploaded_file($_FILES['fotoadmin']['tmp_name'], $target_file)) {
                $fotoadmin = $new_filename;
            }
        }

        // Update isi laporan
        $stmt = $conn->prepare("UPDATE tbl_peng SET isi_lap = ?, is_edited = ? WHERE id = ?");
        $stmt->bind_param("sii", $isi_lap, $is_edit, $id);
        $stmt->execute();

        // Cek apakah sudah ada record di tbl_proses_peng
        if (!empty($data['proses_id'])) {
            // Jika sudah ada, lakukan UPDATE
            $stmt = $conn->prepare("UPDATE tbl_proses_peng 
                                   SET id_status = ?, answ_peng = ?, answ_foto = ? 
                                   WHERE id_peng = ?");
            $stmt->bind_param("issi", $status_id, $jawaban, $fotoadmin, $id);
        } else {
            // Jika belum ada, lakukan INSERT
            $stmt = $conn->prepare("INSERT INTO tbl_proses_peng 
                                   (id_peng, id_status, answ_peng, answ_foto) 
                                   VALUES (?, ?, ?, ?)");
            $stmt->bind_param("iiss", $id, $status_id, $jawaban, $fotoadmin);
        }
        $stmt->execute();

        $conn->commit();
        $_SESSION['editadmin_success'] = "Pengaduan berhasil diupdate!";
        header("Location: dashboard_admin.php");
        exit();
    } catch (Exception $e) {
        $conn->rollback();
        $_SESSION['error'] = "Gagal update: " . $e->getMessage();
    }
}

// Ambil semua status pengaduan
$query_status = "SELECT id, status FROM tbl_status_peng ORDER BY id";
$result_status = $conn->query($query_status);
$statuses = $result_status->fetch_all(MYSQLI_ASSOC);
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

        <form method="POST" enctype="multipart/form-data" class="form-split">
            <div class="form-group">
                <label class="label-user">Isi Laporan:</label>
                <div class="original-complaint">
                    <textarea name="isi_lap" class="form-control" required><?= htmlspecialchars($data['isi_lap'] ?? '') ?></textarea>
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

            <div class="form-group button-save">
                <button class="button-save" type="submit">Simpan</button>
            </div>
            <br>
            <div class="form-group button-back">
                <a href="dashboard_admin.php">Kembali</a>
            </div>
        </form>
    </div>
</body>

</html>