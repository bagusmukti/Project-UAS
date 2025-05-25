<?php

// Mulai sesi
session_start();

// Sertakan koneksi database
include '../config/koneksi.php';

$errors = []; // Array untuk menyimpan error

// Jika Formulir metode POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username']; // Ambil data dari form
    $password = $_POST['password']; // Ambil data dari form
    $email = $_POST['email']; // Ambil data dari form


    if (empty($username)) {
        $errors[] = "Username tidak boleh kosong."; // Validasi username
    }

    if (empty($email)) {
        $errors[] = "Email tidak boleh kosong."; // Validasi email
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Email tidak valid."; // Validasi email
    }

    if (empty($password)) {
        $errors[] = "Password tidak boleh kosong."; // Validasi password
    }

    // Jika tidak ada error
    if (empty($errors)) {
        $stmt = mysqli_prepare($conn, "SELECT id FROM tbl_user WHERE username = ?"); // Siapkan statement
        mysqli_stmt_bind_param($stmt, "s", $username); // Bind parameter
        mysqli_stmt_execute($stmt); // Eksekusi statement
        mysqli_stmt_store_result($stmt); // Simpan hasil

        // Cek apakah username sudah terdaftar
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $errors[] = "Username sudah terdaftar."; // Jika username sudah ada
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT); // Hash password

            $insert_stmt = mysqli_prepare($conn, "INSERT INTO tbl_user (username, password, email, level) VALUES (?, ?, ?, 'masyarakat')"); // Siapkan statement untuk insert
            mysqli_stmt_bind_param($insert_stmt, "sss", $username, $hashed_password, $email); // Bind parameter

            // Eksekusi statement untuk insert
            if (mysqli_stmt_execute($insert_stmt)) {
                echo "Akun berhasil dibuat! Silakan login."; // Tampilkan pesan sukses
                header("Location: login_page.php"); // Redirect ke halaman login
                exit();
            } else {
                $errors[] = "Gagal membuat akun."; // Jika gagal insert
            }
        }
    }

    $_SESSION['errors'] = $errors; // Simpan error ke session
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buat Akun</title>
    <link rel="stylesheet" href="../assets/css/style.css">

    <style>
        .error {
            color: red;
        }

        .success {
            color: green;
        }
    </style>
</head>

<body>
    <div class="login">
        <h2 class="h2-user">Buat Akun</h2>

        <?php if (!empty($_SESSION['errors'])): ?>
            <div class="error">
                <?php foreach ($_SESSION['errors'] as $error): ?>
                    <div><?= $error ?></div>
                <?php endforeach; ?>
            </div>
            <?php unset($_SESSION['errors']); // Hapus error setelah ditampilkan
            ?>
        <?php endif; ?>

        <?php if (!empty($success)): ?>
            <div class="success"><?= $success ?></div>
            <?php unset($_SESSION['success']); // Hapus pesan sukses setelah ditampilkan 
            ?>
        <?php endif; ?>

        <form action="" method="post">
            <div>
                <label class="label-user" for="">Username</label>
                <input type="text" name="username" id="">
            </div>
            <div>
                <label class="label-user" for="">Password</label>
                <input type="password" name="password" id="">
            </div>
            <div>
                <label class="label-user" for="">Email</label>
                <input type="email" name="email" id="">
            </div><br>
            <button type="submit" class="button-user">Buat Akun</button><br><br>
        </form>
        <div class="label-login">
            <span>Sudah punya akun?</span>
            <a href="login_page.php">Klik disini</a>
        </div>
    </div>
</body>

</html>