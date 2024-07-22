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
    
</style>

<script src="libs/LiveSearch.js"></script>
<script src="po/po_helper.js"></script>
<script>
    async function updateStatusList(select_id, selected_idx) {
        let url = "po/po_get_po_status.php";
        try {
            const response = await fetch(url);
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            const data = await response.json(); // Assuming the PHP returns a JSON response
            console.log(data);

            // Populate the select element with the response data
            const selectElement = document.getElementById(select_id);
            for (const [value, text] of Object.entries(data)) {
                const option = document.createElement('option');
                option.value = value;
                option.text = text;
                selectElement.appendChild(option);
            }
            selectElement.selectedIndex = selected_idx;
        } catch (error) {
            console.error('Error fetching the PHP response:', error);
        }
    }

    // function liveSerch_Configure_for_PO() {
    //     LiveSearch_PO = new LiveSearch('LiveSearch_PO','searchInput-po', 'livesearch-po', 'po/livesearch_po_open.php', 'suggestion-item', function(value){
    //         alert(value);            
    //         document.getElementById('searchInput-product').value = value;
    //     });
    //     document.getElementById('searchInput-product').addEventListener("keydown", function(event) {
    //         LiveSearch_PO.navigateSuggestions(event);
    //     });
    //     document.getElementById('searchInput-product').addEventListener("keyup", function() {
    //         LiveSearch_PO.showResult(this.value);
    //     });
    // }

    function liverSearch_Confiure_for_Supplier() {
        LiveSearch_Supplier = new LiveSearch('LiveSearch_Supplier','searchInput-supplier', 'livesearch-supplier', 'supplier/livesearch_supplier.php', 'suggestion-item', function(value){
            // alert(value);            
            document.getElementById('searchInput-supplier').value = value;
            fetchSupplierDetails(value, 'supplier_id');
        });
        document.getElementById('searchInput-supplier').addEventListener("keydown", function(event) {
            LiveSearch_Supplier.navigateSuggestions(event);
        });
        document.getElementById('searchInput-supplier').addEventListener("keyup", function() {
            LiveSearch_Supplier.showResult(this.value);
        });
    }

    
    function liverSearch_Confiure_for_Item() {
        LiveSearch_Items = new LiveSearch('LiveSearch_Items','searchInput-item', 'livesearch-item', 'item/livesearch_item.php', 'suggestion-item', function(value){
            // alert(value);            
            document.getElementById('searchInput-item').value = "";
            fetch_item_details(value);
        });
        document.getElementById('searchInput-item').addEventListener("keydown", function(event) {
            LiveSearch_Items.navigateSuggestions(event);
        });
        document.getElementById('searchInput-item').addEventListener("keyup", function() {
            LiveSearch_Items.showResult(this.value);
        });
    }

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
<h3>Add-Update Purchase Order</h3>
<br>
<h5>Select PO for Update</h5>
<div class="section_line"></div>
<br>
<div id="itemDetails-2" class="item-details" style="display:block;">
    <!-- <div class="row">
        <form autocomplete="off">
            <div class="row">
                <label for="searchInput-product">PO Search :</label>
                <input type="text"   id="searchInput-product" placeholder="Search for POs...">
                <div id="livesearch-product"></div>        
            </div>
        </form>
    </div> -->
    <!-- <div class="row">
        <form autocomplete="off">
            <div class="row">
                <label for="searchInput-po-open">PO Search :</label>
                <input type="text"   id="searchInput-po-open" placeholder="Search for POs...">
                <div id="livesearch-po-open"></div>        
            </div>
        </form>
    </div> -->

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
                <button id="updateButton" class="btn btn-primary mr-2">Update Item</button>
                <button id="addItemButton" class="btn btn-secondary">Add New Item</button>
            </div>
        </div>
    </div>

    <!-- <div class="row">
        <div class="col-6">
            <div class="row">
                <label for="po_code">PO Code:</label>
                <input type="text" id="po_code" name="po_code" maxlength="15" value="Automatically Generated" readonly >
            </div> -->
            <!-- <div class="row">
                <label for="po_name">PO Name:</label>
                <input type="text"   id="po_name" name="po_name" required >
            </div> -->
            <!-- <div class="row">
                <label for="order_date_id">Order Date:</label>
                <input type="date" id="order_date_id" name="order_date">
            </div>
            <div class="row">
                <label for="delivery_date_id">Dilivery Date:</label>
                <input type="date" id="delivery_date_id" name="delivery_date">
            </div> -->
            <!--             
            <div class="row">
                <label for="status_id">Status:</label>
                <select id="status_id"  name="status" required></select>
            </div> -->
            <!-- <div class="row">
                <label for="status_id">PO Staus:</label>
                <input type="text" id="status_id" name="status_id" readonly value="Open">
            </div> -->
            <!-- <div class="row">
                <label for="supplier_name_id">Supplier :</label>
                <select id="supplier_name_id"  name="supplier_name_id" required></select>
            </div> -->

            <!-- <form autocomplete="off">
                <div class="row">
                    <label for="searchInput-supplier">Supplier :</label>
                    <input type="text"   id="searchInput-supplier" placeholder="Search for Supplier">
                    <div id="livesearch-supplier"></div>        
                </div>
            </form>
            <div class="row">
                <label for="supplier_id">Supplier ID:</label>
                <input type="text" id="supplier_id" name="supplier_id" readonly>
            </div>

            <div class="row">
                <label for="description">PO Note:</label>
                <textarea id="description"  name="description" rows="4" ></textarea>
            </div> -->
            <!-- 
            <div class="row">
                <label for="cost">Cost:</label>
                <input type="number" step="0.01"   id="cost" name="cost" value="0.00">
            </div>
            <div class="row">
                <label for="reorder_level">Reorder Level:</label>
                <input type="number"   id="reorder_level" name="reorder_level" value="0">
            </div>
            <div class="row">
                <label for="supplier_id">Supplier ID:</label>
                <select id="supplier_id"  name="supplier_id" required></select>
            </div> -->
            <!-- <br>
        </div>
    </div> -->
    <!-- <div class="row">
        <div class="col-12 text-right">
            <button id="updateButton" class="btn btn-primary mr-2">Update Item</button>
            <button id="addItemButton" class="btn btn-secondary">Add New Item</button>
        </div>
    </div> -->
</div>
<br>
<h5>PO Item List</h5>
<div class="section_line"></div>
<br>
<div class="container">
    <div class="row">
        <div class="col-md-9">
            <div class="item-details" style="display:block;">
                <div class="row" id="id_div_po_table" style="display:block;">
                    <div class="col-6">
                        <div id="id_div_bom_table"></div>
                    </div>
                </div>
            </div>
            <div class="col-6">
                <button id="savePurchaseOrder" onclick="onClick_addItemToPO()" class="btn btn-secondary">Save PO</button>
            </div>
        </div>
    </div>
</div>
<br>
<h5>Select Item</h5>
<div class="section_line"></div>
<br>
<div class="container">
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
                    <div id="id_item_details">
                    </div>
                </div>
                <div class="col-9">
                    <button id="addItemToPO" onclick="onClick_addItemToPO()" class="btn btn-secondary">Add to PO</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- <script src="libs/LiveSearch_PO.js"> -->

</script>
<!-- <script src="po/po_helper.js"></script> -->
<!-- <script src="libs/LiveSearch_PO.js"></script> -->

<?php include 'ui/footer.php'; ?>
