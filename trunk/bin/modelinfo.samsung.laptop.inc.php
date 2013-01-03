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

 
                    
                     array( name 			=> "Samsung_R530_JT02", 
                      		desc 			=> "Samsung R530 JT02",
                      		nullcpu 		=> "no",
                      		sleepEnabler 	=> "no",                      		
                      		ps2pack 		=> "7",
                      		emulatedST 		=> "no",                      		
                      		tscsync 		=> "no",                      		
                      		batteryKext		=> "3",
                      		loadIOATAFamily	=> "",
                      		loadNatit		=> "no",
                      		useACPIfix		=> "yes",
                      		patchCPU		=> "yes",                       		
                      		ethernet		=> "no",
                      		audiopack		=> "7",                      		                      		
                      		supports_sl		=> "yes",
                      		supports_lion	=> "yes",
                      		supports_ml		=> "yes",                      		
                      		customCham 		=> "no",                      		
                      		customKernel 	=> "no"                     		 
                    ),    
                                        
                          
                    array( 	name 			=> "Samsung_np_900x3c", 
                      		desc 			=> "Samsung NP 900X3C",
                      		nullcpu 		=> "no",
                      		sleepEnabler 	=> "no",                      		
                      		ps2pack 		=> "5",
                      		emulatedST 		=> "no",                      		
                      		tscsync 		=> "no",                      		
                      		batteryKext		=> "3",
                      		loadIOATAFamily	=> "no",
                      		loadNatit		=> "no",
                      		useACPIfix		=> "yes",
                      		patchCPU		=> "yes",                      		                    		
                      		ethernet		=> "",
                      		audiopack		=> "",                      		                      		
                      		supports_sl		=> "no",
                      		supports_lion	=> "yes",
                      		supports_ml		=> "yes",                      		
                      		customCham 		=> "no",                      		
                      		customKernel 	=> "no"                      		 
                    ),                                                                                                                                                                   
             );



?>
