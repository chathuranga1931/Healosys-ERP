
function addSupplier(supplierData, callback) {
    fetch('supplier/db_add_supplier.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(supplierData),
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            console.log(data.message);
            if(callback != null){
                callback(data);
            }
        } else {
            console.error('Error:', data.message);
            if(callback != null){
                callback(data);
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        if(callback != null){
            callback(null);
        }
    });
}

function fetchSupplierList(startIdx, noOfSuppliers, callback) {

    fetch('supplier/db_get_supplier_list.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            start_idx: startIdx,
            no_of_suppliers: noOfSuppliers,
        }),
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            console.log('Suppliers retrieved successfully.');
            if (callback) {
                callback(data);
            }
        } else {
            console.error('Error:', data.message);
            if (callback) {
                callback(data);
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        if (callback) {
            callback(null, error.message);
        }
    });
}

function fetchSupplierInfo(supplierId, callback) {
    fetch('supplier/db_get_supplier_info.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ supplier_id: supplierId }),
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            console.log('Supplier details retrieved successfully.');
            if (callback) {
                callback(data.data);
            }
        } else {
            console.error('Error:', data.message);
            if (callback) {
                callback(null, data.message);
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        if (callback) {
            callback(null, error.message);
        }
    });
}

function updateSupplierInfo(supplierData, callback) {
    fetch('supplier/db_supplier_update.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(supplierData),
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            console.log('Supplier updated successfully.');
            if (callback) {
                callback(data);
            }
        } else {
            console.error('Error:', data.message);
            if (callback) {
                callback(null, data.message);
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        if (callback) {
            callback(null, error.message);
        }
    });
}

function get_supplier(supplier_id, callback) {
    fetchSupplierInfo(supplier_id, (data) => {
        if (data) {
            callback(data);
        }
    })
}