<html>
<head>
<script>
function showResult(str) {
  if (str.length==0) {
    document.getElementById("livesearch").innerHTML="";
    document.getElementById("livesearch").style.border="0px";
    return;
  }
  var xmlhttp=new XMLHttpRequest();
  xmlhttp.onreadystatechange=function() {
    if (this.readyState==4 && this.status==200) {
      document.getElementById("livesearch").innerHTML=this.responseText;
      document.getElementById("livesearch").style.border="1px solid #A5ACB2";
    }
  }
  xmlhttp.open("GET","livesearch.php?q="+str,true);
  xmlhttp.send();
}
</script>
</head>
<body>

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

<form>
<input type="text" size="30" onkeyup="showResult(this.value)">
<div id="livesearch"></div>
</form>

</body>
</html>
