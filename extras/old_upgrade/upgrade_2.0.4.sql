ALTER TABLE vicidial_statuses ADD human_answered ENUM('Y','N') default 'N';
ALTER TABLE vicidial_campaign_statuses ADD human_answered ENUM('Y','N') default 'Y';

UPDATE vicidial_statuses SET human_answered='Y' where status IN('DROP','DNC','DEC','SALE','XFER','CALLBK','NP','NI','N');

ALTER TABLE vicidial_campaigns ADD list_order_mix VARCHAR(20) default 'DISABLED';
UPDATE vicidial_campaigns SET list_order_mix='DISABLED';

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
tovdad_display ENUM('Y','N') default 'N'
);

ALTER TABLE vicidial_campaign_statuses ADD category VARCHAR(20) default 'UNDEFINED';
ALTER TABLE vicidial_statuses ADD category VARCHAR(20) default 'UNDEFINED';

INSERT INTO vicidial_status_categories (vsc_id,vsc_name) values('UNDEFINED','Default Category');

ALTER TABLE vicidial_campaign_stats ADD status_category_1 VARCHAR(20);
ALTER TABLE vicidial_campaign_stats ADD status_category_count_1 INT(9) UNSIGNED default '0';
ALTER TABLE vicidial_campaign_stats ADD status_category_2 VARCHAR(20);
ALTER TABLE vicidial_campaign_stats ADD status_category_count_2 INT(9) UNSIGNED default '0';
ALTER TABLE vicidial_campaign_stats ADD status_category_3 VARCHAR(20);
ALTER TABLE vicidial_campaign_stats ADD status_category_count_3 INT(9) UNSIGNED default '0';
ALTER TABLE vicidial_campaign_stats ADD status_category_4 VARCHAR(20);
ALTER TABLE vicidial_campaign_stats ADD status_category_count_4 INT(9) UNSIGNED default '0';

ALTER TABLE system_settings ADD enable_agc_xfer_log ENUM('0','1') default '0';


CREATE TABLE vicidial_ivr (
ivr_id INT(9) UNSIGNED AUTO_INCREMENT PRIMARY KEY NOT NULL,
entry_time DATETIME,
length_in_sec SMALLINT(5) UNSIGNED default '0',
inbound_number VARCHAR(12),
recording_id INT(9) UNSIGNED,
recording_filename VARCHAR(50),
company_id VARCHAR(12),
phone_number VARCHAR(12),
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
index (phone_number),
index (entry_time)
);

ALTER TABLE vicidial_ivr AUTO_INCREMENT = 1357091;

ALTER TABLE vicidial_inbound_groups ADD call_time_id VARCHAR(20) default '24hours';
ALTER TABLE vicidial_inbound_groups ADD after_hours_action ENUM('HANGUP','MESSAGE','EXTENSION','VOICEMAIL') default 'MESSAGE';
ALTER TABLE vicidial_inbound_groups ADD after_hours_message_filename VARCHAR(50) default 'vm-goodbye';
ALTER TABLE vicidial_inbound_groups ADD after_hours_exten VARCHAR(20) default '8300';
ALTER TABLE vicidial_inbound_groups ADD after_hours_voicemail VARCHAR(20);
ALTER TABLE vicidial_inbound_groups ADD welcome_message_filename VARCHAR(50) default '---NONE---';
ALTER TABLE vicidial_inbound_groups ADD moh_context VARCHAR(50) default 'default';
ALTER TABLE vicidial_inbound_groups ADD onhold_prompt_filename VARCHAR(50) default 'generic_hold';
ALTER TABLE vicidial_inbound_groups ADD prompt_interval SMALLINT(5) UNSIGNED default '60';
ALTER TABLE vicidial_inbound_groups ADD agent_alert_exten VARCHAR(20) default '8304';
ALTER TABLE vicidial_inbound_groups ADD agent_alert_delay INT(6) default '1000';

ALTER TABLE vicidial_list MODIFY called_count SMALLINT(5) UNSIGNED default '0';

CREATE TABLE vicidial_inbound_group_agents (
user VARCHAR(20),
group_id VARCHAR(20),			
group_rank TINYINT(1) default '0',
group_weight TINYINT(1) default '0',
calls_today SMALLINT(5) UNSIGNED default '0',
index (group_id),
index (user)
);

ALTER TABLE vicidial_campaigns ADD campaign_allow_inbound ENUM('Y','N') default 'N';

UPDATE vicidial_campaigns SET campaign_allow_inbound='Y' where campaign_id REGEXP '(CLOSER|BLEND|INBND|_C\$|_B\$|_I\$)';

INSERT INTO vicidial_lists SET list_id='999',list_name='Default inbound list',campaign_id='TESTCAMP',active='N';

ALTER TABLE vicidial_campaigns ADD manual_dial_list_id BIGINT(14) UNSIGNED default '999';

ALTER TABLE vicidial_hopper ADD priority TINYINT(2) default '0';

CREATE TABLE vicidial_live_inbound_agents (
user VARCHAR(20),
group_id VARCHAR(20),			
group_weight TINYINT(1) default '0',
calls_today SMALLINT(5) UNSIGNED default '0',
last_call_time DATETIME,
last_call_finish DATETIME,
index (group_id),
index (group_weight)
);

ALTER TABLE vicidial_inbound_groups MODIFY next_agent_call ENUM('random','oldest_call_start','oldest_call_finish','overall_user_level','inbound_group_rank','fewest_calls') default 'oldest_call_finish';

GRANT SELECT,INSERT,UPDATE,DELETE,LOCK TABLES on asterisk.* TO cron@'%' IDENTIFIED BY '1234';
GRANT SELECT,INSERT,UPDATE,DELETE,LOCK TABLES on asterisk.* TO cron@localhost IDENTIFIED BY '1234';

CREATE TABLE vicidial_campaign_agents (
user VARCHAR(20),
campaign_id VARCHAR(20),			
campaign_rank TINYINT(1) default '0',
campaign_weight TINYINT(1) default '0',
calls_today SMALLINT(5) UNSIGNED default '0',
index (campaign_id),
index (user)
);

ALTER TABLE vicidial_live_agents ADD campaign_weight TINYINT(1) default '0';
ALTER TABLE vicidial_live_agents ADD calls_today SMALLINT(5) UNSIGNED default '0';

ALTER TABLE vicidial_inbound_groups MODIFY next_agent_call ENUM('random','oldest_call_start','oldest_call_finish','overall_user_level','inbound_group_rank','campaign_rank','fewest_calls','fewest_calls_campaign') default 'oldest_call_finish';

ALTER TABLE vicidial_campaigns MODIFY next_agent_call ENUM('random','oldest_call_start','oldest_call_finish','campaign_rank','overall_user_level','fewest_calls') default 'oldest_call_finish';


ALTER TABLE vicidial_campaigns ADD default_xfer_group VARCHAR(20) default '---NONE---';

ALTER TABLE vicidial_inbound_groups ADD default_xfer_group VARCHAR(20) default '---NONE---';

ALTER TABLE vicidial_campaigns ADD xfer_groups  TEXT default '';

