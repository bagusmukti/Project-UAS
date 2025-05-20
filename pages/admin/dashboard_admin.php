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
    $params[] = "%$search_nama";
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

var_dump($complaints); // Debug, ngko hapus

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
</head>

<body>
    <h1>Dashboard Admin</h1>
    <br>
    <table border="1">
        <tr>
            <td>Nama Pelapor</td>
            <td>Email</td>
            <td>Isi Laporan</td>
            <td>Foto</td>
            <td>Aksi</td>
        </tr>
        <?php foreach ($complaints as $row): ?>
            <tr>
                <td><?= $row["nama"] ?></td>
                <td><?= $row["email"] ?></td>
                <td><?= $row["isi_lap"] ?></td>
                <td><?= $row["foto"] ?></td>
                <td>
                    <a href="/balas.php/<?= $row["id"] ?>">Balas</a>
                </td>
            </tr>
        <?php endforeach ?>
    </table>
</body>

</html>