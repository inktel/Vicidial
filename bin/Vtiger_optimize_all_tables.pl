#!/usr/bin/perl
#
# Vtiger_optimize_all_tables.pl       version 2.0.5   *DBI-version*
#
# DESCRIPTION:
# runs optimize table on all Vtiger tables in MySQL database
#
# Copyright (C) 2009  Matt Florell <vicidial@gmail.com>    LICENSE: AGPLv2
#
#
# CHANGES
# 90422-0521 - First build
#

$secX = time();

# default path to astguiclient configuration file:
$PATHconf =		'/etc/astguiclient.conf';

open(conf, "$PATHconf") || die "can't open $PATHconf: $!\n";
@conf = <conf>;
close(conf);
$i=0;
foreach(@conf)
	{
	$line = $conf[$i];
	$line =~ s/ |>|\n|\r|\t|\#.*|;.*//gi;
	if ( ($line =~ /^PATHhome/) && ($CLIhome < 1) )
		{$PATHhome = $line;   $PATHhome =~ s/.*=//gi;}
	if ( ($line =~ /^PATHlogs/) && ($CLIlogs < 1) )
		{$PATHlogs = $line;   $PATHlogs =~ s/.*=//gi;}
	if ( ($line =~ /^PATHagi/) && ($CLIagi < 1) )
		{$PATHagi = $line;   $PATHagi =~ s/.*=//gi;}
	if ( ($line =~ /^PATHweb/) && ($CLIweb < 1) )
		{$PATHweb = $line;   $PATHweb =~ s/.*=//gi;}
	if ( ($line =~ /^PATHsounds/) && ($CLIsounds < 1) )
		{$PATHsounds = $line;   $PATHsounds =~ s/.*=//gi;}
	if ( ($line =~ /^PATHmonitor/) && ($CLImonitor < 1) )
		{$PATHmonitor = $line;   $PATHmonitor =~ s/.*=//gi;}
	if ( ($line =~ /^VARserver_ip/) && ($CLIserver_ip < 1) )
		{$VARserver_ip = $line;   $VARserver_ip =~ s/.*=//gi;}
	if ( ($line =~ /^VARDB_server/) && ($CLIDB_server < 1) )
		{$VARDB_server = $line;   $VARDB_server =~ s/.*=//gi;}
	if ( ($line =~ /^VARDB_database/) && ($CLIDB_database < 1) )
		{$VARDB_database = $line;   $VARDB_database =~ s/.*=//gi;}
	if ( ($line =~ /^VARDB_user/) && ($CLIDB_user < 1) )
		{$VARDB_user = $line;   $VARDB_user =~ s/.*=//gi;}
	if ( ($line =~ /^VARDB_pass/) && ($CLIDB_pass < 1) )
		{$VARDB_pass = $line;   $VARDB_pass =~ s/.*=//gi;}
	if ( ($line =~ /^VARDB_port/) && ($CLIDB_port < 1) )
		{$VARDB_port = $line;   $VARDB_port =~ s/.*=//gi;}
	if ( ($line =~ /^VARREPORT_host/) && ($CLIREPORT_host < 1) )
		{$VARREPORT_host = $line;   $VARREPORT_host =~ s/.*=//gi;}
	if ( ($line =~ /^VARREPORT_user/) && ($CLIREPORT_user < 1) )
		{$VARREPORT_user = $line;   $VARREPORT_user =~ s/.*=//gi;}
	if ( ($line =~ /^VARREPORT_pass/) && ($CLIREPORT_pass < 1) )
		{$VARREPORT_pass = $line;   $VARREPORT_pass =~ s/.*=//gi;}
	if ( ($line =~ /^VARREPORT_port/) && ($CLIREPORT_port < 1) )
		{$VARREPORT_port = $line;   $VARREPORT_port =~ s/.*=//gi;}
	$i++;
	}

# Customized Variables
$server_ip = $VARserver_ip;		# Asterisk server IP

### begin parsing run-time options ###
if (length($ARGV[0])>1)
	{
	$i=0;
	while ($#ARGV >= $i)
		{
		$args = "$args $ARGV[$i]";
		$i++;
		}

	if ($args =~ /--help|-h/i)
		{
		print "allowed run time options:\n";
		print "  [-q] = quiet\n";
		print "  [--test] = test\n";
		print "  [--debug] = debug output\n";
		print "  [-h] = this help screen\n\n";

		exit;
		}
	else
		{
		if ($args =~ /-q/i)
			{
			$q=1;
			$DB=0;
			$DBX=0;
			$force_quiet=1;
			}
		if ( ($args =~ /-debug/i) && ($force_quiet < 1) )
			{
			$DB=1;
			print "\n----- DEBUGGING -----\n\n";
			}
		if ( ($args =~ /-debugX/i) && ($force_quiet < 1) )
			{
			$DBX=1;
			print "\n----- SUPER-DUPER DEBUGGING -----\n\n";
			}
		else {$DBX=0;}

		if ($args =~ /-test/i)
			{
			$T=1;
			if ($q < 1) {print "\n----- TESTING -----\n\n";}
			}
		else
			{$T=0;}
		}
	}
else
	{
	print "no command line options set\n";
	$args = "";
	$i=0;
	$forcelistid = '';
	$T=0;
	}
### end parsing run-time options ###

if ($q < 1)
	{
	print "\n\n\n\n\n\n\n\n\n\n\n\n-- Vtiger_optimize_all_tables.pl --\n\n";
	print "This program is designed to optimize all tables in the VtigerCRM MySQL database.\n\n";
	}

$i=0;
$US = '_';
$phone_list = '|';

if (!$VARDB_port) {$VARDB_port='3306';}

use DBI;	  

$dbhA = DBI->connect("DBI:mysql:$VARDB_database:$VARDB_server:$VARDB_port", "$VARDB_user", "$VARDB_pass")
 or die "Couldn't connect to database: " . DBI->errstr;

#############################################
##### START SYSTEM_SETTINGS LOOKUP #####
$stmtA = "SELECT use_non_latin,vtiger_server_ip,vtiger_dbname,vtiger_login,vtiger_pass FROM system_settings;";
$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
$sthArows=$sthA->rows;
if ($sthArows > 0)
	{
	@aryA = $sthA->fetchrow_array;
	$non_latin = 		"$aryA[0]";
	$vtiger_server_ip =	"$aryA[1]";
	$vtiger_dbname = 	"$aryA[2]";
	$vtiger_login = 	"$aryA[3]";
	$vtiger_pass = 		"$aryA[4]";
	}
$sthA->finish();
##### END SETTINGS LOOKUP #####
###########################################

$dbhB = DBI->connect("DBI:mysql:$vtiger_dbname:$vtiger_server_ip:$VARDB_port", "$vtiger_login", "$vtiger_pass")
 or die "Couldn't connect to database: " . DBI->errstr;

if ($non_latin > 0) {$affected_rows = $dbhA->do("SET NAMES 'UTF8'");}


$i=0;	### each table counter ###

$stmtB="show tables;";
$sthB = $dbhB->prepare($stmtB) or die "preparing: ",$dbhB->errstr;
$sthB->execute or die "executing: $stmtB ", $dbhB->errstr;
$sthBrows=$sthB->rows;
while ($sthBrows > $i)
	{
	@aryB = $sthB->fetchrow_array;
	$tables[$i] = "$aryB[0]";
	$i++;
	}
$sthB->finish();

if ($q < 1) {print "VtigerCRM Database tables found: $i\n";}


if ( ($i > 0) && ($T < 1) )
	{
	$j=0;
	while ($i > $j)
		{
		$stmtB="OPTIMIZE TABLE $tables[$j];";
		$sthB = $dbhB->prepare($stmtB) or die "preparing: ",$dbhB->errstr;
		$sthB->execute or die "executing: $stmtB ", $dbhB->errstr;
		$sthBrows=$sthB->rows;
		if ($sthBrows > 0)
			{
			@aryB = $sthB->fetchrow_array;
			if ($q < 1) {print "$stmtB|$aryB[0]|$aryB[1]|$aryB[2]|$aryB[3]\n";}
			}
		$sthB->finish();
		$j++;
		}

	}



### calculate time to run script ###
$secY = time();
$secZ = ($secY - $secX);
$secZm = ($secZ /60);

if ($q < 1)
	{
	print "script execution time in seconds: $secZ     minutes: $secZm\n";
	}

exit;

