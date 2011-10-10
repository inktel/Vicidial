ALTER TABLE vicidial_nanpa_prefix_codes ADD city VARCHAR(50) default '';
ALTER TABLE vicidial_nanpa_prefix_codes ADD state VARCHAR(2) default '';
ALTER TABLE vicidial_nanpa_prefix_codes ADD postal_code VARCHAR(10) default '';
ALTER TABLE vicidial_nanpa_prefix_codes ADD country VARCHAR(2) default '';

UPDATE system_settings SET db_schema_version='1136', version='2.2.0b0.5';

ALTER TABLE vicidial_users ADD delete_from_dnc ENUM('0','1') default '0';

ALTER TABLE vicidial_campaigns ADD vtiger_search_dead ENUM('DISABLED','ASK','RESURRECT') default 'ASK';
ALTER TABLE vicidial_campaigns ADD vtiger_status_call ENUM('Y','N') default 'N';
ALTER TABLE vicidial_campaigns MODIFY vtiger_screen_login ENUM('Y','N','NEW_WINDOW') default 'Y';
ALTER TABLE vicidial_campaigns MODIFY vtiger_create_call_record ENUM('Y','N','DISPO') default 'Y';

ALTER TABLE vicidial_statuses ADD sale ENUM('Y','N') default 'N';
ALTER TABLE vicidial_statuses ADD dnc ENUM('Y','N') default 'N';
ALTER TABLE vicidial_statuses ADD customer_contact ENUM('Y','N') default 'N';
ALTER TABLE vicidial_statuses ADD not_interested ENUM('Y','N') default 'N';
ALTER TABLE vicidial_statuses ADD unworkable ENUM('Y','N') default 'N';
ALTER TABLE vicidial_campaign_statuses ADD sale ENUM('Y','N') default 'N';
ALTER TABLE vicidial_campaign_statuses ADD dnc ENUM('Y','N') default 'N';
ALTER TABLE vicidial_campaign_statuses ADD customer_contact ENUM('Y','N') default 'N';
ALTER TABLE vicidial_campaign_statuses ADD not_interested ENUM('Y','N') default 'N';
ALTER TABLE vicidial_campaign_statuses ADD unworkable ENUM('Y','N') default 'N';

UPDATE system_settings SET db_schema_version='1137';

ALTER TABLE vicidial_users ADD email VARCHAR(100) default '';
ALTER TABLE vicidial_users ADD user_code VARCHAR(100) default '';
ALTER TABLE vicidial_users ADD territory VARCHAR(100) default '';

UPDATE system_settings SET db_schema_version='1138';

ALTER TABLE vicidial_campaigns ADD survey_third_digit VARCHAR(1) default '';
ALTER TABLE vicidial_campaigns ADD survey_third_audio_file VARCHAR(50) default 'US_thanks_no_contact';
ALTER TABLE vicidial_campaigns ADD survey_third_status VARCHAR(6) default 'NI';
ALTER TABLE vicidial_campaigns ADD survey_third_exten VARCHAR(20) default '8300';
ALTER TABLE vicidial_campaigns ADD survey_fourth_digit VARCHAR(1) default '';
ALTER TABLE vicidial_campaigns ADD survey_fourth_audio_file VARCHAR(50) default 'US_thanks_no_contact';
ALTER TABLE vicidial_campaigns ADD survey_fourth_status VARCHAR(6) default 'NI';
ALTER TABLE vicidial_campaigns ADD survey_fourth_exten VARCHAR(20) default '8300';

ALTER TABLE system_settings ADD enable_tts_integration ENUM('0','1') default '0';

CREATE TABLE vicidial_tts_prompts (
tts_id VARCHAR(50) PRIMARY KEY NOT NULL,
tts_name VARCHAR(100),
active ENUM('Y','N'),
tts_text TEXT
);

UPDATE system_settings SET db_schema_version='1139';

CREATE TABLE vicidial_call_menu (
menu_id VARCHAR(50) PRIMARY KEY NOT NULL,
menu_name VARCHAR(100),
menu_prompt VARCHAR(100),
menu_timeout SMALLINT(2) UNSIGNED default '10',
menu_timeout_prompt VARCHAR(100) default 'NONE',
menu_invalid_prompt VARCHAR(100) default 'NONE',
menu_repeat TINYINT(1) UNSIGNED default '0',
menu_time_check ENUM('0','1') default '0',
call_time_id VARCHAR(20) default '',
track_in_vdac ENUM('0','1') default '1'
);

CREATE TABLE vicidial_call_menu_options (
menu_id VARCHAR(50) NOT NULL,
option_value VARCHAR(20) NOT NULL default '',
option_description VARCHAR(255) default '',
option_route VARCHAR(20),
option_route_value VARCHAR(100),
option_route_value_context VARCHAR(100),
index (menu_id),
unique index menuoption (menu_id, option_value)
);

ALTER TABLE vicidial_inbound_dids MODIFY did_route ENUM('EXTEN','VOICEMAIL','AGENT','PHONE','IN_GROUP','CALLMENU') default 'EXTEN';
ALTER TABLE vicidial_inbound_dids ADD menu_id VARCHAR(50) default '';

UPDATE system_settings SET db_schema_version='1140';

ALTER TABLE system_settings ADD agentonly_callback_campaign_lock ENUM('0','1') default '1';

UPDATE system_settings SET db_schema_version='1141';

ALTER TABLE system_settings ADD sounds_central_control_active ENUM('0','1') default '0';
ALTER TABLE system_settings ADD sounds_web_server VARCHAR(15) default '127.0.0.1';
ALTER TABLE system_settings ADD sounds_web_directory VARCHAR(255) default '';

ALTER TABLE servers ADD sounds_update ENUM('Y','N') default 'N';

CREATE TABLE vicidial_user_territories (
user VARCHAR(20) NOT NULL,
territory VARCHAR(100) default '',
index (user),
unique index userterritory (user, territory)
);

UPDATE system_settings SET db_schema_version='1142';

ALTER TABLE system_settings ADD active_voicemail_server VARCHAR(15) default '';
ALTER TABLE system_settings ADD auto_dial_limit VARCHAR(5) default '4';

UPDATE system_settings SET db_schema_version='1143';

CREATE TABLE vicidial_territories (
territory_id MEDIUMINT(8) UNSIGNED AUTO_INCREMENT PRIMARY KEY NOT NULL,
territory VARCHAR(100) default '',
territory_description VARCHAR(255) default '',
unique index uniqueterritory (territory)
);

ALTER TABLE vicidial_user_territories ADD level ENUM('TOP_AGENT','STANDARD_AGENT','BOTTOM_AGENT') default 'STANDARD_AGENT';

ALTER TABLE system_settings ADD user_territories_active ENUM('0','1') default '0';

UPDATE system_settings SET db_schema_version='1144';

ALTER TABLE servers ADD vicidial_recording_limit MEDIUMINT(8) default '60';

ALTER TABLE phones ADD phone_context VARCHAR(20) default 'default';

UPDATE system_settings SET db_schema_version='1145';

CREATE UNIQUE INDEX extenserver ON phones (extension, server_ip);

UPDATE system_settings SET db_schema_version='1146';

CREATE TABLE vicidial_override_ids (
id_table VARCHAR(50) PRIMARY KEY NOT NULL,
active ENUM('0','1') default '0',
value INT(9) default '0'
);

INSERT INTO vicidial_override_ids(id_table,active,value) values('vicidial_users','0','1000');
INSERT INTO vicidial_override_ids(id_table,active,value) values('vicidial_campaigns','0','20000');
INSERT INTO vicidial_override_ids(id_table,active,value) values('vicidial_inbound_groups','0','30000');
INSERT INTO vicidial_override_ids(id_table,active,value) values('vicidial_lists','0','40000');
INSERT INTO vicidial_override_ids(id_table,active,value) values('vicidial_call_menu','0','50000');
INSERT INTO vicidial_override_ids(id_table,active,value) values('vicidial_user_groups','0','60000');
INSERT INTO vicidial_override_ids(id_table,active,value) values('vicidial_lead_filters','0','70000');
INSERT INTO vicidial_override_ids(id_table,active,value) values('vicidial_scripts','0','80000');
INSERT INTO vicidial_override_ids(id_table,active,value) values('phones','0','100');

ALTER TABLE vicidial_campaigns MODIFY disable_alter_custphone ENUM('Y','N','HIDE') default 'Y';

UPDATE system_settings SET db_schema_version='1147';

CREATE TABLE vicidial_carrier_log (
uniqueid VARCHAR(20) PRIMARY KEY NOT NULL,
call_date DATETIME,
server_ip VARCHAR(15) NOT NULL,
lead_id INT(9) UNSIGNED,
hangup_cause TINYINT(1) UNSIGNED default '0',
dialstatus VARCHAR(16),
channel VARCHAR(100),
dial_time SMALLINT(2) UNSIGNED default '0',
index (call_date)
);

ALTER TABLE servers ADD carrier_logging_active ENUM('Y','N') default 'N';

UPDATE system_settings SET db_schema_version='1148';

ALTER TABLE vicidial_campaigns MODIFY adaptive_dropped_percentage VARCHAR(4) default '3';

INSERT INTO vicidial_statuses values('AB','Busy Auto','N','N','UNDEFINED','N','N','N','N','N');
INSERT INTO vicidial_statuses values('ADC','Disconnected Number Auto','N','N','UNDEFINED','N','N','N','N','Y');

UPDATE system_settings SET db_schema_version='1149';

ALTER TABLE vicidial_campaigns ADD drop_lockout_time VARCHAR(6) default '0';

UPDATE system_settings SET db_schema_version='1150';

ALTER TABLE vicidial_live_agents ADD agent_log_id INT(9) UNSIGNED default '0';

UPDATE system_settings SET db_schema_version='1151';

ALTER TABLE system_settings ADD allow_custom_dialplan ENUM('0','1') default '0';

ALTER TABLE vicidial_call_menu ADD custom_dialplan_entry TEXT;

ALTER TABLE phones ADD phone_ring_timeout SMALLINT(3) default '60';

UPDATE system_settings SET db_schema_version='1152';

ALTER TABLE vicidial_call_menu MODIFY menu_prompt VARCHAR(255);
ALTER TABLE vicidial_call_menu MODIFY menu_timeout_prompt VARCHAR(255) default 'NONE';
ALTER TABLE vicidial_call_menu MODIFY menu_invalid_prompt VARCHAR(255) default 'NONE';

ALTER TABLE vicidial_call_menu_options MODIFY option_route_value VARCHAR(255);

UPDATE system_settings SET db_schema_version='1153';

ALTER TABLE phones ADD conf_secret VARCHAR(20) default 'test';
UPDATE phones set conf_secret=pass where ( (conf_secret IS NULL) or (conf_secret='') );

UPDATE system_settings SET db_schema_version='1154';

ALTER TABLE vicidial_call_menu ADD tracking_group VARCHAR(20) default 'CALLMENU';

UPDATE system_settings SET db_schema_version='1155';

ALTER TABLE vicidial_inbound_groups MODIFY after_hours_message_filename VARCHAR(255) default 'vm-goodbye';
ALTER TABLE vicidial_inbound_groups MODIFY welcome_message_filename VARCHAR(255) default '---NONE---';
ALTER TABLE vicidial_inbound_groups MODIFY onhold_prompt_filename VARCHAR(255) default 'generic_hold';
ALTER TABLE vicidial_inbound_groups MODIFY hold_time_option_callback_filename VARCHAR(255) default 'vm-hangup';
ALTER TABLE vicidial_inbound_groups MODIFY agent_alert_exten VARCHAR(100) default 'ding';

UPDATE system_settings SET db_schema_version='1156';

ALTER TABLE vicidial_inbound_groups ADD no_agent_no_queue ENUM('N','Y','NO_PAUSED') default 'N';
ALTER TABLE vicidial_inbound_groups ADD no_agent_action ENUM('CALLMENU','INGROUP','DID','MESSAGE','EXTENSION','VOICEMAIL') default 'MESSAGE';
ALTER TABLE vicidial_inbound_groups ADD no_agent_action_value VARCHAR(255) default 'nbdy-avail-to-take-call|vm-goodbye';

ALTER TABLE vicidial_closer_log MODIFY term_reason  ENUM('CALLER','AGENT','QUEUETIMEOUT','ABANDON','AFTERHOURS','HOLDRECALLXFER','HOLDTIME','NOAGENT','NONE') default 'NONE';

UPDATE system_settings SET db_schema_version='1157';

CREATE TABLE vicidial_list_update_log (
event_date DATETIME,
lead_id INT(9) UNSIGNED,
vendor_id VARCHAR(20),
phone_number VARCHAR(20),
status VARCHAR(6),
old_status VARCHAR(6),
filename VARCHAR(255) default '',
result VARCHAR(20),
result_rows SMALLINT(3) UNSIGNED default '0',
index (event_date)
);

ALTER TABLE vicidial_campaigns ADD quick_transfer_button ENUM('N','IN_GROUP','PRESET_1','PRESET_2') default 'N';
ALTER TABLE vicidial_campaigns ADD prepopulate_transfer_preset ENUM('N','PRESET_1','PRESET_2') default 'N';

UPDATE system_settings SET db_schema_version='1158';

CREATE TABLE vicidial_drop_rate_groups (
group_id VARCHAR(20) PRIMARY KEY NOT NULL,
update_time TIMESTAMP,
calls_today INT(9) UNSIGNED default '0',
answers_today INT(9) UNSIGNED default '0',
drops_today INT(9) UNSIGNED default '0',
drops_today_pct VARCHAR(6) default '0',
drops_answers_today_pct VARCHAR(6) default '0'
);

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

ALTER TABLE vicidial_campaigns ADD drop_rate_group VARCHAR(20) default 'DISABLED';

UPDATE system_settings SET db_schema_version='1159';

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

INSERT INTO vicidial_process_triggers SET trigger_id='LOAD_LEADS',server_ip='10.10.10.15',trigger_name='Load Leads',trigger_time='2009-01-01 00:00:00',trigger_run='0',trigger_lines='/usr/share/astguiclient/VICIDIAL_IN_new_leads_file.pl';

UPDATE system_settings SET db_schema_version='1160';

ALTER TABLE vicidial_user_groups ADD agent_status_viewable_groups TEXT;
ALTER TABLE vicidial_user_groups ADD agent_status_view_time ENUM('Y','N') default 'N';

UPDATE system_settings SET db_schema_version='1161';

ALTER TABLE vicidial_campaigns ADD view_calls_in_queue ENUM('NONE','ALL','1','2','3','4','5') default 'NONE';
ALTER TABLE vicidial_campaigns ADD view_calls_in_queue_launch ENUM('AUTO','MANUAL') default 'MANUAL';
ALTER TABLE vicidial_campaigns ADD grab_calls_in_queue ENUM('Y','N') default 'N';
ALTER TABLE vicidial_campaigns ADD call_requeue_button ENUM('Y','N') default 'N';
ALTER TABLE vicidial_campaigns ADD pause_after_each_call ENUM('Y','N') default 'N';

ALTER TABLE vicidial_auto_calls ADD agent_grab VARCHAR(20) default '';

UPDATE system_settings SET db_schema_version='1162';

ALTER TABLE vicidial_list MODIFY list_id BIGINT(14) UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE vicidial_list ADD rank SMALLINT(5) NOT NULL default '0';
ALTER TABLE vicidial_list ADD owner VARCHAR(20) default '';
CREATE INDEX rank ON vicidial_list (rank);

ALTER TABLE vicidial_campaigns ADD no_hopper_dialing ENUM('Y','N') default 'N';
ALTER TABLE vicidial_campaigns ADD agent_dial_owner_only ENUM('NONE','USER','TERRITORY','USER_GROUP') default 'NONE';

ALTER TABLE vicidial_lists ADD reset_time VARCHAR(100) default '';

UPDATE system_settings SET db_schema_version='1163';

ALTER TABLE vicidial_users ADD allow_alerts ENUM('0','1') default '0';
UPDATE vicidial_users SET alert_enabled='0';

UPDATE system_settings SET db_schema_version='1164';

ALTER TABLE vicidial_campaigns ADD agent_display_dialable_leads ENUM('Y','N') default 'N';
ALTER TABLE servers ADD vicidial_balance_rank TINYINT(3) UNSIGNED default '0';

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

UPDATE system_settings SET db_schema_version='1165';

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
index (lead_id),
index (user),
index (event_time)
);

UPDATE system_settings SET db_schema_version='1166';

ALTER TABLE vicidial_live_agents ADD last_state_change DATETIME;

ALTER TABLE vicidial_campaigns MODIFY next_agent_call ENUM('random','oldest_call_start','oldest_call_finish','campaign_rank','overall_user_level','fewest_calls','longest_wait_time') default 'longest_wait_time';

ALTER TABLE vicidial_inbound_groups MODIFY next_agent_call ENUM('random','oldest_call_start','oldest_call_finish','overall_user_level','inbound_group_rank','campaign_rank','fewest_calls','fewest_calls_campaign','longest_wait_time') default 'longest_wait_time';

UPDATE system_settings SET db_schema_version='1167';

ALTER TABLE vicidial_campaigns MODIFY drop_call_seconds TINYINT(3) default '5';

UPDATE system_settings SET db_schema_version='1168';

ALTER TABLE vicidial_lists ADD agent_script_override VARCHAR(10) default '';

UPDATE system_settings SET db_schema_version='1169';

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

INSERT INTO vicidial_music_on_hold SET moh_id='default',moh_name='Default Music On Hold',active='Y',random='N';
INSERT INTO vicidial_music_on_hold_files SET moh_id='default',filename='conf',rank='1';

ALTER TABLE servers ADD rebuild_music_on_hold ENUM('Y','N') default 'Y';
ALTER TABLE servers ADD active_agent_login_server ENUM('Y','N') default 'Y';

UPDATE system_settings SET db_schema_version='1170';

ALTER TABLE vicidial_list_update_log ADD list_id VARCHAR(255);
ALTER TABLE vicidial_list_update_log MODIFY lead_id VARCHAR(255);
ALTER TABLE vicidial_list_update_log MODIFY vendor_id VARCHAR(255);
ALTER TABLE vicidial_list_update_log MODIFY phone_number VARCHAR(255);
ALTER TABLE vicidial_list_update_log MODIFY old_status VARCHAR(255);

UPDATE system_settings SET db_schema_version='1171';

ALTER TABLE vicidial_agent_log ADD dead_epoch INT(10) UNSIGNED;
ALTER TABLE vicidial_agent_log ADD dead_sec SMALLINT(5) UNSIGNED default '0';

ALTER TABLE twoday_vicidial_agent_log ADD dead_epoch INT(10) UNSIGNED;
ALTER TABLE twoday_vicidial_agent_log ADD dead_sec SMALLINT(5) UNSIGNED default '0';

ALTER TABLE system_settings MODIFY sounds_web_server VARCHAR(50) default '127.0.0.1';

UPDATE system_settings SET db_schema_version='1172';

ALTER TABLE vicidial_inbound_groups MODIFY web_form_address TEXT;
ALTER TABLE vicidial_inbound_groups ADD web_form_address_two TEXT;

ALTER TABLE vicidial_campaigns MODIFY web_form_address TEXT;
ALTER TABLE vicidial_campaigns ADD web_form_address_two TEXT;
ALTER TABLE vicidial_campaigns ADD waitforsilence_options VARCHAR(25) default '';
ALTER TABLE vicidial_campaigns MODIFY am_message_exten VARCHAR(100) default 'vm-goodbye';
UPDATE vicidial_campaigns SET am_message_exten='vm-goodbye' where am_message_exten='';

ALTER TABLE system_settings ADD db_schema_update_date DATETIME;
ALTER TABLE system_settings ADD enable_second_webform ENUM('0','1') default '0';

UPDATE system_settings SET db_schema_version='1173',db_schema_update_date=NOW();

CREATE TABLE vicidial_voicemail (
voicemail_id VARCHAR(10) NOT NULL UNIQUE PRIMARY KEY,
active ENUM('Y','N') default 'Y',
pass VARCHAR(10) NOT NULL,
fullname VARCHAR(100) NOT NULL,
messages INT(4) default '0',
old_messages INT(4) default '0',
email VARCHAR(100)
);

UPDATE system_settings SET db_schema_version='1174',db_schema_update_date=NOW();

ALTER TABLE vicidial_lists ADD campaign_cid_override VARCHAR(20) default '';
ALTER TABLE vicidial_lists ADD am_message_exten_override VARCHAR(100) default '';
ALTER TABLE vicidial_lists ADD drop_inbound_group_override VARCHAR(20) default '';

UPDATE system_settings SET db_schema_version='1175',db_schema_update_date=NOW();

ALTER TABLE vicidial_campaigns ADD agent_select_territories ENUM('Y','N') default 'N';

ALTER TABLE vicidial_users ADD agent_choose_territories ENUM('0','1') default '1';

ALTER TABLE vicidial_live_agents ADD agent_territories TEXT;

CREATE INDEX owner ON vicidial_list (owner);

CREATE TABLE vicidial_user_territory_log (
user VARCHAR(20),
campaign_id VARCHAR(20),
event_date DATETIME,
agent_territories TEXT,
index (user),
index (event_date)
);

UPDATE system_settings SET db_schema_version='1176',db_schema_update_date=NOW();

CREATE UNIQUE INDEX vlia_user_group_id ON vicidial_live_inbound_agents (user, group_id);

ALTER TABLE vicidial_hopper MODIFY status ENUM('READY','QUEUE','INCALL','DONE','HOLD','DNC') default 'READY';

UPDATE system_settings SET db_schema_version='1177',db_schema_update_date=NOW();

ALTER TABLE vicidial_campaigns MODIFY use_internal_dnc ENUM('Y','N','AREACODE') default 'N';
ALTER TABLE vicidial_campaigns MODIFY use_campaign_dnc ENUM('Y','N','AREACODE') default 'N';

UPDATE system_settings SET db_schema_version='1178',db_schema_update_date=NOW();

ALTER TABLE vicidial_server_carriers ADD carrier_description VARCHAR(255);

UPDATE system_settings SET db_schema_version='1179',db_schema_update_date=NOW();

CREATE INDEX group_alias_id ON user_call_log (group_alias_id);

INSERT INTO vicidial_call_menu SET menu_id='defaultlog',menu_name='logging of all outbound calls from agent phones',menu_prompt='sip-silence',menu_timeout='20',menu_timeout_prompt='NONE',menu_invalid_prompt='NONE',menu_repeat='0',menu_time_check='0',call_time_id='',track_in_vdac='0',custom_dialplan_entry='exten => _X.,1,AGI(agi-NVA_recording.agi,BOTH------Y---Y---Y)\nexten => _X.,n,Goto(default,${EXTEN},1)',tracking_group='';

INSERT INTO vicidial_call_menu_options SET menu_id='defaultlog',option_value='TIMEOUT',option_description='hangup',option_route='HANGUP',option_route_value='vm-goodbye',option_route_value_context='';

UPDATE system_settings SET db_schema_version='1180',db_schema_update_date=NOW();

ALTER TABLE vicidial_live_agents ADD outbound_autodial ENUM('Y','N') default 'N';
ALTER TABLE vicidial_live_agents ADD manager_ingroup_set ENUM('Y','N') default 'N';

UPDATE system_settings SET db_schema_version='1181',db_schema_update_date=NOW();

ALTER TABLE servers ADD conf_secret VARCHAR(20) default 'test';

UPDATE system_settings SET db_schema_version='1182',db_schema_update_date=NOW();

ALTER TABLE vicidial_live_agents MODIFY manager_ingroup_set ENUM('Y','N','SET') default 'N';
ALTER TABLE vicidial_live_agents ADD external_ingroups TEXT AFTER external_dial;
ALTER TABLE vicidial_live_agents ADD external_blended ENUM('0','1') default '0' AFTER external_ingroups;
ALTER TABLE vicidial_live_agents ADD external_igb_set_user VARCHAR(20) default '' AFTER external_blended;

ALTER TABLE vicidial_user_closer_log ADD manager_change VARCHAR(20) default '';

UPDATE system_settings SET db_schema_version='1183',db_schema_update_date=NOW();

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

UPDATE system_settings SET db_schema_version='1184',db_schema_update_date=NOW();

ALTER TABLE phones ADD delete_vm_after_email ENUM('N','Y') default 'N';

ALTER TABLE vicidial_voicemail ADD delete_vm_after_email ENUM('N','Y') default 'N';

UPDATE system_settings SET db_schema_version='1185',db_schema_update_date=NOW();

ALTER TABLE vicidial_campaigns ADD campaign_calldate DATETIME;

UPDATE system_settings SET db_schema_version='1186',db_schema_update_date=NOW();

ALTER TABLE vicidial_users ADD custom_one VARCHAR(100) default '';
ALTER TABLE vicidial_users ADD custom_two VARCHAR(100) default '';
ALTER TABLE vicidial_users ADD custom_three VARCHAR(100) default '';
ALTER TABLE vicidial_users ADD custom_four VARCHAR(100) default '';
ALTER TABLE vicidial_users ADD custom_five VARCHAR(100) default '';

ALTER TABLE vicidial_campaigns ADD crm_popup_login ENUM('Y','N') default 'N';
ALTER TABLE vicidial_campaigns ADD crm_login_address TEXT;

UPDATE system_settings SET db_schema_version='1187',db_schema_update_date=NOW();

ALTER TABLE vicidial_closer_log ADD queue_position SMALLINT(4) UNSIGNED default '1';

ALTER TABLE vicidial_auto_calls ADD queue_position SMALLINT(4) UNSIGNED default '1';

UPDATE system_settings SET db_schema_version='1188',db_schema_update_date=NOW();

ALTER TABLE vicidial_live_agents ADD external_update_fields ENUM('0','1') default '0' AFTER external_igb_set_user;
ALTER TABLE vicidial_live_agents ADD external_update_fields_data VARCHAR(255) default '' AFTER external_update_fields;
ALTER TABLE vicidial_live_agents ADD external_timer_action VARCHAR(20) default '' AFTER external_update_fields_data;
ALTER TABLE vicidial_live_agents ADD external_timer_action_message VARCHAR(255) default '' AFTER external_timer_action;
ALTER TABLE vicidial_live_agents ADD external_timer_action_seconds MEDIUMINT(7) default '-1' AFTER external_timer_action_message;

ALTER TABLE vicidial_campaigns ADD timer_action ENUM('NONE','WEBFORM','WEBFORM2','D1_DIAL','D2_DIAL','MESSAGE_ONLY') default 'NONE';
ALTER TABLE vicidial_campaigns ADD timer_action_message VARCHAR(255) default '';
ALTER TABLE vicidial_campaigns ADD timer_action_seconds MEDIUMINT(7) default '-1';

ALTER TABLE vicidial_inbound_groups ADD timer_action ENUM('NONE','WEBFORM','WEBFORM2','D1_DIAL','D2_DIAL','MESSAGE_ONLY') default 'NONE';
ALTER TABLE vicidial_inbound_groups ADD timer_action_message VARCHAR(255) default '';
ALTER TABLE vicidial_inbound_groups ADD timer_action_seconds MEDIUMINT(7) default '-1';

UPDATE system_settings SET db_schema_version='1189',db_schema_update_date=NOW();

ALTER TABLE vicidial_campaigns ADD start_call_url TEXT;
ALTER TABLE vicidial_campaigns ADD dispo_call_url TEXT;
ALTER TABLE vicidial_campaigns ADD xferconf_c_number VARCHAR(50) default '';
ALTER TABLE vicidial_campaigns ADD xferconf_d_number VARCHAR(50) default '';
ALTER TABLE vicidial_campaigns ADD xferconf_e_number VARCHAR(50) default '';
ALTER TABLE vicidial_campaigns MODIFY quick_transfer_button ENUM('N','IN_GROUP','PRESET_1','PRESET_2','PRESET_3','PRESET_4','PRESET_5') default 'N';
ALTER TABLE vicidial_campaigns MODIFY prepopulate_transfer_preset ENUM('N','PRESET_1','PRESET_2','PRESET_3','PRESET_4','PRESET_5') default 'N';
ALTER TABLE vicidial_campaigns MODIFY timer_action ENUM('NONE','WEBFORM','WEBFORM2','D1_DIAL','D2_DIAL','D3_DIAL','D4_DIAL','D5_DIAL','MESSAGE_ONLY') default 'NONE';

ALTER TABLE vicidial_inbound_groups ADD start_call_url TEXT;
ALTER TABLE vicidial_inbound_groups ADD dispo_call_url TEXT;
ALTER TABLE vicidial_inbound_groups ADD xferconf_c_number VARCHAR(50) default '';
ALTER TABLE vicidial_inbound_groups ADD xferconf_d_number VARCHAR(50) default '';
ALTER TABLE vicidial_inbound_groups ADD xferconf_e_number VARCHAR(50) default '';
ALTER TABLE vicidial_inbound_groups MODIFY timer_action ENUM('NONE','WEBFORM','WEBFORM2','D1_DIAL','D2_DIAL','D3_DIAL','D4_DIAL','D5_DIAL','MESSAGE_ONLY') default 'NONE';

ALTER TABLE vicidial_lists ADD xferconf_a_number VARCHAR(50) default '';
ALTER TABLE vicidial_lists ADD xferconf_b_number VARCHAR(50) default '';
ALTER TABLE vicidial_lists ADD xferconf_c_number VARCHAR(50) default '';
ALTER TABLE vicidial_lists ADD xferconf_d_number VARCHAR(50) default '';
ALTER TABLE vicidial_lists ADD xferconf_e_number VARCHAR(50) default '';

UPDATE system_settings SET db_schema_version='1190',db_schema_update_date=NOW();

CREATE TABLE call_log_archive LIKE call_log; 

CREATE TABLE vicidial_log_archive LIKE vicidial_log;

CREATE TABLE vicidial_agent_log_archive LIKE vicidial_agent_log; 
ALTER TABLE vicidial_agent_log_archive MODIFY agent_log_id INT(9) UNSIGNED NOT NULL;

UPDATE system_settings SET db_schema_version='1191',db_schema_update_date=NOW();

ALTER TABLE vicidial_carrier_log MODIFY dial_time SMALLINT(3) UNSIGNED default '0';
ALTER TABLE vicidial_carrier_log ADD answered_time SMALLINT(4) UNSIGNED default '0';

UPDATE system_settings SET db_schema_version='1192',db_schema_update_date=NOW();

ALTER TABLE servers MODIFY carrier_logging_active ENUM('Y','N') default 'Y';
UPDATE servers SET carrier_logging_active='Y';

CREATE TABLE vicidial_carrier_log_archive LIKE vicidial_carrier_log;

UPDATE system_settings SET db_schema_version='1193',db_schema_update_date=NOW();

UPDATE system_settings SET version='2.2.0rc1',db_schema_update_date=NOW();
