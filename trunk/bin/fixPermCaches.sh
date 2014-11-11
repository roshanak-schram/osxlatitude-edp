echo "<b>Trying to fix System kexts permissions and rebuild caches...</b>"

echo "<br>Fixing permissions..."
chmod -R 755 /System/Library/Extensions
chown -R root:wheel /System/Library/Extensions
sudo touch /System/Library/Extensions

echo "<br>Rebuilding caches..."
rm -rf /System/Library/Caches/com.apple.kext.caches/Startup/kernelcache
kextcache -prelinked-kernel /System/Library/Caches/com.apple.kext.caches/Startup/kernelcache -K /System/Library/Kernels/kernel /System/Library/Extensions

echo "<br><b>Fixing permissions and rebuilding caches are finished.</b>"
