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

// Fetch skills
$skills = [];
$skill_stmt = $conn->prepare("SELECT skills.skill_name, skills.proficiency_level, skills.years_of_experience, skill_names.name 
                              FROM skills 
                              JOIN skill_names ON skills.skill_name = skill_names.name 
                              WHERE skills.user_id = ?");
$skill_stmt->bind_param("i", $user['id']);
$skill_stmt->execute();
$skill_result = $skill_stmt->get_result();
while ($row = $skill_result->fetch_assoc()) {
    $skills[] = $row;
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

        /* Styling untuk Navbar Atas */
        .navbar-top {
            background-color: #343a40;
            padding: 10px 15px;
        }
        .navbar-top .navbar-brand {
            color: white;
            font-size: 24px;
            font-weight: bold;
        }
        .navbar-top .nav-link {
            color: white;
            margin-left: 15px;
        }
        .navbar-top .form-control {
            max-width: 400px;
        }
        .navbar-top .navbar-nav .nav-item .nav-link {
            color: #ffffff;
            font-size: 18px;
        }

    .search-mobile {
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 10px;
        background-color: #f5f5f5; /* Warna latar belakang */
        border-radius: 25px; /* Membuat sudut membulat */
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2); /* Efek bayangan yang lebih halus */
        margin: 20px; /* Jarak di sekitar search bar */
        max-width: 400px; /* Lebar maksimum untuk rapi di mobile */
        width: 90%; /* Agar search bar memenuhi lebar layar */
    }

    .search-mobile input[type="search"] {
        flex: 3; /* Mengubah proporsi agar input lebih lebar */
        padding: 10px 15px; /* Ruang dalam yang lebih baik */
        border: none;
        border-radius: 30px 0 0 25px; /* Membulatkan sudut kiri */
        outline: none;
        width: 85%;
        font-size: 16px; /* Ukuran font */
        background-color: #343a40; /* Warna latar belakang input */
        color: white; /* Warna teks input */
    }

    .search-mobile input[type="search"]::placeholder {
        color: #aaa; /* Warna placeholder */
    }

    .search-button {
        background-color: #343a40; /* Warna tombol */
        color: white; /* Warna teks */
        border: none;
        padding: 10px 10px; /* Mengurangi padding untuk memberikan lebih banyak ruang ke input */
        border-radius: 0 25px 25px 0; /* Membulatkan sudut kanan */
        cursor: pointer; /* Mengubah kursor saat hover */
        transition: background-color 0.3s; /* Animasi saat hover */
    }
        .search-button:hover {
            background-color: #495057; /* Warna saat hover */
        }

                /* Responsive untuk menampilkan navbar bawah dan search bar hanya di mobile */
        @media (min-width: 768px) {
            .search-mobile {
                display: none;
            }
        }

        /* Styling untuk Navbar Bawah */
        .navbar-bottom {
            position: fixed;
            bottom: 0;
            width: 100%;
            background-color: #343a40;
            padding: 10px 0;
            display: flex;
            justify-content: space-around;
            align-items: center;
            box-shadow: 0 -2px 5px rgba(0,0,0,0.1);
        }
        .navbar-bottom a {
            color: white;
            text-align: center;
            font-size: 24px;
        }
        .navbar-bottom a.active {
            color: #007bff;
        }

        /* Responsive untuk menampilkan navbar bawah dan search bar hanya di mobile */
        @media (min-width: 768px) {
            .navbar-bottom {
                display: none;
            }
        }

        @media (max-width: 767px) {
            .navbar-top {
                display: none;
            }
            .search-mobile {
                display: block;
            }
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
        .profile-content ul {
            list-style: none;
            padding: 0;
        }
        .profile-content ul li {
            background-color: #e9ecef;
            padding: 15px;
            margin-bottom: 10px;
            border-radius: 8px;
        }
    </style>
</head>

<body>


    <!-- Search Bar Mobile -->
    <div class="search-mobile">
        <input type="search" placeholder="Cari di sini..." aria-label="Search">
        <button type="button" class="search-button">🔍</button>
    </div>


      <!-- Navbar Atas (Desktop dan Mobile) -->
    <nav class="navbar navbar-expand-lg navbar-top">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">MyApp</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#"><i class="bi bi-bell"></i> Notifikasi</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#"><i class="bi bi-chat"></i> Pesan</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#"><i class="bi bi-person"></i> Profil</a>
                    </li>
                </ul>
                <form class="d-flex ms-auto">
                    <input class="form-control" type="search" placeholder="Cari di sini" aria-label="Search">
                </form>
            </div>
        </div>
    </nav>

    <!-- Navbar Bawah (Mobile) -->
    <div class="navbar-bottom">
        <a href="#" class="active"><i class="bi bi-house-door"></i></a>
        <a href="#"><i class="bi bi-search"></i></a>
        <a href="#"><i class="bi bi-plus-circle"></i></a>
        <a href="#"><i class="bi bi-bell"></i></a>
        <a href="#"><i class="bi bi-person"></i></a>
    </div>

    <!-- Konten Profil -->
    <div class="container mt-5">
        <div class="profile-header">
            <img src="../uploads/<?= htmlspecialchars($user['profile_photo']); ?>" alt="Foto Profil">
            <h1><?php echo $user['username']; ?></h1>
            <p>Email: <?php echo $user['email']; ?></p>
            <p>Spesialisasi: <?php echo $user['specialization_name']; ?></p>
            <p>Status Pekerjaan: <?php echo $user['job_status_name']; ?></p>
        </div>

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

            <h2>Skills</h2>
            <?php if (count($skills) > 0): ?>
                <ul>
                    <?php foreach ($skills as $skill): ?>
                        <li>
                            <strong><?php echo $skill['skill_name']; ?></strong> - <?php echo $skill['proficiency_level']; ?> <br>
                            <small>Pengalaman: <?php echo $skill['years_of_experience']; ?> tahun</small>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p>Tidak ada skill yang terdaftar.</p>
            <?php endif; ?>
        </div>
        <div style="height: 7vh;"></div> <!-- Menambah tinggi 100vh untuk ruang kosong -->
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</body>
</html>

