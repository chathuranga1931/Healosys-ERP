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

<script>
document.addEventListener("DOMContentLoaded", function() {
    load_page_title("Bill of Material Management");
});
</script>
<!-- <div class="section_line"></div> -->
<h5>Select Product for Bom</h5>
<!-- <div class="section_line"></div> -->
<div id="itemDetails-2" class="item-details" style="display:block;">
    <div class="row">
        <form autocomplete="off">
            <div class="row">
                <label for="searchInput-product">Product Search :</label>
                <input type="text"   id="searchInput-product" placeholder="Search for Products...">
                <div id="livesearch-product"></div>        
            </div>
        </form>
    </div>
    <div class="row">
        <div class="col-6">
            <div class="row">
                <label for="item_code">Item Code:</label>
                <input type="text" id="item_code" name="item_code" maxlength="15" required readonly>
            </div>
            <!-- <div class="row">
                <label for="category_id">Category ID:</label>
                <select id="category_id"  name="category_id" required readonly></select> -->
                <!-- <input type="text" id="category_id" name="category_id" required readonly> -->
            <!-- </div> -->
            <div class="row">
                <label for="name">Item Name:</label>
                <input type="text"   id="name" name="name" required readonly>
            </div>
            <div class="row">
                <label for="description">Description:</label>
                <textarea id="description"  name="description" rows="4" readonly></textarea>
            </div>
            <!-- <div class="row">
                <label for="price">Price:</label>
                <input type="number" step="0.01"   id="price" name="price" value="0.00">
            </div>
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
            <br>
        </div>

        <div class="col-6">
            <div class="col-6">
                <!-- <label for="itemImage">Image:</label>
                <input type="file" class="form-control-file" name="itemImage"  id="id_ItemImage" onchange="previewFile()"> -->
                <img id="preview" src="#" alt="Image preview" class="img-fluid mt-2">
            </div>
        </div>
    </div>
    <!-- <div class="row">
        <div class="col-12 text-right">
            <button id="updateButton" class="btn btn-primary mr-2">Update Item</button>
            <button id="addItemButton" class="btn btn-secondary">Add New Item</button>
        </div>
    </div> -->
</div>

<!-- <br> -->

<h5>Bill of Materials</h5>
<!-- <br><div class="section_line"></div> -->
<div class="item-details" style="display:block;">
    <div class="row" id="id_bom_table_raw" style="display:block;">
        <div class="col-6">
            <div id="id_div_bom_table">
            </div>
        </div>
        <div class="col-6">
            <button id="saveBom" class="btn btn-secondary">Save BoM</button>
        </div>
    </div>
</div>
<h5>Select Item</h5>
<!-- <div class="section_line"></div> -->
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

    <div class="row" id="id_product_details_raw" style="display:none;">
        <div class="col-6">
            <div id="id_product_details">
            </div>
        </div>
        <div class="col-6">
            <button id="addItemButton" class="btn btn-secondary">Add to Bom</button>
        </div>
    </div>
</div>

<br>

<script src="bom/bom_helper.js"></script>

<?php include 'ui/footer.php'; ?>
