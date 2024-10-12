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

// Fetch work experiences
$work_experiences = [];
$work_stmt = $conn->prepare("SELECT * FROM work_experience WHERE user_id = ?");
$work_stmt->bind_param("i", $user['id']);
$work_stmt->execute();
$work_result = $work_stmt->get_result();
while ($row = $work_result->fetch_assoc()) {
    $work_experiences[] = $row;
}

// Fetch education experiences
$education_experiences = [];
$edu_stmt = $conn->prepare("SELECT * FROM education_experience WHERE user_id = ?");
$edu_stmt->bind_param("i", $user['id']);
$edu_stmt->execute();
$edu_result = $edu_stmt->get_result();
while ($row = $edu_result->fetch_assoc()) {
    $education_experiences[] = $row;
}


// Menampilkan halaman profil
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Pengguna</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Arial', sans-serif;
        }  

          .profile-header {
            background: #343a40;
            padding: 30px;
            color: white;
            border-radius: 12px 12px 0 0;
            display: flex;
            align-items: center;
            flex-direction: column;
        }
        .profile-header img {
            border-radius: 50%;
            width: 150px;
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
        h2 {
            margin-top: 30px;
            color: #343a40;
        }
        ul {
            list-style: none;
            padding: 0;
        }
        ul li {
            background-color: #e9ecef;
            padding: 15px;
            margin-bottom: 10px;
            border-radius: 8px;
        }
        .btn-primary {
            margin-top: 20px;
        }
    </style>
</head>

<body>
    <div class="profile-header">
        <img src="../uploads/<?= htmlspecialchars($user['profile_photo']); ?>" alt="Foto Profil">
        <h1><?php echo $user['username']; ?></h1>
        <p>Email: <?php echo $user['email']; ?></p>
        <p>Spesialisasi: <?php echo $user['specialization_name']; ?></p>
        <p>Status Pekerjaan: <?php echo $user['job_status_name']; ?></p>
    </div>

    <div class="container">
        <div class="profile-content">
            <h2>Pengalaman Kerja</h2>
            <?php if (count($work_experiences) > 0): ?>
                <ul>
                    <?php foreach ($work_experiences as $work): ?>
                        <li>
                            <strong><?php echo $work['job_title']; ?></strong> di <?php echo $work['company_name']; ?> <br>
                            <small>Lokasi: <?php echo $work['location']; ?> | <?php echo $work['start_date']; ?> - <?php echo ($work['end_date'] ?? 'Sekarang'); ?></small><br>
                            <p><?php echo $work['job_description']; ?></p>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p>Tidak ada pengalaman kerja.</p>
            <?php endif; ?>

            <h2>Pengalaman Pendidikan</h2>
            <?php if (count($education_experiences) > 0): ?>
                <ul>
                    <?php foreach ($education_experiences as $edu): ?>
                        <li>
                            <strong><?php echo $edu['education_level']; ?></strong> di <?php echo $edu['institution_name']; ?> <br>
                            <small>Lokasi: <?php echo $edu['location']; ?> | <?php echo $edu['start_year']; ?> - <?php echo $edu['end_year']; ?></small><br>
                            <p><?php echo $edu['description']; ?></p>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p>Tidak ada pengalaman pendidikan.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
