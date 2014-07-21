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
else
    echo "Could not update database... using backup <br>"
    cp backup/edp.sqlite3 ./edp.sqlite3
fi

echo "<br>Database Update finished."
