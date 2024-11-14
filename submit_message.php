<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "error: User not logged in";
    exit();
}

// Include database connection
$host = "localhost";
$user = "root";
$password = ""; // Default XAMPP password
$dbname = "mindsync";

$conn = new mysqli($host, $user, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Retrieve user ID, consultant ID, and message from the POST request
$userId = $_SESSION['user_id'];
$consultantId = $_POST['consultant_id'];
$messageText = $_POST['message_text'];

// Prepare and execute the SQL query to insert the message
$sql = "INSERT INTO messages (user_id, consultant_id, message_text, created_at) VALUES (?, ?, ?, NOW())";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iis", $userId, $consultantId, $messageText);

if ($stmt->execute()) {
    echo "success";
} else {
    echo "error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
