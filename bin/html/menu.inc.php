<?php
	$i = $_GET['i'];
	include_once "../config.inc.php";

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<link href="css/sidebar.css" rel="stylesheet" media="screen" type="text/css" />
</head>
<body background="images/sidebar_bg.png">
<div id="title" class="edpmenu_title_text"><? echo "$i"; ?></div>

<table id="menu" class="edpmenu_menuoption" border="0" width="100%" cellpadding="0" style="border-collapse: collapse">
		
<?php
//Connect to DB.resources and fetch menu items
if ($i == "") { $i = "Configuration"; }
$result = $edp_db->query("SELECT * FROM resources where menu = '$i' order by name");
foreach($result as $row) {
	addMenuItem("loadModule('showresource.php?id=$row[id]');", "icons/$row[icon]", "$row[name]");
}

			
								
	function addMenuItem($action, $icon, $title) {
		echo "<tr onclick=\"$action\" style='cursor: hand'><td width='24' height='30'><img alt='list' src='$icon' width='18px' height='18px'/></td>\n";
		echo "<td><span class='edpmenu_menuoption_text' onmouseover='mover(this)' onmouseout='mout(this)'>$title</span></td></tr>\n";
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
	
</table>


<script>
    function loadModule(page) {
	    top.document.getElementById('console_iframe').src = page;
    }
</script>

</body>