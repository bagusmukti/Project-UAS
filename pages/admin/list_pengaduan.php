<?php

// mulai sesi
session_start();

// Cek apakah user admin sudah login
include '../../config/koneksi.php';

// Cek session user admin
if (!isset($_SESSION['user_id']) || $_SESSION['level'] !== 'admin') {
    header("Location: ../login_page.php");
    exit();
}

// fitur search
$search_nama = $_GET['search_nama'] ?? '';
$search_status = isset($_GET['search_status']) ? strtolower($_GET['search_status']) : '';

// Query untuk menampilkan data pengaduan
// Menggunakan LEFT JOIN untuk mendapatkan status pengaduan
$query = "SELECT p.*, COALESCE(LOWER(s.status), 'menunggu') AS status 
          FROM tbl_peng p 
          LEFT JOIN tbl_proses_peng pp ON p.id = pp.id_peng 
          LEFT JOIN tbl_status_peng s ON pp.id_status = s.id
          WHERE 1=1";

// Menambahkan filter berdasarkan nama dan status
$params = [];
$types = ''; // Tipe data untuk bind_param

// Filter berdasarkan nama
if (!empty($search_nama)) {
    $query .= " AND p.nama LIKE ?"; // Menggunakan LIKE untuk pencarian
    $params[] = "%$search_nama%"; // Menambahkan wildcard untuk LIKE
    $types .= 's'; // Tipe data string
}

// Filter berdasarkan status
if (!empty($search_status)) {
    $query .= " AND (COALESCE(LOWER(s.status), 'menunggu') = ?)"; // Menggunakan status yang dipilih
    $params[] = $search_status; // Menambahkan status ke parameter
    $types .= 's'; // Tipe data string
}

// Menambahkan urutan berdasarkan ID pengaduan
$stmt = $conn->prepare($query); // Menyiapkan statement
if ($params) {
    $stmt->bind_param($types, ...$params); // Mengikat parameter
}

// Eksekusi query
$stmt->execute();
// Mendapatkan hasil
$result = $stmt->get_result();
// Mengambil semua data pengaduan
$complaints = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$currentYear = date('Y');
//$queryChart = "SELECT DATE_FORMAT(created_at, '%M') as month, COUNT(*) as count FROM tbl_peng WHERE YEAR(created_at) = $currentYear GROUP BY MONTH(created_at) ORDER BY created_at";
$queryChart = "SELECT MONTH(created_at) as month, COUNT(*) as count FROM tbl_peng WHERE YEAR(created_at) = $currentYear GROUP BY MONTH(created_at) ORDER BY created_at";
$stmt = $conn->prepare($queryChart);
$stmt->execute();
$chartData = $stmt->get_result();
$stmt->close();

$chartRows = [];
while ($row = $chartData->fetch_assoc()) {
    $chartRows[] = $row;
}

$queryChart2 = "SELECT 
    COALESCE(pp.id_status, 1) AS id_status, 
    COUNT(*) as count
FROM tbl_peng p
LEFT JOIN tbl_proses_peng pp ON p.id = pp.id_peng
GROUP BY COALESCE(pp.id_status, 1)";

$stmt = $conn->prepare($queryChart2);
$stmt->execute();
$chartData2 = $stmt->get_result();
$stmt->close();

$chartRows2 = [];
while ($row = $chartData2->fetch_assoc()) {
    $chartRows2[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="../../assets/css/butterpop.css">

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
</head>

<body>
    <div class="wrapper d-flex">
        <aside class="sidebar p-3" style="background-color:#0e468b; color:rgb(255, 255, 255);">
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
            <div class="header-user-admin">
                <div class="btn-group">
                    <div class="user-info">
                        <p>Admin</p>
                        <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#2a6ec1">
                            <path d="M234-276q51-39 114-61.5T480-360q69 0 132 22.5T726-276q35-41 54.5-93T800-480q0-133-93.5-226.5T480-800q-133 0-226.5 93.5T160-480q0 59 19.5 111t54.5 93Zm246-164q-59 0-99.5-40.5T340-580q0-59 40.5-99.5T480-720q59 0 99.5 40.5T620-580q0 59-40.5 99.5T480-440Zm0 360q-83 0-156-31.5T197-197q-54-54-85.5-127T80-480q0-83 31.5-156T197-763q54-54 127-85.5T480-880q83 0 156 31.5T763-763q54 54 85.5 127T880-480q0 83-31.5 156T763-197q-54 54-127 85.5T480-80Zm0-80q53 0 100-15.5t86-44.5q-39-29-86-44.5T480-280q-53 0-100 15.5T294-220q39 29 86 44.5T480-160Zm0-360q26 0 43-17t17-43q0-26-17-43t-43-17q-26 0-43 17t-17 43q0 26 17 43t43 17Zm0-60Zm0 360Z" />
                        </svg>
                    </div>
                </div>
            </div>

            <br>
            <h3 style="color: #2a6ec1; letter-spacing: 2px; text-align: center; margin-bottom: 2rem;">
                DATA PENGADUAN
            </h3>
            <!-- Tabel untuk mencari aduan-->
            <div class="search-container">
                <form method="get" class="search-form">
                    <input type="text" name="search_nama" placeholder="Cari nama..."
                        value="<?= htmlspecialchars($search_nama) ?>" class="search-input">

                    <select name="search_status" class="status-select pr-5">
                        <option value="">Semua Status</option>
                        <option value="menunggu" <?= $search_status === 'menunggu' ? 'selected' : '' ?>>Menunggu</option>
                        <option value="proses" <?= $search_status === 'proses' ? 'selected' : '' ?>>Proses</option>
                        <option value="selesai" <?= $search_status === 'selesai' ? 'selected' : '' ?>>Selesai</option>
                    </select>
                    <button type="submit" class="btn-filter">
                        <span>Cari</span>
                        <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#fff">
                            <path d="M784-120 532-372q-30 24-69 38t-83 14q-109 0-184.5-75.5T120-580q0-109 75.5-184.5T380-840q109 0 184.5 75.5T640-580q0 44-14 83t-38 69l252 252-56 56ZM380-400q75 0 127.5-52.5T560-580q0-75-52.5-127.5T380-760q-75 0-127.5 52.5T200-580q0 75 52.5 127.5T380-400Z" />
                        </svg>
                    </button>
                    <button type="button" class="btn-filter" data-bs-toggle="modal" data-bs-target="#modalLaporKeluhan">
                        <span>Lapor Keluhan</span>
                        <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#fff">
                            <path d="M200-120q-33 0-56.5-23.5T120-200v-560q0-33 23.5-56.5T200-840h560q33 0 56.5 23.5T840-760v268q-19-9-39-15.5t-41-9.5v-243H200v560h242q3 22 9.5 42t15.5 38H200Zm0-120v40-560 243-3 280Zm80-40h163q3-21 9.5-41t14.5-39H280v80Zm0-160h244q32-30 71.5-50t84.5-27v-3H280v80Zm0-160h400v-80H280v80ZM720-40q-83 0-141.5-58.5T520-240q0-83 58.5-141.5T720-440q83 0 141.5 58.5T920-240q0 83-58.5 141.5T720-40Zm-20-80h40v-100h100v-40H740v-100h-40v100H600v40h100v100Z" />
                        </svg>
                    </button>
                </form>

            </div>

            <table border="1" cellpadding="10" cellspacing="0" class="data-table">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Isi Laporan</th>
                        <th>Foto</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($complaints)): ?> <!-- Jika tidak ada data -->
                        <tr>
                            <td colspan="6" style="text-align: center;">Tidak ada data ditemukan</td>
                        </tr>
                    <?php else: ?> <!-- Jika ada data -->
                        <?php foreach ($complaints as $row): ?> <!-- Looping data -->
                            <tr>
                                <td data-table="Nama"><?= htmlspecialchars($row['nama']) ?></td>
                                <td data-table="Email"><?= htmlspecialchars($row['email']) ?></td>
                                <td data-table="Isi Laporan"><?= htmlspecialchars($row['isi_lap']) ?></td>
                                <td data-table="Foto">
                                    <?php if (!empty($row['foto'])): ?> <!-- Jika ada foto -->
                                        <img src="../../assets/uploaded_pics/<?= htmlspecialchars($row['foto']) ?>"
                                            alt="Laporan <?= $row['id'] ?>"
                                            loading="lazy"
                                            class="photo-thumbnail">
                                    <?php else: ?> <!-- Jika tidak ada foto -->
                                        <img src="../../assets/uploaded_pics/<?= htmlspecialchars($row['foto']) ?>"
                                            alt="Laporan <?= $row['id'] ?>"
                                            loading="lazy"
                                            class="photo-thumbnail">
                                    <?php endif; ?>
                                </td>

                                <!-- Menampilkan status -->
                                <?php
                                $currentStatus = strtolower($row["status"] ?? 'menunggu');
                                $currentStatusClass = str_replace(' ', '-', $currentStatus);
                                ?>

                                <td class="cell-status" data-table="Status">
                                    <span class="status-badge status-<?= $row['status'] ?>">
                                        <?= ucfirst(htmlspecialchars($row["status"] ?? 'Menunggu')) ?>
                                    </span>
                                </td>

                                <td class=""> <!-- Tindakan untuk edit dan hapus -->
                                    <a class="btn btn-outline-success" href="edit.php?id=<?= $row['id'] ?>">Tanggapi
                                        <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m14.304 4.844 2.852 2.852M7 7H4a1 1 0 0 0-1 1v10a1 1 0 0 0 1 1h11a1 1 0 0 0 1-1v-4.5m2.409-9.91a2.017 2.017 0 0 1 0 2.853l-6.844 6.844L8 14l.713-3.565 6.844-6.844a2.015 2.015 0 0 1 2.852 0Z" />
                                        </svg>
                                    </a>
                                    <a class="btn btn-danger" style="color:#fff;" href="delete.php?id=<?= $row['id'] ?>"
                                        onclick="return confirm('Yakin menghapus laporan ini?')">Hapus
                                        <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#fff">
                                            <path d="M280-120q-33 0-56.5-23.5T200-200v-520h-40v-80h200v-40h240v40h200v80h-40v520q0 33-23.5 56.5T680-120H280Zm400-600H280v520h400v-520ZM360-280h80v-360h-80v360Zm160 0h80v-360h-80v360ZM280-720v520-520Z" />
                                        </svg>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php include '../modal.php'; ?>

    <script src="../../assets/js/butterpop.js"></script>

    <script>
        <?php if (!empty($_SESSION['admin_success'])): ?>
            ButterPop.show({
                message: "<?= $_SESSION['admin_success'] ?>",
                type: "success",
                position: "bottom-right",
                theme: "velvet",
                duration: 4000,
                progress: true,
                closable: true,
                pauseOnHover: true,
                closeOnClick: false
            });
            <?php unset($_SESSION['admin_success']); // Hapus supaya notif tidak muncul lagi 
            ?>
        <?php endif; ?>

        <?php if (!empty($_SESSION['editadmin_success'])): ?>
            ButterPop.show({
                message: "<?= $_SESSION['editadmin_success'] ?>",
                type: "success",
                position: "bottom-right",
                theme: "velvet",
                duration: 4000,
                progress: true,
                closable: true,
                pauseOnHover: true,
                closeOnClick: false
            });
            <?php unset($_SESSION['editadmin_success']); // Hapus supaya notif tidak muncul lagi 
            ?>
        <?php endif; ?>

        <?php if (!empty($_SESSION['adminhapus_success'])): ?>
            ButterPop.show({
                message: "<?= $_SESSION['adminhapus_success'] ?>",
                type: "success",
                position: "bottom-right",
                theme: "velvet",
                duration: 4000,
                progress: true,
                closable: true,
                pauseOnHover: true,
                closeOnClick: false
            });
            <?php unset($_SESSION['adminhapus_success']); // Hapus supaya notif tidak muncul lagi 
            ?>
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