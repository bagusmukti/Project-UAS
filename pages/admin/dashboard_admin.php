<?php

// mulai sesi
session_start();

// Cek apakah user admin sudah login
include '../../config/koneksi.php';

// Cek session user admin
if (!isset($_SESSION['user_id']) || $_SESSION['level'] !== 'admin') {
    header("Location: ../login_page.php");
    exit();
}

// fitur search
$search_nama = $_GET['search_nama'] ?? '';
$search_status = isset($_GET['search_status']) ? strtolower($_GET['search_status']) : '';

// Query untuk menampilkan data pengaduan
// Menggunakan LEFT JOIN untuk mendapatkan status pengaduan
$query = "SELECT p.*, COALESCE(LOWER(s.status), 'menunggu') AS status 
          FROM tbl_peng p 
          LEFT JOIN tbl_proses_peng pp ON p.id = pp.id_peng 
          LEFT JOIN tbl_status_peng s ON pp.id_status = s.id
          WHERE 1=1";

// Menambahkan filter berdasarkan nama dan status
$params = [];
$types = ''; // Tipe data untuk bind_param

// Filter berdasarkan nama
if (!empty($search_nama)) {
    $query .= " AND p.nama LIKE ?"; // Menggunakan LIKE untuk pencarian
    $params[] = "%$search_nama%"; // Menambahkan wildcard untuk LIKE
    $types .= 's'; // Tipe data string
}

// Filter berdasarkan status
if (!empty($search_status)) {
    $query .= " AND (COALESCE(LOWER(s.status), 'menunggu') = ?)"; // Menggunakan status yang dipilih
    $params[] = $search_status; // Menambahkan status ke parameter
    $types .= 's'; // Tipe data string
}

// Menambahkan urutan berdasarkan ID pengaduan
$stmt = $conn->prepare($query); // Menyiapkan statement
if ($params) {
    $stmt->bind_param($types, ...$params); // Mengikat parameter
}

// Eksekusi query
$stmt->execute();
// Mendapatkan hasil
$result = $stmt->get_result();
// Mengambil semua data pengaduan
$complaints = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$currentYear = date('Y');
//$queryChart = "SELECT DATE_FORMAT(created_at, '%M') as month, COUNT(*) as count FROM tbl_peng WHERE YEAR(created_at) = $currentYear GROUP BY MONTH(created_at) ORDER BY created_at";
$queryChart = "SELECT MONTH(created_at) as month, COUNT(*) as count FROM tbl_peng WHERE YEAR(created_at) = $currentYear GROUP BY MONTH(created_at) ORDER BY created_at";
$stmt = $conn->prepare($queryChart);
$stmt->execute();
$chartData = $stmt->get_result();
$stmt->close();

$chartRows = [];
while ($row = $chartData->fetch_assoc()) {
    $chartRows[] = $row;
}

$queryChart2 = "SELECT 
    COALESCE(pp.id_status, 1) AS id_status, 
    COUNT(*) as count
FROM tbl_peng p
LEFT JOIN tbl_proses_peng pp ON p.id = pp.id_peng
GROUP BY COALESCE(pp.id_status, 1)";

$stmt = $conn->prepare($queryChart2);
$stmt->execute();
$chartData2 = $stmt->get_result();
$stmt->close();

$chartRows2 = [];
while ($row = $chartData2->fetch_assoc()) {
    $chartRows2[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="../../assets/css/butterpop.css">

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
</head>

<body>
    <div class="wrapper d-flex" style="height: 100vh;">
        <aside class="sidebar p-3" style="background-color:#0e468b; color:rgb(255, 255, 255); height: 100%;" >
            <h1 class="mb-3" style="color:rgb(255, 255, 255) !important; text-decoration: none; padding-left: 15px;">S!AP</h1>
            <nav class="nav flex-column">
                <a class="nav-link active" href="dashboard_admin.php" style="color:rgb(255, 255, 255) !important; text-decoration: none;">Dashboard</a>
                <a class="nav-link active" href="list_pengaduan.php" style="color:rgb(255, 255, 255) !important; text-decoration: none;">Data Pengaduan</a>
                

                <a class="mt-auto btn-logout" href="../logout_page.php">
                    <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="white" style="transform: scaleX(-1);">
                        <path d="M200-120q-33 0-56.5-23.5T120-200v-560q0-33 23.5-56.5T200-840h280v80H200v560h280v80H200Zm440-160-55-58 102-102H360v-80h327L585-622l55-58 200 200-200 200Z" />
                    </svg>
                    Logout
                </a>
            </nav>
        </aside>

        <div class="main-container">
            <div class="header-user-admin">
                <div class="btn-group">
                    <div class="user-info">
                        <p>Admin</p>
                        <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#2a6ec1">
                            <path d="M234-276q51-39 114-61.5T480-360q69 0 132 22.5T726-276q35-41 54.5-93T800-480q0-133-93.5-226.5T480-800q-133 0-226.5 93.5T160-480q0 59 19.5 111t54.5 93Zm246-164q-59 0-99.5-40.5T340-580q0-59 40.5-99.5T480-720q59 0 99.5 40.5T620-580q0 59-40.5 99.5T480-440Zm0 360q-83 0-156-31.5T197-197q-54-54-85.5-127T80-480q0-83 31.5-156T197-763q54-54 127-85.5T480-880q83 0 156 31.5T763-763q54 54 85.5 127T880-480q0 83-31.5 156T763-197q-54 54-127 85.5T480-80Zm0-80q53 0 100-15.5t86-44.5q-39-29-86-44.5T480-280q-53 0-100 15.5T294-220q39 29 86 44.5T480-160Zm0-360q26 0 43-17t17-43q0-26-17-43t-43-17q-26 0-43 17t-17 43q0 26 17 43t43 17Zm0-60Zm0 360Z" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="chart-container mb-3">
                <div class="container-fluid px-4 mt-2">
                <!-- Total Pengaduan -->
                <div class="row mb-3">
                    <div class="col">
                        <div class="card text-white bg-primary shadow-sm">
                            <div class="card-body d-flex justify-content-between align-items-center">
                                <span>Total Pengaduan</span>
                                <h5 class="mb-0"><?= count($complaints) ?></h5>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Rincian Status -->
                <div class="row g-3">
                    <?php
                    $statusCounts = ['menunggu' => 0, 'proses' => 0, 'selesai' => 0];
                    foreach ($complaints as $row) {
                        $status = strtolower($row['status']);
                        if (array_key_exists($status, $statusCounts)) {
                            $statusCounts[$status]++;
                        }
                    }
                    ?>

                    <div class="col-md-4">
                        <div class="card text-dark bg-warning shadow-sm">
                            <div class="card-body d-flex justify-content-between align-items-center">
                                <span>Menunggu</span>
                                <h5 class="mb-0"><?= $statusCounts['menunggu'] ?></h5>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card text-white bg-info shadow-sm">
                            <div class="card-body d-flex justify-content-between align-items-center">
                                <span>Diproses</span>
                                <h5 class="mb-0"><?= $statusCounts['proses'] ?></h5>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card text-white bg-success shadow-sm">
                            <div class="card-body d-flex justify-content-between align-items-center">
                                <span>Selesai</span>
                                <h5 class="mb-0"><?= $statusCounts['selesai'] ?></h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
            <div class="chart-container">
                
                <div>
                    <!-- Chart1 -->
                    <div style="width: 400px;"><canvas id="line"></canvas></div>
                </div>
                <div>
                    <!-- Chart2 -->
                    <h6 style="text-align: center;">Data proses pengaduan</h6>
                    <div style="width: 300px;"><canvas id="doughnut"></canvas></div>
                </div>
            </div>
        </div>
    </div>


    <script src="../../assets/js/butterpop.js"></script>

    <script>
        <?php if (!empty($_SESSION['admin_success'])): ?>
            ButterPop.show({
                message: "<?= $_SESSION['admin_success'] ?>",
                type: "success",
                position: "bottom-right",
                theme: "velvet",
                duration: 4000,
                progress: true,
                closable: true,
                pauseOnHover: true,
                closeOnClick: false
            });
            <?php unset($_SESSION['admin_success']); // Hapus supaya notif tidak muncul lagi 
            ?>
        <?php endif; ?>

        <?php if (!empty($_SESSION['editadmin_success'])): ?>
            ButterPop.show({
                message: "<?= $_SESSION['editadmin_success'] ?>",
                type: "success",
                position: "bottom-right",
                theme: "velvet",
                duration: 4000,
                progress: true,
                closable: true,
                pauseOnHover: true,
                closeOnClick: false
            });
            <?php unset($_SESSION['editadmin_success']); // Hapus supaya notif tidak muncul lagi 
            ?>
        <?php endif; ?>

        <?php if (!empty($_SESSION['adminhapus_success'])): ?>
            ButterPop.show({
                message: "<?= $_SESSION['adminhapus_success'] ?>",
                type: "success",
                position: "bottom-right",
                theme: "velvet",
                duration: 4000,
                progress: true,
                closable: true,
                pauseOnHover: true,
                closeOnClick: false
            });
            <?php unset($_SESSION['adminhapus_success']); // Hapus supaya notif tidak muncul lagi 
            ?>
        <?php endif; ?>

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
    </script>

    <!-- Script untuk Chart -->
    <script>
        // LINE CHART
        (async function() {
            const data_db = <?= json_encode($chartRows) ?>;
            const data = [{
                    month: 'Januari',
                    count: 0
                },
                {
                    month: 'Februari',
                    count: 0
                },
                {
                    month: 'Maret',
                    count: 0
                },
                {
                    month: 'April',
                    count: 0
                },
                {
                    month: 'Mei',
                    count: 0
                },
                {
                    month: 'Juni',
                    count: 0
                },
                {
                    month: 'Juli',
                    count: 0
                },
                {
                    month: 'Agustus',
                    count: 0
                },
                {
                    month: 'September',
                    count: 0
                },
                {
                    month: 'Oktober',
                    count: 0
                },
                {
                    month: 'November',
                    count: 0
                },
                {
                    month: 'Desember',
                    count: 0
                }
            ];
            data_db.forEach(row => {
                data[row.month - 1].count = row.count;
            });

            new Chart(document.getElementById("line"), {
                type: "line",
                data: {
                    labels: data.map((row) => row.month),
                    datasets: [{
                        label: "Data pengaduan per bulan tahun ini",
                        data: data.map((row) => row.count),
                        borderWidth: 3,
                        borderColor: 'rgb(87, 87, 87)',
                        backgroundColor: 'rgb(0, 0, 0)'
                    }, ],
                },
                options: {
                    plugins: {
                        legend: {
                            labels: {
                                font: {
                                    weight: 'bolder',
                                    size: 16,
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            title: {
                                display: true,
                                text: "Bulan"
                            }
                        },
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: "Jumlah Pengaduan"
                            },
                            ticks: {
                                stepSize: 1,
                            }
                        }
                    }
                },

            });
        })();

        // DOUGHNUT CHART
        const status = ['Menunggu', 'Proses', 'Selesai'];
        const color = ['#ffc107', '#17a2b8', '#28a745'];
        (async function() {
            const data = <?= json_encode($chartRows2) ?>;
            console.log(data);
            new Chart(document.getElementById("doughnut"), {
                type: "doughnut",
                data: {
                    labels: data.map((row) => status[row.id_status - 1]),
                    datasets: [{
                        label: "Jumlah data ",
                        data: data.map((row) => row.count),
                        backgroundColor: data.map((row) => color[row.id_status - 1]),
                    }, ],
                },
            });
        })();
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js" integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO" crossorigin="anonymous"></script>
</body>

</html>