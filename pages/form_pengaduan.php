<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ayok Ngadu ke Cruxit</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>
    <form action="proses_pengaduan.php" method="POST" enctype="multipart/form-data" class="form-user">
        <a href="dashboard_user.php" class="button-user-back">
            <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#fff">
                <path d="m480-320 56-56-64-64h168v-80H472l64-64-56-56-160 160 160 160Zm0 240q-83 0-156-31.5T197-197q-54-54-85.5-127T80-480q0-83 31.5-156T197-763q54-54 127-85.5T480-880q83 0 156 31.5T763-763q54 54 85.5 127T880-480q0 83-31.5 156T763-197q-54 54-127 85.5T480-80Zm0-80q134 0 227-93t93-227q0-134-93-227t-227-93q-134 0-227 93t-93 227q0 134 93 227t227 93Zm0-320Z" />
            </svg>
            Kembali
        </a>

        <h2 class="h2-user">FORMULIR PENGADUAN</h2>

        <br>
        <div>
            <label form="" class="label-user">Name</label>
            <input type="text" name="name"><br>
        </div>
        <div>
            <label form="" class="label-user">Email</label>
            <input type="text" name="email"><br>
        </div>
        <div>
            <label form="" class="label-user">Isi Laporan</label>
            <textarea name="isilaporan"></textarea><br>
        </div>
        <div>
            <label for="foto" class="label-user">Pilih file foto : </label>
            <input type="file" name="foto" accept="images/"><br><br>
        </div>
        <button type="submit" class="button-user">Send</button>
    </form>
</body>

</html>