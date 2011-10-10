#!/usr/bin/perl
#
# ADMIN_keepalive_ALL.pl   version  2.4
#
# Designed to keep the astGUIclient processes alive and check every minute
# Replaces all other ADMIN_keepalive scripts
# Uses /etc/astguiclient.conf file to know which processes to keepalive
#
# Other functions of this program:
#  - Launches the timeclock auto-logout process
#  - clear out non-used vicidial_conferences sessions
#  - Generates Asterisk conf files and reloads Asterisk
#  - Synchronizes the audio store files
#  - Runs trigger processes at defined times
#  - Auto reset lists at defined times
#
# Copyright (C) 2011  Matt Florell <vicidial@gmail.com>    LICENSE: AGPLv2
#
# CHANGES
# 61011-1348 - First build
# 61120-2011 - Added option 7 for AST_VDauto_dial_FILL.pl
# 80227-1526 - Added option 8 for ip_relay
# 80526-1350 - Added option 9 for timeclock auto-logout
# 90211-1236 - Added auto-generation of conf files functions
# 90213-0625 - Separated the reloading of Asterisk into 4 separate steps
# 90325-2239 - Rewrote sending of reload commands to Asterisk
# 90327-1421 - Removed [globals] tag from auto-generated extensions file
# 90409-1251 - Fixed voicemail conf file issue
# 90506-1155 - Added Call Menu functionality
# 90513-0449 - Added audio store sync functionality
# 90519-1018 - Added upload file trigger for prompt recording if defined as voicemail server and voicemail/prompt recording extensions auto-generated
# 90529-0652 - Added phone_context and fixed calledid and voicemail for phones entries
# 90614-0753 - Added in-group routing to call menu feature
# 90617-0821 - Added phone ring timeout and call menu custom dialplan entry
# 90621-0823 - Added phones conf file secret field use
# 90621-1425 - Added tracking group for call menus
# 90622-2146 - Added 83047777777777 for dynamic agent alert extension
# 90630-2259 - Added vicidial_process_triggers functionality
# 90713-0140 - Changed direct dial phone extensions to failover to voicemail forwarder
# 90722-1102 - Added list reset by time option
# 90812-0053 - Added clear out non-used vicidial_conferences sessions
# 90821-1246 - Fixed central voicemail server conf file, changed voicemail to use phone password
# 90903-1626 - Added musiconhold and meetme conf file generation
# 90919-1516 - Added generation of standalone voicemail boxes in voicemail conf file
# 91028-1023 - Added clearing of daily-reset tables at the timeclock reset time
# 91031-1258 - Added carrier description comments
# 91109-1205 - Added requirecalltoken=no as IAX setting for newer Asterisk 1.4 versions
# 91125-0709 - Added conf_secret to servers conf
# 91205-2315 - Added delete_vm_after_email option to voicemail conf generation
# 100220-1410 - Added System Settings and Servers custom dialplan entries
# 100225-2020 - Change voicemail configuration to use voicemail.conf
# 100312-1012 - Changed TIMEOUT Call Menu function to work with AGI routes
# 100424-2121 - Added codecs options for phones
# 100616-2245 - Added VIDPROMPT options for call menus, changed INGROUP TIMECHECK routes to special extension like AGI
# 100703-2137 - Added memory table reset nightly
# 100811-2221 - Added --cu3way flag for new optional leave3way cleaning script
# 100812-0515 - Added --cu3way-delay= flag for setting delay in the leave3way cleaning script
# 100814-2206 - Added clearing and optimization for vicidial_xfer_stats table
# 101022-1655 - Added new variables to be cleared from vicidial_cacmpaign_stats table
# 101107-2257 - Added cross-server phone dialplan extensions
# 101214-1507 - Changed list auto-reset to work with inactive lists
# 110512-2112 - Added vicidial_campaign_stats_debug to table cleaning
# 110525-2334 - Added cm.agi optional logging to call menus
# 110705-2023 - Added agents_calls_reset option
# 110725-2356 - Added new voicemail time zone option and menu gather
# 110829-1601 - Added multiple invalid option to Call Menus
# 110911-1452 - Added resets for extension groups and areacode cid tables
# 110922-2148 - Added reset for vicidial_did_ra_extensions
# 111004-2333 - Added Call Menu option for update of fields
#

$DB=0; # Debug flag
$MT[0]='';   $MT[1]='';
@psline=@MT;
$cu3way=0;

($sec,$min,$hour,$mday,$mon,$year,$wday,$yday,$isdst) = localtime(time);
$year = ($year + 1900);
$mon++;
$wtoday = $wday;
if ($mon < 10) {$mon = "0$mon";}
if ($mday < 10) {$mday = "0$mday";}
if ($hour < 10) {$hour = "0$hour";}
if ($min < 10) {$min = "0$min";}
if ($sec < 10) {$sec = "0$sec";}
$now_date = "$year-$mon-$mday $hour:$min:$sec";
$reset_test = "$hour$min";
$cu3way_delay='';

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
		print "  [-t] = test\n";
		print "  [-cu3way] = keepalive for the optional 3way conference checker\n";
		print "  [-cu3way-delay=X] = setting delay seconds on 3way conference checker\n";
		print "  [-debug] = verbose debug messages\n";
		print "  [-debugX] = Extra-verbose debug messages\n";
		print "  [-debugXXX] = Triple-Extra-verbose debug messages\n";
		print "\n";
		exit;
		}
	else
		{
		if ($args =~ /-debug/i)
			{
			$DB=1; # Debug flag
			}
		if ($args =~ /-debugX/i)
			{
			$DBX=1;
			print "\n----- SUPER-DUPER DEBUGGING -----\n\n";
			}
		if ($args =~ /-debugXXX/i)
			{
			$DBXXX=1;
			print "\n----- TRIPLE DEBUGGING -----\n\n";
			}
		if ($args =~ /-cu3way/i)
			{
			$cu3way=1;
			if ($DB > 0) {print "\n----- cu3way ENABLED -----\n\n";}
			}
		if ($args =~ /-cu3way-delay=/i) # CLI defined delay
			{
			@CLIvarARY = split(/-cu3way-delay=/,$args);
			@CLIvarARX = split(/ /,$CLIvarARY[1]);
			if (length($CLIvarARX[0])>0)
				{
				$CLIdelay = $CLIvarARX[0];
				$CLIdelay =~ s/\/$| |\r|\n|\t//gi;
				$CLIdelay =~ s/\D//gi;
				if ( ($CLIdelay > 0) && (length($CLIdelay)> 0) )	
					{$cu3way_delay = "--delay=$CLIdelay";}
				if ($DB > 0) {print "Delay set to $CLIdelay $cu3way_delay\n";}
				}
			@CLIvarARY=@MT;   @CLIvarARY=@MT;
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
	if ( ($line =~ /^VARactive_keepalives/) && ($CLIactive_keepalives < 1) )
		{$VARactive_keepalives = $line;   $VARactive_keepalives =~ s/.*=//gi;}
	$i++;
	}

##### list of codes for active_keepalives and what processes they correspond to
#	X - NO KEEPALIVE PROCESSES (use only if you want none to be keepalive)\n";
#	1 - AST_update\n";
#	2 - AST_send_listen\n";
#	3 - AST_VDauto_dial\n";
#	4 - AST_VDremote_agents\n";
#	5 - AST_VDadapt (If multi-server system, this must only be on one server)\n";
#	6 - FastAGI_log\n";
#	7 - AST_VDauto_dial_FILL\n";
#	8 - ip_relay for blind monitoring\n";
#	9 - Timeclock auto logout\n";
#     - Other setting are set by configuring them in the database

if ($VARactive_keepalives =~ /X/)
	{
	if ($DB) {print "X in active_keepalives, exiting...\n";}
	exit;
	}

$AST_update=0;
$AST_send_listen=0;
$AST_VDauto_dial=0;
$AST_VDremote_agents=0;
$AST_VDadapt=0;
$FastAGI_log=0;
$AST_VDauto_dial_FILL=0;
$ip_relay=0;
$timeclock_auto_logout=0;
$runningAST_update=0;
$runningAST_send=0;
$runningAST_listen=0;
$runningAST_VDauto_dial=0;
$runningAST_VDremote_agents=0;
$runningAST_VDadapt=0;
$runningFastAGI_log=0;
$runningAST_VDauto_dial_FILL=0;
$runningip_relay=0;
$runningAST_conf_3way=0;
$AST_conf_3way=0;

if ($VARactive_keepalives =~ /1/) 
	{
	$AST_update=1;
	if ($DB) {print "AST_update set to keepalive\n";}
	}
if ($VARactive_keepalives =~ /2/) 
	{
	$AST_send_listen=1;
	if ($DB) {print "AST_send_listen set to keepalive\n";}
	}
if ($VARactive_keepalives =~ /3/) 
	{
	$AST_VDauto_dial=1;
	if ($DB) {print "AST_VDauto_dial set to keepalive\n";}
	}
if ($VARactive_keepalives =~ /4/) 
	{
	$AST_VDremote_agents=1;
	if ($DB) {print "AST_VDremote_agents set to keepalive\n";}
	}
if ($VARactive_keepalives =~ /5/) 
	{
	$AST_VDadapt=1;
	if ($DB) {print "AST_VDadapt set to keepalive\n";}
	}
if ($VARactive_keepalives =~ /6/) 
	{
	$FastAGI_log=1;
	if ($DB) {print "FastAGI_log set to keepalive\n";}
	}
if ($VARactive_keepalives =~ /7/) 
	{
	$AST_VDauto_dial_FILL=1;
	if ($DB) {print "AST_VDauto_dial_FILL set to keepalive\n";}
	}
if ($VARactive_keepalives =~ /8/) 
	{
	$ip_relay=1;
	if ($DB) {print "ip_relay set to keepalive\n";}
	}
if ($VARactive_keepalives =~ /9/) 
	{
	$timeclock_auto_logout=1;
	if ($DB) {print "Check to see if Timeclock auto logout should run\n";}
	}
if ($cu3way > 0) 
	{
	$AST_conf_3way=1;
	if ($DB) {print "AST_conf_3way set to keepalive\n";}
	}


$REGhome = $PATHhome;
$REGhome =~ s/\//\\\//gi;






##### First, check and see which processes are running #####

### you may have to use a different ps command if you're not using Slackware Linux
#	@psoutput = `ps -f -C AST_update --no-headers`;
#	@psoutput = `ps -f -C AST_updat* --no-headers`;
#	@psoutput = `/bin/ps -f --no-headers -A`;
#	@psoutput = `/bin/ps -o pid,args -A`; ### use this one for FreeBSD
@psoutput = `/bin/ps -o "%p %a" --no-headers -A`;

$i=0;
foreach (@psoutput)
	{
	chomp($psoutput[$i]);
	if ($DBX) {print "$i|$psoutput[$i]|     \n";}
	@psline = split(/\/usr\/bin\/perl /,$psoutput[$i]);

	if ($psline[1] =~ /$REGhome\/AST_update\.pl/) 
		{
		$runningAST_update++;
		if ($DB) {print "AST_update RUNNING:              |$psline[1]|\n";}
		}
	if ($psline[1] =~ /AST_manager_se/) 
		{
		$runningAST_send++;
		if ($DB) {print "AST_send RUNNING:                |$psline[1]|\n";}
		}
	if ($psline[1] =~ /AST_manager_li/) 
		{
		$psoutput[$i] =~ s/ .*|\n|\r|\t| //gi;
		$listen_pid[$runningAST_listen] = $psoutput[$i];
		$runningAST_listen++;
		if ($DB) {print "AST_listen RUNNING:              |$psline[1]|\n";}
		}
	if ($psline[1] =~ /$REGhome\/AST_VDauto_dial\.pl/) 
		{
		$runningAST_VDauto_dial++;
		if ($DB) {print "AST_VDauto_dial RUNNING:         |$psline[1]|\n";}
		}
	if ($psline[1] =~ /$REGhome\/AST_VDremote_agents\.pl/) 
		{
		$runningAST_VDremote_agents++;
		if ($DB) {print "AST_VDremote_agents RUNNING:     |$psline[1]|\n";}
		}
	if ($psline[1] =~ /$REGhome\/AST_VDadapt\.pl/) 
		{
		$runningAST_VDadapt++;
		if ($DB) {print "AST_VDadapt RUNNING:             |$psline[1]|\n";}
		}
	if ($psline[1] =~ /$REGhome\/FastAGI_log\.pl/) 
		{
		$runningFastAGI_log++;
		if ($DB) {print "FastAGI_log RUNNING:             |$psline[1]|\n";}
		}
	if ($psline[1] =~ /$REGhome\/AST_VDauto_dial_FILL\.pl/) 
		{
		$runningAST_VDauto_dial_FILL++;
		if ($DB) {print "AST_VDauto_dial_FILL RUNNING:    |$psline[1]|\n";}
		}
	if ($psoutput[$i] =~ / ip_relay /) 
		{
		$runningip_relay++;
		if ($DB) {print "ip_relay RUNNING:                |$psoutput[$i]|\n";}
		}
	if ($psline[1] =~ /$REGhome\/AST_conf_update_3way\.pl/) 
		{
		$runningAST_conf_3way++;
		if ($DB) {print "AST_conf_3way RUNNING:           |$psline[1]|\n";}
		}

	$i++;
	}





##### Second, IF MORE THAN ONE LISTEN INSTANCE IS RUNNING, KILL THE SECOND ONE #####
@psline=@MT;
@psoutput=@MT;
@listen_pid=@MT;
if ($runningAST_listen > 1)
	{
	$runningAST_listen=0;

		sleep(1);

	### you may have to use a different ps command if you're not using Slackware Linux
	#	@psoutput = `ps -f -C AST_update --no-headers`;
	#	@psoutput = `ps -f -C AST_updat* --no-headers`;
	#	@psoutput = `/bin/ps -f --no-headers -A`;
	#	@psoutput = `/bin/ps -o pid,args -A`; ### use this one for FreeBSD
	@psoutput = `/bin/ps -o "%p %a" --no-headers -A`;

	$i=0;
	foreach (@psoutput)
		{
			chomp($psoutput[$i]);
		if ($DBX) {print "$i|$psoutput[$i]|     \n";}
		@psline = split(/\/usr\/bin\/perl /,$psoutput[$i]);
		$psoutput[$i] =~ s/^ *//gi;
		$psoutput[$i] =~ s/ .*|\n|\r|\t| //gi;

		if ($psline[1] =~ /AST_manager_li/) 
			{
			$listen_pid[$runningAST_listen] = $psoutput[$i];
			if ($DB) {print "AST_listen RUNNING:              |$psline[1]|$listen_pid[$runningAST_listen]|\n";}
			$runningAST_listen++;
			}

		$i++;
		}

	if ($runningAST_listen > 1)
		{
		if ($DB) {print "Killing AST_manager_listen... |$listen_pid[1]|\n";}
		`/bin/kill -s 9 $listen_pid[1]`;
		}
	}







##### Third, double-check that non-running scripts are not running #####
@psline=@MT;
@psoutput=@MT;

if ( 
	( ($AST_update > 0) && ($runningAST_update < 1) ) ||
	( ($AST_send_listen > 0) && ($runningAST_send < 1) ) ||
	( ($AST_send_listen > 0) && ($runningAST_listen < 1) ) ||
	( ($AST_VDauto_dial > 0) && ($runningAST_VDauto_dial < 1) ) ||
	( ($AST_VDremote_agents > 0) && ($runningAST_VDremote_agents < 1) ) ||
	( ($AST_VDadapt > 0) && ($runningAST_VDadapt < 1) ) ||
	( ($FastAGI_log > 0) && ($runningFastAGI_log < 1) ) ||
	( ($AST_VDauto_dial_FILL > 0) && ($runningAST_VDauto_dial_FILL < 1) ) ||
	( ($ip_relay > 0) && ($runningip_relay < 1) ) ||
	( ($AST_conf_3way > 0) && ($runningAST_conf_3way < 1) )
   )
	{

	if ($DB) {print "double check that processes are not running...\n";}

		sleep(1);

	#`PERL5LIB="$PATHhome/libs"; export PERL5LIB`; # issue #457
	$ENV{'PERL5LIB'} = "$PATHhome/libs";
	### you may have to use a different ps command if you're not using Slackware Linux
	#	@psoutput = `ps -f -C AST_update --no-headers`;
	#	@psoutput = `ps -f -C AST_updat* --no-headers`;
	#	@psoutput = `/bin/ps -f --no-headers -A`;
	#	@psoutput = `/bin/ps -o pid,args -A`; ### use this one for FreeBSD
	@psoutput2 = `/bin/ps -o "%p %a" --no-headers -A`;
	$i=0;
	foreach (@psoutput2)
		{
			chomp($psoutput2[$i]);
		if ($DBX) {print "$i|$psoutput2[$i]|     \n";}
		@psline = split(/\/usr\/bin\/perl /,$psoutput2[$i]);

		if ($psline[1] =~ /$REGhome\/AST_update\.pl/) 
			{
			$runningAST_update++;
			if ($DB) {print "AST_update RUNNING:              |$psline[1]|\n";}
			}
		if ($psline[1] =~ /AST_manager_se/) 
			{
			$runningAST_send++;
			if ($DB) {print "AST_send RUNNING:                |$psline[1]|\n";}
			}
		if ($psline[1] =~ /AST_manager_li/) 
			{
			$runningAST_listen++;
			if ($DB) {print "AST_listen RUNNING:              |$psline[1]|\n";}
			}
		if ($psline[1] =~ /$REGhome\/AST_VDauto_dial\.pl/) 
			{
			$runningAST_VDauto_dial++;
			if ($DB) {print "AST_VDauto_dial RUNNING:         |$psline[1]|\n";}
			}
		if ($psline[1] =~ /$REGhome\/AST_VDremote_agents\.pl/) 
			{
			$runningAST_VDremote_agents++;
			if ($DB) {print "AST_VDremote_agents RUNNING:     |$psline[1]|\n";}
			}
		if ($psline[1] =~ /$REGhome\/AST_VDadapt\.pl/) 
			{
			$runningAST_VDadapt++;
			if ($DB) {print "AST_VDadapt RUNNING:             |$psline[1]|\n";}
			}
		if ($psline[1] =~ /$REGhome\/FastAGI_log\.pl/) 
			{
			$runningFastAGI_log++;
			if ($DB) {print "FastAGI_log RUNNING:             |$psline[1]|\n";}
			}
		if ($psline[1] =~ /$REGhome\/AST_VDauto_dial_FILL\.pl/) 
			{
			$runningAST_VDauto_dial_FILL++;
			if ($DB) {print "AST_VDauto_dial_FILL RUNNING:    |$psline[1]|\n";}
			}
		if ($psoutput2[$i] =~ / ip_relay /) 
			{
			$runningip_relay++;
			if ($DB) {print "ip_relay RUNNING:                |$psoutput2[$i]|\n";}
			}
		if ($psline[1] =~ /$REGhome\/AST_conf_update_3way\.pl/) 
			{
			$runningAST_conf_3way++;
			if ($DB) {print "AST_conf_3way RUNNING:           |$psline[1]|\n";}
			}
		$i++;
		}


	if ( ($AST_update > 0) && ($runningAST_update < 1) )
		{ 
		if ($DB) {print "starting AST_update...\n";}
		# add a '-L' to the command below to activate logging
		`/usr/bin/screen -d -m -S ASTupdate $PATHhome/AST_update.pl`;
		}
	if ( ($AST_send_listen > 0) && ($runningAST_send < 1) )
		{ 
		if ($DB) {print "starting AST_manager_send...\n";}
		# add a '-L' to the command below to activate logging
		`/usr/bin/screen -d -m -S ASTsend $PATHhome/AST_manager_send.pl`;
		}
	if ( ($AST_send_listen > 0) && ($runningAST_listen < 1) )
		{ 
		if ($DB) {print "starting AST_manager_listen...\n";}
		# add a '-L' to the command below to activate logging
		`/usr/bin/screen -d -m -S ASTlisten $PATHhome/AST_manager_listen.pl`;
		}
	if ( ($AST_VDauto_dial > 0) && ($runningAST_VDauto_dial < 1) )
		{ 
		if ($DB) {print "starting AST_VDauto_dial...\n";}
		# add a '-L' to the command below to activate logging
		`/usr/bin/screen -d -m -S ASTVDauto $PATHhome/AST_VDauto_dial.pl`;
		}
	if ( ($AST_VDremote_agents > 0) && ($runningAST_VDremote_agents < 1) )
		{ 
		if ($DB) {print "starting AST_VDremote_agents...\n";}
		# add a '-L' to the command below to activate logging
		`/usr/bin/screen -d -m -S ASTVDremote $PATHhome/AST_VDremote_agents.pl --debug`;
		}
	if ( ($AST_VDadapt > 0) && ($runningAST_VDadapt < 1) )
		{ 
		if ($DB) {print "starting AST_VDadapt...\n";}
		# add a '-L' to the command below to activate logging
		`/usr/bin/screen -d -m -S ASTVDadapt $PATHhome/AST_VDadapt.pl --debug`;
		}
	if ( ($FastAGI_log > 0) && ($runningFastAGI_log < 1) )
		{ 
		if ($DB) {print "starting FastAGI_log...\n";}
		# add a '-L' to the command below to activate logging
		`/usr/bin/screen -d -m -S ASTfastlog $PATHhome/FastAGI_log.pl --debug`;
		}
	if ( ($AST_VDauto_dial_FILL > 0) && ($runningAST_VDauto_dial_FILL < 1) )
		{ 
		if ($DB) {print "starting AST_VDauto_dial_FILL...\n";}
		# add a '-L' to the command below to activate logging
		`/usr/bin/screen -d -m -S ASTVDautoFILL $PATHhome/AST_VDauto_dial_FILL.pl`;
		}
	if ( ($ip_relay > 0) && ($runningip_relay < 1) )
		{ 
		if ($DB) {print "starting ip_relay through relay_control...\n";}
		`$PATHhome/ip_relay/relay_control start  2>/dev/null 1>&2`;
		}
	if ( ($AST_conf_3way > 0) && ($runningAST_conf_3way < 1) )
		{ 
		if ($DB) {print "starting AST_conf_3way...\n";}
		# add a '-L' to the command below to activate logging
		`/usr/bin/screen -d -m -S ASTconf3way $PATHhome/AST_conf_update_3way.pl --debug $cu3way_delay`;
		}

	}



### run the Timeclock auto-logout process ###
if ($timeclock_auto_logout > 0)
	{
	if ($DB) {print "running Timeclock auto-logout process...\n";}
	`/usr/bin/screen -d -m -S Timeclock $PATHhome/ADMIN_timeclock_auto_logout.pl 2>/dev/null 1>&2`;
	}
################################################################################
#####  END keepalive of ViciDial-related scripts
################################################################################





################################################################################
#####  START clear out non-used vicidial_conferences sessions and reset daily
#####        tally tables
################################################################################

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
	if ( ($line =~ /^PATHlogs/) && ($CLIlogs < 1) )
		{$PATHlogs = $line;   $PATHlogs =~ s/.*=//gi;}
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
	if ( ($line =~ /^VARDB_custom_user/) && ($CLIDB_custom_user < 1) )
		{$VARDB_custom_user = $line;   $VARDB_custom_user =~ s/.*=//gi;}
	if ( ($line =~ /^VARDB_custom_pass/) && ($CLIDB_custom_pass < 1) )
		{$VARDB_custom_pass = $line;   $VARDB_custom_pass =~ s/.*=//gi;}
	if ( ($line =~ /^VARDB_port/) && ($CLIDB_port < 1) )
		{$VARDB_port = $line;   $VARDB_port =~ s/.*=//gi;}
	$i++;
	}

# Customized Variables
$server_ip = $VARserver_ip;		# Asterisk server IP
$THISserver_voicemail=0;
$voicemail_server_id='';
if (!$VARDB_port) {$VARDB_port='3306';}

use DBI;	  

$dbhA = DBI->connect("DBI:mysql:$VARDB_database:$VARDB_server:$VARDB_port", "$VARDB_user", "$VARDB_pass")
 or die "Couldn't connect to database: " . DBI->errstr;

$timeclock_end_of_day_NOW=0;
### Grab system_settings values from the database
	$stmtA = "SELECT count(*) from system_settings where timeclock_end_of_day LIKE \"%$reset_test%\";";
	if ($DB) {print "|$stmtA|\n";}
	$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
	$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
	$sthArows=$sthA->rows;
	if ($sthArows > 0)
		{
		@aryA = $sthA->fetchrow_array;
		$timeclock_end_of_day_NOW =	"$aryA[0]";
		}
	$sthA->finish();

if ($timeclock_end_of_day_NOW > 0)
	{
	$stmtA = "SELECT agents_calls_reset from system_settings;";
	if ($DB) {print "|$stmtA|\n";}
	$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
	$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
	$SSsel_ct=$sthA->rows;
	if ($SSsel_ct > 0)
		{
		@aryA = $sthA->fetchrow_array;
		$agents_calls_reset =	 $aryA[0];
		}
	$sthA->finish();

	if ($DB) {print "Starting clear out non-used vicidial_conferences sessions process...\n";}

	$stmtA = "SELECT conf_exten,extension from vicidial_conferences where server_ip='$server_ip' and leave_3way='0';";
	if ($DB) {print "|$stmtA|\n";}
	$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
	$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
	$VCexten_ct=$sthA->rows;
	$rec_count=0;
	while ($VCexten_ct > $rec_count)
		{
		@aryA = $sthA->fetchrow_array;
		$PT_conf_extens[$rec_count] =	 $aryA[0];
		$PT_extensions[$rec_count] =	 $aryA[1];
			if ($DBX) {print "|$PT_conf_extens[$rec_count]|$PT_extensions[$rec_count]|\n";}
		$rec_count++;
		}
	$sthA->finish();
	$k=0;
	while ($k < $rec_count)
		{
		$live_session=0;
		$stmtA = "SELECT count(*) from vicidial_live_agents where conf_exten='$PT_conf_extens[$k]' and server_ip='$server_ip';";
		$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
		$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
		$sthArows=$sthA->rows;
		if ($sthArows > 0)
			{
			@aryA = $sthA->fetchrow_array;
			$live_session =	"$aryA[0]";
			}
		$sthA->finish();

		if ($live_session < 1)
			{
			$stmtA = "UPDATE vicidial_conferences set extension='' where server_ip='$server_ip' and conf_exten='$PT_conf_extens[$k]';";
				if($DBX){print STDERR "\n|$stmtA|\n";}
			$affected_rows = $dbhA->do($stmtA); #  or die  "Couldn't execute query:|$stmtA|\n";
			}
		$k++;
		}


	if ($DB) {print "Starting clear out daily reset tables...\n";}

	$secX = time();
	$TDtarget = ($secX - 86000);	# almost one day old
	($Tsec,$Tmin,$Thour,$Tmday,$Tmon,$Tyear,$Twday,$Tyday,$Tisdst) = localtime($TDtarget);
	$Tyear = ($Tyear + 1900);
	$Tmon++;
	if ($Tmon < 10) {$Tmon = "0$Tmon";}
	if ($Tmday < 10) {$Tmday = "0$Tmday";}
	if ($Thour < 10) {$Thour = "0$Thour";}
	if ($Tmin < 10) {$Tmin = "0$Tmin";}
	if ($Tsec < 10) {$Tsec = "0$Tsec";}
	$TDSQLdate = "$Tyear-$Tmon-$Tmday $Thour:$Tmin:$Tsec";

	$stmtA = "UPDATE vicidial_xfer_stats SET xfer_count='0';";
	if($DBX){print STDERR "\n|$stmtA|\n";}
	$affected_rows = $dbhA->do($stmtA);
	if($DB){print STDERR "\n|$affected_rows vicidial_xfer_stats records reset|\n";}

	$stmtA = "optimize table vicidial_xfer_stats;";
	if($DBX){print STDERR "\n|$stmtA|\n";}
	$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
	$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
	$sthArows=$sthA->rows;
	@aryA = $sthA->fetchrow_array;
	if ($DB) {print "|",$aryA[0],"|",$aryA[1],"|",$aryA[2],"|",$aryA[3],"|","\n";}
	$sthA->finish();

	$stmtA = "UPDATE vicidial_campaign_stats SET dialable_leads='0', calls_today='0', answers_today='0', drops_today='0', drops_today_pct='0', drops_answers_today_pct='0', calls_hour='0', answers_hour='0', drops_hour='0', drops_hour_pct='0', calls_halfhour='0', answers_halfhour='0', drops_halfhour='0', drops_halfhour_pct='0', calls_fivemin='0', answers_fivemin='0', drops_fivemin='0', drops_fivemin_pct='0', calls_onemin='0', answers_onemin='0', drops_onemin='0', drops_onemin_pct='0', differential_onemin='0', agents_average_onemin='0', balance_trunk_fill='0', status_category_count_1='0', status_category_count_2='0', status_category_count_3='0', status_category_count_4='0',agent_calls_today='0',agent_pause_today='0',agent_wait_today='0',agent_custtalk_today='0',agent_acw_today='0';";
	if($DBX){print STDERR "\n|$stmtA|\n";}
	$affected_rows = $dbhA->do($stmtA);
	if($DB){print STDERR "\n|$affected_rows vicidial_campaign_stats records reset|\n";}

	$stmtA = "optimize table vicidial_campaign_stats;";
	if($DBX){print STDERR "\n|$stmtA|\n";}
	$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
	$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
	$sthArows=$sthA->rows;
	@aryA = $sthA->fetchrow_array;
	if ($DB) {print "|",$aryA[0],"|",$aryA[1],"|",$aryA[2],"|",$aryA[3],"|","\n";}
	$sthA->finish();

	$stmtA = "UPDATE vicidial_drop_rate_groups SET calls_today='0', answers_today='0', drops_today='0', drops_today_pct='0', drops_answers_today_pct='0';";
	if($DBX){print STDERR "\n|$stmtA|\n";}
	$affected_rows = $dbhA->do($stmtA);
	if($DB){print STDERR "\n|$affected_rows vicidial_drop_rate_groups records reset|\n";}

	$stmtA = "optimize table vicidial_drop_rate_groups;";
	if($DBX){print STDERR "\n|$stmtA|\n";}
	$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
	$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
	$sthArows=$sthA->rows;
	@aryA = $sthA->fetchrow_array;
	if ($DB) {print "|",$aryA[0],"|",$aryA[1],"|",$aryA[2],"|",$aryA[3],"|","\n";}
	$sthA->finish();

	$stmtA = "delete from vicidial_campaign_server_stats;";
	if($DBX){print STDERR "\n|$stmtA|\n";}
	$affected_rows = $dbhA->do($stmtA);
	if($DB){print STDERR "\n|$affected_rows vicidial_campaign_server_stats records deleted|\n";}

	$stmtA = "optimize table vicidial_campaign_server_stats;";
	if($DBX){print STDERR "\n|$stmtA|\n";}
	$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
	$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
	$sthArows=$sthA->rows;
	@aryA = $sthA->fetchrow_array;
	if ($DB) {print "|",$aryA[0],"|",$aryA[1],"|",$aryA[2],"|",$aryA[3],"|","\n";}
	$sthA->finish();

	$stmtA = "delete from vicidial_campaign_stats_debug;";
	if($DBX){print STDERR "\n|$stmtA|\n";}
	$affected_rows = $dbhA->do($stmtA);
	if($DB){print STDERR "\n|$affected_rows vicidial_campaign_stats_debug records deleted|\n";}

	$stmtA = "optimize table vicidial_campaign_stats_debug;";
	if($DBX){print STDERR "\n|$stmtA|\n";}
	$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
	$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
	$sthArows=$sthA->rows;
	@aryA = $sthA->fetchrow_array;
	if ($DB) {print "|",$aryA[0],"|",$aryA[1],"|",$aryA[2],"|",$aryA[3],"|","\n";}
	$sthA->finish();

	$stmtA = "update vicidial_inbound_group_agents SET calls_today=0;";
	if($DBX){print STDERR "\n|$stmtA|\n";}
	$affected_rows = $dbhA->do($stmtA);
	if($DB){print STDERR "\n|$affected_rows vicidial_inbound_group_agents call counts reset|\n";}

	$stmtA = "optimize table vicidial_inbound_group_agents;";
	if($DBX){print STDERR "\n|$stmtA|\n";}
	$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
	$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
	$sthArows=$sthA->rows;
	@aryA = $sthA->fetchrow_array;
	if ($DB) {print "|",$aryA[0],"|",$aryA[1],"|",$aryA[2],"|",$aryA[3],"|","\n";}
	$sthA->finish();

	$stmtA = "update vicidial_campaign_cid_areacodes SET call_count_today=0;";
	if($DBX){print STDERR "\n|$stmtA|\n";}
	$affected_rows = $dbhA->do($stmtA);
	if($DB){print STDERR "\n|$affected_rows vicidial_campaign_cid_areacodes call counts reset|\n";}

	$stmtA = "optimize table vicidial_campaign_cid_areacodes;";
	if($DBX){print STDERR "\n|$stmtA|\n";}
	$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
	$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
	$sthArows=$sthA->rows;
	@aryA = $sthA->fetchrow_array;
	if ($DB) {print "|",$aryA[0],"|",$aryA[1],"|",$aryA[2],"|",$aryA[3],"|","\n";}
	$sthA->finish();

	$stmtA = "update vicidial_did_ra_extensions SET call_count_today=0;";
	if($DBX){print STDERR "\n|$stmtA|\n";}
	$affected_rows = $dbhA->do($stmtA);
	if($DB){print STDERR "\n|$affected_rows vicidial_did_ra_extensions call counts reset|\n";}

	$stmtA = "optimize table vicidial_did_ra_extensions;";
	if($DBX){print STDERR "\n|$stmtA|\n";}
	$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
	$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
	$sthArows=$sthA->rows;
	@aryA = $sthA->fetchrow_array;
	if ($DB) {print "|",$aryA[0],"|",$aryA[1],"|",$aryA[2],"|",$aryA[3],"|","\n";}
	$sthA->finish();

	$stmtA = "update vicidial_extension_groups SET call_count_today=0;";
	if($DBX){print STDERR "\n|$stmtA|\n";}
	$affected_rows = $dbhA->do($stmtA);
	if($DB){print STDERR "\n|$affected_rows vicidial_extension_groups call counts reset|\n";}

	$stmtA = "optimize table vicidial_extension_groups;";
	if($DBX){print STDERR "\n|$stmtA|\n";}
	$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
	$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
	$sthArows=$sthA->rows;
	@aryA = $sthA->fetchrow_array;
	if ($DB) {print "|",$aryA[0],"|",$aryA[1],"|",$aryA[2],"|",$aryA[3],"|","\n";}
	$sthA->finish();


	$stmtA = "update vicidial_campaign_agents SET calls_today=0;";
	if($DBX){print STDERR "\n|$stmtA|\n";}
	$affected_rows = $dbhA->do($stmtA);
	if($DB){print STDERR "\n|$affected_rows vicidial_campaign_agents call counts reset|\n";}

	$stmtA = "optimize table vicidial_campaign_agents;";
	if($DBX){print STDERR "\n|$stmtA|\n";}
	$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
	$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
	$sthArows=$sthA->rows;
	@aryA = $sthA->fetchrow_array;
	if ($DB) {print "|",$aryA[0],"|",$aryA[1],"|",$aryA[2],"|",$aryA[3],"|","\n";}
	$sthA->finish();

	$stmtA = "update vicidial_live_inbound_agents SET calls_today=0;";
	if($DBX){print STDERR "\n|$stmtA|\n";}
	$affected_rows = $dbhA->do($stmtA);
	if($DB){print STDERR "\n|$affected_rows vicidial_live_inbound_agents call counts reset|\n";}

	if ($agents_calls_reset > 0)
		{
		$stmtA = "delete from vicidial_live_inbound_agents where last_call_finish < \"$TDSQLdate\";";
		if($DBX){print STDERR "\n|$stmtA|\n";}
		$affected_rows = $dbhA->do($stmtA);
		if($DB){print STDERR "\n|$affected_rows vicidial_live_inbound_agents old records deleted|\n";}

		$stmtA = "delete from vicidial_live_agents where last_state_change < \"$TDSQLdate\" and extension NOT LIKE \"R/%\";";
		if($DBX){print STDERR "\n|$stmtA|\n";}
		$affected_rows = $dbhA->do($stmtA);
		if($DB){print STDERR "\n|$affected_rows vicidial_live_agents old records deleted|\n";}

		$stmtA = "delete from vicidial_auto_calls where last_update_time < \"$TDSQLdate\";";
		if($DBX){print STDERR "\n|$stmtA|\n";}
		$affected_rows = $dbhA->do($stmtA);
		if($DB){print STDERR "\n|$affected_rows vicidial_auto_calls old records deleted|\n";}
		}

	$stmtA = "optimize table vicidial_live_inbound_agents;";
	if($DBX){print STDERR "\n|$stmtA|\n";}
	$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
	$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
	$sthArows=$sthA->rows;
	@aryA = $sthA->fetchrow_array;
	if ($DB) {print "|",$aryA[0],"|",$aryA[1],"|",$aryA[2],"|",$aryA[3],"|","\n";}
	$sthA->finish();

	$stmtA = "optimize table vicidial_live_agents;";
	if($DBX){print STDERR "\n|$stmtA|\n";}
	$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
	$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
	$sthArows=$sthA->rows;
	@aryA = $sthA->fetchrow_array;
	if ($DB) {print "|",$aryA[0],"|",$aryA[1],"|",$aryA[2],"|",$aryA[3],"|","\n";}
	$sthA->finish();

	$stmtA = "optimize table vicidial_auto_calls;";
	if($DBX){print STDERR "\n|$stmtA|\n";}
	$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
	$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
	$sthArows=$sthA->rows;
	@aryA = $sthA->fetchrow_array;
	if ($DB) {print "|",$aryA[0],"|",$aryA[1],"|",$aryA[2],"|",$aryA[3],"|","\n";}
	$sthA->finish();


	$dbhC = DBI->connect("DBI:mysql:$VARDB_database:$VARDB_server:$VARDB_port", "$VARDB_custom_user", "$VARDB_custom_pass")
	 or die "Couldn't connect to database: " . DBI->errstr;

	##### find MEMORY tables for reset of empty space #####
	$stmtA = "SHOW TABLE STATUS WHERE Engine='MEMORY';";
	$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
	$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
	$sthArows=$sthA->rows;
	$i=0;
	while ($sthArows > $i)
		{
		@aryA = $sthA->fetchrow_array;
		$table_name	= 	$aryA[0];

		$stmtC = "ALTER TABLE $table_name ENGINE=MEMORY;";
		if($DBX){print STDERR "\n|$stmtC|\n";}
		$Caffected_rows = $dbhC->do($stmtC);
		if($DB){print STDERR "\n|$table_name memory reset $Caffected_rows rows|\n";}
		$i++;
		}
	$sthA->finish();
	}

################################################################################
#####  END   clear out non-used vicidial_conferences sessions and reset daily
#####        tally tables
################################################################################





################################################################################
#####  START Creation of auto-generated conf files
################################################################################

##### Get the settings from system_settings #####
$stmtA = "SELECT sounds_central_control_active,active_voicemail_server,custom_dialplan_entry,default_codecs,generate_cross_server_exten,voicemail_timezones,default_voicemail_timezone FROM system_settings;";
#	print "$stmtA\n";
$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
$sthArows=$sthA->rows;
if ($sthArows > 0)
	{
	@aryA = $sthA->fetchrow_array;
	$sounds_central_control_active =	$aryA[0];
	$active_voicemail_server =			$aryA[1];
	$SScustom_dialplan_entry =			$aryA[2];
	$SSdefault_codecs =					$aryA[3];
	$SSgenerate_cross_server_exten =	$aryA[4];
	$SSvoicemail_timezones =			$aryA[5];
	$SSdefault_voicemail_timezone =		$aryA[6];	
	}
$sthA->finish();
if ($DBXXX > 0) {print "SYSTEM SETTINGS:     $sounds_central_control_active|$active_voicemail_server|$SScustom_dialplan_entry|$SSdefault_codecs\n";}
if ( ($active_voicemail_server =~ /$server_ip/) && ((length($active_voicemail_server)) eq (length($server_ip))) )
	{
	$THISserver_voicemail=1;

	if ( !-e ('/etc/asterisk/voicemail.conf'))
		{`echo -e \"; END OF FILE\n\" > /etc/asterisk/voicemail.conf`;}
	if ( !-e ('/etc/asterisk/BUILDvoicemail-vicidial.conf'))
		{`echo -e \"; END OF FILE\n\" > /etc/asterisk/BUILDvoicemail-vicidial.conf`;}
	$vmCMP =  compare("/etc/asterisk/BUILDvoicemail-vicidial.conf","/etc/asterisk/voicemail.conf");
	if ($vmCMP > 0)
		{
		if ($DB) {print "starting voicemail configuration check\n";}
		################################################################################
		#####  START Parsing of the voicemail.conf file
		################################################################################
		# default path to voicemail configuration file:
		$VMCconf = '/etc/asterisk/voicemail.conf';

		open(vmc, "$VMCconf") || die "can't open $VMCconf: $!\n";
		@vmc = <vmc>;
		close(vmc);
		$i=0;
		$vm_header_content='';
		$vm_zones_content='';
		$boxes=9999999;
		$zones=9999;
		$zonesend=9999;
		$otherboxes=9999999;
		foreach(@vmc)
			{
			$line = $vmc[$i];
			$line =~ s/\n|\r//gi;
			if ( ($zones > 0) && ($i > $zones) && ($line =~ /\[/) )
				{
				$zonesend = $i;
				if ($DBXXX > 0) {print "voicemail zones end:     $zonesend\n";}
				}
			if ($line =~ /\[zonemessages\]/)
				{
				$zones = $i;
				if ($DBXXX > 0) {print "voicemail zones begin:   $zones\n";}
				}
			if ($line =~ /\[default\]/)
				{$boxes = $i;}
			if ($line =~ /^; Other Voicemail Entries/)
				{$otherboxes = $i;}
			### BEGIN parse through voicemail zonemessages
			if ( ($i > $zones) && ($i < $zonesend) )
				{
				if ( ($line !~ /^;/) && (length($line) > 5) )
					{
					$templine = $line;
					$templine =~ s/\|.*//gi;
					$vm_zones_content .= "$templine\n";
					if ($DBXXX > 0) {print "voicemail zones content:   $line\n";}
					}
				}
			### END parse through voicemail zonemessages

			### BEGIN parse through voicemail boxes and update DB with any changed settings
			if ($i > $boxes)
				{
				if ( ($line !~ /^;/) && (length($line) > 5) )
					{
					# 102 => 102,102a Mailbox,test@vicidial.com,,|delete=yes
					chomp($line);
					@parse_line = split(/ => /,$line);
					$mailbox = $parse_line[0];
					$mboptions = $parse_line[1];
					@options_line = split(/,/,$parse_line[1]);
					$vmc_pass = $options_line[0];
					$vmc_email = $options_line[2];
					$vmc_delete_vm_after_email='N';
					$vmc_voicemail_timezone="$SSdefault_voicemail_timezone";
					$vmc_voicemail_options='';
					if ($mboptions =~ /delete=yes/)
						{$vmc_delete_vm_after_email='Y';}
					if ($mboptions =~ /tz=/)
						{
						@options_sgmt = split(/tz=/,$mboptions);
						@tz_sgmt = split(/\|/,$options_sgmt[1]);
						$vmc_voicemail_timezone = $tz_sgmt[0];
						@options_sgmt = split(/tz=$vmc_voicemail_timezone\|/,$mboptions);
						$vmc_voicemail_options = $options_sgmt[1];
						}
					$sthArows=0;

					if ($i < $otherboxes)
						{
						$stmtA = "SELECT voicemail_id,pass,email,delete_vm_after_email,voicemail_timezone,voicemail_options FROM phones where voicemail_id='$mailbox' and active='Y' order by extension limit 1;";
						#	print "$stmtA\n";
						$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
						$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
						$sthArows=$sthA->rows;
						if ($sthArows > 0)
							{
							@aryA = $sthA->fetchrow_array;
							$mb_voicemail =				$aryA[0];
							$mb_pass =					$aryA[1];
							$mb_email =					$aryA[2];
							$mb_delete_vm_after_email =	$aryA[3];
							$mb_voicemail_timezone =	$aryA[4];
							$mb_voicemail_options =		$aryA[5];

							if ( ( ($mb_pass !~ /$vmc_pass/) || (length($mb_pass) != length($vmc_pass)) ) || ( ($mb_email !~ /$vmc_email/) || (length($mb_email) != length($vmc_email)) ) || ( ($mb_delete_vm_after_email !~ /$vmc_delete_vm_after_email/) || (length($mb_delete_vm_after_email) != length($vmc_delete_vm_after_email)) ) || ( ($mb_voicemail_timezone !~ /$vmc_voicemail_timezone/) || (length($mb_voicemail_timezone) != length($vmc_voicemail_timezone)) ) || ( ($mb_voicemail_options !~ /$vmc_voicemail_options/) || (length($mb_voicemail_options) != length($vmc_voicemail_options)) ) )
								{
								$stmtA="UPDATE phones SET pass='$vmc_pass',email='$vmc_email',delete_vm_after_email='$vmc_delete_vm_after_email',voicemail_timezone='$vmc_voicemail_timezone',voicemail_options='$vmc_voicemail_options' where voicemail_id='$mailbox' and active='Y' order by extension limit 1;";
								$affected_rows = $dbhA->do($stmtA);

								$stmtA="UPDATE servers SET rebuild_conf_files='Y' where server_ip='$server_ip';";
								$affected_rows = $dbhA->do($stmtA);
								}
							}
						$sthA->finish();
						}
					else
						{
						$stmtA = "SELECT voicemail_id,pass,email,delete_vm_after_email,voicemail_timezone,voicemail_options FROM vicidial_voicemail WHERE voicemail_id='$mailbox' and active='Y' limit 1;";
						#	print "$stmtA\n";
						$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
						$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
						$sthArows=$sthA->rows;
						if ($sthArows > 0)
							{
							@aryA = $sthA->fetchrow_array;
							$mb_voicemail =				$aryA[0];
							$mb_pass =					$aryA[1];
							$mb_email =					$aryA[2];
							$mb_delete_vm_after_email =	$aryA[3];
							$mb_voicemail_timezone =	$aryA[4];
							$mb_voicemail_options =		$aryA[5];

							if ( ( ($mb_pass !~ /$vmc_pass/) || (length($mb_pass) != length($vmc_pass)) ) || ( ($mb_email !~ /$vmc_email/) || (length($mb_email) != length($vmc_email)) ) || ( ($mb_delete_vm_after_email !~ /$vmc_delete_vm_after_email/) || (length($mb_delete_vm_after_email) != length($vmc_delete_vm_after_email)) ) || ( ($mb_voicemail_timezone !~ /$vmc_voicemail_timezone/) || (length($mb_voicemail_timezone) != length($vmc_voicemail_timezone)) ) || ( ($mb_voicemail_options !~ /$vmc_voicemail_options/) || (length($mb_voicemail_options) != length($vmc_voicemail_options)) ) )
								{
								$stmtA="UPDATE vicidial_voicemail SET pass='$vmc_pass',email='$vmc_email',delete_vm_after_email='$vmc_delete_vm_after_email',voicemail_timezone='$vmc_voicemail_timezone',voicemail_options='$vmc_voicemail_options' where voicemail_id='$mailbox' and active='Y' limit 1;";
								$affected_rows = $dbhA->do($stmtA);

								$stmtA="UPDATE servers SET rebuild_conf_files='Y' where server_ip='$server_ip';";
								$affected_rows = $dbhA->do($stmtA);
								}
							}
						$sthA->finish();
						}
					if ($sthArows < 1) 
						{
						if ($DB) {print "Mailbox not found: $mailbox     it will be removed from voicemail.conf\n";}
						}
					}
				### END parse through voicemail boxes and update DB with any changed settings
				}
			else
				{
				$vm_header_content .= "$line\n";
				}
			$i++;
			}
		if (length($SSvoicemail_timezones) != length($vm_zones_content)) 
			{
			$stmtA="UPDATE system_settings SET voicemail_timezones='$vm_zones_content';";
			$affected_rows = $dbhA->do($stmtA);
			if ($DB) {print "voicemail zones updated\n";}
			}
		################################################################################
		#####  END Parsing of the voicemail.conf file
		################################################################################
		}
	}
else
	{
	$stmtA = "SELECT server_id FROM servers,system_settings where servers.server_ip=system_settings.active_voicemail_server;";
	#	print "$stmtA\n";
	$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
	$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
	$sthArows=$sthA->rows;
	if ($sthArows > 0)
		{
		@aryA = $sthA->fetchrow_array;
		$voicemail_server_id	=	$aryA[0];
		}
	$sthA->finish();
	}

##### Get the settings for this server's server_ip #####
$stmtA = "SELECT active_asterisk_server,generate_vicidial_conf,rebuild_conf_files,asterisk_version,sounds_update,conf_secret,custom_dialplan_entry FROM servers where server_ip='$server_ip';";
#	print "$stmtA\n";
$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
$sthArows=$sthA->rows;
if ($sthArows > 0)
	{
	@aryA = $sthA->fetchrow_array;
	$active_asterisk_server	=		$aryA[0];
	$generate_vicidial_conf	=		$aryA[1];
	$rebuild_conf_files	=			$aryA[2];
	$asterisk_version =				$aryA[3];
	$sounds_update =				$aryA[4];
	$self_conf_secret =				$aryA[5];
	$SERVERcustom_dialplan_entry =	$aryA[6];
	}
$sthA->finish();


if ( ($active_asterisk_server =~ /Y/) && ($generate_vicidial_conf =~ /Y/) && ($rebuild_conf_files =~ /Y/) ) 
	{
	if ($DB) {print "generating new auto-gen conf files\n";}

	$stmtA="UPDATE servers SET rebuild_conf_files='N' where server_ip='$server_ip';";
	$affected_rows = $dbhA->do($stmtA);

	### format the new server_ip dialstring for example to use with extensions.conf
	$S='*';
	if( $VARserver_ip =~ m/(\S+)\.(\S+)\.(\S+)\.(\S+)/ )
		{
		$a = leading_zero($1); 
		$b = leading_zero($2); 
		$c = leading_zero($3); 
		$d = leading_zero($4);
		$VARremDIALstr = "$a$S$b$S$c$S$d";
		}

	$ext  .= "TRUNKloop = IAX2/ASTloop:$self_conf_secret\@127.0.0.1:40569\n";
	$ext  .= "TRUNKblind = IAX2/ASTblind:$self_conf_secret\@127.0.0.1:41569\n";

	$iax  .= "register => ASTloop:$self_conf_secret\@127.0.0.1:40569\n";
	$iax  .= "register => ASTblind:$self_conf_secret\@127.0.0.1:41569\n";

	$Lext  = "\n";
	$Lext .= "; Local Server: $server_ip\n";
	$Lext .= "exten => _$VARremDIALstr*.,1,Goto(default,\${EXTEN:16},1)\n";

	$Liax .= "\n";
	$Liax .= "[ASTloop]\n";
	$Liax .= "accountcode=ASTloop\n";
	$Liax .= "secret=$self_conf_secret\n";
	$Liax .= "type=friend\n";
	$Liax .= "requirecalltoken=no\n";
	$Liax .= "context=default\n";
	$Liax .= "auth=plaintext\n";
	$Liax .= "host=dynamic\n";
	$Liax .= "permit=0.0.0.0/0.0.0.0\n";
	$Liax .= "disallow=all\n";
	$Liax .= "allow=ulaw\n";
	$Liax .= "qualify=yes\n";

	$Liax .= "\n";
	$Liax .= "[ASTblind]\n";
	$Liax .= "accountcode=ASTblind\n";
	$Liax .= "secret=$self_conf_secret\n";
	$Liax .= "type=friend\n";
	$Liax .= "requirecalltoken=no\n";
	$Liax .= "context=default\n";
	$Liax .= "auth=plaintext\n";
	$Liax .= "host=dynamic\n";
	$Liax .= "permit=0.0.0.0/0.0.0.0\n";
	$Liax .= "disallow=all\n";
	$Liax .= "allow=ulaw\n";
	$Liax .= "qualify=yes\n";


	##### Get the server_id for this server's server_ip #####
	$stmtA = "SELECT server_id,vicidial_recording_limit FROM servers where server_ip='$server_ip';";
	#	print "$stmtA\n";
	$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
	$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
	$sthArows=$sthA->rows;
	if ($sthArows > 0)
		{
		@aryA = $sthA->fetchrow_array;
		$server_id	=					$aryA[0];
		$vicidial_recording_limit =		(60 * $aryA[1]);
		$i++;
		}
	$sthA->finish();


	##### BEGIN Generate the server_ips and server_ids of all VICIDIAL servers on the network for load balancing #####
	$stmtA = "SELECT server_ip,server_id,conf_secret FROM servers where server_ip!='$server_ip' and active_asterisk_server='Y' order by server_ip;";
	#	print "$stmtA\n";
	$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
	$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
	$sthArows=$sthA->rows;
	$active_server_ips='';
	$active_dialplan_numbers='';
	$i=0;
	while ($sthArows > $i)
		{
		@aryA = $sthA->fetchrow_array;
		$server_ip[$i] =	$aryA[0];
		$server_id[$i] =	$aryA[1];
		$conf_secret[$i] =	$aryA[2];
		if ($i > 0)
			{$active_server_ips .= ",";}
		$active_server_ips .= "'$aryA[0]'";

		if( $server_ip[$i] =~ m/(\S+)\.(\S+)\.(\S+)\.(\S+)/ )
			{
			$a = leading_zero($1);
			$b = leading_zero($2);
			$c = leading_zero($3);
			$d = leading_zero($4);
			$VARremDIALstr = "$a$S$b$S$c$S$d";
			}
		$ext  .= "TRUNK$server_id[$i] = IAX2/$server_id:$conf_secret[$i]\@$server_ip[$i]:4569\n";

		$iax  .= "register => $server_id:$conf_secret[$i]\@$server_ip[$i]:4569\n";

		$Lext .= "; Remote Server VDAD extens: $server_id[$i] $server_ip[$i]\n";
		$Lext .= "exten => _$VARremDIALstr*.,1,Dial(\${TRUNK$server_id[$i]}/\${EXTEN:16},55,oT)\n";

		$Liax .= "\n";
		$Liax .= "[$server_id[$i]]\n";
		$Liax .= "accountcode=IAX$server_id[$i]\n";
		$Liax .= "secret=$self_conf_secret\n";
		$Liax .= "type=friend\n";
		$Liax .= "requirecalltoken=no\n";
		$Liax .= "context=default\n";
		$Liax .= "auth=plaintext\n";
		$Liax .= "host=dynamic\n";
		$Liax .= "permit=0.0.0.0/0.0.0.0\n";
		$Liax .= "disallow=all\n";
		$Liax .= "allow=ulaw\n";
		$Liax .= "qualify=yes\n";

		$i++;
		}
	$sthA->finish();
	##### END Generate the server_ips and server_ids of all VICIDIAL servers on the network for load balancing #####



	##### BEGIN Create Voicemail extensions for this server_ip #####
	if ( ($THISserver_voicemail > 0) || (length($voicemail_server_id) < 1) )
		{
		$Vext .= "; Voicemail Extensions:\n";
		$Vext .= "exten => _85026666666666.,1,Wait(1)\n";
		$Vext .= "exten => _85026666666666.,2,Voicemail(\${EXTEN:14}|u)\n";
		$Vext .= "exten => _85026666666666.,3,Hangup\n";
		$Vext .= "exten => 8500,1,VoicemailMain\n";
		$Vext .= "exten => 8500,2,Goto(s,6)\n";
		if ($asterisk_version =~ /^1.2/)
			{$Vext .= "exten => 8501,1,VoicemailMain(s\${CALLERIDNUM})\n";}
		else
			{$Vext .= "exten => 8501,1,VoicemailMain(s\${CALLERID(num)})\n";}
		$Vext .= "exten => 8501,2,Hangup\n";
		$Vext .= "\n";
		$Vext .= "; Prompt Extensions:\n";
		$Vext .= "exten => 8167,1,Answer\n";
		$Vext .= "exten => 8167,2,AGI(agi-record_prompts.agi,wav-----720000)\n";
		$Vext .= "exten => 8167,3,Hangup\n";
		$Vext .= "exten => 8168,1,Answer\n";
		$Vext .= "exten => 8168,2,AGI(agi-record_prompts.agi,gsm-----720000)\n";
		$Vext .= "exten => 8168,3,Hangup\n";
		}
	else
		{
		$Vext .= "; Voicemail Extensions go to main voicemail server:\n";
		$Vext .= "exten => _85026666666666.,1,Dial(\${TRUNK$voicemail_server_id}/\${EXTEN},99,oT)\n";
		$Vext .= "exten => 8500,1,Dial(\${TRUNK$voicemail_server_id}/\${EXTEN},99,oT)\n";
		$Vext .= "exten => 8501,1,Dial(\${TRUNK$voicemail_server_id}/\${EXTEN},99,oT)\n";
		$Vext .= "\n";
		$Vext .= "; Prompt Extensions go to main voicemail server:\n";
		$Vext .= "exten => 8167,1,Dial(\${TRUNK$voicemail_server_id}/\${EXTEN},99,oT)\n";
		$Vext .= "exten => 8168,1,Dial(\${TRUNK$voicemail_server_id}/\${EXTEN},99,oT)\n";
		}

	$Vext .= "\n";
	$Vext .= "; this is used for recording conference calls, the client app sends the filename\n";
	$Vext .= ";    value as a callerID recordings go to /var/spool/asterisk/monitor (WAV)\n";
	$Vext .= ";    Recording is limited to 1 hour, to make longer, just change the server\n";
	$Vext .= ";    setting ViciDial Recording Limit\n";
	$Vext .= ";     this is the WAV verison, default\n";
	$Vext .= "exten => 8309,1,Answer\n";
	if ($asterisk_version =~ /^1.2/)
		{$Vext .= "exten => 8309,2,Monitor(wav,\${CALLERIDNAME})\n";}
	else
		{$Vext .= "exten => 8309,2,Monitor(wav,\${CALLERID(name)})\n";}
	$Vext .= "exten => 8309,3,Wait,$vicidial_recording_limit\n";
	$Vext .= "exten => 8309,4,Hangup\n";
	$Vext .= ";     this is the GSM verison\n";
	$Vext .= "exten => 8310,1,Answer\n";
	if ($asterisk_version =~ /^1.2/)
		{$Vext .= "exten => 8310,2,Monitor(gsm,\${CALLERIDNAME})\n";}
	else
		{$Vext .= "exten => 8310,2,Monitor(gsm,\${CALLERID(name)})\n";}
	$Vext .= "exten => 8310,3,Wait,$vicidial_recording_limit\n";
	$Vext .= "exten => 8310,4,Hangup\n";


	$Vext .= "\n;     agent alert extension\n";
	$Vext .= "exten => 83047777777777,1,Answer\n";
	if ($asterisk_version =~ /^1.2/)
		{$Vext .= "exten => 83047777777777,2,Playback(\${CALLERIDNAME})\n";}
	else
		{$Vext .= "exten => 83047777777777,2,Playback(\${CALLERID(name)})\n";}
	$Vext .= "exten => 83047777777777,3,Hangup\n";
	##### END Create Voicemail extensions for this server_ip #####



	##### BEGIN Generate the IAX carriers for this server_ip #####
	$stmtA = "SELECT carrier_id,carrier_name,registration_string,template_id,account_entry,globals_string,dialplan_entry,carrier_description FROM vicidial_server_carriers where server_ip='$server_ip' and active='Y' and protocol='IAX2' order by carrier_id;";
	$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
	$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
	$sthArows=$sthA->rows;
	$i=0;
	while ($sthArows > $i)
		{
		@aryA = $sthA->fetchrow_array;
		$carrier_id[$i]	=			$aryA[0];
		$carrier_name[$i]	=		$aryA[1];
		$registration_string[$i] =	$aryA[2];
		$template_id[$i] =			$aryA[3];
		$account_entry[$i] =		$aryA[4];
		$globals_string[$i] =		$aryA[5];
		$dialplan_entry[$i] =		$aryA[6];
		$carrier_description[$i] =	$aryA[7];
		$i++;
		}
	$sthA->finish();

	$i=0;
	while ($sthArows > $i)
		{
		$template_contents[$i]='';
		if ( (length($template_id[$i]) > 1) && ($template_id[$i] !~ /--NONE--/) ) 
			{
			$stmtA = "SELECT template_contents FROM vicidial_conf_templates where template_id='$template_id[$i]';";
			$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
			$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
			$sthBrows=$sthA->rows;
			if ($sthBrows > 0)
				{
				@aryA = $sthA->fetchrow_array;
				$template_contents[$i]	=	"$aryA[0]";
				}
			$sthA->finish();
			}
		$ext  .= "$globals_string[$i]\n";

		$iax  .= "$registration_string[$i]\n";

		$Lext .= "; VICIDIAL Carrier: $carrier_id[$i] - $carrier_name[$i]\n";
		if (length($carrier_description[$i]) > 0) {$Lext .= "; $carrier_description[$i]\n";}
		$Lext .= "$dialplan_entry[$i]\n";

		$Liax .= "; VICIDIAL Carrier: $carrier_id[$i] - $carrier_name[$i]\n";
		if (length($carrier_description[$i]) > 0) {$Liax .= "; $carrier_description[$i]\n";}
		$Liax .= "$account_entry[$i]\n";
		$Liax .= "$template_contents[$i]\n";

		$i++;
		}
	##### END Generate the IAX carriers for this server_ip #####



	##### BEGIN Generate the SIP carriers for this server_ip #####
	$stmtA = "SELECT carrier_id,carrier_name,registration_string,template_id,account_entry,globals_string,dialplan_entry,carrier_description FROM vicidial_server_carriers where server_ip='$server_ip' and active='Y' and protocol='SIP' order by carrier_id;";
	$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
	$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
	$sthArows=$sthA->rows;
	$i=0;
	while ($sthArows > $i)
		{
		@aryA = $sthA->fetchrow_array;
		$carrier_id[$i]	=			$aryA[0];
		$carrier_name[$i]	=		$aryA[1];
		$registration_string[$i] =	$aryA[2];
		$template_id[$i] =			$aryA[3];
		$account_entry[$i] =		$aryA[4];
		$globals_string[$i] =		$aryA[5];
		$dialplan_entry[$i] =		$aryA[6];
		$carrier_description[$i] =	$aryA[7];
		$i++;
		}
	$sthA->finish();

	$i=0;
	while ($sthArows > $i)
		{
		$template_contents[$i]='';
		if ( (length($template_id[$i]) > 1) && ($template_id[$i] !~ /--NONE--/) ) 
			{
			$stmtA = "SELECT template_contents FROM vicidial_conf_templates where template_id='$template_id[$i]';";
			$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
			$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
			$sthBrows=$sthA->rows;
			if ($sthBrows > 0)
				{
				@aryA = $sthA->fetchrow_array;
				$template_contents[$i]	=	"$aryA[0]";
				}
			$sthA->finish();
			}
		$ext  .= "$globals_string[$i]\n";

		$sip  .= "$registration_string[$i]\n";

		$Lext .= "; VICIDIAL Carrier: $carrier_id[$i] - $carrier_name[$i]\n";
		if (length($carrier_description[$i]) > 0) {$Lext .= "; $carrier_description[$i]\n";}
		$Lext .= "$dialplan_entry[$i]\n";

		$Lsip .= "; VICIDIAL Carrier: $carrier_id[$i] - $carrier_name[$i]\n";
		if (length($carrier_description[$i]) > 0) {$Lsip .= "; $carrier_description[$i]\n";}
		$Lsip .= "$account_entry[$i]\n";
		$Lsip .= "$template_contents[$i]\n";

		$i++;
		}
	##### BEGIN Generate the SIP carriers for this server_ip #####


	$Pext .= "\n";
	$Pext .= "; Phones direct dial extensions:\n";


	##### BEGIN Generate the IAX phone entries #####
	$stmtA = "SELECT extension,dialplan_number,voicemail_id,pass,template_id,conf_override,email,template_id,conf_override,outbound_cid,fullname,phone_context,phone_ring_timeout,conf_secret,delete_vm_after_email,codecs_list,codecs_with_template,voicemail_timezone,voicemail_options FROM phones where server_ip='$server_ip' and protocol='IAX2' and active='Y' order by extension;";
	#	print "$stmtA\n";
	$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
	$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
	$sthArows=$sthA->rows;
	$i=0;
	while ($sthArows > $i)
		{
		@aryA = $sthA->fetchrow_array;
		$extension[$i] =				$aryA[0];
		$dialplan[$i] =					$aryA[1];
		$voicemail[$i] =				$aryA[2];
		$pass[$i] =						$aryA[3];
		$template_id[$i] =				$aryA[4];
		$conf_override[$i] =			$aryA[5];
		$email[$i] =					$aryA[6];
		$template_id[$i] =				$aryA[7];
		$conf_override[$i] =			$aryA[8];
		$outbound_cid[$i] =				$aryA[9];
		$fullname[$i] =					$aryA[10];
		$phone_context[$i] =			$aryA[11];
		$phone_ring_timeout[$i] =		$aryA[12];
		$conf_secret[$i] =				$aryA[13];
		$delete_vm_after_email[$i] =	$aryA[14];
		$codecs_list[$i] =				$aryA[15];
		$codecs_with_template[$i] =		$aryA[16];
		$voicemail_timezone[$i] =		$aryA[17];
		$voicemail_options[$i] =		$aryA[18];
		if ( (length($SSdefault_codecs) > 2) && (length($codecs_list[$i]) < 3) )
			{$codecs_list[$i] = $SSdefault_codecs;}
		$active_dialplan_numbers .= "'$aryA[1]',";

		$i++;
		}
	$sthA->finish();

	$i=0;
	while ($sthArows > $i)
		{
		$conf_entry_written=0;
		$template_contents[$i]='';
		$Pcodec='';
		if (length($codecs_list[$i]) > 2)
			{
			if ($codecs_list[$i] =~ /gsm/i)			{$Pcodec .= "allow=gsm\n";}
			if ($codecs_list[$i] =~ /ulaw|u-law/i)	{$Pcodec .= "allow=ulaw\n";}
			if ($codecs_list[$i] =~ /alaw|a-law/i)	{$Pcodec .= "allow=alaw\n";}
			if ($codecs_list[$i] =~ /g722|g\.722/i)	{$Pcodec .= "allow=g722\n";}
			if ($codecs_list[$i] =~ /g723|g\.723/i)	{$Pcodec .= "allow=g723.1\n";}
			if ($codecs_list[$i] =~ /g726|g\.726/i)	{$Pcodec .= "allow=g726\n";}
			if ($codecs_list[$i] =~ /g729|g\.729/i)	{$Pcodec .= "allow=g729\n";}
			if ($codecs_list[$i] =~ /ilbc/i)		{$Pcodec .= "allow=ilbc\n";}
			if ($codecs_list[$i] =~ /lpc10/i)		{$Pcodec .= "allow=lpc10\n";}
			if ($codecs_list[$i] =~ /speex/i)		{$Pcodec .= "allow=speex\n";}
			if ($codecs_list[$i] =~ /adpcm/i)		{$Pcodec .= "allow=adpcm\n";}
			if (length($Pcodec) > 2)
				{$Pcodec = "disallow=all\n$Pcodec";}
			}
		if ($DBXXX > 0) {print "IAX|$extension[$i]|$codecs_list[$i]|$Pcodec\n";}

		if ( (length($template_id[$i]) > 1) && ($template_id[$i] !~ /--NONE--/) ) 
			{
			$stmtA = "SELECT template_contents FROM vicidial_conf_templates where template_id='$template_id[$i]';";
			$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
			$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
			$sthBrows=$sthA->rows;
			if ($sthBrows > 0)
				{
				@aryA = $sthA->fetchrow_array;
				$template_contents[$i]	=	"$aryA[0]";

				$Piax .= "\n\[$extension[$i]\]\n";
				$Piax .= "username=$extension[$i]\n";
				$Piax .= "secret=$conf_secret[$i]\n";
				$Piax .= "accountcode=$extension[$i]\n";
				$Piax .= "callerid=\"$fullname[$i]\" <$outbound_cid[$i]>\n";
				$Piax .= "mailbox=$voicemail[$i]\n";
				if ($codecs_with_template[$i] > 0) 
					{$Piax .= "$Pcodec";}
				$Piax .= "$template_contents[$i]\n";
				
				$conf_entry_written++;
				}
			$sthA->finish();
			}
		if (length($conf_override[$i]) > 10)
			{
			$Piax .= "\n\[$extension[$i]\]\n";
			$Piax .= "$conf_override[$i]\n";
			$conf_entry_written++;
			}
		if ($conf_entry_written < 1)
			{
			$Piax .= "\n\[$extension[$i]\]\n";
			$Piax .= "username=$extension[$i]\n";
			$Piax .= "secret=$conf_secret[$i]\n";
			$Piax .= "accountcode=$extension[$i]\n";
			$Piax .= "callerid=\"$fullname[$i]\" <$outbound_cid[$i]>\n";
			$Piax .= "mailbox=$voicemail[$i]\n";
			$Piax .= "requirecalltoken=no\n";
			$Piax .= "context=$phone_context[$i]\n";
			$Piax .= "$Pcodec";
			$Piax .= "type=friend\n";
			$Piax .= "auth=md5\n";
			$Piax .= "host=dynamic\n";
			}
		$Pext .= "exten => $dialplan[$i],1,Dial(IAX2/$extension[$i]|$phone_ring_timeout[$i]|)\n";
		$Pext .= "exten => $dialplan[$i],2,Goto(default,85026666666666$voicemail[$i],1)\n";

		if ($delete_vm_after_email[$i] =~ /Y/)
			{$vm  .= "$voicemail[$i] => $pass[$i],$extension[$i] Mailbox,$email[$i],,|delete=yes|tz=$voicemail_timezone[$i]|$voicemail_options[$i]\n";}
		else
			{$vm  .= "$voicemail[$i] => $pass[$i],$extension[$i] Mailbox,$email[$i],,|delete=no|tz=$voicemail_timezone[$i]|$voicemail_options[$i]\n";}

		$i++;
		}
	##### END Generate the IAX phone entries #####



	##### BEGIN Generate the SIP phone entries #####
	$stmtA = "SELECT extension,dialplan_number,voicemail_id,pass,template_id,conf_override,email,template_id,conf_override,outbound_cid,fullname,phone_context,phone_ring_timeout,conf_secret,delete_vm_after_email,codecs_list,codecs_with_template,voicemail_timezone,voicemail_options FROM phones where server_ip='$server_ip' and protocol='SIP' and active='Y' order by extension;";
	#	print "$stmtA\n";
	$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
	$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
	$sthArows=$sthA->rows;
	$i=0;
	while ($sthArows > $i)
		{
		@aryA = $sthA->fetchrow_array;
		$extension[$i] =				$aryA[0];
		$dialplan[$i] =					$aryA[1];
		$voicemail[$i] =				$aryA[2];
		$pass[$i] =						$aryA[3];
		$template_id[$i] =				$aryA[4];
		$conf_override[$i] =			$aryA[5];
		$email[$i] =					$aryA[6];
		$template_id[$i] =				$aryA[7];
		$conf_override[$i] =			$aryA[8];
		$outbound_cid[$i] =				$aryA[9];
		$fullname[$i] =					$aryA[10];
		$phone_context[$i] =			$aryA[11];
		$phone_ring_timeout[$i] =		$aryA[12];
		$conf_secret[$i] =				$aryA[13];
		$delete_vm_after_email[$i] =	$aryA[14];
		$codecs_list[$i] =				$aryA[15];
		$codecs_with_template[$i] =		$aryA[16];
		$voicemail_timezone[$i] =		$aryA[17];
		$voicemail_options[$i] =		$aryA[18];
		if ( (length($SSdefault_codecs) > 2) && (length($codecs_list[$i]) < 3) )
			{$codecs_list[$i] = $SSdefault_codecs;}
		$active_dialplan_numbers .= "'$aryA[1]',";

		$i++;
		}
	$sthA->finish();

	$i=0;
	while ($sthArows > $i)
		{
		$conf_entry_written=0;
		$template_contents[$i]='';
		$Pcodec='';
		if (length($codecs_list[$i]) > 2)
			{
			if ($codecs_list[$i] =~ /gsm/i)			{$Pcodec .= "allow=gsm\n";}
			if ($codecs_list[$i] =~ /ulaw|u-law/i)	{$Pcodec .= "allow=ulaw\n";}
			if ($codecs_list[$i] =~ /alaw|a-law/i)	{$Pcodec .= "allow=alaw\n";}
			if ($codecs_list[$i] =~ /g722|g\.722/i)	{$Pcodec .= "allow=g722\n";}
			if ($codecs_list[$i] =~ /g723|g\.723/i)	{$Pcodec .= "allow=g723.1\n";}
			if ($codecs_list[$i] =~ /g726|g\.726/i)	{$Pcodec .= "allow=g726\n";}
			if ($codecs_list[$i] =~ /g729|g\.729/i)	{$Pcodec .= "allow=g729\n";}
			if ($codecs_list[$i] =~ /ilbc/i)		{$Pcodec .= "allow=ilbc\n";}
			if ($codecs_list[$i] =~ /lpc10/i)		{$Pcodec .= "allow=lpc10\n";}
			if ($codecs_list[$i] =~ /speex/i)		{$Pcodec .= "allow=speex\n";}
			if ($codecs_list[$i] =~ /adpcm/i)		{$Pcodec .= "allow=adpcm\n";}
			if (length($Pcodec) > 2)
				{$Pcodec = "disallow=all\n$Pcodec";}
			}
		if ($DBXXX > 0) {print "SIP|$extension[$i]|$codecs_list[$i]|$Pcodec\n";}

		if ( (length($template_id[$i]) > 1) && ($template_id[$i] !~ /--NONE--/) ) 
			{
			$stmtA = "SELECT template_contents FROM vicidial_conf_templates where template_id='$template_id[$i]';";
			$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
			$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
			$sthBrows=$sthA->rows;
			if ($sthBrows > 0)
				{
				@aryA = $sthA->fetchrow_array;
				$template_contents[$i]	=	"$aryA[0]";

				$Psip .= "\n\[$extension[$i]\]\n";
				$Psip .= "username=$extension[$i]\n";
				$Psip .= "secret=$conf_secret[$i]\n";
				$Psip .= "accountcode=$extension[$i]\n";
				$Psip .= "callerid=\"$fullname[$i]\" <$outbound_cid[$i]>\n";
				$Psip .= "mailbox=$voicemail[$i]\n";
				if ($codecs_with_template[$i] > 0) 
					{$Psip .= "$Pcodec";}
				$Psip .= "$template_contents[$i]\n";
				
				$conf_entry_written++;
				}
			$sthA->finish();
			}
		if (length($conf_override[$i]) > 10)
			{
			$Psip .= "\n\[$extension[$i]\]\n";
			$Psip .= "$conf_override[$i]\n";
			$conf_entry_written++;
			}
		if ($conf_entry_written < 1)
			{
			$Psip .= "\n\[$extension[$i]\]\n";
			$Psip .= "username=$extension[$i]\n";
			$Psip .= "secret=$conf_secret[$i]\n";
			$Psip .= "accountcode=$extension[$i]\n";
			$Psip .= "callerid=\"$fullname[$i]\" <$outbound_cid[$i]>\n";
			$Psip .= "mailbox=$voicemail[$i]\n";
			$Psip .= "context=$phone_context[$i]\n";
			$Psip .= "$Pcodec";
			$Psip .= "type=friend\n";
			$Psip .= "host=dynamic\n";
			}
		$Pext .= "exten => $dialplan[$i],1,Dial(SIP/$extension[$i]|$phone_ring_timeout[$i]|)\n";
		$Pext .= "exten => $dialplan[$i],2,Goto(default,85026666666666$voicemail[$i],1)\n";

		if ($delete_vm_after_email[$i] =~ /Y/)
			{$vm  .= "$voicemail[$i] => $pass[$i],$extension[$i] Mailbox,$email[$i],,|delete=yes|tz=$voicemail_timezone[$i]|$voicemail_options[$i]\n";}
		else
			{$vm  .= "$voicemail[$i] => $pass[$i],$extension[$i] Mailbox,$email[$i],,|delete=no|tz=$voicemail_timezone[$i]|$voicemail_options[$i]\n";}

		$i++;
		}
	##### END Generate the SIP phone entries #####



	if ( ($SSgenerate_cross_server_exten > 0) and (length($active_server_ips) > 7) )
		{
		##### BEGIN Generate the CROSS SERVER IAX and SIP phone entries #####
		$stmtA = "SELECT extension,dialplan_number,fullname,server_ip FROM phones where server_ip NOT IN('$server_ip') and server_ip IN($active_server_ips) and dialplan_number NOT IN($active_dialplan_numbers'') and protocol IN('SIP','IAX2') and active='Y' order by dialplan_number,server_ip;";
		#	print "$stmtA\n";
		$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
		$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
		$sthArows=$sthA->rows;
		$i=0;
		while ($sthArows > $i)
			{
			@aryA = $sthA->fetchrow_array;
			$CXextension[$i] =				$aryA[0];
			$CXdialplan[$i] =				$aryA[1];
			$CXfullname[$i] =				$aryA[2];
			$CXserver_ip[$i] =				$aryA[3];

			$i++;
			}
		$sthA->finish();

		$i=0;
		while ($sthArows > $i)
			{
			$CXVARremDIALstr='';
			if( $CXserver_ip[$i] =~ m/(\S+)\.(\S+)\.(\S+)\.(\S+)/ )
				{
				$a = leading_zero($1);
				$b = leading_zero($2);
				$c = leading_zero($3);
				$d = leading_zero($4);
				$CXVARremDIALstr = "$a$S$b$S$c$S$d$S";
				}
			$Pext .= "; Remote Phone Entry $i: $CXextension[$i] $CXserver_ip[$i] $CXfullname[$i]\n";
			$Pext .= "exten => $CXdialplan[$i],1,Goto(default,$CXVARremDIALstr$CXdialplan[$i],1)\n";

			$i++;
			}
		##### END Generate the CROSS SERVER IAX and SIP phone entries #####
		}


	##### BEGIN Generate the Call Menu entries #####
	$stmtA = "SELECT menu_id,menu_name,menu_prompt,menu_timeout,menu_timeout_prompt,menu_invalid_prompt,menu_repeat,menu_time_check,call_time_id,track_in_vdac,custom_dialplan_entry,tracking_group,dtmf_log,dtmf_field FROM vicidial_call_menu order by menu_id;";
	#	print "$stmtA\n";
	$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
	$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
	$sthArows=$sthA->rows;
	$i=0;
	while ($sthArows > $i)
		{
		@aryA = $sthA->fetchrow_array;
		$menu_id[$i] =				$aryA[0];
		$menu_name[$i] =			$aryA[1];
		$menu_prompt[$i] =			$aryA[2];
		$menu_timeout[$i] =			$aryA[3];
		$menu_timeout_prompt[$i] =	$aryA[4];
		$menu_invalid_prompt[$i] =	$aryA[5];
		$menu_repeat[$i] =			$aryA[6];
		$menu_time_check[$i] =		$aryA[7];
		$call_time_id[$i] =			$aryA[8];
		$track_in_vdac[$i] =		$aryA[9];
		$custom_dialplan_entry[$i]= $aryA[10];
		$tracking_group[$i] =		$aryA[11];
		$dtmf_log[$i] =				$aryA[12];
		$dtmf_field[$i] =			$aryA[13];

		if ($track_in_vdac[$i] > 0)
			{$track_in_vdac[$i] = 'YES'}
		else
			{$track_in_vdac[$i] = 'NO'}
		$i++;
		}
	$sthA->finish();

	$i=0;
	$call_menu_ext = '';
	$CM_agi_string='';
	while ($sthArows > $i)
		{
		$stmtA = "SELECT option_value,option_description,option_route,option_route_value,option_route_value_context FROM vicidial_call_menu_options where menu_id='$menu_id[$i]' order by option_value;";
		#	print "$stmtA\n";
		$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
		$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
		$sthArowsJ=$sthA->rows;
		$j=0;
		$time_check_scheme = '';
		$time_check_route = '';
		$time_check_route_value = '';
		$time_check_route_context = '';
		$call_menu_timeout_ext = '';
		$call_menu_invalid_ext = '';
		$call_menu_options_ext = '';
		$cm_invalid_set=0;
		$cm_timeout_set=0;
		if ($DBX>0) {print "$sthArowsJ|$stmtA\n";}
		while ($sthArowsJ > $j)
			{
			@aryA = $sthA->fetchrow_array;
			$option_value[$j] =					$aryA[0];
			$option_description[$j] =			$aryA[1];
			$option_route[$j] =					$aryA[2];
			$option_route_value[$j] =			$aryA[3];
			$option_route_value_context[$j] =	$aryA[4];
			if ($option_value[$j] =~ /STAR/) {$option_value[$j] = '*';}
			if ($option_value[$j] =~ /HASH/) {$option_value[$j] = '#';}
			$j++;
			}

		$j=0;
		while ($sthArowsJ > $j)
			{
			$PRI=1;
			$call_menu_line='';
			if ( ($option_value[$j] =~ /TIMECHECK/) && ($menu_time_check[$i] > 0) && (length($call_time_id[$i])>0) )
				{
				$time_check_scheme =			$call_time_id[$i];
				$time_check_route =				$option_route[$j];
				$time_check_route_value =		$option_route_value[$j];
				$time_check_route_context =		$option_route_value_context[$j];

				if ($option_route[$j] =~ /INGROUP/)
					{
					@IGoption_route_value_context = split(/,/,$option_route_value_context[$j]);
					$IGhandle_method =			$IGoption_route_value_context[0];
					$IGsearch_method =			$IGoption_route_value_context[1];
					$IGlist_id =				$IGoption_route_value_context[2];
					$IGcampaign_id =			$IGoption_route_value_context[3];
					$IGphone_code =				$IGoption_route_value_context[4];
					$IGvid_enter_filename =		$IGoption_route_value_context[5];
					$IGvid_id_number_filename =	$IGoption_route_value_context[6];
					$IGvid_confirm_filename =	$IGoption_route_value_context[7];
					$IGvid_validate_digits =	$IGoption_route_value_context[8];

					$CM_agi_string = "agi-VDAD_ALL_inbound.agi,$IGhandle_method-----$IGsearch_method-----$option_route_value[$j]-----$menu_id[$i]--------------------$IGlist_id-----$IGphone_code-----$IGcampaign_id---------------$IGvid_enter_filename-----$IGvid_id_number_filename-----$IGvid_confirm_filename-----$IGvid_validate_digits";
					}
				}
			else
				{
				if (length($option_description[$j])>0)
					{
					$call_menu_line .= "; $option_description[$j]\n";
					}
				if ($option_value[$j] =~ /TIMEOUT/)
					{
					$option_value[$j] = 't';
					if ( (length($menu_timeout_prompt[$i])>0)  && ($menu_timeout_prompt[$i] !~ /NONE/) )
						{
						$menu_timeout_prompt_ext='';
						if ($menu_timeout_prompt[$i] =~ /\|/)
							{
							@menu_timeout_prompts_array = split(/\|/,$menu_timeout_prompt[$i]);
							$w=0;
							foreach(@menu_timeout_prompts_array)
								{
								if (length($menu_timeout_prompts_array[$w])>0)
									{
									$menu_timeout_prompt_ext .= "exten => t,$PRI,Playback($menu_timeout_prompts_array[$w])\n";
									$PRI++;
									}
								$w++;
								}
							}
						else
							{
							$menu_timeout_prompt_ext .= "exten => t,1,Playback($menu_timeout_prompt[$i])\n";
							$PRI++;
							}

						$call_menu_line .= "$menu_timeout_prompt_ext";
						$cm_timeout_set++;
						}
					}
				if ($option_value[$j] =~ /INVALID/)
					{
					$menu_invalid_prompt_ext='';
					if ( (length($menu_invalid_prompt[$i])>0) && ($menu_invalid_prompt[$i] !~ /NONE/) )
						{
						if ($menu_invalid_prompt[$i] =~ /\|/)
							{
							@menu_invalid_prompts_array = split(/\|/,$menu_invalid_prompt[$i]);
							$w=0;
							foreach(@menu_invalid_prompts_array)
								{
								if (length($menu_invalid_prompts_array[$w])>0)
									{
									$menu_invalid_prompt_ext .= "exten => i,$PRI,Playback($menu_invalid_prompts_array[$w])\n";
									$PRI++;
									}
								$w++;
								}
							}
						else
							{
							$menu_invalid_prompt_ext .= "exten => i,1,Playback($menu_invalid_prompt[$i])\n";
							$PRI++;
							}

						}
					if ( ($option_value[$j] =~ /INVALID_2ND/) && ($cm_invalid_set < 1) )
						{
						$menu_invalid_prompt_ext .= "exten => i,$PRI,Set(INVCOUNT=\$[\$\{INVCOUNT\} + 1]) \n";   $PRI++;
						$menu_invalid_prompt_ext .= "exten => i,$PRI,NoOp(\$\{INVCOUNT\}) \n";   $PRI++;
						$menu_invalid_prompt_ext .= "exten => i,$PRI,Gotoif(\$[0\$\{INVCOUNT\} < 2]?" . $menu_id[$i] . ",s,3) \n";   $PRI++;
						}
					if ( ($option_value[$j] =~ /INVALID_3RD/) && ($cm_invalid_set < 1) )
						{
						$menu_invalid_prompt_ext .= "exten => i,$PRI,Set(INVCOUNT=\$[\$\{INVCOUNT\} + 1]) \n";   $PRI++;
						$menu_invalid_prompt_ext .= "exten => i,$PRI,NoOp(\${INVCOUNT}) \n";   $PRI++;
						$menu_invalid_prompt_ext .= "exten => i,$PRI,Gotoif(\$[0\$\{INVCOUNT\} < 3]?" . $menu_id[$i] . ",s,3) \n";   $PRI++;
						}

					$call_menu_line .= "$menu_invalid_prompt_ext";
					$cm_invalid_set++;
					$option_value[$j] = 'i';
					}
				if ($option_route[$j] =~ /AGI/)
					{
					if ($dtmf_log[$i] > 0) 
						{$call_menu_line .= "exten => $option_value[$j],$PRI,AGI(cm.agi,$tracking_group[$i]-----$option_value[$j]-----$dtmf_field[$i])\n";   $PRI++;}
					$call_menu_line .= "exten => $option_value[$j],$PRI,AGI($option_route_value[$j])\n";
					}
				if ($option_route[$j] =~ /CALLMENU/)
					{
					if ($dtmf_log[$i] > 0) 
						{$call_menu_line .= "exten => $option_value[$j],$PRI,AGI(cm.agi,$tracking_group[$i]-----$option_value[$j]-----$dtmf_field[$i])\n";   $PRI++;}
					$call_menu_line .= "exten => $option_value[$j],$PRI,Goto($option_route_value[$j],s,1)\n";
					}
				if ($option_route[$j] =~ /DID/)
					{
					if ($dtmf_log[$i] > 0) 
						{$call_menu_line .= "exten => $option_value[$j],$PRI,AGI(cm.agi,$tracking_group[$i]-----$option_value[$j]-----$dtmf_field[$i])\n";   $PRI++;}
					$call_menu_line .= "exten => $option_value[$j],$PRI,Goto(trunkinbound,$option_route_value[$j],1)\n";
					}
				if ($option_route[$j] =~ /INGROUP/)
					{
					@IGoption_route_value_context = split(/,/,$option_route_value_context[$j]);
					$IGhandle_method =			$IGoption_route_value_context[0];
					$IGsearch_method =			$IGoption_route_value_context[1];
					$IGlist_id =				$IGoption_route_value_context[2];
					$IGcampaign_id =			$IGoption_route_value_context[3];
					$IGphone_code =				$IGoption_route_value_context[4];
					$IGvid_enter_filename =		$IGoption_route_value_context[5];
					$IGvid_id_number_filename =	$IGoption_route_value_context[6];
					$IGvid_confirm_filename =	$IGoption_route_value_context[7];
					$IGvid_validate_digits =	$IGoption_route_value_context[8];

					if ($dtmf_log[$i] > 0) 
						{$call_menu_line .= "exten => $option_value[$j],$PRI,AGI(cm.agi,$tracking_group[$i]-----$option_value[$j]-----$dtmf_field[$i])\n";   $PRI++;}
					$call_menu_line .= "exten => $option_value[$j],$PRI,AGI(agi-VDAD_ALL_inbound.agi,$IGhandle_method-----$IGsearch_method-----$option_route_value[$j]-----$menu_id[$i]--------------------$IGlist_id-----$IGphone_code-----$IGcampaign_id---------------$IGvid_enter_filename-----$IGvid_id_number_filename-----$IGvid_confirm_filename-----$IGvid_validate_digits)\n";
					}
				if ($option_route[$j] =~ /EXTENSION/)
					{
					if ($dtmf_log[$i] > 0) 
						{$call_menu_line .= "exten => $option_value[$j],$PRI,AGI(cm.agi,$tracking_group[$i]-----$option_value[$j]-----$dtmf_field[$i])\n";   $PRI++;}
					if (length($option_route_value_context[$j])>0) {$option_route_value_context[$j] = "$option_route_value_context[$j],";}
					$call_menu_line .= "exten => $option_value[$j],$PRI,Goto($option_route_value_context[$j]$option_route_value[$j],1)\n";
					}
				if ($option_route[$j] =~ /VOICEMAIL/)
					{
					if ($dtmf_log[$i] > 0) 
						{$call_menu_line .= "exten => $option_value[$j],$PRI,AGI(cm.agi,$tracking_group[$i]-----$option_value[$j]-----$dtmf_field[$i])\n";   $PRI++;}
					$call_menu_line .= "exten => $option_value[$j],$PRI,Goto(default,85026666666666$option_route_value[$j],1)\n";
					}
				if ($option_route[$j] =~ /HANGUP/)
					{
					if ( (length($option_route_value[$j])>0) && ($option_route_value[$j] !~ /NONE/) )
						{
						$hangup_prompt_ext='';
						if ($option_route_value[$j] =~ /\|/)
							{
							@hangup_prompts_array = split(/\|/,$option_route_value[$j]);
							$w=0;
							foreach(@hangup_prompts_array)
								{
								if (length($hangup_prompts_array[$w])>0)
									{
									$hangup_prompt_ext .= "exten => $option_value[$j],$PRI,Playback($hangup_prompts_array[$w])\n";
									$PRI++;
									}
								$w++;
								}
							}
						else
							{
							$hangup_prompt_ext .= "exten => $option_value[$j],$PRI,Playback($option_route_value[$j])\n";
							$PRI++;
							}

						$call_menu_line .= "$hangup_prompt_ext";
						if ($dtmf_log[$i] > 0) 
							{$call_menu_line .= "exten => $option_value[$j],$PRI,AGI(cm.agi,$tracking_group[$i]-----$option_value[$j]-----$dtmf_field[$i])\n";   $PRI++;}
						$call_menu_line .= "exten => $option_value[$j],n,Hangup\n";
						}
					else
						{
						if ($dtmf_log[$i] > 0) 
							{$call_menu_line .= "exten => $option_value[$j],$PRI,AGI(cm.agi,$tracking_group[$i]-----$option_value[$j]-----$dtmf_field[$i])\n";   $PRI++;}
						$call_menu_line .= "exten => $option_value[$j],$PRI,Hangup\n";
						}
					}
				if ($option_route[$j] =~ /PHONE/)
					{
					$stmtA = "SELECT dialplan_number,server_ip FROM phones where login='$option_route_value[$j]';";
					#	print "$stmtA\n";
					$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
					$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
					$sthArowsP=$sthA->rows;
					if ($sthArowsP > 0)
						{
						@aryA = $sthA->fetchrow_array;
						$Pdialplan =	"$aryA[0]";
						$Pserver_ip =	"$aryA[1]";

						### format the remote server dialstring to get the call to the overflow agent meetme room
						$S='*';
						if( $Pserver_ip =~ m/(\S+)\.(\S+)\.(\S+)\.(\S+)/ )
							{
							$a = leading_zero($1); 
							$b = leading_zero($2); 
							$c = leading_zero($3); 
							$d = leading_zero($4);
							$DIALstring = "$a$S$b$S$c$S$d$S";
							}
						if ($dtmf_log[$i] > 0) 
							{$call_menu_line .= "exten => $option_value[$j],$PRI,AGI(cm.agi,$tracking_group[$i]-----$option_value[$j]-----$dtmf_field[$i])\n";   $PRI++;}
						$call_menu_line .= "exten => $option_value[$j],$PRI,Goto(default,$DIALstring$Pdialplan,1)\n";
						}
					$sthA->finish();
					}

				if ($option_value[$j] =~ /t/)
					{
					$call_menu_timeout_ext = "$call_menu_line";
					}
				if ($option_value[$j] =~ /i/)
					{
					if ($cm_invalid_set > 1) 
						{$call_menu_invalid_ext .= "; COMMENTED OUT...\n";}
					else
						{$call_menu_invalid_ext = "$call_menu_line";}
					}
				if ($option_value[$j] !~ /i|t/)
					{
					$call_menu_options_ext .= "$call_menu_line";
					}
				}
			if ($DBX>0) {print "$i|$j|     $menu_id[$i]|$option_value[$j]\n";}
			$j++;
			}
		$sthA->finish();

		$menu_prompt_ext='';
		if ($menu_prompt[$i] =~ /\|/)
			{
			@menu_prompts_array = split(/\|/,$menu_prompt[$i]);
			$w=0;
			foreach(@menu_prompts_array)
				{
				if (length($menu_prompts_array[$w])>0)
					{$menu_prompt_ext .= "exten => s,n,Background($menu_prompts_array[$w])\n";}
				$w++;
				}
			}
		else
			{$menu_prompt_ext .= "exten => s,n,Background($menu_prompt[$i])\n";}

		if ($time_check_route =~ /AGI/)
			{
			$call_menu_options_ext .= "; time check after hours AGI special extension\n";
			$call_menu_options_ext .= "exten => 9999999999999999999988,1,AGI($time_check_route_value)\n";

			$time_check_route = 'EXTENSION';
			$time_check_route_value = '9999999999999999999988';
			$time_check_route_context = $menu_id[$i];
			}
		if ($time_check_route =~ /INGROUP/)
			{
			$call_menu_options_ext .= "; time check after hours INGROUP special extension\n";
			$call_menu_options_ext .= "exten => 9999999999999999999988,1,AGI($CM_agi_string)\n";

			$time_check_route = 'EXTENSION';
			$time_check_route_value = '9999999999999999999988';
			$time_check_route_context = $menu_id[$i];
			}
		$call_menu_ext .= "\n";
		$call_menu_ext .= "; $menu_name[$i]\n";
		$call_menu_ext .= "[$menu_id[$i]]\n";
		$call_menu_ext .= "exten => s,1,AGI(agi-VDAD_inbound_calltime_check.agi,$tracking_group[$i]-----$track_in_vdac[$i]-----$menu_id[$i]-----$time_check_scheme-----$time_check_route-----$time_check_route_value-----$time_check_route_context)\n";
		$call_menu_ext .= "exten => s,n,Set(INVCOUNT=0) \n";
		$call_menu_ext .= "$menu_prompt_ext";
		if ($menu_timeout[$i] > 0)
			{$call_menu_ext .= "exten => s,n,WaitExten($menu_timeout[$i])\n";}
		$k=0;
		while ($k < $menu_repeat[$i]) 
			{
			$call_menu_ext .= "$menu_prompt_ext";
			if ($menu_timeout[$i] > 0)
				{$call_menu_ext .= "exten => s,n,WaitExten($menu_timeout[$i])\n";}
			$k++;
			}
	#	$call_menu_ext .= "exten => s,n,Hangup\n";
		$call_menu_ext .= "\n";
		$call_menu_ext .= "$call_menu_options_ext";
		$call_menu_ext .= "\n";

		if (length($call_menu_timeout_ext) < 1)
			{
			if ( (length($menu_timeout_prompt[$i])>0)  && ($menu_timeout_prompt[$i] !~ /NONE/) )
				{
				$call_menu_ext .= "exten => t,1,Playback($menu_timeout_prompt[$i])\n";
				$call_menu_ext .= "exten => t,n,Goto(s,3)\n";
				}
			else
				{
				$call_menu_ext .= "exten => t,1,Goto(s,3)\n";
				}
			}
		else
			{
			$call_menu_ext .= "$call_menu_timeout_ext";
			}
		if (length($call_menu_invalid_ext) < 1)
			{
			if ( (length($menu_invalid_prompt[$i])>0) && ($menu_invalid_prompt[$i] !~ /NONE/) )
				{
				$call_menu_ext .= "exten => i,1,Playback($menu_invalid_prompt[$i])\n";
				$call_menu_ext .= "exten => i,n,Goto(s,3)\n";
				}
			else
				{
				$call_menu_ext .= "exten => i,1,Goto(s,3)\n";
				}
			}
		else
			{
			$call_menu_ext .= "$call_menu_invalid_ext";
			}
		$call_menu_ext .= "; hangup\n";
		$call_menu_ext .= 'exten => h,1,DeadAGI(agi://127.0.0.1:4577/call_log--HVcauses--PRI-----NODEBUG-----${HANGUPCAUSE}-----${DIALSTATUS}-----${DIALEDTIME}-----${ANSWEREDTIME})';

		if (length($custom_dialplan_entry[$i]) > 4) 
			{
			$call_menu_ext .= "\n\n";
			$call_menu_ext .= "; custom dialplan entries\n";
			$call_menu_ext .= "$custom_dialplan_entry[$i]\n";
			}

		$call_menu_ext .= "\n\n";

		$i++;
		}
	##### END Generate the Call Menu entries #####



	##### BEGIN generate voicemail accounts for all distinct phones on dedicated voicemail server
	if ($THISserver_voicemail > 0)
		{
		$vm='';

		##### Get the distinct phone entries #####
		$stmtA = "SELECT distinct(voicemail_id) FROM phones where active='Y' order by voicemail_id;";
		#	print "$stmtA\n";
		$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
		$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
		$sthArows=$sthA->rows;
		$i=0;
		while ($sthArows > $i)
			{
			@aryA = $sthA->fetchrow_array;
			$voicemail[$i] =	$aryA[0];
			$i++;
			}
		$sthA->finish();

		$i=0;
		while ($sthArows > $i)
			{
			##### Get the distinct phone entries #####
			$stmtA = "SELECT extension,pass,email,delete_vm_after_email,voicemail_timezone,voicemail_options FROM phones where active='Y' and voicemail_id='$voicemail[$i]';";
			#	print "$stmtA\n";
			$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
			$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
			$sthArowsX=$sthA->rows;
			if ($sthArowsX > 0)
				{
				@aryA = $sthA->fetchrow_array;
				$extension[$i] =				$aryA[0];
				$pass[$i] =						$aryA[1];
				$email[$i] =					$aryA[2];
				$delete_vm_after_email[$i] =	$aryA[3];
				$voicemail_timezone[$i] =		$aryA[4];
				$voicemail_options[$i] =		$aryA[5];

				if ($delete_vm_after_email[$i] =~ /Y/)
					{$vm  .= "$voicemail[$i] => $pass[$i],$extension[$i] Mailbox,$email[$i],,|delete=yes|tz=$voicemail_timezone[$i]|$voicemail_options[$i]\n";}
				else
					{$vm  .= "$voicemail[$i] => $pass[$i],$extension[$i] Mailbox,$email[$i],,|delete=no|tz=$voicemail_timezone[$i]|$voicemail_options[$i]\n";}
				}
			$sthA->finish();

			$i++;
			}

		##### Get the other voicemail box entries #####
		$stmtA = "SELECT voicemail_id,fullname,pass,email,delete_vm_after_email,voicemail_timezone,voicemail_options FROM vicidial_voicemail where active='Y';";
		#	print "$stmtA\n";
		$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
		$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
		$sthArows=$sthA->rows;
		if ($sthArows > 0)
			{
			$vm  .= "\n";
			$vm  .= "; Other Voicemail Entries:\n";
			}
		$i=0;
		while ($sthArows > $i)
			{
			@aryA = $sthA->fetchrow_array;
			$voicemail_id[$i] =				$aryA[0];
			$fullname[$i] =					$aryA[1];
			$pass[$i] =						$aryA[2];
			$email[$i] =					$aryA[3];
			$delete_vm_after_email[$i] =	$aryA[4];
			$voicemail_timezone[$i] =		$aryA[5];
			$voicemail_options[$i] =		$aryA[6];

			if ($delete_vm_after_email[$i] =~ /Y/)
				{$vm  .= "$voicemail_id[$i] => $pass[$i],$fullname[$i],$email[$i],,|delete=yes|tz=$voicemail_timezone[$i]|$voicemail_options[$i]\n";}
			else
				{$vm  .= "$voicemail_id[$i] => $pass[$i],$fullname[$i],$email[$i],,|delete=no|tz=$voicemail_timezone[$i]|$voicemail_options[$i]\n";}

			$i++;
			}
		$sthA->finish();
		}
	##### END generate voicemail accounts for all distinct phones on dedicated voicemail server



	##### BEGIN generate meetme entries for this server
	$mm = "; ViciDial Conferences:\n";

	### Find vicidial_conferences on this server
	$stmtA = "SELECT conf_exten FROM vicidial_conferences where server_ip='$server_ip';";
	$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
	$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
	$sthArowsVC=$sthA->rows;
	$j=0;
	while ($sthArowsVC > $j)
		{
		@aryA = $sthA->fetchrow_array;
		$vc_meetme[$j] =	$aryA[0];
		$j++;
		}
	$sthA->finish();

	$j=0;
	while ($sthArowsVC > $j)
		{
		$mm .= "conf => $vc_meetme[$j]\n";
		$j++;
		}

	$mm .= "\n";
	$mm .= "; Conferences:\n";

	### Find conferences on this server
	$stmtA = "SELECT conf_exten FROM conferences where server_ip='$server_ip';";
	$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
	$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
	$sthArowsC=$sthA->rows;
	$j=0;
	while ($sthArowsC > $j)
		{
		@aryA = $sthA->fetchrow_array;
		$meetme[$j] =	$aryA[0];
		$j++;
		}
	$sthA->finish();

	$j=0;
	while ($sthArowsC > $j)
		{
		$mm .= "conf => $meetme[$j]\n";
		$j++;
		}
	##### END generate meetme entries for this server



	##### BEGIN generate music on hold entries for this server
	$moh='';

	### Find music on hold contexts
	$stmtA = "SELECT moh_id,moh_name,random FROM vicidial_music_on_hold where remove='N' and active='Y' and moh_id NOT IN('astdb','sounds','agi-bin','keys');";
	$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
	$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
	$sthArowsM=$sthA->rows;
	$j=0;
	while ($sthArowsM > $j)
		{
		@aryA = $sthA->fetchrow_array;
		$moh_id[$j] =	$aryA[0];
		$moh_name[$j] =	$aryA[1];
		$random[$j] =	$aryA[2];
		$j++;
		}
	$sthA->finish();

	$j=0;
	while ($sthArowsM > $j)
		{
		$moh  .= "; $moh_name[$j]\n";
		$moh  .= "[$moh_id[$j]]\n";
		$moh  .= "mode=files\n";
		$moh  .= "directory=/var/lib/asterisk/$moh_id[$j]\n";
		if ($random[$j] =~ /Y/) 
			{$moh  .= "random=yes\n";}
		$moh  .= "\n";

		$j++;
		}

	##### END generate music on hold entries for this server

	##### BEGIN gather header lines for voicemail.conf file
	# default path to voicemail configuration file:
	$VMCconf = '/etc/asterisk/voicemail.conf';

	open(vmc, "$VMCconf") || die "can't open $VMCconf: $!\n";
	@vmc = <vmc>;
	close(vmc);
	$i=0;
	$vm_header_content='';
	$boxes=9999999;
	foreach(@vmc)
		{
		$line = $vmc[$i];
		$line =~ s/\n|\r//gi;
		if ($line =~ /\[default\]/)
			{$boxes = $i;}
		if ($i <= $boxes)
			{
			$vm_header_content .= "$line\n";
			}
		$i++;
		}
	##### END  gather header lines for voicemail.conf file

	if ($DB) {print "writing auto-gen conf files\n";}

	open(ext, ">/etc/asterisk/BUILDextensions-vicidial.conf") || die "can't open /etc/asterisk/BUILDextensions-vicidial.conf: $!\n";
	open(iax, ">/etc/asterisk/BUILDiax-vicidial.conf") || die "can't open /etc/asterisk/BUILDiax-vicidial.conf: $!\n";
	open(sip, ">/etc/asterisk/BUILDsip-vicidial.conf") || die "can't open /etc/asterisk/BUILDsip-vicidial.conf: $!\n";
	open(vm, ">/etc/asterisk/BUILDvoicemail-vicidial.conf") || die "can't open /etc/asterisk/BUILDvoicemail-vicidial.conf: $!\n";
	open(moh, ">/etc/asterisk/BUILDmusiconhold-vicidial.conf") || die "can't open /etc/asterisk/BUILDmusiconhold-vicidial.conf: $!\n";
	open(mm, ">/etc/asterisk/BUILDmeetme-vicidial.conf") || die "can't open /etc/asterisk/BUILDmeetme-vicidial.conf: $!\n";

	print ext "; WARNING- THIS FILE IS AUTO-GENERATED BY VICIDIAL, ANY EDITS YOU MAKE WILL BE LOST\n";
	print ext "$ext\n";
	print ext "$call_menu_ext\n";
	print ext "[vicidial-auto]\n";
	print ext 'exten => h,1,DeadAGI(agi://127.0.0.1:4577/call_log--HVcauses--PRI-----NODEBUG-----${HANGUPCAUSE}-----${DIALSTATUS}-----${DIALEDTIME}-----${ANSWEREDTIME})';
	print ext "\n";
	if (length($SScustom_dialplan_entry)>5)
		{print ext "; System Setting Custom Dialplan\n$SScustom_dialplan_entry\n\n";}
	if (length($SERVERcustom_dialplan_entry)>5)
		{print ext "; Server Custom Dialplan\n$SERVERcustom_dialplan_entry\n\n";}
	print ext "$Lext\n";
	print ext "$Vext\n";
	print ext "$Pext\n";
	print ext "\n; END OF FILE\n";

	print iax "; WARNING- THIS FILE IS AUTO-GENERATED BY VICIDIAL, ANY EDITS YOU MAKE WILL BE LOST\n";
	print iax "$iax\n";
	print iax "$Liax\n";
	print iax "$Piax\n";
	print iax "\n; END OF FILE\n";

	print sip "; WARNING- THIS FILE IS AUTO-GENERATED BY VICIDIAL, ANY EDITS YOU MAKE WILL BE LOST\n";
	print sip "$sip\n";
	print sip "$Lsip\n";
	print sip "$Psip\n";
	print sip "\n; END OF FILE\n";

#	print vm "; WARNING- THIS FILE IS AUTO-GENERATED BY VICIDIAL, ANY EDITS YOU MAKE WILL BE LOST\n";
	print vm "$vm_header_content\n";
	print vm "$vm\n";
	print vm "\n; END OF FILE\n";

	print moh "; WARNING- THIS FILE IS AUTO-GENERATED BY VICIDIAL, ANY EDITS YOU MAKE WILL BE LOST\n";
	print moh "$moh\n";
	print moh "\n; END OF FILE\n";

	print mm "; WARNING- THIS FILE IS AUTO-GENERATED BY VICIDIAL, ANY EDITS YOU MAKE WILL BE LOST\n";
	print mm "$mm\n";
	print mm "\n; END OF FILE\n";

	close(ext);
	close(iax);
	close(sip);
	close(vm);
	close(moh);
	close(mm);

	### find cmp binary
	$cmpbin = '';
	if ( -e ('/bin/cmp')) {$cmpbin = '/bin/cmp';}
	else 
		{
		if ( -e ('/usr/bin/cmp')) {$cmpbin = '/usr/bin/cmp';}
		else 
			{
			if ( -e ('/usr/local/bin/cmp')) {$cmpbin = '/usr/local/bin/cmp';}
			else
				{
				print "Can't find cmp binary! Exiting...\n";
				exit;
				}
			}
		}

	if ( !-e ('/etc/asterisk/extensions-vicidial.conf'))
		{`echo -e \"; END OF FILE\n\" > /etc/asterisk/extensions-vicidial.conf`;}

	if ( !-e ('/etc/asterisk/iax-vicidial.conf'))
		{`echo -e \"; END OF FILE\n\" > /etc/asterisk/iax-vicidial.conf`;}

	if ( !-e ('/etc/asterisk/sip-vicidial.conf'))
		{`echo -e \"; END OF FILE\n\" > /etc/asterisk/sip-vicidial.conf`;}

	if ( !-e ('/etc/asterisk/voicemail.conf'))
		{`echo -e \"; END OF FILE\n\" > /etc/asterisk/voicemail.conf`;}

	if ( !-e ('/etc/asterisk/musiconhold-vicidial.conf'))
		{`echo -e \"; END OF FILE\n\" > /etc/asterisk/musiconhold-vicidial.conf`;}

	if ( !-e ('/etc/asterisk/meetme-vicidial.conf'))
		{`echo -e \"; END OF FILE\n\" > /etc/asterisk/meetme-vicidial.conf`;}

	use File::Compare;

	$extCMP = compare("/etc/asterisk/BUILDextensions-vicidial.conf","/etc/asterisk/extensions-vicidial.conf");
	$iaxCMP = compare("/etc/asterisk/BUILDiax-vicidial.conf","/etc/asterisk/iax-vicidial.conf");
	$sipCMP = compare("/etc/asterisk/BUILDsip-vicidial.conf","/etc/asterisk/sip-vicidial.conf");
	$vmCMP =  compare("/etc/asterisk/BUILDvoicemail-vicidial.conf","/etc/asterisk/voicemail.conf");
	$mohCMP = compare("/etc/asterisk/BUILDmusiconhold-vicidial.conf","/etc/asterisk/musiconhold-vicidial.conf");
	$mmCMP =  compare("/etc/asterisk/BUILDmeetme-vicidial.conf","/etc/asterisk/meetme-vicidial.conf");

	sleep(1);

	### reload Asterisk
	if ($DB) {print "reloading asterisk modules:\n";}
	if ($asterisk_version =~ /^1.2/)
		{
		if ($extCMP > 0)
			{
			`cp -f /etc/asterisk/BUILDextensions-vicidial.conf /etc/asterisk/extensions-vicidial.conf`;
			`screen -XS asterisk eval 'stuff "extensions reload\015"'`;
			if ($DB) {print "extensions reload\n";}
			sleep(1);
			}
		if ($sipCMP > 0)
			{
			`cp -f /etc/asterisk/BUILDsip-vicidial.conf /etc/asterisk/sip-vicidial.conf`;
			`screen -XS asterisk eval 'stuff "sip reload\015"'`;
			if ($DB) {print "sip reload\n";}
			sleep(1);
			}
		if ($iaxCMP > 0)
			{
			`cp -f /etc/asterisk/BUILDiax-vicidial.conf /etc/asterisk/iax-vicidial.conf`;
			`screen -XS asterisk eval 'stuff "iax2 reload\015"'`;
			if ($DB) {print "iax reload\n";}
			sleep(1);
			}
		if ($vmCMP > 0)
			{
			`cp -f /etc/asterisk/BUILDvoicemail-vicidial.conf /etc/asterisk/voicemail.conf`;
			`screen -XS asterisk eval 'stuff "reload app_voicemail.so\015"'`;
			if ($DB) {print "reload app_voicemail.so\n";}
			sleep(1);
			}
		if ($mohCMP > 0)
			{
			`cp -f /etc/asterisk/BUILDmusiconhold-vicidial.conf /etc/asterisk/musiconhold-vicidial.conf`;
			`screen -XS asterisk eval 'stuff "moh reload\015"'`;
			if ($DB) {print "moh reload\n";}
			sleep(1);
			}
		if ($mmCMP > 0)
			{
			`cp -f /etc/asterisk/BUILDmeetme-vicidial.conf /etc/asterisk/meetme-vicidial.conf`;
			`screen -XS asterisk eval 'stuff "reload app_meetme.so\015"'`;
			if ($DB) {print "reload app_meetme.so\n";}
			sleep(1);
			}
		}
	else
		{
		if ($extCMP > 0)
			{
			`cp -f /etc/asterisk/BUILDextensions-vicidial.conf /etc/asterisk/extensions-vicidial.conf`;
			`screen -XS asterisk eval 'stuff "dialplan reload\015"'`;
			if ($DB) {print "dialplan reload\n";}
			sleep(1);
			}
		if ($sipCMP > 0)
			{
			`cp -f /etc/asterisk/BUILDsip-vicidial.conf /etc/asterisk/sip-vicidial.conf`;
			`screen -XS asterisk eval 'stuff "sip reload\015"'`;
			if ($DB) {print "sip reload\n";}
			sleep(1);
			}
		if ($iaxCMP > 0)
			{
			`cp -f /etc/asterisk/BUILDiax-vicidial.conf /etc/asterisk/iax-vicidial.conf`;
			`screen -XS asterisk eval 'stuff "iax2 reload\015"'`;
			if ($DB) {print "iax2 reload\n";}
			sleep(1);
			}
		if ($vmCMP > 0)
			{
			`cp -f /etc/asterisk/BUILDvoicemail-vicidial.conf /etc/asterisk/voicemail.conf`;
			`screen -XS asterisk eval 'stuff "module reload app_voicemail.so\015"'`;
			if ($DB) {print "module reload app_voicemail.so\n";}
			sleep(1);
			}
		if ($mohCMP > 0)
			{
			`cp -f /etc/asterisk/BUILDmusiconhold-vicidial.conf /etc/asterisk/musiconhold-vicidial.conf`;
			`screen -XS asterisk eval 'stuff "moh reload\015"'`;
			if ($DB) {print "moh reload\n";}
			sleep(1);
			}
		if ($mmCMP > 0)
			{
			`cp -f /etc/asterisk/BUILDmeetme-vicidial.conf /etc/asterisk/meetme-vicidial.conf`;
			`screen -XS asterisk eval 'stuff "module reload app_meetme.so\015"'`;
			if ($DB) {print "module reload app_meetme.so\n";}
			sleep(1);
			}
		}

	`rm -f /etc/asterisk/BUILDextensions-vicidial.conf`;
	`rm -f /etc/asterisk/BUILDiax-vicidial.conf`;
	`rm -f /etc/asterisk/BUILDsip-vicidial.conf`;
#	`rm -f /etc/asterisk/BUILDvoicemail-vicidial.conf`;
	`rm -f /etc/asterisk/BUILDmusiconhold-vicidial.conf`;
	`rm -f /etc/asterisk/BUILDmeetme-vicidial.conf`;

	}
################################################################################
#####  END Creation of auto-generated conf files
################################################################################








################################################################################
#####  BEGIN  Audio Store sync
################################################################################
$upload_audio = 0;
$upload_flag = '';
$soundsec=0;

if ( ($active_voicemail_server =~ /$server_ip/) && ((length($active_voicemail_server)) eq (length($server_ip))) )
	{
	if (-e "/prompt_count.txt")
		{
		open(test, "/prompt_count.txt") || die "can't open /prompt_count.txt: $!\n";
		@test = <test>;
		close(test);
		chomp($test[0]);
		$test[0] = ($test[0] + 85100000);
		$last_file_gsm = "$test[0].gsm";
		$last_file_wav = "$test[0].wav";

		if (-e "$PATHsounds/$last_file_gsm")
			{
			$sounddate = (-M "$PATHsounds/$last_file_gsm");
			$soundsec =	($sounddate * 86400);
			}
		if (-e "$PATHsounds/$last_file_wav")
			{
			$sounddate = (-M "$PATHsounds/$last_file_wav");
			$soundsec =	($sounddate * 86400);
			}
		if ($DB) {print "age of last audio prompt file: |$sounddate|$soundsec|   ($PATHsounds/$last_file_gsm|$last_file_wav)\n";}
		if ( ($soundsec > 300) && ($soundsec <= 360) )
			{
			$upload_audio = 1;
			$upload_flag = '--upload';
			}
		}
	}

if ( ($active_asterisk_server =~ /Y/) && ( ($sounds_update =~ /Y/) || ($upload_audio > 0) ) )
	{
	if ($sounds_central_control_active > 0)
		{
		if ($DB) {print "running audio store sync process...\n";}
		`/usr/bin/screen -d -m -S AudioStore $PATHhome/ADMIN_audio_store_sync.pl $upload_flag 2>/dev/null 1>&2`;
		}
	}
################################################################################
#####  END  Audio Store sync
################################################################################






################################################################################
#####  BEGIN  process triggers
################################################################################
$stmtA = "SELECT trigger_id,user,trigger_lines FROM vicidial_process_triggers where server_ip='$server_ip' and trigger_run='1' and trigger_time < NOW() order by trigger_time limit 1;";
$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
$sthBrows=$sthA->rows;
if ($sthBrows > 0)
	{
	@aryA = $sthA->fetchrow_array;
	$trigger_id	=		"$aryA[0]";
	$user	=			"$aryA[1]";
	$trigger_lines	=	"$aryA[2]";
	}
$sthA->finish();

if ($sthBrows > 0)
	{
	$stmtA="UPDATE vicidial_process_triggers SET trigger_run='0' where trigger_id='$trigger_id';";
	$affected_rows = $dbhA->do($stmtA);

	$MT[0]='';
	$trigger_results='';
	if ($DB) {print "running process trigger: $trigger_id\n";}
	@triggers = split(/\|/,$trigger_lines);
	$i=0;
	foreach(@triggers)
		{
		$trigger_results .= "$triggers[$i]\n";
		@output=@MT;
		@output = `$triggers[$i]`;
		$m=0;
		foreach(@output) 
			{
			$trigger_results .= "$output[$m]";
			$m++;
			}

		$i++;
		}
	if ($DB) {print "DONE\n";}
	if ($DB) {print "$trigger_results\n";}

	$trigger_lines =~ s/;|\\|\"//gi;
	$trigger_results =~ s/;|\\|\"//gi;

	$stmtA="INSERT INTO vicidial_process_trigger_log SET trigger_id='$trigger_id',user='$user',trigger_time=NOW(),server_ip='$server_ip',trigger_lines='$trigger_lines',trigger_results='$trigger_results';";
	$Iaffected_rows = $dbhA->do($stmtA);
	if ($DB) {print "FINISHED:   $affected_rows|$Iaffected_rows";}

	}
################################################################################
#####  END  process triggers
################################################################################





################################################################################
#####  BEGIN  reset lists
################################################################################
if ($AST_VDadapt > 0)
	{
	$stmtA = "SELECT list_id FROM vicidial_lists where reset_time LIKE \"%$reset_test%\";";
	$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
	$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
	$sthBrows=$sthA->rows;
	$i=0;
	while ($sthBrows > $i)
		{
		@aryA = $sthA->fetchrow_array;
		$list_id[$i] = "$aryA[0]";
		$i++;
		}
	$sthA->finish();

	if ($DBX) {print "RESET LIST:   $i|$reset_test";}

	$i=0;
	while ($sthBrows > $i)
		{
		$stmtA="UPDATE vicidial_list set called_since_last_reset='N' where list_id='$list_id[$i]';";
		$affected_rows = $dbhA->do($stmtA);

		$SQL_log = "$stmtA|";
		$SQL_log =~ s/;|\\|\'|\"//gi;

		if ($DB) {print "DONE\n";}
		if ($DB) {print "$trigger_results\n";}

		$stmtA="INSERT INTO vicidial_admin_log set event_date='$now_date', user='VDAD', ip_address='1.1.1.1', event_section='LISTS', event_type='RESET', record_id='$list_id[$i]', event_code='ADMIN RESET LIST', event_sql=\"$SQL_log\", event_notes='$affected_rows leads reset';";
		$Iaffected_rows = $dbhA->do($stmtA);
		if ($DB) {print "FINISHED:   $affected_rows|$Iaffected_rows|$stmtA";}

		$i++;
		}
	}
################################################################################
#####  END  reset lists
################################################################################







if ($DB) {print "DONE\n";}

exit;



sub leading_zero($) 
{
    $_ = $_[0];
    s/^(\d)$/0$1/;
    s/^(\d\d)$/0$1/;
    return $_;
} # End of the leading_zero() routine.
