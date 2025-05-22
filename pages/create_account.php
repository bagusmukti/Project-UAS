<?php

include '../config/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $email = $_POST['email'];

    $errors = [];

    if (empty($username)) {
        $errors[] = "Username tidak boleh kosong.";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Email tidak valid.";
    }

    if (empty($password)) {
        $errors[] = "Password tidak boleh kosong.";
    }

    if (empty($errors)) {
        $stmt = mysqli_prepare($conn, "SELECT id FROM tbl_user WHERE username = ?");
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);

        if (mysqli_stmt_num_rows($stmt) > 0) {
            $errors[] = "Username sudah terdaftar.";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            $insert_stmt = mysqli_prepare($conn, "INSERT INTO tbl_user (username, password, email, level) VALUES (?, ?, ?, 'masyarakat')");
            mysqli_stmt_bind_param($insert_stmt, "sss", $username, $hashed_password, $email);

            if (mysqli_stmt_execute($insert_stmt)) {
                echo "Akun berhasil dibuat! Silakan login.";
                header("Location: login_page.php");
                exit();
            } else {
                $errors[] = "Gagal membuat akun.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buat Akun</title>
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
    <h2>Buat AKun</h2>

    <?php if (!empty($error)): ?>
        <div class="error"><?= $error ?></div>
    <?php endif; ?>

    <?php if (!empty($success)): ?>
        <div class="success"><?= $success ?></div>
    <?php endif; ?>

    <form action="" method="post">
        <div>
            <label for="">Username</label>
            <input type="text" name="username" id="">
        </div>
        <div>
            <label for="">Password</label>
            <input type="password" name="password" id="">
        </div>
        <div>
            <label for="">Email</label>
            <input type="email" name="email" id="">
        </div>
        <button type="submit">Buat Akun</button>
    </form>
    <div>
        <span>Sudah punya akun?</span>
        <a href="login_page.php">Klik disini</a>
    </div>
</body>

</html>