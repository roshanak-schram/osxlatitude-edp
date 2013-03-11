<?php
//This file is used to show "pages" in EDP

	$i = $_GET['i'];
	include "header.inc.php";
	//include "include/watermark.inc.php"; 
?>
<br>
<ul class="pageitem">
	<li class="textbox">
<?	
	include "$i"; 
?>
	</li>
</ul>


