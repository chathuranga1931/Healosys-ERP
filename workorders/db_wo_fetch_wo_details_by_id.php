<?php

session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit;
}

require_once('../libs/Database.php');
require_once('../libs/Logger.php');

$logger = new Logger();
$logger->log('INFO', __FILE__ . ' - Request started.');

header('Content-Type: application/json');

try {
    $data = json_decode(file_get_contents("php://input"), true);

    if (!isset($data['work_order_id'])) {
        throw new Exception("Missing work_order_id");
    }

    $db = new Database();
    $woId = $data['work_order_id'];

    // Assuming Database class has a method 'getConnection' that returns the mysqli object
    $connection = $db->getConnection();
    $query = "SELECT * FROM work_order_details WHERE work_order_id = ?";

    if ($stmt = $connection->prepare($query)) {
        // 'i' indicates the type is integer
        $stmt->bind_param('i', $woId);

        if ($stmt->execute()) {
            $result = $stmt->get_result();
            $details = $result->fetch_all(MYSQLI_ASSOC);

            echo json_encode(['status' => 'success', 'data' => $details]);
        } else {
            throw new Exception("Execute failed: " . $stmt->error);
        }

        $stmt->close();
    } else {
        throw new Exception("Prepare failed: " . $connection->error);
    }

} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}

?>
