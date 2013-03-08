#! /bin/sh
echo "This fix will fix the iCloud Find my Mac problem."
echo "You need to put the check on manually!\n"
sleep 2
cd /Library/LaunchDaemons
sudo cp /Extra/bin/fixes/storage/com.nvram.root.plist /Library/LaunchDaemons/com.nvram.root.plist
sudo chown root:wheel /Library/LaunchDaemons/com.nvram.root.plist

# Put iCloud fix on!
echo "Go to iCloud and check in Find My Mac. You have 1 minute to do this!"
echo "After this menu, the script will automatically continue.\n"
sleep 60
# Clear nvram
sudo nvram -p > /var/log/nvram.log

cd /Extra/
sudo rm nvram*

# Finished!
echo "\nnvram file created! You can restart your computer now!\n"
echo "After restart verify that Find My Mac check box is still selected. 
echo "If so the edit is working successfully."