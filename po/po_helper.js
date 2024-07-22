
    function padID(id, prefix, n) {
        var idstr = id.toString();
        while (idstr.length < n) {
            idstr = '0' + idstr;
        }
        return prefix + idstr;
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

    function bom_remove_raw(uniqueButtonId){
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

    function create_table_row(table_id, header_list, data_set, row_id){

        var tbody = document.createElement('tbody');
        var dataRow = document.createElement('tr');
        
        dataRow.id = row_id;

        var idx = 0;
        data_set.forEach(function(value) {
            var td = document.createElement('td');
            td.textContent = value;
            td.id = row_id + "_" + header_list[idx];
            dataRow.appendChild(td);
            idx++;
        });

        tbody.appendChild(dataRow);
        var table = document.getElementById(table_id);
        table.appendChild(tbody);
    }

    function add_button_to_table_cell(table_cell_id, button_text, button_cls, button_id, button_callback){

        var tdRemoveButton = document.getElementById(table_cell_id);
        var removeButton = document.createElement('button');

        removeButton.textContent = button_text;
        removeButton.className = button_cls;
        removeButton.onclick = button_callback;
        removeButton.id = button_id;

        tdRemoveButton.textContent = "";
        tdRemoveButton.appendChild(removeButton);
    }

    function add_item_to_po(item_code_selected, quantity_of_item_selected){
        
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
                var headers = ['Item-Code', 'Name', 'Description', "Qty", "Remove"];
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
                    'Button'
                ];
                create_table_row(table_id, headers, data_set, row_id);
                available_qty_element = document.getElementById(div_for_qty);
                available_qty_element.contentEditable = true;


                var table_cell_id = row_id + "_" + "Remove";
                var button_text = "Remove";
                var button_cls = "btn btn-danger";
                var button_id = table_cell_id + "_button";
                add_button_to_table_cell(table_cell_id, button_text, button_cls, button_id, function(){
                    bom_remove_raw(row_id);
                });
            }
        };
        xhr.send();
    }

    // document.getElementById('addItemToPO').addEventListener('click', function() {
        // var item_code_selected = document.getElementById("id_product_details_row_Item-Code").textContent;
        // var quantity_of_item_selected = document.getElementById("id_product_details_row_Qty").textContent;
        // add_item_to_bom(item_code_selected, quantity_of_item_selected);
        // add_item_to_po(item_code_selected, quantity_of_item_selected);
    // });

    // document.getElementById('addItemToPO').addEventListener('click', function() {
        
    //     var item_code_selected = document.getElementById("id_product_details_row_Item-Code").textContent;
    //     var quantity_of_item_selected = document.getElementById("id_product_details_row_Qty").textContent;

    //     add_item_to_po(item_code_selected, quantity_of_item_selected);
    // });

    function onClick_addItemToPO(){
        var item_code_selected = document.getElementById("id_po_table_row_Item-Code").textContent;
        var quantity_of_item_selected = document.getElementById("id_po_table_row_Qty").textContent;

        add_item_to_po(item_code_selected, quantity_of_item_selected);

        document.body.scrollIntoView({ behavior: "smooth", block: "end" });
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
                create_table_row(table_id, headers, data_set, row_id);
                
                var div_for_qty = row_id + "_Qty";
                available_qty_element = document.getElementById(div_for_qty);
                available_qty_element.contentEditable = true;

            }
        };
        xhr.send();
    }

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