ALTER TABLE vicidial_closer_log ADD xfercallid INT(9) UNSIGNED;

ALTER TABLE vicidial_campaign_server_stats ENGINE=HEAP;

ALTER TABLE live_channels ENGINE=HEAP;

ALTER TABLE live_sip_channels ENGINE=HEAP;

ALTER TABLE parked_channels ENGINE=HEAP;

ALTER TABLE server_updater ENGINE=HEAP;

ALTER TABLE web_client_sessions ENGINE=HEAP;


ALTER TABLE vicidial_campaigns MODIFY lead_order VARCHAR(30);

DROP index user on vicidial_users;
ALTER TABLE vicidial_users MODIFY user VARCHAR(20) NOT NULL;
CREATE UNIQUE INDEX user ON vicidial_users (user);
ALTER TABLE vicidial_users MODIFY pass VARCHAR(20) NOT NULL;
ALTER TABLE vicidial_users MODIFY user_level TINYINT(2) NOT NULL default '1';

 CREATE TABLE vicidial_user_closer_log (
user VARCHAR(20),
campaign_id VARCHAR(20),
event_date DATETIME,
blended ENUM('1','0') default '0',
closer_campaigns TEXT,
index (user),
index (event_date)
);

ALTER TABLE vicidial_users ADD qc_enabled ENUM('1','0') default '0';
ALTER TABLE vicidial_users ADD qc_user_level INT(2) default '1';
ALTER TABLE vicidial_users ADD qc_pass ENUM('1','0') default '0';
ALTER TABLE vicidial_users ADD qc_finish ENUM('1','0') default '0';
ALTER TABLE vicidial_users ADD qc_commit ENUM('1','0') default '0';

ALTER TABLE vicidial_user_groups ADD qc_allowed_campaigns TEXT;
ALTER TABLE vicidial_user_groups ADD qc_allowed_inbound_groups TEXT;

ALTER TABLE system_settings ADD db_schema_version INT(8) UNSIGNED default '0';

UPDATE system_settings SET db_schema_version='1074', version='2.0.5b0.5';

ALTER TABLE live_inbound MODIFY uniqueid VARCHAR(20) NOT NULL;
ALTER TABLE live_inbound_log MODIFY uniqueid VARCHAR(20) NOT NULL;
ALTER TABLE live_inbound_log MODIFY uniqueid VARCHAR(20) NOT NULL;
ALTER TABLE vicidial_manager MODIFY uniqueid VARCHAR(20) NOT NULL;
ALTER TABLE vicidial_live_agents MODIFY uniqueid VARCHAR(20) NOT NULL;
ALTER TABLE vicidial_auto_calls MODIFY uniqueid VARCHAR(20) NOT NULL;
ALTER TABLE call_log DROP PRIMARY KEY;
ALTER TABLE call_log DROP INDEX uniqueid;
ALTER TABLE call_log MODIFY uniqueid VARCHAR(20) PRIMARY KEY UNIQUE NOT NULL;
ALTER TABLE park_log DROP PRIMARY KEY;
ALTER TABLE park_log DROP INDEX uniqueid;
ALTER TABLE park_log MODIFY uniqueid VARCHAR(20) PRIMARY KEY UNIQUE NOT NULL;
ALTER TABLE vicidial_log DROP PRIMARY KEY;
ALTER TABLE vicidial_log DROP INDEX uniqueid;
ALTER TABLE vicidial_log MODIFY uniqueid VARCHAR(20) PRIMARY KEY UNIQUE NOT NULL;

UPDATE system_settings SET db_schema_version='1075';

ALTER TABLE vicidial_auto_calls ADD queue_priority TINYINT(2) default '0';
ALTER TABLE vicidial_campaigns ADD queue_priority TINYINT(2) default '50';
ALTER TABLE vicidial_inbound_groups ADD queue_priority TINYINT(2) default '0';

UPDATE system_settings SET db_schema_version='1076';

ALTER TABLE vicidial_inbound_groups CHANGE drop_message drop_action ENUM('HANGUP','MESSAGE','VOICEMAIL','IN_GROUP') default 'MESSAGE';
ALTER TABLE vicidial_inbound_groups ADD drop_inbound_group VARCHAR(20) default '---NONE---';
UPDATE vicidial_inbound_groups SET drop_action='MESSAGE';

ALTER TABLE vicidial_campaigns CHANGE safe_harbor_message drop_action ENUM('HANGUP','MESSAGE','VOICEMAIL','IN_GROUP') default 'MESSAGE';
ALTER TABLE vicidial_campaigns ADD drop_inbound_group VARCHAR(20) default '---NONE---';
UPDATE vicidial_campaigns SET drop_action='MESSAGE';

UPDATE system_settings SET db_schema_version='1077';

ALTER TABLE vicidial_campaigns ADD qc_enabled ENUM('Y','N') default 'N';
ALTER TABLE vicidial_campaigns ADD qc_statuses TEXT;
ALTER TABLE vicidial_campaigns ADD qc_lists TEXT;
ALTER TABLE vicidial_campaigns ADD campaign_shift_start_time VARCHAR(4) default '0900';
ALTER TABLE vicidial_campaigns ADD campaign_shift_length VARCHAR(5) default '16:00';
ALTER TABLE vicidial_campaigns ADD campaign_day_start_time VARCHAR(4) default '0100';

UPDATE system_settings SET db_schema_version='1078';

ALTER TABLE vicidial_campaigns ADD qc_web_form_address VARCHAR(255);
ALTER TABLE vicidial_campaigns ADD qc_script VARCHAR(10);

UPDATE system_settings SET db_schema_version='1079';

ALTER TABLE vicidial_inbound_groups ADD ingroup_recording_override  ENUM('DISABLED','NEVER','ONDEMAND','ALLCALLS','ALLFORCE') default 'DISABLED';
ALTER TABLE vicidial_inbound_groups ADD ingroup_rec_filename VARCHAR(50) default 'NONE';

UPDATE system_settings SET db_schema_version='1080';

 CREATE TABLE vicidial_qc_codes (
code VARCHAR(8) PRIMARY KEY NOT NULL,
code_name VARCHAR(30)
);

UPDATE system_settings SET db_schema_version='1081';

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

ALTER TABLE vicidial_log ADD term_reason  ENUM('CALLER','AGENT','QUEUETIMEOUT','ABANDON','AFTERHOURS','NONE') default 'NONE';
ALTER TABLE vicidial_closer_log ADD term_reason  ENUM('CALLER','AGENT','QUEUETIMEOUT','ABANDON','AFTERHOURS','NONE') default 'NONE';

ALTER TABLE vicidial_inbound_groups MODIFY after_hours_action ENUM('HANGUP','MESSAGE','EXTENSION','VOICEMAIL','IN_GROUP') default 'MESSAGE';
ALTER TABLE vicidial_inbound_groups ADD afterhours_xfer_group VARCHAR(20) default '---NONE---';

UPDATE system_settings SET db_schema_version='1082';

 CREATE TABLE phones_alias (
alias_id VARCHAR(20) NOT NULL UNIQUE PRIMARY KEY,
alias_name VARCHAR(50),
logins_list VARCHAR(255)
);

UPDATE system_settings SET db_schema_version='1083';

ALTER TABLE system_settings ADD auto_user_add_value INT(9) UNSIGNED default '101';
UPDATE system_settings SET auto_user_add_value='1101';

 CREATE TABLE vicidial_shifts (
shift_id VARCHAR(20) NOT NULL,
shift_name VARCHAR(50),
shift_start_time VARCHAR(4) default '0900',
shift_length VARCHAR(5) default '16:00',
shift_weekdays VARCHAR(7) default '0123456',
index (shift_id)
);

ALTER TABLE vicidial_user_groups ADD group_shifts TEXT;

UPDATE system_settings SET db_schema_version='1084';

CREATE INDEX lead_id ON vicidial_agent_log (lead_id);

UPDATE system_settings SET db_schema_version='1085';

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

UPDATE system_settings SET db_schema_version='1086';

ALTER TABLE vicidial_timeclock_log MODIFY event_date DATETIME;
ALTER TABLE vicidial_timeclock_log ADD event_datestamp TIMESTAMP NOT NULL;
ALTER TABLE vicidial_timeclock_log ADD tcid_link INT(9) UNSIGNED;

UPDATE system_settings SET db_schema_version='1087';

ALTER TABLE vicidial_auto_calls MODIFY status ENUM('SENT','RINGING','LIVE','XFER','PAUSED','CLOSER','BUSY','DISCONNECT','IVR') default 'PAUSED';

UPDATE system_settings SET db_schema_version='1088';

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

UPDATE system_settings SET db_schema_version='1089';

ALTER TABLE system_settings ADD timeclock_end_of_day VARCHAR(4) default '0000';
ALTER TABLE system_settings ADD timeclock_last_reset_date DATE;

UPDATE system_settings SET db_schema_version='1090';

ALTER TABLE vicidial_campaigns ADD survey_first_audio_file VARCHAR(50) default 'US_pol_survey_hello';
ALTER TABLE vicidial_campaigns ADD survey_dtmf_digits VARCHAR(16) default '1238';
ALTER TABLE vicidial_campaigns ADD survey_ni_digit VARCHAR(1) default '8';
ALTER TABLE vicidial_campaigns ADD survey_opt_in_audio_file VARCHAR(50) default 'US_pol_survey_transfer';
ALTER TABLE vicidial_campaigns ADD survey_ni_audio_file VARCHAR(50) default 'US_thanks_no_contact';
ALTER TABLE vicidial_campaigns ADD survey_method ENUM('AGENT_XFER','VOICEMAIL','EXTENSION','HANGUP','CAMPREC_60_WAV') default 'AGENT_XFER';
ALTER TABLE vicidial_campaigns ADD survey_no_response_action ENUM('OPTIN','OPTOUT') default 'OPTIN';
ALTER TABLE vicidial_campaigns ADD survey_ni_status VARCHAR(6) default 'NI';
ALTER TABLE vicidial_campaigns ADD survey_response_digit_map VARCHAR(255) default '1-DEMOCRAT|2-REPUBLICAN|3-INDEPENDANT|8-OPTOUT|X-NO RESPONSE|';
ALTER TABLE vicidial_campaigns ADD survey_xfer_exten VARCHAR(20) default '8300';
ALTER TABLE vicidial_campaigns ADD survey_camp_record_dir VARCHAR(255) default '/home/survey';

INSERT INTO vicidial_statuses values('SVYEXT','Survey sent to Extension','N','N','UNDEFINED');
INSERT INTO vicidial_statuses values('SVYVM','Survey sent to Voicemail','N','N','UNDEFINED');
INSERT INTO vicidial_statuses values('SVYHU','Survey Hungup','N','N','UNDEFINED');
INSERT INTO vicidial_statuses values('SVYREC','Survey sent to Record','N','N','UNDEFINED');

ALTER TABLE vicidial_users ADD add_timeclock_log ENUM('1','0') default '0';
ALTER TABLE vicidial_users ADD modify_timeclock_log ENUM('1','0') default '0';
ALTER TABLE vicidial_users ADD delete_timeclock_log ENUM('1','0') default '0';

UPDATE system_settings SET db_schema_version='1091';

CREATE INDEX user ON vicidial_agent_log (user);

UPDATE system_settings SET db_schema_version='1092';

DROP TABLE vicidial_admin_log;

 CREATE TABLE vicidial_admin_log (
admin_log_id INT(9) UNSIGNED AUTO_INCREMENT PRIMARY KEY NOT NULL,
event_date DATETIME NOT NULL,
user VARCHAR(20) NOT NULL,
ip_address VARCHAR(15) NOT NULL,
event_section VARCHAR(30) NOT NULL,
event_type ENUM('ADD','COPY','LOAD','RESET','MODIFY','DELETE','SEARCH','LOGIN','LOGOUT','CLEAR','OTHER') default 'OTHER',
record_id VARCHAR(50) NOT NULL,
event_code VARCHAR(255) NOT NULL,
event_sql TEXT,
event_notes TEXT,
index (user),
index (event_section),
index (record_id)
);

UPDATE system_settings SET db_schema_version='1093';

ALTER TABLE vicidial_live_agents ADD external_hangup VARCHAR(1) default '';
ALTER TABLE vicidial_live_agents ADD external_status VARCHAR(6) default '';

ALTER TABLE vicidial_list MODIFY gender ENUM('M','F','U') default 'U';

ALTER TABLE system_settings ADD vdc_header_date_format VARCHAR(50) default 'MS_DASH_24HR  2008-06-24 23:59:59';
ALTER TABLE system_settings ADD vdc_customer_date_format VARCHAR(50) default 'AL_TEXT_AMPM  OCT 24, 2008 11:59:59 PM';
ALTER TABLE system_settings ADD vdc_header_phone_format VARCHAR(50) default 'US_PARN (000)000-0000';

UPDATE system_settings SET db_schema_version='1094';

ALTER TABLE vicidial_campaigns MODIFY campaign_cid VARCHAR(20) default '0000000000';

UPDATE system_settings SET db_schema_version='1095';

ALTER TABLE vicidial_campaigns ADD disable_alter_custphone ENUM('Y','N') default 'Y';

ALTER TABLE vicidial_users ADD alter_custphone_override ENUM('NOT_ACTIVE','ALLOW_ALTER') default 'NOT_ACTIVE';
ALTER TABLE vicidial_users ADD vdc_agent_api_access ENUM('0','1') default '0';

ALTER TABLE system_settings ADD vdc_agent_api_active ENUM('0','1') default '0';

UPDATE system_settings SET db_schema_version='1096';

ALTER TABLE vicidial_campaigns ADD display_queue_count ENUM('Y','N') default 'Y';

UPDATE system_settings SET db_schema_version='1097';

ALTER TABLE vicidial_list MODIFY source_id VARCHAR(50);

ALTER TABLE recording_log ADD vicidial_id VARCHAR(20);
CREATE INDEX vicidial_id ON recording_log (vicidial_id);
ALTER TABLE recording_log MODIFY start_epoch INT(10) UNSIGNED;
ALTER TABLE recording_log MODIFY end_epoch INT(10) UNSIGNED;
ALTER TABLE recording_log MODIFY length_in_sec MEDIUMINT(8) UNSIGNED;

ALTER TABLE system_settings ADD qc_last_pull_time DATETIME;

UPDATE system_settings SET db_schema_version='1098';

ALTER TABLE vicidial_campaigns MODIFY manual_dial_list_id BIGINT(14) UNSIGNED default '998';

UPDATE system_settings SET db_schema_version='1099';

ALTER TABLE vicidial_list ADD last_local_call_time DATETIME;
CREATE INDEX last_local_call_time ON vicidial_list (last_local_call_time);

UPDATE system_settings SET db_schema_version='1100';

INSERT INTO vicidial_shifts SET shift_id='24HRMIDNIGHT',shift_name='24 hours 7 days a week',shift_start_time='0000',shift_length='24:00',shift_weekdays='0123456';

ALTER TABLE vicidial_campaigns CHANGE campaign_shift_start_time qc_shift_id VARCHAR(20) default '24HRMIDNIGHT';
ALTER TABLE vicidial_campaigns CHANGE campaign_shift_length qc_get_record_launch ENUM('NONE','SCRIPT','WEBFORM','QCSCRIPT','QCWEBFORM') default 'NONE';
ALTER TABLE vicidial_campaigns CHANGE campaign_day_start_time qc_show_recording ENUM('Y','N') default 'Y';
UPDATE vicidial_campaigns SET qc_shift_id='24HRMIDNIGHT';
UPDATE vicidial_campaigns SET qc_get_record_launch='NONE';
UPDATE vicidial_campaigns SET qc_show_recording='Y';

ALTER TABLE vicidial_inbound_groups ADD qc_enabled ENUM('Y','N') default 'N';
ALTER TABLE vicidial_inbound_groups ADD qc_statuses TEXT;
ALTER TABLE vicidial_inbound_groups ADD qc_shift_id VARCHAR(20) default '24HRMIDNIGHT';
ALTER TABLE vicidial_inbound_groups ADD qc_get_record_launch ENUM('NONE','SCRIPT','WEBFORM','QCSCRIPT','QCWEBFORM') default 'NONE';
ALTER TABLE vicidial_inbound_groups ADD qc_show_recording ENUM('Y','N') default 'Y';
ALTER TABLE vicidial_inbound_groups ADD qc_web_form_address VARCHAR(255);
ALTER TABLE vicidial_inbound_groups ADD qc_script VARCHAR(10);

UPDATE system_settings SET qc_last_pull_time="2008-01-01";

UPDATE system_settings SET db_schema_version='1101';

ALTER TABLE vicidial_status_categories ADD sale_category ENUM('Y','N') default 'N';
ALTER TABLE vicidial_status_categories ADD dead_lead_category ENUM('Y','N') default 'N';

UPDATE system_settings SET db_schema_version='1102';

ALTER TABLE vicidial_campaigns ADD manual_dial_filter VARCHAR(50) default 'NONE';
ALTER TABLE vicidial_campaigns ADD agent_clipboard_copy VARCHAR(50) default 'NONE';

UPDATE system_settings SET db_schema_version='1103';

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

ALTER TABLE vicidial_hopper MODIFY alt_dial VARCHAR(6) default 'NONE';

ALTER TABLE vicidial_auto_calls MODIFY alt_dial VARCHAR(6) default 'NONE';

ALTER TABLE vicidial_campaigns MODIFY auto_alt_dial ENUM('NONE','ALT_ONLY','ADDR3_ONLY','ALT_AND_ADDR3','ALT_AND_EXTENDED','ALT_AND_ADDR3_AND_EXTENDED','EXTENDED_ONLY') default 'NONE';

ALTER TABLE vicidial_log ADD alt_dial VARCHAR(6) default 'NONE';

ALTER TABLE vicidial_campaigns ADD agent_extended_alt_dial ENUM('Y','N') default 'N';

UPDATE system_settings SET db_schema_version='1104';

GRANT RELOAD ON *.* TO cron@'%';
GRANT RELOAD ON *.* TO cron@localhost;

flush privileges;

ALTER TABLE vicidial_dnc MODIFY phone_number VARCHAR(18) NOT NULL;

ALTER TABLE vicidial_list MODIFY phone_number VARCHAR(18) NOT NULL;

ALTER TABLE vicidial_auto_calls MODIFY phone_number VARCHAR(18);

ALTER TABLE vicidial_log MODIFY phone_number VARCHAR(18);

ALTER TABLE vicidial_closer_log MODIFY phone_number VARCHAR(18);

ALTER TABLE vicidial_xfer_log MODIFY phone_number VARCHAR(18);

ALTER TABLE vicidial_list_pins MODIFY phone_number VARCHAR(18);

ALTER TABLE vicidial_ivr MODIFY phone_number VARCHAR(18);

ALTER TABLE vicidial_campaigns ADD use_campaign_dnc ENUM('Y','N') default 'N';

CREATE TABLE vicidial_campaign_dnc (
phone_number VARCHAR(18) NOT NULL,
campaign_id VARCHAR(8) NOT NULL,
index (phone_number),
UNIQUE INDEX phonecamp (phone_number, campaign_id)
);

UPDATE system_settings SET db_schema_version='1105';

ALTER TABLE vicidial_conferences ADD leave_3way ENUM('0','1') default '0';
ALTER TABLE vicidial_conferences ADD leave_3way_datetime DATETIME;

UPDATE system_settings SET db_schema_version='1106';

CREATE UNIQUE INDEX serverconf on vicidial_conferences (server_ip, conf_exten);

CREATE INDEX campaign_id on vicidial_closer_log (campaign_id);

ALTER TABLE vicidial_inbound_groups ADD play_place_in_line ENUM('Y','N') default 'N';
ALTER TABLE vicidial_inbound_groups ADD play_estimate_hold_time ENUM('Y','N') default 'N';
ALTER TABLE vicidial_inbound_groups ADD hold_time_option ENUM('NONE','EXTENSION','VOICEMAIL','IN_GROUP','CALLERID_CALLBACK','DROP_ACTION') default 'NONE';
ALTER TABLE vicidial_inbound_groups ADD hold_time_option_seconds SMALLINT(5) default '360';
ALTER TABLE vicidial_inbound_groups ADD hold_time_option_exten VARCHAR(20) default '8300';
ALTER TABLE vicidial_inbound_groups ADD hold_time_option_voicemail VARCHAR(20) default '';
ALTER TABLE vicidial_inbound_groups ADD hold_time_option_xfer_group VARCHAR(20) default '---NONE---';
ALTER TABLE vicidial_inbound_groups ADD hold_time_option_callback_filename VARCHAR(50) default 'vm-hangup';
ALTER TABLE vicidial_inbound_groups ADD hold_time_option_callback_list_id BIGINT(14) UNSIGNED default '999';
ALTER TABLE vicidial_inbound_groups ADD hold_recall_xfer_group VARCHAR(20) default '---NONE---';
ALTER TABLE vicidial_inbound_groups ADD no_delay_call_route ENUM('Y','N') default 'N';
ALTER TABLE vicidial_inbound_groups ADD play_welcome_message ENUM('ALWAYS','NEVER','IF_WAIT_ONLY','YES_UNLESS_NODELAY') default 'ALWAYS';

INSERT INTO vicidial_inbound_groups(group_id,group_name,group_color,active) values('AGENTDIRECT','Single Agent Direct Queue','white','Y');

CREATE TABLE vicidial_inbound_dids (
did_id INT(9) UNSIGNED AUTO_INCREMENT PRIMARY KEY NOT NULL,
did_pattern VARCHAR(50) NOT NULL,
did_description VARCHAR(50),
did_active ENUM('Y','N') default 'Y',
did_route ENUM('EXTEN','VOICEMAIL','AGENT','PHONE','IN_GROUP') default 'EXTEN',
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
unique index (did_pattern),
index (group_id)
);

ALTER TABLE vicidial_auto_calls ADD agent_only VARCHAR(20) default '';

ALTER TABLE phones ADD email VARCHAR(100);

ALTER TABLE vicidial_users ADD modify_inbound_dids ENUM('1','0') default '0';
ALTER TABLE vicidial_users ADD delete_inbound_dids ENUM('1','0') default '0';

UPDATE system_settings SET db_schema_version='1107';

ALTER TABLE vicidial_campaigns ADD three_way_call_cid ENUM('CAMPAIGN','CUSTOMER','AGENT_PHONE') default 'CAMPAIGN';

INSERT INTO vicidial_phone_codes (country_code, country, areacode, state, GMT_offset, DST, DST_range, geographic_description) VALUES ('1','USA','227','MD','-5','Y','SSM-FSN','');
INSERT INTO vicidial_phone_codes (country_code, country, areacode, state, GMT_offset, DST, DST_range, geographic_description) VALUES ('1','CAN','343','ON','-5','Y','SSM-FSN','');
INSERT INTO vicidial_phone_codes (country_code, country, areacode, state, GMT_offset, DST, DST_range, geographic_description) VALUES ('1','USA','364','KY','-6','Y','SSM-FSN','');
INSERT INTO vicidial_phone_codes (country_code, country, areacode, state, GMT_offset, DST, DST_range, geographic_description) VALUES ('1','USA','447','IL','-6','Y','SSM-FSN','');
INSERT INTO vicidial_phone_codes (country_code, country, areacode, state, GMT_offset, DST, DST_range, geographic_description) VALUES ('1','USA','575','NM','-7','Y','SSM-FSN','');
INSERT INTO vicidial_phone_codes (country_code, country, areacode, state, GMT_offset, DST, DST_range, geographic_description) VALUES ('1','CAN','581','QC','-5','Y','SSM-FSN','');
INSERT INTO vicidial_phone_codes (country_code, country, areacode, state, GMT_offset, DST, DST_range, geographic_description) VALUES ('1','CAN','587','AB','-7','Y','SSM-FSN','');
INSERT INTO vicidial_phone_codes (country_code, country, areacode, state, GMT_offset, DST, DST_range, geographic_description) VALUES ('1','USA','659','AL','-6','Y','SSM-FSN','');
INSERT INTO vicidial_phone_codes (country_code, country, areacode, state, GMT_offset, DST, DST_range, geographic_description) VALUES ('1','USA','667','MD','-5','Y','SSM-FSN','');
INSERT INTO vicidial_phone_codes (country_code, country, areacode, state, GMT_offset, DST, DST_range, geographic_description) VALUES ('1','USA','681','WV','-5','Y','SSM-FSN','');
INSERT INTO vicidial_phone_codes (country_code, country, areacode, state, GMT_offset, DST, DST_range, geographic_description) VALUES ('1','USA','730','IL','-6','Y','SSM-FSN','');
INSERT INTO vicidial_phone_codes (country_code, country, areacode, state, GMT_offset, DST, DST_range, geographic_description) VALUES ('1','DOM','829','','-4','N','','Dominican Republic');

UPDATE system_settings SET db_schema_version='1108';

INSERT INTO vicidial_statuses values('QVMAIL','Queue Abandon Voicemail Left','N','N','UNDEFINED');

ALTER TABLE vicidial_inbound_groups MODIFY hold_time_option ENUM('NONE','EXTENSION','VOICEMAIL','IN_GROUP','CALLERID_CALLBACK','DROP_ACTION','PRESS_VMAIL') default 'NONE';

UPDATE system_settings SET db_schema_version='1109';

ALTER TABLE vicidial_campaigns MODIFY dial_method ENUM('MANUAL','RATIO','ADAPT_HARD_LIMIT','ADAPT_TAPERED','ADAPT_AVERAGE','INBOUND_MAN') default 'MANUAL';

UPDATE system_settings SET db_schema_version='1110';

CREATE UNIQUE INDEX server_id on servers (server_id);

UPDATE system_settings SET db_schema_version='1111';

ALTER TABLE vicidial_closer_log ADD uniqueid VARCHAR(20) NOT NULL default '';

CREATE INDEX uniqueid on vicidial_closer_log (uniqueid);

UPDATE system_settings SET db_schema_version='1112';

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

UPDATE system_settings SET db_schema_version='1113';

CREATE INDEX event_time on vicidial_agent_log (event_time);

UPDATE system_settings SET db_schema_version='1114';

ALTER TABLE vicidial_campaigns MODIFY agent_pause_codes_active ENUM('Y','N','FORCE') default 'N';

UPDATE system_settings SET db_schema_version='1115';

ALTER TABLE vicidial_campaigns ADD three_way_dial_prefix VARCHAR(20) default '';

UPDATE system_settings SET db_schema_version='1116';

ALTER TABLE vicidial_user_groups ADD forced_timeclock_login ENUM('Y','N','ADMIN_EXEMPT') default 'N';

UPDATE system_settings SET db_schema_version='1117';

ALTER TABLE vicidial_inbound_groups ADD answer_sec_pct_rt_stat_one SMALLINT(5) UNSIGNED default '20';
ALTER TABLE vicidial_inbound_groups ADD answer_sec_pct_rt_stat_two SMALLINT(5) UNSIGNED default '30';

ALTER TABLE vicidial_campaign_stats ADD hold_sec_stat_one MEDIUMINT(8) UNSIGNED default '0';
ALTER TABLE vicidial_campaign_stats ADD hold_sec_stat_two MEDIUMINT(8) UNSIGNED default '0';
ALTER TABLE vicidial_campaign_stats ADD agent_non_pause_sec MEDIUMINT(8) UNSIGNED default '0';
ALTER TABLE vicidial_campaign_stats ADD hold_sec_answer_calls MEDIUMINT(8) UNSIGNED default '0';
ALTER TABLE vicidial_campaign_stats ADD hold_sec_drop_calls MEDIUMINT(8) UNSIGNED default '0';
ALTER TABLE vicidial_campaign_stats ADD hold_sec_queue_calls MEDIUMINT(8) UNSIGNED default '0';

CREATE INDEX comment_a on live_inbound_log (comment_a);

UPDATE system_settings SET db_schema_version='1118';

ALTER TABLE vicidial_campaigns ADD web_form_target VARCHAR(100) NOT NULL default 'vdcwebform';

UPDATE system_settings SET db_schema_version='1119';

ALTER TABLE servers ADD recording_web_link ENUM('SERVER_IP','ALT_IP') default 'SERVER_IP';
ALTER TABLE servers ADD alt_server_ip VARCHAR(100) default '';

UPDATE system_settings SET db_schema_version='1120';

ALTER TABLE vicidial_campaigns MODIFY campaign_vdad_exten VARCHAR(20) default '8368';

UPDATE system_settings SET db_schema_version='1121';

ALTER TABLE system_settings ADD enable_vtiger_integration ENUM('0','1') default '0';
ALTER TABLE system_settings ADD vtiger_server_ip VARCHAR(15);
ALTER TABLE system_settings ADD vtiger_dbname VARCHAR(50);
ALTER TABLE system_settings ADD vtiger_login VARCHAR(50);
ALTER TABLE system_settings ADD vtiger_pass VARCHAR(50);
ALTER TABLE system_settings ADD vtiger_url VARCHAR(255);

ALTER TABLE vicidial_users ADD active ENUM('Y','N') default 'Y';

ALTER TABLE vicidial_campaigns ADD vtiger_search_category VARCHAR(100) default 'LEAD';

ALTER TABLE server_updater ADD db_time TIMESTAMP;

ALTER TABLE vicidial_ivr ADD prompt_audio_11 VARCHAR(20);
ALTER TABLE vicidial_ivr ADD prompt_response_11 TINYINT(1) UNSIGNED default '0';
ALTER TABLE vicidial_ivr ADD prompt_audio_12 VARCHAR(20);
ALTER TABLE vicidial_ivr ADD prompt_response_12 TINYINT(1) UNSIGNED default '0';
ALTER TABLE vicidial_ivr ADD prompt_audio_13 VARCHAR(20);
ALTER TABLE vicidial_ivr ADD prompt_response_13 TINYINT(1) UNSIGNED default '0';
ALTER TABLE vicidial_ivr ADD prompt_audio_14 VARCHAR(20);
ALTER TABLE vicidial_ivr ADD prompt_response_14 TINYINT(1) UNSIGNED default '0';
ALTER TABLE vicidial_ivr ADD prompt_audio_15 VARCHAR(20);
ALTER TABLE vicidial_ivr ADD prompt_response_15 TINYINT(1) UNSIGNED default '0';
ALTER TABLE vicidial_ivr ADD prompt_audio_16 VARCHAR(20);
ALTER TABLE vicidial_ivr ADD prompt_response_16 TINYINT(1) UNSIGNED default '0';
ALTER TABLE vicidial_ivr ADD prompt_audio_17 VARCHAR(20);
ALTER TABLE vicidial_ivr ADD prompt_response_17 TINYINT(1) UNSIGNED default '0';
ALTER TABLE vicidial_ivr ADD prompt_audio_18 VARCHAR(20);
ALTER TABLE vicidial_ivr ADD prompt_response_18 TINYINT(1) UNSIGNED default '0';
ALTER TABLE vicidial_ivr ADD prompt_audio_19 VARCHAR(20);
ALTER TABLE vicidial_ivr ADD prompt_response_19 TINYINT(1) UNSIGNED default '0';
ALTER TABLE vicidial_ivr ADD prompt_audio_20 VARCHAR(20);
ALTER TABLE vicidial_ivr ADD prompt_response_20 TINYINT(1) UNSIGNED default '0';

ALTER TABLE system_settings MODIFY vicidial_agent_disable ENUM('NOT_ACTIVE','LIVE_AGENT','EXTERNAL','ALL') default 'ALL';

UPDATE system_settings SET db_schema_version='1122';

ALTER TABLE vicidial_campaigns ADD vtiger_create_call_record ENUM('Y','N') default 'Y';
ALTER TABLE vicidial_campaigns ADD vtiger_create_lead_record ENUM('Y','N') default 'Y';

UPDATE system_settings SET db_schema_version='1123';

ALTER TABLE vicidial_closer_log ADD agent_only VARCHAR(20) default '';

UPDATE system_settings SET db_schema_version='1124';

ALTER TABLE vicidial_live_agents ADD external_pause VARCHAR(20) default '';
ALTER TABLE vicidial_live_agents ADD external_dial VARCHAR(100) default '';

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
index(api_date)
);

UPDATE system_settings SET db_schema_version='1125';

ALTER TABLE vicidial_campaigns ADD vtiger_screen_login ENUM('Y','N') default 'Y';

ALTER TABLE vicidial_users ADD alert_enabled ENUM('1','0') default '0';

UPDATE system_settings SET db_schema_version='1126';

CREATE TABLE vicidial_nanpa_prefix_codes (
areacode CHAR(3),
prefix CHAR(3),
GMT_offset VARCHAR(6),
DST enum('Y','N'),
latitude VARCHAR(17),
longitude VARCHAR(17)
);

CREATE INDEX areaprefix on vicidial_nanpa_prefix_codes (areacode,prefix);

UPDATE system_settings SET db_schema_version='1127';

ALTER TABLE system_settings ADD qc_features_active ENUM('1','0') default '0';
ALTER TABLE system_settings ADD outbound_autodial_active ENUM('1','0') default '1';

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

ALTER TABLE vicidial_campaigns ADD cpd_amd_action ENUM('DISABLED','DISPO','MESSAGE') default 'DISABLED';

UPDATE system_settings SET db_schema_version='1128';

ALTER TABLE vicidial_users ADD download_lists ENUM('1','0') default '0';

UPDATE system_settings SET db_schema_version='1129';

ALTER TABLE servers ADD active_asterisk_server ENUM('Y','N') default 'Y';
ALTER TABLE servers ADD generate_vicidial_conf ENUM('Y','N') default 'Y';
ALTER TABLE servers ADD rebuild_conf_files ENUM('Y','N') default 'Y';

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
unique index(carrier_id),
index (server_ip)
);

ALTER TABLE phones ADD template_id VARCHAR(15) NOT NULL;
ALTER TABLE phones ADD conf_override TEXT;

INSERT INTO vicidial_conf_templates SET template_id='SIP_generic',template_name='SIP phone generic',template_contents="type=friend\nhost=dynamic\ncanreinvite=no\ncontext=default";
INSERT INTO vicidial_conf_templates SET template_id='IAX_generic',template_name='IAX phone generic',template_contents="type=friend\nhost=dynamic\nmaxauthreq=10\nauth=md5,plaintext,rsa\ncontext=default";

INSERT INTO vicidial_server_carriers SET carrier_id='PARAXIP', carrier_name='TEST ParaXip CPD example',registration_string='', template_id='--NONE--', account_entry="[paraxip]\ndisallow=all\nallow=ulaw\ntype=peer\nusername=paraxip\nfromuser=paraxip\nsecret=test\nfromdomain=10.10.10.16\nhost=10.10.10.15\ninsecure=port,invite\noutboundproxy=10.0.0.7", protocol='SIP', globals_string='TESTSIPTRUNKP = SIP/paraxip', dialplan_entry="exten => _5591999NXXXXXX,1,AGI(agi://127.0.0.1:4577/call_log)\nexten => _5591999NXXXXXX,2,Dial(${TESTSIPTRUNKP}/${EXTEN:4},,tTor)\nexten => _5591999NXXXXXX,3,Hangup", server_ip='10.10.10.15', active='N';
INSERT INTO vicidial_server_carriers SET carrier_id='SIPEXAMPLE', carrier_name='TEST SIP carrier example',registration_string='register => testcarrier:test@10.10.10.15:5060', template_id='--NONE--', account_entry="[testcarrier]\ndisallow=all\nallow=ulaw\ntype=friend\nusername=testcarrier\nsecret=test\nhost=dynamic\ndtmfmode=rfc2833\ncontext=trunkinbound\n", protocol='SIP', globals_string='TESTSIPTRUNK = SIP/testcarrier', dialplan_entry="exten => _91999NXXXXXX,1,AGI(agi://127.0.0.1:4577/call_log)\nexten => _91999NXXXXXX,2,Dial(${TESTSIPTRUNK}/${EXTEN:2},,tTor)\nexten => _91999NXXXXXX,3,Hangup\n", server_ip='10.10.10.15', active='N';
INSERT INTO vicidial_server_carriers SET carrier_id='IAXEXAMPLE', carrier_name='TEST IAX carrier example',registration_string='register => testcarrier:test@10.10.10.15:4569', template_id='--NONE--', account_entry="[testcarrier]\ndisallow=all\nallow=ulaw\ntype=friend\naccountcode=testcarrier\nsecret=test\nhost=dynamic\ncontext=trunkinbound\n", protocol='IAX2', globals_string='TESTIAXTRUNK = IAX2/testcarrier', dialplan_entry="exten => _71999NXXXXXX,1,AGI(agi://127.0.0.1:4577/call_log)\nexten => _71999NXXXXXX,2,Dial(${TESTIAXTRUNK}/${EXTEN:2},,tTor)\nexten => _71999NXXXXXX,3,Hangup\n", server_ip='10.10.10.15', active='N';

UPDATE system_settings SET db_schema_version='1130';

INSERT INTO vicidial_inbound_dids SET did_pattern='default', did_description='Default DID', did_active='Y', did_route='EXTEN', extension='9998811112', exten_context='default';

UPDATE system_settings SET db_schema_version='1131';

ALTER TABLE vicidial_campaign_agents ADD group_web_vars VARCHAR(255) default '';

ALTER TABLE vicidial_inbound_group_agents ADD group_web_vars VARCHAR(255) default '';

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
index (user),
index (call_date)
);

ALTER TABLE vicidial_campaigns ADD agent_allow_group_alias ENUM('Y','N') default 'N';
ALTER TABLE vicidial_campaigns ADD default_group_alias VARCHAR(30) default '';
ALTER TABLE vicidial_campaigns MODIFY three_way_call_cid ENUM('CAMPAIGN','CUSTOMER','AGENT_PHONE','AGENT_CHOOSE') default 'CAMPAIGN';

ALTER TABLE vicidial_inbound_groups ADD default_group_alias VARCHAR(30) default '';

UPDATE system_settings SET db_schema_version='1132';

ALTER TABLE servers ADD outbound_calls_per_second SMALLINT(3) UNSIGNED default '20';
ALTER TABLE servers ADD sysload INT(6) NOT NULL default '0';
ALTER TABLE servers ADD channels_total SMALLINT(4) UNSIGNED NOT NULL default '0';
ALTER TABLE servers ADD cpu_idle_percent SMALLINT(3) UNSIGNED NOT NULL default '0';
ALTER TABLE servers ADD disk_usage VARCHAR(255) default '1';

ALTER TABLE system_settings ADD outbound_calls_per_second SMALLINT(3) UNSIGNED default '40';

ALTER TABLE vicidial_user_groups ADD shift_enforcement ENUM('OFF','START','ALL') default 'OFF';

ALTER TABLE vicidial_users ADD agent_shift_enforcement_override ENUM('DISABLED','OFF','START','ALL') default 'DISABLED';
ALTER TABLE vicidial_users ADD manager_shift_enforcement_override ENUM('0','1') default '0';
ALTER TABLE vicidial_users ADD shift_override_flag ENUM('0','1') default '0';

ALTER TABLE vicidial_admin_log MODIFY event_type ENUM('ADD','COPY','LOAD','RESET','MODIFY','DELETE','SEARCH','LOGIN','LOGOUT','CLEAR','OVERRIDE','OTHER') default 'OTHER';

UPDATE system_settings SET db_schema_version='1133';

ALTER TABLE vicidial_users ADD export_reports ENUM('1','0') default '0';

ALTER TABLE vicidial_admin_log MODIFY event_type ENUM('ADD','COPY','LOAD','RESET','MODIFY','DELETE','SEARCH','LOGIN','LOGOUT','CLEAR','OVERRIDE','EXPORT','OTHER') default 'OTHER';

UPDATE system_settings SET db_schema_version='1134';

ALTER TABLE vicidial_campaigns DROP campaign_shift_start_time;
ALTER TABLE vicidial_campaigns DROP campaign_shift_length;
ALTER TABLE vicidial_campaigns DROP campaign_day_start_time;

UPDATE system_settings SET db_schema_version='1135';
