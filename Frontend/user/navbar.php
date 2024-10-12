

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

        /* Responsive untuk menampilkan navbar bawah hanya di mobile */
        @media (min-width: 768px) {
            .navbar-bottom {
                display: none;
            }
        }

        /* Responsive untuk menampilkan navbar atas di desktop */
        @media (max-width: 767px) {
            .navbar-top {
                display: none;
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

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</body>
</html>
