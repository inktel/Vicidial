#!/usr/bin/perl
#
# AST_SWAMPauto_dial.pl version 0.3
#
# DESCRIPTION:
# uses MySQL to place auto_dial calls on the VICIDIAL dialer system
#
# SUMMARY:
# This program was designed to test inbound phone system with a set number of
# calls being launched one per second for a set time period using incrementing
# CallerID numbers
#
# # The Gettysburg address is about 100 seconds, this is a free recording
# cd /var/lib/asterisk/sounds/
# wget http://www.eflo.net/files/gettysburgaddress.wav
#
# exten => 834562311,1,Answer
# exten => 834562311,2,Playback(gettysburgaddress)
# exten => 834562311,3,Hangup
#
# Copyright (C) 2009  Matt Florell <vicidial@gmail.com>    LICENSE: AGPLv2
#
# changes:
# 50708-1533 - First version
# 80217-0226 - Changed to DBI and added more options
# 90411-0708 - Added --concurrent-calls flag
#


# constants
	$CIDlist[0] = '9989998888';
$dialstring_800_prefix = '91';
$dialstring_number = '6102350796'; # number being dialed (inbound queue)
	$server_ips[0] = '192.168.75.21';
	$server_ips[1] = '192.168.75.23';
	$server_ips[2] = '192.168.75.22';
#	$server_ips[3] = '192.168.1.240';
#	$server_ips[0] = '192.168.1.106';
#	$server_ips[1] = '192.168.1.249';
#	$server_ips[2] = '192.168.1.239';
#	$server_ips[3] = '192.168.1.240';
#	$server_ips[0] = '10.10.11.11';
#	$server_ips[1] = '10.10.11.21';
#	$server_ips[2] = '10.10.11.22';
#	$server_ips[3] = '10.10.11.23';
#	$server_ips[4] = '10.10.11.24';
#	$server_ips[5] = '10.10.11.25';
#	$server_ips[6] = '10.10.11.13';
#	$server_ips[7] = '10.10.11.14';
#	$server_ips[8] = '10.10.11.15';
#	$server_ips[9] = '10.10.11.16';
#	$server_ips[10] = '10.10.11.18';
#	$server_ips[11] = '10.10.11.19';
$exten='834562311'; # where the test audio file is located
$context='default';
$US='_';
$loop_delay = '10000';
$it='0';
$total_loops='3600'; # 3600 seconds = 1 hour
#$total_loops='100'; # 3600 seconds = 1 hour
$dialstring = "$dialstring_800_prefix$dialstring_number";
$MT[0]='';


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
		print "  [-t] = test\n";
		print "  [-debug] = verbose debug messages\n";
		print "  [--delay=XXX] = delay of XXX seconds per loop, default 2.5 seconds\n";
		print "  [--concurrent-calls=XXX] = sets number of concurrent calls to maintain, overrides delay setting\n\n";
		exit;
		}
	else
		{
		if ($args =~ /--delay=/i)
			{
			@data_in = split(/--delay=/,$args);
			$loop_delay = $data_in[1];
			print "     LOOP DELAY OVERRIDE!!!!! = $loop_delay seconds\n\n";
			$loop_delay = ($loop_delay * 1000);
			}
		else
			{
			$loop_delay = '1000';
			}
		if ($args =~ /--concurrent-calls=/i)
			{
			@data_in = split(/--concurrent-calls=/,$args);
			$concurrent_calls = $data_in[1];
			$loop_delay = (100000 / $concurrent_calls);
			print "     CONCURRENT CALLS OVERRIDE!!!!! = $concurrent_calls - $loop_delay\n\n";
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
	print "no command line options set\n";
	$DB=1;
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

&get_time_now;

# Customized Variables
$server_ip = $VARserver_ip;		# Asterisk server IP

$SWAMPLOGfile = "$PATHlogs/SWAMP_LOG_$file_date$txt";


	$event_string='PROGRAM STARTED||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||';
	&event_logger;	# writes to the log and if debug flag is set prints to STDOUT

use lib './lib', '../lib';
use Time::HiRes ('gettimeofday','usleep','sleep');  # necessary to have perl sleep command of less than one second


if (!$VARDB_port) {$VARDB_port='3306';}

use DBI;	  

$dbhA = DBI->connect("DBI:mysql:$VARDB_database:$VARDB_server:$VARDB_port", "$VARDB_user", "$VARDB_pass")
 or die "Couldn't connect to database: " . DBI->errstr;


$event_string='LOGGED INTO MYSQL SERVER ON 1 CONNECTION|';
&event_logger;

$total=0;
$list_it=0;
$server_it=0;
$list_inc=0;
while($it < $total_loops)
	{
	&get_time_now;	# update time/date variables

	$CIDtemp = ($CIDlist[$list_it] + $list_inc);
	$SERVERtemp = $server_ips[$server_it];
	
	$k=0;
	while ($k < 1)
		{
		$stmtA = "INSERT INTO vicidial_manager values('','','$SQLdate','NEW','N','$SERVERtemp','','Originate','TESTCIDX$CIDdate$US$it','Channel: Local/$dialstring@$context','Context; $context','Exten: $exten','Priority: 1','Callerid: \"Inbound Test Call\" <$CIDtemp>','','','','','');";
		$affected_rows = $dbhA->do($stmtA);
	#	print "|$stmtA|\n";
		$k++;
		$total++;

		$event_string="CALL: $total TO: $dialstring   CID: $CIDtemp   it: $it   list_it: $list_it   list_inc: $list_inc  server: $SERVERtemp";
		print "$event_string\n";
		&event_logger;

		}
	### sleep before beginning the loop again
	usleep(1*$loop_delay*1000);

	$it++;
	$list_it++;
	if ($list_it > $#CIDlist) {$list_it=0;  $list_inc++;}
	$server_it++;
	if ($server_it > $#server_ips) {$server_it=0;}
	}

exit;













sub get_time_now	#get the current date and time and epoch for logging call lengths and datetimes
{
	$secX = time();
$secX = time();

($sec,$min,$hour,$mday,$mon,$year,$wday,$yday,$isdst) = localtime(time);
$year = ($year + 1900);
$mon++;
if ($mon < 10) {$mon = "0$mon";}
if ($mday < 10) {$mday = "0$mday";}
if ($hour < 10) {$Fhour = "0$hour";}
if ($min < 10) {$min = "0$min";}
if ($sec < 10) {$sec = "0$sec";}

$now_date_epoch = time();
$now_date = "$year-$mon-$mday $hour:$min:$sec";
$file_date = "$year-$mon-$mday_$hour$min$sec";
	$CIDdate = "$mon$mday$hour$min$sec";
	$tsSQLdate = "$year$mon$mday$hour$min$sec";
	$SQLdate = "$year-$mon-$mday $hour:$min:$sec";

}





sub event_logger
{

#if ($DB) {print "$now_date|$event_string|\n";}
	### open the log file for writing ###
	open(Lout, ">>$SWAMPLOGfile")
			|| die "Can't open $SWAMPLOGfile: $!\n";

	print Lout "$now_date|$event_string|\n";

	close(Lout);

$event_string='';
}
