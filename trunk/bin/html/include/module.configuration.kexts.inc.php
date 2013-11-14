<?php
		echo "<div id=\"tabs-1\">\n";
		echo "<span class='graytitle'>Kernel Extentions (kexts / drivers)</span>\n";
		echo "<ul class='pageitem'>";

		//Show dropdown for FakeSMC kexts
			$result = $edp_db->query("SELECT * FROM fakesmc");
			echo "<li class='select'><select name='fakesmc'>";
			foreach($result as $row) {
				$s=""; if ("$mdrow[fakesmc]" == "$row[id]") { $s = "SELECTED"; }
				echo "<option value='$row[id]' $s>&nbsp; FakeSMC: $row[name] ($row[notes])</option>\n";
		    }
			echo "</select><span class='arrow'></span> </li>";

				
		//Show dropdown for PS2 kexts
			$result = $edp_db->query("SELECT * FROM ps2New");
			echo "<li class='select'><select name='ps2pack'>";
			if ("$mdrow[ps2]" == "" || "$mdrow[ps2]" == "no") { echo "<option value='no' SELECTED>&nbsp; PS2 kext: None selected</option>\n"; }
			else { echo "<option value='no'>&nbsp; PS2 kext: None selected</option>\n"; }
			foreach($result as $row) {
				$s=""; if ("$mdrow[ps2]" == "$row[id]") { $s = "SELECTED"; }
				echo "<option value='$row[id]' $s>&nbsp; PS2 kext: $row[name] - $row[notes]</option>\n";
		    }
			echo "</select><span class='arrow'></span> </li>";
			
			
			
		//Show dropdown for Audio kexts
			global $os;
			$result = $edp_db->query("SELECT * FROM audio");
			echo "<li class='select'>";
			echo "<select name='audiopack'>\n";	

			foreach($result as $row) {
				$s=""; if ("$mdrow[audio]" == "$row[id]") { $s = "SELECTED"; }
				echo "<option value='$row[id]' $s>&nbsp; Audio: $row[name] - $row[notes]</option>\n";
			}			
				//Check if the field bundledAudio is set to yes, if so we will disable the dropdown field
				if ("$mdrow[audio]" == "builtin" || "$mdrow[audio]" == "builtin") { echo "<option value='no' SELECTED>&nbsp; Audio: Patched AppleHDA</option>"; }

			echo "<option value='no'>&nbsp; Audio: Don't load</option>\n";			
			echo "</select><span class='arrow'></span> </li>\n";



		//Show dropdown for Ethernet (lan) Kexts
			$result = $edp_db->query("SELECT * FROM ethernet");
			echo "<li class='select'><select name='ethernet'>\n";
			if ("$mdrow[ethernet]" == "" || "$mdrow[ethernet]" == "no") { echo "<option value='no' SELECTED>&nbsp; Ethernet kext: Not selected</option>"; }
			else { echo "<option value='no'>&nbsp; Ethernet kext: Don't load</option>\n"; }
			foreach($result as $row) {
				$s=""; if ("$mdrow[ethernet]" == "$row[id]") { $s = "SELECTED"; }
				echo "<option value='$row[id]' $s>&nbsp; Ethernet kext: $row[name] - $row[notes]</option>\n";
		    }			
			echo "</select><span class='arrow'></span> </li>\n";
			

			
		//Show dropdown for Wifi Kexts
			$result = $edp_db->query("SELECT * FROM wifi");
			echo "<li class='select'><select name='wifipack'>\n";
			if ("$mdrow[wifi]" == "" || "$mdrow[wifi]" == "no") { echo "<option value='no' SELECTED>&nbsp; Wifi kext: Not selected</option>\n"; }			
			else { echo "<option value='no'>&nbsp; Wifi kext: Don't load</option>\n"; }
			foreach($result as $row) {
				$s=""; if ("$mdrow[wifi]" == "$row[id]") { $s = "SELECTED"; }
				echo "<option value='$row[id]' $s>&nbsp; Wifi kext: $row[name] - $row[notes]</option>\n";
		    }
			echo "</select><span class='arrow'></span> </li>\n";
			

									
		//Show dropdown for Battery kexts
			$result = $edp_db->query("SELECT * FROM battery");
			echo "<li class='select'><select name='batterypack'>\n";
			if ("$mdrow[battery]" == "" || "$mdrow[battery]" == "no") { echo "<option value='no' SELECTED>&nbsp; Battery kext: Not selected</option>\n"; }
			else { echo "<option value='no'>&nbsp; Battery kext: Don't load</option>\n"; }	
			foreach($result as $row) {
				$s=""; if ("$mdrow[battery]" == "$row[id]") { $s = "SELECTED"; }
				echo "<option value='$row[id]' $s>&nbsp; Battery kext: $row[name] </option>\n";
		    }			
			echo "</select><span class='arrow'></span> </li>\n";
			
			
		echo "</ul><br></div>";
?> 
