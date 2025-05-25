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
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin</title>
    <link rel="stylesheet" href="../../assets/css/style.css">

</head>

<body>
    <div class="main-container">
        <div class="header-user-admin">
            <h1 class="text-dashboard">Dashboard Admin</h1>
            <a href="../logout_page.php" class="btn-logout">
                Logout
                <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#fff">
                    <path d="M200-120q-33 0-56.5-23.5T120-200v-560q0-33 23.5-56.5T200-840h280v80H200v560h280v80H200Zm440-160-55-58 102-102H360v-80h327L585-622l55-58 200 200-200 200Z" />
                </svg>
            </a>
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

        <br>
        <!-- Tabel untuk menampilkan data pengaduan -->
        <!-- <div class="table-container"> -->
        <h3 style="text-align: center; margin-top: 30px;">Data Pengaduan</h3>

        <!-- Tabel untuk mencari aduan-->
        <div class="search-container">
            <form method="get" class="search-form">
                <input type="text" name="search_nama" placeholder="Cari nama..."
                    value="<?= htmlspecialchars($search_nama) ?>" class="search-input">

                <select name="search_status" class="status-select">
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
                                    -
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

                            </td>
                            <td class=""> <!-- Tindakan untuk edit dan hapus -->
                                <a class="btn-tanggapi" href="edit.php?id=<?= $row['id'] ?>">Berikan Tanggapan
                                    <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#fff">
                                        <path d="M160-400v-80h280v80H160Zm0-160v-80h440v80H160Zm0-160v-80h440v80H160Zm360 560v-123l221-220q9-9 20-13t22-4q12 0 23 4.5t20 13.5l37 37q8 9 12.5 20t4.5 22q0 11-4 22.5T863-380L643-160H520Zm300-263-37-37 37 37ZM580-220h38l121-122-18-19-19-18-122 121v38Zm141-141-19-18 37 37-18-19Z" />
                                    </svg>
                                </a>
                                <a class="btn-logout" href="delete.php?id=<?= $row['id'] ?>"
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
        <!-- </div> -->
    </div>
</body>

</html>