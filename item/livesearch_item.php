<?php
require_once('../libs/Database.php');

$db = new Database();
$connection = $db->getConnection();

$q = isset($_GET['q']) ? $_GET['q'] : '';

if (strlen($q) > 0) {
    $stmt = $connection->prepare("SELECT name FROM items WHERE name LIKE ? LIMIT 10");
    $searchTerm = "%$q%";
    $stmt->bind_param('s', $searchTerm);
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
            echo "<div class='suggestion-item' onclick='selectSuggestion_item(\"" . htmlspecialchars($suggestion, ENT_QUOTES) . "\")'>" . htmlspecialchars($suggestion) . "</div>";
        }
    }
}

$connection->close();
?>
