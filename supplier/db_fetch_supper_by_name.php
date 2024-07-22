<?php
require_once('../libs/Database.php');

if (isset($_GET['supplier_name'])) {
    $supplierName = $_GET['supplier_name'];

    $db = new Database();
    $connection = $db->getConnection();

    // Prepare and execute the query to get supplier details by supplier name
    $stmt = $connection->prepare("SELECT * FROM suppliers WHERE supplier_name = ?");
    $stmt->bind_param('s', $supplierName);
    $stmt->execute();
    $result = $stmt->get_result();

    $supplierDetails = [];
    while ($row = $result->fetch_assoc()) {
        $supplierDetails[] = $row;
    }

    $stmt->close();
    $connection->close();

    header('Content-Type: application/json');
    echo json_encode($supplierDetails);
} else {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Supplier name not provided']);
}
?>
