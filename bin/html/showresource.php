
<?php
include_once "edpconfig.inc.php";
include_once "functions.inc.php";

include_once "header.inc.php";


/*
 * load the page of the selected side menu
 */
	$action = $_GET['action'];

	// get category and id from the link
	$categ = $_GET['category'];	
	$id = $_GET['id'];

	switch ($categ) {
		case "Applications":
		case "Tools":
			$query = "SELECT * FROM appsdata";
		break;
	
		case "EDP":
		if (file_exists("$workpath/update.log")) 
			system_call("rm -rf $workpath/update.log");
		
		case "Configuration":
			$query = "SELECT * FROM edpdata";
		break;
	
		case "Fixes":
			$query = "SELECT * FROM fixesdata";
		break;
	}

	// Get info from db
	$stmt = $edp_db->query("$query where id = '$id'");
	$stmt->execute();
	$bigrow = $stmt->fetchAll(); $row = $bigrow[0];
	
	$href = "$row[action]";
		
	if ($action == "")
	{
		// Write out the top menu
		if ($categ != "EDP")
			echoPageItemTOP("icons/sidebar/$row[icon]", "$row[submenu]");
		else
			echoPageItemTOP("icons/big/$row[icon]", "$row[submenu]");
		
		?>
		
		<div class="pageitem_bottom">
		<p><b>About:</b></p>
		<?="$row[brief]";?>
		<br>
		<p><b>Descripton:</b></p>
		<?="$row[description]";?>
		<br>
		<p><b>Website:</b></p>
		<a href='<?="$row[link]";?>'>Project/Support Link</a>
		</div>
		<ul class="pageitem">
			<li class="button"><input name="Submit input" type="submit" onclick="document.location.href='<?=$href?>'" value="Proceed to Install/Update" /></li>
		</ul>
	
		<?php
	}
	elseif ($action == "Install")
	{
		// Start installation process by Launching the script which provides the summary of the build process 
		echo "<script> document.location.href = 'workerapp.php?id=$id&submenu=$row[submenu]&icon=$row[icon]&action=showInstallLog#myfix'; </script>";
		
		// Clear logs and scripts
		if(is_dir("$workpath/apps/dload")) {
			system_call("rm -rf $workpath/apps/dload/*");
		}
		
		// Download app
		appsLoader("$row[menu]","$row[submenu]");
	}
	elseif ($action == "Patch")
	{
		echo "<div class='pageitem_bottom'\">";	
		echo "<ul class='pageitem'>";

		switch ($row[name]) {
		
			case "AppleIntelCPUPowerManagementPatch":
				patchAppleIntelCPUPowerManagement();
			break;
			
			case "":
			break;
		}
		
		echo "<img src=\"icons/big/success.png\" style=\"width:80px;height:80px;position:relative;left:50%;top:50%;margin:15px 0 0 -35px;\">";
		echo "<b><center> Patch finished.</b><br><br><b> You can now reboot the sysem to see the patch in action.</center></b>";
		echo "<br></ul>";
	}

function appsLoader($categ, $fname) {
		global $workpath, $edp;
    	  
    	$applogPath = "$workpath/apps/dload";
    	  	
    	if(!is_dir("$workpath/apps")) {
			system_call("mkdir $workpath/apps");
		}
		if(!is_dir("$applogPath")) {
			system_call("mkdir $applogPath");
		}
		if(!is_dir("$applogPath/statFiles")) {
			system_call("mkdir $applogPath/statFiles");
		}
			
		$createStatFile = "cd $applogPath/statFiles; touch $fname.txt";	
		$endStatFile = "cd $applogPath/statFiles; rm -rf $fname.txt";
		
		//
		// Download apps from SVN
		//
    	$packdir = "$workpath/apps/$categ";
		$svnpath = "apps/$categ/$fname";
			
		if (is_dir("$packdir")) {
			$checkoutCmd = "if svn --non-interactive --username edp --password edp --quiet --force update $packdir/$fname; then echo \"$fname file(s) updated finished<br>\" >> $applogPath/appInstall.log; touch $applogPath/success.txt; else echo \"$fname file(s) update failed (may be wrong svn path or no internet)<br>\" >> $applogPath/appInstall.log; touch $applogPath/fail.txt; fi";

			writeToLog("$applogPath/$fname.sh", "$createStatFile; $checkoutCmd; $endStatFile;");
			system_call("sh $applogPath/$fname.sh >> $applogPath/appInstall.log &");
		}
		else {
			$checkoutCmd = "mkdir $packdir; cd $packdir; if svn --non-interactive --username osxlatitude-edp-read-only --quiet --force co http://osxlatitude-edp.googlecode.com/svn/$svnpath; then echo \"$fname file(s) download finished<br>\" >> $applogPath/appInstall.log; touch $applogPath/success.txt; else echo \"$fname file(s) download failed (may be wrong svn path or no internet)<br>\" >> $applogPath/appInstall.log; touch $applogPath/fail.txt; fi";

			writeToLog("$applogPath/$fname.sh", "$createStatFile; $checkoutCmd; $endStatFile;");
			system_call("sh $applogPath/$fname.sh >> $applogPath/appInstall.log &");	
		}
} 
?>


