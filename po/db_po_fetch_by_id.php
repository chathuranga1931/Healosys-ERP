<?php

session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit;
}

require_once('../libs/Logger.php');
require_once('../libs/Database.php');

$logger = new Logger();
$logger->log('Fetch Purchase Order', 'INFO', __FILE__);

$db = new Database();
$connection = $db->getConnection();

// Retrieve the JSON data from POST
$json_data = file_get_contents('php://input');
$data = json_decode($json_data, true);

if (isset($data['purchase_order_id'])) {
    $purchase_order_id = $data['purchase_order_id'];

    // Fetch the purchase order from purchase_orders table
    $order_stmt = $connection->prepare("SELECT purchase_order_id, supplier_id, order_date, delivery_date, status, total_amount, notes FROM purchase_orders WHERE purchase_order_id = ?");
    $order_stmt->bind_param('i', $purchase_order_id);
    $order_stmt->execute();
    $order_result = $order_stmt->get_result();
    
    if ($order_result->num_rows > 0) {
        $order = $order_result->fetch_assoc();

        // Fetch all purchase order details from purchase_order_details table
        $details_stmt = $connection->prepare("SELECT purchase_order_detail_id, purchase_order_id, item_id, quantity, price, total_amount FROM purchase_order_details WHERE purchase_order_id = ?");
        $details_stmt->bind_param('i', $purchase_order_id);
        $details_stmt->execute();
        $details_result = $details_stmt->get_result();
        
        $details = [];
        while ($row = $details_result->fetch_assoc()) {
            $details[] = $row;
        }

        $response = [
            "status" => "success",
            "order" => $order,
            "details" => $details
        ];
        echo json_encode($response);

        $logger->log("Purchase order and details fetched successfully", 'INFO', __FILE__);
    } else {
        echo json_encode(["status" => "error", "message" => "Purchase order ID not found"]);
        $logger->log("Purchase order ID not found", 'ERROR', __FILE__);
    }

    $order_stmt->close();
    $details_stmt->close();
} else {
    echo json_encode(["status" => "error", "message" => "Invalid input data"]);
    $logger->log("Invalid input data", 'ERROR', __FILE__);
}

$connection->close();
?>
