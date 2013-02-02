
#!/bin/sh
printf "\e[8;50;180;t"
cd "`dirname "$0"`"
clear
cd /Extra/bin/lws
sudo sh startup.sh
