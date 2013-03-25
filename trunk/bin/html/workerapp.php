<?php

$action = $_GET['action'];
$id 	= $_GET['id'];
$url 	= $_GET['url'];

if ($action == "goto_hell") {
	session_start();
	session_destroy();
	echo "If the window does not close automatically you may close it now";
	echo "<script>top.window.close();</script>";
	
	exit;
	
}


include_once "header.inc.php";
include_once "../functions.inc.php";
include_once "../config.inc.php";
include_once "include/functions.edpweb.inc.php";






if ($action == "") {
    echo "No action defined..";
    exit;
}


if ($action == "browseURL") {
	echoPageItemTOP("icons/big/globe.png", "Browsing remote url...");
	echo "<div class='pageitem_bottom'>\n";	
	echo "<ul class='pageitem'>\n";
	echo "<li class='textbox'>\n";
	echo "<iframe id=\"browser\" marginwidth=\"0\" marginheight=\"0\" border=\"0\" frameborder=\"0\" height=\"600px\" width=\"100%\" src=\"$url\"></iframe>\n";			
	echo "</li>\n";
	echo "</ul>\n";	
}
if ($action == "showCredits") {
	//Fetch data for ID
	$stmt = $edp_db->query("SELECT * FROM credits where id = '$id'");
	$stmt->execute();
	$bigrow = $stmt->fetchAll(); $row = $bigrow[0];

	echoPageItemTOP("icons/big/$row[icon]", "$row[name]");
	echo "<div class='pageitem_bottom'>\n";
	echo "<br>";
	echo "<span class='graytitle'>Info</span><br>";
	echo "<ul class='pageitem'>\n";
	echo "<li class='textbox'>\n";
	echo "<p><b>Name:</b> $row[name]</p>\n";
	echo "<p><b>Type:</b> $row[type]</p>\n";	
	echo "<p><b>Creator:</b> $row[owner]</p>\n";
	echo "<p><b>E-mail:</b> $row[contactemail]</p><br>\n";
	echo "<p><b>Website:</b> <a href=\"$row[inforurl]\">$row[inforurl]</a></p>\n";
	echo "<p><b>Donate to support:</b><br><a href=\"$row[donationurl]\">$row[donationurl]</a></p>\n";			
	echo "</li>\n";
	echo "</ul>\n";
	echo "<br>";	
	echo "<span class='graytitle'>About $row[name]</span><br>";
	echo "<ul class='pageitem'>\n";
	echo "<li class='textbox'>\n";
	echo "<p>$row[description]</p>\n";
	echo "</li>\n";
	echo "</ul>\n";
	
	
	exit;	
}


if ($action == "dellBiosCrack") {
    global $workpath;
    system_call("open $workpath/bin/DELLBiosPWgen");
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
    global $slepath;
    echo "<br><br>Touching $slepath... <br><br>Notice: This might tricker a kernelcache rebuild...<br><br>";
    system_call("touch $slepath");
    exit;
}
if ($action == "toggle-hibernation") {
    echo "<pre>";
    fixes_toggleHibernationMode();
    exit;
}
if ($action == "console-slow-start") {
    echo "<pre>";
    system_call("sudo rm -rf /private/var/log/asl/*.asl");
    echo "Fix applied.. <br>";
    exit;
}
if ($action == "fix-sound-delay") {
    echo "<pre>";
    fixes_soundelay();
    exit;
}

if ($action == "fix-biometric") {
    echo "<pre>";
    system_call("mkdir /downloads; cd /downloads; curl -O http://www.osxlatitude.com/files/ProtectorSuite.dmg");
    system_call("hdiutil attach /downloads/ProtectorSuite.dmg");
    system_call("open \"/Volumes/Protector Suite/ProtectorSuite.pkg\"");
    echo "<br>Installer launched...";
    exit;
}
if ($action == "fix-sound-delay") {
    echo "<pre>";
    system_call("mkdir /downloads; cd /downloads; curl -O http://www.osxlatitude.com/files/Soundflower.dmg");
    system_call("hdiutil attach /downloads/Soundflower.dmg");
    system_call("open /Volumes/Soundflower-1.5.2/Soundflower.pkg");
    echo "<br>Installer launched...";
    exit;
}
if ($action == "fix-console-colors") {
    echo "<pre>";
    system_call("echo export CLICOLOR=1 >>~/.bash_profile");
    system_call("echo export LSCOLORS=ExFxCxDxBxegedabagacad >>~/.bash_profile");
    echo "Fix applied..";
    exit;
}
if ($action == "fix-reset-display") {
    echo "<pre>";
    system_call("rm -f /Library/Preferences/com.apple.window*");
    system_call("rm -f ~/Library/Preferences/ByHost/com.apple.window*");
    system_call("rm -f ~/Library/Preferences/ByHost/com.apple.pref*");
    echo "Fix applied..<br>";
    echo "Connect your screen again to use it in extended mode after a reboot..<br>";
    exit;
}
if ($action == "fix-airdrop") {
    echo "<pre>";
    global $workpath;
    system_call("open $workpath/storage/apps/ShowAirDrop.app");
    echo "App launched.. <br>";
    exit;
}

if ($action == "fix-spdisplays") {
    echo "<pre>";
    system_call("/Extra/bin/fixes/Color-LCD-fix.sh");
    exit;
}

if ($action == "fix-icloud-recoveryhd") {
    echo "<pre>";
    system_call("/Extra/bin/fixes/icloud-recoveryhd.sh");
    exit;
}

if ($action == "fix-icloud-nvram") {
    echo "<pre>";
    system_call("/Extra/bin/fixes/icloud-nvram.sh");
    exit;
}

if ($action == "toggle-hidpi") {
    echo "<pre>";
    echo "Enabling HiDPI mode in Mountain Lion.<br><br>";
    system_call("sudo defaults write /Library/Preferences/com.apple.windowserver DisplayResolutionEnabled -bool YES");
    system_call("sudo defaults delete /Library/Preferences/com.apple.windowserver DisplayResolutionDisabled");
    echo "<br>HiDPI mode enabled! Please logout and back in for this to take effect..<br>";
    echo "Go to [Apple menu --> System Preferences --> Displays --> Display --> Scaled] after logging back in.<br>";
    echo "You will see a bunch of 'HiDPI' resolutions in the list to choose from.";
    exit;
}

if ($action == "update-edp") 	{ echo "<pre>"; global $edpmode; $edpmode = "web"; updateEDP(); echo "<script> window.fluid.dockBadge = ''; </script> \n"; exit; }
if ($action == "close-edpweb") 	{ echo "<pre>"; close - edpweb(); exit; }
if ($action == "changelog") 	{ showChangelog(); exit; }
if ($action == "showBuildLog")	{ showBuildLog(); exit ; }


//Functions called by this script

function showChangelog() {
	echoPageItemTOP("icons/big/xcode.png", "Changelog for EDP");
    echo "<div class='pageitem_bottom'>\n";
    
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

function fixes_toggleHibernationMode() {
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
	echo "<body onload=\"JavaScript:timedRefresh(10000);\">";	
	echoPageItemTOP("icons/big/logs.png", "Building configuration...");
	echo "<div class='pageitem_bottom'>\n";	
	include "$workpath/build.log";			
	echo "<script type=\"text/JavaScript\"> function timedRefresh(timeoutPeriod) { setTimeout(\"location.reload(true);\",timeoutPeriod); } </script>\n";
}

?>