#!/usr/bin/perl
#
# ADMIN_qm_sync.pl version 2.4
#
# DESCRIPTION:
# to be run frequently to sync the vicidial_users and remote agents to the QM
# agenti_noti table, also can sync dids, ivrs, in-groups and campaigns
#
# This program only needs to be run by one server
#
# Copyright (C) 2011  Matt Florell <vicidial@gmail.com>    LICENSE: AGPLv2
#
# CHANGES
# 110425-0853 - First Build
# 110506-1516 - Added DIDs, IVRs, in-groups(queues), campaigns(queues) and all-alias to the sync options
#

# constants
$US='__';
$MT[0]='';

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
		print "  [--user] = will synchronize users\n";
		print "  [--remote-agents] = will check for remote agents and sync for the number of lines defined for each\n";
		print "  [--dids] = will sync did entries to dnis entries in QM\n";
		print "  [--ivrs] = will sync call menu entries to ivr entries in QM\n";
		print "  [--ingroups] = will sync in-group entries to queue entries in QM\n";
		print "  [--campaigns] = will sync campaign entries to queue entries in QM\n";
		print "  [--all-sync] = will sync all of the above in QM\n";
		print "  [--all-alias-sync] = will sync all queues in QM into the default \"00 All Queues\" alias\n";
		print "  [-q] = quiet, no output\n";
		print "  [--test] = test\n";
		print "  [--debug] = verbose debug messages\n";
		print "  [--debugX] = Extra-verbose debug messages\n\n";
		exit;
		}
	else
		{
		if ($args =~ /-q/i)
			{
			$Q=1; # quiet
			}
		if ($args =~ /-debug/i)
			{
			$DB=1; # Debug flag
			if ($Q < 1) {print "\n----- DEBUGGING -----\n\n";}
			}
		if ($args =~ /--debugX/i)
			{
			$DBX=1;
			if ($Q < 1) {print "\n----- SUPER-DUPER DEBUGGING -----\n\n";}
			}
		if ($args =~ /-test/i)
			{
			$TEST=1;
			$T=1;
			if ($Q < 1) {print "\n----- TEST RUN, NO UPDATES -----\n\n";}
			}
		if ($args =~ /-all-alias-sync/i)
			{
			$SYNC_all_alias=1;
			if ($Q < 1) {print "\n----- ALL ALIAS SYNC -----\n\n";}
			}
		if ($args =~ /-all-sync/i)
			{
			$SYNC_user=1;
			$SYNC_remoteagents=1;
			$SYNC_dids=1;
			$SYNC_ivrs=1;
			$SYNC_ingroups=1;
			$SYNC_campaigns=1;
			if ($Q < 1) {print "\n----- ALL SYNC -----\n\n";}
			}
		if ($args =~ /-user/i)
			{
			$SYNC_user=1;
			if ($Q < 1) {print "\n----- USER SYNC -----\n\n";}
			}
		if ($args =~ /-remote-agents/i)
			{
			$SYNC_remoteagents=1;
			if ($Q < 1) {print "\n----- REMOTE AGENT SYNC -----\n\n";}
			}
		if ($args =~ /-dids/i)
			{
			$SYNC_dids=1;
			if ($Q < 1) {print "\n----- DID SYNC -----\n\n";}
			}
		if ($args =~ /-ivrs/i)
			{
			$SYNC_ivrs=1;
			if ($Q < 1) {print "\n----- IVR SYNC -----\n\n";}
			}
		if ($args =~ /-ingroups/i)
			{
			$SYNC_ingroups=1;
			if ($Q < 1) {print "\n----- IN-GROUP SYNC -----\n\n";}
			}
		if ($args =~ /-campaigns/i)
			{
			$SYNC_campaigns=1;
			if ($Q < 1) {print "\n----- CAMPAIGN SYNC -----\n\n";}
			}
		}
	}
else
	{
	#	print "no command line options set\n";
	}
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

if (!$CLEANLOGfile) {$CLEANLOGfile = "$PATHlogs/qmsync.$Hyear-$Hmon-$Hmday";}

if (!$VARDB_port) {$VARDB_port='3306';}

use DBI;	  

$dbhA = DBI->connect("DBI:mysql:$VARDB_database:$VARDB_server:$VARDB_port", "$VARDB_user", "$VARDB_pass")
or die "Couldn't connect to database: " . DBI->errstr;

#############################################
##### START QUEUEMETRICS LOGGING LOOKUP #####
$stmtA = "SELECT enable_queuemetrics_logging,queuemetrics_server_ip,queuemetrics_dbname,queuemetrics_login,queuemetrics_pass,queuemetrics_log_id FROM system_settings;";
$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
$sthArows=$sthA->rows;
if ($sthArows > 0)
	{
	@aryA = $sthA->fetchrow_array;
	$enable_queuemetrics_logging =	$aryA[0];
	$queuemetrics_server_ip	=	$aryA[1];
	$queuemetrics_dbname =		$aryA[2];
	$queuemetrics_login=		$aryA[3];
	$queuemetrics_pass =		$aryA[4];
	$queuemetrics_log_id =		$aryA[5];
	}
$sthA->finish();
##### END QUEUEMETRICS LOGGING LOOKUP #####
###########################################


$dbhB = DBI->connect("DBI:mysql:$queuemetrics_dbname:$queuemetrics_server_ip:3306", "$queuemetrics_login", "$queuemetrics_pass")
 or die "Couldn't connect to database: " . DBI->errstr;

if ($DBX) {print "CONNECTED TO QM DATABASE:  $queuemetrics_server_ip|$queuemetrics_dbname\n";}





##### BEGIN sync of vicidial_users to agenti_noti #####
if ($SYNC_user > 0)
	{
	if ($DBX) {print "\n\n";}
	if ($DB) {print " - starting sync of vicidial_users to agenti_noti\n";}
	$found_records=0;
	$updated_records=0;
	$added_records=0;

	### Gather distinct users in vicidial_users
	$stmtA = "SELECT user,full_name from vicidial_users limit 100000;";
	if ($DBX) {print "$stmtA\n";}
	$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
	$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
	$sthArowsU=$sthA->rows;

	$i=0;
	while ($sthArowsU > $i)
		{
		@aryA = $sthA->fetchrow_array;	
		$Vuser[$i]	=			$aryA[0];
		$Vfullname[$i]	=		$aryA[1];
		$i++;
		}
	$sthA->finish();

	$i=0;
	while ($sthArowsU > $i)
		{
		### Find if agent record exists with user id and fullname identical
		$stmtB = "SELECT count(*) FROM agenti_noti where nome_agente='Agent/$Vuser[$i]' and descr_agente='$Vfullname[$i]';";
		$sthB = $dbhB->prepare($stmtB) or die "preparing: ",$dbhB->errstr;
		$sthB->execute or die "executing: $stmtB ", $dbhB->errstr;
		$AN_records=$sthB->rows;
		if ($AN_records > 0)
			{
			@aryB = $sthB->fetchrow_array;
			$AN_count =		$aryB[0];
			}
		$sthB->finish();

		if ($AN_count < 1)
			{
			### Find if agent record exists with user id identical
			$stmtB = "SELECT count(*) FROM agenti_noti where nome_agente='Agent/$Vuser[$i]';";
			$sthB = $dbhB->prepare($stmtB) or die "preparing: ",$dbhB->errstr;
			$sthB->execute or die "executing: $stmtB ", $dbhB->errstr;
			$ANX_records=$sthB->rows;
			if ($ANX_records > 0)
				{
				@aryB = $sthB->fetchrow_array;
				$ANX_count =		$aryB[0];
				}
			$sthB->finish();

			if ($ANX_count < 1)
				{
				### add a new agenti_noti record 
				$stmtB = "INSERT INTO agenti_noti(nome_agente,descr_agente,location,current_terminal,xmpp_address,payroll_code,sys_dt_creazione,sys_user_creazione,sys_dt_modifica,sys_user_modifica,chiave_agente) values('agent/$Vuser[$i]','$Vfullname[$i]','7','-','','',NOW(),'32',NOW(),'32','');";
				if ($TEST < 1)
					{$Baffected_rows = $dbhB->do($stmtB);}
				if ($DB) {print "     AGENT record inserted: $Baffected_rows|$stmtB|\n";}

				$event_string = "AGENT INSERT: $i|$Vuser[$i]|$Vfullname[$i]|$Baffected_rows|$stmtB";
				&event_logger;

				$added_records++;
				}
			else
				{
				### update agenti_noti record with proper name
				$stmtB = "UPDATE agenti_noti SET descr_agente='$Vfullname[$i]' where nome_agente='agent/$Vuser[$i]' LIMIT 1;";
				if ($TEST < 1)
					{$Baffected_rows = $dbhB->do($stmtB);}
				if ($DB) {print "     AGENT record updated: $Baffected_rows|$stmtB|\n";}

				$event_string = "AGENT UPDATE: $i|$Vuser[$i]|$Vfullname[$i]|$Baffected_rows|$stmtB";
				&event_logger;

				$updated_records++;
				}
			}
		
		else
			{
			if ($DB) {print "   agent exists: $Vuser[$i] - $Vfullname[$i]\n";}
			$found_records++;
			}

		$i++;
		}

	if ($DB) {print " - finished user sync:\n";}
	if ($DB) {print "     records scanned:       $i\n";}
	if ($DB) {print "     records found:      $found_records\n";}
	if ($DB) {print "     records updated:    $updated_records\n";}
	if ($DB) {print "     records added:      $added_records\n";}
	}
##### END sync of vicidial_users to agenti_noti #####





##### BEGIN sync of vicidial_remote_agents to agenti_noti #####
if ($SYNC_remoteagents > 0)
	{
	if ($DBX) {print "\n\n";}
	if ($DB) {print " - starting sync of vicidial_remote_agents to agenti_noti\n";}
	$found_records=0;
	$updated_records=0;
	$added_records=0;

	### Gather distinct remote agents in vicidial_remote_agents
	$stmtA = "SELECT user_start,number_of_lines,full_name from vicidial_remote_agents vra,vicidial_users vu where vu.user=vra.user_start and number_of_lines > 0 limit 100000;";
	if ($DBX) {print "$stmtA\n";}
	$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
	$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
	$sthArowsU=$sthA->rows;
	$i=0;
	while ($sthArowsU > $i)
		{
		@aryA = $sthA->fetchrow_array;	
		$Vuser[$i]	=				$aryA[0];
		$Vnumber_of_lines[$i] =		$aryA[1];
		$Vfullname[$i]	=			$aryA[2];
		$i++;
		}
	$sthA->finish();

	$i=0;
	while ($sthArowsU > $i)
		{
		$ra_count=0;
		while ($ra_count < $Vnumber_of_lines[$i])
			{
			$Vuser[$i]++;

			### Find if agent record exists with user id and fullname identical
			$stmtB = "SELECT count(*) FROM agenti_noti where nome_agente='Agent/$Vuser[$i]' and descr_agente='$Vfullname[$i]';";
			$sthB = $dbhB->prepare($stmtB) or die "preparing: ",$dbhB->errstr;
			$sthB->execute or die "executing: $stmtB ", $dbhB->errstr;
			$AN_records=$sthB->rows;
			if ($AN_records > 0)
				{
				@aryB = $sthB->fetchrow_array;
				$AN_count =		$aryB[0];
				}
			$sthB->finish();

			if ($AN_count < 1)
				{
				### Find if agent record exists with user id identical
				$stmtB = "SELECT count(*) FROM agenti_noti where nome_agente='Agent/$Vuser[$i]';";
				$sthB = $dbhB->prepare($stmtB) or die "preparing: ",$dbhB->errstr;
				$sthB->execute or die "executing: $stmtB ", $dbhB->errstr;
				$ANX_records=$sthB->rows;
				if ($ANX_records > 0)
					{
					@aryB = $sthB->fetchrow_array;
					$ANX_count =		$aryB[0];
					}
				$sthB->finish();

				if ($ANX_count < 1)
					{
					### add a new agenti_noti record 
					$stmtB = "INSERT INTO agenti_noti(nome_agente,descr_agente,location,current_terminal,xmpp_address,payroll_code,sys_dt_creazione,sys_user_creazione,sys_dt_modifica,sys_user_modifica,chiave_agente) values('agent/$Vuser[$i]','$Vfullname[$i]','7','-','','',NOW(),'32',NOW(),'32','');";
					if ($TEST < 1)
						{$Baffected_rows = $dbhB->do($stmtB);}
					if ($DB) {print "     AGENT record inserted: $Baffected_rows|$ra_count|$stmtB|\n";}

					$event_string = "AGENT INSERT: $i|$ra_count|$Vuser[$i]|$Vfullname[$i]|$Baffected_rows|$stmtB";
					&event_logger;

					$added_records++;
					}
				else
					{
					### update agenti_noti record with proper name
					$stmtB = "UPDATE agenti_noti SET descr_agente='$Vfullname[$i]' where nome_agente='agent/$Vuser[$i]' LIMIT 1;";
					if ($TEST < 1)
						{$Baffected_rows = $dbhB->do($stmtB);}
					if ($DB) {print "     AGENT record updated: $Baffected_rows|$ra_count|$stmtB|\n";}

					$event_string = "AGENT UPDATE: $i|$ra_count|$Vuser[$i]|$Vfullname[$i]|$Baffected_rows|$stmtB";
					&event_logger;

					$updated_records++;
					}
				}
			
			else
				{
				if ($DB) {print "   agent exists: $Vuser[$i] - $Vfullname[$i]\n";}
				$found_records++;
				}
			$ra_count++;
			}
		$i++;
		}

	if ($DB) {print " - finished remote agent sync:\n";}
	if ($DB) {print "     records scanned:       $i\n";}
	if ($DB) {print "     records found:      $found_records\n";}
	if ($DB) {print "     records updated:    $updated_records\n";}
	if ($DB) {print "     records added:      $added_records\n";}
	}
##### END sync of vicidial_remote_agents to agenti_noti #####





##### BEGIN sync of vicidial_inbound_dids to dnis #####
if ($SYNC_dids > 0)
	{
	if ($DBX) {print "\n\n";}
	if ($DB) {print " - starting sync of vicidial_inbound_dids to dnis\n";}
	$found_records=0;
	$updated_records=0;
	$added_records=0;

	### Gather distinct dids in vicidial_inbound_dids
	$stmtA = "SELECT did_pattern,did_description from vicidial_inbound_dids limit 1000000;";
	if ($DBX) {print "$stmtA\n";}
	$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
	$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
	$sthArowsD=$sthA->rows;

	$i=0;
	while ($sthArowsD > $i)
		{
		@aryA = $sthA->fetchrow_array;	
		$Vdid[$i]	=			$aryA[0];
		$Vdescription[$i]	=	$aryA[1];
		$i++;
		}
	$sthA->finish();

	$i=0;
	while ($sthArowsD > $i)
		{
		### Find if dnis exists with did and description identical
		$stmtB = "SELECT count(*) FROM dnis where dnis_k='$Vdid[$i]' and dnis_v='$Vdid[$i] - $Vdescription[$i]';";
		$sthB = $dbhB->prepare($stmtB) or die "preparing: ",$dbhB->errstr;
		$sthB->execute or die "executing: $stmtB ", $dbhB->errstr;
		$AN_records=$sthB->rows;
		if ($AN_records > 0)
			{
			@aryB = $sthB->fetchrow_array;
			$AN_count =		$aryB[0];
			}
		$sthB->finish();

		if ($AN_count < 1)
			{
			### Find if did record exists with did identical
			$stmtB = "SELECT count(*) FROM dnis where dnis_k='$Vdid[$i]';";
			$sthB = $dbhB->prepare($stmtB) or die "preparing: ",$dbhB->errstr;
			$sthB->execute or die "executing: $stmtB ", $dbhB->errstr;
			$ANX_records=$sthB->rows;
			if ($ANX_records > 0)
				{
				@aryB = $sthB->fetchrow_array;
				$ANX_count =		$aryB[0];
				}
			$sthB->finish();

			if ($ANX_count < 1)
				{
				### add a new dnis record 
				$stmtB = "INSERT INTO dnis (dnis_k,dnis_v,sys_dt_creazione,sys_user_creazione,sys_dt_modifica,sys_user_modifica,sys_optilock) values('$Vdid[$i]','$Vdid[$i] - $Vdescription[$i]',NOW(),'32',NOW(),'32','82946');";
				if ($TEST < 1)
					{$Baffected_rows = $dbhB->do($stmtB);}
				if ($DB) {print "     DNIS record inserted: $Baffected_rows|$stmtB|\n";}

				$event_string = "DNIS INSERT: $i|$Vdid[$i]|$Vdescription[$i]|$Baffected_rows|$stmtB";
				&event_logger;

				$added_records++;
				}
			else
				{
				### update dnis record with proper description
				$stmtB = "UPDATE dnis SET dnis_v='$Vdid[$i] - $Vdescription[$i]' where dnis_k='$Vdid[$i]' LIMIT 1;";
				if ($TEST < 1)
					{$Baffected_rows = $dbhB->do($stmtB);}
				if ($DB) {print "     DNIS record updated: $Baffected_rows|$stmtB|\n";}

				$event_string = "DNIS UPDATE: $i|$Vdid[$i]|$Vdescription[$i]|$Baffected_rows|$stmtB";
				&event_logger;

				$updated_records++;
				}
			}
		
		else
			{
			if ($DB) {print "   did exists: $Vdid[$i] - $Vdescription[$i]\n";}
			$found_records++;
			}

		$i++;
		}

	if ($DB) {print " - finished did sync:\n";}
	if ($DB) {print "     records scanned:       $i\n";}
	if ($DB) {print "     records found:      $found_records\n";}
	if ($DB) {print "     records updated:    $updated_records\n";}
	if ($DB) {print "     records added:      $added_records\n";}
	}
##### END sync of vicidial_inbound_dids to dnis #####





##### BEGIN sync of vicidial_call_menu to ivr #####
if ($SYNC_ivrs > 0)
	{
	if ($DBX) {print "\n\n";}
	if ($DB) {print " - starting sync of vicidial_call_menu to ivr\n";}
	$found_records=0;
	$updated_records=0;
	$added_records=0;

	### Gather distinct dids in vicidial_call_menu
	$stmtA = "SELECT menu_id,menu_name from vicidial_call_menu limit 1000000;";
	if ($DBX) {print "$stmtA\n";}
	$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
	$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
	$sthArowsD=$sthA->rows;

	$i=0;
	while ($sthArowsD > $i)
		{
		@aryA = $sthA->fetchrow_array;	
		$Vivr[$i]	=			$aryA[0];
		$Vdescription[$i]	=	$aryA[1];
		$i++;
		}
	$sthA->finish();

	$i=0;
	while ($sthArowsD > $i)
		{
		### Find if ivr exists with ivr and description identical
		$stmtB = "SELECT count(*) FROM ivr where ivr_k='$Vivr[$i]' and ivr_v='$Vivr[$i] - $Vdescription[$i]';";
		$sthB = $dbhB->prepare($stmtB) or die "preparing: ",$dbhB->errstr;
		$sthB->execute or die "executing: $stmtB ", $dbhB->errstr;
		$AN_records=$sthB->rows;
		if ($AN_records > 0)
			{
			@aryB = $sthB->fetchrow_array;
			$AN_count =		$aryB[0];
			}
		$sthB->finish();

		if ($AN_count < 1)
			{
			### Find if ivr record exists with ivr identical
			$stmtB = "SELECT count(*) FROM ivr where ivr_k='$Vivr[$i]';";
			$sthB = $dbhB->prepare($stmtB) or die "preparing: ",$dbhB->errstr;
			$sthB->execute or die "executing: $stmtB ", $dbhB->errstr;
			$ANX_records=$sthB->rows;
			if ($ANX_records > 0)
				{
				@aryB = $sthB->fetchrow_array;
				$ANX_count =		$aryB[0];
				}
			$sthB->finish();

			if ($ANX_count < 1)
				{
				### add a new ivr record 
				$stmtB = "INSERT INTO ivr (ivr_k,ivr_v,sys_dt_creazione,sys_user_creazione,sys_dt_modifica,sys_user_modifica,sys_optilock) values('$Vivr[$i]','$Vivr[$i] - $Vdescription[$i]',NOW(),'32',NOW(),'32','82946');";
				if ($TEST < 1)
					{$Baffected_rows = $dbhB->do($stmtB);}
				if ($DB) {print "     ivr record inserted: $Baffected_rows|$stmtB|\n";}

				$event_string = "ivr INSERT: $i|$Vivr[$i]|$Vdescription[$i]|$Baffected_rows|$stmtB";
				&event_logger;

				$added_records++;
				}
			else
				{
				### update ivr record with proper description
				$stmtB = "UPDATE ivr SET ivr_v='$Vivr[$i] - $Vdescription[$i]' where ivr_k='$Vivr[$i]' LIMIT 1;";
				if ($TEST < 1)
					{$Baffected_rows = $dbhB->do($stmtB);}
				if ($DB) {print "     ivr record updated: $Baffected_rows|$stmtB|\n";}

				$event_string = "ivr UPDATE: $i|$Vivr[$i]|$Vdescription[$i]|$Baffected_rows|$stmtB";
				&event_logger;

				$updated_records++;
				}
			}
		
		else
			{
			if ($DB) {print "   ivr exists: $Vivr[$i] - $Vdescription[$i]\n";}
			$found_records++;
			}

		$i++;
		}

	if ($DB) {print " - finished ivr sync:\n";}
	if ($DB) {print "     records scanned:       $i\n";}
	if ($DB) {print "     records found:      $found_records\n";}
	if ($DB) {print "     records updated:    $updated_records\n";}
	if ($DB) {print "     records added:      $added_records\n";}
	}
##### END sync of vicidial_call_menu to ivr #####





##### BEGIN sync of vicidial_inbound_groups to code_possibili #####
if ($SYNC_ingroups > 0)
	{
	if ($DBX) {print "\n\n";}
	if ($DB) {print " - starting sync of vicidial_inbound_groups to code_possibili\n";}
	$found_records=0;
	$updated_records=0;
	$added_records=0;

	### Gather distinct in-groups in vicidial_inbound_groups
	$stmtA = "SELECT group_id,group_name from vicidial_inbound_groups limit 1000000;";
	if ($DBX) {print "$stmtA\n";}
	$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
	$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
	$sthArowsD=$sthA->rows;

	$i=0;
	while ($sthArowsD > $i)
		{
		@aryA = $sthA->fetchrow_array;	
		$Vid[$i]	=			$aryA[0];
		$Vdescription[$i]	=	$aryA[1];
		$i++;
		}
	$sthA->finish();

	$i=0;
	while ($sthArowsD > $i)
		{
		### Find if queue exists with id and description identical
		$stmtB = "SELECT count(*) FROM code_possibili where composizione_coda='$Vid[$i]' and nome_coda='$Vdescription[$i]' and q_direction='inbound';";
		$sthB = $dbhB->prepare($stmtB) or die "preparing: ",$dbhB->errstr;
		$sthB->execute or die "executing: $stmtB ", $dbhB->errstr;
		$AN_records=$sthB->rows;
		if ($AN_records > 0)
			{
			@aryB = $sthB->fetchrow_array;
			$AN_count =		$aryB[0];
			}
		$sthB->finish();

		if ($AN_count < 1)
			{
			### Find if queue record exists with id identical
			$stmtB = "SELECT count(*) FROM code_possibili where composizione_coda='$Vid[$i]';";
			$sthB = $dbhB->prepare($stmtB) or die "preparing: ",$dbhB->errstr;
			$sthB->execute or die "executing: $stmtB ", $dbhB->errstr;
			$ANX_records=$sthB->rows;
			if ($ANX_records > 0)
				{
				@aryB = $sthB->fetchrow_array;
				$ANX_count =		$aryB[0];
				}
			$sthB->finish();

			if ($ANX_count < 1)
				{
				### add a new queue record in code_possibili
				$stmtB = "INSERT INTO code_possibili (composizione_coda,nome_coda,q_direction,sys_dt_creazione,sys_user_creazione,sys_dt_modifica,sys_user_modifica) values('$Vid[$i]','$Vdescription[$i]','inbound',NOW(),'32',NOW(),'32');";
				if ($TEST < 1)
					{$Baffected_rows = $dbhB->do($stmtB);}
				if ($DB) {print "     code_possibili in-group record inserted: $Baffected_rows|$stmtB|\n";}

				$event_string = "code_possibili INSERT: $i|$Vid[$i]|$Vdescription[$i]|$Baffected_rows|$stmtB";
				&event_logger;

				$added_records++;
				}
			else
				{
				### update queue record with proper description
				$stmtB = "UPDATE code_possibili SET nome_coda='$Vdescription[$i]',q_direction='inbound' where composizione_coda='$Vid[$i]' LIMIT 1;";
				if ($TEST < 1)
					{$Baffected_rows = $dbhB->do($stmtB);}
				if ($DB) {print "     code_possibili in-group record updated: $Baffected_rows|$stmtB|\n";}

				$event_string = "code_possibili UPDATE: $i|$Vid[$i]|$Vdescription[$i]|$Baffected_rows|$stmtB";
				&event_logger;

				$updated_records++;
				}
			}
		
		else
			{
			if ($DB) {print "   code_possibili in-group exists: $Vid[$i] - $Vdescription[$i]\n";}
			$found_records++;
			}

		$i++;
		}

	if ($DB) {print " - finished inbound queue sync:\n";}
	if ($DB) {print "     records scanned:       $i\n";}
	if ($DB) {print "     records found:      $found_records\n";}
	if ($DB) {print "     records updated:    $updated_records\n";}
	if ($DB) {print "     records added:      $added_records\n";}
	}
##### END sync of vicidial_inbound_groups to code_possibili #####





##### BEGIN sync of vicidial_campaigns to code_possibili #####
if ($SYNC_campaigns > 0)
	{
	if ($DBX) {print "\n\n";}
	if ($DB) {print " - starting sync of vicidial_campaigns to code_possibili\n";}
	$found_records=0;
	$updated_records=0;
	$added_records=0;

	### Gather distinct in-groups in vicidial_campaigns
	$stmtA = "SELECT campaign_id,campaign_name from vicidial_campaigns limit 100000;";
	if ($DBX) {print "$stmtA\n";}
	$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
	$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
	$sthArowsD=$sthA->rows;

	$i=0;
	while ($sthArowsD > $i)
		{
		@aryA = $sthA->fetchrow_array;	
		$Vid[$i]	=			$aryA[0];
		$Vdescription[$i]	=	$aryA[1];
		$i++;
		}
	$sthA->finish();

	$i=0;
	while ($sthArowsD > $i)
		{
		### Find if queue exists with id and description identical
		$stmtB = "SELECT count(*) FROM code_possibili where composizione_coda='$Vid[$i]' and nome_coda='$Vdescription[$i]' and q_direction='outbound';";
		$sthB = $dbhB->prepare($stmtB) or die "preparing: ",$dbhB->errstr;
		$sthB->execute or die "executing: $stmtB ", $dbhB->errstr;
		$AN_records=$sthB->rows;
		if ($AN_records > 0)
			{
			@aryB = $sthB->fetchrow_array;
			$AN_count =		$aryB[0];
			}
		$sthB->finish();

		if ($AN_count < 1)
			{
			### Find if queue record exists with id identical
			$stmtB = "SELECT count(*) FROM code_possibili where composizione_coda='$Vid[$i]';";
			$sthB = $dbhB->prepare($stmtB) or die "preparing: ",$dbhB->errstr;
			$sthB->execute or die "executing: $stmtB ", $dbhB->errstr;
			$ANX_records=$sthB->rows;
			if ($ANX_records > 0)
				{
				@aryB = $sthB->fetchrow_array;
				$ANX_count =		$aryB[0];
				}
			$sthB->finish();

			if ($ANX_count < 1)
				{
				### add a new queue record in code_possibili
				$stmtB = "INSERT INTO code_possibili (composizione_coda,nome_coda,q_direction,sys_dt_creazione,sys_user_creazione,sys_dt_modifica,sys_user_modifica) values('$Vid[$i]','$Vdescription[$i]','outbound',NOW(),'32',NOW(),'32');";
				if ($TEST < 1)
					{$Baffected_rows = $dbhB->do($stmtB);}
				if ($DB) {print "     code_possibili campaign record inserted: $Baffected_rows|$stmtB|\n";}

				$event_string = "code_possibili INSERT: $i|$Vid[$i]|$Vdescription[$i]|$Baffected_rows|$stmtB";
				&event_logger;

				$added_records++;
				}
			else
				{
				### update queue record with proper description
				$stmtB = "UPDATE code_possibili SET nome_coda='$Vdescription[$i]',q_direction='outbound' where composizione_coda='$Vid[$i]' LIMIT 1;";
				if ($TEST < 1)
					{$Baffected_rows = $dbhB->do($stmtB);}
				if ($DB) {print "     code_possibili campaign record updated: $Baffected_rows|$stmtB|\n";}

				$event_string = "code_possibili UPDATE: $i|$Vid[$i]|$Vdescription[$i]|$Baffected_rows|$stmtB";
				&event_logger;

				$updated_records++;
				}
			}
		
		else
			{
			if ($DB) {print "   code_possibili campaign exists: $Vid[$i] - $Vdescription[$i]\n";}
			$found_records++;
			}

		$i++;
		}

	if ($DB) {print " - finished campaign queue sync:\n";}
	if ($DB) {print "     records scanned:       $i\n";}
	if ($DB) {print "     records found:      $found_records\n";}
	if ($DB) {print "     records updated:    $updated_records\n";}
	if ($DB) {print "     records added:      $added_records\n";}
	}
##### END sync of vicidial_campaigns to code_possibili #####





##### BEGIN sync of all alias entry in code_possibili #####
if ($SYNC_all_alias > 0)
	{
	if ($DBX) {print "\n\n";}
	if ($DB) {print " - starting sync of code_possibili All Queues alias\n";}
	$found_records=0;
	$updated_records=0;
	$added_records=0;

	### Find if All Alias queue exists
	$stmtB = "SELECT count(*) FROM code_possibili where nome_coda='00 All Queues';";
	$sthB = $dbhB->prepare($stmtB) or die "preparing: ",$dbhB->errstr;
	$sthB->execute or die "executing: $stmtB ", $dbhB->errstr;
	$AAQ_records=$sthB->rows;
	if ($AAQ_records > 0)
		{
		@aryB = $sthB->fetchrow_array;
		$AAQ_count =		$aryB[0];
		}
	$sthB->finish();

	if ($AAQ_count < 1)
		{
		if ($DB) {print " - All Alias queue does not exist\n";}
		}
	else
		{
		### Gather distinct code_possibili queues
		$AAQ_list='';
		$stmtB = "SELECT composizione_coda FROM code_possibili where nome_coda NOT IN('00 All Queues','00 All');";
		$sthB = $dbhB->prepare($stmtB) or die "preparing: ",$dbhB->errstr;
		$sthB->execute or die "executing: $stmtB ", $dbhB->errstr;
		$AAQD_records=$sthB->rows;
		$i=0;
		while ($AAQD_records > $i)
			{
			@aryB = $sthB->fetchrow_array;
			$AAQ_list .=	"$aryB[0]|";
			$i++;
			}
		$sthB->finish();
		chop($AAQ_list);

		### Find if queue exists with id and description identical
		$stmtB = "SELECT count(*) FROM code_possibili where composizione_coda='$AAQ_list' and nome_coda='00 All Queues';";
		$sthB = $dbhB->prepare($stmtB) or die "preparing: ",$dbhB->errstr;
		$sthB->execute or die "executing: $stmtB ", $dbhB->errstr;
		$AN_records=$sthB->rows;
		if ($AN_records > 0)
			{
			@aryB = $sthB->fetchrow_array;
			$AN_count =		$aryB[0];
			}
		$sthB->finish();

		if ($AN_count < 1)
			{
			### update queue record with proper description
			$stmtB = "UPDATE code_possibili SET composizione_coda='$AAQ_list' where nome_coda='00 All Queues' LIMIT 1;";
			if ($TEST < 1)
				{$Baffected_rows = $dbhB->do($stmtB);}
			if ($DB) {print "     code_possibili all alias record updated: $Baffected_rows|$stmtB|\n";}

			$event_string = "code_possibili UPDATE: $i|all alias|$Baffected_rows|$stmtB";
			&event_logger;

			$updated_records++;
			}

		if ($DB) {print " - finished All Alias queue sync:\n";}
		if ($DB) {print "     records scanned:       $i\n";}
		if ($DB) {print "     records found:      $AAQ_count\n";}
		if ($DB) {print "     records updated:    $updated_records\n";}
		}
	}
##### END sync of all alias in code_possibili #####




if ($DB) {print STDERR "\nDONE\n";}

$dbhB->disconnect();

exit;






sub event_logger
	{
	### open the log file for writing ###
	open(Lout, ">>$CLEANLOGfile")
			|| die "Can't open $CLEANLOGfile: $!\n";
	print Lout "$HDSQLdate|$event_string|\n";
	close(Lout);
	$event_string='';
	}
