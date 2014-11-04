echo "<b>Trying to fix system kexts permissions and rebuild caches...</b>"

echo "<br>Fixing permissions..."
sudo touch /System/Library/Extensions

echo "<br>Rebuilding caches..."
sudo kextcache -system-prelinked-kernel

echo "<br><b>Fixing permissions and rebuilding caches are finished.</b>"
