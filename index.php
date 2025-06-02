<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengaduan Masyarakat</title>
    <link rel="stylesheet" href="../Project-UAS/assets/css/style.css">
    <link rel="stylesheet" href="../Project-UAS/assets/css/butterpop.css">
</head>

<body>

    <div class="main-index">
        <div class="hero-content">
            <h1 class="h1-index">S!AP</h1>
            <p class="subtitle">Sistem Informasi Aduan Publik</p>
            <div>
                <a href="/Project-UAS/pages/login_page.php" class="cta-button">
                    <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#82baff">
                        <path d="M200-120q-33 0-56.5-23.5T120-200v-560q0-33 23.5-56.5T200-840h560q33 0 56.5 23.5T840-760v268q-19-9-39-15.5t-41-9.5v-243H200v560h242q3 22 9.5 42t15.5 38H200Zm0-120v40-560 243-3 280Zm80-40h163q3-21 9.5-41t14.5-39H280v80Zm0-160h244q32-30 71.5-50t84.5-27v-3H280v80Zm0-160h400v-80H280v80ZM720-40q-83 0-141.5-58.5T520-240q0-83 58.5-141.5T720-440q83 0 141.5 58.5T920-240q0 83-58.5 141.5T720-40Zm-20-80h40v-100h100v-40H740v-100h-40v100H600v40h100v100Z" />
                    </svg>
                </a>
            </div>
        </div>
    </div>

    <script src="../Project-UAS/assets/js/butterpop.js"></script>
    <script>
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
                theme: "velvet",
                duration: 5000,
                progress: true,
                closable: true,
                pauseOnHover: true,
                closeOnClick: false
            });
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <?php if (!empty($_SESSION['logout_success'])): ?>
            ButterPop.show({
                message: "<?= $_SESSION['logout_success'] ?>",
                type: "success",
                position: "bottom-right",
                theme: "velvet",
                duration: 4000,
                progress: true,
                closable: true,
                pauseOnHover: true,
                closeOnClick: false
            });
            <?php unset($_SESSION['logout_success']); ?>
        <?php endif; ?>
    </script>
</body>
</html>