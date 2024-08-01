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
<script src="libs/LiveSearch.js"></script>
<script src="ui/ui_supporting.js"></script>
<script src="sys/sys_helper.js"></script>
<script src="po/po_api_helper.js"></script>
<script src="po/po_ui_helper.js"></script>
<script src="workorders/wo_api_helper.js"></script>
<script src="workorders/wo_ui_helper.js"></script>
<script src="supplier/supplier_api_helper.js"></script>
<script src="supplier/supplier_ui_helper.js"></script>
<script src="libs/Print.js"></script>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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

    function on_loading(){
        var parent = "id_tab_list_head"
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
                ItemsContent_Div = createTabPane_Div(InventorySubmenuContent_Div, "items-content", ItemsTab_li, false); 
                Items_ul = createNavTabs_ul(ItemsContent_Div, "items_ul");  
                ItemsContentSubmenuContent_Div = createDivWithClass_Div(ItemsContent_Div, "tab-content", "items-submenu-content");  
                    ItemsAddTab_li = createNavItem_li(Items_ul, "items-add-tab", false, "Add", "items-add-content", null);
                    ItemsAddContent_Div = createTabPane_Div(ItemsContentSubmenuContent_Div, "items-add-content", ItemsAddTab_li, false);
                    ItemsListTab_li = createNavItem_li(Items_ul, "items-list-tab", false, "List", "items-list-content", null);
                    ItemsListContent_Div = createTabPane_Div(ItemsContentSubmenuContent_Div, "items-list-content", ItemsListTab_li, false);  
                    
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
            Manufacturing_ul = createNavTabs_ul(ManufacturingContent, "manufacturing_ul");
            ManufacturingSubmenuContent_Div = createDivWithClass_Div(ManufacturingContent, "tab-content", "manufacturing-submenu-content");
                ManufacturingAddTab_li = createNavItem_li(Manufacturing_ul, "manufacturing-add-tab", false, "Add", "manufacturing-add-content", onWorkOrderAddSelected);
                ManufacturingAddContent_Div = createTabPane_Div(ManufacturingSubmenuContent_Div, "manufacturing-add-content", ManufacturingAddTab_li, false);
                    ManufacturingAddContent_Div.appendChild(createWOAddForm());
                ManufacturingListTab_li = createNavItem_li(Manufacturing_ul, "manufacturing-list-tab", false, "List", "manufacturing-list-content", onWorkOrderListSelected);
                ManufacturingListContent_Div = createTabPane_Div(ManufacturingSubmenuContent_Div, "manufacturing-list-content", ManufacturingListTab_li, false);
                    ManufacturingListContent_Div.appendChild(createDivElement('item-details', 'id_manufacturing_list_table_div', 'display:block;'));
                ManufacturingEditTab_li = createNavItem_li(Manufacturing_ul, "manufacturing-edit-tab", false, "Edit", "manufacturing-edit-content", onWorkOrderEditSelected);
                ManufacturingEditContent_Div = createTabPane_Div(ManufacturingSubmenuContent_Div, "manufacturing-edit-content", ManufacturingEditTab_li, false);
                    ManufacturingEditContent_Div.appendChild(createWOUpdateForm());

            PurchaseTab_li = createNavItem_li(Tabs_level1_ul, "purchase-tab", false, "Purchase", "purchase-content", null);
            PurchaseContent_Div = createTabPane_Div(Content_leve1_Div, "purchase-content", PurchaseTab_li, false); 
            Purchase_ul = createNavTabs_ul(PurchaseContent_Div, "purchase_ul");   
            PurchaseSubmenuContent_Div = createDivWithClass_Div(PurchaseContent_Div, "tab-content", "purchase-submenu-content");            
                PurchaseOrderAddTab_li = createNavItem_li(Purchase_ul, "purchase-order-add-tab", false, "Add", "purchase-order-add-content", null);
                PurchaseOrderAddContent_Div = createTabPane_Div(PurchaseSubmenuContent_Div, "purchase-order-add-content", PurchaseOrderAddTab_li, false);
                    PurchaseOrderAddContent_Div.appendChild(createDivElement('item-details', 'id_purchaseorder_add_div', 'display:block;'));
                PurchaseOrderListTab_li = createNavItem_li(Purchase_ul, "purchase-order-list-tab", false, "List", "purchase-order-list-content", onPurchaseOrderListSelected, null);
                PurchaseOrderListContent_Div = createTabPane_Div(PurchaseSubmenuContent_Div, "purchase-order-list-content", PurchaseOrderListTab_li, false);
                    PurchaseOrderListContent_Div.appendChild(createDivElement('item-details', 'id_purchaser_list_table_div', 'display:block;'));
                PurchaseOrderEditTab_li = createNavItem_li(Purchase_ul, "purchase-order-edit-tab", false, "Edit", "purchase-order-edit-content", onPurchaseOrderEditSelected, null);
                PurchaseOrderEditContent_Div = createTabPane_Div(PurchaseSubmenuContent_Div, "purchase-order-edit-content", PurchaseOrderEditTab_li, false);
                    PurchaseOrderEditContent_Div.appendChild(createPOUpdateForm());

            SalesTab_li = createNavItem_li(Tabs_level1_ul, "sales-tab", false, "Sales", "sales-content", null);
            SalesContent = createTabPane_Div(Content_leve1_Div, "sales-content", SalesTab_li, false); 

            SupplierTab_li = createNavItem_li(Tabs_level1_ul, "supplier-tab", false, "Suppliers", "supplier-content", null);
            SupplierContent = createTabPane_Div(Content_leve1_Div, "supplier-content", SupplierTab_li, false);
            Supplier_ul = createNavTabs_ul(SupplierContent, "supplier_ul");
            SupplierSubmenuContent_Div = createDivWithClass_Div(SupplierContent, "tab-content", "supplier-submenu-content");
                SupplierAddTab_li = createNavItem_li(Supplier_ul, "supplier-add-tab", false, "Add", "supplier-add-content", onSupplierAddTabSelected);
                SupplierAddContent_Div = createTabPane_Div(SupplierSubmenuContent_Div, "supplier-add-content", SupplierAddTab_li, false);
                    SupplierAddContent_Div.appendChild(createSupplierAddForm());
                SupplierListTab_li = createNavItem_li(Supplier_ul, "supplier-list-tab", false, "List", "supplier-list-content", onSupplierListTabSelected);
                SupplierListContent_Div = createTabPane_Div(SupplierSubmenuContent_Div, "supplier-list-content", SupplierListTab_li, false);
                    SupplierListContent_Div.appendChild(createDivElement('item-details', 'id_supplier_list_table_div', 'display:block;'));
                SupplierEditTab_li = createNavItem_li(Supplier_ul, "supplier-edit-tab", false, "Edit", "supplier-edit-content", onSupplierEditTabSelected);
                SupplierEditContent_Div = createTabPane_Div(SupplierSubmenuContent_Div, "supplier-edit-content", SupplierEditTab_li, false);
                    SupplierEditContent_Div.appendChild(createSupplierEditForm());
                    
            ReportingTab_li = createNavItem_li(Tabs_level1_ul, "reporting-tab", false, "Reporting", "reporting-content", null);
            ReportingContent = createTabPane_Div(Content_leve1_Div, "reporting-content", ReportingTab_li, false);
            Reporting_ul = createNavTabs_ul(ReportingContent, "reporting_ul");
            ReportingSubmenuContent_Div = createDivWithClass_Div(ReportingContent, "tab-content", "reporting-submenu-content");
                ReportingInventoryTab_li = createNavItem_li(Reporting_ul, "reporting-inventory-tab", false, "Inventory", "reporting-inventory-content", null);
                ReportingInventoryContent_Div = createTabPane_Div(ReportingSubmenuContent_Div, "reporting-inventory-content", ReportingInventoryTab_li, false);                
        
        PurchaseOrderContentDiv = createDivWithClass_Div(PurchaseOrdersIMContent_Div, "item-details-2", "id_movement_purchase_order_l1");
    }    

</script>

    <div id="id_tab_list_head" >
    </div>

    <div id="id_purchase_order_list" class="item-details" style="display:none;">
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
