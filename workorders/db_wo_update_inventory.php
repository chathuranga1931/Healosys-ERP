<?php

require_once('../libs/Database.php');

header('Content-Type: application/json');

try {
    $data = json_decode(file_get_contents("php://input"), true);

    if (!isset($data['work_order_id'])) {
        throw new Exception("Invalid input: Missing work_order_id");
    }

    $db = new Database();
    $woId = $data['work_order_id'];

    // Check if the work order is Delivered
    $woStatusQuery = "SELECT status FROM work_orders WHERE work_order_id = :work_order_id";
    $woStatus = $db->fetchOne($woStatusQuery, [':work_order_id' => $woId]);

    if (!$woStatus || $woStatus['status'] !== 'Delivered') {
        throw new Exception("Work order is either not found or not in Delivered status");
    }

    // Retrieve the work order details
    $detailsQuery = "SELECT item_id, quantity FROM work_order_details WHERE work_order_id = :work_order_id";
    $workOrderDetails = $db->fetchAll($detailsQuery, [':work_order_id' => $woId]);

    if (!$workOrderDetails) {
        throw new Exception("No work order details found");
    }

    // Update the inventory for each item
    foreach ($workOrderDetails as $detail) {
        $itemId = $detail['item_id'];
        $quantity = $detail['quantity'];

        $updateInventoryQuery = "
            UPDATE items 
            SET quantity = quantity + :quantity
            WHERE item_id = :item_id
        ";

        $db->execute($updateInventoryQuery, [
            ':quantity' => $quantity,
            ':item_id' => $itemId
        ]);
    }

    // Mark the work order as completed
    $completeWOQuery = "
        UPDATE work_orders 
        SET status = 'Completed', completed_date = NOW() 
        WHERE work_order_id = :work_order_id
    ";

    $db->execute($completeWOQuery, [':work_order_id' => $woId]);

    echo json_encode(['status' => 'success', 'message' => 'Work order completed and inventory updated successfully']);
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}

?>
