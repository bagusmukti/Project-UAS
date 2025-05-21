<?php

include '../../config/koneksi.php';

// Handle search
$search_nama = $_GET['search_nama'] ?? '';
$search_status = $_GET['search_status'] ?? '';

$query = "SELECT * FROM tbl_peng WHERE 1=1";
$params = [];
$types = '';

if (!empty($search_nama)) {
    $query .= " AND nama LIKE ?";
    $params[] = "%$search_nama%";
    $types .= 's';
}

if (!empty($search_status)) {
    $query .= " AND status = ?";
    $params[] = $search_status;
    $types .= 's';
}

$stmt = $conn->prepare($query);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}


$stmt->execute();
$result = $stmt->get_result();
$complaints = $result->fetch_all(MYSQLI_ASSOC);

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href=".../assets/css/style.css">
</head>

<body>
    <h2>Dashboard Admin</h2>
    <br>
    <form action="" method="get">
        <label for="search_nama">Nama : </label>
        <input type="text" name="search_nama" id="search_nama" value="<?= htmlspecialchars($search_nama) ?>">

        <label for="search_status">Status :</label>
        <select name="search_status" id="search_status">
            <option value="">-- Pilih Status --</option>
            <option value="menunggu" <?= $search_status === 'menunggu' ? 'selected' : '' ?>>Menunggu</option>
            <option value="diproses" <?= $search_status === 'diproses' ? 'selected' : '' ?>>Diproses</option>
            <option value="selesai" <?= $search_status === 'selesai' ? 'selected' : '' ?>>Selesai</option>
        </select>
        <button type="submit">Cari</button>
    </form>
    <table border="1" cellpadding="10" cellspacing="0">
        <tr>
            <td>Nama Pelapor</td>
            <td>Email</td>
            <td>Isi Laporan</td>
            <td>Foto</td>
            <td>Status</td>
            <td>Aksi</td>
        </tr>
        <?php foreach ($complaints as $row): ?>
            <tr>
                <td><?= htmlspecialchars($row["nama"]) ?></td>
                <td><?= htmlspecialchars($row["email"]) ?></td>
                <td><?= htmlspecialchars($row["isi_lap"]) ?></td>
                <td>
                    <?php if (!empty($row['foto'])): ?>
                        <img src="../assets/uploaded_pics/<?= htmlspecialchars($row['foto']) ?>" alt="Laporan Foto" width="100px">
                    <?php else: ?>
                        <p>Tidak ada foto</p>
                    <?php endif; ?>
                </td>
                <td></td><?= htmlspecialchars($row["status"]) ?></td>
                <td colspan="2">
                    <a href="edit.php?id=<?= $row['id'] ?>">Balas</a>
                    <a href="delete.php?id=<?= $row['id'] ?>"
                        onclick="return confirm('Yakin ingin menghapus?')">Hapus</a>
                </td>
            </tr>
        <?php endforeach ?>
    </table>
</body>

</html>