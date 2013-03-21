<?php

		echo "<div id=\"tabs-2\"><span class='graytitle'>CPU & Power</span>";
		echo "<ul class='pageitem'>";
			checkbox("Patch AppleIntelCPUPowerManagement.kext", "patchCPU", "$mdrow[patchCPU]");
			checkbox("Emulated speedstep", "emulatedST", "$mdrow[emulatedST]");
			checkbox("Install VoodooTSCsync", "tscsync", "$mdrow[tscsync]");
			checkbox("Install NullCPUPowerManagement", "nullcpu", "$mdrow[nullcpu]");	
			checkbox("Install Sleepenabler", "sleepEnabler", "$mdrow[sleepEnabler]");		
		echo "</ul><br></div>";
		
?> 
