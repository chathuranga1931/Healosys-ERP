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
    $supplierName = $data['supplier_name'] ?? null;
    $contactName = $data['contact_name'] ?? null;
    $addressLine1 = $data['address_line1'] ?? null;
    $addressLine2 = $data['address_line2'] ?? null;
    $addressLine3 = $data['address_line3'] ?? null;
    $contactNumber = $data['contact_number'] ?? null;
    $phone = $data['phone'] ?? null;
    $whatsapp = $data['whatsapp'] ?? null;
    $email = $data['email'] ?? null;

    if ($supplierId === null || $supplierName === null || $contactName === null) {
        $logger->log('2', 'ERROR', __FILE__ . ' - Required fields are missing.');
        $response = ['status' => 'error', 'message' => 'Required fields are missing'];
    } else {
        $logger->log('3', 'INFO', __FILE__ . ' - Supplier data received for update.');

        $db = new Database();
        $connection = $db->getConnection();

        $logger->log('4', 'INFO', __FILE__ . ' - Database connection established.');

        $stmt = $connection->prepare("UPDATE suppliers SET supplier_name = ?, contact_name = ?, address_line1 = ?, address_line2 = ?, address_line3 = ?, contact_number = ?, phone = ?, whatsapp = ?, email = ? WHERE supplier_id = ?");
        
        $logger->log('5', 'INFO', __FILE__ . ' - Preparing to execute update query.');

        if ($stmt) {
            $stmt->bind_param('sssssssssi', $supplierName, $contactName, $addressLine1, $addressLine2, $addressLine3, $contactNumber, $phone, $whatsapp, $email, $supplierId);
            $updateSuccess = $stmt->execute();
            $stmt->close();
        } else {
            $logger->log('6', 'ERROR', __FILE__ . ' - Prepare statement failed: ' . $connection->error);
            $updateSuccess = false;
        }

        if ($updateSuccess) {
            $logger->log('7', 'INFO', __FILE__ . ' - Supplier updated successfully.');
            $response = ['status' => 'success', 'message' => 'Supplier updated successfully'];
        } else {
            $logger->log('8', 'ERROR', __FILE__ . ' - Failed to update supplier.');
            $response = ['status' => 'error', 'message' => 'Failed to update supplier'];
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
