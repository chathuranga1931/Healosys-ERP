<?php

require_once('../libs/Database.php');

class SetupItem {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    public function createItemTable() {
        $sql = "
            CREATE TABLE IF NOT EXISTS items (
                item_id INT AUTO_INCREMENT PRIMARY KEY,
                item_code VARCHAR(15) NOT NULL,
                name VARCHAR(255) NOT NULL,
                description TEXT,
                category_id INT,
                price DECIMAL(10, 2),
                cost DECIMAL(10, 2),
                stock_quantity INT,
                reorder_level INT,
                supplier_id INT,
                FOREIGN KEY (category_id) REFERENCES category(CategoryID),
                FOREIGN KEY (supplier_id) REFERENCES suppliers(supplier_id)
            )
        ";
        $this->db->execute($sql);
        echo "Table 'items' created successfully.<br>";
    }

    public function addSampleItems() {
        $sampleItems = [];
        for ($i = 1; $i <= 100; $i++) {
            $sampleItems[] = [
                'item_code' => 'ITEM' . str_pad($i, 12, '0', STR_PAD_LEFT),
                'name' => 'Sample Item ' . $i,
                'description' => 'This is a description for Sample Item ' . $i,
                'category_id' => rand(1, 4), // Assuming there are 4 categories
                'price' => rand(10, 1000) / 10,
                'cost' => rand(5, 500) / 10,
                'stock_quantity' => rand(1, 100),
                'reorder_level' => rand(1, 10),
                'supplier_id' => rand(1, 3) // Assuming there are 3 suppliers
            ];
        }

        foreach ($sampleItems as $item) {
            $query = "
                INSERT INTO items (item_code, name, description, category_id, price, cost, stock_quantity, reorder_level, supplier_id) 
                VALUES ('{$item['item_code']}', '{$item['name']}', '{$item['description']}', '{$item['category_id']}', '{$item['price']}', '{$item['cost']}', '{$item['stock_quantity']}', '{$item['reorder_level']}', '{$item['supplier_id']}')
                ON DUPLICATE KEY UPDATE 
                    name = VALUES(name),
                    description = VALUES(description),
                    category_id = VALUES(category_id),
                    price = VALUES(price),
                    cost = VALUES(cost),
                    stock_quantity = VALUES(stock_quantity),
                    reorder_level = VALUES(reorder_level),
                    supplier_id = VALUES(supplier_id)
            ";
            $this->db->execute($query);
        }

        echo "Sample items added successfully.<br>";
    }
}

?>
