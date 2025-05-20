<?php

include '../config/koneksi.php';

// Handle search
$search_nama = $_GET['search_nama'] ?? '';
$search_status = $_GET['search_status'] ?? '';

$query = "SELECT * FROM complaints WHERE 1=1";
$params = [];
$types = '';

if (!empty($search_nama)) {
    $query .= " AND nama_pelapor LIKE ?";
    $params[] = "%$search_nama";
    $types .= 's';
}

if (!empty($search_status)) {
    $query .= " AND status = ?";
    $params[] = $search_status;
    $types .= 's';
}

$stmt = $conn->prepare($query);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
$complaints = $result->fetch_all(MYSQLI_ASSOC);
