

function on_loading_home(){    
    document.getElementById('id_system_map_workorders').addEventListener('click', on_system_workorder_clicked);
    document.getElementById('id_system_map_wo_open').addEventListener('click', on_system_workorder_process_open_clicked);
    document.getElementById('id_system_map_items').addEventListener('click', on_system_itemsclicked);
    document.getElementById('id_system_map_products').addEventListener('click', on_system_products_clicked);
}

function on_system_workorder_process_open_clicked(){
    document.getElementById('manufacturing-tab').click(); 
    document.getElementById('manufacturing-add-tab').click(); 
}

function on_system_workorder_clicked(){
    document.getElementById('manufacturing-tab').click(); 
    document.getElementById('manufacturing-list-tab').click(); 
}

function on_system_products_clicked(){
    window.open("bom.php", '_blank');
}

function on_system_itemsclicked(){
    window.open("item.php", '_blank');
}

function addImage_withMap(div, image_url, map) {
    // Create the image element
    var imgElement = document.createElement('img');
    imgElement.src = image_url;
    imgElement.useMap = '#' + map;

    // Append the image element to the passed dev
    div.appendChild(imgElement);
}

function createSystemDiagram(){
    // document.getElementById("home-content").innerHTML = "";
    image_div = document.createElement("div");
    addImage_withMap(image_div, "assets/img/system.svg", "system_diagram_map");
    
    const div = document.createElement('div');
    div.id = 'PO-Open-Count';
    div.className = 'area-text';
    div.textContent = '25';
    image_div.style.position = 'relative';
    image_div.appendChild(div);

    return image_div;

}