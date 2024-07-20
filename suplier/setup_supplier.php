<?php

require_once('../libs/Database.php');

class SetupSupplier {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    public function createSupplierTable() {
        $sql = "
            CREATE TABLE IF NOT EXISTS suppliers (
                supplier_id INT AUTO_INCREMENT PRIMARY KEY,
                supplier_name VARCHAR(255) NOT NULL,
                contact_name VARCHAR(255) NOT NULL,
                address_line1 VARCHAR(255) NOT NULL,
                address_line2 VARCHAR(255),
                address_line3 VARCHAR(255),
                contact_number VARCHAR(50),
                phone VARCHAR(50),
                whatsapp VARCHAR(50),
                email VARCHAR(100) NOT NULL
            )
        ";
        $this->db->execute($sql);
        echo "Table 'suppliers' created successfully.<br>";
    }
    public function addSampleData() {
        // Sample data array
        $sampleData = [
            ['name' => 'Supplier 1', 'contact' => 'contact1@example.com', 'phone' => '123-456-7890'],
            ['name' => 'Supplier 2', 'contact' => 'contact2@example.com', 'phone' => '234-567-8901'],
            ['name' => 'Supplier 3', 'contact' => 'contact3@example.com', 'phone' => '345-678-9012'],
        ];
    
        // Insert each supplier into the database
        foreach ($sampleData as $supplier) {
            $query = "
                INSERT INTO suppliers (supplier_name, contact_name, contact_number) 
                VALUES ('{$supplier['name']}', '{$supplier['contact']}', '{$supplier['phone']}')
                ON DUPLICATE KEY UPDATE 
                    supplier_name = VALUES(supplier_name),
                    contact_name = VALUES(contact_name),
                    contact_number = VALUES(contact_number)
            ";
            $this->db->execute($query);
        }
    
        echo "Sample data added successfully.<br>";
    }
    

}

?>
