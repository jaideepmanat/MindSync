<?php
session_start(); // Start the session

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html"); // Redirect to login if not logged in
    exit();
}

// Restrict access to consultants only
if ($_SESSION['user_type'] !== 'consultant') {
    header("Location: login.html"); // Redirect if user type doesn't match
    exit();
}

require 'config.php'; // Include the database connection file

// Fetch questions and their replies
$sql = "SELECT q.id AS question_id, q.question_text, r.reply_text
        FROM questions q
        LEFT JOIN replies r ON q.id = r.question_id"; 

$result = $conn->query($sql);
$questions = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $questions[] = $row;
    }
}

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Community Consult</title>
    <link rel="stylesheet" href="CommunityConsult.css">
    <link rel="icon" type="image/x-icon" href="icon.png">
</head>
<body>
    <!-- Navbar -->
    <nav>
        <div class="left">
            <h1>MindSync</h1>
        </div>
        <div class="right">
        <div class="mailbox" onclick="window.location.href='mail.php';">
            <span>&#9993;</span>
        </div>
            <img src="https://cdn-icons-png.flaticon.com/512/147/147142.png" alt="User Icon" class="user-icon" id="user-icon">
            <div class="dropdown" id="dropdown-menu">
            <a href="home2.php">Home</a>
                <a href="Profile.php">View Profile</a>
                <a href="logout.php">Logout</a>
            </div>
        </div>
    </nav>
    
    <div class="card">
        <h2>Community Forum - Consultant</h2>

        <?php foreach ($questions as $question): ?>
            <div class="question">
                <h3><?php echo htmlspecialchars($question['question_text']); ?></h3>
                <div class="reply-container">
                    <?php if ($question['reply_text']): ?>
                        <div class="reply">
                            <h4>Reply by Consultant:</h4>
                            <p><?php echo htmlspecialchars($question['reply_text']); ?></p>
                        </div>
                    <?php else: ?>
                        <div class="reply-form">
                            <h4>Your Response:</h4>
                            <textarea id="reply<?php echo $question['question_id']; ?>" placeholder="Enter your response..."></textarea>
                            <button class="button" onclick="submitReply(<?php echo $question['question_id']; ?>)">Submit Reply</button>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <script src="CommunityConsult.js"></script>
</body>
</html>
