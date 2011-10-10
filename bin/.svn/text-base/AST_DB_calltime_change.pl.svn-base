#!/usr/bin/perl
#
# AST_DB_calltime_change.pl   version 2.4
#
# DESCRIPTION:
# OPTIONAL!!!
# - changes default start and stop times for calltimes
#
# Copyright (C) 2011  Matt Florell <vicidial@gmail.com>    LICENSE: AGPLv2
#
# CHANGES
# 110421-0639 - first build
#

$calltime='';
$DB=0;
$Q=0;
$startSQL='';
$stopSQL='';

$secX = time();
$time = $secX;
	($sec,$min,$hour,$mday,$mon,$year,$wday,$yday,$isdst) = localtime(time);
$year = ($year + 1900);
$mon++;
if ($mon < 10) {$mon = "0$mon";}
if ($mday < 10) {$mday = "0$mday";}
if ($hour < 10) {$hour = "0$hour";}
if ($min < 10) {$min = "0$min";}
if ($sec < 10) {$sec = "0$sec";}
$timestamp = "$year-$mon-$mday $hour:$min:$sec";

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
		print "  [--calltime=XXX] = define the calltime entry to change\n";
		print "  [--default-start=XXXX] = the default start time to change, 4 digits\n";
		print "  [--default-stop=XXXX] = the default stop time to change, 4 digits\n";
		print "  [-t] = test\n";
		print "  [-q] = quiet\n";
		print "  [-debug] = verbose debug messages\n\n";
		}
	else
		{
		if ($args =~ /-debug/i)
			{
			$DB=1; # Debug flag, set to 0 for no debug messages
			}
		if ($args =~ /-t/i)
			{
			$TEST=1;
			$T=1;
			}
		if ($args =~ /-q/i)
			{
			$Q=1;
			}
		if ($args =~ /--calltime=/i)
			{
			@data_in = split(/--calltime=/,$args);
			$calltime = $data_in[1];
			$calltime =~ s/ .*//gi;
			if ($Q < 1)	{print "\n----- CALLTIME: $calltime -----\n\n";}
			}
		if ($args =~ /--default-start=/i)
			{
			@data_in = split(/--default-start=/,$args);
			$start = $data_in[1];
			$start =~ s/ .*//gi;
			$start =~ s/\D//gi;
			$startSQL = ",ct_default_start='$start'";
			if ($Q < 1)	{print "\n----- START: $start -----\n\n";}
			}
		if ($args =~ /--default-stop=/i)
			{
			@data_in = split(/--default-stop=/,$args);
			$stop = $data_in[1];
			$stop =~ s/ .*//gi;
			$stop =~ s/\D//gi;
			$stopSQL = ",ct_default_stop='$stop'";
			if ($Q < 1)	{print "\n----- STOP: $stop -----\n\n";}
			}
		}
	}
else
	{
	print "no command line options set\n";
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

##### change call time

$stmtA = "UPDATE vicidial_call_times set call_time_comments='auto-modified $timestamp' $startSQL $stopSQL where call_time_id='$calltime';";
if($DB){print STDERR "\n|$stmtA|\n";}
if ( (!$T) && (length($calltime) > 0) )
	{
	$affected_rows = $dbhA->do($stmtA);
	if($DB){print STDERR "\n|$affected_rows records changed|\n";}

	$SQL_log = "$stmtA|";
	$SQL_log =~ s/;|\\|\'|\"//gi;
	$stmtA="INSERT INTO vicidial_admin_log set event_date='$timestamp', user='VDAD', ip_address='1.1.1.1', event_section='CALLTIMES', event_type='MODIFY', record_id='$calltime', event_code='ADMIN AUTO MODIFY CALL TIME', event_sql=\"$SQL_log\", event_notes='$affected_rows updated records';";
	$Iaffected_rows = $dbhA->do($stmtA);
	}

$dbhA->disconnect();

exit;

