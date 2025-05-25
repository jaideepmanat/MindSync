<?php
session_start(); // Start the session

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html"); // Redirect to login if not logged in
    exit();
}

// Optional: Further restrict access based on user type
if ($_SESSION['user_type'] !== 'normal') {
    header("Location: login.html"); // Redirect if user type doesn't match
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
    die("Connection failed: " . $conn->connect_error);
}

// Handling new question submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['question_text'])) {
    $question = $conn->real_escape_string($_POST['question_text']);
    $userId = $_SESSION['user_id'];

    // Insert the new question without the `anonymous` field
    $sql = "INSERT INTO questions (question_text, user_id) VALUES ('$question', '$userId')";
    if ($conn->query($sql) === TRUE) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Failed to post question.']);
    }
    exit();
}

// Fetch questions and their replies
$sql = "SELECT q.id AS question_id, q.question_text, 
               IFNULL(r.reply_text, 'Not replied yet') AS reply_text 
        FROM questions q
        LEFT JOIN replies r ON q.id = r.question_id
        ORDER BY q.created_at DESC";

$result = $conn->query($sql);

$questions = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $questions[] = $row;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Community Forum</title>
    <link rel="stylesheet" href="CommunityUser.css">
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
                <a href="home1.php">Home</a>
                <a href="Profile.php">View Profile</a>
                <a href="logout.php">Logout</a>
            </div>
        </div>
    </nav>

    <div class="spacer"> </div>
    <div class="card">
        <h2>Community Forum</h2>

        <!-- Display questions and replies from the database -->
        <?php foreach ($questions as $question): ?>
            <div class="question">
                <h3><?php echo htmlspecialchars($question['question_text']); ?></h3>
                <div class="reply">
                    <p><?php echo htmlspecialchars($question['reply_text']); ?></p>
                </div>
            </div>
        <?php endforeach; ?>

        <!-- Form for New Question -->
        <div class="question-form">
            <h3>Ask a Question</h3>
            <textarea id="userQuestion" placeholder="Enter your question here..."></textarea>
            <button class="button" onclick="submitQuestion()">Submit Question</button>
        </div>
    </div>

    <script src="CommunityUser.js"></script>
</body>
</html>