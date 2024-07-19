<?php

require_once('Setup.php');

$setup = new Setup();
$currentVersion = $setup->getCurrentVersion();

$injectTestData = isset($_GET['injectTestData']) ? filter_var($_GET['injectTestData'], FILTER_VALIDATE_BOOLEAN) : false;

if ($currentVersion === null) {
    echo "No version found. Running setup...<br>";
    $setup->run($injectTestData);
} elseif ($currentVersion < $setup->getVersion()) {
    echo "Old version detected. Running setup...<br>";
    $setup->run($injectTestData);
} else {
    echo "Already Configured.<br>";
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Live Search</title>
    <style>
        .suggestion-item {
            cursor: pointer;
            padding: 5px;
        }

        .suggestion-item:hover, .suggestion-item.selected {
            background-color: #ddd;
        }
    </style>
    <script>
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
        xmlhttp.open("GET", "livesearch.php?q=" + str, true);
        xmlhttp.send();
    }

    function selectSuggestion(value) {
        document.getElementById("searchInput").value = value;
        document.getElementById("livesearch").innerHTML = "";
        document.getElementById("livesearch").style.border = "0px";
        previousValue = value; // Update previousValue to prevent unnecessary AJAX calls
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

    document.addEventListener("DOMContentLoaded", function() {
        document.getElementById("searchInput").addEventListener("keydown", navigateSuggestions);
        document.getElementById("searchInput").addEventListener("keyup", function() {
            showResult(this.value);
        });
    });
    </script>
</head>
<body>

<form autocomplete="off">
    <input type="text" size="30" id="searchInput">
    <div id="livesearch"></div>
</form>

</body>
</html>
