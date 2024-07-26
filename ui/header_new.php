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
            <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-4">
                

