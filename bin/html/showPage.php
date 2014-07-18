
<?php
	$i = $_GET['i'];
	include_once "edpconfig.inc.php";
	include_once "header.inc.php";

	/*
 	 * load the page of the selected main menu
 	 */
 	 
	switch ($i) {
		case "EDP":
			include_once "libs/Highchart.php";
			//----------------------------------------------------------------------------> Highchart stuff....... //
			$vendors = array();
			$total = 0;
			$dTotal = 0;
			$nbTotal = 0;

			// Get Notebooks data from db
			$nbookData = $edp_db->query("SELECT vendor, COUNT(*) AS count FROM modelsPortable GROUP BY vendor ORDER BY count DESC");

			// Loop the result and add it to $vendors
			foreach($nbookData as $row) {
				$tmp = $row;
				unset($tmp[0], $tmp[1]);
				$vendors[] = $tmp;
				$total += $row['count'];
				$nbTotal += $row['count'];
			}

			// Calculate the percentage per vendor
			foreach($vendors as $i => $vendor) {
			if($total != 0) {
				$c = round(($vendor['count']/$total)*100, 1);
				$vendors[$i]['share'] = round($c, 0);
			} 
			else {
				$vendors[$i]['share'] = 0;
			  }
			}

			// Get Desktops data from db
			$deskData = $edp_db->query("SELECT vendor, COUNT(*) AS count FROM modelsDesk GROUP BY vendor ORDER BY count DESC");

			// Loop the result and add it to $vendors
			foreach($deskData as $row) {
				$tmp = $row;
				unset($tmp[0], $tmp[1]);
				$vendors[] = $tmp;
				$total += $row['count'];
				$dTotal += $row['count'];
			}

			// Calculate the percentage per vendor
			foreach($vendors as $i => $vendor) {
			if($total != 0) {
				$c = round(($vendor['count']/$total)*100, 1);
				$vendors[$i]['share'] = round($c, 0);
			} 
			else {
				$vendors[$i]['share'] = 0;
				}
			}

			/*
			echo count($vendors);

			$nbDet['vendor'] = "Notebooks";
			$nbDet['count'] = $nbTotal;
			$vendors[] = $nbDet;

			$c = round(($nbTotal/$total)*100, 1);
			$vendors[count($vendors) - 1]['share'] = round($c, 0);
			*/

			$chart = new Highchart();

			$chart->chart->renderTo = "container";
			$chart->chart->plotBackgroundColor = '#FFFFFF';
			$chart->chart->plotBorderWidth = '0px';
			$chart->chart->plotShadow = false;
			$chart->title->text = "We currently have $total systems in EDP (Notebooks : $nbTotal, Desktops : $dTotal)";

			$chart->tooltip->formatter = new HighchartJsExpr("function() {
		    return '<b>'+ this.point.name +'</b>: '+ Math.round(this.percentage) +' %';}");

			$chart->plotOptions->pie->allowPointSelect = 1;
			$chart->plotOptions->pie->cursor = "pointer";
			$chart->plotOptions->pie->dataLabels->enabled = 1;
			$chart->plotOptions->pie->dataLabels->color = "#000000";
			$chart->plotOptions->pie->dataLabels->connectorColor = "#000000";

			$chart->plotOptions->pie->dataLabels->formatter = new HighchartJsExpr("function() {
		    return '<b>'+ this.point.name +'</b>: '+ Math.round(this.percentage) +' %'; }");

			// Make the array for the chart
			$vendor_chart_data = array();
			foreach($vendors as $vendor) {
				$vendor_chart_data[] = array(
				'name' => $vendor['vendor'],
				'y' => $vendor['share'],
				'sliced' => $vendor['vendor'] == "Dell" ? TRUE : FALSE,
				);
			}
			$vendor_chart = array(
			'type' => "pie",
			'name' => "Vendor share",
			'data' => $vendor_chart_data,
			);

			$chart->series[] = $vendor_chart;

			foreach ($chart->getScripts() as $script) {
				echo '<script type="text/javascript" src="' . $script . '"></script>';
			}

			//<---------------------------------- Highchart stuff end... //

			echoPageItemTOP("icons/big/edp.png", "Welcome to EDP");
			echo "<div class='pageitem_bottom'>\n";
			echo "EDP is a unique control panel for your hackintosh that makes it easy to maintain and configure your system. Its internal database contains 'best practice' schematics for many systems - this makes it easy to choose the right configuration.</p>";
			
			echo "<div id='container'></div>";
			
			?>
			<script type="text/javascript">

			<?php
     			echo $chart->render("chart1");
			?>
    
 		   // Hack: Modify color of pie chart border   
 			$(document).ready(function() {
 			$( "rect" ).each(function( index ) {
  				if($(this).attr("fill")=="#FFFFFF") { 
	 				 $(this).attr("fill", "#FFFFFF");
  	 		 		}
				});
 			});
 			</script>
 			
 			<?php
 			exit;
		break;
		
		case "Configuration":
			echoPageItemTOP("icons/big/sysprefs.png", "Configuration");
			echo "<div class='pageitem_bottom'>\n";
			echo "Build your model using EDP which provides combination of kexts, dsdt, plists needed to boot your system and allows you to configure.";
			exit;
		break;
		
		case "Applications":
			echoPageItemTOP("icons/big/apps.png", "Applications");
			echo "<div class='pageitem_bottom'>\n";
			echo "In this section you can find some of the most used hackintosh applications.";
		break;
		
		case "Tools":
			echoPageItemTOP("icons/big/tools.png", "Tools");
			echo "<div class='pageitem_bottom'>\n";
			echo "Having the right tool for the situation is always part of the problem. We have collected some of the best tools out there.<br>";
			echo "Know a good tool that we should include? Shoot an email to <a href='mailto:lsb@osxlatitude.com'>lsb@osxlatitude.com</a>";
		break;
		
		case "Fixes":
			echoPageItemTOP("icons/big/emergency.png", "Fixes - The solution to the problem!");
			echo "<div class='pageitem_bottom'>\n";
			echo "This section contains solution for the most common Hackintosh problems. If you still miss a fix, you can post it on our forum.";
		break;
		
		case "Credits":
			echoPageItemTOP("icons/big/credits.png", "Credits");
			echo "<div class='pageitem_bottom'>\n";
			echo "In this section you can find some of the most used hackintosh applications.";
			echo "<h1>Thank you!!!!</h1>";
			echo "<h2> .. to all of you that made EDP happened....</h2>";
			echo "<br>";

			echo "EDP is not just made by the team of OSXLatitude, it's made by a lot of people who have spend thousands and thousands of hours to make all this happened...";

			echo "<br><br>";
			echo "<b>Help us out....</b><br>";
			echo "Found something in here that you made? Send an email to <a href='mailto:lsb@osxlatitude.com'>lsb@osxlatitude.com</a> and we will put you too on this page.";
		break;
		
		case "Changelog":
			echoPageItemTOP("icons/big/xcode.png", "Changelog for EDP...");
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
			exit;
		break;
		
		case "Update":
		
			// Clear logs and scripts
			if(is_dir("$workpath/kpsvn/dload")) {
				system_call("rm -rf $workpath/kpsvn/dload/*");
			}
			
			if(!is_dir("$workpath/kpsvn")) {
				system_call("mkdir $workpath/kpsvn");
			}
			if(!is_dir("$workpath/kpsvn/dload")) {
				system_call("mkdir $workpath/kpsvn/dload");
			}
			if(!is_dir("$workpath/kpsvn/dload/statFiles")) {
				system_call("mkdir $workpath/kpsvn/dload/statFiles");
			}
			if(is_dir("$workpath/kpsvn/dload/statFiles")) {
				$fcount = shell_exec("cd $workpath/kpsvn/dload/statFiles; ls | wc -l");
			}
		
			// Start installation process by Launching the script which provides the summary of the build process 
			echo "<script> document.location.href = 'workerapp.php?action=showUpdateLog#myfix'; </script>";
		
			echoPageItemTOP("icons/big/update.png", "Update");
			echo "<div class='pageitem_bottom'\">";	
			echo "<center><b>Please wait for few minutes while we download the updates... which will take approx 1 to 10 minutes depending on your internet speed</b></center>";
			echo "<img src=\"icons/big/loading.gif\" style=\"width:200px;height:30px;position:relative;left:50%;top:50%;margin:15px 0 0 -100px;\">";
			
			system_call("sudo sh $workpath/bin/update.sh >> $workpath/update.log &");

			echo "</div>";
			exit;
		break;
	}

?>

<br><br>
<b>This section is currently under construction, will be updated soon... come back later</b>
