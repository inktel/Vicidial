#!/usr/bin/perl

# install.pl version 2.4
#
# Copyright (C) 2011  Matt Florell <vicidial@gmail.com>    LICENSE: AGPLv2
#

# CHANGES
# 71004-1155 - Added FTP and REPORT connection variables
# 71012-1251 - Added PATHDONEmonitor setting
# 71121-1048 - Added -p flag to mkdir to not show errors
# 80107-2341 - Added --build_multiserver_conf flag to generate dynamic multi-server conf sections
# 80115-1426 - Added ip_relay scripts for port forwarding
# 80122-0320 - Added build_phones_conf flag to generate phones conf entries from phones table records
# 80227-1536 - Added ip_relay to keepalive list
# 80316-2208 - Added $PATHlogs/archive for backups
# 80526-1345 - Added Timeclock auto-logout option 9
# 90210-0319 - Added option to prompt for Asterisk version
# 90312-1256 - Added CLI flag for automatic configuration
# 90620-1910 - Added check before creating directories and formatting changes
# 90727-1457 - Added GSW directory creation
# 91105-1359 - Added MIX directory to /var/spool/asterisk/monitor
# 91123-0001 - Added FTP2 directory to /var/spool/asterisk/monitorDONE
# 100428-0936 - Added DB custom user/pass fields
# 101217-0520 - Added PREPROCESS directory
# 110619-2153 - Added languages install and conf file specify options
# 110812-1510 - Added tts sound directories creation
#

############################################
# install.pl - puts server files in the right places and creates conf file
#
# default paths.
#
# default path to astguiclient configuration file:
$defaultPATHconf =		'/etc/astguiclient.conf';
$PATHconf =		$defaultPATHconf;
# default path to home directory:
$PATHhome =		'/usr/share/astguiclient';
# default path to astguiclient logs directory: 
$PATHlogs =		'/var/log/astguiclient';
# default path to asterisk agi-bin directory: 
$PATHagi =		'/var/lib/asterisk/agi-bin';
# default path to web root directory: 
#$PATHweb =		'/var/www/html';
#$PATHweb =		'/home/www/htdocs';
$PATHweb =		'/usr/local/apache2/htdocs';
# default path to asterisk sounds directory: 
$PATHsounds =	'/var/lib/asterisk/sounds';
# default path to asterisk recordings directory: 
$PATHmonitor =	'/var/spool/asterisk/monitor';
# default path to asterisk recordings DONE directory: 
$PATHDONEmonitor =	'/var/spool/asterisk/monitorDONE';
# default database server variables: 
$VARDB_server =	'localhost';
$VARDB_database =	'asterisk';
$VARDB_user =	'cron';
$VARDB_pass =	'1234';
$VARDB_custom_user =	'custom';
$VARDB_custom_pass =	'custom1234';
$VARDB_port =	'3306';
# default keepalive processes: 
$VARactive_keepalives =		'1234568';
# default Asterisk version: 
$VARasterisk_version =		'1.4';
# default recording FTP archive variables:
$VARFTP_host = '10.0.0.4';
$VARFTP_user = 'cron';
$VARFTP_pass = 'test';
$VARFTP_port = '21';
$VARFTP_dir  = 'RECORDINGS';
$VARHTTP_path = 'http://10.0.0.4';
# default report FTP variables:
$VARREPORT_host = '10.0.0.4';
$VARREPORT_user = 'cron';
$VARREPORT_pass = 'test';
$VARREPORT_port = '21';
$VARREPORT_dir  = 'REPORTS';
# defaults for FastAGI Server PreFork
$VARfastagi_log_min_servers =	'3';
$VARfastagi_log_max_servers =	'16';
$VARfastagi_log_min_spare_servers = '2';
$VARfastagi_log_max_spare_servers = '8';
$VARfastagi_log_max_requests =	'1000';
$VARfastagi_log_checkfordead =	'30';
$VARfastagi_log_checkforwait =	'60';

############################################

$CLIhome=0;
$CLIlogs=0;
$CLIagi=0;
$CLIweb=0;
$CLIsounds=0;
$CLImonitor=0;
$CLIserver_ip=0;
$CLIDB_server=0;
$CLIDB_database=0;
$CLIDB_user=0;
$CLIDB_pass=0;
$CLIDB_custom_user=0;
$CLIDB_custom_pass=0;
$CLIDB_port=0;
$CLIVARactive_keepalives=0;
$CLIVARasterisk_version=0;
$CLIFTP_host=0;
$CLIFTP_user=0;
$CLIFTP_pass=0;
$CLIFTP_port=0;
$CLIFTP_dir=0;
$CLIHTTP_path=0;
$CLIREPORT_host=0;
$CLIREPORT_user=0;
$CLIREPORT_pass=0;
$CLIREPORT_port=0;
$CLIREPORT_dir=0;
$CLIVARfastagi_log_min_servers=0;
$CLIVARfastagi_log_max_servers=0;
$CLIVARfastagi_log_min_spare_servers=0;
$CLIVARfastagi_log_max_spare_servers=0;
$CLIVARfastagi_log_max_requests=0;
$CLIVARfastagi_log_checkfordead=0;
$CLIVARfastagi_log_checkforwait=0;

$COPYhome=0;
$COPYlogs=0;
$COPYagi=0;
$COPYweb=0;
$COPYsounds=0;
$COPYmonitor=0;

$secX = time();

# constants
$DB=1;  # Debug flag, set to 0 for no debug messages, lots of output
$US='_';
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
		print "install.pl - installs astGUIclient server files in the proper places, this\n";
		print "script will look for a configuration file for existing settings, and\n";
		print "if not present will prompt for proper information then copy files.\n";
		print "\n";
		print "installation options:\n";
		print "  [--help] = this help screen\n";
		print "  [--test] = test (will not copy files)\n";
		print "  [--debug] = verbose debug messages\n";
		print "  [--no-prompt] = do not ask questions, just install\n";
		print "  [--web-only] = only copy files/directories for web server install\n";
		print "  [--without-web] = do not copy web files/directories\n\n";
		print "configuration options:\n";
		print "  [--conffile=/path/from/root] = define configuration file path from root at runtime\n";
		print "  [--home=/path/from/root] = define home path from root at runtime\n";
		print "  [--logs=/path/from/root] = define logs path from root at runtime\n";
		print "  [--agi=/path/from/root] = define agi-bin path from root at runtime\n";
		print "  [--web=/path/from/root] = define webroot path from root at runtime\n";
		print "  [--sounds=/path/from/root] = define sounds path from root at runtime\n";
		print "  [--monitor=/path/from/root] = define monitor path from root at runtime\n";
		print "  [--DONEmonitor=/path/from/root] = define monitor DONE path from root at runtime\n";
		print "  [--server_ip=192.168.0.1] = define server IP address at runtime\n";
		print "  [--DB_server=localhost] = define database server IP address at runtime\n";
		print "  [--DB_database=asterisk] = define database name at runtime\n";
		print "  [--DB_user=cron] = define database user login at runtime\n";
		print "  [--DB_pass=1234] = define database user password at runtime\n";
		print "  [--DB_custom_user=custom] = define database custom user login at runtime\n";
		print "  [--DB_custom_pass=custom1234] = define database custom user password at runtime\n";
		print "  [--DB_port=3306] = define database connection port at runtime\n";
		print "  [--active_keepalives=123456] = define processes to keepalive\n";
		print "     X - NO KEEPALIVE PROCESSES (use only if you want none to be keepalive)\n";
		print "     1 - AST_update\n";
		print "     2 - AST_send_listen\n";
		print "     3 - AST_VDauto_dial\n";
		print "     4 - AST_VDremote_agents\n";
		print "     5 - AST_VDadapt (If multi-server system, this must only be on one server)\n";
		print "     6 - FastAGI_log\n";
		print "     7 - AST_VDauto_dial_FILL (only for multi-server, this must only be on one server)\n";
		print "     8 - ip_relay (used for blind agent monitoring)\n";
		print "     9 - Timeclock auto-logout\n";
		print "  [--asterisk_version] = set the asterisk version you want to install for\n";
		print "  [--copy_sample_conf_files] = copies the sample conf files to /etc/asterisk/\n";
		print "  [--web-languages] = copy language translations (WARNING! may not work on trunk installs)\n";
		print "  [--FTP_host=192.168.0.2] = define recording archive server IP address at runtime\n";
		print "  [--FTP_user=cron] = define archive server name at runtime\n";
		print "  [--FTP_pass=test] = define archive server user login at runtime\n";
		print "  [--FTP_port=21] = define archive server user password at runtime\n";
		print "  [--FTP_dir=RECORDINGS] = define archive server connection port at runtime\n";
		print "  [--HTTP_path=http://192.168.0.2] = define archive web root at runtime\n";
		print "  [--REPORT_host=192.168.0.2] = define report server IP address at runtime\n";
		print "  [--REPORT_user=cron] = define report server name at runtime\n";
		print "  [--REPORT_pass=test] = define report server user login at runtime\n";
		print "  [--REPORT_port=21] = define report server user password at runtime\n";
		print "  [--REPORT_dir=REPORTS] = define report server connection port at runtime\n";
		print "  [--fastagi_log_min_servers=3] = define FastAGI log min servers\n";
		print "  [--fastagi_log_max_servers=16] = define FastAGI log max servers\n";
		print "  [--fastagi_log_min_spare_servers=2] = define FastAGI log min spare servers\n";
		print "  [--fastagi_log_max_spare_servers=8] = define FastAGI log max spare servers\n";
		print "  [--fastagi_log_max_requests=1000] = define FastAGI log max requests\n";
		print "  [--fastagi_log_checkfordead=30] = define FastAGI log check-for-dead seconds\n";
		print "  [--fastagi_log_checkforwait=60] = define FastAGI log check-for-wait seconds\n";
		print "  [--build_multiserver_conf] = generates conf file examples for extensions.conf and iax.conf\n";
		print "  [--build_phones_conf] = generates conf file examples for extensions.conf, sip.conf and iax.conf\n";
		print "\n";

		exit;
		}
	else
		{
		if ($args =~ /--debug/i) # Debug flag
			{$DB=1;}
		if ($args =~ /--test/i) # test flag
			{$TEST=1;   $T=1;}
		if ($args =~ /--web-only/i) # web-only flag
			{$WEBONLY=1;		}
		if ($args =~ /--without-web/i) # without web flag
			{$NOWEB=1;}
		else
			{$NOWEB=0;}
		if ($args =~ /--no-prompt/i) # do not ask questions
			{$NOPROMPT=1;}
		if ($args =~ /--conffile=/i) # CLI defined conffile path
			{
			@CLIconffileARY = split(/--conffile=/,$args);
			@CLIconffileARX = split(/ /,$CLIconffileARY[1]);
			if (length($CLIconffileARX[0])>2)
				{
				$PATHconf = $CLIconffileARX[0];
				$PATHconf =~ s/\/$| |\r|\n|\t//gi;
				$CLIconffile=1;
				print "  CLI defined conffile path:  $PATHconf\n";
				}
			}
		if ($args =~ /--home=/i) # CLI defined home path
			{
			@CLIhomeARY = split(/--home=/,$args);
			@CLIhomeARX = split(/ /,$CLIhomeARY[1]);
			if (length($CLIhomeARX[0])>2)
				{
				$PATHhome = $CLIhomeARX[0];
				$PATHhome =~ s/\/$| |\r|\n|\t//gi;
				$CLIhome=1;
				print "  CLI defined home path:      $PATHhome\n";
				}
			}
		if ($args =~ /--logs=/i) # CLI defined logs path
			{
			@CLIlogsARY = split(/--logs=/,$args);
			@CLIlogsARX = split(/ /,$CLIlogsARY[1]);
			if (length($CLIlogsARX[0])>2)
				{
				$PATHlogs = $CLIlogsARX[0];
				$PATHlogs =~ s/\/$| |\r|\n|\t//gi;
				$CLIlogs=1;
				print "  CLI defined logs path:      $PATHlogs\n";
				}
			}
		if ($args =~ /--agi=/i) # CLI defined agi-bin path
			{
			@CLIagiARY = split(/--agi=/,$args);
			@CLIagiARX = split(/ /,$CLIagiARY[1]);
			if (length($CLIagiARX[0])>2)
				{
				$PATHagi = $CLIagiARX[0];
				$PATHagi =~ s/\/$| |\r|\n|\t//gi;
				$CLIagi=1;
				print "  CLI defined agi-bin path:   $PATHagi\n";
				}
			}
		if ($args =~ /--web=/i) # CLI defined webroot path
			{
			@CLIwebARY = split(/--web=/,$args);
			@CLIwebARX = split(/ /,$CLIwebARY[1]);
			if (length($CLIwebARX[0])>2)
				{
				$PATHweb = $CLIwebARX[0];
				$PATHweb =~ s/\/$| |\r|\n|\t//gi;
				$CLIweb=1;
				print "  CLI defined webroot path:   $PATHweb\n";
				}
			}
		if ($args =~ /--sounds=/i) # CLI defined sounds path
			{
			@CLIsoundsARY = split(/--sounds=/,$args);
			@CLIsoundsARX = split(/ /,$CLIsoundsARY[1]);
			if (length($CLIsoundsARX[0])>2)
				{
				$PATHsounds = $CLIsoundsARX[0];
				$PATHsounds =~ s/\/$| |\r|\n|\t//gi;
				$CLIsounds=1;
				print "  CLI defined sounds path:    $PATHsounds\n";
				}
			}
		if ($args =~ /--monitor=/i) # CLI defined monitor path
			{
			@CLImonitorARY = split(/--monitor=/,$args);
			@CLImonitorARX = split(/ /,$CLImonitorARY[1]);
			if (length($CLImonitorARX[0])>2)
				{
				$PATHmonitor = $CLImonitorARX[0];
				$PATHmonitor =~ s/\/$| |\r|\n|\t//gi;
				$CLImonitor=1;
				print "  CLI defined monitor path:   $PATHmonitor\n";
				}
			}
		if ($args =~ /--DONEmonitor=/i) # CLI defined DONEmonitor path
			{
			@CLIDONEmonitorARY = split(/--DONEmonitor=/,$args);
			@CLIDONEmonitorARX = split(/ /,$CLIDONEmonitorARY[1]);
			if (length($CLIDONEmonitorARX[0])>2)
				{
				$PATHDONEmonitor = $CLIDONEmonitorARX[0];
				$PATHDONEmonitor =~ s/\/$| |\r|\n|\t//gi;
				$CLIDONEmonitor=1;
				print "  CLI defined DONEmonitor:    $PATHDONEmonitor\n";
				}
			}
		if ($args =~ /--server_ip=/i) # CLI defined server IP address
			{
			@CLIserver_ipARY = split(/--server_ip=/,$args);
			@CLIserver_ipARX = split(/ /,$CLIserver_ipARY[1]);
			if (length($CLIserver_ipARX[0])>2)
				{
				$VARserver_ip = $CLIserver_ipARX[0];
				$VARserver_ip =~ s/\/$| |\r|\n|\t//gi;
				$CLIserver_ip=1;
				print "  CLI defined server IP:      $VARserver_ip\n";
				}
			}
		if ($args =~ /--DB_server=/i) # CLI defined Database server address
			{
			@CLIDB_serverARY = split(/--DB_server=/,$args);
			@CLIDB_serverARX = split(/ /,$CLIDB_serverARY[1]);
			if (length($CLIDB_serverARX[0])>2)
				{
				$VARDB_server = $CLIDB_serverARX[0];
				$VARDB_server =~ s/\/$| |\r|\n|\t//gi;
				$CLIDB_server=1;
				print "  CLI defined DB server:      $VARDB_server\n";
				}
			}
		if ($args =~ /--DB_database=/i) # CLI defined Database name
			{
			@CLIDB_databaseARY = split(/--DB_database=/,$args);
			@CLIDB_databaseARX = split(/ /,$CLIDB_databaseARY[1]);
			if (length($CLIDB_databaseARX[0])>1)
				{
				$VARDB_database = $CLIDB_databaseARX[0];
				$VARDB_database =~ s/ |\r|\n|\t//gi;
				$CLIDB_database=1;
				print "  CLI defined DB database:    $VARDB_database\n";
				}
			}
		if ($args =~ /--DB_user=/i) # CLI defined Database user login
			{
			@CLIDB_userARY = split(/--DB_user=/,$args);
			@CLIDB_userARX = split(/ /,$CLIDB_userARY[1]);
			if (length($CLIDB_userARX[0])>1)
				{
				$VARDB_user = $CLIDB_userARX[0];
				$VARDB_user =~ s/ |\r|\n|\t//gi;
				$CLIDB_user=1;
				print "  CLI defined DB user:        $VARDB_user\n";
				}
			}
		if ($args =~ /--DB_pass=/i) # CLI defined Database user password
			{
			@CLIDB_passARY = split(/--DB_pass=/,$args);
			@CLIDB_passARX = split(/ /,$CLIDB_passARY[1]);
			if (length($CLIDB_passARX[0])>1)
				{
				$VARDB_pass = $CLIDB_passARX[0];
				$VARDB_pass =~ s/ |\r|\n|\t//gi;
				$CLIDB_pass=1;
				print "  CLI defined DB password:    $VARDB_pass\n";
				}
			}
		if ($args =~ /--DB_custom_user=/i) # CLI defined Database custom user login
			{
			@CLIDB_custom_userARY = split(/--DB_custom_user=/,$args);
			@CLIDB_custom_userARX = split(/ /,$CLIDB_custom_userARY[1]);
			if (length($CLIDB_custom_userARX[0])>1)
				{
				$VARDB_custom_user = $CLIDB_custom_userARX[0];
				$VARDB_custom_user =~ s/ |\r|\n|\t//gi;
				$CLIDB_custom_user=1;
				print "  CLI defined DB custom user: $VARDB_custom_user\n";
				}
			}
		if ($args =~ /--DB_custom_pass=/i) # CLI defined Database custom password login
			{
			@CLIDB_custom_passARY = split(/--DB_custom_pass=/,$args);
			@CLIDB_custom_passARX = split(/ /,$CLIDB_custom_passARY[1]);
			if (length($CLIDB_custom_passARX[0])>1)
				{
				$VARDB_custom_pass = $CLIDB_custom_passARX[0];
				$VARDB_custom_pass =~ s/ |\r|\n|\t//gi;
				$CLIDB_custom_pass=1;
				print "  CLI defined DB custom pass: $VARDB_custom_pass\n";
				}
			}
		if ($args =~ /--DB_port=/i) # CLI defined Database connection port
			{
			@CLIDB_portARY = split(/--DB_port=/,$args);
			@CLIDB_portARX = split(/ /,$CLIDB_portARY[1]);
			if (length($CLIDB_portARX[0])>1)
				{
				$VARDB_port = $CLIDB_portARX[0];
				$VARDB_port =~ s/ |\r|\n|\t//gi;
				$CLIDB_port=1;
				print "  CLI defined DB port:        $VARDB_port\n";
				}
			}
		if ($args =~ /--active_keepalives=/i) # CLI defined keepalive processes
			{
			@CLIkeepaliveARY = split(/--active_keepalives=/,$args);
			@CLIkeepaliveARX = split(/ /,$CLIkeepaliveARY[1]);
			if (length($CLIkeepaliveARX[0])>1)
				{
				$VARactive_keepalives = $CLIkeepaliveARX[0];
				$VARactive_keepalives =~ s/ |\r|\n|\t//gi;
				$CLIactive_keepalives=1;
				print "  CLI active keepalive procs: $VARactive_keepalives\n";
				}
			}
		if ($args =~ /--asterisk_version=/i) # CLI defined asterisk version
			{
			@CLIastversionARY = split(/--asterisk_version=/,$args);
			@CLIastversionARX = split(/ /,$CLIastversionARY[1]);
			if (length($CLIastversionARX[0])>1)
				{
				$VARasterisk_version = $CLIastversionARX[0];
				$VARasterisk_version =~ s/ |\r|\n|\t//gi;
				$CLIasterisk_version=1;
				print "  CLI asterisk version: $VARasterisk_version\n";
				}
			}
		if ($args =~ /--FTP_host=/i) # CLI defined archive server address
			{
			@CLIFTP_hostARY = split(/--FTP_host=/,$args);
			@CLIFTP_hostARX = split(/ /,$CLIFTP_hostARY[1]);
			if (length($CLIFTP_hostARX[0])>2)
				{
				$VARFTP_host = $CLIFTP_hostARX[0];
				$VARFTP_host =~ s/\/$| |\r|\n|\t//gi;
				$CLIFTP_host=1;
				print "  CLI defined FTP host:       $VARFTP_host\n";
				}
			}
		if ($args =~ /--FTP_user=/i) # CLI defined archive FTP user
			{
			@CLIFTP_userARY = split(/--FTP_user=/,$args);
			@CLIFTP_userARX = split(/ /,$CLIFTP_userARY[1]);
			if (length($CLIFTP_userARX[0])>2)
				{
				$VARFTP_user = $CLIFTP_userARX[0];
				$VARFTP_user =~ s/\/$| |\r|\n|\t//gi;
				$CLIFTP_user=1;
				print "  CLI defined FTP user:       $VARFTP_user\n";
				}
			}
		if ($args =~ /--FTP_pass=/i) # CLI defined archive FTP pass
			{
			@CLIFTP_passARY = split(/--FTP_pass=/,$args);
			@CLIFTP_passARX = split(/ /,$CLIFTP_passARY[1]);
			if (length($CLIFTP_passARX[0])>2)
				{
				$VARFTP_pass = $CLIFTP_passARX[0];
				$VARFTP_pass =~ s/\/$| |\r|\n|\t//gi;
				$CLIFTP_pass=1;
				print "  CLI defined FTP pass:       $VARFTP_pass\n";
				}
			}
		if ($args =~ /--FTP_port=/i) # CLI defined archive FTP port
			{
			@CLIFTP_portARY = split(/--FTP_port=/,$args);
			@CLIFTP_portARX = split(/ /,$CLIFTP_portARY[1]);
			if (length($CLIFTP_portARX[0])>2)
				{
				$VARFTP_port = $CLIFTP_portARX[0];
				$VARFTP_port =~ s/\/$| |\r|\n|\t//gi;
				$CLIFTP_port=1;
				print "  CLI defined FTP port:       $VARFTP_port\n";
				}
			}
		if ($args =~ /--FTP_dir=/i) # CLI defined archive FTP directory
			{
			@CLIFTP_dirARY = split(/--FTP_dir=/,$args);
			@CLIFTP_dirARX = split(/ /,$CLIFTP_dirARY[1]);
			if (length($CLIFTP_dirARX[0])>2)
				{
				$VARFTP_dir = $CLIFTP_dirARX[0];
				$VARFTP_dir =~ s/\/$| |\r|\n|\t//gi;
				$CLIFTP_dir=1;
				print "  CLI defined FTP dir:        $VARFTP_dir\n";
				}
			}
		if ($args =~ /--HTTP_path=/i) # CLI defined archive HTTP path
			{
			@CLIHTTP_pathARY = split(/--HTTP_path=/,$args);
			@CLIHTTP_pathARX = split(/ /,$CLIHTTP_pathARY[1]);
			if (length($CLIHTTP_pathARX[0])>2)
				{
				$VARHTTP_path = $CLIHTTP_pathARX[0];
				$VARHTTP_path =~ s/\/$| |\r|\n|\t//gi;
				$CLIHTTP_path=1;
				print "  CLI defined HTTP path:      $VARHTTP_path\n";
				}
			}
		if ($args =~ /--REPORT_host=/i) # CLI defined archive server address
			{
			@CLIREPORT_hostARY = split(/--REPORT_host=/,$args);
			@CLIREPORT_hostARX = split(/ /,$CLIREPORT_hostARY[1]);
			if (length($CLIREPORT_hostARX[0])>2)
				{
				$VARREPORT_host = $CLIREPORT_hostARX[0];
				$VARREPORT_host =~ s/\/$| |\r|\n|\t//gi;
				$CLIREPORT_host=1;
				print "  CLI defined REPORT host:    $VARREPORT_host\n";
				}
			}
		if ($args =~ /--REPORT_user=/i) # CLI defined archive REPORT user
			{
			@CLIREPORT_userARY = split(/--REPORT_user=/,$args);
			@CLIREPORT_userARX = split(/ /,$CLIREPORT_userARY[1]);
			if (length($CLIREPORT_userARX[0])>2)
				{
				$VARREPORT_user = $CLIREPORT_userARX[0];
				$VARREPORT_user =~ s/\/$| |\r|\n|\t//gi;
				$CLIREPORT_user=1;
				print "  CLI defined REPORT user:    $VARREPORT_user\n";
				}
			}
		if ($args =~ /--REPORT_pass=/i) # CLI defined archive REPORT pass
			{
			@CLIREPORT_passARY = split(/--REPORT_pass=/,$args);
			@CLIREPORT_passARX = split(/ /,$CLIREPORT_passARY[1]);
			if (length($CLIREPORT_passARX[0])>2)
				{
				$VARREPORT_pass = $CLIREPORT_passARX[0];
				$VARREPORT_pass =~ s/\/$| |\r|\n|\t//gi;
				$CLIREPORT_pass=1;
				print "  CLI defined REPORT pass:    $VARREPORT_pass\n";
				}
			}
		if ($args =~ /--REPORT_port=/i) # CLI defined archive REPORT port
			{
			@CLIREPORT_portARY = split(/--REPORT_port=/,$args);
			@CLIREPORT_portARX = split(/ /,$CLIREPORT_portARY[1]);
			if (length($CLIREPORT_portARX[0])>2)
				{
				$VARREPORT_port = $CLIREPORT_portARX[0];
				$VARREPORT_port =~ s/\/$| |\r|\n|\t//gi;
				$CLIREPORT_port=1;
				print "  CLI defined REPORT port:    $VARREPORT_port\n";
				}
			}
		if ($args =~ /--REPORT_dir=/i) # CLI defined archive REPORT directory
			{
			@CLIREPORT_dirARY = split(/--REPORT_dir=/,$args);
			@CLIREPORT_dirARX = split(/ /,$CLIREPORT_dirARY[1]);
			if (length($CLIREPORT_dirARX[0])>2)
				{
				$VARREPORT_dir = $CLIREPORT_dirARX[0];
				$VARREPORT_dir =~ s/\/$| |\r|\n|\t//gi;
				$CLIREPORT_dir=1;
				print "  CLI defined REPORT dir:     $VARREPORT_dir\n";
				}
			}

		if ($args =~ /--copy_sample_conf_files/i) # CLI defined conf files
			{
			$CLIcopy_conf_files='y';
			print "  CLI copy conf files:        YES\n";
			}
		else
			{
			$CLIcopy_conf_files='n';
			}

		if ($args =~ /--web-languages/i) # web languages flag
			{
			$CLIcopy_web_lang='y';
			print "  CLI copy web lang files:    YES\n";
			}
		else
			{
			$CLIcopy_web_lang='n';
			}

		if ($args =~ /--fastagi_log_min_servers=/i) # CLI defined fastagi min servers
			{
			@CLIDB_minserARY = split(/--fastagi_log_min_servers=/,$args);
			@CLIDB_minserARX = split(/ /,$CLIDB_minserARY[1]);
			if (length($CLIDB_minserARX[0])>1)
				{
				$VARfastagi_log_min_servers = $CLIDB_minserARX[0];
				$VARfastagi_log_min_servers =~ s/ |\r|\n|\t//gi;
				$CLIfastagi_log_min_servers=1;
				print "  CLI defined log min server: $VARfastagi_log_min_servers\n";
				}
			}
		if ($args =~ /--fastagi_log_max_servers=/i) # CLI defined fastagi max servers
			{
			@CLIDB_maxserARY = split(/--fastagi_log_max_servers=/,$args);
			@CLIDB_maxserARX = split(/ /,$CLIDB_maxserARY[1]);
			if (length($CLIDB_maxserARX[0])>1)
				{
				$VARfastagi_log_max_servers = $CLIDB_maxserARX[0];
				$VARfastagi_log_max_servers =~ s/ |\r|\n|\t//gi;
				$CLIfastagi_log_max_servers=1;
				print "  CLI defined log max server: $VARfastagi_log_max_servers\n";
				}
			}
		if ($args =~ /--fastagi_log_min_spare_servers=/i) # CLI defined fastagi min spare servers
			{
			@CLIDB_minspaARY = split(/--fastagi_log_min_spare_servers=/,$args);
			@CLIDB_minspaARX = split(/ /,$CLIDB_minspaARY[1]);
			if (length($CLIDB_minspaARX[0])>1)
				{
				$VARfastagi_log_min_spare_servers = $CLIDB_minspaARX[0];
				$VARfastagi_log_min_spare_servers =~ s/ |\r|\n|\t//gi;
				$CLIfastagi_log_min_spare_servers=1;
				print "  CLI defined log min spare:  $VARfastagi_log_min_spare_servers\n";
				}
			}
		if ($args =~ /--fastagi_log_max_spare_servers=/i) # CLI defined fastagi max spare servers
			{
			@CLIDB_maxspaARY = split(/--fastagi_log_max_spare_servers=/,$args);
			@CLIDB_maxspaARX = split(/ /,$CLIDB_maxspaARY[1]);
			if (length($CLIDB_maxspaARX[0])>1)
				{
				$VARfastagi_log_max_spare_servers = $CLIDB_maxspaARX[0];
				$VARfastagi_log_max_spare_servers =~ s/ |\r|\n|\t//gi;
				$CLIfastagi_log_max_spare_servers=1;
				print "  CLI defined log max spare:  $VARfastagi_log_max_spare_servers\n";
				}
			}
		if ($args =~ /--fastagi_log_max_requests=/i) # CLI defined fastagi max requests
			{
			@CLIDB_maxreqARY = split(/--fastagi_log_max_requests=/,$args);
			@CLIDB_maxreqARX = split(/ /,$CLIDB_maxreqARY[1]);
			if (length($CLIDB_maxreqARX[0])>1)
				{
				$VARfastagi_log_max_requests = $CLIDB_maxreqARX[0];
				$VARfastagi_log_max_requests =~ s/ |\r|\n|\t//gi;
				$CLIfastagi_log_max_requests=1;
				print "  CLI defined log max request:$VARfastagi_log_max_requests\n";
				}
			}
		if ($args =~ /--fastagi_log_checkfordead=/i) # CLI defined fastagi check-for-dead seconds
			{
			@CLIDB_ckdeadARY = split(/--fastagi_log_checkfordead=/,$args);
			@CLIDB_ckdeadARX = split(/ /,$CLIDB_ckdeadARY[1]);
			if (length($CLIDB_ckdeadARX[0])>1)
				{
				$VARfastagi_log_checkfordead = $CLIDB_ckdeadARX[0];
				$VARfastagi_log_checkfordead =~ s/ |\r|\n|\t//gi;
				$CLIfastagi_log_checkfordead=1;
				print "  CLI defined log ckdead sec: $VARfastagi_log_checkfordead\n";
				}
			}
		if ($args =~ /--fastagi_log_checkforwait=/i) # CLI defined fastagi check-for-wait seconds
			{
			@CLIDB_ckwaitARY = split(/--fastagi_log_checkforwait=/,$args);
			@CLIDB_ckwaitARX = split(/ /,$CLIDB_ckwaitARY[1]);
			if (length($CLIDB_ckwaitARX[0])>1)
				{
				$VARfastagi_log_checkforwait = $CLIDB_ckwaitARX[0];
				$VARfastagi_log_checkforwait =~ s/ |\r|\n|\t//gi;
				$CLIfastagi_log_checkforwait=1;
				print "  CLI defined log ckwait sec: $VARfastagi_log_checkforwait\n";
				}
			}

		if ($args =~ /--build_multiserver_conf/i) # CLI defined conf files
			{
			$build_multiserver_conf='y';
			print "  CLI multiserver conf gen:   YES\n";

			# default path to astguiclient configuration file:
		#	$PATHconf =		'/etc/astguiclient.conf';

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
			if (!$VARDB_port) {$VARDB_port='3306';}

			use DBI;	  

			$dbhA = DBI->connect("DBI:mysql:$VARDB_database:$VARDB_server:$VARDB_port", "$VARDB_user", "$VARDB_pass")
			 or die "Couldn't connect to database: " . DBI->errstr;

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

			$ext  = "\nAdd the following lines to your extensions.conf file:\n";
			$ext  .= "TRUNKloop = IAX2/ASTloop:test\@127.0.0.1:40569\n";
			$ext  .= "TRUNKblind = IAX2/ASTblind:test\@127.0.0.1:41569\n";

			$iax  = "\nAdd the following lines to your iax.conf file:\n";
			$iax  .= "register => ASTloop:test\@127.0.0.1:40569\n";
			$iax  .= "register => ASTblind:test\@127.0.0.1:41569\n";

			$Lext  = "\n";
			$Lext .= "; Local Server: $server_ip\n";
			$Lext .= "exten => _$VARremDIALstr*.,1,Goto(default,\${EXTEN:16},1)\n";
			$Lext .= "exten => _8600XXX*.,1,AGI(agi-VDADfixCXFER.agi)\n";
			$Lext .= "exten => _78600XXX*.,1,AGI(agi-VDADfixCXFER.agi)\n";
			$Lext .= "; Local blind monitoring\n";
			$Lext .= "exten => _08600XXX,1,Dial(\${TRUNKblind}/6\${EXTEN:1},55,o)\n";

			$Liax .= "\n";
			$Liax .= "[ASTloop]\n";
			$Liax .= "type=friend\n";
			$Liax .= "accountcode=IAXASTloop\n";
			$Liax .= "context=default\n";
			$Liax .= "auth=plaintext\n";
			$Liax .= "host=dynamic\n";
			$Liax .= "permit=0.0.0.0/0.0.0.0\n";
			$Liax .= "secret=test\n";
			$Liax .= "disallow=all\n";
			$Liax .= "allow=ulaw\n";
			$Liax .= "qualify=yes\n";

			$Liax .= "\n";
			$Liax .= "[ASTblind]\n";
			$Liax .= "type=friend\n";
			$Liax .= "accountcode=IAXASTblind\n";
			$Liax .= "context=default\n";
			$Liax .= "auth=plaintext\n";
			$Liax .= "host=dynamic\n";
			$Liax .= "permit=0.0.0.0/0.0.0.0\n";
			$Liax .= "secret=test\n";
			$Liax .= "disallow=all\n";
			$Liax .= "allow=ulaw\n";
			$Liax .= "qualify=yes\n";

			##### Get the server_id for this server's server_ip #####
			$stmtA = "SELECT server_id FROM servers where server_ip='$server_ip';";
			print "$stmtA\n";
			$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
			$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
			$sthArows=$sthA->rows;
			if ($sthArows > 0)
				{
				@aryA = $sthA->fetchrow_array;
				$server_id	=	"$aryA[0]";
				$i++;
				}
			$sthA->finish();

			##### Get the server_ips and server_ids of all VICIDIAL servers on the network #####
			$stmtA = "SELECT server_ip,server_id FROM servers where server_ip!='$server_ip';";
			print "$stmtA\n";
			$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
			$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
			$sthArows=$sthA->rows;
			$i=0;
			while ($sthArows > $i)
				{
				@aryA = $sthA->fetchrow_array;
				$server_ip[$i]	=	"$aryA[0]";
				$server_id[$i]	=	"$aryA[1]";

				if( $server_ip[$i] =~ m/(\S+)\.(\S+)\.(\S+)\.(\S+)/ )
					{
					$a = leading_zero($1); 
					$b = leading_zero($2); 
					$c = leading_zero($3); 
					$d = leading_zero($4);
					$VARremDIALstr = "$a$S$b$S$c$S$d";
					}
				$ext  .= "TRUNK$server_id[$i] = IAX2/$server_id:test\@$server_ip[$i]:4569\n";

				$iax  .= "register => $server_id:test\@$server_ip[$i]:4569\n";

				$Lext .= "; Remote Server VDAD extens: $server_id[$i] $server_ip[$i]\n";
				$Lext .= "exten => _$VARremDIALstr*.,1,Dial(\${TRUNK$server_id[$i]}/\${EXTEN:16},55,o)\n";

				$Liax .= "\n";
				$Liax .= "[$server_id[$i]]\n";
				$Liax .= "type=friend\n";
				$Liax .= "accountcode=IAX$server_id[$i]\n";
				$Liax .= "context=default\n";
				$Liax .= "auth=plaintext\n";
				$Liax .= "host=dynamic\n";
				$Liax .= "permit=0.0.0.0/0.0.0.0\n";
				$Liax .= "secret=test\n";
				$Liax .= "disallow=all\n";
				$Liax .= "allow=ulaw\n";
				$Liax .= "qualify=yes\n";

				$i++;
				}
			$sthA->finish();


			print "$ext$Lext\n$iax$Liax\n";
			exit;
			}


		if ($args =~ /--build_phones_conf/i) # CLI defined conf files
			{
			$build_phones_conf='y';
			print "  CLI phones conf gen:        YES\n";

			# default path to astguiclient configuration file:
		#	$PATHconf =		'/etc/astguiclient.conf';

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
			if (!$VARDB_port) {$VARDB_port='3306';}

			use DBI;	  

			$dbhA = DBI->connect("DBI:mysql:$VARDB_database:$VARDB_server:$VARDB_port", "$VARDB_user", "$VARDB_pass")
			 or die "Couldn't connect to database: " . DBI->errstr;

			$ext  = "\nAdd the following lines to your extensions.conf file:\n";

			$sip  = "\nAdd the following lines to your sip.conf file:\n";

			$iax  = "\nAdd the following lines to your iax.conf file:\n";

			$vm  = "\nAdd the following lines to your voicemail.conf file:\n";

			##### Get the SIP phone entries #####
			$stmtA = "SELECT extension,dialplan_number,voicemail_id,pass FROM phones where server_ip='$server_ip' and protocol='SIP';";
				print "$stmtA\n";
			$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
			$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
			$sthArows=$sthA->rows;
			$i=0;
			while ($sthArows > $i)
				{
				@aryA = $sthA->fetchrow_array;
				$extension[$i] =	"$aryA[0]";
				$dialplan[$i] =		"$aryA[1]";
				$voicemail[$i] =	"$aryA[2]";
				$pass[$i] =			"$aryA[3]";

				$ext .= "exten => $dialplan[$i],1,Dial(SIP/$extension[$i])\n";
				$ext .= "exten => $dialplan[$i],2,Voicemail,u$voicemail[$i]\n";

				$sip .= "\[$extension[$i]\]\n";
				$sip .= "disallow=all\n";
				$sip .= "allow=ulaw\n";
				$sip .= "type=friend\n";
				$sip .= "username=$extension[$i]\n";
				$sip .= "secret=$pass[$i]\n";
				$sip .= "host=dynamic\n";
				$sip .= "dtmfmode=rfc2833\n";
				$sip .= "qualify=1000\n";
				$sip .= "mailbox=$voicemail[$i]\n\n";

				$vm  .= "$voicemail[$i] => $voicemail[$i],$extension[$i] Mailbox\n";

				$i++;
				}
			$sthA->finish();

			##### Get the IAX phone entries #####
			$stmtA = "SELECT extension,dialplan_number,voicemail_id,pass FROM phones where server_ip='$server_ip' and protocol='IAX2';";
				print "$stmtA\n";
			$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
			$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
			$sthArows=$sthA->rows;
			$i=0;
			while ($sthArows > $i)
				{
				@aryA = $sthA->fetchrow_array;
				$extension[$i] =	"$aryA[0]";
				$dialplan[$i] =		"$aryA[1]";
				$voicemail[$i] =	"$aryA[2]";
				$pass[$i] =			"$aryA[3]";

				$ext .= "exten => $dialplan[$i],1,Dial(IAX2/$extension[$i])\n";
				$ext .= "exten => $dialplan[$i],2,Voicemail,u$voicemail[$i]\n";

				$iax .= "\[$extension[$i]\]\n";
				$iax .= "disallow=all\n";
				$iax .= "allow=ulaw\n";
				$iax .= "context=default\n";
				$iax .= "type=friend\n";
				$iax .= "accountcode=$extension[$i]\n";
				$iax .= "secret=$pass[$i]\n";
				$iax .= "auth=md5\n";
				$iax .= "host=dynamic\n";
				$iax .= "permit=0.0.0.0/0.0.0.0\n";
				$iax .= "qualify=1000\n";
				$iax .= "mailbox=$voicemail[$i]\n\n";

				$vm  .= "$voicemail[$i] => $voicemail[$i],$extension[$i] Mailbox\n";

				$i++;
				}
			$sthA->finish();

			print "$ext\n$sip\n$iax\n$vm\n";
			exit;
			}
		}
	}
else
	{
	#	print "no command line options set\n";
	$CLIcopy_conf_files='n';
	}
### end parsing run-time options ###

if (-e "$PATHconf") 
	{
	print "Previous astGUIclient configuration file found at: $PATHconf\n";
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
		if ( ($line =~ /^PATHDONEmonitor/) && ($CLIDONEmonitor < 1) )
			{$PATHDONEmonitor = $line;   $PATHDONEmonitor =~ s/.*=//gi;}
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
		if ( ($line =~ /^VARactive_keepalives/) && ($CLIactive_keepalives < 1) )
			{$VARactive_keepalives = $line;   $VARactive_keepalives =~ s/.*=//gi;}
		if ( ($line =~ /^VARasterisk_version/) && ($CLIasterisk_version < 1) )
			{$VARasterisk_version = $line;   $VARasterisk_version =~ s/.*=//gi;}
		if ( ($line =~ /^VARFTP_host/) && ($CLIFTP_host < 1) )
			{$VARFTP_host = $line;   $VARFTP_host =~ s/.*=//gi;}
		if ( ($line =~ /^VARFTP_user/) && ($CLIFTP_user < 1) )
			{$VARFTP_user = $line;   $VARFTP_user =~ s/.*=//gi;}
		if ( ($line =~ /^VARFTP_pass/) && ($CLIFTP_pass < 1) )
			{$VARFTP_pass = $line;   $VARFTP_pass =~ s/.*=//gi;}
		if ( ($line =~ /^VARFTP_port/) && ($CLIFTP_port < 1) )
			{$VARFTP_port = $line;   $VARFTP_port =~ s/.*=//gi;}
		if ( ($line =~ /^VARFTP_dir/) && ($CLIFTP_dir < 1) )
			{$VARFTP_dir = $line;   $VARFTP_dir =~ s/.*=//gi;}
		if ( ($line =~ /^VARHTTP_path/) && ($CLIHTTP_path < 1) )
			{$VARHTTP_path = $line;   $VARHTTP_path =~ s/.*=//gi;}
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
		if ( ($line =~ /^VARfastagi_log_min_servers/) && ($CLIVARfastagi_log_min_servers < 1) )
			{$VARfastagi_log_min_servers = $line;   $VARfastagi_log_min_servers =~ s/.*=//gi;}
		if ( ($line =~ /^VARfastagi_log_max_servers/) && ($CLIVARfastagi_log_max_servers < 1) )
			{$VARfastagi_log_max_servers = $line;   $VARfastagi_log_max_servers =~ s/.*=//gi;}
		if ( ($line =~ /^VARfastagi_log_min_spare_servers/) && ($CLIVARfastagi_log_min_spare_servers < 1) )
			{$VARfastagi_log_min_spare_servers = $line;   $VARfastagi_log_min_spare_servers =~ s/.*=//gi;}
		if ( ($line =~ /^VARfastagi_log_max_spare_servers/) && ($CLIVARfastagi_log_max_spare_servers < 1) )
			{$VARfastagi_log_max_spare_servers = $line;   $VARfastagi_log_max_spare_servers =~ s/.*=//gi;}
		if ( ($line =~ /^VARfastagi_log_max_requests/) && ($CLIVARfastagi_log_max_requests < 1) )
			{$VARfastagi_log_max_requests = $line;   $VARfastagi_log_max_requests =~ s/.*=//gi;}
		if ( ($line =~ /^VARfastagi_log_checkfordead/) && ($CLIVARfastagi_log_checkfordead < 1) )
			{$VARfastagi_log_checkfordead = $line;   $VARfastagi_log_checkfordead =~ s/.*=//gi;}
		if ( ($line =~ /^VARfastagi_log_checkforwait/) && ($CLIVARfastagi_log_checkforwait < 1) )
			{$VARfastagi_log_checkforwait = $line;   $VARfastagi_log_checkforwait =~ s/.*=//gi;}
		$i++;
		}
	}

if (!$NOPROMPT)
	{
	print("\nWould you like to use manual configuration and installation(y/n): [y] ");
	$manual = <STDIN>;
	chomp($manual);
	}
if ( ($manual =~ /n/i) || ($NOPROMPT > 0) )
	{
	$manual=0;
	}
else
	{
	$config_finished='NO';
	while ($config_finished =~/NO/)
		{
		print "\nSTARTING ASTGUICLIENT MANUAL CONFIGURATION PHASE...\n";
		##### BEGIN astguiclient conf file prompting and existence check #####
		$continue='NO';
		while ($continue =~/NO/)
			{
			print("\nastguiclient configuration file or press enter for default: [$PATHconf] ");
			$PROMPTconf = <STDIN>;
			chomp($PROMPTconf);
			if (length($PROMPTconf)>2)
				{
				$PROMPTconf =~ s/ |\n|\r|\t|\/$//gi;
				$PATHconf=$PROMPTconf;
				$continue='YES';
				}
			else
				{
				$continue='YES';
				}
			}
		if (-e "$PATHconf") 
			{
			print "Previous astGUIclient configuration file found at: $PATHconf\n";
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
				if ( ($line =~ /^PATHDONEmonitor/) && ($CLIDONEmonitor < 1) )
					{$PATHDONEmonitor = $line;   $PATHDONEmonitor =~ s/.*=//gi;}
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
				if ( ($line =~ /^VARactive_keepalives/) && ($CLIactive_keepalives < 1) )
					{$VARactive_keepalives = $line;   $VARactive_keepalives =~ s/.*=//gi;}
				if ( ($line =~ /^VARasterisk_version/) && ($CLIasterisk_version < 1) )
					{$VARasterisk_version = $line;   $VARasterisk_version =~ s/.*=//gi;}
				if ( ($line =~ /^VARFTP_host/) && ($CLIFTP_host < 1) )
					{$VARFTP_host = $line;   $VARFTP_host =~ s/.*=//gi;}
				if ( ($line =~ /^VARFTP_user/) && ($CLIFTP_user < 1) )
					{$VARFTP_user = $line;   $VARFTP_user =~ s/.*=//gi;}
				if ( ($line =~ /^VARFTP_pass/) && ($CLIFTP_pass < 1) )
					{$VARFTP_pass = $line;   $VARFTP_pass =~ s/.*=//gi;}
				if ( ($line =~ /^VARFTP_port/) && ($CLIFTP_port < 1) )
					{$VARFTP_port = $line;   $VARFTP_port =~ s/.*=//gi;}
				if ( ($line =~ /^VARFTP_dir/) && ($CLIFTP_dir < 1) )
					{$VARFTP_dir = $line;   $VARFTP_dir =~ s/.*=//gi;}
				if ( ($line =~ /^VARHTTP_path/) && ($CLIHTTP_path < 1) )
					{$VARHTTP_path = $line;   $VARHTTP_path =~ s/.*=//gi;}
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
				if ( ($line =~ /^VARfastagi_log_min_servers/) && ($CLIVARfastagi_log_min_servers < 1) )
					{$VARfastagi_log_min_servers = $line;   $VARfastagi_log_min_servers =~ s/.*=//gi;}
				if ( ($line =~ /^VARfastagi_log_max_servers/) && ($CLIVARfastagi_log_max_servers < 1) )
					{$VARfastagi_log_max_servers = $line;   $VARfastagi_log_max_servers =~ s/.*=//gi;}
				if ( ($line =~ /^VARfastagi_log_min_spare_servers/) && ($CLIVARfastagi_log_min_spare_servers < 1) )
					{$VARfastagi_log_min_spare_servers = $line;   $VARfastagi_log_min_spare_servers =~ s/.*=//gi;}
				if ( ($line =~ /^VARfastagi_log_max_spare_servers/) && ($CLIVARfastagi_log_max_spare_servers < 1) )
					{$VARfastagi_log_max_spare_servers = $line;   $VARfastagi_log_max_spare_servers =~ s/.*=//gi;}
				if ( ($line =~ /^VARfastagi_log_max_requests/) && ($CLIVARfastagi_log_max_requests < 1) )
					{$VARfastagi_log_max_requests = $line;   $VARfastagi_log_max_requests =~ s/.*=//gi;}
				if ( ($line =~ /^VARfastagi_log_checkfordead/) && ($CLIVARfastagi_log_checkfordead < 1) )
					{$VARfastagi_log_checkfordead = $line;   $VARfastagi_log_checkfordead =~ s/.*=//gi;}
				if ( ($line =~ /^VARfastagi_log_checkforwait/) && ($CLIVARfastagi_log_checkforwait < 1) )
					{$VARfastagi_log_checkforwait = $line;   $VARfastagi_log_checkforwait =~ s/.*=//gi;}
				$i++;
				}
			}
		##### END astguiclient conf file prompting and existence check #####


		##### BEGIN astguiclient home directory prompting and existence check #####
		$continue='NO';
		while ($continue =~/NO/)
			{
			print("\nastguiclient home path or press enter for default: [$PATHhome] ");
			$PROMPThome = <STDIN>;
			chomp($PROMPThome);
			if (length($PROMPThome)>2)
				{
				$PROMPThome =~ s/ |\n|\r|\t|\/$//gi;
				if (!-e "$PROMPThome")
					{
					print("$PROMPThome does not exist, would you like me to create it?(y/n) [y] ");
					$createPROMPThome = <STDIN>;
					chomp($createPROMPThome);
					if ($createPROMPThome =~ /n/i)
						{
						$continue='NO';
						}
					else
						{
						`mkdir -p $PROMPThome`;
						print "     $PROMPThome directory created\n";
						$PATHhome=$PROMPThome;
						$continue='YES';
						}
					}
				else
					{
					$PATHhome=$PROMPThome;
					$continue='YES';
					}
				}
			else
				{
				if (!-e "$PATHhome")
					{
					print("$PATHhome does not exist, would you like me to create it?(y/n) [y] ");
					$createPATHhome = <STDIN>;
					chomp($createPATHhome);
					if ($createPATHhome =~ /n/i)
						{
						$continue='NO';
						}
					else
						{
						`mkdir -p $PATHhome`;
						print "     $PATHhome directory created\n";
						$continue='YES';
						}
					}
				else
					{
					$continue='YES';
					}
				}
			}
		##### END astguiclient home directory prompting and existence check #####

		##### BEGIN astguiclient logs directory prompting and existence check #####
		$continue='NO';
		while ($continue =~/NO/)
			{
			print("\nastguiclient logs path or press enter for default: [$PATHlogs] ");
			$PROMPTlogs = <STDIN>;
			chomp($PROMPTlogs);
			if (length($PROMPTlogs)>2)
				{
				$PROMPTlogs =~ s/ |\n|\r|\t|\/$//gi;
				if (!-e "$PROMPTlogs")
					{
					print("$PROMPTlogs does not exist, would you like me to create it?(y/n) [y] ");
					$createPROMPTlogs = <STDIN>;
					chomp($createPROMPTlogs);
					if ($createPROMPTlogs =~ /n/i)
						{
						$continue='NO';
						}
					else
						{
						`mkdir -p $PROMPTlogs`;
						print "     $PROMPTlogs directory created\n";
						$PATHlogs=$PROMPTlogs;
						$continue='YES';
						}
					}
				else
					{
					$PATHlogs=$PROMPTlogs;
					$continue='YES';
					}
				}
			else
				{
				if (!-e "$PATHlogs")
					{
					print("$PATHlogs does not exist, would you like me to create it?(y/n) [y] ");
					$createPATHlogs = <STDIN>;
					chomp($createPATHlogs);
					if ($createPATHlogs =~ /n/i)
						{
						$continue='NO';
						}
					else
						{
						`mkdir -p --mode=0666 $PATHlogs`;
						print "     $PATHlogs directory created\n";
						$continue='YES';
						}
					}
				else
					{
					$continue='YES';
					}
				}
			}
		##### END astguiclient logs directory prompting and existence check #####

		##### BEGIN asterisk agi-bin directory prompting and existence check #####
		$continue='NO';
		while ($continue =~/NO/)
			{
			print("\nasterisk agi-bin path or press enter for default: [$PATHagi] ");
			$PROMPTagi = <STDIN>;
			chomp($PROMPTagi);
			if (length($PROMPTagi)>2)
				{
				$PROMPTagi =~ s/ |\n|\r|\t|\/$//gi;
				if (!-e "$PROMPTagi")
					{
					print("$PROMPTagi does not exist, would you like me to create it?(y/n) [y] ");
					$createPROMPTagi = <STDIN>;
					chomp($createPROMPTagi);
					if ($createPROMPTagi =~ /n/i)
						{
						$continue='NO';
						}
					else
						{
						`mkdir -p $PROMPTagi`;
						print "     $PROMPTagi directory created\n";
						$PATHagi=$PROMPTagi;
						$continue='YES';
						}
					}
				else
					{
					$PATHagi=$PROMPTagi;
					$continue='YES';
					}
				}
			else
				{
				if (!-e "$PATHagi")
					{
					print("$PATHagi does not exist, would you like me to create it?(y/n) [y] ");
					$createPATHagi = <STDIN>;
					chomp($createPATHagi);
					if ($createPATHagi =~ /n/i)
						{
						$continue='NO';
						}
					else
						{
						`mkdir -p $PATHagi`;
						print "     $PATHagi directory created\n";
						$continue='YES';
						}
					}
				else
					{
					$continue='YES';
					}
				}
			}
		##### END asterisk agi-bin directory prompting and existence check #####

		##### BEGIN server webroot directory prompting and existence check #####
		$continue='NO';
		while ( ($continue =~/NO/) && ($NOWEB < 1) )
			{
			print("\nserver webroot path or press enter for default: [$PATHweb] ");
			$PROMPTweb = <STDIN>;
			chomp($PROMPTweb);
			if (length($PROMPTweb)>2)
				{
				$PROMPTweb =~ s/ |\n|\r|\t|\/$//gi;
				if (!-e "$PROMPTweb")
					{
					print("$PROMPTweb does not exist, would you like me to create it?(y/n) [y] ");
					$createPROMPTweb = <STDIN>;
					chomp($createPROMPTweb);
					if ($createPROMPTweb =~ /n/i)
						{
						$continue='NO';
						}
					else
						{
						`mkdir -p $PROMPTweb`;
						print "     $PROMPTweb directory created\n";
						$PATHweb=$PROMPTweb;
						$continue='YES';
						}
					}
				else
					{
					$PATHweb=$PROMPTweb;
					$continue='YES';
					}
				}
			else
				{
				if (!-e "$PATHweb")
					{
					print("$PATHweb does not exist, would you like me to create it?(y/n) [y] ");
					$createPATHweb = <STDIN>;
					chomp($createPATHweb);
					if ($createPATHweb =~ /n/i)
						{
						$continue='NO';
						}
					else
						{
						`mkdir -p $PATHweb`;
						print "     $PATHweb directory created\n";
						$continue='YES';
						}
					}
				else
					{
					$continue='YES';
					}
				}
			}
		##### END server webroot directory prompting and existence check #####

		##### BEGIN asterisk sounds directory prompting and existence check #####
		$continue='NO';
		while ($continue =~/NO/)
			{
			print("\nasterisk sounds path or press enter for default: [$PATHsounds] ");
			$PROMPTsounds = <STDIN>;
			chomp($PROMPTsounds);
			if (length($PROMPTsounds)>2)
				{
				$PROMPTsounds =~ s/ |\n|\r|\t|\/$//gi;
				if (!-e "$PROMPTsounds")
					{
					print("$PROMPTsounds does not exist, would you like me to create it?(y/n) [y] ");
					$createPROMPTsounds = <STDIN>;
					chomp($createPROMPTsounds);
					if ($createPROMPTsounds =~ /n/i)
						{
						$continue='NO';
						}
					else
						{
						`mkdir -p $PROMPTsounds`;
						print "     $PROMPTsounds directory created\n";
						$PATHsounds=$PROMPTsounds;
						$continue='YES';
						}
					}
				else
					{
					$PATHsounds=$PROMPTsounds;
					$continue='YES';
					}
				}
			else
				{
				if (!-e "$PATHsounds")
					{
					print("$PATHsounds does not exist, would you like me to create it?(y/n) [y] ");
					$createPATHsounds = <STDIN>;
					chomp($createPATHsounds);
					if ($createPATHsounds =~ /n/i)
						{
						$continue='NO';
						}
					else
						{
						`mkdir -p $PATHsounds`;
						print "     $PATHsounds directory created\n";
						$continue='YES';
						}
					}
				else
					{
					$continue='YES';
					}
				}
			}
		##### END asterisk sounds directory prompting and existence check #####

		##### BEGIN asterisk monitor directory prompting and existence check #####
		$continue='NO';
		while ($continue =~/NO/)
			{
			print("\nasterisk monitor path or press enter for default: [$PATHmonitor] ");
			$PROMPTmonitor = <STDIN>;
			chomp($PROMPTmonitor);
			if (length($PROMPTmonitor)>2)
				{
				$PROMPTmonitor =~ s/ |\n|\r|\t|\/$//gi;
				if (!-e "$PROMPTmonitor")
					{
					print("$PROMPTmonitor does not exist, would you like me to create it?(y/n) [y] ");
					$createPROMPTmonitor = <STDIN>;
					chomp($createPROMPTmonitor);
					if ($createPROMPTmonitor =~ /n/i)
						{
						$continue='NO';
						}
					else
						{
						`mkdir -p $PROMPTmonitor`;
						print "     $PROMPTmonitor directory created\n";
						$PATHmonitor=$PROMPTmonitor;
						$continue='YES';
						}
					}
				else
					{
					$PATHmonitor=$PROMPTmonitor;
					$continue='YES';
					}
				}
			else
				{
				if (!-e "$PATHmonitor")
					{
					print("$PATHmonitor does not exist, would you like me to create it?(y/n) [y] ");
					$createPATHmonitor = <STDIN>;
					chomp($createPATHmonitor);
					if ($createPATHmonitor =~ /n/i)
						{
						$continue='NO';
						}
					else
						{
						`mkdir -p $PATHmonitor`;
						print "     $PATHmonitor directory created\n";
						$continue='YES';
						}
					}
				else
					{
					$continue='YES';
					}
				}
			}
		##### END asterisk monitor directory prompting and existence check #####

		##### BEGIN asterisk DONEmonitor directory prompting and existence check #####
		$continue='NO';
		while ($continue =~/NO/)
			{
			print("\nasterisk DONEmonitor path or press enter for default: [$PATHDONEmonitor] ");
			$PROMPTDONEmonitor = <STDIN>;
			chomp($PROMPTDONEmonitor);
			if (length($PROMPTDONEmonitor)>2)
				{
				$PROMPTDONEmonitor =~ s/ |\n|\r|\t|\/$//gi;
				if (!-e "$PROMPTDONEmonitor")
					{
					print("$PROMPTDONEmonitor does not exist, would you like me to create it?(y/n) [y] ");
					$createPROMPTDONEmonitor = <STDIN>;
					chomp($createPROMPTDONEmonitor);
					if ($createPROMPTDONEmonitor =~ /n/i)
						{
						$continue='NO';
						}
					else
						{
						`mkdir -p $PROMPTDONEmonitor`;
						print "     $PROMPTDONEmonitor directory created\n";
						$PATHDONEmonitor=$PROMPTDONEmonitor;
						$continue='YES';
						}
					}
				else
					{
					$PATHDONEmonitor=$PROMPTDONEmonitor;
					$continue='YES';
					}
				}
			else
				{
				if (!-e "$PATHDONEmonitor")
					{
					print("$PATHDONEmonitor does not exist, would you like me to create it?(y/n) [y] ");
					$createPATHDONEmonitor = <STDIN>;
					chomp($createPATHDONEmonitor);
					if ($createPATHDONEmonitor =~ /n/i)
						{
						$continue='NO';
						}
					else
						{
						`mkdir -p $PATHDONEmonitor`;
						print "     $PATHDONEmonitor directory created\n";
						$continue='YES';
						}
					}
				else
					{
					$continue='YES';
					}
				}
			}
		##### END asterisk DONEmonitor directory prompting and existence check #####

		##### BEGIN server_ip prompting and check #####
		if (length($VARserver_ip)<7)
			{	
			### get best guess of IP address from ifconfig output ###
			# inet addr:10.10.11.17  Bcast:10.10.255.255  Mask:255.255.0.0
			@ip = `/sbin/ifconfig`;
			$j=0;
			while($#ip>=$j)
				{
				if ($ip[$j] =~ /inet addr/) {$VARserver_ip = $ip[$j]; $j=1000;}
				$j++;
				}
			$VARserver_ip =~ s/.*addr:| Bcast.*|\r|\n|\t| //gi;
			}

		$continue='NO';
		while ($continue =~/NO/)
			{
			print("\nserver IP address or press enter for default: [$VARserver_ip] ");
			$PROMPTserver_ip = <STDIN>;
			chomp($PROMPTserver_ip);
			if (length($PROMPTserver_ip)>6)
				{
				$PROMPTserver_ip =~ s/ |\n|\r|\t|\/$//gi;
				$VARserver_ip=$PROMPTserver_ip;
				$continue='YES';
				}
			else
				{
				$continue='YES';
				}
			}
		##### END server_ip prompting and check  #####


		##### BEGIN DB_server prompting and check #####
		if (length($VARDB_server)<7)
			{	
			$VARDB_server = 'localhost';
			}
		$continue='NO';
		while ($continue =~/NO/)
			{
			print("\nDB server address or press enter for default: [$VARDB_server] ");
			$PROMPTDB_server = <STDIN>;
			chomp($PROMPTDB_server);
			if (length($PROMPTDB_server)>6)
				{
				$PROMPTDB_server =~ s/ |\n|\r|\t|\/$//gi;
				$VARDB_server=$PROMPTDB_server;
				$continue='YES';
				}
			else
				{
				$continue='YES';
				}
			}
		##### END DB_server prompting and check  #####

		##### BEGIN DB_database prompting and check #####
		$continue='NO';
		while ($continue =~/NO/)
			{
			print("\nDB database name or press enter for default: [$VARDB_database] ");
			$PROMPTDB_database = <STDIN>;
			chomp($PROMPTDB_database);
			if (length($PROMPTDB_database)>1)
				{
				$PROMPTDB_database =~ s/ |\n|\r|\t|\/$//gi;
				$VARDB_database=$PROMPTDB_database;
				$continue='YES';
				}
			else
				{
				$continue='YES';
				}
			}
		##### END DB_database prompting and check  #####

		##### BEGIN DB_user prompting and check #####
		$continue='NO';
		while ($continue =~/NO/)
			{
			print("\nDB user login or press enter for default: [$VARDB_user] ");
			$PROMPTDB_user = <STDIN>;
			chomp($PROMPTDB_user);
			if (length($PROMPTDB_user)>1)
				{
				$PROMPTDB_user =~ s/ |\n|\r|\t|\/$//gi;
				$VARDB_user=$PROMPTDB_user;
				$continue='YES';
				}
			else
				{
				$continue='YES';
				}
			}
		##### END DB_user prompting and check  #####

		##### BEGIN DB_pass prompting and check #####
		$continue='NO';
		while ($continue =~/NO/)
			{
			print("\nDB user password or press enter for default: [$VARDB_pass] ");
			$PROMPTDB_pass = <STDIN>;
			chomp($PROMPTDB_pass);
			if (length($PROMPTDB_pass)>1)
				{
				$PROMPTDB_pass =~ s/ |\n|\r|\t|\/$//gi;
				$VARDB_pass=$PROMPTDB_pass;
				$continue='YES';
				}
			else
				{
				$continue='YES';
				}
			}
		##### END DB_pass prompting and check  #####

		##### BEGIN DB_custom_user prompting and check #####
		$continue='NO';
		while ($continue =~/NO/)
			{
			print("\nDB custom user login or press enter for default: [$VARDB_custom_user] ");
			$PROMPTDB_custom_user = <STDIN>;
			chomp($PROMPTDB_custom_user);
			if (length($PROMPTDB_custom_user)>1)
				{
				$PROMPTDB_custom_user =~ s/ |\n|\r|\t|\/$//gi;
				$VARDB_custom_user=$PROMPTDB_custom_user;
				$continue='YES';
				}
			else
				{
				$continue='YES';
				}
			}
		##### END DB_custom_user prompting and check  #####

		##### BEGIN DB_custom_pass prompting and check #####
		$continue='NO';
		while ($continue =~/NO/)
			{
			print("\nDB custom password login or press enter for default: [$VARDB_custom_pass] ");
			$PROMPTDB_custom_pass = <STDIN>;
			chomp($PROMPTDB_custom_pass);
			if (length($PROMPTDB_custom_pass)>1)
				{
				$PROMPTDB_custom_pass =~ s/ |\n|\r|\t|\/$//gi;
				$VARDB_custom_pass=$PROMPTDB_custom_pass;
				$continue='YES';
				}
			else
				{
				$continue='YES';
				}
			}
		##### END DB_custom_pass prompting and check  #####

		##### BEGIN DB_port prompting and check #####
		$continue='NO';
		while ($continue =~/NO/)
			{
			print("\nDB connection port or press enter for default: [$VARDB_port] ");
			$PROMPTDB_port = <STDIN>;
			chomp($PROMPTDB_port);
			if (length($PROMPTDB_port)>1)
				{
				$PROMPTDB_port =~ s/ |\n|\r|\t|\/$//gi;
				$VARDB_port=$PROMPTDB_port;
				$continue='YES';
				}
			else
				{
				$continue='YES';
				}
			}
		##### END DB_port prompting and check  #####

		##### BEGIN active_keepalives prompting and check #####
		$continue='NO';
		while ($continue =~/NO/)
			{
			print "\nNumeric list of the astGUIclient processes to be kept running\n";
			print "(value should be listing of characters with no spaces: 123456)\n";
			print " X - NO KEEPALIVE PROCESSES (use only if you want none to be keepalive)\n";
			print " 1 - AST_update\n";
			print " 2 - AST_send_listen\n";
			print " 3 - AST_VDauto_dial\n";
			print " 4 - AST_VDremote_agents\n";
			print " 5 - AST_VDadapt (If multi-server system, this must only be on one server)\n";
			print " 6 - FastAGI_log\n";
			print " 7 - AST_VDauto_dial_FILL (only for multi-server, this must only be on one server)\n";
			print " 8 - ip_relay (used for blind agent monitoring)\n";
			print " 9 - Timeclock auto logout\n";
			print "Enter active keepalives or press enter for default: [$VARactive_keepalives] ";
			$PROMPTactive_keepalives = <STDIN>;
			chomp($PROMPTactive_keepalives);
			if (length($PROMPTactive_keepalives)>0)
				{
				$PROMPTactive_keepalives =~ s/ |\n|\r|\t|\/$//gi;
				$VARactive_keepalives=$PROMPTactive_keepalives;
				$continue='YES';
				}
			else
				{
				$continue='YES';
				}
			}
		##### END active_keepalives prompting and check  #####

		##### BEGIN asterisk_version prompting and check #####
		$continue='NO';
		while ($continue =~/NO/)
			{
			print "\nEnter the Asterisk version that you are installing VICIDIAL for\n";
			print "(value should be listing of characters with no spaces: 123456)\n";
			print " 1.2\n";
			print " 1.4\n";
			print "Enter asterisk version or press enter for default: [$VARasterisk_version] ";
			$PROMPTasterisk_version = <STDIN>;
			chomp($PROMPTasterisk_version);
			if (length($PROMPTasterisk_version)>2)
				{
				$PROMPTasterisk_version =~ s/ |\n|\r|\t|\/$//gi;
				$VARasterisk_version=$PROMPTasterisk_version;
				$continue='YES';
				}
			else
				{
				$continue='YES';
				}
			}
		##### END asterisk_version prompting and check  #####

		##### BEGIN copy asterisk sample conf files prompt #####
		$continue='NO';
		while ($continue =~/NO/)
			{
			print("\nCopy sample configuration files to /etc/asterisk/ ? [$CLIcopy_conf_files] ");
			$PROMPTcopy_conf_files = <STDIN>;
			chomp($PROMPTcopy_conf_files);
			if (length($PROMPTcopy_conf_files)<1)
				{$PROMPTcopy_conf_files = $CLIcopy_conf_files;}
			if ($PROMPTcopy_conf_files =~ /y/i)
				{
				if (!-e "/etc/asterisk")
					{
					print("/etc/asterisk does not exist, would you like me to create it?(y/n) [y] ");
					$createPROMPTmonitor = <STDIN>;
					chomp($createPROMPTmonitor);
					if ($createPROMPTmonitor =~ /n/i)
						{
						$continue='NO';
						}
					else
						{
						`mkdir -p /etc/asterisk`;
						print "     /etc/asterisk directory created\n";
						$continue='YES';
						}
					}
				else
					{
					$continue='YES';
					}
				}
			else
				{
				$continue='YES';
				}
			}
		##### END copy asterisk sample conf files prompt #####

		##### BEGIN copy web language translation files prompt #####
		$continue='NO';
		while ($continue =~/NO/)
			{
			print("\nCopy web language translation files to webroot ? [$CLIcopy_web_lang] ");
			$PROMPTcopy_web_lang = <STDIN>;
			chomp($PROMPTcopy_web_lang);
			if (length($PROMPTcopy_web_lang)<1)
				{$PROMPTcopy_web_lang = $CLIcopy_web_lang;}
			if ($PROMPTcopy_web_lang =~ /y/i)
				{
				if (!-e "$PATHweb")
					{
					print("$PATHweb does not exist, would you like me to create it?(y/n) [y] ");
					$createPATHweb = <STDIN>;
					chomp($createPATHweb);
					if ($createPATHweb =~ /n/i)
						{
						$continue='NO';
						}
					else
						{
						`mkdir -p $PATHweb`;
						print "     $PATHweb directory created\n";
						$continue='YES';
						}
					}
				else
					{
					$continue='YES';
					}
				}
			else
				{
				$continue='YES';
				}
			}
		##### END copy web language translation files prompt #####

		##### BEGIN FTP_host prompting and check #####
		if (length($VARFTP_host)<7)
			{	
			$VARFTP_host = 'localhost';
			}
		$continue='NO';
		while ($continue =~/NO/)
			{
			print("\nFTP host address or press enter for default: [$VARFTP_host] ");
			$PROMPTFTP_host = <STDIN>;
			chomp($PROMPTFTP_host);
			if (length($PROMPTFTP_host)>6)
				{
				$PROMPTFTP_host =~ s/ |\n|\r|\t|\/$//gi;
				$VARFTP_host=$PROMPTFTP_host;
				$continue='YES';
				}
			else
				{
				$continue='YES';
				}
			}
		##### END FTP_host prompting and check  #####

		##### BEGIN FTP_user prompting and check #####
		$continue='NO';
		while ($continue =~/NO/)
			{
			print("\nFTP user login or press enter for default: [$VARFTP_user] ");
			$PROMPTFTP_user = <STDIN>;
			chomp($PROMPTFTP_user);
			if (length($PROMPTFTP_user)>1)
				{
				$PROMPTFTP_user =~ s/ |\n|\r|\t|\/$//gi;
				$VARFTP_user=$PROMPTFTP_user;
				$continue='YES';
				}
			else
				{
				$continue='YES';
				}
			}
		##### END FTP_user prompting and check  #####

		##### BEGIN FTP_pass prompting and check #####
		$continue='NO';
		while ($continue =~/NO/)
			{
			print("\nFTP user password or press enter for default: [$VARFTP_pass] ");
			$PROMPTFTP_pass = <STDIN>;
			chomp($PROMPTFTP_pass);
			if (length($PROMPTFTP_pass)>1)
				{
				$PROMPTFTP_pass =~ s/ |\n|\r|\t|\/$//gi;
				$VARFTP_pass=$PROMPTFTP_pass;
				$continue='YES';
				}
			else
				{
				$continue='YES';
				}
			}
		##### END FTP_pass prompting and check  #####

		##### BEGIN FTP_port prompting and check #####
		$continue='NO';
		while ($continue =~/NO/)
			{
			print("\nFTP connection port or press enter for default: [$VARFTP_port] ");
			$PROMPTFTP_port = <STDIN>;
			chomp($PROMPTFTP_port);
			if (length($PROMPTFTP_port)>1)
				{
				$PROMPTFTP_port =~ s/ |\n|\r|\t|\/$//gi;
				$VARFTP_port=$PROMPTFTP_port;
				$continue='YES';
				}
			else
				{
				$continue='YES';
				}
			}
		##### END FTP_port prompting and check  #####

		##### BEGIN FTP_dir prompting and check #####
		$continue='NO';
		while ($continue =~/NO/)
			{
			print("\nFTP directory or press enter for default: [$VARFTP_dir] ");
			$PROMPTFTP_dir = <STDIN>;
			chomp($PROMPTFTP_dir);
			if (length($PROMPTFTP_dir)>1)
				{
				$PROMPTFTP_dir =~ s/ |\n|\r|\t|\/$//gi;
				$VARFTP_dir=$PROMPTFTP_dir;
				$continue='YES';
				}
			else
				{
				$continue='YES';
				}
			}
		##### END FTP_dir prompting and check  #####

		##### BEGIN HTTP_path prompting and check #####
		$continue='NO';
		while ($continue =~/NO/)
			{
			print("\nHTTP path for archive or press enter for default: [$VARHTTP_path] ");
			$PROMPTHTTP_path = <STDIN>;
			chomp($PROMPTHTTP_path);
			if (length($PROMPTHTTP_path)>1)
				{
				$PROMPTHTTP_path =~ s/ |\n|\r|\t|\/$//gi;
				$VARHTTP_path=$PROMPTHTTP_path;
				$continue='YES';
				}
			else
				{
				$continue='YES';
				}
			}
		##### END HTTP_path prompting and check  #####

		##### BEGIN REPORT_host prompting and check #####
		if (length($VARREPORT_host)<7)
			{	
			$VARREPORT_host = 'localhost';
			}
		$continue='NO';
		while ($continue =~/NO/)
			{
			print("\nREPORT host address or press enter for default: [$VARREPORT_host] ");
			$PROMPTREPORT_host = <STDIN>;
			chomp($PROMPTREPORT_host);
			if (length($PROMPTREPORT_host)>6)
				{
				$PROMPTREPORT_host =~ s/ |\n|\r|\t|\/$//gi;
				$VARREPORT_host=$PROMPTREPORT_host;
				$continue='YES';
				}
			else
				{
				$continue='YES';
				}
			}
		##### END REPORT_host prompting and check  #####

		##### BEGIN REPORT_user prompting and check #####
		$continue='NO';
		while ($continue =~/NO/)
			{
			print("\nREPORT user login or press enter for default: [$VARREPORT_user] ");
			$PROMPTREPORT_user = <STDIN>;
			chomp($PROMPTREPORT_user);
			if (length($PROMPTREPORT_user)>1)
				{
				$PROMPTREPORT_user =~ s/ |\n|\r|\t|\/$//gi;
				$VARREPORT_user=$PROMPTREPORT_user;
				$continue='YES';
				}
			else
				{
				$continue='YES';
				}
			}
		##### END REPORT_user prompting and check  #####

		##### BEGIN REPORT_pass prompting and check #####
		$continue='NO';
		while ($continue =~/NO/)
			{
			print("\nREPORT user password or press enter for default: [$VARREPORT_pass] ");
			$PROMPTREPORT_pass = <STDIN>;
			chomp($PROMPTREPORT_pass);
			if (length($PROMPTREPORT_pass)>1)
				{
				$PROMPTREPORT_pass =~ s/ |\n|\r|\t|\/$//gi;
				$VARREPORT_pass=$PROMPTREPORT_pass;
				$continue='YES';
				}
			else
				{
				$continue='YES';
				}
			}
		##### END REPORT_pass prompting and check  #####

		##### BEGIN REPORT_port prompting and check #####
		$continue='NO';
		while ($continue =~/NO/)
			{
			print("\nREPORT connection port or press enter for default: [$VARREPORT_port] ");
			$PROMPTREPORT_port = <STDIN>;
			chomp($PROMPTREPORT_port);
			if (length($PROMPTREPORT_port)>1)
				{
				$PROMPTREPORT_port =~ s/ |\n|\r|\t|\/$//gi;
				$VARREPORT_port=$PROMPTREPORT_port;
				$continue='YES';
				}
			else
				{
				$continue='YES';
				}
			}
		##### END REPORT_port prompting and check  #####

		##### BEGIN REPORT_dir prompting and check #####
		$continue='NO';
		while ($continue =~/NO/)
			{
			print("\nREPORT directory or press enter for default: [$VARREPORT_dir] ");
			$PROMPTREPORT_dir = <STDIN>;
			chomp($PROMPTREPORT_dir);
			if (length($PROMPTREPORT_dir)>1)
				{
				$PROMPTREPORT_dir =~ s/ |\n|\r|\t|\/$//gi;
				$VARREPORT_dir=$PROMPTREPORT_dir;
				$continue='YES';
				}
			else
				{
				$continue='YES';
				}
			}
		##### END REPORT_dir prompting and check  #####

		##### BEGIN fastagi_log_min_servers prompting and check #####
		$continue='NO';
		while ($continue =~/NO/)
			{
			print("\nFastAGI log minimum child servers: [$VARfastagi_log_min_servers] ");
			$PROMPTfastagi_log_min_servers = <STDIN>;
			chomp($PROMPTfastagi_log_min_servers);
			if (length($PROMPTfastagi_log_min_servers)>0)
				{
				$PROMPTfastagi_log_min_servers =~ s/ |\n|\r|\t|\/$//gi;
				$VARfastagi_log_min_servers=$PROMPTfastagi_log_min_servers;
				$continue='YES';
				}
			else
				{
				$continue='YES';
				}
			}
		##### END fastagi_log_min_servers prompting and check  #####

		##### BEGIN fastagi_log_max_servers prompting and check #####
		$continue='NO';
		while ($continue =~/NO/)
			{
			print("\nFastAGI log maximum child servers: [$VARfastagi_log_max_servers] ");
			$PROMPTfastagi_log_max_servers = <STDIN>;
			chomp($PROMPTfastagi_log_max_servers);
			if (length($PROMPTfastagi_log_max_servers)>0)
				{
				$PROMPTfastagi_log_max_servers =~ s/ |\n|\r|\t|\/$//gi;
				$VARfastagi_log_max_servers=$PROMPTfastagi_log_max_servers;
				$continue='YES';
				}
			else
				{
				$continue='YES';
				}
			}
		##### END fastagi_log_max_servers prompting and check  #####

		##### BEGIN fastagi_log_min_spare_servers prompting and check #####
		$continue='NO';
		while ($continue =~/NO/)
			{
			print("\nFastAGI log minimum spare child servers: [$VARfastagi_log_min_spare_servers] ");
			$PROMPTfastagi_log_min_spare_servers = <STDIN>;
			chomp($PROMPTfastagi_log_min_spare_servers);
			if (length($PROMPTfastagi_log_min_spare_servers)>0)
				{
				$PROMPTfastagi_log_min_spare_servers =~ s/ |\n|\r|\t|\/$//gi;
				$VARfastagi_log_min_spare_servers=$PROMPTfastagi_log_min_spare_servers;
				$continue='YES';
				}
			else
				{
				$continue='YES';
				}
			}
		##### END fastagi_log_min_spare_servers prompting and check  #####

		##### BEGIN fastagi_log_max_spare_servers prompting and check #####
		$continue='NO';
		while ($continue =~/NO/)
			{
			print("\nFastAGI log maximum spare child servers: [$VARfastagi_log_max_spare_servers] ");
			$PROMPTfastagi_log_max_spare_servers = <STDIN>;
			chomp($PROMPTfastagi_log_max_spare_servers);
			if (length($PROMPTfastagi_log_max_spare_servers)>0)
				{
				$PROMPTfastagi_log_max_spare_servers =~ s/ |\n|\r|\t|\/$//gi;
				$VARfastagi_log_max_spare_servers=$PROMPTfastagi_log_max_spare_servers;
				$continue='YES';
				}
			else
				{
				$continue='YES';
				}
			}
		##### END fastagi_log_max_spare_servers prompting and check  #####

		##### BEGIN fastagi_log_max_requests prompting and check #####
		$continue='NO';
		while ($continue =~/NO/)
			{
			print("\nFastAGI log maximum requests per child server: [$VARfastagi_log_max_requests] ");
			$PROMPTfastagi_log_max_requests = <STDIN>;
			chomp($PROMPTfastagi_log_max_requests);
			if (length($PROMPTfastagi_log_max_requests)>0)
				{
				$PROMPTfastagi_log_max_requests =~ s/ |\n|\r|\t|\/$//gi;
				$VARfastagi_log_max_requests=$PROMPTfastagi_log_max_requests;
				$continue='YES';
				}
			else
				{
				$continue='YES';
				}
			}
		##### END fastagi_log_max_requests prompting and check  #####

		##### BEGIN fastagi_log_checkfordead prompting and check #####
		$continue='NO';
		while ($continue =~/NO/)
			{
			print("\nFastAGI log check-for-dead seconds: [$VARfastagi_log_checkfordead] ");
			$PROMPTfastagi_log_checkfordead = <STDIN>;
			chomp($PROMPTfastagi_log_checkfordead);
			if (length($PROMPTfastagi_log_checkfordead)>0)
				{
				$PROMPTfastagi_log_checkfordead =~ s/ |\n|\r|\t|\/$//gi;
				$VARfastagi_log_checkfordead=$PROMPTfastagi_log_checkfordead;
				$continue='YES';
				}
			else
				{
				$continue='YES';
				}
			}
		##### END fastagi_log_checkfordead prompting and check  #####

		##### BEGIN fastagi_log_checkforwait prompting and check #####
		$continue='NO';
		while ($continue =~/NO/)
			{
			print("\nFastAGI log check-for-wait seconds: [$VARfastagi_log_checkforwait] ");
			$PROMPTfastagi_log_checkforwait = <STDIN>;
			chomp($PROMPTfastagi_log_checkforwait);
			if (length($PROMPTfastagi_log_checkforwait)>0)
				{
				$PROMPTfastagi_log_checkforwait =~ s/ |\n|\r|\t|\/$//gi;
				$VARfastagi_log_checkforwait=$PROMPTfastagi_log_checkforwait;
				$continue='YES';
				}
			else
				{
				$continue='YES';
				}
			}
		##### END fastagi_log_checkforwait prompting and check  #####


		print "\n";
		print "  defined conf file:        $PATHconf\n";
		print "  defined home path:        $PATHhome\n";
		print "  defined logs path:        $PATHlogs\n";
		print "  defined agi-bin path:     $PATHagi\n";
		print "  defined webroot path:     $PATHweb\n";
		print "  defined sounds path:      $PATHsounds\n";
		print "  defined monitor path:     $PATHmonitor\n";
		print "  defined DONEmonitor path: $PATHDONEmonitor\n";
		print "  defined server_ip:        $VARserver_ip\n";
		print "  defined DB_server:        $VARDB_server\n";
		print "  defined DB_database:      $VARDB_database\n";
		print "  defined DB_user:          $VARDB_user\n";
		print "  defined DB_pass:          $VARDB_pass\n";
		print "  defined DB_custom_user:   $VARDB_custom_user\n";
		print "  defined DB_custom_pass:   $VARDB_custom_pass\n";
		print "  defined DB_port:          $VARDB_port\n";
		print "  defined active_keepalives:     $VARactive_keepalives\n";
		print "  defined asterisk_version:      $VARasterisk_version\n";
		print "  defined copying conf files:    $PROMPTcopy_conf_files\n";
		print "  defined copying weblang files: $PROMPTcopy_web_lang\n";
		print "  defined FTP_host:         $VARFTP_host\n";
		print "  defined FTP_user:         $VARFTP_user\n";
		print "  defined FTP_pass:         $VARFTP_pass\n";
		print "  defined FTP_port:         $VARFTP_port\n";
		print "  defined FTP_dir:          $VARFTP_dir\n";
		print "  defined HTTP_path:        $VARHTTP_path\n";
		print "  defined REPORT_host:      $VARREPORT_host\n";
		print "  defined REPORT_user:      $VARREPORT_user\n";
		print "  defined REPORT_pass:      $VARREPORT_pass\n";
		print "  defined REPORT_port:      $VARREPORT_port\n";
		print "  defined REPORT_dir:       $VARREPORT_dir\n";
		print "  defined fastagi_log_min_servers:       $VARfastagi_log_min_servers\n";
		print "  defined fastagi_log_max_servers:       $VARfastagi_log_max_servers\n";
		print "  defined fastagi_log_min_spare_servers: $VARfastagi_log_min_spare_servers\n";
		print "  defined fastagi_log_max_spare_servers: $VARfastagi_log_max_spare_servers\n";
		print "  defined fastagi_log_max_requests:      $VARfastagi_log_max_requests\n";
		print "  defined fastagi_log_checkfordead:      $VARfastagi_log_checkfordead\n";
		print "  defined fastagi_log_checkforwait:      $VARfastagi_log_checkforwait\n";
		print "\n";

		print("Are these settings correct?(y/n): [y] ");
		$PROMPTconfig = <STDIN>;
		chomp($PROMPTconfig);
		if ( (length($PROMPTconfig)<1) or ($PROMPTconfig =~ /y/i) )
			{
			$config_finished='YES';
			}
		}
	}

print "Writing to configuration file: $PATHconf\n";

open(conf, ">$PATHconf") || die "can't open $PATHconf: $!\n";
print conf "# $PATHconf - configuration elements for the astguiclient/vicidial package\n";
print conf "# this is the astguiclient configuration file \n";
print conf "# all comments will be lost if you run install.pl again\n";
print conf "\n";
print conf "# Paths used by astGUIclient\n";
print conf "PATHhome => $PATHhome\n";
print conf "PATHlogs => $PATHlogs\n";
print conf "PATHagi => $PATHagi\n";
print conf "PATHweb => $PATHweb\n";
print conf "PATHsounds => $PATHsounds\n";
print conf "PATHmonitor => $PATHmonitor\n";
print conf "PATHDONEmonitor => $PATHDONEmonitor\n";
print conf "\n";
print conf "# The IP address of this machine\n";
print conf "VARserver_ip => $VARserver_ip\n";
print conf "\n";
print conf "# Database connection information\n";
print conf "VARDB_server => $VARDB_server\n";
print conf "VARDB_database => $VARDB_database\n";
print conf "VARDB_user => $VARDB_user\n";
print conf "VARDB_pass => $VARDB_pass\n";
print conf "VARDB_custom_user => $VARDB_custom_user\n";
print conf "VARDB_custom_pass => $VARDB_custom_pass\n";
print conf "VARDB_port => $VARDB_port\n";
print conf "\n";
print conf "# Alpha-Numeric list of the astGUIclient processes to be kept running\n";
print conf "# (value should be listing of characters with no spaces: 1234568)\n";
print conf "#  X - NO KEEPALIVE PROCESSES (use only if you want none to be keepalive)\n";
print conf "#  1 - AST_update\n";
print conf "#  2 - AST_send_listen\n";
print conf "#  3 - AST_VDauto_dial\n";
print conf "#  4 - AST_VDremote_agents\n";
print conf "#  5 - AST_VDadapt (If multi-server system, this must only be on one server)\n";
print conf "#  6 - FastAGI_log\n";
print conf "#  7 - AST_VDauto_dial_FILL (only for multi-server, this must only be on one server)\n";
print conf "#  8 - ip_relay (used for blind agent monitoring)\n";
print conf "#  9 - Timeclock auto logout\n";
print conf "VARactive_keepalives => $VARactive_keepalives\n";
print conf "\n";
print conf "# Asterisk version VICIDIAL is installed for\n";
print conf "VARasterisk_version => $VARasterisk_version\n";
print conf "\n";
print conf "# FTP recording archive connection information\n";
print conf "VARFTP_host => $VARFTP_host\n";
print conf "VARFTP_user => $VARFTP_user\n";
print conf "VARFTP_pass => $VARFTP_pass\n";
print conf "VARFTP_port => $VARFTP_port\n";
print conf "VARFTP_dir => $VARFTP_dir\n";
print conf "VARHTTP_path => $VARHTTP_path\n";
print conf "\n";
print conf "# REPORT server connection information\n";
print conf "VARREPORT_host => $VARREPORT_host\n";
print conf "VARREPORT_user => $VARREPORT_user\n";
print conf "VARREPORT_pass => $VARREPORT_pass\n";
print conf "VARREPORT_port => $VARREPORT_port\n";
print conf "VARREPORT_dir => $VARREPORT_dir\n";
print conf "\n";
print conf "# Settings for FastAGI logging server\n";
print conf "VARfastagi_log_min_servers => $VARfastagi_log_min_servers\n";
print conf "VARfastagi_log_max_servers => $VARfastagi_log_max_servers\n";
print conf "VARfastagi_log_min_spare_servers => $VARfastagi_log_min_spare_servers\n";
print conf "VARfastagi_log_max_spare_servers => $VARfastagi_log_max_spare_servers\n";
print conf "VARfastagi_log_max_requests => $VARfastagi_log_max_requests\n";
print conf "VARfastagi_log_checkfordead => $VARfastagi_log_checkfordead\n";
print conf "VARfastagi_log_checkforwait => $VARfastagi_log_checkforwait\n";
close(conf);


print "\nSTARTING ASTGUICLIENT INSTALLATION PHASE...\n";

if ($WEBONLY < 1)
	{
	print "Creating $PATHhome/LEADS_IN directories...\n";
	if (!-e "$PATHhome/libs/Asterisk")	{`mkdir -p $PATHhome/libs/Asterisk`;}
	if (!-e "$PATHhome/LEADS_IN/DONE")
		{
		`mkdir -p $PATHhome/LEADS_IN/DONE`;
		`chmod -R 0766 $PATHhome/LEADS_IN`;
		}
	if (!-e "$PATHhome/PREPROCESS/DONE")
		{
		`mkdir -p $PATHhome/PREPROCESS/DONE`;
		`chmod -R 0766 $PATHhome/PREPROCESS`;
		}
	if (!-e "$PATHhome/VTIGER_IN/DONE")	
		{
		`mkdir -p $PATHhome/VTIGER_IN/DONE`;
		`chmod -R 0766 $PATHhome/VTIGER_IN`;
		}
	if (!-e "$PATHhome/UPDATE_IN/DONE")	
		{
		`mkdir -p $PATHhome/UPDATE_IN/DONE`;
		`chmod -R 0766 $PATHhome/UPDATE_IN`;
		}

	print "Creating $PATHmonitor directories...\n";
	if (!-e "$PATHmonitor")					
		{
		`mkdir -p $PATHmonitor`;
		`chmod -R 0766 $PATHmonitor`;
		}
	if (!-e "$PATHmonitor/MIX")	{`mkdir -p $PATHmonitor/MIX`;}

	if (!-e "$PATHDONEmonitor")					
		{
		`mkdir -p $PATHDONEmonitor`;
		`chmod -R 0766 $PATHDONEmonitor`;
		}
	if (!-e "$PATHDONEmonitor/ORIG")	{`mkdir -p $PATHDONEmonitor/ORIG`;}
	if (!-e "$PATHDONEmonitor/GSM")		{`mkdir -p $PATHDONEmonitor/GSM`;}
	if (!-e "$PATHDONEmonitor/MP3")		{`mkdir -p $PATHDONEmonitor/MP3`;}
	if (!-e "$PATHDONEmonitor/OGG")		{`mkdir -p $PATHDONEmonitor/OGG`;}
	if (!-e "$PATHDONEmonitor/GSW")		{`mkdir -p $PATHDONEmonitor/GSW`;}
	if (!-e "$PATHDONEmonitor/FTP")		{`mkdir -p $PATHDONEmonitor/FTP`;}
	if (!-e "$PATHDONEmonitor/FTP2")	{`mkdir -p $PATHDONEmonitor/FTP2`;}

	print "Creating $PATHlogs/archive directory for backups...\n";
	if (!-e "$PATHlogs/archive")	{`mkdir -p $PATHlogs/archive`;}

	print "Copying bin scripts to $PATHhome ...\n";
	`cp -f ./bin/* $PATHhome/`;

	print "Copying libs to $PATHhome ...\n";
	`cp -f ./libs/* $PATHhome/libs`;
	`cp -f ./extras/Asterisk.pm $PATHhome/libs/`;
	`cp -f ./extras/Asterisk/* $PATHhome/libs/Asterisk/`;

	print "setting cron scripts to executable...\n";
	`chmod 0755 $PATHhome/*`;

	print "Copying extras files to $PATHhome ...\n";
#	`cp -f ./extras/GMT_USA_zip.txt $PATHhome/`;
#	`cp -f ./extras/phone_codes_GMT.txt $PATHhome/`;
	`cp -f ./extras/MySQL_AST_CREATE_tables.sql $PATHhome/`;

	print "Copying agi-bin scripts to $PATHagi ...\n";
	`cp -f ./agi/* $PATHagi/`;

	print "setting agi-bin scripts to executable...\n";
	`chmod 0755 $PATHagi/*`;

	print "Copying sounds to $PATHsounds...\n";
	`cp -fR ./sounds/* $PATHsounds/`;

	print "Creating sound tts directories...\n";
	if (!-e "$PATHsounds/tts/")						{`mkdir -p $PATHsounds/tts/`;}
	if (!-e "$PATHsounds/tts_static/")				{`mkdir -p $PATHsounds/tts_static/`;}

	print "Copying ip_relay scripts to $PATHhome...\n";
	`cp -fR ./extras/ip_relay $PATHhome/`;

	print "Setting ip_relay scripts to executable...\n";
	`chmod 0755 $PATHhome/ip_relay/relay_control`;
	`chmod 0755 $PATHhome/ip_relay/ip_relay_linux_i386`;
	`ln -s $PATHhome/ip_relay/ip_relay_linux_i386 $PATHhome/ip_relay/ip_relay`;
	`ln -s $PATHhome/ip_relay/ip_relay_linux_i386 /usr/bin/ip_relay`;
	`ln -s $PATHhome/ip_relay/ip_relay_linux_i386 /usr/local/bin/ip_relay`;

	print "Starting ip_relay port forwarding for IAX on 40569 and 41569\n";
	`$PATHhome/ip_relay/relay_control start  2>/dev/null 1>&2`;
	}
if ($NOWEB < 1)
	{
	print "Creating $PATHweb web directories...\n";

	if (!-e "$PATHweb/agc/")						{`mkdir -p $PATHweb/agc/`;}
	if (!-e "$PATHweb/vicidial/")					{`mkdir -p $PATHweb/vicidial/`;}
	if (!-e "$PATHweb/vicidial/ploticus/")			{`mkdir -p $PATHweb/vicidial/ploticus/`;}
	if (!-e "$PATHweb/vicidial/agent_reports/")		{`mkdir -p $PATHweb/vicidial/agent_reports/`;}
	if (!-e "$PATHweb/vicidial/server_reports/")	{`mkdir -p $PATHweb/vicidial/server_reports/`;}

	print "Copying web files...\n";
	`cp -f -R ./www/* $PATHweb/`;

	print "setting web scripts to executable...\n";
	`chmod 0777 $PATHweb/`;
	`chmod -R 0755 $PATHweb/agc/`;
	`chmod -R 0755 $PATHweb/vicidial/`;
	`chmod 0777 $PATHweb/agc/`;
	`chmod 0777 $PATHweb/vicidial/`;
	`chmod 0777 $PATHweb/vicidial/ploticus/`;
	`chmod 0777 $PATHweb/vicidial/agent_reports/`;
	`chmod 0777 $PATHweb/vicidial/server_reports/`;
	}

if ( ($PROMPTcopy_web_lang =~ /y/i) || ($CLIcopy_web_lang =~ /y/i) )
	{
	print "Copying web language translation files to $PATHweb...\n";
	if (!-e "$PATHweb/agc_br/")						{`mkdir -p $PATHweb/agc_br/`;}
	if (!-e "$PATHweb/agc_de/")						{`mkdir -p $PATHweb/agc_de/`;}
	if (!-e "$PATHweb/agc_el/")						{`mkdir -p $PATHweb/agc_el/`;}
	if (!-e "$PATHweb/agc_en/")						{`mkdir -p $PATHweb/agc_en/`;}
	if (!-e "$PATHweb/agc_es/")						{`mkdir -p $PATHweb/agc_es/`;}
	if (!-e "$PATHweb/agc_fr/")						{`mkdir -p $PATHweb/agc_fr/`;}
	if (!-e "$PATHweb/agc_it/")						{`mkdir -p $PATHweb/agc_it/`;}
	if (!-e "$PATHweb/agc_nl/")						{`mkdir -p $PATHweb/agc_nl/`;}
	if (!-e "$PATHweb/agc_pl/")						{`mkdir -p $PATHweb/agc_pl/`;}
	if (!-e "$PATHweb/agc_pt/")						{`mkdir -p $PATHweb/agc_pt/`;}
	if (!-e "$PATHweb/agc_ru/")						{`mkdir -p $PATHweb/agc_ru/`;}
	if (!-e "$PATHweb/agc_se/")						{`mkdir -p $PATHweb/agc_se/`;}
	if (!-e "$PATHweb/agc_sk/")						{`mkdir -p $PATHweb/agc_sk/`;}
	if (!-e "$PATHweb/agc_tw/")						{`mkdir -p $PATHweb/agc_tw/`;}
	if (!-e "$PATHweb/vicidial_br/")				{`mkdir -p $PATHweb/vicidial_br/`;}
	if (!-e "$PATHweb/vicidial_de/")				{`mkdir -p $PATHweb/vicidial_de/`;}
	if (!-e "$PATHweb/vicidial_el/")				{`mkdir -p $PATHweb/vicidial_el/`;}
	if (!-e "$PATHweb/vicidial_en/")				{`mkdir -p $PATHweb/vicidial_en/`;}
	if (!-e "$PATHweb/vicidial_es/")				{`mkdir -p $PATHweb/vicidial_es/`;}
	if (!-e "$PATHweb/vicidial_fr/")				{`mkdir -p $PATHweb/vicidial_fr/`;}
	if (!-e "$PATHweb/vicidial_it/")				{`mkdir -p $PATHweb/vicidial_it/`;}

	print "Copying web files...\n";
	`cp -f -R ./LANG_www/* $PATHweb/`;

	print "setting web lang scripts to executable...\n";
	`chmod 0777 $PATHweb/`;
	`chmod -R 0755 $PATHweb/agc_br/`;
	`chmod -R 0755 $PATHweb/agc_de/`;
	`chmod -R 0755 $PATHweb/agc_el/`;
	`chmod -R 0755 $PATHweb/agc_en/`;
	`chmod -R 0755 $PATHweb/agc_es/`;
	`chmod -R 0755 $PATHweb/agc_fr/`;
	`chmod -R 0755 $PATHweb/agc_it/`;
	`chmod -R 0755 $PATHweb/agc_nl/`;
	`chmod -R 0755 $PATHweb/agc_pl/`;
	`chmod -R 0755 $PATHweb/agc_pt/`;
	`chmod -R 0755 $PATHweb/agc_ru/`;
	`chmod -R 0755 $PATHweb/agc_se/`;
	`chmod -R 0755 $PATHweb/agc_sk/`;
	`chmod -R 0755 $PATHweb/agc_tw/`;
	`chmod -R 0755 $PATHweb/vicidial_br/`;
	`chmod -R 0755 $PATHweb/vicidial_de/`;
	`chmod -R 0755 $PATHweb/vicidial_el/`;
	`chmod -R 0755 $PATHweb/vicidial_en/`;
	`chmod -R 0755 $PATHweb/vicidial_es/`;
	`chmod -R 0755 $PATHweb/vicidial_fr/`;
	`chmod -R 0755 $PATHweb/vicidial_it/`;
	`chmod 0777 $PATHweb/agc_br/`;
	`chmod 0777 $PATHweb/agc_de/`;
	`chmod 0777 $PATHweb/agc_el/`;
	`chmod 0777 $PATHweb/agc_en/`;
	`chmod 0777 $PATHweb/agc_es/`;
	`chmod 0777 $PATHweb/agc_fr/`;
	`chmod 0777 $PATHweb/agc_it/`;
	`chmod 0777 $PATHweb/agc_nl/`;
	`chmod 0777 $PATHweb/agc_pl/`;
	`chmod 0777 $PATHweb/agc_pt/`;
	`chmod 0777 $PATHweb/agc_ru/`;
	`chmod 0777 $PATHweb/agc_se/`;
	`chmod 0777 $PATHweb/agc_sk/`;
	`chmod 0777 $PATHweb/agc_tw/`;
	`chmod 0777 $PATHweb/vicidial_br/`;
	`chmod 0777 $PATHweb/vicidial_de/`;
	`chmod 0777 $PATHweb/vicidial_el/`;
	`chmod 0777 $PATHweb/vicidial_en/`;
	`chmod 0777 $PATHweb/vicidial_es/`;
	`chmod 0777 $PATHweb/vicidial_fr/`;
	`chmod 0777 $PATHweb/vicidial_it/`;
	}

if ($PATHconf !~ /\/etc\/astguiclient.conf/)
	{
	print "Using non-default conf file, adjusting hard-coded paths...\n";

	$PATHconfEREG = $PATHconf;
	$PATHconfEREG =~ s/\//\\\//gi;
	$PATHconfDEFAULT = $defaultPATHconf;
	$PATHconfDEFAULT =~ s/\//\\\//gi;
#	print "$PATHconfEREG\n$PATHconfDEFAULT\n";

	`sed -i 's/$PATHconfDEFAULT/$PATHconfEREG/g' $PATHhome/* `;
	`sed -i 's/$PATHconfDEFAULT/$PATHconfEREG/g' $PATHagi/* `;
	`sed -i 's/$PATHconfDEFAULT/$PATHconfEREG/g' $PATHweb/agc/dbconnect.php `;
	`sed -i 's/$PATHconfDEFAULT/$PATHconfEREG/g' $PATHweb/vicidial/dbconnect.php `;
	`sed -i 's/$PATHconfDEFAULT/$PATHconfEREG/g' $PATHweb/vicidial/listloader.pl `;
	`sed -i 's/$PATHconfDEFAULT/$PATHconfEREG/g' $PATHweb/vicidial/listloader_super.pl `;
	`sed -i 's/$PATHconfDEFAULT/$PATHconfEREG/g' $PATHweb/vicidial/listloader_rowdisplay.pl `;
	`sed -i 's/$PATHconfDEFAULT/$PATHconfEREG/g' $PATHweb/vicidial/spreadsheet_sales_viewer.pl `;

	if ( ($PROMPTcopy_web_lang =~ /y/i) || ($CLIcopy_web_lang =~ /y/i) )
		{
		print "Adjusting hard-coded paths in web language translation files...\n";

		`sed -i 's/$PATHconfDEFAULT/$PATHconfEREG/g' $PATHweb/agc_br/dbconnect.php `;
		`sed -i 's/$PATHconfDEFAULT/$PATHconfEREG/g' $PATHweb/agc_de/dbconnect.php `;
		`sed -i 's/$PATHconfDEFAULT/$PATHconfEREG/g' $PATHweb/agc_el/dbconnect.php `;
		`sed -i 's/$PATHconfDEFAULT/$PATHconfEREG/g' $PATHweb/agc_es/dbconnect.php `;
		`sed -i 's/$PATHconfDEFAULT/$PATHconfEREG/g' $PATHweb/agc_fr/dbconnect.php `;
		`sed -i 's/$PATHconfDEFAULT/$PATHconfEREG/g' $PATHweb/agc_it/dbconnect.php `;
		`sed -i 's/$PATHconfDEFAULT/$PATHconfEREG/g' $PATHweb/agc_nl/dbconnect.php `;
		`sed -i 's/$PATHconfDEFAULT/$PATHconfEREG/g' $PATHweb/agc_pl/dbconnect.php `;
		`sed -i 's/$PATHconfDEFAULT/$PATHconfEREG/g' $PATHweb/agc_pt/dbconnect.php `;
		`sed -i 's/$PATHconfDEFAULT/$PATHconfEREG/g' $PATHweb/agc_ru/dbconnect.php `;
		`sed -i 's/$PATHconfDEFAULT/$PATHconfEREG/g' $PATHweb/agc_se/dbconnect.php `;
		`sed -i 's/$PATHconfDEFAULT/$PATHconfEREG/g' $PATHweb/agc_sk/dbconnect.php `;
		`sed -i 's/$PATHconfDEFAULT/$PATHconfEREG/g' $PATHweb/agc_tw/dbconnect.php `;
		`sed -i 's/$PATHconfDEFAULT/$PATHconfEREG/g' $PATHweb/vicidial_br/* `;
		`sed -i 's/$PATHconfDEFAULT/$PATHconfEREG/g' $PATHweb/vicidial_de/* `;
		`sed -i 's/$PATHconfDEFAULT/$PATHconfEREG/g' $PATHweb/vicidial_el/* `;
		`sed -i 's/$PATHconfDEFAULT/$PATHconfEREG/g' $PATHweb/vicidial_es/* `;
		`sed -i 's/$PATHconfDEFAULT/$PATHconfEREG/g' $PATHweb/vicidial_fr/* `;
		`sed -i 's/$PATHconfDEFAULT/$PATHconfEREG/g' $PATHweb/vicidial_it/* `;
		}
	}

if ( ($PROMPTcopy_conf_files =~ /y/i) || ($CLIcopy_conf_files =~ /y/i) )
	{
	print "Copying sample conf files to /etc/asterisk/...\n";
	`mkdir -p /etc/asterisk`;
	if ($VARasterisk_version =~ /^1.2/)
		{
		`cp -f ./docs/conf_examples/extensions.conf.sample /etc/asterisk/extensions.conf`;
		`cp -f ./docs/conf_examples/iax.conf.sample /etc/asterisk/iax.conf`;
		`cp -f ./docs/conf_examples/sip.conf.sample /etc/asterisk/sip.conf`;
		}
	else
		{
		`cp -f ./docs/conf_examples/extensions.conf.sample-1.4 /etc/asterisk/extensions.conf`;
		`cp -f ./docs/conf_examples/iax.conf.sample-1.4 /etc/asterisk/iax.conf`;
		`cp -f ./docs/conf_examples/sip.conf.sample-1.4 /etc/asterisk/sip.conf`;
		}
	`cp -f ./docs/conf_examples/meetme.conf.sample /etc/asterisk/meetme.conf`;
	`cp -f ./docs/conf_examples/manager.conf.sample /etc/asterisk/manager.conf`;
	`cp -f ./docs/conf_examples/musiconhold.conf.sample /etc/asterisk/musiconhold.conf`;
	`cp -f ./docs/conf_examples/voicemail.conf.sample /etc/asterisk/voicemail.conf`;
	`cp -f ./docs/conf_examples/logger.conf.sample /etc/asterisk/logger.conf`;
	`cp -f ./docs/conf_examples/dnsmgr.conf.sample /etc/asterisk/dnsmgr.conf`;
	`cp -f ./docs/conf_examples/features.conf.sample /etc/asterisk/features.conf`;

	print "Creating auto-generated placeholder conf files in /etc/asterisk/...\n";
	`echo "[vicidial-auto] ;placeholder for auto-generated extensions conf" > /etc/asterisk/extensions-vicidial.conf`;
	`echo "[vicidial-auto] ;placeholder for auto-generated iax conf" > /etc/asterisk/iax-vicidial.conf`;
	`echo "[vicidial-auto] ;placeholder for auto-generated sip conf" > /etc/asterisk/sip-vicidial.conf`;
	`echo "[vicidial-auto] ;placeholder for auto-generated voicemail conf" > /etc/asterisk/voicemail-vicidial.conf`;
	}


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

#	print "\nIMPORTANT NOTE!\n";
#	print "\nPlease remember to put these lines in your extensions.conf file:\n";
#	print "exten => _$VARremDIALstr*.,1,Goto(default,\${EXTEN:16},1)\n";
#	print "exten => _8600XXX*.,1,AGI(agi-VDADfixCXFER.agi)\n";
#	print "exten => _78600XXX*.,1,AGI(agi-VDADfixCXFER.agi)\n";

print "\nASTGUICLIENT VICIDIAL INSTALLATION FINISHED!     ENJOY!\n";

$secy = time();		$secz = ($secy - $secX);		$minz = ($secz/60);		# calculate script runtime so far
print "\n     - process runtime      ($secz sec) ($minz minutes)\n";


exit;



sub leading_zero($) 
	{
	$_ = $_[0];
	s/^(\d)$/0$1/;
	s/^(\d\d)$/0$1/;
	return $_;
	} # End of the leading_zero() routine.
