var edp = new edpjscore();


function edpjscore() {
	//Returns "yes" if the core is loaded
	this.isLoaded = function() {
    	return "yes";
    }
    
    //Makes a javascript alert
    this.alert = function(msg) {
    	alert(msg);
    }		
		
	//Bootloader called onload in index.php		
	this.bootloader = function() {
		aligndesign();
	}

    //Used to open an external URL
    this.openlink = function(url) {
    	alert(url);
    	window.open(url, 'External URL');
    }
    
    
	//Returns height of window
	this.getHeight = function() {
		if (document.body && document.body.offsetWidth) {
        	h = document.body.offsetHeight;
        }
        if (document.compatMode == 'CSS1Compat' && document.documentElement && document.documentElement.offsetWidth) {
        	h = document.documentElement.offsetHeight;
        }
        if (window.innerWidth && window.innerHeight) {
            h = window.innerHeight;
        }
        return h;
    }            

    //Returns width of window
    this.getWidth = function() {
		if (document.body && document.body.offsetWidth) {
    		w = document.body.offsetWidth;
        }
        if (document.compatMode == 'CSS1Compat' && document.documentElement && document.documentElement.offsetWidth) {
        	w = document.documentElement.offsetWidth;
        }
        if (window.innerWidth && window.innerHeight) {
        	w = window.innerWidth;
        }
        
        return w;
    }











}
	
	
