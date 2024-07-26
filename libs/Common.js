

function load_page_title(title_name){
    document.getElementById("id_page_title").innerHTML = "<h3>"+title_name+"</h3>";
}

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