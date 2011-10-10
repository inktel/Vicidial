#!/usr/bin/perl
#
# AST_DB_optimize.pl version 2.2.0   *DBI-version*
#
# DESCRIPTION:
# optimizes the tables used in the asterisk MySQL database
#
# It is recommended that you run this program on the local Asterisk machine
#
# Copyright (C) 2009  Matt Florell <vicidial@gmail.com>    LICENSE: AGPLv2
#
# CHANGES
# 60717-1242 - changed to DBI by Marin Blu
# 60718-1645 - changed to use /etc/astguiclient.conf for configs
# 71030-2020 - Added deletions of stats and inbound live agents
# 71109-1725 - fixed vicidial_campaign_stats bug
# 71215-0410 - fixed UPDATE/DELETE results
# 80909-0555 - added vicidial_campaign_dnc table
# 90307-2025 - Added several new queries and rearranged update/deletes
# 90401-1335 - Added quiet and test flag functions
# 90628-2354 - Added vicidial_drop_rate_groups optimizations
# 91028-1028 - Removed non-optimize queries, they are placed in ADMIN_keepalive now and happen at timeclock reset time
#

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



$MT[0]='';
$q=0;

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
		print "  [-q] = quiet\n";
		print "  [-t] = test\n";
		print "  [-help] = this screen\n";
		print "\n";

		exit;
		}
	else
		{
		if ($args =~ /-t/i)
			{
			$T=1;
			}
		if ($args =~ /-q/i)
			{
			$q=1;
			$DB=0;
			}
		}
	}
else
	{
	print "no command line options set\n";
	}

use DBI;	  

$dbhA = DBI->connect("DBI:mysql:$VARDB_database:$VARDB_server:$VARDB_port", "$VARDB_user", "$VARDB_pass")
 or die "Couldn't connect to database: " . DBI->errstr;

$stmtA = "optimize table call_log;";
if($DB){print STDERR "\n|$stmtA|\n";}
if (!$T) 
	{
	$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
	$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
	$sthArows=$sthA->rows;
	@aryA = $sthA->fetchrow_array;
	if (!$q) {print "|",$aryA[0],"|",$aryA[1],"|",$aryA[2],"|",$aryA[3],"|","\n";}
	$sthA->finish();
	}

$stmtA = "optimize table park_log;";
if($DB){print STDERR "\n|$stmtA|\n";}
if (!$T) 
	{
	$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
	$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
	$sthArows=$sthA->rows;
	@aryA = $sthA->fetchrow_array;
	if (!$q) {print "|",$aryA[0],"|",$aryA[1],"|",$aryA[2],"|",$aryA[3],"|","\n";}
	$sthA->finish();
	}

$stmtA = "optimize table vicidial_log;";
if($DB){print STDERR "\n|$stmtA|\n";}
if (!$T) 
	{
	$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
	$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
	$sthArows=$sthA->rows;
	@aryA = $sthA->fetchrow_array;
	if (!$q) {print "|",$aryA[0],"|",$aryA[1],"|",$aryA[2],"|",$aryA[3],"|","\n";}
	$sthA->finish();
	}

$stmtA = "optimize table vicidial_closer_log;";
if($DB){print STDERR "\n|$stmtA|\n";}
if (!$T) 
	{
	$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
	$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
	$sthArows=$sthA->rows;
	@aryA = $sthA->fetchrow_array;
	if (!$q) {print "|",$aryA[0],"|",$aryA[1],"|",$aryA[2],"|",$aryA[3],"|","\n";}
	$sthA->finish();
	}

$stmtA = "optimize table vicidial_xfer_log;";
if($DB){print STDERR "\n|$stmtA|\n";}
if (!$T) 
	{
	$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
	$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
	$sthArows=$sthA->rows;
	@aryA = $sthA->fetchrow_array;
	if (!$q) {print "|",$aryA[0],"|",$aryA[1],"|",$aryA[2],"|",$aryA[3],"|","\n";}
	$sthA->finish();
	}

$stmtA = "optimize table vicidial_list;";
if($DB){print STDERR "\n|$stmtA|\n";}
if (!$T) 
	{
	$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
	$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
	$sthArows=$sthA->rows;
	@aryA = $sthA->fetchrow_array;
	if (!$q) {print "|",$aryA[0],"|",$aryA[1],"|",$aryA[2],"|",$aryA[3],"|","\n";}
	$sthA->finish();
	}

$stmtA = "optimize table vicidial_manager;";
if($DB){print STDERR "\n|$stmtA|\n";}
if (!$T) 
	{
	$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
	$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
	$sthArows=$sthA->rows;
	@aryA = $sthA->fetchrow_array;
	if (!$q) {print "|",$aryA[0],"|",$aryA[1],"|",$aryA[2],"|",$aryA[3],"|","\n";}
	$sthA->finish();
	}

$stmtA = "optimize table vicidial_auto_calls;";
if($DB){print STDERR "\n|$stmtA|\n";}
if (!$T) 
	{
	$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
	$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
	$sthArows=$sthA->rows;
	@aryA = $sthA->fetchrow_array;
	if (!$q) {print "|",$aryA[0],"|",$aryA[1],"|",$aryA[2],"|",$aryA[3],"|","\n";}
	$sthA->finish();
	}

$stmtA = "optimize table vicidial_live_agents;";
if($DB){print STDERR "\n|$stmtA|\n";}
if (!$T) 
	{
	$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
	$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
	$sthArows=$sthA->rows;
	@aryA = $sthA->fetchrow_array;
	if (!$q) {print "|",$aryA[0],"|",$aryA[1],"|",$aryA[2],"|",$aryA[3],"|","\n";}
	$sthA->finish();
	}

$stmtA = "optimize table vicidial_dnc;";
if($DB){print STDERR "\n|$stmtA|\n";}
if (!$T) 
	{
	$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
	$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
	$sthArows=$sthA->rows;
	@aryA = $sthA->fetchrow_array;
	if (!$q) {print "|",$aryA[0],"|",$aryA[1],"|",$aryA[2],"|",$aryA[3],"|","\n";}
	$sthA->finish();
	}

$stmtA = "optimize table vicidial_campaign_dnc;";
if($DB){print STDERR "\n|$stmtA|\n";}
if (!$T) 
	{
	$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
	$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
	$sthArows=$sthA->rows;
	@aryA = $sthA->fetchrow_array;
	if (!$q) {print "|",$aryA[0],"|",$aryA[1],"|",$aryA[2],"|",$aryA[3],"|","\n";}
	$sthA->finish();
	}

$stmtA = "optimize table vicidial_callbacks;";
if($DB){print STDERR "\n|$stmtA|\n";}
if (!$T) 
	{
	$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
	$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
	$sthArows=$sthA->rows;
	@aryA = $sthA->fetchrow_array;
	if (!$q) {print "|",$aryA[0],"|",$aryA[1],"|",$aryA[2],"|",$aryA[3],"|","\n";}
	$sthA->finish();
	}

$stmtA = "optimize table vicidial_agent_log;";
if($DB){print STDERR "\n|$stmtA|\n";}
if (!$T) 
	{
	$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
	$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
	$sthArows=$sthA->rows;
	@aryA = $sthA->fetchrow_array;
	if (!$q) {print "|",$aryA[0],"|",$aryA[1],"|",$aryA[2],"|",$aryA[3],"|","\n";}
	$sthA->finish();
	}

$stmtA = "optimize table vicidial_conferences;";
if($DB){print STDERR "\n|$stmtA|\n";}
if (!$T) 
	{
	$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
	$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
	$sthArows=$sthA->rows;
	@aryA = $sthA->fetchrow_array;
	if (!$q) {print "|",$aryA[0],"|",$aryA[1],"|",$aryA[2],"|",$aryA[3],"|","\n";}
	$sthA->finish();
	}

$stmtA = "optimize table vicidial_hopper;";
if($DB){print STDERR "\n|$stmtA|\n";}
if (!$T) 
	{
	$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
	$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
	$sthArows=$sthA->rows;
	@aryA = $sthA->fetchrow_array;
	if (!$q) {print "|",$aryA[0],"|",$aryA[1],"|",$aryA[2],"|",$aryA[3],"|","\n";}
	$sthA->finish();
	}

$stmtA = "optimize table servers;";
if($DB){print STDERR "\n|$stmtA|\n";}
if (!$T) 
	{
	$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
	$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
	$sthArows=$sthA->rows;
	@aryA = $sthA->fetchrow_array;
	if (!$q) {print "|",$aryA[0],"|",$aryA[1],"|",$aryA[2],"|",$aryA[3],"|","\n";}
	$sthA->finish();
	}

$stmtA = "optimize table vicidial_timeclock_log;";
if($DB){print STDERR "\n|$stmtA|\n";}
if (!$T) 
	{
	$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
	$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
	$sthArows=$sthA->rows;
	@aryA = $sthA->fetchrow_array;
	if (!$q) {print "|",$aryA[0],"|",$aryA[1],"|",$aryA[2],"|",$aryA[3],"|","\n";}
	$sthA->finish();
	}

$stmtA = "optimize table vicidial_timeclock_status;";
if($DB){print STDERR "\n|$stmtA|\n";}
if (!$T) 
	{
	$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
	$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
	$sthArows=$sthA->rows;
	@aryA = $sthA->fetchrow_array;
	if (!$q) {print "|",$aryA[0],"|",$aryA[1],"|",$aryA[2],"|",$aryA[3],"|","\n";}
	$sthA->finish();
	}

$dbhA->disconnect();


exit;

