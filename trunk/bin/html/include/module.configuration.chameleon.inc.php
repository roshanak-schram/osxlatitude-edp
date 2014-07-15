<?php

		echo "<div id=\"tabs-3\"><span class='graytitle'>Chameleon bootloader configuration</span>";
		echo "<ul class='pageitem'>";				
			checkbox("Update Chameleon to latest version 2.2 r2391", "updateCham", "no");
			checkbox("Use Enoch Chameleon version 2.2 r2390?", "useEnochCham", "no");
			checkbox("Use custom chameleon (copy boot file to 'Extra/include' if you have)", "customCham", "no");	
		echo "</ul><br>";
		
		echo "<span class='graytitle'>Modules</span>";
		echo "<ul class='pageitem'>";
			$i=0;
			while ($chamdb[$i] != "") {
				//resetting vars
				$id = ""; $name = ""; $desc = ""; $status = ""; $c = "";
				
				//Getting vars from the optional multidim. array
				$id = $chamdb[$i]['id']; $uid = $chamdb[$i]['edpname']; $desc = $chamdb[$i]['description'];
				
				//Checking wether we are using this in our model
				$status = isChamModsInUse("$id");
				
				checkbox("$desc", "$uid", "$status");
				
				$i++;
			}	
			
		echo "</ul><br></div>";
		
		
		function isChamModsInUse($id) {
			global $modelID; global $edp_db;
			global $query;
			$stmt = $edp_db->query($query);
			$stmt->execute();
			$result = $stmt->fetchAll();

			$data = $result[0]['chameMods'];
			//If nothing is defined in the models db just return blank
			if ($data == "") { return "no"; }
			
			$array 	= explode(',', $data);
			foreach($array as $opt) {
				if ($opt == $id) { return "yes"; }				
			}
			return "no";
		}
	
?> 
