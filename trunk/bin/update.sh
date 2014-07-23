echo "<br>Updating database..."
rm -rf /Extra/EDP/logs/update/*

echo "<br>Making a backup..."
cd /Extra/EDP/bin
rm -Rf /Extra/EDP/bin/backup/edp.sqlite3
cp edp.sqlite3 /Extra/EDP/bin/backup

echo "<br>Cleaning up old files..."
rm -Rf edp.sqlite3
rm -rf /Extra/EDP/logs/update/*

echo "Downloading new database..."
if curl -o edp.sqlite3 http://www.osxlatitude.com/dbupdate.php --connect-timeout 10 ; then
    echo "Database successfully downloaded <br>"
	touch /Extra/EDP/logs/update/Updsuccess.txt
else
    echo "Could not update database... using backup <br>"
    cp backup/edp.sqlite3 ./edp.sqlite3
	touch /Extra/EDP/logs/update/Updfail.txt
fi

echo "<br>Database Update finished."

echo "Cleaning up EDP svn..."
svn cleanup /Extra/EDP/bin
svn cleanup /Extra/EDP/phpWebServer

echo "Downloading latest sources from EDP's svn server<br>"
echo "Updating EDP files..."
if svn --non-interactive --username edp --password edp --force update /Extra/EDP/bin; then
	echo "Updating PHP binary..."
	if svn --non-interactive --username edp --password edp --force update /Extra/EDP/phpWebServer; then
		cd /Extra/EDP/phpWebServer
		rm -rf /Extra/EDP/php
		unzip -X -qq php.zip -d /Extra/EDP
		touch /Extra/EDP/logs/update/Updsuccess.txt
		echo "Update success"
	else
		touch /Extra/EDP/logs/update/Updfail.txt
		echo "Update failed (may be no internet or failed to connect to svn)"
	fi
else
	touch /Extra/EDP/logs/update/Updfail.txt
	echo "Update failed (may be no internet or failed to connect to svn)"
fi

chmod -R 755 /Extra/EDP/bin
chmod -R 755 /Extra/EDP/phpWebServer

echo "<br>EDP Updates finished."
