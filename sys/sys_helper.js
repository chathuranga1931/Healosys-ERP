   // ================= General For System Functions ==================

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