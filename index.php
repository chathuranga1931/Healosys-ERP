
<?php


// if (!isset($_SESSION['configured']) || $_SESSION['configured'] !== true) {
//     header('Location: setup/setup.php');
//     exit;
// }

session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    $_SESSION['configured'] = false;
    exit;
}

?>

<?php include 'ui/header.php'; ?>

<h1 class="mt-4">Welcome to the Dashboard</h1>
<p>This is an example of a simple Bootstrap layout with a left-side navigation bar.</p>

<?php include 'ui/footer.php'; ?>