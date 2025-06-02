<?php

// Mulai sesi
session_start();

// Sertakan file koneksi database
include '../../config/koneksi.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['error'] = "ID tidak valid!";
    header("Location: dashboard_admin.php");
    exit();
}

$id = (int)$_GET['id']; // Ambil ID dari URL

mysqli_begin_transaction($conn); // Memulai transaksi

try {
    //Hapus data terkait di tbl_proses_peng
    $sql1 = "DELETE FROM tbl_proses_peng WHERE id_peng = $id";

    // Hapus data dari tabel tbl_peng
    $sql2 = "DELETE FROM tbl_peng WHERE id = $id";

    // Eksekusi query untuk menghapus data
    if (mysqli_query($conn, $sql1)) { // Eksekusi query
        if (mysqli_query($conn, $sql2)) {
            mysqli_commit($conn);
            $_SESSION['adminhapus_success'] = "Data berhasil dihapus!"; // Set session success message
        } else {
            throw new Exception("Gagal menghapus data pengaduan!");
        }
    }
} catch (Exception $e) {
    mysqli_rollback($conn); // Rollback jika terjadi kesalahan
    $_SESSION['error'] = "Terjadi kesalahan: " . $e->getMessage(); // Set session error message
}

mysqli_close($conn); // Tutup koneksi
header("Location: dashboard_admin.php"); // Redirect ke halaman dashboard
exit(); // Keluar dari script
