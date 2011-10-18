<?php
# vicidial.php - the web-based version of the astVICIDIAL client application
# 
# Copyright (C) 2011  Matt Florell <vicidial@gmail.com>    LICENSE: AGPLv2
#
# Other scripts that this application depends on:
# - vdc_db_query.php: Updates information in the database
# - manager_send.php: Sends manager actions to the DB for execution
# - conf_exten_check.php: time sync and status updater, calls in queue
# - vdc_script_display.php: displays script with variables
# - vdc_form_display.php: display custom fields form
#
# CHANGELOG
# 50607-1426 - First Build of VICIDIAL web client basic login process finished
# 50628-1620 - Added some basic formatting and worked on process flow
# 50628-1715 - Startup variables mapped to javascript variables
# 50629-1303 - Added Login Closer in-groups selection box and vla update
# 50629-1530 - Rough layout for customer info form section and button links
# 50630-1453 - Rough Manual Dial/Hangup with customer info displayed
# 50701-1450 - Added vicidial_log entries on dial and hangup
# 50701-1634 - Added Logout function
# 50705-1259 - Added call disposition functionality
# 50705-1432 - Added lead info DB update function
# 50705-1658 - Added web form functionality
# 50706-1043 - Added call park and pickup functions
# 50706-1234 - Added Start/Stop Recording functionality
# 50706-1614 - Added conference channels display option
# 50711-1333 - Removed call check redundancy and fixed a span bug
# 50727-1424 - Added customer channel and participant present sensing/alerts
# 50804-1057 - Added SendDTMF function and reconfigured the transfer span
# 50804-1224 - Added Local and Internal Closer transfer functions
# 50804-1628 - Added Blind transfer, activated LIVE CALL image and fixed bugs
# 50804-1808 - Added button images for left buttons
# 50815-1151 - Added 3Way calling functions to Transfer-conf frame
# 50815-1602 - Added images and buttons for xfer functions
# 50816-1813 - Added basic autodial outbound call pickup functions
# 50817-1113 - Fixes to auto_dialing call receipt
# 50817-1234 - Added inbound call receipt capability
# 50817-1541 - Added customer time display
# 50818-1327 - Added stop-all-recordings-after-each-vicidial-call option
# 50818-1703 - Added pretty login section
# 50825-1200 - Modified form field lengths, added double-click dispositions
# 50831-1603 - Fixed customer time bug and fronter display bug for CLOSER
# 50901-1314 - Fixed CLOSER IN-GROUP Web Form bug
# 50903-0904 - Added preview-lead code for manual dialing
# 50904-0016 - Added ability to hangup manual dials before pickup
# 50906-1319 - Added override for filters on xfer calls, fixed login display bug
# 50909-1243 - Added hotkeys functionality for quick dispoing in auto-dial mode
# 50912-0958 - Modified hotkeys function, agent must have user_level >= 5 to use
# 50913-1212 - Added campaign_cid to 3rd party calls
# 50923-1546 - Modified to work with language translation
# 50926-1656 - Added campaign pull-down at login of active campaigns
# 50928-1633 - Added manual dial alternate number dial option
# 50930-1538 - Added session_id empty login failure and fixed 2 minor bugs
# 51004-1656 - Fixed recording filename bug and new Spanish translation
# 51020-1103 - Added campaign-specific recording control abilities
# 51020-1352 - Added Basic vicidial_agent_log framework
# 51021-1050 - Fixed custtime display and disable Enter/Return keypresses
# 51021-1718 - Allows for multi-line comments (changes \n to !N in database)
# 51110-1432 - Fixed non-standard http port issue
# 51111-1047 - Added vicidial_agent_log lead_id earlier for manual dial
# 51118-1305 - Activate multi-line comments from $multi_line_comments var
# 51118-1313 - Move Transfer DIV to a floating span to preserve 800x600 view
# 51121-1506 - Small PHP optimizations in many scripts and disabled globalize
# 51129-1010 - Added ability to accept calls from other VICIDIAL servers
# 51129-1254 - Fixed Hangups of other agents channels when customer hangs up
# 51208-1732 - Created user-first login that looks for default phone info
# 51219-1526 - Added variable framework for campaign and in-group scripts
# 51221-1200 - Added SCRIPT tab, layout and functionality
# 51221-1714 - Added auto-switch-to-SCRIPT-tab and auto-webform-popup
# 51222-1605 - Added VMail message blind transfer button to xfer-conf frame
# 51229-1028 - Added checks on web_form_address to allow for var in the DB value
# 60117-1312 - Added Transfer-conf frame toggle on button press
# 60208-1152 - Added DTMF-xfernumber preset links to xfer-conf frame
# 60213-1129 - Added vicidial_users.hotkeys_active  for any user hotkeys
# 60213-1210 - Added ability to sort routing of calls by user_level
# 60214-0932 - Initial Callback calendar display framework
# 60214-1407 - Added ability to minimize the dispo screen to see info below
# 60215-1104 - Added ANYONE scheduled callbacks functionality
# 60410-1116 - Added persistant pause after dispo option and change dispo text
#            - Added web form submit that opens new window with dispo on submit
#            - Added PREVIOUS CALLBACK in customer info to flag callbacks
#            - Added link to try to hangup the call again in the dispo screen
#            - Added link noone-in-session screen to call agent phone again
#            - Added link customer-hungup screen to go straight to dispo screen
# 60410-1532 - Added agent status and campaign calls dialing display option
# 60411-1547 - Add ability to set callback as USERONLY and some basic formatting
# 60413-1752 - Add basic USERONLY callback frame and listings
# 60414-1039 - Changed manual dial preview and alt dial checkboxes to spans
#            - Added beta-level USERONLY callback functionality
#            - Added beta-level manual dialing with lead insertion functionality
# 60415-1534 - Fixed manual dial lead preview and fixed manuald dial override bug
# 60417-1108 - Added capability to do alt-number-dialing in auto-dial mode
#            - Changed several permissions to database-defined
# 60419-1529 - Prevent manual dial or callbacks when alt-dial lead not finished
# 60420-1647 - Fixed DiaLDiaLAltPhonE error, Call Agent Again DialControl error
# 60421-1229 - Check GET/POST vars lines with isset to not trigger PHP NOTICES
# 60424-1005 - Fixed Alt phone disabled bug for callbacks and manual dials
# 60426-1058 - Added vicidial_user setting for default blended check for CLOSER
# 60501-1008 - Added option to manual dial screen to manually lookup phone number
# 60503-1653 - Fixed agentonly_callback not-defined bug in scheduled callbacks screen
# 60504-1032 - Fixed manual dial display bug and transfer dispo alert bug
#            - Fixed recording filename display to not overrun 25 characters
# 60510-1051 - Added Wrapup timer and wrapup message on wrapup screen after dispo
# 60608-1453 - Added CLOSER campaign allowable in-groups limitations
# 60609-1123 - Added add-number-to-DNC-list function and manual dial check DNC
# 60619-1047 - Added variable filters to close security holes for login form
# 60804-1710 - fixed scheduled CALLBK for other languages build
# 60808-1145 - Added consultative transfers with customer data
# 60808-2232 - Added campaign name to pulldown for login screen
# 60809-1603 - Added option to locally transfer consult xfers
# 60809-1732 - Added recheck of transferred channels before customer gone mesg
# 60810-1011 - Fixed CXFER leave 3way call bugs
# 60816-1602 - Added ALLCALLS recording delay option allcalls_delay
# 60816-1716 - Fixed customer time display bug and client DST setting
# 60821-1555 - Added option to omit phone_code on dialout of leads
# 60821-1628 - Added ALLFORCE recording option
# 60821-1643 - Added no_delete_sessions option to not delete sessions
# 60822-0512 - Changed phone number fields to be maxlength of 12
# 60829-1531 - Made compatible with WeBRooTWritablE setting in dbconnect.php
# 60906-1152 - Added Previous CallBack info display span
# 60906-1715 - Allow for Local phone extension conferences
# 61004-1729 - Add ability to control volume per channel in "calls in this session"
# 61122-1341 - Added vicidial_user_groups allowed_campaigns restrictions
# 61122-1523 - Added more SCRIPT variables
# 61128-2229 - Added vicidial_live_agents and vicidial_auto_calls manual dial entries
# 61130-1617 - Added lead_id to MonitorConf for recording_log
# 61221-1212 - Changed width to 760 to better fit 800x600 screens, widened SCRIPT
# 70109-1128 - Fixed wrapup timer bug
# 70109-1635 - Added option for HotKeys automatically dialing next number in manual mode
#            - Added option for alternate number dialing with hotkeys
# 70111-1600 - Added ability to use BLEND/INBND/*_C/*_B/*_I as closer campaigns
# 70118-1517 - Added vicidial_agent_log and vicidial_user_log logging of user_group
# 70201-1249 - Added FAST DIAL option for manually dialing, added UTF8 compatible code
# 70201-1703 - Fixed cursor bug for most text input fields
# 70202-1453 - Added first portions of Agent Pause Codes
# 70203-0108 - Finished Agent Pause Codes functionality
# 70203-0930 - Added dialed_number to webform output
# 70203-1010 - Added dialed_label to webform output
# 70206-1201 - Fixed allow_closers bug
# 70206-1332 - Added vicidial_recording_override users setting function
# 70212-1252 - Fixed small issue with CXFER
# 70213-1018 - Changed CXFER and AXFER to update customer information before transfer
# 70214-1233 - Added queuemetrics_log_id field for server_id in queue_log
# 70215-1240 - Added queuemetrics_log_id field for server_id in queue_log
# 70222-1617 - Changed queue_log PAUSE/UNPAUSE to PAUSEALL/UNPAUSEALL
# 70226-1252 - Added Mute/UnMute to agent screen
# 70309-1035 - Allow amphersands and questions marks in comments to pass through
# 70313-1052 - Allow pound signs(hash) in comments to pass through
# 70316-1406 - Moved the MUTE button to be accessible during a transfer/conf
# 70319-1446 - Added agent-deactive-display and disable customer info update functions
# 70319-1626 - Added option to allow agent logins to campaigns with no leads in the hopper
# 70320-1501 - Added option to allow retry of leave-3way-call from dispo screen
# 70322-1545 - Added sipsak display ability
# 70510-1319 - Added onUnload force Logout
# 70806-1530 - Added Presets Dial links above agent mute button
# 70823-2118 - Fixed XMLHTTPRequest, HotKeys and Scheduled Callbacks issues with MSIE
# 70828-1443 - Added source_id to output of SCRIPTtab-IFRAME and WEBFORM
# 71022-1427 - Added formatting of the customer phone number in the main status bar
# 71029-1848 - Changed CLOSER-type campaign to not use campaign_id restrictions
# 71101-1204 - Fixed bug in callback calendar with DST
# 71116-0957 - Added campaign_weight and calls_today to the vla table insertion
# 71120-1719 - Added XMLHTPRequest lookup of allowable campaigns for agents during login
# 71122-0256 - Added auto-pause notification
# 71125-1751 - Changed Transfer section to allow for selection of in-groups to send calls to
# 71127-0408 - Added height and width settings for easier modification of screen size
# 71129-2025 - restricted callbacks count and list to campaign only
# 71223-0318 - changed logging of closer calls
# 71226-1117 - added option to kick all calls from conference upon logout
# 80109-1510 - added gender select list
# 80116-1032 - added option on CLOSER-type campaigns to change in-groups when paused
# 80317-2106 - added recording override options for inbound group calls
# 80331-1433 - Added second transfer try for VICIDIAL transfers/hangups on manual dial calls
# 80402-0121 - Fixes for manual dial transfers on some systems
# 80407-2112 - Work on adding phone login load balancing across servers
# 80416-0559 - Added ability to log computer_ip at login, set the $PhonESComPIP variable
# 80428-0413 - UTF8 changes and testing
# 80505-0054 - Added multi-phones load-balanced alias option
# 80507-0932 - Fixed Script display bug (+ instead of space)
# 80519-1425 - Added calls in queue display
# 80523-1630 - Added Timeclock links
# 80625-0047 - Added U option for gender, added date/phone display options
# 80630-2210 - Added queue_log entries for Manual Dial
# 80703-0139 - Added alter customer phone permissions
# 80703-1106 - Added API functionality for Hangup and Dispo, added Agent Display Queue Count
# 80707-2325 - Added vicidial_id to recording_log for tracking of vicidial or closer log to recording
# 80709-0358 - Added Default alt phone dial hard-code option
# 80719-1147 - Changed recording and senddtmf conf prefix
# 80815-1014 - Added manual dial list restriction option
# 80823-2123 - Fixed form scroll for IE, added copy to clipboard(IE-only feature)
# 80831-0548 - Added Extended alt-dial-phone display information for non-manual calls
# 80909-1717 - Added support for campaign-specific DNC lists
# 80915-1754 - Rewrote leave-3way functions for external calling
# 81002-1908 - Fixed double-login bug in some conditions
# 81007-0945 - Added three_way_call_cid option for outbound 3way calls
# 81010-1047 - Fixed conf calling prefix to use settings, other 3way improvements
# 81011-1403 - Fixed bugs in leave3way when transferring a manual dial call
# 81012-1729 - Added INBOUND_MAN dial method to allow manual list dialing and inbound calls
# 81013-1644 - Fixed bug in leave 3way for manual dial fronters
# 81015-0405 - Fixed bug related to hangups on 3way calls
# 81016-0703 - Changed leave 3way to allow function at any time transfer-conf is available
# 81020-1501 - Fixed bugs in queue_log logging
# 81023-0411 - Added compatibility for dial-in agents using AGI, bug fixes
# 81030-0403 - Added option to force Pause Codes on PAUSE
# 81103-1427 - Added 3way call dial prefix
# 81104-0140 - Added mysql error logging capability
# 81104-1618 - Changed MySQL queries logging
# 81106-0411 - Changedthe campaign login list behaviour
# 81110-0057 - Changed Pause time to start new vicidial_agent_log on every pause
# 81110-1514 - Added hangup_all_non_reserved to fix non-Hangup bug
# 81119-1811 - webform backslash fix
# 81124-2213 - Fixes blind transfer bug
# 81209-1617 - Added campaign web form target option and web form address variables
# 81211-0422 - Fixed Manual dial agent_log bug
# 90102-1402 - Added time sync check notification
# 90115-0619 - Added ability to send Local Closer to AGENTDIRECT agent_only
# 90120-1719 - Added API pause/resume and number dial functionality
# 90126-2302 - Added Vtiger login option and agent alert option
# 90128-0230 - Added vendor_lead_code to API dial and manuald dial with lookup
# 90202-0148 - Added option to disable BLENDED checkbox
# 90209-0132 - Changed tab images and color scheme
# 90303-1145 - Fixed rare manual dial live hangup bug
# 90304-1333 - Added user-specific web vars option
# 90305-0917 - Added prefix-choice and group-alias options for calls coming from API
# 90307-1736 - Added Shift enforcement and manager override features
# 90315-1009 - Changed revision for new trunk 2.2.0
# 90320-0309 - Fixed agent log bug when using wrapup time
# 90323-1555 - Initial call to agent phone now has campaign callerIDnumber
# 90408-0104 - Added Vtiger callback record ability
# 90508-0727 - Changed to PHP long tags
# 90511-1018 - Added restriction not allowing dialing into agent sessions from manual dial
# 90519-0635 - Fixed manual dial status and logging bug
# 90525-1012 - Fixed transfer issue of auto-received call after manual dial call
# 90529-0741 - Added nophone agent phone login that will not show any empty session alerts
# 90531-0635 - Added option to hide customer phone number
# 90611-1422 - Fixed multiple logging bugs
# 90628-0655 - Added Quick Transfer button and Preset Prepopulate option
# 90705-1400 - Added Agent view sidebar option
# 90706-1432 - Added Agent view transfer selection
# 90709-1649 - Fixed alt-number transfers and dispo variable reset for webform
# 90712-2304 - Added ADD-ALL group selection, view calls in queue, grab call from queue, requeue button
# 90717-0640 - Added dialed_label and dialed_number to script variables
# 90721-1114 - Added rank and owner as vicidial_list fields
# 90726-2012 - Added allow_alerts option
# 90729-0647 - Added agent_display_dialable_leads option
# 90730-0145 - Fixed bugs in re-queue and INBOUND_MAN with blended selected
# 90808-0117 - Fixed manual dial calls today bug, added last_state_change to vicidial_live_agents
# 90812-0046 - Added no-delete-sessions = 1 as default, unused sessions cleared out at timeclock end of day
# 90814-0829 - Moved mute button next to hotkeys button
# 90827-0133 - Reworked Script display code
# 90827-1549 - Added list script override option, original_phone_login variable
# 90831-1456 - Added active_agent_login_server option for servers
# 90908-1038 - Added DEAD call display
# 90909-0921 - Fixed park issues
# 90916-1144 - Added Second web form button, Answering Machine Message change
# 90917-1325 - Fixed script loading bug with customer webform at the same time
# 90920-2108 - Changed web forms to use window.open instead of traditional links(IE7 compatibility issue)
# 90923-1310 - Rolled back last change
# 90928-1955 - Added lead update before closer transfer
# 90930-2243 - Added Territory selection functions
# 91108-2118 - Added QM pause code entry
# 91111-1433 - Fixed Gender pulldown list display for IE, remove links for recording channels in SHOW CHANNELS
# 91123-1801 - Added code for outbound_autodial field
# 91130-2021 - Added code for manager override of in-group selection
# 91204-1638 - Added recording_filename and recording_id script variables and script refresh link
# 91205-2055 - Added CONSULTATIVE checkbox in a redesigned Transfer-Conf frame
# 91206-2020 - Fixed vicidial_agent_log logging bug on logout when not paused
# 91211-1412 - Added User custom variables and CRM login popup
# 91219-0657 - Set pause code automatically on ReQueue and INBOUND_MAN Dial-Next-Number
# 91228-1339 - Added API "fields update" functions and "timer action" functions
# 100103-1250 - Added 3 more conf-presets, list ID override presets and call start/dispo URLs
# 100107-0108 - Added dynamic screen size based on login screen browser dimensions
# 100109-0801 - Added ALTNUM alt number status, fixed alt number dialing from setting
# 100109-1338 - Fixed Manual dial live call detection
# 100116-0709 - Added presets to script and web form variables
# 100123-0954 - changes to AGENTDIRECT selection span
# 100131-2233 - Added functions to allow for a webphone loaded in a separate IFRAME
# 100203-0639 - Fixed logging issues related to INBOUND_MAN dial method
# 100207-1103 - Changed Pause Codes function to allow for multiple pause codes per pause period
# 100220-1040 - Added Call Log View and Customer Info View and fixed HotKeys position
# 100221-1107 - Added Custom CID compatibility
# 100301-1330 - Changed AGENTDIRECT user selection launching to AGENTS link next to number-to-dial field
# 100302-2145 - Added scheduled callbacks alert feature
# 100306-0852 - Added options.php optional file for setting interface options that will survive upgrade
# 100309-0525 - Added queuemetrics_loginout option
# 100313-0053 - Added display options for transfer/conf buttons
# 100315-1148 - fix for rare recording_log uniqueid issue on manual dial calls to same number
# 100317-1301 - Added agent_fullscreen User Group option
# 100327-0901 - fix for manual dial answering machine message
# 100331-1220 - Added human-readable hangup codes for manual dial
# 100401-0019 - Added agent_choose_blended option
# 100413-1349 - Various small logging fixes and extended alt-dial fixes
# 100420-1009 - Added scheduled_callbacks_count option
# 100423-1156 - Added more user logging data and manual_dial_override, blind monitor warnings, uniqueid display and codec features
# 100428-0544 - Added uniqueid display option for PRESERVE
# 100513-0714 - Added options.php option to hide the timeclock link
# 100513-2337 - Changed user_login_first to attempt full login if phone_login/pass are filled in
# 100527-2212 - Added API send_dtmf, transfer_conference and park_call functions
# 100616-1622 - Allowed longer manual dial numbers
# 100622-2209 - Added field labels
# 100625-1118 - Added poor-network-connection-mitigating code
# 100629-1158 - Added initial code for custom list fields
# 100702-1315 - Custom List Fields functionality enabled
# 100712-1441 - Added entry_list_id field to vicidial_list to preserve link to custom fields if any
# 100723-1522 - Added LOCKED options for quick transfer button feature
# 100726-1233 - Added HANGUP, CALLMENU, EXTENSION, IN_GROUP timer actions
# 100803-2324 - Cleanup of URLDecode (issue #375)
# 100811-0810 - Added webphone_url_override option from user Groups
# 100813-0554 - Added Campaign presets
# 100815-1015 - Added manual_dial_prefix campaign option
# 100823-1605 - Added DID variables for webform and scripting
# 100827-1436 - Added webphone dialpad options
# 100902-0046 - Added initial loading screen
# 100902-1349 - Added closecallid, xfercallid, agent_log_id as webform and script variables
# 100908-0955 - Added customer 3way hangup
# 100912-1304 - Changed Dispo screen phone number display to dialed_number
# 100927-1616 - Added custom fields ability to web forms and optimized related code
# 101004-1322 - Added "IVR Park Call" button in agent interface
# 101006-1358 - Raised limits on several dynamic items from the database
# 101008-0356 - Added manual_preview_dial and two new variables for recording filenames
# 101012-1656 - Added scroll command at dispo submission to for scrolling to the top of the screen
# 101024-1639 - Added parked call counter
# 101108-0110 - Added ADDMEMBER option for queue_log
# 101124-0436 - Added manual dial queue and manual dial call time check features
# 101125-2151 - Changed CIDname for 3way calls
# 101128-0102 - Added list webform override options
# 101207-1621 - Added scroll to the top after in-group, pause code, etc... selections, and added focus blur to several functions
# 101208-1210 - Fixed focus/blur coding to work after Dispo
# 101216-1758 - Added the ability to hide fields if the label is set to ---HIDE--- in System Settings
# 101227-1645 - Added dialplan off toggle options, and settings and code changes for top bar webphone
# 110109-1205 - Added queuemetrics_loginout NONE option
# 110112-1254 - Added options.php option for focus/blur/enter functions
# 110129-1050 - Changed to XHTML compliant formatting, issue #444
# 110208-1202 - Made scheduled callbacks notice move when on script/form tabs
# 110212-2206 - Added scheduled callback custom statuses compatibility
# 110215-1412 - Added my_callback_option and per_call_notes options
# 110218-1522 - Added agent_lead_search feature
# 110221-1251 - Changed statuses display to keep track of non-selectable statuses
# 110224-1713 - Added compatibility with QM phone environment logging, QM pause code last call logging and active server twin check
# 110225-1231 - Changed scheduled callbacks list to allow clicking to see lead info without dialing, and separate dial link
# 110303-2321 - Added notice of on-hook phone use, and ability to click 'ring' to call into session, minor queue_log fix
# 110304-1623 - Added callback count notification defer options
# 110310-0331 - Added auto-pause/resume functions in auto-dial mode for pre-call work
# 110310-1627 - Changed most browser alerts to HTML alerts, other bug fixes
# 110322-0923 - Allowed hiding of gender pulldown
# 110413-1244 - Added ALT dialing from scheduled callback list, and other formatting changes
# 110420-1211 - Added web_vars variable
# 110428-1549 - Added use of manual_dial_cid setting
# 110430-1126 - Added ability to use external_dial API function with lead_id and alt_dial options
# 110430-1924 - Added post_phone_time_diff_alert campaign feature
# 110506-1612 - Added custom_3way_button_transfer button feature
# 110510-1637 - Added number validation to custom_3way_button_transfer function
# 110526-1723 - Added webphone_auto_answer option
# 110528-1033 - Added waiting_on_dispo manual dial check
# 110531-2158 - Added callback_days_limit campaign feature
#

$version = '2.4-330c';
$build = '110531-2158';
$mel=1;					# Mysql Error Log enabled = 1
$mysql_log_count=72;
$one_mysql_log=0;

require("dbconnect.php");
require("functions.php");

if (isset($_GET["DB"]))						    {$DB=$_GET["DB"];}
        elseif (isset($_POST["DB"]))            {$DB=$_POST["DB"];}
if (isset($_GET["JS_browser_width"]))				{$JS_browser_width=$_GET["JS_browser_width"];}
        elseif (isset($_POST["JS_browser_width"]))  {$JS_browser_width=$_POST["JS_browser_width"];}
if (isset($_GET["JS_browser_height"]))				{$JS_browser_height=$_GET["JS_browser_height"];}
        elseif (isset($_POST["JS_browser_height"])) {$JS_browser_height=$_POST["JS_browser_height"];}
if (isset($_GET["phone_login"]))                {$phone_login=$_GET["phone_login"];}
        elseif (isset($_POST["phone_login"]))   {$phone_login=$_POST["phone_login"];}
if (isset($_GET["phone_pass"]))					{$phone_pass=$_GET["phone_pass"];}
        elseif (isset($_POST["phone_pass"]))    {$phone_pass=$_POST["phone_pass"];}
if (isset($_GET["VD_login"]))					{$VD_login=$_GET["VD_login"];}
        elseif (isset($_POST["VD_login"]))      {$VD_login=$_POST["VD_login"];}
if (isset($_GET["VD_pass"]))					{$VD_pass=$_GET["VD_pass"];}
        elseif (isset($_POST["VD_pass"]))       {$VD_pass=$_POST["VD_pass"];}
if (isset($_GET["VD_campaign"]))                {$VD_campaign=$_GET["VD_campaign"];}
        elseif (isset($_POST["VD_campaign"]))   {$VD_campaign=$_POST["VD_campaign"];}
if (isset($_GET["relogin"]))					{$relogin=$_GET["relogin"];}
        elseif (isset($_POST["relogin"]))       {$relogin=$_POST["relogin"];}
if (isset($_GET["MGR_override"]))				{$MGR_override=$_GET["MGR_override"];}
        elseif (isset($_POST["MGR_override"]))  {$MGR_override=$_POST["MGR_override"];}
if (!isset($phone_login)) 
	{
	if (isset($_GET["pl"]))                {$phone_login=$_GET["pl"];}
		elseif (isset($_POST["pl"]))   {$phone_login=$_POST["pl"];}
	}
if (!isset($phone_pass))
	{
	if (isset($_GET["pp"]))                {$phone_pass=$_GET["pp"];}
		elseif (isset($_POST["pp"]))   {$phone_pass=$_POST["pp"];}
	}
if (isset($VD_campaign))
	{
	$VD_campaign = strtoupper($VD_campaign);
	$VD_campaign = eregi_replace(" ",'',$VD_campaign);
	}
if (!isset($flag_channels))
	{
	$flag_channels=0;
	$flag_string='';
	}

### security strip all non-alphanumeric characters out of the variables ###
$DB=ereg_replace("[^0-9a-z]","",$DB);
$phone_login=ereg_replace("[^\,0-9a-zA-Z]","",$phone_login);
$phone_pass=ereg_replace("[^0-9a-zA-Z]","",$phone_pass);
$VD_login=ereg_replace("[^-_0-9a-zA-Z]","",$VD_login);
$VD_pass=ereg_replace("[^-_0-9a-zA-Z]","",$VD_pass);
$VD_campaign = ereg_replace("[^-_0-9a-zA-Z]","",$VD_campaign);


$forever_stop=0;

if ($force_logout)
	{
    echo "You have now logged out. Thank you\n";
    exit;
	}

$isdst = date("I");
$StarTtimE = date("U");
$NOW_TIME = date("Y-m-d H:i:s");
$tsNOW_TIME = date("YmdHis");
$FILE_TIME = date("Ymd-His");
$loginDATE = date("Ymd");
$CIDdate = date("ymdHis");
$month_old = mktime(11, 0, 0, date("m"), date("d")-2,  date("Y"));
$past_month_date = date("Y-m-d H:i:s",$month_old);
$minutes_old = mktime(date("H"), date("i")-2, date("s"), date("m"), date("d"),  date("Y"));
$past_minutes_date = date("Y-m-d H:i:s",$minutes_old);
$webphone_width = 460;
$webphone_height = 500;


$random = (rand(1000000, 9999999) + 10000000);

#############################################
##### START SYSTEM_SETTINGS LOOKUP #####
$stmt = "SELECT use_non_latin,vdc_header_date_format,vdc_customer_date_format,vdc_header_phone_format,webroot_writable,timeclock_end_of_day,vtiger_url,enable_vtiger_integration,outbound_autodial_active,enable_second_webform,user_territories_active,static_agent_url,custom_fields_enabled FROM system_settings;";
$rslt=mysql_query($stmt, $link);
	if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'01001',$VD_login,$server_ip,$session_name,$one_mysql_log);}
if ($DB) {echo "$stmt\n";}
$qm_conf_ct = mysql_num_rows($rslt);
if ($qm_conf_ct > 0)
	{
	$row=mysql_fetch_row($rslt);
	$non_latin =					$row[0];
	$vdc_header_date_format =		$row[1];
	$vdc_customer_date_format =		$row[2];
	$vdc_header_phone_format =		$row[3];
	$WeBRooTWritablE =				$row[4];
	$timeclock_end_of_day =			$row[5];
	$vtiger_url =					$row[6];
	$enable_vtiger_integration =	$row[7];
	$outbound_autodial_active =		$row[8];
	$enable_second_webform =		$row[9];
	$user_territories_active =		$row[10];
	$static_agent_url =				$row[11];
	$custom_fields_enabled =		$row[12];
	}
##### END SETTINGS LOOKUP #####
###########################################


##### DEFINABLE SETTINGS AND OPTIONS
###########################################

# set defaults for hard-coded variables
$conf_silent_prefix		= '5';	# vicidial_conferences prefix to enter silently and muted for recording
$dtmf_silent_prefix		= '7';	# vicidial_conferences prefix to enter silently
$HKuser_level			= '5';	# minimum vicidial user_level for HotKeys
$campaign_login_list	= '1';	# show drop-down list of campaigns at login	
$manual_dial_preview	= '1';	# allow preview lead option when manual dial
$multi_line_comments	= '1';	# set to 1 to allow multi-line comment box
$user_login_first		= '0';	# set to 1 to have the vicidial_user login before the phone login
$view_scripts			= '1';	# set to 1 to show the SCRIPTS tab
$dispo_check_all_pause	= '0';	# set to 1 to allow for persistent pause after dispo
$callholdstatus			= '1';	# set to 1 to show calls on hold count
$agentcallsstatus		= '0';	# set to 1 to show agent status and call dialed count
   $campagentstatctmax	= '3';	# Number of seconds for campaign call and agent stats
$show_campname_pulldown	= '1';	# set to 1 to show campaign name on login pulldown
$webform_sessionname	= '1';	# set to 1 to include the session_name in webform URL
$local_consult_xfers	= '1';	# set to 1 to send consultative transfers from original server
$clientDST				= '1';	# set to 1 to check for DST on server for agent time
$no_delete_sessions		= '1';	# set to 1 to not delete sessions at logout
$volumecontrol_active	= '1';	# set to 1 to allow agents to alter volume of channels
$PreseT_DiaL_LinKs		= '0';	# set to 1 to show a DIAL link for Dial Presets
$LogiNAJAX				= '1';	# set to 1 to do lookups on campaigns for login
$HidEMonitoRSessionS	= '1';	# set to 1 to hide remote monitoring channels from "session calls"
$hangup_all_non_reserved= '1';	# set to 1 to force hangup all non-reserved channels upon Hangup Customer
$LogouTKicKAlL			= '1';	# set to 1 to hangup all calls in session upon agent logout
$PhonESComPIP			= '1';	# set to 1 to log computer IP to phone if blank, set to 2 to force log each login
$DefaulTAlTDiaL			= '0';	# set to 1 to enable ALT DIAL by default if enabled for the campaign
$AgentAlert_allowed		= '1';	# set to 1 to allow Agent alert option
$disable_blended_checkbox='0';	# set to 1 to disable the BLENDED checkbox from the in-group chooser screen
$hide_timeclock_link	= '0';	# set to 1 to hide the timeclock link on the agent login screen
$conf_check_attempts	= '100';	# number of attempts to try before loosing webserver connection, for bad network setups
$focus_blur_enabled		= '0';	# set to 1 to enable the focus/blur enter key blocking(some IE instances have issues)
$TEST_all_statuses		= '0';	# TEST variable allows all statuses in dispo screen

$stretch_dimensions		= '1';	# sets the vicidial screen to the size of the browser window
$BROWSER_HEIGHT			= 500;	# set to the minimum browser height, default=500
$BROWSER_WIDTH			= 770;	# set to the minimum browser width, default=770
$webphone_width			= 460;	# set the webphone frame width
$webphone_height		= 500;	# set the webphone frame height
$webphone_pad			= 0;	# set the table cellpadding for the webphone
$webphone_location		= 'right';	# set the location on the agent screen 'right' or 'bar'
$MAIN_COLOR				= '#CCCCCC';	# old default is E0C2D6
$SCRIPT_COLOR			= '#E6E6E6';	# old default is FFE7D0
$FORM_COLOR				= '#EFEFEF';
$SIDEBAR_COLOR			= '#F6F6F6';

# if options file exists, use the override values for the above variables
#   see the options-example.php file for more information
if (file_exists('options.php'))
	{
	require('options.php');
	}

### BEGIN find any custom field labels ###
$label_title =				'Title';
$label_first_name =			'First';
$label_middle_initial =		'MI';
$label_last_name =			'Last';
$label_address1 =			'Address1';
$label_address2 =			'Address2';
$label_address3 =			'Address3';
$label_city =				'City';
$label_state =				'State';
$label_province =			'Province';
$label_postal_code =		'PostCode';
$label_vendor_lead_code =	'Vendor ID';
$label_gender =				'Gender';
$label_phone_number =		'Phone';
$label_phone_code =			'DialCode';
$label_alt_phone =			'Alt. Phone';
$label_security_phrase =	'Show';
$label_email =				'Email';
$label_comments =			'Comments';

$stmt="SELECT label_title,label_first_name,label_middle_initial,label_last_name,label_address1,label_address2,label_address3,label_city,label_state,label_province,label_postal_code,label_vendor_lead_code,label_gender,label_phone_number,label_phone_code,label_alt_phone,label_security_phrase,label_email,label_comments from system_settings;";
$rslt=mysql_query($stmt, $link);
$row=mysql_fetch_row($rslt);
if (strlen($row[0])>0)	{$label_title =				$row[0];}
if (strlen($row[1])>0)	{$label_first_name =		$row[1];}
if (strlen($row[2])>0)	{$label_middle_initial =	$row[2];}
if (strlen($row[3])>0)	{$label_last_name =			$row[3];}
if (strlen($row[4])>0)	{$label_address1 =			$row[4];}
if (strlen($row[5])>0)	{$label_address2 =			$row[5];}
if (strlen($row[6])>0)	{$label_address3 =			$row[6];}
if (strlen($row[7])>0)	{$label_city =				$row[7];}
if (strlen($row[8])>0)	{$label_state =				$row[8];}
if (strlen($row[9])>0)	{$label_province =			$row[9];}
if (strlen($row[10])>0) {$label_postal_code =		$row[10];}
if (strlen($row[11])>0) {$label_vendor_lead_code =	$row[11];}
if (strlen($row[12])>0) {$label_gender =			$row[12];}
if (strlen($row[13])>0) {$label_phone_number =		$row[13];}
if (strlen($row[14])>0) {$label_phone_code =		$row[14];}
if (strlen($row[15])>0) {$label_alt_phone =			$row[15];}
if (strlen($row[16])>0) {$label_security_phrase =	$row[16];}
if (strlen($row[17])>0) {$label_email =				$row[17];}
if (strlen($row[18])>0) {$label_comments =			$row[18];}
### END find any custom field labels ###

$hide_gender=0;
if ($label_gender == '---HIDE---')
	{$hide_gender=1;}

$US='_';
$CL=':';
$AT='@';
$DS='-';
$date = date("r");
$ip = getenv("REMOTE_ADDR");
$browser = getenv("HTTP_USER_AGENT");
$script_name = getenv("SCRIPT_NAME");
$server_name = getenv("SERVER_NAME");
$server_port = getenv("SERVER_PORT");
if (eregi("443",$server_port)) {$HTTPprotocol = 'https://';}
  else {$HTTPprotocol = 'http://';}
if (($server_port == '80') or ($server_port == '443') ) {$server_port='';}
else {$server_port = "$CL$server_port";}
$agcPAGE = "$HTTPprotocol$server_name$server_port$script_name";
$agcDIR = eregi_replace('vicidial.php','',$agcPAGE);
if (strlen($static_agent_url) > 5)
	{$agcPAGE = $static_agent_url;}


header ("Content-type: text/html; charset=utf-8");
header ("Cache-Control: no-cache, must-revalidate");  // HTTP/1.1
header ("Pragma: no-cache");                          // HTTP/1.0
echo '<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" type="text/css" href="css/style.css" />
<link rel="stylesheet" type="text/css" href="css/custom.css" />
';
echo "<!-- VERSION: $version     BUILD: $build -->\n";
echo "<!-- BROWSER: $BROWSER_WIDTH x $BROWSER_HEIGHT     $JS_browser_width x $JS_browser_height -->\n";

if ($campaign_login_list > 0)
	{
    $camp_form_code  = "<select size=\"1\" name=\"VD_campaign\" id=\"VD_campaign\" onfocus=\"login_allowable_campaigns()\">\n";
	$camp_form_code .= "<option value=\"\"></option>\n";

	$LOGallowed_campaignsSQL='';
	if ($relogin == 'YES')
		{
		$stmt="SELECT user_group from vicidial_users where user='$VD_login' and pass='$VD_pass';";
		if ($non_latin > 0) {$rslt=mysql_query("SET NAMES 'UTF8'");}
		$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'01002',$VD_login,$server_ip,$session_name,$one_mysql_log);}
		$row=mysql_fetch_row($rslt);
		$VU_user_group=$row[0];

		$stmt="SELECT allowed_campaigns from vicidial_user_groups where user_group='$VU_user_group';";
		$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'01003',$VD_login,$server_ip,$session_name,$one_mysql_log);}
		$row=mysql_fetch_row($rslt);
		if ( (!eregi("ALL-CAMPAIGNS",$row[0])) )
			{
			$LOGallowed_campaignsSQL = eregi_replace(' -','',$row[0]);
			$LOGallowed_campaignsSQL = eregi_replace(' ',"','",$LOGallowed_campaignsSQL);
			$LOGallowed_campaignsSQL = "and campaign_id IN('$LOGallowed_campaignsSQL')";
			}
		}

	### code for manager override of shift restrictions
	if ($MGR_override > 0)
		{
		if (isset($_GET["MGR_login$loginDATE"]))				{$MGR_login=$_GET["MGR_login$loginDATE"];}
				elseif (isset($_POST["MGR_login$loginDATE"]))	{$MGR_login=$_POST["MGR_login$loginDATE"];}
		if (isset($_GET["MGR_pass$loginDATE"]))					{$MGR_pass=$_GET["MGR_pass$loginDATE"];}
				elseif (isset($_POST["MGR_pass$loginDATE"]))	{$MGR_pass=$_POST["MGR_pass$loginDATE"];}

		$stmt="SELECT count(*) from vicidial_users where user='$MGR_login' and pass='$MGR_pass' and manager_shift_enforcement_override='1' and active='Y';";
		if ($DB) {echo "|$stmt|\n";}
		$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'01058',$VD_login,$server_ip,$session_name,$one_mysql_log);}
		$row=mysql_fetch_row($rslt);
		$MGR_auth=$row[0];

		if($MGR_auth>0)
			{
			$stmt="UPDATE vicidial_users SET shift_override_flag='1' where user='$VD_login' and pass='$VD_pass';";
			if ($DB) {echo "|$stmt|\n";}
			$rslt=mysql_query($stmt, $link);
			if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'01059',$VD_login,$server_ip,$session_name,$one_mysql_log);}
			echo "<!-- Shift Override entered for $VD_login by $MGR_login -->\n";

			### Add a record to the vicidial_admin_log
			$SQL_log = "$stmt|";
			$SQL_log = ereg_replace(';','',$SQL_log);
			$SQL_log = addslashes($SQL_log);
			$stmt="INSERT INTO vicidial_admin_log set event_date='$NOW_TIME', user='$MGR_login', ip_address='$ip', event_section='AGENT', event_type='OVERRIDE', record_id='$VD_login', event_code='MANAGER OVERRIDE OF AGENT SHIFT ENFORCEMENT', event_sql=\"$SQL_log\", event_notes='user: $VD_login';";
			if ($DB) {echo "|$stmt|\n";}
			$rslt=mysql_query($stmt, $link);
			if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'01060',$VD_login,$server_ip,$session_name,$one_mysql_log);}
			}
		}


	$stmt="SELECT campaign_id,campaign_name from vicidial_campaigns where active='Y' $LOGallowed_campaignsSQL order by campaign_id;";
	if ($non_latin > 0) {$rslt=mysql_query("SET NAMES 'UTF8'");}
	$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'01004',$VD_login,$server_ip,$session_name,$one_mysql_log);}
	$camps_to_print = mysql_num_rows($rslt);

	$o=0;
	while ($camps_to_print > $o) 
		{
		$rowx=mysql_fetch_row($rslt);
		if ($show_campname_pulldown)
			{$campname = " - $rowx[1]";}
		else
			{$campname = '';}
		if ($VD_campaign)
			{
			if ( (eregi("$VD_campaign",$rowx[0])) and (strlen($VD_campaign) == strlen($rowx[0])) )
                {$camp_form_code .= "<option value=\"$rowx[0]\" selected=\"selected\">$rowx[0]$campname</option>\n";}
			else
				{
				if (!ereg('login_allowable_campaigns',$camp_form_code))
					{$camp_form_code .= "<option value=\"$rowx[0]\">$rowx[0]$campname</option>\n";}
				}
			}
		else
			{
			if (!ereg('login_allowable_campaigns',$camp_form_code))
					{$camp_form_code .= "<option value=\"$rowx[0]\">$rowx[0]$campname</option>\n";}
			}
		$o++;
		}
	$camp_form_code .= "</select>\n";
	}
else
	{
    $camp_form_code = "<input type=\"text\" name=\"vd_campaign\" size=\"10\" maxlength=\"20\" value=\"$VD_campaign\" />\n";
	}


if ($LogiNAJAX > 0)
	{
	?>

    <script type="text/javascript">

	<!-- 
	var BrowseWidth = 0;
	var BrowseHeight = 0;

	function browser_dimensions() 
		{
	<?php 
		if (ereg('MSIE',$browser)) 
			{
			echo "	if (document.documentElement && document.documentElement.clientHeight)\n";
			echo "			{BrowseWidth = document.documentElement.clientWidth;}\n";
			echo "		else if (document.body)\n";
			echo "			{BrowseWidth = document.body.clientWidth;}\n";
			echo "		if (document.documentElement && document.documentElement.clientHeight)\n";
			echo "			{BrowseHeight = document.documentElement.clientHeight;}\n";
			echo "		else if (document.body)\n";
			echo "			{BrowseHeight = document.body.clientHeight;}\n";
			}
		else 
			{
			echo "BrowseWidth = window.innerWidth;\n";
			echo "		BrowseHeight = window.innerHeight;\n";
			}
	?>

		document.vicidial_form.JS_browser_width.value = BrowseWidth;
		document.vicidial_form.JS_browser_height.value = BrowseHeight;
		}

	// ################################################################################
	// Send Request for allowable campaigns to populate the campaigns pull-down
		function login_allowable_campaigns() 
			{
		//	alert(document.vicidial_form.JS_browser_width.value + '|' + BrowseWidth + '|' + document.vicidial_form.JS_browser_height.value + '|' + BrowseHeight);
			var xmlhttp=false;
			/*@cc_on @*/
			/*@if (@_jscript_version >= 5)
			// JScript gives us Conditional compilation, we can cope with old IE versions.
			// and security blocked creation of the objects.
			 try {
			  xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
			 } catch (e) {
			  try {
			   xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
			  } catch (E) {
			   xmlhttp = false;
			  }
			 }
			@end @*/
			if (!xmlhttp && typeof XMLHttpRequest!='undefined')
				{
				xmlhttp = new XMLHttpRequest();
				}
			if (xmlhttp) 
				{ 
				logincampaign_query = "&user=" + document.vicidial_form.VD_login.value + "&pass=" + document.vicidial_form.VD_pass.value + "&ACTION=LogiNCamPaigns&format=html";
				xmlhttp.open('POST', 'vdc_db_query.php'); 
				xmlhttp.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
				xmlhttp.send(logincampaign_query); 
				xmlhttp.onreadystatechange = function() 
					{ 
					if (xmlhttp.readyState == 4 && xmlhttp.status == 200) 
						{
						Nactiveext = null;
						Nactiveext = xmlhttp.responseText;
					//	alert(logincampaign_query);
					//	alert(xmlhttp.responseText);
						document.getElementById("LogiNCamPaigns").innerHTML = Nactiveext;
                        document.getElementById("LogiNReseT").innerHTML = "<input type=\"button\" value=\"Refresh Campaign List\" onclick=\"login_allowable_campaigns()\" />";
						document.getElementById("VD_campaign").focus();
						}
					}
				delete xmlhttp;
				}
			}
	// -->
	</script>

	<?php
	}
else
	{
	?>

    <script type="text/javascript">

	<!-- 
	function browser_dimensions() 
		{
		var nothing=0;
		}

	// -->
	</script>

	<?php

	}

if ($relogin == 'YES')
	{
	echo "<title>Agent web client: Re-Login</title>\n";
	echo "</head>\n";
    echo "<body onresize=\"browser_dimensions();\" onload=\"browser_dimensions();\">\n";
	if ($hide_timeclock_link < 1)
        {echo "<a href=\"./timeclock.php?referrer=agent&amp;pl=$phone_login&amp;pp=$phone_pass&amp;VD_login=$VD_login&amp;VD_pass=$VD_pass\"> Timeclock</a><br />\n";}
    echo "<table width=\"100%\"><tr><td></td>\n";
	echo "<!-- INTERNATIONALIZATION-LINKS-PLACEHOLDER-VICIDIAL -->\n";
    echo "</tr></table>\n";
    echo "<form name=\"vicidial_form\" id=\"vicidial_form\" action=\"$agcPAGE\" method=\"post\">\n";
    echo "<input type=\"hidden\" name=\"DB\" id=\"DB\" value=\"$DB\" />\n";
    echo "<input type=\"hidden\" name=\"JS_browser_height\" id=\"JS_browser_height\" value=\"\" />\n";
    echo "<input type=\"hidden\" name=\"JS_browser_width\" id=\"JS_browser_width\" value=\"\" />\n";
    echo "<br /><br /><br /><center><table width=\"460px\" cellpadding=\"0\" cellspacing=\"0\" bgcolor=\"$MAIN_COLOR\"><tr bgcolor=\"white\">";
    echo "<td align=\"left\" valign=\"bottom\"><img src=\"./images/vdc_tab_vicidial.gif\" border=\"0\" alt=\"VICIdial\" /></td>";
    echo "<td align=\"center\" valign=\"middle\"> Re-Login </td>";
    echo "</tr>\n";
    echo "<tr><td align=\"left\" colspan=\"2\"><font size=\"1\"> &nbsp; </font></td></tr>\n";
    echo "<tr><td align=\"right\">Phone Login: </td>";
    echo "<td align=\"left\"><input type=\"text\" name=\"phone_login\" size=\"10\" maxlength=\"20\" value=\"$phone_login\" /></td></tr>\n";
    echo "<tr><td align=\"right\">Phone Password:  </td>";
    echo "<td align=\"left\"><input type=\"password\" name=\"phone_pass\" size=\"10\" maxlength=\"20\" value=\"$phone_pass\" /></td></tr>\n";
    echo "<tr><td align=\"right\">User Login:  </td>";
    echo "<td align=\"left\"><input type=\"text\" name=\"VD_login\" size=\"10\" maxlength=\"20\" value=\"$VD_login\" /></td></tr>\n";
    echo "<tr><td align=\"right\">User Password:  </td>";
    echo "<td align=\"left\"><input type=\"password\" name=\"VD_pass\" size=\"10\" maxlength=\"20\" value=\"$VD_pass\" /></td></tr>\n";
    echo "<tr><td align=\"right\">Campaign:  </td>";
    echo "<td align=\"left\"><span id=\"LogiNCamPaigns\">$camp_form_code</span></td></tr>\n";
    echo "<tr><td align=\"center\" colspan=\"2\"><input type=\"submit\" name=\"SUBMIT\" value=\"Submit\" /> &nbsp; \n";
    echo "<span id=\"LogiNReseT\"><input type=\"button\" value=\"Refresh Campaign List\" onclick=\"login_allowable_campaigns()\"></span></td></tr>\n";
    echo "<tr><td align=\"left\" colspan=\"2\"><font size=\"1\"><br />VERSION: $version &nbsp; &nbsp; &nbsp; BUILD: $build</font></td></tr>\n";
    echo "</table></center>\n";
    echo "</form>\n\n";br
	echo "</body>\n\n";
	echo "</html>\n\n";
	exit;
	}

if ($user_login_first == 1)
	{
	if ( (strlen($VD_login)<1) or (strlen($VD_pass)<1) or (strlen($VD_campaign)<1) )
		{
		echo "<title>Agent web client: Campaign Login</title>\n";
		echo "</head>\n";
        echo "<body onresize=\"browser_dimensions();\" onload=\"browser_dimensions();\">\n";
		if ($hide_timeclock_link < 1)
            {echo "<a href=\"./timeclock.php?referrer=agent&amp;pl=$phone_login&amp;pp=$phone_pass&amp;VD_login=$VD_login&amp;VD_pass=$VD_pass\"> Timeclock</a><br />\n";}
        echo "<table width=\"100%\"><tr><td></td>\n";
		echo "<!-- INTERNATIONALIZATION-LINKS-PLACEHOLDER-VICIDIAL -->\n";
        echo "</tr></table>\n";
        echo "<form name=\"vicidial_form\" id=\"vicidial_form\" action=\"$agcPAGE\" method=\"post\">\n";
        echo "<input type=\"hidden\" name=\"DB\" value=\"$DB\" />\n";
        echo "<input type=\"hidden\" name=\"JS_browser_height\" id=\"JS_browser_height\" value=\"\" />\n";
        echo "<input type=\"hidden\" name=\"JS_browser_width\" id=\"JS_browser_width\" value=\"\" />\n";
        #echo "<input type=\"hidden\" name=\"phone_login\" value=\"$phone_login\">\n";
        #echo "<input type=\"hidden\" name=\"phone_pass\" value=\"$phone_pass\">\n";
        echo "<center><br /><b>User Login</b><br /><br />";
        echo "<table width=\"460px\" cellpadding=\"0\" cellspacing=\"0\" bgcolor=\"$MAIN_COLOR\"><tr bgcolor=\"white\">";
        echo "<td align=\"left\" valign=\"bottom\"><img src=\"./images/vdc_tab_vicidial.gif\" border=\"0\" alt=\"VICIdial\" /></td>";
        echo "<td align=\"center\" valign=\"middle\"> Campaign Login </td>";
        echo "</tr>\n";
        echo "<tr><td align=\"left\" colspan=\"2\"><font size=\"1\"> &nbsp; </font></td></tr>\n";
        echo "<tr><td align=\"right\">User Login:  </td>";
        echo "<td align=\"left\"><input type=\"text\" name=\"VD_login\" size=\"10\" maxlength=\"20\" value=\"$VD_login\" /></td></tr>\n";
        echo "<tr><td align=\"right\">User Password:  </td>";
        echo "<td align=\"left\"><input type=\"password\" name=\"VD_pass\" size=\"10\" maxlength=\"20\" value=\"$VD_pass\" /></td></tr>\n";
        echo "<tr><td align=\"right\">Campaign:  </td>";
        echo "<td align=\"left\"><span id=\"LogiNCamPaigns\">$camp_form_code</span></td></tr>\n";
        echo "<tr><td align=\"center\" colspan=\"2\"><input type=\"submit\" name=\"SUBMIT\" value=\"Submit\" /> &nbsp; \n";
        echo "<span id=\"LogiNReseT\"></span></td></tr>\n";
        echo "<tr><td align=\"left\" colspan=\"2\"><font size=\"1\"><br />VERSION: $version &nbsp; &nbsp; &nbsp; BUILD: $build</font></td></tr>\n";
        echo "</table>\n";
        echo "</form>\n\n";
		echo "</body>\n\n";
		echo "</html>\n\n";
		exit;
		}
	else
		{
		if ( (strlen($phone_login)<2) or (strlen($phone_pass)<2) )
			{
			$stmt="SELECT phone_login,phone_pass from vicidial_users where user='$VD_login' and pass='$VD_pass' and user_level > 0 and active='Y';";
			if ($DB) {echo "|$stmt|\n";}
			$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'01005',$VD_login,$server_ip,$session_name,$one_mysql_log);}
			$row=mysql_fetch_row($rslt);
			$phone_login=$row[0];
			$phone_pass=$row[1];

			if ( (strlen($phone_login) < 1) or (strlen($phone_pass) < 1) )
				{
				echo "<title>Agent web client: Login</title>\n";
				echo "</head>\n";
                echo "<body onresize=\"browser_dimensions();\"  onLoad=\"browser_dimensions();\">\n";
				if ($hide_timeclock_link < 1)
                    {echo "<a href=\"./timeclock.php?referrer=agent&amp;pl=$phone_login&amp;pp=$phone_pass&amp;VD_login=$VD_login&amp;VD_pass=$VD_pass\"> Timeclock</a><br />\n";}
                echo "<table width=\"100%\"><tr><td></td>\n";
				echo "<!-- INTERNATIONALIZATION-LINKS-PLACEHOLDER-VICIDIAL -->\n";
                echo "</tr></table>\n";
                echo "<form  name=\"vicidial_form\" id=\"vicidial_form\" action=\"$agcPAGE\" method=\"post\">\n";
                echo "<input type=\"hidden\" name=\"DB\" value=\"$DB\" />\n";
                echo "<input type=\"hidden\" name=\"JS_browser_height\" id=\"JS_browser_height\" value=\"\" />\n";
                echo "<input type=\"hidden\" name=\"JS_browser_width\" id=\"JS_browser_width\" value=\"\" />\n";
                echo "<br /><br /><br /><center><table width=\"460px\" cellpadding=\"0\" cellspacing=\"0\" bgcolor=\"$MAIN_COLOR\"><tr bgcolor=\"white\">";
                echo "<td align=\"left\" valign=\"bottom\"><img src=\"./images/vdc_tab_vicidial.gif\" border=\"0\" alt=\"VICIdial\" /></td>";
                echo "<td align=\"center\" valign=\"middle\"> Login </td>";
                echo "</tr>\n";
                echo "<tr><td align=\"left\" colspan=\"2\"><font size=\"1\"> &nbsp; </font></td></tr>\n";
                echo "<tr><td align=\"right\">Phone Login: </td>";
                echo "<td align=\"left\"><input type=\"text\" name=\"phone_login\" size=\"10\" maxlength=\"20\" value=\"$phone_login\" /></td></tr>\n";
                echo "<tr><td align=\"right\">Phone Password:  </td>";
                echo "<td align=\"left\"><input type=\"password\" name=\"phone_pass\" size=\"10\" maxlength=\"20\" value=\"$phone_pass\" /></td></tr>\n";
                echo "<tr><td align=\"right\">User Login:  </td>";
                echo "<td align=\"left\"><input type=\"text\" name=\"VD_login\" size=\"10\" maxlength=\"20\" value=\"$VD_login\"> /</td></tr>\n";
                echo "<tr><td align=\"right\">User Password:  </td>";
                echo "<td align=\"left\"><input type=\"password\" name=\"VD_pass\" size=\"10\" maxlength=\"20\" value=\"$VD_pass\" /></td></tr>\n";
                echo "<tr><td align=\"right\">Campaign:  </td>";
                echo "<td align=\"left\"><span id=\"LogiNCamPaigns\">$camp_form_code</span></td></tr>\n";
                echo "<tr><td align=\"center\" colspan=\"2>\"<input type=\"submit\" name=\"SUBMIT\" value=\"Submit\" /> &nbsp; \n";
                echo "<span id=\"LogiNReseT\"></span></td></tr>\n";
                echo "<tr><td align=\"left\" colspan=\"2\"><font size=\"1\"><br />VERSION: $version &nbsp; &nbsp; &nbsp; BUILD: $build</font></td></tr>\n";
                echo "</table></center>\n";
                echo "</form>\n\n";
				echo "</body>\n\n";
				echo "</html>\n\n";
				exit;
				}
			}
		}
	}

if ( (strlen($phone_login)<2) or (strlen($phone_pass)<2) )
	{
	echo "<title>Agent web client:  Phone Login</title>\n";
	echo "</head>\n";
    echo "<body onresize=\"browser_dimensions();\"  onload=\"browser_dimensions();\">\n";
	if ($hide_timeclock_link < 1)
        {echo "<a href=\"./timeclock.php?referrer=agent&amp;pl=$phone_login&amp;pp=$phone_pass&amp;VD_login=$VD_login&amp;VD_pass=$VD_pass\"> Timeclock</a><br />\n";}
    echo "<table width=100%><tr><td></td>\n";
	echo "<!-- INTERNATIONALIZATION-LINKS-PLACEHOLDER-VICIDIAL -->\n";
    echo "</tr></table>\n";
    echo "<form name=\"vicidial_form\" id=\"vicidial_form\" action=\"$agcPAGE\" method=\"post\">\n";
    echo "<input type=\"hidden\" name=\"DB\" value=\"$DB\" />\n";
    echo "<input type=\"hidden\" name=\"JS_browser_height\" id=\"JS_browser_height\" value=\"\" />\n";
    echo "<input type=\"hidden\" name=\"JS_browser_width\" id=\"JS_browser_width\" value=\"\" />\n";
    echo "<br /><br /><br /><center><table width=\"460px\" cellpadding=\"0\" cellspacing=\"0\" bgcolor=\"$MAIN_COLOR\"><tr bgcolor=\"white\">";
    echo "<td align=\"left\" valign=\"bottom\"><img src=\"./images/vdc_tab_vicidial.gif\" border=\"0\" alt=\"VICIdial\" /></td>";
    echo "<td align=\"center\" valign=\"middle\"> phone login </td>";
    echo "</tr>\n";
    echo "<tr><td align=\"left\" colspan=\"2\"><font size=\"1\"> &nbsp; </font></td></tr>\n";
    echo "<tr><td align=\"right\">Phone Login: </td>";
    echo "<td align=\"left\"><input type=\"text\" name=\"phone_login\" size=\"10\" maxlength=\"20\" value=\"\" /></td></tr>\n";
    echo "<tr><td align=\"right\">Phone Password:  </td>";
    echo "<td align=\"left\"><input type=\"password\" name=\"phone_pass\" size=\"10\" maxlength=\"20\" value=\"\" /></td></tr>\n";
    echo "<tr><td align=\"center\" colspan=\"2\"><input type=\"submit\" name=\"SUBMIT\" value=\"Submit\" /> &nbsp; \n";
    echo "<span id=\"LogiNReseT\"></span></td></tr>\n";
    echo "<tr><td align=\"left\" colspan=\"2\"><font size=\"1\"><br />VERSION: $version &nbsp; &nbsp; &nbsp; BUILD: $build</font></td></tr>\n";
    echo "</table></center>\n";
    echo "</form>\n\n";
	echo "</body>\n\n";
	echo "</html>\n\n";
	exit;
	}
else
	{
	if ($WeBRooTWritablE > 0)
		{$fp = fopen ("./vicidial_auth_entries.txt", "a");}
	$VDloginDISPLAY=0;

	if ( (strlen($VD_login)<2) or (strlen($VD_pass)<2) or (strlen($VD_campaign)<2) )
		{
		$VDloginDISPLAY=1;
		}
	else
		{
		$stmt="SELECT count(*) from vicidial_users where user='$VD_login' and pass='$VD_pass' and user_level > 0 and active='Y';";
		if ($DB) {echo "|$stmt|\n";}
		$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'01006',$VD_login,$server_ip,$session_name,$one_mysql_log);}
		$row=mysql_fetch_row($rslt);
		$auth=$row[0];

		if($auth>0)
			{
			$login=strtoupper($VD_login);
			$password=strtoupper($VD_pass);
			##### grab the full name of the agent
			$stmt="SELECT full_name,user_level,hotkeys_active,agent_choose_ingroups,scheduled_callbacks,agentonly_callbacks,agentcall_manual,vicidial_recording,vicidial_transfers,closer_default_blended,user_group,vicidial_recording_override,alter_custphone_override,alert_enabled,agent_shift_enforcement_override,shift_override_flag,allow_alerts,closer_campaigns,agent_choose_territories,custom_one,custom_two,custom_three,custom_four,custom_five,agent_call_log_view_override,agent_choose_blended,agent_lead_search_override from vicidial_users where user='$VD_login' and pass='$VD_pass'";
			$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'01007',$VD_login,$server_ip,$session_name,$one_mysql_log);}
			$row=mysql_fetch_row($rslt);
			$LOGfullname =							$row[0];
			$user_level =							$row[1];
			$VU_hotkeys_active =					$row[2];
			$VU_agent_choose_ingroups =				$row[3];
			$VU_scheduled_callbacks =				$row[4];
			$agentonly_callbacks =					$row[5];
			$agentcall_manual =						$row[6];
			$VU_vicidial_recording =				$row[7];
			$VU_vicidial_transfers =				$row[8];
			$VU_closer_default_blended =			$row[9];
			$VU_user_group =						$row[10];
			$VU_vicidial_recording_override =		$row[11];
			$VU_alter_custphone_override =			$row[12];
			$VU_alert_enabled =						$row[13];
			$VU_agent_shift_enforcement_override =	$row[14];
			$VU_shift_override_flag =				$row[15];
			$VU_allow_alerts =						$row[16];
			$VU_closer_campaigns =					$row[17];
			$VU_agent_choose_territories =			$row[18];
			$VU_custom_one =						$row[19];
			$VU_custom_two =						$row[20];
			$VU_custom_three =						$row[21];
			$VU_custom_four =						$row[22];
			$VU_custom_five =						$row[23];
			$VU_agent_call_log_view_override =		$row[24];
			$VU_agent_choose_blended =				$row[25];
			$VU_agent_lead_search_override =		$row[26];


			if ( ($VU_alert_enabled > 0) and ($VU_allow_alerts > 0) ) {$VU_alert_enabled = 'ON';}
			else {$VU_alert_enabled = 'OFF';}
			$AgentAlert_allowed = $VU_allow_alerts;

			### Gather timeclock and shift enforcement restriction settings
			$stmt="SELECT forced_timeclock_login,shift_enforcement,group_shifts,agent_status_viewable_groups,agent_status_view_time,agent_call_log_view,agent_xfer_consultative,agent_xfer_dial_override,agent_xfer_vm_transfer,agent_xfer_blind_transfer,agent_xfer_dial_with_customer,agent_xfer_park_customer_dial,agent_fullscreen,webphone_url_override,webphone_dialpad_override,webphone_systemkey_override from vicidial_user_groups where user_group='$VU_user_group';";
			$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'01052',$VD_login,$server_ip,$session_name,$one_mysql_log);}
			$row=mysql_fetch_row($rslt);
			$forced_timeclock_login =	$row[0];
			$shift_enforcement =		$row[1];
			$LOGgroup_shiftsSQL = eregi_replace('  ','',$row[2]);
			$LOGgroup_shiftsSQL = eregi_replace(' ',"','",$LOGgroup_shiftsSQL);
			$LOGgroup_shiftsSQL = "shift_id IN('$LOGgroup_shiftsSQL')";
			$agent_status_viewable_groups = $row[3];
			$agent_status_viewable_groupsSQL = eregi_replace('  ','',$agent_status_viewable_groups);
			$agent_status_viewable_groupsSQL = eregi_replace(' ',"','",$agent_status_viewable_groupsSQL);
			$agent_status_viewable_groupsSQL = "user_group IN('$agent_status_viewable_groupsSQL')";
			$agent_status_view = 0;
			if (strlen($agent_status_viewable_groups) > 2)
				{$agent_status_view = 1;}
			$agent_status_view_time=0;
			if ($row[4] == 'Y')
				{$agent_status_view_time=1;}
			if ($row[5] == 'Y')
				{$agent_call_log_view=1;}
			if ($row[6] == 'Y')
				{$agent_xfer_consultative=1;}
			if ($row[7] == 'Y')
				{$agent_xfer_dial_override=1;}
			if ($row[8] == 'Y')
				{$agent_xfer_vm_transfer=1;}
			if ($row[9] == 'Y')
				{$agent_xfer_blind_transfer=1;}
			if ($row[10] == 'Y')
				{$agent_xfer_dial_with_customer=1;}
			if ($row[11] == 'Y')
				{$agent_xfer_park_customer_dial=1;}
			if ($VU_agent_call_log_view_override == 'Y')
				{$agent_call_log_view=1;}
			if ($VU_agent_call_log_view_override == 'N')
				{$agent_call_log_view=0;}
			$agent_fullscreen =			$row[12];
			$webphone_url =	$row[13];
			$webphone_dialpad_override = $row[14];
			$system_key = $row[15];
			if ( ($webphone_dialpad_override != 'DISABLED') and (strlen($webphone_dialpad_override) > 0) )
				{$webphone_dialpad = $webphone_dialpad_override;}

			### BEGIN - CHECK TO SEE IF AGENT IS LOGGED IN TO TIMECLOCK, IF NOT, OUTPUT ERROR
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
				$stmt="SELECT event from vicidial_timeclock_log where user='$VD_login' and event_epoch >= '$EoD' order by timeclock_id desc limit 1;";
				$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'01053',$VD_login,$server_ip,$session_name,$one_mysql_log);}
				$events_to_parse = mysql_num_rows($rslt);
				if ($events_to_parse > 0)
					{
					$rowx=mysql_fetch_row($rslt);
					$last_agent_event = $rowx[0];
					}
				if ($DB>0) {echo "|$stmt|$events_to_parse|$last_agent_event|";}
				if ( (strlen($last_agent_event)<2) or (ereg('LOGOUT',$last_agent_event)) )
					{
					$VDloginDISPLAY=1;
                    $VDdisplayMESSAGE = "YOU MUST LOG IN TO THE TIMECLOCK FIRST<br />";
					}
				}
			### END - CHECK TO SEE IF AGENT IS LOGGED IN TO TIMECLOCK, IF NOT, OUTPUT ERROR

			### BEGIN - CHECK TO SEE IF SHIFT ENFORCEMENT IS ENABLED AND AGENT IS OUTSIDE OF THEIR SHIFTS, IF SO, OUTPUT ERROR
			if ( ( (ereg("START|ALL",$shift_enforcement)) and (!ereg("OFF",$VU_agent_shift_enforcement_override)) ) or (ereg("START|ALL",$VU_agent_shift_enforcement_override)) )
				{
				$shift_ok=0;
				if ( (strlen($LOGgroup_shiftsSQL) < 3) and ($VU_shift_override_flag < 1) )
					{
					$VDloginDISPLAY=1;
                    $VDdisplayMESSAGE = "ERROR: There are no Shifts enabled for your user group<br />";
					}
				else
					{
					$HHMM = date("Hi");
					$wday = date("w");

					$stmt="SELECT shift_id,shift_start_time,shift_length,shift_weekdays from vicidial_shifts where $LOGgroup_shiftsSQL order by shift_id";
					$rslt=mysql_query($stmt, $link);
						if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'01056',$VD_login,$server_ip,$session_name,$one_mysql_log);}
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
						$VDloginDISPLAY=1;
                        $VDdisplayMESSAGE = "ERROR: You are not allowed to log in outside of your shift<br />";
						}
					}
				if ( ($shift_ok < 1) and ($VU_shift_override_flag < 1) and ($VDloginDISPLAY > 0) )
					{
                    $VDdisplayMESSAGE.= "<br /><br />MANAGER OVERRIDE:<br />\n";
                    $VDdisplayMESSAGE.= "<form action=\"$PHP_SELF\" method=\"post\">\n";
                    $VDdisplayMESSAGE.= "<input type=\"hidden\" name=\"MGR_override\" value=\"1\" />\n";
                    $VDdisplayMESSAGE.= "<input type=\"hidden\" name=\"relogin\" value=\"YES\" />\n";
                    $VDdisplayMESSAGE.= "<input type=\"hidden\" name=\"DB\" value=\"$DB\" />\n";
                    $VDdisplayMESSAGE.= "<input type=\"hidden\" name=\"phone_login\" value=\"$phone_login\" />\n";
                    $VDdisplayMESSAGE.= "<input type=\"hidden\" name=\"phone_pass\" value=\"$phone_pass\" />\n";
                    $VDdisplayMESSAGE.= "<input type=\"hidden\" name=\"VD_login\" value=\"$VD_login\" />\n";
                    $VDdisplayMESSAGE.= "<input type=\"hidden\" name=\"VD_pass\" value=\"$VD_pass\" />\n";
                    $VDdisplayMESSAGE.= "Manager Login: <input type=\"text\" name=\"MGR_login$loginDATE\" size=\"10\" maxlength=\"20\" /><br />\n";
                    $VDdisplayMESSAGE.= "Manager Password: <input type=\"password\" name=\"MGR_pass$loginDATE\" size=\"10\" maxlength=\"20\" /><br />\n";
                    $VDdisplayMESSAGE.= "<input type=\"submit\" name=\"submit\" value=\"Submit\" /></form>\n";
					}
				}
				### END - CHECK TO SEE IF SHIFT ENFORCEMENT IS ENABLED AND AGENT IS OUTSIDE OF THEIR SHIFTS, IF SO, OUTPUT ERROR



			if ($WeBRooTWritablE > 0)
				{
				fwrite ($fp, "vdweb|GOOD|$date|$VD_login|$VD_pass|$ip|$browser|$LOGfullname|\n");
				fclose($fp);
				}
			$user_abb = "$VD_login$VD_login$VD_login$VD_login";
			while ( (strlen($user_abb) > 4) and ($forever_stop < 200) )
				{$user_abb = eregi_replace("^.","",$user_abb);   $forever_stop++;}

			$stmt="SELECT allowed_campaigns from vicidial_user_groups where user_group='$VU_user_group';";
			$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'01008',$VD_login,$server_ip,$session_name,$one_mysql_log);}
			$row=mysql_fetch_row($rslt);
			$LOGallowed_campaigns		=$row[0];

			if ( (!eregi(" $VD_campaign ",$LOGallowed_campaigns)) and (!eregi("ALL-CAMPAIGNS",$LOGallowed_campaigns)) )
				{
				echo "<title>Agent web client: Campaign Login</title>\n";
				echo "</head>\n";
                echo "<body onresize=\"browser_dimensions();\" onload=\"browser_dimensions();\">\n";
				if ($hide_timeclock_link < 1)
                    {echo "<a href=\"./timeclock.php?referrer=agent&amp;pl=$phone_login&amp;pp=$phone_pass&amp;VD_login=$VD_login&amp;VD_pass=$VD_pass\"> Timeclock</a><br />\n";}
                echo "<table width=\"100%\"><tr><td></td>\n";
				echo "<!-- INTERNATIONALIZATION-LINKS-PLACEHOLDER-VICIDIAL -->\n";
                echo "</tr></table>\n";
                echo "<b>Sorry, you are not allowed to login to this campaign: $VD_campaign</b>\n";
                echo "<form action=\"$PHP_SELF\" method=\"post\">\n";
                echo "<input type=\"hidden\" name=\"db\" value=\"$DB\" />\n";
                echo "<input type=\"hidden\" name=\"JS_browser_height\" id=\"JS_browser_height\" value=\"\" />\n";
                echo "<input type=\"hidden\" name=\"JS_browser_width\" id=\"JS_browser_width\" value=\"\" />\n";
                echo "<input type=\"hidden\" name=\"phone_login\" value=\"$phone_login\" />\n";
                echo "<input type=\"hidden\" name=\"phone_pass\" value=\"$phone_pass\" />\n";
                echo "Login: <input type=\"text\" name=\"VD_login\" size=\"10\" maxlength=\"20\" value=\"$VD_login\" />\n<br />";
                echo "Password: <input type=\"password\" name=\"VD_pass\" size=\"10\" maxlength=\"20\" value=\"$VD_pass\" /><br />\n";
                echo "Campaign: <span id=\"LogiNCamPaigns\">$camp_form_code</span><br />\n";
                echo "<input type=\"submit\" name=\"SUBMIT\" value=\"Submit\" /> &nbsp; \n";
				echo "<span id=\"LogiNReseT\"></span>\n";
                echo "</form>\n\n";
				echo "</body>\n\n";
				echo "</html>\n\n";
				exit;
				}

			##### check to see that the campaign is active
			$stmt="SELECT count(*) FROM vicidial_campaigns where campaign_id='$VD_campaign' and active='Y';";
			if ($DB) {echo "|$stmt|\n";}
			$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'01009',$VD_login,$server_ip,$session_name,$one_mysql_log);}
			$row=mysql_fetch_row($rslt);
			$CAMPactive=$row[0];
			if($CAMPactive>0)
				{
				$VARstatuses='';
				$VARstatusnames='';
                                $VARstatuscategories='';
				$VARSELstatuses='';
				$VARSELstatuses_ct=0;
				$VARCBstatuses='';
				$VARCBstatusesLIST='';
				##### grab the statuses that can be used for dispositioning by an agent
				$stmt="SELECT status,status_name,scheduled_callback,selectable FROM vicidial_statuses WHERE status != 'NEW' order by status limit 500;";
				$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'01010',$VD_login,$server_ip,$session_name,$one_mysql_log);}
				if ($DB) {echo "$stmt\n";}
				$VD_statuses_ct = mysql_num_rows($rslt);
				$i=0;
				while ($i < $VD_statuses_ct)
					{
					$row=mysql_fetch_row($rslt);
					$statuses[$i] =$row[0];
					$status_names[$i] =$row[1];
                                        $status_categories[$i] ="SYSTEM";
					$CBstatuses[$i] =$row[2];
					$SELstatuses[$i] =$row[3];
					if ($TEST_all_statuses > 0) {$SELstatuses[$i]='Y';}
					$VARstatuses = "$VARstatuses'$statuses[$i]',";
					$VARstatusnames = "$VARstatusnames'$status_names[$i]',";
                                        $VARstatuscategories = "$VARstatuscategories'$status_categories[$i]',";
					$VARSELstatuses = "$VARSELstatuses'$SELstatuses[$i]',";
					$VARCBstatuses = "$VARCBstatuses'$CBstatuses[$i]',";
					if ($CBstatuses[$i] == 'Y')
						{$VARCBstatusesLIST .= " $statuses[$i]";}
					if ($SELstatuses[$i] == 'Y')
						{$VARSELstatuses_ct++;}
					$i++;
					}

				##### grab the campaign-specific statuses that can be used for dispositioning by an agent
				$stmt="SELECT status,status_name,scheduled_callback,selectable,category FROM vicidial_campaign_statuses WHERE status != 'NEW' and campaign_id='$VD_campaign' order by status limit 500;";
				$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'01011',$VD_login,$server_ip,$session_name,$one_mysql_log);}
				if ($DB) {echo "$stmt\n";}
				$VD_statuses_camp = mysql_num_rows($rslt);
				$j=0;
				while ($j < $VD_statuses_camp)
					{
					$row=mysql_fetch_row($rslt);
					$statuses[$i] =$row[0];
					$status_names[$i] =$row[1];
					$CBstatuses[$i] =$row[2];
					$SELstatuses[$i] =$row[3];
                                        $status_categories[$i] =$row[4];
					if ($TEST_all_statuses > 0) {$SELstatuses[$i]='Y';}
					$VARstatuses = "$VARstatuses'$statuses[$i]',";
					$VARstatusnames = "$VARstatusnames'$status_names[$i]',";
                                        $VARstatuscategories = "$VARstatuscategories'$status_categories[$i]',";
					$VARSELstatuses = "$VARSELstatuses'$SELstatuses[$i]',";
					$VARCBstatuses = "$VARCBstatuses'$CBstatuses[$i]',";
					if ($CBstatuses[$i] == 'Y')
						{$VARCBstatusesLIST .= " $statuses[$i]";}
					if ($SELstatuses[$i] == 'Y')
						{$VARSELstatuses_ct++;}
					$i++;
					$j++;
					}
				$VD_statuses_ct = ($VD_statuses_ct+$VD_statuses_camp);
				$VARstatuses = substr("$VARstatuses", 0, -1);
				$VARstatusnames = substr("$VARstatusnames", 0, -1);
                                $VARstatuscategories = substr("$VARstatuscategories", 0, -1);
				$VARSELstatuses = substr("$VARSELstatuses", 0, -1);
				$VARCBstatuses = substr("$VARCBstatuses", 0, -1);
				$VARCBstatusesLIST .= " ";

				##### grab the campaign-specific HotKey statuses that can be used for dispositioning by an agent
				$stmt="SELECT hotkey,status,status_name FROM vicidial_campaign_hotkeys WHERE selectable='Y' and status != 'NEW' and campaign_id='$VD_campaign' order by hotkey limit 9;";
				$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'01012',$VD_login,$server_ip,$session_name,$one_mysql_log);}
				if ($DB) {echo "$stmt\n";}
				$HK_statuses_camp = mysql_num_rows($rslt);
				$w=0;
				$HKboxA='';
				$HKboxB='';
				$HKboxC='';
				while ($w < $HK_statuses_camp)
					{
					$row=mysql_fetch_row($rslt);
					$HKhotkey[$w] =$row[0];
					$HKstatus[$w] =$row[1];
					$HKstatus_name[$w] =$row[2];
					$HKhotkeys = "$HKhotkeys'$HKhotkey[$w]',";
					$HKstatuses = "$HKstatuses'$HKstatus[$w]',";
					$HKstatusnames = "$HKstatusnames'$HKstatus_name[$w]',";
					if ($w < 3)
                        {$HKboxA = "$HKboxA <font class=\"skb_text\">$HKhotkey[$w]</font> - $HKstatus[$w] - $HKstatus_name[$w]<br />";}
					if ( ($w >= 3) and ($w < 6) )
                        {$HKboxB = "$HKboxB <font class=\"skb_text\">$HKhotkey[$w]</font> - $HKstatus[$w] - $HKstatus_name[$w]<br />";}
					if ($w >= 6)
                        {$HKboxC = "$HKboxC <font class=\"skb_text\">$HKhotkey[$w]</font> - $HKstatus[$w] - $HKstatus_name[$w]<br />";}
					$w++;
					}
				$HKhotkeys = substr("$HKhotkeys", 0, -1); 
				$HKstatuses = substr("$HKstatuses", 0, -1); 
				$HKstatusnames = substr("$HKstatusnames", 0, -1); 

				##### grab the campaign settings
				$stmt="SELECT park_ext,park_file_name,web_form_address,allow_closers,auto_dial_level,dial_timeout,dial_prefix,campaign_cid,campaign_vdad_exten,campaign_rec_exten,campaign_recording,campaign_rec_filename,campaign_script,get_call_launch,am_message_exten,xferconf_a_dtmf,xferconf_a_number,xferconf_b_dtmf,xferconf_b_number,alt_number_dialing,scheduled_callbacks,wrapup_seconds,wrapup_message,closer_campaigns,use_internal_dnc,allcalls_delay,omit_phone_code,agent_pause_codes_active,no_hopper_leads_logins,campaign_allow_inbound,manual_dial_list_id,default_xfer_group,xfer_groups,disable_alter_custphone,display_queue_count,manual_dial_filter,agent_clipboard_copy,use_campaign_dnc,three_way_call_cid,dial_method,three_way_dial_prefix,web_form_target,vtiger_screen_login,agent_allow_group_alias,default_group_alias,quick_transfer_button,prepopulate_transfer_preset,view_calls_in_queue,view_calls_in_queue_launch,call_requeue_button,pause_after_each_call,no_hopper_dialing,agent_dial_owner_only,agent_display_dialable_leads,web_form_address_two,agent_select_territories,crm_popup_login,crm_login_address,timer_action,timer_action_message,timer_action_seconds,start_call_url,dispo_call_url,xferconf_c_number,xferconf_d_number,xferconf_e_number,use_custom_cid,scheduled_callbacks_alert,scheduled_callbacks_count,manual_dial_override,blind_monitor_warning,blind_monitor_message,blind_monitor_filename,timer_action_destination,enable_xfer_presets,hide_xfer_number_to_dial,manual_dial_prefix,customer_3way_hangup_logging,customer_3way_hangup_seconds,customer_3way_hangup_action,ivr_park_call,manual_preview_dial,api_manual_dial,manual_dial_call_time_check,my_callback_option,per_call_notes,agent_lead_search,agent_lead_search_method,queuemetrics_phone_environment,auto_pause_precall,auto_pause_precall_code,auto_resume_precall,manual_dial_cid,custom_3way_button_transfer,callback_days_limit FROM vicidial_campaigns where campaign_id = '$VD_campaign';";
				$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'01013',$VD_login,$server_ip,$session_name,$one_mysql_log);}
				if ($DB) {echo "$stmt\n";}
				$row=mysql_fetch_row($rslt);
				$park_ext =					$row[0];
				$park_file_name =			$row[1];
				$web_form_address =			stripslashes($row[2]);
				$allow_closers =			$row[3];
				$auto_dial_level =			$row[4];
				$dial_timeout =				$row[5];
				$dial_prefix =				$row[6];
				$campaign_cid =				$row[7];
				$campaign_vdad_exten =		$row[8];
				$campaign_rec_exten =		$row[9];
				$campaign_recording =		$row[10];
				$campaign_rec_filename =	$row[11];
				$campaign_script =			$row[12];
				$get_call_launch =			$row[13];
				$campaign_am_message_exten = '8320';
				$xferconf_a_dtmf =			$row[15];
				$xferconf_a_number =		$row[16];
				$xferconf_b_dtmf =			$row[17];
				$xferconf_b_number =		$row[18];
				$alt_number_dialing =		$row[19];
				$VC_scheduled_callbacks =	$row[20];
				$wrapup_seconds =			$row[21];
				$wrapup_message =			$row[22];
				$closer_campaigns =			$row[23];
				$use_internal_dnc =			$row[24];
				$allcalls_delay =			$row[25];
				$omit_phone_code =			$row[26];
				$agent_pause_codes_active =	$row[27];
				$no_hopper_leads_logins =	$row[28];
				$campaign_allow_inbound =	$row[29];
				$manual_dial_list_id =		$row[30];
				$default_xfer_group =		$row[31];
				$xfer_groups =				$row[32];
				$disable_alter_custphone =	$row[33];
				$display_queue_count =		$row[34];
				$manual_dial_filter =		$row[35];
				$CopY_tO_ClipboarD =		$row[36];
				$use_campaign_dnc =			$row[37];
				$three_way_call_cid =		$row[38];
				$dial_method =				$row[39];
				$three_way_dial_prefix =	$row[40];
				$web_form_target =			$row[41];
				$vtiger_screen_login =		$row[42];
				$agent_allow_group_alias =	$row[43];
				$default_group_alias =		$row[44];
				$quick_transfer_button =	$row[45];
				$prepopulate_transfer_preset = $row[46];
				$view_calls_in_queue =		$row[47];
				$view_calls_in_queue_launch = $row[48];
				$call_requeue_button =		$row[49];
				$pause_after_each_call =	$row[50];
				$no_hopper_dialing =		$row[51];
				$agent_dial_owner_only =	$row[52];
				$agent_display_dialable_leads = $row[53];
				$web_form_address_two =		$row[54];
				$agent_select_territories = $row[55];
				$crm_popup_login =			$row[56];
				$crm_login_address =		$row[57];
				$timer_action =				$row[58];
				$timer_action_message =		$row[59];
				$timer_action_seconds =		$row[60];
				$start_call_url =			$row[61];
				$dispo_call_url =			$row[62];
				$xferconf_c_number =		$row[63];
				$xferconf_d_number =		$row[64];
				$xferconf_e_number =		$row[65];
				$use_custom_cid =			$row[66];
				$scheduled_callbacks_alert = $row[67];
				$scheduled_callbacks_count = $row[68];
				$manual_dial_override =		$row[69];
				$blind_monitor_warning =	$row[70];
				$blind_monitor_message =	$row[71];
				$blind_monitor_filename =	$row[72];
				$timer_action_destination =	$row[73];
				$enable_xfer_presets =		$row[74];
				$hide_xfer_number_to_dial =	$row[75];
				$manual_dial_prefix =		$row[76];
				$customer_3way_hangup_logging =	$row[77];
				$customer_3way_hangup_seconds =	$row[78];
				$customer_3way_hangup_action =	$row[79];
				$ivr_park_call =			$row[80];
				$manual_preview_dial =		$row[81];
				$api_manual_dial =			$row[82];
				$manual_dial_call_time_check = $row[83];
				$my_callback_option =		$row[84];
				$per_call_notes = 			$row[85];
				$agent_lead_search =		$row[86];
				$agent_lead_search_method = $row[87];
				$qm_phone_environment =		$row[88];
				$auto_pause_precall =		$row[89];
				$auto_pause_precall_code =	$row[90];
				$auto_resume_precall =		$row[91];
				$manual_dial_cid =			$row[92];
				$custom_3way_button_transfer =	$row[93];
				$callback_days_limit =		$row[94];

				if ( ($VU_agent_lead_search_override == 'ENABLED') or ($VU_agent_lead_search_override == 'DISABLED') )
					{$agent_lead_search = $VU_agent_lead_search_override;}
				$AllowManualQueueCalls=1;
				$AllowManualQueueCallsChoice=0;
				if ($api_manual_dial == 'QUEUE')
					{
					$AllowManualQueueCalls=0;
					$AllowManualQueueCallsChoice=1;
					}
				if ($manual_preview_dial == 'DISABLED')
					{$manual_dial_preview = 0;}
				if ($manual_dial_override == 'ALLOW_ALL')
					{$agentcall_manual = 1;}
				if ($manual_dial_override == 'DISABLE_ALL')
					{$agentcall_manual = 0;}
				if ($user_territories_active < 1)
					{$agent_select_territories = 0;}
				if (preg_match("/Y/",$agent_select_territories))
					{$agent_select_territories=1;}
				else
					{$agent_select_territories=0;}

				if (preg_match("/Y/",$agent_display_dialable_leads))
					{$agent_display_dialable_leads=1;}
				else
					{$agent_display_dialable_leads=0;}

				if (preg_match("/Y/",$no_hopper_dialing))
					{$no_hopper_dialing=1;}
				else
					{$no_hopper_dialing=0;}

				if ( (preg_match("/Y/",$call_requeue_button)) and ($auto_dial_level > 0) )
					{$call_requeue_button=1;}
				else
					{$call_requeue_button=0;}

				if ( (preg_match("/AUTO/",$view_calls_in_queue_launch)) and ($auto_dial_level > 0) )
					{$view_calls_in_queue_launch=1;}
				else
					{$view_calls_in_queue_launch=0;}

				if ( (!preg_match("/NONE/",$view_calls_in_queue)) and ($auto_dial_level > 0) )
					{$view_calls_in_queue=1;}
				else
					{$view_calls_in_queue=0;}

				if (preg_match("/Y/",$pause_after_each_call))
					{$dispo_check_all_pause=1;}

				$quick_transfer_button_enabled=0;
				$quick_transfer_button_locked=0;
				if (preg_match("/IN_GROUP|PRESET_1|PRESET_2|PRESET_3|PRESET_4|PRESET_5/",$quick_transfer_button))
					{$quick_transfer_button_enabled=1;}
				if (preg_match("/LOCKED/",$quick_transfer_button))
					{$quick_transfer_button_locked=1;}

				$custom_3way_button_transfer_enabled=0;
				$custom_3way_button_transfer_park=0;
				if (preg_match("/PRESET_|FIELD_/",$custom_3way_button_transfer))
					{$custom_3way_button_transfer_enabled=1;}
				if (preg_match("/PARK_/",$custom_3way_button_transfer))
					{$custom_3way_button_transfer_park=1;}

				$preset_populate='';
				$prepopulate_transfer_preset_enabled=0;
				if (preg_match("/PRESET_1|PRESET_2|PRESET_3|PRESET_4|PRESET_5/",$prepopulate_transfer_preset))
					{
					$prepopulate_transfer_preset_enabled=1;
					if (preg_match("/PRESET_1/",$prepopulate_transfer_preset))
						{$preset_populate = $xferconf_a_number;}
					if (preg_match("/PRESET_2/",$prepopulate_transfer_preset))
						{$preset_populate = $xferconf_b_number;}
					if (preg_match("/PRESET_3/",$prepopulate_transfer_preset))
						{$preset_populate = $xferconf_c_number;}
					if (preg_match("/PRESET_4/",$prepopulate_transfer_preset))
						{$preset_populate = $xferconf_d_number;}
					if (preg_match("/PRESET_5/",$prepopulate_transfer_preset))
						{$preset_populate = $xferconf_e_number;}
					}

				$VARpreset_names='';
				$VARpreset_numbers='';
				$VARpreset_dtmfs='';
				$VARpreset_hide_numbers='';
				if ($enable_xfer_presets == 'ENABLED')
					{
					##### grab the presets for this campaign
					$stmt="SELECT preset_name,preset_number,preset_dtmf,preset_hide_number FROM vicidial_xfer_presets WHERE campaign_id='$VD_campaign' order by preset_name limit 500;";
					$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'01067',$VD_login,$server_ip,$session_name,$one_mysql_log);}
					if ($DB) {echo "$stmt\n";}
					$VD_presets = mysql_num_rows($rslt);
					$j=0;
					while ($j < $VD_presets)
						{
						$row=mysql_fetch_row($rslt);
						$preset_names[$j] =			$row[0];
						$preset_numbers[$j] =		$row[1];
						$preset_dtmfs[$j] =			$row[2];
						$preset_hide_numbers[$j] =	$row[3];
						$VARpreset_names = "$VARpreset_names'$preset_names[$j]',";
						$VARpreset_numbers = "$VARpreset_numbers'$preset_numbers[$j]',";
						$VARpreset_dtmfs = "$VARpreset_dtmfs'$preset_dtmfs[$j]',";
						$VARpreset_hide_numbers = "$VARpreset_hide_numbers'$preset_hide_numbers[$j]',";
						$j++;
						}
					$VARpreset_names = substr("$VARpreset_names", 0, -1);
					$VARpreset_numbers = substr("$VARpreset_numbers", 0, -1);
					$VARpreset_dtmfs = substr("$VARpreset_dtmfs", 0, -1);
					$VARpreset_hide_numbers = substr("$VARpreset_hide_numbers", 0, -1);
					$VD_preset_names_ct = $j;
					if ($j < 1)
						{$enable_xfer_presets='DISABLED';}
					}

				$default_group_alias_cid='';
				if (strlen($default_group_alias)>1)
					{
					$stmt = "select caller_id_number from groups_alias where group_alias_id='$default_group_alias';";
					if ($DB) {echo "$stmt\n";}
					$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'01055',$VD_login,$server_ip,$session_name,$one_mysql_log);}
					$VDIG_cidnum_ct = mysql_num_rows($rslt);
					if ($VDIG_cidnum_ct > 0)
						{
						$row=mysql_fetch_row($rslt);
						$default_group_alias_cid	= $row[0];
						}
					}

				$stmt = "select group_web_vars from vicidial_campaign_agents where campaign_id='$VD_campaign' and user='$VD_login';";
				if ($DB) {echo "$stmt\n";}
				$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'01056',$VD_login,$server_ip,$session_name,$one_mysql_log);}
				$VDIG_cidogwv = mysql_num_rows($rslt);
				if ($VDIG_cidogwv > 0)
					{
					$row=mysql_fetch_row($rslt);
					$default_web_vars =	$row[0];
					}

				if ( (!ereg('DISABLED',$VU_vicidial_recording_override)) and ($VU_vicidial_recording > 0) )
					{
					$campaign_recording = $VU_vicidial_recording_override;
					echo "<!-- USER RECORDING OVERRIDE: |$VU_vicidial_recording_override|$campaign_recording| -->\n";
					}
				if ( ($VC_scheduled_callbacks=='Y') and ($VU_scheduled_callbacks=='1') )
					{$scheduled_callbacks='1';}
				if ($VU_vicidial_recording=='0')
					{$campaign_recording='NEVER';}
				if ($VU_alter_custphone_override=='ALLOW_ALTER')
					{$disable_alter_custphone='N';}
				if (strlen($manual_dial_prefix) < 1)
					{$manual_dial_prefix = $dial_prefix;}
				if (strlen($three_way_dial_prefix) < 1)
					{$three_way_dial_prefix = $dial_prefix;}
				if ($alt_number_dialing=='Y')
					{$alt_phone_dialing='1';}
				else
					{
					$alt_phone_dialing='0';
					$DefaulTAlTDiaL='0';
					}
				if ($display_queue_count=='N')
					{$callholdstatus='0';}
				if ( ($dial_method == 'INBOUND_MAN') or ($outbound_autodial_active < 1) )
					{$VU_closer_default_blended=0;}

				$closer_campaigns = preg_replace("/^ | -$/","",$closer_campaigns);
				$closer_campaigns = preg_replace("/ /","','",$closer_campaigns);
				$closer_campaigns = "'$closer_campaigns'";

				if ( (ereg('Y',$agent_pause_codes_active)) or (ereg('FORCE',$agent_pause_codes_active)) )
					{
					##### grab the pause codes for this campaign
					$stmt="SELECT pause_code,pause_code_name FROM vicidial_pause_codes WHERE campaign_id='$VD_campaign' order by pause_code limit 100;";
					$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'01014',$VD_login,$server_ip,$session_name,$one_mysql_log);}
					if ($DB) {echo "$stmt\n";}
					$VD_pause_codes = mysql_num_rows($rslt);
					$j=0;
					while ($j < $VD_pause_codes)
						{
						$row=mysql_fetch_row($rslt);
						$pause_codes[$i] =$row[0];
						$pause_code_names[$i] =$row[1];
						$VARpause_codes = "$VARpause_codes'$pause_codes[$i]',";
						$VARpause_code_names = "$VARpause_code_names'$pause_code_names[$i]',";
						$i++;
						$j++;
						}
					$VD_pause_codes_ct = ($VD_pause_codes_ct+$VD_pause_codes);
					$VARpause_codes = substr("$VARpause_codes", 0, -1); 
					$VARpause_code_names = substr("$VARpause_code_names", 0, -1); 
					}

				##### grab the inbound groups to choose from if campaign contains CLOSER
				$VARingroups="''";
				if ( ($campaign_allow_inbound == 'Y') and ($dial_method != 'MANUAL') )
					{
					$VARingroups='';
					$stmt="select group_id from vicidial_inbound_groups where active = 'Y' and group_id IN($closer_campaigns) order by group_id limit 800;";
					$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'01015',$VD_login,$server_ip,$session_name,$one_mysql_log);}
					if ($DB) {echo "$stmt\n";}
					$closer_ct = mysql_num_rows($rslt);
					$INgrpCT=0;
					while ($INgrpCT < $closer_ct)
						{
						$row=mysql_fetch_row($rslt);
						$closer_groups[$INgrpCT] =$row[0];
						$VARingroups = "$VARingroups'$closer_groups[$INgrpCT]',";
						$INgrpCT++;
						}
					$VARingroups = substr("$VARingroups", 0, -1); 
					}
				else
					{$closer_campaigns = "''";}

				##### gather territory listings for this agent if select territories is enabled
				$VARterritories='';
				if ($agent_select_territories > 0)
					{
					$stmt="SELECT territory from vicidial_user_territories where user='$VD_login';";
					$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'01062',$VD_login,$server_ip,$session_name,$one_mysql_log);}
					if ($DB) {echo "$stmt\n";}
					$territory_ct = mysql_num_rows($rslt);
					$territoryCT=0;
					while ($territoryCT < $territory_ct)
						{
						$row=mysql_fetch_row($rslt);
						$territories[$territoryCT] =$row[0];
						$VARterritories = "$VARterritories'$territories[$territoryCT]',";
						$territoryCT++;
						}
					$VARterritories = substr("$VARterritories", 0, -1); 
					echo "<!-- $territory_ct  $territoryCT |$stmt| -->\n";
					}

				##### grab the allowable inbound groups to choose from for transfer options
				$xfer_groups = preg_replace("/^ | -$/","",$xfer_groups);
				$xfer_groups = preg_replace("/ /","','",$xfer_groups);
				$xfer_groups = "'$xfer_groups'";
				$VARxfergroups="''";
				if ($allow_closers == 'Y')
					{
					$VARxfergroups='';
					$stmt="select group_id,group_name from vicidial_inbound_groups where active = 'Y' and group_id IN($xfer_groups) order by group_id limit 800;";
					$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'01016',$VD_login,$server_ip,$session_name,$one_mysql_log);}
					if ($DB) {echo "$stmt\n";}
					$xfer_ct = mysql_num_rows($rslt);
					$XFgrpCT=0;
					while ($XFgrpCT < $xfer_ct)
						{
						$row=mysql_fetch_row($rslt);
						$VARxfergroups = "$VARxfergroups'$row[0]',";
						$VARxfergroupsnames = "$VARxfergroupsnames'$row[1]',";
						if ($row[0] == "$default_xfer_group") {$default_xfer_group_name = $row[1];}
						$XFgrpCT++;
						}
					$VARxfergroups = substr("$VARxfergroups", 0, -1); 
					$VARxfergroupsnames = substr("$VARxfergroupsnames", 0, -1); 
					}

				if (ereg('Y',$agent_allow_group_alias))
					{
					##### grab the active group aliases
					$stmt="SELECT group_alias_id,group_alias_name,caller_id_number FROM groups_alias WHERE active='Y' order by group_alias_id limit 1000;";
					$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'01054',$VD_login,$server_ip,$session_name,$one_mysql_log);}
					if ($DB) {echo "$stmt\n";}
					$VD_group_aliases = mysql_num_rows($rslt);
					$j=0;
					while ($j < $VD_group_aliases)
						{
						$row=mysql_fetch_row($rslt);
						$group_alias_id[$i] =	$row[0];
						$group_alias_name[$i] = $row[1];
						$caller_id_number[$i] = $row[2];
						$VARgroup_alias_ids = "$VARgroup_alias_ids'$group_alias_id[$i]',";
						$VARgroup_alias_names = "$VARgroup_alias_names'$group_alias_name[$i]',";
						$VARcaller_id_numbers = "$VARcaller_id_numbers'$caller_id_number[$i]',";
						$i++;
						$j++;
						}
					$VD_group_aliases_ct = ($VD_group_aliases_ct+$VD_group_aliases);
					$VARgroup_alias_ids = substr("$VARgroup_alias_ids", 0, -1); 
					$VARgroup_alias_names = substr("$VARgroup_alias_names", 0, -1); 
					$VARcaller_id_numbers = substr("$VARcaller_id_numbers", 0, -1); 
					}

				##### grab the number of leads in the hopper for this campaign
				$stmt="SELECT count(*) FROM vicidial_hopper where campaign_id = '$VD_campaign' and status='READY';";
				$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'01017',$VD_login,$server_ip,$session_name,$one_mysql_log);}
				if ($DB) {echo "$stmt\n";}
				$row=mysql_fetch_row($rslt);
				   $campaign_leads_to_call = $row[0];
				   echo "<!-- $campaign_leads_to_call - leads left to call in hopper -->\n";

				}
			else
				{
				$VDloginDISPLAY=1;
                $VDdisplayMESSAGE = "Campaign not active, please try again<br />";
				}
			}
		else
			{
			if ($WeBRooTWritablE > 0)
				{
				fwrite ($fp, "vdweb|FAIL|$date|$VD_login|$VD_pass|$ip|$browser|\n");
				fclose($fp);
				}
			$VDloginDISPLAY=1;
            $VDdisplayMESSAGE = "Login incorrect, please try again<br />";
			}
		}
	if ($VDloginDISPLAY)
		{
		echo "<title>Agent web client: Campaign Login</title>\n";
		echo "</head>\n";
        echo "<body onresize=\"browser_dimensions();\"  onload=\"browser_dimensions();\">\n";
		if ($hide_timeclock_link < 1)
            {echo "<a href=\"./timeclock.php?referrer=agent&amp;pl=$phone_login&amp;pp=$phone_pass&amp;VD_login=$VD_login&amp;VD_pass=$VD_pass\"> Timeclock</a><br />\n";}
        echo "<table width=\"100%\"><tr><td></td>\n";
		echo "<!-- INTERNATIONALIZATION-LINKS-PLACEHOLDER-VICIDIAL -->\n";
        echo "</tr></table>\n";
        echo "<form name=\"vicidial_form\" id=\"vicidial_form\" action=\"$agcPAGE\" method=\"post\">\n";
        echo "<input type=\"hidden\" name=\"DB\" value=\"$DB\" />\n";
        echo "<input type=\"hidden\" name=\"JS_browser_height\" id=\"JS_browser_height\" value=\"\" />\n";
        echo "<input type=\"hidden\" name=\"JS_browser_width\" id=\"JS_browser_width\" value=\"\" />\n";
        echo "<input type=\"hidden\" name=\"phone_login\" value=\"$phone_login\" />\n";
        echo "<input type=\"hidden\" name=\"phone_pass\" value=\"$phone_pass\" />\n";
        echo "<center><br /><b>$VDdisplayMESSAGE</b><br /><br />";
        echo "<table width=\"460px\" cellpadding=\"0\" cellspacing=\"0\" bgcolor=\"$MAIN_COLOR\"><tr bgcolor=\"white\">";
        echo "<td align=\"left\" valign=\"bottom\"><img src=\"./images/vdc_tab_vicidial.gif\" border=\"0\" alt=\"VICIdial\" /></td>";
        echo "<td align=\"center\" valign=\"middle\"> Campaign Login </td>";
        echo "</tr>\n";
        echo "<tr><td align=\"left\" colspan=\"2\"><font size=\"1\"> &nbsp; </font></td></tr>\n";
        echo "<tr><td align=\"right\">User Login:  </td>";
        echo "<td align=\"left\"><input type=\"text\" name=\"VD_login\" size=\"10\" maxlength=\"20\" value=\"$VD_login\" /></td></tr>\n";
        echo "<tr><td align=\"right\">User Password:  </td>";
        echo "<td align=\"left\"><input type=\"password\" name=\"VD_pass\" size=\"10\" maxlength=\"20\" value=\"$VD_pass\" /></td></tr>\n";
        echo "<tr><td align=\"right\">Campaign:  </td>";
        echo "<td align=\"left\"><span id=\"LogiNCamPaigns\">$camp_form_code</span></td></tr>\n";
        echo "<tr><td align=\"center\" colspan=\"2\"><input type=\"submit\" name=\"SUBMIT\" value=\"Submit\" /> &nbsp; \n";
        echo "<span id=\"LogiNReseT\"></span></td></tr>\n";
        echo "<tr><td align=\"left\" colspan=\"2\"><font size=\"1\"><br />VERSION: $version &nbsp; &nbsp; &nbsp; BUILD: $build</font></td></tr>\n";
        echo "</table>\n";
        echo "</form>\n\n";
		echo "</body>\n\n";
		echo "</html>\n\n";
		exit;
		}

	$original_phone_login = $phone_login;

	# code for parsing load-balanced agent phone allocation where agent interface
	# will send multiple phones-table logins so that the script can determine the
	# server that has the fewest agents logged into it.
	#   login: ca101,cb101,cc101
		$alias_found=0;
	$stmt="select count(*) from phones_alias where alias_id = '$phone_login';";
	$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'01018',$VD_login,$server_ip,$session_name,$one_mysql_log);}
	$alias_ct = mysql_num_rows($rslt);
	if ($alias_ct > 0)
		{
		$row=mysql_fetch_row($rslt);
		$alias_found = "$row[0]";
		}
	if ($alias_found > 0)
		{
		$stmt="select alias_name,logins_list from phones_alias where alias_id = '$phone_login' limit 1;";
		$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'01019',$VD_login,$server_ip,$session_name,$one_mysql_log);}
		$alias_ct = mysql_num_rows($rslt);
		if ($alias_ct > 0)
			{
			$row=mysql_fetch_row($rslt);
			$alias_name = "$row[0]";
			$phone_login = "$row[1]";
			}
		}

	$pa=0;
	if ( (eregi(',',$phone_login)) and (strlen($phone_login) > 2) )
		{
		$phoneSQL = "(";
		$phones_auto = explode(',',$phone_login);
		$phones_auto_ct = count($phones_auto);
		while($pa < $phones_auto_ct)
			{
			if ($pa > 0)
				{$phoneSQL .= " or ";}
			$desc = ($phones_auto_ct - $pa); # traverse in reverse order
			$phoneSQL .= "(login='$phones_auto[$desc]' and pass='$phone_pass')";
			$pa++;
			}
		$phoneSQL .= ")";
		}
	else {$phoneSQL = "login='$phone_login' and pass='$phone_pass'";}

	$authphone=0;
	#$stmt="SELECT count(*) from phones where $phoneSQL and active = 'Y';";
	$stmt="SELECT count(*) from phones,servers where $phoneSQL and phones.active = 'Y' and active_agent_login_server='Y' and phones.server_ip=servers.server_ip;";
	if ($DB) {echo "|$stmt|\n";}
	$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'01020',$VD_login,$server_ip,$session_name,$one_mysql_log);}
	$row=mysql_fetch_row($rslt);
	$authphone=$row[0];
	if (!$authphone)
		{
		echo "<title>Agent web client: Phone Login Error</title>\n";
		echo "</head>\n";
        echo "<body onresize=\"browser_dimensions();\"  onload=\"browser_dimensions();\">\n";
		if ($hide_timeclock_link < 1)
            {echo "<a href=\"./timeclock.php?referrer=agent&amp;pl=$phone_login&amp;pp=$phone_pass&amp;VD_login=$VD_login&amp;VD_pass=$VD_pass\"> Timeclock</a><br />\n";}
        echo "<table width=\"100%\"><tr><td></td>\n";
		echo "<!-- INTERNATIONALIZATION-LINKS-PLACEHOLDER-VICIDIAL -->\n";
        echo "</tr></table>\n";
        echo "<form name=\"vicidial_form\" id=\"vicidial_form\" action=\"$agcPAGE\" method=\"post\">\n";
        echo "<input type=\"hidden\" name=\"DB\" value=\"$DB\">\n";
        echo "<input type=\"hidden\" name=\"JS_browser_height\" value=\"\" />\n";
        echo "<input type=\"hidden\" name=\"JS_browser_width\" value=\"\" />\n";
        echo "<input type=\"hidden\" name=\"VD_login\" value=\"$VD_login\" />\n";
        echo "<input type=\"hidden\" name=\"VD_pass\" value=\"$VD_pass\" />\n";
        echo "<input type=\"hidden\" name=\"VD_campaign\" value=\"$VD_campaign\" />\n";
        echo "<br /><br /><br /><center><table width=\"460px\" cellpadding=\"0\" cellspacing=\"0\" bgcolor=\"$MAIN_COLOR\"><tr bgcolor=\"white\">";
        echo "<td align=\"left\" valign=\"bottom\"><img src=\"./images/vdc_tab_vicidial.gif\" border=\"0\" alt=\"VICIdial\" /></td>";
        echo "<td align=\"center\" valign=\"middle\"> Login Error</td>";
        echo "</tr>\n";
        echo "<tr><td align=\"center\" colspan=\"2\"><font size=\"1\"> &nbsp; <br /><font size=\"3\">Sorry, your phone login and password are not active in this system, please try again: <br /> &nbsp;</font></td></tr>\n";
        echo "<tr><td align=\"right\">Phone Login: </td>";
        echo "<td align=\"left\"><input type=\"text\" name=\"phone_login\" size=\"10\" maxlength=\"20\" value=\"$phone_login\"></td></tr>\n";
        echo "<tr><td align=\"right\">Phone Password:  </td>";
        echo "<td align=\"left\"><input type=\"password\" name=\"phone_pass\" size=10 maxlength=20 value=\"$phone_pass\"></td></tr>\n";
        echo "<tr><td align=\"center\" colspan=\"2\"><input type=\"submit\" name=\"SUBMIT\" value=\"Submit\" /></td></tr>\n";
        echo "<tr><td align=\"left\" colspan=\"2\"><font size=\"1\"><br />VERSION: $version &nbsp; &nbsp; &nbsp; BUILD: $build</font></td></tr>\n";
        echo "</table></center>\n";
        echo "</form>\n\n";
		echo "</body>\n\n";
		echo "</html>\n\n";
		exit;
		}
	else
		{
	### go through the entered phones to figure out which server has fewest agents
	### logged in and use that phone login account
		if ($pa > 0)
			{
			$pb=0;
			$pb_login='';
			$pb_server_ip='';
			$pb_count=0;
			$pb_log='';
			while($pb < $phones_auto_ct)
				{
				### find the server_ip of each phone_login
				$stmtx="SELECT server_ip from phones where login = '$phones_auto[$pb]';";
				if ($DB) {echo "|$stmtx|\n";}
				if ($non_latin > 0) {$rslt=mysql_query("SET NAMES 'UTF8'");}
				$rslt=mysql_query($stmtx, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'01021',$VD_login,$server_ip,$session_name,$one_mysql_log);}
				$rowx=mysql_fetch_row($rslt);

				### get number of agents logged in to each server
				$stmt="SELECT count(*) from vicidial_live_agents where server_ip = '$rowx[0]';";
				if ($DB) {echo "|$stmt|\n";}
				$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'01022',$VD_login,$server_ip,$session_name,$one_mysql_log);}
				$row=mysql_fetch_row($rslt);
				
				### find out whether the server is set to active
				$stmt="SELECT count(*) from servers where server_ip = '$rowx[0]' and active='Y' and active_agent_login_server='Y';";
				if ($DB) {echo "|$stmt|\n";}
				$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'01023',$VD_login,$server_ip,$session_name,$one_mysql_log);}
				$rowy=mysql_fetch_row($rslt);

				### find out if this server has a twin
				$twin_not_live=0;
				$stmt="SELECT active_twin_server_ip from servers where server_ip = '$rowx[0]';";
				if ($DB) {echo "|$stmt|\n";}
				$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'01070',$VD_login,$server_ip,$session_name,$one_mysql_log);}
				$rowyy=mysql_fetch_row($rslt);
				if (strlen($rowyy[0]) > 4)
					{
					### find out whether the twin server_updater is running
					$stmt="SELECT count(*) from server_updater where server_ip = '$rowyy[0]' and last_update > '$past_minutes_date';";
					if ($DB) {echo "|$stmt|\n";}
					$rslt=mysql_query($stmt, $link);
					if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'01071',$VD_login,$server_ip,$session_name,$one_mysql_log);}
					$rowyz=mysql_fetch_row($rslt);
					if ($rowyz[0] < 1) {$twin_not_live=1;}
					}

				### find out whether the server_updater is running
				$stmt="SELECT count(*) from server_updater where server_ip = '$rowx[0]' and last_update > '$past_minutes_date';";
				if ($DB) {echo "|$stmt|\n";}
				$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'01024',$VD_login,$server_ip,$session_name,$one_mysql_log);}
				$rowz=mysql_fetch_row($rslt);

				$pb_log .= "$phones_auto[$pb]|$rowx[0]|$row[0]|$rowy[0]|$rowz[0]|$twin_not_live|   ";

				if ( ($rowy[0] > 0) and ($rowz[0] > 0) and ($twin_not_live < 1) )
					{
					if ( ($pb_count >= $row[0]) or (strlen($pb_server_ip) < 4) )
						{
						$pb_count=$row[0];
						$pb_server_ip=$rowx[0];
						$phone_login=$phones_auto[$pb];
						}
					}
				$pb++;
				}
			echo "<!-- Phones balance selection: $phone_login|$pb_server_ip|$past_minutes_date|     |$pb_log -->\n";
			}
		echo "<title>Agent web client</title>\n";
		$stmt="SELECT extension,dialplan_number,voicemail_id,phone_ip,computer_ip,server_ip,login,pass,status,active,phone_type,fullname,company,picture,messages,old_messages,protocol,local_gmt,ASTmgrUSERNAME,ASTmgrSECRET,login_user,login_pass,login_campaign,park_on_extension,conf_on_extension,VICIDIAL_park_on_extension,VICIDIAL_park_on_filename,monitor_prefix,recording_exten,voicemail_exten,voicemail_dump_exten,ext_context,dtmf_send_extension,call_out_number_group,client_browser,install_directory,local_web_callerID_URL,VICIDIAL_web_URL,AGI_call_logging_enabled,user_switching_enabled,conferencing_enabled,admin_hangup_enabled,admin_hijack_enabled,admin_monitor_enabled,call_parking_enabled,updater_check_enabled,AFLogging_enabled,QUEUE_ACTION_enabled,CallerID_popup_enabled,voicemail_button_enabled,enable_fast_refresh,fast_refresh_rate,enable_persistant_mysql,auto_dial_next_number,VDstop_rec_after_each_call,DBX_server,DBX_database,DBX_user,DBX_pass,DBX_port,DBY_server,DBY_database,DBY_user,DBY_pass,DBY_port,outbound_cid,enable_sipsak_messages,email,template_id,conf_override,phone_context,phone_ring_timeout,conf_secret,is_webphone,use_external_server_ip,codecs_list,webphone_dialpad,phone_ring_timeout,on_hook_agent,webphone_auto_answer from phones where login='$phone_login' and pass='$phone_pass' and active = 'Y';";
		if ($DB) {echo "|$stmt|\n";}
		$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'01025',$VD_login,$server_ip,$session_name,$one_mysql_log);}
		$row=mysql_fetch_row($rslt);
		$extension=$row[0];
		$dialplan_number=$row[1];
		$voicemail_id=$row[2];
		$phone_ip=$row[3];
		$computer_ip=$row[4];
		$server_ip=$row[5];
		$login=$row[6];
		$pass=$row[7];
		$status=$row[8];
		$active=$row[9];
		$phone_type=$row[10];
		$fullname=$row[11];
		$company=$row[12];
		$picture=$row[13];
		$messages=$row[14];
		$old_messages=$row[15];
		$protocol=$row[16];
		$local_gmt=$row[17];
		$ASTmgrUSERNAME=$row[18];
		$ASTmgrSECRET=$row[19];
		$login_user=$row[20];
		$login_pass=$row[21];
		$login_campaign=$row[22];
		$park_on_extension=$row[23];
		$conf_on_extension=$row[24];
		$VICIDiaL_park_on_extension=$row[25];
		$VICIDiaL_park_on_filename=$row[26];
		$monitor_prefix=$row[27];
		$recording_exten=$row[28];
		$voicemail_exten=$row[29];
		$voicemail_dump_exten=$row[30];
		$ext_context=$row[31];
		$dtmf_send_extension=$row[32];
		$call_out_number_group=$row[33];
		$client_browser=$row[34];
		$install_directory=$row[35];
		$local_web_callerID_URL=$row[36];
		$VICIDiaL_web_URL=$row[37];
		$AGI_call_logging_enabled=$row[38];
		$user_switching_enabled=$row[39];
		$conferencing_enabled=$row[40];
		$admin_hangup_enabled=$row[41];
		$admin_hijack_enabled=$row[42];
		$admin_monitor_enabled=$row[43];
		$call_parking_enabled=$row[44];
		$updater_check_enabled=$row[45];
		$AFLogging_enabled=$row[46];
		$QUEUE_ACTION_enabled=$row[47];
		$CallerID_popup_enabled=$row[48];
		$voicemail_button_enabled=$row[49];
		$enable_fast_refresh=$row[50];
		$fast_refresh_rate=$row[51];
		$enable_persistant_mysql=$row[52];
		$auto_dial_next_number=$row[53];
		$VDstop_rec_after_each_call=$row[54];
		$DBX_server=$row[55];
		$DBX_database=$row[56];
		$DBX_user=$row[57];
		$DBX_pass=$row[58];
		$DBX_port=$row[59];
		$outbound_cid=$row[65];
		$enable_sipsak_messages=$row[66];
		$conf_secret=$row[72];
		$is_webphone=$row[73];
		$use_external_server_ip=$row[74];
		$codecs_list=$row[75];
		$webphone_dialpad=$row[76];
		$phone_ring_timeout=$row[77];
		$on_hook_agent=$row[78];
		$webphone_auto_answer=$row[79];

		$no_empty_session_warnings=0;
		if ( ($phone_login == 'nophone') or ($on_hook_agent == 'Y') )
			{
			$no_empty_session_warnings=1;
			}
		if ($PhonESComPIP == '1')
			{
			if (strlen($computer_ip) < 4)
				{
				$stmt="UPDATE phones SET computer_ip='$ip' where login='$phone_login' and pass='$phone_pass' and active = 'Y';";
				if ($DB) {echo "|$stmt|\n";}
				$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'01026',$VD_login,$server_ip,$session_name,$one_mysql_log);}
				}
			}
		if ($PhonESComPIP == '2')
			{
			$stmt="UPDATE phones SET computer_ip='$ip' where login='$phone_login' and pass='$phone_pass' and active = 'Y';";
			if ($DB) {echo "|$stmt|\n";}
			$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'01027',$VD_login,$server_ip,$session_name,$one_mysql_log);}
			}
		if ($clientDST)
			{
			$local_gmt = ($local_gmt + $isdst);
			}
		if ($protocol == 'EXTERNAL')
			{
			$protocol = 'Local';
			$extension = "$dialplan_number$AT$ext_context";
			}
		$SIP_user = "$protocol/$extension";
		$SIP_user_DiaL = "$protocol/$extension";
		if ( (ereg('8300',$dialplan_number)) and (strlen($dialplan_number)<5) and ($protocol == 'Local') )
			{
			$SIP_user = "$protocol/$extension$VD_login";
			}

		$stmt="SELECT asterisk_version from servers where server_ip='$server_ip';";
		if ($DB) {echo "|$stmt|\n";}
		$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'01028',$VD_login,$server_ip,$session_name,$one_mysql_log);}
		$row=mysql_fetch_row($rslt);
		$asterisk_version=$row[0];

		# If a park extension is not set, use the default one
		if ( (strlen($park_ext)>0) && (strlen($park_file_name)>0) )
			{
			$VICIDiaL_park_on_extension = "$park_ext";
			$VICIDiaL_park_on_filename = "$park_file_name";
			echo "<!-- CAMPAIGN CUSTOM PARKING:  |$VICIDiaL_park_on_extension|$VICIDiaL_park_on_filename| -->\n";
			}
		echo "<!-- CAMPAIGN DEFAULT PARKING: |$VICIDiaL_park_on_extension|$VICIDiaL_park_on_filename| -->\n";

		# If a web form address is not set, use the default one
		if (strlen($web_form_address)>0)
			{
			$VICIDiaL_web_form_address = "$web_form_address";
			echo "<!-- CAMPAIGN CUSTOM WEB FORM:   |$VICIDiaL_web_form_address| -->\n";
			}
		else
			{
			$VICIDiaL_web_form_address = "$VICIDiaL_web_URL";
			print "<!-- CAMPAIGN DEFAULT WEB FORM:  |$VICIDiaL_web_form_address| -->\n";
			$VICIDiaL_web_form_address_enc = rawurlencode($VICIDiaL_web_form_address);
			}
		$VICIDiaL_web_form_address_enc = rawurlencode($VICIDiaL_web_form_address);

		# If a web form address two is not set, use the first one
		if (strlen($web_form_address_two)>0)
			{
			$VICIDiaL_web_form_address_two = "$web_form_address_two";
			echo "<!-- CAMPAIGN CUSTOM WEB FORM 2:   |$VICIDiaL_web_form_address_two| -->\n";
			}
		else
			{
			$VICIDiaL_web_form_address_two = "$VICIDiaL_web_form_address";
			echo "<!-- CAMPAIGN DEFAULT WEB FORM 2:  |$VICIDiaL_web_form_address_two| -->\n";
			$VICIDiaL_web_form_address_two_enc = rawurlencode($VICIDiaL_web_form_address_two);
			}
		$VICIDiaL_web_form_address_two_enc = rawurlencode($VICIDiaL_web_form_address_two);

		# If closers are allowed on this campaign
		if ($allow_closers=="Y")
			{
			$VICIDiaL_allow_closers = 1;
			echo "<!-- CAMPAIGN ALLOWS CLOSERS:    |$VICIDiaL_allow_closers| -->\n";
			}
		else
			{
			$VICIDiaL_allow_closers = 0;
			echo "<!-- CAMPAIGN ALLOWS NO CLOSERS: |$VICIDiaL_allow_closers| -->\n";
			}


		$session_ext = eregi_replace("[^a-z0-9]", "", $extension);
		if (strlen($session_ext) > 10) {$session_ext = substr($session_ext, 0, 10);}
		$session_rand = (rand(1,9999999) + 10000000);
		$session_name = "$StarTtimE$US$session_ext$session_rand";

		if ($webform_sessionname)
			{$webform_sessionname = "&session_name=$session_name";}
		else
			{$webform_sessionname = '';}

		$stmt="DELETE from web_client_sessions where start_time < '$past_month_date' and extension='$extension' and server_ip = '$server_ip' and program = 'vicidial';";
		if ($DB) {echo "|$stmt|\n";}
		$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'01029',$VD_login,$server_ip,$session_name,$one_mysql_log);}

		$stmt="INSERT INTO web_client_sessions values('$extension','$server_ip','vicidial','$NOW_TIME','$session_name');";
		if ($DB) {echo "|$stmt|\n";}
		$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'01030',$VD_login,$server_ip,$session_name,$one_mysql_log);}

		if ( ( ($campaign_allow_inbound == 'Y') and ($dial_method != 'MANUAL') ) || ($campaign_leads_to_call > 0) || (ereg('Y',$no_hopper_leads_logins)) )
			{
			##### check to see if the user has a conf extension already, this happens if they previously exited uncleanly
			$stmt="SELECT conf_exten FROM vicidial_conferences where extension='$SIP_user' and server_ip = '$server_ip' LIMIT 1;";
			$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'01032',$VD_login,$server_ip,$session_name,$one_mysql_log);}
			if ($DB) {echo "$stmt\n";}
			$prev_login_ct = mysql_num_rows($rslt);
			$i=0;
			while ($i < $prev_login_ct)
				{
				$row=mysql_fetch_row($rslt);
				$session_id =$row[0];
				$i++;
				}
			if ($prev_login_ct > 0)
				{echo "<!-- USING PREVIOUS MEETME ROOM - $session_id - $NOW_TIME - $SIP_user -->\n";}
			else
				{
				##### grab the next available vicidial_conference room and reserve it
				$stmt="SELECT count(*) FROM vicidial_conferences where server_ip='$server_ip' and ((extension='') or (extension is null));";
				if ($DB) {echo "$stmt\n";}
				$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'01033',$VD_login,$server_ip,$session_name,$one_mysql_log);}
				$row=mysql_fetch_row($rslt);
				if ($row[0] > 0)
					{
					$stmt="UPDATE vicidial_conferences set extension='$SIP_user', leave_3way='0' where server_ip='$server_ip' and ((extension='') or (extension is null)) limit 1;";
						if ($format=='debug') {echo "\n<!-- $stmt -->";}
					$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'01034',$VD_login,$server_ip,$session_name,$one_mysql_log);}

					$stmt="SELECT conf_exten from vicidial_conferences where server_ip='$server_ip' and ( (extension='$SIP_user') or (extension='$VD_login') );";
						if ($format=='debug') {echo "\n<!-- $stmt -->";}
					$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'01035',$VD_login,$server_ip,$session_name,$one_mysql_log);}
					$row=mysql_fetch_row($rslt);
					$session_id = $row[0];
					}
				echo "<!-- USING NEW MEETME ROOM - $session_id - $NOW_TIME - $SIP_user -->\n";
				}

			### mark leads that were not dispositioned during previous calls as ERI
			$stmt="UPDATE vicidial_list set status='ERI', user='' where status IN('QUEUE','INCALL') and user ='$VD_login';";
			if ($DB) {echo "$stmt\n";}
			$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'01036',$VD_login,$server_ip,$session_name,$one_mysql_log);}
			$vlERIaffected_rows = mysql_affected_rows($link);
			echo "<!-- old QUEUE and INCALL reverted list:   |$vlERIaffected_rows| -->\n";

			$stmt="DELETE from vicidial_hopper where status IN('QUEUE','INCALL','DONE') and user ='$VD_login';";
			if ($DB) {echo "$stmt\n";}
			$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'01037',$VD_login,$server_ip,$session_name,$one_mysql_log);}
			$vhICaffected_rows = mysql_affected_rows($link);
			echo "<!-- old QUEUE and INCALL reverted hopper: |$vhICaffected_rows| -->\n";

			$stmt="DELETE from vicidial_live_agents where user ='$VD_login';";
			if ($DB) {echo "$stmt\n";}
			$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'01038',$VD_login,$server_ip,$session_name,$one_mysql_log);}
			$vlaLIaffected_rows = mysql_affected_rows($link);
			echo "<!-- old vicidial_live_agents records cleared: |$vlaLIaffected_rows| -->\n";

			$stmt="DELETE from vicidial_live_inbound_agents where user ='$VD_login';";
			if ($DB) {echo "$stmt\n";}
			$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'01039',$VD_login,$server_ip,$session_name,$one_mysql_log);}
			$vliaLIaffected_rows = mysql_affected_rows($link);
			echo "<!-- old vicidial_live_inbound_agents records cleared: |$vliaLIaffected_rows| -->\n";

			### insert an entry into the user log for the login event
			$vul_data = "$vlERIaffected_rows|$vhICaffected_rows|$vlaLIaffected_rows|$vliaLIaffected_rows";
			$stmt = "INSERT INTO vicidial_user_log (user,event,campaign_id,event_date,event_epoch,user_group,session_id,server_ip,extension,computer_ip,browser,data) values('$VD_login','LOGIN','$VD_campaign','$NOW_TIME','$StarTtimE','$VU_user_group','$session_id','$server_ip','$protocol/$extension','$ip','$browser','$vul_data')";
			if ($DB) {echo "|$stmt|\n";}
			$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'01031',$VD_login,$server_ip,$session_name,$one_mysql_log);}

        #   echo "<b>You have logged in as user: $VD_login on phone: $SIP_user to campaign: $VD_campaign</b><br />\n";
			$VICIDiaL_is_logged_in=1;

			### set the callerID for manager middleware-app to connect the phone to the user
			$SIqueryCID = "S$CIDdate$session_id";

			#############################################
			##### START SYSTEM_SETTINGS LOOKUP #####
			$stmt = "SELECT enable_queuemetrics_logging,queuemetrics_server_ip,queuemetrics_dbname,queuemetrics_login,queuemetrics_pass,queuemetrics_log_id,vicidial_agent_disable,allow_sipsak_messages,queuemetrics_loginout,queuemetrics_addmember_enabled FROM system_settings;";
			$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'01040',$VD_login,$server_ip,$session_name,$one_mysql_log);}
			if ($DB) {echo "$stmt\n";}
			$qm_conf_ct = mysql_num_rows($rslt);
			if ($qm_conf_ct > 0)
				{
				$row=mysql_fetch_row($rslt);
				$enable_queuemetrics_logging =		$row[0];
				$queuemetrics_server_ip	=			$row[1];
				$queuemetrics_dbname =				$row[2];
				$queuemetrics_login	=				$row[3];
				$queuemetrics_pass =				$row[4];
				$queuemetrics_log_id =				$row[5];
				$vicidial_agent_disable =			$row[6];
				$allow_sipsak_messages =			$row[7];
				$queuemetrics_loginout =			$row[8];
				$queuemetrics_addmember_enabled =	$row[9];
				}
			##### END QUEUEMETRICS LOGGING LOOKUP #####
			###########################################

			if ( ($enable_sipsak_messages > 0) and ($allow_sipsak_messages > 0) and (eregi("SIP",$protocol)) )
				{
				$SIPSAK_prefix = 'LIN-';
				echo "<!-- sending login sipsak message: $SIPSAK_prefix$VD_campaign -->\n";
				passthru("/usr/local/bin/sipsak -M -O desktop -B \"$SIPSAK_prefix$VD_campaign\" -r 5060 -s sip:$extension@$phone_ip > /dev/null");
				$SIqueryCID = "$SIPSAK_prefix$VD_campaign$DS$CIDdate";
				}

			$webphone_content='';
			if ($is_webphone != 'Y')
				{
				$TEMP_SIP_user_DiaL = $SIP_user_DiaL;
				if ($on_hook_agent == 'Y')
					{$TEMP_SIP_user_DiaL = 'Local/8300@default';}
				### insert a NEW record to the vicidial_manager table to be processed
				$stmt="INSERT INTO vicidial_manager values('','','$NOW_TIME','NEW','N','$server_ip','','Originate','$SIqueryCID','Channel: $TEMP_SIP_user_DiaL','Context: $ext_context','Exten: $session_id','Priority: 1','Callerid: \"$SIqueryCID\" <$campaign_cid>','','','','','');";
				if ($DB) {echo "$stmt\n";}
				$rslt=mysql_query($stmt, $link);
					if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'01041',$VD_login,$server_ip,$session_name,$one_mysql_log);}
				$affected_rows = mysql_affected_rows($link);
				echo "<!-- call placed to session_id: $session_id from phone: $SIP_user $SIP_user_DiaL -->\n";
				}
			else
				{
				### build Iframe variable content for webphone here
				$codecs_list = preg_replace("/ /",'',$codecs_list);
				$codecs_list = preg_replace("/-/",'',$codecs_list);
				$codecs_list = preg_replace("/&/",'',$codecs_list);
				$webphone_server_ip = $server_ip;
				if ($use_external_server_ip=='Y')
					{
					##### find external_server_ip if enabled for this phone account
					$stmt="SELECT external_server_ip FROM servers where server_ip='$server_ip' LIMIT 1;";
					$rslt=mysql_query($stmt, $link);
						if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'01065',$VD_login,$server_ip,$session_name,$one_mysql_log);}
					if ($DB) {echo "$stmt\n";}
					$exip_ct = mysql_num_rows($rslt);
					if ($exip_ct > 0)
						{
						$row=mysql_fetch_row($rslt);
						$webphone_server_ip =$row[0];
						}
					}
				if (strlen($webphone_url) < 6)
					{
					##### find webphone_url in system_settings and generate IFRAME code for it #####
					$stmt="SELECT webphone_url FROM system_settings LIMIT 1;";
					$rslt=mysql_query($stmt, $link);
						if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'01066',$VD_login,$server_ip,$session_name,$one_mysql_log);}
					if ($DB) {echo "$stmt\n";}
					$wu_ct = mysql_num_rows($rslt);
					if ($wu_ct > 0)
						{
						$row=mysql_fetch_row($rslt);
						$webphone_url =$row[0];
						}
					}
				if (strlen($system_key) < 1)
					{
					##### find system_key in system_settings if populated #####
					$stmt="SELECT webphone_systemkey FROM system_settings LIMIT 1;";
					$rslt=mysql_query($stmt, $link);
						if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'01068',$VD_login,$server_ip,$session_name,$one_mysql_log);}
					if ($DB) {echo "$stmt\n";}
					$wsk_ct = mysql_num_rows($rslt);
					if ($wsk_ct > 0)
						{
						$row=mysql_fetch_row($rslt);
						$system_key =$row[0];
						}
					}
				$webphone_options='INITIAL_LOAD';
				if ($webphone_dialpad == 'Y') {$webphone_options .= "--DIALPAD_Y";}
				if ($webphone_dialpad == 'N') {$webphone_options .= "--DIALPAD_N";}
				if ($webphone_dialpad == 'TOGGLE') {$webphone_options .= "--DIALPAD_TOGGLE";}
				if ($webphone_dialpad == 'TOGGLE_OFF') {$webphone_options .= "--DIALPAD_OFF_TOGGLE";}
				if ($webphone_auto_answer == 'Y') {$webphone_options .= "--AUTOANSWER_Y";}
				if ($webphone_auto_answer == 'N') {$webphone_options .= "--AUTOANSWER_N";}

				### base64 encode variables
				$b64_phone_login =		base64_encode($extension);
				$b64_phone_pass =		base64_encode($conf_secret);
				$b64_session_name =		base64_encode($session_name);
				$b64_server_ip =		base64_encode($webphone_server_ip);
				$b64_callerid =			base64_encode($outbound_cid);
				$b64_protocol =			base64_encode($protocol);
				$b64_codecs =			base64_encode($codecs_list);
				$b64_options =			base64_encode($webphone_options);
				$b64_system_key =		base64_encode($system_key);

				$WebPhonEurl = "$webphone_url?phone_login=$b64_phone_login&phone_login=$b64_phone_login&phone_pass=$b64_phone_pass&server_ip=$b64_server_ip&callerid=$b64_callerid&protocol=$b64_protocol&codecs=$b64_codecs&options=$b64_options&system_key=$b64_system_key";
				if ($webphone_location == 'bar')
					{
					$webphone_content = "<iframe src=\"$WebPhonEurl\" style=\"width:" . $webphone_width . "px;height:" . $webphone_height . "px;background-color:transparent;z-index:17;\" scrolling=\"no\" frameborder=\"0\" allowtransparency=\"true\" id=\"webphone\" name=\"webphone\" width=\"" . $webphone_width . "px\" height=\"" . $webphone_height . "px\"> </iframe>";
					}
				else
					{
					$webphone_content = "<iframe src=\"$WebPhonEurl\" style=\"width:" . $webphone_width . "px;height:" . $webphone_height . "px;background-color:transparent;z-index:17;\" scrolling=\"auto\" frameborder=\"0\" allowtransparency=\"true\" id=\"webphone\" name=\"webphone\" width=\"" . $webphone_width . "px\" height=\"" . $webphone_height . "px\"> </iframe>";
					}
				}

			##### grab the campaign_weight and number of calls today on that campaign for the agent
			$stmt="SELECT campaign_weight,calls_today FROM vicidial_campaign_agents where user='$VD_login' and campaign_id = '$VD_campaign';";
			$rslt=mysql_query($stmt, $link);
			if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'01042',$VD_login,$server_ip,$session_name,$one_mysql_log);}
			if ($DB) {echo "$stmt\n";}
			$vca_ct = mysql_num_rows($rslt);
			if ($vca_ct > 0)
				{
				$row=mysql_fetch_row($rslt);
				$campaign_weight =	$row[0];
				$calls_today =		$row[1];
				$i++;
				}
			else
				{
				$campaign_weight =	'0';
				$calls_today =		'0';
				$stmt="INSERT INTO vicidial_campaign_agents (user,campaign_id,campaign_rank,campaign_weight,calls_today) values('$VD_login','$VD_campaign','0','0','$calls_today');";
				if ($DB) {echo "$stmt\n";}
				$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'01043',$VD_login,$server_ip,$session_name,$one_mysql_log);}
				$affected_rows = mysql_affected_rows($link);
				echo "<!-- new vicidial_campaign_agents record inserted: |$affected_rows| -->\n";
				}

			if ($auto_dial_level > 0)
				{
				echo "<!-- campaign is set to auto_dial_level: $auto_dial_level -->\n";

				$closer_chooser_string='';
				$stmt="INSERT INTO vicidial_live_agents (user,server_ip,conf_exten,extension,status,lead_id,campaign_id,uniqueid,callerid,channel,random_id,last_call_time,last_update_time,last_call_finish,closer_campaigns,user_level,campaign_weight,calls_today,last_state_change,outbound_autodial,manager_ingroup_set,on_hook_ring_time,on_hook_agent) values('$VD_login','$server_ip','$session_id','$SIP_user','PAUSED','','$VD_campaign','','','','$random','$NOW_TIME','$tsNOW_TIME','$NOW_TIME','$closer_chooser_string','$user_level','$campaign_weight','$calls_today','$NOW_TIME','Y','N','$phone_ring_timeout','$on_hook_agent');";
				if ($DB) {echo "$stmt\n";}
				$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'01044',$VD_login,$server_ip,$session_name,$one_mysql_log);}
				$affected_rows = mysql_affected_rows($link);
				echo "<!-- new vicidial_live_agents record inserted: |$affected_rows| -->\n";

				if ($enable_queuemetrics_logging > 0)
					{
					$QM_LOGIN = 'AGENTLOGIN';
					$QM_PHONE = "$VD_login@agents";
					if ( ($queuemetrics_loginout=='CALLBACK') or ($queuemetrics_loginout=='NONE') )
						{
						$QM_LOGIN = 'AGENTCALLBACKLOGIN';
						$QM_PHONE = "$SIP_user_DiaL";
						}
					$linkB=mysql_connect("$queuemetrics_server_ip", "$queuemetrics_login", "$queuemetrics_pass");
					mysql_select_db("$queuemetrics_dbname", $linkB);

					if ($queuemetrics_loginout!='NONE')
						{
						$stmt = "INSERT INTO queue_log SET partition='P01',time_id='$StarTtimE',call_id='NONE',queue='NONE',agent='Agent/$VD_login',verb='$QM_LOGIN',data1='$QM_PHONE',serverid='$queuemetrics_log_id',data4='$qm_phone_environment';";
						if ($DB) {echo "$stmt\n";}
						$rslt=mysql_query($stmt, $linkB);
						if ($mel > 0) {mysql_error_logging($NOW_TIME,$linkB,$mel,$stmt,'01045',$VD_login,$server_ip,$session_name,$one_mysql_log);}
						$affected_rows = mysql_affected_rows($linkB);
						echo "<!-- queue_log $QM_LOGIN entry added: $VD_login|$affected_rows|$QM_PHONE -->\n";
						}

					$stmt = "INSERT INTO queue_log SET partition='P01',time_id='$StarTtimE',call_id='NONE',queue='NONE',agent='Agent/$VD_login',verb='PAUSEALL',serverid='$queuemetrics_log_id',data4='$qm_phone_environment';";
					if ($DB) {echo "$stmt\n";}
					$rslt=mysql_query($stmt, $linkB);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$linkB,$mel,$stmt,'01046',$VD_login,$server_ip,$session_name,$one_mysql_log);}
					$affected_rows = mysql_affected_rows($linkB);
					echo "<!-- queue_log PAUSE entry added: $VD_login|$affected_rows -->\n";

					if ($queuemetrics_addmember_enabled > 0)
						{
						$stmt = "INSERT INTO queue_log SET partition='P01',time_id='$StarTtimE',call_id='NONE',queue='$VD_campaign',agent='Agent/$VD_login',verb='ADDMEMBER2',data1='$QM_PHONE',serverid='$queuemetrics_log_id',data4='$qm_phone_environment';";
						if ($DB) {echo "$stmt\n";}
						$rslt=mysql_query($stmt, $linkB);
					if ($mel > 0) {mysql_error_logging($NOW_TIME,$linkB,$mel,$stmt,'01069',$VD_login,$server_ip,$session_name,$one_mysql_log);}
						$affected_rows = mysql_affected_rows($linkB);
						echo "<!-- queue_log ADDMEMBER2 entry added: $VD_login|$affected_rows -->\n";
						}

					mysql_close($linkB);
					mysql_select_db("$VARDB_database", $link);
					}


				if ( ($campaign_allow_inbound == 'Y') and ($dial_method != 'MANUAL') )
					{
					print "<!-- CLOSER-type campaign -->\n";
					}
				}
			else
				{
				print "<!-- campaign is set to manual dial: $auto_dial_level -->\n";

				$stmt="INSERT INTO vicidial_live_agents (user,server_ip,conf_exten,extension,status,lead_id,campaign_id,uniqueid,callerid,channel,random_id,last_call_time,last_update_time,last_call_finish,user_level,campaign_weight,calls_today,last_state_change,outbound_autodial,manager_ingroup_set,on_hook_ring_time,on_hook_agent) values('$VD_login','$server_ip','$session_id','$SIP_user','PAUSED','','$VD_campaign','','','','$random','$NOW_TIME','$tsNOW_TIME','$NOW_TIME','$user_level', '$campaign_weight', '$calls_today','$NOW_TIME','N','N','$phone_ring_timeout','$on_hook_agent');";
				if ($DB) {echo "$stmt\n";}
				$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'01047',$VD_login,$server_ip,$session_name,$one_mysql_log);}
				$affected_rows = mysql_affected_rows($link);
				echo "<!-- new vicidial_live_agents record inserted: |$affected_rows| -->\n";

				if ($enable_queuemetrics_logging > 0)
					{
					$QM_LOGIN = 'AGENTLOGIN';
					$QM_PHONE = "$VD_login@agents";
					if ( ($queuemetrics_loginout=='CALLBACK') or ($queuemetrics_loginout=='NONE') )
						{
						$QM_LOGIN = 'AGENTCALLBACKLOGIN';
						$QM_PHONE = "$SIP_user_DiaL";
						}
					$linkB=mysql_connect("$queuemetrics_server_ip", "$queuemetrics_login", "$queuemetrics_pass");
					mysql_select_db("$queuemetrics_dbname", $linkB);

					if ($queuemetrics_loginout!='NONE')
						{
						$stmt = "INSERT INTO queue_log SET partition='P01',time_id='$StarTtimE',call_id='NONE',queue='$VD_campaign',agent='Agent/$VD_login',verb='$QM_LOGIN',data1='$QM_PHONE',serverid='$queuemetrics_log_id',data4='$qm_phone_environment';";
						if ($DB) {echo "$stmt\n";}
						$rslt=mysql_query($stmt, $linkB);
						if ($mel > 0) {mysql_error_logging($NOW_TIME,$linkB,$mel,$stmt,'01048',$VD_login,$server_ip,$session_name,$one_mysql_log);}
						$affected_rows = mysql_affected_rows($linkB);
						echo "<!-- queue_log $QM_LOGIN entry added: $VD_login|$affected_rows|$QM_PHONE -->\n";
						}

					$stmt = "INSERT INTO queue_log SET partition='P01',time_id='$StarTtimE',call_id='NONE',queue='NONE',agent='Agent/$VD_login',verb='PAUSEALL',serverid='$queuemetrics_log_id',data4='$qm_phone_environment';";
					if ($DB) {echo "$stmt\n";}
					$rslt=mysql_query($stmt, $linkB);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$linkB,$mel,$stmt,'01049',$VD_login,$server_ip,$session_name,$one_mysql_log);}
					$affected_rows = mysql_affected_rows($linkB);
					echo "<!-- queue_log PAUSE entry added: $VD_login|$affected_rows -->\n";

					if ($queuemetrics_addmember_enabled > 0)
						{
						$stmt = "INSERT INTO queue_log SET partition='P01',time_id='$StarTtimE',call_id='NONE',queue='$VD_campaign',agent='Agent/$VD_login',verb='ADDMEMBER2',data1='$QM_PHONE',serverid='$queuemetrics_log_id',data4='$qm_phone_environment';";
						if ($DB) {echo "$stmt\n";}
						$rslt=mysql_query($stmt, $linkB);
					if ($mel > 0) {mysql_error_logging($NOW_TIME,$linkB,$mel,$stmt,'01072',$VD_login,$server_ip,$session_name,$one_mysql_log);}
						$affected_rows = mysql_affected_rows($linkB);
						echo "<!-- queue_log ADDMEMBER2 entry added: $VD_login|$affected_rows -->\n";
						}

					mysql_close($linkB);
					mysql_select_db("$VARDB_database", $link);
					}
				}
			}
		else
			{
			echo "<title>Agent web client: Campaign Login</title>\n";
			echo "</head>\n";
            echo "<body onresize=\"browser_dimensions();\" onload=\"browser_dimensions();\">\n";
			if ($hide_timeclock_link < 1)
                {echo "<a href=\"./timeclock.php?referrer=agent&amp;pl=$phone_login&amp;pp=$phone_pass&amp;VD_login=$VD_login&amp;VD_pass=$VD_pass\"> Timeclock</a><br />\n";}
            echo "<table width=\"100%\"><tr><td></td>\n";
			echo "<!-- INTERNATIONALIZATION-LINKS-PLACEHOLDER-VICIDIAL -->\n";
            echo "</tr></table>\n";
            echo "<b>Sorry, there are no leads in the hopper for this campaign</b>\n";
            echo "<form action=\"$PHP_SELF\" method=\"post\">\n";
            echo "<input type=\"hidden\" name=\"DB\" value=\"$DB\" />\n";
            echo "<input type=\"hidden\" name=\"JS_browser_height\" id=\"JS_browser_height\" value=\"\" />\n";
            echo "<input type=\"hidden\" name=\"JS_browser_width\" id=\"JS_browser_width\" value=\"\" />\n";
            echo "<input type=\"hidden\" name=\"phone_login\" value=\"$phone_login\" />\n";
            echo "<input type=\"hidden\" name=\"phone_pass\" value=\"$phone_pass\" />\n";
            echo "Login: <input type=\"text\" name=\"VD_login\" size=\"10\" maxlength=\"20\" value=\"$VD_login\" />\n<br />";
            echo "Password: <input type=\"password\" name=\"VD_pass\" size=\"10\" maxlength=\"20\" value=\"$VD_pass\" /><br />\n";
            echo "Campaign: <span id=\"LogiNCamPaigns\">$camp_form_code</span><br />\n";
            echo "<input type=\"submit\" name=\"SUBMIT\" value=\"submit\" /> &nbsp; \n";
			echo "<span id=\"LogiNReseT\"></span>\n";
            echo "</form>\n\n";
			echo "</body>\n\n";
			echo "</html>\n\n";
			exit;
			}
		if (strlen($session_id) < 1)
			{
			echo "<title>Agent web client: Campaign Login</title>\n";
			echo "</head>\n";
            echo "<body onresize=\"browser_dimensions();\" onload=\"browser_dimensions();\">\n";
			if ($hide_timeclock_link < 1)
                {echo "<a href=\"./timeclock.php?referrer=agent&amp;pl=$phone_login&amp;pp=$phone_pass&amp;VD_login=$VD_login&amp;VD_pass=$VD_pass\"> Timeclock</a><br />\n";}
            echo "<table width=\"100%\"><tr><td></td>\n";
			echo "<!-- INTERNATIONALIZATION-LINKS-PLACEHOLDER-VICIDIAL -->\n";
            echo "</tr></table>\n";
            echo "<b>Sorry, there are no available sessions</b>\n";
            echo "<form action=\"$PHP_SELF\" method=\"post\" />\n";
            echo "<input type=\"hidden\" name=\"DB\" value=\"$DB\" />\n";
            echo "<input type=\"hidden\" name=\"JS_browser_height\" id=\"JS_browser_height\" value=\"\" />\n";
            echo "<input type=\"hidden\" name=\"JS_browser_width\" id=\"JS_browser_width\" value=\"\" />\n";
            echo "<input type=\"hidden\" name=\"phone_login\" value=\"$phone_login\" />\n";
            echo "<input type=\"hidden\" name=\"phone_pass\" value=\"$phone_pass\" />\n";
            echo "Login: <input type=\"text\" name=\"VD_login\" size=\"10\" maxlength=\"20\" value=\"$VD_login\" />\n<br />";
            echo "Password: <input type=\"password\" name=\"VD_pass\" size=\"10\" maxlength=\"20\" value=\"$VD_pass\" /><br />\n";
            echo "Campaign: <span id=\"LogiNCamPaigns\">$camp_form_code</span><br />\n";
            echo "<input type=\"submit\" name=\"SUBMIT\" value=\"Submit\" /> &nbsp; \n";
			echo "<span id=\"LogiNReseT\"></span>\n";
			echo "</FORM>\n\n";
			echo "</body>\n\n";
			echo "</html>\n\n";
			exit;
			}

		if (ereg('MSIE',$browser)) 
			{
			$useIE=1;
			echo "<!-- client web browser used: MSIE |$browser|$useIE| -->\n";
			}
		else 
			{
			$useIE=0;
			echo "<!-- client web browser used: W3C-Compliant |$browser|$useIE| -->\n";
			}

		$StarTtimE = date("U");
		$NOW_TIME = date("Y-m-d H:i:s");
		##### Agent is going to log in so insert the vicidial_agent_log entry now
		$stmt="INSERT INTO vicidial_agent_log (user,server_ip,event_time,campaign_id,pause_epoch,pause_sec,wait_epoch,user_group,sub_status) values('$VD_login','$server_ip','$NOW_TIME','$VD_campaign','$StarTtimE','0','$StarTtimE','$VU_user_group','LOGIN');";
		if ($DB) {echo "$stmt\n";}
		$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'01050',$VD_login,$server_ip,$session_name,$one_mysql_log);}
		$affected_rows = mysql_affected_rows($link);
		$agent_log_id = mysql_insert_id($link);
		echo "<!-- vicidial_agent_log record inserted: |$affected_rows|$agent_log_id| -->\n";

		##### update vicidial_campaigns to show agent has logged in
		$stmt="UPDATE vicidial_campaigns set campaign_logindate='$NOW_TIME' where campaign_id='$VD_campaign';";
		if ($DB) {echo "$stmt\n";}
		$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'01064',$VD_login,$server_ip,$session_name,$one_mysql_log);}
		$VCaffected_rows = mysql_affected_rows($link);
		echo "<!-- vicidial_campaigns campaign_logindate updated: |$VCaffected_rows|$NOW_TIME| -->\n";

		if ($enable_queuemetrics_logging > 0)
			{
			$StarTtimEpause = ($StarTtimE + 1);
			$linkB=mysql_connect("$queuemetrics_server_ip", "$queuemetrics_login", "$queuemetrics_pass");
			mysql_select_db("$queuemetrics_dbname", $linkB);

			$stmt = "INSERT INTO queue_log SET partition='P01',time_id='$StarTtimEpause',call_id='NONE',queue='NONE',agent='Agent/$VD_login',verb='PAUSEREASON',data1='LOGIN',data3='$QM_PHONE',serverid='$queuemetrics_log_id';";
			if ($DB) {echo "$stmt\n";}
			$rslt=mysql_query($stmt, $linkB);
		if ($mel > 0) {mysql_error_logging($NOW_TIME,$linkB,$mel,$stmt,'01063',$VD_login,$server_ip,$session_name,$one_mysql_log);}
			$affected_rows = mysql_affected_rows($linkB);
			echo "<!-- queue_log PAUSEREASON LOGIN entry added: $VD_login|$affected_rows|$QM_PHONE -->\n";

			mysql_close($linkB);
			mysql_select_db("$VARDB_database", $link);
			}

		$stmt="UPDATE vicidial_live_agents SET agent_log_id='$agent_log_id' where user='$VD_login';";
		if ($DB) {echo "$stmt\n";}
		$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'01061',$VD_login,$server_ip,$session_name,$one_mysql_log);}
		$VLAaffected_rows_update = mysql_affected_rows($link);

		$stmt="UPDATE vicidial_users SET shift_override_flag='0' where user='$VD_login' and shift_override_flag='1';";
		if ($DB) {echo "$stmt\n";}
		$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'01057',$VD_login,$server_ip,$session_name,$one_mysql_log);}
		$VUaffected_rows = mysql_affected_rows($link);

		$S='*';
		$D_s_ip = explode('.', $server_ip);
		if (strlen($D_s_ip[0])<2) {$D_s_ip[0] = "0$D_s_ip[0]";}
		if (strlen($D_s_ip[0])<3) {$D_s_ip[0] = "0$D_s_ip[0]";}
		if (strlen($D_s_ip[1])<2) {$D_s_ip[1] = "0$D_s_ip[1]";}
		if (strlen($D_s_ip[1])<3) {$D_s_ip[1] = "0$D_s_ip[1]";}
		if (strlen($D_s_ip[2])<2) {$D_s_ip[2] = "0$D_s_ip[2]";}
		if (strlen($D_s_ip[2])<3) {$D_s_ip[2] = "0$D_s_ip[2]";}
		if (strlen($D_s_ip[3])<2) {$D_s_ip[3] = "0$D_s_ip[3]";}
		if (strlen($D_s_ip[3])<3) {$D_s_ip[3] = "0$D_s_ip[3]";}
		$server_ip_dialstring = "$D_s_ip[0]$S$D_s_ip[1]$S$D_s_ip[2]$S$D_s_ip[3]$S";

		##### grab the datails of all active scripts in the system
		$stmt="SELECT script_id,script_name FROM vicidial_scripts WHERE active='Y' order by script_id limit 1000;";
		$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'01051',$VD_login,$server_ip,$session_name,$one_mysql_log);}
		if ($DB) {echo "$stmt\n";}
		$MM_scripts = mysql_num_rows($rslt);
		$e=0;
		while ($e < $MM_scripts)
			{
			$row=mysql_fetch_row($rslt);
			$MMscriptid[$e] =$row[0];
			$MMscriptname[$e] = urlencode($row[1]);
			$MMscriptids = "$MMscriptids'$MMscriptid[$e]',";
			$MMscriptnames = "$MMscriptnames'$MMscriptname[$e]',";
			$e++;
			}
		$MMscriptids = substr("$MMscriptids", 0, -1); 
		$MMscriptnames = substr("$MMscriptnames", 0, -1); 
		}
	}


### SCREEN WIDTH AND HEIGHT CALCULATIONS ###
### DO NOT EDIT! ###
if ($stretch_dimensions > 0)
	{
	if ($agent_status_view < 1)
		{
		if ($JS_browser_width >= 510)
			{$BROWSER_WIDTH = ($JS_browser_width - 80);}
		}
	else
		{
		if ($JS_browser_width >= 730)
			{$BROWSER_WIDTH = ($JS_browser_width - 300);}
		}
	if ($JS_browser_height >= 340)
		{$BROWSER_HEIGHT = ($JS_browser_height - 40);}
	}
if ($agent_fullscreen=='Y')
	{
	$BROWSER_WIDTH = ($JS_browser_width - 10);
	$BROWSER_HEIGHT = $JS_browser_height;
	}
$MASTERwidth=($BROWSER_WIDTH - 340);
$MASTERheight=($BROWSER_HEIGHT - 200);
if ($MASTERwidth < 430) {$MASTERwidth = '430';} 
if ($MASTERheight < 300) {$MASTERheight = '300';} 
if ($webphone_location == 'bar') {$MASTERwidth = ($MASTERwidth + $webphone_height);}

$CAwidth =  ($MASTERwidth + 340);	# 770 - cover all (none-in-session, customer hunngup, etc...)
$SBwidth =	($MASTERwidth + 331);	# 761 - SideBar starting point
$MNwidth =  ($MASTERwidth + 330);	# 760 - main frame
$XFwidth =  ($MASTERwidth + 320);	# 750 - transfer/conference
$HCwidth =  ($MASTERwidth + 310);	# 740 - hotkeys and callbacks
$CQwidth =  ($MASTERwidth + 300);	# 730 - calls in queue listings
$AMwidth =  ($MASTERwidth + 270);	# 700 - refresh links
$SCwidth =  ($MASTERwidth + 230);	# 670 - live call seconds counter, sidebar link
$PDwidth =  ($MASTERwidth + 210);	# 650 - preset-dial links
$MUwidth =  ($MASTERwidth + 180);	# 610 - agent mute
$SSwidth =  ($MASTERwidth + 176);	# 606 - scroll script
$SDwidth =  ($MASTERwidth + 170);	# 600 - scroll script, customer data and calls-in-session
$HKwidth =  ($MASTERwidth + 20);	# 450 - Hotkeys button
$HSwidth =  ($MASTERwidth + 1);		# 431 - Header spacer
$PBwidth =  ($MASTERwidth + 0);		# 430 - Presets list
$CLwidth =  ($MASTERwidth - 120);	# 310 - Calls in queue link


$GHheight =  ($MASTERheight + 1260);# 1560 - Gender Hide span
$DBheight =  ($MASTERheight + 260);	# 560 - Debug span
$WRheight =  ($MASTERheight + 160);	# 460 - Warning boxes
$CQheight =  ($MASTERheight + 140);	# 440 - Calls in queue section
$SLheight =  ($MASTERheight + 122);	# 422 - SideBar link, Agents view link
$QLheight =  ($MASTERheight + 112);	# 412 - Calls in queue link
$HKheight =  ($MASTERheight + 105);	# 405 - HotKey active Button
$AMheight =  ($MASTERheight + 100);	# 400 - Agent mute buttons
$PBheight =  ($MASTERheight + 90);	# 390 - preset dial links
$MBheight =  ($MASTERheight + 65);	# 365 - Manual Dial Buttons
$CBheight =  ($MASTERheight + 50);	# 350 - Agent Callback, pause code, volume control Buttons and agent status
$SSheight =  ($MASTERheight + 31);	# 331 - script content
$HTheight =  ($MASTERheight + 10);	# 310 - transfer frame, callback comments and hotkey
$BPheight =  ($MASTERheight - 250);	# 50 - bottom buffer, Agent Xfer Span
$SCheight =	 49;	# 49 - seconds on call display
$SFheight =	 65;	# 65 - height of the script and form contents
$SRheight =	 69;	# 69 - height of the script and form refrech links
if ($webphone_location == 'bar') 
	{
	$SCheight = ($SCheight + $webphone_height);
#	$SFheight = ($SFheight + $webphone_height);
	$SRheight = ($SRheight + $webphone_height);
	}
$AVTheight = '0';
if ($is_webphone) {$AVTheight = '20';}


################################################################
### BEGIN - build the callback calendar (12 months)          ###
################################################################
define ('ADAY', (60*60*24));
$CdayARY = getdate();
$Cmon = $CdayARY['mon'];
$Cyear = $CdayARY['year'];
$CTODAY = date("Y-m");
$CTODAYmday = date("j");
$CINC=0;
$live_days=0;
$limit_days=999;
if ($callback_days_limit > 0)
	{$limit_days=$callback_days_limit;}

$Cmonths = Array('January','February','March','April','May','June',
				'July','August','September','October','November','December');
$Cdays = Array('Sun','Mon','Tue','Wed','Thu','Fri','Sat');

$CCAL_OUT = '';

$CCAL_OUT .= "<table border=\"0\" cellpadding=\"2\" cellspacing=\"2\">";

while ($CINC < 12)
	{
	if ( ($CINC == 0) || ($CINC == 4) ||($CINC == 8) )
		{$CCAL_OUT .= "<tr>";}

	$CCAL_OUT .= "<td valign=\"top\">";

	$CYyear = $Cyear;
	$Cmonth=	($Cmon + $CINC);
	if ($Cmonth > 12)
		{
		$Cmonth = ($Cmonth - 12);
		$CYyear++;
		}
	$Cstart= mktime(11,0,0,$Cmonth,1,$CYyear);
	$CfirstdayARY = getdate($Cstart);
	#echo "|$Cmon|$Cmonth|$CINC|\n";
	$CPRNTDAY = date("Y-m", $Cstart);

	$CCAL_OUT .= "<table border=\"1\" cellpadding=\"1\" bordercolor=\"000000\" cellspacing=\"0\" bgcolor=\"white\">";
	$CCAL_OUT .= "<tr>";
	$CCAL_OUT .= "<td colspan=\"7\" bordercolor=\"#ffffff\" bgcolor=\"#FFFFCC\">";
	$CCAL_OUT .= "<div align=\"center\"><font color=\"#000066\"><b><font face=\"Arial, Helvetica, sans-serif\" size=\"2\">";
	$CCAL_OUT .= "$CfirstdayARY[month] $CfirstdayARY[year]";
	$CCAL_OUT .= "</font></b></font></div>";
	$CCAL_OUT .= "</td>";
	$CCAL_OUT .= "</tr>";

	foreach($Cdays as $Cday)
		{
		$CDCLR="#ffffff";
		$CCAL_OUT .= "<td bordercolor=\"$CDCLR\">";
		$CCAL_OUT .= "<div align=\"center\"><font color=\"#000066\"><b><font face=\"Arial, Helvetica, sans-serif\" size=\"1\">";
		$CCAL_OUT .= "$Cday";
		$CCAL_OUT .= "</font></b></font></div>";
		$CCAL_OUT .= "</td>";
		}

	for( $Ccount=0;$Ccount<(6*7);$Ccount++)
		{
		$Cdayarray = getdate($Cstart);
		if((($Ccount) % 7) == 0)
			{
			if($Cdayarray['mon'] != $CfirstdayARY['mon'])
				break;
			$CCAL_OUT .= "</tr><tr>";
			}
		if($Ccount < $CfirstdayARY['wday'] || $Cdayarray['mon'] != $Cmonth)
			{
			$CCAL_OUT .= "<td bordercolor=\"#ffffff\"><font color=\"#000066\"><b><font face=\"Arial, Helvetica, sans-serif\" size=\"1\">&nbsp;</font></b></font></td>";
			}
		else
			{
			if( ($Cdayarray['mday'] == $CTODAYmday) and ($CPRNTDAY == $CTODAY) )
				{
				$CPRNTmday = $Cdayarray['mday'];
				if ($CPRNTmday < 10) {$CPRNTmday = "0$CPRNTmday";}
				if ($limit_days > $live_days)
					{
					$CBL = "<a href=\"#\" onclick=\"CB_date_pick('$CPRNTDAY-$CPRNTmday');return false;\">";
					$CEL = "</a>";
					}
				else
					{$CBL='';   $CEL='';}
				$CCAL_OUT .= "<td bgcolor=\"#FFCCCC\" bordercolor=\"#FFCCCC\">";
				$CCAL_OUT .= "<div align=\"center\"><font face=\"Arial, Helvetica, sans-serif\" size=\"1\">";
				$CCAL_OUT .= "$CBL$Cdayarray[mday]$CEL";
				$CCAL_OUT .= "</font></div>";
				$CCAL_OUT .= "</td>";
				$Cstart += ADAY;
				$live_days++;
				}
			else
				{
				$CDCLR="#ffffff";
				if ( ($Cdayarray['mday'] < $CTODAYmday) and ($CPRNTDAY == $CTODAY) )
					{
					$CDCLR="$MAIN_COLOR";
					$CBL = '';
					$CEL = '';
					}
				else
					{
					$CPRNTmday = $Cdayarray['mday'];
					if ($CPRNTmday < 10) {$CPRNTmday = "0$CPRNTmday";}
					if ($limit_days > $live_days)
						{
						$CBL = "<a href=\"#\" onclick=\"CB_date_pick('$CPRNTDAY-$CPRNTmday');return false;\">";
						$CEL = "</a>";
						}
					else
						{$CBL='';   $CEL='';}
					$live_days++;
					}

				$CCAL_OUT .= "<td bgcolor=\"$CDCLR\" bordercolor=\"#ffffff\">";
				$CCAL_OUT .= "<div align=\"center\"><font face=\"Arial, Helvetica, sans-serif\" size=1>";
				$CCAL_OUT .= "$CBL$Cdayarray[mday]$CEL";
				$CCAL_OUT .= "</font></div>";
				$CCAL_OUT .= "</td>";
				$Cstart += ADAY;
				}
			}
		}
	$CCAL_OUT .= "</tr>";
	$CCAL_OUT .= "</table>";
	$CCAL_OUT .= "</td>";

	if ( ($CINC == 3) || ($CINC == 7) ||($CINC == 11) )
		{$CCAL_OUT .= "</tr>";}
	$CINC++;
	}

$CCAL_OUT .= "</table>";

#echo "$CCAL_OUT\n";
################################################################
### END - build the callback calendar (12 months)            ###
################################################################


?>