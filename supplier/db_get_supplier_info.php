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

    $supplierId = isset($data['supplier_id']) ? (int)$data['supplier_id'] : null;

    if ($supplierId === null) {
        $logger->log('2', 'ERROR', __FILE__ . ' - Supplier ID not provided.');
        $response = ['status' => 'error', 'message' => 'Supplier ID not provided'];
    } else {
        $logger->log('3', 'INFO', __FILE__ . ' - Supplier ID received: ' . $supplierId);

        $db = new Database();
        $connection = $db->getConnection();

        $logger->log('4', 'INFO', __FILE__ . ' - Database connection established.');

        $stmt = $connection->prepare("SELECT supplier_id, supplier_name, contact_name, address_line1, address_line2, address_line3, contact_number, phone, whatsapp, email FROM suppliers WHERE supplier_id = ?");
        if (!$stmt) {
            $logger->log('5', 'ERROR', __FILE__ . ' - Prepare statement failed: ' . $connection->error);
            $response = ['status' => 'error', 'message' => 'Failed to prepare statement'];
        } else {
            $stmt->bind_param('i', $supplierId);

            if ($stmt->execute()) {
                $result = $stmt->get_result();
                if ($result->num_rows > 0) {
                    $supplier = $result->fetch_assoc();
                    $response = ['status' => 'success', 'data' => $supplier];
                    $logger->log('6', 'INFO', __FILE__ . ' - Supplier details retrieved successfully.');
                } else {
                    $response = ['status' => 'error', 'message' => 'Supplier not found'];
                    $logger->log('7', 'INFO', __FILE__ . ' - Supplier not found.');
                }
            } else {
                $logger->log('8', 'ERROR', __FILE__ . ' - Execute statement failed: ' . $stmt->error);
                $response = ['status' => 'error', 'message' => 'Failed to retrieve supplier details'];
            }

            $stmt->close();
        }

        $connection->close();
        $logger->log('9', 'INFO', __FILE__ . ' - Database connection closed.');
    }
} else {
    $logger->log('10', 'ERROR', __FILE__ . ' - Invalid request method.');
    $response = ['status' => 'error', 'message' => 'Invalid request method'];
}

header('Content-Type: application/json');
echo json_encode($response);

$logger->log('11', 'INFO', __FILE__ . ' - Response sent.');
?>
