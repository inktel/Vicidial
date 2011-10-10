#!/usr/bin/perl
#
# AST_call_log_export.pl                version: 2.4
#
# This script is designed to gather basic call logs for a VICIDIAL Outbound calling campaigns and
# post them to a directory. This script is a little different from other report
# scripts in that it will use the "processed" fields in the vicidial_log and
# vicidial_agent_log tables to keep track of the records that it has output.
#
# /usr/share/astguiclient/AST_call_log_export.pl --campaign=GOODB-GROUP1-GROUP3-GROUP4-SPECIALS-DNC_BEDS --output-format=tab-basic --debug --filename=BEDSsaleDATETIME.txt --email-list=test@gmail.com --email-sender=test@test.com
#
# Copyright (C) 2011  Matt Florell <vicidial@gmail.com>    LICENSE: AGPLv2
#
# CHANGES
# 100310-0112 - First version
# 110202-2214 - Added ncad14csv format
# 110423-0350 - Do not die on email issue
#

$txt = '.txt';
$US = '_';
$MT[0] = '';
$Q=0;
$DB=0;
$uniqueidLIST='|';
$dateset=0;

# Default FTP account variables
$VARREPORT_host = '10.0.0.4';
$VARREPORT_user = 'cron';
$VARREPORT_pass = 'test';
$VARREPORT_port = '21';
$VARREPORT_dir  = 'REPORTS';

# default CLI values
$campaign = 'TESTCAMP';
$output_format = 'tab-basic';

$secX = time();
$time = $secX;
	($sec,$min,$hour,$mday,$mon,$year,$wday,$yday,$isdst) = localtime(time);
$year = ($year + 1900);
$yy = ($year - 2000);
$mon++;
if ($mon < 10) {$mon = "0$mon";}
if ($mday < 10) {$mday = "0$mday";}
if ($hour < 10) {$hour = "0$hour";}
if ($min < 10) {$min = "0$min";}
if ($sec < 10) {$sec = "0$sec";}
$timestamp = "$year-$mon-$mday $hour:$min:$sec";
$filedate = "$year$mon$mday";
$filedatetime = "$year$mon$mday$hour$min$sec";
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
		print "  [--date=YYYY-MM-DD] = date override, otherwise will use processed=N\n";
		print "  [--hour-offset=X] = print datetime strings with this hour offset\n";
		print "  [--filename=XXX] = Name to be used for file\n";
		print "  [--campaign=XXX] = Campaign that sales will be pulled from\n";
		print "  [--without-camp=XXX] = Campaign that will be excluded from ALL\n";
		print "  [--output-format=XXX] = Format of file. Default \"pipe-standard\"\n";
		print "  [--ftp-transfer] = Send results file by FTP to another server\n";
		print "  [--email-list=test@test.com:test2@test.com] = send email results to these addresses\n";
		print "  [--email-sender=vicidial@localhost] = sender for the email results\n";
		print "  [--quiet] = quiet\n";
		print "  [--test] = test\n";
		print "  [--debug] = debugging messages\n";
		print "  [--debugX] = Super debugging messages\n";
		print "\n";
		print "  format options:\n";
		print "   tab-basic:\n";
		print "   call_date  phone_number  vendor_id  status  user  first_name  last_name  lead_id  list_id  campaign_id  length_in_sec  source_id\n";
		print "   pipe-basic:\n";
		print "   call_date|phone_number|vendor_id|status|user|first_name|last_name|lead_id|list_id|campaign_id|length_in_sec|source_id|\n";
		print "   ncad14csv:\n";
		print "   vendor_lead_code,\"source_id\",\"last_name, first_name\",\"phone_number\",\"address2\",\"alt_phone\",\"postal_code\",\"address1\",\"address3\",\"\security_phrase\",\"status\",\"call_date\",\"email\",\"city\"\n";
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
		if ($args =~ /-quiet/i)
			{
			$q=1;   $Q=1;
			}

		if ($args =~ /--hour-offset=/i)
			{
			@data_in = split(/--hour-offset=/,$args);
			$hour_offset = $data_in[1];
			$hour_offset =~ s/ .*//gi;
			if (!$Q) {print "\n----- HOUR OFFSET: $hour_offset -----\n\n";}
			}
		else
			{$hour_offset=0;}
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
			if (!$Q) {print "\n----- DATE OVERRIDE: $shipdate -----\n\n";}
			$dateset=1;
			}
		else
			{
			$time=$TWOAMsec;
			$dateset=0;
			}


		if ($args =~ /--campaign=/i)
			{
			#	print "\n|$ARGS|\n\n";
			@data_in = split(/--campaign=/,$args);
			$campaign = $data_in[1];
			$campaign =~ s/ .*$//gi;
			$campaignSQL = $campaign;
			if ($campaignSQL =~ /-/) 
				{
				$campaignSQL =~ s/-/','/gi;
				}
			$campaignSQL = "'$campaignSQL'";
			}
		if ($args =~ /--without-camp=/i)
			{
			#	print "\n|$ARGS|\n\n";
			@data_in = split(/--without-camp=/,$args);
			$NOTcampaign = $data_in[1];
			$NOTcampaign =~ s/ .*$//gi;
			$NOTcampaignSQL = $NOTcampaign;
			if ($NOTcampaignSQL =~ /-/) 
				{
				$NOTcampaignSQL =~ s/-/','/gi;
				}
			$NOTcampaignSQL = "'$NOTcampaignSQL'";
			}
		if ($args =~ /--filename=/i)
			{
			#	print "\n|$ARGS|\n\n";
			@data_in = split(/--filename=/,$args);
			$yy = ($year - 2000);
			$filename = $data_in[1];
			$filename =~ s/ .*$//gi;
			$filename =~ s/YYYY/$year/gi;
			$filename =~ s/MM/$mon/gi;
			$filename =~ s/DD/$mday/gi;
			$filename =~ s/DATETIME/$year$mon$mday$hour$min$sec/gi;
			$filename =~ s/DATESPEC/$yy$mon$mday-$hour$min$sec/gi;
			$filename_override=1;
			}
		if ($args =~ /--output-format=/i)
			{
			@data_in = split(/--output-format=/,$args);
			$output_format = $data_in[1];
			$output_format =~ s/ .*$//gi;
			}
		if ($args =~ /-ftp-transfer/i)
			{
			if (!$Q)
				{print "\n----- FTP TRANSFER MODE -----\n\n";}
			$ftp_transfer=1;
			}
		if ($args =~ /--test/i)
			{
			$T=1;   $TEST=1;
			if (!$Q) {print "\n----- TESTING -----\n\n";}
			}
		if ($args =~ /--email-list=/i)
			{
			@data_in = split(/--email-list=/,$args);
			$email_list = $data_in[1];
			$email_list =~ s/ .*//gi;
			$email_list =~ s/:/,/gi;
			if (!$Q) {print "\n----- EMAIL NOTIFICATION: $email_list -----\n\n";}
			}
		else
			{$email_list = '';}

		if ($args =~ /--email-sender=/i)
			{
			@data_in = split(/--email-sender=/,$args);
			$email_sender = $data_in[1];
			$email_sender =~ s/ .*//gi;
			$email_sender =~ s/:/,/gi;
			if (!$Q) {print "\n----- EMAIL NOTIFICATION SENDER: $email_sender -----\n\n";}
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

if ($output_format =~ /^tab-basic$/) 
	{$DLT = "\t";   $txt='.txt';   if (!$Q) {print "---- tab-basic ----\n";}}
if ($output_format =~ /^pipe-basic$/) 
	{$DLT = "|";   $txt='.txt';   if (!$Q) {print "---- pipe-basic ----\n";}}
if ($output_format =~ /^ncad14csv$/) 
	{$DLT = ",";   $txt='.csv';   if (!$Q) {print "---- ncad14csv ----\n";}}

if (length($campaignSQL) < 2)
	{
	if (length($NOTcampaignSQL) < 2)
		{
		$VLcampaignSQL = "vicidial_log.campaign_id NOT IN('')";
		$VLAcampaignSQL = "vicidial_agent_log.campaign_id NOT IN('')";
		}
	else
		{
		$VLcampaignSQL = "vicidial_log.campaign_id NOT IN($NOTcampaignSQL)";
		$VLAcampaignSQL = "vicidial_agent_log.campaign_id NOT IN($NOTcampaignSQL)";
		}
	}
else
	{
	$VLcampaignSQL = "vicidial_log.campaign_id IN($campaignSQL)";
	$VLAcampaignSQL = "vicidial_agent_log.campaign_id IN($campaignSQL)";
	}

if ($dateset > 0)
	{
	$VLdateSQL = "call_date > '$shipdate 00:00:01' and call_date < '$shipdate 23:59:59'";
	$VLAdateSQL = "event_time > '$shipdate 00:00:01' and event_time < '$shipdate 23:59:59'";
	}
else
	{
	$VLdateSQL = "processed='N'";
	$VLAdateSQL = "processed='N'";
	}
if (!$Q)
	{
	print "\n\n\n\n\n\n\n\n\n\n\n\n-- AST_call_log_export.pl --\n\n";
	print "This program is designed to gather all outbound calls and post them to a file. \n";
	print "\n";
	print "Campaign:      $campaign    $VLcampaignSQL\n";
	print "Output Format: $output_format\n";
	print "\n";
	}

$outfile = "$campaign$US$filedatetime$txt";
if ($filename_override > 0) {$outfile = $filename;}

### open the X out file for writing ###
$PATHoutfile = "$PATHweb/vicidial/server_reports/$outfile";
open(out, ">$PATHoutfile")
		|| die "Can't open $PATHoutfile: $!\n";

if (!$VARDB_port) {$VARDB_port='3306';}


use DBI;

$dbhA = DBI->connect("DBI:mysql:$VARDB_database:$VARDB_server:$VARDB_port", "$VARDB_user", "$VARDB_pass")
 or die "Couldn't connect to database: " . DBI->errstr;

$dbhB = DBI->connect("DBI:mysql:$VARDB_database:$VARDB_server:$VARDB_port", "$VARDB_user", "$VARDB_pass")
 or die "Couldn't connect to database: " . DBI->errstr;

$TOTAL_SALES=0;


$timezone='-5';
$stmtA = "SELECT local_gmt FROM servers where server_ip='$server_ip';";
$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
$sthArows=$sthA->rows;
	if ($DBX) {print "   $sthArows|$stmtA|\n";}
if ($sthArows > 0)
	{
	@aryA = $sthA->fetchrow_array;
	$timezone =	$aryA[0];
	$sthA->finish();
	}
$offset_timezone = ($timezone + $hour_offset);
$SQLtimezone = sprintf("%10.2f",$timezone);
$SQLtimezone =~ s/\./:/gi;
$SQLtimezone =~ s/:50/:30/gi;
$SQLtimezone =~ s/ //gi;
$SQLoffset_timezone = sprintf("%10.2f",$offset_timezone);
$SQLoffset_timezone =~ s/\./:/gi;
$SQLoffset_timezone =~ s/:50/:30/gi;
$SQLoffset_timezone =~ s/ //gi;
$convert_tz = "'$SQLtimezone','$SQLoffset_timezone'";

if (!$Q)
	{print "\n----- SQL CONVERT_TZ: $SQLtimezone|$SQLoffset_timezone     $convert_tz -----\n\n";}



###########################################################################
########### CURRENT DAY SALES GATHERING outbound-only non-agent handled calls: vicidial_log  ######
###########################################################################
# call_date|phone_number|vendor_id|status|user|first_name|last_name|lead_id|list_id|campaign_id|length_in_sec|source_id|

$stmtA = "select CONVERT_TZ(call_date,$convert_tz),vicidial_log.phone_number,vendor_lead_code,vicidial_log.status,vicidial_log.user,first_name,last_name,vicidial_list.lead_id,vicidial_log.list_id,campaign_id,length_in_sec,vicidial_list.source_id,uniqueid from vicidial_list,vicidial_log where $VLcampaignSQL and $VLdateSQL and vicidial_log.lead_id=vicidial_list.lead_id and vicidial_log.status IN('DROP','XDROP','NA','AA','AM','AL','AB','ADC','PU','PM','SVYEXT','SVYVM','SVYHU','SVYREC','HXFER','HOLDTO','QVMAIL','RQXFER','CPDATB','CPDB','CPDNA','CPDREJ','CPDINV','CPDSUA','CPDSI','CPDSNC','CPDSR','CPDSUK','CPDSV','CPDUK','CPDERR','TIMEOT','AFTHRS','NANQUE') order by call_date;";
$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
$sthArows=$sthA->rows;
$rec_count=0;
if ($DB) {print "$sthArows|$stmtA|\n";}
while ($sthArows > $rec_count)
	{
	@aryA = $sthA->fetchrow_array;
	$call_date =		$aryA[0];
	$phone_number =		$aryA[1];
	$vendor_id =		$aryA[2];
	$status =			$aryA[3];
	$user =				$aryA[4];
	$first_name =		$aryA[5];
	$last_name =		$aryA[6];
	$lead_id =			$aryA[7];
	$list_id =			$aryA[8];
	$campaign_id =		$aryA[9];
	$length_in_sec =	$aryA[10];
	$source_id =		$aryA[11];
	$uniqueid =			$aryA[12];
	$VLupdateSQL .= "'$uniqueid',";

	&select_format_loop;

	$TOTAL_SALES++;
	$rec_count++;
	}
$sthA->finish();


###########################################################################
########### CURRENT DAY SALES GATHERING outbound-only agent handled calls: vicidial_agent_log  ######
###########################################################################
# call_date|phone_number|vendor_id|status|user|first_name|last_name|lead_id|list_id|campaign_id|length_in_sec|source_id|

$stmtA = "select CONVERT_TZ(event_time,$convert_tz),vicidial_list.phone_number,vendor_lead_code,vicidial_agent_log.status,vicidial_agent_log.user,first_name,last_name,vicidial_list.lead_id,vicidial_list.list_id,campaign_id,talk_sec,vicidial_list.source_id,dead_sec,agent_log_id from vicidial_list,vicidial_agent_log where $VLAcampaignSQL and $VLAdateSQL and vicidial_agent_log.lead_id=vicidial_list.lead_id and vicidial_agent_log.status NOT IN('DROP','XDROP','NA','AA','AM','AL','AB','ADC','PU','PM','SVYEXT','SVYVM','SVYHU','SVYREC','HXFER','HOLDTO','QVMAIL','RQXFER','CPDATB','CPDB','CPDNA','CPDREJ','CPDINV','CPDSUA','CPDSI','CPDSNC','CPDSR','CPDSUK','CPDSV','CPDUK','CPDERR','TIMEOT','AFTHRS','NANQUE','INCALL','','QUEUE') order by event_time;";
$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
$sthArows=$sthA->rows;
$rec_count=0;
if ($DB) {print "$sthArows|$stmtA|\n";}
while ($sthArows > $rec_count)
	{
	@aryA = $sthA->fetchrow_array;
	$call_date =		$aryA[0];
	$phone_number =		$aryA[1];
	$vendor_id =		$aryA[2];
	$status =			$aryA[3];
	$user =				$aryA[4];
	$first_name =		$aryA[5];
	$last_name =		$aryA[6];
	$lead_id =			$aryA[7];
	$list_id =			$aryA[8];
	$campaign_id =		$aryA[9];
	$length_in_sec =	($aryA[10] - $aryA[12]);
	$source_id =		$aryA[11];
	if ($length_in_sec < 0) {$length_in_sec=0;}
	$agent_log_id =		$aryA[13];

	$VLAupdateSQL .= "'$agent_log_id',";
	&select_format_loop;

	$TOTAL_SALES++;
	$rec_count++;
	}
$sthA->finish();


if ($TEST < 1)
	{
	##### update records to processed=Y
	$VLupdateSQL .= "''";
	$stmtA = "UPDATE vicidial_log set processed='Y' where uniqueid IN($VLupdateSQL) and processed='N';";
		if($DBX){print STDERR "\n|$stmtA|\n";}
	$affected_rows = $dbhA->do($stmtA); #  or die  "Couldn't execute query:|$stmtA|\n";
		if($DBX){print STDERR "\n|$affected_rows records updated|\n";}

	$VLAupdateSQL .= "''";
	$stmtA = "UPDATE vicidial_agent_log set processed='Y' where agent_log_id IN($VLAupdateSQL) and processed='N';";
		if($DBX){print STDERR "\n|$stmtA|\n";}
	$affected_rows = $dbhA->do($stmtA); #  or die  "Couldn't execute query:|$stmtA|\n";
		if($DBX){print STDERR "\n|$affected_rows records updated|\n";}
	}
close(out);


###### EMAIL SECTION

if ( (length($Ealert)>5) && (length($email_list) > 3) )
	{
	if (!$Q) {print "Sending email: $email_list\n";}

	use MIME::QuotedPrint;
	use MIME::Base64;
	use Mail::Sendmail;

	$mailsubject = "VICIDIAL Calls Export $outfile";

	%mail = ( To      => "$email_list",
						From    => "$email_sender",
						Subject => "$mailsubject",
				   );
	$boundary = "====" . time() . "====";
	$mail{'content-type'} = "multipart/mixed; boundary=\"$boundary\"";

	$message = encode_qp( "VICIDIAL Calls Export:\n\n Attachment: $outfile\n Total Records: $TOTAL_SALES\n" );

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

	sendmail(%mail); # or die $mail::Sendmail::error;
	if (!$Q) {print "ok. log says:\n", $mail::sendmail::log;}  ### print mail log for status

	}



# FTP overrides-
#	$VARREPORT_host =	'10.0.0.4';
#	$VARREPORT_port =	'21';
#	$VARREPORT_user =	'cron';
#	$VARREPORT_pass =	'test';
#	$VARREPORT_dir =	'';

$NODATEDIR = 0;	# Don't use dated directories for audio
$YEARDIR = 1;	# put dated directories in a year directory first

if ($ftp_transfer > 0)
	{
	use Net::FTP;

	if (!$Q) {print "Sending File Over FTP: $outfile\n";}
	$ftp = Net::FTP->new("$VARREPORT_host", Port => $VARREPORT_port, Debug => $DB);
	$ftp->login("$VARREPORT_user","$VARREPORT_pass");
#	$ftp->cwd("CALL_LOG");
	$ftp->cwd("CALL_LOG");
	$ftp->put("$PATHweb/vicidial/server_reports/$outfile", "$outfile");
	$ftp->quit;
	}


### calculate time to run script ###
$secY = time();
$secZ = ($secY - $secX);
$secZm = ($secZ /60);

if (!$Q) {print "CALLS EXPORT FOR $shipdate: $outfile\n";}
if (!$Q) {print "TOTAL CALLS: $TOTAL_SALES\n";}
if (!$Q) {print "script execution time in seconds: $secZ     minutes: $secZm\n";}

exit;





### Subroutine for formatting of the output ###
sub select_format_loop
	{
	if ($output_format =~ /^pipe-basic$/) 
		{
		$str = "$call_date|$phone_number|$vendor_id|$status|$user|$first_name|$last_name|$lead_id|$list_id|$campaign_id|$length_in_sec|$source_id|\n";
		}

	if ($output_format =~ /^tab-basic$/) 
		{
		$str = "$call_date\t$phone_number\t$vendor_id\t$status\t$user\t$first_name\t$last_name\t$lead_id\t$list_id\t$campaign_id\t$length_in_sec\t$source_id\t\n";
		}

	if ($output_format =~ /^ncad14csv$/) 
		{
		if ($status =~ /^PU$/) {$status='HU';}
		if ($status =~ /^PM$/) {$status='BM';}
		if ($status =~ /^AL$/) {$status='BM';}
		if ($status =~ /^A$/) {$status='AH';}
		if ($status =~ /^AA$/) {$status='AH';}
		if ($status =~ /^B$/) {$status='BS';}
		if ($status =~ /^AB$/) {$status='BS';}
		if ($status =~ /^DC$/) {$status='IP';}
		
		$str = "$vendor_lead_code,\"$source_id\",\"$last_name, $first_name\",\"$phone_number\",\"$address2\",\"$alt_phone\",\"$postal_code\",\"$address1\",\"$address3\",\"$security_phrase\",\"$status\",\"$call_date\",\"$email\",\"$city\"\n";
		}


	$Ealert .= "$str"; 

	print out "$str"; 
	if ($DBX) {print "$str\n";}

	if ($DB > 0)
		{
		if ($rec_count =~ /10$/i) {print STDERR "0     $rec_count\r";}
		if ($rec_count =~ /20$/i) {print STDERR "+     $rec_count\r";}
		if ($rec_count =~ /30$/i) {print STDERR "|     $rec_count\r";}
		if ($rec_count =~ /40$/i) {print STDERR "\\     $rec_count\r";}
		if ($rec_count =~ /50$/i) {print STDERR "-     $rec_count\r";}
		if ($rec_count =~ /60$/i) {print STDERR "/     $rec_count\r";}
		if ($rec_count =~ /70$/i) {print STDERR "|     $rec_count\r";}
		if ($rec_count =~ /80$/i) {print STDERR "+     $rec_count\r";}
		if ($rec_count =~ /90$/i) {print STDERR "0     $rec_count\r";}
		if ($rec_count =~ /00$/i) {print "$rec_count|$TOTAL_SALES|         |$phone_number|$call_date|\n";}
		}
	}

