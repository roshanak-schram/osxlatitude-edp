echo "Cleaning up /Extra using SVN \n"
svn cleanup /Extra
echo "\nDownloading latest sources from EDP's svn server \n"
svn --non-interactive --username edp --password edp --force update /Extra
chmod -R 755 /Extra"
