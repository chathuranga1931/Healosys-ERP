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

<div id="id_page_title"> </div> 
<br>
<div class="section_line"></div>
<br>

<div class="row">
    <div id="id_div_item_list">
        
    </div>
</div>

<div id="itemDetails-2" class="item-details" style="display:none;">
    <form autocomplete="off">
        <div class="row">
            <label for="searchInput">Item Search :</label>
            <input type="text"   id="searchInput" placeholder="Search for items...">
            <div id="livesearch"></div>
        </div>
    </form>

    <br>

    <div class="row">
        <div class="col-6">
            <div class="row">
                <label for="item_code">Item Code:</label>
                <input type="text" id="item_code" name="item_code" maxlength="15" required>
            </div>
            <div class="row">
                <label for="category_id">Category ID:</label>
                <select id="category_id"  name="category_id" required></select>
            </div>
            <div class="row">
                <label for="name">Item Name:</label>
                <input type="text"   id="name" name="name" required>
            </div>
            <div class="row">
                <label for="description">Description:</label>
                <textarea id="description"  name="description" rows="4"></textarea>
            </div>
            <div class="row">
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
            </div>
            <br>
        </div>

        <div class="col-6">
            <div class="col-6">
                <label for="itemImage">Image:</label>
                <input type="file" class="form-control-file" name="itemImage"  id="id_ItemImage" onchange="previewFile()">
                <img id="preview" src="#" alt="Image preview" class="img-fluid mt-2">
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12 text-right">
            <button id="updateButton" class="btn btn-primary mr-2">Update Item</button>
            <button id="addItemButton" class="btn btn-secondary">Add New Item</button>
        </div>
    </div>

    <!-- <form autocomplete="off">
        <label for="searchInput">Item Search :</label>
        <input type="text" size="30" id="searchInput" placeholder="Search for items...">
        <div id="livesearch"></div>
    </form> -->

    <!-- <label for="item_code">Item Code:</label>
    <input type="text" id="item_code" name="item_code" maxlength="15" required><br>
    <label for="name">Item Name:</label>
    <input type="text" id="name" name="name" required><br>
    <label for="category_id">Category ID:</label>
    <select id="category_id" name="category_id" required></select><br>
    <label for="description">Description:</label>
    <textarea id="description" name="description" rows="4" cols="50"></textarea><br>
    <label for="price">Price:</label>
    <input type="number" step="0.01" id="price" name="price" value="0.00"><br>
    <label for="cost">Cost:</label>
    <input type="number" step="0.01" id="cost" name="cost" value="0.00"><br>
    <label for="reorder_level">Reorder Level:</label>
    <input type="number" id="reorder_level" name="reorder_level" value="0"><br>
    <label for="supplier_id">Supplier ID:</label>
    <select id="supplier_id" name="supplier_id" required></select><br>
    <input type="file" name="itemImage" id="id_ItemImage" onchange="previewFile()"><br><br>
    <img id="preview" src="#" alt="Image preview"><br>
    <button id="updateButton">Update Item</button>
    <button id="addItemButton">Add New Item</button> -->
</div>
<script>
    function on_load(){
        load_page_title('Item List');
    }
</script>

<script src="libs/Common.js"></script>
<script src="item/item_list_helper.js"></script>

<?php include 'ui/footer.php'; ?>
