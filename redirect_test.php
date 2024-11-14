<?php
session_start();
$_SESSION['user_id'] = 1; // Simulate a logged-in user

$prediction = "low"; // Change to "high", "medium", or "low" to test each case

if ($prediction === "high" || $prediction === "medium") {
    echo "Redirecting to consult.php<br>";
    header("Location: consult.php");
    echo "<script>window.location.href='consult.php';</script>";
} elseif ($prediction === "low") {
    echo "Redirecting to outcome.php<br>";
    header("Location: outcome.php");
    echo "<script>window.location.href='outcome.php';</script>";
}
exit();
?>
