echo "<b>Trying to fix permissions and rebuild kernel caches...</b>"

echo "<br>Copying myHack kext..."
rm -rf /System/Library/Extensions/myHack.kext
cp -rf /Extra/EDP/myHack.kext /System/Library/Extensions

echo "<br>Integrating kexts inide myHack kext..."
cp -a /Extra/Extensions/. /System/Library/Extensions/myHack.kext/Contents/PlugIns/
cp -a /Extra/Extensions.EDPFix/. /System/Library/Extensions/myHack.kext/Contents/PlugIns/

echo "<br>Fixing permissions..."
chmod -R 755 /System/Library/Extensions/myHack.kext
chown -R root:wheel /System/Library/Extensions/myHack.kext
sudo touch /System/Library/Extensions

echo "<br>Rebuilding caches..."
rm -rf /System/Library/Caches/com.apple.kext.caches/Startup/kernelcache
kextcache -prelinked-kernel /System/Library/Caches/com.apple.kext.caches/Startup/kernelcache -K /mach_kernel /System/Library/Extensions

echo "<br><b>Fixing permissions and rebuilding caches are finished.</b>"
