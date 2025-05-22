<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
</head>

<body>
    <h2>User Login</h2>
    <form action="" method="post">
        <div>
            <label for="">username</label>
            <input type="text" name="username" id="">
        </div>
        <div>
            <label for="">password</label>
            <input type="password" name="password" id="">
        </div>
        <div>
            <span>Belum punya akun?</span>
            <a href="create_account.php">Klik disini</a>
        </div>
        <button type="submit">Login</button>
    </form>
</body>

</html>

<?php

session_start();

include '../config/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Cek apakah username dan password valid
    $query = "SELECT * FROM tbl_user WHERE username='$username' AND password='$password'";
    $result = mysqli_query($conn, $query);
    if (mysqli_num_rows($result) > 0) {
        // Login berhasil
        $_SESSION['username'] = $username;
        header("Location: dashboard_user.php");
        exit();
    } else {
        // Login gagal
        echo "Username atau password salah.";
    }
}
?>