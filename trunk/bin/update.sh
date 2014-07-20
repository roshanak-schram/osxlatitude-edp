echo "<br>Updating database..."

cd /Extra/EDP/bin
echo "<br>Making a backup..."
rm -Rf /Extra/EDP/bin/backup/edp.sqlite3
cp edp.sqlite3 /Extra/EDP/bin/backup
echo "Backup finished"
rm -Rf edp.sqlite3

echo "Downloading new database..."
if curl -o edp.sqlite3 http://www.osxlatitude.com/dbupdate.php --connect-timeout 10 ; then
    echo "Database successfully downloaded <br>"
    touch Updsuccess.txt
else
    echo "Could not update database... using backup <br>"
    cp backup/edp.sqlite3 ./edp.sqlite3
fi

echo "Cleaning up EDP svn... <br>"
svn cleanup /Extra/EDP/bin
svn cleanup /Extra/EDP/phpWebServer

echo "Downloading latest sources from EDP's svn server<br>"
echo "Updating EDP files..."
if svn --non-interactive --username edp --password edp --force update /Extra/EDP/bin; then
	echo "Updating PHP binary..."
	if svn --non-interactive --username edp --password edp --force update /Extra/EDP/phpWebServer; then
		cd /Extra/EDP/phpWebServer
		rm -rf /Extra/EDP/php
		unzip -qq php.zip -d /Extra/EDP
		cd /Extra/EDP/logs/update
		touch Updsuccess.txt
		echo "Update success"
	else
		cd /Extra/EDP/logs/update
		touch Updfail.txt
		echo "Update failed (may be no internet or failed to connect to svn)"
	fi
else
	cd /Extra/EDP/logs/update
	touch Updfail.txt
	echo "Update failed (may be no internet or failed to connect to svn)"
fi

chmod -R 755 /Extra/EDP/bin
chmod -R 755 /Extra/EDP/phpWebServer

echo "Update finished."
