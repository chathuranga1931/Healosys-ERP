<?php

require_once('../libs/Database.php');

header('Content-Type: application/json');

try {
    $data = json_decode(file_get_contents("php://input"), true);

    // Sanitize and set default values for offset and limit
    $offset = isset($data['offset']) ? (int)$data['offset'] : 0;
    $limit = isset($data['limit']) ? (int)$data['limit'] : 10;

    // Ensure limit and offset are non-negative
    if ($limit < 0) $limit = 10;
    if ($offset < 0) $offset = 0;

    $db = new Database();

    // Concatenate limit and offset directly into the query string
    $query = "SELECT * FROM work_orders LIMIT $limit OFFSET $offset";
    $workOrders = $db->fetchAll($query);

    echo json_encode(['status' => 'success', 'data' => $workOrders]);
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}

?>
