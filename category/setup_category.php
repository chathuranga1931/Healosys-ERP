<?php

require_once('../libs/Database.php');

class SetupCategory {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    public function createCategoryTable() {
        $sql = "
            CREATE TABLE IF NOT EXISTS category (
                CategoryID INT AUTO_INCREMENT PRIMARY KEY,
                CategoryType VARCHAR(100) NOT NULL
            )
        ";
        $this->db->execute($sql);
        echo "Table 'category' created successfully.<br>";
    }

    public function insertCategories() {
        $categories = ['Component', 'Product', 'Service', 'R&D'];
        foreach ($categories as $category) {
            $sql = "
                INSERT INTO category (CategoryType) VALUES ('$category')
                ON DUPLICATE KEY UPDATE CategoryType = CategoryType
            ";
            $this->db->execute($sql);
        }
        echo "Categories inserted successfully.<br>";
    }
}

?>
