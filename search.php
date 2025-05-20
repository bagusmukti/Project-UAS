<?php

$nama = isset($_GET['name']) ? mysqli_real_escape_string($conn, $_GET['nama']) : '';
$status = isset($_GET['status']) ? mysqli_real_escape_string($conn, $_GET['status']) : '';

$query = "SELECT p.*, s.nama_status
            FROM pengaduan p
            LEFT JOIN status_pengaduan s ON p.status_id = s.id
            WHERE 1=1";

if (!empty($nama)) {
    $query .= " AND p.nama_pelapor LIKE '%$nama%'";
}

if (!empty($status)) {
    $query .= " AND s.id = '$status'";
}

$result = mysqli_query($conn, $query);
