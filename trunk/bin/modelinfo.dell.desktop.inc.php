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
      
                                        
                	array( 	name 			=> "Dell_Optiplex_745", 
                      		desc 			=> "Dell Optiplex 745",
                      		nullcpu 		=> "yes",
                      		sleepEnabler 	=> "no",                      		
                      		ps2pack 		=> "1",
                      		emulatedST 		=> "yes",                      		
                      		tscsync 		=> "yes",                      		
                      		batteryKext		=> "",
                      		loadIOATAFamily	=> "no",
                      		loadNatit		=> "no",
                      		useACPIfix		=> "yes",                      		
                      		ethernet		=> "BCM5722D.kext",
                      		audiopack		=> "1",                      		                      		
                      		supports_sl		=> "yes",
                      		supports_lion	=> "yes",
                      		supports_ml		=> "no",                      		
                      		customCham 		=> "no",                      		
                      		customKernel 	=> "no"                      		 
                    ),                   


                	array( 	name 			=> "Dell_Optiplex_755", 
                      		desc 			=> "Dell Optiplex 755",
                      		nullcpu 		=> "yes",
                      		sleepEnabler 	=> "no",                      		
                      		ps2pack 		=> "1",
                      		emulatedST 		=> "yes",                      		
                      		tscsync 		=> "yes",                      		
                      		batteryKext		=> "",
                      		loadIOATAFamily	=> "",
                      		loadNatit		=> "yes",
                      		useACPIfix		=> "yes",
                      		ethernet		=> "Intel82566MM.kext",
                      		audiopack		=> "2",                      		                      		
                      		supports_sl		=> "yes",
                      		supports_lion	=> "yes",
                      		supports_ml		=> "no",                      		
                      		customCham 		=> "no",                      		
                      		customKernel 	=> "no"                      		 
                    ), 
                    
                    
			array( 	name 			=> "Dell_Vostro_200", 
                      		desc 			=> "Dell Vostro 200",
                      		nullcpu 		=> "no",
                      		sleepEnabler 	=> "no",                      		
                      		ps2pack 		=> "1",
                      		emulatedST 		=> "yes",                      		
                      		tscsync 		=> "yes",                      		
                      		batteryKext		=> "",
                      		loadIOATAFamily	=> "",
                      		loadNatit		=> "no",
                      		useACPIfix		=> "yes",
                      		patchCPU		=> "",
                      		ethernet		=> "Intel82566MM.kext",
                      		audiopack		=> "",
                      		supports_sl		=> "yes",
                      		supports_lion	=> "yes",
                      		supports_ml		=> "yes",                      		                      		                      		
                      		customCham 		=> "",                      		
                      		customKernel 	=> ""                      		 
                    ),
                                                                                                                                                                                                        
                    
                    
			array( 	name 			=> "Dell_Vostro_410", 
                      		desc 			=> "Dell Vostro 410",
                      		nullcpu 		=> "no",
                      		sleepEnabler 	=> "no",                      		
                      		ps2pack 		=> "",
                      		emulatedST 		=> "",                      		
                      		tscsync 		=> "",                      		
                      		batteryKext		=> "",
                      		loadIOATAFamily	=> "",
                      		loadNatit		=> "no",
                      		useACPIfix		=> "no",
                      		patchCPU		=> "",
                      		ethernet		=> "",
                      		audiopack		=> "",
                      		supports_sl		=> "yes",
                      		supports_lion	=> "yes",
                      		supports_ml		=> "yes",                      		                      		                      		
                      		customCham 		=> "",                      		
                      		customKernel 	=> ""                      		 
                    ),
                                                                                                                                                                                                        
             );



?>
