<?php
session_start();

// Check if the user is logged in and is a valid session
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include 'config.php';  // Include database connection

$user_id = $_SESSION['user_id'];  // Get the logged-in user ID

// Determine if the logged-in user is a consultant or a normal user
$query_user_type = "SELECT user_type FROM users WHERE id = $user_id";
$result_user_type = mysqli_query($conn, $query_user_type);
if (!$result_user_type) {
    die("Query failed: " . mysqli_error($conn));
}
$user_data = mysqli_fetch_assoc($result_user_type);
$user_type = $user_data['user_type'];

$messages = [];  // Initialize an empty array for storing messages/feedback

if ($user_type === 'normal') {
    // If the user is a normal user, fetch feedback from consultants
    $query = "
        SELECT feedback.*, users.name AS consultant_name 
        FROM feedback 
        JOIN users ON feedback.consultant_id = users.id 
        WHERE feedback.user_id = $user_id 
        ORDER BY feedback.created_at DESC
    ";
    $result = mysqli_query($conn, $query);
    if (!$result) {
        die("Query failed: " . mysqli_error($conn));
    }

    while ($row = mysqli_fetch_assoc($result)) {
        $messages[] = $row;  // Store feedback rows in an array
    }

} elseif ($user_type === 'consultant') {
    // If the user is a consultant, fetch messages from users
    $query = "
        SELECT messages.*, users.name AS user_name 
        FROM messages 
        JOIN users ON messages.user_id = users.id 
        WHERE messages.consultant_id = $user_id 
        ORDER BY messages.created_at DESC
    ";
    $result = mysqli_query($conn, $query);
    if (!$result) {
        die("Query failed: " . mysqli_error($conn));
    }

    while ($row = mysqli_fetch_assoc($result)) {
        $messages[] = $row;  // Store message rows in an array
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consult</title>
    <link rel="stylesheet" href="mail.css">
</head>
<body>
    <nav>
        <div class="left">
            <h1>MindSync</h1>
        </div>
        <div class="right">
            <img src="https://cdn-icons-png.flaticon.com/512/147/147142.png" alt="User Icon" class="user-icon" id="user-icon">
            <div class="dropdown" id="dropdown-menu">
                <a href="home1.php">Home</a>
                <a href="Profile.php">View Profile</a>
                <a href="logout.php">Logout</a>
            </div>
        </div>
    </nav>
    
    <section class="cards">
        <?php
        if (empty($messages)) {
            echo "<p>No messages or feedback available.</p>";
        } else {
            foreach ($messages as $message) {
                if ($user_type === 'normal') {
                    // Display feedback from consultant to user
                    echo "
                    <div class='card' onclick='toggleCard(this)'>
                        <div class='cardtitle'>
                            <p>Feedback from {$message['consultant_name']}</p>
                            <div class='cardContent' style='display:none;'>{$message['feedback_text']}</div>
                        </div>
                    </div>
                    ";
                } elseif ($user_type === 'consultant') {
                    // Display messages from user to consultant
                    echo "
                    <div class='card' onclick='toggleCard(this)'>
                        <div class='cardtitle'>
                            <p>Message from {$message['user_name']}</p>
                            <div class='cardContent' style='display:none;'>{$message['message_text']}</div>
                        </div>
                    </div>
                    ";
                }
            }
        }
        ?>
    </section>

    <script src="mail.js"></script>
</body>
</html>
