<?php

require_once('database.php');
require_once('setup_category.php');
require_once('setup_supplier.php');
require_once('setup_item.php');

class Setup {
    private $db;
    private $setup_category;
    private $setup_supplier;
    private $setup_items;

    private $version = '2.074'; // Define the version of the setup file

    public function __construct() {
        $this->db = new Database();
        $this->setup_category = new SetupCategory();
        $this->setup_supplier = new SetupSupplier();
        $this->setup_items = new SetupItem();
    }

    public function run($injectTestData = false) {
        $this->createConfigTable();
        $this->insertVersion();
        $this->createProductTable();
        $this->createUsersTable();
        $this->insertDefaultAdmin();
        $this->setup_category->createCategoryTable(); // Call the category setup
        $this->setup_supplier->createSupplierTable(); // Call the supplier setup
        $this->setup_items->createItemTable(); // Call the item setup

        if ($injectTestData) {
            $this->injectTestData();
            $this->setup_category->insertCategories(); // insert default catogories
            $this->setup_supplier->addSampleData();
            $this->setup_items->addSampleItems();            
        }
    }

    private function createConfigTable() {
        $sql = "
            CREATE TABLE IF NOT EXISTS config (
                ID INT AUTO_INCREMENT PRIMARY KEY,
                version VARCHAR(10) NOT NULL
            )
        ";
        $this->db->execute($sql);
        echo "Table 'config' created successfully.<br>";
    }

    private function insertVersion() {
        $sql = "
            INSERT INTO config (version) VALUES ('$this->version')
            ON DUPLICATE KEY UPDATE version = '$this->version'
        ";
        $this->db->execute($sql);
        echo "Version '$this->version' inserted into 'config' table.<br>";
    }

    private function createProductTable() {
        $sql = "
            CREATE TABLE IF NOT EXISTS products (
                ID INT AUTO_INCREMENT PRIMARY KEY,
                ProductID VARCHAR(15) NOT NULL UNIQUE,
                ProductName VARCHAR(100) NOT NULL,
                ProductCategory VARCHAR(100) NOT NULL
            )
        ";
        $this->db->execute($sql);
        echo "Table 'products' created successfully.<br>";
    }

    private function createUsersTable() {
        $sql = "
            CREATE TABLE IF NOT EXISTS users (
                ID INT AUTO_INCREMENT PRIMARY KEY,
                username VARCHAR(50) NOT NULL UNIQUE,
                password VARCHAR(255) NOT NULL
            )
        ";
        $this->db->execute($sql);
        echo "Table 'users' created successfully.<br>";
    }

    private function insertDefaultAdmin() {
        $username = 'admin';
        $password = password_hash('password', PASSWORD_DEFAULT);
        $sql = "
            INSERT INTO users (username, password) VALUES ('$username', '$password')
            ON DUPLICATE KEY UPDATE username = username
        ";
        $this->db->execute($sql);
        echo "Default admin user inserted successfully.<br>";
    }

    private function injectTestData() {
        include('setup_testdata.php');
    }

    public function getCurrentVersion() {
        $sql = "SELECT version FROM config ORDER BY ID DESC LIMIT 1";
        $result = $this->db->fetchOne($sql);
        return $result ? $result['version'] : null;
    }

    public function getVersion() {
        return $this->version;
    }
}

// Run the setup
new Setup();

?>
