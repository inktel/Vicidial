#!/usr/bin/perl
#
# ADMIN_audio_store_sync.pl      version 2.4
#
# DESCRIPTION:
# syncronizes audio between audio store and this server
#
# 
# Copyright (C) 2010  Matt Florell <vicidial@gmail.com>    LICENSE: AGPLv2
#
# CHANGELOG
# 90513-0458 - First Build
# 90518-2107 - Added force-upload option
# 90831-1349 - Added music-on-hold sync
# 100621-1018 - Added admin_web_directory variable use
# 100824-0032 - Fixed issue with first MoH file being skipped when playing in non-random order
# 101217-2137 - Small fix for admin directories not directly off of the webroot
#

# constants
$DB=0;
$US='__';
$MT[0]='';
$uploaded=0;
$downloaded=0;
$force_moh_rebuild=0;
$new_file_moh_rebuild=0;

$secT = time();
$now_date_epoch = $secT;
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
$inactive_epoch = ($secT - 60);
$HHMM = "$hour$min";

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
		print "allowed run time options(must stay in this order):\n";
		print "  [--debug] = debug\n";
		print "  [--debugX] = super debug\n";
		print "  [-t] = test\n";
		print "  [--upload] = upload audio not found on audio store\n";
		print "  [--force-download] = force download of everything from audio store\n";
		print "  [--force-upload] = force upload of all local audio files to the audio store\n";
		print "  [--settings-override] = ignore database settings and run sync anyway\n";
		print "  [--force-moh-rebuild] = ignore database settings and rebuild Music On Hold\n";
		print "\n";
		exit;
		}
	else
		{
		if ($args =~ /--debug/i)
			{
			$DB=1;
			print "\n----- DEBUG -----\n\n";
			}
		if ($args =~ /-upload/i)
			{
			$upload=1;
			if ($DB) {print "\n----- UPLOAD -----\n\n";}
			}
		if ($args =~ /--force-download/i)
			{
			$force_download=1;
			if ($DB) {print "\n----- FORCE DOWNLOAD -----\n\n";}
			}
		if ($args =~ /--force-upload/i)
			{
			$force_upload=1;
			if ($DB) {print "\n----- FORCE UPLOAD -----\n\n";}
			}
		if ($args =~ /--settings-override/i)
			{
			$settings_override=1;
			if ($DB) {print "\n----- SETTINGS OVERRIDE -----\n\n";}
			}
		if ($args =~ /--force-moh-rebuild/i)
			{
			$force_moh_rebuild=1;
			if ($DB) {print "\n----- FORCE MUSIC ON HOLD REBUILD -----\n\n";}
			}
		if ($args =~ /--debugX/i)
			{
			$DBX=1;
			if ($DB) {print "\n----- SUPER DEBUG -----\n\n";}
			}
		if ($args =~ /-t/i)
			{
			$T=1;   $TEST=1;
			if ($DB) {print "\n-----TESTING -----\n\n";}
			}
		}
	}
else
	{
#	print "no command line options set\n";
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

if (!$VASLOGfile) {$VASLOGfile = "$PATHlogs/audiostore.$year-$mon-$mday";}
if (!$VARDB_port) {$VARDB_port='3306';}

use DBI;	  

$dbhA = DBI->connect("DBI:mysql:$VARDB_database:$VARDB_server:$VARDB_port", "$VARDB_user", "$VARDB_pass")
 or die "Couldn't connect to database: " . DBI->errstr;


### Grab Server values from the database
$stmtA = "SELECT active_asterisk_server FROM servers where server_ip = '$VARserver_ip';";
$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
$sthArows=$sthA->rows;
if ($sthArows > 0)
	{
	@aryA = $sthA->fetchrow_array;
	$active_asterisk_server =	$aryA[0];
	}
$sthA->finish();

### Grab system_settings values from the database
$web_prefix='';
$stmtA = "SELECT sounds_central_control_active,sounds_web_server,sounds_web_directory,admin_web_directory FROM system_settings;";
$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
$sthArows=$sthA->rows;
if ($sthArows > 0)
	{
	@aryA = $sthA->fetchrow_array;
	$sounds_central_control_active =	$aryA[0];
	$sounds_web_server =				$aryA[1];
	$sounds_web_directory =				$aryA[2];
	$admin_web_directory =				$aryA[3];
	if ($admin_web_directory =~ /\//)
			{
			$web_prefix = $admin_web_directory;
			$web_prefix =~ s/\/.*/\//gi;
			}
	}
$sthA->finish();

if ( ( ($sounds_central_control_active < 1) || ($active_asterisk_server !~ /Y/) ) && ($settings_override < 1) )
	{
	print "Audio Sync Settings not active, Exiting\n";
	exit;
	}

$stmtA="UPDATE servers SET sounds_update='N' where server_ip='$VARserver_ip';";
$affected_rows = $dbhA->do($stmtA);




### find wget binary
$wgetbin = '';
if ( -e ('/bin/wget')) {$wgetbin = '/bin/wget';}
else 
	{
	if ( -e ('/usr/bin/wget')) {$wgetbin = '/usr/bin/wget';}
	else 
		{
		if ( -e ('/usr/local/bin/wget')) {$wgetbin = '/usr/local/bin/wget';}
		else
			{
			print "Can't find wget binary! Exiting...\n";
			exit;
			}
		}
	}

### find curl binary
$curlbin = '';
if ( -e ('/bin/curl')) {$curlbin = '/bin/curl';}
else 
	{
	if ( -e ('/usr/bin/curl')) {$curlbin = '/usr/bin/curl';}
	else 
		{
		if ( -e ('/usr/local/bin/curl')) {$curlbin = '/usr/local/bin/curl';}
		else
			{
			print "Can't find curl binary! Exiting...\n";
			exit;
			}
		}
	}


$URL = "http://$sounds_web_server/$admin_web_directory/audio_store.php?action=LIST&audio_server_ip=$VARserver_ip";

$URL =~ s/&/\\&/gi;
if ($DB) 
	{print "\n$URL\n";}

$audio_list_file = '/tmp/audio_store_list.txt';
`rm -f $audio_list_file`;
`$wgetbin -q --output-document=$audio_list_file $URL `;

open(list, "$audio_list_file") || die "can't open $audio_list_file: $!\n";
@list = <list>;
close(list);

opendir(sounds, "$PATHsounds");
@sounds= readdir(sounds); 
closedir(sounds);



####### BEGIN download of audio files
if ($DB > 0) {print "REMOTE AUDIO FILES:\n";}

$i=0;
while ($i <= $#list)
	{
	chomp($list[$i]);
	@file_data = split(/\t/, $list[$i]);
	$filename =		$file_data[1];
	$filedate =		$file_data[2];
	$filesize =		$file_data[3];
	$fileepoch =	$file_data[4];
	if ($DB > 0) {print "$i   $filename     $filedate     $filesize     $fileepoch\n";}

	$k=0;
	$found_file=0;
	while ($k <= $#sounds)
		{
		chomp($sounds[$k]);
		$soundname =	$sounds[$k];
		$soundsize =	(-s "$PATHsounds/$sounds[$k]");

		if ( ($filename eq "$soundname") && ($filesize eq "$soundsize") )
			{
			$found_file++;
			}
		$k++;
		}

	if ( ($found_file < 1) || ($force_download > 0) )
		{
		`$wgetbin -q --output-document=$PATHsounds/$filename http://$sounds_web_server/$web_prefix$sounds_web_directory/$filename`;
		$event_string = "DOWNLOADING: $filename     $filesize";
		if ($DB > 0) {print "          $event_string\n";}
		&event_logger;

		$downloaded++;
		$new_file_moh_rebuild++;
		}
	else
		{
		if ($DB > 0) {print "     FILE FOUND: $filename\n";}
		}
	$i++;
	}
####### END download of audio files

$total_files = $i;



`rm -f $audio_list_file`;
`$wgetbin -q --output-document=$audio_list_file $URL `;

open(list, "$audio_list_file") || die "can't open $audio_list_file: $!\n";
@list = <list>;
close(list);

opendir(sounds, "$PATHsounds");
@sounds= readdir(sounds); 
closedir(sounds);





####### BEGIN upload of audio files
if ($upload > 0)
	{
	if ($DB > 0) {print "LOCAL AUDIO FILES:\n";}

	$k=0;
	while ($k <= $#sounds)
		{
		chomp($sounds[$k]);
		if ($sounds[$k] =~ /\.wav$|\.gsm$/)
			{
			$soundname =	$sounds[$k];
			$sounddate =	(-M "$PATHsounds/$sounds[$k]");
			$soundsize =	(-s "$PATHsounds/$sounds[$k]");
			$soundsec =	($sounddate * 86400);
			$soundepoch =	($secT - $soundsec);

			if ($DB > 0) {print "$k   $soundname     $sounddate     $soundsize     $soundepoch\n";}

			$i=0;
			$found_file=0;
			while ($i <= $#list)
				{
				chomp($list[$i]);
				@file_data = split(/\t/, $list[$i]);
				$filename =		$file_data[1];
				$filedate =		$file_data[2];
				$filesize =		$file_data[3];
				$fileepoch =	$file_data[4];

				if ( ($filename eq "$soundname") && ($filesize eq "$soundsize") )
					{
					$found_file++;
					}
				$i++;
				}

			if ( ($found_file < 1) || ($force_upload > 0) )
				{
				$curloptions = "-s 'http://$sounds_web_server/$admin_web_directory/audio_store.php?action=AUTOUPLOAD&audio_server_ip=$VARserver_ip' -F \"audiofile=\@$PATHsounds/$soundname\"";
				`$curlbin $curloptions`;
				$event_string = "UPLOADING: $soundname     $soundsize";
				if ($DB > 0) {print "          $event_string\n|$curlbin $curloptions|\n";}
				&event_logger;

				$uploaded++;
				}
			else
				{
				if ($DB > 0) {print "     FILE FOUND: $soundname\n";}
				}
			}
		$k++;
		}
	}
####### END upload of audio files




###### If audio was uploaded from this server, set all other servers to update sounds next minute
if ($uploaded > 0)
	{
	$stmtA="UPDATE servers SET sounds_update='Y' where server_ip NOT IN('$VARserver_ip');";
	$affected_rows = $dbhA->do($stmtA);
	}





###### BEGIN Music on hold rebuild #####
$wav = '.wav';
$gsm = '.gsm';

$stmtA = "SELECT rebuild_music_on_hold FROM servers where server_ip = '$VARserver_ip';";
$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
$sthArows=$sthA->rows;
if ($sthArows > 0)
	{
	@aryA = $sthA->fetchrow_array;
	$rebuild_music_on_hold = $aryA[0];
	}
$sthA->finish();

if ( ($force_moh_rebuild > 0) || ($new_file_moh_rebuild > 0) || ($rebuild_music_on_hold =~ /Y/i) )
	{
	$stmtA="UPDATE servers SET rebuild_music_on_hold='N' where server_ip = '$VARserver_ip';";
	$affected_rows = $dbhA->do($stmtA);

	### Find music on hold contexts to remove
	$stmtA = "SELECT moh_id FROM vicidial_music_on_hold where remove='Y' and moh_id NOT IN('astdb','sounds','agi-bin','keys');";
	$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
	$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
	$sthArowsM=$sthA->rows;
	@remove_moh_id=@MT;
	$j=0;
	while ($sthArowsM > $j)
		{
		@aryA = $sthA->fetchrow_array;
		$remove_moh_id[$j] =	$aryA[0];
		$j++;
		}
	$sthA->finish();

	### Remove flagged music on hold contexts
	$j=0;
	while ($sthArowsM > $j)
		{
		$stmtA="DELETE from vicidial_music_on_hold where moh_id='$remove_moh_id[$j]' and remove='Y' and moh_id NOT IN('astdb','sounds','agi-bin','keys');";
		$affected_rowsX = $dbhA->do($stmtA);

		$stmtB="DELETE from vicidial_music_on_hold_files where moh_id='$remove_moh_id[$j]' and moh_id NOT IN('astdb','sounds','agi-bin','keys');";
		$affected_rowsY = $dbhA->do($stmtB);

		if ($affected_rowsX > 0)
			{`rm -fr /var/lib/asterisk/$remove_moh_id[$j]`;}

		if ($DBX)
			{
			print "Deleting Existing MoH: $affected_rowsX|$affected_rowsY|$stmtA|$stmtB|rm -fr /var/lib/asterisk/$remove_moh_id[$j]\n";
			}

		$j++;
		}

	### Find active music on hold contexts
	$stmtA = "SELECT moh_id,moh_name,random FROM vicidial_music_on_hold where active='Y' and moh_id NOT IN('astdb','sounds','agi-bin','keys');";
	$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
	$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
	$sthArowsC=$sthA->rows;
	@remove_moh_id=@MT;
	$j=0;
	while ($sthArowsC > $j)
		{
		@aryA = $sthA->fetchrow_array;
		$moh_id[$j] =	$aryA[0];
		$moh_name[$j] =	$aryA[1];
		$random[$j] =	$aryA[2];
		$j++;
		}
	$sthA->finish();

	### Go through each context and check the files in the folders
	$j=0;
	while ($sthArowsC > $j)
		{
		$filelist_names = '|';
		$MoH_directory = "/var/lib/asterisk/$moh_id[$j]";
		### Check if directory exists, if not, create it
		if ( -e ("$MoH_directory")) {$mohpath = "$MoH_directory";}
		else
			{
			`mkdir $MoH_directory`;
			$mohpath = "$MoH_directory";
			if ($DBX)
				{
				print "Creating New MoH: $MoH_directory\n";
				}
			}

		opendir(sounds, "$PATHsounds");
		@sounds= readdir(sounds); 
		closedir(sounds);

		if (!-e "$MoH_directory/0000_sip-silence.gsm")
			{`cp $PATHsounds/sip-silence.gsm $MoH_directory/0000_sip-silence.gsm`;}

		### copy over files that are not in place currently
		$stmtA = "SELECT filename,rank FROM vicidial_music_on_hold_files where moh_id='$moh_id[$j]' order by rank;";
		$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
		$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
		$sthArowsF=$sthA->rows;
		@remove_moh_id=@MT;
		$m=0;
		while ($sthArowsF > $m)
			{
			@aryA = $sthA->fetchrow_array;
			$filename[$m] =	$aryA[0];
			$rank[$m] =		$aryA[1];
			$filerank[$m] = sprintf("%04d",$rank[$m]) . "_";
			$fileexists[$m] = 0;
			$filesize_moh[$m] = 0;
			$filesize_original[$m] = 0;
			$filelist_names .= "$filerank[$m]$filename[$m]|";

			if ( (-e "$MoH_directory/$filerank[$m]$filename[$m]$wav") || (-e "$MoH_directory/$filerank[$m]$filename[$m]$gsm") )
				{
				if (-e "$MoH_directory/$filerank[$m]$filename[$m]$wav")
					{
					$temp_filesize_moh = (-s "$MoH_directory/$filerank[$m]$filename[$m]$wav");
					$temp_filesize_original = (-s "$PATHsounds/$filename[$m]$wav");
					$filesize_moh[$m] = ($filesize_moh[$m] + $temp_filesize_moh);
					$filesize_original[$m] = ($filesize_original[$m] + $temp_filesize_original);
					if ($DBX)
						{
						print "WAV check existing:\n";
						print "$temp_filesize_moh|$filesize_moh[$m]|$MoH_directory/$filerank[$m]$filename[$m]$wav\n";
						print "$temp_filesize_original|$filesize_original[$m]|$PATHsounds/$filename[$m]$wav\n";
						}
					}
				if (-e "$MoH_directory/$filerank[$m]$filename[$m]$gsm")
					{
					$temp_filesize_moh = (-s "$MoH_directory/$filerank[$m]$filename[$m]$gsm");
					$temp_filesize_original = (-s "$PATHsounds/$filename[$m]$gsm");
					$filesize_moh[$m] = ($filesize_moh[$m] + $temp_filesize_moh);
					$filesize_original[$m] = ($filesize_original[$m] + $temp_filesize_original);
					if ($DBX)
						{
						print "GSM check existing:\n";
						print "$temp_filesize_moh|$filesize_moh[$m]|$MoH_directory/$filerank[$m]$filename[$m]$gsm\n";
						print "$temp_filesize_original|$filesize_original[$m]|$PATHsounds/$filename[$m]$gsm\n";
						}
					}

				if ( ($filesize_moh[$m] == $filesize_original[$m]) && ($filesize_moh[$m] > 0) )
					{$fileexists[$m]++;}
				}
			if ($fileexists[$m] < 1)
				{
				$d=0;
				foreach(@sounds)
					{
					if ($sounds[$d] =~ /^$filename[$m]\./)
						{
						`cp $PATHsounds/$sounds[$d] $MoH_directory/$filerank[$m]$sounds[$d]`;
						$fileexists[$m]++;
						if ($DBX)
							{print "Copy new file: $PATHsounds/$sounds[$d] $MoH_directory/$filerank[$m]$sounds[$d]\n";}
						}
					$d++;
					}
				}
			$m++;
			}
		$sthA->finish();

		opendir(mohdir, "$MoH_directory");
		@MoH_files= readdir(mohdir); 
		closedir(mohdir);

		### Check for files not in MoH context and delete them
		$d=0;
		foreach(@MoH_files)
			{
			$MoH_files_check[$d] = $MoH_files[$d];
			$MoH_files_check[$d] =~ s/\..*$//gi;
			if ($DBX)
				{print "Checking file: $MoH_files[$d] $MoH_files_check[$d] $filelist_names\n";}
			if ( (length($MoH_files[$d]) > 4) && (-f "$MoH_directory/$MoH_files[$d]") && ($filelist_names !~ /\|$MoH_files_check[$d]\|/) && ($MoH_files_check[$d] !~ /^0000_/) )
				{
				`rm -f $MoH_directory/$MoH_files[$d]`;
				if ($DBX)
					{print "Deleting file: $MoH_directory/$MoH_files[$d] $filelist_names\n";}
				}
			$d++;
			}
		$j++;
		}

	### reloading moh in Asterisk
	if ($DBX)
		{print "reloading moh in asterisk\n";}
	`screen -XS asterisk eval 'stuff "moh reload\015"'`;

	}
###### END Music on hold rebuild #####





if($DB)
	{
	print "AUDIO FILES ON SERVER:  $total_files\n";
	print "NEW DOWNLOADED:         $downloaded\n";
	print "NEW UPLOADED:           $uploaded\n\n";

	### calculate time to run script ###
	$secY = time();
	$secZ = ($secY - $secT);

	if (!$q) {print "DONE. Script execution time in seconds: $secZ\n";}
	}

$dbhA->disconnect();

exit;



sub event_logger
	{
	if ($SYSLOG)
		{
		### open the log file for writing ###
		open(Lout, ">>$VASLOGfile")
				|| die "Can't open $VASLOGfile: $!\n";
		print Lout "$now_date|$event_string|\n";
		close(Lout);
		}
	$event_string='';
	}

