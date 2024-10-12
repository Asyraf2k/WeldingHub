<?php
session_start();
include 'koneksi.php'; // Menghubungkan ke database

$error = ''; // Inisialisasi variabel error

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Mencari pengguna di database berdasarkan username
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    // Memeriksa apakah pengguna ditemukan
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        
        // Memverifikasi password
        if (password_verify($password, $user['password'])) {
            // Menyimpan data pengguna ke dalam sesi
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role_name'] = $user['role_name'];

            $base_url = "http://localhost:8080/project1/frontend";
            $encoded_username = urlencode($user['username']);

            // Mengarahkan berdasarkan role
            if ($user['role_name'] == 'Admin') {
                header("Location: $base_url/admin/datauser.php?username=$encoded_username");
            } elseif ($user['role_name'] == 'Recruiter') {
                header("Location: $base_url/user/datauser.php?username=$encoded_username");
            } else {
                header("Location: $base_url/user/datauser.php?username=$encoded_username");
            }
            exit();
        } else {
            $error = "Password salah!"; // Set error message
        }
    } else {
        $error = "Pengguna tidak ditemukan!"; // Set error message
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Login</title>
    <link rel="stylesheet" href="styles.css"> <!-- Link to external CSS -->
    <style>
        body {
            background-color: #f9f9f9;
            font-family: Arial, sans-serif;
        }
        .container {
            width: 400px;
            margin: 50px auto;
            padding: 20px;
            background-color: white;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h2 {
            text-align: center;
            color: #FFA500; /* Yellow color */
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            color: #555;
        }
        input[type="text"], input[type="password"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 3px;
        }
        .btn {
            width: 100%;
            padding: 10px;
            background-color: #FFA500; /* Yellow color */
            border: none;
            color: white;
            font-size: 16px;
            border-radius: 3px;
            cursor: pointer;
        }
        .btn:hover {
            background-color: #ff8c00; /* Darker yellow on hover */
        }
        .error {
            text-align: center;
            color: red;
            margin: 10px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Login</h2>
        <?php if ($error): ?>
            <div class="error"><?= htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <form method="POST" action="">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" name="password" required>
            </div>
            <button type="submit" class="btn">Login</button>
        </form>
        <p>Don't have an account? <a href="register.php">Register here</a></p>
    </div>
</body>
</html>
