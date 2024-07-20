<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Item Search and Add/Update</title>
    <style>
        .suggestion-item {
            cursor: pointer;
            padding: 5px;
        }

        .suggestion-item:hover, .suggestion-item.selected {
            background-color: #ddd;
        }

        .item-details {
            margin-bottom: 20px;
        }

        .item-details label {
            display: inline-block;
            width: 150px;
        }

        .item-details-2 label {
            display: inline-block;
            width: 150px;
        }

        .item-details input, .item-details select, .item-details textarea {
            margin-bottom: 10px;
            width: 300px;
        }
        #preview {
            max-width: 150px;
            max-height: 150px;
            display: none;
        }
        #preview_new {
            max-width: 150px;
            max-height: 150px;
            display: none;
        }
    </style>
    <script>
    var currentFocus = -1;
    var previousValue = "";

    function showResult(str) {
        if (str.length == 0) {
            document.getElementById("livesearch").innerHTML = "";
            document.getElementById("livesearch").style.border = "0px";
            previousValue = "";
            return;
        }
        if (str == previousValue) {
            return;
        }
        previousValue = str;

        var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                document.getElementById("livesearch").innerHTML = this.responseText;
                document.getElementById("livesearch").style.border = "1px solid #A5ACB2";
                currentFocus = -1; // Reset focus when new results are loaded
            }
        }
        xmlhttp.open("GET", "livesearch_item.php?q=" + str, true);
        xmlhttp.send();
    }

    function selectSuggestion(value) {
        document.getElementById("searchInput").value = value;
        document.getElementById("livesearch").innerHTML = "";
        document.getElementById("livesearch").style.border = "0px";
        previousValue = value; // Update previousValue to prevent unnecessary AJAX calls
        fetchItemDetails(value);
    }

    function fetchItemDetails(itemName) {
        var xhr = new XMLHttpRequest();
        xhr.open("GET", "db_item_fetch.php?name=" + itemName, true);
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4 && xhr.status === 200) {
                var item = JSON.parse(xhr.responseText);
                document.getElementById('item_id').value = item.item_id;
                document.getElementById('item_code').value = item.item_code;
                document.getElementById('name').value = item.name;
                document.getElementById('description').value = item.description;
                document.getElementById('category_id').value = item.category_id;
                document.getElementById('price').value = item.price;
                document.getElementById('cost').value = item.cost;
                document.getElementById('reorder_level').value = item.reorder_level;
                document.getElementById('supplier_id').value = item.supplier_id;
                document.getElementById('photo_url').value = item.photo_url || '';
                document.getElementById('itemDetails').style.display = 'block';
            }
        };
        xhr.send();
    }

    function navigateSuggestions(e) {
        var suggestionBox = document.getElementById("livesearch");
        var items = suggestionBox.getElementsByClassName("suggestion-item");
        if (e.keyCode == 40) { // Down arrow
            currentFocus++;
            addActive(items);
        } else if (e.keyCode == 38) { // Up arrow
            currentFocus--;
            addActive(items);
        } else if (e.keyCode == 13) { // Enter
            e.preventDefault();
            if (currentFocus > -1 && items.length > 0) {
                items[currentFocus].click();
            }
        }
    }

    function addActive(items) {
        if (!items) return false;
        removeActive(items);
        if (currentFocus >= items.length) currentFocus = 0;
        if (currentFocus < 0) currentFocus = items.length - 1;
        items[currentFocus].classList.add("selected");
    }

    function removeActive(items) {
        for (var i = 0; i < items.length; i++) {
            items[i].classList.remove("selected");
        }
    }

    function loadCategories() {
        fetch('db_category.php')
            .then(response => response.json())
            .then(categories => {
                var categorySelect = document.getElementById('category_id');
                categorySelect.innerHTML = ""; // Clear existing options
                categories.forEach(category => {
                    var option = document.createElement('option');
                    option.value = category.CategoryID;
                    option.text = category.CategoryType;
                    categorySelect.add(option);
                });
            })
            .catch(error => console.error('Error loading categories:', error));
    }

    function loadSuppliers() {
        fetch('db_supplier.php')
            .then(response => response.json())
            .then(suppliers => {
                var supplierSelect = document.getElementById('supplier_id');
                supplierSelect.innerHTML = ""; // Clear existing options
                suppliers.forEach(supplier => {
                    var option = document.createElement('option');
                    option.value = supplier.supplier_id;
                    option.text = supplier.supplier_name;
                    supplierSelect.add(option);
                });
            })
            .catch(error => console.error('Error loading suppliers:', error));
    }

    function previewFile() {
        const preview = document.getElementById('preview');
        const file = document.getElementById('file').files[0];
        const reader = new FileReader();

        reader.addEventListener('load', function() {
            // Convert the file to base64 string
            preview.src = reader.result;
            preview.style.display = 'block';
        }, false);

        if (file) {
            reader.readAsDataURL(file);
        }
    }

    function previewFile_new() {
        const preview = document.getElementById('preview_new');
        const file = document.getElementById('file_new').files[0];
        const reader = new FileReader();

        reader.addEventListener('load', function() {
            // Convert the file to base64 string
            preview.src = reader.result;
            preview.style.display = 'block';
        }, false);

        if (file) {
            reader.readAsDataURL(file);
        }
    }

    document.addEventListener("DOMContentLoaded", function() {
        document.getElementById("searchInput").addEventListener("keydown", navigateSuggestions);
        document.getElementById("searchInput").addEventListener("keyup", function() {
            showResult(this.value);
        });

        loadCategories();
        loadSuppliers();
    });

    function addItem() {
        var formData = new FormData(document.getElementById('addItemForm'));
        var xhr = new XMLHttpRequest();
        xhr.open("POST", "db_item.php", true);
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4 && xhr.status === 200) {
                alert('Item added successfully');
                document.getElementById('addItemForm').reset();
                loadCategories(); // Reload categories
                loadSuppliers(); // Reload suppliers
            }
        };
        xhr.send(formData);
    }

    function updateItem() {
        var formData = new FormData(document.getElementById('addItemForm'));
        formData.append('update', true);
        var xhr = new XMLHttpRequest();
        xhr.open("POST", "db_item.php", true);
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4 && xhr.status === 200) {
                alert('Item updated successfully');
                document.getElementById('addItemForm').reset();
                document.getElementById('itemDetails').style.display = 'none';
                loadCategories(); // Reload categories
                loadSuppliers(); // Reload suppliers
            }
        };
        xhr.send(formData);
    }

    function handleSubmit(event) {
        event.preventDefault();
        var form = event.target;
        var action = form.dataset.action;
        var formData = new FormData(form);

        if (action === 'update') {
            formData.append('update', true);
        }

        var xhr = new XMLHttpRequest();
        xhr.open("POST", "db_item.php", true);
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4 && xhr.status === 200) {
                alert(action.charAt(0).toUpperCase() + action.slice(1) + ' successful');
                form.reset();
                loadCategories();
                loadSuppliers();
                if (action === 'update') {
                    document.getElementById('itemDetails').style.display = 'none';
                }
            }
        };
        xhr.send(formData);
    }

    function loadCategories() {
        fetch('db_category.php')
            .then(response => response.json())
            .then(categories => {
                populateSelect('category_id', categories);
                populateSelect('category_id_new', categories);
            })
            .catch(error => console.error('Error loading categories:', error));
    }

    function loadSuppliers() {
        fetch('db_supplier.php')
            .then(response => response.json())
            .then(suppliers => {
                populateSelect('supplier_id', suppliers);
                populateSelect('supplier_id_new', suppliers);
            })
            .catch(error => console.error('Error loading suppliers:', error));
    }

    function populateSelect(selectId, items) {
        var select = document.getElementById(selectId);
        select.innerHTML = ''; // Clear existing options
        items.forEach(item => {
            var option = document.createElement('option');
            option.value = item.CategoryID || item.supplier_id;
            option.text = item.CategoryType || item.supplier_name;
            select.add(option);
        });
    }

    document.addEventListener("DOMContentLoaded", function() {
        loadCategories();
        loadSuppliers();
    });
</script>
</head>
<body>

<h1>Item Search and Add/Update</h1>

<form autocomplete="off">
    <input type="text" size="30" id="searchInput" placeholder="Search for items...">
    <div id="livesearch"></div>
</form>

<div id="itemDetails" class="item-details" style="display:none;">
    <h2>Selected Item Details</h2>
    <form id="addItemForm" data-action="update" onsubmit="handleSubmit(event);">
        <input type="hidden" id="item_id" name="item_id">
        <label for="item_code">Item Code:</label>
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
        <label for="photo_url">Photo URL:</label>
        <input type="text" id="photo_url" name="photo_url"><br><br>
        <input type="file" name="file" id="file" onchange="previewFile()"><br><br>
        <img id="preview" src="#" alt="Image preview"><br><br>
        <input type="submit" value="Update Item">
    </form>
</div>

<div id="itemDetails-2" class="item-details" style="display:block;">
<h2>Add New Item</h2>
<form id="addItemFormNew" data-action="add" onsubmit="handleSubmit(event);">
    <label for="item_code_new">Item Code:</label>
    <input type="text" id="item_code_new" name="item_code" maxlength="15" required><br>
    <label for="name_new">Item Name:</label>
    <input type="text" id="name_new" name="name" required><br>
    <label for="category_id_new">Category ID:</label>
    <select id="category_id_new" name="category_id" required></select><br>
    <label for="description_new">Description:</label>
    <textarea id="description_new" name="description" rows="4" cols="50"></textarea><br>
    <label for="price_new">Price:</label>
    <input type="number" step="0.01" id="price_new" name="price" value="0.00"><br>
    <label for="cost_new">Cost:</label>
    <input type="number" step="0.01" id="cost_new" name="cost" value="0.00"><br>
    <label for="reorder_level_new">Reorder Level:</label>
    <input type="number" id="reorder_level_new" name="reorder_level" value="0"><br>
    <label for="supplier_id_new">Supplier ID:</label>
    <select id="supplier_id_new" name="supplier_id" required></select><br>
    <label for="photo_url_new">Photo URL:</label>
    <input type="text" id="photo_url_new" name="photo_url"><br><br>
    <input type="file" name="file" id="file_new" onchange="previewFile_new()"><br><br>
    <img id="preview_new" src="#" alt="Image preview"><br><br>
    <input type="submit" value="Add Item">
</form>
</div>

<p><a href="logout.php">Logout</a></p>
<p><a href="index.php">Back to Home</a></p>

<script>
    function handleSubmit(event) {
        event.preventDefault();
        var form = event.target;
        var action = form.dataset.action;
        var formData = new FormData(form);

        if (action === 'update') {
            formData.append('update', true);
        }

        var xhr = new XMLHttpRequest();
        xhr.open("POST", "db_item.php", true);
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4 && xhr.status === 200) {
                alert(action.charAt(0).toUpperCase() + action.slice(1) + ' successful');
                form.reset();
                loadCategories();
                loadSuppliers();
                if (action === 'update') {
                    document.getElementById('itemDetails').style.display = 'none';
                }
            }
        };
        xhr.send(formData);
    }

    function loadCategories() {
        fetch('db_category.php')
            .then(response => response.json())
            .then(categories => {
                populateSelect('category_id', categories);
                populateSelect('category_id_new', categories);
            })
            .catch(error => console.error('Error loading categories:', error));
    }

    function loadSuppliers() {
        fetch('db_supplier.php')
            .then(response => response.json())
            .then(suppliers => {
                populateSelect('supplier_id', suppliers);
                populateSelect('supplier_id_new', suppliers);
            })
            .catch(error => console.error('Error loading suppliers:', error));
    }

    function populateSelect(selectId, items) {
        var select = document.getElementById(selectId);
        select.innerHTML = ''; // Clear existing options
        items.forEach(item => {
            var option = document.createElement('option');
            option.value = item.CategoryID || item.supplier_id;
            option.text = item.CategoryType || item.supplier_name;
            select.add(option);
        });
    }

    document.addEventListener("DOMContentLoaded", function() {
        loadCategories();
        loadSuppliers();
    });
</script>

</body>
</html>
