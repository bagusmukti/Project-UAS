<?php
$servername = "localhost"; //Menginisialisasi hostname menjadi variabel
$username = "root"; //Menginisialisasi username menjadi variabel
$password = ""; //Menginisialisasi password menjadi variabel
$dbname = ""; //Menginisialisasi nama database menjadi variabel MENUNGGU ZAHRA

//Melakukan koneksi dengan database yang sudah dibuat dengan memasukkan hotsnmae, username, password, dan nama database
$conn = mysqli_connect($servername, $username, $password, $dbname);

//Melakukan pengondisian terhadap hasil koneksi
if (!$conn) {
    //Jika Koneksi gagal akan menampilkan pesan error
    die("Koneksi Gagal : " . mysqli_connect_error());
}
