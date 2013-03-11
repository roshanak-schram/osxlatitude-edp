<?php

$i = (isset($_GET['i'])) ? $_GET['i'] : "";

include_once "../config.inc.php";
include "header.inc.php";


global $edpmode;
$edpmode = "web";


                
                
?>
    <body onload="bootloader();" onresize="aligndesign();">
    	
<div style="position: absolute; display: none; width: 100%; height: 100%; z-index: 10000; left: 50px; top: 0px; background-image: url('images/semi-transparent.png')" id="wait">
	<table border="0" width="100%" cellpadding="0" style="border-collapse: collapse" height="100%">
		<tr><td height="48%" width="48%"></td><td height="48%"></td><td height="48%" width="48%"></td></tr>
		<tr><td width="48%">&nbsp;</td><td><p align="center"><img border="0" src="images/spinning-wheel.gif"><br><br><font color="#FFFFFF">Loading....</font><font></td><td width="48%">&nbsp;</td></tr>
		<tr><td height="48%" width="48%">&nbsp;</td><td height="48%">&nbsp;</td><td height="48%" width="48%">&nbsp;</td></tr>
	</table>	
</div>

        <table border="0" width="100%" cellpadding="0" style="border-collapse: collapse" background="images/topbar-bg-normal2.png" height="69">
            <tr style="vertical-align: bottom;"  class="topbarmenu" align="center" style='cursor: hand'>
            	<td width="80" onclick="loader('EDP')"><img src="icons/edp.png" width="38"></td>
                <td width="80" onclick="loader('Applications')"><img src="icons/fixes.png" width="40"></td>
                <td width="80" onclick="loader('Tools')"><img src="icons/tools.png" width="40"></td>
                            	
                <td width="80" onclick="loader('Configuration')"><img src="icons/sysprefs.png" width="40"></td>
                <td width="80" onclick="loader('Fixes')"><img src="icons/emergency.png" width="40"></td>


                <td>&nbsp;</td>
                <td width="80" onclick="loader('Credits');"><img src="icons/credits.png" width="40"></td>                
                <td width="80"><a href='https://code.google.com/p/osxlatitude-edp/issues/list' target='_blank'><img src="icons/issues.png" width="40"></a></td>
                <td width="80"><a href='<?= "$donateurl"; ?>' target='_blank'><img src="icons/paypal.png" width="40"></a></td>
            </tr>
            <tr class="topbarmenu" align="center" style='cursor: hand'>
            	<td>EDP</td>
                <td>Applications</td>
                <td>Tools</td>            	
                <td>Config</td>
                <td>Fixes</td>
                <td>&nbsp;</td>
                <td>Credits</td>                
                <td>Issues</td>
                <td>Donate</td>
            </tr>
        </table>

		<iframe id="edpmenu" class="edpmenu" marginwidth="0" marginheight="0" border="0" frameborder="0" height="80%" src="menu.inc.php?i=<?= "$i"; ?>"></iframe>

        <iframe id="console_iframe" class="console_iframe" marginwidth="0" marginheight="0" border="0" frameborder="0" src="show.php?i=pages/edp.inc.php"></iframe>


    </body>
</html>
