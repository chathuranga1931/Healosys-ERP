    
    var currentFocus = -1;
    var previousValue = "";

    function showResult(str) {
        if (str.length == 0) {
            document.getElementById("livesearch").innerHTML = "";
            document.getElementById("livesearch").style.border = "0px";
            previousValue = "";
            return;
        }
        if (str == previousValue) {
            return;
        }
        previousValue = str;

        var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                document.getElementById("livesearch").innerHTML = this.responseText;
                document.getElementById("livesearch").style.border = "1px solid #A5ACB2";
                currentFocus = -1; // Reset focus when new results are loaded
            }
        }
        xmlhttp.open("GET", "item/livesearch_item.php?q=" + str, true);
        xmlhttp.send();
    }

    function selectSuggestion_item(value) {
        document.getElementById("searchInput").value = "";
        document.getElementById("livesearch").innerHTML = "";
        document.getElementById("livesearch").style.border = "0px";
        previousValue = value; // Update previousValue to prevent unnecessary AJAX calls
        fetchItemDetails(value);
    }

    // Function to fetch and update the image
    function updateImage(imageId) {
        fetch('item/get_item_image.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `image_id=${imageId}`
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok ' + response.statusText);
            }
            return response.blob();
        })
        .then(blob => {
            // Create a local URL for the image blob
            const imageUrl = URL.createObjectURL(blob);
            // Update the image element's src attribute
            const preview = document.getElementById('preview')
            preview.src = imageUrl;            
            preview.style.display = 'block';
        })
        .catch(error => console.error('Error fetching image:', error));
    }

    function fetchItemDetails(itemName) {
        var xhr = new XMLHttpRequest();
        xhr.open("GET", "item/db_item_fetch.php?name=" + itemName, true);
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4 && xhr.status === 200) {
                var item = JSON.parse(xhr.responseText);
                // document.getElementById('item_id').value = item.item_id;
                document.getElementById('item_code').value = item.item_code;
                document.getElementById('name').value = item.name;
                document.getElementById('description').value = item.description;
                document.getElementById('category_id').value = item.category_id;
                document.getElementById('price').value = item.price;
                document.getElementById('cost').value = item.cost;
                document.getElementById('reorder_level').value = item.reorder_level;
                document.getElementById('supplier_id').value = item.supplier_id;

                updateImage(item.item_code);
            }
        };
        xhr.send();
    }

    function navigateSuggestions(e) {
        var suggestionBox = document.getElementById("livesearch");
        var items = suggestionBox.getElementsByClassName("suggestion-item");
        if (e.keyCode == 40) { // Down arrow
            currentFocus++;
            addActive(items);
        } else if (e.keyCode == 38) { // Up arrow
            currentFocus--;
            addActive(items);
        } else if (e.keyCode == 13) { // Enter
            e.preventDefault();
            if (currentFocus > -1 && items.length > 0) {
                items[currentFocus].click();
            }
        }
    }

    function addActive(items) {
        if (!items) return false;
        removeActive(items);
        if (currentFocus >= items.length) currentFocus = 0;
        if (currentFocus < 0) currentFocus = items.length - 1;
        items[currentFocus].classList.add("selected");
    }

    function removeActive(items) {
        for (var i = 0; i < items.length; i++) {
            items[i].classList.remove("selected");
        }
    }

    function previewFile() {
        const preview = document.getElementById('preview');
        const file = document.getElementById('id_ItemImage').files[0];
        const reader = new FileReader();

        reader.addEventListener('load', function() {
            // Convert the file to base64 string
            preview.src = reader.result;
            preview.style.display = 'block';
        }, false);

        if (file) {
            reader.readAsDataURL(file);
        }
    }

    document.addEventListener("DOMContentLoaded", function() {
        document.getElementById("searchInput").addEventListener("keydown", navigateSuggestions);
        document.getElementById("searchInput").addEventListener("keyup", function() {
            showResult(this.value);
        });

        loadCategories();
        loadSuppliers();
    });

    function uploadFile(file, newFileName, callback) {

        var renamedFile = new File([file], newFileName, { type: file.type });        
        var formData = new FormData();
        formData.append('file', renamedFile);
    
        var xhr = new XMLHttpRequest();
        xhr.open("POST", "item/upload_item_image.php", true);
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4 && xhr.status === 200) {
                var response = JSON.parse(xhr.responseText);
                if (response.status === 'success') {                   
                    show_status("Item Update Successfull, (Image)", 3, "SUCCESS");
                    if(callback != null){
                        callback(null, response.file_path);
                    }
                } else {
                    if(callback != null){
                        callback(response.message, null);
                    }
                }
            }
        };
        xhr.send(formData);
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

    function addUpdateItem(addOrUpdate_) {

        const addOrUpdate = addOrUpdate_;
        const itemCode = document.getElementById('item_code').value;
        const itemName = document.getElementById('name').value;
        const categoryId = document.getElementById('category_id').value;
        const description = document.getElementById('description').value;
        const price = document.getElementById('price').value;
        const cost = document.getElementById('cost').value;
        const reorderLevel = document.getElementById('reorder_level').value;
        const supplierId = document.getElementById('supplier_id').value;
        const fileInput = document.getElementById('id_ItemImage');

        if (!itemCode || !itemName || !categoryId ) {
            // alert('Please fill out all required fields.');
            show_status("Please fill out all required fields, WARNING", 3, "WARNING");
            
            return;
        }

        // const formData = new FormData();
        // formData.append('file', fileInput.files[0]);

        const data = {
            add_or_update: addOrUpdate,
            item_code: itemCode,
            name: itemName,
            category_id: categoryId,
            description: description,
            price: price,
            cost: cost,
            reorder_level: reorderLevel,
            supplier_id: supplierId
        };

        fetch('item/db_item.php', {
            method: 'POST',
            body: JSON.stringify(data),
            headers: {
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                // alert('Response: ' + result.success);              
                if (fileInput.files.length > 0) {
                    const file = fileInput.files[0];
                    const fileExtension = file.name.split('.').pop();
                    // Handle file upload separately if needed
                    uploadFile(file, itemCode+"."+fileExtension);
                }
                else{
                    // alert('Response : ' + result.message);
                    show_status("Item Updated Success (No Image), SUCCESS", 3, "SUCCESS");  
                }                
            } else if (result.error) {
                // alert('Response: ' + result.error);
                show_status("Add or Update Failed, Server Response " + result.error, 3, "ERROR");  
            }           
        })
        .catch(error => {
            // alert('Error:', error);
            show_status("System Error " + error, 3, "ERROR");  
        });
    }

    document.getElementById('updateButton').addEventListener('click', function() {
        addUpdateItem('update');
    });

    document.getElementById('addItemButton').addEventListener('click', function() {
        addUpdateItem('add');
    });

    function loadCategories() {
        fetch('category/db_category.php')
            .then(response => response.json())
            .then(categories => {
                populateSelect('category_id', categories);
            })
            .catch(error => console.error('Error loading categories:', error));
    }

    function loadSuppliers() {
        fetch('supplier/db_supplier.php')
            .then(response => response.json())
            .then(suppliers => {
                populateSelect('supplier_id', suppliers);
            })
            .catch(error => console.error('Error loading suppliers:', error));
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
        loadCategories();
        loadSuppliers();
    });