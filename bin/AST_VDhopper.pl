#!/usr/bin/perl
#
# AST_VDhopper.pl version 2.4
#
# DESCRIPTION:
# Updates the VICIDIAL leads hopper for the streamlined 
# approach of allocating leads to dialers. 
#
# SUMMARY:
# For VICIDIAL auto-dial outbound dialing, this program must be in the crontab
# running every minute
# 
# It is recommended that you run this program on the local Asterisk machine
#
# hopper sources:
#  - A = Auto-alt-dial
#  - C = Scheduled Callbacks
#  - N = Xth New lead order
#  - P = Non-Agent API hopper load
#  - Q = No-hopper queue insert
#  - R = Recycled leads
#  - S = Standard hopper load
#
# Copyright (C) 2011  Matt Florell <vicidial@gmail.com>    LICENSE: AGPLv2
#
# CHANGELOG
# 50810-1613 - Added database server variable definitions lookup
# 60215-1106 - Added Scheduled Callback release functionality
# 60228-1623 - Change Callback activation to set the called_since_last_reset=N
# 60228-1735 - Added hopper gmt validation to remove gmt outside of time range
# 60320-0932 - Added inactive lead list hopper deletion (Thanks Vic Jolin)
# 60322-1030 - Added super debug output
# 60418-0947 - Added lead filter per campaign
# 60509-1416 - Rewrite of local_call_time functions
# 60511-1150 - Added inserts into vicidial_campaign_stats table
# 60609-1451 - Added ability to filter by DNC list vicidial_dnc
# 60614-1159 - Added campaign lead recycling ability
# 60715-2251 - Changed to use /etc/astguiclient.conf for configs
# 60801-1634 - Fixed Callback activation bug 000008
# 60814-1720 - Added option for no logging to file
# 60822-1527 - Added campaign_stats and logging options for adaptive dialing
# 60925-1330 - Fixed recycling leads issues
# 61110-1513 - Changed Xth NEW to fill to hopper_level with standard if not enough NEW
# 70219-1247 - Changed to use dial_statuses field instead of dial_status_x fields
# 71029-1929 - Added 5th and 6th NEW to list order
# 71030-2043 - Added hopper priority for callbacks
# 80112-0221 - Added 2nd, 3rd,... NEW for LAST NAME/PHONE Sort
# 80125-0821 - Added detail logging of each lead inserted
# 80713-0028 - Changed Recycling methodology
# 80909-1901 - Added support for campaign-specific DNC lists
# 90430-0117 - Added last call time and random sorting options
# 90430-1022 - Changed this script to allow for List Mix ability
# 90601-2111 - Added allow_inactive_list_leads to allow for inactive lists while in List Mix mode
# 90603-1157 - Fixed rare bug in list mix where statuses field do not end with -
# 90608-1201 - Added Drop Lockout Time Campaign setting option
# 90723-0842 - Added no hopper dial option to clear hopper leads
# 90809-0347 - Quick fix for null list_id loading when no active campaign lists
# 90904-1612 - Added timezone ordering
# 90907-2132 - Fixed order issues
# 91020-0054 - Fixed Auto-alt-dial DNC issues
# 91026-1207 - Added AREACODE DNC option
# 100409-1101 - Fix for rare dial-time duplicate hopper load issue
# 100427-0429 - Fix for list mix no-status-selected issue
# 100529-0843 - Changed dialable leads to calculate every run for active campaigns
# 100706-2332 - Added ability to purge only one campaign's leads in the hopper
# 101108-1451 - Added ability for the hopper level to be set automatically and remove excess leads from the hopper (MikeC)
# 110103-1118 - Added lead_order_randomize option
# 110212-2255 - Added scheduled callback custom statuses capability
# 110214-2319 - Added lead_order_secondary option
#

# constants
$DB=0;  # Debug flag, set to 0 for no debug messages, On an active system this will generate lots of lines of output per minute
$US='__';
$MT[0]='';
#$vicidial_hopper='TEST_vicidial_hopper';	# for testing
$vicidial_hopper='vicidial_hopper';

# options
$insert_auto_CB_to_hopper	= 1; # set to 1 to automatically insert ANYONE callbacks into the hopper


$secT = time();
$secX = time();
($sec,$min,$hour,$mday,$mon,$year,$wday,$yday,$isdst) = localtime(time);
$year = ($year + 1900);
$mon++;
$wtoday = $wday;
if ($mon < 10) {$mon = "0$mon";}
if ($mday < 10) {$mday = "0$mday";}
if ($hour < 10) {$hour = "0$hour";}
if ($min < 10) {$min = "0$min";}
if ($sec < 10) {$sec = "0$sec";}
$file_date = "$year-$mon-$mday";
$now_date = "$year-$mon-$mday $hour:$min:$sec";
$VDL_date = "$year-$mon-$mday 00:00:01";

### get date-time of one hour ago ###
$VDL_hour = ($secX - (60 * 60));
($Vsec,$Vmin,$Vhour,$Vmday,$Vmon,$Vyear,$Vwday,$Vyday,$Visdst) = localtime($VDL_hour);
$Vyear = ($Vyear + 1900);
$Vmon++;
if ($Vmon < 10) {$Vmon = "0$Vmon";}
if ($Vmday < 10) {$Vmday = "0$Vmday";}
$VDL_hour = "$Vyear-$Vmon-$Vmday $Vhour:$Vmin:$Vsec";

### get date-time of half hour ago ###
$VDL_halfhour = ($secX - (30 * 60));
($Vsec,$Vmin,$Vhour,$Vmday,$Vmon,$Vyear,$Vwday,$Vyday,$Visdst) = localtime($VDL_halfhour);
$Vyear = ($Vyear + 1900);
$Vmon++;
if ($Vmon < 10) {$Vmon = "0$Vmon";}
if ($Vmday < 10) {$Vmday = "0$Vmday";}
$VDL_halfhour = "$Vyear-$Vmon-$Vmday $Vhour:$Vmin:$Vsec";

### get date-time of five minutes ago ###
$VDL_five = ($secX - (5 * 60));
($Vsec,$Vmin,$Vhour,$Vmday,$Vmon,$Vyear,$Vwday,$Vyday,$Visdst) = localtime($VDL_five);
$Vyear = ($Vyear + 1900);
$Vmon++;
if ($Vmon < 10) {$Vmon = "0$Vmon";}
if ($Vmday < 10) {$Vmday = "0$Vmday";}
$VDL_five = "$Vyear-$Vmon-$Vmday $Vhour:$Vmin:$Vsec";

### get date-time of one minute ago ###
$VDL_one = ($secX - (1 * 60));
($Vsec,$Vmin,$Vhour,$Vmday,$Vmon,$Vyear,$Vwday,$Vyday,$Visdst) = localtime($VDL_one);
$Vyear = ($Vyear + 1900);
$Vmon++;
if ($Vmon < 10) {$Vmon = "0$Vmon";}
if ($Vmday < 10) {$Vmday = "0$Vmday";}
$VDL_one = "$Vyear-$Vmon-$Vmday $Vhour:$Vmin:$Vsec";

### get date-time of 10 seconds ago ###
$VDL_tensec = ($secX - 10);
($Vsec,$Vmin,$Vhour,$Vmday,$Vmon,$Vyear,$Vwday,$Vyday,$Visdst) = localtime($VDL_one);
$Vyear = ($Vyear + 1900);
$Vmon++;
if ($Vmon < 10) {$Vmon = "0$Vmon";}
if ($Vmday < 10) {$Vmday = "0$Vmday";}
$VDL_tensec = "$Vyear-$Vmon-$Vmday $Vhour:$Vmin:$Vsec";

### begin parsing run-time options ###
if (length($ARGV[0])>1)
	{
	$i=0;
	$allow_inactive_list_leads=0;
		while ($#ARGV >= $i)
		{
		$args = "$args $ARGV[$i]";
		$i++;
		}

	if ($args =~ /--help/i)
		{
		print "allowed run time options(must stay in this order):\n";
		print "  [--debug] = debug\n";
		print "  [--debugX] = super debug\n";
		print "  [--dbgmt] = show GMT offset of records as they are inserted into hopper\n";
		print "  [--dbdetail] = additional level of logging the leads that are inserted into the hopper\n";
		print "  [--allow-inactive-list-leads] = do not delete inactive list leads\n";
		print "  [-t] = test\n";
		print "  [--level=XXX] = force a hopper_level of XXX\n";
		print "  [--campaign=XXX] = run for campaign XXX only\n";
		print "  [--wipe-hopper-clean] = deletes everything from the hopper    USE WITH CAUTION!!!\n";
		print "\n";
		exit;
		}
	else
		{
		if ($args =~ /--campaign=/i)
			{
			#	print "\n|$ARGS|\n\n";
			@data_in = split(/--campaign=/,$args);
			$CLIcampaign = $data_in[1];
			$CLIcampaign =~ s/ .*$//gi;
			}
		else
			{$CLIcampaign = '';}
		if ($args =~ /--level=/i)
			{
			@data_in = split(/--level=/,$args);
			$CLIlevel = $data_in[1];
			$CLIlevel =~ s/ .*$//gi;
			$CLIlevel =~ s/\D//gi;
			print "\n-----HOPPER LEVEL OVERRIDE: $CLIlevel -----\n\n";
			}
		else
			{$CLIlevel = '';}
		if ($args =~ /--debug/i)
			{
			$DB=1;
			print "\n----- DEBUG -----\n\n";
			}
		if ($args =~ /--debugX/i)
			{
			$DBX=1;
			print "\n----- SUPER DEBUG -----\n\n";
			}
		if ($args =~ /--dbgmt/i)
			{
			$DB_show_offset=1;
			print "\n-----DEBUG GMT -----\n\n";
			}
		if ($args =~ /--dbdetail/i)
			{
			$DB_detail=1;
	#		print "\n-----DEBUG DETAIL -----\n\n";
			}
		if ($args =~ /--allow-inactive-list-leads/i)
			{
			$allow_inactive_list_leads=1;
	#		print "\n-----DEBUG DETAIL -----\n\n";
			}
		if ($args =~ /-t/i)
			{
			$T=1;   $TEST=1;
			print "\n-----TESTING -----\n\n";
			}
		if ($args =~ /--wipe-hopper-clean/i)
			{
			$wipe_hopper_clean=1;
			}
		}
	}
else
	{
	print "no command line options set\n";
	}

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

if (!$VDHLOGfile) {$VDHLOGfile = "$PATHlogs/hopper.$year-$mon-$mday";}
if (!$VDHDLOGfile) {$VDHDLOGfile = "$PATHlogs/hopper-detail.$year-$mon-$mday";}
if (!$VARDB_port) {$VARDB_port='3306';}

use DBI;	  

$dbhA = DBI->connect("DBI:mysql:$VARDB_database:$VARDB_server:$VARDB_port", "$VARDB_user", "$VARDB_pass")
 or die "Couldn't connect to database: " . DBI->errstr;


### Grab Server values from the database
$stmtA = "SELECT vd_server_logs,local_gmt FROM servers where server_ip = '$VARserver_ip';";
$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
$sthArows=$sthA->rows;
if ($sthArows==0) {die "Server IP $VARserver_ip does not have an entry in the servers table\n\n";}	
if ($sthArows > 0)
	{
	@aryA = $sthA->fetchrow_array;
	$DBvd_server_logs =			$aryA[0];
	$DBSERVER_GMT		=		$aryA[1];
	if ($DBvd_server_logs =~ /Y/)	{$SYSLOG = '1';}
	else {$SYSLOG = '0';}
	if (length($DBSERVER_GMT)>0)	{$SERVER_GMT = $DBSERVER_GMT;}
	}
$sthA->finish();



$secX = time();
($sec,$min,$hour,$mday,$mon,$year,$wday,$yday,$isdst) = localtime($secX);
$LOCAL_GMT_OFF = $SERVER_GMT;
$LOCAL_GMT_OFF_STD = $SERVER_GMT;
if ($isdst) {$LOCAL_GMT_OFF++;} 

$GMT_now = ($secX - ($LOCAL_GMT_OFF * 3600));
($sec,$min,$hour,$mday,$mon,$year,$wday,$yday,$isdst) = localtime($GMT_now);
$mon++;
$year = ($year + 1900);
if ($mon < 10) {$mon = "0$mon";}
if ($mday < 10) {$mday = "0$mday";}
if ($hour < 10) {$hour = "0$hour";}
if ($min < 10) {$min = "0$min";}
if ($sec < 10) {$sec = "0$sec";}

if ($DB) {print "TIME DEBUG: $LOCAL_GMT_OFF_STD|$LOCAL_GMT_OFF|$isdst|   GMT: $hour:$min\n";}

if ($wipe_hopper_clean)
	{
	if (length($CLIcampaign)>0)
		{
		$stmtA = "DELETE from $vicidial_hopper where campaign_id = '$CLIcampaign';";
		}
	else
		{
		$stmtA = "DELETE from $vicidial_hopper;";
		}
	$affected_rows = $dbhA->do($stmtA);
	if ($DB) {print "Hopper Wiped Clean:  $affected_rows\n";}
	$event_string = "|HOPPER WIPE CLEAN|";
	&event_logger;

	exit;
	}
### Delete leads from inactive lists if there are any
$stmtA = "SELECT list_id FROM vicidial_lists where active='N';";
if ($DB) {print $stmtA;}
$inactive_lists='';
$inactive_lists_count=0;
$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
$sthArows=$sthA->rows;
while ($sthArows > $inactive_lists_count)
	{
	@aryA = $sthA->fetchrow_array;
	$inactive_list = $aryA[0];
	$inactive_lists .= "'$inactive_list',";
	$inactive_lists_count++;
	}
$sthA->finish();
if ($DB) {print "Inactive Lists:  $inactive_lists_count\n";}

if ($inactive_lists_count > 0)
	{
	chop($inactive_lists);
	if ($allow_inactive_list_leads < 1)
		{
		$stmtA = "DELETE from $vicidial_hopper where list_id IN($inactive_lists);";
		$affected_rows = $dbhA->do($stmtA);
		if ($DB) {print "Inactive List Leads Deleted:  $affected_rows |$stmtA|\n";}
			$event_string = "|INACTIVE LIST DEL|$affected_rows|";
			&event_logger;
		}
	}


### BEGIN Change CBHOLD status leads to CALLBK if their vicidial_callbacks time has passed
$stmtA = "SELECT count(*) FROM vicidial_callbacks where callback_time <= '$now_date' and status='ACTIVE';";
$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
@aryA = $sthA->fetchrow_array;
$CBHOLD_count = $aryA[0];
if ($DB) {print "CALLBACK HOLD: $CBHOLD_count|$stmtA|\n";}
$sthA->finish();

if ($CBHOLD_count > 0)
	{
	$update_leads='';
	$cbc=0;
	$cba=0;
	$stmtA = "SELECT vicidial_callbacks.lead_id,recipient,campaign_id,vicidial_callbacks.list_id,gmt_offset_now,state,vicidial_callbacks.lead_status FROM vicidial_callbacks,vicidial_list where callback_time <= '$now_date' and vicidial_callbacks.status='ACTIVE' and vicidial_callbacks.lead_id=vicidial_list.lead_id;";
	$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
	$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
	$sthArows=$sthA->rows;
	while ($sthArows > $cbc)
		{
		@aryA = $sthA->fetchrow_array;
		$lead_ids[$cbc] = $aryA[0];
		$recipient = $aryA[1];
		$update_leads .= "'$lead_ids[$cbc]',";
		if ($recipient =~ /ANYONE/)
			{
			$CA_lead_id[$cba] =			$aryA[0];
			$CA_campaign_id[$cba] =		$aryA[2];
			$CA_list_id[$cba] =			$aryA[3];
			$CA_gmt_offset_now[$cba] =	$aryA[4];
			$CA_state[$cba] =			$aryA[5];
			$CA_status[$cba] =			$aryA[6];
			$cba++;
			}
		$cbc++;
		}
	$sthA->finish();
	if ($cbc > 0)
		{
		chop($update_leads);

		$stmtA = "UPDATE vicidial_callbacks set status='LIVE' where lead_id IN($update_leads) and status NOT IN('INACTIVE','DEAD','ARCHIVE');";
		$affected_rows = $dbhA->do($stmtA);
		if ($DB) {print "Scheduled Callbacks Activated:  $affected_rows\n";}
		$event_string = "|CALLBACKS CB ACT |$affected_rows|";
		&event_logger;
		}
	### INSERT ANYONE CALLBACKS INTO HOPPER DIRECTLY ###
	if ( ($cba > 0) && ($insert_auto_CB_to_hopper) )
		{
		if ($DB) {print "ANYONE Scheduled Callbacks to Insert into hopper:  $cba\n";}
		$event_string = "|ANYONE CB HOPPER |$cba|";
		&event_logger;
		$CAu=0;
		foreach(@CA_lead_id)
			{
			$stmtA = "UPDATE vicidial_list set status='$CA_status[$CAu]', called_since_last_reset='N' where lead_id='$CA_lead_id[$CAu]';";
			$affected_rows = $dbhA->do($stmtA);
			if ($DB) {print "Scheduled Callbacks Activated:  $affected_rows\n";}
			$event_string = "|CALLBACKS LISTACT|$affected_rows|";
			&event_logger;

			$stmtA = "INSERT INTO $vicidial_hopper SET lead_id='$CA_lead_id[$CAu]',campaign_id='$CA_campaign_id[$CAu]',list_id='$CA_list_id[$CAu]',gmt_offset_now='$CA_gmt_offset_now[$CAu]',user='',state='$CA_state[$CAu]',priority='50',source='C';";
			$affected_rows = $dbhA->do($stmtA);
			if ($DB) {print "ANYONE Scheduled Callback Inserted into hopper:  $affected_rows|$CA_lead_id[$CAu]\n";}
			$CAu++;
			}
		}
	}
### END Change CBHOLD status leads to CALLBK if their vicidial_callbacks time has passed


### BEGIN Auto-Alt-Dial DNC check and update or delete
### Find out how many leads in the hopper are set to DNC status
$hopper_dnc_count=0;
$stmtA = "SELECT count(*) from $vicidial_hopper where status='DNC';";
$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
$sthArows=$sthA->rows;
if ($sthArows > 0)
	{
	@aryA = $sthA->fetchrow_array;
	$hopper_dnc_count = $aryA[0];
	if ($DB) {print "     hopper DNC count:   $hopper_dnc_count\n";}
	if ($DBX) {print "     |$stmtA|\n";}
	}
$sthA->finish();
if ($hopper_dnc_count > 0)
	{
	$event_string = "|hopper DNC count: $hopper_dnc_count||";
	&event_logger;

	### Gather all DNC statused vicidial_hopper entries
	$stmtA = "SELECT hopper_id,lead_id,alt_dial,campaign_id FROM $vicidial_hopper where status='DNC';";
	$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
	$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
	$sthArowsVHdnc=$sthA->rows;
	$aad=0;
	while ($sthArowsVHdnc > $aad)
		{
		@aryA = $sthA->fetchrow_array;
		$AAD_hopper_id[$aad] =		$aryA[0];
		$AAD_lead_id[$aad] =		$aryA[1];
		$AAD_alt_dial[$aad] =		$aryA[2];
		$AAD_campaign_id[$aad] =	$aryA[3];
		$aad++;
		}
	$sthA->finish();

	### Go through all DNC statused vicidial_hopper entries and look for next number. If found, set the next phone number, alt dial and change status to READY
	$aad=0;
	while ($sthArowsVHdnc > $aad)
		{
		$VD_auto_alt_dial='';
		$VD_use_internal_dnc='';
		$VD_use_campaign_dnc='';
		$VD_alt_dial =		$AAD_alt_dial[$aad];
		$VD_campaign_id =	$AAD_campaign_id[$aad];
		$VD_lead_id =		$AAD_lead_id[$aad];
		### look up auto-alt-dial settings for campaign
		$stmtA = "SELECT auto_alt_dial,use_internal_dnc,use_campaign_dnc FROM vicidial_campaigns where campaign_id='$AAD_campaign_id[$aad]';";
		$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
		$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
		$sthArows=$sthA->rows;
		if ($sthArows > 0)
			{
			@aryA = $sthA->fetchrow_array;
			$VD_auto_alt_dial =		$aryA[0];
			$VD_use_internal_dnc =	$aryA[1];
			$VD_use_campaign_dnc =	$aryA[2];
			}
		$sthA->finish();

		$event_string = "|DNC record: $AAD_lead_id[$aad]|$AAD_campaign_id[$aad]|$VD_auto_alt_dial|$AAD_alt_dial[$aad]";
		&event_logger;

		if ( ($VD_auto_alt_dial =~ /(ALT_ONLY|ALT_AND_ADDR3|ALT_AND_EXTENDED)/) && ($VD_alt_dial =~ /NONE|MAIN/) )
			{
			$alt_dial_skip=0;
			$VD_alt_phone='';
			$stmtA="SELECT alt_phone,gmt_offset_now,state,list_id FROM vicidial_list where lead_id='$AAD_lead_id[$aad]';";
				if ($DB) {$event_string = "|$stmtA|";   &event_logger;}
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
						if ($DB) {$event_string = "|$stmtA|";   &event_logger;}
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
						if ($DB) {$event_string = "|$stmtA|";   &event_logger;}
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
					$stmtA = "UPDATE vicidial_hopper SET status='READY',list_id='$VD_list_id',gmt_offset_now='$VD_gmt_offset_now',state='$VD_state',alt_dial='ALT',user='',priority='25' where hopper_id='$AAD_hopper_id[$aad]';";
					$affected_rows = $dbhA->do($stmtA);
					if ($DB) {$event_string = "--    VDH record updated: |$affected_rows|   |$stmtA|";   &event_logger;}
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
			$stmtA="SELECT address3,gmt_offset_now,state,list_id FROM vicidial_list where lead_id='$AAD_lead_id[$aad]';";
				if ($DB) {$event_string = "|$stmtA|";   &event_logger;}
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
						if ($DB) {$event_string = "|$stmtA|";   &event_logger;}
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
						$stmtA="SELECT count(*) FROM vicidial_campaign_dnc where phone_number IN('$VD_address3','$addr3_areacode') and campaign_id='$VD_campaign_id';";
						}
					else
						{$stmtA="SELECT count(*) FROM vicidial_campaign_dnc where phone_number='$VD_address3' and campaign_id='$VD_campaign_id';";}
						if ($DB) {$event_string = "|$stmtA|";   &event_logger;}
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
					$stmtA = "UPDATE vicidial_hopper SET status='READY',list_id='$VD_list_id',gmt_offset_now='$VD_gmt_offset_now',state='$VD_state',alt_dial='ADDR3',user='',priority='25' where hopper_id='$AAD_hopper_id[$aad]';";
					$affected_rows = $dbhA->do($stmtA);
					if ($DB) {$event_string = "--    VDH record updated: |$affected_rows|   |$stmtA|";   &event_logger;}
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
				if ($DB) {$event_string = "|$stmtA|";   &event_logger;}
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
			$stmtA="SELECT count(*) FROM vicidial_list_alt_phones where lead_id='$AAD_lead_id[$aad]';";
				if ($DB) {$event_string = "|$stmtA|";   &event_logger;}
			$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
			$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
			$sthArows=$sthA->rows;
			if ($sthArows > 0)
				{
				@aryA = $sthA->fetchrow_array;
				$alt_dial_phones_count = $aryA[0];
				}
			$sthA->finish();

			if ($alt_dial_phones_count <= $Xlast) 
				{
				$stmtA = "DELETE FROM vicidial_hopper where hopper_id='$AAD_hopper_id[$aad]';";
				$affected_rows = $dbhA->do($stmtA);
				if ($DB) {$event_string = "--    VDH record DNC deleted: |$affected_rows|   |$stmtA|X$Xlast|$VD_altdial_id|";   &event_logger;}
				}
				
			while ( ($alt_dial_phones_count > 0) && ($alt_dial_phones_count > $Xlast) )
				{
				$Xlast++;
				$stmtA="SELECT alt_phone_id,phone_number,active FROM vicidial_list_alt_phones where lead_id='$VD_lead_id' and alt_phone_count='$Xlast';";
					if ($DB) {$event_string = "|$stmtA|";   &event_logger;}
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
							if ($DB) {$event_string = "|$stmtA|";   &event_logger;}
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
							$ad_areacode = substr($VD_altdial_phone, 0, 3);
							$ad_areacode .= "XXXXXXX";
							$stmtA="SELECT count(*) FROM vicidial_campaign_dnc where phone_number IN('$VD_altdial_phone','$ad_areacode') and campaign_id='$VD_campaign_id';";
							}
						else
							{$stmtA="SELECT count(*) FROM vicidial_campaign_dnc where phone_number='$VD_altdial_phone' and campaign_id='$VD_campaign_id';";}
							if ($DB) {$event_string = "|$stmtA|";   &event_logger;}
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
						$stmtA = "UPDATE vicidial_hopper SET status='READY',list_id='$VD_list_id',gmt_offset_now='$VD_gmt_offset_now',state='$VD_state',alt_dial='X$Xlast',user='',priority='25' where hopper_id='$AAD_hopper_id[$aad]';";
						$affected_rows = $dbhA->do($stmtA);
						if ($DB) {$event_string = "--    VDH record updated: |$affected_rows|   |$stmtA|X$Xlast|$VD_altdial_id|";   &event_logger;}
						$Xlast=9999999999;
						}
					else
						{
						if ($alt_dial_phones_count eq '$Xlast') 
							{$Xlast = 'LAST';}
						$stmtA = "UPDATE vicidial_hopper SET status='DNC',list_id='$VD_list_id',gmt_offset_now='$VD_gmt_offset_now',state='$VD_state',alt_dial='X$Xlast',user='',priority='15' where hopper_id='$AAD_hopper_id[$aad]';";
						$affected_rows = $dbhA->do($stmtA);
						if ($DB) {$event_string = "--    VDH record DNC updated: |$affected_rows|   |$stmtA|X$Xlast|$VD_altdial_id|";   &event_logger;}
						$Xlast=9999999999;
						if ($DB) {$event_string = "--    VDH alt dial is DNC|X$Xlast|$VD_altdial_phone|";   &event_logger;}
						}
					}
				}
			}
		if ($VD_alt_dial =~ /XLAST/)
			{
			$stmtA = "DELETE FROM vicidial_hopper where hopper_id='$AAD_hopper_id[$aad]';";
			$affected_rows = $dbhA->do($stmtA);
			if ($DB) {$event_string = "--    VDH record DNC deleted: |$affected_rows|   |$stmtA|X$Xlast|$VD_altdial_id|";   &event_logger;}
			}


		$aad++;
		}
	}
### END Auto-Alt-Dial DNC check and update or delete



@campaign_id=@MT; 

if ($CLIcampaign)
	{
	$stmtA = "SELECT campaign_id,lead_order,hopper_level,auto_dial_level,local_call_time,lead_filter_id,use_internal_dnc,dial_method,available_only_ratio_tally,adaptive_dropped_percentage,adaptive_maximum_level,dial_statuses,list_order_mix,use_campaign_dnc,drop_lockout_time,no_hopper_dialing,auto_alt_dial_statuses,dial_timeout,auto_hopper_multi,use_auto_hopper,auto_trim_hopper,lead_order_randomize,lead_order_secondary from vicidial_campaigns where campaign_id='$CLIcampaign';";
	}
else
	{
	$stmtA = "SELECT campaign_id,lead_order,hopper_level,auto_dial_level,local_call_time,lead_filter_id,use_internal_dnc,dial_method,available_only_ratio_tally,adaptive_dropped_percentage,adaptive_maximum_level,dial_statuses,list_order_mix,use_campaign_dnc,drop_lockout_time,no_hopper_dialing,auto_alt_dial_statuses,dial_timeout,auto_hopper_multi,use_auto_hopper,auto_trim_hopper,lead_order_randomize,lead_order_secondary from vicidial_campaigns where active='Y';";
	}
$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
$sthArows=$sthA->rows;
$rec_count=0;
while ($sthArows > $rec_count)
	{
	@aryA = $sthA->fetchrow_array;
	$campaign_id[$rec_count] =					$aryA[0];
	$lead_order[$rec_count] =					$aryA[1];
	if (!$CLIlevel) 
		{$hopper_level[$rec_count] =			$aryA[2];}
	else
		{$hopper_level[$rec_count] = "$CLIlevel";}
	$auto_dial_level[$rec_count] =				$aryA[3];
	$local_call_time[$rec_count] =				$aryA[4];
	$lead_filter_id[$rec_count] =				$aryA[5];
	$use_internal_dnc[$rec_count] =				$aryA[6];
	$dial_method[$rec_count] =					$aryA[7];
	$available_only_ratio_tally[$rec_count] =	$aryA[8];
	$adaptive_dropped_percentage[$rec_count] =	$aryA[9];
	$adaptive_maximum_level[$rec_count] =		$aryA[10];
	$dial_statuses[$rec_count] =				$aryA[11];
	$list_order_mix[$rec_count] =				$aryA[12];
	$use_campaign_dnc[$rec_count] =				$aryA[13];
	$drop_lockout_time[$rec_count] =			$aryA[14];
	$no_hopper_dialing[$rec_count] = 			$aryA[15];
	$auto_alt_dial_statuses[$rec_count] = 		$aryA[16];
	$dial_timeout[$rec_count] = 				$aryA[17];
	$auto_hopper_multi[$rec_count] =			$aryA[18];
	$use_auto_hopper[$rec_count] = 				$aryA[19];
	$auto_trim_hopper[$rec_count] =				$aryA[20];
	$lead_order_randomize[$rec_count] =			$aryA[21];
	$lead_order_secondary[$rec_count] =			$aryA[22];

	### Auto Hopper Level
	if ( $use_auto_hopper[$rec_count] =~ /Y/) 
		{
		### Find the number of agents
		$stmtB = "SELECT COUNT(*) FROM vicidial_live_agents WHERE campaign_id='$campaign_id[$rec_count]' and status IN ('READY','QUEUE','INCALL','CLOSER') and last_update_time >= '$VDL_tensec'";
		$sthB = $dbhA->prepare($stmtB) or die "preparing: ",$dbhA->errstr;
		$sthB->execute or die "executing: $stmtB ", $dbhA->errstr;
		@aryAgent = $sthB->fetchrow_array;
		$num_agents = $aryAgent[0];
		$sthB->finish();

		$stmtB = "SELECT COUNT(*) FROM vicidial_live_agents WHERE campaign_id='$campaign_id[$rec_count]' and status IN ('PAUSED') and last_update_time >= '$VDL_tensec' and last_state_change >= '$VDL_one'";
		$sthB = $dbhA->prepare($stmtB) or die "preparing: ",$dbhA->errstr;
		$sthB->execute or die "executing: $stmtB ", $dbhA->errstr;
		@aryAgent = $sthB->fetchrow_array;
		$num_paused_agents = $aryAgent[0];
		$sthB->finish();

		### Make sure the auto hopper multiplier is not set to something stupid
		if ( $auto_hopper_multi[$rec_count] <= 0 ) { $auto_hopper_multi[$rec_count] = 0.1; }

		### Save for debug info
		$minimum_hopper_level = $hopper_level[$rec_count];

		### Number of calls per minute
		$dial_timeout_mult = 60 / $dial_timeout[$rec_count];

		$auto_hopper_level = roundup( $auto_hopper_multi[$rec_count] * ( $num_agents + $num_paused_agents ) * $auto_dial_level[$rec_count] * $dial_timeout_mult );	

		### Make sure we are greater than the minimum hopper level
		if ( $auto_hopper_level >= $minimum_hopper_level )
			{
			$hopper_level[$rec_count] = $auto_hopper_level
			}
		else
			{
			$hopper_level[$rec_count] = $minimum_hopper_level;
			}

		if ($DB) 
			{	
			print "---------------Auto Hopper Level Enabled For $campaign_id[$rec_count]---------------------\n";
			print "Number of Agents = $num_agents\n";
			print "Number of Paused Agents = $num_paused_agents\n";
			print "Auto Dial Level = $auto_dial_level[$rec_count]\n";
			print "Dial Timeout = $dial_timeout[$rec_count]\n";
			print "Dial Timeout Multiplier = $dial_timeout_mult\n";
			print "Auto Hopper Multipier = $auto_hopper_multi[$rec_count]\n";
			print "Minimum Hopper Level = $minimum_hopper_level\n";
			print "Auto Hopper Level Adjustment = $auto_hopper_level\n";
			print "Final Hopper Level = $hopper_level[$rec_count]\n\n";
			}

		$stmtB = "UPDATE vicidial_campaigns set auto_hopper_level = '$auto_hopper_level' where campaign_id='$campaign_id[$rec_count]'";
		$affected_rows = $dbhA->do($stmtB);
		}

	### Auto Trim Hopper code
	if ( $auto_trim_hopper[$rec_count] =~ /Y/) 
		{
		if ($DB) { print "---------------Auto Trim Hopper Enabled For $campaign_id[$rec_count]---------------------\n"; }
	
		$stmtB = "SELECT COUNT(*) FROM vicidial_hopper WHERE campaign_id='$campaign_id[$rec_count]' and status IN ('READY') and source IN('S','N');";
		$sthB = $dbhA->prepare($stmtB) or die "preparing: ",$dbhA->errstr;
		$sthB->execute or die "executing: $stmtB ", $dbhA->errstr;
		@aryLead = $sthB->fetchrow_array;
		$camp_leads = $aryLead[0];
		$sthB->finish();

		if ($DB) 
			{ 
			print "Leads in Hopper for this Campaign = $camp_leads\n";
			print "Hopper Level for this Campaign = $hopper_level[$rec_count]\n";
			}

		if ( $camp_leads > ( 2 * $hopper_level[$rec_count] ) ) 
			{
			$num_to_delete = $camp_leads - 2 * $hopper_level[$rec_count];
			$stmtB = "DELETE FROM vicidial_hopper WHERE campaign_id='$campaign_id[$rec_count]' AND source='S' AND status IN ('READY') LIMIT $num_to_delete";
			$affected_rows = $dbhA->do($stmtB);
			
			if ($DB) 
				{ 
				print "TOO MANY LEADS IN THE HOPPER ( $camp_leads > 2 * $hopper_level[$rec_count] ). \n"; 
				print "DELETING $num_to_delete LEADS FROM THE HOPPER.  |$affected_rows|\n";
				}
			}
		if ($DB) { print "\n"; }
		}	
	$rec_count++;
	}
$sthA->finish();
if ($DB) {print "CAMPAIGNS TO PROCESSES HOPPER FOR:  $rec_count|$#campaign_id\n";}


##### LOOP THROUGH EACH CAMPAIGN AND PROCESS THE HOPPER #####
$i=0;
foreach(@campaign_id)
	{
	if ($no_hopper_dialing[$i] =~ /Y/)
		{
		if ($DB) {print "Campaign $campaign_id[$i] set to no-hopper-dialing: $no_hopper_dialing[$i]\n";}

		### BEGIN Change CBHOLD status leads to CALLBK if their vicidial_callbacks time has passed
		$stmtA = "SELECT count(*) FROM $vicidial_hopper where campaign_id='$campaign_id[$i]' and status NOT IN('QUEUE','HOLD');";
		$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
		$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
		@aryA = $sthA->fetchrow_array;
		$NOHOPPER_count = $aryA[0];
		if ($DBX) {print "NO HOPPER COUNT: $NOHOPPER_count|$stmtA|\n";}
		$sthA->finish();

		if ($NOHOPPER_count > 0)
			{
			$stmtA = "DELETE from $vicidial_hopper where campaign_id='$campaign_id[$i]' and status NOT IN('QUEUE','HOLD');";
			$affected_rows = $dbhA->do($stmtA);
			if ($DB) {print "No Hopper delete:  $affected_rows\n";}
			$event_string = "|NO HOPPER DELETE|$affected_rows|";
			&event_logger;
			}
		}
	else
		{
		$vicidial_log = 'vicidial_log';
		$VCSdialable_leads[$i]=0;

		if ($list_order_mix[$i] =~ /DISABLED/)
			{
			$dial_statuses[$i] =~ s/ -$//gi;
			@Dstatuses = split(/ /,$dial_statuses[$i]);
			$Ds_to_print = (($#Dstatuses) + 0);
			$STATUSsql[$i]='';
			$o=0;
			while ($Ds_to_print > $o) 
				{
				$o++;
				$STATUSsql[$i] .= "'$Dstatuses[$o]',";
				}
			if (length($STATUSsql[$i])<3) {$STATUSsql[$i]="''";}
			else {chop($STATUSsql[$i]);}
			}

		### BEGIN - GATHER STATS FROM THE vicidial_campaign_stats TABLE ###
		$stmtA = "SELECT dialable_leads from vicidial_campaign_stats where campaign_id='$campaign_id[$i]';";
		$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
		$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
		$sthArows=$sthA->rows;
		$rec_count=0;
		while ($sthArows > $rec_count)
			{
			@aryA = $sthA->fetchrow_array;
			$VCSdialable_leads[$i] = $aryA[0];
			$rec_count++;
			}
		$sthA->finish();
		### END - GATHER STATS FROM THE vicidial_campaign_stats TABLE ###

		##### BEGIN calculate what gmt_offset_now values are within the allowed local_call_time setting ###
		$g=0;
		$p='13';
		$GMT_gmt[0] = '';
		$GMT_hour[0] = '';
		$GMT_day[0] = '';
		if ($DBX) {print "\n   |GMT-DAY-HOUR|   ";}
		while ($p > -13)
			{
			$pzone = ($GMT_now + ($p * 3600));
			($psec,$pmin,$phour,$pmday,$pmon,$pyear,$pday,$pyday,$pisdst) = localtime($pzone);
			$phour=($phour * 100);
			$tz = sprintf("%.2f", $p);	
			$GMT_gmt[$g] = "$tz";
			$GMT_day[$g] = "$pday";
			$GMT_hour[$g] = ($phour + $pmin);
			$p = ($p - 0.25);
			if ($DBX) {print "|$GMT_gmt[$g]-$GMT_day[$g]-$GMT_hour[$g]|";}
			$g++;
			}
		if ($DBX) {print "\n";}

		$stmtA = "SELECT call_time_id,call_time_name,call_time_comments,ct_default_start,ct_default_stop,ct_sunday_start,ct_sunday_stop,ct_monday_start,ct_monday_stop,ct_tuesday_start,ct_tuesday_stop,ct_wednesday_start,ct_wednesday_stop,ct_thursday_start,ct_thursday_stop,ct_friday_start,ct_friday_stop,ct_saturday_start,ct_saturday_stop,ct_state_call_times FROM vicidial_call_times where call_time_id='$local_call_time[$i]';";
			if ($DBX) {print "   |$stmtA|\n";}
		$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
		$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
		$sthArows=$sthA->rows;
		$rec_count=0;
		while ($sthArows > $rec_count)
			{
			@aryA = $sthA->fetchrow_array;
			$Gct_default_start =	$aryA[3];
			$Gct_default_stop =		$aryA[4];
			$Gct_sunday_start =		$aryA[5];
			$Gct_sunday_stop =		$aryA[6];
			$Gct_monday_start =		$aryA[7];
			$Gct_monday_stop =		$aryA[8];
			$Gct_tuesday_start =	$aryA[9];
			$Gct_tuesday_stop =		$aryA[10];
			$Gct_wednesday_start =	$aryA[11];
			$Gct_wednesday_stop =	$aryA[12];
			$Gct_thursday_start =	$aryA[13];
			$Gct_thursday_stop =	$aryA[14];
			$Gct_friday_start =		$aryA[15];
			$Gct_friday_stop =		$aryA[16];
			$Gct_saturday_start =	$aryA[17];
			$Gct_saturday_stop =	$aryA[18];
			$Gct_state_call_times = $aryA[19];
			$rec_count++;
			}
		$sthA->finish();


		### BEGIN For lead recycling find out the no-call gap time and begin dial time for today
		$lct_gap=0; # number of seconds from stopping of calling to starting of calling based on local call time
		$lct_begin=0; # hour and minute of the begin time for the local call time
		$lct_end=0; # hour and minute of the end time for the local call time

		$secYESTERDAY = ($secX - (24 * 3600));
			($ksec,$kmin,$khour,$kmday,$kmon,$kyear,$wyesterday,$kyday,$kisdst) = localtime($secYESTERDAY);

		if ($wtoday < 1)	#### Sunday local time
			{
			if (($Gct_sunday_start < 1) && ($Gct_sunday_stop < 1)) {$lct_begin = $Gct_default_start;}
			else {$lct_begin = $Gct_sunday_start;}
			}
		if ($wtoday==1)	#### Monday local time
			{
			if (($Gct_monday_start < 1) && ($Gct_monday_stop < 1)) {$lct_begin = $Gct_default_start;}
			else {$lct_begin = $Gct_monday_start;}
			}
		if ($wtoday==2)	#### Tuesday local time
			{
			if (($Gct_tuesday_start < 1) && ($Gct_tuesday_stop < 1)) {$lct_begin = $Gct_default_start;}
			else {$lct_begin = $Gct_tuesday_start;}
			}
		if ($wtoday==3)	#### Wednesday local time
			{
			if (($Gct_wednesday_start < 1) && ($Gct_wednesday_stop < 1)) {$lct_begin = $Gct_default_start;}
			else {$lct_begin = $Gct_wednesday_start;}
			}
		if ($wtoday==4)	#### Thursday local time
			{
			if (($Gct_thursday_start < 1) && ($Gct_thursday_stop < 1)) {$lct_begin = $Gct_default_start;}
			else {$lct_begin = $Gct_thursday_start;}
			}
		if ($wtoday==5)	#### Friday local time
			{
			if (($Gct_friday_start < 1) && ($Gct_friday_stop < 1)) {$lct_begin = $Gct_default_start;}
			else {$lct_begin = $Gct_friday_start;}
			}
		if ($wtoday==6)	#### Saturday local time
			{
			if (($Gct_saturday_start < 1) && ($Gct_saturday_stop < 1)) {$lct_begin = $Gct_default_start;}
			else {$lct_begin = $Gct_saturday_start;}
			}


		$dayBACKsec=0;
		$weekBACK=0;
		while ( ($lct_end < 1) && ($weekBACK <= 1) )
			{
			if ($wyesterday==6)	#### Saturday local time
				{
				if ($Gct_saturday_start > 2399) {$wyesterday = 0;   $dayBACKsec = ($dayBACKsec + 86400);   if ($DBX) {print "DayBACK: $wyesterday\n";}}
				else
					{
					if (($Gct_saturday_start < 1) && ($Gct_saturday_stop < 1)) {$lct_end = $Gct_default_stop;}
					else {$lct_end = $Gct_saturday_stop;}
					}
				}
			if ($wyesterday==5)	#### Friday local time
				{
				if ($Gct_friday_start > 2399) {$wyesterday = 1;   $dayBACKsec = ($dayBACKsec + 86400);   if ($DBX) {print "DayBACK: $wyesterday\n";}}
				else
					{
					if (($Gct_friday_start < 1) && ($Gct_friday_stop < 1)) {$lct_end = $Gct_default_stop;}
					else {$lct_end = $Gct_friday_stop;}
					}
				}
			if ($wyesterday==4)	#### Thursday local time
				{
				if ($Gct_thursday_start > 2399) {$wyesterday = 2;   $dayBACKsec = ($dayBACKsec + 86400);   if ($DBX) {print "DayBACK: $wyesterday\n";}}
				else
					{
					if (($Gct_thursday_start < 1) && ($Gct_thursday_stop < 1)) {$lct_end = $Gct_default_stop;}
					else {$lct_end = $Gct_thursday_stop;}
					}
				}
			if ($wyesterday==3)	#### Wednesday local time
				{
				if ($Gct_wednesday_start > 2399) {$wyesterday = 3;   $dayBACKsec = ($dayBACKsec + 86400);   if ($DBX) {print "DayBACK: $wyesterday\n";}}
				else
					{
					if (($Gct_wednesday_start < 1) && ($Gct_wednesday_stop < 1)) {$lct_end = $Gct_default_stop;}
					else {$lct_end = $Gct_wednesday_stop;}
					}
				}
			if ($wyesterday==2)	#### Tuesday local time
				{
				if ($Gct_tuesday_start > 2399) {$wyesterday = 4;   $dayBACKsec = ($dayBACKsec + 86400);   if ($DBX) {print "DayBACK: $wyesterday\n";}}
				else
					{
					if (($Gct_tuesday_start < 1) && ($Gct_tuesday_stop < 1)) {$lct_end = $Gct_default_stop;}
					else {$lct_end = $Gct_tuesday_stop;}
					}
				}
			if ($wyesterday==1)	#### Monday local time
				{
				if ($Gct_monday_start > 2399) {$wyesterday = 5;   $dayBACKsec = ($dayBACKsec + 86400);   if ($DBX) {print "DayBACK: $wyesterday\n";}}
				else
					{
					if (($Gct_monday_start < 1) && ($Gct_monday_stop < 1)) {$lct_end = $Gct_default_stop;}
					else {$lct_end = $Gct_monday_stop;}
					}
				}
			if ($wyesterday==0)	#### Sunday local time
				{
				if ($Gct_sunday_start > 2399) {$wyesterday = 6;   $dayBACKsec = ($dayBACKsec + 86400);   if ($DBX) {print "DayBACK: $wyesterday\n";}}
				else
					{
					if (($Gct_sunday_start < 1) && ($Gct_sunday_stop < 1)) {$lct_end = $Gct_default_stop;}
					else {$lct_end = $Gct_sunday_stop;}
					}
				}
			$weekBACK++;
			}

		$lct_end = sprintf("%04d", $lct_end);
		$lct_end_hour = substr($lct_end, 0, 2);
		$lct_end_min = substr($lct_end, 2, 2);
		$lct_begin = sprintf("%04d", $lct_begin);
		$lct_begin_hour = substr($lct_begin, 0, 2);
		$lct_begin_min = substr($lct_begin, 2, 2);

		$lct_gap = ( ( ( ( ( (24 - $lct_end_hour) + $lct_begin_hour) * 3600) + ($lct_begin_min * 60) ) - ($lct_end_min * 60) ) + $dayBACKsec);

		if ($DBX) {print "LocalCallTime No-Call Gap: |$lct_gap|$lct_end($lct_end_hour $lct_end_min)|$lct_begin($lct_begin_hour $lct_begin_min)|$wtoday|$wyesterday|\n";}

		### END For lead recycling find out the no-call gap time and begin dial time for today


		$ct_states = '';
		$ct_state_gmt_SQL = '';
		$del_state_gmt_SQL = '';
		$ct_srs=0;
		$b=0;
		if (length($Gct_state_call_times)>2)
			{
			@state_rules = split(/\|/,$Gct_state_call_times);
			$ct_srs = ($#state_rules - 0);
			}
		while($ct_srs >= $b)
			{
			if (length($state_rules[$b])>1)
				{
				$stmtA = "SELECT state_call_time_id,state_call_time_state,state_call_time_name,state_call_time_comments,sct_default_start,sct_default_stop,sct_sunday_start,sct_sunday_stop,sct_monday_start,sct_monday_stop,sct_tuesday_start,sct_tuesday_stop,sct_wednesday_start,sct_wednesday_stop,sct_thursday_start,sct_thursday_stop,sct_friday_start,sct_friday_stop,sct_saturday_start,sct_saturday_stop from vicidial_state_call_times where state_call_time_id='$state_rules[$b]';";
				if ($DBX) {print "   |$stmtA|\n";}
				$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
				$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
				$sthArows=$sthA->rows;
				$rec_count=0;
				while ($sthArows > $rec_count)
					{
					@aryA = $sthA->fetchrow_array;
					$Gstate_call_time_id =		$aryA[0];
					$Gstate_call_time_state =	$aryA[1];
					$Gsct_default_start =		$aryA[4];
					$Gsct_default_stop =		$aryA[5];
					$Gsct_sunday_start =		$aryA[6];
					$Gsct_sunday_stop =			$aryA[7];
					$Gsct_monday_start =		$aryA[8];
					$Gsct_monday_stop =			$aryA[9];
					$Gsct_tuesday_start =		$aryA[10];
					$Gsct_tuesday_stop =		$aryA[11];
					$Gsct_wednesday_start =		$aryA[12];
					$Gsct_wednesday_stop =		$aryA[13];
					$Gsct_thursday_start =		$aryA[14];
					$Gsct_thursday_stop =		$aryA[15];
					$Gsct_friday_start =		$aryA[16];
					$Gsct_friday_stop =			$aryA[17];
					$Gsct_saturday_start =		$aryA[18];
					$Gsct_saturday_stop =		$aryA[19];
					$ct_states .="'$Gstate_call_time_state',";
					$rec_count++;
					}
				$sthA->finish();

				$r=0;
				$state_gmt='';
				$del_state_gmt='';
				while($r < $g)
					{
					if ($GMT_day[$r]==0)	#### Sunday local time
						{
						if (($Gsct_sunday_start==0) && ($Gsct_sunday_stop==0))
							{
							if ( ($GMT_hour[$r]>=$Gsct_default_start) && ($GMT_hour[$r]<$Gsct_default_stop) )
								{$state_gmt.="'$GMT_gmt[$r]',";}
							else
								{$del_state_gmt.="'$GMT_gmt[$r]',";}
							}
						else
							{
							if ( ($GMT_hour[$r]>=$Gsct_sunday_start) && ($GMT_hour[$r]<$Gsct_sunday_stop) )
								{$state_gmt.="'$GMT_gmt[$r]',";}
							else
								{$del_state_gmt.="'$GMT_gmt[$r]',";}
							}
						}
					if ($GMT_day[$r]==1)	#### Monday local time
						{
						if (($Gsct_monday_start==0) && ($Gsct_monday_stop==0))
							{
							if ( ($GMT_hour[$r]>=$Gsct_default_start) && ($GMT_hour[$r]<$Gsct_default_stop) )
								{$state_gmt.="'$GMT_gmt[$r]',";}
							else
								{$del_state_gmt.="'$GMT_gmt[$r]',";}
							}
						else
							{
							if ( ($GMT_hour[$r]>=$Gsct_monday_start) && ($GMT_hour[$r]<$Gsct_monday_stop) )
								{$state_gmt.="'$GMT_gmt[$r]',";}
							else
								{$del_state_gmt.="'$GMT_gmt[$r]',";}
							}
						}
					if ($GMT_day[$r]==2)	#### Tuesday local time
						{
						if (($Gsct_tuesday_start==0) && ($Gsct_tuesday_stop==0))
							{
							if ( ($GMT_hour[$r]>=$Gsct_default_start) && ($GMT_hour[$r]<$Gsct_default_stop) )
								{$state_gmt.="'$GMT_gmt[$r]',";}
							else
								{$del_state_gmt.="'$GMT_gmt[$r]',";}
							}
						else
							{
							if ( ($GMT_hour[$r]>=$Gsct_tuesday_start) && ($GMT_hour[$r]<$Gsct_tuesday_stop) )
								{$state_gmt.="'$GMT_gmt[$r]',";}
							else
								{$del_state_gmt.="'$GMT_gmt[$r]',";}
							}
						}
					if ($GMT_day[$r]==3)	#### Wednesday local time
						{
						if (($Gsct_wednesday_start==0) && ($Gsct_wednesday_stop==0))
							{
							if ( ($GMT_hour[$r]>=$Gsct_default_start) && ($GMT_hour[$r]<$Gsct_default_stop) )
								{$state_gmt.="'$GMT_gmt[$r]',";}
							else
								{$del_state_gmt.="'$GMT_gmt[$r]',";}
							}
						else
							{
							if ( ($GMT_hour[$r]>=$Gsct_wednesday_start) && ($GMT_hour[$r]<$Gsct_wednesday_stop) )
								{$state_gmt.="'$GMT_gmt[$r]',";}
							else
								{$del_state_gmt.="'$GMT_gmt[$r]',";}
							}
						}
					if ($GMT_day[$r]==4)	#### Thursday local time
						{
						if (($Gsct_thursday_start==0) && ($Gsct_thursday_stop==0))
							{
							if ( ($GMT_hour[$r]>=$Gsct_default_start) && ($GMT_hour[$r]<$Gsct_default_stop) )
								{$state_gmt.="'$GMT_gmt[$r]',";}
							else
								{$del_state_gmt.="'$GMT_gmt[$r]',";}
							}
						else
							{
							if ( ($GMT_hour[$r]>=$Gsct_thursday_start) && ($GMT_hour[$r]<$Gsct_thursday_stop) )
								{$state_gmt.="'$GMT_gmt[$r]',";}
							else
								{$del_state_gmt.="'$GMT_gmt[$r]',";}
							}
						}
					if ($GMT_day[$r]==5)	#### Friday local time
						{
						if (($Gsct_friday_start==0) && ($Gsct_friday_stop==0))
							{
							if ( ($GMT_hour[$r]>=$Gsct_default_start) && ($GMT_hour[$r]<$Gsct_default_stop) )
								{$state_gmt.="'$GMT_gmt[$r]',";}
							else
								{$del_state_gmt.="'$GMT_gmt[$r]',";}
							}
						else
							{
							if ( ($GMT_hour[$r]>=$Gsct_friday_start) && ($GMT_hour[$r]<$Gsct_friday_stop) )
								{$state_gmt.="'$GMT_gmt[$r]',";}
							else
								{$del_state_gmt.="'$GMT_gmt[$r]',";}
							}
						}
					if ($GMT_day[$r]==6)	#### Saturday local time
						{
						if (($Gsct_saturday_start==0) && ($Gsct_saturday_stop==0))
							{
							if ( ($GMT_hour[$r]>=$Gsct_default_start) && ($GMT_hour[$r]<$Gsct_default_stop) )
								{$state_gmt.="'$GMT_gmt[$r]',";}
							else
								{$del_state_gmt.="'$GMT_gmt[$r]',";}
							}
						else
							{
							if ( ($GMT_hour[$r]>=$Gsct_saturday_start) && ($GMT_hour[$r]<$Gsct_saturday_stop) )
								{$state_gmt.="'$GMT_gmt[$r]',";}
							else
								{$del_state_gmt.="'$GMT_gmt[$r]',";}
							}
						}
					$r++;
					}
				$state_gmt = "$state_gmt'99'";
				$del_state_gmt = "$del_state_gmt'99'";
				$ct_state_gmt_SQL .= "or (state='$Gstate_call_time_state' && gmt_offset_now IN($state_gmt)) ";
				$del_state_gmt_SQL .= "or (state='$Gstate_call_time_state' && gmt_offset_now IN($del_state_gmt)) ";
				}

			$b++;
			}
		if (length($ct_states)>2)
			{
			$ct_states =~ s/,$//gi;
			$ct_statesSQL = "and state NOT IN($ct_states)";
			}
		else
			{
			$ct_statesSQL = "";
			}

		$r=0;
		@default_gmt_ARY=@MT;
		$dgA=0;
		$default_gmt='';
		$del_default_gmt='';
		while($r < $g)
			{
			if ($GMT_day[$r]==0)	#### Sunday local time
				{
				if (($Gct_sunday_start==0) && ($Gct_sunday_stop==0))
					{
					if ( ($GMT_hour[$r]>=$Gct_default_start) && ($GMT_hour[$r]<$Gct_default_stop) )
						{$default_gmt.="'$GMT_gmt[$r]',";   $default_gmt_ARY[$dgA] = "$GMT_gmt[$r]";   $dgA++;}
					else
						{$del_default_gmt.="'$GMT_gmt[$r]',";}
					}
				else
					{
					if ( ($GMT_hour[$r]>=$Gct_sunday_start) && ($GMT_hour[$r]<$Gct_sunday_stop) )
						{$default_gmt.="'$GMT_gmt[$r]',";   $default_gmt_ARY[$dgA] = "$GMT_gmt[$r]";   $dgA++;}
					else
						{$del_default_gmt.="'$GMT_gmt[$r]',";}
					}
				}
			if ($GMT_day[$r]==1)	#### Monday local time
				{
				if (($Gct_monday_start==0) && ($Gct_monday_stop==0))
					{
					if ( ($GMT_hour[$r]>=$Gct_default_start) && ($GMT_hour[$r]<$Gct_default_stop) )
						{$default_gmt.="'$GMT_gmt[$r]',";   $default_gmt_ARY[$dgA] = "$GMT_gmt[$r]";   $dgA++;}
					else
						{$del_default_gmt.="'$GMT_gmt[$r]',";}
					}
				else
					{
					if ( ($GMT_hour[$r]>=$Gct_monday_start) && ($GMT_hour[$r]<$Gct_monday_stop) )
						{$default_gmt.="'$GMT_gmt[$r]',";   $default_gmt_ARY[$dgA] = "$GMT_gmt[$r]";   $dgA++;}
					else
						{$del_default_gmt.="'$GMT_gmt[$r]',";}
					}
				}
			if ($GMT_day[$r]==2)	#### Tuesday local time
				{
				if (($Gct_tuesday_start==0) && ($Gct_tuesday_stop==0))
					{
					if ( ($GMT_hour[$r]>=$Gct_default_start) && ($GMT_hour[$r]<$Gct_default_stop) )
						{$default_gmt.="'$GMT_gmt[$r]',";   $default_gmt_ARY[$dgA] = "$GMT_gmt[$r]";   $dgA++;}
					else
						{$del_default_gmt.="'$GMT_gmt[$r]',";}
					}
				else
					{
					if ( ($GMT_hour[$r]>=$Gct_tuesday_start) && ($GMT_hour[$r]<$Gct_tuesday_stop) )
						{$default_gmt.="'$GMT_gmt[$r]',";   $default_gmt_ARY[$dgA] = "$GMT_gmt[$r]";   $dgA++;}
					else
						{$del_default_gmt.="'$GMT_gmt[$r]',";}
					}
				}
			if ($GMT_day[$r]==3)	#### Wednesday local time
				{
				if (($Gct_wednesday_start==0) && ($Gct_wednesday_stop==0))
					{
					if ( ($GMT_hour[$r]>=$Gct_default_start) && ($GMT_hour[$r]<$Gct_default_stop) )
						{$default_gmt.="'$GMT_gmt[$r]',";   $default_gmt_ARY[$dgA] = "$GMT_gmt[$r]";   $dgA++;}
					else
						{$del_default_gmt.="'$GMT_gmt[$r]',";}
					}
				else
					{
					if ( ($GMT_hour[$r]>=$Gct_wednesday_start) && ($GMT_hour[$r]<$Gct_wednesday_stop) )
						{$default_gmt.="'$GMT_gmt[$r]',";   $default_gmt_ARY[$dgA] = "$GMT_gmt[$r]";   $dgA++;}
					else
						{$del_default_gmt.="'$GMT_gmt[$r]',";}
					}
				}
			if ($GMT_day[$r]==4)	#### Thursday local time
				{
				if (($Gct_thursday_start==0) && ($Gct_thursday_stop==0))
					{
					if ( ($GMT_hour[$r]>=$Gct_default_start) && ($GMT_hour[$r]<$Gct_default_stop) )
						{$default_gmt.="'$GMT_gmt[$r]',";   $default_gmt_ARY[$dgA] = "$GMT_gmt[$r]";   $dgA++;}
					else
						{$del_default_gmt.="'$GMT_gmt[$r]',";}
					}
				else
					{
					if ( ($GMT_hour[$r]>=$Gct_thursday_start) && ($GMT_hour[$r]<$Gct_thursday_stop) )
						{$default_gmt.="'$GMT_gmt[$r]',";   $default_gmt_ARY[$dgA] = "$GMT_gmt[$r]";   $dgA++;}
					else
						{$del_default_gmt.="'$GMT_gmt[$r]',";}
					}
				}
			if ($GMT_day[$r]==5)	#### Friday local time
				{
				if (($Gct_friday_start==0) && ($Gct_friday_stop==0))
					{
					if ( ($GMT_hour[$r]>=$Gct_default_start) && ($GMT_hour[$r]<$Gct_default_stop) )
						{$default_gmt.="'$GMT_gmt[$r]',";   $default_gmt_ARY[$dgA] = "$GMT_gmt[$r]";   $dgA++;}
					else
						{$del_default_gmt.="'$GMT_gmt[$r]',";}
					}
				else
					{
					if ( ($GMT_hour[$r]>=$Gct_friday_start) && ($GMT_hour[$r]<$Gct_friday_stop) )
						{$default_gmt.="'$GMT_gmt[$r]',";   $default_gmt_ARY[$dgA] = "$GMT_gmt[$r]";   $dgA++;}
					else
						{$del_default_gmt.="'$GMT_gmt[$r]',";}
					}
				}
			if ($GMT_day[$r]==6)	#### Saturday local time
				{
				if (($Gct_saturday_start==0) && ($Gct_saturday_stop==0))
					{
					if ( ($GMT_hour[$r]>=$Gct_default_start) && ($GMT_hour[$r]<$Gct_default_stop) )
						{$default_gmt.="'$GMT_gmt[$r]',";   $default_gmt_ARY[$dgA] = "$GMT_gmt[$r]";   $dgA++;}
					else
						{$del_default_gmt.="'$GMT_gmt[$r]',";}
					}
				else
					{
					if ( ($GMT_hour[$r]>=$Gct_saturday_start) && ($GMT_hour[$r]<$Gct_saturday_stop) )
						{$default_gmt.="'$GMT_gmt[$r]',";   $default_gmt_ARY[$dgA] = "$GMT_gmt[$r]";   $dgA++;}
					else
						{$del_default_gmt.="'$GMT_gmt[$r]',";}
					}
				}
			$r++;
			}

		$default_gmt = "$default_gmt'99'";
		$del_default_gmt = "$del_default_gmt'99'";
		$all_gmtSQL[$i] = "(gmt_offset_now IN($default_gmt) $ct_statesSQL) $ct_state_gmt_SQL";
		$del_gmtSQL[$i] = "(gmt_offset_now IN($del_default_gmt) $ct_statesSQL) $del_state_gmt_SQL";

		##### END calculate what gmt_offset_now values are within the allowed local_call_time setting ###

		##### BEGIN lead recycling parsing and prep ###

		$stmtA = "SELECT recycle_id,campaign_id,status,attempt_delay,attempt_maximum,active from vicidial_lead_recycle where campaign_id='$campaign_id[$i]' and active='Y';";
		$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
		$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
		$sthArows=$sthA->rows;
		@recycle_status=@MT;
		@recycle_delay=@MT;
		@recycle_maximum=@MT;
		@RSQLdate=@MT;
		$r_ct=0;
		$rec_ct[$i]=0;
		while ($sthArows > $r_ct)
			{
			@aryA = $sthA->fetchrow_array;
			$recycle_status[$r_ct] =	 $aryA[2];
			$recycle_delay[$r_ct] =		 $aryA[3];
			$recycle_maximum[$r_ct] =	 $aryA[4];
			$r_ct++;
			$rec_ct[$i]++;
			}
		$sthA->finish();

		if ($rec_ct[$i] > 0)
			{
			$rc=0;
			$recycle_SQL[$i] = "( ";
			while($rc < $rec_ct[$i])
				{
				$Y=1;
				$recycle_Y = "'Y'";
				while ($Y < $recycle_maximum[$rc])
					{
					$recycle_Y .= ",'Y$Y'";
					$Y++;
					}

				if ($rc > 0) {$recycle_SQL[$i] .= " or ";}

				$recycle_SQL[$i] .= "( (called_since_last_reset IN($recycle_Y)) and (status='$recycle_status[$rc]') and (";

				$dgA=0;
				foreach(@default_gmt_ARY)
					{
					$secX = time();
					$LLCT_DATE_offset = ($LOCAL_GMT_OFF - $default_gmt_ARY[$dgA]);
					$LLCT_DATE_offset_epoch = ( $secX - ($LLCT_DATE_offset * 3600) );
					$Rtarget = ($LLCT_DATE_offset_epoch - $recycle_delay[$rc]);
					($Rsec,$Rmin,$Rhour,$Rmday,$Rmon,$Ryear,$Rwday,$Ryday,$Risdst) = localtime($Rtarget);
					$Ryear = ($Ryear + 1900);
					$Rmon++;
					if ($Rmon < 10) {$Rmon = "0$Rmon";}
					if ($Rmday < 10) {$Rmday = "0$Rmday";}
					if ($Rhour < 10) {$Rhour = "0$Rhour";}
					if ($Rmin < 10) {$Rmin = "0$Rmin";}
					if ($Rsec < 10) {$Rsec = "0$Rsec";}
					$Rhourmin = "$Rhour$Rmin";
					if ( ($Rhourmin < $lct_begin) || ($Rhourmin > $lct_end) ) 
						{
						$RGtarget = ($Rtarget - $lct_gap);
						($Rsec,$Rmin,$Rhour,$Rmday,$Rmon,$Ryear,$Rwday,$Ryday,$Risdst) = localtime($RGtarget);
						$Ryear = ($Ryear + 1900);
						$Rmon++;
						if ($Rmon < 10) {$Rmon = "0$Rmon";}
						if ($Rmday < 10) {$Rmday = "0$Rmday";}
						if ($Rhour < 10) {$Rhour = "0$Rhour";}
						if ($Rmin < 10) {$Rmin = "0$Rmin";}
						if ($Rsec < 10) {$Rsec = "0$Rsec";}
						if ($DBX) {print "RECYCLE DELAY GAP: |$campaign_id[$i]|$Rhourmin|$RGtarget|Rtarget|($recycle_delay[$rc] $lct_gap)\n";}
						}
					$RSQLdate[$rc] = "$Ryear-$Rmon-$Rmday $Rhour:$Rmin:$Rsec";

					if ($dgA > 0) {$recycle_SQL[$i] .= " or ";}

					$recycle_SQL[$i] .= "( (gmt_offset_now='$default_gmt_ARY[$dgA]') and (last_local_call_time < \"$RSQLdate[$rc]\") )";

					$dgA++;
					}
				if ($DBX) {print "RECYCLE: |$campaign_id[$i]|$recycle_status[$rc]|$recycle_delay[$rc]|$recycle_maximum[$rc]|$RSQLdate[$rc]|\n";}

				$recycle_SQL[$i] .= " ) )";

				$rc++;
				}

			$recycle_SQL[$i] .= " )";

			if ($DBX) {print "RECYCLE SQL: |$recycle_SQL[$i]|\n";}
			}
		##### END lead recycling parsing and prep ###

		if ($DB) {print "Starting hopper run for $campaign_id[$i] campaign- GMT: $local_call_time[$i]   HOPPER: $hopper_level[$i]   ORDER: $lead_order[$i]|$lead_order_randomize[$i]|$lead_order_secondary[$i]\n";}

		### Delete the DONE leads if there are any
		$stmtA = "DELETE from $vicidial_hopper where campaign_id='$campaign_id[$i]' and status IN('DONE');";
		$affected_rows = $dbhA->do($stmtA);
		if ($DB) {print "     hopper DONE cleared:  $affected_rows\n";}
		if ($DBX) {print "     |$stmtA|\n";}

		### Delete the leads that are out of GMT time range if there are any
		$stmtA = "DELETE from $vicidial_hopper where campaign_id='$campaign_id[$i]' and ($del_gmtSQL[$i]);";
		$affected_rows = $dbhA->do($stmtA);
		if ($DB) {print "     hopper GMT BAD cleared:  $affected_rows\n";}
		if ($DBX) {print "     |$stmtA|\n";}

		### Find out how many leads are in the hopper from a specific campaign
		$hopper_ready_count=0;
		$stmtA = "SELECT count(*) from $vicidial_hopper where campaign_id='$campaign_id[$i]' and status='READY';";
		$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
		$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
		$sthArows=$sthA->rows;
		if ($sthArows > 0)
			{
			@aryA = $sthA->fetchrow_array;
			$hopper_ready_count = $aryA[0];
			if ($DB) {print "     hopper READY count:   $hopper_ready_count\n";}
			if ($DBX) {print "     |$stmtA|\n";}
			}
		$sthA->finish();
		$event_string = "|$campaign_id[$i]|$hopper_level[$i]|$hopper_ready_count|$local_call_time[$i]||";
		&event_logger;

		$active_listSQL = "and active='Y'";
		if ( ($list_order_mix[$i] !~ /DISABLED/) && ($allow_inactive_list_leads > 0) )
			{$active_listSQL = '';}
	
		### Get list of the lists in the campaign ###
		$stmtA = "SELECT list_id FROM vicidial_lists where campaign_id='$campaign_id[$i]' $active_listSQL;";
		$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
		$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
		$sthArows=$sthA->rows;
		$rec_countLISTS=0;
		$camp_lists[$i] = '';
		while ($sthArows > $rec_countLISTS)
			{
			@aryA = $sthA->fetchrow_array;
			$camp_lists[$i] .= "'$aryA[0]',";
			$rec_countLISTS++;
			}
		$sthA->finish();
		if (length($camp_lists[$i])<3) {$camp_lists[$i]="'999876543210'";}
		   else {chop($camp_lists[$i]);}

		if ($DB) {print "     campaign lists count: $rec_countLISTS | $camp_lists[$i]\n";}
		if ($DBX) {print "     |$stmtA|\n";}

		if ($list_order_mix[$i] !~ /DISABLED/)
			{
			$stmtA = "SELECT vcl_id,vcl_name,list_mix_container,mix_method FROM vicidial_campaigns_list_mix where campaign_id='$campaign_id[$i]' and status='ACTIVE';";
			$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
			$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
			$sthArows=$sthA->rows;
			$rec_count=0;
			while ($sthArows > $rec_count)
				{
				@aryA = $sthA->fetchrow_array;
				$vcl_id[$i] =				$aryA[0];
				$vcl_name[$i] =				$aryA[1];
				$list_mix_container[$i] =	$aryA[2];
				$mix_method[$i] =			$aryA[3];
				$rec_count++;
				}
			$sthA->finish();

			@list_mixARY=@MT;
			$list_mix_dialableSQL='';
			@list_mixARY = split(/:/,$list_mix_container[$i]);
			$x=0;
			foreach(@list_mixARY)
				{
				if ($x > 0) {$list_mix_dialableSQL .= " or ";}
				@list_mix_stepARY = split(/\|/,$list_mixARY[$x]);
				$list_mix_stepARY[3] =~ s/ /\',\'/gi;
				$list_mix_stepARY[3] =~ s/^\',|,\'-//gi;
				if ($list_mix_stepARY[3] !~ /\'$/)
					{$list_mix_stepARY[3] = "$list_mix_stepARY[3]'";}
				if ($DBX) {print "     LM $x ++$list_mixARY[$x]++ |$list_mix_stepARY[0]|$list_mix_stepARY[2]|$list_mix_stepARY[3]|\n";}
				$list_mix_dialableSQL .= "(list_id='$list_mix_stepARY[0]' and status IN($list_mix_stepARY[3]))";

				$x++;
				}

			if ($DB) {print "     campaign mix: $list_order_mix[$i] |$vcl_id[$i] - $vcl_name[$i]|$list_mix_container[$i]|$x|$mix_method[$i]|\n";}
			}

		if ( ($lead_filter_id[$i] !~ /NONE/) && (length($lead_filter_id[$i])>0) )
			{
			### Get SQL of lead filter for the campaign ###
			$stmtA = "SELECT lead_filter_sql FROM vicidial_lead_filters where lead_filter_id='$lead_filter_id[$i]';";
			$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
			$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
			$sthArows=$sthA->rows;
			$rec_count=0;
			while ($sthArows > $rec_count)
				{
				@aryA = $sthA->fetchrow_array;
				$lead_filter_sql[$i] = $aryA[0];
				$lead_filter_sql[$i] =~ s/\\//gi;
				$rec_count++;
				}
			$sthA->finish();
			$lead_filter_sql[$i] =~ s/^and|and$|^or|or$|^ and|and $|^ or|or $//gi;
			$lead_filter_sql[$i] = "and $lead_filter_sql[$i]";
			if ($DB) {print "     lead filter $lead_filter_id[$i] defined for $campaign_id[$i]\n";}
			if ($DBX) {print "     |$lead_filter_sql[$i]|\n";}
			}
		else
			{
			$lead_filter_sql[$i] = '';
			if ($DB) {print "     no lead filter defined for campaign: $campaign_id[$i]\n";}
			if ($DBX) {print "     |$lead_filter_id[$i]|\n";}
			}

		$DLTsql[$i]='';
		if ($drop_lockout_time[$i] > 0)
			{
			$DLseconds[$i] = ($drop_lockout_time[$i] * 3600);
			$DLTsql[$i] = "and ( ( (status IN('DROP','XDROP')) and (last_local_call_time < CONCAT(DATE_ADD(NOW(), INTERVAL -$DLseconds[$i] SECOND),' ',CURTIME()) ) ) or (status NOT IN('DROP','XDROP')) )";
			if ($DB) {print "     drop lockout time $drop_lockout_time[$i]($DLseconds[$i]) defined for $campaign_id[$i]\n";}
			if ($DBX) {print "     |$DLTsql[$i]|\n";}
			}
		
		##### Get count of leads that are dialable #####
		if ($list_order_mix[$i] =~ /DISABLED/)
			{
			$stmtA = "SELECT count(*) FROM vicidial_list where called_since_last_reset='N' and status IN($STATUSsql[$i]) and list_id IN($camp_lists[$i]) and ($all_gmtSQL[$i]) $lead_filter_sql[$i] $DLTsql[$i];";
			}
		else
			{
			if (length($list_mix_dialableSQL)<3) {$list_mix_dialableSQL="called_count < 0";}
			$stmtA = "SELECT count(*) FROM vicidial_list where called_since_last_reset='N' and ($list_mix_dialableSQL) and ($all_gmtSQL[$i]) $lead_filter_sql[$i] $DLTsql[$i];";
			}
			if ($DBX) {print "     |$stmtA|\n";}
		$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
		$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
		$sthArows=$sthA->rows;
		if ($sthArows > 0)
			{
			@aryA = $sthA->fetchrow_array;
			$campaign_leads_to_call[$i] = $aryA[0];
			if ($DB) {print "     leads to call count:  $campaign_leads_to_call[$i]\n";}
			if ($DBX) {print "     |$stmtA|\n";}
			}
		$sthA->finish();

		if ( ($lead_order[$i] =~ / 2nd NEW$| 3rd NEW$| 4th NEW$| 5th NEW$| 6th NEW$/) && ($list_order_mix[$i] =~ /DISABLED/) )
			{
			$stmtA = "SELECT count(*) FROM vicidial_list where called_since_last_reset='N' and status IN('NEW') and list_id IN($camp_lists[$i]) and ($all_gmtSQL[$i]) $lead_filter_sql[$i] $DLTsql[$i];";
			$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
			$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
			$sthArows=$sthA->rows;
			if ($sthArows > 0)
				{
				@aryA = $sthA->fetchrow_array;
				$NEW_campaign_leads_to_call[$i] = $aryA[0];
				if ($DB) {print "     NEW leads to call count:  $NEW_campaign_leads_to_call[$i]\n";}
				if ($DBX) {print "     |$stmtA|\n";}
				}
			$sthA->finish();
			}

		##### IF no NEW leads to be called, error out of this campaign #####
		if ( ($lead_order[$i] =~ / 2nd NEW$| 3rd NEW$| 4th NEW$| 5th NEW$| 6th NEW$/) && ($NEW_campaign_leads_to_call[$i] > 0) && ($list_order_mix[$i] =~ /DISABLED/) ) {$GOOD=1;}
		else
			{
			if ($lead_order[$i] !~ / 2nd NEW$| 3rd NEW$| 4th NEW$| 5th NEW$| 6th NEW$/)
				{
				if ($DB) {print "     NO SHUFFLE-NEW-LEADS INTO HOPPER DEFINED FOR LEAD ORDER\n";}
				}
			else
				{
				if ($DB) {print "     ERROR CANNOT ADD ANY NEW LEADS TO HOPPER\n";}
				}
			}

		##### UPDATE the leads to be dialed for this campaign #####
		if ( ($campaign_leads_to_call[$i] < 1) && ($rec_ct[$i] < 1) )
			{
			if ($DB) {print "     NO DIALABLE LEADS FOR THIS CAMPAIGN\n";}
			if ($VCSdialable_leads[$i] > 0)
				{
				$stmtA = "UPDATE vicidial_campaign_stats SET dialable_leads='0' where campaign_id='$campaign_id[$i]';";
				$affected_rows = $dbhA->do($stmtA);
				if ($DBX) {print "CAMPAIGN STATS: $affected_rows|$stmtA|\n";}
				}
			}
		else
			{
			if ($VCSdialable_leads[$i] != $campaign_leads_to_call[$i])
				{
				$stmtA = "UPDATE vicidial_campaign_stats SET dialable_leads='$campaign_leads_to_call[$i]' where campaign_id='$campaign_id[$i]';";
				$affected_rows = $dbhA->do($stmtA);
				if ($DBX) {print "CAMPAIGN STATS: $affected_rows|$stmtA|\n";}
				}
			}
		$ENDoutput .= "$campaign_id[$i]     $campaign_leads_to_call[$i]     $hopper_level[$i]     $hopper_ready_count\n";

		##### IF hopper level is below set minimum, then try to add more leads #####
		if ($hopper_ready_count < $hopper_level[$i])
			{
			if ($DB) {print "     hopper too low ($hopper_ready_count|$hopper_level[$i]) starting hopper dump\n";}

			##### IF no leads to be called, error out of this campaign #####
			if ( ($campaign_leads_to_call[$i] < 1) && ($rec_ct[$i] < 1) )
				{
				if ($DB) {print "     ERROR CANNOT ADD ANY LEADS TO HOPPER\n";}
				}
			else
				{
				if ($DB) {print "     Getting Leads to add to hopper\n";}
				### grab leads already in hopper so we don't duplicate
				$stmtA = "SELECT lead_id FROM $vicidial_hopper where campaign_id='$campaign_id[$i]';";
				if ($DBX) {print "     |$stmtA|\n";}
				$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
				$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
				$sthArows=$sthA->rows;
				$lead_id_lists = '';
				$rec_countLISTS=0;
				while ($sthArows > $rec_countLISTS)
					{
					@aryA = $sthA->fetchrow_array;
					$lead_id_lists .= "'$aryA[0]',";
					$rec_countLISTS++;
					}
				$sthA->finish();
				$lead_id_lists .= "'0'";
				$order_stmt='';
				$NEW_count = 0;
				$NEW_level = 0;
				$OTHER_level = $hopper_level[$i];

				if ($lead_order_randomize[$i] =~ /Y/) {$last_order = "RAND()";}
				else 
					{
					$last_order = "lead_id asc";
					if ($lead_order_secondary[$i] =~ /LEAD_ASCEND/) {$last_order = "lead_id asc";}
					if ($lead_order_secondary[$i] =~ /LEAD_DESCEND/) {$last_order = "lead_id desc";}
					if ($lead_order_secondary[$i] =~ /CALLTIME_ASCEND/) {$last_order = "last_local_call_time asc";}
					if ($lead_order_secondary[$i] =~ /CALLTIME_DESCEND/) {$last_order = "last_local_call_time desc";}
					}

				if ($lead_order[$i] =~ /^DOWN/) {$order_stmt = "order by lead_id asc";}
				if ($lead_order[$i] =~ /^UP/) {$order_stmt = "order by lead_id desc";}
				if ($lead_order[$i] =~ /^UP LAST NAME/) {$order_stmt = "order by last_name desc, $last_order";}
				if ($lead_order[$i] =~ /^DOWN LAST NAME/) {$order_stmt = "order by last_name, $last_order";}
				if ($lead_order[$i] =~ /^UP PHONE/) {$order_stmt = "order by phone_number desc, $last_order";}
				if ($lead_order[$i] =~ /^DOWN PHONE/) {$order_stmt = "order by phone_number, $last_order";}
				if ($lead_order[$i] =~ /^UP COUNT/) {$order_stmt = "order by called_count desc, $last_order";}
				if ($lead_order[$i] =~ /^DOWN COUNT/) {$order_stmt = "order by called_count, $last_order";}
				if ($lead_order[$i] =~ /^UP LAST CALL TIME/) {$order_stmt = "order by last_local_call_time desc, $last_order";}
				if ($lead_order[$i] =~ /^DOWN LAST CALL TIME/) {$order_stmt = "order by last_local_call_time, $last_order";}
				if ($lead_order[$i] =~ /^RANDOM/) {$order_stmt = "order by RAND()";}
				if ($lead_order[$i] =~ /^UP RANK/) {$order_stmt = "order by rank desc, $last_order";}
				if ($lead_order[$i] =~ /^DOWN RANK/) {$order_stmt = "order by rank, $last_order";}
				if ($lead_order[$i] =~ /^UP OWNER/) {$order_stmt = "order by owner desc, $last_order";}
				if ($lead_order[$i] =~ /^DOWN OWNER/) {$order_stmt = "order by owner, $last_order";}
				if ($lead_order[$i] =~ /^UP TIMEZONE/) {$order_stmt = "order by gmt_offset_now desc, $last_order";}
				if ($lead_order[$i] =~ /^DOWN TIMEZONE/) {$order_stmt = "order by gmt_offset_now, $last_order";}
				if ($lead_order[$i] =~ / 2nd NEW$/) {$NEW_count = 2;}
				if ($lead_order[$i] =~ / 3rd NEW$/) {$NEW_count = 3;}
				if ($lead_order[$i] =~ / 4th NEW$/) {$NEW_count = 4;}
				if ($lead_order[$i] =~ / 5th NEW$/) {$NEW_count = 5;}
				if ($lead_order[$i] =~ / 6th NEW$/) {$NEW_count = 6;}


			### BEGIN recycle grab leads ###
				$REC_rec_countLEADS=0;
				@REC_leads_to_hopper=@MT;
				@REC_lists_to_hopper=@MT;
				@REC_phone_to_hopper=@MT;
				@REC_gmt_to_hopper=@MT;
				@REC_state_to_hopper=@MT;
				@REC_status_to_hopper=@MT;
				@REC_modify_to_hopper=@MT;
				@REC_user_to_hopper=@MT;
				@REC_source_to_hopper=@MT;
				if ($rec_ct[$i] > 0)
					{
					if ($DB) {print "     looking for RECYCLE leads, maximum of $hopper_level[$i]\n";}

					$stmtA = "SELECT lead_id,list_id,gmt_offset_now,phone_number,state,status,modify_date,user FROM vicidial_list where $recycle_SQL[$i] and list_id IN($camp_lists[$i]) and lead_id NOT IN($lead_id_lists) and ($all_gmtSQL[$i]) $lead_filter_sql[$i] $DLTsql[$i] $order_stmt limit $hopper_level[$i];";
					if ($DBX) {print "     |$stmtA|\n";}
					$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
					$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
					$sthArows=$sthA->rows;
					if ($DB) {print "     RECYCLE leads to call count:  $sthArows\n";}
					if ($DBX) {print "     |$stmtA|\n";}
					while ($sthArows > $REC_rec_countLEADS)
						{
						@aryA = $sthA->fetchrow_array;
						$REC_leads_to_hopper[$REC_rec_countLEADS] =		$aryA[0];
						$REC_lists_to_hopper[$REC_rec_countLEADS] =		$aryA[1];
						$REC_gmt_to_hopper[$REC_rec_countLEADS] =		$aryA[2];
						$REC_phone_to_hopper[$REC_rec_countLEADS] =		$aryA[3];
						$REC_state_to_hopper[$REC_rec_countLEADS] =		$aryA[4];
						$REC_status_to_hopper[$REC_rec_countLEADS] =	$aryA[5];
						$REC_modify_to_hopper[$REC_rec_countLEADS] =	$aryA[6];
						$REC_user_to_hopper[$REC_rec_countLEADS] =		$aryA[7];
						$REC_source_to_hopper[$REC_rec_countLEADS] =	'R';
						if ($DB_show_offset) {print "LEAD_ADD: $aryA[2] $aryA[3] $aryA[4]\n";}
						$REC_rec_countLEADS++;
						}
					$sthA->finish();
					}
				else
					{
					if ($DB) {print "     NO RECYCLE-LEADS INTO HOPPER DEFINED\n";}
					}
			### END recycle grab leads ###


			### BEGIN NEW grab leads ###
				$NEW_rec_countLEADS=0;
				@NEW_leads_to_hopper=@MT;
				@NEW_lists_to_hopper=@MT;
				@NEW_phone_to_hopper=@MT;
				@NEW_gmt_to_hopper=@MT;
				@NEW_state_to_hopper=@MT;
				@NEW_status_to_hopper=@MT;
				@NEW_modify_to_hopper=@MT;
				@NEW_user_to_hopper=@MT;
				@NEW_source_to_hopper=@MT;
				if ( ($NEW_count > 0) && ($list_order_mix[$i] =~ /DISABLED/) )
					{
					$NEW_level = int($hopper_level[$i] / $NEW_count);   
					$OTHER_level = ($hopper_level[$i] - $NEW_level);   
					if ($DB) {print "     looking for $NEW_level NEW leads mixed in with $OTHER_level other leads\n";}

					$stmtA = "SELECT lead_id,list_id,gmt_offset_now,phone_number,state,status,modify_date,user FROM vicidial_list where called_since_last_reset='N' and status IN('NEW') and list_id IN($camp_lists[$i]) and lead_id NOT IN($lead_id_lists) and ($all_gmtSQL[$i]) $lead_filter_sql[$i] $DLTsql[$i] $order_stmt limit $NEW_level;";
					if ($DBX) {print "     |$stmtA|\n";}
					$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
					$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
					$sthArows=$sthA->rows;
					while ($sthArows > $NEW_rec_countLEADS)
						{
						@aryA = $sthA->fetchrow_array;
						$NEW_leads_to_hopper[$NEW_rec_countLEADS] =		$aryA[0];
						$NEW_lists_to_hopper[$NEW_rec_countLEADS] =		$aryA[1];
						$NEW_gmt_to_hopper[$NEW_rec_countLEADS] =		$aryA[2];
						$NEW_phone_to_hopper[$NEW_rec_countLEADS] =		$aryA[3];
						$NEW_state_to_hopper[$NEW_rec_countLEADS] =		$aryA[4];
						$NEW_status_to_hopper[$NEW_rec_countLEADS] =	$aryA[5];
						$NEW_modify_to_hopper[$NEW_rec_countLEADS] =	$aryA[6];
						$NEW_user_to_hopper[$NEW_rec_countLEADS] =		$aryA[7];
						$NEW_source_to_hopper[$NEW_rec_countLEADS] =	'N';
						if ($DB_show_offset) {print "LEAD_ADD: $aryA[2] $aryA[3] $aryA[4]\n";}
						$NEW_rec_countLEADS++;
						}
					$OTHER_level = ($hopper_level[$i] - $NEW_rec_countLEADS);
					$sthA->finish();
					}

			### BEGIN standard grab leads ###
				$rec_countLEADS=0;
				$NEW_dec=99;
				$NEW_in=0;
				$rec_count=0;
				$REC_insert_count=0;
				@leads_to_hopper=@MT;
				@lists_to_hopper=@MT;
				@gmt_to_hopper=@MT;
				@state_to_hopper=@MT;
				@phone_to_hopper=@MT;
				@status_to_hopper=@MT;
				@modify_to_hopper=@MT;
				@user_to_hopper=@MT;
				@source_to_hopper=@MT;
				if ($campaign_leads_to_call[$i] > 0)
					{
					if ($DB) {print "     lead call order:      $order_stmt\n";}

					if ($list_order_mix[$i] =~ /DISABLED/)
						{
						$stmtA = "SELECT lead_id,list_id,gmt_offset_now,phone_number,state,status,modify_date,user FROM vicidial_list where called_since_last_reset='N' and status IN($STATUSsql[$i]) and list_id IN($camp_lists[$i]) and lead_id NOT IN($lead_id_lists) and ($all_gmtSQL[$i]) $lead_filter_sql[$i] $DLTsql[$i] $order_stmt limit $OTHER_level;";
						if ($DBX) {print "     |$stmtA|\n";}
						$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
						$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
						$sthArows=$sthA->rows;
						while ($sthArows > $rec_count)
							{
							@aryA = $sthA->fetchrow_array;
							if ( ($NEW_count > 0) && ($NEW_rec_countLEADS > $NEW_in) )
								{
								if ($DB_show_offset) {print "NEW_COUNT: $NEW_count|$NEW_dec|$NEW_in|$NEW_rec_countLEADS\n";}
								if ($NEW_count > $NEW_dec) 
									{
									$NEW_dec++;
									}
								else
									{
									$leads_to_hopper[$rec_countLEADS] =		$NEW_leads_to_hopper[$NEW_in];
									$lists_to_hopper[$rec_countLEADS] =		$NEW_lists_to_hopper[$NEW_in];
									$gmt_to_hopper[$rec_countLEADS] =		$NEW_gmt_to_hopper[$NEW_in];
									$state_to_hopper[$rec_countLEADS] =		$NEW_state_to_hopper[$NEW_in];
									$phone_to_hopper[$rec_countLEADS] =		$NEW_phone_to_hopper[$NEW_in];
									$status_to_hopper[$rec_countLEADS] =	$NEW_status_to_hopper[$NEW_in];
									$modify_to_hopper[$rec_countLEADS] =	$NEW_modify_to_hopper[$NEW_in];
									$user_to_hopper[$rec_countLEADS] =		$NEW_user_to_hopper[$NEW_in];
									$source_to_hopper[$rec_countLEADS] =	$NEW_source_to_hopper[$NEW_in];
									if ($DB_show_offset) {print "LEAD_ADD:    $NEW_leads_to_hopper[$NEW_in]   $NEW_phone_to_hopper[$NEW_in]\n";}
									$rec_countLEADS++;
									$NEW_in++;
									$NEW_dec=2;
									}
								}
							if ($REC_rec_countLEADS > $REC_insert_count)
								{
								$leads_to_hopper[$rec_countLEADS] =		$REC_leads_to_hopper[$REC_insert_count];
								$lists_to_hopper[$rec_countLEADS] =		$REC_lists_to_hopper[$REC_insert_count];
								$gmt_to_hopper[$rec_countLEADS] =		$REC_gmt_to_hopper[$REC_insert_count];
								$state_to_hopper[$rec_countLEADS] =		$REC_state_to_hopper[$REC_insert_count];
								$phone_to_hopper[$rec_countLEADS] =		$REC_phone_to_hopper[$REC_insert_count];
								$status_to_hopper[$rec_countLEADS] =	$REC_status_to_hopper[$REC_insert_count];
								$modify_to_hopper[$rec_countLEADS] =	$REC_modify_to_hopper[$REC_insert_count];
								$user_to_hopper[$rec_countLEADS] =		$REC_user_to_hopper[$REC_insert_count];
								$source_to_hopper[$rec_countLEADS] =	$REC_source_to_hopper[$REC_insert_count];
								$rec_countLEADS++;
								$REC_insert_count++;
								}
							$leads_to_hopper[$rec_countLEADS] =		$aryA[0];
							$lists_to_hopper[$rec_countLEADS] =		$aryA[1];
							$gmt_to_hopper[$rec_countLEADS] =		$aryA[2];
							$state_to_hopper[$rec_countLEADS] =		$aryA[4];
							$phone_to_hopper[$rec_countLEADS] =		$aryA[3];
							$status_to_hopper[$rec_countLEADS] =	$aryA[5];
							$modify_to_hopper[$rec_countLEADS] =	$aryA[6];
							$user_to_hopper[$rec_countLEADS] =		$aryA[7];
							$source_to_hopper[$rec_countLEADS] =	'S';
							if ($DB_show_offset) {print "LEAD_ADD: $aryA[2] $aryA[3] $aryA[4]\n";}
							$rec_countLEADS++;
							$rec_count++;
							}
						$sthA->finish();
						}

				##### LIST MIX LEADS GRAB #####
					else
						{
						$USX='_____';
						$x=0;
						$z=0;
						@LM_results=@MT;
						foreach(@list_mixARY)
							{
							$rec_count=0;
							@list_mix_stepARY=@MT;

							@list_mix_stepARY = split(/\|/,$list_mixARY[$x]);
							if ($list_mix_stepARY[2] > 0)
								{
								$LM_step_goal[$x] = ( ($list_mix_stepARY[2] / 100) * $hopper_level[$i]);
								$LM_step_even[$x] = ( (100 / $list_mix_stepARY[2]) * 100000);
								}
							else
								{
								$LM_step_goal[$x] = 0;
								$LM_step_even[$x] = 0;
								}
							$list_mix_stepARY[3] =~ s/ /','/gi;
							$list_mix_stepARY[3] =~ s/^',|,'-//gi;
							if ($list_mix_stepARY[3] !~ /\'$/)
								{$list_mix_stepARY[3] = "$list_mix_stepARY[3]'";}
							if ($DBX) {print "  LM $x |$list_mix_stepARY[0]|$list_mix_stepARY[2]|$LM_step_goal[$x]|$list_mix_stepARY[3]|\n";}
							$list_mix_dialableSQL = "(list_id='$list_mix_stepARY[0]' and status IN($list_mix_stepARY[3]))";

							$stmtA = "SELECT lead_id,list_id,gmt_offset_now,phone_number,state,status,modify_date,user FROM vicidial_list where called_since_last_reset='N' and $list_mix_dialableSQL and lead_id NOT IN($lead_id_lists) and ($all_gmtSQL[$i]) $lead_filter_sql[$i] $DLTsql[$i] $order_stmt limit $LM_step_goal[$x];";
							if ($DBX) {print "     |$stmtA|\n";}
							$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
							$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
							$sthArows=$sthA->rows;
							while ($sthArows > $rec_count)
								{
								@aryA = $sthA->fetchrow_array;
								if ($mix_method[$i] =~ /RANDOM/) 
									{
									$order = int( rand(9999999)) + 10000000;
									}
								else 
									{
									if ($mix_method[$i] =~ /EVEN_MIX/) 
										{
										$order = ( ($rec_count * $LM_step_even[$x]) + $x);
										}
									else
										{
										$order = ( ($x * 1000000) + $rec_count);
										}
									}
								$LM_results[$z] = "$order$USX$aryA[0]$USX$aryA[1]$USX$aryA[2]$USX$aryA[3]$USX$aryA[4]$USX$aryA[5]$USX$aryA[6]$USX$aryA[7]";
							#	if ($DBX) {print "     $z|$LM_results[$z]\n";}

								$rec_count++;
								$z++;
								}
							$sthA->finish();

							$x++;
							}

						@LM_results_SORT = sort { $a <=> $b } @LM_results;

						$w=0;
						while ($z > $w)
							{
							@aryA = split(/_____/,$LM_results_SORT[$w]);
							if ($REC_rec_countLEADS > $REC_insert_count)
								{
								$leads_to_hopper[$rec_countLEADS] =		$REC_leads_to_hopper[$REC_insert_count];
								$lists_to_hopper[$rec_countLEADS] =		$REC_lists_to_hopper[$REC_insert_count];
								$gmt_to_hopper[$rec_countLEADS] =		$REC_gmt_to_hopper[$REC_insert_count];
								$state_to_hopper[$rec_countLEADS] =		$REC_state_to_hopper[$REC_insert_count];
								$phone_to_hopper[$rec_countLEADS] =		$REC_phone_to_hopper[$REC_insert_count];
								$status_to_hopper[$rec_countLEADS] =	$REC_status_to_hopper[$REC_insert_count];
								$modify_to_hopper[$rec_countLEADS] =	$REC_modify_to_hopper[$REC_insert_count];
								$user_to_hopper[$rec_countLEADS] =		$REC_user_to_hopper[$REC_insert_count];
								$source_to_hopper[$rec_countLEADS] =	$REC_source_to_hopper[$REC_insert_count];
								$rec_countLEADS++;
								$REC_insert_count++;
								}
							$leads_to_hopper[$rec_countLEADS] =		$aryA[1];
							$lists_to_hopper[$rec_countLEADS] =		$aryA[2];
							$gmt_to_hopper[$rec_countLEADS] =		$aryA[3];
							$phone_to_hopper[$rec_countLEADS] =		$aryA[4];
							$state_to_hopper[$rec_countLEADS] =		$aryA[5];
							$status_to_hopper[$rec_countLEADS] =	$aryA[6];
							$modify_to_hopper[$rec_countLEADS] =	$aryA[7];
							$user_to_hopper[$rec_countLEADS] =		$aryA[8];
							$source_to_hopper[$rec_countLEADS] =	'S';
							if ($DB_show_offset) {print "LEAD_ADD: $aryA[3] $aryA[4] $aryA[5]\n";}
							if ($DBX) {print "     $w|$LM_results[$w]\n";}
							$rec_countLEADS++;
							$w++;
							}
						}
					}
				### finish inserting any recycled leads if any
				while ($REC_rec_countLEADS > $REC_insert_count)
					{
					$leads_to_hopper[$rec_countLEADS] =		$REC_leads_to_hopper[$REC_insert_count];
					$lists_to_hopper[$rec_countLEADS] =		$REC_lists_to_hopper[$REC_insert_count];
					$gmt_to_hopper[$rec_countLEADS] =		$REC_gmt_to_hopper[$REC_insert_count];
					$state_to_hopper[$rec_countLEADS] =		$REC_state_to_hopper[$REC_insert_count];
					$phone_to_hopper[$rec_countLEADS] =		$REC_phone_to_hopper[$REC_insert_count];
					$status_to_hopper[$rec_countLEADS] =	$REC_status_to_hopper[$REC_insert_count];
					$modify_to_hopper[$rec_countLEADS] =	$REC_modify_to_hopper[$REC_insert_count];
					$user_to_hopper[$rec_countLEADS] =		$REC_user_to_hopper[$REC_insert_count];
					$source_to_hopper[$rec_countLEADS] =	$REC_source_to_hopper[$REC_insert_count];
					$rec_countLEADS++;
					$REC_insert_count++;
					}

				if ($DB) {print "     Adding to hopper:     $rec_countLEADS\n";}
				$event_string = "|$campaign_id[$i]|Added to hopper $rec_countLEADS|";
				&event_logger;

				$h=0;
				foreach(@leads_to_hopper)
					{
					if ($leads_to_hopper[$h] != '0')
						{
						$DNClead=0;
						$DNCC=0;
						$DNCL=0;
						if ( ($use_internal_dnc[$i] =~ /Y/) || ($use_internal_dnc[$i] =~ /AREACODE/) )
							{
							if ($use_internal_dnc[$i] =~ /AREACODE/)
								{
								$pth_areacode = substr($phone_to_hopper[$h], 0, 3);
								$pth_areacode .= "XXXXXXX";
								$stmtA="SELECT count(*) FROM vicidial_dnc where phone_number IN('$phone_to_hopper[$h]','$pth_areacode');";
								}
							else
								{$stmtA="SELECT count(*) FROM vicidial_dnc where phone_number='$phone_to_hopper[$h]';";}
							if ($DB) {print "     Doing DNC Check: $phone_to_hopper[$h] - $use_internal_dnc[$i]\n";}
							$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
							$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
							$sthArows=$sthA->rows;
							if ($sthArows > 0)
								{
								@aryA = $sthA->fetchrow_array;
								$DNClead =		 $aryA[0];
								}
							$sthA->finish();
							if ($DNClead != '0')
								{
								$DNCL++;
								$stmtA = "UPDATE vicidial_list SET status='DNCL' where lead_id='$leads_to_hopper[$h]';";
								$affected_rows = $dbhA->do($stmtA);
								if ($DBX) {print "Flagging DNC lead:     $affected_rows  $phone_to_hopper[$h]\n";}
								}
							}
						if ( ( ($use_campaign_dnc[$i] =~ /Y/) || ($use_campaign_dnc[$i] =~ /AREACODE/) ) && ($DNClead == '0') )
							{
							if ($use_campaign_dnc[$i] =~ /AREACODE/)
								{
								$pth_areacode = substr($phone_to_hopper[$h], 0, 3);
								$pth_areacode .= "XXXXXXX";
								$stmtA="SELECT count(*) FROM vicidial_campaign_dnc where phone_number IN('$phone_to_hopper[$h]','$pth_areacode') and campaign_id='$campaign_id[$i]';";
								}
							else
								{$stmtA="SELECT count(*) FROM vicidial_campaign_dnc where phone_number='$phone_to_hopper[$h]' and campaign_id='$campaign_id[$i]';";}
							if ($DB) {print "Doing CAMP DNC Check: $phone_to_hopper[$h] - $use_campaign_dnc[$i]\n";}
							$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
							$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
							$sthArows=$sthA->rows;
							if ($sthArows > 0)
								{
								@aryA = $sthA->fetchrow_array;
								$DNClead =	($DNClead + $aryA[0]);
								}
							$sthA->finish();
							if ($aryA[0] != '0')
								{
								$DNCC++;
								$stmtA = "UPDATE vicidial_list SET status='DNCC' where lead_id='$leads_to_hopper[$h]';";
								$affected_rows = $dbhA->do($stmtA);
								if ($DBX) {print "Flagging DNC lead:     $affected_rows  $phone_to_hopper[$h] $campaign_id[$i]\n";}
								}
							}

							$VAC_exist=0;
							$stmtA="SELECT count(*) FROM vicidial_auto_calls where lead_id='$leads_to_hopper[$h]';";
							$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
							$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
							$sthArows=$sthA->rows;
							if ($sthArows > 0)
								{
								@aryA = $sthA->fetchrow_array;
								$VAC_exist =	$aryA[0];
								}
							$sthA->finish();

							$VLA_exist=0;
							$stmtA="SELECT count(*) FROM vicidial_live_agents where lead_id='$leads_to_hopper[$h]';";
							$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
							$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
							$sthArows=$sthA->rows;
							if ($sthArows > 0)
								{
								@aryA = $sthA->fetchrow_array;
								$VLA_exist =	$aryA[0];
								}
							$sthA->finish();

						if ( ($VAC_exist > 0) || ($VLA_exist > 0) )
							{
							$detail_string = "LIVE CALL SKIPPING     |$VAC_exist|$VLA_exist|     |$campaign_id[$i]|$leads_to_hopper[$h]|$phone_to_hopper[$h]|$state_to_hopper[$h]|$gmt_to_hopper[$h]|$status_to_hopper[$h]|$modify_to_hopper[$h]|$user_to_hopper[$h]|";
							&detail_logger;
							}
						else
							{
							if ($DNClead == '0')
								{
								$stmtA = "INSERT INTO $vicidial_hopper (lead_id,campaign_id,status,user,list_id,gmt_offset_now,state,priority,source) values('$leads_to_hopper[$h]','$campaign_id[$i]','READY','','$lists_to_hopper[$h]','$gmt_to_hopper[$h]','$state_to_hopper[$h]','0','$source_to_hopper[$h]');";
								$affected_rows = $dbhA->do($stmtA);
								if ($DBX) {print "LEAD INSERTED: $affected_rows|$leads_to_hopper[$h]|\n";}
								if ($DB_detail) 
									{
									$detail_string = "|$campaign_id[$i]|$leads_to_hopper[$h]|$phone_to_hopper[$h]|$state_to_hopper[$h]|$gmt_to_hopper[$h]|$status_to_hopper[$h]|$modify_to_hopper[$h]|$user_to_hopper[$h]|$source_to_hopper[$h]|";
									&detail_logger;
									}
								}
							else
								{
								##### Auto-Alt-Dial if DNCC or DNCL are set to campaign auto-alt-dial statuses, insert lead into hopper as DNC status
								if ( ( ($auto_alt_dial_statuses[$i] =~ / DNCC /) && ($DNCC > 0) ) || ( ($auto_alt_dial_statuses[$i] =~ / DNCL /) && ($DNCL > 0) ) )
									{
									$stmtA = "INSERT INTO $vicidial_hopper (lead_id,campaign_id,status,user,list_id,gmt_offset_now,state,priority,source) values('$leads_to_hopper[$h]','$campaign_id[$i]','DNC','','$lists_to_hopper[$h]','$gmt_to_hopper[$h]','$state_to_hopper[$h]','0','$source_to_hopper[$h]');";
									$affected_rows = $dbhA->do($stmtA);
									if ($DBX) {print "LEAD INSERTED AS DNC: $affected_rows|$leads_to_hopper[$h]|\n";}
									if ($DB_detail) 
										{
										$detail_string = "|$campaign_id[$i]|$leads_to_hopper[$h]|$phone_to_hopper[$h]|$state_to_hopper[$h]|$gmt_to_hopper[$h]|$status_to_hopper[$h]|$modify_to_hopper[$h]|$user_to_hopper[$h]|$source_to_hopper[$h]|";
										&detail_logger;
										}
									}
								}
							}
						}
					$h++;
					}
				if ($DB) {print "     DONE with this campaign\n";}
				}
			}
		}
	$i++;
	}


$dbhA->disconnect();

if($DB)
	{
	if (length($ENDoutput) > 10)
		{
		print "\n";
		print "SUMMARY:     $i campaigns\n";
		print "$ENDoutput";
		}
	### calculate time to run script ###
	$secY = time();
	$secZ = ($secY - $secT);

	if (!$q) {print "DONE. Script execution time in seconds: $secZ\n";}
	}

exit;



sub event_logger
	{
	if ($SYSLOG)
		{
		### open the log file for writing ###
		open(Lout, ">>$VDHLOGfile")
				|| die "Can't open $VDHLOGfile: $!\n";
		print Lout "$now_date|$event_string|\n";
		close(Lout);
		}
	$event_string='';
	}

sub detail_logger
	{
	if ($SYSLOG)
		{
		### open the log file for writing ###
		open(LDout, ">>$VDHDLOGfile")
				|| die "Can't open $VDHDLOGfile: $!\n";
		print LDout "$now_date|$detail_string|\n";
		close(LDout);
		}
	$detail_string='';
	}

sub roundup 
	{
	my $n = shift;
	return(($n == int($n)) ? $n : int($n + 1))
	}
