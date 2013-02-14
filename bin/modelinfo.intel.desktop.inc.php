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
   
                                        
                          
                    array( 	name 			=> "Intel_NUC_DC3217IYE", 
                      		desc 			=> "Intel NUC DC3217IYE",
                      		nullcpu 		=> "no",
                      		sleepEnabler 	=> "no",                      		
                      		ps2pack 		=> "no",
                      		emulatedST 		=> "no",                      		
                      		tscsync 		=> "no",                      		
                      		batteryKext		=> "no",
                      		loadIOATAFamily	=> "no",
                      		loadNatit		=> "no",
                      		useACPIfix		=> "no",
                      		patchCPU		=> "yes",                      		                    		
                      		ethernet		=> "3",
                      		wifikext		=> "no",
                      		audiopack		=> "no",                      		                      		
                      		supports_sl		=> "no",
                      		supports_lion	=> "yes",
                      		supports_ml		=> "yes",                      		
                      		customCham 		=> "no",                      		
                      		customKernel 	=> "no"                      		 
                    ),                                                                                                                                                                   
             );



?>
