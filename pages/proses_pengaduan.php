<?php

// Sertakan file koneksi database
include '../config/koneksi.php';

// Mulai sesi
session_start();

// Cek apakah pengguna sudah login sebagai user
if (!isset($_SESSION['user_id'])) {
    header("Location: login_page.php");
    exit();
}

// Cek apakah form di-submit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id']; // Ambil ID user dari sesi
    $name = $_POST['name']; // Ambil nama dari form
    $email = $_POST['email']; // Ambil email dari form
    $isilaporan = $_POST['isilaporan']; // Ambil isi laporan dari form
    $foto = ''; // Inisialisasi variabel foto

    $errors = [];

    // Validasi input
    if (empty($name)) {
        $errors[] = "Nama wajib diisi!";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Email tidak valid!";
    }

    if (empty($_POST['isilaporan'])) {
        $errors[] = "Isi laporan wajib diisi!";
    }

    // Validasi file foto
    if (!empty($_FILES['foto']['name'])) {
        $target_dir = "../assets/uploaded_pics/"; // Ganti dengan direktori tujuan upload

        $filename = $_FILES['foto']['name'];
        $target_file = $target_dir . $filename;

        if (move_uploaded_file($_FILES['foto']['tmp_name'], $target_file)) {
            $foto = $filename;
        } else {
            $errors[] = "Gagal mengupload file!";
        }
    }


    // Jika ada error, redirect kembali
    if (!empty($errors)) {
        $_SESSION['error'] = implode("<br>", $errors);
        header("Location: dashboard_user.php");
        exit();
    }

    try {
        // Insert data dengan prepared statement
        $stmt = $conn->prepare("INSERT INTO tbl_peng 
            (id_user, nama, email, isi_lap, foto) 
            VALUES (?, ?, ?, ?, ?)");


        $stmt->bind_param(
            "issss",
            $user_id,
            $name,
            $email,
            $isilaporan,
            $foto
        );

        // Eksekusi statement
        if ($stmt->execute()) {
            $_SESSION['success'] = "Laporan berhasil dikirim!";
        } else {
            throw new Exception("Gagal menyimpan data ke database");
        }
    } catch (Exception $e) {
        $_SESSION['error'] = "Terjadi kesalahan sistem: " . $e->getMessage(); // Log error
    } finally {
        $stmt->close(); // Tutup statement
        header("Location: dashboard_user.php"); // Redirect ke halaman dashboard
        exit();
    }
}
