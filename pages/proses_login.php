<?php
session_start();

if (empty($_POST['name'])) {
    echo "Nama wajib diisi";
} else {
    echo "Nama: " . htmlspecialchars($_POST['name']);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $_SESSION['name'] = $_POST['name'];
    echo "<br>Nama tersimpan di session: " . $_SESSION['name'];
}

if (isset($_POST['login'])) {
    $email = $_POST['email'];
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<br>Format email tidak valid!";
    } else {
        echo "<br>Email valid: " . $email;
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['foto'])) {
    $foto = $_FILES['foto']['name'];
    $target_dir = "../Project-UAS/assets/uploaded_pics";
    $target_file = $target_dir . basename($_FILES['foto']['name']);
    if (move_uploaded_file($_FILES['foto']['tmp_name'], $target_file)) {
        echo "<br>File berhasil diupload: " . $target_file;
    } else {
        echo "Gagal upload file.";
    }
}
