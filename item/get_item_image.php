<?php

session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit;
}

require_once ('../libs/Logger.php');

$logger = new Logger();
$logger->log('1', 'INFO', __FILE__);

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if the 'image_id' POST parameter is set
    if (isset($_POST['image_id'])) {
        $imageId = $_POST['image_id'];

        // Define the path to the image directory
        $imageDirectory = '../data/images_item/';

        // Supported image extensions
        $supportedExtensions = ['jpg', 'png', 'webp', 'svg'];

        // Initialize a flag to check if the image is found
        $imageFound = false;

        // Loop through the supported extensions to find the image file
        foreach ($supportedExtensions as $extension) {
            $imagePath = $imageDirectory . $imageId . '.' . $extension;
            if (file_exists($imagePath)) {
                // Set the appropriate content-type header
                switch ($extension) {
                    case 'jpg':
                        header('Content-Type: image/jpeg');
                        break;
                    case 'png':
                        header('Content-Type: image/png');
                        break;
                    case 'webp':
                        header('Content-Type: image/webp');
                        break;
                    case 'svg':
                        header('Content-Type: image/svg+xml');
                        break;
                }

                // Read the image file and output its contents
                readfile($imagePath);
                $imageFound = true;
                break;
            }
        }

        // If the image file doesn't exist, return a 404 response
        if (!$imageFound) {
            header('HTTP/1.0 404 Not Found');
            echo 'Image not found';
        }
    } else {
        // If 'image_id' parameter is not set, return a 400 response
        header('HTTP/1.0 400 Bad Request');
        echo 'Image ID not specified';
    }
} else {
    // If the request method is not POST, return a 405 response
    header('HTTP/1.0 405 Method Not Allowed');
    echo 'Method not allowed';
}
?>
