<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bootstrap Nested Tabs with Smaller Fonts and Reduced Spacing</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-size: 0.875rem; /* Smaller font size for the entire body */
        }
        .nav-tabs .nav-link {
            font-size: 0.875rem; /* Smaller font size for tab titles */
            padding: 0.5rem 1rem; /* Reduce padding for tab titles */
        }
        .tab-content h3, .tab-content h4, .tab-content p {
            font-size: 0.875rem; /* Smaller font size for headings and content */
            margin: 0.5rem 0; /* Reduce margin for headings and content */
        }
        .tab-pane {
            padding: 0.5rem; /* Reduce padding inside tab panes */
        }
        .container {
            max-width: 100%; /* Adjust container width if needed */
            padding-left: 0; /* Remove left padding */
            padding-right: 0; /* Remove right padding */
        }
    </style>
</head>
<body>
    <div class="container mt-3">
        <!-- Primary Nav tabs -->
        <ul class="nav nav-tabs" id="primaryTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="main-home-tab" data-bs-toggle="tab" data-bs-target="#main-home" type="button" role="tab" aria-controls="main-home" aria-selected="true">Home</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="main-profile-tab" data-bs-toggle="tab" data-bs-target="#main-profile" type="button" role="tab" aria-controls="main-profile" aria-selected="false">Profile</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="main-contact-tab" data-bs-toggle="tab" data-bs-target="#main-contact" type="button" role="tab" aria-controls="main-contact" aria-selected="false">Contact</button>
            </li>
        </ul>

        <!-- Primary Tab panes -->
        <div class="tab-content" id="primaryTabContent">
            <div class="tab-pane fade show active" id="main-home" role="tabpanel" aria-labelledby="main-home-tab">
            </div>
            <div class="tab-pane fade" id="main-profile" role="tabpanel" aria-labelledby="main-profile-tab">
                <!-- Secondary Nav tabs -->
                <ul class="nav nav-tabs mt-2" id="secondaryTabProfile" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="sub-profile-details-tab" data-bs-toggle="tab" data-bs-target="#sub-profile-details" type="button" role="tab" aria-controls="sub-profile-details" aria-selected="true">Details</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="sub-profile-settings-tab" data-bs-toggle="tab" data-bs-target="#sub-profile-settings" type="button" role="tab" aria-controls="sub-profile-settings" aria-selected="false">Settings</button>
                    </li>
                </ul>
                <!-- Secondary Tab panes -->
                <div class="tab-content" id="secondaryTabContentProfile">
                    <div class="tab-pane fade show active" id="sub-profile-details" role="tabpanel" aria-labelledby="sub-profile-details-tab">
                    </div>
                    <div class="tab-pane fade" id="sub-profile-settings" role="tabpanel" aria-labelledby="sub-profile-settings-tab">
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="main-contact" role="tabpanel" aria-labelledby="main-contact-tab">
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
