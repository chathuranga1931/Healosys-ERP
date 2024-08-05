
function liverSearch_Confiure_for_Item_for_WOAdd() {
    //constructor(_myname, _search_input, _search_element_id, _livesearch_api, _suggestion_css_element, _callback) {
        live_search_item_for_wo_add = new LiveSearch('live_search_item_for_wo_add','id_searchinput_item_for_wo_add', 'id_livesearch_item_for_wo_add', 'item/livesearch_item.php', 'suggestion-item', function(value){
        // alert(value);            
        document.getElementById('id_searchinput_item_for_wo_add').value = "";
        fetch_item_details(value, 'id_item_details_for_wo_add');
    });
    document.getElementById('id_searchinput_item_for_wo_add').addEventListener("keydown", function(event) {
        live_search_item_for_wo_add.navigateSuggestions(event);
    });
    document.getElementById('id_searchinput_item_for_wo_add').addEventListener("keyup", function() {
        live_search_item_for_wo_add.showResult(this.value);
    });
}

//on_purchase_order_received_for_wo_add(data, "wo", "add")
function on_purchase_order_received_for_wo_add(po, section, subsection){

    // purchase_order_id, supplier_id, order_date, delivery_date, status, total_amount, notes
    document.getElementById("id_" + section + "_code_for_" + section + "_" + subsection).value = padID(data.purchase_order_id, 'WO', 3); // ; po.purchase_order_id.toString();
    document.getElementById("id_order_date_for_" + section + "_" + subsection).value = data.order_date;
    document.getElementById("id_search_input_supplier_for_" + section + "_" + subsection).value = "";
    document.getElementById("id_supplier_id_for_" + section + "_" + subsection).value = padID(data.supplier_id, 'SUP', 3); // po.supplier_id;
    document.getElementById("id_" + section + "_status_for_" + section + "_" + subsection).value = data.status;
    document.getElementById("id_delivery_date_for_" + section + "_" + subsection).value = data.delivery_date;
    document.getElementById("id_" + section + "_note_for_" + section + "_" + subsection).value = data.notes; 

}

function liverSearch_Confiure_for_Item_for_WOEdit() {
    //constructor(_myname, _search_input, _search_element_id, _livesearch_api, _suggestion_css_element, _callback) {
        live_search_item_for_wo_edit = new LiveSearch('live_search_item_for_wo_edit','id_searchinput_item_for_wo_edit', 'id_livesearch_item_for_wo_edit', 'item/livesearch_item.php', 'suggestion-item', function(value){
        // alert(value);            
        document.getElementById('id_searchinput_item_for_wo_edit').value = "";
        fetch_item_details(value, 'id_item_details_for_wo_edit');
    });
    document.getElementById('id_searchinput_item_for_wo_edit').addEventListener("keydown", function(event) {
        live_search_item_for_wo_edit.navigateSuggestions(event);
    });
    document.getElementById('id_searchinput_item_for_wo_edit').addEventListener("keyup", function() {
        live_search_item_for_wo_edit.showResult(this.value);
    });
}

// function on_purchase_order_received_for_wo_edit(po){

//     // purchase_order_id, supplier_id, order_date, delivery_date, status, total_amount, notes
//     document.getElementById("id_wo_code_for_wo_edit").value = padID(po.purchase_order_id, 'WO', 3); // ; po.purchase_order_id.toString();
//     document.getElementById("id_order_date_for_wo_edit").value = po.order_date;
//     document.getElementById("id_search_input_supplier_for_wo_edit").value = "";
//     document.getElementById("id_supplier_id_for_wo_edit").value = padID(po.supplier_id, 'SUP', 3); // po.supplier_id;
//     document.getElementById("id_wo_status_for_wo_edit").value = po.status;
//     document.getElementById("id_delivery_date_for_wo_edit").value = po.delivery_date;
//     document.getElementById("id_wo_note_for_wo_edit").value = po.notes; 

// }

function add_item_to_wo_add(parent, item_code_selected, quantity_of_item_selected, po_status){
        
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
            
            var parent_id = "id_div_wo_table";
            if(parent != null){
                parent_id = parent;
            }

            var headers = ['Item-Code', 'Name', 'Description', "Qty", "Remove"];
            var editable = [false, false, false, true, false];
            if(po_status != null){
                if(po_status == "Delivered" || po_status == "Completed" || po_status == "Suspended" || po_status == "Cancelled"){
                    headers = ['Item-Code', 'Name', 'Description', "Qty"]; 
                    editable = [false, false, false, false, false];                      
                }
                else{
                }
            }
            var table_id = parent_id + '_table';
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
                'Button'
            ];
            if(po_status != null){
                if(po_status == "Delivered" || po_status == "Pending" || po_status == "Completed" || po_status == "Suspended" || po_status == "Cancelled"){
                    data_set = [
                        item.item_code,
                        item.name,
                        item.description,
                        new_qty
                    ];                     
                }
                else{
                    data_set = [
                        item.item_code,
                        item.name,
                        item.description,
                        new_qty,
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


function onClick_addItemToWO_for_add(){
    var item_code_selected = document.getElementById("id_item_details_for_wo_add_table_row_Item-Code").textContent;
    var quantity_of_item_selected = document.getElementById("id_item_details_for_wo_add_table_row_Qty").textContent;

    add_item_to_wo_add('id_div_wo_table_for_wo_add', item_code_selected, quantity_of_item_selected, null);

    document.body.scrollIntoView({ behavior: "smooth", block: "end" });
}

function onClick_addItemToWO_for_edit(){
    var item_code_selected = document.getElementById("id_item_details_for_wo_edit_table_row_Item-Code").textContent;
    var quantity_of_item_selected = document.getElementById("id_item_details_for_wo_edit_table_row_Qty").textContent;

    add_item_to_wo_add('id_div_wo_table_for_wo_edit', item_code_selected, quantity_of_item_selected, null);
    document.body.scrollIntoView({ behavior: "smooth", block: "end" });
}

function on_click_update_wo_for_wo_add(){

    //work_order_id	order_date	estimated_complete_date	completed_date	status	notes	source_inventory_loc_id	manufacturing_process_id	output_inventory_loc_id
    var work_order = {
        order_date : document.getElementById('id_order_date_for_wo_add').value,
        estimated_complete_date : document.getElementById('id_est_delivery_date_for_wo_add').value,
        completed_date : " ",        
        status : "Open",
        notes : document.getElementById('id_wo_note_for_wo_add').value,
        source_inventory_loc_id : "",
        manufacturing_process_id : "",
        output_inventory_loc_id : ""
    }
    
    details = tableToJson('id_div_wo_table_for_wo_add');

    // Adding price to each object in the array
    details.forEach(item => {
    });

    var work_order_id = 1;
    addWorkOrder(work_order, function(response){
        if(response.status != "success"){
            show_status("Error: " + response.message, 3, "ERROR");
        }
        else{
            
            work_order_id = response.id;
            const work_order_details = {
                work_order_id : work_order_id,
                details : details
            }
            addWorkOrderDetails(work_order_details, function(response){

                if(response.status != "success"){
                    show_status("Error: " + response.message, 3, "ERROR");
                }
                else{
                    show_status("Work order details added successfully", 3, "SUCCESS");
                }
            });
        }
    });    
}

function on_click_update_wo_for_wo_edit(){   

    var work_order = {
        work_order_id : unpadID( document.getElementById('id_wo_code_for_wo_edit').value, 'WO', 3 ),
        order_date : document.getElementById('id_order_date_for_wo_edit').value,
        estimated_complete_date : document.getElementById('id_est_delivery_date_for_wo_edit').value,
        completed_date : " ",        
        status : document.getElementById('id_wo_status_for_wo_edit').value,
        notes : document.getElementById('id_wo_note_for_wo_edit').value,
        source_inventory_loc_id : "",
        manufacturing_process_id : "",
        output_inventory_loc_id : ""
    }
    
    details = tableToJson('id_div_wo_table_for_wo_edit');

    // Adding price to each object in the array
    details.forEach(item => {
    });

    const work_order_info = {
        work_order : work_order,
        details : details
    }

    // var work_order_id = 1;
    updateWorkOrderById(work_order_info, function(response){
        if(response.status != "success"){
            show_status("Error: " + response.message, 3, "ERROR");
        }
        else{
            show_status("Work order details updated successfully", 3, "SUCCESS");
        }
    });    
}

function createWOAddForm() {

    var container = document.createElement('div');

    // Heading
    container.appendChild(createHeadingElement('Select WO for Update'));

    // Container for form
    var itemDetailsDiv = createDivElement('item-details', null, 'display:block;');
    var formContainer = createDivElement('container');
    var row1 = createDivElement('row');
    var row2 = createDivElement('row');
    var row3 = createDivElement('row');

    // First row of inputs
    row1.appendChild(createInputElement('text', 'id_wo_code_for_wo_add', 'WO Code:', true, 'Automaticaly Generated', 15));
    row1.appendChild(createInputElement('date', 'id_order_date_for_wo_add', 'Order Date:', false));

    // Second row of inputs
    row2.appendChild(createInputElement('text', 'id_wo_status_for_wo_add', 'WO Status:', true, 'Open', 15));
    row2.appendChild(createInputElement('date', 'id_est_delivery_date_for_wo_add', 'Estimated Delivery Date:', false));
    // row2.appendChild(createInputElement('date', 'id_deliver_date_for_wo_add', 'Delivery Date:', false));

    // Third row with textarea
    row3.appendChild(createTextAreaElement('col-md-3', 'id_wo_note_for_wo_add', 'WO Note:', 2, '150px'));

    formContainer.appendChild(row1);
    formContainer.appendChild(row2);
    formContainer.appendChild(row3);
    itemDetailsDiv.appendChild(formContainer);
    container.appendChild(itemDetailsDiv);

    // Additional sections
    container.appendChild(createHeadingElement('WO Item List'));
    var poItemListContainer = createDivElement('container');
    var poItemListRow = createDivElement('row');
    var poItemDetailsDiv = createDivElement('item-details', null, 'display:block;');
    poItemDetailsDiv.appendChild(
        createDivElement('col-9').appendChild(
            createDivElement(undefined,'id_div_wo_table_for_wo_add')
        )
    );
    poItemDetailsDiv.appendChild(
        createDivElement('col-9').appendChild(
            createButtonElement('id_save_wo_add', 'Update WO', 'on_click_update_wo_for_wo_add()')
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
                            createInputElement_for_LiveSearch('id_searchinput_item_for_wo_add', 'id_livesearch_item_for_wo_add' ,'Item Search', 'Search for items...')
                        )
                    );
                    form.appendChild(searchFormRow);
                    selectItemDetailsDiv.appendChild(form);
                        var tableItemRowDiv = createDivElement('row', 'id_item_details_for_wo_add_parent', 'display:none;');
                            var div_table_col9 = tableItemRowDiv.appendChild(createDivElement('col-9'));
                            div_table_col9.appendChild(createDivElement(undefined, 'id_item_details_for_wo_add'));
                            var div_button_col9 = tableItemRowDiv.appendChild(createDivElement('col-9'));
                            div_button_col9.appendChild(createButtonElement('id_add_to_wo_for_wo_add', 'Add to WO', 'onClick_addItemToWO_for_add()'));
                    selectItemDetailsDiv.appendChild(tableItemRowDiv);

                selectItemRow.appendChild(selectItemDetailsDiv);
            selectItemContainer.appendChild(selectItemRow);
    container.appendChild(selectItemContainer);

    // document.body.appendChild(container);
    return container;
}


function print_pdf_table_wo_items(doc, x, y, data_set){
    
    var table_id = 'id_div_wo_table_for_wo_edit';
    // data_set = tableToJsonItemAll(table_id); //'id_div_wo_table_for_wo_edit'); 
    var remove_column1=4;
    var remove_column2=null;
    
    // Define the columns for the table
    const columns = [
        { header: 'Item-Code', dataKey: 'item_code' },
        { header: 'Name', dataKey: 'name' },
        { header: 'Description', dataKey: 'description' },
        { header: 'Qty', dataKey: 'quantity' }
    ];
    
    jsonToPdfTable(doc, columns, data_set, y);   

    const received = 0;
}

function print_pdf_header_wo_details(doc, x, y, gap_1, doc_id, PageTitle){

    wo_date = document.getElementById('id_order_date_for_wo_edit').value;
    wo_note = document.getElementById('id_wo_note_for_wo_edit').value;
    wo_est_date = document.getElementById('id_est_delivery_date_for_wo_edit').value;

    var displacement2 = {
        x : x, 
        y : y
    };

    displacement2 = print_pdf_add_line_right(x, displacement2.y, gap_1, PageTitle, 14, doc);
    displacement2 = print_pdf_add_line_right(x, displacement2.y, gap_1, "WO Date : " + wo_date , 10, doc);    
    displacement2 = print_pdf_add_line_right(x, displacement2.y, gap_1, "WO Est. Date : " + wo_est_date , 10, doc);
    displacement2 = print_pdf_add_line_right(x, displacement2.y, gap_1, " " , 10, doc);
    displacement2 = print_pdf_add_line_right(x, displacement2.y, gap_1, "# " + doc_id, 10, doc);
}

// Function to remove an object by key
function removeByitemcode(array, keyToRemove) {
    const index = array.findIndex(element => element.item_code === keyToRemove);
    if (index !== -1) {
        array.splice(index, 1);
    }
    return array;
}

function update_item_list_processing_array(item, array){
    const index = array.findIndex(element => element.item_code === item.item_code);
    if (index !== -1) {
        array[index].quantity = parseInt(array[index].quantity, 10) +  parseInt(item.quantity, 10);
    }
    else{
        array.push(item);
    }
}

function update_product_list_max_count_array(item, array){
    const index = array.findIndex(element => element.item_code === item.item_code);
    if (index !== -1) {
        array[index].quantity = parseInt(array[index].quantity, 10) +  parseInt(item.quantity, 10);
    }
    else{
        array.push(item);
    }
}

function fetchItemAndProductDetails(data_set){
    return new Promise((resolve, reject) => {

        const product_list_max_count = [];
        const product_list_processing = [];
        const item_list_processing = [];

        var received = 0;
        for(var i = 0; i < data_set.length; i++){
            fetchItemDetails_by_name(data_set[i].name, data_set, function(item, data_set){
                received++;
                var qty = 0;
                for(var j = 0; j < data_set.length; j++){
                    if(item.item_code === (data_set[j])["item-code"]){
                        qty += parseInt(data_set[j].qty, 10);
                    }
                }
    
                if(is_catogory_product(item.category_id)){
                    
                    const data = {
                        item_code : item.item_code,
                        name: item.name,
                        category_id : item.category_id,
                        quantity : qty,                        
                        description : item.description
                    }
                    product_list_max_count.push(data);
                    product_list_processing.push(data);
                }
                else if(is_catogory_item(item.category_id)){
                    const data = {
                        item_code : item.item_code,
                        name: item.name,
                        category_id : item.category_id,
                        quantity : qty,                        
                        description : item.description
                    }
                    item_list_processing.push(data);
                }
                else{
    
                }

                if(received >= data_set.length){
                    data = {
                        product_list_max_count : product_list_max_count,
                        product_list_processing : product_list_processing,
                        item_list_processing : item_list_processing
                    }
                    resolve(data);
                }
            });        
        }
    });
}

function get_only_item_list(){
    
    return new Promise((resolve, reject) => {

        var table_id = 'id_div_wo_table_for_wo_edit';
        data_set = tableToJsonItemAll(table_id); //'id_div_wo_table_for_wo_edit'); 

        fetchItemAndProductDetails(data_set).then(result => {       

            const product_list_max_count = result.product_list_max_count;
            const product_list_processing = result.product_list_processing;
            const item_list_processing = result.item_list_processing;

            var all_completed = false;
            var idx = 0;
            var expected_receive_count1 = 0;
            var expected_receive_count2 = 0;
            while(!all_completed){

                if(product_list_processing.length == 0){
                    all_completed = true;
                    break;
                }

                if(idx >= product_list_processing.length){
                    idx = 0;
                    is_full_cycle_completed = true;
                    continue;
                }

                var item = product_list_processing[idx];
                idx++;
                removeByitemcode(product_list_processing, item.item_code);

                item.quantity = parseInt(item.quantity, 10);
                if(item.quantity == 0){
                    continue;
                }

                expected_receive_count1++;
                const arg = item;
                fetch_product_details_by_name(item.name, arg, function(product, arg){
                    expected_receive_count1--;
                    product.bom.forEach(function(bom_item){ 
                        expected_receive_count2++;               
                        fetchItemDetails_by_item_code(bom_item.itemCode, arg, function(bom_item_details, arg){                        
                            expected_receive_count2--;   
                            if(is_catogory_product(bom_item_details.category_id)){                        
                                const data = {
                                    item_code : bom_item.itemCode,
                                    name: bom_item_details.name,
                                    category_id : bom_item_details.category_id,
                                    quantity : parseInt(bom_item.quantity, 10) * arg.quantity,
                                    description : bom_item_details.description
                                }
                                update_product_list_max_count_array(data, product_list_max_count);
                            }
                            else if(is_catogory_item(bom_item_details.category_id)){                      
                                const data = {
                                    item_code : bom_item.itemCode,
                                    name: bom_item_details.name,
                                    category_id : bom_item_details.category_id,
                                    quantity : parseInt(bom_item.quantity, 10) * arg.quantity,
                                    description : bom_item_details.description
                                }
                                update_item_list_processing_array(data, item_list_processing);
                            }
                            else{
                
                            }

                            if(expected_receive_count1 == 0 && expected_receive_count2 == 0){
                                // alert("All data received");

                                data = {
                                    product_list_max_count : product_list_max_count,
                                    product_list_processing : product_list_processing,
                                    item_list_processing : item_list_processing
                                }
                                resolve(data);
                            }
                        });                
                    })
                });
            }    
        });
    });
}

function print_pdf_table_wo(doc, x, y){
    
    var table_id = 'id_div_wo_table_for_wo_edit';
    data_set = tableToJsonItemAll(table_id); //'id_div_wo_table_for_wo_edit'); 
    var remove_column1=4;
    var remove_column2=null;
    
    // Define the columns for the table
    const columns = getTableColumns(table_id);
    if(remove_column1) removeColumn(columns, remove_column1);
    if(remove_column2) removeColumn(columns, remove_column2);
    
    jsonToPdfTable(doc, columns, data_set, y);   

    const received = 0;
}

function print_table_for_work_order(PageTitle, doc_id){

    print_pdf_header(40, 15, 2, function(doc, displacement){
        print_pdf_header_wo_details(doc, 40, 15, 2, doc_id, PageTitle);
        print_pdf_table_wo(doc, 40, displacement.y);  
        
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

    get_only_item_list().then(result => {

        const product_list_max_count = result.product_list_max_count;
        const product_list_processing = result.product_list_processing;
        const item_list_processing = result.item_list_processing;

        print_pdf_header(40, 15, 2, function(doc, displacement){
            print_pdf_header_wo_details(doc, 40, 15, 2, doc_id, PageTitle + "-Items");
            print_pdf_table_wo_items(doc, 40, displacement.y, item_list_processing);  
            
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
        
    });    
}

function on_click_print_wo_for_wo_edit() {

    const work_order = document.getElementById('id_wo_code_for_wo_edit').value;
    print_table_for_work_order("Work Order Report", work_order);     
}

function createWOUpdateForm() {
    var container = document.createElement('div');

    // Heading
    container.appendChild(createHeadingElement('Select WO for Update'));

    // Container for form
    var itemDetailsDiv = createDivElement('item-details', null, 'display:block;');
    var formContainer = createDivElement('container');
    var row1 = createDivElement('row');
    var row2 = createDivElement('row');
    var row3 = createDivElement('row');

    // First row of inputs
    row1.appendChild(createInputElement('text', 'id_wo_code_for_wo_edit', 'WO Code:', true, '', 15));
    row1.appendChild(createInputElement('date', 'id_order_date_for_wo_edit', 'Order Date:', false));

    // Second row of inputs
    row2.appendChild(createInputElement('text', 'id_wo_status_for_wo_edit', 'WO Status:', true, '', 15));
    row2.appendChild(createInputElement('date', 'id_est_delivery_date_for_wo_edit', 'Estimated Delivery Date:', false));

    // Third row with textarea
    row3.appendChild(createTextAreaElement('col-md-3', 'id_wo_note_for_wo_edit', 'WO Note:', 2, '150px'));

    formContainer.appendChild(row1);
    formContainer.appendChild(row2);
    formContainer.appendChild(row3);
    itemDetailsDiv.appendChild(formContainer);
    container.appendChild(itemDetailsDiv);

    // Additional sections
    container.appendChild(createHeadingElement('WO Item List'));
    var poItemListContainer = createDivElement('container');
    var poItemListRow = createDivElement('row');
    var poItemDetailsDiv = createDivElement('item-details', null, 'display:block;');
    poItemDetailsDiv.appendChild(
        createDivElement('col-9').appendChild(
            createDivElement(undefined,'id_div_wo_table_for_wo_edit')
        )
    );
    poItemDetailsDiv.appendChild(
        createDivElement('col-9').appendChild(
            createButtonElement('id_save_wo_edit', 'Update WO', 'on_click_update_wo_for_wo_edit()')
        )
    );
    poItemDetailsDiv.appendChild(
        createDivElement('col-9').appendChild(
            createButtonElement('id_print_wo_edit', 'Print WO', 'on_click_print_wo_for_wo_edit()')
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
                            createInputElement_for_LiveSearch('id_searchinput_item_for_wo_edit', 'id_livesearch_item_for_wo_edit' ,'Item Search', 'Search for items...')
                        )
                    );
                    form.appendChild(searchFormRow);
                    selectItemDetailsDiv.appendChild(form);
                        var tableItemRowDiv = createDivElement('row', 'id_item_details_for_wo_edit_parent', 'display:none;');
                            var div_table_col9 = tableItemRowDiv.appendChild(createDivElement('col-9'));
                            div_table_col9.appendChild(createDivElement(undefined, 'id_item_details_for_wo_edit'));
                            var div_button_col9 = tableItemRowDiv.appendChild(createDivElement('col-9'));
                            div_button_col9.appendChild(createButtonElement('id_add_to_wo_for_wo_edit', 'Add to WO', 'onClick_addItemToWO_for_edit()'));
                    selectItemDetailsDiv.appendChild(tableItemRowDiv);

                selectItemRow.appendChild(selectItemDetailsDiv);
            selectItemContainer.appendChild(selectItemRow);
    container.appendChild(selectItemContainer);

    // document.body.appendChild(container);

    return container;
}

function view_wo(order_id){
    localStorage.setItem('wo_edit_order_id', order_id);
    document.getElementById('manufacturing-edit-tab').click();    
} 
    
function load_wo(status, start, limit, element_id){
    // Example usage
    fetchWorkOrderList(start, limit, function(data) {
        if (data) {

            var parent_id = element_id;
            //work_order_id	order_date	estimated_complete_date	completed_date	status	notes	source_inventory_loc_id	manufacturing_process_id	output_inventory_loc_id	
            var header_list = ["WO-ID", "Order-Date", "Estimated-Date", "Completed-Date", "Status", "Notes", "View"];
            var table_id = element_id + "_table";

            create_table(parent_id, header_list, table_id)

            // delivery_date : "2024-07-02"
            // notes : "Test"
            // order_date : "2024-07-01"
            // purchase_order_id : 1
            // status : "Open"
            // supplier_id : 1
            // total_amount : "4750.00"

            data.forEach(data => {

                var data_set = [
                    padID(data.work_order_id, "WO", 3),
                    data.order_date,
                    data.estimated_complete_date,
                    data.completed_date,
                    data.status,                  
                    data.notes,
                    "View",
                ]
                var row_id = table_id + "_row_" + data.work_order_id
                create_table_row(table_id, header_list, data_set, row_id, null)

                var table_cell_id = row_id + "_" + "View";
                var button_text = "View";
                var button_cls = "btn btn-primary";
                var button_id = table_cell_id + "_button";

                add_button_to_table_cell(table_cell_id, button_text, button_cls, button_id, null, function(){
                    view_wo(data.work_order_id);
                });
            }); 
            
            // Handle the fetched data
            // console.log('Fetched purchase orders:', data.purchase_orders);            
            show_status("Fetched purchase orders", 3, "SUCCESS");
        }
    });
}

function onWorkOrderAddSelected() {
    liverSearch_Confiure_for_Item_for_WOAdd();
}

function fetchWorkOrderById_WithCallBack(work_order_id, callback_work_order, callback_work_order_details) {
    
    fetchWorkOrderById(work_order_id, function(response){
        if(response.status != "success"){
            show_status("Error: " + response.message, 3, "ERROR");
        }
        else{
            callback_work_order(response.data);
            fetchWorkOrderDetails(work_order_id, function(response){
                if(response.status != "success"){
                    show_status("Error: " + response.message, 3, "ERROR");
                }
                else{
                    callback_work_order_details(response.data);
                    show_status("Success: " + response.message, 3, "SUCCESS");
                }
            });            
        }

    });       
}

function onWorkOrderListSelected() {   
    Parent = document.getElementById("id_manufacturing_list_table_div");    Parent.innerHTML = '';
    load_wo("Canceled", 0 , 1000 , 'id_manufacturing_list_table_div');
}

function on_work_order_received_for_wo_edit(work_order){
    
    // purchase_order_id, supplier_id, order_date, delivery_date, status, total_amount, notes
    document.getElementById("id_wo_code_for_wo_edit").value = padID(work_order.work_order_id, 'WO', 3); // ; po.purchase_order_id.toString();
    document.getElementById("id_order_date_for_wo_edit").value = work_order.order_date;
    document.getElementById("id_wo_status_for_wo_edit").value = work_order.status;
    document.getElementById("id_est_delivery_date_for_wo_edit").value = work_order.estimated_complete_date;
    document.getElementById("id_wo_note_for_wo_edit").value = work_order.notes; 
}

function on_work_order_details_received_for_wo_edit(work_order_details){

    document.getElementById("id_div_wo_table_for_wo_edit").innerHTML = '';
    for(var i=0; i<work_order_details.length; i++){
        add_item_to_wo_add('id_div_wo_table_for_wo_edit', work_order_details[i].item_id, work_order_details[i].quantity, null);
    }
}

function onWorkOrderEditSelected() {    
        
    var param = localStorage.getItem('wo_edit_order_id');
    fetchWorkOrderById_WithCallBack(param, on_work_order_received_for_wo_edit, on_work_order_details_received_for_wo_edit);

    liverSearch_Confiure_for_Item_for_WOEdit();
}