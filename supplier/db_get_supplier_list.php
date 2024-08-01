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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    $startIdx = isset($data['start_idx']) ? (int)$data['start_idx'] : 0;
    $noOfSuppliers = isset($data['no_of_suppliers']) ? (int)$data['no_of_suppliers'] : 10;

    $logger->log('2', 'INFO', __FILE__ . ' - POST data received: start_idx: ' . $startIdx . ', no_of_suppliers: ' . $noOfSuppliers);

    $db = new Database();
    $connection = $db->getConnection();

    $logger->log('3', 'INFO', __FILE__ . ' - Database connection established.');

    $stmt = $connection->prepare("SELECT supplier_id, supplier_name, contact_name, address_line1, address_line2, address_line3, contact_number, phone, whatsapp, email FROM suppliers LIMIT ?, ?");
    if (!$stmt) {
        $logger->log('4', 'ERROR', __FILE__ . ' - Prepare statement failed: ' . $connection->error);
        $response = ['status' => 'error', 'message' => 'Failed to prepare statement'];
    } else {
        $stmt->bind_param('ii', $startIdx, $noOfSuppliers);

        if ($stmt->execute()) {
            $result = $stmt->get_result();
            $suppliers = $result->fetch_all(MYSQLI_ASSOC);
            $response = ['status' => 'success', 'data' => $suppliers];
            $logger->log('5', 'INFO', __FILE__ . ' - Suppliers retrieved successfully.');
        } else {
            $logger->log('6', 'ERROR', __FILE__ . ' - Execute statement failed: ' . $stmt->error);
            $response = ['status' => 'error', 'message' => 'Failed to retrieve suppliers'];
        }

        $stmt->close();
    }

    $connection->close();
    $logger->log('7', 'INFO', __FILE__ . ' - Database connection closed.');
} else {
    $logger->log('8', 'ERROR', __FILE__ . ' - Invalid request method.');
    $response = ['status' => 'error', 'message' => 'Invalid request method'];
}

header('Content-Type: application/json');
echo json_encode($response);

$logger->log('9', 'INFO', __FILE__ . ' - Response sent.');
?>
