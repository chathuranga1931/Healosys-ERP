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

if (isset($_GET['name'])) {
    $name = $_GET['name'];

    $db = new Database();
    $connection = $db->getConnection();

    $stmt = $connection->prepare("SELECT * FROM items WHERE name = ?");
    $stmt->bind_param('s', $name);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $item = $result->fetch_assoc();
        echo json_encode($item);
    } else {
        echo json_encode([]);
    }

    $stmt->close();
    $connection->close();
}
?>
