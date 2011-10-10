#!/usr/bin/perl
#
# Vtiger_KILL_slow_queries.pl            version 2.2.0
#
# DESCRIPTION:
# script kills rogue mysql SELECT queries that are launched by vtiger
#
#
# Copyright (C) 2009  Matt Florell <vicidial@gmail.com>    LICENSE: AGPLv2
#
#
# CHANGES
# 90727-0757 - First build
#

$secX = time();
$MT[0]='';
$Ealert='';
$importasdeleted=0;

($sec,$min,$hour,$mday,$mon,$year,$wday,$yday,$isdst) = localtime(time);
$year = ($year + 1900);
$mon++;
if ($hour < 10) {$hour = "0$hour";}
if ($min < 10) {$min = "0$min";}
if ($sec < 10) {$sec = "0$sec";}
if ($mon < 10) {$mon = "0$mon";}
if ($mday < 10) {$mday = "0$mday";}
$pulldate0 = "$year-$mon-$mday $hour:$min:$sec";
$NOW_TIME = "$year-$mon-$mday $hour:$min:$sec";
$inSD = $pulldate0;
$dsec = ( ( ($hour * 3600) + ($min * 60) ) + $sec );
$MT[0]='';


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


if (!$VDHLOGfile) {$VDHLOGfile = "$PATHlogs/VTkillslow.$year-$mon-$mday";}

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
		print "  [-h] = this help screen\n\n";
		print "  [-q] = quiet\n";
		print "  [-t] = test\n";
		print "  [--debug] = debug output\n";
		print "  [--slow-seconds=X] = this allows you to override the default slow seconds of 60\n\n";
		print "\n";
		print "This script finds all SELECT queries running on the database and kills the SELECT queries running more than X seconds\n\n";

		exit;
		}
	else
		{
		if ($args =~ /-debug/i)
			{
			$DB=1;
			print "\n----- DEBUGGING -----\n\n";
			}
		if ($args =~ /-debugX/i)
			{
			$DBX=1;
			print "\n----- SUPER-DUPER DEBUGGING -----\n\n";
			}
		else {$DBX=0;}

		if ($args =~ /-q/i)
			{
			$q=1;
			}
		if ($args =~ /-t/i)
			{
			$T=1;
			$TEST=1;
			if ($q < 1) {print "\n----- TESTING -----\n\n";}
			}
		if ($args =~ /--slow-seconds=/i)
			{
			@data_in = split(/--slow-seconds=/,$args);
				$slow_seconds = $data_in[1];
				$slow_seconds =~ s/ .*//gi;
			if ($q < 1) {print "\n----- SLOW SECONDS OVERRIDE: $slow_seconds -----\n\n";}
			}
		else
			{$slow_seconds = '';}
		}
	}
else
	{
	print "no command line options set\n";
	$args = "";
	$i=0;
	$slow_seconds='60';
	}
### end parsing run-time options ###

if ($q < 1)
	{
	print "\n\n\n\n\n\n\n\n\n\n\n\n-- Vtiger_KILL_slow_queries.pl --\n\n";
	print "This program is designed to kill slow SELECT queries launched from the Vtiger system. \n\n";
	}

$i=0;
$US = '_';
$phone_list = '|';
$DASH='-';
$noacct=0;

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

# Get current crm ID, date and description from vtiger_account
$stmtB="SHOW FULL PROCESSLIST;";
	if($DB){print STDERR "\n|$stmtB|\n";}
$sthB = $dbhB->prepare($stmtB) or die "preparing: ",$dbhB->errstr;
$sthB->execute or die "executing: $stmtB ", $dbhB->errstr;
$sthBrows=$sthB->rows;
	if($DB){print STDERR "\n|$sthBrows records|\n";}
$i=0;
while ($sthBrows > $i)
	{
	@aryB = $sthB->fetchrow_array;
	$id[$i] =		$aryB[0];
	$user[$i] =		$aryB[1];
	$host[$i] =		$aryB[2];
	$db[$i] =		$aryB[3];
	$command[$i] =	$aryB[4];
	$time[$i] =		$aryB[5];
	$state[$i] =	$aryB[6];
	$info[$i] =		$aryB[7];
	if ($DBX > 0)
		{
		if ($command[$i] !~ /^Sleep$/i)
			{print "$aryB[0]|$aryB[1]|$aryB[2]|$aryB[3]|$aryB[4]|$aryB[5]|$aryB[6]|$aryB[7]\n";}
		}
	$i++;
	}
$sthB->finish();

$i=0;
while ($sthBrows > $i)
	{
	if ( ($db[$i] =~ /vtiger/i) && ($info[$i] =~ /^SELECT /i) && ($time[$i] >= $slow_seconds) )
		{
		if ($DB > 0) {print "Killing process $id[$i], it has been running for $time[$i] seconds\n";}
		if ($DBX > 0) {print "   STATE:\n$state[$i]\n";}
		if ($DBX > 0) {print "   INFO:\n$info[$i]\n";}
		if ($DBX > 0) {print "   QUERY:\n$command[$i]\n";}

		### kill process ###
		$stmtB = "KILL $id[$i];";
			if (!$T) {$affected_rows = $dbhB->do($stmtB); } #  or die  "Couldn't execute query: |$stmtB|\n";
			if($DB){print STDERR "\n|$affected_rows|$stmtB|\n";}

		### open the log file for writing ###
		open(out, ">>$VDHLOGfile")
				|| die "Can't open $VDHLOGfile: $!\n";

		print out "DATETIME: $NOW_TIME\n";
		print out "ID:       $id[$i]\n";
		print out "USER:     $user[$i]\n";
		print out "HOST:     $host[$i]\n";
		print out "DB:       $db[$i]\n";
		print out "COMMAND:  $command[$i]\n\n";
		print out "TIME:     $time[$i]\n";
		print out "STATE:    $state[$i]\n";
		print out "INFO:     $info[$i]\n";

		close(out);
		chmod 0777, "$VDHLOGfile";

		$Tattempt++;
		}


	$i++;
	}

$Falert  = "\n\nTOTALS:\n";
$Falert .= "Queries: $i\n";
$Falert .= "Kills:   $Tattempt\n";

if ($DB > 0) {print "$Falert";}

$dbhA->disconnect();

### calculate time to run script ###
$secY = time();
$secZ = ($secY - $secX);
$secZm = ($secZ /60);

if ($q < 1)
	{
	print "script execution time in seconds: $secZ     minutes: $secZm\n";
	}


exit;

