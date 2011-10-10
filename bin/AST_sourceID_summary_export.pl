#!/usr/bin/perl
#
# AST_sourceID_summary_export.pl               version: 2.2.0
#
# This script is designed to gather stats for all leads by source_id and
# post them to a directory
#
# Copyright (C) 2009  Matt Florell <vicidial@gmail.com>    LICENSE: AGPLv2
#
# CHANGES
# 70905-1409 - First version
# 71005-0054 - Altered script to use astguiclient.conf for settings
# 90620-0856 - Formatting fixes
#

$txt = '.txt';
$US = '_';
$MT[0] = '';

# Default FTP account variables
$VARREPORT_host = '10.0.0.4';
$VARREPORT_user = 'cron';
$VARREPORT_pass = 'test';
$VARREPORT_port = '21';
$VARREPORT_dir  = 'REPORTS';

# default CLI values
$sale_statuses = 'SALE-UPSELL';
$ni_statuses = 'NI';
$np_statuses = 'NP';

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
		print "  [--sale-statuses=XXX-XXY] = Statuses that are deemed to be \"Sales\". Default SALE\n";
		print "  [--NI-statuses=XXX-XXY] = Statuses that are deemed to be \"Not Interested\". Default NI\n";
		print "  [--NP-statuses=XXX-XXY] = Statuses that are deemed to be \"No Pitch\". Default NP\n";
		print "  [--ignore-lists=XXX-XXY] = lists that should be ignored in summary\n";
		print "  [--ftp-transfer] = Send results file by FTP to another server\n";
		print "  [-q] = quiet\n";
		print "  [-t] = test\n";
		print "  [--debug] = debugging messages\n";
		print "  [--debugX] = Super debugging messages\n";
		print "\n";

		exit;
		}
	else
		{
		if ($args =~ /--debug/i)
			{
			$DB=1;
			print "\n----- DEBUG MODE -----\n\n";
			}
		if ($args =~ /--debugX/i)
			{
			$DBX=1;
			print "\n----- SUPER DEBUG MODE -----\n\n";
			}
		if ($args =~ /-q/i)
			{
			$q=1;   $Q=1;
			}
		if ($args =~ /--sale-statuses=/i)
			{
			@data_in = split(/--sale-statuses=/,$args);
			$sale_statuses = $data_in[1];
			$sale_statuses =~ s/ .*$//gi;
			}
		if ($args =~ /--NI-statuses=/)
			{
			@data_in = split(/--NI-statuses=/,$args);
			$ni_statuses = $data_in[1];
			$ni_statuses =~ s/ .*$//gi;
			}
		if ($args =~ /--NP-statuses=/)
			{
			@data_in = split(/--NP-statuses=/,$args);
			$np_statuses = $data_in[1];
			$np_statuses =~ s/ .*$//gi;
			}
		if ($args =~ /--ignore-lists=/)
			{
			@data_in = split(/--ignore-lists=/,$args);
			$ignore_lists = $data_in[1];
			$ignore_lists =~ s/ .*$//gi;
			}
		if ($args =~ /-ftp-transfer/i)
			{
			if (!$Q)
				{print "\n----- FTP TRANSFER MODE -----\n\n";}
			$ftp_transfer=1;
			}
		if ($args =~ /--test/i)
			{
			$T=1;   $TEST=1;
			print "\n----- TESTING -----\n\n";
			}
		}
	}
else
	{
	print "no command line options set, using defaults.\n";
	}
### end parsing run-time options ###


$secX = time();
($sec,$min,$hour,$mday,$mon,$year,$wday,$yday,$isdst) = localtime(time);
$year = ($year + 1900);
$mon++;

#	# rerun override - Use this to override the day manually
#	$year='2007';
#	$mon='5';
#	$mday='18';

if ($mon < 10) {$mon = "0$mon";}
if ($mday < 10) {$mday = "0$mday";}
if ($hour < 10) {$hour = "0$hour";}
if ($min < 10) {$min = "0$min";}
if ($sec < 10) {$sec = "0$sec";}
$timestamp = "$year-$mon-$mday $hour:$min:$sec";
$filedate = "$year$mon$mday";
$ABIfiledate = "$mon-$mday-$year$us$hour$min$sec";
$shipdate = "$year-$mon-$mday";
$datestamp = "$year/$mon/$mday $hour:$min";

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
	if ( ($line =~ /^VARREPORT_host/) && ($CLIREPORT_host < 1) )
		{$VARREPORT_host = $line;   $VARREPORT_host =~ s/.*=//gi;}
	if ( ($line =~ /^VARREPORT_user/) && ($CLIREPORT_user < 1) )
		{$VARREPORT_user = $line;   $VARREPORT_user =~ s/.*=//gi;}
	if ( ($line =~ /^VARREPORT_pass/) && ($CLIREPORT_pass < 1) )
		{$VARREPORT_pass = $line;   $VARREPORT_pass =~ s/.*=//gi;}
	if ( ($line =~ /^VARREPORT_port/) && ($CLIREPORT_port < 1) )
		{$VARREPORT_port = $line;   $VARREPORT_port =~ s/.*=//gi;}
	if ( ($line =~ /^VARREPORT_dir/) && ($CLIREPORT_dir < 1) )
		{$VARREPORT_dir = $line;   $VARREPORT_dir =~ s/.*=//gi;}

	$i++;
	}

# Customized Variables
$server_ip = $VARserver_ip;		# Asterisk server IP

	$sale_statusesSQL = $sale_statuses;
	$sale_statusesSQL =~ s/-/','/gi;
	$sale_statusesSQL = "'$sale_statusesSQL'";
	$ni_statusesSQL = $ni_statuses;
	$ni_statusesSQL =~ s/-/','/gi;
	$ni_statusesSQL = "'$ni_statusesSQL'";
	$np_statusesSQL = $np_statuses;
	$np_statusesSQL =~ s/-/','/gi;
	$np_statusesSQL = "'$np_statusesSQL'";
	$ignore_listsSQL = $ignore_lists;
	$ignore_listsSQL =~ s/-/','/gi;
	$ignore_listsSQL = "'$ignore_listsSQL'";

if (!$Q)
	{
	print "\n\n\n\n\n\n\n\n\n\n\n\n-- AST_sourceID_summary_export.pl --\n\n";
	print "This program is designed to generate a summary report of sourceIDs for VICIDIAL outbound-only campaign and post them to a file. \n";
	print "\n";
	print "Sale Statuses: $sale_statuses     $sale_statusesSQL\n";
	print "NI Statuses:   $ni_statuses     $ni_statusesSQL\n";
	print "NP Statuses:   $np_statuses     $np_statusesSQL\n";
	print "Ignore Lists:  $ignore_lists     $ignore_listsSQL\n";
	print "\n";
	}

$outfile = "SOURCEID_SUMMARY$filedate$txt";

### open the X out file for writing ###
open(out, ">$PATHweb/vicidial/server_reports/$outfile")
		|| die "Can't open $outfile: $!\n";

if (!$VARDB_port) {$VARDB_port='3306';}


use DBI;

$dbhA = DBI->connect("DBI:mysql:$VARDB_database:$VARDB_server:$VARDB_port", "$VARDB_user", "$VARDB_pass")
 or die "Couldn't connect to database: " . DBI->errstr;

$dbhB = DBI->connect("DBI:mysql:$VARDB_database:$VARDB_server:$VARDB_port", "$VARDB_user", "$VARDB_pass")
 or die "Couldn't connect to database: " . DBI->errstr;

$w=0;


###########################################################################
########### SALES TOTAL IN SYSTEM BY SOURCE_ID vicidial_list         ######
###########################################################################
$stmtA = "select source_id,count(*) from vicidial_list where status IN($sale_statusesSQL) and list_id NOT IN($ignore_listsSQL) group by source_id order by source_id;";
if ($DB) {print "|$stmtA|\n";}
$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
$sthArows=$sthA->rows;
$rec_count=0;
while ($sthArows > $rec_count)
	{
	@aryA = $sthA->fetchrow_array;
	$source_id =	$aryA[0];
	$count =		$aryA[1];

	print out "ALL_SALES|$source_id|$count\r\n"; 
	if ($DBX) {print "$rec_count|ALL_SALES|$source_id|$count|\n";}
	$rec_count++;   $w++;
	}
$sthA->finish();


###########################################################################
########### NI TOTAL IN SYSTEM BY SOURCE_ID vicidial_list            ######
###########################################################################
$stmtA = "select source_id,count(*) from vicidial_list where status IN($ni_statusesSQL) and list_id NOT IN($ignore_listsSQL) group by source_id order by source_id;";
if ($DB) {print "|$stmtA|\n";}
$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
$sthArows=$sthA->rows;
$rec_count=0;
while ($sthArows > $rec_count)
	{
	@aryA = $sthA->fetchrow_array;
	$source_id =	$aryA[0];
	$count =		$aryA[1];

	print out "NOT_INTR|$source_id|$count\r\n"; 
	if ($DBX) {print "$rec_count|NOT_INTR|$source_id|$count|\n";}
	$rec_count++;   $w++;
	}
$sthA->finish();


###########################################################################
########### NP TOTAL IN SYSTEM BY SOURCE_ID vicidial_list            ######
###########################################################################
$stmtA = "select source_id,count(*) from vicidial_list where status IN($np_statusesSQL) and list_id NOT IN($ignore_listsSQL) group by source_id order by source_id;";
if ($DB) {print "|$stmtA|\n";}
$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
$sthArows=$sthA->rows;
$rec_count=0;
while ($sthArows > $rec_count)
	{
	@aryA = $sthA->fetchrow_array;
	$source_id =	$aryA[0];
	$count =		$aryA[1];

	print out "B6_NO_PTCH|$source_id|$count\r\n"; 
	if ($DBX) {print "$rec_count|B6_NO_PTCH|$source_id|$count|\n";}
	$rec_count++;   $w++;
	}
$sthA->finish();


###########################################################################
########### ALL TOTAL IN SYSTEM BY SOURCE_ID vicidial_list           ######
###########################################################################
$stmtA = "select source_id,count(*) from vicidial_list where list_id NOT IN($ignore_listsSQL) group by source_id order by source_id;";
if ($DB) {print "|$stmtA|\n";}
$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
$sthArows=$sthA->rows;
$rec_count=0;
while ($sthArows > $rec_count)
	{
	@aryA = $sthA->fetchrow_array;
	$source_id =	$aryA[0];
	$count =		$aryA[1];

	print out "RECD_COUNT|$source_id|$count\r\n"; 
	if ($DBX) {print "$rec_count|RECD_COUNT|$source_id|$count|\n";}
	$rec_count++;   $w++;
	}
$sthA->finish();

close(out);


if ($ftp_transfer > 0)
	{
	use Net::FTP;

	if (!$Q) {print "Sending File Over FTP: $outfile\n";}
	$ftp = Net::FTP->new("$VARREPORT_host", Port => $VARREPORT_port);
	$ftp->login("$VARREPORT_user","$VARREPORT_pass");
	$ftp->cwd("$VARREPORT_dir");
	$ftp->put("$PATHweb/vicidial/server_reports/$outfile", "$outfile");
	$ftp->quit;
	}

### calculate time to run script ###
$secY = time();
$secZ = ($secY - $secX);
$secZm = ($secZ /60);

if (!$Q) {print "SOURCEID EXPORT FOR $timestamp: $outfile\n";}
if (!$Q) {print "TOTAL RECORDS IN FILE: $w\n";}
if (!$Q) {print "script execution time in seconds: $secZ     minutes: $secZm\n";}

exit;


