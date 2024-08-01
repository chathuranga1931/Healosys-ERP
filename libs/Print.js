

function print_pdf_header(x, y, gap_1, callback){

    const { jsPDF } = window.jspdf;
    const logoURL = "assets/img/logo.png"; // Replace with the path to your logo 

    // Initialize jsPDF
    let doc = new jsPDF('p', 'pt', 'a4');

    // doc.setFont('Tahoma'); // Set font type: 'Helvetica', 'Times', 'Courier'

    // Add title
    var page_y = 0;
    var page_x = 0;
    var gap_1 = 2;

    const img = new Image();
    img.src = logoURL;
    img.onload = () => {

        var image_width = 150;
        page_x += x;
        page_y += y;
        var imagee_height = image_width/img.width*img.height;
        const pageWidth = doc.internal.pageSize.getWidth();
        doc.addImage(img, 'PNG', (pageWidth - image_width)/2, page_y, image_width, imagee_height); // Adjust the dimensions and position as needed

        // page_y += imagee_height;

        var displacement = {
            x : page_x, 
            y : page_y
        };       

        aaddress_start_y = displacement.y;
        displacement = print_pdf_add_line(page_x, displacement.y, gap_1, "HealoSys (Pvt) Ltd", 14, doc);
        displacement = print_pdf_add_line(page_x, displacement.y, gap_1, "48-D-2, Colombo Rd", 10, doc);
        displacement = print_pdf_add_line(page_x, displacement.y, gap_1, "Kandy", 10, doc);   
        displacement = print_pdf_add_line(page_x, displacement.y, gap_1, "+94 71 220 93 10", 10, doc);                             
        displacement = print_pdf_add_line(page_x, displacement.y, gap_1, "https://www.healosys.com", 10, doc);
        displacement = print_pdf_add_line(page_x, displacement.y, gap_1, " ", 2, doc);
        // createLine_full(page_x, displacement.y, doc);  
        displacement = print_pdf_add_line(page_x, displacement.y, gap_1, " ", 4, doc);

        if(callback) callback(doc, displacement);
    }
}



function get_text_width(text, font_size, doc) {
    // Set the font size for accurate measurement
    doc.setFontSize(font_size);

    // Calculate the width of the text in units (units are 1/72 inch)
    const textWidthUnits = doc.getStringUnitWidth(text);

    // Convert the width from units to points
    // 1 unit = 1/72 inch, 1 point = 1/72 inch
    // Therefore, text width in points is the same as in units when considering scaleFactor
    const textWidth = textWidthUnits * font_size / doc.internal.scaleFactor;

    console.log(`Text width: ${textWidth} points`);
    return textWidth;
}

function print_pdf_add_line_right(x, y, gap, text, font_size, doc){
    doc.setFontSize(font_size);
    
    const pageWidth = doc.internal.pageSize.getWidth();
    const textWidth = get_text_width(text, font_size, doc);

    const center_x = pageWidth - textWidth - x;
    
    y += (font_size);
    doc.text(text, center_x, y);

    y += (gap);
    x += textWidth;
    return { x, y };
}

function print_pdf_add_line_center(x, y, gap, text, font_size, doc){
    doc.setFontSize(font_size);
    
    const pageWidth = doc.internal.pageSize.getWidth();
    const textWidth = get_text_width(text, font_size, doc);

    const center_x = pageWidth - textWidth;
    
    y += (font_size);
    doc.text(text, center_x/2, y);

    y += (gap);
    x += textWidth;
    return { x, y };
}

function print_pdf_add_line(x, y, gap, text, font_size, doc){
    doc.setFontSize(font_size);
    y += (font_size);
    doc.text(text, x, y);
    const textWidth = get_text_width(text, font_size, doc);
    y += (gap);
    x += textWidth;
    return { x, y };
}


function createLine_full(sx, sy, doc){    
    // Draw the line on the page
    const pageWidth = doc.internal.pageSize.getWidth();
    doc.line(sx, sy, pageWidth-sx, sy);
}


function jsonToPdfTable(doc, columns, jsonArray, startY) {
    // Render the table using autoTable
    doc.autoTable({
        head: [columns.map(col => col.header)],
        body: jsonArray.map(row => columns.map(col => row[col.dataKey])),
        startX: 0,
        startY: startY, // Initial Y position for the table
        styles: { fontSize: 10, cellPadding: 5 }
    });
}