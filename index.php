
<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit;
}
?>

<?php include 'ui/header.php'; ?>

<h1 class="mt-4">Welcome to the Dashboard</h1>
<p>This is an example of a simple Bootstrap layout with a left-side navigation bar.</p>

<?php include 'ui/footer.php'; ?>