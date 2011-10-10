#!/usr/bin/perl
#
# Vtiger_OUT_sync2VICIDIAL_file.pl version 2.2.0
#
# DESCRIPTION:
# script exports all accounts from the vtiger system table from a PIPE-formatted
# file that is in the proper format for VICIDIAL. (for format see --help)
#
# Copyright (C) 2009  Matt Florell <vicidial@gmail.com>    LICENSE: AGPLv2
#
#
# CHANGES
# 90128-0319 - First build
# 90401-1347 - Fixed quiet flag
# 90417-0519 - Added custom field updates for timezone and last/largest sale amounts
# 90423-0453 - Added calls file and hours file reports
# 90424-1157 - Added orders file report
# 90517-1404 - Added date override and fixed order file report
# 90806-1654 - Added vicidial-owner-update option
#

$secX = time();
$MT[0]='';
$Ealert='';
$force_quiet=0;

($sec,$min,$hour,$mday,$mon,$year,$wday,$yday,$isdst) = localtime(time);
$year = ($year + 1900);
$mon++;
if ($hour < 10) {$hour = "0$hour";}
if ($min < 10) {$min = "0$min";}
if ($sec < 10) {$sec = "0$sec";}
if ($mon < 10) {$mon = "0$mon";}
if ($mday < 10) {$mday = "0$mday";}
$TODAY = "$year-$mon-$mday";
$pulldate0 = "$year-$mon-$mday $hour:$min:$sec";
$NOW_TIME = "$year-$mon-$mday $hour:$min:$sec";
$VDLfile = "Vtiger_account_sync_file_$year$mon$mday$hour$min$sec";
$inSD = $pulldate0;
$dsec = ( ( ($hour * 3600) + ($min * 60) ) + $sec );
$MT[0]='';


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
	if ( ($line =~ /^VARREPORT_host/) && ($CLIREPORT_host < 1) )
		{$VARREPORT_host = $line;   $VARREPORT_host =~ s/.*=//gi;}
	if ( ($line =~ /^VARREPORT_user/) && ($CLIREPORT_user < 1) )
		{$VARREPORT_user = $line;   $VARREPORT_user =~ s/.*=//gi;}
	if ( ($line =~ /^VARREPORT_pass/) && ($CLIREPORT_pass < 1) )
		{$VARREPORT_pass = $line;   $VARREPORT_pass =~ s/.*=//gi;}
	if ( ($line =~ /^VARREPORT_port/) && ($CLIREPORT_port < 1) )
		{$VARREPORT_port = $line;   $VARREPORT_port =~ s/.*=//gi;}
	$i++;
	}

# Customized Variables
$server_ip = $VARserver_ip;		# Asterisk server IP


if (!$VDHLOGfile) {$VDHLOGfile = "$PATHlogs/newleads.$year-$mon-$mday";}



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
		print "allowed run time options-\n";
		print " screen output options:\n";
		print "  [-q] = quiet\n";
		print "  [--test] = test\n";
		print "  [--debug] = debug output\n";
		print "  [--help] = this help screen\n";
		print " vicidial update options:\n";
		print "  [--skip-vicidial-update] = skips the lead file export and all ViciDial list updating functions\n";
		print "  [--vicidial-owner-update] = updates the vicidial_list.owner field with the vtiger owner\n";
		print "  [--format=standard] = ability to define a format, standard is default, formats allowed shown in examples\n";
		print "  [--forcelistid=1234] = overrides the listID given in the file with the 1234\n";
		print "  [--duplicate-system-check] = checks for the same phone number in the entire system before inserting lead\n";
		print "  [--duplicate-system-vendor] = checks for the same website in the entire system before inserting lead\n";
		print "  [--ftp-pull] = grabs lead files from a remote FTP server, uses REPORTS FTP login information\n";
		print "  [--ftp-dir=leads_in] = remote FTP server directory to grab files from, should have a DONE sub-directory\n";
		print " vtiger update options:\n";
		print "  [--vt-sales-update] = updates the Vtiger account custom sales fields based upon today sales order data\n";
		print "  [--vt-sales-update-alldate] = updates the Vtiger account custom sales fields based upon all sales order data\n";
		print "  [--vt-timezone-update] = updates the Vtiger account custom timezone field based upon ViciDial timezone/state\n";
		print "  [--vt-territory-update] = updates the Vtiger account tickersymbol field based upon ViciDial user territory\n";
		print "  [--vt-territory-restrict=3] = if above flag is present, will only update these records owned by this user_id\n";
		print " report generation options:\n";
		print "  [--report-call-file] = generates a spec call file from vicidial records\n";
		print "  [--report-hours-file] = generates a spec agent hours file from vicidial records\n";
		print "  [--report-orders-file] = generates a spec orders file from vtiger records\n";
		print "  [--date-override=YYYY-MM-DD] = run the report for a single day, must be formatted YYYY-MM-DD\n";
		print "  [--all-records] = outputs all records, not restricted by date\n";
		print " email options:\n";
		print "  [--email-list=test@test.com:test2@test.com] = send email results for each file to these addresses\n";
		print "  [--email-sender=vicidial@localhost] = sender for the email results\n";
		print "\n";
		print "This script takes in account CSV files in the following order when they are placed in the $PATHhome/VTIGER_IN directory to be imported into the vtiger system (examples):\n\n";
		print "standard:\n";
		print "vendor_lead_code|source_code|list_id|phone_code|phone_number|title|first_name|middle|last_name|address1|address2|address3|city|state|province|postal_code|country|gender|date_of_birth|alt_phone|email|security_phrase|COMMENTS|called_count|status|entry_date\n";
		print "3857822|31022|99|01144|1625551212|MRS|B||BURTON|249 MUNDON ROAD|MALDON|ESSEX||||CM9 6PW|UK||||||COMMENTS|2|B|2007-08-09 00:00:00|7275551212_1_work!7275551213_61_sister house!7275551214_44_neighbor\n\n";

		exit;
		}
	else
		{
		if ($args =~ /-q/i)
			{
			$q=1;
			$DB=0;
			$DBX=0;
			$force_quiet=1;
			}
		if ( ($args =~ /-debug/i) && ($force_quiet < 1) )
			{
			$DB=1;
			print "\n----- DEBUGGING -----\n\n";
			}
		if ( ($args =~ /-debugX/i) && ($force_quiet < 1) )
			{
			$DBX=1;
			print "\n----- SUPER-DUPER DEBUGGING -----\n\n";
			}
		else {$DBX=0;}

		if ($args =~ /-test/i)
			{
			$T=1;
			if ($q < 1) {print "\n----- TESTING -----\n\n";}
			}
		else
			{$T=0;}

		if ($args =~ /-format=/i)
			{
			@data_in = split(/-format=/,$args);
				$format = $data_in[1];
				$format =~ s/ .*//gi;
			if ($q < 1) {print "\n----- FORMAT OVERRIDE: $format -----\n\n";}
			}
		else
			{$format = 'standard';}

		if ($args =~ /-forcelistid=/i)
			{
			@data_in = split(/-forcelistid=/,$args);
				$forcelistid = $data_in[1];
				$forcelistid =~ s/ .*//gi;
			if ($q < 1) {print "\n----- FORCE LISTID OVERRIDE: $forcelistid -----\n\n";}
			}
		else
			{$forcelistid = '';}

		if ($args =~ /-duplicate-system-check/i)
			{
			$dupchecksys=1;
			if ($q < 1) {print "\n----- DUPLICATE SYSTEM CHECK PHONE -----\n\n";}
			}
		if ($args =~ /-duplicate-system-vendor/i)
			{
			$dupcheckvend=1;
			if ($q < 1) {print "\n----- DUPLICATE SYSTEM CHECK VENDOR -----\n\n";}
			}
		if ($args =~ /-vt-sales-update/i)
			{
			$vt_sales_update=1;
			if ($q < 1) {print "\n----- VTIGER SALES DATE FIELD UPDATE TODAY -----\n\n";}
			}
		if ($args =~ /-vt-sales-update-alldate/i)
			{
			$vt_sales_update_alldate=1;
			if ($q < 1) {print "\n----- VTIGER SALES DATE FIELD UPDATE ALLDATE -----\n\n";}
			}
		if ($args =~ /-vt-timezone-update/i)
			{
			$vt_timezone_update=1;
			if ($q < 1) {print "\n----- VTIGER TIMEZONE FIELD UPDATE -----\n\n";}
			}
		if ($args =~ /-vt-territory-update/i)
			{
			$vt_territory_update=1;
			if ($q < 1) {print "\n----- VTIGER USER TERRITORY UPDATE -----\n\n";}
			}

		if ($args =~ /--vt-territory-restrict=/i)
			{
			@data_in = split(/--vt-territory-restrict=/,$args);
				$vtur_user = $data_in[1];
				$vtur_user =~ s/ .*//gi;
			if ($q < 1) {print "\n----- TERRITORY UPDATE RESTRICTION: $vtur_user -----\n\n";}
			$VCuser_territory_update_restrictionSQL = " and smownerid='$vtur_user'";
			$VTuser_territory_update_restrictionSQL = " and user_id='$vtur_user'";
			}
		if ($args =~ /--report-call-file/i)
			{
			$report_call_file=1;
			if ($q < 1) {print "\n----- VTIGER REPORT CALL FILE -----\n\n";}
			}
		if ($args =~ /--report-hours-file/i)
			{
			$report_hours_file=1;
			if ($q < 1) {print "\n----- VTIGER REPORT HOURS FILE -----\n\n";}
			}
		if ($args =~ /--report-orders-file/i)
			{
			$report_orders_file=1;
			if ($q < 1) {print "\n----- VTIGER REPORT ORDERS FILE -----\n\n";}
			}
		if ($args =~ /--date-override=/i)
			{
			@data_in = split(/--date-override=/,$args);
				$TODAY = $data_in[1];
				$TODAY =~ s/ .*//gi;
			if ($q < 1) {print "\n----- DATE OVERRIDE: $TODAY -----\n\n";}
			}
		if ($args =~ /--all-records/i)
			{
			$TODAY = "1999-12-31";
			$ENDDAY = "2099-12-31";
			if ($q < 1) {print "\n----- REPORT ALL RECORDS -----\n\n";}
			}
		else
			{
			$ENDDAY = $TODAY;
			if ($q < 1) {print "\n----- DATE END DAY: $ENDDAY -----\n\n";}
			}
		if ($args =~ /-skip-vicidial-update/i)
			{
			$skip_vicidial_update=1;
			if ($q < 1) {print "\n----- SKIP VICIDIAL UPDATE -----\n\n";}
			}
		if ($args =~ /--vicidial-owner-update/i)
			{
			$vicidial_owner_update=1;
			if ($q < 1) {print "\n----- VICIDIAL OWNER UPDATE -----\n\n";}
			}
		if ($args =~ /-ftp-pull/i)
			{
			$ftp_pull=1;
			if ($q < 1) {print "\n----- FTP LEAD FILE PULL -----\n\n";}
			}
		if ($args =~ /--ftp-dir=/i)
			{
			@data_in = split(/--ftp-dir=/,$args);
				$ftp_dir = $data_in[1];
				$ftp_dir =~ s/ .*//gi;
			if ($q < 1) {print "\n----- REMOTE FTP DIRECTORY: $ftp_dir -----\n\n";}
			}
		else
			{$ftp_dir = '';}

		if ($args =~ /--email-list=/i)
			{
			@data_in = split(/--email-list=/,$args);
				$email_list = $data_in[1];
				$email_list =~ s/ .*//gi;
				$email_list =~ s/:/,/gi;
			if ($q < 1) {print "\n----- EMAIL NOTIFICATION: $email_list -----\n\n";}
			}
		else
			{$email_list = '';}

		if ($args =~ /--email-sender=/i)
			{
			@data_in = split(/--email-sender=/,$args);
				$email_sender = $data_in[1];
				$email_sender =~ s/ .*//gi;
				$email_sender =~ s/:/,/gi;
			if ($q < 1) {print "\n----- EMAIL NOTIFICATION SENDER: $email_sender -----\n\n";}
			}
		else
			{$email_sender = 'vicidial@localhost';}

		}
	}
else
	{
	print "no command line options set\n";
	$args = "";
	$i=0;
	$forcelistid = '';
	$format='standard';
	$vt_sales_update=0;
	$vt_sales_update_alldate=0;
	$vt_timezone_update=0;
	$skip_vicidial_update=0;
	$T=0;
	}
### end parsing run-time options ###

if ($q < 1)
	{
	print "\n\n\n\n\n\n\n\n\n\n\n\n-- Vtiger_OUT_sync2VICIDIAL_file.pl --\n\n";
	print "This program is designed to export a PIPE delimited file of the Accounts from Vtiger and format it for import into the VICIDIAL system. Also allows for other Vtiger data update functions.\n\n";
	}

$i=0;
$US = '_';
$phone_list = '|';

if (!$VARDB_port) {$VARDB_port='3306';}

use DBI;	  

$dbhA = DBI->connect("DBI:mysql:$VARDB_database:$VARDB_server:$VARDB_port", "$VARDB_user", "$VARDB_pass")
 or die "Couldn't connect to database: " . DBI->errstr;

#############################################
##### START SYSTEM_SETTINGS LOOKUP #####
$stmtA = "SELECT use_non_latin,vtiger_server_ip,vtiger_dbname,vtiger_login,vtiger_pass FROM system_settings;";
$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
$sthArows=$sthA->rows;
if ($sthArows > 0)
	{
	@aryA = $sthA->fetchrow_array;
	$non_latin = 		"$aryA[0]";
	$vtiger_server_ip =	"$aryA[1]";
	$vtiger_dbname = 	"$aryA[2]";
	$vtiger_login = 	"$aryA[3]";
	$vtiger_pass = 		"$aryA[4]";
	}
$sthA->finish();
##### END SETTINGS LOOKUP #####
###########################################

$dbhB = DBI->connect("DBI:mysql:$vtiger_dbname:$vtiger_server_ip:$VARDB_port", "$vtiger_login", "$vtiger_pass")
 or die "Couldn't connect to database: " . DBI->errstr;

if ($non_latin > 0) {$affected_rows = $dbhA->do("SET NAMES 'UTF8'");}

$list_id = '999';
$phone_code = '1';
$suf = '.txt';
$people_packages_id_update='';
$dir1 = "$PATHhome/LEADS_IN";
$dir2 = "$PATHhome";

if($DBX){print STDERR "\nLEADS_IN directory: |$dir1|\n";}


$i=0;	### each Account counter ###
$b=0;	### status of 'APPROVED' counter ###
$c=0;	### status of 'DECLINED' counter ###
$d=0;	### status of 'REFERRED' counter ###
$e=0;	### status of 'DUPLICATE' vendor counter ###
$f=0;	### number of 'DUPLICATE' phone counter ###
$g=0;	### number of leads with multi-alt-entries
$h=0;	### number of timezone updates
$j=0;	### number of saleamount updates
$m=0;	### number of exported records to file
$n=0;	### number of territory updates


### open the output file for writing ###
open(out, ">$dir2/$VDLfile")
		|| die "Can't open $VDLfile: $!\n";

$TODAYfile = $TODAY;
$TODAYfile =~ s/-//gi;

if ($report_call_file > 0) 
	{
	$VTcfWEBfile = "$PATHweb/vicidial/server_reports/$TODAYfile" . "_IMM_Call_File.txt";
	### open the output file for writing ###
	open(CFout, ">$VTcfWEBfile")
			|| die "Can't open $VTcfWEBfile: $!\n";
	}
if ($report_orders_file > 0) 
	{
	$VTofWEBfile = "$PATHweb/vicidial/server_reports/$TODAYfile" . "_IMM_Orders_File.txt";
	### open the output file for writing ###
	open(OFout, ">$VTofWEBfile")
			|| die "Can't open $VTofWEBfile: $!\n";
	}



##### BEGIN REPORT HOURS FILE #####
if ($report_hours_file > 0) 
	{
	$VThfWEBfile = "$PATHweb/vicidial/server_reports/$TODAYfile" . "_IMM_Hours_File.txt";
	### open the output file for writing ###
	open(HFout, ">$VThfWEBfile")
			|| die "Can't open $VThfWEBfile: $!\n";

	$VAL_exists=0;
	$stmtA = "SELECT count(*) FROM vicidial_agent_log where event_time >= \"$TODAY 00:00:00\" and event_time <= \"$ENDDAY 23:59:59\";";
	$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
	$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
	$sthArows=$sthA->rows;
	if ($DB) {print "$sthArows|$stmtA\n";}
	if ($sthArows > 0)
		{
		@aryA = $sthA->fetchrow_array;
		$VAL_exists = 		"$aryA[0]";
		}
	$sthA->finish();

	if ($VAL_exists > 0)
		{
		#  and vu.user_code LIKE \"IMM%\"
		$stmtA = "SELECT pause_sec,wait_sec,talk_sec,dispo_sec,sub_status,vu.user,user_code,campaign_id,comments FROM vicidial_agent_log val,vicidial_users vu where event_time >= \"$TODAY 00:00:00\" and event_time <= \"$ENDDAY 23:59:59\" and val.user=vu.user order by val.user;";
		$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
		$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
		$sthArows=$sthA->rows;
		if ($DB) {print "$sthArows|$stmtA\n";}
		$w=0;
		while ($sthArows > $w)
			{
			@aryA = $sthA->fetchrow_array;
			$pause_sec[$w] = 		"$aryA[0]";
			$wait_sec[$w] = 		"$aryA[1]";
			$talk_sec[$w] = 		"$aryA[2]";
			$dispo_sec[$w] = 		"$aryA[3]";
			$pause_code[$w] = 		"$aryA[4]";
			$user[$w] = 			"$aryA[5]";
			$rep[$w] = 				"$aryA[6]";
			$campaign[$w] = 		"$aryA[7]";
			$comments[$w] = 		"$aryA[8]";
			$w++;
			}
		$sthA->finish();

		$last_user='';
		$w=0;
		while ($sthArows >= $w)
			{
			if ( (length($last_user) != length($user[$w]) ) || ($last_user !~ /$user[$w]/) )
				{
				# rep id				user
				# work date				date yyyymmdd
				# bus line status id	??
				# vendor site id		15
				# time ob talk			outbound talk time in seconds
				# time ib talk			inbound talk time in seconds
				# time acw				dispo time in seconds
				# time onhook			pause time in seconds
				# time train			pause time training in seconds
				# time break			pause time break in seconds
				# time wait				wait time in seconds

				if ($DB) {print "printing $last_user|$Lrep\n";}

				if ($w > 0)
					{print HFout "$last_user,$TODAY,4,15,$L_ob_talk,$L_ib_talk,$L_acw,$L_onhook,$L_train,$L_break,$L_wait\n";}
				$last_user = $user[$w];
				$Lrep=$rep[$w];
				$L_ob_talk=0;
				$L_ib_talk=0;
				$L_acw=0;
				$L_onhook=0;
				$L_train=0;
				$L_break=0;
				$L_wait=0;
				$m++;
				}
			$billable[$w] = 'NO';
			if ( ($pause_code[$w] !~ /NULL/) && (length($pause_code[$w]) > 0) && ($pause_sec[$w] > 0) )
				{
				$stmtA = "SELECT billable from vicidial_pause_codes where campaign_id='$campaign[$w]' and pause_code='$pause_code[$w]';";
				$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
				$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
				$sthArowsX=$sthA->rows;
				if ($sthArowsX > 0)
					{
					@aryA = $sthA->fetchrow_array;
					$billable[$w] = 		"$aryA[0]";
					}
				$sthA->finish();
				}

			if ($comments[$w] =~ /MANUAL|AUTO/) {$L_ob_talk = ($L_ob_talk + $talk_sec[$w]);}
			else  {$L_ib_talk = ($L_ib_talk + $talk_sec[$w]);}
			$L_acw = ($L_acw + $dispo_sec[$w]);
			$L_wait = ($L_wait + $wait_sec[$w]);
			$pause_logged=0;
			if ( ($pause_code[$w] !~ /NULL/i) && (length($pause_code[$w])>0) ) 
				{
				if ( ($pause_code[$w] =~ /train|meet/i) && ($billable[$w] !~ /NO/) ) 
					{
					$L_train = ($L_train + $pause_sec[$w]);
					$pause_logged++;
					}
				if ( ($pause_code[$w] =~ /break|lunch/i) && ($billable[$w] !~ /NO/) ) 
					{
					$L_break = ($L_break + $pause_sec[$w]);
					$pause_logged++;
					}
				}
			if ($pause_logged < 1)
				{$L_onhook = ($L_onhook + $pause_sec[$w]);}

			$w++;
			}

		}

	exit;
	}
##### END REPORT HOURS FILE #####





$vt_timezone_field_exists=0;
if ($vt_timezone_update > 0)
	{
	$stmtB="SELECT count(*) from vtiger_field where fieldlabel='Timezone';";
	$sthB = $dbhB->prepare($stmtB) or die "preparing: ",$dbhB->errstr;
	$sthB->execute or die "executing: $stmtB ", $dbhB->errstr;
	$sthBrows=$sthB->rows;
	if ($sthBrows > 0)
		{
		@aryB = $sthB->fetchrow_array;
		$vt_timezone_field_exists = "$aryB[0]";
		}
	$sthB->finish();

	if ($vt_timezone_field_exists > 0)
		{
		$stmtB="SELECT fieldname from vtiger_field where fieldlabel='Timezone';";
		$sthB = $dbhB->prepare($stmtB) or die "preparing: ",$dbhB->errstr;
		$sthB->execute or die "executing: $stmtB ", $dbhB->errstr;
		$sthBrows=$sthB->rows;
		if ($sthBrows > 0)
			{
			@aryB = $sthB->fetchrow_array;
			$vt_timezone_field_name = "$aryB[0]";
			}
		$sthB->finish();

		if ($q < 1) {print "Vtiger Timezone field found: $vt_timezone_field_name, starting updates...\n";}
		}
	}



$vt_last_saleamount_field_exists=0;
$vt_largest_saleamount_field_exists=0;
if ( ($vt_sales_update > 0) || ($vt_sales_update_alldate > 0) )
	{
	$stmtB="SELECT count(*) from vtiger_field where fieldlabel='Last Sale Amount';";
	$sthB = $dbhB->prepare($stmtB) or die "preparing: ",$dbhB->errstr;
	$sthB->execute or die "executing: $stmtB ", $dbhB->errstr;
	$sthBrows=$sthB->rows;
	if ($sthBrows > 0)
		{
		@aryB = $sthB->fetchrow_array;
		$vt_last_saleamount_field_exists = "$aryB[0]";
		}
	$sthB->finish();

	$stmtB="SELECT count(*) from vtiger_field where fieldlabel='Largest Sale Amount';";
	$sthB = $dbhB->prepare($stmtB) or die "preparing: ",$dbhB->errstr;
	$sthB->execute or die "executing: $stmtB ", $dbhB->errstr;
	$sthBrows=$sthB->rows;
	if ($sthBrows > 0)
		{
		@aryB = $sthB->fetchrow_array;
		$vt_largest_saleamount_field_exists = "$aryB[0]";
		}
	$sthB->finish();

	if ($vt_last_saleamount_field_exists > 0)
		{
		$stmtB="SELECT fieldname from vtiger_field where fieldlabel='Last Sale Amount';";
		$sthB = $dbhB->prepare($stmtB) or die "preparing: ",$dbhB->errstr;
		$sthB->execute or die "executing: $stmtB ", $dbhB->errstr;
		$sthBrows=$sthB->rows;
		if ($sthBrows > 0)
			{
			@aryB = $sthB->fetchrow_array;
			$vt_last_saleamount_field_name = "$aryB[0]";
			}
		$sthB->finish();

		if ($q < 1) {print "Vtiger Last Sale Amount field found: $vt_last_saleamount_field_name, starting updates...\n";}
		}
	if ($vt_largest_saleamount_field_exists > 0)
		{
		$stmtB="SELECT fieldname from vtiger_field where fieldlabel='Largest Sale Amount';";
		$sthB = $dbhB->prepare($stmtB) or die "preparing: ",$dbhB->errstr;
		$sthB->execute or die "executing: $stmtB ", $dbhB->errstr;
		$sthBrows=$sthB->rows;
		if ($sthBrows > 0)
			{
			@aryB = $sthB->fetchrow_array;
			$vt_largest_saleamount_field_name = "$aryB[0]";
			}
		$sthB->finish();

		if ($q < 1) {print "Vtiger Largest Sale Amount field found: $vt_largest_saleamount_field_name, starting updates...\n";}
		}
	}



### Gather all non-deleted Accounts in Vtiger
$stmtB="SELECT crmid,smownerid from vtiger_crmentity where setype='Accounts' and deleted='0' order by crmid limit 3000000;";
$sthB = $dbhB->prepare($stmtB) or die "preparing: ",$dbhB->errstr;
$sthB->execute or die "executing: $stmtB ", $dbhB->errstr;
$sthBrowsC=$sthB->rows;
$i=0;
while ($sthBrowsC > $i)
	{
	@aryB = $sthB->fetchrow_array;
	$crmid[$i] =		$aryB[0];
	$smownerid[$i] =	$aryB[1];
	$i++;
	}
$sthB->finish();

### Gather all account info and address info on those accounts, also checking for duplicates in vicidial_list if selected
$i=0;
while ($sthBrowsC > $i)
	{




	##### BEGIN TIMEZONE UPDATE #####
	if ( ($vt_timezone_update > 0) && ($vt_timezone_field_exists > 0) )
		{
		$VL_exists=0;
		$stmtA = "SELECT count(*) FROM vicidial_list where vendor_lead_code='$crmid[$i]';";
		$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
		$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
		$sthArows=$sthA->rows;
		if ($sthArows > 0)
			{
			@aryA = $sthA->fetchrow_array;
			$VL_exists = 		"$aryA[0]";
			}
		$sthA->finish();

		if ($VL_exists > 0)
			{
			$stmtA = "SELECT state,gmt_offset_now FROM vicidial_list where vendor_lead_code='$crmid[$i]';";
			$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
			$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
			$sthArows=$sthA->rows;
			if ($sthArows > 0)
				{
				@aryA = $sthA->fetchrow_array;
				$VL_state = 		"$aryA[0]";
				$VL_timezone = 		"$aryA[1]";
				}
			$sthA->finish();

			$tz_set=0;
			$timezone_abb = 'NONE';
			if ($VL_state =~ /AZ/)												{$timezone_abb = 'MST';   $tz_set=1;}
			if ( ($VL_state =~ /HI/) && ($tz_set < 1) )							{$timezone_abb = 'HST';   $tz_set=1;}
			if ( ($VL_timezone == "-10") && ($isdst < 1) && ($tz_set < 1) )		{$timezone_abb = 'HST';   $tz_set=1;}
			if ( ($VL_timezone == "-9") && ($isdst < 1) && ($tz_set < 1) )		{$timezone_abb = 'AKT';   $tz_set=1;}
			if ( ($VL_timezone == "-8") && ($isdst < 1) && ($tz_set < 1) )		{$timezone_abb = 'PST';   $tz_set=1;}
			if ( ($VL_timezone == "-7") && ($isdst < 1) && ($tz_set < 1) )		{$timezone_abb = 'MST';   $tz_set=1;}
			if ( ($VL_timezone == "-6") && ($isdst < 1) && ($tz_set < 1) )		{$timezone_abb = 'CST';   $tz_set=1;}
			if ( ($VL_timezone == "-5") && ($isdst < 1) && ($tz_set < 1) )		{$timezone_abb = 'EST';   $tz_set=1;}
			if ( ($VL_timezone == "-4") && ($isdst < 1) && ($tz_set < 1) )		{$timezone_abb = 'AST';   $tz_set=1;}
			if ( ($VL_timezone == "-3.5") && ($isdst < 1) && ($tz_set < 1) )	{$timezone_abb = 'NST';   $tz_set=1;}
			if ( ($VL_timezone == "10") && ($isdst < 1) && ($tz_set < 1) )		{$timezone_abb = 'ChST';  $tz_set=1;}
			if ( ($VL_timezone == "-9") && ($isdst > 0) && ($tz_set < 1) )		{$timezone_abb = 'HDT';   $tz_set=1;}
			if ( ($VL_timezone == "-8") && ($isdst > 0) && ($tz_set < 1) )		{$timezone_abb = 'ADT';   $tz_set=1;}
			if ( ($VL_timezone == "-7") && ($isdst > 0) && ($tz_set < 1) )		{$timezone_abb = 'PDT';   $tz_set=1;}
			if ( ($VL_timezone == "-6") && ($isdst > 0) && ($tz_set < 1) )		{$timezone_abb = 'MDT';   $tz_set=1;}
			if ( ($VL_timezone == "-5") && ($isdst > 0) && ($tz_set < 1) )		{$timezone_abb = 'CDT';   $tz_set=1;}
			if ( ($VL_timezone == "-4") && ($isdst > 0) && ($tz_set < 1) )		{$timezone_abb = 'EDT';   $tz_set=1;}
			if ( ($VL_timezone == "-3") && ($isdst > 0) && ($tz_set < 1) )		{$timezone_abb = 'ADT';   $tz_set=1;}
			if ( ($VL_timezone == "-2.5") && ($isdst > 0) && ($tz_set < 1) )	{$timezone_abb = 'NDT';   $tz_set=1;}

			$stmtB = "UPDATE vtiger_accountscf SET $vt_timezone_field_name='$timezone_abb' where accountid='$crmid[$i]';";
				if ($T < 1) {$affected_rowsB = $dbhB->do($stmtB)    or die  "Couldn't execute query: |$stmtB|\n";}
				if($DB){print "|$affected_rowsB|$stmtB|\n";}
			$h = ($affected_rowsB + $h);
			}
		}
	##### END TIMEZONE UPDATE #####



	##### BEGIN USER TERRITORY UPDATE #####
	if ($vt_territory_update > 0)
		{
		$stmtB="SELECT tickersymbol from vtiger_account where accountid='$crmid[$i]';";
			if($DBX){print STDERR "\n|$stmtB|\n";}
		$sthB = $dbhB->prepare($stmtB) or die "preparing: ",$dbhB->errstr;
		$sthB->execute or die "executing: $stmtB ", $dbhB->errstr;
		$sthBrows=$sthB->rows;
		if ($sthBrows > 0)
			{
			@aryB = $sthB->fetchrow_array;
			$territory =		$aryB[0];
			}
		$sthB->finish();

		if (length($territory)>0)
			{
			$VL_exists=0;
			$stmtA = "SELECT count(*) FROM vicidial_user_territories where territory='$territory';";
			$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
			$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
			$sthArows=$sthA->rows;
			if ($sthArows > 0)
				{
				@aryA = $sthA->fetchrow_array;
				$VL_exists = 		"$aryA[0]";
				}
			$sthA->finish();

			if ($VL_exists > 0)
				{
				$user='';
				$stmtA = "SELECT user FROM vicidial_user_territories where territory='$territory' order by level desc,user;";
				$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
				$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
				$sthArows=$sthA->rows;
				if ($sthArows > 0)
					{
					@aryA = $sthA->fetchrow_array;
					$user = 		"$aryA[0]";
					}
				$sthA->finish();

				if (length($territory)>0)
					{
					$user_id='';
					$stmtB="SELECT id from vtiger_users where user_name='$user';";
						if($DBX){print STDERR "\n|$stmtB|\n";}
					$sthB = $dbhB->prepare($stmtB) or die "preparing: ",$dbhB->errstr;
					$sthB->execute or die "executing: $stmtB ", $dbhB->errstr;
					$sthBrows=$sthB->rows;
					if ($sthBrows > 0)
						{
						@aryB = $sthB->fetchrow_array;
						$user_id =	$aryB[0];
						}
					$sthB->finish();

					if (length($user_id)>0)
						{
						$stmtB = "UPDATE vtiger_crmentity SET smownerid='$user_id',smcreatorid='$user_id',modifiedby='$user_id' where crmid='$crmid[$i]' $VCuser_territory_update_restrictionSQL;";
							if ($T < 1) {$affected_rowsB = $dbhB->do($stmtB)    or die  "Couldn't execute query: |$stmtB|\n";}
							if($DB){print "|$affected_rowsB|$stmtB|\n";}

						$stmtB = "UPDATE vtiger_tracker SET user_id='$user_id' where item_id='$crmid[$i]' $VTuser_territory_update_restrictionSQL;";
							if ($T < 1) {$affected_rowsB = $dbhB->do($stmtB)    or die  "Couldn't execute query: |$stmtB|\n";}
							if($DB){print "|$affected_rowsB|$stmtB|\n";}
						$n++;
						}
					}
				}
			}
		}
	##### END USER TERRITORY UPDATE #####



	##### BEGIN SALE AMOUNT UPDATE #####
	if ( ( ($vt_sales_update > 0) || ($vt_sales_update_alldate > 0) ) && ( ($vt_last_saleamount_field_exists > 0) && ($vt_largest_saleamount_field_exists > 0) ) )
		{
		$last_sale='0.00';
		$largest_sale='0.00';
		if ($vt_sales_update > 0) {$duedateSQL = "and duedate='$crmid[$i]'";}
		if ($vt_sales_update_alldate > 0) {$duedateSQL = "";}

		$VS_exists=0;
		$stmtB="SELECT count(*) from vtiger_salesorder where accountid='$crmid[$i]' $duedateSQL order by duedate;";
			if($DBX){print STDERR "\n|$stmtB|\n";}
		$sthB = $dbhB->prepare($stmtB) or die "preparing: ",$dbhB->errstr;
		$sthB->execute or die "executing: $stmtB ", $dbhB->errstr;
		$sthBrows=$sthB->rows;
		if ($sthBrows > 0)
			{
			@aryB = $sthB->fetchrow_array;
			$VS_exists =		$aryB[0];
			}
		$sthB->finish();

		if ($VS_exists > 0)
			{
			$k=0;
			$stmtB="SELECT total from vtiger_salesorder where accountid='$crmid[$i]' $duedateSQL order by duedate;";
				if($DBX){print STDERR "\n|$stmtB|\n";}
			$sthB = $dbhB->prepare($stmtB) or die "preparing: ",$dbhB->errstr;
			$sthB->execute or die "executing: $stmtB ", $dbhB->errstr;
			$sthBrows=$sthB->rows;
			while ($sthBrows > $k)
				{
				@aryB = $sthB->fetchrow_array;
				$last_sale =		$aryB[0];
				if ($aryB[0] > $largest_sale) {$largest_sale = $aryB[0];}
				$k++;
				}
			$sthB->finish();

			$stmtB = "UPDATE vtiger_accountscf SET $vt_largest_saleamount_field_name='$largest_sale',$vt_last_saleamount_field_name='$last_sale' where accountid='$crmid[$i]';";
				if ($T < 1) {$affected_rowsB = $dbhB->do($stmtB)    or die  "Couldn't execute query: |$stmtB|\n";}
				if($DB){print "|$affected_rowsB|$stmtB|\n";}
			$j = ($affected_rowsB + $j);
			}
		}
	##### END SALE AMOUNT UPDATE #####





	##### BEGIN REPORT CALL FILE #####
	if ($report_call_file > 0)
		{
		$VC_count=0;
		$stmtA = "SELECT count(*) FROM vicidial_list where vendor_lead_code='$crmid[$i]' and last_local_call_time >= \"$TODAY 00:00:00\" and last_local_call_time <= \"$ENDDAY 23:59:59\";";
		$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
		$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
		$sthArows=$sthA->rows;
		if ($sthArows > 0)
			{
			@aryA = $sthA->fetchrow_array;
			$VC_count = 		"$aryA[0]";
			}
		$sthA->finish();

		if ($VC_count > 0)
			{
			$stmtB="SELECT website,tickersymbol,employees from vtiger_account where accountid='$crmid[$i]';";
				if($DBX){print STDERR "\n|$stmtB|\n";}
			$sthB = $dbhB->prepare($stmtB) or die "preparing: ",$dbhB->errstr;
			$sthB->execute or die "executing: $stmtB ", $dbhB->errstr;
			$sthBrows=$sthB->rows;
			if ($sthBrows > 0)
				{
				@aryB = $sthB->fetchrow_array;
				$website =			$aryB[0];
					@webARY =			split(/-/,$website);
					$account =			$webARY[0];
					$sequence =			$webARY[1];
				$territory =		$aryB[1];
				$business_line =	$aryB[2];
				}
			$sthB->finish();

			$lead_id='';
			$stmtA = "SELECT lead_id FROM vicidial_list where vendor_lead_code='$crmid[$i]' and last_local_call_time >= \"$TODAY 00:00:00\" and last_local_call_time <= \"$ENDDAY 23:59:59\" order by lead_id desc limit 1;";
			$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
			$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
			$sthArows=$sthA->rows;
			if ($sthArows > 0)
				{
				@aryA = $sthA->fetchrow_array;
				$lead_id = 		"$aryA[0]";
				}
			$sthA->finish();

			$VL_count=0;
			$stmtA = "SELECT count(*) FROM vicidial_log where lead_id='$lead_id' and call_date >= \"$TODAY 00:00:00\" and call_date <= \"$ENDDAY 23:59:59\";";
			$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
			$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
			$sthArows=$sthA->rows;
			if ($sthArows > 0)
				{
				@aryA = $sthA->fetchrow_array;
				$VL_count = 	"$aryA[0]";
				}
			$sthA->finish();

			$VCL_count=0;
			$stmtA = "SELECT count(*) FROM vicidial_closer_log where lead_id='$lead_id' and call_date >= \"$TODAY 00:00:00\" and call_date <= \"$ENDDAY 23:59:59\";";
			$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
			$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
			$sthArows=$sthA->rows;
			if ($sthArows > 0)
				{
				@aryA = $sthA->fetchrow_array;
				$VCL_count = 	"$aryA[0]";
				}
			$sthA->finish();

			# transaction id		vicidial_log.uniqueid
			# vendor site id		15
			# rep id				vicidial_users.user_code
			# account id			first half of the vtiger_account.website field
			# sequence				second half of the vtiger_account.website field
			# territory id			vtiger_account.tickersymbol
			# phone number			vicidial_log.phone_number
			# disposition code		vicidial_log.status
			# call length (s)		vicidial_log.length_in_sec
			# call date				vicidial_log.call_date
			# call type				vicidial_log.comments turned into a numeric value
			# bus line status id	vtiger_account.employees

			if ($VL_count > 0)
				{
				$stmtA = "SELECT uniqueid,vl.user,user_code,phone_number,vl.status,length_in_sec,call_date,comments FROM vicidial_log vl,vicidial_users vu where lead_id='$lead_id' and call_date >= \"$TODAY 00:00:00\" and call_date <= \"$ENDDAY 23:59:59\" and vl.user=vu.user order by call_date;";
				$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
				$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
				$sthArows=$sthA->rows;
				$w=0;
				while ($sthArows > $w)
					{
					@aryA = $sthA->fetchrow_array;
					$transaction = 		"$aryA[0]";
					$user = 			"$aryA[1]";
					$rep =		 		"$aryA[2]";
					$phone =			"$aryA[3]";
					$disposition = 		"$aryA[4]";
					$call_length = 		"$aryA[5]";
					$call_date = 		"$aryA[6]";
					$call_type = 		"$aryA[7]";
					if ($call_type =~ /AUTO/) {$ct='3';}
					else {$ct='2';}

					print CFout "$transaction,15,$user,$account,$sequence,$territory,$phone,$disposition,$call_length,$call_date,$ct\n";
					$m++;
					$w++;
					}
				$sthA->finish();
				}

			if ($VCL_count > 0)
				{
				$stmtA = "SELECT closecallid,vcl.user,user_code,phone_number,vcl.status,length_in_sec,call_date,comments FROM vicidial_closer_log vcl,vicidial_users vu where lead_id='$lead_id' and call_date >= \"$TODAY 00:00:00\" and call_date <= \"$ENDDAY 23:59:59\" and vcl.user=vu.user order by call_date;";
				$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
				$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
				$sthArows=$sthA->rows;
				$w=0;
				while ($sthArows > $w)
					{
					@aryA = $sthA->fetchrow_array;
					$transaction = 		"$aryA[0]";
					$user = 			"$aryA[1]";
					$rep =		 		"$aryA[2]";
					$phone =			"$aryA[3]";
					$disposition = 		"$aryA[4]";
					$call_length = 		"$aryA[5]";
					$call_date = 		"$aryA[6]";
					$call_type = 		"$aryA[7]";

					print CFout "$transaction,15,$user,$account,$sequence,$territory,$phone,$disposition,$call_length,$call_date,1\n";
					$m++;
					$w++;
					}
				$sthA->finish();
				}
			}
		}
	##### END REPORT CALL FILE #####




	##### BEGIN REPORT ORDERS FILE #####
	if ($report_orders_file > 0)
		{
		$VS_exists=0;
		$stmtB="SELECT count(*) from vtiger_crmentity vc,vtiger_salesorder vs where setype='SalesOrder' and accountid='$crmid[$i]' and createdtime >= \"$TODAY 00:00:00\" and createdtime <= \"$ENDDAY 23:59:59\" and vc.crmid=vs.salesorderid;";
			if($DBX){print STDERR "\n|$stmtB|\n";}
		$sthB = $dbhB->prepare($stmtB) or die "preparing: ",$dbhB->errstr;
		$sthB->execute or die "executing: $stmtB ", $dbhB->errstr;
		$sthBrows=$sthB->rows;
		if ($sthBrows > 0)
			{
			@aryB = $sthB->fetchrow_array;
			$VS_exists =		$aryB[0];
			}
		$sthB->finish();

		if ($VS_exists > 0)
			{
			$stmtB="SELECT website,phone,accountname,ownership,siccode from vtiger_account where accountid='$crmid[$i]';";
				if($DBX){print STDERR "\n|$stmtB|\n";}
			$sthB = $dbhB->prepare($stmtB) or die "preparing: ",$dbhB->errstr;
			$sthB->execute or die "executing: $stmtB ", $dbhB->errstr;
			$sthBrows=$sthB->rows;
			if ($sthBrows > 0)
				{
				@aryB = $sthB->fetchrow_array;
				$website =			$aryB[0];
					@webARY =			split(/-/,$website);
					$account =			$webARY[0];
					$sequence =			$webARY[1];
				$phone =			$aryB[1];
				$company =			$aryB[2];
				$ownership =		$aryB[3];
					@ownARY =			split(/ /,$ownership);
					$first_name =			$ownARY[0];
					$last_name =			$ownARY[1];
				$VCstatus =			$aryB[4];
				}
			$sthB->finish();

			$stmtA = "SELECT status FROM vicidial_statuses where status_name='$VCstatus';";
			$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
			$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
			$sthArows=$sthA->rows;
			if ($sthArows > 0)
				{
				@aryA = $sthA->fetchrow_array;
				$VCstatus = 	"$aryA[0]";
				$sthA->finish();
				}
			else
				{
				$sthA->finish();
				$stmtA = "SELECT status FROM vicidial_campaign_statuses where status_name='$VCstatus';";
				$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
				$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
				$sthArows=$sthA->rows;
				if ($sthArows > 0)
					{
					@aryA = $sthA->fetchrow_array;
					$VCstatus = 	"$aryA[0]";
					$sthA->finish();
					}
				}
			$sthA->finish();

			$k=0;
			$stmtB="SELECT salesorderid,total,duedate,smownerid,user_name from vtiger_salesorder vso, vtiger_crmentity vce, vtiger_users vu where accountid='$crmid[$i]' and createdtime >= \"$TODAY 00:00:00\" and createdtime <= \"$ENDDAY 23:59:59\" and vso.salesorderid=vce.crmid and vu.id=vce.smownerid order by duedate;";
				if($DBX){print STDERR "\n|$stmtB|\n";}
			$sthB = $dbhB->prepare($stmtB) or die "preparing: ",$dbhB->errstr;
			$sthB->execute or die "executing: $stmtB ", $dbhB->errstr;
			$sthBrows=$sthB->rows;
			while ($sthBrows > $k)
				{
				@aryB = $sthB->fetchrow_array;
				$salesorderid = $aryB[0];
				$total =		$aryB[1];
					chop($total);
				$duedate =		$aryB[2];
				$so_owner =		$aryB[3];
				$user =			$aryB[4];

				print OFout "$salesorderid,$phone,$account,$sequence,$company,$first_name,$last_name,$VCstatus,$duedate,$total,$user\n";
				$m++;
				$k++;
				}
			$sthB->finish();
			}

		}
	##### END REPORT ORDERS FILE #####





	##### BEGIN VICIDIAL UPDATE #####
	if ($skip_vicidial_update < 1)
		{
		$VL_dup=0;
		$VL_phone_dup=0;

		$stmtB="SELECT accountname,ownership,siccode,annualrevenue,tickersymbol,phone,otherphone,fax,email1,website from vtiger_account where accountid='$crmid[$i]';";
			if($DBX){print STDERR "\n|$stmtB|\n";}
		$sthB = $dbhB->prepare($stmtB) or die "preparing: ",$dbhB->errstr;
		$sthB->execute or die "executing: $stmtB ", $dbhB->errstr;
		$sthBrows=$sthB->rows;
		if ($sthBrows > 0)
			{
			@aryB = $sthB->fetchrow_array;
			$accountname =		$aryB[0];
			$ownership =		$aryB[1];
			$siccode =			$aryB[2];
			$annualrevenue =	$aryB[3];
			$tickersymbol =		$aryB[4];
			$phone =			$aryB[5];
			$otherphone =		$aryB[6];
			$fax =				$aryB[7];
			$email1 =			$aryB[8];
			$website =			$aryB[9];
				$accountname =~ s/'//gi;
				$ownership =~ s/'//gi;
				$siccode =~ s/'//gi;
				$annualrevenue =~ s/'//gi;
				$tickersymbol =~ s/'//gi;
				$phone =~ s/'//gi;
				$otherphone =~ s/'//gi;
				$fax =~ s/'//gi;
				$email1 =~ s/'//gi;
				$website =~ s/'//gi;
			}
		$sthB->finish();

		$stmtB="SELECT bill_city,bill_code,bill_country,bill_state,bill_street,bill_pobox from vtiger_accountbillads where accountaddressid='$crmid[$i]';";
			if($DBX){print STDERR "\n|$stmtB|\n";}
		$sthB = $dbhB->prepare($stmtB) or die "preparing: ",$dbhB->errstr;
		$sthB->execute or die "executing: $stmtB ", $dbhB->errstr;
		$sthBrows=$sthB->rows;
		if ($sthBrows > 0)
			{
			@aryB = $sthB->fetchrow_array;
			$bill_city =		$aryB[0];
			$bill_code =		$aryB[1];
			$bill_country =		$aryB[2];
			$bill_state =		$aryB[3];
			$bill_street =		$aryB[4];
			$bill_pobox =		$aryB[5];
				$bill_city =~ s/'//gi;
				$bill_code =~ s/'//gi;
				$bill_street =~ s/'//gi;
			}
		$sthB->finish();

		if ($dupchecksys > 0)
			{
			$stmtA = "SELECT count(*) FROM vicidial_list where phone_number='$phone';";
			$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
			$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
			$sthArows=$sthA->rows;
			if ($sthArows > 0)
				{
				@aryA = $sthA->fetchrow_array;
				$VL_phone_dup = 		"$aryA[0]";
				}
			$sthA->finish();
			}
		if ($dupcheckvend > 0)
			{
			$stmtA = "SELECT count(*) FROM vicidial_list where vendor_lead_code='$crmid[$i]';";
			$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
			$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
			$sthArows=$sthA->rows;
			if ($sthArows > 0)
				{
				@aryA = $sthA->fetchrow_array;
				$VL_dup = 		"$aryA[0]";
				}
			$sthA->finish();
			}

		$vicidial_owner_updateSQL='';
		$vicidial_owner_updateFILE='';
		if ($vicidial_owner_update > 0)
			{
			$user='';
			$stmtB="SELECT user_name from vtiger_users where id='$smownerid[$i]';";
				if($DBX){print STDERR "\n|$stmtB|\n";}
			$sthB = $dbhB->prepare($stmtB) or die "preparing: ",$dbhB->errstr;
			$sthB->execute or die "executing: $stmtB ", $dbhB->errstr;
			$sthBrows=$sthB->rows;
			if ($sthBrows > 0)
				{
				@aryB = $sthB->fetchrow_array;
				$user =	$aryB[0];
				}
			$sthB->finish();

			$vicidial_owner_updateSQL = ",owner='$user'";
			$vicidial_owner_updateFILE = "|||||$user";
			}

		if ($VL_phone_dup > 0)
			{
			if($DB){print "DUPLICATE PHONE: $phone|$crmid[$i]\n";}
			$f++;
			$affected_rowsA=0;
			### update the existing vicidial_list entry ###
			$stmtA = "UPDATE vicidial_list SET first_name='$accountname',last_name='$ownership',address1='$bill_street',address2='$bill_pobox',city='$bill_city',state='$bill_state',postal_code='$bill_code',country='$bill_country',vendor_lead_code='$crmid[$i]',address3='$fax',alt_phone='$otherphone',email='$email1',province='$website',security_phrase='$tickersymbol',comments='$siccode|$annualrevenue' $vicidial_owner_updateSQL where phone_number='$phone' limit 1;";
				if ($T < 1) {$affected_rowsA = $dbhA->do($stmtA); } #  or die  "Couldn't execute query: |$stmtB|\n";
				if($DB){print STDERR "\n|$affected_rowsA|$stmtA|\n";}
			$c = ($affected_rowsA + $c);
			}
		else
			{
			if ($VL_dup > 0)
				{
				if($DB){print "DUPLICATE VENDOR_ID: $crmid[$i]\n";}
				$e++;
				$affected_rowsA=0;
				### update the existing vicidial_list entry ###
				$stmtA = "UPDATE vicidial_list SET first_name='$accountname',last_name='$ownership',address1='$bill_street',address2='$bill_pobox',city='$bill_city',state='$bill_state',postal_code='$bill_code',country_code='$bill_country',phone_number='$phone',address3='$fax',alt_phone='$otherphone',email='$email1',province='$website',security_phrase='$tickersymbol',comments='$siccode $annualrevenue' $vicidial_owner_updateSQL where vendor_lead_code='$crmid[$i]' limit 1;";
					if ($T < 1) {$affected_rowsA = $dbhA->do($stmtA); } #  or die  "Couldn't execute query: |$stmtB|\n";
					if($DB){print STDERR "\n|$affected_rowsA|$stmtA|\n";}
				$c = ($affected_rowsA + $c);
				}
			else
				{
				### print the output file in proper format
				print out "$crmid[$i]||$list_id|$phone_code|$phone||$accountname||$ownership|$bill_street|$bill_pobox|$fax|$bill_city|$bill_state|$website|$bill_code|$bill_country|||$otherphone|$email1|$ticketsymbol|$siccode $annualrevenue$vicidial_owner_updateFILE\n";
				$b++;
				}
			}
		} 
	##### END VICIDIAL UPDATE #####

	$i++;

	if ($q < 1) 
		{
		if ($i =~ /10$/i) {print STDERR "0     $i\r";}
		if ($i =~ /20$/i) {print STDERR "+     $i\r";}
		if ($i =~ /30$/i) {print STDERR "|     $i\r";}
		if ($i =~ /40$/i) {print STDERR "\\     $i\r";}
		if ($i =~ /50$/i) {print STDERR "-     $i\r";}
		if ($i =~ /60$/i) {print STDERR "/     $i\r";}
		if ($i =~ /70$/i) {print STDERR "|     $i\r";}
		if ($i =~ /80$/i) {print STDERR "+     $i\r";}
		if ($i =~ /90$/i) {print STDERR "0     $i\r";}
		if ($i =~ /00$/i) {print "$i|$b|$c|$d|$e|$f|$g|$h|$n|$crmid[$i]|$phone_number|\n";}
		}
	}



	### open the stats out file for writing ###
	open(Sout, ">>$VDHLOGfile")
			|| die "Can't open $VDHLOGfile: $!\n";


	### close file handler and DB connections ###
	$Falert  = "\n\nTOTALS FOR $FILEname:\n";
	$Falert .= "Records in System:  $i\n";
	$Falert .= "LINES OUTPUTED:     $b\n";
	$Falert .= "LIVE VL UPDATE:     $c\n";
	$Falert .= "ERROR:              $e\n";
#	$Falert .= "MULTI-ALT-PHONE:    $g\n";
	if ($e > 0)
		{$Falert .= "VENDOR DUPLICATES:  $e\n";}
	if ($f > 0)
		{$Falert .= "PHONE DUPLICATES:   $f\n";}
	$Falert .= "TIMEZONE UPDATES:   $h\n";
	$Falert .= "SALE AMOUNT UPDATES:$j\n";
	$Falert .= "TERRITORY UPDATES:  $n\n";
	$Falert .= "REPORT RECORDS:     $m\n";

	if ($q < 1) {print "$Falert";}
	print Sout "$Falert";
	$Ealert .= "$Falert";

	close(out);
	close(Sout);
	chmod 0777, "$VDHLOGfile";

	### Move file to the LEADS_IN directory locally
	if ($T < 1) {`mv -f $dir2/$VDLfile $dir1/$VDLfile`;}


$dbhA->disconnect();
$dbhB->disconnect();

### calculate time to run script ###
$secY = time();
$secZ = ($secY - $secX);
$secZm = ($secZ /60);

if ($q < 1)
	{
	print "script execution time in seconds: $secZ     minutes: $secZm\n";
	}

###### EMAIL SECTION
$email_sent=0;
if ( ($report_call_file > 0) && (length($email_list) > 3) && ($m > 0) )
	{
	if ($q < 1) {print "Sending email: $email_list\n";}

	use MIME::QuotedPrint;
	use MIME::Base64;
	use Mail::Sendmail;

	$mailsubject = "VICIDIAL Lead Lists Report $shipdate";

	  %mail = ( To      => "$email_list",
							From    => "$email_sender",
							Subject => "$mailsubject",
					   );
		$boundary = "====" . time() . "====";
		$mail{'content-type'} = "multipart/mixed; boundary=\"$boundary\"";

		$message = encode_qp( "VICIDIAL Lead Lists Report for $shipdate:\n\n Attachment: $outfile" );

		$Zfile = "$PATHoutfile";

		open (F, $Zfile) or die "Cannot read $Zfile: $!";
		binmode F; undef $/;
		$attachment = encode_base64(<F>);
		close F;

		$boundary = '--'.$boundary;
		$mail{body} .= "$boundary\n";
		$mail{body} .= "Content-Type: text/plain; charset=\"iso-8859-1\"\n";
		$mail{body} .= "Content-Transfer-Encoding: quoted-printable\n\n";
		$mail{body} .= "$message\n";
		$mail{body} .= "$boundary\n";
		$mail{body} .= "Content-Type: application/octet-stream; name=\"$outfile\"\n";
		$mail{body} .= "Content-Transfer-Encoding: base64\n";
		$mail{body} .= "Content-Disposition: attachment; filename=\"$outfile\"\n\n";
		$mail{body} .= "$attachment\n";
		$mail{body} .= "$boundary";
		$mail{body} .= "--\n";

			sendmail(%mail) or die $mail::Sendmail::error;
		   print "ok. log says:\n", $mail::sendmail::log;  ### print mail log for status

	$email_sent++;
	}

if ( (length($Ealert)>5) && (length($email_list) > 3) && ($email_sent < 1) )
	{
	if ($q < 1) {print "Sending email: $email_list\n";}

	use MIME::QuotedPrint;
	use MIME::Base64;
	use Mail::Sendmail;

	$mailsubject = "VTIGER ACCOUNT FILE LOAD $pulldate0";

	  %mail = ( To      => "$email_list",
							From    => "$email_sender",
							Subject => "$mailsubject",
							Message => "VTIGER ACCOUNT FILE LOAD $pulldate0\n\n$Ealert\n"
					   );
			sendmail(%mail) or die $mail::Sendmail::error;
		   if ($q < 1) {print "ok. log says:\n", $mail::sendmail::log;}  ### print mail log for status
	}

exit;

