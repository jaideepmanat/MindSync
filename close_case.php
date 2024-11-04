<?php
session_start(); // Start the session
include 'config.php'; // Include your database configuration file

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("HTTP/1.1 403 Forbidden");
    exit("Access denied.");
}

// Get the user_id from the request
if (isset($_POST['user_id'])) {
    $userId = $_POST['user_id'];
    $consultantId = $_SESSION['user_id'];

    // Prepare SQL statement to delete the booked user
    $sql = "DELETE FROM booked_users WHERE user_id = ? AND consultant_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $userId, $consultantId); // Assuming both are integers
    if ($stmt->execute()) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false, "error" => $stmt->error]);
    }
    $stmt->close();
} else {
    echo json_encode(["success" => false, "error" => "User ID not provided."]);
}
$conn->close(); // Close the database connection
?>
