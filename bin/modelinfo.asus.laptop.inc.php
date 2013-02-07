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


                	array( 	name 			=> "Asus_K53_K53SC", 
                      		desc 			=> "Asus K53 SC",
                      		nullcpu 		=> "no",
                      		sleepEnabler 	=> "no",                      		
                      		ps2pack 		=> "0",
                      		emulatedST 		=> "no",                      		
                      		tscsync 		=> "no",                      		
                      		batteryKext		=> "2",
                      		loadIOATAFamily	=> "",
                      		loadNatit		=> "no",
                      		useACPIfix		=> "yes",                      		
                      		ethernet		=> "8",
                      		audiopack		=> "1",                      		                      		
                      		supports_sl		=> "yes",
                      		supports_lion	=> "yes",
                      		supports_ml		=> "yes",                      		
                      		customCham 		=> "no",                      		
                      		customKernel 	=> "no"                     		 
                    ),      
                        
                                        
                    
					array( 	name 			=> "Asus_F70_sl", 
                      		desc 			=> "Asus F70 SL",
                      		nullcpu 		=> "yes",
                      		sleepEnabler 	=> "yes",                      		
                      		ps2pack 		=> "5",
                      		emulatedST 		=> "yes",                      		
                      		tscsync 		=> "yes",                      		
                      		batteryKext		=> "1",
                      		loadIOATAFamily	=> "no",
                      		loadNatit		=> "no",
                      		useACPIfix		=> "no",
                      		ethernet		=> "no",
                      		audiopack		=> "2",
                      		supports_sl		=> "yes",
                      		supports_lion	=> "no",
                      		supports_ml		=> "no",                      		                   	
                      		customCham 		=> "",                      		
                      		customKernel 	=> ""                      		 
                    ),        
                                                                                                                                                                   
             );



?>
