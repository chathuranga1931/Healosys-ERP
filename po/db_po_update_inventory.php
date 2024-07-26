<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit;
}

require_once('../libs/Logger.php');
require_once('../libs/Database.php');

$logger = new Logger();
$db = new Database();
$connection = $db->getConnection();

// Retrieve the JSON data from POST
$json_data = file_get_contents('php://input');
$data = json_decode($json_data, true);

if (isset($data['purchase_order_id'])) {
    $purchase_order_id = $data['purchase_order_id'];

    $logger->log("Processing purchase order ID: $purchase_order_id", 'INFO', __FILE__);    

    // Check the status of the purchase order
    $status_stmt = $connection->prepare("SELECT status FROM purchase_orders WHERE purchase_order_id = ?");
    $status_stmt->bind_param('i', $purchase_order_id);
    $status_stmt->execute();
    $status_result = $status_stmt->get_result();

    if ($status_result->num_rows > 0) {
        $status_row = $status_result->fetch_assoc();
        $status = $status_row['status'];

        $logger->log("Current status of purchase order ID $purchase_order_id: $status", 'INFO', __FILE__);

        if ($status !== 'Delivered') {
            echo json_encode(["status" => "error", "message" => "Order is not delivered"]);
            $logger->log("Order ID $purchase_order_id is not delivered", 'ERROR', __FILE__);
        } else {

            $logger->log("Order ID $purchase_order_id is delivered", 'INFO', __FILE__);

            // Fetch all items from the purchase_order_details table
            $details_stmt = $connection->prepare("SELECT item_id, quantity FROM purchase_order_details WHERE purchase_order_id = ?");
            $details_stmt->bind_param('i', $purchase_order_id);
            $details_stmt->execute();
            $details_result = $details_stmt->get_result();

            $logger->log("Getting item details", 'INFO', __FILE__);

            $items_list = [];
            while ($details_row = $details_result->fetch_assoc()) {
                $item_id = $details_row['item_id'];
                $quantity = $details_row['quantity'];
                $items_list[] = ['item_id' => $item_id, 'quantity' => $quantity];

                $logger->log("Updated item ID $item_id with quantity $quantity", 'INFO', __FILE__);

                // Update the quantities in the items table
                $update_stmt = $connection->prepare("UPDATE items SET stock_quantity = stock_quantity + ? WHERE item_code = ?");
                $update_stmt->bind_param('ds', $quantity, $item_id);
                $update_stmt->execute();
                $update_stmt->close();

                $logger->log("Updated item ID $item_id with quantity $quantity", 'INFO', __FILE__);
            }

            $details_stmt->close();

            $logger->log("All items updated for purchase order ID $purchase_order_id. Items list: " . json_encode($items_list), 'INFO', __FILE__);

            // Update the purchase order status to "Completed"
            $update_status_stmt = $connection->prepare("UPDATE purchase_orders SET status = 'Completed' WHERE purchase_order_id = ?");
            $update_status_stmt->bind_param('i', $purchase_order_id);
            $update_status_stmt->execute();
            $update_status_stmt->close();

            echo json_encode(["status" => "success", "message" => "Order completed and items updated"]);
            $logger->log("Order ID $purchase_order_id marked as completed", 'INFO', __FILE__);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "Invalid purchase order ID"]);
        $logger->log("Invalid purchase order ID: $purchase_order_id", 'ERROR', __FILE__);
    }

    $status_stmt->close();
} else {
    echo json_encode(["status" => "error", "message" => "Invalid input data"]);
    $logger->log("Invalid input data received", 'ERROR', __FILE__);
}

$connection->close();
?>
