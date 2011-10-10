#!/usr/bin/perl
#
# ADMIN_area_code_populate.pl    version 2.4
#
# Copyright (C) 2011  Matt Florell <vicidial@gmail.com>    LICENSE: AGPLv2
#
# Description:
# server application that allows load areacodes into to asterisk list database
#
# CHANGES
# 60615-1514 - Changed to ignore the header row
# 60807-1003 - Changed to DBI
#            - changed to use /etc/astguiclient.conf for configs
# 61122-1902 - Added GMT_USA_zip.txt data import for USA postal GMT data
# 80416-1017 - Added download of phone codes from remote host
# 90129-0932 - Added optional NANP prefix/time date import "--load-NANPA-prefix" flag
# 90131-0933 - Added purge-table option to clear out old records before adding new ones
# 90204-0806 - Added duplicate check to nanpa list loading
# 90317-2353 - Added city, state, postal_code, country to nanpa format
# 100902-1536 - Move old data files if wgetting new ones
# 110424-0735 - Added timezone abbreviation column
#


# default path to astguiclient configuration file:
$PATHconf =		"/etc/astguiclient.conf";
$domain   =		"http://phonecodes.vicidial.com";
#$URL1     =		"$domain/phone_codes_GMT-latest.txt";
$URL1     =		"$domain/phone_codes_GMT-latest-24.txt";
$URL2     =		"$domain/GMT_USA_zip-latest.txt";


### begin parsing run-time options ###
if (length($ARGV[0])>1)
	{
	$i=0;
	while ($#ARGV >= $i)
		{
		$args = "$args $ARGV[$i]";
		$i++;
		}

	if ($args =~ /--help|-h/i)
		{
		print "allowed run time options:\n";
		print "  [-q] = quiet\n";
		print "  [-t] = test\n";
		print "  [--debug] = debug output\n";
		print "  [--use-local-files] = Do not download files, use local copies\n";
		print "  [--load-NANPA-prefix] = Only loads the special NANPA list into the database\n";
		print "  [--purge-table] = Purges the table to be inserted before inserting\n";
		print "\n     files used by this script are:\n";
		print "   phone_codes_GMT-latest-220.txt - Phone codes and country codes with time zone data\n";
		print "   GMT_USA_zip-latest.txt - USA zip code and time zone data\n";
		print "   NANPA_prefix-latest.txt - North American areacode, prefix and time zone data\n";

		exit;
		}
	else
		{
		if ($args =~ /-debug/i)
			{
			$DB=1;
			print "\n----- DEBUGGING -----\n\n";
			}
		if ($args =~ /-debugX/i)
			{
			$DBX=1;
			print "\n----- SUPER-DUPER DEBUGGING -----\n\n";
			}
		else {$DBX=0;}

		if ($args =~ /-q/i)
			{
			$q=1;
			}
		if ($args =~ /-t/i)
			{
			$T=1;
			$TEST=1;
			print "\n----- TESTING -----\n\n";
			}
		if ($args =~ /--use-local-files/i)
			{
			$use_local_files=1;
			print "\n----- USING LOCAL DATA FILES -----\n\n";
			}
		if ($args =~ /--load-NANPA-prefix/i)
			{
			$nanpa_load=1;
			print "\n----- NANPA PHONE PREFIX DATA LOAD -----\n\n";
			}
		if ($args =~ /--purge-table/i)
			{
			$purge_table=1;
			print "\n----- PURGE TABLE BEFORE DATA LOAD -----\n\n";
			}
		}
	}
else
	{
	print "no command line options set\n";
	$args = "";
	$i=0;
	$forcelistid = '';
	$format='standard';
	}
### end parsing run-time options ###



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
$slash_star = '\*';


use Time::HiRes ('gettimeofday','usleep','sleep');  # needed to have perl sleep in increments of less than one second
use DBI;	  

if (!$VARDB_port) {$VARDB_port='3306';}

$dbhA = DBI->connect("DBI:mysql:$VARDB_database:$VARDB_server:$VARDB_port", "$VARDB_user", "$VARDB_pass")
 or die "Couldn't connect to database: " . DBI->errstr;



if ($nanpa_load > 0)
	{
	#### load special North American phone code prefix table ####
	# LONG  # NPA,NXX,,,,,,STATE,COUNTRY,,,,,RC,TZ,DST,ZIP,,,,,,,,,,,LATITUDE,LONGITUDE,,,,,
	# SHORT # NPA,NXX,TZ,DST,LATITUDE,LONGITUDE,CITY,STATE,POSTAL_CODE,COUNTRY

	#### BEGIN vicidial_nanpa_prefix_codes population from NANPA_prefix-latest.txt file ####
	open(prefixfile, "$PATHhome/NANPA_prefix-latest.txt") || die "can't open $PATHhome/NANPA_prefix-latest.txt: $!\n";
	@prefixfile = <prefixfile>;
	close(prefixfile);
	if ( ($purge_table > 0) && ($#prefixfile > 10) )
		{
		print "\n----- PURGING DATA IN vicidial_nanpa_prefix_codes TABLE -----\n\n";

		$stmtA = "DELETE from vicidial_nanpa_prefix_codes;";
				if($DB){print STDERR "\n|$stmtA|\n";}
		$affected_rows = $dbhA->do($stmtA);
		$stmtA = "OPTIMIZE table vicidial_nanpa_prefix_codes;";
				if($DB){print STDERR "\n|$stmtA|\n";}
		$affected_rows = $dbhA->do($stmtA);
		}
	$pc=0;   $full_file=0;   $dup_count=0;   $ins_count=0;   $dup_list='';
	$ins_stmt="insert into vicidial_nanpa_prefix_codes VALUES ";
	foreach (@prefixfile) 
		{
		@row=split(/,/, $prefixfile[$pc]);
		if ($#row > 20)
			{$full_file++;}
		if ( ($prefixfile[$pc] !~ /XXXXX/) && ($prefixfile[$pc] !~ /^\D|^0|^1/) )
			{
			$prefixfile[$pc] =~ s/\r|\n|\t| $//gi;
			$pc++;
			$temp_insert='';
			$dup_check=0;
			if ($full_file > 0)
				{
				if ($row[14] !~ /XX/)
					{
					$row[13] =~ s/\(.*//gi;
					$row[13] =~ s/ $//gi;
					$row[13] =~ s/\'/\\\'/gi;
					$row[14] =~ s/NT/-3.50/gi;
					$row[14] =~ s/AT/-4.00/gi;
					$row[14] =~ s/ET/-5.00/gi;
					$row[14] =~ s/CT/-6.00/gi;
					$row[14] =~ s/MT/-7.00/gi;
					$row[14] =~ s/PT/-8.00/gi;
					$row[14] =~ s/AK/-9.00/gi;
					$row[14] =~ s/HT/-10.00/gi;
					$row[14] =~ s/AS/-11.00/gi;
					$row[14] =~ s/CH/10.00/gi;
					$row[15] =~ s/X/N/gi;
					$temp_insert="('$row[0]', '$row[1]', '$row[14]', '$row[15]', '$row[27]', '$row[28]', '$row[13]', '$row[7]', '$row[16]', '$row[8]'), ";
					}
				}
			else
				{
				$row[6] =~ s/\'/\\\'/gi;
				$row[9] =~ s/\r|\n|\t| $//gi;
				$temp_insert="('$row[0]', '$row[1]', '$row[2]', '$row[3]', '$row[4]', '$row[5]', '$row[6]', '$row[7]', '$row[8]', '$row[9]'), ";
				}

			$stmtA = "SELECT count(*) FROM vicidial_nanpa_prefix_codes where areacode='$row[0]' and prefix='$row[1]';";
			$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
			$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
			$sthArows=$sthA->rows;
				if ($sthArows > 0)
				{
				@aryA = $sthA->fetchrow_array;
				$dup_check	= "$aryA[0]";
				}
			$sthA->finish();

			if ($dup_check < 1) 
				{
				if ($dup_list =~ /$row[0]$row[1]\|/)
					{$dup_count++;}
				else 
					{
					$dup_list .= "$row[0]$row[1]|";
					$ins_stmt .= "$temp_insert";
					$ins_count++;
					}
				}
			else
				{$dup_count++;}

			if ($pc =~ /000$/) 
				{
				chop($ins_stmt);
				chop($ins_stmt);
				$affected_rows = $dbhA->do($ins_stmt) || die "can't execute query: |$ins_stmt| $!\n";
				$ins_stmt="insert into vicidial_nanpa_prefix_codes VALUES ";
				print STDERR "$pc Lines     $ins_count Inserted     $dup_count Duplicates\r";
				$dup_list='';
				}
			}
		else 
			{$pc++;}
		}

	chop($ins_stmt);
	chop($ins_stmt);
	$affected_rows = $dbhA->do($ins_stmt);
	$ins_stmt="insert into vicidial_nanpa_prefix_codes VALUES ";
	print STDERR "$pc Lines     $ins_count Inserted     $dup_count Duplicates\n";
	#### END vicidial_nanpa_prefix_codes population ####

	}
else
	{
	#### download the latest phone code table ####
	chdir("$PATHhome");

	if ($use_local_files < 1)
		{
		$wget = `which wget`;
		print "$wget\n";
		if ( $wget eq "" ) 
			{
			print STDERR "Please install the wget command\n";
			exit();
			}

		print STDERR "Downloading latest phone codes tables\n";

		# move old files
		`mv -f $PATHhome/phone_codes_GMT-latest-24.txt $PATHhome/phone_codes_GMT-latest-24-old.txt`;
		`mv -f $PATHhome/GMT_USA_zip-latest.txt $PATHhome/GMT_USA_zip-latest-old.txt`;

		# get files
		`wget $URL1`;
		`wget $URL2`;
		}

	#### BEGIN vicidial_phone_codes population from phone_codes_GMT-latest-24.txt file ####
	open(codefile, "$PATHhome/phone_codes_GMT-latest-24.txt") || die "can't open $PATHhome/phone_codes_GMT-latest-24.txt: $!\n";
	@codefile = <codefile>;
	close(codefile);
	if ( ($purge_table > 0) && ($#codefile > 10) )
		{
		print "\n----- PURGING DATA IN vicidial_phone_codes TABLE -----\n\n";

		$stmtA = "DELETE from vicidial_phone_codes;";
				if($DB){print STDERR "\n|$stmtA|\n";}
		$affected_rows = $dbhA->do($stmtA);
		$stmtA = "OPTIMIZE table vicidial_phone_codes;";
				if($DB){print STDERR "\n|$stmtA|\n";}
		$affected_rows = $dbhA->do($stmtA);
		}
	$pc=0;
	$ins_stmt="insert into vicidial_phone_codes VALUES ";
	foreach (@codefile) 
		{
		@row=split(/\t/, $codefile[$pc]);
		if ($codefile[$pc] !~ /GEOGRAPHIC DESCRIPTION/)
			{
			$pc++;
			$row[8] =~ s/\r|\n|\t| $//gi;
			$row[7] =~ s/\r|\n|\t| $//gi;
			$row[6] =~ s/\r|\n|\t| $//gi;
			$row[5] =~ s/\r|\n|\t| $//gi;
			$row[4] =~ s/\r|\n|\t| $//gi;
			$row[3] =~ s/\r|\n|\t| $//gi;
			$row[2] =~ s/\r|\n|\t| $//gi;
			$row[1] =~ s/\r|\n|\t| $//gi;
			$row[0] =~ s/\r|\n|\t| $//gi;
			$ins_stmt.="('$row[0]', '$row[1]', '$row[2]', '$row[3]', '$row[4]', '$row[5]', '$row[6]', '$row[7]', '$row[8]'), ";
			if ($pc =~ /00$/) 
				{
				chop($ins_stmt);
				chop($ins_stmt);
				$affected_rows = $dbhA->do($ins_stmt) || die "can't execute query: |$ins_stmt| $!\n";
				$ins_stmt="insert into vicidial_phone_codes VALUES ";
				print STDERR "$pc\r";
				}
			}
		else {$pc++;}
		}

	chop($ins_stmt);
	chop($ins_stmt);
	$affected_rows = $dbhA->do($ins_stmt);
	$ins_stmt="insert into vicidial_phone_codes VALUES ";
	print STDERR "$pc\n";
	#### END vicidial_phone_codes population ####


	#### BEGIN vicidial_postal_codes population from GMT_USA_zip-latest.txt file ####
	open(zipfile, "$PATHhome/GMT_USA_zip-latest.txt") || die "can't open $PATHhome/GMT_USA_zip-latest.txt: $!\n";
	@zipfile = <zipfile>;
	close(zipfile);
	if ( ($purge_table > 0) && ($#zipfile > 10) )
		{
		print "\n----- PURGING DATA IN vicidial_postal_codes TABLE -----\n\n";

		$stmtA = "DELETE from vicidial_postal_codes;";
				if($DB){print STDERR "\n|$stmtA|\n";}
		$affected_rows = $dbhA->do($stmtA);
		$stmtA = "OPTIMIZE table vicidial_postal_codes;";
				if($DB){print STDERR "\n|$stmtA|\n";}
		$affected_rows = $dbhA->do($stmtA);
		}
	$pc=0;
	$ins_stmt="insert into vicidial_postal_codes VALUES ";
	foreach (@zipfile) 
		{
		@row=split(/\t/, $zipfile[$pc]);
		$pc++;
		$row[0] =~ s/\r|\n|\t| $//gi;
		$row[1] =~ s/\r|\n|\t| $//gi;
		$row[2] =~ s/\r|\n|\t| $//gi;
		$row[3] =~ s/\r|\n|\t| $//gi;
		if ($row[3] =~ /Y/i) {$DST_range = 'SSM-FSN';}
		else {$DST_range = '';}
		$ins_stmt.="('$row[0]', '$row[1]', '$row[2]', '$row[3]', '$DST_range', 'USA', '1'), ";
		if ($pc =~ /00$/) 
			{
			chop($ins_stmt);
			chop($ins_stmt);
			$affected_rows = $dbhA->do($ins_stmt) || die "can't execute query: |$ins_stmt| $!\n";
			$ins_stmt="insert into vicidial_postal_codes VALUES ";
			print STDERR "$pc\r";
			}
		}

	chop($ins_stmt);
	chop($ins_stmt);
	$affected_rows = $dbhA->do($ins_stmt);
	$ins_stmt="insert into vicidial_postal_codes VALUES ";
	print STDERR "$pc\n";
	#### END vicidial_postal_codes population ####
	}

$dbhA->disconnect();

exit;
