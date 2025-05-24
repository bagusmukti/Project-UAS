<?php

// Mulai sesi
session_start();

// Cek apakah pengguna sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: login_page.php");
    exit();
}

// Sertakan file koneksi database
include '../config/koneksi.php';

$complaints = []; // Inisialisasi array untuk menyimpan data laporan

// Ambil data laporan dari database
try {
    // mengambil id user dari session
    $user_id = $_SESSION['user_id'];

    // Query untuk mengambil data laporan
    $query = "SELECT p.*, COALESCE(s.status, 'Menunggu') AS status, pp.answ_peng, pp.answ_foto
              FROM tbl_peng p
              LEFT JOIN tbl_proses_peng pp ON p.id = pp.id_peng
              LEFT JOIN tbl_status_peng s ON pp.id_status = s.id
              WHERE p.id_user = ?";

    // Siapkan dan eksekusi query    
    $stmt = mysqli_prepare($conn, $query);
    // Cek apakah query berhasil disiapkan
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    // Cek apakah parameter berhasil dieksekusi
    mysqli_stmt_execute($stmt);
    // Ambil hasil query
    $result = mysqli_stmt_get_result($stmt);
    // Menyimpan hasil ke dalam array
    $complaints = mysqli_fetch_all($result, MYSQLI_ASSOC);
}
// Cek apakah ada error
catch (Exception $e) {
    error_log("Database error: " . $e->getMessage());
    $_SESSION['error'] = "Terjadi kesalahan saat memuat data. Silakan coba lagi.";
    header("Location: dashboard_user.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard User</title>
    <link rel="stylesheet" href="../assets/css/style.css">

</head>

<body>
    <div class="table-container">
        <table cellpadding="10" cellspacing="0" class="tabellll">
            <tr>
                <td class="header-db">
                    <h1 class="text-dashboard">Dashboard User</h1>
                </td>
                <td class="header-sisa">
                    <a href="form_pengaduan.php" class="btn-buataduan">
                        Buat Aduan
                        <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#fff">
                            <path d="M200-120q-33 0-56.5-23.5T120-200v-560q0-33 23.5-56.5T200-840h357l-80 80H200v560h560v-278l80-80v358q0 33-23.5 56.5T760-120H200Zm280-360ZM360-360v-170l367-367q12-12 27-18t30-6q16 0 30.5 6t26.5 18l56 57q11 12 17 26.5t6 29.5q0 15-5.5 29.5T897-728L530-360H360Zm481-424-56-56 56 56ZM440-440h56l232-232-28-28-29-28-231 231v57Zm260-260-29-28 29 28 28 28-28-28Z" />
                        </svg>
                    </a>
                </td>
                <td class="header-sisa">
                    <a href="logout_page.php" class="btn-logout">
                        Logout
                        <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3">
                            <path d="M200-120q-33 0-56.5-23.5T120-200v-560q0-33 23.5-56.5T200-840h280v80H200v560h280v80H200Zm440-160-55-58 102-102H360v-80h327L585-622l55-58 200 200-200 200Z" />
                        </svg>
                    </a>
                </td>
            </tr>
        </table>
    </div>


    <!-- Notifikasi -->
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success"><?= $_SESSION['success'] ?></div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-error"><?= $_SESSION['error'] ?></div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <div class="table-container">
        <table border="1" cellpadding="10" cellspacing="0">
            <tr>
                <th>Nama Pelapor</th>
                <th>Email</th>
                <th>Isi Laporan</th>
                <th>Foto</th>
                <th>Status</th>
                <th>Balasan</th>
                <th>Foto Balasan</th>
            </tr>
            <!-- Tampilkan data laporan -->
            <!-- Jika tidak ada laporan -->
            <?php if (empty($complaints)): ?>
                <tr>
                    <td colspan="7" style="text-align: center;">Tidak ada laporan yang ditemukan</td>
                </tr>
            <?php else: ?>
                <!-- Jika ada laporan -->
                <?php foreach ($complaints as $row): ?>
                    <tr>
                        <td><?= htmlspecialchars($row["nama"]) ?></td>
                        <td><?= htmlspecialchars($row["email"]) ?></td>
                        <td><?= htmlspecialchars($row["isi_lap"]) ?></td>
                        <td>
                            <!-- Cek apakah ada foto -->
                            <!-- Jika ada foto, tampilkan gambar -->
                            <?php if (!empty($row['foto'])): ?>
                                <img src="../assets/uploaded_pics/<?= htmlspecialchars($row['foto']) ?>"
                                    alt="Laporan Foto"
                                    loading="lazy"
                                    style="width: 100px; height: auto; max-width: 100%;">
                            <?php else: ?>
                                <!-- Jika tidak ada foto, tampilkan pesan -->
                                <p>Tidak ada foto</p>
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($row["status"]) ?></td>
                        <td>
                            <!-- Cek apakah ada balasan -->
                            <!-- Jika ada balasan, tampilkan isi balasan -->
                            <?php if (!empty($row['answ_peng'])): ?>
                                <?= nl2br(htmlspecialchars($row['answ_peng'])) ?>
                            <?php else: ?>
                                <!-- Jika tidak ada balasan, tampilkan pesan -->
                                <p>Belum ada balasan</p>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if (!empty($row['answ_foto'])): ?>
                                <img src="../assets/uploaded_pics/<?= htmlspecialchars($row['answ_foto']) ?>"
                                    alt="Foto Balasan"
                                    loading="lazy"
                                    style="width: 100px; height: auto; max-width: 100%;"
                                >
                            <?php else: ?>
                                <p>Tidak ada foto balasan</p>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach ?>
            <?php endif; ?>
        </table>
    </div>
</body>

</html>