echo "Cleaning up /Extra using SVN \n"
svn cleanup /Extra
echo "Downloading latest sources from EDP's svn server \n"
svn --non-interactive --username edp --password edp --force update /Extra

cd /Extra/bin

rm -Rf edp.sqlite3

if curl -o edp.sqlite3 http://www.osxlatitude.com/dbupdate.php --connect-timeout 10 ; then
    echo "Database successfully downloaded..."
else
    echo "Could not update database... using backup"
    cp backup/edp.sqlite3 ./edp.sqlite3
fi

chmod -R 755 /Extra