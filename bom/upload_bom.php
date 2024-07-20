<?php

session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit;
}

require_once ('../libs/Logger.php');

$logger = new Logger();
$logger->log('livesearch_item_products', 'INFO', __FILE__);


// Check if a file was uploaded
if (isset($_FILES['file'])) {
    // Define the target directory
    $target_dir = "../data/bom/";

    // Create the target directory if it doesn't exist
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
        $logger->log('Folder created', 'INFO', __FILE__);
    }

    // Define the target file path
    $target_file = $target_dir . basename($_FILES["file"]["name"]);

    // Move the uploaded file to the target directory
    if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {
        echo "The file ". htmlspecialchars(basename($_FILES["file"]["name"])). " has been uploaded.";
        $logger->log("The file ". htmlspecialchars(basename($_FILES["file"]["name"])). " has been uploaded.", 'INFO', __FILE__);
    } else {
        echo "Sorry, there was an error uploading your file.";
        $logger->log('Sorry, there was an error uploading your file.', 'INFO', __FILE__);
    }
} else {
    echo "No file was uploaded.";
    $logger->log("No file was uploaded.", 'INFO', __FILE__);
}
?>