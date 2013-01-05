<?

	function loadTools() {
		$choice = showToolsMenu();
		if ($choice == "1")  { runDellBiosCrack(); }
		if ($choice == "x")  { loadMainSystem(); }
		if ($choice == "q")  { exit; }			
		
	}

	function showToolsMenu() {
		include "config.inc.php";
		system("clear");
		echo "$header\n\n";
		echo " This section contains diffrent tools included in EDP\n\n";
		echo "What do you wanna do ? \n\n";
		echo "  1.  Run the Dell Bios password cracker\n";
		echo "  x.  <-- Go back to last menu \n";
		echo "  q. Quit - don't do anything. \n\n";
		echo "$footer\n\n";	
		echo " Please choose: ";
		$choice = getChoice();
		return "$choice";		
	}


	function runDellBiosCrack() {
		global $workpath;
		system("open $workpath/bin/DELLBiosPWgen");
		loadTools();
	}
?>
