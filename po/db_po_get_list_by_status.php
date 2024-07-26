<?php

session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit;
}

require_once ('../libs/Logger.php');

$logger = new Logger();
$logger->log('1', 'INFO', __FILE__);

require_once('../libs/Database.php');

$db = new Database();
$connection = $db->getConnection();

// Retrieve the JSON data from POST
$json_data = file_get_contents('php://input');
$data = json_decode($json_data, true);

if (isset($data['status']) && isset($data['start_idx']) && isset($data['numbers'])) {
    $status = $data['status'];
    $start_idx = $data['start_idx'];
    $numbers = $data['numbers'];

    // Prepare the SQL query to count the total number of purchase orders with the given status
    $count_stmt = $connection->prepare("SELECT COUNT(*) as total FROM purchase_orders WHERE status = ?");
    $count_stmt->bind_param('s', $status);
    $count_stmt->execute();
    $count_result = $count_stmt->get_result();
    $count_row = $count_result->fetch_assoc();
    $total_count = $count_row['total'];
    $count_stmt->close();

    // Prepare the SQL query to fetch the purchase orders with the given status
    $search_stmt = $connection->prepare("SELECT * FROM purchase_orders WHERE status = ? LIMIT ?, ?");
    $search_stmt->bind_param('sii', $status, $start_idx, $numbers);
    $search_stmt->execute();
    $search_result = $search_stmt->get_result();

    $purchase_orders = array();
    while ($row = $search_result->fetch_assoc()) {
        $purchase_orders[] = $row;
    }

    $search_stmt->close();

    // Respond with the purchase orders and total count
    echo json_encode([
        "status" => "success",
        "total_count" => $total_count,
        "purchase_orders" => $purchase_orders
    ]);

    $logger->log("Purchase orders retrieved successfully", 'INFO', __FILE__);
} else {
    echo json_encode(["status" => "error", "message" => "Invalid input data"]);
    $logger->log("Invalid input data", 'ERROR', __FILE__);
}

$connection->close();
?>
