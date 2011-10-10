#!/usr/bin/perl
#
# AST_conf_update.pl version 2.4
#
# This script checks if there are channels in reserved conferences
#
# Copyright (C) 2010  Matt Florell <vicidial@gmail.com>    LICENSE: AGPLv2
#
# 50810-1532 - Added database server variable definitions lookup
# 50823-1456 - Added commandline arguments for debug at runtime
# 60717-1135 - changed to DBI by Marin Blu
# 60717-1536 - changed to use /etc/astguiclient.conf for configs
# 80506-1055 - Added check for vicidial_conferences in 3WAY
# 80914-1533 - Added kickall for leave-3way calls after one hour
# 81008-0937 - Added kickall from vicidial_conferences if only one participant
# 100625-1220 - Added waitfors after logout to fix broken pipe errors in asterisk <MikeC>
# 100811-2054 - Added --no-vc-3way-check flag to use when AST_conf_update_3way.pl script is used
# 100928-1506 - Changed from hard-coded 60 minute limit to servers.vicidial_recording_limit
#

# constants
$DB=0;  # Debug flag, set to 0 for no debug messages per minute
$US='__';
$MT[0]='';
$no_vc_3way_check=0;

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
		print "  [--no-vc-3way-check] = separate 3way script is running\n";
		print "\n";
		exit;
		}
	else
		{
		if ($args =~ /-debug/i)
			{
			$DB=1; # Debug flag
			print "-- DEBUGGING ENABLED --\n\n";
			}
		if ($args =~ /-no-vc-3way-check/i)
			{
			$no_vc_3way_check=1; # no 3way check flag
			if ($DB > 0) {print "-- NO VC 3way check flag ENABLED --\n\n";}
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

use Time::HiRes ('gettimeofday','usleep','sleep');  # necessary to have perl sleep command of less than one second
use DBI;
use Net::Telnet ();
	  
$dbhA = DBI->connect("DBI:mysql:$VARDB_database:$VARDB_server:$VARDB_port", "$VARDB_user", "$VARDB_pass")
 or die "Couldn't connect to database: " . DBI->errstr;

### Grab Server values from the database
$stmtA = "SELECT telnet_host,telnet_port,ASTmgrUSERNAME,ASTmgrSECRET,ASTmgrUSERNAMEupdate,ASTmgrUSERNAMElisten,ASTmgrUSERNAMEsend,max_vicidial_trunks,answer_transfer_agent,local_gmt,ext_context,vicidial_recording_limit FROM servers where server_ip = '$server_ip';";
if ($DB) {print "|$stmtA|\n";}
$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
$sthArows=$sthA->rows;
$rec_count=0;
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
	$vicidial_recording_limit = $aryA[11];
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
	if ($vicidial_recording_limit < 60) {$vicidial_recording_limit=60;}
	}
 $sthA->finish(); 


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

##### Find date-time one hour in the past
$secX = time();
$TDtarget = ($secX - ($vicidial_recording_limit * 60));
($Tsec,$Tmin,$Thour,$Tmday,$Tmon,$Tyear,$Twday,$Tyday,$Tisdst) = localtime($TDtarget);
$Tyear = ($Tyear + 1900);
$Tmon++;
if ($Tmon < 10) {$Tmon = "0$Tmon";}
if ($Tmday < 10) {$Tmday = "0$Tmday";}
if ($Thour < 10) {$Thour = "0$Thour";}
if ($Tmin < 10) {$Tmin = "0$Tmin";}
if ($Tsec < 10) {$Tsec = "0$Tsec";}
$TDSQLdate = "$Tyear-$Tmon-$Tmday $Thour:$Tmin:$Tsec";
$TDnum = "$Tyear$Tmon$Tmday$Thour$Tmin$Tsec";

######################################################################
##### CLEAR vicidial_conferences ENTRIES IN LEAVE-3WAY FOR MORE THAN ONE HOUR
######################################################################
@PTextensions=@MT; @PT_conf_extens=@MT; @PTmessages=@MT; @PTold_messages=@MT; @NEW_messages=@MT; @OLD_messages=@MT;
$stmtA = "SELECT conf_exten,extension from vicidial_conferences where server_ip='$server_ip' and leave_3way='1' and leave_3way_datetime < \"$TDSQLdate\";";
if ($DB) {print "|$stmtA|\n";}
$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
$sthArows=$sthA->rows;
$rec_count=0;
while ($sthArows > $rec_count)
	{
	@aryA = $sthA->fetchrow_array;
	$PT_conf_extens[$rec_count] =	 $aryA[0];
	$PT_extensions[$rec_count] =	 $aryA[1];
		if ($DB) {print "|$PT_conf_extens[$rec_count]|$PT_extensions[$rec_count]|\n";}
	$rec_count++;
	}
$sthA->finish();
$k=0;
while ($k < $rec_count)
	{
	$local_DEF = 'Local/5555';
	$local_AMP = '@';
	$kick_local_channel = "$local_DEF$PT_conf_extens[$k]$local_AMP$ext_context";
	$queryCID = "ULGC35$TDnum";

	$stmtA="INSERT INTO vicidial_manager values('','','$now_date','NEW','N','$server_ip','','Originate','$queryCID','Channel: $kick_local_channel','Context: $ext_context','Exten: 8300','Priority: 1','Callerid: $queryCID','','','','','');";
		$affected_rows = $dbhA->do($stmtA); #  or die  "Couldn't execute query:|$stmtA|\n";

	$stmtA = "UPDATE vicidial_conferences set extension='',leave_3way='0' where server_ip='$server_ip' and conf_exten='$PT_conf_extens[$k]';";
		if($DB){print STDERR "\n|$stmtA|\n";}
	$affected_rows = $dbhA->do($stmtA); #  or die  "Couldn't execute query:|$stmtA|\n";

	$k++;
	}



if ($no_vc_3way_check < 1)
	{
	######################################################################
	##### CHECK vicidial_conferences TABLE #####
	######################################################################
	@PTextensions=@MT; @PT_conf_extens=@MT; @PTmessages=@MT; @PTold_messages=@MT; @NEW_messages=@MT; @OLD_messages=@MT;
	$stmtA = "SELECT extension,conf_exten from vicidial_conferences where server_ip='$server_ip' and leave_3way='1';";
	if ($DB) {print "|$stmtA|\n";}
	$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
	$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
	$sthArows=$sthA->rows;
	$rec_count=0;
	while ($sthArows > $rec_count)
		{
		@aryA = $sthA->fetchrow_array;
		$PTextensions[$rec_count] =		 $aryA[0];
		$PT_conf_extens[$rec_count] =	 $aryA[1];
			if ($DB) {print "|$PT_conf_extens[$rec_count]|$PTextensions[$rec_count]|\n";}
		$rec_count++;
		}
	$sthA->finish(); 

	if (!$telnet_port) {$telnet_port = '5038';}

	### connect to asterisk manager through telnet
	$t = new Net::Telnet (Port => $telnet_port,
						  Prompt => '/.*[\$%#>] $/',
						  Output_record_separator => '',);
	#$fh = $t->dump_log("$telnetlog");  # uncomment for telnet log
		if (length($ASTmgrUSERNAMEsend) > 3) {$telnet_login = $ASTmgrUSERNAMEsend;}
		else {$telnet_login = $ASTmgrUSERNAME;}

	$t->open("$telnet_host"); 
	$t->waitfor('/[01]\n$/');			# print login
	$t->print("Action: Login\nUsername: $telnet_login\nSecret: $ASTmgrSECRET\n\n");
	$t->waitfor('/Authentication accepted/');		# waitfor auth accepted


	$i=0;
	foreach(@PTextensions)
		{
		@list_channels=@MT;
		$t->buffer_empty;
		$COMMAND = "Action: Command\nCommand: Meetme list $PT_conf_extens[$i]\n\nAction: Ping\n\n";
		if ($DB) {print "|$PT_conf_extens[$i]|$COMMAND|\n";}
		@list_channels = $t->cmd(String => "$COMMAND", Prompt => '/Response: Pong.*/'); 


		$j=0;
		$conf_empty[$i]=0;
		$conf_users[$i]='';
		foreach(@list_channels)
			{
			if($DB){print "|$list_channels[$j]|\n";}
			### mark all empty conferences and conferences with only one channel as empty
			if ($list_channels[$j] =~ /No active conferences|No such conference/i)
				{$conf_empty[$i]++;}
			if ($list_channels[$j] =~ /1 users in that conference/i)
				{$conf_empty[$i]++;}
			$j++;
			}

		if($DB){print "Meetme list $PT_conf_extens[$i]-  Exten:|$PTextensions[$i]| Empty:|$conf_empty[$i]|    ";}
		if (!$conf_empty[$i])
			{
			if($DB){print "CONFERENCE STILL HAS PARTICIPANTS, DOING NOTHING FOR THIS CONFERENCE\n";}
			if ($PTextensions[$i] =~ /Xtimeout\d$/i) 
				{
				$PTextensions[$i] =~ s/Xtimeout\d$//gi;
				$stmtA = "UPDATE vicidial_conferences set extension='$PTextensions[$i]' where server_ip='$server_ip' and conf_exten='$PT_conf_extens[$i]';";
					if($DB){print STDERR "\n|$stmtA|\n";}
				$affected_rows = $dbhA->do($stmtA); #  or die  "Couldn't execute query:|$stmtA|\n";
				}
			}
		else
			{
			$NEWexten[$i] = $PTextensions[$i];
			$leave_3waySQL='1';
			if ($PTextensions[$i] =~ /Xtimeout3$/i) {$NEWexten[$i] =~ s/Xtimeout3$/Xtimeout2/gi;}
			if ($PTextensions[$i] =~ /Xtimeout2$/i) {$NEWexten[$i] =~ s/Xtimeout2$/Xtimeout1/gi;}
			if ($PTextensions[$i] =~ /Xtimeout1$/i) {$NEWexten[$i] = ''; $leave_3waySQL='0';}
			if ( ($PTextensions[$i] !~ /Xtimeout\d$/i) and (length($PTextensions[$i])> 0) ) {$NEWexten[$i] .= 'Xtimeout3';}

			if ($NEWexten[$i] =~ /Xtimeout1$/i)
				{
				### Kick all participants if there are any left in the conference so it can be reused
				$local_DEF = 'Local/5555';
				$local_AMP = '@';
				$kick_local_channel = "$local_DEF$PT_conf_extens[$i]$local_AMP$ext_context";
				$queryCID = "ULGC36$TDnum";

				$stmtA="INSERT INTO vicidial_manager values('','','$now_date','NEW','N','$server_ip','','Originate','$queryCID','Channel: $kick_local_channel','Context: $ext_context','Exten: 8300','Priority: 1','Callerid: $queryCID','','','','','');";
					$affected_rows = $dbhA->do($stmtA); #  or die  "Couldn't execute query:|$stmtA|\n";
				if($DB){print STDERR "\n|$affected_rows|$stmtA|\n";}
				}

			$stmtA = "UPDATE vicidial_conferences set extension='$NEWexten[$i]',leave_3way='$leave_3waySQL' where server_ip='$server_ip' and conf_exten='$PT_conf_extens[$i]';";
				if($DB){print STDERR "\n|$stmtA|\n";}
			$affected_rows = $dbhA->do($stmtA); #  or die  "Couldn't execute query:|$stmtA|\n";
			}

		$i++;
			### sleep for 10 hundredths of a second
			usleep(1*100*1000);
		}

	$t->buffer_empty;
	@hangup = $t->cmd(String => "Action: Logoff\n\n", Prompt => "/.*/"); 
	$t->buffer_empty;
	$t->waitfor(Match => '/Message:.*\n\n/', Timeout => 10);
	$ok = $t->close;


	sleep(5);
	}






######################################################################
##### CHECK conferences TABLE #####
######################################################################
@PTextensions=@MT; @PT_conf_extens=@MT; @PTmessages=@MT; @PTold_messages=@MT; @NEW_messages=@MT; @OLD_messages=@MT;
$stmtA = "SELECT extension,conf_exten from conferences where server_ip='$server_ip' and extension is NOT NULL and extension != '';";
if ($DB) {print "|$stmtA|\n";}
$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
$sthArows=$sthA->rows;
$rec_count=0;
while ($sthArows > $rec_count)
    {
	@aryA = $sthA->fetchrow_array;
	$PTextensions[$rec_count] =		 $aryA[0];
	$PT_conf_extens[$rec_count] =	 $aryA[1];
		if ($DB) {print "|$PT_conf_extens[$rec_count]|$PTextensions[$rec_count]|\n";}
	$rec_count++;
	}
$sthA->finish(); 

if (!$telnet_port) {$telnet_port = '5038';}

### connect to asterisk manager through telnet
$t = new Net::Telnet (Port => $telnet_port,
					  Prompt => '/.*[\$%#>] $/',
					  Output_record_separator => '',);
#$fh = $t->dump_log("$telnetlog");  # uncomment for telnet log
if (length($ASTmgrUSERNAMEsend) > 3) {$telnet_login = $ASTmgrUSERNAMEsend;}
else {$telnet_login = $ASTmgrUSERNAME;}

$t->open("$telnet_host"); 
$t->waitfor('/[01]\n$/');			# print login
$t->print("Action: Login\nUsername: $telnet_login\nSecret: $ASTmgrSECRET\n\n");
$t->waitfor('/Authentication accepted/');		# waitfor auth accepted


$i=0;
foreach(@PTextensions)
	{
	if (length($PT_conf_extens[$i]) > 0)
		{
		@list_channels=@MT;
		$t->buffer_empty;
		$COMMAND = "Action: Command\nCommand: Meetme list $PT_conf_extens[$i]\n\nAction: Ping\n\n";
		if ($DB) {print "|$PT_conf_extens[$i]|$COMMAND|\n";}
		@list_channels = $t->cmd(String => "$COMMAND", Prompt => '/Response: Pong.*/'); 


		$j=0;
		$conf_empty[$i]=0;
		$conf_users[$i]='';
		foreach(@list_channels)
			{
			if($DB){print "|$list_channels[$j]|\n";}
			if ($list_channels[$j] =~ /No active conferences|No such conference/i)
				{$conf_empty[$i]++;}
	#		if ($list_channels[$j] =~ /^User /i)
	#			{
	#			$userx = '';
	#			$userx = $list_channels[$j];
	#			$userx =~ s/User \#: //gi;
	#			$conf_users[$i] .= "$userx|";
	#			}
			$j++;
			}

		if($DB){print "Meetme list $PT_conf_extens[$i]-  Exten:|$PTextensions[$i]| Empty:|$conf_empty[$i]|    ";}
		if (!$conf_empty[$i])
			{
			if($DB){print "CONFERENCE STILL HAS PARTICIPANTS, DOING NOTHING FOR THIS CONFERENCE\n";}
			if ($PTextensions[$i] =~ /Xtimeout\d$/i) 
				{
				$PTextensions[$i] =~ s/Xtimeout\d$//gi;
				$stmtA = "UPDATE conferences set extension='$PTextensions[$i]' where server_ip='$server_ip' and conf_exten='$PT_conf_extens[$i]';";
					if($DB){print STDERR "\n|$stmtA|\n";}
				$affected_rows = $dbhA->do($stmtA); #  or die  "Couldn't execute query:|$stmtA|\n";
				}
			}
		else
			{
			$NEWexten[$i] = $PTextensions[$i];
			if ($PTextensions[$i] =~ /Xtimeout3$/i) {$NEWexten[$i] =~ s/Xtimeout3$/Xtimeout2/gi;}
			if ($PTextensions[$i] =~ /Xtimeout2$/i) {$NEWexten[$i] =~ s/Xtimeout2$/Xtimeout1/gi;}
			if ($PTextensions[$i] =~ /Xtimeout1$/i) {$NEWexten[$i] = '';}
			if ( ($PTextensions[$i] !~ /Xtimeout\d$/i) and (length($PTextensions[$i])> 0) ) {$NEWexten[$i] .= 'Xtimeout3';}


			$stmtA = "UPDATE conferences set extension='$NEWexten[$i]' where server_ip='$server_ip' and conf_exten='$PT_conf_extens[$i]';";
				if($DB){print STDERR "\n|$stmtA|\n";}
			$affected_rows = $dbhA->do($stmtA); #  or die  "Couldn't execute query:|$stmtA|\n";
			}
		}
	$i++;
		### sleep for 10 hundredths of a second
		usleep(1*100*1000);
	}


$t->buffer_empty;
@hangup = $t->cmd(String => "Action: Logoff\n\n", Prompt => "/.*/"); 
$t->buffer_empty;
$t->waitfor(Match => '/Message:.*\n\n/', Timeout => 10);
$ok = $t->close;

$dbhA->disconnect();

if($DB){print "DONE... Exiting... Goodbye... See you later... \n";}

exit;

