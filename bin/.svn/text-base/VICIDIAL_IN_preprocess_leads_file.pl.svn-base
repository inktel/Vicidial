#!/usr/bin/perl
#
# VICIDIAL_IN_preprocess_leads_file.pl version 2.4
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
# 101217-0515 - First build
# 110418-0604 - Added dccsv52 format
# 110420-0944 - Fixed file prefix issue with multiple processes running
#

$version = '110418-0604';

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


if (!$VDHLOGfile) {$VDHLOGfile = "$PATHlogs/preprocessleads.$year-$mon-$mday";}

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
		print "  [-q] = quiet\n";
		print "  [-t] = test\n";
		print "  [--version] = version\n";
		print "  [--debug] = debug output\n";
		print "  [--format=standard] = ability to define a format, standard is default, formats allowed shown in examples\n";
		print "  [--file-prefix-filter=WXYZ] = will only process lead files that begin with the characters you define\n";
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
		print "dccsv43, dccsvref51, dccsv52, dccsvref52 and ncacsv39:\n";
		print "---format too confusing to list in the help screen---\n\n";

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

		if ($args =~ /-t/i)
			{
			$T=1;
			$TEST=1;
			if ($q < 1) {print "\n----- TESTING -----\n\n";}
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
	print "\n\n\n\n\n\n\n\n\n\n\n\n-- VICIDIAL_IN_preprocess_leads_file.pl --\n\n";
	print "This program is designed to take a file and clean it up before it is imported into the VICIDIAL system. \n\n";
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
$dir1 = "$PATHhome/PREPROCESS";
$dir2 = "$PATHhome/PREPROCESS/DONE";
$dir3 = "$PATHhome/LEADS_IN";

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
	$pipe='.pipe';

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

			### open the in file for reading ###
			open(infile, "$dir2/$source$FILES[$i]")
					|| die "Can't open $source$FILES[$i]: $!\n";

			### open the new out file for writing ###
			open(Pout, ">$dir2/$FILEname$pipe")
					|| die "Can't open $dir2/$FILEname$pipe: $!\n";

			$a=0;	### each line of input file counter ###
			$b=0;	### new records counter ###
			$c=0;	### no-phone counter ###
			$d=0;	###
			$e=0;	###
			$f=0;	###
			$g=0;	###

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


			##### BEGIN ncacsv31 format #####
			# This is the format for the ncacsv39x lead files <multi-lead insert format>
			#5200556,"00052555570016383767","SERVERS JR,JIM M","2915551802",1121.31,"627555120","77380","HSBC","SCRIPT4","6205554433","No Message","2915551802","16383767","","","","","","","","","","","","","","","6001","DLR3","549","","","","","","","","",""

			# field mappings:
			#	$vendor_lead_code =		1  - acctid
			#	$source_id =			2  - uniqueid
			#	$first_name =			3  - first_name
			#	$middle_initial =		 <blank>
			#	$last_name =			3  - last_name
			#	$province =				 <blank>
			#	$address2 =				29 - collectorid
			#	$address3 =				8 - account description
			#	$alt_phone =			5 - balance
			#	$postal_code =			7  - zip
			#	$address1 =				 <blank>
			#	$city =					9  - script
			#	$security_phrase =		 <assigned by state lookup>
			#	$email =				6  - ssn
			#	$title =				27 - folderid
			#	$state =				 <assigned by phone number areacode lookup>
			#	$country =				 <blank>
			#	$gender =				 <blank>
			#	$date_of_birth =		 <blank>
			#	$comments =				 <blank>
			#	$rank =					 <assigned by phone number type>
			#	$owner =				 <assigned by phone number type>

			if ( ($format =~ /ncacsv31/) && ($format_set < 1) )
				{
				$number = $raw_number;
				chomp($number);
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
				$number =~ s/,\"/\|/gi;
				$number =~ s/\",/\|/gi;
				$number =~ s/,/\|/gi;
				$number =~ s/\"//gi;
				@m = split(/\|/, $number);

				if ($DBX) {print "ncacsv31($a) -   $number\n";}

				$vendor_lead_code =		$m[0];		chomp($vendor_lead_code);	# acctid
				$source_id =			$m[1];		chomp($source_id);	# uniqueid
				$list_id =				'939';
				$phone_code =			'1';
				$full_name =			$m[2];		@name = split(/,/, $full_name);
				$first_name =			$name[1];		chomp($first_name);
				$middle_initial =		'';
				$last_name =			$name[0];		chomp($last_name);
				$province =				$m[3];	# acct main phone
				$address2 =				$m[4];	# balance
				$alt_phone =			$m[5];		chomp($alt_phone);	$alt_phone =~ s/\D//gi;	# ssn
				$postal_code =			$m[6];		chomp($postal_code);	$postal_code =~ s/\D//gi;	# zip
				$address1 =				$m[7];	# descrip
				$address3 =				$m[8];	# script
				$security_phrase =		$m[9];		chomp($security_phrase);	$security_phrase =~ s/\D//gi;	# cid
				$email =				$m[36];	# collectorid
				$title =				$m[28];	# folderid
				$phone_number =			$m[3];
					$USarea = 			substr($phone_number, 0, 3);
				$state =				'';
				$country =				'';
				$gender =				'';
				$date_of_birth =		'';
				$comments =				'';
				$called_count =			'0';
				$status =				'NEW';
				$insert_date =			$pulldate0;
				$rank =					'';
				$owner =				'';
				$entry_date =			'';
				$multi_alt_phones =		'';
				$phone_found=0;


				if (length($m[11]) > 9) 
					{
					$phone_number =			$m[11];
					$rank =					'1';
					$owner =				'home';
						&cid_state_areacode;		# lookup areacode-state-based CID
					print Pout "$vendor_lead_code|$source_id|$list_id|$phone_code|$phone_number|$title|$first_name|$middle|$last_name|$address1|$address2|$address3|$city|$state|$province|$postal_code|$country|$gender|$date_of_birth|$alt_phone|$email|$security_phrase|$owner|$called_count|$status|$entry_date|$rank|$owner|\n";
					$b++;   $phone_found++;
					if ($DBX) {print "     $b|$phone_found -   $a|$phone_number|$city|$rank|$owner|$vendor_lead_code|$source_id\n";}
					}
				if (length($m[12]) > 9) 
					{
					$phone_number =			$m[12];
					$rank =					'2';
					$owner =				'home2';
						&cid_state_areacode;		# lookup areacode-state-based CID
					print Pout "$vendor_lead_code|$source_id|$list_id|$phone_code|$phone_number|$title|$first_name|$middle|$last_name|$address1|$address2|$address3|$city|$state|$province|$postal_code|$country|$gender|$date_of_birth|$alt_phone|$email|$security_phrase|$owner|$called_count|$status|$entry_date|$rank|$owner|\n";
					$b++;   $phone_found++;
					if ($DBX) {print "     $b|$phone_found -   $a|$phone_number|$city|$rank|$owner|$vendor_lead_code|$source_id\n";}
					}
				if (length($m[13]) > 9) 
					{
					$phone_number =			$m[13];
					$rank =					'3';
					$owner =				'work';
						&cid_state_areacode;		# lookup areacode-state-based CID
					print Pout "$vendor_lead_code|$source_id|$list_id|$phone_code|$phone_number|$title|$first_name|$middle|$last_name|$address1|$address2|$address3|$city|$state|$province|$postal_code|$country|$gender|$date_of_birth|$alt_phone|$email|$security_phrase|$owner|$called_count|$status|$entry_date|$rank|$owner|\n";
					$b++;   $phone_found++;
					if ($DBX) {print "     $b|$phone_found -   $a|$phone_number|$city|$rank|$owner|$vendor_lead_code|$source_id\n";}
					}
				if (length($m[14]) > 9) 
					{
					$phone_number =			$m[14];
					$rank =					'4';
					$owner =				'work2';
						&cid_state_areacode;		# lookup areacode-state-based CID
					print Pout "$vendor_lead_code|$source_id|$list_id|$phone_code|$phone_number|$title|$first_name|$middle|$last_name|$address1|$address2|$address3|$city|$state|$province|$postal_code|$country|$gender|$date_of_birth|$alt_phone|$email|$security_phrase|$owner|$called_count|$status|$entry_date|$rank|$owner|\n";
					$b++;   $phone_found++;
					if ($DBX) {print "     $b|$phone_found -   $a|$phone_number|$city|$rank|$owner|$vendor_lead_code|$source_id\n";}
					}
				if (length($m[15]) > 9) 
					{
					$phone_number =			$m[15];
					$rank =					'5';
					$owner =				'cell';
						&cid_state_areacode;		# lookup areacode-state-based CID
					print Pout "$vendor_lead_code|$source_id|$list_id|$phone_code|$phone_number|$title|$first_name|$middle|$last_name|$address1|$address2|$address3|$city|$state|$province|$postal_code|$country|$gender|$date_of_birth|$alt_phone|$email|$security_phrase|$owner|$called_count|$status|$entry_date|$rank|$owner|\n";
					$b++;   $phone_found++;
					if ($DBX) {print "     $b|$phone_found -   $a|$phone_number|$city|$rank|$owner|$vendor_lead_code|$source_id\n";}
					}
				if (length($m[16]) > 9) 
					{
					$phone_number =			$m[16];
					$rank =					'6';
					$owner =				'cell2';
						&cid_state_areacode;		# lookup areacode-state-based CID
					print Pout "$vendor_lead_code|$source_id|$list_id|$phone_code|$phone_number|$title|$first_name|$middle|$last_name|$address1|$address2|$address3|$city|$state|$province|$postal_code|$country|$gender|$date_of_birth|$alt_phone|$email|$security_phrase|$owner|$called_count|$status|$entry_date|$rank|$owner|\n";
					$b++;   $phone_found++;
					if ($DBX) {print "     $b|$phone_found -   $a|$phone_number|$city|$rank|$owner|$vendor_lead_code|$source_id\n";}
					}
				if (length($m[17]) > 9) 
					{
					$phone_number =			$m[17];
					$rank =					'7';
					$owner =				'alt1';
						&cid_state_areacode;		# lookup areacode-state-based CID
					print Pout "$vendor_lead_code|$source_id|$list_id|$phone_code|$phone_number|$title|$first_name|$middle|$last_name|$address1|$address2|$address3|$city|$state|$province|$postal_code|$country|$gender|$date_of_birth|$alt_phone|$email|$security_phrase|$owner|$called_count|$status|$entry_date|$rank|$owner|\n";
					$b++;   $phone_found++;
					if ($DBX) {print "     $b|$phone_found -   $a|$phone_number|$city|$rank|$owner|$vendor_lead_code|$source_id\n";}
					}
				if (length($m[18]) > 9) 
					{
					$phone_number =			$m[18];
					$rank =					'8';
					$owner =				'alt2';
						&cid_state_areacode;		# lookup areacode-state-based CID
					print Pout "$vendor_lead_code|$source_id|$list_id|$phone_code|$phone_number|$title|$first_name|$middle|$last_name|$address1|$address2|$address3|$city|$state|$province|$postal_code|$country|$gender|$date_of_birth|$alt_phone|$email|$security_phrase|$owner|$called_count|$status|$entry_date|$rank|$owner|\n";
					$b++;   $phone_found++;
					if ($DBX) {print "     $b|$phone_found -   $a|$phone_number|$city|$rank|$owner|$vendor_lead_code|$source_id\n";}
					}
				if (length($m[19]) > 9) 
					{
					$phone_number =			$m[19];
					$rank =					'9';
					$owner =				'alt3';
						&cid_state_areacode;		# lookup areacode-state-based CID
					print Pout "$vendor_lead_code|$source_id|$list_id|$phone_code|$phone_number|$title|$first_name|$middle|$last_name|$address1|$address2|$address3|$city|$state|$province|$postal_code|$country|$gender|$date_of_birth|$alt_phone|$email|$security_phrase|$owner|$called_count|$status|$entry_date|$rank|$owner|\n";
					$b++;   $phone_found++;
					if ($DBX) {print "     $b|$phone_found -   $a|$phone_number|$city|$rank|$owner|$vendor_lead_code|$source_id\n";}
					}
				if (length($m[20]) > 9) 
					{
					$phone_number =			$m[20];
					$rank =					'10';
					$owner =				'trigger1';
						&cid_state_areacode;		# lookup areacode-state-based CID
					print Pout "$vendor_lead_code|$source_id|$list_id|$phone_code|$phone_number|$title|$first_name|$middle|$last_name|$address1|$address2|$address3|$city|$state|$province|$postal_code|$country|$gender|$date_of_birth|$alt_phone|$email|$security_phrase|$owner|$called_count|$status|$entry_date|$rank|$owner|\n";
					$b++;   $phone_found++;
					if ($DBX) {print "     $b|$phone_found -   $a|$phone_number|$city|$rank|$owner|$vendor_lead_code|$source_id\n";}
					}
				if (length($m[21]) > 9) 
					{
					$phone_number =			$m[21];
					$rank =					'11';
					$owner =				'trigger2';
						&cid_state_areacode;		# lookup areacode-state-based CID
					print Pout "$vendor_lead_code|$source_id|$list_id|$phone_code|$phone_number|$title|$first_name|$middle|$last_name|$address1|$address2|$address3|$city|$state|$province|$postal_code|$country|$gender|$date_of_birth|$alt_phone|$email|$security_phrase|$owner|$called_count|$status|$entry_date|$rank|$owner|\n";
					$b++;   $phone_found++;
					if ($DBX) {print "     $b|$phone_found -   $a|$phone_number|$city|$rank|$owner|$vendor_lead_code|$source_id\n";}
					}
				if (length($m[22]) > 9) 
					{
					$phone_number =			$m[22];
					$rank =					'12';
					$owner =				'skiphome';
						&cid_state_areacode;		# lookup areacode-state-based CID
					print Pout "$vendor_lead_code|$source_id|$list_id|$phone_code|$phone_number|$title|$first_name|$middle|$last_name|$address1|$address2|$address3|$city|$state|$province|$postal_code|$country|$gender|$date_of_birth|$alt_phone|$email|$security_phrase|$owner|$called_count|$status|$entry_date|$rank|$owner|\n";
					$b++;   $phone_found++;
					if ($DBX) {print "     $b|$phone_found -   $a|$phone_number|$city|$rank|$owner|$vendor_lead_code|$source_id\n";}
					}
				if (length($m[23]) > 9) 
					{
					$phone_number =			$m[23];
					$rank =					'13';
					$owner =				'skipwork';
						&cid_state_areacode;		# lookup areacode-state-based CID
					print Pout "$vendor_lead_code|$source_id|$list_id|$phone_code|$phone_number|$title|$first_name|$middle|$last_name|$address1|$address2|$address3|$city|$state|$province|$postal_code|$country|$gender|$date_of_birth|$alt_phone|$email|$security_phrase|$owner|$called_count|$status|$entry_date|$rank|$owner|\n";
					$b++;   $phone_found++;
					if ($DBX) {print "     $b|$phone_found -   $a|$phone_number|$city|$rank|$owner|$vendor_lead_code|$source_id\n";}
					}
				if (length($m[24]) > 9) 
					{
					$phone_number =			$m[24];
					$rank =					'14';
					$owner =				'skipcell';
						&cid_state_areacode;		# lookup areacode-state-based CID
					print Pout "$vendor_lead_code|$source_id|$list_id|$phone_code|$phone_number|$title|$first_name|$middle|$last_name|$address1|$address2|$address3|$city|$state|$province|$postal_code|$country|$gender|$date_of_birth|$alt_phone|$email|$security_phrase|$owner|$called_count|$status|$entry_date|$rank|$owner|\n";
					$b++;   $phone_found++;
					if ($DBX) {print "     $b|$phone_found -   $a|$phone_number|$city|$rank|$owner|$vendor_lead_code|$source_id\n";}
					}

				if ($phone_found < 1) 
					{
					if ($DB) {print "No-Phone entry: $a|$vendor_lead_code|$source_id\n";}
					$c++;
					}
				$format_set++;
				}
			##### END ncacsv31 format #####


			##### BEGIN ncacsv39 format #####
			# This is the format for the ncacsv39 lead files <multi-lead insert format>
			#5200556,"00052555570016383767","SERVERS JR,JIM M","2915551802",1121.31,"627555120","77380","HSBC","SCRIPT4","6205554433","No Message","2915551802","16383767","","","","","","","","","","","","","","","6001","DLR3","549","","","","","","","","",""

			# field mappings:
			#	$vendor_lead_code =		1  - acctid
			#	$source_id =			2  - uniqueid
			#	$first_name =			3  - first_name
			#	$middle_initial =		 <blank>
			#	$last_name =			3  - last_name
			#	$province =				4  - acct main phone
			#	$address2 =				5  - balance
			#	$alt_phone =			6  - ssn
			#	$postal_code =			7  - zip
			#	$address1 =				8  - descrip
			#	$address3 =				9  - script
			#	$security_phrase =		10 - cid
			#	$email =				37 - collectorid
			#	$title =				29 - folderid
			#	$state =				 <blank>
			#	$country =				 <blank>
			#	$gender =				 <blank>
			#	$date_of_birth =		 <blank>
			#	$comments =				 <blank>
			#	$rank =					 <assigned by phone number type>
			#	$owner =				 <assigned by phone number type>
			#	$city =					 <assigned by phone number id>

			if ( ($format =~ /ncacsv39/) && ($format_set < 1) )
				{
				$number = $raw_number;
				chomp($number);
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
				$number =~ s/,\"/\|/gi;
				$number =~ s/\",/\|/gi;
				$number =~ s/\"//gi;
				@m = split(/\|/, $number);

				if ($DBX) {print "$a -   $number\n";}

				$vendor_lead_code =		$m[0];		chomp($vendor_lead_code);	# acctid
				$source_id =			$m[1];		chomp($source_id);	# uniqueid
				$list_id =				'939';
				$phone_code =			'1';
				$full_name =			$m[2];		@name = split(/,/, $full_name);
				$first_name =			$name[1];		chomp($first_name);
				$middle_initial =		'';
				$last_name =			$name[0];		chomp($last_name);
				$province =				$m[3];	# acct main phone
				$address2 =				$m[4];	# balance
				$alt_phone =			$m[5];		chomp($alt_phone);	$alt_phone =~ s/\D//gi;	# ssn
				$postal_code =			$m[6];		chomp($postal_code);	$postal_code =~ s/\D//gi;	# zip
				$address1 =				$m[7];	# descrip
				$address3 =				$m[8];	# script
				$security_phrase =		$m[9];		chomp($security_phrase);	$security_phrase =~ s/\D//gi;	# cid
				$email =				$m[36];	# collectorid
				$title =				$m[28];	# folderid

				$phone_number =			$m[3];
					$USarea = 			substr($phone_number, 0, 3);
				$state =				'';
				$country =				'';
				$gender =				'';
				$date_of_birth =		'';
				$comments =				'';
				$called_count =			'0';
				$status =				'NEW';
				$insert_date =			$pulldate0;
				$rank =					'';
				$owner =				'';
				$entry_date =			'';
				$multi_alt_phones =		'';
				$phone_found=0;

				if (length($m[11]) > 9) 
					{
					$phone_number =			$m[11];
					$city =					$m[12];	# phoneid
					$rank =					'1';
					$owner =				'home';

					print Pout "$vendor_lead_code|$source_id|$list_id|$phone_code|$phone_number|$title|$first_name|$middle|$last_name|$address1|$address2|$address3|$city|$state|$province|$postal_code|$country|$gender|$date_of_birth|$alt_phone|$email|$security_phrase|$owner|$called_count|$status|$entry_date|$rank|$owner|\n";
					$b++;   $phone_found++;
					if ($DBX) {print "     $b|$phone_found -   $a|$phone_number|$city|$rank|$owner|$vendor_lead_code|$source_id\n";}
					}
				if (length($m[13]) > 9) 
					{
					$phone_number =			$m[13];
					$city =					$m[14];	# phoneid
					$rank =					'2';
					$owner =				'work';

					print Pout "$vendor_lead_code|$source_id|$list_id|$phone_code|$phone_number|$title|$first_name|$middle|$last_name|$address1|$address2|$address3|$city|$state|$province|$postal_code|$country|$gender|$date_of_birth|$alt_phone|$email|$security_phrase|$owner|$called_count|$status|$entry_date|$rank|$owner|\n";
					$b++;   $phone_found++;
					if ($DBX) {print "     $b|$phone_found -   $a|$phone_number|$city|$rank|$owner|$vendor_lead_code|$source_id\n";}
					}
				if (length($m[15]) > 9) 
					{
					$phone_number =			$m[15];
					$city =					$m[16];	# phoneid
					$rank =					'3';
					$owner =				'cell';

					print Pout "$vendor_lead_code|$source_id|$list_id|$phone_code|$phone_number|$title|$first_name|$middle|$last_name|$address1|$address2|$address3|$city|$state|$province|$postal_code|$country|$gender|$date_of_birth|$alt_phone|$email|$security_phrase|$owner|$called_count|$status|$entry_date|$rank|$owner|\n";
					$b++;   $phone_found++;
					if ($DBX) {print "     $b|$phone_found -   $a|$phone_number|$city|$rank|$owner|$vendor_lead_code|$source_id\n";}
					}
				if (length($m[17]) > 9) 
					{
					$phone_number =			$m[17];
					$city =					$m[18];	# phoneid
					$rank =					'4';
					$owner =				'alt1';

					print Pout "$vendor_lead_code|$source_id|$list_id|$phone_code|$phone_number|$title|$first_name|$middle|$last_name|$address1|$address2|$address3|$city|$state|$province|$postal_code|$country|$gender|$date_of_birth|$alt_phone|$email|$security_phrase|$owner|$called_count|$status|$entry_date|$rank|$owner|\n";
					$b++;   $phone_found++;
					if ($DBX) {print "     $b|$phone_found -   $a|$phone_number|$city|$rank|$owner|$vendor_lead_code|$source_id\n";}
					}
				if (length($m[19]) > 9) 
					{
					$phone_number =			$m[19];
					$city =					$m[20];	# phoneid
					$rank =					'5';
					$owner =				'alt2';

					print Pout "$vendor_lead_code|$source_id|$list_id|$phone_code|$phone_number|$title|$first_name|$middle|$last_name|$address1|$address2|$address3|$city|$state|$province|$postal_code|$country|$gender|$date_of_birth|$alt_phone|$email|$security_phrase|$owner|$called_count|$status|$entry_date|$rank|$owner|\n";
					$b++;   $phone_found++;
					if ($DBX) {print "     $b|$phone_found -   $a|$phone_number|$city|$rank|$owner|$vendor_lead_code|$source_id\n";}
					}
				if (length($m[21]) > 9) 
					{
					$phone_number =			$m[21];
					$city =					$m[22];	# phoneid
					$rank =					'6';
					$owner =				'alt3';

					print Pout "$vendor_lead_code|$source_id|$list_id|$phone_code|$phone_number|$title|$first_name|$middle|$last_name|$address1|$address2|$address3|$city|$state|$province|$postal_code|$country|$gender|$date_of_birth|$alt_phone|$email|$security_phrase|$owner|$called_count|$status|$entry_date|$rank|$owner|\n";
					$b++;   $phone_found++;
					if ($DBX) {print "     $b|$phone_found -   $a|$phone_number|$city|$rank|$owner|$vendor_lead_code|$source_id\n";}
					}
				if (length($m[23]) > 9) 
					{
					$phone_number =			$m[23];
					$city =					$m[24];	# phoneid
					$rank =					'7';
					$owner =				'trigger1';

					print Pout "$vendor_lead_code|$source_id|$list_id|$phone_code|$phone_number|$title|$first_name|$middle|$last_name|$address1|$address2|$address3|$city|$state|$province|$postal_code|$country|$gender|$date_of_birth|$alt_phone|$email|$security_phrase|$owner|$called_count|$status|$entry_date|$rank|$owner|\n";
					$b++;   $phone_found++;
					if ($DBX) {print "     $b|$phone_found -   $a|$phone_number|$city|$rank|$owner|$vendor_lead_code|$source_id\n";}
					}
				if (length($m[25]) > 9) 
					{
					$phone_number =			$m[25];
					$city =					$m[26];	# phoneid
					$rank =					'8';
					$owner =				'trigger2';

					print Pout "$vendor_lead_code|$source_id|$list_id|$phone_code|$phone_number|$title|$first_name|$middle|$last_name|$address1|$address2|$address3|$city|$state|$province|$postal_code|$country|$gender|$date_of_birth|$alt_phone|$email|$security_phrase|$owner|$called_count|$status|$entry_date|$rank|$owner|\n";
					$b++;   $phone_found++;
					if ($DBX) {print "     $b|$phone_found -   $a|$phone_number|$city|$rank|$owner|$vendor_lead_code|$source_id\n";}
					}
				if (length($m[30]) > 9) 
					{
					$phone_number =			$m[30];
					$city =					$m[31];	# phoneid
					$rank =					'9';
					$owner =				'skiphome';

					print Pout "$vendor_lead_code|$source_id|$list_id|$phone_code|$phone_number|$title|$first_name|$middle|$last_name|$address1|$address2|$address3|$city|$state|$province|$postal_code|$country|$gender|$date_of_birth|$alt_phone|$email|$security_phrase|$owner|$called_count|$status|$entry_date|$rank|$owner|\n";
					$b++;   $phone_found++;
					if ($DBX) {print "     $b|$phone_found -   $a|$phone_number|$city|$rank|$owner|$vendor_lead_code|$source_id\n";}
					}
				if (length($m[32]) > 9) 
					{
					$phone_number =			$m[32];
					$city =					$m[33];	# phoneid
					$rank =					'10';
					$owner =				'skipwork';

					print Pout "$vendor_lead_code|$source_id|$list_id|$phone_code|$phone_number|$title|$first_name|$middle|$last_name|$address1|$address2|$address3|$city|$state|$province|$postal_code|$country|$gender|$date_of_birth|$alt_phone|$email|$security_phrase|$owner|$called_count|$status|$entry_date|$rank|$owner|\n";
					$b++;   $phone_found++;
					if ($DBX) {print "     $b|$phone_found -   $a|$phone_number|$city|$rank|$owner|$vendor_lead_code|$source_id\n";}
					}
				if (length($m[34]) > 9) 
					{
					$phone_number =			$m[34];
					$city =					$m[35];	# phoneid
					$rank =					'11';
					$owner =				'skipcell';

					print Pout "$vendor_lead_code|$source_id|$list_id|$phone_code|$phone_number|$title|$first_name|$middle|$last_name|$address1|$address2|$address3|$city|$state|$province|$postal_code|$country|$gender|$date_of_birth|$alt_phone|$email|$security_phrase|$owner|$called_count|$status|$entry_date|$rank|$owner|\n";
					$b++;   $phone_found++;
					if ($DBX) {print "     $b|$phone_found -   $a|$phone_number|$city|$rank|$owner|$vendor_lead_code|$source_id\n";}
					}

				if ($phone_found < 1) 
					{
					if ($DB) {print "No-Phone entry: $a|$vendor_lead_code|$source_id\n";}
					$c++;
					}
				$format_set++;
				}
			##### END ncacsv39 format #####


			##### BEGIN dccsv52 format #####
			# This is the format for the dccsv52 lead files <multi-lead insert format>
			#"BFRAME","RECORD_TYPE","LAST_NAME","FIRST_NAME","ADDR1","ADDR2","CITY","STATE","ZIP","ZIP4","ADDR_STATUS","DATE_PLACED","DATE_ADDED","DOB","LAST_LETTER","LAST_LETTER_DATE","LAST_WORKED","NEXT_ACTION_DATE","CAPTURE_CODE","CUR_CATEGORY","TIMES_DIALED","LAST_DIALED","TOTAL_PAID","DATE_LAST_PAID","NMBR_CALLS","NMBR_CONTACTS","NMBR_TIMES_WRKD","NMBR_LETTERS","STATUS_CODE","STATUS_DATE","SCORE","TIMES_TO_SERVICER","1ST-PMT-DEFAULT","TIME_ZONE","ORIG_CREDITOR","BALANCE","HOME_PHONE","WORK_PHONE","OTHER_PHONE","ACCT_OTHTEL2","ACCT_OTHTEL3","ACCT_OTHTEL4","ACCT_OTHTEL5","REF-NAME","REF-AD1","REF-AD2","REF-CITY","REF-ST","REF-POSTAL","REF-TEL1","REF-TEL2","SSN"
			#"II ACCT/1103566666  ","P","SMITH           ","        SAMMY","7838 W 109TH ST APT 12        ","                              ","OVERLAND PARK       ","KS","66212","0000","G","20091110","20091110","19661216","NOLTTR","00000000","20091214","20091219","1000","03","000","00000000","000000000.00 ","00000000","0004","0003","0004","000","ACTIVE","20091110","0648","00"," ","C","HSBC                          ","000000692.09 ","9135551212","0000000000","0000000000","0000000000","0000000000","0000000000","0000000000","","","","","  ","          ","          ","          ","578888888"

			# field mappings:
			#	$vendor_lead_code =		1  - BFRAME (number only)
			#	$source_id =			1  - BFRAME
			#	$first_name =			4  - FIRST_NAME
			#	$middle_initial =		 <blank>
			#	$last_name =			3  - LAST_NAME
			#	$province =				36 - BALANCE
			#	$address1 =				5  - ADDR1
			#	$address2 =				6  - ADDR2
			#	$city =					7  - CITY
			#	$state =				8  - STATE
			#	$postal_code =			9  - ZIP
			#	$address3 =				35 - ORIG_CREDITOR
			#	$date_of_birth =		14 - DOB
			#	$alt_phone =			27&29  - NMBR_TIMES_WRKD & STATUS_CODE
			#	$email =				52 - SSN
			#	$title =				26 - NMBR_CONTACTS
			#	$security_phrase =		 <assigned by state lookup>
			#	$comments =				44 - REF-NAME
			#	$country =				 <blank>
			#	$gender =				 <blank>
			#	$rank =					 <assigned by phone number type>
			#	$owner =				 <assigned by phone number type>

			if ( ($format =~ /dccsv52/) && ($format_set < 1) )
				{
				$number = $raw_number;
				chomp($number);
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
				$number =~ s/,\"/\|/gi;
				$number =~ s/\",/\|/gi;
				$number =~ s/\"//gi;
				@m = split(/\|/, $number);

				if ($DBX) {print "$a -   $number\n";}

				$vendor_lead_code =		$m[0];		chomp($vendor_lead_code);		$vendor_lead_code =~ s/\s+$//gi;
					$vendor_lead_code =~s/II ACCT\///gi;
					$vendor_lead_code =~s/WDRF  //gi;
					while (length($vendor_lead_code) > 10) {chop($vendor_lead_code);}
				$source_id =			$m[0];		chomp($source_id);		$source_id =~ s/\s+$//gi;
				$list_id =				'939';
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
				$rank =					'';			
				$owner =				'';
				$alt_phone =			"$m[26] - $m[28]";	# number of times worked AND old status code
				$phone_number =			$m[3];
				$entry_date =			'';
				$multi_alt_phones =		'';
				$phone_found=0;
				# remove non-digits from phone number fields
				$m[36] =~ s/\D//gi;
				$m[37] =~ s/\D//gi;
				$m[38] =~ s/\D//gi;
				$m[39] =~ s/\D//gi;
				$m[40] =~ s/\D//gi;
				$m[41] =~ s/\D//gi;
				$m[42] =~ s/\D//gi;
				$m[49] =~ s/\D//gi;
				$m[50] =~ s/\D//gi;

				if ( (length($m[36]) > 9) && ($m[36] !~ /0000000000/) )
					{
					$phone_number =			$m[36];
					$rank =					'1';
					$owner =				'HOME_PHONE';
					&cid_state;				# lookup state-based CID

					print Pout "$vendor_lead_code|$source_id|$list_id|$phone_code|$phone_number|$title|$first_name|$middle|$last_name|$address1|$address2|$address3|$city|$state|$province|$postal_code|$country|$gender|$date_of_birth|$alt_phone|$email|$security_phrase|$comments|$called_count|$status|$entry_date|$rank|$owner|\n";
					$b++;   $phone_found++;
					if ($DBX) {print "     $b|$phone_found -   $a|$phone_number|$city|$rank|$owner|$security_phrase|$vendor_lead_code|$source_id\n";}
					}
				if ( (length($m[37]) > 9) && ($m[37] !~ /0000000000/) )
					{
					$phone_number =			$m[37];
					$rank =					'2';
					$owner =				'WORK_PHONE';
					&cid_state;				# lookup state-based CID

					print Pout "$vendor_lead_code|$source_id|$list_id|$phone_code|$phone_number|$title|$first_name|$middle|$last_name|$address1|$address2|$address3|$city|$state|$province|$postal_code|$country|$gender|$date_of_birth|$alt_phone|$email|$security_phrase|$comments|$called_count|$status|$entry_date|$rank|$owner|\n";
					$b++;   $phone_found++;
					if ($DBX) {print "     $b|$phone_found -   $a|$phone_number|$city|$rank|$owner|$security_phrase|$vendor_lead_code|$source_id\n";}
					}
				if ( (length($m[38]) > 9) && ($m[38] !~ /0000000000/) )
					{
					$phone_number =			$m[38];
					$rank =					'3';
					$owner =				'OTHER_PHONE';
					&cid_state;				# lookup state-based CID

					print Pout "$vendor_lead_code|$source_id|$list_id|$phone_code|$phone_number|$title|$first_name|$middle|$last_name|$address1|$address2|$address3|$city|$state|$province|$postal_code|$country|$gender|$date_of_birth|$alt_phone|$email|$security_phrase|$comments|$called_count|$status|$entry_date|$rank|$owner|\n";
					$b++;   $phone_found++;
					if ($DBX) {print "     $b|$phone_found -   $a|$phone_number|$city|$rank|$owner|$security_phrase|$vendor_lead_code|$source_id\n";}
					}
				if ( (length($m[39]) > 9) && ($m[39] !~ /0000000000/) )
					{
					$phone_number =			$m[39];
					$rank =					'4';
					$owner =				'ACCT_OTHTEL2';
					&cid_state;				# lookup state-based CID

					print Pout "$vendor_lead_code|$source_id|$list_id|$phone_code|$phone_number|$title|$first_name|$middle|$last_name|$address1|$address2|$address3|$city|$state|$province|$postal_code|$country|$gender|$date_of_birth|$alt_phone|$email|$security_phrase|$comments|$called_count|$status|$entry_date|$rank|$owner|\n";
					$b++;   $phone_found++;
					if ($DBX) {print "     $b|$phone_found -   $a|$phone_number|$city|$rank|$owner|$security_phrase|$vendor_lead_code|$source_id\n";}
					}
				if ( (length($m[40]) > 9) && ($m[40] !~ /0000000000/) )
					{
					$phone_number =			$m[40];
					$rank =					'5';
					$owner =				'ACCT_OTHTEL3';
					&cid_state;				# lookup state-based CID

					print Pout "$vendor_lead_code|$source_id|$list_id|$phone_code|$phone_number|$title|$first_name|$middle|$last_name|$address1|$address2|$address3|$city|$state|$province|$postal_code|$country|$gender|$date_of_birth|$alt_phone|$email|$security_phrase|$comments|$called_count|$status|$entry_date|$rank|$owner|\n";
					$b++;   $phone_found++;
					if ($DBX) {print "     $b|$phone_found -   $a|$phone_number|$city|$rank|$owner|$security_phrase|$vendor_lead_code|$source_id\n";}
					}
				if ( (length($m[41]) > 9) && ($m[41] !~ /0000000000/) )
					{
					$phone_number =			$m[41];
					$rank =					'6';
					$owner =				'ACCT_OTHTEL4';
					&cid_state;				# lookup state-based CID

					print Pout "$vendor_lead_code|$source_id|$list_id|$phone_code|$phone_number|$title|$first_name|$middle|$last_name|$address1|$address2|$address3|$city|$state|$province|$postal_code|$country|$gender|$date_of_birth|$alt_phone|$email|$security_phrase|$comments|$called_count|$status|$entry_date|$rank|$owner|\n";
					$b++;   $phone_found++;
					if ($DBX) {print "     $b|$phone_found -   $a|$phone_number|$city|$rank|$owner|$security_phrase|$vendor_lead_code|$source_id\n";}
					}
				if ( (length($m[42]) > 9) && ($m[42] !~ /0000000000/) )
					{
					$phone_number =			$m[42];
					$rank =					'7';
					$owner =				'ACCT_OTHTEL5';
					&cid_state;				# lookup state-based CID

					print Pout "$vendor_lead_code|$source_id|$list_id|$phone_code|$phone_number|$title|$first_name|$middle|$last_name|$address1|$address2|$address3|$city|$state|$province|$postal_code|$country|$gender|$date_of_birth|$alt_phone|$email|$security_phrase|$comments|$called_count|$status|$entry_date|$rank|$owner|\n";
					$b++;   $phone_found++;
					if ($DBX) {print "     $b|$phone_found -   $a|$phone_number|$city|$rank|$owner|$security_phrase|$vendor_lead_code|$source_id\n";}
					}
				if ( (length($m[49]) > 9) && ($m[49] !~ /0000000000/) )
					{
					$phone_number =			$m[49];
					$rank =					'8';
					$owner =				'REF-TEL1';
					&cid_state;				# lookup state-based CID

					print Pout "$vendor_lead_code|$source_id|$list_id|$phone_code|$phone_number|$title|$first_name|$middle|$last_name|$address1|$address2|$address3|$city|$state|$province|$postal_code|$country|$gender|$date_of_birth|$alt_phone|$email|$security_phrase|$comments|$called_count|$status|$entry_date|$rank|$owner|\n";
					$b++;   $phone_found++;
					if ($DBX) {print "     $b|$phone_found -   $a|$phone_number|$city|$rank|$owner|$security_phrase|$vendor_lead_code|$source_id\n";}
					}
				if ( (length($m[50]) > 9) && ($m[50] !~ /0000000000/) )
					{
					$phone_number =			$m[50];
					$rank =					'9';
					$owner =				'REF-TEL2';
					&cid_state;				# lookup state-based CID

					print Pout "$vendor_lead_code|$source_id|$list_id|$phone_code|$phone_number|$title|$first_name|$middle|$last_name|$address1|$address2|$address3|$city|$state|$province|$postal_code|$country|$gender|$date_of_birth|$alt_phone|$email|$security_phrase|$comments|$called_count|$status|$entry_date|$rank|$owner|\n";
					$b++;   $phone_found++;
					if ($DBX) {print "     $b|$phone_found -   $a|$phone_number|$city|$rank|$owner|$security_phrase|$vendor_lead_code|$source_id\n";}
					}

				if ($phone_found < 1) 
					{
					if ($DB) {print "No-Phone entry: $a|$vendor_lead_code|$source_id\n";}
					$c++;
					}
				$format_set++;
				}
			##### END dccsv52 format #####


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


			if ($DBX) {print "$a|$phone_number\n";}

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
				if ($a =~ /000$/i) {print "$a|$b|$c|$d|$e|$f|$g|$phone_number|\n";}
				}

			}


			### open the stats out file for writing ###
			open(Sout, ">>$VDHLOGfile")
					|| die "Can't open $VDHLOGfile: $!\n";


			### close file handler and DB connections ###
			$Falert  = "\n\nTOTALS FOR $FILEname:\n";
			$Falert .= "Lines in lead file: $a\n";
			$Falert .= "Lines in new file:  $b\n";
			$Falert .= "No-Phone records:   $c\n";

			if ($q < 1) {print "$Falert";}
			print Sout "$Falert";
			$Ealert .= "$Falert";

			close(infile);
			close(Pout);
			close(Sout);
			chmod 0777, "$VDHLOGfile";

			### Move file to the DONE directory locally
			if (!$T) {`mv -f $dir1/$FILEname $dir2/$FILEname`;}
			if (!$T) {`mv -f $dir2/$FILEname$pipe $dir3/$FILEname$pipe`;}

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

	$mailsubject = "VICIDIAL LEAD FILE PREPROCESS $pulldate0";

	  %mail = ( To      => "$email_list",
							From    => "$email_sender",
							Subject => "$mailsubject",
							Message => "VICIDIAL LEAD FILE PREPROCESS $pulldate0\n\n$Ealert\n"
					   );
			sendmail(%mail) or die $mail::Sendmail::error;
		   if ($q < 1) {print "ok. log says:\n", $mail::sendmail::log;}  ### print mail log for status
	}

exit;





##### SUBROUTINES #####

sub cid_state 
	{
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
	}


sub cid_state_areacode
	{
	### look up the custom CID to use for this areacode's state
	$USarea = 	substr($phone_number, 0, 3);
	$state='';

	$stmtA = "select state from vicidial_phone_codes where areacode='$USarea' and country_code='1';";
	$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
	$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
	$sthArows=$sthA->rows;
		if($DBX){print STDERR "\n$sthArows|$stmtA|\n";}
	if ($sthArows > 0)
		{
		@aryA = $sthA->fetchrow_array;
		$state = $aryA[0];
		}
	$sthA->finish();

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
	}

