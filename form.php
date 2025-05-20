<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ayok Ngadu ke Cruxit</title>
</head>

<body>
    <form action="proseslogin.php" method="POST" enctype="multipart/form-data">
        <div>
            <label form="">Name</label>
            <input type="text" name="name"><br>
        </div>
        <div>
            <label form="">Email</label>
            <input type="text" name="email"><br>
        </div>
        <div>
            <label form="">Isi Laporan</label>
            <textarea> type="text" n><br>
            <textarea name="isilaporan"></textarea>
        </div>
        <div>
            <label for="foto">Pilih file foto : </label><br>
            <input type="file" name="foto" accept="images/"><br><br>
        </div>
        <button type="submit">Send</button>
    </form>
</body>

</html>