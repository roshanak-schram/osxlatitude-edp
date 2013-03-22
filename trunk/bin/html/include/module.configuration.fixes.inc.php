<?php

		echo "<div id=\"tabs-4\"><span class='graytitle'>Fixes</span>";
		echo "<ul class='pageitem'>";				
			checkbox("Use custom kernel", "customKernel", "$mdrow[customKernel]");	
			checkbox("GMA950 Brightness fix", "useGMA950brightfix", "$mdrow[useGMA950brightfix]");
			checkbox("Use ACPI fix (coolbook fix)", "useACPIfix", "$mdrow[useACPIfix]");
			checkbox("Use AHCI fix (Fix waiting for root device)", "patchAHCIml", "$mdrow[patchAHCIml]");
			checkbox("Use patched IOATAFamily.kext (Fix waiting for root device)", "loadIOATAFamily", "$mdrow[loadIOATAFamily]");
			checkbox("Use Rollback USB Drivers", "usbRollBack", "$mdrow[usbRollBack]");
			checkbox("Load natit.kext (Make Apple NVIDIA kexts work)", "loadNatit", "$mdrow[loadNatit]");	
		echo "</ul><br></div>"; 

?>