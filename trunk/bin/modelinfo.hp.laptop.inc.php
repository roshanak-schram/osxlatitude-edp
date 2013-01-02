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
                                                                                                                                                                                     
             );



?>
