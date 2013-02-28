<?php
	include_once "../../config.inc.php";
?>
var iWebkit;if(!iWebkit){iWebkit=window.onload=function(){function fullscreen(){var a=document.getElementsByTagName("a");for(var i=0;i<a.length;i++){if(a[i].className.match("noeffect")){}else{a[i].onclick=function(){window.location=this.getAttribute("href");return false}}}}function hideURLbar(){window.scrollTo(0,0.9)}iWebkit.init=function(){fullscreen();hideURLbar()};iWebkit.init()}}

function aligndesign() {
		if (document.body && document.body.offsetWidth) {
    		w = document.body.offsetWidth;
        	h = document.body.offsetHeight;
        }
        if (document.compatMode == 'CSS1Compat' && document.documentElement && document.documentElement.offsetWidth) {
        	w = document.documentElement.offsetWidth;
        	h = document.documentElement.offsetHeight;
        }
        if (window.innerWidth && window.innerHeight) {
        	w = window.innerWidth;
            h = window.innerHeight;
        }

        //Calculate and correction of the console_iframe
        var console_iframe_width = w - 285;
        var console_iframe_height = h - 69;
        document.getElementById('console_iframe').style.width = console_iframe_width + 'px';
        document.getElementById('console_iframe').style.height = console_iframe_height + 'px';

        //Calculate and correction of menu dev
        var edpmenu_div_height = h - 69;
        document.getElementById('edpmenu').style.height = edpmenu_div_height + 'px';
}

function load(page) {
	document.location.href = page;
}

function loadModule(page) {
	top.document.getElementById('console_iframe').src = page;
}
            