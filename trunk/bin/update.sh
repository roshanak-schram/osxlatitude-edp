
// create download stat file
cd /Extra/EDP/kpsvn/dload/statFiles
touch edpUpdate.txt

echo "Cleaning up EDP using SVN \n"
svn cleanup /Extra/EDP/bin
svn cleanup /Extra/EDP/modules
svn cleanup /Extra/EDP/Themes
svn cleanup /Extra/EDP/phpWebServer

echo "Downloading latest sources from EDP's svn server \n"
svn --non-interactive --username edp --password edp --force update /Extra/EDP/bin
svn --non-interactive --username edp --password edp --force update /Extra/EDP/modules
svn --non-interactive --username edp --password edp --force update /Extra/EDP/Themes
svn --non-interactive --username edp --password edp --force update /Extra/EDP/phpWebServer

cd /Extra/EDP/phpWebServer
rm -rf /Extra/EDP/php
unzip php.zip -d /Extra/EDP

echo "\nEDP files updated. \n"

cd /Extra/EDP/bin

echo "\nUpdating database... \n"

rm -Rf edp.sqlite3

if curl -o edp.sqlite3 http://www.osxlatitude.com/dbupdate.php --connect-timeout 10 ; then
    echo "Database successfully downloaded \n"
else
    echo "Could not update database... using backup \n"
    cp backup/edp.sqlite3 ./edp.sqlite3
fi

chmod -R 755 /Extra/EDP

echo "Update finished. \n"

// remove download stat file after update
cd /Extra/EDP/kpsvn/dload/statFiles
rm -rf edpUpdate.txt
