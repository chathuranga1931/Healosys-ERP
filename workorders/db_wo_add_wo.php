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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $data = json_decode(file_get_contents('php://input'), true);
        $logger->log('2', 'INFO', __FILE__ . ' - POST data received: ' . json_encode($data));

        // Validate required fields
        if (!isset($data['order_date'], $data['estimated_complete_date'], $data['status'], $data['source_inventory_loc_id'], $data['manufacturing_process_id'], $data['output_inventory_loc_id'])) {
            $logger->log('3', 'ERROR', __FILE__ . ' - Missing required fields.');
            throw new Exception('Missing required fields');
        }

        $db = new Database();
        $connection = $db->getConnection();
        $logger->log('4', 'INFO', __FILE__ . ' - Database connection established.');

        $sql = "
            INSERT INTO `work_orders` (`order_date`, `estimated_complete_date`, `status`, `notes`, `source_inventory_loc_id`, `manufacturing_process_id`, `output_inventory_loc_id`) 
            VALUES (?, ?, ?, ?, ?, ?, ?);
        ";
        $stmt = $connection->prepare($sql);

        if (!$stmt) {
            $logger->log('5', 'ERROR', __FILE__ . ' - Prepare statement failed: ' . $connection->error);
            throw new Exception('Failed to prepare statement');
        }

        if (!$stmt->bind_param(
            'ssssiii',
            $data['order_date'],
            $data['estimated_complete_date'],
            $data['status'],
            $data['notes'],
            $data['source_inventory_loc_id'],
            $data['manufacturing_process_id'],
            $data['output_inventory_loc_id']
        )) {
            $logger->log('6', 'ERROR', __FILE__ . ' - Bind parameters failed: ' . $stmt->error);
            throw new Exception('Failed to bind parameters');
        }

        if ($stmt->execute()) {
            $logger->log('7', 'INFO', __FILE__ . ' - Work order added successfully.');
            $response = [
                'status' => 'success',
                'message' => 'Work order added successfully',
                'id' => $stmt->insert_id // Include the ID of the new record
            ];
        } else {
            $logger->log('8', 'ERROR', __FILE__ . ' - Execute statement failed: ' . $stmt->error);
            $response = ['status' => 'error', 'message' => 'Failed to add work order'];
        }

        $stmt->close();
        $connection->close();
        $logger->log('9', 'INFO', __FILE__ . ' - Database connection closed.');

    } catch (Exception $e) {
        $logger->log('10', 'ERROR', __FILE__ . ' - Exception: ' . $e->getMessage());
        $response = ['status' => 'error', 'message' => $e->getMessage()];
    }

    echo json_encode($response);
    $logger->log('11', 'INFO', __FILE__ . ' - Response sent.');

} else {
    $logger->log('12', 'ERROR', __FILE__ . ' - Invalid request method.');
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}

$logger->log('13', 'INFO', __FILE__ . ' - Request ended.');

?>
