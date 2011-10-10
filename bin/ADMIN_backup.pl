#!/usr/bin/perl
#
# ADMIN_backup.pl    version 2.4
#
# DESCRIPTION:
# Backs-up the asterisk database, conf/agi/sounds/bin files 
#
# Copyright (C) 2010  Matt Florell <vicidial@gmail.com>    LICENSE: AGPLv2
#
# CHANGELOG
#
# 80316-2211 - First Build
# 80317-1609 - Added Sangoma conf file backup and changed FTP settings
# 80328-0135 - Do not attempt to archive /etc/my.cnf is --without-db flag is set
# 80611-0549 - Added DB option to backup all tables except for log tables
# 90620-1851 - Moved mysqldump bin lookup to database backup section
# 100211-0910 - Added crontab backup and voicemail backup option
# 100817-1202 - Fixed test option bug
# 101208-0452 - Added checks for zaptel and dahdi conf files
#

$secT = time();
$secX = time();
($sec,$min,$hour,$mday,$mon,$year,$wday,$yday,$isdst) = localtime(time);
$year = ($year + 1900);
$mon++;
if ($mon < 10) {$mon = "0$mon";}
if ($mday < 10) {$mday = "0$mday";}
if ($hour < 10) {$Fhour = "0$hour";}
if ($min < 10) {$min = "0$min";}
if ($sec < 10) {$sec = "0$sec";}
$file_date = "$year-$mon-$mday";
$now_date = "$year-$mon-$mday $hour:$min:$sec";
$VDL_date = "$year-$mon-$mday 00:00:01";

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
		print "  [--db-only] = only backup the database\n";
		print "  [--db-without-logs] = do not backup the log tables in the database\n";
		print "  [--conf-only] = only backup the asterisk conf files\n";
		print "  [--without-db] = do not backup the database\n";
		print "  [--without-conf] = do not backup the conf files\n";
		print "  [--without-web] = do not backup web files\n";
		print "  [--without-sounds] = do not backup asterisk sounds\n";
		print "  [--without-voicemail] = do not backup asterisk voicemail\n";
		print "  [--without-crontab] = do not backup crontab\n";
		print "  [--ftp-transfer] = Transfer backup to FTP server\n";
		print "  [--debugX] = super debug\n";
		print "  [--debug] = debug\n";
		print "  [--test] = test\n";
		exit;
		}
	else
		{
		if ($args =~ /--debug/i)
			{
			$DB=1; $FTPdebug=1;
			print "\n----- DEBUG -----\n\n";
			}
		if ($args =~ /--debugX/i)
			{
			$DBX=1;
			print "\n----- SUPER DEBUG -----\n\n";
			}
		if ($args =~ /--test/i)
			{
			$T=1;   $TEST=1;
			print "\n-----TESTING -----\n\n";
			}
		if ($args =~ /--db-only/i)
			{
			$db_only=1;
			print "\n----- Backup Database Only -----\n\n";
			}
		if ($args =~ /--db-without-logs/i)
			{
			$db_without_logs=1;
			print "\n----- Backup Database Without Logs -----\n\n";
			}
		if ($args =~ /--conf-only/i)
			{
			$conf_only=1;
			print "\n----- Conf Files Backup Only -----\n\n";
			}
		if ($args =~ /--without-db/i)
			{
			$without_db=1;
			print "\n----- No Database Backup -----\n\n";
			}
		if ($args =~ /--without-conf/i)
			{
			$without_conf=1;
			print "\n----- No Conf Files Backup -----\n\n";
			}
		if ($args =~ /--without-sounds/i)
			{
			$without_sounds=1;
			print "\n----- No Sounds Backup -----\n\n";
			}
		if ($args =~ /--without-web/i)
			{
			$without_web=1;
			print "\n----- No web files Backup -----\n\n";
			}
		if ($args =~ /--without-voicemail/i)
			{
			$without_voicemail=1;
			print "\n----- No voicemail files Backup -----\n\n";
			}
		if ($args =~ /--without-crontab/i)
			{
			$without_crontab=1;
			print "\n----- No crontab Backup -----\n\n";
			}
		if ($args =~ /--ftp-transfer/i)
			{
			$ftp_transfer=1;
			print "\n----- FTP transfer -----\n\n";
			}
		}
	}
else
	{
	print "no command line options set\n";
	}

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

if (!$ARCHIVEpath) {$ARCHIVEpath = "$PATHlogs/archive";}
if (!$VARDB_port) {$VARDB_port='3306';}

### find tar binary to do the archiving
$tarbin = '';
if ( -e ('/usr/bin/tar')) {$tarbin = '/usr/bin/tar';}
else 
	{
	if ( -e ('/usr/local/bin/tar')) {$tarbin = '/usr/local/bin/tar';}
	else
		{
		if ( -e ('/bin/tar')) {$tarbin = '/bin/tar';}
		else
			{
			print "Can't find tar binary! Exiting...\n";
			exit;
			}
		}
	}

### find gzip binary to do the archiving
$gzipbin = '';
if ( -e ('/usr/bin/gzip')) {$gzipbin = '/usr/bin/gzip';}
else 
	{
	if ( -e ('/usr/local/bin/gzip')) {$gzipbin = '/usr/local/bin/gzip';}
	else
		{
		if ( -e ('/bin/gzip')) {$gzipbin = '/bin/gzip';}
		else
			{
			print "Can't find gzip binary! Exiting...\n";
			exit;
			}
		}
	}

$conf='_CONF_';
$sangoma='_SANGOMA_';
$linux='_LINUX_';
$bin='_BIN_';
$web='_WEB_';
$sounds='_SOUNDS_';
$voicemail='_VOICEMAIL_';
$all='_ALL_';
$tar='.tar';
$gz='.gz';
$sgSTRING='';

`cd $ARCHIVEpath`;
`mkdir $ARCHIVEpath/temp`;

if ( ($without_db < 1) && ($conf_only < 1) )
	{
	### find mysqldump binary to do the database dump
	$mysqldumpbin = '';
	if ( -e ('/usr/bin/mysqldump')) {$mysqldumpbin = '/usr/bin/mysqldump';}
	else 
		{
		if ( -e ('/usr/local/mysql/bin/mysqldump')) {$mysqldumpbin = '/usr/local/mysql/bin/mysqldump';}
		else
			{
			if ( -e ('/bin/mysqldump')) {$mysqldumpbin = '/bin/mysqldump';}
			else
				{
				print "Can't find mysqldump binary! MySQL backups will not work...\n";
				}
			}
		}

	### BACKUP THE MYSQL FILES ON THE DB SERVER ###
	if ($db_without_logs)
		{
		use DBI;
			
		### connect to MySQL database defined in the conf file
		$dbhA = DBI->connect("DBI:mysql:$VARDB_database:$VARDB_server:$VARDB_port", "$VARDB_user", "$VARDB_pass")
		or die "Couldn't connect to database: " . DBI->errstr;

		$stmtA = "show tables;";
		$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
		$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
		$sthArows=$sthA->rows;
		$rec_count=0;
		$log_tables='';
		$archive_tables='';
		while ($sthArows > $rec_count)
			{
			@aryA = $sthA->fetchrow_array;
			if ($aryA[0] =~ /_log|server_performance|vicidial_ivr|vicidial_hopper|vicidial_manager|web_client_sessions|imm_outcomes/) 
				{
				$log_tables .= " $aryA[0]";
				}
			else 
				{
				$archive_tables .= " $aryA[0]";
				}
			$rec_count++;
			}
		$sthA->finish();

		$dump_non_log_command = "$mysqldumpbin --user=$VARDB_user --password=$VARDB_pass --lock-tables --flush-logs $VARDB_database $archive_tables | $gzipbin > $ARCHIVEpath/temp/$VARserver_ip$VARDB_database$wday.gz";
		$dump_log_command = "$mysqldumpbin --user=$VARDB_user --password=$VARDB_pass --lock-tables --flush-logs --no-data --no-create-db $VARDB_database $log_tables | $gzipbin > $ARCHIVEpath/temp/LOGS_$VARserver_ip$VARDB_database$wday.gz";

		if ($DBX) {print "$dump_non_log_command\n$dump_log_command";}
		`$dump_non_log_command`;
		`$dump_log_command`;
		}
	else
		{
		if ($DBX) {print "$mysqldumpbin --user=$VARDB_user --password=$VARDB_pass --lock-tables --flush-logs $VARDB_database | $gzipbin > $ARCHIVEpath/temp/$VARserver_ip$VARDB_database$wday.gz\n";}
		`$mysqldumpbin --user=$VARDB_user --password=$VARDB_pass --lock-tables --flush-logs $VARDB_database | $gzipbin > $ARCHIVEpath/temp/$VARserver_ip$VARDB_database$wday.gz`;
		}
	}

if ( ($without_conf < 1) && ($db_only < 1) )
	{
	### BACKUP THE ASTERISK CONF FILES ON THE SERVER ###
	$zapdahdi='';
	if (-e "/etc/zaptel.conf") {$zapdahdi .= " /etc/zaptel.conf";}
	if (-e "/etc/dahdi/system.conf") {$zapdahdi .= " /etc/dahdi";}
	if ($DBX) {print "$tarbin cf $ARCHIVEpath/temp/$VARserver_ip$conf$wday$tar /etc/astguiclient.conf $zapdahdi /etc/asterisk\n";}
	`$tarbin cf $ARCHIVEpath/temp/$VARserver_ip$conf$wday$tar /etc/astguiclient.conf $zapdahdi /etc/asterisk`;


	### BACKUP THE WANPIPE CONF FILES(if there are any) ###
	if ( -e ('/etc/wanpipe/wanpipe1.conf')) 
		{
		$sgSTRING = '/etc/wanpipe/wanpipe1.conf ';
		if ( -e ('/etc/wanpipe/wanpipe2.conf')) {$sgSTRING .= '/etc/wanpipe/wanpipe2.conf ';}
		if ( -e ('/etc/wanpipe/wanpipe3.conf')) {$sgSTRING .= '/etc/wanpipe/wanpipe3.conf ';}
		if ( -e ('/etc/wanpipe/wanpipe4.conf')) {$sgSTRING .= '/etc/wanpipe/wanpipe4.conf ';}
		if ( -e ('/etc/wanpipe/wanpipe5.conf')) {$sgSTRING .= '/etc/wanpipe/wanpipe5.conf ';}
		if ( -e ('/etc/wanpipe/wanpipe6.conf')) {$sgSTRING .= '/etc/wanpipe/wanpipe6.conf ';}
		if ( -e ('/etc/wanpipe/wanpipe7.conf')) {$sgSTRING .= '/etc/wanpipe/wanpipe7.conf ';}
		if ( -e ('/etc/wanpipe/wanpipe8.conf')) {$sgSTRING .= '/etc/wanpipe/wanpipe8.conf ';}
		if ( -e ('/etc/wanpipe/wanrouter.rc')) {$sgSTRING .= '/etc/wanpipe/wanrouter.rc ';}

		if ($DBX) {print "$tarbin cf $ARCHIVEpath/temp/$VARserver_ip$sangoma$wday$tar $sgSTRING\n";}
		`$tarbin cf $ARCHIVEpath/temp/$VARserver_ip$sangoma$wday$tar $sgSTRING`;
		}

	### BACKUP OTHER CONF FILES ON THE SERVER ###
	$files = "";

	if ($without_db < 1)
		{
		if ( -e ('/etc/my.cnf')) {$files .= "/etc/my.cnf ";}
		}
	if ($without_crontab < 1)
		{
		`crontab -l > /etc/crontab_snapshot`;
		if ( -e ('/etc/crontab_snapshot')) {$files .= "/etc/crontab_snapshot ";}
		}

	if ( -e ('/etc/hosts')) {$files .= "/etc/hosts ";}
	if ( -e ('/etc/rc.d/rc.local')) {$files .= "/etc/rc.d/rc.local ";}
	if ( -e ('/etc/resolv.conf')) {$files .= "/etc/resolv.conf ";}

	if ($DBX) {print "$tarbin cf $ARCHIVEpath/temp/$VARserver_ip$linux$wday$tar $files\n";}
	`$tarbin cf $ARCHIVEpath/temp/$VARserver_ip$linux$wday$tar $files`;
	}

if ( ($conf_only < 1) && ($db_only < 1) && ($without_web < 1) )
	{
	### BACKUP THE WEB FILES ON THE SERVER ###
	if ($DBX) {print "$tarbin cf $ARCHIVEpath/temp/$VARserver_ip$web$wday$tar $PATHweb\n";}
	`$tarbin cf $ARCHIVEpath/temp/$VARserver_ip$web$wday$tar $PATHweb`;
	}

if ( ($conf_only < 1) && ($db_only < 1) )
	{
	### BACKUP THE ASTGUICLIENT AND AGI FILES ON THE SERVER ###
	if ($DBX) {print "$tarbin cf $ARCHIVEpath/temp/$VARserver_ip$bin$wday$tar $PATHagi $PATHhome\n";}
	`$tarbin cf $ARCHIVEpath/temp/$VARserver_ip$bin$wday$tar $PATHagi $PATHhome`;
	}

if ( ($conf_only < 1) && ($db_only < 1) && ($without_sounds < 1) )
	{
	### BACKUP THE ASTERISK SOUNDS ON THE SERVER ###
	if ($DBX) {print "$tarbin cf $ARCHIVEpath/temp/$VARserver_ip$sounds$wday$tar $PATHsounds\n";}
	`$tarbin cf $ARCHIVEpath/temp/$VARserver_ip$sounds$wday$tar $PATHsounds`;
	}

if ( ($conf_only < 1) && ($db_only < 1) && ($without_voicemail < 1) )
	{
	### BACKUP THE ASTERISK VOICEMAIL ON THE SERVER ###
	if ($DBX) {print "$tarbin cf $ARCHIVEpath/temp/$VARserver_ip$voicemail$wday$tar /var/spool/asterisk/voicemail\n";}
	`$tarbin cf $ARCHIVEpath/temp/$VARserver_ip$voicemail$wday$tar /var/spool/asterisk/voicemail`;
	}

### PUT EVERYTHING TOGETHER TO BE COMPRESSED ###
if ($DBX) {print "$tarbin cf $ARCHIVEpath/$VARserver_ip$all$wday$tar $ARCHIVEpath/temp\n";}
`$tarbin cf $ARCHIVEpath/$VARserver_ip$all$wday$tar $ARCHIVEpath/temp`;

### REMOVE OLD GZ FILE
if ($DBX) {print "rm -f $ARCHIVEpath/$VARserver_ip$all$wday$tar$gz\n";}
`rm -f $ARCHIVEpath/$VARserver_ip$all$wday$tar$gz`;

### COMPRESS THE ALL FILE ###
if ($DBX) {print "$gzipbin -9 $ARCHIVEpath/$VARserver_ip$all$wday$tar\n";}
`$gzipbin -9 $ARCHIVEpath/$VARserver_ip$all$wday$tar`;

### REMOVE TEMP FILES ###
if ($DBX) {print "rm -fR $ARCHIVEpath/temp\n";}
`rm -fR $ARCHIVEpath/temp`;


#### FTP to the Backup server and upload the final file
if ($ftp_transfer > 0)
	{
	use Net::FTP;
	$ftp = Net::FTP->new("$VARREPORT_host", Port => "$VARREPORT_port", Debug => "$FTPdebug");
	$ftp->login("$VARREPORT_user","$VARREPORT_pass");
	$ftp->cwd("$VARREPORT_dir");
	$ftp->binary();
	$ftp->put("$ARCHIVEpath/$VARserver_ip$all$wday$tar$gz", "$VARserver_ip$all$wday$tar$gz");
	$ftp->quit;
	}


if ($DBX) {print "DONE, Exiting...\n";}

exit;
