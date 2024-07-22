
    var currentFocus = -1;
    var previousValue = "";

    function showResult(str, type) {
        if (str.length == 0) {
            document.getElementById("livesearch-" + type).innerHTML = "";
            document.getElementById("livesearch-" + type).style.border = "0px";
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
                document.getElementById("livesearch-" + type).innerHTML = this.responseText;
                document.getElementById("livesearch-" + type).style.border = "1px solid #A5ACB2";
                currentFocus = -1; // Reset focus when new results are loaded
            }
        }
        if(type == "product") {
            xmlhttp.open("GET", "item/livesearch_item_product.php?q=" + str, true);
        }
        else{
            xmlhttp.open("GET", "item/livesearch_item.php?q=" + str, true);
        }
        
        xmlhttp.send();
    }

    function selectSuggestion_product(value) {
        document.getElementById("searchInput-product").value = "";
        document.getElementById("livesearch-product").innerHTML = "";
        document.getElementById("livesearch-product").style.border = "0px";
        previousValue = value; // Update previousValue to prevent unnecessary AJAX calls        
        fetch_product_details(value);
    }

    function selectSuggestion_item(value) {
        document.getElementById("searchInput-item").value = "";
        document.getElementById("livesearch-item").innerHTML = "";
        document.getElementById("livesearch-item").style.border = "0px";
        previousValue = value; // Update previousValue to prevent unnecessary AJAX calls
        fetch_item_details(value);
    }

    // Function to fetch and update the image
    function updateImage(imageId) {
        fetch('item/get_item_image.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `image_id=${imageId}`
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok ' + response.statusText);
            }
            return response.blob();
        })
        .then(blob => {
            // Create a local URL for the image blob
            const imageUrl = URL.createObjectURL(blob);
            // Update the image element's src attribute
            const preview = document.getElementById('preview')
            preview.src = imageUrl;            
            preview.style.display = 'block';
        })
        .catch(error => console.error('Error fetching image:', error));
    }

    function fetch_product_details(itemName) {
        var xhr = new XMLHttpRequest();
        xhr.open("GET", "item/db_item_fetch.php?name=" + itemName, true);
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4 && xhr.status === 200) {
                var item = JSON.parse(xhr.responseText); 

                // document.getElementById('item_id').value = item.item_id;
                document.getElementById('item_code').value = item.item_code;
                document.getElementById('name').value = item.name;
                document.getElementById('description').value = item.description;
                // document.getElementById('category_id').value = item.category_id;
                // document.getElementById('price').value = item.price;
                // document.getElementById('cost').value = item.cost;
                // document.getElementById('reorder_level').value = item.reorder_level;
                // document.getElementById('supplier_id').value = item.supplier_id;

                updateImage(item.item_code);

                fetchJsonFile(item.item_code);
            }
        };
        xhr.send();
    }

    function fetch_item_details(itemName) {
        var xhr = new XMLHttpRequest();
        xhr.open("GET", "item/db_item_fetch.php?name=" + itemName, true);
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4 && xhr.status === 200) {
                var item = JSON.parse(xhr.responseText); 

                // document.getElementById('item_id').value = item.item_id;
                // document.getElementById('item_code').value = item.item_code;
                // document.getElementById('name').value = item.name;
                // document.getElementById('description').value = item.description;
                // // document.getElementById('category_id').value = item.category_id;
                // // document.getElementById('price').value = item.price;
                // // document.getElementById('cost').value = item.cost;
                // // document.getElementById('reorder_level').value = item.reorder_level;
                // // document.getElementById('supplier_id').value = item.supplier_id;

                // updateImage(item.item_code);

                // Create a table element with Bootstrap classes
                var table = document.createElement('table');
                table.className = 'table table-bordered';

                // Define the table headers
                var headers = ['Item-Code', 'Name', 'Description', 'Qty'];
                var thead = document.createElement('thead');
                var headerRow = document.createElement('tr');
                headers.forEach(function(header) {
                    var th = document.createElement('th');
                    th.textContent = header;
                    headerRow.appendChild(th);
                });
                thead.appendChild(headerRow);
                table.appendChild(thead);

                // Create a row for the item data
                var tbody = document.createElement('tbody');
                var dataRow = document.createElement('tr');
                var data = [
                    item.item_code,
                    item.name,
                    item.description
                ];
                data.forEach(function(value) {
                    var td = document.createElement('td');

                    if(value === item.item_code){
                        td.id = 'id_item_code_selected';   
                    }

                    td.textContent = value;
                    dataRow.appendChild(td);
                });
                
                var tdQuantity = document.createElement('td');
                tdQuantity.id = 'id_quantity_of_item_selected_selected';
                tdQuantity.contentEditable = true;  // Make the cell editable
                tdQuantity.textContent = 1.0;

                // id_quantity_of_item_selected_selected
                // var textElement = document.createElement('text');
                // textElement.id = 'id_quantity_of_item_selected_selected';
                // textElement.textContent = 1.0
                // tdQuantity.appendChild(textElement);
                dataRow.appendChild(tdQuantity);

                tbody.appendChild(dataRow);
                table.appendChild(tbody);

                // Get the div element
                var productDetailsDiv = document.getElementById('id_product_details');                

                // Remove all child elements from the div
                while (productDetailsDiv.firstChild) {
                    productDetailsDiv.removeChild(productDetailsDiv.firstChild);
                }

                var id_product_details_raw = document.getElementById('id_product_details_raw');
                id_product_details_raw.style.display = 'block';

                // Append the table to the div with id 'id_product_details'
                document.getElementById('id_product_details').appendChild(table);
            }
        };
        xhr.send();
    }

    function navigateSuggestions(e, type) {
        var suggestionBox = document.getElementById("livesearch-" + type);
        var items = suggestionBox.getElementsByClassName("suggestion-"+ type);
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

    function previewFile() {
        const preview = document.getElementById('preview');
        const file = document.getElementById('id_ItemImage').files[0];
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
        document.getElementById("searchInput-product").addEventListener("keydown", function(event) {
            navigateSuggestions(event, "product");
        });
        document.getElementById("searchInput-product").addEventListener("keyup", function() {
            showResult(this.value, "product");
        });

        document.getElementById("searchInput-item").addEventListener("keydown", function(event) {
            navigateSuggestions(event, "item");
        });
        document.getElementById("searchInput-item").addEventListener("keyup", function() {
            showResult(this.value, "item");
        });
    });

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

    function bom_remove_raw(uniqueButtonId){
        // alert("Removing item from BOM " + uniqueButtonId);
        var raw = document.getElementById(uniqueButtonId);
        raw.remove();

    }

    function add_item_to_bom(item_code_selected, quantity_of_item_selected){
        
        var new_qty = Number(quantity_of_item_selected);
        if(new_qty < 0 ){
            // alert("Please enter a valid quantity");
            show_status("Please enter a valid quantity", 3, "ERROR");
            return;
        }

        var xhr = new XMLHttpRequest();
        xhr.open("GET", "item/db_item_fetch_by_item_code.php?itemcode=" + item_code_selected, true);
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4 && xhr.status === 200) {
                var item = JSON.parse(xhr.responseText); 

                var headers = ['Item-Code', 'Name', 'Description', "Qty", "Remove"];                    
                var bom_table_element = document.getElementById('id_bom_table'); 
                if(bom_table_element == null){
                    bom_table_element = document.createElement('table');
                    bom_table_element.id = 'id_bom_table';

                    bom_table_element.className = 'table table-bordered';

                    // Define the table headers
                    var thead = document.createElement('thead');
                    var headerRow = document.createElement('tr');
                    headers.forEach(function(header) {
                        var th = document.createElement('th');
                        th.textContent = header;
                        headerRow.appendChild(th);
                    });
                    thead.appendChild(headerRow);
                    bom_table_element.appendChild(thead);

                    var div_bom_table = document.getElementById('id_div_bom_table');
                    div_bom_table.appendChild(bom_table_element);
                }

                var div_for_qty = "bom_table_id_Qty_" + item.item_code;
                var qty = 0;
                var available_qty_element = document.getElementById(div_for_qty);
                if(available_qty_element){
                    var tmp = available_qty_element.textContent;
                    qty = Number(tmp);
                    qty += new_qty;
                    available_qty_element.textContent = qty;
                    return;
                }
                qty += new_qty;               
               
                // Create a table element with Bootstrap classes
                // Create a row for the item data
                var tbody = document.createElement('tbody');
                var dataRow = document.createElement('tr');
                dataRow.id = 'bom_tablerow_' + item.item_code;

                var idx = 0;
                var data = [
                    item.item_code,
                    item.name,
                    item.description,
                ];
                data.forEach(function(value) {
                    var td = document.createElement('td');
                    td.textContent = value;
                    td.id = "bom_table_id_" + headers[idx] + "_" + item.item_code;
                    dataRow.appendChild(td);
                    idx++;
                });
                
                var td = document.createElement('td');
                td.textContent = qty;
                td.id = "bom_table_id_" + headers[idx] + "_" + item.item_code;
                td.contentEditable = true;  // Make the cell editable
                dataRow.appendChild(td);
                
                var tdRemoveButton = document.createElement('td');
                var removeButton = document.createElement('button');
                // var uniqueButtonId = 'id-remove-button-' + item.item_code; // Unique ID for the button
                // removeButton.id = uniqueButtonId;
                removeButton.textContent = 'Remove';
                removeButton.className = 'btn btn-danger';
                removeButton.onclick = function() {
                    bom_remove_raw(dataRow.id);
                };
                tdRemoveButton.appendChild(removeButton);
                dataRow.appendChild(tdRemoveButton);

                tbody.appendChild(dataRow);
                bom_table_element.appendChild(tbody);                
            }
        };
        xhr.send();
    }

    function tableToJson(tableId) {
        const table = document.getElementById(tableId);
        const rows = table.querySelectorAll('tbody tr');
        const headers = table.querySelectorAll('thead th');
        const data = [];
    
        // Find the indices for "Item Code" and "Quantity" columns
        let itemCodeIndex = -1;
        let quantityIndex = -1;
        headers.forEach((header, index) => {
            const headerText = header.textContent.trim();
            if (headerText === "Item-Code") {
                itemCodeIndex = index;
            } else if (headerText === "Qty") {
                quantityIndex = index;
            }
        });
    
        // If either index is not found, return empty data
        if (itemCodeIndex === -1 || quantityIndex === -1) {
            return data;
        }
    
        rows.forEach(row => {
            const rowData = {
                itemCode: row.cells[itemCodeIndex].textContent.trim(),
                quantity: row.cells[quantityIndex].textContent.trim()
            };
            data.push(rowData);
        });
    
        return data;
    }

    document.getElementById('addItemButton').addEventListener('click', function() {
        var item_code_selected = document.getElementById("id_item_code_selected").textContent;
        var quantity_of_item_selected = document.getElementById("id_quantity_of_item_selected_selected").textContent;
        add_item_to_bom(item_code_selected, quantity_of_item_selected);
    });

    function save_bom() {
        // alert('save bom');
        var item_code_element = document.getElementById("item_code");

        if(item_code_element.value === ""){
            // alert("Please select a product to save BOM");
            show_status("Please select a product to save BOM", 3, "ERROR");
            return;
        }

        var bom = tableToJson('id_bom_table');   
        
        const jsonString = JSON.stringify(bom);

        // Create a Blob from the JSON string
        const blob = new Blob([jsonString], { type: 'application/json' });

        // Create a FormData object to hold the file data
        const formData = new FormData();
        formData.append('file', blob, item_code_element.value +'.json');

        // Send the FormData object using Fetch API
        fetch('bom/upload_bom.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.text())
        .then(result => {
            // console.log('Success:', result);
            show_status("Saving BOM, SUCCESS", 3, "SUCCESS");
            
        })
        .catch(error => {
            // console.error('Error:', error);
            show_status("Saving BOM, FAILED", 3, "ERROR");
        });
    }

    document.getElementById('saveBom').addEventListener('click', function() {
        save_bom();
    });

    function update_bom_table_from_bom_file(items_) {

        var items = JSON.parse(items_);
    
        items.forEach(function(item) {
            var new_qty = Number(item.quantity);
            if (new_qty < 0) {
                // alert("Please enter a valid quantity");
                show_status("Please enter a valid quantity", 3, "ERROR");
                return;
            }
            add_item_to_bom(item.itemCode, new_qty);               
        });
    }

    function fetchJsonFile(filename) {
        fetch('bom/download_bom.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: new URLSearchParams({
                'filename': filename
            })
        })
        .then(response => response.text())
        .then(result => {
            console.log('Success:', result);
            update_bom_table_from_bom_file(result);            
            show_status("BOM Loaded from system, SUCCESS", 3, "SUCCESS");
        })
        .catch(error => {
            show_status("BOM Loaded from system, WARNING", 3, "WARNING");
        });       
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

    });