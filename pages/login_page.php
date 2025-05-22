<?php

session_start();

include '../config/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Cek apakah username dan password valid
    $query = "SELECT id, username, level, password FROM tbl_user WHERE username = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($user = mysqli_fetch_assoc($result)) {
        if (password_verify($password, $user['password'])) {
            if($user['level'] == 'admin')
            {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['level'] = 'admin';
                header("Location: ../pages/admin/dashboard_admin.php");
                exit();    
            }
            else {
                $_SESSION['user_id'] = $user['id'];
                header("Location: dashboard_user.php");
                exit();
            }

        } else {
            echo "Username atau Password salah.";
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
</head>

<body>
    <h2>User Login</h2>
    <?php if (!empty($error)): ?>
        <div style="color: red;"><?= $error ?></div>
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
            <span>Belum punya akun?</span>
            <a href="create_account.php">Klik disini</a>
        </div>
        <button type="submit">Login</button>
    </form>
</body>

</html>