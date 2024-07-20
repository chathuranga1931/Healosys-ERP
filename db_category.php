<?php
require_once('Database.php');

$db = new Database();
$connection = $db->getConnection();

$result = $connection->query("SELECT CategoryID, CategoryType FROM category");

$categories = [];
while ($row = $result->fetch_assoc()) {
    $categories[] = $row;
}

$connection->close();

header('Content-Type: application/json');
echo json_encode($categories);
?>
