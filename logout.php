<?php
session_start();
session_destroy();
header('Location: login.php');
exit;
?>

<!DOCTYPE html>
<html>
<head>
    <title>Logout</title>
</head>
<body>

<h2>You have been logged out</h2>
<p><a href="login.php">Login again</a></p>

</body>
</html>