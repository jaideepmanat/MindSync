<?php
// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

// Check if the user is logged in; if not, redirect to login page
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

// Include database configuration
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // URL of the Flask API
    $url = 'http://127.0.0.1:5000/predict';

    // Collect form data into an array
    $data = [];
    foreach ($_POST as $key => $value) {
        $formatted_key = str_replace('_', ' ', $key);
        $data[$formatted_key] = is_numeric($value) ? (int)$value : $value;
    }

    // Convert data to JSON
    $data_json = json_encode($data);

    // Initialize cURL
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data_json);

    // Execute the request and get the response
    $response = curl_exec($ch);
    if ($response === false) {
        echo 'cURL error: ' . curl_error($ch);
        exit();
    }
    curl_close($ch);

    // Decode the JSON response
    $result = json_decode($response, true);
    if (!$result || !isset($result['prediction'])) {
        echo "<p>Error decoding JSON or 'prediction' key not found in response.</p>";
        exit();
    }

    $prediction = $result['prediction'];

    // Decode the JSON response
$result = json_decode($response, true);
if (!$result || !isset($result['prediction'])) {
    echo "<p>Error decoding JSON or 'prediction' key not found in response.</p>";
    exit();
}

$prediction = $result['prediction'];
// Store the prediction in session for use in Outcome2.php
$_SESSION['risk_level'] = $prediction;


// Insert prediction into the database
try {
    $user_id = $_SESSION['user_id'];
    $stmt = $conn->prepare("INSERT INTO predictions (user_id, prediction, prediction_data, created_at) VALUES (?, ?, ?, NOW())");
    $stmt->bind_param("iss", $user_id, $prediction, $data_json);
    $stmt->execute();
    
    // Redirect based on prediction value
    if ($prediction === "High" || $prediction === "Mid") {
        header("Location: Outcome2.php");
    } else if ($prediction === "Low") {
        header("Location: Outcome.php");
    }
    exit();

} catch (mysqli_sql_exception $e) {
    echo "<p>Error storing prediction: " . htmlspecialchars($e->getMessage()) . "</p>";
    exit();
}


    // Insert prediction into the database
    try {
        $user_id = $_SESSION['user_id'];
        $stmt = $conn->prepare("INSERT INTO predictions (user_id, prediction, prediction_data, created_at) VALUES (?, ?, ?, NOW())");
        $stmt->bind_param("iss", $user_id, $prediction, $data_json);
        $stmt->execute();
    } catch (mysqli_sql_exception $e) {
        echo "<p>Error storing prediction: " . htmlspecialchars($e->getMessage()) . "</p>";
        exit();
    }
}
?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mental Health Risk Prediction</title>
    <link rel="stylesheet" href="form.css">
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
                <a href="prediction.php">Predictions</a>
                <a href="table.php">History</a>
                <a href="logout.php">Logout</a>
            </div>
        </div>
    </nav>
    <div class="form-container">
    <form method="post" action="">
        <label for="age">Age:</label>
        <input type="number" id="age" name="Age" required><br><br>

        <label for="gender">Gender:</label>
        <select id="gender" name="gender" required>
            <option value="Female">Female</option>
            <option value="Male">Male</option>
            <option value="Other">Other</option>
            <option value="Prefer not to say">Prefer not to say</option>
        </select><br><br>

        <label for="stress">How often do you feel stressed? (1-5):</label>
        <input type="number" id="stress" name="How often do you feel stressed?" min="1" max="5" required><br><br>

        <label for="anxious">How often have you felt anxious or worried in the past month? (1-5):</label>
        <input type="number" id="anxious" name="How often have you felt anxious or worried in the past month?" min="1" max="5" required><br><br>

        <label for="depressed">How often have you felt down, depressed, or hopeless? (1-5):</label>
        <input type="number" id="depressed" name="How often have you felt down, depressed, or hopeless?" min="1" max="5" required><br><br>

        <label for="self_care">How often do you engage in self-care activities? (1-5):</label>
        <input type="number" id="self_care" name="How often do you engage in self-care activities?" min="1" max="5" required><br><br>

        <label for="sleep">How would you rate your sleep quality over the past week? (1-5):</label>
        <input type="number" id="sleep" name="How would you rate your sleep quality over the past week?" min="1" max="5" required><br><br>

        <label for="exercise">How often do you exercise? (1-5):</label>
        <input type="number" id="exercise" name="How often do you exercise?" min="1" max="5" required><br><br>

        <label for="diet">How balanced do you consider your diet? (1-5):</label>
        <input type="number" id="diet" name="How balanced do you consider your diet?" min="1" max="5" required><br><br>

        <label for="substances">How often do you consume alcohol or use recreational substances? (1-5):</label>
        <input type="number" id="substances" name="How often do you consume alcohol or use recreational substances?" min="1" max="5" required><br><br>

        <label for="academic_satisfaction">How satisfied are you with your academic performance? (1-5):</label>
        <input type="number" id="academic_satisfaction" name="How satisfied are you with your academic performance?" min="1" max="5" required><br><br>

        <label for="academic_pressure">How pressured do you feel by your academic responsibilities? (1-5):</label>
        <input type="number" id="academic_pressure" name="How pressured do you feel by your academic responsibilities?" min="1" max="5" required><br><br>

        <label for="procrastinate">How often do you procrastinate? (1-5):</label>
        <input type="number" id="procrastinate" name="How often do you procrastinate?" min="1" max="5" required><br><br>

        <label for="balance">How balanced do you feel between work/study and personal life? (1-5):</label>
        <input type="number" id="balance" name="How balanced do you feel between work/study and personal life?" min="1" max="5" required><br><br>

        <label for="resilience">How resilient do you consider yourself in the face of challenges? (1-5):</label>
        <input type="number" id="resilience" name="How resilient do you consider yourself in the face of challenges?" min="1" max="5" required><br><br>

        <label for="optimism">How optimistic are you about your future? (1-5):</label>
        <input type="number" id="optimism" name="How optimistic are you about your future?" min="1" max="5" required><br><br>

        <label for="emotional_awareness">How aware are you of your emotions and feelings? (1-5):</label>
        <input type="number" id="emotional_awareness" name="How aware are you of your emotions and feelings?" min="1" max="5" required><br><br>

        <label for="coping_mechanisms">How effective are your coping mechanisms? (1-5):</label>
        <input type="number" id="coping_mechanisms" name="How effective are your coping mechanisms?" min="1" max="5" required><br><br>

        <label for="loneliness">How often do you feel lonely? (1-5):</label>
        <input type="number" id="loneliness" name="How often do you feel lonely?" min="1" max="5" required><br><br>

        <label for="family_relationship">How would you rate your relationship with your family? (1-5):</label>
        <input type="number" id="family_relationship" name="How would you rate your relationship with your family?" min="1" max="5" required><br><br>

        <label for="romantic_relationship">How satisfied are you with your romantic relationship (if applicable)? (1-5):</label>
        <input type="number" id="romantic_relationship" name="How satisfied are you with your romantic relationship (if applicable)?" min="1" max="5" required><br><br>

        <label for="social_anxiety">How anxious do you feel in social situations? (1-5):</label>
        <input type="number" id="social_anxiety" name="How anxious do you feel in social situations?" min="1" max="5" required><br><br>

        <label for="communication">How confident are you in your communication skills? (1-5):</label>
        <input type="number" id="communication" name="How confident are you in your communication skills?" min="1" max="5" required><br><br>

        <label for="mental_health_diagnosis">Have you ever been diagnosed with a mental health condition?</label>
        <select id="mental_health_diagnosis" name="Have you ever been diagnosed with a mental health condition?" required>
            <option value="Yes">Yes</option>
            <option value="No">No</option>
        </select><br><br>

        <label for="therapy">Have you ever attended therapy or counseling sessions?</label>
        <select id="therapy" name="Have you ever attended therapy or counseling sessions?" required>
            <option value="Yes">Yes</option>
            <option value="No">No</option>
        </select><br><br>

        <label for="physical_health">How would you rate your physical health over the past month? (1-5):</label>
        <input type="number" id="physical_health" name="How would you rate your physical health over the past month?" min="1" max="5" required><br><br>

        <input type="submit" name="submit" value="Get Prediction">
    </form>
    </div>
<script src="form.js"></script>

</body>
</html>
