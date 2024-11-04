<?php
session_start(); // Start the session

// Database connection
$host = "localhost";
$user = "root";
$password = ""; // XAMPP default
$dbname = "mindsync";

$conn = new mysqli($host, $user, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Proceed only if the user is logged in
$user_id = $_SESSION['user_id']; // Logged-in user ID from session

// Get the consultant ID from POST data
if (isset($_POST['consultantName'])) {
    $consultant_name = $_POST['consultantName'];

    // Retrieve the consultant's ID from the users table
    $query = "SELECT id FROM users WHERE name = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $consultant_name);
    $stmt->execute();
    $stmt->bind_result($consultant_id);
    $stmt->fetch();
    $stmt->close();

    if ($consultant_id) {
        // Insert booking record into booked_users table
        $insertQuery = "INSERT INTO booked_users (consultant_id, user_id) VALUES (?, ?)";
        $stmt = $conn->prepare($insertQuery);
        $stmt->bind_param("ii", $consultant_id, $user_id);

        if ($stmt->execute()) {
            echo "success";
        } else {
            echo "Failed to book the consultant: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "Consultant not found.";
    }
} else {
    echo "Consultant name not provided.";
}

$conn->close();
?>