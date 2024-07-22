<?php
require_once('../libs/Database.php');

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['category_id'])) {
    $category_id = intval($_GET['category_id']);

    $db = new Database();
    $connection = $db->getConnection();

    $stmt = $connection->prepare("SELECT category_name FROM category WHERE category_id = ?");
    $stmt->bind_param("i", $category_id);
    $stmt->execute();
    $stmt->bind_result($category_name);
    $stmt->fetch();

    $stmt->close();
    $connection->close();

    if ($category_name) {
        echo json_encode(['status' => 'success', 'category_name' => $category_name]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Category not found']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
}
?>
