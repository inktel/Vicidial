#!/usr/bin/perl

# AST_CRON_audio_3_newftp.pl   version 2.4
#
# This is a STEP-3 program in the audio archival process. Normally you can run it 
# every 3 minutes and copies the recording files to an FTP server.
#
# put an entry into the cron of of your asterisk machine to run this script 
# every 3 minutes or however often you desire
#
# ### recording mixing/compressing/ftping scripts
# #0,3,6,9,12,15,18,21,24,27,30,33,36,39,42,45,48,51,54,57 * * * * /usr/share/astguiclient/AST_CRON_audio_1_move_mix.pl
# 0,3,6,9,12,15,18,21,24,27,30,33,36,39,42,45,48,51,54,57 * * * * /usr/share/astguiclient/AST_CRON_audio_1_move_VDonly.pl
# 1,4,7,10,13,16,19,22,25,28,31,34,37,40,43,46,49,52,55,58 * * * * /usr/share/astguiclient/AST_CRON_audio_2_compress.pl --MP3
# 2,5,8,11,14,17,20,23,26,29,32,35,38,41,44,47,50,53,56,59 * * * * /usr/share/astguiclient/AST_CRON_audio_3_newftp.pl --MP3
#
# FLAGS FOR COMPRESSION FILES TO TRANSFER
# --gsm or --GSM = GSM 6.10 files
# --mp3 or --MP3 = MPEG Layer3 files
# --ogg or --OGG = OGG Vorbis files
# --wav or --WAV = WAV files
# --gsw or --GSW = GSM 6.10 codec with RIFF headers (.wav extension)
#
# FLAGS FOR PING SETTINGS
# --ping-type = The type of ping to send. Options are "none", "tcp", "udp", "icmp", 
#                 "stream", "syn", and "external". None disables pinging. Default is "icmp"
#					WARNING setting --ping-type="none" can lead to files being "transfer" to no if your ftp server goes down.
# --ping-timeout = How long to wait for the ping to timeout before giving up, default is 5 seconds.
#
# FLAGS FOR FTP TRANSFER INFO
# --ftp-host = the host address to ftp into
# --ftp-port = the port of the ftp server 
# --ftp-user = the user to log into the ftp server with
# --ftp-pass = the password to log into the ftp server with
# --ftp-dir  = the directory to put the files into on the ftp server
# --url-path = the url where the recordings can be accessed after the move
#
# FLAGS THAT EFFECT THE ACTUAL TRANSFER
# --stay-connected = remain connected to the ftp server. Without this the script will disconnect and reconnect for each file.
# --transfer-limit = the number of files to transfer before giving up. Default is 1000
# --list-limit     = number of files to list in the directory before moving on
# --no-date-dir    = does not create a date directory on the server for the files.
# --campaign_id    = which OUTBOUND campaigns to transfer files for in a '-' delimited list 
#                       (this only works for outbound calls, not inbound or transfers) 
# --ingroup_id     = which ingroups to transfer files for in a '-' delimited list
#                       WARNING you can only set --campaign_id or --ingroup_id, not both.
#
# The following example will transfer 50 mp3s for the campaigns TESTCAMP1 and TESTCAMP2 to the ftp server. 
# It will not create dated directories on the ftp server. It will send icmp pings to the server 
# and wait at most 3 seconds for a response:
# /usr/share/astguiclient/AST_CRON_audio_3_newftp.pl --no-date-dir --mp3 --ping-type="icmp" --ping-timeout=3 \
#    --ftp-host="10.10.10.15" --ftp-port=21 --ftp-user="username" --ftp-pass="password" --ftp-dir="RECORDINGS" \
#    --url-path="http://10.10.10.15/RECORDINGS" --transfer-limit=50 --list-limit=200 --campaign_id="TESTCAMP1-TESTCAMP2"
#
# Copyright (C) 2011  Matt Florell <vicidial@gmail.com>    LICENSE: AGPLv2
#
# CHANGELOG:
# 90930-1405 - mikec - first build
# 110524-1054 - Added run-check concurrency check option
#

use 5.008;
use strict;
use warnings;

# OTHER USE FLAGS
use DBI;
use Net::Ping;
use Net::FTP;
use Time::HiRes ('gettimeofday','usleep','sleep');  # necessary to have perl sleep command for less than one second


# Vars
my $debug = 0;
my $debugX = 0;
my $test = 0;
my $run_check = 0;

my $pingtype	= "tcp";
my $pingtimeout = 5;
my $datedir		= 1;
my $camp_check	= 0;
my $campaigns	= "";
my @camp_array;
my $ingrp_check	= 0;
my $ingroups	= "";
my @ingrp_array;
my $list_limit	= 1000;
my $trans_limit	= 1000;
my $trans_type	= "wav";

my $PATHhome = '';
my $PATHlogs = '';
my $PATHagi = '';
my $PATHweb = '';
my $PATHsounds = '';
my $PATHmonitor = '';
my $PATHDONEmonitor = '';
my $VARserver_ip = '';
my $VARDB_server = '';
my $VARDB_database = '';
my $VARDB_user = '';
my $VARDB_pass = '';
my $VARDB_port = '';
my $ftp_host = '10.0.0.4';
my $ftp_user = 'cron';
my $ftp_pass = 'test';
my $ftp_port = '21';
my $ftp_dir  = 'RECORDINGS';
my $url_path = 'http://10.0.0.4';

# default path to astguiclient configuration file:
my $PATHconf =		'/etc/astguiclient.conf';

# load the config file settings
open(CONF, "$PATHconf") || die "can't open $PATHconf: $!\n";
my @conf = <CONF>;
close(CONF);
my $i=0;
my $line='';
foreach(@conf) {
	$line = $conf[$i];
	$line =~ s/ |>|\n|\r|\t|\#.*|;.*//gi;
	if ($line =~ /^PATHhome/)
		{$PATHhome = $line;   $PATHhome =~ s/.*=//gi;}
	if ($line =~ /^PATHlogs/)
		{$PATHlogs = $line;   $PATHlogs =~ s/.*=//gi;}
	if ($line =~ /^PATHagi/)
		{$PATHagi = $line;   $PATHagi =~ s/.*=//gi;}
	if ($line =~ /^PATHweb/)
		{$PATHweb = $line;   $PATHweb =~ s/.*=//gi;}
	if ($line =~ /^PATHsounds/)
		{$PATHsounds = $line;   $PATHsounds =~ s/.*=//gi;}
	if ($line =~ /^PATHmonitor/)
		{$PATHmonitor = $line;   $PATHmonitor =~ s/.*=//gi;}
	if ($line =~ /^PATHDONEmonitor/)
		{$PATHDONEmonitor = $line;   $PATHDONEmonitor =~ s/.*=//gi;}
	if ($line =~ /^VARserver_ip/)
		{$VARserver_ip = $line;   $VARserver_ip =~ s/.*=//gi;}
	if ($line =~ /^VARDB_server/)
		{$VARDB_server = $line;   $VARDB_server =~ s/.*=//gi;}
	if ($line =~ /^VARDB_database/)
		{$VARDB_database = $line;   $VARDB_database =~ s/.*=//gi;}
	if ($line =~ /^VARDB_user/)
		{$VARDB_user = $line;   $VARDB_user =~ s/.*=//gi;}
	if ($line =~ /^VARDB_pass/)
		{$VARDB_pass = $line;   $VARDB_pass =~ s/.*=//gi;}
	if ($line =~ /^VARDB_port/)
		{$VARDB_port = $line;   $VARDB_port =~ s/.*=//gi;}
	if ($line =~ /^VARFTP_host/)
		{$ftp_host = $line;   $ftp_host =~ s/.*=//gi;}
	if ($line =~ /^VARFTP_user/)
		{$ftp_user = $line;   $ftp_user =~ s/.*=//gi;}
	if ($line =~ /^VARFTP_pass/)
		{$ftp_pass = $line;   $ftp_pass =~ s/.*=//gi;}
	if ($line =~ /^VARFTP_port/)
		{$ftp_port = $line;   $ftp_port =~ s/.*=//gi;}
	if ($line =~ /^VARFTP_dir/)
		{$ftp_dir = $line;   $ftp_dir =~ s/.*=//gi;}
	if ($line =~ /^VARHTTP_path/)
		{$url_path = $line;   $url_path =~ s/.*=//gi;}
	$i++;
}

# parse run-time options
my $args = '';
if (length($ARGV[0])>1) {
	$i=0;
	while ($#ARGV >= $i) {
		$args = "$args $ARGV[$i]";
		$i++;
	}
	if ($args =~ /--help/i)	{
		print "allowed run time options:\n";
		print "  [--help]               = this screen\n";
		print "  [--test]               = don't move the file\n";
		print "  [--debug]              = debug\n";
		print "  [--debugX]             = super debug\n";
		print "  [--gsm or --GSM]       = copy GSM 6.10 files\n";
		print "  [--mp3 or --MP3]       = copy MPEG Layer3 files\n";
		print "  [--ogg or --OGG]       = copy OGG Vorbis files\n";
		print "  [--wav or --WAV]       = copy WAV files\n";
		print "  [--gsw or --GSW]       = copy GSM 6.10 codec with RIFF headers (.wav extension)\n";
		print "  [--ping-type]          = The type of ping to send. Options are \"none\", \"tcp\", \"udp\", \"icmp\", \n";
		print "                         \"stream\", \"syn\", and \"external\". None disables pinging. Default is \"icmp\"\n";
		print "                         WARNING setting --ping-type=\"none\" can lead to files being \"transfer\"\n";
		print "                         to no where if your ftp server goes down.\n";
		print "  [--ping-timeout]       = How long to wait for the ping to timeout before giving up, default is 5 seconds.\n";
		print "  [--ftp-host]           = the host address to ftp into\n";
		print "  [--ftp-port]           = the port of the ftp server \n";
		print "  [--ftp-user]           = the user to log into the ftp server with\n";
		print "  [--ftp-pass]           = the password to log into the ftp server with\n";
		print "  [--ftp-dir]            = the directory to put the files into on the ftp server\n";
		print "  [--url-path]           = the url where the recordings can be accessed after the move\n";
		print "  [--transfer-limit=XXX] = the number of files to transfer before giving up. Default is 1000\n";
		print "  [--list-limit=XXX]     = number of files to list in the directory before moving on\n";
		print "  [--no-date-dir]        = does not put the files in a dated directory.\n";
		print "  [--run-check]          = concurrency check, die if another instance is running\n";
		print "  [--campaign_id]        = which OUTBOUND campaigns to transfer files for in a '-' delimited list \n";
		print "                         (this only works for outbound calls, not inbound or transfers)\n"; 
		print "  [--ingroup_id]         = which ingroups to transfer files for in a '-' delimited list\n";
		print "                         WARNING you can only set --campaign_id or --ingroup_id, not both.\n\n";
		exit;
	} else {
		if ($args =~ /--debug/i) {
			$debug=1;
			print "\n----- DEBUG -----\n\n";
		}
		if ($args =~ /--debugX/i) {
			$debugX=1;
			$debug=1;
			print "\n----- SUPER DEBUG -----\n\n";
		}
		if ($args =~ /--test/i) {
			$test=1;
			print "\n----- TESTING -----\n\n";
		}
		if (($args =~ /--nodatedir/i) || ($args =~ /--no-date-dir/i)) {
			$datedir=0;
			if ($debug) {
				print "\n----- NO DATE DIRECTORIES -----\n\n";
			}
		}
		if ($args =~ /--run-check/i)
			{
			$run_check=1;
			if ($debug) {print "\n----- CONCURRENCY CHECK -----\n\n";}
			}

		if ($args =~ /--ping-type=/i) {
			my @data_in = split(/--ping-type=/,$args);
			$pingtype = $data_in[1];
			$pingtype =~ s/ .*//gi;
			if ($debug) {
				print "\n----- FILE TRANSFER LIMIT: $trans_limit -----\n\n";
			}
		}
		if ($args =~ /--ping-timeout=/i) {
			my @data_in = split(/--ping-timeout=/,$args);
			$pingtimeout = $data_in[1];
			$pingtimeout =~ s/ .*//gi;
			if ($debug) {
				print "\n----- PING TIMEOUT: $pingtimeout -----\n\n";
			}
		}
		if ($args =~ /--ftp-host=/i) {
			my @data_in = split(/--ftp-host=/,$args);
			$ftp_host = $data_in[1];
			$ftp_host =~ s/ .*//gi;
			if ($debug) {
				print "\n----- FTP HOST: $ftp_host -----\n\n";
			}
		}
		if ($args =~ /--ftp-port=/i) {
			my @data_in = split(/--ftp-port=/,$args);
			$ftp_port = $data_in[1];
			$ftp_port =~ s/ .*//gi;
			if ($debug) {
				print "\n----- FTP PORT: $ftp_port -----\n\n";
			}
		}
		if ($args =~ /--ftp-user=/i) {
			my @data_in = split(/--ftp-user=/,$args);
			$ftp_user = $data_in[1];
			$ftp_user =~ s/ .*//gi;
			if ($debug) {
				print "\n----- FTP USER: $ftp_user -----\n\n";
			}
		}
		if ($args =~ /--ftp-pass=/i) {
			my @data_in = split(/--ftp-pass=/,$args);
			$ftp_pass = $data_in[1];
			$ftp_pass =~ s/ .*//gi;
			if ($debug) {
				print "\n----- FTP PASSWORD: $ftp_pass -----\n\n";
			}
		}
		if ($args =~ /--ftp-dir=/i) {
			my @data_in = split(/--ftp-dir=/,$args);
			$ftp_dir = $data_in[1];
			$ftp_dir =~ s/ .*//gi;
			if ($debug) {
				print "\n----- FTP DIRECTORY: $ftp_dir -----\n\n";
			}
		}
		if ($args =~ /--url-path=/i) {
			my @data_in = split(/--url-path=/,$args);
			$url_path = $data_in[1];
			$url_path =~ s/ .*//gi;
			if ($debug) {
				print "\n----- URL PATH: $url_path -----\n\n";
			}
		}
		if ($args =~ /--campaign_id=/i) {
			my @data_in = split(/--campaign_id=/,$args);
			$campaigns = $data_in[1];
			$campaigns =~ s/ .*//gi;
			$campaigns = uc($campaigns);
			$camp_check=1;
			if ($debug) {
				print "\n----- CAMPAIGNS: $campaigns -----\n\n";
			}
		}
		if ($args =~ /--ingroup_id=/i) {
			my @data_in = split(/--ingroup_id=/,$args);
			$ingroups = $data_in[1];
			$ingroups =~ s/ .*//gi;
			$ingrp_check=1;
			if ($debug) {
				print "\n----- INGROUPS: $ingroups -----\n\n";
			}
		}
		if ($args =~ /--transfer-limit=/i) {
			my @data_in = split(/--transfer-limit=/,$args);
			$trans_limit = $data_in[1];
			$trans_limit =~ s/ .*//gi;
			if ($debug) {
				print "\n----- FILE TRANSFER LIMIT: $trans_limit -----\n\n";
			}
		}
		if ($args =~ /--list-limit=/i) {
			my @data_in = split(/--list-limit=/,$args);
			$list_limit = $data_in[1];
			$list_limit =~ s/ .*//gi;
			if ($debug) {
				print "\n----- FILE LIST LIMIT: $list_limit -----\n\n";
			}
		}
		if ( ( $args =~ /--GSM/i ) || ( $args =~ /--gsm/i ) ){
			$trans_type="gsm";
			if ($debug) {
				print "GSM audio files\n";
			}
		} else {
			if ( ($args =~ /--MP3/i) || ($args =~ /--mp3/i) ) {
				$trans_type="mp3";
				if ($debug) {
					print "MP3 audio files\n";
				}
			} else {
				if ( ( $args =~ /--OGG/i) || ($args =~ /--ogg/i) ) {
					$trans_type="ogg";
					if ($debug) {
						print "OGG audio files\n";
					}
				} else {
					if ( ( $args =~ /--WAV/i ) || ( $args =~ /--wav/i ) ) {
						$trans_type="wav";
						if ($debug) {
							print "WAV audio files\n";
						}
					} else {
						if ( ($args =~ /--GSW/i) || ($args =~ /--gsw/i) ) {
							$trans_type="gsw";
							if ($debug) {
								print "GSW audio files\n";
							}
						}
					}
				}
			}
		}
	}
}
	
#### make sure they are not trying to do something we cannot do ####
if (($camp_check) && ($ingrp_check)) {
	print "ERROR. You cannot specify ingroups and campaigns in the same instance of this script.\n";
	exit();
}

#### get the list of campaigns ####
if ($camp_check) {
	@camp_array = split(/-/,$campaigns);
}

#### get the list of ingroups ####
if ($ingrp_check) {
	@ingrp_array = split(/-/,$ingroups);
}


### concurrency check
if ($run_check > 0)
	{
	my $grepout = `/bin/ps ax | grep $0 | grep -v grep`;
	my $grepnum=0;
	$grepnum++ while ($grepout =~ m/\n/g);
	if ($grepnum > 1) 
		{
		if ($debug) {print "I am not alone! Another $0 is running! Exiting...\n";}
		exit;
		}
	}


#### connect to the db ####
my $dbhA = DBI->connect("DBI:mysql:$VARDB_database:$VARDB_server:$VARDB_port", "$VARDB_user", "$VARDB_pass")
 or die "Couldn't connect to database: " . DBI->errstr;

#### prepare the sql queries ahead of time so they run quicker ####
# get the info for the recording
my $rec_log_stmt = "SELECT recording_id, start_time, vicidial_id, lead_id FROM recording_log WHERE filename=? ORDER BY recording_id DESC LIMIT 1;";
my $rec_log_sth = $dbhA->prepare($rec_log_stmt) or die "preparing: ",$dbhA->errstr;

# find the campaign_id for the call.
my $vici_log_stmt = "SELECT campaign_id FROM vicidial_log WHERE uniqueid=? AND lead_id=? ORDER BY campaign_id DESC LIMIT 1;";
my $vici_log_sth = $dbhA->prepare($vici_log_stmt) or die "preparing: ",$dbhA->errstr;

# find the ingroup_id for the call.
# why did matt name the ingroup field in the closer log campaign_id?
my $clsr_log_stmt = "SELECT campaign_id FROM vicidial_closer_log WHERE closecallid=? AND lead_id=? ORDER BY campaign_id DESC LIMIT 1;";
my $clsr_log_sth = $dbhA->prepare($clsr_log_stmt) or die "preparing: ",$dbhA->errstr;

# update the recording log
my $update_log_stmt = "UPDATE recording_log SET location=? WHERE recording_id=?;";
my $update_log_sth = $dbhA->prepare($update_log_stmt) or die "preparing: ",$dbhA->errstr;

#### directory where -all recordings are
my $directory = '';
if ($trans_type eq "wav") {$directory = "$PATHDONEmonitor";}
if ($trans_type eq "gsw") {$directory = "$PATHDONEmonitor/GSW";}
if ($trans_type eq "gsm") {$directory = "$PATHDONEmonitor/GSM";}
if ($trans_type eq "ogg") {$directory = "$PATHDONEmonitor/OGG";}
if ($trans_type eq "mp3") {$directory = "$PATHDONEmonitor/MP3";}


opendir(FILE, "$directory/");
my @files = readdir(FILE);

### Loop through files first to gather filesizes
my $file_loop_count=0;
my $files_that_count=0;
my @FILEsize1;
foreach(@files)	{
	$FILEsize1[$file_loop_count] = 0;
	if ( (length($files[$file_loop_count]) > 4) && (!-d "$directory/$files[$file_loop_count]") ) {
		$FILEsize1[$file_loop_count] = (-s "$directory/$files[$file_loop_count]");
		if ($debugX) {
			print "$directory/$files[$file_loop_count] $FILEsize1[$file_loop_count]\n";
		}
		$files_that_count++;
	}
	$file_loop_count++;
	if ($files_that_count >= $list_limit) {
		last();
	}		
}

### sleep 5 seconds
sleep(5);

my $transfered_files = 0;
my @FILEsize2;
my $recording_id = '';
my $start_date = '';
my $ALLfile = '';
my $SQLFILE = '';
my $transfer_file=0;

my $ping = Net::Ping->new($pingtype, $pingtimeout);

if ($pingtype eq "none") {
	$ping = 0;
}

### Loop through files a second time to gather filesizes again 5 seconds later
$file_loop_count=0;
$files_that_count=0;
foreach(@files)	{
	if ($debug) {print "\n\n\n--------NEW-FILE-------------------------------------------------------------------------------------------\n";}
	$transfer_file=0;	
	$FILEsize2[$file_loop_count] = 0;

	if ( (length($files[$file_loop_count]) > 4) && (!-d "$directory/$files[$file_loop_count]") ) {

		$FILEsize2[$file_loop_count] = (-s "$directory/$files[$file_loop_count]");
		if ($debug) {
			print "$directory/$files[$file_loop_count] $FILEsize2[$file_loop_count]\n";
		}
		
		if ($FILEsize1[$file_loop_count] ne $FILEsize2[$file_loop_count]) {
			if ($debugX) {print "not transfering $directory/$files[$file_loop_count]. File size mismatch $FILEsize2[$file_loop_count] != $FILEsize1[$file_loop_count]\n";}
		}

		if ( ($files[$file_loop_count] !~ /out\.|in\.|lost\+found/i) && ($FILEsize1[$file_loop_count] eq $FILEsize2[$file_loop_count]) && (length($files[$file_loop_count]) > 4)) {
			my $recording_id = '';
			my $start_date = '';
			my $lead_id = '';
			my $vicidial_id = '';
			my $ALLfile = $files[$file_loop_count];
			my $SQLFILE = $files[$file_loop_count];
			$SQLFILE =~ s/-all\.wav|-all\.gsm|-all\.ogg|-all\.mp3//gi;

			my $rec_log_db_stmt = "select recording_id, start_time, vicidial_id, lead_id from recording_log where filename=$SQLFILE order by recording_id desc LIMIT 1;";
			$rec_log_sth->execute($SQLFILE) or die "executing: $rec_log_db_stmt ", $dbhA->errstr;
			my $sthArows=$rec_log_sth->rows;
			if ($sthArows > 0) {
				my @aryA = $rec_log_sth->fetchrow_array;
				$recording_id =	"$aryA[0]";
				$start_date   = "$aryA[1]";
				$vicidial_id  = "$aryA[2]";
				$lead_id      = "$aryA[3]";
				$start_date =~ s/ .*//gi;
			}
			$rec_log_sth->finish();

			if ($debug) {
				print "|$camp_check|$recording_id|$start_date|$ALLfile|$SQLFILE|\n";
			}
			
			### are we doing a campaign check
			if ($camp_check) {
				my $vici_log_db_stmt = "select campaign_id from vicidial_log where uniqueid=$vicidial_id and lead_id=$lead_id;";
				$vici_log_sth->execute($vicidial_id, $lead_id) or die "executing: $rec_log_db_stmt ", $dbhA->errstr;
				my $sthArows=$vici_log_sth->rows;
				if ($sthArows > 0) {
					my @aryA = $vici_log_sth->fetchrow_array;
					my $campaign_id = "$aryA[0]";
					
					if($debug){print STDERR "\n|$ALLfile is in the $campaign_id campaign.|\n";}

					# loop through the campaigns they want to transfer
					foreach( @camp_array ) {
						# see if the campaign is in there
						if ( $_ eq $campaign_id ) {
							$transfer_file = 1;
							if($debug){print STDERR "\n|$_ is in the list of campaigns.|\n";}
						}
					}
					if(($debug) && ($transfer_file == 0)) {print STDERR "\n|$campaign_id is not in the list of campaigns.|\n";}			
				}
				$vici_log_sth->finish();				
			} else {
				### are we doing an ingroup check
				if ($ingrp_check) {
					my $clsr_log_db_stmt = "select campaign_id from vicidial_closer_log where closecallid=$vicidial_id and lead_id=$lead_id;";
					$clsr_log_sth->execute($vicidial_id, $lead_id) or die "executing: $rec_log_db_stmt ", $dbhA->errstr;
					my $sthArows=$clsr_log_sth->rows;
					if ($sthArows > 0) {
						my @aryA = $clsr_log_sth->fetchrow_array;
						my $ingroup_id = "$aryA[0]";	
						
						if($debug){print STDERR "\n|$ALLfile is in the $ingroup_id ingroup.|\n";}

						# loop through the ingroups they want to transfer
						foreach( @ingrp_array ) {
							# see if the ingroup is in there
							if ( $_ eq $ingroup_id ) {
								$transfer_file = 1;
								if($debug){print STDERR "\n|$_ is in the list of ingroups.|\n";}
							}
						}
						if(($debug) && ($transfer_file == 0)) {print STDERR "\n|$ingroup_id is not in the list of ingroups.|\n";}
					}
					$clsr_log_sth->finish();
				} else {
					### doing neither so always transfer the file.
					$transfer_file = 1;
				}
			}

			### BEGIN Remote file transfer
			if ($transfer_file) {
				# ping the host to make sure it is alive
				my $ping_good = 0;
				if ($pingtype ne "none") {
					$ping_good = $ping->ping("$ftp_host");
					if($debug){print "Ping result: $ping_good\n";}
				}
				
				### if the ping came back okay or if we are not pinging the server
				if (($ping_good) || ($pingtype eq "none")) {	
					if($debug) {
						print STDERR "Transfering the file\n";
					}
					$transfered_files++;
					
					my $start_date_PATH='';
					my $ftp = Net::FTP->new("$ftp_host", Port => $ftp_port, Debug => $debugX);
					$ftp->login("$ftp_user","$ftp_pass");
					$ftp->mkdir("$ftp_dir");
					$ftp->cwd("$ftp_dir");
					if ($datedir) {
						$ftp->mkdir("$start_date");
						$ftp->cwd("$start_date");
						$start_date_PATH = "$start_date/";
					}
					$ftp->binary();
					$ftp->put("$directory/$ALLfile", "$ALLfile");
					$ftp->quit;
	
					my $update_log_db_stmt = "UPDATE recording_log set location='$url_path/$start_date_PATH$ALLfile' where recording_id='$recording_id';";
					if($debug){print STDERR "\n|$update_log_db_stmt|\n";}
					my $affected_rows = $update_log_sth->execute('$url_path/$start_date_PATH$ALLfile',$recording_id) 
						or die "executing: $rec_log_db_stmt ", $dbhA->errstr;
	
					if (!$test)	{
						if($debugX) {
							print STDERR "Moving file from $directory/$ALLfile to $PATHDONEmonitor/FTP/$ALLfile\n";
						}
						`mv -f "$directory/$ALLfile" "$PATHDONEmonitor/FTP/$ALLfile"`;
					}
					
					if($debugX){
						print STDERR "Transfered $transfered_files files\n";
					}
					
					if ( $transfered_files == $trans_limit) {
						if($debug) {
							print STDERR "Transfer limit of $trans_limit reached breaking out of the loop\n";
						}
						last();
					}	
				} else {
					if($debug){
						print "ERROR: Could not ping server $ftp_host\n";
					}
				}
			}
			### END Remote file transfer

			### sleep for twenty hundredths of a second to not flood the server with disk activity
			usleep(200*1000);
		}
		# keep track of the files that we actually care about listing.
		$files_that_count++;
	} else {
		if($debug) {
			print STDERR "$files[$file_loop_count]'s file name is to short or it is a directory.\n";
		}
	}
	$file_loop_count++;
	# break out of here if we have reached the list_limit
	if ($files_that_count >= $list_limit) {
		last();
	}
}

if ($debug) {print "DONE... EXITING\n\n";}

$dbhA->disconnect();


exit;
