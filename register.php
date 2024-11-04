<?php
include 'config.php'; // Include your database configuration file

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Collect form data
    $name = $_POST['name'];
    $email = $_POST['email'];
    $mobile = $_POST['mobile'];
    $user_type = $_POST['userType'];
    $consultant_area = isset($_POST['consultantArea']) ? $_POST['consultantArea'] : null;
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash the password

    // Check if email or mobile already exists
    $checkQuery = "SELECT * FROM users WHERE email = ? OR mobile = ?";
    $checkStmt = $conn->prepare($checkQuery);
    $checkStmt->bind_param("ss", $email, $mobile);
    $checkStmt->execute();
    $result = $checkStmt->get_result();

    if ($result->num_rows > 0) {
        // Email or mobile already exists
        echo "<script>alert('Email or mobile number already exists!'); window.history.back();</script>";
    } else {
        // Prepare SQL statement for new user
        $sql = "INSERT INTO users (name, email, mobile, user_type, consultant_area, password) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssss", $name, $email, $mobile, $user_type, $consultant_area, $password);

        if ($stmt->execute()) {
            // Account created successfully, redirect to the login page
            header("Location: login.html"); // Redirect to the login page
            exit();
        } else {
            echo "Error: " . $stmt->error; // Display any errors
        }

        $stmt->close();
    }

    $checkStmt->close();
}

$conn->close(); // Close the database connection
?>
