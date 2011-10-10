#!/usr/bin/perl

# This report gathers the user, lead_id, event_time, campaign_id, pause_sec,
# wait_sec, talk_sec, dispo_sec, and status from the vicidial_agent_log as
# well as the the vendor_lead code from the list, and the status_name from
# either the vicidial_statuses or vicidial_campaign_statuses table and
# puts all that information into a CSV file which gets emailed out.

# lets play nice
use 5.008;
use strict;
use warnings;

# all the modules we use
use MIME::QuotedPrint;
use MIME::Base64;
use Mail::Sendmail;
use DBI;

my $DB = 0;	# 1 for debugging
my $email_list = ""; # people to send the report to
my $email_sender = ""; # person the email is from

# begin parsing run-time options
if ( ( $ARGV[0] ) && ( length( $ARGV[0] ) > 1 ) ) {
	my $i = 0;
	my $args = "";

	while ($#ARGV >= $i) {
		$args = "$args $ARGV[$i]";
		$i++;
	}

	if ($args =~ /--help/i) {
		print "allowed run time options:\n";
		print "  [--email-list=test\@test.com:test2\@test.com] = send email results to these addresses\n";
		print "  [--email-sender=vicidial\@localhost] = sender for the email results\n";
		print "  [--debug] = debugging messages\n";
		print "\n";

		exit;
	} else {
		if ($args =~ /--debug/i) {
			$DB=1;
			print "\n----- DEBUG MODE -----\n\n";
		}
		if ($args =~ /--email-list=/i) {
			my @data_in = split( /--email-list=/ , $args );
			$email_list = $data_in[1];
			$email_list =~ s/ .*//gi;
			$email_list =~ s/:/,/gi;
			print "\n----- EMAIL NOTIFICATION: $email_list -----\n\n";
		}
		if ($args =~ /--email-sender=/i) {
			my @data_in = split(/--email-sender=/,$args);
			$email_sender = $data_in[1];
			$email_sender =~ s/ .*//gi;
			$email_sender =~ s/:/,/gi;
			print "\n----- EMAIL NOTIFICATION SENDER: $email_sender -----\n\n";
		} else {
			$email_sender = 'vicidial@localhost';
		}
	}
}


# get the current time
my $current_time = time();

my $csec = 0;
my $cmin = 0;
my $chour = 0;
my $cmday = 0;
my $cmon = 0;
my $cyear = 0;
my $cwday = 0;
my $cyday = 0;
my $cisdst = 0;

($csec,$cmin,$chour,$cmday,$cmon,$cyear,$cwday,$cyday,$cisdst) = localtime($current_time);

$cyear = ($cyear + 1900);
$cmon++;
if ($cmon < 10) {$cmon = "0$cmon";}
if ($cmday < 10) {$cmday = "0$cmday";}
if ($chour < 10) {$chour = "0$chour";}
if ($cmin < 10) {$cmin = "0$cmin";}
if ($csec < 10) {$csec = "0$csec";}

# now get the time a week (604800 seconds) ago
my $week_ago_time = $current_time - 604800;

my $wsec = 0;
my $wmin = 0;
my $whour = 0;
my $wmday = 0;
my $wmon = 0;
my $wyear = 0;
my $wwday = 0;
my $wyday = 0;
my $wisdst = 0;

($wsec,$wmin,$whour,$wmday,$wmon,$wyear,$wwday,$wyday,$wisdst) = localtime($week_ago_time);

$wyear = ($wyear + 1900);
$wmon++;
if ($wmon < 10) {$wmon = "0$wmon";}
if ($wmday < 10) {$wmday = "0$wmday";}
if ($whour < 10) {$whour = "0$whour";}
if ($wmin < 10) {$wmin = "0$wmin";}
if ($wsec < 10) {$wsec = "0$wsec";}

# mysql date stamps
my $current_time_stamp = "$cyear-$cmon-$cmday $chour:$cmin:$csec";
my $week_ago_time_stamp = "$wyear-$wmon-$wmday $whour:$wmin:$wsec";

print "running report for date range $week_ago_time_stamp to $current_time_stamp\n";

# read in the config file 

# default path to astguiclient configuration file:
my $config_file = '/etc/astguiclient.conf';

open(CONFIG, "$config_file") || die "can't open $config_file: $!\n";
my @conf = <CONFIG>;
close(CONFIG);
my $i = 0;

my $PATHhome 		= "";
my $PATHlogs 		= "";
my $PATHagi 		= "";
my $PATHweb 		= "";
my $PATHsounds 		= "";
my $PATHmonitor 	= "";
my $VARserver_ip 	= "";
my $VARDB_server 	= "";
my $VARDB_database 	= "";
my $VARDB_user 		= "";
my $VARDB_pass 		= "";
my $VARDB_port 		= "";
my $VARREPORT_host 	= "";
my $VARREPORT_user 	= "";
my $VARREPORT_pass 	= "";
my $VARREPORT_port 	= "";
my $VARREPORT_dir 	= "";

foreach(@conf) {
	my $line = $conf[$i];
	$line =~ s/ |>|\n|\r|\t|\#.*|;.*//gi;
	if ($line =~ /^PATHhome/) { $PATHhome = $line;   $PATHhome =~ s/.*=//gi; }
	if ($line =~ /^PATHlogs/) { $PATHlogs = $line;   $PATHlogs =~ s/.*=//gi; }
	if ($line =~ /^PATHagi/) { $PATHagi = $line;   $PATHagi =~ s/.*=//gi; }
	if ($line =~ /^PATHweb/) { $PATHweb = $line;   $PATHweb =~ s/.*=//gi; }
	if ($line =~ /^PATHsounds/) { $PATHsounds = $line;   $PATHsounds =~ s/.*=//gi; }
	if ($line =~ /^PATHmonitor/) { $PATHmonitor = $line;   $PATHmonitor =~ s/.*=//gi; }
	if ($line =~ /^VARserver_ip/) { $VARserver_ip = $line;   $VARserver_ip =~ s/.*=//gi; }
	if ($line =~ /^VARDB_server/) { $VARDB_server = $line;   $VARDB_server =~ s/.*=//gi; }
	if ($line =~ /^VARDB_database/) { $VARDB_database = $line;   $VARDB_database =~ s/.*=//gi; }
	if ($line =~ /^VARDB_user/) { $VARDB_user = $line;   $VARDB_user =~ s/.*=//gi; }
	if ($line =~ /^VARDB_pass/) { $VARDB_pass = $line;   $VARDB_pass =~ s/.*=//gi; }
	if ($line =~ /^VARDB_port/) { $VARDB_port = $line;   $VARDB_port =~ s/.*=//gi; }
	if ($line =~ /^VARREPORT_host/) { $VARREPORT_host = $line;   $VARREPORT_host =~ s/.*=//gi; }
	if ($line =~ /^VARREPORT_user/) { $VARREPORT_user = $line;   $VARREPORT_user =~ s/.*=//gi; }
	if ($line =~ /^VARREPORT_pass/) { $VARREPORT_pass = $line;   $VARREPORT_pass =~ s/.*=//gi; }
	if ($line =~ /^VARREPORT_port/) { $VARREPORT_port = $line;   $VARREPORT_port =~ s/.*=//gi; }
	if ($line =~ /^VARREPORT_dir/) { $VARREPORT_dir = $line;   $VARREPORT_dir =~ s/.*=//gi; }
	$i++;
}


# the name of the csv file
my $csv_file_name = "weekly_agent_stats-$wyear$wmon$wmday-$cyear$cmon$cmday.csv";

# path to the file
my $csv_path = "$PATHweb/$csv_file_name";

if ( !$VARDB_port ) { $VARDB_port='3306'; }

# connect to the db
my $dbh = DBI->connect("DBI:mysql:$VARDB_database:$VARDB_server:$VARDB_port", "$VARDB_user", "$VARDB_pass") or die "Couldn't connect to database: " . DBI->errstr;

# setup the queries
my $sys_status_stmt = "SELECT status, status_name FROM vicidial_statuses;";
my $sys_status_sth = $dbh->prepare($sys_status_stmt) or die "preparing: ",$dbh->errstr;

my $camp_status_stmt = "SELECT status, status_name FROM vicidial_campaign_statuses;";
my $camp_status_sth = $dbh->prepare($camp_status_stmt) or die "preparing: ",$dbh->errstr;

my $agent_stats_stmt = "SELECT user, lead_id, event_time, campaign_id, pause_sec, wait_sec, talk_sec, dispo_sec, status FROM vicidial_agent_log where event_time <= ? and event_time >= ?";
my $agent_stats_sth = $dbh->prepare($agent_stats_stmt) or die "preparing: ",$dbh->errstr;

my $vendor_stmt = "SELECT vendor_lead_code FROM vicidial_list WHERE lead_id = ?;";
my $vendor_sth = $dbh->prepare($vendor_stmt) or die "preparing: ",$dbh->errstr;


# get the system statuses
$sys_status_sth->execute() or die "executing: $sys_status_stmt ", $dbh->errstr();
my $sys_status_ref = $sys_status_sth->fetchall_hashref('status');

# get the campaign statuses
$camp_status_sth->execute() or die "executing: $camp_status_stmt ", $dbh->errstr();
my $camp_status_ref = $camp_status_sth->fetchall_hashref('status');

# open the CSV file for writing
open (CSVFILE, ">>$csv_path") or die $!;

# print the header
print CSVFILE "\"user\",\"lead_id\",\"event_time\",\"campaign_id\",\"pause_sec\",\"wait_sec\",\"talk_sec\",\"dispo_sec\",\"status\",\"status_name\",\"vendor_lead_code\"\n";

# grab the vicidial_agent_log records
$agent_stats_sth->execute( $current_time_stamp, $week_ago_time_stamp ) or die "executing: $agent_stats_stmt ", $dbh->errstr();
my $agent_stats_rows = $agent_stats_sth->rows;
my $rec_count = 0;

while ($agent_stats_rows > $rec_count) {
	my @ary = $agent_stats_sth->fetchrow_array;

	my $user	= $ary[0];
	my $lead_id	= $ary[1];
	my $event_time	= $ary[2];
	my $campaign_id	= $ary[3];
	my $pause_sec	= $ary[4];
	my $wait_sec	= $ary[5];
	my $talk_sec	= $ary[6];
	my $dispo_sec	= $ary[7];
	my $status	= $ary[8];

	my $status_name = "";

	# apparently you can have a NULL status
	if ($status) { 
		$status_name = $sys_status_ref->{$status}->{'status_name'};
		if ( !($status_name) ) {
			$status_name = $camp_status_ref->{$status}->{'status_name'};
		}
	} else {
		$status = "";
	}
	
	my $vendor_lead_code = "";

	# apparently you can have a NULL lead_id
	if ($lead_id) {
		$vendor_sth->execute($lead_id) or die "executing: $agent_stats_stmt ", $dbh->errstr();
		my @vend_ary = $vendor_sth->fetchrow_array();
		$vendor_lead_code = $vend_ary[0];
		if ( !($vendor_lead_code) ) {
                        $vendor_lead_code = "";
                }
	} else {
		$lead_id="";
	}
	
	print CSVFILE "\"$user\",\"$lead_id\",\"$event_time\",\"$campaign_id\",\"$pause_sec\",\"$wait_sec\",\"$talk_sec\",\"$dispo_sec\",\"$status\",\"$status_name\",\"$vendor_lead_code\"\n";
	
	$rec_count++;
}

close (CSVFILE);

# clean up the statement handles
$sys_status_sth->finish();
$camp_status_sth->finish();
$agent_stats_sth->finish();
$vendor_sth->finish();
$dbh->disconnect();

# email the file if they gave use some email addresses
if ($email_list ne "") {
	print "Generating the email\n";

	my $mailsubject = "VICIDIAL Weekly Agent Status Report $csv_file_name";

	my %mail = ( 
			To      => "$email_list",
			From    => "$email_sender",
			Subject => "$mailsubject",
		);
	my $boundary = "====" . time() . "====";
	$mail{'content-type'} = "multipart/mixed; boundary=\"$boundary\"";

	my $message = encode_qp( "VICIDIAL Lead Export:\n\n Attachment: $csv_file_name\n Total Records: $agent_stats_rows\n" );

	my $Zfile = "$csv_path";

	open (F, $Zfile) or die "Cannot read $Zfile: $!";
	binmode F; undef $/;
	my $attachment = encode_base64(<F>);
	close F;

	$boundary = '--'.$boundary;
	$mail{body} .= "$boundary\n";
	$mail{body} .= "Content-Type: text/plain; charset=\"iso-8859-1\"\n";
	$mail{body} .= "Content-Transfer-Encoding: quoted-printable\n\n";
	$mail{body} .= "$message\n";
	$mail{body} .= "$boundary\n";
	$mail{body} .= "Content-Type: application/octet-stream; name=\"$csv_file_name\"\n";
	$mail{body} .= "Content-Transfer-Encoding: base64\n";
	$mail{body} .= "Content-Disposition: attachment; filename=\"$csv_file_name\"\n\n";
	$mail{body} .= "$attachment\n";
	$mail{body} .= "$boundary";
	$mail{body} .= "--\n";

	print "Sending email to: $email_list\n";
	sendmail(%mail) or die $Mail::Sendmail::error;
	print "OK. Log says:\n", $Mail::Sendmail::log, "\n";
}
