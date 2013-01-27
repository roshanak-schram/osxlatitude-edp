<?
 
global $modeldb; 
$modeldb = array(
					//This one have to be empty, its used for when we do custom builds...
					array( 	name 			=> "", 
                      		desc 			=> "",
                      		nullcpu 		=> "",
                      		sleepEnabler 	=> "",                      		
                      		ps2pack 		=> "",
                      		emulatedST 		=> "",                      		
                      		tscsync 		=> "",                      		
                      		batteryKext		=> "1",
                      		loadIOATAFamily	=> "",
                      		loadNatit		=> "yes",
                      		useACPIfix		=> "yes",
                      		patchCPU		=> "",
                      		ethernet		=> "",
                      		audiopack		=> "1",
                      		supports_sl		=> "",
                      		supports_lion	=> "",
                      		supports_ml		=> "",                      		                      		                      		
                      		customCham 		=> "",                      		
                      		customKernel 	=> ""                      		 
                    ), 	



                	array( 	name 			=> "HP_HDX_X16-1370US", 
                      		desc 			=> "HP HDX X16-1370US",
                      		nullcpu 		=> "no",
                      		sleepEnabler 	=> "no",                      		
                      		ps2pack 		=> "",
                      		emulatedST 		=> "no",                      		
                      		tscsync 		=> "no",                      		
                      		batteryKext		=> "1",
                      		loadIOATAFamily	=> "",
                      		loadNatit		=> "no",
                      		useACPIfix		=> "yes",                      		
                      		ethernet		=> "RealtekRTL81xx.kext",
                      		audiopack		=> "2",                      		                      		
                      		supports_sl		=> "yes",
                      		supports_lion	=> "yes",
                      		supports_ml		=> "yes",                      		
                      		customCham 		=> "no",                      		
                      		customKernel 	=> "no"                     		 
                    ),
                      
                	array( 	name 			=> "HP_ProBook_6360b", 
                      		desc 			=> "HP ProBook 6360b",
                      		nullcpu 		=> "no",
                      		sleepEnabler 	=> "no",                      		
                      		ps2pack 		=> "0",
                      		emulatedST 		=> "no",                      		
                      		tscsync 		=> "no",                      		
                      		batteryKext		=> "0",
                      		loadIOATAFamily	=> "no",
                      		loadNatit		=> "no",
                      		useACPIfix		=> "no",
                      		patchCPU		=> "yes",                      		                    		
                      		ethernet		=> "AppleIntelE1000e.kext",
                      		audiopack		=> "",                      		                      		
                      		supports_sl		=> "yes",
                      		supports_lion	=> "yes",
                      		supports_ml		=> "yes",                      		
                      		customCham 		=> "no",                      		
                      		customKernel 	=> "no"                   		 
                    ),
                                          
                	array( 	name 			=> "HP_Pavilion_dv6401eo", 
                      		desc 			=> "HP Pavilion dv6401eo",
                      		nullcpu 		=> "yes",
                      		sleepEnabler 	=> "yes",                      		
                      		ps2pack 		=> "0",
                      		emulatedST 		=> "yes",                      		
                      		tscsync 		=> "yes",                      		
                      		batteryKext		=> "0",
                      		loadIOATAFamily	=> "no",
                      		loadNatit		=> "no",
                      		useACPIfix		=> "no",
                      		patchCPU		=> "no",                      		                    		
                      		ethernet		=> "IntelPro100.kext",
                      		audiopack		=> "2",                      		                      		
                      		supports_sl		=> "yes",
                      		supports_lion	=> "yes",
                      		supports_ml		=> "no",                      		
                      		customCham 		=> "no",                      		
                      		customKernel 	=> "no"                   		 
                    ),
                                                                                                                                                                                     
             );



?>
