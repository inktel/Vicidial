#!/usr/bin/perl
#
# AST_VDremote_agents.pl version 2.4
#
# SUMMARY:
# To use VICIDIAL with remote agents, this must always be running 
# 
# This program must be run on each local Asterisk machine that has Remote Agents
#
# This script is to run perpetually querying every second to update the remote 
# agents that should appear to be logged in so that the calls can be transferred 
# out to them properly.
#
# Copyright (C) 2011  Matt Florell <vicidial@gmail.com>    LICENSE: AGPLv2
#
# CHANGELOG:
# 50215-0954 - First version of script
# 50810-1615 - Added database server variable definitions lookup
# 60807-1003 - Changed to DBI
#            - Changed to use /etc/astguiclient.conf for configs
# 60814-1726 - Added option for no logging to file
# 61012-1025 - Added performance testing options
# 61110-1443 - Added user_level from vicidial_user record into vicidial_live_agents
# 70213-1306 - Added queuemetrics logging
# 70214-1243 - Added queuemetrics_log_id field to queue_log logging
# 70222-1606 - Changed queue_log PAUSE/UNPAUSE to PAUSEALL/UNPAUSEALL
# 70417-1346 - Fixed bug that would add unneeded simulated agent lines
# 80128-0105 - Fixed calls_today bug
# 81007-1746 - Added debugX output for count statements and changed to if from while
# 81026-1246 - Added better logging of calls and fixed the DROP bug
# 90808-0247 - Added agent last_state_change field
# 91013-1128 - Added full inbound compatibility with vicidial_live_inbound_agents table
# 91123-1802 - Added outbound_autodial field
# 100309-0555 - Added queuemetrics_loginout option
# 100318-2307 - Added ra_user field input to vla table
# 100524-1542 - Fixed live call detection bug on multi-server systems
# 100622-0917 - Added start_call_url function for remote agents, this launches a separate child script
# 101108-0032 - Added ADDMEMBER queue_log code
# 110103-1230 - Added queuemetrics_loginout NONE option
# 110124-1134 - Small query fix for large queue_log tables
# 110224-1903 - Added compatibility with QM phone environment logging
# 110304-0007 - Added agent on-hook compatibility
# 110626-0030 - Added queuemetrics_pe_phone_append
# 110707-1342 - Added last_inbound_call_time to next agent call options for inbound
#

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
		print "  [-v] = verbose debug messages\n";
		print "  [--delay=XXX] = delay of XXX seconds per loop, default 2 seconds\n\n";
		}
	else
		{
		if ($args =~ /--delay=/i)
			{
			@data_in = split(/--delay=/,$args);
			$loop_delay = $data_in[1];
			print "     LOOP DELAY OVERRIDE!!!!! = $loop_delay seconds\n\n";
			$loop_delay = ($loop_delay * 1000);
			}
		else
			{
			$loop_delay = '2000';
			}
		if ($args =~ /--debug/i)
			{
			$DB=1;
			print "\n-----DEBUGGING -----\n\n";
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
	print "no command line options set\n";
	$loop_delay = '2000';
	}
### end parsing run-time options ###


# constants
$US='__';
$MT[0]='';
$local_DEF = 'Local/';
$conf_silent_prefix = '7';
$local_AMP = '@';
$agents = '@agents';

# default path to astguiclient configuration file:
$PATHconf =	'/etc/astguiclient.conf';

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


&get_time_now;	# update time/date variables

if (!$VDRLOGfile) {$VDRLOGfile = "$PATHlogs/remoteagent.$year-$mon-$mday";}
if (!$VARDB_port) {$VARDB_port='3306';}

use Time::HiRes ('gettimeofday','usleep','sleep');  # necessary to have perl sleep command of less than one second
use DBI;	  

$dbhA = DBI->connect("DBI:mysql:$VARDB_database:$VARDB_server:$VARDB_port", "$VARDB_user", "$VARDB_pass")
 or die "Couldn't connect to database: " . DBI->errstr;

### Grab Server values from the database
$stmtA = "SELECT vd_server_logs,local_gmt,ext_context FROM servers where server_ip = '$VARserver_ip';";
$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
$sthArows=$sthA->rows;
if ($sthArows > 0)
	{
	@aryA = $sthA->fetchrow_array;
	$DBvd_server_logs =		$aryA[0];
	$DBSERVER_GMT =			$aryA[1];
	$ext_context =			$aryA[2];
	if ($DBvd_server_logs =~ /Y/)	{$SYSLOG = '1';}
	else {$SYSLOG = '0';}
	if (length($DBSERVER_GMT)>0)	{$SERVER_GMT = $DBSERVER_GMT;}
	}
$sthA->finish();


&get_time_now;	# update time/date variables

$event_string='PROGRAM STARTED||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||';
&event_logger;	# writes to the log and if debug flag is set prints to STDOUT


$event_string='LOGGED INTO MYSQL SERVER ON 1 CONNECTION|';
&event_logger;

$one_day_interval = 12;		# 1 month loops for one year 
while($one_day_interval > 0)
	{
	$endless_loop=5760000;		# 30 days minutes at XXX seconds per loop
	while($endless_loop > 0)
		{
		&get_time_now;

		$VDRLOGfile = "$PATHlogs/remoteagent.$year-$mon-$mday";

		if ($endless_loop =~ /0$|5$/)
			{
			### Grab Server values from the database
			$stmtA = "SELECT vd_server_logs FROM servers where server_ip = '$VARserver_ip';";
			$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
			$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
			$sthArows=$sthA->rows;
			if ($sthArows > 0)
				{
				@aryA = $sthA->fetchrow_array;
				$DBvd_server_logs =			$aryA[0];
				if ($DBvd_server_logs =~ /Y/)	{$SYSLOG = '1';}
				else {$SYSLOG = '0';}
				}
			$sthA->finish();

			#############################################
			##### START QUEUEMETRICS LOGGING LOOKUP #####
			$stmtA = "SELECT enable_queuemetrics_logging,queuemetrics_server_ip,queuemetrics_dbname,queuemetrics_login,queuemetrics_pass,queuemetrics_log_id,queuemetrics_loginout,queuemetrics_addmember_enabled,queuemetrics_pe_phone_append FROM system_settings;";
			$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
			$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
			$sthArows=$sthA->rows;
			if ($sthArows > 0)
				{
				@aryA = $sthA->fetchrow_array;
				$enable_queuemetrics_logging =		$aryA[0];
				$queuemetrics_server_ip	=			$aryA[1];
				$queuemetrics_dbname =				$aryA[2];
				$queuemetrics_login	=				$aryA[3];
				$queuemetrics_pass =				$aryA[4];
				$queuemetrics_log_id =				$aryA[5];
				$queuemetrics_loginout =			$aryA[6];
				$queuemetrics_addmember_enabled =	$aryA[7];
				$queuemetrics_pe_phone_append =		$aryA[8];
				}
			$sthA->finish();
			##### END QUEUEMETRICS LOGGING LOOKUP #####
			###########################################


			##### grab paused remote agents on this server, then we will delete them
			@VLApausedUSER = @MT;
			$stmtA = "SELECT user FROM vicidial_live_agents where server_ip='$server_ip' and status IN('PAUSED') and extension LIKE \"R/%\";";
			$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
			$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
			$vla_PAUSED_ct=$sthA->rows;
			$r=0;
			while ($vla_PAUSED_ct > $r)
				{
				@aryA = $sthA->fetchrow_array;
				$VLApausedUSER[$r] =	$aryA[0];
				$r++;
				}
			$sthA->finish();

			$r=0;
			$VLAaffected_rows=0;
			$VLIAaffected_rows=0;
			$VLAaffected_rowsTOTAL=0;
			$VLIAaffected_rowsTOTAL=0;
			while ($vla_PAUSED_ct > $r)
				{
				### delete vicidial_live_agents records for paused remote users
				$stmtA = "DELETE FROM vicidial_live_agents where user='$VLApausedUSER[$r]' and server_ip='$server_ip' and status IN('PAUSED') and extension LIKE \"R/%\";";
				$VLAaffected_rows = $dbhA->do($stmtA);
				### delete vicidial_live_inbound_agents records for paused remote users
				$stmtA = "DELETE FROM vicidial_live_inbound_agents where user='$VLApausedUSER[$r]';";
				$VLIAaffected_rows = $dbhA->do($stmtA);
				$VLAaffected_rowsTOTAL = ($VLAaffected_rows + $VLAaffected_rowsTOTAL);
				$VLIAaffected_rowsTOTAL = ($VLIAaffected_rows + $VLIAaffected_rowsTOTAL);
				$r++;
				}

			if ( ($VLAaffected_rowsTOTAL > 0) || ($VLIAaffected_rowsTOTAL > 0) )
				{
				$event_string = "|     lagged call vla agent DELETED:  $VLAaffected_rowsTOTAL|$VLIAaffected_rowsTOTAL";
				&event_logger;
				}

			@QHlive_agent_id=@MT;
			@QHlead_id=@MT;
			@QHuniqueid=@MT;
			@QHuser=@MT;
			@QHcall_type=@MT;
			@QHcampaign_id=@MT;
			@QHphone_number=@MT;
			@QHalt_dial=@MT;
			##### grab number of QUEUE calls right now and update
			$stmtA = "SELECT vla.live_agent_id,vla.lead_id,vla.uniqueid,vla.user,vac.call_type,vac.campaign_id,vac.phone_number,vac.alt_dial,vac.callerid FROM vicidial_live_agents vla,vicidial_auto_calls vac where vla.server_ip='$server_ip' and vla.status IN('QUEUE') and vla.extension LIKE \"R/%\" and vla.uniqueid=vac.uniqueid and vla.channel=vac.channel;";
			$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
			$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
			$vla_qh_ct=$sthA->rows;
			$w=0;
			while ($vla_qh_ct > $w)
				{
				@aryA = $sthA->fetchrow_array;
				$QHlive_agent_id[$w] =	$aryA[0];
				$QHlead_id[$w] =		$aryA[1];
				$QHuniqueid[$w] =		$aryA[2];
				$QHuser[$w] =			$aryA[3];
				$QHcall_type[$w] =		$aryA[4];
				$QHcampaign_id[$w] =	$aryA[5];
				$QHphone_number[$w] =	$aryA[6];
				$QHalt_dial[$w] =		$aryA[7];
				$QHcall_id[$w] =		$aryA[8];
				if (length($QHalt_dial[$w]) < 1) {$QHalt_dial[$w] = 'MAIN';}
				$w++;
				}
			$sthA->finish();

			$w=0;
			while ($vla_qh_ct > $w)
				{
				$start_call_url='';
				$licf_SQL = '';
				if ($QHcall_type[$w] =~ /IN/)
					{$licf_SQL = ",last_inbound_call_time='$SQLdate'";}

				$stmtA = "UPDATE vicidial_live_agents set status='INCALL',last_call_time='$SQLdate',comments='REMOTE',calls_today=(calls_today + 1),last_state_change='$SQLdate' $licf_SQL where live_agent_id='$QHlive_agent_id[$w]';";
				$Aaffected_rows = $dbhA->do($stmtA);

				$stmtB = "UPDATE vicidial_list set status='XFER',user='$QHuser[$w]' where lead_id='$QHlead_id[$w]';";
				$Baffected_rows = $dbhA->do($stmtB);

				if ($QHcall_type[$w] =~ /IN/)
					{
					$stmtC = "UPDATE vicidial_closer_log set status='XFER',user='$QHuser[$w]',comments='REMOTE' where lead_id='$QHlead_id[$w]' and uniqueid='$QHuniqueid[$w]' and campaign_id='$QHcampaign_id[$w]' limit 1;";
					$Caffected_rows = $dbhA->do($stmtC);

					$stmtD = "UPDATE vicidial_live_inbound_agents set calls_today=(calls_today + 1),last_call_time='$SQLdate' where user='$QHuser[$w]' and group_id='$QHcampaign_id[$w]';";
					$Daffected_rows = $dbhA->do($stmtD);

					$stmtE = "UPDATE vicidial_inbound_group_agents set calls_today=(calls_today + 1) where user='$QHuser[$w]' and group_id='$QHcampaign_id[$w]';";
					$Eaffected_rows = $dbhA->do($stmtE);

					$stmtG = "SELECT start_call_url FROM vicidial_inbound_groups where group_id='$QHcampaign_id[$w]';";
					}
				else
					{
					$stmtC = "UPDATE vicidial_log set status='XFER',user='$QHuser[$w]',comments='REMOTE' where uniqueid='$QHuniqueid[$w]';";
					$Caffected_rows = $dbhA->do($stmtC);

					$Daffected_rows=0;
					$Eaffected_rows=0;

					$stmtG = "SELECT start_call_url FROM vicidial_campaigns where campaign_id='$QHcampaign_id[$w]';";
					}

				$sthA = $dbhA->prepare($stmtG) or die "preparing: ",$dbhA->errstr;
				$sthA->execute or die "executing: $stmtG ", $dbhA->errstr;
				$start_url_ct=$sthA->rows;
				if ($start_url_ct > 0)
					{
					@aryA = $sthA->fetchrow_array;
					$start_call_url =	$aryA[0];
					}
				$sthA->finish();

				### This is where the call to the start_call_url launch goes

				if (length($start_call_url) > 5) 
					{
					$launch = $PATHhome . "/AST_send_URL.pl";
					$launch .= " --SYSLOG" if ($SYSLOG);
					$launch .= " --lead_id=" . $QHlead_id[$w];
					$launch .= " --phone_number=" . $QHphone_number[$w];
					$launch .= " --user=" . $QHuser[$w];
					$launch .= " --call_type=" . $QHcall_type[$w];
					$launch .= " --campaign=" . $QHcampaign_id[$w];
					$launch .= " --uniqueid=" . $QHuniqueid[$w];
					$launch .= " --alt_dial=" . $QHalt_dial[$w];
					$launch .= " --call_id=" . $QHcall_id[$w];
					$launch .= " --function=REMOTE_AGENT_START_CALL_URL";

					system($launch . ' &');

					$event_string="$launch|";
					&event_logger;
					}

				$event_string = "|     QUEUEd listing UPDATEd:  |$Aaffected_rows|$Baffected_rows|$Caffected_rows|$Daffected_rows|$Eaffected_rows|     |$QHlive_agent_id[$w]|$QHlead_id[$w]|$QHuniqueid[$w]|$QHuser[$w]|$QHcall_type[$w]|$QHcampaign_id[$w]|";
				 &event_logger;

				$w++;
				}

			#@psoutput = `/bin/ps -f --no-headers -A`;
			@psoutput = `/bin/ps -o "%p %a" --no-headers -A`;

			$running_listen = 0;

			$i=0;
			foreach (@psoutput)
				{
				chomp($psoutput[$i]);

				@psline = split(/\/usr\/bin\/perl /,$psoutput[$i]);

				if ($psline[1] =~ /AST_manager_li/) {$running_listen++;}

				$i++;
				}

			if (!$running_listen) 
				{
				$endless_loop=0;
				$one_day_interval=0;
				print "\nPROCESS KILLED NO LISTENER RUNNING... EXITING\n\n";
				}

			if($DB){print "checking to see if listener is dead |$running_listen|\n";}
			}

		$user_counter=0;
		$DELusers='';
		@DBuser_start=@MT;
		@DBuser_level=@MT;
		@DBremote_user=@MT;
		@DBremote_server_ip=@MT;
		@DBremote_campaign=@MT;
		@DBremote_conf_exten=@MT;
		@DBremote_closer=@MT;
		@DBremote_random=@MT;
		@DBon_hook_agent=@MT;
		@DBon_hook_ring_time=@MT;
		@loginexistsRANDOM=@MT;
		@loginexistsALL=@MT;
		@VD_user=@MT;
		@VD_extension=@MT;
		@VD_status=@MT;
		@VD_uniqueid=@MT;
		@VD_callerid=@MT;
		@VD_random=@MT;
		@autocallexists=@MT;
		@calllogfinished=@MT;

		###############################################################################
		###### first, grab all of the ACTIVE remote agents information from the database
		###############################################################################
		$stmtA = "SELECT remote_agent_id,user_start,number_of_lines,server_ip,conf_exten,status,campaign_id,closer_campaigns,on_hook_agent,on_hook_ring_time FROM vicidial_remote_agents where status IN('ACTIVE') and server_ip='$server_ip' order by user_start;";
		$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
		$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
		$sthArows=$sthA->rows;
		$TESTrun=0;
		$rec_count=0;
		while ($sthArows > $rec_count)
			{
			@aryA = $sthA->fetchrow_array;
			$user_start =				$aryA[1];
			$number_of_lines =			$aryA[2];
			$conf_exten =				$aryA[4];
			$campaign_id =				$aryA[6];
			$closer_campaigns =			$aryA[7];
			$on_hook_agent =			$aryA[8];
			$on_hook_ring_time =		$aryA[9];

			$y=0;
			while ($y < $number_of_lines)
				{
				$random = int( rand(9999999)) + 10000000;
				$user_id = ($user_start + $y);
				$DBuser_start[$user_counter] =			$user_start;
				$DBremote_user[$user_counter] =			$user_id;
				$DBremote_server_ip[$user_counter] =	$server_ip;
				$DBremote_campaign[$user_counter] =		$campaign_id;
				$DBremote_conf_exten[$user_counter] =	$conf_exten;
				if ($conf_exten =~ /999999999999/)
					{
					$TESTrun++;
					$DBremote_conf_exten[$user_counter] = ($user_counter + 8600051);
					}
				$DBremote_closer[$user_counter] =		$closer_campaigns;
				$DBremote_random[$user_counter] =		$random;
				$DBon_hook_agent[$user_counter] =		$on_hook_agent;
				$DBon_hook_ring_time[$user_counter] =	$on_hook_ring_time;
				
				$y++;
				$user_counter++;
				}
				
			$rec_count++;
			}
		$sthA->finish();
		if ($DB) {print STDERR "$user_counter live remote agents ACTIVE\n";}
   

		###############################################################################
		###### second, grab all of the INACTIVE remote agents information from the database
		###############################################################################
		$stmtA = "SELECT remote_agent_id,user_start,number_of_lines,server_ip,conf_exten,status,campaign_id,closer_campaigns FROM vicidial_remote_agents where status IN('INACTIVE') and server_ip='$server_ip' order by user_start;";
		$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
		$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
		$sthArows=$sthA->rows;
		$rec_count=0;
		while ($sthArows > $rec_count)
			{
			@aryA = $sthA->fetchrow_array;
			$Duser_start =			$aryA[1];
			$Dnumber_of_lines =		$aryA[2];
			$w=0;
			while ($w < $Dnumber_of_lines)
				{
				$Duser_id = ($Duser_start + $w);
				$DELusers .= "R/$Duser_id|";
				$w++;
				}
			$rec_count++;
			}
		$sthA->finish();
		# if ($DBX) {print STDERR "INACTIVE remote agents: |$DELusers|\n";}


		###############################################################################
		###### third, traverse array of remote agents to be active and insert or update 
		###### in vicidial_live_agents and vicidial_live_inbound_agents tables 
		###############################################################################
		$h=0;
		foreach(@DBremote_user) 
			{
			if (length($DBremote_user[$h])>1) 
				{
				$CAMPAIGN_autodial[$h] = 'Y';
				$CAMPAIGN_queuemetrics_phone_environment[$h] = '';
				### find the dial method of the campaign for each remote agent
				$stmtA = "SELECT dial_method,queuemetrics_phone_environment FROM vicidial_campaigns where campaign_id='$DBremote_campaign[$h]';";
					if ($DBX) {print STDERR "|$stmtA|\n";}
				$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
				$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
				$sthArows=$sthA->rows;
				if ($sthArows > 0)
					{
					@aryA = $sthA->fetchrow_array;
					$CAMPAIGN_dial_method[$h] =	$aryA[0];
					$CAMPAIGN_queuemetrics_phone_environment[$h] = $aryA[1];

					if ($CAMPAIGN_dial_method[$h] =~ /MANUAL|INBOUND_MAN/)
						{$CAMPAIGN_autodial[$h] = 'N';}
					}
				$sthA->finish();

				$DBon_hook_agent[$user_counter] =		$on_hook_agent;
				$DBon_hook_ring_time[$user_counter] =	$on_hook_ring_time;

				### check to see if the record exists and only needs random number update
				$stmtA = "SELECT count(*) FROM vicidial_live_agents where user='$DBremote_user[$h]' and server_ip='$server_ip' and campaign_id='$DBremote_campaign[$h]' and conf_exten='$DBremote_conf_exten[$h]' and closer_campaigns='$DBremote_closer[$h]' and outbound_autodial='$CAMPAIGN_autodial[$h]' and on_hook_agent='$DBon_hook_agent[$h]' and on_hook_ring_time='$DBon_hook_ring_time[$h]';";
					if ($DBX) {print STDERR "|$stmtA|\n";}
				$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
				$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
				$sthArows=$sthA->rows;
				if ($sthArows > 0)
					{
					@aryA = $sthA->fetchrow_array;
					$loginexistsRANDOM[$h] =	$aryA[0];
					}
				$sthA->finish();
				
				if ($loginexistsRANDOM[$h] > 0)
					{
					$stmtA = "UPDATE vicidial_live_agents set random_id='$DBremote_random[$h]' where user='$DBremote_user[$h]' and server_ip='$server_ip' and campaign_id='$DBremote_campaign[$h]' and conf_exten='$DBremote_conf_exten[$h]' and closer_campaigns='$DBremote_closer[$h]';";
					$affected_rows = $dbhA->do($stmtA);
					if ($DBX) {print STDERR "$DBremote_user[$h] $DBremote_campaign[$h] ONLY RANDOM ID UPDATE: $affected_rows\n";}
					}
				### check if record for user on server exists at all in vicidial_live_agents
				else
					{
					$stmtA = "SELECT count(*) FROM vicidial_live_agents where user='$DBremote_user[$h]' and server_ip='$server_ip'";
						if ($DBX) {print STDERR "|$stmtA|\n";}
					$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
					$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
					$sthArows=$sthA->rows;
					if ($sthArows > 0)
						{
						@aryA = $sthA->fetchrow_array;
						$loginexistsALL[$h] =	$aryA[0];
						}
					$sthA->finish();

					if ($loginexistsALL[$h] > 0)
						{
						$stmtA = "UPDATE vicidial_live_agents set random_id='$DBremote_random[$h]',campaign_id='$DBremote_campaign[$h]',conf_exten='$DBremote_conf_exten[$h]',closer_campaigns='$DBremote_closer[$h]',status='READY',last_state_change='$SQLdate',outbound_autodial='$CAMPAIGN_autodial[$h]',on_hook_agent='$DBon_hook_agent[$h]',on_hook_ring_time='$DBon_hook_ring_time[$h]' where user='$DBremote_user[$h]' and server_ip='$server_ip';";
						$affected_rows = $dbhA->do($stmtA);
						if ($DBX) {print STDERR "$DBremote_user[$h] ALL UPDATE: $affected_rows\n";}
			#			if ($affected_rows>0) 
			#				{
			#				if ($enable_queuemetrics_logging > 0)
			#					{
			#					$dbhB = DBI->connect("DBI:mysql:$queuemetrics_dbname:$queuemetrics_server_ip:3306", "$queuemetrics_login", "$queuemetrics_pass")
			#					 or die "Couldn't connect to database: " . DBI->errstr;

			#					if ($DBX) {print "CONNECTED TO DATABASE:  $queuemetrics_server_ip|$queuemetrics_dbname\n";}

			#					$stmtB = "INSERT INTO queue_log SET partition='P01',time_id='$secX',call_id='NONE',queue='$DBremote_campaign[$h]',agent='Agent/$DBremote_user[$h]',verb='UNPAUSE',serverid='1';";
			#					$Baffected_rows = $dbhB->do($stmtB);

			#					$dbhB->disconnect();
			#					}

			#				}

						}
					### no records exist so insert a new one
					else
						{
						# grab the user_level of the agent
						$DBuser_level[$h]='1';
						$stmtA = "SELECT user_level FROM vicidial_users where user='$DBuser_start[$h]';";
						$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
						$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
						$sthArows=$sthA->rows;
						$rec_count=0;
						while ($sthArows > $rec_count)
							{
							@aryA = $sthA->fetchrow_array;
							$DBuser_level[$h] =	$aryA[0];
							$rec_count++;
							}
						$sthA->finish();

						$stmtA = "INSERT INTO vicidial_live_agents (user,server_ip,conf_exten,extension,status,campaign_id,random_id,last_call_time,last_update_time,last_call_finish,closer_campaigns,channel,uniqueid,callerid,user_level,comments,last_state_change,outbound_autodial,ra_user,on_hook_agent,on_hook_ring_time,last_inbound_call_time,last_inbound_call_finish) values('$DBremote_user[$h]','$server_ip','$DBremote_conf_exten[$h]','R/$DBremote_user[$h]','READY','$DBremote_campaign[$h]','$DBremote_random[$h]','$SQLdate','$FDtsSQLdate','$SQLdate','$DBremote_closer[$h]','','','','$DBuser_level[$h]','REMOTE','$SQLdate','$CAMPAIGN_autodial[$h]','$DBuser_start[$h]','$DBon_hook_agent[$h]','$DBon_hook_ring_time[$h]','$SQLdate','$SQLdate');";
						$affected_rows = $dbhA->do($stmtA);
						if ($DBX) {print STDERR "$DBremote_user[$h] NEW INSERT\n";}
						if ($TESTrun > 0)
							{
							$stmtA = "SELECT count(*) FROM live_sip_channels where extension LIKE \"%999999999999\" and server_ip='$server_ip';";
							$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
							$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
							$sthArows=$sthA->rows;
							if ($sthArows > 0)
								{
								@aryA = $sthA->fetchrow_array;
								$LSC_count =	$aryA[0];
								}
							$sthA->finish();

							if ($number_of_lines > $LSC_count)
								{
								$SIqueryCID = "T$CIDdate$DBremote_conf_exten[$h]";
								$stmtA="INSERT INTO vicidial_manager values('','','$SQLdate','NEW','N','$server_ip','','Originate','$SIqueryCID','Channel: $local_DEF$DBremote_conf_exten[$h]$local_AMP$ext_context','Context: $ext_context','Exten: 999999999999','Priority: 1','Callerid: $SIqueryCID','','','','','');";
								$affected_rows = $dbhA->do($stmtA);
								if ($DBX) {print STDERR "   TESTrun CALL PLACED: 999999999999 $DBremote_conf_exten[$h] $DBremote_user[$h] NEW INSERT: |$affected_rows|\n";}
								}
							else {print STDERR "Agent test calls already adequate $number_of_lines !> $LSC_count\n";}
							}
						if ($affected_rows>0) 
							{
							if ($enable_queuemetrics_logging > 0)
								{
								$QM_LOGIN = 'AGENTLOGIN';
								if ($queuemetrics_loginout =~ /CALLBACK/)
									{$QM_LOGIN = 'AGENTCALLBACKLOGIN';}

								$dbhB = DBI->connect("DBI:mysql:$queuemetrics_dbname:$queuemetrics_server_ip:3306", "$queuemetrics_login", "$queuemetrics_pass")
								 or die "Couldn't connect to database: " . DBI->errstr;

								if ($DBX) {print "CONNECTED TO DATABASE:  $queuemetrics_server_ip|$queuemetrics_dbname\n";}

								$pe_append='';
								if ( ($queuemetrics_pe_phone_append > 0) && (length($CAMPAIGN_queuemetrics_phone_environment[$h])>0) )
									{$pe_append = "-$DBremote_conf_exten[$h]";}

								if ($queuemetrics_loginout !~ /NONE/)
									{
									$stmtB = "INSERT INTO queue_log SET partition='P01',time_id='$secX',call_id='NONE',queue='$DBremote_campaign[$h]',agent='Agent/$DBremote_user[$h]',verb='$QM_LOGIN',data1='$DBremote_user[$h]$agents',serverid='$queuemetrics_log_id',data4='$CAMPAIGN_queuemetrics_phone_environment[$h]$pe_append';";
									$Baffected_rows = $dbhB->do($stmtB);
									}

								$stmtB = "INSERT INTO queue_log SET partition='P01',time_id='$secX',call_id='NONE',queue='$DBremote_campaign[$h]',agent='Agent/$DBremote_user[$h]',verb='UNPAUSE',serverid='$queuemetrics_log_id',data4='$CAMPAIGN_queuemetrics_phone_environment[$h]$pe_append';";
								$Baffected_rows = $dbhB->do($stmtB);

								if ($queuemetrics_addmember_enabled > 0)
									{
									$stmtB = "INSERT INTO queue_log SET partition='P01',time_id='$secX',call_id='NONE',queue='$DBremote_campaign[$h]',agent='Agent/$DBremote_user[$h]',verb='ADDMEMBER2',data1='$DBremote_user[$h]$agents',serverid='$queuemetrics_log_id',data4='$CAMPAIGN_queuemetrics_phone_environment[$h]$pe_append';";
									$Baffected_rows = $dbhB->do($stmtB);
									}

								$dbhB->disconnect();
								}
							}
						}
					}
				##### If there are selected inbound/closer groups, gather in-group rankings and insert/update if needed
				@TEMPingroups=@MT;
				$TEMPagentINGROUPS='';
				if (length($DBremote_closer[$h]) > 1)
					{
					if ( ($enable_queuemetrics_logging > 0) && ($queuemetrics_addmember_enabled > 0) )
						{
						$dbhB = DBI->connect("DBI:mysql:$queuemetrics_dbname:$queuemetrics_server_ip:3306", "$queuemetrics_login", "$queuemetrics_pass")
						 or die "Couldn't connect to database: " . DBI->errstr;
						}
					$TEMPagentINGROUPS = $DBremote_closer[$h];
					$TEMPagentINGROUPS =~ s/-$//gi;
					@TEMPingroups = split(/ /,$TEMPagentINGROUPS);
					$s=0;
					foreach(@TEMPingroups)
						{
						if (length($TEMPingroups[$s]) > 1)
							{
							$TEMPagentWEIGHT=0;
							$TEMPagentCALLS=0;
							$TEMPexistsVLIA=0;
							# grab the group weight and calls today of the agent in each in-group
							$DBuser_level[$h]='1';
							$stmtA = "SELECT group_weight,calls_today FROM vicidial_inbound_group_agents where user='$DBuser_start[$h]' and group_id='$TEMPingroups[$s]';";
							$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
							$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
							$sthArowsVIGA=$sthA->rows;
							if ($sthArowsVIGA > 0)
								{
								@aryA = $sthA->fetchrow_array;
								$TEMPagentWEIGHT =	$aryA[0];
								$TEMPagentCALLS =	$aryA[1];
								}
							$sthA->finish();

							$stmtA = "SELECT count(*) FROM vicidial_live_inbound_agents where user='$DBremote_user[$h]' and group_id='$TEMPingroups[$s]' and group_weight='$TEMPagentWEIGHT';";
							$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
							$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
							$sthArowsVLIA=$sthA->rows;
							if ($sthArowsVLIA > 0)
								{
								@aryA = $sthA->fetchrow_array;
								$TEMPexistsVLIA =	$aryA[0];
								}
							$sthA->finish();

							$pe_append='';
							if ( ($queuemetrics_pe_phone_append > 0) && (length($CAMPAIGN_queuemetrics_phone_environment[$h])>0) )
								{$pe_append = "-$DBremote_conf_exten[$h]";}

							if ($TEMPexistsVLIA < 1)
								{
								$stmtA = "INSERT IGNORE INTO vicidial_live_inbound_agents SET user='$DBremote_user[$h]', group_id='$TEMPingroups[$s]', group_weight='$TEMPagentWEIGHT', calls_today='$TEMPagentCALLS', last_call_time='$SQLdate', last_call_finish='$SQLdate' ON DUPLICATE KEY UPDATE group_weight='$TEMPagentWEIGHT';";
								$affected_rows = $dbhA->do($stmtA);
								if ( ($DBX) && ($affected_rows > 0) ) {print STDERR "$DBremote_user[$h] VLIA UPDATE: $affected_rows|$TEMPingroups[$s]|$TEMPagentWEIGHT\n";}

								if ( ($enable_queuemetrics_logging > 0) && ($queuemetrics_addmember_enabled > 0) )
									{
									$stmtB = "INSERT INTO queue_log SET partition='P01',time_id='$secX',call_id='NONE',queue='$TEMPingroups[$s]',agent='Agent/$DBremote_user[$h]',verb='ADDMEMBER2',data1='$DBremote_user[$h]$agents',serverid='$queuemetrics_log_id',data4='$CAMPAIGN_queuemetrics_phone_environment[$h]$pe_append';";
									$Baffected_rows = $dbhB->do($stmtB);
									}
								}

							}
						$s++;
						}
					if ( ($enable_queuemetrics_logging > 0) && ($queuemetrics_addmember_enabled > 0) )
						{$dbhB->disconnect();}
					}
				}
			$h++;
			}


		###############################################################################
		###### fourth, validate that the calls that the vicidial_live_agents are on and not dead
		###### and if they are wipe out the values and set the agent record back to READY
		###############################################################################
		$stmtA = "SELECT user,extension,status,uniqueid,callerid,lead_id,campaign_id,call_server_ip FROM vicidial_live_agents where extension LIKE \"R/%\" and server_ip='$server_ip' and uniqueid > 10;";
		$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
		$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
		$sthArows=$sthA->rows;
		$rec_count=0;
		$z=0;
		while ($sthArows > $rec_count)
			{
			@aryA = $sthA->fetchrow_array;
			$VDuser =				$aryA[0];
			$VDextension =			$aryA[1];
			$VDstatus =				$aryA[2];
			$VDuniqueid =			$aryA[3];
			$VDcallerid =			$aryA[4];
			$VDlead_id =			$aryA[5];
			$VDcampaign_id =		$aryA[6];
			$VDcall_server_ip =		$aryA[7];
			$VDrandom = int( rand(9999999)) + 10000000;

			$VD_user[$z] =			$VDuser;
			$VD_extension[$z] =		$VDextension;
			$VD_status[$z] =		$VDstatus;
			$VD_uniqueid[$z] =		$VDuniqueid;
			$VD_callerid[$z] =		$VDcallerid;
			$VD_lead_id[$z] =		$VDlead_id;
			$VD_campaign_id[$z] =	$VDcampaign_id;
			$VD_call_server_ip[$z] =	$VDcall_server_ip;
			$VD_random[$z] =		$VDrandom;
			$USER_queuemetrics_phone_environment[$z]='';

			$z++;				
			$rec_count++;
			}
		$sthA->finish();
		if ($DB) {print STDERR "$z remote agents on calls\n";}

		$z=0;
		foreach(@VD_user) 
			{
			$stmtA = "SELECT count(*) FROM vicidial_auto_calls where uniqueid='$VD_uniqueid[$z]' and server_ip IN('$server_ip','$VD_call_server_ip[$z]');";
			$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
			$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
			$sthArows=$sthA->rows;
			if ($sthArows > 0)
				{
				@aryA = $sthA->fetchrow_array;
				$autocallexists[$z] =	$aryA[0];
				}
			$sthA->finish();
			
			$stmtA = "SELECT queuemetrics_phone_environment FROM vicidial_campaigns where campaign_id='$VD_campaign_id[$z]';";
			$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
			$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
			$sthArows=$sthA->rows;
			if ($sthArows > 0)
				{
				@aryA = $sthA->fetchrow_array;
				$USER_queuemetrics_phone_environment[$z] =	$aryA[0];
				}
			$sthA->finish();
			
			if ($autocallexists[$z] < 1)
				{
				$pe_append='';
				if ( ($queuemetrics_pe_phone_append > 0) && (length($USER_queuemetrics_phone_environment[$z])>0) )
					{$pe_append = "-$VD_extension[$z]";}

				if ($DELusers =~ /R\/$VD_user[$z]\|/)
					{
					$stmtA = "UPDATE vicidial_live_agents set random_id='$VD_random[$z]',status='PAUSED', last_call_finish='$SQLdate',lead_id='',uniqueid='',callerid='',channel='',last_state_change='$SQLdate' where user='$VD_user[$z]' and server_ip='$server_ip';";
					$affected_rows = $dbhA->do($stmtA);
					if ($DB) {print STDERR "$VD_user[$z] CALL WIPE DELETE UPDATE: $affected_rows|PAUSED|$VD_uniqueid[$z]|$VD_user[$z]|\n";}
					if ($affected_rows>0) 
						{
						if ($enable_queuemetrics_logging > 0)
							{
							$logintime=0;
							$QM_LOGOFF = 'AGENTLOGOFF';
							if ($queuemetrics_loginout =~ /CALLBACK/)
								{$QM_LOGOFF = 'AGENTCALLBACKLOGOFF';}

							$dbhB = DBI->connect("DBI:mysql:$queuemetrics_dbname:$queuemetrics_server_ip:3306", "$queuemetrics_login", "$queuemetrics_pass")
							 or die "Couldn't connect to database: " . DBI->errstr;

							if ($DBX) {print "CONNECTED TO DATABASE:  $queuemetrics_server_ip|$queuemetrics_dbname\n";}

							$stmtB = "INSERT INTO queue_log SET partition='P01',time_id='$secX',call_id='NONE',queue='NONE',agent='Agent/$VD_user[$z]',verb='PAUSEALL',serverid='$queuemetrics_log_id',data4='$USER_queuemetrics_phone_environment[$z]$pe_append';";
							$Baffected_rows = $dbhB->do($stmtB);

							$stmtB = "SELECT time_id,data1 FROM queue_log where agent='Agent/$VD_user[$z]' and verb IN('AGENTLOGIN','AGENTCALLBACKLOGIN') and time_id > $check_time order by time_id desc limit 1;";
							$sthB = $dbhB->prepare($stmtB) or die "preparing: ",$dbhB->errstr;
							$sthB->execute or die "executing: $stmtB ", $dbhB->errstr;
							$sthBrows=$sthB->rows;
							$rec_count=0;
							while ($sthBrows > $rec_count)
								{
								@aryB = $sthB->fetchrow_array;
								$logintime =		$aryB[0];
								$phone_logged_in =	$aryB[1];
								$rec_count++;
								}
							$sthB->finish();
							if ($DBX) {print "LOGOFF TIME LOG:  $logintime|$secX|$stmtB|\n";}

							$time_logged_in = ($secX - $logintime);
							if ($time_logged_in > 1000000) {$time_logged_in=1;}

							if ($queuemetrics_addmember_enabled > 0)
								{
								if ( (length($logintime) < 1) || ($queuemetrics_loginout =~ /NONE/) )
									{
									$stmtB = "SELECT time_id,data3 FROM queue_log where agent='Agent/$VD_user[$z]' and verb='PAUSEREASON' and data1='LOGIN' order by time_id desc limit 1;";
									$sthB = $dbhB->prepare($stmtB) or die "preparing: ",$dbhB->errstr;
									$sthB->execute or die "executing: $stmtA ", $dbhB->errstr;
									$sthBrows=$sthB->rows;
									if ($sthBrows > 0)
										{
										@aryB = $sthB->fetchrow_array;
										$logintime =		$aryB[0];
										$phone_logged_in =	$aryB[1];
										}
									$sthB->finish();
									}
								if ($queuemetrics_loginout =~ /NONE/)
									{
									$stmtB = "INSERT INTO queue_log SET partition='P01',time_id='$secX',call_id='NONE',queue='NONE',agent='Agent/$VD_user[$z]',verb='PAUSEREASON',serverid='$queuemetrics_log_id',data1='LOGOFF';";
									$Baffected_rows = $dbhB->do($stmtB);
									}
								$stmtB = "SELECT distinct queue FROM queue_log where time_id >= $logintime and agent='Agent/$VD_user[$z]' and verb IN('ADDMEMBER','ADDMEMBER2') and queue != '$VD_campaign_id[$z]' order by time_id desc;";
								$sthB = $dbhB->prepare($stmtB) or die "preparing: ",$dbhB->errstr;
								$sthB->execute or die "executing: $stmtB ", $dbhB->errstr;
								$sthBrows=$sthB->rows;
								$rec_count=0;
								while ($sthBrows > $rec_count)
									{
									@aryB = $sthB->fetchrow_array;
									$AM_queue[$rec_count] =		$aryB[0];
									$rec_count++;
									}
								$sthB->finish();

								$AM_queue[$rec_count] =	$VD_campaign_id[$z];
								$rec_count++;
								$sthBrows++;

								$rec_count=0;
								while ($sthBrows > $rec_count)
									{
									$stmtB = "INSERT INTO queue_log SET partition='P01',time_id='$secX',call_id='NONE',queue='$AM_queue[$rec_count]',agent='Agent/$VD_user[$z]',verb='REMOVEMEMBER',data1='$phone_logged_in',serverid='$queuemetrics_log_id',data4='$USER_queuemetrics_phone_environment[$z]$pe_append';";
									$Baffected_rows = $dbhB->do($stmtB);
									$rec_count++;
									}
								}

							if ($queuemetrics_loginout !~ /NONE/)
								{
								$stmtB = "INSERT INTO queue_log SET partition='P01',time_id='$secX',call_id='NONE',queue='$VD_campaign_id[$z]',agent='Agent/$VD_user[$z]',verb='$QM_LOGOFF',data1='$phone_logged_in',data2='$time_logged_in',serverid='$queuemetrics_log_id',data4='$USER_queuemetrics_phone_environment[$z]$pe_append';";
								$Baffected_rows = $dbhB->do($stmtB);
								}

							$dbhB->disconnect();
							}

						}
					}
				else
					{
					$stmtA = "UPDATE vicidial_live_agents set random_id='$VD_random[$z]', last_call_finish='$SQLdate',lead_id='',uniqueid='',callerid='',channel='',last_state_change='$SQLdate' where user='$VD_user[$z]' and server_ip='$server_ip';";
					$affected_rows = $dbhA->do($stmtA);
					if ($DB) {print STDERR "$VD_user[$z] CALL WIPE UPDATE: $affected_rows|READY|$VD_uniqueid[$z]|$VD_user[$z]|\n";}

					$stmtA = "UPDATE vicidial_live_agents set status='READY' where user='$VD_user[$z]' and server_ip='$server_ip';";
					$affected_rows = $dbhA->do($stmtA);
					if ($DB) {print STDERR "$VD_user[$z] CALL WIPE UPDATE: $affected_rows|READY|$VD_uniqueid[$z]|$VD_user[$z]|\n";}
					if ($affected_rows>0) 
						{
						if ($enable_queuemetrics_logging > 0)
							{
							$dbhB = DBI->connect("DBI:mysql:$queuemetrics_dbname:$queuemetrics_server_ip:3306", "$queuemetrics_login", "$queuemetrics_pass")
							 or die "Couldn't connect to database: " . DBI->errstr;

							if ($DBX) {print "CONNECTED TO DATABASE:  $queuemetrics_server_ip|$queuemetrics_dbname\n";}

							$stmtB = "INSERT INTO queue_log SET partition='P01',time_id='$secX',call_id='NONE',queue='NONE',agent='Agent/$VD_user[$z]',verb='PAUSEALL',serverid='$queuemetrics_log_id',data4='$USER_queuemetrics_phone_environment[$z]$pe_append';";
							$Baffected_rows = $dbhB->do($stmtB);

							$stmtB = "INSERT INTO queue_log SET partition='P01',time_id='$secX',call_id='NONE',queue='NONE',agent='Agent/$VD_user[$z]',verb='UNPAUSEALL',serverid='$queuemetrics_log_id',data4='$USER_queuemetrics_phone_environment[$z]$pe_append';";
							$Baffected_rows = $dbhB->do($stmtB);

							$dbhB->disconnect();
							}

						}

					}
				}
	### possible future active call checker
	#		else
	#			{
	#			$stmtA = "SELECT count(*) FROM call_log where caller_code='$VD_callerid[$z]' and server_ip='$server_ip' and end_epoch > 10;";
	#			$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
	#			$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
	#			$sthArows=$sthA->rows;
	#			$rec_count=0;
	#			while ($sthArows > $rec_count)
	#				{
	#				@aryA = $sthA->fetchrow_array;
	#				$calllogfinished[$z] =	"$aryA[0]";
	#				$rec_count++;
	#				}
	#			$sthA->finish();
	#			if ($calllogfinished[$z] > 1)
	#				{
	#				$stmtA = "UPDATE vicidial_live_agents set random_id='$VD_random[$z]',status='READY', last_call_finish='$SQLdate',lead_id='',uniqueid='',callerid='',channel=''  where user='$VD_user[$z]' and server_ip='$server_ip';";
	#				$affected_rows = $dbhA->do($stmtA);
	#				if ($DB) {print STDERR "$VD_user[$z] AGENT READY UPDATE: $affected_rows|READY|$VD_uniqueid[$z]|$VD_callerid[$z]|$VD_user[$z]|\n";}

	#				$stmtA = "DELETE from vicidial_auto_calls where callerid='$VD_callerid[$z]' and server_ip='$server_ip';";
	#				$affected_rows = $dbhA->do($stmtA);
	#				if ($DB) {print STDERR "$VD_user[$z] VAC DELETE: $affected_rows|$VD_callerid[$z]|\n";}
	#				}
	#			}

			$z++;
			}



		###############################################################################
		###### last, wait for a little bit and repeat the loop
		###############################################################################

		### sleep for X seconds before beginning the loop again
		usleep(1*$loop_delay*1000);

		$endless_loop--;
		if($DB){print STDERR "\nloop counter: |$endless_loop|\n";}

		### putting a blank file called "VDAD.kill" in the directory will automatically safely kill this program
		if (-e "$PATHhome/VDAD.kill")
			{
			unlink("$PATHhome/VDAD.kill");
			$endless_loop=0;
			$one_day_interval=0;
			print "\nPROCESS KILLED MANUALLY... EXITING\n\n"
			}

		$bad_grabber_counter=0;
		}


	if($DB){print "DONE... Exiting... Goodbye... See you later... Not really, initiating next loop...\n";}

	$event_string='HANGING UP|';
	&event_logger;

	$one_day_interval--;
	}

$event_string='CLOSING DB CONNECTION|';
&event_logger;


$dbhA->disconnect();


if($DB){print "DONE... Exiting... Goodbye... See you later... Really I mean it this time\n";}


exit;









##### SUBROUTINES #####
sub get_time_now	#get the current date and time and epoch for logging call lengths and datetimes
	{
	$secX = time();
	($sec,$min,$hour,$mday,$mon,$year,$wday,$yday,$isdst) = localtime($secX);
	$LOCAL_GMT_OFF = $SERVER_GMT;
	$LOCAL_GMT_OFF_STD = $SERVER_GMT;
	if ($isdst) {$LOCAL_GMT_OFF++;} 
	$check_time = ($secX - 86400);

	$GMT_now = ($secX - ($LOCAL_GMT_OFF * 3600));
	($sec,$min,$hour,$mday,$mon,$year,$wday,$yday,$isdst) = localtime($GMT_now);
	if ($hour < 10) {$hour = "0$hour";}
	if ($min < 10) {$min = "0$min";}

	if ($DB) {print "TIME DEBUG: $LOCAL_GMT_OFF_STD|$LOCAL_GMT_OFF|$isdst|   GMT: $hour:$min\n";}

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
	$CIDdate = "$mon$mday$hour$min$sec";
	$tsSQLdate = "$year$mon$mday$hour$min$sec";
	$SQLdate = "$year-$mon-$mday $hour:$min:$sec";
	$filedate = "$year-$mon-$mday";

	$FDtarget = ($secX + 10);
	($Fsec,$Fmin,$Fhour,$Fmday,$Fmon,$Fyear,$Fwday,$Fyday,$Fisdst) = localtime($FDtarget);
	$Fyear = ($Fyear + 1900);
	$Fmon++;
	if ($Fmon < 10) {$Fmon = "0$Fmon";}
	if ($Fmday < 10) {$Fmday = "0$Fmday";}
	if ($Fhour < 10) {$Fhour = "0$Fhour";}
	if ($Fmin < 10) {$Fmin = "0$Fmin";}
	if ($Fsec < 10) {$Fsec = "0$Fsec";}
	$FDtsSQLdate = "$Fyear$Fmon$Fmday$Fhour$Fmin$Fsec";

	$BDtarget = ($secX - 10);
	($Bsec,$Bmin,$Bhour,$Bmday,$Bmon,$Byear,$Bwday,$Byday,$Bisdst) = localtime($BDtarget);
	$Byear = ($Byear + 1900);
	$Bmon++;
	if ($Bmon < 10) {$Bmon = "0$Bmon";}
	if ($Bmday < 10) {$Bmday = "0$Bmday";}
	if ($Bhour < 10) {$Bhour = "0$Bhour";}
	if ($Bmin < 10) {$Bmin = "0$Bmin";}
	if ($Bsec < 10) {$Bsec = "0$Bsec";}
	$BDtsSQLdate = "$Byear$Bmon$Bmday$Bhour$Bmin$Bsec";

	$PDtarget = ($secX - 30);
	($Psec,$Pmin,$Phour,$Pmday,$Pmon,$Pyear,$Pwday,$Pyday,$Pisdst) = localtime($PDtarget);
	$Pyear = ($Pyear + 1900);
	$Pmon++;
	if ($Pmon < 10) {$Pmon = "0$Pmon";}
	if ($Pmday < 10) {$Pmday = "0$Pmday";}
	if ($Phour < 10) {$Phour = "0$Phour";}
	if ($Pmin < 10) {$Pmin = "0$Pmin";}
	if ($Psec < 10) {$Psec = "0$Psec";}
	$PDtsSQLdate = "$Pyear$Pmon$Pmday$Phour$Pmin$Psec";

	$XDtarget = ($secX - 120);
	($Xsec,$Xmin,$Xhour,$Xmday,$Xmon,$Xyear,$Xwday,$Xyday,$Xisdst) = localtime($XDtarget);
	$Xyear = ($Xyear + 1900);
	$Xmon++;
	if ($Xmon < 10) {$Xmon = "0$Xmon";}
	if ($Xmday < 10) {$Xmday = "0$Xmday";}
	if ($Xhour < 10) {$Xhour = "0$Xhour";}
	if ($Xmin < 10) {$Xmin = "0$Xmin";}
	if ($Xsec < 10) {$Xsec = "0$Xsec";}
	$XDSQLdate = "$Xyear-$Xmon-$Xmday $Xhour:$Xmin:$Xsec";

	$TDtarget = ($secX - 600);
	($Tsec,$Tmin,$Thour,$Tmday,$Tmon,$Tyear,$Twday,$Tyday,$Tisdst) = localtime($TDtarget);
	$Tyear = ($Tyear + 1900);
	$Tmon++;
	if ($Tmon < 10) {$Tmon = "0$Tmon";}
	if ($Tmday < 10) {$Tmday = "0$Tmday";}
	if ($Thour < 10) {$Thour = "0$Thour";}
	if ($Tmin < 10) {$Tmin = "0$Tmin";}
	if ($Tsec < 10) {$Tsec = "0$Tsec";}
	$TDSQLdate = "$Tyear-$Tmon-$Tmday $Thour:$Tmin:$Tsec";
	}


sub event_logger
	{
	if ($DB) {print "$now_date|$event_string|\n";}
	if ($SYSLOG)
		{
		### open the log file for writing ###
		open(Lout, ">>$VDRLOGfile")
				|| die "Can't open $VDRLOGfile: $!\n";
		print Lout "$now_date|$event_string|\n";
		close(Lout);
		}
	$event_string='';
	}
