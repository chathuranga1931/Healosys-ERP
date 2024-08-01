<?php

session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit;
}

require_once('../libs/Database.php');
require_once('../libs/Logger.php');

$logger = new Logger();
$logger->log('1', 'INFO', __FILE__ . ' - Request started.');

header('Content-Type: application/json');

try {
    $data = json_decode(file_get_contents("php://input"), true);
    $logger->log('2', 'INFO', __FILE__ . ' - Received data: ' . json_encode($data));

    if (!isset($data['work_order_id'], $data['details']) || !is_array($data['details'])) {
        $logger->log('3', 'ERROR', __FILE__ . ' - Invalid input data.');
        throw new Exception("Invalid input");
    }

    $db = new Database();
    $connection = $db->getConnection();
    $logger->log('4', 'INFO', __FILE__ . ' - Database connection established.');

    $woId = $data['work_order_id'];
    $logger->log('5', 'INFO', __FILE__ . ' - Checking status for work_order_id: ' . $woId);

    // Check if the work order is open
    $woStatusQuery = "SELECT status FROM work_orders WHERE work_order_id = ?";
    $stmt = $connection->prepare($woStatusQuery);

    if (!$stmt) {
        $logger->log('6', 'ERROR', __FILE__ . ' - Prepare statement failed: ' . $connection->error);
        throw new Exception("Failed to prepare statement: " . $connection->error);
    }

    $stmt->bind_param('i', $woId);
    $stmt->execute();
    $result = $stmt->get_result();
    $woStatus = $result->fetch_assoc();
    $logger->log('7', 'INFO', __FILE__ . ' - Work order status: ' . $woStatus['status']);

    if ($woStatus['status'] !== 'Open') {
        $logger->log('8', 'ERROR', __FILE__ . ' - Work order is not open.');
        throw new Exception("Work order is not open");
    }

    $sql = "
        INSERT INTO work_order_details (work_order_id, item_id, quantity) 
        VALUES (?, ?, ?)
    ";
    $stmt = $connection->prepare($sql);
    if (!$stmt) {
        $logger->log('9', 'ERROR', __FILE__ . ' - Prepare statement for insertion failed: ' . $connection->error);
        throw new Exception("Failed to prepare statement: " . $connection->error);
    }

    foreach ($data['details'] as $detail) {
        if (!isset($detail['item_id'], $detail['quantity'])) {
            $logger->log('10', 'ERROR', __FILE__ . ' - Missing detail fields.');
            throw new Exception("Missing detail fields");
        }
        $logger->log('11', 'INFO', __FILE__ . ' - Inserting detail for item_id: ' . $detail['item_id']);

        $stmt->bind_param('isd', $woId, $detail['item_id'], $detail['quantity']);
        $stmt->execute();
    }

    $logger->log('12', 'INFO', __FILE__ . ' - All details inserted successfully.');
    echo json_encode(['status' => 'success', 'message' => 'Work order details added successfully']);
} catch (Exception $e) {
    $logger->log('13', 'ERROR', __FILE__ . ' - Exception occurred: ' . $e->getMessage());
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}

$logger->log('14', 'INFO', __FILE__ . ' - Request ended.');

?>
