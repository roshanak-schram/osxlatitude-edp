 
 <?php

class kexts {
 
	

    //------> Function to get version from kext
    public function getKextVersion($kext) {
    	global $workpath;
    
    	if (!is_dir($kext)) { return "0.00"; }		// If $kext dosent exist we will just return 0.00
    
    	include_once "$workpath/bin/html/libs/PlistParser.inc";
    	$parser = new plistParser();
    	$plist = $parser->parseFile("$kext/Contents/Info.plist");
    	reset($plist);
    
    	while (list($key, $value) = each($plist)) {
        	if ($key == "CFBundleShortVersionString") {
            	return "$value";
            }
        }
    }



    //-----> Copys $kext to /System/Library/Extensions/
    function copyKextToSLE($kext, $frompath) {
    	global $slepath, $workpath;

    	//Create backup folder
    	date_default_timezone_set('UTC');
    	$date = date("d-m-Y");
    	$backupfolder = "/backup/$date";
    	system_call("mkdir /backup");
    	system_call("mkdir $backupfolder");
    	system_call("rm -Rf $backupfolder/*");
    
    	//Do backup
    	echo "Copying old $slepath/$kext to $backupfolder \n";
    	system_call("cp -R $slepath/$kext $backupfolder");

    	//Remove the present kext
    	system_call("rm -R $slepath/$kext");

    	echo "Copying $workpath/$frompath/$kext to $slepath/ \n";
    	system_call("cp -R $workpath/$frompath/$kext $slepath/");

    	system_call("chown -R root:wheel $slepath/$kext");
    	system_call("chmod -R 755 \"$slepath/$kext\"");
    }



























	
		
}


$kexts = new kexts();


?> 
