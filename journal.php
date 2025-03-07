<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Redirect to login if no session exists
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Journal</title>
    <link rel="stylesheet" href="journal.css">
</head>
<body>
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
            <a href="Profile.php">View Profile</a>
            <a href="table.php">History</a>
            <a href="logout.php">Logout</a>
        </div>
    </div>
</nav>

<div class="journal-container">
    <div class="card text-entry">
        <textarea id="journal-text" placeholder="Write your journal..." class="journal-text"></textarea>
        <button id="submit-journal" class="submit-journal">Submit</button>
    </div>
    <div class="card record-entry">
        <button id="record-btn">🎤</button>
        <audio id="audio-preview" controls style="display: none;"></audio>
        <button id="cancel-record" class="cancel-btn" style="display: none;">❌ Cancel</button>
        <button id="submit-recording" class="submit-btn" style="display: none;">Submit</button>
    </div>
</div>

<script src="journal.js"></script>
</body>
</html>
