<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit;
}
?>
<?php include 'ui/header.php'; ?>

<style>
    .suggestion-item {
        cursor: pointer;
        padding: 5px;
    }

    .suggestion-item:hover, .suggestion-item.selected {
        background-color: #ddd;
    }

    .item-details {
        margin-bottom: 5px;
    }

    .item-details label {
        display: inline-block;
        width: 120px;
    }

    .item-details input, .item-details select, .item-details textarea {
        margin-bottom: 10px;
        width: 300px;
    }
    
    #preview {
        max-width: 150px;
        max-height: 150px;
        display: none;
    }    
    .item-details {
        font-size: 0.75rem; /* Reduced font size */
    }    
    
</style>

<script src="libs/Common.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    load_page_title("Item List");
});
</script>
<!-- <div class="section_line"></div> -->

<div id="itemDetails-2" class="item-details" style="display:block;">
    <div class="row" >
        <div class="col-md-9">
            <div id="id_div_item_list"></div>
        </div>
    </div>
</div>
<script src="item/item_list_helper.js"></script>

<?php include 'ui/footer.php'; ?>
