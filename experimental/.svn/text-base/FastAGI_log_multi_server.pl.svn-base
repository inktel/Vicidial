#!/usr/bin/perl
#
# FastAGI_log_multi_server.pl version 2.2.0   *DBI-version*
# 
# Experimental Deamon using perl Net::Server that runs as FastAGI to reduce load
# replaces the following AGI scripts:
# - call_log.agi
# - call_logCID.agi
# - VD_hangup.agi
#
# This script needs to be running all of the time for AGI requests to work
# 
# You need to put lines similar to those below in your extensions.conf file if the FastAGI is running on the same server as Asterisk:
# 
# ;outbound dialing:
# exten => _91NXXNXXXXXX,1,AGI(agi://127.0.0.1:4577/call_log) 
#
# ;inbound calls:
# exten => 101,1,AGI(agi://127.0.0.1:4577/call_log)
#   or
# exten => 101,1,AGI(agi://127.0.0.1:4577/call_log--fullCID--${EXTEN}-----${CALLERID}-----${CALLERIDNUM}-----${CALLERIDNAME})
# 
# ;all hangups:
# exten => h,1,DeadAGI(agi://127.0.0.1:4577/call_log--HVcauses--PRI-----NODEBUG-----${HANGUPCAUSE}-----${DIALSTATUS}-----${DIALEDTIME}-----${ANSWEREDTIME})
# 
#
# If the FastAGI server will be handling multiple Asterisk servers you need to put the following in each Asterisk servers extensions.conf,
# replacing "10.10.10.15" with ip address of the FastAGI server:
#
# ;outbound dialing:
# exten => _91NXXNXXXXXX,1,AGI(agi://10.10.10.15:4577/call_log) 
#
# ;inbound calls:
# exten => 101,1,AGI(agi://10.10.10.15:4577/call_log)
#   or
# exten => 101,1,AGI(agi://10.10.10.15:4577/call_log--fullCID--${EXTEN}-----${CALLERID}-----${CALLERIDNUM}-----${CALLERIDNAME})
# 
# ;all hangups:
# exten => h,1,DeadAGI(agi://10.10.10.15:4577/call_log--HVcauses--PRI-----NODEBUG-----${HANGUPCAUSE}-----${DIALSTATUS}-----${DIALEDTIME}-----${ANSWEREDTIME})
#
# Copyright (C) 2009  Vicidial Group info@vicidial.com    LICENSE: AGPLv2
#
# CHANGELOG:
# 61010-1007 - mattf - First test build
# 70116-1619 - mattf - Added Auto Alt Dial code
# 70215-1258 - mattf - Added queue_log entry when deleting vac record
# 70808-1425 - mattf - Moved VD_hangup section to the call_log end stage to improve efficiency
# 71030-2039 - mattf - Added priority to hopper insertions
# 80224-0040 - mattf - Fixed bugs in vicidial_log updates
# 80430-0907 - mattf - Added term_reason to vicidial_log and vicidial_closer_log
# 80507-1138 - mattf - Fixed vicidial_closer_log CALLER hangups
# 80510-0414 - mattf - Fixed crossover logging bugs
# 80510-2058 - mattf - Fixed status override bug
# 80525-1040 - mattf - Added IVR vac status compatibility for inbound calls
# 80830-0035 - mattf - Added auto alt dialing for EXTERNAL leads for each lead
# 80909-0843 - mattf - Added support for campaign-speccific DNC lists
# 81021-0306 - mattf - Added Local channel logging support and while-to-if changes
# 81026-1247 - mattf - Changed to allow for better remote agent calling
# 81029-0522 - mattf - Changed to disallow queue_log logging of IVR calls
# 90504-1712 - mikec - Added support for multi server logging.


### defaults for PreFork
$VARfastagi_log_min_servers =	'3';
$VARfastagi_log_max_servers =	'16';
$VARfastagi_log_min_spare_servers = '2';
$VARfastagi_log_max_spare_servers = '8';
$VARfastagi_log_max_requests =	'1000';
$VARfastagi_log_checkfordead =	'30';
$VARfastagi_log_checkforwait =	'60';

### default path to astguiclient configuration file: ###
$PATHconf =		'/etc/astguiclient.conf';

### open the config file and parse it. ###
open(CONF, "$PATHconf") || die "can't open $PATHconf: $!\n";
@conf = <CONF>;
close(CONF);
$i=0;
foreach(@conf)
	{
	$line = $conf[$i];
	$line =~ s/ |>|\n|\r|\t|\#.*|;.*//gi;
	if ($line =~ /^PATHlogs/)
		{
		$PATHlogs = $line;
		$PATHlogs =~ s/.*=//gi;
		}
	if ($line =~ /^VARfastagi_log_min_servers/)
		{
		$VARfastagi_log_min_servers = $line;
		$VARfastagi_log_min_servers =~ s/.*=//gi;
		}
	if ($line =~ /^VARfastagi_log_max_servers/)
		{
		$VARfastagi_log_max_servers = $line;
		$VARfastagi_log_max_servers =~ s/.*=//gi;
		}
	if ($line =~ /^VARfastagi_log_min_spare_servers/)
		{
		$VARfastagi_log_min_spare_servers = $line;
		$VARfastagi_log_min_spare_servers =~ s/.*=//gi;
		}
	if ($line =~ /^VARfastagi_log_max_spare_servers/)
		{
		$VARfastagi_log_max_spare_servers = $line;
		$VARfastagi_log_max_spare_servers =~ s/.*=//gi;
		}
	if ($line =~ /^VARfastagi_log_max_requests/)
		{
		$VARfastagi_log_max_requests = $line;
		$VARfastagi_log_max_requests =~ s/.*=//gi;
		}
	if ($line =~ /^VARfastagi_log_checkfordead/)
		{
		$VARfastagi_log_checkfordead = $line;
		$VARfastagi_log_checkfordead =~ s/.*=//gi;
		}
	if ($line =~ /^VARfastagi_log_checkforwait/)
		{
		$VARfastagi_log_checkforwait = $line;
		$VARfastagi_log_checkforwait =~ s/.*=//gi;
		}
	if ($line =~ /^VARserver_ip/)
		{
		$VARserver_ip = $line;
		$VARserver_ip =~ s/.*=//gi;
		}
	if ($line =~ /^VARDB_server/)
		{
		$VARDB_server = $line;
		$VARDB_server =~ s/.*=//gi;
		}
	if ($line =~ /^VARDB_database/)
		{
		$VARDB_database = $line;
		$VARDB_database =~ s/.*=//gi;
		}
	if ($line =~ /^VARDB_user/)
		{
		$VARDB_user = $line;
		$VARDB_user =~ s/.*=//gi;
		}
	if ($line =~ /^VARDB_pass/)
		{
		$VARDB_pass = $line;
		$VARDB_pass =~ s/.*=//gi;
		}
	if ($line =~ /^VARDB_port/)
		{
		$VARDB_port = $line;
		$VARDB_port =~ s/.*=//gi;
		}
	$i++;
	}

($sec,$min,$hour,$mday,$mon,$year,$wday,$yday,$isdst) = localtime(time);
$year = ($year + 1900);
$mon++;
if ($mon < 10) {$mon = "0$mon";}
if ($mday < 10) {$mday = "0$mday";}
if ($hour < 10) {$Fhour = "0$hour";}
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
$rec_count=0;
while ($sthBrows > $rec_count)
	{
	 @aryB = $sthB->fetchrow_array;
		$SERVERLOG =	"$aryB[0]";
	 $rec_count++;
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




sub process_request {
	my $self = shift;
	
	$process = 'begin';
	$script = 'VDfastAGI';
	########## Get current time, parse configs, get logging preferences ##########
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


	### default path to astguiclient configuration file: ###
	$PATHconf =		'/etc/astguiclient.conf';

	### open and parse the config file ###
	open(CONF, "$PATHconf") || die "can't open $PATHconf: $!\n";
	@conf = <CONF>;
	close(CONF);
	$i=0;
	foreach(@conf)
		{
		$line = $conf[$i];
		$line =~ s/ |>|\n|\r|\t|\#.*|;.*//gi;
		if ($line =~ /^PATHhome/)
			{
			$PATHhome = $line;   
			$PATHhome =~ s/.*=//gi;
			}
		if ($line =~ /^PATHlogs/)
			{
			$PATHlogs = $line;   
			$PATHlogs =~ s/.*=//gi;
			}
		if ($line =~ /^PATHagi/)
			{
			$PATHagi = $line;   
			$PATHagi =~ s/.*=//gi;
			}
		if ($line =~ /^PATHweb/)
			{
			$PATHweb = $line;   
			$PATHweb =~ s/.*=//gi;
			}
		if ($line =~ /^PATHsounds/)
			{
			$PATHsounds = $line;   
			$PATHsounds =~ s/.*=//gi;
			}
		if ($line =~ /^PATHmonitor/)
			{
			$PATHmonitor = $line;   
			$PATHmonitor =~ s/.*=//gi;
			}
		if ($line =~ /^VARserver_ip/)
			{
			$VARserver_ip = $line;   
			$VARserver_ip =~ s/.*=//gi;
			}
		if ($line =~ /^VARDB_server/)
			{
			$VARDB_server = $line;   
			$VARDB_server =~ s/.*=//gi;
			}
		if ($line =~ /^VARDB_database/)
			{
			$VARDB_database = $line;   
			$VARDB_database =~ s/.*=//gi;
			}
		if ($line =~ /^VARDB_user/)
			{
			$VARDB_user = $line;   
			$VARDB_user =~ s/.*=//gi;
			}
		if ($line =~ /^VARDB_pass/)
			{
			$VARDB_pass = $line;   
			$VARDB_pass =~ s/.*=//gi;
			}
		if ($line =~ /^VARDB_port/)
			{
			$VARDB_port = $line;   
			$VARDB_port =~ s/.*=//gi;
			}
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
	$rec_count=0;
	while ($sthArows > $rec_count)
		{
		$AGILOG = '0';
		 @aryA = $sthA->fetchrow_array;
			$DBagi_output =			"$aryA[0]";
			if ($DBagi_output =~ /STDERR/)	{$AGILOG = '1';}
			if ($DBagi_output =~ /FILE/)	{$AGILOG = '2';}
			if ($DBagi_output =~ /BOTH/)	{$AGILOG = '3';}
		 $rec_count++;
		}
	$sthA->finish();

	### get the socket connection to the client ###
	my $socket = $self->{server}->{client};

	### ask the socket for the clients ip address ###
	my $ast_server_ip = $socket->peerhost();

	### if client is connecting from localhost ###
	### use the ip address in the config file ###
	if ( $ast_server_ip eq "127.0.0.1" ) 
		{
			$ast_server_ip = $VARserver_ip;
		}

	### start the log if we are logging ###
	if ($AGILOG) 
		{
		$agi_string = "+++++++++++++++++ FastAGI Start ++++++++++++++++++++++++++++++++++++++++"; 
		&agi_output;
		$agi_string = "Connection accepted from $ast_server_ip";
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
			$ring_time = $ARGV_vars[5];
			$agi_string = "URL HVcauses: |$PRI|$DEBUG|$hangup_cause|$dialstatus|$dial_time|$ring_time|";   
			&agi_output;
			}
		if (!$fullCID)	# if no fullCID sent
			{
			if (/^agi_callerid\:\s+(.*)$/)		{$callerid = $1;}
			if (/^agi_calleridname\:\s+(.*)$/)	{$calleridname = $1;}
			if ( $calleridname =~ /\"/)  {$calleridname =~ s/\"//gi;}
		if ( 
		     ( 
		       (length($calleridname)>5) && 
		       ( 
		         (!$callerid) or 
		         ($callerid =~ /unknown|private|00000000/i) or 
		         ($callerid =~ /5551212/) 
		       )
		      ) or 
		     ( 
		       (length($calleridname)>17) && 
		       ($calleridname =~ /\d\d\d\d\d\d\d\d\d\d\d\d\d\d\d\d\d\d\d/) 
		      ) 
		    )
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

	if ( ($extension =~ /h/i) && (length($extension) < 3) )  {$stage = 'END';}
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
			$channel_group='';

			if ($AGILOG) {$agi_string = "+++++ CALL LOG START : $now_date";   &agi_output;}

			if ($channel =~ /^SIP/) {$channel =~ s/-.*//gi;}
			if ($channel =~ /^IAX2/) {$channel =~ s/\/\d+$//gi;}
			if ($channel =~ /^Zap\//)
				{
				$channel_line = $channel;
				$channel_line =~ s/^Zap\///gi;

				$stmtA = "SELECT count(*) FROM phones where server_ip='$ast_server_ip' and extension='$channel_line' and protocol='Zap';";
				$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
				$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
				$sthArows=$sthA->rows;
				@aryA = $sthA->fetchrow_array;
				$is_client_phone	 = "$aryA[0]";
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

				$stmtA = "SELECT count(*) FROM phones where server_ip='$ast_server_ip' and dialplan_number='$channel_line' and protocol='EXTERNAL';";
				$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
				$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
				$sthArows=$sthA->rows;
				@aryA = $sthA->fetchrow_array;
				$is_client_phone	 = "$aryA[0]";
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
				
				$SIP_ext = $channel;	$SIP_ext =~ s/SIP\/|IAX2\/|Zap\/|Local\///gi;

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
					$cmd_line_b	=	"$aryA[0]";
					$cmd_line_d	=	"$aryA[1]";
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
			$stmtA = "INSERT INTO call_log (uniqueid,channel,channel_group,type,server_ip,extension,number_dialed,start_time,start_epoch,end_time,end_epoch,length_in_sec,length_in_min,caller_code) values('$unique_id','$channel','$channel_group','$type','$ast_server_ip','$extension','$number_dialed','$now_date','$now_date_epoch','','','','','$callerid')";

			if ($AGILOG) {$agi_string = "|$stmtA|";   &agi_output;}
			$affected_rows = $dbhA->do($stmtA);

			$dbhA->disconnect();
			}


		### call end stage
		else		 
			{
			if ($AGILOG) {$agi_string = "|CALL HUNG UP|";   &agi_output;}
			if ($request =~ /--HVcauses--/i)
				{
				$HVcauses=1;
				@ARGV_vars = split(/-----/, $request);
				$PRI = $ARGV_vars[0];
				$PRI =~ s/.*--HVcauses--//gi;
				$DEBUG = $ARGV_vars[1];
				$hangup_cause = $ARGV_vars[2];
				$dialstatus = $ARGV_vars[3];
				$dial_time = $ARGV_vars[4];
				$ring_time = $ARGV_vars[5];
				$agi_string = "URL HVcauses: |$PRI|$DEBUG|$hangup_cause|$dialstatus|$dial_time|$ring_time|";   
				&agi_output;
				}

			### get uniqueid and start_epoch from the call_log table
			$stmtA = "SELECT uniqueid,start_epoch FROM call_log where uniqueid='$unique_id';";
			$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
			$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
			$sthArows=$sthA->rows;
			$rec_count=0;
			while ($sthArows > $rec_count)
				{
				@aryA = $sthA->fetchrow_array;
				$start_time	=			"$aryA[1]";
				if ($AGILOG) {$agi_string = "|$aryA[0]|$aryA[1]|";   &agi_output;}
				$rec_count++;
				}
			$sthA->finish();

			if ($rec_count)
				{
				$length_in_sec = ($now_date_epoch - $start_time);
				$length_in_min = ($length_in_sec / 60);
				$length_in_min = sprintf("%8.2f", $length_in_min);

				if ($AGILOG) {$agi_string = "QUERY done: start time = $start_time | sec: $length_in_sec | min: $length_in_min |";   &agi_output;}

				$stmtA = "UPDATE call_log set end_time='$now_date',end_epoch='$now_date_epoch',length_in_sec=$length_in_sec,length_in_min='$length_in_min',channel='$channel' where uniqueid='$unique_id'";

				if ($AGILOG) {$agi_string = "|$stmtA|";   &agi_output;}
				$affected_rows = $dbhA->do($stmtA);
				}

			$stmtA = "DELETE from live_inbound where uniqueid='$unique_id' and server_ip='$ast_server_ip'";
			if ($AGILOG) {$agi_string = "|$stmtA|";   &agi_output;}
			$affected_rows = $dbhA->do($stmtA);

		##### BEGIN Park Log entry check and update #####
			$stmtA = "SELECT UNIX_TIMESTAMP(parked_time),UNIX_TIMESTAMP(grab_time) FROM park_log where uniqueid='$unique_id' and server_ip='$ast_server_ip' LIMIT 1;";
			$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
			$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
			$sthArows=$sthA->rows;
			$rec_count=0;
			if ($sthArows > 0)
				{
				@aryA = $sthA->fetchrow_array;
				$parked_time	=			"$aryA[0]";
				$grab_time	=			"$aryA[1]";
				if ($AGILOG) {$agi_string = "|$aryA[0]|$aryA[1]|";   &agi_output;}
				$rec_count++;
				}
			$sthA->finish();

			  if ($rec_count)
			  {
			if ($AGILOG) {$agi_string = "*****Entry found for $unique_id-$ast_server_ip in park_log: $parked_time|$grab_time";   &agi_output;}
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

				$stmtA = "UPDATE park_log set status='HUNGUP',hangup_time='$now_date',parked_sec='$parked_sec',talked_sec='$talked_sec' where uniqueid='$unique_id' and server_ip='$ast_server_ip'";
				$affected_rows = $dbhA->do($stmtA);
			   }
		##### END Park Log entry check and update #####

		#	$dbhA->disconnect();

			if ($AGILOG) {$agi_string = "+++++ CALL LOG HUNGUP: |$unique_id|$channel|$extension|$now_date|min: $length_in_min|";   &agi_output;}


		##### BEGIN former VD_hangup section functions #####

			$VDADcampaign='';
			$VDADphone='';
			$VDADphone_code='';

			if ($DEBUG =~ /^DEBUG$/)
			{
				### open the hangup cause out file for writing ###
				open(DEBUGOUT, ">>$PATHlogs/HANGUP_cause-output.txt")
						|| die "Can't open $PATHlogs/HANGUP_cause-output.txt: $!\n";

				print DEBUGOUT "$now_date|$hangup_cause|$dialstatus|$dial_time|$ring_time|$unique_id|$channel|$extension|$type|$callerid|$calleridname|$priority|\n";

				close(DEBUGOUT);
			}
			else 
			{
			if ($AGILOG) {$agi_string = "DEBUG: $DEBUG";   &agi_output;}
			}


			$callerid =~ s/\"//gi;
			$CIDlead_id = $callerid;
			$CIDlead_id = substr($CIDlead_id, 11, 9);
			$CIDlead_id = ($CIDlead_id + 0);

			if ($AGILOG) {$agi_string = "VD_hangup : $callerid $channel $priority $CIDlead_id";   &agi_output;}

			if ($channel =~ /^Local/)
			{
				if ( ($PRI =~ /^PRI$/) && ($callerid =~ /\d\d\d\d\d\d\d\d\d\d\d\d\d\d\d\d\d\d\d/) && ( ($dialstatus =~ /BUSY/) || ( ($dialstatus =~ /CHANUNAVAIL/) && ($hangup_cause =~ /^1$|^28$/) ) ) )
				{
					if ($dialstatus =~ /BUSY/) {$VDL_status = 'B'; $VDAC_status = 'BUSY';}
					if ($dialstatus =~ /CHANUNAVAIL/) {$VDL_status = 'DC'; $VDAC_status = 'DISCONNECT';}

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
					if ($AGILOG) {$agi_string = "--    VDAD vicidial_log update: |$VDLaffected_rows|$uniqueid|$Euniqueid|";   &agi_output;}

					sleep(1);

					$dbhA->disconnect();
				}
				else
				{
					if ($AGILOG) {$agi_string = "--    VD_hangup Local DEBUG: |$PRI|$callerid|$dialstatus|$hangup_cause|";   &agi_output;}
				}

				if ($AGILOG) {$agi_string = "+++++ VDAD START LOCAL CHANNEL: EXITING- $priority";   &agi_output;}
				if ($priority > 2) {sleep(1);}
			}
			else
			{

				########## FIND AND DELETE vicidial_auto_calls ##########
				$VD_alt_dial = 'NONE';
				$stmtA = "SELECT lead_id,callerid,campaign_id,alt_dial,stage,UNIX_TIMESTAMP(call_time),uniqueid,status FROM vicidial_auto_calls where uniqueid = '$uniqueid' or callerid = '$callerid' limit 1;";
				if ($AGILOG) {$agi_string = "|$stmtA|";   &agi_output;}
				$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
				$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
				$sthArows=$sthA->rows;
				 $rec_countCUSTDATA=0;
				if ($sthArows > 0)
					{
					@aryA = $sthA->fetchrow_array;
					$VD_lead_id	=		"$aryA[0]";
					$VD_callerid	=	"$aryA[1]";
					$VD_campaign_id	=	"$aryA[2]";
					$VD_alt_dial	=	"$aryA[3]";
					$VD_stage =			"$aryA[4]";
					$VD_start_epoch =	"$aryA[5]";
					$VD_uniqueid =		"$aryA[6]";
					$VD_status =		"$aryA[7]";
					 $rec_countCUSTDATA++;
					}
				$sthA->finish();

				if (!$rec_countCUSTDATA)
					{
					if ($AGILOG) {$agi_string = "VD hangup: no VDAC record found: $uniqueid $calleridname";   &agi_output;}
					}
				else
					{
					$stmtA = "DELETE FROM vicidial_auto_calls where ( ( (status!='IVR') and (uniqueid='$uniqueid' or callerid = '$callerid') ) or ( (status='IVR') and (uniqueid='$uniqueid') ) ) order by call_time desc limit 1;";
					$affected_rows = $dbhA->do($stmtA);
					if ($AGILOG) {$agi_string = "--    VDAC record deleted: |$affected_rows|   |$VD_lead_id|$uniqueid|$VD_uniqueid|$VD_callerid|$ast_server_ip|$VD_status|";   &agi_output;}

					#############################################
					##### START QUEUEMETRICS LOGGING LOOKUP #####
					$stmtA = "SELECT enable_queuemetrics_logging,queuemetrics_server_ip,queuemetrics_dbname,queuemetrics_login,queuemetrics_pass,queuemetrics_log_id FROM system_settings;";
					$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
					$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
					$sthArows=$sthA->rows;
					$rec_count=0;
					while ($sthArows > $rec_count)
						{
						 @aryA = $sthA->fetchrow_array;
							$enable_queuemetrics_logging =	"$aryA[0]";
							$queuemetrics_server_ip	=		"$aryA[1]";
							$queuemetrics_dbname =			"$aryA[2]";
							$queuemetrics_login=			"$aryA[3]";
							$queuemetrics_pass =			"$aryA[4]";
							$queuemetrics_log_id =			"$aryA[5]";
						 $rec_count++;
						}
					$sthA->finish();
					##### END QUEUEMETRICS LOGGING LOOKUP #####
					###########################################
					if ( ($enable_queuemetrics_logging > 0) && ($VD_status !~ /IVR/) )
						{
						$VD_agent='NONE';
						$secX = time();
						$VD_call_length = ($secX - $VD_start_epoch);
						$VD_stage =~ s/.*-//gi;
						if ($VD_stage < 0.25) {$VD_stage=0;}

						$dbhB = DBI->connect("DBI:mysql:$queuemetrics_dbname:$queuemetrics_server_ip:3306", "$queuemetrics_login", "$queuemetrics_pass")
						 or die "Couldn't connect to database: " . DBI->errstr;

						if ($DBX) {print "CONNECTED TO DATABASE:  $queuemetrics_server_ip|$queuemetrics_dbname\n";}

						$stmtB = "SELECT agent from queue_log where call_id='$VD_callerid' and verb='CONNECT';";
						$sthB = $dbhB->prepare($stmtB) or die "preparing: ",$dbhB->errstr;
						$sthB->execute or die "executing: $stmtB ", $dbhB->errstr;
						$sthBrows=$sthB->rows;
						$rec_count=0;
						while ($sthBrows > $rec_count)
							{
							@aryB = $sthB->fetchrow_array;
							$VD_agent =	"$aryB[0]";
							$rec_count++;
							}
						$sthB->finish();

						if ($rec_count < 1)
							{
							$stmtB = "INSERT INTO queue_log SET partition='P01',time_id='$secX',call_id='$VD_callerid',queue='$VD_campaign_id',agent='$VD_agent',verb='ABANDON',data1='1',data2='1',data3='$VD_stage',serverid='$queuemetrics_log_id';";
							$Baffected_rows = $dbhB->do($stmtB);
							}
						else
							{
							$stmtB = "INSERT INTO queue_log SET partition='P01',time_id='$secX',call_id='$VD_callerid',queue='$VD_campaign_id',agent='$VD_agent',verb='COMPLETECALLER',data1='$VD_stage',data2='$VD_call_length',data3='1',serverid='$queuemetrics_log_id';";
							$Baffected_rows = $dbhB->do($stmtB);
							}

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
							$VD_start_epoch	=	"$aryA[0]";
							$VD_status =		"$aryA[1]";
							$VD_user =			"$aryA[2]";
							$VD_term_reason =	"$aryA[3]";
							$VD_comments =		"$aryA[4]";
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
							$VD_start_epoch	=	"$aryA[0]";
							$VD_status =		"$aryA[1]";
							$VD_closecallid	=	"$aryA[2]";
							$VD_user =			"$aryA[3]";
							$VD_term_reason =	"$aryA[4]";
							$VD_length_in_sec =	"$aryA[5]";
							$VD_queue_seconds =	"$aryA[6]";
							$VD_comments =		"$aryA[7]";
							 $epc_countCUSTDATA++;
							}
						$sthA->finish();
						}
					if (!$epc_countCUSTDATA)
						{
						if ($AGILOG) {$agi_string = "no VDL or VDCL record found: $uniqueid $calleridname $VD_lead_id $uniqueid $VD_uniqueid";   &agi_output;}
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
								if ( ($VD_term_reason !~ /AGENT|CALLER|QUEUETIMEOUT|AFTERHOURS|HOLDRECALLXFER|HOLDTIME/) && ( ($VD_user =~ /VDAD|VDCL/) || (length($VD_user) < 1) ) )
									{$VDCLSQL_term_reason = "term_reason='ABANDON',";}
								else
									{
									if ($VD_term_reason !~ /AGENT|CALLER|QUEUETIMEOUT|AFTERHOURS|HOLDRECALLXFER|HOLDTIME/)
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
						$VD_auto_alt_dial	=			"$aryA[0]";
						$VD_auto_alt_dial_statuses	=	"$aryA[1]";
						$VD_use_internal_dnc =			"$aryA[2]";
						$VD_use_campaign_dnc =			"$aryA[3]";
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
								$VD_alt_phone =			"$aryA[0]";
								$VD_alt_phone =~ s/\D//gi;
								$VD_gmt_offset_now =	"$aryA[1]";
								$VD_state =				"$aryA[2]";
								$VD_list_id =			"$aryA[3]";
								 $epc_countCAMPDATA++;
								}
							$sthA->finish();
							if (length($VD_alt_phone)>5)
								{
								if ($VD_use_internal_dnc =~ /Y/)
									{
									$stmtA="SELECT count(*) FROM vicidial_dnc where phone_number='$VD_alt_phone';";
										if ($AGILOG) {$agi_string = "|$stmtA|";   &agi_output;}
									$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
									$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
									$sthArows=$sthA->rows;
									if ($sthArows > 0)
										{
										@aryA = $sthA->fetchrow_array;
										$VD_alt_dnc_count =	"$aryA[0]";
										}
									$sthA->finish();
									}
								else {$VD_alt_dnc_count=0;}
								if ($VD_use_campaign_dnc =~ /Y/)
									{
									$stmtA="SELECT count(*) FROM vicidial_campaign_dnc where phone_number='$VD_alt_phone' and campaign_id='$VD_campaign_id';";
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
									$stmtA = "INSERT INTO vicidial_hopper SET lead_id='$VD_lead_id',campaign_id='$VD_campaign_id',status='READY',list_id='$VD_list_id',gmt_offset_now='$VD_gmt_offset_now',state='$VD_state',alt_dial='ALT',user='',priority='25';";
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
								$VD_address3 =			"$aryA[0]";
								$VD_address3 =~ s/\D//gi;
								$VD_gmt_offset_now =	"$aryA[1]";
								$VD_state =				"$aryA[2]";
								$VD_list_id =			"$aryA[3]";
								 $epc_countCAMPDATA++;
								}
							$sthA->finish();
							if (length($VD_address3)>5)
								{
								if ($VD_use_internal_dnc =~ /Y/)
									{
									$stmtA="SELECT count(*) FROM vicidial_dnc where phone_number='$VD_address3';";
										if ($AGILOG) {$agi_string = "|$stmtA|";   &agi_output;}
									$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
									$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
									$sthArows=$sthA->rows;
									if ($sthArows > 0)
										{
										@aryA = $sthA->fetchrow_array;
										$VD_alt_dnc_count =	"$aryA[0]";
										}
									$sthA->finish();
									}
								else {$VD_alt_dnc_count=0;}
								if ($VD_use_campaign_dnc =~ /Y/)
									{
									$stmtA="SELECT count(*) FROM vicidial_campaign_dnc where phone_number='$VD_address3' and campaign_id='$VD_campaign_id';";
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
									$stmtA = "INSERT INTO vicidial_hopper SET lead_id='$VD_lead_id',campaign_id='$VD_campaign_id',status='READY',list_id='$VD_list_id',gmt_offset_now='$VD_gmt_offset_now',state='$VD_state',alt_dial='ADDR3',user='',priority='20';";
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
								$VD_gmt_offset_now =	"$aryA[1]";
								$VD_state =				"$aryA[2]";
								$VD_list_id =			"$aryA[3]";
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
								$alt_dial_phones_count = "$aryA[0]";
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
									$VD_altdial_id =		"$aryA[0]";
									$VD_altdial_phone = 	"$aryA[1]";
									$VD_altdial_active = 	"$aryA[2]";
									}
								else
									{$Xlast=9999999999;}
								$sthA->finish();

								if ($VD_altdial_active =~ /Y/)
									{
									if ($VD_use_internal_dnc =~ /Y/)
										{
										$stmtA="SELECT count(*) FROM vicidial_dnc where phone_number='$VD_altdial_phone';";
											if ($AGILOG) {$agi_string = "|$stmtA|";   &agi_output;}
										$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
										$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
										$sthArows=$sthA->rows;
										if ($sthArows > 0)
											{
											@aryA = $sthA->fetchrow_array;
											$VD_alt_dnc_count =	"$aryA[0]";
											}
										$sthA->finish();
										}
									else {$VD_alt_dnc_count=0;}
									if ($VD_use_campaign_dnc =~ /Y/)
										{
										$stmtA="SELECT count(*) FROM vicidial_campaign_dnc where phone_number='$VD_altdial_phone' and campaign_id='$VD_campaign_id';";
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
										if ($alt_dial_phones_count eq '$Xlast') 
											{$Xlast = 'LAST';}
										$stmtA = "INSERT INTO vicidial_hopper SET lead_id='$VD_lead_id',campaign_id='$VD_campaign_id',status='READY',list_id='$VD_list_id',gmt_offset_now='$VD_gmt_offset_now',state='$VD_state',alt_dial='X$Xlast',user='',priority='15';";
										$affected_rows = $dbhA->do($stmtA);
										if ($AGILOG) {$agi_string = "--    VDH record inserted: |$affected_rows|   |$stmtA|X$Xlast|$VD_altdial_id|";   &agi_output;}
										$Xlast=9999999999;
										}
									else
										{if ($AGILOG) {$agi_string = "--    VDH alt dial is DNC|X$Xlast|$VD_altdial_phone|";   &agi_output;}}
									}
								}
							}
						}
					##### END AUTO ALT PHONE DIAL SECTION #####
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
	open(LOGOUT, ">>$AGILOGfile")
			|| die "Can't open $AGILOGfile: $!\n";
	print LOGOUT "$now_date|$script|$process|$agi_string\n";
	close(LOGOUT);
	}
	### send to STDERR writing ###
if ( ($AGILOG == '1') || ($AGILOG == '3') )
	{
	print STDERR "$now_date|$script|$process|$agi_string\n";
	}
$agi_string='';
}
