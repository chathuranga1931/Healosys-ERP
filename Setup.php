<?php

require_once('Database.php');

class Setup {
    private $db;
    private $version = '1.7'; // Define the version of the setup file

    public function __construct() {
        $this->db = new Database();
    }

    public function run($injectTestData = false) {
        $this->createConfigTable();
        $this->insertVersion();
        $this->createProductTable();

        if ($injectTestData) {
            $this->injectTestData();
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

    private function injectTestData() {
        include('Setup_TestData.php');
    }

    public function getCurrentVersion() {
        $sql = "SHOW TABLES LIKE 'config'";
        $result = $this->db->query($sql);

        if ($result->num_rows == 0) {
            return null; // Table does not exist
        }

        $sql = "SELECT version FROM config ORDER BY ID DESC LIMIT 1";
        $result = $this->db->fetchOne($sql);
        return $result ? $result['version'] : null;
    }

    public function getVersion() {
        return $this->version;
    }
}

?>
