<?php
require_once('../libs/Database.php');

require_once ('../libs/Logger.php');

$logger = new Logger();
$logger->log('livesearch_item_products', 'INFO', __FILE__);

$db = new Database();
$connection = $db->getConnection();

$q = isset($_GET['q']) ? $_GET['q'] : '';

if (strlen($q) > 0) {
    // First, get the category ID for the category "product"
    $categoryStmt = $connection->prepare("SELECT CategoryID FROM category WHERE CategoryType = 'Product' LIMIT 1");
    $categoryStmt->execute();
    $categoryResult = $categoryStmt->get_result();
    $categoryRow = $categoryResult->fetch_assoc();

    if ($categoryRow) {
        
        $categoryId = $categoryRow['CategoryID'];
        $logger->log('CategoryID = ' . $categoryId . '', 'INFO', __FILE__);        

        // Now, search for items within this category ID
        $stmt = $connection->prepare("SELECT name FROM items WHERE category_id = ? AND name LIKE ? LIMIT 10");
        $searchTerm = "%$q%";
        $stmt->bind_param('is', $categoryId, $searchTerm);
        $stmt->execute();
        $result = $stmt->get_result();

        $suggestions = [];
        while ($row = $result->fetch_assoc()) {
            $suggestions[] = $row['name'];
        }

        $stmt->close();

        if (empty($suggestions)) {
            echo "no suggestion";
        } else {
            foreach ($suggestions as $suggestion) {
                echo "<div class='suggestion-item' onclick='selectSuggestion_product(\"" . htmlspecialchars($suggestion, ENT_QUOTES) . "\")'>" . htmlspecialchars($suggestion) . "</div>";
            }
        }
    } else {
        echo "no suggestion";
    }

    $categoryStmt->close();
}

$connection->close();
?>
