
<?php
	include_once "functions.inc.php";
	include_once "edpconfig.inc.php";
		
	include "header.inc.php";

	$skippbuild = "";
		
	function deleteEDPFiles()
	{
			//Get all the kexts name in comma seperated way
			$edlinfo = shell_exec("ls -m /Extra/Extensions/");
    		$edkarray = explode(',', $edlinfo);
				
			$peuid = 0;
			foreach($edkarray as $edfkname) {
								
				if ($edfkname != "") {
					
				$edfkname = preg_replace('/\s+/', '',$edfkname); // remove white spaces
				
				if($_POST[$peuid] == "on")
					; //ignore
				else {
					system("rm -rf /Extra/Extensions/$edfkname");
					 //echo $edfkname;
					}
				}
				$peuid++;
				
			}
		
		$edsdt = $_POST['edsdt']; $eboot = $_POST['eboot']; $esmbios = $_POST['esmbios'];
		$essdt = $_POST['essdt']; $essdt1 = $_POST['essdt1']; $essdt2 = $_POST['essdt2']; $essdt3 = $_POST['essdt3']; $essdt4 = $_POST['essdt4'];
			
		if(file_exists("/Extra/DSDT.aml") && $edsdt != "on") {system("rm -f /Extra/DSDT.aml");}
		
		if(file_exists("/Extra/org.chameleon.Boot.plist") && $eboot != "on") { system("rm -f /Extra/org.chameleon.Boot.plist");}

		if(file_exists("/Extra/SMBios.plist") && $esmbios != "on") { system("rm -f /Extra/SMBios.plist");} 

		if(file_exists("/Extra/SSDT.aml") && $essdt != "on") {  system("rm -f /Extra/SSDT.aml");} 

		if(file_exists("/Extra/SSDT-1.aml") && $essdt1 != "on") { system("rm -f /Extra/SSDT-1.aml");}

		if(file_exists("/Extra/SSDT-2.aml") && $essdt2 != "on") {  system("rm -f /Extra/SSDT-2.aml");}

		if(file_exists("/Extra/SSDT-3.aml") && $essdt3 != "on") {  system("rm -f /Extra/SSDT-3.aml");} 

		if(file_exists("/Extra/SSDT-4.aml") && $essdt4 != "on") { system("rm -f /Extra/SSDT-4.aml");} 
	}
	
	function deleteUsrIncludeFiles()
	{	
		$dsdt = $_POST['dsdt']; $boot = $_POST['boot']; $smbios = $_POST['smbios'];
		$ssdt = $_POST['ssdt']; $ssdt1 = $_POST['ssdt1']; $ssdt2 = $_POST['ssdt2']; $ssdt3 = $_POST['ssdt3']; $ssdt4 = $_POST['ssdt4'];
	
		//echo "List: $dsdt, $boot, $smbios, $ssdt, $ssdt1, $ssdt2, $ssdt3, $ssdt4,E";
	
			//Get all the kexts name in comma seperated way
			$cdlinfo = shell_exec("ls -m /Extra/include/Extensions/");
    		$cdkarray = explode(',', $cdlinfo);
				
			$pckid = 100;
			
			foreach($cdkarray as $cdfkname) {
								
				if ($cdfkname != "") {
					
				$cdfkname = preg_replace('/\s+/', '',$cdfkname); //remove white spaces
						
				if($_POST[$pckid] == "on")
					;//ignore
				else {
					  system("rm -rf /Extra/include/Extensions/$cdfkname");
					 //echo "DEL:$cdfkname";
					}
			 	}
			 	$pckid++;
			 }
		
			if(file_exists("/Extra/include/DSDT.aml") && $dsdt != "on") { system("rm -f /Extra/include/DSDT.aml"); }  
			
			if(file_exists("/Extra/include/org.chameleon.Boot.plist") && $boot != "on") { system("rm -f /Extra/include/org.chameleon.Boot.plist"); }
			
			if(file_exists("/Extra/include/SMBios.plist") && $smbios != "on") {  system("rm -f /Extra/include/SMBios.plist"); } 
			
			if(file_exists("/Extra/include/SSDT.aml") && $ssdt != "on") {  system("rm -f /Extra/include/SSDT.aml");} 
			
			if(file_exists("/Extra/include/SSDT-1.aml") && $ssdt1 != "on") {  system("rm -f /Extra/include/SSDT-1.aml"); } 
			
			if(file_exists("/Extra/include/SSDT-2.aml") && $ssdt2 != "on") {  system("rm -f /Extra/include/SSDT-2.aml"); } 
			
			if(file_exists("/Extra/include/SSDT-3.aml") && $ssdt3 != "on") {  system("rm -f /Extra/include/SSDT-3.aml"); } 
			
			if(file_exists("/Extra/include/SSDT-4.aml") && $ssdt4 != "on") { system("rm -f /Extra/include/SSDT-4.aml");} 
			
		}

		// Fetch vars if any was posted and if action is set to "dobuild" then fetch the rest of the vars and run doCustomBuild :)
		$action		= $_POST['action'];
		if ($action == "dobuild") {	
		
			$cusoper = $_POST['copchoice'];
			$edpoper = $_POST['eopchoice'];
			
			if($edpoper === "edelonly" || $edpoper == "ebuild")
				deleteEDPFiles();
				
			if($cusoper == "cdelbuild" || $cusoper == "cdelonly")
				deleteUsrIncludeFiles();
				
			if($cusoper == "cbuild" || $edpoper == "ebuild" || $cusoper == "cdelbuild" ||
			   $cusoper == "cfixcache" || $cusoper == "cfullfix")	 {
				doCustomBuild();
				echo "<body onload=\"JavaScript:showStatus();\">";
				echo "<script type=\"text/JavaScript\"> function showStatus() { top.document.getElementById('edpmenu').src ='workerapp.php?action=showCustomBuildInfo#myfix'; } </script>\n";
			}
		}


// Build Process		
function doCustomBuild() {

	global $workpath; global $edp;
	global $slepath;
	$incpath = "/Extra/include"; global $ee;
	
			$cusoper = $_POST['copchoice'];
			$edpoper = $_POST['eopchoice'];
			
			$dsdt = $_POST['dsdt']; $boot = $_POST['boot']; $smbios = $_POST['smbios'];
			$ssdt = $_POST['ssdt']; $ssdt1 = $_POST['ssdt1']; $ssdt2 = $_POST['ssdt2']; $ssdt3 = $_POST['ssdt3']; $ssdt4 = $_POST['ssdt4'];

	
    // check if myhack.kext exists in ale, and if it dosent then copy it there...
	if (!is_dir("$slepath/myHack.kext")) {
        $myhackkext = "$workpath/bin/myHack/myHack.kext";
        system_call("rm -Rf `find -f path \"$myhackkext\" -type d -name .svn`");
        system_call("cp -R \"$myhackkext\" $slepath");
    }
    if (!is_file("/usr/sbin/")) {
        system_call("cp \"$workpath/bin/myHack/myfix\" /usr/sbin/myfix; chmod 777 /usr/sbin/myfix");
    }
    
    //
	// Start by defining our log file and cleaning it..
	//
	$log = "$workpath/build.log";
	if (is_file("$log")) { 
		system_call("rm -Rf $log"); 
	}
	
	$myFixlog = "$workpath/myFix.log";
		if (is_file("$myFixlog")) { 
			system_call("rm -Rf $myFixlog"); 
		}
		
	if(!is_dir("$workpath/kpsvn/dload/"))
    		system_call("mkdir $workpath/kpsvn/dload");
    	else
    		system_call("rm -Rf $workpath/kpsvn/dload/*");
    		
	//$edp->writeToLog("$workpath/build.log", "Choice: $cusoper, $edpoper<br>");

	
	if($cusoper == "cfullfix") {
   		$edp->writeToLog("$workpath/build.log", "<br><b>Calling myFix for Full fix to copy kexts and generate kernelcache</b><br><pre>");
   		system_call("stty -tostop; sudo myfix -t / >>$workpath/build.log 2>&1 &");
   	 }
	else if($cusoper == "cfixcache" || ($edpoper == "ebuild" && $cusoper == "cnone")) {
   		$edp->writeToLog("$workpath/build.log", "<br><b>Calling myFix for Quick fix to copy kexts and generate kernelcache</b><br><pre>");
   		system_call("stty -tostop; sudo myfix -q -t / >>$workpath/build.log 2>&1 &");
   	 }
   		
	else if($cusoper != "cnone")
	{
		//Step 1
		$edp->writeToLog("$workpath/build.log", "<br><b>Step 1) Checking if you have selected any sources from $incpath </b><br>");

		$edp->writeToLog("$workpath/build.log", "Copying $incpath/Extensions to $ee <br>");
			
			//Get all the kexts name in comma seperated way
			$cclinfo = shell_exec("ls -m /Extra/include/Extensions/");
    		$cckarray = explode(',', $cclinfo);
				
			$pckid = 100;
			
			foreach($cckarray as $ccfkname) {
								
				if ($ccfkname != "") {
					
				$ccfkname = preg_replace('/\s+/', '',$ccfkname); //remove white spaces
					
				if($_POST[$pckid] == "on") {
					  system("cp -R /Extra/include/Extensions/$ccfkname $ee");
					  $edp->writeToLog("$workpath/build.log", "Copying $ccfkname to $ee<br>");
					}
					else {
						if(is_dir("/Extra/Extensions/$ccfkname")) {
							system("rm -rf /Extra/Extensions/$ccfkname");
					  		$edp->writeToLog("$workpath/build.log", "Removing $ccfkname from $ee<br>");
					  	}
					}
			 	}
			 	$pckid++;
			 }
	
		if ($dsdt == "on") {
			$edp->writeToLog("$workpath/build.log", "Copying $incpath/dsdt.aml to /Extra<br>");
			system_call("cp -f /Extra/include/dsdt.aml /Extra");
		} 	
	
    	if (is_file("/Extra/include/SSDT.aml") && $ssdt == "on") 					{ 
    		$edp->writeToLog("$workpath/build.log", "Copying SSDT file to /Extra<br>");
    		system_call("cp -f /Extra/include/SSDT.aml /Extra"); 
    	}
    	if (is_file("/Extra/include/SSDT-1.aml") && $ssdt1 == "on") 				{ 
    		$edp->writeToLog("$workpath/build.log", "Copying SSDT-1 file to /Extra<br>");
    		system_call("cp -f /Extra/include/SSDT-1.aml /Extra"); 
    	}
    	if (is_file("/Extra/include/SSDT-2.aml") && $ssdt2 == "on") 				{ 
    		$edp->writeToLog("$workpath/build.log", "Copying SSDT-2 file to /Extra<br>");
    		system_call("cp -f /Extra/include/SSDT-2.aml /Extra"); 
    	}
    	if (is_file("/Extra/include/SSDT-3.aml") && $ssdt3 == "on") 				{ 
    		$edp->writeToLog("$workpath/build.log", "Copying SSDT-3 file to /Extra<br>");
			system_call("cp -f /Extra/include/SSDT-3.aml /Extra"); 
    	}    
    	if (is_file("/Extra/include/SSDT-4.aml") && $ssdt4 == "on") 				{ 
    		$edp->writeToLog("$workpath/build.log", "Copying SSDT-4 file to /Extra<br>");
    		system_call("cp -f /Extra/include/SSDT-4.aml /Extra"); 
    	}

		if ($smbios == "on")	{
			$edp->writeToLog("$workpath/build.log", "Copying $incpath/smbios.plist to /Extra<br>"); 
			system_call("cp -f /Extra/include/smbios.plist /Extra");
			}
	
		if ($boot == "on") { 
			$edp->writeToLog("$workpath/build.log", "Copying $incpath/org.chameleon.Boot.plist to /Extra<br>");
			system_call("cp -f /Extra/include/org.chameleon.Boot.plist /Extra");
			}		
		
		$edp->writeToLog("$workpath/build.log", "<br><b>Step 2) Calling myFix to copy kexts and generate kernelcache</b><br><pre>");
		system_call("stty -tostop; sudo myfix -q -t / >>$workpath/build.log 2>&1 &");
		$edp->writeToLog("$workpath/build.log", "<a name='myfix'></a>");
  
    }
    
		$edp->writeToLog("$workpath/build.log", "<a name='myfix'></a>");			
		echo "<script> document.location.href = 'workerapp.php?action=showBuildLog#myfix'; </script>";
	
		exit;
	}		

	//
	// Custom configuration page
	//
	echo "<form action='module.configuration.custom.php' method='post'>";
	
	// Write out the top menu
	echoPageItemTOP("icons/big/config.png", "Custom build configuration");

	echo "<div class='pageitem_bottom'>\n";
	echo "EDP custom configuration allows you to use your existing configuration in /Extra and allows you to choose your custom files included from /Extra/include.<br><br>";
	echo "Copy your custom files like DSDT, SSDT, plists and boot(chameleon bootloader) to /Extra/include and <br> custom Kexts to /Extra/include/Extensions, which can be managed very effectively and easily using our EDP.<br>";
	echo "</div>";
			
	//Load the tabs
	echo "<script> $(function() { $( \"#tabs\" ).tabs(); }); </script>\n";
		
		// Show the tabs bar ?>
		<div id="tabs">
			<div id="menutabs">
				<ul>
					<li><a href="#tabs-0">Custom Files</a></li>
					<li><a href="#tabs-1">EDP Kexts</a></li>

				</ul>
			</div>
			<?php

			echo "<div class='pageitem_bottom'><br>\n";

			//Include tabs
			include "include/module.configuration.custom.files.inc.php";		
			include "include/module.configuration.edp.files.inc.php";

			echo "<input type='hidden' name='action' value='dobuild'>";
			echo "</div><br>";
			
	echo "<ul class='pageitem'><li class='button'><input name='Submit input' type='submit' value='Do build!' /></li></ul><br><br>\n";
	
	exit;
		
?>
