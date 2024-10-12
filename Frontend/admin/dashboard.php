<?php
session_start();

// Cek apakah pengguna sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

// Cek apakah pengguna adalah Admin
if ($_SESSION['role_name'] !== 'Admin') {
    header("Location: ../user/dashboard.php"); // Arahkan ke dashboard user
    exit();
}

// Ambil data pengguna
$username = $_SESSION['username'];
$role_name = $_SESSION['role_name'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../styles.css"> <!-- Link to external CSS -->
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 20px;
        }
        .container {
            width: 80%;
            margin: auto;
            background: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            color: #FFA500; /* Yellow color */
            text-align: center;
        }
        .welcome {
            text-align: center;
            margin: 20px 0;
        }
        .btn-logout {
            display: block;
            width: 100px;
            margin: 20px auto;
            padding: 10px;
            background-color: #FFA500; /* Yellow color */
            color: white;
            border: none;
            border-radius: 3px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Selamat Datang, <?php echo htmlspecialchars($username); ?>!</h1>
        <div class="welcome">
            <p>Anda login sebagai: <?php echo htmlspecialchars($role_name); ?></p>
        </div>
        <a href="../logout.php" class="btn-logout">Logout</a>
    </div>
</body>
</html>
