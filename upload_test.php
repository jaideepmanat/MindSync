<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>File Upload Test</title>
</head>
<body>
    <form action="upload_test.php" method="post" enctype="multipart/form-data">
        <label for="proofUpload">Upload Proof File:</label>
        <input type="file" name="proofUpload" id="proofUpload" required>
        <button type="submit">Upload</button>
    </form>

    <?php
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        echo "<pre>";
        print_r($_FILES);
        echo "</pre>";
    }
    ?>
</body>
</html>
