<?php

session_start();

include '../../config/koneksi.php';

$id = $_GET['id'];

$sql = "DELETE FROM tbl_peng WHERE id=$id";

if (mysqli_query($conn, $sql)) {
    $_SESSION['success'] = "Data berhasil dihapus!";
    header("Location: dashboard_admin.php");
    exit();
} else {
    echo "Data Gagal Dihapus";
}

mysqli_close($conn);
