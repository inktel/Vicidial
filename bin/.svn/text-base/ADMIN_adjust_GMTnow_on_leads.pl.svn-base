#!/usr/bin/perl

# ADMIN_adjust_GMTnow_on_leads.pl    verison 2.4
#
# program goes throught the vicidial_list table and adjusts the gmt_offset_now
# field to change it to today's offset if needed because of Daylight Saving Time
#
# run every time you load leads into the vicidial_list table
# 
# Copyright (C) 2011  Matt Florell <vicidial@gmail.com>    LICENSE: AGPLv2
#
#
# CHANGES
# 50810-1540 - Added database server variable definitions lookup
# 60615-1717 - Added definition GMT lookup from database not flat text file
# 60717-1045 - Changed to DBI by Marin Blu
# 60717-1531 - Changed to use /etc/astguiclient.conf for configuration
# 61108-1320 - Added new DST schemes for USA/Canada change and changes in other countries
# 61110-1204 - Added new DST scheme for Brazil
# 61128-1034 - Added postal code GMT lookup option
# 61219-1106 - Fixed updating for NULL gmt_offset records
# 70823-1633 - Added ability to restrict by list_id
# 80917-2202 - Added FSO-FSA for Eastern Australia (not active)
#              Added LSS-FSA for New Zealand (not active)
# 90129-1114 - Added NANPA prefix lookup option
# 90401-1327 - Fixed quiet flag function
# 91129-2155 - Replaced SELECT STAR queries with field lists, formatting fixes
# 100117-2122 - Added force-date option and activated FSO-FSA and LSS-FSA
# 110406-0652 - Added omitlistid option
# 110424-0834 - Added ownertimezone option
# 110513-1444 - Added list-settings option to use settings from the vicidial_lists table
#

use Time::Local;

$secX = time();
$time = $secX;
	
$MT[0]='';
$q=0;
$forcedate='';
$singlelistid='';
$omitlistidSQL='';

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
		print "  [-q] = quiet\n";
		print "  [-t] = test\n";
		print "  [--debug] = debugging messages\n";
		print "  [--debugX] = Super debugging messages\n";
		print "  [--postal-code-gmt] = Attempt postal codes lookup for timezones\n";
		print "  [--nanpa-prefix-gmt] = Attempt nanpa prefix lookup for timezones\n";
		print "  [--singlelistid=XXX] = Only lookup and alter leads in one list_id\n";
		print "  [--omitlistid=XXX-YYY-ZZZ] = Skip these list_ids, separated by dash\n";
		print "  [--ownertimezone] = Check the owner field for time zone abbreviation: (EST,CST,etc...)\n";
		print "  [--list-settings] = Will use the time zone settings as defined in each list\n";
		print "  [--force-date=YYYY-MM-DD] = Force this date as date to run\n";
		print "\n";

		exit;
		}
	else
		{
		if ($args =~ /-q/i)
			{
			$q=1;
			}
		if ($args =~ /-t|--test/i)
			{
			$T=1; $TEST=1;
			if ($q < 1) {print "\n-----TESTING -----\n\n";}
			}
		if ($args =~ /--debug/i)
			{
			$DB=1;
			print "\n-----DEBUGGING -----\n\n";
			}
		if ($args =~ /--debugX/i)
			{
			$DBX=1;
			print "\n----- SUPER-DUPER DEBUGGING -----\n\n";
			}
		if ($args =~ /--postal-code-gmt/i)
			{
			$searchPOST=1;
			if ($q < 1) {print "\n----- DO POSTAL CODE LOOKUP -----\n\n";}
			}
		if ($args =~ /--nanpa-prefix-gmt/i)
			{
			$searchNANPA=1;
			if ($q < 1) {print "\n----- DO NANPA PREFIX LOOKUP -----\n\n";}
			}
		if ($args =~ /--ownertimezone/i)
			{
			$ownertimezone=1;
			if ($q < 1) {print "\n----- OWNER TIMEZONE CHECK -----\n\n";}
			}
		if ($args =~ /-singlelistid=/i)
			{
			@data_in = split(/-singlelistid=/,$args);
			$singlelistid = $data_in[1];
			if ($q < 1) {print "\n----- SINGLE LISTID OVERRIDE: $singlelistid -----\n\n";}
			}
		if ($args =~ /-omitlistid=/i)
			{
			@data_in = split(/-omitlistid=/,$args);
			$omitlistidSQL = $data_in[1];
			if ($q < 1) {print "\n----- OMIT LISTID OVERRIDE: $omitlistidSQL -----\n\n";}
			$omitlistidSQL =~ s/-/','/gi;
			}
		if ($args =~ /--list-settings/i)
			{
			$use_list_settings=1;
			if ($q < 1) {print "\n----- USE LIST SETTINGS -----\n\n";}
			}
		if ($args =~ /-force-date=/i)
			{
			@data_in = split(/-force-date=/,$args);
			$forcedate = $data_in[1];
			@cli_date = split("-",$forcedate);
			$year = $cli_date[0];
			$mon =	$cli_date[1];
			$mday = $cli_date[2];
			$cli_date[1] = ($cli_date[1] - 1);
			$time = timelocal(0,0,2,$cli_date[2],$cli_date[1],$cli_date[0]);

			if ($q < 1) {print "\n----- FORCE DATE OVERRIDE: $forcedate $time -----\n\n";}
			}
		}
	}
else
	{
	print "no command line options set\n";
	}

if ($DB) {print "STARTING TIME ZONE DAYLIGHT SAVING TIME CALCULATION SCRIPT\n\n\n\n";}

### end parsing run-time options ###

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

if (!$VARDB_port) {$VARDB_port='3306';}

($sec,$min,$hour,$mday,$mon,$year,$wday,$yday,$isdst) = localtime($time);
$mon++;
$year = ($year + 1900);
if ($mon < 10) {$mon = "0$mon";}
if ($mday < 10) {$mday = "0$mday";}
if ($hour < 10) {$hour = "0$hour";}
if ($min < 10) {$min = "0$min";}
if ($sec < 10) {$sec = "0$sec";}
$dsec = ( ( ($hour * 3600) + ($min * 60) ) + $sec );

use DBI;	  

$dbhA = DBI->connect("DBI:mysql:$VARDB_database:$VARDB_server:$VARDB_port", "$VARDB_user", "$VARDB_pass")
 or die "Couldn't connect to database: " . DBI->errstr;


### Grab Server values from the database
$stmtA = "SELECT telnet_host,telnet_port,ASTmgrUSERNAME,ASTmgrSECRET,ASTmgrUSERNAMEupdate,ASTmgrUSERNAMElisten,ASTmgrUSERNAMEsend,max_vicidial_trunks,answer_transfer_agent,local_gmt,ext_context FROM servers where server_ip = '$server_ip';";
$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
$sthArows=$sthA->rows;
if ($sthArows > 0)
	{
	@aryA = $sthA->fetchrow_array;
	$DBtelnet_host	=			$aryA[0];
	$DBtelnet_port	=			$aryA[1];
	$DBASTmgrUSERNAME	=		$aryA[2];
	$DBASTmgrSECRET	=			$aryA[3];
	$DBASTmgrUSERNAMEupdate	=	$aryA[4];
	$DBASTmgrUSERNAMElisten	=	$aryA[5];
	$DBASTmgrUSERNAMEsend	=	$aryA[6];
	$DBmax_vicidial_trunks	=	$aryA[7];
	$DBanswer_transfer_agent=	$aryA[8];
	$DBSERVER_GMT		=		$aryA[9];
	$DBext_context	=			$aryA[10];
	if ($DBtelnet_host)				{$telnet_host = $DBtelnet_host;}
	if ($DBtelnet_port)				{$telnet_port = $DBtelnet_port;}
	if ($DBASTmgrUSERNAME)			{$ASTmgrUSERNAME = $DBASTmgrUSERNAME;}
	if ($DBASTmgrSECRET)			{$ASTmgrSECRET = $DBASTmgrSECRET;}
	if ($DBASTmgrUSERNAMEupdate)	{$ASTmgrUSERNAMEupdate = $DBASTmgrUSERNAMEupdate;}
	if ($DBASTmgrUSERNAMElisten)	{$ASTmgrUSERNAMElisten = $DBASTmgrUSERNAMElisten;}
	if ($DBASTmgrUSERNAMEsend)		{$ASTmgrUSERNAMEsend = $DBASTmgrUSERNAMEsend;}
	if ($DBmax_vicidial_trunks)		{$max_vicidial_trunks = $DBmax_vicidial_trunks;}
	if ($DBanswer_transfer_agent)	{$answer_transfer_agent = $DBanswer_transfer_agent;}
	if ($DBSERVER_GMT)				{$SERVER_GMT = $DBSERVER_GMT;}
	if ($DBext_context)				{$ext_context = $DBext_context;}
	}
$sthA->finish();

$LOCAL_GMT_OFF = $SERVER_GMT;
$LOCAL_GMT_OFF_STD = $SERVER_GMT;

if ($isdst) {$LOCAL_GMT_OFF++;} 
if ($DB) {print "SEED TIME  $time      :   $year-$mon-$mday $hour:$min:$sec  LOCAL GMT OFFSET NOW: $LOCAL_GMT_OFF\n";}

$CAAC_codes_list='';
$POST_codes_list='';
$NPFX_codes_list='';
$OWTZ_codes_list='';
$rec_countCAAC=0;
$rec_countPOST=0;
$rec_countNPFX=0;
$rec_countOWTZ=0;

if ($use_list_settings > 0)
	{
	##### COUNTRY_AND_AREA_CODE lists gather #####
	$stmtA = "select list_id from vicidial_lists where time_zone_setting='COUNTRY_AND_AREA_CODE';";
	if($DBX){print STDERR "\n|$stmtA|\n";}
	$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
	$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
	$sthArows=$sthA->rows;
	while ($sthArows > $rec_countCAAC)
		{
		@aryA = $sthA->fetchrow_array;
		$CAAC_codes_list .=	"'$aryA[0]',";
		$rec_countCAAC++;
		}
	$sthA->finish();
	chop($CAAC_codes_list);
	if ($rec_countCAAC > 0)
		{$CAAC_codes_list = "and list_id IN($CAAC_codes_list)";}
	else 
		{$rec_countCAAC=-1;}
	if ($DB) {print " - COUNTRY_AND_AREA_CODE lists found: $rec_countCAAC     $CAAC_codes_list\n";}


	##### POSTAL_CODE lists gather #####
	$stmtA = "select list_id from vicidial_lists where time_zone_setting='POSTAL_CODE';";
	if($DBX){print STDERR "\n|$stmtA|\n";}
	$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
	$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
	$sthArows=$sthA->rows;
	while ($sthArows > $rec_countPOST)
		{
		@aryA = $sthA->fetchrow_array;
		$POST_codes_list .=	"'$aryA[0]',";
		$rec_countPOST++;
		}
	$sthA->finish();
	chop($POST_codes_list);
	if ($rec_countPOST > 0)
		{$POST_codes_list = "and list_id IN($POST_codes_list)";}
	else 
		{$rec_countPOST=-1;}
	if ($DB) {print " - POSTAL_CODE lists found: $rec_countPOST     $POST_codes_list\n";}


	##### NANPA_PREFIX lists gather #####
	$stmtA = "select list_id from vicidial_lists where time_zone_setting='NANPA_PREFIX';";
	if($DBX){print STDERR "\n|$stmtA|\n";}
	$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
	$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
	$sthArows=$sthA->rows;
	while ($sthArows > $rec_countNPFX)
		{
		@aryA = $sthA->fetchrow_array;
		$NPFX_codes_list .=	"'$aryA[0]',";
		$rec_countNPFX++;
		}
	$sthA->finish();
	chop($NPFX_codes_list);
	if ($rec_countNPFX > 0)
		{$NPFX_codes_list = "and list_id IN($NPFX_codes_list)";}
	else 
		{$rec_countNPFX=-1;}
	if ($DB) {print " - NANPA_PREFIX lists found: $rec_countNPFX     $NPFX_codes_list\n";}


	##### OWNER_TIME_ZONE_CODE lists gather #####
	$stmtA = "select list_id from vicidial_lists where time_zone_setting='OWNER_TIME_ZONE_CODE';";
	if($DBX){print STDERR "\n|$stmtA|\n";}
	$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
	$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
	$sthArows=$sthA->rows;
	while ($sthArows > $rec_countOWTZ)
		{
		@aryA = $sthA->fetchrow_array;
		$OWTZ_codes_list .=	"'$aryA[0]',";
		$rec_countOWTZ++;
		}
	$sthA->finish();
	chop($OWTZ_codes_list);
	if ($rec_countOWTZ > 0)
		{$OWTZ_codes_list = "and list_id IN($OWTZ_codes_list)";}
	else 
		{$rec_countOWTZ=-1;}
	if ($DB) {print " - OWNER_TIME_ZONE_CODE lists found: $rec_countOWTZ     $OWTZ_codes_list\n";}
	}

if (length($singlelistid)> 0) {$listSQL = "where list_id='$singlelistid'";  $XlistSQL=" and list_id='$singlelistid' ";}
else {$listSQL = '';  $XlistSQL='';}
if (length($omitlistidSQL)> 0) 
	{
	$XomitlistidSQL=" and list_id NOT IN('$omitlistidSQL')";
	if (length($singlelistid)> 0)
		{$omitlistidSQL=" and list_id NOT IN('$omitlistidSQL')";}
	else
		{$omitlistidSQL = "where list_id NOT IN('$omitlistidSQL')";}
	}
else {$XomitlistidSQL='';   $omitlistidSQL='';}


$stmtA = "select distinct phone_code from vicidial_list $listSQL $omitlistidSQL;";
if($DBX){print STDERR "\n|$stmtA|\n";}
$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
$sthArows=$sthA->rows;
$rec_countY=0;
@phone_codes=@MT;
$phone_codes_list='';

while ($sthArows > $rec_countY)
	{
	@aryA = $sthA->fetchrow_array;
	 
	$phone_codes[$rec_countY] = $aryA[0];
	$phone_codes_ORIG[$rec_countY] = $aryA[0];
	$phone_codes[$rec_countY] =~ s/011|0011| |\t|\r|\n//gi;

	$phone_codes_list .= "'$aryA[0]',";

	if ($DBX) {print "|",$aryA[0],"|","\n";}
	$rec_countY++;
	}
$sthA->finish();
chop($phone_codes_list);
if ($DB) {print " - Unique Country dial codes found: $rec_countY\n";}
if (length($phone_codes_list)<2) {$phone_codes_list="''";}

##### Owner time zone abbreviation lookup #####
if ( ( ($ownertimezone > 0) || ($rec_countOWTZ > 0) ) && ($rec_countOWTZ > -1) )
	{
	$stmtA = "select distinct tz_code,GMT_offset,country_code from vicidial_phone_codes where country_code IN($phone_codes_list);";
	if($DBX){print STDERR "\n|$stmtA|\n";}
	$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
	$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
	$sthArows=$sthA->rows;
	$rec_countC=0;
	@tz_codes=@MT;
	@tz_offset=@MT;
	@tz_country=@MT;
	$tz_codes_list='|';

	while ($sthArows > $rec_countC)
		{
		@aryA = $sthA->fetchrow_array;

		$tz_codes[$rec_countC] =	$aryA[0];
		$tz_offset[$rec_countC] =	$aryA[1];
		$tz_country[$rec_countC] =	$aryA[2];

		$tz_codes_list .= "$aryA[0]|";

		$rec_countC++;
		}
	$sthA->finish();
	if ($DB) {print " - Unique time zone codes found: $rec_countC     $tz_codes_list\n";}
	}

##### Put all country/area code records into an array for speed
$stmtA = "select country_code,country,areacode,state,GMT_offset,DST,DST_range,geographic_description,tz_code from vicidial_phone_codes;";
if($DBX){print STDERR "\n|$stmtA|\n";}
$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
$sthArows=$sthA->rows;
$rec_countZ=0;
@codefile=@MT;
while ($sthArows > $rec_countZ)
	{
	@aryA = $sthA->fetchrow_array;
	$codefile[$rec_countZ] = "$aryA[0]\t$aryA[1]\t$aryA[2]\t$aryA[3]\t$aryA[4]\t$aryA[5]\t$aryA[6]\t$aryA[7]\t$aryA[8]\n";
	$rec_countZ++;
	}

if ( ( ($searchPOST > 0) || ($rec_countPOST > 0) ) && ($rec_countPOST > -1) )
	{
	##### Put all postal code records into an array for speed
	$stmtA = "select postal_code,state,GMT_offset,DST,DST_range,country,country_code from vicidial_postal_codes;";
	if($DBX){print STDERR "\n|$stmtA|\n";}
	$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
	$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
	$sthArows=$sthA->rows;
	$rec_countT=0;
	@postalfile=@MT;
	while ($sthArows > $rec_countT)
		{
		@aryA = $sthA->fetchrow_array;
		$postalfile[$rec_countT] = "$aryA[0]\t$aryA[1]\t$aryA[2]\t$aryA[3]\t$aryA[4]\t$aryA[5]\t$aryA[6]";
		$rec_countT++;
		}
	$sthA->finish();
	if ($DB) {print " - GMT postal codes records: $rec_countT\n";}
	}

if ( ( ($searchNANPA > 0) || ($rec_countNPFX > 0) ) && ($rec_countNPFX > -1) )
	{
	##### Put all nanpa prefix records into an array for speed
	$stmtA = "select areacode,prefix,GMT_offset,DST from vicidial_nanpa_prefix_codes;";
	if($DBX){print STDERR "\n|$stmtA|\n";}
	$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
	$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
	$sthArows=$sthA->rows;
	$rec_countN=0;
	@nanpafile=@MT;
	while ($sthArows > $rec_countN)
		{
		if ($aryA[3] =~ /Y/) {$DST_method = 'SSM-FSN';}
		else {$DST_method = '';}
		@aryA = $sthA->fetchrow_array;
		$nanpafile[$rec_countN] = "$aryA[0]\t$aryA[1]\t$aryA[2]\t$DST_method";
		$rec_countN++;
		}
	$sthA->finish();
	if ($DB) {print " - NANPA prefix records: $rec_countN\n";}
	}

$ep=0; $ei=0; $ee=0;
$d=0;
$TOTALpostal_updated_count=0;
$TOTALarea_updated_count=0;
$TOTALnanpa_updated_count=0;

foreach (@phone_codes)
	{
	$match_code = $phone_codes[$d];
	$match_code_ORIG = $phone_codes[$d];

	if ($DB) {print "\nRUNNING LOOP FOR COUNTRY CODE: $match_code\n";}

	##### BEGIN RUN LOOP FOR EACH COUNTRY CODE/AREA CODE RECORD THAT IS INSIDE THIS COUNTRY CODE #####
	$e=0;
	$area_updated_count=0;
	foreach (@codefile)
		{
		chomp($codefile[$e]);
		if ($codefile[$e] =~ /^$match_code\t/)
			{
			@m = split(/\t/, $codefile[$e]);
			$area_code = $m[2];
			$area_state = $m[3];
			$area_GMT = $m[4];		$area_GMT =~ s/\+//gi;	$area_GMT = ($area_GMT + 0);
			$area_GMT_method = $m[6];
			if ($area_code =~ /S/)	# look for state override flag in areacode field
				{
				$area_code =~ s/\D//gi;
				$AC_match = " and phone_number LIKE \"$area_code%\" and state='$area_state'";
				if ($DB) {print "  AREA CODE STATE OVERRIDE: $codefile[$e]\n";}
				}
			else
				{
				if ($area_code =~ /\*/) {$AC_match = '';}
				else {$AC_match = " and phone_number LIKE \"$area_code%\"";}
				}
			if ($DBX) {print "PROCESSING THIS LINE: $codefile[$e]\n";}

			$stmtA = "select count(*) from vicidial_list where phone_code='$match_code_ORIG' $AC_match $XlistSQL $XomitlistidSQL;";
			if($DBX){print STDERR "\n|$stmtA|\n";}
			
			$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
			$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
			$sthArows=$sthA->rows;
			$rec_countZ=0;
			@aryA = $sthA->fetchrow_array;
			$rec_countZ = $aryA[0];
			$sthA->finish();
			
			if (!$rec_countZ)
				{
			#	if ($DB) {print "   IGNORING: $codefile[$e]\n";}
				$ei++;
				}
			else
				{
				$AC_GMT_diff = ($area_GMT - $LOCAL_GMT_OFF_STD);
				$AC_localtime = ($time + (3600 * $AC_GMT_diff));
				($sec,$min,$hour,$mday,$mon,$year,$wday,$yday,$isdst) = localtime($AC_localtime);
				$year = ($year + 1900);
				$mon++;
				if ($mon < 10) {$mon = "0$mon";}
				if ($mday < 10) {$mday = "0$mday";}
				if ($hour < 10) {$hour = "0$hour";}
				if ($min < 10) {$min = "0$min";}
				if ($sec < 10) {$sec = "0$sec";}
				$dsec = ( ( ($hour * 3600) + ($min * 60) ) + $sec );
				
				$AC_processed=0;

				if ( (!$AC_processed) && ($area_GMT_method =~ /SSM-FSN/) )
					{
					if ($DBX) {print "     Second Sunday March to First Sunday November\n";}
					&USACAN_dstcalc;
					if ($DBX) {print "     DST: $USACAN_DST\n";}
					if ($USACAN_DST) {$area_GMT++;}
					$AC_processed++;
					}
				if ( (!$AC_processed) && ($area_GMT_method =~ /FSA-LSO/) )
					{
					if ($DBX) {print "     First Sunday April to Last Sunday October\n";}
					&NA_dstcalc;
					if ($DBX) {print "     DST: $NA_DST\n";}
					if ($NA_DST) {$area_GMT++;}
					$AC_processed++;
					}
				if ( (!$AC_processed) && ($area_GMT_method =~ /LSM-LSO/) )
					{
					if ($DBX) {print "     Last Sunday March to Last Sunday October\n";}
					&GBR_dstcalc;
					if ($DBX) {print "     DST: $GBR_DST\n";}
					if ($GBR_DST) {$area_GMT++;}
					$AC_processed++;
					}
				if ( (!$AC_processed) && ($area_GMT_method =~ /LSO-LSM/) )
					{
					if ($DBX) {print "     Last Sunday October to Last Sunday March\n";}
					&AUS_dstcalc;
					if ($DBX) {print "     DST: $AUS_DST\n";}
					if ($AUS_DST) {$area_GMT++;}
					$AC_processed++;
					}
				if ( (!$AC_processed) && ($area_GMT_method =~ /FSO-LSM/) )
					{
					if ($DBX) {print "     First Sunday October to Last Sunday March\n";}
					&AUST_dstcalc;
					if ($DBX) {print "     DST: $AUST_DST\n";}
					if ($AUST_DST) {$area_GMT++;}
					$AC_processed++;
					}
				if ( (!$AC_processed) && ($area_GMT_method =~ /FSO-FSA/) )
					{
					if ($DBX) {print "     First Sunday October to First Sunday April\n";}
					&AUSE_dstcalc;
					if ($DBX) {print "     DST: $AUSE_DST\n";}
					if ($AUSE_DST) {$area_GMT++;}
					$AC_processed++;
					}
				if ( (!$AC_processed) && ($area_GMT_method =~ /FSO-TSM/) )
					{
					if ($DBX) {print "     First Sunday October to Third Sunday March\n";}
					&NZL_dstcalc;
					if ($DBX) {print "     DST: $NZL_DST\n";}
					if ($NZL_DST) {$area_GMT++;}
					$AC_processed++;
					}
				if ( (!$AC_processed) && ($area_GMT_method =~ /LSS-FSA/) )
					{
					if ($DBX) {print "     Last Sunday September to First Sunday April\n";}
					&NZLN_dstcalc;
					if ($DBX) {print "     DST: $NZLN_DST\n";}
					if ($NZLN_DST) {$area_GMT++;}
					$AC_processed++;
					}
				if ( (!$AC_processed) && ($area_GMT_method =~ /TSO-LSF/) )
					{
					if ($DBX) {print "     Third Sunday October to Last Sunday February\n";}
					&BZL_dstcalc;
					if ($DBX) {print "     DST: $BZL_DST\n";}
					if ($BZL_DST) {$area_GMT++;}
					$AC_processed++;
					}
				if (!$AC_processed)
					{
					if ($DBX) {print "     No DST Method Found\n";}
					if ($DBX) {print "     DST: 0\n";}
					$AC_processed++;
					}

				if ($AC_processed)
					{
					$stmtA = "select count(*) from vicidial_list where phone_code='$match_code_ORIG' $AC_match and (gmt_offset_now != '$area_GMT' or gmt_offset_now IS NULL) $XlistSQL $XomitlistidSQL;";
					if($DBX){print STDERR "\n|$stmtA|\n";}
					$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
					$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
					$sthArows=$sthA->rows;
					$rec_countW=0;
					@aryA = $sthA->fetchrow_array;
					$rec_countW = $aryA[0];
   	       			$sthA->finish();
						
					if (!$rec_countW)
						{
						if ($DBX) {print "   ALL GMT ALREADY CORRECT FOR : $match_code_ORIG  $area_code   $area_GMT\n";}
						$ei++;
						}
					else
						{
						$stmtA = "update vicidial_list set gmt_offset_now='$area_GMT',modify_date=modify_date where phone_code='$match_code_ORIG' $AC_match and (gmt_offset_now != '$area_GMT' or gmt_offset_now IS NULL) $XlistSQL $XomitlistidSQL;";
						if($DBX){print STDERR "\n|$stmtA|\n";}
						if (!$T) 
							{
							$affected_rows = $dbhA->do($stmtA);
							$area_updated_count = ($area_updated_count + $affected_rows);
							}
						$Prec_countW = sprintf("%8s", $rec_countW);
						if ($DB) {print " $Prec_countW records in $match_code_ORIG  $area_code   updated to $area_GMT\n";}
						$ee++;
				#		sleep(1);
						}
					}
				}
			
			$ep++;
			}
		else
			{
		#	if ($DBX) {print "   IGNORING: $codefile[$e]\n";}
			$ei++;
			}
		$e++;
		}
	##### END RUN LOOP FOR EACH COUNTRY CODE/AREA CODE RECORD THAT IS INSIDE THIS COUNTRY CODE #####
	if($DB){print "Area Code Updates: $area_updated_count\n";}
	$TOTALarea_updated_count = ($TOTALarea_updated_count + $area_updated_count);





	##### BEGIN RUN LOOP FOR EACH POSTAL CODE RECORD THAT IS INSIDE THIS COUNTRY CODE #####
	$postal_updated_count=0;
	if ( ( ($searchPOST > 0) || ($rec_countPOST > 0) ) && ($rec_countPOST > -1) )
		{
		if ($DB) {print "POSTAL CODE RUN START...\n";}
		$e=0;
		foreach (@postalfile)
			{
			chomp($postalfile[$e]);
			if ($postalfile[$e] =~ /\t$match_code$/)
				{
				@m = split(/\t/, $postalfile[$e]);
				$postal_code = $m[0];
				$postal_state = $m[1];
				$area_GMT = $m[2];		$area_GMT =~ s/\+//gi;	$area_GMT = ($area_GMT + 0);
				$area_GMT_method = $m[4];
				$AC_match = " and postal_code LIKE \"$postal_code%\"";
				if ($DBX) {print "PROCESSING THIS LINE: $postalfile[$e]\n";}
				
				$stmtA = "select count(*) from vicidial_list where phone_code='$match_code_ORIG' $AC_match $XlistSQL $XomitlistidSQL $POST_codes_list;";
				if($DBX){print STDERR "\n|$stmtA|\n";}
				
				$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
				$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
				$sthArows=$sthA->rows;
				$rec_countZ=0;
				@aryA = $sthA->fetchrow_array;
				$rec_countZ = $aryA[0];
				$sthA->finish();
				
				if (!$rec_countZ)
					{
			#		if ($DB) {print "   IGNORING: $postalfile[$e]\n";}
					$ei++;
					}
				else
					{
					$AC_GMT_diff = ($area_GMT - $LOCAL_GMT_OFF_STD);
					$AC_localtime = ($time + (3600 * $AC_GMT_diff));
					($sec,$min,$hour,$mday,$mon,$year,$wday,$yday,$isdst) = localtime($AC_localtime);
					$year = ($year + 1900);
					$mon++;
					if ($mon < 10) {$mon = "0$mon";}
					if ($mday < 10) {$mday = "0$mday";}
					if ($hour < 10) {$hour = "0$hour";}
					if ($min < 10) {$min = "0$min";}
					if ($sec < 10) {$sec = "0$sec";}
					$dsec = ( ( ($hour * 3600) + ($min * 60) ) + $sec );
					
					$AC_processed=0;

					if ( (!$AC_processed) && ($area_GMT_method =~ /SSM-FSN/) )
						{
						if ($DBX) {print "     Second Sunday March to First Sunday November\n";}
						&USACAN_dstcalc;
						if ($DBX) {print "     DST: $USACAN_DST\n";}
						if ($USACAN_DST) {$area_GMT++;}
						$AC_processed++;
						}
					if ( (!$AC_processed) && ($area_GMT_method =~ /FSA-LSO/) )
						{
						if ($DBX) {print "     First Sunday April to Last Sunday October\n";}
						&NA_dstcalc;
						if ($DBX) {print "     DST: $NA_DST\n";}
						if ($NA_DST) {$area_GMT++;}
						$AC_processed++;
						}
					if ( (!$AC_processed) && ($area_GMT_method =~ /LSM-LSO/) )
						{
						if ($DBX) {print "     Last Sunday March to Last Sunday October\n";}
						&GBR_dstcalc;
						if ($DBX) {print "     DST: $GBR_DST\n";}
						if ($GBR_DST) {$area_GMT++;}
						$AC_processed++;
						}
					if ( (!$AC_processed) && ($area_GMT_method =~ /LSO-LSM/) )
						{
						if ($DBX) {print "     Last Sunday October to Last Sunday March\n";}
						&AUS_dstcalc;
						if ($DBX) {print "     DST: $AUS_DST\n";}
						if ($AUS_DST) {$area_GMT++;}
						$AC_processed++;
						}
					if ( (!$AC_processed) && ($area_GMT_method =~ /FSO-LSM/) )
						{
						if ($DBX) {print "     First Sunday October to Last Sunday March\n";}
						&AUST_dstcalc;
						if ($DBX) {print "     DST: $AUST_DST\n";}
						if ($AUST_DST) {$area_GMT++;}
						$AC_processed++;
						}
					if ( (!$AC_processed) && ($area_GMT_method =~ /FSO-FSA/) )
						{
						if ($DBX) {print "     First Sunday October to First Sunday April\n";}
						&AUSE_dstcalc;
						if ($DBX) {print "     DST: $AUSE_DST\n";}
						if ($AUSE_DST) {$area_GMT++;}
						$AC_processed++;
						}
					if ( (!$AC_processed) && ($area_GMT_method =~ /FSO-TSM/) )
						{
						if ($DBX) {print "     First Sunday October to Third Sunday March\n";}
						&NZL_dstcalc;
						if ($DBX) {print "     DST: $NZL_DST\n";}
						if ($NZL_DST) {$area_GMT++;}
						$AC_processed++;
						}
					if ( (!$AC_processed) && ($area_GMT_method =~ /LSS-FSA/) )
						{
						if ($DBX) {print "     Last Sunday September to First Sunday April\n";}
						&NZLN_dstcalc;
						if ($DBX) {print "     DST: $NZLN_DST\n";}
						if ($NZLN_DST) {$area_GMT++;}
						$AC_processed++;
						}
					if ( (!$AC_processed) && ($area_GMT_method =~ /TSO-LSF/) )
						{
						if ($DBX) {print "     Third Sunday October to Last Sunday February\n";}
						&BZL_dstcalc;
						if ($DBX) {print "     DST: $BZL_DST\n";}
						if ($BZL_DST) {$area_GMT++;}
						$AC_processed++;
						}
					if (!$AC_processed)
						{
						if ($DBX) {print "     No DST Method Found\n";}
						if ($DBX) {print "     DST: 0\n";}
						$AC_processed++;
						}


					if ($AC_processed)
						{
						$stmtA = "select count(*) from vicidial_list where phone_code='$match_code_ORIG' $AC_match and (gmt_offset_now != '$area_GMT' or gmt_offset_now IS NULL) $XlistSQL $XomitlistidSQL $POST_codes_list;";
						if($DBX){print STDERR "\n|$stmtA|\n";}
						$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
						$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
						$sthArows=$sthA->rows;
						$rec_countW=0;
						@aryA = $sthA->fetchrow_array;
						$rec_countW = $aryA[0];
						$sthA->finish();
							
						if (!$rec_countW)
							{
							if ($DBX) {print "   ALL GMT ALREADY CORRECT FOR : $match_code_ORIG  $postal_code   $area_GMT\n";}
							$ei++;
							}
						else
							{
							$stmtA = "update vicidial_list set gmt_offset_now='$area_GMT',modify_date=modify_date where phone_code='$match_code_ORIG' $AC_match and (gmt_offset_now != '$area_GMT' or gmt_offset_now IS NULL) $XlistSQL $XomitlistidSQL $POST_codes_list;";
							if($DBX){print STDERR "\n|$stmtA|\n";}
							if (!$T) 
								{
								$affected_rows = $dbhA->do($stmtA);
								$postal_updated_count = ($postal_updated_count + $affected_rows);
								}
							$Prec_countW = sprintf("%8s", $rec_countW);
							if ($DB) {print " $Prec_countW records in $match_code_ORIG  $postal_code   updated to $area_GMT\n";}
							$ee++;
					#		sleep(1);
							}
						}
					}
				
				$ep++;
				}
			else
				{
		#		if ($DBX) {print "   IGNORING: $postalfile[$e]\n";}
				$ei++;
				}
			$e++;
			}
		}
	##### END RUN LOOP FOR EACH POSTAL CODE RECORD THAT IS INSIDE THIS COUNTRY CODE #####
	if($DB){print "Postal Updates:    $postal_updated_count\n";}
	$TOTALpostal_updated_count = ($TOTALpostal_updated_count + $postal_updated_count);





	##### START RUN LOOP FOR EACH NANPA PREFIX RECORD #####
	# areacode,prefix,GMT_offset,DST,SSM-FSN
	$nanpa_updated_count=0;
	if ( ( ( ($searchNANPA > 0) && ($match_code =~ /^1$/) ) || ($rec_countNPFX > 0) ) && ($rec_countNPFX > -1) )
		{
		if ($DB) {print "NANPA PREFIX RUN START...\n";}
		$e=0;
		foreach (@nanpafile)
			{
			chomp($nanpafile[$e]);
			@m = split(/\t/, $nanpafile[$e]);
			$nanpa_areacode = $m[0];
			$nanpa_prefix = $m[1];
			$area_GMT = $m[2];		$area_GMT =~ s/\+//gi;	$area_GMT = ($area_GMT + 0);
			$area_GMT_method = $m[3];
			$AC_match = " and phone_number LIKE \"$nanpa_areacode$nanpa_prefix%\"";
			if ($DBX) {print "PROCESSING THIS LINE: $nanpafile[$e]\n";}
			
			$stmtA = "select count(*) from vicidial_list where phone_code='$match_code_ORIG' $AC_match $XlistSQL $XomitlistidSQL $NPFX_codes_list;";
			if($DBX){print STDERR "\n|$stmtA|\n";}
			
			$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
			$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
			$sthArows=$sthA->rows;
			$rec_countZ=0;
			@aryA = $sthA->fetchrow_array;
			$rec_countZ = $aryA[0];
			$sthA->finish();
			
			if (!$rec_countZ)
				{
		#		if ($DB) {print "   IGNORING: $nanpafile[$e]\n";}
				$ei++;
				}
			else
				{
				$AC_GMT_diff = ($area_GMT - $LOCAL_GMT_OFF_STD);
				$AC_localtime = ($time + (3600 * $AC_GMT_diff));
				($sec,$min,$hour,$mday,$mon,$year,$wday,$yday,$isdst) = localtime($AC_localtime);
				$year = ($year + 1900);
				$mon++;
				if ($mon < 10) {$mon = "0$mon";}
				if ($mday < 10) {$mday = "0$mday";}
				if ($hour < 10) {$hour = "0$hour";}
				if ($min < 10) {$min = "0$min";}
				if ($sec < 10) {$sec = "0$sec";}
				$dsec = ( ( ($hour * 3600) + ($min * 60) ) + $sec );
				
				$AC_processed=0;

				if ( (!$AC_processed) && ($area_GMT_method =~ /SSM-FSN/) )
					{
					if ($DBX) {print "     Second Sunday March to First Sunday November\n";}
					&USACAN_dstcalc;
					if ($DBX) {print "     DST: $USACAN_DST\n";}
					if ($USACAN_DST) {$area_GMT++;}
					$AC_processed++;
					}
				if ( (!$AC_processed) && ($area_GMT_method =~ /FSA-LSO/) )
					{
					if ($DBX) {print "     First Sunday April to Last Sunday October\n";}
					&NA_dstcalc;
					if ($DBX) {print "     DST: $NA_DST\n";}
					if ($NA_DST) {$area_GMT++;}
					$AC_processed++;
					}
				if ( (!$AC_processed) && ($area_GMT_method =~ /LSM-LSO/) )
					{
					if ($DBX) {print "     Last Sunday March to Last Sunday October\n";}
					&GBR_dstcalc;
					if ($DBX) {print "     DST: $GBR_DST\n";}
					if ($GBR_DST) {$area_GMT++;}
					$AC_processed++;
					}
				if ( (!$AC_processed) && ($area_GMT_method =~ /LSO-LSM/) )
					{
					if ($DBX) {print "     Last Sunday October to Last Sunday March\n";}
					&AUS_dstcalc;
					if ($DBX) {print "     DST: $AUS_DST\n";}
					if ($AUS_DST) {$area_GMT++;}
					$AC_processed++;
					}
				if ( (!$AC_processed) && ($area_GMT_method =~ /FSO-LSM/) )
					{
					if ($DBX) {print "     First Sunday October to Last Sunday March\n";}
					&AUST_dstcalc;
					if ($DBX) {print "     DST: $AUST_DST\n";}
					if ($AUST_DST) {$area_GMT++;}
					$AC_processed++;
					}
				if ( (!$AC_processed) && ($area_GMT_method =~ /FSO-FSA/) )
					{
					if ($DBX) {print "     First Sunday October to First Sunday April\n";}
					&AUSE_dstcalc;
					if ($DBX) {print "     DST: $AUSE_DST\n";}
					if ($AUSE_DST) {$area_GMT++;}
					$AC_processed++;
					}
				if ( (!$AC_processed) && ($area_GMT_method =~ /FSO-TSM/) )
					{
					if ($DBX) {print "     First Sunday October to Third Sunday March\n";}
					&NZL_dstcalc;
					if ($DBX) {print "     DST: $NZL_DST\n";}
					if ($NZL_DST) {$area_GMT++;}
					$AC_processed++;
					}
				if ( (!$AC_processed) && ($area_GMT_method =~ /LSS-FSA/) )
					{
					if ($DBX) {print "     Last Sunday September to First Sunday April\n";}
					&NZLN_dstcalc;
					if ($DBX) {print "     DST: $NZLN_DST\n";}
					if ($NZLN_DST) {$area_GMT++;}
					$AC_processed++;
					}
				if ( (!$AC_processed) && ($area_GMT_method =~ /TSO-LSF/) )
					{
					if ($DBX) {print "     Third Sunday October to Last Sunday February\n";}
					&BZL_dstcalc;
					if ($DBX) {print "     DST: $BZL_DST\n";}
					if ($BZL_DST) {$area_GMT++;}
					$AC_processed++;
					}
				if (!$AC_processed)
					{
					if ($DBX) {print "     No DST Method Found\n";}
					if ($DBX) {print "     DST: 0\n";}
					$AC_processed++;
					}


				if ($AC_processed)
					{
					$stmtA = "select count(*) from vicidial_list where phone_code='$match_code_ORIG' $AC_match and (gmt_offset_now != '$area_GMT' or gmt_offset_now IS NULL) $XlistSQL $XomitlistidSQL $NPFX_codes_list;";
					if($DBX){print STDERR "\n|$stmtA|\n";}
					$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
					$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
					$sthArows=$sthA->rows;
					$rec_countW=0;
					@aryA = $sthA->fetchrow_array;
					$rec_countW = $aryA[0];
					$sthA->finish();
						
					if (!$rec_countW)
						{
						if ($DBX) {print "   ALL GMT ALREADY CORRECT FOR : $match_code_ORIG  $nanpa_code   $area_GMT\n";}
						$ei++;
						}
					else
						{
						$stmtA = "update vicidial_list set gmt_offset_now='$area_GMT',modify_date=modify_date where phone_code='$match_code_ORIG' $AC_match and (gmt_offset_now != '$area_GMT' or gmt_offset_now IS NULL) $XlistSQL $XomitlistidSQL $NPFX_codes_list;";
						if($DBX){print STDERR "\n|$stmtA|\n";}
						if (!$T) 
							{
							$affected_rows = $dbhA->do($stmtA);
							$nanpa_updated_count = ($nanpa_updated_count + $affected_rows);
							}
						$Prec_countW = sprintf("%8s", $rec_countW);
						if ($DB) {print " $Prec_countW records in $match_code_ORIG  $nanpa_areacode $nanpa_prefix  updated to $area_GMT\n";}
						$ee++;
				#		sleep(1);
						}
					}
				}
			
			$ep++;
			$e++;
			}
		}
	##### START RUN LOOP FOR EACH NANPA PREFIX RECORD #####
	if($DB){print "NANPA Updates:     $nanpa_updated_count\n";}
	$TOTALnanpa_updated_count = ($TOTALnanpa_updated_count + $nanpa_updated_count);





	##### START RUN LOOP FOR EACH TIME ZONE CODE RECORD #####
	$tzcode_updated_count=0;
	if ( ( ($ownertimezone > 0) || ($rec_countOWTZ > 0) ) && ($rec_countOWTZ > -1) )
		{
		if ($DB) {print "TIME ZONE CODE RUN START...\n";}
		$e=0;
		foreach (@tz_codes)
			{
			$area_GMT_method='';
			$area_GMT = $tz_offset[$e];
			$area_GMT =~ s/\+//gi;
			$area_GMT = ($area_GMT + 0);

			$stmtA = "select distinct DST_range from vicidial_phone_codes where tz_code='$tz_codes[$e]' and country_code='$tz_country[$e]' order by DST_range desc limit 1;";
			if($DBX){print STDERR "\n|$stmtA|\n";}
			$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
			$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
			$sthArows=$sthA->rows;
			@aryA = $sthA->fetchrow_array;
			$area_GMT_method = $aryA[0];
			$sthA->finish();

			if ($DBX) {print "PROCESSING THIS TIME ZONE CODE: $tz_codes[$e]|$tz_country[$e]|$area_GMT|$area_GMT_method\n";}
			
			$stmtA = "select count(*) from vicidial_list where owner='$tz_codes[$e]' and phone_code='$tz_country[$e]' $XlistSQL $XomitlistidSQL $OWTZ_codes_list;";
			if($DBX){print STDERR "\n|$stmtA|\n";}
			$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
			$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
			$sthArows=$sthA->rows;
			$rec_countZ=0;
			@aryA = $sthA->fetchrow_array;
			$rec_countZ = $aryA[0];
			$sthA->finish();
			
			if (!$rec_countZ)
				{
		#		if ($DB) {print "   IGNORING: $tzcodefile[$e]\n";}
				$ei++;
				}
			else
				{
				$AC_GMT_diff = ($area_GMT - $LOCAL_GMT_OFF_STD);
				$AC_localtime = ($time + (3600 * $AC_GMT_diff));
				($sec,$min,$hour,$mday,$mon,$year,$wday,$yday,$isdst) = localtime($AC_localtime);
				$year = ($year + 1900);
				$mon++;
				if ($mon < 10) {$mon = "0$mon";}
				if ($mday < 10) {$mday = "0$mday";}
				if ($hour < 10) {$hour = "0$hour";}
				if ($min < 10) {$min = "0$min";}
				if ($sec < 10) {$sec = "0$sec";}
				$dsec = ( ( ($hour * 3600) + ($min * 60) ) + $sec );
				
				$AC_processed=0;

				if ( (!$AC_processed) && ($area_GMT_method =~ /SSM-FSN/) )
					{
					if ($DBX) {print "     Second Sunday March to First Sunday November\n";}
					&USACAN_dstcalc;
					if ($DBX) {print "     DST: $USACAN_DST\n";}
					if ($USACAN_DST) {$area_GMT++;}
					$AC_processed++;
					}
				if ( (!$AC_processed) && ($area_GMT_method =~ /FSA-LSO/) )
					{
					if ($DBX) {print "     First Sunday April to Last Sunday October\n";}
					&NA_dstcalc;
					if ($DBX) {print "     DST: $NA_DST\n";}
					if ($NA_DST) {$area_GMT++;}
					$AC_processed++;
					}
				if ( (!$AC_processed) && ($area_GMT_method =~ /LSM-LSO/) )
					{
					if ($DBX) {print "     Last Sunday March to Last Sunday October\n";}
					&GBR_dstcalc;
					if ($DBX) {print "     DST: $GBR_DST\n";}
					if ($GBR_DST) {$area_GMT++;}
					$AC_processed++;
					}
				if ( (!$AC_processed) && ($area_GMT_method =~ /LSO-LSM/) )
					{
					if ($DBX) {print "     Last Sunday October to Last Sunday March\n";}
					&AUS_dstcalc;
					if ($DBX) {print "     DST: $AUS_DST\n";}
					if ($AUS_DST) {$area_GMT++;}
					$AC_processed++;
					}
				if ( (!$AC_processed) && ($area_GMT_method =~ /FSO-LSM/) )
					{
					if ($DBX) {print "     First Sunday October to Last Sunday March\n";}
					&AUST_dstcalc;
					if ($DBX) {print "     DST: $AUST_DST\n";}
					if ($AUST_DST) {$area_GMT++;}
					$AC_processed++;
					}
				if ( (!$AC_processed) && ($area_GMT_method =~ /FSO-FSA/) )
					{
					if ($DBX) {print "     First Sunday October to First Sunday April\n";}
					&AUSE_dstcalc;
					if ($DBX) {print "     DST: $AUSE_DST\n";}
					if ($AUSE_DST) {$area_GMT++;}
					$AC_processed++;
					}
				if ( (!$AC_processed) && ($area_GMT_method =~ /FSO-TSM/) )
					{
					if ($DBX) {print "     First Sunday October to Third Sunday March\n";}
					&NZL_dstcalc;
					if ($DBX) {print "     DST: $NZL_DST\n";}
					if ($NZL_DST) {$area_GMT++;}
					$AC_processed++;
					}
				if ( (!$AC_processed) && ($area_GMT_method =~ /LSS-FSA/) )
					{
					if ($DBX) {print "     Last Sunday September to First Sunday April\n";}
					&NZLN_dstcalc;
					if ($DBX) {print "     DST: $NZLN_DST\n";}
					if ($NZLN_DST) {$area_GMT++;}
					$AC_processed++;
					}
				if ( (!$AC_processed) && ($area_GMT_method =~ /TSO-LSF/) )
					{
					if ($DBX) {print "     Third Sunday October to Last Sunday February\n";}
					&BZL_dstcalc;
					if ($DBX) {print "     DST: $BZL_DST\n";}
					if ($BZL_DST) {$area_GMT++;}
					$AC_processed++;
					}
				if (!$AC_processed)
					{
					if ($DBX) {print "     No DST Method Found\n";}
					if ($DBX) {print "     DST: 0\n";}
					$AC_processed++;
					}


				if ($AC_processed)
					{
					$stmtA = "select count(*) from vicidial_list where owner='$tz_codes[$e]' and phone_code='$tz_country[$e]' and (gmt_offset_now != '$area_GMT' or gmt_offset_now IS NULL) $XlistSQL $XomitlistidSQL $OWTZ_codes_list;";
					if($DBX){print STDERR "\n|$stmtA|\n";}
					$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
					$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
					$sthArows=$sthA->rows;
					$rec_countW=0;
					@aryA = $sthA->fetchrow_array;
					$rec_countW = $aryA[0];
					$sthA->finish();
						
					if (!$rec_countW)
						{
						if ($DBX) {print "   ALL GMT ALREADY CORRECT FOR : $tz_codes[$e]  $tz_country[$e]   $area_GMT\n";}
						$ei++;
						}
					else
						{
						$stmtA = "UPDATE vicidial_list set gmt_offset_now='$area_GMT',modify_date=modify_date where owner='$tz_codes[$e]' and phone_code='$tz_country[$e]' and (gmt_offset_now != '$area_GMT' or gmt_offset_now IS NULL) $XlistSQL $XomitlistidSQL $OWTZ_codes_list;";
						if($DBX){print STDERR "\n|$stmtA|\n";}
						if (!$T) 
							{
							$affected_rows = $dbhA->do($stmtA);
							$tzcode_updated_count = ($tzcode_updated_count + $affected_rows);
							}
						$Prec_countW = sprintf("%8s", $rec_countW);
						if ($DB) {print " $Prec_countW records in $tz_codes[$e]  $tz_country[$e]  updated to $area_GMT\n";}
						$ee++;
				#		sleep(1);
						}
					}
				}
			
			$ep++;
			$e++;
			}
		}
	##### STOP RUN LOOP FOR EACH TIME ZONE CODE RECORD #####
	if($DB){print "Time Zone Code Updates:     $tzcode_updated_count\n";}
	$TOTALtzcode_updated_count = ($TOTALtzcode_updated_count + $tzcode_updated_count);



	$d++;
	}



$dbhA->disconnect();

if($DB){print "\nGRAND TOTALS:\n";}
if($DB){print "Postal Updates:    $TOTALpostal_updated_count\n";}
if($DB){print "Area Code Updates: $TOTALarea_updated_count\n";}
if($DB){print "NANPA Updates:     $TOTALnanpa_updated_count\n";}
if($DB){print "TIME ZONE Updates: $TOTALtzcode_updated_count\n";}
if($DB){print "\nDONE\n";}
$secy = time();		$secz = ($secy - $secX);		$minz = ($secz/60);		# calculate script runtime so far
if ($q < 1) {print "     - process runtime      ($secz sec) ($minz minutes)\n";}

exit;



sub USACAN_dstcalc {
#**********************************************************************
# SSM-FSN
#   USA and CANADA
#     This is returns 1 if Daylight Savings Time is in effect and 0 if 
#       Standard time is in effect.
#     Based on Second Sunday March to First Sunday November at 2 am.
#     INPUTS:
#       mm              INTEGER       Month.
#       dd              INTEGER       Day of the month.
#       ns              INTEGER       Seconds into the day.
#       dow             INTEGER       Day of week (0=Sunday, to 6=Saturday)
#     OPTIONAL INPUT:
#       timezone        INTEGER       hour difference UTC - local standard time
#                                      (DEFAULT is blank)
#                                     make calculations based on UTC time, 
#                                     which means shift at 10:00 UTC in April
#                                     and 9:00 UTC in October
#     OUTPUT: 
#                       INTEGER       1 = DST, 0 = not DST
#
# S  M  T  W  T  F  S
# 1  2  3  4  5  6  7
# 8  9 10 11 12 13 14
#15 16 17 18 19 20 21
#22 23 24 25 26 27 28
#29 30 31
# 
# S  M  T  W  T  F  S
#    1  2  3  4  5  6
# 7  8  9 10 11 12 13
#14 15 16 17 18 19 20
#21 22 23 24 25 26 27
#28 29 30 31
# 
#**********************************************************************

	$USACAN_DST=0;
	$mm = $mon;
	$dd = $mday;
	$ns = $dsec;
	$dow= $wday;

    if ($mm < 3 || $mm > 11) {
	$USACAN_DST=0;   return 0;
    } elsif ($mm >= 4 && $mm <= 10) {
	$USACAN_DST=1;   return 1;
    } elsif ($mm == 3) {
	if ($dd > 13) {
	    $USACAN_DST=1;   return 1;
	} elsif ($dd >= ($dow+8)) {
	    if ($timezone) {
		if ($dow == 0 && $ns < (7200+$timezone*3600)) {
		    $USACAN_DST=0;   return 0;
		} else {
		    $USACAN_DST=1;   return 1;
		}
	    } else {
		if ($dow == 0 && $ns < 7200) {
		    $USACAN_DST=0;   return 0;
		} else {
		    $USACAN_DST=1;   return 1;
		}
	    }
	} else {
	    $USACAN_DST=0;   return 0;
	}
    } elsif ($mm == 11) {
	if ($dd > 7) {
	    $USACAN_DST=0;   return 0;
	} elsif ($dd < ($dow+1)) {
	    $USACAN_DST=1;   return 1;
	} elsif ($dow == 0) {
	    if ($timezone) { # UTC calculations
		if ($ns < (7200+($timezone-1)*3600)) {
		    $USACAN_DST=1;   return 1;
		} else {
		    $USACAN_DST=0;   return 0;
		}
	    } else { # local time calculations
		if ($ns < 7200) {
		    $USACAN_DST=1;   return 1;
		} else {
		    $USACAN_DST=0;   return 0;
		}
	    }
	} else {
	    $USACAN_DST=0;   return 0;
	}
    } # end of month checks
} # end of subroutine dstcalc




sub NA_dstcalc {
#**********************************************************************
# FSA-LSO
#   Mexico and parts of North America
#     This is returns 1 if Daylight Savings Time is in effect and 0 if 
#       Standard time is in effect.
#     Based on first Sunday in April and last Sunday in October at 2 am.
#**********************************************************************
    
	$NA_DST=0;
	$mm = $mon;
	$dd = $mday;
	$ns = $dsec;
	$dow= $wday;

    if ($mm < 4 || $mm > 10) {
	$NA_DST=0;   return 0;
    } elsif ($mm >= 5 && $mm <= 9) {
	$NA_DST=1;   return 1;
    } elsif ($mm == 4) {
	if ($dd > 7) {
	    $NA_DST=1;   return 1;
	} elsif ($dd >= ($dow+1)) {
	    if ($timezone) {
		if ($dow == 0 && $ns < (7200+$timezone*3600)) {
		    $NA_DST=0;   return 0;
		} else {
		    $NA_DST=1;   return 1;
		}
	    } else {
		if ($dow == 0 && $ns < 7200) {
		    $NA_DST=0;   return 0;
		} else {
		    $NA_DST=1;   return 1;
		}
	    }
	} else {
	    $NA_DST=0;   return 0;
	}
    } elsif ($mm == 10) {
	if ($dd < 25) {
	    $NA_DST=1;   return 1;
	} elsif ($dd < ($dow+25)) {
	    $NA_DST=1;   return 1;
	} elsif ($dow == 0) {
	    if ($timezone) { # UTC calculations
		if ($ns < (7200+($timezone-1)*3600)) {
		    $NA_DST=1;   return 1;
		} else {
		    $NA_DST=0;   return 0;
		}
	    } else { # local time calculations
		if ($ns < 7200) {
		    $NA_DST=1;   return 1;
		} else {
		    $NA_DST=0;   return 0;
		}
	    }
	} else {
	    $NA_DST=0;   return 0;
	}
    } # end of month checks
} # end of subroutine dstcalc




sub GBR_dstcalc {
#**********************************************************************
# LSM-LSO
#    Europe
#     This is returns 1 if Daylight Savings Time is in effect and 0 if 
#       Standard time is in effect.
#     Based on last Sunday in March and last Sunday in October at 1 am.
#**********************************************************************
    
	$GBR_DST=0;
	$mm = $mon;
	$dd = $mday;
	$ns = $dsec;
	$dow= $wday;

    if ($mm < 3 || $mm > 10) {
	$GBR_DST=0;   return 0;
    } elsif ($mm >= 4 && $mm <= 9) {
	$GBR_DST=1;   return 1;
    } elsif ($mm == 3) {
	if ($dd < 25) {
	    $GBR_DST=0;   return 0;
	} elsif ($dd < ($dow+25)) {
	    $GBR_DST=0;   return 0;
	} elsif ($dow == 0) {
	    if ($timezone) { # UTC calculations
		if ($ns < (3600+($timezone-1)*3600)) {
		    $GBR_DST=0;   return 0;
		} else {
		    $GBR_DST=1;   return 1;
		}
	    } else { # local time calculations
		if ($ns < 3600) {
		    $GBR_DST=0;   return 0;
		} else {
		    $GBR_DST=1;   return 1;
		}
	    }
	} else {
	    $GBR_DST=1;   return 1;
	}
    } elsif ($mm == 10) {
	if ($dd < 25) {
	    $GBR_DST=1;   return 1;
	} elsif ($dd < ($dow+25)) {
	    $GBR_DST=1;   return 1;
	} elsif ($dow == 0) {
	    if ($timezone) { # UTC calculations
		if ($ns < (3600+($timezone-1)*3600)) {
		    $GBR_DST=1;   return 1;
		} else {
		    $GBR_DST=0;   return 0;
		}
	    } else { # local time calculations
		if ($ns < 3600) {
		    $GBR_DST=1;   return 1;
		} else {
		    $GBR_DST=0;   return 0;
		}
	    }
	} else {
	    $GBR_DST=0;   return 0;
	}
    } # end of month checks
} # end of subroutine dstcalc




sub AUS_dstcalc {
#**********************************************************************
# LSO-LSM - Australia, for 2008-9 Western Australia only (stopped in 2009)
#     This is returns 1 if Daylight Savings Time is in effect and 0 if 
#       Standard time is in effect.
#     Based on last Sunday in October and last Sunday in March at 1 am.
#**********************************************************************
    
	$AUS_DST=0;
	$mm = $mon;
	$dd = $mday;
	$ns = $dsec;
	$dow= $wday;

    if ($mm < 3 || $mm > 10) {
	$AUS_DST=1;   return 1;
    } elsif ($mm >= 4 && $mm <= 9) {
	$AUS_DST=0;   return 0;
    } elsif ($mm == 3) {
	if ($dd < 25) {
	    $AUS_DST=1;   return 1;
	} elsif ($dd < ($dow+25)) {
	    $AUS_DST=1;   return 1;
	} elsif ($dow == 0) {
	    if ($timezone) { # UTC calculations
		if ($ns < (3600+($timezone-1)*3600)) {
		    $AUS_DST=1;   return 1;
		} else {
		    $AUS_DST=0;   return 0;
		}
	    } else { # local time calculations
		if ($ns < 3600) {
		    $AUS_DST=1;   return 1;
		} else {
		    $AUS_DST=0;   return 0;
		}
	    }
	} else {
	    $AUS_DST=0;   return 0;
	}
    } elsif ($mm == 10) {
	if ($dd < 25) {
	    $AUS_DST=0;   return 0;
	} elsif ($dd < ($dow+25)) {
	    $AUS_DST=0;   return 0;
	} elsif ($dow == 0) {
	    if ($timezone) { # UTC calculations
		if ($ns < (3600+($timezone-1)*3600)) {
		    $AUS_DST=0;   return 0;
		} else {
		    $AUS_DST=1;   return 1;
		}
	    } else { # local time calculations
		if ($ns < 3600) {
		    $AUS_DST=0;   return 0;
		} else {
		    $AUS_DST=1;   return 1;
		}
	    }
	} else {
	    $AUS_DST=1;   return 1;
	}
    } # end of month checks
} # end of subroutine dstcalc





sub AUST_dstcalc {
#**********************************************************************
# FSO-LSM
#   TASMANIA ONLY (stopped in 2008)
#     This is returns 1 if Daylight Savings Time is in effect and 0 if 
#       Standard time is in effect.
#     Based on first Sunday in October and last Sunday in March at 1 am.
#**********************************************************************
    
	$AUST_DST=0;
	$mm = $mon;
	$dd = $mday;
	$ns = $dsec;
	$dow= $wday;

    if ($mm < 3 || $mm > 10) {
	$AUST_DST=1;   return 1;
    } elsif ($mm >= 4 && $mm <= 9) {
	$AUST_DST=0;   return 0;
    } elsif ($mm == 3) {
	if ($dd < 25) {
	    $AUST_DST=1;   return 1;
	} elsif ($dd < ($dow+25)) {
	    $AUST_DST=1;   return 1;
	} elsif ($dow == 0) {
	    if ($timezone) { # UTC calculations
		if ($ns < (3600+($timezone-1)*3600)) {
		    $AUST_DST=1;   return 1;
		} else {
		    $AUST_DST=0;   return 0;
		}
	    } else { # local time calculations
		if ($ns < 3600) {
		    $AUST_DST=1;   return 1;
		} else {
		    $AUST_DST=0;   return 0;
		}
	    }
	} else {
	    $AUST_DST=0;   return 0;
	}
    } elsif ($mm == 10) {
	if ($dd >= 8) {
	    $AUST_DST=1;   return 1;
	} elsif ($dd >= ($dow+1)) {
	    if ($timezone) {
		if ($dow == 0 && $ns < (7200+$timezone*3600)) {
		    $AUST_DST=0;   return 0;
		} else {
		    $AUST_DST=1;   return 1;
		}
	    } else {
		if ($dow == 0 && $ns < 3600) {
		    $AUST_DST=0;   return 0;
		} else {
		    $AUST_DST=1;   return 1;
		}
	    }
	} else {
	    $AUST_DST=0;   return 0;
	}
    } # end of month checks
} # end of subroutine dstcalc





sub AUSE_dstcalc {
#**********************************************************************
# FSO-FSA
#   2008+ AUSTRALIA ONLY (country code 61)
#     This is returns 1 if Daylight Savings Time is in effect and 0 if 
#       Standard time is in effect.
#     Based on first Sunday in October and first Sunday in April at 1 am.
#**********************************************************************
    
	$AUSE_DST=0;
	$mm = $mon;
	$dd = $mday;
	$ns = $dsec;
	$dow= $wday;

    if ($mm < 4 || $mm > 10) {
	$AUSE_DST=1;   return 1;
    } elsif ($mm >= 5 && $mm <= 9) {
	$AUSE_DST=0;   return 0;
    } elsif ($mm == 4) {
	if ($dd > 7) {
	    $AUSE_DST=0;   return 1;
	} elsif ($dd >= ($dow+1)) {
	    if ($timezone) {
		if ($dow == 0 && $ns < (3600+$timezone*3600)) {
		    $AUSE_DST=1;   return 0;
		} else {
		    $AUSE_DST=0;   return 1;
		}
	    } else {
		if ($dow == 0 && $ns < 7200) {
		    $AUSE_DST=1;   return 0;
		} else {
		    $AUSE_DST=0;   return 1;
		}
	    }
	} else {
	    $AUSE_DST=1;   return 0;
	}
    } elsif ($mm == 10) {
	if ($dd >= 8) {
	    $AUSE_DST=1;   return 1;
	} elsif ($dd >= ($dow+1)) {
	    if ($timezone) {
		if ($dow == 0 && $ns < (7200+$timezone*3600)) {
		    $AUSE_DST=0;   return 0;
		} else {
		    $AUSE_DST=1;   return 1;
		}
	    } else {
		if ($dow == 0 && $ns < 3600) {
		    $AUSE_DST=0;   return 0;
		} else {
		    $AUSE_DST=1;   return 1;
		}
	    }
	} else {
	    $AUSE_DST=0;   return 0;
	}
    } # end of month checks
} # end of subroutine dstcalc





sub NZL_dstcalc {
#**********************************************************************
# FSO-TSM (stopped in 2007)
#     This is returns 1 if Daylight Savings Time is in effect and 0 if 
#       Standard time is in effect.
#     Based on first Sunday in October and third Sunday in March at 1 am.
#**********************************************************************
    
	$NZL_DST=0;
	$mm = $mon;
	$dd = $mday;
	$ns = $dsec;
	$dow= $wday;

    if ($mm < 3 || $mm > 10) {
	$NZL_DST=1;   return 1;
    } elsif ($mm >= 4 && $mm <= 9) {
	$NZL_DST=0;   return 0;
    } elsif ($mm == 3) {
	if ($dd < 14) {
	    $NZL_DST=1;   return 1;
	} elsif ($dd < ($dow+14)) {
	    $NZL_DST=1;   return 1;
	} elsif ($dow == 0) {
	    if ($timezone) { # UTC calculations
		if ($ns < (3600+($timezone-1)*3600)) {
		    $NZL_DST=1;   return 1;
		} else {
		    $NZL_DST=0;   return 0;
		}
	    } else { # local time calculations
		if ($ns < 3600) {
		    $NZL_DST=1;   return 1;
		} else {
		    $NZL_DST=0;   return 0;
		}
	    }
	} else {
	    $NZL_DST=0;   return 0;
	}
    } elsif ($mm == 10) {
	if ($dd >= 8) {
	    $NZL_DST=1;   return 1;
	} elsif ($dd >= ($dow+1)) {
	    if ($timezone) {
		if ($dow == 0 && $ns < (7200+$timezone*3600)) {
		    $NZL_DST=0;   return 0;
		} else {
		    $NZL_DST=1;   return 1;
		}
	    } else {
		if ($dow == 0 && $ns < 3600) {
		    $NZL_DST=0;   return 0;
		} else {
		    $NZL_DST=1;   return 1;
		}
	    }
	} else {
	    $NZL_DST=0;   return 0;
	}
    } # end of month checks
} # end of subroutine dstcalc




sub NZLN_dstcalc {
#**********************************************************************
# LSS-FSA
#   2007+ NEW ZEALAND (country code 64)
#     This is returns 1 if Daylight Savings Time is in effect and 0 if 
#       Standard time is in effect.
#     Based on last Sunday in September and first Sunday in April at 1 am.
#**********************************************************************
    
	$NZLN_DST=0;
	$mm = $mon;
	$dd = $mday;
	$ns = $dsec;
	$dow= $wday;

    if ($mm < 4 || $mm > 9) {
	$NZLN_DST=1;   return 1;
    } elsif ($mm >= 5 && $mm <= 9) {
	$NZLN_DST=0;   return 0;
    } elsif ($mm == 4) {
	if ($dd > 7) {
	    $NZLN_DST=0;   return 1;
	} elsif ($dd >= ($dow+1)) {
	    if ($timezone) {
		if ($dow == 0 && $ns < (3600+$timezone*3600)) {
		    $NZLN_DST=1;   return 0;
		} else {
		    $NZLN_DST=0;   return 1;
		}
	    } else {
		if ($dow == 0 && $ns < 7200) {
		    $NZLN_DST=1;   return 0;
		} else {
		    $NZLN_DST=0;   return 1;
		}
	    }
	} else {
	    $NZLN_DST=1;   return 0;
	}
    } elsif ($mm == 9) {
	if ($dd < 25) {
	    $NZLN_DST=0;   return 0;
	} elsif ($dd < ($dow+25)) {
	    $NZLN_DST=0;   return 0;
	} elsif ($dow == 0) {
	    if ($timezone) { # UTC calculations
		if ($ns < (3600+($timezone-1)*3600)) {
		    $NZLN_DST=0;   return 0;
		} else {
		    $NZLN_DST=1;   return 1;
		}
	    } else { # local time calculations
		if ($ns < 3600) {
		    $NZLN_DST=0;   return 0;
		} else {
		    $NZLN_DST=1;   return 1;
		}
	    }
	} else {
	    $NZLN_DST=1;   return 1;
	}
    } # end of month checks
} # end of subroutine dstcalc






sub BZL_dstcalc {
#**********************************************************************
# TSO-LSF
#   BRAZIL
#     This is returns 1 if Daylight Savings Time is in effect and 0 if 
#       Standard time is in effect. Brazil
#     Based on Third Sunday October to Last Sunday February at 1 am.
#**********************************************************************
    
	$BZL_DST=0;
	$mm = $mon;
	$dd = $mday;
	$ns = $dsec;
	$dow= $wday;

    if ($mm < 2 || $mm > 10) {
	$BZL_DST=1;   return 1;
    } elsif ($mm >= 3 && $mm <= 9) {
	$BZL_DST=0;   return 0;
    } elsif ($mm == 2) {
	if ($dd < 22) {
	    $BZL_DST=1;   return 1;
	} elsif ($dd < ($dow+22)) {
	    $BZL_DST=1;   return 1;
	} elsif ($dow == 0) {
	    if ($timezone) { # UTC calculations
		if ($ns < (3600+($timezone-1)*3600)) {
		    $BZL_DST=1;   return 1;
		} else {
		    $BZL_DST=0;   return 0;
		}
	    } else { # local time calculations
		if ($ns < 3600) {
		    $BZL_DST=1;   return 1;
		} else {
		    $BZL_DST=0;   return 0;
		}
	    }
	} else {
	    $BZL_DST=0;   return 0;
	}
    } elsif ($mm == 10) {
	if ($dd < 22) {
	    $BZL_DST=0;   return 0;
	} elsif ($dd < ($dow+22)) {
	    $BZL_DST=0;   return 0;
	} elsif ($dow == 0) {
	    if ($timezone) { # UTC calculations
		if ($ns < (3600+($timezone-1)*3600)) {
		    $BZL_DST=0;   return 0;
		} else {
		    $BZL_DST=1;   return 1;
		}
	    } else { # local time calculations
		if ($ns < 3600) {
		    $BZL_DST=0;   return 0;
		} else {
		    $BZL_DST=1;   return 1;
		}
	    }
	} else {
	    $BZL_DST=1;   return 1;
	}
    } # end of month checks
} # end of subroutine dstcalc

