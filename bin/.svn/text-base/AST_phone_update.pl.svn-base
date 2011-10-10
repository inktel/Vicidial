#!/usr/bin/perl
#
# AST_phone_update.pl version 2.4
#
# DESCRIPTION:
# checks the registered IP address of the phone and updates the phones table
#
# Copyright (C) 2010  Matt Florell <vicidial@gmail.com>    LICENSE: AGPLv2
#
# 70521-1529 - first build
# 100625-1220 - Added waitfors after logout to fix broken pipe errors in asterisk <MikeC>
#

# constants
$DB=0;  # Debug flag, set to 0 for no debug messages per minute
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
	print "allowed run time options:\n  [-t] = test\n  [-debug] = verbose debug messages\n\n";
	}
	else
	{
		if ($args =~ /-debug/i)
		{
		$DB=1; # Debug flag
		}
		if ($args =~ /-t/i)
		{
		$TEST=1;
		$T=1;
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

if (!$VARDB_port) {$VARDB_port='3306';}

use Time::HiRes ('gettimeofday','usleep','sleep');  # necessary to have perl sleep command of less than one second
use DBI;
use Net::Telnet ();
	  
 $dbhA = DBI->connect("DBI:mysql:$VARDB_database:$VARDB_server:$VARDB_port", "$VARDB_user", "$VARDB_pass")
 or die "Couldn't connect to database: " . DBI->errstr;
 
### Grab Server values from the database
$stmtA = "SELECT telnet_host,telnet_port,ASTmgrUSERNAME,ASTmgrSECRET,ASTmgrUSERNAMEupdate,ASTmgrUSERNAMElisten,ASTmgrUSERNAMEsend,max_vicidial_trunks,answer_transfer_agent,local_gmt,ext_context FROM servers where server_ip = '$server_ip';";

$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
if ($DB) {print "|$stmtA|\n";}
$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
$sthArows=$sthA->rows;
$rec_count=0;
if ($sthArows > 0)
    {
	   @aryA = $sthA->fetchrow_array;
		$DBtelnet_host	=			"$aryA[0]";
		$DBtelnet_port	=			"$aryA[1]";
		$DBASTmgrUSERNAME	=		"$aryA[2]";
		$DBASTmgrSECRET	=			"$aryA[3]";
		$DBASTmgrUSERNAMEupdate	=	"$aryA[4]";
		$DBASTmgrUSERNAMElisten	=	"$aryA[5]";
		$DBASTmgrUSERNAMEsend	=	"$aryA[6]";
		$DBmax_vicidial_trunks	=	"$aryA[7]";
		$DBanswer_transfer_agent=	"$aryA[8]";
		$DBSERVER_GMT		=		"$aryA[9]";
		$DBext_context	=			"$aryA[10]";
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
	

############# BEGIN SIP SECTION ################################################
if($DB){print "\n\nSIP EXTENSIONS:\n";}


@PTextensions=@MT; @PTphone_ips=@MT;
$stmtA = "SELECT extension,phone_ip from phones where server_ip='$server_ip' and protocol='SIP'";
if ($DB) {print "|$stmtA|\n";}
$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
$sthArows=$sthA->rows;
$rec_count=0;
while ($sthArows > $rec_count)
    {
	   @aryA = $sthA->fetchrow_array;
		$PTextensions[$rec_count] =		"$aryA[0]";
		$PTphone_ips[$rec_count] =		"$aryA[1]";
		$rec_count++;
    }
    $sthA->finish(); 


### connect to asterisk manager through telnet
$t = new Net::Telnet (Port => 5038,
					  Prompt => '/.*[\$%#>] $/',
					  Output_record_separator => '',);
#$fh = $t->dump_log("$telnetlog");  # uncomment for telnet log
	if (length($ASTmgrUSERNAMEsend) > 3) {$telnet_login = $ASTmgrUSERNAMEsend;}
	else {$telnet_login = $ASTmgrUSERNAME;}

$t->open("$telnet_host"); 
$t->waitfor('/[01]\n$/');			# print login
$t->print("Action: Login\nUsername: $telnet_login\nSecret: $ASTmgrSECRET\n\n");
$t->waitfor('/Authentication accepted/');		# waitfor auth accepted


$i=0;
foreach(@PTextensions)
	{
	@list_channels=@MT;
	$t->buffer_empty;
	@list_channels = $t->cmd(String => "Action: Command\nCommand: sip show peer $PTextensions[$i]\n\nAction: Ping\n\n", Prompt => '/Response: Pong.*/'); 

	$j=0;
	foreach(@list_channels)
		{		#  Addr->IP     : 10.10.14.25 Port 5060
		if ($list_channels[$j] =~ /  Addr->IP     : /)
			{
			$NEW_IPaddr[$i] = "$list_channels[$j]";
			$NEW_IPaddr[$i] =~ s/  Addr->IP     : | Port .*|\n|\r|\t//g;
			}

		$j++;
		}

	if($DB){print "Phone IP- $PTextensions[$i]    NEW:|$NEW_IPaddr[$i]|  OLD:|$PTphone_ips[$i]|    ";}
	if ( ($NEW_IPaddr[$i] =~ /$PTphone_ips[$i]/) && (length($NEW_IPaddr[$i]) eq length($PTphone_ips[$i])) )
		{
		if($DB){print "PHONE IP UNCHANGED, DOING NOTHING FOR THIS EXTENSION\n";}
		}
	else
		{
		if ($NEW_IPaddr[$i] =~ /Unspecified/) 
			{
			if($DB){print "PHONE IP UNSPECIFIED, DOING NOTHING FOR THIS EXTENSION\n";}
			}
		else
			{
			$stmtA = "UPDATE phones set phone_ip='$NEW_IPaddr[$i]' where server_ip='$server_ip' and extension='$PTextensions[$i]'";
				if($DB){print STDERR "\n|$stmtA|\n";}
			$affected_rows = $dbhA->do($stmtA); #  or die  "Couldn't execute query:|$stmtA|\n";
			}
		}

	$i++;
		### sleep for 10 hundredths of a second
		usleep(1*100*1000);
	}


$t->buffer_empty;
@hangup = $t->cmd(String => "Action: Logoff\n\n", Prompt => "/.*/"); 
$t->buffer_empty;
$t->waitfor(Match => '/Message:.*\n\n/', Timeout => 10);
$ok = $t->close;

############# END SIP SECTION ################################################



############# BEGIN IAX2 SECTION ################################################
if($DB){print "\n\niax2 EXTENSIONS:\n";}

@PTextensions=@MT; @PTphone_ips=@MT;
$stmtA = "SELECT extension,phone_ip from phones where server_ip='$server_ip' and protocol='IAX2'";
if ($DB) {print "|$stmtA|\n";}
$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
$sthArows=$sthA->rows;
$rec_count=0;
while ($sthArows > $rec_count)
    {
	   @aryA = $sthA->fetchrow_array;
		$PTextensions[$rec_count] =		"$aryA[0]";
		$PTphone_ips[$rec_count] =		"$aryA[1]";
		$rec_count++;
    }
    $sthA->finish(); 


### connect to asterisk manager through telnet
$t = new Net::Telnet (Port => 5038,
					  Prompt => '/.*[\$%#>] $/',
					  Output_record_separator => '',);
#$fh = $t->dump_log("$telnetlog");  # uncomment for telnet log
	if (length($ASTmgrUSERNAMEsend) > 3) {$telnet_login = $ASTmgrUSERNAMEsend;}
	else {$telnet_login = $ASTmgrUSERNAME;}

$t->open("$telnet_host"); 
$t->waitfor('/[01]\n$/');			# print login
$t->print("Action: Login\nUsername: $telnet_login\nSecret: $ASTmgrSECRET\n\n");
$t->waitfor('/Authentication accepted/');		# waitfor auth accepted


$i=0;
foreach(@PTextensions)
	{
	@list_channels=@MT;
	$t->buffer_empty;
	@list_channels = $t->cmd(String => "Action: Command\nCommand: iax2 show peer $PTextensions[$i]\n\nAction: Ping\n\n", Prompt => '/Response: Pong.*/'); 

	$j=0;
	foreach(@list_channels)
		{		#  Addr->IP     : 10.10.14.25 Port 5060
		if ($list_channels[$j] =~ /  Addr->IP     : /)
			{
			$NEW_IPaddr[$i] = "$list_channels[$j]";
			$NEW_IPaddr[$i] =~ s/  Addr->IP     : | Port .*|\n|\r|\t//g;
			}

		$j++;
		}

	if($DB){print "Phone IP- $PTextensions[$i]    NEW:|$NEW_IPaddr[$i]|  OLD:|$PTphone_ips[$i]|    ";}
	if ( ($NEW_IPaddr[$i] =~ /$PTphone_ips[$i]/) && (length($NEW_IPaddr[$i]) eq length($PTphone_ips[$i])) )
		{
		if($DB){print "PHONE IP UNCHANGED, DOING NOTHING FOR THIS EXTENSION\n";}
		}
	else
		{
		if ($NEW_IPaddr[$i] =~ /Unspecified/) 
			{
			if($DB){print "PHONE IP UNSPECIFIED, DOING NOTHING FOR THIS EXTENSION\n";}
			}
		else
			{
			$stmtA = "UPDATE phones set phone_ip='$NEW_IPaddr[$i]' where server_ip='$server_ip' and extension='$PTextensions[$i]'";
				if($DB){print STDERR "\n|$stmtA|\n";}
			$affected_rows = $dbhA->do($stmtA); #  or die  "Couldn't execute query:|$stmtA|\n";
			}
		}

	$i++;
		### sleep for 10 hundredths of a second
		usleep(1*100*1000);
	}


$t->buffer_empty;
@hangup = $t->cmd(String => "Action: Logoff\n\n", Prompt => "/.*/"); 
$t->buffer_empty;
$t->waitfor(Match => '/Message:.*\n\n/', Timeout => 10);
$ok = $t->close;
############# END IAX2 SECTION ################################################





$dbhA->disconnect();

if($DB){print "DONE... Exiting...\n";}

exit;




