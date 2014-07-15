echo "Cleaning up /Extra/EDP using SVN \n"
svn cleanup /Extra/EDP/bin
svn cleanup /Extra/EDP/modules
svn cleanup /Extra/EDP/Themes
svn cleanup /Extra/EDP/phpWebServer

echo "Downloading latest sources from EDP's svn server \n"
svn --non-interactive --username edp --password edp --force update /Extra/EDP/bin
svn --non-interactive --username edp --password edp --force update /Extra/EDP/modules
svn --non-interactive --username edp --password edp --force update /Extra/EDP/Themes
svn --non-interactive --username edp --password edp --force update /Extra/EDP/phpWebServer

echo “Updating php binary from EDP's svn server \n"
cd /Extra/EDP/phpWebServer
rm -rf /Extra/EDP/php
unzip php.zip -d /Extra/EDP

cd /Extra/EDP/bin

rm -Rf edp.sqlite3

if curl -o edp.sqlite3 http://www.osxlatitude.com/dbupdate.php --connect-timeout 10 ; then
    echo "Database successfully downloaded..."
else
    echo "Could not update database... using backup"
    cp backup/edp.sqlite3 ./edp.sqlite3
fi

chmod -R 755 /Extra/EDP