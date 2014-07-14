<?php

echo "<div id=\"tabs-0\">\n";

echo "<span class='graytitle'>Software</span><br>\n";
echo "<div><b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Operating system:</b></b> $os_string</div>";
echo "<br><br>";  

echo "<span class='graytitle'>Compatibility</span>\n";
echo "<ul class='pageitem'>";
		
?>
<br>
<table class='compatibility_table' border="1" width="100%" cellspacing="0" cellpadding="0" style="border-collapse: collapse" bordercolor="#F4F4F4">
<thead>
        <tr bgcolor="#FFFFFF">
                <th id="icon" class="compat_icon_cell"><img width="35px" src="/images/compat/qeci.png" alt="QE/CI"></th>
                <th id="icon" class="compat_icon_cell"><img width="35px" src="/images/compat/Speakers.png" alt="Sound"></th>
                <th id="icon" class="compat_icon_cell"><img height="35px" src="/images/compat/Trackpad.png" alt="Trackpad"></th>
                <th id="icon" class="compat_icon_cell"><img height="35px" src="/images/compat/USB.png" alt="USB"></th>
                <th id="icon" class="compat_icon_cell"><img height="35px" src="/images/compat/LAN.png" alt="LAN"></th>
                <th id="icon" class="compat_icon_cell"><img width="35px" src="/images/compat/WiFi.png" alt="WiFi"></th>
                <th id="icon" class="compat_icon_cell"><img height="35px" src="/images/compat/BT.png" alt="Bluetooth"></th>
                <th id="icon" class="compat_icon_cell"><img width="35px" src="/images/compat/VGA.png" alt="Video Out"></th>
                <th id="icon" class="compat_icon_cell"><img width="35px" src="/images/compat/HDMI.png" alt="HDMI"></th>
                <th id="icon" class="compat_icon_cell"><img height="35px" src="/images/compat/Sleep.png" alt="Sleep"></th>
                <th id="icon" class="compat_icon_cell"><img width="35px" src="/images/compat/Battery.png" alt="Battery"></th>
                <th id="icon" class="compat_icon_cell"><img width="35px" src="/images/compat/Webcam.png" alt="Webcam"></th>
                <th id="icon" class="compat_icon_cell"><img height="35px" src="/images/compat/Cardreader.png" alt="Cardreader"></th>
        </tr>
        <tr class="compat_icon_text">
                <th>QE/CI</th>
                <th>Sound</th>
                <th>Trkpad</th>
                <th>USB</th>
                <th>LAN</th>
                <th >WiFi</th>
                <th>BT</th>
                <th>VGA</th>
                <th>HDMI</th>
                <th>Sleep</th>
                <th>Batt.</th>
                <th>Cam</th>
                <th>SD</th>
        </tr>
</thead>

<?
		global $type;
		
		switch ($type) {
 			  case "Notebook":
 			  case "Ultrabook":
 			  case "Tablet":
 			  	$cquery = "SELECT * FROM compatNB where model_id = '$modelID'";
 			  break;
 			  
 			  case "Desktop":
 			  case "Workstation":
 			  case "AllinOnePC":
 			  	$cquery = "SELECT * FROM compatDesk where model_id = '$modelID'";
 			  break;
 		}
 		
		$stmt = $edp_db->query($cquery);
		$stmt->execute();
		$bigrow = $stmt->fetchAll(); $crow = $bigrow[0];	
		
        echo "<tr class='compat_model_text' height='40px'>\n";

        if ($crow[qeci] != "")          { echo "  <td><center><img src=\"/images/compat/$crow[qeci].png\" width='20px'></center></td>\n";  } else { echo "<td></td>"; }
        if ($crow[sound] != "")         { echo "  <td><center><img src=\"/images/compat/$crow[sound].png\" width='20px'></center></td>\n";  } else { echo "<td></td>"; }
        if ($crow[trackpad] != "")      { echo "  <td><center><img src=\"/images/compat/$crow[trackpad].png\" width='20px'></center></td>\n";  } else { echo "<td></td>"; }
        if ($crow[usb] != "")           { echo "  <td><center><img src=\"/images/compat/$crow[usb].png\" width='20px'></center></td>\n";  } else { echo "<td></td>"; }
        if ($crow[ethernet] != "")      { echo "  <td><center><img src=\"/images/compat/$crow[ethernet].png\" width='20px'></center></td>\n";  } else { echo "<td></td>"; }
        if ($crow[wifi] != "")          { echo "  <td><center><img src=\"/images/compat/$crow[wifi].png\" width='20px'></center></td>\n";  } else { echo "<td></td>"; }
        if ($crow[bluetooth] != "")     { echo "  <td><center><img src=\"/images/compat/$crow[bluetooth].png\" width='20px'></center></td>\n"; } else { echo "<td></td>"; }
        if ($crow[vgaout] != "")        { echo "  <td><center><img src=\"/images/compat/$crow[vgaout].png\" width='20px'></center></td>\n";  } else { echo "<td></td>"; }
        if ($crow[hdmiout] != "")       { echo "  <td><center><img src=\"/images/compat/$crow[hdmiout].png\" width='20px'></center></td>\n"; } else { echo "<td></td>"; }
        if ($crow[sleep] != "")         { echo "  <td><center><img src=\"/images/compat/$crow[sleep].png\" width='20px'></center></td>\n";  } else { echo "<td></td>"; }
        if ($crow[battery] != "")       { echo "  <td><center><img src=\"/images/compat/$crow[battery].png\" width='20px'></center></td>\n";  } else { echo "<td></td>"; }
        if ($crow[webcam] != "")        { echo "  <td><center><img src=\"/images/compat/$crow[webcam].png\" width='20px'></center></td>\n";  } else { echo "<td></td>"; }
        if ($crow[cardreader] != "")    { echo "  <td><center><img src=\"/images/compat/$crow[cardreader].png\" width='20px'></center></td>\n";  } else { echo "<td></td>"; }

        echo "</tr></table> \n";

	echo "</ul><br>";
		
	echo "<span class='graytitle'>Include EDP Files</span>";
	echo "<ul class='pageitem'>";
	checkbox("Use kexts from EDP ?", "useEDPExtensions", "yes");
	checkbox("Use DSDT.aml from EDP ?", "useEDPDSDT", "yes");
	checkbox("Use SSDT files from EDP ?", "useEDPSSDT", "yes");
	checkbox("Use smbios.plist from EDP ?", "useEDPSMBIOS", "yes");
	checkbox("Use org.chameleon.boot.plust from EDP ?", "useEDPCHAM", "yes");		
	echo "</ul>";
	
	echo "<span class='graytitle'>Include Custom Files</span>";
	echo "<ul class='pageitem'>";
	checkbox("Include kexts from /Extra/include/Extensions ?", "useIncExtensions", "yes");
	checkbox("Include DSDT.aml from /Extra/include ?", "useIncDSDT", "yes");
	checkbox("Include SSDT files from /Extra/include ?", "useIncSSDT", "yes");
	checkbox("Include smbios.plist from /Extra/include ?", "useIncSMBIOS", "yes");
	checkbox("Include org.chameleon.boot.plust from /Extra/include ?", "useIncCHAM", "yes");		
	echo "</ul>";
	echo "</div>";	
?> 
