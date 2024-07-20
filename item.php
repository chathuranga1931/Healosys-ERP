<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Item Search</title>
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
        xmlhttp.open("GET", "livesearch_item.php?q=" + str, true);
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

<h1>Item Search</h1>


<form autocomplete="off">
    <input type="text" size="30" id="searchInput">
    <div id="livesearch"></div>
</form>

<p><a href="logout.php">Logout</a></p>
<p><a href="index.php">Back to Home</a></p>

</body>
</html>
