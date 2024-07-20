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
    <title>Item Search and Add</title>
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

        .item-details input {
            margin-bottom: 10px;
            width: 300px;
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

    document.addEventListener("DOMContentLoaded", function() {
        document.getElementById("searchInput").addEventListener("keydown", navigateSuggestions);
        document.getElementById("searchInput").addEventListener("keyup", function() {
            showResult(this.value);
        });

        fetch('db_category.php')
            .then(response => response.json())
            .then(categories => {
                var categorySelect = document.getElementById('category_id');
                categories.forEach(category => {
                    var option = document.createElement('option');
                    option.value = category.CategoryID;
                    option.text = category.CategoryType;
                    categorySelect.add(option);
                });
            });

        fetch('db_supplier.php')
            .then(response => response.json())
            .then(suppliers => {
                var supplierSelect = document.getElementById('supplier_id');
                suppliers.forEach(supplier => {
                    var option = document.createElement('option');
                    option.value = supplier.supplier_id;
                    option.text = supplier.supplier_name;
                    supplierSelect.add(option);
                });
            });
    });

    function addItem() {
        var formData = new FormData(document.getElementById('addItemForm'));
        var xhr = new XMLHttpRequest();
        xhr.open("POST", "db_item.php", true);
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4 && xhr.status === 200) {
                alert('Item added successfully');
                document.getElementById('addItemForm').reset();
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
            }
        };
        xhr.send(formData);
    }
    </script>
</head>
<body>

<h1>Item Search and Add</h1>

<form autocomplete="off">
    <input type="text" size="30" id="searchInput" placeholder="Search for items...">
    <div id="livesearch"></div>
</form>

<div id="itemDetails" class="item-details" style="display:none;">
    <h2>Selected Item Details</h2>
    <form id="addItemForm" onsubmit="event.preventDefault(); updateItem();">
        <input type="hidden" id="item_id" name="item_id">
        <label for="item_code">Item Code:</label><br>
        <input type="text" id="item_code" name="item_code" maxlength="15" required><br>
        <label for="name">Item Name:</label><br>
        <input type="text" id="name" name="name" required><br>
        <label for="category_id">Category ID:</label><br>
        <select id="category_id" name="category_id" required></select><br>
        <label for="description">Description:</label><br>
        <input type="text" id="description" name="description"><br>
        <label for="price">Price:</label><br>
        <input type="number" step="0.01" id="price" name="price" value="0.00"><br>
        <label for="cost">Cost:</label><br>
        <input type="number" step="0.01" id="cost" name="cost" value="0.00"><br>
        <label for="reorder_level">Reorder Level:</label><br>
        <input type="number" id="reorder_level" name="reorder_level" value="0"><br>
        <label for="supplier_id">Supplier ID:</label><br>
        <select id="supplier_id" name="supplier_id" required></select><br><br>
        <input type="submit" value="Update Item">
    </form>
</div>

<h2>Add New Item</h2>
<form id="addItemForm" onsubmit="event.preventDefault(); addItem();">
    <label for="item_code">Item Code:</label><br>
    <input type="text" id="item_code" name="item_code" maxlength="15" required><br>
    <label for="name">Item Name:</label><br>
    <input type="text" id="name" name="name" required><br>
    <label for="category_id">Category ID:</label><br>
    <select id="category_id" name="category_id" required></select><br>
    <label for="description">Description:</label><br>
    <input type="text" id="description" name="description"><br>
    <label for="price">Price:</label><br>
    <input type="number" step="0.01" id="price" name="price" value="0.00"><br>
    <label for="cost">Cost:</label><br>
    <input type="number" step="0.01" id="cost" name="cost" value="0.00"><br>
    <label for="reorder_level">Reorder Level:</label><br>
    <input type="number" id="reorder_level" name="reorder_level" value="0"><br>
    <label for="supplier_id">Supplier ID:</label><br>
    <select id="supplier_id" name="supplier_id" required></select><br><br>
    <input type="submit" value="Add Item">
</form>

<p><a href="logout.php">Logout</a></p>
<p><a href="index.php">Back to Home</a></p>

</body>
</html>
