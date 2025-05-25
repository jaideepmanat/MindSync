<?php
// Include the database configuration file
include 'config.php';

// Get the input data
$data = json_decode(file_get_contents("php://input"), true);
$email = $data['email'];
$mobile = $data['mobile'];

// Prepare a SQL statement to check for existing email and mobile
$sql = "SELECT * FROM users WHERE email = ? OR mobile = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $email, $mobile);
$stmt->execute();
$result = $stmt->get_result();

// Check if any row is returned
if ($result->num_rows > 0) {
    // Email or mobile already exists
    echo json_encode(['exists' => true]);
} else {
    // Email and mobile are unique
    echo json_encode(['exists' => false]);
}

// Close the statement and connection
$stmt->close();
$conn->close();
?>
