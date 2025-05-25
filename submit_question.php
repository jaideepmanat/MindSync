<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'User not logged in.']);
    exit();
}

// Database connection
$host = "localhost";
$user = "root";
$password = ""; // Default XAMPP password
$dbname = "mindsync"; // Database name

$conn = new mysqli($host, $user, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'error' => 'Database connection failed.']);
    exit();
}

// Handle the question submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['question_text'])) {
    $questionText = $conn->real_escape_string($_POST['question_text']);
    $userId = $_SESSION['user_id'];

    // Insert question into the database
    $sql = "INSERT INTO questions (question_text, user_id) VALUES ('$questionText', '$userId')";
    if ($conn->query($sql) === TRUE) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Failed to post question.']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request.']);
}

$conn->close();
?>
