// ================= Function Related to User Interface (PO) ==================

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

    // function liverSearch_Confiure_for_Supplier() {
    //     LiveSearch_Supplier = new LiveSearch('LiveSearch_Supplier','searchInput-supplier', 'livesearch-supplier', 'supplier/livesearch_supplier.php', 'suggestion-item', function(value){
    //         // alert(value);            
    //         document.getElementById('searchInput-supplier').value = value;
    //         fetchSupplierDetails(value, 'supplier_id');
    //     });
    //     document.getElementById('searchInput-supplier').addEventListener("keydown", function(event) {
    //         LiveSearch_Supplier.navigateSuggestions(event);
    //     });
    //     document.getElementById('searchInput-supplier').addEventListener("keyup", function() {
    //         LiveSearch_Supplier.showResult(this.value);
    //     });
    // }
    
    // function liverSearch_Confiure_for_Item() {
    //     LiveSearch_Items = new LiveSearch('LiveSearch_Items','searchInput-item', 'livesearch-item', 'item/livesearch_item.php', 'suggestion-item', function(value){
    //         // alert(value);            
    //         document.getElementById('searchInput-item').value = "";
    //         fetch_item_details(value);
    //     });
    //     document.getElementById('searchInput-item').addEventListener("keydown", function(event) {
    //         LiveSearch_Items.navigateSuggestions(event);
    //     });
    //     document.getElementById('searchInput-item').addEventListener("keyup", function() {
    //         LiveSearch_Items.showResult(this.value);
    //     });
    // }   
    
    // ================= Function Related to User Interface Events ==================

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
   
    // ================= UI Binded Functions ==================

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

    function change_po_status(po_id, activity) {
        
        var status = get_status_after_activity(activity);
        updatePurchaseOrderStatus(po_id, status, activity);
    }

    async function updatePurchaseOrderStatus(purchaseOrderId, newStatus, activity) {
        try {
            // Define the URL to the PHP script
            const url = 'po/db_po_update_status.php'; // Replace with your actual path
    
            // Create the data object to send
            const data = {
                purchase_order_id: purchaseOrderId,
                status: newStatus
            };
    
            // Send the POST request
            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            });
    
            // Parse the JSON response
            const result = await response.json();
    
            // Handle the response based on status
            if (result.status === 'success') {
                console.log('Purchase order status updated successfully:', result.message);
                // Additional logic if needed, e.g., updating UI or notifying the user     
                show_status("Update status Success", 3, "SUCCESS");
    
                // Create a new URL object from the current URL
                const url = new URL(window.location.href);
                // Set the new parameter value
                url.searchParams.set("param", activity);
    
                window.location.href = url;
    
            } else {
                console.error('Error updating purchase order status:', result.message);
                // Additional error handling logic if needed      
                show_status("Update status failed", 3, "ERROR");
            }
        } catch (error) {
            console.error('Network or server error:', error);
            // Handle network or server errors      
            show_status("Update status failed", 3, "ERROR");
        }
    }

    function load_po_activity(status, start, limit, element_id, activity, create_button_function=null, activity_function=null, callback=null){
        // Example usage
        fetchPurchaseOrders(status, start, limit).then(data => {
            if (data) {
    
                var parent_id = element_id;
                var header_list = ["PO_ID", "Order-Date", "Delivery-Date", "Supplier-ID", "Status", "Notes", "Total-Amount", activity];
                var table_id = element_id + "_table";
    
                create_table(parent_id, header_list, table_id)
    
                // delivery_date : "2024-07-02"
                // notes : "Test"
                // order_date : "2024-07-01"
                // purchase_order_id : 1
                // status : "Open"
                // supplier_id : 1
                // total_amount : "4750.00"
    
                data.purchase_orders.forEach(po => {
    
                    var data_set = [
                        padID(po.purchase_order_id, "PO", 3),
                        po.order_date,
                        po.delivery_date,
                        padID(po.supplier_id, 'SUP', 3),
                        po.status,                    
                        po.notes,
                        po.total_amount,
                        activity,
                    ]
                    var row_id = table_id + "_row_" + po.purchase_order_id
                    create_table_row(table_id, header_list, data_set, row_id, null)
    
                    var table_cell_id = row_id + "_" + activity;
                    var button_text = activity;
                    var button_cls = "btn btn-primary";
                    var button_id = table_cell_id + "_button";
    
                    if(activity_function != null){
                        add_button_to_table_cell(table_cell_id, button_text, button_cls, button_id, create_button_function, function(){
                            activity_function(po.purchase_order_id , activity);
                        });
                    }
                    else{
                        add_button_to_table_cell(table_cell_id, button_text, button_cls, button_id, create_button_function, function(){
                            change_po_status(po.purchase_order_id , activity);
                        });
                    }
                }); 
                
                // Handle the fetched data
                // console.log('Fetched purchase orders:', data.purchase_orders);            
                show_status("Fetched purchase orders", 3, "SUCCESS");
            }
        });
    }    
    
    function load_po(status, start, limit, element_id){
        // Example usage
        fetchPurchaseOrders(status, start, limit).then(data => {
            if (data) {
    
                var parent_id = element_id;
                var header_list = ["PO_ID", "Order-Date", "Delivery-Date", "Supplier-ID", "Status", "Notes", "Total-Amount", "View"];
                var table_id = element_id + "_table";
    
                create_table(parent_id, header_list, table_id)
    
                // delivery_date : "2024-07-02"
                // notes : "Test"
                // order_date : "2024-07-01"
                // purchase_order_id : 1
                // status : "Open"
                // supplier_id : 1
                // total_amount : "4750.00"
    
                data.purchase_orders.forEach(po => {
    
                    var data_set = [
                        padID(po.purchase_order_id, "PO", 3),
                        po.order_date,
                        po.delivery_date,
                        padID(po.supplier_id, 'SUP', 3),
                        po.status,                    
                        po.notes,
                        po.total_amount,
                        "View",
                    ]
                    var row_id = table_id + "_row_" + po.purchase_order_id
                    create_table_row(table_id, header_list, data_set, row_id, null)
    
                    var table_cell_id = row_id + "_" + "View";
                    var button_text = "View";
                    var button_cls = "btn btn-primary";
                    var button_id = table_cell_id + "_button";
    
                    add_button_to_table_cell(table_cell_id, button_text, button_cls, button_id, null, function(){
                        view_po(po.purchase_order_id);
                    });
                }); 
                
                // Handle the fetched data
                // console.log('Fetched purchase orders:', data.purchase_orders);            
                show_status("Fetched purchase orders", 3, "SUCCESS");
            }
        });
    }

    function view_po(purchase_order_id){
        localStorage.setItem('po_edit_order_id', purchase_order_id);
        try {
            document.getElementById('purchase-order-edit-tab').click();
        } catch (error) {
            try {alert('this will not goto po_edit, uncomment below line if needed');
            window.location.href = "po_edit.php?param=" + encodeURIComponent(purchase_order_id);
            } catch (error) {
                alert(error);
            }
        }
    }      

    function liverSearch_Confiure_for_Supplier_for_POEdit() {
        live_search_supplier_for_po_edit = new LiveSearch('live_search_supplier_for_po_edit','id_search_input_supplier_for_po_edit', 'id_livesearch_supplier_for_po_edit', 'supplier/livesearch_supplier.php', 'suggestion-item', function(value){
            // alert(value);            
            document.getElementById('id_search_input_supplier_for_po_edit').value = value;
            fetchSupplierDetails(value, 'id_supplier_id_for_po_edit');
        });
        document.getElementById('id_search_input_supplier_for_po_edit').addEventListener("keydown", function(event) {
            live_search_supplier_for_po_edit.navigateSuggestions(event);
        });
        document.getElementById('id_search_input_supplier_for_po_edit').addEventListener("keyup", function() {
            live_search_supplier_for_po_edit.showResult(this.value);
        });
    }
    
    function liverSearch_Confiure_for_Item_for_POEdit() {
        //constructor(_myname, _search_input, _search_element_id, _livesearch_api, _suggestion_css_element, _callback) {
            live_search_item_for_po_edit = new LiveSearch('live_search_item_for_po_edit','id_searchinput_item_for_po_edit', 'id_livesearch_item_for_po_edit', 'item/livesearch_item.php', 'suggestion-item', function(value){
            // alert(value);            
            document.getElementById('id_searchinput_item_for_po_edit').value = "";
            fetch_item_details(value, 'id_item_details_for_po_edit');
        });
        document.getElementById('id_searchinput_item_for_po_edit').addEventListener("keydown", function(event) {
            live_search_item_for_po_edit.navigateSuggestions(event);
        });
        document.getElementById('id_searchinput_item_for_po_edit').addEventListener("keyup", function() {
            live_search_item_for_po_edit.showResult(this.value);
        });
    }

    function on_purchase_order_received_for_po_edit(po){

        // purchase_order_id, supplier_id, order_date, delivery_date, status, total_amount, notes
        document.getElementById("id_po_code_for_po_edit").value = padID(po.purchase_order_id, 'PO', 3); // ; po.purchase_order_id.toString();
        document.getElementById("id_order_date_for_po_edit").value = po.order_date;
        get_supplier(po.supplier_id, function(data){
            window.current_purchase_order_supplier = data;
            document.getElementById("id_search_input_supplier_for_po_edit").value = data.supplier_name;
        });        
        document.getElementById("id_supplier_id_for_po_edit").value = padID(po.supplier_id, 'SUP', 3); // po.supplier_id;
        document.getElementById("id_po_status_for_po_edit").value = po.status;
        document.getElementById("id_delivery_date_for_po_edit").value = po.delivery_date;
        document.getElementById("id_po_note_for_po_edit").value = po.notes; 

    }
    
    function on_purchase_order_details_received_for_po_edit(po, po_details){     

        // purchase_order_detail_id	purchase_order_id	item_id	quantity	price	total_amount
        po_details.forEach(po_detail => {
            add_item_to_po('id_div_po_table_for_po_edit', po_detail.item_id, po_detail.quantity, null);
        });
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

    // Function to create the entire structure
    function createPOUpdateForm() {
        var container = document.createElement('div');

        // Heading
        container.appendChild(createHeadingElement('Select PO for Update'));

        // Container for form
        var itemDetailsDiv = createDivElement('item-details', null, 'display:block;');
        var formContainer = createDivElement('container');
        var row1 = createDivElement('row');
        var row2 = createDivElement('row');
        var row3 = createDivElement('row');

        // First row of inputs
        row1.appendChild(createInputElement('text', 'id_po_code_for_po_edit', 'PO Code:', true, '', 15));
                       //createInputElement_for_LiveSearch(inputTextId, liveSearchShowDevID, labelName, placeholder)
        row1.appendChild(createInputElement_for_LiveSearch('id_search_input_supplier_for_po_edit', 'id_livesearch_supplier_for_po_edit','Supplier :', 'Search for Supplier'));
        row1.appendChild(createInputElement('date', 'id_order_date_for_po_edit', 'Order Date:', false));

        // Second row of inputs
        row2.appendChild(createInputElement('text', 'id_po_status_for_po_edit', 'PO Status:', true, '', 15));
        row2.appendChild(createInputElement('text', 'id_supplier_id_for_po_edit', 'Supplier ID:', true));
        row2.appendChild(createInputElement('date', 'id_delivery_date_for_po_edit', 'Delivery Date:', false));

        // Third row with textarea
        row3.appendChild(createTextAreaElement('col-md-3', 'id_po_note_for_po_edit', 'PO Note:', 2, '150px'));

        formContainer.appendChild(row1);
        formContainer.appendChild(row2);
        formContainer.appendChild(row3);
        itemDetailsDiv.appendChild(formContainer);
        container.appendChild(itemDetailsDiv);

        // Additional sections
        container.appendChild(createHeadingElement('PO Item List'));
        var poItemListContainer = createDivElement('container');
        var poItemListRow = createDivElement('row');
        var poItemDetailsDiv = createDivElement('item-details', null, 'display:block;');
        poItemDetailsDiv.appendChild(
            createDivElement('col-9').appendChild(
                createDivElement(undefined,'id_div_po_table_for_po_edit')
            )
        );
        poItemDetailsDiv.appendChild(
            createDivElement('col-9').appendChild(
                createButtonElement('id_save_po_edit', 'Update PO', 'on_click_update_po_for_po_edit()')
            )
        );        
        poItemDetailsDiv.appendChild(
            createDivElement('col-9').appendChild(
                createButtonElement('id_print_po_edit', 'Print PO', 'on_click_print_po_for_o_edit()')
            )
        );
        poItemListRow.appendChild(poItemDetailsDiv);
        poItemListContainer.appendChild(poItemListRow);
        container.appendChild(poItemListContainer);

        // Select Item Section
        container.appendChild(createHeadingElement('Select Item'));
            var selectItemContainer = createDivElement('container');
                var selectItemRow = createDivElement('row');
                    var selectItemDetailsDiv = createDivElement('item-details', null, 'display:block;');
                        var searchFormRow = createDivElement('row');
                        var form = document.createElement('form');
                        form.setAttribute('autocomplete', 'off');
                        searchFormRow.appendChild(
                            createDivElement('row').appendChild(
                                createInputElement_for_LiveSearch('id_searchinput_item_for_po_edit', 'id_livesearch_item_for_po_edit' ,'Item Search', 'Search for items...')
                            )
                        );
                        form.appendChild(searchFormRow);
                        selectItemDetailsDiv.appendChild(form);
                            var tableItemRowDiv = createDivElement('row', 'id_item_div_row', 'display:none;');
                                var div_table_col9 = tableItemRowDiv.appendChild(createDivElement('col-9'));
                                div_table_col9.appendChild(createDivElement(undefined, 'id_item_details_for_po_edit'));
                                var div_button_col9 = tableItemRowDiv.appendChild(createDivElement('col-9'));
                                div_button_col9.appendChild(createButtonElement('id_add_to_po_for_po_edit', 'Add to PO', 'onClick_addItemToPO()'));
                        selectItemDetailsDiv.appendChild(tableItemRowDiv);

                    selectItemRow.appendChild(selectItemDetailsDiv);
                selectItemContainer.appendChild(selectItemRow);
        container.appendChild(selectItemContainer);

        // document.body.appendChild(container);

        return container;
    }

    function onPurchaseOrderEditSelected(){    
        
        var param = localStorage.getItem('po_edit_order_id');
        fetchPurchaseOrderById_WithCallBack(param, on_purchase_order_received_for_po_edit, on_purchase_order_details_received_for_po_edit);

        liverSearch_Confiure_for_Supplier_for_POEdit();
        liverSearch_Confiure_for_Item_for_POEdit();
    }

    function onPurchaseOrderListSelected(){
        purchaseOrderListParent = document.getElementById("id_purchaser_list_table_div");
        // purchaseOrderListParent.style.display = 'block';
        
        purchaseOrderListParent.innerHTML = '';
        load_po("Canceled", 0 , 1000 , 'id_purchaser_list_table_div');
        load_po("Completed", 0 , 1000 , 'id_purchaser_list_table_div');
        load_po("Dilevered", 0 , 1000 , 'id_purchaser_list_table_div');
        load_po("Open", 0 , 1000 , 'id_purchaser_list_table_div');
    }

    
    function print_pdf_header_po_details(doc, x, y, gap_1, doc_id, PageTitle){

        po_date = document.getElementById('id_order_date_for_po_edit').value;
        po_note = document.getElementById('id_po_note_for_po_edit').value;
        po_est_date = document.getElementById('id_delivery_date_for_po_edit').value;
        po_supplier = document.getElementById('id_search_input_supplier_for_po_edit').value;

        var displacement2 = {
            x : x, 
            y : y
        };

        displacement2 = print_pdf_add_line_right(x, displacement2.y, gap_1, PageTitle, 14, doc);
        displacement2 = print_pdf_add_line_right(x, displacement2.y, gap_1, "PO Date : " + po_date , 10, doc);    
        displacement2 = print_pdf_add_line_right(x, displacement2.y, gap_1, "PO . Date : " + po_est_date , 10, doc);
        displacement2 = print_pdf_add_line_right(x, displacement2.y, gap_1, "Supplier : " + window.current_purchase_order_supplier.supplier_name , 10, doc);
        displacement2 = print_pdf_add_line_right(x, displacement2.y, gap_1, "Name : " + window.current_purchase_order_supplier.contact_name , 10, doc);
        displacement2 = print_pdf_add_line_right(x, displacement2.y, gap_1, "Contact : " + window.current_purchase_order_supplier.contact_number , 10, doc);
        displacement2 = print_pdf_add_line_right(x, displacement2.y, gap_1, " " , 10, doc);
        displacement2 = print_pdf_add_line_right(x, displacement2.y, gap_1, "#: " + doc_id, 10, doc);



        return displacement2;
    }

    function print_pdf_table_po(doc, x, y){
        
        var table_id = 'id_div_po_table_for_po_edit'
        data_set = tableToJsonItemAll(table_id); //'id_div_wo_table_for_wo_edit'); 
        var remove_column1=6;
        var remove_column2=null;
        
        // Define the columns for the table
        const columns = getTableColumns(table_id);
        if(remove_column1) removeColumn(columns, remove_column1);
        if(remove_column2) removeColumn(columns, remove_column2);
        
        jsonToPdfTable(doc, columns, data_set, y);  
    }

    
    function print_table_for_purchase_order(PageTitle, doc_id){
        print_pdf_header(40, 15, 2, function(doc, displacement){
            displacement2 = print_pdf_header_po_details(doc, 40, 15, 2, doc_id, PageTitle);
            if(displacement2.y > displacement.y) displacement.y = displacement2.y;
            print_pdf_table_po(doc, 40, displacement.y);
            // doc.save(doc_id + '.pdf');
            doc.autoPrint();

            // Generate the PDF and create a Blob URL
            const blobUrl = doc.output('bloburl');

            // Create an invisible anchor element
            const link = document.createElement('a');
            link.href = blobUrl;
            link.download = doc_id+'.pdf'; // Set the custom file name

            // Open the Blob URL in a new tab and trigger the print dialog
            window.open(blobUrl, '_blank');
            // link.click();
        });
    }

    function on_click_print_po_for_o_edit() {
        const purchase_order = document.getElementById('id_po_code_for_po_edit').value;
        print_table_for_purchase_order("Purchase Order Report", purchase_order);     
    }
    
    function on_click_update_po_for_po_edit(){
        db_update_po('id_po_code_for_po_edit', 'id_supplier_id_for_po_edit', 'id_div_po_table_for_po_edit');
    }  
    
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