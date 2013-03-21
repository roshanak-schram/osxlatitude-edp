<?php

		echo "<div id=\"tabs-3\"><span class='graytitle'>Chameleon bootloader configuration</span>";
		echo "<ul class='pageitem'>";				
			checkbox("Update Chameleon to latest version", "updateCham", "yes");	
			checkbox("Use custom chameleon", "customCham", "$mdrow[customCham]");
		echo "</ul><br>";
		
echo "<span class='graytitle'>Modules</span>";
echo "<ul class='pageitem'>";

$result = $edp_db->query("SELECT * FROM chammods order by name");
foreach($result as $row1) {
	$name = "$row1[name]"; $edpname = $row1[edpname];
	checkbox("$name: $row1[description]", "$edpname", $mdrow[$edpname]);
}
echo "</ul><br>";
				
		
		
		echo "</div>";
		
?>