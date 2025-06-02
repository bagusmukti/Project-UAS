<?php

// Mulai sesi
session_start();

// Cek apakah pengguna sudah login
if (!isset($_SESSION['user_id']) || $_SESSION['level'] !== 'masyarakat') {
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

    $query_user = "SELECT username FROM tbl_user WHERE id = ?";
    $stmt_user = mysqli_prepare($conn, $query_user);
    mysqli_stmt_bind_param($stmt_user, "i", $user_id);
    mysqli_stmt_execute($stmt_user);
    $result_user = mysqli_stmt_get_result($stmt_user);
    $user = mysqli_fetch_assoc($result_user);
    $username = $user['username'] ?? 'User'; // Ambil username atau default ke 'User'
    mysqli_stmt_close($stmt_user);
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
    <link rel="stylesheet" href="../assets/css/butterpop.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<body>
    <div class="main-container">
        <div class="header-user-admin">
            <h1 class="text-dashboard">S!AP</h1>

            <div class="btn-group">
                <a href="form_pengaduan.php" class="btn btn-buataduan">
                    Buat Aduan
                    <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="green">
                        <path d="M200-120q-33 0-56.5-23.5T120-200v-560q0-33 23.5-56.5T200-840h357l-80 80H200v560h560v-278l80-80v358q0 33-23.5 56.5T760-120H200Zm280-360ZM360-360v-170l367-367q12-12 27-18t30-6q16 0 30.5 6t26.5 18l56 57q11 12 17 26.5t6 29.5q0 15-5.5 29.5T897-728L530-360H360Zm481-424-56-56 56 56ZM440-440h56l232-232-28-28-29-28-231 231v57Zm260-260-29-28 29 28 28 28-28-28Z" />
                    </svg>
                </a>
                <a href="logout_page.php" class="btn btn-logout">
                    Logout
                    <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="red">
                        <path d="M200-120q-33 0-56.5-23.5T120-200v-560q0-33 23.5-56.5T200-840h280v80H200v560h280v80H200Zm440-160-55-58 102-102H360v-80h327L585-622l55-58 200 200-200 200Z" />
                    </svg>
                </a>
                <div class="user-info">
                    <p><?= htmlspecialchars($username) ?></p>
                    <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#82BAFF">
                        <path d="M234-276q51-39 114-61.5T480-360q69 0 132 22.5T726-276q35-41 54.5-93T800-480q0-133-93.5-226.5T480-800q-133 0-226.5 93.5T160-480q0 59 19.5 111t54.5 93Zm246-164q-59 0-99.5-40.5T340-580q0-59 40.5-99.5T480-720q59 0 99.5 40.5T620-580q0 59-40.5 99.5T480-440Zm0 360q-83 0-156-31.5T197-197q-54-54-85.5-127T80-480q0-83 31.5-156T197-763q54-54 127-85.5T480-880q83 0 156 31.5T763-763q54 54 85.5 127T880-480q0 83-31.5 156T763-197q-54 54-127 85.5T480-80Zm0-80q53 0 100-15.5t86-44.5q-39-29-86-44.5T480-280q-53 0-100 15.5T294-220q39 29 86 44.5T480-160Zm0-360q26 0 43-17t17-43q0-26-17-43t-43-17q-26 0-43 17t-17 43q0 26 17 43t43 17Zm0-60Zm0 360Z" />
                    </svg>
                </div>
            </div>
        </div>

        <table border="1" cellpadding="10" cellspacing="0" class="data-table">
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
                        <td data-table="Foto">
                            <!-- Cek apakah ada foto -->
                            <!-- Jika ada foto, tampilkan gambar -->
                            <?php if (!empty($row['foto'])): ?>
                                <img src="../assets/uploaded_pics/<?= htmlspecialchars($row['foto']) ?>"
                                    alt="Laporan Foto"
                                    loading="lazy"
                                    class="photo-thumbnail">
                            <?php else: ?>
                                <!-- Jika tidak ada foto, tampilkan pesan -->
                                <p>Tidak ada foto</p>
                            <?php endif; ?>
                        </td>

                        <!-- Menampilkan status -->
                        <?php
                        $currentStatus = strtolower($row["status"] ?? 'menunggu');
                        $currentStatusClass = str_replace(' ', '-', $currentStatus);
                        ?>

                        <td class="cell-status">
                            <span class="status-badge status-<?= $currentStatusClass ?>">
                                <?= htmlspecialchars($row["status"]) ?? 'Menunggu' ?>
                            </span>
                        </td>
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
                        <td data-table="Foto">
                            <?php if (!empty($row['answ_foto'])): ?>
                                <img src="../assets/uploaded_pics/<?= htmlspecialchars($row['answ_foto']) ?>"
                                    alt="Foto Balasan"
                                    loading="lazy"
                                    class="photo-thumbnail">
                            <?php else: ?>
                                <p>Tidak ada foto balasan</p>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach ?>
            <?php endif; ?>
        </table>
    </div>

    <script src="../assets/js/butterpop.js"></script>

    <?php if (!empty($_SESSION['user_success'])): ?>
        <script>
            ButterPop.show({
                message: "<?= $_SESSION['user_success'] ?>",
                type: "success",
                position: "bottom-right",
                theme: "velvet",
                duration: 4000,
                progress: true,
                closable: true,
                pauseOnHover: true,
                closeOnClick: false
            });
        </script>
        <?php unset($_SESSION['user_success']); // Hapus supaya notif tidak muncul lagi 
        ?>
    <?php endif; ?>

    <?php if (!empty($_SESSION['form_peng_success'])): ?>
        <script>
            ButterPop.show({
                message: "<?= $_SESSION['form_peng_success'] ?>",
                type: "success",
                position: "bottom-right",
                theme: "velvet",
                duration: 4000,
                progress: true,
                closable: true,
                pauseOnHover: true,
                closeOnClick: false
            });
        </script>
        <?php unset($_SESSION['form_peng_success']); ?>
    <?php endif; ?>

    <script>
        <?php if (!empty($_SESSION['success'])): ?>
            ButterPop.show({
                message: "<?= htmlspecialchars($_SESSION['success'], ENT_QUOTES) ?>",
                type: "success",
                position: "bottom-right",
                theme: "velvet",
                duration: 4000,
                progress: true,
                closable: true,
                pauseOnHover: true,
                closeOnClick: false
            });
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <?php if (!empty($_SESSION['error'])): ?>
            ButterPop.show({
                message: "<?= htmlspecialchars($_SESSION['error'], ENT_QUOTES) ?>",
                type: "error",
                position: "top-right",
                theme: "velvet",
                duration: 4000,
                progress: true,
                closable: true,
                pauseOnHover: true,
                closeOnClick: false
            });
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>
    </script>
</body>

</html>