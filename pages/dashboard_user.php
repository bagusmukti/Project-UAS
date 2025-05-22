<?php

session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login_page.php");
    exit();
}

include '../config/koneksi.php';

$complaints = [];

try {
    $user_id = $_SESSION['user_id'];

    $query = "SELECT tp.*, s.status
              FROM tbl_peng p
              LEFT JOIN tbl_proses_peng pp ON p.id = pp.id_peng
              LEFT JOIN tbl_status_peng s ON pp.id_status = s.id
              WHERE p.id_user = ?";

    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $complaints = mysqli_fetch_all($result, MYSQLI_ASSOC);
} catch (Exception $e) {
    error_log("Database error: " . $e->getMessage());
    echo "Terjadi kesalahan pada server. Silakan coba lagi nanti.";
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard User</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>

<body>
    <h2>Dashboard User</h2>
    <br>
    <table border="1" cellpadding="10" cellspacing="0">
        <tr>
            <td>Nama Pelapor</td>
            <td>Email</td>
            <td>Isi Laporan</td>
            <td>Foto</td>
            <td>Status</td>
        </tr>
        <?php if (empty($complaints)): ?>
            <tr>
                <td colspan="5" style="text-align: center;">Tidak ada laporan yang ditemukan</td>
            </tr>
        <?php else: ?>
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
                    <td><?= htmlspecialchars($row["status_peng"] ?? 'menunggu') ?></td>
                </tr>
            <?php endforeach ?>
        <?php endif; ?>
    </table>
</body>

</html>