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

$stmt = $connection->prepare("SELECT * FROM items");
$stmt->execute();
$result = $stmt->get_result();

$items = [];
while ($row = $result->fetch_assoc()) {
    $items[] = $row;
}

echo json_encode($items);

$stmt->close();
$connection->close();
?>