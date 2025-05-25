<?php
include 'config.php'; // Include database configuration

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Collect and sanitize form data
    $name = htmlspecialchars(trim($_POST['name']));
    $email = strtolower(trim($_POST['email'])); // Normalize email to lowercase
    $mobile = trim($_POST['mobile']);
    $user_type = htmlspecialchars(trim($_POST['userType']));
    $consultant_area = isset($_POST['consultantArea']) ? htmlspecialchars(trim($_POST['consultantArea'])) : null;
    $password = $_POST['password'];
    $confirm_password = $_POST['confirmPassword'];

    // Validate that passwords match
    if ($password !== $confirm_password) {
        echo "<script>alert('Passwords do not match!'); window.history.back();</script>";
        exit();
    }

    // Hash the password securely
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Handle file upload if user type is consultant
    $proofFilePath = null;
    if ($user_type === 'consultant') {
        if (isset($_FILES['proofUpload'])) {
            $fileError = $_FILES['proofUpload']['error'];

            // Debugging: Log the $_FILES array
            error_log("File upload details: " . print_r($_FILES['proofUpload'], true));

            if ($fileError === UPLOAD_ERR_OK) {
                $uploadsDir = "uploads/";
                $fileName = basename($_FILES['proofUpload']['name']);
                $uniqueFileName = uniqid() . "_" . $fileName;
                $targetFilePath = $uploadsDir . $uniqueFileName;

                // Validate file type
                $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));
                $allowedTypes = ['pdf', 'jpg', 'jpeg', 'png'];
                if (!in_array($fileType, $allowedTypes)) {
                    error_log("Invalid file type: $fileType");
                    echo "<script>alert('Invalid file type. Only PDF, JPG, JPEG, and PNG are allowed.'); window.history.back();</script>";
                    exit();
                }

                // Validate file size
                if ($_FILES['proofUpload']['size'] > 5 * 1024 * 1024) { // Limit to 5MB
                    error_log("File size exceeds limit: " . $_FILES['proofUpload']['size']);
                    echo "<script>alert('File size exceeds the 5MB limit.'); window.history.back();</script>";
                    exit();
                }

                // Attempt to move the uploaded file
                if (move_uploaded_file($_FILES['proofUpload']['tmp_name'], $targetFilePath)) {
                    $proofFilePath = $targetFilePath; // Store the file path for the database
                } else {
                    error_log("Failed to move uploaded file: " . $_FILES['proofUpload']['tmp_name']);
                    echo "<script>alert('Failed to move the uploaded file. Please check server permissions.'); window.history.back();</script>";
                    exit();
                }
            } else {
                // Handle specific file upload errors
                switch ($fileError) {
                    case UPLOAD_ERR_INI_SIZE:
                        error_log("Error: Uploaded file exceeds the `upload_max_filesize` in php.ini.");
                        echo "<script>alert('Uploaded file exceeds the allowed size (server limit).'); window.history.back();</script>";
                        break;
                    case UPLOAD_ERR_FORM_SIZE:
                        error_log("Error: Uploaded file exceeds the `MAX_FILE_SIZE` specified in the form.");
                        echo "<script>alert('Uploaded file exceeds the allowed size (form limit).'); window.history.back();</script>";
                        break;
                    case UPLOAD_ERR_NO_FILE:
                        error_log("Error: No file was uploaded.");
                        echo "<script>alert('Proof file is required for consultants. Please upload a valid file.'); window.history.back();</script>";
                        break;
                    case UPLOAD_ERR_NO_TMP_DIR:
                        error_log("Error: Temporary folder missing. Check `upload_tmp_dir` in php.ini.");
                        echo "<script>alert('Temporary folder missing. Please contact the administrator.'); window.history.back();</script>";
                        break;
                    case UPLOAD_ERR_CANT_WRITE:
                        error_log("Error: Failed to write file to disk. Check server permissions.");
                        echo "<script>alert('Failed to write file to disk. Please check permissions.'); window.history.back();</script>";
                        break;
                    default:
                        error_log("Error: Unknown error occurred during file upload. Error code: $fileError.");
                        echo "<script>alert('An unknown error occurred during file upload. Please try again.'); window.history.back();</script>";
                        break;
                }
                exit();
            }
        } else {
            error_log("Error: File input `proofUpload` is not set.");
            echo "<script>alert('Proof file is required for consultants. Please upload a valid file.'); window.history.back();</script>";
            exit();
        }
    }

    // Check if email or mobile already exists in the database
    $checkQuery = "
        SELECT email, mobile FROM users WHERE email = ? OR mobile = ?
        UNION
        SELECT email, mobile FROM consultant_pending WHERE email = ? OR mobile = ?
    ";
    $checkStmt = $conn->prepare($checkQuery);
    $checkStmt->bind_param("ssss", $email, $mobile, $email, $mobile);
    $checkStmt->execute();
    $result = $checkStmt->get_result();

    if ($result->num_rows > 0) {
        // If email or mobile number already exists
        error_log("Error: Duplicate email or mobile detected. Email: $email, Mobile: $mobile.");
        echo "<script>alert('Email or mobile number already exists! Please try again.'); window.history.back();</script>";
        $checkStmt->close();
        exit();
    }
    $checkStmt->close();

    // Insert data into the appropriate table based on user type
    if ($user_type === 'consultant') {
        $sql = "INSERT INTO consultant_pending (name, email, mobile, user_type, consultant_area, file_path, password, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, NOW())";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssss", $name, $email, $mobile, $user_type, $consultant_area, $proofFilePath, $hashed_password);
    } else {
        $sql = "INSERT INTO users (name, email, mobile, user_type, password, created_at) 
                VALUES (?, ?, ?, ?, ?, NOW())";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssss", $name, $email, $mobile, $user_type, $hashed_password);
    }

    // Execute the insert statement
    if ($stmt->execute()) {
        if ($user_type === 'consultant') {
            echo "<script>alert('Your application as a consultant is under review.'); window.location.href = 'login.html';</script>";
        } else {
            header("Location: login.html");
        }
    } else {
        error_log("Database insertion error: " . $stmt->error);
        echo "<script>alert('Error: Unable to create account. Please try again later.'); window.history.back();</script>";
    }

    $stmt->close();
}

$conn->close(); // Close the database connection
?>
