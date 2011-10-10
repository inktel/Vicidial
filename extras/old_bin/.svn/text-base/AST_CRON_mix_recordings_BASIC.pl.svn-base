#!/usr/bin/perl
#
# AST_CRON_mix_recordings_BASIC.pl
# runs every 5 minutes and mixes the call recordings in the monitor together
# and puts the resulting ALL file into the DONE directory in the "monitor" dir
# 
# soxmix is REQUIRED to use this script, soxmix is available only as part of 
# the sox audio package, and only in newer versions. Make sure you have soxmix
# installed properly and in your path
# 
# put an entry into the cron of of your asterisk machine to run this script 
# every 5 minutes or however often you desire
#
# make sure that the following directories exist:
# /var/spool/asterisk/monitor		# default Asterisk recording directory
# /var/spool/asterisk/monitor/DONE	# where the combined files are put
# /var/spool/asterisk/monitor/ORIG	# where the original in/out files are put
# 
# This program assumes that recordings are saved as .wav
# should be easy to change this code if you use .gsm instead
# 
# This program also sends the ALL combined file to an FTP server for archival
# purposes, you can comment out the Net::Ping and Net::FTP lines as well as the
# file transfer section of the code to deactivate remote copying of the
# recording files
#
# Copyright (C) 2006  Matt Florell <vicidial@gmail.com>    LICENSE: GPLv2
#
# 
# 51021-1058 - Added quotes around CLI executed commands
# 51122-1456 - Added soxmix binary path check
# 60318-0921 - Added ability to mix gsm audio files
# 60807-1308 - Modified to use /etc/astguiclient.conf for settings 
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
	$i++;
	}

# Customized Variables
$server_ip = $VARserver_ip;		# Asterisk server IP

### directory where in/out recordings are saved to by Asterisk
$dir1 = "$PATHmonitor";

$soxmixbin = '';
if ( -e ('/usr/bin/soxmix')) {$soxmixbin = '/usr/bin/soxmix';}
else 
	{
	if ( -e ('/usr/local/bin/soxmix')) {$soxmixbin = '/usr/local/bin/soxmix';}
	else
		{
		print "Can't find soxmix binary! Trying sox...\n";
		if ( -e ('/usr/bin/sox')) {$soxmixbin = '/usr/bin/sox -m';}
		else 
			{
			if ( -e ('/usr/local/bin/sox')) {$soxmixbin = '/usr/local/bin/sox -m';}
			else
				{
				print "Can't find sox binary! Exiting...\n";
				exit;
				}
			}
		}
	}

 opendir(FILE, "$dir1/");
 @FILES = readdir(FILE);

$i=0;
foreach(@FILES)
   {
	$size1 = 0;
	$size2 = 0;

	if ( (length($FILES[$i]) > 4) && (!-d $FILES[$i]) )
		{

		$size1 = (-s "$dir1/$FILES[$i]");
		if ($v) {print "$FILES[$i] $size1\n";}
		sleep(1);
		$size2 = (-s "$dir1/$FILES[$i]");
		if ($v) {print "$FILES[$i] $size2\n\n";}


		if ( ($FILES[$i] !~ /out\.wav|out\.gsm/i) && ($size1 eq $size2) && (length($FILES[$i]) > 4))
			{
			$INfile = $FILES[$i];
			$OUTfile = $FILES[$i];
			$OUTfile =~ s/-in\.wav/-out.wav/gi;
			$OUTfile =~ s/-in\.gsm/-out.gsm/gi;
			$ALLfile = $FILES[$i];
			$ALLfile =~ s/-in\.wav/-all.wav/gi;
			$ALLfile =~ s/-in\.gsm/-all.gsm/gi;

		if ($v) {print "|$INfile|    |$OUTfile|     |$ALLfile|\n\n";}

			`$soxmixbin "$dir1/$INfile" "$dir1/$OUTfile" "$dir1/$ALLfile"`;
		if ($v) {print "|$INfile|    |$OUTfile|     |$ALLfile|\n\n";}
			if (!$T)
				{
				`mv -f "$dir1/$INfile" "$dir1/ORIG/$INfile"`;
				`mv -f "$dir1/$OUTfile" "$dir1/ORIG/$OUTfile"`;
				`mv -f "$dir1/$ALLfile" "$dir1/DONE/$ALLfile"`;
				}
			}
		}
	$i++;
	}

if ($v) {print "DONE... EXITING\n\n";}

exit;
