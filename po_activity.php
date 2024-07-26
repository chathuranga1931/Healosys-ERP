<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit;
}
if (isset($_GET['param'])) {
    $param = htmlspecialchars($_GET['param']); // Sanitize the input to prevent XSS attacks
    echo '<div id="id_po_activity" style="display:none;">' . htmlspecialchars($param) . '</div>';
} else {
    echo '<div id="id_po_activity" style="display:none;">' . htmlspecialchars(1) . '</div>';
}
?>

<?php include 'ui/header.php'; ?>

<style>
    .suggestion-item {
        cursor: pointer;
        padding: 5px;
    }
    .suggestion-item:hover, .suggestion-item.selected {
        background-color: #ddd;
    }
    .item-details {
        margin-bottom: 5px;
    }
    .item-details label {
        display: inline-block;
        width: 120px;
    }

    /* .item-details-2 label {
        display: inline-block;
        width: 150px;
    } */

    .item-details input, .item-details select, .item-details textarea {
        margin-bottom: 10px;
        width: 300px;
    }    
    #preview {
        max-width: 150px;
        max-height: 150px;
        display: none;
    }    
    .item-details {
        font-size: 0.75rem; /* Reduced font size */
    }
    #container {
        width: 300px;
        height: 200px;
        border: 1px solid black;
        overflow-y: scroll;
    } 
    
</style>

<script src="libs/Common.js"></script>
<script src="libs/LiveSearch.js"></script>
<script src="po/po_helper.js"></script>
<script src="po/po_list_helper.js"></script>
<script>

document.addEventListener('DOMContentLoaded', (event) => {
    let today = new Date();
    let day = String(today.getDate()).padStart(2, '0');
    let month = String(today.getMonth() + 1).padStart(2, '0'); // January is 0!
    let year = today.getFullYear();

    let todayDate = `${year}-${month}-${day}`;
    document.getElementById('order_date_id').value = todayDate;
    document.getElementById('delivery_date_id').value = todayDate;

    // liveSerch_Configure_for_PO();
    liverSearch_Confiure_for_Supplier();
    liverSearch_Confiure_for_Item();
});

document.addEventListener("DOMContentLoaded", function() {
    
    load_page_title("Purchase Order List");
    po_activity = document.getElementById("id_po_activity").innerHTML;       
    var statuses = get_statuses_for_activity(po_activity);    

    statuses.forEach(function(status) {
        load_po_activity(status, 0 , 1000 , 'id_div_po_list', po_activity);
    });
    
});
</script>

<div id="itemDetails" class="item-details" style="display:block;">
    <div class="row" >
        <div class="col-md-9">
            <div id="id_div_po_list"></div>
        </div>
    </div>
</div>

<?php include 'ui/footer.php'; ?>