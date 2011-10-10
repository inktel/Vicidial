#!/usr/bin/perl
#
# VICIDIAL_UPDATE_leads_status_file.pl   version 2.2.0
#
# DESCRIPTION:
# script lets you update vicidial_list records from a PIPE-delimited
# file that is in the proper format. (for format see --help)
#
# NOTE: add to DNC feature is not implemented yet
#
# /usr/share/astguiclient/VICIDIAL_UPDATE_leads_status_file.pl --quiet --delete-from-hopper
#
# Copyright (C) 2009  Matt Florell <vicidial@gmail.com>    LICENSE: AGPLv2
#
#
# CHANGES
# 90627-1402 - First Build
# 90907-0635 - Added list_id to table and allowed for multiple matches in results
# 100427-0604 - Added purge callbacks option
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
$pulldate0 = "$year-$mon-$mday $hour:$min:$sec";
$SQL_date = "$year-$mon-$mday $hour:$min:$sec";
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


if (!$VDHLOGfile) {$VDHLOGfile = "$PATHlogs/updateleads.$year-$mon-$mday";}

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
		print "  [--quiet] = quiet\n";
		print "  [--test] = test\n";
		print "  [--debug] = debug output\n";
		print "  [--delete-from-hopper] = delete lead from hopper if present\n";
		print "  [--delete-from-callbacks] = delete lead from scheduled callbacks if present\n";
		print "  [--format=standard] = ability to define a format, standard is default, formats allowed shown in examples\n";
		print "  [--ftp-pull] = grabs lead files from a remote FTP server, uses REPORTS FTP login information\n";
		print "  [--ftp-dir=leads_in] = remote FTP server directory to grab files from, should have a DONE sub-directory\n";
		print "  [--email-list=test@test.com:test2@test.com] = send email results for each file to these addresses\n";
		print "  [--email-sender=vicidial@localhost] = sender for the email results\n";
		print "  [--help] = this help screen\n\n";
		print "\n";
		print "This script takes in files in the following order when they are placed in the $PATHhome/UPDATE_IN directory to be imported into the vicidial_list table (examples):\n\n";
		print "standard:\n";
		print "vendor_lead_code|new_status|DNC_this_lead\n\n";
		print "leadid:\n";
		print "lead_id|new_status|DNC_this_lead\n\n";

		exit;
		}
	else
		{
		if ($args =~ /-quiet/i)
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
		if ($args =~ /--delete-from-hopper/)
			{
			$delete_from_hopper=1;
			if ($q < 1) {print "\n----- DELETE FROM HOPPER -----\n\n";}
			}
		else {$delete_from_hopper=0;}
		if ($args =~ /--delete-from-callbacks/)
			{
			$delete_from_callbacks=1;
			if ($q < 1) {print "\n----- DELETE FROM CALLBACKS -----\n\n";}
			}
		else {$delete_from_callbacks=0;}

		if ($args =~ /-format=/i)
			{
			@data_in = split(/-format=/,$args);
				$format = $data_in[1];
				$format =~ s/ .*//gi;
			if ($q < 1) {print "\n----- FORMAT OVERRIDE: $format -----\n\n";}
			}
		else
			{$format = 'standard';}
		if ($args =~ /-test/i)
			{
			$T=1;
			$TEST=1;
			if ($q < 1) {print "\n----- TESTING -----\n\n";}
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
	}
### end parsing run-time options ###

if ($q < 1)
	{
	print "\n\n\n\n\n\n\n\n\n\n\n\n-- VICIDIAL_UPDATE_leads_status_file.pl --\n\n";
	print "This program is designed to take a pipe delimited file and update vicidial_list entries in the VICIDIAL system. \n\n";
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
$dir1 = "$PATHhome/UPDATE_IN";
$dir2 = "$PATHhome/UPDATE_IN/DONE";

	if($DBX){print STDERR "\nUPDATE_IN directory: |$dir1|\n";}

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
			$b=0;	### status of 'UPDATED' counter ###
			$c=0;	### status of 'NOT_UPDATED' counter ###
			$d=0;	### status of 'NOT_FOUND' counter ###

			$multi_insert_counter=0;
			$multistmt='';

		#if ($DB)
			while (<infile>)
			{
			$lead_id =			'';
			$vendor_lead_code =	'';
			$old_status =		'';
			$phone_number =		'';
			$old_list_id =		'';
			$Uaffected_rows =	0;
			@m=@MT;
		#		print "$a| $number\n";
				$number = $_;
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

		# This is the format for the leadid update files
		# lead_id|new_status|add_to_DNC
		# "32124","HOLD","N"
			if ( ($format =~ /leadid/) && ($format_set < 1) )
				{
				@name=@MT;
				$vendor_lead_code =		'';
				$lead_id =				$m[0];
				$status =				$m[1];
				$add_to_dnc =			$m[2];

				$lookupSQL = "lead_id='$lead_id'";

				$format_set++;
				}

		# This is the format for the standard update files
		# vendor_lead_code|new_status|add_to_DNC
		# 321654987,HOLD,N
			if ($format_set < 1)
				{
				$vendor_lead_code =		$m[0];
				$lead_id =				'';
				$status =				$m[1];
				$add_to_dnc =			$m[2];

				$lookupSQL = "vendor_lead_code='$vendor_lead_code'";
				}

			if ($DBX) {print "$a|$vendor_lead_code|$lead_id|$status|$add_to_dnc\n";}


			$lead_found=0;
			$stmtA = "SELECT lead_id,vendor_lead_code,status,phone_number,list_id from vicidial_list where $lookupSQL;";
				if($DBX){print STDERR "\n|$stmtA|\n";}
			$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
			$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
			$sthArows=$sthA->rows;
			$h=0;
			if ($sthArows > 0)
				{
				$lead_id =			'';
				$vendor_lead_code =	'';
				$old_status =		'';
				$phone_number =		'';
				$old_list_id =		'';
				}
			while ($sthArows > $h)
				{
				@aryA = $sthA->fetchrow_array;
				$lead_idSQL .=			"'$aryA[0]',";
				$lead_id .=				"$aryA[0],";
				$vendor_lead_code .=	"$aryA[1],";
				$old_status .=			"$aryA[2],";
				$phone_number .=			"$aryA[3],";
				$old_list_id .=			"$aryA[4],";

				$lead_found++;
				$h++;
				}
			$sthA->finish();

			if ($sthArows > 0)
				{
				chop($lead_idSQL);
				chop($lead_id);
				chop($vendor_lead_code);
				chop($old_status);
				chop($phone_number);
				chop($old_list_id);
				}

			if ($lead_found > 0)
				{
				$stmtZ = "UPDATE vicidial_list SET status='$status' where $lookupSQL;";
					if (!$T) {$Uaffected_rows = $dbhA->do($stmtZ); } #  or die  "Couldn't execute query: |$stmtZ|\n";
					if($DB){print STDERR "\n|$affected_rows|$stmtZ|\n";}
				if ($Uaffected_rows > 0)
					{$result = 'UPDATED';   $b++;}
				else
					{$result = 'NO_UPDATE';   $c++;}

				if ($delete_from_hopper > 0)
					{
					$stmtZ = "DELETE from vicidial_hopper where lead_id IN($lead_id);";
						if (!$T) {$Uaffected_rows = $dbhA->do($stmtZ); } #  or die  "Couldn't execute query: |$stmtZ|\n";
						if($DB){print STDERR "\nhopper purge: |$Uaffected_rows|$stmtZ|\n";}
					}
				if ($delete_from_callbacks > 0)
					{
					$stmtZ = "DELETE from vicidial_callbacks where lead_id IN($lead_id);";
						if (!$T) {$Uaffected_rows = $dbhA->do($stmtZ); } #  or die  "Couldn't execute query: |$stmtZ|\n";
						if($DB){print STDERR "\ncallbacks purge: |$Uaffected_rows|$stmtZ|\n";}
					}
				}
			else
				{$result = 'NOT_FOUND';   $d++;}

		#	CREATE TABLE vicidial_list_update_log (
		#	event_date DATETIME,
		#	lead_id INT(9) UNSIGNED,
		#	vendor_id VARCHAR(20),
		#	phone_number VARCHAR(20),
		#	status VARCHAR(6),
		#	old_status VARCHAR(6),
		#	filename VARCHAR(255) default '',
		#	result VARCHAR(20),
		#	result_rows SMALLINT(3) UNSIGNED default '0',
		#	index (event_date)
		#	);

			$stmtZ = "INSERT INTO vicidial_list_update_log SET event_date='$SQL_date',lead_id='$lead_id',vendor_id='$vendor_lead_code',phone_number='$phone_number',status='$status',old_status='$old_status',filename='$FILEname',result='$result',result_rows='$Uaffected_rows',list_id='$old_list_id';";
				if (!$T) {$affected_rows = $dbhA->do($stmtZ); } #  or die  "Couldn't execute query: |$stmtZ|\n";
			#	$alt_phone_id = $dbhA->{'mysql_insertid'};
				if($DB){print STDERR "\n|$affected_rows|$stmtZ|\n";}

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
				if ($a =~ /000$/i) {print "$a|$b|$c|$d|$phone_number|\n";}
				}

			}


			### open the stats out file for writing ###
			open(Sout, ">>$VDHLOGfile")
					|| die "Can't open $VDHLOGfile: $!\n";

			### close file handler and DB connections ###
			$Falert  = "\n\nTOTALS FOR $FILEname:\n";
			$Falert .= "Lines in update file: $a\n";
			$Falert .= "UPDATED:              $b\n";
			$Falert .= "NOT UPDATED:          $c\n";
			$Falert .= "NOT FOUND:            $d\n";

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

	$mailsubject = "VICIDIAL LEAD UPDATE LOAD $pulldate0";

	  %mail = ( To      => "$email_list",
							From    => "$email_sender",
							Subject => "$mailsubject",
							Message => "VICIDIAL LEAD UPDATE LOAD $pulldate0\n\n$Ealert\n"
					   );
			sendmail(%mail) or die $mail::Sendmail::error;
		   if ($q < 1) {print "ok. log says:\n", $mail::sendmail::log;}  ### print mail log for status
	}

exit;

