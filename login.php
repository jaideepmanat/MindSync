<?php
session_start(); // Start a session
include 'config.php'; // Include database configuration

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['emailSignIn']);
    $password = trim($_POST['passwordSignIn']);

    // Input validation
    if (empty($email) || empty($password)) {
        header("Location: login.html?error=Fill%20all%20required%20fields");
        exit();
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header("Location: login.html?error=Invalid%20email%20format");
        exit();
    }

    // Special case for admin login
    if ($email === 'admin@test.com' && $password === 'admin') {
        session_regenerate_id(true); // Regenerate session ID
        $_SESSION['user_id'] = 'admin';
        $_SESSION['user_type'] = 'admin';

        // Redirect to admin page
        header("Location: admin.php");
        exit();
    }

    // Proceed with normal user authentication
    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password'])) {
            session_regenerate_id(true); // Regenerate session ID

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_type'] = $user['user_type'] ?? 'undefined';

            // Redirect based on user type
            if ($user['user_type'] === 'normal') {
                header("Location: home1.php");
            } elseif ($user['user_type'] === 'consultant') {
                header("Location: home2.php");
            } else {
                header("Location: login.html?error=Invalid%20user%20type");
            }
            exit();
        } else {
            header("Location: login.html?error=Incorrect%20Password");
            exit();
        }
    } else {
        header("Location: login.html?error=Email%20not%20registered");
        exit();
    }

    $stmt->close();
    $conn->close();
} else {
    header("Location: login.html");
    exit();
}
?>
