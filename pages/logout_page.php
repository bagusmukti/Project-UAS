<?php
session_start();

if (isset($_SESSION['user_id'])) {
    session_unset();
    session_destroy();

    // Mulai ulang sesi untuk simpan pesan
    session_start();
    $_SESSION['logout_success'] = "Anda berhasil logout.";

    header("Location: ../index.php");
    exit();
}
