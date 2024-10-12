<?php
// profile.php
include '../koneksi.php'; // Include koneksi database
session_start(); // Memulai sesi

// Mendapatkan username dari URL
$username = isset($_GET['username']) ? $_GET['username'] : '';

// Query untuk mendapatkan data user berdasarkan username
$stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

// Jika pengguna ditemukan
if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
} else {
    echo "Pengguna tidak ditemukan!";
    exit;
}

// Fetch job statuses
$job_statuses = [];
$status_stmt = $conn->prepare("SELECT * FROM job_status");
$status_stmt->execute();
$status_result = $status_stmt->get_result();
while ($row = $status_result->fetch_assoc()) {
    $job_statuses[] = $row;
}

// Fetch specializations
$specializations = [];
$spec_stmt = $conn->prepare("SELECT * FROM specializations");
$spec_stmt->execute();
$spec_result = $spec_stmt->get_result();
while ($row = $spec_result->fetch_assoc()) {
    $specializations[] = $row;
}

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    // Cek apakah username yang login adalah pemilik profil
    if ($_SESSION['username'] !== $username) {
        echo "Anda tidak memiliki izin untuk mengedit profil ini!";
        exit;
    }

    // Get posted data
    $new_email = $_POST['email'];
    $new_specialization = $_POST['specialization_name'];
    $new_job_status = $_POST['job_status_name'];

    // Update user profile in the database
    $update_stmt = $conn->prepare("UPDATE users SET email = ?, specialization_name = ?, job_status_name = ? WHERE username = ?");
    $update_stmt->bind_param("ssss", $new_email, $new_specialization, $new_job_status, $username);

    if ($update_stmt->execute()) {
        // Redirect to the same profile page after update
        header("Location: profile.php?username=" . urlencode($username));
        exit;
    } else {
        echo "Error updating profile: " . $conn->error;
    }
}

// Handle post creation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_post'])) {
    // Cek apakah username yang login adalah pemilik profil
    if ($_SESSION['username'] !== $username) {
        echo "Anda tidak memiliki izin untuk memposting di profil ini!";
        exit;
    }

    $content = $_POST['content'];
    $images = $_FILES['images'];

    // Upload images
    $image_paths = [];
    $upload_directory = '../uploads/posts/';

    foreach ($images['name'] as $key => $name) {
        $temp_name = $images['tmp_name'][$key];
        $target_file = $upload_directory . basename($name);

        if (move_uploaded_file($temp_name, $target_file)) {
            $image_paths[] = basename($name);
        }
    }

    // Save post to database
    $image_paths_json = json_encode($image_paths);
    $post_stmt = $conn->prepare("INSERT INTO posts (username, content, images) VALUES (?, ?, ?)");
    $post_stmt->bind_param("sss", $username, $content, $image_paths_json);
    $post_stmt->execute();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Pengguna - <?= htmlspecialchars($user['username']); ?></title>
    <link rel="stylesheet" href="styles.css">
    <style>
         body {
            font-family: 'Poppins', sans-serif;
            background-color: #f0f4f8;
            color: #333;
        }
        .container {
            width: 75%;
            margin: 40px auto;
            background-color: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
        }
        .profile-header {
            background: linear-gradient(135deg, #FF8C00 0%, #FFA500 100%);
            padding: 30px;
            color: white;
            border-radius: 12px 12px 0 0;
            display: flex;
            align-items: center;
            flex-direction: column;
        }
        .profile-header img {
            border-radius: 50%;
            width: 160px;
            height: 160px;
            object-fit: cover;
            margin-bottom: 20px;
            border: 4px solid white;
        }
        .profile-header h1 {
            margin: 10px 0;
            font-size: 32px;
            font-weight: bold;
        }
        .profile-header p {
            margin: 0;
            font-size: 18px;
            color: #f0f0f0;
        }
        .profile-content {
            margin-top: 20px;
        }
        .profile-content table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
        }
        .profile-content table td {
            padding: 15px;
            font-size: 16px;
            border-bottom: 1px solid #eee;
        }
        .profile-content table td:first-child {
            font-weight: bold;
            text-align: left;
            width: 30%;
        }
        .btn-edit {
            background: linear-gradient(135deg, #FF8C00 0%, #FFA500 100%);
            color: white;
            padding: 12px 25px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            margin-top: 20px;
        }
        .btn-edit:hover {
            transform: scale(1.05);
            box-shadow: 0 8px 15px rgba(255, 140, 0, 0.3);
        }
        
        /* Modal Styles */
        .modal {
            display: none; 
            position: fixed; 
            z-index: 1; 
            left: 0;
            top: 0;
            width: 100%; 
            height: 100%; 
            overflow: auto; 
            background-color: rgba(0,0,0,0.6); 
            padding-top: 60px;
        }
        .modal-content {
            background-color: #fff;
            margin: 5% auto; 
            padding: 40px;
            border: 1px solid #888;
            width: 60%; 
            border-radius: 15px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
            animation: fadeIn 0.5s ease;
        }
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }
        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
        h2 {
            margin-bottom: 20px;
            color: #FF8C00;
        }
        .form-group {
            margin-bottom: 15px;
            display: flex;
            flex-direction: column;
        }
        .form-group label {
            margin-bottom: 8px;
            font-weight: bold;
            color: #333;
        }
        .form-group select, .form-group input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 16px;
            transition: border-color 0.3s;
        }
        .form-group select:focus, .form-group input:focus {
            border-color: #FFA500;
            outline: none;
        }
        .btn-save {
            background-color: #FFA500;
            color: white;
            padding: 12px 25px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
            transition: background-color 0.3s;
            margin-top: 20px;
        }
        .btn-save:hover {
            background-color: #FF8C00;
        }

        /* Keyframes for Modal Animation */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .posts-container {
            margin-top: 40px;
        }

        .posts {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .post {
            background:#f9f9f9;;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            padding: 15px;
        }

        .post-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }

        .post-images {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 10px;
        }

        .post-images img {
            width: 100%;
            border-radius: 8px;
            object-fit: cover;
        }


        .post-input-container {
    background: white;
    border-radius: 10px;
    padding: 15px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    margin-bottom: 20px;
}

textarea {
    width: 100%;
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 8px;
    resize: none;
}

.post-options {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-top: 10px;
}

.upload-image {
    cursor: pointer;
    color: #007bff;
}

.btn-post {
    background-color: #007bff;
    color: white;
    border: none;
    border-radius: 6px;
    padding: 8px 15px;
    cursor: pointer;
}

.btn-post:hover {
    background-color: #0056b3;
}

#imagePreviewContainer {
    display: flex;
    margin-top: 10px;
    gap: 10px;
}

#imagePreviewContainer img {
    width: 100px;
    height: auto;
    border-radius: 5px;
}


    </style>
</head>
<body>
<div class="container">
    <div class="profile-header">
        <img src="../uploads/<?= htmlspecialchars($user['profile_photo']); ?>" alt="Foto Profil">
        <h1>@<?= htmlspecialchars($user['username']); ?></h1>
        <p><?= htmlspecialchars($user['role_name']); ?></p>
    </div>
    
    <div class="profile-content">
        <table>
            <tr>
                <td>Nama Pengguna</td>
                <td><?= htmlspecialchars($user['username']); ?></td>
            </tr>
            <tr>
                <td>Email</td>
                <td><?= htmlspecialchars($user['email']); ?></td>
            </tr>
            <tr>
                <td>Peran</td>
                <td><?= htmlspecialchars($user['role_name']); ?></td>
            </tr>
            <tr>
                <td>Specializations</td>
                <td><?= htmlspecialchars($user['specialization_name']); ?></td>
            </tr>
            <tr>
                <td>Job Status</td>
                <td><?= htmlspecialchars($user['job_status_name']); ?></td>
            </tr>
            <tr>
                <td>Bergabung Sejak</td>
                <td><?= htmlspecialchars($user['created_at']); ?></td>
            </tr>
        </table>

        <!-- Hanya tampilkan tombol "Edit Profil" jika pengguna yang login adalah pemilik profil -->
        <?php if (isset($_SESSION['username']) && $_SESSION['username'] === $username): ?>
            <button class="btn-edit" id="editBtn">Edit Profil</button>
        <?php endif; ?>
    </div>

<!-- Form untuk membuat post -->
<div class="post-input-container">
    <form method="POST" enctype="multipart/form-data">
        <textarea name="content" rows="4" placeholder="Apa yang Anda pikirkan?" required></textarea>
        <input type="file" name="images[]" multiple accept="image/*" id="file-input" style="display:none;" onchange="previewImages(event)">
        <button type="button" id="upload-btn" class="btn-post" onclick="document.getElementById('file-input').click();">Upload Gambar</button>
        <button type="submit" name="create_post" id="submit" class="btn-post">Posting</button>
        <div id="image-preview" style="margin-top: 10px;"></div>
    </form>
</div>


<div class="posts-container">
    <h2>Postingan</h2>
    <div class="posts">
        <?php
        // Ambil postingan dari database
        $posts_stmt = $conn->prepare("SELECT * FROM posts WHERE username = ? ORDER BY created_at DESC");
        $posts_stmt->bind_param("s", $username);
        $posts_stmt->execute();
        $posts_result = $posts_stmt->get_result();

        while ($post = $posts_result->fetch_assoc()) {
            $images = json_decode($post['images'], true); // Mengambil gambar dari JSON
            ?>
            <div class="post">
                <div class="post-header">
                    <strong><?= htmlspecialchars($post['username']); ?></strong>
                    <span><?= htmlspecialchars($post['created_at']); ?></span>
                </div>
                <div class="post-content">
                    <p><?= htmlspecialchars($post['content']); ?></p>
                </div>
                <div class="post-images">
                    <?php foreach ($images as $image): ?>
                        <img src="../uploads/posts/<?= htmlspecialchars($image); ?>" alt="Post Image">
                    <?php endforeach; ?>
                </div>
            </div>
            <?php
        }
        ?>
    </div>
</div>

<!-- Modal untuk Edit Profil -->
<div id="editModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Edit Profil</h2>
        <form id="editForm" method="POST" action="profile.php?username=<?= urlencode($username); ?>"> <!-- Keep the username in the URL for the update -->
            <input type="hidden" name="username" value="<?= htmlspecialchars($user['username']); ?>">
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" name="email" id="email" value="<?= htmlspecialchars($user['email']); ?>" required>
            </div>
            <div class="form-group">
                <label for="specialization">Specialization:</label>
                <select name="specialization_name" id="specialization" required>
                    <option value="">Pilih Spesialisasi</option>
                    <?php foreach ($specializations as $specialization): ?>
                        <option value="<?= htmlspecialchars($specialization['name']); ?>" <?= ($specialization['name'] === $user['specialization_name']) ? 'selected' : ''; ?>>
                            <?= htmlspecialchars($specialization['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="job_status">Job Status:</label>
                <select name="job_status_name" id="job_status" required>
                    <option value="">Pilih Status Pekerjaan</option>
                    <?php foreach ($job_statuses as $job_status): ?>
                        <option value="<?= htmlspecialchars($job_status['status']); ?>" <?= ($job_status['status'] === $user['job_status_name']) ? 'selected' : ''; ?>>
                            <?= htmlspecialchars($job_status['status']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="btn-save">Simpan</button>
        </form>
    </div>
</div>


<script>
    function previewImages(event) {
        const previewContainer = document.getElementById('image-preview');
        previewContainer.innerHTML = ''; // Clear previous previews

        const files = event.target.files;
        for (let i = 0; i < files.length; i++) {
            const file = files[i];
            const reader = new FileReader();

            reader.onload = function(e) {
                const img = document.createElement('img');
                img.src = e.target.result;
                img.style.width = '100px'; // Set width for preview
                img.style.marginRight = '10px'; // Add some margin
                img.style.borderRadius = '8px'; // Optional: Add some border radius
                previewContainer.appendChild(img);
            }

            reader.readAsDataURL(file);
        }
    }
</script>

<script>
    // Script untuk menampilkan dan menyembunyikan modal edit
    var modal = document.getElementById("editModal");
    var btn = document.getElementById("editBtn");
    var span = document.getElementsByClassName("close")[0];

    btn.onclick = function() {
        modal.style.display = "block";
    }

    span.onclick = function() {
        modal.style.display = "none";
    }

    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }
</script>

</body>
</html>
