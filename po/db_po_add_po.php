<?php

session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit;
}

require_once('../libs/Logger.php');
require_once('../libs/Database.php');

$logger = new Logger();
$logger->log('Add Purchase Order', 'INFO', __FILE__);

$db = new Database();
$connection = $db->getConnection();

// Retrieve the JSON data from POST
$json_data = file_get_contents('php://input');
$data = json_decode($json_data, true);

if (isset($data['supplier_id'], $data['order_date'], $data['delivery_date'], $data['status'], $data['total_amount'], $data['notes'])) {
    $supplier_id = $data['supplier_id'];
    $order_date = $data['order_date'];
    $delivery_date = $data['delivery_date'];
    $status = $data['status'];
    $total_amount = $data['total_amount'];
    $notes = $data['notes'];

    // Check if there are more than 5 purchase orders with the same status
    $check_stmt = $connection->prepare("SELECT COUNT(*) as count FROM purchase_orders WHERE status = ?");
    $check_stmt->bind_param('s', $status);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    $count = $check_result->fetch_assoc()['count'];

    if ($count >= 5) {
        // If count is higher than 5, respond with an error
        echo json_encode(["status" => "error", "message" => "There are already $count elements in the system with the status '$status'"]);
        $logger->log("Error: Too many purchase orders with the same status", 'ERROR', __FILE__);
    } else {
        // Insert new purchase order
        $insert_stmt = $connection->prepare("INSERT INTO purchase_orders (supplier_id, order_date, delivery_date, status, total_amount, notes) VALUES (?, ?, ?, ?, ?, ?)");
        $insert_stmt->bind_param('isssis', $supplier_id, $order_date, $delivery_date, $status, $total_amount, $notes);

        if ($insert_stmt->execute()) {
            // Get the ID of the newly inserted purchase order
            $new_purchase_order_id = $connection->insert_id;

            echo json_encode([
                "status" => "success",
                "message" => "Purchase order added successfully",
                "purchase_order_id" => $new_purchase_order_id
            ]);

            $logger->log("Purchase order added successfully, ID: $new_purchase_order_id", 'INFO', __FILE__);
        } else {
            echo json_encode(["status" => "error", "message" => "Failed to add purchase order"]);
            $logger->log("Error: Failed to add purchase order", 'ERROR', __FILE__);
        }

        $insert_stmt->close();
    }

    $check_stmt->close();
} else {
    echo json_encode(["status" => "error", "message" => "Invalid input data"]);
    $logger->log("Invalid input data", 'ERROR', __FILE__);
}

$connection->close();
?>
