#!/usr/bin/perl
#
# AST_DB_did_route_external_NSD.pl version 2.0.5   *DBI-version*
#
# DESCRIPTION:
# 
# - Sets did_route to route calls out for NSD
#
# CHANGES
# 111211-1148 - first build
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

use DBI;	  

$dbhA = DBI->connect("DBI:mysql:$VARDB_database:$VARDB_server:$VARDB_port", "$VARDB_user", "$VARDB_pass")
 or die "Couldn't connect to database: " . DBI->errstr;

##### change Pacific Mountain	

$stmtA = "SELECT count(*) FROM vicidial_live_agents WHERE campaign_id = 'NSD';";
print STDERR "\n|$stmtA|\n";
$sthA = $dbhA->prepare($stmtA) or die "preparing: ", $dbhA->errstr;
$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
$agentsLoggedIn = $sthA->rows;
print STDERR "\n|$agentsLoggedIn agents logged in|\n";
if ($agentsLoggedIn <= 0)
{
	$stmtA = "UPDATE vicidial_inbound_dids set did_route='EXTEN' where (did_id >= 259 and did_id <= 341) or (did_id in (171,172,173));";
	print STDERR "\n|$stmtA|\n";
	$affected_rows = $dbhA->do($stmtA);
	print STDERR "\n|$affected_rows records changed|\n";
}
else
{
	$stmtA = "UPDATE vicidial_inbound_dids set did_route=filter_url where (did_id >= 259 and did_id <= 341) or (did_id in (171,172,173));";
	print STDERR "\n|$stmtA|\n";
	$affected_rows = $dbhA->do($stmtA);
	print STDERR "\n|$affected_rows records changed|\n";
}

$dbhA->disconnect();

exit;

