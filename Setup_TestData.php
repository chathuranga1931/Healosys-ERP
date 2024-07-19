<?php

require_once('Database.php');

class SetupTestData {
    private $db;

    public function __construct() {
        $this->db = new Database();
        $this->insertTestData();
    }

    private function insertTestData() {
        $products = [
            ['ProductID' => 'P001', 'ProductName' => 'Product 1', 'ProductCategory' => 'Category A'],
            ['ProductID' => 'P002', 'ProductName' => 'Product 2', 'ProductCategory' => 'Category B'],
            ['ProductID' => 'P003', 'ProductName' => 'Product 3', 'ProductCategory' => 'Category C']
        ];

        foreach ($products as $product) {
            $sql = "
                INSERT INTO products (ProductID, ProductName, ProductCategory)
                VALUES ('{$product['ProductID']}', '{$product['ProductName']}', '{$product['ProductCategory']}')
                ON DUPLICATE KEY UPDATE 
                    ProductName = VALUES(ProductName), 
                    ProductCategory = VALUES(ProductCategory)
            ";

            $this->db->execute($sql);
        }

        echo "Sample products inserted successfully.<br>";
    }
}

new SetupTestData();

?>
