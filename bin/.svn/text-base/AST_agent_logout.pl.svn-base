#!/usr/bin/perl
#
# AST_agent_logout.pl version 2.2.0
#
# DESCRIPTION:
# forces logout of agents in a specified campaign or all campaigns
#
# This script is meant to be used in the crontab at scheduled intervals
# 
# EXAMPLE CRONTAB ENTRIES:
#
#	### Force logout at different times
#
#	## 3:15PM and 10:15PM Monday-Thursday
#	15 15,22 * * 1,2,3,4 /usr/share/astguiclient/AST_agent_logout.pl --debugX
#
#	## 8:15PM on Friday
#	15 20 * * 5 /usr/share/astguiclient/AST_agent_logout.pl --debugX
#
#	## 4:15PM on Saturday
#	15 16 * * 6 /usr/share/astguiclient/AST_agent_logout.pl --debugX
#
#
# Copyright (C) 2008  Matt Florell <vicidial@gmail.com>    LICENSE: AGPLv2
#
# CHANGELOG
# 80112-0330 - First Build
# 91129-2138 - Replace SELECT STAR in SQL statement, fixed other formatting
#

# constants
$DB=0;  # Debug flag, set to 0 for no debug messages, On an active system this will generate lots of lines of output per minute
$US='__';
$MT[0]='';

$secT = time();
$now_date_epoch = $secT;
($sec,$min,$hour,$mday,$mon,$year,$wday,$yday,$isdst) = localtime(time);
$year = ($year + 1900);
$mon++;
if ($mon < 10) {$mon = "0$mon";}
if ($mday < 10) {$mday = "0$mday";}
if ($hour < 10) {$Fhour = "0$hour";}
if ($min < 10) {$min = "0$min";}
if ($sec < 10) {$sec = "0$sec";}
$file_date = "$year-$mon-$mday";
$now_date = "$year-$mon-$mday $hour:$min:$sec";
$VDL_date = "$year-$mon-$mday 00:00:01";
$inactive_epoch = ($secT - 60);

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
		print "allowed run time options(must stay in this order):\n  [--debug] = debug\n  [--debugX] = super debug\n  [-t] = test\n  [--campaign=XXX] = run for campaign XXX only\n\n";
		exit;
		}
	else
		{
		if ($args =~ /--campaign=/i)
			{
			#	print "\n|$ARGS|\n\n";
			@data_in = split(/--campaign=/,$args);
				$CLIcampaign = $data_in[1];
				$CLIcampaign =~ s/ .*$//gi;
			}
		else
			{$CLIcampaign = '';}
		if ($args =~ /--debug/i)
			{
			$DB=1;
			print "\n----- DEBUG -----\n\n";
			}
		if ($args =~ /--debugX/i)
			{
			$DBX=1;
			print "\n----- SUPER DEBUG -----\n\n";
			}
		if ($args =~ /-t/i)
			{
			$T=1;   $TEST=1;
			print "\n-----TESTING -----\n\n";
			}
		}
	}
else
	{
	print "no command line options set\n";
	}

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

if (!$VDALOGfile) {$VDALOGfile = "$PATHlogs/agentlogout.$year-$mon-$mday";}
if (!$VARDB_port) {$VARDB_port='3306';}

use DBI;	  

$dbhA = DBI->connect("DBI:mysql:$VARDB_database:$VARDB_server:$VARDB_port", "$VARDB_user", "$VARDB_pass")
 or die "Couldn't connect to database: " . DBI->errstr;


### Grab Server values from the database
$stmtA = "SELECT vd_server_logs,local_gmt FROM servers where server_ip = '$VARserver_ip';";
$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
$sthArows=$sthA->rows;
if ($sthArows > 0)
	{
	@aryA = $sthA->fetchrow_array;
	$DBvd_server_logs =			"$aryA[0]";
	$DBSERVER_GMT		=		"$aryA[1]";
	if ($DBvd_server_logs =~ /Y/)	{$SYSLOG = '1';}
	else {$SYSLOG = '0';}
	if (length($DBSERVER_GMT)>0)	{$SERVER_GMT = $DBSERVER_GMT;}
	}
$sthA->finish();



@user=@MT; 

if ($CLIcampaign)
	{
	$stmtA = "SELECT user,server_ip,status,lead_id,campaign_id,uniqueid,callerid,channel,last_call_time,UNIX_TIMESTAMP(last_update_time),last_call_finish,closer_campaigns,call_server_ip from vicidial_live_agents where campaign_id='$CLIcampaign'";
	}
else
	{
	$stmtA = "SELECT user,server_ip,status,lead_id,campaign_id,uniqueid,callerid,channel,last_call_time,UNIX_TIMESTAMP(last_update_time),last_call_finish,closer_campaigns,call_server_ip from vicidial_live_agents";
	}
$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
$sthArows=$sthA->rows;
$rec_count=0;
while ($sthArows > $rec_count)
	{
	@aryA = $sthA->fetchrow_array;
	$user[$rec_count] =				"$aryA[0]";
	$server_ip[$rec_count] =		"$aryA[1]";
	$status[$rec_count] =			"$aryA[2]";
	$lead_id[$rec_count] =			"$aryA[3]";
	$campaign_id[$rec_count] =		"$aryA[4]";
	$uniqueid[$rec_count] =			"$aryA[5]";
	$callerid[$rec_count] =			"$aryA[6]";
	$channel[$rec_count] =			"$aryA[7]";
	$last_call_time[$rec_count] =	"$aryA[8]";
	$last_update_time[$rec_count] =	"$aryA[9]";
	$last_call_finish[$rec_count] =	"$aryA[10]";
	$closer_campaigns[$rec_count] =	"$aryA[11]";
	$call_server_ip[$rec_count] =	"$aryA[12]";

	$rec_count++;
	}
$sthA->finish();
if ($DB) {print "AGENTS TO LOGOUT:  $rec_count\n";}

##### LOOP THROUGH EACH AGENT(USER) AND LOG THEM OUT #####
$i=0;
foreach(@user)
	{
	### attempt to gracefully update the timers in the logs before logging out the agent
	if ($last_update_time[$i] > $inactive_epoch)
		{
		$stmtA = "SELECT agent_log_id,user,server_ip,event_time,lead_id,campaign_id,pause_epoch,pause_sec,wait_epoch,wait_sec,talk_epoch,talk_sec,dispo_epoch,dispo_sec,status,user_group,comments,sub_status,dead_epoch,dead_sec from vicidial_agent_log where user='$user[$i]' and campaign_id='$campaign_id[$i]' order by agent_log_id desc LIMIT 1;";
		$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
		$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
		$sthArows=$sthA->rows;
		if ($sthArows > 0)
			{
			@aryA = $sthA->fetchrow_array;
			$agent_log_id[$i] =		"$aryA[0]";
			$pause_epoch[$i] =		"$aryA[6]";
			$pause_sec[$i] =		"$aryA[7]";
			$wait_epoch[$i] =		"$aryA[8]";
			$wait_sec[$i] =			"$aryA[9]";
			$talk_epoch[$i] =		"$aryA[10]";
			$talk_sec[$i] =			"$aryA[11]";
			$dispo_epoch[$i] =		"$aryA[12]";
			$dispo_sec[$i] =		"$aryA[13]";
			$user_group[$i] =		"$aryA[15]";
			}
		$sthA->finish();

		if ( ($wait_epoch[$i] < 1) || ( ($status[$i] =~ /PAUSE/) && ($dispo_epoch[$i] < 1) ) )
			{
			$pause_sec = ( ($now_date_epoch - $pause_epoch[$i]) + $pause_sec[$i]);
			$stmtA = "UPDATE vicidial_agent_log SET wait_epoch='$now_date_epoch', pause_sec='$pause_sec' where agent_log_id='$agent_log_id[$i]';";
			}
		else
			{
			if ($talk_epoch[$i] < 1)
				{
				$wait_sec = ( ($now_date_epoch - $wait_epoch[$i]) + $wait_sec[$i]);
				$stmtA = "UPDATE vicidial_agent_log SET talk_epoch='$now_date_epoch', wait_sec='$wait_sec' where agent_log_id='$agent_log_id[$i]';";
				}
			else
				{
				if ($dispo_epoch[$i] < 1)
					{
					$talk_sec = ($now_date_epoch - $talk_epoch[$i]);
					$stmtA = "UPDATE vicidial_agent_log SET dispo_epoch='$now_date_epoch', talk_sec='$talk_sec' where agent_log_id='$agent_log_id[$i]';";
					}
				else
					{
					if ($dispo_sec[$i] < 1)
						{
						$dispo_sec = ($now_date_epoch - $dispo_epoch[$i]);
						$stmtA = "UPDATE vicidial_agent_log SET dispo_sec='$dispo_sec' where agent_log_id='$agent_log_id[$i]';";
						}
					}
				}
			}
		$affected_rows = $dbhA->do($stmtA);
		if ($DBX) {print "UPDATING VAL RECORD:    $affected_rows  |$stmtA|\n";}
			$event_string = "AGENT UPDATE|$affected_rows|$stmtA|";
			&event_logger;
		}

	$stmtA = "DELETE from vicidial_live_agents where user='$user[$i]' and campaign_id='$campaign_id[$i]' order by live_agent_id LIMIT 1;";
	$affected_rows = $dbhA->do($stmtA);
	if ($DBX) {print "VLA record Deleted:     $affected_rows  $user[$1] $campaign_id[$i] $status[$i] $last_call_time[$i]\n";}
		$event_string = "AGENT LOGOUT|$user[$1]|$campaign_id[$i]|$status[$i]|$last_call_time[$i]";
		&event_logger;


	if (length($user_group[$i])<1)
		{
		$stmtA = "SELECT user_group FROM vicidial_users where user='$user[$i]';";
		$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
		$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
		$sthArows=$sthA->rows;
		if ($sthArows > 0)
			{
			@aryA = $sthA->fetchrow_array;
			$user_group[$i] =		"$aryA[0]";
			}
		$sthA->finish();
		}
	$stmtA = "INSERT INTO vicidial_user_log (user,event,campaign_id,event_date,event_epoch,user_group) values('$user[$i]','LOGOUT','$campaign_id[$i]','$now_date','$now_date_epoch','$user_group[$i]');";
	$affected_rows = $dbhA->do($stmtA);

	$i++;
	}


$dbhA->disconnect();

if($DB)
	{
	### calculate time to run script ###
	$secY = time();
	$secZ = ($secY - $secT);

	if (!$q) {print "DONE. Script execution time in seconds: $secZ\n";}
	}

exit;



sub event_logger
	{
	if ($SYSLOG)
		{
		### open the log file for writing ###
		open(Lout, ">>$VDALOGfile")
				|| die "Can't open $VDALOGfile: $!\n";
		print Lout "$now_date|$event_string|\n";
		close(Lout);
		}
	$event_string='';
	}

