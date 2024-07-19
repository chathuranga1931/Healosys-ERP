<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit;
}

require_once('Database.php');

if (isset($_POST['new_username']) && isset($_POST['new_password'])) {
    $new_username = $_POST['new_username'];
    $new_password = password_hash($_POST['new_password'], PASSWORD_DEFAULT);

    // Update credentials in the database
    $db = new Database();
    $connection = $db->getConnection();
    $stmt = $connection->prepare("UPDATE users SET username = ?, password = ? WHERE username = 'admin'");
    $stmt->bind_param('ss', $new_username, $new_password);
    $stmt->execute();
    $stmt->close();

    $message = 'Username and password updated successfully';
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Configuration</title>
</head>
<body>

<h2>Change Login Information</h2>
<?php if (isset($message)): ?>
    <p style="color: green;"><?php echo $message; ?></p>
<?php endif; ?>
<form method="post">
    <label for="new_username">New Username:</label>
    <input type="text" id="new_username" name="new_username" required>
    <br>
    <label for="new_password">New Password:</label>
    <input type="password" id="new_password" name="new_password" required>
    <br>
    <button type="submit">Update</button>
</form>

<p><a href="index.php">Back to Home</a></p>

</body>
</html>
