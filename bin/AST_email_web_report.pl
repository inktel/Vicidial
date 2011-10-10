#!/usr/bin/perl
#
# AST_email_web_report.pl                version: 2.0.5
#
# This script is designed to wget a web report and then email it as an attachment
# this script should be run from the web server
#
# /usr/share/astguiclient/AST_VDlist_summary_export.pl --email-list=test@gmail.com --email-sender=test@test.com
#
# Copyright (C) 2009  Matt Florell <vicidial@gmail.com>    LICENSE: AGPLv2
#
# CHANGES
# 90225-1247 - First version
#

$txt = '.txt';
$html = '.html';
$US = '_';
$MT[0] = '';

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
		print "  [-q] = quiet\n";
		print "  [-t] = test\n";
		print "  [--debug] = debugging messages\n";
		print "  [--debugX] = Super debugging messages\n";
		print "  [--email-list=test@test.com:test2@test.com] = send email results to these addresses\n";
		print "  [--email-sender=vicidial@localhost] = sender for the email results\n";
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
	print "no command line options set, using defaults.\n";
	}
### end parsing run-time options ###


$secX = time();
	($sec,$min,$hour,$mday,$mon,$year,$wday,$yday,$isdst) = localtime(time);
	$year = ($year + 1900);
	$mon++;

#	# rerun override - Use this to override the day manually
#	$year='2007';
#	$mon='5';
#	$mday='18';

	if ($mon < 10) {$mon = "0$mon";}
	if ($mday < 10) {$mday = "0$mday";}
	if ($hour < 10) {$hour = "0$hour";}
	if ($min < 10) {$min = "0$min";}
	if ($sec < 10) {$sec = "0$sec";}
	$timestamp = "$year-$mon-$mday $hour:$min:$sec";
	$filedate = "$year$mon$mday";
	$shipdate = "$year-$mon-$mday";
	$datestamp = "$year/$mon/$mday $hour:$min";

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
	if ( ($line =~ /^VARREPORT_dir/) && ($CLIREPORT_dir < 1) )
		{$VARREPORT_dir = $line;   $VARREPORT_dir =~ s/.*=//gi;}
	$i++;
	}

# Customized Variables
$server_ip = $VARserver_ip;		# Asterisk server IP

if ($output_format =~ /^pipe-standard$/) 
	{$DLT = '|';   $txt='.txt';   print "---- pipe-standard ----\n";}
if ($output_format =~ /^csv-standard$/) 
	{$DLT = "','";   $txt='.csv';   print "---- csv-standard ----\n";}
if ($output_format =~ /^tab-standard$/) 
	{$DLT = "\t";   $txt='.txt';   print "---- tab-standard ----\n";}
if ($output_format =~ /^pipe-triplep$/) 
	{$DLT = '';   $txt='.txt';   print "---- pipe-triplep ----\n";}
if ($output_format =~ /^pipe-vici$/) 
	{$DLT = '|';   $txt='.txt';   print "---- pipe-vici ----\n";}
if ($output_format =~ /^html-rec$/) 
	{$DLT = ' ';   $txt='.html';   print "---- html-rec ----\n";}

if (!$Q)
	{
	print "\n\n\n\n\n\n\n\n\n\n\n\n-- AST_email_web_report.pl --\n\n";
	print "This program is designed to wget a web report and email it. \n";
	print "\n";
	}


$HTMLfile = "Outbound_Report_ALL_$shipdate$html";

#$location = "http://6666:1234\@127.0.0.1/vicidial/fcstats.php?db_log=1\\&query_date=$shipdate\\&group=$group\\&shift=$shift\\&archive=1";
#$location = "http://6666:1234\@192.168.1.101/vicidial/AST_VDADstats.php?query_date=$shipdate\\&end_date=$shipdate\\&group[]=--ALL--\\&shift=ALL";
$location = "http://8888:8888\@127.0.0.1/vicidial/AST_VDADstats.php?query_date=$shipdate\\&end_date=$shipdate\\&group[]=--ALL--\\&shift=ALL";

print "Running Report $ship_date\n$location\n";
`/usr/bin/wget --output-document=/tmp/$HTMLfile $location `;






###### EMAIL SECTION

if (length($email_list) > 3)
	{
	print "Sending email: $email_list\n";

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
		   print "ok. log says:\n", $mail::sendmail::log;  ### print mail log for status

	}


exit;
