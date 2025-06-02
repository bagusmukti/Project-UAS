<?php
session_start();
include '../config/koneksi.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $email    = trim($_POST['email'] ?? '');

    if ($username === '') {
        $errors[] = "Username tidak boleh kosong.";
    }

    if ($email === '') {
        $errors[] = "Email tidak boleh kosong.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Email tidak valid.";
    }

    if ($password === '') {
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
                $_SESSION['success'] = "Akun berhasil dibuat! Silakan login.";
                header("Location: create_account.php");
                exit();
            } else {
                $errors[] = "Gagal membuat akun.";
            }
        }
    }

    $_SESSION['errors'] = $errors;
    header("Location: create_account.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <title>Buat Akun</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="stylesheet" href="../assets/css/style.css" />
    <link rel="stylesheet" href="../assets/css/butterpop.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<body>

    <div class="login">
        <h2 class="h2-user">Buat Akun</h2>
        <form action="" method="post">
            <div>
                <label class="label-user">Username</label>
                <input type="text" name="username" value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" />
            </div>
            <div>
                <label class="label-user">Password</label>
                <input type="password" name="password" />
            </div>
            <div>
                <label class="label-user">Email</label>
                <input type="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" />
            </div><br />
            <button type="submit" class="button-user">Buat Akun</button><br /><br />
        </form>
        <div class="label-login">
            <span>Sudah memiliki akun?</span>
            <a href="login_page.php">Login</a>
        </div>
    </div>

    <script src="../assets/js/butterpop.js"></script>
    <script>
        <?php if (!empty($_SESSION['errors'])): ?>
            <?php foreach ($_SESSION['errors'] as $err): ?>
                ButterPop.show({
                    message: "<?= htmlspecialchars($err, ENT_QUOTES) ?>",
                    type: "error",
                    position: "top-right",
                    theme: "velvet",
                    duration: 4000,
                    progress: true,
                    closable: true,
                    pauseOnHover: true,
                    closeOnClick: false
                });
            <?php endforeach; ?>
            <?php unset($_SESSION['errors']); ?>
        <?php endif; ?>

        <?php if (!empty($_SESSION['success'])): ?>
            ButterPop.show({
                message: "<?= htmlspecialchars($_SESSION['success'], ENT_QUOTES) ?>",
                type: "success",
                position: "top-right",
                theme: "velvet",
                duration: 5000,
                progress: true,
                closable: true,
                pauseOnHover: true,
                closeOnClick: false
            });
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>
    </script>

</body>
</html>
