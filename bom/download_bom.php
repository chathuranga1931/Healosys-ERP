<?php

session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit;
}

require_once ('../libs/Logger.php');

$logger = new Logger();
$logger->log('download_bom.php', 'INFO', __FILE__);

// Get the filename from the POST data
$filename = basename($_POST['filename']);
$logger->log('filename = '.$filename.' ', 'INFO', __FILE__);


// Construct the full path to the JSON file
$filepath = '../data/bom/' . $filename . '.json';
$logger->log('filepath = '.$filepath.' ', 'INFO', __FILE__);

// Check if the file exists and is readable
if (!file_exists($filepath) || !is_readable($filepath)) {
    http_response_code(404); // Not Found
    echo json_encode(array("error" => "File not found or not readable"));
    $logger->log('File not found or not readable', 'INFO', __FILE__);
    exit();
}

// Read the content of the JSON file
$jsonContent = file_get_contents($filepath);

// Check if reading the file was successful
if ($jsonContent === false) {
    http_response_code(500); // Internal Server Error
    echo json_encode(array("error" => "Failed to read the file"));
    $logger->log('Failed to read the file', 'INFO', __FILE__);
    exit();
}

// Set the response content type to application/json
header('Content-Type: application/json');
$logger->log('Downloaded... ', 'INFO', __FILE__);

// Send the JSON content as the response
echo $jsonContent;
?>
