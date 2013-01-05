<?

	function loadFixSystem() {
		$choice = showFixMenu();
		if ($choice == "1")  { fixes_touch_sle(); }
		if ($choice == "2")  { fixes_sleepfix(); }
		if ($choice == "3")  { fixes_slowconsole(); }
		if ($choice == "4")  { fixes_soundelay(); }	
		if ($choice == "5")  { fixes_biometric(); }
		if ($choice == "6")  { fixes_console_colors(); }	
		if ($choice == "7")  { fixes_reset_display(); }
		if ($choice == "8")  { fixes_airdrop(); }
		if ($choice == "9")  { fixes_toogleHibernationMode(); }
		if ($choice == "x")  { loadMainSystem(); }
		if ($choice == "q")  { exit; }			
		
	}


	function showFixMenu() {
		include "config.inc.php";
		system("clear");
		echo "$header\n\n";
		echo " This section contains a lot of solutions/fixes to everyday small problems..\n\n";
		echo "What do you wanna do ? \n\n";
		echo "  1.  Touch /System/Library/Extensions/ (Fix alot of weird issues)\n";
		echo "  2.  Disable Hibernation (part of getting sleep to work)\n";
		echo "  3.  Fix console slow start issue\n";
		echo "  4.  Fix sound delay (Install soundflower)\n";
		echo "  5.  Fix biometric reader (Install Protector suite)\n";
		echo "  6.  Add file/dir colors to console\n";
		echo "  7.  Reset displays (fixes video corruption after mirror mode selection)\n";
		echo "  8.  Enable Aidrop\n";
		echo "  9.  Toogle Hibernation - Currently set to mode: $hibernatemode (0=off, 3 = on)\n"; 
		echo "  x.  <-- Go back to last menu \n";
		echo "  q. Quit - don't do anything. \n\n";
		echo "$footer\n\n";	
		echo " Please choose: ";
		$choice = getChoice();
		return "$choice";			
	}
	
	
	
	function fixes_touch_sle() {
		global $slepath;
		system("touch $slepath");
		system("clear"); echo "Fix applied.. return to menu in 3 secs.."; system("sleep 3");
		loadFixSystem();
	}
	
	function fixes_sleepfix() {
		system("pmset hibernatemode 0");
		system("touch /var/vm/sleepimage");
		system("rm /var/vm/sleepimage");
		system("clear"); echo "Fix applied.. return to menu in 3 secs.."; system("sleep 3");
		loadFixSystem();	
	}
	
	function fixes_slowconsole() {
		system("sudo rm -rf /private/var/log/asl/*.asl");
		system("clear"); echo "Fix applied.. return to menu in 3 secs.."; system("sleep 3");
		loadFixSystem();		
	}
	
	function fixes_soundelay() {
	    system("mkdir /downloads; cd /downloads; curl -O http://www.osxlatitude.com/files/Soundflower.dmg");
		system("hdiutil attach /downloads/Soundflower.dmg");
		system("open /Volumes/Soundflower-1.5.2/Soundflower.pkg");
		system("clear"); echo "Fix applied.. return to menu in 3 secs.."; system("sleep 3");
		loadFixSystem();		
	}
	
	function fixes_biometric() {
		system("mkdir /downloads; cd /downloads; curl -O http://www.osxlatitude.com/files/ProtectorSuite.dmg");
		system("hdiutil attach /downloads/ProtectorSuite.dmg");
		system("open \"/Volumes/Protector Suite/ProtectorSuite.pkg\"");
		system("clear"); echo "Fix applied.. return to menu in 3 secs.."; system("sleep 3");
		loadFixSystem();			
	}
	
	function fixes_console_colors() {
		system("echo export CLICOLOR=1 >>~/.bash_profile");
        system("echo export LSCOLORS=ExFxCxDxBxegedabagacad >>~/.bash_profile");
		system("clear"); echo "Fix applied.. return to menu in 3 secs.."; system("sleep 3");
		loadFixSystem();        
	}
	
	function fixes_reset_display() {
		system("rm -f /Library/Preferences/com.apple.window*");
        system("rm -f ~/Library/Preferences/ByHost/com.apple.window*");
        system("rm -f ~/Library/Preferences/ByHost/com.apple.pref*");
		system("clear");
		echo "Fix applied.. return to menu in 3 secs..\n";
		echo "Connect your screen again to use it in extended mode after a reboot..\n";
		system("sleep 3");
		loadFixSystem();        
	
	}


	function fixes_toogleHibernationMode() {
		global $hibernatemode;
		system("clear");
				
		if ($hibernatemode == "3") {
			echo "Disabling hibernation... \n";
			system("pmset -a hibernatemode 0");
			if (is_file("/var/vm/sleepimage")) { echo "Hibernation file was found... removing.. \n\n"; system("rm -Rf /var/vm/sleepimage"); }
			echo "Fix applied.. return to menu in 3 secs..\n";
			system("sleep 3");
			loadFixSystem();  			
		}
		if ($hibernatemode == "0") {
			echo "Enabling hibernation... \n\n";
			system("pmset -a hibernatemode 3");
			echo "Fix applied.. return to menu in 3 secs..\n";
			system("sleep 3");
			loadFixSystem(); 
		} 								
		
	}
			

	function fixes_airdrop() {
		system("open $workpath/storage/apps/ShowAirDrop.app");
		system("clear"); echo "Fix applied.. return to menu in 3 secs.."; system("sleep 3");
		loadFixSystem(); 		
	}
	
?>
