<?php
session_start();
include '../../config/koneksi.php';

if (!isset($_SESSION['loggedin'])) {
    header('Location: ../login.php');
    exit;
}

$id = $_GET['id'];
$stmt = $conn->prepare("SELECT * FROM complaints WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$complaint = $stmt->get_result()->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $status = $_POST['status'];
    $balasan = $_POST['balasan'];

    $stmt = $conn->prepare("UPDATE complaints SET status = ?, balasan = ? WHERE id = ?");
    $stmt->bind_param("ssi", $status, $balasan, $id);
    $stmt->execute();

    header('Location: dashboard.php');
    exit;
}
?>

<div class="container">
    <h2>Edit Pengaduan</h2>

    <form method="POST">
        <div class="form-group">
            <label>Status</label>
            <select name="status" class="form-control">
                <option value="menunggu" <?= $complaint['status'] == 'menunggu' ? 'selected' : '' ?>>Menunggu</option>
                <option value="diproses" <?= $complaint['status'] == 'diproses' ? 'selected' : '' ?>>Diproses</option>
                <option value="selesai" <?= $complaint['status'] == 'selesai' ? 'selected' : '' ?>>Selesai</option>
            </select>
        </div>
        <div class="form-group">
            <label>Balasan</label>
            <textarea name="balasan" class="form-control" rows="5"><?= htmlspecialchars($complaint['balasan']) ?></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
    </form>
</div>