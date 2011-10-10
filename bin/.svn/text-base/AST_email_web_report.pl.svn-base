#!/usr/bin/perl
#
# AST_email_web_report.pl                version: 2.4
#
# This script is designed to wget a web report and then email it as an attachment
# this script should be run from a vicidial web server.
#
# NOTE: you need to alter the URL to change the report that is run by this script
#
# /usr/share/astguiclient/AST_email_web_report --email-list=test@gmail.com --email-sender=test@test.com
#
# Copyright (C) 2011  Matt Florell <vicidial@gmail.com>    LICENSE: AGPLv2
#
# CHANGES
# 90225-1247 - First version
# 111004-1057 - Added ftp options
#

$txt = '.txt';
$html = '.html';
$US = '_';
$MT[0] = '';


$secX = time();
$time = $secX;
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
$start_date = "$year$mon$mday";
$datestamp = "$year/$mon/$mday $hour:$min";


use Time::Local;

### find epoch of 2AM today
$TWOAMsec = ( ($secX - ($sec + ($min * 60) + ($hour * 3600) ) ) + 7200);
### find epoch of 2AM yesterday
$TWOAMsecY = ($TWOAMsec - 86400);

($Tsec,$Tmin,$Thour,$Tmday,$Tmon,$Tyear,$Twday,$Tyday,$Tisdst) = localtime($TWOAMsecY);
$Tyear = ($Tyear + 1900);
$Tmon++;
if ($Tmon < 10) {$Tmon = "0$Tmon";}
if ($Tmday < 10) {$Tmday = "0$Tmday";}
if ($Thour < 10) {$Thour = "0$Thour";}
if ($Tmin < 10) {$Tmin = "0$Tmin";}
if ($Tsec < 10) {$Tsec = "0$Tsec";}


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
		print "  [--debug] = debugging messages\n";
		print "  [--debugX] = Super debugging messages\n";
		print "  [--email-list=test@test.com:test2@test.com] = send email results to these addresses\n";
		print "  [--email-sender=vicidial@localhost] = sender for the email results\n";
		print "  [--date=YYYY-MM-DD] = date override, can also use 'today' and 'yesterday'\n";
		print "  [--ftp-server=XXXXXXXX] = FTP server to send file to\n";
		print "  [--ftp-login=XXXXXXXX] = FTP user\n";
		print "  [--ftp-pass=XXXXXXXX] = FTP pass\n";
		print "  [--ftp-dir=XXXXXXXX] = remote FTP server directory to post files to\n";
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
		if ($args =~ /--test/i)
			{
			$T=1;   $TEST=1;
			print "\n----- TESTING -----\n\n";
			}
		if ($args =~ /--ftp-server=/i)
			{
			@data_in = split(/--ftp-server=/,$args);
			$VARREPORT_host = $data_in[1];
			$VARREPORT_host =~ s/ .*//gi;
			$VARREPORT_host =~ s/:/,/gi;
			print "\n----- FTP SERVER: $VARREPORT_host -----\n\n";
			}
		else
			{$VARREPORT_host = '';}
		if ($args =~ /--ftp-login=/i)
			{
			@data_in = split(/--ftp-login=/,$args);
			$VARREPORT_user = $data_in[1];
			$VARREPORT_user =~ s/ .*//gi;
			$VARREPORT_user =~ s/:/,/gi;
			print "\n----- FTP LOGIN: $VARREPORT_user -----\n\n";
			}
		else
			{$VARREPORT_user = '';}
		if ($args =~ /--ftp-pass=/i)
			{
			@data_in = split(/--ftp-pass=/,$args);
			$VARREPORT_pass = $data_in[1];
			$VARREPORT_pass =~ s/ .*//gi;
			$VARREPORT_pass =~ s/:/,/gi;
			print "\n----- FTP PASS: <SET> -----\n\n";
			}
		else
			{$VARREPORT_pass = '';}
		if ($args =~ /--ftp-dir=/i)
			{
			@data_in = split(/--ftp-dir=/,$args);
			$VARREPORT_dir = $data_in[1];
			$VARREPORT_dir =~ s/ .*//gi;
			$VARREPORT_dir =~ s/:/,/gi;
			print "\n----- FTP DIR: $VARREPORT_dir -----\n\n";
			}
		else
			{$VARREPORT_dir = '';}

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
		if ($args =~ /--date=/i)
			{
			@data_in = split(/--date=/,$args);
			$shipdate = $data_in[1];
			$shipdate =~ s/ .*//gi;
			if ($shipdate =~ /today/)
				{
				$shipdate="$year-$mon-$mday";
				$time = $TWOAMsec;
				}
			else
				{
				if ($shipdate =~ /yesterday/)
					{
					$shipdate="$Tyear-$Tmon-$Tmday";
					$year = $Tyear;
					$mon =	$Tmon;
					$mday = $Tmday;
					$time=$TWOAMsecY;
					}
				else
					{
					@cli_date = split("-",$shipdate);
					$year = $cli_date[0];
					$mon =	$cli_date[1];
					$mday = $cli_date[2];
					$cli_date[1] = ($cli_date[1] - 1);
					$time = timelocal(0,0,2,$cli_date[2],$cli_date[1],$cli_date[0]);
					}
				}
			$start_date = $shipdate;
			$start_date =~ s/-//gi;
			if (!$Q) {print "\n----- DATE OVERRIDE: $shipdate($start_date) -----\n\n";}
			}
		else
			{
			$time=$TWOAMsec;
			}
		}
	}
else
	{
	print "no command line options set, using defaults.\n";
	}
### end parsing run-time options ###

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
#	if ( ($line =~ /^VARREPORT_host/) && ($CLIREPORT_host < 1) )
#		{$VARREPORT_host = $line;   $VARREPORT_host =~ s/.*=//gi;}
#	if ( ($line =~ /^VARREPORT_user/) && ($CLIREPORT_user < 1) )
#		{$VARREPORT_user = $line;   $VARREPORT_user =~ s/.*=//gi;}
#	if ( ($line =~ /^VARREPORT_pass/) && ($CLIREPORT_pass < 1) )
#		{$VARREPORT_pass = $line;   $VARREPORT_pass =~ s/.*=//gi;}
#	if ( ($line =~ /^VARREPORT_dir/) && ($CLIREPORT_dir < 1) )
#		{$VARREPORT_dir = $line;   $VARREPORT_dir =~ s/.*=//gi;}
	if ( ($line =~ /^VARREPORT_port/) && ($CLIREPORT_port < 1) )
		{$VARREPORT_port = $line;   $VARREPORT_port =~ s/.*=//gi;}
	$i++;
	}

# Customized Variables
$server_ip = $VARserver_ip;		# Asterisk server IP

if (!$Q)
	{
	print "\n\n\n\n\n\n\n\n\n\n\n\n-- AST_email_web_report.pl --\n\n";
	print "This program is designed to wget a web report and email or FTP it. \n";
	print "\n";
	}


######################################################
##### CHANGE REPORT NAME AND URL HERE!!!!!!!!!
######################################################
#$HTMLfile = "Outbound_Report_ALL_$shipdate$html";
$HTMLfile = "DISCH_SURVEY_$start_date$txt";

#$location = "http://6666:1234\@127.0.0.1/vicidial/fcstats.php?db_log=1\\&query_date=$shipdate\\&group=$group\\&shift=$shift\\&archive=1";
#$location = "http://6666:1234\@192.168.1.101/vicidial/AST_VDADstats.php?query_date=$shipdate\\&end_date=$shipdate\\&group[]=--ALL--\\&shift=ALL";
#$location = "http://8888:8888\@127.0.0.1/vicidial/AST_VDADstats.php?query_date=$shipdate\\&end_date=$shipdate\\&group[]=--ALL--\\&shift=ALL";

# Outbound IVR Export Report:
$location = "http://127.0.0.1/vicidial/call_report_export.php?query_date=$shipdate\\&end_date=$shipdate\\&list_id[]=--ALL--\\&status[]=--ALL--\\&campaign[]=RSIDESVY\\&run_export=1\\&ivr_export=YES\\&export_fields=EXTENDED\\&header_row=NO\\&rec_fields=NONE\\&custom_fields=NO\\&call_notes=NO";

######################################################
######################################################


### GRAB THE REPORT
if (!$Q) {print "Running Report $ship_date\n$location\n";}

`/usr/bin/wget --auth-no-challenge --http-user=6666 --http-password=1234 --output-document=/tmp/$HTMLfile $location `;


###### BEGIN EMAIL SECTION #####
if (length($email_list) > 3)
	{
	if (!$Q) {print "Sending email: $email_list\n";}

	use MIME::QuotedPrint;
	use MIME::Base64;
	use Mail::Sendmail;

	$mailsubject = "VICIDIAL Outbound Report $shipdate";

	%mail = ( To      => "$email_list",
					From    => "$email_sender",
					Subject => "$mailsubject",
			   );
	$boundary = "====" . time() . "====";
	$mail{'content-type'} = "multipart/mixed; boundary=\"$boundary\"";

	$message = encode_qp( "VICIDIAL Outbound Report for $shipdate:\n\n Attachment: $HTMLfile" );

	$Zfile = "/tmp/$HTMLfile";

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
	$mail{body} .= "Content-Type: application/octet-stream; name=\"$HTMLfile\"\n";
	$mail{body} .= "Content-Transfer-Encoding: base64\n";
	$mail{body} .= "Content-Disposition: attachment; filename=\"$HTMLfile\"\n\n";
	$mail{body} .= "$attachment\n";
	$mail{body} .= "$boundary";
	$mail{body} .= "--\n";

	sendmail(%mail) or die $mail::Sendmail::error;
	if (!$Q) {print "ok. log says:\n", $mail::sendmail::log;} ### print mail log for status
	}
###### END EMAIL SECTION #####


###### BEGIN FTP SECTION #####

# FTP overrides-
#	$VARREPORT_host =	'10.0.0.4';
#	$VARREPORT_port =	'21';
#	$VARREPORT_user =	'cron';
#	$VARREPORT_pass =	'test';
#	$VARREPORT_dir =	'';

if (length($VARREPORT_host) > 7)
	{
	use Net::FTP;

	if (!$Q) {print "Sending File Over FTP: $HTMLfile\n";}
	$ftp = Net::FTP->new("$VARREPORT_host", Port => $VARREPORT_port);
	$ftp->login("$VARREPORT_user","$VARREPORT_pass");
	$ftp->cwd("$VARREPORT_dir");
	$ftp->put("/tmp/$HTMLfile", "$HTMLfile");
	$ftp->quit;
	}
###### END FTP SECTION #####


exit;
