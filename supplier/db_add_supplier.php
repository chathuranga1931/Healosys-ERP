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

    $supplierName = $data['supplier_name'];
    $contactName = $data['contact_name'];
    $addressLine1 = $data['address_line1'];
    $addressLine2 = $data['address_line2'];
    $addressLine3 = $data['address_line3'];
    $contactNumber = $data['contact_number'];
    $phone = $data['phone'];
    $whatsapp = $data['whatsapp'];
    $email = $data['email'];

    $logger->log('2', 'INFO', __FILE__ . ' - POST data received.');
    $logger->log('2', 'INFO', __FILE__ . ' - POST data received: Supplier Name: ' . $supplierName . ', Contact Name: ' . $contactName . ', Address Line 1: ' . $addressLine1 . ', Address Line 2: ' . $addressLine2 . ', Address Line 3: ' . $addressLine3 . ', Contact Number: ' . $contactNumber . ', Phone: ' . $phone . ', WhatsApp: ' . $whatsapp . ', Email: ' . $email);

    if (!empty($supplierName) && !empty($contactName)) {
        $db = new Database();
        $connection = $db->getConnection();

        $logger->log('3', 'INFO', __FILE__ . ' - Database connection established.');

        // Check if the supplier name already exists
        $checkStmt = $connection->prepare("SELECT COUNT(*) FROM suppliers WHERE supplier_name = ?");
        if ($checkStmt) {
            $checkStmt->bind_param('s', $supplierName);
            $checkStmt->execute();
            $checkStmt->bind_result($count);
            $checkStmt->fetch();
            $checkStmt->close();

            if ($count > 0) {
                $logger->log('4', 'ERROR', __FILE__ . ' - Supplier name already exists.');
                $response = ['status' => 'error', 'message' => 'Supplier name already exists'];
            } else {
                // Prepare and execute the query to insert a new supplier
                $stmt = $connection->prepare("INSERT INTO suppliers (supplier_name, contact_name, address_line1, address_line2, address_line3, contact_number, phone, whatsapp, email) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
                if (!$stmt) {
                    $logger->log('5', 'ERROR', __FILE__ . ' - Prepare statement failed: ' . $connection->error);
                    $response = ['status' => 'error', 'message' => 'Failed to prepare statement'];
                } else {
                    $stmt->bind_param('sssssssss', $supplierName, $contactName, $addressLine1, $addressLine2, $addressLine3, $contactNumber, $phone, $whatsapp, $email);
                    
                    if ($stmt->execute()) {
                        $logger->log('6', 'INFO', __FILE__ . ' - Supplier added successfully.');
                        $response = ['status' => 'success', 'message' => 'Supplier added successfully'];
                    } else {
                        $logger->log('7', 'ERROR', __FILE__ . ' - Execute statement failed: ' . $stmt->error);
                        $response = ['status' => 'error', 'message' => 'Failed to add supplier'];
                    }
                }

                $stmt->close();
            }
        } else {
            $logger->log('8', 'ERROR', __FILE__ . ' - Prepare statement for checking existing supplier failed: ' . $connection->error);
            $response = ['status' => 'error', 'message' => 'Failed to check existing supplier'];
        }

        $connection->close();
        $logger->log('9', 'INFO', __FILE__ . ' - Database connection closed.');
    } else {
        $logger->log('10', 'ERROR', __FILE__ . ' - Required fields are missing.');
        $response = ['status' => 'error', 'message' => 'Required fields are missing'];
    }
} else {
    $logger->log('11', 'ERROR', __FILE__ . ' - Invalid request method.');
    $response = ['status' => 'error', 'message' => 'Invalid request method'];
}

header('Content-Type: application/json');
echo json_encode($response);

$logger->log('12', 'INFO', __FILE__ . ' - Response sent.');
?>
