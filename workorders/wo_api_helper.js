
    function FetchWoStatusList(callback = null) {
        // Define the URL of the PHP script
        const url = "workorders/db_wo_get_wo_status.php";

        // Fetch the response from the server
        fetch(url)
            .then(response => {
                // Check if the response is OK (status code 200-299)
                if (!response.ok) {
                    throw new Error(`HTTP error! Status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                // Log the data or handle it as needed
                console.log(data);

                // Call the callback function if provided
                if (callback != null) {
                    callback(data);
                }
            })
            .catch(error => {
                // Handle any errors that occur during the fetch
                console.error('Error fetching data:', error);
            }
        );
    }

    function addWorkOrder(workOrder, callback) {
        var xhr = new XMLHttpRequest();
        xhr.open("POST", "workorders/db_wo_add_wo.php", true);
        xhr.setRequestHeader("Content-Type", "application/json");

        xhr.onreadystatechange = function () {
            if (xhr.readyState === 4) {
                var response = JSON.parse(xhr.responseText);
                callback(response);
            }
        };

        xhr.send(JSON.stringify(workOrder));
    }

    function addWorkOrderDetails(details, callback) {
        var xhr = new XMLHttpRequest();
        xhr.open("POST", "workorders/db_wo_add_wo_details.php", true);
        xhr.setRequestHeader("Content-Type", "application/json");

        xhr.onreadystatechange = function () {
            if (xhr.readyState === 4) {
                var response = JSON.parse(xhr.responseText);
                callback(response);
            }
        };

        xhr.send(JSON.stringify(details));
    }

    function fetchWorkOrderById(workOrderId, callback) {
        var xhr = new XMLHttpRequest();
        xhr.open("POST", "workorders/db_wo_fetch_wo_by_id.php", true);
        xhr.setRequestHeader("Content-Type", "application/json");

        xhr.onreadystatechange = function () {
            if (xhr.readyState === 4) {
                var response = JSON.parse(xhr.responseText);
                callback(response);
            }
        };

        xhr.send(JSON.stringify({ work_order_id: workOrderId }));
    }

    function fetchWorkOrderList(offset, limit, callback) {
        var xhr = new XMLHttpRequest();
        xhr.open("POST", "workorders/db_wo_get_list.php", true);
        xhr.setRequestHeader("Content-Type", "application/json");

        xhr.onreadystatechange = function () {
            if (xhr.readyState === 4) {
                var response = JSON.parse(xhr.responseText);
                if(response.status === 'success') {
                    if(callback != null) {
                        callback(response.data);
                    }
                }
                else{           
                    show_status("Update status failed", 3, "ERROR");
                }
            }
        };

        xhr.send(JSON.stringify({ offset: offset, limit: limit }));
    }

    function fetchWorkOrderDetails(workOrderId, callback) {
        var xhr = new XMLHttpRequest();
        xhr.open("POST", "workorders/db_wo_fetch_wo_details_by_id.php", true);
        xhr.setRequestHeader("Content-Type", "application/json");

        xhr.onreadystatechange = function () {
            if (xhr.readyState === 4) {
                var response = JSON.parse(xhr.responseText);
                callback(response);
            }
        };

        xhr.send(JSON.stringify({ work_order_id: workOrderId }));
    }

    function updateWorkOrderById(workOrderData, callback) {
        var xhr = new XMLHttpRequest();
        xhr.open("POST", "workorders/db_wo_update_wo_by_id.php", true);
        xhr.setRequestHeader("Content-Type", "application/json");

        xhr.onreadystatechange = function () {
            if (xhr.readyState === 4) {
                var response = JSON.parse(xhr.responseText);
                callback(response);
            }
        };

        xhr.send(JSON.stringify(workOrderData));
    }
    


