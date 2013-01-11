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

        }
        
        function navbar_button_mouseover(id) {
                var topbar = id+'_topbar';
		document.getElementById(id).style.background = '#222222';		
                document.getElementById(topbar).style.background = '#A20013';
        }

        function navbar_button_mouseout(id) {
                var topbar = id+'_topbar';
                document.getElementById(topbar).style.background = '';
                document.getElementById(id).style.background = '';
        }

        function load(page) {
	        if (page == "configuration") 	{ document.getElementById("console_actual").src = 'configuration.php'; }
	        if (page == "updateEDP")		{ document.getElementById("console_actual").src = 'update.php'; }
        }