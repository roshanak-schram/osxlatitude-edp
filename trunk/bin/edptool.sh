cd /Extra/bin
killall php
./php -S 127.0.0.1:11250 -t /Extra/bin/html &
sleep 1;
php launch.php
