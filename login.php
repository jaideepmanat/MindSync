<?php
session_start(); // Start a new session

include 'config.php'; // Include your database configuration file

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize input to prevent SQL injection
    $email = trim($_POST['emailSignIn']);
    $password = trim($_POST['passwordSignIn']);

    // Check if fields are empty
    if (empty($email) || empty($password)) {
        echo "<script>alert('Please fill in all required fields.'); window.history.back();</script>";
        exit();
    }

    // Prepare SQL statement to check for user
    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if user exists
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc(); // Fetch user data

        // Verify password
        if (password_verify($password, $user['password'])) {
            // Store user data in session
            $_SESSION['user_id'] = $user['id'];

            // Check if user_type is set and valid
            if (isset($user['user_type']) && !empty($user['user_type'])) {
                $_SESSION['user_type'] = $user['user_type'];
                
                // Redirect based on user type
                if ($user['user_type'] === 'normal') {
                    header("Location: home1.php"); // Redirect to user home page
                } elseif ($user['user_type'] === 'consultant') {
                    header("Location: home2.php"); // Redirect to consultant home page
                }
                exit();
            } else {
                // Error handling if user_type is not set or is invalid
                echo "<script>alert('Error: User type is undefined or invalid for this account.'); window.history.back();</script>";
                exit();
            }
        } else {
            echo "<script>alert('Incorrect password!'); window.history.back();</script>"; // Alert for incorrect password
            exit();
        }
    } else {
        echo "<script>alert('Email not registered!'); window.history.back();</script>"; // Alert for unregistered email
        exit();
    }

    // Close statement and connection
    $stmt->close();
    $conn->close();
} else {
    // Redirect to login page if accessed directly
    header("Location: login.html");
    exit();
}
?>
