
#!/bin/sh
printf "\e[8;50;180;t"
cd "`dirname "$0"`"
clear
sudo sh bin/edptool.sh
