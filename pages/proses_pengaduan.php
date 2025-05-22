<?php
include '../config/koneksi.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login_page.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $isilaporan = $_POST['isilaporan'];
    $foto = '';

    $errors = [];

    if (empty($name)) {
        $errors[] = "Nama wajib diisi!";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Email tidak valid!";
    }

    if (empty($_POST['isilaporan'])) {
        $errors[] = "Isi laporan wajib diisi!";
    }

    if (!empty($_FILES['foto']['name'])) {
        $target_dir = "../assets/uploaded_pics";

        $file_ext = pathinfo($_FILES["foto"]["name"], PATHINFO_EXTENSION);
        $filename = uniqid('laporan_', true) . '.' . $file_ext;
        $target_file = $target_dir . $filename;

        $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
        $max_size = 2 * 1024 * 1024; // 2MB

        if (!in_array($_FILES['foto']['type'], $allowed_types)) {
            $errors[] = "Hanya file JPG, PNG, dan GIF yang diizinkan!";
        } elseif ($_FILES['foto']['size'] > $max_size) {
            $errors[] = "Ukuran file maksimal 2MB!";
        } elseif (!move_uploaded_file($_FILES['foto']['tmp_name'], $target_file)) {
            $errors[] = "Gagal mengupload foto!";
        } else {
            $foto = $filename;
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

        if ($stmt->execute()) {
            $_SESSION['success'] = "Laporan berhasil dikirim!";
        } else {
            throw new Exception("Gagal menyimpan data ke database");
        }
    } catch (Exception $e) {
        $_SESSION['error'] = "Terjadi kesalahan sistem: " . $e->getMessage();
    } finally {
        $stmt->close();
        header("Location: dashboard_user.php");
        exit();
    }
}
