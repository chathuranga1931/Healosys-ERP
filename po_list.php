<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit;
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
</style>

<script src="libs/Common.js"></script>
<script src="po/po_helper.js"></script>
<script src="po/po_list_helper.js"></script>

<script>
document.addEventListener("DOMContentLoaded", function() {
    load_page_title("Purchase Order List");

    updateStatusList('po_status_list_id', 0, function(status){        
        load_po(status, 0 , 1000 , 'id_div_po_list');
    });
});

// document.addEventListener('DOMContentLoaded', (event) => {
//     let today = new Date();
//     let day = String(today.getDate()).padStart(2, '0');
//     let month = String(today.getMonth() + 1).padStart(2, '0'); // January is 0!
//     let year = today.getFullYear();

//     let todayDate = `${year}-${month}-${day}`;
//     document.getElementById('order_date_id').value = todayDate;
//     document.getElementById('delivery_date_id').value = todayDate;

//     // liveSerch_Configure_for_PO();
//     liverSearch_Confiure_for_Supplier();
//     liverSearch_Confiure_for_Item();
// });

function on_status_changed() {
    var status_select = document.getElementById('po_status_list_id');
    var status = status_select.options[status_select.selectedIndex].innerText;
    document.getElementById('id_div_po_list').innerHTML = '';
    load_po(status, 0 , 1000 , 'id_div_po_list');
}

</script>

<div class="row">
    <div class="col-md-9">
        <label for="po_status_list_id">Purcase Order Status: </label>
        <select id="po_status_list_id"  name="po_status_list_id" onchange="on_status_changed()"></select>
    </div>
</div>
<br>
<div id="itemDetails" class="item-details" style="display:block;">
    <div class="row" >
        <div class="col-md-9">
            <div id="id_div_po_list"></div>
        </div>
    </div>
</div>

<?php include 'ui/footer.php'; ?>
