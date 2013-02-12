<?php
	$i = $_GET['i'];
	if ($i == "") { $i="Home"; }
	
	include "header.inc.php";
	include_once "../config.inc.php";
	global $edpmode; $edpmode = "web";
?>

<body onload="bootloader();" onresize="aligndesign();">



<div id="topbar" class="black">
		<?php
			if ($i != "Home") {
				echo "<div id='leftnav'><a href='index.php'><img alt='home' src='images/home.png' />&nbsp;</a></div>";
			}
			else { }
		?>
	<div id="title">EDP <?="$edpversion";?></div>
	
	<div id="rightbutton"><a href="#" onclick="loadModule('workerapp.php?action=close-edpweb');" class="noeffect">&nbsp; Close &nbsp;</a> </div>
</div>
	


<?php
include "tributton.menu.inc.php";
?>

<iframe id="edpmenu" class="edpmenu" marginwidth="0" marginheight="0" border="0" frameborder="0" height="80%" src="menu.inc.php?i=<?="$i";?>"></iframe>

<iframe id="console_iframe" class="console_iframe" marginwidth="0" marginheight="0" border="0" frameborder="0" src="welcome.php"></iframe>



<script charset="utf-8">

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


        //Calculate and correction of the console_iframe
        var console_iframe_width = w-285;
        var console_iframe_height = h-80;
        document.getElementById('console_iframe').style.width = console_iframe_width+'px';	
        document.getElementById('console_iframe').style.height = console_iframe_height+'px';
                
        //Calculate and correction of menu dev
        var edpmenu_div_height = h-76;
        document.getElementById('edpmenu').style.height = edpmenu_div_height+'px';        
	
        	
    }
    
    function loadModule(page) {
	    top.document.getElementById('console_iframe').src = page;
    }

</script>

</body>

</html>
