<?php
	$i = $_GET['i'];
	if ($i == "") { $i="Home"; }
	
	include "header.inc.php";
?>

<body onload="bootloader();" onresize="aligndesign();" background="../images/background.png">


<div id="topbar">
		<?php
			if ($i != "Home") {
				echo "<div id='leftnav'><a href='index.php'><img alt='home' src='images/home.png' /></a></div>";
			}
			else { echo "<div id='leftbutton'><a href='edp.news.php' class='noeffect'>Changelog</a> </div>"; }
		?>
	<div id="title">EDP <?="$edpversion";?></div>
	
	<div id="rightbutton"><a href="#" onclick="loadModule('workerapp.php?action=update-edp');" class="noeffect">Update!</a> </div>
</div>
<?php
include "tributton.menu.inc.php";
?>


<div id="content" style="width: 100%; height: 100%">





	<ul class="pageitem" id="console_iframe_div" style="left: 275px">
		<iframe id="console_iframe" style="padding: 2px;" marginwidth="0" marginheight="0" border="0" frameborder="0" height="80%" src="welcome.php"></iframe>
	</ul>

	<iframe id="menu" style="position: 	absolute; top: 0px; left: 0px; height: 500px; width: 275px" marginwidth="0" marginheight="0" border="0" frameborder="0" height="80%" src="menu.inc.php?i=<?="$i";?>"></iframe>



	
</div>


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


        //Calculate and correction posetion of the console_iframe_div
        var console_iframe_div_width = w-300;
        var console_iframe_width = w-305;
        var console_iframe_height = h-123;
                
        document.getElementById('console_iframe_div').style.width = console_iframe_div_width+'px';
        document.getElementById('console_iframe').style.width = console_iframe_width+'px';	
        document.getElementById('console_iframe').style.height = console_iframe_height+'px';	
        	
    }
    
    function loadModule(page) {
	    top.document.getElementById('console_iframe').src = page;
    }
</script>

</body>

</html>
