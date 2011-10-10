UPDATE system_settings SET version='2.4b0.5',db_schema_update_date=NOW();

INSERT INTO vicidial_statuses values('TIMEOT','Inbound Queue Timeout Drop','N','Y','UNDEFINED','N','N','N','N','N');
INSERT INTO vicidial_statuses values('AFTHRS','Inbound After Hours Drop','N','Y','UNDEFINED','N','N','N','N','N');
INSERT INTO vicidial_statuses values('NANQUE','Inbound No Agent No Queue Drop','N','Y','UNDEFINED','N','N','N','N','N');

UPDATE system_settings SET db_schema_version='1194',db_schema_update_date=NOW();

ALTER TABLE vicidial_users ADD voicemail_id VARCHAR(10) default '';

ALTER TABLE vicidial_inbound_dids ADD record_call ENUM('Y','N','Y_QUEUESTOP') default 'N';

UPDATE system_settings SET db_schema_version='1195',db_schema_update_date=NOW();

CREATE TABLE vtiger_vicidial_roles (
user_level TINYINT(2),
vtiger_role VARCHAR(5)
);

UPDATE system_settings SET db_schema_version='1196',db_schema_update_date=NOW();

ALTER TABLE vicidial_inbound_groups ADD ignore_list_script_override ENUM('Y','N') default 'N';

UPDATE system_settings SET db_schema_version='1197',db_schema_update_date=NOW();

ALTER TABLE servers ADD external_server_ip VARCHAR(100) default '';
ALTER TABLE servers MODIFY recording_web_link ENUM('SERVER_IP','ALT_IP','EXTERNAL_IP') default 'SERVER_IP';

ALTER TABLE phones ADD is_webphone ENUM('Y','N') default 'N';
ALTER TABLE phones ADD use_external_server_ip ENUM('Y','N') default 'N';

ALTER TABLE system_settings ADD default_webphone ENUM('1','0') default '0';
ALTER TABLE system_settings ADD default_external_server_ip ENUM('1','0') default '0';
ALTER TABLE system_settings ADD webphone_url VARCHAR(255) default '';

UPDATE system_settings SET db_schema_version='1198',db_schema_update_date=NOW();

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

UPDATE system_settings SET db_schema_version='1199',db_schema_update_date=NOW();

CREATE INDEX lead_id on vicidial_call_notes (lead_id);

ALTER TABLE system_settings ADD static_agent_url VARCHAR(255) default '';
ALTER TABLE system_settings ADD default_phone_code VARCHAR(8) default '1';

UPDATE system_settings SET db_schema_version='1200',db_schema_update_date=NOW();

INSERT INTO vicidial_scripts (script_id,script_name,script_comments,active,script_text) values('CALLNOTES','Call Notes and Appointment Setting','','Y','<iframe src=\"../agc/vdc_script_notes.php?lead_id=--A--lead_id--B--&vendor_id=--A--vendor_lead_code--B--&list_id=--A--list_id--B--&gmt_offset_now=--A--gmt_offset_now--B--&phone_code=--A--phone_code--B--&phone_number=--A--phone_number--B--&title=--A--title--B--&first_name=--A--first_name--B--&middle_initial=--A--middle_initial--B--&last_name=--A--last_name--B--&address1=--A--address1--B--&address2=--A--address2--B--&address3=--A--address3--B--&city=--A--city--B--&state=--A--state--B--&province=--A--province--B--&postal_code=--A--postal_code--B--&country_code=--A--country_code--B--&gender=--A--gender--B--&date_of_birth=--A--date_of_birth--B--&alt_phone=--A--alt_phone--B--&email=--A--email--B--&security_phrase=--A--security_phrase--B--&comments=--A--comments--B--&user=--A--user--B--&pass=--A--pass--B--&campaign=--A--campaign--B--&phone_login=--A--phone_login--B--&fronter=--A--fronter--B--&closer=--A--user--B--&group=--A--group--B--&channel_group=--A--group--B--&SQLdate=--A--SQLdate--B--&epoch=--A--epoch--B--&uniqueid=--A--uniqueid--B--&rank=--A--rank--B--&owner=--A--owner--B--&customer_zap_channel=--A--customer_zap_channel--B--&server_ip=--A--server_ip--B--&SIPexten=--A--SIPexten--B--&session_id=--A--session_id--B--\" style=\"background-color:transparent;\" scrolling=\"auto\" frameborder=\"0\" allowtransparency=\"true\" id=\"popupFrame\" name=\"popupFrame\"  width=\"--A--script_width--B--\" height=\"--A--script_height--B--\" STYLE=\"z-index:17\"> </iframe>');

ALTER TABLE system_settings ADD enable_agc_dispo_log ENUM('0','1') default '0';

ALTER TABLE vicidial_user_groups ADD agent_call_log_view ENUM('Y','N') default 'N';

ALTER TABLE vicidial_users ADD agent_call_log_view_override ENUM('DISABLED','Y','N') default 'DISABLED';

UPDATE system_settings SET db_schema_version='1201',db_schema_update_date=NOW();

ALTER TABLE system_settings ADD custom_dialplan_entry TEXT;

ALTER TABLE servers ADD custom_dialplan_entry TEXT;

UPDATE system_settings SET db_schema_version='1202',db_schema_update_date=NOW();

ALTER TABLE vicidial_campaigns ADD use_custom_cid ENUM('Y','N') default 'N';
ALTER TABLE vicidial_campaigns MODIFY three_way_call_cid ENUM('CAMPAIGN','CUSTOMER','AGENT_PHONE','AGENT_CHOOSE','CUSTOM_CID') default 'CAMPAIGN';

CREATE TABLE vicidial_custom_cid (
cid VARCHAR(18) NOT NULL,
state VARCHAR(20),
areacode VARCHAR(6),
country_code SMALLINT(5) UNSIGNED,
campaign_id VARCHAR(8) default '--ALL--',
index (state),
index (areacode)
);

ALTER TABLE vicidial_agent_log ADD processed ENUM('Y','N') default 'N';
ALTER TABLE vicidial_agent_log_archive ADD processed ENUM('Y','N') default 'N';

UPDATE system_settings SET db_schema_version='1203',db_schema_update_date=NOW();

ALTER TABLE vicidial_campaigns ADD scheduled_callbacks_alert ENUM('NONE','BLINK','RED','BLINK_RED') default 'NONE';

UPDATE system_settings SET db_schema_version='1204',db_schema_update_date=NOW();

ALTER TABLE system_settings ADD queuemetrics_loginout ENUM('STANDARD','CALLBACK') default 'STANDARD';

UPDATE system_settings SET db_schema_version='1205',db_schema_update_date=NOW();

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

ALTER TABLE system_settings ADD callcard_enabled ENUM('1','0') default '0';

ALTER TABLE vicidial_users ADD callcard_admin ENUM('1','0') default '0';

UPDATE system_settings SET db_schema_version='1206',db_schema_update_date=NOW();

ALTER TABLE vicidial_user_groups ADD agent_xfer_consultative ENUM('Y','N') default 'Y';
ALTER TABLE vicidial_user_groups ADD agent_xfer_dial_override ENUM('Y','N') default 'Y';
ALTER TABLE vicidial_user_groups ADD agent_xfer_vm_transfer ENUM('Y','N') default 'Y';
ALTER TABLE vicidial_user_groups ADD agent_xfer_blind_transfer ENUM('Y','N') default 'Y';
ALTER TABLE vicidial_user_groups ADD agent_xfer_dial_with_customer ENUM('Y','N') default 'Y';
ALTER TABLE vicidial_user_groups ADD agent_xfer_park_customer_dial ENUM('Y','N') default 'Y';

ALTER TABLE vicidial_agent_log ADD uniqueid VARCHAR(20) default '';
ALTER TABLE vicidial_agent_log_archive ADD uniqueid VARCHAR(20) default '';

UPDATE system_settings SET db_schema_version='1207',db_schema_update_date=NOW();

ALTER TABLE vicidial_user_groups ADD agent_fullscreen ENUM('Y','N') default 'N';

UPDATE system_settings SET db_schema_version='1208',db_schema_update_date=NOW();

ALTER TABLE vicidial_auto_calls ADD extension VARCHAR(100) default '';

ALTER TABLE vicidial_live_agents ADD ra_user VARCHAR(20) default '';
ALTER TABLE vicidial_live_agents ADD ra_extension VARCHAR(100) default '';

ALTER TABLE vicidial_remote_agents ADD extension_group VARCHAR(20) default 'NONE';
ALTER TABLE vicidial_remote_agents ADD extension_group_order VARCHAR(20) default 'NONE';

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

UPDATE system_settings SET db_schema_version='1209',db_schema_update_date=NOW();

ALTER TABLE vicidial_users ADD agent_choose_blended ENUM('0','1') default '1';

UPDATE system_settings SET db_schema_version='1210',db_schema_update_date=NOW();

ALTER TABLE system_settings ADD queuemetrics_callstatus ENUM('0','1') default '1';

UPDATE system_settings SET db_schema_version='1211',db_schema_update_date=NOW();

ALTER TABLE vicidial_campaigns ADD queuemetrics_callstatus_override ENUM('DISABLED','NO','YES') default 'DISABLED';

UPDATE system_settings SET db_schema_version='1212',db_schema_update_date=NOW();

ALTER TABLE vicidial_campaigns ADD extension_appended_cidname ENUM('Y','N') default 'N';

ALTER TABLE vicidial_inbound_groups ADD extension_appended_cidname ENUM('Y','N') default 'N';

UPDATE system_settings SET db_schema_version='1213',db_schema_update_date=NOW();

ALTER TABLE vicidial_campaigns ADD scheduled_callbacks_count ENUM('LIVE','ALL_ACTIVE') default 'ALL_ACTIVE';

UPDATE system_settings SET db_schema_version='1214',db_schema_update_date=NOW();

ALTER TABLE vicidial_user_log ADD session_id VARCHAR(20);
ALTER TABLE vicidial_user_log ADD server_ip VARCHAR(15);
ALTER TABLE vicidial_user_log ADD extension VARCHAR(50);
ALTER TABLE vicidial_user_log ADD computer_ip VARCHAR(15);
ALTER TABLE vicidial_user_log ADD browser VARCHAR(255);
ALTER TABLE vicidial_user_log ADD data VARCHAR(255);

ALTER TABLE vicidial_campaigns ADD manual_dial_override ENUM('NONE','ALLOW_ALL','DISABLE_ALL') default 'NONE';
ALTER TABLE vicidial_campaigns ADD blind_monitor_warning ENUM('DISABLED','ALERT','NOTICE','AUDIO','ALERT_NOTICE','ALERT_AUDIO','NOTICE_AUDIO','ALL') default 'DISABLED';
ALTER TABLE vicidial_campaigns ADD blind_monitor_message VARCHAR(255) default 'Someone is blind monitoring your session';
ALTER TABLE vicidial_campaigns ADD blind_monitor_filename VARCHAR(100) default '';

ALTER TABLE vicidial_users ADD realtime_block_user_info ENUM('0','1') default '0';

ALTER TABLE vicidial_inbound_groups ADD uniqueid_status_display ENUM('DISABLED','ENABLED','ENABLED_PREFIX') default 'DISABLED';
ALTER TABLE vicidial_inbound_groups ADD uniqueid_status_prefix VARCHAR(50) default '';

CREATE TABLE vicidial_log_extended (
uniqueid VARCHAR(50) PRIMARY KEY NOT NULL,
server_ip VARCHAR(15),
call_date DATETIME,
lead_id INT(9) UNSIGNED,
caller_code VARCHAR(30) NOT NULL,
custom_call_id VARCHAR(100)
);

ALTER TABLE system_settings ADD default_codecs VARCHAR(100) default '';

ALTER TABLE phones ADD codecs_list VARCHAR(100) default '';
ALTER TABLE phones ADD codecs_with_template ENUM('0','1') default '0';

UPDATE system_settings SET db_schema_version='1215',db_schema_update_date=NOW();

ALTER TABLE vicidial_inbound_groups MODIFY uniqueid_status_display ENUM('DISABLED','ENABLED','ENABLED_PREFIX','ENABLED_PRESERVE') default 'DISABLED';

UPDATE system_settings SET db_schema_version='1216',db_schema_update_date=NOW();

ALTER TABLE system_settings ADD custom_fields_enabled ENUM('0','1') default '0';

ALTER TABLE vicidial_users ADD custom_fields_modify ENUM('0','1') default '0';

GRANT ALTER,CREATE on asterisk.* TO custom@'%' IDENTIFIED BY 'custom1234';
GRANT ALTER,CREATE on asterisk.* TO custom@localhost IDENTIFIED BY 'custom1234';

CREATE TABLE vicidial_lists_fields (
field_id INT(9) UNSIGNED AUTO_INCREMENT PRIMARY KEY NOT NULL,
list_id BIGINT(14) UNSIGNED NOT NULL DEFAULT '0',
field_label VARCHAR(50),
field_name VARCHAR(50),
field_description VARCHAR(100),
field_rank SMALLINT(5),
field_help VARCHAR(255),
field_type ENUM('TEXT','AREA','SELECT','MULTI','RADIO','CHECKBOX','DATE','TIME') default 'TEXT',
field_options VARCHAR(5000),
field_size SMALLINT(5),
field_max SMALLINT(5),
field_default VARCHAR(255),
field_cost SMALLINT(5),
field_required ENUM('Y','N') default 'N'
);

CREATE UNIQUE INDEX listfield on vicidial_lists_fields (list_id, field_label);

UPDATE system_settings SET db_schema_version='1217',db_schema_update_date=NOW();

ALTER TABLE vicidial_lists_fields MODIFY field_name VARCHAR(1000);
ALTER TABLE vicidial_lists_fields MODIFY field_help VARCHAR(1000);
ALTER TABLE vicidial_lists_fields ADD name_position ENUM('LEFT','TOP') default 'LEFT';
ALTER TABLE vicidial_lists_fields ADD multi_position ENUM('HORIZONTAL','VERTICAL') default 'HORIZONTAL';

ALTER TABLE vicidial_inbound_groups ADD hold_time_option_minimum SMALLINT(5) default '0';

UPDATE system_settings SET db_schema_version='1218',db_schema_update_date=NOW();

ALTER TABLE vicidial_lists_fields ADD field_order SMALLINT(5) default '1';

UPDATE system_settings SET db_schema_version='1219',db_schema_update_date=NOW();

ALTER TABLE vicidial_lists_fields MODIFY field_type ENUM('TEXT','AREA','SELECT','MULTI','RADIO','CHECKBOX','DATE','TIME','DISPLAY') default 'TEXT';

UPDATE system_settings SET db_schema_version='1220',db_schema_update_date=NOW();

ALTER TABLE vicidial_inbound_groups MODIFY hold_time_option VARCHAR(30) default 'NONE';
ALTER TABLE vicidial_inbound_groups ADD hold_time_option_press_filename VARCHAR(255) default 'to-be-called-back';
ALTER TABLE vicidial_inbound_groups ADD hold_time_option_callmenu VARCHAR(50) default '';

UPDATE system_settings SET db_schema_version='1221',db_schema_update_date=NOW();

ALTER TABLE vicidial_inbound_groups MODIFY hold_time_option_press_filename VARCHAR(255) default 'to-be-called-back|digits/1';

UPDATE vicidial_inbound_groups SET hold_time_option_press_filename='to-be-called-back|digits/1' where hold_time_option_press_filename='to-be-called-back';

UPDATE system_settings SET db_schema_version='1222',db_schema_update_date=NOW();

ALTER TABLE vicidial_campaigns ADD inbound_queue_no_dial ENUM('DISABLED','ENABLED','ALL_SERVERS') default 'DISABLED';

ALTER TABLE vicidial_call_times ADD default_afterhours_filename_override VARCHAR(255) default '';
ALTER TABLE vicidial_call_times ADD sunday_afterhours_filename_override VARCHAR(255) default '';
ALTER TABLE vicidial_call_times ADD monday_afterhours_filename_override VARCHAR(255) default '';
ALTER TABLE vicidial_call_times ADD tuesday_afterhours_filename_override VARCHAR(255) default '';
ALTER TABLE vicidial_call_times ADD wednesday_afterhours_filename_override VARCHAR(255) default '';
ALTER TABLE vicidial_call_times ADD thursday_afterhours_filename_override VARCHAR(255) default '';
ALTER TABLE vicidial_call_times ADD friday_afterhours_filename_override VARCHAR(255) default '';
ALTER TABLE vicidial_call_times ADD saturday_afterhours_filename_override VARCHAR(255) default '';

UPDATE system_settings SET db_schema_version='1223',db_schema_update_date=NOW();

ALTER table vicidial_inbound_groups ADD hold_time_option_no_block ENUM('N','Y') default 'N';
ALTER table vicidial_inbound_groups ADD hold_time_option_prompt_seconds SMALLINT(5) default '10';
ALTER table vicidial_inbound_groups ADD onhold_prompt_no_block ENUM('N','Y') default 'N';
ALTER table vicidial_inbound_groups ADD onhold_prompt_seconds SMALLINT(5) default '10';

UPDATE system_settings SET db_schema_version='1224',db_schema_update_date=NOW();

ALTER TABLE vicidial_live_agents ADD external_dtmf VARCHAR(100) default '';
ALTER TABLE vicidial_live_agents ADD external_transferconf VARCHAR(100) default '';
ALTER TABLE vicidial_live_agents ADD external_park VARCHAR(40) default '';

UPDATE system_settings SET db_schema_version='1225',db_schema_update_date=NOW();

ALTER TABLE vicidial_call_menu_options MODIFY option_route_value_context VARCHAR(1000);

UPDATE system_settings SET db_schema_version='1226',db_schema_update_date=NOW();

ALTER TABLE system_settings ADD admin_web_directory VARCHAR(255) default 'vicidial';

UPDATE system_settings SET db_schema_version='1227',db_schema_update_date=NOW();

ALTER TABLE vicidial_tts_prompts ADD tts_voice VARCHAR(100) default 'Allison-8kHz';

UPDATE system_settings SET db_schema_version='1228',db_schema_update_date=NOW();

ALTER TABLE system_settings ADD label_title VARCHAR(40) default '';
ALTER TABLE system_settings ADD label_first_name VARCHAR(40) default '';
ALTER TABLE system_settings ADD label_middle_initial VARCHAR(40) default '';
ALTER TABLE system_settings ADD label_last_name VARCHAR(40) default '';
ALTER TABLE system_settings ADD label_address1 VARCHAR(40) default '';
ALTER TABLE system_settings ADD label_address2 VARCHAR(40) default '';
ALTER TABLE system_settings ADD label_address3 VARCHAR(40) default '';
ALTER TABLE system_settings ADD label_city VARCHAR(40) default '';
ALTER TABLE system_settings ADD label_state VARCHAR(40) default '';
ALTER TABLE system_settings ADD label_province VARCHAR(40) default '';
ALTER TABLE system_settings ADD label_postal_code VARCHAR(40) default '';
ALTER TABLE system_settings ADD label_vendor_lead_code VARCHAR(40) default '';
ALTER TABLE system_settings ADD label_gender VARCHAR(40) default '';
ALTER TABLE system_settings ADD label_phone_number VARCHAR(40) default '';
ALTER TABLE system_settings ADD label_phone_code VARCHAR(40) default '';
ALTER TABLE system_settings ADD label_alt_phone VARCHAR(40) default '';
ALTER TABLE system_settings ADD label_security_phrase VARCHAR(40) default '';
ALTER TABLE system_settings ADD label_email VARCHAR(40) default '';
ALTER TABLE system_settings ADD label_comments VARCHAR(40) default '';

UPDATE system_settings SET db_schema_version='1229',db_schema_update_date=NOW();

ALTER TABLE vicidial_lists_fields MODIFY field_type ENUM('TEXT','AREA','SELECT','MULTI','RADIO','CHECKBOX','DATE','TIME','DISPLAY','SCRIPT') default 'TEXT';

UPDATE system_settings SET db_schema_version='1230',db_schema_update_date=NOW();

ALTER TABLE vicidial_campaigns MODIFY get_call_launch ENUM('NONE','SCRIPT','WEBFORM','WEBFORMTWO','FORM') default 'NONE';

ALTER TABLE vicidial_inbound_groups MODIFY get_call_launch ENUM('NONE','SCRIPT','WEBFORM','WEBFORMTWO','FORM') default 'NONE';

UPDATE system_settings SET db_schema_version='1231',db_schema_update_date=NOW();

ALTER TABLE vicidial_auto_calls ENGINE=MEMORY;

UPDATE system_settings SET db_schema_version='1232',db_schema_update_date=NOW();

ALTER TABLE vicidial_hopper ENGINE=MEMORY;

UPDATE system_settings SET db_schema_version='1233',db_schema_update_date=NOW();

ALTER TABLE system_settings ADD slave_db_server VARCHAR(50) default '';
ALTER TABLE system_settings ADD reports_use_slave_db VARCHAR(2000) default '';

ALTER TABLE vicidial_list ADD entry_list_id BIGINT(14) UNSIGNED NOT NULL DEFAULT '0';

UPDATE system_settings SET db_schema_version='1234',db_schema_update_date=NOW();

ALTER TABLE vicidial_inbound_groups ADD hold_time_second_option VARCHAR(30) default 'NONE';
ALTER TABLE vicidial_inbound_groups ADD hold_time_third_option VARCHAR(30) default 'NONE';
ALTER TABLE vicidial_inbound_groups ADD wait_hold_option_priority ENUM('WAIT','HOLD','BOTH') default 'WAIT';
ALTER TABLE vicidial_inbound_groups ADD wait_time_option VARCHAR(30) default 'NONE';
ALTER TABLE vicidial_inbound_groups ADD wait_time_second_option VARCHAR(30) default 'NONE';
ALTER TABLE vicidial_inbound_groups ADD wait_time_third_option VARCHAR(30) default 'NONE';
ALTER TABLE vicidial_inbound_groups ADD wait_time_option_seconds SMALLINT(5) default '120';
ALTER TABLE vicidial_inbound_groups ADD wait_time_option_exten VARCHAR(20) default '8300';
ALTER TABLE vicidial_inbound_groups ADD wait_time_option_voicemail VARCHAR(20) default '';
ALTER TABLE vicidial_inbound_groups ADD wait_time_option_xfer_group VARCHAR(20) default '---NONE---';
ALTER TABLE vicidial_inbound_groups ADD wait_time_option_callmenu VARCHAR(50) default '';
ALTER TABLE vicidial_inbound_groups ADD wait_time_option_callback_filename VARCHAR(255) default 'vm-hangup';
ALTER TABLE vicidial_inbound_groups ADD wait_time_option_callback_list_id BIGINT(14) UNSIGNED default '999';
ALTER TABLE vicidial_inbound_groups ADD wait_time_option_press_filename VARCHAR(255) default 'to-be-called-back|digits/1';
ALTER TABLE vicidial_inbound_groups ADD wait_time_option_no_block ENUM('N','Y') default 'N';
ALTER TABLE vicidial_inbound_groups ADD wait_time_option_prompt_seconds SMALLINT(5) default '10';

UPDATE system_settings SET db_schema_version='1235',db_schema_update_date=NOW();

ALTER TABLE vicidial_campaigns MODIFY quick_transfer_button VARCHAR(20) default 'N';

UPDATE system_settings SET db_schema_version='1236',db_schema_update_date=NOW();

ALTER TABLE vicidial_campaigns MODIFY timer_action ENUM('NONE','WEBFORM','WEBFORM2','D1_DIAL','D2_DIAL','D3_DIAL','D4_DIAL','D5_DIAL','MESSAGE_ONLY','HANGUP','CALLMENU','EXTENSION','IN_GROUP') default 'NONE';
ALTER TABLE vicidial_campaigns ADD timer_action_destination VARCHAR(30) default '';

ALTER TABLE vicidial_inbound_groups MODIFY timer_action ENUM('NONE','WEBFORM','WEBFORM2','D1_DIAL','D2_DIAL','D3_DIAL','D4_DIAL','D5_DIAL','MESSAGE_ONLY','HANGUP','CALLMENU','EXTENSION','IN_GROUP') default 'NONE';
ALTER TABLE vicidial_inbound_groups ADD timer_action_destination VARCHAR(30) default '';

ALTER TABLE vicidial_live_agents ADD external_timer_action_destination VARCHAR(100) default '';

UPDATE system_settings SET db_schema_version='1237',db_schema_update_date=NOW();

ALTER TABLE vicidial_user_groups ADD allowed_reports VARCHAR(2000) default 'ALL REPORTS';

UPDATE system_settings SET db_schema_version='1238',db_schema_update_date=NOW();

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

ALTER TABLE vicidial_inbound_dids ADD filter_inbound_number ENUM('DISABLED','GROUP','URL') default 'DISABLED';
ALTER TABLE vicidial_inbound_dids ADD filter_phone_group_id VARCHAR(20) default '';
ALTER TABLE vicidial_inbound_dids ADD filter_url VARCHAR(1000) default '';
ALTER TABLE vicidial_inbound_dids ADD filter_action ENUM('EXTEN','VOICEMAIL','AGENT','PHONE','IN_GROUP','CALLMENU') default 'EXTEN';
ALTER TABLE vicidial_inbound_dids ADD filter_extension VARCHAR(50) default '9998811112';
ALTER TABLE vicidial_inbound_dids ADD filter_exten_context VARCHAR(50) default 'default';
ALTER TABLE vicidial_inbound_dids ADD filter_voicemail_ext VARCHAR(10);
ALTER TABLE vicidial_inbound_dids ADD filter_phone VARCHAR(100);
ALTER TABLE vicidial_inbound_dids ADD filter_server_ip VARCHAR(15);
ALTER TABLE vicidial_inbound_dids ADD filter_user VARCHAR(20);
ALTER TABLE vicidial_inbound_dids ADD filter_user_unavailable_action ENUM('IN_GROUP','EXTEN','VOICEMAIL','PHONE') default 'VOICEMAIL';
ALTER TABLE vicidial_inbound_dids ADD filter_user_route_settings_ingroup VARCHAR(20) default 'AGENTDIRECT';
ALTER TABLE vicidial_inbound_dids ADD filter_group_id VARCHAR(20);
ALTER TABLE vicidial_inbound_dids ADD filter_call_handle_method VARCHAR(20) default 'CID';
ALTER TABLE vicidial_inbound_dids ADD filter_agent_search_method ENUM('LO','LB','SO') default 'LB';
ALTER TABLE vicidial_inbound_dids ADD filter_list_id BIGINT(14) UNSIGNED default '999';
ALTER TABLE vicidial_inbound_dids ADD filter_campaign_id VARCHAR(8);
ALTER TABLE vicidial_inbound_dids ADD filter_phone_code VARCHAR(10) default '1';
ALTER TABLE vicidial_inbound_dids ADD filter_menu_id VARCHAR(50) default '';
ALTER TABLE vicidial_inbound_dids ADD filter_clean_cid_number VARCHAR(20) default '';

UPDATE system_settings SET db_schema_version='1239',db_schema_update_date=NOW();

ALTER TABLE vicidial_user_groups ADD webphone_url_override VARCHAR(255) default '';

ALTER TABLE vicidial_inbound_groups ADD calculate_estimated_hold_seconds SMALLINT(5) UNSIGNED default '0';

UPDATE system_settings SET db_schema_version='1240',db_schema_update_date=NOW();

ALTER TABLE vicidial_campaigns ADD enable_xfer_presets ENUM('DISABLED','ENABLED') default 'DISABLED';
ALTER TABLE vicidial_campaigns ADD hide_xfer_number_to_dial ENUM('DISABLED','ENABLED') default 'DISABLED';

CREATE TABLE vicidial_xfer_presets (
campaign_id VARCHAR(20) NOT NULL,
preset_name VARCHAR(40) NOT NULL,
preset_number VARCHAR(50) NOT NULL,
preset_dtmf VARCHAR(50) default '',
preset_hide_number ENUM('Y','N') default 'N',
index (preset_name)
);

ALTER TABLE user_call_log ADD preset_name VARCHAR(40) default '';
ALTER TABLE user_call_log ADD campaign_id VARCHAR(20) default '';

CREATE TABLE vicidial_xfer_stats (
campaign_id VARCHAR(20) NOT NULL,
preset_name VARCHAR(40) NOT NULL,
xfer_count SMALLINT(5) UNSIGNED default '0',
index (campaign_id)
);

UPDATE system_settings SET db_schema_version='1241',db_schema_update_date=NOW();

ALTER TABLE vicidial_campaigns ADD manual_dial_prefix VARCHAR(20) default '';

UPDATE system_settings SET db_schema_version='1242',db_schema_update_date=NOW();

ALTER TABLE system_settings ADD webphone_systemkey VARCHAR(100) default '';

ALTER TABLE phones ADD webphone_dialpad ENUM('Y','N','TOGGLE') default 'Y';

ALTER TABLE vicidial_user_groups ADD webphone_systemkey_override VARCHAR(100) default '';
ALTER TABLE vicidial_user_groups ADD webphone_dialpad_override ENUM('DISABLED','Y','N','TOGGLE') default 'DISABLED';

UPDATE system_settings SET db_schema_version='1243',db_schema_update_date=NOW();

ALTER TABLE vicidial_users ADD force_change_password ENUM('Y','N') default 'N';

ALTER TABLE system_settings ADD first_login_trigger ENUM('Y','N') default 'N';
ALTER TABLE system_settings ADD hosted_settings VARCHAR(100) default '';
ALTER TABLE system_settings ADD default_phone_registration_password VARCHAR(20) default 'test';
ALTER TABLE system_settings ADD default_phone_login_password VARCHAR(20) default 'test';
ALTER TABLE system_settings ADD default_server_password VARCHAR(20) default 'test';

UPDATE system_settings SET db_schema_version='1244',db_schema_update_date=NOW();

ALTER TABLE vicidial_lists_fields MODIFY field_name VARCHAR(5000);

UPDATE system_settings SET db_schema_version='1245',db_schema_update_date=NOW();

ALTER TABLE vicidial_campaigns ADD customer_3way_hangup_logging ENUM('DISABLED','ENABLED') default 'ENABLED';
ALTER TABLE vicidial_campaigns ADD customer_3way_hangup_seconds SMALLINT(5) UNSIGNED default '5';
ALTER TABLE vicidial_campaigns ADD customer_3way_hangup_action ENUM('NONE','DISPO') default 'NONE';

ALTER TABLE user_call_log ADD customer_hungup ENUM('BEFORE_CALL','DURING_CALL','') default '';
ALTER TABLE user_call_log ADD customer_hungup_seconds SMALLINT(5) UNSIGNED default '0';

UPDATE system_settings SET db_schema_version='1246',db_schema_update_date=NOW();

ALTER TABLE vicidial_inbound_groups ADD add_lead_url TEXT;

UPDATE system_settings SET db_schema_version='1247',db_schema_update_date=NOW();

ALTER TABLE vicidial_campaigns MODIFY park_file_name VARCHAR(100) default 'default';
ALTER TABLE vicidial_campaigns ADD ivr_park_call ENUM('DISABLED','ENABLED','ENABLED_PARK_ONLY','ENABLED_BUTTON_HIDDEN') default 'DISABLED';
ALTER TABLE vicidial_campaigns ADD ivr_park_call_agi TEXT;

UPDATE system_settings SET db_schema_version='1248',db_schema_update_date=NOW();

ALTER TABLE vicidial_campaigns ADD manual_preview_dial ENUM('DISABLED','PREVIEW_AND_SKIP','PREVIEW_ONLY') default 'PREVIEW_AND_SKIP';

ALTER TABLE vicidial_inbound_groups ADD eht_minimum_prompt_filename VARCHAR(255) default '';
ALTER TABLE vicidial_inbound_groups ADD eht_minimum_prompt_no_block ENUM('N','Y') default 'N';
ALTER TABLE vicidial_inbound_groups ADD eht_minimum_prompt_seconds SMALLINT(5) default '10';

UPDATE system_settings SET db_schema_version='1249',db_schema_update_date=NOW();

ALTER TABLE vicidial_campaigns ADD realtime_agent_time_stats ENUM('DISABLED','WAIT_CUST_ACW','WAIT_CUST_ACW_PAUSE','CALLS_WAIT_CUST_ACW_PAUSE') default 'CALLS_WAIT_CUST_ACW_PAUSE';

ALTER TABLE vicidial_campaign_stats ADD agent_calls_today INT(9) UNSIGNED default '0';
ALTER TABLE vicidial_campaign_stats ADD agent_wait_today BIGINT(14) UNSIGNED default '0';
ALTER TABLE vicidial_campaign_stats ADD agent_custtalk_today BIGINT(14) UNSIGNED default '0';
ALTER TABLE vicidial_campaign_stats ADD agent_acw_today BIGINT(14) UNSIGNED default '0';
ALTER TABLE vicidial_campaign_stats ADD agent_pause_today BIGINT(14) UNSIGNED default '0';

ALTER TABLE park_log MODIFY uniqueid VARCHAR(20) default '';
ALTER TABLE park_log ADD lead_id INT(9) UNSIGNED default '0';
ALTER TABLE park_log DROP PRIMARY KEY;
ALTER TABLE park_log DROP KEY uniqueid;
CREATE INDEX lead_id_park on park_log (lead_id);
CREATE INDEX uniqueid_park on park_log (uniqueid);

UPDATE system_settings SET db_schema_version='1250',db_schema_update_date=NOW();

ALTER TABLE system_settings ADD admin_modify_refresh SMALLINT(5) UNSIGNED default '0';
ALTER TABLE system_settings ADD nocache_admin ENUM('0','1') default '1';
ALTER TABLE system_settings ADD generate_cross_server_exten ENUM('0','1') default '0';
ALTER TABLE system_settings ADD queuemetrics_addmember_enabled ENUM('0','1') default '0';

UPDATE system_settings SET db_schema_version='1251',db_schema_update_date=NOW();

ALTER TABLE vicidial_campaigns ADD use_auto_hopper ENUM('Y','N') default 'N';
ALTER TABLE vicidial_campaigns ADD auto_hopper_multi VARCHAR(6) default '1';
ALTER TABLE vicidial_campaigns ADD auto_hopper_level MEDIUMINT(8) UNSIGNED default '0';
ALTER TABLE vicidial_campaigns ADD auto_trim_hopper ENUM('Y','N') default 'N';

ALTER TABLE vicidial_hopper ADD source VARCHAR(1) default '';

UPDATE system_settings SET db_schema_version='1252',db_schema_update_date=NOW();

ALTER TABLE vicidial_campaigns MODIFY concurrent_transfers ENUM('AUTO','1','2','3','4','5','6','7','8','9','10','15','20','25','30','40','50','60','80','100') default 'AUTO';

UPDATE system_settings SET db_schema_version='1253',db_schema_update_date=NOW();

CREATE TABLE vicidial_manual_dial_queue (
mdq_id INT(9) UNSIGNED AUTO_INCREMENT PRIMARY KEY NOT NULL,
user VARCHAR(20),
phone_number VARCHAR(100) default '',
entry_time DATETIME,
status ENUM('READY','QUEUE') default 'READY',
external_dial VARCHAR(100) default '',
index (user)
);

ALTER TABLE vicidial_campaigns ADD api_manual_dial ENUM('STANDARD','QUEUE','QUEUE_AND_AUTOCALL') default 'STANDARD';
ALTER TABLE vicidial_campaigns ADD manual_dial_call_time_check ENUM('DISABLED','ENABLED') default 'DISABLED';

UPDATE system_settings SET db_schema_version='1254',db_schema_update_date=NOW();

ALTER TABLE vicidial_lists ADD web_form_address TEXT;
ALTER TABLE vicidial_lists ADD web_form_address_two TEXT;

UPDATE system_settings SET db_schema_version='1255',db_schema_update_date=NOW();

ALTER TABLE vicidial_campaigns ADD display_leads_count ENUM('Y','N') default 'N';

UPDATE system_settings SET db_schema_version='1256',db_schema_update_date=NOW();

ALTER TABLE phones MODIFY webphone_dialpad ENUM('Y','N','TOGGLE','TOGGLE_OFF') default 'Y';
ALTER TABLE vicidial_user_groups MODIFY webphone_dialpad_override ENUM('DISABLED','Y','N','TOGGLE','TOGGLE_OFF') default 'DISABLED';

UPDATE system_settings SET db_schema_version='1257',db_schema_update_date=NOW();

ALTER TABLE system_settings MODIFY queuemetrics_loginout ENUM('STANDARD','CALLBACK','NONE') default 'STANDARD';
ALTER TABLE system_settings ADD queuemetrics_dispo_pause VARCHAR(6) default '';

ALTER TABLE vicidial_campaigns ADD lead_order_randomize ENUM('Y','N') default 'N';

UPDATE system_settings SET db_schema_version='1258',db_schema_update_date=NOW();

CREATE TABLE vicidial_call_notes_archive LIKE vicidial_call_notes; 
ALTER TABLE vicidial_call_notes_archive MODIFY notesid INT(9) UNSIGNED NOT NULL;

ALTER TABLE vicidial_statuses ADD scheduled_callback ENUM('Y','N') default 'N';
ALTER TABLE vicidial_campaign_statuses ADD scheduled_callback ENUM('Y','N') default 'N';
UPDATE vicidial_statuses SET scheduled_callback='Y' where status='CALLBK';
UPDATE vicidial_campaign_statuses SET scheduled_callback='Y' where status='CALLBK';

ALTER TABLE vicidial_callbacks ADD lead_status VARCHAR(6) default 'CALLBK';

UPDATE system_settings SET db_schema_version='1259',db_schema_update_date=NOW();

ALTER TABLE vicidial_campaigns ADD lead_order_secondary ENUM('LEAD_ASCEND','LEAD_DESCEND','CALLTIME_ASCEND','CALLTIME_DESCEND') default 'LEAD_ASCEND';
ALTER TABLE vicidial_campaigns ADD per_call_notes ENUM('ENABLED','DISABLED') default 'DISABLED';
ALTER TABLE vicidial_campaigns ADD my_callback_option ENUM('CHECKED','UNCHECKED') default 'UNCHECKED';

UPDATE system_settings SET db_schema_version='1260',db_schema_update_date=NOW();

ALTER TABLE vicidial_campaigns ADD agent_lead_search ENUM('ENABLED','DISABLED') default 'DISABLED';
ALTER TABLE vicidial_campaigns ADD agent_lead_search_method ENUM('SYSTEM','CAMPAIGNLISTS','CAMPLISTS_ALL','LIST') default 'CAMPLISTS_ALL';

ALTER TABLE vicidial_users ADD agent_lead_search_override ENUM('NOT_ACTIVE','ENABLED','DISABLED') default 'NOT_ACTIVE';

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

CREATE TABLE vicidial_lead_search_log_archive LIKE vicidial_lead_search_log; 
ALTER TABLE vicidial_lead_search_log_archive MODIFY search_log_id INT(9) UNSIGNED NOT NULL;

UPDATE system_settings SET db_schema_version='1261',db_schema_update_date=NOW();

ALTER TABLE vicidial_campaigns MODIFY agent_lead_search_method VARCHAR(30) default 'CAMPLISTS_ALL';

UPDATE system_settings SET db_schema_version='1262',db_schema_update_date=NOW();

ALTER TABLE vicidial_campaigns ADD queuemetrics_phone_environment VARCHAR(20) default '';

ALTER TABLE servers ADD active_twin_server_ip VARCHAR(15) default '';

UPDATE system_settings SET db_schema_version='1263',db_schema_update_date=NOW();

ALTER TABLE vicidial_inbound_groups ADD on_hook_ring_time SMALLINT(5) default '15';
ALTER TABLE vicidial_inbound_groups MODIFY next_agent_call ENUM('random','oldest_call_start','oldest_call_finish','overall_user_level','inbound_group_rank','campaign_rank','fewest_calls','fewest_calls_campaign','longest_wait_time','ring_all') default 'longest_wait_time';

ALTER TABLE vicidial_live_agents ADD on_hook_agent ENUM('Y','N') default 'N';
ALTER TABLE vicidial_live_agents ADD on_hook_ring_time SMALLINT(5) default '15';
ALTER TABLE vicidial_live_agents ADD ring_callerid VARCHAR(20) default '';

ALTER TABLE phones ADD on_hook_agent ENUM('Y','N') default 'N';

ALTER TABLE vicidial_remote_agents ADD on_hook_agent ENUM('Y','N') default 'N';
ALTER TABLE vicidial_remote_agents ADD on_hook_ring_time SMALLINT(5) default '15';

ALTER TABLE vicidial_auto_calls ADD agent_grab_extension VARCHAR(100) default '';

UPDATE system_settings SET db_schema_version='1264',db_schema_update_date=NOW();

ALTER TABLE vicidial_campaigns MODIFY scheduled_callbacks_alert ENUM('NONE','BLINK','RED','BLINK_RED','BLINK_DEFER','RED_DEFER','BLINK_RED_DEFER') default 'NONE';

UPDATE system_settings SET db_schema_version='1265',db_schema_update_date=NOW();

ALTER TABLE vicidial_campaigns ADD auto_pause_precall ENUM('Y','N') default 'N';
ALTER TABLE vicidial_campaigns ADD auto_pause_precall_code VARCHAR(6) default 'PRECAL';
ALTER TABLE vicidial_campaigns ADD auto_resume_precall ENUM('Y','N') default 'N';

UPDATE system_settings SET db_schema_version='1266',db_schema_update_date=NOW();

ALTER TABLE vicidial_postal_codes MODIFY GMT_offset VARCHAR(6) default '';

ALTER TABLE vicidial_phone_codes MODIFY GMT_offset VARCHAR(6) default '';

UPDATE system_settings SET db_schema_version='1267',db_schema_update_date=NOW();

ALTER TABLE vicidial_campaigns MODIFY auto_alt_dial ENUM('NONE','ALT_ONLY','ADDR3_ONLY','ALT_AND_ADDR3','ALT_AND_EXTENDED','ALT_AND_ADDR3_AND_EXTENDED','EXTENDED_ONLY','MULTI_LEAD') default 'NONE';

UPDATE system_settings SET db_schema_version='1268',db_schema_update_date=NOW();

ALTER TABLE vicidial_api_log ADD run_time VARCHAR(20) default '0';

UPDATE system_settings SET db_schema_version='1269',db_schema_update_date=NOW();

ALTER TABLE vicidial_phone_codes ADD tz_code VARCHAR(4) default '';

UPDATE system_settings SET db_schema_version='1270',db_schema_update_date=NOW();

ALTER TABLE vicidial_campaigns ADD manual_dial_cid ENUM('CAMPAIGN','AGENT_PHONE') default 'CAMPAIGN';

UPDATE system_settings SET db_schema_version='1271',db_schema_update_date=NOW();

CREATE TABLE vicidial_closer_log_archive LIKE vicidial_closer_log; 
ALTER TABLE vicidial_closer_log_archive MODIFY closecallid INT(9) UNSIGNED NOT NULL;

UPDATE system_settings SET db_schema_version='1272',db_schema_update_date=NOW();

ALTER TABLE vicidial_campaigns ADD post_phone_time_diff_alert VARCHAR(30) default 'DISABLED';

UPDATE system_settings SET db_schema_version='1273',db_schema_update_date=NOW();

ALTER TABLE vicidial_campaigns ADD custom_3way_button_transfer VARCHAR(30) default 'DISABLED';

UPDATE system_settings SET db_schema_version='1274',db_schema_update_date=NOW();

ALTER TABLE vicidial_campaigns ADD available_only_tally_threshold ENUM('DISABLED','LOGGED-IN_AGENTS','NON-PAUSED_AGENTS','WAITING_AGENTS') default 'DISABLED';
ALTER TABLE vicidial_campaigns ADD available_only_tally_threshold_agents SMALLINT(5) UNSIGNED default '0';
ALTER TABLE vicidial_campaigns ADD dial_level_threshold ENUM('DISABLED','LOGGED-IN_AGENTS','NON-PAUSED_AGENTS','WAITING_AGENTS') default 'DISABLED';
ALTER TABLE vicidial_campaigns ADD dial_level_threshold_agents SMALLINT(5) UNSIGNED default '0';

ALTER TABLE vicidial_lists ADD time_zone_setting ENUM('COUNTRY_AND_AREA_CODE','POSTAL_CODE','NANPA_PREFIX','OWNER_TIME_ZONE_CODE') default 'COUNTRY_AND_AREA_CODE';

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

UPDATE system_settings SET db_schema_version='1275',db_schema_update_date=NOW();

ALTER TABLE vicidial_campaigns MODIFY drop_action ENUM('HANGUP','MESSAGE','VOICEMAIL','IN_GROUP','AUDIO','CALLMENU') default 'AUDIO';
ALTER TABLE vicidial_campaigns ADD safe_harbor_audio VARCHAR(100) default 'buzz';
ALTER TABLE vicidial_campaigns ADD safe_harbor_menu_id VARCHAR(50) default '';

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

CREATE TABLE vicidial_outbound_ivr_log_archive LIKE vicidial_outbound_ivr_log;

ALTER TABLE vicidial_call_menu ADD dtmf_log ENUM('0','1') default '0';

UPDATE system_settings SET db_schema_version='1276',db_schema_update_date=NOW() where db_schema_version < 1276;

ALTER TABLE phones ADD webphone_auto_answer ENUM('Y','N') default 'Y';

UPDATE system_settings SET db_schema_version='1277',db_schema_update_date=NOW() where db_schema_version < 1277;

ALTER TABLE vicidial_campaigns MODIFY survey_method ENUM('AGENT_XFER','VOICEMAIL','EXTENSION','HANGUP','CAMPREC_60_WAV','CALLMENU') default 'AGENT_XFER';
ALTER TABLE vicidial_campaigns ADD survey_menu_id VARCHAR(50) default '';

UPDATE system_settings SET db_schema_version='1278',db_schema_update_date=NOW() where db_schema_version < 1278;

ALTER TABLE vicidial_campaigns ADD callback_days_limit SMALLINT(3) default '0';

UPDATE system_settings SET db_schema_version='1279',db_schema_update_date=NOW() where db_schema_version < 1279;

ALTER TABLE vicidial_campaigns ADD dl_diff_target_method ENUM('ADAPT_CALC_ONLY','CALLS_PLACED') default 'ADAPT_CALC_ONLY';

UPDATE system_settings SET db_schema_version='1280',db_schema_update_date=NOW() where db_schema_version < 1280;

ALTER TABLE vicidial_campaigns ADD disable_dispo_screen ENUM('DISPO_ENABLED','DISPO_DISABLED') default 'DISPO_ENABLED';
ALTER TABLE vicidial_campaigns ADD disable_dispo_status VARCHAR(6) default '';

UPDATE system_settings SET db_schema_version='1281',db_schema_update_date=NOW() where db_schema_version < 1281;

ALTER TABLE vicidial_campaigns ADD screen_labels VARCHAR(20) default '--SYSTEM-SETTINGS--';
ALTER TABLE vicidial_campaigns ADD status_display_fields VARCHAR(30) default 'CALLID';

ALTER TABLE system_settings ADD label_hide_field_logs VARCHAR(6) default 'Y';

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

UPDATE system_settings SET db_schema_version='1282',db_schema_update_date=NOW() where db_schema_version < 1282;

ALTER TABLE system_settings ADD queuemetrics_pe_phone_append ENUM('0','1') default '0';

UPDATE system_settings SET db_schema_version='1283',db_schema_update_date=NOW() where db_schema_version < 1283;

ALTER TABLE vicidial_lists_fields MODIFY field_type ENUM('TEXT','AREA','SELECT','MULTI','RADIO','CHECKBOX','DATE','TIME','DISPLAY','SCRIPT','HIDDEN','READONLY') default 'TEXT';

UPDATE system_settings SET db_schema_version='1284',db_schema_update_date=NOW() where db_schema_version < 1284;

ALTER TABLE system_settings ADD test_campaign_calls ENUM('0','1') default '0';
ALTER TABLE system_settings ADD agents_calls_reset ENUM('0','1') default '1';

UPDATE system_settings SET db_schema_version='1285',db_schema_update_date=NOW() where db_schema_version < 1285;

ALTER TABLE vicidial_live_agents ADD last_inbound_call_time DATETIME;
ALTER TABLE vicidial_live_agents ADD last_inbound_call_finish DATETIME;

ALTER TABLE vicidial_inbound_groups MODIFY next_agent_call VARCHAR(30) default 'longest_wait_time';

UPDATE system_settings SET db_schema_version='1286',db_schema_update_date=NOW() where db_schema_version < 1286;

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

UPDATE system_settings SET db_schema_version='1287',db_schema_update_date=NOW() where db_schema_version < 1287;

ALTER TABLE vicidial_lists_fields MODIFY field_type ENUM('TEXT','AREA','SELECT','MULTI','RADIO','CHECKBOX','DATE','TIME','DISPLAY','SCRIPT','HIDDEN','READONLY','HIDEBLOB') default 'TEXT';

UPDATE system_settings SET db_schema_version='1288',db_schema_update_date=NOW() where db_schema_version < 1288;

ALTER TABLE vicidial_voicemail ADD voicemail_timezone VARCHAR(30) default 'eastern';
ALTER TABLE vicidial_voicemail ADD voicemail_options VARCHAR(255) default '';

ALTER TABLE phones ADD voicemail_timezone VARCHAR(30) default 'eastern';
ALTER TABLE phones ADD voicemail_options VARCHAR(255) default '';

ALTER TABLE system_settings ADD voicemail_timezones TEXT;
ALTER TABLE system_settings ADD default_voicemail_timezone VARCHAR(30) default 'eastern';
ALTER TABLE system_settings ADD default_local_gmt VARCHAR(6) default '-5.00';

UPDATE system_settings SET db_schema_version='1289',db_schema_update_date=NOW() where db_schema_version < 1289;

ALTER TABLE vicidial_campaigns MODIFY disable_dispo_screen ENUM('DISPO_ENABLED','DISPO_DISABLED','DISPO_SELECT_DISABLED') default 'DISPO_ENABLED';

UPDATE system_settings SET db_schema_version='1290',db_schema_update_date=NOW() where db_schema_version < 1290;

ALTER TABLE vicidial_log_extended ADD start_url_processed ENUM('N','Y','U') default 'N';
ALTER TABLE vicidial_log_extended ADD dispo_url_processed ENUM('N','Y','U','XY','XU') default 'N';
ALTER TABLE vicidial_log_extended ADD multi_alt_processed ENUM('N','Y','U') default 'N';
CREATE INDEX call_date on vicidial_log_extended (call_date);

ALTER TABLE vicidial_campaigns ADD na_call_url TEXT;

ALTER TABLE vicidial_inbound_groups ADD na_call_url TEXT;
ALTER TABLE vicidial_inbound_groups ADD on_hook_cid VARCHAR(30) default 'GENERIC';
ALTER TABLE vicidial_inbound_groups ADD group_calldate DATETIME;

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

CREATE TABLE vicidial_log_extended_archive LIKE vicidial_log_extended;
CREATE UNIQUE INDEX vlea on vicidial_log_extended_archive (uniqueid,call_date,lead_id);

UPDATE system_settings SET db_schema_version='1291',db_schema_update_date=NOW() where db_schema_version < 1291;

CREATE UNIQUE INDEX vicidial_campaign_statuses_key on vicidial_campaign_statuses(status, campaign_id);

ALTER TABLE system_settings ADD noanswer_log ENUM('Y','N') default 'N';
ALTER TABLE system_settings ADD alt_log_server_ip VARCHAR(50) default '';
ALTER TABLE system_settings ADD alt_log_dbname VARCHAR(50) default '';
ALTER TABLE system_settings ADD alt_log_login VARCHAR(50) default '';
ALTER TABLE system_settings ADD alt_log_pass VARCHAR(50) default '';
ALTER TABLE system_settings ADD tables_use_alt_log_db VARCHAR(2000) default '';

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

CREATE TABLE vicidial_log_noanswer_archive LIKE vicidial_log_noanswer; 

ALTER TABLE vicidial_log_extended ADD noanswer_processed ENUM('N','Y','U') default 'N';

UPDATE system_settings SET db_schema_version='1292',db_schema_update_date=NOW() where db_schema_version < 1292;

ALTER TABLE vicidial_log_extended_archive ADD noanswer_processed ENUM('N','Y','U') default 'N';

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

CREATE TABLE vicidial_did_agent_log_archive LIKE vicidial_did_agent_log; 
CREATE UNIQUE INDEX vdala on vicidial_did_agent_log_archive (uniqueid,call_date,did_route);

ALTER TABLE system_settings ADD did_agent_log ENUM('Y','N') default 'N';

UPDATE system_settings SET db_schema_version='1293',db_schema_update_date=NOW() where db_schema_version < 1293;

ALTER TABLE vicidial_campaigns ADD survey_recording ENUM('Y','N') default 'N';

UPDATE system_settings SET db_schema_version='1294',db_schema_update_date=NOW() where db_schema_version < 1294;

ALTER TABLE system_settings ADD campaign_cid_areacodes_enabled ENUM('0','1') default '1';

ALTER TABLE vicidial_campaigns MODIFY use_custom_cid ENUM('Y','N','AREACODE') default 'N';

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

UPDATE system_settings SET db_schema_version='1295',db_schema_update_date=NOW() where db_schema_version < 1295;

ALTER TABLE system_settings ADD pllb_grouping_limit SMALLINT(5) default '100';

ALTER TABLE vicidial_campaigns ADD pllb_grouping ENUM('DISABLED','ONE_SERVER_ONLY','CASCADING') default 'DISABLED';
ALTER TABLE vicidial_campaigns ADD pllb_grouping_limit SMALLINT(5) default '50';

UPDATE system_settings SET db_schema_version='1296',db_schema_update_date=NOW() where db_schema_version < 1296;

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

ALTER TABLE system_settings ADD did_ra_extensions_enabled ENUM('0','1') default '0';

ALTER TABLE vicidial_users ADD modify_shifts ENUM('1','0') default '0';
ALTER TABLE vicidial_users ADD modify_phones ENUM('1','0') default '0';
ALTER TABLE vicidial_users ADD modify_carriers ENUM('1','0') default '0';
ALTER TABLE vicidial_users ADD modify_labels ENUM('1','0') default '0';
ALTER TABLE vicidial_users ADD modify_statuses ENUM('1','0') default '0';
ALTER TABLE vicidial_users ADD modify_voicemail ENUM('1','0') default '0';
ALTER TABLE vicidial_users ADD modify_audiostore ENUM('1','0') default '0';
ALTER TABLE vicidial_users ADD modify_moh ENUM('1','0') default '0';
ALTER TABLE vicidial_users ADD modify_tts ENUM('1','0') default '0';

UPDATE system_settings SET db_schema_version='1297',db_schema_update_date=NOW() where db_schema_version < 1297;

ALTER TABLE vicidial_inbound_groups MODIFY drop_action ENUM('HANGUP','MESSAGE','VOICEMAIL','IN_GROUP','CALLMENU') default 'MESSAGE';
ALTER TABLE vicidial_inbound_groups MODIFY after_hours_action ENUM('HANGUP','MESSAGE','EXTENSION','VOICEMAIL','IN_GROUP','CALLMENU') default 'MESSAGE';
ALTER TABLE vicidial_inbound_groups ADD action_xfer_cid VARCHAR(18) default 'CUSTOMER';
ALTER TABLE vicidial_inbound_groups ADD drop_callmenu VARCHAR(50) default '';
ALTER TABLE vicidial_inbound_groups ADD after_hours_callmenu VARCHAR(50) default '';

UPDATE system_settings SET db_schema_version='1298',db_schema_update_date=NOW() where db_schema_version < 1298;

ALTER TABLE vicidial_inbound_dids ADD custom_one VARCHAR(100) default '';
ALTER TABLE vicidial_inbound_dids ADD custom_two VARCHAR(100) default '';
ALTER TABLE vicidial_inbound_dids ADD custom_three VARCHAR(100) default '';
ALTER TABLE vicidial_inbound_dids ADD custom_four VARCHAR(100) default '';
ALTER TABLE vicidial_inbound_dids ADD custom_five VARCHAR(100) default '';

UPDATE system_settings SET db_schema_version='1299',db_schema_update_date=NOW() where db_schema_version < 1299;

ALTER TABLE vicidial_campaigns MODIFY survey_recording ENUM('Y','N','Y_WITH_AMD') default 'N';

UPDATE system_settings SET db_schema_version='1300',db_schema_update_date=NOW() where db_schema_version < 1300;

ALTER TABLE vicidial_call_menu ADD dtmf_field VARCHAR(50) default 'NONE';

UPDATE system_settings SET db_schema_version='1301',db_schema_update_date=NOW() where db_schema_version < 1301;

ALTER TABLE vicidial_campaigns ADD call_count_limit SMALLINT(5) UNSIGNED default '0';
ALTER TABLE vicidial_campaigns ADD call_count_target SMALLINT(5) UNSIGNED default '3';

ALTER TABLE vicidial_statuses ADD completed ENUM('Y','N') default 'N';
ALTER TABLE vicidial_campaign_statuses ADD completed ENUM('Y','N') default 'N';

ALTER TABLE system_settings ADD expanded_list_stats ENUM('0','1') default '1';

ALTER TABLE vicidial_shifts ADD report_option ENUM('Y','N') default 'N';

UPDATE system_settings SET db_schema_version='1302',db_schema_update_date=NOW() where db_schema_version < 1302;
