// Events from UI
function onSupplierListTabSelected() {
    
    supplierListParent = document.getElementById("id_supplier_list_table_div");    
    supplierListParent.innerHTML = '';

    fetchSupplierList(0, 1000, function(response, error_message) {

        if(response.data){
                
            var parent_id = supplierListParent.id;
            // supplier_id, supplier_name, contact_name, address_line1, address_line2, address_line3, contact_number, phone, whatsapp, email
            var header_list = ["Supplier-ID", "Supplier Name", "Address", "Contact Name", "Contact Number", "Phone", "Whatsapp", "Email", "View"];
            var table_id = parent_id + "_table";

            create_table(parent_id, header_list, table_id);
            var suppliers = response.data;

            suppliers.forEach(supplier => {

                var data_set = [
                    padID(supplier.supplier_id, "SUP", 3),
                    supplier.supplier_name,
                    supplier.address_line1 + '\n' + supplier.address_line2 + '\n' + supplier.address_line3,
                    supplier.contact_name,
                    supplier.contact_number,
                    supplier.phone,
                    supplier.whatsapp,
                    supplier.email,
                    "View",
                ]
                var row_id = table_id + "_row_" + supplier.supplier_id
                create_table_row(table_id, header_list, data_set, row_id, null);

                var table_cell_id = row_id + "_" + "View";
                var button_text = "View";
                var button_cls = "btn btn-primary";
                var button_id = table_cell_id + "_button";

                add_button_to_table_cell(table_cell_id, button_text, button_cls, button_id, null, function(){
                    view_supplier(supplier.supplier_id);
                });
            });             
        
            show_status("Fetched Supplier Lists : Success", 3, "SUCCESS");

        }
        else{
            show_status("Error while fetching suppliers " + error_message, 3, "ERROR");
        }
    });
}

function view_supplier(supplier_id){
    localStorage.setItem('supplier_edit_supplier_id', supplier_id);
    document.getElementById('supplier-edit-tab').click();    
}

function onSupplierAddTabSelected() {
    
}

function onSupplierEditTabSelected() {

    var supplier_id = localStorage.getItem('supplier_edit_supplier_id');
    if(supplier_id == null){
        show_status("Please select supplier to edit", 3, "ERROR");
        return;
    }

    fetchSupplierInfo(supplier_id, function(data, error_message) {

        if(data){

            document.getElementById('id_supplier_code_for_supplier_edit').value = padID(data.supplier_id, "SUP", 3);
            document.getElementById('id_supplier_name_for_supplier_edit').value = data.supplier_name;
            document.getElementById('id_phone_for_supplier_edit').value = data.phone;
            document.getElementById('id_supplier_email_for_supplier_edit').value = data.email;
            document.getElementById('id_address_l1_for_supplier_edit').value = data.address_line1;
            document.getElementById('id_address_l2_for_supplier_edit').value = data.address_line2;
            document.getElementById('id_address_l3_for_supplier_edit').value = data.address_line3;
            document.getElementById('id_contact_person_for_supplier_edit').value = data.contact_name;
            document.getElementById('id_contact_number_for_supplier_edit').value = data.contact_number;
            document.getElementById('id_whatsapp_for_supplier_edit').value = data.whatsapp;
        }
        else{
            show_status("Error while fetching supplier " + error_message, 3, "ERROR");
        }
    })
    
}

function onClick_addSupplier(){

    var supplierData = {
        supplier_id: document.getElementById('id_supplier_code_for_supplier_add').value,
        supplier_name: document.getElementById('id_supplier_name_for_supplier_add').value,
        phone: document.getElementById('id_phone_for_supplier_add').value,
        email: document.getElementById('id_supplier_email_for_supplier_add').value,
        
        address_line1: document.getElementById('id_address_l1_for_supplier_add').value,
        address_line2: document.getElementById('id_address_l2_for_supplier_add').value,
        address_line3: document.getElementById('id_address_l3_for_supplier_add').value,

        contact_number: document.getElementById('id_contact_number_for_supplier_add').value,
        contact_name: document.getElementById('id_contact_person_for_supplier_add').value,        
        whatsapp: document.getElementById('id_whatsapp_for_supplier_add').value
    }

    if(supplierData.supplier_name == ""){
        show_status("Please enter supplier name", 3, "ERROR");
        return;
    }

    addSupplier(supplierData, function(result){

        if(result){
            if(result.status == "success"){
                show_status("Added Supplier Successfully", 3, "SUCCESS");
            }
            else {
                show_status("Added Supplier Failed : " + result.message , 3, "ERROR");
            }
        }
    });
}

// UI Cuszomization
function createSupplierAddForm() {
    
    var container = document.createElement('div');

    // Heading
    container.appendChild(createHeadingElement('Supplier Details'));

    // Container for form
    var itemDetailsDiv = createDivElement('item-details', null, 'display:block;');
    var formContainer = createDivElement('container');
    var row1 = createDivElement('row');
    var row2 = createDivElement('row');
    var row3 = createDivElement('row');
    var row4 = createDivElement('row');

    // First row of inputs
    row1.appendChild(createInputElement('text', 'id_supplier_name_for_supplier_add', 'Supplier Name:', false, '', 100));
    row2.appendChild(createInputElement('text', 'id_supplier_code_for_supplier_add', 'Supplier Code:', true, 'Automaticaly Generated', 15));  
    row3.appendChild(createInputElement('text', 'id_supplier_email_for_supplier_add', 'Email:', false, '', 100));
    row4.appendChild(createInputElement('text', 'id_phone_for_supplier_add', 'Phone:', false, '', 15)); 
    
    row1.appendChild(createInputElement('text', 'id_address_l1_for_supplier_add', 'Address Line 1:', false, '', 100));
    row2.appendChild(createInputElement('text', 'id_address_l2_for_supplier_add', 'Address Line 2:', false, '', 100));
    row3.appendChild(createInputElement('text', 'id_address_l3_for_supplier_add', 'Address Line 3:', false, '', 100));   

    // Second row of inputs            
    row1.appendChild(createInputElement('text', 'id_contact_person_for_supplier_add', 'Contact Person:', false, '', 100)); 
    row2.appendChild(createInputElement('text', 'id_contact_number_for_supplier_add', 'Contact Number:', false, '', 15)); 
    row3.appendChild(createInputElement('text', 'id_whatsapp_for_supplier_add', 'Whatsapp:', false, '', 15)); 

    formContainer.appendChild(row1);
    formContainer.appendChild(row2);
    formContainer.appendChild(row3);
    formContainer.appendChild(row4);
    
    itemDetailsDiv.appendChild(formContainer);
    container.appendChild(itemDetailsDiv);
    
    var buttonRow = createDivElement('row');
    var div_button_col9 = buttonRow.appendChild(createDivElement('col-9'));
    div_button_col9.appendChild(createButtonElement('id_add_to_supplier_for_supplier_add', 'Add Supplier', 'onClick_addSupplier()'));
    formContainer.appendChild(buttonRow);


    return container;
}

function onClick_updateSupplier(){

    var supplierData = {
        supplier_id: unpadID(document.getElementById('id_supplier_code_for_supplier_edit').value, "SUP"),
        supplier_name: document.getElementById('id_supplier_name_for_supplier_edit').value,
        phone: document.getElementById('id_phone_for_supplier_edit').value,
        email: document.getElementById('id_supplier_email_for_supplier_edit').value,
        
        address_line1: document.getElementById('id_address_l1_for_supplier_edit').value,
        address_line2: document.getElementById('id_address_l2_for_supplier_edit').value,
        address_line3: document.getElementById('id_address_l3_for_supplier_edit').value,

        contact_number: document.getElementById('id_contact_number_for_supplier_edit').value,
        contact_name: document.getElementById('id_contact_person_for_supplier_edit').value,        
        whatsapp: document.getElementById('id_whatsapp_for_supplier_edit').value
    }

    if(supplierData.supplier_name == ""){
        show_status("Please enter supplier name", 3, "ERROR");
        return;
    }

    updateSupplierInfo(supplierData, function(result){

        if(result){
            if(result.status == "success"){
                show_status("Added Supplier Successfully", 3, "SUCCESS");
            }
            else {
                show_status("Added Supplier Failed : " + result.message , 3, "ERROR");
            }
        }
    });
}

function createSupplierEditForm() {
    
    var container = document.createElement('div');

    // Heading
    container.appendChild(createHeadingElement('Supplier Details'));

    // Container for form
    var itemDetailsDiv = createDivElement('item-details', null, 'display:block;');
    var formContainer = createDivElement('container');
    var row1 = createDivElement('row');
    var row2 = createDivElement('row');
    var row3 = createDivElement('row');
    var row4 = createDivElement('row');

    // First row of inputs
    row1.appendChild(createInputElement('text', 'id_supplier_name_for_supplier_edit', 'Supplier Name:', false, '', 100));
    row2.appendChild(createInputElement('text', 'id_supplier_code_for_supplier_edit', 'Supplier Code:', true, 'Automaticaly Generated', 15));  
    row3.appendChild(createInputElement('text', 'id_supplier_email_for_supplier_edit', 'Email:', false, '', 100));
    row4.appendChild(createInputElement('text', 'id_phone_for_supplier_edit', 'Phone:', false, '', 15)); 
    
    row1.appendChild(createInputElement('text', 'id_address_l1_for_supplier_edit', 'Address Line 1:', false, '', 100));
    row2.appendChild(createInputElement('text', 'id_address_l2_for_supplier_edit', 'Address Line 2:', false, '', 100));
    row3.appendChild(createInputElement('text', 'id_address_l3_for_supplier_edit', 'Address Line 3:', false, '', 100));   

    // Second row of inputs            
    row1.appendChild(createInputElement('text', 'id_contact_person_for_supplier_edit', 'Contact Person:', false, '', 100)); 
    row2.appendChild(createInputElement('text', 'id_contact_number_for_supplier_edit', 'Contact Number:', false, '', 15)); 
    row3.appendChild(createInputElement('text', 'id_whatsapp_for_supplier_edit', 'Whatsapp:', false, '', 15)); 

    formContainer.appendChild(row1);
    formContainer.appendChild(row2);
    formContainer.appendChild(row3);
    formContainer.appendChild(row4);
    
    itemDetailsDiv.appendChild(formContainer);
    container.appendChild(itemDetailsDiv);
    
    var buttonRow = createDivElement('row');
    var div_button_col9 = buttonRow.appendChild(createDivElement('col-9'));
    div_button_col9.appendChild(createButtonElement('id_update_to_supplier_for_supplier_edit', 'Update Supplier', 'onClick_updateSupplier()'));
    formContainer.appendChild(buttonRow);


    return container;
}