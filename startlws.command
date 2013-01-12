
#!/bin/sh
printf "\e[8;50;180;t"
cd "`dirname "$0"`"
clear
cd /Extra/bin/lws
sudo sh startup.sh &
sleep 3
open http://127.0.0.1:11250/index.php
