<?php
	include "../vendordb.inc.php";
	$vendor = $_GET['vendor'];

	if ($vendor == "0")  { include "../modelinfo.acer.laptop.inc.php"; }
	if ($vendor == "1")  { include "../modelinfo.acer.desktop.inc.php"; }	
	if ($vendor == "2")  { include "../modelinfo.asus.laptop.inc.php"; }
	if ($vendor == "3")  { include "../modelinfo.asus.desktop.inc.php"; }		
	if ($vendor == "4")  { include "../modelinfo.dell.laptop.inc.php"; }
	if ($vendor == "5")  { include "../modelinfo.dell.desktop.inc.php"; }
	if ($vendor == "6")  { include "../modelinfo.hp.laptop.inc.php"; }
	if ($vendor == "7")  { include "../modelinfo.hp.desktop.inc.php"; }			
	if ($vendor == "8")  { include "../modelinfo.lg.laptop.inc.php"; }
	if ($vendor == "9")  { include "../modelinfo.lg.desktop.inc.php"; }
	if ($vendor == "10") { include "../modelinfo.samsung.laptop.inc.php"; }			
	if ($vendor == "11") { include "../modelinfo.samsung.desktop.inc.php"; }
	if ($vendor == "12") { include "../modelinfo.shuttle.laptop.inc.php"; }			
	if ($vendor == "13") { include "../modelinfo.shuttle.desktop.inc.php"; }
?>

<html>

<head>
</head>

<body >

<br><br><br><br><br>

<table align="center"><tr>
<td>Select a model your wish to configure for:&nbsp;</td>

<td>
	<select name="vendor" id="vendor" style="width:160px;" onchange="showType();">
<?php
	//Start by building the first vendor selector	
	$id = 0;
	while ($vendordb[$id] != ""){
		$name = $vendordb[$id]["name"];		
		if ($vendor == "$id") { echo "<option value='$id' selected>$name</option>\n"; } else { echo "<option value='$id'>$name</option>\n"; }
		$id++;
	}	
	echo "</select></td>";

	//Check if $vendor is set so that we know if we should build the model dropdown	
	if ($vendor != "") {
		echo "<td><select name='model' id='model' style='width:160px;'>";
		$id = 1;
		while ($modeldb[$id] != ""){
			$desc = $modeldb[$id]["desc"];		
			echo "<option value='$id'>$desc</option>\n";
			$id++;
		}		
		echo "</select></td>";	
	}
?>
<td><input type="button" value="OK" onclick="doSubmit();">
</tr></table>


<script>
	function doSubmit() {
		var vendor = '<?php echo "$vendor";?>';
		var a = document.getElementById("model");
		var model = a.options[a.selectedIndex].value;
		alert('vendor: '+vendor+' - model: '+model);		
	}
	function showType() {
		var a = document.getElementById("vendor");
		var vendor = a.options[a.selectedIndex].value;
		document.location.href = 'configuration.php?vendor='+vendor+'';
	}
</script>
</body>

</html>
