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
        <h2 class="h2-user">FORMULIR PENGADUAN</h2>
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
            <label for="foto" class="label-user">Pilih file foto : </label><br>
            <input type="file" name="foto" accept="images/"><br><br>
        </div>
        <button type="submit" class="button-user">Send</button>
    </form>
</body>

</html>