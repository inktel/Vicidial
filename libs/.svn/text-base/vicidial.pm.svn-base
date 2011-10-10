package vicidial;

# vicidial.pm version 2.0.5 
#
# Experimental library for general vicidial things
# 
# This library has not been tested in a production level enviroment.
#
# Copyright (C) 2008  VICIDIAL-GROUP  <info@vicidial-group.com> LICENSE: AGPLv2
#
# CHANGELOG:
# 80311-1611 - First test build
# 80324-1435 - Fixed minor issues and tested all functions.
# 80326-1113 - Moved agi_log to the new viciagi.pm library

# Export our symbols
require Exporter;
@ISA = qw(Exporter);
@EXPORT = qw(get_date_hash_current get_date_hash_value get_now_date load_config_file load_sys_config_db load_server_config_db load_all_servers_config_db);

# USE FLAGS
use 5.008;
use strict;
use warnings;
use Switch;
use DBI;

# function to get a date hash that represents the current time
#    this function does not take any arguments
#    this function returns a reference to a hash with the localtime in it
#      the has has the following values:
#       'sec'	= the seconds
#       'min'	= the minutes
#       'hour'	= the hours
#       'Fhour'	= the hours with a 0 infront if it it less than 10
#       'mday' 	= the day of the month
#       'mon'	= the month of the year
#       'year'	= the year
#       'wday'	= the day of the week
#       'yday'	= the day of the year
#       'isdst'	= if day light savings time is in effect
sub get_date_hash_current {
	return get_date_hash_value(time());
}


# function to get a date hash that represents a time value
#    ARG1 a time value in seconds since epoch
#    this function returns a reference to a hash with the localtime in it
#      the has has the following values:
#       'sec'	= the seconds
#       'min'	= the minutes
#       'hour'	= the hours
#       'Fhour'	= the hours with a 0 infront if it it less than 10
#       'mday' 	= the day of the month
#       'mon'	= the month of the year
#       'year'	= the year
#       'wday'	= the day of the week
#       'yday'	= the day of the year
#       'isdst'	= if day light savings time is in effect
sub get_date_hash_value {
	# the time to compute
	my $time_val = $_[0];

	# init the hash
	my %date_hash = ();

	# assign the values
	(	
		$date_hash{ 'sec' },
	 	$date_hash{ 'min' },
		$date_hash{ 'hour' },
		$date_hash{ 'mday' },
		$date_hash{ 'month' }, 
		$date_hash{ 'year' }, 
		$date_hash{ 'wday' }, 
		$date_hash{ 'yday' }, 
		$date_hash{ 'isdst' } 
	) = localtime( $time_val );

	# make the values more useful
	$date_hash{ 'year' } = ( $date_hash{ 'year' } + 1900 );
	$date_hash{ 'month' }++;
	if ( $date_hash{ 'month' } < 10 )  {
		$date_hash{ 'month' }   = "0$date_hash{ 'month' }";
	}
	if ( $date_hash{ 'mday' } < 10 ) {
		$date_hash{ 'mday' }  = "0$date_hash{ 'mday' }";
	}
	if ( $date_hash{ 'min' } < 10 )  {
		$date_hash{ 'min' }   = "0$date_hash{ 'min' }";
	}
	if ( $date_hash{ 'sec' } < 10 )  {
		$date_hash{ 'sec' }   = "0$date_hash{ 'sec' }";
	}
	if ( $date_hash{ 'hour' } < 10 ) { 
		$date_hash{ 'Fhour' } = "0$date_hash{ 'hour' }"; 
	} else {
		$date_hash{ 'Fhour' } = $date_hash{ 'hour' };
	}

	# return the hash	
	return \%date_hash;
}


# function to convert a date hash to a date string
#    ARG1 is a reference to a date_hash to convert
#    this function returns a reference to a date string
sub get_now_date {
	my $date_hash_ref = $_[0];
	my %date_hash = %$date_hash_ref;

	# build the string
	my $now_date = 
		"$date_hash{ 'year' }-$date_hash{ 'month' }-$date_hash{ 'mday' } $date_hash{ 'Fhour' }:$date_hash{ 'min' }:$date_hash{ 'sec' }";

	return \$now_date;
}

# function to load the astguicleint config file
#    ARG0 the path to the astguiclient config file
#    this function returns a reference to a hash with all of the config file values
#      the has has the following values:
#        'logs_path'		= the path to the log directory
#        'home_path'		= the location of the astguiclient scripts
#        'agi_path'		= the path to the asteirsk agi directory
#        'web_path'		= the path to the apache web directory vicidial is in
#        'sounds_path'		= the path to the asterisk sounds directory
#        'monitor_path'		= the path to where asterisk stores the recordings
#        'done_monitor_path'	= the path to put the recordings after where done compressing them
#        'fagi_log_min_servers'		= minimum number of server threads for the FAGI
#        'fagi_log_max_servers'		= maximum number of server threads for the FAGI
#        'fagi_log_min_spare_servers'	= minimum number of spare server threads for the FAGI
#        'fagi_log_max_spare_servers'	= maximum number of spare server threads for the FAGI
#        'fagi_log_max_requests'	= maximum number of requests a thread should handle before dieing
#        'fagi_log_checkfordead'	= seconds before looking for dead threads to kill
#        'fagi_log_checkforwait'	= seconds before looking if the FAGI can kill some waitng threads
#        'server_ip'		= IP address of the server
#        'active_keepalives'	= which processes should be check to see if they are alive
#        'db_server'		= the IP or Hostname of the database server
#        'db_database'		= the database that vicidial uses
#        'db_user'		= the username that is used to connect to the database
#        'db_pass'		= the password that is used to connect to the database
#        'db_port'		= the port number to connect to the database
#        'ftp_host'		= the IP or Hostname of the ftp server to store the recordings on
#        'ftp_user'		= the username that is used to connect to the recording ftp server
#        'ftp_pass'		= the password that is used to connect to the recording ftp server
#        'ftp_port'		= the port number to connect to the recording ftp server
#        'ftp_dir'		= the directory to place the recording into on the recording ftp server
#        'ftp_http_path'		= the web address to access the recording that are on the recording ftp server
#        'report_host'	= the IP or Hostname of the ftp server to store the reports on
#        'report_user'	= the username that is used to connect to the report ftp server
#        'report_pass'	= the password that is used to connect to the report ftp server
#        'report_port'	= the port number to connect to the report ftp server
#        'report_dir'	= the directory to place the reports into on the report ftp server
sub load_config_file {
	my $file_path = $_[0];

	# init the hash
	my %config_hash = ();

	$config_hash{ 'logs_path' } = "";
	$config_hash{ 'home_path' } = "";
	$config_hash{ 'agi_path' } = "";
	$config_hash{ 'web_path' } = "";
	$config_hash{ 'sounds_path' } = "";
	$config_hash{ 'monitor_path' } = "";
	$config_hash{ 'done_monitor_path' } = "";
	$config_hash{ 'fagi_log_server-port' } = "4577";
	$config_hash{ 'fagi_log_min_servers' } = "";
	$config_hash{ 'fagi_log_max_servers' } = "";
	$config_hash{ 'fagi_log_min_spare_servers' } = "";
	$config_hash{ 'fagi_log_max_spare_servers' } = "";
	$config_hash{ 'fagi_log_max_requests' } = "";
	$config_hash{ 'fagi_log_checkfordead' } = "";
	$config_hash{ 'fagi_log_checkforwait' } = "";
	$config_hash{ 'fagi_in_server_port' } = "4578";
	$config_hash{ 'fagi_in_min_servers' } = "";
	$config_hash{ 'fagi_in_max_servers' } = "";
	$config_hash{ 'fagi_in_min_spare_servers' } = "";
	$config_hash{ 'fagi_in_max_spare_servers' } = "";
	$config_hash{ 'fagi_in_max_requests' } = "";
	$config_hash{ 'fagi_in_checkfordead' } = "";
	$config_hash{ 'fagi_in_checkforwait' } = "";
	$config_hash{ 'fagi_out_server-port' } = "4579";
	$config_hash{ 'fagi_out_min_servers' } = "";
	$config_hash{ 'fagi_out_max_servers' } = "";
	$config_hash{ 'fagi_out_min_spare_servers' } = "";
	$config_hash{ 'fagi_out_max_spare_servers' } = "";
	$config_hash{ 'fagi_out_max_requests' } = "";
	$config_hash{ 'fagi_out_checkfordead' } = "";
	$config_hash{ 'fagi_out_checkforwait' } = "";
	$config_hash{ 'server_ip' } = "";
	$config_hash{ 'active_keepalives' } = "";
	$config_hash{ 'db_server' } = "";
	$config_hash{ 'db_database' } = "";
	$config_hash{ 'db_user' } = "";
	$config_hash{ 'db_pass' } = "";
	$config_hash{ 'db_port' } = "";
	$config_hash{ 'ftp_host' } = "";
	$config_hash{ 'ftp_user' } = "";
	$config_hash{ 'ftp_pass' } = "";
	$config_hash{ 'ftp_port' } = "";
	$config_hash{ 'ftp_dir' } = "";
	$config_hash{ 'ftp_http_path' } = "";
	$config_hash{ 'report_host' } = "";
	$config_hash{ 'report_user' } = "";
	$config_hash{ 'report_pass' } = "";
	$config_hash{ 'report_port' } = "";
	$config_hash{ 'report_dir' } = "";
	

	# open the config file and read it in
	open( CONFIG, "$file_path" ) || die "can't open $file_path: $!\n";
	my @conf = <CONFIG>;
	close(CONFIG);

	# loop through the configs and set the apportiate value in the config_hash
	my $i = 0;
	foreach (@conf) {
		my $line = $conf[$i];
		$line =~ s/ |>|\n|\r|\t|\#.*|;.*//gi;

		switch ( $line ) {
			# Paths to various directories
			case qr/^PATHlogs/ {				
				$config_hash{ 'logs_path' } = $line;
				$config_hash{ 'logs_path' } =~ s/.*=//gi;
			}
			case qr/^PATHhome/ {
				$config_hash{ 'home_path' } = $line;
				$config_hash{ 'home_path' } =~ s/.*=//gi;
			}
			case qr/^PATHagi/ {
				$config_hash{ 'agi_path' } = $line;
				$config_hash{ 'agi_path' } =~ s/.*=//gi;
			}
			case qr/^PATHweb/ {
				$config_hash{ 'web_path' } = $line;
				$config_hash{ 'web_path' } =~ s/.*=//gi;
			}
			case qr/^PATHsounds/ {
				$config_hash{ 'sounds_path' } = $line;
				$config_hash{ 'sounds_path' } =~ s/.*=//gi;
			}
			case qr/^PATHmonitor/ {
				$config_hash{ 'monitor_path' } = $line;
				$config_hash{ 'monitor_path' } =~ s/.*=//gi;
			}
			case qr/^PATHDONEmonitor/ {
				$config_hash{ 'done_monitor_path' } = $line;
				$config_hash{ 'done_monitor_path' } =~ s/.*=//gi;
			}

			# FAGI log server settings
			case qr/^VARfastagi_log_server_port/ {
				$config_hash{ 'fagi_log_server_port' } = $line;
				$config_hash{ 'fagi_log_server_port' } =~ s/.*=//gi;
			}
			case qr/^VARfastagi_log_min_servers/ {
				$config_hash{ 'fagi_log_min_servers' } = $line;
				$config_hash{ 'fagi_log_min_servers' } =~ s/.*=//gi;
			}
			case qr/^VARfastagi_log_max_servers/ {
				$config_hash{ 'fagi_log_max_servers' } = $line;
				$config_hash{ 'fagi_log_max_servers' } =~ s/.*=//gi;
			}
			case qr/^VARfastagi_log_min_spare_servers/ {
				$config_hash{ 'fagi_log_min_spare_servers' } = $line;
				$config_hash{ 'fagi_log_min_spare_servers' } =~ s/.*=//gi;
			}
			case qr/^VARfastagi_log_max_spare_servers/ {
				$config_hash{ 'fagi_log_max_spare_servers' } = $line;
				$config_hash{ 'fagi_log_max_spare_servers' } =~ s/.*=//gi;
			}
			case qr/^VARfastagi_log_max_requests/ {
				$config_hash{ 'fagi_log_max_requests' } = $line;
				$config_hash{ 'fagi_log_max_requests' } =~ s/.*=//gi;
			}
			case qr/^VARfastagi_log_checkfordead/ {
				$config_hash{ 'fagi_log_checkfordead' } = $line;
				$config_hash{ 'fagi_log_checkfordead' } =~ s/.*=//gi;
			}
			case qr/^VARfastagi_log_checkforwait/ {
				$config_hash{ 'fagi_log_checkforwait' } = $line;
				$config_hash{ 'fagi_log_checkforwait' } =~ s/.*=//gi;
			}

			# FAGI inbound server settings
			case qr/^VARfastagi_in_server_port/ {
				$config_hash{ 'fagi_in_server_port' } = $line;
				$config_hash{ 'fagi_in_server_port' } =~ s/.*=//gi;
			}
			case qr/^VARfastagi_in_min_servers/ {
				$config_hash{ 'fagi_in_min_servers' } = $line;
				$config_hash{ 'fagi_in_min_servers' } =~ s/.*=//gi;
			}
			case qr/^VARfastagi_in_max_servers/ {
				$config_hash{ 'fagi_in_max_servers' } = $line;
				$config_hash{ 'fagi_in_max_servers' } =~ s/.*=//gi;
			}
			case qr/^VARfastagi_in_min_spare_servers/ {
				$config_hash{ 'fagi_in_min_spare_servers' } = $line;
				$config_hash{ 'fagi_in_min_spare_servers' } =~ s/.*=//gi;
			}
			case qr/^VARfastagi_in_max_spare_servers/ {
				$config_hash{ 'fagi_in_max_spare_servers' } = $line;
				$config_hash{ 'fagi_in_max_spare_servers' } =~ s/.*=//gi;
			}
			case qr/^VARfastagi_in_max_requests/ {
				$config_hash{ 'fagi_in_max_requests' } = $line;
				$config_hash{ 'fagi_in_max_requests' } =~ s/.*=//gi;
			}
			case qr/^VARfastagi_in_checkfordead/ {
				$config_hash{ 'fagi_in_checkfordead' } = $line;
				$config_hash{ 'fagi_in_checkfordead' } =~ s/.*=//gi;
			}
			case qr/^VARfastagi_in_checkforwait/ {
				$config_hash{ 'fagi_in_checkforwait' } = $line;
				$config_hash{ 'fagi_in_checkforwait' } =~ s/.*=//gi;
			}

			# FAGI outbound server settings
			case qr/^VARfastagi_out_server_port/ {
				$config_hash{ 'fagi_out_server_port' } = $line;
				$config_hash{ 'fagi_out_server_port' } =~ s/.*=//gi;
			}
			case qr/^VARfastagi_out_min_servers/ {
				$config_hash{ 'fagi_out_min_servers' } = $line;
				$config_hash{ 'fagi_out_min_servers' } =~ s/.*=//gi;
			}
			case qr/^VARfastagi_out_max_servers/ {
				$config_hash{ 'fagi_out_max_servers' } = $line;
				$config_hash{ 'fagi_out_max_servers' } =~ s/.*=//gi;
			}
			case qr/^VARfastagi_out_min_spare_servers/ {
				$config_hash{ 'fagi_out_min_spare_servers' } = $line;
				$config_hash{ 'fagi_out_min_spare_servers' } =~ s/.*=//gi;
			}
			case qr/^VARfastagi_out_max_spare_servers/ {
				$config_hash{ 'fagi_out_max_spare_servers' } = $line;
				$config_hash{ 'fagi_out_max_spare_servers' } =~ s/.*=//gi;
			}
			case qr/^VARfastagi_out_max_requests/ {
				$config_hash{ 'fagi_out_max_requests' } = $line;
				$config_hash{ 'fagi_out_max_requests' } =~ s/.*=//gi;
			}
			case qr/^VARfastagi_out_checkfordead/ {
				$config_hash{ 'fagi_out_checkfordead' } = $line;
				$config_hash{ 'fagi_out_checkfordead' } =~ s/.*=//gi;
			}
			case qr/^VARfastagi_out_checkforwait/ {
				$config_hash{ 'fagi_out_checkforwait' } = $line;
				$config_hash{ 'fagi_out_checkforwait' } =~ s/.*=//gi;
			}

			# general server settings
			case qr/^VARserver_ip/ {
				$config_hash{ 'server_ip' } = $line;
				$config_hash{ 'server_ip' } =~ s/.*=//gi;
			}
			case qr/^VARactive_keepalives/ {
				$config_hash{ 'active_keepalives' } = $line;
				$config_hash{ 'active_keepalives' } =~ s/.*=//gi;
			}

			# Database server connection information
			case qr/^VARDB_server/ {
				$config_hash{ 'db_server' } = $line;
				$config_hash{ 'db_server' } =~ s/.*=//gi;
			}
			case qr/^VARDB_database/ {
				$config_hash{ 'db_database' } = $line;
				$config_hash{ 'db_database' } =~ s/.*=//gi;
			}
			case qr/^VARDB_user/ {
				$config_hash{ 'db_user' } = $line;
				$config_hash{ 'db_user' } =~ s/.*=//gi;
			}
			case qr/^VARDB_pass/ {
				$config_hash{ 'db_pass' } = $line;
				$config_hash{ 'db_pass' } =~ s/.*=//gi;
			}
			case qr/^VARDB_port/ {
				$config_hash{ 'db_port' } = $line;
				$config_hash{ 'db_port' } =~ s/.*=//gi;
			}

			# FTP recording archive connection information
			case qr/^VARFTP_host/ {
				$config_hash{ 'ftp_host' } = $line;
				$config_hash{ 'ftp_host' } =~ s/.*=//gi;
			}
			case qr/^VARFTP_user/ {
				$config_hash{ 'ftp_user' } = $line;
				$config_hash{ 'ftp_user' } =~ s/.*=//gi;
			}
			case qr/^VARFTP_pass/ {
				$config_hash{ 'ftp_pass' } = $line;
				$config_hash{ 'ftp_pass' } =~ s/.*=//gi;
			}
			case qr/^VARFTP_port/ {
				$config_hash{ 'ftp_port' } = $line;
				$config_hash{ 'ftp_port' } =~ s/.*=//gi;
			}
			case qr/^VARFTP_dir/ {
				$config_hash{ 'ftp_dir' } = $line;
				$config_hash{ 'ftp_dir' } =~ s/.*=//gi;
			}
			case qr/^VARHTTP_path/ {
				$config_hash{ 'ftp_http_path' } = $line;
				$config_hash{ 'ftp_http_path' } =~ s/.*=//gi;
			}

			# REPORT server connection information
			case qr/^VARREPORT_host/ {
				$config_hash{ 'report_host' } = $line;
				$config_hash{ 'report_host' } =~ s/.*=//gi;
			}
			case qr/^VARREPORT_user/ {
				$config_hash{ 'report_user' } = $line;
				$config_hash{ 'report_user' } =~ s/.*=//gi;
			}
			case qr/^VARREPORT_pass/ {
				$config_hash{ 'report_pass' } = $line;
				$config_hash{ 'report_pass' } =~ s/.*=//gi;
			}
			case qr/^VARREPORT_port/ {
				$config_hash{ 'report_port' } = $line;
				$config_hash{ 'report_port' } =~ s/.*=//gi;
			}
			case qr/^VARREPORT_dir/ {
				$config_hash{ 'report_dir' } = $line;
				$config_hash{ 'report_dir' } =~ s/.*=//gi;
			}
		} 
		$i++;
	}

	# return the hash
	return \%config_hash;
}


# function to load the system configs from the db
#    ARG0 is an already connected database handle
#    this function returns a hash with the servers configs in it
#      the hash has the following values:
#        version		= the version of astguiclient that is being used
#        install_date		= the date that the orginal installation was done
#        use_utf8		= if to use UTF8 instead of latin characters
#        webroot_writable	= if to place temp files in the webroot directory
#        enable_qm_logging	= if to use QueueMetrics
#        qm_server_ip		= QM servers ip address
#        qm_dbname		= QM DB name
#        qm_login		= QM DB username
#        qm_pass		= QM DB password
#        qm_url			= url to get to QM
#        qm_log_id		= the server ID that all VICIDIAL logs will use as an id for each record in QM
#        qm_eq_prepend		= field from vicidial_list to prepend to the cust. phone number in QM
#        vd_agent_disable	= if to disable manager actions on agents screens
#        allow_sipsak_msgs	= if to use sipsak
#        admin_home_url		= url to go to if you click the HOME link on the top of the admin page
#        agent_xfer_weblog	= log to a text logfile on the webserver every time call is xfered to agent
sub load_sys_config_db {
	my $dbh	= $_[0];

	# init the hash
	my %system_hash = ();

	# fetch data from db
	my $stmt = 
"SELECT version, install_date, use_non_latin,  webroot_writable, enable_queuemetrics_logging, queuemetrics_server_ip, queuemetrics_dbname, queuemetrics_login, queuemetrics_pass, queuemetrics_url, queuemetrics_log_id, queuemetrics_eq_prepend, vicidial_agent_disable, allow_sipsak_messages, admin_home_url, enable_agc_xfer_log FROM system_settings LIMIT 1;";
	my $sth = $dbh->prepare($stmt) or die "preparing: ", $dbh->errstr;
	$sth->execute or die "executing: $stmt ", $dbh->errstr;
	my @ary = $sth->fetchrow_array;
	$sth->finish();

	# assign the hash values
	$system_hash{ 'version' } 		= $ary[0];	# version db field
	$system_hash{ 'install_date' } 		= $ary[1];	# install_date db field
	$system_hash{ 'use_utf8' } 		= $ary[2];	# use_non_latin db field
	$system_hash{ 'webroot_writable' } 	= $ary[3];	# webroot_writable db field
	$system_hash{ 'enable_qm_log' } 	= $ary[4];	# enable_queuemetrics_logging db field
	$system_hash{ 'qm_server_ip' } 		= $ary[5];	# queuemetrics_server_ip db field
	$system_hash{ 'qm_dbname' } 		= $ary[6];	# queuemetrics_dbname db field
	$system_hash{ 'qm_login' } 		= $ary[7];	# queuemetrics_login db field
	$system_hash{ 'qm_pass' } 		= $ary[8];	# queuemetrics_pass db field
	$system_hash{ 'qm_url' } 		= $ary[9];	# queuemetrics_url db field
	$system_hash{ 'qm_log_id' } 		= $ary[10];	# queuemetrics_log_id db field
	$system_hash{ 'qm_eq_prepend' } 	= $ary[11];	# queuemetrics_eq_prepend db field
	$system_hash{ 'vd_agent_disable' } 	= $ary[12];	# vicidial_agent_disable db field
	$system_hash{ 'allow_sipsak_msgs' }	= $ary[13];	# allow_sipsak_messages db field
	$system_hash{ 'admin_home_url' } 	= $ary[14];	# admin_home_url db field
	$system_hash{ 'agent_xfer_weblog' } 	= $ary[15];	# enable_agc_xfer_log db field
		
	# run through the hash and set any non-defined keys to ""
	for my $sys_key ( keys %system_hash ) {
		if ( !defined( $system_hash{ $sys_key } ) ) {
			$system_hash{ $sys_key } = "";
		}
	}
	
	# return the hash
	return \%system_hash;
}


# function to load a servers configs from the db
#    ARG0 is the ip of the server to load configs for
#    ARG1 is an already connected database handle
#    this function returns a reference to a hash with the servers configs in it
#      the hash has the following values:
#        server_id		= the id for the server in the DB
#        server_desc		= the description of the server
#        server_ip		= the IP address of the server
#        active			= if the server is active or not
#        ast_version		= the version of asterisk used on the server
#        max_vd_trunks		= the maximum number of trunks vicidial can use on this server
#        ast_mgr_host		= the telnet host to connect to this servers Asterisk Manager interface
#        localhost		= what this server thinks localhost should be (usually 127.0.0.1)
#        ast_mgr_port		= the telnet port to connect to this servers Asterisk Manager interface
#        ast_mgr_user		= the username for the general connection to the manager interface
#        ast_mgr_pass		= the secret used to connect to the manager inferface for all of the usernames
#        ast_mgr_update_user	= the username to run updates from on the manager interface
#        ast_mgr_listen_user 	= the username to listen on the manager interface
#        ast_mgr_send_user	= the username to send actions on the manager interface
#        local_gmt		= the gmt offset for this server
#        vm_dump_exten		= the extension to send voicemail to on this server
#        dft_xfer_exten		= default extension to send calls to for VICIDIAL auto dialing
#        ext_context		= the context where to find the vicidial extension
#        sys_perf_log		= if vicidial should do performance logging for this server
#        vd_server_logs		= if vicidial should do normal logging for this server
#        agi_output		= what style of logging should be done for this server
#        vd_balance_active	= if vicidial should do balanced dialing with this server
#        vd_balance_free_trunks	= the number of trunks balanced dialing must keep free
sub load_server_config_db {
	my $server_ip	= $_[0];
	my $dbh		= $_[1];

	# init the hash
	my %server_hash = ();

	# fetch data from the DB
	my $stmt = "SELECT server_id, server_description, server_ip, active, asterisk_version, max_vicidial_trunks, telnet_host, telnet_port, ASTmgrUSERNAME, ASTmgrSECRET, ASTmgrUSERNAMEupdate, ASTmgrUSERNAMElisten, ASTmgrUSERNAMEsend, local_gmt, voicemail_dump_exten, answer_transfer_agent, ext_context, sys_perf_log, vd_server_logs, agi_output, vicidial_balance_active, balance_trunks_offlimits FROM servers WHERE server_ip = '$server_ip' LIMIT 1;";

	my $sth = $dbh->prepare($stmt) or die "preparing: ", $dbh->errstr;
	$sth->execute or die "executing: $stmt ", $dbh->errstr;
	my @ary = $sth->fetchrow_array;
	$sth->finish();

	# assign the hash values
	$server_hash{ 'server_id' } 			= "$ary[0]";	# server_id db field
	$server_hash{ 'server_desc' } 			= "$ary[1]";	# server_description db field
	$server_hash{ 'server_ip' } 			= "$ary[2]";	# server_ip db field
	$server_hash{ 'active' } 			= "$ary[3]";	# active db field
	$server_hash{ 'ast_version' } 			= "$ary[4]";	# asterisk_version db field
	$server_hash{ 'max_vd_trunks' } 		= "$ary[5]";	# max_vicidial_trunks db field
	$server_hash{ 'ast_mgr_host' } 			= "$ary[6]";	# telnet_host db field
	$server_hash{ 'ast_mgr_port' } 			= "$ary[7]";	# telnet_port db field
	$server_hash{ 'ast_mgr_user' } 			= "$ary[8]";	# ASTmgrUSERNAME db field
	$server_hash{ 'ast_mgr_pass' } 			= "$ary[9]";	# ASTmgrSECRET db field
	$server_hash{ 'ast_mgr_update_user' } 		= "$ary[10]";	# ASTmgrUSERNAMEupdate db field
	$server_hash{ 'ast_mgr_listen_user' } 		= "$ary[11]";	# ASTmgrUSERNAMElisten db field
	$server_hash{ 'ast_mgr_send_user' } 		= "$ary[12]";	# ASTmgrUSERNAMEsend db field
	$server_hash{ 'local_gmt' } 			= "$ary[13]";	# local_gmt db field
	$server_hash{ 'vm_dump_exten' } 		= "$ary[14]";	# voicemail_dump_exten db field
	$server_hash{ 'dft_xfer_exten' } 		= "$ary[15]";	# answer_transfer_agent db field
	$server_hash{ 'ext_context' } 			= "$ary[16]";	# ext_context db field
	$server_hash{ 'sys_perf_log' } 			= "$ary[17]";	# sys_perf_log db field
	$server_hash{ 'vd_server_logs' } 		= "$ary[18]";	# vd_server_logs db field
	$server_hash{ 'agi_output' } 			= "$ary[19]";	# agi_output db field
	$server_hash{ 'vd_balance_active' }	 	= "$ary[20]";	# vicidial_balance_active db field
	$server_hash{ 'vd_balance_free_trunks' }	= "$ary[21]";	# balance_trunks_offlimits db field
	
	# run through the hash and set any non-defined keys to ""
	for my $serv_key ( keys %server_hash ) {
		if ( !defined( $server_hash{ $serv_key } ) ) {
			$server_hash{ $serv_key } = "";
		}
	}
	
	#return the hash
	return \%server_hash;
}


# function to load all servers configs from the db
#    ARG0 is an already connected database handle
#    this function returns a reference to a hash of hashes with the servers configs in them
#      the first level hash is referneced by the servers IP address
#      the second level hash has the following values:
#        server_id		= the id for the server in the DB
#        server_desc		= the description of the server
#        server_ip		= the IP address of the server
#        active			= if the server is active or not
#        ast_version		= the version of asterisk used on the server
#        max_vd_trunks		= the maximum number of trunks vicidial can use on this server
#        ast_mgr_host		= the telnet host to connect to this servers Asterisk Manager interface
#        localhost		= what this server thinks localhost should be (usually 127.0.0.1)
#        ast_mgr_port		= the telnet port to connect to this servers Asterisk Manager interface
#        ast_mgr_user		= the username for the general connection to the manager interface
#        ast_mgr_pass		= the secret used to connect to the manager inferface for all of the usernames
#        ast_mgr_update_user	= the username to run updates from on the manager interface
#        ast_mgr_listen_user 	= the username to listen on the manager interface
#        ast_mgr_send_user	= the username to send actions on the manager interface
#        local_gmt		= the gmt offset for this server
#        vm_dump_exten		= the extension to send voicemail to on this server
#        dft_xfer_exten		= default extension to send calls to for VICIDIAL auto dialing
#        ext_context		= the context where to find the vicidial extension
#        sys_perf_log		= if vicidial should do performance logging for this server
#        vd_server_logs		= if vicidial should do normal logging for this server
#        agi_output		= what style of logging should be done for this server
#        vd_balance_active	= if vicidial should do balanced dialing with this server
#        vd_balance_free_trunks	= the number of trunks balanced dialing must keep free
sub load_all_servers_config_db { 
	my $dbh = $_[0];
	
	# init the hash
	my %all_servers_hash = ();

	# fetch data from the DB
	my $stmt = 
"SELECT server_id, server_description, server_ip, active, asterisk_version, max_vicidial_trunks, telnet_host, telnet_port, ASTmgrUSERNAME, ASTmgrSECRET, ASTmgrUSERNAMEupdate, ASTmgrUSERNAMElisten, ASTmgrUSERNAMEsend, local_gmt, voicemail_dump_exten, answer_transfer_agent, ext_context, sys_perf_log, vd_server_logs, agi_output, vicidial_balance_active, balance_trunks_offlimits FROM servers;";
	my $sth = $dbh->prepare($stmt) or die "preparing: ", $dbh->errstr;
	$sth->execute or die "executing: $stmt ", $dbh->errstr;

	# loop through each of the servers
	my $sthrows  = $sth->rows;
	my $rec_count = 0;
	while ( $sthrows > $rec_count ) {
		my @ary = $sth->fetchrow_array;

		# get the ip address of the server for refernecing
		my $server_ip = "$ary[2]";
	
		# assign the hash values
		$all_servers_hash{ $server_ip }{ 'server_id' } 			= "$ary[0]";	# server_id db field
		$all_servers_hash{ $server_ip }{ 'server_desc' } 		= "$ary[1]";	# server_description db field
		$all_servers_hash{ $server_ip }{ 'server_ip' } 			= "$ary[2]";	# server_ip db field
		$all_servers_hash{ $server_ip }{ 'active' } 			= "$ary[3]";	# active db field
		$all_servers_hash{ $server_ip }{ 'ast_version' } 		= "$ary[4]";	# asterisk_version db field
		$all_servers_hash{ $server_ip }{ 'max_vd_trunks' } 		= "$ary[5]";	# max_vicidial_trunks db field
		$all_servers_hash{ $server_ip }{ 'ast_mgr_host' } 		= "$ary[6]";	# telnet_host db field
		$all_servers_hash{ $server_ip }{ 'ast_mgr_port' } 		= "$ary[7]";	# telnet_port db field
		$all_servers_hash{ $server_ip }{ 'ast_mgr_user' } 		= "$ary[8]";	# ASTmgrUSERNAME db field
		$all_servers_hash{ $server_ip }{ 'ast_mgr_pass' } 		= "$ary[9]";	# ASTmgrSECRET db field
		$all_servers_hash{ $server_ip }{ 'ast_mgr_update_user' } 	= "$ary[10]";	# ASTmgrUSERNAMEupdate db field
		$all_servers_hash{ $server_ip }{ 'ast_mgr_listen_user' } 	= "$ary[11]";	# ASTmgrUSERNAMElisten db field
		$all_servers_hash{ $server_ip }{ 'ast_mgr_send_user' } 		= "$ary[12]";	# ASTmgrUSERNAMEsend db field
		$all_servers_hash{ $server_ip }{ 'local_gmt' } 			= "$ary[13]";	# local_gmt db field
		$all_servers_hash{ $server_ip }{ 'vm_dump_exten' } 		= "$ary[14]";	# voicemail_dump_exten db field
		$all_servers_hash{ $server_ip }{ 'dft_xfer_exten' } 		= "$ary[15]";	# answer_transfer_agent db field
		$all_servers_hash{ $server_ip }{ 'ext_context' } 		= "$ary[16]";	# ext_context db field
		$all_servers_hash{ $server_ip }{ 'sys_perf_log' } 		= "$ary[17]";	# sys_perf_log db field
		$all_servers_hash{ $server_ip }{ 'vd_server_logs' } 		= "$ary[18]";	# vd_server_logs db field
		$all_servers_hash{ $server_ip }{ 'agi_output' } 		= "$ary[19]";	# agi_output db field
		$all_servers_hash{ $server_ip }{ 'vd_balance_active' }	 	= "$ary[20]";	# vicidial_balance_active db field
		$all_servers_hash{ $server_ip }{ 'vd_balance_free_trunks' }	= "$ary[21]";	# balance_trunks_offlimits db field

		$rec_count++;
	}

	my $all_servers_hash_ref = \%all_servers_hash;

	# run through the hash and set any non-defined keys to ""
	for my $serv_ip_key ( keys %$all_servers_hash_ref ) {
		for my $serv_key ( keys %{$all_servers_hash_ref->{ $serv_ip_key }} ) {
			if ( !defined( $all_servers_hash{ $serv_ip_key }{ $serv_key } ) ) {
				$all_servers_hash{ $serv_ip_key }{ $serv_key } = "";
			}
		}
	}

	$sth->finish();

	# return the hash of hashes
	return \%all_servers_hash;
}

1;
