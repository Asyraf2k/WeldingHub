<?php
$servername = "localhost"; // Your database server
$username = "root"; // Your database username
$password = "12345678"; // Your database password
$dbname = "lasindo"; // Your database name
$port = 3307; // Port number if you're using a non-default port

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname, $port);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$query = $_GET['query'] ?? '';
$searchQuery = "%" . $conn->real_escape_string($query) . "%";

// Ambil data dari tabel users yang sesuai dengan kata kunci
$sql = "SELECT username, email, specialization_name, job_status_name FROM users WHERE username LIKE ? OR specialization_name LIKE ? OR job_status_name LIKE ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sss", $searchQuery, $searchQuery, $searchQuery);
$stmt->execute();
$result = $stmt->get_result();

$users = [];
while ($row = $result->fetch_assoc()) {
    $users[] = $row;
}

$stmt->close();
$conn->close();

// Mengembalikan hasil pencarian dalam format JSON
header('Content-Type: application/json');
echo json_encode($users);
?>
