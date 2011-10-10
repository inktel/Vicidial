#!/usr/bin/perl
#
# AST_manager_listen.pl version 2.4
#
# Part of the Asterisk Central Queue System (ACQS)
#
# DESCRIPTION:
# connects to the Asterisk Manager interface and updates records in the 
# vicidial_manager table of the asterisk database in MySQL based upon the 
# events that it receives
# 
# SUMMARY:
# This program was designed as the listen-only part of the ACQS. It's job is to
# look for certain events and based upon either the uniqueid or the callerid of 
# the call update the status and information of an action record in the 
# vicidial_manager table of the asterisk MySQL database.
#
# Copyright (C) 2010  Matt Florell <vicidial@gmail.com>    LICENSE: AGPLv2
#
# CHANGES
# 50322-1300 - changed callerid parsing to remove quotes and number
# 50616-1559 - Added NewCallerID parsing and updating 
# 50621-1406 - Added Asterisk server shutdown and connection dead detection 
# 50810-1534 - Added database server variable definitions lookup
# 50824-1606 - Altered CVS/1.2 support for different output
# 50901-2359 - Another CVS/1.2 output parsing fix
# 51222-1553 - fixed parentheses bug in manager output
# 60403-1230 - Added SVN/1.4 support for different output
# 60718-0909 - changed to DBI by Marin Blu
# 60718-0955 - changed to use /etc/astguiclient.conf for configs
# 60720-1142 - added keepalive to MySQL connection every 50 seconds
# 60814-1733 - added option for no logging to file
# 60906-1714 - added updating for special vicidial conference calls
# 71129-2004 - Fixed SQL error
# 100416-0635 - Added fix for extension append feature
# 100625-1220 - Added waitfors after logout to fix broken pipe errors in asterisk <MikeC>
# 100903-0041 - Changed lead_id max length to 10 digits
#

# constants
$DB=1;  # Debug flag, set to 0 for no debug messages, lots of output
$US='__';
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
		print "allowed run time options:\n  [-t] = test\n  [-debug] = verbose debug messages\n[-debugX] = Extra-verbose debug messages\n\n";
		}
	else
		{
		if ($args =~ /-debug/i)
			{
			$DB=1; # Debug flag
			}
		if ($args =~ /--debugX/i)
			{
			$DBX=1;
			print "\n----- SUPER-DUPER DEBUGGING -----\n\n";
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
	#	print "no command line options set\n";
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

	&get_time_now;

#use lib './lib', '../lib';
use Time::HiRes ('gettimeofday','usleep','sleep');  # necessary to have perl sleep command of less than one second
use DBI;
use Net::Telnet ();
  
$dbhA = DBI->connect("DBI:mysql:$VARDB_database:$VARDB_server:$VARDB_port", "$VARDB_user", "$VARDB_pass")
or die "Couldn't connect to database: " . DBI->errstr;

### Grab Server values from the database
$stmtA = "SELECT telnet_host,telnet_port,ASTmgrUSERNAME,ASTmgrSECRET,ASTmgrUSERNAMEupdate,ASTmgrUSERNAMElisten,ASTmgrUSERNAMEsend,max_vicidial_trunks,answer_transfer_agent,local_gmt,ext_context,vd_server_logs FROM servers where server_ip = '$server_ip';";
$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
$sthArows=$sthA->rows;
if ($sthArows > 0)
	{
	@aryA = $sthA->fetchrow_array;
	$DBtelnet_host	=			$aryA[0];
	$DBtelnet_port	=			$aryA[1];
	$DBASTmgrUSERNAME	=		$aryA[2];
	$DBASTmgrSECRET	=			$aryA[3];
	$DBASTmgrUSERNAMEupdate	=	$aryA[4];
	$DBASTmgrUSERNAMElisten	=	$aryA[5];
	$DBASTmgrUSERNAMEsend	=	$aryA[6];
	$DBmax_vicidial_trunks	=	$aryA[7];
	$DBanswer_transfer_agent=	$aryA[8];
	$DBSERVER_GMT		=		$aryA[9];
	$DBext_context	=			$aryA[10];
	$DBvd_server_logs =			$aryA[11];
	if ($DBtelnet_host)				{$telnet_host = $DBtelnet_host;}
	if ($DBtelnet_port)				{$telnet_port = $DBtelnet_port;}
	if ($DBASTmgrUSERNAME)			{$ASTmgrUSERNAME = $DBASTmgrUSERNAME;}
	if ($DBASTmgrSECRET)			{$ASTmgrSECRET = $DBASTmgrSECRET;}
	if ($DBASTmgrUSERNAMEupdate)	{$ASTmgrUSERNAMEupdate = $DBASTmgrUSERNAMEupdate;}
	if ($DBASTmgrUSERNAMElisten)	{$ASTmgrUSERNAMElisten = $DBASTmgrUSERNAMElisten;}
	if ($DBASTmgrUSERNAMEsend)		{$ASTmgrUSERNAMEsend = $DBASTmgrUSERNAMEsend;}
	if ($DBmax_vicidial_trunks)		{$max_vicidial_trunks = $DBmax_vicidial_trunks;}
	if ($DBanswer_transfer_agent)	{$answer_transfer_agent = $DBanswer_transfer_agent;}
	if ($DBSERVER_GMT)				{$SERVER_GMT = $DBSERVER_GMT;}
	if ($DBext_context)				{$ext_context = $DBext_context;}
	if ($DBvd_server_logs =~ /Y/)	{$SYSLOG = '1';}
		else {$SYSLOG = '0';}
	}
$sthA->finish();

if (!$telnet_port) {$telnet_port = '5038';}

	$event_string='LOGGED INTO MYSQL SERVER ON 1 CONNECTION|';
	&event_logger;

$one_day_interval = 90;		# 1 day loops for 3 months
while($one_day_interval > 0)
	{

		$event_string="STARTING NEW MANAGER TELNET CONNECTION||ATTEMPT|ONE DAY INTERVAL:$one_day_interval|";
	&event_logger;
#   Errmode    => Return,

	### connect to asterisk manager through telnet
	$tn = new Net::Telnet (Port => $telnet_port,
						  Prompt => '/.*[\$%#>] $/',
						  Output_record_separator => '',);
	#$LItelnetlog = "$PATHlogs/listen_telnet_log.txt"  # uncomment for telnet log
	#$fh = $tn->dump_log("$LItelnetlog");  # uncomment for telnet log
	if (length($ASTmgrUSERNAMElisten) > 3) {$telnet_login = $ASTmgrUSERNAMElisten;}
	else {$telnet_login = $ASTmgrUSERNAME;}
	$tn->open("$telnet_host"); 
	$tn->waitfor('/[01]\n$/');			# print login
	$tn->print("Action: Login\nUsername: $telnet_login\nSecret: $ASTmgrSECRET\n\n");
	$tn->waitfor('/Authentication accepted/');		# waitfor auth accepted

			$tn->buffer_empty;

		$event_string="STARTING NEW MANAGER TELNET CONNECTION|$telnet_login|CONFIRMED CONNECTION|ONE DAY INTERVAL:$one_day_interval|";
	&event_logger;

	$endless_loop=864000;		# 1 day at .10 seconds per loop

	while($endless_loop > 0)
		{
		### sleep for 10 hundredths of a second
		usleep(1*100*1000);

		$msg='';
		$read_input_buf = $tn->get(Errmode    => Return, Timeout    => 1,);
		$input_buf_length = length($read_input_buf);
		$msg = $tn->errmsg;
		if ($msg =~ /filehandle isn\'t open/i)
			{
			$endless_loop=0;
			$one_day_interval=0;
			print "ERRMSG: |$msg|\n";
			print "\nAsterisk server shutting down, PROCESS KILLED... EXITING\n\n";
				$event_string="Asterisk server shutting down, PROCESS KILLED... EXITING|ONE DAY INTERVAL:$one_day_interval|$msg|";
			&event_logger;
			}

		if ( ($read_input_buf !~ /\n\n/) or ($input_buf_length < 10) )
			{
	#		if ($read_input_buf =~ /\n/) {print "\n|||$input_buf_length|||$read_input_buf|||\n";}
			$input_buf = "$input_buf$read_input_buf";
			}
		else
			{
			$partial=0;
			$partial_input_buf='';

			if ($read_input_buf !~ /\n\n$/)
				{
				$read_input_buf =~ s/\(|\)/ /gi; # replace parens with space
				$partial_input_buf = $read_input_buf;
				$partial_input_buf =~ s/\n/-----/gi;
				$partial_input_buf =~ s/\*/\\\*/gi;
				$partial_input_buf =~ s/.*----------//gi;
				$partial_input_buf =~ s/-----/\n/gi;
				$read_input_buf =~ s/$partial_input_buf$//gi;
				$partial++;
				}

			$input_buf = "$input_buf$read_input_buf";
			@input_lines = split(/\n\n/, $input_buf);

			if($DB){print "input buffer: $input_buf_length     lines: $#input_lines     partial: $partial\n";}
			if ( ($DB) && ($partial) ) {print "-----[$partial_input_buf]-----\n\n";}
			if($DB){print "|$input_buf|\n";}
			
			$manager_string = "$input_buf";
				&manager_output_logger;

			$input_buf = "$partial_input_buf";
			

			@command_line=@MT;
			$ILcount=0;
			foreach(@input_lines)
				{
				##### look for special vicidial conference call event #####
				if ( ($input_lines[$ILcount] =~ /CallerIDName: DCagcW/) && ($input_lines[$ILcount] =~ /Event: Dial|State: Up/) )
					{
					### BEGIN 1.2.X tree versions
					$input_lines[$ILcount] =~ s/^\n|^\n\n//gi;
					@command_line=split(/\n/, $input_lines[$ILcount]);

					if ($input_lines[$ILcount] =~ /Event: Dial/)
						{
						if ($command_line[3] =~ /Destination: /i)
							{
							$channel = $command_line[3];
							$channel =~ s/Destination: |\s*$//gi;
							$callid = $command_line[5];
							$callid =~ s/CallerIDName: |\s*$//gi;
							   $callid =~ s/^\"//gi;   $callid =~ s/\".*$//gi;
							   if ($callid =~ /\S\S\S\S\S\S\S\S\S\S\S\S\S\S\S\S\S\S/) {$callid =~ s/ .*//gi;}
							$uniqueid = $command_line[6];
							$uniqueid =~ s/SrcUniqueID: |\s*$//gi;
							$stmtA = "UPDATE vicidial_manager set status='UPDATED', channel='$channel', uniqueid = '$uniqueid' where server_ip = '$server_ip' and callerid = '$callid'";
							if ($channel !~ /local/i)
								{
								print STDERR "|$stmtA|\n";
								my $affected_rows = $dbhA->do($stmtA);
								if($DB){print "|$affected_rows Conference DIALs updated|\n";}
								}
							}
						}
					if ($input_lines[$ILcount] =~ /State: Up/)
						{
						if ($command_line[2] =~ /Channel: /i)
							{
							$channel = $command_line[2];
							$channel =~ s/Channel: |\s*$//gi;
							$callid = $command_line[5];
							$callid =~ s/CallerIDName: |\s*$//gi;
							   $callid =~ s/^\"//gi;   $callid =~ s/\".*$//gi;
							   if ($callid =~ /\S\S\S\S\S\S\S\S\S\S\S\S\S\S\S\S\S\S/) {$callid =~ s/ .*//gi;}
							$uniqueid = $command_line[6];
							$uniqueid =~ s/SrcUniqueID: |\s*$//gi;
							$stmtA = "UPDATE vicidial_manager set status='UPDATED', channel='$channel', uniqueid = '$uniqueid' where server_ip = '$server_ip' and callerid = '$callid' and status='SENT';";
							print STDERR "|$stmtA|\n";
							my $affected_rows = $dbhA->do($stmtA);
							if($DB){print "|$affected_rows Conference DIALs updated|\n";}
							}
						}
					### END 1.2.X tree versions
					}

				##### parse through all other important events #####
				if ( ($input_lines[$ILcount] =~ /State: Ringing|State: Up|State: Dialing|Event: Newstate|Event: Hangup|Event: Newcallerid|Event: Shutdown|Event: CPD-Result/) && ($input_lines[$ILcount] !~ /ZOMBIE/) )
					{
					$input_lines[$ILcount] =~ s/^\n|^\n\n//gi;
					@command_line=split(/\n/, $input_lines[$ILcount]);
					if ($input_lines[$ILcount] =~ /Event: Shutdown/)
						{
						$endless_loop=0;
						$one_day_interval=0;
						print "\nAsterisk server shutting down, PROCESS KILLED... EXITING\n\n";
							$event_string="Asterisk server shutting down, PROCESS KILLED... EXITING|ONE DAY INTERVAL:$one_day_interval|";
						&event_logger;
						}

					if ($input_lines[$ILcount] =~ /Event: Hangup/)
						{
						if ( ($command_line[2] =~ /^Channel: /i) && ($command_line[3] =~ /^Uniqueid: /i) ) ### post 2005-08-07 CVS -- added Privilege line
							{
							$channel = $command_line[2];
							$channel =~ s/Channel: |\s*$//gi;
							$uniqueid = $command_line[3];
							$uniqueid =~ s/Uniqueid: |\s*$//gi;
							$stmtA = "UPDATE vicidial_manager set status='DEAD', channel='$channel' where server_ip = '$server_ip' and uniqueid = '$uniqueid' and callerid NOT LIKE \"DCagcW%\";";

							print STDERR "|$stmtA|\n";
							my $affected_rows = $dbhA->do($stmtA);
						   
							if($DB){print "|$affected_rows HANGUPS updated|\n";}
							}
						else
							{
							if ( ($command_line[3] =~ /^Channel: /i) && ($command_line[4] =~ /^Uniqueid: /i) ) ### post 2006-03-20 SVN -- Added Timestamp line
								{
								$channel = $command_line[3];
								$channel =~ s/Channel: |\s*$//gi;
								$uniqueid = $command_line[4];
								$uniqueid =~ s/Uniqueid: |\s*$//gi;
								$stmtA = "UPDATE vicidial_manager set status='DEAD', channel='$channel' where server_ip = '$server_ip' and uniqueid = '$uniqueid' and callerid NOT LIKE \"DCagcW%\";";

								print STDERR "|$stmtA|\n";
							    my $affected_rows = $dbhA->do($stmtA);
								if($DB){print "|$affected_rows HANGUPS updated|\n";}
								}
							else
								{
								$channel = $command_line[1];
								$channel =~ s/Channel: |\s*$//gi;
								$uniqueid = $command_line[2];
								$uniqueid =~ s/Uniqueid: |\s*$//gi;
								$stmtA = "UPDATE vicidial_manager set status='DEAD', channel='$channel' where server_ip = '$server_ip' and uniqueid = '$uniqueid' and callerid NOT LIKE \"DCagcW%\";";

								print STDERR "|$stmtA|\n";
							    my $affected_rows = $dbhA->do($stmtA);
								if($DB){print "|$affected_rows HANGUPS updated|\n";}
								}
							}
						}
		
					if ($input_lines[$ILcount] =~ /State: Dialing/)
						{
						if ( ($command_line[1] =~ /^Channel: /i) && ($command_line[4] =~ /^Uniqueid: /i) ) ### pre 2004-10-07 CVS
							{
							$channel = $command_line[1];
							$channel =~ s/Channel: |\s*$//gi;
							$callid = $command_line[3];
							$callid =~ s/Callerid: |\s*$//gi;
							   $callid =~ s/^\"//gi;   $callid =~ s/\".*$//gi;
							   if ($callid =~ /\S\S\S\S\S\S\S\S\S\S\S\S\S\S\S\S\S\S/) {$callid =~ s/ .*//gi;}
							$uniqueid = $command_line[4];
							$uniqueid =~ s/Uniqueid: |\s*$//gi;
							$stmtA = "UPDATE vicidial_manager set status='SENT', channel='$channel', uniqueid = '$uniqueid' where server_ip = '$server_ip' and callerid = '$callid'";
							print STDERR "|$stmtA|\n";
						    my $affected_rows = $dbhA->do($stmtA);
							
						    if($DB){print "|$affected_rows DIALINGs updated|\n";}
							}
						if ( ($command_line[1] =~ /^Channel: /i) && ($command_line[4] =~ /^CalleridName: /i) ) ### post 2004-10-07 CVS
							{
							$channel = $command_line[1];
							$channel =~ s/Channel: |\s*$//gi;
							$callid = $command_line[4];
							$callid =~ s/CalleridName: |\s*$//gi;
							   $callid =~ s/^\"//gi;   $callid =~ s/\".*$//gi;
							   if ($callid =~ /\S\S\S\S\S\S\S\S\S\S\S\S\S\S\S\S\S\S/) {$callid =~ s/ .*//gi;}
							$uniqueid = $command_line[5];
							$uniqueid =~ s/Uniqueid: |\s*$//gi;
							$stmtA = "UPDATE vicidial_manager set status='SENT', channel='$channel', uniqueid = '$uniqueid' where server_ip = '$server_ip' and callerid = '$callid'";
							print STDERR "|$stmtA|\n";
						    my $affected_rows = $dbhA->do($stmtA);
							if($DB){print "|$affected_rows DIALINGs updated|\n";}
							}
						if ( ($command_line[2] =~ /^Channel: /i) && ($command_line[5] =~ /^CalleridName: /i) ) ### post 2005-08-07 CVS
							{
							$channel = $command_line[2];
							$channel =~ s/Channel: |\s*$//gi;
							$callid = $command_line[5];
							$callid =~ s/CalleridName: |\s*$//gi;
							   $callid =~ s/^\"//gi;   $callid =~ s/\".*$//gi;
							   if ($callid =~ /\S\S\S\S\S\S\S\S\S\S\S\S\S\S\S\S\S\S/) {$callid =~ s/ .*//gi;}
							$uniqueid = $command_line[6];
							$uniqueid =~ s/Uniqueid: |\s*$//gi;
							$stmtA = "UPDATE vicidial_manager set status='SENT', channel='$channel', uniqueid = '$uniqueid' where server_ip = '$server_ip' and callerid = '$callid'";
							print STDERR "|$stmtA|\n";
						    my $affected_rows = $dbhA->do($stmtA);
							if($DB){print "|$affected_rows DIALINGs updated|\n";}
							}
						if ( ($command_line[3] =~ /^Channel: /i) && ($command_line[6] =~ /^CalleridName: /i) ) ### post 2006-03-20 -- Added Timestamp line
							{
							$channel = $command_line[3];
							$channel =~ s/Channel: |\s*$//gi;
							$callid = $command_line[6];
							$callid =~ s/CalleridName: |\s*$//gi;
							   $callid =~ s/^\"//gi;   $callid =~ s/\".*$//gi;
							   if ($callid =~ /\S\S\S\S\S\S\S\S\S\S\S\S\S\S\S\S\S\S/) {$callid =~ s/ .*//gi;}
							$uniqueid = $command_line[7];
							$uniqueid =~ s/Uniqueid: |\s*$//gi;
							$stmtA = "UPDATE vicidial_manager set status='SENT', channel='$channel', uniqueid = '$uniqueid' where server_ip = '$server_ip' and callerid = '$callid'";
							print STDERR "|$stmtA|\n";
						    my $affected_rows = $dbhA->do($stmtA);
							if($DB){print "|$affected_rows DIALINGs updated|\n";}
							}
						}
					if ($input_lines[$ILcount] =~ /State: Ringing|State: Up/)
						{
						if ( ($command_line[1] =~ /^Channel: /i) && ($command_line[4] =~ /^Uniqueid: /i) ) ### pre 2004-10-07 CVS
							{
							$channel = $command_line[1];
							$channel =~ s/Channel: |\s*$//gi;
							$callid = $command_line[3];
							$callid =~ s/Callerid: |\s*$//gi;
							   $callid =~ s/^\"//gi;   $callid =~ s/\".*$//gi;
							   if ($callid =~ /\S\S\S\S\S\S\S\S\S\S\S\S\S\S\S\S\S\S/) {$callid =~ s/ .*//gi;}
							$uniqueid = $command_line[4];
							$uniqueid =~ s/Uniqueid: |\s*$//gi;
							$stmtA = "UPDATE vicidial_manager set status='UPDATED', channel='$channel', uniqueid = '$uniqueid' where server_ip = '$server_ip' and callerid = '$callid'";
							if ($channel !~ /local/i)
								{
								print STDERR "|$stmtA|\n";
								my $affected_rows = $dbhA->do($stmtA);
								if($DB){print "|$affected_rows RINGINGs updated|\n";}
								}
							}
						if ( ($command_line[1] =~ /^Channel: /i) && ($command_line[4] =~ /^CalleridName: /i) ) ### post 2004-10-07 CVS
							{
							$channel = $command_line[1];
							$channel =~ s/Channel: |\s*$//gi;
							$callid = $command_line[4];
							$callid =~ s/CalleridName: |\s*$//gi;
							   $callid =~ s/^\"//gi;   $callid =~ s/\".*$//gi;
							   if ($callid =~ /\S\S\S\S\S\S\S\S\S\S\S\S\S\S\S\S\S\S/) {$callid =~ s/ .*//gi;}
							$uniqueid = $command_line[5];
							$uniqueid =~ s/Uniqueid: |\s*$//gi;
							$stmtA = "UPDATE vicidial_manager set status='UPDATED', channel='$channel', uniqueid = '$uniqueid' where server_ip = '$server_ip' and callerid = '$callid'";
							if ($channel !~ /local/i)
								{
								print STDERR "|$stmtA|\n";
								my $affected_rows = $dbhA->do($stmtA);
								if($DB){print "|$affected_rows RINGINGs updated|\n";}
								}
							}
						if ( ($command_line[2] =~ /^Channel: /i) && ($command_line[5] =~ /^CalleridName: /i) ) ### post 2005-08-07 CVS
							{
							$channel = $command_line[2];
							$channel =~ s/Channel: |\s*$//gi;
							$callid = $command_line[5];
							$callid =~ s/CalleridName: |\s*$//gi;
							   $callid =~ s/^\"//gi;   $callid =~ s/\".*$//gi;
							   if ($callid =~ /\S\S\S\S\S\S\S\S\S\S\S\S\S\S\S\S\S\S/) {$callid =~ s/ .*//gi;}
							$uniqueid = $command_line[6];
							$uniqueid =~ s/Uniqueid: |\s*$//gi;
							$stmtA = "UPDATE vicidial_manager set status='UPDATED', channel='$channel', uniqueid = '$uniqueid' where server_ip = '$server_ip' and callerid = '$callid'";
							if ($channel !~ /local/i)
								{
								print STDERR "|$stmtA|\n";
								my $affected_rows = $dbhA->do($stmtA);
								if($DB){print "|$affected_rows RINGINGs updated|\n";}
								}
							}
						if ( ($command_line[3] =~ /^Channel: /i) && ($command_line[6] =~ /^CalleridName: /i) ) ### post 2006-03-20 SVN -- Added Timestamp line
							{
							$channel = $command_line[3];
							$channel =~ s/Channel: |\s*$//gi;
							$callid = $command_line[6];
							$callid =~ s/CalleridName: |\s*$//gi;
							   $callid =~ s/^\"//gi;   $callid =~ s/\".*$//gi;
							   if ($callid =~ /\S\S\S\S\S\S\S\S\S\S\S\S\S\S\S\S\S\S/) {$callid =~ s/ .*//gi;}
							$uniqueid = $command_line[7];
							$uniqueid =~ s/Uniqueid: |\s*$//gi;
							$stmtA = "UPDATE vicidial_manager set status='UPDATED', channel='$channel', uniqueid = '$uniqueid' where server_ip = '$server_ip' and callerid = '$callid'";
							if ($channel !~ /local/i)
								{
								print STDERR "|$stmtA|\n";
								my $affected_rows = $dbhA->do($stmtA);
								if($DB){print "|$affected_rows RINGINGs updated|\n";}
								}
							}
						}
		
					if ($input_lines[$ILcount] =~ /Event: Newcallerid/)
						{
						if ( ($command_line[1] =~ /^Channel: /i) && ($command_line[3] =~ /^Uniqueid: /i) ) 
							{
							$channel = $command_line[1];
							$channel =~ s/Channel: |\s*$//gi;
							$callid = $command_line[2];
							$callid =~ s/Callerid: |\s*$//gi;
							   $callid =~ s/^\"//gi;   $callid =~ s/\".*$//gi;
							   if ($callid =~ /\S\S\S\S\S\S\S\S\S\S\S\S\S\S\S\S\S\S/) {$callid =~ s/ .*//gi;}
							$uniqueid = $command_line[3];
							$uniqueid =~ s/Uniqueid: |\s*$//gi;
							$stmtA = "UPDATE vicidial_manager set status='UPDATED', channel='$channel', uniqueid = '$uniqueid' where server_ip = '$server_ip' and callerid = '$callid'";
							if ($channel =~ /local/i)
								{
								print STDERR "|$stmtA|\n";
								my $affected_rows = $dbhA->do($stmtA);
								if($DB){print "|$affected_rows RINGINGs updated|\n";}
								}
							}
						if ( ($command_line[3] =~ /^Channel: /i) && ($command_line[6] =~ /^Uniqueid: /i) ) ### post 2006-03-20 SVN -- Added Timestamp line
							{
							$channel = $command_line[3];
							$channel =~ s/Channel: |\s*$//gi;
							$callid = $command_line[5];
							$callid =~ s/Callerid: |\s*$//gi;
					#		$callid =~ s/CallerIDName: |\s*$//gi;
							   $callid =~ s/^\"//gi;   $callid =~ s/\".*$//gi;
							   if ($callid =~ /\S\S\S\S\S\S\S\S\S\S\S\S\S\S\S\S\S\S/) {$callid =~ s/ .*//gi;}
							$uniqueid = $command_line[6];
							$uniqueid =~ s/Uniqueid: |\s*$//gi;
							$stmtA = "UPDATE vicidial_manager set status='UPDATED', channel='$channel', uniqueid = '$uniqueid' where server_ip = '$server_ip' and callerid = '$callid'";
							if ($channel =~ /local/i)
								{
								print STDERR "|$stmtA|\n";
								my $affected_rows = $dbhA->do($stmtA);
								if($DB){print "|$affected_rows RINGINGs updated|\n";}
								}
							}
					#	if ( ($command_line[2] =~ /^Channel: /i) && ($command_line[5] =~ /^Uniqueid: /i) ) ### post 2006-06-21 SVN -- Changed from CallerID to CallerIDName
					#		{
					#		$channel = $command_line[2];
					#		$channel =~ s/Channel: |\s*$//gi;
					#		$callid = $command_line[4];
					#		$callid =~ s/CallerIDName: |\s*$//gi;
					#		   $callid =~ s/^\"//gi;   $callid =~ s/\".*$//gi;
					#		$uniqueid = $command_line[5];
					#		$uniqueid =~ s/Uniqueid: |\s*$//gi;
					#		$stmtA = "UPDATE vicidial_manager set status='UPDATED', channel='$channel', uniqueid = '$uniqueid' where server_ip = '$server_ip' and callerid = '$callid'";
					#		if ($channel =~ /local/i)
					#			{
					#			print STDERR "|$stmtA|\n";
					#			my $affected_rows = $dbhA->do($stmtA);
					#			if($DB){print "|$affected_rows RINGINGs updated|\n";}
					#			}
					#		}
						}
					if ($input_lines[$ILcount] =~ /Event: CPD-Result/)
						{
						#	Event: CPD-Result
						#	Privilege: system,all
						#	ChannelDriver: SIP
						#	Channel: SIP/paraxip-out-08291448
						#	CallerIDName: V0202034729000030735
						#	Uniqueid: 1233564450.141
						#	Result: Answering-Machine
						if ( ($command_line[3] =~ /^Channel: /i) && ($command_line[5] =~ /^Uniqueid: /i) ) 
							{
								&get_time_now;

							$channel = $command_line[3];
							$channel =~ s/Channel: |\s*$//gi;
							$callid = $command_line[4];
							$callid =~ s/CallerIDName: |\s*$//gi;
							   $callid =~ s/^\"//gi;   $callid =~ s/\".*$//gi;
							   if ($callid =~ /\S\S\S\S\S\S\S\S\S\S\S\S\S\S\S\S\S\S/) {$callid =~ s/ .*//gi;}
							$uniqueid = $command_line[5];
							$uniqueid =~ s/Uniqueid: |\s*$//gi;
							$result = $command_line[6];
							$result =~ s/Result: |\s*$//gi;
							if (length($result)>0)
								{
								# 2011-03-22 13:22:12.123   (1277187888 123 456)
								# ALTER TABLE vicidial_cpd_log ADD hires_time VARCHAR(26) default '';
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

								$lead_id = substr($callid, 10, 10);
								$lead_id = ($lead_id + 0);
							#	$stmtA = "INSERT INTO vicidial_cpd_log set channel='$channel', uniqueid='$uniqueid', callerid='$callid', server_ip='$server_ip', lead_id='$lead_id', event_date='$now_date', result='$result', hires_time='$HRnow_date';";
								$stmtA = "INSERT INTO vicidial_cpd_log set channel='$channel', uniqueid='$uniqueid', callerid='$callid', server_ip='$server_ip', lead_id='$lead_id', event_date='$now_date', result='$result';";
								print STDERR "|$stmtA|\n";
								my $affected_rows = $dbhA->do($stmtA);
								if($DB){print "|$affected_rows CPD_log inserted|$HRnow_date|$s_hires|$usec|\n";}
								}
							}
						}

					}
				$ILcount++;
				}

			}


	$endless_loop--;
	$keepalive_count_loop++;
		if($DB){print STDERR "loop counter: |$endless_loop|$keepalive_count_loop|\r";}

		### putting a blank file called "sendmgr.kill" in a directory will automatically safely kill this program
		if ( (-e "$PATHhome/listenmgr.kill") or ($sendonlyone) )
			{
			unlink("$PATHhome/listenmgr.kill");
			$endless_loop=0;
			$one_day_interval=0;
			print "\nPROCESS KILLED MANUALLY... EXITING\n\n";
			}

		### run a keepalive command to flush whatever is in the buffer through and to keep the connection alive
		### Also, keep the MySQL connection alive by selecting the server_updater time for this server
		if ($endless_loop =~ /00$|50$/) 
			{
				&get_time_now;

			### Grab Server values from the database
				$stmtA = "SELECT vd_server_logs FROM servers where server_ip = '$VARserver_ip';";
				$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
				$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
				$sthArows=$sthA->rows;
				if ($sthArows > 0)
					{
					@aryA = $sthA->fetchrow_array;
					$DBvd_server_logs =			"$aryA[0]";
					if ($DBvd_server_logs =~ /Y/)	{$SYSLOG = '1';}
						else {$SYSLOG = '0';}
					}
				$sthA->finish();

			### Grab Server values to keep DB connection alive
			$stmtA = "SELECT last_update FROM server_updater where server_ip = '$server_ip';";
			$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
			$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
			@aryA = $sthA->fetchrow_array;
				$last_update	=		"$aryA[0]";
			$sthA->finish();

			@list_lines = $tn->cmd(String => "Action: Command\nCommand: show uptime\n\n", Prompt => '/--END COMMAND--.*/', Errmode    => Return, Timeout    => 1); 
			if($DB){print "input lines: $#list_lines\n";}

			if($DB){print "+++++++++++++++++++++++++++++++sending keepalive transmit line $endless_loop|$now_date|$last_update|\n";}
			$keepalive_count_loop=0;

			}
		}

	if($DB){print "DONE... Exiting... Goodbye... See you later... Not really, initiating next loop...$one_day_interval left\n";}

	$event_string='HANGING UP|';
	&event_logger;

	@hangup = $tn->cmd(String => "Action: Logoff\n\n", Prompt => "/.*/", Errmode    => Return, Timeout    => 1); 

	$tn->buffer_empty;
	$tn->waitfor(Match => '/Message:.*\n\n/', Timeout => 10);
	$ok = $tn->close;

	$one_day_interval--;
	}

$event_string='CLOSING DB CONNECTION|';
&event_logger;


$dbhA->disconnect();


if($DB){print "DONE... Exiting... Goodbye... See you later... Really I mean it this time\n";}


exit;







sub get_time_now	#get the current date and time and epoch for logging call lengths and datetimes
	{
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
	$action_log_date = "$year-$mon-$mday";
	}





sub event_logger 
	{
	if ($SYSLOG)
		{
		### open the log file for writing ###
		open(Lout, ">>$PATHlogs/listen_process.$action_log_date")
				|| die "Can't open $PATHlogs/listen_process.$action_log_date: $!\n";
		print Lout "$now_date|$event_string|\n";
		close(Lout);
		}
	$event_string='';
	}




sub manager_output_logger
	{
	if ($SYSLOG)
		{
		open(MOout, ">>$PATHlogs/listen.$action_log_date")
				|| die "Can't open $PATHlogs/listen.$action_log_date: $!\n";
		print MOout "$now_date|$manager_string|\n";
		close(MOout);
		}
	}
