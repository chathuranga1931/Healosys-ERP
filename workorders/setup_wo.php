<?php

require_once('../libs/Database.php');

class SetupWorkOrder {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    public function createWorkOrderTables() {
        // Create Work Orders Table
        $sqlWorkOrder = "
            CREATE TABLE IF NOT EXISTS work_orders (
                work_order_id INT AUTO_INCREMENT PRIMARY KEY,
                order_date DATE,
                estimated_complete_date DATE,
                completed_date DATE,
                status VARCHAR(50),
                notes TEXT,
                source_inventory_loc_id INT,
                manufacturing_process_id INT,
                output_inventory_loc_id INT,
                FOREIGN KEY (source_inventory_loc_id) REFERENCES inventory_locations(inventory_loc_id),
                FOREIGN KEY (output_inventory_loc_id) REFERENCES inventory_locations(inventory_loc_id)
            )
        ";
        $this->db->execute($sqlWorkOrder);
        echo "Table 'work_orders' created successfully.<br>";

        // Create Work Order Details Table
        $sqlWorkOrderDetails = "
            CREATE TABLE IF NOT EXISTS work_order_details (
                work_order_detail_id INT AUTO_INCREMENT PRIMARY KEY,
                work_order_id INT,
                item_id VARCHAR(15),
                quantity DECIMAL(10, 3),
                FOREIGN KEY (work_order_id) REFERENCES work_orders(work_order_id),
                FOREIGN KEY (item_id) REFERENCES items(item_id)
            )
        ";
        $this->db->execute($sqlWorkOrderDetails);
        echo "Table 'work_order_details' created successfully.<br>";
    }

    public function addSampleWorkOrders() {
        $sampleOrders = [];
        for ($i = 1; $i <= 10; $i++) {
            $sampleOrders[] = [
                'order_date' => date('Y-m-d', strtotime("-".rand(1,30)." days")),
                'estimated_complete_date' => date('Y-m-d', strtotime("+".rand(1,30)." days")),
                'completed_date' => rand(0, 1) ? date('Y-m-d', strtotime("+".rand(1,30)." days")) : null,
                'status' => rand(0, 1) ? 'in progress' : 'completed',
                'notes' => 'Sample note for Work Order ' . $i,
                'source_inventory_loc_id' => rand(1, 5), // Assuming there are 5 inventory locations
                'manufacturing_process_id' => rand(1, 3), // Assuming there are 3 manufacturing processes
                'output_inventory_loc_id' => rand(1, 5)
            ];
        }

        foreach ($sampleOrders as $order) {
            $query = "
                INSERT INTO work_orders (order_date, estimated_complete_date, completed_date, status, notes, source_inventory_loc_id, manufacturing_process_id, output_inventory_loc_id) 
                VALUES ('{$order['order_date']}', '{$order['estimated_complete_date']}', ".($order['completed_date'] ? "'{$order['completed_date']}'" : "NULL").", '{$order['status']}', '{$order['notes']}', '{$order['source_inventory_loc_id']}', '{$order['manufacturing_process_id']}', '{$order['output_inventory_loc_id']}')
            ";
            $this->db->execute($query);

            $workOrderId = $this->db->getLastInsertId();

            $numberOfDetails = rand(1, 5);
            for ($j = 1; $j <= $numberOfDetails; $j++) {
                $itemId = rand(1, 100);
                $quantity = rand(1, 10);

                $detailQuery = "
                    INSERT INTO work_order_details (work_order_id, item_id, quantity) 
                    VALUES ('{$workOrderId}', '{$itemId}', '{$quantity}')
                ";
                $this->db->execute($detailQuery);
            }
        }

        echo "Sample work orders and details added successfully.<br>";
    }
}

?>
