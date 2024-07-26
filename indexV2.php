<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit;
}

?>

<?php include 'ui/header_new.php'; ?>

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

        document.getElementById("popup_model_po_confirmation_form").addEventListener("submit", function(event) {
            event.preventDefault();
            
            const confirmation = confirm("Invetory will be updated with this quantities, Please confirm !");
            if (confirmation) {
                
                var po_id_str = document.getElementById("po_code").value;
                var po_id = unpadID(po_id_str, 'PO');
                
                processPurchaseOrder(po_id, function(result) {
                    if(result.status === 'success'){                        
                        show_status("Inventory, Updated: Success", 3, "SUCCESS");
                    }
                    else{
                        show_status("Inventory Updated: Failed, " + result.message, 3, "ERROR");
                    }
                }); 
            } 
            else {

            }

            var modal = bootstrap.Modal.getInstance(document.getElementById('popup_model_po_confirmation'));
            modal.hide();
        });

        on_loading();
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
        button.setAttribute('data-bs-target', '#popup_model_po_confirmation');

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

    function createDivWithClass_DivAndRole(parentId, className, role, id=null) {
        // Get the parent element by its ID
        var parentElement = document.getElementById(parentId);
        
        // Check if the parent element exists
        if (parentElement) {
            // Create a new div element
            var newDiv = document.createElement('div');
            
            // Set the class attribute
            newDiv.className = className;

            if(id != null){
                newDiv.id = id;
            } 
            
            // Append the new div to the parent element
            parentElement.appendChild(newDiv);
            
            // Return the created div element
            return newDiv;
        } else {
            console.error('Parent element not found');
            return null;
        }
    }

    function createDivWithClass_Div(parentElement, className, id=null) {
        // Get the parent element by its ID
        
        
        // Check if the parent element exists
        if (parentElement) {
            // Create a new div element
            var newDiv = document.createElement('div');
            
            // Set the class attribute
            newDiv.className = className;

            if(id != null){
                newDiv.id = id;
            } 
            
            // Append the new div to the parent element
            parentElement.appendChild(newDiv);
            
            // Return the created div element
            return newDiv;
        } else {
            console.error('Parent element not found');
            return null;
        }
    }

    function createNavTabs_ul(parentElement, ulId) {
        
        // Check if the parent element exists
        if (parentElement) {
            // Create a new ul element
            var newUl = document.createElement('ul');
            
            // Set the class attribute
            newUl.className = 'nav nav-tabs';
            
            // Set the id attribute
            newUl.id = ulId;
            
            // Set the role attribute
            newUl.setAttribute('role', 'tablist');
            
            // Append the new ul to the parent element
            parentElement.appendChild(newUl);
            
            // Return the created ul element
            return newUl;
        } else {
            console.error('Parent element not found');
            return null;
        }
    }

    function createNavItem_li(parentElement, id, selected, content, target_div_id, on_click=null) {
        // Check if the parent element exists
        if (parentElement) {
            // Create a new li element
            var newLi = document.createElement('li');
            newLi.className = 'nav-item';
            newLi.setAttribute('role', 'presentation');

            // Create a new button element
            var newButton = document.createElement('button');
            newButton.className = 'nav-link' + (selected ? ' active' : '');
            newButton.id = id;
            newButton.setAttribute('data-bs-toggle', 'tab');
            newButton.setAttribute('data-bs-target', `#${target_div_id}`);
            newButton.setAttribute('type', 'button');
            newButton.setAttribute('role', 'tab');
            newButton.setAttribute('aria-controls', target_div_id);
            newButton.setAttribute('aria-selected', selected ? 'true' : 'false');
            newButton.textContent = content;

            if(on_click != null){
                newLi.addEventListener('shown.bs.tab', on_click);
            }                

            // Append the button to the li
            newLi.appendChild(newButton);

            // Append the li to the parent element
            parentElement.appendChild(newLi);

            // Return the created li element
            return newLi;
        } else {
            console.error('Parent element not found');
            return null;
        }
    }

    function createTabPane_Div(parentElement, id, controlledByElement, isActive) {
        // Check if the parent element exists
        if (parentElement) {
            // Create a new div element
            var newDiv = document.createElement('div');
            
            // Set the class attribute
            newDiv.className = 'tab-pane fade';
            if (isActive) {
                newDiv.className += ' show active';
            }
            
            // Set the id attribute
            newDiv.id = id;
            
            // Set the role attribute
            newDiv.setAttribute('role', 'tabpanel');
            
            // Set the aria-labelledby attribute
            newDiv.setAttribute('aria-labelledby', controlledByElement.id);
            
            // Append the new div to the parent element
            parentElement.appendChild(newDiv);
            
            // Return the created div element
            return newDiv;
        } else {
            console.error('Parent element not found');
            return null;
        }
    }

    function on_loading(){
        var parent = "id_testing_container"
        var parentElement = document.getElementById(parent);

        // function createDivWithClass_Div(parentElement, className, id=null)        
        Tabs_element_div = createDivWithClass_Div(parentElement, "container mt-3", "parent-div-nnnn");
        // Level 1 - Contents
        Content_leve1_Div = createDivWithClass_Div(parentElement, "tab-content");
            
        // function createNavTabs_ul(parentElement, ulId)
        Tabs_level1_ul = createNavTabs_ul(Tabs_element_div, "primaryTab");   
            
            //function createNavItem_li(parentElement, id, selected, content, target_div_id, on_click=null) {
            HomeTab_li = createNavItem_li(Tabs_level1_ul, "main-home-tab", true, "Home", "main-home", null);
            //function createTabPane_Div(parentElement, id, controlledByElement, isActive) {
            HomeContent_Div = createTabPane_Div(Content_leve1_Div, "main-home", HomeTab_li, true);

            InventoryTab_li = createNavItem_li(Tabs_level1_ul, "main-inventory-tab", false, "Inventory", "main-inventory", null);        
            InventoryContent_Div = createTabPane_Div(Content_leve1_Div, "main-inventory", InventoryTab_li, false);
            Inventory_ul = createNavTabs_ul(InventoryContent_Div, "inventory_ul");   
            InventorySubmenuContent_Div = createDivWithClass_Div(InventoryContent_Div, "tab-content", "inventory-submenu-content");
                    ItemsTab_li = createNavItem_li(Inventory_ul, "items-tab", false, "Items", "items-content", null);
                    ItemsContent = createTabPane_Div(InventorySubmenuContent_Div, "items-content", ItemsTab_li, false); 
                    Items_ul = createNavTabs_ul(ItemsContent, "items_ul");  
                        ItemsAddTab_li = createNavItem_li(Items_ul, "items-add-tab", false, "Add", "items-add-content", null);
                        ItemsAddContent_Div = createTabPane_Div(ItemsContent, "items-add-content", ItemsAddTab_li, false);
                        ItemsListTab_li = createNavItem_li(Items_ul, "items-list-tab", false, "List", "items-list-content", null);
                        ItemsListContent_Div = createTabPane_Div(ItemsContent, "items-list-content", ItemsListTab_li, false);  
                        
                    InventoryMovementsTab_li = createNavItem_li(Inventory_ul, "inventory-movements-tab", false, "Inventory Movements", "inventory-movements-content", null);
                    InventoryMovementsContent_Div = createTabPane_Div(InventorySubmenuContent_Div, "inventory-movements-content", InventoryMovementsTab_li, false); 
                    InventoryMovements_ul = createNavTabs_ul(InventoryMovementsContent_Div, "inventory_movement_ul");    
                    InventoryMovementsSubmenuContent_Div = createDivWithClass_Div(InventoryMovementsContent_Div, "tab-content", "inventory_movement-submenu-content");
                        WokrOrdersIMTab_li = createNavItem_li(InventoryMovements_ul, "work-orders-im-tab", false, "Work Orders", "work-orders-im-content", null);                        
                        WokrOrdersIMContent_Div = createTabPane_Div(InventoryMovementsSubmenuContent_Div, "work-orders-im-content", WokrOrdersIMTab_li, false);                         
                        PurchaseOrdersIMTab_li = createNavItem_li(InventoryMovements_ul, "purchase-orders-im-tab", false, "Purchase Orders", "purchase-orders-im-content", onMovementsPurchaseOrderTabSelected);
                        PurchaseOrdersIMContent_Div = createTabPane_Div(InventoryMovementsSubmenuContent_Div, "purchase-orders-im-content", PurchaseOrdersIMTab_li, false); 
                    
            ManufacturingTab_li = createNavItem_li(Tabs_level1_ul, "manufacturing-tab", false, "Manufacturing", "manufacturing-content", null);
            ManufacturingContent = createTabPane_Div(Content_leve1_Div, "manufacturing-content", ManufacturingTab_li, false);
            PurchaseTab_li = createNavItem_li(Tabs_level1_ul, "purchase-tab", false, "Purchase", "purchase-content", null);
            PurchaseContent = createTabPane_Div(Content_leve1_Div, "purchase-content", PurchaseTab_li, false); 
            SalesTab_li = createNavItem_li(Tabs_level1_ul, "sales-tab", false, "Sales", "sales-content", null);
            SalesContent = createTabPane_Div(Content_leve1_Div, "sales-content", SalesTab_li, false); 
            SupplierTab_li = createNavItem_li(Tabs_level1_ul, "supplier-tab", false, "Suppliers", "supplier-content", null);
            SupplierContent = createTabPane_Div(Content_leve1_Div, "supplier-content", SupplierTab_li, false);
        
        PurchaseOrderContentDiv = createDivWithClass_Div(PurchaseOrdersIMContent_Div, "item-details-2", "id_movement_purchase_order_l1");
    }


</script>

    <div id="id_testing_container">
    </div>

    <!-- Modal -->
    <div class="modal fade" id="popup_model_po_confirmation" tabindex="-1" aria-labelledby="dataEntryModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="dataEntryModalLabel">Purchase Order Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="popup_model_po_confirmation_form">

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

<?php include 'ui/footer_new.php'; ?>
