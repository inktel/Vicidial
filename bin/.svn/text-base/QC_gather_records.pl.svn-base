#!/usr/bin/perl
#
# QC_gather_records.pl                version: 2.0.5
#
# This script is designed to gather records to be posted to the 
# qc_vicidial_list_original table so that it can be processed by the QC system
#
# Copyright (C) 2008  Matt Florell <vicidial@gmail.com>    LICENSE: AGPLv2
#
# CHANGES
# 80717-1146 - First version development
# 80721-1627 - functional beta version
#

use Time::Local;

$txt = '.txt';
$US = '_';
$DS = '-';
$MT[0] = '';
$date_override_set=0;

# default CLI values
$campaign = '---ALL---';
$pull_statuses = 'SALE-UPSELL';

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
		print "  [--campaign=XXX] = Single campaign/in-group that records will be pulled from.\n";
		print "    Default will pull all qc_active campaigns and in-groups\n";
		print "  [--pull-statuses=XXX-XXY] = Statuses that are to be pulled. Default SALE and UPSELL\n";
		print "    NOTE: To include all statuses in the export, use \"--sale-statuses=---ALL---\"\n";
		print "  [--date-override=YYYYMMDDHHMMSS] = Date to force a re-pull from.\n";
		print "    Pulls 24 hours of records from the override time/date\n";
		print "  [-q] = quiet\n";
		print "  [-t] = test\n";
		print "  [--debug] = debugging messages\n";
		print "  [--debugX] = Super debugging messages\n";
		print "\n";

		exit;
		}
	else
		{
		if ($args =~ /--debug/i)
			{
			$DB=1;
			print "\n----- DEBUG MODE -----\n\n";
			}
		if ($args =~ /--debugX/i)
			{
			$DBX=1;
			print "\n----- SUPER DEBUG MODE -----\n\n";
			}
		if ($args =~ /-q/i)
			{
			$q=1;   $Q=1;
			}
		if ($args =~ /--date-override=/i)
			{
			@data_in = split(/--date-override=/,$args);
				$date_override = $data_in[1];
				$date_override =~ s/ .*$//gi;
				$date_override =~ s/\D//gi;
				if ( (length($date_override)<14) || (length($date_override)>14) )
					{
					print "ERROR! Invalid date_override format |$date_override|\n";
					$data_override='';
					}
				else
					{
					$YYYY = substr($date_override, 0, 4);
					$MM = substr($date_override, 4, 2);
						$MM = ($MM - 1);
					$DD = substr($date_override, 6, 2);
					$hh = substr($date_override, 8, 2);
					$mm = substr($date_override, 10, 2);
					$ss = substr($date_override, 12, 2);
					$STARTdateSQL = "$YYYY$DS$MM$DS$DD $hh:$mm:$ss";
					$STARTdateEPOCH = timelocal($ss,$mm,$hh,$DD,$MM,$YYYY);
					$ENDdateEPOCH = ($STARTdateEPOCH + 86400);
					($EDsec,$EDmin,$EDhour,$EDmday,$EDmon,$EDyear,$EDwday,$EDyday,$EDisdst) = localtime($ENDdateEPOCH);
					$EDyear = ($EDyear + 1900);
					$EDmon++;
					if ($EDmon < 10) {$EDmon = "0$EDmon";}
					if ($EDmday < 10) {$EDmday = "0$EDmday";}
					if ($EDhour < 10) {$EDhour = "0$EDhour";}
					if ($EDmin < 10) {$EDmin = "0$EDmin";}
					if ($EDsec < 10) {$EDsec = "0$EDsec";}
					$ENDdateSQL = "$EDyear-$EDmon-$EDmday $EDhour:$EDmin:$EDsec";

					$dateSQL = "and (call_date >= \"$STARTdateSQL\" and call_date < \"$ENDdateSQL\")";
					$date_override_set++;
					}
			}
		if ($args =~ /--campaign=/i)
			{
			@data_in = split(/--campaign=/,$args);
				$campaign = $data_in[1];
				$campaign =~ s/ .*$//gi;
			}
		else
			{$campaign='---ALL---';}
		if ($args =~ /--pull-statuses=/i)
			{
			@data_in = split(/--pull-statuses=/,$args);
				$pull_statuses = $data_in[1];
				$pull_statuses =~ s/ .*$//gi;
				if ($pull_statuses =~ /---ALL---/)
					{if (!$Q) {print "\n----- PULL ALL STATUSES -----\n\n";} }
			}
		else
			{$pull_statuses='---ALL---';}
		if ($args =~ /--test/i)
			{
			$T=1;   $TEST=1;
			print "\n----- TESTING -----\n\n";
			}
		}
	}
else
	{
	print "no command line options set, using defaults.\n";
	}
### end parsing run-time options ###

if (!$Q)
	{
	print "\n\n\n\n\n\n\n\n\n\n\n\n-- QC_gather_records.pl --\n\n";
	print "This program is designed to gather records from VICIDIAL campaigns and in-groups and place them into the qc_vicidial_list_original table for processing by the QC system. \n";
	print "\n";
	}

$secX = time();
	($sec,$min,$hour,$mday,$mon,$year,$wday,$yday,$isdst) = localtime(time);
	$year = ($year + 1900);
	$mon++;
	if ($mon < 10) {$mon = "0$mon";}
	if ($mday < 10) {$mday = "0$mday";}
	if ($hour < 10) {$hour = "0$hour";}
	if ($min < 10) {$min = "0$min";}
	if ($sec < 10) {$sec = "0$sec";}
	$timestamp = "$year-$mon-$mday $hour:$min:$sec";
	$filedate = "$year$mon$mday";
	$ABIfiledate = "$mon-$mday-$year$us$hour$min$sec";
	$shipdate = "$year-$mon-$mday";
	$datestamp = "$year/$mon/$mday $hour:$min";

	$four_hours_behind = ($secX -14400);
	($MBsec,$MBmin,$MBhour,$MBmday,$MBmon,$MByear,$MBwday,$MByday,$MBisdst) = localtime($four_hours_behind);
	$MByear = ($MByear + 1900);
	$MBmon++;
	if ($MBmon < 10) {$MBmon = "0$MBmon";}
	if ($MBmday < 10) {$MBmday = "0$MBmday";}
	if ($MBhour < 10) {$MBhour = "0$MBhour";}
	if ($MBmin < 10) {$MBmin = "0$MBmin";}
	if ($MBsec < 10) {$MBsec = "0$MBsec";}
	$qc_this_pull_time = "$MByear-$MBmon-$MBmday $MBhour:$MBmin:$MBsec";


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

$dbhB = DBI->connect("DBI:mysql:$VARDB_database:$VARDB_server:$VARDB_port", "$VARDB_user", "$VARDB_pass")
 or die "Couldn't connect to database: " . DBI->errstr;

#############################################
##### START SYSTEM_SETTINGS LOOKUP #####
$stmtA = "SELECT use_non_latin,qc_last_pull_time FROM system_settings;";
$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
$sthArows=$sthA->rows;
if ($sthArows > 0)
	{
	@aryA = $sthA->fetchrow_array;
	$non_latin = 			$aryA[0];
	$qc_last_pull_time =	$aryA[1];
	}
$sthA->finish();
##### END SETTINGS LOOKUP #####
###########################################

if ($non_latin > 0) {$affected_rows = $dbhA->do("SET NAMES 'UTF8'");}
if ($date_override_set<1) {$dateSQL = "and (call_date >= \"$qc_last_pull_time\")";}

##### BEGIN find campaigns or in-groups to query #####
$CLIcampSQLc='';
$CLIcampSQLi='';
$campaignSQL='';
$ingroupSQL='';
$pull_campaigns=0;
$pull_ingroups=0;

if ($campaign !~ /---ALL---/)
	{
	$CLIcampSQLc = "and campaign_id='$campaign'";
	$CLIcampSQLi = "and group_id='$campaign'";
	}

$stmtA = "select campaign_id from vicidial_campaigns where qc_enabled='Y' $CLIcampSQLc;";
if ($DB) {print "|$stmtA|\n";}
$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
$sthArows=$sthA->rows;
while ($sthArows > $pull_campaigns)
	{
	@aryA = $sthA->fetchrow_array;
	$campaignSQL .=	"'$aryA[0]',";
	$campaigns[$pull_campaigns] = $aryA[0];
	$pull_campaigns++;
	}
$sthA->finish();
if (length($campaignSQL)>2)
	{chop($campaignSQL);}
else {$campaignSQL="''";}

$stmtA = "select group_id from vicidial_inbound_groups where qc_enabled='Y' $CLIcampSQLi;";
if ($DB) {print "|$stmtA|\n";}
$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
$sthArows=$sthA->rows;
while ($sthArows > $pull_ingroups)
	{
	@aryA = $sthA->fetchrow_array;
	$ingroupSQL .=	"'$aryA[0]',";
	$ingroups[$pull_ingroups] = $aryA[0];
	$pull_ingroups++;
	}
$sthA->finish();
if (length($ingroupSQL)>2)
	{chop($ingroupSQL);}
else {$ingroupSQL="''";}
##### END find campaigns or in-groups to query #####


##### START get lists of statuses to pull for each in-group and campaign #####
$VCLquerySQL='';
$VLquerySQL='';
if ($pull_statuses =~ /---ALL---/)
	{
	### if all statuses, loop through each campaign and in-group to grab statuses
	$i=0;   $j=0;
	foreach(@ingroups)
		{
		$stmtA = "select qc_statuses,qc_shift_id,shift_start_time from vicidial_inbound_groups,vicidial_shifts where group_id='$ingroups[$i]' and qc_shift_id=shift_id;";
		if ($DB) {print "|$stmtA|\n";}
		$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
		$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
		$sthArows=$sthA->rows;
		if ($sthArows > 0)
			{
			@aryA = $sthA->fetchrow_array;
				$aryA[0] =~ s/ -$//gi;
				@QCstatuses = split(/ /, $aryA[0]);
				$QCs_to_print = $#QCstatuses;
				$QCsql = '';
				$o=0;
				while ($QCs_to_print > $o) 
					{
					$o++;
					$QCsql .= "'$QCstatuses[$o]',";
					}
				$QCsql =~ s/,$//gi;
				if (length($QCsql)<2) {$QCsql="''";}

			if ($j>0) {$VCLquerySQL .=	" or ";}
			$VCLquerySQL .= "(vicidial_closer_log.campaign_id='$ingroups[$i]' and vicidial_closer_log.status IN($QCsql))";
			$ingroup_statuses[$i] = $aryA[0];
			$j++;
			}
		$sthA->finish();
		$i++;
		}

	### if all statuses, loop through each campaign and in-group to grab statuses
	$i=0;   $j=0;
	foreach(@campaigns)
		{
		$stmtA = "select qc_statuses,qc_shift_id,shift_start_time from vicidial_campaigns,vicidial_shifts where campaign_id='$campaigns[$i]' and qc_shift_id=shift_id;";
		if ($DB) {print "|$stmtA|\n";}
		$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
		$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
		$sthArows=$sthA->rows;
		if ($sthArows > 0)
			{
			@aryA = $sthA->fetchrow_array;
				$aryA[0] =~ s/ -$//gi;
				@QCstatuses = split(/ /, $aryA[0]);
				$QCs_to_print = $#QCstatuses;
				$QCsql = '';
				$o=0;
				while ($QCs_to_print > $o) 
					{
					$o++;
					$QCsql .= "'$QCstatuses[$o]',";
					}
				$QCsql =~ s/,$//gi;
				if (length($QCsql)<2) {$QCsql="''";}

			if ($j>0) {$VLquerySQL .=	" or ";}
			$VLquerySQL .= "(vicidial_log.campaign_id='$campaigns[$i]' and vicidial_log.status IN($QCsql))";
			$campaign_statuses[$i] = $aryA[0];
			$j++;
			}
		$sthA->finish();
		$i++;
		}
	$VCLquerySQL = "and ($VCLquerySQL)";
	$VLquerySQL = "and ($VLquerySQL)";
	if ($DB>0) {print "\n$VCLquerySQL\n";}
	if ($DB>0) {print "\n$VLquerySQL\n";}
	##### END get lists of statuses to pull for each in-group and campaign #####

	$pull_statusesSQL='';
	$close_statusesSQL='';
	}
else
	{
	$pull_statusesSQL = $pull_statuses;
	$pull_statusesSQL =~ s/-/','/gi;
	$pull_statusesSQL = "'$pull_statusesSQL'";
	$close_statusesSQL = $pull_statusesSQL;
	$pull_statusesSQL = " and (vicidial_log.status IN($pull_statusesSQL))";
	$close_statusesSQL = " and (vicidial_closer_log.status IN($close_statusesSQL))";
	}

##### Construct the queries to pull records for QC #####
$VCLsql= "SELECT closecallid,lead_id,campaign_id,call_date,length_in_sec,status,user,comments,user_group,term_reason,xfercallid from vicidial_closer_log where campaign_id IN($ingroupSQL) $dateSQL $VCLquerySQL $close_statusesSQL;";
$VLsql= "SELECT uniqueid,lead_id,campaign_id,call_date,length_in_sec,status,user,comments,user_group,term_reason from vicidial_log where campaign_id IN($campaignSQL) $dateSQL $VLquerySQL $pull_statusesSQL;";


$j=0;
##### Pull records from vicidial_closer_log table
$stmtA = "$VCLsql";
if ($DB) {print "|$stmtA|\n";}
$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
$sthArows=$sthA->rows;
$i=0;
while ($sthArows > $i)
	{
	@aryA = $sthA->fetchrow_array;
	$vicidial_id[$j] =		$aryA[0];
	$lead_id[$j] =			$aryA[1];
	$campaign_id[$j] =		$aryA[2];
	$call_date[$j] =		$aryA[3];
	$length_in_sec[$j] =	$aryA[4];
	$status[$j] =			$aryA[5];
	$user[$j] =				$aryA[6];
	$call_type[$j] =		$aryA[7];
	$user_group[$j] =		$aryA[8];
	$term_reason[$j] =		$aryA[9];
	$xfercallid[$j] =		$aryA[10];

	$i++; $j++;
	}
$sthA->finish();

##### Pull records from vicidial_log table
$stmtA = "$VLsql";
if ($DB) {print "|$stmtA|\n";}
$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
$sthArows=$sthA->rows;
$k=0;
while ($sthArows > $k)
	{
	@aryA = $sthA->fetchrow_array;
	$vicidial_id[$j] =		$aryA[0];
	$lead_id[$j] =			$aryA[1];
	$campaign_id[$j] =		$aryA[2];
	$call_date[$j] =		$aryA[3];
	$length_in_sec[$j] =	$aryA[4];
	$status[$j] =			$aryA[5];
	$user[$j] =				$aryA[6];
	$call_type[$j] =		$aryA[7];
	$user_group[$j] =		$aryA[8];
	$term_reason[$j] =		$aryA[9];
	$xfercallid[$j] =		'';

	$k++; $j++;
	}
$sthA->finish();

if ($DB) {print "vicidial_closer_log records found: $i\n";}
if ($DB) {print "vicidial_log records found:        $k\n";}
if ($DB) {print "                TOTAL:             $j\n";}

$i=0; $j=0;
foreach(@vicidial_id)
	{
	### check to see if there is already an entry in the vicidial_qc_list table for this record
	$VDIDduplicate=0;
	$stmtA = "select count(*) from vicidial_qc_list where vicidial_id='$vicidial_id[$i]';";
	if ($DB) {print "|$stmtA|\n";}
	$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
	$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
	$sthArows=$sthA->rows;
	if ($sthArows > 0)
		{
		@aryA = $sthA->fetchrow_array;
		$VDIDduplicate =	$aryA[0];
		}
	$sthA->finish();

	if ($VDIDduplicate > 0)
		{
		$j++;
		if ($DB) {print "DUPLICATE: |$vicidial_id[$i]|$i|$j|\n";}
		}
	else
		{
		### grab vicidial_list details
		$stmtA = "select vendor_lead_code,source_id,list_id,phone_code,phone_number,title,first_name,middle_initial,last_name,address1,address2,address3,city,state,province,postal_code,country_code,gender,date_of_birth,alt_phone,email,security_phrase,comments from vicidial_list where lead_id='$lead_id[$i]';";
		if ($DB) {print "|$stmtA|\n";}
		$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
		$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
		$sthArows=$sthA->rows;
		if ($sthArows > 0)
			{
			@aryA = $sthA->fetchrow_array;
			$vendor_lead_code[$i] =	$aryA[0];
			$source_id[$i] =		$aryA[1];
			$list_id[$i] =			$aryA[2];
			$phone_code[$i] =		$aryA[3];
			$phone_number[$i] =		$aryA[4];
			$title[$i] =			$aryA[5];
			$first_name[$i] =		$aryA[6];
			$middle_initial[$i] =	$aryA[7];
			$last_name[$i] =		$aryA[8];
			$address1[$i] =			$aryA[9];
			$address2[$i] =			$aryA[10];
			$address3[$i] =			$aryA[11];
			$city[$i] =				$aryA[12];
			$state[$i] =			$aryA[13];
			$province[$i] =			$aryA[14];
			$postal_code[$i] =		$aryA[15];
			$country_code[$i] =		$aryA[16];
			$gender[$i] =			$aryA[17];
			$date_of_birth[$i] =	$aryA[18];
			$alt_phone[$i] =		$aryA[19];
			$email[$i] =			$aryA[20];
			$security_phrase[$i] =	$aryA[21];
			$comments[$i] =			$aryA[22];
			}
		$sthA->finish();

		### if there is a fronter, look for one in the xfer log by the xfercallid
		if (length($xfercallid > 0))
			{
			$stmtA = "select user from vicidial_xfer_log where xfercallid='$xfercallid[$i]' limit 1;";
			$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
			$sthA->execute or die "executing: $stmtB ", $dbhA->errstr;
			$sthArows=$sthA->rows;
			if ($sthArows > 0)
				{
				@aryA = $sthA->fetchrow_array;
				$fronter[$i] = $aryA[0];
				}
			$sthA->finish();
			}
		else 
			{$fronter[$i]='';}

		### Insert a record into the vicidial_qc_list table
		$stmtA  = "INSERT INTO vicidial_qc_list SET ";
		$stmtA .= "qc_import_date='$timestamp',";
		$stmtA .= "qc_stage='NEW',";
		$stmtA .= "call_date='$call_date[$i]',";
		$stmtA .= "vicidial_id='$vicidial_id[$i]',";
		$stmtA .= "campaign_group_id='$campaign_id[$i]',";
		$stmtA .= "lead_id='$lead_id[$i]',";
		$stmtA .= "list_id='$list_id[$i]',";
		$stmtA .= "length_in_sec='$length_in_sec[$i]',";
		$stmtA .= "status='$status[$i]',";
		$stmtA .= "call_type='$call_type[$i]',";
		$stmtA .= "term_reason='$term_reason[$i]',";
		$stmtA .= "qc_user='VDQC',";
		$stmtA .= "user='$user[$i]',";
		$stmtA .= "user_group='$user_group[$i]',";
		$stmtA .= "fronter='$fronter[$i]',";
		$stmtA .= "vendor_lead_code='$vendor_lead_code[$i]',";
		$stmtA .= "source_id='$source_id[$i]',";
		$stmtA .= "phone_code='$phone_code[$i]',";
		$stmtA .= "phone_number='$phone_number[$i]',";
		$stmtA .= "title='$title[$i]',";
		$stmtA .= "first_name='$first_name[$i]',";
		$stmtA .= "middle_initial='$middle_initial[$i]',";
		$stmtA .= "last_name='$last_name[$i]',";
		$stmtA .= "address1='$address1[$i]',";
		$stmtA .= "address2='$address2[$i]',";
		$stmtA .= "address3='$address3[$i]',";
		$stmtA .= "city='$city[$i]',";
		$stmtA .= "state='$state[$i]',";
		$stmtA .= "province='$province[$i]',";
		$stmtA .= "postal_code='$postal_code[$i]',";
		$stmtA .= "country_code='$country_code[$i]',";
		$stmtA .= "gender='$gender[$i]',";
		$stmtA .= "date_of_birth='$date_of_birth[$i]',";
		$stmtA .= "alt_phone='$alt_phone[$i]',";
		$stmtA .= "email='$email[$i]',";
		$stmtA .= "security_phrase='$security_phrase[$i]',";
		$stmtA .= "comments='$comments[$i]'";
		$stmtA .= ";";
		$affected_rows = $dbhA->do($stmtA);
		$inserted = ($inserted + $affected_rows);
		}
	$i++;
	}
if ($DB) {print "Records Inserted: $inserted out of $i\n";}

$stmtA = "UPDATE system_settings SET qc_last_pull_time = '$qc_this_pull_time';";
$affected_rows = $dbhA->do($stmtA);
if ($DB) {print "system_settings update: $qc_this_pull_time\n";}

if ($DB) {print "DONE\n";}

exit;

