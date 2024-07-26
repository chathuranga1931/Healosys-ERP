
    function padID(id, prefix, n) {

        var idstr = id.toString();
        while (idstr.length < n) {
            idstr = '0' + idstr;
        }
        return prefix + idstr;
    }

    function unpadID(paddedId, prefix) {

        let withoutPrefix = paddedId.startsWith(prefix) ? paddedId.slice(prefix.length) : paddedId;
        let integerId = parseInt(withoutPrefix, 10);
        return integerId;
    }

    function fetchSupplierDetails(supplierName, populate_ele_id) {
        var xhr = new XMLHttpRequest();
        xhr.open('GET', 'supplier/db_fetch_supper_by_name.php?supplier_name=' + encodeURIComponent(supplierName), true);
        xhr.onreadystatechange = function () {
            if (xhr.readyState == 4 && xhr.status == 200) {
                var data = JSON.parse(xhr.responseText);

                document.getElementById(populate_ele_id).value = padID(data[0].supplier_id, 'SUP', 3);            
                console.log(data);
                // Process the data as needed
            } else if (xhr.readyState == 4) {
                console.error('There was a problem with the request:', xhr.statusText);
            }
        };
        xhr.send();
    }

    function remove_raw(uniqueButtonId){
        // alert("Removing item from BOM " + uniqueButtonId);
        var raw = document.getElementById(uniqueButtonId);
        raw.remove();

    }

    function create_table(parent_id, header_list, table_id){
                
        var table_element = document.getElementById(table_id); 
        if(table_element == null){
            table_element = document.createElement('table');
            table_element.id = table_id;
            table_element.className = 'table table-bordered';

            // Define the table headers
            var thead = document.createElement('thead');
            var headerRow = document.createElement('tr');
            header_list.forEach(function(header) {
                var th = document.createElement('th');
                th.textContent = header;
                headerRow.appendChild(th);
            });
            thead.appendChild(headerRow);
            table_element.appendChild(thead);

            var parent_element = document.getElementById(parent_id);
            parent_element.appendChild(table_element);
        }
    }

    function create_table_row(table_id, header_list, data_set, row_id, editable=null){

        var tbody = document.createElement('tbody');
        var dataRow = document.createElement('tr');
        
        dataRow.id = row_id;

        var idx = 0;
        data_set.forEach(function(value) {
            var td = document.createElement('td');
            td.textContent = value;
            td.id = row_id + "_" + header_list[idx];
            if(editable != null){
                td.contentEditable = editable[idx];  // Make the cell editable                
            }
            dataRow.appendChild(td);
            idx++;
        });

        tbody.appendChild(dataRow);
        var table = document.getElementById(table_id);
        table.appendChild(tbody);
    }

    function add_button_to_table_cell(table_cell_id, button_text, button_cls, button_id, create_button_function=null, button_callback){

        if(create_button_function != null){

            var tdButton = document.getElementById(table_cell_id);
            var Button = create_button_function(button_text, button_cls, button_id, button_callback);   
            tdButton.textContent = "";
            tdButton.appendChild(Button);
        }
        else{
            var tdRemoveButton = document.getElementById(table_cell_id);
            var removeButton = document.createElement('button');

            removeButton.textContent = button_text;
            removeButton.className = button_cls;
            removeButton.onclick = button_callback;
            removeButton.id = button_id;

            tdRemoveButton.textContent = "";
            tdRemoveButton.appendChild(removeButton);
        }
    }

    function add_item_to_po(parent=null, item_code_selected, quantity_of_item_selected, po_status){
        
        var new_qty = Number(quantity_of_item_selected);
        if(new_qty <= 0 ){
            // alert("Please enter a valid quantity");
            show_status("Please enter a valid quantity", 3, "ERROR");
            return;
        }

        var xhr = new XMLHttpRequest();
        xhr.open("GET", "item/db_item_fetch_by_item_code.php?itemcode=" + item_code_selected, true);
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4 && xhr.status === 200) {
                var item = JSON.parse(xhr.responseText); 
                
                var parent_id = "id_div_po_table";
                if(parent != null){
                    parent_id = parent;
                }

                var headers = ['Item-Code', 'Name', 'Description', "Qty", "Price", "Total", "Remove"];
                var editable = [false, false, false, true, true, true, false];
                if(po_status != null){
                    if(po_status == "Delivered" || po_status == "Completed" || po_status == "Suspended" || po_status == "Cancelled"){
                        headers = ['Item-Code', 'Name', 'Description', "Qty", "Price", "Total"]; 
                        editable = [false, false, false, false, false, false, false];                      
                    }
                    else{
                    }
                }
                var table_id = 'id_div_po_table_1';
                create_table(parent_id, headers, table_id); 
                
                var row_id = table_id +  "_row_" + item.item_code;

                var div_for_qty = row_id + "_Qty";
                var available_qty_element = document.getElementById(div_for_qty);
                if(available_qty_element){
                    var tmp = available_qty_element.textContent;
                    var qty = Number(tmp);
                    qty += new_qty;
                    available_qty_element.textContent = qty;
                    return;
                }

                var data_set = [
                    item.item_code,
                    item.name,
                    item.description,
                    new_qty,
                    item.price,
                    item.total,
                    'Button'
                ];
                if(po_status != null){
                    if(po_status == "Delivered" || po_status == "Pending" || po_status == "Completed" || po_status == "Suspended" || po_status == "Cancelled"){
                        data_set = [
                            item.item_code,
                            item.name,
                            item.description,
                            item.price,
                            item.total,
                            new_qty
                        ];                     
                    }
                    else{
                        data_set = [
                            item.item_code,
                            item.name,
                            item.description,
                            new_qty,
                            item.price,
                            item.total,
                            'Button'
                        ];
                    }
                }
                else{
                    data_set = [
                        item.item_code,
                        item.name,
                        item.description,
                        new_qty,
                        item.price,
                        item.total,
                        'Button'
                    ];
                }
                
                create_table_row(table_id, headers, data_set, row_id, editable);
                // available_qty_element = document.getElementById(div_for_qty);
                // available_qty_element.contentEditable = true;

                if(po_status != null){
                    if(po_status == "Delivered" || po_status == "Pending" || po_status == "Completed" || po_status == "Suspended" || po_status == "Cancelled"){
                                           
                    }
                    else{
                        var table_cell_id = row_id + "_" + "Remove";
                        var button_text = "Remove";
                        var button_cls = "btn btn-danger";
                        var button_id = table_cell_id + "_button";
                        add_button_to_table_cell(table_cell_id, button_text, button_cls, button_id, null, function(){
                            remove_raw(row_id);
                        });
                    }
                }
                else{
                    var table_cell_id = row_id + "_" + "Remove";
                    var button_text = "Remove";
                    var button_cls = "btn btn-danger";
                    var button_id = table_cell_id + "_button";
                    add_button_to_table_cell(table_cell_id, button_text, button_cls, button_id, null, function(){
                        remove_raw(row_id);
                    });
                }
            }
        };
        xhr.send();
    }

    async function updateStatusList(select_id, selected_idx, callback = null) {
        try {
            // Define the URL of the PHP script
            const url = "po/po_get_po_status.php";
            
            // Fetch the response from the server
            const response = await fetch(url);
            
            // Check if the response is OK (status code 200-299)
            if (!response.ok) {
                throw new Error(`HTTP error! Status: ${response.status}`);
            }
    
            // Parse the JSON data from the response
            const data = await response.json();
            
            // Log the data or handle it as needed
            console.log(data);

            // Populate the select element with the response data
            const selectElement = document.getElementById(select_id);
            for (const [value, text] of Object.entries(data.details)) {
                const option = document.createElement('option');
                option.value = value;
                option.text = text;
                selectElement.appendChild(option);
            }
            selectElement.selectedIndex = selected_idx;
            if(callback != null){
                callback(selectElement.options[selectElement.value].textContent);
            }
            
            // You can now work with the data here
            // e.g., display it on the web page, process it further, etc.
        } catch (error) {
            // Handle any errors that occur during the fetch
            console.error('Error fetching data:', error);
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
                item_id: row.cells[itemCodeIndex].textContent,
                quantity: row.cells[quantityIndex].textContent.trim()
            };
            data.push(rowData);
        });
    
        return data;
    }
    

    function onClick_addItemToPO(){
        var item_code_selected = document.getElementById("id_po_table_row_Item-Code").textContent;
        var quantity_of_item_selected = document.getElementById("id_po_table_row_Qty").textContent;

        add_item_to_po(null, item_code_selected, quantity_of_item_selected, null);

        document.body.scrollIntoView({ behavior: "smooth", block: "end" });
    }

    function onClick_SavePO(){
        
        supplier_id_str = document.getElementById('supplier_id').value;
        if(supplier_id_str == ""){
            show_status("Please Select Supplier", 3, "ERROR");
            return;
        }

        oder_date = document.getElementById('order_date_id').value;
        delivery_date = document.getElementById('delivery_date_id').value;
        status = document.getElementById('status_id').value;
        note = document.getElementById('description').value;

        addPurchaseOrder(unpadID(supplier_id_str, 'SUP'), oder_date, delivery_date, status, 0.0, note, function(idx){
            
            details = tableToJson('id_div_po_table_1');

            // Adding price to each object in the array
            details.forEach(item => {
                // You can set the price based on your logic
                item.price = 0.0;
                item.total_amount = item.quantity * item.price;
            });    
    
            sendPurchaseOrderDetails(idx, details); 
        });
    }

    function onClick_UpdatePO(){
        supplier_id_str = document.getElementById('supplier_id').value;
        if(supplier_id_str == ""){
            show_status("Please Select Supplier", 3, "ERROR");
            return;
        }

        details = tableToJson('id_div_po_table_1');

        idx = unpadID(document.getElementById('po_code').value, 'PO');

        // Adding price to each object in the array
        details.forEach(item => {
            // You can set the price based on your logic
            item.price = 0.0;
            item.total_amount = item.quantity * item.price;
        });    

        sendPurchaseOrderDetails(idx, details); 
    }

    // Function to add a new purchase order
    // addPurchaseOrder(1, '2024-08-01', '2024-08-10', 'Open', 1500.00, 'New order notes');
    function addPurchaseOrder(supplierId, orderDate, deliveryDate, status, totalAmount, notes, callback) {
        // Endpoint URL
        const url = 'po/db_po_add_po.php';

        // Data to send in the POST request
        const data = {
            supplier_id: supplierId,
            order_date: orderDate,
            delivery_date: deliveryDate,
            status: status,
            total_amount: totalAmount,
            notes: notes
        };  

        console.log('Sending data:', data);

        // Make the POST request using Fetch API
        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                // Handle success: display a success message
                console.log('Success:', data.message);
                if(callback != null){
                    callback(data.purchase_order_id);
                }
            } else {
                // Handle error: display the error message
                console.error('Error:', data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    }


    function sendPurchaseOrderDetails(purchaseOrderId, details) { 
    
        // Create JSON data
        var data = {
            purchase_order_id: purchaseOrderId,
            details: details
        };
    
        // Convert to JSON string
        var jsonData = JSON.stringify(data);
    
        // Send POST request to po_add.php
        var xhr = new XMLHttpRequest();
        xhr.open("POST", "po/db_po_add_po_details.php", true); // Replace with the actual path to po_add.php
        xhr.setRequestHeader("Content-Type", "application/json");
    
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4 && xhr.status === 200) {
                var response = JSON.parse(xhr.responseText);
                if (response.status === "success") {
                    show_status("Purchase order details added successfully", 3, "SUCCESS");
                } else {
                    show_status("Error: " + response.message, 3, "ERROR");
                    
                }
            }
        };
    
        xhr.send(jsonData);
    }

    function fetch_item_details(itemName) {

        var xhr = new XMLHttpRequest();
        xhr.open("GET", "item/db_item_fetch.php?name=" + itemName, true);
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4 && xhr.status === 200) {
                var item = JSON.parse(xhr.responseText); 

                var parent_id = "id_item_details";
                var productDetailsDiv = document.getElementById(parent_id);                

                // Remove all child elements from the div
                while (productDetailsDiv.firstChild) {
                    productDetailsDiv.removeChild(productDetailsDiv.firstChild);
                }

                var id_item_div_row = document.getElementById('id_item_div_row');
                id_item_div_row.style.display = 'block';

                var headers = ['Item-Code', 'Name', 'Description', 'Qty'];
                var table_id = 'id_po_table';
                create_table(parent_id, headers, table_id); 

                var data_set = [
                    item.item_code,
                    item.name,
                    item.description,
                    "1"
                ];
                var row_id = table_id +  "_row"; // id_product_details_row_Qty
                create_table_row(table_id, headers, data_set, row_id, null);
                
                var div_for_qty = row_id + "_Qty";
                available_qty_element = document.getElementById(div_for_qty);
                available_qty_element.contentEditable = true;

            }
        };
        xhr.send();
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

    function update_po(po) {
        // purchase_order_id, supplier_id, order_date, delivery_date, status, total_amount, notes
        document.getElementById("po_code").value = padID(po.purchase_order_id, 'PO', 3); // ; po.purchase_order_id.toString();
        document.getElementById("order_date_id").value = po.order_date;
        document.getElementById("searchInput-supplier").value = "";
        document.getElementById("supplier_id").value = padID(po.supplier_id, 'SUP', 3); // po.supplier_id;
        document.getElementById("status_id").value = po.status;
        document.getElementById("delivery_date_id").value = po.delivery_date;
        document.getElementById("description").value = po.notes;    
    }

    function update_po_details(po_details){

        // purchase_order_detail_id	purchase_order_id	item_id	quantity	price	total_amount
        po_details.forEach(po_detail => {
            add_item_to_po(null, po_detail.item_id, po_detail.quantity, null);
        });

        document.body.scrollIntoView({ behavior: "smooth", block: "end" });
    }

    function fetchPurchaseOrderById_WithCallBack(purchaseOrderId, callback_Purchase_Order, callback_Purchase_OrderDetails) {
        // Endpoint URL
        const url = 'po/db_po_fetch_by_id.php';
    
        // Data to send in the POST request
        const data = { purchase_order_id: purchaseOrderId };
    
        // Make the POST request using Fetch API
        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                // Handle success: display the fetched purchase order and details
                console.log('Purchase Order:', data.order);
                console.log('Order Details:', data.details);
                callback_Purchase_Order(data.order);
                callback_Purchase_OrderDetails(data.order, data.details);
            } else {
                // Handle error
                console.error('Error:', data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    }

    function fetchPurchaseOrderById(purchaseOrderId) {
        // Endpoint URL
        const url = 'po/db_po_fetch_by_id.php';
    
        // Data to send in the POST request
        const data = { purchase_order_id: purchaseOrderId };
    
        // Make the POST request using Fetch API
        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                // Handle success: display the fetched purchase order and details
                console.log('Purchase Order:', data.order);
                console.log('Order Details:', data.details);
                update_po(data.order);
                update_po_details(data.details);
            } else {
                // Handle error
                console.error('Error:', data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    }

  

