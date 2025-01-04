<?php
// Include the database configuration file
include 'config.php';

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve the consultant ID from the POST data
    $id = isset($_POST['id']) ? $_POST['id'] : null;

    if (!$id) {
        echo json_encode(["error" => "Invalid consultant ID."]);
        exit;
    }

    // Fetch the consultant details from the `consultant_pending` table
    $query = "SELECT * FROM consultant_pending WHERE id = ?";
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        echo json_encode(["error" => "Error preparing SELECT query: " . $conn->error]);
        exit;
    }
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        echo json_encode(["error" => "Consultant not found in pending list."]);
        exit;
    }

    $consultant = $result->fetch_assoc();

    // Insert the consultant details into the `users` table (without id for auto-increment)
    $insertQuery = "INSERT INTO users (name, email, mobile, user_type, consultant_area, password, created_at) 
                    VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($insertQuery);
    if (!$stmt) {
        echo json_encode(["error" => "Error preparing INSERT query: " . $conn->error]);
        exit;
    }
    $stmt->bind_param(
        "sssssss",
        $consultant['name'],
        $consultant['email'],
        $consultant['mobile'],
        $consultant['user_type'],
        $consultant['consultant_area'],
        $consultant['password'],
        $consultant['created_at']
    );

    if (!$stmt->execute()) {
        echo json_encode(["error" => "Error inserting consultant into users table: " . $stmt->error]);
        exit;
    }

    // Delete the consultant from the `consultant_pending` table
    $deleteQuery = "DELETE FROM consultant_pending WHERE id = ?";
    $stmt = $conn->prepare($deleteQuery);
    if (!$stmt) {
        echo json_encode(["error" => "Error preparing DELETE query: " . $conn->error]);
        exit;
    }
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo json_encode(["message" => "Consultant accepted successfully."]);
    } else {
        echo json_encode(["error" => "Error deleting consultant from pending list: " . $stmt->error]);
    }
} else {
    echo json_encode(["error" => "Invalid request method."]);
}
?>
