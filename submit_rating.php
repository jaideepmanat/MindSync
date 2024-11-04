<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $consultantId = $data['consultantId'];
    $ratingValue = $data['rating'];

    // Database connection
    $host = "localhost";
    $user = "root";
    $password = ""; // XAMPP default
    $dbname = "mindsync"; // Your database name

    $conn = new mysqli($host, $user, $password, $dbname);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Check if the user has already rated this consultant
    $userId = $_SESSION['user_id'];
    $checkRatingSql = "SELECT * FROM ratings WHERE consultant_id = ? AND user_id = ?";
    $stmt = $conn->prepare($checkRatingSql);
    $stmt->bind_param("ii", $consultantId, $userId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'You can only rate once.']);
    } else {
        // Insert the rating into the database
        $insertRatingSql = "INSERT INTO ratings (consultant_id, user_id, rating) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($insertRatingSql);
        $stmt->bind_param("iii", $consultantId, $userId, $ratingValue);
        $stmt->execute();

        // Calculate the new average rating
        $avgRatingSql = "SELECT AVG(rating) AS average FROM ratings WHERE consultant_id = ?";
        $stmt = $conn->prepare($avgRatingSql);
        $stmt->bind_param("i", $consultantId);
        $stmt->execute();
        $averageResult = $stmt->get_result()->fetch_assoc();

        echo json_encode(['success' => true, 'newAverage' => round($averageResult['average'], 1)]);
    }

    $stmt->close();
    $conn->close();
}
?>
