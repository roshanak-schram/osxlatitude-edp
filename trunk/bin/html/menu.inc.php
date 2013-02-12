<?php
	$i = $_GET['i'];
	//include "header.inc.php";
	
?>
<div id="title" style="position: absolute; width: 220px; height: 23px; z-index: 2; left: 20px; top: 20px; border-bottom-style: solid; border-bottom-width: 1px">
<b><font face="Arial" color="#476A83">&nbsp;<? echo "$i"; ?></font></b></div>

<table id="menu" style="position: absolute; width: 220px; height: 23px; z-index: 2; left: 25px; top: 50px;" border="0" width="100%" cellpadding="0" style="border-collapse: collapse">
		
		<?php
		
			if ($i == "Configuration") { 
				addMenuItem("loadModule('module.configuration.predefined.php');", "icons/tick.png", "Build from model database");
			}

			if ($i == "Fixes") { 
				addMenuItem("loadModule('workerapp.php?action=fix-touch-sle');", 		"icons/spanner.png", 	"Touch /s/l/e");
				addMenuItem("loadModule('workerapp.php?action=toogle-hibernation');", 	"icons/sleep.png", 		"Ch. Hibernation: $hibernatemode (0/3 = off/on)");
				addMenuItem("loadModule('workerapp.php?action=console-slow-start');", 	"icons/race-flag.png", 	"Console slow start");
				addMenuItem("loadModule('workerapp.php?action=fix-sound-delay');", 		"icons/speaker-on.png", "Fix sound delay");
				addMenuItem("loadModule('workerapp.php?action=fix-biometric');", 		"icons/key.png", 		"Biometric reader");
				addMenuItem("loadModule('workerapp.php?action=fix-console-colors');", 	"icons/paint.png", 		"Console colors");								
				addMenuItem("loadModule('workerapp.php?action=fix-reset-display');",	"icons/monitor.png", 	"Reset displays");
				addMenuItem("loadModule('workerapp.php?action=fix-airdrop');", 			"icons/share.png", 		"Enable Aidrop");					
			}
						
			if ($i == "Tools") { 
				addMenuItem("loadModule('workerapp.php?action=dellBiosCrack');", 	"icons/lock.png", "Dell Bios password cracker");
				addMenuItem("loadModule('workerapp.php?action=install-mc');", 		"icons/window-app.png", "Install Midnight Commander");
				addMenuItem("loadModule('workerapp.php?action=install-htop');", 	"icons/uptrend.png", "Install Htop");
				addMenuItem("loadModule('workerapp.php?action=install-lynx');", 	"icons/globe.png", "Install Lynx");							
				addMenuItem("loadModule('workerapp.php?action=install-istat2');", 	"icons/pie-chart.png", "Install Istat menus 2");				
			}			
			
			if ($i == "Home") {
				addMenuItem("loadModule('workerapp.php?action=update-edp');", 	"icons/update.png", "Download EDP updates");
				addMenuItem("loadModule('workerapp.php?action=changelog');", 	"icons/binocular.png", "Changelog");
			}			
			function addMenuItem($action, $icon, $title) {
				echo "<tr onclick=\"$action\" style='cursor: hand'><td width='40' height='45'><img alt='list' src='$icon' width='32px' height='32px'/></td><td><font face='Arial' size='2'>$title</font></td></tr>\n";
			}
		?>
		
		

	
</table>


<script>
    function loadModule(page) {
	    top.document.getElementById('console_iframe').src = page;
    }
</script>

