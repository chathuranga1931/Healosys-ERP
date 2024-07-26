<?php

session_start();

// Check if the user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit;
}

// Include the Logger class file
require_once('../libs/Logger.php');

// Initialize the Logger
$logger = new Logger();
$logger->log('Get device list', 'INFO', __FILE__);

// Set the content type to application/json
header('Content-Type: application/json');

// Define the array with status data
$statusData = [
    '0' => 'Open',
    '1' => 'Suspended',
    '3' => 'Pending',
    '4' => 'Delivered',
    '5' => 'Completed',
    '6' => 'Canceled',
    '7' => 'Delivered_ModifyRquired'
];

$logger->log('Status data: ' . json_encode($statusData), 'INFO', __FILE__);

// Define $order variable or fetch it as needed
$order = []; // Replace with actual data fetching logic

// Prepare the response
$response = [
    "status" => "success",
    "order" => $order,
    "details" => $statusData
];

// Encode the response as JSON and handle potential errors
$jsonResponse = json_encode($response);
if ($jsonResponse === false) {
    $logger->log('JSON encode error: ' . json_last_error_msg(), 'ERROR', __FILE__);
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "Internal Server Error"]);
} else {
    echo $jsonResponse;
    $logger->log('Response: ' . $jsonResponse, 'INFO', __FILE__);
}

?>
