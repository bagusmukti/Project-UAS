<?php
session_start();
include '../config/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($username === '' || $email === '' || $password === '') {
        $_SESSION['error'] = "Semua kolom wajib diisi.";
        header("Location: register.php");
        exit();
    }

    // Cek apakah username atau email sudah ada
    $checkQuery = "SELECT id FROM tbl_user WHERE username = ? OR email = ?";
    $stmt = mysqli_prepare($conn, $checkQuery);
    mysqli_stmt_bind_param($stmt, "ss", $username, $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_fetch_assoc($result)) {
        $_SESSION['error'] = "Username atau Email sudah terdaftar.";
        header("Location: register.php");
        exit();
    }

    // Hash password & simpan user
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $insertQuery = "INSERT INTO tbl_user (username, email, password, level) VALUES (?, ?, ?, 'masyarakat')";
    $stmt = mysqli_prepare($conn, $insertQuery);
    mysqli_stmt_bind_param($stmt, "sss", $username, $email, $hashedPassword);

    if (mysqli_stmt_execute($stmt)) {
        $_SESSION['success'] = "Pendaftaran berhasil! Silakan login.";
        header("Location: login_page.php");
        exit();
    } else {
        $_SESSION['error'] = "Terjadi kesalahan saat mendaftar.";
        header("Location: register.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet"/>   
  <link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700&display=swap" rel="stylesheet"/>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/9.0.0/mdb.min.css" rel="stylesheet"/>  
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">

  <title>Register</title>
  <style>
    .divider:after,
    .divider:before {
      content: "";
      flex: 1;
      height: 1px;
      background: #eee;
    }
    .register-form-container {
      max-width: 600px;
      width: 100%;
    }
      #img-regis{
        width: 350px;
        height: 350px;
        margin-left :  50px;
    }
  </style>
</head>
<body>
  <div class="container py-5">
    <div class="row justify-content-center align-items-center">
      <div class="col-lg-4 d-none d-lg-block text-center">
            <img src="/Project-UAS/pages/img/regis.png" alt="Login Image" id="img-regis" />
      </div>
      <div class="col-lg-8 col-md-8">
        <div class="register-form-container bg-white shadow-lg rounded-4 p-4 p-md-5 mx-auto">
          <form action="" method="post">
            <h3 class="text-center mb-4">Sign Up</h3>

            <!-- Username input -->
            <div class="form-outline mb-3">
              <input type="text" name="username" id="username" class="form-control form-control-xs" value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" required />
              <label class="form-label" for="username">Username</label>
            </div>

            <!-- Email input -->
            <div class="form-outline mb-3">
              <input type="email" name="email" id="email" class="form-control form-control-xs" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required />
              <label class="form-label" for="email">Email</label>
            </div>

            <!-- Password input -->
            <div class="form-outline mb-4">
              <input type="password" name="password" id="password" class="form-control form-control-xs" required />
              <label class="form-label" for="password">Password</label>
            </div>

            <!-- Submit button -->
            <button type="submit" class="btn btn-primary btn-xs w-100">Sign Up</button>

            <div class="divider d-flex align-items-center my-4">
              <p class="text-center fw-bold mx-3 mb-0 text-muted">OR</p>
            </div>

            <a class="btn btn-primary btn-xs w-100 mb-2" style="background-color: #3b5998" href="#!" role="button">
              <i class="fab fa-facebook-f me-2"></i>Continue with Facebook
            </a>
            <a class="btn btn-primary btn-xs w-100" style="background-color: #55acee" href="#!" role="button">
              <i class="fab fa-twitter me-2"></i>Continue with Twitter
            </a>

          </form>
        </div>
      </div>
    </div>
  </div>

  <!-- JS -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/9.0.0/mdb.umd.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>