var iWebkit;if(!iWebkit){iWebkit=window.onload=function(){function fullscreen(){var a=document.getElementsByTagName("a");for(var i=0;i<a.length;i++){if(a[i].className.match("noeffect")){}else{a[i].onclick=function(){window.location=this.getAttribute("href");return false}}}}function hideURLbar(){window.scrollTo(0,0.9)}iWebkit.init=function(){fullscreen();hideURLbar()};iWebkit.init()}}


function checkUpdates() {
	<?php
		global $localrev; global $workpath;
		$remoterev      = exec("cd $workpath; svn info -r HEAD --username edp --password edp --non-interactive | grep -i \"Last Changed Rev\"");
		$remoterev      = str_replace("Last Changed Rev: ", "", $remoterev);
		$number_updates = ($remoterev - $localrev); 
		if ($number_updates > "0") { echo "window.fluid.dockBadge = '$number_updates';"; }
	?>
	return;
}