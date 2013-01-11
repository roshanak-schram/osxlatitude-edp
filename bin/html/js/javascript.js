        function bootloader() {
                aligndesign();
        }

        function aligndesign() {
                if (document.body && document.body.offsetWidth) {
                        w = document.body.offsetWidth;
                        h = document.body.offsetHeight;
                }
                if (document.compatMode=='CSS1Compat' &&
                        document.documentElement &&
                        document.documentElement.offsetWidth ) {
                        w = document.documentElement.offsetWidth;
                        h = document.documentElement.offsetHeight;
                }
                if (window.innerWidth && window.innerHeight) {
                        w = window.innerWidth;
                        h = window.innerHeight;
                }


                //Calculate and correction posetion of the console
                var console_newleft = w-1200;
                console_newleft = console_newleft/2;
                document.getElementById('console_container').style.left = console_newleft;	
	
                //Calculate and correction posetion of the navbar
                var navbar_newleft = w-1180;
                navbar_newleft = navbar_newleft/2;
                document.getElementById('navbar_container').style.left = navbar_newleft;
                
                //Calculate and realign the console
                document.getElementById('console_pageshadow').style.height = h-173;
                document.getElementById('console_actual').style.height = h-200;
                document.getElementById('console_bottomshadow').style.top = h-160;
                
                

        }
        
        function navbar_button_mouseover(id) {
                var topbar = id+'_topbar';
                document.getElementById(id).style.background = '#222222';		
                document.getElementById(topbar).style.background = '#A20013';
        }

        function navbar_button_mouseout(id) {
                var topbar = id+'_topbar';

                if (top.activepage == id) {
                	document.getElementById(id).style.background = '#222222';		
                	document.getElementById(topbar).style.background = '#A20013';                
                }                
                if (top.activepage != id) {
                	document.getElementById(topbar).style.background = '';
                	document.getElementById(id).style.background = '';
                }
        }

        function load(id, page) {
	        top.activepage = id;
	        
	        //Deselect all menus
	        navbarDeselect('config_button'); navbarDeselect('fixes_button'); navbarDeselect('tools_button'); navbarDeselect('installer_button'); navbarDeselect('update_button');
	        
            var topbar = id+'_topbar';            
            
            document.getElementById(id).style.background = '#222222';		
            document.getElementById(topbar).style.background = '#A20013';
                	        
	        if (page == "configuration") 	{ document.getElementById("console_actual").src = 'configuration.php'; 	changeTopbarTitle('System Configuration'); }
	        if (page == "updateEDP")		{ document.getElementById("console_actual").src = 'update.php'; 		changeTopbarTitle('Update EDP to latest version...'); }
	        if (page == "test")				{ document.getElementById("console_actual").src = 'test.php'; }	        
        }
        
        function navbarDeselect(id) {
                	var topbar = id+'_topbar';	        
                	document.getElementById(topbar).style.background = '';
                	document.getElementById(id).style.background = '';	        
        }
        
        function changeTopbarTitle(new_title) {
	    	document.getElementById("console_topbar_title").innerHTML = new_title;
        }
        