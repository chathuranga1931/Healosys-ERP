<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    echo json_encode(['status' => 'error', 'message' => 'You are not logged in.']);
    exit;
}

$targetDir = "../data/images_item/";

// Ensure the target directory exists
if (!is_dir($targetDir)) {
    mkdir($targetDir, 0755, true);
}

$response = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_FILES["file"]) && $_FILES["file"]["error"] == 0) {
        $fileName = basename($_FILES["file"]["name"]);
        $targetFilePath = $targetDir . $fileName;
        $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);

        // Define allowed file types
        $allowedTypes = array("jpg", "jpeg", "png", "gif");

        // Check if the file type is allowed
        if (in_array(strtolower($fileType), $allowedTypes)) {
            // Attempt to move the uploaded file to the target directory
            if (move_uploaded_file($_FILES["file"]["tmp_name"], $targetFilePath)) {
                $response['status'] = 'success';
                $response['message'] = 'File uploaded successfully.';
                $response['file_path'] = $targetFilePath;
            } else {
                $response['status'] = 'error';
                $response['message'] = 'There was an error uploading your file.';
            }
        } else {
            $response['status'] = 'error';
            $response['message'] = 'Only JPG, JPEG, PNG, and GIF files are allowed.';
        }
    } else {
        $response['status'] = 'error';
        $response['message'] = 'Error: ' . $_FILES["file"]["error"];
    }
} else {
    $response['status'] = 'error';
    $response['message'] = 'Invalid request method.';
}

// Return response as JSON
echo json_encode($response);
?>
