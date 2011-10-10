#!/usr/bin/perl
#
# VICIDIAL_fix_lead_statuses.pl version 2.0.5
#
# DESCRIPTION:
# resets the status in vicidial_list for leads marked in status NOUSE
# Very useful if you manually mess up the list statuses with a SQL query
#
# Copyright (C) 2008  Matt Florell <vicidial@gmail.com>    LICENSE: AGPLv2
#
# CHANGES
# 80308-0915 - first build
#

$secX = time();

($sec,$min,$hour,$mday,$mon,$year,$wday,$yday,$isdst) = localtime(time);
$year = ($year + 1900);
$mon++;
if ($hour < 10) {$hour = "0$hour";}
if ($min < 10) {$min = "0$min";}
if ($sec < 10) {$sec = "0$sec";}
if ($mon < 10) {$mon = "0$mon";}
if ($mday < 10) {$mday = "0$mday";}
$pulldate0 = "$year-$mon-$mday $hour:$min:$sec";
$inSD = $pulldate0;
$dsec = ( ( ($hour * 3600) + ($min * 60) ) + $sec );



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

$DB=1;

$liveupdate=0;

if (!$VDHLOGfile) {$VDHLOGfile = "$PATHlogs/dupleads.$year-$mon-$mday";}

print "\n\n\n\n\n\n\n\n\n\n\n\n-- VICIDIAL_fix_list_statuses.pl --\n\n";
print "This program is designed to scan all leads marked NOUSE and set them to their proper status according to the logs. \n\n";


$stmtA = "select lead_id,list_id from vicidial_list where status='NOUSE';";
if($DBX){print STDERR "\n|$stmtA|\n";}
$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
$sthArows=$sthA->rows;
$i=0;
$nonDUP='0';
while ( ($sthArows > $i) && ($nonDUP=='0') )
	{
	@aryA = $sthA->fetchrow_array;
	if ($aryA[0] > 1)
		{
		$lead_id[$i] =	"$aryA[0]";
		$list_id[$i] =	"$aryA[1]";
		}
	$i++;
	}
$sthA->finish();

$b=0;
foreach(@lead_id)
	{
	$Nstatus='';
	$Nepoch=0;
	$Cstatus='';
	$Cepoch=0;
	$stmtA = "select status,start_epoch from vicidial_log where lead_id='$lead_id[$b]' order by call_date desc LIMIT 1;";
		if($DBX){print STDERR "\n|$stmtA|\n";}
	$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
	$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
	$sthArows=$sthA->rows;
	if ($sthArows > 0)
		{
		@aryA = $sthA->fetchrow_array;
		$Nstatus = $aryA[0];
		$Nepoch = $aryA[1];
		$rec_count++;
		}
	$sthA->finish();

	$stmtA = "select status,start_epoch from vicidial_closer_log where lead_id='$lead_id[$b]' order by closecallid desc LIMIT 1;";
		if($DBX){print STDERR "\n|$stmtA|\n";}
	$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
	$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
	$sthArows=$sthA->rows;
	if ($sthArows > 0)
		{
		@aryA = $sthA->fetchrow_array;
		$Cstatus = $aryA[0];
		$Cepoch = $aryA[1];
		$rec_count++;
		}
	$sthA->finish();

	if ($Cepoch > $Nepoch) {$NEWstatus = $Cstatus;}
	else {$NEWstatus = $Nstatus;}

	$stmtA = "UPDATE vicidial_list set status='$NEWstatus' where lead_id='$lead_id[$b]';";
	$affected_rows = $dbhA->do($stmtA); #  or die  "Couldn't execute query:|$stmtA|\n";
		if($DB){print STDERR "|$b|$lead_id[$b]|$Nstatus|$Cstatus|$list_id[$b]||$stmtA|\n";}

	$b++;

	if ($b =~ /100$/i) {print STDERR "0     $b\r";}
	if ($b =~ /200$/i) {print STDERR "+     $b\r";}
	if ($b =~ /300$/i) {print STDERR "|     $b\r";}
	if ($b =~ /400$/i) {print STDERR "\\     $b\r";}
	if ($b =~ /500$/i) {print STDERR "-     $b\r";}
	if ($b =~ /600$/i) {print STDERR "/     $b\r";}
	if ($b =~ /700$/i) {print STDERR "|     $b\r";}
	if ($b =~ /800$/i) {print STDERR "+     $b\r";}
	if ($b =~ /900$/i) {print STDERR "0     $b\r";}
	if ($b =~ /000$/i) {print "|$b|$lead_id[$b]|$Nstatus|$Cstatus|$list_id[$b]|\n";}

	}
chop($DUP_updates);


$dbhA->disconnect();

### calculate time to run script ###
$secY = time();
$secZ = ($secY - $secX);
$secZm = ($secZ /60);

print "script execution time in seconds: $secZ     minutes: $secZm\n";

exit;

