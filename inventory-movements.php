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
    #container {
        width: 300px;
        height: 200px;
        border: 1px solid black;
        overflow-y: scroll;
    } 
    .grid-item {
        background-color: #ccc;
        border: 1px solid #333;
        display: flex;
        align-items: center;
        justify-content: center;
        height: 200px; /* Optional: set a fixed height */
    }     
    .item-details-2 {
        font-size: 0.75rem; /* Reduced font size */
    } 
    
</style>

<script src="libs/Common.js"></script>
<!-- <script src="libs/LiveSearch.js"></script> -->
<script src="po/po_helper.js"></script>
<script src="po/po_list_helper.js"></script>

<script>

    document.addEventListener("DOMContentLoaded", function() {
        load_page_title("Inventory Movements");

        document.getElementById('main-movements-tab').addEventListener('shown.bs.tab', function(event) {
            // Call your function when the "Movements" tab is selected
            onMovementsTabSelected();
        });

        document.getElementById('sub-movements-purcahseorder-tab').addEventListener('shown.bs.tab', function(event) {
            // Call your function when the "Movements" tab is selected
            onMovementsPurchaseOrderTabSelected();
        });
    });


    function callback_Purchase_Order(po){
        // purchase_order_id, supplier_id, order_date, delivery_date, status, total_amount, notes
        document.getElementById("po_code").value = padID(po.purchase_order_id, 'PO', 3); // ; po.purchase_order_id.toString();
        document.getElementById("order_date_id").value = po.order_date;
        document.getElementById("searchInput-supplier").value = "";
        document.getElementById("supplier_id").value = padID(po.supplier_id, 'SUP', 3); // po.supplier_id;
        document.getElementById("status_id").value = po.status;
        document.getElementById("delivery_date_id").value = po.delivery_date;
        document.getElementById("description").value = po.notes; 
    } 

    function callback_Purchase_OrderDetails(po, po_details){

        // purchase_order_detail_id	purchase_order_id	item_id	quantity	price	total_amount
        po_details.forEach(po_detail => {
            add_item_to_po('id_purchase_order_table_from_form', po_detail.item_id, po_detail.quantity, po.status);
        });
    }

    function on_complete_order_clicked(purchase_order_id , activity){
        
        fetchPurchaseOrderById_WithCallBack(purchase_order_id, callback_Purchase_Order, callback_Purchase_OrderDetails);
    }

    function create_button_function(button_text, button_cls, button_id, button_callback){

        var button = document.createElement('button');

        // Set the button's attributes
        button.type = 'button';
        button.className = 'btn btn-primary';
        button.innerText = button_text;
        button.id = button_id;
        button.onclick = button_callback;

        // Set Bootstrap data attributes for modal
        button.setAttribute('data-bs-toggle', 'modal');
        button.setAttribute('data-bs-target', '#dataEntryModal');

        return button;
    }

    function load_delivered_pos(id_movement_purchase_order_l1){

        var status = "Delivered";
        var start = 0;
        var limit = 50;
        var activity = "Add-to-Inventory";

        load_po_activity(status, start, limit, id_movement_purchase_order_l1, activity, create_button_function, on_complete_order_clicked, function(){
        });

        // load_po_activity(status, start, limit, id_movement_purchase_order_l1, activity, on_complete_order_clicked, function(){
        // });
    }    

    function onMovementsPurchaseOrderTabSelected() {
        // alert("Movements Purchase Order tab is selected");  
        document.getElementById('id_movement_purchase_order_l1').innerHTML = "";
        load_delivered_pos('id_movement_purchase_order_l1');    
    }

    // Function to be called when "Movements" tab is selected
    function onMovementsTabSelected() {
        // alert("Movements tab is selected");
        document.getElementById('id_movement_purchase_order_l1').innerHTML = "";
        load_delivered_pos('id_movement_purchase_order_l1');
    }

    document.getElementById("dataEntryForm").addEventListener("submit", function(event) {
        event.preventDefault();
        alert("Form submitted!");
        var modal = bootstrap.Modal.getInstance(document.getElementById('dataEntryModal'));
        modal.hide();
    });

</script>

    <div class="container mt-3">
        <!-- Primary Nav tabs -->
        <ul class="nav nav-tabs" id="primaryTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="main-home-tab" data-bs-toggle="tab" data-bs-target="#main-home" type="button" role="tab" aria-controls="main-home" aria-selected="true">Home</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="main-movements-tab" data-bs-toggle="tab" data-bs-target="#main-movements" type="button" role="tab" aria-controls="main-movements" aria-selected="false">Movements</button>
            </li>
        </ul>

        <!-- Primary Tab panes -->
        <div class="tab-content" id="primaryTabContent">
            <div class="tab-pane fade show active" id="main-home" role="tabpanel" aria-labelledby="main-home-tab">
            </div>
            <div class="tab-pane fade" id="main-movements" role="tabpanel" aria-labelledby="main-movements-tab">
                <!-- Secondary Nav tabs -->
                <ul class="nav nav-tabs mt-2" id="secondaryTabMovements" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="sub-movements-purcahseorder-tab" data-bs-toggle="tab" data-bs-target="#sub-movements-purcahseorder" type="button" role="tab" aria-controls="sub-movements-purcahseorder" aria-selected="true">Purchase Orders</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="sub-movements-workorders-tab" data-bs-toggle="tab" data-bs-target="#sub-movements-workorders" type="button" role="tab" aria-controls="sub-movements-workorders" aria-selected="false">Work Orders</button>
                    </li>
                </ul>
                <!-- Secondary Tab panes -->
                <div class="tab-content" id="secondaryTabContentProfile">
                    <div class="tab-pane fade show active" id="sub-movements-purcahseorder" role="tabpanel" aria-labelledby="sub-movements-purcahseorder-tab">
                        <div class='item-details-2' id="id_movement_purchase_order_l1"> </div>
                    </div>
                    <div class="tab-pane fade" id="sub-movements-workorders" role="tabpanel" aria-labelledby="sub-movements-workorders-tab">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="dataEntryModal" tabindex="-1" aria-labelledby="dataEntryModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="dataEntryModalLabel">Purchase Order Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="dataEntryForm">

                        <div id="itemDetails-2" class="item-details" style="display:block;">
                            <div class="container">
                                <div class="row">
                                    <div class="col-md-2">
                                        <label for="po_code">PO Code :</label>
                                        <input type="text" id="po_code" name="po_code" maxlength="15" value="Automatically Generated" readonly style="width: 150px;">                
                                    </div>
                                    <div class="col-md-2">                
                                        <label for="order_date_id">Order Date :</label>
                                        <input type="date" id="order_date_id" name="order_date" style="width: 150px;">
                                    </div> 
                                    <div class="col-md-2">                
                                        <label for="delivery_date_id">Dilivery Date :</label>
                                        <input type="date" id="delivery_date_id" name="delivery_date" style="width: 150px;">
                                    </div>
                                    <div class="col-md-2">
                                        <label for="searchInput-supplier">Supplier :</label>
                                        <input type="text" id="searchInput-supplier" placeholder="Search for Supplier" style="width: 150px;">
                                        <div id="livesearch-supplier"></div>                
                                    </div>
                                    <div class="col-md-2">
                                        <label for="supplier_id">Supplier ID :</label>
                                        <input type="text" id="supplier_id" name="supplier_id" readonly style="width: 150px;">              
                                    </div>
                                    <div class="col-md-2">                
                                        <label for="status_id">PO Staus :</label>
                                        <input type="text" id="status_id" name="status_id" readonly value="Open" style="width: 150px;">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-2">
                                        <label for="description">PO Note:</label>
                                        <textarea id="description"  name="description" rows="2"  size="20"></textarea>                 
                                    </div>  
                                    <div class="col-md-2">  
                                    </div> 
                                    <div class="col-md-2">             
                                    </div>   
                                </div>
                                <div class="row">
                                    <div class="col-12 text-right">
                                        <!-- <button id="updateButton" class="btn btn-primary mr-2">Update Item</button> -->
                                        <!-- <button id="addItemButton" class="btn btn-secondary">Add</button> -->
                                    </div>
                                </div>
                                <div class="row">
                                    <div id="id_purchase_order_table_from_form" ></div>
                                </div>
                                <div class="row">
                                    <div class="col-12 d-flex justify-content-end">
                                        <button type="submit" class="btn btn-primary">Approve</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div id='container-generated-tab'>
    </div>

<?php include 'ui/footer.php'; ?>
