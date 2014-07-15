<?php
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
	} else {
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
	} else {
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


//-----------------------------------------------------------------> Start rendering the page
echoPageItemTOP("icons/big/edp.png", "Welcome to EDP");
echo "<div class='pageitem_bottom'>\n";
	
?>

EDP is a unique control panel for your hackintosh that makes it easy to maintain and configure your system. EDP's internal database contains 'best practice' schematics for 80+ systems - this makes it easy to choose the right configuration</p>	
<br><br>

<div id="container"></div>
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
