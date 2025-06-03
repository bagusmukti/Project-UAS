<?php
session_start();
include '../config/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    // Validasi server side minimal (jaga keamanan)
    if ($username === '' || $password === '') {
        $_SESSION['error'] = "Masukkan data yang sesuai.";
        header("Location: login.php");
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
            header("Location: login.php");
            exit();
        }
    } else {
        $_SESSION['error'] = "Data yang dimasukkan salah.";
        header("Location: login.php");
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
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

  <title>Login</title>
  <style>
    .divider:after,
    .divider:before {
      content: "";
      flex: 1;
      height: 1px;
      background: #eee;
    }
    .login-form-container {
      max-width: 500px;
      width: 100%;
    }

    #img-login{
        width: 350px;
        height: 350px;
        margin-left :  0px;
    }
  </style>
</head>
<body>
  <div class="container py-5">
    <div class="row justify-content-center align-items-center" style="margin-left:50px;">
      <div class="col-lg-7 col-md-8 ">
          <div class="login-form-container bg-white shadow-lg rounded-4 p-4 p-md-5 lg-auto">
              <form id="loginForm" action="" method="post" enctype="multipart/form-data">
                  <h3 class="text-center mb-4">Sign In</h3>
  
            <!-- Email input -->
            <div data-mdb-input-init class="form-outline mb-2">
            <input type="text" name="username" id="username" value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"  class="form-control form-control-xs" />
              <label class="form-label" for="form1Example13">Username</label>
            </div>
  
            <!-- Password input -->
            <div data-mdb-input-init class="form-outline mb-4">
              <input type="password" id="password" name="password" class="form-control form-control-xs" />
              <label class="form-label" for="form1Example23">Password</label>
            </div>
  
            <div class="d-flex justify-content-between align-items-center mb-4">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" id="form1Example3" checked />
                <label class="form-check-label" for="form1Example3"> Remember me </label>
              </div>
              <a href="#!">Forgot password?</a>
            </div>
  
            <!-- Submit button -->
            <button type="submit" data-mdb-button-init data-mdb-ripple-init class="btn btn-primary btn-xs btn-block w-100">Sign in</button>
  
            <div class="divider d-flex align-items-center my-4">
              <!-- <p class="text-center fw-bold mx-3 mb-0 text-muted">OR</p> -->
            </div>
  
          
             <div class="label-login" style="margin-top: 20px; text-align: center;">
                <span>Belum memiliki akun?</span>
                <a href="register.php">Register</a>
            </div>
          </form>
        </div>
      </div>
      <div class="col-lg-4 col-md-8 d-none d-lg-block text-center">
                <img src="/Project-UAS/pages/img/login.png" alt="Login Image" id="img-login" />
          </div>
    </div>
  </div>

  <!-- JS -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/9.0.0/mdb.umd.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
  
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
