echo "Cleaning up /Extra using SVN \n"
svn cleanup /Extra
echo "Downloading latest sources from EDP's svn server \n"
svn --non-interactive --username edp --password edp --force update /Extra


cd /Extra/bin
rm -Rf edp.sqlite3
curl -o edp.sqlite3 http://www.osxlatitude.com/dbupdate.php

chmod -R 755 /Extra

