<?php
require_once('../libs/Database.php');

$db = new Database();
$connection = $db->getConnection();

$q = isset($_GET['q']) ? $_GET['q'] : '';
$callBack = isset($_GET['oc']) ? $_GET['oc'] : 'selectSuggestion_OnClick';

if (strlen($q) > 0) {
    $stmt = $connection->prepare("SELECT supplier_name FROM suppliers WHERE supplier_name LIKE ?");
    $searchTerm = "%$q%";
    $stmt->bind_param('s', $searchTerm);
    $stmt->execute();
    $result = $stmt->get_result();

    $suggestions = [];
    while ($row = $result->fetch_assoc()) {
        $suggestions[] = $row['supplier_name'];
    }

    $stmt->close();

    if (empty($suggestions)) {
        echo "no suggestion";
    } else {
        foreach ($suggestions as $suggestion) {
            echo "<div class='suggestion-item' onclick='$callBack(\"" . htmlspecialchars($suggestion) . "\")'>" . htmlspecialchars($suggestion) . "</div>";
        }
    }
}

$connection->close();
?>
