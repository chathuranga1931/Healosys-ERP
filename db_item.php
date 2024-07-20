<?php
require_once('database.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $item_id = isset($_POST['item_id']) ? $_POST['item_id'] : null;
    $item_code = $_POST['item_code'];
    $name = $_POST['name'];
    $category_id = $_POST['category_id'];
    $description = $_POST['description'] ?? '';
    $price = $_POST['price'] ?? 0.00;
    $cost = $_POST['cost'] ?? 0.00;
    $reorder_level = $_POST['reorder_level'] ?? 0;
    $supplier_id = $_POST['supplier_id'];

    $db = new Database();
    $connection = $db->getConnection();

    if ($item_id) {
        // Update the item
        $stmt = $connection->prepare("
            UPDATE items 
            SET item_code = ?, name = ?, description = ?, category_id = ?, price = ?, cost = ?, reorder_level = ?, supplier_id = ?
            WHERE item_id = ?
        ");
        $stmt->bind_param('sssiiddii', $item_code, $name, $description, $category_id, $price, $cost, $reorder_level, $supplier_id, $item_id);
        
        if ($stmt->execute()) {
            echo "Item updated successfully.";
        } else {
            echo "Error: " . $stmt->error;
        }

        $stmt->close();
    } else {
        // Check if the item code already exists
        $stmt = $connection->prepare("SELECT COUNT(*) FROM items WHERE item_code = ?");
        $stmt->bind_param('s', $item_code);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();

        if ($count > 0) {
            echo "Error: Item code already exists.";
        } else {
            // Insert the new item
            $stmt = $connection->prepare("
                INSERT INTO items (item_code, name, description, category_id, price, cost, reorder_level, supplier_id) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->bind_param('sssiiddi', $item_code, $name, $description, $category_id, $price, $cost, $reorder_level, $supplier_id);

            if ($stmt->execute()) {
                echo "Item added successfully.";
            } else {
                echo "Error: " . $stmt->error;
            }

            $stmt->close();
        }
    }

    $connection->close();
}
?>
