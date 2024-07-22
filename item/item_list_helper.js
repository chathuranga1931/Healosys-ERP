    
    
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

    function add_button_to_table_cell(table_cell_id, button_text, button_id, button_callback){

    }

    function load_catogories(callback) {
        fetch('category/db_category.php')
            .then(response => response.json())
            .then(categories => {
                if(callback != null){
                    callback(categories);
                }
            })
            .catch(error => console.error('Error loading categories:', error)
        );
    }

    function get_category_name(category_id, categories){
        for(var i=0; i<categories.length; i++){
            if(categories[i].CategoryID == category_id){
                return categories[i].CategoryType;
            }
        }
    }

    function list_item_in_table(parent, data){
        // id_div_item_list
        var table_id = 'id_table_item_list';
        var row_id_prefix = table_id +  "_row_";

        var headers = ['Item-Code', 'Category' , 'Name', 'Description', "Qty"];   
        create_table(parent, headers, table_id); 
        
        load_catogories(function(categories){
            for(var i = 0; i < data.length; i++){
                var item = data[i];
    
                var category = get_category_name(item.category_id, categories);

                var _qty = item.stock_quantity;
                if(_qty == null){
                    _qty = 0;
                }
                var data_set = [
                    item.item_code,
                    category,
                    item.name,
                    item.description,
                    _qty
                ]
                create_table_row(table_id, headers, data_set, row_id_prefix + item.item_code); 
            }   
        });                  
    }

    function load_items(){
        
        show_status("Page Loaded....", 3, "SUCCESS");

        var limit = 0;
        var start = 0;

        const url = `item/db_item_list.php?start=${start}&limit=${limit}`;
        fetch(url)
        .then(response => response.json())
        .then(data => {
            show_status("Fetched items : Success", 3, "SUCCESS");
            list_item_in_table('id_div_item_list', data);
            // Handle the fetched items here (e.g., render them on the page)
        })
        .catch(error => {
            show_status("Error fetching items: Failed" + error, 3, "ERROR");
        });
    }

    document.addEventListener("DOMContentLoaded", function() {
        on_load();
        load_items();
    });

    