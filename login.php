
<?php
session_start();
require_once('libs/Database.php');

require_once ('libs/Logger.php');
$logger = new Logger();
$logger->log('1', 'INFO', __FILE__);

if (isset($_POST['username']) && isset($_POST['password'])) {
    
    $logger->log('Data is available', 'INFO', __FILE__);

    $username = $_POST['username'];
    $password = $_POST['password'];

    // Check credentials in the database
    $db = new Database();
    $connection = $db->getConnection();
    $stmt = $connection->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();
    
    $logger->log('User Name = '.$username.'' ,'INFO', __FILE__);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['loggedin'] = true;
        $_SESSION['username'] = $username;
        header('Location: index.php');
        $logger->log('Login Success...', 'INFO', __FILE__);
        exit;
    } else {
        $logger->log('Login Failed...', 'INFO', __FILE__);
        $error = 'Invalid username or password';
    }
}

$logger->log('Something is not right', 'INFO', __FILE__);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .login-container {
            max-width: 400px;
            margin: auto;
            margin-top: 100px;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 10px;
            background-color: #f9f9f9;
        }
        .login-logo {
            width: 300px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="login-container text-center">
            <img src="https://assets.zyrosite.com/Aq260V4Dq2CJn0lp/logo-no-background-AR07Vg4zkkiZ55j3.svg" class="login-logo" alt="Logo">
            <h2 class="mb-4">Login</h2>
            <form action="login.php" method="post">
                <div class="mb-3">
                    <input type="text" class="form-control" id="username" name="username" placeholder="Username" required>
                </div>
                <div class="mb-3">
                    <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
                </div>
                <button type="submit" class="btn btn-primary btn-block">Login</button>
            </form>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
