CREATE TABLE phones (
extension VARCHAR(100),
dialplan_number VARCHAR(20),
voicemail_id VARCHAR(10),
phone_ip VARCHAR(15),
computer_ip VARCHAR(15),
server_ip VARCHAR(15),
login VARCHAR(15),
pass VARCHAR(10),
status VARCHAR(10),
active ENUM('Y','N'),
phone_type VARCHAR(50),
fullname VARCHAR(50),
company VARCHAR(10),
picture VARCHAR(19),
messages INT(4),
old_messages INT(4),
protocol ENUM('SIP','Zap','IAX2','EXTERNAL') default 'SIP',
local_gmt VARCHAR(6) default '-5.00',
ASTmgrUSERNAME VARCHAR(20) default 'cron',
ASTmgrSECRET VARCHAR(20) default '1234',
login_user VARCHAR(20),
login_pass VARCHAR(20),
login_campaign VARCHAR(10),
park_on_extension VARCHAR(10) default '8301',
conf_on_extension VARCHAR(10) default '8302',
VICIDIAL_park_on_extension VARCHAR(10) default '8301',
VICIDIAL_park_on_filename VARCHAR(10) default 'park',
monitor_prefix VARCHAR(10) default '8612',
recording_exten VARCHAR(10) default '8309',
voicemail_exten VARCHAR(10) default '8501',
voicemail_dump_exten VARCHAR(20) default '85026666666666',
ext_context VARCHAR(20) default 'default',
dtmf_send_extension VARCHAR(100) default 'local/8500998@default',
call_out_number_group VARCHAR(100) default 'Zap/g2/',
client_browser VARCHAR(100) default '/usr/bin/mozilla',
install_directory VARCHAR(100) default '/usr/local/perl_TK',
local_web_callerID_URL VARCHAR(255) default 'http://astguiclient.sf.net/test_callerid_output.php',
VICIDIAL_web_URL VARCHAR(255) default 'http://astguiclient.sf.net/test_VICIDIAL_output.php',
AGI_call_logging_enabled ENUM('0','1') default '1',
user_switching_enabled ENUM('0','1') default '1',
conferencing_enabled ENUM('0','1') default '1',
admin_hangup_enabled ENUM('0','1') default '0',
admin_hijack_enabled ENUM('0','1') default '0',
admin_monitor_enabled ENUM('0','1') default '1',
call_parking_enabled ENUM('0','1') default '1',
updater_check_enabled ENUM('0','1') default '1',
AFLogging_enabled ENUM('0','1') default '1',
QUEUE_ACTION_enabled ENUM('0','1') default '1',
CallerID_popup_enabled ENUM('0','1') default '1',
voicemail_button_enabled ENUM('0','1') default '1',
enable_fast_refresh ENUM('0','1') default '0',
fast_refresh_rate INT(5) default '1000',
enable_persistant_mysql ENUM('0','1') default '0',
auto_dial_next_number ENUM('0','1') default '1',
VDstop_rec_after_each_call ENUM('0','1') default '1',
DBX_server VARCHAR(15),
DBX_database VARCHAR(15) default 'asterisk',
DBX_user VARCHAR(15) default 'cron',
DBX_pass VARCHAR(15) default '1234',
DBX_port INT(6) default '3306',
DBY_server VARCHAR(15),
DBY_database VARCHAR(15) default 'asterisk',
DBY_user VARCHAR(15) default 'cron',
DBY_pass VARCHAR(15) default '1234',
DBY_port INT(6) default '3306',
outbound_cid VARCHAR(20),
enable_sipsak_messages ENUM('0','1') default '0',
email VARCHAR(100),
template_id VARCHAR(15) NOT NULL,
conf_override TEXT,
phone_context VARCHAR(20) default 'default',
phone_ring_timeout SMALLINT(3) default '60',
conf_secret VARCHAR(20) default 'test',
delete_vm_after_email ENUM('N','Y') default 'N',
is_webphone ENUM('Y','N') default 'N',
use_external_server_ip ENUM('Y','N') default 'N',
codecs_list VARCHAR(100) default '',
codecs_with_template ENUM('0','1') default '0',
webphone_dialpad ENUM('Y','N','TOGGLE','TOGGLE_OFF') default 'Y',
on_hook_agent ENUM('Y','N') default 'N',
webphone_auto_answer ENUM('Y','N') default 'Y',
voicemail_timezone VARCHAR(30) default 'eastern',
voicemail_options VARCHAR(255) default '',
index (server_ip),
unique index extenserver (extension, server_ip)
);

CREATE TABLE servers (
server_id VARCHAR(10) NOT NULL,
server_description VARCHAR(255),
server_ip VARCHAR(15) NOT NULL,
active ENUM('Y','N'),
asterisk_version VARCHAR(20) default '1.4.21.1',
max_vicidial_trunks SMALLINT(4) default '23',
telnet_host VARCHAR(20) NOT NULL default 'localhost',
telnet_port INT(5) NOT NULL default '5038',
ASTmgrUSERNAME VARCHAR(20) NOT NULL default 'cron',
ASTmgrSECRET VARCHAR(20) NOT NULL default '1234',
ASTmgrUSERNAMEupdate VARCHAR(20) NOT NULL default 'updatecron',
ASTmgrUSERNAMElisten VARCHAR(20) NOT NULL default 'listencron',
ASTmgrUSERNAMEsend VARCHAR(20) NOT NULL default 'sendcron',
local_gmt VARCHAR(6) default '-5.00',
voicemail_dump_exten VARCHAR(20) NOT NULL default '85026666666666',
answer_transfer_agent VARCHAR(20) NOT NULL default '8365',
ext_context VARCHAR(20) NOT NULL default 'default',
sys_perf_log ENUM('Y','N') default 'N',
vd_server_logs ENUM('Y','N') default 'Y',
agi_output ENUM('NONE','STDERR','FILE','BOTH') default 'FILE',
vicidial_balance_active ENUM('Y','N') default 'N',
balance_trunks_offlimits SMALLINT(5) UNSIGNED default '0',
recording_web_link ENUM('SERVER_IP','ALT_IP','EXTERNAL_IP') default 'SERVER_IP',
alt_server_ip VARCHAR(100) default '',
active_asterisk_server ENUM('Y','N') default 'Y',
generate_vicidial_conf ENUM('Y','N') default 'Y',
rebuild_conf_files ENUM('Y','N') default 'Y',
outbound_calls_per_second SMALLINT(3) UNSIGNED default '20',
sysload INT(6) NOT NULL default '0',
channels_total SMALLINT(4) UNSIGNED NOT NULL default '0',
cpu_idle_percent SMALLINT(3) UNSIGNED NOT NULL default '0',
disk_usage VARCHAR(255) default '1',
sounds_update ENUM('Y','N') default 'N',
vicidial_recording_limit MEDIUMINT(8) default '60',
carrier_logging_active ENUM('Y','N') default 'Y',
vicidial_balance_rank TINYINT(3) UNSIGNED default '0',
rebuild_music_on_hold ENUM('Y','N') default 'Y',
active_agent_login_server ENUM('Y','N') default 'Y',
conf_secret VARCHAR(20) default 'test',
external_server_ip VARCHAR(100) default '',
custom_dialplan_entry TEXT,
active_twin_server_ip VARCHAR(15) default ''
);

CREATE UNIQUE INDEX server_id on servers (server_id);

CREATE TABLE live_channels (
channel VARCHAR(100) NOT NULL,
server_ip VARCHAR(15) NOT NULL,
channel_group VARCHAR(30),
extension VARCHAR(100),
channel_data VARCHAR(100)
);

CREATE TABLE live_sip_channels (
channel VARCHAR(100) NOT NULL,
server_ip VARCHAR(15) NOT NULL,
channel_group VARCHAR(30),
extension VARCHAR(100),
channel_data VARCHAR(100)
);

CREATE TABLE parked_channels (
channel VARCHAR(100) NOT NULL,
server_ip VARCHAR(15) NOT NULL,
channel_group VARCHAR(30),
extension VARCHAR(100),
parked_by VARCHAR(100),
parked_time DATETIME
);

CREATE TABLE conferences (
conf_exten INT(7) UNSIGNED NOT NULL,
server_ip VARCHAR(15) NOT NULL,
extension VARCHAR(100)
);

CREATE TABLE recording_log (
recording_id INT(10) UNSIGNED AUTO_INCREMENT PRIMARY KEY NOT NULL,
channel VARCHAR(100),
server_ip VARCHAR(15),
extension VARCHAR(100),
start_time DATETIME,
start_epoch INT(10) UNSIGNED,
end_time DATETIME,
end_epoch INT(10) UNSIGNED,
length_in_sec MEDIUMINT(8) UNSIGNED,
length_in_min DOUBLE(8,2),
filename VARCHAR(50),
location VARCHAR(255),
lead_id INT(9) UNSIGNED,
user VARCHAR(20),
vicidial_id VARCHAR(20),
index(filename),
index(lead_id),
index(user),
index(vicidial_id)
);

CREATE TABLE live_inbound (
uniqueid VARCHAR(20) NOT NULL,
channel VARCHAR(100) NOT NULL,
server_ip VARCHAR(15) NOT NULL,
caller_id VARCHAR(30),
extension VARCHAR(100),
phone_ext VARCHAR(40),
start_time DATETIME,
acknowledged ENUM('Y','N') default 'N',
inbound_number VARCHAR(20),
comment_a VARCHAR(50),
comment_b VARCHAR(50),
comment_c VARCHAR(50),
comment_d VARCHAR(50),
comment_e VARCHAR(50)
);

CREATE TABLE inbound_numbers (
extension VARCHAR(30) NOT NULL,
full_number VARCHAR(30) NOT NULL,
server_ip VARCHAR(15) NOT NULL,
inbound_name VARCHAR(30),
department VARCHAR(30)
);

CREATE TABLE server_updater (
server_ip VARCHAR(15) NOT NULL,
last_update DATETIME,
db_time TIMESTAMP
);

CREATE TABLE call_log (
uniqueid VARCHAR(20) PRIMARY KEY NOT NULL,
channel VARCHAR(100),
channel_group VARCHAR(30),
type VARCHAR(10),
server_ip VARCHAR(15),
extension VARCHAR(100),
number_dialed VARCHAR(15),
caller_code VARCHAR(20),
start_time DATETIME,
start_epoch INT(10),
end_time DATETIME,
end_epoch INT(10),
length_in_sec INT(10),
length_in_min DOUBLE(8,2),
index (caller_code),
index (server_ip),
index (channel)
);

CREATE TABLE park_log (
uniqueid VARCHAR(20) default '',
status VARCHAR(10),
channel VARCHAR(100),
channel_group VARCHAR(30),
server_ip VARCHAR(15),
parked_time DATETIME,
grab_time DATETIME,
hangup_time DATETIME,
parked_sec INT(10),
talked_sec INT(10),
extension VARCHAR(100),
user VARCHAR(20),
lead_id INT(9) UNSIGNED default '0',
index (parked_time),
index (lead_id)
);

CREATE INDEX uniqueid_park on park_log (uniqueid);

CREATE TABLE vicidial_manager (
man_id INT(9) UNSIGNED AUTO_INCREMENT PRIMARY KEY NOT NULL,
uniqueid VARCHAR(20),
entry_date DATETIME,
status  ENUM('NEW','QUEUE','SENT','UPDATED','DEAD'),
response  ENUM('Y','N'),
server_ip VARCHAR(15) NOT NULL,
channel VARCHAR(100),
action VARCHAR(20),
callerid VARCHAR(20),
cmd_line_b VARCHAR(100),
cmd_line_c VARCHAR(100),
cmd_line_d VARCHAR(100),
cmd_line_e VARCHAR(100),
cmd_line_f VARCHAR(100),
cmd_line_g VARCHAR(100),
cmd_line_h VARCHAR(100),
cmd_line_i VARCHAR(100),
cmd_line_j VARCHAR(100),
cmd_line_k VARCHAR(100),
index (callerid),
index (uniqueid),
index serverstat(server_ip,status)
);

CREATE TABLE vicidial_list (
lead_id INT(9) UNSIGNED AUTO_INCREMENT PRIMARY KEY NOT NULL,
entry_date DATETIME,
modify_date TIMESTAMP,
status VARCHAR(6),
user VARCHAR(20),
vendor_lead_code VARCHAR(20),
source_id VARCHAR(50),
list_id BIGINT(14) UNSIGNED NOT NULL DEFAULT '0',
gmt_offset_now DECIMAL(4,2) DEFAULT '0.00',
called_since_last_reset ENUM('Y','N','Y1','Y2','Y3','Y4','Y5','Y6','Y7','Y8','Y9','Y10') default 'N',
phone_code VARCHAR(10),	
phone_number VARCHAR(18) NOT NULL,
title VARCHAR(4),
first_name VARCHAR(30),
middle_initial VARCHAR(1),
last_name VARCHAR(30),
address1 VARCHAR(100),
address2 VARCHAR(100),
address3 VARCHAR(100),
city VARCHAR(50),
state VARCHAR(2),
province VARCHAR(50),
postal_code VARCHAR(10),
country_code VARCHAR(3),
gender ENUM('M','F','U') default 'U',
date_of_birth DATE,
alt_phone VARCHAR(12),
email VARCHAR(70),
security_phrase VARCHAR(100),
comments VARCHAR(255),
called_count SMALLINT(5) UNSIGNED default '0',
last_local_call_time DATETIME,
rank SMALLINT(5) NOT NULL default '0',
owner VARCHAR(20) default '',
entry_list_id BIGINT(14) UNSIGNED NOT NULL DEFAULT '0',
index (phone_number),
index (list_id),
index (called_since_last_reset),
index (status),
index (gmt_offset_now),
index (postal_code),
index (last_local_call_time),
index (rank),
index (owner)
);

CREATE TABLE vicidial_hopper (
hopper_id INT(9) UNSIGNED AUTO_INCREMENT PRIMARY KEY NOT NULL,
lead_id INT(9) UNSIGNED NOT NULL,
campaign_id VARCHAR(8),			
status ENUM('READY','QUEUE','INCALL','DONE','HOLD','DNC') default 'READY',
user VARCHAR(20),			
list_id BIGINT(14) UNSIGNED NOT NULL,
gmt_offset_now DECIMAL(4,2) DEFAULT '0.00',
state VARCHAR(2) default '',
alt_dial VARCHAR(6) default 'NONE',
priority TINYINT(2) default '0',
source VARCHAR(1) default '',
index (lead_id)
);

CREATE TABLE vicidial_live_agents (
live_agent_id INT(9) UNSIGNED AUTO_INCREMENT PRIMARY KEY NOT NULL,
user VARCHAR(20),
server_ip VARCHAR(15) NOT NULL,
conf_exten VARCHAR(20),
extension VARCHAR(100),
status ENUM('READY','QUEUE','INCALL','PAUSED','CLOSER') default 'PAUSED',
lead_id INT(9) UNSIGNED NOT NULL,
campaign_id VARCHAR(8),			
uniqueid VARCHAR(20),
callerid VARCHAR(20),
channel VARCHAR(100),
random_id INT(8) UNSIGNED,
last_call_time DATETIME,
last_update_time TIMESTAMP,
last_call_finish DATETIME,
closer_campaigns TEXT,
call_server_ip VARCHAR(15),
user_level INT(2) default '0',
comments VARCHAR(20),
campaign_weight TINYINT(1) default '0',
calls_today SMALLINT(5) UNSIGNED default '0',
external_hangup VARCHAR(1) default '',
external_status VARCHAR(6) default '',
external_pause VARCHAR(20) default '',
external_dial VARCHAR(100) default '',
external_ingroups TEXT,
external_blended ENUM('0','1') default '0',
external_igb_set_user VARCHAR(20) default '',
external_update_fields ENUM('0','1') default '0',
external_update_fields_data VARCHAR(255) default '',
external_timer_action VARCHAR(20) default '',
external_timer_action_message VARCHAR(255) default '',
external_timer_action_seconds MEDIUMINT(7) default '-1',
agent_log_id INT(9) UNSIGNED default '0',
last_state_change DATETIME,
agent_territories TEXT,
outbound_autodial ENUM('Y','N') default 'N',
manager_ingroup_set ENUM('Y','N','SET') default 'N',
ra_user VARCHAR(20) default '',
ra_extension VARCHAR(100) default '',
external_dtmf VARCHAR(100) default '',
external_transferconf VARCHAR(100) default '',
external_park VARCHAR(40) default '',
external_timer_action_destination VARCHAR(100) default '',
on_hook_agent ENUM('Y','N') default 'N',
on_hook_ring_time SMALLINT(5) default '15',
ring_callerid VARCHAR(20) default '',
last_inbound_call_time DATETIME,
last_inbound_call_finish DATETIME,
index (random_id),
index (last_call_time),
index (last_update_time),
index (last_call_finish)
);

CREATE TABLE vicidial_auto_calls (
auto_call_id INT(9) UNSIGNED AUTO_INCREMENT PRIMARY KEY NOT NULL,
server_ip VARCHAR(15) NOT NULL,
campaign_id VARCHAR(20),			
status ENUM('SENT','RINGING','LIVE','XFER','PAUSED','CLOSER','BUSY','DISCONNECT','IVR') default 'PAUSED',
lead_id INT(9) UNSIGNED NOT NULL,
uniqueid VARCHAR(20),
callerid VARCHAR(20),
channel VARCHAR(100),
phone_code VARCHAR(10),
phone_number VARCHAR(18),
call_time DATETIME,
call_type ENUM('IN','OUT','OUTBALANCE') default 'OUT',
stage VARCHAR(20) default 'START',
last_update_time TIMESTAMP,
alt_dial VARCHAR(6) default 'NONE',
queue_priority TINYINT(2) default '0',
agent_only VARCHAR(20) default '',
agent_grab VARCHAR(20) default '',
queue_position SMALLINT(4) UNSIGNED default '1',
extension VARCHAR(100) default '',
agent_grab_extension VARCHAR(100) default '',
index (uniqueid),
index (callerid),
index (call_time),
index (last_update_time)
);

CREATE TABLE vicidial_log (
uniqueid VARCHAR(20) PRIMARY KEY NOT NULL,
lead_id INT(9) UNSIGNED NOT NULL,
list_id BIGINT(14) UNSIGNED,
campaign_id VARCHAR(8),
call_date DATETIME,
start_epoch INT(10) UNSIGNED,
end_epoch INT(10) UNSIGNED,
length_in_sec INT(10),
status VARCHAR(6),
phone_code VARCHAR(10),
phone_number VARCHAR(18),
user VARCHAR(20),
comments VARCHAR(255),
processed ENUM('Y','N'),
user_group VARCHAR(20),
term_reason  ENUM('CALLER','AGENT','QUEUETIMEOUT','ABANDON','AFTERHOURS','NONE') default 'NONE',
alt_dial VARCHAR(6) default 'NONE',
index (lead_id),
index (call_date)
);

CREATE TABLE vicidial_closer_log (
closecallid INT(9) UNSIGNED AUTO_INCREMENT PRIMARY KEY NOT NULL,
lead_id INT(9) UNSIGNED NOT NULL,
list_id BIGINT(14) UNSIGNED,
campaign_id VARCHAR(20),
call_date DATETIME,
start_epoch INT(10) UNSIGNED,
end_epoch INT(10) UNSIGNED,
length_in_sec INT(10),
status VARCHAR(6),
phone_code VARCHAR(10),
phone_number VARCHAR(18),
user VARCHAR(20),
comments VARCHAR(255),
processed ENUM('Y','N'),
queue_seconds DECIMAL(7,2) default '0',
user_group VARCHAR(20),
xfercallid INT(9) UNSIGNED,
term_reason  ENUM('CALLER','AGENT','QUEUETIMEOUT','ABANDON','AFTERHOURS','HOLDRECALLXFER','HOLDTIME','NOAGENT','NONE') default 'NONE',
uniqueid VARCHAR(20) NOT NULL default '',
agent_only VARCHAR(20) default '',
queue_position SMALLINT(4) UNSIGNED default '1',
index (lead_id),
index (call_date),
index (campaign_id),
index (uniqueid)
);

CREATE TABLE vicidial_xfer_log (
xfercallid INT(9) UNSIGNED AUTO_INCREMENT PRIMARY KEY NOT NULL,
lead_id INT(9) UNSIGNED NOT NULL,
list_id BIGINT(14) UNSIGNED,
campaign_id VARCHAR(20),
call_date DATETIME,
phone_code VARCHAR(10),
phone_number VARCHAR(18),
user VARCHAR(20),
closer VARCHAR(20),
index (lead_id),
index (call_date)
);

CREATE TABLE vicidial_users (
user_id INT(9) UNSIGNED AUTO_INCREMENT PRIMARY KEY NOT NULL,
user VARCHAR(20) NOT NULL,
pass VARCHAR(20) NOT NULL,
full_name VARCHAR(50),
user_level TINYINT(2) default '1',
user_group VARCHAR(20),
phone_login VARCHAR(20),
phone_pass VARCHAR(20),
delete_users ENUM('0','1') default '0',
delete_user_groups ENUM('0','1') default '0',
delete_lists ENUM('0','1') default '0',
delete_campaigns ENUM('0','1') default '0',
delete_ingroups ENUM('0','1') default '0',
delete_remote_agents ENUM('0','1') default '0',
load_leads ENUM('0','1') default '0',
campaign_detail ENUM('0','1') default '0',
ast_admin_access ENUM('0','1') default '0',
ast_delete_phones ENUM('0','1') default '0',
delete_scripts ENUM('0','1') default '0',
modify_leads ENUM('0','1') default '0',
hotkeys_active ENUM('0','1') default '0',
change_agent_campaign ENUM('0','1') default '0',
agent_choose_ingroups ENUM('0','1') default '1',
closer_campaigns TEXT,
scheduled_callbacks ENUM('0','1') default '1',
agentonly_callbacks ENUM('0','1') default '0',
agentcall_manual ENUM('0','1') default '0',
vicidial_recording ENUM('0','1') default '1',
vicidial_transfers ENUM('0','1') default '1',
delete_filters ENUM('0','1') default '0',
alter_agent_interface_options ENUM('0','1') default '0',
closer_default_blended ENUM('0','1') default '0',
delete_call_times ENUM('0','1') default '0',
modify_call_times ENUM('0','1') default '0',
modify_users ENUM('0','1') default '0',
modify_campaigns ENUM('0','1') default '0',
modify_lists ENUM('0','1') default '0',
modify_scripts ENUM('0','1') default '0',
modify_filters ENUM('0','1') default '0',
modify_ingroups ENUM('0','1') default '0',
modify_usergroups ENUM('0','1') default '0',
modify_remoteagents ENUM('0','1') default '0',
modify_servers ENUM('0','1') default '0',
view_reports ENUM('0','1') default '0',
vicidial_recording_override ENUM('DISABLED','NEVER','ONDEMAND','ALLCALLS','ALLFORCE') default 'DISABLED',
alter_custdata_override ENUM('NOT_ACTIVE','ALLOW_ALTER') default 'NOT_ACTIVE',
qc_enabled ENUM('0','1') default '0',
qc_user_level INT(2) default '1',
qc_pass ENUM('0','1') default '0',
qc_finish ENUM('0','1') default '0',
qc_commit ENUM('0','1') default '0',
add_timeclock_log ENUM('0','1') default '0',
modify_timeclock_log ENUM('0','1') default '0',
delete_timeclock_log ENUM('0','1') default '0',
alter_custphone_override ENUM('NOT_ACTIVE','ALLOW_ALTER') default 'NOT_ACTIVE',
vdc_agent_api_access ENUM('0','1') default '0',
modify_inbound_dids ENUM('0','1') default '0',
delete_inbound_dids ENUM('0','1') default '0',
active ENUM('Y','N') default 'Y',
alert_enabled ENUM('0','1') default '0',
download_lists ENUM('0','1') default '0',
agent_shift_enforcement_override ENUM('DISABLED','OFF','START','ALL') default 'DISABLED',
manager_shift_enforcement_override ENUM('0','1') default '0',
shift_override_flag ENUM('0','1') default '0',
export_reports ENUM('0','1') default '0',
delete_from_dnc ENUM('0','1') default '0',
email VARCHAR(100) default '',
user_code VARCHAR(100) default '',
territory VARCHAR(100) default '',
allow_alerts ENUM('0','1') default '0',
agent_choose_territories ENUM('0','1') default '1',
custom_one VARCHAR(100) default '',
custom_two VARCHAR(100) default '',
custom_three VARCHAR(100) default '',
custom_four VARCHAR(100) default '',
custom_five VARCHAR(100) default '',
voicemail_id VARCHAR(10),
agent_call_log_view_override ENUM('DISABLED','Y','N') default 'DISABLED',
callcard_admin ENUM('1','0') default '0',
agent_choose_blended ENUM('0','1') default '1',
realtime_block_user_info ENUM('0','1') default '0',
custom_fields_modify ENUM('0','1') default '0',
force_change_password ENUM('Y','N') default 'N',
agent_lead_search_override ENUM('NOT_ACTIVE','ENABLED','DISABLED') default 'NOT_ACTIVE',
modify_shifts ENUM('1','0') default '0',
modify_phones ENUM('1','0') default '0',
modify_carriers ENUM('1','0') default '0',
modify_labels ENUM('1','0') default '0',
modify_statuses ENUM('1','0') default '0',
modify_voicemail ENUM('1','0') default '0',
modify_audiostore ENUM('1','0') default '0',
modify_moh ENUM('1','0') default '0',
modify_tts ENUM('1','0') default '0'
);

CREATE UNIQUE INDEX user ON vicidial_users (user);

CREATE TABLE vicidial_user_log (
user_log_id INT(9) UNSIGNED AUTO_INCREMENT PRIMARY KEY NOT NULL,
user VARCHAR(20),
event VARCHAR(50),
campaign_id VARCHAR(8),
event_date DATETIME,
event_epoch INT(10) UNSIGNED,
user_group VARCHAR(20),
session_id VARCHAR(20),
server_ip VARCHAR(15),
extension VARCHAR(50),
computer_ip VARCHAR(15),
browser VARCHAR(255),
data VARCHAR(255),
index (user)
);

CREATE TABLE vicidial_user_groups (
user_group VARCHAR(20) NOT NULL,
group_name VARCHAR(40) NOT NULL,
allowed_campaigns TEXT,
qc_allowed_campaigns TEXT,
qc_allowed_inbound_groups TEXT,
group_shifts TEXT,
forced_timeclock_login ENUM('Y','N','ADMIN_EXEMPT') default 'N',
shift_enforcement ENUM('OFF','START','ALL') default 'OFF',
agent_status_viewable_groups TEXT,
agent_status_view_time ENUM('Y','N') default 'N',
agent_call_log_view ENUM('Y','N') default 'N',
agent_xfer_consultative ENUM('Y','N') default 'Y',
agent_xfer_dial_override ENUM('Y','N') default 'Y',
agent_xfer_vm_transfer ENUM('Y','N') default 'Y',
agent_xfer_blind_transfer ENUM('Y','N') default 'Y',
agent_xfer_dial_with_customer ENUM('Y','N') default 'Y',
agent_xfer_park_customer_dial ENUM('Y','N') default 'Y',
agent_fullscreen ENUM('Y','N') default 'N',
allowed_reports VARCHAR(2000) default 'ALL REPORTS',
webphone_url_override VARCHAR(255) default '',
webphone_systemkey_override VARCHAR(100) default '',
webphone_dialpad_override ENUM('DISABLED','Y','N','TOGGLE','TOGGLE_OFF') default 'DISABLED'
);

CREATE TABLE vicidial_campaigns (
campaign_id VARCHAR(8) PRIMARY KEY NOT NULL,
campaign_name VARCHAR(40),
active ENUM('Y','N'),
dial_status_a VARCHAR(6),
dial_status_b VARCHAR(6),
dial_status_c VARCHAR(6),
dial_status_d VARCHAR(6),
dial_status_e VARCHAR(6),
lead_order VARCHAR(30),
park_ext VARCHAR(10),
park_file_name VARCHAR(100) default 'default',
web_form_address TEXT,
allow_closers ENUM('Y','N'),
hopper_level INT(8) UNSIGNED default '1',
auto_dial_level VARCHAR(6) default '0',
next_agent_call ENUM('random','oldest_call_start','oldest_call_finish','campaign_rank','overall_user_level','fewest_calls','longest_wait_time') default 'longest_wait_time',
local_call_time VARCHAR(10) DEFAULT '9am-9pm',
voicemail_ext VARCHAR(10),
dial_timeout TINYINT UNSIGNED default '60',
dial_prefix VARCHAR(20) default '9',
campaign_cid VARCHAR(20) default '0000000000',
campaign_vdad_exten VARCHAR(20) default '8368',
campaign_rec_exten VARCHAR(20) default '8309',
campaign_recording ENUM('NEVER','ONDEMAND','ALLCALLS','ALLFORCE') default 'ONDEMAND',
campaign_rec_filename VARCHAR(50) default 'FULLDATE_CUSTPHONE',
campaign_script VARCHAR(10),
get_call_launch ENUM('NONE','SCRIPT','WEBFORM','WEBFORMTWO','FORM') default 'NONE',
am_message_exten VARCHAR(100) default 'vm-goodbye',
amd_send_to_vmx ENUM('Y','N') default 'N',
xferconf_a_dtmf VARCHAR(50),
xferconf_a_number VARCHAR(50),
xferconf_b_dtmf VARCHAR(50),
xferconf_b_number VARCHAR(50),
alt_number_dialing ENUM('Y','N') default 'N',
scheduled_callbacks ENUM('Y','N') default 'N',
lead_filter_id VARCHAR(10) default 'NONE',
drop_call_seconds TINYINT(3) default '5',
drop_action ENUM('HANGUP','MESSAGE','VOICEMAIL','IN_GROUP','AUDIO','CALLMENU') default 'AUDIO',
safe_harbor_exten VARCHAR(20)  default '8307',
display_dialable_count ENUM('Y','N') default 'Y',
wrapup_seconds SMALLINT(3) UNSIGNED default '0',
wrapup_message VARCHAR(255) default 'Wrapup Call',
closer_campaigns TEXT default '',
use_internal_dnc ENUM('Y','N','AREACODE') default 'N',
allcalls_delay SMALLINT(3) UNSIGNED default '0',
omit_phone_code ENUM('Y','N') default 'N',
dial_method ENUM('MANUAL','RATIO','ADAPT_HARD_LIMIT','ADAPT_TAPERED','ADAPT_AVERAGE','INBOUND_MAN') default 'MANUAL',
available_only_ratio_tally ENUM('Y','N') default 'N',
adaptive_dropped_percentage VARCHAR(4) default '3',
adaptive_maximum_level VARCHAR(6) default '3.0',
adaptive_latest_server_time VARCHAR(4) default '2100',
adaptive_intensity VARCHAR(6) default '0',
adaptive_dl_diff_target SMALLINT(3) default '0',
concurrent_transfers ENUM('AUTO','1','2','3','4','5','6','7','8','9','10','15','20','25','30','40','50','60','80','100') default 'AUTO',
auto_alt_dial ENUM('NONE','ALT_ONLY','ADDR3_ONLY','ALT_AND_ADDR3','ALT_AND_EXTENDED','ALT_AND_ADDR3_AND_EXTENDED','EXTENDED_ONLY','MULTI_LEAD') default 'NONE',
auto_alt_dial_statuses VARCHAR(255) default ' B N NA DC -',
agent_pause_codes_active ENUM('Y','N','FORCE') default 'N',
campaign_description VARCHAR(255),
campaign_changedate DATETIME,
campaign_stats_refresh ENUM('Y','N') default 'N',
campaign_logindate DATETIME,
dial_statuses VARCHAR(255) default ' NEW -',
disable_alter_custdata ENUM('Y','N') default 'N',
no_hopper_leads_logins ENUM('Y','N') default 'N',
list_order_mix VARCHAR(20) default 'DISABLED',
campaign_allow_inbound ENUM('Y','N') default 'N',
manual_dial_list_id BIGINT(14) UNSIGNED default '998',
default_xfer_group VARCHAR(20) default '---NONE---',
xfer_groups  TEXT default '',
queue_priority TINYINT(2) default '50',
drop_inbound_group VARCHAR(20) default '---NONE---',
qc_enabled ENUM('Y','N') default 'N',
qc_statuses TEXT,
qc_lists TEXT,
qc_shift_id VARCHAR(20) default '24HRMIDNIGHT',
qc_get_record_launch ENUM('NONE','SCRIPT','WEBFORM','QCSCRIPT','QCWEBFORM') default 'NONE',
qc_show_recording ENUM('Y','N') default 'Y',
qc_web_form_address VARCHAR(255),
qc_script VARCHAR(10),
survey_first_audio_file VARCHAR(50) default 'US_pol_survey_hello',
survey_dtmf_digits VARCHAR(16) default '1238',
survey_ni_digit VARCHAR(1) default '8',
survey_opt_in_audio_file VARCHAR(50) default 'US_pol_survey_transfer',
survey_ni_audio_file VARCHAR(50) default 'US_thanks_no_contact',
survey_method ENUM('AGENT_XFER','VOICEMAIL','EXTENSION','HANGUP','CAMPREC_60_WAV','CALLMENU') default 'AGENT_XFER',
survey_no_response_action ENUM('OPTIN','OPTOUT') default 'OPTIN',
survey_ni_status VARCHAR(6) default 'NI',
survey_response_digit_map VARCHAR(255) default '1-DEMOCRAT|2-REPUBLICAN|3-INDEPENDANT|8-OPTOUT|X-NO RESPONSE|',
survey_xfer_exten VARCHAR(20) default '8300',
survey_camp_record_dir VARCHAR(255) default '/home/survey',
disable_alter_custphone ENUM('Y','N','HIDE') default 'Y',
display_queue_count ENUM('Y','N') default 'Y',
manual_dial_filter VARCHAR(50) default 'NONE',
agent_clipboard_copy VARCHAR(50) default 'NONE',
agent_extended_alt_dial ENUM('Y','N') default 'N',
use_campaign_dnc ENUM('Y','N','AREACODE') default 'N',
three_way_call_cid ENUM('CAMPAIGN','CUSTOMER','AGENT_PHONE','AGENT_CHOOSE','CUSTOM_CID') default 'CAMPAIGN',
three_way_dial_prefix VARCHAR(20) default '',
web_form_target VARCHAR(100) NOT NULL default 'vdcwebform',
vtiger_search_category VARCHAR(100) default 'LEAD',
vtiger_create_call_record ENUM('Y','N','DISPO') default 'Y',
vtiger_create_lead_record ENUM('Y','N') default 'Y',
vtiger_screen_login ENUM('Y','N','NEW_WINDOW') default 'Y',
cpd_amd_action ENUM('DISABLED','DISPO','MESSAGE') default 'DISABLED',
agent_allow_group_alias ENUM('Y','N') default 'N',
default_group_alias VARCHAR(30) default '',
vtiger_search_dead ENUM('DISABLED','ASK','RESURRECT') default 'ASK',
vtiger_status_call ENUM('Y','N') default 'N',
survey_third_digit VARCHAR(1) default '',
survey_third_audio_file VARCHAR(50) default 'US_thanks_no_contact',
survey_third_status VARCHAR(6) default 'NI',
survey_third_exten VARCHAR(20) default '8300',
survey_fourth_digit VARCHAR(1) default '',
survey_fourth_audio_file VARCHAR(50) default 'US_thanks_no_contact',
survey_fourth_status VARCHAR(6) default 'NI',
survey_fourth_exten VARCHAR(20) default '8300',
drop_lockout_time VARCHAR(6) default '0',
quick_transfer_button VARCHAR(20) default 'N',
prepopulate_transfer_preset ENUM('N','PRESET_1','PRESET_2','PRESET_3','PRESET_4','PRESET_5') default 'N',
drop_rate_group VARCHAR(20) default 'DISABLED',
view_calls_in_queue ENUM('NONE','ALL','1','2','3','4','5') default 'NONE',
view_calls_in_queue_launch ENUM('AUTO','MANUAL') default 'MANUAL',
grab_calls_in_queue ENUM('Y','N') default 'N',
call_requeue_button ENUM('Y','N') default 'N',
pause_after_each_call ENUM('Y','N') default 'N',
no_hopper_dialing ENUM('Y','N') default 'N',
agent_dial_owner_only ENUM('NONE','USER','TERRITORY','USER_GROUP') default 'NONE',
agent_display_dialable_leads ENUM('Y','N') default 'N',
web_form_address_two TEXT,
waitforsilence_options VARCHAR(25) default '',
agent_select_territories ENUM('Y','N') default 'N',
campaign_calldate DATETIME,
crm_popup_login ENUM('Y','N') default 'N',
crm_login_address TEXT,
timer_action ENUM('NONE','WEBFORM','WEBFORM2','D1_DIAL','D2_DIAL','D3_DIAL','D4_DIAL','D5_DIAL','MESSAGE_ONLY','HANGUP','CALLMENU','EXTENSION','IN_GROUP') default 'NONE',
timer_action_message VARCHAR(255) default '',
timer_action_seconds MEDIUMINT(7) default '-1',
start_call_url TEXT,
dispo_call_url TEXT,
xferconf_c_number VARCHAR(50) default '',
xferconf_d_number VARCHAR(50) default '',
xferconf_e_number VARCHAR(50) default '',
use_custom_cid ENUM('Y','N','AREACODE') default 'N',
scheduled_callbacks_alert ENUM('NONE','BLINK','RED','BLINK_RED','BLINK_DEFER','RED_DEFER','BLINK_RED_DEFER') default 'NONE',
queuemetrics_callstatus_override ENUM('DISABLED','NO','YES') default 'DISABLED',
extension_appended_cidname ENUM('Y','N') default 'N',
scheduled_callbacks_count ENUM('LIVE','ALL_ACTIVE') default 'ALL_ACTIVE',
manual_dial_override ENUM('NONE','ALLOW_ALL','DISABLE_ALL') default 'NONE',
blind_monitor_warning ENUM('DISABLED','ALERT','NOTICE','AUDIO','ALERT_NOTICE','ALERT_AUDIO','NOTICE_AUDIO','ALL') default 'DISABLED',
blind_monitor_message VARCHAR(255) default 'Someone is blind monitoring your session',
blind_monitor_filename VARCHAR(100) default '',
inbound_queue_no_dial ENUM('DISABLED','ENABLED','ALL_SERVERS') default 'DISABLED',
timer_action_destination VARCHAR(30) default '',
enable_xfer_presets ENUM('DISABLED','ENABLED') default 'DISABLED',
hide_xfer_number_to_dial ENUM('DISABLED','ENABLED') default 'DISABLED',
manual_dial_prefix VARCHAR(20) default '',
customer_3way_hangup_logging ENUM('DISABLED','ENABLED') default 'ENABLED',
customer_3way_hangup_seconds SMALLINT(5) UNSIGNED default '5',
customer_3way_hangup_action ENUM('NONE','DISPO') default 'NONE',
ivr_park_call ENUM('DISABLED','ENABLED','ENABLED_PARK_ONLY','ENABLED_BUTTON_HIDDEN') default 'DISABLED',
ivr_park_call_agi TEXT,
manual_preview_dial ENUM('DISABLED','PREVIEW_AND_SKIP','PREVIEW_ONLY') default 'PREVIEW_AND_SKIP',
realtime_agent_time_stats ENUM('DISABLED','WAIT_CUST_ACW','WAIT_CUST_ACW_PAUSE','CALLS_WAIT_CUST_ACW_PAUSE') default 'CALLS_WAIT_CUST_ACW_PAUSE',
use_auto_hopper ENUM('Y','N') default 'Y',
auto_hopper_multi VARCHAR(6) default '1',
auto_hopper_level MEDIUMINT(8) UNSIGNED default '0',
auto_trim_hopper ENUM('Y','N') default 'Y',
api_manual_dial ENUM('STANDARD','QUEUE','QUEUE_AND_AUTOCALL') default 'STANDARD',
manual_dial_call_time_check ENUM('DISABLED','ENABLED') default 'DISABLED',
display_leads_count ENUM('Y','N') default 'N',
lead_order_randomize ENUM('Y','N') default 'N',
lead_order_secondary ENUM('LEAD_ASCEND','LEAD_DESCEND','CALLTIME_ASCEND','CALLTIME_DESCEND') default 'LEAD_ASCEND',
per_call_notes ENUM('ENABLED','DISABLED') default 'DISABLED',
my_callback_option ENUM('CHECKED','UNCHECKED') default 'UNCHECKED',
agent_lead_search ENUM('ENABLED','DISABLED') default 'DISABLED',
agent_lead_search_method VARCHAR(30) default 'CAMPLISTS_ALL',
queuemetrics_phone_environment VARCHAR(20) default '',
auto_pause_precall ENUM('Y','N') default 'N',
auto_pause_precall_code VARCHAR(6) default 'PRECAL',
auto_resume_precall ENUM('Y','N') default 'N',
manual_dial_cid ENUM('CAMPAIGN','AGENT_PHONE') default 'CAMPAIGN',
post_phone_time_diff_alert VARCHAR(30) default 'DISABLED',
custom_3way_button_transfer VARCHAR(30) default 'DISABLED',
available_only_tally_threshold ENUM('DISABLED','LOGGED-IN_AGENTS','NON-PAUSED_AGENTS','WAITING_AGENTS') default 'DISABLED',
available_only_tally_threshold_agents SMALLINT(5) UNSIGNED default '0',
dial_level_threshold ENUM('DISABLED','LOGGED-IN_AGENTS','NON-PAUSED_AGENTS','WAITING_AGENTS') default 'DISABLED',
dial_level_threshold_agents SMALLINT(5) UNSIGNED default '0',
safe_harbor_audio VARCHAR(100) default 'buzz',
safe_harbor_menu_id VARCHAR(50) default '',
survey_menu_id VARCHAR(50) default '',
callback_days_limit SMALLINT(3) default '0',
dl_diff_target_method ENUM('ADAPT_CALC_ONLY','CALLS_PLACED') default 'ADAPT_CALC_ONLY',
disable_dispo_screen ENUM('DISPO_ENABLED','DISPO_DISABLED','DISPO_SELECT_DISABLED') default 'DISPO_ENABLED',
disable_dispo_status VARCHAR(6) default '',
screen_labels VARCHAR(20) default '--SYSTEM-SETTINGS--',
status_display_fields VARCHAR(30) default 'CALLID',
na_call_url TEXT,
survey_recording ENUM('Y','N','Y_WITH_AMD') default 'N',
pllb_grouping ENUM('DISABLED','ONE_SERVER_ONLY','CASCADING') default 'DISABLED',
pllb_grouping_limit SMALLINT(5) default '50',
call_count_limit SMALLINT(5) UNSIGNED default '0',
call_count_target SMALLINT(5) UNSIGNED default '3'
);

CREATE TABLE vicidial_lists (
list_id BIGINT(14) UNSIGNED PRIMARY KEY NOT NULL,
list_name VARCHAR(30),
campaign_id VARCHAR(8),
active ENUM('Y','N'),
list_description VARCHAR(255),
list_changedate DATETIME,
list_lastcalldate DATETIME,
reset_time VARCHAR(100) default '',
agent_script_override VARCHAR(10) default '',
campaign_cid_override VARCHAR(20) default '',
am_message_exten_override VARCHAR(100) default '',
drop_inbound_group_override VARCHAR(20) default '',
xferconf_a_number VARCHAR(50) default '',
xferconf_b_number VARCHAR(50) default '',
xferconf_c_number VARCHAR(50) default '',
xferconf_d_number VARCHAR(50) default '',
xferconf_e_number VARCHAR(50) default '',
web_form_address TEXT,
web_form_address_two TEXT,
time_zone_setting ENUM('COUNTRY_AND_AREA_CODE','POSTAL_CODE','NANPA_PREFIX','OWNER_TIME_ZONE_CODE') default 'COUNTRY_AND_AREA_CODE'
);

CREATE TABLE vicidial_statuses (
status VARCHAR(6) PRIMARY KEY NOT NULL,
status_name VARCHAR(30),
selectable ENUM('Y','N'),
human_answered ENUM('Y','N') default 'N',
category VARCHAR(20) default 'UNDEFINED',
sale ENUM('Y','N') default 'N',
dnc ENUM('Y','N') default 'N',
customer_contact ENUM('Y','N') default 'N',
not_interested ENUM('Y','N') default 'N',
unworkable ENUM('Y','N') default 'N',
scheduled_callback ENUM('Y','N') default 'N',
completed ENUM('Y','N') default 'N'
);

CREATE TABLE vicidial_campaign_statuses (
status VARCHAR(6) NOT NULL,
status_name VARCHAR(30),
selectable ENUM('Y','N'),
campaign_id VARCHAR(8),
human_answered ENUM('Y','N') default 'N',
category VARCHAR(20) default 'UNDEFINED',
sale ENUM('Y','N') default 'N',
dnc ENUM('Y','N') default 'N',
customer_contact ENUM('Y','N') default 'N',
not_interested ENUM('Y','N') default 'N',
unworkable ENUM('Y','N') default 'N',
scheduled_callback ENUM('Y','N') default 'N',
completed ENUM('Y','N') default 'N',
index (campaign_id)
);

CREATE TABLE vicidial_campaign_hotkeys (
status VARCHAR(6) NOT NULL,
hotkey VARCHAR(1) NOT NULL,
status_name VARCHAR(30),
selectable ENUM('Y','N'),
campaign_id VARCHAR(8),
index (campaign_id)
);

CREATE TABLE vicidial_conferences (
conf_exten INT(7) UNSIGNED NOT NULL,
server_ip VARCHAR(15) NOT NULL,
extension VARCHAR(100),
leave_3way ENUM('0','1') default '0',
leave_3way_datetime DATETIME
);

CREATE UNIQUE INDEX serverconf on vicidial_conferences (server_ip, conf_exten);

CREATE TABLE vicidial_phone_codes (
country_code SMALLINT(5) UNSIGNED,
country CHAR(3),
areacode CHAR(3),
state VARCHAR(4),
GMT_offset VARCHAR(6),
DST enum('Y','N'),
DST_range VARCHAR(8),
geographic_description VARCHAR(30),
tz_code VARCHAR(4) default ''
);

CREATE TABLE vicidial_inbound_groups (
group_id VARCHAR(20) PRIMARY KEY NOT NULL,
group_name VARCHAR(30),
group_color VARCHAR(7),
active ENUM('Y','N'),
web_form_address TEXT,
voicemail_ext VARCHAR(10),
next_agent_call VARCHAR(30) default 'longest_wait_time',
fronter_display ENUM('Y','N') default 'Y',
ingroup_script VARCHAR(10),
get_call_launch ENUM('NONE','SCRIPT','WEBFORM','WEBFORMTWO','FORM') default 'NONE',
xferconf_a_dtmf VARCHAR(50),
xferconf_a_number VARCHAR(50),
xferconf_b_dtmf VARCHAR(50),
xferconf_b_number VARCHAR(50),
drop_call_seconds SMALLINT(4) unsigned default '360',
drop_action ENUM('HANGUP','MESSAGE','VOICEMAIL','IN_GROUP','CALLMENU') default 'MESSAGE',
drop_exten VARCHAR(20)  default '8307',
call_time_id VARCHAR(20) default '24hours',
after_hours_action ENUM('HANGUP','MESSAGE','EXTENSION','VOICEMAIL','IN_GROUP','CALLMENU') default 'MESSAGE',
after_hours_message_filename VARCHAR(255) default 'vm-goodbye',
after_hours_exten VARCHAR(20) default '8300',
after_hours_voicemail VARCHAR(20),
welcome_message_filename VARCHAR(255) default '---NONE---',
moh_context VARCHAR(50) default 'default',
onhold_prompt_filename VARCHAR(255) default 'generic_hold',
prompt_interval SMALLINT(5) UNSIGNED default '60',
agent_alert_exten VARCHAR(100) default 'ding',
agent_alert_delay INT(6) default '1000',
default_xfer_group VARCHAR(20) default '---NONE---',
queue_priority TINYINT(2) default '0',
drop_inbound_group VARCHAR(20) default '---NONE---',
ingroup_recording_override  ENUM('DISABLED','NEVER','ONDEMAND','ALLCALLS','ALLFORCE') default 'DISABLED',
ingroup_rec_filename VARCHAR(50) default 'NONE',
afterhours_xfer_group VARCHAR(20) default '---NONE---',
qc_enabled ENUM('Y','N') default 'N',
qc_statuses TEXT,
qc_shift_id VARCHAR(20) default '24HRMIDNIGHT',
qc_get_record_launch ENUM('NONE','SCRIPT','WEBFORM','QCSCRIPT','QCWEBFORM') default 'NONE',
qc_show_recording ENUM('Y','N') default 'Y',
qc_web_form_address VARCHAR(255),
qc_script VARCHAR(10),
play_place_in_line ENUM('Y','N') default 'N',
play_estimate_hold_time ENUM('Y','N') default 'N',
hold_time_option VARCHAR(30) default 'NONE',
hold_time_option_seconds SMALLINT(5) default '360',
hold_time_option_exten VARCHAR(20) default '8300',
hold_time_option_voicemail VARCHAR(20) default '',
hold_time_option_xfer_group VARCHAR(20) default '---NONE---',
hold_time_option_callback_filename VARCHAR(255) default 'vm-hangup',
hold_time_option_callback_list_id BIGINT(14) UNSIGNED default '999',
hold_recall_xfer_group VARCHAR(20) default '---NONE---',
no_delay_call_route ENUM('Y','N') default 'N',
play_welcome_message ENUM('ALWAYS','NEVER','IF_WAIT_ONLY','YES_UNLESS_NODELAY') default 'ALWAYS',
answer_sec_pct_rt_stat_one SMALLINT(5) UNSIGNED default '20',
answer_sec_pct_rt_stat_two SMALLINT(5) UNSIGNED default '30',
default_group_alias VARCHAR(30) default '',
no_agent_no_queue ENUM('N','Y','NO_PAUSED') default 'N',
no_agent_action ENUM('CALLMENU','INGROUP','DID','MESSAGE','EXTENSION','VOICEMAIL') default 'MESSAGE',
no_agent_action_value VARCHAR(255) default 'nbdy-avail-to-take-call|vm-goodbye',
web_form_address_two TEXT,
timer_action ENUM('NONE','WEBFORM','WEBFORM2','D1_DIAL','D2_DIAL','D3_DIAL','D4_DIAL','D5_DIAL','MESSAGE_ONLY','HANGUP','CALLMENU','EXTENSION','IN_GROUP') default 'NONE',
timer_action_message VARCHAR(255) default '',
timer_action_seconds MEDIUMINT(7) default '-1',
start_call_url TEXT,
dispo_call_url TEXT,
xferconf_c_number VARCHAR(50) default '',
xferconf_d_number VARCHAR(50) default '',
xferconf_e_number VARCHAR(50) default '',
ignore_list_script_override ENUM('Y','N') default 'N',
extension_appended_cidname ENUM('Y','N') default 'N',
uniqueid_status_display ENUM('DISABLED','ENABLED','ENABLED_PREFIX','ENABLED_PRESERVE') default 'DISABLED',
uniqueid_status_prefix VARCHAR(50) default '',
hold_time_option_minimum SMALLINT(5) default '0',
hold_time_option_press_filename VARCHAR(255) default 'to-be-called-back|digits/1',
hold_time_option_callmenu VARCHAR(50) default '',
hold_time_option_no_block ENUM('N','Y') default 'N',
hold_time_option_prompt_seconds SMALLINT(5) default '10',
onhold_prompt_no_block ENUM('N','Y') default 'N',
onhold_prompt_seconds SMALLINT(5) default '10',
hold_time_second_option VARCHAR(30) default 'NONE',
hold_time_third_option VARCHAR(30) default 'NONE',
wait_hold_option_priority ENUM('WAIT','HOLD','BOTH') default 'WAIT',
wait_time_option VARCHAR(30) default 'NONE',
wait_time_second_option VARCHAR(30) default 'NONE',
wait_time_third_option VARCHAR(30) default 'NONE',
wait_time_option_seconds SMALLINT(5) default '120',
wait_time_option_exten VARCHAR(20) default '8300',
wait_time_option_voicemail VARCHAR(20) default '',
wait_time_option_xfer_group VARCHAR(20) default '---NONE---',
wait_time_option_callmenu VARCHAR(50) default '',
wait_time_option_callback_filename VARCHAR(255) default 'vm-hangup',
wait_time_option_callback_list_id BIGINT(14) UNSIGNED default '999',
wait_time_option_press_filename VARCHAR(255) default 'to-be-called-back|digits/1',
wait_time_option_no_block ENUM('N','Y') default 'N',
wait_time_option_prompt_seconds SMALLINT(5) default '10',
timer_action_destination VARCHAR(30) default '',
calculate_estimated_hold_seconds SMALLINT(5) UNSIGNED default '0',
add_lead_url TEXT,
eht_minimum_prompt_filename VARCHAR(255) default '',
eht_minimum_prompt_no_block ENUM('N','Y') default 'N',
eht_minimum_prompt_seconds SMALLINT(5) default '10',
on_hook_ring_time SMALLINT(5) default '15',
na_call_url TEXT,
on_hook_cid VARCHAR(30) default 'GENERIC',
group_calldate DATETIME,
action_xfer_cid VARCHAR(18) default 'CUSTOMER',
drop_callmenu VARCHAR(50) default '',
after_hours_callmenu VARCHAR(50) default ''
);

CREATE TABLE vicidial_stations (
agent_station VARCHAR(10) PRIMARY KEY NOT NULL,
phone_channel VARCHAR(100),
computer_ip VARCHAR(15) NOT NULL,
server_ip VARCHAR(15) NOT NULL,
DB_server_ip VARCHAR(15) NOT NULL,
DB_user VARCHAR(15),
DB_pass VARCHAR(15),
DB_port VARCHAR(6)
);

CREATE TABLE vicidial_remote_agents (
remote_agent_id INT(9) UNSIGNED AUTO_INCREMENT PRIMARY KEY NOT NULL,
user_start VARCHAR(20),
number_of_lines TINYINT UNSIGNED default '1',
server_ip VARCHAR(15) NOT NULL,
conf_exten VARCHAR(20),
status ENUM('ACTIVE','INACTIVE') default 'INACTIVE',
campaign_id VARCHAR(8),
closer_campaigns TEXT,
extension_group VARCHAR(20) default 'NONE',
extension_group_order VARCHAR(20) default 'NONE',
on_hook_agent ENUM('Y','N') default 'N',
on_hook_ring_time SMALLINT(5) default '15'
);

CREATE TABLE live_inbound_log (
uniqueid VARCHAR(20) NOT NULL,
channel VARCHAR(100) NOT NULL,
server_ip VARCHAR(15) NOT NULL,
caller_id VARCHAR(30),
extension VARCHAR(100),
phone_ext VARCHAR(40),
start_time DATETIME,
acknowledged ENUM('Y','N') default 'N',
inbound_number VARCHAR(20),
comment_a VARCHAR(50),
comment_b VARCHAR(50),
comment_c VARCHAR(50),
comment_d VARCHAR(50),
comment_e VARCHAR(50),
index (uniqueid),
index (phone_ext),
index (start_time)
);

CREATE TABLE web_client_sessions (
extension VARCHAR(100) NOT NULL,
server_ip VARCHAR(15) NOT NULL,
program ENUM('agc','vicidial','monitor','other') default 'agc',
start_time DATETIME NOT NULL,
session_name VARCHAR(40) UNIQUE NOT NULL
);

CREATE TABLE server_performance (
start_time DATETIME NOT NULL,
server_ip VARCHAR(15) NOT NULL,
sysload INT(6) NOT NULL,
freeram SMALLINT(5) UNSIGNED NOT NULL,
usedram SMALLINT(5) UNSIGNED NOT NULL,
processes SMALLINT(4) UNSIGNED NOT NULL,
channels_total SMALLINT(4) UNSIGNED NOT NULL,
trunks_total SMALLINT(4) UNSIGNED NOT NULL,
clients_total SMALLINT(4) UNSIGNED NOT NULL,
clients_zap SMALLINT(4) UNSIGNED NOT NULL,
clients_iax SMALLINT(4) UNSIGNED NOT NULL,
clients_local SMALLINT(4) UNSIGNED NOT NULL,
clients_sip SMALLINT(4) UNSIGNED NOT NULL,
live_recordings SMALLINT(4) UNSIGNED NOT NULL,
cpu_user_percent SMALLINT(3) UNSIGNED NOT NULL default '0',
cpu_system_percent SMALLINT(3) UNSIGNED NOT NULL default '0',
cpu_idle_percent SMALLINT(3) UNSIGNED NOT NULL default '0'
);

CREATE TABLE vicidial_agent_log (
agent_log_id INT(9) UNSIGNED AUTO_INCREMENT PRIMARY KEY NOT NULL,
user VARCHAR(20),
server_ip VARCHAR(15) NOT NULL,
event_time DATETIME,
lead_id INT(9) UNSIGNED,
campaign_id VARCHAR(8),
pause_epoch INT(10) UNSIGNED,
pause_sec SMALLINT(5) UNSIGNED default '0',
wait_epoch INT(10) UNSIGNED,
wait_sec SMALLINT(5) UNSIGNED default '0',
talk_epoch INT(10) UNSIGNED,
talk_sec SMALLINT(5) UNSIGNED default '0',
dispo_epoch INT(10) UNSIGNED,
dispo_sec SMALLINT(5) UNSIGNED default '0',
status VARCHAR(6),
user_group VARCHAR(20),
comments VARCHAR(20),
sub_status VARCHAR(6),
dead_epoch INT(10) UNSIGNED,
dead_sec SMALLINT(5) UNSIGNED default '0',
processed ENUM('Y','N') default 'N',
uniqueid VARCHAR(20) default '',
index (lead_id),
index (user),
index (event_time)
);

CREATE TABLE vicidial_scripts (
script_id VARCHAR(10) PRIMARY KEY NOT NULL,
script_name VARCHAR(50),
script_comments VARCHAR(255),
script_text TEXT,
active ENUM('Y','N')
);

CREATE TABLE phone_favorites (
extension VARCHAR(100),
server_ip VARCHAR(15),
extensions_list TEXT
);

CREATE TABLE vicidial_callbacks (
callback_id INT(9) UNSIGNED AUTO_INCREMENT PRIMARY KEY NOT NULL,
lead_id INT(9) UNSIGNED,
list_id BIGINT(14) UNSIGNED,		
campaign_id VARCHAR(8),			
status VARCHAR(10),
entry_time DATETIME,
callback_time DATETIME,
modify_date TIMESTAMP,
user VARCHAR(20),
recipient ENUM('USERONLY','ANYONE'),	
comments VARCHAR(255),
user_group VARCHAR(20),
lead_status VARCHAR(6) default 'CALLBK',
index (lead_id),
index (status),
index (callback_time)
);

CREATE TABLE vicidial_list_pins (
pins_id INT(9) UNSIGNED AUTO_INCREMENT PRIMARY KEY NOT NULL,
entry_time DATETIME,
phone_number VARCHAR(18),
lead_id INT(9) UNSIGNED,
campaign_id VARCHAR(20),			
product_code VARCHAR(20),
user VARCHAR(20),
digits VARCHAR(20),
index (lead_id),
index (phone_number),
index (entry_time)
);

CREATE TABLE vicidial_lead_filters (
lead_filter_id VARCHAR(10) PRIMARY KEY NOT NULL,
lead_filter_name VARCHAR(30) NOT NULL,
lead_filter_comments VARCHAR(255),
lead_filter_sql TEXT
);

CREATE TABLE vicidial_call_times (
call_time_id VARCHAR(10) PRIMARY KEY NOT NULL,
call_time_name VARCHAR(30) NOT NULL,
call_time_comments VARCHAR(255) default '',
ct_default_start SMALLINT(4) unsigned NOT NULL default '900',
ct_default_stop SMALLINT(4) unsigned NOT NULL default '2100',
ct_sunday_start SMALLINT(4) unsigned default '0',
ct_sunday_stop SMALLINT(4) unsigned default '0',
ct_monday_start SMALLINT(4) unsigned default '0',
ct_monday_stop SMALLINT(4) unsigned default '0',
ct_tuesday_start SMALLINT(4) unsigned default '0',
ct_tuesday_stop SMALLINT(4) unsigned default '0',
ct_wednesday_start SMALLINT(4) unsigned default '0',
ct_wednesday_stop SMALLINT(4) unsigned default '0',
ct_thursday_start SMALLINT(4) unsigned default '0',
ct_thursday_stop SMALLINT(4) unsigned default '0',
ct_friday_start SMALLINT(4) unsigned default '0',
ct_friday_stop SMALLINT(4) unsigned default '0',
ct_saturday_start SMALLINT(4) unsigned default '0',
ct_saturday_stop SMALLINT(4) unsigned default '0',
ct_state_call_times TEXT default '',
default_afterhours_filename_override VARCHAR(255) default '',
sunday_afterhours_filename_override VARCHAR(255) default '',
monday_afterhours_filename_override VARCHAR(255) default '',
tuesday_afterhours_filename_override VARCHAR(255) default '',
wednesday_afterhours_filename_override VARCHAR(255) default '',
thursday_afterhours_filename_override VARCHAR(255) default '',
friday_afterhours_filename_override VARCHAR(255) default '',
saturday_afterhours_filename_override VARCHAR(255) default ''
);

CREATE TABLE vicidial_state_call_times (
state_call_time_id VARCHAR(10) PRIMARY KEY NOT NULL,
state_call_time_state VARCHAR(2) NOT NULL,
state_call_time_name VARCHAR(30) NOT NULL,
state_call_time_comments VARCHAR(255) default '',
sct_default_start SMALLINT(4) unsigned NOT NULL default '900',
sct_default_stop SMALLINT(4) unsigned NOT NULL default '2100',
sct_sunday_start SMALLINT(4) unsigned default '0',
sct_sunday_stop SMALLINT(4) unsigned default '0',
sct_monday_start SMALLINT(4) unsigned default '0',
sct_monday_stop SMALLINT(4) unsigned default '0',
sct_tuesday_start SMALLINT(4) unsigned default '0',
sct_tuesday_stop SMALLINT(4) unsigned default '0',
sct_wednesday_start SMALLINT(4) unsigned default '0',
sct_wednesday_stop SMALLINT(4) unsigned default '0',
sct_thursday_start SMALLINT(4) unsigned default '0',
sct_thursday_stop SMALLINT(4) unsigned default '0',
sct_friday_start SMALLINT(4) unsigned default '0',
sct_friday_stop SMALLINT(4) unsigned default '0',
sct_saturday_start SMALLINT(4) unsigned default '0',
sct_saturday_stop SMALLINT(4) unsigned default '0'
);

CREATE TABLE vicidial_campaign_stats (
campaign_id VARCHAR(20) PRIMARY KEY NOT NULL,
update_time TIMESTAMP,
dialable_leads INT(9) UNSIGNED default '0',
calls_today INT(9) UNSIGNED default '0',
answers_today INT(9) UNSIGNED default '0',
drops_today INT(9) UNSIGNED default '0',
drops_today_pct VARCHAR(6) default '0',
drops_answers_today_pct VARCHAR(6) default '0',
calls_hour INT(9) UNSIGNED default '0',
answers_hour INT(9) UNSIGNED default '0',
drops_hour INT(9) UNSIGNED default '0',
drops_hour_pct VARCHAR(6) default '0',
calls_halfhour INT(9) UNSIGNED default '0',
answers_halfhour INT(9) UNSIGNED default '0',
drops_halfhour INT(9) UNSIGNED default '0',
drops_halfhour_pct VARCHAR(6) default '0',
calls_fivemin INT(9) UNSIGNED default '0',
answers_fivemin INT(9) UNSIGNED default '0',
drops_fivemin INT(9) UNSIGNED default '0',
drops_fivemin_pct VARCHAR(6) default '0',
calls_onemin INT(9) UNSIGNED default '0',
answers_onemin INT(9) UNSIGNED default '0',
drops_onemin INT(9) UNSIGNED default '0',
drops_onemin_pct VARCHAR(6) default '0',
differential_onemin VARCHAR(20) default '0',
agents_average_onemin VARCHAR(20) default '0',
balance_trunk_fill SMALLINT(5) UNSIGNED default '0',
status_category_1 VARCHAR(20),
status_category_count_1 INT(9) UNSIGNED default '0',
status_category_2 VARCHAR(20),
status_category_count_2 INT(9) UNSIGNED default '0',
status_category_3 VARCHAR(20),
status_category_count_3 INT(9) UNSIGNED default '0',
status_category_4 VARCHAR(20),
status_category_count_4 INT(9) UNSIGNED default '0',
hold_sec_stat_one MEDIUMINT(8) UNSIGNED default '0',
hold_sec_stat_two MEDIUMINT(8) UNSIGNED default '0',
agent_non_pause_sec MEDIUMINT(8) UNSIGNED default '0',
hold_sec_answer_calls MEDIUMINT(8) UNSIGNED default '0',
hold_sec_drop_calls MEDIUMINT(8) UNSIGNED default '0',
hold_sec_queue_calls MEDIUMINT(8) UNSIGNED default '0',
agent_calls_today INT(9) UNSIGNED default '0',
agent_wait_today BIGINT(14) UNSIGNED default '0',
agent_custtalk_today BIGINT(14) UNSIGNED default '0',
agent_acw_today BIGINT(14) UNSIGNED default '0',
agent_pause_today BIGINT(14) UNSIGNED default '0'
);

CREATE TABLE vicidial_dnc (
phone_number VARCHAR(18) PRIMARY KEY NOT NULL
);

CREATE TABLE vicidial_lead_recycle (
recycle_id INT(9) UNSIGNED AUTO_INCREMENT PRIMARY KEY NOT NULL,
campaign_id VARCHAR(8),
status VARCHAR(6) NOT NULL,
attempt_delay SMALLINT(5) UNSIGNED default '1800',
attempt_maximum TINYINT(3) UNSIGNED default '2',
active ENUM('Y','N') default 'N',
index (campaign_id)
);

CREATE TABLE vicidial_campaign_server_stats (
campaign_id VARCHAR(20) NOT NULL,
server_ip VARCHAR(15) NOT NULL,
update_time TIMESTAMP,
local_trunk_shortage SMALLINT(5) UNSIGNED default '0',
index (campaign_id),
index (server_ip)
);

CREATE TABLE vicidial_server_trunks (
server_ip VARCHAR(15) NOT NULL,
campaign_id VARCHAR(20) NOT NULL,
dedicated_trunks SMALLINT(5) UNSIGNED default '0',
trunk_restriction ENUM('MAXIMUM_LIMIT','OVERFLOW_ALLOWED') default 'OVERFLOW_ALLOWED',
index (campaign_id),
index (server_ip)
);

CREATE TABLE vicidial_postal_codes (
postal_code VARCHAR(10) NOT NULL,
state VARCHAR(4),
GMT_offset VARCHAR(6),
DST enum('Y','N'),
DST_range VARCHAR(8),
country CHAR(3),
country_code SMALLINT(5) UNSIGNED
);

CREATE TABLE vicidial_pause_codes (
pause_code VARCHAR(6) NOT NULL,
pause_code_name VARCHAR(30),
billable ENUM('NO','YES','HALF') default 'NO',
campaign_id VARCHAR(8),
index (campaign_id)
);

CREATE TABLE system_settings (
version VARCHAR(50),
install_date VARCHAR(50),
use_non_latin ENUM('0','1') default '0',
webroot_writable ENUM('0','1') default '1',
enable_queuemetrics_logging ENUM('0','1') default '0',
queuemetrics_server_ip VARCHAR(15),
queuemetrics_dbname VARCHAR(50),
queuemetrics_login VARCHAR(50),
queuemetrics_pass VARCHAR(50),
queuemetrics_url VARCHAR(255),
queuemetrics_log_id VARCHAR(10) default 'VIC',
queuemetrics_eq_prepend VARCHAR(255) default 'NONE',
vicidial_agent_disable ENUM('NOT_ACTIVE','LIVE_AGENT','EXTERNAL','ALL') default 'ALL',
allow_sipsak_messages ENUM('0','1') default '0',
admin_home_url VARCHAR(255) default '../vicidial/welcome.php',
enable_agc_xfer_log ENUM('0','1') default '0',
db_schema_version INT(8) UNSIGNED default '0',
auto_user_add_value INT(9) UNSIGNED default '101',
timeclock_end_of_day VARCHAR(4) default '0000',
timeclock_last_reset_date DATE,
vdc_header_date_format VARCHAR(50) default 'MS_DASH_24HR  2008-06-24 23:59:59',
vdc_customer_date_format VARCHAR(50) default 'AL_TEXT_AMPM  OCT 24, 2008 11:59:59 PM',
vdc_header_phone_format VARCHAR(50) default 'US_PARN (000)000-0000',
vdc_agent_api_active ENUM('0','1') default '0',
qc_last_pull_time DATETIME,
enable_vtiger_integration ENUM('0','1') default '0',
vtiger_server_ip VARCHAR(15),
vtiger_dbname VARCHAR(50),
vtiger_login VARCHAR(50),
vtiger_pass VARCHAR(50),
vtiger_url VARCHAR(255),
qc_features_active ENUM('1','0') default '0',
outbound_autodial_active ENUM('1','0') default '1',
outbound_calls_per_second SMALLINT(3) UNSIGNED default '40',
enable_tts_integration ENUM('0','1') default '0',
agentonly_callback_campaign_lock ENUM('0','1') default '1',
sounds_central_control_active ENUM('0','1') default '0',
sounds_web_server VARCHAR(50) default '127.0.0.1',
sounds_web_directory VARCHAR(255) default '',
active_voicemail_server VARCHAR(15) default '',
auto_dial_limit VARCHAR(5) default '4',
user_territories_active ENUM('0','1') default '0',
allow_custom_dialplan ENUM('0','1') default '0',
db_schema_update_date DATETIME,
enable_second_webform ENUM('0','1') default '1',
default_webphone ENUM('1','0') default '0',
default_external_server_ip ENUM('1','0') default '0',
webphone_url VARCHAR(255) default '',
static_agent_url VARCHAR(255) default '',
default_phone_code VARCHAR(8) default '1',
enable_agc_dispo_log ENUM('0','1') default '0',
custom_dialplan_entry TEXT,
queuemetrics_loginout ENUM('STANDARD','CALLBACK','NONE') default 'STANDARD',
callcard_enabled ENUM('1','0') default '0',
queuemetrics_callstatus ENUM('0','1') default '1',
default_codecs VARCHAR(100) default '',
custom_fields_enabled ENUM('0','1') default '0',
admin_web_directory VARCHAR(255) default 'vicidial',
label_title VARCHAR(40) default '',
label_first_name VARCHAR(40) default '',
label_middle_initial VARCHAR(40) default '',
label_last_name VARCHAR(40) default '',
label_address1 VARCHAR(40) default '',
label_address2 VARCHAR(40) default '',
label_address3 VARCHAR(40) default '',
label_city VARCHAR(40) default '',
label_state VARCHAR(40) default '',
label_province VARCHAR(40) default '',
label_postal_code VARCHAR(40) default '',
label_vendor_lead_code VARCHAR(40) default '',
label_gender VARCHAR(40) default '',
label_phone_number VARCHAR(40) default '',
label_phone_code VARCHAR(40) default '',
label_alt_phone VARCHAR(40) default '',
label_security_phrase VARCHAR(40) default '',
label_email VARCHAR(40) default '',
label_comments VARCHAR(40) default '',
slave_db_server VARCHAR(50) default '',
reports_use_slave_db VARCHAR(2000) default '',
webphone_systemkey VARCHAR(100) default '',
first_login_trigger ENUM('Y','N') default 'N',
hosted_settings VARCHAR(100) default '',
default_phone_registration_password VARCHAR(20) default 'test',
default_phone_login_password VARCHAR(20) default 'test',
default_server_password VARCHAR(20) default 'test',
admin_modify_refresh SMALLINT(5) UNSIGNED default '0',
nocache_admin ENUM('0','1') default '1',
generate_cross_server_exten ENUM('0','1') default '0',
queuemetrics_addmember_enabled ENUM('0','1') default '0',
queuemetrics_dispo_pause VARCHAR(6) default '',
label_hide_field_logs VARCHAR(6) default 'Y',
queuemetrics_pe_phone_append ENUM('0','1') default '0',
test_campaign_calls ENUM('0','1') default '0',
agents_calls_reset ENUM('0','1') default '1',
voicemail_timezones TEXT,
default_voicemail_timezone VARCHAR(30) default 'eastern',
default_local_gmt VARCHAR(6) default '-5.00',
noanswer_log ENUM('Y','N') default 'N',
alt_log_server_ip VARCHAR(50) default '',
alt_log_dbname VARCHAR(50) default '',
alt_log_login VARCHAR(50) default '',
alt_log_pass VARCHAR(50) default '',
tables_use_alt_log_db VARCHAR(2000) default '',
did_agent_log ENUM('Y','N') default 'N',
campaign_cid_areacodes_enabled ENUM('0','1') default '1',
pllb_grouping_limit SMALLINT(5) default '100',
did_ra_extensions_enabled ENUM('0','1') default '0',
expanded_list_stats ENUM('0','1') default '1'
);

CREATE TABLE vicidial_campaigns_list_mix (
vcl_id VARCHAR(20) PRIMARY KEY NOT NULL,
vcl_name VARCHAR(50),
campaign_id VARCHAR(8),
list_mix_container TEXT,
mix_method ENUM('EVEN_MIX','IN_ORDER','RANDOM') default 'IN_ORDER',
status ENUM('ACTIVE','INACTIVE') default 'INACTIVE',
index (campaign_id)
);

CREATE TABLE vicidial_status_categories (
vsc_id VARCHAR(20) PRIMARY KEY NOT NULL,
vsc_name VARCHAR(50),
vsc_description VARCHAR(255),
tovdad_display ENUM('Y','N') default 'N',
sale_category ENUM('Y','N') default 'N',
dead_lead_category ENUM('Y','N') default 'N'
);

CREATE TABLE vicidial_ivr (
ivr_id INT(9) UNSIGNED AUTO_INCREMENT PRIMARY KEY NOT NULL,
entry_time DATETIME,
length_in_sec SMALLINT(5) UNSIGNED default '0',
inbound_number VARCHAR(12),
recording_id INT(9) UNSIGNED,
recording_filename VARCHAR(50),
company_id VARCHAR(12),
phone_number VARCHAR(18),
lead_id INT(9) UNSIGNED,
campaign_id VARCHAR(20),			
product_code VARCHAR(20),
user VARCHAR(20),
prompt_audio_1 VARCHAR(20),
prompt_response_1 TINYINT(1) UNSIGNED default '0',
prompt_audio_2 VARCHAR(20),
prompt_response_2 TINYINT(1) UNSIGNED default '0',
prompt_audio_3 VARCHAR(20),
prompt_response_3 TINYINT(1) UNSIGNED default '0',
prompt_audio_4 VARCHAR(20),
prompt_response_4 TINYINT(1) UNSIGNED default '0',
prompt_audio_5 VARCHAR(20),
prompt_response_5 TINYINT(1) UNSIGNED default '0',
prompt_audio_6 VARCHAR(20),
prompt_response_6 TINYINT(1) UNSIGNED default '0',
prompt_audio_7 VARCHAR(20),
prompt_response_7 TINYINT(1) UNSIGNED default '0',
prompt_audio_8 VARCHAR(20),
prompt_response_8 TINYINT(1) UNSIGNED default '0',
prompt_audio_9 VARCHAR(20),
prompt_response_9 TINYINT(1) UNSIGNED default '0',
prompt_audio_10 VARCHAR(20),
prompt_response_10 TINYINT(1) UNSIGNED default '0',
prompt_audio_11 VARCHAR(20),
prompt_response_11 TINYINT(1) UNSIGNED default '0',
prompt_audio_12 VARCHAR(20),
prompt_response_12 TINYINT(1) UNSIGNED default '0',
prompt_audio_13 VARCHAR(20),
prompt_response_13 TINYINT(1) UNSIGNED default '0',
prompt_audio_14 VARCHAR(20),
prompt_response_14 TINYINT(1) UNSIGNED default '0',
prompt_audio_15 VARCHAR(20),
prompt_response_15 TINYINT(1) UNSIGNED default '0',
prompt_audio_16 VARCHAR(20),
prompt_response_16 TINYINT(1) UNSIGNED default '0',
prompt_audio_17 VARCHAR(20),
prompt_response_17 TINYINT(1) UNSIGNED default '0',
prompt_audio_18 VARCHAR(20),
prompt_response_18 TINYINT(1) UNSIGNED default '0',
prompt_audio_19 VARCHAR(20),
prompt_response_19 TINYINT(1) UNSIGNED default '0',
prompt_audio_20 VARCHAR(20),
prompt_response_20 TINYINT(1) UNSIGNED default '0',
index (phone_number),
index (entry_time)
);

ALTER TABLE vicidial_ivr AUTO_INCREMENT = 1000000;

CREATE TABLE vicidial_inbound_group_agents (
user VARCHAR(20),
group_id VARCHAR(20),			
group_rank TINYINT(1) default '0',
group_weight TINYINT(1) default '0',
calls_today SMALLINT(5) UNSIGNED default '0',
group_web_vars VARCHAR(255) default '',
index (group_id),
index (user)
);

CREATE TABLE vicidial_live_inbound_agents (
user VARCHAR(20),
group_id VARCHAR(20),			
group_weight TINYINT(1) default '0',
calls_today SMALLINT(5) UNSIGNED default '0',
last_call_time DATETIME,
last_call_finish DATETIME,
index (group_id),
index (group_weight),
unique index vlia_user_group_id (user, group_id)
);

CREATE TABLE vicidial_campaign_agents (
user VARCHAR(20),
campaign_id VARCHAR(20),			
campaign_rank TINYINT(1) default '0',
campaign_weight TINYINT(1) default '0',
calls_today SMALLINT(5) UNSIGNED default '0',
group_web_vars VARCHAR(255) default '',
index (campaign_id),
index (user)
);

CREATE TABLE vicidial_user_closer_log (
user VARCHAR(20),
campaign_id VARCHAR(20),
event_date DATETIME,
blended ENUM('1','0') default '0',
closer_campaigns TEXT,
manager_change VARCHAR(20) default '',
index (user),
index (event_date)
);

CREATE TABLE vicidial_qc_codes (
code VARCHAR(8) PRIMARY KEY NOT NULL,
code_name VARCHAR(30)
);

CREATE TABLE vicidial_agent_sph (
campaign_group_id VARCHAR(20) NOT NULL,
stat_date DATE NOT NULL,
shift VARCHAR(20) NOT NULL,
role ENUM('FRONTER','CLOSER') default 'FRONTER',
user VARCHAR(20) NOT NULL,
calls MEDIUMINT(8) UNSIGNED default '0',
sales MEDIUMINT(8) UNSIGNED default '0',
login_sec MEDIUMINT(8) UNSIGNED default '0',
login_hours DECIMAL(5,2) DEFAULT '0.00',
sph DECIMAL(6,2) DEFAULT '0.00',
index (campaign_group_id),
index (stat_date)
);

CREATE TABLE phones_alias (
alias_id VARCHAR(20) NOT NULL UNIQUE PRIMARY KEY,
alias_name VARCHAR(50),
logins_list VARCHAR(255)
);

CREATE TABLE vicidial_shifts (
shift_id VARCHAR(20) NOT NULL,
shift_name VARCHAR(50),
shift_start_time VARCHAR(4) default '0900',
shift_length VARCHAR(5) default '16:00',
shift_weekdays VARCHAR(7) default '0123456',
report_option ENUM('Y','N') default 'N',
index (shift_id)
);

CREATE TABLE vicidial_timeclock_log (
timeclock_id INT(9) UNSIGNED AUTO_INCREMENT PRIMARY KEY NOT NULL,
event_epoch INT(10) UNSIGNED NOT NULL,
event_date DATETIME NOT NULL,
login_sec INT(10) UNSIGNED,
event VARCHAR(50) NOT NULL,
user VARCHAR(20) NOT NULL,
user_group VARCHAR(20) NOT NULL,
ip_address VARCHAR(15),
shift_id VARCHAR(20),
notes VARCHAR(255),
manager_user VARCHAR(20),
manager_ip VARCHAR(15),
event_datestamp TIMESTAMP NOT NULL,
tcid_link INT(9) UNSIGNED,
index (user)
);

CREATE TABLE vicidial_timeclock_status (
user VARCHAR(20) UNIQUE NOT NULL,
user_group VARCHAR(20) NOT NULL,
event_epoch INT(10) UNSIGNED,
event_date TIMESTAMP,
status VARCHAR(50),
ip_address VARCHAR(15),
shift_id VARCHAR(20),
index (user)
);

CREATE TABLE vicidial_timeclock_audit_log (
timeclock_id INT(9) UNSIGNED NOT NULL,
event_epoch INT(10) UNSIGNED NOT NULL,
event_date DATETIME NOT NULL,
login_sec INT(10) UNSIGNED,
event VARCHAR(50) NOT NULL,
user VARCHAR(20) NOT NULL,
user_group VARCHAR(20) NOT NULL,
ip_address VARCHAR(15),
shift_id VARCHAR(20),
event_datestamp TIMESTAMP NOT NULL,
tcid_link INT(9) UNSIGNED,
index (timeclock_id),
index (user)
);

CREATE TABLE vicidial_admin_log (
admin_log_id INT(9) UNSIGNED AUTO_INCREMENT PRIMARY KEY NOT NULL,
event_date DATETIME NOT NULL,
user VARCHAR(20) NOT NULL,
ip_address VARCHAR(15) NOT NULL,
event_section VARCHAR(30) NOT NULL,
event_type ENUM('ADD','COPY','LOAD','RESET','MODIFY','DELETE','SEARCH','LOGIN','LOGOUT','CLEAR','OVERRIDE','EXPORT','OTHER') default 'OTHER',
record_id VARCHAR(50) NOT NULL,
event_code VARCHAR(255) NOT NULL,
event_sql TEXT,
event_notes TEXT,
index (user),
index (event_section),
index (record_id)
);

CREATE TABLE vicidial_list_alt_phones (
alt_phone_id INT(9) UNSIGNED AUTO_INCREMENT PRIMARY KEY NOT NULL,
lead_id INT(9) UNSIGNED NOT NULL,
phone_code VARCHAR(10),
phone_number VARCHAR(18),
alt_phone_note VARCHAR(30),
alt_phone_count SMALLINT(5) UNSIGNED,
active ENUM('Y','N') default 'Y',
index (lead_id),
index (phone_number)
);

CREATE TABLE vicidial_campaign_dnc (
phone_number VARCHAR(18) NOT NULL,
campaign_id VARCHAR(8) NOT NULL,
index (phone_number),
unique index phonecamp (phone_number, campaign_id)
);

CREATE TABLE vicidial_inbound_dids (
did_id INT(9) UNSIGNED AUTO_INCREMENT PRIMARY KEY NOT NULL,
did_pattern VARCHAR(50) NOT NULL,
did_description VARCHAR(50),
did_active ENUM('Y','N') default 'Y',
did_route ENUM('EXTEN','VOICEMAIL','AGENT','PHONE','IN_GROUP','CALLMENU') default 'EXTEN',
extension VARCHAR(50) default '9998811112',
exten_context VARCHAR(50) default 'default',
voicemail_ext VARCHAR(10),
phone VARCHAR(100),
server_ip VARCHAR(15),
user VARCHAR(20),
user_unavailable_action ENUM('IN_GROUP','EXTEN','VOICEMAIL','PHONE') default 'VOICEMAIL',
user_route_settings_ingroup VARCHAR(20) default 'AGENTDIRECT',
group_id VARCHAR(20),
call_handle_method VARCHAR(20) default 'CID',
agent_search_method ENUM('LO','LB','SO') default 'LB',
list_id BIGINT(14) UNSIGNED default '999',
campaign_id VARCHAR(8),
phone_code VARCHAR(10) default '1',
menu_id VARCHAR(50) default '',
record_call ENUM('Y','N','Y_QUEUESTOP') default 'N',
filter_inbound_number ENUM('DISABLED','GROUP','URL') default 'DISABLED',
filter_phone_group_id VARCHAR(20) default '',
filter_url VARCHAR(1000) default '',
filter_action ENUM('EXTEN','VOICEMAIL','AGENT','PHONE','IN_GROUP','CALLMENU') default 'EXTEN',
filter_extension VARCHAR(50) default '9998811112',
filter_exten_context VARCHAR(50) default 'default',
filter_voicemail_ext VARCHAR(10),
filter_phone VARCHAR(100),
filter_server_ip VARCHAR(15),
filter_user VARCHAR(20),
filter_user_unavailable_action ENUM('IN_GROUP','EXTEN','VOICEMAIL','PHONE') default 'VOICEMAIL',
filter_user_route_settings_ingroup VARCHAR(20) default 'AGENTDIRECT',
filter_group_id VARCHAR(20),
filter_call_handle_method VARCHAR(20) default 'CID',
filter_agent_search_method ENUM('LO','LB','SO') default 'LB',
filter_list_id BIGINT(14) UNSIGNED default '999',
filter_campaign_id VARCHAR(8),
filter_phone_code VARCHAR(10) default '1',
filter_menu_id VARCHAR(50) default '',
filter_clean_cid_number VARCHAR(20) default '',
custom_one VARCHAR(100) default '',
custom_two VARCHAR(100) default '',
custom_three VARCHAR(100) default '',
custom_four VARCHAR(100) default '',
custom_five VARCHAR(100) default '',
unique index (did_pattern),
index (group_id)
);

CREATE TABLE vicidial_did_log (
uniqueid VARCHAR(20) NOT NULL,
channel VARCHAR(100) NOT NULL,
server_ip VARCHAR(15) NOT NULL,
caller_id_number VARCHAR(18),
caller_id_name VARCHAR(20),
extension VARCHAR(100),
call_date DATETIME,
did_id VARCHAR(9) default '',
did_route VARCHAR(9) default '',
index (uniqueid),
index (caller_id_number),
index (extension),
index (call_date)
);

CREATE TABLE vicidial_api_log (
api_id INT(9) UNSIGNED AUTO_INCREMENT PRIMARY KEY NOT NULL,
user VARCHAR(20) NOT NULL,
api_date DATETIME,
api_script VARCHAR(10),
function VARCHAR(20) NOT NULL,
agent_user VARCHAR(20),
value VARCHAR(255),
result VARCHAR(10),
result_reason VARCHAR(255),
source VARCHAR(20),
data TEXT,
run_time VARCHAR(20) default '0',
index(api_date)
);

CREATE TABLE vicidial_nanpa_prefix_codes (
areacode CHAR(3),
prefix CHAR(3),
GMT_offset VARCHAR(6),
DST enum('Y','N'),
latitude VARCHAR(17),
longitude VARCHAR(17),
city VARCHAR(50) default '',
state VARCHAR(2) default '',
postal_code VARCHAR(10) default '',
country VARCHAR(2) default ''
);

CREATE INDEX areaprefix on vicidial_nanpa_prefix_codes (areacode,prefix);

CREATE TABLE vicidial_cpd_log (
cpd_id INT(10) UNSIGNED AUTO_INCREMENT PRIMARY KEY NOT NULL,
channel VARCHAR(100) NOT NULL,
uniqueid VARCHAR(20),
callerid VARCHAR(20),
server_ip VARCHAR(15) NOT NULL,
lead_id INT(9) UNSIGNED,
event_date DATETIME,
result VARCHAR(20),
status ENUM('NEW','PROCESSED') default 'NEW',
cpd_seconds DECIMAL(7,2) default '0',
index(uniqueid),
index(callerid),
index(lead_id)
);

CREATE TABLE vicidial_conf_templates (
template_id VARCHAR(15) NOT NULL,
template_name VARCHAR(50) NOT NULL,
template_contents TEXT,
unique index (template_id)
);

CREATE TABLE vicidial_server_carriers (
carrier_id VARCHAR(15) NOT NULL,
carrier_name VARCHAR(50) NOT NULL,
registration_string VARCHAR(255),
template_id VARCHAR(15) NOT NULL,
account_entry TEXT,
protocol ENUM('SIP','Zap','IAX2','EXTERNAL') default 'SIP',
globals_string VARCHAR(255),
dialplan_entry TEXT,
server_ip VARCHAR(15) NOT NULL,
active ENUM('Y','N') default 'Y',
carrier_description VARCHAR(255),
unique index(carrier_id),
index (server_ip)
);

CREATE TABLE groups_alias (
group_alias_id VARCHAR(30) NOT NULL UNIQUE PRIMARY KEY,
group_alias_name VARCHAR(50),
caller_id_number VARCHAR(20),
caller_id_name VARCHAR(20),
active ENUM('Y','N') default 'N'
);

CREATE TABLE user_call_log (
user_call_log_id INT(9) UNSIGNED AUTO_INCREMENT PRIMARY KEY NOT NULL,
user VARCHAR(20),
call_date DATETIME,
call_type VARCHAR(20),
server_ip VARCHAR(15) NOT NULL,
phone_number VARCHAR(20),
number_dialed VARCHAR(30),
lead_id INT(9) UNSIGNED,
callerid VARCHAR(20),
group_alias_id VARCHAR(30),
preset_name VARCHAR(40) default '',
campaign_id VARCHAR(20) default '',
customer_hungup ENUM('BEFORE_CALL','DURING_CALL','') default '',
customer_hungup_seconds SMALLINT(5) UNSIGNED default '0',
index (user),
index (call_date),
index (group_alias_id)
);

CREATE TABLE vicidial_tts_prompts (
tts_id VARCHAR(50) PRIMARY KEY NOT NULL,
tts_name VARCHAR(100),
active ENUM('Y','N'),
tts_text TEXT,
tts_voice VARCHAR(100) default 'Allison-8kHz'
);

CREATE TABLE vicidial_call_menu (
menu_id VARCHAR(50) PRIMARY KEY NOT NULL,
menu_name VARCHAR(100),
menu_prompt VARCHAR(255),
menu_timeout SMALLINT(2) UNSIGNED default '10',
menu_timeout_prompt VARCHAR(255) default 'NONE',
menu_invalid_prompt VARCHAR(255) default 'NONE',
menu_repeat TINYINT(1) UNSIGNED default '0',
menu_time_check ENUM('0','1') default '0',
call_time_id VARCHAR(20) default '',
track_in_vdac ENUM('0','1') default '1',
custom_dialplan_entry TEXT,
tracking_group VARCHAR(20) default 'CALLMENU',
dtmf_log ENUM('0','1') default '0',
dtmf_field VARCHAR(50) default 'NONE'
);

CREATE TABLE vicidial_call_menu_options (
menu_id VARCHAR(50) NOT NULL,
option_value VARCHAR(20) NOT NULL default '',
option_description VARCHAR(255) default '',
option_route VARCHAR(20),
option_route_value VARCHAR(255),
option_route_value_context VARCHAR(1000),
index (menu_id),
unique index menuoption (menu_id, option_value)
);

CREATE TABLE vicidial_user_territories (
user VARCHAR(20) NOT NULL,
territory VARCHAR(100) default '',
level ENUM('TOP_AGENT','STANDARD_AGENT','BOTTOM_AGENT') default 'STANDARD_AGENT',
index (user),
unique index userterritory (user, territory)
);

CREATE TABLE vicidial_territories (
territory_id MEDIUMINT(8) UNSIGNED AUTO_INCREMENT PRIMARY KEY NOT NULL,
territory VARCHAR(100) default '',
territory_description VARCHAR(255) default '',
unique index uniqueterritory (territory)
);

CREATE TABLE vicidial_override_ids (
id_table VARCHAR(50) PRIMARY KEY NOT NULL,
active ENUM('0','1') default '1',
value INT(9) default '0'
);

CREATE TABLE vicidial_carrier_log (
uniqueid VARCHAR(20) PRIMARY KEY NOT NULL,
call_date DATETIME,
server_ip VARCHAR(15) NOT NULL,
lead_id INT(9) UNSIGNED,
hangup_cause TINYINT(1) UNSIGNED default '0',
dialstatus VARCHAR(16),
channel VARCHAR(100),
dial_time SMALLINT(3) UNSIGNED default '0',
answered_time SMALLINT(4) UNSIGNED default '0',
index (call_date)
);

CREATE TABLE vicidial_list_update_log (
event_date DATETIME,
lead_id VARCHAR(255),
vendor_id VARCHAR(255),
phone_number VARCHAR(255),
status VARCHAR(6),
old_status VARCHAR(255),
filename VARCHAR(255) default '',
result VARCHAR(20),
result_rows SMALLINT(3) UNSIGNED default '0',
list_id VARCHAR(255),
index (event_date)
);

CREATE TABLE vicidial_drop_rate_groups (
group_id VARCHAR(20) PRIMARY KEY NOT NULL,
update_time TIMESTAMP,
calls_today INT(9) UNSIGNED default '0',
answers_today INT(9) UNSIGNED default '0',
drops_today INT(9) UNSIGNED default '0',
drops_today_pct VARCHAR(6) default '0',
drops_answers_today_pct VARCHAR(6) default '0'
);

CREATE TABLE vicidial_process_triggers (
trigger_id VARCHAR(20) PRIMARY KEY NOT NULL,
trigger_name VARCHAR(100),
server_ip VARCHAR(15) NOT NULL,
trigger_time DATETIME,
trigger_run ENUM('0','1') default '0',
user VARCHAR(20),
trigger_lines TEXT
);

CREATE TABLE vicidial_process_trigger_log (
trigger_id VARCHAR(20) NOT NULL,
server_ip VARCHAR(15) NOT NULL,
trigger_time DATETIME,
user VARCHAR(20),
trigger_lines TEXT,
trigger_results TEXT,
index (trigger_id),
index (trigger_time)
);

CREATE TABLE vtiger_rank_data (
account VARCHAR(20) PRIMARY KEY NOT NULL,
seqacct VARCHAR(20) UNIQUE NOT NULL,
last_attempt_days SMALLINT(5) UNSIGNED NOT NULL,
orders SMALLINT(5) NOT NULL,
net_sales SMALLINT(5) NOT NULL,
net_sales_ly SMALLINT(5) NOT NULL,
percent_variance VARCHAR(10) NOT NULL,
imu VARCHAR(10) NOT NULL,
aov SMALLINT(5) NOT NULL,
returns SMALLINT(5) NOT NULL,
rank SMALLINT(5) NOT NULL
);

CREATE TABLE vtiger_rank_parameters (
parameter_id INT(9) UNSIGNED AUTO_INCREMENT PRIMARY KEY NOT NULL,
parameter VARCHAR(20) NOT NULL,
lower_range VARCHAR(20) NOT NULL,
upper_range VARCHAR(20) NOT NULL,
points SMALLINT(5) NOT NULL,
index (parameter)
);


CREATE TABLE twoday_call_log (
uniqueid VARCHAR(20) PRIMARY KEY NOT NULL,
channel VARCHAR(100),
channel_group VARCHAR(30),
type VARCHAR(10),
server_ip VARCHAR(15),
extension VARCHAR(100),
number_dialed VARCHAR(15),
caller_code VARCHAR(20),
start_time DATETIME,
start_epoch INT(10),
end_time DATETIME,
end_epoch INT(10),
length_in_sec INT(10),
length_in_min DOUBLE(8,2),
index (caller_code),
index (server_ip),
index (channel)
);

CREATE TABLE twoday_vicidial_log (
uniqueid VARCHAR(20) PRIMARY KEY NOT NULL,
lead_id INT(9) UNSIGNED NOT NULL,
list_id BIGINT(14) UNSIGNED,
campaign_id VARCHAR(8),
call_date DATETIME,
start_epoch INT(10) UNSIGNED,
end_epoch INT(10) UNSIGNED,
length_in_sec INT(10),
status VARCHAR(6),
phone_code VARCHAR(10),
phone_number VARCHAR(18),
user VARCHAR(20),
comments VARCHAR(255),
processed ENUM('Y','N'),
user_group VARCHAR(20),
term_reason  ENUM('CALLER','AGENT','QUEUETIMEOUT','ABANDON','AFTERHOURS','NONE') default 'NONE',
alt_dial VARCHAR(6) default 'NONE',
index (lead_id),
index (call_date)
);

CREATE TABLE twoday_vicidial_closer_log (
closecallid INT(9) UNSIGNED PRIMARY KEY NOT NULL,
lead_id INT(9) UNSIGNED NOT NULL,
list_id BIGINT(14) UNSIGNED,
campaign_id VARCHAR(20),
call_date DATETIME,
start_epoch INT(10) UNSIGNED,
end_epoch INT(10) UNSIGNED,
length_in_sec INT(10),
status VARCHAR(6),
phone_code VARCHAR(10),
phone_number VARCHAR(18),
user VARCHAR(20),
comments VARCHAR(255),
processed ENUM('Y','N'),
queue_seconds DECIMAL(7,2) default '0',
user_group VARCHAR(20),
xfercallid INT(9) UNSIGNED,
term_reason  ENUM('CALLER','AGENT','QUEUETIMEOUT','ABANDON','AFTERHOURS','HOLDRECALLXFER','HOLDTIME','NOAGENT','NONE') default 'NONE',
uniqueid VARCHAR(20) NOT NULL default '',
agent_only VARCHAR(20) default '',
index (lead_id),
index (call_date),
index (campaign_id),
index (uniqueid)
);

CREATE TABLE twoday_vicidial_xfer_log (
xfercallid INT(9) UNSIGNED PRIMARY KEY NOT NULL,
lead_id INT(9) UNSIGNED NOT NULL,
list_id BIGINT(14) UNSIGNED,
campaign_id VARCHAR(20),
call_date DATETIME,
phone_code VARCHAR(10),
phone_number VARCHAR(18),
user VARCHAR(20),
closer VARCHAR(20),
index (lead_id),
index (call_date)
);

CREATE TABLE twoday_recording_log (
recording_id INT(10) UNSIGNED PRIMARY KEY NOT NULL,
channel VARCHAR(100),
server_ip VARCHAR(15),
extension VARCHAR(100),
start_time DATETIME,
start_epoch INT(10) UNSIGNED,
end_time DATETIME,
end_epoch INT(10) UNSIGNED,
length_in_sec MEDIUMINT(8) UNSIGNED,
length_in_min DOUBLE(8,2),
filename VARCHAR(50),
location VARCHAR(255),
lead_id INT(9) UNSIGNED,
user VARCHAR(20),
vicidial_id VARCHAR(20),
index(filename),
index(lead_id),
index(user),
index(vicidial_id)
);

CREATE TABLE twoday_vicidial_agent_log (
agent_log_id INT(9) UNSIGNED PRIMARY KEY NOT NULL,
user VARCHAR(20),
server_ip VARCHAR(15) NOT NULL,
event_time DATETIME,
lead_id INT(9) UNSIGNED,
campaign_id VARCHAR(8),	
pause_epoch INT(10) UNSIGNED,
pause_sec SMALLINT(5) UNSIGNED default '0',
wait_epoch INT(10) UNSIGNED,
wait_sec SMALLINT(5) UNSIGNED default '0',
talk_epoch INT(10) UNSIGNED,
talk_sec SMALLINT(5) UNSIGNED default '0',
dispo_epoch INT(10) UNSIGNED,
dispo_sec SMALLINT(5) UNSIGNED default '0',
status VARCHAR(6),
user_group VARCHAR(20),
comments VARCHAR(20),
sub_status VARCHAR(6),
dead_epoch INT(10) UNSIGNED,
dead_sec SMALLINT(5) UNSIGNED default '0',
processed ENUM('Y','N') default 'N',
uniqueid VARCHAR(20) default '',
index (lead_id),
index (user),
index (event_time)
);

CREATE TABLE vicidial_music_on_hold (
moh_id VARCHAR(100) PRIMARY KEY NOT NULL,
moh_name VARCHAR(255),
active ENUM('Y','N') default 'N',
random ENUM('Y','N') default 'N',
remove ENUM('Y','N') default 'N'
);

CREATE TABLE vicidial_music_on_hold_files (
filename VARCHAR(100) NOT NULL,
moh_id VARCHAR(100) NOT NULL,
rank SMALLINT(5),
unique index mohfile (filename, moh_id)
);

CREATE TABLE vicidial_voicemail (
voicemail_id VARCHAR(10) NOT NULL UNIQUE PRIMARY KEY,
active ENUM('Y','N') default 'Y',
pass VARCHAR(10) NOT NULL,
fullname VARCHAR(100) NOT NULL,
messages INT(4) default '0',
old_messages INT(4) default '0',
email VARCHAR(100),
delete_vm_after_email ENUM('N','Y') default 'N',
voicemail_timezone VARCHAR(30) default 'eastern',
voicemail_options VARCHAR(255) default ''
);

CREATE TABLE vicidial_user_territory_log (
user VARCHAR(20),
campaign_id VARCHAR(20),
event_date DATETIME,
agent_territories TEXT,
index (user),
index (event_date)
);

CREATE TABLE vicidial_grab_call_log (
auto_call_id INT(9) UNSIGNED NOT NULL,
user VARCHAR(20),
event_date DATETIME,
call_time DATETIME,
campaign_id VARCHAR(20),
uniqueid VARCHAR(20),
phone_number VARCHAR(20),
lead_id INT(9) UNSIGNED,
queue_priority TINYINT(2) default '0',
call_type ENUM('IN','OUT','OUTBALANCE') default 'OUT',
index (auto_call_id),
index (event_date),
index (user),
index (campaign_id)
);

CREATE TABLE vtiger_vicidial_roles (
user_level TINYINT(2),
vtiger_role VARCHAR(5)
);

CREATE TABLE vicidial_call_notes (
notesid INT(9) UNSIGNED AUTO_INCREMENT PRIMARY KEY NOT NULL,
lead_id INT(9) UNSIGNED NOT NULL,
vicidial_id VARCHAR(20),
call_date DATETIME,
order_id VARCHAR(20),
appointment_date DATE,
appointment_time TIME,
call_notes TEXT
);

ALTER TABLE vicidial_call_notes AUTO_INCREMENT = 100;
CREATE INDEX lead_id on vicidial_call_notes (lead_id);

CREATE TABLE vicidial_custom_cid (
cid VARCHAR(18) NOT NULL,
state VARCHAR(20),
areacode VARCHAR(6),
country_code SMALLINT(5) UNSIGNED,
campaign_id VARCHAR(8) default '--ALL--',
index (state),
index (areacode)
);

CREATE TABLE callcard_accounts (
card_id VARCHAR(20) PRIMARY KEY NOT NULL,
pin VARCHAR(10) NOT NULL,
status ENUM('GENERATE','PRINT','SHIP','HOLD','ACTIVE','USED','EMPTY','CANCEL','VOID') default 'GENERATE',
balance_minutes SMALLINT(5) default '3',
inbound_group_id VARCHAR(20) default '',
index (pin)
);

CREATE TABLE callcard_accounts_details (
card_id VARCHAR(20) PRIMARY KEY NOT NULL,
run VARCHAR(4) default '',
batch VARCHAR(5) default '',
pack VARCHAR(5) default '',
sequence VARCHAR(5) default '',
status ENUM('GENERATE','PRINT','SHIP','HOLD','ACTIVE','USED','EMPTY','CANCEL','VOID') default 'GENERATE',
balance_minutes SMALLINT(5) default '3',
initial_value VARCHAR(6) default '0.00',
initial_minutes SMALLINT(5) default '3',
note_purchase_order VARCHAR(20) default '',
note_printer VARCHAR(20) default '',
note_did VARCHAR(18) default '',
inbound_group_id VARCHAR(20) default '',
note_language VARCHAR(10) default 'English',
note_name VARCHAR(20) default '',
note_comments VARCHAR(255) default '',
create_user VARCHAR(20) default '',
activate_user VARCHAR(20) default '',
used_user VARCHAR(20) default '',
void_user VARCHAR(20) default '',
create_time DATETIME,
activate_time DATETIME,
used_time DATETIME,
void_time DATETIME
);

CREATE TABLE callcard_log (
uniqueid VARCHAR(20) PRIMARY KEY NOT NULL,
card_id VARCHAR(20),
balance_minutes_start SMALLINT(5) default '3',
call_time DATETIME,
agent_time DATETIME,
dispo_time DATETIME,
agent VARCHAR(20) default '',
agent_dispo VARCHAR(6) default '',
agent_talk_sec MEDIUMINT(8) default '0',
agent_talk_min MEDIUMINT(8) default '0',
phone_number VARCHAR(18),
inbound_did VARCHAR(18),
index (card_id),
index (call_time)
);

CREATE TABLE vicidial_extension_groups (
extension_id INT(9) UNSIGNED AUTO_INCREMENT PRIMARY KEY NOT NULL,
extension_group_id VARCHAR(20) NOT NULL,
extension VARCHAR(100) default '8300',
rank MEDIUMINT(7) default '0',
campaign_groups TEXT,
call_count_today MEDIUMINT(7) default '0',
last_call_time DATETIME,
last_callerid VARCHAR(20) default '',
index (extension_group_id)
);

CREATE TABLE vicidial_remote_agent_log (
uniqueid VARCHAR(20) default '',
callerid VARCHAR(20) default '',
ra_user VARCHAR(20),
user VARCHAR(20),
call_time DATETIME,
extension VARCHAR(100) default '',
lead_id INT(9) UNSIGNED default '0',
phone_number VARCHAR(18) default '',
campaign_id VARCHAR(20) default '',
processed ENUM('Y','N') default 'N',
comment VARCHAR(255) default '',
index (call_time),
index (ra_user),
index (extension),
index (phone_number)
);

CREATE TABLE vicidial_log_extended (
uniqueid VARCHAR(50) PRIMARY KEY NOT NULL,
server_ip VARCHAR(15),
call_date DATETIME,
lead_id INT(9) UNSIGNED,
caller_code VARCHAR(30) NOT NULL,
custom_call_id VARCHAR(100),
start_url_processed ENUM('N','Y','U') default 'N',
dispo_url_processed ENUM('N','Y','U','XY','XU') default 'N',
multi_alt_processed ENUM('N','Y','U') default 'N',
noanswer_processed ENUM('N','Y','U') default 'N'
);

CREATE INDEX call_date on vicidial_log_extended (call_date);

CREATE TABLE vicidial_lists_fields (
field_id INT(9) UNSIGNED AUTO_INCREMENT PRIMARY KEY NOT NULL,
list_id BIGINT(14) UNSIGNED NOT NULL DEFAULT '0',
field_label VARCHAR(50),
field_name VARCHAR(5000),
field_description VARCHAR(100),
field_rank SMALLINT(5),
field_help VARCHAR(1000),
field_type ENUM('TEXT','AREA','SELECT','MULTI','RADIO','CHECKBOX','DATE','TIME','DISPLAY','SCRIPT','HIDDEN','READONLY','HIDEBLOB') default 'TEXT',
field_options VARCHAR(5000),
field_size SMALLINT(5),
field_max SMALLINT(5),
field_default VARCHAR(255),
field_cost SMALLINT(5),
field_required ENUM('Y','N') default 'N',
name_position ENUM('LEFT','TOP') default 'LEFT',
multi_position ENUM('HORIZONTAL','VERTICAL') default 'HORIZONTAL',
field_order SMALLINT(5) default '1'
);

CREATE UNIQUE INDEX listfield on vicidial_lists_fields (list_id, field_label);

CREATE TABLE vicidial_filter_phone_groups (
filter_phone_group_id VARCHAR(20) NOT NULL,
filter_phone_group_name VARCHAR(40) NOT NULL,
filter_phone_group_description VARCHAR(100),
index (filter_phone_group_id)
);

CREATE TABLE vicidial_filter_phone_numbers (
phone_number VARCHAR(18) NOT NULL,
filter_phone_group_id VARCHAR(20) NOT NULL,
index (phone_number),
unique index phonefilter (phone_number, filter_phone_group_id)
);

CREATE TABLE vicidial_xfer_presets (
campaign_id VARCHAR(20) NOT NULL,
preset_name VARCHAR(40) NOT NULL,
preset_number VARCHAR(50) NOT NULL,
preset_dtmf VARCHAR(50) default '',
preset_hide_number ENUM('Y','N') default 'N',
index (preset_name)
);

CREATE TABLE vicidial_xfer_stats (
campaign_id VARCHAR(20) NOT NULL,
preset_name VARCHAR(40) NOT NULL,
xfer_count SMALLINT(5) UNSIGNED default '0',
index (campaign_id)
);

CREATE TABLE vicidial_manual_dial_queue (
mdq_id INT(9) UNSIGNED AUTO_INCREMENT PRIMARY KEY NOT NULL,
user VARCHAR(20),
phone_number VARCHAR(100) default '',
entry_time DATETIME,
status ENUM('READY','QUEUE') default 'READY',
external_dial VARCHAR(100) default '',
index (user)
);

CREATE TABLE vicidial_lead_search_log (
search_log_id INT(9) UNSIGNED AUTO_INCREMENT PRIMARY KEY NOT NULL,
user VARCHAR(20) NOT NULL,
event_date DATETIME NOT NULL,
source VARCHAR(10) default '',
search_query TEXT,
results INT(9) UNSIGNED default '0',
seconds MEDIUMINT(7) UNSIGNED default '0',
index (user),
index (event_date)
);

CREATE TABLE vicidial_campaign_stats_debug (
campaign_id VARCHAR(20) NOT NULL,
server_ip VARCHAR(15) NOT NULL,
entry_time DATETIME,
update_time TIMESTAMP,
debug_output TEXT,
adapt_output TEXT,
index (campaign_id),
unique index campserver (campaign_id, server_ip)
);

CREATE TABLE vicidial_outbound_ivr_log (
uniqueid VARCHAR(50) NOT NULL,
caller_code VARCHAR(30) NOT NULL,
event_date DATETIME,
campaign_id VARCHAR(20) default '',
lead_id INT(9) UNSIGNED,
menu_id VARCHAR(50) default '',
menu_action VARCHAR(50) default '',
index (event_date),
index (lead_id),
index (campaign_id),
unique index campserver (event_date, lead_id, menu_id)
);

CREATE TABLE vicidial_screen_labels (
label_id VARCHAR(20) PRIMARY KEY NOT NULL,
label_name VARCHAR(100),
active ENUM('Y','N') default 'N',
label_hide_field_logs VARCHAR(6) default 'Y',
label_title VARCHAR(40) default '',
label_first_name VARCHAR(40) default '',
label_middle_initial VARCHAR(40) default '',
label_last_name VARCHAR(40) default '',
label_address1 VARCHAR(40) default '',
label_address2 VARCHAR(40) default '',
label_address3 VARCHAR(40) default '',
label_city VARCHAR(40) default '',
label_state VARCHAR(40) default '',
label_province VARCHAR(40) default '',
label_postal_code VARCHAR(40) default '',
label_vendor_lead_code VARCHAR(40) default '',
label_gender VARCHAR(40) default '',
label_phone_number VARCHAR(40) default '',
label_phone_code VARCHAR(40) default '',
label_alt_phone VARCHAR(40) default '',
label_security_phrase VARCHAR(40) default '',
label_email VARCHAR(40) default '',
label_comments VARCHAR(40) default ''
);

CREATE TABLE vicidial_agent_skip_log (
user_skip_log_id INT(9) UNSIGNED AUTO_INCREMENT PRIMARY KEY NOT NULL,
user VARCHAR(20),
event_date DATETIME,
lead_id INT(9) UNSIGNED,
campaign_id VARCHAR(20) default '',
previous_status VARCHAR(6) default '',
previous_called_count SMALLINT(5) UNSIGNED default '0',
index (user),
index (event_date),
index (campaign_id)
);

CREATE TABLE vicidial_url_log (
url_log_id INT(9) UNSIGNED AUTO_INCREMENT PRIMARY KEY NOT NULL,
uniqueid VARCHAR(50) NOT NULL,
url_date DATETIME,
url_type VARCHAR(10) default '',
response_sec SMALLINT(5) UNSIGNED default '0',
url TEXT,
url_response TEXT,
index (uniqueid)
);

CREATE TABLE vicidial_log_noanswer (
uniqueid VARCHAR(20) PRIMARY KEY NOT NULL,
lead_id INT(9) UNSIGNED NOT NULL,
list_id BIGINT(14) UNSIGNED,
campaign_id VARCHAR(8),
call_date DATETIME,
start_epoch INT(10) UNSIGNED,
end_epoch INT(10) UNSIGNED,
length_in_sec INT(10),
status VARCHAR(6),
phone_code VARCHAR(10),
phone_number VARCHAR(18),
user VARCHAR(20),
comments VARCHAR(255),
processed ENUM('Y','N'),
user_group VARCHAR(20),
term_reason  ENUM('CALLER','AGENT','QUEUETIMEOUT','ABANDON','AFTERHOURS','NONE') default 'NONE',
alt_dial VARCHAR(6) default 'NONE',
caller_code VARCHAR(30) NOT NULL,
index (lead_id),
index (call_date)
);

CREATE TABLE vicidial_did_agent_log (
uniqueid VARCHAR(20) NOT NULL,
server_ip VARCHAR(15) NOT NULL,
caller_id_number VARCHAR(18),
caller_id_name VARCHAR(20),
extension VARCHAR(100),
call_date DATETIME,
did_id VARCHAR(9) default '',
did_description VARCHAR(50) default '',
did_route VARCHAR(9) default '',
group_id VARCHAR(20) default '',
user VARCHAR(20) default 'VDCL',
index (uniqueid),
index (caller_id_number),
index (extension),
index (call_date)
);

CREATE TABLE vicidial_campaign_cid_areacodes (
campaign_id VARCHAR(8) NOT NULL,
areacode VARCHAR(5) NOT NULL,
outbound_cid VARCHAR(20),
active ENUM('Y','N','') default '',
cid_description VARCHAR(50),
call_count_today MEDIUMINT(7) default '0',
index (campaign_id),
index (areacode)
);

CREATE UNIQUE INDEX campareacode on vicidial_campaign_cid_areacodes (campaign_id, areacode, outbound_cid);

CREATE TABLE vicidial_did_ra_extensions (
did_id INT(9) UNSIGNED NOT NULL,
user_start VARCHAR(20),
extension VARCHAR(50) default '',
description VARCHAR(50),
active ENUM('Y','N','') default '',
call_count_today MEDIUMINT(7) default '0',
index (did_id),
index (user_start)
);

CREATE UNIQUE INDEX didraexten on vicidial_did_ra_extensions (did_id, user_start, extension);


ALTER TABLE vicidial_campaign_server_stats ENGINE=MEMORY;

ALTER TABLE live_channels ENGINE=MEMORY;

ALTER TABLE live_sip_channels ENGINE=MEMORY;

ALTER TABLE parked_channels ENGINE=MEMORY;

ALTER TABLE server_updater ENGINE=MEMORY;

ALTER TABLE web_client_sessions ENGINE=MEMORY;

ALTER TABLE vicidial_auto_calls ENGINE=MEMORY;

ALTER TABLE vicidial_hopper ENGINE=MEMORY;


UPDATE system_settings SET auto_user_add_value='1101';

INSERT INTO vicidial_music_on_hold SET moh_id='default',moh_name='Default Music On Hold',active='Y',random='N';
INSERT INTO vicidial_music_on_hold_files SET moh_id='default',filename='conf',rank='1';

INSERT INTO vicidial_inbound_groups(group_id,group_name,group_color,active,queue_priority) values('AGENTDIRECT','Single Agent Direct Queue','white','Y','99');

INSERT INTO vicidial_lists SET list_id='999',list_name='Default inbound list',campaign_id='TESTCAMP',active='N';
INSERT INTO vicidial_lists SET list_id='998',list_name='Default Manual list',campaign_id='TESTCAMP',active='N';

INSERT INTO system_settings (version,install_date,first_login_trigger) values('2.4b0.5', CURDATE(), 'Y');

INSERT INTO vicidial_status_categories (vsc_id,vsc_name) values('UNDEFINED','Default Category');

INSERT INTO vicidial_user_groups SET user_group='ADMIN',group_name='VICIDIAL ADMINISTRATORS',allowed_campaigns=' -ALL-CAMPAIGNS- - -',agent_status_viewable_groups=' --ALL-GROUPS-- ';

INSERT INTO vicidial_call_times SET call_time_id='24hours',call_time_name='default 24 hours calling',ct_default_start='0',ct_default_stop='2400';
INSERT INTO vicidial_call_times SET call_time_id='9am-9pm',call_time_name='default 9am to 9pm calling',ct_default_start='900',ct_default_stop='2100';
INSERT INTO vicidial_call_times SET call_time_id='9am-5pm',call_time_name='default 9am to 5pm calling',ct_default_start='900',ct_default_stop='1700';
INSERT INTO vicidial_call_times SET call_time_id='12pm-5pm',call_time_name='default 12pm to 5pm calling',ct_default_start='1200',ct_default_stop='1700';
INSERT INTO vicidial_call_times SET call_time_id='12pm-9pm',call_time_name='default 12pm to 9pm calling',ct_default_start='1200',ct_default_stop='1200';
INSERT INTO vicidial_call_times SET call_time_id='5pm-9pm',call_time_name='default 5pm to 9pm calling',ct_default_start='1700',ct_default_stop='2100';

INSERT INTO vicidial_state_call_times SET state_call_time_id='alabama',state_call_time_state='AL',state_call_time_name='Alabama 8am-8pm and Sunday',sct_default_start='800',sct_default_stop='2000',sct_sunday_start='2400',sct_sunday_stop='2400';
INSERT INTO vicidial_state_call_times SET state_call_time_id='illinois',state_call_time_state='IL',state_call_time_name='Illinois 8am',sct_default_start='800',sct_default_stop='2100';
INSERT INTO vicidial_state_call_times SET state_call_time_id='indiana',state_call_time_state='IN',state_call_time_name='Indiana 8pm restriction',sct_default_start='900',sct_default_stop='2000';
INSERT INTO vicidial_state_call_times SET state_call_time_id='kentucky',state_call_time_state='KY',state_call_time_name='Kentucky 10am restriction',sct_default_start='1000',sct_default_stop='2100';
INSERT INTO vicidial_state_call_times SET state_call_time_id='louisiana',state_call_time_state='LA',state_call_time_name='Louisiana 8am-8pm and Sunday',sct_default_start='800',sct_default_stop='2000',sct_sunday_start='2400',sct_sunday_stop='2400';
INSERT INTO vicidial_state_call_times SET state_call_time_id='massachuse',state_call_time_state='MA',state_call_time_name='Massachusetts 8am-8pm',sct_default_start='800',sct_default_stop='2000';
INSERT INTO vicidial_state_call_times SET state_call_time_id='mississipp',state_call_time_state='MS',state_call_time_name='Mississippi 8am-8pm and Sunday',sct_default_start='800',sct_default_stop='2000',sct_sunday_start='2400',sct_sunday_stop='2400';
INSERT INTO vicidial_state_call_times SET state_call_time_id='nebraska',state_call_time_state='NE',state_call_time_name='Nebraska 8am',sct_default_start='800',sct_default_stop='2100';
INSERT INTO vicidial_state_call_times SET state_call_time_id='nevada',state_call_time_state='NV',state_call_time_name='Nevada 8pm restriction',sct_default_start='900',sct_default_stop='2000';
INSERT INTO vicidial_state_call_times SET state_call_time_id='pennsylvan',state_call_time_state='PA',state_call_time_name='Pennsylvania sunday restriction',sct_sunday_start='1330',sct_sunday_stop='2100';
INSERT INTO vicidial_state_call_times SET state_call_time_id='rhodeislan',state_call_time_state='RI',state_call_time_name='Rhode Island restrictions',sct_default_start='900',sct_default_stop='1800',sct_sunday_start='2400',sct_sunday_stop='2400',sct_saturday_start='1000',sct_saturday_stop='1700';
INSERT INTO vicidial_state_call_times SET state_call_time_id='sdakota',state_call_time_state='SD',state_call_time_name='South Dakota sunday restrict',sct_sunday_start='2400',sct_sunday_stop='2400';
INSERT INTO vicidial_state_call_times SET state_call_time_id='tennessee',state_call_time_state='TN',state_call_time_name='Tennessee 8am',sct_default_start='800',sct_default_stop='2100';
INSERT INTO vicidial_state_call_times SET state_call_time_id='texas',state_call_time_state='TX',state_call_time_name='Texas sunday restriction',sct_sunday_start='1200',sct_sunday_stop='2100';
INSERT INTO vicidial_state_call_times SET state_call_time_id='utah',state_call_time_state='UT',state_call_time_name='Utah 8pm restriction',sct_default_start='900',sct_default_stop='2000';
INSERT INTO vicidial_state_call_times SET state_call_time_id='washington',state_call_time_state='WA',state_call_time_name='Washington 8am',sct_default_start='800',sct_default_stop='2100';
INSERT INTO vicidial_state_call_times SET state_call_time_id='wyoming',state_call_time_state='WY',state_call_time_name='Wyoming 8am-8pm',sct_default_start='800',sct_default_stop='2000';

INSERT INTO vicidial_shifts SET shift_id='24HRMIDNIGHT',shift_name='24 hours 7 days a week',shift_start_time='0000',shift_length='24:00',shift_weekdays='0123456';

INSERT INTO vicidial_conf_templates SET template_id='SIP_generic',template_name='SIP phone generic',template_contents="type=friend\nhost=dynamic\ncanreinvite=no\ncontext=default";
INSERT INTO vicidial_conf_templates SET template_id='IAX_generic',template_name='IAX phone generic',template_contents="type=friend\nhost=dynamic\nmaxauthreq=10\nauth=md5,plaintext,rsa\ncontext=default";

INSERT INTO vicidial_server_carriers SET carrier_id='PARAXIP', carrier_name='TEST ParaXip CPD example',registration_string='', template_id='--NONE--', account_entry="[paraxip]\ndisallow=all\nallow=ulaw\ntype=peer\nusername=paraxip\nfromuser=paraxip\nsecret=test\nfromdomain=10.10.10.16\nhost=10.10.10.15\ninsecure=port,invite\noutboundproxy=10.0.0.7", protocol='SIP', globals_string='TESTSIPTRUNKP = SIP/paraxip', dialplan_entry="exten => _5591999NXXXXXX,1,AGI(agi://127.0.0.1:4577/call_log)\nexten => _5591999NXXXXXX,2,Dial(${TESTSIPTRUNKP}/${EXTEN:4},,tTor)\nexten => _5591999NXXXXXX,3,Hangup", server_ip='10.10.10.15', active='N';
INSERT INTO vicidial_server_carriers SET carrier_id='SIPEXAMPLE', carrier_name='TEST SIP carrier example',registration_string='register => testcarrier:test@10.10.10.15:5060', template_id='--NONE--', account_entry="[testcarrier]\ndisallow=all\nallow=ulaw\ntype=friend\nusername=testcarrier\nsecret=test\nhost=dynamic\ndtmfmode=rfc2833\ncontext=trunkinbound\n", protocol='SIP', globals_string='TESTSIPTRUNK = SIP/testcarrier', dialplan_entry="exten => _91999NXXXXXX,1,AGI(agi://127.0.0.1:4577/call_log)\nexten => _91999NXXXXXX,2,Dial(${TESTSIPTRUNK}/${EXTEN:2},,tTor)\nexten => _91999NXXXXXX,3,Hangup\n", server_ip='10.10.10.15', active='N';
INSERT INTO vicidial_server_carriers SET carrier_id='IAXEXAMPLE', carrier_name='TEST IAX carrier example',registration_string='register => testcarrier:test@10.10.10.15:4569', template_id='--NONE--', account_entry="[testcarrier]\ndisallow=all\nallow=ulaw\ntype=friend\naccountcode=testcarrier\nsecret=test\nhost=dynamic\ncontext=trunkinbound\n", protocol='IAX2', globals_string='TESTIAXTRUNK = IAX2/testcarrier', dialplan_entry="exten => _71999NXXXXXX,1,AGI(agi://127.0.0.1:4577/call_log)\nexten => _71999NXXXXXX,2,Dial(${TESTIAXTRUNK}/${EXTEN:2},,tTor)\nexten => _71999NXXXXXX,3,Hangup\n", server_ip='10.10.10.15', active='N';

INSERT INTO vicidial_inbound_dids SET did_pattern='default', did_description='Default DID', did_active='Y', did_route='EXTEN', extension='9998811112', exten_context='default';

INSERT INTO vicidial_override_ids(id_table,active,value) values('vicidial_users','0','1000');
INSERT INTO vicidial_override_ids(id_table,active,value) values('vicidial_campaigns','0','20000');
INSERT INTO vicidial_override_ids(id_table,active,value) values('vicidial_inbound_groups','0','30000');
INSERT INTO vicidial_override_ids(id_table,active,value) values('vicidial_lists','0','40000');
INSERT INTO vicidial_override_ids(id_table,active,value) values('vicidial_call_menu','0','50000');
INSERT INTO vicidial_override_ids(id_table,active,value) values('vicidial_user_groups','0','60000');
INSERT INTO vicidial_override_ids(id_table,active,value) values('vicidial_lead_filters','0','70000');
INSERT INTO vicidial_override_ids(id_table,active,value) values('vicidial_scripts','0','80000');
INSERT INTO vicidial_override_ids(id_table,active,value) values('phones','0','100');

INSERT INTO vicidial_lead_filters(lead_filter_id,lead_filter_name,lead_filter_comments,lead_filter_sql) values('DROP72HOUR','UK 72 hour Drop No Call','Prevents dropped calls from being called within 72 hours of the last attempt',"( ( (status='DROP') and (last_local_call_time < CONCAT(DATE_ADD(CURDATE(), INTERVAL -3 DAY),' ',CURTIME()) ) ) or (status != 'DROP') )");

INSERT INTO vicidial_drop_rate_groups SET group_id='101';
INSERT INTO vicidial_drop_rate_groups SET group_id='102';
INSERT INTO vicidial_drop_rate_groups SET group_id='103';
INSERT INTO vicidial_drop_rate_groups SET group_id='104';
INSERT INTO vicidial_drop_rate_groups SET group_id='105';
INSERT INTO vicidial_drop_rate_groups SET group_id='106';
INSERT INTO vicidial_drop_rate_groups SET group_id='107';
INSERT INTO vicidial_drop_rate_groups SET group_id='108';
INSERT INTO vicidial_drop_rate_groups SET group_id='109';
INSERT INTO vicidial_drop_rate_groups SET group_id='110';

INSERT INTO vicidial_process_triggers SET trigger_id='LOAD_LEADS',server_ip='10.10.10.15',trigger_name='Load Leads',trigger_time='2009-01-01 00:00:00',trigger_run='0',trigger_lines='/usr/share/astguiclient/VICIDIAL_IN_new_leads_file.pl';

INSERT INTO vicidial_call_menu SET menu_id='defaultlog',menu_name='logging of all outbound calls from agent phones',menu_prompt='sip-silence',menu_timeout='20',menu_timeout_prompt='NONE',menu_invalid_prompt='NONE',menu_repeat='0',menu_time_check='0',call_time_id='',track_in_vdac='0',custom_dialplan_entry='exten => _X.,1,AGI(agi-NVA_recording.agi,BOTH------Y---Y---Y)\nexten => _X.,n,Goto(default,${EXTEN},1)',tracking_group='';

INSERT INTO vicidial_call_menu_options SET menu_id='defaultlog',option_value='TIMEOUT',option_description='hangup',option_route='HANGUP',option_route_value='vm-goodbye',option_route_value_context='';

INSERT INTO vicidial_scripts (script_id,script_name,script_comments,active,script_text) values('CALLNOTES','Call Notes and Appointment Setting','','Y','<iframe src=\"../agc/vdc_script_notes.php?lead_id=--A--lead_id--B--&vendor_id=--A--vendor_lead_code--B--&list_id=--A--list_id--B--&gmt_offset_now=--A--gmt_offset_now--B--&phone_code=--A--phone_code--B--&phone_number=--A--phone_number--B--&title=--A--title--B--&first_name=--A--first_name--B--&middle_initial=--A--middle_initial--B--&last_name=--A--last_name--B--&address1=--A--address1--B--&address2=--A--address2--B--&address3=--A--address3--B--&city=--A--city--B--&state=--A--state--B--&province=--A--province--B--&postal_code=--A--postal_code--B--&country_code=--A--country_code--B--&gender=--A--gender--B--&date_of_birth=--A--date_of_birth--B--&alt_phone=--A--alt_phone--B--&email=--A--email--B--&security_phrase=--A--security_phrase--B--&comments=--A--comments--B--&user=--A--user--B--&pass=--A--pass--B--&campaign=--A--campaign--B--&phone_login=--A--phone_login--B--&fronter=--A--fronter--B--&closer=--A--user--B--&group=--A--group--B--&channel_group=--A--group--B--&SQLdate=--A--SQLdate--B--&epoch=--A--epoch--B--&uniqueid=--A--uniqueid--B--&rank=--A--rank--B--&owner=--A--owner--B--&customer_zap_channel=--A--customer_zap_channel--B--&server_ip=--A--server_ip--B--&SIPexten=--A--SIPexten--B--&session_id=--A--session_id--B--\" style=\"background-color:transparent;\" scrolling=\"auto\" frameborder=\"0\" allowtransparency=\"true\" id=\"popupFrame\" name=\"popupFrame\"  width=\"--A--script_width--B--\" height=\"--A--script_height--B--\" STYLE=\"z-index:17\"> </iframe>');

UPDATE system_settings SET qc_last_pull_time=NOW();

UPDATE system_settings SET voicemail_timezones="newzealand=Pacific/Auckland\naustraliaeast=Australia/Sydney\naustraliacentral=Australia/Adelaide\naustraliawest=Australia/Perth\njapan=Asia/Tokyo\nphilippines=Asia/Manila\nchina=Asia/Shanghai\nmalaysia=Asia/Kuala_Lumpur\nthailand=Asia/Bangkok\nindia=Asia/Calcutta\npakistan=Asia/Karachi\nrussiaeast=Europe/Moscow\nkenya=Africa/Nairobi\neuropeaneast=Europe/Kiev\nsouthafrica=Africa/Johannesburg\neuropean=Europe/Copenhagen\nnigeria=Africa/Lagos\nuk=Europe/London\nbrazil=America/Sao_Paulo\nnewfoundland=Canada/Newfoundland\ncarribeaneast=America/Santo_Domingo\natlantic=Canada/Atlantic\nchile=America/Santiago\neastern=America/New_York\nperu=America/Lima\ncentral=America/Chicago\nmexicocity=America/Mexico_City\nmountain=America/Denver\narizona=America/Phoenix\nsaskatchewan=America/Saskatchewan\npacific=America/Los_Angeles\nalaska=America/Anchorage\nhawaii=Pacific/Honolulu\neastern24=America/New_York\ncentral24=America/Chicago\nmountain24=America/Denver\npacific24=America/Los_Angeles\nmilitary=Zulu\n";

CREATE INDEX country_postal_code on vicidial_postal_codes (country_code,postal_code);
CREATE INDEX country_area_code on vicidial_phone_codes (country_code,areacode);
CREATE INDEX country_state on vicidial_phone_codes (country_code,state);
CREATE INDEX country_code on vicidial_phone_codes (country_code);
CREATE INDEX phone_list on vicidial_list (phone_number,list_id);
CREATE INDEX list_phone on vicidial_list (list_id,phone_number);
CREATE INDEX start_time on call_log (start_time);
CREATE INDEX end_time on call_log (end_time);
CREATE INDEX time on call_log (start_time,end_time);
CREATE INDEX list_status on vicidial_list (list_id,status);
CREATE INDEX time_user on vicidial_agent_log (event_time,user);
CREATE INDEX date_user on vicidial_xfer_log (call_date,user);
CREATE INDEX date_closer on vicidial_xfer_log (call_date,closer);
CREATE INDEX phone_number on vicidial_xfer_log (phone_number);
CREATE INDEX phone_number on vicidial_closer_log (phone_number);
CREATE INDEX date_user on vicidial_closer_log (call_date,user);
CREATE INDEX comment_a on live_inbound_log (comment_a);
CREATE UNIQUE INDEX vicidial_campaign_statuses_key on vicidial_campaign_statuses(status, campaign_id);

CREATE TABLE call_log_archive LIKE call_log; 

CREATE TABLE vicidial_log_archive LIKE vicidial_log;

CREATE TABLE vicidial_agent_log_archive LIKE vicidial_agent_log; 
ALTER TABLE vicidial_agent_log_archive MODIFY agent_log_id INT(9) UNSIGNED NOT NULL;

CREATE TABLE vicidial_carrier_log_archive LIKE vicidial_carrier_log;

CREATE TABLE vicidial_call_notes_archive LIKE vicidial_call_notes; 
ALTER TABLE vicidial_call_notes_archive MODIFY notesid INT(9) UNSIGNED NOT NULL;

CREATE TABLE vicidial_lead_search_log_archive LIKE vicidial_lead_search_log; 
ALTER TABLE vicidial_lead_search_log_archive MODIFY search_log_id INT(9) UNSIGNED NOT NULL;

CREATE TABLE vicidial_closer_log_archive LIKE vicidial_closer_log; 
ALTER TABLE vicidial_closer_log_archive MODIFY closecallid INT(9) UNSIGNED NOT NULL;

CREATE TABLE vicidial_outbound_ivr_log_archive LIKE vicidial_outbound_ivr_log;

CREATE TABLE vicidial_log_extended_archive LIKE vicidial_log_extended;
CREATE UNIQUE INDEX vlea on vicidial_log_extended_archive (uniqueid,call_date,lead_id);

CREATE TABLE vicidial_log_noanswer_archive LIKE vicidial_log_noanswer; 

CREATE TABLE vicidial_did_agent_log_archive LIKE vicidial_did_agent_log; 
CREATE UNIQUE INDEX vdala on vicidial_did_agent_log_archive (uniqueid,call_date,did_route);

UPDATE system_settings SET db_schema_version='1302',db_schema_update_date=NOW();

GRANT RELOAD ON *.* TO cron@'%';
GRANT RELOAD ON *.* TO cron@localhost;

GRANT ALTER,CREATE on asterisk.* TO custom@'%' IDENTIFIED BY 'custom1234';
GRANT ALTER,CREATE on asterisk.* TO custom@localhost IDENTIFIED BY 'custom1234';

flush privileges;

