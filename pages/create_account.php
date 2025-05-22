<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buat Akun</title>
</head>

<body>
    <h2>Buat AKun</h2>
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
            <label for="">email</label>
            <input type="email" name="email" id="">
        </div>
        <button type="submit">Buat Akun</button>
    </form>
</body>

</html>

<?php

include '../config/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $email = $_POST['email'];

    // Cek apakah username sudah ada di database
    $query = "SELECT * FROM tbl_user WHERE username='$username'";
    $result = mysqli_query($conn, $query);
    if (mysqli_num_rows($result) > 0) {
        echo "Username sudah ada. Silakan pilih username lain.";
    } else {
        // Simpan data ke database
        $query = "INSERT INTO tbl_user (username, password, email) VALUES ('$username', '$password', '$email')";
        if (mysqli_query($conn, $query)) {
            echo "Akun berhasil dibuat. Silakan login.";
        } else {
            echo "Error: " . mysqli_error($koneksi);
        }
    }
}

?>