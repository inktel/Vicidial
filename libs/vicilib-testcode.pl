#!/usr/bin/perl


# vicilib-testcode.pl version 2.0.5 
#
# This script is designed to test the vicidial.pm library.
# It gets it's settings from the local /etc/astguiclient.conf file.
# and connects the the database specified there in.
#
# This script should be updated anytime anything new is added to the
# vicidial.pm library and should throughly test each functon
# 
# Copyright (C) 2008  VICIDIAL-GROUP  <info@vicidial-group.com> LICENSE: AGPLv2
#
# CHANGELOG:
# 80324-1435 - First fully successful run


use strict;
use warnings;

use vicidial;
use viciagi;
use DBI;

# Test if get_date_hash_current is working
print "Test if get_date_hash_current is working\n";
print "printing the current time\n";
my $cur_date_hash_ref = get_date_hash_current();
my %cur_date_hash = %$cur_date_hash_ref;

print "cur_date_hash{ 'sec' } = $cur_date_hash{ 'sec' }\n";
print "cur_date_hash{ 'min' } = $cur_date_hash{ 'min' }\n";
print "cur_date_hash{ 'hour' } = $cur_date_hash{ 'hour' }\n";
print "cur_date_hash{ 'mday' } = $cur_date_hash{ 'mday' }\n";
print "cur_date_hash{ 'month' } = $cur_date_hash{ 'month' }\n";
print "cur_date_hash{ 'year' } = $cur_date_hash{ 'year' }\n";
print "cur_date_hash{ 'wday' } = $cur_date_hash{ 'wday' }\n";
print "cur_date_hash{ 'yday' } = $cur_date_hash{ 'yday' }\n";
print "cur_date_hash{ 'isdst' } = $cur_date_hash{ 'isdst' }\n";
print "cur_date_hash{ 'Fhour' } = $cur_date_hash{ 'Fhour' }\n\n";



# Test if get_date_hash_value is working
print "Test if get_date_hash_value is working\n";
print "printing the time at the epoch\n";
my $epoch_date_hash_ref = get_date_hash_value(0);
my %epoch_date_hash = %$epoch_date_hash_ref;

print "epoch_date_hash{ 'sec' } = $epoch_date_hash{ 'sec' }\n";
print "epoch_date_hash{ 'min' } = $epoch_date_hash{ 'min' }\n";
print "epoch_date_hash{ 'hour' } = $epoch_date_hash{ 'hour' }\n";
print "epoch_date_hash{ 'mday' } = $epoch_date_hash{ 'mday' }\n";
print "epoch_date_hash{ 'month' } = $epoch_date_hash{ 'month' }\n";
print "epoch_date_hash{ 'year' } = $epoch_date_hash{ 'year' }\n";
print "epoch_date_hash{ 'wday' } = $epoch_date_hash{ 'wday' }\n";
print "epoch_date_hash{ 'yday' } = $epoch_date_hash{ 'yday' }\n";
print "epoch_date_hash{ 'isdst' } = $epoch_date_hash{ 'isdst' }\n";
print "epoch_date_hash{ 'Fhour' } = $epoch_date_hash{ 'Fhour' }\n\n";



# Test if get_now_date is working
print "Test if get_now_date is working\n";
print "printing the current time as a now_date string\n";

my $now_date_str_ref = get_now_date( \%cur_date_hash );
my $now_date_str = $$now_date_str_ref;

print "$now_date_str\n\n";


# Test if agi_log is working
agi_output(3, $now_date_str, $0, "test agi_log", "./agi_log_test", "Test if agi_log is working");
 

# Test if load_config_file is working
print "Test if load_config_file is working\n";
print "printing the config_hash loaded from /etc/astguiclient.conf\n";

my $file_path = "/etc/astguiclient.conf";

my $config_hash_ref = load_config_file( $file_path );
my %config_hash = %$config_hash_ref;

print "config_hash{ 'logs_path' } = $config_hash{ 'logs_path' }\n";
print "config_hash{ 'home_path' } = $config_hash{ 'home_path' }\n";
print "config_hash{ 'agi_path' } = $config_hash{ 'agi_path' }\n";
print "config_hash{ 'web_path' } = $config_hash{ 'web_path' }\n";
print "config_hash{ 'sounds_path' } = $config_hash{ 'sounds_path' }\n";
print "config_hash{ 'monitor_path' } = $config_hash{ 'monitor_path' }\n";
print "config_hash{ 'done_monitor_path' } = $config_hash{ 'done_monitor_path' }\n";
print "config_hash{ 'fagi_log_min_servers' } = $config_hash{ 'fagi_log_min_servers' }\n";
print "config_hash{ 'fagi_log_max_servers' } = $config_hash{ 'fagi_log_max_servers' }\n";
print "config_hash{ 'fagi_log_min_spare_servers' } = $config_hash{ 'fagi_log_min_spare_servers' }\n";
print "config_hash{ 'fagi_log_max_spare_servers' } = $config_hash{ 'fagi_log_max_spare_servers' }\n";
print "config_hash{ 'fagi_log_max_requests' } = $config_hash{ 'fagi_log_max_requests' }\n";
print "config_hash{ 'fagi_log_checkfordead' } = $config_hash{ 'fagi_log_checkfordead' }\n";
print "config_hash{ 'fastagi_log_checkforwait' } = $config_hash{ 'fastagi_log_checkforwait' }\n";
print "config_hash{ 'server_ip' } = $config_hash{ 'server_ip' }\n";
print "config_hash{ 'active_keepalives' } = $config_hash{ 'active_keepalives' }\n";
print "config_hash{ 'db_server' } = $config_hash{ 'db_server' }\n";
print "config_hash{ 'db_database' } = $config_hash{ 'db_database' }\n";
print "config_hash{ 'db_user' } = $config_hash{ 'db_user' }\n";
print "config_hash{ 'db_pass' } = $config_hash{ 'db_pass' }\n";
print "config_hash{ 'db_port' } = $config_hash{ 'db_port' }\n";
print "config_hash{ 'ftp_host' } = $config_hash{ 'ftp_host' }\n";
print "config_hash{ 'ftp_user' } = $config_hash{ 'ftp_user' }\n";
print "config_hash{ 'ftp_pass' } = $config_hash{ 'ftp_pass' }\n";
print "config_hash{ 'ftp_port' } = $config_hash{ 'ftp_port' }\n";
print "config_hash{ 'ftp_dir' } = $config_hash{ 'ftp_dir' }\n";
print "config_hash{ 'ftp_http_path' } = $config_hash{ 'report_host' }\n";
print "config_hash{ 'report_host' } = $config_hash{ 'report_host' }\n";
print "config_hash{ 'report_user' } = $config_hash{ 'report_user' }\n";
print "config_hash{ 'report_pass' } = $config_hash{ 'report_pass' }\n";
print "config_hash{ 'report_port' } = $config_hash{ 'report_port' }\n";
print "config_hash{ 'report_dir' } = $config_hash{ 'report_dir' }\n\n";

# get a dbconnection for the rest of these routines
my $dbh = DBI->connect(
		"DBI:mysql:$config_hash{ 'db_database' }:$config_hash{ 'db_server' }:$config_hash{ 'db_port' }",
		"$config_hash{ 'db_user' }",
		"$config_hash{ 'db_pass' }" 
	) or die "Couldn't connect to database: " . DBI->errstr;


# Test if load_sys_config_db is working
print "Test if load_sys_config_db is working\n";

my $system_hash_ref = load_sys_config_db( $dbh );
my %system_hash = %$system_hash_ref;

print "system_hash{ 'version' } 		= $system_hash{ 'version' }\n";
print "system_hash{ 'install_date' } 		= $system_hash{ 'install_date' }\n";
print "system_hash{ 'use_utf8' } 		= $system_hash{ 'use_utf8' }\n";
print "system_hash{ 'webroot_writable' } 	= $system_hash{ 'webroot_writable' }\n";
print "system_hash{ 'enable_qm_log' } 		= $system_hash{ 'enable_qm_log' }\n";
print "system_hash{ 'qm_server_ip' } 		= $system_hash{ 'qm_server_ip' }\n";
print "system_hash{ 'qm_dbname' } 		= $system_hash{ 'qm_dbname' }\n";
print "system_hash{ 'qm_login' } 		= $system_hash{ 'qm_login' }\n";
print "system_hash{ 'qm_pass' } 		= $system_hash{ 'qm_pass' }\n";
print "system_hash{ 'qm_url' } 		= $system_hash{ 'qm_url' }\n";
print "system_hash{ 'qm_log_id' } 		= $system_hash{ 'qm_log_id' }\n";
print "system_hash{ 'qm_eq_prepend' } 		= $system_hash{ 'qm_eq_prepend' }\n";
print "system_hash{ 'vd_agent_disable' } 	= $system_hash{ 'vd_agent_disable' }\n";
print "system_hash{ 'allow_sipsak_msgs' }	= $system_hash{ 'allow_sipsak_msgs' }\n";
print "system_hash{ 'admin_home_url' } 	= $system_hash{ 'admin_home_url' }\n";
print "system_hash{ 'agent_xfer_weblog' } 	= $system_hash{ 'agent_xfer_weblog' }\n\n";



# Test if load_server_config_db is working
print "Test if load_server_config_db is working\n";

my $server_hash_ref = load_server_config_db( $config_hash{ 'server_ip' } , $dbh );
my %server_hash = %$server_hash_ref;

print "server_hash{ 'server_id' } 			= $server_hash{ 'server_id' }\n";
print "server_hash{ 'server_desc' } 			= $server_hash{ 'server_desc' }\n";
print "server_hash{ 'server_ip' } 			= $server_hash{ 'server_ip' }\n";
print "server_hash{ 'active' } 			= $server_hash{ 'active' }\n";
print "server_hash{ 'ast_version' } 			= $server_hash{ 'ast_version' }\n";
print "server_hash{ 'max_vd_trunks' } 			= $server_hash{ 'max_vd_trunks' }\n";
print "server_hash{ 'ast_mgr_host' } 			= $server_hash{ 'ast_mgr_host' }\n";
print "server_hash{ 'ast_mgr_port' } 			= $server_hash{ 'ast_mgr_port' }\n";
print "server_hash{ 'ast_mgr_user' } 			= $server_hash{ 'ast_mgr_user' }\n";
print "server_hash{ 'ast_mgr_pass' } 			= $server_hash{ 'ast_mgr_pass' }\n";
print "server_hash{ 'ast_mgr_update_user' } 		= $server_hash{ 'ast_mgr_update_user' }\n";
print "server_hash{ 'ast_mgr_listen_user' } 		= $server_hash{ 'ast_mgr_listen_user' }\n";
print "server_hash{ 'ast_mgr_send_user' } 		= $server_hash{ 'ast_mgr_send_user' }\n";
print "server_hash{ 'local_gmt' } 			= $server_hash{ 'local_gmt' }\n";
print "server_hash{ 'vm_dump_exten' } 			= $server_hash{ 'vm_dump_exten' }\n";
print "server_hash{ 'dft_xfer_exten' } 		= $server_hash{ 'dft_xfer_exten' }\n";
print "server_hash{ 'ext_context' } 			= $server_hash{ 'ext_context' }\n";
print "server_hash{ 'sys_perf_log' } 			= $server_hash{ 'sys_perf_log' }\n";
print "server_hash{ 'vd_server_logs' } 		= $server_hash{ 'vd_server_logs' }\n";
print "server_hash{ 'agi_output' } 			= $server_hash{ 'agi_output' }\n";
print "server_hash{ 'vd_balance_active' }	 	= $server_hash{ 'vd_balance_active' }\n";
print "server_hash{ 'vd_balance_free_trunks' }		= $server_hash{ 'vd_balance_free_trunks' }\n\n";


print "Test if load_all_servers_config_db is working\n";

my $all_servers_hash_ref = load_all_servers_config_db( $dbh );
my %all_servers_hash = %$all_servers_hash_ref;

my $hash_size = %all_servers_hash;

for my $server_ip ( keys %all_servers_hash ) {
	# assign the hash values
	print "all_servers_hash{ $server_ip }{ 'server_id' } 			= $all_servers_hash{ $server_ip }{ 'server_id' }\n";
	print "all_servers_hash{ $server_ip }{ 'server_desc' } 			= $all_servers_hash{ $server_ip }{ 'server_desc' }\n";
	print "all_servers_hash{ $server_ip }{ 'server_ip' } 			= $all_servers_hash{ $server_ip }{ 'server_ip' }\n";
	print "all_servers_hash{ $server_ip }{ 'active' } 			= $all_servers_hash{ $server_ip }{ 'active' }\n";
	print "all_servers_hash{ $server_ip }{ 'ast_version' } 			= $all_servers_hash{ $server_ip }{ 'ast_version' }\n"; 
	print "all_servers_hash{ $server_ip }{ 'max_vd_trunks' } 		= $all_servers_hash{ $server_ip }{ 'max_vd_trunks' }\n";
	print "all_servers_hash{ $server_ip }{ 'ast_mgr_host' } 			= $all_servers_hash{ $server_ip }{ 'ast_mgr_host' }\n";
	print "all_servers_hash{ $server_ip }{ 'ast_mgr_port' } 			= $all_servers_hash{ $server_ip }{ 'ast_mgr_port' }\n";
	print "all_servers_hash{ $server_ip }{ 'ast_mgr_user' } 			= $all_servers_hash{ $server_ip }{ 'ast_mgr_user' }\n";
	print "all_servers_hash{ $server_ip }{ 'ast_mgr_pass' } 			= $all_servers_hash{ $server_ip }{ 'ast_mgr_pass' }\n";
	print "all_servers_hash{ $server_ip }{ 'ast_mgr_update_user' } 		= $all_servers_hash{ $server_ip }{ 'ast_mgr_update_user' }\n";
	print "all_servers_hash{ $server_ip }{ 'ast_mgr_listen_user' } 		= $all_servers_hash{ $server_ip }{ 'ast_mgr_listen_user' }\n";
	print "all_servers_hash{ $server_ip }{ 'ast_mgr_send_user' } 		= $all_servers_hash{ $server_ip }{ 'ast_mgr_send_user' }\n";
	print "all_servers_hash{ $server_ip }{ 'local_gmt' } 			= $all_servers_hash{ $server_ip }{ 'local_gmt' }\n";
	print "all_servers_hash{ $server_ip }{ 'vm_dump_exten' } 		= $all_servers_hash{ $server_ip }{ 'vm_dump_exten' }\n";
	print "all_servers_hash{ $server_ip }{ 'dft_xfer_exten' } 		= $all_servers_hash{ $server_ip }{ 'dft_xfer_exten' }\n";
	print "all_servers_hash{ $server_ip }{ 'ext_context' } 			= $all_servers_hash{ $server_ip }{ 'ext_context' }\n"; 
	print "all_servers_hash{ $server_ip }{ 'sys_perf_log' } 			= $all_servers_hash{ $server_ip }{ 'sys_perf_log' }\n";
	print "all_servers_hash{ $server_ip }{ 'vd_server_logs' } 		= $all_servers_hash{ $server_ip }{ 'vd_server_logs' }\n";
	print "all_servers_hash{ $server_ip }{ 'agi_output' } 			= $all_servers_hash{ $server_ip }{ 'agi_output' }\n";
	print "all_servers_hash{ $server_ip }{ 'vd_balance_active' }	 	= $all_servers_hash{ $server_ip }{ 'vd_balance_active' }\n"; 
	print "all_servers_hash{ $server_ip }{ 'vd_balance_free_trunks' }	= $all_servers_hash{ $server_ip }{ 'vd_balance_free_trunks' }\n\n";
		
}


