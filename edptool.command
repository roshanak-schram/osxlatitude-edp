#!/bin/sh
printf "\e[8;15;75;t"
cd "`dirname "$0"`"
clear
cd /Extra/bin
sudo sh edptool.sh
