#!/usr/bin/perl
#
# AST_VDlist_summary_export.pl                version: 2.0.5
#
# This script is designed to gather stats for the lists and write to static files
# this script should be run from the web server
#
# /usr/share/astguiclient/AST_VDlist_summary_export.pl --email-list=test@gmail.com --email-sender=test@test.com
#
# Copyright (C) 2009  Matt Florell <vicidial@gmail.com>    LICENSE: AGPLv2
#
# CHANGES
# 81214-0001 - First version
# 90106-2308 - Added email sending
#

$txt = '.txt';
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
	print "\n\n\n\n\n\n\n\n\n\n\n\n-- AST_VDlist_summary_export.pl --\n\n";
	print "This program is designed to gather stats on lead lists and post them to a file. \n";
	print "\n";
	print "$timestamp         LIST STATUS SUMMARY REPORT\n";
	print "\n";
	}

$outfile = "LIST_REPORT_$filedate$txt";

### open the X out file for writing ###
$PATHoutfile = "$PATHweb/vicidial/server_reports/$outfile";
open(out, ">$PATHoutfile")
		|| die "Can't open $PATHoutfile: $!\n";

$Ealert = "$timestamp         LIST STATUS SUMMARY REPORT\n\n";


if (!$VARDB_port) {$VARDB_port='3306';}


use DBI;

$dbhA = DBI->connect("DBI:mysql:$VARDB_database:$VARDB_server:$VARDB_port", "$VARDB_user", "$VARDB_pass")
 or die "Couldn't connect to database: " . DBI->errstr;

$TOTAL_LEADS=0;


###########################################################################
########### CURRENT DAY SALES GATHERING outbound-only: vicidial_log  ######
###########################################################################
$stmtA = "select count(*),list_id,status from vicidial_list where list_id>99 group by list_id,status order by list_id,status;";
if ($DB) {print "|$stmtA|\n";}
$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
$sthArows=$sthA->rows;
$i=0;
while ($sthArows > $i)
	{
	@aryA = $sthA->fetchrow_array;
	$count[$i] =	$aryA[0];
	$list_id[$i] =	$aryA[1];
	$status[$i] =	$aryA[2];
	$i++;
	}
$sthA->finish();

$i=0;
$k=0;
$m=0;
$previous_list=$lists_list_id[0];
foreach(@list_id)
	{
	$stmtA = "select status_name from vicidial_statuses where status='$status[$i]';";
	if ($DB) {print "|$stmtA|\n";}
	$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
	$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
	$sthArows=$sthA->rows;
	if ($sthArows > 0)
		{
		@aryA = $sthA->fetchrow_array;
		$status_name[$i] =	$aryA[0];
		}
	$sthA->finish();
	if (length($status_name[$i])<1)
		{
		$stmtA = "select status_name from vicidial_campaign_statuses where status='$status[$i]';";
		if ($DB) {print "|$stmtA|\n";}
		$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
		$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
		$sthArows=$sthA->rows;
		if ($sthArows > 0)
			{
			@aryA = $sthA->fetchrow_array;
			$status_name[$i] =	$aryA[0];
			}
		$sthA->finish();
		}

	$status[$i] = sprintf("%-7s", $status[$i]);
	$status_name[$i] = sprintf("%-20s", $status_name[$i]);   while (length($status_name[$i])>20) {$status_name[$i] =~ s/.$//gi;}
	$count[$i] = sprintf("%7s", $count[$i]);
	if ( ($list_id[$i] =~ /$previous_list/) && (length($list_id[$i]) == length($previous_list)) ) 
		{
		if ($lists_line_count[$k] == 1) {$p='List Name ';}
		if ($lists_line_count[$k] == 2) {$p='Campaign ID ';}
		if ($lists_line_count[$k] == 3) {$p='Campaign Name ';}
		if ($lists_line_count[$k] == 4) {$p='  TOTAL LEADS IN LIST ';}
		if ($lists_line_count[$k] > 4)  {$p='                                          ';}
		$lists_line_output[$k] .= "$p $status[$i] $status_name[$i] $count[$i]\n";
		$lists_line_count[$k]++;
		$lists_line_tally[$k] = ($lists_line_tally[$k] + $count[$i]);
		$lists_list_id[$k] = $list_id[$i];
		}
	else
		{
		if ($lists_line_count[$k] <= 1) {$lists_line_output[$k] .= "List Name \n";}
		if ($lists_line_count[$k] <= 2) {$lists_line_output[$k] .= "Campaign ID \n";}
		if ($lists_line_count[$k] <= 3) {$lists_line_output[$k] .= "Campaign Name \n";}
		if ($lists_line_count[$k] <= 4) {$lists_line_output[$k] .= "  TOTAL LEADS IN LIST \n";}
		$k++;
		$lists_line_output[$k] .= "List ID  $status[$i] $status_name[$i] $count[$i]\n";
		$lists_line_count[$k]++;
		$lists_line_tally[$k] = ($lists_line_tally[$k] + $count[$i]);
		$lists_list_id[$k] = $list_id[$i];
		$previous_list=$list_id[$i];
		}
	$i++;
	}

	if ($lists_line_count[$k] <= 1) {$lists_line_output[$k] .= "List Name \n";}
	if ($lists_line_count[$k] <= 2) {$lists_line_output[$k] .= "Campaign ID \n";}
	if ($lists_line_count[$k] <= 3) {$lists_line_output[$k] .= "Campaign Name \n";}
	if ($lists_line_count[$k] <= 4) {$lists_line_output[$k] .= "  TOTAL LEADS IN LIST \n";}

$i=0;
foreach(@lists_list_id)
	{
	if (length($lists_list_id[$i])>2)
	{
	$stmtA = "select list_name,vicidial_lists.campaign_id,campaign_name from vicidial_lists,vicidial_campaigns where list_id='$lists_list_id[$i]' and vicidial_lists.campaign_id=vicidial_campaigns.campaign_id;";
	if ($DB) {print "|$stmtA|\n";}
	$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
	$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
	$sthArows=$sthA->rows;
	if ($sthArows > 0)
		{
		@aryA = $sthA->fetchrow_array;
		$lists_list_name[$i] =		sprintf("%-30s", $aryA[0]);   while (length($lists_list_name[$i])>30) {$lists_list_name[$i] =~ s/.$//gi;}
		$lists_campaign_id[$i] =	sprintf("%-28s", $aryA[1]);   while (length($lists_campaign_id[$i])>28) {$lists_campaign_id[$i] =~ s/.$//gi;}
		$lists_campaign_name[$i] =	sprintf("%-26s", $aryA[2]);   while (length($lists_campaign_name[$i])>26) {$lists_campaign_name[$i] =~ s/.$//gi;}
		}
	$sthA->finish();

	$lists_list_id[$i] =	sprintf("%-15s", $lists_list_id[$i]);   while (length($lists_list_id[$i])>15) {$lists_list_id[$i] =~ s/.$//gi;}
	$lists_list_id[$i] =	"$lists_list_id[$i]  ($i of $k)";
	$lists_list_id[$i] =	sprintf("%-32s", $lists_list_id[$i]);   while (length($lists_list_id[$i])>32) {$lists_list_id[$i] =~ s/.$//gi;}
	$lists_line_tally[$i] =	sprintf("%-18s", $lists_line_tally[$i]);   while (length($lists_line_tally[$i])>18) {$lists_line_tally[$i] =~ s/.$//gi;}
	$TOTAL_LEADS = ($TOTAL_LEADS + $lists_line_tally[$i]);

	$lists_line_output[$i] =~ s/List ID/List ID: $lists_list_id[$i]/gi;
	$lists_line_output[$i] =~ s/List Name/List Name: $lists_list_name[$i]/gi;
	$lists_line_output[$i] =~ s/Campaign ID/Campaign ID: $lists_campaign_id[$i]/gi;
	$lists_line_output[$i] =~ s/Campaign Name/Campaign Name: $lists_campaign_name[$i]/gi;
	$lists_line_output[$i] =~ s/TOTAL LEADS IN LIST/TOTAL LEADS IN LIST: $lists_line_tally[$i]/gi;

	$Ealert .= "\n";
	$Ealert .= " LIST INFORMATION                          STATUS  DESCRIPTION            COUNT\n";
	$Ealert .= "-------------------------------------------------------------------------------\n";
	$Ealert .= "$lists_line_output[$i]";
	}
	$i++;
	}





if ($ftp_transfer > 0)
{
	use Net::FTP;

	if (!$Q) {print "Sending File Over FTP: $outfile\n";}
	$ftp = Net::FTP->new("$VARREPORT_host", Port => $VARREPORT_port);
	$ftp->login("$VARREPORT_user","$VARREPORT_pass");
	$ftp->cwd("$VARREPORT_dir");
	$ftp->put("$PATHweb/vicidial/server_reports/$outfile", "$outfile");
	$ftp->quit;
}

### calculate time to run script ###
$secY = time();
$secZ = ($secY - $secX);
$secZm = ($secZ /60);

$Ealert .= "\n";
$Ealert .= "-------------------------------------------------------------------------------------\n";
$Ealert .= "LIST REPORT FOR $timestamp: $outfile\n";
$Ealert .= "TOTAL LEADS IN SYSTEM: $TOTAL_LEADS\n";
$Ealert .= "\n";
$Ealert .= "TOTAL LEADS IN SYSTEM BY STATUS:\n";

### gather status subtotals
$stmtA = "select count(*),status from vicidial_list where list_id>99 group by status order by status;";
if ($DB) {print "|$stmtA|\n";}
$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
$sthArows=$sthA->rows;
$i=0;
while ($sthArows > $i)
	{
	@aryA = $sthA->fetchrow_array;
	$Tcount[$i] =	$aryA[0];
	$Tstatus[$i] =	$aryA[1];
	$i++;
	}
$sthA->finish();

$i=0;
$k=0;
$m=0;
foreach(@Tstatus)
	{
	$stmtA = "select status_name from vicidial_statuses where status='$Tstatus[$i]';";
	if ($DB) {print "|$stmtA|\n";}
	$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
	$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
	$sthArows=$sthA->rows;
	if ($sthArows > 0)
		{
		@aryA = $sthA->fetchrow_array;
		$Tstatus_name[$i] =	$aryA[0];
		}
	$sthA->finish();
	if (length($Tstatus_name[$i])<1)
		{
		$stmtA = "select status_name from vicidial_campaign_statuses where status='$Tstatus[$i]';";
		if ($DB) {print "|$stmtA|\n";}
		$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
		$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
		$sthArows=$sthA->rows;
		if ($sthArows > 0)
			{
			@aryA = $sthA->fetchrow_array;
			$Tstatus_name[$i] =	$aryA[0];
			}
		$sthA->finish();
		}

	$Tstatus[$i] = sprintf("%-7s", $Tstatus[$i]);
	$Tstatus_name[$i] = sprintf("%-20s", $Tstatus_name[$i]);   while (length($Tstatus_name[$i])>20) {$Tstatus_name[$i] =~ s/.$//gi;}
	$Tcount[$i] = sprintf("%7s", $Tcount[$i]);

	$Ealert .= "$Tstatus[$i] - $Tstatus_name[$i]: $Tcount[$i]\n";

	$i++;
	}

print "script execution time in seconds: $secZ     minutes: $secZm\n";

print out "$Ealert";

close(out);

###### EMAIL SECTION

if ( (length($Ealert)>5) && (length($email_list) > 3) )
	{
	print "Sending email: $email_list\n";

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

	}


exit;
