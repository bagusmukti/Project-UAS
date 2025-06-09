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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
</head>

<body style="background-color: white;">
    <div>
        <nav class="navbar navbar-expand-lg navbar-dark bg-primary fixed-top mb-5">
            <div class="container-fluid">
                <h1 class="text-dashboard" style="margin-left: 20px; margin-top:5px;">S!AP</h1>

                <div class="collapse navbar-collapse" id="navbarText">
                    <span class="navbar-text ms-auto">
                        <form class="container-fluid justify-content-start">
                            <a class="btn me-2"
                                href="./pages/login.php"
                                style="transition: transform 0.5s ease, background-color 0.5s ease, color 0.5s ease;"
                                onmouseover="this.style.backgroundColor='#fff'; this.style.color='rgb(0, 0, 255)'; this.style.transform='scale(1.05)';"
                                onmouseout="this.style.backgroundColor=''; this.style.color=''">
                                Login
                            </a>
                           
                        </form>
                    </span>
                </div>
            </div>
        </nav>
        <div class="container mt-3">
            <div class="row mt-5" style="margin-top: 50px;">
                <div class="col-3 mt-5">
                    <img src="assets/img/successful-businessman-standing-confidently.png" width="250rem" alt="" style="margin-left: 0px;">
                </div>
                <div class="col-6">
                    <div class="mt-5" style="background-color: white; padding: 15px; border-radius: 10px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);">
                        <h3 class="text-center mt-5">SISTEM INFORMASI ADUAN PUBLIK</h3>
                        <hr>
                        <p class="slmtdtg p-3">
                        Selamat datang di sistem informasi aduan publik kami. Silakan masuk atau daftar untuk melanjutkan.  
                        Sistem ini dirancang untuk memudahkan masyarakat dalam menyampaikan laporan, keluhan, atau saran kepada instansi terkait secara cepat dan transparan.  
                        </p>                       
                         <hr>
                        <a href="/Project-UAS/pages/login.php" class="cta-button">
                            <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#82baff">
                                <path d="M200-120q-33 0-56.5-23.5T120-200v-560q0-33 23.5-56.5T200-840h560q33 0 56.5 23.5T840-760v268q-19-9-39-15.5t-41-9.5v-243H200v560h242q3 22 9.5 42t15.5 38H200Zm0-120v40-560 243-3 280Zm80-40h163q3-21 9.5-41t14.5-39H280v80Zm0-160h244q32-30 71.5-50t84.5-27v-3H280v80Zm0-160h400v-80H280v80ZM720-40q-83 0-141.5-58.5T520-240q0-83 58.5-141.5T720-440q83 0 141.5 58.5T920-240q0 83-58.5 141.5T720-40Zm-20-80h40v-100h100v-40H740v-100h-40v100H600v40h100v100Z" />
                            </svg>
                        </a>
                    </div>
                </div>
                <div class="col-3 mt-5">
                    <img src="assets/img/successful-businessman-standing-confidently.png" width="250rem" alt="" style="transform: scaleX(-1); margin-left: 0px;">
                </div>
            </div>
            <!DOCTYPE html>
            <html lang="id">

            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Bootstrap Stepper</title>
                <!-- Bootstrap CSS -->
                <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
                <!-- Bootstrap Icons -->
                <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.0/font/bootstrap-icons.min.css" rel="stylesheet">

                <style>
                    .stepper {
                        display: flex;
                        justify-content: space-between;
                        align-items: center;
                        margin: 2rem 0;
                    }

                    .step {
                        display: flex;
                        flex-direction: column;
                        align-items: center;
                        flex: 1;
                        position: relative;
                        cursor: pointer;
                    }

                    .step-number {
                        width: 40px;
                        height: 40px;
                        border-radius: 50%;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        font-weight: bold;
                        margin-bottom: 8px;
                        transition: all 0.3s ease;
                        z-index: 2;
                        position: relative;
                    }

                    .step-inactive .step-number {
                        background-color: #e9ecef;
                        color: #6c757d;
                        border: 2px solid #dee2e6;
                    }

                    .step-active .step-number {
                        background-color: #0d6efd;
                        color: white;
                        border: 2px solid #0d6efd;
                    }

                    .step-completed .step-number {
                        background-color: #198754;
                        color: white;
                        border: 2px solid #198754;
                    }

                    .step-title {
                        font-size: 14px;
                        text-align: center;
                        font-weight: 500;
                    }

                    .step-inactive .step-title {
                        color: #6c757d;
                    }

                    .step-active .step-title {
                        color: #0d6efd;
                        font-weight: 600;
                    }

                    .step-completed .step-title {
                        color: #198754;
                        font-weight: 600;
                    }

                    .step-line {
                        position: absolute;
                        top: 20px;
                        left: 50%;
                        right: -50%;
                        height: 2px;
                        background-color: #dee2e6;
                        z-index: 1;
                    }

                    .step:last-child .step-line {
                        display: none;
                    }

                    .step-line-active {
                        background-color: #198754 !important;
                    }

                    /* Responsive */
                    @media (max-width: 768px) {
                        .step-title {
                            font-size: 12px;
                        }

                        .step-number {
                            width: 35px;
                            height: 35px;
                            font-size: 12px;
                        }
                    }
                </style>
            </head>

            <body>
                <div class="container">
                    <div class="row">
                        <div class="col-12">

                            <!-- Stepper -->
                            <div class="stepper">
                                <div class="step step-inactive" data-step="1">
                                    <div class="step-number"><a href="./pages/create_account.php" style="color: black; text-decoration-line: none;  ">1</a> </div>
                                    <div class="step-title">Buat Akun</div>
                                    <div class="step-line"></div>
                                </div>

                                <div class="step step-inactive" data-step="2">
                                    <div class="step-number"><a href="./pages/login_page.php" style="color: black; text-decoration-line: none;">2</a></div>
                                    <div class=" step-title">Masuk</div>
                                    <div class="step-line"></div>
                                </div>

                                <div class="step step-inactive" data-step="3">
                                    <div class="step-number"><a href="./pages/form_pengaduan.php" style="color: black; text-decoration-line: none;">3</a></div>
                                    <div class="step-title">Buat Aduan</div>
                                    <div class="step-line"></div>
                                </div>

                                <div class="step step-inactive" data-step="4">
                                    <div class="step-number"><a href="./pages/dashboard_user.php" style=" color: black; text-decoration-line: none;">4</a></div>
                                    <div class="step-title">Lihat Balasan</div>
                                    <div class="step-line"></div>
                                </div>

                                <div class="step step-inactive" data-step="5">
                                    <div class="step-number">5</div>
                                    <div class="step-title">Selesai</div>
                                </div>
                            </div>


                        </div>
                    </div>
                </div>

                <!-- Bootstrap JS -->
                <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>

                <script>
                    // Contoh fungsi untuk mengubah status step
                    function setStepStatus(stepNumber, status) {
                        const step = document.querySelector(`[data-step="${stepNumber}"]`);
                        const stepNumberElement = step.querySelector('.step-number');
                        const stepLine = step.querySelector('.step-line');

                        // Reset classes
                        step.className = 'step';

                        // Set status
                        if (status === 'completed') {
                            step.classList.add('step-completed');
                            stepNumberElement.innerHTML = '<i class="bi bi-check-lg"></i>';
                            if (stepLine) stepLine.classList.add('step-line-active');
                        } else if (status === 'active') {
                            step.classList.add('step-active');
                            stepNumberElement.textContent = stepNumber;
                            if (stepLine) stepLine.classList.remove('step-line-active');
                        } else {
                            step.classList.add('step-inactive');
                            stepNumberElement.textContent = stepNumber;
                            if (stepLine) stepLine.classList.remove('step-line-active');
                        }
                    }

                    // Contoh penggunaan:
                    // setStepStatus(1, 'completed');
                    // setStepStatus(2, 'active');
                    // setStepStatus(3, 'inactive');

                    // Event listener untuk demo click (opsional)
                    document.querySelectorAll('.step').forEach(step => {
                        step.addEventListener('click', function() {
                            const stepNumber = parseInt(this.dataset.step);
                            console.log(`Clicked on step ${stepNumber}`);
                            // Di sini Anda bisa menambahkan logika navigasi custom
                        });
                    });
                </script>
            </body>

            </html>
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
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js" integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO" crossorigin="anonymous"></script>
</body>

</html>