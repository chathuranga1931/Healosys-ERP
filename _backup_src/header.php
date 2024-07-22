<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HealoSys ERP System</title>
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
            width: 150px;
            margin-bottom: 10px;
            margin-left: 10px;
            margin-top: 10px;
        }
        .section_line {
            background-color: lightgrey;
            border: 1px solid lightblue;
        }
    </style>
</head>
<body>
    <!-- <div class="row">
        <div id="id_global_footer_status">
            <script>
                function show_status(message, duration_sec, type) {
                    switch(type) {
                        case 'ERROR':
                            var alertElement = document.getElementById('id_global_footer_status');
                            alertElement.innerHTML = '<div class="alert alert-danger" role="alert" align="center">' + message + '</div>';
                            break;  
                        case 'WARNING':
                            var alertElement = document.getElementById('id_global_footer_status');
                            alertElement.innerHTML = '<div class="alert alert-warning" role="alert" align="center">' + message + '</div>';
                            break;
                        case 'SUCCESS':
                            var alertElement = document.getElementById('id_global_footer_status');
                            alertElement.innerHTML = '<div class="alert alert-success" role="alert" align="center">' + message + '</div>';
                            break;
                        case 'INFO':
                            default:
                            var alertElement = document.getElementById('id_global_footer_status');
                            alertElement.innerHTML = '<div class="alert alert-info" role="alert" align="center">' + message + '</div>';
                            break;
                    }
                    alertElement.style.display = 'block';

                    // Set a timeout to hide the alert after 5 seconds (5000 milliseconds)
                    setTimeout(function() {
                        alertElement.style.display = 'none'; // Hide the alert
                    }, duration_sec*1000);
                }
            </script>
        </div>
    </div> -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <!-- <a class="navbar-brand" href="#">HealoSys</a> -->
        <img src="https://assets.zyrosite.com/Aq260V4Dq2CJn0lp/logo-no-background-AR07Vg4zkkiZ55j3.svg" class="login-logo" alt="Logo">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" href="#">Welcome, <?php echo $_SESSION['username']; ?></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="logout.php">Logout</a>
                </li>
            </ul>
        </div>
    </nav>   
    <div class="container-fluid"> 
        <div class="row">
            <nav class="col-md-2 d-none d-md-block bg-light sidebar">
                <div class="sidebar-sticky">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link" href="#" data-bs-toggle="collapse" data-bs-target="#submenu1" aria-expanded="false" aria-controls="submenu1">
                                Inventory
                            </a>
                            <ul class="collapse" id="submenu1">
                                <li class="nav-item">
                                    <a class="nav-link" data-bs-toggle="collapse" data-bs-target="#submenu1-1" aria-expanded="false" aria-controls="submenu1-1">Items</a>
                                    <ul class="collapse" id="submenu1-1">
                                        <li class="nav-item">
                                            <a class="nav-link" href="item.php">Add Items</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" href="item_list.php">List Items</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" href="bom.php">Search Items</a>
                                        </li>
                                    </ul>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="supplier.php">Suppliers</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="bom.php">BOMs</a>
                                </li>
                            </ul>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#" data-bs-toggle="collapse" data-bs-target="#submenu2" aria-expanded="false" aria-controls="submenu2">
                                Sales
                            </a>
                            <ul class="collapse" id="submenu2">
                                <li class="nav-item">
                                    <a class="nav-link" href="#">Quotations</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="#">Work Orders</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="#">Sales Orders</a>
                                </li>
                            </ul>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">
                                Administration
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>
            <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-4">

