<?php
include_once "../config.inc.php";
include_once "header.inc.php";
include_once "../functions.inc.php";

//Fetch ID
$id = $_GET['id'];

//Get info from db
$stmt = $edp_db->query("SELECT * FROM resources where id = '$id'");
$stmt->execute();
$bigrow = $stmt->fetchAll(); $row = $bigrow[0];


	//Write out the top menu
	echoPageItemTOP("icons/big/$row[icon]", "$row[name]");
	
		
?>


<div class="pageitem_bottom">
	<?="$row[description]";?>
</div>
<ul class="pageitem">
<li class="button"><input name="Submit input" type="submit" onclick="document.location.href='<?="$row[action]";?>'" value="Next.." /></li>
</ul>


