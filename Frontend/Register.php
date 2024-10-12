<?php
require 'koneksi.php'; // Include database connection

$error = '';
$success = '';

// Fetch roles for the dropdown
$roles = [];
$stmt = $conn->prepare("SELECT role_name FROM Roles");
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $roles[] = $row['role_name'];
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get user input
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $profile_photo = $_FILES['profile_photo']['name']; // Get uploaded file name
    $role_name = trim($_POST['role_name']); // Get selected role

    // Check if fields are empty
    if (empty($username) || empty($email) || empty($password) || empty($profile_photo) || empty($role_name)) {
        $error = 'Please fill in all fields.';
    } else {
        // Check if the username or email already exists
        $stmt = $conn->prepare("SELECT * FROM Users WHERE username = ? OR email = ?");
        $stmt->bind_param("ss", $username, $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $error = 'Username or email already exists.';
        } else {
            // Hash the password
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

            // Move uploaded file to the desired location (ensure the directory exists)
            $target_dir = "uploads/"; // Ensure this directory exists and is writable
            move_uploaded_file($_FILES['profile_photo']['tmp_name'], $target_dir . $profile_photo);

            // Insert the user into the Users table
            $stmt = $conn->prepare("INSERT INTO Users (username, email, password, profile_photo, role_name) VALUES (?, ?, ?, ?, ?)");
            if ($stmt->execute([$username, $email, $hashedPassword, $profile_photo, $role_name])) {
                $success = 'Registration successful!';
            } else {
                $error = 'Registration failed. Please try again.';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Registration</title>
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
        input[type="text"], input[type="email"], input[type="password"], input[type="file"], select {
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
        .success {
            text-align: center;
            color: green;
            margin: 10px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Create an Account</h2>
        <?php if ($error): ?>
            <div class="error"><?= $error; ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="success"><?= $success; ?></div>
        <?php endif; ?>
        <form method="POST" action="" enctype="multipart/form-data">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" name="username" required>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" name="password" required>
            </div>
            <div class="form-group">
                <label for="profile_photo">Profile Photo</label>
                <input type="file" name="profile_photo" accept="image/*" required>
            </div>
            <div class="form-group">
                <label for="role_name">Select Role</label>
                <select name="role_name" required>
                    <option value="">Select Role</option>
                    <?php foreach ($roles as $role): ?>
                        <option value="<?= htmlspecialchars($role); ?>"><?= htmlspecialchars($role); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="btn">Register</button>
        </form>
        <p>Already have an account? <a href="login.php">Login here</a></p>
    </div>
</body>
</html>
