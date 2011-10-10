#!/usr/bin/perl
#
# listloader_super.pl   version 2.2.0
# 
# Copyright (C) 2010  Matt Florell,Joe Johnson <vicidial@gmail.com>    LICENSE: AGPLv2
#
#
# CHANGES
# 60616-1548 - Added listID override feature to force all leads into same list
#            - Added gmt_offset_now lookup for each lead
# 60811-1232 - Changed to DBI
# 60811-1329 - changed to use /etc/astguiclient.conf for configs
# 60906-1056 - added filter of non-digits in alt_phone field
# 61110-1229 - added new USA-Canada DST scheme and Brazil DST scheme
# 61128-1207 - added postal code GMT lookup and duplicate check options
# 70205-1703 - Defaulted phone_code to 1 if not populated
# 70417-1059 - Fixed default phone_code bug
# 70510-1518 - Added campaign and system duplicate check and phonecode override
# 80428-0144 - UTF8 cleanup
# 80713-0023 - added last_local_call_time field default of 2008-01-01
# 90721-1341 - Added rank and owner as vicidial_list fields
# 91112-0616 - Added title/alt-phone duplicate checking
# 100118-0539 - Added new Australian and New Zealand DST schemes (FSO-FSA and LSS-FSA)
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

	if ($args =~ /--help|-h/i)
		{
		print "allowed run time options:\n  [-forcelistid=1234] = overrides the listID given in the file with the 1234\n  [-h] = this help screen\n\n";

		exit;
		}
	else
		{
		if ($args =~ /-duplicate-check/i)
			{$dupcheck=1;}
		if ($args =~ /-duplicate-campaign-check/i)
			{$dupcheckcamp=1;}
		if ($args =~ /-duplicate-system-check/i)
			{$dupchecksys=1;}
		if ($args =~ /-duplicate-tap-list-check/i)
			{$duptapchecklist=1;}
		if ($args =~ /-duplicate-tap-system-check/i)
			{$duptapchecksys=1;}
		if ($args =~ /-postal-code-gmt/i)
			{$postalgmt=1;}
		if ($args =~ /--forcelistid=/i)
			{
			@data_in = split(/--forcelistid=/,$args);
			$forcelistid = $data_in[1];
			$forcelistid =~ s/ .*//gi;
			print "\n----- FORCE LISTID OVERRIDE: $forcelistid -----\n\n";
			}
		else
			{$forcelistid = '';}

		if ($args =~ /--forcephonecode=/i)
			{
			@data_in = split(/--forcephonecode=/,$args);
			$forcephonecode = $data_in[1];
			$forcephonecode =~ s/ .*//gi;
			print "\n----- FORCE PHONECODE OVERRIDE: $forcephonecode -----\n\n";
			}
		else
			{$forcephonecode = '';}

		if ($args =~ /--lead-file=/i)
			{
			@data_in = split(/--lead-file=/,$args);
			$lead_file = $data_in[1];
			$lead_file =~ s/ .*//gi;
		#	print "\n----- LEAD FILE: $lead_file -----\n\n";
			}
		else
			{$lead_file = './vicidial_temp_file.xls';}
		}
	}
### end parsing run-time options ###

use Spreadsheet::ParseExcel;
use Time::Local;
use DBI;	  


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

$dbhA = DBI->connect("DBI:mysql:$VARDB_database:$VARDB_server:$VARDB_port", "$VARDB_user", "$VARDB_pass")
 or die "Couldn't connect to database: " . DBI->errstr;


$vars=$ARGV[0];
@xls_fields=split(/\,/, $vars);

$|=0;
$secX = time();

($sec,$min,$hour,$mday,$mon,$year,$wday,$yday,$isdst) = localtime(time);
$year = ($year + 1900);
$mon++;
if ($hour < 10) {$hour = "0$hour";}
if ($min < 10) {$min = "0$min";}
if ($sec < 10) {$sec = "0$sec";}
if ($mon < 10) {$mon = "0$mon";}
if ($mday < 10) {$mday = "0$mday";}
$pulldate0 = "$year-$mon-$mday $hour:$min:$sec";
$pulldate="$year-$mon-$mday $hour:$min:$sec";
$inSD = $pulldate0;
$dsec = ( ( ($hour * 3600) + ($min * 60) ) + $sec );

#############################################
##### START SYSTEM_SETTINGS LOOKUP #####
$stmtA = "SELECT use_non_latin FROM system_settings;";
$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
$sthArows=$sthA->rows;
if ($sthArows > 0)
	{
	@aryA = $sthA->fetchrow_array;
	$non_latin		=		"$aryA[0]";
	}
$sthA->finish();
##### END SETTINGS LOOKUP #####
###########################################


if ($non_latin > 0) {$affected_rows = $dbhA->do("SET NAMES 'UTF8'");}

### Grab Server values from the database
$stmtA = "SELECT local_gmt FROM servers where server_ip = '$server_ip';";
$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
$sthArows=$sthA->rows;
$rec_count=0;
while ($sthArows > $rec_count)
	{
	@aryA = $sthA->fetchrow_array;
	$DBSERVER_GMT		=		"$aryA[0]";
	if ($DBSERVER_GMT)				{$SERVER_GMT = $DBSERVER_GMT;}
	$rec_count++;
	}
$sthA->finish();

	$LOCAL_GMT_OFF = $SERVER_GMT;
	$LOCAL_GMT_OFF_STD = $SERVER_GMT;

if ($isdst) {$LOCAL_GMT_OFF++;} 
if ($DB) {print "SEED TIME  $secX      :   $year-$mon-$mday $hour:$min:$sec  LOCAL GMT OFFSET NOW: $LOCAL_GMT_OFF\n";}



$total=0; $good=0; $bad=0;
open(STMT_FILE, "> $PATHlogs/listloader_stmts.txt");

$oBook = Spreadsheet::ParseExcel::Workbook->Parse("$lead_file");
my($iR, $iC, $oWkS, $oWkC);

foreach $oWkS (@{$oBook->{Worksheet}}) {
	for($iR = 0 ; defined $oWkS->{MaxRow} && $iR <= $oWkS->{MaxRow} ; $iR++) {

		$entry_date =			"$pulldate";
		$modify_date =			"";
		$status =				"NEW";
		$user =					"";
		$oWkC = $oWkS->{Cells}[$iR][$xls_fields[0]];
		if ($oWkC) {$vendor_lead_code=$oWkC->Value; }
		$oWkC = $oWkS->{Cells}[$iR][$xls_fields[1]];
		if ($oWkC) {$source_code=$oWkC->Value; }
		$source_id=$source_code;
		$oWkC = $oWkS->{Cells}[$iR][$xls_fields[2]];
		if ($oWkC) {$list_id=$oWkC->Value; }
		$gmt_offset =			'0';
		$called_since_last_reset='N';
		$oWkC = $oWkS->{Cells}[$iR][$xls_fields[3]];
		if ($oWkC) {$phone_code=$oWkC->Value; }
		$phone_code=~s/[^0-9]//g;
		$oWkC = $oWkS->{Cells}[$iR][$xls_fields[4]];
		if ($oWkC) {$phone_number=$oWkC->Value; }
		$phone_number=~s/[^0-9]//g;
			$USarea = 			substr($phone_number, 0, 3);
		$oWkC = $oWkS->{Cells}[$iR][$xls_fields[5]];
		if ($oWkC) {$title=$oWkC->Value; }
		$oWkC = $oWkS->{Cells}[$iR][$xls_fields[6]];
		if ($oWkC) {$first_name=$oWkC->Value; }
		$oWkC = $oWkS->{Cells}[$iR][$xls_fields[7]];
		if ($oWkC) {$middle_initial=$oWkC->Value; }
		$oWkC = $oWkS->{Cells}[$iR][$xls_fields[8]];
		if ($oWkC) {$last_name=$oWkC->Value; }
		$oWkC = $oWkS->{Cells}[$iR][$xls_fields[9]];
		if ($oWkC) {$address1=$oWkC->Value; }
		$oWkC = $oWkS->{Cells}[$iR][$xls_fields[10]];
		if ($oWkC) {$address2=$oWkC->Value; }
		$oWkC = $oWkS->{Cells}[$iR][$xls_fields[11]];
		if ($oWkC) {$address3=$oWkC->Value; }
		$oWkC = $oWkS->{Cells}[$iR][$xls_fields[12]];
		if ($oWkC) {$city=$oWkC->Value; }
		$oWkC = $oWkS->{Cells}[$iR][$xls_fields[13]];
		if ($oWkC) {$state=$oWkC->Value; }
		$oWkC = $oWkS->{Cells}[$iR][$xls_fields[14]];
		if ($oWkC) {$province=$oWkC->Value; }
		$oWkC = $oWkS->{Cells}[$iR][$xls_fields[15]];
		if ($oWkC) {$postal_code=$oWkC->Value; }
		$oWkC = $oWkS->{Cells}[$iR][$xls_fields[16]];
		if ($oWkC) {$country_code=$oWkC->Value; }
		$oWkC = $oWkS->{Cells}[$iR][$xls_fields[17]];
		if ($oWkC) {$gender=$oWkC->Value; }
		$oWkC = $oWkS->{Cells}[$iR][$xls_fields[18]];
		if ($oWkC) {$date_of_birth=$oWkC->Value; }
		$oWkC = $oWkS->{Cells}[$iR][$xls_fields[19]];
		if ($oWkC) {$alt_phone=$oWkC->Value; }
		$alt_phone=~s/[^0-9]//g;
		$oWkC = $oWkS->{Cells}[$iR][$xls_fields[20]];
		if ($oWkC) {$email=$oWkC->Value; }
		$oWkC = $oWkS->{Cells}[$iR][$xls_fields[21]];
		if ($oWkC) {$security_phrase=$oWkC->Value; }
		$oWkC = $oWkS->{Cells}[$iR][$xls_fields[22]];
		if ($oWkC) {$comments=$oWkC->Value; }
		$comments=~s/^\s*(.*?)\s*$/$1/;
		$oWkC = $oWkS->{Cells}[$iR][$xls_fields[23]];
		if ($oWkC) {$rank=$oWkC->Value; }
		if (length($rank)<1) {$rank='0';}
		$oWkC = $oWkS->{Cells}[$iR][$xls_fields[24]];
		if ($oWkC) {$owner=$oWkC->Value; }


		
		if (length($forcelistid) > 0)
			{
			$list_id =	$forcelistid;		# set list_id to override value
			}
		if (length($forcephonecode) > 0)
			{
			$phone_code =	$forcephonecode;	# set phone_code to override value
			}

		##### Check for duplicate phone numbers in vicidial_list table entire database #####
		if ($dupchecksys > 0)
			{
			$dup_lead=0;
			$stmtA = "select count(*) from vicidial_list where phone_number='$phone_number';";
				if($DBX){print STDERR "\n|$stmtA|\n";}
			$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
			$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
			$sthArows=$sthA->rows;
			if ($sthArows > 0)
				{
				@aryA = $sthA->fetchrow_array;
				$dup_lead = $aryA[0];
				$dup_lead_list=$list_id;
				}
			$sthA->finish();
			if ($dup_lead < 1)
				{
				if ($phone_list =~ /\|$phone_number$US$list_id\|/)
					{$dup_lead++;}
				}
			}
		##### Check for duplicate phone numbers in vicidial_list table for one list_id #####
		if ($dupcheck > 0)
			{
			$dup_lead=0;
			$stmtA = "select list_id from vicidial_list where phone_number='$phone_number' and list_id='$list_id' limit 1;";
				if($DBX){print STDERR "\n|$stmtA|\n";}
			$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
			$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
			$sthArows=$sthA->rows;
			if ($sthArows > 0)
				{
				@aryA = $sthA->fetchrow_array;
				$dup_lead_list = $aryA[0];
				$dup_lead++;
				}
			$sthA->finish();
			if ($dup_lead < 1)
				{
				if ($phone_list =~ /\|$phone_number$US$list_id\|/)
					{$dup_lead++;}
				}
			}
		##### Check for duplicate phone numbers in vicidial_list table for all lists in a campaign #####
		if ($dupcheckcamp > 0)
			{
			$dup_lead=0;
			$dup_lists='';

			$stmtA = "select count(*) from vicidial_lists where list_id='$list_id';";
				if($DBX){print STDERR "\n|$stmtA|\n";}
			$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
			$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
				@aryA = $sthA->fetchrow_array;
				$ci_recs = $aryA[0];
			$sthA->finish();
			if ($ci_recs > 0)
				{
				$stmtA = "select campaign_id from vicidial_lists where list_id='$list_id';";
					if($DBX){print STDERR "\n|$stmtA|\n";}
				$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
				$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
					@aryA = $sthA->fetchrow_array;
					$dup_camp = $aryA[0];
				$sthA->finish();

				$stmtA = "select list_id from vicidial_lists where campaign_id='$dup_camp';";
				$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
				$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
				$sthArows=$sthA->rows;
				$rec_count=0;
				while ($sthArows > $rec_count)
					{
					@aryA = $sthA->fetchrow_array;
					$dup_lists .=	"'$aryA[0]',";
					$rec_count++;
					}
				$sthA->finish();

				chop($dup_lists);
				$stmtA = "select list_id from vicidial_list where phone_number='$phone_number' and list_id IN($dup_lists) limit 1;";
				$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
				$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
				$sthArows=$sthA->rows;
				$rec_count=0;
				while ($sthArows > $rec_count)
					{
					@aryA = $sthA->fetchrow_array;
					$dup_lead_list =	"'$aryA[0]',";
					$rec_count++;
					$dup_lead=1;
					}
				$sthA->finish();
				}
			if ($dup_lead < 1)
				{
				if ($phone_list =~ /\|$phone_number$US$list_id\|/)
					{$dup_lead++;}
				}
			}
		##### Check for duplicate title/alt-phone in vicidial_list table entire database #####
		if ($duptapchecksys > 0)
			{
			$dup_lead=0;
			$stmtA = "select count(*) from vicidial_list where title='$title' and alt_phone='$alt_phone';";
				if($DBX){print STDERR "\n|$stmtA|\n";}
			$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
			$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
			$sthArows=$sthA->rows;
			if ($sthArows > 0)
				{
				@aryA = $sthA->fetchrow_array;
				$dup_lead = $aryA[0];
				$dup_lead_list=$list_id;
				}
			$sthA->finish();
			if ($dup_lead < 1)
				{
				if ($phone_list =~ /\|$alt_phone$title$US$list_id\|/)
					{$dup_lead++;}
				}
			}
		##### Check for duplicate title/alt-phone in vicidial_list table for one list_id #####
		if ($duptapchecklist > 0)
			{
			$dup_lead=0;
			$stmtA = "select list_id from vicidial_list where title='$title' and alt_phone='$alt_phone' and list_id='$list_id' limit 1;";
				if($DBX){print STDERR "\n|$stmtA|\n";}
			$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
			$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
			$sthArows=$sthA->rows;
			if ($sthArows > 0)
				{
				@aryA = $sthA->fetchrow_array;
				$dup_lead_list = $aryA[0];
				$dup_lead++;
				}
			$sthA->finish();
			if ($dup_lead < 1)
				{
				if ($phone_list =~ /\|$alt_phone$title$US$list_id\|/)
					{$dup_lead++;}
				}
			}

		if ( (length($phone_number)>6) && ($dup_lead < 1) )
			{
			if ( ($duptapchecklist > 0) || ($duptapchecksys > 0) )
				{$phone_list .= "$alt_phone$title$US$list_id|";}
			else
				{$phone_list .= "$phone_number$US$list_id|";}
			$postalgmt_found=0;
			if (length($phone_code)<1) {$phone_code = '1';}

			if ( ($postalgmt > 0) && (length($postal_code)>4) )
				{
				if ($phone_code =~ /^1$/)
					{
					$stmtA = "select postal_code,state,GMT_offset,DST,DST_range,country,country_code from vicidial_postal_codes where country_code='$phone_code' and postal_code LIKE \"$postal_code%\";";
						if($DBX){print STDERR "\n|$stmtA|\n";}
					$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
					$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
					$sthArows=$sthA->rows;
					$rec_count=0;
					while ($sthArows > $rec_count)
						{
						@aryA = $sthA->fetchrow_array;
						$gmt_offset =	$aryA[2];  $gmt_offset =~ s/\+| //gi;
						$dst =			$aryA[3];
						$dst_range =	$aryA[4];
						$PC_processed++;
						$rec_count++;
						$postalgmt_found++;
						if ($DBX) {print "     Postal GMT record found for $postal_code: |$gmt_offset|$dst|$dst_range|\n";}
						}
					$sthA->finish();
					}
				}
			if ($postalgmt_found < 1)
				{
				$PC_processed=0;
				### UNITED STATES ###
				if ($phone_code =~ /^1$/)
					{
					$stmtA = "select country_code,country,areacode,state,GMT_offset,DST,DST_range,geographic_description from vicidial_phone_codes where country_code='$phone_code' and areacode='$USarea';";
						if($DBX){print STDERR "\n|$stmtA|\n";}
					$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
					$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
					$sthArows=$sthA->rows;
					$rec_count=0;
					while ($sthArows > $rec_count)
						{
						@aryA = $sthA->fetchrow_array;
						$gmt_offset =	$aryA[4];  $gmt_offset =~ s/\+| //gi;
						$dst =			$aryA[5];
						$dst_range =	$aryA[6];
						$PC_processed++;
						$rec_count++;
						}
					$sthA->finish();
					}
				### MEXICO ###
				if ($phone_code =~ /^52$/)
					{
					$stmtA = "select country_code,country,areacode,state,GMT_offset,DST,DST_range,geographic_description from vicidial_phone_codes where country_code='$phone_code' and areacode='$USarea';";
						if($DBX){print STDERR "\n|$stmtA|\n";}
					$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
					$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
					$sthArows=$sthA->rows;
					$rec_count=0;
					while ($sthArows > $rec_count)
						{
						@aryA = $sthA->fetchrow_array;
						$gmt_offset =	$aryA[4];  $gmt_offset =~ s/\+| //gi;
						$dst =			$aryA[5];
						$dst_range =	$aryA[6];
						$PC_processed++;
						$rec_count++;
						}
					$sthA->finish();
					}
				### AUSTRALIA ###
				if ($phone_code =~ /^61$/)
					{
					$stmtA = "select country_code,country,areacode,state,GMT_offset,DST,DST_range,geographic_description from vicidial_phone_codes where country_code='$phone_code' and state='$state';";
						if($DBX){print STDERR "\n|$stmtA|\n";}
					$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
					$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
					$sthArows=$sthA->rows;
					$rec_count=0;
					while ($sthArows > $rec_count)
						{
						@aryA = $sthA->fetchrow_array;
						$gmt_offset =	$aryA[4];  $gmt_offset =~ s/\+| //gi;
						$dst =			$aryA[5];
						$dst_range =	$aryA[6];
						$PC_processed++;
						$rec_count++;
						}
					$sthA->finish();
					}
				### ALL OTHER COUNTRY CODES ###
				if (!$PC_processed)
					{
					$stmtA = "select country_code,country,areacode,state,GMT_offset,DST,DST_range,geographic_description from vicidial_phone_codes where country_code='$phone_code';";
						if($DBX){print STDERR "\n|$stmtA|\n";}
					$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
					$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
					$sthArows=$sthA->rows;
					$rec_count=0;
					while ($sthArows > $rec_count)
						{
						@aryA = $sthA->fetchrow_array;
						$gmt_offset =	$aryA[4];  $gmt_offset =~ s/\+| //gi;
						$dst =			$aryA[5];
						$dst_range =	$aryA[6];
						$PC_processed++;
						$rec_count++;
						}
					$sthA->finish();
					}
				}

				### Find out if DST to raise the gmt offset ###
				$AC_GMT_diff = ($gmt_offset - $LOCAL_GMT_OFF_STD);
				$AC_localtime = ($secX + (3600 * $AC_GMT_diff));
				($sec,$min,$hour,$mday,$mon,$year,$wday,$yday,$isdst) = localtime($AC_localtime);
				$year = ($year + 1900);
				$mon++;
				if ($mon < 10) {$mon = "0$mon";}
				if ($mday < 10) {$mday = "0$mday";}
				if ($hour < 10) {$hour = "0$hour";}
				if ($min < 10) {$min = "0$min";}
				if ($sec < 10) {$sec = "0$sec";}
				$dsec = ( ( ($hour * 3600) + ($min * 60) ) + $sec );
				
				$AC_processed=0;

				if ( (!$AC_processed) && ($dst_range =~ /SSM-FSN/) )
					{
					if ($DBX) {print "     Second Sunday March to First Sunday November\n";}
					&USACAN_dstcalc;
					if ($DBX) {print "     DST: $USACAN_DST\n";}
					if ($USACAN_DST) {$area_GMT++;}
					$AC_processed++;
					}
				if ( (!$AC_processed) && ($dst_range =~ /FSA-LSO/) )
					{
					if ($DBX) {print "     First Sunday April to Last Sunday October\n";}
					&NA_dstcalc;
					if ($DBX) {print "     DST: $NA_DST\n";}
					if ($NA_DST) {$area_GMT++;}
					$AC_processed++;
					}
				if ( (!$AC_processed) && ($dst_range =~ /LSM-LSO/) )
					{
					if ($DBX) {print "     Last Sunday March to Last Sunday October\n";}
					&GBR_dstcalc;
					if ($DBX) {print "     DST: $GBR_DST\n";}
					if ($GBR_DST) {$gmt_offset++;}
					$AC_processed++;
					}
				if ( (!$AC_processed) && ($dst_range =~ /LSO-LSM/) )
					{
					if ($DBX) {print "     Last Sunday October to Last Sunday March\n";}
					&AUS_dstcalc;
					if ($DBX) {print "     DST: $AUS_DST\n";}
					if ($AUS_DST) {$gmt_offset++;}
					$AC_processed++;
					}
				if ( (!$AC_processed) && ($dst_range =~ /FSO-LSM/) )
					{
					if ($DBX) {print "     First Sunday October to Last Sunday March\n";}
					&AUST_dstcalc;
					if ($DBX) {print "     DST: $AUST_DST\n";}
					if ($AUST_DST) {$gmt_offset++;}
					$AC_processed++;
					}
				if ( (!$AC_processed) && ($area_GMT_method =~ /FSO-FSA/) )
					{
					if ($DBX) {print "     First Sunday October to First Sunday April\n";}
					&AUSE_dstcalc;
					if ($DBX) {print "     DST: $AUSE_DST\n";}
					if ($AUSE_DST) {$area_GMT++;}
					$AC_processed++;
					}
				if ( (!$AC_processed) && ($dst_range =~ /FSO-TSM/) )
					{
					if ($DBX) {print "     First Sunday October to Third Sunday March\n";}
					&NZL_dstcalc;
					if ($DBX) {print "     DST: $NZL_DST\n";}
					if ($NZL_DST) {$gmt_offset++;}
					$AC_processed++;
					}
				if ( (!$AC_processed) && ($area_GMT_method =~ /LSS-FSA/) )
					{
					if ($DBX) {print "     Last Sunday September to First Sunday April\n";}
					&NZLN_dstcalc;
					if ($DBX) {print "     DST: $NZLN_DST\n";}
					if ($NZLN_DST) {$area_GMT++;}
					$AC_processed++;
					}
				if ( (!$AC_processed) && ($dst_range =~ /TSO-LSF/) )
					{
					if ($DBX) {print "     Third Sunday October to Last Sunday February\n";}
					&BZL_dstcalc;
					if ($DBX) {print "     DST: $BZL_DST\n";}
					if ($BZL_DST) {$area_GMT++;}
					$AC_processed++;
					}
				if (!$AC_processed)
					{
					if ($DBX) {print "     No DST Method Found\n";}
					if ($DBX) {print "     DST: 0\n";}
					$AC_processed++;
					}

			if ($multi_insert_counter > 8) {
				### insert good deal into pending_transactions table ###
				$stmtZ = "INSERT INTO vicidial_list (lead_id,entry_date,modify_date,status,user,vendor_lead_code,source_id,list_id,gmt_offset_now,called_since_last_reset,phone_code,phone_number,title,first_name,middle_initial,last_name,address1,address2,address3,city,state,province,postal_code,country_code,gender,date_of_birth,alt_phone,email,security_phrase,comments,called_count,last_local_call_time,rank,owner) values$multistmt('','$entry_date','$modify_date','$status','$user','$vendor_lead_code','$source_id','$list_id','$gmt_offset','$called_since_last_reset','$phone_code','$phone_number','$title','$first_name','$middle_initial','$last_name','$address1','$address2','$address3','$city','$state','$province','$postal_code','$country_code','$gender','$date_of_birth','$alt_phone','$email','$security_phrase','$comments',0,'2008-01-01 00:00:00','$rank','$owner');";
				$affected_rows = $dbhA->do($stmtZ);
				print STMT_FILE $stmtZ."\r\n";
				$multistmt='';
				$multi_insert_counter=0;

			} else {
				$multistmt .= "('','$entry_date','$modify_date','$status','$user','$vendor_lead_code','$source_id','$list_id','$gmt_offset','$called_since_last_reset','$phone_code','$phone_number','$title','$first_name','$middle_initial','$last_name','$address1','$address2','$address3','$city','$state','$province','$postal_code','$country_code','$gender','$date_of_birth','$alt_phone','$email','$security_phrase','$comments',0,'2008-01-01 00:00:00','$rank','$owner'),";
				$multi_insert_counter++;
			}

			$good++;
		} else {
			if ($bad < 10000) {print "<BR></b><font size=1 color=red>record $total BAD- PHONE: $phone_number ROW: |$row[0]| $dup_lead_list</font><b>\n";}
			$bad++;
		}
		$total++;
		if ($total%100==0) {
			print "<script language='JavaScript1.2'>ShowProgress($good, $bad, $total, $dup_lead, $postalgmt_found)</script>";
			sleep(1);
#			flush();
		}
	}
}

if ($multi_insert_counter > 0) {
	$stmtZ = "INSERT INTO vicidial_list (lead_id,entry_date,modify_date,status,user,vendor_lead_code,source_id,list_id,gmt_offset_now,called_since_last_reset,phone_code,phone_number,title,first_name,middle_initial,last_name,address1,address2,address3,city,state,province,postal_code,country_code,gender,date_of_birth,alt_phone,email,security_phrase,comments,called_count,last_local_call_time,rank,owner) values ".substr($multistmt, 0, -1).";";
	$affected_rows = $dbhA->do($stmtZ);
	print STMT_FILE $stmtZ."\r\n";
}

print "<BR><BR>Done</B> GOOD: $good &nbsp; &nbsp; &nbsp; BAD: $bad &nbsp; &nbsp; &nbsp; TOTAL: $total</font></center>";

exit;






sub USACAN_dstcalc {
#**********************************************************************
# SSM-FSN
#     This is returns 1 if Daylight Savings Time is in effect and 0 if 
#       Standard time is in effect.
#     Based on Second Sunday March to First Sunday November at 2 am.
#     INPUTS:
#       mm              INTEGER       Month.
#       dd              INTEGER       Day of the month.
#       ns              INTEGER       Seconds into the day.
#       dow             INTEGER       Day of week (0=Sunday, to 6=Saturday)
#     OPTIONAL INPUT:
#       timezone        INTEGER       hour difference UTC - local standard time
#                                      (DEFAULT is blank)
#                                     make calculations based on UTC time, 
#                                     which means shift at 10:00 UTC in April
#                                     and 9:00 UTC in October
#     OUTPUT: 
#                       INTEGER       1 = DST, 0 = not DST
#
# S  M  T  W  T  F  S
# 1  2  3  4  5  6  7
# 8  9 10 11 12 13 14
#15 16 17 18 19 20 21
#22 23 24 25 26 27 28
#29 30 31
# 
# S  M  T  W  T  F  S
#    1  2  3  4  5  6
# 7  8  9 10 11 12 13
#14 15 16 17 18 19 20
#21 22 23 24 25 26 27
#28 29 30 31
# 
#**********************************************************************

	$USACAN_DST=0;
	$mm = $mon;
	$dd = $mday;
	$ns = $dsec;
	$dow= $wday;

    if ($mm < 3 || $mm > 11) {
	$USACAN_DST=0;   return 0;
    } elsif ($mm >= 4 && $mm <= 10) {
	$USACAN_DST=1;   return 1;
    } elsif ($mm == 3) {
	if ($dd > 13) {
	    $USACAN_DST=1;   return 1;
	} elsif ($dd >= ($dow+8)) {
	    if ($timezone) {
		if ($dow == 0 && $ns < (7200+$timezone*3600)) {
		    $USACAN_DST=0;   return 0;
		} else {
		    $USACAN_DST=1;   return 1;
		}
	    } else {
		if ($dow == 0 && $ns < 7200) {
		    $USACAN_DST=0;   return 0;
		} else {
		    $USACAN_DST=1;   return 1;
		}
	    }
	} else {
	    $USACAN_DST=0;   return 0;
	}
    } elsif ($mm == 11) {
	if ($dd > 7) {
	    $USACAN_DST=0;   return 0;
	} elsif ($dd < ($dow+1)) {
	    $USACAN_DST=1;   return 1;
	} elsif ($dow == 0) {
	    if ($timezone) { # UTC calculations
		if ($ns < (7200+($timezone-1)*3600)) {
		    $USACAN_DST=1;   return 1;
		} else {
		    $USACAN_DST=0;   return 0;
		}
	    } else { # local time calculations
		if ($ns < 7200) {
		    $USACAN_DST=1;   return 1;
		} else {
		    $USACAN_DST=0;   return 0;
		}
	    }
	} else {
	    $USACAN_DST=0;   return 0;
	}
    } # end of month checks
} # end of subroutine dstcalc




sub NA_dstcalc {
#**********************************************************************
# FSA-LSO
#     This is returns 1 if Daylight Savings Time is in effect and 0 if 
#       Standard time is in effect.
#     Based on first Sunday in April and last Sunday in October at 2 am.
#**********************************************************************
    
	$NA_DST=0;
	$mm = $mon;
	$dd = $mday;
	$ns = $dsec;
	$dow= $wday;

    if ($mm < 4 || $mm > 10) {
	$NA_DST=0;   return 0;
    } elsif ($mm >= 5 && $mm <= 9) {
	$NA_DST=1;   return 1;
    } elsif ($mm == 4) {
	if ($dd > 7) {
	    $NA_DST=1;   return 1;
	} elsif ($dd >= ($dow+1)) {
	    if ($timezone) {
		if ($dow == 0 && $ns < (7200+$timezone*3600)) {
		    $NA_DST=0;   return 0;
		} else {
		    $NA_DST=1;   return 1;
		}
	    } else {
		if ($dow == 0 && $ns < 7200) {
		    $NA_DST=0;   return 0;
		} else {
		    $NA_DST=1;   return 1;
		}
	    }
	} else {
	    $NA_DST=0;   return 0;
	}
    } elsif ($mm == 10) {
	if ($dd < 25) {
	    $NA_DST=1;   return 1;
	} elsif ($dd < ($dow+25)) {
	    $NA_DST=1;   return 1;
	} elsif ($dow == 0) {
	    if ($timezone) { # UTC calculations
		if ($ns < (7200+($timezone-1)*3600)) {
		    $NA_DST=1;   return 1;
		} else {
		    $NA_DST=0;   return 0;
		}
	    } else { # local time calculations
		if ($ns < 7200) {
		    $NA_DST=1;   return 1;
		} else {
		    $NA_DST=0;   return 0;
		}
	    }
	} else {
	    $NA_DST=0;   return 0;
	}
    } # end of month checks
} # end of subroutine dstcalc




sub GBR_dstcalc {
#**********************************************************************
# LSM-LSO
#     This is returns 1 if Daylight Savings Time is in effect and 0 if 
#       Standard time is in effect.
#     Based on last Sunday in March and last Sunday in October at 1 am.
#**********************************************************************
    
	$GBR_DST=0;
	$mm = $mon;
	$dd = $mday;
	$ns = $dsec;
	$dow= $wday;

    if ($mm < 3 || $mm > 10) {
	$GBR_DST=0;   return 0;
    } elsif ($mm >= 4 && $mm <= 9) {
	$GBR_DST=1;   return 1;
    } elsif ($mm == 3) {
	if ($dd < 25) {
	    $GBR_DST=0;   return 0;
	} elsif ($dd < ($dow+25)) {
	    $GBR_DST=0;   return 0;
	} elsif ($dow == 0) {
	    if ($timezone) { # UTC calculations
		if ($ns < (3600+($timezone-1)*3600)) {
		    $GBR_DST=0;   return 0;
		} else {
		    $GBR_DST=1;   return 1;
		}
	    } else { # local time calculations
		if ($ns < 3600) {
		    $GBR_DST=0;   return 0;
		} else {
		    $GBR_DST=1;   return 1;
		}
	    }
	} else {
	    $GBR_DST=1;   return 1;
	}
    } elsif ($mm == 10) {
	if ($dd < 25) {
	    $GBR_DST=1;   return 1;
	} elsif ($dd < ($dow+25)) {
	    $GBR_DST=1;   return 1;
	} elsif ($dow == 0) {
	    if ($timezone) { # UTC calculations
		if ($ns < (3600+($timezone-1)*3600)) {
		    $GBR_DST=1;   return 1;
		} else {
		    $GBR_DST=0;   return 0;
		}
	    } else { # local time calculations
		if ($ns < 3600) {
		    $GBR_DST=1;   return 1;
		} else {
		    $GBR_DST=0;   return 0;
		}
	    }
	} else {
	    $GBR_DST=0;   return 0;
	}
    } # end of month checks
} # end of subroutine dstcalc




sub AUS_dstcalc {
#**********************************************************************
# LSO-LSM
#     This is returns 1 if Daylight Savings Time is in effect and 0 if 
#       Standard time is in effect.
#     Based on last Sunday in October and last Sunday in March at 1 am.
#**********************************************************************
    
	$AUS_DST=0;
	$mm = $mon;
	$dd = $mday;
	$ns = $dsec;
	$dow= $wday;

    if ($mm < 3 || $mm > 10) {
	$AUS_DST=1;   return 1;
    } elsif ($mm >= 4 && $mm <= 9) {
	$AUS_DST=0;   return 0;
    } elsif ($mm == 3) {
	if ($dd < 25) {
	    $AUS_DST=1;   return 1;
	} elsif ($dd < ($dow+25)) {
	    $AUS_DST=1;   return 1;
	} elsif ($dow == 0) {
	    if ($timezone) { # UTC calculations
		if ($ns < (3600+($timezone-1)*3600)) {
		    $AUS_DST=1;   return 1;
		} else {
		    $AUS_DST=0;   return 0;
		}
	    } else { # local time calculations
		if ($ns < 3600) {
		    $AUS_DST=1;   return 1;
		} else {
		    $AUS_DST=0;   return 0;
		}
	    }
	} else {
	    $AUS_DST=0;   return 0;
	}
    } elsif ($mm == 10) {
	if ($dd < 25) {
	    $AUS_DST=0;   return 0;
	} elsif ($dd < ($dow+25)) {
	    $AUS_DST=0;   return 0;
	} elsif ($dow == 0) {
	    if ($timezone) { # UTC calculations
		if ($ns < (3600+($timezone-1)*3600)) {
		    $AUS_DST=0;   return 0;
		} else {
		    $AUS_DST=1;   return 1;
		}
	    } else { # local time calculations
		if ($ns < 3600) {
		    $AUS_DST=0;   return 0;
		} else {
		    $AUS_DST=1;   return 1;
		}
	    }
	} else {
	    $AUS_DST=1;   return 1;
	}
    } # end of month checks
} # end of subroutine dstcalc





sub AUST_dstcalc {
#**********************************************************************
# FSO-LSM
#   TASMANIA ONLY
#     This is returns 1 if Daylight Savings Time is in effect and 0 if 
#       Standard time is in effect.
#     Based on first Sunday in October and last Sunday in March at 1 am.
#**********************************************************************
    
	$AUST_DST=0;
	$mm = $mon;
	$dd = $mday;
	$ns = $dsec;
	$dow= $wday;

    if ($mm < 3 || $mm > 10) {
	$AUST_DST=1;   return 1;
    } elsif ($mm >= 4 && $mm <= 9) {
	$AUST_DST=0;   return 0;
    } elsif ($mm == 3) {
	if ($dd < 25) {
	    $AUST_DST=1;   return 1;
	} elsif ($dd < ($dow+25)) {
	    $AUST_DST=1;   return 1;
	} elsif ($dow == 0) {
	    if ($timezone) { # UTC calculations
		if ($ns < (3600+($timezone-1)*3600)) {
		    $AUST_DST=1;   return 1;
		} else {
		    $AUST_DST=0;   return 0;
		}
	    } else { # local time calculations
		if ($ns < 3600) {
		    $AUST_DST=1;   return 1;
		} else {
		    $AUST_DST=0;   return 0;
		}
	    }
	} else {
	    $AUST_DST=0;   return 0;
	}
    } elsif ($mm == 10) {
	if ($dd >= 8) {
	    $AUST_DST=1;   return 1;
	} elsif ($dd >= ($dow+1)) {
	    if ($timezone) {
		if ($dow == 0 && $ns < (7200+$timezone*3600)) {
		    $AUST_DST=0;   return 0;
		} else {
		    $AUST_DST=1;   return 1;
		}
	    } else {
		if ($dow == 0 && $ns < 3600) {
		    $AUST_DST=0;   return 0;
		} else {
		    $AUST_DST=1;   return 1;
		}
	    }
	} else {
	    $AUST_DST=0;   return 0;
	}
    } # end of month checks
} # end of subroutine dstcalc





sub AUSE_dstcalc {
#**********************************************************************
# FSO-FSA
#   2008+ AUSTRALIA ONLY (country code 61)
#     This is returns 1 if Daylight Savings Time is in effect and 0 if 
#       Standard time is in effect.
#     Based on first Sunday in October and first Sunday in April at 1 am.
#**********************************************************************
    
	$AUSE_DST=0;
	$mm = $mon;
	$dd = $mday;
	$ns = $dsec;
	$dow= $wday;

    if ($mm < 4 || $mm > 10) {
	$AUSE_DST=1;   return 1;
    } elsif ($mm >= 5 && $mm <= 9) {
	$AUSE_DST=0;   return 0;
    } elsif ($mm == 4) {
	if ($dd > 7) {
	    $AUSE_DST=0;   return 1;
	} elsif ($dd >= ($dow+1)) {
	    if ($timezone) {
		if ($dow == 0 && $ns < (3600+$timezone*3600)) {
		    $AUSE_DST=1;   return 0;
		} else {
		    $AUSE_DST=0;   return 1;
		}
	    } else {
		if ($dow == 0 && $ns < 7200) {
		    $AUSE_DST=1;   return 0;
		} else {
		    $AUSE_DST=0;   return 1;
		}
	    }
	} else {
	    $AUSE_DST=1;   return 0;
	}
    } elsif ($mm == 10) {
	if ($dd >= 8) {
	    $AUSE_DST=1;   return 1;
	} elsif ($dd >= ($dow+1)) {
	    if ($timezone) {
		if ($dow == 0 && $ns < (7200+$timezone*3600)) {
		    $AUSE_DST=0;   return 0;
		} else {
		    $AUSE_DST=1;   return 1;
		}
	    } else {
		if ($dow == 0 && $ns < 3600) {
		    $AUSE_DST=0;   return 0;
		} else {
		    $AUSE_DST=1;   return 1;
		}
	    }
	} else {
	    $AUSE_DST=0;   return 0;
	}
    } # end of month checks
} # end of subroutine dstcalc





sub NZL_dstcalc {
#**********************************************************************
# FSO-TSM
#     This is returns 1 if Daylight Savings Time is in effect and 0 if 
#       Standard time is in effect.
#     Based on first Sunday in October and third Sunday in March at 1 am.
#**********************************************************************
    
	$NZL_DST=0;
	$mm = $mon;
	$dd = $mday;
	$ns = $dsec;
	$dow= $wday;

    if ($mm < 3 || $mm > 10) {
	$NZL_DST=1;   return 1;
    } elsif ($mm >= 4 && $mm <= 9) {
	$NZL_DST=0;   return 0;
    } elsif ($mm == 3) {
	if ($dd < 14) {
	    $NZL_DST=1;   return 1;
	} elsif ($dd < ($dow+14)) {
	    $NZL_DST=1;   return 1;
	} elsif ($dow == 0) {
	    if ($timezone) { # UTC calculations
		if ($ns < (3600+($timezone-1)*3600)) {
		    $NZL_DST=1;   return 1;
		} else {
		    $NZL_DST=0;   return 0;
		}
	    } else { # local time calculations
		if ($ns < 3600) {
		    $NZL_DST=1;   return 1;
		} else {
		    $NZL_DST=0;   return 0;
		}
	    }
	} else {
	    $NZL_DST=0;   return 0;
	}
    } elsif ($mm == 10) {
	if ($dd >= 8) {
	    $NZL_DST=1;   return 1;
	} elsif ($dd >= ($dow+1)) {
	    if ($timezone) {
		if ($dow == 0 && $ns < (7200+$timezone*3600)) {
		    $NZL_DST=0;   return 0;
		} else {
		    $NZL_DST=1;   return 1;
		}
	    } else {
		if ($dow == 0 && $ns < 3600) {
		    $NZL_DST=0;   return 0;
		} else {
		    $NZL_DST=1;   return 1;
		}
	    }
	} else {
	    $NZL_DST=0;   return 0;
	}
    } # end of month checks
} # end of subroutine dstcalc




sub NZLN_dstcalc {
#**********************************************************************
# LSS-FSA
#   2007+ NEW ZEALAND (country code 64)
#     This is returns 1 if Daylight Savings Time is in effect and 0 if 
#       Standard time is in effect.
#     Based on last Sunday in September and first Sunday in April at 1 am.
#**********************************************************************
    
	$NZLN_DST=0;
	$mm = $mon;
	$dd = $mday;
	$ns = $dsec;
	$dow= $wday;

    if ($mm < 4 || $mm > 9) {
	$NZLN_DST=1;   return 1;
    } elsif ($mm >= 5 && $mm <= 9) {
	$NZLN_DST=0;   return 0;
    } elsif ($mm == 4) {
	if ($dd > 7) {
	    $NZLN_DST=0;   return 1;
	} elsif ($dd >= ($dow+1)) {
	    if ($timezone) {
		if ($dow == 0 && $ns < (3600+$timezone*3600)) {
		    $NZLN_DST=1;   return 0;
		} else {
		    $NZLN_DST=0;   return 1;
		}
	    } else {
		if ($dow == 0 && $ns < 7200) {
		    $NZLN_DST=1;   return 0;
		} else {
		    $NZLN_DST=0;   return 1;
		}
	    }
	} else {
	    $NZLN_DST=1;   return 0;
	}
    } elsif ($mm == 9) {
	if ($dd < 25) {
	    $NZLN_DST=0;   return 0;
	} elsif ($dd < ($dow+25)) {
	    $NZLN_DST=0;   return 0;
	} elsif ($dow == 0) {
	    if ($timezone) { # UTC calculations
		if ($ns < (3600+($timezone-1)*3600)) {
		    $NZLN_DST=0;   return 0;
		} else {
		    $NZLN_DST=1;   return 1;
		}
	    } else { # local time calculations
		if ($ns < 3600) {
		    $NZLN_DST=0;   return 0;
		} else {
		    $NZLN_DST=1;   return 1;
		}
	    }
	} else {
	    $NZLN_DST=1;   return 1;
	}
    } # end of month checks
} # end of subroutine dstcalc





sub BZL_dstcalc {
#**********************************************************************
# TSO-LSF
#     This is returns 1 if Daylight Savings Time is in effect and 0 if 
#       Standard time is in effect. Brazil
#     Based on Third Sunday October to Last Sunday February at 1 am.
#**********************************************************************
    
	$BZL_DST=0;
	$mm = $mon;
	$dd = $mday;
	$ns = $dsec;
	$dow= $wday;

    if ($mm < 2 || $mm > 10) {
	$BZL_DST=1;   return 1;
    } elsif ($mm >= 3 && $mm <= 9) {
	$BZL_DST=0;   return 0;
    } elsif ($mm == 2) {
	if ($dd < 22) {
	    $BZL_DST=1;   return 1;
	} elsif ($dd < ($dow+22)) {
	    $BZL_DST=1;   return 1;
	} elsif ($dow == 0) {
	    if ($timezone) { # UTC calculations
		if ($ns < (3600+($timezone-1)*3600)) {
		    $BZL_DST=1;   return 1;
		} else {
		    $BZL_DST=0;   return 0;
		}
	    } else { # local time calculations
		if ($ns < 3600) {
		    $BZL_DST=1;   return 1;
		} else {
		    $BZL_DST=0;   return 0;
		}
	    }
	} else {
	    $BZL_DST=0;   return 0;
	}
    } elsif ($mm == 10) {
	if ($dd < 22) {
	    $BZL_DST=0;   return 0;
	} elsif ($dd < ($dow+22)) {
	    $BZL_DST=0;   return 0;
	} elsif ($dow == 0) {
	    if ($timezone) { # UTC calculations
		if ($ns < (3600+($timezone-1)*3600)) {
		    $BZL_DST=0;   return 0;
		} else {
		    $BZL_DST=1;   return 1;
		}
	    } else { # local time calculations
		if ($ns < 3600) {
		    $BZL_DST=0;   return 0;
		} else {
		    $BZL_DST=1;   return 1;
		}
	    }
	} else {
	    $BZL_DST=1;   return 1;
	}
    } # end of month checks
} # end of subroutine dstcalc
