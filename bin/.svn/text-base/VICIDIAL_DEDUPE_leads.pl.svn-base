#!/usr/bin/perl
#
# VICIDIAL_DEDUPE_leads.pl version 2.0.5
#
# DESCRIPTION:
# moves duplicate leads to another list_id
#
# Copyright (C) 2008  Matt Florell <vicidial@gmail.com>    LICENSE: AGPLv2
#
# /usr/share/astguiclient/VICIDIAL_DEDUPE_leads.pl --debugX --campaign-duplicate=CTF --ignore-list=999
#
# CHANGES
# 70521-1643 - first build
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

$liveupdate=0;

if (!$VDHLOGfile) {$VDHLOGfile = "$PATHlogs/dupleads.$year-$mon-$mday";}

print "\n\n\n\n\n\n\n\n\n\n\n\n-- VICIDIAL_DEDUPE_leads.pl --\n\n";
print "This program is designed to scan all leads for a campaign or entire system that are duplicates and move the newer lead into a different list_id. \n\n";

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
	print "  [-q] = quiet\n";
	print "  [-t] = test\n";
	print "  [--live-update] = runs the live UPDATE to move leads, default is report only\n";
	print "  [--system-duplicate] = checks for duplicates in entire database\n";
	print "  [--campaign-duplicate=1234] = duplicate check witin this campaign\n";
	print "  [--ignore-list=999] = ignores the list in duplicate check\n";
	print "  [--duplicate-list=998] = list_id that duplicate leads are moved to, default is 998\n";
	print "  [-h] = this help screen\n\n";
	print "\n";

	exit;
	}
	else
	{
		if ($args =~ /-debug/i)
		{
		$DB=1;
		print "\n-----DEBUGGING -----\n\n";
		}
		if ($args =~ /-debugX/i)
		{
		$DBX=1;
		print "\n----- SUPER-DUPER DEBUGGING -----\n\n";
		}
		if ($args =~ /-q/i)
		{
		$q=1;
		}
		if ($args =~ /-t/i)
		{
		$T=1;
		$TEST=1;
		print "\n----- TESTING -----\n\n";
		}
		if ($args =~ /-live-update/i)
		{
		$liveupdate=1;
		print "\n----- RUN LIVE UPDATE -----\n\n";
		}
		if ($args =~ /-system-duplicate/i)
		{
		$sysdup=1;
		print "\n----- ENTIRE SYSTEM DUPLICATE CHECK -----\n\n";
		}
		if ($args =~ /-campaign-duplicate=/i)
		{
		@data_in = split(/-campaign-duplicate=/,$args);
			$campdup = $data_in[1];
			$campdup =~ s/ .*//gi;
		print "\n----- CAMPAIGN DUPLICATE CHECK: $campdup -----\n\n";
		}
		else
			{$campdup = '';}

		if ($args =~ /--ignore-list=/i)
		{
		@data_in = split(/--ignore-list=/,$args);
			$ignorelist = $data_in[1];
			$ignorelist =~ s/ .*//gi;
		print "\n----- LISTID IGNORE: $ignorelist -----\n\n";
		}
		else
			{$ignorelist = '';}

		if ($args =~ /--duplicate-list=/i)
		{
		@data_in = split(/--duplicate-list=/,$args);
			$duplicatelist = $data_in[1];
			$duplicatelist =~ s/ .*//gi;
		print "\n----- LISTID DUPLICATE: $duplicatelist -----\n\n";
		}
		else
			{$duplicatelist = '998';}

	}
}
else
{
print "no command line options set\n";
$args = "";
$i=0;
$campdup = '';
$liveupdate=0;
$duplicatelist = '998';
}
### end parsing run-time options ###

$US = '_';
$phone_list = '|';
$MT[0]='';

if (!$VARDB_port) {$VARDB_port='3306';}

use DBI;	  

$dbhA = DBI->connect("DBI:mysql:$VARDB_database:$VARDB_server:$VARDB_port", "$VARDB_user", "$VARDB_pass")
 or die "Couldn't connect to database: " . DBI->errstr;

open(out, ">>$VDHLOGfile") || die "Can't open $VDHLOGfile: $!\n";
print out "\n\nSTARTED DUPLICATE CHECK: $pulldate0|$sysdup: $sysdup|campdup: $campdup|ignorelist: $ignorelist|\n";
close(out);

$campSQL='';
$listSQL='';
$where='WHERE';
$and='and';
if (length($campdup)>0)
	{
	$stmtA = "select list_id from vicidial_lists where campaign_id='$campdup';";
	if($DBX){print STDERR "\n|$stmtA|\n";}
	$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
	$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
	$sthArows=$sthA->rows;
	$rec_count=0;
	while ($sthArows > $rec_count)
		{
		@aryA = $sthA->fetchrow_array;
		$dup_lists .=	"'$aryA[0]',";
		$rec_count++;
		}
	$sthA->finish();
	chop($dup_lists);
	$campSQL="list_id IN($dup_lists)";
	if (length($ignorelist)>0)
		{
		$listSQL=" and list_id NOT IN('$ignorelist')";
		}

	}
else
	{
	if (length($ignorelist)>0)
		{
		$listSQL="list_id NOT IN('$ignorelist')";
		}
	else
		{
		$where='';
		$and='';
		}
	}

$stmtA = "select count(*) as tally, phone_number from vicidial_list $where $campSQL $listSQL group by phone_number order by tally desc;";
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
		$dup_count[$i] =	"$aryA[0]";
		$dup_list[$i] =		"$aryA[1]";
		}
	else
		{
		$nonDUP++;
		}
	$i++;
	}
$sthA->finish();

$b=0;
foreach(@dup_list)
	{
	$dup_limit = ($dup_count[$b] - 1);
	$stmtA = "select lead_id,list_id,entry_date from vicidial_list where phone_number='$dup_list[$b]' $and $campSQL $listSQL order by entry_date;";
		if($DBX){print STDERR "\n|$stmtA|\n";}
	$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
	$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
	$sthArows=$sthA->rows;
	$rec_count=0;
	open(out, ">>$VDHLOGfile") || die "Can't open $VDHLOGfile: $!\n";
	print out "$dup_list[$b]|$dup_count[$b]|";
	while ($sthArows > $rec_count)
		{
		@aryA = $sthA->fetchrow_array;
		if ($rec_count<1) {print out "$aryA[0]|$aryA[1]|$aryA[2]\n";}
		else
			{
			print out "     $aryA[0]|$aryA[1]|$aryA[2]\n";
			$DUP_updates .= "'$aryA[0]',";
			}
		$rec_count++;
		}
	close(out);
	$sthA->finish();

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
	if ($b =~ /000$/i) {print "|$b|$phone_number|\n";}

	}
chop($DUP_updates);

if ($liveupdate>0)
	{
	$stmtA = "UPDATE vicidial_list set list_id='$duplicatelist' where lead_id IN($DUP_updates);";
		if($DB){print STDERR "\n|$stmtA|\n";}
	$affected_rows = $dbhA->do($stmtA); #  or die  "Couldn't execute query:|$stmtA|\n";

		if($DBX){print STDERR "\nDUP UPDATES|$DUP_updates|\n   $affected_rows Leads moved to $duplicatelist\n";}
	}
else
	{
		if($DB){print STDERR "\n|$stmtA|\n";}
	}

$dbhA->disconnect();

### calculate time to run script ###
$secY = time();
$secZ = ($secY - $secX);
$secZm = ($secZ /60);

print "script execution time in seconds: $secZ     minutes: $secZm\n";

exit;

