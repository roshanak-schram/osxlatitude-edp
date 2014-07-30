
<?php

	include_once "edpconfig.inc.php";
	include_once "functions.inc.php";

	include_once "header.inc.php";

	/*
	 * load the fix
	 */
 
 	//
 	// get action, category and id from the get and post methods
 	//
	$action = $_GET['action'];
	if ($action == "") {
		$action = $_POST['action'];
	}
	
	$kCategory	= $_GET['category'];
	if ($kCategory == "") {
		$kCategory = $_POST['category'];
	}	
	
	$id = $_GET['id'];
	if ($id == "") {
		$id = $_POST['id'];
	}
		
	// Get info from db
	switch ($kCategory) {
		case "audio":
			$stmt = $edp_db->query("SELECT * FROM audio where id = '$id'");
		break;
		
		case "bat":
			$stmt = $edp_db->query("SELECT * FROM battery where id = '$id'");
		break;
		
		case "smc":
			$stmt = $edp_db->query("SELECT * FROM fakesmc where id = '$id'");
		break;
		
		case "lan":
			$stmt = $edp_db->query("SELECT * FROM ethernet where id = '$id'");
		break;
		
		case "optpacks":
			$stmt = $edp_db->query("SELECT * FROM optionalpacks where id = '$id'");
		break;
		
		case "ps2":
			$stmt = $edp_db->query("SELECT * FROM ps2 where id = '$id'");
		break;
		
	}
	
	
	$stmt->execute();
	$bigrow = $stmt->fetchAll(); $row = $bigrow[0];
			
	if ($action == "")
	{
		echo "<form action='kexts.php' method='post'>";

		// Write out the top menu
		echoPageItemTOP("icons/big/$row[icon]", "$row[name]");
		
		?>
		
		<div class="pageitem_bottom">
		<p><b>About:</b></p>
		<?="$row[brief]";?>
		<br>
		<p><b>Descripton:</b></p>
		<?="$row[description]";?>
		<br>
		<p><b>Developer and Version:</b></p>
		<?="$row[owner], v$row[version]";?>
		<br>
		<p><b>Website:</b></p>
		<a href='<?="$row[link]";?>'>Project/Support Link</a>
		
		<?php
			echo "<input type='hidden' name='id' value='$id'>";
			echo "<input type='hidden' name='action' value='Install'>";
			echo "<input type='hidden' name='category' value='$kCategory'>";
			
			echo "<br><br><ul class='pageitem'>";				
				checkbox("Install kext to /System/Library/Extensions instead of myHack load?", "goToSLE", "no");
			echo "</ul></div>";
			
		?>
		
		<ul class="pageitem">
			<li class="button"><input name="Submit input" type="submit" value="Proceed to Install/Update" /></li>
		</ul>

		</form>
		<?php
	}
	elseif ($action == "Install")
	{

		$goToSLE = $_GET['goToSLE']; if ($goToSLE == "") { $goToSLE = $_POST['goToSLE']; }
		
		if ($goToSLE == "on") {
			$installPath = "/System/Library/Extensions";
		}
		else {
			$installPath = "/Extra/Extensions";
		}
		
		global $svnLoad;
		
		// Clear logs and scripts
		if(is_dir("$workpath/logs/build")) {
			system_call("rm -rf $workpath/logs/build/*");
		}
		
		$kInfoKeys = "id,foldername,name,icon,categ,path";
		$kInfoValues = "$id,$row[foldername],$row[name],$row[icon],$row[category],$installPath";
		
		// Download kext
		switch ($kCategory) {
			case "audio":
			case "bat":
			case "smc":
			case "ps2":
				$svnLoad->svnDataLoader("Kexts", "$row[category]", "$row[foldername]");
			break;
			
			case "optpacks":
				
				// Generic XHCI USB3.0
				if($id == "5") {
					// Choose new version 
					if(getMacOSXVersion() >= "10.8.5")
						$svnLoad->PrepareKextpackDownload("Kexts", "$row[category]", "GenericXHCIUSB3_New");
			
					// Choose old version
					else if(getMacOSXVersion() < "10.8.5")
						$svnLoad->PrepareKextpackDownload("Kexts", "$row[category]", "$row[foldername]");
				}
				else
					$svnLoad->svnDataLoader("Kexts", "$row[category]", "$row[foldername]");
				
			break;
			
			case "lan":
				
				if(!is_dir("$workpath/kextpacks/Ethernet"))
    				system_call("mkdir $workpath/kextpacks/Ethernet");
    				
    			//
				// name and foldername values are swapped due to Extra category folder of Ethernet
				// so we can check the status of the kext download
				//
				$kInfoValues = "$id,$row[name],$row[foldername],$row[icon],$row[category],$installPath";
					
				// New Realtek kext
    			if($id == "11") {
				
					// Choose 10.8+ version 
					if(getMacOSXVersion() >= "10.8")
						$svnLoad->svnDataLoader("Kexts", "Ethernet/$row[foldername]", "RealtekRTL8111");
					
					// Choose Lion version
					else if(getMacOSXVersion() == "10.7")
						$svnLoad->svnDataLoader("Kexts", "Ethernet/$row[foldername]", "RealtekRTL8111_Lion");
    			}
    			else
    				$svnLoad->svnDataLoader("Kexts", "Ethernet/$row[foldername]", "$row[name]");
    				
			break;
		}
			
		// Start installation process by Launching the script which provides the summary of the build process 
		echo "<script> document.location.href = 'workerapp.php?kInfoKeys=$kInfoKeys&kInfoValues=$kInfoValues&action=showKextsLog'; </script>";
		
	}

?>
