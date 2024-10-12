<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>API: Login atau Daftar</title>
  <!-- Bootstrap CSS -->
  <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

  <style>
    body {
      font-family: Arial, sans-serif;
    }

    /* Header Section */
    .navbar {
      background-color: #343a40;
    }

    .navbar-brand {
      font-weight: bold;
      color: white;
    }

    .navbar-nav .nav-link {
      color: white !important;
      font-weight: bold;
    }

    .btn-register {
      background-color: #dc3545;
      color: white;
      font-weight: bold;
    }

    .btn-register:hover {
      background-color: #c82333;
    }

    /* Hero Section */
    .hero-section {
      background-image: url('https://www.example.com/welding-background.jpg');
      background-size: cover;
      background-position: center;
      padding: 100px 0;
      text-align: center;
      color: white;
    }

    .hero-section h1 {
      font-size: 36px; /* Ukuran disesuaikan untuk mobile */
      font-weight: bold;
      text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
    }

    .hero-section p {
      font-size: 18px; /* Ukuran disesuaikan untuk mobile */
      margin-bottom: 20px;
    }

    .btn-hero {
      background-color: #dc3545;
      color: white;
      font-weight: bold;
      padding: 10px 20px; /* Padding disesuaikan untuk mobile */
    }

    .btn-hero:hover {
      background-color: #c82333;
    }

    /* Features Section */
    .features-section {
      padding: 60px 0;
      text-align: center;
    }

    .features-section h2 {
      font-size: 28px; /* Ukuran disesuaikan untuk mobile */
      color: #343a40;
      margin-bottom: 40px;
    }

    .feature-item {
      margin-bottom: 30px;
    }

    .feature-item i {
      font-size: 48px;
      margin-bottom: 20px;
      color: #dc3545;
    }

    .feature-item h4 {
      font-size: 20px;
      color: #dc3545;
    }

    .feature-item p {
      color: #666;
    }

    /* Footer Section */
    .footer {
      background-color: #343a40;
      color: white;
      padding: 40px 0;
      text-align: center;
    }

    .footer p {
      color: white;
    }

    .footer a {
      color: #dc3545;
      margin: 0 10px;
      font-weight: bold;
    }

    @media (max-width: 768px) {
      .hero-section {
        padding: 50px 0;
      }

      .navbar-nav {
        text-align: center;
      }

      .features-section {
        padding: 40px 0;
      }
    }
  </style>
</head>
<body>

  <!-- Header -->
  <nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container">
      <a class="navbar-brand" href="#">API</a>
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ml-auto">
          <li class="nav-item">
            <a class="nav-link" href="#">Login</a>
          </li>
          <li class="nav-item">
            <a class="btn btn-register nav-link" href="#">Daftar Sekarang</a>
          </li>
        </ul>
      </div>
    </div>
  </nav>

  <!-- Hero Section -->
  <section class="hero-section" style="background-image: url('https://www.example.com/welding.jpg');">
    <div class="container">
      <h1>Jaringan Tukang Las Profesional</h1>
      <p>Bangun karier dan portofolio Anda sebagai tukang las bersama kami.</p>
      <a href="#" class="btn btn-hero">Bergabung Sekarang</a>
    </div>
  </section>

  <!-- Features Section -->
  <section class="features-section">
    <div class="container">
      <h2>Manfaat Bergabung dengan Kami</h2>
      <div class="row">
        <div class="col-md-4 feature-item">
          <i class="fas fa-tools"></i> <!-- Ikon baru dari Font Awesome -->
          <h4>Pertajam Keterampilan</h4>
          <p>Temukan berbagai proyek dan peluang untuk mengasah keterampilan Anda.</p>
        </div>
        <div class="col-md-4 feature-item">
          <i class="fas fa-network-wired"></i> <!-- Ikon baru dari Font Awesome -->
          <h4>Jaringan Profesional</h4>
          <p>Terhubung dengan tukang las dan ahli lainnya di industri ini.</p>
        </div>
        <div class="col-md-4 feature-item">
          <i class="fas fa-briefcase"></i> <!-- Ikon baru dari Font Awesome -->
          <h4>Peluang Pekerjaan</h4>
          <p>Temukan pekerjaan atau kontrak lepas yang sesuai dengan keahlian Anda.</p>
        </div>
      </div>
    </div>
  </section>

  <!-- Footer Section -->
  <footer class="footer">
    <div class="container">
      <p>© 2024 API</p>
      <p>
        <a href="#">Tentang</a>
        <a href="#">Bantuan</a>
        <a href="#">Privasi</a>
        <a href="#">Ketentuan</a>
      </p>
    </div>
  </footer>

  <!-- Bootstrap JS -->
  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
