<?php
require_once('../libs/Database.php');

$db = new Database();
$connection = $db->getConnection();

$q = isset($_GET['q']) ? $_GET['q'] : '';
$callBack = isset($_GET['oc']) ? $_GET['oc'] : 'selectSuggestion_OnClick';

if (strlen($q) > 0) {
    $stmt = $connection->prepare("SELECT purchase_order_id FROM purchase_orders WHERE status = ? AND purchase_order_id LIKE ? LIMIT 10");
    $searchTerm = "%$q%";
    $status = 'Open';
    $stmt->bind_param('ss', $status, $searchTerm);
    $stmt->execute();
    $result = $stmt->get_result();

    $suggestions = [];
    while ($row = $result->fetch_assoc()) {
        $suggestions[] = $row['purchase_order_id'];
    }

    $stmt->close();

    if (empty($suggestions)) {
        echo "no suggestion";
    } else {
        foreach ($suggestions as $suggestion) {
            echo "<div class='suggestion-item' onclick='$callBack(\"" . htmlspecialchars($suggestion, ENT_QUOTES) . "\")'>" . htmlspecialchars($suggestion) . "</div>";
        }
    }
}

$connection->close();
?>
