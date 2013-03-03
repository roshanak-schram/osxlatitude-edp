<?php
	$i = $_GET['i'];
	include_once "../config.inc.php";	
?>
<div id="title" style="position: absolute; width: 235px; height: 23px; z-index: 2; left: 15px; top: 15px; border-bottom-style: solid; border-bottom-width: 1px">
<b><font face="Arial" color="#476A83">&nbsp;<? echo "$i"; ?></font></b></div>

<table id="menu" style="position: absolute; width: 220px; height: 23px; z-index: 2; left: 25px; top: 45px;" border="0" width="100%" cellpadding="0" style="border-collapse: collapse">
		
<?php
	//Connect to DB.resources and fetch menu items
	if ($i == "") { $i = "Configuration"; }
	$result = $edp_db->query("SELECT * FROM resources where menu = '$i' order by name");
	foreach($result as $row) {
		addMenuItem("loadModule('showresource.php?id=$row[id]');", "icons/$row[icon]", "$row[name]");
	}
					
								
	function addMenuItem($action, $icon, $title) {
		echo "<tr onclick=\"$action\" style='cursor: hand'><td width='36' height='38'><img alt='list' src='$icon' width='28px' height='28px'/></td><td onmouseover='mover(this)' onmouseout='mout(this)'><font face='Arial' size='2'>$title</font></td></tr>\n";
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

