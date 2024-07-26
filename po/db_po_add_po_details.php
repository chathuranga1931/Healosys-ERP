<?php

session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit;
}

require_once ('../libs/Logger.php');

$logger = new Logger();
$logger->log('1', 'INFO', __FILE__);

require_once('../libs/Database.php');

$db = new Database();
$connection = $db->getConnection();

// Retrieve the JSON data from POST
$json_data = file_get_contents('php://input');
$data = json_decode($json_data, true);

if (isset($data['purchase_order_id']) && isset($data['details']) && is_array($data['details'])) {
    $purchase_order_id = $data['purchase_order_id'];
    $details = $data['details'];

    // Check if purchase_order_id exists in the purchase_orders table
    // $check_stmt = $connection->prepare("SELECT purchase_order_id FROM purchase_orders WHERE purchase_order_id = ? AND status = 'Open'");
    $check_stmt = $connection->prepare("SELECT purchase_order_id FROM purchase_orders WHERE purchase_order_id = ? AND status IN ('Open', 'Delivered_ModifyRquired')");
    $check_stmt->bind_param('i', $purchase_order_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows > 0) {
        
        $logger->log("Purchase order ID exists and the status is Open", 'INFO', __FILE__);
        // purchase_order_id exists, delete existing details
        $delete_stmt = $connection->prepare("DELETE FROM purchase_order_details WHERE purchase_order_id = ?");
        $delete_stmt->bind_param('i', $purchase_order_id);
        $delete_stmt->execute();
        $delete_stmt->close();
        $logger->log("Deleted, old data for the same purchase order ID", 'INFO', __FILE__);

        // Insert new details
        $stmt = $connection->prepare("INSERT INTO purchase_order_details (purchase_order_id, item_id, quantity, price, total_amount) VALUES (?, ?, ?, ?, ?)");

        foreach ($details as $detail) {
            $item_id = $detail['item_id'];
            $quantity = $detail['quantity'];
            $price = $detail['price'];
            $total_amount = $detail['total_amount'];
            
            $logger->log("Adding details: $purchase_order_id, $item_id, $quantity, $price, $total_amount", 'INFO', __FILE__);

            $stmt->bind_param('isddd', $purchase_order_id, $item_id, $quantity, $price, $total_amount);
            $stmt->execute();
        }

        $stmt->close();
        echo json_encode(["status" => "success", "message" => "Purchase order details updated successfully"]);
        $logger->log("Purchase order details updated successfully", 'INFO', __FILE__);
    } else {
        // purchase_order_id does not exist, return error
        echo json_encode(["status" => "error", "message" => "Opened or Modify Requeseted Purchase order ID not found"]);
        $logger->log("Purchase order ID not found or the status is not Open", 'ERROR', __FILE__);
    }

    $check_stmt->close();
} else {
    echo json_encode(["status" => "error", "message" => "Invalid input data"]);
    $logger->log("Invalid input data", 'ERROR', __FILE__);
}

$connection->close();
?>
