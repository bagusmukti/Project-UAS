<?php

// Mulai sesi
session_start();

// Sertakan file koneksi database
include '../config/koneksi.php';

// Jika Formulir metode POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username']; // Ambil data dari form
    $password = $_POST['password']; // Ambil data dari form

    // Cek apakah username dan password valid
    $query = "SELECT id, username, level, password FROM tbl_user WHERE username = ?"; // Siapkan statement SELECT
    $stmt = mysqli_prepare($conn, $query); // Siapkan statement
    mysqli_stmt_bind_param($stmt, "s", $username); // Bind parameter
    mysqli_stmt_execute($stmt); // Eksekusi statement
    $result = mysqli_stmt_get_result($stmt); // Ambil hasil query

    // Cek apakah ada hasil
    if ($user = mysqli_fetch_assoc($result)) {
        if (password_verify($password, $user['password'])) { // Verifikasi password
            if ($user['level'] == 'admin') // Cek level user 
            {
                // Jika level admin
                $_SESSION['user_id'] = $user['id']; // Simpan ID user ke session
                $_SESSION['level'] = 'admin'; // Simpan level user ke session
                header("Location: ../pages/admin/dashboard_admin.php"); // Redirect ke halaman dashboard admin
                exit();
            } else {
                // Jika level masyarakat
                $_SESSION['user_id'] = $user['id']; // Simpan ID user ke session
                $_SESSION['level'] = 'masyarakat'; // Simpan level user ke session
                header("Location: dashboard_user.php"); // Redirect ke halaman dashboard user
                exit();
            }
        } else {
            echo "Username atau Password salah."; // Jika password tidak valid
        }
    } else {
        // Login gagal
        echo "Username atau password salah.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>
    <div class="login">
        <h2 class="h2-user">Log In</h2>
        <br>

        <?php if (!empty($error)): ?>
            <div style="color: red;"><?= $error ?></div>
        <?php endif; ?>

        <form action="" method="post" enctype="multipart/form-data">
            <div>
                <label class="label-user" for="">Username</label>
                <input type="text" name="username" id="">
            </div>
            <div>
                <label class="label-user" for="">Password</label>
                <input type="password" name="password" id="">
            </div><br>
            <button type="submit" class="button-user">Log In</button><br><br>
            <div class="label-login">
                <span>Belum punya akun?</span>
                <a href="create_account.php">Klik disini</a>
            </div>
        </form>
    </div>
</body>

</html>