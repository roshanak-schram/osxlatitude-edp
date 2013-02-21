#!/usr/bin/perl
# Patch IOAHCIBlockStorage to delay its start by 200ms.  This delay avoids
# a timing bug which can otherwise cause the AHCI identify command to fail.
# This bug manifests itself as a "Still waiting for root device" error at boot
# time under OSX 10.8, and 10.8.x, when booting from SATA disks.
# This timing bug (race condition?) is seen on some systems with intel 6 series
# chipsets (sandy bridge h67, z68, p67), as well as some mobile sandy bridge
# chipsets.
# Affected systems include gigabyte desktop motherboards with non-uefi bios,
# mobile systems with uefi bios (at least some securecore tiano based systems)
# such as the vostro 3450.
#
# This patch works on OSX 10.8 releases starting with developer preview 4 thru
# OSX 10.8.1
#
# Version 0.3
# Copyright (c) 2012 B.C. <bcc24x7@gmail.com> (bcc9 at insanelymac.com). 
# This software was developed by B.C. <bcc24x7@gmail.com>
# 
# All advertising materials mentioning features or use of this software must
# display the following acknowledgment: This product includes software
# developed by B.C. <bcc24x7@gmail.com>
# 
# Redistribution in source or binary form is not permitted without the
# author's prior consent.  In the event such permission is given, no changes
# in or deletion of author attribution, or copyright notice shall be made to
# the copyrighted material.
# 
# THIS SOFTWARE IS PROVIDED BY THE AUTHOR ``AS IS'' AND ANY EXPRESS OR
# IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES
# OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN
# NO EVENT SHALL THE AUTHOR BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
# SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED
# TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR
# PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF
# LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING
# NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
# SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.

use Getopt::Long;

#The patch comes in two pieces:
#1. patch relocation table for our patch point - kprintf() -> IOSleep()
#2. Make unconditional call to IOSleep(delay) at beginning of kext
#I build matching strings for these two patches dynamically.

sub main()
{
    my $glob_relocation, $glob_relocation_patch, $glob_call, $glob_call_patch;
    my $match_expect;
    my $matches = 0;

    my $sledir = "/Extra/Extensions/";
    my $kext = "IOAHCIBlockStorage";
    my $plugin_parent = "IOAHCIFamily.kext/Contents/PlugIns/";
    my $file = $sledir . $plugin_parent . $kext . ".kext/Contents/MacOS/" . $kext;
    my $outfile="/tmp/$kext";
    my $delay = 250;	#200ms 
    my $testonly = 0;
    my $osxvers;

    GetOptions (
        'v+' => \$verbose,
	'd=i' => \$delay,
	't' => \$testonly,
	'o=s' => \$osxvers
	);

    $iosleep_relocation_entry=488;

    $glob_jump="\x74\x0e";
    $glob_arg1_inst="\x48\x8d\x3d";
    #Use movl
    $glob_arg1_inst_patch="\xbf";
    $glob_arg1_operand_patch = pack("l", $delay);
    $glob_call_patch= $glob_arg1_inst_patch . $glob_arg1_operand_patch .
	"\x90\x90\x90\x90";

    if (!$osxvers) {
	chomp($osxvers = `sw_vers -productVersion`);
    }
    if ($osxvers < "10.8") {
	printf "This patch is for OSX 10.8/10.8.x only\n";
	exit(1);
    }
    if ($osxvers eq "10.8") {
	$relocation_offset=0x4ceb;
	$kprintf_relocation_entry=1002;
	$glob_arg1_operand="\xa5\x90\x00\x00";
    }
    if ($osxvers eq "10.8.1") {
	$relocation_offset=0x4bbb;
	$kprintf_relocation_entry=1003;
	$glob_arg1_operand="\xb2\x91\x00\x00";
    }
    if ($osxvers >= "10.8.2") {
	$relocation_offset=0x4a8b;
	$kprintf_relocation_entry=1003;
	$glob_arg1_operand="\x72\x92\x00\x00";
    }

#build patch string for relocation table
    $glob_relocation_addr = pack("l", $relocation_offset);
    $glob_relocation_index = pack("v", $kprintf_relocation_entry);
    $glob_relocation = $glob_relocation_addr . $glob_relocation_index;
    $glob_relocation_index = pack("v", $iosleep_relocation_entry);
    $glob_relocation_patch = $glob_relocation_addr . $glob_relocation_index;
#build patch string for unconditional call to IOSleep
    $glob_call=$glob_jump . $glob_arg1_inst . $glob_arg1_operand;


    open(my $IN, '<', $file) || die "Cannot open '$file' $!";
    open(my $OUT, '>', $outfile) || die "Cannot open '$outfile' $!";
    while ( <$IN> ) {
	if (s/$glob_call/$glob_call_patch/) {
	    $matches++;
	}
	if (s/$glob_relocation/$glob_relocation_patch/) {
	    $matches++;
	}
	print $OUT $_;
    }
    close $OUT;
    close $IN;

    $match_expect = 2;
    if ($matches != $match_expect) {
	if ($matches == 0) {
	    printf "Found no matching data to patch.  $kext may already be patched\n";
	} else {
	    printf "Unexpected patch count: %d (%d expected)\n", $matches,
	    $match_expect;
	}
	printf "Aborting with $kext NOT patched\n";
	exit(1);
    }
    my $uid=`id -u`;
    if (!$testonly) {
	if ($uid != 0) {
	    printf "This script requires superuser access to update $kext\n";
	}
	system("sudo mv $file $file.orig");
	system("sudo mv $outfile $file");
	system("sudo chown root:wheel $file");
	system("sudo chmod 755 $file");
	system("sudo touch $sledir");
    }
    printf "$file patched successfully.\n";
}

main();
exit(0);
