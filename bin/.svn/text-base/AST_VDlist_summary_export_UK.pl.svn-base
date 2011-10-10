#!/usr/bin/perl
#
# AST_VDlist_summary_export_UK.pl                version: 2.0.5
#
# This script is designed to gather stats for the lists and write to static files
# this script should be run from the web server
#
# /usr/share/astguiclient/AST_VDlist_summary_export_UK.pl --campaign=GOODB-GROUP1 --good-statuses=NEW --filename=UNWORKED_LEADS_YYYY-MM-DD.txt --email-list=test@gmail.com --email-sender=test@test.com
#
# Copyright (C) 2009  Matt Florell <vicidial@gmail.com>    LICENSE: AGPLv2
#
# CHANGES
# 90105-2343 - First version
# 90106-2308 - Added email sending
# 

$secX = time();
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
	$shipdate = "$year-$mon-$mday";
	$datestamp = "$year/$mon/$mday $hour:$min";

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
		print "  [--campaign=XXX] = Campaign that sales will be pulled from\n";
		print "  [--good-statuses=XXX-XXY] = Statuses that are deemed to be \"Good\". Default NEW\n";
		print "  [--filename=XXX] = Name to be used for file\n";
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
		if ($args =~ /--good-statuses=/i)
			{
			@data_in = split(/--good-statuses=/,$args);
			$good_statuses = $data_in[1];
			$good_statuses =~ s/ .*$//gi;
			$good_statusesSQL = $good_statuses;
			$good_statusesSQL =~ s/-/','/gi;
			$good_statusesSQL = "'$good_statusesSQL'";
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
		if ($args =~ /--filename=/i)
			{
			#	print "\n|$ARGS|\n\n";
			@data_in = split(/--filename=/,$args);
				$filename = $data_in[1];
				$filename =~ s/ .*$//gi;
				$filename =~ s/YYYY/$year/gi;
				$filename =~ s/MM/$mon/gi;
				$filename =~ s/DD/$mday/gi;
			$filename_override=1;
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

if (!$Q)
	{
	print "\n\n\n\n\n\n\n\n\n\n\n\n-- AST_VDlist_summary_export_UK.pl --\n\n";
	print "This program is designed to gather stats on lead lists and post them to a file. \n";
	print "\n";
	print "$timestamp         UK LIST STATUS SUMMARY REPORT\n";
	print "\n";
	print "================================================================================\n";
	print "Region    Postal --------------------- Prioirty ----------------------\n";
	print " Code      Code    1       2       7       8       5       6       Bad  Total\n";
	print "--------------------------------------------------------------------------------\n";
	}

$outfile = "UNWORKED_LEADS_$filedate$txt";
if ($filename_override > 0) {$outfile = $filename;}

### open the X out file for writing ###
$PATHoutfile = "$PATHweb/vicidial/server_reports/$outfile";
open(out, ">$PATHoutfile")
		|| die "Can't open $PATHoutfile: $!\n";


$Ealert .= "$timestamp         UK REGION STATUS SUMMARY REPORT\n\n";
$Ealert .= "================================================================================\n";
$Ealert .= "Region    Postal --------------------- Prioirty ----------------------\n";
$Ealert .= " Code      Code    1       2       7       8       5       6       Bad  Total\n";
$Ealert .= "--------------------------------------------------------------------------------\n";


if (!$VARDB_port) {$VARDB_port='3306';}


use DBI;

$dbhA = DBI->connect("DBI:mysql:$VARDB_database:$VARDB_server:$VARDB_port", "$VARDB_user", "$VARDB_pass")
 or die "Couldn't connect to database: " . DBI->errstr;

$TOTAL_LEADS=0;


###########################################################################
########### grab lists within campaigns and regions of the UK  ######
###########################################################################

$stmtA = "select list_id from vicidial_lists where campaign_id IN($campaignSQL);";
if ($DBX) {print "|$stmtA|\n";}
$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
$sthArows=$sthA->rows;
$i=0;
while ($sthArows > $i)
	{
	@aryA = $sthA->fetchrow_array;
	$listSQL .= "'$aryA[0]',";
	$i++;
	}
$sthA->finish();
if ($sthArows < 1) {print "ERROR!  no lists for campaigns $campaign";   exit;}
else {$listSQL =~ s/,$//gi;}

$stmtA = "select region,region_code,post_code_prefix,country_code,list_code_id from vicidial_uk_region_codes order by region_code,post_code_prefix limit 2000;";
if ($DBX) {print "|$stmtA|\n";}
$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
$sthArows=$sthA->rows;
$i=0;
$m=0;
while ($sthArows > $m)
	{
	@aryA = $sthA->fetchrow_array;
	if (length($aryA[2])<3)
		{
		$region[$i] =			$aryA[0];
		$region_code[$i] =		$aryA[1];
		$post_code_prefix[$i] =	$aryA[2]; if (length($post_code_prefix[$i])<2) {$post_code_prefix[$i] = "$post_code_prefix[$i] ";}
		$country_code[$i] =		$aryA[3];
		$list_code_id[$i] =		$aryA[4];
		$i++;
		}
	$m++;
	}
$sthA->finish();
if ($sthArows < 1) {print "ERROR!  no regions in the UK";   exit;}

$i=0;
$k=0;
$m=0;
$previous_region_code='';
$p1t=0;  $p2t=0;  $p7t=0;  $p8t=0;  $p5t=0;  $p6t=0;  $p9t=0;  $pSt=0;
$p1T=0;  $p2T=0;  $p7T=0;  $p8T=0;  $p5T=0;  $p6T=0;  $p9T=0;  $pST=0;
foreach(@region_code)
	{
	if ( ($region_code[$i] =~ /$previous_region_code/) && (length($region_code[$i]) == length($previous_region_code)) ) 
		{$do_nothing=1;}
	else
		{
		if ($i > 0)
			{
			$p1T = ($p1T + $p1t);
			$p2T = ($p2T + $p2t);
			$p7T = ($p7T + $p7t);
			$p8T = ($p8T + $p8t);
			$p5T = ($p5T + $p5t);
			$p6T = ($p6T + $p6t);
			$p9T = ($p9T + $p9t);
			$pST = ($pST + $pSt);
			$p1t = sprintf("%-8s",$p1t);
			$p2t = sprintf("%-8s",$p2t);
			$p7t = sprintf("%-8s",$p7t);
			$p8t = sprintf("%-8s",$p8t);
			$p5t = sprintf("%-8s",$p5t);
			$p6t = sprintf("%-8s",$p6t);
			$p9t = sprintf("%-7s",$p9t);
			$pSt = sprintf("%-8s",$pSt);
			$previous_region_code = sprintf("%-3s",$previous_region_code);

			$Ealert .= "   Subtotal: $previous_region_code $p1t$p2t$p7t$p8t$p5t$p6t$p9t$pSt\n";
			if ($DB) {print "   Subtotal: $previous_region_code $p1t$p2t$p7t$p8t$p5t$p6t$p9t$pSt\n";}
			$p1t=0;  $p2t=0;  $p7t=0;  $p8t=0;  $p5t=0;  $p6t=0;  $p9t=0;  $pSt=0;
			}
		$Ealert .= "$region[$i] - $region_code[$i]:\n";
		if ($DB) {print "$region[$i] - $region_code[$i]:\n";}
		$previous_region_code=$region_code[$i];
		}

	$p1[$i]=0;  $p2[$i]=0;  $p7[$i]=0;  $p8[$i]=0;  $p5[$i]=0;  $p6[$i]=0;  $p9[$i]=0;  $pS[$i]=0;
	$stmtA = "select count(*),list_id from vicidial_list where postal_code LIKE \"$post_code_prefix[$i]%\" and list_id IN($listSQL) and status IN($good_statusesSQL) group by list_id order by list_id,status;";
	if ($DBX) {print "|$stmtA|\n";}
	$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
	$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
	$sthArows=$sthA->rows;
	$k=0;
	while ($sthArows > $k)
		{
		@aryA = $sthA->fetchrow_array;
		$count[$k] =	$aryA[0];
		$list_id[$k] =	$aryA[1];
			if ($DBX) {print "$i|$k|$region_code[$i]|$post_code_prefix[$i]|     |$count[$k]|$list_id[$k]|\n";}
		if ($list_id[$k] =~ /1$/)			{$p1[$i]=($p1[$i] + $count[$k]);   $p1t=($p1t + $count[$k]);}
		if ($list_id[$k] =~ /2$/)			{$p2[$i]=($p2[$i] + $count[$k]);   $p2t=($p2t + $count[$k]);}
		if ($list_id[$k] =~ /7$/)			{$p7[$i]=($p7[$i] + $count[$k]);   $p7t=($p7t + $count[$k]);}
		if ($list_id[$k] =~ /8$/)			{$p8[$i]=($p8[$i] + $count[$k]);   $p8t=($p8t + $count[$k]);}
		if ($list_id[$k] =~ /5$/)			{$p5[$i]=($p5[$i] + $count[$k]);   $p5t=($p5t + $count[$k]);}
		if ($list_id[$k] =~ /6$/)			{$p6[$i]=($p6[$i] + $count[$k]);   $p6t=($p6t + $count[$k]);}
		if ($list_id[$k] =~ /3$|4$|9$|0$/)	{$p9[$i]=($p9[$i] + $count[$k]);   $p9t=($p9t + $count[$k]);}
		$pS[$i] = ($pS[$i] + $count[$k]);
		$pSt = ($pSt + $count[$k]);
		$k++;
		}
	$sthA->finish();

	$p1[$i] = sprintf("%-8s",$p1[$i]);
	$p2[$i] = sprintf("%-8s",$p2[$i]);
	$p7[$i] = sprintf("%-8s",$p7[$i]);
	$p8[$i] = sprintf("%-8s",$p8[$i]);
	$p5[$i] = sprintf("%-8s",$p5[$i]);
	$p6[$i] = sprintf("%-8s",$p6[$i]);
	$p9[$i] = sprintf("%-7s",$p9[$i]);
	$pS[$i] = sprintf("%-8s",$pS[$i]);
	$post_code_prefix[$i] = sprintf("%-5s",$post_code_prefix[$i]);

	$Ealert .= "            $post_code_prefix[$i]$p1[$i]$p2[$i]$p7[$i]$p8[$i]$p5[$i]$p6[$i]$p9[$i]$pS[$i]\n";
	if ($DB) {print "            $post_code_prefix[$i]$p1[$i]$p2[$i]$p7[$i]$p8[$i]$p5[$i]$p6[$i]$p9[$i]$pS[$i]\n";}
	
	$i++;
	}

$p1T = sprintf("%-8s",$p1T);
$p2T = sprintf("%-8s",$p2T);
$p7T = sprintf("%-8s",$p7T);
$p8T = sprintf("%-8s",$p8T);
$p5T = sprintf("%-8s",$p5T);
$p6T = sprintf("%-8s",$p6T);
$p9T = sprintf("%-7s",$p9T);
$pST = sprintf("%-8s",$pST);

$Ealert .= "TOTAL            $p1T$p2T$p7T$p8T$p5T$p6T$p9T$pST\n";
if ($DB) {print "TOTAL            $p1T$p2T$p7T$p8T$p5T$p6T$p9T$pST\n";}


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

	$mailsubject = "VICIDIAL Unworked Leads $outfile";

	  %mail = ( To      => "$email_list",
							From    => "$email_sender",
							Subject => "$mailsubject",
					   );
		$boundary = "====" . time() . "====";
		$mail{'content-type'} = "multipart/mixed; boundary=\"$boundary\"";

		$message = encode_qp( "VICIDIAL Unworked leads Report for $shipdate:\n\n Attachment: $outfile" );

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
