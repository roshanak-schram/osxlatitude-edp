<?

	function loadInstallerConfig() {
		include_once "functions.inc.php";
		
		$choice = showINSTALLERmenu();
		
	
		if ($choice == "0") {
				downloadAndRun("http://www.osxlatitude.com/files/mc.dmg", "dmg", "mc.dmg", "/Volumes/mc.pkg/mc.pkg");	
		}

		if ($choice == "1") {
				downloadAndRun("http://www.osxlatitude.com/files/htop.pkg", "pkg", "htop.pkg", "/downloads/htop.pkg");		
		}
		
		if ($choice == "2") {
				downloadAndRun("http://www.osxlatitude.com/files/lynx.dmg", "dmg", "lynx.dmg", "/Volumes/Lynx-2.8.7d9-10.5.1+u/install.command");		
		}

		if ($choice == "3") {
				system("open -n $workpath/storage/prefPanes/VoodooHDA.prefPane");	
		}

		if ($choice == "4") {

		}

		if ($choice == "5") {

		}

		if ($choice == "6") {
				system("open -n $workpath/storage/prefPanes/VoodooPS2.prefpane");	
		}
						
		if ($choice == "7") {
				downloadAndRun("http://www.osxlatitude.com/files/Chameleon.pkg", "pkg", "Chameleon.pkg", "/downloads/Chameleon.pkg");		
		}
				
		if ($choice == "8") {
				downloadAndRun("http://www.osxlatitude.com/files/istat2.dmg", "dmg", "istat2.dmg", "/Volumes/istat2/install.app");		
		}
		
		
		
		
		if ($choice != "q" && $choice != "x") {	
			echo "Returning to installer menu in 10 secs..... \n";
			system("sleep 10");
			loadInstallerConfig();
		}
				
		
			
		if ($choice == "q") { exit; }
		if ($choice == "x") { loadMainSystem(); exit; }
		
		
					
	}
		
	function showINSTALLERmenu() {
		include "config.inc.php";
		system("clear");
		echo "$header\n\n";
		
		echo "Looking for the right tool for the job ?.. look no more.. \n\n";
		echo " 0. Install Midnight Commander (Legendary text based file manager)\n";
		echo " 1. Install Htop (Awesome replacement for the top command)\n";
		echo " 2. Install Lynx (Legendary text based web browser)\n";
		echo " 3. Install VoodooHDA PrefPane\n";
		echo " 4. EMPTY \n";
		echo " 5. EMPTY \n";
		echo " 6. Install VoodooPS2 PrefPane (Slice version)\n";
		echo " 7. Install Chameleon $chameleonversion (Latest Certified to work with EDP)\n";
		echo " 8. Install Istat menus 2 (Awesome way to show info about your system) \n\n";
		echo " x. <-- Go back to last menu \n";
		echo " q. Quit - don't do anything. \n\n";
		echo "$footer\n";
		echo "Please choose: ";
		$choice = getChoice();	
		return $choice;
					
	}
	
	
?>