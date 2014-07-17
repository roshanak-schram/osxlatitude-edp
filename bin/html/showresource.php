
<?php
include_once "edpconfig.inc.php";
include_once "functions.inc.php";

include_once "header.inc.php";

/*
 * load the page of the selected side menu
 */

// Fetch category and id from the link
$categ = $_GET['category'];
$id = $_GET['id'];

// echo $_GET['category'];
// echo $_GET['id'];

switch ($categ) {
	case "Applications":
		$query = "SELECT * FROM appsdata";
	break;
	
	case "EDP":
	if (file_exists("$workpath/update.log")) 
			system_call("rm -rf $workpath/update.log");
	case "Configuration":
		$query = "SELECT * FROM edpdata";
	break;
	
	case "Fixes":
		$query = "SELECT * FROM fixesdata";
	break;
	
	case "Tools":
		$query = "SELECT * FROM toolsdata";
	break;
	
}

// Get info from db
$stmt = $edp_db->query("$query where id = '$id'");
$stmt->execute();
$bigrow = $stmt->fetchAll(); $row = $bigrow[0];

// Write out the top menu
echoPageItemTOP("icons/big/$row[icon]", "$row[submenu]");
	
?>

<div class="pageitem_bottom">
	<p><b>About:</b></p>
	<?="$row[brief]";?>
	<br>
	<p><b>Descripton:</b></p>
	<?="$row[description]";?>
	<br>
	<p><b>Website:</b></p>
	<a href='<?="$row[link]";?>'>Project/Support Link</a>
</div>
<ul class="pageitem">
	<li class="button"><input name="Submit input" type="submit" onclick="document.location.href='<?="$row[action]";?>'" value="Proceed to Install/Update" /></li>
</ul>


