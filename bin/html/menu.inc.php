<?php
	$i = $_GET['i'];
	include_once "../config.inc.php";
	if (!$i) { $i = "EDP"; }

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<link href="css/sidebar.css" rel="stylesheet" media="screen" type="text/css" />
</head>
<body background="images/sidebar_bg.png">


<? //Write out the standard menu - un-categorized items ?>
<div id='title' class='edpmenu_title_text' style='margin-top: 10px'>&nbsp;&nbsp;<?="$i";?></div>
<? generateMenu("$i", ""); ?>


<?php
//Fetch menu items that have a category defined 
$result1 = $edp_db->query("SELECT * FROM resources where menu = '$i' order by category");
foreach($result1 as $row1) {
	if ($row1[category] != $last) { 
		//$result2 = $edp_db->query("SELECT * FROM resources where menu = '$i' AND category = '$row1[category]' order by name");
		echo "<div id='title' class='edpmenu_title_text'>&nbsp;&nbsp;$row1[category]</div>";
		generateMenu("$i", "$row1[category]");
	}
	$last = $row1[category];
}	



function generateMenu($menu, $category) {
	global $edp_db;
	echo "<table id='menu' class='edpmenu_menuoption' border='0' width='100%' cellpadding='0' style='border-collapse: collapse'>\n";
	
	$result = $edp_db->query("SELECT * FROM resources where menu = '$menu' order by name");
	foreach($result as $row) {
		if ($row[category] == "$category" && $row[status] == "active") {
			//Check if the type is redirect (meaning it will go thru showresource for direct where it will use ACTION from db instead
			if ($row[type] == "direct") { addMenuItem("loadModule('$row[action]');", "icons/sidebar/$row[icon]", "$row[name]"); }
			else { addMenuItem("loadModule('showresource.php?id=$row[id]');", "icons/sidebar/$row[icon]", "$row[name]"); }
		}
	}
	echo "</table><br>";
}	


function addMenuItem($action, $icon, $title) {
	echo "<tr onclick=\"$action\" style='cursor: hand'>";
	echo "	<td width='20' height='28'></td>\n";
	echo "	<td width='24' height='28'><img alt='list' src='$icon' width='18px' height='18px'/></td>\n";
	echo "	<td><span class='edpmenu_menuoption_text' onmouseover='mover(this)' onmouseout='mout(this)'>$title</span></td>";
	echo "</tr>\n";
}


?>
		
		
<script>
	function mover(obj) {
		obj.style.color = '#476A83';
	}
	function mout(obj) {
		obj.style.color = '#000000';
	}
</script>
	



<script>
    function loadModule(page) {
	    top.document.getElementById('console_iframe').src = page;
    }
</script>

</body>