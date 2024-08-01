<?php

session_start();

// Check if user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit;
}

require_once('../libs/Database.php');
require_once('../libs/Logger.php');

// Initialize Logger
$logger = new Logger();
$logger->log('INFO', __FILE__ . ' - Request started.');

// Set response type
header('Content-Type: application/json');

try {
    // Get and decode the JSON input
    $data = json_decode(file_get_contents("php://input"), true);
    $logger->log('DEBUG', __FILE__ . ' - Input data: ' . json_encode($data));

    // Validate input data
    if (!isset($data['work_order']) || !isset($data['work_order']['work_order_id'])) {
        $logger->log('ERROR', __FILE__ . ' - Invalid input.');
        throw new Exception("Invalid input");
    }

    // Initialize Database connection
    $db = new Database();
    $connection = $db->getConnection();
    $woId = $data['work_order']['work_order_id'];

    // Check if the work order is open
    $woStatusQuery = "SELECT status FROM work_orders WHERE work_order_id = ?";
    $logger->log('INFO', __FILE__ . ' - Checking work order status for ID: ' . $woId);
    
    if ($stmt = $connection->prepare($woStatusQuery)) {
        $stmt->bind_param('i', $woId);
        $stmt->execute();
        $result = $stmt->get_result();
        $woStatus = $result->fetch_assoc();
        $stmt->close();

        if ($woStatus['status'] !== 'Open') {
            $logger->log('ERROR', __FILE__ . ' - Work order not open: ' . $woStatus['status']);
            throw new Exception("Work order is already in process or completed");
        }

        $logger->log('INFO', __FILE__ . ' - Work order is open.');
    } else {
        $logger->log('ERROR', __FILE__ . ' - Prepare failed: ' . $connection->error);
        throw new Exception("Prepare failed: " . $connection->error);
    }

    // Update work order
    $updateWOQuery = "
        UPDATE work_orders 
        SET order_date = ?, estimated_complete_date = ?, completed_date = ?, status = ?, notes = ?, 
            source_inventory_loc_id = ?, manufacturing_process_id = ?, output_inventory_loc_id = ?
        WHERE work_order_id = ?
    ";
    $logger->log('INFO', __FILE__ . ' - Updating work order ID: ' . $woId);

    if ($stmt = $connection->prepare($updateWOQuery)) {
        $stmt->bind_param(
            'ssssssiii',
            $data['work_order']['order_date'],
            $data['work_order']['estimated_complete_date'],
            $data['work_order']['completed_date'],
            $data['work_order']['status'],
            $data['work_order']['notes'],
            $data['work_order']['source_inventory_loc_id'],
            $data['work_order']['manufacturing_process_id'],
            $data['work_order']['output_inventory_loc_id'],
            $woId
        );
        $stmt->execute();
        $stmt->close();
        $logger->log('INFO', __FILE__ . ' - Work order updated successfully.');
    } else {
        $logger->log('ERROR', __FILE__ . ' - Prepare failed: ' . $connection->error);
        throw new Exception("Prepare failed: " . $connection->error);
    }

    // Delete existing details and re-insert updated ones
    $deleteDetailsQuery = "DELETE FROM work_order_details WHERE work_order_id = ?";
    $logger->log('INFO', __FILE__ . ' - Deleting existing details for work order ID: ' . $woId);

    if ($stmt = $connection->prepare($deleteDetailsQuery)) {
        $stmt->bind_param('i', $woId);
        $stmt->execute();
        $stmt->close();
        $logger->log('INFO', __FILE__ . ' - Existing details deleted successfully.');
    } else {
        $logger->log('ERROR', __FILE__ . ' - Prepare failed: ' . $connection->error);
        throw new Exception("Prepare failed: " . $connection->error);
    }

    // Insert new details
    foreach ($data['details'] as $detail) {
        if (!isset($detail['item_id'], $detail['quantity'])) {
            $logger->log('ERROR', __FILE__ . ' - Invalid details input.');
            throw new Exception("Invalid details input");
        }

        $insertDetailQuery = "
            INSERT INTO work_order_details (work_order_id, item_id, quantity) 
            VALUES (?, ?, ?)
        ";
        $logger->log('INFO', __FILE__ . ' - Inserting detail for item ID: ' . $detail['item_id']);

        if ($stmt = $connection->prepare($insertDetailQuery)) {
            $stmt->bind_param('isd', $woId, $detail['item_id'], $detail['quantity']);
            $stmt->execute();
            $stmt->close();
            $logger->log('INFO', __FILE__ . ' - Detail inserted successfully.');
        } else {
            $logger->log('ERROR', __FILE__ . ' - Prepare failed: ' . $connection->error);
            throw new Exception("Prepare failed: " . $connection->error);
        }
    }

    echo json_encode(['status' => 'success', 'message' => 'Work order updated successfully']);
} catch (Exception $e) {
    $logger->log('ERROR', __FILE__ . ' - Exception caught: ' . $e->getMessage());
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}

$logger->log('INFO', __FILE__ . ' - Request ended.');

?>
