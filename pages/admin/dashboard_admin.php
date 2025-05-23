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
$search_status = $_GET['search_status'] ?? '';

// Query untuk menampilkan data pengaduan
// Menggunakan LEFT JOIN untuk mendapatkan status pengaduan
$query = "SELECT p.*, s.status 
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
    $query .= " AND s.status = ?"; // Menggunakan status yang dipilih
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
    <style>
        img {
            max-width: 100px;
            height: auto;
        }

        .action-links a {
            margin-right: 10px;
        }
    </style>
</head>

<body>
    <h2>Dashboard Admin</h2>

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
    <form action="" method="get">
        <a href="../logout_page.php">Logout</a>

        <!-- Tabel untuk mencari aduan-->
        <form method="get" style="margin: 20px 0;">
            <input type="text" name="search_nama" placeholder="Cari nama..."
                value="<?= htmlspecialchars($search_nama) ?>">

            <select name="search_status">
                <option value="">Semua Status</option>
                <option value="menunggu" <?= $search_status === 'menunggu' ? 'selected' : '' ?>>Menunggu</option>
                <option value="proses" <?= $search_status === 'proses' ? 'selected' : '' ?>>Proses</option>
                <option value="selesai" <?= $search_status === 'selesai' ? 'selected' : '' ?>>Selesai</option>
            </select>

            <button type="submit">Filter</button>
        </form>

        <!-- Tabel untuk menampilkan data pengaduan -->
        <h3>Data Pengaduan</h3>
        <table border="1" cellpadding="10" cellspacing="0">
            <tr>
                <th>Nama</th>
                <th>Email</th>
                <th>Isi Laporan</th>
                <th>Foto</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
            <?php if (empty($complaints)): ?> <!-- Jika tidak ada data -->
                <tr>
                    <td colspan="6">Tidak ada data ditemukan</td>
                </tr>
            <?php else: ?> <!-- Jika ada data -->
                <?php foreach ($complaints as $row): ?> <!-- Looping data -->
                    <tr>
                        <td><?= htmlspecialchars($row['nama']) ?></td>
                        <td><?= htmlspecialchars($row['email']) ?></td>
                        <td><?= htmlspecialchars($row['isi_lap']) ?></td>
                        <td>
                            <?php if (!empty($row['foto'])): ?> <!-- Jika ada foto -->
                                <img src="../assets/uploaded_pics/<?= htmlspecialchars($row['foto']) ?>"
                                    alt="Laporan <?= $row['id'] ?>"
                                    loading="lazy">
                            <?php else: ?> <!-- Jika tidak ada foto -->
                                -
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($row['status'] ?? 'menunggu') ?></td> <!-- Menampilkan status -->
                        <td class="action-links"> <!-- Tindakan untuk edit dan hapus -->
                            <a href="edit.php?id=<?= $row['id'] ?>">Edit</a>
                            <a href="delete.php?id=<?= $row['id'] ?>"
                                onclick="return confirm('Yakin menghapus laporan ini?')">Hapus</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </table>
</body>

</html>