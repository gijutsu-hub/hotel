<?php
session_start();

if (!isset($_SESSION['subs'])) {
    echo "Unauthorized access.";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $uploadDir = 'uploadsss/';  // Make sure this directory exists and is writable
    $fileName = basename($_FILES['file']['name']);
    $uploadFilePath = $uploadDir . $fileName;

    
    // Check for upload errors
    if ($_FILES['file']['error'] !== UPLOAD_ERR_OK) {
        echo "File upload error: " . $_FILES['file']['error'];
        exit();
    }

    // Move the file to the upload directory
    if (move_uploaded_file($_FILES['file']['tmp_name'], $uploadFilePath)) {
        echo "File uploaded successfully!";
    } else {
        echo "File upload failed!";
    }
} else {
    echo "Invalid request.";
}
?>
