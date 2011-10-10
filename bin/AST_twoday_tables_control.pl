#!/usr/bin/perl
#
# AST_twoday_tables_control.pl version 2.2.0
#
# DESCRIPTION:
# populates the twoday_ log tables during the day, and purges them at night
#
# crontab entry example:
# two-day processes
#0,5,10,15,20,25,30,35,40,45,50,55 0,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23 * * * /usr/share/astguiclient/AST_twoday_tables_control.pl --insert
#31 2 * * * /usr/share/astguiclient/AST_twoday_tables_control.pl --purge
#
# Copyright (C) 2009  Matt Florell <vicidial@gmail.com>    LICENSE: AGPLv2
#
# CHANGES
# 90729-0951 - First build
#

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
		print "allowed run time options:\n";
		print "  [--help] = this screen\n";
		print "  [-t] = test\n";
		print "  [-debug] = verbose debug messages\n";
		print "  [-q] = quiet\n";
		print "  [-insert] = run inserts, grab records in the past hour\n";
		print "  [-purge] = purge records more than 24 hours old\n";
		print "\n";
		exit;
		}
	else
		{
		if ($args =~ /-q/i)
			{
			$Q=1;
			}
		if ($args =~ /-insert/i)
			{
			$insert=1;
			}
		if ($args =~ /-purge/i)
			{
			$purge=1;
			}
		if ($args =~ /-debug/i)
			{
			$DB=1; # Debug flag, set to 0 for no debug messages, On an active system this will generate hundreds of lines of output per minute
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
	if ($Q < 1) {print "no command line options set\n";}
	}
### end parsing run-time options ###


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


$secX = time();
	($sec,$min,$hour,$mday,$mon,$year,$wday,$yday,$isdst) = localtime(time);
	$year = ($year + 1900);
	$mon++;
	if ($mon < 10) {$mon = "0$mon";}
	if ($mday < 10) {$mday = "0$mday";}
	if ($hour < 10) {$hour = "0$hour";}
	if ($min < 10) {$min = "0$min";}
	if ($sec < 10) {$sec = "0$sec";}
	$filedate = "$year$mon$mday-$hour$min$sec";
	$ABIfiledate = "$mon-$mday-$year$us$hour$min$sec";
	$shipdate = "$year-$mon-$mday";
	$datestamp = "$year/$mon/$mday $hour:$min";

use Time::Local;

$secX = time();

$HHtarget = ($secX - 7200);  # 2 hours
($Hsec,$Hmin,$Hhour,$Hmday,$Hmon,$Hyear,$Hwday,$Hyday,$Hisdst) = localtime($HHtarget);
$Hyear = ($Hyear + 1900);
$Hmon++;
if ($Hmon < 10) {$Hmon = "0$Hmon";}
if ($Hmday < 10) {$Hmday = "0$Hmday";}
if ($Hhour < 10) {$Hhour = "0$Hhour";}
if ($Hmin < 10) {$Hmin = "0$Hmin";}
if ($Hsec < 10) {$Hsec = "0$Hsec";}
$HHSQLdate = "$Hyear-$Hmon-$Hmday $Hhour:$Hmin:$Hsec";

$YDtarget = ($secX - 86400); # 24 hours
($Ysec,$Ymin,$Yhour,$Ymday,$Ymon,$Yyear,$Ywday,$Yyday,$Yisdst) = localtime($YDtarget);
$Yyear = ($Yyear + 1900);
$Ymon++;
if ($Ymon < 10) {$Ymon = "0$Ymon";}
if ($Ymday < 10) {$Ymday = "0$Ymday";}
if ($Yhour < 10) {$Yhour = "0$Yhour";}
if ($Ymin < 10) {$Ymin = "0$Ymin";}
if ($Ysec < 10) {$Ysec = "0$Ysec";}
$YDSQLdate = "$Yyear-$Ymon-$Ymday $Yhour:$Ymin:$Ysec";


################################ INSERT PROCESS
if ($insert > 0)
	{
	$stmtA = "INSERT IGNORE INTO twoday_call_log SELECT * from call_log where start_time > '$HHSQLdate' ON DUPLICATE KEY UPDATE end_time=VALUES(end_time),end_epoch=VALUES(end_epoch),length_in_sec=VALUES(length_in_sec),length_in_min=VALUES(length_in_min);";
	if (!$T) 
		{
		$affected_rows = $dbhA->do($stmtA);
		if($DB){print "\n|$affected_rows records changed|$stmtA|\n";}
		}

	$stmtA = "INSERT IGNORE INTO twoday_vicidial_log SELECT * from vicidial_log where call_date > '$HHSQLdate' ON DUPLICATE KEY UPDATE end_epoch=VALUES(end_epoch),length_in_sec=VALUES(length_in_sec),status=VALUES(status),user=VALUES(user),comments=VALUES(comments),user_group=VALUES(user_group),term_reason=VALUES(term_reason),alt_dial=VALUES(alt_dial);";
	if (!$T) 
		{
		$affected_rows = $dbhA->do($stmtA);
		if($DB){print "\n|$affected_rows records changed|$stmtA|\n";}
		}

	$stmtA = "INSERT IGNORE INTO twoday_vicidial_closer_log SELECT * from vicidial_closer_log where call_date > '$HHSQLdate' ON DUPLICATE KEY UPDATE list_id=VALUES(list_id),campaign_id=VALUES(campaign_id),end_epoch=VALUES(end_epoch),length_in_sec=VALUES(length_in_sec),status=VALUES(status),user=VALUES(user),comments=VALUES(comments),queue_seconds=VALUES(queue_seconds),user_group=VALUES(user_group),xfercallid=VALUES(xfercallid),term_reason=VALUES(term_reason),uniqueid=VALUES(uniqueid),agent_only=VALUES(agent_only);";
	if (!$T) 
		{
		$affected_rows = $dbhA->do($stmtA);
		if($DB){print "\n|$affected_rows records changed|$stmtA|\n";}
		}

	$stmtA = "INSERT IGNORE INTO twoday_recording_log SELECT * from recording_log where start_time > '$HHSQLdate' ON DUPLICATE KEY UPDATE end_time=VALUES(end_time),end_epoch=VALUES(end_epoch),length_in_sec=VALUES(length_in_sec),length_in_min=VALUES(length_in_min),filename=VALUES(filename),location=VALUES(location),lead_id=VALUES(lead_id),user=VALUES(user),vicidial_id=VALUES(vicidial_id);";
	if (!$T) 
		{
		$affected_rows = $dbhA->do($stmtA);
		if($DB){print "\n|$affected_rows records changed|$stmtA|\n";}
		}

	$stmtA = "INSERT IGNORE INTO twoday_vicidial_xfer_log SELECT * from vicidial_xfer_log where call_date > '$HHSQLdate' ON DUPLICATE KEY UPDATE list_id=VALUES(list_id),campaign_id=VALUES(campaign_id),call_date=VALUES(call_date),user=VALUES(user),closer=VALUES(closer);";
	if (!$T) 
		{
		$affected_rows = $dbhA->do($stmtA);
		if($DB){print "\n|$affected_rows records changed|$stmtA|\n";}
		}

	$stmtA = "INSERT IGNORE INTO twoday_vicidial_agent_log SELECT * from vicidial_agent_log where event_time > '$HHSQLdate' ON DUPLICATE KEY UPDATE lead_id=VALUES(lead_id),campaign_id=VALUES(campaign_id),pause_epoch=VALUES(pause_epoch),pause_sec=VALUES(pause_sec),wait_epoch=VALUES(wait_epoch),wait_sec=VALUES(wait_sec),talk_epoch=VALUES(talk_epoch),talk_sec=VALUES(talk_sec),dispo_epoch=VALUES(dispo_epoch),dispo_sec=VALUES(dispo_sec),status=VALUES(status),user_group=VALUES(user_group),comments=VALUES(comments),sub_status=VALUES(sub_status);";
	if (!$T) 
		{
		$affected_rows = $dbhA->do($stmtA);
		if($DB){print "\n|$affected_rows records changed|$stmtA|\n";}
		}
	}


################################ PURGE PROCESS
if ($purge > 0)
	{
	##### PURGE RECORDS OLDER THAN 24 HOURS
	$stmtA = "DELETE FROM twoday_call_log where start_time < '$YDSQLdate';";
	if (!$T) 
		{
		$affected_rows = $dbhA->do($stmtA);
		if($DB){print "\n|$affected_rows records changed|$stmtA|\n";}
		}

	$stmtA = "DELETE FROM twoday_vicidial_log where call_date < '$YDSQLdate';";
	if (!$T) 
		{
		$affected_rows = $dbhA->do($stmtA);
		if($DB){print "\n|$affected_rows records changed|$stmtA|\n";}
		}

	$stmtA = "DELETE FROM twoday_vicidial_closer_log where call_date < '$YDSQLdate';";
	if (!$T) 
		{
		$affected_rows = $dbhA->do($stmtA);
		if($DB){print "\n|$affected_rows records changed|$stmtA|\n";}
		}

	$stmtA = "DELETE FROM twoday_recording_log where start_time < '$YDSQLdate';";
	if (!$T) 
		{
		$affected_rows = $dbhA->do($stmtA);
		if($DB){print "\n|$affected_rows records changed|$stmtA|\n";}
		}

	$stmtA = "DELETE FROM twoday_vicidial_xfer_log where call_date < '$YDSQLdate';";
	if (!$T) 
		{
		$affected_rows = $dbhA->do($stmtA);
		if($DB){print "\n|$affected_rows records changed|$stmtA|\n";}
		}

	$stmtA = "DELETE FROM twoday_vicidial_agent_log where event_time < '$YDSQLdate';";
	if (!$T) 
		{
		$affected_rows = $dbhA->do($stmtA);
		if($DB){print "\n|$affected_rows records changed|$stmtA|\n";}
		}


	##### OPTIMIZE TABLES
	$stmtA = "OPTIMIZE TABLE twoday_call_log;";
	if (!$T) 
		{
		$affected_rows = $dbhA->do($stmtA);
		if($DB){print "\n|$affected_rows records changed|$stmtA|\n";}
		}

	$stmtA = "OPTIMIZE TABLE twoday_vicidial_log;";
	if (!$T) 
		{
		$affected_rows = $dbhA->do($stmtA);
		if($DB){print "\n|$affected_rows records changed|$stmtA|\n";}
		}

	$stmtA = "OPTIMIZE TABLE twoday_vicidial_closer_log;";
	if (!$T) 
		{
		$affected_rows = $dbhA->do($stmtA);
		if($DB){print "\n|$affected_rows records changed|$stmtA|\n";}
		}

	$stmtA = "OPTIMIZE TABLE twoday_recording_log;";
	if (!$T) 
		{
		$affected_rows = $dbhA->do($stmtA);
		if($DB){print "\n|$affected_rows records changed|$stmtA|\n";}
		}

	$stmtA = "OPTIMIZE TABLE twoday_vicidial_xfer_log;";
	if (!$T) 
		{
		$affected_rows = $dbhA->do($stmtA);
		if($DB){print "\n|$affected_rows records changed|$stmtA|\n";}
		}

	$stmtA = "OPTIMIZE TABLE twoday_vicidial_agent_log;";
	if (!$T) 
		{
		$affected_rows = $dbhA->do($stmtA);
		if($DB){print "\n|$affected_rows records changed|$stmtA|\n";}
		}

	}



$dbhA->disconnect();

if ($DB > 0)
	{
	$secY=time();
	$seconds = ($secY - $secX);
	print "Finished, $seconds seconds\n";
	}

exit;
