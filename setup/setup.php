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
require_once('../category/setup_category.php');
require_once('../supplier/setup_supplier.php');
require_once('../item/setup_item.php');
require_once('../po/setup_po.php');
require_once('../workorders/setup_wo.php');

class Setup {
    private $db;
    private $setup_category;
    private $setup_supplier;
    private $setup_items;
    private $setup_po;
    private $setup_wo;

    private $version = '2.079'; // Define the version of the setup file

    public function __construct() {
        $this->db = new Database();
        $this->setup_category = new SetupCategory();
        $this->setup_supplier = new SetupSupplier();
        $this->setup_items = new SetupItem();
        $this->setup_po = new SetupPurchaseOrder();
        $this->setup_wo = new SetupWorkOrder();
    }

    public function run($injectTestData = false) {
        $this->createConfigTable();
        $this->insertVersion();
        $this->createUsersTable();
        $this->insertDefaultAdmin();
        $this->setup_category->createCategoryTable(); // Call the category setup
        $this->setup_supplier->createSupplierTable(); // Call the supplier setup
        $this->setup_items->createItemTable(); // Call the item setup
        $this->setup_po->createPurchaseOrderTables(); // Call the item setup
        $this->setup_wo->createWorkOrderTables(); // Call the item setup

        if ($injectTestData) {
            $this->injectTestData();
            $this->setup_category->insertCategories(); // insert default catogories
            $this->setup_supplier->addSampleData();
            $this->setup_items->addSampleItems();     
            $this->setup_po->addSamplePurchaseOrders();          
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

    // private function createProductTable() {
    //     $sql = "
    //         CREATE TABLE IF NOT EXISTS products (
    //             ID INT AUTO_INCREMENT PRIMARY KEY,
    //             ProductID VARCHAR(15) NOT NULL UNIQUE,
    //             ProductName VARCHAR(100) NOT NULL,
    //             ProductCategory VARCHAR(100) NOT NULL
    //         )
    //     ";
    //     $this->db->execute($sql);
    //     echo "Table 'products' created successfully.<br>";
    // }

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

$setup = new Setup();
$currentVersion = $setup->getCurrentVersion();

echo "Installed version " . $currentVersion . "...<br>";
echo "Current version " . $setup->getVersion() . "...<br>";

$injectTestData = isset($_GET['injectTestData']) ? filter_var($_GET['injectTestData'], FILTER_VALIDATE_BOOLEAN) : false;

if ($currentVersion === null) {
    $_SESSION['configured'] = true;
    echo "No version found. Running setup...<br>";
    $setup->run($injectTestData);
    //header('Location: ../index.php');
} elseif ($currentVersion < $setup->getVersion()) {
    $_SESSION['configured'] = true;
    echo "Old version detected. Running setup...<br>";
    $setup->run($injectTestData);
    //header('Location: ../index.php');
} else {
    $_SESSION['configured'] = true;
    echo "Already Configured.<br>";
    //header('Location: ../index.php');
}

?>
