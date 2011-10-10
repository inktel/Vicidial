#!/usr/bin/perl
#
# VICIDIAL_IN_new_leads_file.pl version 2.4
#
# DESCRIPTION:
# script lets you insert leads into the vicidial_list table from a TAB-delimited
# lead file that is in the proper format. (for format see --help)
#
# It is recommended that you run this program on the local Asterisk machine
#
# NOTE: the machine this is run on must have a servers entry in the database
#
# Copyright (C) 2011  Matt Florell <vicidial@gmail.com>    LICENSE: AGPLv2
#
#
# CHANGES
# 60615-1543 - Added gmt_offset_now lookup for each lead
#            - Added option to force a gmt value(field after COMMENTS field)
# 60616-0958 - Added listID override feature to force all leads into same list
# 60807-1003 - Changed to DBI
#            - Changed to use /etc/astguiclient.conf for configs
# 60906-1055 - Added filter of non-digits in alt_phone field
# 60913-1236 - Fixed MySQL bugs and non-debug bug
#            - Added duplicate check flag option within same list
# 61127-1132 - Added new DST methods
# 61128-1037 - Added postal codes GMT lookup option
# 70510-1518 - Added campaign and system duplicate check and phonecode override
# 70801-0912 - Added called count and status to import leads format (fields 24 and 25)
# 70815-2128 - Added entry_date to import leads format (field 26)
# 80128-0105 - Fixed bugs in file loading
# 80428-0320 - UTF8 update
# 80713-0023 - Added last_local_call_time field default of 2008-01-01
# 80730-1607 - Added minicsv format
# 80812-1204 - Added FTP grab options and summary email options
# 80829-2301 - Added multi-alt-phone entry insertion capability
# 90324-1038 - Added minicsv02 format
# 90401-1340 - Fixed quiet flag functionality
# 90721-1315 - Added rank and owner as vicidial_list fields, stdrankowner format
# 90728-1255 - Added fixed254 file format
# 90802-0758 - Added sctab08 file format
# 90810-0956 - Added sccsv11 file format
# 90830-0953 - Added forcelistfilename option
# 91112-0645 - Added title/alt_phone duplicate checks
# 91129-2202 - removed SELECT STAR and formatting fixes
# 100118-0527 - Added new Australian and New Zealand DST schemes (FSO-FSA and LSS-FSA)
# 100204-2333 - Added dccsv10 file format
# 100221-0939 - Added dccsv43 file format with custom cid lookup
# 100427-0434 - Added ability to create new list in system for each new loaded file
# 100610-0756 - Added dccsv52 file format
# 100624-2143 - Added dccsvref52 file format
# 100928-1121 - Added file-prefix-filter option
# 110420-0944 - Fixed file prefix issue with multiple processes running
# 110424-0948 - Added time-zone-code-gmt option to use time zone code from the owner field
# 110705-1913 - Added options for USACAN prefix(no 0 or 1) and valid areacode filtering
# 110929-1423 - Added new format for abbreviated timezone in owner(pipe30tz) and list creation options
#

$version = '110929-1423';

$secX = time();
$MT[0]='';
$Ealert='';
$force_quiet=0;
$forcelistfilename=0;

($sec,$min,$hour,$mday,$mon,$year,$wday,$yday,$isdst) = localtime(time);
$year = ($year + 1900);
$mon++;
if ($hour < 10) {$hour = "0$hour";}
if ($min < 10) {$min = "0$min";}
if ($sec < 10) {$sec = "0$sec";}
if ($mon < 10) {$mon = "0$mon";}
if ($mday < 10) {$mday = "0$mday";}
$pulldate0 = "$year-$mon-$mday $hour:$min:$sec";
$insert_date = "$year-$mon-$mday $hour:$min:$sec";
$listdate = "$year$mon$mday";
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

	if ($args =~ /--version/i)
		{
		print "$version\n";
		exit;
		}
	if ($args =~ /--help|-h/i)
		{
		print "allowed run time options:\n";
		print "  [--quiet] = quiet\n";
		print "  [--test] = test\n";
		print "  [--version] = version\n";
		print "  [--forcegmt] = forces gmt value of column after comments column\n";
		print "  [--debug] = debug output\n";
		print "  [--format=standard] = ability to define a format, standard is default, formats allowed shown in examples\n";
		print "  [--forcelistid=1234] = overrides the listID given in the file with the 1234\n";
		print "  [--forcelistfilename] = overrides the listID using last number in filename: (XYZ_1234.txt = list ID 1234)\n";
		print "  [--forcephonecode=44] = overrides the phone_code given in the lead with the 44\n";
		print "  [--file-prefix-filter=WXYZ] = will only process lead files that begin with the characters you define\n";
		print "  [--new-list-for-each-file] = creates a new list for each file loaded, listID = YYYYMMDDX where X is incremented\n";
		print "  [--new-listid-prefix=X] = prefix for listID when creating new lists, must be only numbers, and 4 or less digits\n";
		print "  [--new-listname-prefix=X] = prefix for list name when creating new lists, will be followed by filename\n";
		print "  [--new-list-campaign=X] = campaign that the new list will be assigned to\n";
		print "  [--new-list-active=X] = Y or N, if list is to be set as active when created, default N\n";
		print "  [--new-list-reset-times=X] = reset times(4 digits each dash separated) default is blank\n";
		print "  [--new-list-tz-setting=X] = COUNTRY_AND_AREA_CODE|POSTAL_CODE|NANPA_PREFIX|OWNER_TIME_ZONE_CODE default COUNTRY_AND_AREA_CODE\n";
		print "  [--USACAN-prefix-check] = check for the 4th digit 2-9, USA and Canada validation\n";
		print "  [--USACAN-areacode-check] = check for the valid phone code 1 areacodes, USA and Canada validation\n";
		print "  [--duplicate-check] = checks for the same phone number in the same list id before inserting lead\n";
		print "  [--duplicate-campaign-check] = checks for the same phone number in the same campaign before inserting lead\n";
		print "  [--duplicate-system-check] = checks for the same phone number in the entire system before inserting lead\n";
		print "  [--duplicate-tap-list-check] = checks for the same title/alt-number in the same list ID before inserting lead\n";
		print "  [--duplicate-tap-system-check] = checks for the same title/alt-number in the entire system before inserting lead\n";
		print "  [--postal-code-gmt] = checks for the time zone based on the postal code given where available\n";
		print "  [--time-zone-code-gmt] = checks for the time zone based on the owner field time zone code given where available\n";
		print "  [--ftp-pull] = grabs lead files from a remote FTP server, uses REPORTS FTP login information\n";
		print "  [--ftp-dir=leads_in] = remote FTP server directory to grab files from, should have a DONE sub-directory\n";
		print "  [--email-list=test@test.com:test2@test.com] = send email results for each file to these addresses\n";
		print "  [--email-sender=vicidial@localhost] = sender for the email results\n";
		print "  [-h] = this help screen\n\n";
		print "\n";
		print "This script takes in lead files in the following order when they are placed in the $PATHhome/LEADS_IN directory to be imported into the vicidial_list table (examples):\n\n";
		print "standard:\n";
		print "vendor_lead_code|source_id|list_id|phone_code|phone_number|title|first_name|middle|last_name|address1|address2|address3|city|state|province|postal_code|country|gender|date_of_birth|alt_phone|email|security_phrase|COMMENTS|called_count|status|entry_date|multi-alt-entries\n";
		print "3857822|31022|105|01144|1625551212|MRS|B||BURTON|249 MUNDON ROAD|MALDON|ESSEX||||CM9 6PW|UK||||||COMMENTS|2|B|2007-08-09 00:00:00|7275551212_1_work!7275551213_61_sister house!7275551214_44_neighbor\n\n";
		print "stdrankowner:\n";
		print "vendor_lead_code|source_id|list_id|phone_code|phone_number|title|first_name|middle|last_name|address1|address2|address3|city|state|province|postal_code|country|gender|date_of_birth|alt_phone|email|security_phrase|COMMENTS|called_count|status|entry_date|rank|owner|multi-alt-entries\n";
		print "3857822|31022|105|01144|1625551212|MRS|B||BURTON|249 MUNDON ROAD|MALDON|ESSEX||||CM9 6PW|UK||||||COMMENTS|2|B|2007-08-09 00:00:00|99|6666|7275551212_1_work!7275551213_61_sister house!7275551214_44_neighbor\n\n";
		print "minicsv:\n";
		print "address1,city,name,phone_number,state,postal_code\n";
		print "\"105 Fifth St\",\"Steinhatchee\",\"Frank Smith\",\"3525556601\",\"FL\",\"32359\"\n\n";
		print "minicsv02:\n";
		print "name,address1,city,state,postal_code,phone_number\n";
		print "\"Adam Smith\",\"123 Fourth St\",\"Englishtown\",\"NS\",\"B0C1H0\",\"902-555-1212\"\n\n";
		print "fixed254:\n";
		print "fixed width(without the quotes)\n";
		print "\"9185551212ROSE            SMITHS                  155 TIGER MOUNTAIN RD.                  RR 1 BOX 107                            HENRYETTA                   OK74437-941DEMG  226555                                                   0                     \"\n\n";
		print "sctab08:\n";
		print "STATE\tNAME\tPHONE\tFAX\tADDRESS\tADDRESS2\tCITY\tZIP\n";
		print "AL\tTom Wilson\t205 5551212\t\t 1689 15th Ave\t\tBESSEMER\t35020\n\n";
		print "sccsv11:\n";
		print "STATE,ID,NAME,ADDRESS,ADDRESS2,CITY,COUNTY,PHONE,FAX,ZIP,COMMENTS\n";
		print "AL,16700,Fairfax Retirement Center (Crowder House),1324 25th St,,FAIRFAX,JEFFERSON,(205) 555-0508,,35733,Comments\n\n";
		print "dccsv10:\n";
		print "VENDOR_ID,FIRST_NAME,LAST_NAME,PHONE_1,PHONE_2,PHONE_3,PHONE_4,PHONE_5,PHONE_6,PHONE_7\n";
		print "\"100998\",\"ANGELA    \",\"SMITH     \",\"3145551212\",\"3145551213\",\"3145551214\",\"0\",\"3145551215\",\"3145551216\",\"0\",\n\n";
		print "pipe30tz:\n";
		print "TEST01||09292011|1|5125554727||Mike||Frank|||||||||||||||||C|2145559922|TESTSURVEY|TESTSURVEY|111\n";
		print "dccsv43, dccsvref51, dccsv52 and dccsvref52:\n";
		print "---format too confusing to list in the help screen---\n\n";

		exit;
		}
	else
		{
		if ($args =~ /--quiet/i)
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

		if ($args =~ /--test/i)
			{
			$T=1;
			$TEST=1;
			if ($q < 1) {print "\n----- TESTING -----\n\n";}
			}
		if ($args =~ /-forcegmt/i)
			{
			$forcegmt=1;
			if ($q < 1) {print "\n----- FORCE GMT -----\n\n";}
			}
		if ($args =~ /-forcelistid=/i)
			{
			@data_in = split(/-forcelistid=/,$args);
				$forcelistid = $data_in[1];
				$forcelistid =~ s/ .*//gi;
			if ($q < 1) {print "\n----- FORCE LISTID OVERRIDE: $forcelistid -----\n\n";}
			}
		else
			{$forcelistid = '';}

		if ($args =~ /-forcelistfilename/i)
			{
			$forcelistfilename=1;
			if ($q < 1) {print "\n----- FORCE LISTID FROM FILENAME -----\n\n";}
			}

		if ($args =~ /-format=/i)
			{
			@data_in = split(/-format=/,$args);
				$format = $data_in[1];
				$format =~ s/ .*//gi;
			if ($q < 1) {print "\n----- FORMAT OVERRIDE: $format -----\n\n";}
			}
		else
			{$format = 'standard';}

		if ($args =~ /--forcephonecode=/i)
			{
			@data_in = split(/--forcephonecode=/,$args);
				$forcephonecode = $data_in[1];
				$forcephonecode =~ s/ .*//gi;
			if ($q < 1) {print "\n----- FORCE PHONECODE OVERRIDE: $forcephonecode -----\n\n";}
			}
		else
			{$forcephonecode = '';}

		if ($args =~ /--file-prefix-filter=/i)
			{
			@data_in = split(/--file-prefix-filter=/,$args);
				$file_prefix_filter = $data_in[1];
				$file_prefix_filter =~ s/ .*//gi;
			if ($q < 1) {print "\n----- FILE PREFIX FILTER: $file_prefix_filter -----\n\n";}
			}
		else
			{$file_prefix_filter = '';}

		if ($args =~ /--USACAN-prefix-check/i)
			{
			$usacan_prefix_check=1;
			if ($q < 1) {print "\n----- USACAN PREFIX CHECK -----\n\n";}
			}
		if ($args =~ /--USACAN-areacode-check/i)
			{
			$usacan_areacode_check=1;
			if ($q < 1) {print "\n----- USACAN AREACODE CHECK -----\n\n";}
			}
		if ($args =~ /-duplicate-check/i)
			{
			$dupcheck=1;
			if ($q < 1) {print "\n----- DUPLICATE CHECK -----\n\n";}
			}
		if ($args =~ /-duplicate-campaign-check/i)
			{
			$dupcheckcamp=1;
			if ($q < 1) {print "\n----- DUPLICATE CAMPAIGN CHECK -----\n\n";}
			}
		if ($args =~ /-duplicate-system-check/i)
			{
			$dupchecksys=1;
			if ($q < 1) {print "\n----- DUPLICATE SYSTEM CHECK -----\n\n";}
			}
		if ($args =~ /-duplicate-tap-list-check/i)
			{
			$duptapchecklist=1;
			if ($q < 1) {print "\n----- DUPLICATE TITLE/ALT-PHONE LIST CHECK -----\n\n";}
			}
		if ($args =~ /-duplicate-tap-system-check/i)
			{
			$duptapchecksys=1;
			if ($q < 1) {print "\n----- DUPLICATE TITLE/ALT-PHONE SYSTEM CHECK -----\n\n";}
			}
		if ($args =~ /-postal-code-gmt/i)
			{
			$postalgmt=1;
			if ($q < 1) {print "\n----- POSTAL CODE TIMEZONE -----\n\n";}
			}
		if ($args =~ /-time-zone-code-gmt/i)
			{
			$tzcodegmt=1;
			if ($q < 1) {print "\n----- TZ CODE TIMEZONE -----\n\n";}
			}

		if ($args =~ /-new-list-for-each-file/i)
			{
			$new_list_for_each_file=1;
			if ($q < 1) {print "\n----- NEW LIST FOR EACH FILE -----\n\n";}
			}
		if ($args =~ /--new-listid-prefix=/i)
			{
			@data_in = split(/--new-listid-prefix=/,$args);
				$list_id_prefix = $data_in[1];
				$list_id_prefix =~ s/ .*//gi;
			if ($q < 1) {print "\n----- NEW LISTID PREFIX: $list_id_prefix -----\n\n";}
			}
		else
			{$list_id_prefix = '';}
		if ($args =~ /--new-listname-prefix=/i)
			{
			@data_in = split(/--new-listname-prefix=/,$args);
				$list_name_prefix = $data_in[1];
				$list_name_prefix =~ s/ .*//gi;
			if ($q < 1) {print "\n----- NEW LISTNAME PREFIX: $list_name_prefix -----\n\n";}
			}
		else
			{$list_name_prefix = '';}
		if ($args =~ /--new-list-campaign=/i)
			{
			@data_in = split(/--new-list-campaign=/,$args);
				$list_campaign = $data_in[1];
				$list_campaign =~ s/ .*//gi;
			if ($q < 1) {print "\n----- NEW LIST CAMPAIGN: $list_campaign -----\n\n";}
			}
		else
			{$list_campaign = '';}
		if ($args =~ /--new-list-active=/i)
			{
			@data_in = split(/--new-list-active=/,$args);
				$active_list = $data_in[1];
				$active_list =~ s/ .*//gi;
			if ($q < 1) {print "\n----- NEW LIST ACTIVE: $active_list -----\n\n";}
			}
		else
			{$active_list = 'N';}
		if ($args =~ /--new-list-reset-times=/i)
			{
			@data_in = split(/--new-list-reset-times=/,$args);
				$reset_time = $data_in[1];
				$reset_time =~ s/ .*//gi;
			if ($q < 1) {print "\n----- NEW LIST RESET TIMES: $reset_time -----\n\n";}
			}
		else
			{$reset_time = '';}
		if ($args =~ /--new-list-tz-setting=/i)
			{
			@data_in = split(/--new-list-tz-setting=/,$args);
				$time_zone_setting = $data_in[1];
				$time_zone_setting =~ s/ .*//gi;
			if ($q < 1) {print "\n----- NEW LIST TZ SETTING: $time_zone_setting -----\n\n";}
			}
		else
			{$time_zone_setting = 'COUNTRY_AND_AREA_CODE';}

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
	}
### end parsing run-time options ###

if ($q < 1)
	{
	print "\n\n\n\n\n\n\n\n\n\n\n\n-- VICIDIAL_IN_new_leads_file.pl --\n\n";
	print "This program is designed to take a tab delimited file and import it into the VICIDIAL system. \n\n";
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

$suf = '.txt';
$people_packages_id_update='';
$dir1 = "$PATHhome/LEADS_IN";
$dir2 = "$PATHhome/LEADS_IN/DONE";

	if($DBX){print STDERR "\nLEADS_IN directory: |$dir1|\n";}


$files_copied_count=0;
$fcc=0;
@FTPfiles=@MT;

if ($ftp_pull > 0)
	{
	$i=0;
	use Net::FTP;

	$ftp = Net::FTP->new("$VARREPORT_host", Port => "$VARREPORT_port", Debug => "$DBX",  Passive => 1)
					or die "Can't connect: $@\n";
	$ftp->login("$VARREPORT_user","$VARREPORT_pass") or die "Cannot log in /: $!";
	  @FILES = $ftp->dir("$ftp_dir")
	or die "Can't get a list of files in $ftp_dir: $!";

	foreach(@FILES)
		{
		if ($DBX > 0) {print "$FILES[$i]\n";}
		if ($FILES[$i] !~ /^d/i)
			{
			chomp($FILES[$i]);
			@FILEDETAILS = split(/ \d\d:\d\d /, $FILES[$i]);
			$FILES[$i] = "$FILEDETAILS[1]";
			$FILES[$i] =~ s/ *$//gi;
			
			$passed_file_filter=1;
			if (length($file_prefix_filter)>0)
				{
				$passed_file_filter=0;

				if ($FILES[$i] !~ /^$file_prefix_filter/)
					{
					if ($DB > 0) {print "SKIPPING FILE, NO FILTER MATCH: $FILES[$i]\n";}
					}
				else
					{$passed_file_filter=1;}
				}
			if ( (length($FILES[$i]) > 4) && ($passed_file_filter > 0) )
				{
				$GOODfname = $FILES[$i];
				$FILES[$i] =~ s/ /_/gi;
				$FILES[$i] =~ s/\(|\)|\||\\|\/|\'|\"|//gi;
				$ftp->rename("$ftp_dir/$GOODfname","$ftp_dir/$FILES[$i]");
				$FILEname = $FILES[$i];
				$ftp->get("$ftp_dir/$FILES[$i]", "$dir1/$FILES[$i]");

				if (!$TEST) {$ftp->rename("$ftp_dir/$FILES[$i]", "$ftp_dir/DONE/$FILES[$i]");}
				if ($DB > 0) {print "FTP FILE COPIED: $FILES[$i]\n";}
				$FTPfiles[$fcc] = $FILES[$i];
				$fcc++;
				$files_copied_count++;
				}
			}
		$i++;
		}
	if (!$q) {print "$ftp_dir - $VARREPORT_host - $#FILES - $files_copied_count\n";}

	@FILES = @FTPfiles;
	}
else
	{
	@FILES=@MT;
	opendir(FILE, "$dir1/");
	@FILES = readdir(FILE);
	}


$i=0;

foreach(@FILES)
	{
	$size1 = 0;
	$size2 = 0;
	$person_id_delete = '';
	$transaction_id_delete = '';
	$forcelistfilename_name = '';

	if (length($FILES[$i]) > 4)
		{
		$size1 = (-s "$dir1/$FILES[$i]");
		if (!$q) {print "$FILES[$i] $size1\n";}
		sleep(2);
		$size2 = (-s "$dir1/$FILES[$i]");
		if (!$q) {print "$FILES[$i] $size2\n\n";}

		$passed_file_filter=1;
		if (length($file_prefix_filter)>0)
			{
			$passed_file_filter=0;

			if ($FILES[$i] !~ /^$file_prefix_filter/)
				{
				if ($DB > 0) {print "SKIPPING FILE, NO FILTER MATCH: $FILES[$i]\n";}
				}
			else
				{$passed_file_filter=1;}
			}

		if ( ($FILES[$i] !~ /^TRANSFERRED/i) && ($size1 eq $size2) && (length($FILES[$i]) > 4) && ($passed_file_filter > 0) )
			{
			$GOODfname = $FILES[$i];
			$FILES[$i] =~ s/ /_/gi;
			$FILES[$i] =~ s/\(|\)|\||\\|\/|\'|\"|//gi;
			rename("$dir1/$GOODfname","$dir1/$FILES[$i]");
			$FILEname = $FILES[$i];

			`cp -f $dir1/$FILES[$i] $dir2/$source$FILES[$i]`;

			if ($forcelistfilename > 0)
				{
				$forcelistfilename_name = $FILES[$i];
				$forcelistfilename_name =~ s/\..*//gi;
				@forcelistfilename_nameARY = split(/_/, $forcelistfilename_name);
				$forcelistfilename_listid = $forcelistfilename_nameARY[$#forcelistfilename_nameARY];
				$forcelistfilename_listid =~ s/\D//gi;
				if ($DB > 0) {print "$forcelistfilename_listid|$#forcelistfilename_nameARY|$forcelistfilename_name|$FILES[$i]\n";}
				}

			if ($new_list_for_each_file > 0)
				{
				$dup_list_id=1;
				$xloop=0;
				$x=1;
				$new_list_id = "$list_id_prefix$listdate$x";
				while ( ($xloop < 10000) && ($dup_list_id > 0) )
					{
					$stmtA = "select count(*) from vicidial_lists where list_id='$new_list_id';";
						if($DBX){print STDERR "\n|$stmtA|\n";}
					$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
					$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
					$sthArows=$sthA->rows;
					if ($sthArows > 0)
						{
						@aryA = $sthA->fetchrow_array;
						$dup_list_id = $aryA[0];
						}
					$sthA->finish();
					if ($dup_list_id > 0)
						{
						$x++;
						$new_list_id = "$list_id_prefix$listdate$x";
						}
					$xloop++;
					}


				$forcelistfilename_listid = $new_list_id;
				$stmtZ = "INSERT INTO vicidial_lists (list_id,list_name,list_description,campaign_id,active,list_changedate,time_zone_setting,reset_time) values('$new_list_id','$list_name_prefix $FILES[$i]','Created: $insert_date','$list_campaign','$active_list','$insert_date','$time_zone_setting','$reset_time');";
					if (!$T) {$affected_rows = $dbhA->do($stmtZ); } #  or die  "Couldn't execute query: |$stmtZ|\n";
				if ($DB > 0) {print "LIST CREATED: $new_list_id|$affected_rows|$stmtZ\n";}
				}


			### open the in file for reading ###
			open(infile, "$dir2/$source$FILES[$i]")
					|| die "Can't open $source$FILES[$i]: $!\n";


		### Grab Server values from the database
		$stmtA = "SELECT local_gmt FROM servers where server_ip = '$server_ip';";
		$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
		$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
		@aryA = $sthA->fetchrow_array;
		$DBSERVER_GMT		=		$aryA[0];
		if (length($DBSERVER_GMT)>0)	{$SERVER_GMT = $DBSERVER_GMT;}
		$sthA->finish();

			$LOCAL_GMT_OFF = $SERVER_GMT;
			$LOCAL_GMT_OFF_STD = $SERVER_GMT;

		if ($isdst) {$LOCAL_GMT_OFF++;} 
		if ($DB) {print "SEED TIME  $secX      :   $year-$mon-$mday $hour:$min:$sec  LOCAL GMT OFFSET NOW: $LOCAL_GMT_OFF\n";}


		$a=0;	### each line of input file counter ###
		$b=0;	### status of 'APPROVED' counter ###
		$c=0;	### status of 'DECLINED' counter ###
		$d=0;	### status of 'REFERRED' counter ###
		$e=0;	### status of 'ERROR' counter ###
		$f=0;	### number of 'DUPLICATE' counter ###
		$g=0;	### number of leads with multi-alt-entries
		$h=0;	### number of 'INVALID' counter ###

		$multi_insert_counter=0;
		$multistmt='';
		$rank='';
		$owner='';

		#if ($DB)
		while (<infile>)
		{
		@m=@MT;
	#		print "$a| $number\n";
			$number = $_;
			$raw_number = $number;
			chomp($number);
	#		$number =~ s/,/\|/gi;
			$number =~ s/\t/\|/gi;
			$number =~ s/\'|\t|\r|\n|\l//gi;
			$number =~ s/\'|\t|\r|\n|\l//gi;
			$number =~ s/\",,,,,,,\"/\|\|\|\|\|\|\|/gi;
			$number =~ s/\",,,,,,\"/\|\|\|\|\|\|/gi;
			$number =~ s/\",,,,,\"/\|\|\|\|\|/gi;
			$number =~ s/\",,,,\"/\|\|\|\|/gi;
			$number =~ s/\",,,\"/\|\|\|/gi;
			$number =~ s/\",,\"/\|\|/gi;
			$number =~ s/\",\"/\|/gi;
			$number =~ s/\"//gi;
		@m = split(/\|/, $number);

		$format_set=0;

		# This is the format for the minicsv02 lead files
		# name,address1,city,state,postal_code,phone_number
		# "Adam Smith","123 Fourth St","Englishtown","NS","B0C1H0","902-555-1212"
			if ( ($format =~ /minicsv02/) && ($format_set < 1) )
				{
				@name=@MT;
				$vendor_lead_code =		'';
				$source_id =			'';
				$list_id =				'995';
				$phone_code =			'1';
				$phone_number =			$m[5];		chomp($phone_number);	$phone_number =~ s/\D//gi;
					$USarea = 			substr($phone_number, 0, 3);
				$title =				'';
				$full_name =			$m[0];		@name = split(/ /, $full_name);
				$first_name =			$name[0];		chomp($first_name);
				$last_name =			"$name[1] $name[2]";		chomp($first_name);
				$middle_initial =		'';
				$address1 =				$m[1];		chomp($address1);
				$address2 =				'';
				$address3 =				'';
				$city =					$m[2];		chomp($city);
				$state =				$m[3];		chomp($state);
				$province =				'';
				$postal_code =			$m[4];		chomp($postal_code);
				$country =				'USA';
				$gender =				'U';
				$date_of_birth =		'0000-00-00';
				$alt_phone =			'';
				$email =				'';
				$security_phrase =		'';
				$comments =				'';
				$called_count =			0;
				$status =				'NEW';

				$format_set++;
				}

		# This is the format for the minicsv lead files
		#"105 Fifth St","Steinhatchee","Frank Smith","3525556601","FL","32359"
			if ( ($format =~ /minicsv/) && ($format_set < 1) )
				{
				@name=@MT;
				$vendor_lead_code =		'';
				$source_id =			'';
				$list_id =				'995';
				$phone_code =			'1';
				$phone_number =			$m[3];		chomp($phone_number);	$phone_number =~ s/\D//gi;
					$USarea = 			substr($phone_number, 0, 3);
				$title =				'';
				$full_name =			$m[2];		@name = split(/ /, $full_name);
				$first_name =			$name[1];		chomp($first_name);
				$last_name =			"$name[0] $name[2]";		chomp($first_name);
				$middle_initial =		'';
				$address1 =				$m[0];		chomp($address1);
				$address2 =				'';
				$address3 =				'';
				$city =					$m[1];		chomp($city);
				$state =				$m[4];		chomp($state);
				$province =				'';
				$postal_code =			$m[5];		chomp($postal_code);
				$country =				'USA';
				$gender =				'U';
				$date_of_birth =		'0000-00-00';
				$alt_phone =			'';
				$email =				'';
				$security_phrase =		'';
				$comments =				'';
				$called_count =			0;
				$status =				'NEW';

				$format_set++;
				}

		# This is the format for the fixed254 lead files
		#"9185551212ROSE            SMITHS                  155 TIGER MOUNTAIN RD.                  RR 1 BOX 107                            HENRYETTA                   OK74437-941DEMG  226555                                                   0                     "
			if ( ($format =~ /fixed254/) && ($format_set < 1) )
				{
				$phone_number =			substr($raw_number, 0, 10);		$phone_number =~ s/\D//gi;
					$USarea = 			substr($phone_number, 0, 3);
				$phone_code =			'1';
				$first_name =			substr($raw_number, 10, 16);	$first_name =~ s/\s+$//gi;
				$last_name =			substr($raw_number, 26, 24);	$last_name =~ s/\s+$//gi;
				$address1 =				substr($raw_number, 50, 40);	$address1 =~ s/\s+$//gi;
				$address2 =				substr($raw_number, 90, 40);	$address2 =~ s/\s+$//gi;
				$city =					substr($raw_number, 130, 28);	$city =~ s/\s+$//gi;
				$state =				substr($raw_number, 158, 2);
				$postal_code =			substr($raw_number, 160, 5);	$postal_code =~ s/\s+$//gi;
				$province =				substr($raw_number, 169, 4);
				$vendor_lead_code =		substr($raw_number, 173, 8);	$vendor_lead_code =~ s/^\s+//gi;
				$security_phrase =		substr($raw_number, 181, 4);

				$source_id =			'';
				$list_id =				'995';
				$country =				'USA';
				$gender =				'U';
				$date_of_birth =		'0000-00-00';
				$alt_phone =			'';
				$email =				'';
				$comments =				'';
				$called_count =			0;
				$status =				'NEW';

				$format_set++;
				}

		# This is the format for the sctab08 lead files
		# STATE\tNAME\tPHONE\tFAX\tADDRESS\tADDRESS2\tCITY\tZIP\n";
		# AL\tTom Wilson\t205 5551212\t\t 1689 15th Ave\t\tBESSEMER\t35020
			if ( ($format =~ /sctab08/) && ($format_set < 1) )
				{
				@name=@MT;
				$vendor_lead_code =		'';
				$source_id =			'';
				$list_id =				'995';
				$phone_code =			'1';
				$phone_number =			$m[2];		chomp($phone_number);	$phone_number =~ s/\D//gi;
					$USarea = 			substr($phone_number, 0, 3);
				$title =				'';
				$full_name =			$m[1];		@name = split(/ /, $full_name);
				$first_name =			$name[1];		chomp($first_name);
				$last_name =			"$name[0] $name[2]";		chomp($first_name);
				$middle_initial =		'';
				$address1 =				$m[4];		chomp($address1);
				$address2 =				$m[5];		chomp($address1);
				$address3 =				'';
				$city =					$m[6];		chomp($city);
				$state =				$m[0];		chomp($state);
				$province =				'';
				$postal_code =			$m[7];		chomp($postal_code);
				$country =				'USA';
				$gender =				'U';
				$date_of_birth =		'0000-00-00';
				$alt_phone =			$m[3];		chomp($alt_phone);	$alt_phone =~ s/\D//gi;
				$email =				'';
				$security_phrase =		'';
				$comments =				'';
				$called_count =			0;
				$status =				'NEW';

				$format_set++;
				}

		# This is the format for the sccsv11 lead files
		# STATE,ID,NAME,ADDRESS,ADDRESS2,CITY,COUNTY,PHONE,FAX,ZIP,COMMENTS
		# AL,16700,Fairfax Retirement Center (Crowder House),1324 25th St,,FAIRFAX,JEFFERSON,(205) 555-0508,,35733,Comments

			if ( ($format =~ /sccsv11/) && ($format_set < 1) )
				{
				$raw_number =~ s/\'|\t|\r|\n|\l//gi;
				$raw_number =~ s/,/\|/gi;
				@m = split(/\|/, $raw_number);
				@name=@MT;
				$state =				$m[0];		chomp($state);
				$vendor_lead_code =		$m[1];		$vendor_lead_code =~ s/\D//gi;

				$field = 2;
				if ($m[$field] =~ /^\"/)
					{
					$full_name =		"$m[$field]";   $field++;
					$full_name .=		"$m[$field]";   $field++;
					}
				else
					{
					$full_name =		"$m[$field]";   $field++;
					}
				$full_name =~ s/\"|\)|\(|;//gi;
				@name = split(/ /, $full_name);
					$first_name =			$name[1];		chomp($first_name);
					$last_name =			"$name[0] $name[2]";		chomp($first_name);
					$middle_initial =		'';

				if ($m[$field] =~ /^\"/)
					{
					$address1 =		"$m[$field]";   $field++;
					$address1 .=		"$m[$field]";   $field++;
					}
				else
					{
					$address1 =		"$m[$field]";   $field++;
					}
				$address1 =~ s/\"|\)|\(|;//gi;

				if ($m[$field] =~ /^\"/)
					{
					$address2 =		"$m[$field]";   $field++;
					$address2 .=		"$m[$field]";   $field++;
					}
				else
					{
					$address2 =		"$m[$field]";   $field++;
					}
				$address2 =~ s/\"|\)|\(|;//gi;

				if ($m[$field] =~ /^\"/)
					{
					$city =		"$m[$field]";   $field++;
					$city .=		"$m[$field]";   $field++;
					}
				else
					{
					$city =		"$m[$field]";   $field++;
					}
				$city =~ s/\"|\)|\(|;//gi;

				if ($m[$field] =~ /^\"/)
					{
					$province =		"$m[$field]";   $field++;
					$province .=		"$m[$field]";   $field++;
					}
				else
					{
					$province =		"$m[$field]";   $field++;
					}
				$province =~ s/\"|\)|\(|;//gi;

				$phone_number =		"$m[$field]";   $field++;	$phone_number =~ s/\D//gi;
					$USarea = 				substr($phone_number, 0, 3);
				$alt_phone =		"$m[$field]";   $field++;	$alt_phone =~ s/\D//gi;
				$postal_code =		"$m[$field]";   $field++;

				if ($m[$field] =~ /^\"/)
					{
					$comments =		"$m[$field]";   $field++;
					$comments .=		"$m[$field]";   $field++;
					}
				else
					{
					$comments =		"$m[$field]";   $field++;
					}
				$comments =~ s/\"|\)|\(|;//gi;

				$source_id =			'';
				$list_id =				'995';
				$phone_code =			'1';
				$title =				'';
				$address3 =				'';
				$country =				'USA';
				$gender =				'U';
				$date_of_birth =		'0000-00-00';
				$email =				'';
				$security_phrase =		'';
				$called_count =			0;
				$status =				'NEW';

				$format_set++;
				}

		# This is the format for the stdrankowner lead files (standard plus rank and owner )
		#3857822|31022|105|01144|1625551212|MRS|B||BURTON|249 MUNDON ROAD|MALDON|ESSEX||||CM9 6PW|UK||||||COMMENTS|COUNT|STATUS|INS-DATE|RANK|OWNER
			if ( ($format =~ /stdrankowner/) && ($format_set < 1) )
				{
				$vendor_lead_code =		$m[0];		chomp($vendor_lead_code);
				$source_id =			$m[1];		chomp($source_id);
				$list_id =				$m[2];		chomp($list_id);
				$phone_code =			$m[3];		chomp($phone_code);	$phone_code =~ s/\D//gi;
				$phone_number =			$m[4];		chomp($phone_number);	$phone_number =~ s/\D//gi;
					$USarea = 			substr($phone_number, 0, 3);
				$title =				$m[5];		chomp($title);
				$first_name =			$m[6];		chomp($first_name);
				$middle_initial =		$m[7];		chomp($middle_initial);
				$last_name =			$m[8];		chomp($last_name);
				$address1 =				$m[9];		chomp($address1);
				$address2 =				$m[10];		chomp($address2);
				$address3 =				$m[11];		chomp($address3);
				$city =					$m[12];		chomp($city);
				$state =				$m[13];		chomp($state);
				$province =				$m[14];		chomp($province);
				$postal_code =			$m[15];		chomp($postal_code);
				$country =				$m[16];		chomp($country);
				$gender =				$m[17];
				$date_of_birth =		$m[18];
				$alt_phone =			$m[19];		chomp($alt_phone);	$alt_phone =~ s/\D//gi;
				$email =				$m[20];
				$security_phrase =		$m[21];
				$comments =				$m[22];
				$called_count =			$m[23];		$called_count =~ s/\D|\r|\n|\t//gi; if (length($called_count)<1) {$called_count=0;}
				$status =				$m[24];		$status =~ s/ |\r|\n|\t//gi;  if (length($status)<1) {$status='NEW';}
				$insert_date =			$m[25];	$insert_date =~ s/\r|\n|\t|[a-zA-Z]//gi;  if (length($insert_date)<6) {$insert_date=$pulldate0;}
					if ($insert_date =~ /\//) 
						{
						@iD = split(/\//, $insert_date);
						$iD[0] = sprintf("%02d", $iD[0]);
						$iD[1] = sprintf("%02d", $iD[1]);
						$insert_date = "$iD[2]-$iD[0]-$iD[1]";
						}
				$rank =					$m[26];
				$owner =				$m[27];
				$multi_alt_phones =		$m[28];

				$map_count=0;
				if (length($multi_alt_phones)>4)
					{
					@map=@MT;  @ALTm_phone_code=@MT;  @ALTm_phone_number=@MT;  @ALTm_phone_note=@MT;
					@map = split(/\!/, $multi_alt_phones);
					$map_count = ($#map +1);
					if ($DBX) {print "multi-al-entry: $a|$map_count|$multi_alt_phones\n";}
					$g++;
					$r=0;
					while ($r < $map_count)
						{
						@ncn=@MT;
						@ncn = split(/\_/, $map[$r]);
						print "$ncn[0]|$ncn[1]|$ncn[2]";

						if (length($forcephonecode) > 0)
							{$ALTm_phone_code[$r] =	$forcephonecode;}
						else
							{$ALTm_phone_code[$r] =		$ncn[1];}
						if (length($ALTm_phone_code[$r]) < 1)
							{$ALTm_phone_code[$r]='1';}
						$ALTm_phone_number[$r] =	$ncn[0];
						$ALTm_phone_note[$r] =		$ncn[2];
						$r++;
						}
					}

				$format_set++;
				}

		# This is the format for the dccsv10 lead files
		#"100998","ANGELA    ","SMITH     ","3145551212","3145551213","3145551214","0","3145551215","3145551216","0",
			if ( ($format =~ /dccsv10/) && ($format_set < 1) )
				{
				$raw_number = $number;
				chomp($number);
				$number =~ s/,"0"//gi;
				$number =~ s/\t/\|/gi;
				$number =~ s/\'|\t|\r|\n|\l//gi;
				$number =~ s/\'|\t|\r|\n|\l//gi;
				$number =~ s/\",,,,,,,\"/\|\|\|\|\|\|\|/gi;
				$number =~ s/\",,,,,,\"/\|\|\|\|\|\|/gi;
				$number =~ s/\",,,,,\"/\|\|\|\|\|/gi;
				$number =~ s/\",,,,\"/\|\|\|\|/gi;
				$number =~ s/\",,,\"/\|\|\|/gi;
				$number =~ s/\",,\"/\|\|/gi;
				$number =~ s/\",\"/\|/gi;
				$number =~ s/\"//gi;
				@m = split(/\|/, $number);

				$vendor_lead_code =		$m[0];		chomp($vendor_lead_code);
				$source_id =			'';
				$list_id =				'929';
				$phone_code =			'1';
				$first_name =			$m[1];		chomp($first_name);		$first_name =~ s/\s+$//gi;
				$middle_initial =		'';
				$last_name =			$m[2];		chomp($last_name);		$last_name =~ s/\s+$//gi;
				$phone_number =			$m[3];
					$USarea = 			substr($phone_number, 0, 3);
				$title =				'';
				$address1 =				'';
				$address2 =				'';
				$address3 =				$m[5];		chomp($alt_phone);	$alt_phone =~ s/\D//gi;
				$city =					'';
				$state =				'';
				$province =				'';
				$postal_code =			'';
				$country =				'';
				$gender =				'';
				$date_of_birth =		'';
				$alt_phone =			$m[4];		chomp($alt_phone);	$alt_phone =~ s/\D//gi;
				$email =				'';
				$security_phrase =		'';
				$comments =				'';
				$called_count =			'0';
				$status =				'NEW';
				$insert_date =			$pulldate0;
				$rank =					'';
				$owner =				'';
				$multi_alt_phones =		'';

				$r=0;
				$map_count=0;
				if (length($m[6]) > 9) 
					{
					$ALTm_phone_number[$r] =	$m[6];
					$ALTm_phone_code[$r] =		'1';
					$r++;	$map_count++;
					$g++;
					}
				if (length($m[7]) > 9) 
					{
					$ALTm_phone_number[$r] =	$m[7];
					$ALTm_phone_code[$r] =		'1';
					$r++;	$map_count++;
					}
				if (length($m[8]) > 9) 
					{
					$ALTm_phone_number[$r] =	$m[8];
					$ALTm_phone_code[$r] =		'1';
					$r++;	$map_count++;
					}
				if (length($m[9]) > 9) 
					{
					$ALTm_phone_number[$r] =	$m[9];
					$ALTm_phone_code[$r] =		'1';
					$r++;	$map_count++;
					}

				$format_set++;
				}

		# This is the format for the dccsv43 lead files
#"BF_ID","RECORD_TYPE","LAST_NAME","FIRST_NAME","4ADDR1","5ADDR2","6CITY","STATE","ZIP","9ZIP4","ADDR_STATUS","DATE_PLACED","DATE_ADDED","DOB","LAST_LETTER","LAST_LETTER_DATE","LAST_WORKED","NEXT_ACTION_DATE","CAPTURE_CODE","CUR_CATEGORY","TIMES_DIALED","LAST_DIALED","TOTAL_PAID","DATE_LAST_PAID","NMBR_CALLS","NMBR_CONTACTS","NMBR_TIMES_WRKD","NMBR_LETTERS","STATUS_CODE","STATUS_DATE","SCORE","TIMES_TO_SERVICER","1ST-PMT-DEFAULT","TIME_ZONE","ORIG_CREDITOR","BALANCE","HOME_PHONE","WORK_PHONE","OTHER_PHONE","ACCT_OTHTEL2","ACCT_OTHTEL3","ACCT_OTHTEL4","ACCT_OTHTEL5"
#"II ACCT/1103566666  ","P","SMITH           ","        SAMMY","7838 W 109TH ST APT 12        ","                              ","OVERLAND PARK       ","KS","66212","0000","G","20091110","20091110","19661216","NOLTTR","00000000","20091214","20091219","1000","03","000","00000000","000000000.00 ","00000000","0004","0003","0004","000","ACTIVE","20091110","0648","00"," ","C","HSBC                          ","000000692.09 ","9135551212","0000000000","0000000000","0000000000","0000000000","0000000000","0000000000"

			if ( ($format =~ /dccsv43/) && ($format_set < 1) )
				{
				$raw_number = $number;
				chomp($number);
				$number =~ s/,\"0\"/,/gi;
				$number =~ s/\t/\|/gi;
				$number =~ s/\'|\t|\r|\n|\l//gi;
				$number =~ s/\",,,,,,,\"/\|\|\|\|\|\|\|/gi;
				$number =~ s/\",,,,,,\"/\|\|\|\|\|\|/gi;
				$number =~ s/\",,,,,\"/\|\|\|\|\|/gi;
				$number =~ s/\",,,,\"/\|\|\|\|/gi;
				$number =~ s/\",,,\"/\|\|\|/gi;
				$number =~ s/\",,\"/\|\|/gi;
				$number =~ s/\",\"/\|/gi;
				$number =~ s/\"//gi;
				$number =~ s/\|0000000000//gi;
				$number =~ s/          //gi;
				@m=@MT;
				@m = split(/\|/, $number);
				if ($DBX) {print "RAW: $#m-----$number\n";}

				$vendor_lead_code =		$m[0];		chomp($vendor_lead_code);		$vendor_lead_code =~ s/\s+$//gi;
					$vendor_lead_code =~s/II ACCT\///gi;
					$vendor_lead_code =~s/WDRF  //gi;
					while (length($vendor_lead_code) > 10) {chop($vendor_lead_code);}
				$source_id =			$m[0];		chomp($source_id);		$source_id =~ s/\s+$//gi;
				$list_id =				'929';
				$phone_code =			'1';
				$first_name =			$m[3];		chomp($first_name);		$first_name =~ s/^\s+|\s+$//gi;
				$middle_initial =		'';
				$last_name =			$m[2];		chomp($last_name);		$last_name =~ s/\s+$//gi;
				$phone_number =			$m[36];			$phone_number =~ s/\D//gi;
					$USarea = 			substr($phone_number, 0, 3);
				$title =				$m[25];			# number of contacts
				$address1 =				$m[4];					$address1 =~ s/\s+$//gi;
				$address2 =				$m[5];					$address2 =~ s/\s+$//gi;
				$address3 =				$m[34];			# orig creditor
				$city =					$m[6];					$city =~ s/\s+$//gi;
				$state =				$m[7];
				$province =				$m[35];			$province =~ s/\s+$//gi;   # balance
				$postal_code =			$m[8];
				$country =				'';
				$gender =				'';
				$date_of_birth =		$m[13];
					$dobYYYY = substr($date_of_birth, 0, 4);
					$dobMM = substr($date_of_birth, 4, 2);
					$dobDD = substr($date_of_birth, 6, 2);
					$date_of_birth = "$dobYYYY-$dobMM-$dobDD";
				$alt_phone =			$m[37];		chomp($alt_phone);	$alt_phone =~ s/\D//gi;
				$email =				$m[11];			# date placed
				$security_phrase =		''; # looked-up geographic CID will go here
				$comments =				"$m[16]|$m[21]";	# last worked/dialed
				$called_count =			'0';
				$status =				'NEW';
				$insert_date =			$pulldate0;
				$rank =					$m[26];			# number of times worked
				$owner =				$m[28];			# old status code
				$multi_alt_phones =		'';

				$r=0;
				$map_count=0;
				if ( (length($m[38]) > 9) && ($m[38] !~ /000000000/) )
					{
					$ALTm_phone_number[$r] =	$m[38];
					$ALTm_phone_code[$r] =		'1';
					$r++;	$map_count++;
					$g++;
					}
				if ( (length($m[39]) > 9) && ($m[39] !~ /000000000/) )
					{
					$ALTm_phone_number[$r] =	$m[39];
					$ALTm_phone_code[$r] =		'1';
					$r++;	$map_count++;
					}
				if ( (length($m[40]) > 9) && ($m[40] !~ /000000000/) )
					{
					$ALTm_phone_number[$r] =	$m[40];
					$ALTm_phone_code[$r] =		'1';
					$r++;	$map_count++;
					}
				if ( (length($m[41]) > 9) && ($m[41] !~ /000000000/) )
					{
					$ALTm_phone_number[$r] =	$m[41];
					$ALTm_phone_code[$r] =		'1';
					$r++;	$map_count++;
					}
				if ( (length($m[42]) > 9) && ($m[42] !~ /000000000/) )
					{
					$ALTm_phone_number[$r] =	$m[42];
					$ALTm_phone_code[$r] =		'1';
					$r++;	$map_count++;
					}

				### look up the custom CID to use for this state
				$stmtA = "select cid from vicidial_custom_cid where state='$state';";
				$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
				$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
				$sthArows=$sthA->rows;
					if($DBX){print STDERR "\n$sthArows|$stmtA|\n";}
				if ($sthArows > 0)
					{
					@aryA = $sthA->fetchrow_array;
					$security_phrase = $aryA[0];
					}
				$sthA->finish();

				$format_set++;
				}

		# This is the format for the dccsvref51 lead files
#"BF_ID","RECORD_TYPE","LAST_NAME","FIRST_NAME","4ADDR1","5ADDR2","6CITY","STATE","ZIP","9ZIP4","ADDR_STATUS","DATE_PLACED","DATE_ADDED","DOB","LAST_LETTER","LAST_LETTER_DATE","LAST_WORKED","NEXT_ACTION_DATE","CAPTURE_CODE","CUR_CATEGORY","TIMES_DIALED","LAST_DIALED","TOTAL_PAID","DATE_LAST_PAID","NMBR_CALLS","NMBR_CONTACTS","NMBR_TIMES_WRKD","NMBR_LETTERS","STATUS_CODE","STATUS_DATE","SCORE","TIMES_TO_SERVICER","1ST-PMT-DEFAULT","TIME_ZONE","ORIG_CREDITOR","BALANCE","HOME_PHONE","WORK_PHONE","OTHER_PHONE","ACCT_OTHTEL2","ACCT_OTHTEL3","ACCT_OTHTEL4","ACCT_OTHTEL5"
#"II ACCT/1103566666  ","P","SMITH           ","        SAMMY","7838 W 109TH ST APT 12        ","                              ","OVERLAND PARK       ","KS","66212","0000","G","20091110","20091110","19661216","NOLTTR","00000000","20091214","20091219","1000","03","000","00000000","000000000.00 ","00000000","0004","0003","0004","000","ACTIVE","20091110","0648","00"," ","C","HSBC                          ","000000692.09 ","9135551212","0000000000","0000000000","0000000000","0000000000","0000000000","0000000000"

			if ( ($format =~ /dccsvref51/) && ($format_set < 1) )
				{
				$raw_number = $number;
				chomp($number);
				$number =~ s/,\"0\"/,/gi;
				$number =~ s/\t/\|/gi;
				$number =~ s/\'|\t|\r|\n|\l//gi;
				$number =~ s/\",,,,,,,\"/\|\|\|\|\|\|\|/gi;
				$number =~ s/\",,,,,,\"/\|\|\|\|\|\|/gi;
				$number =~ s/\",,,,,\"/\|\|\|\|\|/gi;
				$number =~ s/\",,,,\"/\|\|\|\|/gi;
				$number =~ s/\",,,\"/\|\|\|/gi;
				$number =~ s/\",,\"/\|\|/gi;
				$number =~ s/\",\"/\|/gi;
				$number =~ s/\"//gi;
			#	$number =~ s/\|0000000000//gi;
				@m=@MT;
				@m = split(/\|/, $number);
				if ($DBX) {print "RAW: $#m-----$number\n";}

				$vendor_lead_code =		$m[0];		chomp($vendor_lead_code);		$vendor_lead_code =~ s/\s+$//gi;
					$vendor_lead_code =~s/II ACCT\///gi;
					$vendor_lead_code =~s/WDRF  //gi;
					while (length($vendor_lead_code) > 10) {chop($vendor_lead_code);}
				$source_id =			$m[0];		chomp($source_id);		$source_id =~ s/\s+$//gi;
				$list_id =				'929';
				$phone_code =			'1';
				$first_name =			$m[3];		chomp($first_name);		$first_name =~ s/^\s+|\s+$//gi;
				$middle_initial =		'';
				$last_name =			$m[2];		chomp($last_name);		$last_name =~ s/\s+$//gi;
				$phone_number =			$m[36];			$phone_number =~ s/\D//gi;
					$USarea = 			substr($phone_number, 0, 3);
				$title =				$m[25];			# number of contacts
				$address1 =				$m[4];					$address1 =~ s/\s+$//gi;
				$address2 =				$m[5];					$address2 =~ s/\s+$//gi;
				$address3 =				$m[34];			# orig creditor
				$city =					$m[6];					$city =~ s/\s+$//gi;
				$state =				$m[7];
				$province =				$m[35];			$province =~ s/\s+$//gi;   # balance
				$postal_code =			$m[8];
				$country =				'';
				$gender =				'';
				$date_of_birth =		$m[13];
					$dobYYYY = substr($date_of_birth, 0, 4);
					$dobMM = substr($date_of_birth, 4, 2);
					$dobDD = substr($date_of_birth, 6, 2);
					$date_of_birth = "$dobYYYY-$dobMM-$dobDD";
				$alt_phone =			$m[37];		chomp($alt_phone);	$alt_phone =~ s/\D//gi;
				$email =				$m[11];			# date placed
				$security_phrase =		''; # looked-up geographic CID will go here
				$comments =				$m[43];			# ref-name
				$called_count =			'0';
				$status =				'NEW';
				$insert_date =			$pulldate0;
				$rank =					$m[26];			# number of times worked
				$owner =				$m[28];			# old status code
				$multi_alt_phones =		'';

				$r=0;
				$map_count=0;
				if ( (length($m[38]) > 9) && ($m[38] !~ /000000000/) )
					{
					$ALTm_phone_number[$r] =	$m[38];
					$ALTm_phone_code[$r] =		'1';
					$r++;	$map_count++;
					$g++;
					}
				if ( (length($m[39]) > 9) && ($m[39] !~ /000000000/) )
					{
					$ALTm_phone_number[$r] =	$m[39];
					$ALTm_phone_code[$r] =		'1';
					$r++;	$map_count++;
					}
				if ( (length($m[40]) > 9) && ($m[40] !~ /000000000/) )
					{
					$ALTm_phone_number[$r] =	$m[40];
					$ALTm_phone_code[$r] =		'1';
					$r++;	$map_count++;
					}
				if ( (length($m[41]) > 9) && ($m[41] !~ /000000000/) )
					{
					$ALTm_phone_number[$r] =	$m[41];
					$ALTm_phone_code[$r] =		'1';
					$r++;	$map_count++;
					}
				if ( (length($m[42]) > 9) && ($m[42] !~ /000000000/) )
					{
					$ALTm_phone_number[$r] =	$m[42];
					$ALTm_phone_code[$r] =		'1';
					$r++;	$map_count++;
					}

				### look up the custom CID to use for this state
				$stmtA = "select cid from vicidial_custom_cid where state='$state';";
				$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
				$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
				$sthArows=$sthA->rows;
					if($DBX){print STDERR "\n$sthArows|$stmtA|\n";}
				if ($sthArows > 0)
					{
					@aryA = $sthA->fetchrow_array;
					$security_phrase = $aryA[0];
					}
				$sthA->finish();

				$format_set++;
				}
		# This is the format for the dccsv52 lead files
#"BFRAME","RECORD_TYPE","LAST_NAME","FIRST_NAME","ADDR1","ADDR2","CITY","STATE","ZIP","ZIP4","ADDR_STATUS","DATE_PLACED","DATE_ADDED","DOB","LAST_LETTER","LAST_LETTER_DATE","LAST_WORKED","NEXT_ACTION_DATE","CAPTURE_CODE","CUR_CATEGORY","TIMES_DIALED","LAST_DIALED","TOTAL_PAID","DATE_LAST_PAID","NMBR_CALLS","NMBR_CONTACTS","NMBR_TIMES_WRKD","NMBR_LETTERS","STATUS_CODE","STATUS_DATE","SCORE","TIMES_TO_SERVICER","1ST-PMT-DEFAULT","TIME_ZONE","ORIG_CREDITOR","BALANCE","HOME_PHONE","WORK_PHONE","OTHER_PHONE","ACCT_OTHTEL2","ACCT_OTHTEL3","ACCT_OTHTEL4","ACCT_OTHTEL5","REF-NAME","REF-AD1","REF-AD2","REF-CITY","REF-ST","REF-POSTAL","REF-TEL1","REF-TEL2","SSN"
#"II ACCT/1103566666  ","P","SMITH           ","        SAMMY","7838 W 109TH ST APT 12        ","                              ","OVERLAND PARK       ","KS","66212","0000","G","20091110","20091110","19661216","NOLTTR","00000000","20091214","20091219","1000","03","000","00000000","000000000.00 ","00000000","0004","0003","0004","000","ACTIVE","20091110","0648","00"," ","C","HSBC                          ","000000692.09 ","9135551212","0000000000","0000000000","0000000000","0000000000","0000000000","0000000000","","","","","  ","          ","          ","          ","578888888"

			if ( ($format =~ /dccsv52/) && ($format_set < 1) )
				{
				$raw_number = $number;
				chomp($number);
				$number =~ s/,\"0\"/,/gi;
				$number =~ s/\t/\|/gi;
				$number =~ s/\'|\t|\r|\n|\l//gi;
				$number =~ s/\",,,,,,,\"/\|\|\|\|\|\|\|/gi;
				$number =~ s/\",,,,,,\"/\|\|\|\|\|\|/gi;
				$number =~ s/\",,,,,\"/\|\|\|\|\|/gi;
				$number =~ s/\",,,,\"/\|\|\|\|/gi;
				$number =~ s/\",,,\"/\|\|\|/gi;
				$number =~ s/\",,\"/\|\|/gi;
				$number =~ s/\",\"/\|/gi;
				$number =~ s/\"//gi;
			#	$number =~ s/\|0000000000//gi;
				@m=@MT;
				@m = split(/\|/, $number);
				if ($DBX) {print "RAW: $#m-----$number\n";}

				$vendor_lead_code =		$m[0];		chomp($vendor_lead_code);		$vendor_lead_code =~ s/\s+$//gi;
					$vendor_lead_code =~s/II ACCT\///gi;
					$vendor_lead_code =~s/WDRF  //gi;
					while (length($vendor_lead_code) > 10) {chop($vendor_lead_code);}
				$source_id =			$m[0];		chomp($source_id);		$source_id =~ s/\s+$//gi;
				$list_id =				'929';
				$phone_code =			'1';
				$first_name =			$m[3];		chomp($first_name);		$first_name =~ s/^\s+|\s+$//gi;
				$middle_initial =		'';
				$last_name =			$m[2];		chomp($last_name);		$last_name =~ s/\s+$//gi;
				$title =				$m[25];			# number of contacts
				$address1 =				$m[4];					$address1 =~ s/\s+$//gi;
				$address2 =				$m[5];					$address2 =~ s/\s+$//gi;
				$address3 =				$m[34];			# orig creditor
				$city =					$m[6];					$city =~ s/\s+$//gi;
				$state =				$m[7];
				$province =				$m[35];			$province =~ s/\s+$//gi;   # balance
				$postal_code =			$m[8];
				$country =				'';
				$gender =				'';
				$date_of_birth =		$m[13];
					$dobYYYY = substr($date_of_birth, 0, 4);
					$dobMM = substr($date_of_birth, 4, 2);
					$dobDD = substr($date_of_birth, 6, 2);
					$date_of_birth = "$dobYYYY-$dobMM-$dobDD";
				$email =				$m[51];			# ssn
				$security_phrase =		''; # looked-up geographic CID will go here
				$comments =				$m[43];			# ref-name
				$called_count =			'0';
				$status =				'NEW';
				$insert_date =			$pulldate0;
				$rank =					$m[26];			# number of times worked
				$owner =				$m[28];			# old status code
				$multi_alt_phones =		'';

				$number =~ s/\|0000000000//gi;
				@m=@MT;
				@m = split(/\|/, $number);
				$m[36] =~ s/\D//gi; $m[37] =~ s/\D//gi; $m[38] =~ s/\D//gi; $m[39] =~ s/\D//gi; $m[40] =~ s/\D//gi; $m[41] =~ s/\D//gi; $m[42] =~ s/\D//gi;

				$phone_number =			$m[36];			$phone_number =~ s/\D//gi;
					$USarea = 			substr($phone_number, 0, 3);
				$alt_phone='';
				if ( (length($m[37]) > 9) && ($m[37] !~ /000000000/) )
					{
					$alt_phone =			$m[37];		chomp($alt_phone);
					}

				$r=0;
				$map_count=0;
				if ( (length($m[38]) > 9) && ($m[38] !~ /000000000/) )
					{
					$ALTm_phone_number[$r] =	$m[38];
					$ALTm_phone_code[$r] =		'1';
					$r++;	$map_count++;
					$g++;
					}
				if ( (length($m[39]) > 9) && ($m[39] !~ /000000000/) )
					{
					$ALTm_phone_number[$r] =	$m[39];
					$ALTm_phone_code[$r] =		'1';
					$r++;	$map_count++;
					}
				if ( (length($m[40]) > 9) && ($m[40] !~ /000000000/) )
					{
					$ALTm_phone_number[$r] =	$m[40];
					$ALTm_phone_code[$r] =		'1';
					$r++;	$map_count++;
					}
				if ( (length($m[41]) > 9) && ($m[41] !~ /000000000/) )
					{
					$ALTm_phone_number[$r] =	$m[41];
					$ALTm_phone_code[$r] =		'1';
					$r++;	$map_count++;
					}
				if ( (length($m[42]) > 9) && ($m[42] !~ /000000000/) )
					{
					$ALTm_phone_number[$r] =	$m[42];
					$ALTm_phone_code[$r] =		'1';
					$r++;	$map_count++;
					}

				### look up the custom CID to use for this state
				$stmtA = "select cid from vicidial_custom_cid where state='$state';";
				$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
				$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
				$sthArows=$sthA->rows;
					if($DBX){print STDERR "\n$sthArows|$stmtA|\n";}
				if ($sthArows > 0)
					{
					@aryA = $sthA->fetchrow_array;
					$security_phrase = $aryA[0];
					}
				$sthA->finish();

				$format_set++;
				}



		# This is the format for the dccsvref52 lead files
#"BFRAME","RECORD_TYPE","LAST_NAME","FIRST_NAME","ADDR1","ADDR2","CITY","STATE","ZIP","ZIP4","ADDR_STATUS","DATE_PLACED","DATE_ADDED","DOB","LAST_LETTER","LAST_LETTER_DATE","LAST_WORKED","NEXT_ACTION_DATE","CAPTURE_CODE","CUR_CATEGORY","TIMES_DIALED","LAST_DIALED","TOTAL_PAID","DATE_LAST_PAID","NMBR_CALLS","NMBR_CONTACTS","NMBR_TIMES_WRKD","NMBR_LETTERS","STATUS_CODE","STATUS_DATE","SCORE","TIMES_TO_SERVICER","1ST-PMT-DEFAULT","TIME_ZONE","ORIG_CREDITOR","BALANCE","HOME_PHONE","WORK_PHONE","OTHER_PHONE","ACCT_OTHTEL2","ACCT_OTHTEL3","ACCT_OTHTEL4","ACCT_OTHTEL5","REF-NAME","REF-AD1","REF-AD2","REF-CITY","REF-ST","REF-POSTAL","REF-TEL1","REF-TEL2","SSN"
#"II ACCT/1103566666  ","P","SMITH           ","        SAMMY","7838 W 109TH ST APT 12        ","                              ","OVERLAND PARK       ","KS","66212","0000","G","20091110","20091110","19661216","NOLTTR","00000000","20091214","20091219","1000","03","000","00000000","000000000.00 ","00000000","0004","0003","0004","000","ACTIVE","20091110","0648","00"," ","C","HSBC                          ","000000692.09 ","9135551212","0000000000","0000000000","0000000000","0000000000","0000000000","0000000000","","","","","  ","          ","          ","          ","578888888"

			if ( ($format =~ /dccsvref52/) && ($format_set < 1) )
				{
				$raw_number = $number;
				chomp($number);
				$number =~ s/,\"0\"/,/gi;
				$number =~ s/\t/\|/gi;
				$number =~ s/\'|\t|\r|\n|\l//gi;
				$number =~ s/\",,,,,,,\"/\|\|\|\|\|\|\|/gi;
				$number =~ s/\",,,,,,\"/\|\|\|\|\|\|/gi;
				$number =~ s/\",,,,,\"/\|\|\|\|\|/gi;
				$number =~ s/\",,,,\"/\|\|\|\|/gi;
				$number =~ s/\",,,\"/\|\|\|/gi;
				$number =~ s/\",,\"/\|\|/gi;
				$number =~ s/\",\"/\|/gi;
				$number =~ s/\"//gi;
			#	$number =~ s/\|0000000000//gi;
				@m=@MT;
				@m = split(/\|/, $number);
				if ($DBX) {print "RAW: $#m-----$number\n";}

				$vendor_lead_code =		$m[0];		chomp($vendor_lead_code);		$vendor_lead_code =~ s/\s+$//gi;
					$vendor_lead_code =~s/II ACCT\///gi;
					$vendor_lead_code =~s/WDRF  //gi;
					while (length($vendor_lead_code) > 10) {chop($vendor_lead_code);}
				$source_id =			$m[0];		chomp($source_id);		$source_id =~ s/\s+$//gi;
				$list_id =				'929';
				$phone_code =			'1';
				$first_name =			$m[3];		chomp($first_name);		$first_name =~ s/^\s+|\s+$//gi;
				$middle_initial =		'';
				$last_name =			$m[2];		chomp($last_name);		$last_name =~ s/\s+$//gi;
				$title =				$m[25];			# number of contacts
				$address1 =				$m[4];					$address1 =~ s/\s+$//gi;
				$address2 =				$m[5];					$address2 =~ s/\s+$//gi;
				$address3 =				$m[34];			# orig creditor
				$city =					$m[6];					$city =~ s/\s+$//gi;
				$state =				$m[7];
				$province =				$m[35];			$province =~ s/\s+$//gi;   # balance
				$postal_code =			$m[8];
				$country =				'';
				$gender =				'';
				$date_of_birth =		$m[13];
					$dobYYYY = substr($date_of_birth, 0, 4);
					$dobMM = substr($date_of_birth, 4, 2);
					$dobDD = substr($date_of_birth, 6, 2);
					$date_of_birth = "$dobYYYY-$dobMM-$dobDD";
				$email =				$m[51];			# ssn
				$security_phrase =		''; # looked-up geographic CID will go here
				$comments =				$m[43];			# ref-name
				$called_count =			'0';
				$status =				'NEW';
				$insert_date =			$pulldate0;
				$rank =					$m[26];			# number of times worked
				$owner =				$m[28];			# old status code
				$multi_alt_phones =		'';

				$phone_number =			$m[49];			$phone_number =~ s/\D//gi;
					$USarea = 			substr($phone_number, 0, 3);
				$alt_phone='';
				if ( (length($m[50]) > 9) && ($m[50] !~ /000000000/) )
					{
					$alt_phone =			$m[37];		chomp($alt_phone);
					}

				### look up the custom CID to use for this state
				$stmtA = "select cid from vicidial_custom_cid where state='$state';";
				$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
				$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
				$sthArows=$sthA->rows;
					if($DBX){print STDERR "\n$sthArows|$stmtA|\n";}
				if ($sthArows > 0)
					{
					@aryA = $sthA->fetchrow_array;
					$security_phrase = $aryA[0];
					}
				$sthA->finish();

				$format_set++;
				}

			# This is the format for the pipe30tz lead files
			# TEST01||09292011|1|5125554727||Mike||Frank|||||||||||||||||C|2145559922|TESTSURVEY|TESTSURVEY|111
			if ( ($format =~ /pipe30tz/) && ($format_set < 1) )
				{
				$source_id =			$m[0];		chomp($source_id);
				$vendor_lead_code =		$m[2];		chomp($vendor_lead_code);
				$phone_code =			$m[3];		chomp($phone_code);	$phone_code =~ s/\D//gi;
				$phone_number =			$m[4];		chomp($phone_number);	$phone_number =~ s/\D//gi;
					$USarea = 			substr($phone_number, 0, 3);
				$title =				$m[5];		chomp($title);
				$first_name =			$m[6];		chomp($first_name);
				$middle_initial =		$m[7];		chomp($middle_initial);
				$last_name =			$m[8];		chomp($last_name);
		#		$address1 =				$m[9];		chomp($address1);
		#		$address2 =				$m[10];		chomp($address2);
		#		$address3 =				$m[11];		chomp($address3);
		#		$city =					$m[12];		chomp($city);
		#		$state =				$m[13];		chomp($state);
		#		$province =				$m[14];		chomp($province);
		#		$postal_code =			$m[15];		chomp($postal_code);
		#		$country =				$m[16];		chomp($country);
		#		$gender =				$m[17];
		#		$date_of_birth =		$m[18];
		#		$alt_phone =			$m[19];		chomp($alt_phone);	$alt_phone =~ s/\D//gi;
		#		$email =				$m[20];
		#		$security_phrase =		$m[21];
		#		$comments =				$m[22];
				$owner =				$m[25];
					if ($owner =~ /E/) {$owner='EST';}
					if ($owner =~ /C/) {$owner='CST';}
					if ($owner =~ /M/) {$owner='MST';}
					if ($owner =~ /P/) {$owner='PST';}
				$security_phrase =		$m[26];
				$province =				$m[27];
				$address3 =				$m[28];
				$alt_phone =			$m[29];

				$list_id=$new_list_id;
				$called_count=0;
				$status='NEW';
				$insert_date=$pulldate0;
				$map_count=0;

				$format_set++;
				}


		# This is the format for the standard lead files
		#3857822|31022|105|01144|1625551212|MRS|B||BURTON|249 MUNDON ROAD|MALDON|ESSEX||||CM9 6PW|UK||||||COMMENTS
			if ($format_set < 1)
				{
				$vendor_lead_code =		$m[0];		chomp($vendor_lead_code);
				$source_id =			$m[1];		chomp($source_id);
				$list_id =				$m[2];		chomp($list_id);
				$phone_code =			$m[3];		chomp($phone_code);	$phone_code =~ s/\D//gi;
				$phone_number =			$m[4];		chomp($phone_number);	$phone_number =~ s/\D//gi;
					$USarea = 			substr($phone_number, 0, 3);
				$title =				$m[5];		chomp($title);
				$first_name =			$m[6];		chomp($first_name);
				$middle_initial =		$m[7];		chomp($middle_initial);
				$last_name =			$m[8];		chomp($last_name);
				$address1 =				$m[9];		chomp($address1);
				$address2 =				$m[10];		chomp($address2);
				$address3 =				$m[11];		chomp($address3);
				$city =					$m[12];		chomp($city);
				$state =				$m[13];		chomp($state);
				$province =				$m[14];		chomp($province);
				$postal_code =			$m[15];		chomp($postal_code);
				$country =				$m[16];		chomp($country);
				$gender =				$m[17];
				$date_of_birth =		$m[18];
				$alt_phone =			$m[19];		chomp($alt_phone);	$alt_phone =~ s/\D//gi;
				$email =				$m[20];
				$security_phrase =		$m[21];
				$comments =				$m[22];
				$called_count =			$m[23];		$called_count =~ s/\D|\r|\n|\t//gi; if (length($called_count)<1) {$called_count=0;}
				$status =				$m[24];		$status =~ s/ |\r|\n|\t//gi;  if (length($status)<1) {$status='NEW';}
				$insert_date =			$m[25];	$insert_date =~ s/\r|\n|\t|[a-zA-Z]//gi;  if (length($insert_date)<6) {$insert_date=$pulldate0;}
					if ($insert_date =~ /\//) 
						{
						@iD = split(/\//, $insert_date);
						$iD[0] = sprintf("%02d", $iD[0]);
						$iD[1] = sprintf("%02d", $iD[1]);
						$insert_date = "$iD[2]-$iD[0]-$iD[1]";
						}
				$multi_alt_phones =		$m[26];

				$map_count=0;
				if (length($multi_alt_phones)>4)
					{
					@map=@MT;  @ALTm_phone_code=@MT;  @ALTm_phone_number=@MT;  @ALTm_phone_note=@MT;
					@map = split(/\!/, $multi_alt_phones);
					$map_count = ($#map +1);
					if ($DBX) {print "multi-alt-entry: $a|$map_count|$multi_alt_phones\n";}
					$g++;
					$r=0;
					while ($r < $map_count)
						{
						@ncn=@MT;
						@ncn = split(/\_/, $map[$r]);
						print "$ncn[0]|$ncn[1]|$ncn[2]";

						if (length($forcephonecode) > 0)
							{$ALTm_phone_code[$r] =	$forcephonecode;}
						else
							{$ALTm_phone_code[$r] =		$ncn[1];}
						if (length($ALTm_phone_code[$r]) < 1)
							{$ALTm_phone_code[$r]='1';}
						$ALTm_phone_number[$r] =	$ncn[0];
						$ALTm_phone_note[$r] =		$ncn[2];
						$r++;
						}
					}
				}

			if (length($rank)<1) {$rank='0';}

			if (length($forcelistfilename_listid) > 0)
				{
				$list_id =	$forcelistfilename_listid;		# set list_id to filename override value
				}
			if (length($forcelistid) > 0)
				{
				$list_id =	$forcelistid;		# set list_id to override value
				}
			if (length($forcephonecode) > 0)
				{
				$phone_code =	$forcephonecode;	# set phone_code to override value
				}

			if ($DBX) {print "$a|$phone_number\n";}


			$valid_lead=1;
			##### Check for valid USA and Canada prefix #####
			if ($usacan_prefix_check > 0)
				{
				$USprefix = 			substr($phone_number, 3, 1);
				if ($USprefix < 2)
					{
					$valid_lead = '0';
					if ($DBX) {print "     Invalid USACAN prefix 4th digit: |$USprefix|$phone_number|\n";}
					}
				}

			##### Check for valid USA and Canada areacodes #####
			if ( ($usacan_areacode_check > 0) && ($valid_lead > 0) )
				{
				$USarea = 			substr($phone_number, 0, 3);
				$stmtA = "select count(*) from vicidial_phone_codes where areacode='$USarea' and country_code='1';";
					if($DBX){print STDERR "\n|$stmtA|\n";}
				$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
				$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
				$sthArows=$sthA->rows;
				if ($sthArows > 0)
					{
					@aryA = $sthA->fetchrow_array;
					$valid_lead = $aryA[0];
					if ($valid_lead < 1)
						{
						if ($DBX) {print "     Invalid USACAN areacode: |$USprefix|$phone_number|\n";}
						}
					}
				$sthA->finish();
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

			if ( (length($phone_number)>6) && ($dup_lead < 1) && ($valid_lead > 0) )
				{
				if ( ($duptapchecklist > 0) || ($duptapchecksys > 0) )
					{$phone_list .= "$alt_phone$title$US$list_id|";}
				else
					{$phone_list .= "$phone_number$US$list_id|";}
				# set default values
				$modify_date =			"";
				$user =					"";
				$called_since_last_reset='N';
				$gmt_offset =			'0';

				if ($forcegmt > 0)
					{
					$gmt_offset =	$m[23];		# set GMT offset value to 24th field value
					}
				else
					{
					$postalgmt_found=0;
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
					if ( ($tzcodegmt > 0) && (length($owner)>1) )
						{
						$dst_range='';
						$dst='N';
						$gmt_offset=0;

						$stmtA="select GMT_offset from vicidial_phone_codes where tz_code='$owner' and country_code='$phone_code' limit 1;";
							if($DBX){print STDERR "\n|$stmtA|\n";}
						$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
						$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
						$sthArows=$sthA->rows;
						if ($sthArows > 0)
							{
							@aryA = $sthA->fetchrow_array;
							$gmt_offset =	$aryA[0];  $gmt_offset =~ s/\+| //gi;
							$PC_processed++;
							$postalgmt_found++;
							}
						$sthA->finish();

						$stmtA = "select distinct DST_range from vicidial_phone_codes where tz_code='$owner' and country_code='$phone_code' order by DST_range desc limit 1;";
							if($DBX){print STDERR "\n|$stmtA|\n";}
						$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
						$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
						$sthArows=$sthA->rows;
						if ($sthArows > 0)
							{
							@aryA = $sthA->fetchrow_array;
							$dst_range =	$aryA[0];
							if (length($dst_range)>2) {$dst = 'Y';}
							if ($DBX) {print "     TZcode GMT record found for $owner: |$gmt_offset|$dst|$dst_range|\n";}
							}
						$sthA->finish();
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
						$AC_GMT_diff = ($area_GMT - $LOCAL_GMT_OFF_STD);
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
						if ($USACAN_DST) {$gmt_offset++;}
						$AC_processed++;
						}
					if ( (!$AC_processed) && ($dst_range =~ /FSA-LSO/) )
						{
						if ($DBX) {print "     First Sunday April to Last Sunday October\n";}
						&NA_dstcalc;
						if ($DBX) {print "     DST: $NA_DST\n";}
						if ($NA_DST) {$gmt_offset++;}
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
						if ($BZL_DST) {$gmt_offset++;}
						$AC_processed++;
						}
					if (!$AC_processed)
						{
						if ($DBX) {print "     No DST Method Found\n";}
						if ($DBX) {print "     DST: 0\n";}
						$AC_processed++;
						}

					}


				if ($map_count > 0)
					{
					$stmtZ = "INSERT INTO vicidial_list (lead_id,entry_date,modify_date,status,user,vendor_lead_code,source_id,list_id,gmt_offset_now,called_since_last_reset,phone_code,phone_number,title,first_name,middle_initial,last_name,address1,address2,address3,city,state,province,postal_code,country_code,gender,date_of_birth,alt_phone,email,security_phrase,comments,called_count,last_local_call_time,rank,owner) values('','$insert_date','$modify_date','$status','$user','$vendor_lead_code','$source_id','$list_id','$gmt_offset','$called_since_last_reset','$phone_code','$phone_number','$title','$first_name','$middle_initial','$last_name','$address1','$address2','$address3','$city','$state','$province','$postal_code','$country','$gender','$date_of_birth','$alt_phone','$email','$security_phrase','$comments','$called_count','2008-01-01 00:00:00','$rank','$owner');";
						if (!$T) {$affected_rows = $dbhA->do($stmtZ); } #  or die  "Couldn't execute query: |$stmtZ|\n";
						$lead_id = $dbhA->{'mysql_insertid'};
						if($DB){print STDERR "\n|$affected_rows|$stmtZ|\n";}
						$c++;

					$r=0; $s=0;
					while ($r < $map_count)
						{
						$s++;
						$stmtZ = "INSERT INTO vicidial_list_alt_phones (lead_id,phone_code,phone_number,alt_phone_note,alt_phone_count) values('$lead_id','$ALTm_phone_code[$r]','$ALTm_phone_number[$r]','$ALTm_phone_note[$r]','$s');";
							if (!$T) {$affected_rows = $dbhA->do($stmtZ); } #  or die  "Couldn't execute query: |$stmtZ|\n";
						#	$alt_phone_id = $dbhA->{'mysql_insertid'};
							if($DB){print STDERR "\n|$affected_rows|$stmtZ|\n";}
						$r++;
						}
					}
				else
					{
					if ($multi_insert_counter > 8)
						{
						### insert good lead into pending_transactions table ###
						$stmtZ = "INSERT INTO vicidial_list (lead_id,entry_date,modify_date,status,user,vendor_lead_code,source_id,list_id,gmt_offset_now,called_since_last_reset,phone_code,phone_number,title,first_name,middle_initial,last_name,address1,address2,address3,city,state,province,postal_code,country_code,gender,date_of_birth,alt_phone,email,security_phrase,comments,called_count,last_local_call_time,rank,owner) values$multistmt('','$insert_date','$modify_date','$status','$user','$vendor_lead_code','$source_id','$list_id','$gmt_offset','$called_since_last_reset','$phone_code','$phone_number','$title','$first_name','$middle_initial','$last_name','$address1','$address2','$address3','$city','$state','$province','$postal_code','$country','$gender','$date_of_birth','$alt_phone','$email','$security_phrase','$comments','$called_count','2008-01-01 00:00:00','$rank','$owner');";
							if (!$T) {$affected_rows = $dbhA->do($stmtZ); } #  or die  "Couldn't execute query: |$stmtZ|\n";
							if($DB){print STDERR "\n|$affected_rows|$stmtZ|\n";}

						$multistmt='';
						$multi_insert_counter=0;
						$c++;
						}
					else
						{
						$multistmt .= "('','$insert_date','$modify_date','$status','$user','$vendor_lead_code','$source_id','$list_id','$gmt_offset','$called_since_last_reset','$phone_code','$phone_number','$title','$first_name','$middle_initial','$last_name','$address1','$address2','$address3','$city','$state','$province','$postal_code','$country','$gender','$date_of_birth','$alt_phone','$email','$security_phrase','$comments','$called_count','2008-01-01 00:00:00','$rank','$owner'),";
						$multi_insert_counter++;
						}
					}
				$b++;
				}
			else
				{
				if ($valid_lead < 1)
					{if ($q < 1) {print "INVALID: $phone_number|$list_id|$dup_lead_list|$a|$title $alt_phone\n";}   $h++;}
				else
					{
					if ($dup_lead > 0)
						{if ($q < 1) {print "DUPLICATE: $phone_number|$list_id|$dup_lead_list|$a|$title $alt_phone\n";}   $f++;}
					else
						{if ($q < 1) {print "BAD Home_Phone: $phone_number|$vendor_id|$a\n";}   $e++;}
					}
				}
			
			$a++;

			if ($q < 1) 
				{
				if ($a =~ /100$/i) {print STDERR "0     $a\r";}
				if ($a =~ /200$/i) {print STDERR "+     $a\r";}
				if ($a =~ /300$/i) {print STDERR "|     $a\r";}
				if ($a =~ /400$/i) {print STDERR "\\     $a\r";}
				if ($a =~ /500$/i) {print STDERR "-     $a\r";}
				if ($a =~ /600$/i) {print STDERR "/     $a\r";}
				if ($a =~ /700$/i) {print STDERR "|     $a\r";}
				if ($a =~ /800$/i) {print STDERR "+     $a\r";}
				if ($a =~ /900$/i) {print STDERR "0     $a\r";}
				if ($a =~ /000$/i) {print "$a|$b|$c|$d|$e|$f|$g|$h|$phone_number|\n";}
				}

			}

		if (length($multistmt) > 10)
			{
			chop($multistmt);
			### insert good deal into pending_transactions table ###
			$stmtZ = "INSERT INTO vicidial_list (lead_id,entry_date,modify_date,status,user,vendor_lead_code,source_id,list_id,gmt_offset_now,called_since_last_reset,phone_code,phone_number,title,first_name,middle_initial,last_name,address1,address2,address3,city,state,province,postal_code,country_code,gender,date_of_birth,alt_phone,email,security_phrase,comments,called_count,last_local_call_time,rank,owner) values$multistmt;";
					if (!$T) {$affected_rows = $dbhA->do($stmtZ); } #  or die  "Couldn't execute query: |$stmtZ|\n";
					if($DB){print STDERR "\n|$affected_rows|$stmtZ|\n";}

			$multistmt='';
			$multi_insert_counter=0;
			$c++;
			}


			### open the stats out file for writing ###
			open(Sout, ">>$VDHLOGfile")
					|| die "Can't open $VDHLOGfile: $!\n";


			### close file handler and DB connections ###
			$Falert  = "\n\nTOTALS FOR $FILEname:\n";
			if ($new_list_for_each_file > 0)
				{$Falert .= "New List ID:        $new_list_id\n";}
			$Falert .= "Lines in lead file: $a\n";
			$Falert .= "INSERTED:           $b\n";
			$Falert .= "INSERT STATEMENTS:  $c\n";
			$Falert .= "ERROR:              $e\n";
			$Falert .= "MULTI-ALT-PHONE:    $g\n";
			if ($f > 0)
				{$Falert .= "DUPLICATES:         $f\n";}
			if ($h > 0)
				{$Falert .= "INVALID PHONES:     $h\n";}

			if ($q < 1) {print "$Falert";}
			print Sout "$Falert";
			$Ealert .= "$Falert";

			close(infile);
			close(Sout);
			chmod 0777, "$VDHLOGfile";

			### Move file to the DONE directory locally
			if (!$T) {`mv -f $dir1/$FILEname $dir2/$FILEname`;}

			}
		}
	$i++;
	}


$dbhA->disconnect();

### calculate time to run script ###
$secY = time();
$secZ = ($secY - $secX);
$secZm = ($secZ /60);

if ($q < 1)
	{
	print "script execution time in seconds: $secZ     minutes: $secZm\n";
	}

###### EMAIL SECTION

if ( (length($Ealert)>5) && (length($email_list) > 3) )
	{
	if ($q < 1) {print "Sending email: $email_list\n";}

	use MIME::QuotedPrint;
	use MIME::Base64;
	use Mail::Sendmail;

	$mailsubject = "VICIDIAL LEAD FILE LOAD $pulldate0";

	  %mail = ( To      => "$email_list",
							From    => "$email_sender",
							Subject => "$mailsubject",
							Message => "VICIDIAL LEAD FILE LOAD $pulldate0\n\n$Ealert\n"
					   );
			sendmail(%mail) or die $mail::Sendmail::error;
		   if ($q < 1) {print "ok. log says:\n", $mail::sendmail::log;}  ### print mail log for status
	}

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
