#!/usr/bin/perl

use DBI;

# command line arguments
my $phone_number = shift;
my $exten = shift;
my $cid_name = shift;
my $cid_num = shift;

# check if they typed --help
if (($phone_number eq "--help") || ($exten eq "--help") || ($cid_name eq "--help") || ( $cid_num eq "--help")) {
	print "useage: test_call.pl phone_number extension caller_id_name caller_id_number\n";
	print "example: test_call.pl 917275551212 600 test 7775551212\n";
	exit;
}

# get the current time
my ($sec,$min,$hour,$mday,$mon,$year,$wday,$yday,$isdst) = localtime(time);

$year = ($year + 1900);
$mon++;
if ($mon < 10) {$mon = "0$mon";}
if ($mday < 10) {$mday = "0$mday";}
if ($hour < 10) {$Fhour = "0$hour";}
if ($min < 10) {$min = "0$min";}
if ($sec < 10) {$sec = "0$sec";}

my $date = "$year-$mon-$mday $hour:$min:$sec";

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

$dbhB = DBI->connect("DBI:mysql:$VARDB_database:$VARDB_server:$VARDB_port", "$VARDB_user", "$VARDB_pass")
	or die "Couldn't connect to database: " . DBI->errstr;

# Insert our call record
$stmtB = "INSERT INTO vicidial_manager values( '', '', '$date', 'NEW', 'N', '$VARserver_ip', '', 'Originate', 'TESTCIDBLASTCALL0124', 'Channel: Local/$phone_number@default', 'Context; default', 'Exten: $exten', 'Priority: 1', 'Callerid: \"$cid_name\" <$cid_num>', '', '', '', '', '' );";
print $stmtB . "\n";
$sthB = $dbhB->prepare($stmtB) or die "preparing: ",$dbhB->errstr;
$sthB->execute or die "executing: $stmtB ", $dbhB->errstr;
$sthB->finish();
$dbhB->disconnect();


