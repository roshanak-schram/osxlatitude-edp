<?

global $ps2db;
$ps2db = array( 
		//This one have to be empty, its used for when we do custom builds...

		array( 	name 		=> "myHack default", 
                foldername 	=> "myhackps2",
                arch		=> "x86_x64",
                notes 		=> "myHack 3.1 default ps2 kexts"
             ),
                         
		array( 	name 		=> "Apple's PS2 controller", 
                foldername 	=> "appleps2controller",
                arch		=> "x86",
                notes		=> "Standard package for many ALPS pads, have support for 2 finger scroll and the track point works"
             ),

        array(  name 		=> "VoodooPS2Controller (debug version; suited for Dell D4x0/D520)",
                foldername 	=> "vps2c-debug",
			    arch		=> "x86",
                notes		=> "General good package for ALPS pads, side/bottom scroll works, mouse works great"
             ),  

        array(  name 		=> "Slice's ApplePS2Controller",
                foldername 	=> "slicePS2Controller",
                arch    	=> "x86",
                notes	 	=> "Works very well for most trackpads, its a beta driver, so there might be unknown bugs"
             ),  

        array(  name 		=> "ANV-Slice Modified VoodooPS2Controller",
                foldername 	=> "ANV-SliceModified",
                arch    	=> "x86_x64",
                notes	 	=> "Allthough its a beta driver it works well on most ALPS pads, scrolling is a little too fast"
             ),  

        array(  name 		=> "Apple's PS2 controller for Dell E generation laptops",
                foldername 	=> "appleps2controller2",
                arch    	=> "x86_x64",
                notes	 	=> "It works on the Dell e6220, no multi touch, gestures or on pad scrolling"
             ),  
             
        array(  name 		=> "Apple's PS2 controller for Samsung R530",
                foldername 	=> "appleps2controller3",
                arch    	=> "x86_x64",
                notes	 	=> "PS2controller, made for the Samsung R530 ALPS."
             ),   
                       
        array(  name 		=> "Apple's PS2 controller for Dell E65xx",
                foldername 	=> "appleps2controller3",
                arch    	=> "x86_x64",
                notes	 	=> "It works on the Dell E6520, no multi touch, gestures or on pad scrolling"
             ),        
                                                                             
        );

?>
