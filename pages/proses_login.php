<?php
include '../config/koneksi.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $isilaporan = $_POST['isilaporan'];
    $foto = '';

    if (empty($name)) {
        $_SESSION['error'] = "Nama wajib diisi!";
        header("Location: form.php");
        exit();
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = "Email tidak valid!";
        header("Location: form.php");
        exit();
    } elseif (empty($_POST['isilaporan'])) {
        $_SESSION['error'] = "Isi Laporan wajib diisi!";
        header("Location: form.php");
        exit();
    }

    if (!empty($_FILES['foto']['name'])) {
        $target_dir = "../assets/uploaded_pics";
        $target_file = $target_dir . basename($_FILES["foto"]["name"]);
        $foto = $_FILES["foto"]["name"];

        if (!move_uploaded_file($_FILES['foto']['tmp_name'], $target_file)) {
            $_SESSION['error'] = "Gagal mengupload foto!";
            header("Location: form.php");
            exit();
        }
    }

    $stmt = $conn->prepare("INSERT INTO tbl_peng (nama, email, isi_lap, foto) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $email, $isilaporan, $foto);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Data Berhasil Dikirim!";
    } else {
        $_SESSION['error'] = "Gagal mengirim data!";
    }

    header("Location: form.php");
    exit();
}
