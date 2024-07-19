<?php

require_once('Database.php');

// Get the q parameter from URL
$q = isset($_GET["q"]) ? $_GET["q"] : '';

if (strlen($q) > 0) {
    $db = new Database();
    $connection = $db->getConnection();
    $sql = "SELECT ProductName FROM products WHERE ProductName LIKE ?";
    $stmt = $connection->prepare($sql);
    $search = "%".$q."%";
    $stmt->bind_param('s', $search);
    $stmt->execute();
    $result = $stmt->get_result();

    $hint = "";
    while ($row = $result->fetch_assoc()) {
        if ($hint == "") {
            $hint = $row['ProductName'];
        } else {
            $hint .= "<br />" . $row['ProductName'];
        }
    }

    // Set output to "no suggestion" if no hint was found
    if ($hint == "") {
        $response = "no suggestion";
    } else {
        $response = $hint;
    }

    $stmt->close();
    $db->close();
} else {
    $response = "no suggestion";
}

// Output the response
echo $response;

?>
