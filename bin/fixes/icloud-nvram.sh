#! /bin/sh
echo "This fix will fix the iCloud Find my Mac problem.\n"
echo "You need to put the check on manually!"
sleep 2
cd /Library/LaunchDaemons

sudo cp storage/com.nvram.root.plist /Library/LaunchDaemons/com.nvram.root.plist
sudo chown root:wheel /Library/LaunchDaemons/com.nvram.root.plist

# Put iCloud fix on!
echo "Go to iCloud and check in Find My Mac. You have 1 minute to do this!\n\n"
sleep 60
# Clear nvram
sudo nvram -p > /var/log/nvram.log

# Remove file nvram from Extra folder
cd /Extra/
sudo rm nvram*

# Finished!
echo "nvram file created! You can restart your computer now!\n"
echo "After restart verify that Find My Mac check box is still selected. If so the edit is working successfully."