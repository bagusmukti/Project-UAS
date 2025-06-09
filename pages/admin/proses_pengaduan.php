<?php

// Sertakan file koneksi database
include '../../config/koneksi.php';

// Mulai sesi
session_start();

// Cek apakah pengguna sudah login sebagai user
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

// Cek apakah form di-submit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id']; // Ambil ID user dari sesi
    $name = $_POST['name']; // Ambil nama dari form
    $email = $_POST['email']; // Ambil email dari form
    $isilaporan = $_POST['isilaporan']; // Ambil isi laporan dari form
    $file = $_FILES['foto']; // Ambil file foto dari form

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

    // Jika ada error, redirect kembali
    if (!empty($errors)) {
        $_SESSION['error'] = implode("<br>", $errors);
        header("Location: list_pengaduan.php");
        exit();
    }

    try {
        // Mulai transaksi
        $conn->begin_transaction();

        // Simpan data laporan tanpa foto
        // Insert data dengan prepared statement
        $stmt = $conn->prepare("INSERT INTO tbl_peng 
            (id_user, nama, email, isi_lap) 
            VALUES (?, ?, ?, ?)");

        $stmt->bind_param(
            "isss",
            $user_id,
            $name,
            $email,
            $isilaporan
        );

        $stmt->execute();
        $id_laporan = $conn->insert_id; // Ambil ID laporan yang baru saja dimasukkan
        $stmt->close(); // Tutup statement

        // Proses upload foto jika ada
        if (!empty($_FILES['foto']['name'])) {
            $file = $_FILES['foto'];
            $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $target_dir = "../../assets/uploaded_pics/";

            $new_filename = "laporan_$id_laporan" . ($extension ? ".$extension" : "");
            $target_file = $target_dir . $new_filename;

            if (move_uploaded_file($file['tmp_name'], $target_file)) {
                // Update nama file foto di database
                $stmt = $conn->prepare("UPDATE tbl_peng SET foto = ? WHERE id = ?");
                $stmt->bind_param("si", $new_filename, $id_laporan);
                $stmt->execute();
                $stmt->close(); // Tutup statement
            } else {
                $_SESSION['foto_error'] = "Gagal mengupload foto!";
            }
        }

        $conn->commit(); // Commit transaksi
        $_SESSION['success'] = "Laporan berhasil dikirim!"; // Set session success

    } catch (Exception $e) {
        $conn->rollback(); // Rollback transaksi jika terjadi error
        $_SESSION['error'] = "Error: " . $e->getMessage(); // Log error
    } finally {
        header("Location: list_pengaduan.php"); // Redirect ke halaman dashboard
        exit();
    }
}
