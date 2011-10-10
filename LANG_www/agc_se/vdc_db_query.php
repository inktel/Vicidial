<?php
# vdc_db_query.php
# 
# Copyright (C) 2010  Matt Florell <vicidial@gmail.com>    LICENSE: AGPLv2
#
# This script is designed to exchange information between vicidial.php and the database server for various actions
# 
# required variables:
#  - $server_ip
#  - $session_name
#  - $user
#  - $pass
# optional variables:
#  - $format - ('text','debug')
#  - $ACTION - ('regCLOSER','regTERRITORY','manDiaLnextCALL','manDiaLskip','manDiaLonly','manDiaLlookCaLL','manDiaLlogCALL','userLOGout','updateDISPO','updateLEAD','VDADpause','VDADready','VDADcheckINCOMING','UpdatEFavoritEs','CalLBacKLisT','CalLBacKCounT','PauseCodeSubmit','LogiNCamPaigns','alt_phone_change','AlertControl','AGENTSview','CALLSINQUEUEview','CALLSINQUEUEgrab','DiaLableLeaDsCounT','UpdateFields')
#  - $stage - ('start','finish','lookup','new')
#  - $closer_choice - ('CL_TESTCAMP_L CL_OUT123_L -')
#  - $conf_exten - ('8600011',...)
#  - $exten - ('123test',...)
#  - $ext_context - ('default','demo',...)
#  - $ext_priority - ('1','2',...)
#  - $campaign - ('testcamp',...)
#  - $dial_timeout - ('60','26',...)
#  - $dial_prefix - ('9','8',...)
#  - $campaign_cid - ('3125551212','0000000000',...)
#  - $MDnextCID - ('M06301413000000002',...)
#  - $uniqueid - ('1120232758.2406800',...)
#  - $lead_id - ('36524',...)
#  - $list_id - ('101','123456',...)
#  - $length_in_sec - ('12',...)
#  - $phone_code - ('1',...)
#  - $phone_number - ('3125551212',...)
#  - $channel - ('Zap/12-1',...)
#  - $start_epoch - ('1120236911',...)
#  - $vendor_lead_code - ('1234test',...)
#  - $title - ('Mr.',...)
#  - $first_name - ('Bob',...)
#  - $middle_initial - ('L',...)
#  - $last_name - ('Wilson',...)
#  - $address1 - ('1324 Main St.',...)
#  - $address2 - ('Apt. 12',...)
#  - $address3 - ('co Robert Wilson',...)
#  - $city - ('Chicago',...)
#  - $state - ('IL',...)
#  - $province - ('NA',...)
#  - $postal_code - ('60054',...)
#  - $country_code - ('USA',...)
#  - $gender - ('M',...)
#  - $date_of_birth - ('1970-01-01',...)
#  - $alt_phone - ('3125551213',...)
#  - $email - ('bob@bob.com',...)
#  - $security_phrase - ('Hello',...)
#  - $comments - ('Good Customer',...)
#  - $auto_dial_level - ('0','1','1.2',...)
#  - $VDstop_rec_after_each_call - ('0','1')
#  - $conf_silent_prefix - ('7','8','5',...)
#  - $extension - ('123','user123','25-1',...)
#  - $protocol - ('Zap','SIP','IAX2',...)
#  - $user_abb - ('1234','6666',...)
#  - $preview - ('YES','NO',...)
#  - $called_count - ('0','1','2',...)
#  - $agent_log_id - ('123456',...)
#  - $agent_log - ('NO',...)
#  - $favorites_list - (",'cc160','cc100'",...)
#  - $CallBackDatETimE - ('2006-04-21 14:30:00',...)
#  - $recipient - ('ANYONE,'USERONLY')
#  - $callback_id - ('12345','12346',...)
#  - $use_internal_dnc - ('Y','N')
#  - $use_campaign_dnc - ('Y','N')
#  - $omit_phone_code - ('Y','N')
#  - $no_delete_sessions - ('0','1')
#  - $LogouTKicKAlL - ('0','1');
#  - $closer_blended = ('0','1');
#  - $inOUT = ('IN','OUT');
#  - $manual_dial_filter = ('NONE','CAMPLISTS','DNC','CAMPLISTS_DNC')
#  - $agentchannel = ('Zap/1-1','SIP/testing-6ry4i3',...)
#  - $conf_dialed = ('0','1')
#  - $leaving_threeway = ('0','1')
#  - $blind_transfer = ('0','1')
#  - $usegroupalias - ('0','1')
#  - $account - ('DEFAULT',...)
#  - $agent_dialed_number - ('1','')
#  - $agent_dialed_type - ('MANUAL_OVERRIDE','MANUAL_DIALNOW','MANUAL_PREVIEW',...)
#  - $wrapup - ('WRAPUP','')
#  - $vtiger_callback_id - ('16534'...)
#  - $nodeletevdac - ('0','1')
#  - $agent_territories - ('ABC001','ABC002'...)
#  - $alt_num_status - ('0','1')
#  - $DiaL_SecondS - ('0','1','2',...)
#
# CHANGELOG:
# 50629-1044 - First build of script
# 50630-1422 - Added manual dial action and MD channel lookup
# 50701-1451 - Added dial log for start and end of vicidial calls
# 50705-1239 - Added call disposition update
# 50804-1627 - Fixed updateDispo to update vicidial_log entry
# 50816-1605 - Added VDADpause/ready for auto dialing
# 50816-1811 - Added basic autodial call pickup functions
# 50817-1005 - Altered logging functions to accomodate auto_dialing
# 50818-1305 - Added stop-all-recordings-after-each-vicidial-call option
# 50818-1411 - Added hangup of agent phone after Logout
# 50901-1315 - Fixed CLOSER IN-GROUP Web Form bug
# 50902-1507 - Fixed CLOSER log length_in_sec bug
# 50902-1730 - Added functions for manual preview dialing and revert
# 50913-1214 - Added agent random update to leadupdate
# 51020-1421 - Added agent_log_id framework for detailed agent activity logging
# 51021-1717 - Allows for multi-line comments (changes \n to !N in database)
# 51111-1046 - Added vicidial_agent_log lead_id earlier for manual dial
# 51121-1445 - Altered echo statements for several small PHP speed optimizations
# 51122-1328 - Fixed UserLogout issue not removing conference reservation
# 51129-1012 - Added ability to accept calls from other VICIDIAL servers
# 51129-1729 - Changed manual dial to use the '/n' flag for calls
# 51221-1154 - Added SCRIPT id lookup and sending to vicidial.php for display
# 60105-1059 - Added Updating of astguiclient favorites in the DB
# 60208-1617 - Added dtmf buttons output per call
# 60213-1521 - Added closer_campaigns update to vicidial_users
# 60215-1036 - Added Callback date-time entry into vicidial_callbacks table
# 60413-1541 - Added USERONLY Callback listings output - CalLBacKLisT
#            - Added USERONLY Callback count output - CalLBacKCounT
# 60414-1140 - Added Callback lead lookup for manual dialing
# 60419-1517 - After CALLBK is sent to agent, update callback record to INACTIVE
# 60421-1419 - Check GET/POST vars lines with isset to not trigger PHP NOTICES
# 60427-1236 - Fixed closer_choice error for CLOSER campaigns
# 60609-1148 - Added ability to check for manual dial numbers in DNC
# 60619-1117 - Added variable filters to close security holes for login form
# 60623-1414 - Fixed variable filter for phone_code and fixed manual dial logic
# 60821-1600 - Added ability to omit the phone code on vicidial lead dialing
# 60821-1647 - Added ability to not delete sessions at logout
# 60906-1124 - Added lookup and sending of callback data for CALLBK calls
# 61128-2229 - Added vicidial_live_agents and vicidial_auto_calls manual dial entries
# 70111-1600 - Added ability to use BLEND/INBND/*_C/*_B/*_I as closer campaigns
# 70115-1733 - Added alt_dial functionality in auto-dial modes
# 70118-1501 - Added user_group to vicidial_log,_agent_log,_closer_log,_callbacks
# 70123-1357 - Fixed bug that would not update vicidial_closer_log status to dispo
# 70202-1438 - Added pause code submit function
# 70203-0930 - Added dialed_number to lead info output
# 70203-1030 - Added dialed_label to lead info output
# 70206-1126 - Added INBOUND status for inbound/closer calls in vicidial_live_agents
# 70212-1253 - Fixed small issue with CXFER
# 70213-1431 - Added QueueMetrics PAUSE/UNPAUSE/AGENTLOGIN/AGENTLOGOFF actions
# 70214-1231 - Added queuemetrics_log_id field for server_id in queue_log
# 70215-1210 - Added queuemetrics COMPLETEAGENT action
# 70216-1051 - Fixed double call complete queuemetrics logging
# 70222-1616 - Changed queue_log PAUSE/UNPAUSE to PAUSEALL/UNPAUSEALL
# 70309-1034 - Allow amphersands and questions marks in comments to pass through
# 70313-1052 - Allow pound signs(hash) in comments to pass through
# 70319-1544 - Added agent disable update customer data function
# 70322-1545 - Added sipsak display ability
# 70413-1253 - Fixed bug for outbound call time in CLOSER-type blended campaigns
# 70424-1100 - Fixed bug for fronter/closer calls that would delete vdac records
# 70802-1729 - Fixed bugs with pause_sec and wait_sec under certain call handling 
# 70828-1443 - Added source_id to output of SCRIPTtab-IFRAME and WEBFORM
# 71029-1855 - removed campaign_id naming restrictions for CLOSER-type campaigns
# 71030-2047 - added hopper priority for auto alt dial entries
# 71116-1011 - added calls_today count updating of the vicidial_live_agents upon INCALL
# 71120-1520 - added LogiNCamPaigns to show only allowed campaigns for agents upon login
# 71125-1751 - Added inbound-group default inbound group sending to vicidial.php
# 71129-2025 - restricted callbacks count and list to campaign only
# 71223-0318 - changed logging of closer calls
# 71226-1117 - added option to kick all calls from conference upon logout
# 80116-1032 - added user_closer_log logging in regCLOSER
# 80125-1213 - fixed vicidial_log bug when call is from closer
# 80317-2051 - Added in-group recording settings
# 80402-0121 - Fixes for manual dial transfers on some systems, removed /n persist flag
# 80424-0442 - Added non_latin lookup from system_settings
# 80430-1006 - Added term_reason for vicidial_log and vicidial_closer_log
# 80430-1957 - Changed to leave lead_id in vicidial_live_agents record until after dispo
# 80630-2153 - Added queue_log logging for Manual dial calls
# 80703-0139 - Added alter customer phone permissions
# 80707-2325 - Added vicidial_id to recording_log for tracking of vicidial or closer log to recording
# 80713-0624 - Added vicidial_list.last_local_call_time field
# 80717-1604 - Modified logging function to use inOUT to determine call direction and place to log
# 80719-1147 - Changed recording conf prefix
# 80815-1019 - Added manual dial list restriction option
# 80831-0545 - Added extended alt dial number info display support
# 80909-1710 - Added support for campaign-specific DNC lists
# 81010-1048 - Added support for hangup of all channels except for agent channel after attempting a 3way call
# 81011-1404 - Fixed bugs in leave3way when transferring a manual dial call
# 81020-1459 - Fixed bugs in queue_log logging
# 81104-0134 - Added mysql error logging capability
# 81104-1617 - Added multi-retry for some vicidial_live_agents table MySQL queries
# 81106-0410 - Added force_timeclock_login option to LoginCampaigns function
# 81107-0424 - Added carryover of script and presets for in-group calls from campaign settings
# 81110-0058 - Changed Pause time to start new vicidial_agent_log on every pause
# 81110-1512 - Added hangup_all_non_reserved to fix non-Hangup bug
# 81111-1630 - Added another hangup fix for non-hangup
# 81114-0126 - More vicidial_agent_log bug fixes
# 81119-1809 - webform backslash fix
# 81124-2212 - Fixes blind transfer bug
# 81126-1522 - Fixed callback comments bug
# 81211-0420 - Fixed Manual dial agent_log bug
# 90120-1718 - Added external pause and dial option
# 90126-1759 - Fixed QM section that wasn't qualified and added agent alert option
# 90128-0231 - Added vendor_lead_code to manual dial lead lookup
# 90304-1335 - Added support for group aliases and agent-specific variables for campaigns and in-groups
# 90305-1041 - Added agent_dialed_number and type for user_call_log feature
# 90307-1735 - Added Shift enforcement and manager override features
# 90320-0306 - Fixed agent log bug when using wrapup time
# 90323-2013 - Added function to put phone numbers in the DNC lists if they were set to status type dnc=Y
# 90324-1316 - Added functions to log calls to Vtiger accounts and update status to siccode
# 90327-1348 - Changed Vtiger status populate to use status name
# 90408-0021 - Added API vtiger specific callback activity record ability
# 90508-0726 - Changed to PHP long tags
# 90511-0923 - Added agentonly_callback_campaign_lock option
# 90519-0634 - Fixed manual dial status and logging bug
# 90611-1423 - Fixed agent log and vicidial log bugs
# 90705-2008 - Added AGENTSview function
# 90706-1431 - Added Agent view transfer selection
# 90712-2303 - Added view calls in queue, grab call from queue
# 90717-0638 - Fixed alt dial on overflow in-group calls
# 90722-1542 - Added no hopper dialing
# 90729-0637 - Added DiaLableLeaDsCounT
# 90808-0221 - Added last_state_change to vicidial_live_agents
# 90904-1622 - Added timezone sort options for no hopper dialing
# 90908-1037 - Added DEAD call logging
# 90916-1839 - Added nodeletevdac
# 90917-2246 - Fixed auto-alt-dial DNC check bug
# 90924-1544 - Added List callerid override option
# 90930-1638 - Added agent_territories feature
# 91012-0535 - Fixed User territory no-hopper dial bug
# 91019-1224 - Fixed auto-alt-dial DNC issues
# 91026-1101 - Added AREACODE DNC option
# 91108-2120 - Fixed QM log issue with PAUSEREASON entries
# 91112-1107 - Changed ENTERQUEUE to CALLOUTBOUND for QM logging
# 91123-1801 - Added outbound_autodial field
# 91204-1937 - Added logging of agent grab calls
# 91213-0946 - Added queue_position to queue_log COMPLETE... records
# 91228-1340 - Added API fields update functions
# 100103-1254 - Added 3 more conf-presets, list ID override presets and call start/dispo URLs
# 100104-1509 - Fixed vicidial_log duplicate check to allow update if dup and logging update
# 100109-0745 - Added alt_num_status for ALTNUM dialing status
# 100109-1336 - Fixed Manual dial live call detection
# 100113-1949 - Fixed dispo_choice bug and added dispo_status to dispo URL call
# 100202-2306 - Fixed logging issues related to INBOUND_MAN dial_method
# 100207-1110 - Changed Pause Codes function to allow for multiple pause codes per pause period
# 100301-1342 - Changed Available agents output for AGENTDIRECT selection
# 100413-1342 - Fixes for extended alt-dial
#

$version = '2.2.0-143';
$build = '100413-1342';
$mel=1;					# Mysql Error Log enabled = 1
$mysql_log_count=310;
$one_mysql_log=0;

require("dbconnect.php");

### If you have globals turned off uncomment these lines
if (isset($_GET["user"]))						{$user=$_GET["user"];}
	elseif (isset($_POST["user"]))				{$user=$_POST["user"];}
if (isset($_GET["pass"]))						{$pass=$_GET["pass"];}
	elseif (isset($_POST["pass"]))				{$pass=$_POST["pass"];}
if (isset($_GET["server_ip"]))					{$server_ip=$_GET["server_ip"];}
	elseif (isset($_POST["server_ip"]))			{$server_ip=$_POST["server_ip"];}
if (isset($_GET["session_name"]))				{$session_name=$_GET["session_name"];}
	elseif (isset($_POST["session_name"]))		{$session_name=$_POST["session_name"];}
if (isset($_GET["format"]))						{$format=$_GET["format"];}
	elseif (isset($_POST["format"]))			{$format=$_POST["format"];}
if (isset($_GET["ACTION"]))						{$ACTION=$_GET["ACTION"];}
	elseif (isset($_POST["ACTION"]))			{$ACTION=$_POST["ACTION"];}
if (isset($_GET["stage"]))						{$stage=$_GET["stage"];}
	elseif (isset($_POST["stage"]))				{$stage=$_POST["stage"];}
if (isset($_GET["closer_choice"]))				{$closer_choice=$_GET["closer_choice"];}
	elseif (isset($_POST["closer_choice"]))		{$closer_choice=$_POST["closer_choice"];}
if (isset($_GET["conf_exten"]))					{$conf_exten=$_GET["conf_exten"];}
	elseif (isset($_POST["conf_exten"]))		{$conf_exten=$_POST["conf_exten"];}
if (isset($_GET["exten"]))						{$exten=$_GET["exten"];}
	elseif (isset($_POST["exten"]))				{$exten=$_POST["exten"];}
if (isset($_GET["ext_context"]))				{$ext_context=$_GET["ext_context"];}
	elseif (isset($_POST["ext_context"]))		{$ext_context=$_POST["ext_context"];}
if (isset($_GET["ext_priority"]))				{$ext_priority=$_GET["ext_priority"];}
	elseif (isset($_POST["ext_priority"]))		{$ext_priority=$_POST["ext_priority"];}
if (isset($_GET["campaign"]))					{$campaign=$_GET["campaign"];}
	elseif (isset($_POST["campaign"]))			{$campaign=$_POST["campaign"];}
if (isset($_GET["dial_timeout"]))				{$dial_timeout=$_GET["dial_timeout"];}
	elseif (isset($_POST["dial_timeout"]))		{$dial_timeout=$_POST["dial_timeout"];}
if (isset($_GET["dial_prefix"]))				{$dial_prefix=$_GET["dial_prefix"];}
	elseif (isset($_POST["dial_prefix"]))		{$dial_prefix=$_POST["dial_prefix"];}
if (isset($_GET["campaign_cid"]))				{$campaign_cid=$_GET["campaign_cid"];}
	elseif (isset($_POST["campaign_cid"]))		{$campaign_cid=$_POST["campaign_cid"];}
if (isset($_GET["MDnextCID"]))					{$MDnextCID=$_GET["MDnextCID"];}
	elseif (isset($_POST["MDnextCID"]))			{$MDnextCID=$_POST["MDnextCID"];}
if (isset($_GET["uniqueid"]))					{$uniqueid=$_GET["uniqueid"];}
	elseif (isset($_POST["uniqueid"]))			{$uniqueid=$_POST["uniqueid"];}
if (isset($_GET["lead_id"]))					{$lead_id=$_GET["lead_id"];}
	elseif (isset($_POST["lead_id"]))			{$lead_id=$_POST["lead_id"];}
if (isset($_GET["list_id"]))					{$list_id=$_GET["list_id"];}
	elseif (isset($_POST["list_id"]))			{$list_id=$_POST["list_id"];}
if (isset($_GET["length_in_sec"]))				{$length_in_sec=$_GET["length_in_sec"];}
	elseif (isset($_POST["length_in_sec"]))		{$length_in_sec=$_POST["length_in_sec"];}
if (isset($_GET["phone_code"]))					{$phone_code=$_GET["phone_code"];}
	elseif (isset($_POST["phone_code"]))		{$phone_code=$_POST["phone_code"];}
if (isset($_GET["phone_number"]))				{$phone_number=$_GET["phone_number"];}
	elseif (isset($_POST["phone_number"]))		{$phone_number=$_POST["phone_number"];}
if (isset($_GET["channel"]))					{$channel=$_GET["channel"];}
	elseif (isset($_POST["channel"]))			{$channel=$_POST["channel"];}
if (isset($_GET["start_epoch"]))				{$start_epoch=$_GET["start_epoch"];}
	elseif (isset($_POST["start_epoch"]))		{$start_epoch=$_POST["start_epoch"];}
if (isset($_GET["dispo_choice"]))				{$dispo_choice=$_GET["dispo_choice"];}
	elseif (isset($_POST["dispo_choice"]))		{$dispo_choice=$_POST["dispo_choice"];}
if (isset($_GET["vendor_lead_code"]))			{$vendor_lead_code=$_GET["vendor_lead_code"];}
	elseif (isset($_POST["vendor_lead_code"]))	{$vendor_lead_code=$_POST["vendor_lead_code"];}
if (isset($_GET["title"]))						{$title=$_GET["title"];}
	elseif (isset($_POST["title"]))				{$title=$_POST["title"];}
if (isset($_GET["first_name"]))					{$first_name=$_GET["first_name"];}
	elseif (isset($_POST["first_name"]))		{$first_name=$_POST["first_name"];}
if (isset($_GET["middle_initial"]))				{$middle_initial=$_GET["middle_initial"];}
	elseif (isset($_POST["middle_initial"]))	{$middle_initial=$_POST["middle_initial"];}
if (isset($_GET["last_name"]))					{$last_name=$_GET["last_name"];}
	elseif (isset($_POST["last_name"]))			{$last_name=$_POST["last_name"];}
if (isset($_GET["address1"]))					{$address1=$_GET["address1"];}
	elseif (isset($_POST["address1"]))			{$address1=$_POST["address1"];}
if (isset($_GET["address2"]))					{$address2=$_GET["address2"];}
	elseif (isset($_POST["address2"]))			{$address2=$_POST["address2"];}
if (isset($_GET["address3"]))					{$address3=$_GET["address3"];}
	elseif (isset($_POST["address3"]))			{$address3=$_POST["address3"];}
if (isset($_GET["city"]))						{$city=$_GET["city"];}
	elseif (isset($_POST["city"]))				{$city=$_POST["city"];}
if (isset($_GET["state"]))						{$state=$_GET["state"];}
	elseif (isset($_POST["state"]))				{$state=$_POST["state"];}
if (isset($_GET["province"]))					{$province=$_GET["province"];}
	elseif (isset($_POST["province"]))			{$province=$_POST["province"];}
if (isset($_GET["postal_code"]))				{$postal_code=$_GET["postal_code"];}
	elseif (isset($_POST["postal_code"]))		{$postal_code=$_POST["postal_code"];}
if (isset($_GET["country_code"]))				{$country_code=$_GET["country_code"];}
	elseif (isset($_POST["country_code"]))		{$country_code=$_POST["country_code"];}
if (isset($_GET["gender"]))						{$gender=$_GET["gender"];}
	elseif (isset($_POST["gender"]))			{$gender=$_POST["gender"];}
if (isset($_GET["date_of_birth"]))				{$date_of_birth=$_GET["date_of_birth"];}
	elseif (isset($_POST["date_of_birth"]))		{$date_of_birth=$_POST["date_of_birth"];}
if (isset($_GET["alt_phone"]))					{$alt_phone=$_GET["alt_phone"];}
	elseif (isset($_POST["alt_phone"]))			{$alt_phone=$_POST["alt_phone"];}
if (isset($_GET["email"]))						{$email=$_GET["email"];}
	elseif (isset($_POST["email"]))				{$email=$_POST["email"];}
if (isset($_GET["security_phrase"]))			{$security_phrase=$_GET["security_phrase"];}
	elseif (isset($_POST["security_phrase"]))	{$security_phrase=$_POST["security_phrase"];}
if (isset($_GET["comments"]))					{$comments=$_GET["comments"];}
	elseif (isset($_POST["comments"]))			{$comments=$_POST["comments"];}
if (isset($_GET["auto_dial_level"]))			{$auto_dial_level=$_GET["auto_dial_level"];}
	elseif (isset($_POST["auto_dial_level"]))	{$auto_dial_level=$_POST["auto_dial_level"];}
if (isset($_GET["VDstop_rec_after_each_call"]))				{$VDstop_rec_after_each_call=$_GET["VDstop_rec_after_each_call"];}
	elseif (isset($_POST["VDstop_rec_after_each_call"]))		{$VDstop_rec_after_each_call=$_POST["VDstop_rec_after_each_call"];}
if (isset($_GET["conf_silent_prefix"]))				{$conf_silent_prefix=$_GET["conf_silent_prefix"];}
	elseif (isset($_POST["conf_silent_prefix"]))	{$conf_silent_prefix=$_POST["conf_silent_prefix"];}
if (isset($_GET["extension"]))					{$extension=$_GET["extension"];}
	elseif (isset($_POST["extension"]))			{$extension=$_POST["extension"];}
if (isset($_GET["protocol"]))					{$protocol=$_GET["protocol"];}
	elseif (isset($_POST["protocol"]))			{$protocol=$_POST["protocol"];}
if (isset($_GET["user_abb"]))					{$user_abb=$_GET["user_abb"];}
	elseif (isset($_POST["user_abb"]))			{$user_abb=$_POST["user_abb"];}
if (isset($_GET["preview"]))					{$preview=$_GET["preview"];}
	elseif (isset($_POST["preview"]))			{$preview=$_POST["preview"];}
if (isset($_GET["called_count"]))				{$called_count=$_GET["called_count"];}
	elseif (isset($_POST["called_count"]))		{$called_count=$_POST["called_count"];}
if (isset($_GET["agent_log_id"]))				{$agent_log_id=$_GET["agent_log_id"];}
	elseif (isset($_POST["agent_log_id"]))		{$agent_log_id=$_POST["agent_log_id"];}
if (isset($_GET["agent_log"]))					{$agent_log=$_GET["agent_log"];}
	elseif (isset($_POST["agent_log"]))			{$agent_log=$_POST["agent_log"];}
if (isset($_GET["favorites_list"]))				{$favorites_list=$_GET["favorites_list"];}
	elseif (isset($_POST["favorites_list"]))	{$favorites_list=$_POST["favorites_list"];}
if (isset($_GET["CallBackDatETimE"]))			{$CallBackDatETimE=$_GET["CallBackDatETimE"];}
	elseif (isset($_POST["CallBackDatETimE"]))	{$CallBackDatETimE=$_POST["CallBackDatETimE"];}
if (isset($_GET["recipient"]))					{$recipient=$_GET["recipient"];}
	elseif (isset($_POST["recipient"]))			{$recipient=$_POST["recipient"];}
if (isset($_GET["callback_id"]))				{$callback_id=$_GET["callback_id"];}
	elseif (isset($_POST["callback_id"]))		{$callback_id=$_POST["callback_id"];}
if (isset($_GET["use_internal_dnc"]))			{$use_internal_dnc=$_GET["use_internal_dnc"];}
	elseif (isset($_POST["use_internal_dnc"]))	{$use_internal_dnc=$_POST["use_internal_dnc"];}
if (isset($_GET["use_campaign_dnc"]))			{$use_campaign_dnc=$_GET["use_campaign_dnc"];}
	elseif (isset($_POST["use_campaign_dnc"]))	{$use_campaign_dnc=$_POST["use_campaign_dnc"];}
if (isset($_GET["omit_phone_code"]))			{$omit_phone_code=$_GET["omit_phone_code"];}
	elseif (isset($_POST["omit_phone_code"]))	{$omit_phone_code=$_POST["omit_phone_code"];}
if (isset($_GET["phone_ip"]))				{$phone_ip=$_GET["phone_ip"];}
	elseif (isset($_POST["phone_ip"]))		{$phone_ip=$_POST["phone_ip"];}
if (isset($_GET["enable_sipsak_messages"]))				{$enable_sipsak_messages=$_GET["enable_sipsak_messages"];}
	elseif (isset($_POST["enable_sipsak_messages"]))	{$enable_sipsak_messages=$_POST["enable_sipsak_messages"];}
if (isset($_GET["status"]))						{$status=$_GET["status"];}
	elseif (isset($_POST["status"]))			{$status=$_POST["status"];}
if (isset($_GET["LogouTKicKAlL"]))				{$LogouTKicKAlL=$_GET["LogouTKicKAlL"];}
	elseif (isset($_POST["LogouTKicKAlL"]))		{$LogouTKicKAlL=$_POST["LogouTKicKAlL"];}
if (isset($_GET["closer_blended"]))				{$closer_blended=$_GET["closer_blended"];}
	elseif (isset($_POST["closer_blended"]))	{$closer_blended=$_POST["closer_blended"];}
if (isset($_GET["inOUT"]))						{$inOUT=$_GET["inOUT"];}
	elseif (isset($_POST["inOUT"]))				{$inOUT=$_POST["inOUT"];}
if (isset($_GET["manual_dial_filter"]))				{$manual_dial_filter=$_GET["manual_dial_filter"];}
	elseif (isset($_POST["manual_dial_filter"]))	{$manual_dial_filter=$_POST["manual_dial_filter"];}
if (isset($_GET["alt_dial"]))					{$alt_dial=$_GET["alt_dial"];}
	elseif (isset($_POST["alt_dial"]))			{$alt_dial=$_POST["alt_dial"];}
if (isset($_GET["agentchannel"]))				{$agentchannel=$_GET["agentchannel"];}
	elseif (isset($_POST["agentchannel"]))		{$agentchannel=$_POST["agentchannel"];}
if (isset($_GET["conf_dialed"]))				{$conf_dialed=$_GET["conf_dialed"];}
	elseif (isset($_POST["conf_dialed"]))		{$conf_dialed=$_POST["conf_dialed"];}
if (isset($_GET["leaving_threeway"]))			{$leaving_threeway=$_GET["leaving_threeway"];}
	elseif (isset($_POST["leaving_threeway"]))	{$leaving_threeway=$_POST["leaving_threeway"];}
if (isset($_GET["hangup_all_non_reserved"]))			{$hangup_all_non_reserved=$_GET["hangup_all_non_reserved"];}
	elseif (isset($_POST["hangup_all_non_reserved"]))	{$hangup_all_non_reserved=$_POST["hangup_all_non_reserved"];}
if (isset($_GET["blind_transfer"]))				{$blind_transfer=$_GET["blind_transfer"];}
	elseif (isset($_POST["blind_transfer"]))	{$blind_transfer=$_POST["blind_transfer"];}
if (isset($_GET["usegroupalias"]))			{$usegroupalias=$_GET["usegroupalias"];}
	elseif (isset($_POST["usegroupalias"]))	{$usegroupalias=$_POST["usegroupalias"];}
if (isset($_GET["account"]))				{$account=$_GET["account"];}
	elseif (isset($_POST["account"]))		{$account=$_POST["account"];}
if (isset($_GET["agent_dialed_number"]))			{$agent_dialed_number=$_GET["agent_dialed_number"];}
	elseif (isset($_POST["agent_dialed_number"]))	{$agent_dialed_number=$_POST["agent_dialed_number"];}
if (isset($_GET["agent_dialed_type"]))				{$agent_dialed_type=$_GET["agent_dialed_type"];}
	elseif (isset($_POST["agent_dialed_type"]))		{$agent_dialed_type=$_POST["agent_dialed_type"];}
if (isset($_GET["wrapup"]))					{$wrapup=$_GET["wrapup"];}
	elseif (isset($_POST["wrapup"]))		{$wrapup=$_POST["wrapup"];}
if (isset($_GET["vtiger_callback_id"]))				{$vtiger_callback_id=$_GET["vtiger_callback_id"];}
	elseif (isset($_POST["vtiger_callback_id"]))	{$vtiger_callback_id=$_POST["vtiger_callback_id"];}
if (isset($_GET["dial_method"]))				{$dial_method=$_GET["dial_method"];}
	elseif (isset($_POST["dial_method"]))		{$dial_method=$_POST["dial_method"];}
if (isset($_GET["no_delete_sessions"]))				{$no_delete_sessions=$_GET["no_delete_sessions"];}
	elseif (isset($_POST["no_delete_sessions"]))	{$no_delete_sessions=$_POST["no_delete_sessions"];}
if (isset($_GET["nodeletevdac"]))				{$nodeletevdac=$_GET["nodeletevdac"];}
	elseif (isset($_POST["nodeletevdac"]))		{$nodeletevdac=$_POST["nodeletevdac"];}
if (isset($_GET["agent_territories"]))			{$agent_territories=$_GET["agent_territories"];}
	elseif (isset($_POST["agent_territories"]))	{$agent_territories=$_POST["agent_territories"];}
if (isset($_GET["alt_num_status"]))				{$alt_num_status=$_GET["alt_num_status"];}
	elseif (isset($_POST["alt_num_status"]))	{$alt_num_status=$_POST["alt_num_status"];}
if (isset($_GET["DiaL_SecondS"]))				{$DiaL_SecondS=$_GET["DiaL_SecondS"];}
	elseif (isset($_POST["DiaL_SecondS"]))		{$DiaL_SecondS=$_POST["DiaL_SecondS"];}


header ("Content-type: text/html; charset=utf-8");
header ("Cache-Control: no-cache, must-revalidate");  // HTTP/1.1
header ("Pragma: no-cache");                          // HTTP/1.0

$txt = '.txt';
$StarTtime = date("U");
$NOW_DATE = date("Y-m-d");
$NOW_TIME = date("Y-m-d H:i:s");
$CIDdate = date("mdHis");
$ENTRYdate = date("YmdHis");
$MT[0]='';
$agents='@agents';
$US='_';


##### Hangup Cause Dictionary #####
$hangup_cause_dictionary = array(
0 => "Unspecified. No other cause codes applicable.",
1 => "Unallocated (unassigned) number.",
2 => "No route to specified transit network (national use).",
3 => "No route to destination.",
6 => "Channel unacceptable.",
7 => "Call awarded, being delivered in an established channel.",
16 => "Normal call clearing.",
17 => "Användare busy.",
18 => "No user responding.",
19 => "No answer from user (user alerted).",
20 => "Subscriber absent.",
21 => "Call rejected.",
22 => "Number changed.",
23 => "Redirection to new destination.",
25 => "Exchange routing error.",
27 => "Destination out of order.",
28 => "Felaktig number format (address incomplete).",
29 => "Facilities rejected.",
30 => "Response to STATUS INQUIRY.",
31 => "Normal, unspecified.",
34 => "No circuit/channel available.",
38 => "Network out of order.",
41 => "Temporary failure.",
42 => "Switching equipment congestion.",
43 => "Access information discarded.",
44 => "Requested circuit/channel not available.",
50 => "Requested facility not subscribed.",
52 => "Outgoing calls barred.",
54 => "Inkommande calls barred.",
57 => "Bearer capability not authorized.",
58 => "Bearer capability not presently available.",
63 => "Service or option not available, unspecified.",
65 => "Bearer capability not implemented.",
66 => "Channel type not implemented.",
69 => "Requested facility not implemented.",
79 => "Service or option not implemented, unspecified.",
81 => "Felaktig call reference value.",
88 => "Incompatible destination.",
95 => "Felaktig message, unspecified.",
96 => "Mandatory information element is missing.",
97 => "Message type non-existent or not implemented.",
98 => "Message not compatible with call state or message type non-existent or not implemented.",
99 => "Information element / parameter non-existent or not implemented.",
100 => "Felaktig information element contents.",
101 => "Message not compatible with call state.",
102 => "Recovery på timer expiry.",
103 => "Parameter non-existent or not implemented - passed på (national use).",
111 => "Protocol error, unspecified.",
127 => "Interworking, unspecified."
);


#############################################
##### START SYSTEM_SETTINGS LOOKUP #####
$stmt = "SELECT use_non_latin,timeclock_end_of_day,agentonly_callback_campaign_lock FROM system_settings;";
$rslt=mysql_query($stmt, $link);
	if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00001',$user,$server_ip,$session_name,$one_mysql_log);}
if ($DB) {echo "$stmt\n";}
$qm_conf_ct = mysql_num_rows($rslt);
if ($qm_conf_ct > 0)
	{
	$row=mysql_fetch_row($rslt);
	$non_latin =							$row[0];
	$timeclock_end_of_day =					$row[1];
	$agentonly_callback_campaign_lock =		$row[2];
	}
##### END SETTINGS LOOKUP #####
###########################################

if ($non_latin < 1)
	{
	$user=ereg_replace("[^-_0-9a-zA-Z]","",$user);
	$pass=ereg_replace("[^-_0-9a-zA-Z]","",$pass);
	$length_in_sec = ereg_replace("[^0-9]","",$length_in_sec);
	$phone_code = ereg_replace("[^0-9]","",$phone_code);
	$phone_number = ereg_replace("[^0-9]","",$phone_number);
	}
else
	{
	$user = ereg_replace("'|\"|\\\\|;","",$user);
	$pass = ereg_replace("'|\"|\\\\|;","",$pass);
	}


# default optional vars if not set
if (!isset($format))   {$format="text";}
	if ($format == 'debug')	{$DB=1;}
if (!isset($ACTION))   {$ACTION="refresh";}
if (!isset($query_date)) {$query_date = $NOW_DATE;}

if ($ACTION == 'LogiNCamPaigns')
	{
	$skip_user_validation=1;
	}
else
	{
	$stmt="SELECT count(*) from vicidial_users where user='$user' and pass='$pass' and user_level > 0;";
	if ($DB) {echo "|$stmt|\n";}
	if ($non_latin > 0) {$rslt=mysql_query("SET NAMES 'UTF8'");}
	$rslt=mysql_query($stmt, $link);
		if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00002',$user,$server_ip,$session_name,$one_mysql_log);}
	$row=mysql_fetch_row($rslt);
	$auth=$row[0];

	if( (strlen($user)<2) or (strlen($pass)<2) or ($auth==0))
		{
		echo "Felaktig Användarnamn/Lösenord: |$user|$pass|\n";
		exit;
		}
	else
		{
		if( (strlen($server_ip)<6) or (!isset($server_ip)) or ( (strlen($session_name)<12) or (!isset($session_name)) ) )
			{
			echo "Felaktig server_ip: |$server_ip|  or  Felaktig session_name: |$session_name|\n";
			exit;
			}
		else
			{
			$stmt="SELECT count(*) from web_client_sessions where session_name='$session_name' and server_ip='$server_ip';";
			if ($DB) {echo "|$stmt|\n";}
			$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00003',$user,$server_ip,$session_name,$one_mysql_log);}
			$row=mysql_fetch_row($rslt);
			$SNauth=$row[0];
			  if($SNauth==0)
				{
				echo "Felaktig session_name: |$session_name|$server_ip|\n";
				exit;
				}
			  else
				{
				# do nothing for now
				}
			}
		}
	}

if ($format=='debug')
	{
	echo "<html>\n";
	echo "<head>\n";
	echo "<!-- VERSION: $version     SKAPA: $build    USER: $user   server_ip: $server_ip-->\n";
	echo "<title>VICIDiaL Databas frågeskript";
	echo "</title>\n";
	echo "</head>\n";
	echo "<BODY BGCOLOR=white marginheight=0 marginwidth=0 leftmargin=0 topmargin=0>\n";
	}



################################################################################
### LogiNCamPaigns - generates an HTML SELECT list of allowed campaigns for a 
###                  specific agent on the login screen
################################################################################
if ($ACTION == 'LogiNCamPaigns')
	{
	if ( (strlen($user)<1) )
		{
		echo "<select size=1 name=VD_campaign id=VD_campaign onFocus=\"login_allowable_campaigns()\">\n";
		echo "<option value=\"\">-- ERROR --</option>\n";
		echo "</select>\n";
		exit;
		}
	else
		{
		$stmt="SELECT user_group,user_level,agent_shift_enforcement_override,shift_override_flag from vicidial_users where user='$user' and pass='$pass'";
		if ($non_latin > 0) {$rslt=mysql_query("SET NAMES 'UTF8'");}
		$rslt=mysql_query($stmt, $link);
			if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00004',$user,$server_ip,$session_name,$one_mysql_log);}
		$row=mysql_fetch_row($rslt);
		$VU_user_group =						$row[0];
		$VU_user_level =						$row[1];
		$VU_agent_shift_enforcement_override =	$row[2];
		$VU_shift_override_flag =				$row[3];

		$LOGallowed_campaignsSQL='';

		$stmt="SELECT allowed_campaigns,forced_timeclock_login,shift_enforcement,group_shifts from vicidial_user_groups where user_group='$VU_user_group';";
		$rslt=mysql_query($stmt, $link);
			if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00005',$user,$server_ip,$session_name,$one_mysql_log);}
		$row=mysql_fetch_row($rslt);
		$forced_timeclock_login =	$row[1];
		$shift_enforcement =		$row[2];
		$LOGgroup_shiftsSQL = eregi_replace('  ','',$row[3]);
		$LOGgroup_shiftsSQL = eregi_replace(' ',"','",$LOGgroup_shiftsSQL);
		$LOGgroup_shiftsSQL = "shift_id IN('$LOGgroup_shiftsSQL')";
		if ( (!eregi("ALL-CAMPAIGNS",$row[0])) )
			{
			$LOGallowed_campaignsSQL = eregi_replace(' -','',$row[0]);
			$LOGallowed_campaignsSQL = eregi_replace(' ',"','",$LOGallowed_campaignsSQL);
			$LOGallowed_campaignsSQL = "and campaign_id IN('$LOGallowed_campaignsSQL')";
			}

		$show_campaign_list=1;
		### CHECK TO SEE IF AGENT IS LOGGED IN TO TIMECLOCK, IF NOT, OUTPUT ERROR
		if ( (ereg('Y',$forced_timeclock_login)) or ( (ereg('ADMIN_EXEMPT',$forced_timeclock_login)) and ($VU_user_level < 8) ) )
			{
			$last_agent_event='';
			$HHMM = date("Hi");
			$HHteod = substr($timeclock_end_of_day,0,2);
			$MMteod = substr($timeclock_end_of_day,2,2);

			if ($HHMM < $timeclock_end_of_day)
				{$EoD = mktime($HHteod, $MMteod, 10, date("m"), date("d")-1, date("Y"));}
			else
				{$EoD = mktime($HHteod, $MMteod, 10, date("m"), date("d"), date("Y"));}

			$EoDdate = date("Y-m-d H:i:s", $EoD);

			##### grab timeclock logged-in time for each user #####
			$stmt="SELECT event from vicidial_timeclock_log where user='$user' and event_epoch >= '$EoD' order by timeclock_id desc limit 1;";
			if ($DB>0) {echo "|$stmt|";}
			$rslt=mysql_query($stmt, $link);
			if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00184',$user,$server_ip,$session_name,$one_mysql_log);}
			$events_to_parse = mysql_num_rows($rslt);
			if ($events_to_parse > 0)
				{
				$rowx=mysql_fetch_row($rslt);
				$last_agent_event = $rowx[0];
				}
			if ( (strlen($last_agent_event)<2) or (ereg('LOGOUT',$last_agent_event)) )
				{$show_campaign_list=0;}
			}
		}

	### CHECK TO SEE IF AGENT IS WITHIN THEIR SHIFT IF RESTRICTED, IF NOT, OUTPUT ERROR
	if ( ( (ereg("START|ALL",$shift_enforcement)) and (!ereg("OFF",$VU_agent_shift_enforcement_override)) ) or (ereg("START|ALL",$VU_agent_shift_enforcement_override)) )
		{
		$shift_ok=0;
		if ( (strlen($LOGgroup_shiftsSQL) < 3) and ($VU_shift_override_flag < 1) )
			{
			$VDdisplayMESSAGE = "<B>ERROR: Det finns inga skift definierade för din användargrupp</B>\n";
			$VDloginDISPLAY=1;
			}
		else
			{
			$HHMM = date("Hi");
			$wday = date("w");

			$stmt="SELECT shift_id,shift_start_time,shift_length,shift_weekdays from vicidial_shifts where $LOGgroup_shiftsSQL order by shift_id";
			$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00193',$user,$server_ip,$session_name,$one_mysql_log);}
			$shifts_to_print = mysql_num_rows($rslt);

			$o=0;
			while ( ($shifts_to_print > $o) and ($shift_ok < 1) )
				{
				$rowx=mysql_fetch_row($rslt);
				$shift_id =			$rowx[0];
				$shift_start_time =	$rowx[1];
				$shift_length =		$rowx[2];
				$shift_weekdays =	$rowx[3];

				if (eregi("$wday",$shift_weekdays))
					{
					$HHshift_length = substr($shift_length,0,2);
					$MMshift_length = substr($shift_length,3,2);
					$HHshift_start_time = substr($shift_start_time,0,2);
					$MMshift_start_time = substr($shift_start_time,2,2);
					$HHshift_end_time = ($HHshift_length + $HHshift_start_time);
					$MMshift_end_time = ($MMshift_length + $MMshift_start_time);
					if ($MMshift_end_time > 59)
						{
						$MMshift_end_time = ($MMshift_end_time - 60);
						$HHshift_end_time++;
						}
					if ($HHshift_end_time > 23)
						{$HHshift_end_time = ($HHshift_end_time - 24);}
					$HHshift_end_time = sprintf("%02s", $HHshift_end_time);	
					$MMshift_end_time = sprintf("%02s", $MMshift_end_time);	
					$shift_end_time = "$HHshift_end_time$MMshift_end_time";

					if ( 
						( ($HHMM >= $shift_start_time) and ($HHMM < $shift_end_time) ) or
						( ($HHMM < $shift_start_time) and ($HHMM < $shift_end_time) and ($shift_end_time <= $shift_start_time) ) or
						( ($HHMM >= $shift_start_time) and ($HHMM >= $shift_end_time) and ($shift_end_time <= $shift_start_time) )
					   )
						{$shift_ok++;}
					}
				$o++;
				}

			if ( ($shift_ok < 1) and ($VU_shift_override_flag < 1) )
				{
				$VDdisplayMESSAGE = "<B>ERROR: Du har inte behörighet att logga in utanför ditt skift</B>\n";
				$VDloginDISPLAY=1;
				}
			}
		if ($VDloginDISPLAY > 0)
			{
			$loginDATE = date("Ymd");
			$VDdisplayMESSAGE.= "<BR><BR>MANAGER OVERRIDE:<BR>\n";
			$VDdisplayMESSAGE.= "<FORM ACTION=\"$PHP_SELF\" METHOD=POST>\n";
			$VDdisplayMESSAGE.= "<INPUT TYPE=HIDDEN NAME=MGR_override VALUE=\"1\">\n";
			$VDdisplayMESSAGE.= "<INPUT TYPE=HIDDEN NAME=relogin VALUE=\"YES\">\n";
			$VDdisplayMESSAGE.= "<INPUT TYPE=HIDDEN NAME=VD_login VALUE=\"$user\">\n";
			$VDdisplayMESSAGE.= "<INPUT TYPE=HIDDEN NAME=VD_pass VALUE=\"$pass\">\n";
			$VDdisplayMESSAGE.= "ManagerLogga in: <INPUT TYPE=TEXT NAME=\"MGR_login$loginDATE\" SIZE=10 maxlength=20><br>\n";
			$VDdisplayMESSAGE.= "ManagerLösenord: <INPUT TYPE=PASSWORD NAME=\"MGR_pass$loginDATE\" SIZE=10 maxlength=20><br>\n";
			$VDdisplayMESSAGE.= "<INPUT TYPE=Submit NAME=SKICKA VALUE=SKICKA></FORM><BR><BR><BR><BR>\n";
			echo "$VDdisplayMESSAGE";
			exit;
			}
		}

	if ($show_campaign_list > 0)
		{
		$stmt="SELECT campaign_id,campaign_name from vicidial_campaigns where active='Y' $LOGallowed_campaignsSQL order by campaign_id";
		$rslt=mysql_query($stmt, $link);
			if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00006',$user,$server_ip,$session_name,$one_mysql_log);}
		$camps_to_print = mysql_num_rows($rslt);

		echo "<select size=1 name=VD_campaign id=VD_campaign>\n";
		echo "<option value=\"\">-- PLEASE SELECT A CAMPAIGN --</option>\n";

		$o=0;
		while ($camps_to_print > $o) 
			{
			$rowx=mysql_fetch_row($rslt);
			echo "<option value=\"$rowx[0]\">$rowx[0] - $rowx[1]</option>\n";
			$o++;
			}
		echo "</select>\n";
		}
	else
		{
		echo "<select size=1 name=VD_campaign id=VD_campaign onFocus=\"login_allowable_campaigns()\">\n";
		echo "<option value=\"\">-- DU MÅSTE STÄMPLA IN FÖRST --</option>\n";
		echo "</select>\n";
		}
	exit;
	}



################################################################################
### regCLOSER - update the vicidial_live_agents table to reflect the closer
###             inbound choices made upon login
################################################################################
if ($ACTION == 'regCLOSER')
	{
	$row='';   $rowx='';
	$channel_live=1;
	if ( (strlen($closer_choice)<1) || (strlen($user)<1) )
		{
		$channel_live=0;
		echo "Gruppval $closer_choice är ej giltig\n";
		exit;
		}
	else
		{
		if ($closer_blended > 0)
			{$vla_autodial = 'Y';}
		else
			{$vla_autodial = 'N';}
		if (preg_match('/INBOUND_MAN|MANUAL/',$dial_method))
			{$vla_autodial = 'N';}

		if ($closer_choice == "MGRLOCK-")
			{
			$stmt="SELECT closer_campaigns FROM vicidial_users where user='$user' LIMIT 1;";
			$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00007',$user,$server_ip,$session_name,$one_mysql_log);}
			if ($DB) {echo "$stmt\n";}
				$row=mysql_fetch_row($rslt);
				$closer_choice =$row[0];

			$stmt="UPDATE vicidial_live_agents set closer_campaigns='$closer_choice',last_state_change='$NOW_TIME',outbound_autodial='$vla_autodial' where user='$user' and server_ip='$server_ip';";
				if ($format=='debug') {echo "\n<!-- $stmt -->";}
			$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00008',$user,$server_ip,$session_name,$one_mysql_log);}
			}
		else
			{
			$stmt="UPDATE vicidial_live_agents set closer_campaigns='$closer_choice',last_state_change='$NOW_TIME',outbound_autodial='$vla_autodial' where user='$user' and server_ip='$server_ip';";
				if ($format=='debug') {echo "\n<!-- $stmt -->";}
			$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00009',$user,$server_ip,$session_name,$one_mysql_log);}

			$stmt="UPDATE vicidial_users set closer_campaigns='$closer_choice' where user='$user';";
				if ($format=='debug') {echo "\n<!-- $stmt -->";}
			$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00010',$user,$server_ip,$session_name,$one_mysql_log);}
			}

		$stmt="INSERT INTO vicidial_user_closer_log set user='$user',campaign_id='$campaign',event_date='$NOW_TIME',blended='$closer_blended',closer_campaigns='$closer_choice';";
			if ($format=='debug') {echo "\n<!-- $stmt -->";}
		$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00011',$user,$server_ip,$session_name,$one_mysql_log);}

		$stmt="DELETE FROM vicidial_live_inbound_agents where user='$user';";
			if ($format=='debug') {echo "\n<!-- $stmt -->";}
		$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00012',$user,$server_ip,$session_name,$one_mysql_log);}

		$in_groups_pre = preg_replace('/-$/','',$closer_choice);
		$in_groups = explode(" ",$in_groups_pre);
		$in_groups_ct = count($in_groups);
		$k=1;
		while ($k < $in_groups_ct)
			{
			if (strlen($in_groups[$k])>1)
				{
				$stmt="SELECT group_weight,calls_today FROM vicidial_inbound_group_agents where user='$user' and group_id='$in_groups[$k]';";
				$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00013',$user,$server_ip,$session_name,$one_mysql_log);}
				if ($DB) {echo "$stmt\n";}
				$viga_ct = mysql_num_rows($rslt);
				if ($viga_ct > 0)
					{
					$row=mysql_fetch_row($rslt);
					$group_weight = $row[0];
					$calls_today =	$row[1];
					}
				else
					{
					$group_weight = 0;
					$calls_today =	0;
					}
				$stmt="INSERT INTO vicidial_live_inbound_agents set user='$user',group_id='$in_groups[$k]',group_weight='$group_weight',calls_today='$calls_today',last_call_time='$NOW_TIME',last_call_finish='$NOW_TIME';";
					if ($format=='debug') {echo "\n<!-- $stmt -->";}
				$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00014',$user,$server_ip,$session_name,$one_mysql_log);}
				}
			$k++;
			}

		}
	echo "Closer In Gruppval $closer_choice har blivit registrerad till användare $user\n";
	}





#################################################################################
### regTERRITORY - update the vicidial_live_agents table to reflect the territory
###                choices made upon login or while paused  (agent_territories)
#################################################################################
if ($ACTION == 'regTERRITORY')
	{
	$row='';   $rowx='';
	$channel_live=1;
	if ( (strlen($agent_territories)<1) || (strlen($user)<1) )
		{
		$channel_live=0;
		echo "Territory Choice $agent_territories är ej giltig\n";
		exit;
		}
	else
		{
		if (preg_match("/^MGRLOCK/",$agent_territories))
			{
			$stmt="SELECT territory FROM vicidial_user_territories where user='$user';";
			$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00253',$user,$server_ip,$session_name,$one_mysql_log);}
			$territories_ct = mysql_num_rows($rslt);
			if ($DB) {echo "$territories_ct|$stmt\n";}
			$k=0;
			$agent_territories='';
			while ($territories_ct > $k)
				{
				$row=mysql_fetch_row($rslt);
				$agent_territories .=	" $row[0]";
				$k++;
				}
			$agent_territories .= " -";

			$stmt="UPDATE vicidial_live_agents set agent_territories='$agent_territories',last_state_change='$NOW_TIME' where user='$user' and server_ip='$server_ip';";
				if ($format=='debug') {echo "\n<!-- $stmt -->";}
			$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00254',$user,$server_ip,$session_name,$one_mysql_log);}
			}
		else
			{
			$stmt="UPDATE vicidial_live_agents set agent_territories='$agent_territories',last_state_change='$NOW_TIME' where user='$user' and server_ip='$server_ip';";
				if ($format=='debug') {echo "\n<!-- $stmt -->";}
			$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00255',$user,$server_ip,$session_name,$one_mysql_log);}
			}

		$stmt="INSERT INTO vicidial_user_territory_log set user='$user',campaign_id='$campaign',event_date='$NOW_TIME',agent_territories='$agent_territories';";
			if ($format=='debug') {echo "\n<!-- $stmt -->";}
		$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00256',$user,$server_ip,$session_name,$one_mysql_log);}
		}
	echo "Territory Choice $agent_territories har blivit registrerad till användare $user\n";
	}





################################################################################
### For every process below, lookup the current agent_log_id for the user
################################################################################
$stmt="SELECT agent_log_id from vicidial_live_agents where user='$user';";
$rslt=mysql_query($stmt, $link);
	if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00222',$user,$server_ip,$session_name,$one_mysql_log);}
$users_to_parse = mysql_num_rows($rslt);
if ($users_to_parse > 0) 
	{
	$rowx=mysql_fetch_row($rslt);
	if ($rowx[0] > 0) {$agent_log_id = $rowx[0];}
	}



################################################################################
### UpdateFields - sends current vicidial_list values for fields
################################################################################
if ($ACTION == 'UpdateFields')
	{
	$stmt="UPDATE vicidial_live_agents set external_update_fields='0',external_update_fields_data='' where user='$user';";
		if ($format=='debug') {echo "\n<!-- $stmt -->";}
	$rslt=mysql_query($stmt, $link);
			if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00276',$user,$server_ip,$session_name,$one_mysql_log);}

	$stmt="SELECT lead_id from vicidial_live_agents where user='$user';";
	$rslt=mysql_query($stmt, $link);
		if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00274',$user,$server_ip,$session_name,$one_mysql_log);}
	$vla_records = mysql_num_rows($rslt);
	if ($vla_records > 0) 
		{
		$rowx=mysql_fetch_row($rslt);
		if ($rowx[0] > 0) {$lead_id = $rowx[0];}
		##### grab the data from vicidial_list for the lead_id
		$stmt="SELECT vendor_lead_code,source_id,gmt_offset_now,phone_code,phone_number,title,first_name,middle_initial,last_name,address1,address2,address3,city,state,province,postal_code,country_code,gender,date_of_birth,alt_phone,email,security_phrase,comments,rank,owner FROM vicidial_list where lead_id='$lead_id' LIMIT 1;";
		$rslt=mysql_query($stmt, $link);
		if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00275',$user,$server_ip,$session_name,$one_mysql_log);}
		if ($DB) {echo "$stmt\n";}
		$list_lead_ct = mysql_num_rows($rslt);
		if ($list_lead_ct > 0)
			{
			$row=mysql_fetch_row($rslt);
			$vendor_id		= trim("$row[0]");
			$source_id		= trim("$row[1]");
			$gmt_offset_now	= trim("$row[2]");
			$phone_code		= trim("$row[3]");
			$phone_number	= trim("$row[4]");
			$title			= trim("$row[5]");
			$first_name		= trim("$row[6]");
			$middle_initial	= trim("$row[7]");
			$last_name		= trim("$row[8]");
			$address1		= trim("$row[9]");
			$address2		= trim("$row[10]");
			$address3		= trim("$row[11]");
			$city			= trim("$row[12]");
			$state			= trim("$row[13]");
			$province		= trim("$row[14]");
			$postal_code	= trim("$row[15]");
			$country_code	= trim("$row[16]");
			$gender			= trim("$row[17]");
			$date_of_birth	= trim("$row[18]");
			$alt_phone		= trim("$row[19]");
			$email			= trim("$row[20]");
			$security		= trim("$row[21]");
			$comments		= stripslashes(trim("$row[22]"));
			$rank			= trim("$row[23]");
			$owner			= trim("$row[24]");

			$comments = eregi_replace("\r",'',$comments);
			$comments = eregi_replace("\n",'!N',$comments);

			$LeaD_InfO  =	"GOOD\n";
			$LeaD_InfO .=	$vendor_id . "\n";
			$LeaD_InfO .=	$source_id . "\n";
			$LeaD_InfO .=	$gmt_offset_now . "\n";
			$LeaD_InfO .=	$phone_code . "\n";
			$LeaD_InfO .=	$phone_number . "\n";
			$LeaD_InfO .=	$title . "\n";
			$LeaD_InfO .=	$first_name . "\n";
			$LeaD_InfO .=	$middle_initial . "\n";
			$LeaD_InfO .=	$last_name . "\n";
			$LeaD_InfO .=	$address1 . "\n";
			$LeaD_InfO .=	$address2 . "\n";
			$LeaD_InfO .=	$address3 . "\n";
			$LeaD_InfO .=	$city . "\n";
			$LeaD_InfO .=	$state . "\n";
			$LeaD_InfO .=	$province . "\n";
			$LeaD_InfO .=	$postal_code . "\n";
			$LeaD_InfO .=	$country_code . "\n";
			$LeaD_InfO .=	$gender . "\n";
			$LeaD_InfO .=	$date_of_birth . "\n";
			$LeaD_InfO .=	$alt_phone . "\n";
			$LeaD_InfO .=	$email . "\n";
			$LeaD_InfO .=	$security . "\n";
			$LeaD_InfO .=	$comments . "\n";
			$LeaD_InfO .=	$rank . "\n";
			$LeaD_InfO .=	$owner . "\n";
			$LeaD_InfO .=	"\n";

			echo $LeaD_InfO;
			}
		else
			{
			echo "ERROR: no lead info in system: $lead_id\n";
			}
		}
	else
		{
		echo "ERROR: no lead active for this agent\n";
		}
	}


################################################################################
### manDiaLnextCaLL - for manual VICIDiaL dialing this will grab the next lead
###                   in the campaign, reserve it, send data back to client and
###                   place the call by inserting into vicidial_manager
################################################################################
if ($ACTION == 'manDiaLnextCaLL')
	{
	$MT[0]='';
	$row='';   $rowx='';
	$channel_live=1;
	if ( (strlen($conf_exten)<1) || (strlen($campaign)<1)  || (strlen($ext_context)<1) )
		{
		$channel_live=0;
		echo "HOPPERN ÄR TOM!\n";
		echo "Conf Exten $conf_exten or campaign $campaign or ext_context $ext_context är ej giltig\n";
		exit;
		}
	else
	{
	##### grab number of calls today in this campaign and increment
	$stmt="SELECT calls_today FROM vicidial_live_agents WHERE user='$user' and campaign_id='$campaign';";
	$rslt=mysql_query($stmt, $link);
		if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00015',$user,$server_ip,$session_name,$one_mysql_log);}
	if ($DB) {echo "$stmt\n";}
	$vla_cc_ct = mysql_num_rows($rslt);
	if ($vla_cc_ct > 0)
		{
		$row=mysql_fetch_row($rslt);
		$calls_today = $row[0];
		}
	else
		{$calls_today ='0';}
	$calls_today++;

	$script_recording_delay=0;
	##### find if script contains recording fields
	$stmt="SELECT count(*) FROM vicidial_scripts vs,vicidial_campaigns vc WHERE campaign_id='$campaign' and vs.script_id=vc.campaign_script and script_text LIKE \"%--A--recording_%\";";
	$rslt=mysql_query($stmt, $link);
		if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00257',$user,$server_ip,$session_name,$one_mysql_log);}
	if ($DB) {echo "$stmt\n";}
	$vs_vc_ct = mysql_num_rows($rslt);
	if ($vs_vc_ct > 0)
		{
		$row=mysql_fetch_row($rslt);
		$script_recording_delay = $row[0];
		}

	### check if this is a callback, if it is, skip the grabbing of a new lead and mark the callback as INACTIVE
	if ( (strlen($callback_id)>0) and (strlen($lead_id)>0) )
		{
		$affected_rows=1;
		$CBleadIDset=1;

		$stmt = "UPDATE vicidial_callbacks set status='INACTIVE' where callback_id='$callback_id';";
		if ($DB) {echo "$stmt\n";}
		$rslt=mysql_query($stmt, $link);
			if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00016',$user,$server_ip,$session_name,$one_mysql_log);}
		}
	else
		{
		if (strlen($phone_number)>3)
			{
			if (ereg("DNC",$manual_dial_filter))
				{
				$stmt="SELECT count(*) FROM vicidial_dnc where phone_number='$phone_number';";
				$rslt=mysql_query($stmt, $link);
			if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00017',$user,$server_ip,$session_name,$one_mysql_log);}
				if ($DB) {echo "$stmt\n";}
				$row=mysql_fetch_row($rslt);
				
				if ($row[0] > 0)
					{
					echo "DNC NUNNER\n";
					exit;
					}
				$stmt="SELECT count(*) FROM vicidial_campaign_dnc where phone_number='$phone_number' and campaign_id='$campaign';";
				$rslt=mysql_query($stmt, $link);
			if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00018',$user,$server_ip,$session_name,$one_mysql_log);}
				if ($DB) {echo "$stmt\n";}
				$row=mysql_fetch_row($rslt);
				
				if ($row[0] > 0)
					{
					echo "DNC NUNNER\n";
					exit;
					}
				}
			if (ereg("CAMPLISTS",$manual_dial_filter))
				{
				$stmt="SELECT list_id,active from vicidial_lists where campaign_id='$campaign'";
				$rslt=mysql_query($stmt, $link);
			if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00019',$user,$server_ip,$session_name,$one_mysql_log);}
				$lists_to_parse = mysql_num_rows($rslt);
				$camp_lists='';
				$o=0;
				while ($lists_to_parse > $o) 
					{
					$rowx=mysql_fetch_row($rslt);
					if (ereg("Y", $rowx[1])) {$active_lists++;   $camp_lists .= "'$rowx[0]',";}
					if (ereg("N", $rowx[1])) {$inactive_lists++;}
					$o++;
					}
				$camp_lists = eregi_replace(".$","",$camp_lists);

				$stmt="SELECT count(*) FROM vicidial_list where phone_number='$phone_number' and list_id IN($camp_lists);";
				$rslt=mysql_query($stmt, $link);
			if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00020',$user,$server_ip,$session_name,$one_mysql_log);}
				if ($DB) {echo "$stmt\n";}
				$row=mysql_fetch_row($rslt);
				
				if ($row[0] < 1)
					{
					echo "NUNNER NOT IN CAMPLISTS\n";
					exit;
					}
				}
			if ($stage=='lookup')
				{
				if (strlen($vendor_lead_code)>0)
					{
					$stmt="SELECT lead_id FROM vicidial_list where vendor_lead_code='$vendor_lead_code' order by modify_date desc LIMIT 1;";
					$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00021',$user,$server_ip,$session_name,$one_mysql_log);}
					if ($DB) {echo "$stmt\n";}
					$man_leadID_ct = mysql_num_rows($rslt);
					if ( ($man_leadID_ct > 0) and (strlen($phone_number) > 5) )
						{$override_phone++;}
					}
				else
					{
					$stmt="SELECT lead_id FROM vicidial_list where phone_number='$phone_number' order by modify_date desc LIMIT 1;";
					$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00021',$user,$server_ip,$session_name,$one_mysql_log);}
					if ($DB) {echo "$stmt\n";}
					$man_leadID_ct = mysql_num_rows($rslt);
					}
				if ($man_leadID_ct > 0)
					{
					$row=mysql_fetch_row($rslt);
					$affected_rows=1;
					$lead_id =$row[0];
					$CBleadIDset=1;
					}
				else
					{
					### insert a new lead in the system with this phone number
					$stmt = "INSERT INTO vicidial_list SET phone_code='$phone_code',phone_number='$phone_number',list_id='$list_id',status='QUEUE',user='$user',called_since_last_reset='Y',entry_date='$ENTRYdate',last_local_call_time='$NOW_TIME',vendor_lead_code='$vendor_lead_code';";
					if ($DB) {echo "$stmt\n";}
					$rslt=mysql_query($stmt, $link);
			if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00022',$user,$server_ip,$session_name,$one_mysql_log);}
					$affected_rows = mysql_affected_rows($link);
					$lead_id = mysql_insert_id($link);
					$CBleadIDset=1;
					}
				}
			else
				{
				### insert a new lead in the system with this phone number
				$stmt = "INSERT INTO vicidial_list SET phone_code='$phone_code',phone_number='$phone_number',list_id='$list_id',status='QUEUE',user='$user',called_since_last_reset='Y',entry_date='$ENTRYdate',last_local_call_time='$NOW_TIME';";
				if ($DB) {echo "$stmt\n";}
				$rslt=mysql_query($stmt, $link);
			if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00023',$user,$server_ip,$session_name,$one_mysql_log);}
				$affected_rows = mysql_affected_rows($link);
				$lead_id = mysql_insert_id($link);
				$CBleadIDset=1;
				}
			}
		else
			{
			##### gather no hopper dialing settings from campaign
			$stmt="SELECT no_hopper_dialing,agent_dial_owner_only,local_call_time,dial_statuses,drop_lockout_time,lead_filter_id,lead_order FROM vicidial_campaigns where campaign_id='$campaign';";
			$rslt=mysql_query($stmt, $link);
			if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00236',$user,$server_ip,$session_name,$one_mysql_log);}
			if ($DB) {echo "$stmt\n";}
			$camp_nohopper_ct = mysql_num_rows($rslt);
			if ($camp_nohopper_ct > 0)
				{
				$row=mysql_fetch_row($rslt);
				$no_hopper_dialing =		$row[0];
				$agent_dial_owner_only =	$row[1];
				$local_call_time =			$row[2];
				$dial_statuses =			$row[3];
				$drop_lockout_time =		$row[4];
				$lead_filter_id =			$row[5];
				$lead_order =				$row[6];
				}
			if (eregi("N",$no_hopper_dialing))
				{
				### grab the next lead in the hopper for this campaign and reserve it for the user
				$stmt = "UPDATE vicidial_hopper set status='QUEUE', user='$user' where campaign_id='$campaign' and status='READY' order by priority desc,hopper_id LIMIT 1";
				if ($DB) {echo "$stmt\n";}
				$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00024',$user,$server_ip,$session_name,$one_mysql_log);}
				$affected_rows = mysql_affected_rows($link);
				}
			else
				{
				### figure out what the next lead that should be dialed is

				##########################################################
				### BEGIN find the next lead to dial without looking in the hopper
				##########################################################
			#	$DB=1;
				if (strlen($dial_statuses)>2)
					{
					$g=0;
					$p='13';
					$GMT_gmt[0] = '';
					$GMT_hour[0] = '';
					$GMT_day[0] = '';
					while ($p > -13)
						{
						$pzone=3600 * $p;
						$pmin=(gmdate("i", time() + $pzone));
						$phour=( (gmdate("G", time() + $pzone)) * 100);
						$pday=gmdate("w", time() + $pzone);
						$tz = sprintf("%.2f", $p);	
						$GMT_gmt[$g] = "$tz";
						$GMT_day[$g] = "$pday";
						$GMT_hour[$g] = ($phour + $pmin);
						$p = ($p - 0.25);
						$g++;
						}

					$stmt="SELECT call_time_id,call_time_name,call_time_comments,ct_default_start,ct_default_stop,ct_sunday_start,ct_sunday_stop,ct_monday_start,ct_monday_stop,ct_tuesday_start,ct_tuesday_stop,ct_wednesday_start,ct_wednesday_stop,ct_thursday_start,ct_thursday_stop,ct_friday_start,ct_friday_stop,ct_saturday_start,ct_saturday_stop,ct_state_call_times FROM vicidial_call_times where call_time_id='$local_call_time';";
					if ($DB) {echo "$stmt\n";}
					$rslt=mysql_query($stmt, $link);
					if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00237',$user,$server_ip,$session_name,$one_mysql_log);}
					$rowx=mysql_fetch_row($rslt);
					$Gct_default_start =	"$rowx[3]";
					$Gct_default_stop =		"$rowx[4]";
					$Gct_sunday_start =		"$rowx[5]";
					$Gct_sunday_stop =		"$rowx[6]";
					$Gct_monday_start =		"$rowx[7]";
					$Gct_monday_stop =		"$rowx[8]";
					$Gct_tuesday_start =	"$rowx[9]";
					$Gct_tuesday_stop =		"$rowx[10]";
					$Gct_wednesday_start =	"$rowx[11]";
					$Gct_wednesday_stop =	"$rowx[12]";
					$Gct_thursday_start =	"$rowx[13]";
					$Gct_thursday_stop =	"$rowx[14]";
					$Gct_friday_start =		"$rowx[15]";
					$Gct_friday_stop =		"$rowx[16]";
					$Gct_saturday_start =	"$rowx[17]";
					$Gct_saturday_stop =	"$rowx[18]";
					$Gct_state_call_times = "$rowx[19]";

					$ct_states = '';
					$ct_state_gmt_SQL = '';
					$ct_srs=0;
					$b=0;
					if (strlen($Gct_state_call_times)>2)
						{
						$state_rules = explode('|',$Gct_state_call_times);
						$ct_srs = ((count($state_rules)) - 2);
						}
					while($ct_srs >= $b)
						{
						if (strlen($state_rules[$b])>1)
							{
							$stmt="SELECT state_call_time_id,state_call_time_state,state_call_time_name,state_call_time_comments,sct_default_start,sct_default_stop,sct_sunday_start,sct_sunday_stop,sct_monday_start,sct_monday_stop,sct_tuesday_start,sct_tuesday_stop,sct_wednesday_start,sct_wednesday_stop,sct_thursday_start,sct_thursday_stop,sct_friday_start,sct_friday_stop,sct_saturday_start,sct_saturday_stop from vicidial_state_call_times where state_call_time_id='$state_rules[$b]';";
							$rslt=mysql_query($stmt, $link);
							if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00238',$user,$server_ip,$session_name,$one_mysql_log);}
							$row=mysql_fetch_row($rslt);
							$Gstate_call_time_id =		"$row[0]";
							$Gstate_call_time_state =	"$row[1]";
							$Gsct_default_start =		"$row[4]";
							$Gsct_default_stop =		"$row[5]";
							$Gsct_sunday_start =		"$row[6]";
							$Gsct_sunday_stop =			"$row[7]";
							$Gsct_monday_start =		"$row[8]";
							$Gsct_monday_stop =			"$row[9]";
							$Gsct_tuesday_start =		"$row[10]";
							$Gsct_tuesday_stop =		"$row[11]";
							$Gsct_wednesday_start =		"$row[12]";
							$Gsct_wednesday_stop =		"$row[13]";
							$Gsct_thursday_start =		"$row[14]";
							$Gsct_thursday_stop =		"$row[15]";
							$Gsct_friday_start =		"$row[16]";
							$Gsct_friday_stop =			"$row[17]";
							$Gsct_saturday_start =		"$row[18]";
							$Gsct_saturday_stop =		"$row[19]";

							$ct_states .="'$Gstate_call_time_state',";

							$r=0;
							$state_gmt='';
							while($r < $g)
								{
								if ($GMT_day[$r]==0)	#### Sunday local time
									{
									if (($Gsct_sunday_start==0) and ($Gsct_sunday_stop==0))
										{
										if ( ($GMT_hour[$r]>=$Gsct_default_start) and ($GMT_hour[$r]<$Gsct_default_stop) )
											{$state_gmt.="'$GMT_gmt[$r]',";}
										}
									else
										{
										if ( ($GMT_hour[$r]>=$Gsct_sunday_start) and ($GMT_hour[$r]<$Gsct_sunday_stop) )
											{$state_gmt.="'$GMT_gmt[$r]',";}
										}
									}
								if ($GMT_day[$r]==1)	#### Monday local time
									{
									if (($Gsct_monday_start==0) and ($Gsct_monday_stop==0))
										{
										if ( ($GMT_hour[$r]>=$Gsct_default_start) and ($GMT_hour[$r]<$Gsct_default_stop) )
											{$state_gmt.="'$GMT_gmt[$r]',";}
										}
									else
										{
										if ( ($GMT_hour[$r]>=$Gsct_monday_start) and ($GMT_hour[$r]<$Gsct_monday_stop) )
											{$state_gmt.="'$GMT_gmt[$r]',";}
										}
									}
								if ($GMT_day[$r]==2)	#### Tuesday local time
									{
									if (($Gsct_tuesday_start==0) and ($Gsct_tuesday_stop==0))
										{
										if ( ($GMT_hour[$r]>=$Gsct_default_start) and ($GMT_hour[$r]<$Gsct_default_stop) )
											{$state_gmt.="'$GMT_gmt[$r]',";}
										}
									else
										{
										if ( ($GMT_hour[$r]>=$Gsct_tuesday_start) and ($GMT_hour[$r]<$Gsct_tuesday_stop) )
											{$state_gmt.="'$GMT_gmt[$r]',";}
										}
									}
								if ($GMT_day[$r]==3)	#### Wednesday local time
									{
									if (($Gsct_wednesday_start==0) and ($Gsct_wednesday_stop==0))
										{
										if ( ($GMT_hour[$r]>=$Gsct_default_start) and ($GMT_hour[$r]<$Gsct_default_stop) )
											{$state_gmt.="'$GMT_gmt[$r]',";}
										}
									else
										{
										if ( ($GMT_hour[$r]>=$Gsct_wednesday_start) and ($GMT_hour[$r]<$Gsct_wednesday_stop) )
											{$state_gmt.="'$GMT_gmt[$r]',";}
										}
									}
								if ($GMT_day[$r]==4)	#### Thursday local time
									{
									if (($Gsct_thursday_start==0) and ($Gsct_thursday_stop==0))
										{
										if ( ($GMT_hour[$r]>=$Gsct_default_start) and ($GMT_hour[$r]<$Gsct_default_stop) )
											{$state_gmt.="'$GMT_gmt[$r]',";}
										}
									else
										{
										if ( ($GMT_hour[$r]>=$Gsct_thursday_start) and ($GMT_hour[$r]<$Gsct_thursday_stop) )
											{$state_gmt.="'$GMT_gmt[$r]',";}
										}
									}
								if ($GMT_day[$r]==5)	#### Friday local time
									{
									if (($Gsct_friday_start==0) and ($Gsct_friday_stop==0))
										{
										if ( ($GMT_hour[$r]>=$Gsct_default_start) and ($GMT_hour[$r]<$Gsct_default_stop) )
											{$state_gmt.="'$GMT_gmt[$r]',";}
										}
									else
										{
										if ( ($GMT_hour[$r]>=$Gsct_friday_start) and ($GMT_hour[$r]<$Gsct_friday_stop) )
											{$state_gmt.="'$GMT_gmt[$r]',";}
										}
									}
								if ($GMT_day[$r]==6)	#### Saturday local time
									{
									if (($Gsct_saturday_start==0) and ($Gsct_saturday_stop==0))
										{
										if ( ($GMT_hour[$r]>=$Gsct_default_start) and ($GMT_hour[$r]<$Gsct_default_stop) )
											{$state_gmt.="'$GMT_gmt[$r]',";}
										}
									else
										{
										if ( ($GMT_hour[$r]>=$Gsct_saturday_start) and ($GMT_hour[$r]<$Gsct_saturday_stop) )
											{$state_gmt.="'$GMT_gmt[$r]',";}
										}
									}
								$r++;
								}
							$state_gmt = "$state_gmt'99'";
							$ct_state_gmt_SQL .= "or (state='$Gstate_call_time_state' and gmt_offset_now IN($state_gmt)) ";
							}

						$b++;
						}
					if (strlen($ct_states)>2)
						{
						$ct_states = eregi_replace(",$",'',$ct_states);
						$ct_statesSQL = "and state NOT IN($ct_states)";
						}
					else
						{
						$ct_statesSQL = "";
						}

					$r=0;
					$default_gmt='';
					while($r < $g)
						{
						if ($GMT_day[$r]==0)	#### Sunday local time
							{
							if (($Gct_sunday_start==0) and ($Gct_sunday_stop==0))
								{
								if ( ($GMT_hour[$r]>=$Gct_default_start) and ($GMT_hour[$r]<$Gct_default_stop) )
									{$default_gmt.="'$GMT_gmt[$r]',";}
								}
							else
								{
								if ( ($GMT_hour[$r]>=$Gct_sunday_start) and ($GMT_hour[$r]<$Gct_sunday_stop) )
									{$default_gmt.="'$GMT_gmt[$r]',";}
								}
							}
						if ($GMT_day[$r]==1)	#### Monday local time
							{
							if (($Gct_monday_start==0) and ($Gct_monday_stop==0))
								{
								if ( ($GMT_hour[$r]>=$Gct_default_start) and ($GMT_hour[$r]<$Gct_default_stop) )
									{$default_gmt.="'$GMT_gmt[$r]',";}
								}
							else
								{
								if ( ($GMT_hour[$r]>=$Gct_monday_start) and ($GMT_hour[$r]<$Gct_monday_stop) )
									{$default_gmt.="'$GMT_gmt[$r]',";}
								}
							}
						if ($GMT_day[$r]==2)	#### Tuesday local time
							{
							if (($Gct_tuesday_start==0) and ($Gct_tuesday_stop==0))
								{
								if ( ($GMT_hour[$r]>=$Gct_default_start) and ($GMT_hour[$r]<$Gct_default_stop) )
									{$default_gmt.="'$GMT_gmt[$r]',";}
								}
							else
								{
								if ( ($GMT_hour[$r]>=$Gct_tuesday_start) and ($GMT_hour[$r]<$Gct_tuesday_stop) )
									{$default_gmt.="'$GMT_gmt[$r]',";}
								}
							}
						if ($GMT_day[$r]==3)	#### Wednesday local time
							{
							if (($Gct_wednesday_start==0) and ($Gct_wednesday_stop==0))
								{
								if ( ($GMT_hour[$r]>=$Gct_default_start) and ($GMT_hour[$r]<$Gct_default_stop) )
									{$default_gmt.="'$GMT_gmt[$r]',";}
								}
							else
								{
								if ( ($GMT_hour[$r]>=$Gct_wednesday_start) and ($GMT_hour[$r]<$Gct_wednesday_stop) )
									{$default_gmt.="'$GMT_gmt[$r]',";}
								}
							}
						if ($GMT_day[$r]==4)	#### Thursday local time
							{
							if (($Gct_thursday_start==0) and ($Gct_thursday_stop==0))
								{
								if ( ($GMT_hour[$r]>=$Gct_default_start) and ($GMT_hour[$r]<$Gct_default_stop) )
									{$default_gmt.="'$GMT_gmt[$r]',";}
								}
							else
								{
								if ( ($GMT_hour[$r]>=$Gct_thursday_start) and ($GMT_hour[$r]<$Gct_thursday_stop) )
									{$default_gmt.="'$GMT_gmt[$r]',";}
								}
							}
						if ($GMT_day[$r]==5)	#### Friday local time
							{
							if (($Gct_friday_start==0) and ($Gct_friday_stop==0))
								{
								if ( ($GMT_hour[$r]>=$Gct_default_start) and ($GMT_hour[$r]<$Gct_default_stop) )
									{$default_gmt.="'$GMT_gmt[$r]',";}
								}
							else
								{
								if ( ($GMT_hour[$r]>=$Gct_friday_start) and ($GMT_hour[$r]<$Gct_friday_stop) )
									{$default_gmt.="'$GMT_gmt[$r]',";}
								}
							}
						if ($GMT_day[$r]==6)	#### Saturday local time
							{
							if (($Gct_saturday_start==0) and ($Gct_saturday_stop==0))
								{
								if ( ($GMT_hour[$r]>=$Gct_default_start) and ($GMT_hour[$r]<$Gct_default_stop) )
									{$default_gmt.="'$GMT_gmt[$r]',";}
								}
							else
								{
								if ( ($GMT_hour[$r]>=$Gct_saturday_start) and ($GMT_hour[$r]<$Gct_saturday_stop) )
									{$default_gmt.="'$GMT_gmt[$r]',";}
								}
							}
						$r++;
						}

					$default_gmt = "$default_gmt'99'";
					$all_gmtSQL = "(gmt_offset_now IN($default_gmt) $ct_statesSQL) $ct_state_gmt_SQL";

					$dial_statuses = preg_replace("/ -$/","",$dial_statuses);
					$Dstatuses = explode(" ", $dial_statuses);
					$Ds_to_print = (count($Dstatuses) - 0);
					$Dsql = '';
					$o=0;
					while ($Ds_to_print > $o) 
						{
						$o++;
						$Dsql .= "'$Dstatuses[$o]',";
						}
					$Dsql = preg_replace("/,$/","",$Dsql);
					if (strlen($Dsql) < 2) {$Dsql = "''";}

					$DLTsql='';
					if ($drop_lockout_time > 0)
						{
						$DLseconds = ($drop_lockout_time * 3600);
						$DLseconds = floor($DLseconds);
						$DLseconds = intval("$DLseconds");
						$DLTsql = "and ( ( (status IN('DROP','XDROP')) and (last_local_call_time < CONCAT(DATE_ADD(NOW(), INTERVAL -$DLseconds SECOND),' ',CURTIME()) ) ) or (status NOT IN('DROP','XDROP')) )";
						}

					$stmt="SELECT lead_filter_sql FROM vicidial_lead_filters where lead_filter_id='$lead_filter_id';";
					$rslt=mysql_query($stmt, $link);
					if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00239',$user,$server_ip,$session_name,$one_mysql_log);}
					$filtersql_ct = mysql_num_rows($rslt);
					if ($DB) {echo "$filtersql_ct|$stmt\n";}
					if ($filtersql_ct > 0)
						{
						$row=mysql_fetch_row($rslt);
						$fSQL =		$row[0];
						}

					$stmt="SELECT list_id FROM vicidial_lists where campaign_id='$campaign' and active='Y';";
					$rslt=mysql_query($stmt, $link);
					if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00240',$user,$server_ip,$session_name,$one_mysql_log);}
					$camplists_ct = mysql_num_rows($rslt);
					if ($DB) {echo "$camplists_ct|$stmt\n";}
					$k=0;
					$camp_lists='';
					while ($camplists_ct > $k)
						{
						$row=mysql_fetch_row($rslt);
						$camp_lists .=	"'$row[0]',";
						$k++;
						}
					$camp_lists = eregi_replace(".$","",$camp_lists);
					if (strlen($camp_lists) < 4) {$camp_lists="''";}

					$stmt="SELECT user_group,territory FROM vicidial_users where user='$user';";
					$rslt=mysql_query($stmt, $link);
					if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00241',$user,$server_ip,$session_name,$one_mysql_log);}
					$userterr_ct = mysql_num_rows($rslt);
					if ($DB) {echo "$userterr_ct|$stmt\n";}
					if ($userterr_ct > 0)
						{
						$row=mysql_fetch_row($rslt);
						$user_group =	$row[0];
						$territory =	$row[1];
						}

					$agent_territories='';
					$stmt="SELECT agent_territories FROM vicidial_live_agents where user='$user';";
					$rslt=mysql_query($stmt, $link);
					if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00258',$user,$server_ip,$session_name,$one_mysql_log);}
					$userterrVLA_ct = mysql_num_rows($rslt);
					if ($DB) {echo "$userterrVLA_ct|$stmt\n";}
					if ($userterrVLA_ct > 0)
						{
						$row=mysql_fetch_row($rslt);
						$agent_territories =	$row[0];
						}
					if (strlen($agent_territories) > 3)
						{
						$agent_territoriesSQL = preg_replace('/-$/','',$agent_territories);
						$agent_territoriesSQL = preg_replace('/ $|^ /','',$agent_territoriesSQL);
						$territory = preg_replace('/ /',"','",$agent_territoriesSQL);
						}

					$adooSQL = '';
					if (eregi("USER",$agent_dial_owner_only)) {$adooSQL = "and owner='$user'";}
					if (eregi("TERRITORY",$agent_dial_owner_only)) {$adooSQL = "and owner IN('$territory')";}
					if (eregi("USER_GROUP",$agent_dial_owner_only)) {$adooSQL = "and owner='$user_group'";}

					$order_stmt = '';
					if (eregi("DOWN",$lead_order)){$order_stmt = 'order by lead_id asc';}
					if (eregi("UP",$lead_order)){$order_stmt = 'order by lead_id desc';}
					if (eregi("UP LAST NAME",$lead_order)){$order_stmt = 'order by last_name desc, lead_id asc';}
					if (eregi("DOWN LAST NAME",$lead_order)){$order_stmt = 'order by last_name, lead_id asc';}
					if (eregi("UP PHONE",$lead_order)){$order_stmt = 'order by phone_number desc, lead_id asc';}
					if (eregi("DOWN PHONE",$lead_order)){$order_stmt = 'order by phone_number, lead_id asc';}
					if (eregi("UP COUNT",$lead_order)){$order_stmt = 'order by called_count desc, lead_id asc';}
					if (eregi("DOWN COUNT",$lead_order)){$order_stmt = 'order by called_count, lead_id asc';}
					if (eregi("UP LAST SAMTAL TIME",$lead_order)){$order_stmt = 'order by last_local_call_time desc, lead_id asc';}
					if (eregi("DOWN LAST SAMTAL TIME",$lead_order)){$order_stmt = 'order by last_local_call_time, lead_id asc';}
					if (eregi("RANDOM",$lead_order)){$order_stmt = 'order by RAND()';}
					if (eregi("UP RANK",$lead_order)){$order_stmt = 'order by rank desc, lead_id asc';}
					if (eregi("DOWN RANK",$lead_order)){$order_stmt = 'order by rank, lead_id asc';}
					if (eregi("UP OWNER",$lead_order)){$order_stmt = 'order by owner desc, lead_id asc';}
					if (eregi("DOWN OWNER",$lead_order)){$order_stmt = 'order by owner, lead_id asc';}
					if (eregi("UP TIMEZONE",$lead_order)){$order_stmt = 'order by gmt_offset_now desc, lead_id asc';}
					if (eregi("DOWN TIMEZONE",$lead_order)){$order_stmt = 'order by gmt_offset_now, lead_id asc';}

					$stmt="UPDATE vicidial_list SET status='QUEUE',user='$user' where called_since_last_reset='N' and status IN($Dsql) and list_id IN($camp_lists) and ($all_gmtSQL) $DLTsql $fSQL $adooSQL $order_stmt LIMIT 1;";
					if ($DB) {echo "$stmt\n";}
					$rslt=mysql_query($stmt, $link);
					if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00242',$user,$server_ip,$session_name,$one_mysql_log);}
					$affected_rows = mysql_affected_rows($link);

					if ($affected_rows > 0)
						{
						$stmt="SELECT lead_id,list_id,gmt_offset_now,state FROM vicidial_list where status='QUEUE' and user='$user' order by modify_date desc LIMIT 1;";
						$rslt=mysql_query($stmt, $link);
						if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00243',$user,$server_ip,$session_name,$one_mysql_log);}
						if ($DB) {echo "$stmt\n";}
						$leadpick_ct = mysql_num_rows($rslt);
						if ($leadpick_ct > 0)
							{
							$row=mysql_fetch_row($rslt);
							$lead_id =			$row[0];
							$list_id =			$row[1];
							$gmt_offset_now =	$row[2];
							$state =			$row[3];

							$stmt = "INSERT INTO vicidial_hopper SET lead_id='$lead_id',campaign_id='$campaign',status='QUEUE',list_id='$list_id',gmt_offset_now='$gmt_offset_now',state='$state',alt_dial='MAIN',user='$user',priority='0';";
							if ($DB) {echo "$stmt\n";}
							$rslt=mysql_query($stmt, $link);
							if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00244',$user,$server_ip,$session_name,$one_mysql_log);}
							}
						}
					}
				##########################################################
				### END  find the next lead to dial without looking in the hopper
				##########################################################
			#	$DB=0;
				}
			}
		}

	if ($affected_rows > 0)
		{
		if (!$CBleadIDset)
			{
			##### grab the lead_id of the reserved user in vicidial_hopper
			$stmt="SELECT lead_id FROM vicidial_hopper where campaign_id='$campaign' and status='QUEUE' and user='$user' LIMIT 1;";
			$rslt=mysql_query($stmt, $link);
			if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00025',$user,$server_ip,$session_name,$one_mysql_log);}
			if ($DB) {echo "$stmt\n";}
			$hopper_leadID_ct = mysql_num_rows($rslt);
			if ($hopper_leadID_ct > 0)
				{
				$row=mysql_fetch_row($rslt);
				$lead_id =$row[0];
				}
			}

			##### grab the data from vicidial_list for the lead_id
			$stmt="SELECT lead_id,entry_date,modify_date,status,user,vendor_lead_code,source_id,list_id,gmt_offset_now,called_since_last_reset,phone_code,phone_number,title,first_name,middle_initial,last_name,address1,address2,address3,city,state,province,postal_code,country_code,gender,date_of_birth,alt_phone,email,security_phrase,comments,called_count,last_local_call_time,rank,owner FROM vicidial_list where lead_id='$lead_id' LIMIT 1;";
			$rslt=mysql_query($stmt, $link);
			if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00026',$user,$server_ip,$session_name,$one_mysql_log);}
			if ($DB) {echo "$stmt\n";}
			$list_lead_ct = mysql_num_rows($rslt);
			if ($list_lead_ct > 0)
				{
				$row=mysql_fetch_row($rslt);
			#	$lead_id		= trim("$row[0]");
				$dispo			= trim("$row[3]");
				$tsr			= trim("$row[4]");
				$vendor_id		= trim("$row[5]");
				$source_id		= trim("$row[6]");
				$list_id		= trim("$row[7]");
				$gmt_offset_now	= trim("$row[8]");
				$called_since_last_reset = trim("$row[9]");
				$phone_code		= trim("$row[10]");
				if ($override_phone < 1)
					{$phone_number	= trim("$row[11]");}
				$title			= trim("$row[12]");
				$first_name		= trim("$row[13]");
				$middle_initial	= trim("$row[14]");
				$last_name		= trim("$row[15]");
				$address1		= trim("$row[16]");
				$address2		= trim("$row[17]");
				$address3		= trim("$row[18]");
				$city			= trim("$row[19]");
				$state			= trim("$row[20]");
				$province		= trim("$row[21]");
				$postal_code	= trim("$row[22]");
				$country_code	= trim("$row[23]");
				$gender			= trim("$row[24]");
				$date_of_birth	= trim("$row[25]");
				$alt_phone		= trim("$row[26]");
				$email			= trim("$row[27]");
				$security		= trim("$row[28]");
				$comments		= stripslashes(trim("$row[29]"));
				$called_count	= trim("$row[30]");
				$rank			= trim("$row[32]");
				$owner			= trim("$row[33]");
				}

			$called_count++;

			##### check if system is set to generate logfile for transfers
			$stmt="SELECT enable_agc_xfer_log FROM system_settings;";
			$rslt=mysql_query($stmt, $link);
			if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00027',$user,$server_ip,$session_name,$one_mysql_log);}
			if ($DB) {echo "$stmt\n";}
			$enable_agc_xfer_log_ct = mysql_num_rows($rslt);
			if ($enable_agc_xfer_log_ct > 0)
				{
				$row=mysql_fetch_row($rslt);
				$enable_agc_xfer_log =$row[0];
				}

			if ( ($WeBRooTWritablE > 0) and ($enable_agc_xfer_log > 0) )
				{
				# generate callerID for unique identifier in xfer_log file
				$PADlead_id = sprintf("%09s", $lead_id);
					while (strlen($PADlead_id) > 9) {$PADlead_id = substr("$PADlead_id", 0, -1);}
				# Create unique calleridname to track the call: MmmddhhmmssLLLLLLLLL
					$MqueryCID = "M$CIDdate$PADlead_id";

				#	DATETIME|campaign|lead_id|phone_number|user|type
				#	2007-08-22 11:11:11|TESTCAMP|65432|3125551212|1234|M
				$fp = fopen ("./xfer_log.txt", "a");
				fwrite ($fp, "$NOW_TIME|$campaign|$lead_id|$phone_number|$user|M|$MqueryCID||$province\n");
				fclose($fp);
				}

			##### if lead is a callback, grab the callback comments
			$CBentry_time =		'';
			$CBcallback_time =	'';
			$CBuser =			'';
			$CBcomments =		'';
			if (ereg("CALLBK",$dispo))
				{
				$stmt="SELECT entry_time,callback_time,user,comments FROM vicidial_callbacks where lead_id='$lead_id' order by callback_id desc LIMIT 1;";
				$rslt=mysql_query($stmt, $link);
			if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00028',$user,$server_ip,$session_name,$one_mysql_log);}
				if ($DB) {echo "$stmt\n";}
				$cb_record_ct = mysql_num_rows($rslt);
				if ($cb_record_ct > 0)
					{
					$row=mysql_fetch_row($rslt);
					$CBentry_time =		trim("$row[0]");
					$CBcallback_time =	trim("$row[1]");
					$CBuser =			trim("$row[2]");
					$CBcomments =		trim("$row[3]");
					}
				}

			$stmt = "SELECT local_gmt FROM servers where active='Y' limit 1;";
			$rslt=mysql_query($stmt, $link);
			if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00029',$user,$server_ip,$session_name,$one_mysql_log);}
			if ($DB) {echo "$stmt\n";}
			$server_ct = mysql_num_rows($rslt);
			if ($server_ct > 0)
				{
				$row=mysql_fetch_row($rslt);
				$local_gmt =	$row[0];
				}
			$LLCT_DATE_offset = ($local_gmt - $gmt_offset_now);
			$LLCT_DATE = date("Y-m-d H:i:s", mktime(date("H")-$LLCT_DATE_offset,date("i"),date("s"),date("m"),date("d"),date("Y")));

			if (ereg('Y',$called_since_last_reset))
				{
				$called_since_last_reset = ereg_replace('Y','',$called_since_last_reset);
				if (strlen($called_since_last_reset) < 1) {$called_since_last_reset = 0;}
				$called_since_last_reset++;
				$called_since_last_reset = "Y$called_since_last_reset";
				}
			else {$called_since_last_reset = 'Y';}
			### flag the lead as called and change it's status to INCALL
			$stmt = "UPDATE vicidial_list set status='INCALL', called_since_last_reset='$called_since_last_reset', called_count='$called_count',user='$user',last_local_call_time='$LLCT_DATE' where lead_id='$lead_id';";
			if ($DB) {echo "$stmt\n";}
			$rslt=mysql_query($stmt, $link);
			if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00030',$user,$server_ip,$session_name,$one_mysql_log);}

			if (!$CBleadIDset)
				{
				### delete the lead from the hopper
				$stmt = "DELETE FROM vicidial_hopper where lead_id='$lead_id';";
				if ($DB) {echo "$stmt\n";}
				$rslt=mysql_query($stmt, $link);
			if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00031',$user,$server_ip,$session_name,$one_mysql_log);}
				}

			$stmt="UPDATE vicidial_agent_log set lead_id='$lead_id',comments='MANUAL' where agent_log_id='$agent_log_id';";
				if ($format=='debug') {echo "\n<!-- $stmt -->";}
			$rslt=mysql_query($stmt, $link);
			if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00032',$user,$server_ip,$session_name,$one_mysql_log);}
		
			### if preview dialing, do not send the call	
			if ( (strlen($preview)<1) || ($preview == 'NO') )
				{
				### prepare variables to place manual call from VICIDiaL
				$CCID_on=0;   $CCID='';
				$local_DEF = 'Local/';
				$local_AMP = '@';
				$Local_out_prefix = '9';
				$Local_dial_timeout = '60';
			#	$Local_persist = '/n';
                                $Local_persist = '';
				if ($dial_timeout > 4) {$Local_dial_timeout = $dial_timeout;}
				$Local_dial_timeout = ($Local_dial_timeout * 1000);
				if (strlen($dial_prefix) > 0) {$Local_out_prefix = "$dial_prefix";}
				if (strlen($campaign_cid) > 6) {$CCID = "$campaign_cid";   $CCID_on++;}
				$campaign_cid_override='';
				### check if there is a list_id override
				if (strlen($list_id) > 1)
					{
					$stmt = "SELECT campaign_cid_override FROM vicidial_lists where list_id='$list_id';";
					$rslt=mysql_query($stmt, $link);
					if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00245',$user,$server_ip,$session_name,$one_mysql_log);}
					if ($DB) {echo "$stmt\n";}
					$lio_ct = mysql_num_rows($rslt);
					if ($lio_ct > 0)
						{
						$row=mysql_fetch_row($rslt);
						$campaign_cid_override =	$row[0];
						}
					}
				if (strlen($campaign_cid_override) > 6) {$CCID = "$campaign_cid_override";   $CCID_on++;}
				if (eregi("x",$dial_prefix)) {$Local_out_prefix = '';}

				$PADlead_id = sprintf("%09s", $lead_id);
					while (strlen($PADlead_id) > 9) {$PADlead_id = substr("$PADlead_id", 0, -1);}

				# Create unique calleridname to track the call: MmmddhhmmssLLLLLLLLL
					$MqueryCID = "M$CIDdate$PADlead_id";
				if ($CCID_on) {$CIDstring = "\"$MqueryCID\" <$CCID>";}
				else {$CIDstring = "$MqueryCID";}

				### whether to omit phone_code or not
				if (eregi('Y',$omit_phone_code)) 
					{$Ndialstring = "$Local_out_prefix$phone_number";}
				else
					{$Ndialstring = "$Local_out_prefix$phone_code$phone_number";}

				if ( ($usegroupalias > 0) and (strlen($account)>1) )
					{
					$RAWaccount = $account;
					$account = "Account: $account";
					$variable = "Variable: usegroupalias=1";
					}
				else
					{$account='';   $variable='';}

				### insert the call action into the vicidial_manager table to initiate the call
				#	$stmt = "INSERT INTO vicidial_manager values('','','$NOW_TIME','NEW','N','$server_ip','','Originate','$MqueryCID','Exten: $conf_exten','Context: $ext_context','Channel: $local_DEF$Local_out_prefix$phone_code$phone_number$local_AMP$ext_context','Priority: 1','Callerid: $CIDstring','Timeout: $Local_dial_timeout','','','','');";
				$stmt = "INSERT INTO vicidial_manager values('','','$NOW_TIME','NEW','N','$server_ip','','Originate','$MqueryCID','Exten: $Ndialstring','Context: $ext_context','Channel: $local_DEF$conf_exten$local_AMP$ext_context$Local_persist','Priority: 1','Callerid: $CIDstring','Timeout: $Local_dial_timeout','$account','$variable','','');";
				if ($DB) {echo "$stmt\n";}
				$rslt=mysql_query($stmt, $link);
			if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00033',$user,$server_ip,$session_name,$one_mysql_log);}

				$stmt = "INSERT INTO vicidial_auto_calls (server_ip,campaign_id,status,lead_id,callerid,phone_code,phone_number,call_time,call_type) values('$server_ip','$campaign','XFER','$lead_id','$MqueryCID','$phone_code','$phone_number','$NOW_TIME','OUT')";
				if ($DB) {echo "$stmt\n";}
				$rslt=mysql_query($stmt, $link);
			if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00034',$user,$server_ip,$session_name,$one_mysql_log);}

				### update the agent status to INCALL in vicidial_live_agents
				$stmt = "UPDATE vicidial_live_agents set status='INCALL',last_call_time='$NOW_TIME',callerid='$MqueryCID',lead_id='$lead_id',comments='MANUAL',calls_today='$calls_today',external_hangup=0,external_status='',external_pause='',external_dial='',last_state_change='$NOW_TIME' where user='$user' and server_ip='$server_ip';";
				if ($DB) {echo "$stmt\n";}
				$rslt=mysql_query($stmt, $link);
			if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00035',$user,$server_ip,$session_name,$one_mysql_log);}

				### update calls_today count in vicidial_campaign_agents
				$stmt = "UPDATE vicidial_campaign_agents set calls_today='$calls_today' where user='$user' and campaign_id='$campaign';";
				if ($DB) {echo "$stmt\n";}
				$rslt=mysql_query($stmt, $link);
			if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00036',$user,$server_ip,$session_name,$one_mysql_log);}

		#		#### update vicidial_agent_log if not MANUAL dial_method
		#		if ($dial_method != 'MANUAL')
		#			{
		#			$pause_sec=0;
		#			$stmt = "select pause_epoch,pause_sec,wait_epoch,talk_epoch,dispo_epoch,agent_log_id from vicidial_agent_log where agent_log_id >= '$agent_log_id' and user='$user' order by agent_log_id desc limit 1;";
		#			if ($DB) {echo "$stmt\n";}
		#			$rslt=mysql_query($stmt, $link);
		#					if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00302',$user,$server_ip,$session_name,$one_mysql_log);}
		#			$VDpr_ct = mysql_num_rows($rslt);
		#			if ( ($VDpr_ct > 0) and (strlen($row[3]<5)) and (strlen($row[4]<5)) )
		#				{
		#				$row=mysql_fetch_row($rslt);
		#				$agent_log_id = $row[5];
		#				$pause_sec = (($StarTtime - $row[0]) + $row[1]);
		#
		#				$stmt="UPDATE vicidial_agent_log set pause_sec='$pause_sec',wait_epoch='$StarTtime' where agent_log_id='$agent_log_id';";
		#					if ($format=='debug') {echo "\n<!-- $stmt -->";}
		#				$rslt=mysql_query($stmt, $link);
		#					if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00303',$user,$server_ip,$session_name,$one_mysql_log);}
		#				}
		#			}


				$val_pause_epoch=0;
				$val_pause_sec=0;
				$stmt = "SELECT pause_epoch FROM vicidial_agent_log where agent_log_id='$agent_log_id';";
				$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00XXX',$user,$server_ip,$session_name,$one_mysql_log);}
				if ($DB) {echo "$stmt\n";}
				$vald_ct = mysql_num_rows($rslt);
				if ($vald_ct > 0)
					{
					$row=mysql_fetch_row($rslt);
					$val_pause_epoch =	$row[0];
					$val_pause_sec = ($StarTtime - $val_pause_epoch);
					}

				$stmt="UPDATE vicidial_agent_log set pause_sec='$val_pause_sec',wait_epoch='$StarTtime' where agent_log_id='$agent_log_id';";
					if ($format=='debug') {echo "\n<!-- $stmt -->";}
				$rslt=mysql_query($stmt, $link);
					if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00XXX',$user,$server_ip,$session_name,$one_mysql_log);}


				if ($agent_dialed_number > 0)
					{
					$stmt = "INSERT INTO user_call_log (user,call_date,call_type,server_ip,phone_number,number_dialed,lead_id,callerid,group_alias_id) values('$user','$NOW_TIME','$agent_dialed_type','$server_ip','$phone_number','$Ndialstring','$lead_id','$CCID','$RAWaccount')";
					if ($DB) {echo "$stmt\n";}
					$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00191',$user,$server_ip,$session_name,$one_mysql_log);}
					}

				#############################################
				##### START QUEUEMETRICS LOGGING LOOKUP #####
				$stmt = "SELECT enable_queuemetrics_logging,queuemetrics_server_ip,queuemetrics_dbname,queuemetrics_login,queuemetrics_pass,queuemetrics_log_id FROM system_settings;";
				$rslt=mysql_query($stmt, $link);
			if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00037',$user,$server_ip,$session_name,$one_mysql_log);}
				if ($DB) {echo "$stmt\n";}
				$qm_conf_ct = mysql_num_rows($rslt);
				if ($qm_conf_ct > 0)
					{
					$row=mysql_fetch_row($rslt);
					$enable_queuemetrics_logging =	$row[0];
					$queuemetrics_server_ip	=		$row[1];
					$queuemetrics_dbname =			$row[2];
					$queuemetrics_login	=			$row[3];
					$queuemetrics_pass =			$row[4];
					$queuemetrics_log_id =			$row[5];
					}
				##### END QUEUEMETRICS LOGGING LOOKUP #####
				###########################################
				if ($enable_queuemetrics_logging > 0)
					{
					$linkB=mysql_connect("$queuemetrics_server_ip", "$queuemetrics_login", "$queuemetrics_pass");
					mysql_select_db("$queuemetrics_dbname", $linkB);

					# UNPAUSEALL
					$stmt = "INSERT INTO queue_log SET partition='P01',time_id='$StarTtime',call_id='NONE',queue='NONE',agent='Agent/$user',verb='UNPAUSEALL',serverid='$queuemetrics_log_id';";
					if ($DB) {echo "$stmt\n";}
					$rslt=mysql_query($stmt, $linkB);
			if ($mel > 0) {mysql_error_logging($NOW_TIME,$linkB,$mel,$stmt,'00038',$user,$server_ip,$session_name,$one_mysql_log);}
					$affected_rows = mysql_affected_rows($linkB);

					# SAMTALOUTBOUND (formerly ENTERQUEUE)
					$stmt = "INSERT INTO queue_log SET partition='P01',time_id='$StarTtime',call_id='$MqueryCID',queue='$campaign',agent='NONE',verb='CALLOUTBOUND',data2='$phone_number',serverid='$queuemetrics_log_id';";
					if ($DB) {echo "$stmt\n";}
					$rslt=mysql_query($stmt, $linkB);
			if ($mel > 0) {mysql_error_logging($NOW_TIME,$linkB,$mel,$stmt,'00039',$user,$server_ip,$session_name,$one_mysql_log);}
					$affected_rows = mysql_affected_rows($linkB);

					# CONNECT
					$stmt = "INSERT INTO queue_log SET partition='P01',time_id='$StarTtime',call_id='$MqueryCID',queue='$campaign',agent='Agent/$user',verb='CONNECT',data1='0',serverid='$queuemetrics_log_id';";
					if ($DB) {echo "$stmt\n";}
					$rslt=mysql_query($stmt, $linkB);
			if ($mel > 0) {mysql_error_logging($NOW_TIME,$linkB,$mel,$stmt,'00040',$user,$server_ip,$session_name,$one_mysql_log);}
					$affected_rows = mysql_affected_rows($linkB);

					mysql_close($linkB);
					}

				}

			##### find if script contains recording fields
			$stmt="SELECT count(*) FROM vicidial_lists WHERE list_id='$list_id' and agent_script_override!='' and agent_script_override IS NOT NULL and agent_script_override!='NONE';";
			$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00259',$user,$server_ip,$session_name,$one_mysql_log);}
			if ($DB) {echo "$stmt\n";}
			$vls_vc_ct = mysql_num_rows($rslt);
			if ($vls_vc_ct > 0)
				{
				$row=mysql_fetch_row($rslt);
				if ($row[0] > 0)
					{
					$script_recording_delay=0;
					##### find if script contains recording fields
					$stmt="SELECT count(*) FROM vicidial_scripts vs,vicidial_lists vls WHERE list_id='$list_id' and vs.script_id=vls.agent_script_override and script_text LIKE \"%--A--recording_%\";";
					$rslt=mysql_query($stmt, $link);
						if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00260',$user,$server_ip,$session_name,$one_mysql_log);}
					if ($DB) {echo "$stmt\n";}
					$vs_vc_ct = mysql_num_rows($rslt);
					if ($vs_vc_ct > 0)
						{
						$row=mysql_fetch_row($rslt);
						$script_recording_delay = $row[0];
						}
					}
				}

			### Check for List ID override settings
			$VDCL_xferconf_a_number='';
			$VDCL_xferconf_b_number='';
			$VDCL_xferconf_c_number='';
			$VDCL_xferconf_d_number='';
			$VDCL_xferconf_e_number='';
			$stmt = "select xferconf_a_number,xferconf_b_number,xferconf_c_number,xferconf_d_number,xferconf_e_number from vicidial_campaigns where campaign_id='$campaign';";
			if ($DB) {echo "$stmt\n";}
			$rslt=mysql_query($stmt, $link);
			if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00277',$user,$server_ip,$session_name,$one_mysql_log);}
			$VC_preset_ct = mysql_num_rows($rslt);
			if ($VC_preset_ct > 0)
				{
				$row=mysql_fetch_row($rslt);
				$VDCL_xferconf_a_number =	$row[0];
				$VDCL_xferconf_b_number =	$row[1];
				$VDCL_xferconf_c_number =	$row[2];
				$VDCL_xferconf_d_number =	$row[3];
				$VDCL_xferconf_e_number =	$row[4];
				}

			if (strlen($list_id)>0)
				{
				$stmt = "select xferconf_a_number,xferconf_b_number,xferconf_c_number,xferconf_d_number,xferconf_e_number from vicidial_lists where list_id='$list_id';";
				if ($DB) {echo "$stmt\n";}
				$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00278',$user,$server_ip,$session_name,$one_mysql_log);}
				$VDIG_preset_ct = mysql_num_rows($rslt);
				if ($VDIG_preset_ct > 0)
					{
					$row=mysql_fetch_row($rslt);
					if (strlen($row[0]) > 0)
						{$VDCL_xferconf_a_number =	$row[0];}
					if (strlen($row[1]) > 0)
						{$VDCL_xferconf_b_number =	$row[1];}
					if (strlen($row[2]) > 0)
						{$VDCL_xferconf_c_number =	$row[2];}
					if (strlen($row[3]) > 0)
						{$VDCL_xferconf_d_number =	$row[3];}
					if (strlen($row[4]) > 0)
						{$VDCL_xferconf_e_number =	$row[4];}
					}
				}


			$comments = eregi_replace("\r",'',$comments);
			$comments = eregi_replace("\n",'!N',$comments);

			$LeaD_InfO =	$MqueryCID . "\n";
			$LeaD_InfO .=	$lead_id . "\n";
			$LeaD_InfO .=	$dispo . "\n";
			$LeaD_InfO .=	$tsr . "\n";
			$LeaD_InfO .=	$vendor_id . "\n";
			$LeaD_InfO .=	$list_id . "\n";
			$LeaD_InfO .=	$gmt_offset_now . "\n";
			$LeaD_InfO .=	$phone_code . "\n";
			$LeaD_InfO .=	$phone_number . "\n";
			$LeaD_InfO .=	$title . "\n";
			$LeaD_InfO .=	$first_name . "\n";
			$LeaD_InfO .=	$middle_initial . "\n";
			$LeaD_InfO .=	$last_name . "\n";
			$LeaD_InfO .=	$address1 . "\n";
			$LeaD_InfO .=	$address2 . "\n";
			$LeaD_InfO .=	$address3 . "\n";
			$LeaD_InfO .=	$city . "\n";
			$LeaD_InfO .=	$state . "\n";
			$LeaD_InfO .=	$province . "\n";
			$LeaD_InfO .=	$postal_code . "\n";
			$LeaD_InfO .=	$country_code . "\n";
			$LeaD_InfO .=	$gender . "\n";
			$LeaD_InfO .=	$date_of_birth . "\n";
			$LeaD_InfO .=	$alt_phone . "\n";
			$LeaD_InfO .=	$email . "\n";
			$LeaD_InfO .=	$security . "\n";
			$LeaD_InfO .=	$comments . "\n";
			$LeaD_InfO .=	$called_count . "\n";
			$LeaD_InfO .=	$CBentry_time . "\n";
			$LeaD_InfO .=	$CBcallback_time . "\n";
			$LeaD_InfO .=	$CBuser . "\n";
			$LeaD_InfO .=	$CBcomments . "\n";
			$LeaD_InfO .=	$phone_number . "\n";
			$LeaD_InfO .=	"MAIN\n";
			$LeaD_InfO .=	$source_id . "\n";
			$LeaD_InfO .=	$rank . "\n";
			$LeaD_InfO .=	$owner . "\n";
			$LeaD_InfO .=	"\n";
			$LeaD_InfO .=	$script_recording_delay . "\n";
			$LeaD_InfO .=	$VDCL_xferconf_a_number . "\n";
			$LeaD_InfO .=	$VDCL_xferconf_b_number . "\n";
			$LeaD_InfO .=	$VDCL_xferconf_c_number . "\n";
			$LeaD_InfO .=	$VDCL_xferconf_d_number . "\n";
			$LeaD_InfO .=	$VDCL_xferconf_e_number . "\n";

			echo $LeaD_InfO;
			}
		else
			{
			echo "HOPPERN ÄR TOM!\n";
			}
		}
	}


################################################################################
### alt_phone_change - change alt phone numbers to active and inactive
### 
################################################################################
if ($ACTION == 'alt_phone_change')
{
	$MT[0]='';
	$row='';   $rowx='';
	$channel_live=1;
	if ( (strlen($stage)<1) || (strlen($called_count)<1) || (strlen($lead_id)<1)  || (strlen($phone_number)<1) )
		{
		$channel_live=0;
		echo "ALTERNATIVT NUMMER NUNNER STATUS NOT CHANGED\n";
		echo "$phone_number $stage $lead_id or $called_count är ej giltig\n";
		exit;
		}
	else
		{
		$stmt = "UPDATE vicidial_list_alt_phones set active='$stage' where lead_id='$lead_id' and phone_number='$phone_number' and alt_phone_count='$called_count';";
		if ($DB) {echo "$stmt\n";}
		$rslt=mysql_query($stmt, $link);
			if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00041',$user,$server_ip,$session_name,$one_mysql_log);}

		echo "ALTERNATIVT NUMMER NUNNER STATUS CHANGED\n";
		}
}


################################################################################
### AlertControl - change the agent alert setting in vicidial_users
### 
################################################################################
if ($ACTION == 'AlertControl')
{
	if (strlen($stage)<1)
		{
		$channel_live=0;
		echo "AGENT ALERT SETTING NOT CHANGED\n";
		echo "$stage är ej giltig\n";
		exit;
		}
	else
		{
		if (ereg('ON',$stage)) {$stage = '1';}
		else {$stage = '0';}

		$stmt = "UPDATE vicidial_users set alert_enabled='$stage' where user='$user' and pass='$pass';";
		if ($DB) {echo "$stmt\n";}
		$rslt=mysql_query($stmt, $link);
			if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'000185',$user,$server_ip,$session_name,$one_mysql_log);}

		echo "AGENT ALERT SETTING CHANGED $stage\n";
		}
}


################################################################################
### manDiaLskip - for manual VICIDiaL dialing this skips the lead that was
###               previewed in the step above and puts it back in orig status
################################################################################
if ($ACTION == 'manDiaLskip')
	{
	$MT[0]='';
	$row='';   $rowx='';
	$channel_live=1;
	if ( (strlen($stage)<1) || (strlen($called_count)<1) || (strlen($lead_id)<1) )
		{
		$channel_live=0;
		echo "LEAD NOT REVERTED\n";
		echo "Conf Exten $conf_exten or campaign $campaign or ext_context $ext_context är ej giltig\n";
		exit;
		}
	else
		{
		$called_count = ($called_count - 1);
		### flag the lead as called and change it's status to INCALL
		$stmt = "UPDATE vicidial_list set status='$stage', called_count='$called_count',user='$user' where lead_id='$lead_id';";
		if ($DB) {echo "$stmt\n";}
		$rslt=mysql_query($stmt, $link);
			if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00042',$user,$server_ip,$session_name,$one_mysql_log);}


		echo "LEAD REVERTED\n";
		}
	}


################################################################################
### manDiaLonly - for manual VICIDiaL dialing this sends the call that was
###               previewed in the step above
################################################################################
if ($ACTION == 'manDiaLonly')
	{
	$MT[0]='';
	$row='';   $rowx='';
	$channel_live=1;
	if ( (strlen($conf_exten)<1) || (strlen($campaign)<1) || (strlen($ext_context)<1) || (strlen($phone_number)<1) || (strlen($lead_id)<1) )
		{
		$channel_live=0;
		echo " SAMTAL NOT PLACED\n";
		echo "Conf Exten $conf_exten or campaign $campaign or ext_context $ext_context är ej giltig\n";
		exit;
		}
	else
		{
		##### grab number of calls today in this campaign and increment
		$stmt="SELECT calls_today FROM vicidial_live_agents WHERE user='$user' and campaign_id='$campaign';";
		$rslt=mysql_query($stmt, $link);
			if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00043',$user,$server_ip,$session_name,$one_mysql_log);}
		if ($DB) {echo "$stmt\n";}
		$vla_cc_ct = mysql_num_rows($rslt);
		if ($vla_cc_ct > 0)
			{
			$row=mysql_fetch_row($rslt);
			$calls_today =$row[0];
			}
		else
			{$calls_today ='0';}
		$calls_today++;

		### prepare variables to place manual call from VICIDiaL
		$CCID_on=0;   $CCID='';
		$local_DEF = 'Local/';
		$local_AMP = '@';
		$Local_out_prefix = '9';
		$Local_dial_timeout = '60';
		$Local_persist = '/n';
		if ($dial_timeout > 4) {$Local_dial_timeout = $dial_timeout;}
		$Local_dial_timeout = ($Local_dial_timeout * 1000);
		if (strlen($dial_prefix) > 0) {$Local_out_prefix = "$dial_prefix";}
		if (strlen($campaign_cid) > 6) {$CCID = "$campaign_cid";   $CCID_on++;}
		if (eregi("x",$dial_prefix)) {$Local_out_prefix = '';}
		$campaign_cid_override='';
		### check if there is a list_id override
		if (strlen($lead_id) > 1)
			{
			$list_id='';
			$stmt = "SELECT list_id FROM vicidial_list where lead_id='$lead_id';";
			$rslt=mysql_query($stmt, $link);
			if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00246',$user,$server_ip,$session_name,$one_mysql_log);}
			if ($DB) {echo "$stmt\n";}
			$lio_ct = mysql_num_rows($rslt);
			if ($lio_ct > 0)
				{
				$row=mysql_fetch_row($rslt);
				$list_id =	$row[0];

				if (strlen($list_id) > 1)
					{
					$stmt = "SELECT campaign_cid_override FROM vicidial_lists where list_id='$list_id';";
					$rslt=mysql_query($stmt, $link);
					if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00247',$user,$server_ip,$session_name,$one_mysql_log);}
					if ($DB) {echo "$stmt\n";}
					$lio_ct = mysql_num_rows($rslt);
					if ($lio_ct > 0)
						{
						$row=mysql_fetch_row($rslt);
						$campaign_cid_override =	$row[0];
						}
					}
				}
			}
		if (strlen($campaign_cid_override) > 6) {$CCID = "$campaign_cid_override";   $CCID_on++;}

		$PADlead_id = sprintf("%09s", $lead_id);
			while (strlen($PADlead_id) > 9) {$PADlead_id = substr("$PADlead_id", 0, -1);}

		# Create unique calleridname to track the call: MmmddhhmmssLLLLLLLLL
			$MqueryCID = "M$CIDdate$PADlead_id";
		if ($CCID_on) {$CIDstring = "\"$MqueryCID\" <$CCID>";}
		else {$CIDstring = "$MqueryCID";}

		if ( ($usegroupalias > 0) and (strlen($account)>1) )
			{
			$RAWaccount = $account;
			$account = "Account: $account";
			$variable = "Variable: usegroupalias=1";
			}
		else
			{$account='';   $variable='';}

		### whether to omit phone_code or not
		if (eregi('Y',$omit_phone_code)) 
			{$Ndialstring = "$Local_out_prefix$phone_number";}
		else
			{$Ndialstring = "$Local_out_prefix$phone_code$phone_number";}
		### insert the call action into the vicidial_manager table to initiate the call
		#	$stmt = "INSERT INTO vicidial_manager values('','','$NOW_TIME','NEW','N','$server_ip','','Originate','$MqueryCID','Exten: $conf_exten','Context: $ext_context','Channel: $local_DEF$Local_out_prefix$phone_code$phone_number$local_AMP$ext_context','Priority: 1','Callerid: $CIDstring','Timeout: $Local_dial_timeout','','','','');";
		$stmt = "INSERT INTO vicidial_manager values('','','$NOW_TIME','NEW','N','$server_ip','','Originate','$MqueryCID','Exten: $Ndialstring','Context: $ext_context','Channel: $local_DEF$conf_exten$local_AMP$ext_context$Local_persist','Priority: 1','Callerid: $CIDstring','Timeout: $Local_dial_timeout','$account','$variable','','');";
		if ($DB) {echo "$stmt\n";}
		$rslt=mysql_query($stmt, $link);
			if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00044',$user,$server_ip,$session_name,$one_mysql_log);}

		$stmt = "INSERT INTO vicidial_auto_calls (server_ip,campaign_id,status,lead_id,callerid,phone_code,phone_number,call_time,call_type) values('$server_ip','$campaign','XFER','$lead_id','$MqueryCID','$phone_code','$phone_number','$NOW_TIME','OUT')";
		if ($DB) {echo "$stmt\n";}
		$rslt=mysql_query($stmt, $link);
			if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00045',$user,$server_ip,$session_name,$one_mysql_log);}

		### update the agent status to INCALL in vicidial_live_agents
		$stmt = "UPDATE vicidial_live_agents set status='INCALL',last_call_time='$NOW_TIME',callerid='$MqueryCID',lead_id='$lead_id',comments='MANUAL',calls_today='$calls_today',external_hangup=0,external_status='',external_pause='',external_dial='',last_state_change='$NOW_TIME' where user='$user' and server_ip='$server_ip';";
		if ($DB) {echo "$stmt\n";}
		$rslt=mysql_query($stmt, $link);
			if ($mel > 0) {$errno = mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00046',$user,$server_ip,$session_name,$one_mysql_log);}
		$retry_count=0;
		while ( ($errno > 0) and ($retry_count < 9) )
			{
			$rslt=mysql_query($stmt, $link);
			$one_mysql_log=1;
			$errno = mysql_error_logging($NOW_TIME,$link,$mel,$stmt,"9046$retry_count",$user,$server_ip,$session_name,$one_mysql_log);
			$one_mysql_log=0;
			$retry_count++;
			}

		$stmt = "UPDATE vicidial_campaign_agents set calls_today='$calls_today' where user='$user' and campaign_id='$campaign';";
		if ($DB) {echo "$stmt\n";}
		$rslt=mysql_query($stmt, $link);
			if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00047',$user,$server_ip,$session_name,$one_mysql_log);}

		echo "$MqueryCID\n";

#		#### update vicidial_agent_log if not MANUAL dial_method
#		if ($dial_method != 'MANUAL')
#			{
#			$pause_sec=0;
#			$stmt = "SELECT pause_epoch,pause_sec,wait_epoch,talk_epoch,dispo_epoch,agent_log_id from vicidial_agent_log where agent_log_id >= '$agent_log_id' and user='$user' order by agent_log_id desc limit 1;";
#			if ($DB) {echo "$stmt\n";}
#			$rslt=mysql_query($stmt, $link);
#					if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00304',$user,$server_ip,$session_name,$one_mysql_log);}
#			$VDpr_ct = mysql_num_rows($rslt);
#			if ( ($VDpr_ct > 0) and (strlen($row[3]<5)) and (strlen($row[4]<5)) )
#				{
#				$row=mysql_fetch_row($rslt);
#				$agent_log_id = $row[5];
#				$pause_sec = (($StarTtime - $row[0]) + $row[1]);
#
#				$stmt="UPDATE vicidial_agent_log set pause_sec='$pause_sec',wait_epoch='$StarTtime' where agent_log_id='$agent_log_id';";
#					if ($format=='debug') {echo "\n<!-- $stmt -->";}
#				$rslt=mysql_query($stmt, $link);
#					if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00305',$user,$server_ip,$session_name,$one_mysql_log);}
#				}
#			}

		$val_pause_epoch=0;
		$val_pause_sec=0;
		$val_dispo_epoch=0;
		$val_dispo_sec=0;
		$val_wait_epoch=0;
		$val_wait_sec=0;
		$stmt = "SELECT dispo_epoch,wait_epoch,pause_epoch FROM vicidial_agent_log where agent_log_id='$agent_log_id';";
		$rslt=mysql_query($stmt, $link);
		if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00XXX',$user,$server_ip,$session_name,$one_mysql_log);}
		if ($DB) {echo "$stmt\n";}
		$vald_ct = mysql_num_rows($rslt);
		if ($vald_ct > 0)
			{
			$row=mysql_fetch_row($rslt);
			$val_dispo_epoch =	$row[0];
			$val_wait_epoch =	$row[1];
			$val_pause_epoch =	$row[2];
			$val_dispo_sec = ($StarTtime - $val_dispo_epoch);
			$val_wait_sec = ($StarTtime - $val_wait_epoch);
			$val_pause_sec = ($StarTtime - $val_pause_epoch);
			}
		if ($val_dispo_epoch > 1000)
			{
			$stmt="UPDATE vicidial_agent_log set status='ALTNUM',dispo_sec='$val_dispo_sec' where agent_log_id='$agent_log_id';";
				if ($format=='debug') {echo "\n<!-- $stmt -->";}
			$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00XXX',$user,$server_ip,$session_name,$one_mysql_log);}

			$user_group='';
			$stmt="SELECT user_group FROM vicidial_users where user='$user' LIMIT 1;";
			$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00XXX',$user,$server_ip,$session_name,$one_mysql_log);}
			if ($DB) {echo "$stmt\n";}
			$ug_record_ct = mysql_num_rows($rslt);
			if ($ug_record_ct > 0)
				{
				$row=mysql_fetch_row($rslt);
				$user_group =		trim("$row[0]");
				}

			$stmt="INSERT INTO vicidial_agent_log (user,server_ip,event_time,campaign_id,pause_epoch,pause_sec,wait_epoch,user_group,sub_status) values('$user','$server_ip','$NOW_TIME','$campaign','$StarTtime','0','$StarTtime','$user_group','ANDIAL');";
			if ($DB) {echo "$stmt\n";}
			$rslt=mysql_query($stmt, $link);
					if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00XXX',$user,$server_ip,$session_name,$one_mysql_log);}
			$affected_rows = mysql_affected_rows($link);
			$agent_log_id = mysql_insert_id($link);

			$stmt="UPDATE vicidial_live_agents SET agent_log_id='$agent_log_id',last_state_change='$NOW_TIME' where user='$user';";
			if ($DB) {echo "$stmt\n";}
			$rslt=mysql_query($stmt, $link);
					if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00XXX',$VD_login,$server_ip,$session_name,$one_mysql_log);}
			$VLAaffected_rows_update = mysql_affected_rows($link);
			}
		else
			{
			$stmt="UPDATE vicidial_agent_log set pause_sec='$val_pause_sec',wait_epoch='$StarTtime' where agent_log_id='$agent_log_id';";
				if ($format=='debug') {echo "\n<!-- $stmt -->";}
			$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00XXX',$user,$server_ip,$session_name,$one_mysql_log);}
			}

		echo "$agent_log_id\n";


		if ($agent_dialed_number > 0)
			{
			$stmt = "INSERT INTO user_call_log (user,call_date,call_type,server_ip,phone_number,number_dialed,lead_id,callerid,group_alias_id) values('$user','$NOW_TIME','$agent_dialed_type','$server_ip','$phone_number','$Ndialstring','$lead_id','$CCID','$RAWaccount')";
			if ($DB) {echo "$stmt\n";}
			$rslt=mysql_query($stmt, $link);
		if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00192',$user,$server_ip,$session_name,$one_mysql_log);}
			}


		#############################################
		##### START QUEUEMETRICS LOGGING LOOKUP #####
		$stmt = "SELECT enable_queuemetrics_logging,queuemetrics_server_ip,queuemetrics_dbname,queuemetrics_login,queuemetrics_pass,queuemetrics_log_id FROM system_settings;";
		$rslt=mysql_query($stmt, $link);
			if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00048',$user,$server_ip,$session_name,$one_mysql_log);}
		if ($DB) {echo "$stmt\n";}
		$qm_conf_ct = mysql_num_rows($rslt);
		if ($qm_conf_ct > 0)
			{
			$row=mysql_fetch_row($rslt);
			$enable_queuemetrics_logging =	$row[0];
			$queuemetrics_server_ip	=		$row[1];
			$queuemetrics_dbname =			$row[2];
			$queuemetrics_login	=			$row[3];
			$queuemetrics_pass =			$row[4];
			$queuemetrics_log_id =			$row[5];
			}
		##### END QUEUEMETRICS LOGGING LOOKUP #####
		###########################################
		if ($enable_queuemetrics_logging > 0)
			{
			$linkB=mysql_connect("$queuemetrics_server_ip", "$queuemetrics_login", "$queuemetrics_pass");
			mysql_select_db("$queuemetrics_dbname", $linkB);

			# UNPAUSEALL
			$stmt = "INSERT INTO queue_log SET partition='P01',time_id='$StarTtime',call_id='NONE',queue='NONE',agent='Agent/$user',verb='UNPAUSEALL',serverid='$queuemetrics_log_id';";
			if ($DB) {echo "$stmt\n";}
			$rslt=mysql_query($stmt, $linkB);
			if ($mel > 0) {mysql_error_logging($NOW_TIME,$linkB,$mel,$stmt,'00049',$user,$server_ip,$session_name,$one_mysql_log);}
			$affected_rows = mysql_affected_rows($linkB);

			# SAMTALOUTBOUND (formerly ENTERQUEUE)
			$stmt = "INSERT INTO queue_log SET partition='P01',time_id='$StarTtime',call_id='$MqueryCID',queue='$campaign',agent='NONE',verb='CALLOUTBOUND',data2='$phone_number',serverid='$queuemetrics_log_id';";
			if ($DB) {echo "$stmt\n";}
			$rslt=mysql_query($stmt, $linkB);
			if ($mel > 0) {mysql_error_logging($NOW_TIME,$linkB,$mel,$stmt,'00050',$user,$server_ip,$session_name,$one_mysql_log);}
			$affected_rows = mysql_affected_rows($linkB);

			# CONNECT
			$stmt = "INSERT INTO queue_log SET partition='P01',time_id='$StarTtime',call_id='$MqueryCID',queue='$campaign',agent='Agent/$user',verb='CONNECT',data1='0',serverid='$queuemetrics_log_id';";
			if ($DB) {echo "$stmt\n";}
			$rslt=mysql_query($stmt, $linkB);
			if ($mel > 0) {mysql_error_logging($NOW_TIME,$linkB,$mel,$stmt,'00051',$user,$server_ip,$session_name,$one_mysql_log);}
			$affected_rows = mysql_affected_rows($linkB);

			mysql_close($linkB);
			}

		}
	}


################################################################################
### manDiaLlookCaLL - for manual VICIDiaL dialing this will attempt to look up
###                   the trunk channel that the call was placed on
################################################################################
if ($ACTION == 'manDiaLlookCaLL')
	{
	$MT[0]='';
	$row='';   $rowx='';
	$call_good=0;
	if (strlen($MDnextCID)<18)
		{
		echo "NO\n";
		echo "MDnextCID $MDnextCID är ej giltig\n";
		exit;
		}
	else
		{
		##### look for the channel in the UPDATED vicidial_manager record of the call initiation
		$stmt="SELECT uniqueid,channel FROM vicidial_manager where callerid='$MDnextCID' and server_ip='$server_ip' and status IN('UPDATED','DEAD') LIMIT 1;";
		$rslt=mysql_query($stmt, $link);
			if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00052',$user,$server_ip,$session_name,$one_mysql_log);}
		if ($DB) {echo "$stmt\n";}
		$VM_mancall_ct = mysql_num_rows($rslt);
		if ($VM_mancall_ct > 0)
			{
			$row=mysql_fetch_row($rslt);
			$uniqueid =$row[0];
			$channel =$row[1];
			$call_output = "$uniqueid\n$channel\n";
			$call_good++;
			}
		else
			{
			### after 10 sekunder, start checking for call termination in the carrier log
			if ( ($DiaL_SecondS > 0) and (preg_match("/0$/",$DiaL_SecondS)) )
				{
				$stmt="SELECT uniqueid,channel,end_epoch FROM call_log where caller_code='$MDnextCID' and server_ip='$server_ip' order by start_time desc LIMIT 1;";
				$rslt=mysql_query($stmt, $link);
					if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00291',$user,$server_ip,$session_name,$one_mysql_log);}
				if ($DB) {echo "$stmt\n";}
				$VM_mancallX_ct = mysql_num_rows($rslt);
				if ($VM_mancallX_ct > 0)
					{
					$row=mysql_fetch_row($rslt);
					$uniqueid =		$row[0];
					$channel =		$row[1];
					$end_epoch =	$row[2];

					### Check carrier log for error
					$stmt="SELECT dialstatus,hangup_cause FROM vicidial_carrier_log where uniqueid='$uniqueid' and server_ip='$server_ip' and channel='$channel' and dialstatus IN('BUSY','CHANUNAVAIL','CONGESTION') LIMIT 1;";
					$rslt=mysql_query($stmt, $link);
						if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00292',$user,$server_ip,$session_name,$one_mysql_log);}
					if ($DB) {echo "$stmt\n";}
					$CL_mancall_ct = mysql_num_rows($rslt);
					if ($CL_mancall_ct > 0)
						{
						$row=mysql_fetch_row($rslt);
						$dialstatus =$row[0];
						$hangup_cause =$row[1];
						
						$channel = $dialstatus;
						$hangup_cause_msg = "Cause: " . $hangup_cause . " - " . hangup_cause_description($hangup_cause);

						$call_output = "$uniqueid\n$channel\nERROR\n" . $hangup_cause_msg; 
						$call_good++;

						### Delete call record
						$stmt="DELETE from vicidial_auto_calls where callerid='$MDnextCID';";
							if ($format=='debug') {echo "\n<!-- $stmt -->";}
						$rslt=mysql_query($stmt, $link);
							if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00293',$user,$server_ip,$session_name,$one_mysql_log);}
						}
					}
				}
			}

		if ($call_good > 0)
			{
			$wait_sec=0;
			$dead_epochSQL = '';
			$stmt = "select wait_epoch,wait_sec,dead_epoch from vicidial_agent_log where agent_log_id='$agent_log_id';";
			if ($DB) {echo "$stmt\n";}
			$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00053',$user,$server_ip,$session_name,$one_mysql_log);}
			$VDpr_ct = mysql_num_rows($rslt);
			if ($VDpr_ct > 0)
				{
				$row=mysql_fetch_row($rslt);
				$wait_sec = (($StarTtime - $row[0]) + $row[1]);
				$now_dead_epoch = $row[2];
				if ( ($now_dead_epoch > 1000) and ($now_dead_epoch < $StarTtime) )
					{$dead_epochSQL = ",dead_epoch='$StarTtime'";}
				}
			$stmt="UPDATE vicidial_agent_log set wait_sec='$wait_sec',talk_epoch='$StarTtime',lead_id='$lead_id' $dead_epochSQL where agent_log_id='$agent_log_id';";
				if ($format=='debug') {echo "\n<!-- $stmt -->";}
			$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00054',$user,$server_ip,$session_name,$one_mysql_log);}

			$stmt="UPDATE vicidial_auto_calls set uniqueid='$uniqueid',channel='$channel' where callerid='$MDnextCID';";
				if ($format=='debug') {echo "\n<!-- $stmt -->";}
			$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00055',$user,$server_ip,$session_name,$one_mysql_log);}

			$stmt="UPDATE call_log set uniqueid='$uniqueid',channel='$channel' where caller_code='$MDnextCID';";
				if ($format=='debug') {echo "\n<!-- $stmt -->";}
			$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00300',$user,$server_ip,$session_name,$one_mysql_log);}

			echo "$call_output";
			}
		else
			{echo "NO\n$DiaL_SecondS\n";}
		}
	}



################################################################################
### manDiaLlogCALL - for manual VICIDiaL logging of calls places record in
###                  vicidial_log and then sends process to call_log entry
################################################################################
if ($ACTION == 'manDiaLlogCaLL')
{
	$MT[0]='';
	$row='';   $rowx='';
	$vidSQL='';
	$VDterm_reason='';

if ($stage == "start")
	{
	if ( (strlen($uniqueid)<1) || (strlen($lead_id)<1) || (strlen($list_id)<1) || (strlen($phone_number)<1) || (strlen($campaign)<1) )
		{
		$fp = fopen ("./vicidial_debug.txt", "a");
		fwrite ($fp, "$NOW_TIME|VL_LOG_0|$uniqueid|$lead_id|$user|$list_id|$campaign|$start_epoch|$phone_number|$agent_log_id|\n");
		fclose($fp);

		echo "LOGG SKREVS EJ\n";
		echo "uniqueid $uniqueid or lead_id: $lead_id or list_id: $list_id or phone_number: $phone_number or campaign: $campaign är ej giltig\n";
		exit;
		}
	else
		{
		$user_group='';
		$stmt="SELECT user_group FROM vicidial_users where user='$user' LIMIT 1;";
		$rslt=mysql_query($stmt, $link);
		if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00056',$user,$server_ip,$session_name,$one_mysql_log);}
		if ($DB) {echo "$stmt\n";}
		$ug_record_ct = mysql_num_rows($rslt);
		if ($ug_record_ct > 0)
			{
			$row=mysql_fetch_row($rslt);
			$user_group =		trim("$row[0]");
			}

		$manualVLexists=0;
		$beginUNIQUEID = preg_replace("/\..*/","",$uniqueid);
		$stmt="SELECT count(*) from vicidial_log where lead_id='$lead_id' and user='$user' and phone_number='$phone_number' and uniqueid LIKE \"$beginUNIQUEID%\";";
		$rslt=mysql_query($stmt, $link);
			if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00223',$user,$server_ip,$session_name,$one_mysql_log);}
		if ($DB) {echo "$stmt\n";}
		$VL_exists_ct = mysql_num_rows($rslt);
		if ($VL_exists_ct > 0)
			{
			$row=mysql_fetch_row($rslt);
			$manualVLexists =		$row[0];
			}

		$manualVLexistsDUP=0;
		if ($manualVLexists < 1)
			{
			##### insert log into vicidial_log for manual VICIDiaL call
			$stmt="INSERT INTO vicidial_log (uniqueid,lead_id,list_id,campaign_id,call_date,start_epoch,status,phone_code,phone_number,user,comments,processed,user_group,alt_dial) values('$uniqueid','$lead_id','$list_id','$campaign','$NOW_TIME','$StarTtime','INCALL','$phone_code','$phone_number','$user','MANUAL','N','$user_group','$alt_dial');";
			if ($DB) {echo "$stmt\n";}
			$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00279',$user,$server_ip,$session_name,$one_mysql_log);}
			$DUPerrno = mysql_errno($link);
			if ($DUPerrno > 0)
				{$manualVLexistsDUP=1;}
			$affected_rows = mysql_affected_rows($link);
			}
		if ( ($manualVLexists > 0) or ($manualVLexistsDUP > 0) )
			{
			##### insert log into vicidial_log for manual VICIDiaL call
			$stmt="UPDATE vicidial_log SET list_id='$list_id',comments='MANUAL',user_group='$user_group',alt_dial='$alt_dial' where lead_id='$lead_id' and user='$user' and phone_number='$phone_number' and uniqueid LIKE \"$beginUNIQUEID%\";";
			if ($DB) {echo "$stmt\n";}
			$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00224',$user,$server_ip,$session_name,$one_mysql_log);}
			$affected_rows = mysql_affected_rows($link);
			}

		if ($affected_rows > 0)
			{
			echo "VICIDiaL_LOG Tillagd: $uniqueid|$channel|$NOW_TIME\n";
			echo "$StarTtime\n";
			}
		else
			{
			echo "LOGG SKREVS EJ\n";
			}

		$stmt = "UPDATE vicidial_auto_calls SET uniqueid='$uniqueid' where lead_id='$lead_id';";
		if ($DB) {echo "$stmt\n";}
		$rslt=mysql_query($stmt, $link);
			if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00058',$user,$server_ip,$session_name,$one_mysql_log);}

	#	##### insert log into call_log for manual VICIDiaL call
	#	$stmt = "INSERT INTO call_log (uniqueid,channel,server_ip,extension,number_dialed,caller_code,start_time,start_epoch) values('$uniqueid','$channel','$server_ip','$exten','$phone_code$phone_number','MD $user $lead_id','$NOW_TIME','$StarTtime')";
	#	if ($DB) {echo "$stmt\n";}
	#	$rslt=mysql_query($stmt, $link);
	#	$affected_rows = mysql_affected_rows($link);

	#	if ($affected_rows > 0)
	#		{
	#		echo "CALL_LOG Tillagd: $uniqueid|$channel|$NOW_TIME";
	#		}
	#	else
	#		{
	#		echo "LOGG SKREVS EJ\n";
	#		}
		}
	}

if ($stage == "end")
	{
	$status_dispo = 'DISPO';
	if ($alt_num_status > 0)
		{$status_dispo = 'ALTNUM';}
	##### get call type from vicidial_live_agents table
	$VLA_inOUT='NONE';
	$stmt="SELECT comments FROM vicidial_live_agents where user='$user' order by last_update_time desc limit 1;";
	$rslt=mysql_query($stmt, $link);
			if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00059',$user,$server_ip,$session_name,$one_mysql_log);}
	if ($DB) {echo "$stmt\n";}
	$VLA_inOUT_ct = mysql_num_rows($rslt);
	if ($VLA_inOUT_ct > 0)
		{
		$row=mysql_fetch_row($rslt);
		$VLA_inOUT =		$row[0];
		}

	if ( (strlen($uniqueid)<1) and ($VLA_inOUT == 'INBOUND') )
		{
		$fp = fopen ("./vicidial_debug.txt", "a");
		fwrite ($fp, "$NOW_TIME|INBND_LOG_0|$uniqueid|$lead_id|$user|$inOUT|$VLA_inOUT|$start_epoch|$phone_number|$agent_log_id|\n");
		fclose($fp);
		$uniqueid='6666.1';
		}
	if ( (strlen($uniqueid)<1) or (strlen($lead_id)<1) )
		{
		echo "LOGG SKREVS EJ\n";
		echo "uniqueid $uniqueid or lead_id: $lead_id är ej giltig\n";
		exit;
		}
	else
		{
		$term_reason='NONE';
		if ($start_epoch < 1000)
			{
			if ($VLA_inOUT == 'INBOUND')
				{
				$four_hours_ago = date("Y-m-d H:i:s", mktime(date("H")-4,date("i"),date("s"),date("m"),date("d"),date("Y")));

				##### look for the start epoch in the vicidial_closer_log table
				$stmt="SELECT start_epoch,term_reason,closecallid,campaign_id FROM vicidial_closer_log where phone_number='$phone_number' and lead_id='$lead_id' and user='$user' and call_date > \"$four_hours_ago\" order by closecallid desc limit 1;";
				$VDIDselect =		"VDCL_LID $lead_id $phone_number $user $four_hours_ago";
				}
			else
				{
				##### look for the start epoch in the vicidial_log table
				$stmt="SELECT start_epoch,term_reason,uniqueid,campaign_id FROM vicidial_log where uniqueid='$uniqueid' and lead_id='$lead_id' order by call_date desc limit 1;";
				$VDIDselect =		"VDL_UIDLID $uniqueid $lead_id";
				}
			$rslt=mysql_query($stmt, $link);
			if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00060',$user,$server_ip,$session_name,$one_mysql_log);}
			if ($DB) {echo "$stmt\n";}
			$VM_mancall_ct = mysql_num_rows($rslt);
			if ($VM_mancall_ct > 0)
				{
				$row=mysql_fetch_row($rslt);
				$start_epoch =		$row[0];
				$VDterm_reason =	$row[1];
				$VDvicidial_id =	$row[2];
				$VDcampaign_id =	$row[3];
				$length_in_sec = ($StarTtime - $start_epoch);
				}
			else
				{
				$length_in_sec = 0;
				}

			if ( ($length_in_sec < 1) and ($VLA_inOUT == 'INBOUND') )
				{
				$fp = fopen ("./vicidial_debug.txt", "a");
				fwrite ($fp, "$NOW_TIME|INBND_LOG_1|$uniqueid|$lead_id|$user|$inOUT|$length_in_sec|$VDterm_reason|$VDvicidial_id|$start_epoch|\n");
				fclose($fp);

				##### start epoch in the vicidial_log table, couldn't find one in vicidial_closer_log
				$stmt="SELECT start_epoch,term_reason,campaign_id FROM vicidial_log where uniqueid='$uniqueid' and lead_id='$lead_id' order by call_date desc limit 1;";
				$rslt=mysql_query($stmt, $link);
			if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00061',$user,$server_ip,$session_name,$one_mysql_log);}
				if ($DB) {echo "$stmt\n";}
				$VM_mancall_ct = mysql_num_rows($rslt);
				if ($VM_mancall_ct > 0)
					{
					$row=mysql_fetch_row($rslt);
					$start_epoch =		$row[0];
					$VDterm_reason =	$row[1];
					$VDcampaign_id =	$row[2];
					$length_in_sec = ($StarTtime - $start_epoch);
					}
				else
					{
					$length_in_sec = 0;
					}
				}
			}
		else {$length_in_sec = ($StarTtime - $start_epoch);}
		
		if (strlen($VDcampaign_id)<1) {$VDcampaign_id = $campaign;}

		$four_hours_ago = date("Y-m-d H:i:s", mktime(date("H")-4,date("i"),date("s"),date("m"),date("d"),date("Y")));

		if ($VLA_inOUT == 'INBOUND')
			{
			$stmt = "UPDATE vicidial_closer_log set end_epoch='$StarTtime', length_in_sec='$length_in_sec', status='$status_dispo' where lead_id='$lead_id' and user='$user' and call_date > \"$four_hours_ago\" order by call_date desc limit 1;";
			if ($DB) {echo "$stmt\n";}
			$rslt=mysql_query($stmt, $link);
			if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00062',$user,$server_ip,$session_name,$one_mysql_log);}
			$affected_rows = mysql_affected_rows($link);
			if ($affected_rows > 0)
				{
				echo "$uniqueid\n$channel\n";
				}
			else
				{
				$fp = fopen ("./vicidial_debug.txt", "a");
				fwrite ($fp, "$NOW_TIME|INBND_LOG_2|$uniqueid|$lead_id|$user|$inOUT|$length_in_sec|$VDterm_reason|$VDvicidial_id|$start_epoch|\n");
				fclose($fp);
				}
			}

		#############################################
		##### START QUEUEMETRICS LOGGING LOOKUP #####
		$stmt = "SELECT enable_queuemetrics_logging,queuemetrics_server_ip,queuemetrics_dbname,queuemetrics_login,queuemetrics_pass,queuemetrics_log_id FROM system_settings;";
		$rslt=mysql_query($stmt, $link);
			if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00063',$user,$server_ip,$session_name,$one_mysql_log);}
		if ($DB) {echo "$stmt\n";}
		$qm_conf_ct = mysql_num_rows($rslt);
		$i=0;
		if ($qm_conf_ct > 0)
			{
			$row=mysql_fetch_row($rslt);
			$enable_queuemetrics_logging =	$row[0];
			$queuemetrics_server_ip	=		$row[1];
			$queuemetrics_dbname =			$row[2];
			$queuemetrics_login	=			$row[3];
			$queuemetrics_pass =			$row[4];
			$queuemetrics_log_id =			$row[5];

			if ($enable_queuemetrics_logging > 0)
				{
				$linkB=mysql_connect("$queuemetrics_server_ip", "$queuemetrics_login", "$queuemetrics_pass");
				mysql_select_db("$queuemetrics_dbname", $linkB);
				}
			}
		##### END QUEUEMETRICS LOGGING LOOKUP #####
		###########################################

		if ($auto_dial_level > 0)
			{
			### check to see if campaign has alt_dial enabled
			$stmt="SELECT auto_alt_dial,use_internal_dnc,use_campaign_dnc FROM vicidial_campaigns where campaign_id='$campaign';";
			$rslt=mysql_query($stmt, $link);
			if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00064',$user,$server_ip,$session_name,$one_mysql_log);}
			if ($DB) {echo "$stmt\n";}
			$VAC_mancall_ct = mysql_num_rows($rslt);
			if ($VAC_mancall_ct > 0)
				{
				$row=mysql_fetch_row($rslt);
				$auto_alt_dial =	$row[0];
				$use_internal_dnc =	$row[1];
				$use_campaign_dnc =	$row[2];
				}
			else {$auto_alt_dial = 'NONE';}
			if (eregi("(ALT_ONLY|ADDR3_ONLY|ALT_AND_ADDR3|ALT_AND_EXTENDED|ALT_AND_ADDR3_AND_EXTENDED|EXTENDED_ONLY)",$auto_alt_dial))
				{
				### check to see if lead should be alt_dialed
				if (strlen($alt_dial)<2) {$alt_dial = 'NONE';}

				### check if inbound call, if so find a recent outbound call to pull alt_dial value from
				if ($VLA_inOUT == 'INBOUND')
					{
					$one_hour_ago = date("Y-m-d H:i:s", mktime(date("H")-1,date("i"),date("s"),date("m"),date("d"),date("Y")));
					##### find a recent outbound call associated with this inbound call
					$stmt="SELECT alt_dial FROM vicidial_log where lead_id='$lead_id' and status IN('DROP','XDROP') and call_date > \"$one_hour_ago\" order by call_date desc limit 1;";
					$rslt=mysql_query($stmt, $link);
						if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00235',$user,$server_ip,$session_name,$one_mysql_log);}
					if ($DB) {echo "$stmt\n";}
					$VL_alt_ct = mysql_num_rows($rslt);
					if ($VL_alt_ct > 0)
						{
						$row=mysql_fetch_row($rslt);
						$alt_dial =		$row[0];
						}
					}

				if ( (eregi("(NONE|MAIN)",$alt_dial)) and (eregi("(ALT_ONLY|ALT_AND_ADDR3|ALT_AND_EXTENDED)",$auto_alt_dial)) )
					{
					$alt_dial_skip=0;
					$stmt="SELECT alt_phone,gmt_offset_now,state FROM vicidial_list where lead_id='$lead_id';";
					$rslt=mysql_query($stmt, $link);
			if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00065',$user,$server_ip,$session_name,$one_mysql_log);}
					if ($DB) {echo "$stmt\n";}
					$VAC_mancall_ct = mysql_num_rows($rslt);
					if ($VAC_mancall_ct > 0)
						{
						$row=mysql_fetch_row($rslt);
						$alt_phone =		$row[0];
						$alt_phone = eregi_replace("[^0-9]","",$alt_phone);
						$gmt_offset_now =	$row[1];
						$state =			$row[2];
						}
					else {$alt_phone = '';}
					if (strlen($alt_phone)>5)
						{
						if ( (ereg("Y",$use_internal_dnc)) or (ereg("AREACODE",$use_internal_dnc)) )
							{
							if (ereg("AREACODE",$use_internal_dnc))
								{
								$alt_phone_areacode = substr($alt_phone, 0, 3);
								$alt_phone_areacode .= "XXXXXXX";
								$stmtA="SELECT count(*) from vicidial_dnc where phone_number IN('$alt_phone','$alt_phone_areacode');";
								}
							else
								{$stmtA="SELECT count(*) FROM vicidial_dnc where phone_number='$alt_phone';";}
							$rslt=mysql_query($stmtA, $link);
			if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmtA,'00066',$user,$server_ip,$session_name,$one_mysql_log);}
							if ($DB) {echo "$stmt\n";}
							$VLAP_dnc_ct = mysql_num_rows($rslt);
							if ($VLAP_dnc_ct > 0)
								{
								$row=mysql_fetch_row($rslt);
								$VD_alt_dnc_count =		$row[0];
								}
							}
						else {$VD_alt_dnc_count=0;}
						if ( (ereg("Y",$use_campaign_dnc)) or (ereg("AREACODE",$use_campaign_dnc)) )
							{
							if (ereg("AREACODE",$use_campaign_dnc))
								{
								$alt_phone_areacode = substr($alt_phone, 0, 3);
								$alt_phone_areacode .= "XXXXXXX";
								$stmtA="SELECT count(*) from vicidial_campaign_dnc where phone_number IN('$alt_phone','$alt_phone_areacode') and campaign_id='$campaign';";
								}
							else
								{$stmtA="SELECT count(*) FROM vicidial_campaign_dnc where phone_number='$alt_phone' and campaign_id='$campaign';";}
							$rslt=mysql_query($stmtA, $link);
			if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmtA,'00067',$user,$server_ip,$session_name,$one_mysql_log);}
							if ($DB) {echo "$stmt\n";}
							$VLAP_cdnc_ct = mysql_num_rows($rslt);
							if ($VLAP_cdnc_ct > 0)
								{
								$row=mysql_fetch_row($rslt);
								$VD_alt_dnc_count =		($VD_alt_dnc_count + $row[0]);
								}
							}
						if ($VD_alt_dnc_count < 1)
							{
							### insert record into vicidial_hopper for alt_phone call attempt
							$stmt = "INSERT INTO vicidial_hopper SET lead_id='$lead_id',campaign_id='$campaign',status='HOLD',list_id='$list_id',gmt_offset_now='$gmt_offset_now',state='$state',alt_dial='ALT',user='',priority='25';";
							if ($DB) {echo "$stmt\n";}
							$rslt=mysql_query($stmt, $link);
			if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00068',$user,$server_ip,$session_name,$one_mysql_log);}
							}
						else
							{$alt_dial_skip=1;}
						}
					else
						{$alt_dial_skip=1;}
					if ($alt_dial_skip > 0)
						{$alt_dial='ALT';}
					}

				if ( ( (eregi("(ALT)",$alt_dial)) and (eregi("ALT_AND_ADDR3",$auto_alt_dial)) ) or ( (eregi("(NONE|MAIN)",$alt_dial)) and (eregi("ADDR3_ONLY",$auto_alt_dial)) ) )
					{
					$addr3_dial_skip=0;
					$stmt="SELECT address3,gmt_offset_now,state FROM vicidial_list where lead_id='$lead_id';";
					$rslt=mysql_query($stmt, $link);
			if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00069',$user,$server_ip,$session_name,$one_mysql_log);}
					if ($DB) {echo "$stmt\n";}
					$VAC_mancall_ct = mysql_num_rows($rslt);
					if ($VAC_mancall_ct > 0)
						{
						$row=mysql_fetch_row($rslt);
						$address3 =			$row[0];
						$address3 = eregi_replace("[^0-9]","",$address3);
						$gmt_offset_now =	$row[1];
						$state =			$row[2];
						}
					else {$address3 = '';}
					if (strlen($address3)>5)
						{
						if ( (ereg("Y",$use_internal_dnc)) or (ereg("AREACODE",$use_internal_dnc)) )
							{
							if (ereg("AREACODE",$use_internal_dnc))
								{
								$addr3_phone_areacode = substr($address3, 0, 3);
								$addr3_phone_areacode .= "XXXXXXX";
								$stmtA="SELECT count(*) from vicidial_dnc where phone_number IN('$address3','$addr3_phone_areacode');";
								}
							else
								{$stmtA="SELECT count(*) FROM vicidial_dnc where phone_number='$address3';";}
							$rslt=mysql_query($stmtA, $link);
			if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmtA,'00070',$user,$server_ip,$session_name,$one_mysql_log);}
							if ($DB) {echo "$stmt\n";}
							$VLAP_dnc_ct = mysql_num_rows($rslt);
							if ($VLAP_dnc_ct > 0)
								{
								$row=mysql_fetch_row($rslt);
								$VD_alt_dnc_count =		$row[0];
								}
							}
						else {$VD_alt_dnc_count=0;}
						if ( (ereg("Y",$use_campaign_dnc)) or (ereg("AREACODE",$use_campaign_dnc)) )
							{
							if (ereg("AREACODE",$use_campaign_dnc))
								{
								$addr3_phone_areacode = substr($address3, 0, 3);
								$addr3_phone_areacode .= "XXXXXXX";
								$stmtA="SELECT count(*) from vicidial_campaign_dnc where phone_number IN('$address3','$addr3_phone_areacode') and campaign_id='$campaign';";
								}
							else
								{$stmtA="SELECT count(*) FROM vicidial_campaign_dnc where phone_number='$address3' and campaign_id='$campaign';";}
							$rslt=mysql_query($stmtA, $link);
			if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmtA,'00071',$user,$server_ip,$session_name,$one_mysql_log);}
							if ($DB) {echo "$stmt\n";}
							$VLAP_cdnc_ct = mysql_num_rows($rslt);
							if ($VLAP_cdnc_ct > 0)
								{
								$row=mysql_fetch_row($rslt);
								$VD_alt_dnc_count =		($VD_alt_dnc_count + $row[0]);
								}
							}
						if ($VD_alt_dnc_count < 1)
							{
							### insert record into vicidial_hopper for address3 call attempt
							$stmt = "INSERT INTO vicidial_hopper SET lead_id='$lead_id',campaign_id='$campaign',status='HOLD',list_id='$list_id',gmt_offset_now='$gmt_offset_now',state='$state',alt_dial='ADDR3',user='',priority='20';";
							if ($DB) {echo "$stmt\n";}
							$rslt=mysql_query($stmt, $link);
			if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00072',$user,$server_ip,$session_name,$one_mysql_log);}
							}
						else
							{$addr3_dial_skip=1;}
						}
					else
						{$addr3_dial_skip=1;}
					if ($addr3_dial_skip > 0)
						{$alt_dial='ADDR3';}
					}

	#		$fp = fopen ("./alt_multi_log.txt", "a");
	#		fwrite ($fp, "$NOW_TIME|PRE-X|$campaign|$lead_id|$phone_number|$user|$Ctype|$callerid|$uniqueid|$stmt|$auto_alt_dial|$alt_dial\n");
	#		fclose($fp);

				if ( ( ( (eregi("(NONE|MAIN)",$alt_dial)) and (eregi("EXTENDED_ONLY",$auto_alt_dial)) ) or ( (eregi("(ALT)",$alt_dial)) and (eregi("(ALT_AND_EXTENDED)",$auto_alt_dial)) ) or ( (eregi("(ADDR3)",$alt_dial)) and (eregi("(ADDR3_AND_EXTENDED|ALT_AND_ADDR3_AND_EXTENDED)",$auto_alt_dial)) ) or ( (eregi("(X)",$alt_dial)) and (eregi("EXTENDED",$auto_alt_dial)) ) )  and (!eregi("LAST",$alt_dial)) )
					{
					if (eregi("(ADDR3)",$alt_dial)) {$Xlast=0;}
					else
						{$Xlast = ereg_replace("[^0-9]","",$alt_dial);}
					if (strlen($Xlast)<1)
						{$Xlast=0;}
					$VD_altdialx='';

					$stmt="SELECT gmt_offset_now,state,list_id FROM vicidial_list where lead_id='$lead_id';";
					$rslt=mysql_query($stmt, $link);
			if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00073',$user,$server_ip,$session_name,$one_mysql_log);}
					if ($DB) {echo "$stmt\n";}
					$VL_deailts_ct = mysql_num_rows($rslt);
					if ($VL_deailts_ct > 0)
						{
						$row=mysql_fetch_row($rslt);
						$EA_gmt_offset_now =	$row[0];
						$EA_state =				$row[1];
						$EA_list_id =			$row[2];
						}
					$alt_dial_phones_count=0;
					$stmt="SELECT count(*) FROM vicidial_list_alt_phones where lead_id='$lead_id';";
					$rslt=mysql_query($stmt, $link);
			if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00074',$user,$server_ip,$session_name,$one_mysql_log);}
					if ($DB) {echo "$stmt\n";}
					$VLAP_ct = mysql_num_rows($rslt);
					if ($VLAP_ct > 0)
						{
						$row=mysql_fetch_row($rslt);
						$alt_dial_phones_count =	$row[0];
						}
					while ( ($alt_dial_phones_count > 0) and ($alt_dial_phones_count > $Xlast) )
						{
						$Xlast++;
						$stmt="SELECT alt_phone_id,phone_number,active FROM vicidial_list_alt_phones where lead_id='$lead_id' and alt_phone_count='$Xlast';";
						$rslt=mysql_query($stmt, $link);
			if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00075',$user,$server_ip,$session_name,$one_mysql_log);}
						if ($DB) {echo "$stmt\n";}
						$VLAP_detail_ct = mysql_num_rows($rslt);
						if ($VLAP_detail_ct > 0)
							{
							$row=mysql_fetch_row($rslt);
							$VD_altdial_id =		$row[0];
							$VD_altdial_phone =		$row[1];
							$VD_altdial_active =	$row[2];
							}
						else
							{$Xlast=9999999999;}

						if (ereg("Y",$VD_altdial_active))
							{
							if ( (ereg("Y",$use_internal_dnc)) or (ereg("AREACODE",$use_internal_dnc)) )
								{
								if (ereg("AREACODE",$use_internal_dnc))
									{
									$vdap_phone_areacode = substr($VD_altdial_phone, 0, 3);
									$vdap_phone_areacode .= "XXXXXXX";
									$stmtA="SELECT count(*) from vicidial_dnc where phone_number IN('$VD_altdial_phone','$vdap_phone_areacode');";
									}
								else
									{$stmtA="SELECT count(*) FROM vicidial_dnc where phone_number='$VD_altdial_phone';";}
								$rslt=mysql_query($stmtA, $link);
			if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmtA,'00076',$user,$server_ip,$session_name,$one_mysql_log);}
								if ($DB) {echo "$stmt\n";}
								$VLAP_dnc_ct = mysql_num_rows($rslt);
								if ($VLAP_dnc_ct > 0)
									{
									$row=mysql_fetch_row($rslt);
									$VD_alt_dnc_count =		$row[0];
									}
								}
							else {$VD_alt_dnc_count=0;}
							if ( (ereg("Y",$use_campaign_dnc)) or (ereg("AREACODE",$use_campaign_dnc)) )
								{
								if (ereg("AREACODE",$use_campaign_dnc))
									{
									$vdap_phone_areacode = substr($VD_altdial_phone, 0, 3);
									$vdap_phone_areacode .= "XXXXXXX";
									$stmtA="SELECT count(*) from vicidial_campaign_dnc where phone_number IN('$VD_altdial_phone','$vdap_phone_areacode') and campaign_id='$campaign';";
									}
								else
									{$stmtA="SELECT count(*) FROM vicidial_campaign_dnc where phone_number='$VD_altdial_phone' and campaign_id='$campaign';";}
								$rslt=mysql_query($stmtA, $link);
			if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmtA,'00077',$user,$server_ip,$session_name,$one_mysql_log);}
								if ($DB) {echo "$stmt\n";}
								$VLAP_cdnc_ct = mysql_num_rows($rslt);
								if ($VLAP_cdnc_ct > 0)
									{
									$row=mysql_fetch_row($rslt);
									$VD_alt_dnc_count =		($VD_alt_dnc_count + $row[0]);
									}
								}
							if ($VD_alt_dnc_count < 1)
								{
								if ($alt_dial_phones_count == $Xlast) 
									{$Xlast = 'LAST';}
								$stmt = "INSERT INTO vicidial_hopper SET lead_id='$lead_id',campaign_id='$campaign',status='HOLD',list_id='$EA_list_id',gmt_offset_now='$EA_gmt_offset_now',state='$EA_state',alt_dial='X$Xlast',user='',priority='15';";
								if ($DB) {echo "$stmt\n";}
								$rslt=mysql_query($stmt, $link);
			if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00078',$user,$server_ip,$session_name,$one_mysql_log);}
								$Xlast=9999999999;
								}
							}
						}
					}
				}

			if ($enable_queuemetrics_logging > 0)
				{
				### grab call lead information needed for QM logging
				$stmt="SELECT auto_call_id,lead_id,phone_number,status,campaign_id,phone_code,alt_dial,stage,callerid,uniqueid from vicidial_auto_calls where lead_id='$lead_id' order by call_time limit 1;";
				$rslt=mysql_query($stmt, $link);
			if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00079',$user,$server_ip,$session_name,$one_mysql_log);}
				if ($DB) {echo "$stmt\n";}
				$VAC_qm_ct = mysql_num_rows($rslt);
				if ($VAC_qm_ct > 0)
					{
					$row=mysql_fetch_row($rslt);
					$auto_call_id	= $row[0];
					$CLlead_id		= $row[1];
					$CLphone_number	= $row[2];
					$CLstatus		= $row[3];
					$CLcampaign_id	= $row[4];
					$CLphone_code	= $row[5];
					$CLalt_dial		= $row[6];
					$CLstage		= $row[7];
					$CLcallerid		= $row[8];
					$CLuniqueid		= $row[9];
					}

				$CLstage = preg_replace("/.*-/",'',$CLstage);
				if (strlen($CLstage) < 1) {$CLstage=0;}

				$stmt="SELECT count(*) from queue_log where call_id='$MDnextCID' and verb='COMPLETECALLER' and queue='$VDcampaign_id';";
				$rslt=mysql_query($stmt, $linkB);
			if ($mel > 0) {mysql_error_logging($NOW_TIME,$linkB,$mel,$stmt,'00080',$user,$server_ip,$session_name,$one_mysql_log);}
				if ($DB) {echo "$stmt\n";}
				$VAC_cc_ct = mysql_num_rows($rslt);
				if ($VAC_cc_ct > 0)
					{
					$row=mysql_fetch_row($rslt);
					$caller_complete	= $row[0];
					}

				if ($caller_complete < 1)
					{
					$term_reason='AGENT';
					}
				else
					{
					$term_reason='CALLER';
					}

				}

			if ($nodeletevdac < 1)
				{
				### delete call record from  vicidial_auto_calls
				$stmt = "DELETE from vicidial_auto_calls where lead_id='$lead_id' and campaign_id='$VDcampaign_id' and uniqueid='$uniqueid';";
				if ($DB) {echo "$stmt\n";}
				$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00081',$user,$server_ip,$session_name,$one_mysql_log);}
				}

			$stmt = "UPDATE vicidial_live_agents set status='PAUSED',uniqueid=0,callerid='',channel='',call_server_ip='',last_call_finish='$NOW_TIME',comments='',last_state_change='$NOW_TIME' where user='$user' and server_ip='$server_ip';";
			if ($DB) {echo "$stmt\n";}
			$rslt=mysql_query($stmt, $link);
			if ($mel > 0) {$errno = mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00082',$user,$server_ip,$session_name,$one_mysql_log);}
				$retry_count=0;
				while ( ($errno > 0) and ($retry_count < 9) )
					{
					$rslt=mysql_query($stmt, $link);
					$one_mysql_log=1;
					$errno = mysql_error_logging($NOW_TIME,$link,$mel,$stmt,"9082$retry_count",$user,$server_ip,$session_name,$one_mysql_log);
					$one_mysql_log=0;
					$retry_count++;
					}

			$affected_rows = mysql_affected_rows($link);
			if ($affected_rows > 0) 
				{
				if ($enable_queuemetrics_logging > 0)
					{
					$stmt = "INSERT INTO queue_log SET partition='P01',time_id='$StarTtime',call_id='NONE',queue='NONE',agent='Agent/$user',verb='PAUSEALL',serverid='$queuemetrics_log_id';";
					if ($DB) {echo "$stmt\n";}
					$rslt=mysql_query($stmt, $linkB);
			if ($mel > 0) {mysql_error_logging($NOW_TIME,$linkB,$mel,$stmt,'00083',$user,$server_ip,$session_name,$one_mysql_log);}
					$affected_rows = mysql_affected_rows($linkB);
					}
				}
			}
		else
			{
			if ($enable_queuemetrics_logging > 0)
				{
				$CLqueue_position=1;
				### check to see if lead should be alt_dialed
				$stmt="SELECT auto_call_id,lead_id,phone_number,status,campaign_id,phone_code,alt_dial,stage,callerid,uniqueid,queue_position from vicidial_auto_calls where lead_id='$lead_id' order by call_time desc limit 1;";
				$rslt=mysql_query($stmt, $link);
			if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00084',$user,$server_ip,$session_name,$one_mysql_log);}
				if ($DB) {echo "$stmt\n";}
				$VAC_qm_ct = mysql_num_rows($rslt);
				if ($VAC_qm_ct > 0)
					{
					$row=mysql_fetch_row($rslt);
					$auto_call_id = 		$row[0];
					$CLlead_id = 			$row[1];
					$CLphone_number =		$row[2];
					$CLstatus = 			$row[3];
					$CLcampaign_id = 		$row[4];
					$CLphone_code = 		$row[5];
					$CLalt_dial =			$row[6];
					$CLstage =				$row[7];
					$CLcallerid =			$row[8];
					$CLuniqueid =			$row[9];
					$CLqueue_position =		$row[10];
					}

				$CLstage = preg_replace("/XFER|CLOSER|-/",'',$CLstage);
				if ($CLstage < 0.25) {$CLstage=0;}

				$stmt = "INSERT INTO queue_log SET partition='P01',time_id='$StarTtime',call_id='$MDnextCID',queue='$VDcampaign_id',agent='Agent/$user',verb='COMPLETEAGENT',data1='$CLstage',data2='$length_in_sec',data3='$CLqueue_position',serverid='$queuemetrics_log_id';";
				if ($DB) {echo "$stmt\n";}
				$rslt=mysql_query($stmt, $linkB);
			if ($mel > 0) {mysql_error_logging($NOW_TIME,$linkB,$mel,$stmt,'00085',$user,$server_ip,$session_name,$one_mysql_log);}
				$affected_rows = mysql_affected_rows($linkB);
				}

			if ($nodeletevdac < 1)
				{
			#	$stmt = "DELETE from vicidial_auto_calls where lead_id='$lead_id' and campaign_id='$campaign' and uniqueid='$uniqueid';";
				$stmt = "DELETE from vicidial_auto_calls where lead_id='$lead_id' and campaign_id='$VDcampaign_id' and callerid LIKE \"M%\";";
				if ($DB) {echo "$stmt\n";}
				$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00086',$user,$server_ip,$session_name,$one_mysql_log);}
				}

			$stmt = "UPDATE vicidial_live_agents set status='PAUSED',uniqueid=0,callerid='',channel='',call_server_ip='',last_call_finish='$NOW_TIME',comments='',last_state_change='$NOW_TIME' where user='$user' and server_ip='$server_ip';";
			if ($DB) {echo "$stmt\n";}
			$rslt=mysql_query($stmt, $link);
			if ($mel > 0) {$errno = mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00087',$user,$server_ip,$session_name,$one_mysql_log);}
				$retry_count=0;
				while ( ($errno > 0) and ($retry_count < 9) )
					{
					$rslt=mysql_query($stmt, $link);
					$one_mysql_log=1;
					$errno = mysql_error_logging($NOW_TIME,$link,$mel,$stmt,"9087$retry_count",$user,$server_ip,$session_name,$one_mysql_log);
					$one_mysql_log=0;
					$retry_count++;
					}

			$affected_rows = mysql_affected_rows($link);
			if ($affected_rows > 0) 
				{
				if ($enable_queuemetrics_logging > 0)
					{
					$stmt = "INSERT INTO queue_log SET partition='P01',time_id='$StarTtime',call_id='NONE',queue='NONE',agent='Agent/$user',verb='PAUSEALL',serverid='$queuemetrics_log_id';";
					if ($DB) {echo "$stmt\n";}
					$rslt=mysql_query($stmt, $linkB);
			if ($mel > 0) {mysql_error_logging($NOW_TIME,$linkB,$mel,$stmt,'00088',$user,$server_ip,$session_name,$one_mysql_log);}
					$affected_rows = mysql_affected_rows($linkB);
					}
				}
			}

		if ( ($VLA_inOUT == 'AUTO') or ($VLA_inOUT == 'MANUAL') )
			{
			$SQLterm = "term_reason='$term_reason',";

			if ( (ereg("NONE",$term_reason)) or (ereg("NONE",$VDterm_reason)) or (strlen($VDterm_reason) < 1) )
				{
				### check to see if lead should be alt_dialed
				$stmt="SELECT term_reason,uniqueid from vicidial_log where uniqueid='$uniqueid' and lead_id='$lead_id' order by call_date desc limit 1;";
				$rslt=mysql_query($stmt, $link);
			if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00089',$user,$server_ip,$session_name,$one_mysql_log);}
				$VAC_qm_ct = mysql_num_rows($rslt);
				if ($VAC_qm_ct > 0)
					{
					$row=mysql_fetch_row($rslt);
					$VDterm_reason	= $row[0];
					$VDvicidial_id	= $row[1];
					$VDIDselect =		"VDL_UIDLID $uniqueid $lead_id";
					}
				if (ereg("CALLER",$VDterm_reason))
					{
					$SQLterm = "";
					}
				else
					{
					$SQLterm = "term_reason='AGENT',";
					}
				}

			### check to see if the vicidial_log record exists, if not, insert it
			$manualVLexists=0;
			$beginUNIQUEID = preg_replace("/\..*/","",$uniqueid);
			$stmt="SELECT count(*) from vicidial_log where lead_id='$lead_id' and user='$user' and phone_number='$phone_number' and uniqueid LIKE \"$beginUNIQUEID%\";";
			$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00223',$user,$server_ip,$session_name,$one_mysql_log);}
			if ($DB) {echo "$stmt\n";}
			$VL_exists_ct = mysql_num_rows($rslt);
			if ($VL_exists_ct > 0)
				{
				$row=mysql_fetch_row($rslt);
				$manualVLexists =		$row[0];
				}

			if ($manualVLexists < 1)
				{
				##### insert log into vicidial_log for manual VICIDiaL call
				$stmt="INSERT INTO vicidial_log (uniqueid,lead_id,list_id,campaign_id,call_date,start_epoch,status,phone_code,phone_number,user,comments,processed,user_group,alt_dial) values('$uniqueid','$lead_id','$list_id','$campaign','$NOW_TIME','$StarTtime','DONEM','$phone_code','$phone_number','$user','MANUAL','N','$user_group','$alt_dial');";
				if ($DB) {echo "$stmt\n";}
				$rslt=mysql_query($stmt, $link);
					if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00280',$user,$server_ip,$session_name,$one_mysql_log);}
				$affected_rows = mysql_affected_rows($link);

				if ($affected_rows > 0)
					{
					echo "VICIDiaL_LOG Tillagd: $uniqueid|$channel|$NOW_TIME\n";
					echo "$StarTtime\n";
					}
				else
					{
					echo "LOGG SKREVS EJ\n";
					}
				}
			else
				{
				$stmt="UPDATE vicidial_log SET uniqueid='$uniqueid' where lead_id='$lead_id' and user='$user' and phone_number='$phone_number' and uniqueid LIKE \"$beginUNIQUEID%\";";
				if ($DB) {echo "$stmt\n";}
				$rslt=mysql_query($stmt, $link);
					if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00057',$user,$server_ip,$session_name,$one_mysql_log);}
				$affected_rows = mysql_affected_rows($link);
				}

			##### update the duration and end time in the vicidial_log table
			$stmt="UPDATE vicidial_log set $SQLterm end_epoch='$StarTtime', length_in_sec='$length_in_sec', status='$status_dispo' where uniqueid='$uniqueid' and lead_id='$lead_id' and user='$user' order by call_date desc limit 1;";
			if ($DB) {echo "$stmt\n";}
			$rslt=mysql_query($stmt, $link);
			if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00090',$user,$server_ip,$session_name,$one_mysql_log);}
			$affected_rows = mysql_affected_rows($link);

			if ($affected_rows > 0)
				{
				echo "$uniqueid\n$channel\n";
				}
			else
				{
				echo "LOGG SKREVS EJ\n\n";
				}
			}
		else
			{
			$SQLterm = "term_reason='$term_reason'";
			$QL_term='';

			if ( (ereg("NONE",$term_reason)) or (ereg("NONE",$VDterm_reason)) or (strlen($VDterm_reason) < 1) )
				{
				### find out who hung up the call
				$stmt="SELECT term_reason,closecallid,queue_position from vicidial_closer_log where lead_id='$lead_id' and call_date > \"$four_hours_ago\" order by call_date desc limit 1;";
				$rslt=mysql_query($stmt, $link);
			if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00091',$user,$server_ip,$session_name,$one_mysql_log);}
				$VAC_qm_ct = mysql_num_rows($rslt);
				if ($VAC_qm_ct > 0)
					{
					$row=mysql_fetch_row($rslt);
					$VDterm_reason =		$row[0];
					$VDvicidial_id =		$row[1];
					$VDqueue_position =		$row[2];
					$VDIDselect =		"VDCL_LID4HOUR $lead_id $four_hours_ago";
					}
				if (ereg("CALLER",$VDterm_reason))
					{
					$SQLterm = "";
					}
				else
					{
					$SQLterm = "term_reason='AGENT'";
					$QL_term = 'COMPLETEAGENT';
					}
				}

			if (strlen($SQLterm) > 0)
				{
				##### update the duration and end time in the vicidial_log table
				$stmt="UPDATE vicidial_closer_log set $SQLterm, status='$status_dispo' where lead_id='$lead_id' and call_date > \"$four_hours_ago\" order by call_date desc limit 1;";
				if ($DB) {echo "$stmt\n";}
				$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00092',$user,$server_ip,$session_name,$one_mysql_log);}
				$affected_rows = mysql_affected_rows($link);
				}

			if ($enable_queuemetrics_logging > 0)
				{
				if ( (strlen($QL_term) > 0) and ($leaving_threeway > 0) )
					{
					$stmt="SELECT count(*) from queue_log where call_id='$MDnextCID' and verb='COMPLETEAGENT' and queue='$VDcampaign_id';";
					$rslt=mysql_query($stmt, $linkB);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$linkB,$mel,$stmt,'00093',$user,$server_ip,$session_name,$one_mysql_log);}
					if ($DB) {echo "$stmt\n";}
					$VAC_cc_ct = mysql_num_rows($rslt);
					if ($VAC_cc_ct > 0)
						{
						$row=mysql_fetch_row($rslt);
						$agent_complete	= $row[0];
						}
					if ($agent_complete < 1)
						{
						if (strlen($VDqueue_position) < 1)
							{
							### find out who hung up the call
							$stmt="SELECT queue_position from vicidial_closer_log where lead_id='$lead_id' and call_date > \"$four_hours_ago\" order by call_date desc limit 1;";
							$rslt=mysql_query($stmt, $link);
						if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00273',$user,$server_ip,$session_name,$one_mysql_log);}
							$VAC_qm_ct = mysql_num_rows($rslt);
							if ($VAC_qm_ct > 0)
								{
								$row=mysql_fetch_row($rslt);
								$VDqueue_position =		$row[0];
								}
							}
						if (strlen($VDqueue_position) < 1)
							{$VDqueue_position=1;}

						$stmt = "INSERT INTO queue_log SET partition='P01',time_id='$StarTtime',call_id='$MDnextCID',queue='$VDcampaign_id',agent='Agent/$user',verb='COMPLETEAGENT',data1='$CLstage',data2='$length_in_sec',data3='$VDqueue_position',serverid='$queuemetrics_log_id';";
						if ($DB) {echo "$stmt\n";}
						$rslt=mysql_query($stmt, $linkB);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$linkB,$mel,$stmt,'00094',$user,$server_ip,$session_name,$one_mysql_log);}
						$affected_rows = mysql_affected_rows($linkB);
						}
					}
				}
			}
		}

	echo $VDstop_rec_after_each_call . '|' . $extension . '|' . $conf_silent_prefix . '|' . $conf_exten . '|' . $user_abb . "|\n";

	##### if VICIDiaL call and hangup_after_each_call activated, find all recording 
	##### channels and hang them up while entering info into recording_log and 
	##### returning filename/recordingID
	if ($VDstop_rec_after_each_call == 1)
		{
		$local_DEF = 'Local/';
		$local_AMP = '@';
		$total_rec=0;
		$total_hangup=0;
		$loop_count=0;
		$stmt="SELECT channel FROM live_sip_channels where server_ip = '$server_ip' and extension = '$conf_exten' order by channel desc;";
			if ($format=='debug') {echo "\n<!-- $stmt -->";}
		$rslt=mysql_query($stmt, $link);
			if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00095',$user,$server_ip,$session_name,$one_mysql_log);}
		if ($rslt) {$rec_list = mysql_num_rows($rslt);}
			while ($rec_list>$loop_count)
			{
			$row=mysql_fetch_row($rslt);
			if (preg_match("/Local\/$conf_silent_prefix$conf_exten\@/i",$row[0]))
				{
				$rec_channels[$total_rec] = "$row[0]";
				$total_rec++;
				}
			else
				{
		#		if (preg_match("/$agentchannel/i",$row[0]))
				if ( ($agentchannel == "$row[0]") or (ereg('ASTblind',$row[0])) )
					{
					$donothing=1;
					}
				else
					{
					$hangup_channels[$total_hangup] = "$row[0]";
					$total_hangup++;
					}
				}
			if ($format=='debug') {echo "\n<!-- $row[0] -->";}
			$loop_count++; 
			}

		$loop_count=0;
		$stmt="SELECT channel FROM live_channels where server_ip = '$server_ip' and extension = '$conf_exten' order by channel desc;";
			if ($format=='debug') {echo "\n<!-- $stmt -->";}
		$rslt=mysql_query($stmt, $link);
			if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00184',$user,$server_ip,$session_name,$one_mysql_log);}
		if ($rslt) {$rec_list = mysql_num_rows($rslt);}
			while ($rec_list>$loop_count)
			{
			$row=mysql_fetch_row($rslt);
			if (preg_match("/Local\/$conf_silent_prefix$conf_exten\@/i",$row[0]))
				{
				$rec_channels[$total_rec] = "$row[0]";
				$total_rec++;
				}
			else
				{
		#		if (preg_match("/$agentchannel/i",$row[0]))
				if ( ($agentchannel == "$row[0]") or (ereg('ASTblind',$row[0])) )
					{
					$donothing=1;
					}
				else
					{
					$hangup_channels[$total_hangup] = "$row[0]";
					$total_hangup++;
					}
				}
			if ($format=='debug') {echo "\n<!-- $row[0] -->";}
			$loop_count++; 
			}


		### if a conference call or 3way call was attempted, then hangup all channels except for the agentchannel
		if ( ( ($conf_dialed > 0) or ($hangup_all_non_reserved > 0) ) and ($leaving_threeway < 1) and ($blind_transfer < 1) )
			{
			$loop_count=0;
			while($loop_count < $total_hangup)
				{
				if (strlen($hangup_channels[$loop_count])>5)
					{
					$stmt="INSERT INTO vicidial_manager values('','','$NOW_TIME','NEW','N','$server_ip','','Hangup','CH12346$StarTtime$loop_count','Channel: $hangup_channels[$loop_count]','','','','','','','','','');";
						if ($format=='debug') {echo "\n<!-- $stmt -->";}
					$rslt=mysql_query($stmt, $link);
			if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00096',$user,$server_ip,$session_name,$one_mysql_log);}
					}
				$loop_count++;
				}
			}

		$total_recFN=0;
		$loop_count=0;
		$filename=$MT;		# not necessary : and cmd_line_f LIKE \"%_$user_abb\"
		$stmt="SELECT cmd_line_f FROM vicidial_manager where server_ip='$server_ip' and action='Originate' and cmd_line_b = 'Channel: $local_DEF$conf_silent_prefix$conf_exten$local_AMP$ext_context' order by entry_date desc limit $total_rec;";
			if ($format=='debug') {echo "\n<!-- $stmt -->";}
		$rslt=mysql_query($stmt, $link);
			if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00097',$user,$server_ip,$session_name,$one_mysql_log);}
		if ($rslt) {$recFN_list = mysql_num_rows($rslt);}
			while ($recFN_list>$loop_count)
			{
			$row=mysql_fetch_row($rslt);
			$filename[$total_recFN] = preg_replace("/Callerid: /i","",$row[0]);
			if ($format=='debug') {echo "\n<!-- $row[0] -->";}
			$total_recFN++;
			$loop_count++; 
			}

		$loop_count=0;
		while($loop_count < $total_rec)
			{
			if (strlen($rec_channels[$loop_count])>5)
				{
				$stmt="INSERT INTO vicidial_manager values('','','$NOW_TIME','NEW','N','$server_ip','','Hangup','RH12345$StarTtime$loop_count','Channel: $rec_channels[$loop_count]','','','','','','','','','');";
					if ($format=='debug') {echo "\n<!-- $stmt -->";}
				$rslt=mysql_query($stmt, $link);
			if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00098',$user,$server_ip,$session_name,$one_mysql_log);}

				echo "REC_STOP|$rec_channels[$loop_count]|$filename[$loop_count]|";
				if (strlen($filename)>2)
					{
					$stmt="SELECT recording_id,start_epoch,vicidial_id,lead_id FROM recording_log where filename='$filename[$loop_count]'";
						if ($format=='debug') {echo "\n<!-- $stmt -->";}
					$rslt=mysql_query($stmt, $link);
			if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00099',$user,$server_ip,$session_name,$one_mysql_log);}
					if ($rslt) {$fn_count = mysql_num_rows($rslt);}
					if ($fn_count)
						{
						$row=mysql_fetch_row($rslt);
						$recording_id = $row[0];
						$start_time =	$row[1];
						$vicidial_id =	$row[2];
						$RClead_id =	$row[3];

						if ( (strlen($RClead_id)<1) or ($RClead_id < 1) or ($RClead_id=='NULL') )
							{$lidSQL = ",lead_id='$lead_id'";}
						if (strlen($vicidial_id)<1) 
							{$vidSQL = ",vicidial_id='$VDvicidial_id'";}
						else
							{
							if ( (ereg('.',$vicidial_id)) and ($VLA_inOUT == 'INBOUND') )
								{
								if (!ereg('.',$VDvicidial_id))
									{$vidSQL = ",vicidial_id='$VDvicidial_id'";}

								$fp = fopen ("./vicidial_debug.txt", "a");
								fwrite ($fp, "$NOW_TIME|INBND_LOG_3|$uniqueid|$lead_id|$user|$inOUT|$VLA_inOUT|$length_in_sec|$VDterm_reason|$VDvicidial_id|$vicidial_id|$start_epoch|$recording_id|\n");
								fclose($fp);
								}
							}
						$length_in_sec = ($StarTtime - $start_time);
						$length_in_min = ($length_in_sec / 60);
						$length_in_min = sprintf("%8.2f", $length_in_min);

						$stmt="UPDATE recording_log set end_time='$NOW_TIME',end_epoch='$StarTtime',length_in_sec=$length_in_sec,length_in_min='$length_in_min' $vidSQL $lidSQL where filename='$filename[$loop_count]' and end_epoch is NULL;";
							if ($format=='debug') {echo "\n<!-- $stmt -->";}
						$rslt=mysql_query($stmt, $link);
			if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00100',$user,$server_ip,$session_name,$one_mysql_log);}

						echo "$recording_id|$length_in_min|";

			#			$fp = fopen ("./recording_debug_$NOW_DATE$txt", "a");
			#			fwrite ($fp, "$NOW_TIME|RECORD_LOG|$filename[$loop_count]|$uniqueid|$lead_id|$user|$inOUT|$VLA_inOUT|$length_in_sec|$VDterm_reason|$VDvicidial_id|$VDvicidial_id|$vicidial_id|$start_epoch|$recording_id|$VDIDselect|\n");
			#			fclose($fp);
						}
					else {echo "||";}
					}
				else {echo "||";}
				echo "\n";
				}
			$loop_count++;
			}
		}


	$talk_sec=0;
	$talk_epochSQL='';
	$dead_secSQL='';
	$lead_id_commentsSQL='';
	$StarTtime = date("U");
	$stmt = "select talk_epoch,talk_sec,wait_sec,wait_epoch,lead_id,comments,dead_epoch from vicidial_agent_log where agent_log_id='$agent_log_id';";
	if ($DB) {echo "$stmt\n";}
	$rslt=mysql_query($stmt, $link);
			if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00101',$user,$server_ip,$session_name,$one_mysql_log);}
	$VDpr_ct = mysql_num_rows($rslt);
	if ($VDpr_ct > 0)
		{
		$row=mysql_fetch_row($rslt);
		if ( (eregi("NULL",$row[0])) or ($row[0] < 1000) )
			{
			$talk_epochSQL=",talk_epoch='$StarTtime'";
			$row[0]=$row[3];
			}
		if ( (!eregi("NULL",$row[6])) and ($row[6] > 1000) )
			{
			$dead_sec = ($StarTtime - $row[6]);
			if ($dead_sec < 0) {$dead_sec=0;}
			$dead_secSQL=",dead_sec='$dead_sec'";
			}
		$talk_sec = (($StarTtime - $row[0]) + $row[1]);
		if ( ( ($auto_dial_level < 1) or (preg_match('/^M/',$MDnextCID)) ) and (preg_match('/INBOUND_MAN/',$dial_method)) )
			{
			if ( (eregi("NULL",$row[5])) or (strlen($row[5]) < 1) )
				{
				$lead_id_commentsSQL .= ",comments='MANUAL'";
				}
			if ( (eregi("NULL",$row[4])) or ($row[4] < 1) or (strlen($row[4]) < 1) )
				{
				$lead_id_commentsSQL .= ",lead_id='$lead_id'";
				}
			}
		}
	$stmt="UPDATE vicidial_agent_log set talk_sec='$talk_sec',dispo_epoch='$StarTtime' $talk_epochSQL $dead_secSQL $lead_id_commentsSQL where agent_log_id='$agent_log_id';";
		if ($format=='debug') {echo "\n<!-- $stmt -->";}
	$rslt=mysql_query($stmt, $link);
			if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00102',$user,$server_ip,$session_name,$one_mysql_log);}

	### update vicidial_carrier_log to match uniqueIDs
	$beginUNIQUEID = preg_replace("/\..*/","",$uniqueid);
	$stmt="UPDATE vicidial_carrier_log set uniqueid='$uniqueid' where lead_id='$lead_id' and uniqueid LIKE \"$beginUNIQUEID%\";";
		if ($format=='debug') {echo "\n<!-- $stmt -->";}
	$rslt=mysql_query($stmt, $link);
			if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00299',$user,$server_ip,$session_name,$one_mysql_log);}
	}
}


################################################################################
### VDADREcheckINCOMING - for auto-dial VICIDiaL dialing this will recheck for
###                       calls to see if the channel has updated
################################################################################
if ($ACTION == 'VDADREcheckINCOMING')
	{
	$MT[0]='';
	$row='';   $rowx='';
	$channel_live=1;
	if ( (strlen($campaign)<1) || (strlen($server_ip)<1) || (strlen($lead_id)<1) )
		{
		$channel_live=0;
		echo "0\n";
		echo "Kampanj $campaign är ej giltig\n";
		echo "lead_id $lead_id är ej giltig\n";
		exit;
		}
	else
		{
		### grab the call and lead info from the vicidial_live_agents table
		$stmt = "SELECT lead_id,uniqueid,callerid,channel,call_server_ip FROM vicidial_live_agents where server_ip = '$server_ip' and user='$user' and campaign_id='$campaign' and lead_id='$lead_id';";
		if ($DB) {echo "$stmt\n";}
		$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00103',$user,$server_ip,$session_name,$one_mysql_log);}
		$queue_leadID_ct = mysql_num_rows($rslt);

		if ($queue_leadID_ct > 0)
			{
			$row=mysql_fetch_row($rslt);
			$lead_id	=$row[0];
			$uniqueid	=$row[1];
			$callerid	=$row[2];
			$channel	=$row[3];
			$call_server_ip	=$row[4];
				if (strlen($call_server_ip)<7) {$call_server_ip = $server_ip;}
			echo "1\n" . $lead_id . '|' . $uniqueid . '|' . $callerid . '|' . $channel . '|' . $call_server_ip . "|\n";
			}
		}
	}


################################################################################
### VDADcheckINCOMING - for auto-dial VICIDiaL dialing this will check for calls
###                     in the vicidial_live_agents table in QUEUE status, then
###                     lookup the lead info and pass it back to vicidial.php
################################################################################
if ($ACTION == 'VDADcheckINCOMING')
	{
	$VDCL_ingroup_recording_override = '';
	$VDCL_ingroup_rec_filename = '';
	$Ctype = 'A';
	$MT[0]='';
	$row='';   $rowx='';
	$channel_live=1;
	$alt_phone_code='';
	$alt_phone_number='';
	$alt_phone_note='';
	$alt_phone_active='';
	$alt_phone_count='';

	if ( (strlen($campaign)<1) || (strlen($server_ip)<1) )
		{
		$channel_live=0;
		echo "0\n";
		echo "Kampanj $campaign är ej giltig\n";
		exit;
		}
	else
		{
		### grab the call and lead info from the vicidial_live_agents table
		$stmt = "SELECT lead_id,uniqueid,callerid,channel,call_server_ip,comments FROM vicidial_live_agents where server_ip = '$server_ip' and user='$user' and campaign_id='$campaign' and status='QUEUE';";
		if ($DB) {echo "$stmt\n";}
		$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00104',$user,$server_ip,$session_name,$one_mysql_log);}
		$queue_leadID_ct = mysql_num_rows($rslt);

		if ($queue_leadID_ct > 0)
			{
			$row=mysql_fetch_row($rslt);
			$lead_id	=$row[0];
			$uniqueid	=$row[1];
			$callerid	=$row[2];
			$channel	=$row[3];
			$call_server_ip	=$row[4];
			$VLAcomments=$row[5];

				if (strlen($call_server_ip)<7) {$call_server_ip = $server_ip;}
			echo "1\n" . $lead_id . '|' . $uniqueid . '|' . $callerid . '|' . $channel . '|' . $call_server_ip . "|\n";

			##### grab number of calls today in this campaign and increment
			$stmt="SELECT calls_today FROM vicidial_live_agents WHERE user='$user' and campaign_id='$campaign';";
			$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00105',$user,$server_ip,$session_name,$one_mysql_log);}
			if ($DB) {echo "$stmt\n";}
			$vla_cc_ct = mysql_num_rows($rslt);
			if ($vla_cc_ct > 0)
				{
				$row=mysql_fetch_row($rslt);
				$calls_today =$row[0];
				}
			else
				{$calls_today ='0';}
			$calls_today++;

			### update the agent status to INCALL in vicidial_live_agents
			$stmt = "UPDATE vicidial_live_agents set status='INCALL',last_call_time='$NOW_TIME',calls_today='$calls_today',external_hangup=0,external_status='',external_pause='',external_dial='',last_state_change='$NOW_TIME' where user='$user' and server_ip='$server_ip';";
			if ($DB) {echo "$stmt\n";}
			$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {$errno = mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00106',$user,$server_ip,$session_name,$one_mysql_log);}
			$retry_count=0;
			while ( ($errno > 0) and ($retry_count < 9) )
				{
				$rslt=mysql_query($stmt, $link);
				$one_mysql_log=1;
				$errno = mysql_error_logging($NOW_TIME,$link,$mel,$stmt,"9106$retry_count",$user,$server_ip,$session_name,$one_mysql_log);
				$one_mysql_log=0;
				$retry_count++;
				}

			$stmt = "UPDATE vicidial_campaign_agents set calls_today='$calls_today' where user='$user' and campaign_id='$campaign';";
			if ($DB) {echo "$stmt\n";}
			$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00107',$user,$server_ip,$session_name,$one_mysql_log);}

			##### grab the data from vicidial_list for the lead_id
			$stmt="SELECT lead_id,entry_date,modify_date,status,user,vendor_lead_code,source_id,list_id,gmt_offset_now,called_since_last_reset,phone_code,phone_number,title,first_name,middle_initial,last_name,address1,address2,address3,city,state,province,postal_code,country_code,gender,date_of_birth,alt_phone,email,security_phrase,comments,called_count,last_local_call_time,rank,owner FROM vicidial_list where lead_id='$lead_id' LIMIT 1;";
			$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00108',$user,$server_ip,$session_name,$one_mysql_log);}
			if ($DB) {echo "$stmt\n";}
			$list_lead_ct = mysql_num_rows($rslt);
			if ($list_lead_ct > 0)
				{
				$row=mysql_fetch_row($rslt);
			#	$lead_id		= trim("$row[0]");
				$dispo			= trim("$row[3]");
				$tsr			= trim("$row[4]");
				$vendor_id		= trim("$row[5]");
				$source_id		= trim("$row[6]");
				$list_id		= trim("$row[7]");
				$gmt_offset_now	= trim("$row[8]");
				$phone_code		= trim("$row[10]");
				$phone_number	= trim("$row[11]");
				$title			= trim("$row[12]");
				$first_name		= trim("$row[13]");
				$middle_initial	= trim("$row[14]");
				$last_name		= trim("$row[15]");
				$address1		= trim("$row[16]");
				$address2		= trim("$row[17]");
				$address3		= trim("$row[18]");
				$city			= trim("$row[19]");
				$state			= trim("$row[20]");
				$province		= trim("$row[21]");
				$postal_code	= trim("$row[22]");
				$country_code	= trim("$row[23]");
				$gender			= trim("$row[24]");
				$date_of_birth	= trim("$row[25]");
				$alt_phone		= trim("$row[26]");
				$email			= trim("$row[27]");
				$security		= trim("$row[28]");
				$comments		= stripslashes(trim("$row[29]"));
				$called_count	= trim("$row[30]");
				$rank			= trim("$row[32]");
				$owner			= trim("$row[33]");
				}

			##### if lead is a callback, grab the callback comments
			$CBentry_time =		'';
			$CBcallback_time =	'';
			$CBuser =			'';
			$CBcomments =		'';
			if (ereg("CALLBK",$dispo))
				{
				$stmt="SELECT entry_time,callback_time,user,comments FROM vicidial_callbacks where lead_id='$lead_id' order by callback_id desc LIMIT 1;";
				$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00109',$user,$server_ip,$session_name,$one_mysql_log);}
				if ($DB) {echo "$stmt\n";}
				$cb_record_ct = mysql_num_rows($rslt);
				if ($cb_record_ct > 0)
					{
					$row=mysql_fetch_row($rslt);
					$CBentry_time =		trim("$row[0]");
					$CBcallback_time =	trim("$row[1]");
					$CBuser =			trim("$row[2]");
					$CBcomments =		trim("$row[3]");
					}
				}

			### update the lead status to INCALL
			$stmt = "UPDATE vicidial_list set status='INCALL', user='$user' where lead_id='$lead_id';";
			if ($DB) {echo "$stmt\n";}
			$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00110',$user,$server_ip,$session_name,$one_mysql_log);}

			### update the log status to INCALL
			$user_group='';
			$stmt="SELECT user_group FROM vicidial_users where user='$user' LIMIT 1;";
			$rslt=mysql_query($stmt, $link);
			if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00111',$user,$server_ip,$session_name,$one_mysql_log);}
			if ($DB) {echo "$stmt\n";}
			$ug_record_ct = mysql_num_rows($rslt);
			if ($ug_record_ct > 0)
				{
				$row=mysql_fetch_row($rslt);
				$user_group =		trim("$row[0]");
				}


			$stmt = "SELECT campaign_id,phone_number,alt_dial,call_type from vicidial_auto_calls where callerid = '$callerid' order by call_time desc limit 1;";
			if ($DB) {echo "$stmt\n";}
			$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00112',$user,$server_ip,$session_name,$one_mysql_log);}
			$VDAC_cid_ct = mysql_num_rows($rslt);
			if ($VDAC_cid_ct > 0)
				{
				$row=mysql_fetch_row($rslt);
				$VDADchannel_group	=$row[0];
				$dialed_number		=$row[1];
				$dialed_label		=$row[2];
				$call_type			=$row[3];
				if ( ($dialed_number != $phone_number) and (strlen($dialed_label) < 3) )
					{
					if ($dialed_number != $alt_phone)
						{
						if ($dialed_number != $address3)
							{
							$dialed_label = 'X1';
							$stmt = "SELECT alt_phone_count from vicidial_list_alt_phones where lead_id='$lead_id' and phone_number = '$dialed_number' order by alt_phone_count limit 1;";
							if ($DB) {echo "$stmt\n";}
							$rslt=mysql_query($stmt, $link);
								if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00248',$user,$server_ip,$session_name,$one_mysql_log);}
							$VDAP_cid_ct = mysql_num_rows($rslt);
							if ($VDAP_cid_ct > 0)
								{
								$row=mysql_fetch_row($rslt);
								$Xalt_phone_count	=$row[0];

								$stmt = "SELECT count(*) from vicidial_list_alt_phones where lead_id='$lead_id';";
								if ($DB) {echo "$stmt\n";}
								$rslt=mysql_query($stmt, $link);
									if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00249',$user,$server_ip,$session_name,$one_mysql_log);}
								$VDAPct_cid_ct = mysql_num_rows($rslt);
								if ($VDAPct_cid_ct > 0)
									{
									$row=mysql_fetch_row($rslt);
									$COUNTalt_phone_count	=$row[0];

									if ($COUNTalt_phone_count <= $Xalt_phone_count)
										{$dialed_label = 'XLAST';}
									else
										{$dialed_label = "X$Xalt_phone_count";}
									}

								}
							}
						else
							{$dialed_label = 'ADDR3';}
						}
					else
						{$dialed_label = 'ALT';}
					}
				}
			else
				{
				$dialed_number = $phone_number;
				$dialed_label = 'MAIN';
				if (preg_match('/^M|^V/',$callerid))
					{
					$call_type = 'OUT';
					$VDADchannel_group = $campaign;
					}
				else
					{
					$call_type = 'IN';
					$stmt = "SELECT campaign_id from vicidial_closer_log where lead_id = '$lead_id' order by call_date desc limit 1;";
					if ($DB) {echo "$stmt\n";}
					$rslt=mysql_query($stmt, $link);
						if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00183',$user,$server_ip,$session_name,$one_mysql_log);}
					$VDCL_mvac_ct = mysql_num_rows($rslt);
					if ($VDCL_mvac_ct > 0)
						{
						$row=mysql_fetch_row($rslt);
						$VDADchannel_group	=$row[0];
						}
					}
				if ($WeBRooTWritablE > 0)
					{
					$fp = fopen ("./vicidial_debug.txt", "a");
					fwrite ($fp, "$NOW_TIME|INBND|$callerid|$user|$user_group|$list_id|$lead_id|$phone_number|$uniqueid|$VDADchannel_group|$call_type|$dialed_number|$dialed_label\n");
					fclose($fp);
					}
				}

			if ( ($call_type=='OUT') or ($call_type=='OUTBALANCE') )
				{
				$stmt = "UPDATE vicidial_log set user='$user', comments='AUTO', list_id='$list_id', status='INCALL', user_group='$user_group' where lead_id='$lead_id' and uniqueid='$uniqueid';";
				if ($DB) {echo "$stmt\n";}
				$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00113',$user,$server_ip,$session_name,$one_mysql_log);}

				$script_recording_delay=0;
				##### grab number of calls today in this campaign and increment
				$stmt="SELECT count(*) FROM vicidial_scripts vs,vicidial_campaigns vc WHERE campaign_id='$campaign' and vs.script_id=vc.campaign_script and script_text LIKE \"%--A--recording_%\";";
				$rslt=mysql_query($stmt, $link);
					if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00261',$user,$server_ip,$session_name,$one_mysql_log);}
				if ($DB) {echo "$stmt\n";}
				$vs_vc_ct = mysql_num_rows($rslt);
				if ($vs_vc_ct > 0)
					{
					$row=mysql_fetch_row($rslt);
					$script_recording_delay = $row[0];
					}

				$stmt = "SELECT campaign_script,get_call_launch,xferconf_a_dtmf,xferconf_a_number,xferconf_b_dtmf,xferconf_b_number,default_xfer_group,start_call_url,dispo_call_url,xferconf_c_number,xferconf_d_number,xferconf_e_number from vicidial_campaigns where campaign_id='$campaign';";
				if ($DB) {echo "$stmt\n";}
				$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00114',$user,$server_ip,$session_name,$one_mysql_log);}
				$VDIG_cid_ct = mysql_num_rows($rslt);
				if ($VDIG_cid_ct > 0)
					{
					$row=mysql_fetch_row($rslt);
					$VDCL_campaign_script =		$row[0];
					$VDCL_get_call_launch =		$row[1];
					$VDCL_xferconf_a_dtmf =		$row[2];
					$VDCL_xferconf_a_number =	$row[3];
					$VDCL_xferconf_b_dtmf =		$row[4];
					$VDCL_xferconf_b_number =	$row[5];
					$VDCL_default_xfer_group =	$row[6];
					if (strlen($VDCL_default_xfer_group)<2) {$VDCL_default_xfer_group='X';}
					$VDCL_start_call_url =		$row[7];
					$VDCL_dispo_call_url =		$row[8];
					$VDCL_xferconf_c_number =	$row[9];
					$VDCL_xferconf_d_number =	$row[10];
					$VDCL_xferconf_e_number =	$row[11];
					}

				### Check for List ID override settings
				if (strlen($list_id)>0)
					{
					$stmt = "select xferconf_a_number,xferconf_b_number,xferconf_c_number,xferconf_d_number,xferconf_e_number from vicidial_lists where list_id='$list_id';";
					if ($DB) {echo "$stmt\n";}
					$rslt=mysql_query($stmt, $link);
					if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00281',$user,$server_ip,$session_name,$one_mysql_log);}
					$VDIG_xferOR_ct = mysql_num_rows($rslt);
					if ($VDIG_xferOR_ct > 0)
						{
						$row=mysql_fetch_row($rslt);
						if (strlen($row[0]) > 0)
							{$VDCL_xferconf_a_number =	$row[0];}
						if (strlen($row[1]) > 0)
							{$VDCL_xferconf_b_number =	$row[1];}
						if (strlen($row[2]) > 0)
							{$VDCL_xferconf_c_number =	$row[2];}
						if (strlen($row[3]) > 0)
							{$VDCL_xferconf_d_number =	$row[3];}
						if (strlen($row[4]) > 0)
							{$VDCL_xferconf_e_number =	$row[4];}
						}
					}

				echo "|||||$VDCL_campaign_script|$VDCL_get_call_launch|$VDCL_xferconf_a_dtmf|$VDCL_xferconf_a_number|$VDCL_xferconf_b_dtmf|$VDCL_xferconf_b_number|$VDCL_default_xfer_group|X|X|||||$VDCL_xferconf_c_number|$VDCL_xferconf_d_number|$VDCL_xferconf_e_number\n|\n";
				
				if (ereg('X',$dialed_label))
					{
					if (ereg('LAST',$dialed_label))
						{
						$stmt = "SELECT phone_code,phone_number,alt_phone_note,active,alt_phone_count FROM vicidial_list_alt_phones where lead_id='$lead_id' order by alt_phone_count desc limit 1;";
						}
					else
						{
						$Talt_dial = ereg_replace("[^0-9]","",$dialed_label);
						$stmt = "SELECT phone_code,phone_number,alt_phone_note,active,alt_phone_count FROM vicidial_list_alt_phones where lead_id='$lead_id' and alt_phone_count='$Talt_dial';";										
						}

					if ($DB) {echo "$stmt\n";}
					$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00116',$user,$server_ip,$session_name,$one_mysql_log);}
					$VLAP_ct = mysql_num_rows($rslt);
					if ($VLAP_ct > 0)
						{
						$row=mysql_fetch_row($rslt);
						$alt_phone_code	=	$row[0];
						$alt_phone_number = $row[1];
						$alt_phone_note =	$row[2];
						$alt_phone_active = $row[3];
						$alt_phone_count =	$row[4];
						}
					}
				}
			else
				{
				### update the vicidial_closer_log user to INCALL
				$stmt = "UPDATE vicidial_closer_log set user='$user', comments='AUTO', list_id='$list_id', status='INCALL', user_group='$user_group' where lead_id='$lead_id' order by closecallid desc limit 1;";
				if ($DB) {echo "$stmt\n";}
				$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00117',$user,$server_ip,$session_name,$one_mysql_log);}

				$stmt = "select count(*) from vicidial_log where lead_id='$lead_id' and uniqueid='$uniqueid';";
				if ($DB) {echo "$stmt\n";}
				$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00118',$user,$server_ip,$session_name,$one_mysql_log);}
				$VDL_cid_ct = mysql_num_rows($rslt);
				if ($VDL_cid_ct > 0)
					{
					$row=mysql_fetch_row($rslt);
					$VDCL_front_VDlog	=$row[0];
					}

				$script_recording_delay=0;
				##### find if script contains recording fields
				$stmt="SELECT count(*) FROM vicidial_scripts vs,vicidial_inbound_groups vig WHERE group_id='$VDADchannel_group' and vs.script_id=vig.ingroup_script and script_text LIKE \"%--A--recording_%\";";
				$rslt=mysql_query($stmt, $link);
					if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00262',$user,$server_ip,$session_name,$one_mysql_log);}
				if ($DB) {echo "$stmt\n";}
				$vs_vc_ct = mysql_num_rows($rslt);
				if ($vs_vc_ct > 0)
					{
					$row=mysql_fetch_row($rslt);
					$script_recording_delay = $row[0];
					}

				$stmt = "select group_name,group_color,web_form_address,fronter_display,ingroup_script,get_call_launch,xferconf_a_dtmf,xferconf_a_number,xferconf_b_dtmf,xferconf_b_number,default_xfer_group,ingroup_recording_override,ingroup_rec_filename,default_group_alias,web_form_address_two,timer_action,timer_action_message,timer_action_seconds,start_call_url,dispo_call_url,xferconf_c_number,xferconf_d_number,xferconf_e_number from vicidial_inbound_groups where group_id='$VDADchannel_group';";
				if ($DB) {echo "$stmt\n";}
				$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00119',$user,$server_ip,$session_name,$one_mysql_log);}
				$VDIG_cid_ct = mysql_num_rows($rslt);
				if ($VDIG_cid_ct > 0)
					{
					$row=mysql_fetch_row($rslt);
					$VDCL_group_name =					$row[0];
					$VDCL_group_color =					$row[1];
					$VDCL_group_web	=					stripslashes($row[2]);
					$VDCL_fronter_display =				$row[3];
					$VDCL_ingroup_script =				$row[4];
					$VDCL_get_call_launch =				$row[5];
					$VDCL_xferconf_a_dtmf =				$row[6];
					$VDCL_xferconf_a_number =			$row[7];
					$VDCL_xferconf_b_dtmf =				$row[8];
					$VDCL_xferconf_b_number =			$row[9];
					$VDCL_default_xfer_group =			$row[10];
					$VDCL_ingroup_recording_override =	$row[11];
					$VDCL_ingroup_rec_filename =		$row[12];
					$VDCL_default_group_alias =			$row[13];
					$VDCL_group_web_two =		stripslashes($row[14]);
					$VDCL_timer_action =				$row[15];
					$VDCL_timer_action_message =		$row[16];
					$VDCL_timer_action_seconds =		$row[17];
					$VDCL_start_call_url =				$row[18];
					$VDCL_dispo_call_url =				$row[19];
					$VDCL_xferconf_c_number =			$row[20];
					$VDCL_xferconf_d_number =			$row[21];
					$VDCL_xferconf_e_number =			$row[22];

					$stmt = "select campaign_script,xferconf_a_dtmf,xferconf_a_number,xferconf_b_dtmf,xferconf_b_number,default_group_alias,timer_action,timer_action_message,timer_action_seconds,start_call_url,dispo_call_url,xferconf_c_number,xferconf_d_number,xferconf_e_number from vicidial_campaigns where campaign_id='$campaign';";
					if ($DB) {echo "$stmt\n";}
					$rslt=mysql_query($stmt, $link);
					if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00181',$user,$server_ip,$session_name,$one_mysql_log);}
					$VDIG_cidOR_ct = mysql_num_rows($rslt);
					if ($VDIG_cidOR_ct > 0)
						{
						$row=mysql_fetch_row($rslt);
						if (strlen($VDCL_xferconf_a_dtmf) < 1)
							{$VDCL_xferconf_a_dtmf =	$row[1];}
						if (strlen($VDCL_xferconf_a_number) < 1)
							{$VDCL_xferconf_a_number =	$row[2];}
						if (strlen($VDCL_xferconf_b_dtmf) < 1)
							{$VDCL_xferconf_b_dtmf =	$row[3];}
						if (strlen($VDCL_xferconf_b_number) < 1)
							{$VDCL_xferconf_b_number =	$row[4];}
						if (strlen($VDCL_default_group_alias) < 1)
							{$VDCL_default_group_alias =	$row[5];}
						if (strlen($VDCL_timer_action) < 1)
							{$VDCL_timer_action =	$row[6];}
						if (strlen($VDCL_timer_action_message) < 1)
							{$VDCL_timer_action_message =	$row[7];}
						if (strlen($VDCL_timer_action_seconds) < 1)
							{$VDCL_timer_action_seconds =	$row[8];}
						if (strlen($VDCL_start_call_url) < 1)
							{$VDCL_start_call_url =	$row[9];}
						if (strlen($VDCL_dispo_call_url) < 1)
							{$VDCL_dispo_call_url =	$row[10];}
						if (strlen($VDCL_xferconf_c_number) < 1)
							{$VDCL_xferconf_c_number =	$row[11];}
						if (strlen($VDCL_xferconf_d_number) < 1)
							{$VDCL_xferconf_d_number =	$row[12];}
						if (strlen($VDCL_xferconf_e_number) < 1)
							{$VDCL_xferconf_e_number =	$row[13];}

						if ( ( (ereg('NONE',$VDCL_ingroup_script)) and (strlen($VDCL_ingroup_script) < 5) ) or (strlen($VDCL_ingroup_script) < 1) )
							{
							$VDCL_ingroup_script =		$row[0];
							$script_recording_delay=0;
							##### find if script contains recording fields
							$stmt="SELECT count(*) FROM vicidial_scripts vs,vicidial_campaigns vc WHERE campaign_id='$campaign' and vs.script_id=vc.campaign_script and script_text LIKE \"%--A--recording_%\";";
							$rslt=mysql_query($stmt, $link);
								if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00263',$user,$server_ip,$session_name,$one_mysql_log);}
							if ($DB) {echo "$stmt\n";}
							$vs_vc_ct = mysql_num_rows($rslt);
							if ($vs_vc_ct > 0)
								{
								$row=mysql_fetch_row($rslt);
								$script_recording_delay = $row[0];
								}
							}
						}

					$stmt = "select group_web_vars from vicidial_inbound_group_agents where group_id='$VDADchannel_group' and user='$user';";
					if ($DB) {echo "$stmt\n";}
					$rslt=mysql_query($stmt, $link);
					if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00188',$user,$server_ip,$session_name,$one_mysql_log);}
					$VDIG_cidgwv_ct = mysql_num_rows($rslt);
					if ($VDIG_cidgwv_ct > 0)
						{
						$row=mysql_fetch_row($rslt);
						$VDCL_group_web_vars =	$row[0];
						}

					if (strlen($VDCL_group_web_vars) < 1)
						{
						$stmt = "select group_web_vars from vicidial_campaign_agents where campaign_id='$campaign' and user='$user';";
						if ($DB) {echo "$stmt\n";}
						$rslt=mysql_query($stmt, $link);
						if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00189',$user,$server_ip,$session_name,$one_mysql_log);}
						$VDIG_cidogwv = mysql_num_rows($rslt);
						if ($VDIG_cidogwv > 0)
							{
							$row=mysql_fetch_row($rslt);
							$VDCL_group_web_vars =	$row[0];
							}
						}

					### update the comments in vicidial_live_agents record
					$stmt = "UPDATE vicidial_live_agents set comments='INBOUND' where user='$user' and server_ip='$server_ip';";
					if ($DB) {echo "$stmt\n";}
					$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00120',$user,$server_ip,$session_name,$one_mysql_log);}

					$Ctype = 'I';
					}
				else
					{
					$stmt = "select campaign_script,get_call_launch,xferconf_a_dtmf,xferconf_a_number,xferconf_b_dtmf,xferconf_b_number,default_group_alias,timer_action,timer_action_message,timer_action_seconds,start_call_url,dispo_call_url,xferconf_c_number,xferconf_d_number,xferconf_e_number from vicidial_campaigns where campaign_id='$VDADchannel_group';";
					if ($DB) {echo "$stmt\n";}
					$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00121',$user,$server_ip,$session_name,$one_mysql_log);}
					$VDIG_cid_ct = mysql_num_rows($rslt);
					if ($VDIG_cid_ct > 0)
						{
						$row=mysql_fetch_row($rslt);
						$VDCL_ingroup_script	=		$row[0];
						$VDCL_get_call_launch	=		$row[1];
						$VDCL_xferconf_a_dtmf	=		$row[2];
						$VDCL_xferconf_a_number	=		$row[3];
						$VDCL_xferconf_b_dtmf	=		$row[4];
						$VDCL_xferconf_b_number	=		$row[5];
						$VDCL_default_group_alias =		$row[6];
						$VDCL_timer_action = 			$row[7];
						$VDCL_timer_action_message = 	$row[8];
						$VDCL_timer_action_seconds = 	$row[9];
						$VDCL_start_call_url =			$row[10];
						$VDCL_dispo_call_url =			$row[11];
						$VDCL_xferconf_c_number =		$row[12];
						$VDCL_xferconf_d_number =		$row[13];
						$VDCL_xferconf_e_number =		$row[14];
						}

					$script_recording_delay=0;
					##### find if script contains recording fields
					$stmt="SELECT count(*) FROM vicidial_scripts vs,vicidial_campaigns vc WHERE campaign_id='$VDADchannel_group' and vs.script_id=vc.campaign_script and script_text LIKE \"%--A--recording_%\";";
					$rslt=mysql_query($stmt, $link);
						if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00264',$user,$server_ip,$session_name,$one_mysql_log);}
					if ($DB) {echo "$stmt\n";}
					$vs_vc_ct = mysql_num_rows($rslt);
					if ($vs_vc_ct > 0)
						{
						$row=mysql_fetch_row($rslt);
						$script_recording_delay = $row[0];
						}

					$stmt = "select group_web_vars from vicidial_campaign_agents where campaign_id='$VDADchannel_group' and user='$user';";
					if ($DB) {echo "$stmt\n";}
					$rslt=mysql_query($stmt, $link);
					if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00190',$user,$server_ip,$session_name,$one_mysql_log);}
					$VDIG_cidogwv = mysql_num_rows($rslt);
					if ($VDIG_cidogwv > 0)
						{
						$row=mysql_fetch_row($rslt);
						$VDCL_group_web_vars =	$row[0];
						}
					}

				$VDCL_caller_id_number='';
				if (strlen($VDCL_default_group_alias)>1)
					{
					$stmt = "select caller_id_number from groups_alias where group_alias_id='$VDCL_default_group_alias';";
					if ($DB) {echo "$stmt\n";}
					$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00187',$user,$server_ip,$session_name,$one_mysql_log);}
					$VDIG_cidnum_ct = mysql_num_rows($rslt);
					if ($VDIG_cidnum_ct > 0)
						{
						$row=mysql_fetch_row($rslt);
						$VDCL_caller_id_number	= $row[0];
						}
					}

				### Check for List ID override settings
				if (strlen($list_id)>0)
					{
					$stmt = "select xferconf_a_number,xferconf_b_number,xferconf_c_number,xferconf_d_number,xferconf_e_number from vicidial_lists where list_id='$list_id';";
					if ($DB) {echo "$stmt\n";}
					$rslt=mysql_query($stmt, $link);
					if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00282',$user,$server_ip,$session_name,$one_mysql_log);}
					$VDIG_cidOR_ct = mysql_num_rows($rslt);
					if ($VDIG_cidOR_ct > 0)
						{
						$row=mysql_fetch_row($rslt);
						if (strlen($row[0]) > 0)
							{$VDCL_xferconf_a_number =	$row[0];}
						if (strlen($row[1]) > 0)
							{$VDCL_xferconf_b_number =	$row[1];}
						if (strlen($row[2]) > 0)
							{$VDCL_xferconf_c_number =	$row[2];}
						if (strlen($row[3]) > 0)
							{$VDCL_xferconf_d_number =	$row[3];}
						if (strlen($row[4]) > 0)
							{$VDCL_xferconf_e_number =	$row[4];}
						}
					}

				### if web form is set then send på to vicidial.php for override of WEB_FORM address
				if ( (strlen($VDCL_group_web)>5) or (strlen($VDCL_group_name)>0) ) {echo "$VDCL_group_web|$VDCL_group_name|$VDCL_group_color|$VDCL_fronter_display|$VDADchannel_group|$VDCL_ingroup_script|$VDCL_get_call_launch|$VDCL_xferconf_a_dtmf|$VDCL_xferconf_a_number|$VDCL_xferconf_b_dtmf|$VDCL_xferconf_b_number|$VDCL_default_xfer_group|$VDCL_ingroup_recording_override|$VDCL_ingroup_rec_filename|$VDCL_default_group_alias|$VDCL_caller_id_number|$VDCL_group_web_vars|$VDCL_group_web_two|$VDCL_timer_action|$VDCL_timer_action_message|$VDCL_timer_action_seconds|$VDCL_xferconf_c_number|$VDCL_xferconf_d_number|$VDCL_xferconf_e_number|\n";}
				else {echo "X|$VDCL_group_name|$VDCL_group_color|$VDCL_fronter_display|$VDADchannel_group|$VDCL_ingroup_script|$VDCL_get_call_launch|$VDCL_xferconf_a_dtmf|$VDCL_xferconf_a_number|$VDCL_xferconf_b_dtmf|$VDCL_xferconf_b_number|$VDCL_default_xfer_group|$VDCL_ingroup_recording_override|$VDCL_ingroup_rec_filename|$VDCL_default_group_alias|$VDCL_caller_id_number|$VDCL_group_web_vars|$VDCL_group_web_two|$VDCL_timer_action|$VDCL_timer_action_message|$VDCL_timer_action_seconds|$VDCL_xferconf_c_number|$VDCL_xferconf_d_number|$VDCL_xferconf_e_number|\n";}

				$stmt = "SELECT full_name from vicidial_users where user='$tsr';";
				if ($DB) {echo "$stmt\n";}
				$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00122',$user,$server_ip,$session_name,$one_mysql_log);}
				$VDU_cid_ct = mysql_num_rows($rslt);
				if ($VDU_cid_ct > 0)
					{
					$row=mysql_fetch_row($rslt);
					$fronter_full_name		= $row[0];
					echo $fronter_full_name . '|' . $tsr . "\n";
					}
				else {echo '|' . $tsr . "\n";}
				}

			##### find if script contains recording fields
			$stmt="SELECT count(*) FROM vicidial_lists WHERE list_id='$list_id' and agent_script_override!='' and agent_script_override IS NOT NULL and agent_script_override!='NONE';";
			$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00265',$user,$server_ip,$session_name,$one_mysql_log);}
			if ($DB) {echo "$stmt\n";}
			$vls_vc_ct = mysql_num_rows($rslt);
			if ($vls_vc_ct > 0)
				{
				$row=mysql_fetch_row($rslt);
				if ($row[0] > 0)
					{
					$script_recording_delay=0;
					##### find if script contains recording fields
					$stmt="SELECT count(*) FROM vicidial_scripts vs,vicidial_lists vls WHERE list_id='$list_id' and vs.script_id=vls.agent_script_override and script_text LIKE \"%--A--recording_%\";";
					$rslt=mysql_query($stmt, $link);
						if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00266',$user,$server_ip,$session_name,$one_mysql_log);}
					if ($DB) {echo "$stmt\n";}
					$vs_vc_ct = mysql_num_rows($rslt);
					if ($vs_vc_ct > 0)
						{
						$row=mysql_fetch_row($rslt);
						$script_recording_delay = $row[0];
						}
					}
				}

			$comments = eregi_replace("\r",'',$comments);
			$comments = eregi_replace("\n",'!N',$comments);

			$LeaD_InfO =	$callerid . "\n";
			$LeaD_InfO .=	$lead_id . "\n";
			$LeaD_InfO .=	$dispo . "\n";
			$LeaD_InfO .=	$tsr . "\n";
			$LeaD_InfO .=	$vendor_id . "\n";
			$LeaD_InfO .=	$list_id . "\n";
			$LeaD_InfO .=	$gmt_offset_now . "\n";
			$LeaD_InfO .=	$phone_code . "\n";
			$LeaD_InfO .=	$phone_number . "\n";
			$LeaD_InfO .=	$title . "\n";
			$LeaD_InfO .=	$first_name . "\n";
			$LeaD_InfO .=	$middle_initial . "\n";
			$LeaD_InfO .=	$last_name . "\n";
			$LeaD_InfO .=	$address1 . "\n";
			$LeaD_InfO .=	$address2 . "\n";
			$LeaD_InfO .=	$address3 . "\n";
			$LeaD_InfO .=	$city . "\n";
			$LeaD_InfO .=	$state . "\n";
			$LeaD_InfO .=	$province . "\n";
			$LeaD_InfO .=	$postal_code . "\n";
			$LeaD_InfO .=	$country_code . "\n";
			$LeaD_InfO .=	$gender . "\n";
			$LeaD_InfO .=	$date_of_birth . "\n";
			$LeaD_InfO .=	$alt_phone . "\n";
			$LeaD_InfO .=	$email . "\n";
			$LeaD_InfO .=	$security . "\n";
			$LeaD_InfO .=	$comments . "\n";
			$LeaD_InfO .=	$called_count . "\n";
			$LeaD_InfO .=	$CBentry_time . "\n";
			$LeaD_InfO .=	$CBcallback_time . "\n";
			$LeaD_InfO .=	$CBuser . "\n";
			$LeaD_InfO .=	$CBcomments . "\n";
			$LeaD_InfO .=	$dialed_number . "\n";
			$LeaD_InfO .=	$dialed_label . "\n";
			$LeaD_InfO .=	$source_id . "\n";
			$LeaD_InfO .=	$alt_phone_code . "\n";
			$LeaD_InfO .=	$alt_phone_number . "\n";
			$LeaD_InfO .=	$alt_phone_note . "\n";
			$LeaD_InfO .=	$alt_phone_active . "\n";
			$LeaD_InfO .=	$alt_phone_count . "\n";
			$LeaD_InfO .=	$rank . "\n";
			$LeaD_InfO .=	$owner . "\n";
			$LeaD_InfO .=	$script_recording_delay . "\n";

			echo $LeaD_InfO;



			$wait_sec=0;
			$StarTtime = date("U");
			$stmt = "select wait_epoch,wait_sec from vicidial_agent_log where agent_log_id='$agent_log_id';";
			if ($DB) {echo "$stmt\n";}
			$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00123',$user,$server_ip,$session_name,$one_mysql_log);}
			$VDpr_ct = mysql_num_rows($rslt);
			if ($VDpr_ct > 0)
				{
				$row=mysql_fetch_row($rslt);
				$wait_sec = (($StarTtime - $row[0]) + $row[1]);
				}
			$stmt="UPDATE vicidial_agent_log set wait_sec='$wait_sec',talk_epoch='$StarTtime',lead_id='$lead_id' where agent_log_id='$agent_log_id';";
				if ($format=='debug') {echo "\n<!-- $stmt -->";}
			$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00124',$user,$server_ip,$session_name,$one_mysql_log);}

			### If SAMTALBK, change vicidial_callback record to INACTIVE
			if (eregi("CALLBK|CBHOLD", $dispo))
				{
				$stmt="UPDATE vicidial_callbacks set status='INACTIVE' where lead_id='$lead_id' and status NOT IN('INACTIVE','DEAD','ARCHIVE');";
					if ($format=='debug') {echo "\n<!-- $stmt -->";}
				$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00125',$user,$server_ip,$session_name,$one_mysql_log);}
				}

			##### check if system is set to generate logfile for transfers
			$stmt="SELECT enable_agc_xfer_log FROM system_settings;";
			$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00126',$user,$server_ip,$session_name,$one_mysql_log);}
			if ($DB) {echo "$stmt\n";}
			$enable_agc_xfer_log_ct = mysql_num_rows($rslt);
			if ($enable_agc_xfer_log_ct > 0)
				{
				$row=mysql_fetch_row($rslt);
				$enable_agc_xfer_log =$row[0];
				}

			if ( ($WeBRooTWritablE > 0) and ($enable_agc_xfer_log > 0) )
				{
				#	DATETIME|campaign|lead_id|phone_number|user|type
				#	2007-08-22 11:11:11|TESTCAMP|65432|3125551212|1234|A
				$fp = fopen ("./xfer_log.txt", "a");
				fwrite ($fp, "$NOW_TIME|$campaign|$lead_id|$phone_number|$user|$Ctype|$callerid|$uniqueid|$province\n");
				fclose($fp);
				}

			### Issue Start Call URL if defined
			if (strlen($VDCL_start_call_url) > 7)
				{
				if (eregi('--A--user_custom_',$VDCL_start_call_url))
					{
					$stmt = "select custom_one,custom_two,custom_three,custom_four,custom_five from vicidial_users where user='$user';";
					if ($DB) {echo "$stmt\n";}
					$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00283',$user,$server_ip,$session_name,$one_mysql_log);}
					$VUC_ct = mysql_num_rows($rslt);
					if ($VUC_ct > 0)
						{
						$row=mysql_fetch_row($rslt);
						$user_custom_one	=		urlencode(trim($row[0]));
						$user_custom_two	=		urlencode(trim($row[1]));
						$user_custom_three	=		urlencode(trim($row[2]));
						$user_custom_four	=		urlencode(trim($row[3]));
						$user_custom_five	=		urlencode(trim($row[4]));
						}
					}
				$VDCL_start_call_url = preg_replace('/^VAR/','',$VDCL_start_call_url);
				$VDCL_start_call_url = eregi_replace('--A--lead_id--B--',urlencode(trim($lead_id)),$VDCL_start_call_url);
				$VDCL_start_call_url = eregi_replace('--A--vendor_id--B--',urlencode(trim($vendor_id)),$VDCL_start_call_url);
				$VDCL_start_call_url = eregi_replace('--A--vendor_lead_code--B--',urlencode(trim($vendor_id)),$VDCL_start_call_url);
				$VDCL_start_call_url = eregi_replace('--A--list_id--B--',urlencode(trim($list_id)),$VDCL_start_call_url);
				$VDCL_start_call_url = eregi_replace('--A--gmt_offset_now--B--',urlencode(trim($gmt_offset_now)),$VDCL_start_call_url);
				$VDCL_start_call_url = eregi_replace('--A--phone_code--B--',urlencode(trim($phone_code)),$VDCL_start_call_url);
				$VDCL_start_call_url = eregi_replace('--A--phone_number--B--',urlencode(trim($phone_number)),$VDCL_start_call_url);
				$VDCL_start_call_url = eregi_replace('--A--title--B--',urlencode(trim($title)),$VDCL_start_call_url);
				$VDCL_start_call_url = eregi_replace('--A--first_name--B--',urlencode(trim($first_name)),$VDCL_start_call_url);
				$VDCL_start_call_url = eregi_replace('--A--middle_initial--B--',urlencode(trim($middle_initial)),$VDCL_start_call_url);
				$VDCL_start_call_url = eregi_replace('--A--last_name--B--',urlencode(trim($last_name)),$VDCL_start_call_url);
				$VDCL_start_call_url = eregi_replace('--A--address1--B--',urlencode(trim($address1)),$VDCL_start_call_url);
				$VDCL_start_call_url = eregi_replace('--A--address2--B--',urlencode(trim($address2)),$VDCL_start_call_url);
				$VDCL_start_call_url = eregi_replace('--A--address3--B--',urlencode(trim($address3)),$VDCL_start_call_url);
				$VDCL_start_call_url = eregi_replace('--A--city--B--',urlencode(trim($city)),$VDCL_start_call_url);
				$VDCL_start_call_url = eregi_replace('--A--state--B--',urlencode(trim($state)),$VDCL_start_call_url);
				$VDCL_start_call_url = eregi_replace('--A--province--B--',urlencode(trim($province)),$VDCL_start_call_url);
				$VDCL_start_call_url = eregi_replace('--A--postal_code--B--',urlencode(trim($postal_code)),$VDCL_start_call_url);
				$VDCL_start_call_url = eregi_replace('--A--country_code--B--',urlencode(trim($country_code)),$VDCL_start_call_url);
				$VDCL_start_call_url = eregi_replace('--A--gender--B--',urlencode(trim($gender)),$VDCL_start_call_url);
				$VDCL_start_call_url = eregi_replace('--A--date_of_birth--B--',urlencode(trim($date_of_birth)),$VDCL_start_call_url);
				$VDCL_start_call_url = eregi_replace('--A--alt_phone--B--',urlencode(trim($alt_phone)),$VDCL_start_call_url);
				$VDCL_start_call_url = eregi_replace('--A--email--B--',urlencode(trim($email)),$VDCL_start_call_url);
				$VDCL_start_call_url = eregi_replace('--A--security_phrase--B--',urlencode(trim($security_phrase)),$VDCL_start_call_url);
				$VDCL_start_call_url = eregi_replace('--A--comments--B--',urlencode(trim($comments)),$VDCL_start_call_url);
				$VDCL_start_call_url = eregi_replace('--A--user--B--',urlencode(trim($user)),$VDCL_start_call_url);
				$VDCL_start_call_url = eregi_replace('--A--pass--B--',urlencode(trim($pass)),$VDCL_start_call_url);
				$VDCL_start_call_url = eregi_replace('--A--campaign--B--',urlencode(trim($campaign)),$VDCL_start_call_url);
				$VDCL_start_call_url = eregi_replace('--A--phone_login--B--',urlencode(trim($phone_login)),$VDCL_start_call_url);
				$VDCL_start_call_url = eregi_replace('--A--original_phone_login--B--',urlencode(trim($original_phone_login)),$VDCL_start_call_url);
				$VDCL_start_call_url = eregi_replace('--A--phone_pass--B--',urlencode(trim($phone_pass)),$VDCL_start_call_url);
				$VDCL_start_call_url = eregi_replace('--A--fronter--B--',urlencode(trim($fronter)),$VDCL_start_call_url);
				$VDCL_start_call_url = eregi_replace('--A--closer--B--',urlencode(trim($closer)),$VDCL_start_call_url);
				$VDCL_start_call_url = eregi_replace('--A--group--B--',urlencode(trim($group)),$VDCL_start_call_url);
				$VDCL_start_call_url = eregi_replace('--A--channel_group--B--',urlencode(trim($channel_group)),$VDCL_start_call_url);
				$VDCL_start_call_url = eregi_replace('--A--SQLdate--B--',urlencode(trim($SQLdate)),$VDCL_start_call_url);
				$VDCL_start_call_url = eregi_replace('--A--epoch--B--',urlencode(trim($epoch)),$VDCL_start_call_url);
				$VDCL_start_call_url = eregi_replace('--A--uniqueid--B--',urlencode(trim($uniqueid)),$VDCL_start_call_url);
				$VDCL_start_call_url = eregi_replace('--A--customer_zap_channel--B--',urlencode(trim($customer_zap_channel)),$VDCL_start_call_url);
				$VDCL_start_call_url = eregi_replace('--A--customer_server_ip--B--',urlencode(trim($customer_server_ip)),$VDCL_start_call_url);
				$VDCL_start_call_url = eregi_replace('--A--server_ip--B--',urlencode(trim($server_ip)),$VDCL_start_call_url);
				$VDCL_start_call_url = eregi_replace('--A--SIPexten--B--',urlencode(trim($SIPexten)),$VDCL_start_call_url);
				$VDCL_start_call_url = eregi_replace('--A--session_id--B--',urlencode(trim($session_id)),$VDCL_start_call_url);
				$VDCL_start_call_url = eregi_replace('--A--phone--B--',urlencode(trim($phone)),$VDCL_start_call_url);
				$VDCL_start_call_url = eregi_replace('--A--parked_by--B--',urlencode(trim($parked_by)),$VDCL_start_call_url);
				$VDCL_start_call_url = eregi_replace('--A--dispo--B--',urlencode(trim($dispo)),$VDCL_start_call_url);
				$VDCL_start_call_url = eregi_replace('--A--dialed_number--B--',urlencode(trim($dialed_number)),$VDCL_start_call_url);
				$VDCL_start_call_url = eregi_replace('--A--dialed_label--B--',urlencode(trim($dialed_label)),$VDCL_start_call_url);
				$VDCL_start_call_url = eregi_replace('--A--source_id--B--',urlencode(trim($source_id)),$VDCL_start_call_url);
				$VDCL_start_call_url = eregi_replace('--A--rank--B--',urlencode(trim($rank)),$VDCL_start_call_url);
				$VDCL_start_call_url = eregi_replace('--A--owner--B--',urlencode(trim($owner)),$VDCL_start_call_url);
				$VDCL_start_call_url = eregi_replace('--A--camp_script--B--',urlencode(trim($camp_script)),$VDCL_start_call_url);
				$VDCL_start_call_url = eregi_replace('--A--in_script--B--',urlencode(trim($in_script)),$VDCL_start_call_url);
				$VDCL_start_call_url = eregi_replace('--A--fullname--B--',urlencode(trim($fullname)),$VDCL_start_call_url);
				$VDCL_start_call_url = eregi_replace('--A--user_custom_one--B--',urlencode(trim($user_custom_one)),$VDCL_start_call_url);
				$VDCL_start_call_url = eregi_replace('--A--user_custom_two--B--',urlencode(trim($user_custom_two)),$VDCL_start_call_url);
				$VDCL_start_call_url = eregi_replace('--A--user_custom_three--B--',urlencode(trim($user_custom_three)),$VDCL_start_call_url);
				$VDCL_start_call_url = eregi_replace('--A--user_custom_four--B--',urlencode(trim($user_custom_four)),$VDCL_start_call_url);
				$VDCL_start_call_url = eregi_replace('--A--user_custom_five--B--',urlencode(trim($user_custom_five)),$VDCL_start_call_url);
				$VDCL_start_call_url = eregi_replace('--A--talk_time--B--',"0",$VDCL_start_call_url);
				$VDCL_start_call_url = eregi_replace('--A--talk_time_min--B--',"0",$VDCL_start_call_url);
				if ($DB > 0) {echo "$VDCL_start_call_url<BR>\n";}
				$SCUfile = file("$VDCL_start_call_url");
				if ($DB > 0) {echo "$SCUfile[0]<BR>\n";}

				##### BEGIN special filtering and response for Vtiger account balance function #####
				$stmt = "SELECT enable_vtiger_integration FROM system_settings;";
				$rslt=mysql_query($stmt, $link);
					if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00294',$user,$server_ip,$session_name,$one_mysql_log);}
				$ss_conf_ct = mysql_num_rows($rslt);
				if ($ss_conf_ct > 0)
					{
					$row=mysql_fetch_row($rslt);
					$enable_vtiger_integration =	$row[0];
					}
				if ( ($enable_vtiger_integration > 0) and (ereg('callxfer',$VDCL_start_call_url)) and (ereg('contactwsid',$VDCL_start_call_url)) )
					{
					$SCUoutput='';
					foreach ($SCUfile as $SCUline) 
						{$SCUoutput .= "$SCUline";}
					# {"result":true,"durationLimit":3071}
					if (strlen($SCUoutput) > 4)
						{
						$SCUresponse = explode('durationLimit',$SCUoutput);
						$durationLimit = preg_replace('/\D/','',$SCUresponse[1]);
						$durationLimitSEC = ( ( ($durationLimit + 0) - 3) * 60);  # minutes - 3 for 3-minute-warning
						if ($durationLimitSEC < 5) {$durationLimitSEC = 5;}

						$stmt="UPDATE vicidial_live_agents set external_timer_action='D1_DIAL',external_timer_action_message='3 minute warning for customer',external_timer_action_seconds='$durationLimitSEC' where user='$user';";
						if ($DB) {echo "$stmt\n";}
						$rslt=mysql_query($stmt, $link);
							if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00295',$user,$server_ip,$session_name,$one_mysql_log);}
						$vla_update_timer = mysql_affected_rows($link);

						$fp = fopen ("./call_url_log.txt", "a");
						fwrite ($fp, "$VDCL_start_call_url\n$SCUoutput\n$durationLimit|$durationLimitSEC|$vla_update_timer\n");
						fclose($fp);
						}
					}
				##### END special filtering and response for Vtiger account balance function #####
				}
			}
			else
			{
			echo "0\n";
		#	echo "No calls in QUEUE for $user på $server_ip\n";
			exit;
			}
		}
	}


################################################################################
### userLOGout - Logs the user out of VICIDiaL client, deleting db records and 
###              inserting into vicidial_user_log
################################################################################
if ($ACTION == 'userLOGout')
	{
	$MT[0]='';
	$row='';   $rowx='';
	if ( (strlen($campaign)<1) || (strlen($conf_exten)<1) )
		{
		echo "NO\n";
		echo "campaign $campaign or conf_exten $conf_exten är ej giltig\n";
		exit;
		}
	else
		{
		$user_group='';
		$stmt="SELECT user_group FROM vicidial_users where user='$user' LIMIT 1;";
		$rslt=mysql_query($stmt, $link);
			if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00127',$user,$server_ip,$session_name,$one_mysql_log);}
		if ($DB) {echo "$stmt\n";}
		$ug_record_ct = mysql_num_rows($rslt);
		if ($ug_record_ct > 0)
			{
			$row=mysql_fetch_row($rslt);
			$user_group =		trim("$row[0]");
			}
		##### Insert a LOGOUT record into the user log
		$stmt="INSERT INTO vicidial_user_log (user,event,campaign_id,event_date,event_epoch,user_group) values('$user','LOGOUT','$campaign','$NOW_TIME','$StarTtime','$user_group');";
		if ($DB) {echo "$stmt\n";}
		$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00128',$user,$server_ip,$session_name,$one_mysql_log);}
		$vul_insert = mysql_affected_rows($link);

		if ($no_delete_sessions < 1)
			{
			##### Remove the reservation på the vicidial_conferences meetme room
			$stmt="UPDATE vicidial_conferences set extension='' where server_ip='$server_ip' and conf_exten='$conf_exten';";
			if ($DB) {echo "$stmt\n";}
			$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00129',$user,$server_ip,$session_name,$one_mysql_log);}
			$vc_remove = mysql_affected_rows($link);
			}

		##### Delete the web_client_sessions
		$stmt="DELETE from web_client_sessions where server_ip='$server_ip' and session_name ='$session_name';";
		if ($DB) {echo "$stmt\n";}
		$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00132',$user,$server_ip,$session_name,$one_mysql_log);}
		$wcs_delete = mysql_affected_rows($link);

		##### Hangup the client phone
		$stmt="SELECT channel FROM live_sip_channels where server_ip = '$server_ip' and channel LIKE \"$protocol/$extension%\" order by channel desc;";
			if ($format=='debug') {echo "\n<!-- $stmt -->";}
		$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00133',$user,$server_ip,$session_name,$one_mysql_log);}
		if ($rslt) 
			{
			$row=mysql_fetch_row($rslt);
			$agent_channel = "$row[0]";
			if ($format=='debug') {echo "\n<!-- $row[0] -->";}
			$stmt="INSERT INTO vicidial_manager values('','','$NOW_TIME','NEW','N','$server_ip','','Hangup','ULGH3459$StarTtime','Channel: $agent_channel','','','','','','','','','');";
				if ($format=='debug') {echo "\n<!-- $stmt -->";}
			$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00134',$user,$server_ip,$session_name,$one_mysql_log);}
			}

		if ($LogouTKicKAlL > 0)
			{
			$local_DEF = 'Local/5555';
			$local_AMP = '@';
			$kick_local_channel = "$local_DEF$conf_exten$local_AMP$ext_context";
			$queryCID = "ULGH3458$StarTtime";

			$stmt="INSERT INTO vicidial_manager values('','','$NOW_TIME','NEW','N','$server_ip','','Originate','$queryCID','Channel: $kick_local_channel','Context: $ext_context','Exten: 8300','Priority: 1','Callerid: $queryCID','','','','$channel','$exten');";
				if ($format=='debug') {echo "\n<!-- $stmt -->";}
			$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00135',$user,$server_ip,$session_name,$one_mysql_log);}
			}

		sleep(1);

		##### Delete the vicidial_live_agents record for this session
		$stmt="DELETE from vicidial_live_agents where server_ip='$server_ip' and user ='$user';";
		if ($DB) {echo "$stmt\n";}
		$rslt=mysql_query($stmt, $link);
			if ($mel > 0) {$errno = mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00130',$user,$server_ip,$session_name,$one_mysql_log);}
		$retry_count=0;
		while ( ($errno > 0) and ($retry_count < 9) )
			{
			$rslt=mysql_query($stmt, $link);
			$one_mysql_log=1;
			$errno = mysql_error_logging($NOW_TIME,$link,$mel,$stmt,"9130$retry_count",$user,$server_ip,$session_name,$one_mysql_log);
			$one_mysql_log=0;
			$retry_count++;
			}
		$vla_delete = mysql_affected_rows($link);

		##### Delete the vicidial_live_inbound_agents records for this session
		$stmt="DELETE from vicidial_live_inbound_agents where user ='$user';";
		if ($DB) {echo "$stmt\n";}
		$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00131',$user,$server_ip,$session_name,$one_mysql_log);}
		$vlia_delete = mysql_affected_rows($link);

		$pause_sec=0;
		$stmt = "select pause_epoch,pause_sec,wait_epoch,talk_epoch,dispo_epoch,agent_log_id from vicidial_agent_log where agent_log_id >= '$agent_log_id' and user='$user' order by agent_log_id desc limit 1;";
		if ($DB) {echo "$stmt\n";}
		$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00136',$user,$server_ip,$session_name,$one_mysql_log);}
		$VDpr_ct = mysql_num_rows($rslt);
		if ( ($VDpr_ct > 0) and (strlen($row[3]<5)) and (strlen($row[4]<5)) )
			{
			$row=mysql_fetch_row($rslt);
			$agent_log_id = $row[5];
			$pause_sec = (($StarTtime - $row[0]) + $row[1]);

			$stmt="UPDATE vicidial_agent_log set pause_sec='$pause_sec',wait_epoch='$StarTtime' where agent_log_id='$agent_log_id';";
				if ($format=='debug') {echo "\n<!-- $stmt -->";}
			$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00137',$user,$server_ip,$session_name,$one_mysql_log);}
			}

		if ($vla_delete > 0) 
			{
			#############################################
			##### START QUEUEMETRICS LOGGING LOOKUP #####
			$stmt = "SELECT enable_queuemetrics_logging,queuemetrics_server_ip,queuemetrics_dbname,queuemetrics_login,queuemetrics_pass,queuemetrics_log_id,allow_sipsak_messages FROM system_settings;";
			$rslt=mysql_query($stmt, $link);
			if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00138',$user,$server_ip,$session_name,$one_mysql_log);}
			if ($DB) {echo "$stmt\n";}
			$qm_conf_ct = mysql_num_rows($rslt);
			if ($qm_conf_ct > 0)
				{
				$row=mysql_fetch_row($rslt);
				$enable_queuemetrics_logging =	$row[0];
				$queuemetrics_server_ip	=		$row[1];
				$queuemetrics_dbname =			$row[2];
				$queuemetrics_login	=			$row[3];
				$queuemetrics_pass =			$row[4];
				$queuemetrics_log_id =			$row[5];
				$allow_sipsak_messages =		$row[6];
				}
			##### END QUEUEMETRICS LOGGING LOOKUP #####
			###########################################
			if ( ($enable_sipsak_messages > 0) and ($allow_sipsak_messages > 0) and (eregi("SIP",$protocol)) )
				{
				$SIPSAK_message = 'LOGGED OUT';
				passthru("/usr/local/bin/sipsak -M -O desktop -B \"$SIPSAK_message\" -r 5060 -s sip:$extension@$phone_ip > /dev/null");
				}

			if ($enable_queuemetrics_logging > 0)
				{
				$linkB=mysql_connect("$queuemetrics_server_ip", "$queuemetrics_login", "$queuemetrics_pass");
				mysql_select_db("$queuemetrics_dbname", $linkB);

			#	$stmt = "INSERT INTO queue_log SET partition='P01',time_id='$StarTtime',call_id='NONE',queue='$campaign',agent='Agent/$user',verb='PAUSE',serverid='1';";
			#	if ($DB) {echo "$stmt\n";}
			#	
			#	$rslt=mysql_query($stmt, $linkB);
			#	$affected_rows = mysql_affected_rows($linkB);

				$stmt = "SELECT time_id FROM queue_log where agent='Agent/$user' and verb='AGENTLOGIN' order by time_id desc limit 1;";
				$rslt=mysql_query($stmt, $linkB);
			if ($mel > 0) {mysql_error_logging($NOW_TIME,$linkB,$mel,$stmt,'00139',$user,$server_ip,$session_name,$one_mysql_log);}
				if ($DB) {echo "$stmt\n";}
				echo "$stmt\n";
				$li_conf_ct = mysql_num_rows($rslt);
				$i=0;
				while ($i < $li_conf_ct)
					{
					$row=mysql_fetch_row($rslt);
					$logintime =	$row[0];
					$i++;
					}

				$time_logged_in = ($StarTtime - $logintime);
				if ($time_logged_in > 1000000) {$time_logged_in=1;}

				$stmt = "INSERT INTO queue_log SET partition='P01',time_id='$StarTtime',call_id='NONE',queue='NONE',agent='Agent/$user',verb='AGENTLOGOFF',data1='$user$agents',data2='$time_logged_in',serverid='$queuemetrics_log_id';";
				if ($DB) {echo "$stmt\n";}
				$rslt=mysql_query($stmt, $linkB);
			if ($mel > 0) {mysql_error_logging($NOW_TIME,$linkB,$mel,$stmt,'00140',$user,$server_ip,$session_name,$one_mysql_log);}
				$affected_rows = mysql_affected_rows($linkB);

				mysql_close($linkB);
				}
			}

		echo "$vul_insert|$vc_remove|$vla_delete|$wcs_delete|$agent_channel|$vlia_delete\n";
		}
	}


################################################################################
### updateDISPO - update the vicidial_list table to reflect the agent choice of
###               call disposition for that lead
################################################################################
if ($ACTION == 'updateDISPO')
	{
	$MT[0]='';
	$row='';   $rowx='';
	$MAN_vl_insert=0;
	if ( (strlen($dispo_choice)<1) || (strlen($lead_id)<1) )
		{
		echo "Dispo Choice $dispo or lead_id $lead_id är ej giltig\n";
		exit;
		}
	else
		{
		$stmt = "SELECT dispo_call_url from vicidial_campaigns where campaign_id='$campaign';";
		if ($DB) {echo "$stmt\n";}
		$rslt=mysql_query($stmt, $link);
			if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00284',$user,$server_ip,$session_name,$one_mysql_log);}
		$VC_dcu_ct = mysql_num_rows($rslt);
		if ($VC_dcu_ct > 0)
			{
			$row=mysql_fetch_row($rslt);
			$dispo_call_url	=$row[0];
			}

		### reset the API fields in vicidial_live_agents record
		$stmt = "UPDATE vicidial_live_agents set lead_id=0,external_hangup=0,external_status='',external_update_fields='0',external_update_fields_data='',external_timer_action_seconds='-1',last_state_change='$NOW_TIME' where user='$user' and server_ip='$server_ip';";
		if ($DB) {echo "$stmt\n";}
		$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {$errno = mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00141',$user,$server_ip,$session_name,$one_mysql_log);}
				$retry_count=0;
				while ( ($errno > 0) and ($retry_count < 9) )
					{
					$rslt=mysql_query($stmt, $link);
					$one_mysql_log=1;
					$errno = mysql_error_logging($NOW_TIME,$link,$mel,$stmt,"9141$retry_count",$user,$server_ip,$session_name,$one_mysql_log);
					$one_mysql_log=0;
					$retry_count++;
					}

		if ($auto_dial_level < 1)
			{
			$stmt = "UPDATE vicidial_live_agents set status='PAUSED',callerid='' where user='$user';";
			if ($DB) {echo "$stmt\n";}
			$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00285',$user,$server_ip,$session_name,$one_mysql_log);}
			}

		$stmt="UPDATE vicidial_list set status='$dispo_choice', user='$user' where lead_id='$lead_id';";
			if ($format=='debug') {echo "\n<!-- $stmt -->";}
		$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00142',$user,$server_ip,$session_name,$one_mysql_log);}

		$stmt = "SELECT count(*) from vicidial_inbound_groups where group_id='$stage';";
			if ($format=='debug') {echo "\n<!-- $stmt -->";}
		$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00143',$user,$server_ip,$session_name,$one_mysql_log);}
			$row=mysql_fetch_row($rslt);
		if ($row[0] > 0)
			{
			$stmt = "UPDATE vicidial_closer_log set status='$dispo_choice' where lead_id='$lead_id' and user='$user' order by closecallid desc limit 1;";
			if ($DB) {echo "$stmt\n";}
			$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00144',$user,$server_ip,$session_name,$one_mysql_log);}

			$stmt = "UPDATE vicidial_live_inbound_agents set last_call_finish=NOW() where group_id='$stage' and user='$user' limit 1;";
			if ($DB) {echo "$stmt\n";}
			$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00310',$user,$server_ip,$session_name,$one_mysql_log);}

			$stmt = "SELECT dispo_call_url from vicidial_inbound_groups where group_id='$stage';";
				if ($format=='debug') {echo "\n<!-- $stmt -->";}
			$rslt=mysql_query($stmt, $link);
					if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00286',$user,$server_ip,$session_name,$one_mysql_log);}
			$row=mysql_fetch_row($rslt);
			$dispo_call_url = $row[0];
			}
		else
			{
			$four_hours_ago = date("Y-m-d H:i:s", mktime(date("H")-4,date("i"),date("s"),date("m"),date("d"),date("Y")));

			if ( ($auto_dial_level < 1) or (preg_match('/^M/',$MDnextCID)) )
				{
				$stmt = "SELECT count(*) from vicidial_log where lead_id='$lead_id' and user='$user' and call_date > \"$four_hours_ago\";";
					if ($format=='debug') {echo "\n<!-- $stmt -->";}
				$rslt=mysql_query($stmt, $link);
						if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00213',$user,$server_ip,$session_name,$one_mysql_log);}
					$row=mysql_fetch_row($rslt);
				if ($row[0] > 0)
					{
					$stmt="UPDATE vicidial_log set status='$dispo_choice' where lead_id='$lead_id' and user='$user' and call_date > \"$four_hours_ago\" order by uniqueid desc limit 1;";
						if ($format=='debug') {echo "\n<!-- $stmt -->";}
					$rslt=mysql_query($stmt, $link);
						if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00145',$user,$server_ip,$session_name,$one_mysql_log);}
					}
				else
					{
					$VLlist_id = '';   $VLphone_number = '';   $VLphone_code = '';   $user_group='';
					$stmt = "SELECT user_group FROM vicidial_users where user='$user';";
					$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00217',$user,$server_ip,$session_name,$one_mysql_log);}
					if ($DB) {echo "$stmt\n";}
					$VUinfo_ct = mysql_num_rows($rslt);
					if ($VUinfo_ct > 0)
						{
						$row=mysql_fetch_row($rslt);
						$user_group =		"$row[0]";
						}

					$stmt = "SELECT list_id,phone_number,phone_code,alt_phone,address3 FROM vicidial_list where lead_id='$lead_id';";
					$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00216',$user,$server_ip,$session_name,$one_mysql_log);}
					if ($DB) {echo "$stmt\n";}
					$VLinfo_ct = mysql_num_rows($rslt);
					if ($VLinfo_ct > 0)
						{
						$row=mysql_fetch_row($rslt);
						$VLlist_id =		"$row[0]";
						if (strlen($phone_number)<6)
							{
							$VLphone_number =	"$row[1]";
							$VLalt =			'MAIN';
							$VLalt_phone =		"$row[3]";
							$VLaddress3 =		"$row[4]";
							}
						else
							{
							$VLphone_number =	"$phone_number";
							if ($phone_number == "$row[1]")
								{$VLalt =			'MAIN';}
							else
								{
								if ($phone_number != $VLalt_phone)
									{
									if ($phone_number != $VLaddress3)
										{
										$VLalt = 'X1';
										$stmt = "SELECT alt_phone_count from vicidial_list_alt_phones where lead_id='$lead_id' and phone_number = '$dialed_number' order by alt_phone_count limit 1;";
										if ($DB) {echo "$stmt\n";}
										$rslt=mysql_query($stmt, $link);
											if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00250',$user,$server_ip,$session_name,$one_mysql_log);}
										$VDAP_cid_ct = mysql_num_rows($rslt);
										if ($VDAP_cid_ct > 0)
											{
											$row=mysql_fetch_row($rslt);
											$Xalt_phone_count	=$row[0];

											$stmt = "SELECT count(*) from vicidial_list_alt_phones where lead_id='$lead_id';";
											if ($DB) {echo "$stmt\n";}
											$rslt=mysql_query($stmt, $link);
												if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00251',$user,$server_ip,$session_name,$one_mysql_log);}
											$VDAPct_cid_ct = mysql_num_rows($rslt);
											if ($VDAPct_cid_ct > 0)
												{
												$row=mysql_fetch_row($rslt);
												$COUNTalt_phone_count	=$row[0];

												if ($COUNTalt_phone_count <= $Xalt_phone_count)
													{$VLalt = 'XLAST';}
												else
													{$VLalt = "X$Xalt_phone_count";}
												}

											}
										}
									else
										{$VLalt =			'ADDR3';}
									}
								else
									{$VLalt =			'ALT';}
								}
							}
						if (strlen($phone_code)<1)
							{$VLphone_code =	"$row[2]";}
						else
							{$VLphone_code =	"$phone_code";}
						}

					$PADlead_id = sprintf("%09s", $lead_id);
						while (strlen($PADlead_id) > 9) {$PADlead_id = substr("$PADlead_id", 0, -1);}
					$FAKEcall_id = "$StarTtime.$PADlead_id";
					$stmt = "INSERT INTO vicidial_log set uniqueid='$FAKEcall_id',lead_id='$lead_id',list_id='$VLlist_id',campaign_id='$campaign',call_date='$NOW_TIME',start_epoch='$StarTtime',end_epoch='$StarTtime',length_in_sec='0',status='$dispo_choice',phone_code='$VLphone_code',phone_number='$VLphone_number',user='$user',comments='MANUAL',processed='N',user_group='$user_group',term_reason='AGENT',alt_dial='$VLalt';";
					if ($DB) {echo "$stmt\n";}
					$rslt=mysql_query($stmt, $link);
						if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00215',$user,$server_ip,$session_name,$one_mysql_log);}

					$MAN_vl_insert++;
					}

				$stmt="DELETE FROM vicidial_auto_calls where callerid='$MDnextCID';";
					if ($format=='debug') {echo "\n<!-- $stmt -->";}
				$rslt=mysql_query($stmt, $link);
					if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00219',$user,$server_ip,$session_name,$one_mysql_log);}
				}
			else
				{
				$stmt="UPDATE vicidial_log set status='$dispo_choice' where lead_id='$lead_id' and user='$user' and call_date > \"$four_hours_ago\" order by uniqueid desc limit 1;";
					if ($format=='debug') {echo "\n<!-- $stmt -->";}
				$rslt=mysql_query($stmt, $link);
					if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00145',$user,$server_ip,$session_name,$one_mysql_log);}
				}
			}

		### find all DNC-type statuses in the system
		if ( ($use_internal_dnc=='Y') or ($use_campaign_dnc=='Y') )
			{
			$DNC_string_check = '|';
			$stmt = "SELECT status FROM vicidial_statuses where dnc='Y';";
			$rslt=mysql_query($stmt, $link);
		if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00195',$user,$server_ip,$session_name,$one_mysql_log);}
			if ($DB) {echo "$stmt\n";}
			$dncvs_ct = mysql_num_rows($rslt);
			$i=0;
			while ($i < $dncvs_ct)
				{
				$row=mysql_fetch_row($rslt);
				$DNC_string_check .= "$row[0]|";
				$i++;
				}

			$stmt = "SELECT status FROM vicidial_campaign_statuses where dnc='Y';";
			$rslt=mysql_query($stmt, $link);
		if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00196',$user,$server_ip,$session_name,$one_mysql_log);}
			if ($DB) {echo "$stmt\n";}
			$dncvcs_ct = mysql_num_rows($rslt);
			$i=0;
			while ($i < $dncvcs_ct)
				{
				$row=mysql_fetch_row($rslt);
				$DNC_string_check .= "$row[0]|";
				$i++;
				}

		#	echo "$DNC_string_check";
			}

		$insert_into_dnc=0;
		if ( ($use_internal_dnc=='Y') and (eregi("\|$dispo_choice\|", $DNC_string_check) ) )
			{
			$stmt = "select phone_number from vicidial_list where lead_id='$lead_id';";
			if ($DB) {echo "$stmt\n";}
			$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00146',$user,$server_ip,$session_name,$one_mysql_log);}
				$row=mysql_fetch_row($rslt);
			$stmt="INSERT INTO vicidial_dnc (phone_number) values('$row[0]');";
			$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00147',$user,$server_ip,$session_name,$one_mysql_log);}
			if ($DB) {echo "$stmt\n";}
			$insert_into_dnc++;
			}
		if ( ($use_campaign_dnc=='Y') and (eregi("\|$dispo_choice\|", $DNC_string_check) ) )
			{
			$stmt = "select phone_number from vicidial_list where lead_id='$lead_id';";
			if ($DB) {echo "$stmt\n";}
			$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00148',$user,$server_ip,$session_name,$one_mysql_log);}
				$row=mysql_fetch_row($rslt);
			$stmt="INSERT INTO vicidial_campaign_dnc (phone_number,campaign_id) values('$row[0]','$campaign');";
			$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00149',$user,$server_ip,$session_name,$one_mysql_log);}
			if ($DB) {echo "$stmt\n";}
			$insert_into_dnc++;
			}
		}

	$dispo_sec=0;
	$dispo_epochSQL='';
	$lead_id_commentsSQL='';
	$StarTtime = date("U");
	$stmt = "select dispo_epoch,dispo_sec,talk_epoch,wait_epoch,lead_id,comments,agent_log_id from vicidial_agent_log where agent_log_id <='$agent_log_id' and lead_id='$lead_id' order by agent_log_id desc limit 1;";
	if ($DB) {echo "$stmt\n";}
	$rslt=mysql_query($stmt, $link);
			if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00150',$user,$server_ip,$session_name,$one_mysql_log);}
	$VDpr_ct = mysql_num_rows($rslt);
	if ($VDpr_ct > 0)
		{
		$row=mysql_fetch_row($rslt);
		$agent_log_id = $row[6];
		if ( (eregi("NULL",$row[2])) or ($row[2] < 1000) )
			{
			$row[2]=$StarTtime;
			$wait_sec=($row[2] - $row[3]);
			$dispo_epochSQL = ",talk_epoch='$row[2]',wait_sec='$wait_sec'";
			}
		if ( (eregi("NULL",$row[0])) or ($row[0] < 1000) )
			{
			$dispo_epochSQL .= ",dispo_epoch='$StarTtime'";
			$row[0]=$row[2];
			}
		$dispo_sec = (($StarTtime - $row[0]) + $row[1]);
		if ( (preg_match('/^M/',$MDnextCID)) and (preg_match('/INBOUND_MAN/',$dial_method)) )
			{
			if ( (eregi("NULL",$row[5])) or (strlen($row[5]) < 1) )
				{
				$lead_id_commentsSQL .= ",comments='MANUAL'";
				}
			if ( (eregi("NULL",$row[4])) or ($row[4] < 1) or (strlen($row[4]) < 1) )
				{
				$lead_id_commentsSQL .= ",lead_id='$lead_id'";
				}
			}
		}
	$stmt="UPDATE vicidial_agent_log set dispo_sec='$dispo_sec',status='$dispo_choice' $dispo_epochSQL $lead_id_commentsSQL where agent_log_id='$agent_log_id';";
		if ($format=='debug') {echo "\n<!-- $stmt -->";}
	$rslt=mysql_query($stmt, $link);
			if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00151',$user,$server_ip,$session_name,$one_mysql_log);}

	$stmt="UPDATE vicidial_campaigns set campaign_calldate='$NOW_TIME' where campaign_id='$campaign';";
		if ($format=='debug') {echo "\n<!-- $stmt -->";}
	$rslt=mysql_query($stmt, $link);
			if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00272',$user,$server_ip,$session_name,$one_mysql_log);}

	$user_group='';
	$stmt="SELECT user_group FROM vicidial_users where user='$user' LIMIT 1;";
	$rslt=mysql_query($stmt, $link);
		if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00152',$user,$server_ip,$session_name,$one_mysql_log);}
	if ($DB) {echo "$stmt\n";}
	$ug_record_ct = mysql_num_rows($rslt);
	if ($ug_record_ct > 0)
		{
		$row=mysql_fetch_row($rslt);
		$user_group =		trim("$row[0]");
		}
	$CALL_agent_log_id = $agent_log_id;

	if ($auto_dial_level < 1)
		{
		$MAN_insert_leadIDsql='';
		if ($MAN_vl_insert > 0)
			{$MAN_insert_leadIDsql = ",lead_id='$lead_id'";}
		$stmt="INSERT INTO vicidial_agent_log SET user='$user',server_ip='$server_ip',event_time='$NOW_TIME',campaign_id='$campaign',pause_epoch='$StarTtime',pause_sec='0',wait_epoch='$StarTtime',user_group='$user_group'$MAN_insert_leadIDsql;";
		if ($DB) {echo "$stmt\n";}
		$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00153',$user,$server_ip,$session_name,$one_mysql_log);}
		$affected_rows = mysql_affected_rows($link);
		$agent_log_id = mysql_insert_id($link);

		$stmt="UPDATE vicidial_live_agents SET agent_log_id='$agent_log_id' where user='$user';";
		if ($DB) {echo "$stmt\n";}
		$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00220',$VD_login,$server_ip,$session_name,$one_mysql_log);}
		$VLAaffected_rows_update = mysql_affected_rows($link);
		}

	### SAMTALBACK ENTRY
	if ( ($dispo_choice == 'CBHOLD') and (strlen($CallBackDatETimE)>10) )
		{
		$comments = eregi_replace('"','',$comments);
		$comments = eregi_replace("'",'',$comments);
		$comments = eregi_replace(';','',$comments);
		$comments = eregi_replace("\\\\",' ',$comments);
		$stmt="INSERT INTO vicidial_callbacks (lead_id,list_id,campaign_id,status,entry_time,callback_time,user,recipient,comments,user_group) values('$lead_id','$list_id','$campaign','ACTIVE','$NOW_TIME','$CallBackDatETimE','$user','$recipient','$comments','$user_group');";
		if ($DB) {echo "$stmt\n";}
		$rslt=mysql_query($stmt, $link);
			if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00154',$user,$server_ip,$session_name,$one_mysql_log);}
		}

	$stmt="SELECT auto_alt_dial_statuses,use_internal_dnc,use_campaign_dnc from vicidial_campaigns where campaign_id='$campaign';";
	$rslt=mysql_query($stmt, $link);
			if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00155',$user,$server_ip,$session_name,$one_mysql_log);}
	$row=mysql_fetch_row($rslt);
	$VC_auto_alt_dial_statuses =	$row[0];
	$use_internal_dnc =				$row[1];
	$use_campaign_dnc =				$row[2];

	if ( ($auto_dial_level > 0) and (ereg(" $dispo_choice ",$VC_auto_alt_dial_statuses)) )
		{
		$stmt = "select count(*) from vicidial_hopper where lead_id='$lead_id' and status='HOLD';";
		if ($DB) {echo "$stmt\n";}
		$rslt=mysql_query($stmt, $link);
			if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00156',$user,$server_ip,$session_name,$one_mysql_log);}
		$row=mysql_fetch_row($rslt);

		if ($row[0] > 0)
			{
			##### Check for alt phone number in DNC list if applicable
			$UD_DNC_campaign=0;
			$UD_DNC_internal=0;
			$vh_phone='';
			$stmt="SELECT phone_number FROM vicidial_list where lead_id='$lead_id';";
			$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00267',$user,$server_ip,$session_name,$one_mysql_log);}
			if ($DB) {echo "$stmt\n";}
			$ud_record_ct = mysql_num_rows($rslt);
			if ($ud_record_ct > 0)
				{
				$row=mysql_fetch_row($rslt);
				$vh_phone =		$row[0];
				}

			if ( (ereg("Y",$use_internal_dnc)) or (ereg("AREACODE",$use_internal_dnc)) )
				{
				if (ereg("AREACODE",$use_internal_dnc))
					{
					$vhp_phone_areacode = substr($vh_phone, 0, 3);
					$vhp_phone_areacode .= "XXXXXXX";
					$stmtA="SELECT count(*) from vicidial_dnc where phone_number IN('$vh_phone','$vhp_phone_areacode');";
					}
				else
					{$stmtA="SELECT count(*) FROM vicidial_dnc where phone_number='$vh_phone';";}
				$rslt=mysql_query($stmtA, $link);
					if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmtA,'00268',$user,$server_ip,$session_name,$one_mysql_log);}
				if ($DB) {echo "$stmt\n";}
				$ud_record_ct = mysql_num_rows($rslt);
				if ($ud_record_ct > 0)
					{
					$row=mysql_fetch_row($rslt);
					$UD_DNC_internal =		$row[0];
					}
				}

			if ( (ereg("Y",$use_campaign_dnc)) or (ereg("AREACODE",$use_campaign_dnc)) )
				{
				if (ereg("AREACODE",$use_campaign_dnc))
					{
					$vhp_phone_areacode = substr($vh_phone, 0, 3);
					$vhp_phone_areacode .= "XXXXXXX";
					$stmtA="SELECT count(*) from vicidial_campaign_dnc where phone_number IN('$vh_phone','$vhp_phone_areacode') and campaign_id='$campaign';";
					}
				else
					{$stmtA="SELECT count(*) FROM vicidial_campaign_dnc where phone_number='$vh_phone' and campaign_id='$campaign';";}
				$rslt=mysql_query($stmtA, $link);
					if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmtA,'00269',$user,$server_ip,$session_name,$one_mysql_log);}
				if ($DB) {echo "$stmt\n";}
				$ud_record_ct = mysql_num_rows($rslt);
				if ($ud_record_ct > 0)
					{
					$row=mysql_fetch_row($rslt);
					$UD_DNC_campaign =		$row[0];
					}
				}

			if ( ($UD_DNC_campaign > 0) or ($UD_DNC_internal > 0) ) 
				{
				if ( ( (ereg(" DNCC ",$VC_auto_alt_dial_statuses)) and ($UD_DNC_campaign > 0) ) or ( (ereg(" DNCL ",$VC_auto_alt_dial_statuses)) and ($UD_DNC_internal > 0) ) )
					{
					$stmt="UPDATE vicidial_hopper set status='DNC' where lead_id='$lead_id' and status='HOLD' limit 1;";
						if ($format=='debug') {echo "\n<!-- $stmt -->";}
					$rslt=mysql_query($stmt, $link);
					if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00157',$user,$server_ip,$session_name,$one_mysql_log);}
					}
				}
			else
				{
				$stmt="UPDATE vicidial_hopper set status='READY' where lead_id='$lead_id' and status='HOLD' limit 1;";
					if ($format=='debug') {echo "\n<!-- $stmt -->";}
				$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00157',$user,$server_ip,$session_name,$one_mysql_log);}
				}
			}
		}
	else
		{
		$stmt="DELETE from vicidial_hopper where lead_id='$lead_id' and status='HOLD';";
			if ($format=='debug') {echo "\n<!-- $stmt -->";}
		$rslt=mysql_query($stmt, $link);
			if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00158',$user,$server_ip,$session_name,$one_mysql_log);}
		}

	####### START Vtiger Call Logging #######
	$stmt = "SELECT enable_vtiger_integration,vtiger_server_ip,vtiger_dbname,vtiger_login,vtiger_pass,vtiger_url FROM system_settings;";
	$rslt=mysql_query($stmt, $link);
		if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00197',$user,$server_ip,$session_name,$one_mysql_log);}
	$ss_conf_ct = mysql_num_rows($rslt);
	if ($ss_conf_ct > 0)
		{
		$row=mysql_fetch_row($rslt);
		$enable_vtiger_integration =	$row[0];
		$vtiger_server_ip	=			$row[1];
		$vtiger_dbname =				$row[2];
		$vtiger_login =					$row[3];
		$vtiger_pass =					$row[4];
		$vtiger_url =					$row[5];
		}

	if ($enable_vtiger_integration > 0)
		{
		$stmt = "SELECT vtiger_search_category,vtiger_create_call_record,vtiger_create_lead_record,vtiger_search_dead,vtiger_status_call FROM vicidial_campaigns where campaign_id='$campaign';";
		$rslt=mysql_query($stmt, $link);
			if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00198',$user,$server_ip,$session_name,$one_mysql_log);}
		$vtc_conf_ct = mysql_num_rows($rslt);
		if ($vtc_conf_ct > 0)
			{
			$row=mysql_fetch_row($rslt);
			$vtiger_search_category =		$row[0];
			$vtiger_create_call_record =	$row[1];
			$vtiger_create_lead_record =	$row[2];
			$vtiger_search_dead =			$row[3];
			$vtiger_status_call =			$row[4];
			}
		if ( (ereg('ACCTID',$vtiger_search_category)) or (ereg('ACCOUNT',$vtiger_search_category)) )
			{
			### find the full status name for this status
			$stmt = "select status_name from vicidial_statuses where status='$dispo_choice';";
			if ($DB) {echo "$stmt\n";}
			$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00211',$user,$server_ip,$session_name,$one_mysql_log);}
			$vs_ct = mysql_num_rows($rslt);
			if ($vs_ct > 0)
				{
				$row=mysql_fetch_row($rslt);
				$status_name =		$row[0];
				}
			else
				{
				$stmt = "select status_name from vicidial_campaign_statuses where status='$dispo_choice' and campaign_id='$campaign';";
				if ($DB) {echo "$stmt\n";}
				$rslt=mysql_query($stmt, $link);
					if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00212',$user,$server_ip,$session_name,$one_mysql_log);}
				$vs_ct = mysql_num_rows($rslt);
				if ($vs_ct > 0)
					{
					$row=mysql_fetch_row($rslt);
					$status_name =		$row[0];
					}
				}
			if (strlen($status_name) < 1) {$status_name = $dispo_choice;}

			### connect to your vtiger database
			$linkV=mysql_connect("$vtiger_server_ip", "$vtiger_login","$vtiger_pass");
			if (!$linkV) {die("Could not connect: $vtiger_server_ip|$vtiger_dbname|$vtiger_login|$vtiger_pass" . mysql_error());}
			mysql_select_db("$vtiger_dbname", $linkV);

			$stmt = "SELECT vendor_lead_code FROM vicidial_list where lead_id='$lead_id';";
			$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00210',$user,$server_ip,$session_name,$one_mysql_log);}
			$vlc_ct = mysql_num_rows($rslt);
			if ($vlc_ct > 0)
				{
				$row=mysql_fetch_row($rslt);
				$vendor_id =		$row[0];
				}

			# make sure the ID is present in Vtiger database as an account
			$stmt="SELECT count(*) from vtiger_account where accountid='$vendor_id';";
			if ($DB) {echo "$stmt\n";}
			$rslt=mysql_query($stmt, $linkV);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$linkV,$mel,$stmt,'00199',$user,$server_ip,$session_name,$one_mysql_log);}
			$row=mysql_fetch_row($rslt);
			$VIDcount = $row[0];
			if ($VIDcount > 0)
				{
				### create a call record in vtiger linked to the account
				if (ereg('DISPO',$vtiger_create_call_record))
					{
					$TODAY = date("Y-m-d");
					$HHMMnow = date("H:i");
					$minute_old = mktime(date("H"), date("i")+5, date("s"), date("m"), date("d"),  date("Y"));
					$HHMMend = date("H:i",$minute_old);

					#Get logged in user ID
					$stmt="SELECT id from vtiger_users where user_name='$user';";
					if ($DB) {echo "$stmt\n";}
					$rslt=mysql_query($stmt, $linkV);
						if ($mel > 0) {mysql_error_logging($NOW_TIME,$linkV,$mel,$stmt,'00200',$user,$server_ip,$session_name,$one_mysql_log);}
					$row=mysql_fetch_row($rslt);
					$user_id = $row[0];

					## if numbered callback activity record, alter existing record
					$vtiger_callback_modified=0;
					if ($vtiger_callback_id > 0)
						{
						# make sure the ID is present in Vtiger database as an account
						$stmt="SELECT count(*) from vtiger_seactivityrel where activityid='$vtiger_callback_id';";
						if ($DB) {echo "$stmt\n";}
						$rslt=mysql_query($stmt, $linkV);
							if ($mel > 0) {mysql_error_logging($NOW_TIME,$linkV,$mel,$stmt,'00213',$user,$server_ip,$session_name,$one_mysql_log);}
						$vt_act_ct = mysql_num_rows($rslt);
						if ($vt_act_ct > 0)
							{
							$row=mysql_fetch_row($rslt);
							$activity_check = $row[0];
							}
						if ($activity_check > 0)
							{
							$act_description='';
							$stmt="SELECT description from vtiger_crmentity where crmid='$vtiger_callback_id';";
							if ($DB) {echo "$stmt\n";}
							$rslt=mysql_query($stmt, $linkV);
								if ($mel > 0) {mysql_error_logging($NOW_TIME,$linkV,$mel,$stmt,'00214',$user,$server_ip,$session_name,$one_mysql_log);}
							$vt_actd_ct = mysql_num_rows($rslt);
							if ($vt_actd_ct > 0)
								{
								$row=mysql_fetch_row($rslt);
								$act_description = $row[0];
								}
							$act_subject='';
							$stmt="SELECT subject from vtiger_activity where activityid='$vtiger_callback_id';";
							if ($DB) {echo "$stmt\n";}
							$rslt=mysql_query($stmt, $linkV);
								if ($mel > 0) {mysql_error_logging($NOW_TIME,$linkV,$mel,$stmt,'00215',$user,$server_ip,$session_name,$one_mysql_log);}
							$vt_actd_ct = mysql_num_rows($rslt);
							if ($vt_actd_ct > 0)
								{
								$row=mysql_fetch_row($rslt);
								$act_subject = $row[0];
								}

							$stmt = "UPDATE vtiger_crmentity SET modifiedby='$user_id', description='$act_description - VICIDIAL Call user $user',modifiedtime='$NOW_TIME',viewedtime='$NOW_TIME' where crmid='$vtiger_callback_id';";
							if ($DB) {echo "|$stmt|\n";}
							$rslt=mysql_query($stmt, $linkV);
								if ($mel > 0) {mysql_error_logging($NOW_TIME,$linkV,$mel,$stmt,'00216',$user,$server_ip,$session_name,$one_mysql_log);}
							$stmt = "UPDATE vtiger_activity SET subject='VC Call: $status_name - $act_subject',date_start='$TODAY',time_start='$HHMMnow',time_end='$HHMMend',eventstatus='Held' where activityid='$vtiger_callback_id';";
							if ($DB) {echo "|$stmt|\n";}
							$rslt=mysql_query($stmt, $linkV);
								if ($mel > 0) {mysql_error_logging($NOW_TIME,$linkV,$mel,$stmt,'00217',$user,$server_ip,$session_name,$one_mysql_log);}
							$vtiger_callback_modified=1;
							}
						}

					## create new activity record
					if ($vtiger_callback_modified < 1)
						{
						# Get next aviable id from vtiger_crmentity_seq to use as activityid in vtiger_crmentity	
						$stmt="SELECT id from vtiger_crmentity_seq ;";
						if ($DB) {echo "$stmt\n";}
						$rslt=mysql_query($stmt, $linkV);
							if ($mel > 0) {mysql_error_logging($NOW_TIME,$linkV,$mel,$stmt,'00201',$user,$server_ip,$session_name,$one_mysql_log);}
						$row=mysql_fetch_row($rslt);
						$activityid = ($row[0] + 1);

						# Increase next aviable crmid with 1 so next record gets proper id
						$stmt="UPDATE vtiger_crmentity_seq SET id = '$activityid';";
						if ($DB) {echo "$stmt\n";}
						$rslt=mysql_query($stmt, $linkV);
							if ($mel > 0) {mysql_error_logging($NOW_TIME,$linkV,$mel,$stmt,'00202',$user,$server_ip,$session_name,$one_mysql_log);}
						
						#Insert values into vtiger_salesmanactivityrel
						$stmt = "INSERT INTO vtiger_salesmanactivityrel SET smid='$user_id',activityid='$activityid';";
						if ($DB) {echo "|$stmt|\n";}
						$rslt=mysql_query($stmt, $linkV);
							if ($mel > 0) {mysql_error_logging($NOW_TIME,$linkV,$mel,$stmt,'00203',$user,$server_ip,$session_name,$one_mysql_log);}
						
						#Insert values into vtiger_seactivityrel
						$stmt = "INSERT INTO vtiger_seactivityrel SET crmid='$vendor_id',activityid='$activityid';";
						if ($DB) {echo "|$stmt|\n";}
						$rslt=mysql_query($stmt, $linkV);
							if ($mel > 0) {mysql_error_logging($NOW_TIME,$linkV,$mel,$stmt,'00204',$user,$server_ip,$session_name,$one_mysql_log);}
						
						#Insert values into vtiger_crmentity
						$stmt = "INSERT INTO vtiger_crmentity (crmid, smcreatorid, smownerid, modifiedby, setype, description, createdtime, modifiedtime, viewedtime, status, version, presence, deleted) VALUES ('$activityid', '$user_id', '$user_id','$user_id', 'Calendar', 'VICIDIAL Call user $user', '$NOW_TIME', '$NOW_TIME', '$NOW_TIME', NULL, '0', '1', '0');";
						if ($DB) {echo "|$stmt|\n";}
						$rslt=mysql_query($stmt, $linkV);
							if ($mel > 0) {mysql_error_logging($NOW_TIME,$linkV,$mel,$stmt,'00205',$user,$server_ip,$session_name,$one_mysql_log);}

						#Insert values into vtiger_activity
						$stmt = "INSERT INTO vtiger_activity SET activityid='$activityid',subject='VC Call: $status_name',activitytype='Call',date_start='$TODAY',due_date='$TODAY',time_start='$HHMMnow',time_end='$HHMMend',sendnotification='0',duration_hours='0',duration_minutes='1',status='',eventstatus='Held',priority='Medium',location='VICIDIAL Användare $user',notime='0',visibility='Public',recurringtype='--None--';";
						if ($DB) {echo "|$stmt|\n";}
						$rslt=mysql_query($stmt, $linkV);
							if ($mel > 0) {mysql_error_logging($NOW_TIME,$linkV,$mel,$stmt,'00206',$user,$server_ip,$session_name,$one_mysql_log);}
						if ($DB) {echo "|$leadid|\n";}
						}
					}
				### update the status of the record in vtiger
				if (ereg('Y',$vtiger_status_call))
					{
					#Get logged in user ID
					$stmt="SELECT id from vtiger_users where user_name='$user';";
					if ($DB) {echo "$stmt\n";}
					$rslt=mysql_query($stmt, $linkV);
						if ($mel > 0) {mysql_error_logging($NOW_TIME,$linkV,$mel,$stmt,'00207',$user,$server_ip,$session_name,$one_mysql_log);}
					$row=mysql_fetch_row($rslt);
					$user_id = $row[0];

					#Update vtiger_crmentity
					$stmt = "UPDATE vtiger_crmentity SET modifiedby='$user_id', modifiedtime='$NOW_TIME' where crmid='$vendor_id';";
					if ($DB) {echo "|$stmt|\n";}
					$rslt=mysql_query($stmt, $linkV);
						if ($mel > 0) {mysql_error_logging($NOW_TIME,$linkV,$mel,$stmt,'00208',$user,$server_ip,$session_name,$one_mysql_log);}
					if ($DB) {echo "|$leadid|\n";}

					if ($insert_into_dnc > 0) {$emailoptoutSQL = ", emailoptout='1'";}
					#Update vtiger_account   dnc=emailoptout
					$stmt = "UPDATE vtiger_account SET siccode='$status_name' $emailoptoutSQL where accountid='$vendor_id';";
					if ($DB) {echo "|$stmt|\n";}
					$rslt=mysql_query($stmt, $linkV);
						if ($mel > 0) {mysql_error_logging($NOW_TIME,$linkV,$mel,$stmt,'00209',$user,$server_ip,$session_name,$one_mysql_log);}
					if ($DB) {echo "|$leadid|\n";}


					### check and see if the custom date fields exist, if they do then update them if necessary
					# Date of Efternamn Attempt, Date of Efternamn Non-Contact, Date of Efternamn Contact, Date of Efternamn Sale

					# first find the sale statuses that are in the system
					$SALE_string_check = '|';
					$stmt = "SELECT status FROM vicidial_statuses where sale='Y';";
					$rslt=mysql_query($stmt, $link);
						if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00211',$user,$server_ip,$session_name,$one_mysql_log);}
					if ($DB) {echo "$stmt\n";}
					$salevs_ct = mysql_num_rows($rslt);
					$i=0;
					while ($i < $salevs_ct)
						{
						$row=mysql_fetch_row($rslt);
						$SALE_string_check .= "$row[0]|";
						$i++;
						}
					$stmt = "SELECT status FROM vicidial_campaign_statuses where sale='Y';";
					$rslt=mysql_query($stmt, $link);
						if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00212',$user,$server_ip,$session_name,$one_mysql_log);}
					if ($DB) {echo "$stmt\n";}
					$salevcs_ct = mysql_num_rows($rslt);
					$i=0;
					while ($i < $salevcs_ct)
						{
						$row=mysql_fetch_row($rslt);
						$SALE_string_check .= "$row[0]|";
						$i++;
						}

					# second find the customer contact statuses that are in the system
					$CC_string_check = '|';
					$stmt = "SELECT status FROM vicidial_statuses where customer_contact='Y';";
					$rslt=mysql_query($stmt, $link);
						if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00213',$user,$server_ip,$session_name,$one_mysql_log);}
					if ($DB) {echo "$stmt\n";}
					$cc_vs_ct = mysql_num_rows($rslt);
					$i=0;
					while ($i < $cc_vs_ct)
						{
						$row=mysql_fetch_row($rslt);
						$CC_string_check .= "$row[0]|";
						$i++;
						}
					$stmt = "SELECT status FROM vicidial_campaign_statuses where customer_contact='Y';";
					$rslt=mysql_query($stmt, $link);
						if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00287',$user,$server_ip,$session_name,$one_mysql_log);}
					if ($DB) {echo "$stmt\n";}
					$cc_vcs_ct = mysql_num_rows($rslt);
					$i=0;
					while ($i < $cc_vcs_ct)
						{
						$row=mysql_fetch_row($rslt);
						$CC_string_check .= "$row[0]|";
						$i++;
						}

					# third calculate what custom date fields need their date updated
					$VT_last_noncontact_update=0;	$VT_last_noncontact_ct=0;
					$VT_last_contact_update=0;		$VT_last_contact_ct=0;
					$VT_last_sale_update=0;			$VT_last_sale_ct=0;
					if (eregi("\|$dispo_choice\|", $SALE_string_check) )
						{$VT_last_sale_update++;}
					if (eregi("\|$dispo_choice\|", $CC_string_check) )
						{$VT_last_contact_update++;}
					else
						{$VT_last_noncontact_update++;}

					# fourth see if the vtiger database has the custom date fields in it
					$stmt="SELECT count(*) from vtiger_field where fieldlabel='Date of Last Attempt';";
					if ($DB) {echo "$stmt\n";}
					$rslt=mysql_query($stmt, $linkV);
						if ($mel > 0) {mysql_error_logging($NOW_TIME,$linkV,$mel,$stmt,'00215',$user,$server_ip,$session_name,$one_mysql_log);}
					$row=mysql_fetch_row($rslt);
					$VT_last_attempt_ct = $row[0];

					if ($VT_last_noncontact_update > 0)
						{
						$stmt="SELECT count(*) from vtiger_field where fieldlabel='Date of Last Non-Contact';";
						if ($DB) {echo "$stmt\n";}
						$rslt=mysql_query($stmt, $linkV);
							if ($mel > 0) {mysql_error_logging($NOW_TIME,$linkV,$mel,$stmt,'00216',$user,$server_ip,$session_name,$one_mysql_log);}
						$row=mysql_fetch_row($rslt);
						$VT_last_noncontact_ct = $row[0];
						}
					if ($VT_last_contact_update > 0)
						{
						$stmt="SELECT count(*) from vtiger_field where fieldlabel='Date of Last Contact';";
						if ($DB) {echo "$stmt\n";}
						$rslt=mysql_query($stmt, $linkV);
							if ($mel > 0) {mysql_error_logging($NOW_TIME,$linkV,$mel,$stmt,'00217',$user,$server_ip,$session_name,$one_mysql_log);}
						$row=mysql_fetch_row($rslt);
						$VT_last_contact_ct = $row[0];
						}
					if ($VT_last_sale_update > 0)
						{
						$stmt="SELECT count(*) from vtiger_field where fieldlabel='Date of Last Sale';";
						if ($DB) {echo "$stmt\n";}
						$rslt=mysql_query($stmt, $linkV);
							if ($mel > 0) {mysql_error_logging($NOW_TIME,$linkV,$mel,$stmt,'00218',$user,$server_ip,$session_name,$one_mysql_log);}
						$row=mysql_fetch_row($rslt);
						$VT_last_sale_ct = $row[0];
						}

					# fifth find the fieldnames if they exist and update the dates
					if ($VT_last_attempt_ct > 0)
						{
						$stmt="SELECT fieldname from vtiger_field where fieldlabel='Date of Last Attempt';";
						if ($DB) {echo "$stmt\n";}
						$rslt=mysql_query($stmt, $linkV);
							if ($mel > 0) {mysql_error_logging($NOW_TIME,$linkV,$mel,$stmt,'00219',$user,$server_ip,$session_name,$one_mysql_log);}
						$row=mysql_fetch_row($rslt);
						$VT_last_attempt_field = $row[0];

						$stmt = "UPDATE vtiger_accountscf SET $VT_last_attempt_field='$TODAY' where accountid='$vendor_id';";
						if ($DB) {echo "|$stmt|\n";}
						$rslt=mysql_query($stmt, $linkV);
							if ($mel > 0) {mysql_error_logging($NOW_TIME,$linkV,$mel,$stmt,'00223',$user,$server_ip,$session_name,$one_mysql_log);}
						}
					if ($VT_last_noncontact_ct > 0)
						{
						$stmt="SELECT fieldname from vtiger_field where fieldlabel='Date of Last Non-Contact';";
						if ($DB) {echo "$stmt\n";}
						$rslt=mysql_query($stmt, $linkV);
							if ($mel > 0) {mysql_error_logging($NOW_TIME,$linkV,$mel,$stmt,'00220',$user,$server_ip,$session_name,$one_mysql_log);}
						$row=mysql_fetch_row($rslt);
						$VT_last_noncontact_field = $row[0];

						$stmt = "UPDATE vtiger_accountscf SET $VT_last_noncontact_field='$TODAY' where accountid='$vendor_id';";
						if ($DB) {echo "|$stmt|\n";}
						$rslt=mysql_query($stmt, $linkV);
							if ($mel > 0) {mysql_error_logging($NOW_TIME,$linkV,$mel,$stmt,'00224',$user,$server_ip,$session_name,$one_mysql_log);}
						}
					if ($VT_last_contact_ct > 0)
						{
						$stmt="SELECT fieldname from vtiger_field where fieldlabel='Date of Last Contact';";
						if ($DB) {echo "$stmt\n";}
						$rslt=mysql_query($stmt, $linkV);
							if ($mel > 0) {mysql_error_logging($NOW_TIME,$linkV,$mel,$stmt,'00221',$user,$server_ip,$session_name,$one_mysql_log);}
						$row=mysql_fetch_row($rslt);
						$VT_last_contact_field = $row[0];

						$stmt = "UPDATE vtiger_accountscf SET $VT_last_contact_field='$TODAY' where accountid='$vendor_id';";
						if ($DB) {echo "|$stmt|\n";}
						$rslt=mysql_query($stmt, $linkV);
							if ($mel > 0) {mysql_error_logging($NOW_TIME,$linkV,$mel,$stmt,'00225',$user,$server_ip,$session_name,$one_mysql_log);}
						}
					if ($VT_last_sale_ct > 0)
						{
						$stmt="SELECT fieldname from vtiger_field where fieldlabel='Date of Last Sale';";
						if ($DB) {echo "$stmt\n";}
						$rslt=mysql_query($stmt, $linkV);
							if ($mel > 0) {mysql_error_logging($NOW_TIME,$linkV,$mel,$stmt,'00222',$user,$server_ip,$session_name,$one_mysql_log);}
						$row=mysql_fetch_row($rslt);
						$VT_last_sale_field = $row[0];

						$stmt = "UPDATE vtiger_accountscf SET $VT_last_sale_field='$TODAY' where accountid='$vendor_id';";
						if ($DB) {echo "|$stmt|\n";}
						$rslt=mysql_query($stmt, $linkV);
							if ($mel > 0) {mysql_error_logging($NOW_TIME,$linkV,$mel,$stmt,'00226',$user,$server_ip,$session_name,$one_mysql_log);}
						}
					}
				}
			}
		}
	####### END Vtiger Call Logging #######

	#############################################
	##### START QUEUEMETRICS LOGGING LOOKUP #####
	$stmt = "SELECT enable_queuemetrics_logging,queuemetrics_server_ip,queuemetrics_dbname,queuemetrics_login,queuemetrics_pass,queuemetrics_log_id FROM system_settings;";
	$rslt=mysql_query($stmt, $link);
			if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00159',$user,$server_ip,$session_name,$one_mysql_log);}
	if ($DB) {echo "$stmt\n";}
	$qm_conf_ct = mysql_num_rows($rslt);
	if ($qm_conf_ct > 0)
		{
		$row=mysql_fetch_row($rslt);
		$enable_queuemetrics_logging =	$row[0];
		$queuemetrics_server_ip	=		$row[1];
		$queuemetrics_dbname =			$row[2];
		$queuemetrics_login	=			$row[3];
		$queuemetrics_pass =			$row[4];
		$queuemetrics_log_id =			$row[5];
		}
	##### END QUEUEMETRICS LOGGING LOOKUP #####
	###########################################
	if ($enable_queuemetrics_logging > 0)
		{
		$linkB=mysql_connect("$queuemetrics_server_ip", "$queuemetrics_login", "$queuemetrics_pass");
		mysql_select_db("$queuemetrics_dbname", $linkB);

		if (strlen($stage) < 2) 
			{$stage = $campaign;}

		$stmt = "INSERT INTO queue_log SET partition='P01',time_id='$StarTtime',call_id='$MDnextCID',queue='$stage',agent='Agent/$user',verb='CALLSTATUS',data1='$dispo_choice',serverid='$queuemetrics_log_id';";
		if ($DB) {echo "$stmt\n";}
		$rslt=mysql_query($stmt, $linkB);
			if ($mel > 0) {mysql_error_logging($NOW_TIME,$linkB,$mel,$stmt,'00160',$user,$server_ip,$session_name,$one_mysql_log);}
		$affected_rows = mysql_affected_rows($linkB);

		mysql_close($linkB);
		}

	### Issue Dispo Call URL if defined
	if (strlen($dispo_call_url) > 7)
		{
		$talk_time=0;
		$talk_time_ms=0;
		$talk_time_min=0;
		if (eregi('--A--user_custom_',$dispo_call_url))
			{
			$stmt = "select custom_one,custom_two,custom_three,custom_four,custom_five from vicidial_users where user='$user';";
			if ($DB) {echo "$stmt\n";}
			$rslt=mysql_query($stmt, $link);
		if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00288',$user,$server_ip,$session_name,$one_mysql_log);}
			$VUC_ct = mysql_num_rows($rslt);
			if ($VUC_ct > 0)
				{
				$row=mysql_fetch_row($rslt);
				$user_custom_one	=		urlencode(trim($row[0]));
				$user_custom_two	=		urlencode(trim($row[1]));
				$user_custom_three	=		urlencode(trim($row[2]));
				$user_custom_four	=		urlencode(trim($row[3]));
				$user_custom_five	=		urlencode(trim($row[4]));
				}
			}

		if (eregi('--A--talk_time',$dispo_call_url))
			{
			$stmt = "select talk_sec,dead_sec from vicidial_agent_log where lead_id='$lead_id' and agent_log_id='$CALL_agent_log_id';";
			if ($DB) {echo "$stmt\n";}
			$rslt=mysql_query($stmt, $link);
		if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00289',$user,$server_ip,$session_name,$one_mysql_log);}
			$VAL_talk_ct = mysql_num_rows($rslt);
			if ($VAL_talk_ct > 0)
				{
				$row=mysql_fetch_row($rslt);
				$talk_sec	=		$row[0];
				$dead_sec	=		$row[1];
				$talk_time = ($talk_sec - $dead_sec);
				if ($talk_time < 1)
					{
					$talk_time = 0;
					$talk_time_ms = 0;
					}
				else
					{
					$talk_time_ms = ($talk_time * 1000);
					$talk_time_min = ceil($talk_time / 60);
					}
				}
			}

		if (eregi('--A--dispo_name--B--',$dispo_call_url))
			{
			### find the full status name for this status
			$stmt = "select status_name from vicidial_statuses where status='$dispo_choice';";
			if ($DB) {echo "$stmt\n";}
			$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00297',$user,$server_ip,$session_name,$one_mysql_log);}
			$vs_name_ct = mysql_num_rows($rslt);
			if ($vs_name_ct > 0)
				{
				$row=mysql_fetch_row($rslt);
				$status_name =		$row[0];
				}
			else
				{
				$stmt = "select status_name from vicidial_campaign_statuses where status='$dispo_choice' and campaign_id='$campaign';";
				if ($DB) {echo "$stmt\n";}
				$rslt=mysql_query($stmt, $link);
					if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00298',$user,$server_ip,$session_name,$one_mysql_log);}
				$vcs_name_ct = mysql_num_rows($rslt);
				if ($vcs_name_ct > 0)
					{
					$row=mysql_fetch_row($rslt);
					$status_name =		$row[0];
					}
				}
			if (strlen($status_name) < 1) {$status_name = $dispo_choice;}
			}
		$dispo_name = urlencode(trim($status_name));
			


		##### grab the data from vicidial_list for the lead_id
		$stmt="SELECT lead_id,entry_date,modify_date,status,user,vendor_lead_code,source_id,list_id,gmt_offset_now,called_since_last_reset,phone_code,phone_number,title,first_name,middle_initial,last_name,address1,address2,address3,city,state,province,postal_code,country_code,gender,date_of_birth,alt_phone,email,security_phrase,comments,called_count,last_local_call_time,rank,owner FROM vicidial_list where lead_id='$lead_id' LIMIT 1;";
		$rslt=mysql_query($stmt, $link);
			if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00290',$user,$server_ip,$session_name,$one_mysql_log);}
		if ($DB) {echo "$stmt\n";}
		$list_lead_ct = mysql_num_rows($rslt);
		if ($list_lead_ct > 0)
			{
			$row=mysql_fetch_row($rslt);
			$dispo			= urlencode(trim($row[3]));
			$tsr			= urlencode(trim($row[4]));
			$vendor_id		= urlencode(trim($row[5]));
			$vendor_lead_code	= urlencode(trim($row[5]));
			$source_id		= urlencode(trim($row[6]));
			$list_id		= urlencode(trim($row[7]));
			$gmt_offset_now	= urlencode(trim($row[8]));
			$phone_code		= urlencode(trim($row[10]));
			$phone_number	= urlencode(trim($row[11]));
			$title			= urlencode(trim($row[12]));
			$first_name		= urlencode(trim($row[13]));
			$middle_initial	= urlencode(trim($row[14]));
			$last_name		= urlencode(trim($row[15]));
			$address1		= urlencode(trim($row[16]));
			$address2		= urlencode(trim($row[17]));
			$address3		= urlencode(trim($row[18]));
			$city			= urlencode(trim($row[19]));
			$state			= urlencode(trim($row[20]));
			$province		= urlencode(trim($row[21]));
			$postal_code	= urlencode(trim($row[22]));
			$country_code	= urlencode(trim($row[23]));
			$gender			= urlencode(trim($row[24]));
			$date_of_birth	= urlencode(trim($row[25]));
			$alt_phone		= urlencode(trim($row[26]));
			$email			= urlencode(trim($row[27]));
			$security		= urlencode(trim($row[28]));
			$comments		= urlencode(trim($row[29]));
			$called_count	= urlencode(trim($row[30]));
			$rank			= urlencode(trim($row[32]));
			$owner			= urlencode(trim($row[33]));
			}

		$dispo_call_url = preg_replace('/^VAR/','',$dispo_call_url);
		$dispo_call_url = eregi_replace('--A--lead_id--B--',"$lead_id",$dispo_call_url);
		$dispo_call_url = eregi_replace('--A--vendor_id--B--',"$vendor_id",$dispo_call_url);
		$dispo_call_url = eregi_replace('--A--vendor_lead_code--B--',"$vendor_lead_code",$dispo_call_url);
		$dispo_call_url = eregi_replace('--A--list_id--B--',"$list_id",$dispo_call_url);
		$dispo_call_url = eregi_replace('--A--gmt_offset_now--B--',"$gmt_offset_now",$dispo_call_url);
		$dispo_call_url = eregi_replace('--A--phone_code--B--',"$phone_code",$dispo_call_url);
		$dispo_call_url = eregi_replace('--A--phone_number--B--',"$phone_number",$dispo_call_url);
		$dispo_call_url = eregi_replace('--A--title--B--',"$title",$dispo_call_url);
		$dispo_call_url = eregi_replace('--A--first_name--B--',"$first_name",$dispo_call_url);
		$dispo_call_url = eregi_replace('--A--middle_initial--B--',"$middle_initial",$dispo_call_url);
		$dispo_call_url = eregi_replace('--A--last_name--B--',"$last_name",$dispo_call_url);
		$dispo_call_url = eregi_replace('--A--address1--B--',"$address1",$dispo_call_url);
		$dispo_call_url = eregi_replace('--A--address2--B--',"$address2",$dispo_call_url);
		$dispo_call_url = eregi_replace('--A--address3--B--',"$address3",$dispo_call_url);
		$dispo_call_url = eregi_replace('--A--city--B--',"$city",$dispo_call_url);
		$dispo_call_url = eregi_replace('--A--state--B--',"$state",$dispo_call_url);
		$dispo_call_url = eregi_replace('--A--province--B--',"$province",$dispo_call_url);
		$dispo_call_url = eregi_replace('--A--postal_code--B--',"$postal_code",$dispo_call_url);
		$dispo_call_url = eregi_replace('--A--country_code--B--',"$country_code",$dispo_call_url);
		$dispo_call_url = eregi_replace('--A--gender--B--',"$gender",$dispo_call_url);
		$dispo_call_url = eregi_replace('--A--date_of_birth--B--',"$date_of_birth",$dispo_call_url);
		$dispo_call_url = eregi_replace('--A--alt_phone--B--',"$alt_phone",$dispo_call_url);
		$dispo_call_url = eregi_replace('--A--email--B--',"$email",$dispo_call_url);
		$dispo_call_url = eregi_replace('--A--security_phrase--B--',"$security_phrase",$dispo_call_url);
		$dispo_call_url = eregi_replace('--A--comments--B--',"$comments",$dispo_call_url);
		$dispo_call_url = eregi_replace('--A--user--B--',"$user",$dispo_call_url);
		$dispo_call_url = eregi_replace('--A--pass--B--',"$pass",$dispo_call_url);
		$dispo_call_url = eregi_replace('--A--campaign--B--',"$campaign",$dispo_call_url);
		$dispo_call_url = eregi_replace('--A--phone_login--B--',"$phone_login",$dispo_call_url);
		$dispo_call_url = eregi_replace('--A--original_phone_login--B--',"$original_phone_login",$dispo_call_url);
		$dispo_call_url = eregi_replace('--A--phone_pass--B--',"$phone_pass",$dispo_call_url);
		$dispo_call_url = eregi_replace('--A--fronter--B--',"$fronter",$dispo_call_url);
		$dispo_call_url = eregi_replace('--A--closer--B--',"$closer",$dispo_call_url);
		$dispo_call_url = eregi_replace('--A--group--B--',"$group",$dispo_call_url);
		$dispo_call_url = eregi_replace('--A--channel_group--B--',"$channel_group",$dispo_call_url);
		$dispo_call_url = eregi_replace('--A--SQLdate--B--',"$SQLdate",$dispo_call_url);
		$dispo_call_url = eregi_replace('--A--epoch--B--',"$epoch",$dispo_call_url);
		$dispo_call_url = eregi_replace('--A--uniqueid--B--',"$uniqueid",$dispo_call_url);
		$dispo_call_url = eregi_replace('--A--customer_zap_channel--B--',"$customer_zap_channel",$dispo_call_url);
		$dispo_call_url = eregi_replace('--A--customer_server_ip--B--',"$customer_server_ip",$dispo_call_url);
		$dispo_call_url = eregi_replace('--A--server_ip--B--',"$server_ip",$dispo_call_url);
		$dispo_call_url = eregi_replace('--A--SIPexten--B--',"$SIPexten",$dispo_call_url);
		$dispo_call_url = eregi_replace('--A--session_id--B--',"$session_id",$dispo_call_url);
		$dispo_call_url = eregi_replace('--A--phone--B--',"$phone",$dispo_call_url);
		$dispo_call_url = eregi_replace('--A--parked_by--B--',"$parked_by",$dispo_call_url);
		$dispo_call_url = eregi_replace('--A--dispo--B--',"$dispo",$dispo_call_url);
		$dispo_call_url = eregi_replace('--A--dispo_name--B--',"$dispo_name",$dispo_call_url);
		$dispo_call_url = eregi_replace('--A--dialed_number--B--',"$dialed_number",$dispo_call_url);
		$dispo_call_url = eregi_replace('--A--dialed_label--B--',"$dialed_label",$dispo_call_url);
		$dispo_call_url = eregi_replace('--A--source_id--B--',"$source_id",$dispo_call_url);
		$dispo_call_url = eregi_replace('--A--rank--B--',"$rank",$dispo_call_url);
		$dispo_call_url = eregi_replace('--A--owner--B--',"$owner",$dispo_call_url);
		$dispo_call_url = eregi_replace('--A--camp_script--B--',"$camp_script",$dispo_call_url);
		$dispo_call_url = eregi_replace('--A--in_script--B--',"$in_script",$dispo_call_url);
		$dispo_call_url = eregi_replace('--A--fullname--B--',"$fullname",$dispo_call_url);
		$dispo_call_url = eregi_replace('--A--user_custom_one--B--',"$user_custom_one",$dispo_call_url);
		$dispo_call_url = eregi_replace('--A--user_custom_two--B--',"$user_custom_two",$dispo_call_url);
		$dispo_call_url = eregi_replace('--A--user_custom_three--B--',"$user_custom_three",$dispo_call_url);
		$dispo_call_url = eregi_replace('--A--user_custom_four--B--',"$user_custom_four",$dispo_call_url);
		$dispo_call_url = eregi_replace('--A--user_custom_five--B--',"$user_custom_five",$dispo_call_url);
		$dispo_call_url = eregi_replace('--A--talk_time--B--',"$talk_time",$dispo_call_url);
		$dispo_call_url = eregi_replace('--A--talk_time_ms--B--',"$talk_time_ms",$dispo_call_url);
		$dispo_call_url = eregi_replace('--A--talk_time_min--B--',"$talk_time_min",$dispo_call_url);
		$dispo_call_url = eregi_replace('--A--agent_log_id--B--',"$CALL_agent_log_id",$dispo_call_url);
		if ($DB > 0) {echo "$dispo_call_url<BR>\n";}
		$SCUfile = file("$dispo_call_url");
		if ($DB > 0) {echo "$SCUfile[0]<BR>\n";}

		$stmt = "SELECT enable_vtiger_integration FROM system_settings;";
		$rslt=mysql_query($stmt, $link);
			if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00296',$user,$server_ip,$session_name,$one_mysql_log);}
		$ss_conf_ct = mysql_num_rows($rslt);
		if ($ss_conf_ct > 0)
			{
			$row=mysql_fetch_row($rslt);
			$enable_vtiger_integration =	$row[0];
			}
		if ( ($enable_vtiger_integration > 0) and (ereg('mode=callend',$dispo_call_url)) and (ereg('contactwsid',$dispo_call_url)) )
			{
			$SCUoutput='';
			foreach ($SCUfile as $SCUline) 
				{$SCUoutput .= "$SCUline";}
			$fp = fopen ("./call_url_log.txt", "a");
			fwrite ($fp, "$dispo_call_url\n$SCUoutput\n");
			fclose($fp);
			}
		}
	echo 'Lead ' . $lead_id . ' har blivit ändrad till ' . $dispo_choice . " Status\nNext agent_log_id:\n" . $agent_log_id . "\n";
	}

################################################################################
### updateLEAD - update the vicidial_list table to reflect the values that are
###              in the agents screen at time of call hangup
################################################################################
if ($ACTION == 'updateLEAD')
	{
	$MT[0]='';
	$row='';   $rowx='';
	$DO_NOT_UPDATE=0;
	$DO_NOT_UPDATE_text='';
	if ( (strlen($phone_number)<1) || (strlen($lead_id)<1) )
		{
		echo "phone_number $phone_number or lead_id $lead_id är ej giltig\n";
		exit;
		}
	else
		{
		$stmt = "SELECT disable_alter_custdata,disable_alter_custphone FROM vicidial_campaigns where campaign_id='$campaign'";
		$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00161',$user,$server_ip,$session_name,$one_mysql_log);}
		if ($DB) {echo "$stmt\n";}
		$dac_conf_ct = mysql_num_rows($rslt);
		$i=0;
		while ($i < $dac_conf_ct)
			{
			$row=mysql_fetch_row($rslt);
			$disable_alter_custdata =	$row[0];
			$disable_alter_custphone =	$row[1];
			$i++;
			}
		if ( (ereg('Y',$disable_alter_custdata)) or (ereg('Y',$disable_alter_custphone)) )
			{
			if (ereg('Y',$disable_alter_custdata))
				{
				$DO_NOT_UPDATE=1;
				$DO_NOT_UPDATE_text=' NOT';
				}
			if (ereg('Y',$disable_alter_custphone))
				{
				$DO_NOT_UPDATEphone=1;
				}
			$stmt = "SELECT alter_custdata_override,alter_custphone_override FROM vicidial_users where user='$user'";
			$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00162',$user,$server_ip,$session_name,$one_mysql_log);}
			if ($DB) {echo "$stmt\n";}
			$aco_conf_ct = mysql_num_rows($rslt);
			$i=0;
			while ($i < $aco_conf_ct)
				{
				$row=mysql_fetch_row($rslt);
				$alter_custdata_override =	$row[0];
				$alter_custphone_override = $row[1];
				$i++;
				}
			if (ereg('ALLOW_ALTER',$alter_custdata_override))
				{
				$DO_NOT_UPDATE=0;
				$DO_NOT_UPDATE_text='';
				}
			if (ereg('ALLOW_ALTER',$alter_custphone_override))
				{
				$DO_NOT_UPDATEphone=0;
				}
			}

		if ($DO_NOT_UPDATE < 1)
			{
			$comments = eregi_replace("\r",'',$comments);
			$comments = eregi_replace("\n",'!N',$comments);
			$comments = eregi_replace("--AMP--",'&',$comments);
			$comments = eregi_replace("--QUES--",'?',$comments);
			$comments = eregi_replace("--POUND--",'#',$comments);

			$phoneSQL='';
			if ($DO_NOT_UPDATEphone < 1)
				{$phoneSQL = ",phone_number='$phone_number'";}

			$stmt="UPDATE vicidial_list set vendor_lead_code='" . mysql_real_escape_string($vendor_lead_code) . "', title='" . mysql_real_escape_string($title) . "', first_name='" . mysql_real_escape_string($first_name) . "', middle_initial='" . mysql_real_escape_string($middle_initial) . "', last_name='" . mysql_real_escape_string($last_name) . "', address1='" . mysql_real_escape_string($address1) . "', address2='" . mysql_real_escape_string($address2) . "', address3='" . mysql_real_escape_string($address3) . "', city='" . mysql_real_escape_string($city) . "', state='" . mysql_real_escape_string($state) . "', province='" . mysql_real_escape_string($province) . "', postal_code='" . mysql_real_escape_string($postal_code) . "', country_code='" . mysql_real_escape_string($country_code) . "', gender='" . mysql_real_escape_string($gender) . "', date_of_birth='" . mysql_real_escape_string($date_of_birth) . "', alt_phone='" . mysql_real_escape_string($alt_phone) . "', email='" . mysql_real_escape_string($email) . "', security_phrase='" . mysql_real_escape_string($security_phrase) . "', comments='" . mysql_real_escape_string($comments) . "' $phoneSQL where lead_id='$lead_id';";
				if ($format=='debug') {echo "\n<!-- $stmt -->";}
			$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00163',$user,$server_ip,$session_name,$one_mysql_log);}
			}

		$random = (rand(1000000, 9999999) + 10000000);
		$stmt="UPDATE vicidial_live_agents set random_id='$random' where user='$user' and server_ip='$server_ip';";
			if ($format=='debug') {echo "\n<!-- $stmt -->";}
		$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {$errno = mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00164',$user,$server_ip,$session_name,$one_mysql_log);}
		$retry_count=0;
		while ( ($errno > 0) and ($retry_count < 9) )
			{
			$rslt=mysql_query($stmt, $link);
			$one_mysql_log=1;
			$errno = mysql_error_logging($NOW_TIME,$link,$mel,$stmt,"9164$retry_count",$user,$server_ip,$session_name,$one_mysql_log);
			$one_mysql_log=0;
			$retry_count++;
			}

		}
		echo "Lead $lead_id information has$DO_NOT_UPDATE_text been updated\n";
	}


################################################################################
### VDADpause - update the vicidial_live_agents table to show that the agent is
###  or ready   now active and ready to take calls
################################################################################
if ( ($ACTION == 'VDADpause') || ($ACTION == 'VDADready') )
	{
	$MT[0]='';
	$row='';   $rowx='';
	if ( (strlen($stage)<2) || (strlen($server_ip)<1) )
		{
		echo "stage $stage är ej giltig\n";
		exit;
		}
	else
		{
		$vla_autodialSQL='';
		if (preg_match('/INBOUND_MAN/',$dial_method))
			{$vla_autodialSQL = ",outbound_autodial='N'";}
		$random = (rand(1000000, 9999999) + 10000000);
		$stmt="UPDATE vicidial_live_agents set uniqueid=0,callerid='',channel='', random_id='$random',comments='',last_state_change='$NOW_TIME' $vla_autodialSQL where user='$user' and server_ip='$server_ip';";
			if ($format=='debug') {echo "\n<!-- $stmt -->";}
		$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {$errno = mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00165',$user,$server_ip,$session_name,$one_mysql_log);}
		$retry_count=0;
		while ( ($errno > 0) and ($retry_count < 9) )
			{
			$rslt=mysql_query($stmt, $link);
			$one_mysql_log=1;
			$errno = mysql_error_logging($NOW_TIME,$link,$mel,$stmt,"9165$retry_count",$user,$server_ip,$session_name,$one_mysql_log);
			$one_mysql_log=0;
			$retry_count++;
			}

		if ($comments != 'NO_STATUS_CHANGE')
			{
			$stmt="UPDATE vicidial_live_agents set status='$stage' where user='$user' and server_ip='$server_ip';";
				if ($format=='debug') {echo "\n<!-- $stmt -->";}
			$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {$errno = mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00166',$user,$server_ip,$session_name,$one_mysql_log);}
			$retry_count=0;
			while ( ($errno > 0) and ($retry_count < 9) )
				{
				$rslt=mysql_query($stmt, $link);
				$one_mysql_log=1;
				$errno = mysql_error_logging($NOW_TIME,$link,$mel,$stmt,"9166$retry_count",$user,$server_ip,$session_name,$one_mysql_log);
				$one_mysql_log=0;
				$retry_count++;
				}
			$affected_rows = mysql_affected_rows($link);
			}
		if ( ($affected_rows > 0) or ($comments == 'NO_STATUS_CHANGE') )
			{
			#############################################
			##### START QUEUEMETRICS LOGGING LOOKUP #####
			$stmt = "SELECT enable_queuemetrics_logging,queuemetrics_server_ip,queuemetrics_dbname,queuemetrics_login,queuemetrics_pass,queuemetrics_log_id FROM system_settings;";
			$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00167',$user,$server_ip,$session_name,$one_mysql_log);}
			if ($DB) {echo "$stmt\n";}
			$qm_conf_ct = mysql_num_rows($rslt);
			$i=0;
			while ($i < $qm_conf_ct)
				{
				$row=mysql_fetch_row($rslt);
				$enable_queuemetrics_logging =	$row[0];
				$queuemetrics_server_ip	=		$row[1];
				$queuemetrics_dbname =			$row[2];
				$queuemetrics_login	=			$row[3];
				$queuemetrics_pass =			$row[4];
				$queuemetrics_log_id =			$row[5];
				$i++;
				}
			##### END QUEUEMETRICS LOGGING LOOKUP #####
			###########################################
			if ($enable_queuemetrics_logging > 0)
				{
				if ( (ereg('READY',$stage)) or (ereg('CLOSER',$stage)) ) {$QMstatus='UNPAUSEALL';}
				if (ereg('PAUSE',$stage)) {$QMstatus='PAUSEALL';}
				$linkB=mysql_connect("$queuemetrics_server_ip", "$queuemetrics_login", "$queuemetrics_pass");
				mysql_select_db("$queuemetrics_dbname", $linkB);

				$user_group='';
				$stmt="SELECT user_group FROM vicidial_users where user='$user' LIMIT 1;";
				$rslt=mysql_query($stmt, $link);
					if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00182',$user,$server_ip,$session_name,$one_mysql_log);}
				if ($DB) {echo "$stmt\n";}
				$ug_record_ct = mysql_num_rows($rslt);
				if ($ug_record_ct > 0)
					{
					$row=mysql_fetch_row($rslt);
					$user_group =		trim("$row[0]");
					}

				$stmt = "INSERT INTO queue_log SET partition='P01',time_id='$StarTtime',call_id='NONE',queue='NONE',agent='Agent/$user',verb='$QMstatus',serverid='$queuemetrics_log_id';";
				if ($DB) {echo "$stmt\n";}
				$rslt=mysql_query($stmt, $linkB);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$linkB,$mel,$stmt,'00168',$user,$server_ip,$session_name,$one_mysql_log);}
				$affected_rows = mysql_affected_rows($linkB);

				mysql_close($linkB);
				}
			}

		$pause_sec=0;
		$stmt = "select pause_epoch,pause_sec,wait_epoch,wait_sec,dispo_epoch from vicidial_agent_log where agent_log_id='$agent_log_id';";
		if ($DB) {echo "$stmt\n";}
		$rslt=mysql_query($stmt, $link);
			if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00169',$user,$server_ip,$session_name,$one_mysql_log);}
		$VDpr_ct = mysql_num_rows($rslt);
		if ($VDpr_ct > 0)
			{
			$row=mysql_fetch_row($rslt);
			$dispo_epoch = $row[4];
			$wait_sec=0;
			if ($row[2] > 0)
				{
				$wait_sec = (($StarTtime - $row[2]) + $row[3]);
				}
			if ( (eregi("NULL",$row[4])) or ($row[4] < 1000) )
				{$pause_sec = (($StarTtime - $row[0]) + $row[1]);}
			else
				{$pause_sec = (($row[4] - $row[0]) + $row[1]);}

			}
		if ($ACTION == 'VDADready')
			{
			if ( (eregi("NULL",$dispo_epoch)) or ($dispo_epoch < 1000) )
				{
				$stmt="UPDATE vicidial_agent_log set pause_sec='$pause_sec',wait_epoch='$StarTtime' where agent_log_id='$agent_log_id';";
					if ($format=='debug') {echo "\n<!-- $stmt -->";}
				$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00170',$user,$server_ip,$session_name,$one_mysql_log);}
				}
			}
		if ($ACTION == 'VDADpause')
			{
			if ( (eregi("NULL",$dispo_epoch)) or ($dispo_epoch < 1000) )
				{
				$stmt="UPDATE vicidial_agent_log set wait_sec='$wait_sec' where agent_log_id='$agent_log_id';";
					if ($format=='debug') {echo "\n<!-- $stmt -->";}
				$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00171',$user,$server_ip,$session_name,$one_mysql_log);}
				}
			
			$agent_log = 'NEW_ID';
			}

		if ($wrapup == 'WRAPUP')
			{
			if ( (eregi("NULL",$dispo_epoch)) or ($dispo_epoch < 1000) )
				{
				$stmt="UPDATE vicidial_agent_log set dispo_epoch='$StarTtime', dispo_sec='0' where agent_log_id='$agent_log_id';";
				}
			else
				{
				$dispo_sec = ($StarTtime - $dispo_epoch);
				$stmt="UPDATE vicidial_agent_log set dispo_sec='$dispo_sec' where agent_log_id='$agent_log_id';";
				}

				if ($format=='debug') {echo "\n<!-- $stmt -->";}
			$rslt=mysql_query($stmt, $link);
			if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00194',$user,$server_ip,$session_name,$one_mysql_log);}
			}

		if ($agent_log == 'NEW_ID')
			{
			$user_group='';
			$stmt="SELECT user_group FROM vicidial_users where user='$user' LIMIT 1;";
			$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00182',$user,$server_ip,$session_name,$one_mysql_log);}
			if ($DB) {echo "$stmt\n";}
			$ug_record_ct = mysql_num_rows($rslt);
			if ($ug_record_ct > 0)
				{
				$row=mysql_fetch_row($rslt);
				$user_group =		trim("$row[0]");
				}

			$stmt="INSERT INTO vicidial_agent_log (user,server_ip,event_time,campaign_id,pause_epoch,pause_sec,wait_epoch,user_group) values('$user','$server_ip','$NOW_TIME','$campaign','$StarTtime','0','$StarTtime','$user_group');";
			if ($DB) {echo "$stmt\n";}
			$rslt=mysql_query($stmt, $link);
					if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00153',$user,$server_ip,$session_name,$one_mysql_log);}
			$affected_rows = mysql_affected_rows($link);
			$agent_log_id = mysql_insert_id($link);

			$stmt="UPDATE vicidial_live_agents SET agent_log_id='$agent_log_id' where user='$user';";
			if ($DB) {echo "$stmt\n";}
			$rslt=mysql_query($stmt, $link);
					if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00221',$VD_login,$server_ip,$session_name,$one_mysql_log);}
			$VLAaffected_rows_update = mysql_affected_rows($link);
			}
		}
	echo 'Agent ' . $user . ' har nu status ' . $stage . "\nNext agent_log_id:\n$agent_log_id\n";
	}


################################################################################
### UpdatEFavoritEs - update the astguiclient favorites list for this extension
################################################################################
if ($ACTION == 'UpdatEFavoritEs')
	{
	$row='';   $rowx='';
	$channel_live=1;
	if ( (strlen($favorites_list)<1) || (strlen($user)<1) || (strlen($exten)<1) )
		{
		echo "favorites list $favorites_list är ej giltig\n";
		exit;
		}
	else
		{
		$stmt = "select count(*) from phone_favorites where extension='$exten' and server_ip='$server_ip';";
		if ($DB) {echo "$stmt\n";}
		$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00172',$user,$server_ip,$session_name,$one_mysql_log);}
		$row=mysql_fetch_row($rslt);

		if ($row[0] > 0)
			{
			$stmt="UPDATE phone_favorites set extensions_list=\"$favorites_list\" where extension='$exten' and server_ip='$server_ip';";
				if ($format=='debug') {echo "\n<!-- $stmt -->";}
			$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00173',$user,$server_ip,$session_name,$one_mysql_log);}
			}
		else
			{
			$stmt="INSERT INTO phone_favorites values('$exten','$server_ip',\"$favorites_list\");";
				if ($format=='debug') {echo "\n<!-- $stmt -->";}
			$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00174',$user,$server_ip,$session_name,$one_mysql_log);}
			}
		}
		echo "Favorites list has been updated to $favorites_list for $exten\n";
	}


################################################################################
### PauseCodeSubmit - Update vicidial_agent_log with pause code
################################################################################
if ($ACTION == 'PauseCodeSubmit')
	{
	$row='';   $rowx='';
	if ( (strlen($status)<1) || (strlen($agent_log_id)<1) )
		{
		echo "agent_log_id $agent_log_id or pause_code $status är ej giltig\n";
		exit;
		}
	else
		{
		### if this is the first pause code entry in a pause session, simply update and log to queue_log
		if ($stage < 1)
			{
			$stmt="UPDATE vicidial_agent_log set sub_status=\"$status\" where agent_log_id >= '$agent_log_id' and user='$user' and ( (sub_status is NULL) or (sub_status='') )order by agent_log_id limit 2;";
				if ($format=='debug') {echo "\n<!-- $stmt -->";}
			$rslt=mysql_query($stmt, $link);
					if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00175',$user,$server_ip,$session_name,$one_mysql_log);}
			$affected_rows = mysql_affected_rows($link);
			}
		### this is not the first pause code entry, insert new vicidial_agent_log entry
		else
			{
			$pause_sec=0;
			$stmt = "select pause_epoch from vicidial_agent_log where agent_log_id='$agent_log_id';";
			if ($DB) {echo "$stmt\n";}
			$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00305',$user,$server_ip,$session_name,$one_mysql_log);}
			$VDpr_ct = mysql_num_rows($rslt);
			if ($VDpr_ct > 0)
				{
				$row=mysql_fetch_row($rslt);
				$pause_sec = ($StarTtime - $row[0]);
				}
			$stmt="UPDATE vicidial_agent_log set pause_sec='$pause_sec' where agent_log_id='$agent_log_id';";
				if ($format=='debug') {echo "\n<!-- $stmt -->";}
			$rslt=mysql_query($stmt, $link);
			if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00306',$user,$server_ip,$session_name,$one_mysql_log);}

			$user_group='';
			$stmt="SELECT user_group FROM vicidial_users where user='$user' LIMIT 1;";
			$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00309',$user,$server_ip,$session_name,$one_mysql_log);}
			if ($DB) {echo "$stmt\n";}
			$ug_record_ct = mysql_num_rows($rslt);
			if ($ug_record_ct > 0)
				{
				$row=mysql_fetch_row($rslt);
				$user_group =		trim("$row[0]");
				}

			$stmt="INSERT INTO vicidial_agent_log (user,server_ip,event_time,campaign_id,pause_epoch,pause_sec,wait_epoch,user_group,sub_status) values('$user','$server_ip','$NOW_TIME','$campaign','$StarTtime','0','$StarTtime','$user_group','$status');";
			if ($DB) {echo "$stmt\n";}
			$rslt=mysql_query($stmt, $link);
					if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00307',$user,$server_ip,$session_name,$one_mysql_log);}
			$affected_rows = mysql_affected_rows($link);
			$agent_log_id = mysql_insert_id($link);

			$stmt="UPDATE vicidial_live_agents SET agent_log_id='$agent_log_id',last_state_change='$NOW_TIME' where user='$user';";
			if ($DB) {echo "$stmt\n";}
			$rslt=mysql_query($stmt, $link);
					if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00308',$VD_login,$server_ip,$session_name,$one_mysql_log);}
			$VLAaffected_rows_update = mysql_affected_rows($link);
			}

		### if entry accepted, add a queue_log entry if QM integration is enabled
		if ($affected_rows > 0) 
			{
			#############################################
			##### START QUEUEMETRICS LOGGING LOOKUP #####
			$stmt = "SELECT enable_queuemetrics_logging,queuemetrics_server_ip,queuemetrics_dbname,queuemetrics_login,queuemetrics_pass,queuemetrics_log_id,allow_sipsak_messages FROM system_settings;";
			$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00176',$user,$server_ip,$session_name,$one_mysql_log);}
			if ($DB) {echo "$stmt\n";}
			$qm_conf_ct = mysql_num_rows($rslt);
			$i=0;
			while ($i < $qm_conf_ct)
				{
				$row=mysql_fetch_row($rslt);
				$enable_queuemetrics_logging =	$row[0];
				$queuemetrics_server_ip	=		$row[1];
				$queuemetrics_dbname =			$row[2];
				$queuemetrics_login	=			$row[3];
				$queuemetrics_pass =			$row[4];
				$queuemetrics_log_id =			$row[5];
				$allow_sipsak_messages =		$row[6];
				$i++;
				}
			##### END QUEUEMETRICS LOGGING LOOKUP #####
			###########################################
			if ( ($enable_sipsak_messages > 0) and ($allow_sipsak_messages > 0) and (eregi("SIP",$protocol)) )
				{
				$SIPSAK_prefix = 'BK-';
				passthru("/usr/local/bin/sipsak -M -O desktop -B \"$SIPSAK_prefix$status\" -r 5060 -s sip:$extension@$phone_ip > /dev/null");
				}
			if ($enable_queuemetrics_logging > 0)
				{
				$linkB=mysql_connect("$queuemetrics_server_ip", "$queuemetrics_login", "$queuemetrics_pass");
				mysql_select_db("$queuemetrics_dbname", $linkB);

				$stmt = "INSERT INTO queue_log SET partition='P01',time_id='$StarTtime',call_id='NONE',queue='NONE',agent='Agent/$user',verb='PAUSEREASON',serverid='$queuemetrics_log_id',data1='$status';";
				if ($DB) {echo "$stmt\n";}
				$rslt=mysql_query($stmt, $linkB);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$linkB,$mel,$stmt,'00177',$user,$server_ip,$session_name,$one_mysql_log);}
				$affected_rows = mysql_affected_rows($linkB);

				mysql_close($linkB);
				}
			}
		}
	echo ' Paus Code ' . $status . " has been recorded\nNext agent_log_id:\n" . $agent_log_id . "\n";
	}


################################################################################
### AGENTSview - List statuses of other agents in sidebar or xfer frame
################################################################################
if ($ACTION == 'AGENTSview')
	{
	$stmt="SELECT user_group from vicidial_users where user='$user' and pass='$pass'";
	if ($non_latin > 0) {$rslt=mysql_query("SET NAMES 'UTF8'");}
	$rslt=mysql_query($stmt, $link);
		if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00225',$user,$server_ip,$session_name,$one_mysql_log);}
	$row=mysql_fetch_row($rslt);
	$VU_user_group =	$row[0];

	$agent_status_viewable_groupsSQL='';
	### Gather timeclock and shift enforcement restriction settings
	$stmt="SELECT agent_status_viewable_groups,agent_status_view_time from vicidial_user_groups where user_group='$VU_user_group';";
	$rslt=mysql_query($stmt, $link);
		if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00226',$VD_login,$server_ip,$session_name,$one_mysql_log);}
	$row=mysql_fetch_row($rslt);
	$agent_status_viewable_groups = $row[0];
	$agent_status_viewable_groupsSQL = eregi_replace('  ','',$agent_status_viewable_groups);
	$agent_status_viewable_groupsSQL = eregi_replace(' ',"','",$agent_status_viewable_groupsSQL);
	$agent_status_viewable_groupsSQL = "user_group IN('$agent_status_viewable_groupsSQL')";
	$agent_status_view = 0;
	if (strlen($agent_status_viewable_groups) > 2)
		{$agent_status_view = 1;}
	$agent_status_view_time=0;
	if ($row[1] == 'Y')
		{$agent_status_view_time=1;}
	$andSQL='';
	if (ereg("ALL-GROUPS",$agent_status_viewable_groups))
		{$AGENTviewSQL = "";}
	else
		{
		$AGENTviewSQL = "($agent_status_viewable_groupsSQL)";

		if (ereg("CAMPAIGN-AGENTS",$agent_status_viewable_groups))
			{$AGENTviewSQL = "($AGENTviewSQL or (campaign_id='$campaign'))";}
		$AGENTviewSQL = "and $AGENTviewSQL";
		}
	if ($comments=='AgentXferViewSelect') 
		{$AGENTviewSQL .= " and (vla.closer_campaigns LIKE \"%AGENTDIRECT%\")";}


	echo "<TABLE CELLPADDING=0 CELLSPACING=1>";
	### Gather agents data and statuses
	$stmt="SELECT vla.user,vla.status,vu.full_name,UNIX_TIMESTAMP(last_call_time),UNIX_TIMESTAMP(last_call_finish) from vicidial_live_agents vla,vicidial_users vu where vla.user=vu.user $AGENTviewSQL order by vu.full_name;";
	$rslt=mysql_query($stmt, $link);
		if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00227',$VD_login,$server_ip,$session_name,$one_mysql_log);}
	if ($rslt) {$agents_count = mysql_num_rows($rslt);}
	$loop_count=0;
	while ($agents_count > $loop_count)
		{
		$row=mysql_fetch_row($rslt);
		$user =			$row[0];
		$status =		$row[1];
		$full_name =	$row[2];
		$call_start =	$row[3];
		$call_finish =	$row[4];

		if ( ($status=='READY') or ($status=='CLOSER') ) 
			{
			$statuscolor='#ADD8E6';
			$call_time = ($StarTtime - $call_finish);
			}
		if ( ($status=='QUEUE') or ($status=='INCALL') ) 
			{
			$statuscolor='#D8BFD8';
			$call_time = ($StarTtime - $call_start);
			}
		if ($status=='PAUSED') 
			{
			$statuscolor='#F0E68C';
			$call_time = ($StarTtime - $call_finish);
			}

		if ($call_time < 1)
			{
			$call_time = "0:00";
			}
		else
			{
			$Fminutes_M = ($call_time / 60);
			$Fminutes_M_int = floor($Fminutes_M);
			$Fminutes_M_int = intval("$Fminutes_M_int");
			$Fminutes_S = ($Fminutes_M - $Fminutes_M_int);
			$Fminutes_S = ($Fminutes_S * 60);
			$Fminutes_S = round($Fminutes_S, 0);
			if ($Fminutes_S < 10) {$Fminutes_S = "0$Fminutes_S";}
			$call_time = "$Fminutes_M_int:$Fminutes_S";
			}

		if ($comments=='AgentXferViewSelect') 
			{
			echo "<TR BGCOLOR=\"$statuscolor\"><TD><font style=\"font-size: 12px; font-family: sans-serif;\"> &nbsp; <a href=\"#\" onclick=\"AgentsXferSelect('$row[0]','$comments');return false;\">$row[0] - $row[2]</a>&nbsp;</font></TD>";
			if ($agent_status_view_time > 0)
				{echo "<TD><font style=\"font-size: 12px;  font-family: sans-serif;\">&nbsp; $call_time &nbsp;</font></TD>";}
			echo "</TR>";
			}
		else
			{
			echo "<TR BGCOLOR=\"$statuscolor\"><TD><font style=\"font-size: 12px;  font-family: sans-serif;\"> &nbsp; ";
			echo "$row[0] - $row[2]";
			echo "&nbsp;</font></TD>";
			if ($agent_status_view_time > 0)
				{echo "<TD><font style=\"font-size: 12px;  font-family: sans-serif;\">&nbsp; $call_time &nbsp;</font></TD>";}
			echo "</TR>";
			}
		$loop_count++;
		}
	echo "</TABLE><BR>\n";
	echo "<font style=\"font-size:10px;font-family:sans-serif;\"><font style=\"background-color:#ADD8E6;\"> &nbsp; &nbsp;</font>-READY &nbsp; <font style=\"background-color:#D8BFD8;\">&nbsp; &nbsp;</font>-INCALL &nbsp; <font style=\"background-color:#F0E68C;\"> &nbsp; &nbsp;</font>-PAUSED &nbsp;\n";

	echo "</font>\n";
	}


################################################################################
### CALLSINQUEUEview - List calls in queue for the bottombar 228
################################################################################
if ($ACTION == 'CALLSINQUEUEview')
	{
	$stmt="SELECT view_calls_in_queue,grab_calls_in_queue from vicidial_campaigns where campaign_id='$campaign'";
	if ($non_latin > 0) {$rslt=mysql_query("SET NAMES 'UTF8'");}
	$rslt=mysql_query($stmt, $link);
		if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00228',$user,$server_ip,$session_name,$one_mysql_log);}
	$row=mysql_fetch_row($rslt);
	$view_calls_in_queue =	$row[0];
	$grab_calls_in_queue =	$row[1];

	if (eregi('NONE',$view_calls_in_queue))
		{
		echo "Samtalskö är frånkopplad för denna kampanj\n";
		exit;
		}
	else
		{
		$view_calls_in_queue = ereg_replace('ALL','99', $view_calls_in_queue);
	
		### grab the status and campaign/in-group information for this agent to display
		$ADsql='';
		$stmt="SELECT status,campaign_id,closer_campaigns from vicidial_live_agents where user='$user' and server_ip='$server_ip';";
		if ($DB) {echo "|$stmt|\n";}
		$rslt=mysql_query($stmt, $link);
			if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00229',$user,$server_ip,$session_name,$one_mysql_log);}
		$row=mysql_fetch_row($rslt);
		$Alogin=$row[0];
		$Acampaign=$row[1];
		$AccampSQL=$row[2];
		$AccampSQL = ereg_replace(' -','', $AccampSQL);
		$AccampSQL = ereg_replace(' ',"','", $AccampSQL);
		if (eregi('AGENTDIRECT', $AccampSQL))
			{
			$AccampSQL = ereg_replace('AGENTDIRECT','', $AccampSQL);
			$ADsql = "or ( (campaign_id LIKE \"%AGENTDIRECT%\") and (agent_only='$user') )";
			}

		### grab the basic data på calls in the queue for this agent
		$stmt="SELECT lead_id,campaign_id,phone_number,uniqueid,UNIX_TIMESTAMP(call_time),call_type,auto_call_id from vicidial_auto_calls where status IN('LIVE') and ( (campaign_id='$Acampaign') or (campaign_id IN('$AccampSQL')) $ADsql) order by queue_priority,call_time;";
		if ($DB) {echo "|$stmt|\n";}
		$rslt=mysql_query($stmt, $link);
			if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00230',$user,$server_ip,$session_name,$one_mysql_log);}
		if ($rslt) {$calls_count = mysql_num_rows($rslt);}
		$loop_count=0;
		while ($calls_count > $loop_count)
			{
			$row=mysql_fetch_row($rslt);
			$CQlead_id[$loop_count] =		$row[0];
			$CQcampaign_id[$loop_count] =	$row[1];
			$CQphone_number[$loop_count] =	$row[2];
			$CQuniqueid[$loop_count] =		$row[3];
			$CQcall_time[$loop_count] =		$row[4];
			$CQcall_type[$loop_count] =		$row[5];
			$CQauto_call_id[$loop_count] =	$row[6];
			$loop_count++;
			}

		### re-order the calls to always make sure the AGENTDIRECT calls are first
		$loop_count=0;
		$o=0;
		while ($calls_count > $loop_count)
			{
			if (eregi('AGENTDIRECT', $CQcampaign_id[$loop_count]))
				{
				$OQlead_id[$o] =		$CQlead_id[$loop_count];
				$OQcampaign_id[$o] =	$CQcampaign_id[$loop_count];
				$OQphone_number[$o] =	$CQphone_number[$loop_count];
				$OQuniqueid[$o] =		$CQuniqueid[$loop_count];
				$OQcall_time[$o] =		$CQcall_time[$loop_count];
				$OQcall_type[$o] =		$CQcall_type[$loop_count];
				$OQauto_call_id[$o] =	$CQauto_call_id[$loop_count];
				$o++;
				}
			$loop_count++;
			}
		$loop_count=0;
		while ($calls_count > $loop_count)
			{
			if (!eregi('AGENTDIRECT', $CQcampaign_id[$loop_count]))
				{
				$OQlead_id[$o] =		$CQlead_id[$loop_count];
				$OQcampaign_id[$o] =	$CQcampaign_id[$loop_count];
				$OQphone_number[$o] =	$CQphone_number[$loop_count];
				$OQuniqueid[$o] =		$CQuniqueid[$loop_count];
				$OQcall_time[$o] =		$CQcall_time[$loop_count];
				$OQcall_type[$o] =		$CQcall_type[$loop_count];
				$OQauto_call_id[$o] =	$CQauto_call_id[$loop_count];
				$o++;
				}
			$loop_count++;
			}

		echo "<TABLE CELLPADDING=0 CELLSPACING=1 Border=0 WIDTH=$stage>";
		echo "<TR>";
		echo "<TD> &nbsp; </TD>";
		echo "<TD BGCOLOR=\"#CCCCCC\"><font style=\"font-size:11px;font-family:sans-serif;\"><B> &nbsp; PHONE &nbsp; </font></TD>";
		echo "<TD BGCOLOR=\"#CCCCCC\"><font style=\"font-size:11px;font-family:sans-serif;\"><B> &nbsp; NAME &nbsp; </font></TD>";
		echo "<TD BGCOLOR=\"#CCCCCC\"><font style=\"font-size:11px;font-family:sans-serif;\"><B> &nbsp; WAIT &nbsp; </font></TD>";
		echo "<TD BGCOLOR=\"#CCCCCC\"><font style=\"font-size:11px;font-family:sans-serif;\"><B> &nbsp; AGENT &nbsp; </font></TD>";
		echo "<TD BGCOLOR=\"#CCCCCC\"><font style=\"font-size:11px;font-family:sans-serif;\"> &nbsp; &nbsp; &nbsp; </font></TD>";
		echo "<TD BGCOLOR=\"#CCCCCC\"><font style=\"font-size:11px;font-family:sans-serif;\"><B> &nbsp; SAMTAL GROUP &nbsp; </font></TD>";
		echo "<TD BGCOLOR=\"#CCCCCC\"><font style=\"font-size:11px;font-family:sans-serif;\"><B> &nbsp; TYPE &nbsp; </font></TD>";
		echo "</TR>";

		### Print call information and gather more info på the calls as they are printed
		$loop_count=0;
		while ( ($calls_count > $loop_count) and ($view_calls_in_queue > $loop_count) )
			{
			$call_time = ($StarTtime - $OQcall_time[$loop_count]);
			$Fminutes_M = ($call_time / 60);
			$Fminutes_M_int = floor($Fminutes_M);
			$Fminutes_M_int = intval("$Fminutes_M_int");
			$Fminutes_S = ($Fminutes_M - $Fminutes_M_int);
			$Fminutes_S = ($Fminutes_S * 60);
			$Fminutes_S = round($Fminutes_S, 0);
			if ($Fminutes_S < 10) {$Fminutes_S = "0$Fminutes_S";}
			$call_time = "$Fminutes_M_int:$Fminutes_S";
			$call_handle_method='';

			if ($OQcall_type[$loop_count]=='IN')
				{
				$stmt="SELECT group_name,group_color from vicidial_inbound_groups where group_id='$OQcampaign_id[$loop_count]';";
				$rslt=mysql_query($stmt, $link);
					if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00231',$user,$server_ip,$session_name,$one_mysql_log);}
				$row=mysql_fetch_row($rslt);
				$group_name =			$row[0];
				$group_color =			$row[1];
				}
			$stmt="SELECT comments,user,first_name,last_name from vicidial_list where lead_id='$OQlead_id[$loop_count]'";
			$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00232',$user,$server_ip,$session_name,$one_mysql_log);}
			$row=mysql_fetch_row($rslt);
			$comments =		$row[0];
			$agent =		$row[1];
			$first_last_name =	"$row[2] $row[3]";
			$caller_name =	$first_last_name;

			$stmt="SELECT full_name from vicidial_users where user='$agent'";
			$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00232',$user,$server_ip,$session_name,$one_mysql_log);}
			if ($rslt) {$agent_name_count = mysql_num_rows($rslt);}
			if ($agent_name_count > 0)
				{
				$row=mysql_fetch_row($rslt);
				$agent_name =		$row[0];
				}
			else
				{$agent_name='';}

			if (strlen($caller_name)<2)
				{$caller_name =	$comments;}
			if (strlen($caller_name) > 30) {$caller_name = substr("$caller_name", 0, 30);}

			if (eregi("0$|2$|4$|6$|8$", $loop_count)) {$Qcolor='bgcolor="#FCFCFC"';} 
			else{$Qcolor='bgcolor="#ECECEC"';}

			if ( (eregi('Y',$grab_calls_in_queue)) and ($OQcall_type[$loop_count]=='IN') )
				{
				echo "<TR $Qcolor>";
				echo "<TD> <a href=\"#\" onclick=\"callinqueuegrab('$OQauto_call_id[$loop_count]');return false;\"><font style=\"font-size: 11px; font-family: sans-serif;\">TAKE SAMTAL</a> &nbsp; </TD>";
				echo "<TD><font style=\"font-size: 11px; font-family: sans-serif;\"> &nbsp; $OQphone_number[$loop_count] &nbsp; </font></TD>";
				echo "<TD><font style=\"font-size: 11px; font-family: sans-serif;\"> &nbsp; $caller_name &nbsp; </font></TD>";
				echo "<TD><font style=\"font-size: 11px; font-family: sans-serif;\"> &nbsp; $call_time &nbsp; </font></TD>";
				echo "<TD><font style=\"font-size: 11px; font-family: sans-serif;\"> &nbsp; $agent - $agent_name &nbsp; </font></TD>";
				echo "<TD bgcolor=\"$group_color\"><font style=\"font-size: 11px; font-family: sans-serif;\"> &nbsp; &nbsp; &nbsp; </font></TD>";
				echo "<TD><font style=\"font-size: 11px; font-family: sans-serif;\"> &nbsp; $OQcampaign_id[$loop_count] - $group_name &nbsp; </font></TD>";
				echo "<TD><font style=\"font-size: 11px; font-family: sans-serif;\"> &nbsp; $OQcall_type[$loop_count] &nbsp; </font></TD>";
				echo "</TR>";
				}
			else
				{
				echo "<TR $Qcolor>";
				echo "<TD> &nbsp; </TD>";
				echo "<TD><font style=\"font-size: 11px; font-family: sans-serif;\"> &nbsp; $OQphone_number[$loop_count] &nbsp; </font></TD>";
				echo "<TD><font style=\"font-size: 11px; font-family: sans-serif;\"> &nbsp; $caller_name &nbsp; </font></TD>";
				echo "<TD><font style=\"font-size: 11px; font-family: sans-serif;\"> &nbsp; $call_time &nbsp; </font></TD>";
				echo "<TD><font style=\"font-size: 11px; font-family: sans-serif;\"> &nbsp; $agent - $agent_name &nbsp; </font></TD>";
				echo "<TD bgcolor=\"$group_color\"><font style=\"font-size: 11px; font-family: sans-serif;\"> &nbsp; &nbsp; &nbsp; </font></TD>";
				echo "<TD><font style=\"font-size: 11px; font-family: sans-serif;\"> &nbsp; $OQcampaign_id[$loop_count] - $group_name &nbsp; </font></TD>";
				echo "<TD><font style=\"font-size: 11px; font-family: sans-serif;\"> &nbsp; $OQcall_type[$loop_count] &nbsp; </font></TD>";
				echo "</TR>";
				}
			$loop_count++;
			}
		echo "</TABLE><BR> &nbsp;\n";
		}
	}


################################################################################
### CALLSINQUEUEgrab - grab a call in queue and reserve it for that agent
################################################################################
if ($ACTION == 'CALLSINQUEUEgrab')
	{
	$stmt="SELECT view_calls_in_queue,grab_calls_in_queue from vicidial_campaigns where campaign_id='$campaign'";
	if ($non_latin > 0) {$rslt=mysql_query("SET NAMES 'UTF8'");}
	$rslt=mysql_query($stmt, $link);
		if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00233',$user,$server_ip,$session_name,$one_mysql_log);}
	$row=mysql_fetch_row($rslt);
	$view_calls_in_queue =	$row[0];
	$grab_calls_in_queue =	$row[1];

	if ( (eregi('NONE',$view_calls_in_queue)) or (eregi('N',$grab_calls_in_queue)) )
		{
		echo "ERROR: Samtalskö är frånkopplad för denna kampanj\n";
		exit;
		}
	else
		{
		$stmt="UPDATE vicidial_auto_calls set agent_grab=\"$user\" where auto_call_id='$stage' and agent_grab='' and status='LIVE';";
			if ($format=='debug') {echo "\n<!-- $stmt -->";}
		$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00234',$user,$server_ip,$session_name,$one_mysql_log);}
		$affected_rows = mysql_affected_rows($link);
		if ($affected_rows > 0) 
			{
			$stmt="SELECT call_time,campaign_id,uniqueid,phone_number,lead_id,queue_priority,call_type from vicidial_auto_calls where auto_call_id='$stage';";
			$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00270',$user,$server_ip,$session_name,$one_mysql_log);}
			if ($rslt) {$vac_count = mysql_num_rows($rslt);}
			if ($vac_count > 0)
				{
				$row=mysql_fetch_row($rslt);
				$GCcall_time =		$row[0];
				$GCcampaign_id =	$row[1];
				$GCuniqueid =		$row[2];
				$GCphone_number =	$row[3];
				$GClead_id =		$row[4];
				$GCqueue_priority = $row[5];
				$GCcall_type =		$row[6];

				$stmt="INSERT INTO vicidial_grab_call_log SET auto_call_id='$stage',user='$user',event_date='$NOW_TIME',call_time='$GCcall_time',campaign_id='$GCcampaign_id',uniqueid='$GCuniqueid',phone_number='$GCphone_number',lead_id='$GClead_id',queue_priority='$GCqueue_priority',call_type='$GCcall_type';";
					if ($format=='debug') {echo "\n<!-- $stmt -->";}
				$rslt=mysql_query($stmt, $link);
						if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00271',$user,$server_ip,$session_name,$one_mysql_log);}
				$affected_rows = mysql_affected_rows($link);
				}

			echo "SUCCESS: Call $stage grabbed for $user";
			exit;
			}
		else
			{
			echo "ERROR: Call $stage could not be grabbed for $user\n";
			exit;
			}
		}
	}


################################################################################
### CalLBacKLisT - List the USERONLY callbacks for an agent
################################################################################
if ($ACTION == 'CalLBacKLisT')
	{
	if ($agentonly_callback_campaign_lock > 0)
		{$campaignCBsql = "and campaign_id='$campaign'";}
	else
		{$campaignCBsql = '';}
	$stmt = "select callback_id,lead_id,campaign_id,status,entry_time,callback_time,comments from vicidial_callbacks where recipient='USERONLY' and user='$user' $campaignCBsql and status NOT IN('INACTIVE','DEAD') order by callback_time;";
	if ($DB) {echo "$stmt\n";}
	$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00178',$user,$server_ip,$session_name,$one_mysql_log);}
	if ($rslt) {$callbacks_count = mysql_num_rows($rslt);}
	echo "$callbacks_count\n";
	$loop_count=0;
	while ($callbacks_count>$loop_count)
		{
		$row=mysql_fetch_row($rslt);
		$callback_id[$loop_count]	= $row[0];
		$lead_id[$loop_count]		= $row[1];
		$campaign_id[$loop_count]	= $row[2];
		$status[$loop_count]		= $row[3];
		$entry_time[$loop_count]	= $row[4];
		$callback_time[$loop_count]	= $row[5];
		$comments[$loop_count]		= $row[6];
		$loop_count++;
		}
	$loop_count=0;
	while ($callbacks_count>$loop_count)
		{
		$stmt = "select first_name,last_name,phone_number from vicidial_list where lead_id='$lead_id[$loop_count]';";
		if ($DB) {echo "$stmt\n";}
		$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00179',$user,$server_ip,$session_name,$one_mysql_log);}
		$row=mysql_fetch_row($rslt);

		echo "$row[0] ~$row[1] ~$row[2] ~$callback_id[$loop_count] ~$lead_id[$loop_count] ~$campaign_id[$loop_count] ~$status[$loop_count] ~$entry_time[$loop_count] ~$callback_time[$loop_count] ~$comments[$loop_count]\n";
		$loop_count++;
		}

	}


################################################################################
### CalLBacKCounT - send the count of the USERONLY callbacks for an agent
################################################################################
if ($ACTION == 'CalLBacKCounT')
	{
	if ($agentonly_callback_campaign_lock > 0)
		{$campaignCBsql = "and campaign_id='$campaign'";}
	else
		{$campaignCBsql = '';}
	$stmt = "select count(*) from vicidial_callbacks where recipient='USERONLY' and user='$user' $campaignCBsql and status NOT IN('INACTIVE','DEAD');";
	if ($DB) {echo "$stmt\n";}
	$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00180',$user,$server_ip,$session_name,$one_mysql_log);}
	$row=mysql_fetch_row($rslt);
	$cbcount=$row[0];

	echo "$cbcount";
	}




################################################################################
### DiaLableLeaDsCounT - send the count of the dialable leads in this campaign
################################################################################
if ($ACTION == 'DiaLableLeaDsCounT')
	{
	$stmt = "select dialable_leads from vicidial_campaign_stats where campaign_id='$campaign';";
	if ($DB) {echo "$stmt\n";}
	$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00252',$user,$server_ip,$session_name,$one_mysql_log);}
	if ($rslt) {$dialable_count = mysql_num_rows($rslt);}
	if ($dialable_count > 0)
		{
		$row=mysql_fetch_row($rslt);
		$DLcount	= $row[0];
		}

	echo "$DLcount";
	}




if ($format=='debug') 
{
$ENDtime = date("U");
$RUNtime = ($ENDtime - $StarTtime);
echo "\n<!-- scriptet tog: $RUNtime sekunder -->";
echo "\n</body>\n</html>\n";
}
	
exit; 


##### Hangup Cause Description Map  #####
function hangup_cause_description($code)
	{
	global $hangup_cause_dictionary;
	if ( array_key_exists($code,$hangup_cause_dictionary)  ) { return $hangup_cause_dictionary[$code]; }
	else { return "Unidentified Hangup Cause Code."; }
	}


##### MySQL Error Logging #####
function mysql_error_logging($NOW_TIME,$link,$mel,$stmt,$query_id,$user,$server_ip,$session_name,$one_mysql_log)
{
$NOW_TIME = date("Y-m-d H:i:s");
#	mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00001',$user,$server_ip,$session_name,$one_mysql_log);
$errno='';   $error='';
if ( ($mel > 0) or ($one_mysql_log > 0) )
	{
	$errno = mysql_errno($link);
	if ( ($errno > 0) or ($mel > 1) or ($one_mysql_log > 0) )
		{
		$error = mysql_error($link);
		$efp = fopen ("./vicidial_mysql_errors.txt", "a");
		fwrite ($efp, "$NOW_TIME|vdc_db_query|$query_id|$errno|$error|$stmt|$user|$server_ip|$session_name|\n");
		fclose($efp);
		}
	}
$one_mysql_log=0;
return $errno;
}

?>
