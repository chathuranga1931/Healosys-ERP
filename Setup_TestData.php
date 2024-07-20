<?php

require_once('database.php');

class SetupTestData {
    private $db;

    public function __construct() {
        $this->db = new Database();
        $this->insertTestData();
    }

    private function insertTestData() {
        // Sample list of electronic components with variations
        $componentNames = [
            "Transistor NPN C828", "Transistor NPN C945", "Transistor PNP A1015", "Transistor PNP C3906",
            "Capacitor Ceramic 10nF", "Capacitor Electrolytic 100uF", "Capacitor Tantalum 47uF", "Capacitor Film 1uF",
            "Resistor 1K Ohm", "Resistor 10K Ohm", "Resistor 100K Ohm", "Resistor 1M Ohm",
            "Diode 1N4148", "Diode Zener 5.1V", "Diode Schottky 1N5819", "Diode Rectifier 1N4007",
            "Inductor 10uH", "Inductor 100uH", "Inductor 1mH", "Inductor 10mH",
            "LED Red 5mm", "LED Green 5mm", "LED Blue 5mm", "LED Yellow 5mm",
            "Microcontroller ATmega328P", "Microcontroller PIC16F877A", "Microcontroller STM32F103", "Microcontroller MSP430G2553",
            "Switch Toggle SPDT", "Switch Push Button", "Switch Slide SPDT", "Switch Rotary Encoder",
            "Relay 5V", "Relay 12V", "Relay 24V", "Relay Solid State",
            "Connector USB Type-A", "Connector USB Type-B", "Connector HDMI", "Connector RJ45",
            "Sensor Temperature LM35", "Sensor Humidity DHT22", "Sensor Pressure BMP180", "Sensor Light LDR"
        ];

        // Function to generate unique ProductID
        function generateProductID($index) {
            return sprintf("P%03d", $index+10);
        }

        // Generate 100 products
        $products = [];
        for ($i = 1; $i <= 100; $i++) {
            $productName = $componentNames[array_rand($componentNames)];
            $products[] = [
                'ProductID' => generateProductID($i),
                'ProductName' => $productName,
                'ProductCategory' => 'Component'
            ];
        }

        // Insert products into the database
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

        echo "100 products inserted successfully.<br>";
    }
}

new SetupTestData();

?>
