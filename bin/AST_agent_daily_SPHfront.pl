#!/usr/bin/perl

# AST_agent_daily_SPH.pl
#
# This script is designed to gather stats for all agent activity over the course
# of a week and print it in an ASCI text file to be placed 
# on a web server for viewing.
#
# This script assumes a work day to be from 2AM to 2AM
#
# Place in the crontab and run every night at 02:00AM for the previous day's stats
# 0 2 * * * /home/cron/AST_agent_daily_SPH.pl
# 
# Copyright (C) 2008  Matt Florell <vicidial@gmail.com>    LICENSE: AGPLv2
#
# CHANGES
# 80114-1221 - First version
# 80116-0941 - only print agents with week and month hours of 0 to PIPE file
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

use Time::Local;

### find epoch of 2AM today
$TWOAMsec = ( ($secX - ($sec + ($min * 60) + ($hour * 3600) ) ) + 7200);
### find epoch of 2AM yesterday
$TWOAMsecY = ($TWOAMsec - 86400);

($Tsec,$Tmin,$Thour,$Tmday,$Tmon,$Tyear,$Twday,$Tyday,$Tisdst) = localtime($TWOAMsecY);
$Tyear = ($Tyear + 1900);
$Tmon++;
if ($Tmon < 10) {$Tmon = "0$Tmon";}
if ($Tmday < 10) {$Tmday = "0$Tmday";}
if ($Thour < 10) {$Thour = "0$Thour";}
if ($Tmin < 10) {$Tmin = "0$Tmin";}
if ($Tsec < 10) {$Tsec = "0$Tsec";}
	$begindate = "$Tyear-$Tmon-$Tmday $Thour:$Tmin:$Tsec";

### find epoch of 2AM the first day of the month
$BMONTHsec = ($TWOAMsecY - (86400 * ($Tmday - 1) ) );
### find epoch of 2AM the first day of next month
if ($Tmon>11) {$Tmon=0; $Tyear++;}
$EMONTHsec = timelocal($Tsec,$Tmin,$Thour,1,$Tmon,$Tyear);

	if ($Twday==0) {$day='Sunday   '; $SETprevday = 6;}
	if ($Twday==1) {$day='Monday   '; $SETprevday = 0;}
	if ($Twday==2) {$day='Tuesday  '; $SETprevday = 1;}
	if ($Twday==3) {$day='Wednesday'; $SETprevday = 2;}
	if ($Twday==4) {$day='Thursday '; $SETprevday = 3;}
	if ($Twday==5) {$day='Friday   '; $SETprevday = 4;}
	if ($Twday==6) {$day='Saturday '; $SETprevday = 5;}

($TMsec,$TMmin,$TMhour,$TMmday,$TMmon,$TMyear,$TMwday,$TMyday,$TMisdst) = localtime($BMONTHsec);
$TMyear = ($TMyear + 1900);
$TMmon++;
if ($TMmon < 10) {$TMmon = "0$TMmon";}
if ($TMmday < 10) {$TMmday = "0$TMmday";}
if ($TMhour < 10) {$TMhour = "0$TMhour";}
if ($TMmin < 10) {$TMmin = "0$TMmin";}
if ($TMsec < 10) {$TMsec = "0$TMsec";}
	$beginmonth = "$TMyear-$TMmon-$TMmday $TMhour:$TMmin:$TMsec";

($TMsec,$TMmin,$TMhour,$TMmday,$TMmon,$TMyear,$TMwday,$TMyday,$TMisdst) = localtime($EMONTHsec);
$TMyear = ($TMyear + 1900);
$TMmon++;
if ($TMmon < 10) {$TMmon = "0$TMmon";}
if ($TMmday < 10) {$TMmday = "0$TMmday";}
if ($TMhour < 10) {$TMhour = "0$TMhour";}
if ($TMmin < 10) {$TMmin = "0$TMmin";}
if ($TMsec < 10) {$TMsec = "0$TMsec";}
	$endmonth = "$TMyear-$TMmon-$TMmday $TMhour:$TMmin:$TMsec";

#print "|$ABIfiledate|$begindate|$beginmonth|$endmonth|\n";
#exit;

print "\n\n\n\n\n\n\n\n\n\n\n\n-- AST_agent_daily_SPH.pl --\n\n";
print "This program is designed to print sales-per-hour(SPH) to a file for agents' activity for the current week. \n\n";


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

$outfile = "AGENT_SPH_$Tyear-$Tmon-$Tmday$txt";
$Doutfile = "AGENT_SPH_PIPE_$Tyear-$Tmon-$Tmday$txt";

### open the X out file for writing ###
open(out, ">$PATHweb/vicidial/agent_reports/$outfile")
		|| die "Can't open $outfile: $!\n";
open(Dout, ">$PATHweb/vicidial/agent_reports/$Doutfile")
		|| die "Can't open $Doutfile: $!\n";

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
print Dout "DATE|AGENT|USER|MON HOURS|MON SPH|TUE HOURS|TUE SPH|WED HOURS|WED SPH|THU HOURS|THU SPH|FRI HOURS|FRI SPH|SAT HOURS|SAT SPH|SUN HOURS|SUN SPH|WEEK HOURS|WEEK SPH|MONTH HOURS|MONTH SPH|GROUP|\n";
$outline = "REPORT: SPH $Tyear-$Tmon-$Tmday\n\n";
$outline.= "                               MONDAY      TUESDAY     WEDNESDAY   THURSDAY    FRIDAY      SATURDAY    SUNDAY       WEEK         MONTH\n";
$outline.= "AGENT                USER       HRS  SPH    HRS  SPH    HRS  SPH    HRS  SPH    HRS  SPH    HRS  SPH    HRS  SPH     HRS  SPH     HRS  SPH  GROUP   \n";
print "$outline";
print out "$outline";

### FIND ALL ACTIVE USERS IN THE SYSTEM IN('1090','1102','1245')='1090'
$stmtA = "select full_name,user,user_group from vicidial_users order by user_group,full_name limit 1000;";
$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
$sthArows=$sthA->rows;
$rec_count=0;
while ($sthArows > $rec_count)
	{
	@aryA = $sthA->fetchrow_array;

	$Dname[$rec_count] =	$aryA[0];
	$Duser[$rec_count] =	$aryA[1];
	$Dgroup[$rec_count] = 	$aryA[2];
	$name[$rec_count] = 	sprintf("%-20s", $aryA[0]); while(length($name[$rec_count])>20) {chop($name[$rec_count]);}
	$user[$rec_count] = 	sprintf("%-8s", $aryA[1]);
	$group[$rec_count] = 	sprintf("%-8s", $aryA[2]);

	$rec_count++;
	}
$sthA->finish();


$Gsph[0]='   0'; $Gsph[1]='   0'; $Gsph[2]='   0'; $Gsph[3]='   0'; $Gsph[4]='   0'; $Gsph[5]='   0'; $Gsph[6]='   0'; 
$Gsales[0]=0; $Gsales[1]=0; $Gsales[2]=0; $Gsales[3]=0; $Gsales[4]=0; $Gsales[5]=0; $Gsales[6]=0; 
$Ghours[0]='  0.0'; $Ghours[1]='  0.0'; $Ghours[2]='  0.0'; $Ghours[3]='  0.0'; $Ghours[4]='  0.0'; $Ghours[5]='  0.0'; $Ghours[6]='  0.0';
$GDsph[0]=0; $GDsph[1]=0; $GDsph[2]=0; $GDsph[3]=0; $GDsph[4]=0; $GDsph[5]=0; $GDsph[6]=0; 
$GDsales[0]=0; $GDsales[1]=0; $GDsales[2]=0; $GDsales[3]=0; $GDsales[4]=0; $GDsales[5]=0; $GDsales[6]=0; 
$GDhours[0]=0; $GDhours[1]=0; $GDhours[2]=0; $GDhours[3]=0; $GDhours[4]=0; $GDhours[5]=0; $GDhours[6]=0;
$GWEEKsph=0; $GWEEKhours=0; $GWEEKsales=0;
$GMONTHsph=0; $GMONTHhours=0; $GMONTHsales=0;

$TOTsph[0]='   0'; $TOTsph[1]='   0'; $TOTsph[2]='   0'; $TOTsph[3]='   0'; $TOTsph[4]='   0'; $TOTsph[5]='   0'; $TOTsph[6]='   0'; 
$TOTsales[0]=0; $TOTsales[1]=0; $TOTsales[2]=0; $TOTsales[3]=0; $TOTsales[4]=0; $TOTsales[5]=0; $TOTsales[6]=0; 
$TOThours[0]='  0.0'; $TOThours[1]='  0.0'; $TOThours[2]='  0.0'; $TOThours[3]='  0.0'; $TOThours[4]='  0.0'; $TOThours[5]='  0.0'; $TOThours[6]='  0.0';
$TOTDsph[0]=0; $TOTDsph[1]=0; $TOTDsph[2]=0; $TOTDsph[3]=0; $TOTDsph[4]=0; $TOTDsph[5]=0; $TOTDsph[6]=0; 
$TOTDsales[0]=0; $TOTDsales[1]=0; $TOTDsales[2]=0; $TOTDsales[3]=0; $TOTDsales[4]=0; $TOTDsales[5]=0; $TOTDsales[6]=0; 
$TOTDhours[0]=0; $TOTDhours[1]=0; $TOTDhours[2]=0; $TOTDhours[3]=0; $TOTDhours[4]=0; $TOTDhours[5]=0; $TOTDhours[6]=0;
$TOTWEEKsph=0; $TOTWEEKhours=0; $TOTWEEKsales=0;
$TOTMONTHsph=0; $TOTMONTHhours=0; $TOTMONTHsales=0;


### GO THROUGH EACH USER ACCOUNT AND FIND OUT THE HOURS AND SPH
$i=0;
foreach (@Duser)
	{
	$j=0;
	$sph[0]='   0'; $sph[1]='   0'; $sph[2]='   0'; $sph[3]='   0'; $sph[4]='   0'; $sph[5]='   0'; $sph[6]='   0';
	$sales[0]=0; $sales[1]=0; $sales[2]=0; $sales[3]=0; $sales[4]=0; $sales[5]=0; $sales[6]=0;
	$hours[0]='  0.0'; $hours[1]='  0.0'; $hours[2]='  0.0'; $hours[3]='  0.0'; $hours[4]='  0.0'; $hours[5]='  0.0'; $hours[6]='  0.0';
	$Dsph[0]=0; $Dsph[1]=0; $Dsph[2]=0; $Dsph[3]=0; $Dsph[4]=0; $Dsph[5]=0; $Dsph[6]=0;
	$Dsales[0]=0; $Dsales[1]=0; $Dsales[2]=0; $Dsales[3]=0; $Dsales[4]=0; $Dsales[5]=0; $Dsales[6]=0;
	$Dhours[0]=0; $Dhours[1]=0; $Dhours[2]=0; $Dhours[3]=0; $Dhours[4]=0; $Dhours[5]=0; $Dhours[6]=0;
	$WEEKsph=0; $WEEKhours=0; $WEEKsales=0;
	$MONTHsph=0; $MONTHhours=0; $MONTHsales=0;

	$UDtarget[0] = ($TWOAMsec - (86400 * 0) ); # X-0 days in the past
	$UDtarget[1] = ($TWOAMsec - (86400 * 1) ); # X-1 days in the past
	$UDtarget[2] = ($TWOAMsec - (86400 * 2) ); # X-2 days in the past
	$UDtarget[3] = ($TWOAMsec - (86400 * 3) ); # X-3 days in the past
	$UDtarget[4] = ($TWOAMsec - (86400 * 4) ); # X-4 days in the past
	$UDtarget[5] = ($TWOAMsec - (86400 * 5) ); # X-5 days in the past
	$UDtarget[6] = ($TWOAMsec - (86400 * 6) ); # X-6 days in the past
	$UDtarget[7] = ($TWOAMsec - (86400 * 7) ); # X-7 days in the past

	$prevday = $SETprevday;

	### GO THROUGH EACH DAY THIS WEEK UP TO TODAY
	while ($prevday >= 0)
		{
		($Tsec,$Tmin,$Thour,$Tmday,$Tmon,$Tyear,$Twday,$Tyday,$Tisdst) = localtime($UDtarget[$j]);
		$Tyear = ($Tyear + 1900);
		$Tmon++;
		if ($Tmon < 10) {$Tmon = "0$Tmon";}
		if ($Tmday < 10) {$Tmday = "0$Tmday";}
		if ($Thour < 10) {$Thour = "0$Thour";}
		if ($Tmin < 10) {$Tmin = "0$Tmin";}
		if ($Tsec < 10) {$Tsec = "0$Tsec";}
			$enddate = "$Tyear-$Tmon-$Tmday $Thour:$Tmin:$Tsec";

		$k = ($j + 1);
		($Tsec,$Tmin,$Thour,$Tmday,$Tmon,$Tyear,$Twday,$Tyday,$Tisdst) = localtime($UDtarget[$k]);
		$Tyear = ($Tyear + 1900);
		$Tmon++;
		if ($Tmon < 10) {$Tmon = "0$Tmon";}
		if ($Tmday < 10) {$Tmday = "0$Tmday";}
		if ($Thour < 10) {$Thour = "0$Thour";}
		if ($Tmin < 10) {$Tmin = "0$Tmin";}
		if ($Tsec < 10) {$Tsec = "0$Tsec";}
			$begindate = "$Tyear-$Tmon-$Tmday $Thour:$Tmin:$Tsec";

		$Dsales[$prevday]=0;
		$Dhours[$prevday]=0;

		$stmtA = "select sum(talk_sec+pause_sec+wait_sec+dispo_sec) from vicidial_agent_log where event_time <= '$enddate' and event_time >= '$begindate' and pause_sec<48800 and wait_sec<48800 and talk_sec<48800 and dispo_sec<48800 and user='$Duser[$i]' limit 1;";
		$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
		$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
		$sthArows=$sthA->rows;
		if ($sthArows > 0)
			{
			@aryA = $sthA->fetchrow_array;
			$Dhours[$prevday] = 	($aryA[0]/3600);
			}

		$stmtA = "select count(*) from vicidial_log where call_date <= '$enddate' and call_date >= '$begindate' and status IN('A1','A2','A3','A4','SALE','UPSELL') and user='$Duser[$i]';";
		$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
		$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
		$sthArows=$sthA->rows;
		if ($sthArows > 0)
			{
			@aryA = $sthA->fetchrow_array;
			$Dsales[$prevday] = 	$aryA[0];
			}

		$Dhours[$prevday] =	sprintf("%.2f", $Dhours[$prevday]);
		$hours[$prevday] = 	sprintf("%5s", $Dhours[$prevday]);
		$sales[$prevday] = 	sprintf("%5s", $Dsales[$prevday]);
		if ( ($Dsales[$prevday] > 0) && ($Dhours[$prevday] > 0) )
			{$Dsph[$prevday] =		($Dsales[$prevday]/$Dhours[$prevday]);}
		else
			{$Dsph[$prevday] =		0;}
		$Dsph[$prevday] =		sprintf("%.1f", $Dsph[$prevday]);
		$sph[$prevday] = 		sprintf("%4s", $Dsph[$prevday]);

		$GDsales[$prevday] = ($GDsales[$prevday] + $Dsales[$prevday]);
		$TOTDsales[$prevday] = ($TOTDsales[$prevday] + $Dsales[$prevday]);
		$GDhours[$prevday] = ($GDhours[$prevday] + $Dhours[$prevday]);
		$TOTDhours[$prevday] = ($TOTDhours[$prevday] + $Dhours[$prevday]);

		$WEEKhours = ($WEEKhours + $Dhours[$prevday]);
		$WEEKsales = ($WEEKsales + $Dsales[$prevday]);

		if ($DB) {print STDERR "$hours[$prevday] $sales[$prevday] $sph[$prevday] $prevday $begindate\n";}
		$prevday = ($prevday - 1);
		$j++;
		}

	$stmtA = "select sum(talk_sec+pause_sec+wait_sec+dispo_sec) from vicidial_agent_log where event_time <= '$endmonth' and event_time >= '$beginmonth' and pause_sec<48800 and wait_sec<48800 and talk_sec<48800 and dispo_sec<48800 and user='$Duser[$i]' limit 1;";
	$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
	$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
	$sthArows=$sthA->rows;
	if ($sthArows > 0)
		{
		@aryA = $sthA->fetchrow_array;
		$MONTHhours = 	($aryA[0]/3600);
		}

	$stmtA = "select count(*) from vicidial_log where call_date <= '$endmonth' and call_date >= '$beginmonth' and status IN('A1','A2','A3','A4','SALE','UPSELL') and user='$Duser[$i]';";
	$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
	$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
	$sthArows=$sthA->rows;
	if ($sthArows > 0)
		{
		@aryA = $sthA->fetchrow_array;
		$MONTHsales = 	$aryA[0];
		}

	if ( ($WEEKsales > 0) && ($WEEKhours > 0) )
		{$WEEKsph =	($WEEKsales/$WEEKhours);}
	else
		{$WEEKsph =	0;}

	if ( ($MONTHsales > 0) && ($MONTHhours > 0) )
		{$MONTHsph = ($MONTHsales/$MONTHhours);}
	else
		{$MONTHsph = 0;}

	$GMONTHsales = ($GMONTHsales + $MONTHsales);
	$TOTMONTHsales = ($TOTMONTHsales + $MONTHsales);
	$GMONTHhours = ($GMONTHhours + $MONTHhours);
	$TOTMONTHhours = ($TOTMONTHhours + $MONTHhours);

	print Dout "$shipdate|$Dname[$i]|$Duser[$i]|$Dhours[0]|$Dsph[0]|$Dhours[1]|$Dsph[1]|$Dhours[2]|$Dsph[2]|$Dhours[3]|$Dsph[3]|$Dhours[4]|$Dsph[4]|$Dhours[5]|$Dsph[5]|$Dhours[6]|$Dsph[6]|$WEEKhours|$WEEKsph|$MONTHhours|$MONTHsph|$Dgroup[$i]\n"; 

	### only print to ASCII fixed-length file if week-to-date and month-to-date hours > 0
	if ( ($WEEKhours > 0) || ($MONTHhours > 0) )
		{
		$WEEKhours =	sprintf("%.0f", $WEEKhours);
		$WEEKhours =	sprintf("%5s", $WEEKhours);
		$WEEKsph =		sprintf("%.1f", $WEEKsph);
		$WEEKsph =		sprintf("%4s", $WEEKsph);

		$MONTHhours =	sprintf("%.0f", $MONTHhours);
		$MONTHhours =	sprintf("%5s", $MONTHhours);
		$MONTHsph =		sprintf("%.1f", $MONTHsph);
		$MONTHsph =		sprintf("%4s", $MONTHsph);

		$outline = "$name[$i] $user[$i] $hours[0] $sph[0]  $hours[1] $sph[1]  $hours[2] $sph[2]  $hours[3] $sph[3]  $hours[4] $sph[4]  $hours[5] $sph[5]  $hours[6] $sph[6]   $WEEKhours $WEEKsph   $MONTHhours $MONTHsph  $group[$i]\n";
		print "$outline";
		print out "$outline";
		}
	else
		{
		print "SKIPPED: $name[$i] $user[$i] $WEEKhours $MONTHhours\n";
		}

	$Dprevgroup = $Dgroup[$i];
	$prevgroup = $group[$i];
	$i++;

	### BEGIN GROUP SUMMARY STATS
	if ($Dgroup[$i] !~ /^$Dprevgroup$/) 
		{
		$prevday = $SETprevday;

		### GO THROUGH EACH DAY THIS WEEK UP TO TODAY
		while ($prevday >= 0)
			{
			$GDhours[$prevday] =	sprintf("%.2f", $GDhours[$prevday]);
			$Ghours[$prevday] = 	sprintf("%5s", $GDhours[$prevday]); while(length($Ghours[$prevday])>5) {chop($Ghours[$prevday]);}
			$Gsales[$prevday] = 	sprintf("%5s", $GDsales[$prevday]);
			if ( ($GDsales[$prevday] > 0) && ($GDhours[$prevday] > 0) )
				{$GDsph[$prevday] =		($GDsales[$prevday]/$GDhours[$prevday]);}
			else
				{$GDsph[$prevday] =		0;}
			$GDsph[$prevday] =		sprintf("%.1f", $GDsph[$prevday]);
			$Gsph[$prevday] = 		sprintf("%4s", $GDsph[$prevday]);

			$GWEEKhours = ($GWEEKhours + $GDhours[$prevday]);
			$GWEEKsales = ($GWEEKsales + $GDsales[$prevday]);

			$prevday = ($prevday - 1);
			}

		if ( ($GWEEKsales > 0) && ($GWEEKhours > 0) )
			{$GWEEKsph =	($GWEEKsales/$GWEEKhours);}
		else
			{$GWEEKsph =	0;}

		if ( ($GMONTHsales > 0) && ($GMONTHhours > 0) )
			{$GMONTHsph = ($GMONTHsales/$GMONTHhours);}
		else
			{$GMONTHsph = 0;}

		print Dout "$shipdate|TOTAL $Dprevgroup||$GDhours[0]|$GDsph[0]|$GDhours[1]|$GDsph[1]|$GDhours[2]|$GDsph[2]|$GDhours[3]|$GDsph[3]|$GDhours[4]|$GDsph[4]|$GDhours[5]|$GDsph[5]|$GDhours[6]|$GDsph[6]|$GWEEKhours|$GWEEKsph|$GMONTHhours|$GMONTHsph|\n"; 

			$GWEEKhours =	sprintf("%.0f", $GWEEKhours);
			$GWEEKhours =	sprintf("%5s", $GWEEKhours); while(length($GWEEKhours)>5) {chop($GWEEKhours);}
			$GWEEKsph =		sprintf("%.1f", $GWEEKsph);
			$GWEEKsph =		sprintf("%4s", $GWEEKsph);

			$GMONTHhours =	sprintf("%.0f", $GMONTHhours);
			$GMONTHhours =	sprintf("%5s", $GMONTHhours); while(length($GMONTHhours)>5) {chop($GMONTHhours);}
			$GMONTHsph =		sprintf("%.1f", $GMONTHsph);
			$GMONTHsph =		sprintf("%4s", $GMONTHsph);

		$outline = "   GROUP TOTAL $prevgroup       $Ghours[0] $Gsph[0]  $Ghours[1] $Gsph[1]  $Ghours[2] $Gsph[2]  $Ghours[3] $Gsph[3]  $Ghours[4] $Gsph[4]  $Ghours[5] $Gsph[5]  $Ghours[6] $Gsph[6]   $GWEEKhours $GWEEKsph   $GMONTHhours $GMONTHsph\n\n";
		print "$outline";
		print out "$outline";

		$Gsph[0]='   0'; $Gsph[1]='   0'; $Gsph[2]='   0'; $Gsph[3]='   0'; $Gsph[4]='   0'; $Gsph[5]='   0'; $Gsph[6]='   0';
		$Gsales[0]=0; $Gsales[1]=0; $Gsales[2]=0; $Gsales[3]=0; $Gsales[4]=0; $Gsales[5]=0; $Gsales[6]=0;
		$Ghours[0]='  0.0'; $Ghours[1]='  0.0'; $Ghours[2]='  0.0'; $Ghours[3]='  0.0'; $Ghours[4]='  0.0'; $Ghours[5]='  0.0'; $Ghours[6]='  0.0';
		$GDsph[0]=0; $GDsph[1]=0; $GDsph[2]=0; $GDsph[3]=0; $GDsph[4]=0; $GDsph[5]=0; $GDsph[6]=0;
		$GDsales[0]=0; $GDsales[1]=0; $GDsales[2]=0; $GDsales[3]=0; $GDsales[4]=0; $GDsales[5]=0; $GDsales[6]=0;
		$GDhours[0]=0; $GDhours[1]=0; $GDhours[2]=0; $GDhours[3]=0; $GDhours[4]=0; $GDhours[5]=0; $GDhours[6]=0;
		$GWEEKsph=0; $GWEEKhours=0; $GWEEKsales=0;
		$GMONTHsph=0; $GMONTHhours=0; $GMONTHsales=0;
		}
	}
	### END AGENT STATS


### BEGIN TOTAL STATS


$prevday = $SETprevday;

### GO THROUGH EACH DAY THIS WEEK UP TO TODAY
while ($prevday >= 0)
	{
	$TOTDhours[$prevday] =	sprintf("%.0f", $TOTDhours[$prevday]);
	$TOThours[$prevday] = 	sprintf("%5s", $TOTDhours[$prevday]); while(length($TOThours[$prevday])>5) {chop($TOThours[$prevday]);}
	$TOTsales[$prevday] = 	sprintf("%5s", $TOTDsales[$prevday]);
	if ( ($TOTDsales[$prevday] > 0) && ($TOTDhours[$prevday] > 0) )
		{$TOTDsph[$prevday] =		($TOTDsales[$prevday]/$TOTDhours[$prevday]);}
	else
		{$TOTDsph[$prevday] =		0;}
	$TOTDsph[$prevday] =		sprintf("%.1f", $TOTDsph[$prevday]);
	$TOTsph[$prevday] = 		sprintf("%4s", $TOTDsph[$prevday]);

	$TOTWEEKhours = ($TOTWEEKhours + $TOTDhours[$prevday]);
	$TOTWEEKsales = ($TOTWEEKsales + $TOTDsales[$prevday]);

	$prevday = ($prevday - 1);
	}

if ( ($TOTWEEKsales > 0) && ($TOTWEEKhours > 0) )
	{$TOTWEEKsph =	($TOTWEEKsales/$TOTWEEKhours);}
else
	{$TOTWEEKsph =	0;}

if ( ($TOTMONTHsales > 0) && ($TOTMONTHhours > 0) )
	{$TOTMONTHsph = ($TOTMONTHsales/$TOTMONTHhours);}
else
	{$TOTMONTHsph = 0;}


print Dout "$shipdate|TOTAL||$TOTDhours[0]|$TOTDsph[0]|$TOTDhours[1]|$TOTDsph[1]|$TOTDhours[2]|$TOTDsph[2]|$TOTDhours[3]|$TOTDsph[3]|$TOTDhours[4]|$TOTDsph[4]|$TOTDhours[5]|$TOTDsph[5]|$TOTDhours[6]|$TOTDsph[6]|$TOTWEEKhours|$TOTWEEKsph|$TOTMONTHhours|$TOTMONTHsph|\n"; 

	$TOTWEEKhours =	sprintf("%.0f", $TOTWEEKhours);
	$TOTWEEKhours =	sprintf("%5s", $TOTWEEKhours); while(length($TOTWEEKhours)>5) {chop($TOTWEEKhours);}
	$TOTWEEKsph =		sprintf("%.1f", $TOTWEEKsph);
	$TOTWEEKsph =		sprintf("%4s", $TOTWEEKsph);

	$TOTMONTHhours =	sprintf("%.0f", $TOTMONTHhours);
	$TOTMONTHhours =	sprintf("%5s", $TOTMONTHhours); while(length($TOTMONTHhours)>5) {chop($TOTMONTHhours);}
	$TOTMONTHsph =		sprintf("%.1f", $TOTMONTHsph);
	$TOTMONTHsph =		sprintf("%4s", $TOTMONTHsph);

$outline = "     TOTAL                    $TOThours[0] $TOTsph[0]  $TOThours[1] $TOTsph[1]  $TOThours[2] $TOTsph[2]  $TOThours[3] $TOTsph[3]  $TOThours[4] $TOTsph[4]  $TOThours[5] $TOTsph[5]  $TOThours[6] $TOTsph[6]   $TOTWEEKhours $TOTWEEKsph   $TOTMONTHhours $TOTMONTHsph\n";
print "$outline";
print out "$outline";


exit;
























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
	$Dactive = $TIME_HMS;
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
	$Dtalk = $TIME_HMS;
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
	$Dpause = $TIME_HMS;
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
	$Dwait = $TIME_HMS;
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
	$Ddispo = $TIME_HMS;
	$dispo =	sprintf("%9s", $TIME_HMS);

	$stmtB = "select event_time,UNIX_TIMESTAMP(event_time) from $vicidial_agent_log where event_time <= '$shipdate 23:59:59' and event_time >= '$begindate 00:00:00' and user='$aryA[2]' order by event_time limit 1;";
	$sthB = $dbhB->prepare($stmtB) or die "preparing: ",$dbhB->errstr;
	$sthB->execute or die "executing: $stmtA ", $dbhB->errstr;
	@aryB = $sthB->fetchrow_array;
	$Dfirst_time = $aryB[0];
	$first_time = sprintf("%21s", $aryB[0]);
	$Dfirst_log = $aryB[1];
	$first_log = $aryB[1];

	$stmtB = "select event_time,UNIX_TIMESTAMP(event_time) from $vicidial_agent_log where event_time <= '$shipdate 23:59:59' and event_time >= '$begindate 00:00:00' and user='$aryA[2]' order by event_time desc limit 1;";
	$sthB = $dbhB->prepare($stmtB) or die "preparing: ",$dbhB->errstr;
	$sthB->execute or die "executing: $stmtA ", $dbhB->errstr;
	@aryB = $sthB->fetchrow_array;
	$Dlast_time = $aryB[0];
	$last_time = sprintf("%21s", $aryB[0]);
	$Dlast_log = $aryB[1];
	$last_log = $aryB[1];

	$TIME_S = ($last_log - $first_log);
	$TIME_H = int($TIME_S / 3600);
	$TIME_S = ($TIME_S - ($TIME_H * 3600));
	$TIME_M = int($TIME_S / 60);
	$TIME_S = ($TIME_S - ($TIME_M * 60));
	if ($TIME_S < 10) {$TIME_S = "0$TIME_S";}
	if ($TIME_M < 10) {$TIME_M = "0$TIME_M";}
	$TIME_HMS = "$TIME_H:$TIME_M:$TIME_S";
	$TIME_HMS = "N/A";
	$Dlogin_time = $TIME_HMS;
	$login_time =	sprintf("%9s", $TIME_HMS);


	
	print Dout "$shipdate|$Dname|$Duser|$Dcalls|$Dtalk|$Dpause|$Dwait|$Ddispo|$Dactive|$Dlogin_time|$Dfirst_time|$Dlast_time\n"; 
	print out "$name$user$calls$talk$pause$wait$dispo$active$login_time$first_time$last_time\n"; 
	print "$name$user$calls$talk$pause$wait$dispo$active$login_time$first_time$last_time\n"; 



close(Dout);
close(out);

### calculate time to run script ###
$secY = time();
$secZ = ($secY - $secX);
$secZm = ($secZ /60);

print "script execution time in seconds: $secZ     minutes: $secZm\n";

exit;





