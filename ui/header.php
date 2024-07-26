<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HealoSys ERP System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">    
    <script src="libs/Common.js"></script>
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
        .fixed-bottom-div {
            position: fixed;
            bottom: 0;
            width: 100%;
            background-color: #f8f9fa; /* Background color */
            padding: 10px; /* Padding */
            box-shadow: 0 -2px 5px rgba(0,0,0,0.1); /* Optional shadow for styling */
        }
        .navbar .d-flex {
            align-items: center; /* Optional: center vertically */
        }
        #welcome-user {
            margin-left: auto; /* Moves the username to the right */
        }
    </style>
</head>
<body>
    <div class="row">
        <div id="id_global_footer_status" class="fixed-bottom-div text-center">
        </div>
    </div>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <!-- <a class="navbar-brand" href="#">HealoSys</a> -->
        <img src="https://assets.zyrosite.com/Aq260V4Dq2CJn0lp/logo-no-background-AR07Vg4zkkiZ55j3.svg" class="login-logo" alt="Logo">
        <div class="d-flex justify-content-center w-100">
            <div id="id_page_title"></div>
        </div>

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
                            <a class="nav-link" href="#" data-bs-toggle="collapse" data-bs-target="#submenuAct" aria-expanded="false" aria-controls="submenuAct">
                                Activities
                            </a>
                            <ul class="collapse" id="submenuAct">
                                <li class="nav-item">
                                    <a class="nav-link" data-bs-toggle="collapse" data-bs-target="#submenuAPO-1" aria-expanded="false" aria-controls="submenuAPO-1">Purchase Order</a>
                                    <ul class="collapse" id="submenuAPO-1">
                                        <li class="nav-item">
                                            <a class="nav-link" href="po_activity.php?param=Place">Place</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" href="po_activity.php?param=Delivered">Delivered</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" href="po_activity.php?param=Delivered_ModifyRquired">Delivered, Modify Required</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" href="po_activity.php?param=Suspend">Suspend</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" href="po_activity.php?param=Unsuspend">Unsuspend</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" href="po_activity.php?param=Cancel">Cancel</a>
                                        </li>
                                    </ul>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-bs-toggle="collapse" data-bs-target="#submenuWO-1" aria-expanded="false" aria-controls="submenuWO-1" >Work Order</a>
                                    <ul class="collapse" id="submenuWO-1">
                                        <li class="nav-item">
                                            <a class="nav-link" href="po_add.php">Start</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" href="po_list.php">Delivered</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" href="po_list.php">Complete</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" href="po_list.php">Suspend</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" href="po_list.php">Cancel</a>
                                        </li>
                                    </ul>                                
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-bs-toggle="collapse" data-bs-target="#submenuSale-1" aria-expanded="false" aria-controls="submenuSale-1" >Sale</a>
                                    <ul class="collapse" id="submenuSale-1">
                                        <li class="nav-item">
                                            <a class="nav-link" href="po_add.php">Start</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" href="po_list.php">Dispatch</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" href="po_list.php">Buyer Comfirmed</a>
                                        </li>
                                    </ul>
                                </li>
                            </ul>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#" data-bs-toggle="collapse" data-bs-target="#submenu1" aria-expanded="false" aria-controls="submenu1">
                                Inventory
                            </a>
                            <ul class="collapse" id="submenu1">    
                                <li class="nav-item">
                                    <a class="nav-link" href="inventory-movements.php">Movements</a>
                                    <!-- <ul class="collapse" id="submenuIM-1">
                                        <li class="nav-item">
                                            <a class="nav-link" href="po_add.php">Add </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" href="po_list.php">List</a>
                                        </li>
                                    </ul>                                 -->
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-bs-toggle="collapse" data-bs-target="#submenu1-1" aria-expanded="false" aria-controls="submenu1-1">Items</a>
                                    <ul class="collapse" id="submenu1-1">
                                        <li class="nav-item">
                                            <a class="nav-link" href="item.php">Add</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" href="item_list.php">List</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" href="bom.php">BOMs</a>
                                        </li>
                                    </ul>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-bs-toggle="collapse" data-bs-target="#submenu2-1" aria-expanded="false" aria-controls="submenu2-1" >Purchase Orders</a>
                                    <ul class="collapse" id="submenu2-1">
                                        <li class="nav-item">
                                            <a class="nav-link" href="po_add.php">Add </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" href="po_list.php">List</a>
                                        </li>
                                    </ul>                                
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="supplier.php">Suppliers</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="bom.php">Work Orders</a>
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
                <div id="id_page_title"></div>

