#!/bin/bash

# VENDOR_HEX=$(ioreg -l -x -w0 | grep DisplayProductID | head -n 1 | cut -c 50-55)

# VENDOR_DEC=$(ioreg -l -w0 | grep DisplayProductID | head -n 1 | cut -c 50-55)

# echo $VENDOR_HEX
# # echo $VENDOR_DEC
# echo 'ibase=16;$VENDOR_HEX | bc'

# # echo ${VENDOR:49:6}
# # echo ${VENDOR:-6:6}
# # codec_to_patch=$(echo '\x'${1:8:2}'\x'${1:6:2}'\x'${1:4:2}'\x'${1:2:2})

# # ioreg -l -x -w0 | grep DisplayVendorID


# exit 0
echo "                      -- [spdisplays_display] Fixer --"
echo "                           v1.0 © joshhh 2008-2012"
echo "                      Fix internal LCD name on laptops"                            

# sleep 2
echo
echo "Detecting Internal LCD display VendorID and ProductID"

sleep 1
VenID=`ioreg -n AppleBacklightDisplay -rxw0 | grep DisplayVendorID | sed 's/.*0x//'`
# Collecting the product ID
ProdID=`ioreg -n AppleBacklightDisplay -rxw0 | grep DisplayProductID | sed 's/.*0x//'`
# Preparing the names of the output file and folder
genFile=DisplayProductID-$ProdID
genDir=DisplayVendorID-$VenID
# Byteflipping the VendorID
if [ "$(( ${#VenID} % 2 ))" -eq "0" ]
then
    VenIDflip=${VenID:${#VenID}-2:2}${VenID:2:${#VenID}-4}${VenID:0:2}
else
    VenIDflip=${VenID:${#VenID}-2:2}${VenID:1:${#VenID}-3}0${VenID:0:1}
fi
# Showing what the script has collected
echo
echo "Vendor ID: 0x$VenID (hex) $((0x$VenID)) (dec)"
echo "Product ID: 0x$ProdID (hex) $((0x$ProdID)) (dec)"
# echo "Flipbyted Vendor ID: 0x$VenIDflip (hex) $((0x$VenIDflip)) (dec)"
# echo
sleep 1
# echo "Now create folder and configuration file..."
# echo
# echo "Detected Vendor ID: 0x$VenID (hex) $((0x$VenID)) (dec)"
# echo "Detected Product ID: 0x$ProdID (hex) $((0x$ProdID)) (dec)"
# echo "Flipbyted Vendor ID: 0x$VenIDflip (hex) $((0x$VenIDflip)) (dec)"
# echo
# echo $genFile
echo
echo "Configuration folder $genDir created in:"
echo "/System/Library/Displays/Overrides/"
sleep 1
echo
echo "Configuration file $genFile created in:"
echo "/System/Library/Displays/Overrides/$genDir"
# echo

echo `sudo mkdir /System/Library/Displays/Overrides/$genDir`
# sudo cd /System/Library/Displays/Overrides | mkdir $genDir
# echo
echo "<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE plist PUBLIC "-//Apple//DTD PLIST 1.0//EN" "http://www.apple.com/DTDs/PropertyList-1.0.dtd">
<plist version="1.0">
<dict>
	<key>DisplayProductID</key>
	<integer>$((0x$ProdID))</integer>
	<key>DisplayVendorID</key>
	<integer>$((0x$VenIDflip))</integer>
	<key>DisplayProductName</key>
	<string>Color LCD</string>
</dict>
</plist>" > ~/Desktop/$genFile

sudo mv ~/Desktop/$genFile /System/Library/Displays/Overrides/$genDir
# echo $genDir/$genFile

# echo
echo "All operations completed"
echo
echo "You must reboot your computer for the changes to take effect"
echo
