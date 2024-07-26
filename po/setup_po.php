<?php

require_once('../libs/Database.php');

class SetupPurchaseOrder {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    public function createPurchaseOrderTables() {
        // Create Purchase Orders Table
        $sqlPurchaseOrder = "
            CREATE TABLE IF NOT EXISTS purchase_orders (
                purchase_order_id INT AUTO_INCREMENT PRIMARY KEY,
                supplier_id INT,
                order_date DATE,
                delivery_date DATE,
                status VARCHAR(50),
                total_amount DECIMAL(10, 2),
                notes TEXT,
                FOREIGN KEY (supplier_id) REFERENCES suppliers(supplier_id)
            )
        ";
        $this->db->execute($sqlPurchaseOrder);
        echo "Table 'purchase_orders' created successfully.<br>";

        // Create Purchase Order Details Table
        $sqlPurchaseOrderDetails = "
            CREATE TABLE IF NOT EXISTS purchase_order_details (
                purchase_order_detail_id INT AUTO_INCREMENT PRIMARY KEY,
                purchase_order_id INT,
                item_id VARCHAR(15),
                quantity DECIMAL(10, 3),
                price DECIMAL(10, 2),
                total_amount DECIMAL(10, 2),
                FOREIGN KEY (purchase_order_id) REFERENCES purchase_orders(purchase_order_id),
                FOREIGN KEY (item_id) REFERENCES items(item_id)
            )
        ";
        $this->db->execute($sqlPurchaseOrderDetails);
        echo "Table 'purchase_order_details' created successfully.<br>";
    }

    public function addSamplePurchaseOrders() {
        $sampleOrders = [];
        for ($i = 1; $i <= 10; $i++) {
            $sampleOrders[] = [
                'supplier_id' => rand(1, 3), // Assuming there are 3 suppliers
                'order_date' => date('Y-m-d', strtotime("-".rand(1,30)." days")),
                'delivery_date' => date('Y-m-d', strtotime("+".rand(1,30)." days")),
                'status' => rand(0, 1) ? 'pending' : 'completed',
                'total_amount' => rand(100, 1000),
                'notes' => 'Sample note for Purchase Order ' . $i
            ];
        }

        foreach ($sampleOrders as $order) {
            $query = "
                INSERT INTO purchase_orders (supplier_id, order_date, delivery_date, status, total_amount, notes) 
                VALUES ('{$order['supplier_id']}', '{$order['order_date']}', '{$order['delivery_date']}', '{$order['status']}', '{$order['total_amount']}', '{$order['notes']}')
            ";
            $this->db->execute($query);

            $purchaseOrderId = $this->db->getLastInsertId();

            $numberOfDetails = rand(1, 5);
            for ($j = 1; $j <= $numberOfDetails; $j++) {
                $itemId = rand(1, 100);
                $quantity = rand(1, 10);
                $price = rand(10, 100) / 10;
                $totalAmount = $quantity * $price;

                $detailQuery = "
                    INSERT INTO purchase_order_details (purchase_order_id, item_id, quantity, price, total_amount) 
                    VALUES ('{$purchaseOrderId}', '{$itemId}', '{$quantity}', '{$price}', '{$totalAmount}')
                ";
                $this->db->execute($detailQuery);
            }
        }

        echo "Sample purchase orders and details added successfully.<br>";
    }
}

?>
