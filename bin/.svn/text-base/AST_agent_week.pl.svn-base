#!/usr/bin/perl
#
# AST_agent_week.pl    version 2.0.5
#
# This script is designed to gather stats for all agent activity over the course
# of a week(Sunday to Saturday) and print it in an ASCI text file to be placed 
# on a web server for viewing.
#
# Copyright (C) 2008  Matt Florell <vicidial@gmail.com>    LICENSE: AGPLv2
#
# CHANGES
# 60517-1100 - First version
# 60715-2325 - changed to use /etc/astguiclient.conf for configs
#

$txt = '.txt';
$US = '_';
$MT[0] = '';

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
	print "allowed run time options:\n  [-q] = quiet\n  [-t] = test\n\n";
	exit;
	}
	else
	{
		if ($args =~ /--debug/i)
		{
		$DB=1;
		print "\n-----DEBUG MODE-----\n\n";
		}
		if ($args =~ /-q/i)
		{
		$q=1;   $Q=1;
		}
		if ($args =~ /-t/i)
		{
		$T=1;   $TEST=1;
		print "\n-----TESTING-----\n\n";
		}
	}
}
else
{
print "no command line options set\n";
}
### end parsing run-time options ###


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

	$STARTtarget = ($secX - (86400 * $wday));
	($Ssec,$Smin,$Shour,$Smday,$Smon,$Syear,$Swday,$Syday,$Sisdst) = localtime($STARTtarget);
	$Syear = ($Syear + 1900);   $Smon++;
	if ($Smon < 10) {$Smon = "0$Smon";}
	if ($Smday < 10) {$Smday = "0$Smday";}
	$START_week = (($Syday/7)+1);  $START_week = sprintf("%2d", $START_week);
	$STARTtarget_date = "$Syear-$Smon-$Smday";

	$ENDtarget = ($STARTtarget + (86400 * 6));
	($Ssec,$Smin,$Shour,$Smday,$Smon,$Syear,$Swday,$Syday,$Sisdst) = localtime($ENDtarget);
	$Syear = ($Syear + 1900);   $Smon++;
	if ($Smon < 10) {$Smon = "0$Smon";}
	if ($Smday < 10) {$Smday = "0$Smday";}
	$ENDtarget_date = "$Syear-$Smon-$Smday";

	$PAST_STARTtarget = ($STARTtarget - (86400 * 7));
	($Ssec,$Smin,$Shour,$Smday,$Smon,$Syear,$Swday,$Syday,$Sisdst) = localtime($PAST_STARTtarget);
	$Syear = ($Syear + 1900);   $Smon++;
	if ($Smon < 10) {$Smon = "0$Smon";}
	if ($Smday < 10) {$Smday = "0$Smday";}
	$PAST_START_week = (($Syday/7)+1);  $PAST_START_week = sprintf("%2d", $PAST_START_week);
	$PAST_STARTtarget_date = "$Syear-$Smon-$Smday";

	$PAST_ENDtarget = ($ENDtarget - (86400 * 7));
	($Ssec,$Smin,$Shour,$Smday,$Smon,$Syear,$Swday,$Syday,$Sisdst) = localtime($PAST_ENDtarget);
	$Syear = ($Syear + 1900);   $Smon++;
	if ($Smon < 10) {$Smon = "0$Smon";}
	if ($Smday < 10) {$Smday = "0$Smday";}
	$PAST_ENDtarget_date = "$Syear-$Smon-$Smday";

	$PAST_date[0] = $PAST_STARTtarget_date;
		$PAST_day[0] = 'Sunday   ';
	$PAST_date[6] = $PAST_ENDtarget_date;
		$PAST_day[6] = 'Saturday ';

	$PAST_Mondaytarget = ($STARTtarget - (86400 * 6));
	($Ssec,$Smin,$Shour,$Smday,$Smon,$Syear,$Swday,$Syday,$Sisdst) = localtime($PAST_Mondaytarget);
	$Syear = ($Syear + 1900);   $Smon++;
	if ($Smon < 10) {$Smon = "0$Smon";}
	if ($Smday < 10) {$Smday = "0$Smday";}
	$PAST_date[1] = "$Syear-$Smon-$Smday";
		$PAST_day[1] = 'Monday   ';

	$PAST_Tuesdaytarget = ($STARTtarget - (86400 * 5));
	($Ssec,$Smin,$Shour,$Smday,$Smon,$Syear,$Swday,$Syday,$Sisdst) = localtime($PAST_Tuesdaytarget);
	$Syear = ($Syear + 1900);   $Smon++;
	if ($Smon < 10) {$Smon = "0$Smon";}
	if ($Smday < 10) {$Smday = "0$Smday";}
	$PAST_date[2] = "$Syear-$Smon-$Smday";
		$PAST_day[2] = 'Tuesday  ';

	$PAST_Wednesdaytarget = ($STARTtarget - (86400 * 4));
	($Ssec,$Smin,$Shour,$Smday,$Smon,$Syear,$Swday,$Syday,$Sisdst) = localtime($PAST_Wednesdaytarget);
	$Syear = ($Syear + 1900);   $Smon++;
	if ($Smon < 10) {$Smon = "0$Smon";}
	if ($Smday < 10) {$Smday = "0$Smday";}
	$PAST_date[3] = "$Syear-$Smon-$Smday";
		$PAST_day[3] = 'Wednesday';

	$PAST_Thursdaytarget = ($STARTtarget - (86400 * 3));
	($Ssec,$Smin,$Shour,$Smday,$Smon,$Syear,$Swday,$Syday,$Sisdst) = localtime($PAST_Thursdaytarget);
	$Syear = ($Syear + 1900);   $Smon++;
	if ($Smon < 10) {$Smon = "0$Smon";}
	if ($Smday < 10) {$Smday = "0$Smday";}
	$PAST_date[4] = "$Syear-$Smon-$Smday";
		$PAST_day[4] = 'Thursday ';

	$PAST_Fridaytarget = ($STARTtarget - (86400 * 2));
	($Ssec,$Smin,$Shour,$Smday,$Smon,$Syear,$Swday,$Syday,$Sisdst) = localtime($PAST_Fridaytarget);
	$Syear = ($Syear + 1900);   $Smon++;
	if ($Smon < 10) {$Smon = "0$Smon";}
	if ($Smday < 10) {$Smday = "0$Smday";}
	$PAST_date[5] = "$Syear-$Smon-$Smday";
		$PAST_day[5] = 'Friday   ';

print "\n\n\n\n\n\n\n\n\n\n\n\n-- AST_agent_week.pl --\n\n";
print "This program is designed to print stats to a file for agents' activity for the previous week. \n\n";

$TLoutfile  = '';
$TLoutfile .= "TODAY:          $shipdate\n";
$TLoutfile .= "THIS WEEK:  week $START_week    $STARTtarget_date - $ENDtarget_date\n";
$TLoutfile .= "LAST WEEK:  week $PAST_START_week    $PAST_STARTtarget_date - $PAST_ENDtarget_date\n\n";


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

$outfile = "AGENT_HOURS_$PAST_STARTtarget_date$US$PAST_ENDtarget_date$txt";

### open the X out file for writing ###
open(out, ">$PATHweb/vicidial/agent_reports/$outfile")
		|| die "Can't open $outfile: $!\n";

if (!$VARDB_port) {$VARDB_port='3306';}


use DBI;	  

$dbhA = DBI->connect("DBI:mysql:$VARDB_database:$VARDB_server:$VARDB_port", "$VARDB_user", "$VARDB_pass")
 or die "Couldn't connect to database: " . DBI->errstr;

$dbhB = DBI->connect("DBI:mysql:$VARDB_database:$VARDB_server:$VARDB_port", "$VARDB_user", "$VARDB_pass")
 or die "Couldn't connect to database: " . DBI->errstr;

#$vicidial_agent_log = 'vicidial_agent_log_archive';
$vicidial_agent_log = 'vicidial_agent_log';

###########################################################################
########### PAST WEEK STAT GATHERING LOOP #################################
###########################################################################
$h=0;
foreach(@PAST_date)
{
	print out "\n$PAST_day[$h] $PAST_date[$h]\n\n";
	print out "AGENT                USER     CALLS  TALK     PAUSE    WAIT     DISPO    ACTIVE   LOGTIME  FIRST                LAST                    \n";
	print "\n$PAST_day[$h] $PAST_date[$h]\n\n";
	print  "AGENT                USER     CALLS  TALK     PAUSE    WAIT     DISPO    ACTIVE   LOGTIME  FIRST                LAST                    \n";

	$stmtA = "select count(*) as calls, full_name,vicidial_users.user,sum(talk_sec),sum(pause_sec),sum(wait_sec),sum(dispo_sec) from vicidial_users,$vicidial_agent_log where event_time <= '$PAST_date[$h] 23:59:59' and event_time >= '$PAST_date[$h] 00:00:00' and vicidial_users.user=$vicidial_agent_log.user and pause_sec<48800 and wait_sec<48800 and talk_sec<48800 and dispo_sec<48800 group by vicidial_users.user order by full_name limit 10000;";
	$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
	$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
	$sthArows=$sthA->rows;
	$rec_count=0;
	while ($sthArows > $rec_count)
		{
		@aryA = $sthA->fetchrow_array;

		$calls = 	sprintf("%7s", $aryA[0]);
		$name = 	sprintf("%-20s", $aryA[1]); while(length($name)>20) {chop($name);}
		$user = 	sprintf("%-8s", $aryA[2]);

		### TOTAL ACTIVE TIME
		$active = 	($aryA[3] + $aryA[4] + $aryA[5] + $aryA[6]);
		$TIME_S = $active;
		$TIME_H = int($TIME_S / 3600);
		$TIME_S = ($TIME_S - ($TIME_H * 3600));
		$TIME_M = int($TIME_S / 60);
		$TIME_S = ($TIME_S - ($TIME_M * 60));
		if ($TIME_S < 10) {$TIME_S = "0$TIME_S";}
		if ($TIME_M < 10) {$TIME_M = "0$TIME_M";}
		$TIME_HMS = "$TIME_H:$TIME_M:$TIME_S";
		$active =	sprintf("%9s", $TIME_HMS);

		### TOTAL TALK TIME
		$TIME_S = $aryA[3];
		$TIME_H = int($TIME_S / 3600);
		$TIME_S = ($TIME_S - ($TIME_H * 3600));
		$TIME_M = int($TIME_S / 60);
		$TIME_S = ($TIME_S - ($TIME_M * 60));
		if ($TIME_S < 10) {$TIME_S = "0$TIME_S";}
		if ($TIME_M < 10) {$TIME_M = "0$TIME_M";}
		$TIME_HMS = "$TIME_H:$TIME_M:$TIME_S";
		$talk =	sprintf("%9s", $TIME_HMS);

		### TOTAL PAUSE TIME
		$TIME_S = $aryA[4];
		$TIME_H = int($TIME_S / 3600);
		$TIME_S = ($TIME_S - ($TIME_H * 3600));
		$TIME_M = int($TIME_S / 60);
		$TIME_S = ($TIME_S - ($TIME_M * 60));
		if ($TIME_S < 10) {$TIME_S = "0$TIME_S";}
		if ($TIME_M < 10) {$TIME_M = "0$TIME_M";}
		$TIME_HMS = "$TIME_H:$TIME_M:$TIME_S";
		$pause =	sprintf("%9s", $TIME_HMS);

		### TOTAL WAIT TIME
		$TIME_S = $aryA[5];
		$TIME_H = int($TIME_S / 3600);
		$TIME_S = ($TIME_S - ($TIME_H * 3600));
		$TIME_M = int($TIME_S / 60);
		$TIME_S = ($TIME_S - ($TIME_M * 60));
		if ($TIME_S < 10) {$TIME_S = "0$TIME_S";}
		if ($TIME_M < 10) {$TIME_M = "0$TIME_M";}
		$TIME_HMS = "$TIME_H:$TIME_M:$TIME_S";
		$wait =	sprintf("%9s", $TIME_HMS);

		### TOTAL DISPO TIME
		$TIME_S = $aryA[6];
		$TIME_H = int($TIME_S / 3600);
		$TIME_S = ($TIME_S - ($TIME_H * 3600));
		$TIME_M = int($TIME_S / 60);
		$TIME_S = ($TIME_S - ($TIME_M * 60));
		if ($TIME_S < 10) {$TIME_S = "0$TIME_S";}
		if ($TIME_M < 10) {$TIME_M = "0$TIME_M";}
		$TIME_HMS = "$TIME_H:$TIME_M:$TIME_S";
		$dispo =	sprintf("%9s", $TIME_HMS);

		$stmtB = "select event_time,UNIX_TIMESTAMP(event_time) from $vicidial_agent_log where event_time <= '$PAST_date[$h] 23:59:59' and event_time >= '$PAST_date[$h] 00:00:00' and user='$aryA[2]' order by event_time limit 1;";
		$sthB = $dbhB->prepare($stmtB) or die "preparing: ",$dbhB->errstr;
		$sthB->execute or die "executing: $stmtA ", $dbhB->errstr;
		@aryB = $sthB->fetchrow_array;
		$first_time = sprintf("%21s", $aryB[0]);
		$first_log = $aryB[1];

		$stmtB = "select event_time,UNIX_TIMESTAMP(event_time) from $vicidial_agent_log where event_time <= '$PAST_date[$h] 23:59:59' and event_time >= '$PAST_date[$h] 00:00:00' and user='$aryA[2]' order by event_time desc limit 1;";
		$sthB = $dbhB->prepare($stmtB) or die "preparing: ",$dbhB->errstr;
		$sthB->execute or die "executing: $stmtA ", $dbhB->errstr;
		@aryB = $sthB->fetchrow_array;
		$last_time = sprintf("%21s", $aryB[0]);
		$last_log = $aryB[1];

		$TIME_S = ($last_log - $first_log);
		$TIME_H = int($TIME_S / 3600);
		$TIME_S = ($TIME_S - ($TIME_H * 3600));
		$TIME_M = int($TIME_S / 60);
		$TIME_S = ($TIME_S - ($TIME_M * 60));
		if ($TIME_S < 10) {$TIME_S = "0$TIME_S";}
		if ($TIME_M < 10) {$TIME_M = "0$TIME_M";}
		$TIME_HMS = "$TIME_H:$TIME_M:$TIME_S";
		$login_time =	sprintf("%9s", $TIME_HMS);


		
		print out "$name$user$calls$talk$pause$wait$dispo$active$login_time$first_time$last_time\n"; 
		print "$name$user$calls$talk$pause$wait$dispo$active$login_time$first_time$last_time\n"; 


		$rec_count++;
		}
	$sthA->finish();

$h++;
}



close(out);

### calculate time to run script ###
$secY = time();
$secZ = ($secY - $secX);
$secZm = ($secZ /60);

print "script execution time in seconds: $secZ     minutes: $secZm\n";

exit;





