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
?>
