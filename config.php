<?php
$host = "localhost";
$user = "root";
$password = ""; // XAMPP default
$dbname = "mindsync"; // Updated database name

// Create a database connection
$conn = new mysqli($host, $user, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
