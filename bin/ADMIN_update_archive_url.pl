#!/usr/bin/perl

# ADMIN_update_archive_url.pl - updates the url for the archive server
# in the recording_log table
#
# Copyright (C) 2009  Matt Florell <vicidial@gmail.com>    LICENSE: AGPLv2
#
# CHANGELOG
# 90609-1609 - mikec - created the script

use DBI;

# function to print out the help for this script
sub print_help {
	print "$0 - updates the url in the recording_log\n";
	print "command-line options:\n";
	print "  [--debug]          = shows the sql as it is being executed.\n";
	print "  [--test]           = activates debuging and does not actually\n";
	print "                       execute the updates.\n";
	print "  [--old-server-url] = the old url used to access the recordings.\n";
	print "                       This is a required argument.\n";
	print "  [--new-server-url] = the new url used to access the recordings.\n";
	print "                       This is a required argument.\n\n";

	exit;
}

# default path to astguiclient configuration file:
$PATHconf = '/etc/astguiclient.conf';

# read in the conf file
open(CONFIG, "$PATHconf") || die "can't open $PATHconf: $!\n";
@config = <CONFIG>;
close(CONFIG);
$i=0;
foreach(@config) {
	$line = $config[$i];
	$line =~ s/ |>|\n|\r|\t|\#.*|;.*//gi;
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
	$i++;
}

# initialize the variables
$old_url = "";
$new_url = "";
$DB      = 0;
$TEST    = 0;

# begin parsing run-time options
if ( length($ARGV[0]) > 1 ) {
	$i=0;
	while ($#ARGV >= $i) {
		$args = "$args $ARGV[$i]";
		$i++;
	}

	if ($args =~ /--help/i) {
		&print_help;
	} else {
		# activate debugging
		if ($args =~ /--debug/i) {
			$DB=1;
		}

		# activate test mode
		if ($args =~ /--test/i) {
			$DB=1;
			$TEST=1;
		}

		# get the old server url
		if ($args =~ /--old-server-url=/i) {
			@CLIoldurlARY = split(/--old-server-url=/,$args);
			@CLIoldurlARX = split(/ /,$CLIoldurlARY[1]);
			$old_url = $CLIoldurlARX[0];
		}

		# get the new server url
		if ($args =~ /--new-server-url=/i) {
			@CLInewurlARY = split(/--new-server-url=/,$args);
			@CLInewurlARX = split(/ /,$CLInewurlARY[1]);
			$new_url = $CLInewurlARX[0];
		}
	}
} else {
	# we didnt get any arguments
	&print_help;
}

# make sure they set something
if (( $new_url eq "") || ( $old_url eq "")) {
	&print_help;
}

# connect to the db
$dbhA = DBI->connect("DBI:mysql:$VARDB_database:$VARDB_server:$VARDB_port", "$VARDB_user", "$VARDB_pass")
	or die "Couldn't connect to database: " . DBI->errstr;

# get all the recordings with the old_url
$stmtA = "SELECT recording_id, location from recording_log where location LIKE '$old_url%';";
if ( $DB ) {
	print $stmtA . "\n";
}
$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
$sthArows=$sthA->rows;
$rec_count=0;

# cycle through them
while ($sthArows > $rec_count) {
	@aryA = $sthA->fetchrow_array;

	# get the parameters from the DB
	$rec_id   = "$aryA[0]";
	$location = "$aryA[1]";

	# change the url
	$new_loc = $location;
	$new_loc =~ s/$old_url/$new_url/gi;

	# UPDATE THE recording_log RECORD
	$stmtB = "UPDATE recording_log SET location='$new_loc' where recording_id='$rec_id';";
	if ( $DB ) {
		print $stmtB . "\n";
	}

	# execute the update if we are not in test mode
	if ( $TEST == 0 ) {
		$sthB = $dbhA->prepare($stmtB) or die "preparing: ",$dbhB->errstr;
		$sthB->execute or die "executing: $stmtB ", $dbhB->errstr;
		$sthB->finish();
	}
	$rec_count++;
}

print "Updated $rec_count records in the recording_log table\n";

$sthA->finish();

$dbhA->disconnect();