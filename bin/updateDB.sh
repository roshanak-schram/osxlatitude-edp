echo "Updating database..."

echo "<br>Making a backup..."
cd /Extra/EDP/bin
rm -Rf /Extra/EDP/bin/backup/edp.sqlite3
cp edp.sqlite3 /Extra/EDP/bin/backup

echo "Cleaning up database..."
rm -Rf edp.sqlite3

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
