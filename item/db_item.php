
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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve the JSON input
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);

    // Extract the data from the JSON payload
    $item_code = $data['item_code'];
    $name = $data['name'];
    $category_id = $data['category_id'];
    $description = $data['description'] ?? '';
    $price = $data['price'] ?? 0.00;
    $cost = $data['cost'] ?? 0.00;
    $reorder_level = $data['reorder_level'] ?? 0;
    $supplier_id = $data['supplier_id'];
    $add_or_update = $data['add_or_update'];

    $db = new Database();
    $connection = $db->getConnection();

    // Check if the item code already exists
    $stmt = $connection->prepare("SELECT COUNT(*) FROM items WHERE item_code = ?");
    $stmt->bind_param('s', $item_code);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();

    $logger->log("Item code =" . $item_code . " Name =" . $name ,'INFO', __FILE__);
    $logger->log('Count =' . $count,'INFO', __FILE__);
    if($add_or_update == "update") {
        if ($count == 0) {
            echo json_encode(["error" => "Item code does not exist."]);
            $logger->log("Item code does not exist.",'INFO', __FILE__);
        } else {
            // Update the existing item
            $stmt = $connection->prepare("
                UPDATE items
                SET name = ?, description = ?, category_id = ?, price = ?, cost = ?, reorder_level = ?, supplier_id = ?
                WHERE item_code = ?
            ");
            $stmt->bind_param('ssiiddis', $name, $description, $category_id, $price, $cost, $reorder_level, $supplier_id, $item_code);

            if ($stmt->execute()) {
                echo json_encode(["success" => "Item updated successfully."]);
                $logger->log('Item updated successfully.','INFO', __FILE__);
            } else {
                echo json_encode(["error" => "Error: " . $stmt->error]);
                $logger->log("Error: " . $stmt->error,'INFO', __FILE__);
            }

            $stmt->close();
        }
    }
    else if($add_or_update == "add") {

        if ($count > 0) {
            echo json_encode(["error" => "Item code already exists."]);
            $logger->log('Item code already exists','INFO', __FILE__);
        } else {
            // Insert the new item
            $stmt = $connection->prepare("
                INSERT INTO items (item_code, name, description, category_id, price, cost, reorder_level, supplier_id) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->bind_param('sssiiddi', $item_code, $name, $description, $category_id, $price, $cost, $reorder_level, $supplier_id);

            if ($stmt->execute()) {
                echo json_encode(["success" => "Item added successfully."]);
                $logger->log('Item added successfully','INFO', __FILE__);
            } else {
                echo json_encode(["error" => "Error: " . $stmt->error]);
                $logger->log("Error: " . $stmt->error,'INFO', __FILE__);
            }

            $stmt->close();
        }
    }

    $connection->close();
}
?>
