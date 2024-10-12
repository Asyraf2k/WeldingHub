<?php
session_start();
include '../koneksi.php'; // Ganti dengan jalur yang sesuai ke file koneksi database

if (!isset($_SESSION['username'])) {
    exit(); // Jika tidak ada sesi, keluar
}

$username = $_SESSION['username'];
$receiver_username = $_GET['receiver_username'];
$last_message_time = isset($_GET['last_message_time']) ? (int)$_GET['last_message_time'] : 0;

// Mendapatkan pesan baru
$stmt = $conn->prepare("SELECT * FROM messages WHERE ((sender_username = ? AND receiver_username = ?) OR (sender_username = ? AND receiver_username = ?)) AND UNIX_TIMESTAMP(sent_at) > ? ORDER BY sent_at ASC");
$stmt->bind_param("ssssi", $username, $receiver_username, $receiver_username, $username, $last_message_time);
$stmt->execute();
$result = $stmt->get_result();
$messages = [];
while ($row = $result->fetch_assoc()) {
    $messages[] = $row; // Menyimpan semua pesan
}
$stmt->close();

// Mengembalikan pesan dalam format JSON
echo json_encode($messages);
?>
