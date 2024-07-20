<?php
require_once('database.php');

$db = new Database();
$connection = $db->getConnection();

$result = $connection->query("SELECT supplier_id, supplier_name FROM suppliers");

$suppliers = [];
while ($row = $result->fetch_assoc()) {
    $suppliers[] = $row;
}

$connection->close();

header('Content-Type: application/json');
echo json_encode($suppliers);
?>
