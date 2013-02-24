<?php
	$i = $_GET['i'];
?>
<div id="title" style="position: absolute; width: 235px; height: 23px; z-index: 2; left: 15px; top: 15px; border-bottom-style: solid; border-bottom-width: 1px">
<b><font face="Arial" color="#476A83">&nbsp;<? echo "$i"; ?></font></b></div>

<table id="menu" style="position: absolute; width: 220px; height: 23px; z-index: 2; left: 25px; top: 45px;" border="0" width="100%" cellpadding="0" style="border-collapse: collapse">
		
		<?php
		
			if ($i == "Configuration" || $i == "") { 
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
					
			function addMenuItem($action, $icon, $title) {
				echo "<tr onclick=\"$action\" style='cursor: hand'><td width='36' height='38'><img alt='list' src='$icon' width='28px' height='28px'/></td><td onmouseover='mover(this)' onmouseout='mout(this)'><font face='Arial' size='2'>$title</font></td></tr>\n";
			}
		?>
		
		
<script>
	function mover(obj) {
		obj.style.color = '#476A83';
	}
	function mout(obj) {
		obj.style.color = '#000000';
	}
</script>
	
</table>


<script>
    function loadModule(page) {
	    top.document.getElementById('console_iframe').src = page;
    }
</script>

