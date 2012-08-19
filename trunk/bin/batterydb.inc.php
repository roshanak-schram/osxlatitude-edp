<?

global $batterydb;
$batterydb = array( 
		//This one have to be empty, its used for when we do custom builds...

		array( 	name 		=> "Voodoo Battery", 
                kextname 	=> "VoodooBattery.kext",
                arch		=> "x86_x64"
             ),
                         
		array( 	name 		=> "Apple ACPI Battery Manager", 
                kextname 	=> "AppleACPIBatteryManager.kext",
                arch		=> "x86_x64"
             ),

		array( 	name 		=> "Apple SmartBattery Manager", 
                kextname 	=> "AppleSmartBatteryManager.kext",
                arch		=> "x86_x64"	
             ), 

        );
?>
