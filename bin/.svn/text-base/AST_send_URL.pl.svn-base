#!/usr/bin/perl
#
# AST_send_URL.pl   version 2.4
# 
# DESCRIPTION:
# This script is spawned for remote agents when the Start Call URL is set in the
# campaign or in-group that the call came from when sent to the remote agent.
# This script is also used for the Add-Lead-URL feature in In-groups.
#
# Copyright (C) 2011  Matt Florell <vicidial@gmail.com>    LICENSE: AGPLv2
#
# CHANGES
# 100622-0929 - First Build
# 100929-0918 - Added function variable and new Add Lead URL function
# 110731-0127 - Added call_id variable and logging
#

$|++;
use Getopt::Long;

### Initialize date/time vars ###
my $secX = time(); #Start time

($sec,$min,$hour,$mday,$mon,$year,$wday,$yday,$isdst) = localtime(time);
$year = ($year + 1900);
$mon++;
if ($mon < 10) {$mon = "0$mon";}
if ($mday < 10) {$mday = "0$mday";}
if ($hour < 10) {$hour = "0$hour";}
if ($min < 10) {$min = "0$min";}
if ($sec < 10) {$sec = "0$sec";}
$now_date = "$year-$mon-$mday $hour:$min:$sec";

### Initialize run-time variables ###
my ($CLOhelp, $SYSLOG,$DB, $DBX);
my ($campaign, $lead_id, $phone_number, $call_type, $user, $uniqueid, $alt_dial, $call_id, $function);
my $FULL_LOG = 1;
$url_function = 'other';
$US='_';

### begin parsing run-time options ###
if (scalar @ARGV) {
	GetOptions('help!' => \$CLOhelp,
		'SYSLOG!' => \$SYSLOG,
		'campaign=s' => \$campaign,
		'lead_id=s' => \$lead_id,
		'phone_number=s' => \$phone_number,
		'call_type=s' => \$call_type,
		'user=s' => \$user,
		'uniqueid=s' => \$uniqueid,
		'alt_dial=s' => \$alt_dial,
		'call_id=s' => \$call_id,
		'function=s' => \$function,
		'debug!' => \$DB,
		'debugX!' => \$DBX,
		'fulllog!' => \$FULL_LOG);

	$DB = 1 if ($DBX);
	if ($DB) 
		{
		print "\n----- DEBUGGING -----\n\n";
		print "\n----- SUPER-DUPER DEBUGGING -----\n\n" if ($DBX);
		print "  SYSLOG:                $SYSLOG\n" if ($SYSLOG);
		print "  campaign:              $campaign\n" if ($campaign);
		print "  lead_id:		        $lead_id\n" if ($lead_id);
		print "  phone_number:          $phone_number\n" if ($phone_number);
		print "  call_type:             $call_type\n" if ($call_type);
		print "  user:                  $user\n" if ($user);
		print "  uniqueid:              $uniqueid\n" if ($uniqueid);
		print "  alt_dial:              $alt_dial\n" if ($alt_dial);
		print "  call_id:               $call_id\n" if ($call_id);
		print "  function:              $function\n" if ($function);
		print "\n";
		}
	if ($CLOhelp) 
		{
		print "allowed run time options:\n";
		print "  [--help] = this help screen\n";
		print "  [--SYSLOG] = whether to log actions or not\n";
		print "required flags:\n";
		print "  [--campaign] = campaign ID or In-group ID of the call\n";
		print "  [--lead_id] = lead ID for the call\n";
		print "  [--phone_number] = phone number of the call\n";
		print "  [--call_type] = Inbound or outbound call\n";
		print "  [--user] = remote user that received the call\n";
		print "  [--uniqueid] = uniqueid of the call\n";
		print "  [--alt_dial] = label of the phone number dialed\n";
		print "  [--call_id] = call_id or caller_code of the call\n";
		print "  [--function] = which function is to be run, default is REMOTE_AGENT_START_CALL_URL\n";
		print "      *REMOTE_AGENT_START_CALL_URL - performs a Start Call URL get for Remote Agent Calls\n";
		print "      *INGROUP_ADD_LEAD_URL - performs an Add Lead URL get for In-Groups when a lead is added\n";
		print "\n";
		print "You may prefix an option with 'no' to disable it.\n";
		print " ie. --noSYSLOG or --noFULLLOG\n";

		exit 0;
		}
	}

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
	if ( ($line =~ /^PATHlogs/) && ($CLIlogs < 1) )
		{$PATHlogs = $line;   $PATHlogs =~ s/.*=//gi;}
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


if (length($lead_id) > 0) 
	{
	### find wget binary
	$wgetbin = '';
	if ( -e ('/bin/wget')) {$wgetbin = '/bin/wget';}
	else
		{
		if ( -e ('/usr/bin/wget')) {$wgetbin = '/usr/bin/wget';}
		else
			{
			if ( -e ('/usr/local/bin/wget')) {$wgetbin = '/usr/local/bin/wget';}
			else
				{
				print "Can't find wget binary! Exiting...\n";
				exit;
				}
			}
		}

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

	if ($call_type =~ /IN/)
		{$stmtG = "SELECT start_call_url,add_lead_url,na_call_url FROM vicidial_inbound_groups where group_id='$campaign';";}
	else
		{$stmtG = "SELECT start_call_url,'NONE',na_call_url FROM vicidial_campaigns where campaign_id='$campaign';";}
	$sthA = $dbhA->prepare($stmtG) or die "preparing: ",$dbhA->errstr;
	$sthA->execute or die "executing: $stmtG ", $dbhA->errstr;
	$start_url_ct=$sthA->rows;
	if ($start_url_ct > 0)
		{
		@aryA = $sthA->fetchrow_array;
		$start_call_url =	$aryA[0];
		$add_lead_url =		$aryA[1];
		$na_call_url =		$aryA[2];
		}
	$sthA->finish();

	$VAR_lead_id =			$lead_id;
	$VAR_user =				$user;
	$VAR_phone_number =		$phone_number;
	$VAR_call_type =		$call_type;
	$VAR_uniqueid =			$uniqueid;
	$VAR_group_id =			$campaign;
	$VAR_campaign_id =		$campaign;
	$VAR_group =			$campaign;
	$VAR_alt_dial =			$alt_dial;
	$VAR_call_id =			$call_id;
	$VAR_list_id =			'';
	$VAR_phone_code =		'';
	$VAR_vendor_lead_code =	'';
	$VAR_did_id =			'';
	$VAR_did_extension =	'';
	$VAR_did_pattern =		'';
	$VAR_did_description =	'';
	$VAR_closecallid =		'';

	if ($function =~ /INGROUP_ADD_LEAD_URL/)
		{
		##### BEGIN Add Lead URL function #####
		$stmtA = "SELECT list_id,phone_code,vendor_lead_code FROM vicidial_list where lead_id='$lead_id';";
		$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
		$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
		$sthArows=$sthA->rows;
		if ($sthArows > 0)
			{
			@aryA = $sthA->fetchrow_array;
			$VAR_list_id =			$aryA[0];
			$VAR_phone_code =		$aryA[1];
			$VAR_vendor_lead_code =	$aryA[2];
			}
		$sthA->finish();

		$stmtA = "SELECT did_id,extension FROM vicidial_did_log where uniqueid='$uniqueid';";
		$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
		$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
		$sthArows=$sthA->rows;
		if ($sthArows > 0)
			{
			@aryA = $sthA->fetchrow_array;
			$VAR_did_id =			$aryA[0];
			$VAR_did_extension =	$aryA[1];
			}
		$sthA->finish();

		$stmtA = "SELECT did_pattern,did_description FROM vicidial_inbound_dids where did_id='$VAR_did_id';";
		$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
		$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
		$sthArows=$sthA->rows;
		if ($sthArows > 0)
			{
			@aryA = $sthA->fetchrow_array;
			$VAR_did_pattern =		$aryA[0];
			$VAR_did_description =	$aryA[1];
			}
		$sthA->finish();

		$stmtA = "SELECT closecallid FROM vicidial_closer_log where uniqueid='$uniqueid' order by closecallid limit 1;";
		$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
		$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
		$sthArows=$sthA->rows;
		if ($sthArows > 0)
			{
			@aryA = $sthA->fetchrow_array;
			$VAR_closecallid =		$aryA[0];
			}
		$sthA->finish();

		$add_lead_url =~ s/^VAR//gi;
		$add_lead_url =~ s/--A--lead_id--B--/$VAR_lead_id/gi;
		$add_lead_url =~ s/--A--vendor_id--B--/$VAR_vendor_lead_code/gi;
		$add_lead_url =~ s/--A--vendor_lead_code--B--/$VAR_vendor_lead_code/gi;
		$add_lead_url =~ s/--A--list_id--B--/$VAR_list_id/gi;
		$add_lead_url =~ s/--A--phone_number--B--/$VAR_phone_number/gi;
		$add_lead_url =~ s/--A--phone_code--B--/$VAR_phone_code/gi;
		$add_lead_url =~ s/--A--did_id--B--/$VAR_did_id/gi;
		$add_lead_url =~ s/--A--did_extension--B--/$VAR_did_extension/gi;
		$add_lead_url =~ s/--A--did_pattern--B--/$VAR_did_pattern/gi;
		$add_lead_url =~ s/--A--did_description--B--/$VAR_did_description/gi;
		$add_lead_url =~ s/--A--closecallid--B--/$VAR_closecallid/gi;
		$add_lead_url =~ s/--A--uniqueid--B--/$VAR_uniqueid/gi;
		$add_lead_url =~ s/--A--call_id--B--/$VAR_call_id/gi;
		$add_lead_url =~ s/ /+/gi;
		$add_lead_url =~ s/&/\\&/gi;
		$parse_url = $add_lead_url;
		$url_function = 'add_lead';
		##### END Add Lead URL function #####
		}
	elsif ($function =~ /NA_CALL_URL/)
		{
		##### BEGIN Call URL function #####
		$stmtA = "SELECT lead_id,entry_date,modify_date,status,user,vendor_lead_code,source_id,list_id,phone_number,title,first_name,middle_initial,last_name,address1,address2,address3,city,state,province,postal_code,country_code,gender,date_of_birth,alt_phone,email,security_phrase,comments,called_count,last_local_call_time,rank,owner,phone_code FROM vicidial_list where lead_id='$lead_id';";
		$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
		$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
		$sthArows=$sthA->rows;
		if ($sthArows > 0)
			{
			@aryA = $sthA->fetchrow_array;
			$VAR_entry_date =		$aryA[1];
			$VAR_modify_date =		$aryA[2];
			$VAR_status =			$aryA[3];
			$VAR_vendor_lead_code =	$aryA[5];
			$VAR_source_id =		$aryA[6];
			$VAR_list_id =			$aryA[7];
			$VAR_title =			$aryA[9];
			$VAR_first_name =		$aryA[10];
			$VAR_middle_initial =	$aryA[11];
			$VAR_last_name =		$aryA[12];
			$VAR_address1 =			$aryA[13];
			$VAR_address2 =			$aryA[14];
			$VAR_address3 =			$aryA[15];
			$VAR_city =				$aryA[16];
			$VAR_state =			$aryA[17];
			$VAR_province =			$aryA[18];
			$VAR_postal_code =		$aryA[19];
			$VAR_country_code =		$aryA[20];
			$VAR_gender =			$aryA[21];
			$VAR_date_of_birth =	$aryA[22];
			$VAR_alt_phone =		$aryA[23];
			$VAR_email =			$aryA[24];
			$VAR_security_phrase =	$aryA[25];
			$VAR_comments =			$aryA[26];
			$VAR_called_count =		$aryA[27];
			$VAR_last_local_call_time = $aryA[28];
			$VAR_rank =				$aryA[29];
			$VAR_owner =			$aryA[30];
			$VAR_phone_code =		$aryA[31];
			}
		$sthA->finish();

		if ($na_call_url =~ /--A--user_custom_/)
			{
			$stmtA = "SELECT custom_one,custom_two,custom_three,custom_four,custom_five from vicidial_users where user='$user';";
			$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
			$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
			$sthArows=$sthA->rows;
			if ($sthArows > 0)
				{
				@aryA = $sthA->fetchrow_array;
				$VAR_user_custom_one =		$aryA[0];
				$VAR_user_custom_two =		$aryA[1];
				$VAR_user_custom_three =	$aryA[2];
				$VAR_user_custom_four =		$aryA[3];
				$VAR_user_custom_five =		$aryA[4];
				}
			}

		if ($na_call_url =~ /--A--did_/)
			{
			$stmtA = "SELECT did_id,extension FROM vicidial_did_log where uniqueid='$uniqueid';";
			$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
			$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
			$sthArows=$sthA->rows;
			if ($sthArows > 0)
				{
				@aryA = $sthA->fetchrow_array;
				$VAR_did_id =			$aryA[0];
				$VAR_did_extension =	$aryA[1];
				}
			$sthA->finish();

			$stmtA = "SELECT did_pattern,did_description FROM vicidial_inbound_dids where did_id='$VAR_did_id';";
			$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
			$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
			$sthArows=$sthA->rows;
			if ($sthArows > 0)
				{
				@aryA = $sthA->fetchrow_array;
				$VAR_did_pattern =		$aryA[0];
				$VAR_did_description =	$aryA[1];
				}
			$sthA->finish();
			}

		if ($na_call_url =~ /--A--closecallid--B--/)
			{
			$stmtA = "SELECT closecallid FROM vicidial_closer_log where uniqueid='$uniqueid' order by closecallid limit 1;";
			$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
			$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
			$sthArows=$sthA->rows;
			if ($sthArows > 0)
				{
				@aryA = $sthA->fetchrow_array;
				$VAR_closecallid =		$aryA[0];
				}
			$sthA->finish();
			}
		
		$na_call_url =~ s/^VAR//gi;
		$na_call_url =~ s/--A--lead_id--B--/$VAR_lead_id/gi;
		$na_call_url =~ s/--A--entry_date--B--/$VAR_entry_date/gi;
		$na_call_url =~ s/--A--modify_date--B--/$VAR_modify_date/gi;
		$na_call_url =~ s/--A--status--B--/$VAR_status/gi;
		$na_call_url =~ s/--A--dispo--B--/$VAR_status/gi;
		$na_call_url =~ s/--A--user--B--/$VAR_user/gi;
		$na_call_url =~ s/--A--vendor_id--B--/$VAR_vendor_lead_code/gi;
		$na_call_url =~ s/--A--vendor_lead_code--B--/$VAR_vendor_lead_code/gi;
		$na_call_url =~ s/--A--source_id--B--/$VAR_source_id/gi;
		$na_call_url =~ s/--A--list_id--B--/$VAR_list_id/gi;
		$na_call_url =~ s/--A--phone_code--B--/$VAR_phone_code/gi;
		$na_call_url =~ s/--A--phone_number--B--/$VAR_phone_number/gi;
		$na_call_url =~ s/--A--title--B--/$VAR_title/gi;
		$na_call_url =~ s/--A--first_name--B--/$VAR_first_name/gi;
		$na_call_url =~ s/--A--middle_initial--B--/$VAR_middle_initial/gi;
		$na_call_url =~ s/--A--last_name--B--/$VAR_last_name/gi;
		$na_call_url =~ s/--A--address1--B--/$VAR_address1/gi;
		$na_call_url =~ s/--A--address2--B--/$VAR_address2/gi;
		$na_call_url =~ s/--A--address3--B--/$VAR_address3/gi;
		$na_call_url =~ s/--A--city--B--/$VAR_city/gi;
		$na_call_url =~ s/--A--state--B--/$VAR_state/gi;
		$na_call_url =~ s/--A--province--B--/$VAR_province/gi;
		$na_call_url =~ s/--A--postal_code--B--/$VAR_postal_code/gi;
		$na_call_url =~ s/--A--country_code--B--/$VAR_country_code/gi;
		$na_call_url =~ s/--A--gender--B--/$VAR_gender/gi;
		$na_call_url =~ s/--A--date_of_birth--B--/$VAR_date_of_birth/gi;
		$na_call_url =~ s/--A--alt_phone--B--/$VAR_alt_phone/gi;
		$na_call_url =~ s/--A--email--B--/$VAR_email/gi;
		$na_call_url =~ s/--A--security_phrase--B--/$VAR_security_phrase/gi;
		$na_call_url =~ s/--A--comments--B--/$VAR_comments/gi;
		$na_call_url =~ s/--A--called_count--B--/$VAR_called_count/gi;
		$na_call_url =~ s/--A--last_local_call_time--B--/$VAR_last_local_call_time/gi;
		$na_call_url =~ s/--A--rank--B--/$VAR_rank/gi;
		$na_call_url =~ s/--A--owner--B--/$VAR_owner/gi;
		$na_call_url =~ s/--A--dialed_number--B--/$VAR_phone_number/gi;
		$na_call_url =~ s/--A--dialed_label--B--/$VAR_alt_dial/gi;
		$na_call_url =~ s/--A--user_custom_one--B--/$VAR_user_custom_one/gi;
		$na_call_url =~ s/--A--user_custom_two--B--/$VAR_user_custom_two/gi;
		$na_call_url =~ s/--A--user_custom_three--B--/$VAR_user_custom_three/gi;
		$na_call_url =~ s/--A--user_custom_four--B--/$VAR_user_custom_four/gi;
		$na_call_url =~ s/--A--user_custom_five--B--/$VAR_user_custom_five/gi;
		$na_call_url =~ s/--A--did_id--B--/$VAR_did_id/gi;
		$na_call_url =~ s/--A--did_extension--B--/$VAR_did_extension/gi;
		$na_call_url =~ s/--A--did_pattern--B--/$VAR_did_pattern/gi;
		$na_call_url =~ s/--A--did_description--B--/$VAR_did_description/gi;
		$na_call_url =~ s/--A--closecallid--B--/$VAR_closecallid/gi;
		$na_call_url =~ s/--A--uniqueid--B--/$VAR_uniqueid/gi;
		$na_call_url =~ s/--A--call_id--B--/$VAR_call_id/gi;
		$na_call_url =~ s/ /+/gi;
		$na_call_url =~ s/&/\\&/gi;
		$parse_url = $na_call_url;

		$url_function = 'na_callurl';
		}
	else
		{
		##### BEGIN Call URL function #####
		$stmtA = "SELECT lead_id,entry_date,modify_date,status,user,vendor_lead_code,source_id,list_id,phone_number,title,first_name,middle_initial,last_name,address1,address2,address3,city,state,province,postal_code,country_code,gender,date_of_birth,alt_phone,email,security_phrase,comments,called_count,last_local_call_time,rank,owner,phone_code FROM vicidial_list where lead_id='$lead_id';";
		$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
		$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
		$sthArows=$sthA->rows;
		if ($sthArows > 0)
			{
			@aryA = $sthA->fetchrow_array;
			$VAR_entry_date =		$aryA[1];
			$VAR_modify_date =		$aryA[2];
			$VAR_status =			$aryA[3];
			$VAR_vendor_lead_code =	$aryA[5];
			$VAR_source_id =		$aryA[6];
			$VAR_list_id =			$aryA[7];
			$VAR_title =			$aryA[9];
			$VAR_first_name =		$aryA[10];
			$VAR_middle_initial =	$aryA[11];
			$VAR_last_name =		$aryA[12];
			$VAR_address1 =			$aryA[13];
			$VAR_address2 =			$aryA[14];
			$VAR_address3 =			$aryA[15];
			$VAR_city =				$aryA[16];
			$VAR_state =			$aryA[17];
			$VAR_province =			$aryA[18];
			$VAR_postal_code =		$aryA[19];
			$VAR_country_code =		$aryA[20];
			$VAR_gender =			$aryA[21];
			$VAR_date_of_birth =	$aryA[22];
			$VAR_alt_phone =		$aryA[23];
			$VAR_email =			$aryA[24];
			$VAR_security_phrase =	$aryA[25];
			$VAR_comments =			$aryA[26];
			$VAR_called_count =		$aryA[27];
			$VAR_last_local_call_time = $aryA[28];
			$VAR_rank =				$aryA[29];
			$VAR_owner =			$aryA[30];
			$VAR_phone_code =		$aryA[31];
			}
		$sthA->finish();

		if ($start_call_url =~ /--A--user_custom_/)
			{
			$stmtA = "SELECT custom_one,custom_two,custom_three,custom_four,custom_five from vicidial_users where user='$user';";
			$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
			$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
			$sthArows=$sthA->rows;
			if ($sthArows > 0)
				{
				@aryA = $sthA->fetchrow_array;
				$VAR_user_custom_one =		$aryA[0];
				$VAR_user_custom_two =		$aryA[1];
				$VAR_user_custom_three =	$aryA[2];
				$VAR_user_custom_four =		$aryA[3];
				$VAR_user_custom_five =		$aryA[4];
				}
			}

		if ($start_call_url =~ /--A--did_/)
			{
			$stmtA = "SELECT did_id,extension FROM vicidial_did_log where uniqueid='$uniqueid';";
			$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
			$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
			$sthArows=$sthA->rows;
			if ($sthArows > 0)
				{
				@aryA = $sthA->fetchrow_array;
				$VAR_did_id =			$aryA[0];
				$VAR_did_extension =	$aryA[1];
				}
			$sthA->finish();

			$stmtA = "SELECT did_pattern,did_description FROM vicidial_inbound_dids where did_id='$VAR_did_id';";
			$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
			$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
			$sthArows=$sthA->rows;
			if ($sthArows > 0)
				{
				@aryA = $sthA->fetchrow_array;
				$VAR_did_pattern =		$aryA[0];
				$VAR_did_description =	$aryA[1];
				}
			$sthA->finish();
			}

		if ($start_call_url =~ /--A--closecallid--B--/)
			{
			$stmtA = "SELECT closecallid FROM vicidial_closer_log where uniqueid='$uniqueid' order by closecallid limit 1;";
			$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
			$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
			$sthArows=$sthA->rows;
			if ($sthArows > 0)
				{
				@aryA = $sthA->fetchrow_array;
				$VAR_closecallid =		$aryA[0];
				}
			$sthA->finish();
			}

		$start_call_url =~ s/^VAR//gi;
		$start_call_url =~ s/--A--lead_id--B--/$VAR_lead_id/gi;
		$start_call_url =~ s/--A--entry_date--B--/$VAR_entry_date/gi;
		$start_call_url =~ s/--A--modify_date--B--/$VAR_modify_date/gi;
		$start_call_url =~ s/--A--status--B--/$VAR_status/gi;
		$start_call_url =~ s/--A--user--B--/$VAR_user/gi;
		$start_call_url =~ s/--A--vendor_id--B--/$VAR_vendor_lead_code/gi;
		$start_call_url =~ s/--A--vendor_lead_code--B--/$VAR_vendor_lead_code/gi;
		$start_call_url =~ s/--A--source_id--B--/$VAR_source_id/gi;
		$start_call_url =~ s/--A--list_id--B--/$VAR_list_id/gi;
		$start_call_url =~ s/--A--phone_code--B--/$VAR_phone_code/gi;
		$start_call_url =~ s/--A--phone_number--B--/$VAR_phone_number/gi;
		$start_call_url =~ s/--A--title--B--/$VAR_title/gi;
		$start_call_url =~ s/--A--first_name--B--/$VAR_first_name/gi;
		$start_call_url =~ s/--A--middle_initial--B--/$VAR_middle_initial/gi;
		$start_call_url =~ s/--A--last_name--B--/$VAR_last_name/gi;
		$start_call_url =~ s/--A--address1--B--/$VAR_address1/gi;
		$start_call_url =~ s/--A--address2--B--/$VAR_address2/gi;
		$start_call_url =~ s/--A--address3--B--/$VAR_address3/gi;
		$start_call_url =~ s/--A--city--B--/$VAR_city/gi;
		$start_call_url =~ s/--A--state--B--/$VAR_state/gi;
		$start_call_url =~ s/--A--province--B--/$VAR_province/gi;
		$start_call_url =~ s/--A--postal_code--B--/$VAR_postal_code/gi;
		$start_call_url =~ s/--A--country_code--B--/$VAR_country_code/gi;
		$start_call_url =~ s/--A--gender--B--/$VAR_gender/gi;
		$start_call_url =~ s/--A--date_of_birth--B--/$VAR_date_of_birth/gi;
		$start_call_url =~ s/--A--alt_phone--B--/$VAR_alt_phone/gi;
		$start_call_url =~ s/--A--email--B--/$VAR_email/gi;
		$start_call_url =~ s/--A--security_phrase--B--/$VAR_security_phrase/gi;
		$start_call_url =~ s/--A--comments--B--/$VAR_comments/gi;
		$start_call_url =~ s/--A--called_count--B--/$VAR_called_count/gi;
		$start_call_url =~ s/--A--last_local_call_time--B--/$VAR_last_local_call_time/gi;
		$start_call_url =~ s/--A--rank--B--/$VAR_rank/gi;
		$start_call_url =~ s/--A--owner--B--/$VAR_owner/gi;
		$start_call_url =~ s/--A--dialed_number--B--/$VAR_phone_number/gi;
		$start_call_url =~ s/--A--dialed_label--B--/$VAR_alt_dial/gi;
		$start_call_url =~ s/--A--user_custom_one--B--/$VAR_user_custom_one/gi;
		$start_call_url =~ s/--A--user_custom_two--B--/$VAR_user_custom_two/gi;
		$start_call_url =~ s/--A--user_custom_three--B--/$VAR_user_custom_three/gi;
		$start_call_url =~ s/--A--user_custom_four--B--/$VAR_user_custom_four/gi;
		$start_call_url =~ s/--A--user_custom_five--B--/$VAR_user_custom_five/gi;
		$start_call_url =~ s/--A--did_id--B--/$VAR_did_id/gi;
		$start_call_url =~ s/--A--did_extension--B--/$VAR_did_extension/gi;
		$start_call_url =~ s/--A--did_pattern--B--/$VAR_did_pattern/gi;
		$start_call_url =~ s/--A--did_description--B--/$VAR_did_description/gi;
		$start_call_url =~ s/--A--closecallid--B--/$VAR_closecallid/gi;
		$start_call_url =~ s/--A--uniqueid--B--/$VAR_uniqueid/gi;
		$start_call_url =~ s/--A--call_id--B--/$VAR_call_id/gi;
		$start_call_url =~ s/ /+/gi;
		$start_call_url =~ s/&/\\&/gi;
		$parse_url = $start_call_url;

		if ($function =~ /REMOTE_AGENT_START_CALL_URL/)
			{
			$url_function = 'start_ra';
			
			$stmtA="UPDATE vicidial_log_extended set start_url_processed='Y' where uniqueid='$uniqueid';";
			$affected_rows = $dbhA->do($stmtA);
			}

		##### END Call URL function #####
		}

	### insert a new url log entry
	$SQL_log = "$parse_url";
	$SQL_log =~ s/;|\|//gi;
	$stmtA = "INSERT INTO vicidial_url_log SET uniqueid='$uniqueid',url_date='$now_date',url_type='$url_function',url='$SQL_log',url_response='';";
	$affected_rows = $dbhA->do($stmtA);
	$stmtB = "SELECT LAST_INSERT_ID() LIMIT 1;";
	$sthA = $dbhA->prepare($stmtB) or die "preparing: ",$dbhA->errstr;
	$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
	$sthArows=$sthA->rows;
	if ($sthArows > 0)
		{
		@aryA = $sthA->fetchrow_array;
		$url_id = $aryA[0];
		}
	$sthA->finish();

	`$wgetbin -q --output-document=/tmp/ASUtmp$US$uniqueid$US$secX $parse_url `;

	$event_string="$function|$wgetbin -q --output-document=/tmp/ASUtmp$US$uniqueid$US$secX $parse_url|";
	&event_logger;

	### update url log entry
	$stmtA = "UPDATE vicidial_url_log SET url_response='/tmp/ASUtmp$US$uniqueid$US$secX' where url_log_id='$url_id';";
	$affected_rows = $dbhA->do($stmtA);
	}



my $secZ = time();
my $script_time = ($secZ - $secX);
print "DONE execute time: $script_time seconds\n";

exit 0;
# Program ends.



### Start of subroutines

sub event_logger
	{
	my ($tms) = @_;
	my($sec,$min,$hour,$mday,$mon,$year) = getTime($tms);
	$now_date = $year.'-'.$mon.'-'.$mday.' '.$hour.':'.$min.':'.$sec;
	$log_date = $year . '-' . $mon . '-' . $mday;
	if (!$ASULOGfile) {$ASULOGfile = "$PATHlogs/sendurl.$log_date";}

	if ($DB) {print "$now_date|$event_string|\n";}
	if ($SYSLOG)
		{
		### open the log file for writing ###
		open(Lout, ">>$ASULOGfile")
				|| die "Can't open $VDRLOGfile: $!\n";
		print Lout "$now_date|$event_string|\n";
		close(Lout);
		}
	$event_string='';
	}


# getTime usage:
#   getTime($SecondsSinceEpoch);
# Options:
#   $SecondsSinceEpoch : Request time in seconds, defaults to current date/time.
# Returns:
#   ($sec, $min, $hour. $day, $mon, $year)
sub getTime 
	{
	my ($tms) = @_;
	$tms = time unless ($tms);
	my($sec,$min,$hour,$mday,$mon,$year,$wday,$yday,$isdst)=localtime($tms);
	$year += 1900;
	$mon++;
	$mon = "0" . $mon if ($mon < 10);
	$mday = "0" . $mday if ($mday < 10);
	$min = "0" . $min if ($min < 10);
	$sec = "0" . $sec if ($sec < 10);
	return ($sec,$min,$hour,$mday,$mon,$year);
	}

### End of subs

