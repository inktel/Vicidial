#!/usr/bin/perl
#
# start_asterisk_boot.pl    version 2.0.5
#
# Copyright (C) 2009  Matt Florell <vicidial@gmail.com>    LICENSE: AGPLv2
#
# CHANGES
#
# 60814-1658 - mattf - Added option to start without logging through servers table setting
# 90309-0905 - mattf - Added deleting of asterisk command files
# 90325-2238 - mattf - Rewrote launching of Asterisk, removed command files
# 90506-1443 - mikec - Added the T option to the asterisk command. This enables timestamping.
# 91210-1500 - mattf - Added datetimestamp to astshell screen
#

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
	$i++;
	}

$secX = time();
($sec,$min,$hour,$mday,$mon,$year,$wday,$yday,$isdst) = localtime($secX);
$mon++;
$year = ($year + 1900);
if ($mon < 10) {$mon = "0$mon";}
if ($mday < 10) {$mday = "0$mday";}
if ($hour < 10) {$hour = "0$hour";}
if ($min < 10) {$min = "0$min";}
if ($sec < 10) {$sec = "0$sec";}
$launch_date = "$year$mon$mday$hour$min$sec";

`PERL5LIB="$PATHhome/libs"; export PERL5LIB`;

use DBI;	  

$dbhA = DBI->connect("DBI:mysql:$VARDB_database:$VARDB_server:$VARDB_port", "$VARDB_user", "$VARDB_pass")
 or die "Couldn't connect to database: " . DBI->errstr;

### Grab Server values from the database
$stmtA = "SELECT vd_server_logs FROM servers where server_ip = '$VARserver_ip';";
$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
$sthArows=$sthA->rows;
if ($sthArows > 0)
	{
	@aryA = $sthA->fetchrow_array;
	$DBvd_server_logs =			$aryA[0];
	if ($DBvd_server_logs =~ /Y/)	{$SYSLOG = '1';}
	else {$SYSLOG = '0';}
	}
$sthA->finish();

`ulimit -n 65536`;

if ($SYSLOG) 
	{
	`/usr/bin/screen -d -m -S astershell$launch_date /usr/bin/screen -S astshell$launch_date`;
	print "started screen\n";

	sleep(1);
	`screen -XS astshell$launch_date eval 'stuff "cd /var/log/astguiclient\015"'`;
	print "changed directory\n";

	sleep(1);
	`screen -XS astshell$launch_date eval 'stuff "screen -L -S asterisk\015"'`;
	print "started new screen session\n";

	sleep(1);
	`screen -d astshell$launch_date`;
	`screen -d asterisk`;
	print "detached screens\n";

	sleep(1);
	`screen -XS asterisk eval 'stuff "ulimit -n 65536\015"'`;
	print "raised ulimit open files\n";

	sleep(1);
	`screen -XS asterisk eval 'stuff "/usr/sbin/asterisk -vvvvvvvvvvvvvvvvvvvvvgcT\015"'`;
	print "Asterisk started... screen logging on\n";
	}
else
	{
	`/usr/bin/screen -d -m -S astershell$launch_date /usr/bin/screen -S asterisk`;
	print "started screen\n";

	sleep(1);
	`screen -d asterisk`;
	print "detached screen\n";

	sleep(1);
	`screen -XS asterisk eval 'stuff "ulimit -n 65536\015"'`;
	print "raised ulimit open files\n";

	sleep(1);
	`screen -XS asterisk eval 'stuff "/usr/sbin/asterisk -vvvvgcT\015"'`;
	print "Asterisk started... screen logging off\n";
	}


### Set the conf files to automatically update
$stmtA = "UPDATE servers SET rebuild_conf_files='Y' where server_ip = '$VARserver_ip';";
$affected_rows = $dbhA->do($stmtA);


exit;
