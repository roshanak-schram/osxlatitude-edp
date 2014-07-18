<?php
	$i = $_GET['i'];
	include_once "edpconfig.inc.php";
	if (!$i) { $i = "EDP"; }

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<link href="css/sidebar.css" rel="stylesheet" media="screen" type="text/css" />
</head>
<body background="images/sidebar_bg.png">


<script>
	function mover(obj) {
		obj.style.color = '#476A83';
	}
	function mout(obj) {
		obj.style.color = '#000000';
	}
</script>
	

<script>
    function loadURL(page) {
	    top.document.getElementById('console_iframe').src = page;
    }
</script>


<?php

	// Credits menu
	if ($i == "Credits") {
		$result1 = $edp_db->query("SELECT * FROM credits order by category");
		foreach($result1 as $row1) {
			if ($row1[category] != $last && $row1[status] == "active") { 
				echo "<div id='title' class='edpmenu_title_text' style='margin-top: 10px;'>&nbsp;&nbsp;$row1[category]</div>";
				echo "<table id='menu' class='edpmenu_menuoption' border='0' width='100%' cellpadding='0' style='border-collapse: collapse'>\n";
				$result2 = $edp_db->query("SELECT * FROM credits where category = '$row1[category]' order by name");
				foreach($result2 as $row2) {
					addMenuItem("loadURL('workerapp.php?action=showCredits&id=$row2[id]');", "icons/sidebar/draft.png", "$row2[name] <i>by $row2[owner]</i>");
				}
				echo "</table>";
			}
			$last = $row1[category];
		}
		exit;	
	}

 //-------------------------> side menus
 
switch ($i) {
	case "Applications":
	case "Tools":
		$query = "SELECT * FROM appsdata";
	break;
	
	case "EDP":
	case "Configuration":
		$query = "SELECT * FROM edpdata";
	break;
	
	case "Fixes":
		$query = "SELECT * FROM fixesdata";
	break;
}

//echo "$i<br>";
//echo "$query<br>";

// Fetch menu items that have a category defined 
$categData = $edp_db->query("$query where category = '$i' order by menu");
foreach($categData as $row) {
	if ($row[menu] != $last && $i == $row[category]) { 
		echo "<div id='title' class='edpmenu_title_text'  style='margin-top: 10px;'>&nbsp;&nbsp;$row[menu]</div>";
		generateMenu("$query", "$row[menu]", "$row[category]");
	}
	$last = $row[menu];
}	

function generateMenu($query, $menu, $category) {
	global $edp_db;
	echo "<table id='menu' class='edpmenu_menuoption' border='0' width='100%' cellpadding='0' style='border-collapse: collapse'>\n";
	
	$menuData = $edp_db->query("$query where menu = '$menu' order by submenu");
	foreach($menuData as $row) {
		if ($row[status] == "active") {
			//
			// Check if the type is redirect (meaning it will go thru showresource php) or direct instead
			//
			if ($row[type] == "direct") { addMenuItem("loadURL('$row[action]');", "icons/sidebar/$row[icon]", "$row[submenu]"); }
			
			// redirecting the resource with category and id info in the link
			else { addMenuItem("loadURL('showresource.php?category=$category&id=$row[id]');", "icons/sidebar/$row[icon]", "$row[submenu]"); }
		}
	}
	echo "</table>";
}	

// Function for writeing out menu items
function addMenuItem($action, $icon, $title) {
	echo "<tr onclick=\"$action\" style='cursor: hand'>";
	echo "	<td width='20' height='28'></td>\n";
	echo "	<td width='24' height='28'><img alt='list' src='$icon' width='18px' height='18px'/></td>\n";
	echo "	<td><span class='edpmenu_menuoption_text' onmouseover='mover(this)' onmouseout='mout(this)'>$title</span></td>";
	echo "</tr>\n";
}

?>
		
</body>