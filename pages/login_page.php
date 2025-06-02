<?php
session_start();
include '../config/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    // Validasi server side minimal (jaga keamanan)
    if ($username === '' || $password === '') {
        $_SESSION['error'] = "Masukkan data yang sesuai.";
        header("Location: login_page.php");
        exit();
    }

    $query = "SELECT id, username, level, password FROM tbl_user WHERE username = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($user = mysqli_fetch_assoc($result)) {
        if (password_verify($password, $user['password'])) {
            if ($user['level'] == 'admin') {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['level'] = 'admin';
                $_SESSION['admin_success'] = "Halo " . htmlspecialchars($user['username']) . ", selamat datang!";  // khusus login
                header("Location: ../pages/admin/dashboard_admin.php");
                exit();
            } else {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['level'] = 'masyarakat';
                $_SESSION['user_success'] = "Halo " . htmlspecialchars($user['username']) . ", selamat datang!";  // khusus login
                header("Location: dashboard_user.php");
                exit();
            }
        } else {
            $_SESSION['error'] = "Data yang dimasukkan salah.";
            header("Location: login_page.php");
            exit();
        }
    } else {
        $_SESSION['error'] = "Data yang dimasukkan salah.";
        header("Location: login_page.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Login Page</title>
    <link rel="stylesheet" href="../assets/css/style.css" />
    <link rel="stylesheet" href="../assets/css/butterpop.css" />
</head>

<body>
    <div class="login">
        <h2 class="h2-user">Log In</h2>
        <br>

        <form id="loginForm" action="" method="post" enctype="multipart/form-data">
            <div>
                <label class="label-user" for="username">Username</label>
                <input type="text" name="username" id="username" value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" />
            </div>
            <div>
                <label class="label-user" for="password">Password</label>
                <input type="password" name="password" id="password" />
            </div><br />
            <button type="submit" class="button-user">Log In</button><br><br>
            <div class="label-login">
                <span>Belum punya akun?</span>
                <a href="create_account.php">Buat Akun</a>
            </div>
        </form>
    </div>

    <script src="../assets/js/butterpop.js"></script>
    <script>
        // Validasi form sebelum submit
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const username = document.getElementById('username').value.trim();
            const password = document.getElementById('password').value.trim();

            if (username === '' || password === '') {
                e.preventDefault(); // Cegah submit
                ButterPop.show({
                    message: "Masukkan data yang sesuai.",
                    type: "error",
                    position: "top-right",
                    theme: "velvet",
                    duration: 4000,
                    progress: true,
                    closable: true,
                    pauseOnHover: true,
                    closeOnClick: false
                });
            }
        });

        <?php if (!empty($_SESSION['error'])): ?>
            ButterPop.show({
                message: "<?= htmlspecialchars($_SESSION['error'], ENT_QUOTES) ?>",
                type: "error",
                position: "top-right",
                theme: "velvet",
                duration: 4000,
                progress: true,
                closable: true,
                pauseOnHover: true,
                closeOnClick: false
            });
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

         <?php if (!empty($_SESSION['success'])): ?>
            ButterPop.show({
                message: "<?= htmlspecialchars($_SESSION['success'], ENT_QUOTES) ?>",
                type: "success",
                position: "top-right",
                theme: "default",
                duration: 5000,
                progress: true,
                closable: true,
                pauseOnHover: true,
                closeOnClick: false
            });
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <?php if (!empty($_SESSION['logout_success'])): ?>
            ButterPop.toast.success("<?= htmlspecialchars($_SESSION['logout_success'], ENT_QUOTES) ?>");
            <?php unset($_SESSION['logout_success']); ?>
        <?php endif; ?>
    </script>
</body>

</html>
