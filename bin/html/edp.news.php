<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<meta content="yes" name="apple-mobile-web-app-capable" />
<meta content="index,follow" name="robots" />
<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
<link href="pics/homescreen.gif" rel="apple-touch-icon" />
<meta content="minimum-scale=1.0, width=device-width, maximum-scale=0.6667, user-scalable=no" name="viewport" />
<link href="css/developer-style.css" rel="stylesheet" media="screen" type="text/css" />
<script src="javascipt/functions.js" type="text/javascript"></script>
<title>EDP</title>
</head>

<body>

<div id="topbar">
	<div id="leftnav"><a href="index.php"><img alt="home" src="images/home.png" /></a></div>
	<div id="title">EDP Changelog</div>
	<div id="rightbutton"><a href="#" onclick="loadModule('workerapp.php?action=close-edpweb');" class="noeffect">&nbsp; Close &nbsp;</a> </div>
</div>
<?php
include "tributton.menu.inc.php";
?>

<div id="content">
	<?php include("include/rss.php");?>
</div>
</html>
