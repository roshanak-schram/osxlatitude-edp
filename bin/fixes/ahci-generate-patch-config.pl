#!/usr/bin/perl
# Parse IOAHCIBlockStorage, to determine patch information for
# patch-ahci-mlion.pl
#
# Version 0.1
#
# Copyright (c) 2012-2013 B.C. <bcc24x7@gmail.com> (bcc9 at insanelymac.com). 
# All rights reserved.

use Getopt::Long;
use Data::Compare;
use Data::Dumper;

my $sledir = "/System/Library/Extensions";
my $verbose = 0;

#Given a hex string in an operand string, strip off preceeding 0x and any index
#register, just leaving the hex value itself.  Convert result to decimal.
sub hexstrtoint
{
    my($prefix, $val, $post) = split(/0x|\(/, $_[0], 3);
    return hex($val);
}

# Read configuration file
sub read_config
{
    my $file = $_[0];
    our $err;
    my $rc;

    # Process the contents of the config file
    $rc = do($file);

    # Check for errors
    if ($@) {
	$::err = "ERROR: Failure compiling '$file' - $@";
    } elsif (! defined($rc)) {
	$::err = "ERROR: Failure reading '$file' - $!";
    } elsif (! $rc) {
	$::err = "ERROR: Failure processing '$file'";
    }
    return ($err);
}

sub parse_relocation
{
    my $dump = $_[0];
    my $sleepaddr = $_[1];
    my $kprintfaddr = $_[2];

    open(IN, "$dump |") || die "Cannot open $dump for input\n";
    if ($verbose > 2) {
	printf "IOSleep address: %s\n", $sleepaddr;
	printf "Kprintf address: %s\n", $kprintfaddr;
    }
    while ($_ = <IN>) {
	$match = "";
	if (/$sleepaddr/) {
	    $match = "IOSleep";
	} elsif (/$kprintfaddr/) {
	    $match = "kprintf";
	}
	if ($match) {
	    my @relocation = split(/ /, $_);
	    my $index = $relocation[-1];
	    chomp $index; 
	    if ($verbose) {
		printf "\$%s_relocation_index=%s;\n", $match, $index;
	    }
	    if ($match eq "IOSleep") {
		$patch->{IOSleep_relocation_index} = $index;
	    } else {
		$patch->{kprintf_relocation_index} = $index;
	    }
	}
	if (/$kprintfaddr/) {
	    last;
	}
    }
    close(IN);
}

sub parse_instr
{
    my $dump = $_[0];
    my $callop;
    my $pc;
    my @callarg = ();
    my $got_arg = 0;

    open(IN, "$dump |") || die "Cannot open $dump for input\n";
    while ($_ = <IN>) {
	if (!/IOAHCIBlockStorageDriver::start/) {
	    next;
	}

	$callarg = "leaq";
	$callop = "callq";
	while (!/ret$/) {
	    $_ = <IN>;
	    if ($verbose > 3) {
		print $_;
	    }
	    if (/$callarg/) {
		push(@callarg, $_);
		$got_arg = 1;
	    }
	    if (/$callop/) {
		$callinst = $_;
		last;
	    }
	}
	if (!$callinst || $#callarg == -1) {
	    print "Error: debug kprintf not found\n";
	} else {
	    my($addr, $operator, $operands) = split(/\t/, $callinst, 3);
	    $pc = hex($addr);
	    $relocation_offset = $pc + 1;
	    $patch->{relocation_offset} = $relocation_offset;
	    if ($verbose) {
		printf "\$relocation_offset=0x%x;\n" , $relocation_offset;
	    }
	    $calladdr = sprintf("%.8x", $relocation_offset);
#	    printf "Relocation address padded: %s\n" , $calladdr;

	    my($addr, $operator, $operands) = split(/\t/, $callarg[-1], 3);
	    my($op1, $op2) = split(/,/, $operands, 2);
	    $kprintf_arg = hexstrtoint($op1);
	    $patch->{kprintf_arg} = $kprintf_arg;
	    if ($verbose) {
		printf "\$kprintf_arg=0x%x;\n" , $kprintf_arg;
	    }
	}
	last;
    }
    close(IN);
}

#See if patch is already in our config array
#If not, add it
sub add_patch()
{
    foreach my $p (@patches) {
	if ($p->{osx_version} eq $patch->{osx_version}) {
	    if (Compare($p,$patch)) {
		printf "Patch for this OSX version already present in config file\n";
		return;
	    }
	    printf "Patch for this OSX version found in config file mismatches with our calculated patch\n";
	    printf "Patch for this OSX version found in config file mismatches with our calculated patch.  Aborting\n";
	    exit(1);
	}
    }
    printf "Adding patch for OSX %s:\n", $patch->{osx_version};
    print STDERR Data::Dumper->Dump([\$patch], ['*patch']);
    push @patches, $patch;
}

sub dump_patches()
{
    $Data::Dumper::Purity = 1;

#    foreach my $patch (@patches) {
#	printf "{ %s => %x,\n", "osx_version", $patch->{osx_version};
#	printf "  %s => %x,\n", "relocation_offset", $patch->{relocation_offset};
#	printf "  %s => %x,\n", "kprintf_arg", $patch->{kprintf_arg};
#	printf "  %s => %x,\n", "IOSleep_relocation_index", $patch->{IOSleep_relocation_index};
#	printf "  %s => %x\n}\n", "kprintf_relocation_index", $patch->{kprintf_relocation_index};
#    }
#    printf "\n";

#    print STDERR Data::Dumper->Dump([\@patches], ['*patches']);

    open (FILE, "> /Extra/bin/fixes/patch-ahci.config") or die "can't open /Extra/bin/fixes/patch-ahci.config: $!";
    print FILE Data::Dumper->Dump([\@patches], ['*patches']);
    close FILE        
}

sub main()
{
    my $dump, $kextbin;
    my $kext = "IOAHCIBlockStorage";
    my $plugin_parent = "/IOAHCIFamily.kext/Contents/PlugIns/";

    GetOptions (
        'v+' => \$verbose,
        's=s' => \$sledir
	);
    
    chomp($osxvers = `sw_vers -productVersion`);
    if ($verbose) {
	printf "OS version: %s\n", $osxvers;
    }

    if ($err = read_config("/Extra/bin/fixes/patch-ahci.config")) {
	printf "No existing patch config file was found; a new one will be generated.\n";
    }
    $patch = {};
    $patch->{osx_version} = $osxvers;
    $kextbin = $sledir . $plugin_parent . $kext . ".kext/Contents/MacOS/" . $kext;
    if ($verbose > 3) {
	printf "Kext %s\n", $kextbin;
    }
    chomp($have_otool=`which otool`);
    if ($have_otool eq "") {
	printf "This script requires otool to be installed in the standard location.
otool is part of the package 'Command Line Tools' for Xcode, available
at https://developer.apple.com/downloads/index.action\n";
	exit(1);
    }
    $dump="otool -vt " . $kextbin . " | c++filt";
    parse_instr($dump);
    chomp($sleepaddrstr = `otool -vr $kextbin | grep IOSleep | head -1`);
    my($sleepaddr) = split(/ /, $sleepaddrstr);
#    printf "Sleep: %s\n", $sleepaddr;
    $dump="otool -r " . $kextbin;
    parse_relocation($dump, $sleepaddr, $calladdr);
    add_patch();
    dump_patches();
}

main();
exit(0);
