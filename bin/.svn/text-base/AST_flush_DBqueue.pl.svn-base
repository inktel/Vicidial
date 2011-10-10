#!/usr/bin/perl
#
# AST_flush_DBqueue.pl    version 2.2.0
#
# DESCRIPTION:
# - clears out mysql records for this server for the ACQS vicidial_manager table
# - optimizes tables used frequently by VICIDIAL
#
# It is recommended that you run this program on the local Asterisk machine
#
# Copyright (C) 2009  Matt Florell <vicidial@gmail.com>    LICENSE: AGPLv2
#
# CHANGES
# 60717-1214 - changed to DBI by Marin Blu
# 60717-1536 - changed to use /etc/astguiclient.conf for configs
# 60910-0238 - removed park_log query
# 90628-2358 - Added vicidial_drop_rate_groups optimization
# 91206-2149 - Added vicidial_campaigns and vicidial_lists optimization
#

$secX = time();
	($sec,$min,$hour,$mday,$mon,$year,$wday,$yday,$isdst) = localtime(time);
	$year = ($year + 1900);
	$yy = $year; $yy =~ s/^..//gi;
	$mon++;
	if ($mon < 10) {$mon = "0$mon";}
	if ($mday < 10) {$mday = "0$mday";}
	if ($hour < 10) {$hour = "0$hour";}
	if ($min < 10) {$min = "0$min";}
	if ($sec < 10) {$sec = "0$sec";}
$SQLdate_NOW="$year-$mon-$mday $hour:$min:$sec";

($sec,$min,$hour,$mday,$mon,$year,$wday,$yday,$isdst) = localtime(time()-3600);
	$year = ($year + 1900);
	$yy = $year; $yy =~ s/^..//gi;
	$mon++;
	if ($mon < 10) {$mon = "0$mon";}
	if ($mday < 10) {$mday = "0$mday";}
	if ($hour < 10) {$hour = "0$hour";}
	if ($min < 10) {$min = "0$min";}
	if ($sec < 10) {$sec = "0$sec";}
$SQLdate_NEG_1hour="$year-$mon-$mday $hour:$min:$sec";

($sec,$min,$hour,$mday,$mon,$year,$wday,$yday,$isdst) = localtime(time()-1800);
	$year = ($year + 1900);
	$yy = $year; $yy =~ s/^..//gi;
	$mon++;
	if ($mon < 10) {$mon = "0$mon";}
	if ($mday < 10) {$mday = "0$mday";}
	if ($hour < 10) {$hour = "0$hour";}
	if ($min < 10) {$min = "0$min";}
	if ($sec < 10) {$sec = "0$sec";}
$SQLdate_NEG_halfhour="$year-$mon-$mday $hour:$min:$sec";

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
		print "allowed run time options:\n  [-q] = quiet\n  [-t] = test\n  [--debug] = debugging messages\n\n";
		}
	else
		{
		if ($args =~ /-q/i)
			{
			$q=1;   $Q=1;
			}
		if ($args =~ /--debug/i)
			{
			$DB=1;
			print "\n-----DEBUGGING -----\n\n";
			}
		if ($args =~ /-t|--test/i)
			{
			$T=1; $TEST=1;
			print "\n-----TESTING -----\n\n";
			}
		}
	}
else
	{
	print "no command line options set\n";
	}
### end parsing run-time options ###


if (!$Q) {print "TEST\n\n";}
if (!$Q) {print "NOW DATETIME:         $SQLdate_NOW\n";}
if (!$Q) {print "1 HOUR AGO DATETIME:  $SQLdate_NEG_1hour\n\n";}

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
	$i++;
	}

# Customized Variables
$server_ip = $VARserver_ip;		# Asterisk server IP

if (!$VARDB_port) {$VARDB_port='3306';}

use DBI;	  

$dbhA = DBI->connect("DBI:mysql:$VARDB_database:$VARDB_server:$VARDB_port", "$VARDB_user", "$VARDB_pass")
 or die "Couldn't connect to database: " . DBI->errstr;

### Grab Server values from the database
$stmtA = "SELECT vd_server_logs FROM servers where server_ip = '$VARserver_ip';";
$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
$sthArows=$sthA->rows;
$rec_count=0;
while ($sthArows > $rec_count)
	{
	@aryA = $sthA->fetchrow_array;
	$DBvd_server_logs =			"$aryA[0]";
	if ($DBvd_server_logs =~ /Y/)	{$SYSLOG = '1';}
	else {$SYSLOG = '0';}
	$rec_count++;
	}
$sthA->finish();

if ($SYSLOG) 
	{$flush_time = $SQLdate_NEG_1hour;}
else
	{$flush_time = $SQLdate_NEG_halfhour;}

$stmtA = "delete from vicidial_manager where server_ip='$server_ip' and entry_date < '$flush_time';";
if($DB){print STDERR "\n|$stmtA|\n";}
if (!$T) {	$affected_rows = $dbhA->do($stmtA);}
if (!$Q) {print " - vicidial_manager flush\n";}


$stmtA = "optimize table vicidial_manager;";
if($DB){print STDERR "\n|$stmtA|\n";}
if (!$T) 
	{
	$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
	$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
	$sthArows=$sthA->rows;
	@aryA = $sthA->fetchrow_array;
	if (!$Q) {print "|",$aryA[0],"|",$aryA[1],"|",$aryA[2],"|",$aryA[3],"|","\n";}
	$sthA->finish();
	}
if (!$Q) {print " - optimize vicidial_manager          \n";}


$stmtA = "optimize table vicidial_live_agents;";
if($DB){print STDERR "\n|$stmtA|\n";}
if (!$T) 
	{
	$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
	$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
	$sthArows=$sthA->rows;
	@aryA = $sthA->fetchrow_array;
	if (!$Q) {print "|",$aryA[0],"|",$aryA[1],"|",$aryA[2],"|",$aryA[3],"|","\n";}             
	$sthA->finish();
	}
if (!$Q) {print " - optimize vicidial_live_agents          \n";}


$stmtA = "optimize table vicidial_auto_calls;";
if($DB){print STDERR "\n|$stmtA|\n";}
if (!$T) 
	{
	$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
	$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
	$sthArows=$sthA->rows;
	@aryA = $sthA->fetchrow_array;
	if (!$Q) {print "|",$aryA[0],"|",$aryA[1],"|",$aryA[2],"|",$aryA[3],"|","\n";}
	$rec_countY++;
	$sthA->finish();
	}
if (!$Q) {print " - optimize vicidial_auto_calls          \n";}


$stmtA = "optimize table vicidial_hopper;";
if($DB){print STDERR "\n|$stmtA|\n";}
if (!$T) 
	{
	$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
	$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
	$sthArows=$sthA->rows;
	@aryA = $sthA->fetchrow_array;
	if (!$Q) {print "|",$aryA[0],"|",$aryA[1],"|",$aryA[2],"|",$aryA[3],"|","\n";}
	$sthA->finish();
	}
if (!$Q) {print " - optimize vicidial_hopper          \n";}


$stmtA = "optimize table vicidial_drop_rate_groups;";
if($DB){print STDERR "\n|$stmtA|\n";}
if (!$T) 
	{
	$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
	$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
	$sthArows=$sthA->rows;
	@aryA = $sthA->fetchrow_array;
	if (!$Q) {print "|",$aryA[0],"|",$aryA[1],"|",$aryA[2],"|",$aryA[3],"|","\n";}
	$sthA->finish();
	}
if (!$Q) {print " - optimize vicidial_drop_rate_groups          \n";}


$stmtA = "optimize table vicidial_campaigns;";
if($DB){print STDERR "\n|$stmtA|\n";}
if (!$T) 
	{
	$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
	$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
	$sthArows=$sthA->rows;
	@aryA = $sthA->fetchrow_array;
	if (!$Q) {print "|",$aryA[0],"|",$aryA[1],"|",$aryA[2],"|",$aryA[3],"|","\n";}
	$sthA->finish();
	}
if (!$Q) {print " - optimize vicidial_campaigns          \n";}


$stmtA = "optimize table vicidial_lists;";
if($DB){print STDERR "\n|$stmtA|\n";}
if (!$T) 
	{
	$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
	$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
	$sthArows=$sthA->rows;
	@aryA = $sthA->fetchrow_array;
	if (!$Q) {print "|",$aryA[0],"|",$aryA[1],"|",$aryA[2],"|",$aryA[3],"|","\n";}
	$sthA->finish();
	}
if (!$Q) {print " - optimize vicidial_lists          \n";}



$dbhA->disconnect();


exit;

