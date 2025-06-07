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
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Mulish:ital@0;1&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
</head>

<body>
    <div class="wrapper d-flex">
        <aside class="sidebar p-3" style="background-color:#0e468b; color:rgb(255, 255, 255);">
            <h1 class="mb-3" style="color:rgb(255, 255, 255) !important; text-decoration: none; padding-left: 15px;">S!AP</h1>
            <nav class="nav flex-column">
                <a class="nav-link active" href="dashboard_user.php" style="color:rgb(255, 255, 255) !important; text-decoration: none;">Dashboard</a>
                <div class="dropdown mt-2">
                    <a class="nav-link dropdown-toggle" href="#" style="color:rgb(255, 255, 255) !important; text-decoration: none;">
                        Pengaduan
                    </a>
                    <ul class="dropdown-menu w-100">
                        <li>
                            <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#modalLaporKeluhan" style="color:rgb(10, 60, 120) !important; text-decoration: none; font-size: smaller;">
                                Form Pengaduan
                            </a>
                        </li>
                        <li><a class="dropdown-item" href="riwayat_aduan.php" style="color: rgb(10, 60, 120) !important; text-decoration: none; font-size: smaller;">Riwayat</a></li>
                    </ul>
                </div>

                <a class="mt-auto btn-logout" href="logout_page.php">
                    <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="white" style="transform: scaleX(-1);">
                        <path d="M200-120q-33 0-56.5-23.5T120-200v-560q0-33 23.5-56.5T200-840h280v80H200v560h280v80H200Zm440-160-55-58 102-102H360v-80h327L585-622l55-58 200 200-200 200Z" />
                    </svg>
                    Logout
                </a>
            </nav>
        </aside>

        <div class="main-container">
            <div class="header-user-admin">
                <a class="user-info" href="edit_profile.php">
                    <p><?= htmlspecialchars($username) ?></p>
                    <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#2a6ec1">
                        <path d="M234-276q51-39 114-61.5T480-360q69 0 132 22.5T726-276q35-41 54.5-93T800-480q0-133-93.5-226.5T480-800q-133 0-226.5 93.5T160-480q0 59 19.5 111t54.5 93Zm246-164q-59 0-99.5-40.5T340-580q0-59 40.5-99.5T480-720q59 0 99.5 40.5T620-580q0 59-40.5 99.5T480-440Zm0 360q-83 0-156-31.5T197-197q-54-54-85.5-127T80-480q0-83 31.5-156T197-763q54-54 127-85.5T480-880q83 0 156 31.5T763-763q54 54 85.5 127T880-480q0 83-31.5 156T763-197q-54 54-127 85.5T480-80Zm0-80q53 0 100-15.5t86-44.5q-39-29-86-44.5T480-280q-53 0-100 15.5T294-220q39 29 86 44.5T480-160Zm0-360q26 0 43-17t17-43q0-26-17-43t-43-17q-26 0-43 17t-17 43q0 26 17 43t43 17Zm0-60Zm0 360Z" />
                    </svg>
                </a>
            </div>

            <div class="cont-keluhan">
                <h2>Ada keluhan apa hari ini?</h2><br>
                <button class="link-lapor p-3" data-bs-toggle="modal" data-bs-target="#modalLaporKeluhan">Lapor Keluhan</button>
            </div>
        </div>
    </div>

    <?php include 'modal.php'; ?>

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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js" integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO" crossorigin="anonymous"></script>

</body>

</html>