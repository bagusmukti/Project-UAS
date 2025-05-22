<?php

session_start();

if(isset($_SESSION['user_id']))
{
    session_unset(); // Hapus semua variabel session
    session_destroy(); // Hapus session data
    header("Location: ../pages/login_page.php");
    exit();
}



?>