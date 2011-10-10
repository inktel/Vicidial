#!/usr/bin/perl
#
# ADMIN_timeclock_auto_logout.pl version 2.2.0   *DBI-version*
#
# DESCRIPTION:
# forces logout of all users still logged into the timeclock
#
# This script is launched by the ADMIN_keepalive_ALL.pl script with the '9' flag
# defined in astguiclient.conf
# 
# Copyright (C) 2009  Matt Florell <vicidial@gmail.com>    LICENSE: AGPLv2
#
# CHANGELOG
# 80526-0958 - First Build
# 80604-0733 - Fixed minor bug in update
# 90812-0103 - Formatting fixes
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
if ($hour < 10) {$hour = "0$hour";}
if ($min < 10) {$min = "0$min";}
if ($sec < 10) {$sec = "0$sec";}
$file_date = "$year-$mon-$mday";
$now_date = "$year-$mon-$mday $hour:$min:$sec";
$VDL_date = "$year-$mon-$mday 00:00:01";
$inactive_epoch = ($secT - 60);
$HHMM = "$hour$min";

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
		print "allowed run time options(must stay in this order):\n  [--debug] = debug\n  [--debugX] = super debug\n  [-t] = test\n  [--force-run] = force run even if already run for the day\n\n";
		exit;
		}
	else
		{
		if ($args =~ /--force-run/i)
			{
			$force_run=1;
			print "\n----- FORCE RUN -----\n\n";
			}
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

if (!$VDALOGfile) {$VDALOGfile = "$PATHlogs/timeclockautologout.$year-$mon-$mday";}
if (!$VARDB_port) {$VARDB_port='3306';}

use DBI;	  

$dbhA = DBI->connect("DBI:mysql:$VARDB_database:$VARDB_server:$VARDB_port", "$VARDB_user", "$VARDB_pass")
 or die "Couldn't connect to database: " . DBI->errstr;


### Grab Server values from the database
	$stmtA = "SELECT vd_server_logs,local_gmt FROM servers where server_ip = '$VARserver_ip';";
	$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
	$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
	$sthArows=$sthA->rows;
	$rec_count=0;
	while ($sthArows > $rec_count)
		{
		@aryA = $sthA->fetchrow_array;
		$DBvd_server_logs =			"$aryA[0]";
		$DBSERVER_GMT		=		"$aryA[1]";
		if ($DBvd_server_logs =~ /Y/)	{$SYSLOG = '1';}
		else {$SYSLOG = '0';}
		if (length($DBSERVER_GMT)>0)	{$SERVER_GMT = $DBSERVER_GMT;}
		$rec_count++;
		}
	$sthA->finish();

### Grab system_settings values from the database
	$stmtA = "SELECT timeclock_end_of_day,timeclock_last_reset_date FROM system_settings;";
	$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
	$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
	$sthArows=$sthA->rows;
	if ($sthArows > 0)
		{
		@aryA = $sthA->fetchrow_array;
		$timeclock_end_of_day =			"$aryA[0]";
		$timeclock_last_reset_date =	"$aryA[1]";
		}
	$sthA->finish();




### check to see if the script should run
$nowtest = ($HHMM+0);
$logtest = ($timeclock_end_of_day+0);
if ( ($force_run > 0) || ( ($nowtest >= $logtest) && ($timeclock_last_reset_date ne "$file_date") ) )
	{
	@user=@MT; 

	### grab users that are currently logged-in to the timeclock
	$stmtA = "SELECT user,user_group,event_epoch,status,ip_address,shift_id from vicidial_timeclock_status where status IN('START','LOGIN')";

	$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
	$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
	$sthArows=$sthA->rows;
	$rec_count=0;
	while ($sthArows > $rec_count)
		{
		@aryA = $sthA->fetchrow_array;
		$user[$rec_count] =				"$aryA[0]";
		$user_group[$rec_count] =		"$aryA[1]";
		$event_epoch[$rec_count] =		"$aryA[2]";
		$status[$rec_count] =			"$aryA[3]";
		$ip_address[$rec_count] =		"$aryA[4]";
		$shift_id[$rec_count] =			"$aryA[5]";

		$rec_count++;
		}
	$sthA->finish();
	if ($DB) {print "USERS TO LOG OUT OF THE TIMECLOCK:  $rec_count\n";}

	##### LOOP THROUGH EACH USER AND LOG THEM OUT OF THE TIMECLOCK #####
	$i=0;
	while($i < $rec_count)
		{	
		if ($DBX) {print "     USER: $user[$i]\n";}
		$event_string = "     USER: $user[$i]";
			&event_logger;

		$last_action_sec = ($now_date_epoch - $event_epoch[$i]);
		$stmtA = "INSERT INTO vicidial_timeclock_log set event='AUTOLOGOUT', user='$user[$i]', user_group='$user_group[$i]', event_epoch='$now_date_epoch', ip_address='$VARserver_ip', login_sec='$last_action_sec', event_date='$now_date';";
		$affected_rows = $dbhA->do($stmtA);
		$timeclock_id = $dbhA->{'mysql_insertid'};
		if ($DBX) {print "|$affected_rows|$stmtA|\n";}
		$event_string = "USER VTL INSERT|$affected_rows|$timeclock_id|$stmtA|";
			&event_logger;

		$stmtA = "UPDATE vicidial_timeclock_log set login_sec='$last_action_sec',tcid_link='$timeclock_id' where event='LOGIN' and user='$user[$i]' order by timeclock_id desc limit 1;";
		$affected_rows = $dbhA->do($stmtA);
		if ($DBX) {print "|$affected_rows|$stmtA|\n";}
		$event_string = "USER VTL UPDATE|$affected_rows|$stmtA|";
			&event_logger;

		$stmtA = "UPDATE vicidial_timeclock_status set status='AUTOLOGOUT', user_group='$user_group[$i]', event_epoch='$now_date_epoch', ip_address='$VARserver_ip' where user='$user[$i]';";
		$affected_rows = $dbhA->do($stmtA);
		if ($DBX) {print "|$affected_rows|$stmtA|\n";}
		$event_string = "USER VTS UPDATE|$affected_rows|$stmtA|";
			&event_logger;

		$stmtA = "INSERT INTO vicidial_timeclock_audit_log set timeclock_id='$timeclock_id', event='AUTOLOGOUT', user='$user[$i]', user_group='$user_group[$i]', event_epoch='$now_date_epoch', ip_address='$VARserver_ip', login_sec='$last_action_sec', event_date='$now_date';";
		$affected_rows = $dbhA->do($stmtA);
		if ($DBX) {print "|$affected_rows|$stmtA|\n";}
		$event_string = "USER VTAL INSERT|$affected_rows|$stmtA|";
			&event_logger;

		$stmtA = "UPDATE vicidial_timeclock_audit_log set login_sec='$last_action_sec',tcid_link='$timeclock_id' where event='LOGIN' and user='$user[$i]' order by timeclock_id desc limit 1;";
		$affected_rows = $dbhA->do($stmtA);
		if ($DBX) {print "|$affected_rows|$stmtA|\n";}
		$event_string = "USER VTAL UPDATE|$affected_rows|$stmtA|";
			&event_logger;

		$i++;
		}

	$stmtA = "UPDATE system_settings set timeclock_last_reset_date='$file_date';";
	$affected_rows = $dbhA->do($stmtA);
	if ($DBX) {print "|$affected_rows|$stmtA|\n";}
	$event_string = "SYSTEM SETTINGS UPDATE|$affected_rows|$stmtA|";
		&event_logger;

	}
else
	{
	print "cannot run: |$force_run|$HHMM($nowtest)|$timeclock_end_of_day($logtest)|$timeclock_last_reset_date|$file_date|\n";
	}

if($DB)
	{
	### calculate time to run script ###
	$secY = time();
	$secZ = ($secY - $secT);

	if (!$q) {print "DONE. Script execution time in seconds: $secZ\n";}
	}

$dbhA->disconnect();

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

