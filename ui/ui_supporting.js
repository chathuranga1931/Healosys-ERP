// ================== General Functions (Supporting UI) ==================

    // Function to create a heading element
    function createHeadingElement(headingText, level = 5) {
        var heading = document.createElement(`h${level}`);
        heading.textContent = headingText;
        return heading;
    }

    // Function to create a div element with specified class, id, and style
    function createDivElement(className, id, style) {
        var div = document.createElement('div');
        if (className) div.className = className;
        if (id) div.id = id;
        if (style) div.style.cssText = style;
        return div;
    }

    function createInputElement_for_LiveSearch(inputTextId, liveSearchShowDevID, labelName, placeholder) {
        // Create a div element with the class 'col-md-3'
        var div = document.createElement('div');
        div.className = 'col-md-3';

        // Create a label element
        var label = document.createElement('label');
        label.setAttribute('for', inputTextId);
        label.textContent = labelName;

        // Create an input element
        var input = document.createElement('input');
        input.type = 'text';
        input.id = inputTextId;
        input.name = inputTextId;
        input.placeholder = placeholder;
        input.style.width = '150px';

        // Create a div for live search results
        var liveSearchDiv = document.createElement('div');
        liveSearchDiv.id = liveSearchShowDevID;

        // Append the label, input, and live search div to the main div
        div.appendChild(label);
        div.appendChild(input);
        div.appendChild(liveSearchDiv);

        // Return the main div element
        return div;
    }

    // Function to create an input element with label
    function createInputElement(inputType, inputTextId, labelName, isReadonly, placeholder = '', maxLength = 15) {
        var div = document.createElement('div');
        div.className = 'col-md-3';

        var label = document.createElement('label');
        label.setAttribute('for', inputTextId);
        label.textContent = labelName;

        var input;
        if (inputType === 'select') {
            input = document.createElement('select');
        } else {
            input = document.createElement('input');
            input.type = inputType;
        }

        input.id = inputTextId;
        input.name = inputTextId;
        input.style.width = '150px';
        if (inputType === 'text') {
            input.maxLength = maxLength;
            input.placeholder = placeholder;
        }
        if (isReadonly) {
            input.setAttribute('readonly', true);
        }

        div.appendChild(label);
        div.appendChild(input);
        return div;
    }

    // Function to create a textarea element with label
    function createTextAreaElement(className, textAreaId, labelName, rows, width) {
        var div = document.createElement('div');
        div.className = className;

        var label = document.createElement('label');
        label.setAttribute('for', textAreaId);
        label.textContent = labelName;

        var textarea = document.createElement('textarea');
        textarea.id = textAreaId;
        textarea.name = textAreaId;
        textarea.rows = rows;
        textarea.style.width = width;

        div.appendChild(label);
        div.appendChild(textarea);
        return div;
    }

    // Function to create a button element
    function createButtonElement(buttonId, text, onClickFunction) {
        var button = document.createElement('button');
        button.id = buttonId;
        button.textContent = text;
        button.className = 'btn btn-secondary';
        if (onClickFunction) {
            button.setAttribute('onclick', onClickFunction);
        }
        return button;
    }

    function create_button_function(button_text, button_cls, button_id, button_callback){

        var button = document.createElement('button');

        // Set the button's attributes
        button.type = 'button';
        button.className = 'btn btn-primary';
        button.innerText = button_text;
        button.id = button_id;
        button.onclick = button_callback;

        // Set Bootstrap data attributes for modal
        button.setAttribute('data-bs-toggle', 'modal');
        button.setAttribute('data-bs-target', '#popup_model_po_confirmation');

        return button;
    }

    
    function createDivWithClass_DivAndRole(parentId, className, role, id=null) {
        // Get the parent element by its ID
        var parentElement = document.getElementById(parentId);
        
        // Check if the parent element exists
        if (parentElement) {
            // Create a new div element
            var newDiv = document.createElement('div');
            
            // Set the class attribute
            newDiv.className = className;

            if(id != null){
                newDiv.id = id;
            } 
            
            // Append the new div to the parent element
            parentElement.appendChild(newDiv);
            
            // Return the created div element
            return newDiv;
        } else {
            console.error('Parent element not found');
            return null;
        }
    }

    function createDivWithClass_Div(parentElement, className, id=null) {
        // Get the parent element by its ID
        
        
        // Check if the parent element exists
        if (parentElement) {
            // Create a new div element
            var newDiv = document.createElement('div');
            
            // Set the class attribute
            newDiv.className = className;

            if(id != null){
                newDiv.id = id;
            } 
            
            // Append the new div to the parent element
            parentElement.appendChild(newDiv);
            
            // Return the created div element
            return newDiv;
        } else {
            console.error('Parent element not found');
            return null;
        }
    }

    function createNavTabs_ul(parentElement, ulId) {
        
        // Check if the parent element exists
        if (parentElement) {
            // Create a new ul element
            var newUl = document.createElement('ul');
            
            // Set the class attribute
            newUl.className = 'nav nav-tabs';
            
            // Set the id attribute
            newUl.id = ulId;
            
            // Set the role attribute
            newUl.setAttribute('role', 'tablist');
            
            // Append the new ul to the parent element
            parentElement.appendChild(newUl);
            
            // Return the created ul element
            return newUl;
        } else {
            console.error('Parent element not found');
            return null;
        }
    }

    function createNavItem_li(parentElement, id, selected, content, target_div_id, on_click=null, on_unclick=null) {
        // Check if the parent element exists
        if (parentElement) {
            // Create a new li element
            var newLi = document.createElement('li');
            newLi.className = 'nav-item';
            newLi.setAttribute('role', 'presentation');

            // Create a new button element
            var newButton = document.createElement('button');
            newButton.className = 'nav-link' + (selected ? ' active' : '');
            newButton.id = id;
            newButton.setAttribute('data-bs-toggle', 'tab');
            newButton.setAttribute('data-bs-target', `#${target_div_id}`);
            newButton.setAttribute('type', 'button');
            newButton.setAttribute('role', 'tab');
            newButton.setAttribute('aria-controls', target_div_id);
            newButton.setAttribute('aria-selected', selected ? 'true' : 'false');
            newButton.textContent = content;

            if(on_click != null){
                newLi.addEventListener('shown.bs.tab', on_click);
            }
            if(on_unclick != null){
                newLi.addEventListener('hidden.bs.tab', on_unclick);
            }                

            // Append the button to the li
            newLi.appendChild(newButton);

            // Append the li to the parent element
            parentElement.appendChild(newLi);

            // Return the created li element
            return newLi;
        } else {
            console.error('Parent element not found');
            return null;
        }
    }

    function createTabPane_Div(parentElement, id, controlledByElement, isActive) {
        // Check if the parent element exists
        if (parentElement) {
            // Create a new div element
            var newDiv = document.createElement('div');
            
            // Set the class attribute
            newDiv.className = 'tab-pane fade';
            if (isActive) {
                newDiv.className += ' show active';
            }
            
            // Set the id attribute
            newDiv.id = id;
            
            // Set the role attribute
            newDiv.setAttribute('role', 'tabpanel');
            
            // Set the aria-labelledby attribute
            newDiv.setAttribute('aria-labelledby', controlledByElement.id);
            
            // Append the new div to the parent element
            parentElement.appendChild(newDiv);
            
            // Return the created div element
            return newDiv;
        } else {
            console.error('Parent element not found');
            return null;
        }
    }
    
    function remove_raw(uniqueButtonId){
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

    function create_table_row(table_id, header_list, data_set, row_id, editable=null){

        var tbody = document.createElement('tbody');
        var dataRow = document.createElement('tr');
        
        dataRow.id = row_id;

        var idx = 0;
        data_set.forEach(function(value) {
            var td = document.createElement('td');
            td.textContent = value;
            td.id = row_id + "_" + header_list[idx];
            if(editable != null){
                td.contentEditable = editable[idx];  // Make the cell editable                
            }
            dataRow.appendChild(td);
            idx++;
        });

        tbody.appendChild(dataRow);
        var table = document.getElementById(table_id);
        table.appendChild(tbody);
    }

    function removeColumn(columns, index) {
        if (index >= 0 && index < columns.length) {
            columns.splice(index, 1);
        } else {
            console.error('Invalid index for column removal');
        }
    }

    function getTableColumns(tableDivId) {
        // Retrieve the div element containing the table
        const tableDiv = document.getElementById(tableDivId);
        if (!tableDiv) {
            console.error('Table div not found');
            return [];
        }
    
        // Find the table element within the div
        const table = tableDiv.querySelector('table');
        if (!table) {
            console.error('Table element not found in div');
            return [];
        }
    
        // Get the header row (assumed to be the first row in the table)
        const headerRow = table.querySelector('thead tr');
        if (!headerRow) {
            console.error('Header row not found');
            return [];
        }
    
        // Generate the columns array
        const columns = Array.from(headerRow.children).map((headerCell, index) => {
            const headerText = headerCell.textContent.trim();
            const dataKey = headerText.toLowerCase().replace(/\s+/g, '');
            return { header: headerText, dataKey };
        });
    
        return columns;
    }

    function add_button_to_table_cell(table_cell_id, button_text, button_cls, button_id, create_button_function=null, button_callback){

        if(create_button_function != null){

            var tdButton = document.getElementById(table_cell_id);
            var Button = create_button_function(button_text, button_cls, button_id, button_callback);   
            tdButton.textContent = "";
            tdButton.appendChild(Button);
        }
        else{
            var tdRemoveButton = document.getElementById(table_cell_id);
            var removeButton = document.createElement('button');

            removeButton.textContent = button_text;
            removeButton.className = button_cls;
            removeButton.onclick = button_callback;
            removeButton.id = button_id;

            removeButton.style.fontSize = '12px';
            removeButton.style.padding = '2px';
            removeButton.style.margin = '2px';
            tdRemoveButton.style.padding = '2px';

            tdRemoveButton.textContent = "";
            tdRemoveButton.appendChild(removeButton);
        }
    }

    function tableToJsonItemAll(tableId) {
        const table = document.getElementById(tableId);
        const rows = table.querySelectorAll('tbody tr');
        const headers = table.querySelectorAll('thead th');
        const data = [];
    
        headers.forEach((header, index) => {
            header.dataset.index = index;
        });
    
        rows.forEach(row => {
            const rowData = {};
            headers.forEach(header => {
                //const dataKey = headerText.toLowerCase().replace(/\s+/g, '');
                const headerText = header.textContent.trim().toLowerCase().replace(/\s+/g, ''); // Replace spaces with underscores
                const cell = row.cells[header.dataset.index];
                if (cell) {
                    rowData[headerText] = cell.textContent.trim();
                }
            });
            data.push(rowData);
        });
    
        return data;
    }
    

    function tableToJson(tableId) {
        const table = document.getElementById(tableId);
        const rows = table.querySelectorAll('tbody tr');
        const headers = table.querySelectorAll('thead th');
        const data = [];
    
        // Find the indices for "Item Code" and "Quantity" columns
        let itemCodeIndex = -1;
        let quantityIndex = -1;
        headers.forEach((header, index) => {
            const headerText = header.textContent.trim();
            if (headerText === "Item-Code") {
                itemCodeIndex = index;
            } else if (headerText === "Qty") {
                quantityIndex = index;
            }
        });
    
        // If either index is not found, return empty data
        if (itemCodeIndex === -1 || quantityIndex === -1) {
            return data;
        }
    
        rows.forEach(row => {
            const rowData = {
                item_id: row.cells[itemCodeIndex].textContent,
                quantity: row.cells[quantityIndex].textContent.trim()
            };
            data.push(rowData);
        });
    
        return data;
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