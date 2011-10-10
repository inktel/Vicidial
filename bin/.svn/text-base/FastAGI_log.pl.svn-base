#!/usr/bin/perl
#
# FastAGI_log.pl version 2.4
# 
# Experimental Deamon using perl Net::Server that runs as FastAGI to reduce load
# replaces the following AGI scripts:
# - call_log.agi
# - call_logCID.agi
# - VD_hangup.agi
#
# This script needs to be running all of the time for AGI requests to work
# 
# You need to put lines similar to those below in your extensions.conf file:
# 
# ;outbound dialing:
# exten => _91NXXNXXXXXX,1,AGI(agi://127.0.0.1:4577/call_log) 
#
# ;inbound calls:
# exten => 101,1,AGI(agi://127.0.0.1:4577/call_log)
#   or
# exten => 101,1,AGI(agi://127.0.0.1:4577/call_log--fullCID--${EXTEN}-----${CALLERID}-----${CALLERIDNUM}-----${CALLERIDNAME})
#
# 
# ;all hangups:
# exten => h,1,DeadAGI(agi://127.0.0.1:4577/call_log--HVcauses--PRI-----NODEBUG-----${HANGUPCAUSE}-----${DIALSTATUS}-----${DIALEDTIME}-----${ANSWEREDTIME})
# 
#
# Copyright (C) 2011  Matt Florell <vicidial@gmail.com>    LICENSE: AGPLv2
#
# CHANGELOG:
# 61010-1007 - First test build
# 70116-1619 - Added Auto Alt Dial code
# 70215-1258 - Added queue_log entry when deleting vac record
# 70808-1425 - Moved VD_hangup section to the call_log end stage to improve efficiency
# 71030-2039 - Added priority to hopper insertions
# 80224-0040 - Fixed bugs in vicidial_log updates
# 80430-0907 - Added term_reason to vicidial_log and vicidial_closer_log
# 80507-1138 - Fixed vicidial_closer_log CALLER hangups
# 80510-0414 - Fixed crossover logging bugs
# 80510-2058 - Fixed status override bug
# 80525-1040 - Added IVR vac status compatibility for inbound calls
# 80830-0035 - Added auto alt dialing for EXTERNAL leads for each lead
# 80909-0843 - Added support for campaign-specific DNC lists
# 81021-0306 - Added Local channel logging support and while-to-if changes
# 81026-1247 - Changed to allow for better remote agent calling
# 81029-0522 - Changed to disallow queue_log logging of IVR calls
# 90604-1044 - Fixed formatting, added DAHDI support, added carrier hangup code logging
# 90608-0316 - Changed hangup code dispos B and DC to AB and ADC to separate Agent dispos from Auto
# 90630-2253 - Added Sangoma CDP pre-Answer call processing
# 90814-0810 - Added extra logging for vicidial_log in some cases
# 90815-0750 - Fixed extra vicidial_log logging
# 91020-0055 - Fixed several bugs with auto-alt-dial, DNC and extended alt number dialing
# 91026-1148 - Added AREACODE DNC option
# 91213-1213 - Added queue_position to queue_log COMPLETE... and ABANDON records
# 100108-2242 - Added answered_time to vicidial_carrier_log
# 100123-1449 - Added double-log end logging
# 100224-1229 - Fixed manual dial park call bug
# 100903-0041 - Changed lead_id max length to 10 digits
# 101111-1556 - Added source to vicidial_hopper inserts
# 101123-0443 - Fixed minor parked call manual dial bug
# 110224-1854 - Added compatibility with QM phone environment logging
# 110304-0005 - Small changes for CPD and on-hook agent features
# 110324-2336 - Changes to CPD logging of calls and addition of the PDROP status
#

# defaults for PreFork
$VARfastagi_log_min_servers =	'3';
$VARfastagi_log_max_servers =	'16';
$VARfastagi_log_min_spare_servers = '2';
$VARfastagi_log_max_spare_servers = '8';
$VARfastagi_log_max_requests =	'1000';
$VARfastagi_log_checkfordead =	'30';
$VARfastagi_log_checkforwait =	'60';

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
	if ( ($line =~ /^PATHlogs/) && ($CLIlogs < 1) )
		{$PATHlogs = $line;   $PATHlogs =~ s/.*=//gi;}
	if ( ($line =~ /^VARfastagi_log_min_servers/) && ($CLIVARfastagi_log_min_servers < 1) )
		{$VARfastagi_log_min_servers = $line;   $VARfastagi_log_min_servers =~ s/.*=//gi;}
	if ( ($line =~ /^VARfastagi_log_max_servers/) && ($CLIVARfastagi_log_max_servers < 1) )
		{$VARfastagi_log_max_servers = $line;   $VARfastagi_log_max_servers =~ s/.*=//gi;}
	if ( ($line =~ /^VARfastagi_log_min_spare_servers/) && ($CLIVARfastagi_log_min_spare_servers < 1) )
		{$VARfastagi_log_min_spare_servers = $line;   $VARfastagi_log_min_spare_servers =~ s/.*=//gi;}
	if ( ($line =~ /^VARfastagi_log_max_spare_servers/) && ($CLIVARfastagi_log_max_spare_servers < 1) )
		{$VARfastagi_log_max_spare_servers = $line;   $VARfastagi_log_max_spare_servers =~ s/.*=//gi;}
	if ( ($line =~ /^VARfastagi_log_max_requests/) && ($CLIVARfastagi_log_max_requests < 1) )
		{$VARfastagi_log_max_requests = $line;   $VARfastagi_log_max_requests =~ s/.*=//gi;}
	if ( ($line =~ /^VARfastagi_log_checkfordead/) && ($CLIVARfastagi_log_checkfordead < 1) )
		{$VARfastagi_log_checkfordead = $line;   $VARfastagi_log_checkfordead =~ s/.*=//gi;}
	if ( ($line =~ /^VARfastagi_log_checkforwait/) && ($CLIVARfastagi_log_checkforwait < 1) )
		{$VARfastagi_log_checkforwait = $line;   $VARfastagi_log_checkforwait =~ s/.*=//gi;}
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

($sec,$min,$hour,$mday,$mon,$year,$wday,$yday,$isdst) = localtime(time);
$year = ($year + 1900);
$mon++;
if ($mon < 10) {$mon = "0$mon";}
if ($mday < 10) {$mday = "0$mday";}
if ($hour < 10) {$hour = "0$hour";}
if ($min < 10) {$min = "0$min";}
if ($sec < 10) {$sec = "0$sec";}

if (!$VARDB_port) {$VARDB_port='3306';}

$SERVERLOG = 'N';
$log_level = '0';

use DBI;
$dbhB = DBI->connect("DBI:mysql:$VARDB_database:$VARDB_server:$VARDB_port", "$VARDB_user", "$VARDB_pass")
	or die "Couldn't connect to database: " . DBI->errstr;

### Grab Server values from the database
$stmtB = "SELECT vd_server_logs FROM servers where server_ip = '$VARserver_ip';";
$sthB = $dbhB->prepare($stmtB) or die "preparing: ",$dbhB->errstr;
$sthB->execute or die "executing: $stmtB ", $dbhB->errstr;
$sthBrows=$sthB->rows;
if ($sthBrows > 0)
	{
	@aryB = $sthB->fetchrow_array;
	$SERVERLOG =	$aryB[0];
	}
$sthB->finish();
$dbhB->disconnect();

if ($SERVERLOG =~ /Y/) 
	{
	$childLOGfile = "$PATHlogs/FastAGIchildLOG.$year-$mon-$mday";
	$log_level = "4";
	print "SERVER LOGGING ON: LEVEL-$log_level FILE-$childLOGfile\n";
	}

package VDfastAGI;

use Net::Server;
use Asterisk::AGI;
use vars qw(@ISA);
use Net::Server::PreFork; # any personality will do
@ISA = qw(Net::Server::PreFork);
use Time::HiRes ('gettimeofday','usleep','sleep');  # necessary to have perl sleep command of less than one second




sub process_request 
	{
	$carrier_logging_active=0;
	$process = 'begin';
	$script = 'VDfastAGI';
	########## Get current time, parse configs, get logging preferences ##########
	($sec,$min,$hour,$mday,$mon,$year,$wday,$yday,$isdst) = localtime(time);
	$year = ($year + 1900);
	$mon++;
	if ($mon < 10) {$mon = "0$mon";}
	if ($mday < 10) {$mday = "0$mday";}
	if ($hour < 10) {$hour = "0$hour";}
	if ($min < 10) {$min = "0$min";}
	if ($sec < 10) {$sec = "0$sec";}

	$now_date_epoch = time();
	$now_date = "$year-$mon-$mday $hour:$min:$sec";


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

	if (!$VARDB_port) {$VARDB_port='3306';}
	if (!$AGILOGfile) {$AGILOGfile = "$PATHlogs/FASTagiout.$year-$mon-$mday";}

	$dbhA = DBI->connect("DBI:mysql:$VARDB_database:$VARDB_server:$VARDB_port", "$VARDB_user", "$VARDB_pass")
		or die "Couldn't connect to database: " . DBI->errstr;

	### Grab Server values from the database
	$stmtA = "SELECT agi_output FROM servers where server_ip = '$VARserver_ip';";
	$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
	$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
	$sthArows=$sthA->rows;
	if ($sthArows > 0)
		{
		$AGILOG = '0';
		@aryA = $sthA->fetchrow_array;
		$DBagi_output =			$aryA[0];
		if ($DBagi_output =~ /STDERR/)	{$AGILOG = '1';}
		if ($DBagi_output =~ /FILE/)	{$AGILOG = '2';}
		if ($DBagi_output =~ /BOTH/)	{$AGILOG = '3';}
		}
	$sthA->finish();


	if ($AGILOG) 
		{
		$agi_string = "+++++++++++++++++ FastAGI Start ++++++++++++++++++++++++++++++++++++++++"; 
		&agi_output;
		}



	### begin parsing run-time options ###
	if (length($ARGV[0])>1)
		{
		if ($AGILOG) 
			{
			$agi_string = "Perl Environment Dump:"; 
			&agi_output;
			}
		$i=0;
		while ($#ARGV >= $i)
			{
			$args = "$args $ARGV[$i]";
			if ($AGILOG) 
				{
				$agi_string = "$i|$ARGV[$i]";   
				&agi_output;
				}
			$i++;
			}
		}

	$HVcauses=0;
	$fullCID=0;
	$callerid='';
	$calleridname='';
	$|=1;
	while(<STDIN>) 
		{
		chomp;
		last unless length($_);
		if ($AGILOG)
			{
			if (/^agi_(\w+)\:\s+(.*)$/)
				{
					$AGI{$1} = $2;
				}
			}

		if (/^agi_uniqueid\:\s+(.*)$/)		{$unique_id = $1; $uniqueid = $unique_id;}
		if (/^agi_priority\:\s+(.*)$/)		{$priority = $1;}
		if (/^agi_channel\:\s+(.*)$/)		{$channel = $1;}
		if (/^agi_extension\:\s+(.*)$/)		{$extension = $1;}
		if (/^agi_type\:\s+(.*)$/)			{$type = $1;}
		if (/^agi_request\:\s+(.*)$/)		{$request = $1;}
		if ( ($request =~ /--fullCID--/i) && (!$fullCID) )
			{
			$fullCID=1;
			@CID = split(/-----/, $request);
			$callerid =	$CID[2];
			$calleridname =	$CID[3];
			$agi_string = "URL fullCID: |$callerid|$calleridname|$request|";   
			&agi_output;
			}
		if ( ($request =~ /--HVcauses--/i) && (!$HVcauses) )
			{
			$HVcauses=1;
			@ARGV_vars = split(/-----/, $request);
			$PRI = $ARGV_vars[0];
			$PRI =~ s/.*--HVcauses--//gi;
			$DEBUG = $ARGV_vars[1];
			$hangup_cause = $ARGV_vars[2];
			$dialstatus = $ARGV_vars[3];
			$dial_time = $ARGV_vars[4];
			$answered_time = $ARGV_vars[5];
            if( $dial_time > $answered_time ) 
				{$ring_time = $dial_time - $answered_time;}
            else 
				{$ring_time = 0;}
			$agi_string = "URL HVcauses: |$PRI|$DEBUG|$hangup_cause|$dialstatus|$dial_time|$ring_time|";   
			&agi_output;
			}
		if (!$fullCID)	# if no fullCID sent
			{
			if (/^agi_callerid\:\s+(.*)$/)		{$callerid = $1;}
			if (/^agi_calleridname\:\s+(.*)$/)	{$calleridname = $1;}
			if ( $calleridname =~ /\"/)  {$calleridname =~ s/\"//gi;}
	#	if ( (length($calleridname)>5) && ( (!$callerid) or ($callerid =~ /unknown|private|00000000/i) or ($callerid =~ /5551212/) ) )
		if ( ( 
		(length($calleridname)>5) && ( (!$callerid) or ($callerid =~ /unknown|private|00000000/i) or ($callerid =~ /5551212/) )
		) or ( (length($calleridname)>17) && ($calleridname =~ /\d\d\d\d\d\d\d\d\d\d\d\d\d\d\d\d\d\d\d/) ) )
			{$callerid = $calleridname;}

			### allow for ANI being sent with the DNIS "*3125551212*9999*"
			if ($extension =~ /^\*\d\d\d\d\d\d\d\d\d\d\*/)
				{
				$callerid = $extension;
				$callerid =~ s/\*\d\d\d\d\*$//gi;
				$callerid =~ s/^\*//gi;
				$extension =~ s/^\*\d\d\d\d\d\d\d\d\d\d\*//gi;
				$extension =~ s/\*$//gi;
				}
			$calleridname = $callerid;
			}
		}

	if ($AGILOG) 
		{
		$agi_string = "AGI Environment Dump:";   
		&agi_output;
		}

	foreach $i (sort keys %AGI) 
		{
		if ($AGILOG) 
			{
			$agi_string = " -- $i = $AGI{$i}";   
			&agi_output;
			}
		}


	if ($AGILOG) 
		{
		$agi_string = "AGI Variables: |$unique_id|$channel|$extension|$type|$callerid|";   
		&agi_output;
		}

	if ( ($extension =~ /h/i) && (length($extension) < 3))  {$stage = 'END';}
	else {$stage = 'START';}

	$process = $request;
	$process =~ s/agi:\/\///gi;
	$process =~ s/.*\/|--.*//gi;
	if ($AGILOG) 
		{
		$agi_string = "Process to run: |$request|$process|$stage|";   
		&agi_output;
		}


	###################################################################
	##### START call_log process ######################################
	###################################################################
	if ($process =~ /^call_log/)
		{
		### call start stage
		if ($stage =~ /START/)
			{
			$callerid =~ s/ .*//gi;
			$channel_group='';

			if ($AGILOG) {$agi_string = "+++++ CALL LOG START : $now_date";   &agi_output;}

			if ($channel =~ /^SIP/) {$channel =~ s/-.*//gi;}
			if ($channel =~ /^IAX2/) {$channel =~ s/\/\d+$//gi;}
			if ($channel =~ /^Zap\/|^DAHDI\//)
				{
				$channel_line = $channel;
				$channel_line =~ s/^Zap\/|^DAHDI\///gi;

				$stmtA = "SELECT count(*) FROM phones where server_ip='$VARserver_ip' and extension='$channel_line' and protocol='Zap';";
				$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
				$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
				$sthArows=$sthA->rows;
				@aryA = $sthA->fetchrow_array;
				$is_client_phone	 = $aryA[0];
				$sthA->finish();

				if ($is_client_phone < 1)
					{
					$channel_group = 'Zap Trunk Line';
					$number_dialed = $extension;
					}
				else
					{
					$channel_group = 'Zap Client Phone';
					}
				if ($AGILOG) {$agi_string = $channel_group . ": $aryA[0]|$channel_line|";   &agi_output;}
				}
			if ($channel =~ /^Local\//)
				{
				$channel_line = $channel;
				$channel_line =~ s/^Local\/|\@.*//gi;

				$stmtA = "SELECT count(*) FROM phones where server_ip='$VARserver_ip' and dialplan_number='$channel_line' and protocol='EXTERNAL';";
				$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
				$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
				$sthArows=$sthA->rows;
				@aryA = $sthA->fetchrow_array;
				$is_client_phone	 = $aryA[0];
				$sthA->finish();

				if ($is_client_phone < 1)
					{
					$channel_group = 'Local Channel Line';
					$number_dialed = $extension;
					}
				else
					{
					$channel_group = 'EXTERNAL Client Phone';
					}
				if ($AGILOG) {$agi_string = $channel_group . ": $aryA[0]|$channel_line|";   &agi_output;}
				}
			### This section breaks the outbound dialed number down(or builds it up) to a 10 digit number and gives it a description
			if ( ($channel =~ /^SIP|^IAX2/) || ( ($is_client_phone > 0) && (length($channel_group) < 1) ) )
				{
				if ( ($extension =~ /^901144/) && (length($extension)==16) )  #test 207 608 6400 
					{$extension =~ s/^9//gi;	$channel_group = 'Outbound Intl UK';}
				if ( ($extension =~ /^901161/) && (length($extension)==15) )  #test  39 417 2011
					{$extension =~ s/^9//gi;	$channel_group = 'Outbound Intl AUS';}
				if ( ($extension =~ /^91800|^91888|^91877|^91866/) && (length($extension)==12) )
					{$extension =~ s/^91//gi;	$channel_group = 'Outbound Local 800';}
				if ( ($extension =~ /^9/) && (length($extension)==8) )
					{$extension =~ s/^9/727/gi;	$channel_group = 'Outbound Local';}
				if ( ($extension =~ /^9/) && (length($extension)==11) )
					{$extension =~ s/^9//gi;	$channel_group = 'Outbound Local';}
				if ( ($extension =~ /^91/) && (length($extension)==12) )
					{$extension =~ s/^91//gi;	$channel_group = 'Outbound Long Distance';}
				if ($is_client_phone > 0)
					{$channel_group = 'Client Phone';}
				
				$SIP_ext = $channel;	$SIP_ext =~ s/SIP\/|IAX2\/|Zap\/|DAHDI\/|Local\///gi;

				$number_dialed = $extension;
				$extension = $SIP_ext;
				}

			if ( ($callerid =~ /^V|^M/) && ($callerid =~ /\d\d\d\d\d\d\d\d\d/) && (length($number_dialed)<1) )
				{
				$stmtA = "SELECT cmd_line_b,cmd_line_d FROM vicidial_manager where callerid='$callerid' limit 1;";
				$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
				$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
				$sthArows=$sthA->rows;
				$rec_count=0;
				if ($sthArows > 0)
					{
					@aryA = $sthA->fetchrow_array;
					$cmd_line_b	=	$aryA[0];
					$cmd_line_d	=	$aryA[1];
					$cmd_line_b =~ s/Exten: //gi;
					$cmd_line_d =~ s/Channel: Local\/|@.*//gi;
					$rec_count++;
					}
				$sthA->finish();
				if ($callerid =~ /^V/) {$number_dialed = "$cmd_line_d";}
				if ($callerid =~ /^M/) {$number_dialed = "$cmd_line_b";}
				$number_dialed =~ s/\D//gi;
				if (length($number_dialed)<1) {$number_dialed=$extension;}
				}
			$stmtA = "INSERT INTO call_log (uniqueid,channel,channel_group,type,server_ip,extension,number_dialed,start_time,start_epoch,end_time,end_epoch,length_in_sec,length_in_min,caller_code) values('$unique_id','$channel','$channel_group','$type','$VARserver_ip','$extension','$number_dialed','$now_date','$now_date_epoch','','','','','$callerid')";

			if ($AGILOG) {$agi_string = "|$stmtA|";   &agi_output;}
			$affected_rows = $dbhA->do($stmtA);

			$dbhA->disconnect();
			}


		### call end stage
		else
			{
			if ($AGILOG) {$agi_string = "|CALL HUNG UP|";   &agi_output;}

			$callerid =~ s/ .*//gi;
			$callerid =~ s/\"//gi;
			$CIDlead_id = $callerid;
			$CIDlead_id = substr($CIDlead_id, 10, 10);
			$CIDlead_id = ($CIDlead_id + 0);

			$park_abandon=0;
			### find if call was parked on another server
			$stmtA = "SELECT count(*) from parked_channels where channel_group='$callerid';";
			$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
			$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
			$sthArows=$sthA->rows;
			if ($sthArows > 0)
				{
				@aryA = $sthA->fetchrow_array;
				$sthA->finish();
				$parked_count = $aryA[0];
				if ($parked_count > 0)
					{
					$stmtA = "SELECT server_ip from parked_channels where channel_group='$callerid';";
					$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
					$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
					$sthArows=$sthA->rows;
					if ($sthArows > 0)
						{
						@aryA = $sthA->fetchrow_array;
						$sthA->finish();
						$parked_server_ip = $aryA[0];
						if ( ($parked_server_ip =~ /$VARserver_ip/) && (length($VARserver_ip) == length($parked_server_ip)) )
							{
							if ($AGILOG) {$agi_string = "--    PARKED CALL ON-SERVER: |$parked_server_ip|$VARserver_ip|$callerid";   &agi_output;}
							}
						else
							{
							if ($AGILOG) {$agi_string = "--    PARKED CALL NON-SERVER: |$parked_server_ip|$VARserver_ip|$callerid";   &agi_output;}
							$park_abandon=1;
							}
						}
					}
				}
			
			if ($park_abandon < 1)
				{
				if ($request =~ /--HVcauses--/i)
					{
					$HVcauses=1;
					@ARGV_vars = split(/-----/, $request);
					$PRI = $ARGV_vars[0];
					$PRI =~ s/.*--HVcauses--//gi;
					$DEBUG =			$ARGV_vars[1];
					$hangup_cause =		$ARGV_vars[2];
					$dialstatus =		$ARGV_vars[3];
					$dial_time =		$ARGV_vars[4];
					$answered_time =	$ARGV_vars[5];
					if( $dial_time > $answered_time ) 
						{$ring_time = $dial_time - $answered_time;}
					else 
						{$ring_time = 0;}
					$agi_string = "URL HVcauses: |$PRI|$DEBUG|$hangup_cause|$dialstatus|$dial_time|$ring_time|";   
					&agi_output;

					if ( (length($dialstatus) > 0) && ($callerid =~ /^V|^M/) )
						{
						### Grab Server values from the database
						$stmtA = "SELECT carrier_logging_active FROM servers where server_ip = '$VARserver_ip';";
						$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
						$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
						$sthArows=$sthA->rows;
						if ($sthArows > 0)
							{
							@aryA = $sthA->fetchrow_array;
							if ($aryA[0] =~ /Y/)
								{$carrier_logging_active = 1;}
							else
								{$carrier_logging_active = 0;}
							}
						$sthA->finish();

						if ($carrier_logging_active > 0)
							{
							if ($callerid =~ /^M/) 
								{
								$beginUNIQUEID = $unique_id;
								$beginUNIQUEID =~ s/\..*//gi;
								$stmtA = "SELECT uniqueid FROM call_log where uniqueid LIKE \"$beginUNIQUEID%\" and caller_code LIKE \"%$CIDlead_id\";";
								$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
								$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
								$sthArows=$sthA->rows;
								$rec_count=0;
								if ($sthArows > 0)
									{
									@aryA = $sthA->fetchrow_array;
									$unique_id =	$aryA[0];
									$uniqueid =		$aryA[0];
									}
								$sthA->finish();
								}
							$stmtA = "INSERT IGNORE INTO vicidial_carrier_log set uniqueid='$uniqueid',call_date='$now_date',server_ip='$VARserver_ip',lead_id='$CIDlead_id',hangup_cause='$hangup_cause',dialstatus='$dialstatus',channel='$channel',dial_time='$dial_time',answered_time='$answered_time';";
								if ($AGILOG) {$agi_string = "|$stmtA|";   &agi_output;}
							$VCARaffected_rows = $dbhA->do($stmtA);
							if ($AGILOG) {$agi_string = "--    CARRIER LOG insert: |$VCARaffected_rows|$CIDlead_id|$hangup_cause";   &agi_output;}
							}
						}
					}

				### get uniqueid and start_epoch from the call_log table
				$CALLunique_id = $unique_id;
				$stmtA = "SELECT uniqueid,start_epoch,channel,end_epoch,channel_group FROM call_log where uniqueid='$unique_id';";
				if ($callerid =~ /^M/) 
					{$stmtA = "SELECT uniqueid,start_epoch,channel,end_epoch,channel_group FROM call_log where caller_code='$callerid' and channel NOT LIKE \"Local\/%\";";}
				$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
				$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
				$sthArows=$sthA->rows;
				$rec_count=0;
				while ($sthArows > $rec_count)
					{
					@aryA = $sthA->fetchrow_array;
					$unique_id =		$aryA[0];
					$uniqueid =			$aryA[0];
					$start_time	=		$aryA[1];
					if ($callerid =~ /^M/)
						{$channel =			$aryA[2];}
					$end_epoch =		$aryA[3];
					$channel_group = 	$aryA[4]; 
					if ($AGILOG) {$agi_string = "|$aryA[0]|$aryA[1]|$aryA[2]|$aryA[3]|";   &agi_output;}
					$rec_count++;
					}
				$sthA->finish();

				$did_log=0;
				if ( ($channel_group =~ /DID_INBOUND/) && ($end_epoch > 1000) )
					{$did_log=1;}
				if ( ($rec_count) && ($did_log < 1) )
					{
					$length_in_sec = ($now_date_epoch - $start_time);
					$length_in_min = ($length_in_sec / 60);
					$length_in_min = sprintf("%8.2f", $length_in_min);

					$stmtA = "UPDATE call_log set end_time='$now_date',end_epoch='$now_date_epoch',length_in_sec=$length_in_sec,length_in_min='$length_in_min',channel='$channel' where uniqueid='$unique_id'";

					if ($AGILOG) {$agi_string = "|$stmtA|";   &agi_output;}
					$affected_rowsL = $dbhA->do($stmtA);

					if ($AGILOG) {$agi_string = "QUERY done: start time = $start_time | sec: $length_in_sec | min: $length_in_min |$affected_rowsL";   &agi_output;}

					if ($channel_group =~ /DID_INBOUND/)
						{
						$stmtA = "SELECT recording_id,start_epoch,filename,end_epoch FROM recording_log where vicidial_id='$unique_id' order by start_time desc limit 1;";
						$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
						$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
						$sthArows=$sthA->rows;
						if ($sthArows > 0)
							{
							@aryA = $sthA->fetchrow_array;
							$CLrecording_id = 	$aryA[0];
							$CLstart_epoch =	$aryA[1];
							$CLfilename = 		$aryA[2];
							$CLend_epoch = 		$aryA[3];

							if (length($CLend_epoch) < 5)
								{
								$CLlength_in_sec = ($now_date_epoch - $CLstart_epoch);
								$CLlength_in_min = ($CLlength_in_sec / 60);
								$CLlength_in_min = sprintf("%8.2f", $CLlength_in_min);

								$stmtA = "UPDATE recording_log set end_time='$now_date',end_epoch='$now_date_epoch',length_in_sec=$CLlength_in_sec,length_in_min='$CLlength_in_min' where recording_id='$CLrecording_id'";
								$affected_rowsRL = $dbhA->do($stmtA);
								if ($AGILOG) {$agi_string = "Recording stopped: $CLstart_epoch|$now_date_epoch|$affected_rowsRL|$stmtA|";   &agi_output;}
								}
							}
						$sthA->finish();
						}
					}
				### BEGIN Double-log end logging ###
				$DOUBLEunique_id = $unique_id . "99";
				$stmtA = "SELECT start_epoch FROM call_log where uniqueid='$DOUBLEunique_id' and channel_group='DOUBLE_LOG' order by start_time desc limit 1;";
				$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
				$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
				$sthArows=$sthA->rows;
				if ($sthArows > 0)
					{
					@aryA = $sthA->fetchrow_array;
					$DOUBLEstart_epoch =	$aryA[0];

					$DOUBLElength_in_sec = ($now_date_epoch - $DOUBLEstart_epoch);
					$DOUBLElength_in_min = ($DOUBLElength_in_sec / 60);
					$DOUBLElength_in_min = sprintf("%8.2f", $DOUBLElength_in_min);

					$stmtA = "UPDATE call_log set end_time='$now_date',end_epoch='$now_date_epoch',length_in_sec=$DOUBLElength_in_sec,length_in_min='$DOUBLElength_in_min' where uniqueid='$DOUBLEunique_id' and channel_group='DOUBLE_LOG' order by start_time desc limit 1";

					if ($AGILOG) {$agi_string = "|$stmtA|";   &agi_output;}
					$affected_rowsDB = $dbhA->do($stmtA);

					if ($AGILOG) {$agi_string = "DOUBLE QUERY done: start time = $DOUBLEstart_epoch | sec: $DOUBLElength_in_sec | min: $DOUBLElength_in_min |$affected_rowsDB";   &agi_output;}
					}
				$sthA->finish();
				### END Double-log end logging ###

				$stmtA = "DELETE from live_inbound where uniqueid IN('$unique_id','$CALLunique_id') and server_ip='$VARserver_ip'";
				if ($AGILOG) {$agi_string = "|$stmtA|";   &agi_output;}
				$affected_rows = $dbhA->do($stmtA);

			##### BEGIN Park Log entry check and update #####
				$stmtA = "SELECT UNIX_TIMESTAMP(parked_time),UNIX_TIMESTAMP(grab_time) FROM park_log where uniqueid='$unique_id' and server_ip='$VARserver_ip' and (parked_sec is null or parked_sec < 1) order by parked_time desc LIMIT 1;";
				$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
				$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
				$sthArows=$sthA->rows;
				$rec_count=0;
				if ($sthArows > 0)
					{
					@aryA = $sthA->fetchrow_array;
					$parked_time	=		$aryA[0];
					$grab_time	=			$aryA[1];
					if ($AGILOG) {$agi_string = "|$aryA[0]|$aryA[1]|";   &agi_output;}
					$rec_count++;
					}
				$sthA->finish();

				if ($rec_count)
					{
					if ($AGILOG) {$agi_string = "*****Entry found for $unique_id-$VARserver_ip in park_log: $parked_time|$grab_time";   &agi_output;}
					if ($parked_time > $grab_time)
						{
						$parked_sec=($now_date_epoch - $parked_time);
						$talked_sec=0;
						}
					else
						{
						$talked_sec=($now_date_epoch - $parked_time);
						$parked_sec=($grab_time - $parked_time);
						}

					$stmtA = "UPDATE park_log set status='HUNGUP',hangup_time='$now_date',parked_sec='$parked_sec',talked_sec='$talked_sec' where uniqueid='$unique_id' and server_ip='$VARserver_ip' order by parked_time desc LIMIT 1";
					$affected_rows = $dbhA->do($stmtA);
					}
			##### END Park Log entry check and update #####

			#	$dbhA->disconnect();

				if ($AGILOG) {$agi_string = "+++++ CALL LOG HUNGUP: |$unique_id|$channel|$extension|$now_date|min: $length_in_min|";   &agi_output;}


			##### BEGIN former VD_hangup section functions #####

				$VDADcampaign='';
				$VDADphone='';
				$VDADphone_code='';
				$VDL_status='';

				if ($DEBUG =~ /^DEBUG$/)
					{
					### open the hangup cause out file for writing ###
					open(out, ">>$PATHlogs/HANGUP_cause-output.txt")
							|| die "Can't open $PATHlogs/HANGUP_cause-output.txt: $!\n";

					print out "$now_date|$hangup_cause|$dialstatus|$dial_time|$ring_time|$unique_id|$channel|$extension|$type|$callerid|$calleridname|$priority|\n";

					close(out);
					}
				else 
					{
					if ($AGILOG) {$agi_string = "DEBUG: $DEBUG";   &agi_output;}
					}

				if ($AGILOG) {$agi_string = "VD_hangup : $callerid $channel $priority $CIDlead_id";   &agi_output;}

				if ($channel =~ /^Local/)
					{
					$CPDfound=0;

					# V2251502010052435563
					if ($callerid =~ /^V\d\d\d\d\d\d\d\d\d\d\d\d\d\d\d\d\d\d\d/)
						{
					#	($s_hires, $usec) = gettimeofday();   # get seconds and microseconds since the epoch
					#	$usec = sprintf("%06s", $usec);
					#	$HRmsec = substr($usec, -6);
					#	($HRsec,$HRmin,$HRhour,$HRmday,$HRmon,$HRyear,$HRwday,$HRyday,$HRisdst) = localtime($s_hires);
					#	$HRyear = ($HRyear + 1900);
					#	$HRmon++;
					#	if ($HRmon < 10) {$HRmon = "0$HRmon";}
					#	if ($HRmday < 10) {$HRmday = "0$HRmday";}
					#	if ($HRhour < 10) {$HRFhour = "0$HRhour";}
					#	if ($HRmin < 10) {$HRmin = "0$HRmin";}
					#	if ($HRsec < 10) {$HRsec = "0$HRsec";}
					#	$HRnow_date = "$HRyear-$HRmon-$HRmday $HRhour:$HRmin:$HRsec.$HRmsec";
					#
					#	if ($AGILOG) {$agi_string = "HiRes Time: $callerid|$channel|$priority|$CIDlead_id|$uniqueid|$HRnow_date|$now_date";   &agi_output;}


						##############################################################
						### BEGIN - CPD Look for result for B/DC calls
						##############################################################
						sleep(1);

						$stmtA = "SELECT result FROM vicidial_cpd_log where callerid='$callerid' and result NOT IN('Voice','Unknown','???','') order by cpd_id desc limit 1;";
						$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
						$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
						$sthArows=$sthA->rows;
						if ($sthArows > 0)
							{
							@aryA = $sthA->fetchrow_array;
							$cpd_result		= $aryA[0];
							$sthA->finish();
							if ($cpd_result =~ /Busy/i)					{$VDL_status='CPDB';	$VDAC_status='BUSY';   $CPDfound++;}
							if ($cpd_result =~ /Unknown/i)				{$VDL_status='CPDUK';	$VDAC_status='NA';   $CPDfound++;}
							if ($cpd_result =~ /All-Trunks-Busy/i)		{$VDL_status='CPDATB';	$VDAC_status='CONGESTION';   $CPDfound++;}
							if ($cpd_result =~ /No-Answer/i)			{$VDL_status='CPDNA';	$VDAC_status='NA';   $CPDfound++;}
							if ($cpd_result =~ /Reject/i)				{$VDL_status='CPDREJ';	$VDAC_status='DISCONNECT';   $CPDfound++;}
							if ($cpd_result =~ /Invalid-Number/i)		{$VDL_status='CPDINV';	$VDAC_status='DISCONNECT';   $CPDfound++;}
							if ($cpd_result =~ /Service-Unavailable/i)	{$VDL_status='CPDSUA';	$VDAC_status='CONGESTION';   $CPDfound++;}
							if ($cpd_result =~ /Sit-Intercept/i)		{$VDL_status='CPDSI';	$VDAC_status='DISCONNECT';   $CPDfound++;}
							if ($cpd_result =~ /Sit-No-Circuit/i)		{$VDL_status='CPDSNC';	$VDAC_status='CONGESTION';   $CPDfound++;}
							if ($cpd_result =~ /Sit-Reorder/i)			{$VDL_status='CPDSR';	$VDAC_status='CONGESTION';   $CPDfound++;}
							if ($cpd_result =~ /Sit-Unknown/i)			{$VDL_status='CPDSUK';	$VDAC_status='CONGESTION';   $CPDfound++;}
							if ($cpd_result =~ /Sit-Vacant/i)			{$VDL_status='CPDSV';	$VDAC_status='CONGESTION';   $CPDfound++;}
							if ($cpd_result =~ /\?\?\?/i)				{$VDL_status='CPDERR';	$VDAC_status='NA';   $CPDfound++;}
							if ($cpd_result =~ /Fax|Modem/i)			{$VDL_status='AFAX';	$VDAC_status='FAX';   $CPDfound++;}
							if ($cpd_result =~ /Answering-Machine/i)	{$VDL_status='AA';		$VDAC_status='AMD';   $CPDfound++;}
							}
						$sthA->finish();
							if ($AGILOG) {$agi_string = "$sthArows|$cpd_result|$stmtA|";   &agi_output;}
						##############################################################
						### END - CPD Look for result for B/DC calls
						##############################################################
						}
					if ( ($PRI =~ /^PRI$/) && ($callerid =~ /\d\d\d\d\d\d\d\d\d\d\d\d\d\d\d\d\d\d\d/) && ( ( ($dialstatus =~ /BUSY/) || ( ($dialstatus =~ /CHANUNAVAIL/) && ($hangup_cause =~ /^1$|^28$/) ) ) || ($CPDfound > 0) ))
						{
						if ($CPDfound < 1) 
							{
							if ($dialstatus =~ /BUSY/) {$VDL_status = 'AB'; $VDAC_status = 'BUSY';}
							if ($dialstatus =~ /CHANUNAVAIL/) {$VDL_status = 'ADC'; $VDAC_status = 'DISCONNECT';}
							}

						$stmtA = "UPDATE vicidial_list set status='$VDL_status' where lead_id = '$CIDlead_id';";
							if ($AGILOG) {$agi_string = "|$stmtA|";   &agi_output;}
						$VDADaffected_rows = $dbhA->do($stmtA);
						if ($AGILOG) {$agi_string = "--    VDAD vicidial_list update: |$VDADaffected_rows|$CIDlead_id";   &agi_output;}

						$stmtA = "UPDATE vicidial_auto_calls set status='$VDAC_status' where callerid = '$callerid';";
							if ($AGILOG) {$agi_string = "|$stmtA|";   &agi_output;}
						$VDACaffected_rows = $dbhA->do($stmtA);
						if ($AGILOG) {$agi_string = "--    VDAC update: |$VDACaffected_rows|$CIDlead_id";   &agi_output;}

							$Euniqueid=$uniqueid;
							$Euniqueid =~ s/\.\d+$//gi;
						$stmtA = "UPDATE vicidial_log FORCE INDEX(lead_id) set status='$VDL_status' where lead_id = '$CIDlead_id' and uniqueid LIKE \"$Euniqueid%\";";
							if ($AGILOG) {$agi_string = "|$stmtA|";   &agi_output;}
						$VDLaffected_rows = $dbhA->do($stmtA);
						if ($AGILOG) {$agi_string = "--    VDAD vicidial_log update: |$VDLaffected_rows|$uniqueid|$VDACuniqueid|";   &agi_output;}
						if ($VDLaffected_rows < 1)
							{
							$VD_alt_dial = 'NONE';
							$stmtA = "SELECT lead_id,callerid,campaign_id,alt_dial,stage,UNIX_TIMESTAMP(call_time),uniqueid,status,call_time,phone_code,phone_number,queue_position FROM vicidial_auto_calls where uniqueid = '$uniqueid' or callerid = '$callerid' limit 1;";
								if ($AGILOG) {$agi_string = "|$stmtA|";   &agi_output;}
							$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
							$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
							$sthArows=$sthA->rows;
							$rec_countCUSTDATA=0;
							if ($sthArows > 0)
								{
								@aryA = $sthA->fetchrow_array;
								$VD_lead_id	=			$aryA[0];
								$VD_callerid	=		$aryA[1];
								$VD_campaign_id	=		$aryA[2];
								$VD_alt_dial	=		$aryA[3];
								$VD_stage =				$aryA[4];
								$VD_start_epoch =		$aryA[5];
								$VD_uniqueid =			$aryA[6];
								$VD_status =			$aryA[7];
								$VD_call_time =			$aryA[8];
								$VD_phone_code =		$aryA[9];
								$VD_phone_number =		$aryA[10];
								$VD_queue_position =	$aryA[11];
								$rec_countCUSTDATA++;
								}
							$sthA->finish();

							if ($sthArows > 0)
								{
								$stmtA = "SELECT list_id FROM vicidial_list where lead_id='$VD_lead_id' limit 1;";
									if ($AGILOG) {$agi_string = "|$stmtA|";   &agi_output;}
								$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
								$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
								$sthArowsVLd=$sthA->rows;
								if ($sthArowsVLd > 0)
									{
									@aryA = $sthA->fetchrow_array;
									$VD_list_id	=			$aryA[0];
									}
								$sthA->finish();

								$stmtA = "INSERT INTO vicidial_log SET uniqueid='$uniqueid',lead_id='$VD_lead_id',list_id='$VD_list_id',status='$VDL_status',campaign_id='$VD_campaign_id',call_date='$VD_call_time',start_epoch='$VD_start_epoch',phone_code='$VD_phone_code',phone_number='$VD_phone_number',user='VDAD',processed='N',length_in_sec='0',end_epoch='$VD_start_epoch',alt_dial='$VD_alt_dial';";
									if ($AGILOG) {$agi_string = "|$stmtA|";   &agi_output;}
								$VDLIaffected_rows = $dbhA->do($stmtA);
								if ($AGILOG) {$agi_string = "--    VDAD vicidial_log insert: |$VDLIaffected_rows|$uniqueid|$CIDlead_id|$VDL_status|";   &agi_output;}

								$stmtA="INSERT IGNORE INTO vicidial_log_extended SET uniqueid='$uniqueid',server_ip='$VARserver_ip',call_date='$VD_call_time',lead_id='$VD_lead_id',caller_code='$VD_callerid',custom_call_id='' ON DUPLICATE KEY UPDATE server_ip='$VARserver_ip',call_date='$VD_call_time',lead_id='$VD_lead_id',caller_code='$VD_callerid';";
									if ($AGILOG) {$agi_string = "|$stmtA|";   &agi_output;}
								$VDLXaffected_rows = $dbhA->do($stmtA);
								if ($AGILOG) {$agi_string = "--    VDAD vicidial_extended_log insert: |$VDLXaffected_rows|$uniqueid|$CIDlead_id|$VDL_status|";   &agi_output;}
								}
							}
						if ( ($CPDfound > 0) && ($VDL_status !~ /AA/) )
							{
							$stmtA = "DELETE FROM vicidial_auto_calls where callerid = '$callerid';";
								if ($AGILOG) {$agi_string = "|$stmtA|";   &agi_output;}
							$VDACDELaffected_rows = $dbhA->do($stmtA);
							if ($AGILOG) {$agi_string = "--    CPD VDAC deleted: |$VDACDELaffected_rows|$callerid";   &agi_output;}
							}
						}
					else
						{
						if ($AGILOG) {$agi_string = "--    VD_hangup Local DEBUG: |$PRI|$callerid|$dialstatus|$hangup_cause|$CPDfound|$VDL_status|";   &agi_output;}
						}

					if ($AGILOG) {$agi_string = "+++++ VDAD START LOCAL CHANNEL: EXITING- $priority";   &agi_output;}
					if ($priority > 2) {sleep(1);}
					}
				else
					{
					########## FIND AND DELETE vicidial_auto_calls ##########
					$VD_alt_dial = 'NONE';
					$stmtA = "SELECT lead_id,callerid,campaign_id,alt_dial,stage,UNIX_TIMESTAMP(call_time),uniqueid,status,call_time,phone_code,phone_number,queue_position FROM vicidial_auto_calls where uniqueid = '$uniqueid' or callerid = '$callerid' limit 1;";
						if ($AGILOG) {$agi_string = "|$stmtA|";   &agi_output;}
					$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
					$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
					$sthArows=$sthA->rows;
					$rec_countCUSTDATA=0;
					if ($sthArows > 0)
						{
						@aryA = $sthA->fetchrow_array;
						$VD_lead_id	=			$aryA[0];
						$VD_callerid	=		$aryA[1];
						$VD_campaign_id	=		$aryA[2];
						$VD_alt_dial	=		$aryA[3];
						$VD_stage =				$aryA[4];
						$VD_start_epoch =		$aryA[5];
						$VD_uniqueid =			$aryA[6];
						$VD_status =			$aryA[7];
						$VD_call_time =			$aryA[8];
						$VD_phone_code =		$aryA[9];
						$VD_phone_number =		$aryA[10];
						$VD_queue_position =	$aryA[11];
						$rec_countCUSTDATA++;
						}
					$sthA->finish();

					if (!$rec_countCUSTDATA)
						{
						if ($AGILOG) {$agi_string = "VD hangup: no VDAC record found: $uniqueid $calleridname";   &agi_output;}
						}
					else
						{
						$PC_count=0;
						$PLC_count=0;
						$stmtA = "SELECT channel from parked_channels where channel_group='$callerid';";
							if ($AGILOG) {$agi_string = "|$stmtA|";   &agi_output;}
						$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
						$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
						$PC_count_rows=$sthA->rows;
						if ($PC_count_rows > 0)
							{
							@aryA = $sthA->fetchrow_array;
							$PC_channel = $aryA[0];
							}
						$sthA->finish();

						if ($PC_count_rows > 0)
							{
							sleep(1);

							$stmtA = "SELECT count(*) from live_channels where channel='$PC_channel';";
								if ($AGILOG) {$agi_string = "|$stmtA|";   &agi_output;}
							$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
							$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
							$PLC_count_rows=$sthA->rows;
							if ($PLC_count_rows > 0)
								{
								@aryA = $sthA->fetchrow_array;
								$PLC_count = $aryA[0];
								}
							$sthA->finish();
							}
						if ($PLC_count > 0)
							{
							if ($AGILOG) {$agi_string = "VD hangup: VDAC record found with park record: $channel $PC_channel $uniqueid $calleridname";   &agi_output;}
							}
						else
							{
							$stmtA = "DELETE FROM vicidial_auto_calls where ( ( (status!='IVR') and (uniqueid='$uniqueid' or callerid = '$callerid') ) or ( (status='IVR') and (uniqueid='$uniqueid') ) ) order by call_time desc limit 1;";
							$VACaffected_rows = $dbhA->do($stmtA);
							if ($AGILOG) {$agi_string = "--    VDAC record deleted: |$VACaffected_rows|   |$VD_lead_id|$uniqueid|$VD_uniqueid|$VD_callerid|$VARserver_ip|$VD_status|";   &agi_output;}

							$stmtA = "UPDATE vicidial_live_agents SET ring_callerid='' where ring_callerid='$callerid';";
							$VLACaffected_rows = $dbhA->do($stmtA);

							#############################################
							##### START QUEUEMETRICS LOGGING LOOKUP #####
							$stmtA = "SELECT enable_queuemetrics_logging,queuemetrics_server_ip,queuemetrics_dbname,queuemetrics_login,queuemetrics_pass,queuemetrics_log_id FROM system_settings;";
							$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
							$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
							$sthArows=$sthA->rows;
							if ($sthArows > 0)
								{
								@aryA = $sthA->fetchrow_array;
								$enable_queuemetrics_logging =	$aryA[0];
								$queuemetrics_server_ip	=		$aryA[1];
								$queuemetrics_dbname =			$aryA[2];
								$queuemetrics_login=			$aryA[3];
								$queuemetrics_pass =			$aryA[4];
								$queuemetrics_log_id =			$aryA[5];
								}
							$sthA->finish();
							##### END QUEUEMETRICS LOGGING LOOKUP #####
							###########################################
							if ( ($enable_queuemetrics_logging > 0) && ($VD_status !~ /IVR/) )
								{
								$data_four='';
								$VD_agent='NONE';
								$secX = time();
								$VD_call_length = ($secX - $VD_start_epoch);
								$VD_stage =~ s/.*-//gi;
								if ($VD_stage < 0.25) {$VD_stage=0;}

								$dbhB = DBI->connect("DBI:mysql:$queuemetrics_dbname:$queuemetrics_server_ip:3306", "$queuemetrics_login", "$queuemetrics_pass")
								 or die "Couldn't connect to database: " . DBI->errstr;

								if ($DBX) {print "CONNECTED TO DATABASE:  $queuemetrics_server_ip|$queuemetrics_dbname\n";}

								$secX = time();
								$Rtarget = ($secX - 21600);	# look for VDCL entry within last 6 hours
								($Rsec,$Rmin,$Rhour,$Rmday,$Rmon,$Ryear,$Rwday,$Ryday,$Risdst) = localtime($Rtarget);
								$Ryear = ($Ryear + 1900);
								$Rmon++;
								if ($Rmon < 10) {$Rmon = "0$Rmon";}
								if ($Rmday < 10) {$Rmday = "0$Rmday";}
								if ($Rhour < 10) {$Rhour = "0$Rhour";}
								if ($Rmin < 10) {$Rmin = "0$Rmin";}
								if ($Rsec < 10) {$Rsec = "0$Rsec";}
									$RSQLdate = "$Ryear-$Rmon-$Rmday $Rhour:$Rmin:$Rsec";

								### find original queue position of the call
								$queue_position=1;
								$stmtA = "SELECT queue_position,call_date FROM vicidial_closer_log where uniqueid='$unique_id' and lead_id='$CIDlead_id' and campaign_id='$VD_campaign_id' and call_date > \"$RSQLdate\" order by closecallid desc limit 1;";
								$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
								$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
								$sthArows=$sthA->rows;
								if ($sthArows > 0)
									{
									@aryA = $sthA->fetchrow_array;
									$queue_position =	$aryA[0];
									$VCLcall_date =		$aryA[1];
									}
								$sthA->finish();

								$stmtB = "SELECT agent,data4 from queue_log where call_id='$VD_callerid' and verb='CONNECT' order by time_id desc limit 1;";
								$sthB = $dbhB->prepare($stmtB) or die "preparing: ",$dbhB->errstr;
								$sthB->execute or die "executing: $stmtB ", $dbhB->errstr;
								$sthBrows=$sthB->rows;
								$rec_count=0;
								if ($sthBrows > 0)
									{
									@aryB = $sthB->fetchrow_array;
									$VD_agent =		$aryB[0];
									$data_four =	$aryB[1];
									$rec_count++;
									}
								$sthB->finish();
								if ($AGILOG) {$agi_string = "$VD_agent|$data_four|$stmtB|";   &agi_output;}

								if ($rec_count < 1)
									{
									### find current number of calls in this queue to find position when channel hung up
									$current_position=1;
									$stmtA = "SELECT count(*) FROM vicidial_auto_calls where status = 'LIVE' and campaign_id='$VD_campaign_id' and call_time < '$VCLcall_date';";
									$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
									$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
									$sthArows=$sthA->rows;
									if ($sthArows > 0)
										{
										@aryA = $sthA->fetchrow_array;
										$current_position =	($aryA[0] + 1);
										}
									$sthA->finish();

									$stmtB = "INSERT INTO queue_log SET partition='P01',time_id='$secX',call_id='$VD_callerid',queue='$VD_campaign_id',agent='$VD_agent',verb='ABANDON',data1='$current_position',data2='$queue_position',data3='$VD_stage',serverid='$queuemetrics_log_id',data4='$data_four';";
									$Baffected_rows = $dbhB->do($stmtB);
									}
								else
									{
									$stmtB = "INSERT INTO queue_log SET partition='P01',time_id='$secX',call_id='$VD_callerid',queue='$VD_campaign_id',agent='$VD_agent',verb='COMPLETECALLER',data1='$VD_stage',data2='$VD_call_length',data3='$queue_position',serverid='$queuemetrics_log_id',data4='$data_four';";
									$Baffected_rows = $dbhB->do($stmtB);
									}

								if ($AGILOG) {$agi_string = "|$stmtB|";   &agi_output;}

								$dbhB->disconnect();
								}


							$epc_countCUSTDATA=0;
							$VD_closecallid='';
							$VDL_update=0;
							$Euniqueid=$uniqueid;
							$Euniqueid =~ s/\.\d+$//gi;

							if ($calleridname !~ /^Y\d\d\d\d/)
								{
								########## FIND AND UPDATE vicidial_log ##########
								$stmtA = "SELECT start_epoch,status,user,term_reason,comments FROM vicidial_log FORCE INDEX(lead_id) where lead_id = '$VD_lead_id' and uniqueid LIKE \"$Euniqueid%\" limit 1;";
									if ($AGILOG) {$agi_string = "|$stmtA|";   &agi_output;}
								$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
								$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
								$sthArows=$sthA->rows;
								if ($sthArows > 0)
									{
									@aryA = $sthA->fetchrow_array;
									$VD_start_epoch	=	$aryA[0];
									$VD_status =		$aryA[1];
									$VD_user =			$aryA[2];
									$VD_term_reason =	$aryA[3];
									$VD_comments =		$aryA[4];
									$epc_countCUSTDATA++;
									}
								$sthA->finish();
								}

							if ( (!$epc_countCUSTDATA) || ($calleridname =~ /^Y\d\d\d\d/) )
								{
								if ($AGILOG) {$agi_string = "no VDL record found: $uniqueid $calleridname $VD_lead_id $uniqueid $VD_uniqueid";   &agi_output;}

								$secX = time();
								$Rtarget = ($secX - 21600);	# look for VDCL entry within last 6 hours
								($Rsec,$Rmin,$Rhour,$Rmday,$Rmon,$Ryear,$Rwday,$Ryday,$Risdst) = localtime($Rtarget);
								$Ryear = ($Ryear + 1900);
								$Rmon++;
								if ($Rmon < 10) {$Rmon = "0$Rmon";}
								if ($Rmday < 10) {$Rmday = "0$Rmday";}
								if ($Rhour < 10) {$Rhour = "0$Rhour";}
								if ($Rmin < 10) {$Rmin = "0$Rmin";}
								if ($Rsec < 10) {$Rsec = "0$Rsec";}
									$RSQLdate = "$Ryear-$Rmon-$Rmday $Rhour:$Rmin:$Rsec";

								$stmtA = "SELECT start_epoch,status,closecallid,user,term_reason,length_in_sec,queue_seconds,comments FROM vicidial_closer_log where lead_id = '$VD_lead_id' and call_date > \"$RSQLdate\" order by call_date desc limit 1;";
									if ($AGILOG) {$agi_string = "|$stmtA|";   &agi_output;}
								$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
								$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
								$sthArows=$sthA->rows;
								 $epc_countCUSTDATA=0;
								 $VD_closecallid='';
								if ($sthArows > 0)
									{
									@aryA = $sthA->fetchrow_array;
									$VD_start_epoch	=	$aryA[0];
									$VD_status =		$aryA[1];
									$VD_closecallid	=	$aryA[2];
									$VD_user =			$aryA[3];
									$VD_term_reason =	$aryA[4];
									$VD_length_in_sec =	$aryA[5];
									$VD_queue_seconds =	$aryA[6];
									$VD_comments =		$aryA[7];
									 $epc_countCUSTDATA++;
									}
								$sthA->finish();
								}
							if (!$epc_countCUSTDATA)
								{
								if ($AGILOG) {$agi_string = "no VDL or VDCL record found: $uniqueid $calleridname $VD_lead_id $uniqueid $VD_uniqueid |$VACaffected_rows|$VD_callerid|";   &agi_output;}

								### BEGIN if call answers but has not reached routing AGI, then log as a PDROP
								if ( ($callerid =~ /^V\d\d\d\d\d\d\d\d\d\d\d\d\d\d\d\d\d\d\d/) && ($VD_status =~ /SENT/) )
									{
									$stmtA = "SELECT list_id FROM vicidial_list where lead_id='$VD_lead_id' limit 1;";
										if ($AGILOG) {$agi_string = "|$stmtA|";   &agi_output;}
									$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
									$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
									$sthArowsVLd=$sthA->rows;
									if ($sthArowsVLd > 0)
										{
										@aryA = $sthA->fetchrow_array;
										$VD_list_id	=			$aryA[0];
										}
									$sthA->finish();

									$stmtA = "UPDATE vicidial_list SET status='PDROP' where lead_id='$VD_lead_id';";
									$VLPDaffected_rows = $dbhA->do($stmtA);
									if ($AGILOG) {$agi_string = "--    PDROP vicidial_list update: |$VLPDaffected_rows|$uniqueid|$CIDlead_id|$VDL_status|";   &agi_output;}

									$stmtA = "INSERT INTO vicidial_log SET uniqueid='$uniqueid',lead_id='$VD_lead_id',list_id='$VD_list_id',status='PDROP',campaign_id='$VD_campaign_id',call_date='$VD_call_time',start_epoch='$VD_start_epoch',phone_code='$VD_phone_code',phone_number='$VD_phone_number',user='VDAD',processed='N',length_in_sec='0',end_epoch='$VD_start_epoch',alt_dial='$VD_alt_dial';";
									$VDLPDaffected_rows = $dbhA->do($stmtA);
									if ($AGILOG) {$agi_string = "--    PDROP vicidial_log insert: |$VDLPDaffected_rows|$uniqueid|$CIDlead_id|$VDL_status|";   &agi_output;}

									$stmtA="INSERT IGNORE INTO vicidial_log_extended SET uniqueid='$uniqueid',server_ip='$VARserver_ip',call_date='$VD_call_time',lead_id='$VD_lead_id',caller_code='$VD_callerid',custom_call_id='' ON DUPLICATE KEY UPDATE server_ip='$VARserver_ip',call_date='$VD_call_time',lead_id='$VD_lead_id',caller_code='$VD_callerid';";
									$VDLXPDaffected_rows = $dbhA->do($stmtA);
									if ($AGILOG) {$agi_string = "--    PDROP vicidial_extended_log insert: |$VDLXPDaffected_rows|$uniqueid|$CIDlead_id|$VDL_status|";   &agi_output;}
									}
								### END if call answers but has not reached routing AGI, then log as a PDROP
								}
							else
								{
								$VD_seconds = ($now_date_epoch - $VD_start_epoch);

								$SQL_status='';
								if ( ($VD_status =~ /^NA$|^NEW$|^QUEUE$|^XFER$/) && ($VD_comments !~ /REMOTE/) )
									{
									if ( ($VD_term_reason !~ /AGENT|CALLER|QUEUETIMEOUT/) && ( ($VD_user =~ /VDAD|VDCL/) || (length($VD_user) < 1) ) )
										{$VDLSQL_term_reason = "term_reason='ABANDON',";}
									else
										{
										if ($VD_term_reason !~ /AGENT|CALLER|QUEUETIMEOUT/)
											{$VDLSQL_term_reason = "term_reason='CALLER',";}
										else
											{$VDLSQL_term_reason = '';}
										}
									$SQL_status = "status='DROP',$VDLSQL_term_reason";

									########## FIND AND UPDATE vicidial_list ##########
									$stmtA = "UPDATE vicidial_list set status='DROP' where lead_id = '$VD_lead_id';";
										if ($AGILOG) {$agi_string = "|$stmtA|";   &agi_output;}
									$affected_rows = $dbhA->do($stmtA);
									if ($AGILOG) {$agi_string = "--    VDAD vicidial_list update: |$affected_rows|$VD_lead_id";   &agi_output;}
									}
								else 
									{
									$SQL_status = "term_reason='CALLER',";
									}

								if ($calleridname !~ /^Y\d\d\d\d/)
									{
									$VDL_update=1;
									$stmtA = "UPDATE vicidial_log FORCE INDEX(lead_id) set $SQL_status end_epoch='$now_date_epoch',length_in_sec='$VD_seconds' where lead_id = '$VD_lead_id' and uniqueid LIKE \"$Euniqueid%\";";
										if ($AGILOG) {$agi_string = "|$stmtA|";   &agi_output;}
									$VLaffected_rows = $dbhA->do($stmtA);
									if ($AGILOG) {$agi_string = "--    VDAD vicidial_log update: |$VLaffected_rows|$uniqueid|$VD_status|";   &agi_output;}
									}



								########## UPDATE vicidial_closer_log ##########
								if ( (length($VD_closecallid) < 1) || ($VDL_update > 0) )
									{
									if ($AGILOG) {$agi_string = "no VDCL record found: $uniqueid|$calleridname|$VD_lead_id|$uniqueid|$VD_uniqueid|$VDL_update|";   &agi_output;}
									}
								else
									{
									if ($VD_status =~ /^DONE$|^INCALL$|^XFER$/)
										{$VDCLSQL_update = "term_reason='CALLER',";}
									else
										{
										if ( ($VD_term_reason !~ /AGENT|CALLER|QUEUETIMEOUT|AFTERHOURS|HOLDRECALLXFER|HOLDTIME|NOAGENT/) && ( ($VD_user =~ /VDAD|VDCL/) || (length($VD_user) < 1) ) )
											{$VDCLSQL_term_reason = "term_reason='ABANDON',";}
										else
											{
											if ($VD_term_reason !~ /AGENT|CALLER|QUEUETIMEOUT|AFTERHOURS|HOLDRECALLXFER|HOLDTIME|NOAGENT/)
												{$VDCLSQL_term_reason = "term_reason='CALLER',";}
											else
												{$VDCLSQL_term_reason = '';}
											}
										if ($VD_status =~ /QUEUE/)
											{
											$VDCLSQL_status = "status='DROP',";
											$VDCLSQL_queue_seconds = "queue_seconds='$VD_seconds',";
											}
										else
											{
											$VDCLSQL_status = '';
											$VDCLSQL_queue_seconds = '';
											}

										$VDCLSQL_update = "$VDCLSQL_status$VDCLSQL_term_reason$VDCLSQL_queue_seconds";
										}

									$VD_seconds = ($now_date_epoch - $VD_start_epoch);
									$stmtA = "UPDATE vicidial_closer_log set $VDCLSQL_update end_epoch='$now_date_epoch',length_in_sec='$VD_seconds' where closecallid = '$VD_closecallid';";
										if ($AGILOG) {$agi_string = "|$VDCLSQL_update|$VD_status|$VD_length_in_sec|$VD_term_reason|$VD_queue_seconds|\n|$stmtA|";   &agi_output;}
									$affected_rows = $dbhA->do($stmtA);
									if ($AGILOG) {$agi_string = "--    VDCL update: |$affected_rows|$uniqueid|$VD_closecallid|";   &agi_output;}

									}
								}

							##### BEGIN AUTO ALT PHONE DIAL SECTION #####
							### check to see if campaign has alt_dial enabled
							$VD_auto_alt_dial = 'NONE';
							$VD_auto_alt_dial_statuses='';
							$stmtA="SELECT auto_alt_dial,auto_alt_dial_statuses,use_internal_dnc,use_campaign_dnc FROM vicidial_campaigns where campaign_id='$VD_campaign_id';";
								if ($AGILOG) {$agi_string = "|$stmtA|";   &agi_output;}
							$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
							$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
							$sthArows=$sthA->rows;
							 $epc_countCAMPDATA=0;
							while ($sthArows > $epc_countCAMPDATA)
								{
								@aryA = $sthA->fetchrow_array;
								$VD_auto_alt_dial	=			$aryA[0];
								$VD_auto_alt_dial_statuses	=	$aryA[1];
								$VD_use_internal_dnc =			$aryA[2];
								$VD_use_campaign_dnc =			$aryA[3];
								$epc_countCAMPDATA++;
								}
							$sthA->finish();
							if ($VD_auto_alt_dial_statuses =~ / $VD_status | $VDL_status /)
								{
								if ( ($VD_auto_alt_dial =~ /(ALT_ONLY|ALT_AND_ADDR3|ALT_AND_EXTENDED)/) && ($VD_alt_dial =~ /NONE|MAIN/) )
									{
									$alt_dial_skip=0;
									$VD_alt_phone='';
									$stmtA="SELECT alt_phone,gmt_offset_now,state,list_id FROM vicidial_list where lead_id='$VD_lead_id';";
										if ($AGILOG) {$agi_string = "|$stmtA|";   &agi_output;}
									$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
									$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
									$sthArows=$sthA->rows;
									$epc_countCAMPDATA=0;
									while ($sthArows > $epc_countCAMPDATA)
										{
										@aryA = $sthA->fetchrow_array;
										$VD_alt_phone =			$aryA[0];
										$VD_alt_phone =~ s/\D//gi;
										$VD_gmt_offset_now =	$aryA[1];
										$VD_state =				$aryA[2];
										$VD_list_id =			$aryA[3];
										$epc_countCAMPDATA++;
										}
									$sthA->finish();
									if (length($VD_alt_phone)>5)
										{
										if ( ($VD_use_internal_dnc =~ /Y/) || ($VD_use_internal_dnc =~ /AREACODE/) )
											{
											if ($VD_use_internal_dnc =~ /AREACODE/)
												{
												$alt_areacode = substr($VD_alt_phone, 0, 3);
												$alt_areacode .= "XXXXXXX";
												$stmtA="SELECT count(*) FROM vicidial_dnc where phone_number IN('$VD_alt_phone','$alt_areacode');";
												}
											else
												{$stmtA="SELECT count(*) FROM vicidial_dnc where phone_number='$VD_alt_phone';";}
												if ($AGILOG) {$agi_string = "|$stmtA|";   &agi_output;}
											$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
											$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
											$sthArows=$sthA->rows;
											if ($sthArows > 0)
												{
												@aryA = $sthA->fetchrow_array;
												$VD_alt_dnc_count =	$aryA[0];
												}
											$sthA->finish();
											}
										else {$VD_alt_dnc_count=0;}
										if ( ($VD_use_campaign_dnc =~ /Y/) || ($VD_use_campaign_dnc =~ /AREACODE/) )
											{
											if ($VD_use_campaign_dnc =~ /AREACODE/)
												{
												$alt_areacode = substr($VD_alt_phone, 0, 3);
												$alt_areacode .= "XXXXXXX";
												$stmtA="SELECT count(*) FROM vicidial_campaign_dnc where phone_number IN('$VD_alt_phone','$alt_areacode') and campaign_id='$VD_campaign_id';";
												}
											else
												{$stmtA="SELECT count(*) FROM vicidial_campaign_dnc where phone_number='$VD_alt_phone' and campaign_id='$VD_campaign_id';";}
												if ($AGILOG) {$agi_string = "|$stmtA|";   &agi_output;}
											$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
											$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
											$sthArows=$sthA->rows;
											if ($sthArows > 0)
												{
												@aryA = $sthA->fetchrow_array;
												$VD_alt_dnc_count =	($VD_alt_dnc_count + $aryA[0]);
												}
											$sthA->finish();
											}
										if ($VD_alt_dnc_count < 1)
											{
											$stmtA = "INSERT INTO vicidial_hopper SET lead_id='$VD_lead_id',campaign_id='$VD_campaign_id',status='READY',list_id='$VD_list_id',gmt_offset_now='$VD_gmt_offset_now',state='$VD_state',alt_dial='ALT',user='',priority='25',source='A';";
											$affected_rows = $dbhA->do($stmtA);
											if ($AGILOG) {$agi_string = "--    VDH record inserted: |$affected_rows|   |$stmtA|";   &agi_output;}
											}
										else
											{$alt_dial_skip=1;}
										}
									else
										{$alt_dial_skip=1;}
									if ($alt_dial_skip > 0)
										{$VD_alt_dial='ALT';}
									}
								if ( ( ($VD_auto_alt_dial =~ /(ADDR3_ONLY)/) && ($VD_alt_dial =~ /NONE|MAIN/) ) || ( ($VD_auto_alt_dial =~ /(ALT_AND_ADDR3)/) && ($VD_alt_dial =~ /ALT/) ) )
									{
									$addr3_dial_skip=0;
									$VD_address3='';
									$stmtA="SELECT address3,gmt_offset_now,state,list_id FROM vicidial_list where lead_id='$VD_lead_id';";
										if ($AGILOG) {$agi_string = "|$stmtA|";   &agi_output;}
									$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
									$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
									$sthArows=$sthA->rows;
									 $epc_countCAMPDATA=0;
									while ($sthArows > $epc_countCAMPDATA)
										{
										@aryA = $sthA->fetchrow_array;
										$VD_address3 =			$aryA[0];
										$VD_address3 =~ s/\D//gi;
										$VD_gmt_offset_now =	$aryA[1];
										$VD_state =				$aryA[2];
										$VD_list_id =			$aryA[3];
										$epc_countCAMPDATA++;
										}
									$sthA->finish();
									if (length($VD_address3)>5)
										{
										if ( ($VD_use_internal_dnc =~ /Y/) || ($VD_use_internal_dnc =~ /AREACODE/) )
											{
											if ($VD_use_internal_dnc =~ /AREACODE/)
												{
												$addr3_areacode = substr($VD_address3, 0, 3);
												$addr3_areacode .= "XXXXXXX";
												$stmtA="SELECT count(*) FROM vicidial_dnc where phone_number IN('$VD_address3','$addr3_areacode');";
												}
											else
												{$stmtA="SELECT count(*) FROM vicidial_dnc where phone_number='$VD_address3';";}
												if ($AGILOG) {$agi_string = "|$stmtA|";   &agi_output;}
											$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
											$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
											$sthArows=$sthA->rows;
											if ($sthArows > 0)
												{
												@aryA = $sthA->fetchrow_array;
												$VD_alt_dnc_count =	$aryA[0];
												}
											$sthA->finish();
											}
										else {$VD_alt_dnc_count=0;}
										if ( ($VD_use_campaign_dnc =~ /Y/) || ($VD_use_campaign_dnc =~ /AREACODE/) )
											{
											if ($VD_use_campaign_dnc =~ /AREACODE/)
												{
												$addr3_areacode = substr($VD_address3, 0, 3);
												$addr3_areacode .= "XXXXXXX";
												$stmtA="SELECT count(*) FROM vicidial_campaign_dnc where phone_number IN('$VD_address3','$alt_areacode') and campaign_id='$VD_campaign_id';";
												}
											else
												{$stmtA="SELECT count(*) FROM vicidial_campaign_dnc where phone_number='$VD_address3' and campaign_id='$VD_campaign_id';";}
												if ($AGILOG) {$agi_string = "|$stmtA|";   &agi_output;}
											$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
											$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
											$sthArows=$sthA->rows;
											if ($sthArows > 0)
												{
												@aryA = $sthA->fetchrow_array;
												$VD_alt_dnc_count =	($VD_alt_dnc_count + $aryA[0]);
												}
											$sthA->finish();
											}
										if ($VD_alt_dnc_count < 1)
											{
											$stmtA = "INSERT INTO vicidial_hopper SET lead_id='$VD_lead_id',campaign_id='$VD_campaign_id',status='READY',list_id='$VD_list_id',gmt_offset_now='$VD_gmt_offset_now',state='$VD_state',alt_dial='ADDR3',user='',priority='20',source='A';";
											$affected_rows = $dbhA->do($stmtA);
											if ($AGILOG) {$agi_string = "--    VDH record inserted: |$affected_rows|   |$stmtA|";   &agi_output;}
											}
										else
											{$addr3_dial_skip=1;}
										}
									else
										{$addr3_dial_skip=1;}
									if ($addr3_dial_skip > 0)
										{$VD_alt_dial='ADDR3';}
									}
								if ( ( ($VD_auto_alt_dial =~ /(EXTENDED_ONLY)/) && ($VD_alt_dial =~ /NONE|MAIN/) ) || ( ($VD_auto_alt_dial =~ /(ALT_AND_EXTENDED)/) && ($VD_alt_dial =~ /ALT/) ) || ( ($VD_auto_alt_dial =~ /ADDR3_AND_EXTENDED|ALT_AND_ADDR3_AND_EXTENDED/) && ($VD_alt_dial =~ /ADDR3/) ) || ( ($VD_auto_alt_dial =~ /(EXTENDED)/) && ($VD_alt_dial =~ /X/) && ($VD_alt_dial !~ /XLAST/) ) )
									{
									if ($VD_alt_dial =~ /ADDR3/) {$Xlast=0;}
									else
										{$Xlast = $VD_alt_dial;}
									$Xlast =~ s/\D//gi;
									if (length($Xlast)<1)
										{$Xlast=0;}
									$VD_altdialx='';
									$stmtA="SELECT gmt_offset_now,state,list_id FROM vicidial_list where lead_id='$VD_lead_id';";
										if ($AGILOG) {$agi_string = "|$stmtA|";   &agi_output;}
									$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
									$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
									$sthArows=$sthA->rows;
									$epc_countCAMPDATA=0;
									while ($sthArows > $epc_countCAMPDATA)
										{
										@aryA = $sthA->fetchrow_array;
										$VD_gmt_offset_now =	$aryA[0];
										$VD_state =				$aryA[1];
										$VD_list_id =			$aryA[2];
										$epc_countCAMPDATA++;
										}
									$sthA->finish();
									$alt_dial_phones_count=0;
									$stmtA="SELECT count(*) FROM vicidial_list_alt_phones where lead_id='$VD_lead_id';";
										if ($AGILOG) {$agi_string = "|$stmtA|";   &agi_output;}
									$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
									$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
									$sthArows=$sthA->rows;
									if ($sthArows > 0)
										{
										@aryA = $sthA->fetchrow_array;
										$alt_dial_phones_count = $aryA[0];
										}
									$sthA->finish();

									while ( ($alt_dial_phones_count > 0) && ($alt_dial_phones_count > $Xlast) )
										{
										$Xlast++;
										$stmtA="SELECT alt_phone_id,phone_number,active FROM vicidial_list_alt_phones where lead_id='$VD_lead_id' and alt_phone_count='$Xlast';";
											if ($AGILOG) {$agi_string = "|$stmtA|";   &agi_output;}
										$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
										$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
										$sthArows=$sthA->rows;
										if ($sthArows > 0)
											{
											@aryA = $sthA->fetchrow_array;
											$VD_altdial_id =		$aryA[0];
											$VD_altdial_phone = 	$aryA[1];
											$VD_altdial_active = 	$aryA[2];
											}
										else
											{$Xlast=9999999999;}
										$sthA->finish();

										if ($VD_altdial_active =~ /Y/)
											{
											$DNCC=0;
											$DNCL=0;
											if ( ($VD_use_internal_dnc =~ /Y/) || ($VD_use_internal_dnc =~ /AREACODE/) )
												{
												if ($VD_use_internal_dnc =~ /AREACODE/)
													{
													$ad_areacode = substr($VD_altdial_phone, 0, 3);
													$ad_areacode .= "XXXXXXX";
													$stmtA="SELECT count(*) FROM vicidial_dnc where phone_number IN('$VD_altdial_phone','$ad_areacode');";
													}
												else
													{$stmtA="SELECT count(*) FROM vicidial_dnc where phone_number='$VD_altdial_phone';";}
													if ($AGILOG) {$agi_string = "|$stmtA|";   &agi_output;}
												$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
												$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
												$sthArows=$sthA->rows;
												if ($sthArows > 0)
													{
													@aryA = $sthA->fetchrow_array;
													$VD_alt_dnc_count =		$aryA[0];
													$DNCL =					$aryA[0];
													}
												$sthA->finish();
												}
											else {$VD_alt_dnc_count=0;}
											if ( ($VD_use_campaign_dnc =~ /Y/) || ($VD_use_campaign_dnc =~ /AREACODE/) )
												{
												if ($VD_use_campaign_dnc =~ /AREACODE/)
													{
													$ap_areacode = substr($VD_altdial_phone, 0, 3);
													$ap_areacode .= "XXXXXXX";
													$stmtA="SELECT count(*) FROM vicidial_campaign_dnc where phone_number IN('$VD_altdial_phone','$ap_areacode') and campaign_id='$VD_campaign_id';";
													}
												else
													{$stmtA="SELECT count(*) FROM vicidial_campaign_dnc where phone_number='$VD_altdial_phone' and campaign_id='$VD_campaign_id';";}
													if ($AGILOG) {$agi_string = "|$stmtA|";   &agi_output;}
												$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
												$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
												$sthArows=$sthA->rows;
												if ($sthArows > 0)
													{
													@aryA = $sthA->fetchrow_array;
													$VD_alt_dnc_count =	($VD_alt_dnc_count + $aryA[0]);
													$DNCC =					$aryA[0];
													}
												$sthA->finish();
												}
											if ($VD_alt_dnc_count < 1)
												{
												if ($alt_dial_phones_count eq '$Xlast') 
													{$Xlast = 'LAST';}
												$stmtA = "INSERT INTO vicidial_hopper SET lead_id='$VD_lead_id',campaign_id='$VD_campaign_id',status='READY',list_id='$VD_list_id',gmt_offset_now='$VD_gmt_offset_now',state='$VD_state',alt_dial='X$Xlast',user='',priority='15',source='A';";
												$affected_rows = $dbhA->do($stmtA);
												if ($AGILOG) {$agi_string = "--    VDH record inserted: |$affected_rows|   |$stmtA|X$Xlast|$VD_altdial_id|";   &agi_output;}
												$Xlast=9999999999;
												}
											else
												{
												if ( ( ($VD_auto_alt_dial_statuses =~ / DNCC /) && ($DNCC > 0) ) || ( ($VD_auto_alt_dial_statuses =~ / DNCL /) && ($DNCL > 0) ) )
													{
													if ($alt_dial_phones_count eq '$Xlast') 
														{$Xlast = 'LAST';}
													$stmtA = "INSERT INTO vicidial_hopper SET lead_id='$VD_lead_id',campaign_id='$VD_campaign_id',status='DNC',list_id='$VD_list_id',gmt_offset_now='$VD_gmt_offset_now',state='$VD_state',alt_dial='X$Xlast',user='',priority='15',source='A';";
													$affected_rows = $dbhA->do($stmtA);
													if ($AGILOG) {$agi_string = "--    VDH record DNC inserted: |$affected_rows|   |$stmtA|X$Xlast|$VD_altdial_id|";   &agi_output;}
													$Xlast=9999999999;
													if ($AGILOG) {$agi_string = "--    VDH alt dial inserting DNC|X$Xlast|$VD_altdial_phone|";   &agi_output;}
													}
												else
													{
													if ($AGILOG) {$agi_string = "--    VDH alt dial not-inserting DNC|X$Xlast|$VD_altdial_phone|";   &agi_output;}
													}
												}
											}
										}
									}
								}
							##### END AUTO ALT PHONE DIAL SECTION #####
							}
						}
					}
				}
			$dbhA->disconnect();
			}
		}
	###################################################################
	##### END call_log process ########################################
	###################################################################





	###################################################################
	##### START VD_hangup process #####################################
	###################################################################
	if ($process =~ /^VD_hangup/)
	{
	$nothing=0;
	}
	###################################################################
	##### END VD_hangup process #######################################
	###################################################################


	}


VDfastAGI->run(
				port=>4577,
				user=>'root',
				group=>'root',
				min_servers=>$VARfastagi_log_min_servers,
				max_servers=>$VARfastagi_log_max_servers,
				min_spare_servers=>$VARfastagi_log_min_spare_servers,
				max_spare_servers=>$VARfastagi_log_max_spare_servers,
				max_requests=>$VARfastagi_log_max_requests,
				check_for_dead=>$VARfastagi_log_checkfordead,
				check_for_waiting=>$VARfastagi_log_checkforwait,
				log_file=>$childLOGfile,
				log_level=>$log_level
				);
exit;




sub agi_output
	{
	if ($AGILOG >=2)
		{
		### open the log file for writing ###
		open(Lout, ">>$AGILOGfile")
				|| die "Can't open $AGILOGfile: $!\n";
		print Lout "$now_date|$script|$process|$agi_string\n";
		close(Lout);
		}
		### send to STDERR writing ###
	if ( ($AGILOG == '1') || ($AGILOG == '3') )
		{
		print STDERR "$now_date|$script|$process|$agi_string\n";
		}
	$agi_string='';
	}
