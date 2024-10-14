<?php
// Koneksi ke database
$servername = "localhost";
$username = "root";
$password = "12345678";
$dbname = "lasindo";
$port = 3307;

$conn = new mysqli($servername, $username, $password, $dbname, $port);

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Jika ada query string 'q' pada URL
$search_results = [];
if (isset($_GET['q'])) {
    $search_keyword = '%' . $_GET['q'] . '%';

    // Query untuk mencari data pada semua elemen tabel
    $sql = "SELECT username, email, specialization_name, job_status_name FROM users 
            WHERE username LIKE ? OR email LIKE ? OR specialization_name LIKE ? OR job_status_name LIKE ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $search_keyword, $search_keyword, $search_keyword, $search_keyword);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $search_results[] = $row;
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hasil Pencarian - Pangkalan Data Pendidikan Tinggi</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background-color: #f4f4f4;
        }

        header {
            background-color: #1877f2;
            padding: 10px 20px;
            color: white;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .header-title {
            font-size: 24px;
            font-weight: bold;
        }

        .content {
            padding: 20px;
            text-align: left;
        }

        h2 {
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        table th {
            background-color: #1877f2;
            color: white;
            padding: 10px;
        }

        table td {
            padding: 10px;
        }

        .no-results {
            text-align: center;
            font-size: 18px;
            margin-top: 20px;
            color: #888;
        }

        .action-link {
            color: #1877f2;
            text-decoration: none;
        }

        .action-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <header>
        <div class="header-title">Pangkalan Data Pendidikan Tinggi</div>
    </header>

    <div class="content">
        <h2>Hasil Pencarian untuk "<?php echo htmlspecialchars($_GET['q']); ?>"</h2>
        <table>
            <tr>
                <th>Username</th>
                <th>Email</th>
                <th>Specialization</th>
                <th>Job Status</th>
                <th>Aksi</th>
            </tr>
            <!-- PHP untuk menampilkan hasil pencarian -->
            <?php if (!empty($search_results)): ?>
                <?php foreach ($search_results as $row): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['username']); ?></td>
                        <td><?php echo htmlspecialchars($row['email']); ?></td>
                        <td><?php echo htmlspecialchars($row['specialization_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['job_status_name']); ?></td>
                        <td><a class="action-link" href="datauser.php?username=<?php echo urlencode($row['username']); ?>">Lihat Detail</a></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" class="no-results">Tidak ada data ditemukan.</td>
                </tr>
            <?php endif; ?>
        </table>
    </div>

</body>
</html>
