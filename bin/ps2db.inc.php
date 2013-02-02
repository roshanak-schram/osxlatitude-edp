<?

global $ps2db;
$ps2db = array( 
		//This one have to be empty, its used for when we do custom builds...

		array( 	name 		=> "myHack default", 
                foldername 	=> "myhackps2",
                kextname	=> "ApplePS2Controller.kext",
                arch		=> "x86_x64",
                notes 		=> "myHack 3.1 default ps2 kexts"
             ),
                         
		array( 	name 		=> "Apple's PS2 controller", 
                foldername 	=> "appleps2controller",
                kextname	=> "ApplePS2Controller.kext",                
                arch		=> "x86",
                notes		=> "Standard package for ALPS pads, 2 finger scroll and the track point works"
             ),

        array(  name 		=> "VoodooPS2Controller (debug version)",
                foldername 	=> "vps2c-debug",
                kextname	=> "VoodooPS2Controller.kext", 
			    arch		=> "x86",
                notes		=> "Good package for ALPS pads, side/bottom scroll works, mouse works great"
             ),  

        array(  name 		=> "Slice's ApplePS2Controller",
                foldername 	=> "slicePS2Controller",
                kextname	=> "ApplePS2Controller.kext",                 
                arch    	=> "x86",
                notes	 	=> "Works for most trackpads, its a beta driver, so there might be unknown bugs"
             ),  

        array(  name 		=> "ANV-Slice Modified VoodooPS2Controller",
                foldername 	=> "ANV-SliceModified",
                kextname	=> "ApplePS2Controller.kext",                
                arch    	=> "x86_x64",
                notes	 	=> "Works well on most ALPS pads, scrolling is a little too fast"
             ),  

        array(  name 		=> "Apple's PS2 controller for Dell E generation laptops",
                foldername 	=> "appleps2controller2",
                kextname	=> "ApplePS2Controller.kext",                 
                arch    	=> "x86_x64",
                notes	 	=> "It works on the Dell e6220, no multi touch, gestures or on pad scrolling"
             ),  
             
        array(  name 		=> "Apple's PS2 controller for Samsung R530",
                foldername 	=> "appleps2controller3",
                kextname	=> "ApplePS2Controller.kext",                 
                arch    	=> "x86_x64",
                notes	 	=> "PS2controller, made for the Samsung R530 ALPS."
             ),   
                       
        array(  name 		=> "Apple's PS2 controller for Dell E65xx",
                foldername 	=> "appleps2controller3",
                kextname	=> "ApplePS2Controller.kext",                 
                arch    	=> "x86_x64",
                notes	 	=> "Works on the Dell E6520, no multi touch, gestures or on pad scrolling"
             ),        

        array(  name 		=> "VoodooPS2controller #1",
                foldername 	=> "voodoops2-1",
                kextname	=> "VoodooPS2Controller.kext",                
                arch    	=> "x86",
                notes	 	=> "Works good with Synaptics with both 2 fingers etc."
             ),
             
        array(  name 		=> "Elan v4 by Dinesh",
                foldername 	=> "elanv4",
                kextname	=> "ApplePS2ElanTouchpad.kext",                
                arch    	=> "x86_x64",
                notes	 	=> "Elan v4 kext by Dinesh - see forum for more info."
             ),

                                                                             
        );

?>
