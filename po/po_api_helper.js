
    
    // ================= Functions for Server APIs, Item Related ==================

    function fetch_item_details(itemName, parent=null) {

        var xhr = new XMLHttpRequest();
        xhr.open("GET", "item/db_item_fetch.php?name=" + itemName, true);
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4 && xhr.status === 200) {
                var item = JSON.parse(xhr.responseText); 

                var parent_id = "id_item_details";
                if(parent != null){
                    parent_id = parent;
                }
                var productDetailsDiv = document.getElementById(parent_id);                

                // Remove all child elements from the div
                while (productDetailsDiv.firstChild) {
                    productDetailsDiv.removeChild(productDetailsDiv.firstChild);
                }

                //var id_item_div_row = productDetailsDiv.appendChild(createDivElement('row', parent_id+'_row', 'display:block;'));
                var id_item_div_row = document.getElementById('id_item_div_row');
                id_item_div_row.style.display = 'block';
                var id_item_div_row = document.getElementById(parent_id + '_parent');
                id_item_div_row.style.display = 'block';

                var headers = ['Item-Code', 'Name', 'Description', 'Qty'];
                var table_id = parent_id + '_table';
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

    // ================= Functions for Server APIs, PO Related ==================

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

    function db_update_po(po_code_str_id, po_supplier_element_id, po_details_table_id){
        supplier_id_str = document.getElementById(po_supplier_element_id).value;
        if(supplier_id_str == ""){
            show_status("Please Select Supplier", 3, "ERROR");
            return;
        }

        details = tableToJson(po_details_table_id);

        idx = unpadID(document.getElementById(po_code_str_id).value, 'PO');

        // Adding price to each object in the array
        details.forEach(item => {
            // You can set the price based on your logic
            if(item.price == null) {item.price = 0.0;}
            if(item.total_amount == null) {item.total_amount = item.quantity * item.price;}
        }); 

        sendPurchaseOrderDetails(idx, details); 
    }

    async function fetchPurchaseOrders(status, start_idx, numbers) {
        const url = 'po/db_po_get_list_by_status.php'; // Replace with the actual path to your PHP file
    
        // Prepare the data to be sent in the POST request
        const requestData = {
            status: status,
            start_idx: start_idx,
            numbers: numbers
        };
    
        try {
            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(requestData)
            });
    
            if (!response.ok) {
                throw new Error('Network response was not ok ' + response.statusText);
            }
    
            const responseData = await response.json();
    
            if (responseData.status === 'success') {
                show_status("Get the PO details Success", 3, "SUCCESS");
                return responseData;
            } else {
                console.error('Error:', responseData.message);
                show_status("Get the PO details Failed", 3, "ERROR");
                return null;
            }
        } catch (error) {
            console.error('Fetch error:', error);
            show_status("Get the PO details Failed", 3, "ERROR");
            return null;
        }
    }

    
    /**
     * Update purchase order status and item quantities
     * @param {number} purchaseOrderId - The ID of the purchase order to process.
     * @param {function} callback - A callback function to handle the response.
     */
    function processPurchaseOrder(purchaseOrderId, callback) {
        // Check if the purchaseOrderId is valid
        if (!purchaseOrderId || typeof purchaseOrderId !== 'number') {
            console.error("Invalid purchase order ID");
            callback({ status: 'error', message: 'Invalid purchase order ID' });
            return;
        }
    
        // Prepare the data to send
        const data = {
            purchase_order_id: purchaseOrderId
        };
    
        // Make the API call to the PHP script
        fetch('po/db_po_update_inventory.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(result => {
            // Log the result for debugging
            console.log(result);
    
            // Call the callback function with the result
            callback(result);
        })
        .catch(error => {
            console.error('Error:', error);
            callback({ status: 'error', message: 'An error occurred' });
        });
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