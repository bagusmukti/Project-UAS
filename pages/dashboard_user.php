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

    // Perbaikan query dengan alias yang konsisten dan COALESCE untuk status default
    $query = "SELECT p.*, COALESCE(s.status, 'menunggu') AS status
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
    <style>
        .alert {
            padding: 15px;
            margin: 10px 0;
            border-radius: 4px;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
        }

        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
        }

        img {
            max-width: 100px;
            height: auto;
        }
    </style>
</head>

<body>
    <h2>Dashboard User</h2>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success"><?= $_SESSION['success'] ?></div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-error"><?= $_SESSION['error'] ?></div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <a href="form_pengaduan.php">Buat Aduan</a>

    <table border="1" cellpadding="10" cellspacing="0">
        <tr>
            <th>Nama Pelapor</th>
            <th>Email</th>
            <th>Isi Laporan</th>
            <th>Foto</th>
            <th>Status</th>
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
                            <img src="../assets/uploaded_pics/<?= htmlspecialchars($row['foto']) ?>"
                                alt="Laporan Foto"
                                loading="lazy">
                        <?php else: ?>
                            <p>Tidak ada foto</p>
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($row["status"]) ?></td>
                </tr>
            <?php endforeach ?>
        <?php endif; ?>
    </table>
</body>

</html>