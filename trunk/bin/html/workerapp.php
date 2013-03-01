<?php

include_once "header.inc.php";
include_once "../functions.inc.php";
include_once "../config.inc.php";
include "include/watermark.inc.php";

$action = $_GET['action'];

if ($action == "") {
    echo "No action defined..";
    exit;
}
if ($action == "dellBiosCrack") {
    runDellBiosCrack();
    exit;
}

if ($action == "dellBiosCrack") {
    runDellBiosCrack();
    exit;
}
if ($action == "install-mc") {
    echo "<pre>";
    downloadAndRun("http://www.osxlatitude.com/files/mc.dmg", "dmg", "mc.dmg", "/Volumes/mc.pkg/mc.pkg");
    exit;
}
if ($action == "install-htop") {
    echo "<pre>";
    downloadAndRun("http://www.osxlatitude.com/files/htop.pkg", "pkg", "htop.pkg", "/downloads/htop.pkg");
    exit;
}
if ($action == "install-lynx") {
    echo "<pre>";
    downloadAndRun("http://www.osxlatitude.com/files/lynx.dmg", "dmg", "lynx.dmg", "/Volumes/Lynx-2.8.7d9-10.5.1+u/install.command");
    exit;
}
if ($action == "install-istat2") {
    echo "<pre>";
    downloadAndRun("http://www.osxlatitude.com/files/istat2.dmg", "dmg", "istat2.dmg", "/Volumes/istat2/install.app");
    exit;
}

if ($action == "fix-touch-sle") {
    echo "<pre>";
    fixes_touch_sle();
    exit;
}
if ($action == "toogle-hibernation") {
    echo "<pre>";
    fixes_toogleHibernationMode();
    exit;
}
if ($action == "console-slow-start") {
    echo "<pre>";
    fixes_slowconsole();
    exit;
}
if ($action == "fix-sound-delay") {
    echo "<pre>";
    fixes_soundelay();
    exit;
}

if ($action == "fix-biometric") {
    echo "<pre>";
    fixes_biometric();
    exit;
}
if ($action == "fix-sound-delay") {
    echo "<pre>";
    fixes_soundelay();
    exit;
}
if ($action == "fix-console-colors") {
    echo "<pre>";
    fixes_console_colors();
    exit;
}
if ($action == "fix-reset-display") {
    echo "<pre>";
    fixes_reset_display();
    exit;
}
if ($action == "fix-airdrop") {
    echo "<pre>";
    fixes_airdrop();
    exit;
}
if ($action == "fix-spdisplays") {
    echo "<pre>";
    fixes_spdisplays();
    exit;
}
if ($action == "update-edp") 	{ echo "<pre>"; global $edpmode; $edpmode = "web"; updateEDP(); exit; }
if ($action == "close-edpweb") 	{ echo "<pre>"; close - edpweb(); exit; }
if ($action == "changelog") 	{ showChangelog(); exit; }
if ($action == "showBuildLog")	{ showBuildLog(); exit ; }


//Functions called by this script

function showChangelog() {
    echo "<div id='content'>\n";
    
    $url = "http://pipes.yahoo.com/pipes/pipe.run?_id=fcf8f5975800dd5f04a86cdcdcef7c4d&_render=rss";
    $xml = new SimpleXmlElement(file_get_contents($url));

    foreach ($xml->channel->item as $item) {
        echo '<ul class="pageitem"><li class="textbox">';
        echo '<span class="header">' . $item->title . '</span>';
        echo '<p>' . trim($item->description) . '</p><br/>';
        echo '<p>Commited on: ' . date('l jS \of F Y h:i:s A', strtotime($item->pubDate)) . '</p></li></ul>';
    }
    
    echo "</div>\n";
}

//function close-edpweb() {
//	system_call("kill $(ps aux | grep 'jetty' | awk '{print $2}')");
//	echo "<script> alert('You may now close the window...'); window.close();</script>";
//}

function runDellBiosCrack() {
    global $workpath;
    system_call("open $workpath/bin/DELLBiosPWgen");
}

function fixes_slowconsole() {
    system_call("sudo rm -rf /private/var/log/asl/*.asl");
    echo "Fix applied.. <br>";
}
function fixes_soundelay() {
    system_call("mkdir /downloads; cd /downloads; curl -O http://www.osxlatitude.com/files/Soundflower.dmg");
    system_call("hdiutil attach /downloads/Soundflower.dmg");
    system_call("open /Volumes/Soundflower-1.5.2/Soundflower.pkg");
    echo "<br>Installer launched...";
}
function fixes_console_colors() {
    system_call("echo export CLICOLOR=1 >>~/.bash_profile");
    system_call("echo export LSCOLORS=ExFxCxDxBxegedabagacad >>~/.bash_profile");
    echo "Fix applied..";
}
function fixes_airdrop() {
    global $workpath;
    system_call("open $workpath/storage/apps/ShowAirDrop.app");
    echo "App launched.. <br>";
}
function fixes_reset_display() {
    system_call("rm -f /Library/Preferences/com.apple.window*");
    system_call("rm -f ~/Library/Preferences/ByHost/com.apple.window*");
    system_call("rm -f ~/Library/Preferences/ByHost/com.apple.pref*");
    echo "Fix applied..<br>";
    echo "Connect your screen again to use it in extended mode after a reboot..<br>";
}
function fixes_biometric() {
    system_call("mkdir /downloads; cd /downloads; curl -O http://www.osxlatitude.com/files/ProtectorSuite.dmg");
    system_call("hdiutil attach /downloads/ProtectorSuite.dmg");
    system_call("open \"/Volumes/Protector Suite/ProtectorSuite.pkg\"");
    echo "<br>Installer launched...";
}
function fixes_touch_sle() {
    global $slepath;
    echo "<br><br>Touching $slepath... <br><br>Notice: This might tricker a kernelcache rebuild...<br><br>";
    system_call("touch $slepath");
}
function fixes_toogleHibernationMode() {
    global $hibernatemode;
    
    if ($hibernatemode == "3") {
        echo "Disabling hibernation... <br>";
        system_call("pmset -a hibernatemode 0");
        if (is_file("/var/vm/sleepimage")) {
            echo "Hibernation file was found... removing.. <br>";
            system("rm -Rf /var/vm/sleepimage");
        }
        echo "Fix applied..<br>";
    }
    
    if ($hibernatemode == "0") {
        echo "Enabling hibernation... <br>";
        system_call("pmset -a hibernatemode 3");
        echo "Fix applied..<br>";
    }
}


function showBuildLog() {
	global $workpath;
	include "header.inc.php";
	echo "<body onload=\"JavaScript:timedRefresh(5000);\">";
	echo "<span class='console'>";
	include "include/watermark.inc.php";
	include "$workpath/build.log";
	echo "<script type=\"text/JavaScript\"> function timedRefresh(timeoutPeriod) { setTimeout(\"location.reload(true);\",timeoutPeriod); } </script>\n";

}

?>