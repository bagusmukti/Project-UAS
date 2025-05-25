<?php

// Mulai sesi
session_start();

// Cek apakah user admin sudah login
if (isset($_SESSION['user_id'])) {
    session_unset(); // Hapus semua variabel sesi
    session_destroy(); // Hancurkan sesi
    header("Location: index.php"); // Redirect ke halaman utama
    exit();
}
