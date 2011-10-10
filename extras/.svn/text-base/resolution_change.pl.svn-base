#!/usr/bin/perl
#
# res.pl - script to gather resolution and set to consistent resolution
#
# CHANGES:
# 20100518-1326 - first build
#

$build = '20100518-1326';


### begin parsing run-time options ###
if (length($ARGV[0])>1)
	{
	$i=0;
	while ($#ARGV >= $i)
		{
		$args = "$args $ARGV[$i]";
		$i++;
		}

	if ($args =~ /--help/i)
		{
		print "res.pl - forces resoution change\n";
		print "allowed run time options:\n";
		print "  [-t] = test, don't change anything\n";
		print "  [-debug] = lots of debug output\n";
		print "  [-debugX] = even more debug output\n";
		print "  [-change-only] = do not add new resolution mode\n";
		print "  [-800x600] = find 800 by 600 resolution monitor specifics and set resolutions to it\n";
		print "  [-1024x768] = find 1024 by 768 resolution monitor specifics and set resolutions to it\n";
		print "  [-help] = this help message\n";
		print "\n";
		exit;
		}
	else
		{
		if ($args =~ /-change-only/i)
			{
			$change_only = 1;
			}
		if ($args =~ /-debug/i)
			{$DB=1;}
		if ($args =~ /--debugX/i)
			{
			$DB=1;
			$DBX=1;
			print "\n----- SUPER-DUPER DEBUGGING -----\nBUILD: $build\n";
			}
		if ($args =~ /-1024x768/i)
			{
			$cvt_options = '1024 768';
			$xrandr_options = '1024x768';
			}
		if ( ($args =~ /-800x600/i) || (length($cvt_options) < 1) )
			{
			$cvt_options = '800 600';
			$xrandr_options = '800x600';
			}
		if ($args =~ /-t/i)
			{
			$TEST=1;
			$T=1;
			}
		}
	}
else
	{
	$cvt_options = '800 600';
	$xrandr_options = '800x600';
	}
### end parsing run-time options ###


### find cvt binary    "cvt 800 600"
$cvtbin = '';
if ( -e ('/bin/cvt')) {$cvtbin = '/bin/cvt';}
else 
	{
	if ( -e ('/usr/bin/cvt')) {$cvtbin = '/usr/bin/cvt';}
	else 
		{
		if ( -e ('/usr/local/bin/cvt')) {$cvtbin = '/usr/local/bin/cvt';}
		else
			{
			print "Can't find cvt binary! Exiting...\n";
			exit;
			}
		}
	}

### find xrandr binary
$xrandrbin = '';
if ( -e ('/bin/xrandr')) {$xrandrbin = '/bin/xrandr';}
else 
	{
	if ( -e ('/usr/bin/xrandr')) {$xrandrbin = '/usr/bin/xrandr';}
	else 
		{
		if ( -e ('/usr/local/bin/xrandr')) {$xrandrbin = '/usr/local/bin/xrandr';}
		else
			{
			print "Can't find xrandr binary! Exiting...\n";
			exit;
			}
		}
	}

if ($change_only < 1)
	{
	if ($DBX > 0) {print "CVT COMMAND: |$cvtbin $cvt_options|\n";}

	@cvtOUTPUT = `$cvtbin $cvt_options`;
	## 800x600 59.86 Hz (CVT 0.48M3) hsync: 37.35 kHz; pclk: 38.25 MHz
	#Modeline "800x600_60.00"   38.25  800 832 912 1024  600 603 607 624 -hsync +vsync

	$Modeline_found=0;
	$ct=0;
	while( ($Modeline_found < 1) && ($ct < 10) )
		{
		if ($cvtOUTPUT[$ct] =~ /Modeline/)
			{
			$Modeline_found++;
			chomp($cvtOUTPUT[$ct]);
			$modline = $cvtOUTPUT[$ct];
			$modline =~ s/Modeline //gi;
			if ($DBX > 0) {print "MODLINE: $ct|$modline|\n";}
			}
		$ct++;
		}

	if ($DBX > 0) {print "XRANDR COMMAND: |$xrandrbin --newmode $modline|\n";}

	@xrandrOUTPUT = `$xrandrbin --newmode $modline`;


	$ct=0;
	foreach(@xrandrOUTPUT)
		{
		$modline = chomp($xrandrOUTPUT[$ct]);
		if ($DBX > 0) {print "XRANDR OUTPUT: $ct|$xrandrOUTPUT[$ct]|\n";}
		$ct++;
		}
	}

if ($DBX > 0) {print "CHANGING RESOLUTION: |$xrandrbin --size $xrandr_options|\n";}

@xrandr_sizeOUTPUT = `$xrandrbin --size $xrandr_options`;


if ($DBX > 0) {print "DONE, EXITING...\n";}


exit;
