<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit;
}

if (isset($_GET['param'])) {
    $param = htmlspecialchars($_GET['param']); // Sanitize the input to prevent XSS attacks
    echo '<div id="id_po_code_for_edit" style="display:none;">' . htmlspecialchars($param) . '</div>';
} else {
    echo '<div id="id_po_code_for_edit" style="display:none;">' . htmlspecialchars(1) . '</div>';
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
    .item-details-2 {
        font-size: 0.75rem; /* Reduced font size */
    } 
    .item-details-3 {
        font-size: 0.75rem; /* Reduced font size */
    } 
    
</style>

<script src="libs/Common.js"></script>
<script src="libs/LiveSearch.js"></script>
<script src="po/po_helper.js"></script>

<script>
document.addEventListener("DOMContentLoaded", function() {
    load_page_title("Purchase Order Update");

    po_id_for_edit = document.getElementById("id_po_code_for_edit").innerHTML;
    fetchPurchaseOrderById(po_id_for_edit);
});

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
</script>

<h5>Select PO for Update</h5>
<!-- <div class="section_line"></div> -->
<div id="itemDetails-2" class="item-details" style="display:block;">
    <div class="container">
        <div class="row">
            <div class="col-md-3">
                <label for="po_code">PO Code:</label>
                <input type="text" id="po_code" name="po_code" maxlength="15" value="Automatically Generated" readonly >                
            </div>
            <div class="col-md-3">
                <label for="searchInput-supplier">Supplier :</label>
                <input type="text"   id="searchInput-supplier" placeholder="Search for Supplier">
                <div id="livesearch-supplier"></div>                
            </div>
            <div class="col-md-3">                
                <label for="order_date_id">Order Date:</label>
                <input type="date" id="order_date_id" name="order_date">
            </div> 
        </div>
        <div class="row">
            <div class="col-md-3">                
                <label for="status_id">PO Staus:</label>
                <input type="text" id="status_id" name="status_id" readonly value="Open">
            </div>
            <div class="col-md-3">
                <label for="supplier_id">Supplier ID:</label>
                <input type="text" id="supplier_id" name="supplier_id" readonly>              
            </div>
            <div class="col-md-3">                
                <label for="delivery_date_id">Dilivery Date:</label>
                <input type="date" id="delivery_date_id" name="delivery_date">
            </div>
            <div class="col-md-3">             
            </div>  
        </div>
        <div class="row">
            <div class="col-md-3">
                <label for="description">PO Note:</label>
                <textarea id="description"  name="description" rows="2" ></textarea>                 
            </div>  
            <div class="col-md-3">  
            </div> 
            <div class="col-md-3">             
            </div>   
        </div>
        <div class="row">
            <div class="col-12 text-right">
                <!-- <button id="updateButton" class="btn btn-primary mr-2">Update Item</button> -->
                <!-- <button id="addItemButton" class="btn btn-secondary">Add</button> -->
            </div>
        </div>
    </div>
</div>
<h5>PO Item List</h5>
<div class="container">
    <div class="row">
        <div id="itemDetails-3" class="item-details" style="display:block;">
            <div class="row" style="display:block;">
                <div class="col-9">
                    <div class="item-details" id="id_div_po_table">
                    </div>
                </div>
                <div class="col-9">
                    <button id="savePurchaseOrder" onclick="onClick_UpdatePO()" class="btn btn-secondary">Update PO</button>
                </div>
            </div>
        </div>
    </div>
</div>
<br>
<h5>Select Item</h5>
<div class="container mt-3">
    <div class="row">
        <div class="item-details" style="display:block;">
            <div class="row">
                <form autocomplete="off">
                    <div class="row">
                        <label for="searchInput-item">Item Search :</label>
                        <input type="text"   id="searchInput-item" placeholder="Search for items...">
                        <div id="livesearch-item"></div>        
                    </div>
                </form>
            </div>
            <div class="row" id="id_item_div_row" style="display:none;">
                <div class="col-9">
                    <div style="font-size: 0.75em;" id="id_item_details">
                    </div>
                </div>
                <div class="col-9">
                    <button id="addItemToPO" onclick="onClick_addItemToPO()" class="btn btn-secondary">Add to PO</button>
                </div>
            </div>
        </div>
    </div>
</div>
</script>

<?php include 'ui/footer.php'; ?>
