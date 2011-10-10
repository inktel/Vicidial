ALTER TABLE servers ADD sys_perf_log ENUM('Y','N') default 'N';
ALTER TABLE servers ADD vd_server_logs ENUM('Y','N') default 'Y';
ALTER TABLE servers ADD agi_output ENUM('NONE','STDERR','FILE','BOTH') default 'FILE';
ALTER TABLE vicidial_campaigns ADD allcalls_delay SMALLINT(3) UNSIGNED default '0';
ALTER TABLE vicidial_campaigns ADD omit_phone_code ENUM('Y','N') default 'N';
ALTER TABLE vicidial_campaigns MODIFY campaign_recording ENUM('NEVER','ONDEMAND','ALLCALLS','ALLFORCE') default 'ONDEMAND';

ALTER TABLE vicidial_list MODIFY phone_number VARCHAR(12);
ALTER TABLE vicidial_auto_calls MODIFY phone_number VARCHAR(12);
ALTER TABLE vicidial_log MODIFY phone_number VARCHAR(12);
ALTER TABLE vicidial_closer_log MODIFY phone_number VARCHAR(12);
ALTER TABLE vicidial_xfer_log MODIFY phone_number VARCHAR(12);
ALTER TABLE vicidial_list_pins MODIFY phone_number VARCHAR(12);
ALTER TABLE vicidial_dnc MODIFY phone_number VARCHAR(12);
ALTER TABLE vicidial_list MODIFY alt_phone VARCHAR(12);

ALTER TABLE phones MODIFY local_gmt VARCHAR(6) default '-5';
ALTER TABLE servers MODIFY local_gmt VARCHAR(6) default '-5';

ALTER TABLE vicidial_campaigns MODIFY auto_dial_level VARCHAR(6) default '0';
ALTER TABLE vicidial_campaigns ADD dial_method ENUM('MANUAL','RATIO','ADAPT_HARD_LIMIT','ADAPT_TAPERED','ADAPT_AVERAGE') default 'MANUAL';
ALTER TABLE vicidial_campaigns ADD available_only_ratio_tally ENUM('Y','N') default 'N';
ALTER TABLE vicidial_campaigns ADD adaptive_dropped_percentage SMALLINT(3) default '3';
ALTER TABLE vicidial_campaigns ADD adaptive_maximum_level VARCHAR(6) default '3.0';
ALTER TABLE vicidial_campaigns ADD adaptive_latest_server_time VARCHAR(4) default '2100';
ALTER TABLE vicidial_campaigns ADD adaptive_intensity VARCHAR(6) default '0';
ALTER TABLE vicidial_campaigns ADD adaptive_dl_diff_target SMALLINT(3) default '0';

ALTER TABLE vicidial_campaign_stats ADD differential_onemin VARCHAR(20) default '0';
ALTER TABLE vicidial_campaign_stats ADD agents_average_onemin VARCHAR(20) default '0';

ALTER TABLE vicidial_closer_log ADD queue_seconds DECIMAL(7,2) default '0';
