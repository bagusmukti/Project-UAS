<?php
session_start();

include '../../config/koneksi.php';

// Cek session admin
if (!isset($_SESSION['user_id']) || $_SESSION['level'] !== 'admin') {
    header("Location: ../login_page.php");
    exit();
}

$id = $_GET['id'] ?? 0;

// Query untuk mengambil data pengaduan
$query = "SELECT 
            p.id,
            p.nama,
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
     <link rel="stylesheet" href="../../assets/css/butterpop.css">

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">

</head>

<body>
        <div class="wrapper d-flex" style="height: 100vh;">
            <aside class="sidebar p-3" style="background-color:#0e468b; color:rgb(255, 255, 255); height: 100%;" >
                <h1 class="mb-3" style="color:rgb(255, 255, 255) !important; text-decoration: none; padding-left: 15px;">S!AP</h1>
                <nav class="nav flex-column">
                    <a class="nav-link active" href="dashboard_admin.php" style="color:rgb(255, 255, 255) !important; text-decoration: none;">Dashboard</a>
                    <a class="nav-link active" href="list_pengaduan.php" style="color:rgb(255, 255, 255) !important; text-decoration: none;">Data Pengaduan</a>
                    

                    <a class="mt-auto btn-logout" href="../logout_page.php">
                        <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="white" style="transform: scaleX(-1);">
                            <path d="M200-120q-33 0-56.5-23.5T120-200v-560q0-33 23.5-56.5T200-840h280v80H200v560h280v80H200Zm440-160-55-58 102-102H360v-80h327L585-622l55-58 200 200-200 200Z" />
                        </svg>
                        Logout
                    </a>
                </nav>
            </aside>

            <div class="main-container">
                <div class="form-user mt-3">
                    <h2 class="h2-user">Edit Pengaduan #<?= htmlspecialchars($id) ?></h2>
                    <form method="POST" enctype="multipart/form-data" class="row g-4">
                        <!-- Kolom Kiri -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="label-user" for="fotoadmin">Nama:</label>
                                <input type="text" class="form-control" value="<?= htmlspecialchars($data['nama'] ?? '') ?>">
                            </div>
                            
                            <div class="form-group">
                                <label class="label-user">Isi Laporan:</label>
                                <textarea name="isi_lap" class="form-control"><?= htmlspecialchars($data['isi_lap'] ?? '') ?></textarea>
                            </div> 
                            <div class="form-group">
                                <label class="label-user">Foto Laporan:</label>
                                <?php if (!empty($data['foto'])): ?>
                                    <img src="../../assets/uploaded_pics/<?= htmlspecialchars($data['foto']) ?>" alt="Foto Aduan" class="img-fluid mb-2">
                                <?php else: ?>
                                    <p style="font-style: italic; font-size: 12px; color: grey;">Tidak ada foto yang diunggah oleh pelapor</p>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Kolom Kanan -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="label-user">Status:</label>
                                <select name="status" class="form-control" required>
                                    <?php foreach ($statuses as $status): ?>
                                        <option value="<?= htmlspecialchars($status['id']) ?>" <?= ($data['status'] ?? '') === $status['status'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars(ucfirst($status['status'])) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="label-user">Jawaban:</label>
                                <textarea name="jawaban" class="form-control"><?= htmlspecialchars($data['answ_peng'] ?? '') ?></textarea>
                            </div>
                            <?php if (!empty($data['answ_foto'])): ?>
                                <div class="form-group">
                                    <label class="label-user">Foto Balasan Saat Ini:</label>
                                    <img src="../../assets/uploaded_pics/<?= htmlspecialchars($data['answ_foto']) ?>" class="img-fluid" width="200">
                                </div>
                            <?php endif; ?>
                            <div class="form-group">
                                <label class="label-user" for="fotoadmin">Unggah Bukti Foto:</label>
                                <input type="file" name="fotoadmin" accept="image/*" class="form-control">
                            </div>
                        </div>
                        <div class="col-12">
                            <button class="btn btn-primary w-100" type="submit">Simpan</button>
                        </div> 

                    </form>
                </div>
            </div>
        </div>
</body>

</html>