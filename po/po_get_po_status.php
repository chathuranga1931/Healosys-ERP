<?php

session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit;
}

// Set the content type to application/json
header('Content-Type: application/json');

// Define the array with status data
$statusData = [
    '0' => 'Open',
    '1' => 'Suspended',
    '3' => 'Pending',
    '4' => 'Delivered',
    '5' => 'Completed',
    '6' => 'Canceled'
];

// Echo the JSON-encoded data
echo json_encode($statusData);
?>