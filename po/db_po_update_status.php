<?php

session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit;
}

require_once('../libs/Logger.php');
require_once('../libs/Database.php');

$logger = new Logger();
$logger->log('Update Purchase Order Status', 'INFO', __FILE__);

$db = new Database();
$connection = $db->getConnection();

// Retrieve the JSON data from POST
$json_data = file_get_contents('php://input');
$data = json_decode($json_data, true);

if (isset($data['purchase_order_id']) && isset($data['status'])) {
    $purchase_order_id = $data['purchase_order_id'];
    $new_status = $data['status'];

    // Update the status of the purchase order in the purchase_orders table
    $update_stmt = $connection->prepare("UPDATE purchase_orders SET status = ? WHERE purchase_order_id = ?");
    $update_stmt->bind_param('si', $new_status, $purchase_order_id);
    $update_stmt->execute();

    if ($update_stmt->affected_rows > 0) {
        echo json_encode(["status" => "success", "message" => "Purchase order status updated successfully"]);
        $logger->log("Purchase order status updated successfully for ID: $purchase_order_id", 'INFO', __FILE__);
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to update purchase order status or no changes made"]);
        $logger->log("Failed to update purchase order status for ID: $purchase_order_id", 'ERROR', __FILE__);
    }

    $update_stmt->close();
} else {
    echo json_encode(["status" => "error", "message" => "Invalid input data"]);
    $logger->log("Invalid input data", 'ERROR', __FILE__);
}

$connection->close();
?>
