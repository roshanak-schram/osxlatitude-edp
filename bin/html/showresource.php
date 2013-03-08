<?php
include_once "../config.inc.php";
include_once "header.inc.php";
include_once "include/watermark.inc.php";

//Fetch ID
$id = $_GET['id'];

//Get info from db
$stmt = $edp_db->query("SELECT * FROM resources where id = '$id'");
$stmt->execute();
$bigrow = $stmt->fetchAll(); $row = $bigrow[0];
		
		
?>

<span class="graytitle"><center><?="$row[name]";?></center></span>
<br>
<ul class="pageitem">
	<li class="textbox">
	<span class="header">Description</span>
	<?="$row[description]";?>
	<br>
	
	</li>
</ul>

<br>
<br>
<ul class="pageitem">
<li class="button"><input name="Submit input" type="submit" onclick="document.location.href='<?="$row[action]";?>'" value="OK, I understand.. go ahead!" /></li>
</ul>

