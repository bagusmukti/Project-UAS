<?php

// Mulai sesi
session_start();

// Sertakan file koneksi database
include '../../config/koneksi.php';

$id = $_GET['id']; // Ambil ID dari URL

$sql = "DELETE FROM tbl_peng WHERE id=$id"; // Query untuk menghapus data

if (mysqli_query($conn, $sql)) { // Eksekusi query
    // Jika berhasil menghapus data, set session success message
    $_SESSION['success'] = "Data berhasil dihapus!"; // Set session success message
    header("Location: dashboard_admin.php"); // Redirect ke halaman dashboard
    exit();
} else {
    echo "Data Gagal Dihapus"; // Jika gagal menghapus data
}

mysqli_close($conn); // Tutup koneksi
