#!/usr/bin/perl

use strict;
use DBI;
use Cwd;

my $pwd = cwd();

# change this to the name of the csv file that you are reading in.
my $datafile = "carrier_rates.csv";

# change this to the string needed to place the call through this carrier
my $carrier_string = "SIP/provider1";

# default path to astguiclient configuration file:
my $PATHconf = '/etc/astguiclient.conf';
my $VARDB_server = 'localhost';
my $VARDB_database = 'asterisk';
my $VARDB_user = 'cron';
my $VARDB_pass = '1234';
my $VARDB_port = '3306';

print "Reading Vicidial configs\n";

open( conf, "$PATHconf" ) || die "can't open $PATHconf: $!\n";
my @conf = <conf>;
close(conf);
my $i = 0;
foreach (@conf) {
        my $line = $conf[$i];
        $line =~ s/ |>|\n|\r|\t|\#.*|;.*//gi;
        if ( $line =~ /^VARDB_server/ ) {
                $VARDB_server = $line;
                $VARDB_server =~ s/.*=//gi;
        }
        if ( $line =~ /^VARDB_database/ ) {
                $VARDB_database = $line;
                $VARDB_database =~ s/.*=//gi;
        }
        if ( $line =~ /^VARDB_user/ ) {
                $VARDB_user = $line;
                $VARDB_user =~ s/.*=//gi;
        }
        if ( $line =~ /^VARDB_pass/ ) {
                $VARDB_pass = $line;
                $VARDB_pass =~ s/.*=//gi;
        }
        if ( $line =~ /^VARDB_port/ ) {
                $VARDB_port = $line;
                $VARDB_port =~ s/.*=//gi;
        }
        $i++;
}

print "Opening connection to the database\n";

my $dbhA = DBI->connect(
        "DBI:mysql:$VARDB_database:$VARDB_server:$VARDB_port",
        "$VARDB_user",
        "$VARDB_pass"
) or die "Couldn't connect to database: " . DBI->errstr;


print "opening the file $datafile\n";

open( DATAFILE, "$datafile" ) or die "no such file $datafile";

my $npanxx; 
my $state; 
my $lata5;
my $lata_name;
my $ocn;
my $rate;
my $billing_zone;
my $nothing;

my $stmtA = "INSERT IGNORE INTO lcr ( npanxx, rate, carrier_string  ) VALUES ( ?,?,? );";

print "Inserting data into the lcr table\n";

my $index = 0;
while (<DATAFILE>) {
        my $line = $_;
        $line =~ s/\r\n/\n/gi;
        chomp($line);
        if ( $index > 0 ) {
                # parse the comma delimited line
                ( $npanxx, $state, $lata5, $lata_name, $ocn, $rate, $billing_zone, $nothing ) = split ( ",", $line);

                # remove the extra white space
                chomp( $npanxx );
                chomp( $rate );
                
                # remove any quotes that might be in the csv
                $npanxx =~ s/\"//g;
                $rate =~ s/\"//g;

                # put it into the db
                my $sthA = $dbhA->prepare_cached($stmtA) or die "preparing: ", $dbhA->errstr;
                $sthA->execute( $npanxx, $rate, $carrier_string) or die "executing: $stmtA ", $dbhA->errstr;
                $sthA->finish();

        }
        $index++;
}

close( data );
