<?php
session_start();
include '../../config/koneksi.php';

// Cek session admin
if (!isset($_SESSION['user_id']) || $_SESSION['level'] !== 'admin') {
    header("Location: ../login_page.php");
    exit();
}

// Handle search
$search_nama = $_GET['search_nama'] ?? '';
$search_status = $_GET['search_status'] ?? '';

$query = "SELECT p.*, s.status 
          FROM tbl_peng p 
          LEFT JOIN tbl_proses_peng pp ON p.id = pp.id_peng 
          LEFT JOIN tbl_status_peng s ON pp.id_status = s.id
          WHERE 1=1";

$params = [];
$types = '';

if (!empty($search_nama)) {
    $query .= " AND p.nama LIKE ?";
    $params[] = "%$search_nama%";
    $types .= 's';
}

if (!empty($search_status)) {
    $query .= " AND s.status = ?";
    $params[] = $search_status;
    $types .= 's';
}

$stmt = $conn->prepare($query);
if ($params) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();
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

    <br>
    <form action="" method="get">
    <a href="../logout_page.php">Logout</a>

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

    <table border="1" cellpadding="10" cellspacing="0">
        <tr>
            <th>Nama</th>
            <th>Email</th>
            <th>Isi Laporan</th>
            <th>Foto</th>
            <th>Status</th>
            <th>Aksi</th>
        </tr>
        <?php if (empty($complaints)): ?>
            <tr>
                <td colspan="6">Tidak ada data ditemukan</td>
            </tr>
        <?php else: ?>
            <?php foreach ($complaints as $row): ?>
                <tr>
                    <td><?= htmlspecialchars($row['nama']) ?></td>
                    <td><?= htmlspecialchars($row['email']) ?></td>
                    <td><?= htmlspecialchars($row['isi_lap']) ?></td>
                    <td>
                        <?php if (!empty($row['foto'])): ?>
                            <img src="../assets/uploaded_pics/<?= htmlspecialchars($row['foto']) ?>"
                                alt="Laporan <?= $row['id'] ?>">
                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($row['status'] ?? 'menunggu') ?></td>
                    <td class="action-links">
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