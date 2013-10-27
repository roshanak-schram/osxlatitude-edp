cd /Extra/EDP/bin
killall php
./php -S 127.0.0.1:11250 -t /Extra/EDP/bin/html &
sleep 1;
php launch.php
