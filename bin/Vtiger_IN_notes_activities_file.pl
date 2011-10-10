#!/usr/bin/perl
#
# Vtiger_IN_notes_activities_file.pl version 2.2.0
#
# DESCRIPTION:
# script lets you insert notes and activities into the vtiger system from a 
# CSV-formatted file that is in the proper format. (for format see --help)
#
# NOTE - written for a specific client use, you will probably need to modify
#        this script to be able to use it for your purposes
#
# Copyright (C) 2009  Matt Florell <vicidial@gmail.com>    LICENSE: AGPLv2
#
#
# CHANGES
# 90327-1148 - First build
# 90623-0605 - Added duplicate check
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


if (!$VDHLOGfile) {$VDHLOGfile = "$PATHlogs/addnotes.$year-$mon-$mday";}

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
		print "  [--duplicate-check] = check whether the record exists before attempting to insert it\n";
		print "  [--format=notes] = select whether you are importing notes or activities\n";
		print "  [--ftp-pull] = grabs lead files from a remote FTP server, uses REPORTS FTP login information\n";
		print "  [--ftp-dir=leads_in] = remote FTP server directory to grab files from, should have a DONE sub-directory\n";
		print "  [--email-list=test@test.com:test2@test.com] = send email results for each file to these addresses\n";
		print "  [--email-sender=vicidial@localhost] = sender for the email results\n";
		print "  [-h] = this help screen\n\n";
		print "\n";
		print "This script takes in notes or activities CSV files in the following order when they are placed in the $PATHhome/VTIGER_IN directory to be imported into the vtiger system (examples):\n\n";
		print "notes:\n";
		print "account,sequence,status,date,notes1,notes2\n";
		print "39207955,2,ANSWERING MACHINE,01/30/09,SPOKE WITH DEVONNE,SHE PLACED AN ORDER ONLINE THIS MORNING WILL CALL HER BACK IN TWO WEEKS ON FEB 19\n\n";
		print "activity:\n";
		print "account,sequence,date,time,session_length,call_length,status\n";
		print "39207955,2,01/30/09,14:47:42,205,116,ANSWERING MACHINE\n\n";

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

		if ($args =~ /--duplicate-check/i)
			{
			$dup_check=1;
			print "\n----- DUPLICATE CHECK -----\n\n";
			}
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
if ($format !~ /notes|activities/)
	{$format='notes';}
### end parsing run-time options ###

if ($q < 1)
	{
	print "\n\n\n\n\n\n\n\n\n\n\n\n-- Vtiger_IN_notes_activities.pl --\n\n";
	print "This program is designed to take a CSV delimited file and import it as notes or activities into the Vtiger system. \n\n";
	}

$i=0;
$US = '_';
$phone_list = '|';
$DASH='-';

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
			$f=0;	### number of 'NOTFOUND' counter ###
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

	### This is the format for the activities files
	# account,sequence,date,time,session_length,call_length,status
	# 39207955,2,01/30/09,14:47:42,205,116,ANSWERING MACHINE
			if ( ($format =~ /activities/) && ($format_set < 1) )
				{
				$format='activities';
				$web1 =					$m[0];		chomp($web1);			$web1 =~ s/\D//gi;
				$revenue =				$m[1];		chomp($revenue);		$revenue =~ s/\D//gi;
				$date =					$m[2];		chomp($date);
				$time =					$m[3];		chomp($time);
				$session_length =		$m[4];		chomp($session_length);
				$call_length =			$m[5];		chomp($call_length);
				$status =				$m[6];		chomp($status);
				$website="$web1$DASH$revenue";
				@date_array = split(/\//,$date);
				$YYYY =		$date_array[2];	$YYYY = ($YYYY + 2000);
				$MM =		$date_array[0];
				$DD =		$date_array[1];
				$date = "$YYYY-$MM-$DD";
				if ($session_length > 300)
					{$session_length = int($session_length / 60);}
				else
					{$session_length = '5';}
				@time_array = split(/:/,$time);
				$HH =		($time_array[0] + 0);
				$MM =		($time_array[1] + $session_length);
				$SS =		$time_array[2];
				if ($MM > 59)
					{$MM = ($MM - 60); $HH = ($HH + 1);}
				if ($HH < 10) {$HH = "0$HH";}
				if ($MM < 10) {$MM = "0$MM";}
				$end_time = "$HH:$MM:$SS";

				$format_set++;
				}

	### This is the format for the notes files
	# account,sequence,status,date,notes1,notes2
	# 39207955,2,ANSWERING MACHINE,01/30/09,SPOKE WITH DEVONNE,SHE PLACED AN ORDER ONLINE THIS MORNING WILL CALL HER BACK IN TWO WEEKS ON FEB 19
			if ( ($format =~ /notes/) || ($format_set < 1) )
				{
				$format='notes';
				$web1 =					$m[0];		chomp($web1);			$web1 =~ s/\D//gi;
				$revenue =				$m[1];		chomp($revenue);		$revenue =~ s/\D//gi;
				$status =				$m[2];		chomp($status);
				$date =					$m[3];		chomp($date);
				$notes =				"$m[4] $m[5]";		chomp($notes);
				$website="$web1$DASH$revenue";
				@date_array = split(/\//,$date);
				$YYYY =		$date_array[2];	$YYYY = ($YYYY + 2000);
				$MM =		$date_array[0];
				$DD =		$date_array[1];
				$date = "$YYYY-$MM-$DD";
				$time = "14:00";

				$format_set++;
				}

			if ($DBX) {print "$a|$web1|$revenue|$website|     $number\n";}



			if (length($web1)>0)
				{
				# Get current vendor ID from vtiger_account
				$stmtB="SELECT accountid from vtiger_account where website='$website';";
					if($DBX){print STDERR "\n|$stmtB|\n";}
				$sthB = $dbhB->prepare($stmtB) or die "preparing: ",$dbhB->errstr;
				$sthB->execute or die "executing: $stmtB ", $dbhB->errstr;
				$sthBrows=$sthB->rows;
				if ($sthBrows < 1)
					{
					$sthB->finish();
					
					print "BAD Account Not Found: $website|$a\n";   $f++;
					}
				else
					{
					@aryB = $sthB->fetchrow_array;
					$vendor_id = $aryB[0];
					$sthB->finish();


					$user='6666';

					##### BEGIN Duplicate Check #####
					$duplicate=0;
					if ($dup_check)
						{
						if ($format =~ /activities/)
							{
							$first_duplicate=0;
							# Check for duplicate activities entry
							$stmtB="SELECT count(*) from vtiger_activity where subject='Old Call: $status' and activitytype='Call' and date_start='$date' and time_start='$time' and time_end='$end_time';";
								if($DBX){print STDERR "\n|$stmtA|\n";}
							$sthB = $dbhB->prepare($stmtB) or die "preparing: ",$dbhB->errstr;
							$sthB->execute or die "executing: $stmtB ", $dbhB->errstr;
							$sthBrows=$sthB->rows;
							if ($sthBrows > 0)
								{
								@aryB = $sthB->fetchrow_array;
								$first_duplicate = $aryB[0];
								}
							$sthB->finish();

							if ($first_duplicate > 0)
								{
								$stmtB="SELECT count(*) from vtiger_seactivityrel vs, vtiger_activity va where vs.crmid='$vendor_id' and vs.activityid=va.activityid and subject='Old Call: $status' and activitytype='Call' and date_start='$date' and time_start='$time' and time_end='$end_time';";
									if($DBX){print STDERR "\n|$stmtA|\n";}
								$sthB = $dbhB->prepare($stmtB) or die "preparing: ",$dbhB->errstr;
								$sthB->execute or die "executing: $stmtB ", $dbhB->errstr;
								$sthBrows=$sthB->rows;
								if ($sthBrows > 0)
									{
									@aryB = $sthB->fetchrow_array;
									$duplicate = $aryB[0];
									}
								$sthB->finish();
								}
							}
						else  # notes dup check
							{
							$first_duplicate=0;
							# Check for duplicate activities entry
							$stmtB="SELECT count(*) from vtiger_crmentity where setype='Notes' and description='Old Notes: $status' and createdtime='$date $time';";
								if($DBX){print STDERR "\n|$stmtA|\n";}
							$sthB = $dbhB->prepare($stmtB) or die "preparing: ",$dbhB->errstr;
							$sthB->execute or die "executing: $stmtB ", $dbhB->errstr;
							$sthBrows=$sthB->rows;
							if ($sthBrows > 0)
								{
								@aryB = $sthB->fetchrow_array;
								$first_duplicate = $aryB[0];
								}
							$sthB->finish();

							if ($first_duplicate > 0)
								{
								$stmtB="SELECT count(*) from vtiger_senotesrel vn, vtiger_crmentity vc where vn.crmid='$vendor_id' and vn.notesid=vc.crmid and subject='Old Call: $status' and activitytype='Call' and date_start='$date' and time_start='$time' and time_end='$end_time';";
									if($DBX){print STDERR "\n|$stmtA|\n";}
								$sthB = $dbhB->prepare($stmtB) or die "preparing: ",$dbhB->errstr;
								$sthB->execute or die "executing: $stmtB ", $dbhB->errstr;
								$sthBrows=$sthB->rows;
								if ($sthBrows > 0)
									{
									@aryB = $sthB->fetchrow_array;
									$duplicate = $aryB[0];
									}
								$sthB->finish();
								}
							}
						}
					##### END Duplicate Check #####

					if ($duplicate < 1)
						{
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

						if ($format =~ /activities/)
							{
							### insert record into vtiger_salesmanactivityrel table ###
							$stmtB = "INSERT INTO vtiger_salesmanactivityrel SET smid='$user_id',activityid='$crm_id';";
								if (!$T) {$affected_rows = $dbhB->do($stmtB); } #  or die  "Couldn't execute query: |$stmtB|\n";
								if($DB){print STDERR "\n|$affected_rows|$stmtB|\n";}

							### insert record into vtiger_seactivityrel table ###
							$stmtB = "INSERT INTO vtiger_seactivityrel SET crmid='$vendor_id',activityid='$crm_id';";
								if (!$T) {$affected_rows = $dbhB->do($stmtB); } #  or die  "Couldn't execute query: |$stmtB|\n";
								if($DB){print STDERR "\n|$affected_rows|$stmtB|\n";}					

							### insert record into crmentity table ###
							$stmtB = "INSERT INTO vtiger_crmentity SET crmid='$crm_id',smcreatorid='$user_id',smownerid='$user_id',modifiedby='$user_id',setype='Calendar',description='Old Call: $status',createdtime='$date $time',modifiedtime='$date $time', viewedtime='$date $time';";
								if (!$T) {$affected_rows = $dbhB->do($stmtB); } #  or die  "Couldn't execute query: |$stmtB|\n";
								if($DB){print STDERR "\n|$affected_rows|$stmtB|\n";}

							### insert record into vtiger_activity table ###
							$stmtB = "INSERT INTO vtiger_activity SET activityid='$crm_id',subject='Old Call: $status',activitytype='Call',date_start='$date',due_date='$date',time_start='$time',time_end='$end_time',sendnotification='0',duration_hours='0',duration_minutes='$session_length',status='',eventstatus='Held',priority='Medium',location='VICIDIAL User $user',notime='0',visibility='Public',recurringtype='--None--';";
								if (!$T) {$affected_rows = $dbhB->do($stmtB); } #  or die  "Couldn't execute query: |$stmtB|\n";
								if($DB){print STDERR "\n|$affected_rows|$stmtB|\n";}
							}
						else
							{
							### insert record into crmentity table ###
							$stmtB = "INSERT INTO vtiger_crmentity SET crmid='$crm_id',smcreatorid='$user_id',smownerid='$user_id',modifiedby='$user_id',setype='Notes',description='Old Notes: $status',createdtime='$date $time',modifiedtime='$date $time', viewedtime='$date $time';";
								if (!$T) {$affected_rows = $dbhB->do($stmtB); } #  or die  "Couldn't execute query: |$stmtB|\n";
								if($DB){print STDERR "\n|$affected_rows|$stmtB|\n";}

							### insert record into vtiger_notes table ###
							$stmtB = "INSERT INTO vtiger_notes SET notesid='$crm_id',title='Old Notes: $status',notecontent='$notes';";
								if (!$T) {$affected_rows = $dbhB->do($stmtB); } #  or die  "Couldn't execute query: |$stmtB|\n";
								if($DB){print STDERR "\n|$affected_rows|$stmtB|\n";}

							### insert record into vtiger_senotesrel table ###
							$stmtB = "INSERT INTO vtiger_senotesrel SET crmid='$vendor_id',notesid='$crm_id';";
								if (!$T) {$affected_rows = $dbhB->do($stmtB); } #  or die  "Couldn't execute query: |$stmtB|\n";
								if($DB){print STDERR "\n|$affected_rows|$stmtB|\n";}
							}
						$b++;
						}
					else
						{
						print "BAD duplicate: $web1|$revenue|$status|$date $time|$a\n";   $h++;
						}
					}
				}
			else
				{
				print "BAD Account Number: $web1|$revenue|$a\n";   $e++;
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
			if ($a =~ /000$/i) {print "$a|$b|$c|$d|$e|$f|$g|$h|$phone_number|\n";}
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
			$Falert .= "NOT FOUND:          $f\n";
			$Falert .= "MULTI-ALT-PHONE:    $g\n";
			$Falert .= "DUPLICATE:          $h\n";

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

