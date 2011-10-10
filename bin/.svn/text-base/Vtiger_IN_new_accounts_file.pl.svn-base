#!/usr/bin/perl
#
# Vtiger_IN_new_accounts_file.pl version 2.2.0   *DBI-version*
#
# DESCRIPTION:
# script lets you insert leads into the vtiger system table from a CSV-formatted
# file that is in the proper format. (for format see --help)
#
# Copyright (C) 2009  Matt Florell <vicidial@gmail.com>    LICENSE: AGPLv2
#
#
# CHANGES
# 90127-1206 - First build
# 90318-0142 - Added vicidialalt import format and importasdeleted option
# 90327-1149 - Added phone-limit option
#

$secX = time();
$MT[0]='';
$Ealert='';
$importasdeleted=0;

($sec,$min,$hour,$mday,$mon,$year,$wday,$yday,$isdst) = localtime(time);
$year = ($year + 1900);
$mon++;
if ($hour < 10) {$hour = "0$hour";}
if ($min < 10) {$min = "0$min";}
if ($sec < 10) {$sec = "0$sec";}
if ($mon < 10) {$mon = "0$mon";}
if ($mday < 10) {$mday = "0$mday";}
$pulldate0 = "$year-$mon-$mday $hour:$min:$sec";
$NOW_TIME = "$year-$mon-$mday $hour:$min:$sec";
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

	if ($args =~ /--help|-h/i)
		{
		print "allowed run time options:\n";
		print "  [-q] = quiet\n";
		print "  [-t] = test\n";
		print "  [--debug] = debug output\n";
		print "  [--format=standard] = ability to define a format, standard is default, formats allowed shown in examples\n";
		print "  [--duplicate-system-check] = checks for the same phone number in the entire system before inserting lead\n";
		print "  [--duplicate-system-siccode] = checks for the same SIC code in the entire system before inserting lead\n";
		print "  [--duplicate-system-website] = checks for the same website in the entire system before inserting lead\n";
		print "  [--import-as-deleted] = imports the accounts as deleted in vtiger_crmentity\n";
		print "  [--phone-limit=X] = limit phone and fax numbers to X digits\n";
		print "  [--ftp-pull] = grabs lead files from a remote FTP server, uses REPORTS FTP login information\n";
		print "  [--ftp-dir=leads_in] = remote FTP server directory to grab files from, should have a DONE sub-directory\n";
		print "  [--email-list=test@test.com:test2@test.com] = send email results for each file to these addresses\n";
		print "  [--email-sender=vicidial@localhost] = sender for the email results\n";
		print "  [-h] = this help screen\n\n";
		print "\n";
		print "This script takes in account CSV files in the following order when they are placed in the $PATHhome/VTIGER_IN directory to be imported into the vtiger system (examples):\n\n";
		print "standard:\n";
		print "employees,ticker_symbol,sic_code,revenue,account_name,address,po_box,city,state,post_code,phone_number,ownership,fax,email,other_phone,other_email,website\n";
		print "4,C03IMM001,39207955,2,ST MARK CHURCH,1710 W COLLEGE AVE,,CHICAGO,IL,61555,3095551111,PAUL SMITH,3095551212,TEST@VERIZON.NET,7775553333,test@1034.com,bigsite.com\n\n";
		print "vicidialalt:\n";
		print "employees,ticker_symbol,web1,revenue,account_name,address,po_box,city,state,post_code,phone_number,ownership,fax,email,other_phone,other_email,website,emailoptout,notify_owner\n";
		print "4,C03IMM001,12345,2,ST MARK CHURCH,1710 W COLLEGE AVE,,CHICAGO,IL,61555,3095551111,PAUL SMITH,3095551212,TEST@VERIZON.NET,7775553333,test@1034.com,bigsite.com,1,0\n\n";
		print "minicsv:\n";
		print "business,contact,phone_number,address1,city,state,postal_code\n";
		print "\"CHURCH\",\"Bob Smith\",\"3525556601\",\"105 Fifth St\",\"Steinhatchee\",\"FL\",\"32359\"\n\n";

		exit;
		}
	else
		{
		if ($args =~ /-debug/i)
			{
			$DB=1;
			print "\n----- DEBUGGING -----\n\n";
			}
		if ($args =~ /-debugX/i)
			{
			$DBX=1;
			print "\n----- SUPER-DUPER DEBUGGING -----\n\n";
			}
		else {$DBX=0;}

		if ($args =~ /-q/i)
			{
			$q=1;
			}
		if ($args =~ /-t/i)
			{
			$T=1;
			$TEST=1;
			print "\n----- TESTING -----\n\n";
			}

		if ($args =~ /-format=/i)
			{
			@data_in = split(/-format=/,$args);
				$format = $data_in[1];
				$format =~ s/ .*//gi;
			print "\n----- FORMAT OVERRIDE: $format -----\n\n";
			}
		else
			{$format = 'standard';}

		if ($args =~ /-phone-limit=/i)
			{
			@data_in = split(/-phone-limit=/,$args);
				$phone_limit = $data_in[1];
				$phone_limit =~ s/ .*//gi;
			print "\n----- PHONE LIMIT: $phone_limit -----\n\n";
			}
		else
			{$phone_limit = 0;}

		if ($args =~ /-duplicate-system-check/i)
			{
			$dupchecksys=1;
			print "\n----- DUPLICATE SYSTEM CHECK PHONE -----\n\n";
			}
		if ($args =~ /-duplicate-system-siccode/i)
			{
			$dupchecksic=1;
			print "\n----- DUPLICATE SYSTEM CHECK SICCODE -----\n\n";
			}
		if ($args =~ /-duplicate-system-website/i)
			{
			$dupcheckweb=1;
			print "\n----- DUPLICATE SYSTEM CHECK WEBSITE -----\n\n";
			}
		if ($args =~ /-import-as-deleted/i)
			{
			$importasdeleted=1;
			print "\n----- IMPORT ACCOUNTS AS DELETED -----\n\n";
			}
		if ($args =~ /-ftp-pull/i)
			{
			$ftp_pull=1;
			print "\n----- FTP LEAD FILE PULL -----\n\n";
			}
		if ($args =~ /--ftp-dir=/i)
			{
			@data_in = split(/--ftp-dir=/,$args);
				$ftp_dir = $data_in[1];
				$ftp_dir =~ s/ .*//gi;
			print "\n----- REMOTE FTP DIRECTORY: $ftp_dir -----\n\n";
			}
		else
			{$ftp_dir = '';}

		if ($args =~ /--email-list=/i)
			{
			@data_in = split(/--email-list=/,$args);
				$email_list = $data_in[1];
				$email_list =~ s/ .*//gi;
				$email_list =~ s/:/,/gi;
			print "\n----- EMAIL NOTIFICATION: $email_list -----\n\n";
			}
		else
			{$email_list = '';}

		if ($args =~ /--email-sender=/i)
			{
			@data_in = split(/--email-sender=/,$args);
				$email_sender = $data_in[1];
				$email_sender =~ s/ .*//gi;
				$email_sender =~ s/:/,/gi;
			print "\n----- EMAIL NOTIFICATION SENDER: $email_sender -----\n\n";
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
if ($format !~ /minicsv|vicidialalt|standard/)
	{$format='standard';}
### end parsing run-time options ###

if ($q < 1)
	{
	print "\n\n\n\n\n\n\n\n\n\n\n\n-- Vtiger_IN_new_accounts_file.pl --\n\n";
	print "This program is designed to take a CSV delimited file and import it into the Vtiger system. \n\n";
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

$suf = '.txt';
$people_packages_id_update='';
$dir1 = "$PATHhome/VTIGER_IN";
$dir2 = "$PATHhome/VTIGER_IN/DONE";

	if($DBX){print STDERR "\nVTIGER_IN directory: |$dir1|\n";}

if ($ftp_pull > 0)
	{
	$i=0;
	use Net::FTP;

	$files_copied_count=0;

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

			if (length($FILES[$i]) > 4)
				{
				$GOODfname = $FILES[$i];
				$FILES[$i] =~ s/ /_/gi;
				$FILES[$i] =~ s/\(|\)|\||\\|\/|\'|\"|//gi;
				$ftp->rename("$ftp_dir/$GOODfname","$ftp_dir/$FILES[$i]");
				$FILEname = $FILES[$i];
				$ftp->get("$ftp_dir/$FILES[$i]", "$dir1/$FILES[$i]");

				if (!$TEST) {$ftp->rename("$ftp_dir/$FILES[$i]", "$ftp_dir/DONE/$FILES[$i]");}
				if ($DB > 0) {print "FTP FILE COPIED: $FILES[$i]\n";}
				$files_copied_count++;
				}
			}
		$i++;
		}
	if (!$q) {print "$ftp_dir - $VARREPORT_host - $#FILES - $files_copied_count\n";}
	}


$i=0;
@FILES=@MT;
opendir(FILE, "$dir1/");
@FILES = readdir(FILE);

foreach(@FILES)
   {
	$size1 = 0;
	$size2 = 0;
	$person_id_delete = '';
	$transaction_id_delete = '';

	if (length($FILES[$i]) > 4)
		{

		$size1 = (-s "$dir1/$FILES[$i]");
		if (!$q) {print "$FILES[$i] $size1\n";}
		sleep(2);
		$size2 = (-s "$dir1/$FILES[$i]");
		if (!$q) {print "$FILES[$i] $size2\n\n";}


		if ( ($FILES[$i] !~ /^TRANSFERRED/i) && ($size1 eq $size2) && (length($FILES[$i]) > 4))
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

			$a=0;	### each line of input file counter ###
			$b=0;	### status of 'APPROVED' counter ###
			$c=0;	### status of 'DECLINED' counter ###
			$d=0;	### status of 'REFERRED' counter ###
			$e=0;	### status of 'ERROR' counter ###
			$f=0;	### number of 'DUPLICATE' counter ###
			$g=0;	### number of leads with multi-alt-entries

			$multi_insert_counter=0;
			$multistmt='';

		#if ($DB)
			while (<infile>)
			{
			@m=@MT;
		#		print "$a| $number\n";
				$number = $_;
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
				$number =~ s/\"//gi;
				$number =~ s/,/\|/gi;
			@m = split(/\|/, $number);

	$format_set=0;

	# This is the format for the minicsv lead files
	# business,contact,phone_number,address1,city,state,postal_code
	# "CHURCH","Bob Smith","3525556601","105 Fifth St","Steinhatchee","FL","32359"
			if ( ($format =~ /minicsv/) && ($format_set < 1) )
				{
				$employees =			'1';
				$ticker_symbol =		'';
				$sic_code =				'';
				$revenue =				'';
				$account_name =			$m[0];		chomp($account_name);
					if (length($account_name)<1) {$account_name='NONE';}
				$address =				$m[3];		chomp($address);
				$po_box =				'';
				$city =					$m[4];		chomp($city);
				$state =				$m[5];		chomp($state);
				$post_code =			$m[6];		chomp($post_code);
				$phone_number =			$m[2];		chomp($phone_number);	$phone_number =~ s/\D//gi;
					if ($phone_limit > 6) {while (length($phone_number)>$phone_limit) {chop($phone_number);} }
				$ownership =			$m[1];		chomp($ownership);
				$other_phone =			'';
					if ($phone_limit > 6) {while (length($other_phone)>$phone_limit) {chop($other_phone);} }
				$email =				'';
				$fax =					'';
					if ($phone_limit > 6) {while (length($fax)>$phone_limit) {chop($fax);} }
				$other_email =			'';
				$website =				'';
				$emailoptout =			'0';
				$notify_owner =			'0';
				$country =				'USA';

				$format_set++;
				}

	# This is the format for the vicidialalt(VICIDIAL alternate) lead files
	# employees,ticker_symbol,web1,revenue,account_name,address,po_box,city,state,post_code,phone_number,ownership,fax,email,other_phone,other_email,website,emailoptout,notify_owner
	# 4,C03IMM001,12345,2,ST MARK CHURCH,1710 W COLLEGE AVE,,CHICAGO,IL,61555,3095551111,PAUL SMITH,3095551212,TEST@VERIZON.NET,7775553333,test@1034.com,bigsite.com,1,0
	# sic_code is left blank because it will be used by vicidial to put the last call dispo status in
			if ( ($format =~ /vicidialalt/) && ($format_set < 1) )
				{
				$employees =			$m[0];		chomp($employees);		$employees =~ s/\D//gi;
				$ticker_symbol =		$m[1];		chomp($ticker_symbol);
				$sic_code =				'';
				$web1 =					$m[2];		chomp($web1);
				$revenue =				$m[3];		chomp($revenue);		$revenue =~ s/\D//gi;
				$account_name =			$m[4];		chomp($account_name);
					if (length($account_name)<1) {$account_name='NONE';}
				$address =				$m[5];		chomp($address);
				$po_box =				$m[6];		chomp($po_box);
				$city =					$m[7];		chomp($city);
				$state =				$m[8];		chomp($state);
				$post_code =			$m[9];		chomp($post_code);
				$phone_number =			$m[10];		chomp($phone_number);	$phone_number =~ s/\D//gi;
					if ($phone_limit > 6) {while (length($phone_number)>$phone_limit) {chop($phone_number);} }
				$ownership =			$m[11];		chomp($ownership);
				$fax =					$m[12];		chomp($fax);
					if ($phone_limit > 6) {while (length($fax)>$phone_limit) {chop($fax);} }
				$email =				$m[13];		chomp($email);
				$other_phone =			$m[14];		chomp($other_phone);
					if ($phone_limit > 6) {while (length($other_phone)>$phone_limit) {chop($other_phone);} }
				$other_email =			$m[15];		chomp($other_email);
				$website =				$m[16];		chomp($website);
					if (length($website)<3) {$website="$web1-$revenue";}
				$emailoptout =			$m[17];		chomp($emailoptout);	$emailoptout =~ s/\D//gi;
					if (length($emailoptout)<1) {$emailoptout="0";}
				$notify_owner =			$m[18];		chomp($notify_owner);	$notify_owner =~ s/\D//gi;
					if (length($notify_owner)<1) {$notify_owner="0";}
				$country =				'USA';

				$format_set++;
				}

	# This is the format for the standard lead files
	# employees,ticker_symbol,sic_code,revenue,account_name,address,po_box,city,state,post_code,phone_number,ownership,fax,email,other_phone,other_email,website
	# 4,C03IMM001,39207955,2,ST MARK CHURCH,1710 W COLLEGE AVE,,CHICAGO,IL,61555,3095551111,PAUL SMITH,3095551212,TEST@VERIZON.NET,7775553333,test@1034.com,bigsite.com
			if ( ($format =~ /standard/) || ($format_set < 1) )
				{
				$employees =			$m[0];		chomp($employees);		$employees =~ s/\D//gi;
				$ticker_symbol =		$m[1];		chomp($ticker_symbol);
				$sic_code =				$m[2];		chomp($sic_code);
				$revenue =				$m[3];		chomp($revenue);		$revenue =~ s/\D//gi;
				$account_name =			$m[4];		chomp($account_name);
					if (length($account_name)<1) {$account_name='NONE';}
				$address =				$m[5];		chomp($address);
				$po_box =				$m[6];		chomp($po_box);
				$city =					$m[7];		chomp($city);
				$state =				$m[8];		chomp($state);
				$post_code =			$m[9];		chomp($post_code);
				$phone_number =			$m[10];		chomp($phone_number);	$phone_number =~ s/\D//gi;
					if ($phone_limit > 6) {while (length($phone_number)>$phone_limit) {chop($phone_number);} }
				$ownership =			$m[11];		chomp($ownership);
				$fax =					$m[12];		chomp($fax);
					if ($phone_limit > 6) {while (length($fax)>$phone_limit) {chop($fax);} }
				$email =				$m[13];		chomp($email);
				$other_phone =			$m[14];		chomp($other_phone);
					if ($phone_limit > 6) {while (length($other_phone)>$phone_limit) {chop($other_phone);} }
				$other_email =			$m[15];		chomp($other_email);
				$website =				$m[16];		chomp($website);
					if (length($website)<3) {$website="$sic_code-$revenue";}
				$emailoptout =			'0';
				$notify_owner =			'0';
				$country =				'USA';

				$format_set++;
				}

			if ($DBX) {print "$a|$phone_number|$account_name|$website\n";}

			$dup_lead=0;
			##### Check for duplicate phone numbers in vtiger_account table #####
			if ($dupchecksys > 0)
				{
				$stmtB="SELECT count(*) from vtiger_account where phone='$phone_number';";
					if($DBX){print STDERR "\n|$stmtB|\n";}
				$sthB = $dbhB->prepare($stmtB) or die "preparing: ",$dbhB->errstr;
				$sthB->execute or die "executing: $stmtB ", $dbhB->errstr;
				$sthBrows=$sthB->rows;
				if ($sthBrows > 0)
					{
					@aryB = $sthB->fetchrow_array;
					$dup_lead = $aryB[0];
					}
				$sthB->finish();
				}
			##### Check for duplicate sic code acct ID in vtiger_account table #####
			if ($dupchecksic > 0)
				{
				$stmtB="SELECT count(*) from vtiger_account where siccode='$sic_code';";
					if($DBX){print STDERR "\n|$stmtB|\n";}
				$sthB = $dbhB->prepare($stmtB) or die "preparing: ",$dbhB->errstr;
				$sthB->execute or die "executing: $stmtB ", $dbhB->errstr;
				$sthBrows=$sthB->rows;
				if ($sthBrows > 0)
					{
					@aryB = $sthB->fetchrow_array;
					$dup_lead = $aryB[0];
					}
				$sthB->finish();
				}
			##### Check for duplicate website in vtiger_account table #####
			if ($dupcheckweb > 0)
				{
				$stmtB="SELECT count(*) from vtiger_account where website='$website';";
					if($DBX){print STDERR "\n|$stmtB|\n";}
				$sthB = $dbhB->prepare($stmtB) or die "preparing: ",$dbhB->errstr;
				$sthB->execute or die "executing: $stmtB ", $dbhB->errstr;
				$sthBrows=$sthB->rows;
				if ($sthBrows > 0)
					{
					@aryB = $sthB->fetchrow_array;
					$dup_lead = $aryB[0];
					}
				$sthB->finish();
				}

			if ( (length($phone_number)>6) && ($dup_lead < 1) )
				{
				$user='6666';

				#Get logged in user ID
				$stmtB="SELECT id from vtiger_users where user_name='$user';";
					if($DBX){print STDERR "\n|$stmtA|\n";}
				$sthB = $dbhB->prepare($stmtB) or die "preparing: ",$dbhB->errstr;
				$sthB->execute or die "executing: $stmtB ", $dbhB->errstr;
				$sthBrows=$sthB->rows;
				if ($sthBrows > 0)
					{
					@aryB = $sthB->fetchrow_array;
					$user_id = $aryB[0];
					}
				$sthB->finish();
				
				# Get current ID from vtiger_crmentity_seq
				$stmtB="SELECT id from vtiger_crmentity_seq;";
					if($DBX){print STDERR "\n|$stmtB|\n";}
				$sthB = $dbhB->prepare($stmtB) or die "preparing: ",$dbhB->errstr;
				$sthB->execute or die "executing: $stmtB ", $dbhB->errstr;
				$sthBrows=$sthB->rows;
				if ($sthBrows > 0)
					{
					@aryB = $sthB->fetchrow_array;
					$crm_id = ($aryB[0] + 1);
					}
				$sthB->finish();

				### update the crm ID ###
				$stmtB = "UPDATE vtiger_crmentity_seq SET id = '$crm_id';";
					if (!$T) {$affected_rows = $dbhB->do($stmtB); } #  or die  "Couldn't execute query: |$stmtB|\n";
					if($DB){print STDERR "\n|$affected_rows|$stmtB|\n";}

				### insert record into crmentity table ###
				$stmtB = "INSERT INTO vtiger_crmentity SET crmid='$crm_id',smcreatorid='$user_id',smownerid='$user_id',modifiedby='$user_id',setype='Accounts',description='',createdtime='$NOW_TIME',modifiedtime='$NOW_TIME', viewedtime='$NOW_TIME', deleted='$importasdeleted';";
					if (!$T) {$affected_rows = $dbhB->do($stmtB); } #  or die  "Couldn't execute query: |$stmtB|\n";
					if($DB){print STDERR "\n|$affected_rows|$stmtB|\n";}

				### insert record into vtiger_account table ###
				$stmtB = "INSERT INTO vtiger_account SET accountid='$crm_id',accountname='$account_name',account_type='Customer',industry='--None--',annualrevenue='$revenue',rating='--None--',ownership='$ownership',siccode='$sic_code',tickersymbol='$ticker_symbol',phone='$phone_number',otherphone='$other_phone',email1='$email',email2='$other_email',website='$website',fax='$fax',employees='$employees',emailoptout='$emailoptout',notify_owner='$notify_owner';";
					if (!$T) {$affected_rows = $dbhB->do($stmtB); } #  or die  "Couldn't execute query: |$stmtB|\n";
					if($DB){print STDERR "\n|$affected_rows|$stmtB|\n";}

				### insert record into vtiger_accountbillads table ###
				$stmtB = "INSERT INTO vtiger_accountbillads SET accountaddressid='$crm_id',bill_city='$city',bill_code='$post_code',bill_country='$country',bill_state='$state',bill_street='$address',bill_pobox='$po_box';";
					if (!$T) {$affected_rows = $dbhB->do($stmtB); } #  or die  "Couldn't execute query: |$stmtB|\n";
					if($DB){print STDERR "\n|$affected_rows|$stmtB|\n";}

				### insert record into vtiger_accountscf table ###
				$stmtB = "INSERT INTO vtiger_accountscf SET accountid='$crm_id';";
					if (!$T) {$affected_rows = $dbhB->do($stmtB); } #  or die  "Couldn't execute query: |$stmtB|\n";
					if($DB){print STDERR "\n|$affected_rows|$stmtB|\n";}

				### insert record into vtiger_accountshipads table ###
				$stmtB = "INSERT INTO vtiger_accountshipads SET accountaddressid='$crm_id',ship_city='',ship_code='',ship_country='',ship_state='',ship_street='',ship_pobox='';";
					if (!$T) {$affected_rows = $dbhB->do($stmtB); } #  or die  "Couldn't execute query: |$stmtB|\n";
					if($DB){print STDERR "\n|$affected_rows|$stmtB|\n";}

				### insert record into vtiger_tracker table ###
				$stmtB = "INSERT INTO vtiger_tracker SET user_id='$user_id',module_name='Accounts',item_id='$crm_id',item_summary='$account_name';";
					if (!$T) {$affected_rows = $dbhB->do($stmtB); } #  or die  "Couldn't execute query: |$stmtB|\n";
					if($DB){print STDERR "\n|$affected_rows|$stmtB|\n";}

				$b++;
				}
			else
				{
				if ($dup_lead > 0)
					{print "DUPLICATE: $phone_number|$sic_code|$website|$a\n";   $f++;}
				else
					{print "BAD Phone Number: $phone_number|$vendor_id|$a\n";   $e++;}
				}
			
			$a++;

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



			### open the stats out file for writing ###
			open(Sout, ">>$VDHLOGfile")
					|| die "Can't open $VDHLOGfile: $!\n";


			### close file handler and DB connections ###
			$Falert  = "\n\nTOTALS FOR $FILEname:\n";
			$Falert .= "Lines in file:      $a\n";
			$Falert .= "INSERTED:           $b\n";
			$Falert .= "INSERT STATEMENTS:  $c\n";
			$Falert .= "ERROR:              $e\n";
			$Falert .= "MULTI-ALT-PHONE:    $g\n";
			if ($f > 0)
				{$Falert .= "DUPLICATES:         $f\n";}

			print "$Falert";
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
	print "Sending email: $email_list\n";

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
		   print "ok. log says:\n", $mail::sendmail::log;  ### print mail log for status
	}

exit;

