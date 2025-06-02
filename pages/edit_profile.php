<?php
// mulai sesi
session_start();

include '../config/koneksi.php';

// Cek apakah user sudah login
// Cek session user
if (!isset($_SESSION['user_id']) || $_SESSION['level'] !== 'masyarakat') {
    header("Location: ./login_page.php");
    exit();
}

$user_id = $_SESSION['user_id'];
if (isset($_POST['edit'])) {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);

    // Check for duplicate username (excluding current user)
    $checkQuery = "SELECT id FROM tbl_user WHERE username = ? AND id != ?";
    $checkStmt = $conn->prepare($checkQuery);
    $checkStmt->bind_param("si", $username, $user_id);
    $checkStmt->execute();
    $checkStmt->store_result();

    if ($checkStmt->num_rows > 0) {
        $_SESSION['error'] = "Username already taken. Please choose another.";
    } else {
        $query = "UPDATE tbl_user SET username = ?, email = ? WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ssi", $username, $email, $user_id);
        $stmt->execute();
        $stmt->close();
        $_SESSION['success'] = "Profile updated successfully!";
    }
    $checkStmt->close();
}

// Ambil data user dari database
$query = "SELECT username, email FROM tbl_user WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 1) {
    $user = $result->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User Profile</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/butterpop.css" />
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background-color: #f5f7fa;
            padding: 20px;
            line-height: 1.6;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 30px;
        }

        h1 {
            color: #333;
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 30px;
        }

        .profile-photo-section {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid #e2e8f0;
        }

        .profile-photo {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
        }

        .photo-info h3 {
            color: #333;
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .photo-info p {
            color: #64748b;
            font-size: 12px;
            margin-bottom: 8px;
        }

        .upload-btn {
            background: #3b82f6;
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 12px;
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .upload-btn:hover {
            background: #2563eb;
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 25px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-group.full-width {
            grid-column: 1 / -1;
        }

        label {
            color: #4b5563;
            font-size: 14px;
            font-weight: 500;
            margin-bottom: 6px;
        }

        input,
        select {
            padding: 12px;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            font-size: 14px;
            transition: border-color 0.2s, box-shadow 0.2s;
            background: white;
        }

        input:focus,
        select:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .password-section {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 25px;
        }

        .password-section h3 {
            color: #333;
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 10px;
        }

        .password-section p {
            color: #64748b;
            font-size: 14px;
            margin-bottom: 15px;
        }

        .password-fields {
            display: none;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-top: 15px;
        }

        .password-fields.show {
            display: grid;
        }

        .button-group {
            display: flex;
            gap: 12px;
            justify-content: flex-start;
            margin-bottom: 30px;
        }

        .btn-primary {
            background: #3b82f6;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .btn-primary:hover {
            background: #2563eb;
        }

        .btn-secondary {
            background: transparent;
            color: #64748b;
            border: 2px solid #e2e8f0;
            padding: 10px 24px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-secondary:hover {
            border-color: #cbd5e1;
            color: #475569;
        }

        .deactivate-section {
            border-top: 1px solid #e2e8f0;
            padding-top: 25px;
        }

        .deactivate-section h3 {
            color: #333;
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .deactivate-section p {
            color: #64748b;
            font-size: 14px;
            margin-bottom: 15px;
        }

        .btn-danger {
            background: transparent;
            color: #dc2626;
            border: 2px solid #dc2626;
            padding: 10px 20px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-danger:hover {
            background: #dc2626;
            color: white;
        }

        @media (max-width: 640px) {
            .form-grid {
                grid-template-columns: 1fr;
            }

            .password-fields {
                grid-template-columns: 1fr;
            }

            .button-group {
                flex-direction: column;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Account settings</h1>

        <form id="editUserForm" method="POST">
            <!-- Profile Photo Section -->
            <div class="profile-photo-section">
                <div class="profile-photo">
                    📷
                </div>
                <div class="photo-info">
                    <h3>Profile Photo</h3>
                    <p>Accepted file type .png. Less than 1MB</p>
                    <button type="button" class="upload-btn">Upload</button>
                </div>
            </div>

            <!-- User Information -->
            <div class="form-grid">
                <div class="form-group">
                    <label for="username">Username *</label>
                    <input type="text" id="username" name="username" required value="<?= htmlspecialchars($user['username']); ?>">
                </div>
                <div class="form-group">
                    <label for="email">Email Address *</label>
                    <input type="email" id="email" name="email" required value="<?= htmlspecialchars($user['email']); ?>">
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="button-group">
                <button type="submit" class="btn-primary" name="edit">Save Changes</button>
                <a type="button" class="btn-secondary" href="dashboard_user.php">Kembali</a>
            </div>
        </form>


    </div>

    <script src="../assets/js/butterpop.js"></script>

    <script>
        <?php if (!empty($_SESSION['success'])): ?>
            ButterPop.show({
                message: "<?= $_SESSION['success'] ?>",
                type: "success",
                position: "bottom-right",
                theme: "velvet",
                duration: 4000,
                progress: true,
                closable: true,
                pauseOnHover: true,
                closeOnClick: false
            });
            <?php unset($_SESSION['success']); // Hapus supaya notif tidak muncul lagi 
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
</body>

</html>