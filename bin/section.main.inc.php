<?

	function loadMainSystem() {
	
		$choice = showMainMenu();
		
		if ("$choice" == "1") { loadKextConfig(); }
		if ("$choice" == "2") { updateEDP(); exit; }
		if ("$choice" == "q") { exit; }	
	
	
	}
	
	function showMainMenu() {
		global $edpconfig;
		
		include "config.inc.php";
		system("clear");
		echo "$header\n\n";
		
		if ($edpconfig[0]["checkSVNonStart"] == "yes") { checkSVNrevs(); } 
		
		echo " Please choose a submenu..\n\n";
		echo "  1. CONFIGURATION : Configure kexts, dsdt and plists for your computer \n";
		echo "  2. UPDATE        : Update EDP, requires internet connection \n \n";
		echo "  q. Quit          : Don't do squad.. just exit..\n\n\n";
		
		echo "Sys.INFO >> Operating system: $os - Extentions folder: $ee - $slepath \n\n";
		echo "$footer\n\n";	
		echo "Please choose: ";

		
		$choice = getChoice();
		return $choice;
	}


