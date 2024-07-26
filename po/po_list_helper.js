
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
function view_po(purchase_order_id){
    window.location.href = "po_edit.php?param=" + encodeURIComponent(purchase_order_id);
}
function get_statuses_for_activity(po_activity) {

    // '0' => 'Open',
    // '1' => 'Suspended',
    // '3' => 'Pending',
    // '4' => 'Delivered',
    // '5' => 'Completed',
    // '6' => 'Canceled'
    var statuses = ["Open"];
    switch(po_activity) {
        case "Unsuspend":
            statuses = ["Suspended"];
            break; 
        case "Place":
            statuses = ["Open"];
            break; 
        case "Delivered":
            statuses = ["Pending", "Delivered_ModifyRquired"];
            break; 
        // case "Complete":
        //     statuses = ["Delivered"];
        //     break; 
        case "Suspend":
            statuses = ["Open", "Pending"];
            break; 
        case "Cancel":
            statuses = ["Open", "Suspended", "Pending"];
            break; 
        case "Delivered_ModifyRquired":
            statuses = ["Delivered"];
            break; 
        default:
        statuses = ["Open"];
            break;
    }
    return statuses;
}
function get_status_after_activity(po_activity) {

    // '0' => 'Open',
    // '1' => 'Suspended',
    // '3' => 'Pending',
    // '4' => 'Delivered',
    // '5' => 'Completed',
    // '6' => 'Canceled'
    var status = "Open"
    switch(po_activity) {
        case "Unsuspend":
            status = "Open";
            break; 
        case "Place":
            status = "Pending";
            break; 
        case "Delivered":
            status = "Delivered";
            break; 
        case "Complete":
            status = "Completed";
            break; 
        case "Suspend":
            status = "Suspended";
            break; 
        case "Cancel":
            status = "Canceled";
            break; 
        case "Delivered_ModifyRquired":
            status = "Delivered_ModifyRquired";
            break; 
        default:
            status = "Open";
            break;
    }
    return status;
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
