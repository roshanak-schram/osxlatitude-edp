
// create download stat file
cd /Extra/EDP/kpsvn/dload/statFiles
touch edpUpdate.txt

echo "Cleaning up EDP using SVN <br>"
svn cleanup /Extra/EDP/bin
svn cleanup /Extra/EDP/phpWebServer

echo "Downloading latest sources from EDP's svn server<br>"
echo "Updating EDP files..."
svn --non-interactive --username edp --password edp --force update /Extra/EDP/bin
echo "Updating PHP binary..."
svn --non-interactive --username edp --password edp --force update /Extra/EDP/phpWebServer

cd /Extra/EDP/phpWebServer
rm -rf /Extra/EDP/php
unzip php.zip -d /Extra/EDP


echo "<br>Updating database..."

cd /Extra/EDP/bin

rm -Rf edp.sqlite3

if curl -o edp.sqlite3 http://www.osxlatitude.com/dbupdate.php --connect-timeout 10 ; then
    echo "Database successfully downloaded <br>"
else
    echo "Could not update database... using backup <br>"
    cp backup/edp.sqlite3 ./edp.sqlite3
fi

chmod -R 755 /Extra/EDP

echo "Update finished."

// remove download stat file after update
cd /Extra/EDP/kpsvn/dload/statFiles
rm -rf edpUpdate.txt
