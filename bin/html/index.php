<?php

$i = (isset($_GET['i'])) ? $_GET['i'] : "";
if ($i == "") {
    $i = "Configuration";
}
include_once "../config.inc.php";
include "header.inc.php";


global $edpmode;
$edpmode = "web";
$donateurl = "https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=mail%40r2x2%2ecom&lc=US&item_name=OSXlatitude%20Donation&item_number=OSXLatitude%20Donation&no_note=0&currency_code=USD&bn=PP%2dDonationsBF%3abtn_donate_SM%2egif%3aNonHostedGuest";

                
                
?>
    <body onload="bootloader();" onresize="aligndesign();">
        <table border="0" width="100%" cellpadding="0" style="border-collapse: collapse" background="images/topbar-bg-normal2.png" height="69">
            <tr style="vertical-align: bottom;"  class="topbarmenu" align="center" style='cursor: hand'>
                <td width="90" onclick="load('index.php?i=Configuration')"><img src="icons/sysprefs.png" width="40"></td>
                <td width="90" onclick="load('index.php?i=Fixes')"><img src="icons/fixes.png" width="38"></td>
                <td width="90" onclick="load('index.php?i=Tools')"><img src="icons/tools.png" width="38"></td>
                <td width="90" onclick="loadModule('workerapp.php?action=update-edp');"><img src="icons/Installer.png" width="38"></td>
                <td width="90" onclick="loadModule('workerapp.php?action=changelog');"><img src="icons/changelog.png" width="38"></td>
                <td width="90" onclick="loadModule('workerapp.php?action=credits');"><img src="icons/credits.png" width="38"></td>
                <td>&nbsp;</td>
                <td width="90" onclick="window.open('<?= "$donateurl"; ?>')"><img src="icons/paypal.png" width="38"></td>
            </tr>
            <tr class="topbarmenu" align="center" style='cursor: hand'>
                <td onclick="load('index.php?i=Configuration')">Config</td>
                <td onclick="load('index.php?i=Fixes')">Fixes</td>
                <td onclick="load('index.php?i=Tools')">Tools</td>
                <td onclick="load('index.php?i=Configuration')">Update</td>
                <td onclick="loadModule('workerapp.php?action=changelog');">Changelog</td>
                <td onclick="loadModule('workerapp.php?action=credits');">Credits</td>
                <td>&nbsp;</td>
                <td onclick="window.open('<?= $donateurl; ?>');">Donate</td>
            </tr>
        </table>

        <iframe id="edpmenu" class="edpmenu" marginwidth="0" marginheight="0" border="0" frameborder="0" height="80%" src="menu.inc.php?i=<?= "$i"; ?>"></iframe>
        <iframe id="console_iframe" class="console_iframe" marginwidth="0" marginheight="0" border="0" frameborder="0" src="welcome.php"></iframe>

        <script charset="utf-8">
            function bootloader() {
                aligndesign();
            }
        </script>
    </body>
</html>