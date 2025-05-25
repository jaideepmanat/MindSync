<?php
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];

    // Delete the consultant from consultant_pending table
    $query = "DELETE FROM consultant_pending WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo json_encode(["message" => "Consultant rejected successfully"]);
    } else {
        echo json_encode(["error" => "Error rejecting consultant"]);
    }
}
?>
