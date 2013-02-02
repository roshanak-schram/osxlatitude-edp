<?

global $audiodb;
$audiodb = array( 
		//This one have to be empty, its used for when we do custom builds...

		array( 	name 		=> "VoodooHDA #1 - Leppy version", 
                foldername 	=> "VoodooHDA1",
                arch		=> "x86_x64",
                notes 		=> "Works good on most models - No microphone support"
             ),
                         
		array( 	name 		=> "VoodooHDA #2 (standard)", 
                foldername 	=> "VoodooHDA2",
                arch		=> "x86_x64",
                notes		=> "Works on most models: No ID injection required (less stable)"
             ),

		array( 	name 		=> "VoodooHDA #3 - Version 2.8.1", 
                foldername 	=> "VoodooHDA3",
                arch		=> "x86_x64",
                notes		=> "Latest VoodooHDA - should work with allmost any machine"
             ),  

        array(  name 		=> "AppleHDA.kext - STAC9200",
                foldername 	=> "STAC9200",
                arch		=> "x86_x64",
                notes	 	=> "Patched appleHDA for STAC9200 (such as Dell Latitude D430, D520 & D620)"
             ),  

        array(  name 		=> "AppleHDA.kext - STAC9205 #1",
                foldername 	=> "STAC9205-D630",
                arch		=> "x86_x64",
                notes	 	=> "Patched appleHDA for STAC9205 (such as Dell Latitude D630)"
             ),   

        array(  name 		=> "AppleHDA.kext - STAC9205 #2",
                foldername 	=> "STAC9205-D830",
                arch		=> "x86_x64",
                notes	 	=> "Patched appleHDA for STAC9205 (such as Dell Latitude D830)"
             ), 
             
        array(  name 		=> "AppleHDA.kext - ALC269",
                foldername 	=> "ALC269-applehda",
                arch		=> "x86_x64",
                notes	 	=> "Patched appleHDA for ALC269 (such as Samsung R530)"
             ), 
             
        array(  name 		=> "AppleHDA.kext - IDT 92HD87B1",
                foldername 	=> "92HD87B1-applehda",
                arch		=> "x86_x64",
                notes	 	=> "Patched appleHDA for IDT 92HD87B1 (such as Dell Latitude E6520)"
             ),  
                                                                             
        );

?>
