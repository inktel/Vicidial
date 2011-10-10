#!/usr/bin/perl
#
# AST_VDauto_dial.pl version 2.4
#
# DESCRIPTION:
# Places auto_dial calls on the VICIDIAL dialer system 
#
# SUMMARY:
# This program was designed for people using the Asterisk PBX with VICIDIAL
#
# For the client to use VICIDIAL, this program must be in the cron constantly 
# 
# For this program to work you need to have the "asterisk" MySQL database 
# created and create the tables listed in the CONF_MySQL.txt file, also make sure
# that the machine running this program has read/write/update/delete access 
# to that database
# 
# It is recommended that you run this program on the local Asterisk machine
#
# This script is to run perpetually querying every second to place new phone
# calls from the vicidial_hopper based upon how many available agents there are
# and the value of the auto_dial_level setting in the campaign screen of the 
# admin web page
#
# It is good practice to keep this program running by placing the associated 
# KEEPALIVE script running every minute to ensure this program is always running
#
# Copyright (C) 2011  Matt Florell <vicidial@gmail.com>    LICENSE: AGPLv2
#
# CHANGELOG:
# 50125-1201 - Changed dial timeout to 120 seconds from 180 seconds
# 50317-0954 - Added duplicate check per cycle to account for DB lockups
# 50322-1302 - Added campaign custom callerid feature
# 50324-1353 - Added optional variable to record ring time thru separate diap prefix 
# 50606-2308 - Added code to ensure no calls placed out on inactive campaign
# 50617-1248 - Added code to place LOGOUT entry in auto-paused user logs
# 50620-1349 - Added custom vdad transfer AGI extension per campaign
# 50810-1610 - Added database server variable definitions lookup
# 50810-1630 - Added max_vicidial_trunks server limiter on active lines
# 50812-0957 - Corrected max_trunks logic, added update of server every 25 sec
# 60120-1522 - Corrected time error for hour variable, caused agi problems
# 60427-1223 - Fixed Blended in/out CLOSER campaign issue
# 60608-1134 - Altered vac table field of call_type for IN OUT distinction
# 60612-1324 - Altered dead call section to accept BUSY detection from VD_hangup
#            - Altered dead call section to accept DISCONNECT detection from VD_hangup
# 60614-1142 - Added code to work with recycled leads, multi called_since_last_reset values
#            - Removed gmt lead validation because it is already done by VDhopper
# 60807-1438 - Changed to DBI
#            - Changed to use /etc/astguiclient.conf for configs
# 60814-1749 - Added option for no logging to file
# 60821-1546 - Added option to not dial phone_code per campaign
# 60824-1437 - Added available_only_ratio_tally option
# 61003-1353 - Added restrictions for server trunks
# 61113-1625 - Added code for clearing VDAC LIVE jams
# 61115-1725 - Added OUTBALANCE to call calculation for call_type for balance dialing
# 70111-1600 - Added ability to use BLEND/INBND/*_C/*_B/*_I as closer campaigns
# 70115-1635 - Added initial auto-alt-dial functionality
# 70116-1619 - Added VDAD Ring-No-Answer Auto Alt Dial code
# 70118-1539 - Added user_group logging to vicidial_user_log
# 70131-1550 - Fixed Manual dialing trunk shortage bug
# 70205-1414 - Added code for last called date update
# 70207-1031 - Fixed Tally-only-available bug with customer hangups
# 70215-1123 - Added queue_log ABANDON logging
# 70302-1412 - Fixed max_vicidial_trunks update if set to 0
# 70320-1458 - Fixed several errors in calculating trunk shortage for campaigns
# 71029-1909 - Changed CLOSER-type campaign_id restriction
# 71030-2054 - Added hopper priority sorting
# 80227-0406 - added queue_priority
# 80525-1040 - Added IVR vac status compatibility for inbound calls
# 80713-0624 - Added vicidial_list_last_local_call_time field
# 80829-2359 - Added extended alt dial and dnc checkon all alt dial hopper insertions
# 80909-0845 - Added support for campaign-specific DNC lists
# 81013-2216 - Fixed improper deletion of auto_calls records
# 81020-0125 - Bug fixes from changes to auto_calls deletion changes
# 90124-0721 - Added parameter to ensure no auto-dial calls are placed for MANUAL campaigns
# 90202-0203 - Added outbound_autodial_active option to halt all dialing
# 90306-1845 - Added configurable calls-per-second option
# 90611-0554 - Bug fix for Manual dial calls and logging
# 90619-1948 - Format fixing
# 90630-2252 - Added Sangoma CDP pre-Answer call processing
# 90816-0057 - Changed default vicidial_log time to 0 from 1 second
# 90827-1227 - Added list_id logging in vicidial_log on NA calls
# 90907-0919 - Added LAGGED pause code update for paused agents, reduced logging if no issues
# 90909-0640 - Parked bug fix and code optimizations
# 90917-1432 - Fixed issue on high-volume systems with lagged agents
# 90924-0914 - Added List callerid override option
# 91026-1218 - Added AREACODE DNC option
# 91108-2122 - Added LAGGED PAUSEREASON QM entry for lagged agents
# 91123-1802 - Added outbound_autodial field, and exception for outbound-only agents on blended campaign
# 91213-1856 - Added queue_position to queue_log ABANDON records
# 100309-0551 - Added queuemetrics_loginout option
# 100327-1359 - Fixed LAGGED issues
# 100903-0041 - Changed lead_id max length to 10 digits
# 101111-1556 - Added source to vicidial_hopper inserts
# 101117-1656 - Added accounting for DEAD agent calls when in available-only-tally dialing
# 101207-0713 - Added more info to Originate for rare VDAC issue
# 110103-1227 - Added queuemetrics_loginout NONE option
# 110124-1134 - Small query fix for large queue_log tables
# 110224-1408 - Fixed trunk reservation bug
# 110224-1859 - Added compatibility with QM phone environment logging
# 110303-1710 - Added clearing of ring_callerid when vicidial_auto_calls deleted
# 110513-1745 - Added double-check for dial level difference target, and dial level and avail-only-tally features
# 110525-1940 - Allow for auto-dial IVR transfers
# 110602-0953 - Added dl_diff_target_method option
# 110723-1256 - Added extra debug for vac deletes and fix for long ring/drop time
# 110731-2127 - Added sections for handling MULTI_LEAD auto-alt-dial and na-call-url functions
# 110809-1516 - Added section for noanswer logging
# 110901-1125 - Added campaign areacode cid function
# 110922-1202 - Added logging of last calltime to campaign
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
		print "  [-debug] = verbose debug messages\n";
		print "  [--delay=XXX] = delay of XXX seconds per loop, default 2.5 seconds\n";
		print "\n";
		exit;
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
			$loop_delay = '2500';
			}
		if ($args =~ /-debug/i)
			{
			$DB=1; # Debug flag, set to 0 for no debug messages, On an active system this will generate hundreds of lines of output per minute
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
	$loop_delay = '2500';
	$DB=1;
	}
### end parsing run-time options ###


# constants
$US='__';
$MT[0]='';
$RECcount=''; ### leave blank for no REC count
$RECprefix='7'; ### leave blank for no REC prefix
$useJAMdebugFILE='1'; ### leave blank for no Jam call debug file writing
$max_vicidial_trunks=0; ### setting a default value for max_vicidial_trunks

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

	&get_time_now;	# update time/date variables

if (!$VDADLOGfile) {$VDADLOGfile = "$PATHlogs/vdautodial.$year-$mon-$mday";}
if (!$JAMdebugFILE) {$JAMdebugFILE = "$PATHlogs/vdad-JAM.$year-$mon-$mday";}

use Time::HiRes ('gettimeofday','usleep','sleep');  # necessary to have perl sleep command of less than one second
use DBI;
	
### connect to MySQL database defined in the conf file
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
	$max_vicidial_trunks = $DBmax_vicidial_trunks;
	if ($DBanswer_transfer_agent)	{$answer_transfer_agent = $DBanswer_transfer_agent;}
	if ($DBSERVER_GMT)				{$SERVER_GMT = $DBSERVER_GMT;}
	if ($DBext_context)				{$ext_context = $DBext_context;}
	if ($DBvd_server_logs =~ /Y/)	{$SYSLOG = '1';}
	else {$SYSLOG = '0';}
	}
$sthA->finish();

$event_string='LOGGED INTO MYSQL SERVER ON 1 CONNECTION|';
&event_logger;

#############################################
##### START QUEUEMETRICS LOGGING LOOKUP #####
$stmtA = "SELECT enable_queuemetrics_logging,queuemetrics_server_ip,queuemetrics_dbname,queuemetrics_login,queuemetrics_pass,queuemetrics_log_id,outbound_autodial_active,queuemetrics_loginout,queuemetrics_addmember_enabled FROM system_settings;";
$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
$sthArows=$sthA->rows;
if ($sthArows > 0)
	{
	@aryA = $sthA->fetchrow_array;
	$enable_queuemetrics_logging =		$aryA[0];
	$queuemetrics_server_ip	=			$aryA[1];
	$queuemetrics_dbname =				$aryA[2];
	$queuemetrics_login=				$aryA[3];
	$queuemetrics_pass =				$aryA[4];
	$queuemetrics_log_id =				$aryA[5];
	$outbound_autodial_active =			$aryA[6];
	$queuemetrics_loginout =			$aryA[7];
	$queuemetrics_addmember_enabled =	$aryA[8];
	}
$sthA->finish();
##### END QUEUEMETRICS LOGGING LOOKUP #####
###########################################


$one_day_interval = 12;		# 1 month loops for one year 
while($one_day_interval > 0)
	{
	$endless_loop=5760000;		# 30 days minutes at XXX seconds per loop
	$stat_count=1;

	while($endless_loop > 0)
		{
		&get_time_now;

		$VDADLOGfile = "$PATHlogs/vdautodial.$year-$mon-$mday";

	###############################################################################
	###### first figure out how many calls should be placed for each campaign per server
	###############################################################################
		@DBlive_user=@MT;
		@DBlive_server_ip=@MT;
		@DBlive_campaign=@MT;
		@DBlive_conf_exten=@MT;
		@DBlive_status=@MT;
		@DBlive_call_id=@MT;
		@DBcampaigns=@MT;
		@DBIPaddress=@MT;
		@DBIPcampaign=@MT;
		@DBIPactive=@MT;
		@DBIPvdadexten=@MT;
		@DBIPcount=@MT;
		@DBIPACTIVEcount=@MT;
		@DBIPINCALLcount=@MT;
		@DBIPDEADcount=@MT;
		@DBIPadlevel=@MT;
		@DBIPdialtimeout=@MT;
		@DBIPdialprefix=@MT;
		@DBIPcampaigncid=@MT;
		@DBIPexistcalls=@MT;
		@DBIPexistcalls_IN=@MT;
		@DBIPexistcalls_IN_ALL=@MT;
		@DBIPexistcalls_IN_LIVE=@MT;
		@DBIPexistcalls_OUT=@MT;
		@DBIPgoalcalls=@MT;
		@DBIPmakecalls=@MT;
		@DBIPlivecalls=@MT;
		@DBIPclosercamp=@MT;
		@DBIPomitcode=@MT;
		@DBIPautoaltdial=@MT;
		@DBIPtrunk_shortage=@MT;
		@DBIPcampaign_ready_agents=@MT;
		@DBIPold_trunk_shortage=@MT;
		@DBIPserver_trunks_limit=@MT;
		@DBIPserver_trunks_other=@MT;
		@DBIPserver_trunks_allowed=@MT;
		@DBIPqueue_priority=@MT;
		@DBIPdial_method=@MT;
		@DBIPuse_custom_cid=@MT;
		@DBIPinbound_queue_no_dial=@MT;
		@DBIPavailable_only_tally=@MT;
		@DBIPavailable_only_tally_threshold=@MT;
		@DBIPavailable_only_tally_threshold_agents=@MT;
		@DBIPdial_level_threshold=@MT;
		@DBIPdial_level_threshold_agents=@MT;
		@DBIPadaptive_dl_diff_target=@MT;
		@DBIPdl_diff_target_method=@MT;

		$active_line_counter=0;
		$user_counter=0;
		$user_campaigns = '|';
		$user_campaignsSQL = "''";
		$user_campaigns_counter = 0;
		$user_campaignIP = '|';
		$user_CIPct = 0;
		$active_agents = "'READY','QUEUE','INCALL','DONE'";
		$lists_update = '';
		$LUcount=0;
		$campaigns_update = '';
		$CPcount=0;

		##### Get maximum calls per second that this process can send out
		$stmtA = "SELECT outbound_calls_per_second FROM servers where server_ip='$server_ip';";
		$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
		$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
		$sthArows=$sthA->rows;
		if ($sthArows > 0)
			{
			@aryA = $sthA->fetchrow_array;
			$outbound_calls_per_second =	$aryA[0];
			}
		$sthA->finish();

		if ( ($outbound_calls_per_second > 0) && ($outbound_calls_per_second < 201) )
			{$per_call_delay = (1000 / $outbound_calls_per_second);}
		else
			{$per_call_delay = '25';}

		$event_string="SERVER CALLS PER SECOND MAXIMUM SET TO: $outbound_calls_per_second |$per_call_delay|";
		&event_logger;




		#############################################
		##### Check if auto-dialing is enabled
		$stmtA = "SELECT outbound_autodial_active,noanswer_log,alt_log_server_ip,alt_log_dbname,alt_log_login,alt_log_pass,tables_use_alt_log_db FROM system_settings;";
		$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
		$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
		$sthArows=$sthA->rows;
		if ($sthArows > 0)
			{
			@aryA = $sthA->fetchrow_array;
			$outbound_autodial_active =		$aryA[0];
			$noanswer_log =					$aryA[1];
			$alt_log_server_ip =			$aryA[2];
			$alt_log_dbname =				$aryA[3];
			$alt_log_login =				$aryA[4];
			$alt_log_pass =					$aryA[5];
			$tables_use_alt_log_db =		$aryA[6];
			}
		$sthA->finish();

		##### Get a listing of the users that are active and ready to take calls
		##### Also get a listing of the campaigns and campaigns/serverIP that will be used
		$stmtA = "SELECT user,server_ip,campaign_id,conf_exten,status,callerid FROM vicidial_live_agents where status IN($active_agents) and outbound_autodial='Y' and server_ip='$server_ip' and last_update_time > '$BDtsSQLdate' order by last_call_time";
		$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
		$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
		$sthArows=$sthA->rows;
		$rec_count=0;
		while ($sthArows > $rec_count)
			{
			@aryA = $sthA->fetchrow_array;
			$DBlive_user[$user_counter] =		$aryA[0];
			$DBlive_server_ip[$user_counter] =	$aryA[1];
			$DBlive_campaign[$user_counter] =	$aryA[2];
			$DBlive_conf_exten[$user_counter] =	$aryA[3];
			$DBlive_status[$user_counter] =		$aryA[4];
			$DBlive_call_id[$user_counter] =	$aryA[5];
			
			if ($user_campaigns !~ /\|$DBlive_campaign[$user_counter]\|/i)
				{
				if ($campaigns_update !~ /'$DBlive_campaign[$user_counter]'/) {$campaigns_update .= "'$DBlive_campaign[$user_counter]',"; $CPcount++;}
				$user_campaigns .= "$DBlive_campaign[$user_counter]|";
				$user_campaignsSQL .= ",'$DBlive_campaign[$user_counter]'";
				$DBcampaigns[$user_campaigns_counter] = $DBlive_campaign[$user_counter];
				$user_campaigns_counter++;
				}
			if ($user_campaignIP !~ /\|$DBlive_campaign[$user_counter]__$DBlive_server_ip[$user_counter]\|/i)
				{
				$user_campaignIP .= "$DBlive_campaign[$user_counter]__$DBlive_server_ip[$user_counter]|";
				$DBIPcampaign[$user_CIPct] = "$DBlive_campaign[$user_counter]";
				$DBIPaddress[$user_CIPct] = "$DBlive_server_ip[$user_counter]";
				$user_CIPct++;
				}
			$user_counter++;
			$rec_count++;
			}
		$sthA->finish();

		### see how many total VDAD calls are going on right now for max limiter
		$stmtA = "SELECT count(*) FROM vicidial_auto_calls where server_ip='$server_ip' and status IN('SENT','RINGING','LIVE','XFER','CLOSER','IVR');";
		$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
		$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
		$sthArows=$sthA->rows;
		if ($sthArows > 0)
			{
			@aryA = $sthA->fetchrow_array;
			$active_line_counter = $aryA[0];
			}
		$sthA->finish();

		$event_string="LIVE AGENTS LOGGED IN: $user_counter   ACTIVE CALLS: $active_line_counter";
		&event_logger;

		$stmtA = "UPDATE vicidial_campaign_server_stats set local_trunk_shortage='0' where server_ip='$server_ip' and campaign_id NOT IN($user_campaignsSQL);";
		$UVCSSaffected_rows = $dbhA->do($stmtA);
		if ($UVCSSaffected_rows > 0) 
			{
			$event_string="OLD TRUNK SHORTS CLEARED: $UVCSSaffected_rows |$user_campaignsSQL|";
			&event_logger;
			}
		$user_CIPct = 0;
		foreach(@DBIPcampaign)
			{
			$debug_string='';
			$user_counter=0;
			foreach(@DBlive_campaign)
				{
				if ( ($DBlive_campaign[$user_counter] =~ /$DBIPcampaign[$user_CIPct]/i) && (length($DBlive_campaign[$user_counter]) == length($DBIPcampaign[$user_CIPct])) && ($DBlive_server_ip[$user_counter] =~ /$DBIPaddress[$user_CIPct]/i) )
					{
					$DBIPcount[$user_CIPct]++;
					$DBIPACTIVEcount[$user_CIPct] = ($DBIPACTIVEcount[$user_CIPct] + 0);
					$DBIPINCALLcount[$user_CIPct] = ($DBIPINCALLcount[$user_CIPct] + 0);
					$DBIPDEADcount[$user_CIPct] = ($DBIPDEADcount[$user_CIPct] + 0);
					if ($DBlive_status[$user_counter] =~ /READY|DONE/) 
						{
						$DBIPACTIVEcount[$user_CIPct]++;
						}
					else
						{
						$stmtA = "SELECT count(*) FROM vicidial_auto_calls where callerid='$DBlive_call_id[$user_counter]';";
						$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
						$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
						$sthArows=$sthA->rows;
						if ($sthArows > 0)
							{
							@aryA = $sthA->fetchrow_array;
							if ($aryA[0] > 0)
								{
								$DBIPINCALLcount[$user_CIPct]++;
								}
							else
								{
								$DBIPDEADcount[$user_CIPct]++;
								}
							}
						else
							{
							$DBIPINCALLcount[$user_CIPct]++;
							}
						}
					}
				$user_counter++;
				}

			### get count of READY-status agents in this campaign
			$stmtA = "SELECT count(*) FROM vicidial_live_agents where campaign_id='$DBIPcampaign[$user_CIPct]' and server_ip='$server_ip' and status='READY';";
			$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
			$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
			$sthArows=$sthA->rows;
			if ($sthArows > 0)
				{
				@aryA = $sthA->fetchrow_array;
				$DBIPcampaign_ready_agents[$user_CIPct] =		$aryA[0];
				}

			### check for vicidial_campaign_server_stats record, if non present then create it
			$stmtA = "SELECT local_trunk_shortage FROM vicidial_campaign_server_stats where campaign_id='$DBIPcampaign[$user_CIPct]' and server_ip='$server_ip';";
			$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
			$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
			$sthArows=$sthA->rows;
			$rec_count=0;
			while ($sthArows > $rec_count)
				{
				@aryA = $sthA->fetchrow_array;
				$DBIPold_trunk_shortage[$user_CIPct] =		$aryA[0];
				$rec_count++;
				}
			if ($rec_count < 1)
				{
				$stmtA = "INSERT INTO vicidial_campaign_server_stats SET local_trunk_shortage='0', server_ip='$server_ip',campaign_id='$DBIPcampaign[$user_CIPct]';";
				$affected_rows = $dbhA->do($stmtA);

				$DBIPold_trunk_shortage[$user_CIPct]=0;

				$event_string="VCSS ENTRY INSERTED: $affected_rows";
				$debug_string .= "$event_string\n";
				&event_logger;
				}

			$DBIPserver_trunks_limit[$user_CIPct] = '';
			$DBIPserver_trunks_other[$user_CIPct] = 0;
			$DBIPserver_trunks_allowed[$user_CIPct] = $max_vicidial_trunks;
			### check for vicidial_server_trunks record
			$stmtA = "SELECT dedicated_trunks FROM vicidial_server_trunks where campaign_id='$DBIPcampaign[$user_CIPct]' and server_ip='$server_ip' and trunk_restriction='MAXIMUM_LIMIT';";
			$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
			$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
			$sthArows=$sthA->rows;
			$rec_count=0;
			while ($sthArows > $rec_count)
				{
				@aryA = $sthA->fetchrow_array;
				$DBIPserver_trunks_limit[$user_CIPct] =		$aryA[0];
				$rec_count++;
				}
			$stmtA = "SELECT sum(dedicated_trunks) FROM vicidial_server_trunks where campaign_id NOT IN('$DBIPcampaign[$user_CIPct]') and server_ip='$server_ip';";
			$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
			$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
			$sthArows=$sthA->rows;
			$rec_count=0;
			while ($sthArows > $rec_count)
				{
				@aryA = $sthA->fetchrow_array;
				$DBIPserver_trunks_other[$user_CIPct] =		$aryA[0];
				$rec_count++;
				}

			$DBIPserver_trunks_allowed[$user_CIPct] = ($max_vicidial_trunks - $DBIPserver_trunks_other[$user_CIPct]);


			### grab the dial_level and multiply by active agents to get your goalcalls
			$DBIPadlevel[$user_CIPct]=0;
			$stmtA = "SELECT auto_dial_level,local_call_time,dial_timeout,dial_prefix,campaign_cid,active,campaign_vdad_exten,closer_campaigns,omit_phone_code,available_only_ratio_tally,auto_alt_dial,campaign_allow_inbound,queue_priority,dial_method,use_custom_cid,inbound_queue_no_dial,available_only_tally_threshold,available_only_tally_threshold_agents,dial_level_threshold,dial_level_threshold_agents,adaptive_dl_diff_target,dl_diff_target_method FROM vicidial_campaigns where campaign_id='$DBIPcampaign[$user_CIPct]'";
			$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
			$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
			$sthArows=$sthA->rows;
			$rec_count=0;
			$active_only=0;
			while ($sthArows > $rec_count)
				{
				@aryA = $sthA->fetchrow_array;
				$DBIPadlevel[$user_CIPct] =			$aryA[0];
				$DBIPcalltime[$user_CIPct] =		$aryA[1];
				$DBIPdialtimeout[$user_CIPct] =		$aryA[2];
				$DBIPdialprefix[$user_CIPct] =		$aryA[3];
				$DBIPcampaigncid[$user_CIPct] =		$aryA[4];
				$DBIPactive[$user_CIPct] =			$aryA[5];
				$DBIPvdadexten[$user_CIPct] =		$aryA[6];
				$DBIPclosercamp[$user_CIPct] =		$aryA[7];
				$omit_phone_code =					$aryA[8];
				if ($omit_phone_code =~ /Y/) {$DBIPomitcode[$user_CIPct] = 1;}
				else {$DBIPomitcode[$user_CIPct] = 0;}
				$DBIPavailable_only_tally[$user_CIPct] =		$aryA[9];
				$DBIPautoaltdial[$user_CIPct] =					$aryA[10];
				$DBIPcampaign_allow_inbound[$user_CIPct] =		$aryA[11];
				$DBIPqueue_priority[$user_CIPct] =				$aryA[12];
				$DBIPdial_method[$user_CIPct] =					$aryA[13];
				$DBIPuse_custom_cid[$user_CIPct] =				$aryA[14];
				$DBIPinbound_queue_no_dial[$user_CIPct] =		$aryA[15];
				$DBIPavailable_only_tally_threshold[$user_CIPct] = $aryA[16];
				$DBIPavailable_only_tally_threshold_agents[$user_CIPct] = $aryA[17];
				$DBIPdial_level_threshold[$user_CIPct] =		$aryA[18];
				$DBIPdial_level_threshold_agents[$user_CIPct] = $aryA[19];
				$DBIPadaptive_dl_diff_target[$user_CIPct] =		$aryA[20];
				$DBIPdl_diff_target_method[$user_CIPct] =		$aryA[21];
				if ($DBIPdl_diff_target_method[$user_CIPct] =~ /ADAPT_CALC_ONLY/)
					{
					if ( ($DBIPadaptive_dl_diff_target[$user_CIPct] < 0) || ($DBIPadaptive_dl_diff_target[$user_CIPct] > 0) )
						{
						$debug_string .= "   !! DL DIFF TARGET SET TO 0, CALC-ONLY MODE: $DBIPdl_diff_target_method[$user_CIPct]|$DBIPadaptive_dl_diff_target[$user_CIPct]\n";
						}
					$DBIPadaptive_dl_diff_target[$user_CIPct] = 0;
					}
				if ($DBIPavailable_only_tally[$user_CIPct] =~ /Y/) 
					{
					$DBIPcount[$user_CIPct] = $DBIPACTIVEcount[$user_CIPct];
					$active_only=1;
					}
				else
					{
					### Check for available only tally threshold ###
					if ( ($DBIPavailable_only_tally_threshold[$user_CIPct] =~ /LOGGED-IN_AGENTS/) && ($DBIPavailable_only_tally_threshold_agents[$user_CIPct] > $user_counter) )
						{
						$debug_string .= "   !! AVAILABLE ONLY TALLY THRESHOLD triggered for LOGGED-IN_AGENTS: ($DBIPavailable_only_tally_threshold_agents[$user_CIPct] > $user_counter)\n";
						$active_only=1;
						}
					if ( ($DBIPavailable_only_tally_threshold[$user_CIPct] =~ /NON-PAUSED_AGENTS/) && ($DBIPavailable_only_tally_threshold_agents[$user_CIPct] > $DBIPcount[$user_CIPct]) )
						{
						$debug_string .= "   !! AVAILABLE ONLY TALLY THRESHOLD triggered for NON-PAUSED_AGENTS: ($DBIPavailable_only_tally_threshold_agents[$user_CIPct] > $DBIPcount[$user_CIPct])\n";
						$active_only=1;
						}
					if ( ($DBIPavailable_only_tally_threshold[$user_CIPct] =~ /WAITING_AGENTS/) && ($DBIPavailable_only_tally_threshold_agents[$user_CIPct] > $DBIPACTIVEcount[$user_CIPct]) )
						{
						$debug_string .= "   !! AVAILABLE ONLY TALLY THRESHOLD triggered for WAITING_AGENTS: ($DBIPavailable_only_tally_threshold_agents[$user_CIPct] > $DBIPACTIVEcount[$user_CIPct])\n";
						$active_only=1;
						}

					if ($active_only > 0) 
						{$DBIPcount[$user_CIPct] = $DBIPACTIVEcount[$user_CIPct];}
					else
						{$DBIPcount[$user_CIPct] = ($DBIPcount[$user_CIPct] - $DBIPDEADcount[$user_CIPct]);}

					if ($DBIPcount[$user_CIPct] < 0)
						{$DBIPcount[$user_CIPct]=0;}
					}

				$rec_count++;
				}
			$sthA->finish();

			### Check for dial level threshold ###
			$nonpause_agents_temp = ($DBIPACTIVEcount[$user_CIPct] + $DBIPINCALLcount[$user_CIPct]);
			if ( ($DBIPdial_level_threshold[$user_CIPct] =~ /LOGGED-IN_AGENTS/) && ($DBIPdial_level_threshold_agents[$user_CIPct] > $user_counter) )
				{
				$debug_string .= "   !! DIAL LEVEL THRESHOLD triggered for LOGGED-IN_AGENTS: ($DBIPdial_level_threshold_agents[$user_CIPct] > $user_counter)\n";
				$DBIPadlevel[$user_CIPct]=1;
				}
			if ( ($DBIPdial_level_threshold[$user_CIPct] =~ /NON-PAUSED_AGENTS/) && ($DBIPdial_level_threshold_agents[$user_CIPct] > $nonpause_agents_temp) )
				{
				$debug_string .= "   !! DIAL LEVEL THRESHOLD triggered for NON-PAUSED_AGENTS: ($DBIPdial_level_threshold_agents[$user_CIPct] > $nonpause_agents_temp)\n";
				$DBIPadlevel[$user_CIPct]=1;
				}
			if ( ($DBIPdial_level_threshold[$user_CIPct] =~ /WAITING_AGENTS/) && ($DBIPdial_level_threshold_agents[$user_CIPct] > $DBIPACTIVEcount[$user_CIPct]) )
				{
				$debug_string .= "   !! DIAL LEVEL THRESHOLD triggered for WAITING_AGENTS: ($DBIPdial_level_threshold_agents[$user_CIPct] > $DBIPACTIVEcount[$user_CIPct])\n";
				$DBIPadlevel[$user_CIPct]=1;
				}

			### apply dial level difference target ###
			$DBIPcount[$user_CIPct] = ($DBIPcount[$user_CIPct] + $DBIPadaptive_dl_diff_target[$user_CIPct]);
			if ($DBIPcount[$user_CIPct] < 0)
				{$DBIPcount[$user_CIPct]=0;}

			$DBIPgoalcalls[$user_CIPct] = ($DBIPadlevel[$user_CIPct] * $DBIPcount[$user_CIPct]);
			if ($active_only > 0) 
				{
				$tally_xfer_line_counter=0;
				### see how many VDAD calls are live as XFERs to agents
				$stmtA = "SELECT count(*) FROM vicidial_auto_calls where server_ip='$DBIPaddress[$user_CIPct]' and campaign_id='$DBIPcampaign[$user_CIPct]' and status IN('XFER','CLOSER');";
				$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
				$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
				$sthArows=$sthA->rows;
				if ($sthArows > 0)
					{
					@aryA = $sthA->fetchrow_array;
					$tally_xfer_line_counter = $aryA[0];
					}
				$sthA->finish();

				$DBIPgoalcalls[$user_CIPct] = ($DBIPgoalcalls[$user_CIPct] + $tally_xfer_line_counter);
				}
			if ($DBIPactive[$user_CIPct] =~ /N/) {$DBIPgoalcalls[$user_CIPct] = 0;}
			$DBIPgoalcalls[$user_CIPct] = sprintf("%.0f", $DBIPgoalcalls[$user_CIPct]);

			$event_string="$DBIPcampaign[$user_CIPct] $DBIPaddress[$user_CIPct]: agents: $DBIPcount[$user_CIPct] (READY: $DBIPcampaign_ready_agents[$user_CIPct])    dial_level: $DBIPadlevel[$user_CIPct]     ($DBIPACTIVEcount[$user_CIPct]|$DBIPINCALLcount[$user_CIPct]|$DBIPDEADcount[$user_CIPct])   $DBIPadaptive_dl_diff_target[$user_CIPct]";
			$debug_string .= "$event_string\n";
			&event_logger;


			### see how many calls are already active per campaign per server and 
			### subtract that number from goalcalls to determine how many new 
			### calls need to be placed in this loop
			if ($DBIPcampaign_allow_inbound[$user_CIPct] =~ /Y/)
				{
				if (length($DBIPclosercamp[$user_CIPct]) > 2)
					{
					$DBIPclosercamp[$user_CIPct] =~ s/^ | -$//gi;
					$DBIPclosercamp[$user_CIPct] =~ s/ /','/gi;
					$DBIPclosercamp[$user_CIPct] = "'$DBIPclosercamp[$user_CIPct]'";
					}
				else {$DBIPclosercamp[$user_CIPct]="''";}

				$stmtA = "SELECT count(*) FROM vicidial_auto_calls where (call_type='IN' and campaign_id IN($DBIPclosercamp[$user_CIPct])) and server_ip='$DBIPaddress[$user_CIPct]' and status IN('SENT','RINGING','LIVE','XFER','CLOSER');";
				$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
				$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
				$sthArows=$sthA->rows;
				if ($sthArows > 0)
					{
					@aryA = $sthA->fetchrow_array;
					$DBIPexistcalls_IN[$user_CIPct] = $aryA[0];
					}
				$sthA->finish();

				$stmtA = "SELECT count(*) FROM vicidial_auto_calls where (call_type='IN' and campaign_id IN($DBIPclosercamp[$user_CIPct])) and status IN('SENT','RINGING','LIVE','XFER','CLOSER');";
				$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
				$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
				$sthArows=$sthA->rows;
				if ($sthArows > 0)
					{
					@aryA = $sthA->fetchrow_array;
					$DBIPexistcalls_IN_ALL[$user_CIPct] = $aryA[0];
					}
				$sthA->finish();

				$stmtA = "SELECT count(*) FROM vicidial_auto_calls where call_type='IN' and campaign_id IN($DBIPclosercamp[$user_CIPct]) and status IN('LIVE');";
				$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
				$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
				$sthArows=$sthA->rows;
				if ($sthArows > 0)
					{
					@aryA = $sthA->fetchrow_array;
					$DBIPexistcalls_IN_LIVE[$user_CIPct] = $aryA[0];
					}
				$sthA->finish();
				}
			else 
				{$DBIPexistcalls_IN[$user_CIPct]=0;}

			$stmtA = "SELECT count(*) FROM vicidial_auto_calls where (campaign_id='$DBIPcampaign[$user_CIPct]' and call_type IN('OUT','OUTBALANCE')) and server_ip='$DBIPaddress[$user_CIPct]' and status IN('SENT','RINGING','LIVE','XFER','CLOSER');";
			$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
			$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
			$sthArows=$sthA->rows;
			if ($sthArows > 0)
				{
				@aryA = $sthA->fetchrow_array;
				$DBIPexistcalls_OUT[$user_CIPct] = $aryA[0];
				}
			$sthA->finish();

			$DBIPexistcalls[$user_CIPct] = ($DBIPexistcalls_IN[$user_CIPct] + $DBIPexistcalls_OUT[$user_CIPct]);
			if ($DBIPinbound_queue_no_dial[$user_CIPct] =~ /ALL_SERVERS/) 
				{$DBIPexistcalls[$user_CIPct] = ($DBIPexistcalls_IN_ALL[$user_CIPct] + $DBIPexistcalls_OUT[$user_CIPct]);}

			if ( ($DBIPcampaign_ready_agents[$user_CIPct] > 0) && ($DBIPexistcalls_IN[$user_CIPct] > 0) )
				{
				$event_string="     BLENDED-OUTBOUND-AGENTS-WAITING OVERRIDE: $DBIPcampaign[$user_CIPct] $DBIPexistcalls[$user_CIPct] [$DBIPexistcalls_IN[$user_CIPct] + $DBIPexistcalls_OUT[$user_CIPct]])";
				$debug_string .= "$event_string\n";
				&event_logger;

				$DBIPexistcalls[$user_CIPct] = $DBIPexistcalls_OUT[$user_CIPct];
				}

			$active_line_goal=0;
			$DBIPmakecalls[$user_CIPct] = ($DBIPgoalcalls[$user_CIPct] - $DBIPexistcalls[$user_CIPct]);
			$DBIPmakecallsGOAL = $DBIPmakecalls[$user_CIPct];
			$MVT_msg = '';
			$DBIPtrunk_shortage[$user_CIPct] = 0;
			$active_line_goal = ($active_line_counter + $DBIPmakecalls[$user_CIPct]);
			if ($active_line_goal > $max_vicidial_trunks) 
				{
				$NEWmakecallsgoal = ($max_vicidial_trunks - $active_line_counter);
				if ($DBIPmakecalls[$user_CIPct] > $NEWmakecallsgoal)
					{$DBIPmakecalls[$user_CIPct] = $NEWmakecallsgoal;}
				$DBIPtrunk_shortage[$user_CIPct] = ($active_line_goal - $max_vicidial_trunks);
				if ($DBIPtrunk_shortage[$user_CIPct] > $DBIPmakecallsGOAL) 
					{$DBIPtrunk_shortage[$user_CIPct] = $DBIPmakecallsGOAL}
				$MVT_msg .= "     MVT override: $max_vicidial_trunks |$DBIPmakecalls[$user_CIPct] $DBIPtrunk_shortage[$user_CIPct]|";
				}
			if (length($DBIPserver_trunks_limit[$user_CIPct])>0) 
				{
				if ($DBIPserver_trunks_limit[$user_CIPct] < $DBIPgoalcalls[$user_CIPct])
					{
					$MVT_msg .= "     TRUNK LIMIT override: $DBIPserver_trunks_limit[$user_CIPct]";
					$DBIPtrunk_shortage[$user_CIPct] = ($DBIPgoalcalls[$user_CIPct] - $DBIPserver_trunks_limit[$user_CIPct]);
					$DBIPmakecalls[$user_CIPct] = ($DBIPserver_trunks_limit[$user_CIPct] - $DBIPexistcalls[$user_CIPct]);
					if ($DBIPtrunk_shortage[$user_CIPct] > $DBIPmakecallsGOAL) 
						{$DBIPtrunk_shortage[$user_CIPct] = $DBIPmakecallsGOAL}
					$active_line_goal = $DBIPserver_trunks_limit[$user_CIPct];
					}
				}
			else
				{
				if ($DBIPserver_trunks_allowed[$user_CIPct] < $active_line_goal)
					{
					$MVT_msg .= "     OTHER LIMIT override: $DBIPserver_trunks_allowed[$user_CIPct]";
					$DBIPtrunk_shortage[$user_CIPct] = ($active_line_goal - $DBIPserver_trunks_allowed[$user_CIPct]);
					if ($DBIPtrunk_shortage[$user_CIPct] > $DBIPmakecallsGOAL) 
						{$DBIPtrunk_shortage[$user_CIPct] = $DBIPmakecallsGOAL}
					$active_line_goal = $DBIPserver_trunks_allowed[$user_CIPct];
					$NEWmakecallsgoal = ($active_line_goal - $active_line_counter);
					if ($DBIPmakecalls[$user_CIPct] > $NEWmakecallsgoal)
						{$DBIPmakecalls[$user_CIPct] = $NEWmakecallsgoal;}
					}
				}

			if ($DBIPmakecalls[$user_CIPct] > 0) 
				{$active_line_counter = ($DBIPmakecalls[$user_CIPct] + $active_line_counter);}

			$event_string="$DBIPcampaign[$user_CIPct] $DBIPaddress[$user_CIPct]: Calls to place: $DBIPmakecalls[$user_CIPct] ($DBIPgoalcalls[$user_CIPct] - $DBIPexistcalls[$user_CIPct] [$DBIPexistcalls_IN[$user_CIPct] + $DBIPexistcalls_OUT[$user_CIPct]|$DBIPexistcalls_IN_ALL[$user_CIPct]|$DBIPexistcalls_IN_LIVE[$user_CIPct]]) $active_line_counter $MVT_msg";
			$debug_string .= "$event_string\n";
			&event_logger;

			### Calculate campaign-wide agent waiting and calls waiting differential
			### This is used by the AST_VDadapt script to see if the current dial_level
			### should be changed at all
			$total_agents=0;
			$ready_agents=0;
			$waiting_calls=0;
			$waiting_calls_IN=0;
			$waiting_calls_OUT=0;

			$stmtA = "SELECT count(*),status from vicidial_live_agents where campaign_id='$DBIPcampaign[$user_CIPct]' and last_update_time > '$halfminSQLdate' group by status;";
			$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
			$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
			$sthArows=$sthA->rows;
			$rec_count=0;
			while ($sthArows > $rec_count)
				{
				@aryA = $sthA->fetchrow_array;
				$VCSagent_count =		 $aryA[0];
				$VCSagent_status =		 $aryA[1];
				$rec_count++;
				if ($VCSagent_status =~ /READY|DONE/) {$ready_agents = ($ready_agents + $VCSagent_count);}
				$total_agents = ($total_agents + $VCSagent_count);
				}
			$sthA->finish();

			if ($DBIPcampaign_allow_inbound[$user_CIPct] =~ /Y/)
				{
				$stmtA = "SELECT count(*) FROM vicidial_auto_calls where (call_type='IN' and campaign_id IN($DBIPclosercamp[$user_CIPct])) and status IN('LIVE');";
				$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
				$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
				$sthArows=$sthA->rows;
				if ($sthArows > 0)
					{
					@aryA = $sthA->fetchrow_array;
					$waiting_calls_IN = $aryA[0];
					}
				$sthA->finish();
				}
			else
				{$waiting_calls_IN=0;}

			$stmtA = "SELECT count(*) FROM vicidial_auto_calls where (campaign_id='$DBIPcampaign[$user_CIPct]' and call_type IN('OUT','OUTBALANCE')) and status IN('LIVE');";
			$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
			$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
			$sthArows=$sthA->rows;
			if ($sthArows > 0)
				{
				@aryA = $sthA->fetchrow_array;
				$waiting_calls_OUT = $aryA[0];
				}
			$sthA->finish();

			$waiting_calls = ($waiting_calls_IN + $waiting_calls_OUT);

			$stat_ready_agents[$user_CIPct][$stat_count] = $ready_agents;
			$stat_waiting_calls[$user_CIPct][$stat_count] = $waiting_calls;
			$stat_total_agents[$user_CIPct][$stat_count] = $total_agents;

			$stat_it=20;
			$ready_diff_total=0;
			$waiting_diff_total=0;
			$total_agents_total=0;
			$ready_diff_avg=0;
			$waiting_diff_avg=0;
			$total_agents_avg=0;
			$stat_differential=0;
			if ($stat_count < 20) 
				{
				$stat_it = $stat_count;
				$stat_B = 1;
				}
			else
				{
				$stat_B = ($stat_count - 19);
				}
			
			$it=0;
			while($it < $stat_it)
				{
				$it_ary = ($it + $stat_B);
				$ready_diff_total = ($ready_diff_total + $stat_ready_agents[$user_CIPct][$it_ary]);
				$waiting_diff_total = ($waiting_diff_total + $stat_waiting_calls[$user_CIPct][$it_ary]);
				$total_agents_total = ($total_agents_total + $stat_total_agents[$user_CIPct][$it_ary]);
		#		$event_string="$stat_count $it_ary   $stat_total_agents[$user_CIPct][$it_ary]|$stat_ready_agents[$user_CIPct][$it_ary]|$stat_waiting_calls[$user_CIPct][$it_ary]";
		#		&event_logger;
				$it++;
				}
			
			if ($ready_diff_total > 0) 
				{$ready_diff_avg = ($ready_diff_total / $stat_it);}
			if ($waiting_diff_total > 0) 
				{$waiting_diff_avg = ($waiting_diff_total / $stat_it);}
			if ($total_agents_total > 0) 
				{$total_agents_avg = ($total_agents_total / $stat_it);}
			$stat_differential = ($ready_diff_avg - $waiting_diff_avg);

			$event_string="CAMPAIGN DIFFERENTIAL: $total_agents_avg   $stat_differential   ($ready_diff_avg - $waiting_diff_avg)";
			$debug_string .= "$event_string\n";
			&event_logger;

			$stmtA = "UPDATE vicidial_campaign_stats SET differential_onemin='$stat_differential', agents_average_onemin='$total_agents_avg' where campaign_id='$DBIPcampaign[$user_CIPct]';";
			$affected_rows = $dbhA->do($stmtA);

			if ( ($DBIPold_trunk_shortage[$user_CIPct] > $DBIPtrunk_shortage[$user_CIPct]) || ($DBIPold_trunk_shortage[$user_CIPct] < $DBIPtrunk_shortage[$user_CIPct]) )
				{
				if ( ($DBIPadlevel[$user_CIPct] < 1) || ($DBIPdial_method[$user_CIPct] =~ /MANUAL|INBOUND_MAN/) )
					{
					$event_string="Manual Dial Override for Shortage |$DBIPadlevel[$user_CIPct]|$DBIPtrunk_shortage[$user_CIPct]|";
					&event_logger;
					$debug_string .= "$event_string\n";
					$DBIPtrunk_shortage[$user_CIPct] = 0;
					}
				$stmtA = "UPDATE vicidial_campaign_server_stats SET local_trunk_shortage='$DBIPtrunk_shortage[$user_CIPct]',update_time='$now_date' where server_ip='$server_ip' and campaign_id='$DBIPcampaign[$user_CIPct]';";
				$affected_rows = $dbhA->do($stmtA);
				}

			$event_string="LOCAL TRUNK SHORTAGE: $DBIPtrunk_shortage[$user_CIPct]|$DBIPold_trunk_shortage[$user_CIPct]  ($active_line_goal - $max_vicidial_trunks)";
			$debug_string .= "$event_string\n";
			&event_logger;

			$stmtA="INSERT IGNORE INTO vicidial_campaign_stats_debug SET server_ip='$server_ip',campaign_id='$DBIPcampaign[$user_CIPct]',entry_time='$now_date',debug_output='$debug_string' ON DUPLICATE KEY UPDATE entry_time='$now_date',debug_output='$debug_string';";
			$affected_rows = $dbhA->do($stmtA);

			$user_CIPct++;
			}

	###############################################################################
	###### second lookup leads and place calls for each campaign/server_ip
	######     go one lead at a time and place the call by inserting a record into vicidial_manager
	###############################################################################

		$user_CIPct = 0;
		foreach(@DBIPcampaign)
			{
			$calls_placed=0;
			if ( ($DBIPdial_method[$user_CIPct] =~ /MANUAL|INBOUND_MAN/) || ($outbound_autodial_active < 1) )
				{
				$event_string="$DBIPcampaign[$user_CIPct] $DBIPaddress[$user_CIPct]: MANUAL DIAL CAMPAIGN, NO DIALING";
				&event_logger;
				}
			else
				{
				if ( ($DBIPinbound_queue_no_dial[$user_CIPct] =~ /ENABLED/) && ($DBIPexistcalls_IN_LIVE[$user_CIPct] > 0) )
					{
					$event_string="$DBIPcampaign[$user_CIPct] $DBIPexistcalls_IN_LIVE[$user_CIPct]: INBOUND QUEUE NO DIAL, NO DIALING";
					&event_logger;
					}
				else
					{
					$event_string="$DBIPcampaign[$user_CIPct] $DBIPaddress[$user_CIPct]: CALLING";
					&event_logger;
					$call_CMPIPct=0;
					$lead_id_call_list='|';
					my $UDaffected_rows=0;
					if ($call_CMPIPct < $DBIPmakecalls[$user_CIPct])
						{
						$stmtA = "UPDATE vicidial_hopper set status='QUEUE', user='VDAD_$server_ip' where campaign_id='$DBIPcampaign[$user_CIPct]' and status='READY' order by priority desc,hopper_id LIMIT $DBIPmakecalls[$user_CIPct]";
						print "|$stmtA|\n";
						$UDaffected_rows = $dbhA->do($stmtA);
						print "hopper rows updated to QUEUE: |$UDaffected_rows|\n";

						if ($UDaffected_rows)
							{
							$lead_id=''; $phone_code=''; $phone_number=''; $called_count='';
							while ($call_CMPIPct < $UDaffected_rows)
								{
								$stmtA = "SELECT lead_id,alt_dial FROM vicidial_hopper where campaign_id='$DBIPcampaign[$user_CIPct]' and status='QUEUE' and user='VDAD_$server_ip' order by priority desc,hopper_id LIMIT 1";
								print "|$stmtA|\n";
								$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
								$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
								$sthArows=$sthA->rows;
								$rec_count=0;
								$rec_countCUSTDATA=0;
								while ($sthArows > $rec_count)
									{
									@aryA = $sthA->fetchrow_array;
									$lead_id =		$aryA[0];
									$alt_dial =		$aryA[1];
									$rec_count++;
									}
								$sthA->finish();

							if ($lead_id_call_list =~ /\|$lead_id\|/)
								{
								print "!!!!!!!!!!!!!!!!duplicate lead_id for this run: |$lead_id|     $lead_id_call_list\n";
								if ($SYSLOG)
									{
									open(DUPout, ">>$PATHlogs/VDAD_DUPLICATE.$file_date")
											|| die "Can't open $PATHlogs/VDAD_DUPLICATE.$file_date: $!\n";
									print DUPout "$now_date-----$lead_id_call_list-----$lead_id\n";
									close(DUPout);
									}
								}
							else
								{
								$stmtA = "UPDATE vicidial_hopper set status='INCALL' where lead_id='$lead_id'";
								print "|$stmtA|\n";
								$UQaffected_rows = $dbhA->do($stmtA);
								print "hopper row updated to INCALL: |$UQaffected_rows|$lead_id|\n";

								### Gather lead data
								$stmtA = "SELECT list_id,gmt_offset_now,called_since_last_reset,phone_code,phone_number,address3,alt_phone,called_count,security_phrase FROM vicidial_list where lead_id='$lead_id';";
								$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
								$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
								$sthArows=$sthA->rows;
								$rec_count=0;
								$rec_countCUSTDATA=0;
								if ($sthArows > 0)
									{
									@aryA = $sthA->fetchrow_array;
									$list_id =					$aryA[0];
									$gmt_offset_now	=			$aryA[1];
									$called_since_last_reset =	$aryA[2];
									$phone_code	=				$aryA[3];
									$phone_number =				$aryA[4];
									$address3 =					$aryA[5];
									$alt_phone =				$aryA[6];
									$called_count =				$aryA[7];
									$security_phrase =			$aryA[8];

									$rec_countCUSTDATA++;
									$rec_count++;
									}
								$sthA->finish();

								if ($rec_countCUSTDATA)
									{
									$campaign_cid_override='';
									### gather list_id overrides
									$stmtA = "SELECT campaign_cid_override FROM vicidial_lists where list_id='$list_id';";
									$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
									$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
									$sthArowsL=$sthA->rows;
									if ($sthArowsL > 0)
										{
										@aryA = $sthA->fetchrow_array;
										$campaign_cid_override =	$aryA[0];
										}
									$sthA->finish();

									### update called_count
									$called_count++;
									if ($called_since_last_reset =~ /^Y/)
										{
										if ($called_since_last_reset =~ /^Y$/) {$CSLR = 'Y1';}
										else
											{
											$called_since_last_reset =~ s/^Y//gi;
											$called_since_last_reset++;
											$CSLR = "Y$called_since_last_reset";
											}
										}
									else {$CSLR = 'Y';}
									
									$LLCT_DATE_offset = ($LOCAL_GMT_OFF - $gmt_offset_now);
									$LLCT_DATE_offset_epoch = ( $secX - ($LLCT_DATE_offset * 3600) );
									($Lsec,$Lmin,$Lhour,$Lmday,$Lmon,$Lyear,$Lwday,$Lyday,$Lisdst) = localtime($LLCT_DATE_offset_epoch);
									$Lyear = ($Lyear + 1900);
									$Lmon++;
									if ($Lmon < 10) {$Lmon = "0$Lmon";}
									if ($Lmday < 10) {$Lmday = "0$Lmday";}
									if ($Lhour < 10) {$Lhour = "0$Lhour";}
									if ($Lmin < 10) {$Lmin = "0$Lmin";}
									if ($Lsec < 10) {$Lsec = "0$Lsec";}
										$LLCT_DATE = "$Lyear-$Lmon-$Lmday $Lhour:$Lmin:$Lsec";

									if ( ($alt_dial =~ /ALT|ADDR3|X/) && ($DBIPautoaltdial[$user_CIPct] =~ /ALT|ADDR|X/) )
										{
										if ( ($alt_dial =~ /ALT/) && ($DBIPautoaltdial[$user_CIPct] =~ /ALT/) )
											{
											$alt_phone =~ s/\D//gi;
											$phone_number = $alt_phone;
											}
										if ( ($alt_dial =~ /ADDR3/) && ($DBIPautoaltdial[$user_CIPct] =~ /ADDR3/) )
											{
											$address3 =~ s/\D//gi;
											$phone_number = $address3;
											}
										if  ( ($alt_dial =~ /X/) && ($DBIPautoaltdial[$user_CIPct] =~ /X/) )
											{
											if ($alt_dial =~ /LAST/) 
												{
												$stmtA = "SELECT phone_code,phone_number FROM vicidial_list_alt_phones where lead_id='$lead_id' order by alt_phone_count desc limit 1;";
												}
											else
												{
												$Talt_dial = $alt_dial;
												$Talt_dial =~ s/\D//gi;
												$stmtA = "SELECT phone_code,phone_number FROM vicidial_list_alt_phones where lead_id='$lead_id' and alt_phone_count='$Talt_dial';";										
												}
											$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
											$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
											$sthArows=$sthA->rows;
											if ($sthArows > 0)
												{
												@aryA = $sthA->fetchrow_array;
												$phone_code	=	$aryA[0];
												$phone_number =	$aryA[1];
												$phone_number =~ s/\D//gi;
												}
											$sthA->finish();
											}

										$stmtA = "UPDATE vicidial_list set called_since_last_reset='$CSLR',called_count='$called_count',user='VDAD',last_local_call_time='$LLCT_DATE' where lead_id='$lead_id'";
										}
									else
										{
										$stmtA = "UPDATE vicidial_list set called_since_last_reset='$CSLR', called_count='$called_count',user='VDAD',last_local_call_time='$LLCT_DATE' where lead_id='$lead_id'";
										}
									$affected_rows = $dbhA->do($stmtA);

									$stmtA = "DELETE FROM vicidial_hopper where lead_id='$lead_id'";
									$affected_rows = $dbhA->do($stmtA);

									$CCID_on=0;   $CCID='';
									$local_DEF = 'Local/';
									$local_AMP = '@';
									$Local_out_prefix = '9';
									$Local_dial_timeout = '60';
									if ($DBIPdialtimeout[$user_CIPct] > 4) {$Local_dial_timeout = $DBIPdialtimeout[$user_CIPct];}
									$Local_dial_timeout = ($Local_dial_timeout * 1000);
									if (length($DBIPdialprefix[$user_CIPct]) > 0) {$Local_out_prefix = "$DBIPdialprefix[$user_CIPct]";}
									if (length($DBIPvdadexten[$user_CIPct]) > 0) {$VDAD_dial_exten = "$DBIPvdadexten[$user_CIPct]";}
									else {$VDAD_dial_exten = "$answer_transfer_agent";}

									if (length($DBIPcampaigncid[$user_CIPct]) > 6) {$CCID = "$DBIPcampaigncid[$user_CIPct]";   $CCID_on++;}
									if (length($campaign_cid_override) > 6) {$CCID = "$campaign_cid_override";   $CCID_on++;}
									if ($DBIPuse_custom_cid[$user_CIPct] =~ /Y/) 
										{
										$temp_CID = $security_phrase;
										$temp_CID =~ s/\D//gi;
										if (length($temp_CID) > 6) 
											{$CCID = "$temp_CID";   $CCID_on++;}
										}
									if ($DBIPuse_custom_cid[$user_CIPct] =~ /AREACODE/) 
										{
										$temp_CID='';
										$temp_vcca='';
										$temp_ac = substr("$phone_number", 0, 3);
										$stmtA = "SELECT outbound_cid FROM vicidial_campaign_cid_areacodes where campaign_id='$DBIPcampaign[$user_CIPct]' and areacode='$temp_ac' and active='Y' order by call_count_today limit 1;";
										$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
										$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
										$sthArows=$sthA->rows;
										if ($sthArows > 0)
											{
											@aryA = $sthA->fetchrow_array;
											$temp_vcca	=	$aryA[0];
											$sthA->finish();

											$stmtA="UPDATE vicidial_campaign_cid_areacodes set call_count_today=(call_count_today + 1) where campaign_id='$DBIPcampaign[$user_CIPct]' and areacode='$temp_ac' and outbound_cid='$temp_vcca';";
											$affected_rows = $dbhA->do($stmtA);
											}
										$temp_CID = $temp_vcca;
										$temp_CID =~ s/\D//gi;
										if (length($temp_CID) > 6) 
											{$CCID = "$temp_CID";   $CCID_on++;}
										}

									if ($DBIPdialprefix[$user_CIPct] =~ /x/i) {$Local_out_prefix = '';}

									if ($RECcount)
										{
										if ( (length($RECprefix)>0) && ($called_count < $RECcount) )
										   {$Local_out_prefix .= "$RECprefix";}
										}
									$PADlead_id = sprintf("%010s", $lead_id);	while (length($PADlead_id) > 10) {chop($PADlead_id);}

									if ($lists_update !~ /'$list_id'/) {$lists_update .= "'$list_id',"; $LUcount++;}

									$lead_id_call_list .= "$lead_id|";

									if (length($alt_dial)<1) {$alt_dial='MAIN';}

									### whether to omit phone_code or not
									if ($DBIPomitcode[$user_CIPct] > 0) 
										{$Ndialstring = "$Local_out_prefix$phone_number";}
									else
										{$Ndialstring = "$Local_out_prefix$phone_code$phone_number";}

									if (length($ext_context) < 1) {$ext_context='default';}
									### use manager middleware-app to connect the next call to the meetme room
									# VmddhhmmssLLLLLLLLLL
										$VqueryCID = "V$CIDdate$PADlead_id";
									if ($CCID_on) {$CIDstring = "\"$VqueryCID\" <$CCID>";}
									else {$CIDstring = "$VqueryCID";}
									### insert a NEW record to the vicidial_manager table to be processed
										$stmtA = "INSERT INTO vicidial_manager values('','','$SQLdate','NEW','N','$DBIPaddress[$user_CIPct]','','Originate','$VqueryCID','Exten: $VDAD_dial_exten','Context: $ext_context','Channel: $local_DEF$Ndialstring$local_AMP$ext_context','Priority: 1','Callerid: $CIDstring','Timeout: $Local_dial_timeout','','','','VDACnote: $DBIPcampaign[$user_CIPct]|$lead_id|$phone_code|$phone_number|OUT|$alt_dial|$DBIPqueue_priority[$user_CIPct]')";
										$affected_rows = $dbhA->do($stmtA);

										$event_string = "|     number call dialed|$DBIPcampaign[$user_CIPct]|$VqueryCID|$stmtA|$gmt_offset_now|$alt_dial|";
										 &event_logger;

									### insert a SENT record to the vicidial_auto_calls table 
										$stmtA = "INSERT INTO vicidial_auto_calls (server_ip,campaign_id,status,lead_id,callerid,phone_code,phone_number,call_time,call_type,alt_dial,queue_priority,last_update_time) values('$DBIPaddress[$user_CIPct]','$DBIPcampaign[$user_CIPct]','SENT','$lead_id','$VqueryCID','$phone_code','$phone_number','$SQLdate','OUT','$alt_dial','$DBIPqueue_priority[$user_CIPct]','$SQLdate')";
										$affected_rows = $dbhA->do($stmtA);

										### sleep for a five hundredths of a second to not flood the server with new calls
									#	usleep(1*50*1000);
										usleep(1*$per_call_delay*1000);
										$calls_placed++;
										}
									}
								$call_CMPIPct++;
								}
							}
						}
					}
				}
			if ($calls_placed > 0)
				{
				$stmtA="UPDATE vicidial_campaigns SET campaign_calldate='$now_date' where campaign_id='$DBIPcampaign[$user_CIPct]';";
				$affected_rows = $dbhA->do($stmtA);
				$calls_placed=0;
				}
			$user_CIPct++;
			}





	&get_time_now;

	###############################################################################
	###### third we will grab the callerids of the vicidial_auto_calls records and check for dead calls
	######    we also check to make sure that it isn't a call that has been transferred, 
	######    if it has been we need to leave the vicidial_list status alone
	###############################################################################

		@KLcallerid = @MT;
		@KLserver_ip = @MT;
		@KLchannel = @MT;
		@KLuniqueid = @MT;
		@KLstatus = @MT;
		@KLcalltime = @MT;
		$kill_vac=0;

		$stmtA = "SELECT callerid,server_ip,channel,uniqueid,status,call_time FROM vicidial_auto_calls where server_ip='$server_ip' order by call_time;";
		$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
		$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
		$sthArows=$sthA->rows;
		$rec_count=0;
		$rec_countCUSTDATA=0;
		$kill_vac=0;
		while ($sthArows > $rec_count)
			{
			@aryA = $sthA->fetchrow_array;
			$KLcallerid[$kill_vac]		= $aryA[0];
			$KLserver_ip[$kill_vac]		= $aryA[1];
			$KLchannel[$kill_vac]		= $aryA[2];
			$KLuniqueid[$kill_vac]		= $aryA[3];
			$KLstatus[$kill_vac]		= $aryA[4];
			$KLcalltime[$kill_vac]		= $aryA[5];
			$kill_vac++;
			$rec_count++;
			}
		$sthA->finish();

		$kill_vac=0;
		foreach(@KLcallerid)
			{
			if (length($KLserver_ip[$kill_vac]) > 7)
				{
				$end_epoch=0;   $CLuniqueid='';   $start_epoch=0;   $CLlast_update_time=0;
				$KLcalleridCHECK[$kill_vac]=$KLcallerid[$kill_vac];
				$KLcalleridCHECK[$kill_vac] =~ s/\W//gi;

				if ( (length($KLcalleridCHECK[$kill_vac]) > 17) && ($KLcalleridCHECK[$kill_vac] =~ /\d\d\d\d\d\d\d\d\d\d\d\d\d\d/) )
					{
					$stmtA = "SELECT end_epoch,uniqueid,start_epoch FROM call_log where caller_code='$KLcallerid[$kill_vac]' and server_ip='$KLserver_ip[$kill_vac]' order by end_epoch, start_time desc limit 1;";
					$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
					$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
					$sthArows=$sthA->rows;
					$rec_count=0;
					$rec_countCUSTDATA=0;
					while ($sthArows > $rec_count)
						{
						@aryA = $sthA->fetchrow_array;
						$end_epoch		= $aryA[0];
						$CLuniqueid		= $aryA[1];
						$start_epoch	= $aryA[2];
						$rec_count++;
						}
					$sthA->finish();
					}

				if ( (length($KLuniqueid[$kill_vac]) > 11) && (length($CLuniqueid) < 12) )
					{
					$stmtA = "SELECT end_epoch,uniqueid,start_epoch FROM call_log where uniqueid='$KLuniqueid[$kill_vac]' and server_ip='$KLserver_ip[$kill_vac]' order by end_epoch, start_time desc limit 1;";
					$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
					$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
					$sthArows=$sthA->rows;
					$rec_count=0;
					$rec_countCUSTDATA=0;
					while ($sthArows > $rec_count)
						{
						@aryA = $sthA->fetchrow_array;
						$end_epoch		= $aryA[0];
						$CLuniqueid		= $aryA[1];
						$start_epoch	= $aryA[2];
						$rec_count++;
						}
					$sthA->finish();
					}

				# Find out if (the call is parked
				$PARKchannel=0;
				if (length($KLcallerid[$kill_vac]) > 17)
					{
					$stmtA = "SELECT count(*) from parked_channels where channel_group='$KLcallerid[$kill_vac]';";
					$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
					$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
					$sthArowsPK=$sthA->rows;
					if ($sthArowsPK > 0)
						{
						@aryA = $sthA->fetchrow_array;
						$PARKchannel	= $aryA[0];
						}
					$sthA->finish();
					}

				$CLlead_id=''; $auto_call_id=''; $CLstatus=''; $CLcampaign_id=''; $CLphone_number=''; $CLphone_code='';

				$stmtA = "SELECT auto_call_id,lead_id,phone_number,status,campaign_id,phone_code,alt_dial,stage,call_type,UNIX_TIMESTAMP(last_update_time) FROM vicidial_auto_calls where callerid='$KLcallerid[$kill_vac]';";
				$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
				$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
				$sthArows=$sthA->rows;
				$rec_count=0;
				$rec_countCUSTDATA=0;
				while ($sthArows > $rec_count)
					{
					@aryA = $sthA->fetchrow_array;
					$auto_call_id	=		$aryA[0];
					$CLlead_id		=		$aryA[1];
					$CLphone_number	=		$aryA[2];
					$CLstatus		=		$aryA[3];
					$CLcampaign_id	=		$aryA[4];
					$CLphone_code	=		$aryA[5];
					$CLalt_dial		=		$aryA[6];
					$CLstage		=		$aryA[7];
					$CLcall_type	=		$aryA[8];
					$CLlast_update_time =	$aryA[9];
					$rec_count++;
					}
				$sthA->finish();
				$stmtA = "SELECT user FROM vicidial_live_agents where callerid='$KLcallerid[$kill_vac]'";
				$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
				$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
				$sthArows=$sthA->rows;
				if ($sthArows > 0)
					{
					@aryA = $sthA->fetchrow_array;
					$CLuser	= $aryA[0];
					}
				$sthA->finish();
				if ( (length($CLlead_id) < 1) || ($CLlead_id < 1) )
					{
					$CLlead_id = $KLcallerid[$kill_vac];
					$CLlead_id = substr($CLlead_id, 10, 10);
					$CLlead_id = ($CLlead_id + 0);
					}

				if ($CLcall_type =~ /IN/)
					{
					$stmtA = "SELECT drop_call_seconds FROM vicidial_inbound_groups where group_id='$CLcampaign_id';";
					$timeout_leeway = 30;
					}
				else
					{
					$stmtA = "SELECT dial_timeout,drop_call_seconds FROM vicidial_campaigns where campaign_id='$CLcampaign_id';";
					$timeout_leeway = 7;
					}
				$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
				$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
				$sthArows=$sthA->rows;
				if ($sthArows > 0)
					{
					@aryA = $sthA->fetchrow_array;
					$CLdial_timeout	=		$aryA[0];
					$CLdrop_call_seconds =	$aryA[1];
					}
				$sthA->finish();

				$dialtime_log = ($end_epoch - $start_epoch);
				$dialtime_catch = ($now_date_epoch - ($start_epoch + $timeout_leeway));
				$hanguptime_manual = ($end_epoch + 60);
				if ($dialtime_catch > 100000) {$dialtime_catch=0;}
				$call_timeout = ($CLdial_timeout + $CLdrop_call_seconds);
				if ($CLstage =~ /SURVEY|REMIND/) {$call_timeout = ($call_timeout + 120);}
				if ( ($CLstage =~ /SENT/) && ($call_timeout > 110) ) {$call_timeout = 110;}

		#		$event_string = "|     vac test: |$auto_call_id|$CLstatus|$KLcalltime[$kill_vac]|$CLlead_id|$KLcallerid[$kill_vac]|$end_epoch|$KLchannel[$kill_vac]|$CLcall_type|$CLdial_timeout|$CLdrop_call_seconds|$call_timeout|$dialtime_log|$dialtime_catch|$PARKchannel|";
		#		&event_logger;

				if ( ( ($dialtime_log >= $call_timeout) || ($dialtime_catch >= $call_timeout) || ($CLstatus =~ /BUSY|DISCONNECT|XFER|CLOSER/) ) && ($PARKchannel < 1) )
					{
					if ( ($CLcall_type !~ /IN/) && ($CLstatus !~ /IVR/) )
						{
						if ($CLstatus !~ /XFER|CLOSER/)
							{
							$stmtA = "DELETE from vicidial_auto_calls where auto_call_id='$auto_call_id'";
							$affected_rows = $dbhA->do($stmtA);

							$stmtA = "UPDATE vicidial_live_agents set ring_callerid='' where ring_callerid='$KLcallerid[$kill_vac]';";
							$affected_rowsX = $dbhA->do($stmtA);

							$event_string = "|     dead call vac deleted|$auto_call_id|$CLstatus|$CLlead_id|$KLcallerid[$kill_vac]|$end_epoch|$affected_rows|$KLchannel[$kill_vac]|$CLcall_type|$CLdial_timeout|$CLdrop_call_seconds|$call_timeout|$dialtime_log|$dialtime_catch|$affected_rowsX|";
							 &event_logger;

							$CLstage =~ s/LIVE|-//gi;
							if ($CLstage < 0.25) {$CLstage=0;}

							if ($CLstatus =~ /BUSY/) {$CLnew_status = 'B';}
							else
								{
								if ($CLstatus =~ /DISCONNECT/) {$CLnew_status = 'DC';}
								else {$CLnew_status = 'NA';}
								}
							if ($CLstatus =~ /LIVE/) {$CLnew_status = 'DROP';}
							else 
								{
								$insertVLuser = 'VDAD';
								$insertVLcount=0;
								if ($KLcallerid[$kill_vac] =~ /^M\d\d\d\d\d\d\d\d\d\d/) 
									{
									$beginUNIQUEID = $CLuniqueid;
									$beginUNIQUEID =~ s/\..*//gi;
									$insertVLuser = $CLuser;
									$stmtA="SELECT count(*) from vicidial_log where lead_id='$CLlead_id' and user='$CLuser' and phone_number='$CLphone_number' and uniqueid LIKE \"$beginUNIQUEID%\";";
										if ($DB) {$event_string = "|$stmtA|";   &event_logger;}
									$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
									$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
									$sthArowsVL=$sthA->rows;
									if ($sthArowsVL > 0)
										{
										@aryA = $sthA->fetchrow_array;
										$insertVLcount = 	$aryA[0];
										}
									$sthA->finish();
									}

								##############################################################
								### BEGIN - CPD Look for result for NA/B/DC calls
								##############################################################
								$stmtA = "SELECT result FROM vicidial_cpd_log where callerid='$KLcallerid[$kill_vac]' order by cpd_id desc limit 1;";
									if ($DB) {$event_string = "|$stmtA|";   &event_logger;}
								$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
								$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
								$sthArows=$sthA->rows;
								if ($sthArows > 0)
									{
									@aryA = $sthA->fetchrow_array;
									$cpd_result		= $aryA[0];
									$sthA->finish();
									$CPDfound=0;
									if ($cpd_result =~ /Busy/i)					{$CLnew_status='CPDB';   $CPDfound++;}
									if ($cpd_result =~ /Unknown/i)				{$CLnew_status='CPDUK';   $CPDfound++;}
									if ($cpd_result =~ /All-Trunks-Busy/i)		{$CLnew_status='CPDATB';   $CPDfound++;}
									if ($cpd_result =~ /No-Answer/i)			{$CLnew_status='CPDNA';   $CPDfound++;}
									if ($cpd_result =~ /Reject/i)				{$CLnew_status='CPDREJ';   $CPDfound++;}
									if ($cpd_result =~ /Invalid-Number/i)		{$CLnew_status='CPDINV';   $CPDfound++;}
									if ($cpd_result =~ /Service-Unavailable/i)	{$CLnew_status='CPDSUA';   $CPDfound++;}
									if ($cpd_result =~ /Sit-Intercept/i)		{$CLnew_status='CPDSI';   $CPDfound++;}
									if ($cpd_result =~ /Sit-No-Circuit/i)		{$CLnew_status='CPDSNC';   $CPDfound++;}
									if ($cpd_result =~ /Sit-Reorder/i)			{$CLnew_status='CPDSR';   $CPDfound++;}
									if ($cpd_result =~ /Sit-Unknown/i)			{$CLnew_status='CPDSUK';   $CPDfound++;}
									if ($cpd_result =~ /Sit-Vacant/i)			{$CLnew_status='CPDSV';   $CPDfound++;}
									if ($cpd_result =~ /\?\?\?/i)				{$CLnew_status='CPDERR';   $CPDfound++;}
									if ($cpd_result =~ /Fax|Modem/i)			{$CLnew_status='AFAX';   $CPDfound++;}
									if ($cpd_result =~ /Answering-Machine/i)	{$CLnew_status='AA';   $CPDfound++;}
									}

								##############################################################
								### END - CPD Look for result for NA/B/DC calls
								##############################################################

								$end_epoch = $now_date_epoch;
								if ($insertVLcount < 1)
									{
									$xCLlist_id=0;
									$stmtA="SELECT list_id from vicidial_list where lead_id='$CLlead_id' limit 1;";
										if ($DB) {$event_string = "|$stmtA|";   &event_logger;}
									$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
									$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
									$sthArowsVLL=$sthA->rows;
									if ($sthArowsVLL > 0)
										{
										@aryA = $sthA->fetchrow_array;
										$xCLlist_id = 	$aryA[0];
										}
									$sthA->finish();

									$stmtA = "INSERT INTO vicidial_log (uniqueid,lead_id,campaign_id,call_date,start_epoch,status,phone_code,phone_number,user,processed,length_in_sec,end_epoch,alt_dial,list_id) values('$CLuniqueid','$CLlead_id','$CLcampaign_id','$SQLdate','$now_date_epoch','$CLnew_status','$CLphone_code','$CLphone_number','$insertVLuser','N','$CLstage','$end_epoch','$CLalt_dial','$xCLlist_id')";
										if($M){print STDERR "\n|$stmtA|\n";}
									$affected_rows = $dbhA->do($stmtA);

									$stmtB = "INSERT INTO vicidial_log_extended set uniqueid='$CLuniqueid',server_ip='$server_ip',call_date='$SQLdate',lead_id = '$CLlead_id',caller_code='$KLcallerid[$kill_vac]',custom_call_id='';";
									$affected_rowsB = $dbhA->do($stmtB);

									$event_string = "|     dead NA call added to log $CLuniqueid|$CLlead_id|$CLphone_number|$CLstatus|$CLnew_status|$affected_rows|$affected_rowsB|$insertVLcount";
									 &event_logger;
									}
								else
									{
									$stmtA = "UPDATE vicidial_log SET status='$CLnew_status',length_in_sec='$CLstage',end_epoch='$end_epoch',alt_dial='$CLalt_dial' where lead_id='$CLlead_id' and user='$CLuser' and phone_number='$CLphone_number' and uniqueid LIKE \"$beginUNIQUEID%\";";
										if($M){print STDERR "\n|$stmtA|\n";}
									$affected_rows = $dbhA->do($stmtA);

									$event_string = "|     dead NA call updated in log $CLuniqueid|$CLlead_id|$CLphone_number|$CLstatus|$CLnew_status|$affected_rows|$insertVLcount|$beginUNIQUEID";
									 &event_logger;
									}
								}

							if ($CLlead_id > 0)
								{
								$stmtA = "UPDATE vicidial_list set status='$CLnew_status' where lead_id='$CLlead_id'";
								$affected_rows = $dbhA->do($stmtA);

								$event_string = "|     dead call vac lead marked $CLnew_status|$CLlead_id|$CLphone_number|$CLstatus|";
								 &event_logger;
								}

					#		if ($KLcallerid[$kill_vac] !~ /^M\d\d\d\d\d\d\d\d\d\d/)
					#			{
					#			$stmtA = "UPDATE vicidial_live_agents set status='PAUSED',random_id='10' where callerid='$KLcallerid[$kill_vac]';";
					#			$Vaffected_rows = $dbhA->do($stmtA);
					#			if ($Vaffected_rows > 0)
					#				{
					#				$stmtA = "SELECT agent_log_id,user from vicidial_live_agents where callerid='$KLcallerid[$kill_vac]';";
					#				$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
					#				$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
					#				$sthArowsML=$sthA->rows;
					#				$lagged_ids=0;
					#				while ($sthArowsML > $lagged_ids)
					#					{
					#					@aryA = $sthA->fetchrow_array;
					#					$MLAGagent_log_id[$lagged_ids] =	$aryA[0];
					#					$MLAGuser[$lagged_ids] =			$aryA[1];
					#					$lagged_ids++;
					#					}
					#				$sthA->finish();
					#				$lagged_ids=0;
					#				while ($sthArowsML > $lagged_ids)
					#					{
					#					$stmtA = "UPDATE vicidial_agent_log set sub_status='LAGGED' where agent_log_id='$MLAGagent_log_id[$lagged_ids]';";
					#					$VLaffected_rows = $dbhA->do($stmtA);
					#					if ($enable_queuemetrics_logging > 0)
					#						{
					#						$secX = time();
					#						$dbhB = DBI->connect("DBI:mysql:$queuemetrics_dbname:$queuemetrics_server_ip:3306", "$queuemetrics_login", "$queuemetrics_pass")
					#						 or die "Couldn't connect to database: " . DBI->errstr;
					#						if ($DBX) {print "CONNECTED TO DATABASE:  $queuemetrics_server_ip|$queuemetrics_dbname\n";}
					#						$stmtB = "INSERT INTO queue_log SET partition='P01',time_id='$secX',call_id='NONE',queue='NONE',agent='Agent/$MLAGuser[$lagged_ids]',verb='PAUSEREASON',serverid='$queuemetrics_log_id',data1='LAGGED';";
					#						$Baffected_rows = $dbhB->do($stmtB);
					#						$dbhB->disconnect();
					#						}
					#					$lagged_ids++;
					#					}
					#				$event_string = "|     dead call vla agent PAUSED $Vaffected_rows|$VLaffected_rows|$CLlead_id|$CLphone_number|$CLstatus|";
					#				 &event_logger;
					#				}
					#			}

							if ( ($enable_queuemetrics_logging > 0) && ($CLstatus =~ /LIVE/) )
								{
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
								$stmtA = "SELECT queue_position,call_date FROM vicidial_closer_log where lead_id='$CLlead_id' and campaign_id='$CLcampaign_id' and call_date > \"$RSQLdate\" order by closecallid desc limit 1;";
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

								### find current number of calls in this queue to find position when channel hung up
								$current_position=1;
								$stmtA = "SELECT count(*) FROM vicidial_auto_calls where status = 'LIVE' and campaign_id='$CLcampaign_id' and call_time < '$VCLcall_date';";
								$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
								$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
								$sthArows=$sthA->rows;
								if ($sthArows > 0)
									{
									@aryA = $sthA->fetchrow_array;
									$current_position =	($aryA[0] + 1);
									}
								$sthA->finish();

								$stmtB = "INSERT INTO queue_log SET partition='P01',time_id='$secX',call_id='$KLcallerid[$kill_vac]',queue='$CLcampaign_id',agent='NONE',verb='ABANDON',data1='$current_position',data2='$queue_position',data3='$CLstage',serverid='$queuemetrics_log_id';";
								$Baffected_rows = $dbhB->do($stmtB);

								$dbhB->disconnect();
								}

							##### BEGIN AUTO ALT PHONE DIAL SECTION #####
							### check to see if campaign has alt_dial enabled
							$VD_auto_alt_dial = 'NONE';
							$VD_auto_alt_dial_statuses='';
							$stmtA="SELECT auto_alt_dial,auto_alt_dial_statuses,use_internal_dnc,use_campaign_dnc FROM vicidial_campaigns where campaign_id='$CLcampaign_id';";
							$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
							$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
							$sthArows=$sthA->rows;
							if ($sthArows > 0)
								{
								@aryA = $sthA->fetchrow_array;
								$VD_auto_alt_dial	=			$aryA[0];
								$VD_auto_alt_dial_statuses	=	$aryA[1];
								$VD_use_internal_dnc =			$aryA[2];
								$VD_use_campaign_dnc =			$aryA[3];
								}
							$sthA->finish();
								$event_string = "|$stmtA|$VD_auto_alt_dial|$VD_auto_alt_dial_statuses|$CLnew_status|$CLlead_id|$CLalt_dial";   &event_logger;
							if ( ($VD_auto_alt_dial_statuses =~ / $CLnew_status /) && ($CLlead_id > 0) )
								{
								if ( ($VD_auto_alt_dial =~ /ALT_ONLY|ALT_AND_ADDR3|ALT_AND_EXTENDED/) && ($CLalt_dial =~ /NONE|MAIN/) )
									{
									$alt_dial_skip=0;
									$VD_alt_phone='';
									$stmtA="SELECT alt_phone,gmt_offset_now,state,list_id FROM vicidial_list where lead_id='$CLlead_id';";
									$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
									$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
									$sthArows=$sthA->rows;
									if ($sthArows > 0)
										{
										@aryA = $sthA->fetchrow_array;
										$VD_alt_phone =			$aryA[0];
										$VD_alt_phone =~ s/\D//gi;
										$VD_gmt_offset_now =	$aryA[1];
										$VD_state =				$aryA[2];
										$VD_list_id =			$aryA[3];
										}
									$sthA->finish();
										$event_string = "|$stmtA|$VD_alt_phone|";   &event_logger;
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
											$event_string = "|$stmtA|$VD_alt_dnc_count|";   &event_logger;
										if ( ($VD_use_campaign_dnc =~ /Y/) || ($VD_use_campaign_dnc =~ /AREACODE/) )
											{
											if ($VD_use_campaign_dnc =~ /AREACODE/)
												{
												$alt_areacode = substr($VD_alt_phone, 0, 3);
												$alt_areacode .= "XXXXXXX";
												$stmtA="SELECT count(*) FROM vicidial_campaign_dnc where phone_number IN('$VD_alt_phone','$alt_areacode') and campaign_id='$CLcampaign_id';";
												}
											else
												{$stmtA="SELECT count(*) FROM vicidial_campaign_dnc where phone_number='$VD_alt_phone' and campaign_id='$CLcampaign_id';";}
											$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
											$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
											$sthArows=$sthA->rows;
											if ($sthArows > 0)
												{
												@aryA = $sthA->fetchrow_array;
												$VD_alt_dnc_count =	($VD_alt_dnc_count + $aryA[0]);
												}
											$sthA->finish();
												$event_string = "|$stmtA|$VD_alt_dnc_count|";   &event_logger;
											}
										if ($VD_alt_dnc_count < 1)
											{
											$stmtA = "INSERT INTO vicidial_hopper SET lead_id='$CLlead_id',campaign_id='$CLcampaign_id',status='READY',list_id='$VD_list_id',gmt_offset_now='$VD_gmt_offset_now',state='$VD_state',alt_dial='ALT',user='',priority='25',source='A';";
											$affected_rows = $dbhA->do($stmtA);
											$event_string = "--    VDH record inserted: |$affected_rows|   |$stmtA|";   &event_logger;
											}
										else
											{$alt_dial_skip=1;}
										}
									else
										{$alt_dial_skip=1;}
									if ($alt_dial_skip > 0)
										{$CLalt_dial='ALT';}
									}
								if ( ( ($VD_auto_alt_dial =~ /ADDR3_ONLY/) && ($CLalt_dial =~ /NONE|MAIN/) ) || ( ($VD_auto_alt_dial =~ /ALT_AND_ADDR3/) && ($CLalt_dial =~ /ALT/) ) )
									{
									$addr3_dial_skip=0;
									$VD_address3='';
									$stmtA="SELECT address3,gmt_offset_now,state,list_id FROM vicidial_list where lead_id='$CLlead_id';";
									$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
									$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
									$sthArows=$sthA->rows;
									if ($sthArows > 0)
										{
										@aryA = $sthA->fetchrow_array;
										$VD_address3 =			$aryA[0];
										$VD_address3 =~ s/\D//gi;
										$VD_gmt_offset_now =	$aryA[1];
										$VD_state =				$aryA[2];
										$VD_list_id =			$aryA[3];
										}
									$sthA->finish();
										$event_string = "|$stmtA|$VD_address3|";   &event_logger;
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
											$event_string = "|$stmtA|$VD_alt_dnc_count|";   &event_logger;
										if ( ($VD_use_campaign_dnc =~ /Y/) || ($VD_use_campaign_dnc =~ /AREACODE/) )
											{
											if ($VD_use_campaign_dnc =~ /AREACODE/)
												{
												$addr3_areacode = substr($VD_address3, 0, 3);
												$addr3_areacode .= "XXXXXXX";
												$stmtA="SELECT count(*) FROM vicidial_campaign_dnc where phone_number IN('$VD_address3','$addr3_areacode') and campaign_id='$CLcampaign_id';";
												}
											else
												{$stmtA="SELECT count(*) FROM vicidial_campaign_dnc where phone_number='$VD_address3' and campaign_id='$CLcampaign_id';";}
											$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
											$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
											$sthArows=$sthA->rows;
											if ($sthArows > 0)
												{
												@aryA = $sthA->fetchrow_array;
												$VD_alt_dnc_count =	($VD_alt_dnc_count + $aryA[0]);
												}
											$sthA->finish();
												$event_string = "|$stmtA|$VD_alt_dnc_count|";   &event_logger;
											}
										if ($VD_alt_dnc_count < 1)
											{
											$stmtA = "INSERT INTO vicidial_hopper SET lead_id='$CLlead_id',campaign_id='$CLcampaign_id',status='READY',list_id='$VD_list_id',gmt_offset_now='$VD_gmt_offset_now',state='$VD_state',alt_dial='ADDR3',user='',priority='20',source='A';";
											$affected_rows = $dbhA->do($stmtA);
											$event_string = "--    VDH record inserted: |$affected_rows|   |$stmtA|";   &event_logger;
											}
										else
											{$addr3_dial_skip=1;}
										}
									else
										{$addr3_dial_skip=1;}
									if ($addr3_dial_skip > 0)
										{$CLalt_dial='ADDR3';}
									}
								if ( ( ($VD_auto_alt_dial =~ /EXTENDED_ONLY/) && ($CLalt_dial =~ /NONE|MAIN/) ) || ( ($VD_auto_alt_dial =~ /ALT_AND_EXTENDED/) && ($CLalt_dial =~ /ALT/) ) || ( ($VD_auto_alt_dial =~ /ADDR3_AND_EXTENDED|ALT_AND_ADDR3_AND_EXTENDED/) && ($CLalt_dial =~ /ADDR3/) ) || ( ($VD_auto_alt_dial =~ /EXTENDED/) && ($CLalt_dial =~ /^X/) && ($CLalt_dial !~ /XLAST/) ) )
									{
									if ($CLalt_dial =~ /ADDR3/) {$Xlast=0;}
									else
										{$Xlast = $CLalt_dial;}
									$Xlast =~ s/\D//gi;
									if (length($Xlast)<1)
										{$Xlast=0;}
									$VD_altdialx='';
									$stmtA="SELECT gmt_offset_now,state,list_id FROM vicidial_list where lead_id='$CLlead_id';";
									$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
									$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
									$sthArows=$sthA->rows;
									if ($sthArows > 0)
										{
										@aryA = $sthA->fetchrow_array;
										$VD_gmt_offset_now =	$aryA[0];
										$VD_state =				$aryA[1];
										$VD_list_id =			$aryA[2];
										}
									$sthA->finish();
									$alt_dial_phones_count=0;
									$stmtA="SELECT count(*) FROM vicidial_list_alt_phones where lead_id='$CLlead_id';";
									$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
									$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
									$sthArows=$sthA->rows;
									if ($sthArows > 0)
										{
										@aryA = $sthA->fetchrow_array;
										$alt_dial_phones_count = $aryA[0];
										}
									$sthA->finish();
										$event_string = "|$stmtA|$alt_dial_phones_count";   &event_logger;

									while ( ($alt_dial_phones_count > 0) && ($alt_dial_phones_count > $Xlast) )
										{
										$Xlast++;
										$stmtA="SELECT alt_phone_id,phone_number,active FROM vicidial_list_alt_phones where lead_id='$CLlead_id' and alt_phone_count='$Xlast';";
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
											$event_string = "|$stmtA|$VD_altdial_phone|$Xlast|";   &event_logger;
										$sthA->finish();

										$DNCC=0;
										$DNCL=0;
										if ($VD_altdial_active =~ /Y/)
											{
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
												$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
												$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
												$sthArows=$sthA->rows;
												if ($sthArows > 0)
													{
													@aryA = $sthA->fetchrow_array;
													$VD_alt_dnc_count =	$aryA[0];
													$DNCL =				$aryA[0];
													}
												$sthA->finish();
													$event_string = "|$stmtA|$VD_alt_dnc_count|";   &event_logger;
												}
											else {$VD_alt_dnc_count=0;}
											if ( ($VD_use_campaign_dnc =~ /Y/) || ($VD_use_campaign_dnc =~ /AREACODE/) )
												{
												if ($VD_use_campaign_dnc =~ /AREACODE/)
													{
													$ad_areacode = substr($VD_altdial_phone, 0, 3);
													$ad_areacode .= "XXXXXXX";
													$stmtA="SELECT count(*) FROM vicidial_campaign_dnc where phone_number IN('$VD_altdial_phone','$ad_areacode') and campaign_id='$CLcampaign_id';";
													}
												else
													{$stmtA="SELECT count(*) FROM vicidial_campaign_dnc where phone_number='$VD_altdial_phone' and campaign_id='$CLcampaign_id';";}
												$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
												$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
												$sthArows=$sthA->rows;
												if ($sthArows > 0)
													{
													@aryA = $sthA->fetchrow_array;
													$VD_alt_dnc_count =	($VD_alt_dnc_count + $aryA[0]);
													$DNCC =	$aryA[0];
													}
												$sthA->finish();
													$event_string = "|$stmtA|$VD_alt_dnc_count|";   &event_logger;
												}
											if ($VD_alt_dnc_count < 1)
												{
												if ($alt_dial_phones_count == $Xlast) 
													{$Xlast = 'LAST';}
												$stmtA = "INSERT INTO vicidial_hopper SET lead_id='$CLlead_id',campaign_id='$CLcampaign_id',status='READY',list_id='$VD_list_id',gmt_offset_now='$VD_gmt_offset_now',state='$VD_state',alt_dial='X$Xlast',user='',priority='15',source='A';";
												$affected_rows = $dbhA->do($stmtA);
												$event_string = "--    VDH record inserted: |$affected_rows|   |$stmtA|X$Xlast|$VD_altdial_id|";   &event_logger;
												$Xlast=9999999999;
												}
											else
												{
												if ( ( ($VD_auto_alt_dial_statuses =~ / DNCC /) && ($DNCC > 0) ) || ( ($VD_auto_alt_dial_statuses =~ / DNCL /) && ($DNCL > 0) ) )
													{
													if ($alt_dial_phones_count == $Xlast) 
														{$Xlast = 'LAST';}
													$stmtA = "INSERT INTO vicidial_hopper SET lead_id='$CLlead_id',campaign_id='$CLcampaign_id',status='DNC',list_id='$VD_list_id',gmt_offset_now='$VD_gmt_offset_now',state='$VD_state',alt_dial='X$Xlast',user='',priority='15',source='A';";
													$affected_rows = $dbhA->do($stmtA);
													$event_string = "--    VDH record DNC inserted: |$affected_rows|   |$stmtA|X$Xlast|$VD_altdial_id|";   &event_logger;
													$Xlast=9999999999;
													}
												else
													{
													$event_string = "--    VDH record DNC not-inserted: |$affected_rows|   |$stmtA|X$Xlast|$VD_altdial_id|";   &event_logger;
													}
												}
											}
										}
									}
								}
							##### END AUTO ALT PHONE DIAL SECTION #####




							}
						else
							{
							if ( ($KLcallerid[$kill_vac] =~ /^M\d\d\d\d\d\d\d\d\d\d/) && ($CLlast_update_time < $TDtarget) )
								{
								$stmtA = "DELETE from vicidial_auto_calls where auto_call_id='$auto_call_id'";
								$affected_rows = $dbhA->do($stmtA);

								$stmtA = "UPDATE vicidial_live_agents set ring_callerid='' where ring_callerid='$KLcallerid[$kill_vac]';";
								$affected_rowsX = $dbhA->do($stmtA);

								$event_string = "|   M dead call vac deleted|$auto_call_id|$CLlead_id|$KLcallerid[$kill_vac]|$end_epoch|$affected_rows|$KLchannel[$kill_vac]|$CLcall_type|$CLlast_update_time < $XDtarget|$affected_rowsX|";
								 &event_logger;

								}
							else
								{
								$event_string = "|     dead call vac XFERd do nothing|$CLlead_id|$CLphone_number|$CLstatus|";
								 &event_logger;
								}
							}
						}
					else
						{
						$event_string = "|     dead call vac INBOUND do nothing|$CLlead_id|$CLphone_number|$CLstatus|";
						 &event_logger;
						}
					}
				}
			$kill_vac++;
			}



		### pause agents that have disconnected or closed their apps over 30 seconds ago
		$toPAUSEcount=0;
		$stmtA = "SELECT count(*) FROM vicidial_live_agents where server_ip='$server_ip' and last_update_time < '$PDtsSQLdate' and status NOT IN('PAUSED');";
		$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
		$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
		$sthArowsC=$sthA->rows;
		if ($sthArowsC > 0)
			{
			@aryA = $sthA->fetchrow_array;
			$toPAUSEcount =		$aryA[0];
			}
		$sthA->finish();
		$affected_rows=0;

		if ($toPAUSEcount > 0)
			{
			$stmtA = "SELECT agent_log_id,user,server_ip,campaign_id from vicidial_live_agents where server_ip='$server_ip' and last_update_time < '$PDtsSQLdate' and status NOT IN('PAUSED');";
			$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
			$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
			$sthArowsL=$sthA->rows;
			$lagged_ids=0;
			while ($sthArowsL > $lagged_ids)
				{
				@aryA = $sthA->fetchrow_array;
				$LAGagent_log_id[$lagged_ids] =	$aryA[0];
				$LAGuser[$lagged_ids] =			$aryA[1];
				$LAGserver_ip[$lagged_ids] =	$aryA[2];
				$LAGcampaign_id[$lagged_ids] =	$aryA[3];
				$lagged_ids++;
				}
			$sthA->finish();
			$lagged_ids=0;
			while ($sthArowsL > $lagged_ids)
				{
				$secX = time();
				$stmtA = "UPDATE vicidial_agent_log set sub_status='LAGGED' where agent_log_id='$LAGagent_log_id[$lagged_ids]';";
				$VLaffected_rows = $dbhA->do($stmtA);

				$stmtA = "SELECT user_group from vicidial_users where user='$server_ip' limit 1;";
				$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
				$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
				$sthArowsLU=$sthA->rows;
				if ($sthArowsLU > 0)
					{
					@aryA = $sthA->fetchrow_array;
					$LAGuser_group[$lagged_ids] =	$aryA[0];
					}
				$sthA->finish();

				$stmtA = "INSERT INTO vicidial_agent_log set event_time='$now_date',server_ip='$LAGserver_ip[$lagged_ids]',campaign_id='$LAGcampaign_id[$lagged_ids]',user_group='$LAGuser_group[$lagged_ids]',user='$LAGuser[$lagged_ids]',pause_epoch='$secX',pause_sec='0',wait_epoch='$secX';";
				$VLaffected_rows = $dbhA->do($stmtA);

				if ($enable_queuemetrics_logging > 0)
					{
					$dbhB = DBI->connect("DBI:mysql:$queuemetrics_dbname:$queuemetrics_server_ip:3306", "$queuemetrics_login", "$queuemetrics_pass")
					 or die "Couldn't connect to database: " . DBI->errstr;

					if ($DBX) {print "CONNECTED TO DATABASE:  $queuemetrics_server_ip|$queuemetrics_dbname\n";}

					$stmtB = "INSERT INTO queue_log SET partition='P01',time_id='$secX',call_id='NONE',queue='NONE',agent='Agent/$LAGuser[$lagged_ids]',verb='PAUSEREASON',serverid='$queuemetrics_log_id',data1='LAGGED';";
					$Baffected_rows = $dbhB->do($stmtB);

					$dbhB->disconnect();
					}

				$lagged_ids++;
				}

			$stmtA = "UPDATE vicidial_live_agents set status='PAUSED',random_id='10' where server_ip='$server_ip' and last_update_time < '$PDtsSQLdate' and status NOT IN('PAUSED');";
			$affected_rows = $dbhA->do($stmtA);

			$event_string = "|     lagged call vla agent PAUSED $affected_rows|$VLaffected_rows|$PDtsSQLdate|$BDtsSQLdate|$tsSQLdate|";
			 &event_logger;

			if ($affected_rows > 0)
				{
				@VALOuser=@MT; @VALOcampaign=@MT; @VALOtimelog=@MT; @VALOextension=@MT;
				$logcount=0;
				$stmtA = "SELECT user,campaign_id,last_update_time,extension FROM vicidial_live_agents where server_ip='$server_ip' and status='PAUSED' and random_id='10' order by last_update_time desc limit $affected_rows";
				$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
				$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
				$sthArows=$sthA->rows;
				$rec_count=0;
				$rec_countCUSTDATA=0;
				while ($sthArows > $rec_count)
					{
					@aryA = $sthA->fetchrow_array;
					$VALOuser[$logcount] =		$aryA[0];
					$VALOcampaign[$logcount] =	$aryA[1];
					$VALOtimelog[$logcount]	=	$aryA[2];
					$VALOextension[$logcount] = $aryA[3];
					$logcount++;
					$rec_count++;
					}
				$sthA->finish();
				$logrun=0;
				foreach(@VALOuser)
					{
					$VALOuser_group='';
					$stmtA = "SELECT user_group FROM vicidial_users where user='$VALOuser[$logrun]';";
					$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
					$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
					$sthArows=$sthA->rows;
					$UGrec_count=0;
					while ($sthArows > $UGrec_count)
						{
						@aryA = $sthA->fetchrow_array;
						$VALOuser_group =		$aryA[0];
						$UGrec_count++;
						}
					$sthA->finish();
					$stmtA = "INSERT INTO vicidial_user_log (user,event,campaign_id,event_date,event_epoch,user_group) values('$VALOuser[$logrun]','LOGOUT','$VALOcampaign[$logrun]','$SQLdate','$now_date_epoch','$VALOuser_group');";
					$affected_rows = $dbhA->do($stmtA);

					if ($enable_queuemetrics_logging > 0)
						{
						$QM_LOGOFF = 'AGENTLOGOFF';
						if ($queuemetrics_loginout =~ /CALLBACK/)
							{$QM_LOGOFF = 'AGENTCALLBACKLOGOFF';}

						$secX = time();
						$dbhB = DBI->connect("DBI:mysql:$queuemetrics_dbname:$queuemetrics_server_ip:3306", "$queuemetrics_login", "$queuemetrics_pass")
						 or die "Couldn't connect to database: " . DBI->errstr;

						if ($DBX) {print "CONNECTED TO DATABASE:  $queuemetrics_server_ip|$queuemetrics_dbname\n";}

						$agents='@agents';
						$time_logged_in='';
						$data4='';
						$RAWtime_logged_in=$TDtarget;
						if ($queuemetrics_loginout !~ /NONE/)
							{
							$stmtB = "SELECT time_id,data1,data4 FROM queue_log where agent='Agent/$VALOuser[$logrun]' and verb IN('AGENTLOGIN','AGENTCALLBACKLOGIN') and time_id > $check_time order by time_id desc limit 1;";
							$sthB = $dbhB->prepare($stmtB) or die "preparing: ",$dbhB->errstr;
							$sthB->execute or die "executing: $stmtA ", $dbhB->errstr;
							$sthBrows=$sthB->rows;
							if ($sthBrows > 0)
								{
								@aryB = $sthB->fetchrow_array;
								$time_logged_in =		$aryB[0];
								$RAWtime_logged_in =	$aryB[0];
								$phone_logged_in =		$aryB[1];
								$data4 =				$aryB[2];
								}
							$sthB->finish();

							$time_logged_in = ($secX - $logintime);
							if ($time_logged_in > 1000000) {$time_logged_in=1;}
							$LOGOFFtime = ($secX + 1);

							$stmtB = "INSERT INTO queue_log SET partition='P01',time_id='$LOGOFFtime',call_id='NONE',queue='NONE',agent='Agent/$VALOuser[$logrun]',verb='$QM_LOGOFF',serverid='$queuemetrics_log_id',data1='$phone_logged_in',data2='$time_logged_in',data4='$data4';";
							$Baffected_rows = $dbhB->do($stmtB);
							}

						if ($queuemetrics_addmember_enabled > 0)
							{
							if ( (length($time_logged_in) < 1) || ($queuemetrics_loginout =~ /NONE/) )
								{
								$stmtB = "SELECT time_id,data3,data4 FROM queue_log where agent='Agent/$VALOuser[$logrun]' and verb='PAUSEREASON' and data1='LOGIN' order by time_id desc limit 1;";
								$sthB = $dbhB->prepare($stmtB) or die "preparing: ",$dbhB->errstr;
								$sthB->execute or die "executing: $stmtA ", $dbhB->errstr;
								$sthBrows=$sthB->rows;
								if ($sthBrows > 0)
									{
									@aryB = $sthB->fetchrow_array;
									$time_logged_in =		$aryB[0];
									$RAWtime_logged_in =	$aryB[0];
									$phone_logged_in =		$aryB[1];
									$data4 =				$aryB[2];
									}
								$sthB->finish();

								$time_logged_in = ($secX - $logintime);
								if ($time_logged_in > 1000000) {$time_logged_in=1;}
								$LOGOFFtime = ($secX + 1);
								}
							if ($queuemetrics_loginout =~ /NONE/)
								{
								$stmtB = "INSERT INTO queue_log SET partition='P01',time_id='$LOGOFFtime',call_id='NONE',queue='NONE',agent='Agent/$VALOuser[$logrun]',verb='PAUSEREASON',serverid='$queuemetrics_log_id',data1='LOGOFF';";
								$Baffected_rows = $dbhB->do($stmtB);
								}
							$stmtB = "SELECT distinct queue FROM queue_log where time_id >= $RAWtime_logged_in and agent='Agent/$VALOuser[$logrun]' and verb IN('ADDMEMBER','ADDMEMBER2') and queue != '$VALOcampaign[$logrun]' order by time_id desc;";
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

							$AM_queue[$rec_count] =	$VALOcampaign[$logrun];
							$rec_count++;
							$sthBrows++;

							$rec_count=0;
							while ($sthBrows > $rec_count)
								{
								$stmtB = "INSERT INTO queue_log SET partition='P01',time_id='$LOGOFFtime',call_id='NONE',queue='$AM_queue[$rec_count]',agent='Agent/$VALOuser[$logrun]',verb='REMOVEMEMBER',data1='$phone_logged_in',serverid='$queuemetrics_log_id',data4='$data4';";
								$Baffected_rows = $dbhB->do($stmtB);
								$rec_count++;
								}
							}

						$dbhB->disconnect();
						}

					$event_string = "|          lagged agent LOGOUT entry inserted $VALOuser[$logrun]|$VALOcampaign[$logrun]|$VALOextension[$logcount]|";
					 &event_logger;

					$logrun++;
					}
				}
			}


		### display and delete call records that are SENT for over 2 minutes
		$stmtA = "SELECT status,lead_id,last_update_time FROM vicidial_auto_calls where server_ip='$server_ip' and call_time < '$XDSQLdate' and status NOT IN('XFER','CLOSER','LIVE','IVR');";
		$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
		$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
		$sthArows=$sthA->rows;
		$dead_delete_string='';
		$dead_delete_ct=0;
		while ($sthArows > $dead_delete_ct)
			{
			@aryA = $sthA->fetchrow_array;
			$dead_delete_string .= "$dead_delete_ct-$aryA[0]-$aryA[1]-$aryA[2]|";
			$dead_delete_ct++;
			}
		$sthA->finish();

		if ($dead_delete_ct > 0)
			{
			$stmtA = "DELETE FROM vicidial_auto_calls where server_ip='$server_ip' and call_time < '$XDSQLdate' and status NOT IN('XFER','CLOSER','LIVE','IVR');";
			$VACaffected_rows = $dbhA->do($stmtA);

			if ($VACaffected_rows > 0)
				{
				$event_string = "|     lagged call vac 2-minutes DELETED $VACaffected_rows|$XDSQLdate|$dead_delete_string";
				&event_logger;
				}
			}

		### For debugging purposes, try to grab Jammed calls and log them to jam logfile
		if ($useJAMdebugFILE)
			{
			$stmtA = "SELECT auto_call_id,server_ip,campaign_id,status,lead_id,uniqueid,callerid,channel,phone_code,phone_number,call_time,call_type,stage,last_update_time,alt_dial,queue_priority,agent_only,agent_grab FROM vicidial_auto_calls where server_ip='$server_ip' and last_update_time < '$BDtsSQLdate' and status IN('LIVE');";
			$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
			$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
			$sthArows=$sthA->rows;
			$JAMrec_count=0;
			while ($sthArows > $JAMrec_count)
				{
				@aryA = $sthA->fetchrow_array;
				$jam_string = "$JAMrec_count|$BDtsSQLdate|     |$aryA[0]|$aryA[1]|$aryA[2]|$aryA[3]|$aryA[4]|$aryA[5]|$aryA[6]|$aryA[7]|$aryA[8]|$aryA[9]|$aryA[10]|$aryA[11]|$aryA[12]|$aryA[13]|$aryA[14]|";
				 &jam_event_logger;
				$JAMrec_count++;
				}
			$sthA->finish();
			}

		### find call records that are LIVE and not updated for over 10 seconds
		$stmtA = "SELECT auto_call_id,lead_id,phone_number,status,campaign_id,phone_code,alt_dial,stage,callerid,uniqueid from vicidial_auto_calls where server_ip='$server_ip' and last_update_time < '$BDtsSQLdate' and status IN('LIVE','IVR');";
		$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
		$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
		$sthArows=$sthA->rows;
		$rec_count=0;
		while ($sthArows > $rec_count)
			{
			@aryA = $sthA->fetchrow_array;
			$auto_call_id =		$aryA[0];
			$CLlead_id =		$aryA[1];
			$CLphone_number =	$aryA[2];
			$CLstatus =			$aryA[3];
			$CLcampaign_id =	$aryA[4];
			$CLphone_code =		$aryA[5];
			$CLalt_dial =		$aryA[6];
			$CLstage =			$aryA[7];
			$CLcallerid	=		$aryA[8];
			$CLuniqueid	=		$aryA[9];
			$rec_count++;
			}
		$sthA->finish();

		### delete call records that are LIVE and not updated for over 10 seconds
		$rec_count=0;
		while ($sthArows > $rec_count)
			{
			$stmtA = "DELETE from vicidial_auto_calls where auto_call_id='$auto_call_id';";
			$affected_rows = $dbhA->do($stmtA);

			$stmtA = "UPDATE vicidial_live_agents set ring_callerid='' where ring_callerid='$CLcallerid';";
			$affected_rowsX = $dbhA->do($stmtA);

			$event_string = "|     lagged call vdac call DELETED $affected_rows|$affected_rowsX|$BDtsSQLdate|$auto_call_id|$CLcallerid|$CLuniqueid|$CLphone_number|$CLstatus|";
			 &event_logger;

			if ( ($affected_rows > 0) && ($CLlead_id > 0) )
				{
				$xCLlist_id=0;
				$stmtA="SELECT list_id from vicidial_list where lead_id='$CLlead_id' limit 1;";
					if ($DB) {$event_string = "|$stmtA|";   &event_logger;}
				$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
				$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
				$sthArowsVLL=$sthA->rows;
				if ($sthArowsVLL > 0)
					{
					@aryA = $sthA->fetchrow_array;
					$xCLlist_id = 	$aryA[0];
					}
				$sthA->finish();

				$jam_string = "|     lagged call vdac call DELETED $affected_rows|$BDtsSQLdate|$auto_call_id|$CLcallerid|$CLuniqueid|$CLphone_number|$CLstatus|$xCLlist_id|";
				 &jam_event_logger;

				$CLstage =~ s/LIVE|-//gi;
				if ($CLstage < 0.25) {$CLstage=0;}

				$end_epoch = $now_date_epoch;
				$stmtA = "INSERT INTO vicidial_log (uniqueid,lead_id,campaign_id,call_date,start_epoch,status,phone_code,phone_number,user,processed,length_in_sec,end_epoch,alt_dial,list_id) values('$CLuniqueid','$CLlead_id','$CLcampaign_id','$SQLdate','$now_date_epoch','DROP','$CLphone_code','$CLphone_number','VDAD','N','$CLstage','$end_epoch','$CLalt_dial','$xCLlist_id')";
					if($M){print STDERR "\n|$stmtA|\n";}
				$affected_rows = $dbhA->do($stmtA);

				$stmtB = "INSERT INTO vicidial_log_extended set uniqueid='$CLuniqueid',server_ip='$server_ip',call_date='$SQLdate',lead_id = '$CLlead_id',caller_code='$CLcallerid',custom_call_id='';";
				$affected_rowsB = $dbhA->do($stmtB);

				$event_string = "|     dead NA call added to logs $CLuniqueid|$CLlead_id|$CLphone_number|$CLstatus|DROP|$affected_rows|$affected_rowsB|";
				 &event_logger;

				if ($enable_queuemetrics_logging > 0)
					{
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
					$stmtA = "SELECT queue_position,call_date FROM vicidial_closer_log where lead_id='$CLlead_id' and campaign_id='$CLcampaign_id' and call_date > \"$RSQLdate\" order by closecallid desc limit 1;";
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

					### find current number of calls in this queue to find position when channel hung up
					$current_position=1;
					$stmtA = "SELECT count(*) FROM vicidial_auto_calls where status = 'LIVE' and campaign_id='$CLcampaign_id' and call_time < '$VCLcall_date';";
					$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
					$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
					$sthArows=$sthA->rows;
					if ($sthArows > 0)
						{
						@aryA = $sthA->fetchrow_array;
						$current_position =	($aryA[0] + 1);
						}
					$sthA->finish();

					$stmtB = "INSERT INTO queue_log SET partition='P01',time_id='$secX',call_id='$CLcallerid',queue='$CLcampaign_id',agent='NONE',verb='ABANDON',data1='$current_position',data2='$queue_position',data3='$CLstage',serverid='$queuemetrics_log_id';";
					$Baffected_rows = $dbhB->do($stmtB);

					$dbhB->disconnect();
					}

				}

			$rec_count++;
			}
		$sthA->finish();


		if ($LUcount > 0)
			{
			chop($lists_update);
			$stmtA = "UPDATE vicidial_lists SET list_lastcalldate='$SQLdate' where list_id IN($lists_update);";
			$affected_rows = $dbhA->do($stmtA);
			$event_string = "|     lastcalldate UPDATED $affected_rows|$lists_update|";
			 &event_logger;
			}

		if ($CPcount > 0)
			{
			chop($campaigns_update);
			$stmtA = "UPDATE vicidial_campaigns SET campaign_logindate='$SQLdate' where campaign_id IN($campaigns_update);";
			$affected_rows = $dbhA->do($stmtA);
			$event_string = "|     logindate UPDATED $affected_rows|$campaigns_update|";
			 &event_logger;
			}


	&get_time_now;

	###############################################################################
	###### fourth we will check to see if any campaign is running MULTI_LEAD
	######    auto-alt-dial. if yes, then go through the unprocessed extended 
	######    log entries for this server_ip and process them.
	###############################################################################
		
		$MLincall='|INCALL|QUEUE|DISPO|';
		$multi_alt_count=0;
		$stmtA = "SELECT count(*) FROM vicidial_campaigns where auto_alt_dial='MULTI_LEAD' and dial_method NOT IN('MANUAL','INBOUND_MAN') and campaign_calldate > \"$RMSQLdate\";";
		$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
		$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
		$sthArows=$sthA->rows;
		if ($sthArows > 0)
			{
			@aryA = $sthA->fetchrow_array;
			$multi_alt_count	= $aryA[0];
			}

		if ($multi_alt_count > 0)
			{
			@MLcamp_id = @MT;
			@MLaad_statuses = @MT;
			@MLlists = @MT;

			$MLcampaigns='|';
			$stmtA = "SELECT campaign_id,auto_alt_dial_statuses FROM vicidial_campaigns where auto_alt_dial='MULTI_LEAD' and dial_method NOT IN('MANUAL','INBOUND_MAN') and campaign_calldate > \"$RMSQLdate\";";
			$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
			$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
			$sthArows=$sthA->rows;
			$MLcamp_count=0;
			while ($sthArows > $MLcamp_count)
				{
				@aryA = $sthA->fetchrow_array;
				$MLcampaigns	.= "$aryA[0]|";
				$MLcamp_id[$MLcamp_count]		= $aryA[0];
				$MLaad_statuses[$MLcamp_count]	= $aryA[1];
				$MLcamp_count++;
				}

			$MLcamp_count=0;
			foreach(@MLcamp_id)
				{
				$MLlists[$MLcamp_count]='';
				$stmtA = "SELECT list_id FROM vicidial_lists where campaign_id='$MLcamp_id[$MLcamp_count]';";
				$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
				$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
				$sthArows=$sthA->rows;
				$MLlist_count=0;
				while ($sthArows > $MLlist_count)
					{
					@aryA = $sthA->fetchrow_array;
					$MLlists[$MLcamp_count]		.= "'$aryA[0]',";
					$MLlist_count++;
					}
				$MLlists[$MLcamp_count] =~ s/,$//gi;
				if (length($MLlists[$MLcamp_count]) < 2)
					{$MLlists[$MLcamp_count]="''";}
				$MLcamp_count++;
				}

			$event_string = "     MULTI_LEAD auto-alt-dial check:   $multi_alt_count active, checking unprocessed calls...";
			 &event_logger;

			@MLuniqueid = @MT;
			@MLleadid = @MT;
			@MLcalldate = @MT;
			@MLcallerid = @MT;
			@MLflag = @MT;
			@MLcampaign = @MT;
			@MLstatus = @MT;

			$stmtA = "SELECT uniqueid,lead_id,call_date,caller_code FROM vicidial_log_extended where server_ip='$server_ip' and call_date > \"$RMSQLdate\" and multi_alt_processed='N' order by call_date,lead_id limit 100000;";
			$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
			$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
			$sthArows=$sthA->rows;
			$vle_count=0;
			$vle_auto_count=0;
			while ($sthArows > $vle_count)
				{
				$MLflag[$vle_count]	= 0;
				@aryA = $sthA->fetchrow_array;
				if ($aryA[3] =~ /^V/)
					{
					$vle_auto_count++;
					$MLflag[$vle_count]	= 1;
					}
				$MLuniqueid[$vle_count]	= $aryA[0];
				$MLleadid[$vle_count]	= $aryA[1];
				$MLcalldate[$vle_count]	= $aryA[2];
				$MLcallerid[$vle_count]	= $aryA[3];
				$vle_count++;
				}
			$sthA->finish();

			$event_string = "     MULTI_LEAD auto-alt-dial vle records count:   $vle_count|$vle_auto_count";
			 &event_logger;

			$vle_count=0;
			foreach(@MLuniqueid)
				{
				if ($MLflag[$vle_count] > 0)
					{
					$vac_count=0;
					$stmtA = "SELECT count(*) FROM vicidial_auto_calls where callerid='$MLcallerid[$vle_count]' and lead_id='$MLleadid[$vle_count]';";
					$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
					$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
					$sthArows=$sthA->rows;
					if ($sthArows > 0)
						{
						@aryA = $sthA->fetchrow_array;
						$vac_count	= $aryA[0];
						}

					if ($vac_count < 1)
						{
						$stmtA = "SELECT campaign_id,status FROM vicidial_log where uniqueid='$MLuniqueid[$vle_count]' and lead_id='$MLleadid[$vle_count]' and call_date > \"$RMSQLdate\";";
						$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
						$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
						$sthArows=$sthA->rows;
						if ($sthArows > 0)
							{
							@aryA = $sthA->fetchrow_array;
							$MLcampaign[$vle_count]	= $aryA[0];
							$MLstatus[$vle_count]	= $aryA[1];

							if ($MLcampaigns =~ /\|$MLcampaign[$vle_count]\|/i)
								{
								if ($MLincall !~ /\|$MLstatus[$vle_count]\|/i)
									{
									$MLaads_value='';
									$MLlists_value="''";
									$MLcamp_match=0;
									$MLcamp_loop=0;
									while ( ($MLcamp_match < 1) && ($MLcamp_loop <= $MLcamp_count) )
										{
										if ( ($MLcampaign[$vle_count] =~ /$MLcamp_id[$MLcamp_loop]/i) && (length($MLcampaign[$vle_count]) == length($MLcamp_id[$MLcamp_loop])) )
											{
											$MLcamp_match++;
											$MLaads_value = $MLaad_statuses[$MLcamp_loop];
											$MLlists_value = $MLlists[$MLcamp_loop];
											}
										$MLcamp_loop++;
										}
									if ($MLaads_value !~ / $MLstatus[$vle_count] /i)
										{
										$event_string = "        ML status non-match, looking for matching accounts:   $MLcallerid[$vle_count]|$MLstatus[$vle_count]";
										 &event_logger;

										$MLnonmatch_output='';
										$MLnonmatch_leadids='';
										$MLvendor_lead_code='';
										$stmtA = "SELECT vendor_lead_code FROM vicidial_list where lead_id='$MLleadid[$vle_count]';";
										$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
										$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
										$sthArows=$sthA->rows;
										if ($sthArows > 0)
											{
											@aryA = $sthA->fetchrow_array;
											$MLvlc[$vle_count]	= $aryA[0];
											}
										
										if (length($MLvlc[$vle_count]) > 1)
											{
											$stmtA = "SELECT lead_id,status FROM vicidial_list where vendor_lead_code='$MLvlc[$vle_count]' and list_id IN($MLlists_value) and lead_id!='$MLleadid[$vle_count]';";
											$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
											$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
											$sthArows=$sthA->rows;
											$MLnm_count=0;
											while ($sthArows > $MLnm_count)
												{
												@aryA = $sthA->fetchrow_array;
												$MLnonmatch_output	.= "$aryA[0]-$aryA[1]|";
												$MLnonmatch_leadids	.= "'$aryA[0]',";
												$MLnm_count++;
												}
											if ($MLnm_count > 0)
												{
												chop($MLnonmatch_leadids);
												if (length($MLnonmatch_leadids)<2)
													{$MLnonmatch_leadids="''";}
												
												$event_string = "        ML status non-match, $MLnm_count matching accounts found:   $MLcallerid[$vle_count]|$MLvlc[$vle_count]\n          $MLnonmatch_output";
												 &event_logger;
												
												$stmtA = "UPDATE vicidial_list SET status='MLINAT' where lead_id IN($MLnonmatch_leadids);";
												$affected_rows = $dbhA->do($stmtA);

												$stmtB = "DELETE FROM vicidial_hopper where lead_id IN($MLnonmatch_leadids);";
												$affected_rowsH = $dbhA->do($stmtB);

												$event_string = "        ML status non-match accounts inactivated:   $MLnonmatch_leadids|$affected_rows|$affected_rowsH|MLINAT";
												 &event_logger;
												}

											### set multi-lead status non-match record to Y for processed
											$stmtA = "UPDATE vicidial_log_extended SET multi_alt_processed='Y' where uniqueid='$MLuniqueid[$vle_count]' and call_date='$MLcalldate[$vle_count]' and lead_id='$MLleadid[$vle_count]';";
											$affected_rows = $dbhA->do($stmtA);

											$event_string = "        ML status non-match, log processed:   $MLcallerid[$vle_count]|$affected_rows";
											 &event_logger;
											}
										else
											{
											### set multi-lead status non-match, no vendor_lead_code record to Y for processed
											$stmtA = "UPDATE vicidial_log_extended SET multi_alt_processed='Y' where uniqueid='$MLuniqueid[$vle_count]' and call_date='$MLcalldate[$vle_count]' and lead_id='$MLleadid[$vle_count]';";
											$affected_rows = $dbhA->do($stmtA);

											$event_string = "        ML status non-match, no vlc, log processed:   $MLcallerid[$vle_count]|$MLvlc[$vle_count]|$affected_rows";
											 &event_logger;
											}
										}
									else
										{
										### set multi-lead status match record to Y for processed
										$stmtA = "UPDATE vicidial_log_extended SET multi_alt_processed='Y' where uniqueid='$MLuniqueid[$vle_count]' and call_date='$MLcalldate[$vle_count]' and lead_id='$MLleadid[$vle_count]';";
										$affected_rows = $dbhA->do($stmtA);

										$event_string = "        ML match, log processed:   $MLcallerid[$vle_count]|$affected_rows";
										 &event_logger;
										}
									}
								else
									{
									$event_string = "        ML status in-call, do nothing:   $MLcallerid[$vle_count]|$MLuniqueid[$vle_count]|$MLstatus[$vle_count]";
									 &event_logger;
									}
								}
							else
								{
								### set non-multi-lead campaign record to U for unqualified for MULTI_LEAD processing
								$stmtA = "UPDATE vicidial_log_extended SET multi_alt_processed='U' where uniqueid='$MLuniqueid[$vle_count]' and call_date='$MLcalldate[$vle_count]' and lead_id='$MLleadid[$vle_count]';";
								$affected_rows = $dbhA->do($stmtA);
								}
							}
						else
							{
							$event_string = "        ML no log entry, do nothing:   $MLcallerid[$vle_count]|$MLuniqueid[$vle_count]";
							 &event_logger;
							}
						}
					else
						{
						$event_string = "        ML active call, do nothing:   $MLcallerid[$vle_count]";
						 &event_logger;
						}
					}
				else
					{
					### set non-auto-dial record to U for unqualified for MULTI_LEAD processing
					$stmtA = "UPDATE vicidial_log_extended SET multi_alt_processed='U' where uniqueid='$MLuniqueid[$vle_count]' and call_date='$MLcalldate[$vle_count]' and lead_id='$MLleadid[$vle_count]';";
					$affected_rows = $dbhA->do($stmtA);
					}
				$vle_count++;
				}
			}


	###############################################################################
	###### fifth we will check to see if any campaign or in-group has na_call_url
	######    populated. if yes, then go through the unprocessed extended log
	######    entries for this server_ip and process them.
	###############################################################################
		$NCUincall='|INCALL|QUEUE|DISPO|';
		$ncu_count=0;
		$ncu_in_count=0;
		$stmtA = "SELECT count(*) FROM vicidial_campaigns where na_call_url IS NOT NULL and na_call_url!='' and campaign_calldate > \"$RMSQLdate\";";
		$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
		$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
		$sthArows=$sthA->rows;
		if ($sthArows > 0)
			{
			@aryA = $sthA->fetchrow_array;
			$ncu_count	= $aryA[0];
			}
		$stmtA = "SELECT count(*) FROM vicidial_inbound_groups where na_call_url IS NOT NULL and na_call_url!='' and group_calldate > \"$RMSQLdate\";";
		$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
		$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
		$sthArows=$sthA->rows;
		if ($sthArows > 0)
			{
			@aryA = $sthA->fetchrow_array;
			$ncu_in_count	= $aryA[0];
			}

		if ( ($ncu_count > 0) || ($ncu_in_count > 0) )
			{
			@NCUcamp_id = @MT;
			@NCUncurl = @MT;
			$ncu_total_count=0;
			$NCUcampaigns='|';

			if ($ncu_count > 0)
				{
				$stmtA = "SELECT campaign_id,na_call_url FROM vicidial_campaigns where na_call_url IS NOT NULL and na_call_url!='' and campaign_calldate > \"$RMSQLdate\";";
				$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
				$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
				$sthArows=$sthA->rows;
				$NCUcamp_count=0;
				while ($sthArows > $NCUcamp_count)
					{
					@aryA = $sthA->fetchrow_array;
					$NCUcampaigns	.= "$aryA[0]|";
					$NCUcamp_id[$ncu_total_count] = $aryA[0];
					$NCUncurl[$ncu_total_count]	=	$aryA[1];
					$NCUcamp_count++;
					$ncu_total_count++;
					}
				}
			if ($ncu_in_count > 0)
				{
				$stmtA = "SELECT group_id,na_call_url FROM vicidial_inbound_groups where na_call_url IS NOT NULL and na_call_url!='' and group_calldate > \"$RMSQLdate\";";
				$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
				$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
				$sthArows=$sthA->rows;
				$NCUig_count=0;
				while ($sthArows > $NCUig_count)
					{
					@aryA = $sthA->fetchrow_array;
					$NCUcampaigns	.= "$aryA[0]|";
					$NCUcamp_id[$ncu_total_count] = $aryA[0];
					$NCUncurl[$ncu_total_count]	=	$aryA[1];
					$NCUig_count++;
					$ncu_total_count++;
					}
				}
			$event_string = "     NA-CALL-URL check:   $ncu_total_count active, checking unprocessed calls...";
			 &event_logger;

			@NCUuniqueid = @MT;
			@NCUleadid = @MT;
			@NCUcalldate = @MT;
			@NCUcallerid = @MT;
			@NCUcampaign = @MT;
			@NCUstatus = @MT;
			@NCUuser = @MT;
			@NCUphone = @MT;
			@NCUaltdial = @MT;
			@NCUcalltype = @MT;

			$stmtA = "SELECT uniqueid,lead_id,call_date,caller_code FROM vicidial_log_extended where server_ip='$server_ip' and call_date > \"$RMSQLdate\" and dispo_url_processed='N' order by call_date,lead_id limit 100000;";
			$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
			$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
			$sthArows=$sthA->rows;
			$vle_count=0;
			$vle_auto_count=0;
			while ($sthArows > $vle_count)
				{
				@aryA = $sthA->fetchrow_array;
				$NCUuniqueid[$vle_count] =	$aryA[0];
				$NCUleadid[$vle_count] =	$aryA[1];
				$NCUcalldate[$vle_count] =	$aryA[2];
				$NCUcallerid[$vle_count] =	$aryA[3];
				$vle_count++;
				}
			$sthA->finish();

			$event_string = "     NA-CALL-URL vle records count:   $vle_count|$vle_auto_count";
			 &event_logger;

			$vle_count=0;
			if (length($NCUuniqueid[0]) > 5) 
				{
				foreach(@NCUuniqueid)
					{
					if ( (length($NCUcallerid[$vle_count]) > 10) && ($NCUcallerid[$vle_count] !~ /^M/) && ($NCUcallerid[$vle_count] =~ /^V|^Y/) )
						{
						$vac_count=0;
						$stmtA = "SELECT count(*) FROM vicidial_auto_calls where callerid='$NCUcallerid[$vle_count]' and lead_id='$NCUleadid[$vle_count]';";
						$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
						$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
						$sthArows=$sthA->rows;
						if ($sthArows > 0)
							{
							@aryA = $sthA->fetchrow_array;
							$vac_count	= $aryA[0];
							}

						if ($vac_count < 1)
							{
							if ($NCUcallerid[$vle_count] =~ /^Y/) 
								{
								$stmtA = "SELECT campaign_id,status,user,phone_number,'MAIN' FROM vicidial_closer_log where uniqueid='$NCUuniqueid[$vle_count]' and lead_id='$NCUleadid[$vle_count]' and call_date='$NCUcalldate[$vle_count]';";
								$NCUcalltype[$vle_count] = 'IN';
								}
							else
								{
								$stmtA = "SELECT campaign_id,status,user,phone_number,alt_dial FROM vicidial_log where uniqueid='$NCUuniqueid[$vle_count]' and lead_id='$NCUleadid[$vle_count]' and call_date > \"$RMSQLdate\";";
								$NCUcalltype[$vle_count] = 'OUT';
								}
							$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
							$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
							$sthArows=$sthA->rows;
							if ($sthArows > 0)
								{
								@aryA = $sthA->fetchrow_array;
								$NCUcampaign[$vle_count] =	$aryA[0];
								$NCUstatus[$vle_count] =	$aryA[1];
								$NCUuser[$vle_count] =		$aryA[2];
								$NCUphone[$vle_count] =		$aryA[3];
								$NCUaltdial[$vle_count] =	$aryA[4];

								if ($NCUcampaigns =~ /\|$NCUcampaign[$vle_count]\|/i)
									{
									if ($NCUincall !~ /\|$NCUstatus[$vle_count]\|/i)
										{
										if ($NCUuser[$vle_count] =~ /VDAD|VDCL/i)
											{
											$NCUncurl_value='';
											$NCUcamp_match=0;
											$NCUcamp_loop=0;
											while ( ($NCUcamp_match < 1) && ($NCUcamp_loop <= $NCUcamp_count) )
												{
												if ( ($NCUcampaign[$vle_count] =~ /$NCUcamp_id[$NCUcamp_loop]/i) && (length($NCUcampaign[$vle_count]) == length($NCUcamp_id[$NCUcamp_loop])) )
													{
													$NCUcamp_match++;
													$NCUncurl_value = $NCUncurl[$NCUcamp_loop];
													}
												$NCUcamp_loop++;
												}
											if (length($NCUncurl_value) > 10)
												{
												$event_string = "        NCU url defined, launching web GET:   $NCUcallerid[$vle_count]|$NCUstatus[$vle_count]";
												 &event_logger;

												### set dispo_url_processed match record to Y for processed and sent
												$stmtA = "UPDATE vicidial_log_extended SET dispo_url_processed='XY' where uniqueid='$NCUuniqueid[$vle_count]' and call_date='$NCUcalldate[$vle_count]' and lead_id='$NCUleadid[$vle_count]';";
												$affected_rows = $dbhA->do($stmtA);

												$launch = $PATHhome . "/AST_send_URL.pl";
												$launch .= " --SYSLOG" if ($SYSLOG);
												$launch .= " --lead_id=" . $NCUleadid[$vle_count];
												$launch .= " --phone_number=" . $NCUphone[$vle_count];
												$launch .= " --user=" . $NCUuser[$vle_count];
												$launch .= " --call_type=" . $NCUcalltype[$vle_count];
												$launch .= " --campaign=" . $NCUcampaign[$vle_count];
												$launch .= " --uniqueid=" . $NCUuniqueid[$vle_count];
												$launch .= " --alt_dial=" . $NCUaltdial[$vle_count];
												$launch .= " --call_id=" . $NCUcallerid[$vle_count];
												$launch .= " --function=NA_CALL_URL";

												system($launch . ' &');

												$event_string = "        NCU url sent processed:   $NCUcallerid[$vle_count]|$NCUcampaign[$vle_count]|$NCUuser[$vle_count]";
												 &event_logger;
												}
											else
												{
												### set dispo_url_processed match record to Y for processed but not sent, invalid url
												$stmtA = "UPDATE vicidial_log_extended SET dispo_url_processed='XY' where uniqueid='$NCUuniqueid[$vle_count]' and call_date='$NCUcalldate[$vle_count]' and lead_id='$NCUleadid[$vle_count]';";
												$affected_rows = $dbhA->do($stmtA);

												$event_string = "        NCU invalid url defined, log processed:   $NCUcallerid[$vle_count]|$affected_rows|$NCUcampaign[$vle_count]";
												 &event_logger;
												}
											}
										else
											{
											### set dispo_url_processed record to U for unqualified processing because call sent to agent
											$stmtA = "UPDATE vicidial_log_extended SET dispo_url_processed='XY' where uniqueid='$NCUuniqueid[$vle_count]' and call_date='$NCUcalldate[$vle_count]' and lead_id='$NCUleadid[$vle_count]';";
											$affected_rows = $dbhA->do($stmtA);

											$event_string = "        NCU call sent to agent, log processed:   $NCUcallerid[$vle_count]|$affected_rows|$NCUuser[$vle_count]";
											 &event_logger;
											}
										}
									else
										{
										$event_string = "        NCU status in-call, do nothing:   $NCUcallerid[$vle_count]|$NCUuniqueid[$vle_count]|$NCUstatus[$vle_count]";
										 &event_logger;
										}
									}
								else
									{
									### set dispo_url_processed record to XU for unqualified processing because no url defined
									$stmtA = "UPDATE vicidial_log_extended SET dispo_url_processed='XU' where uniqueid='$NCUuniqueid[$vle_count]' and call_date='$NCUcalldate[$vle_count]' and lead_id='$NCUleadid[$vle_count]';";
									$affected_rows = $dbhA->do($stmtA);
									}
								}
							else
								{
								$event_string = "        NCU no log entry, do nothing:   $NCUcallerid[$vle_count]|$NCUuniqueid[$vle_count]";
								 &event_logger;
								}
							}
						else
							{
							$event_string = "        NCU active call, do nothing:   $NCUcallerid[$vle_count]";
							 &event_logger;
							}
						}
					else
						{
						### set log extended record to XU for invalid callid or manual dial call
						$stmtA = "UPDATE vicidial_log_extended SET dispo_url_processed='XU' where uniqueid='$NCUuniqueid[$vle_count]' and call_date='$NCUcalldate[$vle_count]' and lead_id='$NCUleadid[$vle_count]';";
						$affected_rows = $dbhA->do($stmtA);

						$event_string = "        NCU invalid call, mark as XU:   $NCUcallerid[$vle_count]|$NCUuniqueid[$vle_count]";
						 &event_logger;
						}
					$vle_count++;
					}
				}
			}


	###############################################################################
	###### sixth, if noanswer_log is enabled in the system settings then look for
	######    unprocessed entries for this server_ip and process them.
	###############################################################################
		$MLincall='|INCALL|QUEUE|DISPO|';
		$MLnoanswer='|NA|B|AB|DC|ADC|CPDATB|CPDB|CPDNA|CPDREJ|CPDINV|CPDSUA|CPDSI|CPDSNC|CPDSR|CPDSUK|CPDSV|CPDUK|CPDERR|';

		if ($noanswer_log =~ /Y/)
			{
			$event_string = "     NO-ANSWER log check:   $noanswer_log, active, checking unprocessed calls...";
			 &event_logger;

			@MLuniqueid = @MT;
			@MLleadid = @MT;
			@MLcalldate = @MT;
			@MLcallerid = @MT;
			@MLflag = @MT;
			@MLcampaign = @MT;
			@MLstatus = @MT;

			$stmtA = "SELECT uniqueid,lead_id,call_date,caller_code FROM vicidial_log_extended where server_ip='$server_ip' and call_date > \"$RMSQLdate\" and noanswer_processed='N' order by call_date,lead_id limit 100000;";
			$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
			$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
			$sthArows=$sthA->rows;
			$vle_count=0;
			$vle_auto_count=0;
			while ($sthArows > $vle_count)
				{
				$MLflag[$vle_count]	= 0;
				@aryA = $sthA->fetchrow_array;
				if ($aryA[3] =~ /^V/)
					{
					$vle_auto_count++;
					$MLflag[$vle_count]	= 1;
					}
				$MLuniqueid[$vle_count]	= $aryA[0];
				$MLleadid[$vle_count]	= $aryA[1];
				$MLcalldate[$vle_count]	= $aryA[2];
				$MLcallerid[$vle_count]	= $aryA[3];
				$vle_count++;
				}
			$sthA->finish();

			$event_string = "     NO-ANSWER log vle records count:   $vle_count|$vle_auto_count";
			 &event_logger;

			$vle_count=0;
			foreach(@MLuniqueid)
				{
				if ($MLflag[$vle_count] > 0)
					{
					$vac_count=0;
					$stmtA = "SELECT count(*) FROM vicidial_auto_calls where callerid='$MLcallerid[$vle_count]' and lead_id='$MLleadid[$vle_count]';";
					$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
					$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
					$sthArows=$sthA->rows;
					if ($sthArows > 0)
						{
						@aryA = $sthA->fetchrow_array;
						$vac_count	= $aryA[0];
						}
					if ($vac_count < 1)
						{
						$stmtA = "SELECT status FROM vicidial_log where uniqueid='$MLuniqueid[$vle_count]' and lead_id='$MLleadid[$vle_count]' and call_date > \"$RMSQLdate\";";
						$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
						$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
						$sthArows=$sthA->rows;
						if ($sthArows > 0)
							{
							@aryA = $sthA->fetchrow_array;
							$MLstatus[$vle_count]	= $aryA[0];

							if ($MLincall !~ /\|$MLstatus[$vle_count]\|/i)
								{
								if ($MLnoanswer =~ /\|$MLstatus[$vle_count]\|/i)
									{
									if ( ($tables_use_alt_log_db =~ /log_noanswer/i) && (length($alt_log_server_ip)>4) && (length($alt_log_dbname)>0) )
										{
										$stmtA = "SELECT uniqueid,lead_id,list_id,campaign_id,call_date,start_epoch,end_epoch,length_in_sec,status,phone_code,phone_number,user,comments,processed,user_group,term_reason,alt_dial FROM vicidial_log where uniqueid='$MLuniqueid[$vle_count]' and lead_id='$MLleadid[$vle_count]' and call_date > \"$RMSQLdate\";";
										$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
										$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
										$sthArows=$sthA->rows;
										if ($sthArows > 0)
											{
											@aryA = $sthA->fetchrow_array;

											$dbhD = DBI->connect("DBI:mysql:$alt_log_dbname:$alt_log_server_ip:3306", "$alt_log_login", "$alt_log_pass")
											 or die "Couldn't connect to database: " . DBI->errstr;

											if ($DB) {print "CONNECTED TO ALT-LOG DATABASE:  $alt_log_server_ip|$alt_log_dbname\n";}

											$stmtD = "INSERT INTO vicidial_log_noanswer SET uniqueid='$aryA[0]',lead_id='$aryA[1]',list_id='$aryA[2]',campaign_id='$aryA[3]',call_date='$aryA[4]',start_epoch='$aryA[5]',end_epoch='$aryA[6]',length_in_sec='$aryA[7]',status='$aryA[8]',phone_code='$aryA[9]',phone_number='$aryA[10]',user='$aryA[11]',comments='$aryA[12]',processed='$aryA[13]',user_group='$aryA[14]',term_reason='$aryA[15]',alt_dial='$aryA[16]',caller_code='$MLcallerid[$vle_count]';";
											$affected_rows = $dbhD->do($stmtD);

											$dbhD->disconnect();
											}
										else
											{
											$event_string = "        VNA ERROR:   $MLcallerid[$vle_count]|$MLuniqueid[$vle_count]|$MLleadid[$vle_count]";
											 &event_logger;
											}
										}
									else
										{
										### insert vicidial_log_noanswer entry
										$stmtA = "INSERT INTO vicidial_log_noanswer SELECT uniqueid,lead_id,list_id,campaign_id,call_date,start_epoch,end_epoch,length_in_sec,status,phone_code,phone_number,user,comments,processed,user_group,term_reason,alt_dial,\"$MLcallerid[$vle_count]\" from vicidial_log where uniqueid='$MLuniqueid[$vle_count]' and lead_id='$MLleadid[$vle_count]' LIMIT 1;";
										$affected_rows = $dbhA->do($stmtA);
										}

									### set noanswer to Y for processed
									$stmtB = "UPDATE vicidial_log_extended SET noanswer_processed='Y' where uniqueid='$MLuniqueid[$vle_count]' and call_date='$MLcalldate[$vle_count]' and lead_id='$MLleadid[$vle_count]';";
									$affected_rowsB = $dbhA->do($stmtB);

									$event_string = "        VNA match, log processed:   $MLcallerid[$vle_count]|$affected_rows|$affected_rowsB";
									 &event_logger;
									}
								else
									{
									### set noanswer record to U for unqualified for noanswer logging
									$stmtA = "UPDATE vicidial_log_extended SET noanswer_processed='U' where uniqueid='$MLuniqueid[$vle_count]' and call_date='$MLcalldate[$vle_count]' and lead_id='$MLleadid[$vle_count]';";
									$affected_rows = $dbhA->do($stmtA);

									$event_string = "        VNA no-match, log processed:   $MLcallerid[$vle_count]|$affected_rows";
									 &event_logger;
									}
								}
							else
								{
								$event_string = "        VNA status in-call, do nothing:   $MLcallerid[$vle_count]|$MLuniqueid[$vle_count]|$MLstatus[$vle_count]";
								 &event_logger;
								}
							}
						else
							{
							$event_string = "        VNA status no vicidial_log record, do nothing:   $MLcallerid[$vle_count]|$MLuniqueid[$vle_count]|$MLstatus[$vle_count]";
							 &event_logger;
							}
						}
					else
						{
						$event_string = "        VNA active call, do nothing:   $MLcallerid[$vle_count]";
						 &event_logger;
						}
					}
				else
					{
					### set noanswer record to U for unqualified for noanswer logging
					$stmtA = "UPDATE vicidial_log_extended SET noanswer_processed='U' where uniqueid='$MLuniqueid[$vle_count]' and call_date='$MLcalldate[$vle_count]' and lead_id='$MLleadid[$vle_count]';";
					$affected_rows = $dbhA->do($stmtA);
					}
				$vle_count++;
				}
			}



	###############################################################################
	###### last, wait for a little bit and repeat the loop
	###############################################################################

		### sleep for 2 and a half seconds before beginning the loop again
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
		if ($endless_loop =~ /0$/)	# run every ten cycles (about 25 seconds)
			{
			### Grab Server values from the database
				$stmtA = "SELECT vd_server_logs FROM servers where server_ip = '$VARserver_ip';";
				$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
				$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
				$sthArows=$sthA->rows;
				$rec_count=0;
				while ($sthArows > $rec_count)
					{
					@aryA = $sthA->fetchrow_array;
					$DBvd_server_logs =		$aryA[0];
					if ($DBvd_server_logs =~ /Y/)	{$SYSLOG = '1';}
					else {$SYSLOG = '0';}
					$rec_count++;
					}
				$sthA->finish();

			#############################################
			##### START QUEUEMETRICS LOGGING LOOKUP #####
			$stmtA = "SELECT enable_queuemetrics_logging,queuemetrics_server_ip,queuemetrics_dbname,queuemetrics_login,queuemetrics_pass,queuemetrics_log_id,queuemetrics_loginout FROM system_settings;";
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
				$queuemetrics_loginout =		$aryA[6];
				}
			$sthA->finish();
			##### END QUEUEMETRICS LOGGING LOOKUP #####
			###########################################

			### display and delete call records that are LIVE but not updated for over 100 minutes
			$stmtA = "SELECT status,lead_id,call_time FROM vicidial_auto_calls where server_ip='$server_ip' and call_time < '$TDSQLdate' and status NOT IN('XFER','CLOSER');";
			$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
			$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
			$sthArows=$sthA->rows;
			$dead_delete_string='';
			$dead_delete_ct=0;
			while ($sthArows > $dead_delete_ct)
				{
				@aryA = $sthA->fetchrow_array;
				$dead_delete_string .= "$dead_delete_ct-$aryA[0]-$aryA[1]-$aryA[2]|";
				$dead_delete_ct++;
				}
			$sthA->finish();

			if ($dead_delete_ct > 0)
				{
				### delete call records that are LIVE but not updated for over 100 minutes
				$stmtA = "DELETE FROM vicidial_auto_calls where server_ip='$server_ip' and call_time < '$TDSQLdate' and status NOT IN('XFER','CLOSER');";
				$affected_rows = $dbhA->do($stmtA);

				$event_string = "|     lagged call vac 100-minutes DELETED $affected_rows|$TDSQLdate|LIVE|$dead_delete_string";
				&event_logger;
				}

			### Grab Server values from the database in case they've changed
			$stmtA = "SELECT max_vicidial_trunks,answer_transfer_agent,local_gmt,ext_context FROM servers where server_ip = '$server_ip';";
			$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
			$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
			$sthArows=$sthA->rows;
			if ($sthArows > 0)
				{
				@aryA = $sthA->fetchrow_array;
				$DBmax_vicidial_trunks = 	$aryA[0];
				$DBanswer_transfer_agent = 	$aryA[1];
				$DBSERVER_GMT =				$aryA[2];
				$DBext_context = 			$aryA[3];
					$max_vicidial_trunks = $DBmax_vicidial_trunks;
				if ($DBanswer_transfer_agent)	{$answer_transfer_agent = $DBanswer_transfer_agent;}
				if ($DBSERVER_GMT)				{$SERVER_GMT = $DBSERVER_GMT;}
				if ($DBext_context)				{$ext_context = $DBext_context;}
				}
			$sthA->finish();

			$event_string = "|     updating server parameters $max_vicidial_trunks|$answer_transfer_agent|$SERVER_GMT|$ext_context|";
			&event_logger;

				&get_time_now;

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

		$bad_grabber_counter=0;

		$stat_count++;
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
	$file_date = "$year-$mon-$mday";
	$CIDdate = "$mon$mday$hour$min$sec";
	$tsSQLdate = "$year$mon$mday$hour$min$sec";
	$SQLdate = "$year-$mon-$mday $hour:$min:$sec";
		while (length($CIDdate) > 9) {$CIDdate =~ s/^.//gi;} # 0902235959 changed to 902235959

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
	$halfminSQLdate = "$Pyear-$Pmon-$Pmday $Phour:$Pmin:$Psec";

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

	$TDtarget = ($secX - 6000);
	($Tsec,$Tmin,$Thour,$Tmday,$Tmon,$Tyear,$Twday,$Tyday,$Tisdst) = localtime($TDtarget);
	$Tyear = ($Tyear + 1900);
	$Tmon++;
	if ($Tmon < 10) {$Tmon = "0$Tmon";}
	if ($Tmday < 10) {$Tmday = "0$Tmday";}
	if ($Thour < 10) {$Thour = "0$Thour";}
	if ($Tmin < 10) {$Tmin = "0$Tmin";}
	if ($Tsec < 10) {$Tsec = "0$Tsec";}
	$TDtsSQLdate = "$Tyear$Tmon$Tmday$Thour$Tmin$Tsec";
	$TDSQLdate = "$Tyear-$Tmon-$Tmday $Thour:$Tmin:$Tsec";

	$RMtarget = ($secX - 21600);	# 6 hours ago
	($RMsec,$RMmin,$RMhour,$RMmday,$RMmon,$RMyear,$RMwday,$RMyday,$RMisdst) = localtime($RMtarget);
	$RMyear = ($RMyear + 1900);
	$RMmon++;
	if ($RMmon < 10) {$RMmon = "0$RMmon";}
	if ($RMmday < 10) {$RMmday = "0$RMmday";}
	if ($RMhour < 10) {$RMhour = "0$RMhour";}
	if ($RMmin < 10) {$RMmin = "0$RMmin";}
	if ($RMsec < 10) {$RMsec = "0$RMsec";}
	$RMSQLdate = "$RMyear-$RMmon-$RMmday $RMhour:$RMmin:$RMsec";
	}



sub event_logger
	{
	if ($DB) {print "$now_date|$event_string|\n";}
	if ($SYSLOG)
		{
		### open the log file for writing ###
		open(Lout, ">>$VDADLOGfile")
				|| die "Can't open $VDADLOGfile: $!\n";
		print Lout "$now_date|$event_string|\n";
		close(Lout);
		}
	$event_string='';
	}

sub jam_event_logger
	{
	if ($DB) {print "$now_date|$jam_string|\n";}
	if ($useJAMdebugFILE)
		{
		### open the log file for writing ###
		open(Jout, ">>$JAMdebugFILE")
				|| die "Can't open $JAMdebugFILE: $!\n";
		print Jout "$now_date|$jam_string|\n";
		close(Jout);
		}
	$jam_string='';
	}
