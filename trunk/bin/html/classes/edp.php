 <?php

class edp {
 
	//-----> Updates EDP from SVN and downloads a new database
	public function update() {
		global $workpath;
    	include_once "$workpath/bin/html/edpconfig.inc.php";
    
    	echo "Cleaning up $workpath using SVN <br>\n";
    	system_call("svn cleanup $workpath/bin");
    	system_call("svn cleanup $workpath/modules");
    	system_call("svn cleanup $workpath/Themes");
    	system_call("svn cleanup $workpath/phpWebServer");
    
    	echo "\n<br>Downloading latest sources from EDP's svn server<br>\n\n";
    	system_call("svn --non-interactive --username edp --password edp --force update $workpath/bin");
    	system_call("svn --non-interactive --username edp --password edp --force update $workpath/modules");
    	system_call("svn --non-interactive --username edp --password edp --force update $workpath/Themes");
    	system_call("svn --non-interactive --username edp --password edp --force update $workpath/phpWebServer");

		// Copy the updated php binary
		system_call("cd /Extra/EDP/phpWebServer");
		system_call("rm -rf /Extra/EDP/php");
		system_call("unzip php.zip -d /Extra/EDP");
		
    	echo "\nUpdating database... <br><br>\n";
    	system_call("rm -Rf $workpath/bin/edp.sqlite3");
    	system_call("curl -o $workpath/bin/edp.sqlite3 http://www.osxlatitude.com/dbupdate.php");

    	system_call("chmod -R 755 $workpath");
    	echo "\n<br> .. Your EDP have been updated...<br><br>.. Press COMMAND+R to reload EDP...<br>\n";

    	exit;
    }

    //------> Writes a $data to $logfile
    public function writeToLog($logfile, $data) {
    	file_put_contents($logfile, $data, FILE_APPEND | LOCK_EX);
    }
}


$edp = new edp();


?> 
