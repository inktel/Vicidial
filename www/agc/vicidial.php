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
# 111129-1745 - Added park 3rd party call capability
# 111129-1634 - Added number-only filtering for 3rd 'number to call'

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
$phone_pass=$phone_login;

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
$hide_timeclock_link	= '1';	# set to 1 to hide the timeclock link on the agent login screen
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
$label_postal_code =		'Zip Code';
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
$server_addr = getenv("SERVER_ADDR");
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
<!-- <link rel="stylesheet" type="text/css" href="css/style.css" /> -->
<link rel="stylesheet" type="text/css" href="css/custom.css" />
';
echo "<!-- VERSION: $version     BUILD: $build -->\n";
echo "<!-- BROWSER: $BROWSER_WIDTH x $BROWSER_HEIGHT     $JS_browser_width x $JS_browser_height -->\n";

if ($campaign_login_list > 0)
	{
    $camp_form_code  = "<select size=\"1\" name=\"VD_campaign\" id=\"VD_campaign\">\n";
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


	$stmt="SELECT campaign_id,campaign_name from vicidial_campaigns where active='Y' $LOGallowed_campaignsSQL order by campaign_name, campaign_id;";
	if ($non_latin > 0) {$rslt=mysql_query("SET NAMES 'UTF8'");}
	$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'01004',$VD_login,$server_ip,$session_name,$one_mysql_log);}
	$camps_to_print = mysql_num_rows($rslt);

	$o=0;
	while ($camps_to_print > $o) 
		{
		$rowx=mysql_fetch_row($rslt);
		if ($show_campname_pulldown)
			{$campname = "$rowx[1]";}
		else
			{$campname = '';}
		if ($VD_campaign)
			{
			if ( (eregi("$VD_campaign",$rowx[0])) and (strlen($VD_campaign) == strlen($rowx[0])) )
                {$camp_form_code .= "<option value=\"$rowx[0]\" selected=\"selected\">$campname ($rowx[0])</option>\n";}
			else
				{
				if (!ereg('login_allowable_campaigns',$camp_form_code))
					{$camp_form_code .= "<option value=\"$rowx[0]\">$campname ($rowx[0])</option>\n";}
				}
			}
		else
			{
			if (!ereg('login_allowable_campaigns',$camp_form_code))
					{$camp_form_code .= "<option value=\"$rowx[0]\">$campname ($rowx[0])</option>\n";}
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
                        //document.getElementById("LogiNReseT").innerHTML = "<input type=\"button\" value=\"Refresh Campaign List\" onclick=\"login_allowable_campaigns()\" />";
						document.getElementById("LogiNReseT").innerHTML = "<img src=\"./images/Refresh-32.png\" border=\"0\" alt=\"Refresh list of campaigns\" title=\"Refresh list of campaigns\"  onclick=\"login_allowable_campaigns()\" />";
                                                //document.getElementById("VD_campaign").focus();
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

$login_script = "<title>Inktel | X Frontline - Login</title>\n" .
    "</head>\n" .
    "<body onresize=\"browser_dimensions();\" onload=\"browser_dimensions();\">\n";
if ($hide_timeclock_link < 1)
    {
        $login_script .= "<a href=\"./timeclock.php?referrer=agent&amp;pl=$phone_login&amp;pp=$phone_pass&amp;VD_login=$VD_login&amp;VD_pass=$VD_pass\"> Timeclock</a><br />\n";
    }
    
# text watermark helpers - set to default if actual values are empty
$temp_VD_login = $VD_login;
if (strlen($VD_login) <= 0)
    $temp_VD_login = "User Id";
    
$temp_VD_pass = $VD_pass;
if (strlen($temp_VD_pass) <= 0)
    $temp_VD_pass = "Password";
    
$temp_phone_login = $phone_login;
if (strlen($temp_phone_login) <= 0)
    $temp_phone_login = "Extension";

$login_script .= "<table width=\"100%\"><tr><td></td>\n" .
    "<!-- INTERNATIONALIZATION-LINKS-PLACEHOLDER-VICIDIAL -->\n" .
    "</tr></table>\n" .
    "<form name=\"vicidial_form\" id=\"vicidial_form\" action=\"$agcPAGE\" method=\"post\">\n" .
    "<input type=\"hidden\" name=\"DB\" id=\"DB\" value=\"$DB\" />\n" .
    "<input type=\"hidden\" name=\"JS_browser_height\" id=\"JS_browser_height\" value=\"\" />\n" .
    "<input type=\"hidden\" name=\"JS_browser_width\" id=\"JS_browser_width\" value=\"\" />\n" .
    "<input type=\"hidden\" name=\"phone_pass\" id=\"phone_pass\" value=\"$phone_pass\" />\n" .
    
    "<br /><br /><br /><center>\n" .
    "<div id=\"loginBox\" class=\"barStyle\">\n" .
    "<img src=\"./images/InktelX-Frontline-Sponsored.png\" border=\"0\" alt=\"X Frontline\" />" .
    "<h1>Log In</h1>\n" .
    
    "<fieldset id=\"inputs\">" .
    "<input type=\"text\" id=\"VD_login\" name=\"VD_login\" placeholder=\"$temp_VD_login\" size=\"10\" maxlength=\"20\" value=\"$temp_VD_login\" onfocus=\"if(this.value=='User Id')this.value='';\" onblur=\"if(this.value=='')this.value='User Id';\" />\n" .
    "<input type=\"password\" id=\"VD_pass\" name=\"VD_pass\" placeholder=\"$temp_VD_pass\" size=\"10\" maxlength=\"20\" value=\"$temp_VD_pass\" onfocus=\"if(this.value=='Password')this.value='';\" onblur=\"if(this.value=='')this.value='Password';\" />\n" .
    "<input type=\"text\" id=\"phone_login\" name=\"phone_login\" placeholder=\"$temp_phone_login\" size=\"10\" maxlength=\"20\" value=\"$temp_phone_login\" onfocus=\"if(this.value=='Extension')this.value='';\" onblur=\"login_allowable_campaigns();if(this.value=='')this.value='Extension';\" />\n" .
    "<span id=\"LogiNCamPaigns\">$camp_form_code</span><span id=\"LogiNReseT\"><img src=\"./images/Refresh-32.png\" border=\"0\" alt=\"Refresh list of campaigns\" title=\"Refresh list of campaigns\" onclick=\"login_allowable_campaigns()\"/></span>\n" .
    "</fieldset>" .
    
    "<fieldset id=\"actions\">" .
    "<input type=\"submit\" name=\"SUBMIT\" id=\"submit\" class=\"button blue\" value=\"Log In\" /> &nbsp; \n" .
    "<span id=\"instructions\">[UserMessage]</span>" .
    "</fieldset>" .
    
    "</div>" .
    ##"<div id=\"footer\"><span><font size=\"1\"><br />Powered by Vicidial &nbsp; &nbsp; &nbsp; VERSION: $version &nbsp; &nbsp; &nbsp; BUILD: $build</font></span></div>" .
    "</center>\n" .
    "</form>\n\n" .
    "</body>\n\n" .
    "</html>\n\n";

if ($relogin == 'YES')
	{
	echo str_replace("[UserMessage]", "", $login_script);
	exit;
	}

if ($user_login_first == 1)
	{
	if ( (strlen($VD_login)<1) or (strlen($VD_pass)<1) or (strlen($VD_campaign)<1) )
		{
                    echo str_replace("[UserMessage]", "", $login_script);
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
                                    echo str_replace("[UserMessage]", "", $login_script);
                                    exit;
				}
			}
		}
	}

if ( (strlen($phone_login)<2) or (strlen($phone_pass)<2) )
	{
            echo str_replace("[UserMessage]", "", $login_script);
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
                    $VDdisplayMESSAGE = "There are no Shifts enabled for your user group<br />";
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
                        $VDdisplayMESSAGE = "You are not allowed to log in outside of your shift<br />";
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
                                    echo str_replace("[UserMessage]", $LOGfullname . ", you do not have permission to log in to that campaign.", $login_script);
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
				$stmt="SELECT status,status_name,scheduled_callback,selectable FROM vicidial_statuses WHERE status != 'NEW' order by status_name limit 500;";
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
				$stmt="SELECT status,status_name,scheduled_callback,selectable,category FROM vicidial_campaign_statuses WHERE status != 'NEW' and campaign_id='$VD_campaign' order by status_name limit 500;";
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
				$stmt="SELECT park_ext,park_file_name,web_form_address,allow_closers,auto_dial_level,dial_timeout,dial_prefix,campaign_cid,campaign_vdad_exten,campaign_rec_exten,campaign_recording,campaign_rec_filename,campaign_script,get_call_launch,am_message_exten,xferconf_a_dtmf,xferconf_a_number,xferconf_b_dtmf,xferconf_b_number,alt_number_dialing,scheduled_callbacks,wrapup_seconds,wrapup_message,closer_campaigns,use_internal_dnc,allcalls_delay,omit_phone_code,agent_pause_codes_active,no_hopper_leads_logins,campaign_allow_inbound,manual_dial_list_id,default_xfer_group,xfer_groups,disable_alter_custphone,display_queue_count,manual_dial_filter,agent_clipboard_copy,use_campaign_dnc,three_way_call_cid,dial_method,three_way_dial_prefix,web_form_target,vtiger_screen_login,agent_allow_group_alias,default_group_alias,quick_transfer_button,prepopulate_transfer_preset,view_calls_in_queue,view_calls_in_queue_launch,call_requeue_button,pause_after_each_call,no_hopper_dialing,agent_dial_owner_only,agent_display_dialable_leads,web_form_address_two,agent_select_territories,crm_popup_login,crm_login_address,timer_action,timer_action_message,timer_action_seconds,start_call_url,dispo_call_url,xferconf_c_number,xferconf_d_number,xferconf_e_number,use_custom_cid,scheduled_callbacks_alert,scheduled_callbacks_count,manual_dial_override,blind_monitor_warning,blind_monitor_message,blind_monitor_filename,timer_action_destination,enable_xfer_presets,hide_xfer_number_to_dial,manual_dial_prefix,customer_3way_hangup_logging,customer_3way_hangup_seconds,customer_3way_hangup_action,ivr_park_call,manual_preview_dial,api_manual_dial,manual_dial_call_time_check,my_callback_option,per_call_notes,agent_lead_search,agent_lead_search_method,queuemetrics_phone_environment,auto_pause_precall,auto_pause_precall_code,auto_resume_precall,manual_dial_cid,custom_3way_button_transfer,callback_days_limit,campaign_name,end_call_action FROM vicidial_campaigns where campaign_id = '$VD_campaign';";
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
                                $VDcampaign_name    =           $row[95];
                                $end_call_action    =           $row[96];

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
                                        
                                $end_call_action_enabled=0;
				if (preg_match("/IN_GROUP|PRESET_1|PRESET_2|PRESET_3|PRESET_4|PRESET_5/",$end_call_action))
					{$end_call_action_enabled=1;}

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
                $VDdisplayMESSAGE = $LOGfullname .  ", that campaign is not active. Please try again later<br />";
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
            $VDdisplayMESSAGE = "The credentials entered is incorrect. Please verify your login info and try again<br />";
			}
		}
	if ($VDloginDISPLAY)
		{
                    echo str_replace("[UserMessage]", $VDdisplayMESSAGE, $login_script);
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
		echo str_replace("[UserMessage]", "We're sorry, " . $LOGfullname . ", but that extension could not be verified.", $login_script);
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
		echo "<title>X Frontline - Inktel</title>\n";
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
                            echo str_replace("[UserMessage]", "We're sorry, " . $LOGfullname . ", but there are no leads in the hopper for that campaign.", $login_script);
                            exit;
			}
		if (strlen($session_id) < 1)
			{
                            echo str_replace("[UserMessage]", "We're sorry, " . $LOGfullname . ", but there are no available conference sessions in the system.", $login_script);
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
/*
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
*/

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
	$CCAL_OUT .= "<td colspan=\"7\" bordercolor=\"#ffffff\" bgcolor=\"#3C86B7\">";
	$CCAL_OUT .= "<div align=\"center\"><font color=\"#FFF\"><b><font face=\"Arial, Helvetica, sans-serif\" size=\"2\">";
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
	<script language="Javascript">
	window.name='vicidial_window';
	var MTvar;
	var NOW_TIME = '<?php echo $NOW_TIME ?>';
	var SQLdate = '<?php echo $NOW_TIME ?>';
	var StarTtimE = '<?php echo $StarTtimE ?>';
	var UnixTime = '<?php echo $StarTtimE ?>';
	var UnixTimeMS = 0;
	var t = new Date();
	var c = new Date();
	LCAe = new Array('','','','','','');
	LCAc = new Array('','','','','','');
	LCAt = new Array('','','','','','');
	LMAe = new Array('','','','','','');
	var CalL_XC_a_Dtmf = '<?php echo $xferconf_a_dtmf ?>';
	var CalL_XC_a_NuMber = '<?php echo $xferconf_a_number ?>';
	var CalL_XC_b_Dtmf = '<?php echo $xferconf_b_dtmf ?>';
	var CalL_XC_b_NuMber = '<?php echo $xferconf_b_number ?>';
	var CalL_XC_c_NuMber = '<?php echo $xferconf_c_number ?>';
	var CalL_XC_d_NuMber = '<?php echo $xferconf_d_number ?>';
	var CalL_XC_e_NuMber = '<?php echo $xferconf_e_number ?>';
	var VU_hotkeys_active = '<?php echo $VU_hotkeys_active ?>';
	var VU_agent_choose_ingroups = '<?php echo $VU_agent_choose_ingroups ?>';
	var VU_agent_choose_ingroups_DV = '';
	var agent_choose_territories = '<?php echo $VU_agent_choose_territories ?>';
	var agent_select_territories = '<?php echo $agent_select_territories ?>';
	var agent_choose_blended = '<?php echo $VU_agent_choose_blended ?>';
	var VU_closer_campaigns = '<?php echo $VU_closer_campaigns ?>';
	var CallBackDatETimE = '';
	var CallBackrecipient = '';
	var CallBackCommenTs = '';
	var CallBackLeadStatus = '';
	var scheduled_callbacks = '<?php echo $scheduled_callbacks ?>';
	var dispo_check_all_pause = '<?php echo $dispo_check_all_pause ?>';
	var api_check_all_pause = '<?php echo $api_check_all_pause ?>';
	VARgroup_alias_ids = new Array(<?php echo $VARgroup_alias_ids ?>);
	VARgroup_alias_names = new Array(<?php echo $VARgroup_alias_names ?>);
	VARcaller_id_numbers = new Array(<?php echo $VARcaller_id_numbers ?>);
	var VD_group_aliases_ct = '<?php echo $VD_group_aliases_ct ?>';
	var agent_allow_group_alias = '<?php echo $agent_allow_group_alias ?>';
	var default_group_alias = '<?php echo $default_group_alias ?>';
	var default_group_alias_cid = '<?php echo $default_group_alias_cid ?>';
	var active_group_alias = '';
	var agent_pause_codes_active = '<?php echo $agent_pause_codes_active ?>';
	VARpause_codes = new Array(<?php echo $VARpause_codes ?>);
	VARpause_code_names = new Array(<?php echo $VARpause_code_names ?>);
	var VD_pause_codes_ct = '<?php echo $VD_pause_codes_ct ?>';
	VARpreset_names = new Array(<?php echo $VARpreset_names ?>);
	VARpreset_numbers = new Array(<?php echo $VARpreset_numbers ?>);
	VARpreset_dtmfs = new Array(<?php echo $VARpreset_dtmfs ?>);
	VARpreset_hide_numbers = new Array(<?php echo $VARpreset_hide_numbers ?>);
	var VD_preset_names_ct = '<?php echo $VD_preset_names_ct ?>';
	VARstatuses = new Array(<?php echo $VARstatuses ?>);
	VARstatusnames = new Array(<?php echo $VARstatusnames ?>);
        VARstatuscategories = new Array(<?php echo $VARstatuscategories ?>);
	VARSELstatuses = new Array(<?php echo $VARSELstatuses ?>);
	VARCBstatuses = new Array(<?php echo $VARCBstatuses ?>);
	var VARCBstatusesLIST = '<?php echo $VARCBstatusesLIST ?>';
	var VD_statuses_ct = '<?php echo $VD_statuses_ct ?>';
	var VARSELstatuses_ct = '<?php echo $VARSELstatuses_ct ?>';
	VARingroups = new Array(<?php echo $VARingroups ?>);
	var INgroupCOUNT = '<?php echo $INgrpCT ?>';
	VARterritories = new Array(<?php echo $VARterritories ?>);
	var territoryCOUNT = '<?php echo $territoryCT ?>';
	VARxfergroups = new Array(<?php echo $VARxfergroups ?>);
	VARxfergroupsnames = new Array(<?php echo $VARxfergroupsnames ?>);
	var XFgroupCOUNT = '<?php echo $XFgrpCT ?>';
	var default_xfer_group = '<?php echo $default_xfer_group ?>';
	var default_xfer_group_name = '<?php echo $default_xfer_group_name ?>';
	var LIVE_default_xfer_group = '<?php echo $default_xfer_group ?>';
	var HK_statuses_camp = '<?php echo $HK_statuses_camp ?>';
	HKhotkeys = new Array(<?php echo $HKhotkeys ?>);
	HKstatuses = new Array(<?php echo $HKstatuses ?>);
	HKstatusnames = new Array(<?php echo $HKstatusnames ?>);
	var hotkeys = new Array();
	<?php $h=0;
	while ($HK_statuses_camp > $h)
		{
		echo "hotkeys['$HKhotkey[$h]'] = \"$HKstatus[$h] ----- $HKstatus_name[$h]\";\n";
		$h++;
		}
	?>
	var HKdispo_display = 0;
	var HKbutton_allowed = 1;
	var HKfinish = 0;
	var scriptnames = new Array();
	<?php $h=0;
	while ($MM_scripts > $h)
		{
		echo "scriptnames['$MMscriptid[$h]'] = \"$MMscriptname[$h]\";\n";
		$h++;
		}
	?>
	var view_scripts = '<?php echo $view_scripts ?>';
	var LOGfullname = '<?php echo $LOGfullname ?>';
	var recLIST = '';
	var filename = '';
	var last_filename = '';
	var LCAcount = 0;
	var LMAcount = 0;
	var filedate = '<?php echo $FILE_TIME ?>';
	var agcDIR = '<?php echo $agcDIR ?>';
	var agcPAGE = '<?php echo $agcPAGE ?>';
	var extension = '<?php echo $extension ?>';
	var extension_xfer = '<?php echo $extension ?>';
	var dialplan_number = '<?php echo $dialplan_number ?>';
	var ext_context = '<?php echo $ext_context ?>';
	var protocol = '<?php echo $protocol ?>';
	var agentchannel = '';
	var local_gmt ='<?php echo $local_gmt ?>';
	var server_ip = '<?php echo $server_ip ?>';
	var server_ip_dialstring = '<?php echo $server_ip_dialstring ?>';
	var asterisk_version = '<?php echo $asterisk_version ?>';
<?php
if ($enable_fast_refresh < 1) {echo "\tvar refresh_interval = 1000;\n";}
	else {echo "\tvar refresh_interval = $fast_refresh_rate;\n";}
?>
	var session_id = '<?php echo $session_id ?>';
	var VICIDiaL_closer_login_checked = 0;
	var VICIDiaL_closer_login_selected = 0;
	var VICIDiaL_pause_calling = 1;
	var CalLCID = '';
	var MDnextCID = '';
	var XDnextCID = '';
	var LasTCID = '';
	var lead_dial_number = '';
	var MD_channel_look = 0;
	var XD_channel_look = 0;
	var MDuniqueid = '';
	var MDchannel = '';
	var MD_ring_secondS = 0;
	var MDlogEPOCH = 0;
	var VD_live_customer_call = 0;
	var VD_live_call_secondS = 0;
	var XD_live_customer_call = 0;
	var XD_live_call_secondS = 0;
	var xfer_in_call = 0;
	var open_dispo_screen = 0;
	var AgentDispoing = 0;
        var AutoDialNextNumber = 0;
	var logout_stop_timeouts = 0;
	var VICIDiaL_allow_closers = '<?php echo $VICIDiaL_allow_closers ?>';
	var VICIDiaL_closer_blended = '0';
	var VU_closer_default_blended = '<?php echo $VU_closer_default_blended ?>';
	var VDstop_rec_after_each_call = '<?php echo $VDstop_rec_after_each_call ?>';
	var phone_login = '<?php echo $phone_login ?>';
	var original_phone_login = '<?php echo $original_phone_login ?>';
	var phone_pass = '<?php echo $phone_pass ?>';
	var user = '<?php echo $VD_login ?>';
	var user_abb = '<?php echo $user_abb ?>';
	var pass = '<?php echo $VD_pass ?>';
	var campaign = '<?php echo $VD_campaign ?>';
	var group = '<?php echo $VD_campaign ?>';
	var VICIDiaL_web_form_address_enc = '<?php echo $VICIDiaL_web_form_address_enc ?>';
	var VICIDiaL_web_form_address = '<?php echo $VICIDiaL_web_form_address ?>';
	var VDIC_web_form_address = '<?php echo $VICIDiaL_web_form_address ?>';
	var VICIDiaL_web_form_address_two_enc = '<?php echo $VICIDiaL_web_form_address_two_enc ?>';
	var VICIDiaL_web_form_address_two = '<?php echo $VICIDiaL_web_form_address_two ?>';
	var VDIC_web_form_address_two = '<?php echo $VICIDiaL_web_form_address_two ?>';
	var CalL_ScripT_id = '';
	var CalL_AutO_LauncH = '';
	var panel_bgcolor = '<?php echo $MAIN_COLOR ?>';
	var CusTCB_bgcolor = '#FFFF66';
	var auto_dial_level = '<?php echo $auto_dial_level ?>';
	var starting_dial_level = '<?php echo $auto_dial_level ?>';
	var dial_timeout = '<?php echo $dial_timeout ?>';
	var dial_prefix = '<?php echo $dial_prefix ?>';
	var manual_dial_prefix = '<?php echo $manual_dial_prefix ?>';
	var three_way_dial_prefix = '<?php echo $three_way_dial_prefix ?>';
	var campaign_cid = '<?php echo $campaign_cid ?>';
	var use_custom_cid = '<?php echo $use_custom_cid ?>';
	var campaign_vdad_exten = '<?php echo $campaign_vdad_exten ?>';
	var campaign_leads_to_call = '<?php echo $campaign_leads_to_call ?>';
	var epoch_sec = <?php echo $StarTtimE ?>;
	var dtmf_send_extension = '<?php echo $dtmf_send_extension ?>';
	var recording_exten = '<?php echo $campaign_rec_exten ?>';
	var campaign_recording = '<?php echo $campaign_recording ?>';
	var campaign_rec_filename = '<?php echo $campaign_rec_filename ?>';
	var LIVE_campaign_recording = '<?php echo $campaign_recording ?>';
	var LIVE_campaign_rec_filename = '<?php echo $campaign_rec_filename ?>';
	var LIVE_default_group_alias = '<?php echo $default_group_alias ?>';
	var LIVE_caller_id_number = '<?php echo $default_group_alias_cid ?>';
	var LIVE_web_vars = '<?php echo $default_web_vars ?>';
	var default_web_vars = '<?php echo $default_web_vars ?>';
	var campaign_script = '<?php echo $campaign_script ?>';
	var get_call_launch = '<?php echo $get_call_launch ?>';
	var campaign_am_message_exten = '<?php echo $campaign_am_message_exten ?>';
	var park_on_extension = '<?php echo $VICIDiaL_park_on_extension ?>';
        var auto_dial_next_number = '<?php echo $auto_dial_next_number ?>';
	var park_count=0;
	var customerparked=0;
	var customerparkedcounter=0;
	var customerparkedxfer=0;
	var customerparkedcounterxfer=0;
	var check_n = 0;
	var conf_check_recheck = 0;
	var lastconf='';
	var lastcustchannel='';
	var lastcustserverip='';
	var lastxferchannel='';
	var custchannellive=0;
	var xferchannellive=0;
	var nochannelinsession=0;
	var agc_dial_prefix = '91';
	var dtmf_silent_prefix = '<?php echo $dtmf_silent_prefix ?>';
	var conf_silent_prefix = '<?php echo $conf_silent_prefix ?>';
	var menuheight = 30;
	var menuwidth = 30;
	var menufontsize = 8;
	var textareafontsize = 10;
	var check_s;
	var active_display = 1;
	var conf_channels_xtra_display = 0;
	var display_message = '';
	var web_form_vars = '';
	var Nactiveext;
	var Nbusytrunk;
	var Nbusyext;
	var extvalue = extension;
	var activeext_query;
	var busytrunk_query;
	var busyext_query;
	var busytrunkhangup_query;
	var busylocalhangup_query;
	var activeext_order='asc';
	var busytrunk_order='asc';
	var busyext_order='asc';
	var busytrunkhangup_order='asc';
	var busylocalhangup_order='asc';
	var xmlhttp=false;
	var XfeR_channel = '';
	var XDcheck = '';
	var agent_log_id = '<?php echo $agent_log_id ?>';
	var session_name = '<?php echo $session_name ?>';
	var AutoDialReady = 0;
	var AutoDialWaiting = 0;
	var fronter = '';
	var VDCL_group_id = '';
	var previous_dispo = '';
	var previous_called_count = '';
	var hot_keys_active = 0;
	var all_record = 'NO';
	var all_record_count = 0;
	var LeaDDispO = '';
	var LeaDPreVDispO = '';
	var AgaiNHanguPChanneL = '';
	var AgaiNHanguPServeR = '';
	var AgainCalLSecondS = '';
	var AgaiNCalLCID = '';
	var CB_count_check = 60;
	var callholdstatus = '<?php echo $callholdstatus ?>'
	var agentcallsstatus = '<?php echo $agentcallsstatus ?>'
	var campagentstatctmax = '<?php echo $campagentstatctmax ?>'
	var campagentstatct = '0';
	var manual_dial_in_progress = 0;
	var auto_dial_alt_dial = 0;
	var reselect_preview_dial = 0;
	var in_lead_preview_state = 0;
	var reselect_alt_dial = 0;
	var alt_dial_active = 0;
	var alt_dial_status_display = 0;
	var mdnLisT_id = '<?php echo $manual_dial_list_id ?>';
	var VU_vicidial_transfers = '<?php echo $VU_vicidial_transfers ?>';
	var agentonly_callbacks = '<?php echo $agentonly_callbacks ?>';
	var agentcall_manual = '<?php echo $agentcall_manual ?>';
	var manual_dial_preview = '<?php echo $manual_dial_preview ?>';
	var manual_preview_dial = '<?php echo $manual_preview_dial ?>';
	var starting_alt_phone_dialing = '<?php echo $alt_phone_dialing ?>';
	var alt_phone_dialing = '<?php echo $alt_phone_dialing ?>';
	var DefaulTAlTDiaL = '<?php echo $DefaulTAlTDiaL ?>';
	var wrapup_seconds = '<?php echo $wrapup_seconds ?>';
	var wrapup_message = '<?php echo $wrapup_message ?>';
	var wrapup_counter = 0;
	var wrapup_waiting = 0;
	var use_internal_dnc = '<?php echo $use_internal_dnc ?>';
	var use_campaign_dnc = '<?php echo $use_campaign_dnc ?>';
	var three_way_call_cid = '<?php echo $three_way_call_cid ?>';
	var outbound_cid = '<?php echo $outbound_cid ?>';
	var threeway_cid = '';
	var cid_choice = '';
	var prefix_choice = '';
	var agent_dialed_number='';
	var agent_dialed_type='';
	var allcalls_delay = '<?php echo $allcalls_delay ?>';
	var omit_phone_code = '<?php echo $omit_phone_code ?>';
	var no_delete_sessions = '<?php echo $no_delete_sessions ?>';
	var webform_session = '<?php echo $webform_sessionname ?>';
	var local_consult_xfers = '<?php echo $local_consult_xfers ?>';
	var vicidial_agent_disable = '<?php echo $vicidial_agent_disable ?>';
	var CBentry_time = '';
	var CBcallback_time = '';
	var CBuser = '';
	var CBcomments = '';
	var volumecontrol_active = '<?php echo $volumecontrol_active ?>';
	var PauseCode_HTML = '';
	var manual_auto_hotkey = 0;
	var dialed_number = '';
	var dialed_label = '';
	var source_id = '';
	var DispO3waychannel = '';
	var DispO3wayXtrAchannel = '';
	var DispO3wayCalLserverip = '';
	var DispO3wayCalLxfernumber = '';
	var DispO3wayCalLcamptail = '';
	var PausENotifYCounTer = 0;
	var RedirecTxFEr = 0;
	var phone_ip = '<?php echo $phone_ip ?>';
	var enable_sipsak_messages = '<?php echo $enable_sipsak_messages ?>';
	var allow_sipsak_messages = '<?php echo $allow_sipsak_messages ?>';
	var HidEMonitoRSessionS = '<?php echo $HidEMonitoRSessionS ?>';
	var LogouTKicKAlL = '<?php echo $LogouTKicKAlL ?>';
	var flag_channels = '<?php echo $flag_channels ?>';
	var flag_string = '<?php echo $flag_string ?>';
	var vdc_header_date_format = '<?php echo $vdc_header_date_format ?>';
	var vdc_customer_date_format = '<?php echo $vdc_customer_date_format ?>';
	var vdc_header_phone_format = '<?php echo $vdc_header_phone_format ?>';
	var disable_alter_custphone = '<?php echo $disable_alter_custphone ?>';
	var manual_dial_filter = '<?php echo $manual_dial_filter ?>';
	var CopY_tO_ClipboarD = '<?php echo $CopY_tO_ClipboarD ?>';
	var inOUT = 'OUT';
	var useIE = '<?php echo $useIE ?>';
	var random = '<?php echo $random ?>';
	var threeway_end = 0;
	var agentphonelive = 0;
	var conf_dialed = 0;
	var leaving_threeway = 0;
	var blind_transfer = 0;
	var hangup_all_non_reserved = '<?php echo $hangup_all_non_reserved ?>';
	var dial_method = '<?php echo $dial_method ?>';
	var web_form_target = '<?php echo $web_form_target ?>';
	var TEMP_VDIC_web_form_address = '';
	var TEMP_VDIC_web_form_address_two = '';
	var APIPausE_ID = '99999';
	var APIDiaL_ID = '99999';
	var CheckDEADcall = 0;
	var CheckDEADcallON = 0;
	var VtigeRLogiNScripT = '<?php echo $vtiger_screen_login ?>';
	var VtigeRurl = '<?php echo $vtiger_url ?>';
	var VtigeREnableD = '<?php echo $enable_vtiger_integration ?>';
	var alert_enabled = '<?php echo $VU_alert_enabled ?>';
	var allow_alerts = '<?php echo $VU_allow_alerts ?>';
	var shift_logout_flag = 0;
	var vtiger_callback_id = 0;
	var agent_status_view = '<?php echo $agent_status_view ?>';
	var agent_status_view_time = '<?php echo $agent_status_view_time ?>';
	var agent_status_view_active = 0;
	var xfer_select_agents_active = 0;
	var even=0;
	var VU_user_group = '<?php echo $VU_user_group ?>';
	var quick_transfer_button = '<?php echo $quick_transfer_button ?>';
	var quick_transfer_button_enabled = '<?php echo $quick_transfer_button_enabled ?>';
	var quick_transfer_button_orig = '';
	var quick_transfer_button_locked = '<?php echo $quick_transfer_button_locked ?>';
        var end_call_action = '<?php echo $end_call_action ?>';
	var end_call_action_enabled = '<?php echo $end_call_action_enabled ?>';
	var prepopulate_transfer_preset = '<?php echo $prepopulate_transfer_preset ?>';
	var prepopulate_transfer_preset_enabled = '<?php echo $prepopulate_transfer_preset_enabled ?>';
	var view_calls_in_queue = '<?php echo $view_calls_in_queue ?>';
	var view_calls_in_queue_launch = '<?php echo $view_calls_in_queue_launch ?>';
	var view_calls_in_queue_active = '<?php echo $view_calls_in_queue_launch ?>';
	var call_requeue_button = '<?php echo $call_requeue_button ?>';
	var no_hopper_dialing = '<?php echo $no_hopper_dialing ?>';
	var agent_dial_owner_only = '<?php echo $agent_dial_owner_only ?>';
	var agent_display_dialable_leads = '<?php echo $agent_display_dialable_leads ?>';
	var no_empty_session_warnings = '<?php echo $no_empty_session_warnings ?>';
	var script_width = '<?php echo $SDwidth ?>';
	var script_height = '<?php echo $SSheight ?>';
	var enable_second_webform = '<?php echo $enable_second_webform ?>';
	var no_delete_VDAC=0;
	var manager_ingroups_set=0;
	var external_igb_set_name='';
	var recording_filename='';
	var recording_id='';
	var delayed_script_load='';
	var script_recording_delay='';
	var VDRP_stage='PAUSED';
	var VU_custom_one = '<?php echo $VU_custom_one ?>';
	var VU_custom_two = '<?php echo $VU_custom_two ?>';
	var VU_custom_three = '<?php echo $VU_custom_three ?>';
	var VU_custom_four = '<?php echo $VU_custom_four ?>';
	var VU_custom_five = '<?php echo $VU_custom_five ?>';
	var crm_popup_login = '<?php echo $crm_popup_login ?>';
	var crm_login_address = '<?php echo $crm_login_address ?>';
	var update_fields=0;
	var update_fields_data='';
	var campaign_timer_action = '<?php echo $timer_action ?>';
	var campaign_timer_action_message = '<?php echo $timer_action_message ?>';
	var campaign_timer_action_seconds = '<?php echo $timer_action_seconds ?>';
	var campaign_timer_action_destination = '<?php echo $timer_action_destination ?>';
	var timer_action='';
	var timer_action_message='';
	var timer_action_seconds='';
	var timer_action_destination = '';
	var is_webphone='<?php echo $is_webphone ?>';
	var WebPhonEurl='<?php echo $WebPhonEurl ?>';
	var pause_code_counter=1;
	var agent_call_log_view='<?php echo $agent_call_log_view ?>';
	var scheduled_callbacks_alert='<?php echo $scheduled_callbacks_alert ?>';
	var scheduled_callbacks_count='<?php echo $scheduled_callbacks_count ?>';
	var callback_days_limit='<?php echo $callback_days_limit ?>';
	var tmp_vicidial_id='';
	var agent_xfer_consultative='<?php echo $agent_xfer_consultative ?>';
	var agent_xfer_dial_override='<?php echo $agent_xfer_dial_override ?>';
	var agent_xfer_vm_transfer='<?php echo $agent_xfer_vm_transfer ?>';
	var agent_xfer_blind_transfer='<?php echo $agent_xfer_blind_transfer ?>';
	var agent_xfer_dial_with_customer='<?php echo $agent_xfer_dial_with_customer ?>';
	var agent_xfer_park_customer_dial='<?php echo $agent_xfer_park_customer_dial ?>';
	var EAphone_code='';
	var EAphone_number='';
	var EAalt_phone_notes='';
	var EAalt_phone_active='';
	var EAalt_phone_count='';
	var conf_check_attempts = '<?php echo $conf_check_attempts ?>';
	var conf_check_attempts_cleanup = '<?php echo ($conf_check_attempts + 2) ?>';
	var blind_monitor_warning='<?php echo $blind_monitor_warning ?>';
	var blind_monitor_message='<?php echo $blind_monitor_message ?>';
	var blind_monitor_filename='<?php echo $blind_monitor_filename ?>';
	var blind_monitoring_now=0;
	var blind_monitoring_now_trigger=0;
	var no_blind_monitors=0;
	var uniqueid_status_display='';
	var uniqueid_status_prefix='';
	var custom_call_id='';
	var api_dtmf='';
	var api_transferconf_function='';
	var api_transferconf_group='';
	var api_transferconf_number='';
	var api_transferconf_consultative='';
	var api_transferconf_override='';
	var api_parkcustomer='';
	var API_selected_xfergroup='';
	var API_selected_callmenu='';
	var custom_fields_enabled='<?php echo $custom_fields_enabled ?>';
   var on_hook_agent='<?php echo $on_hook_agent ?>';
   var SIP_user_DiaL='<?php echo $SIP_user_DiaL ?>';
   var SIqueryCID  = '<?php echo $SIqueryCID ?>';
	var form_contents_loaded=0;
	var enable_xfer_presets='<?php echo $enable_xfer_presets ?>';
	var hide_xfer_number_to_dial='<?php echo $hide_xfer_number_to_dial ?>';
	var Presets_HTML='';
	var did_pattern='';
	var did_id='';
	var did_extension='';
	var did_description='';
	var closecallid='';
	var xfercallid='';
	var custom_field_names='';
	var custom_field_values='';
	var custom_field_types='';
	var customer_3way_hangup_logging='<?php echo $customer_3way_hangup_logging ?>';
	var customer_3way_hangup_seconds='<?php echo $customer_3way_hangup_seconds ?>';
	var customer_3way_hangup_action='<?php echo $customer_3way_hangup_action ?>';
	var customer_3way_hangup_counter=0;
	var customer_3way_hangup_counter_trigger=0;
	var customer_3way_hangup_dispo_message='';
        var customer_3way_hangup_custom_message='Customer has hung up:';
	var ivr_park_call='<?php echo $ivr_park_call ?>';
	var qm_phone='<?php echo $QM_PHONE ?>';
	var APIManualDialQueue=0;
	var APIManualDialQueue_last=0;
	var api_manual_dial='<?php echo $api_manual_dial ?>';
	var manual_dial_call_time_check='<?php echo $manual_dial_call_time_check ?>';
	var CloserSelecting=0;
	var TerritorySelecting=0;
	var WaitingForNextStep=0;
	var AllowManualQueueCalls='<?php echo $AllowManualQueueCalls ?>';
	var AllowManualQueueCallsChoice='<?php echo $AllowManualQueueCallsChoice ?>';
	var call_variables='';
	var focus_blur_enabled='<?php echo $focus_blur_enabled ?>';
	var CBlinkCONTENT='';
	var my_callback_option='<?php echo $my_callback_option ?>';
	var per_call_notes='<?php echo $per_call_notes ?>';
	var agent_lead_search='<?php echo $agent_lead_search ?>';
	var agent_lead_search_method='<?php echo $agent_lead_search_method ?>';
	var qm_phone_environment='<?php echo $qm_phone_environment ?>';
	var LastCallCID='';
	var LastCallbackCount=0;
	var LastCallbackViewed=0;
	var auto_pause_precall='<?php echo $auto_pause_precall ?>';
	var auto_pause_precall_code='<?php echo $auto_pause_precall_code ?>';
	var auto_resume_precall='<?php echo $auto_resume_precall ?>';
	var trigger_ready=0;
	var hide_gender='<?php echo $hide_gender ?>';
	var manual_dial_cid='<?php echo $manual_dial_cid ?>';
	var post_phone_time_diff_alert_message='';
	var custom_3way_button_transfer='<?php echo $custom_3way_button_transfer ?>';
	var custom_3way_button_transfer_enabled='<?php echo $custom_3way_button_transfer_enabled ?>';
	var custom_3way_button_transfer_park='<?php echo $custom_3way_button_transfer_park ?>';
	var waiting_on_dispo=0;
    var DiaLControl_auto_HTML = "<a href=\"#\" onclick=\"AutoDial_ReSume_PauSe('VDADready');\"><img src=\"./images/x-resume.png\" border=\"0\" alt=\"Resume\" /><span id=\"x-ready\">Ready</span></a><a href=\"#\" style=\"opacity:.15;\" disabled><img class=\"disabled_img\" src=\"./images/x-pause.png\" border=\"0\" alt=\" Pause \" /><span id=\"x-pause\">Pause</span></a>";
    var DiaLControl_auto_HTML_ready = "<a href=\"#\" style=\"opacity:.15;\" disabled><img class=\"disabled_img\" src=\"./images/x-resume.png\" border=\"0\" alt=\"Resume\" /><span id=\"x-resume\">Ready</span></a><a href=\"#\" onclick=\"AutoDial_ReSume_PauSe('VDADpause');\"><img src=\"./images/x-pause.png\" border=\"0\" alt=\" Pause \" /><span id=\"x-pause\">Pause</span></a>";
    var DiaLControl_auto_HTML_OFF = "<a href=\"#\" style=\"opacity:.15;\" disabled><img class=\"disabled_img\" src=\"./images/x-resume.png\" border=\"0\" alt=\"Resume\" /><span id=\"x-resume\">Ready</span></a><a href=\"#\" style=\"opacity:.15;\" disabled><img class=\"disabled_img\" src=\"./images/x-pause.png\" border=\"0\" alt=\" Pause \" /><span id=\"x-pause\">Pause</span></a>";
    var DiaLControl_manual_HTML = "<a href=\"#\" onclick=\"ManualDialNext('','','','','','0');\"><img src=\"./images/x-dialnext.png\" style=\"margin-bottom:-15px;\" border=\"0\" alt=\"Dial Next Number\" /><br /><span id=\"x-dialnext\">Dial Next #</span></a>";
    var DiaLControl_manual_HTML_OFF = "<a href=\"#\" style=\"opacity:.15;\" disabled><img class=\"disabled_img\" src=\"./images/x-dialnext.png\" style=\"margin-bottom:-15px;\" border=\"0\" alt=\"Dial Next Number\" /><br /><span id=\"x-dialnext\">Dial Next #</span></a>";
	var image_loading = new Image();
		image_loading.src="./images/loading.gif";
	var image_blank = new Image();
		image_blank.src="./images/blank.gif";
	var image_livecall_OFF = new Image();
		image_livecall_OFF.src="./images/agc_live_call_OFF.gif";
	var image_livecall_ON = new Image();
		image_livecall_ON.src="./images/agc_live_call_ON.gif";
	var image_livecall_DEAD = new Image();
		image_livecall_DEAD.src="./images/agc_live_call_DEAD.gif";
	var image_LB_dialnextnumber = new Image();
		image_LB_dialnextnumber.src="./images/vdc_LB_dialnextnumber.gif";
	var image_LB_hangupcustomer = new Image();
		image_LB_hangupcustomer.src="./images/vdc_LB_hangupcustomer.gif";
	var image_LB_transferconf = new Image();
		image_LB_transferconf.src="./images/vdc_LB_transferconf.gif";
	var image_LB_grabparkedcall = new Image();
		image_LB_grabparkedcall.src="./images/vdc_LB_grabparkedcall.gif";
	var image_LB_parkcall = new Image();
		image_LB_parkcall.src="./images/vdc_LB_parkcall.gif";
	var image_LB_webform = new Image();
		image_LB_webform.src="./images/vdc_LB_webform.gif";
	var image_LB_stoprecording = new Image();
		image_LB_stoprecording.src="./images/vdc_LB_stoprecording.gif";
	var image_LB_startrecording = new Image();
		image_LB_startrecording.src="./images/vdc_LB_startrecording.gif";
	var image_LB_pause = new Image();
		image_LB_pause.src="./images/vdc_LB_pause.gif";
	var image_LB_resume = new Image();
		image_LB_resume.src="./images/vdc_LB_resume.gif";
	var image_LB_senddtmf = new Image();
		image_LB_senddtmf.src="./images/vdc_LB_senddtmf.gif";
	var image_LB_dialnextnumber_OFF = new Image();
		image_LB_dialnextnumber_OFF.src="./images/vdc_LB_dialnextnumber_OFF.gif";
	var image_LB_hangupcustomer_OFF = new Image();
		image_LB_hangupcustomer_OFF.src="./images/vdc_LB_hangupcustomer_OFF.gif";
	var image_LB_transferconf_OFF = new Image();
		image_LB_transferconf_OFF.src="./images/vdc_LB_transferconf_OFF.gif";
	var image_LB_grabparkedcall_OFF = new Image();
		image_LB_grabparkedcall_OFF.src="./images/vdc_LB_grabparkedcall_OFF.gif";
	var image_LB_parkcall_OFF = new Image();
		image_LB_parkcall_OFF.src="./images/vdc_LB_parkcall_OFF.gif";
	var image_LB_webform_OFF = new Image();
		image_LB_webform_OFF.src="./images/vdc_LB_webform_OFF.gif";
	var image_LB_stoprecording_OFF = new Image();
		image_LB_stoprecording_OFF.src="./images/vdc_LB_stoprecording_OFF.gif";
	var image_LB_startrecording_OFF = new Image();
		image_LB_startrecording_OFF.src="./images/vdc_LB_startrecording_OFF.gif";
	var image_LB_pause_OFF = new Image();
		image_LB_pause_OFF.src="./images/vdc_LB_pause_OFF.gif";
	var image_LB_resume_OFF = new Image();
		image_LB_resume_OFF.src="./images/vdc_LB_resume_OFF.gif";
	var image_LB_senddtmf_OFF = new Image();
		image_LB_senddtmf_OFF.src="./images/vdc_LB_senddtmf_OFF.gif";
	var image_LB_ivrgrabparkedcall = new Image();
		image_LB_ivrgrabparkedcall.src="./images/vdc_LB_grabivrparkcall.gif";
	var image_LB_ivrparkcall = new Image();
		image_LB_ivrparkcall.src="./images/vdc_LB_ivrparkcall.gif";



// ################################################################################
// Send Hangup command for Live call connected to phone now to Manager
	function livehangup_send_hangup(taskvar) 
		{
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
			var queryCID = "HLagcW" + epoch_sec + user_abb;
			var hangupvalue = taskvar;
			livehangup_query = "server_ip=" + server_ip + "&session_name=" + session_name + "&user=" + user + "&pass=" + pass + "&ACTION=Hangup&format=text&channel=" + hangupvalue + "&queryCID=" + queryCID + "&log_campaign=" + campaign;
			xmlhttp.open('POST', 'manager_send.php'); 
			xmlhttp.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
			xmlhttp.send(livehangup_query); 
			xmlhttp.onreadystatechange = function() 
				{ 
				if (xmlhttp.readyState == 4 && xmlhttp.status == 200) 
					{
					Nactiveext = null;
					Nactiveext = xmlhttp.responseText;
					alert_box(xmlhttp.responseText);
					}
				}
			delete xmlhttp;
			}
		}

// ################################################################################
// Send volume control command for meetme participant
	function volume_control(taskdirection,taskvolchannel,taskagentmute) 
		{
		if (taskagentmute=='AgenT')
			{
			taskvolchannel = agentchannel;
			}
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
			var queryCID = "VCagcW" + epoch_sec + user_abb;
			var volchanvalue = taskvolchannel;
			livevolume_query = "server_ip=" + server_ip + "&session_name=" + session_name + "&user=" + user + "&pass=" + pass + "&ACTION=VolumeControl&format=text&channel=" + volchanvalue + "&stage=" + taskdirection + "&exten=" + session_id + "&ext_context=" + ext_context + "&queryCID=" + queryCID;
			xmlhttp.open('POST', 'manager_send.php'); 
			xmlhttp.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
			xmlhttp.send(livevolume_query); 
			xmlhttp.onreadystatechange = function() 
				{ 
				if (xmlhttp.readyState == 4 && xmlhttp.status == 200) 
					{
					Nactiveext = null;
					Nactiveext = xmlhttp.responseText;
				//	alert(xmlhttp.responseText);
					}
				}
			delete xmlhttp;
			}
		if (taskagentmute=='AgenT')
			{
			if (taskdirection=='MUTING')
				{
                document.getElementById("AgentMuteSpan").innerHTML = "<a href=\"#CHAN-" + agentchannel + "\" onclick=\"volume_control('UNMUTE','" + agentchannel + "','AgenT');return false;\"><img style=\"height:35px;\" src=\"./images/x-volume-unmute.png\" border=\"0\" /></a>";
				}
			else
				{
                document.getElementById("AgentMuteSpan").innerHTML = "<a href=\"#CHAN-" + agentchannel + "\" onclick=\"volume_control('MUTING','" + agentchannel + "','AgenT');return false;\"><img style=\"height:35px;\" src=\"./images/x-volume-mute.png\" border=\"0\" /></a>";
				}
			}

		}


// ################################################################################
// Send alert control command for agent
	function alert_control(taskalert) 
		{
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
			alert_query = "server_ip=" + server_ip + "&session_name=" + session_name + "&user=" + user + "&pass=" + pass + "&ACTION=AlertControl&format=text&stage=" + taskalert;
			xmlhttp.open('POST', 'vdc_db_query.php'); 
			xmlhttp.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
			xmlhttp.send(alert_query); 
			xmlhttp.onreadystatechange = function() 
				{ 
				if (xmlhttp.readyState == 4 && xmlhttp.status == 200) 
					{
					Nactiveext = null;
					Nactiveext = xmlhttp.responseText;
				//	alert(xmlhttp.responseText);
					}
				}
			delete xmlhttp;
			}
		if (taskalert=='ON')
			{
			alert_enabled = 'ON';
			document.getElementById("AgentAlertSpan").innerHTML = "<a href=\"#\" onclick=\"alert_control('OFF');return false;\">Alert is ON</a>";
			}
		else
			{
			alert_enabled = 'OFF';
			document.getElementById("AgentAlertSpan").innerHTML = "<a href=\"#\" onclick=\"alert_control('ON');return false;\">Alert is OFF</a>";
			}

		}


// ################################################################################
// custom button transfer 3way call
	function custom_button_transfer()
		{
		ShoWTransferMain('ON');

		if ( (custom_3way_button_transfer == 'PRESET_1') || (custom_3way_button_transfer == 'PARK_PRESET_1') )
			{document.vicidial_form.xfernumber.value = CalL_XC_a_NuMber;   document.vicidial_form.xfername.value='D1';}
		if ( (custom_3way_button_transfer == 'PRESET_2') || (custom_3way_button_transfer == 'PARK_PRESET_2') )
			{document.vicidial_form.xfernumber.value = CalL_XC_b_NuMber;   document.vicidial_form.xfername.value='D2';}
		if ( (custom_3way_button_transfer == 'PRESET_3') || (custom_3way_button_transfer == 'PARK_PRESET_3') )
			{document.vicidial_form.xfernumber.value = CalL_XC_c_NuMber;   document.vicidial_form.xfername.value='D3';}
		if ( (custom_3way_button_transfer == 'PRESET_4') || (custom_3way_button_transfer == 'PARK_PRESET_4') )
			{document.vicidial_form.xfernumber.value = CalL_XC_d_NuMber;   document.vicidial_form.xfername.value='D4';}
		if ( (custom_3way_button_transfer == 'PRESET_5') || (custom_3way_button_transfer == 'PARK_PRESET_5') )
			{document.vicidial_form.xfernumber.value = CalL_XC_e_NuMber;   document.vicidial_form.xfername.value='D5';}
		if ( (custom_3way_button_transfer == 'FIELD_address3') || (custom_3way_button_transfer == 'PARK_FIELD_address3') )
			{document.vicidial_form.xfernumber.value = document.vicidial_form.address3.value;}
		if ( (custom_3way_button_transfer == 'FIELD_province') || (custom_3way_button_transfer == 'PARK_FIELD_province') )
			{document.vicidial_form.xfernumber.value = document.vicidial_form.province.value;}
		if ( (custom_3way_button_transfer == 'FIELD_security_phrase') || (custom_3way_button_transfer == 'PARK_FIELD_security_phrase') )
			{document.vicidial_form.xfernumber.value = document.vicidial_form.security_phrase.value;}
		if ( (custom_3way_button_transfer == 'FIELD_vendor_lead_code') || (custom_3way_button_transfer == 'PARK_FIELD_vendor_lead_code') )
			{document.vicidial_form.xfernumber.value = document.vicidial_form.vendor_lead_code.value;}
		if ( (custom_3way_button_transfer == 'FIELD_email') || (custom_3way_button_transfer == 'PARK_FIELD_email') )
			{document.vicidial_form.xfernumber.value = document.vicidial_form.email.value;}
		if ( (custom_3way_button_transfer == 'FIELD_owner') || (custom_3way_button_transfer == 'PARK_FIELD_owner') )
			{document.vicidial_form.xfernumber.value = document.vicidial_form.owner.value;}

		var temp_xfernumber = document.vicidial_form.xfernumber.value;
		if (temp_xfernumber.length < 3)
			{
			alert_box("Number to Dial invalid: " + temp_xfernumber);
			ShoWTransferMain('OFF','YES');
			}
		else
			{
			if (custom_3way_button_transfer_park > 0)
				{
				xfer_park_dial();
				}
			else
				{
				SendManualDial('YES');
				}
			}
		}

// ################################################################################
// park customer and place 3way call
	function xfer_park_dial()
		{
		conf_dialed=1;

		if (customerparked == 0)
		{
			mainxfer_send_redirect('ParK',lastcustchannel,lastcustserverip);
		}
		SendManualDial('YES');
		}

// ################################################################################
// place 3way and customer into other conference and fake-hangup the lines
	function leave_3way_call(tempvarattempt)
		{
		threeway_end=0;
		leaving_threeway=1;

		if (customerparked > 0)
			{
			mainxfer_send_redirect('FROMParK',lastcustchannel,lastcustserverip);
			}

		mainxfer_send_redirect('3WAY','','',tempvarattempt);

//		if (threeway_end == '0')
//			{
//			document.vicidial_form.xferchannel.value = '';
//			xfercall_send_hangup();
//
//			document.vicidial_form.callchannel.value = '';
//			document.vicidial_form.callserverip.value = '';
//			dialedcall_send_hangup();
//			}

		//if( document.images ) { document.images['livecall'].src = image_livecall_OFF.src;}
                document.getElementById('livecall').innerHTML = 'Not on Call';
                document.getElementById('livecall').className = 'off';
		}

// ################################################################################
// filter manual dialstring and pass on to originate call
	function SendManualDial(taskFromConf)
		{
		conf_dialed=1;
		var sending_group_alias = 0;
                document.vicidial_form.xfernumber.value = document.vicidial_form.xfernumber.value.replace(/[^\d]/g,'');
                
		// Dial With Customer button
		if (taskFromConf == 'YES')
			{
			xfer_in_call=1;
			agent_dialed_number='1';
			agent_dialed_type='XFER_3WAY';

            document.getElementById("DialWithCustomer").innerHTML ="Warm X-fer";
            document.getElementById("DialWithCustomer").className = "button blueinactive";


            document.getElementById("ParkCustomerDial").innerHTML ="Park &amp; Dial";
            document.getElementById("ParkCustomerDial").className = "button blueinactive";


			var manual_number = document.vicidial_form.xfernumber.value;
			var manual_number_hidden = document.vicidial_form.xfernumhidden.value;
			if ( (manual_number.length < 1) && (manual_number_hidden.length > 0) )
				{manual_number=manual_number_hidden;}
			var manual_string = manual_number.toString();
			var dial_conf_exten = session_id;
			threeway_cid = '';
			if (three_way_call_cid == 'CAMPAIGN')
				{threeway_cid = campaign_cid;}
			if (three_way_call_cid == 'AGENT_PHONE')
				{threeway_cid = outbound_cid;}
			if (three_way_call_cid == 'CUSTOMER')
				{threeway_cid = document.vicidial_form.phone_number.value;}
			if (three_way_call_cid == 'CUSTOM_CID')
				{threeway_cid = document.vicidial_form.security_phrase.value;}
			if (three_way_call_cid == 'AGENT_CHOOSE')
				{
				threeway_cid = cid_choice;
				if (active_group_alias.length > 1)
					{var sending_group_alias = 1;}
				}
			}
		else
			{
			var manual_number = document.vicidial_form.xfernumber.value;
			var manual_string = manual_number.toString();
			var threeway_cid='1';
			if (manual_dial_cid == 'AGENT_PHONE')
				{threeway_cid = outbound_cid;}
			}
		var regXFvars = new RegExp("XFER","g");
		if (manual_string.match(regXFvars))
			{
			var donothing=1;
			}
		else
			{
			if (document.vicidial_form.xferoverride.checked==false)
				{
				if (three_way_dial_prefix == 'X') {var temp_dial_prefix = '';}
				else {var temp_dial_prefix = three_way_dial_prefix;}
				if (omit_phone_code == 'Y') {var temp_phone_code = '';}
				else {var temp_phone_code = document.vicidial_form.phone_code.value;}

				if (manual_string.length > 7)
					{manual_string = temp_dial_prefix + "" + temp_phone_code + "" + manual_string;}
				}
			else
				{agent_dialed_type='XFER_OVERRIDE';}
			// due to a bug in Asterisk, these call variables do not actually work
			call_variables = '__vendor_lead_code=' + document.vicidial_form.vendor_lead_code.value + ',__lead_id=' + document.vicidial_form.lead_id.value;
			}
		var sending_preset_name = document.vicidial_form.xfername.value;
		if (taskFromConf == 'YES')
			{basic_originate_call(manual_string,'NO','YES',dial_conf_exten,'NO',taskFromConf,threeway_cid,sending_group_alias,'',sending_preset_name,call_variables);}
		else
			{basic_originate_call(manual_string,'NO','NO','','','',threeway_cid,sending_group_alias,sending_preset_name,call_variables);}

		MD_ring_secondS=0;
		}

// ################################################################################
// Send Originate command to manager to place a phone call
	function basic_originate_call(tasknum,taskprefix,taskreverse,taskdialvalue,tasknowait,taskconfxfer,taskcid,taskusegroupalias,taskalert,taskpresetname,taskvariables) 
		{
		if (taskalert == '1')
			{
			var TAqueryCID = tasknum;
			tasknum = '83047777777777';
			taskdialvalue = '7' + taskdialvalue;
			var alertquery = 'alertCID=1';
			}
		else
			{var alertquery = 'alertCID=0';}
		var usegroupalias=0;
		var consultativexfer_checked = 0;
		if (document.vicidial_form.consultativexfer.checked==true)
			{consultativexfer_checked = 1;}
		var regCXFvars = new RegExp("CXFER","g");
		var tasknum_string = tasknum.toString();
		if ( (tasknum_string.match(regCXFvars)) || (consultativexfer_checked > 0) )
			{
			if (tasknum_string.match(regCXFvars))
				{
				var Ctasknum = tasknum_string.replace(regCXFvars, '');
				if (Ctasknum.length < 2)
					{Ctasknum = '90009';}
				var agentdirect = '';
				}
			else
				{
				Ctasknum = '90009';
				var agentdirect = tasknum_string;
				}
			var XfeRSelecT = document.getElementById("XfeRGrouP");
			var XfeR_GrouP = XfeRSelecT.value;
			if (API_selected_xfergroup.length > 1)
				{var XfeR_GrouP = API_selected_xfergroup;}
			tasknum = Ctasknum + "*" + XfeR_GrouP + '*CXFER*' + document.vicidial_form.lead_id.value + '**' + dialed_number + '*' + user + '*' + agentdirect + '*' + VD_live_call_secondS + '*';

			CustomerData_update();
			}
		var regAXFvars = new RegExp("AXFER","g");
		if (tasknum_string.match(regAXFvars))
			{
			var Ctasknum = tasknum_string.replace(regAXFvars, '');
			if (Ctasknum.length < 2)
				{Ctasknum = '83009';}
			var closerxfercamptail = '_L';
			if (closerxfercamptail.length < 3)
				{closerxfercamptail = 'IVR';}
			tasknum = Ctasknum + '*' + document.vicidial_form.phone_number.value + '*' + document.vicidial_form.lead_id.value + '*' + campaign + '*' + closerxfercamptail + '*' + user + '**' + VD_live_call_secondS + '*';

			CustomerData_update();

			}


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
			if (taskprefix == 'NO') {var call_prefix = '';}
			  else {var call_prefix = agc_dial_prefix;}

			if (prefix_choice.length > 0)
				{var call_prefix = prefix_choice;}

			if (taskreverse == 'YES')
				{
				if (taskdialvalue.length < 2)
					{var dialnum = dialplan_number;}
				else
					{var dialnum = taskdialvalue;}
				var call_prefix = '';
				var originatevalue = "Local/" + tasknum + "@" + ext_context;
				}
			  else 
				{
				var dialnum = tasknum;
				if ( (protocol == 'EXTERNAL') || (protocol == 'Local') )
					{
					var protodial = 'Local';
					var extendial = extension;
			//		var extendial = extension + "@" + ext_context;
					}
				else
					{
					var protodial = protocol;
					var extendial = extension;
					}
				var originatevalue = protodial + "/" + extendial;
				}

			var leadCID = document.vicidial_form.lead_id.value;
			var epochCID = epoch_sec;
			if (leadCID.length < 1)
				{leadCID = user_abb;}
			leadCID = set_length(leadCID,'10','left');
			epochCID = set_length(epochCID,'6','right');
			if (taskconfxfer == 'YES')
				{var queryCID = "DC" + epochCID + 'W' + leadCID + 'W';}
			else
				{var queryCID = "DV" + epochCID + 'W' + leadCID + 'W';}

	//		if (taskconfxfer == 'YES')
	//			{var queryCID = "DCagcW" + epoch_sec + user_abb;}
	//		else
	//			{var queryCID = "DVagcW" + epoch_sec + user_abb;}

			if (taskalert == '1')
				{
				queryCID = TAqueryCID;
				}

			if (cid_choice.length > 3) 
				{
				var call_cid = cid_choice;
				usegroupalias=1;
				}
			else 
				{
				if (taskcid.length > 3) 
					{var call_cid = taskcid;}
				else 
					{var call_cid = campaign_cid;}
				}

			VMCoriginate_query = "server_ip=" + server_ip + "&session_name=" + session_name + "&user=" + user + "&pass=" + pass + "&ACTION=Originate&format=text&channel=" + originatevalue + "&queryCID=" + queryCID + "&exten=" + call_prefix + "" + dialnum + "&ext_context=" + ext_context + "&ext_priority=1&outbound_cid=" + call_cid + "&usegroupalias="+ usegroupalias + "&preset_name=" + taskpresetname + "&campaign=" + campaign + "&account=" + active_group_alias + "&agent_dialed_number=" + agent_dialed_number + "&agent_dialed_type=" + agent_dialed_type + "&lead_id=" + document.vicidial_form.lead_id.value + "&stage=" + CheckDEADcallON + "&" + alertquery + "&call_variables=" + taskvariables;
			xmlhttp.open('POST', 'manager_send.php'); 
			xmlhttp.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
			xmlhttp.send(VMCoriginate_query); 
			xmlhttp.onreadystatechange = function() 
				{ 
				if (xmlhttp.readyState == 4 && xmlhttp.status == 200) 
					{
				//	alert(VMCoriginate_query);
				//	alert(xmlhttp.responseText);

					var regBOerr = new RegExp("ERROR","g");
					var BOresponse = xmlhttp.responseText;
					if (BOresponse.match(regBOerr))
						{
						alert_box(BOresponse);
						}

					if ((taskdialvalue.length > 0) && (tasknowait != 'YES'))
						{
						XDnextCID = queryCID;
						MD_channel_look=1;
						XDcheck = 'YES';

                                document.getElementById("HangupXferLine").innerHTML ="<a href=\"#\" onclick=\"xfercall_send_hangup();return false;\">Hangup 3rd Party</a>";
                                document.getElementById("HangupXferLine").className = "button blue";
						}
					}
				}
			delete xmlhttp;
			active_group_alias='';
			cid_choice='';
			prefix_choice='';
			agent_dialed_number='';
			agent_dialed_type='';
			CalL_ScripT_id='';
			call_variables='';
			}
		}


// ################################################################################
// zero-pad numbers or chop them to get to the desired length
function set_length(SLnumber,SLlength_goal,SLdirection)
	{
	var SLnumber = SLnumber + '';
	var begin_point=0;
	var number_length = SLnumber.length;
	if (number_length > SLlength_goal)
		{
		if (SLdirection == 'right')
			{
			begin_point = (number_length - SLlength_goal);
			SLnumber = SLnumber.substr(begin_point,SLlength_goal);
			}
		else
			{
			SLnumber = SLnumber.substr(0,SLlength_goal);
			}
		}
//	alert(SLnumber + '|' + SLlength_goal + '|' + begin_point + '|' + SLdirection + '|' + SLnumber.length + '|' + number_length);
	var result = SLnumber + '';
	while(result.length < SLlength_goal)
		{
		result = "0" + result;
		}
	return result;
	}


// ################################################################################
// filter conf_dtmf send string and pass on to originate call
	function SendConfDTMF(taskconfdtmf)
		{
		var dtmf_number = document.vicidial_form.conf_dtmf.value;
		var dtmf_string = dtmf_number.toString();
		var conf_dtmf_room = taskconfdtmf;

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
			var queryCID = dtmf_string;
			VMCoriginate_query = "server_ip=" + server_ip + "&session_name=" + session_name + "&user=" + user + "&pass=" + pass  + "&ACTION=SysCIDdtmfOriginate&format=text&channel=" + dtmf_send_extension + "&queryCID=" + queryCID + "&exten=" + dtmf_silent_prefix + '' + conf_dtmf_room + "&ext_context=" + ext_context + "&ext_priority=1";
			xmlhttp.open('POST', 'manager_send.php'); 
			xmlhttp.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
			xmlhttp.send(VMCoriginate_query); 
			xmlhttp.onreadystatechange = function() 
				{ 
				if (xmlhttp.readyState == 4 && xmlhttp.status == 200) 
					{
			//		alert(xmlhttp.responseText);
					}
				}
			delete xmlhttp;
			}
		document.vicidial_form.conf_dtmf.value = '';
		}

// ################################################################################
// Check to see if there are any channels live in the agent's conference meetme room
	function check_for_conf_calls(taskconfnum,taskforce)
		{
		if (typeof(xmlhttprequestcheckconf) == "undefined") {
			//alert (xmlhttprequestcheckconf == xmlhttpSendConf);
			xmlhttprequestcheckconf_wait = 0;
			custchannellive--;
			if ( (agentcallsstatus == '1') || (callholdstatus == '1') )
				{
				campagentstatct++;
				if (campagentstatct > campagentstatctmax) 
					{
					campagentstatct=0;
					var campagentstdisp = 'YES';
					}
				else
					{
					var campagentstdisp = 'NO';
					}
				}
			else
				{
				var campagentstdisp = 'NO';
				}

			xmlhttprequestcheckconf=false;
			/*@cc_on @*/
			/*@if (@_jscript_version >= 5)
			// JScript gives us Conditional compilation, we can cope with old IE versions.
			// and security blocked creation of the objects.
			 try {
			  xmlhttprequestcheckconf = new ActiveXObject("Msxml2.XMLHTTP");
			 } catch (e) {
			  try {
			   xmlhttprequestcheckconf = new ActiveXObject("Microsoft.XMLHTTP");
			  } catch (E) {
			   xmlhttprequestcheckconf = false;
			  }
			 }
			@end @*/
			//alert ("1");
			if (!xmlhttprequestcheckconf && typeof XMLHttpRequest!='undefined')
				{
				xmlhttprequestcheckconf = new XMLHttpRequest();
				}
			if (xmlhttprequestcheckconf) 
				{ 
				checkconf_query = "server_ip=" + server_ip + "&session_name=" + session_name + "&user=" + user + "&pass=" + pass + "&client=vdc&conf_exten=" + taskconfnum + "&auto_dial_level=" + auto_dial_level + "&campagentstdisp=" + campagentstdisp;
				xmlhttprequestcheckconf.open('POST', 'conf_exten_check.php'); 
				xmlhttprequestcheckconf.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
				xmlhttprequestcheckconf.send(checkconf_query); 
				xmlhttprequestcheckconf.onreadystatechange = function() 
					{
					if (xmlhttprequestcheckconf && xmlhttprequestcheckconf.readyState == 4 && xmlhttprequestcheckconf.status == 200) 
						{
						var check_conf = null;
						var LMAforce = taskforce;
						check_conf = xmlhttprequestcheckconf.responseText;
					//	alert(checkconf_query);
					//	alert(xmlhttprequestcheckconf.responseText);
						var check_ALL_array=check_conf.split("\n");
						var check_time_array=check_ALL_array[0].split("|");
						var Time_array = check_time_array[1].split("UnixTime: ");
						 UnixTime = Time_array[1];
						 UnixTime = parseInt(UnixTime);
						 UnixTimeMS = (UnixTime * 1000);
						t.setTime(UnixTimeMS);
						if ( (callholdstatus == '1') || (agentcallsstatus == '1') || (vicidial_agent_disable != 'NOT_ACTIVE') )
							{
							var Alogin_array = check_time_array[2].split("Logged-in: ");
							var AGLogiN = Alogin_array[1];
							var CamPCalLs_array = check_time_array[3].split("CampCalls: ");
							var CamPCalLs = CamPCalLs_array[1];
							var DiaLCalLs_array = check_time_array[5].split("DiaLCalls: ");
							var DiaLCalLs = DiaLCalLs_array[1];
							if (AGLogiN != 'N')
								{
								document.getElementById("AgentStatusStatus").innerHTML = AGLogiN;
								}
							if (CamPCalLs != 'N')
								{
								document.getElementById("AgentStatusCalls").innerHTML = CamPCalLs;
								}
							if (DiaLCalLs != 'N')
								{
								document.getElementById("AgentStatusDiaLs").innerHTML = DiaLCalLs;
								}
							if ( (AGLogiN == 'DEAD_VLA') && ( (vicidial_agent_disable == 'LIVE_AGENT') || (vicidial_agent_disable == 'ALL') ) )
								{
								showDiv('AgenTDisablEBoX');
								}
							if ( (AGLogiN == 'DEAD_EXTERNAL') && ( (vicidial_agent_disable == 'EXTERNAL') || (vicidial_agent_disable == 'ALL') ) )
								{
								showDiv('AgenTDisablEBoX');
								}
							if ( (AGLogiN == 'TIME_SYNC') && (vicidial_agent_disable == 'ALL') )
								{
								showDiv('SysteMDisablEBoX');
								}
							if (AGLogiN == 'SHIFT_LOGOUT')
								{
								shift_logout_flag=1;
								}
							}
                                                var CallsToday_array = check_time_array[25].split("CallsToday: ");
                                                document.getElementById('AgentCallsToday').innerHTML = CallsToday_array[1];
                                                var AvgWaitTime_array = check_time_array[26].split("AvgWaitTime: ");
                                                document.getElementById('AgentAvgWaitTime').innerHTML = AvgWaitTime_array[1];
                                                var AvgTalkTime_array = check_time_array[27].split("AvgTalkTime: ");
                                                document.getElementById('AgentAvgTalkTime').innerHTML = AvgTalkTime_array[1];
                                                
						var VLAStatuS_array = check_time_array[4].split("Status: ");
						var VLAStatuS = VLAStatuS_array[1];
						if ( (VLAStatuS == 'PAUSED') && (AutoDialWaiting == 1) )
							{
							if (PausENotifYCounTer > 30)
								{
								alert_box('Your session has been paused');
								AutoDial_ReSume_PauSe('VDADpause');
								PausENotifYCounTer=0;
								}
							else {PausENotifYCounTer++;}
							}
						else {PausENotifYCounTer=0;}

						var APIHanguP_array = check_time_array[6].split("APIHanguP: ");
						var APIHanguP = APIHanguP_array[1];
						var APIStatuS_array = check_time_array[7].split("APIStatuS: ");
						var APIStatuS = APIStatuS_array[1];
						var APIPausE_array = check_time_array[8].split("APIPausE: ");
						var APIPausE = APIPausE_array[1];
						var APIDiaL_array = check_time_array[9].split("APIDiaL: ");
						var APIDiaL = APIDiaL_array[1];
						var APIManualDialQueue_array = check_time_array[24].split("APIManualDialQueue: ");
						APIManualDialQueue = APIManualDialQueue_array[1];
						var CheckDEADcall_array = check_time_array[10].split("DEADcall: ");
						var CheckDEADcall = CheckDEADcall_array[1];
						var InGroupChange_array = check_time_array[11].split("InGroupChange: ");
						var InGroupChange = InGroupChange_array[1];
						var InGroupChangeBlend = check_time_array[12];
						var InGroupChangeUser = check_time_array[13];
						var InGroupChangeName = check_time_array[14];
						var APIFields_array = check_time_array[15].split("APIFields: ");
						update_fields = APIFields_array[1];
						var APIFieldsData_array = check_time_array[16].split("APIFieldsData: ");
						update_fields_data = APIFieldsData_array[1];
						var APITimerAction_array = check_time_array[17].split("APITimerAction: ");
						api_timer_action = APITimerAction_array[1];
						var APITimerMessage_array = check_time_array[18].split("APITimerMessage: ");
						api_timer_action_message = APITimerMessage_array[1];
						var APITimerSeconds_array = check_time_array[19].split("APITimerSeconds: ");
						api_timer_action_seconds = APITimerSeconds_array[1];
						var APITimerDestination_array = check_time_array[23].split("APITimerDestination: ");
						api_timer_action_destination = APITimerDestination_array[1];
						var APIdtmf_array = check_time_array[20].split("APIdtmf: ");
						api_dtmf = APIdtmf_array[1];
						var APItransfercond_array = check_time_array[21].split("APItransferconf: ");
						var api_transferconf_values_array = APItransfercond_array[1].split("---");
						api_transferconf_function = api_transferconf_values_array[0];
						api_transferconf_group = api_transferconf_values_array[1];
						api_transferconf_number = api_transferconf_values_array[2];
						api_transferconf_consultative = api_transferconf_values_array[3];
						api_transferconf_override = api_transferconf_values_array[4];
						var APIpark_array = check_time_array[22].split("APIpark: ");
						api_parkcustomer = APIpark_array[1];

						if (api_transferconf_function.length > 0)
							{
							if (api_transferconf_function == 'HANGUP_XFER')
								{xfercall_send_hangup();}
							if (api_transferconf_function == 'HANGUP_BOTH')
								{bothcall_send_hangup();}
							if (api_transferconf_function == 'LEAVE_VM')
								{mainxfer_send_redirect('XfeRVMAIL',lastcustchannel,lastcustserverip);}
							if (api_transferconf_function == 'LEAVE_3WAY_CALL')
								{leave_3way_call('FIRST');}
							if (api_transferconf_function == 'BLIND_TRANSFER')
								{
								document.vicidial_form.xfernumber.value = api_transferconf_number;
								mainxfer_send_redirect('XfeRBLIND',lastcustchannel,lastcustserverip);
								}
							if (api_transferconf_function == 'LOCAL_CLOSER')
								{
								API_selected_xfergroup = api_transferconf_group;
								document.vicidial_form.xfernumber.value = api_transferconf_number;
								mainxfer_send_redirect('XfeRLOCAL',lastcustchannel,lastcustserverip);
								}
							if (api_transferconf_function == 'DIAL_WITH_CUSTOMER')
								{
								if (api_transferconf_consultative=='YES')
									{document.vicidial_form.consultativexfer.checked=true;}
								if (api_transferconf_consultative=='NO')
									{document.vicidial_form.consultativexfer.checked=false;}
								if (api_transferconf_override=='YES')
									{document.vicidial_form.xferoverride.checked=true;}
								API_selected_xfergroup = api_transferconf_group;
								document.vicidial_form.xfernumber.value = api_transferconf_number;
								SendManualDial('YES');
								}
							if (api_transferconf_function == 'PARK_CUSTOMER_DIAL')
								{
								if (api_transferconf_consultative=='YES')
									{document.vicidial_form.consultativexfer.checked=true;}
								if (api_transferconf_consultative=='NO')
									{document.vicidial_form.consultativexfer.checked=false;}
								if (api_transferconf_override=='YES')
									{document.vicidial_form.xferoverride.checked=true;}
								API_selected_xfergroup = api_transferconf_group;
								document.vicidial_form.xfernumber.value = api_transferconf_number;
								xfer_park_dial();
								}
							Clear_API_Field('external_transferconf');
							}
						if (api_parkcustomer == 'PARK_CUSTOMER')
							{mainxfer_send_redirect('ParK',lastcustchannel,lastcustserverip);}
						if (api_parkcustomer == 'GRAB_CUSTOMER')
							{mainxfer_send_redirect('FROMParK',lastcustchannel,lastcustserverip);}
						if (api_parkcustomer == 'PARK_IVR_CUSTOMER')
							{mainxfer_send_redirect('ParKivr',lastcustchannel,lastcustserverip);}
						if (api_parkcustomer == 'GRAB_IVR_CUSTOMER')
							{mainxfer_send_redirect('FROMParKivr',lastcustchannel,lastcustserverip);}
						if (api_dtmf.length > 0)
							{
							var REGdtmfPOUND = new RegExp("P","g");
							var REGdtmfSTAR = new RegExp("S","g");
							var REGdtmfQUIET = new RegExp("Q","g");
							api_dtmf = api_dtmf.replace(REGdtmfPOUND, '#');
							api_dtmf = api_dtmf.replace(REGdtmfSTAR, '*');
							api_dtmf = api_dtmf.replace(REGdtmfQUIET, ',');
							document.vicidial_form.conf_dtmf.value = api_dtmf;
							SendConfDTMF(session_id);
							}

						if (api_timer_action.length > 2)
							{
							timer_action = api_timer_action;
							timer_action_message = api_timer_action_message;
							timer_action_seconds = api_timer_action_seconds;
							timer_action_destination = api_timer_action_destination;
						//	alert("TIMER_API:" + timer_action + '|' + timer_action_message + '|' + timer_action_seconds + '|' + timer_action_destination + '|');
							}
						if ( (APIHanguP==1) && (VD_live_customer_call==1) )
							{
							hideDiv('CustomerGoneBox');
							WaitingForNextStep=0;
							custchannellive=0;

							dialedcall_send_hangup();
							}
						if ( (APIStatuS.length < 10) && (APIStatuS.length > 0) && (AgentDispoing > 1) )
							{
							document.vicidial_form.DispoSelection.value = APIStatuS;
							DispoSelect_submit();
							}
						if (APIPausE.length > 4)
							{
							var APIPausE_array = APIPausE.split("!");
							if (APIPausE_ID == APIPausE_array[1])
								{
							//	alert("PAUSE ALREADY RECEIVED");
								}
							else
								{
								APIPausE_ID = APIPausE_array[1];
								if (APIPausE_array[0]=='PAUSE')
									{
									if (VD_live_customer_call==1)
										{
										// set to pause on next dispo
										document.vicidial_form.DispoSelectStop.checked=true;
									//	alert("Setting dispo to PAUSE");
										}
									else
										{
										if (AutoDialReady==1)
											{
											if (auto_dial_level != '0')
												{
												AutoDialWaiting = 0;
												AutoDial_ReSume_PauSe("VDADpause");
												}
											VICIDiaL_pause_calling = 1;
											}
										}
									}
								if ( (APIPausE_array[0]=='RESUME') && (AutoDialReady < 1) && (auto_dial_level > 0) )
									{
									AutoDialWaiting = 1;
									AutoDial_ReSume_PauSe("VDADready");
									}
								}
							}
						if ( (APIDiaL.length > 9) && (AllowManualQueueCalls == '0') )
							{
							APIManualDialQueue++;
							}
						if (APIManualDialQueue != APIManualDialQueue_last)
							{
							APIManualDialQueue_last = APIManualDialQueue;
                                                        document.getElementById("ManualQueueNotice").innerHTML = "<b><font color=\"red\" size=\"3\">Manual Queue: " + APIManualDialQueue + "</font></b><br />";
							}
						if ( (APIDiaL.length > 9) && (WaitingForNextStep == '0') && (AllowManualQueueCalls == '1') && (check_n > 2) )
							{
							var APIDiaL_array_detail = APIDiaL.split("!");
							if (APIDiaL_ID == APIDiaL_array_detail[6])
								{
							//	alert("DiaL ALREADY RECEIVED: " + APIDiaL_ID + "|" + APIDiaL_array_detail[5]);
								}
							else
								{
								APIDiaL_ID = APIDiaL_array_detail[6];
								document.vicidial_form.MDDiaLCodE.value = APIDiaL_array_detail[1];
								document.vicidial_form.phone_code.value = APIDiaL_array_detail[1];
								document.vicidial_form.MDPhonENumbeR.value = APIDiaL_array_detail[0];
								document.vicidial_form.vendor_lead_code.value = APIDiaL_array_detail[5];
								prefix_choice = APIDiaL_array_detail[7];
								active_group_alias = APIDiaL_array_detail[8];
								cid_choice = APIDiaL_array_detail[9];
								vtiger_callback_id = APIDiaL_array_detail[10];
								document.vicidial_form.MDLeadID.value = APIDiaL_array_detail[11];
								document.vicidial_form.MDType.value = APIDiaL_array_detail[12];

							//	alert(APIDiaL_array_detail[1] + "-----" + APIDiaL + "-----" + document.vicidial_form.MDDiaLCodE.value + "-----" + document.vicidial_form.phone_code.value);

								if (APIDiaL_array_detail[2] == 'YES')  // lookup lead in system
									{document.vicidial_form.LeadLookuP.checked=true;}
								else
									{document.vicidial_form.LeadLookuP.checked=false;}
								if (APIDiaL_array_detail[4] == 'YES')  // focus on vicidial agent screen
									{window.focus();   alert_box("Placing call to:" + APIDiaL_array_detail[1] + " " + APIDiaL_array_detail[0]);}
								if (APIDiaL_array_detail[3] == 'YES')  // call preview
									{NeWManuaLDiaLCalLSubmiT('PREVIEW');}
								else
									{NeWManuaLDiaLCalLSubmiT('NOW');}
								}
							}

						if ( (CheckDEADcall > 0) && (VD_live_customer_call==1) )
							{
							if (CheckDEADcallON < 1)
								{
								//if( document.images ) 
								//	{ document.images['livecall'].src = image_livecall_DEAD.src;}
                                                                document.getElementById('livecall').innerHTML = 'Caller Hungup';
                                                                document.getElementById('livecall').className = 'dead';
                                                                
                                                                // reset hangup in case we had an End Call action
                                                                document.getElementById("HangupControl").innerHTML = "<a href=\"#\" onclick=\"dialedcall_send_hangup();\"><img src=\"./images/x-endcall-on.png\" border=\"0\" alt=\"Hangup Customer\" /><span>End Call</span></a>";
                                
								CheckDEADcallON=1;

								if ( (xfer_in_call > 0) && (customer_3way_hangup_logging=='ENABLED') )
									{
									customer_3way_hangup_counter_trigger=1;
									customer_3way_hangup_counter=1;
									}
								}
							}
						if (InGroupChange > 0)
							{
							var external_blended = InGroupChangeBlend;
							var external_igb_set_user = InGroupChangeUser;
							external_igb_set_name = InGroupChangeName;
							manager_ingroups_set=1;

							if ( (external_blended == '1') && (dial_method != 'INBOUND_MAN') )
								{VICIDiaL_closer_blended = '1';}

							if (external_blended == '0')
								{VICIDiaL_closer_blended = '0';}
							}

						var check_conf_array=check_ALL_array[1].split("|");
						var live_conf_calls = check_conf_array[0];
						var conf_chan_array = check_conf_array[1].split(" ~");
						if ( (conf_channels_xtra_display == 1) || (conf_channels_xtra_display == 0) )
							{
							if (live_conf_calls > 0)
								{
								var temp_blind_monitors=0;
								var loop_ct=0;
								var ARY_ct=0;
								var LMAalter=0;
								var LMAcontent_change=0;
								var LMAcontent_match=0;
								agentphonelive=0;
								var conv_start=-1;
                                                                var live_conf_HTML = "<font face=\"Arial,Helvetica\"><b>Current Calls in Your Conference Room:</b></font><br /><table><tr bgcolor=\"<?php echo $SCRIPT_COLOR ?>\"><td><font class=\"log_title\">#</font></td><td><font class=\"log_title\">REMOTE CHANNEL</font></td><td><font class=\"log_title\">HANGUP</font></td><td><font class=\"log_title\">VOLUME</font></td></tr>";
								if ( (LMAcount > live_conf_calls)  || (LMAcount < live_conf_calls) || (LMAforce > 0))
									{
									LMAe[0]=''; LMAe[1]=''; LMAe[2]=''; LMAe[3]=''; LMAe[4]=''; LMAe[5]=''; 
									LMAcount=0;   LMAcontent_change++;
									}
								while (loop_ct < live_conf_calls)
									{
									loop_ct++;
									loop_s = loop_ct.toString();
									if (loop_s.match(/1$|3$|5$|7$|9$/)) 
										{var row_color = '#DDDDFF';}
									else
										{var row_color = '#CCCCFF';}
									var conv_ct = (loop_ct + conv_start);
									var channelfieldA = conf_chan_array[conv_ct];
									var regXFcred = new RegExp(flag_string,"g");
									var regRNnolink = new RegExp('Local/5' + taskconfnum,"g")
									if ( (channelfieldA.match(regXFcred)) && (flag_channels>0) )
										{
										var chan_name_color = 'log_text_red';
										}
									else
										{
										var chan_name_color = 'log_text';
										}
									if ( (HidEMonitoRSessionS==1) && (channelfieldA.match(/ASTblind/)) )
										{
										var hide_channel=1;
										blind_monitoring_now++;
										temp_blind_monitors++;
										if (blind_monitoring_now==1)
											{blind_monitoring_now_trigger=1;}
										}
									else
										{
										if (channelfieldA.match(regRNnolink))
											{
											// do not show hangup or volume control links for recording channels
											live_conf_HTML = live_conf_HTML + "<tr bgcolor=\"" + row_color + "\"><td><font class=\"log_text\">" + loop_ct + "</font></td><td><font class=\"" + chan_name_color + "\">" + channelfieldA + "</font></td><td><font class=\"log_text\">recording</font></td><td></td></tr>";
											}
										else
											{
											if (volumecontrol_active!=1)
												{
												live_conf_HTML = live_conf_HTML + "<tr bgcolor=\"" + row_color + "\"><td><font class=\"log_text\">" + loop_ct + "</font></td><td><font class=\"" + chan_name_color + "\">" + channelfieldA + "</font></td><td><font class=\"log_text\"><a href=\"#\" onclick=\"livehangup_send_hangup('" + channelfieldA + "');return false;\">Hang Up</a></font></td><td></td></tr>";
												}
											else
												{
                                                                                                live_conf_HTML = live_conf_HTML + "<tr bgcolor=\"" + row_color + "\"><td><font class=\"log_text\">" + loop_ct + "</font></td><td><font class=\"" + chan_name_color + "\">" + channelfieldA + "</font></td><td><font class=\"log_text\"><a href=\"#\" onclick=\"livehangup_send_hangup('" + channelfieldA + "');return false;\">HANGUP</a></font></td><td><a href=\"#\" onclick=\"volume_control('UP','" + channelfieldA + "','');return false;\"><img src=\"./images/vdc_volume_up.gif\" border=\"0\" /></a> &nbsp; <a href=\"#\" onclick=\"volume_control('DOWN','" + channelfieldA + "','');return false;\"><img src=\"./images/vdc_volume_down.gif\" border=\"0\" /></a> &nbsp; &nbsp; &nbsp; <a href=\"#\" onclick=\"volume_control('MUTING','" + channelfieldA + "','');return false;\"><img src=\"./images/vdc_volume_MUTE.gif\" border=\"0\" /></a> &nbsp; <a href=\"#\" onclick=\"volume_control('UNMUTE','" + channelfieldA + "','');return false;\"><img src=\"./images/vdc_volume_UNMUTE.gif\" border=\"0\" /></a></td></tr>";
												}
											}
										}
				//		var debugspan = document.getElementById("debugbottomspan").innerHTML;

									if (channelfieldA == lastcustchannel) {custchannellive++;}
									else
										{
										if(customerparked == 1)
											{custchannellive++;}
										// allow for no customer hungup errors if call from another server
										if(server_ip == lastcustserverip)
											{var nothing='';}
										else
											{custchannellive++;}
										}

									if (volumecontrol_active > 0)
										{
										if ( (protocol != 'EXTERNAL') && (protocol != 'Local') )
											{
											var regAGNTchan = new RegExp(protocol + '/' + extension,"g");
											if  ( (channelfieldA.match(regAGNTchan)) && (agentchannel != channelfieldA) )
												{
												agentchannel = channelfieldA;

                                                                                                document.getElementById("AgentMuteSpan").innerHTML = "<a href=\"#CHAN-" + agentchannel + "\" onclick=\"volume_control('MUTING','" + agentchannel + "','AgenT');return false;\"><img style=\"height:35px;\" src=\"./images/x-volume-mute.png\" border=\"0\" /></a>";
												}
											}
										else							
											{
											if (agentchannel.length < 3)
												{
												agentchannel = channelfieldA;

                                                                                                document.getElementById("AgentMuteSpan").innerHTML = "<a href=\"#CHAN-" + agentchannel + "\" onclick=\"volume_control('MUTING','" + agentchannel + "','AgenT');return false;\"><img style=\"height:35px;\" src=\"./images/x-volume-mute.png\" border=\"0\" /></a>";
												}
											}
							//			document.getElementById("agentchannelSPAN").innerHTML = agentchannel;
										}

                //      document.getElementById("debugbottomspan").innerHTML = debugspan + '<br />' + channelfieldA + '|' + lastcustchannel + '|' + custchannellive + '|' + LMAcontent_change + '|' + LMAalter;

									if (!LMAe[ARY_ct]) 
										{LMAe[ARY_ct] = channelfieldA;   LMAcontent_change++;  LMAalter++;}
									else
										{
										if (LMAe[ARY_ct].length < 1) 
											{LMAe[ARY_ct] = channelfieldA;   LMAcontent_change++;  LMAalter++;}
										else
											{
											if (LMAe[ARY_ct] == channelfieldA) {LMAcontent_match++;}
											 else {LMAcontent_change++;   LMAe[ARY_ct] = channelfieldA;}
											}
										}
									if (LMAalter > 0) {LMAcount++;}
									
									if (agentchannel == channelfieldA) {agentphonelive++;}

									ARY_ct++;
									}
		//	var debug_LMA = LMAcontent_match+"|"+LMAcontent_change+"|"+LMAcount+"|"+live_conf_calls+"|"+LMAe[0]+LMAe[1]+LMAe[2]+LMAe[3]+LMAe[4]+LMAe[5];
        //                          document.getElementById("confdebug").innerHTML = debug_LMA + "<br />";

								if (agentphonelive < 1) {agentchannel='';}

								live_conf_HTML = live_conf_HTML + "</table>";

								if (LMAcontent_change > 0)
									{
									if (conf_channels_xtra_display == 1)
										{document.getElementById("outboundcallsspan").innerHTML = live_conf_HTML;}
									}
								nochannelinsession=0;
								if (temp_blind_monitors < 1)
									{
									no_blind_monitors++;
									if (no_blind_monitors > 2)
										{blind_monitoring_now=0;}
									}
								}
							else
								{
								LMAe[0]=''; LMAe[1]=''; LMAe[2]=''; LMAe[3]=''; LMAe[4]=''; LMAe[5]=''; 
								LMAcount=0;
								if (conf_channels_xtra_display == 1)
									{
									if (document.getElementById("outboundcallsspan").innerHTML.length > 2)
										{
										document.getElementById("outboundcallsspan").innerHTML = '';
										}
									}
								custchannellive = -99;
								nochannelinsession++;

								no_blind_monitors++;
								if (no_blind_monitors > 2)
									{blind_monitoring_now=0;}
								}
							}
							delete xmlhttprequestcheckconf;
							xmlhttprequestcheckconf = undefined; 
						}
					else if (xmlhttprequestcheckconf && xmlhttprequestcheckconf.readyState == 4 && xmlhttprequestcheckconf.status != 200) 
						{
						// Cleanup  after AJAX Request returns error.
						// alert("Status: " + xmlhttprequestcheckconf.status);
						delete xmlhttprequestcheckconf;
						xmlhttprequestcheckconf = undefined;
						}
					}
				}
			}
		else 
			{
			if (xmlhttprequestcheckconf) 
				{
				xmlhttprequestcheckconf_wait++;
				if (xmlhttprequestcheckconf_wait >= conf_check_attempts) 
					{
					// Abort AJAX Request, due to timeout.
					// The handler must take care of cleanup.
					// alert("xmlhttprequestcheckconf: Abort (Wait > 3 sec)");
					xmlhttprequestcheckconf.abort();
					}
				}
			if (xmlhttprequestcheckconf_wait >= conf_check_attempts_cleanup) 
				{
				// In case the handler function fails to do cleanup, cleanup manually.
				xmlhttprequestcheckconf_wait = 0;
				delete xmlhttprequestcheckconf;
				xmlhttprequestcheckconf = undefined;
				}
			else 
				{
				xmlhttprequestcheckconf = undefined;
				}
			}
		}


// ################################################################################
// Send MonitorConf/StopMonitorConf command for recording of conferences
	function conf_send_recording(taskconfrectype,taskconfrec,taskconffile) 
		{
		if (inOUT == 'OUT')
			{
			tmp_vicidial_id = document.vicidial_form.uniqueid.value;
			}
		else
			{
			tmp_vicidial_id = 'IN';
			}
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
			if (taskconfrectype == 'MonitorConf')
				{
				var REGrecCLEANvlc = new RegExp(" ","g");
				var recVendorLeadCode = document.vicidial_form.vendor_lead_code.value;
				recVendorLeadCode = recVendorLeadCode.replace(REGrecCLEANvlc, '');
				var recLeadID = document.vicidial_form.lead_id.value;

				// 	var campaign_recording = '<?php echo $campaign_recording ?>';
				//	var campaign_rec_filename = '<?php echo $campaign_rec_filename ?>';
				//	CAMPAIGN CUSTPHONE FULLDATE TINYDATE EPOCH AGENT VENDORLEADCODE LEADID
				var REGrecCAMPAIGN = new RegExp("CAMPAIGN","g");
				var REGrecCUSTPHONE = new RegExp("CUSTPHONE","g");
				var REGrecFULLDATE = new RegExp("FULLDATE","g");
				var REGrecTINYDATE = new RegExp("TINYDATE","g");
				var REGrecEPOCH = new RegExp("EPOCH","g");
				var REGrecAGENT = new RegExp("AGENT","g");
				var REGrecVENDORLEADCODE = new RegExp("VENDORLEADCODE","g");
				var REGrecLEADID = new RegExp("LEADID","g");
				filename = LIVE_campaign_rec_filename;
				filename = filename.replace(REGrecCAMPAIGN, campaign);
				filename = filename.replace(REGrecCUSTPHONE, lead_dial_number);
				filename = filename.replace(REGrecFULLDATE, filedate);
				filename = filename.replace(REGrecTINYDATE, tinydate);
				filename = filename.replace(REGrecEPOCH, epoch_sec);
				filename = filename.replace(REGrecAGENT, user);
				filename = filename.replace(REGrecVENDORLEADCODE, recVendorLeadCode);
				filename = filename.replace(REGrecLEADID, recLeadID);
			//	filename = filedate + "_" + user_abb;
				var query_recording_exten = recording_exten;
				var channelrec = "Local/" + conf_silent_prefix + '' + taskconfrec + "@" + ext_context;
                var conf_rec_start_html = "<a href=\"#\" onclick=\"conf_send_recording('StopMonitorConf','" + taskconfrec + "','" + filename + "');return false;\"><img src=\"./images/x-record-stop.png\" border=\"0\" alt=\"Stop Recording\" /><span id=\"x-recording\">Stop Recording</span></a>";

				if (LIVE_campaign_recording == 'ALLFORCE')
					{
                    document.getElementById("RecorDControl").innerHTML = "<a href=\"#\" style=\"opacity:.15;\" disabled><img class=\"disabled_img\" src=\"./images/x-record.png\" border=\"0\" alt=\"Start Recording\" /><br /><span id=\"x-recording\">Record</span></a>";
					}
				else
					{
					document.getElementById("RecorDControl").innerHTML = conf_rec_start_html;
					}
			}
			if (taskconfrectype == 'StopMonitorConf')
				{
				filename = taskconffile;
				var query_recording_exten = session_id;
				var channelrec = "Local/" + conf_silent_prefix + '' + taskconfrec + "@" + ext_context;
                var conf_rec_start_html = "<a href=\"#\" onclick=\"conf_send_recording('MonitorConf','" + taskconfrec + "','');return false;\"><img src=\"./images/x-record.png\" border=\"0\" alt=\"Start Recording\" /><span id=\"x-recording\">Record</span></a>";
				if (LIVE_campaign_recording == 'ALLFORCE')
					{
                    document.getElementById("RecorDControl").innerHTML = "<a href=\"#\" style=\"opacity:.15;\" disabled><img class=\"disabled_img\" src=\"./images/x-record.png\" border=\"0\" alt=\"Start Recording\" /><br /><span id=\"x-recording\">Record</span></a>";
					}
				else
					{
					document.getElementById("RecorDControl").innerHTML = conf_rec_start_html;
					}
				}
			confmonitor_query = "server_ip=" + server_ip + "&session_name=" + session_name + "&user=" + user + "&pass=" + pass + "&ACTION=" + taskconfrectype + "&format=text&channel=" + channelrec + "&filename=" + filename + "&exten=" + query_recording_exten + "&ext_context=" + ext_context + "&lead_id=" + document.vicidial_form.lead_id.value + "&ext_priority=1&FROMvdc=YES&uniqueid=" + tmp_vicidial_id;
			xmlhttp.open('POST', 'manager_send.php'); 
			xmlhttp.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
			xmlhttp.send(confmonitor_query); 
			xmlhttp.onreadystatechange = function() 
				{ 
				if (xmlhttp.readyState == 4 && xmlhttp.status == 200) 
					{
					var RClookResponse = null;
			//	document.getElementById("busycallsdebug").innerHTML = confmonitor_query;
			//		alert(xmlhttp.responseText);
					RClookResponse = xmlhttp.responseText;
					var RClookResponse_array=RClookResponse.split("\n");
					var RClookFILE = RClookResponse_array[1];
					var RClookID = RClookResponse_array[2];
					var RClookFILE_array = RClookFILE.split("Filename: ");
					var RClookID_array = RClookID.split("RecorDing_ID: ");
					if (RClookID_array.length > 0)
						{
						recording_filename = RClookFILE_array[1];
						recording_id = RClookID_array[1];

						if (delayed_script_load == 'YES')
							{
							RefresHScript();
							delayed_script_load='NO';
							}

						var RecDispNamE = RClookFILE_array[1];
						if (RecDispNamE.length > 25)
							{
							RecDispNamE = RecDispNamE.substr(0,22);
							RecDispNamE = RecDispNamE + '...';
							}
						document.getElementById("RecorDingFilename").innerHTML = '<b>Recording: </b>' + RecDispNamE + '<br />';
						document.getElementById("RecorDID").innerHTML = '<b>Recording Id: </b>' + RClookID_array[1] + '<br />';
						}
					}
				}
			delete xmlhttp;
			}
		}

// ################################################################################
// Send Redirect command for live call to Manager sends phone name where call is going to
// Covers the following types: XFER, VMAIL, ENTRY, CONF, PARK, FROMPARK, XfeRLOCAL, XfeRINTERNAL, XfeRBLIND, VfeRVMAIL, XfeRPARK, XfeRFROMPARK
	function mainxfer_send_redirect(taskvar,taskxferconf,taskserverip,taskdebugnote,taskdispowindow,tasklockedquick,taskispreset) 
		{
		blind_transfer=1;
		var consultativexfer_checked = 0;
		if (document.vicidial_form.consultativexfer.checked==true)
			{consultativexfer_checked = 1;}
                        
                if (taskispreset == 1 && (quick_transfer_button_enabled == 1 || end_call_action_enabled == 1)) //ensures that we do not pick up an old transfer number value
                {
                    if ( (quick_transfer_button == 'PRESET_1') || (quick_transfer_button == 'LOCKED_PRESET_1') || (end_call_action == 'PRESET_1') )
                            {document.vicidial_form.xfernumber.value = CalL_XC_a_NuMber;   document.vicidial_form.xfername.value='D1';}
                    if ( (quick_transfer_button == 'PRESET_2') || (quick_transfer_button == 'LOCKED_PRESET_2') || (end_call_action == 'PRESET_2') )
                            {document.vicidial_form.xfernumber.value = CalL_XC_b_NuMber;   document.vicidial_form.xfername.value='D2';}
                    if ( (quick_transfer_button == 'PRESET_3') || (quick_transfer_button == 'LOCKED_PRESET_3') || (end_call_action == 'PRESET_3') )
                            {document.vicidial_form.xfernumber.value = CalL_XC_c_NuMber;   document.vicidial_form.xfername.value='D3';}
                    if ( (quick_transfer_button == 'PRESET_4') || (quick_transfer_button == 'LOCKED_PRESET_4') || (end_call_action == 'PRESET_4') )
                            {document.vicidial_form.xfernumber.value = CalL_XC_d_NuMber;   document.vicidial_form.xfername.value='D4';}
                    if ( (quick_transfer_button == 'PRESET_5') || (quick_transfer_button == 'LOCKED_PRESET_5') || (end_call_action == 'PRESET_5') )
                            {document.vicidial_form.xfernumber.value = CalL_XC_e_NuMber;   document.vicidial_form.xfername.value='D5';}
                }

	//	conf_dialed=1;
		if (auto_dial_level == 0) {RedirecTxFEr = 1;}
		var xmlhttpXF=false;
		/*@cc_on @*/
		/*@if (@_jscript_version >= 5)
		// JScript gives us Conditional compilation, we can cope with old IE versions.
		// and security blocked creation of the objects.
		 try {
		  xmlhttpXF = new ActiveXObject("Msxml2.XMLHTTP");
		 } catch (e) {
		  try {
		   xmlhttpXF = new ActiveXObject("Microsoft.XMLHTTP");
		  } catch (E) {
		   xmlhttpXF = false;
		  }
		 }
		@end @*/
		if (!xmlhttpXF && typeof XMLHttpRequest!='undefined')
			{
			xmlhttpXF = new XMLHttpRequest();
			}
		if (xmlhttpXF) 
			{ 
			var redirectvalue = MDchannel;
			var redirectserverip = lastcustserverip;
			if (redirectvalue.length < 2)
				{redirectvalue = lastcustchannel}
			if ( (taskvar == 'XfeRBLIND') || (taskvar == 'XfeRVMAIL') )
				{
				if (tasklockedquick > 0)
					{document.vicidial_form.xfernumber.value = quick_transfer_button_orig;}
				var queryCID = "XBvdcW" + epoch_sec + user_abb;
				var blindxferdialstring = document.vicidial_form.xfernumber.value;
				var blindxferhiddendialstring = document.vicidial_form.xfernumhidden.value;
				if ( (blindxferdialstring.length < 1) && (blindxferhiddendialstring.length > 0) )
					{blindxferdialstring=blindxferhiddendialstring;}
				var regXFvars = new RegExp("XFER","g");
				if (blindxferdialstring.match(regXFvars))
					{
					var regAXFvars = new RegExp("AXFER","g");
					if (blindxferdialstring.match(regAXFvars))
						{
						var Ctasknum = blindxferdialstring.replace(regAXFvars, '');
						if (Ctasknum.length < 2)
							{Ctasknum = '83009';}
						var closerxfercamptail = '_L';
						if (closerxfercamptail.length < 3)
							{closerxfercamptail = 'IVR';}
						blindxferdialstring = Ctasknum + '*' + document.vicidial_form.phone_number.value + '*' + document.vicidial_form.lead_id.value + '*' + campaign + '*' + closerxfercamptail + '*' + user + '**' + VD_live_call_secondS + '*';
						}
					}
				else
					{
					if (document.vicidial_form.xferoverride.checked==false)
						{
						if (three_way_dial_prefix == 'X') {var temp_dial_prefix = '';}
						else {var temp_dial_prefix = three_way_dial_prefix;}
						if (omit_phone_code == 'Y') {var temp_phone_code = '';}
						else {var temp_phone_code = document.vicidial_form.phone_code.value;}

						if (blindxferdialstring.length > 7)
							{blindxferdialstring = temp_dial_prefix + "" + temp_phone_code + "" + blindxferdialstring;}
						}
					}
				if (API_selected_callmenu.length > 0)
					{
					var blindxferdialstring = 's';
					var blindxfercontext = document.vicidial_form.xfernumber.value;
					}
				else
					{var blindxfercontext = ext_context;}
				no_delete_VDAC=0;
				if (taskvar == 'XfeRVMAIL')
					{
					var blindxferdialstring = campaign_am_message_exten + '*' + campaign + '*' + document.vicidial_form.phone_code.value + '*' + document.vicidial_form.phone_number.value + '*' + document.vicidial_form.lead_id.value;
					no_delete_VDAC=1;
					}
				if (blindxferdialstring.length<'1')
					{
					xferredirect_query='';
					taskvar = 'NOTHING';
					alert_box("Transfer number must have at least 1 digit:" + blindxferdialstring);
					}
				else
					{
					xferredirect_query = "server_ip=" + server_ip + "&session_name=" + session_name + "&user=" + user + "&pass=" + pass + "&ACTION=RedirectVD&format=text&channel=" + redirectvalue + "&call_server_ip=" + redirectserverip + "&queryCID=" + queryCID + "&exten=" + blindxferdialstring + "&ext_context=" + blindxfercontext + "&ext_priority=1&auto_dial_level=" + auto_dial_level + "&campaign=" + campaign + "&uniqueid=" + document.vicidial_form.uniqueid.value + "&lead_id=" + document.vicidial_form.lead_id.value + "&secondS=" + VD_live_call_secondS + "&session_id=" + session_id + "&nodeletevdac=" + no_delete_VDAC + "&preset_name=" + document.vicidial_form.xfername.value;
					}
				}
			if (taskvar == 'XfeRINTERNAL') 
				{
				var closerxferinternal = '';
				taskvar = 'XfeRLOCAL';
				}
			else 
				{
				var closerxferinternal = '9';
				}
			if (taskvar == 'XfeRLOCAL')
				{
				CustomerData_update();

				document.vicidial_form.xfername.value='';
				var XfeRSelecT = document.getElementById("XfeRGrouP");
				var XfeR_GrouP = XfeRSelecT.value;
				if (API_selected_xfergroup.length > 1)
					{var XfeR_GrouP = API_selected_xfergroup;}
				if (tasklockedquick > 0)
					{XfeR_GrouP = quick_transfer_button_orig;}
				var queryCID = "XLvdcW" + epoch_sec + user_abb;
				// 		 "90009*$group**$lead_id**$phone_number*$user*$agent_only*";
				var redirectdestination = closerxferinternal + '90009*' + XfeR_GrouP + '**' + document.vicidial_form.lead_id.value + '**' + dialed_number + '*' + user + '*' + document.vicidial_form.xfernumber.value + '*';


				xferredirect_query = "server_ip=" + server_ip + "&session_name=" + session_name + "&user=" + user + "&pass=" + pass + "&ACTION=RedirectVD&format=text&channel=" + redirectvalue + "&call_server_ip=" + redirectserverip + "&queryCID=" + queryCID + "&exten=" + redirectdestination + "&ext_context=" + ext_context + "&ext_priority=1&auto_dial_level=" + auto_dial_level + "&campaign=" + campaign + "&uniqueid=" + document.vicidial_form.uniqueid.value + "&lead_id=" + document.vicidial_form.lead_id.value + "&secondS=" + VD_live_call_secondS + "&session_id=" + session_id;
				}
			if (taskvar == 'XfeR')
				{
				var queryCID = "LRvdcW" + epoch_sec + user_abb;
				var redirectdestination = document.vicidial_form.extension_xfer.value;
				xferredirect_query = "server_ip=" + server_ip + "&session_name=" + session_name + "&user=" + user + "&pass=" + pass + "&ACTION=RedirectName&format=text&channel=" + redirectvalue + "&call_server_ip=" + redirectserverip + "&queryCID=" + queryCID + "&extenName=" + redirectdestination + "&ext_context=" + ext_context + "&ext_priority=1" + "&session_id=" + session_id;
				}
			if (taskvar == 'VMAIL')
				{
				var queryCID = "LVvdcW" + epoch_sec + user_abb;
				var redirectdestination = document.vicidial_form.extension_xfer.value;
				xferredirect_query = "server_ip=" + server_ip + "&session_name=" + session_name + "&user=" + user + "&pass=" + pass + "&ACTION=RedirectNameVmail&format=text&channel=" + redirectvalue + "&call_server_ip=" + redirectserverip + "&queryCID=" + queryCID + "&exten=" + voicemail_dump_exten + "&extenName=" + redirectdestination + "&ext_context=" + ext_context + "&ext_priority=1" + "&session_id=" + session_id;
				}
			if (taskvar == 'ENTRY')
				{
				var queryCID = "LEvdcW" + epoch_sec + user_abb;
				var redirectdestination = document.vicidial_form.extension_xfer_entry.value;
				xferredirect_query = "server_ip=" + server_ip + "&session_name=" + session_name + "&user=" + user + "&pass=" + pass + "&ACTION=Redirect&format=text&channel=" + redirectvalue + "&call_server_ip=" + redirectserverip + "&queryCID=" + queryCID + "&exten=" + redirectdestination + "&ext_context=" + ext_context + "&ext_priority=1" + "&session_id=" + session_id;
				}
			if (taskvar == '3WAY')
				{
				xferredirect_query='';

				var queryCID = "VXvdcW" + epoch_sec + user_abb;
				var redirectdestination = "NEXTAVAILABLE";
				var redirectXTRAvalue = XDchannel;
				var redirecttype_test = document.vicidial_form.xfernumber.value;
				var XfeRSelecT = document.getElementById("XfeRGrouP");
				var XfeR_GrouP = XfeRSelecT.value;
				if (API_selected_xfergroup.length > 1)
					{var XfeR_GrouP = API_selected_xfergroup;}
				var regRXFvars = new RegExp("CXFER","g");
				if ( ( (redirecttype_test.match(regRXFvars)) || (consultativexfer_checked > 0) ) && (local_consult_xfers > 0) )
					{var redirecttype = 'RedirectXtraCXNeW';}
				else
					{var redirecttype = 'RedirectXtraNeW';}
				DispO3waychannel = redirectvalue;
				DispO3wayXtrAchannel = redirectXTRAvalue;
				DispO3wayCalLserverip = redirectserverip;
				DispO3wayCalLxfernumber = document.vicidial_form.xfernumber.value;
				DispO3wayCalLcamptail = '';

				xferredirect_query = "server_ip=" + server_ip + "&session_name=" + session_name + "&user=" + user + "&pass=" + pass + "&ACTION=" + redirecttype + "&format=text&channel=" + redirectvalue + "&call_server_ip=" + redirectserverip + "&queryCID=" + queryCID + "&exten=" + redirectdestination + "&ext_context=" + ext_context + "&ext_priority=1&extrachannel=" + redirectXTRAvalue + "&lead_id=" + document.vicidial_form.lead_id.value + "&phone_code=" + document.vicidial_form.phone_code.value + "&phone_number=" + document.vicidial_form.phone_number.value + "&filename=" + taskdebugnote + "&campaign=" + XfeR_GrouP + "&session_id=" + session_id + "&agentchannel=" + agentchannel + "&protocol=" + protocol + "&extension=" + extension + "&auto_dial_level=" + auto_dial_level;

				if (taskdebugnote == 'FIRST') 
					{
					document.getElementById("DispoSelectHAspan").innerHTML = "<a href=\"#\" onclick=\"DispoLeavE3wayAgaiN()\">Leave 3Way Call Again</a>";
					}
				}
			if (taskvar == 'ParK')
				{
				if (CalLCID.length < 1)
					{
					CalLCID = MDnextCID;
					}
				blind_transfer=0;
				var queryCID = "LPvdcW" + epoch_sec + user_abb;
				var redirectdestination = taskxferconf;
				var redirectdestserverip = taskserverip;
				var parkedby = protocol + "/" + extension;
				xferredirect_query = "server_ip=" + server_ip + "&session_name=" + session_name + "&user=" + user + "&pass=" + pass + "&ACTION=RedirectToPark&format=text&channel=" + redirectdestination + "&call_server_ip=" + redirectdestserverip + "&queryCID=" + queryCID + "&exten=" + park_on_extension + "&ext_context=" + ext_context + "&ext_priority=1&extenName=park&parkedby=" + parkedby + "&session_id=" + session_id + "&CalLCID=" + CalLCID + "&uniqueid=" + document.vicidial_form.uniqueid.value + "&lead_id=" + document.vicidial_form.lead_id.value + "&campaign=" + campaign;

                document.getElementById("ParkControl").innerHTML ="<a href=\"#\" onclick=\"mainxfer_send_redirect('FROMParK','" + redirectdestination + "','" + redirectdestserverip + "');return false;\"><img style=\"margin-left:-10px;margin-right:-10px;\" src=\"./images/x-grabhold.png\" border=\"0\" alt=\"Grab Parked Call\" /><span>Grab Parked Call</span></a>";
				if ( (ivr_park_call=='ENABLED') || (ivr_park_call=='ENABLED_PARK_ONLY') )
					{
                    document.getElementById("ivrParkControl").innerHTML ="<a href=\"#\" style=\"opacity:.15;\" disabled><img class=\"disabled_img\" src=\"./images/x-holdivr.png\" border=\"0\" alt=\"IVR Park Call\" /><span>IVR Hold</span></a>";
					}
				customerparked=1;
				customerparkedcounter=0;
				}
			if (taskvar == 'FROMParK')
				{
				blind_transfer=0;
				var queryCID = "FPvdcW" + epoch_sec + user_abb;
				var redirectdestination = taskxferconf;
				var redirectdestserverip = taskserverip;

				if( (server_ip == taskserverip) && (taskserverip.length > 6) )
					{var dest_dialstring = session_id;}
				else
					{
					if(taskserverip.length > 6)
						{var dest_dialstring = server_ip_dialstring + "" + session_id;}
					else
						{var dest_dialstring = session_id;}
					}

				xferredirect_query = "server_ip=" + server_ip + "&session_name=" + session_name + "&user=" + user + "&pass=" + pass + "&ACTION=RedirectFromPark&format=text&channel=" + redirectdestination + "&call_server_ip=" + redirectdestserverip + "&queryCID=" + queryCID + "&exten=" + dest_dialstring + "&ext_context=" + ext_context + "&ext_priority=1" + "&session_id=" + session_id + "&CalLCID=" + CalLCID + "&uniqueid=" + document.vicidial_form.uniqueid.value + "&lead_id=" + document.vicidial_form.lead_id.value + "&campaign=" + campaign;

                document.getElementById("ParkControl").innerHTML ="<a href=\"#\" onclick=\"mainxfer_send_redirect('ParK','" + redirectdestination + "','" + redirectdestserverip + "');return false;\"><img src=\"./images/x-hold-on.png\" border=\"0\" alt=\"Park Call\" /><span>Place on Hold</span></a>";
				if ( (ivr_park_call=='ENABLED') || (ivr_park_call=='ENABLED_PARK_ONLY') )
					{
                    document.getElementById("ivrParkControl").innerHTML ="<a href=\"#\" onclick=\"mainxfer_send_redirect('ParKivr','" + redirectdestination + "','" + redirectdestserverip + "');return false;\"><img src=\"./images/x-holdivr.png\" border=\"0\" alt=\"IVR Park Call\" /><span>IVR Hold</span></a>";
					}
				customerparked=0;
				customerparkedcounter=0;
				}
				if (taskvar == 'ParKXfer')
				{
					blind_transfer=0;
					var queryCID = "LXPvdW" + epoch_sec + user_abb;
					var redirectdestination = taskxferconf;
					var redirectdestserverip = taskserverip;
					var parkedby = protocol + "/" + extension;
					xferredirect_query = "server_ip=" + server_ip + "&session_name=" + session_name + "&user=" + user + "&pass=" + pass + "&ACTION=RedirectToParkXfer&format=text&channel=" + redirectdestination + "&call_server_ip=" + redirectdestserverip + "&queryCID=" + queryCID + "&exten=" + park_on_extension + "&ext_context=" + ext_context + "&ext_priority=1&extenName=park&parkedby=" + parkedby + "&session_id=" + session_id + "&CalLCID=" + XDnextCID + "&uniqueid=" + document.vicidial_form.xferuniqueid.value + "&lead_id=" + document.vicidial_form.lead_id.value + "&campaign=" + campaign;
					
					document.getElementById("ParkControl2").innerHTML ="<a href=\"#\" onclick=\"mainxfer_send_redirect('FROMParKXfer','" + redirectdestination + "','" + redirectdestserverip + "');return false;\">Grab 3rd Party</a>";
                                        document.getElementById("ParkControl2").className = "button blue";
                                        
					customerparkedxfer=1;
					customerparkedcounterxfer=0;
				}
				if (taskvar == 'FROMParKXfer')
				{
					blind_transfer=0;
					var queryCID = "FXPvdW" + epoch_sec + user_abb;
					var redirectdestination = taskxferconf;
					var redirectdestserverip = taskserverip;
					
					if( (server_ip == taskserverip) && (taskserverip.length > 6) )
					{var dest_dialstring = session_id;}
					else
					{
						if(taskserverip.length > 6)
						{var dest_dialstring = server_ip_dialstring + "" + session_id;}
						else
						{var dest_dialstring = session_id;}
					}
					
					xferredirect_query = "server_ip=" + server_ip + "&session_name=" + session_name + "&user=" + user + "&pass=" + pass + "&ACTION=RedirectFromParkXfer&format=text&channel=" + redirectdestination + "&call_server_ip=" + redirectdestserverip + "&queryCID=" + queryCID + "&exten=" + dest_dialstring + "&ext_context=" + ext_context + "&ext_priority=1" + "&session_id=" + session_id + "&CalLCID=" + XDnextCID + "&uniqueid=" + document.vicidial_form.xferuniqueid.value + "&lead_id=" + document.vicidial_form.lead_id.value + "&campaign=" + campaign;
					
					document.getElementById("ParkControl2").innerHTML ="<a href=\"#\" onclick=\"mainxfer_send_redirect('ParKXfer','" + redirectdestination + "','" + redirectdestserverip + "');return false;\">Park 3rd Party</a>";
                                        document.getElementById("ParkControl2").className = "button blue";
                                
					customerparkedxfer=0;
					customerparkedcounterxfer=0;
				}
			if (taskvar == 'ParKivr')
				{
				if (CalLCID.length < 1)
					{
					CalLCID = MDnextCID;
					}
				blind_transfer=0;
				var queryCID = "LPvdcW" + epoch_sec + user_abb;
				var redirectdestination = taskxferconf;
				var redirectdestserverip = taskserverip;
				var parkedby = protocol + "/" + extension;
				xferredirect_query = "server_ip=" + server_ip + "&session_name=" + session_name + "&user=" + user + "&pass=" + pass + "&ACTION=RedirectToParkIVR&format=text&channel=" + redirectdestination + "&call_server_ip=" + redirectdestserverip + "&queryCID=" + queryCID + "&exten=" + park_on_extension + "&ext_context=" + ext_context + "&ext_priority=1&extenName=park&parkedby=" + parkedby + "&session_id=" + session_id + "&CalLCID=" + CalLCID + "&uniqueid=" + document.vicidial_form.uniqueid.value + "&lead_id=" + document.vicidial_form.lead_id.value + "&campaign=" + campaign;

                document.getElementById("ParkControl").innerHTML ="<a href=\"#\" style=\"opacity:.15;\" disabled><img class=\"disabled_img\" src=\"./images/x-hold-on.png\" border=\"0\" alt=\"Park Call\" /><span>Place on Hold</span></a>";
				if (ivr_park_call=='ENABLED_PARK_ONLY')
					{
                    document.getElementById("ivrParkControl").innerHTML ="<a href=\"#\" style=\"opacity:.15;\" disabled><img class=\"disabled_img\" src=\"./images/x-holdivr.png\" border=\"0\" alt=\"IVR Park Call\" /><span>IVR Hold</span></a>";
					}
				if (ivr_park_call=='ENABLED')
					{
                    document.getElementById("ivrParkControl").innerHTML ="<a href=\"#\" onclick=\"mainxfer_send_redirect('FROMParKivr','" + redirectdestination + "','" + redirectdestserverip + "');return false;\"><img src=\"./images/x-grabhold.png\" border=\"0\" alt=\"Grab IVR Parked Call\" /><span>Grab IVR</span></a>";
					}
				customerparked=1;
				customerparkedcounter=0;
				}
			if (taskvar == 'FROMParKivr')
				{
				blind_transfer=0;
				var queryCID = "FPvdcW" + epoch_sec + user_abb;
				var redirectdestination = taskxferconf;
				var redirectdestserverip = taskserverip;

				if( (server_ip == taskserverip) && (taskserverip.length > 6) )
					{var dest_dialstring = session_id;}
				else
					{
					if(taskserverip.length > 6)
						{var dest_dialstring = server_ip_dialstring + "" + session_id;}
					else
						{var dest_dialstring = session_id;}
					}

				xferredirect_query = "server_ip=" + server_ip + "&session_name=" + session_name + "&user=" + user + "&pass=" + pass + "&ACTION=RedirectFromParkIVR&format=text&channel=" + redirectdestination + "&call_server_ip=" + redirectdestserverip + "&queryCID=" + queryCID + "&exten=" + dest_dialstring + "&ext_context=" + ext_context + "&ext_priority=1" + "&session_id=" + session_id + "&CalLCID=" + CalLCID + "&uniqueid=" + document.vicidial_form.uniqueid.value + "&lead_id=" + document.vicidial_form.lead_id.value + "&campaign=" + campaign;

                document.getElementById("ParkControl").innerHTML ="<a href=\"#\" onclick=\"mainxfer_send_redirect('ParK','" + redirectdestination + "','" + redirectdestserverip + "');return false;\"><img src=\"./images/x-hold-on.png\" border=\"0\" alt=\"Park Call\" /><span>Place on Hold</span></a>";
				if ( (ivr_park_call=='ENABLED') || (ivr_park_call=='ENABLED_PARK_ONLY') )
					{
                    document.getElementById("ivrParkControl").innerHTML ="<a href=\"#\" onclick=\"mainxfer_send_redirect('ParKivr','" + redirectdestination + "','" + redirectdestserverip + "');return false;\"><img src=\"./images/x-holdivr.png\" border=\"0\" alt=\"IVR Park Call\" /><span>IVR Hold</span></a>";
					}
				customerparked=0;
				customerparkedcounter=0;
				}

			var XFRDop = '';
			xmlhttpXF.open('POST', 'manager_send.php'); 
			xmlhttpXF.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
			xmlhttpXF.send(xferredirect_query); 
			xmlhttpXF.onreadystatechange = function() 
				{ 
				if (xmlhttpXF.readyState == 4 && xmlhttpXF.status == 200) 
					{
					var XfeRRedirecToutput = null;
					XfeRRedirecToutput = xmlhttpXF.responseText;
					var XfeRRedirecToutput_array=XfeRRedirecToutput.split("|");
					var XFRDop = XfeRRedirecToutput_array[0];
					if (XFRDop == "NeWSessioN")
						{
						threeway_end=1;
						document.getElementById("callchannel").innerHTML = '';
						document.vicidial_form.callserverip.value = '';
						dialedcall_send_hangup();

						document.vicidial_form.xferchannel.value = '';
						xfercall_send_hangup();

						session_id = XfeRRedirecToutput_array[1];
						document.getElementById("sessionIDspan").innerHTML = session_id;

				//		alert("session_id changed to: " + session_id);
						}
				//	alert(xferredirect_query + "\n" + xmlhttpXF.responseText);
				//	document.getElementById("debugbottomspan").innerHTML = xferredirect_query + "\n" + xmlhttpXF.responseText;
					}
				}
			delete xmlhttpXF;
			}

			// used to send second Redirect for manual dial calls
			if ( (auto_dial_level == 0) && (taskvar != '3WAY') )
			{
				RedirecTxFEr = 1;
				var xmlhttpXF2=false;
				/*@cc_on @*/
				/*@if (@_jscript_version >= 5)
				// JScript gives us Conditional compilation, we can cope with old IE versions.
				// and security blocked creation of the objects.
				 try {
				  xmlhttpXF2 = new ActiveXObject("Msxml2.XMLHTTP");
				 } catch (e) {
				  try {
				   xmlhttpXF2 = new ActiveXObject("Microsoft.XMLHTTP");
				  } catch (E) {
				   xmlhttpXF2 = false;
				  }
				 }
				@end @*/
				if (!xmlhttpXF2 && typeof XMLHttpRequest!='undefined')
				{
					xmlhttpXF2 = new XMLHttpRequest();
				}
				if (xmlhttpXF2) 
				{ 
					xmlhttpXF2.open('POST', 'manager_send.php'); 
					xmlhttpXF2.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
					xmlhttpXF2.send(xferredirect_query + "&stage=2NDXfeR"); 
					xmlhttpXF2.onreadystatechange = function() 
						{ 
						if (xmlhttpXF2.readyState == 4 && xmlhttpXF2.status == 200) 
							{
							Nactiveext = null;
							Nactiveext = xmlhttpXF2.responseText;
					//		alert(RedirecTxFEr + "|" + xmlhttpXF2.responseText);
						}
				}
				delete xmlhttpXF2;
				}
			}

		if ( (taskvar == 'XfeRLOCAL') || (taskvar == 'XfeRBLIND') || (taskvar == 'XfeRVMAIL') )
			{
			if (auto_dial_level == 0) {RedirecTxFEr = 1;}
			document.getElementById("callchannel").innerHTML = '';
			document.vicidial_form.callserverip.value = '';
			//if( document.images ) { document.images['livecall'].src = image_livecall_OFF.src;}
                        document.getElementById('livecall').innerHTML = 'Not on Call';
                        document.getElementById('livecall').className = 'off';
		//	alert(RedirecTxFEr + "|" + auto_dial_level);
			dialedcall_send_hangup(taskdispowindow,'','',no_delete_VDAC);
			}

		}

// ################################################################################
// Finish the alternate dialing and move on to disposition the call
	function ManualDialAltDonE()
		{
		alt_phone_dialing=starting_alt_phone_dialing;
		alt_dial_active = 0;
		alt_dial_status_display = 0;
		open_dispo_screen=1;
		document.getElementById("MainStatuSSpan").innerHTML = "Dial Next Number";
		}
// ################################################################################
// Insert or update the vicidial_log entry for a customer call
	function DialLog(taskMDstage,nodeletevdac)
		{
		var alt_num_status = 0;
		if (taskMDstage == "start") 
			{
			var MDlogEPOCH = 0;
			var UID_test = document.vicidial_form.uniqueid.value;
			if (UID_test.length < 4)
				{
				UID_test = epoch_sec + '.' + random;
				document.vicidial_form.uniqueid.value = UID_test;
				}
			}
		else
			{
			if (alt_phone_dialing == 1)
				{
				if (document.vicidial_form.DiaLAltPhonE.checked==true)
					{
					alt_num_status = 1;
					reselect_alt_dial = 1;
					alt_dial_active = 1;
					alt_dial_status_display = 1;
					var man_status = "Dial Alt Phone Number: <a href=\"#\" onclick=\"ManualDialOnly('MaiNPhonE')\"><font class=\"preview_text\">MAIN PHONE</font></a> or <a href=\"#\" onclick=\"ManualDialOnly('ALTPhonE')\"><font class=\"preview_text\">ALT PHONE</font></a> or <a href=\"#\" onclick=\"ManualDialOnly('AddresS3')\"><font class=\"preview_text\">ADDRESS3</font></a> or <a href=\"#\" onclick=\"ManualDialAltDonE()\"><font class=\"preview_text_red\">FINISH LEAD</font></a>"; 
					document.getElementById("MainStatuSSpan").innerHTML = man_status;
					}
				}
			}
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
			manDiaLlog_query = "format=text&server_ip=" + server_ip + "&session_name=" + session_name + "&ACTION=manDiaLlogCaLL&stage=" + taskMDstage + "&uniqueid=" + document.vicidial_form.uniqueid.value + 
			"&user=" + user + "&pass=" + pass + "&campaign=" + campaign + 
			"&lead_id=" + document.vicidial_form.lead_id.value + 
			"&list_id=" + document.vicidial_form.list_id.value + 
			"&length_in_sec=0&phone_code=" + document.vicidial_form.phone_code.value + 
			"&phone_number=" + lead_dial_number + 
			"&exten=" + extension + "&channel=" + lastcustchannel + "&start_epoch=" + MDlogEPOCH + "&auto_dial_level=" + auto_dial_level + "&VDstop_rec_after_each_call=" + VDstop_rec_after_each_call + "&conf_silent_prefix=" + conf_silent_prefix + "&protocol=" + protocol + "&extension=" + extension + "&ext_context=" + ext_context + "&conf_exten=" + session_id + "&user_abb=" + user_abb + "&agent_log_id=" + agent_log_id + "&MDnextCID=" + LasTCID + "&inOUT=" + inOUT + "&alt_dial=" + dialed_label + "&DB=0" + "&agentchannel=" + agentchannel + "&conf_dialed=" + conf_dialed + "&leaving_threeway=" + leaving_threeway + "&hangup_all_non_reserved=" + hangup_all_non_reserved + "&blind_transfer=" + blind_transfer + "&dial_method" + dial_method + "&nodeletevdac=" + nodeletevdac + "&alt_num_status=" + alt_num_status;
			xmlhttp.open('POST', 'vdc_db_query.php'); 
			xmlhttp.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
		//		document.getElementById("busycallsdebug").innerHTML = "vdc_db_query.php?" + manDiaLlog_query;
			xmlhttp.send(manDiaLlog_query); 
			xmlhttp.onreadystatechange = function() 
				{ 
				if (xmlhttp.readyState == 4 && xmlhttp.status == 200) 
					{
					var MDlogResponse = null;
			//		alert(xmlhttp.responseText);
					MDlogResponse = xmlhttp.responseText;
					var MDlogResponse_array=MDlogResponse.split("\n");
					MDlogLINE = MDlogResponse_array[0];
					if ( (MDlogLINE == "LOG NOT ENTERED") && (VDstop_rec_after_each_call != 1) )
						{
				//		alert("error: log not entered\n");
						}
					else
						{
						MDlogEPOCH = MDlogResponse_array[1];
				//		alert("VICIDIAL Call log entered:\n" + document.vicidial_form.uniqueid.value);
						if ( (taskMDstage != "start") && (VDstop_rec_after_each_call == 1) )
							{
                            var conf_rec_start_html = "<a href=\"#\" onclick=\"conf_send_recording('MonitorConf','" + session_id + "','');return false;\"><img src=\"./images/x-record.png\" border=\"0\" alt=\"Start Recording\" /><span id=\"x-recording\">Record</span></a>";
							if ( (LIVE_campaign_recording == 'NEVER') || (LIVE_campaign_recording == 'ALLFORCE') )
								{
                                document.getElementById("RecorDControl").innerHTML = "<a href=\"#\" style=\"opacity:.15;\" disabled><img class=\"disabled_img\" src=\"./images/x-record.png\" border=\"0\" alt=\"Start Recording\" /><br /><span id=\"x-recording\">Record</span></a>";
								}
							else
								{document.getElementById("RecorDControl").innerHTML = conf_rec_start_html;}
							
							MDlogRecorDings = MDlogResponse_array[3];
							if (window.MDlogRecorDings)
								{
								var MDlogRecorDings_array=MDlogRecorDings.split("|");
						//		recording_filename = MDlogRecorDings_array[2];
						//		recording_id = MDlogRecorDings_array[3];

								var RecDispNamE = MDlogRecorDings_array[2];
								if (RecDispNamE.length > 25)
									{
									RecDispNamE = RecDispNamE.substr(0,22);
									RecDispNamE = RecDispNamE + '...';
									}
								document.getElementById("RecorDingFilename").innerHTML = '<b>Recording:</b> ' + RecDispNamE + '<br />';
								document.getElementById("RecorDID").innerHTML = '<b>Recording Id:</b> ' + MDlogRecorDings_array[3] + '<br />';
								}
							}
						}
					}
				}
			delete xmlhttp;
			}
		RedirecTxFEr=0;
		conf_dialed=0;
		}


// ################################################################################
// Request number of dialable leads left in this campaign
	function DiaLableLeaDsCounT()
		{
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
			DLcount_query = "server_ip=" + server_ip + "&session_name=" + session_name + "&user=" + user + "&pass=" + pass + "&ACTION=DiaLableLeaDsCounT&campaign=" + campaign + "&format=text";
			xmlhttp.open('POST', 'vdc_db_query.php'); 
			xmlhttp.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
			xmlhttp.send(DLcount_query); 
			xmlhttp.onreadystatechange = function() 
				{ 
				if (xmlhttp.readyState == 4 && xmlhttp.status == 200) 
					{
				//	alert(xmlhttp.responseText);
					var DLcounT = xmlhttp.responseText;
                        document.getElementById("dialableleadsspan").innerHTML ="Dialable Leads:&nbsp; " + DLcounT;
						
					}
				}
			delete xmlhttp;
			}
		}


// ################################################################################
// Request number of USERONLY callbacks for this agent
	function CalLBacKsCounTCheck()
		{
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
			CBcount_query = "server_ip=" + server_ip + "&session_name=" + session_name + "&user=" + user + "&pass=" + pass + "&ACTION=CalLBacKCounT&campaign=" + campaign + "&format=text";
			xmlhttp.open('POST', 'vdc_db_query.php'); 
			xmlhttp.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
			xmlhttp.send(CBcount_query); 
			xmlhttp.onreadystatechange = function() 
				{ 
				if (xmlhttp.readyState == 4 && xmlhttp.status == 200) 
					{
					var CBpre = '';
					var CBpost = '';
					var Defer=0;

				//	alert(xmlhttp.responseText);
					var CBcounTtotal = xmlhttp.responseText;
					var CBcounTtotal_array=CBcounTtotal.split("|");
					var CBcounT = CBcounTtotal_array[0];
					if (scheduled_callbacks_count=='LIVE')
						{CBcounT = CBcounTtotal_array[1];}
					if (CBcounT == 0) {var CBprint = "NO";}
					else 
						{
						var CBprint = CBcounT;
						if ( (LastCallbackCount < CBcounT) || (LastCallbackCount > CBcounT) )
							{
							LastCallbackCount = CBcounT;
							LastCallbackViewed=0;
							}

						if ( (scheduled_callbacks_alert == 'RED_DEFER') || (scheduled_callbacks_alert == 'BLINK_DEFER') || (scheduled_callbacks_alert == 'BLINK_RED_DEFER') )
							{Defer=1;}

						if ( (LastCallbackViewed > 0) && (Defer > 0) )
							{var do_nothing=1;}
						else
							{
							if ( (scheduled_callbacks_alert == 'BLINK') || (scheduled_callbacks_alert == 'BLINK_DEFER') )
								{
								CBpre = '<blink>';
								CBpost = '</blink>';
								}
							if ( (scheduled_callbacks_alert == 'RED') || (scheduled_callbacks_alert == 'RED_DEFER') )
								{
								CBpre = '<b><font color="red">';
								CBpost = '</font></b>';
								}
							if ( (scheduled_callbacks_alert == 'BLINK_RED') || (scheduled_callbacks_alert == 'BLINK_RED_DEFER') )
								{
								CBpre = '<b><font color="red"><blink>';
								CBpost = '</blink></font></b>';
								}
							}
						}
					CBlinkCONTENT ="<a href=\"#\" onclick=\"CalLBacKsLisTCheck();return false;\">" + CBpre + '' + CBprint + '' + " Active Callbacks" + CBpost + "</a>";	
					document.getElementById("CBstatusSpan").innerHTML = CBlinkCONTENT;	
					}
				}
			delete xmlhttp;
			}
		}


// ################################################################################
// Request list of USERONLY callbacks for this agent
	function CalLBacKsLisTCheck()
		{
		var move_on=1;
		if ( (AutoDialWaiting == 1) || (VD_live_customer_call==1) || (alt_dial_active==1) || (MD_channel_look==1) || (in_lead_preview_state==1) )
			{
			if ( (auto_pause_precall == 'Y') && ( (agent_pause_codes_active=='Y') || (agent_pause_codes_active=='FORCE') ) && (AutoDialWaiting == 1) && (VD_live_customer_call!=1) && (alt_dial_active!=1) && (MD_channel_look!=1) && (in_lead_preview_state!=1) )
				{
				agent_log_id = AutoDial_ReSume_PauSe("VDADpause",'','','','','1',auto_pause_precall_code);
				}
			else
				{
				move_on=0;
				alert_box("YOU MUST BE PAUSED TO CHECK CALLBACKS IN AUTO-DIAL MODE");
				}
			}
		if (move_on == 1)
			{
			LastCallbackViewed=1;

			showDiv('CallBacKsLisTBox');

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
				var CBlist_query = "server_ip=" + server_ip + "&session_name=" + session_name + "&user=" + user + "&pass=" + pass + "&ACTION=CalLBacKLisT&campaign=" + campaign + "&format=text";
				xmlhttp.open('POST', 'vdc_db_query.php'); 
				xmlhttp.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
				xmlhttp.send(CBlist_query); 
				xmlhttp.onreadystatechange = function() 
					{ 
					if (xmlhttp.readyState == 4 && xmlhttp.status == 200) 
						{
					//	alert(xmlhttp.responseText);
						var all_CBs = null;
						all_CBs = xmlhttp.responseText;
						var all_CBs_array=all_CBs.split("\n");
						var CB_calls = all_CBs_array[0];
						var loop_ct=0;
						var conv_start=0;
                        var CB_HTML = "<table width=\"100%\"><tr bgcolor=\"<?php echo $SCRIPT_COLOR ?>\"><td><font class=\"log_title\">#</font></td><td align=\"center\"><font class=\"log_title\"> CALLBACK DATE/TIME</font></td><td align=\"center\"><font class=\"log_title\">NUMBER</font></td><td align=\"center\"><font class=\"log_title\">INFO</font></td><td align=\"center\"><font class=\"log_title\">NAME</font></td><td align=\"center\"><font class=\"log_title\">  STATUS</font></td><td align=\"center\"><font class=\"log_title\">CAMPAIGN</font></td><td align=\"center\"><font class=\"log_title\">LAST CALL DATE/TIME</font></td><td align=\"center\"><font class=\"log_title\"> DIAL</font></td><td align=\"center\"><font class=\"log_title\"> ALT</font></td></tr>"
						while (loop_ct < CB_calls)
							{
							loop_ct++;
							loop_s = loop_ct.toString();
							if (loop_s.match(/1$|3$|5$|7$|9$/)) 
								{var row_color = '#DDDDFF';}
							else
								{var row_color = '#CCCCFF';}
							var conv_ct = (loop_ct + conv_start);
							var call_array = all_CBs_array[conv_ct].split(" ~");
							var CB_name = call_array[0] + " " + call_array[1];
							var CB_phone = call_array[2];
							var CB_id = call_array[3];
							var CB_lead_id = call_array[4];
							var CB_campaign = call_array[5];
							var CB_status = call_array[6];
							var CB_lastcall_time = call_array[7];
							var CB_callback_time = call_array[8];
							var CB_comments = call_array[9];
							var CB_comments_ten = CB_comments;
                                                        if (CB_comments_ten)
                                                        {
							if (CB_comments_ten.length > 10)
								{
								CB_comments_ten = CB_comments_ten.substr(0,10);
								CB_comments_ten = CB_comments_ten + '...';
								}
                                                        }
                            CB_HTML = CB_HTML + "<tr bgcolor=\"" + row_color + "\"><td><font class=\"log_text_sm\">" + loop_ct + "</font></td><td align=\"right\"><font class=\"log_text_sm\">" + CB_callback_time + "</td><td align=\"right\"><font class=\"log_text_sm\">" + CB_phone + "</td><td align=\"right\"><font class=\"log_text_sm\">" + CB_comments_ten + " - <a href=\"#\" onclick=\"VieWLeaDInfO('" + CB_lead_id + "','" + CB_id + "');return false;\">INFO</a></font></td><td align=\"right\"><font class=\"log_text_sm\">" + CB_name + "</font></td><td align=\"right\"><font class=\"log_text_sm\">" + CB_status + "</font></td><td align=\"right\"><font class=\"log_text_sm\">" + CB_campaign + "</font></td><td align=\"right\"><font class=\"log_text_sm\">" + CB_lastcall_time + "&nbsp;</font></td><td align=\"right\"><font class=\"log_text_sm\"><a href=\"#\" onclick=\"new_callback_call('" + CB_id + "','" + CB_lead_id + "','MAIN');return false;\">DIAL</a>&nbsp;</font></td><td align=\"right\"><font class=\"log_text_sm\"><a href=\"#\" onclick=\"new_callback_call('" + CB_id + "','" + CB_lead_id + "','ALT');return false;\">ALT</a>&nbsp;</font></td></tr>";
							}
						CB_HTML = CB_HTML + "</table>";
						document.getElementById("CallBacKsLisT").innerHTML = CB_HTML;
						}
					}
				delete xmlhttp;
				}
			}
		}


// ################################################################################
// closes callback list screen
	function alert_box(temp_message)
		{
		document.getElementById("AlertBoxContent").innerHTML = temp_message;

		showDiv('AlertBox');

		document.alert_form.alert_button.focus();
		}


// ################################################################################
// closes callback list screen
	function CalLBacKsLisTClose()
		{
		if (auto_resume_precall == 'Y')
			{
			AutoDial_ReSume_PauSe("VDADready");
			}
		hideDiv('CallBacKsLisTBox');
		CalLBacKsCounTCheck();
		}


// ################################################################################
// closes call log display screen
	function CalLLoGVieWClose()
		{
		if (auto_resume_precall == 'Y')
			{
			AutoDial_ReSume_PauSe("VDADready");
			}
		hideDiv('CalLLoGDisplaYBox');
		}


// ################################################################################
// closes lead search screen
	function LeaDSearcHVieWClose()
		{
		if (auto_resume_precall == 'Y')
			{
			AutoDial_ReSume_PauSe("VDADready");
			}
		hideDiv('SearcHForMDisplaYBox');
		}


// ################################################################################
// Open up a callback customer record as manual dial preview mode
	function new_callback_call(taskCBid,taskLEADid,taskCBalt)
		{
		if (waiting_on_dispo > 0)
			{
			alert_box("System Delay, Please try again<BR><font size=1>code:" + agent_log_id + " - " + waiting_on_dispo + "</font>");
			}
		else
			{
		//	alt_phone_dialing=1;
			LastCallbackViewed=1;
			LastCallbackCount = (LastCallbackCount - 1);
			auto_dial_level=0;
			manual_dial_in_progress=1;
			MainPanelToFront();
			buildDiv('DiaLLeaDPrevieW');
			if (alt_phone_dialing == 1)
				{buildDiv('DiaLDiaLAltPhonE');}
			document.vicidial_form.LeadPreview.checked=true;
		//	document.vicidial_form.DiaLAltPhonE.checked=true;
			hideDiv('CallBacKsLisTBox');
			ManualDialNext(taskCBid,taskLEADid,'','','','0','',taskCBalt);
			}
		}


// ################################################################################
// Finish Callback and go back to original screen
	function manual_dial_finished()
		{
		alt_phone_dialing=starting_alt_phone_dialing;
		auto_dial_level=starting_dial_level;
		MainPanelToFront();
		CalLBacKsCounTCheck();
		manual_dial_in_progress=0;
		}


// ################################################################################
// Open page to enter details for a new manual dial lead
	function NeWManuaLDiaLCalL(TVfast,TVphone_code,TVphone_number,TVlead_id,TVtype)
		{
		var move_on=1;
		if ( (AutoDialWaiting == 1) || (VD_live_customer_call==1) || (alt_dial_active==1) || (MD_channel_look==1) || (in_lead_preview_state==1) )
			{
			if ((auto_pause_precall == 'Y') && ( (agent_pause_codes_active=='Y') || (agent_pause_codes_active=='FORCE') ) && (AutoDialWaiting == 1) && (VD_live_customer_call!=1) && (alt_dial_active!=1) && (MD_channel_look!=1) && (in_lead_preview_state!=1) )
				{
				agent_log_id = AutoDial_ReSume_PauSe("VDADpause",'','','','','1',auto_pause_precall_code);
				}
			else
				{
				move_on=0;
				alert_box("YOU MUST BE PAUSED TO MANUAL DIAL A NEW LEAD IN AUTO-DIAL MODE");
				}
			}
		if (move_on == 1)
			{
			if (TVfast=='FAST')
				{
				NeWManuaLDiaLCalLSubmiTfast();
				}
			else
				{
				if (TVfast=='CALLLOG')
					{
					hideDiv('CalLLoGDisplaYBox');
					hideDiv('SearcHForMDisplaYBox');
					hideDiv('SearcHResultSDisplaYBox');
					hideDiv('LeaDInfOBox');
					document.vicidial_form.MDDiaLCodE.value = TVphone_code;
					document.vicidial_form.MDPhonENumbeR.value = TVphone_number;
					document.vicidial_form.MDLeadID.value = TVlead_id;
					document.vicidial_form.MDType.value = TVtype;
					}
				if (TVfast=='LEADSEARCH')
					{
					hideDiv('SearcHForMDisplaYBox');
					hideDiv('SearcHResultSDisplaYBox');
					hideDiv('LeaDInfOBox');
					document.vicidial_form.MDDiaLCodE.value = TVphone_code;
					document.vicidial_form.MDPhonENumbeR.value = TVphone_number;
					document.vicidial_form.MDLeadID.value = TVlead_id;
					document.vicidial_form.MDType.value = TVtype;
					}
				if (agent_allow_group_alias == 'Y')
					{
                    document.getElementById("ManuaLDiaLGrouPSelecteD").innerHTML = "<font size=\"2\" face=\"Arial,Helvetica\">Group Alias: " + active_group_alias + "</font>";
                    document.getElementById("ManuaLDiaLGrouP").innerHTML = "<a href=\"#\" onclick=\"GroupAliasSelectContent_create('0');\"><font size=\"1\" face=\"Arial,Helvetica\">Click Here to Choose a Group Alias</font></a>";
					}
				showDiv('NeWManuaLDiaLBox');

				document.vicidial_form.search_phone_number.value='';
				document.vicidial_form.search_lead_id.value='';
				document.vicidial_form.search_vendor_lead_code.value='';
				document.vicidial_form.search_first_name.value='';
				document.vicidial_form.search_last_name.value='';
				document.vicidial_form.search_city.value='';
				document.vicidial_form.search_state.value='';
				document.vicidial_form.search_postal_code.value='';
				}
			}
		}


// ################################################################################
// Insert the new manual dial as a lead and go to manual dial screen
	function NeWManuaLDiaLCalLSubmiT(tempDiaLnow)
		{
		if (waiting_on_dispo > 0)
			{
			alert_box("System Delay, Please try again<BR><font size=1>code:" + agent_log_id + " - " + waiting_on_dispo + "</font>");
			}
		else
			{
			hideDiv('NeWManuaLDiaLBox');
			//document.getElementById("debugbottomspan").innerHTML = "DEBUG OUTPUT" + document.vicidial_form.MDPhonENumbeR.value + "|" + active_group_alias;

			var sending_group_alias = 0;
			var MDDiaLCodEform = document.vicidial_form.MDDiaLCodE.value;
			var MDPhonENumbeRform = document.vicidial_form.MDPhonENumbeR.value;
			var MDLeadIDform = document.vicidial_form.MDLeadID.value;
			var MDTypeform = document.vicidial_form.MDType.value;
			var MDDiaLOverridEform = document.vicidial_form.MDDiaLOverridE.value;
			var MDVendorLeadCode = document.vicidial_form.vendor_lead_code.value;
			var MDLookuPLeaD = 'new';
			if (document.vicidial_form.LeadLookuP.checked==true)
				{MDLookuPLeaD = 'lookup';}

			if (MDDiaLCodEform.length < 1)
				{MDDiaLCodEform = document.vicidial_form.phone_code.value;}

			if (MDDiaLOverridEform.length > 0)
				{
				agent_dialed_number=1;
				agent_dialed_type='MANUAL_OVERRIDE';
				basic_originate_call(session_id,'NO','YES',MDDiaLOverridEform,'YES','','1','0');
				}
			else
				{
				auto_dial_level=0;
				manual_dial_in_progress=1;
				agent_dialed_number=1;
				MainPanelToFront();

				if (tempDiaLnow == 'PREVIEW')
					{
				//	alt_phone_dialing=1;
					agent_dialed_type='MANUAL_PREVIEW';
					buildDiv('DiaLLeaDPrevieW');
					if (alt_phone_dialing == 1)
						{buildDiv('DiaLDiaLAltPhonE');}
					document.vicidial_form.LeadPreview.checked=true;
				//	document.vicidial_form.DiaLAltPhonE.checked=true;
					}
				else
					{
					agent_dialed_type='MANUAL_DIALNOW';
					document.vicidial_form.LeadPreview.checked=false;
					document.vicidial_form.DiaLAltPhonE.checked=false;
					}
				if (active_group_alias.length > 1)
					{var sending_group_alias = 1;}

				ManualDialNext("",MDLeadIDform,MDDiaLCodEform,MDPhonENumbeRform,MDLookuPLeaD,MDVendorLeadCode,sending_group_alias,MDTypeform);
				}

			document.vicidial_form.MDPhonENumbeR.value = '';
			document.vicidial_form.MDDiaLOverridE.value = '';
			document.vicidial_form.MDLeadID.value = '';
			document.vicidial_form.MDType.value = '';
			}
		}

// ################################################################################
// Fast version of manual dial
		function NeWManuaLDiaLCalLSubmiTfast()
		{
		var MDDiaLCodEform = document.vicidial_form.phone_code.value;
		var MDPhonENumbeRform = document.vicidial_form.phone_number.value;
		var MDVendorLeadCode = document.vicidial_form.vendor_lead_code.value;

		if ( (MDDiaLCodEform.length < 1) || (MDPhonENumbeRform.length < 5) )
			{
			alert_box("YOU MUST ENTER A PHONE NUMBER AND DIAL CODE TO USE FAST DIAL");
			}
		else
			{
			if (waiting_on_dispo > 0)
				{
				alert_box("System Delay, Please try again<BR><font size=1>code:" + agent_log_id + " - " + waiting_on_dispo + "</font>");
				}
			else
				{
				var MDLookuPLeaD = 'new';
				if (document.vicidial_form.LeadLookuP.checked==true)
					{MDLookuPLeaD = 'lookup';}
			
				agent_dialed_number=1;
				agent_dialed_type='MANUAL_DIALFAST';
			//	alt_phone_dialing=1;
				auto_dial_level=0;
				manual_dial_in_progress=1;
				MainPanelToFront();
				buildDiv('DiaLLeaDPrevieW');
				if (alt_phone_dialing == 1)
					{buildDiv('DiaLDiaLAltPhonE');}
				document.vicidial_form.LeadPreview.checked=false;
			//	document.vicidial_form.DiaLAltPhonE.checked=true;
				ManualDialNext("","",MDDiaLCodEform,MDPhonENumbeRform,MDLookuPLeaD,MDVendorLeadCode,'0');
				}
			}
		}

// ################################################################################
// Request lookup of manual dial channel
	function ManualDialCheckChanneL(taskCheckOR)
		{
		if (taskCheckOR == 'YES')
			{
			var CIDcheck = XDnextCID;
			}
		else
			{
			var CIDcheck = MDnextCID;
			}
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
			manDiaLlook_query = "server_ip=" + server_ip + "&session_name=" + session_name + "&ACTION=manDiaLlookCaLL&conf_exten=" + session_id + "&user=" + user + "&pass=" + pass + "&MDnextCID=" + CIDcheck + "&agent_log_id=" + agent_log_id + "&lead_id=" + document.vicidial_form.lead_id.value + "&DiaL_SecondS=" + MD_ring_secondS;
			xmlhttp.open('POST', 'vdc_db_query.php'); 
			xmlhttp.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
			xmlhttp.send(manDiaLlook_query); 
			xmlhttp.onreadystatechange = function() 
				{ 
				if (xmlhttp.readyState == 4 && xmlhttp.status == 200) 
					{
					var MDlookResponse = null;
				//	alert(xmlhttp.responseText);
					MDlookResponse = xmlhttp.responseText;
					var MDlookResponse_array=MDlookResponse.split("\n");
					var MDlookCID = MDlookResponse_array[0];
					var regMDL = new RegExp("^Local","ig");
                                        
					if (MDlookCID == "NO")
						{
						MD_ring_secondS++;
						var dispnum = lead_dial_number;

						var status_display_number = phone_number_format(dispnum);

						if (alt_dial_status_display=='0')
							{
					//		alert(document.getElementById("MainStatuSSpan").innerHTML);
					//		document.getElementById("MainStatuSSpan").innerHTML = " Calling: " + status_display_number + " UID: " + CIDcheck + " &nbsp; Waiting for Ring... " + MD_ring_secondS + " seconds";
                                                        document.getElementById("MainStatuSSpan").innerHTML = " Calling: " + status_display_number + " &nbsp; Waiting for Ring... " + MD_ring_secondS + " seconds";
					//		alert("channel not found yet:\n" + campaign);
							}
						}
					else
						{
						if (taskCheckOR == 'YES')
							{
							XDuniqueid = MDlookResponse_array[0];
							XDchannel = MDlookResponse_array[1];
							var XDalert = MDlookResponse_array[2];
							
							if (XDalert == 'ERROR')
								{
								var XDerrorDesc = MDlookResponse_array[3];
								var DiaLAlerTMessagE = "Call Rejected: " + XDchannel + "\n" + XDerrorDesc;
								TimerActionRun("DiaLAlerT",DiaLAlerTMessagE);
								}
							if ( (XDchannel.match(regMDL)) && (asterisk_version != '1.0.8') && (asterisk_version != '1.0.9') && (MD_ring_secondS < 10) )
								{
								// bad grab of Local channel, try again
								MD_ring_secondS++;
								}
							else
								{
								document.vicidial_form.xferuniqueid.value	= MDlookResponse_array[0];
								document.vicidial_form.xferchannel.value	= MDlookResponse_array[1];
								lastxferchannel = MDlookResponse_array[1];
								document.vicidial_form.xferlength.value		= 0;

								XD_live_customer_call = 1;
								XD_live_call_secondS = 0;
								MD_channel_look=0;

								var called3rdparty = document.vicidial_form.xfernumber.value;
								if (hide_xfer_number_to_dial=='ENABLED')
									{called3rdparty=' ';}
								//document.getElementById("MainStatuSSpan").innerHTML = " Called 3rd party: " + called3rdparty + " UID: " + CIDcheck;
                                                                document.getElementById("MainStatuSSpan").innerHTML = " Called 3rd party: " + called3rdparty;
									
								document.getElementById("ParkControl2").innerHTML ="<a href=\"#\" onclick=\"mainxfer_send_redirect('ParKXfer','" + lastxferchannel + "','" + server_ip + "');return false;\">Park 3rd Party</a>";
                                                                document.getElementById("ParkControl2").className = "button blue";

                                document.getElementById("Leave3WayCall").innerHTML ="<a href=\"#\" onclick=\"leave_3way_call('FIRST');return false;\">Leave 3-Way</a>";
                                document.getElementById("Leave3WayCall").className = "button blue";

                                document.getElementById("DialWithCustomer").innerHTML ="Warm X-fer";
                                document.getElementById("DialWithCustomer").className = "button blueinactive";


                                document.getElementById("ParkCustomerDial").innerHTML ="Park &amp; Dial";
                                document.getElementById("ParkCustomerDial").className = "button blueinactive";

                                document.getElementById("HangupXferLine").innerHTML ="<a href=\"#\" onclick=\"xfercall_send_hangup();return false;\">Hangup 3rd Party</a>";
                                document.getElementById("HangupXferLine").className = "button blue";
                                hideDivDiplay("x-transfer-options");
                                showDivDisplay("x-transfer-active");

                                document.getElementById("HangupBothLines").innerHTML ="<a href=\"#\" onclick=\"bothcall_send_hangup();return false;\">Hangup Both</a>";
                                document.getElementById("HangupBothLines").className = "button blue";

								xferchannellive=1;
								XDcheck = '';
								}
							}
						else
							{
							MDuniqueid = MDlookResponse_array[0];
							MDchannel = MDlookResponse_array[1];
							var MDalert = MDlookResponse_array[2];
							
							if (MDalert == 'ERROR')
								{
								var MDerrorDesc = MDlookResponse_array[3];
								var DiaLAlerTMessagE = "Call Rejected: " + MDchannel + "\n" + MDerrorDesc;
								TimerActionRun("DiaLAlerT",DiaLAlerTMessagE);
								}
							if ( (MDchannel.match(regMDL)) && (asterisk_version != '1.0.8') && (asterisk_version != '1.0.9') )
								{
								// bad grab of Local channel, try again
								MD_ring_secondS++;
								}
							else
								{
								custchannellive=1;

								document.vicidial_form.uniqueid.value		= MDlookResponse_array[0];
								document.getElementById("callchannel").innerHTML	= MDlookResponse_array[1];
								lastcustchannel = MDlookResponse_array[1];
								//if( document.images ) { document.images['livecall'].src = image_livecall_ON.src;}
                                                                document.getElementById('livecall').innerHTML = 'On a Call';
                                                                document.getElementById('livecall').className = 'on';
								document.vicidial_form.SecondS.value		= 0;
								document.getElementById("SecondSDISP").innerHTML = '0';

								VD_live_customer_call = 1;
								VD_live_call_secondS = 0;

								MD_channel_look=0;
								var dispnum = lead_dial_number;
								var status_display_number = phone_number_format(dispnum);

								//document.getElementById("MainStatuSSpan").innerHTML = " Called: " + status_display_number + " UID: " + CIDcheck + " &nbsp;";
                                                                document.getElementById("MainStatuSSpan").innerHTML = " Called: " + status_display_number + " &nbsp;"; 

                                document.getElementById("ParkControl").innerHTML ="<a href=\"#\" onclick=\"mainxfer_send_redirect('ParK','" + lastcustchannel + "','" + lastcustserverip + "');return false;\"><img src=\"./images/x-hold-on.png\" border=\"0\" alt=\"Park Call\" /><span>Place on Hold</span></a>";
								if ( (ivr_park_call=='ENABLED') || (ivr_park_call=='ENABLED_PARK_ONLY') )
									{
                                    document.getElementById("ivrParkControl").innerHTML ="<a href=\"#\" onclick=\"mainxfer_send_redirect('ParKivr','" + lastcustchannel + "','" + lastcustserverip + "');return false;\"><img src=\"./images/x-holdivr.png\" border=\"0\" alt=\"IVR Park Call\" /><span>IVR Hold</span></a>";
									}

                                document.getElementById("HangupControl").innerHTML = "<a href=\"#\" onclick=\"dialedcall_send_hangup();\"><img src=\"./images/x-endcall-on.png\" border=\"0\" alt=\"Hangup Customer\" /><span>End Call</span></a>";
                                
                                document.getElementById("XferControl").innerHTML = "<a href=\"#\" onclick=\"ShoWTransferMain('ON');\"><img src=\"./images/x-transfer-on.png\" border=\"0\" alt=\"Transfer - Conference\" /><span>X-fer or Conf.</span></a>";
                                
                                document.getElementById("LocalCloser").innerHTML = "<a href=\"#\" onclick=\"mainxfer_send_redirect('XfeRLOCAL','" + lastcustchannel + "','" + lastcustserverip + "');return false;\">X-fer to Queue</a>";
                                document.getElementById("LocalCloser").className = "button blue";

                                document.getElementById("DialBlindTransfer").innerHTML = "<a href=\"#\" onclick=\"mainxfer_send_redirect('XfeRBLIND','" + lastcustchannel + "','" + lastcustserverip + "');return false;\">Blind X-fer</a>";
                                document.getElementById("DialBlindTransfer").className = "button blue";
                                
                                document.getElementById("DialBlindVMail").innerHTML = "<a href=\"#\" onclick=\"mainxfer_send_redirect('XfeRVMAIL','" + lastcustchannel + "','" + lastcustserverip + "');return false;\"><img src=\"./images/vdc_XB_ammessage.gif\" border=\"0\" alt=\"Blind Transfer VMail Message\" style=\"vertical-align:middle\" /></a>";

                                document.getElementById("VolumeUpSpan").innerHTML = "<a href=\"#\" onclick=\"volume_control('UP','" + MDchannel + "','');return false;\"><img style=\"margin-bottom:10px;height:35px;\" src=\"./images/x-volume-up.png\" border=\"0\"></a>";
                                document.getElementById("VolumeDownSpan").innerHTML = "<a href=\"#\" onclick=\"volume_control('DOWN','" + MDchannel + "','');return false;\"><img style=\"margin-bottom:10px;height:35px;\" src=\"./images/x-volume-down.png\" border=\"0\"></a>";

								if ( (quick_transfer_button == 'IN_GROUP') || (quick_transfer_button == 'LOCKED_IN_GROUP') )
									{
									quick_transfer_button_orig='';
									if (quick_transfer_button_locked > 0)
										{quick_transfer_button_orig = default_xfer_group;}

                                    document.getElementById("QuickXfer").innerHTML = "<a href=\"#\" onclick=\"mainxfer_send_redirect('XfeRLOCAL','" + lastcustchannel + "','" + lastcustserverip + "','','','" + quick_transfer_button_locked + "',1);return false;\"><img src=\"./images/x-transfer-quick.png\" border=\"0\" alt=\"QUICK TRANSFER\" /><br /><span>Quick X-fer</span></a>";
									}
								if (prepopulate_transfer_preset_enabled > 0)
									{
									if ( (prepopulate_transfer_preset == 'PRESET_1') || (prepopulate_transfer_preset == 'LOCKED_PRESET_1') )
										{document.vicidial_form.xfernumber.value = CalL_XC_a_NuMber;   document.vicidial_form.xfername.value='D1';}
									if ( (prepopulate_transfer_preset == 'PRESET_2') || (prepopulate_transfer_preset == 'LOCKED_PRESET_2') )
										{document.vicidial_form.xfernumber.value = CalL_XC_b_NuMber;   document.vicidial_form.xfername.value='D2';}
									if ( (prepopulate_transfer_preset == 'PRESET_3') || (prepopulate_transfer_preset == 'LOCKED_PRESET_3') )
										{document.vicidial_form.xfernumber.value = CalL_XC_c_NuMber;   document.vicidial_form.xfername.value='D3';}
									if ( (prepopulate_transfer_preset == 'PRESET_4') || (prepopulate_transfer_preset == 'LOCKED_PRESET_4') )
										{document.vicidial_form.xfernumber.value = CalL_XC_d_NuMber;   document.vicidial_form.xfername.value='D4';}
									if ( (prepopulate_transfer_preset == 'PRESET_5') || (prepopulate_transfer_preset == 'LOCKED_PRESET_5') )
										{document.vicidial_form.xfernumber.value = CalL_XC_e_NuMber;   document.vicidial_form.xfername.value='D5';}
									}
								if ( (quick_transfer_button == 'PRESET_1') || (quick_transfer_button == 'PRESET_2') || (quick_transfer_button == 'PRESET_3') || (quick_transfer_button == 'PRESET_4') || (quick_transfer_button == 'PRESET_5') ||
                                                                        (quick_transfer_button == 'LOCKED_PRESET_1') || (quick_transfer_button == 'LOCKED_PRESET_2') || (quick_transfer_button == 'LOCKED_PRESET_3') || (quick_transfer_button == 'LOCKED_PRESET_4') || (quick_transfer_button == 'LOCKED_PRESET_5') ||
                                                                        (end_call_action == 'PRESET_1') || (end_call_action == 'PRESET_2') || (end_call_action == 'PRESET_3') || (end_call_action == 'PRESET_4') || (end_call_action == 'PRESET_5'))
									{
                                                                        if (quick_transfer_button_enabled == 0 && end_call_action_enabled == 1)
                                                                            {quick_transfer_button = end_call_action;}
                                                                            
									if ( (quick_transfer_button == 'PRESET_1') || (quick_transfer_button == 'LOCKED_PRESET_1') )
										{document.vicidial_form.xfernumber.value = CalL_XC_a_NuMber;   document.vicidial_form.xfername.value='D1';}
									if ( (quick_transfer_button == 'PRESET_2') || (quick_transfer_button == 'LOCKED_PRESET_2') )
										{document.vicidial_form.xfernumber.value = CalL_XC_b_NuMber;   document.vicidial_form.xfername.value='D2';}
									if ( (quick_transfer_button == 'PRESET_3') || (quick_transfer_button == 'LOCKED_PRESET_3') )
										{document.vicidial_form.xfernumber.value = CalL_XC_c_NuMber;   document.vicidial_form.xfername.value='D3';}
									if ( (quick_transfer_button == 'PRESET_4') || (quick_transfer_button == 'LOCKED_PRESET_4') )
										{document.vicidial_form.xfernumber.value = CalL_XC_d_NuMber;   document.vicidial_form.xfername.value='D4';}
									if ( (quick_transfer_button == 'PRESET_5') || (quick_transfer_button == 'LOCKED_PRESET_5') )
										{document.vicidial_form.xfernumber.value = CalL_XC_e_NuMber;   document.vicidial_form.xfername.value='D5';}
									quick_transfer_button_orig='';
									if (quick_transfer_button_locked > 0)
										{quick_transfer_button_orig = document.vicidial_form.xfernumber.value;}

                                                                        if (quick_transfer_button_enabled > 0)
                                                                            {
                                                                                document.getElementById("QuickXfer").innerHTML = "<a href=\"#\" onclick=\"mainxfer_send_redirect('XfeRBLIND','" + lastcustchannel + "','" + lastcustserverip + "','','','" + quick_transfer_button_locked + "',1);return false;\"><img src=\"./images/x-transfer-quick.png\" border=\"0\" alt=\"QUICK TRANSFER\" /><br /><span>Quick X-fer</span></a>";
                                                                            }
									
                                                                            if (end_call_action_enabled > 0 && document.vicidial_form.xfernumber.value.length > 0)
                                                                            {
                                                                                document.getElementById("HangupControl").innerHTML = "<a href=\"#\" onclick=\"mainxfer_send_redirect('XfeRBLIND','" + lastcustchannel + "','" + lastcustserverip + "','','','" + quick_transfer_button_locked + "',1);return false;\"><img src=\"./images/x-endcall-on.png\" border=\"0\" alt=\"Hangup Customer\" /><span>End Call</span></a>";
                                                                            }
                                                                        }

								if (custom_3way_button_transfer_enabled > 0)
									{
									document.getElementById("CustomXfer").innerHTML = "<a href=\"#\" onclick=\"custom_button_transfer();return false;\"><img src=\"./images/x-transfer-custom.png\" border=\"0\" alt=\"Custom Transfer\" /><br /><span>Custom X-fer</span></a>";
									}

								if (call_requeue_button > 0)
									{
									var CloserSelectChoices = document.vicidial_form.CloserSelectList.value;
									var regCRB = new RegExp("AGENTDIRECT","ig");
									if ( (CloserSelectChoices.match(regCRB)) || (VU_closer_campaigns.match(regCRB)) )
										{
                                        document.getElementById("ReQueueCall").innerHTML =  "<a href=\"#\" onclick=\"call_requeue_launch();return false;\"><img src=\"./images/x-requeue-call.png\" border=\"0\" alt=\"Re-Queue Call\" /><br /><span>Re-Queue Call</span></a>";
										}
									else
										{
                                        document.getElementById("ReQueueCall").innerHTML =  "<a href=\"#\" style=\"opacity:.15;\" disabled><img class=\"disabled_img\" src=\"./images/x-requeue-call.png\" border=\"0\" alt=\"Re-Queue Call\" /><br /><span>Re-Queue Call</span></a>";
										}
									}

								// Build transfer pull-down list
								var loop_ct = 0;
								var live_XfeR_HTML = '';
								var XfeR_SelecT = '';
								while (loop_ct < XFgroupCOUNT)
									{
									if (VARxfergroups[loop_ct] == LIVE_default_xfer_group)
										{XfeR_SelecT = 'selected ';}
									else {XfeR_SelecT = '';}
									live_XfeR_HTML = live_XfeR_HTML + "<option " + XfeR_SelecT + "value=\"" + VARxfergroups[loop_ct] + "\">" + VARxfergroups[loop_ct] + " - " + VARxfergroupsnames[loop_ct] + "</option>\n";
									loop_ct++;
									}
                                document.getElementById("XfeRGrouPLisT").innerHTML = "<select size=\"1\" name=\"XfeRGrouP\" id=\"XfeRGrouP\" class=\"cust_form\" onChange=\"XferAgentSelectLink();return false;\">" + live_XfeR_HTML + "</select>";

								// INSERT VICIDIAL_LOG ENTRY FOR THIS CALL PROCESS
								DialLog("start");

								custchannellive=1;
								}
							}
						}
					}
				}
			delete xmlhttp;
			}

		if (MD_ring_secondS > 49) 
			{
			MD_channel_look=0;
			MD_ring_secondS=0;
			alert_box("No answer was received for almost a minute. Please try again later.\n");
			if (taskCheckOR == 'YES')
				{
				xfercall_send_hangup();
				}
			}

		}

// ################################################################################
// Update Agent screen with values from vicidial_list record
	function UpdateFieldsData()
		{
		var fields_list = update_fields_data + ',';
		update_fields=0;
		update_fields_data='';
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
			UpdateFields_query = "server_ip=" + server_ip + "&session_name=" + session_name + "&ACTION=UpdateFields&conf_exten=" + session_id + "&user=" + user + "&pass=" + pass + "&stage=" + update_fields_data;
			//		alert(manual_dial_filter + "\n" +manDiaLnext_query);
			xmlhttp.open('POST', 'vdc_db_query.php');
			xmlhttp.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
			xmlhttp.send(UpdateFields_query); 
			xmlhttp.onreadystatechange = function() 
				{ 
				if (xmlhttp.readyState == 4 && xmlhttp.status == 200) 
					{
					var UDfieldsResponse = null;
				//	alert(UpdateFields_query);
				//	alert(xmlhttp.responseText);
					UDfieldsResponse = xmlhttp.responseText;

					var UDfieldsResponse_array=UDfieldsResponse.split("\n");

					var UDresponse_status							= UDfieldsResponse_array[0];
					if (UDresponse_status == 'GOOD')
						{
						var regUDvendor_lead_code = new RegExp("vendor_lead_code,","ig");
						if (fields_list.match(regUDvendor_lead_code))
							{document.vicidial_form.vendor_lead_code.value	= UDfieldsResponse_array[1];}
						var regUDsource_id = new RegExp("source_id,","ig");
						if (fields_list.match(regUDsource_id))
							{source_id										= UDfieldsResponse_array[2];}
						var regUDgmt_offset_now = new RegExp("gmt_offset_now,","ig");
						if (fields_list.match(regUDgmt_offset_now))
							{document.vicidial_form.gmt_offset_now.value	= UDfieldsResponse_array[3];}
						var regUDphone_code = new RegExp("phone_code,","ig");
						if (fields_list.match(regUDphone_code))
							{document.vicidial_form.phone_code.value		= UDfieldsResponse_array[4];}
						var regUDphone_number = new RegExp("phone_number,","ig");
						if (fields_list.match(regUDphone_number))
							{
							if ( (disable_alter_custphone=='Y') || (disable_alter_custphone=='HIDE') )
								{
								var tmp_pn = document.getElementById("phone_numberDISP");
								if (disable_alter_custphone=='Y')
									{
									tmp_pn.innerHTML						= UDfieldsResponse_array[5];
									}
								}
							document.vicidial_form.phone_number.value		= UDfieldsResponse_array[5];
							}
						var regUDtitle = new RegExp("title,","ig");
						if (fields_list.match(regUDtitle))
							{document.vicidial_form.title.value				= UDfieldsResponse_array[6];}
						var regUDfirst_name = new RegExp("first_name,","ig");
						if (fields_list.match(regUDfirst_name))
							{document.vicidial_form.first_name.value		= UDfieldsResponse_array[7];}
						var regUDmiddle_initial = new RegExp("middle_initial,","ig");
						if (fields_list.match(regUDmiddle_initial))
							{document.vicidial_form.middle_initial.value	= UDfieldsResponse_array[8];}
						var regUDlast_name = new RegExp("last_name,","ig");
						if (fields_list.match(regUDlast_name))
							{document.vicidial_form.last_name.value			= UDfieldsResponse_array[9];}
						var regUDaddress1 = new RegExp("address1,","ig");
						if (fields_list.match(regUDaddress1))
							{document.vicidial_form.address1.value			= UDfieldsResponse_array[10];}
						var regUDaddress2 = new RegExp("address2,","ig");
						if (fields_list.match(regUDaddress2))
							{document.vicidial_form.address2.value			= UDfieldsResponse_array[11];}
						var regUDaddress3 = new RegExp("address3,","ig");
						if (fields_list.match(regUDaddress3))
							{document.vicidial_form.address3.value			= UDfieldsResponse_array[12];}
						var regUDcity = new RegExp("city,","ig");
						if (fields_list.match(regUDcity))
							{document.vicidial_form.city.value				= UDfieldsResponse_array[13];}
						var regUDstate = new RegExp("state,","ig");
						if (fields_list.match(regUDstate))
							{document.vicidial_form.state.value				= UDfieldsResponse_array[14];}
						var regUDprovince = new RegExp("province,","ig");
						if (fields_list.match(regUDprovince))
							{document.vicidial_form.province.value			= UDfieldsResponse_array[15];}
						var regUDpostal_code = new RegExp("postal_code,","ig");
						if (fields_list.match(regUDpostal_code))
							{document.vicidial_form.postal_code.value		= UDfieldsResponse_array[16];}
						var regUDcountry_code = new RegExp("country_code,","ig");
						if (fields_list.match(regUDcountry_code))
							{document.vicidial_form.country_code.value		= UDfieldsResponse_array[17];}
						var regUDgender = new RegExp("gender,","ig");
						if (fields_list.match(regUDgender))
							{
							document.vicidial_form.gender.value				= UDfieldsResponse_array[18];
							if (hide_gender > 0)
								{
								document.vicidial_form.gender_list.value		= UDfieldsResponse_array[18];
								}
							else
								{
								var gIndex = 0;
								if (document.vicidial_form.gender.value == 'M') {var gIndex = 1;}
								if (document.vicidial_form.gender.value == 'F') {var gIndex = 2;}
								document.getElementById("gender_list").selectedIndex = gIndex;
								var genderIndex = document.getElementById("gender_list").selectedIndex;
								var genderValue =  document.getElementById('gender_list').options[genderIndex].value;
								document.vicidial_form.gender.value = genderValue;
								}
							}
						var regUDdate_of_birth = new RegExp("date_of_birth,","ig");
						if (fields_list.match(regUDdate_of_birth))
							{document.vicidial_form.date_of_birth.value		= UDfieldsResponse_array[19];}
						var regUDalt_phone = new RegExp("alt_phone,","ig");
						if (fields_list.match(regUDalt_phone))
							{document.vicidial_form.alt_phone.value			= UDfieldsResponse_array[20];}
						var regUDemail = new RegExp("email,","ig");
						if (fields_list.match(regUDemail))
							{document.vicidial_form.email.value				= UDfieldsResponse_array[21];}
						var regUDsecurity_phrase = new RegExp("security_phrase,","ig");
						if (fields_list.match(regUDsecurity_phrase))
							{document.vicidial_form.security_phrase.value	= UDfieldsResponse_array[22];}
						var regUDcomments = new RegExp("comments,","ig");
						if (fields_list.match(regUDcomments))
							{
							var REGcommentsNL = new RegExp("!N","g");
							UDfieldsResponse_array[23] = UDfieldsResponse_array[23].replace(REGcommentsNL, "\n");
							document.vicidial_form.comments.value			= UDfieldsResponse_array[23];
							}
						var regUDrank = new RegExp("rank,","ig");
						if (fields_list.match(regUDrank))
							{document.vicidial_form.rank.value				= UDfieldsResponse_array[24];}
						var regUDowner = new RegExp("owner,","ig");
						if (fields_list.match(regUDowner))
							{document.vicidial_form.owner.value				= UDfieldsResponse_array[25];}
						var regUDformreload = new RegExp("formreload,","ig");
						if (fields_list.match(regUDformreload))
							{FormContentsLoad();}

						var regWFAcustom = new RegExp("^VAR","ig");
						if (VDIC_web_form_address.match(regWFAcustom))
							{
							TEMP_VDIC_web_form_address = URLDecode(VDIC_web_form_address,'YES','CUSTOM');
							TEMP_VDIC_web_form_address = TEMP_VDIC_web_form_address.replace(regWFAcustom, '');
							}
						else
							{
							TEMP_VDIC_web_form_address = URLDecode(VDIC_web_form_address,'YES','DEFAULT','1');
							}

						if (VDIC_web_form_address_two.match(regWFAcustom))
							{
							TEMP_VDIC_web_form_address_two = URLDecode(VDIC_web_form_address_two,'YES','CUSTOM');
							TEMP_VDIC_web_form_address_two = TEMP_VDIC_web_form_address_two.replace(regWFAcustom, '');
							}
						else
							{
							TEMP_VDIC_web_form_address_two = URLDecode(VDIC_web_form_address_two,'YES','DEFAULT','2');
							}

                                                document.getElementById("WebFormSpan").innerHTML = "<a href=\"" + TEMP_VDIC_web_form_address + "\" target=\"" + web_form_target + "\" onMouseOver=\"WebFormRefresH();\"><img src=\"./images/x-webform.png\" border=\"0\" alt=\"Web Form\" /><br /><span id=\"x-webform\">Launch</span></a>\n";
						if (enable_second_webform > 0)
							{
                                                        document.getElementById("WebFormSpanTwo").innerHTML = "<a href=\"" + TEMP_VDIC_web_form_address_two + "\" target=\"" + web_form_target + "\" onMouseOver=\"WebFormTwoRefresH();\"><img src=\"./images/x-webform.png\" border=\"0\" alt=\"Web Form 2\" /><br /><span id=\"x-webform\">Launch 2nd</span></a>\n";
							}
						}
					else
						{
						alert_box("Update Fields Error!: " + xmlhttp.responseText);
						}
					}
				}
			}
		}


// ################################################################################
// Send the Manual Dial Next Number request
	function ManualDialNext(mdnCBid,mdnBDleadid,mdnDiaLCodE,mdnPhonENumbeR,mdnStagE,mdVendorid,mdgroupalias,mdtype)
		{
		if (waiting_on_dispo > 0)
			{
			alert_box("System Delay, Please try again<BR><font size=1>code:" + agent_log_id + " - " + waiting_on_dispo + "</font>");
			}
		else
			{
			inOUT = 'OUT';
			all_record = 'NO';
			all_record_count=0;
			if (dial_method == "INBOUND_MAN")
				{
				auto_dial_level=0;

				if (VDRP_stage != 'PAUSED')
					{
					agent_log_id = AutoDial_ReSume_PauSe("VDADpause",'','','',"DIALNEXT",'1','NXDIAL');

				//	PauseCodeSelect_submit("NXDIAL");
					}
				else
					{auto_dial_level=starting_dial_level;}

				document.getElementById("DiaLControl").innerHTML = DiaLControl_auto_HTML_OFF;
                                document.getElementById("ManualDiaLControl").innerHTML = DiaLControl_manual_HTML_OFF;
				}
			else
				{
				document.getElementById("DiaLControl").innerHTML = DiaLControl_auto_HTML_OFF;
                                document.getElementById("ManualDiaLControl").innerHTML = DiaLControl_manual_HTML_OFF;
				}
			var manual_dial_only_type_flag = '';
			if ( (mdtype == 'ALT') || (mdtype == 'ADDR3') )
				{
				agent_dialed_type = mdtype;
				agent_dialed_number = mdnPhonENumbeR;
				if (mdtype == 'ALT')
					{manual_dial_only_type_flag = 'ALTPhonE';}
				if (mdtype == 'ADDR3')
					{manual_dial_only_type_flag = 'AddresS3';}
				}
			if (document.vicidial_form.LeadPreview.checked==true)
				{
				reselect_preview_dial = 1;
				in_lead_preview_state = 1;
				var man_preview = 'YES';
				var man_status = "Preview the Lead then <a href=\"#\" onclick=\"ManualDialOnly('" + manual_dial_only_type_flag + "')\"><font class=\"preview_text\"> Dial It</font></a> or <a href=\"#\" onclick=\"ManualDialSkip()\"><font class=\"preview_text\">Skip It</font></a>"; 
				if (manual_preview_dial=='PREVIEW_ONLY')
					{
					var man_status = "Preview the Lead then <a href=\"#\" onclick=\"ManualDialOnly('" + manual_dial_only_type_flag + "')\"><font class=\"preview_text\"> Dial It</font></a>"; 
					}
				}
			else
				{
				reselect_preview_dial = 0;
				var man_preview = 'NO';
				var man_status = "Waiting for Ring..."; 
				}

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
				if (cid_choice.length > 3) 
					{var call_cid = cid_choice;}
				else 
					{
					var call_cid = campaign_cid;
					if (manual_dial_cid == 'AGENT_PHONE')
						{call_cid = outbound_cid;}
					}
				if (prefix_choice.length > 0)
					{var call_prefix = prefix_choice;}
				else
					{var call_prefix = manual_dial_prefix;}

                                var manual_dial_next_action = 'manDiaLnextCaLL';
                                if (on_hook_agent == 'Y') // if agent is on hook, need to call manDiaLnextCaLL both to agent and manually dialed number
                                {
                                    manual_dial_next_action = 'manDiaLnextCaLLOnHook';
                                }
                    
				manDiaLnext_query = "server_ip=" + server_ip + "&session_name=" + session_name + "&ACTION=" + manual_dial_next_action + "&conf_exten=" + session_id + "&user=" + user + "&pass=" + pass + "&campaign=" + campaign + "&ext_context=" + ext_context + "&dial_timeout=" + dial_timeout + "&dial_prefix=" + call_prefix + "&campaign_cid=" + call_cid + "&preview=" + man_preview + "&agent_log_id=" + agent_log_id + "&callback_id=" + mdnCBid + "&lead_id=" + mdnBDleadid + "&phone_code=" + mdnDiaLCodE + "&phone_number=" + mdnPhonENumbeR + "&list_id=" + mdnLisT_id + "&stage=" + mdnStagE  + "&use_internal_dnc=" + use_internal_dnc + "&use_campaign_dnc=" + use_campaign_dnc + "&omit_phone_code=" + omit_phone_code + "&manual_dial_filter=" + manual_dial_filter + "&vendor_lead_code=" + mdVendorid + "&usegroupalias=" + mdgroupalias + "&account=" + active_group_alias + "&agent_dialed_number=" + agent_dialed_number + "&agent_dialed_type=" + agent_dialed_type + "&vtiger_callback_id=" + vtiger_callback_id + "&dial_method=" + dial_method + "&manual_dial_call_time_check=" + manual_dial_call_time_check + "&extrachannel=" + SIP_user_DiaL + "&customCID=" + SIqueryCID;
				//		alert(manual_dial_filter + "\n" +manDiaLnext_query);
				xmlhttp.open('POST', 'vdc_db_query.php');
				xmlhttp.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
				xmlhttp.send(manDiaLnext_query); 
				xmlhttp.onreadystatechange = function() 
					{ 
					if (xmlhttp.readyState == 4 && xmlhttp.status == 200) 
						{
						var MDnextResponse = null;
					//	alert(manDiaLnext_query);
					//	alert(xmlhttp.responseText);
						MDnextResponse = xmlhttp.responseText;

						var MDnextResponse_array=MDnextResponse.split("\n");
						MDnextCID = MDnextResponse_array[0];
						LastCallCID = MDnextResponse_array[0];

						var regMNCvar = new RegExp("HOPPER EMPTY","ig");
						var regMDFvarDNC = new RegExp("DNC","ig");
						var regMDFvarCAMP = new RegExp("CAMPLISTS","ig");
						var regMDFvarTIME = new RegExp("OUTSIDE","ig");
						if ( (MDnextCID.match(regMNCvar)) || (MDnextCID.match(regMDFvarDNC)) || (MDnextCID.match(regMDFvarCAMP)) || (MDnextCID.match(regMDFvarTIME)) )
							{
							var alert_displayed=0;
							trigger_ready=1;
							alt_phone_dialing=starting_alt_phone_dialing;
							auto_dial_level=starting_dial_level;
							MainPanelToFront();
							CalLBacKsCounTCheck();

							if (MDnextCID.match(regMNCvar))
								{alert_box("No more leads in the hopper for campaign:\n" + campaign);   alert_displayed=1;}
							if (MDnextCID.match(regMDFvarDNC))
								{alert_box("This phone number is in the DNC list:\n" + mdnPhonENumbeR);   alert_displayed=1;}
							if (MDnextCID.match(regMDFvarCAMP))
								{alert_box("This phone number is not in the campaign lists:\n" + mdnPhonENumbeR);   alert_displayed=1;}
							if (MDnextCID.match(regMDFvarTIME))
								{alert_box("This phone number is outside of the local call time:\n" + mdnPhonENumbeR);   alert_displayed=1;}
							if (alert_displayed==0)						
								{alert_box("Unspecified error:\n" + mdnPhonENumbeR + "|" + MDnextCID);   alert_displayed=1;}

							if (starting_dial_level == 0)
								{
								document.getElementById("DiaLControl").innerHTML = DiaLControl_auto_HTML_OFF;
                                                                document.getElementById("ManualDiaLControl").innerHTML = DiaLControl_manual_HTML;
								}
							else
								{
								if (dial_method == "INBOUND_MAN")
									{
									auto_dial_level=starting_dial_level;

									document.getElementById("DiaLControl").innerHTML = DiaLControl_auto_HTML;
                                                                        document.getElementById("ManualDiaLControl").innerHTML = DiaLControl_manual_HTML;
									}
								else
									{
									document.getElementById("DiaLControl").innerHTML = DiaLControl_auto_HTML;
                                                                        document.getElementById("ManualDiaLControl").innerHTML = DiaLControl_manual_HTML_OFF;
									}
								//document.getElementById("MainStatuSSpan").style.background = panel_bgcolor;
								reselect_alt_dial = 0;
								}
							}
						else
							{
							fronter = user;
							LasTCID											= MDnextResponse_array[0];
							document.vicidial_form.lead_id.value			= MDnextResponse_array[1];
							LeaDPreVDispO									= MDnextResponse_array[2];
							document.vicidial_form.vendor_lead_code.value	= MDnextResponse_array[4];
							document.vicidial_form.list_id.value			= MDnextResponse_array[5];
							document.vicidial_form.gmt_offset_now.value		= MDnextResponse_array[6];
							document.vicidial_form.phone_code.value			= MDnextResponse_array[7];
							if ( (disable_alter_custphone=='Y') || (disable_alter_custphone=='HIDE') )
								{
								var tmp_pn = document.getElementById("phone_numberDISP");
								if (disable_alter_custphone=='Y')
									{
									tmp_pn.innerHTML						= MDnextResponse_array[8];
									}
								}
							document.vicidial_form.phone_number.value		= MDnextResponse_array[8];
							document.vicidial_form.title.value				= MDnextResponse_array[9];
							document.vicidial_form.first_name.value			= MDnextResponse_array[10];
							document.vicidial_form.middle_initial.value		= MDnextResponse_array[11];
							document.vicidial_form.last_name.value			= MDnextResponse_array[12];
							document.vicidial_form.address1.value			= MDnextResponse_array[13];
							document.vicidial_form.address2.value			= MDnextResponse_array[14];
							document.vicidial_form.address3.value			= MDnextResponse_array[15];
							document.vicidial_form.city.value				= MDnextResponse_array[16];
							document.vicidial_form.state.value				= MDnextResponse_array[17];
							document.vicidial_form.province.value			= MDnextResponse_array[18];
							document.vicidial_form.postal_code.value		= MDnextResponse_array[19];
							document.vicidial_form.country_code.value		= MDnextResponse_array[20];
							document.vicidial_form.gender.value				= MDnextResponse_array[21];
							document.vicidial_form.date_of_birth.value		= MDnextResponse_array[22];
							document.vicidial_form.alt_phone.value			= MDnextResponse_array[23];
							document.vicidial_form.email.value				= MDnextResponse_array[24];
							document.vicidial_form.security_phrase.value	= MDnextResponse_array[25];
							var REGcommentsNL = new RegExp("!N","g");
							MDnextResponse_array[26] = MDnextResponse_array[26].replace(REGcommentsNL, "\n");
							document.vicidial_form.comments.value			= MDnextResponse_array[26];
                                                        var quantityStr = " times";
                                                        if (MDnextResponse_array[27]==1)
                                                            {quantityStr = " time";}
                                                        document.getElementById('called_count_desc').innerHTML = "&nbsp;-&nbsp;Has been called " + MDnextResponse_array[27] + quantityStr;
							document.vicidial_form.called_count.value		= MDnextResponse_array[27];
							previous_called_count							= MDnextResponse_array[27];
							previous_dispo									= MDnextResponse_array[2];
							CBentry_time									= MDnextResponse_array[28];
							CBcallback_time									= MDnextResponse_array[29];
							CBuser											= MDnextResponse_array[30];
							CBcomments										= MDnextResponse_array[31];
							dialed_number									= MDnextResponse_array[32];
							dialed_label									= MDnextResponse_array[33];
							source_id										= MDnextResponse_array[34];
							document.vicidial_form.rank.value				= MDnextResponse_array[35];
							document.vicidial_form.owner.value				= MDnextResponse_array[36];
						//	CalL_ScripT_id									= MDnextResponse_array[37];
							script_recording_delay							= MDnextResponse_array[38];
							CalL_XC_a_NuMber								= MDnextResponse_array[39];
							CalL_XC_b_NuMber								= MDnextResponse_array[40];
							CalL_XC_c_NuMber								= MDnextResponse_array[41];
							CalL_XC_d_NuMber								= MDnextResponse_array[42];
							CalL_XC_e_NuMber								= MDnextResponse_array[43];
							document.vicidial_form.entry_list_id.value		= MDnextResponse_array[44];
							custom_field_names								= MDnextResponse_array[45];
							custom_field_values								= MDnextResponse_array[46];
							custom_field_types								= MDnextResponse_array[47];
							var list_webform								= MDnextResponse_array[48];
							var list_webform_two							= MDnextResponse_array[49];
							post_phone_time_diff_alert_message				= MDnextResponse_array[50];

							timer_action = campaign_timer_action;
							timer_action_message = campaign_timer_action_message;
							timer_action_seconds = campaign_timer_action_seconds;
							timer_action_destination = campaign_timer_action_destination;
				
							lead_dial_number = dialed_number;
							var dispnum = dialed_number;
							var status_display_number = phone_number_format(dispnum);

							//document.getElementById("MainStatuSSpan").innerHTML = " Calling: " + status_display_number + " UID: " + MDnextCID + " &nbsp; " + man_status;
                                                        document.getElementById("MainStatuSSpan").innerHTML = " Calling: " + status_display_number + " &nbsp; " + man_status;
							if ( (dialed_label.length < 2) || (dialed_label=='NONE') ) {dialed_label='MAIN';}

							if (hide_gender > 0)
								{
								document.vicidial_form.gender_list.value		= MDnextResponse_array[21];
								}
							else
								{
								var gIndex = 0;
								if (document.vicidial_form.gender.value == 'M') {var gIndex = 1;}
								if (document.vicidial_form.gender.value == 'F') {var gIndex = 2;}
								document.getElementById("gender_list").selectedIndex = gIndex;
								var genderIndex = document.getElementById("gender_list").selectedIndex;
								var genderValue =  document.getElementById('gender_list').options[genderIndex].value;
								document.vicidial_form.gender.value = genderValue;
								}

							LeaDDispO='';

							VDIC_web_form_address = VICIDiaL_web_form_address
							VDIC_web_form_address_two = VICIDiaL_web_form_address_two
							if (list_webform.length > 5) {VDIC_web_form_address=list_webform;}
							if (list_webform_two.length > 5) {VDIC_web_form_address_two=list_webform_two;}

							var regWFAcustom = new RegExp("^VAR","ig");
							if (VDIC_web_form_address.match(regWFAcustom))
								{
								TEMP_VDIC_web_form_address = URLDecode(VDIC_web_form_address,'YES','CUSTOM');
								TEMP_VDIC_web_form_address = TEMP_VDIC_web_form_address.replace(regWFAcustom, '');
								}
							else
								{
								TEMP_VDIC_web_form_address = URLDecode(VDIC_web_form_address,'YES','DEFAULT','1');
								}

							if (VDIC_web_form_address_two.match(regWFAcustom))
								{
								TEMP_VDIC_web_form_address_two = URLDecode(VDIC_web_form_address_two,'YES','CUSTOM');
								TEMP_VDIC_web_form_address_two = TEMP_VDIC_web_form_address_two.replace(regWFAcustom, '');
								}
							else
								{
								TEMP_VDIC_web_form_address_two = URLDecode(VDIC_web_form_address_two,'YES','DEFAULT','2');
								}

							document.getElementById("WebFormSpan").innerHTML = "<a href=\"" + TEMP_VDIC_web_form_address + "\" target=\"" + web_form_target + "\" onMouseOver=\"WebFormRefresH();\"><img src=\"./images/x-webform.png\" border=\"0\" alt=\"Web Form\" /><br /><span id=\"x-webform\">Launch</span></a>\n";
							if (enable_second_webform > 0)
								{
								document.getElementById("WebFormSpanTwo").innerHTML = "<a href=\"" + TEMP_VDIC_web_form_address_two + "\" target=\"" + web_form_target + "\" onMouseOver=\"WebFormTwoRefresH();\"><img src=\"./images/x-webform.png\" border=\"0\" alt=\"Web Form 2\" /><br /><span id=\"x-webform\">Launch 2nd</span></a>\n";
								}

							if (CBentry_time.length > 2)
								{
								document.getElementById("CusTInfOSpaN").innerHTML = " <b> PREVIOUS CALLBACK </b>";
								document.getElementById("CusTInfOSpaN").style.background = CusTCB_bgcolor;
								document.getElementById("CBcommentsBoxA").innerHTML = "<b>Last Call: </b>" + CBentry_time;
								document.getElementById("CBcommentsBoxB").innerHTML = "<b>CallBack: </b>" + CBcallback_time;
								document.getElementById("CBcommentsBoxC").innerHTML = "<b>Agent: </b>" + CBuser;
								document.getElementById("CBcommentsBoxD").innerHTML = "<b>Comments: </b><br />" + CBcomments;
								showDiv('CBcommentsBox');
								}

							if (post_phone_time_diff_alert_message.length > 10)
								{
								document.getElementById("post_phone_time_diff_span_contents").innerHTML = " &nbsp; &nbsp; " + post_phone_time_diff_alert_message + "<br />";
								showDiv('post_phone_time_diff_span');
								}

							if (document.vicidial_form.LeadPreview.checked==false)
								{
								reselect_preview_dial = 0;
								MD_channel_look=1;
								custchannellive=1;

								document.getElementById("HangupControl").innerHTML = "<a href=\"#\" onclick=\"dialedcall_send_hangup();\"><img src=\"./images/x-endcall-on.png\" border=\"0\" alt=\"Hangup Customer\" /><span>End Call</span></a>";

								if ( (LIVE_campaign_recording == 'ALLCALLS') || (LIVE_campaign_recording == 'ALLFORCE') )
									{all_record = 'YES';}

								if ( (view_scripts == 1) && (campaign_script.length > 0) )
									{
									var SCRIPT_web_form = 'http://127.0.0.1/testing.php';
									var TEMP_SCRIPT_web_form = URLDecode(SCRIPT_web_form,'YES','DEFAULT','1');

									if ( (script_recording_delay > 0) && ( (LIVE_campaign_recording == 'ALLCALLS') || (LIVE_campaign_recording == 'ALLFORCE') ) )
										{
										delayed_script_load = 'YES';
										RefresHScript('CLEAR');
										}
									else
										{
										load_script_contents();
										}
									}

								if (custom_fields_enabled > 0)
									{
									FormContentsLoad();
									}
								if (get_call_launch == 'SCRIPT')
									{
									if (delayed_script_load == 'YES')
										{
										load_script_contents();
										}
									ScriptPanelToFront();
									}

								if (get_call_launch == 'FORM')
									{
									FormPanelToFront();
									}


								if (get_call_launch == 'WEBFORM')
									{
									window.open(TEMP_VDIC_web_form_address, web_form_target, 'toolbar=1,scrollbars=1,location=1,statusbar=1,menubar=1,resizable=1,width=640,height=450');
									}
								if (get_call_launch == 'WEBFORMTWO')
									{
									window.open(TEMP_VDIC_web_form_address_two, web_form_target, 'toolbar=1,scrollbars=1,location=1,statusbar=1,menubar=1,resizable=1,width=640,height=450');
									}

								}
							else
								{
								reselect_preview_dial = 1;
								}
							}
						}
					}
				delete xmlhttp;

				if (document.vicidial_form.LeadPreview.checked==false)
					{
					active_group_alias='';
					cid_choice='';
					prefix_choice='';
					agent_dialed_number='';
					agent_dialed_type='';
					CalL_ScripT_id='';
					}
				}
			}
		}


// ################################################################################
// Send the Manual Dial Skip
	function ManualDialSkip()
		{
		if (manual_dial_in_progress==1)
			{
			alert_box('YOU CANNOT SKIP A CALLBACK OR MANUAL DIAL, YOU MUST DIAL THE LEAD');
			}
		else
			{
			in_lead_preview_state=0;
			if (dial_method == "INBOUND_MAN")
				{
				auto_dial_level=starting_dial_level;

                                document.getElementById("DiaLControl").innerHTML = DiaLControl_auto_HTML_OFF;
                                document.getElementById("ManualDiaLControl").innerHTML = DiaLControl_manual_HTML_OFF;
				}
			else
				{
                                document.getElementById("DiaLControl").innerHTML = DiaLControl_auto_HTML_OFF;
                                document.getElementById("ManualDiaLControl").innerHTML = DiaLControl_manual_HTML_OFF;
				}

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
				manDiaLskip_query = "server_ip=" + server_ip + "&session_name=" + session_name + "&ACTION=manDiaLskip&conf_exten=" + session_id + "&user=" + user + "&pass=" + pass + "&lead_id=" + document.vicidial_form.lead_id.value + "&stage=" + previous_dispo + "&called_count=" + previous_called_count;
				xmlhttp.open('POST', 'vdc_db_query.php'); 
				xmlhttp.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
				xmlhttp.send(manDiaLskip_query); 
				xmlhttp.onreadystatechange = function() 
					{ 
					if (xmlhttp.readyState == 4 && xmlhttp.status == 200) 
						{
						var MDSnextResponse = null;
					//	alert(manDiaLskip_query);
					//	alert(xmlhttp.responseText);
						MDSnextResponse = xmlhttp.responseText;

						var MDSnextResponse_array=MDSnextResponse.split("\n");
						MDSnextCID = MDSnextResponse_array[0];
						if (MDSnextCID == "LEAD NOT REVERTED")
							{
							alert_box("Lead was not reverted, there was an error:\n" + MDSnextResponse);
							}
						else
							{
							document.vicidial_form.lead_id.value		='';
							document.vicidial_form.vendor_lead_code.value='';
							document.vicidial_form.list_id.value		='';
							document.vicidial_form.entry_list_id.value	='';
							document.vicidial_form.gmt_offset_now.value	='';
							document.vicidial_form.phone_code.value		='';
							if ( (disable_alter_custphone=='Y') || (disable_alter_custphone=='HIDE') )
								{
								var tmp_pn = document.getElementById("phone_numberDISP");
								tmp_pn.innerHTML			= ' &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; ';
								}
							document.vicidial_form.phone_number.value	='';
							document.vicidial_form.title.value			='';
							document.vicidial_form.first_name.value		='';
							document.vicidial_form.middle_initial.value	='';
							document.vicidial_form.last_name.value		='';
							document.vicidial_form.address1.value		='';
							document.vicidial_form.address2.value		='';
							document.vicidial_form.address3.value		='';
							document.vicidial_form.city.value			='';
							document.vicidial_form.state.value			='';
							document.vicidial_form.province.value		='';
							document.vicidial_form.postal_code.value	='';
							document.vicidial_form.country_code.value	='';
							document.vicidial_form.gender.value			='';
							document.vicidial_form.date_of_birth.value	='';
							document.vicidial_form.alt_phone.value		='';
							document.vicidial_form.email.value			='';
							document.vicidial_form.security_phrase.value='';
							document.vicidial_form.comments.value		='';
							document.vicidial_form.called_count.value	='';
                                                        document.getElementById('called_count_desc').innerHTML = '';
							document.vicidial_form.rank.value			='';
							document.vicidial_form.owner.value			='';
							VDCL_group_id = '';
							fronter = '';
							previous_called_count = '';
							previous_dispo = '';
							custchannellive=1;

							if (post_phone_time_diff_alert_message.length > 10)
								{
								document.getElementById("post_phone_time_diff_span_contents").innerHTML = "";
								hideDiv('post_phone_time_diff_span');
								}

							document.getElementById("MainStatuSSpan").innerHTML = " Lead skipped, go on to next lead";

							if (dial_method == "INBOUND_MAN")
								{
                                                                document.getElementById("DiaLControl").innerHTML = DiaLControl_auto_HTML;
                                                                document.getElementById("ManualDiaLControl").innerHTML = DiaLControl_manual_HTML;
								}
							else
								{
                                                                document.getElementById("DiaLControl").innerHTML = DiaLControl_auto_HTML_OFF;
                                                                document.getElementById("ManualDiaLControl").innerHTML = DiaLControl_manual_HTML;
								}
							}
						}
					}
				delete xmlhttp;
				active_group_alias='';
				cid_choice='';
				prefix_choice='';
				agent_dialed_number='';
				agent_dialed_type='';
				CalL_ScripT_id='';
				}
			}
		}


// ################################################################################
// Send the Manual Dial Only - dial the previewed lead
	function ManualDialOnly(taskaltnum)
		{
		in_lead_preview_state=0;
		inOUT = 'OUT';
		alt_dial_status_display = 0;
		all_record = 'NO';
		all_record_count=0;
		var usegroupalias=0;
		if (taskaltnum == 'ALTPhonE')
			{
			var manDiaLonly_num = document.vicidial_form.alt_phone.value;
			lead_dial_number = document.vicidial_form.alt_phone.value;
			dialed_number = lead_dial_number;
			dialed_label = 'ALT';
			WebFormRefresH('');
			}
		else
			{
			if (taskaltnum == 'AddresS3')
				{
				var manDiaLonly_num = document.vicidial_form.address3.value;
				lead_dial_number = document.vicidial_form.address3.value;
				dialed_number = lead_dial_number;
				dialed_label = 'ADDR3';
				WebFormRefresH('');
				}
			else
				{
				var manDiaLonly_num = document.vicidial_form.phone_number.value;
				lead_dial_number = document.vicidial_form.phone_number.value;
				dialed_number = lead_dial_number;
				dialed_label = 'MAIN';
				WebFormRefresH('');
				}
			}
		if (dialed_label == 'ALT')
            {document.getElementById("CusTInfOSpaN").innerHTML = " <b> ALT DIAL NUMBER: ALT </b>";}
		if (dialed_label == 'ADDR3')
            {document.getElementById("CusTInfOSpaN").innerHTML = " <b> ALT DIAL NUMBER: ADDRESS3 </b>";}
		var REGalt_dial = new RegExp("X","g");
		if (dialed_label.match(REGalt_dial))
			{
            document.getElementById("CusTInfOSpaN").innerHTML = " <b> ALT DIAL NUMBER: " + dialed_label + "</b>";
			document.getElementById("EAcommentsBoxA").innerHTML = "<b>Phone Code and Number: </b>" + EAphone_code + " " + EAphone_number;

			var EAactive_link = '';
			if (EAalt_phone_active == 'Y') 
				{EAactive_link = "<a href=\"#\" onclick=\"alt_phone_change('" + EAphone_number + "','" + EAalt_phone_count + "','" + document.vicidial_form.lead_id.value + "','N');\">Change this phone number to INACTIVE</a>";}
			else
				{EAactive_link = "<a href=\"#\" onclick=\"alt_phone_change('" + EAphone_number + "','" + EAalt_phone_count + "','" + document.vicidial_form.lead_id.value + "','Y');\">Change this phone number to ACTIVE</a>";}

            document.getElementById("EAcommentsBoxB").innerHTML = "<b>Active: </b>" + EAalt_phone_active + "<br />" + EAactive_link;
			document.getElementById("EAcommentsBoxC").innerHTML = "<b>Alt Count: </b>" + EAalt_phone_count;
            document.getElementById("EAcommentsBoxD").innerHTML = "<b>Notes: </b><br />" + EAalt_phone_notes;
			showDiv('EAcommentsBox');
			}

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
			if (cid_choice.length > 3) 
				{
				var call_cid = cid_choice;
				usegroupalias=1;
				}
			else 
				{
				var call_cid = campaign_cid;
				if (manual_dial_cid == 'AGENT_PHONE')
					{call_cid = outbound_cid;}
				}
			if (prefix_choice.length > 0)
				{var call_prefix = prefix_choice;}
			else
				{var call_prefix = manual_dial_prefix;}

			manDiaLonly_query = "server_ip=" + server_ip + "&session_name=" + session_name + "&ACTION=manDiaLonly&conf_exten=" + session_id + "&user=" + user + "&pass=" + pass + "&lead_id=" + document.vicidial_form.lead_id.value + "&phone_number=" + manDiaLonly_num + "&phone_code=" + document.vicidial_form.phone_code.value + "&campaign=" + campaign + "&ext_context=" + ext_context + "&dial_timeout=" + dial_timeout + "&dial_prefix=" + call_prefix + "&campaign_cid=" + call_cid + "&omit_phone_code=" + omit_phone_code + "&usegroupalias=" + usegroupalias + "&account=" + active_group_alias + "&agent_dialed_number=" + agent_dialed_number + "&agent_dialed_type=" + agent_dialed_type + "&dial_method=" + dial_method + "&agent_log_id=" + agent_log_id + "&security=" + document.vicidial_form.security_phrase.value;
			xmlhttp.open('POST', 'vdc_db_query.php'); 
			xmlhttp.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
			xmlhttp.send(manDiaLonly_query);
			xmlhttp.onreadystatechange = function() 
				{ 
				if (xmlhttp.readyState == 4 && xmlhttp.status == 200) 
					{
					var MDOnextResponse = null;
			//		alert(manDiaLonly_query);
			//		alert(xmlhttp.responseText);
					MDOnextResponse = xmlhttp.responseText;

					var MDOnextResponse_array=MDOnextResponse.split("\n");
					MDnextCID =		MDOnextResponse_array[0];
					LastCallCID =	MDOnextResponse_array[0];
					agent_log_id =	MDOnextResponse_array[1];
					if (MDnextCID == " CALL NOT PLACED")
						{
						alert_box("call was not placed, there was an error:\n" + MDOnextResponse);
						}
					else
						{
						LasTCID =	MDOnextResponse_array[0];
						MD_channel_look=1;
						custchannellive=1;

						var dispnum = manDiaLonly_num;
						var status_display_number = phone_number_format(dispnum);

						if (alt_dial_status_display=='0')
							{
							//document.getElementById("MainStatuSSpan").innerHTML = " Calling: " + status_display_number + " UID: " + MDnextCID + " &nbsp; Waiting for Ring...";
                                                        document.getElementById("MainStatuSSpan").innerHTML = " Calling: " + status_display_number + " &nbsp; Waiting for Ring...";
							
                                                        document.getElementById("HangupControl").innerHTML = "<a href=\"#\" onclick=\"dialedcall_send_hangup();\"><img src=\"./images/x-endcall-on.png\" border=\"0\" alt=\"Hangup Customer\" /><span>End Call</span></a>";
							}
						if ( (LIVE_campaign_recording == 'ALLCALLS') || (LIVE_campaign_recording == 'ALLFORCE') )
							{all_record = 'YES';}

						if ( (view_scripts == 1) && (campaign_script.length > 0) )
							{
							var SCRIPT_web_form = 'http://127.0.0.1/testing.php';
							var TEMP_SCRIPT_web_form = URLDecode(SCRIPT_web_form,'YES','DEFAULT','1');

							if ( (script_recording_delay > 0) && ( (LIVE_campaign_recording == 'ALLCALLS') || (LIVE_campaign_recording == 'ALLFORCE') ) )
								{
								delayed_script_load = 'YES';
								RefresHScript('CLEAR');
								}
							else
								{
								load_script_contents();
								}
							}

						if (custom_fields_enabled > 0)
							{
							FormContentsLoad();
							}
						if (get_call_launch == 'SCRIPT')
							{
							if (delayed_script_load == 'YES')
								{
								load_script_contents();
								}
							ScriptPanelToFront();
							}
						if (get_call_launch == 'FORM')
							{
							FormPanelToFront();
							}
						if (get_call_launch == 'WEBFORM')
							{
							window.open(TEMP_VDIC_web_form_address, web_form_target, 'toolbar=1,scrollbars=1,location=1,statusbar=1,menubar=1,resizable=1,width=640,height=450');
							}
						if (get_call_launch == 'WEBFORMTWO')
							{
							window.open(TEMP_VDIC_web_form_address_two, web_form_target, 'toolbar=1,scrollbars=1,location=1,statusbar=1,menubar=1,resizable=1,width=640,height=450');
							}
						}
					}
				}
			delete xmlhttp;
			active_group_alias='';
			cid_choice='';
			prefix_choice='';
			agent_dialed_number='';
			agent_dialed_type='';
			CalL_ScripT_id='';
			}
		}


// ################################################################################
// Set the client to READY and start looking for calls (VDADready, VDADpause)
	function AutoDial_ReSume_PauSe(taskaction,taskagentlog,taskwrapup,taskstatuschange,temp_reason,temp_auto,temp_auto_code)
		{
		var add_pause_code='';
		if (taskaction == 'VDADready')
			{
			VDRP_stage = 'READY';
			if (INgroupCOUNT > 0)
				{
				if (VICIDiaL_closer_blended == 0)
					{VDRP_stage = 'CLOSER';}
				else 
					{VDRP_stage = 'READY';}
				}
			AutoDialReady = 1;
			AutoDialWaiting = 1;
			if (dial_method == "INBOUND_MAN")
				{
				auto_dial_level=starting_dial_level;

                                document.getElementById("DiaLControl").innerHTML = DiaLControl_auto_HTML_ready;
                                document.getElementById("ManualDiaLControl").innerHTML = DiaLControl_manual_HTML;
				}
                        else if (dial_method == "MANUAL")
				{
				document.getElementById("DiaLControl").innerHTML = DiaLControl_auto_HTML_ready;
                                document.getElementById("ManualDiaLControl").innerHTML = DiaLControl_manual_HTML;
				}
			else
				{
				document.getElementById("DiaLControl").innerHTML = DiaLControl_auto_HTML_ready;
                                document.getElementById("ManualDiaLControl").innerHTML = DiaLControl_manual_HTML_OFF;
				}
			}
		else
			{
			VDRP_stage = 'PAUSED';
			AutoDialReady = 0;
			AutoDialWaiting = 0;
			pause_code_counter = 0;
			if (dial_method == "INBOUND_MAN")
				{
				auto_dial_level=starting_dial_level;

                                document.getElementById("DiaLControl").innerHTML = DiaLControl_auto_HTML;
                                document.getElementById("ManualDiaLControl").innerHTML = DiaLControl_manual_HTML;
				}
                        else if (dial_method == "MANUAL")
				{
				document.getElementById("DiaLControl").innerHTML = DiaLControl_auto_HTML;
                                document.getElementById("ManualDiaLControl").innerHTML = DiaLControl_manual_HTML;
				}
			else
				{
				document.getElementById("DiaLControl").innerHTML = DiaLControl_auto_HTML;
                                document.getElementById("ManualDiaLControl").innerHTML = DiaLControl_manual_HTML_OFF;
				}

			if ( (agent_pause_codes_active=='FORCE') && (temp_reason != 'LOGOUT') && (temp_reason != 'REQUEUE') && (temp_reason != 'DIALNEXT') && (temp_auto != '1') )
				{
				PauseCodeSelectContent_create();
 				}
			if (temp_auto == '1')
				{
				add_pause_code = "&sub_status=" + temp_auto_code;
				}
			}

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
			autoDiaLready_query = "server_ip=" + server_ip + "&session_name=" + session_name + "&ACTION=" + taskaction + "&user=" + user + "&pass=" + pass + "&stage=" + VDRP_stage + "&agent_log_id=" + agent_log_id + "&agent_log=" + taskagentlog + "&wrapup=" + taskwrapup + "&campaign=" + campaign + "&dial_method" + dial_method + "&comments=" + taskstatuschange + add_pause_code;
			xmlhttp.open('POST', 'vdc_db_query.php'); 
			xmlhttp.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
			xmlhttp.send(autoDiaLready_query); 
			xmlhttp.onreadystatechange = function() 
				{ 
				if (xmlhttp.readyState == 4 && xmlhttp.status == 200) 
					{
					var check_dispo = null;
					check_dispo = xmlhttp.responseText;
					var check_DS_array=check_dispo.split("\n");
				//	alert(xmlhttp.responseText + "\n|" + check_DS_array[1] + "\n|" + check_DS_array[2] + "|");
					if (check_DS_array[1] == 'Next agent_log_id:')
						{agent_log_id = check_DS_array[2];}
					}
				}
			delete xmlhttp;
			}
		waiting_on_dispo=0;
		return agent_log_id;
		}



// ################################################################################
// Check to see if there is a call being sent from the auto-dialer to agent conf
	function ReChecKCustoMerChaN()
		{
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
			recheckVDAI_query = "server_ip=" + server_ip + "&session_name=" + session_name + "&user=" + user + "&pass=" + pass + "&campaign=" + campaign + "&ACTION=VDADREcheckINCOMING" + "&agent_log_id=" + agent_log_id + "&lead_id=" + document.vicidial_form.lead_id.value;
			xmlhttp.open('POST', 'vdc_db_query.php'); 
			xmlhttp.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
			xmlhttp.send(recheckVDAI_query); 
			xmlhttp.onreadystatechange = function() 
				{ 
				if (xmlhttp.readyState == 4 && xmlhttp.status == 200) 
					{
					var recheck_incoming = null;
					recheck_incoming = xmlhttp.responseText;
				//	alert(xmlhttp.responseText);
					var recheck_VDIC_array=recheck_incoming.split("\n");
					if (recheck_VDIC_array[0] == '1')
						{
						var reVDIC_data_VDAC=recheck_VDIC_array[1].split("|");
						if (reVDIC_data_VDAC[3] == lastcustchannel)
							{
						// do nothing
							}
						else
							{
				//	alert("Channel has changed from:\n" + lastcustchannel + '|' + lastcustserverip + "\nto:\n" + reVDIC_data_VDAC[3] + '|' + reVDIC_data_VDAC[4]);
							document.getElementById("callchannel").innerHTML	= reVDIC_data_VDAC[3];
							lastcustchannel = reVDIC_data_VDAC[3];
							document.vicidial_form.callserverip.value	= reVDIC_data_VDAC[4];
							lastcustserverip = reVDIC_data_VDAC[4];
							custchannellive = 1;
							}
						}
					}
				}
			delete xmlhttp;
			}
		}


// ################################################################################
// pull the script contents sending the webform variables to the script display script
	function load_script_contents()
		{
		var new_script_content = null;
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
			NeWscript_query = "server_ip=" + server_ip + "&session_name=" + session_name + "&user=" + user + "&pass=" + pass + "&ScrollDIV=1&" + web_form_vars;
			xmlhttp.open('POST', 'vdc_script_display.php'); 
			xmlhttp.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
			xmlhttp.send(NeWscript_query); 
			xmlhttp.onreadystatechange = function() 
				{ 
				if (xmlhttp.readyState == 4 && xmlhttp.status == 200) 
					{
					new_script_content = xmlhttp.responseText;
					document.getElementById("ScriptContents").innerHTML = new_script_content;
					}
				}
			delete xmlhttp;
			}
		}


// ################################################################################
// Alternate phone number change
	function alt_phone_change(APCphone,APCcount,APCleadID,APCactive)
		{

		var EAactive_link = '';
		if (APCactive == 'Y') 
			{EAactive_link = "<a href=\"#\" onclick=\"alt_phone_change('" + EAphone_number + "','" + EAalt_phone_count + "','" + document.vicidial_form.lead_id.value + "','N');\">Change this phone number to INACTIVE</a>";}
		else
			{EAactive_link = "<a href=\"#\" onclick=\"alt_phone_change('" + EAphone_number + "','" + EAalt_phone_count + "','" + document.vicidial_form.lead_id.value + "','Y');\">Change this phone number to ACTIVE</a>";}

        document.getElementById("EAcommentsBoxB").innerHTML = "<b>Active: </b>" + EAalt_phone_active + "<br />" + EAactive_link;

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
			APC_query = "server_ip=" + server_ip + "&session_name=" + session_name + "&user=" + user + "&pass=" + pass + "&campaign=" + campaign + "&ACTION=alt_phone_change" + "&phone_number=" + APCphone + "&lead_id=" + APCleadID + "&called_count=" + APCcount + "&stage=" + APCactive;
			xmlhttp.open('POST', 'vdc_db_query.php'); 
			xmlhttp.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
			xmlhttp.send(APC_query); 
			xmlhttp.onreadystatechange = function() 
				{ 
				if (xmlhttp.readyState == 4 && xmlhttp.status == 200) 
					{
			//		alert(xmlhttp.responseText);
					}
				}
			delete xmlhttp;
			}
		}


// ################################################################################
// Check to see if there is a call being sent from the auto-dialer to agent conf
	function check_for_auto_incoming()
		{
		if (typeof(xmlhttprequestcheckauto) == "undefined") 
			{
			all_record = 'NO';
			all_record_count=0;
		//	document.vicidial_form.lead_id.value = '';
			var xmlhttprequestcheckauto=false;
			/*@cc_on @*/
			/*@if (@_jscript_version >= 5)
			// JScript gives us Conditional compilation, we can cope with old IE versions.
			// and security blocked creation of the objects.
			 try {
			  xmlhttprequestcheckauto = new ActiveXObject("Msxml2.XMLHTTP");
			 } catch (e) {
			  try {
			   xmlhttprequestcheckauto = new ActiveXObject("Microsoft.XMLHTTP");
			  } catch (E) {
			   xmlhttprequestcheckauto = false;
			  }
			 }
			@end @*/
			if (!xmlhttprequestcheckauto && typeof XMLHttpRequest!='undefined')
				{
				xmlhttprequestcheckauto = new XMLHttpRequest();
				}
			if (xmlhttprequestcheckauto) 
				{ 
				checkVDAI_query = "server_ip=" + server_ip + "&session_name=" + session_name + "&user=" + user + "&pass=" + pass + "&campaign=" + campaign + "&ACTION=VDADcheckINCOMING" + "&agent_log_id=" + agent_log_id;
				xmlhttprequestcheckauto.open('POST', 'vdc_db_query.php'); 
				xmlhttprequestcheckauto.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
				xmlhttprequestcheckauto.send(checkVDAI_query); 
				xmlhttprequestcheckauto.onreadystatechange = function() 
					{ 
					if (xmlhttprequestcheckauto.readyState == 4 && xmlhttprequestcheckauto.status == 200) 
						{
						var check_incoming = null;
						check_incoming = xmlhttprequestcheckauto.responseText;
					//	alert(checkVDAI_query);
					//	alert(xmlhttprequestcheckauto.responseText);
						var check_VDIC_array=check_incoming.split("\n");
						if (check_VDIC_array[0] == '1')
							{
						//	alert(xmlhttprequestcheckauto.responseText);
							AutoDialWaiting = 0;

							var VDIC_data_VDAC=check_VDIC_array[1].split("|");
							VDIC_web_form_address = VICIDiaL_web_form_address
							VDIC_web_form_address_two = VICIDiaL_web_form_address_two
							var VDIC_fronter='';

							var VDIC_data_VDIG=check_VDIC_array[2].split("|");
							if (VDIC_data_VDIG[0].length > 5)
								{VDIC_web_form_address = VDIC_data_VDIG[0];}
							var VDCL_group_name			= VDIC_data_VDIG[1];
							var VDCL_group_color		= VDIC_data_VDIG[2];
							var VDCL_fronter_display	= VDIC_data_VDIG[3];
							 VDCL_group_id				= VDIC_data_VDIG[4];
							 CalL_ScripT_id				= VDIC_data_VDIG[5];
							 CalL_AutO_LauncH			= VDIC_data_VDIG[6];
							 CalL_XC_a_Dtmf				= VDIC_data_VDIG[7];
							 CalL_XC_a_NuMber			= VDIC_data_VDIG[8];
							 CalL_XC_b_Dtmf				= VDIC_data_VDIG[9];
							 CalL_XC_b_NuMber			= VDIC_data_VDIG[10];
							if ( (VDIC_data_VDIG[11].length > 1) && (VDIC_data_VDIG[11] != '---NONE---') )
								{LIVE_default_xfer_group = VDIC_data_VDIG[11];}
							else
								{LIVE_default_xfer_group = default_xfer_group;}

							if ( (VDIC_data_VDIG[12].length > 1) && (VDIC_data_VDIG[12]!='DISABLED') )
								{LIVE_campaign_recording = VDIC_data_VDIG[12];}
							else
								{LIVE_campaign_recording = campaign_recording;}

							if ( (VDIC_data_VDIG[13].length > 1) && (VDIC_data_VDIG[13]!='NONE') )
								{LIVE_campaign_rec_filename = VDIC_data_VDIG[13];}
							else
								{LIVE_campaign_rec_filename = campaign_rec_filename;}

							if ( (VDIC_data_VDIG[14].length > 1) && (VDIC_data_VDIG[14]!='NONE') )
								{LIVE_default_group_alias = VDIC_data_VDIG[14];}
							else
								{LIVE_default_group_alias = default_group_alias;}

							if ( (VDIC_data_VDIG[15].length > 1) && (VDIC_data_VDIG[15]!='NONE') )
								{LIVE_caller_id_number = VDIC_data_VDIG[15];}
							else
								{LIVE_caller_id_number = default_group_alias_cid;}

							if (VDIC_data_VDIG[16].length > 0)
								{LIVE_web_vars = VDIC_data_VDIG[16];}
							else
								{LIVE_web_vars = default_web_vars;}

							if (VDIC_data_VDIG[17].length > 5)
								{VDIC_web_form_address_two = VDIC_data_VDIG[17];}

							var call_timer_action							= VDIC_data_VDIG[18];

							if ( (call_timer_action == 'NONE') || (call_timer_action.length < 2) )
								{
								timer_action = campaign_timer_action;
								timer_action_message = campaign_timer_action_message;
								timer_action_seconds = campaign_timer_action_seconds;
								timer_action_destination = campaign_timer_action_destination;
								}
							else
								{
								var call_timer_action_message				= VDIC_data_VDIG[19];
								var call_timer_action_seconds				= VDIC_data_VDIG[20];
								var call_timer_action_destination			= VDIC_data_VDIG[27];
								timer_action = call_timer_action;
								timer_action_message = call_timer_action_message;
								timer_action_seconds = call_timer_action_seconds;
								timer_action_destination = call_timer_action_destination;
								}

							CalL_XC_c_NuMber			= VDIC_data_VDIG[21];
							CalL_XC_d_NuMber			= VDIC_data_VDIG[22];
							CalL_XC_e_NuMber			= VDIC_data_VDIG[23];
							CalL_XC_e_NuMber			= VDIC_data_VDIG[23];
							uniqueid_status_display		= VDIC_data_VDIG[24];
							uniqueid_status_prefix		= VDIC_data_VDIG[26];
							did_id						= VDIC_data_VDIG[28];
							did_extension				= VDIC_data_VDIG[29];
							did_pattern					= VDIC_data_VDIG[30];
							did_description				= VDIC_data_VDIG[31];
							closecallid					= VDIC_data_VDIG[32];
							xfercallid					= VDIC_data_VDIG[33];

							var VDIC_data_VDFR=check_VDIC_array[3].split("|");
							if ( (VDIC_data_VDFR[1].length > 1) && (VDCL_fronter_display == 'Y') )
								{
                                                                    VDIC_fronter = phone_number_format(VDIC_data_VDFR[1]);
                                                                    if (VDIC_data_VDFR[0].length > 1)
                                                                    {
                                                                        VDIC_fronter += " - " + VDIC_data_VDFR[0];
                                                                    }
                                                                }
							
							document.vicidial_form.lead_id.value		= VDIC_data_VDAC[0];
							document.vicidial_form.uniqueid.value		= VDIC_data_VDAC[1];
							CIDcheck									= VDIC_data_VDAC[2];
							CalLCID										= VDIC_data_VDAC[2];
							LastCallCID									= VDIC_data_VDAC[2];
							document.getElementById("callchannel").innerHTML	= VDIC_data_VDAC[3];
							lastcustchannel = VDIC_data_VDAC[3];
							document.vicidial_form.callserverip.value	= VDIC_data_VDAC[4];
							lastcustserverip = VDIC_data_VDAC[4];
							//if( document.images ) { document.images['livecall'].src = image_livecall_ON.src;}
                                                        document.getElementById('livecall').innerHTML = 'On a Call';
                                                        document.getElementById('livecall').className = 'on';
							document.vicidial_form.SecondS.value		= 0;
							document.getElementById("SecondSDISP").innerHTML = '0';

							if (uniqueid_status_display=='ENABLED')
								{custom_call_id			= " Call ID " + VDIC_data_VDAC[1];}
							if (uniqueid_status_display=='ENABLED_PREFIX')
								{custom_call_id			= " Call ID " + uniqueid_status_prefix + "" + VDIC_data_VDAC[1];}
							if (uniqueid_status_display=='ENABLED_PRESERVE')
								{custom_call_id			= " Call ID " + VDIC_data_VDIG[25];}

							VD_live_customer_call = 1;
							VD_live_call_secondS = 0;

							// INSERT VICIDIAL_LOG ENTRY FOR THIS CALL PROCESS
						//	DialLog("start");

							custchannellive=1;

							LasTCID											= check_VDIC_array[4];
							LeaDPreVDispO									= check_VDIC_array[6];
							fronter											= check_VDIC_array[7];
							document.vicidial_form.vendor_lead_code.value	= check_VDIC_array[8];
							document.vicidial_form.list_id.value			= check_VDIC_array[9];
							document.vicidial_form.gmt_offset_now.value		= check_VDIC_array[10];
							document.vicidial_form.phone_code.value			= check_VDIC_array[11];
							if ( (disable_alter_custphone=='Y') || (disable_alter_custphone=='HIDE') )
								{
								var tmp_pn = document.getElementById("phone_numberDISP");
								if (disable_alter_custphone=='Y')
									{
									tmp_pn.innerHTML						= check_VDIC_array[12];
									}
								}
							document.vicidial_form.phone_number.value		= check_VDIC_array[12];
							document.vicidial_form.title.value				= check_VDIC_array[13];
							document.vicidial_form.first_name.value			= check_VDIC_array[14];
							document.vicidial_form.middle_initial.value		= check_VDIC_array[15];
							document.vicidial_form.last_name.value			= check_VDIC_array[16];
							document.vicidial_form.address1.value			= check_VDIC_array[17];
							document.vicidial_form.address2.value			= check_VDIC_array[18];
							document.vicidial_form.address3.value			= check_VDIC_array[19];
							document.vicidial_form.city.value				= check_VDIC_array[20];
							document.vicidial_form.state.value				= check_VDIC_array[21];
							document.vicidial_form.province.value			= check_VDIC_array[22];
							document.vicidial_form.postal_code.value		= check_VDIC_array[23];
							document.vicidial_form.country_code.value		= check_VDIC_array[24];
							document.vicidial_form.gender.value				= check_VDIC_array[25];
							document.vicidial_form.date_of_birth.value		= check_VDIC_array[26];
							document.vicidial_form.alt_phone.value			= check_VDIC_array[27];
							document.vicidial_form.email.value				= check_VDIC_array[28];
							document.vicidial_form.security_phrase.value	= check_VDIC_array[29];
							var REGcommentsNL = new RegExp("!N","g");
							check_VDIC_array[30] = check_VDIC_array[30].replace(REGcommentsNL, "\n");
							document.vicidial_form.comments.value			= check_VDIC_array[30];
                                                        var quantityStr = " times";
                                                        if (check_VDIC_array[31]==1)
                                                            {quantityStr = " time";}
                                                            
                                                        if (VDIC_data_VDIG[1].length > 0) //inbound call
                                                            {document.getElementById('called_count_desc').innerHTML = "&nbsp;-&nbsp;Has called in " + check_VDIC_array[31] + quantityStr;}
                                                        else
                                                            {document.getElementById('called_count_desc').innerHTML = "&nbsp;-&nbsp;Has been called " + check_VDIC_array[31] + quantityStr;}
							document.vicidial_form.called_count.value		= check_VDIC_array[31];
							CBentry_time									= check_VDIC_array[32];
							CBcallback_time									= check_VDIC_array[33];
							CBuser											= check_VDIC_array[34];
							CBcomments										= check_VDIC_array[35];
							dialed_number									= check_VDIC_array[36];
							dialed_label									= check_VDIC_array[37];
							source_id										= check_VDIC_array[38];
							EAphone_code									= check_VDIC_array[39];
							EAphone_number									= check_VDIC_array[40];
							EAalt_phone_notes								= check_VDIC_array[41];
							EAalt_phone_active								= check_VDIC_array[42];
							EAalt_phone_count								= check_VDIC_array[43];
							document.vicidial_form.rank.value				= check_VDIC_array[44];
							document.vicidial_form.owner.value				= check_VDIC_array[45];
							script_recording_delay							= check_VDIC_array[46];
							document.vicidial_form.entry_list_id.value		= check_VDIC_array[47];
							custom_field_names								= check_VDIC_array[48];
							custom_field_values								= check_VDIC_array[49];
							custom_field_types								= check_VDIC_array[50];


							if (hide_gender > 0)
								{
								document.vicidial_form.gender_list.value	= check_VDIC_array[25];
								}
							else
								{
								var gIndex = 0;
								if (document.vicidial_form.gender.value == 'M') {var gIndex = 1;}
								if (document.vicidial_form.gender.value == 'F') {var gIndex = 2;}
								document.getElementById("gender_list").selectedIndex = gIndex;
								}

							lead_dial_number = document.vicidial_form.phone_number.value;
							var dispnum = document.vicidial_form.phone_number.value;
							var status_display_number = phone_number_format(dispnum);
							var callnum = dialed_number;
							var dial_display_number = phone_number_format(callnum);

							//document.getElementById("MainStatuSSpan").innerHTML = " <b>Call From:</b> " + dial_display_number + " " + custom_call_id + " <b>UID:</b> " + CIDcheck + " &nbsp; " + VDIC_fronter;
                                                        document.getElementById("MainStatuSSpan").innerHTML = " <b>Call From:</b> " + dial_display_number + " " + custom_call_id + " &nbsp; " + VDIC_fronter; 

							if (CBentry_time.length > 2)
								{
                                                                document.getElementById("CusTInfOSpaN").innerHTML = " <b> PREVIOUS CALLBACK </b>";
								document.getElementById("CusTInfOSpaN").style.background = CusTCB_bgcolor;
								document.getElementById("CBcommentsBoxA").innerHTML = "<b>Last Call: </b>" + CBentry_time;
								document.getElementById("CBcommentsBoxB").innerHTML = "<b>CallBack: </b>" + CBcallback_time;
								document.getElementById("CBcommentsBoxC").innerHTML = "<b>Agent: </b>" + CBuser;
                                                                document.getElementById("CBcommentsBoxD").innerHTML = "<b>Comments: </b><br />" + CBcomments;
								showDiv('CBcommentsBox');
								}
							if (dialed_label == 'ALT')
                                                            {document.getElementById("CusTInfOSpaN").innerHTML = " <b> ALT DIAL NUMBER: ALT </b>";}
							if (dialed_label == 'ADDR3')
                                                            {document.getElementById("CusTInfOSpaN").innerHTML = " <b> ALT DIAL NUMBER: ADDRESS3 </b>";}
							var REGalt_dial = new RegExp("X","g");
							if (dialed_label.match(REGalt_dial))
								{
                                                                document.getElementById("CusTInfOSpaN").innerHTML = " <b> ALT DIAL NUMBER: " + dialed_label + "</b>";
								document.getElementById("EAcommentsBoxA").innerHTML = "<b>Phone Code and Number: </b>" + EAphone_code + " " + EAphone_number;

								var EAactive_link = '';
								if (EAalt_phone_active == 'Y') 
									{EAactive_link = "<a href=\"#\" onclick=\"alt_phone_change('" + EAphone_number + "','" + EAalt_phone_count + "','" + document.vicidial_form.lead_id.value + "','N');\">Change this phone number to INACTIVE</a>";}
								else
									{EAactive_link = "<a href=\"#\" onclick=\"alt_phone_change('" + EAphone_number + "','" + EAalt_phone_count + "','" + document.vicidial_form.lead_id.value + "','Y');\">Change this phone number to ACTIVE</a>";}

                                                                document.getElementById("EAcommentsBoxB").innerHTML = "<b>Active: </b>" + EAalt_phone_active + "<br />" + EAactive_link;
								document.getElementById("EAcommentsBoxC").innerHTML = "<b>Alt Count: </b>" + EAalt_phone_count;
								document.getElementById("EAcommentsBoxD").innerHTML = "<b>Notes: </b>" + EAalt_phone_notes;
								showDiv('EAcommentsBox');
								}

							if (VDIC_data_VDIG[1].length > 0)
								{
								inOUT = 'IN';
								//if (VDIC_data_VDIG[2].length > 2)
								//	{
								//	document.getElementById("MainStatuSSpan").style.background = VDIC_data_VDIG[2];
								//	}
								var dispnum = document.vicidial_form.phone_number.value;
								var status_display_number = phone_number_format(dispnum);
								var callnum = dialed_number;
								var dial_display_number = phone_number_format(callnum);

								document.getElementById("MainStatuSSpan").innerHTML = " <b>Call From:</b> " + dial_display_number + " " + custom_call_id + " <b>Queue:</b> " + VDIC_data_VDIG[1] + " &nbsp; " + VDIC_fronter; 
								}

                                                        document.getElementById("ParkControl").innerHTML ="<a href=\"#\" onclick=\"mainxfer_send_redirect('ParK','" + lastcustchannel + "','" + lastcustserverip + "');return false;\"><img src=\"./images/x-hold-on.png\" border=\"0\" alt=\"Park Call\" /><span>Place on Hold</span></a>";
							if ( (ivr_park_call=='ENABLED') || (ivr_park_call=='ENABLED_PARK_ONLY') )
								{
                                                                document.getElementById("ivrParkControl").innerHTML ="<a href=\"#\" onclick=\"mainxfer_send_redirect('ParKivr','" + lastcustchannel + "','" + lastcustserverip + "');return false;\"><img src=\"./images/x-holdivr.png\" border=\"0\" alt=\"IVR Park Call\" /><span>IVR Hold</span></a>";
								}

                                                        document.getElementById("HangupControl").innerHTML = "<a href=\"#\" onclick=\"dialedcall_send_hangup();\"><img src=\"./images/x-endcall-on.png\" border=\"0\" alt=\"Hangup Customer\" /><span>End Call</span></a>";
                            
                                                        document.getElementById("XferControl").innerHTML = "<a href=\"#\" onclick=\"ShoWTransferMain('ON');\"><img src=\"./images/x-transfer-on.png\" border=\"0\" alt=\"Transfer - Conference\" /><span>X-fer or Conf.</span></a>";
                                                        
                                                        document.getElementById("LocalCloser").innerHTML = "<a href=\"#\" onclick=\"mainxfer_send_redirect('XfeRLOCAL','" + lastcustchannel + "','" + lastcustserverip + "');return false;\">X-fer to Queue</a>";
                                                        document.getElementById("LocalCloser").className = "button blue";
                            
                                                        document.getElementById("DialBlindTransfer").innerHTML = "<a href=\"#\" onclick=\"mainxfer_send_redirect('XfeRBLIND','" + lastcustchannel + "','" + lastcustserverip + "');return false;\">Blind X-fer</a>";
                                                        document.getElementById("DialBlindTransfer").className = "button blue";
                                                        
                                                        document.getElementById("DialBlindVMail").innerHTML = "<a href=\"#\" onclick=\"mainxfer_send_redirect('XfeRVMAIL','" + lastcustchannel + "','" + lastcustserverip + "');return false;\"><img src=\"./images/vdc_XB_ammessage.gif\" border=\"0\" alt=\"Blind Transfer VMail Message\" style=\"vertical-align:middle\" /></a>";
                                            
							if ( (quick_transfer_button == 'IN_GROUP') || (quick_transfer_button == 'LOCKED_IN_GROUP') )
								{
								if (quick_transfer_button_locked > 0)
									{quick_transfer_button_orig = default_xfer_group;}

                                                                document.getElementById("QuickXfer").innerHTML = "<a href=\"#\" onclick=\"mainxfer_send_redirect('XfeRLOCAL','" + lastcustchannel + "','" + lastcustserverip + "','','','" + quick_transfer_button_locked + "',1);return false;\"><img src=\"./images/x-transfer-quick.png\" border=\"0\" alt=\"QUICK TRANSFER\" /><br /><span>Quick X-fer</span></a>";
								}
							if (prepopulate_transfer_preset_enabled > 0)
								{
								if ( (prepopulate_transfer_preset == 'PRESET_1') || (prepopulate_transfer_preset == 'LOCKED_PRESET_1') )
									{document.vicidial_form.xfernumber.value = CalL_XC_a_NuMber;   document.vicidial_form.xfername.value='D1';}
								if ( (prepopulate_transfer_preset == 'PRESET_2') || (prepopulate_transfer_preset == 'LOCKED_PRESET_2') )
									{document.vicidial_form.xfernumber.value = CalL_XC_b_NuMber;   document.vicidial_form.xfername.value='D2';}
								if ( (prepopulate_transfer_preset == 'PRESET_3') || (prepopulate_transfer_preset == 'LOCKED_PRESET_3') )
									{document.vicidial_form.xfernumber.value = CalL_XC_c_NuMber;   document.vicidial_form.xfername.value='D3';}
								if ( (prepopulate_transfer_preset == 'PRESET_4') || (prepopulate_transfer_preset == 'LOCKED_PRESET_4') )
									{document.vicidial_form.xfernumber.value = CalL_XC_d_NuMber;   document.vicidial_form.xfername.value='D4';}
								if ( (prepopulate_transfer_preset == 'PRESET_5') || (prepopulate_transfer_preset == 'LOCKED_PRESET_5') )
									{document.vicidial_form.xfernumber.value = CalL_XC_e_NuMber;   document.vicidial_form.xfername.value='D5';}
								}
							if ( (quick_transfer_button == 'PRESET_1') || (quick_transfer_button == 'PRESET_2') || (quick_transfer_button == 'PRESET_3') || (quick_transfer_button == 'PRESET_4') || (quick_transfer_button == 'PRESET_5') ||
                                                                (quick_transfer_button == 'LOCKED_PRESET_1') || (quick_transfer_button == 'LOCKED_PRESET_2') || (quick_transfer_button == 'LOCKED_PRESET_3') || (quick_transfer_button == 'LOCKED_PRESET_4') || (quick_transfer_button == 'LOCKED_PRESET_5') ||
                                                                (end_call_action == 'PRESET_1') || (end_call_action == 'PRESET_2') || (end_call_action == 'PRESET_3') || (end_call_action == 'PRESET_4') || (end_call_action == 'PRESET_5'))
								{
                                                                    
                                                                if (quick_transfer_button_enabled == 0 && end_call_action_enabled == 1)
                                                                    {quick_transfer_button = end_call_action;}
                                                                            
								if ( (quick_transfer_button == 'PRESET_1') || (quick_transfer_button == 'LOCKED_PRESET_1') )
									{document.vicidial_form.xfernumber.value = CalL_XC_a_NuMber;   document.vicidial_form.xfername.value='D1';}
								if ( (quick_transfer_button == 'PRESET_2') || (quick_transfer_button == 'LOCKED_PRESET_2') )
									{document.vicidial_form.xfernumber.value = CalL_XC_b_NuMber;   document.vicidial_form.xfername.value='D2';}
								if ( (quick_transfer_button == 'PRESET_3') || (quick_transfer_button == 'LOCKED_PRESET_3') )
									{document.vicidial_form.xfernumber.value = CalL_XC_c_NuMber;   document.vicidial_form.xfername.value='D3';}
								if ( (quick_transfer_button == 'PRESET_4') || (quick_transfer_button == 'LOCKED_PRESET_4') )
									{document.vicidial_form.xfernumber.value = CalL_XC_d_NuMber;   document.vicidial_form.xfername.value='D4';}
								if ( (quick_transfer_button == 'PRESET_5') || (quick_transfer_button == 'LOCKED_PRESET_5') )
									{document.vicidial_form.xfernumber.value = CalL_XC_e_NuMber;   document.vicidial_form.xfername.value='D5';}
								if (quick_transfer_button_locked > 0)
									{quick_transfer_button_orig = document.vicidial_form.xfernumber.value;}

                                                                if (quick_transfer_button_enabled > 0)
                                                                    {
                                                                        document.getElementById("QuickXfer").innerHTML = "<a href=\"#\" onclick=\"mainxfer_send_redirect('XfeRBLIND','" + lastcustchannel + "','" + lastcustserverip + "','','','" + quick_transfer_button_locked + "',1);return false;\"><img src=\"./images/x-transfer-quick.png\" border=\"0\" alt=\"QUICK TRANSFER\" /><br /><span>Quick X-fer</span></a>";
                                                                    }
                                                                    
                                                                if (end_call_action_enabled > 0 && document.vicidial_form.xfernumber.value.length > 0)
                                                                    {
                                                                        document.getElementById("HangupControl").innerHTML = "<a href=\"#\" onclick=\"mainxfer_send_redirect('XfeRBLIND','" + lastcustchannel + "','" + lastcustserverip + "','','','" + quick_transfer_button_locked + "',1);return false;\"><img src=\"./images/x-endcall-on.png\" border=\"0\" alt=\"Hangup Customer\" /><span>End Call</span></a>";
                                                                    }
								}

							if (custom_3way_button_transfer_enabled > 0)
								{
								document.getElementById("CustomXfer").innerHTML = "<a href=\"#\" onclick=\"custom_button_transfer();return false;\"><img src=\"./images/x-transfer-custom.png\" border=\"0\" alt=\"Custom Transfer\" /><br /><span>Custom X-fer</span></a>";
								}


							if (call_requeue_button > 0)
								{
								var CloserSelectChoices = document.vicidial_form.CloserSelectList.value;
								var regCRB = new RegExp("AGENTDIRECT","ig");
								if ( (CloserSelectChoices.match(regCRB)) || (VU_closer_campaigns.match(regCRB)) )
									{
                                                                        document.getElementById("ReQueueCall").innerHTML =  "<a href=\"#\" onclick=\"call_requeue_launch();return false;\"><img src=\"./images/x-requeue-call.png\" border=\"0\" alt=\"Re-Queue Call\" /><br /><span>Re-Queue Call</span></a>";
									}
								else
									{
                                                                        document.getElementById("ReQueueCall").innerHTML =  "<a href=\"#\" style=\"opacity:.15;\" disabled><img class=\"disabled_img\" src=\"./images/x-requeue-call.png\" border=\"0\" alt=\"Re-Queue Call\" /><br /><span>Re-Queue Call</span></a>";
									}
								}

							// Build transfer pull-down list
							var loop_ct = 0;
							var live_XfeR_HTML = '';
							var XfeR_SelecT = '';
							while (loop_ct < XFgroupCOUNT)
								{
								if (VARxfergroups[loop_ct] == LIVE_default_xfer_group)
									{XfeR_SelecT = 'selected ';}
								else {XfeR_SelecT = '';}
								live_XfeR_HTML = live_XfeR_HTML + "<option " + XfeR_SelecT + "value=\"" + VARxfergroups[loop_ct] + "\">" + VARxfergroups[loop_ct] + " - " + VARxfergroupsnames[loop_ct] + "</option>\n";
								loop_ct++;
								}
                                                        document.getElementById("XfeRGrouPLisT").innerHTML = "<select size=\"1\" name=\"XfeRGrouP\" class=\"cust_form\" id=\"XfeRGrouP\" onChange=\"XferAgentSelectLink();return false;\">" + live_XfeR_HTML + "</select>";

							if (lastcustserverip == server_ip)
								{
                                                                document.getElementById("VolumeUpSpan").innerHTML = "<a href=\"#\" onclick=\"volume_control('UP','" + lastcustchannel + "','');return false;\"><img style=\"margin-bottom:10px;height:35px;\" src=\"./images/x-volume-up.png\" border=\"0\" /></a>";
                                                                document.getElementById("VolumeDownSpan").innerHTML = "<a href=\"#\" onclick=\"volume_control('DOWN','" + lastcustchannel + "','');return false;\"><img style=\"margin-bottom:10px;height:35px;\" src=\"./images/x-volume-down.png\" border=\"0\" /></a>";
								}

							if (dial_method == "INBOUND_MAN")
								{
                                                                document.getElementById("DiaLControl").innerHTML = DiaLControl_auto_HTML_OFF;
                                                                document.getElementById("ManualDiaLControl").innerHTML = DiaLControl_manual_HTML_OFF;
								}
							else
								{
								document.getElementById("DiaLControl").innerHTML = DiaLControl_auto_HTML_OFF;
                                                                document.getElementById("ManualDiaLControl").innerHTML = DiaLControl_manual_HTML_OFF;
								}

							if (VDCL_group_id.length > 1)
								{var group = VDCL_group_id;}
							else
								{var group = campaign;}
							if ( (dialed_label.length < 2) || (dialed_label=='NONE') ) {dialed_label='MAIN';}

							if (hide_gender < 1)
								{
								var genderIndex = document.getElementById("gender_list").selectedIndex;
								var genderValue =  document.getElementById('gender_list').options[genderIndex].value;
								document.vicidial_form.gender.value = genderValue;
								}

							LeaDDispO='';

							var regWFAcustom = new RegExp("^VAR","ig");
							if (VDIC_web_form_address.match(regWFAcustom))
								{
								TEMP_VDIC_web_form_address = URLDecode(VDIC_web_form_address,'YES','CUSTOM');
								TEMP_VDIC_web_form_address = TEMP_VDIC_web_form_address.replace(regWFAcustom, '');
								}
							else
								{
								TEMP_VDIC_web_form_address = URLDecode(VDIC_web_form_address,'YES','DEFAULT','1');
								}

							if (VDIC_web_form_address_two.match(regWFAcustom))
								{
								TEMP_VDIC_web_form_address_two = URLDecode(VDIC_web_form_address_two,'YES','CUSTOM');
								TEMP_VDIC_web_form_address_two = TEMP_VDIC_web_form_address_two.replace(regWFAcustom, '');
								}
							else
								{
								TEMP_VDIC_web_form_address_two = URLDecode(VDIC_web_form_address_two,'YES','DEFAULT','2');
								}


                                                        document.getElementById("WebFormSpan").innerHTML = "<a href=\"" + TEMP_VDIC_web_form_address + "\" target=\"" + web_form_target + "\" onMouseOver=\"WebFormRefresH();\"><img src=\"./images/x-webform.png\" border=\"0\" alt=\"Web Form\" /><br /><span id=\"x-webform\">Launch</span></a>\n";

							if (enable_second_webform > 0)
								{
                                                                document.getElementById("WebFormSpanTwo").innerHTML = "<a href=\"" + TEMP_VDIC_web_form_address_two + "\" target=\"" + web_form_target + "\" onMouseOver=\"WebFormTwoRefresH();\"><img src=\"./images/x-webform.png\" border=\"0\" alt=\"Web Form 2\" /><br /><span id=\"x-webform\">Launch 2nd</span></a>\n";
								}

							if ( (LIVE_campaign_recording == 'ALLCALLS') || (LIVE_campaign_recording == 'ALLFORCE') )
								{all_record = 'YES';}

							if ( (view_scripts == 1) && (CalL_ScripT_id.length > 0) )
								{
								var SCRIPT_web_form = 'http://127.0.0.1/testing.php';
								var TEMP_SCRIPT_web_form = URLDecode(SCRIPT_web_form,'YES','DEFAULT','1');

								if ( (script_recording_delay > 0) && ( (LIVE_campaign_recording == 'ALLCALLS') || (LIVE_campaign_recording == 'ALLFORCE') ) )
									{
									delayed_script_load = 'YES';
									RefresHScript('CLEAR');
									}
								else
									{
									load_script_contents();
									}
								}

							if (custom_fields_enabled > 0)
								{
								FormContentsLoad();
								}
							if (CalL_AutO_LauncH == 'SCRIPT')
								{
								if (delayed_script_load == 'YES')
									{
									load_script_contents();
									}
								ScriptPanelToFront();
								}
							if (CalL_AutO_LauncH == 'FORM')
								{
								FormPanelToFront();
								}

							if (CalL_AutO_LauncH == 'WEBFORM')
								{
								window.open(TEMP_VDIC_web_form_address, web_form_target, 'toolbar=1,scrollbars=1,location=1,statusbar=1,menubar=1,resizable=1,width=640,height=450');
								}
							if (CalL_AutO_LauncH == 'WEBFORMTWO')
								{
								window.open(TEMP_VDIC_web_form_address_two, web_form_target, 'toolbar=1,scrollbars=1,location=1,statusbar=1,menubar=1,resizable=1,width=640,height=450');
								}

							if (useIE > 0)
								{
								var regCTC = new RegExp("^NONE","ig");
								if (CopY_tO_ClipboarD.match(regCTC))
									{var nothing=1;}
								else
									{
									var tmp_clip = document.getElementById(CopY_tO_ClipboarD);
							//		alert_box("Copy to clipboard SETTING: |" + useIE + "|" + CopY_tO_ClipboarD + "|" + tmp_clip.value + "|");
									window.clipboardData.setData('Text', tmp_clip.value)
							//		alert_box("Copy to clipboard: |" + tmp_clip.value + "|" + CopY_tO_ClipboarD + "|");
									}
								}

							if (alert_enabled=='ON')
								{
								var callnum = dialed_number;
								var dial_display_number = phone_number_format(callnum);
								alert(" <b>Call From:</b> " + dial_display_number + "\n <b>Queue:</b> " + VDIC_data_VDIG[1] + " &nbsp; " + VDIC_fronter);
								}
							}
						else
							{
							// do nothing
							}
							xmlhttprequestcheckauto = undefined;
							delete xmlhttprequestcheckauto;
						}
					}
				}
			}
		}


// ################################################################################
// refresh or clear the SCRIPT frame contents
	function RefresHScript(temp_wipe)
		{
		if (temp_wipe == 'CLEAR')
			{
			document.getElementById("ScriptContents").innerHTML = '';
			}
		else
			{
			document.getElementById("ScriptContents").innerHTML = '';
			WebFormRefresH('','','1');
			load_script_contents();
			}
		}


// ################################################################################
// refresh the content of the web form URL
	function WebFormRefresH(taskrefresh,submittask,force_webvars_refresh) 
		{
		var webvars_refresh=0;

		if (VDCL_group_id.length > 1)
			{var group = VDCL_group_id;}
		else
			{var group = campaign;}
		if ( (dialed_label.length < 2) || (dialed_label=='NONE') ) {dialed_label='MAIN';}

		if (submittask != 'YES')
			{
			if (hide_gender < 1)
				{
				var genderIndex = document.getElementById("gender_list").selectedIndex;
				var genderValue =  document.getElementById('gender_list').options[genderIndex].value;
				document.vicidial_form.gender.value = genderValue;
				}
			}

		var regWFAcustom = new RegExp("^VAR","ig");
		if (VDIC_web_form_address.match(regWFAcustom))
			{
			TEMP_VDIC_web_form_address = URLDecode(VDIC_web_form_address,'YES','CUSTOM');
			TEMP_VDIC_web_form_address = TEMP_VDIC_web_form_address.replace(regWFAcustom, '');
			}
		else
			{webvars_refresh=1;}

		if ( (webvars_refresh > 0) || (force_webvars_refresh > 0) )
			{
			TEMP_VDIC_web_form_address = URLDecode(VDIC_web_form_address,'YES','DEFAULT','1');
			}

		if (taskrefresh == 'OUT')
			{
            document.getElementById("WebFormSpan").innerHTML = "<a href=\"" + TEMP_VDIC_web_form_address + "\" target=\"" + web_form_target + "\" onMouseOver=\"WebFormRefresH('IN');\"><img src=\"./images/x-webform.png\" border=\"0\" alt=\"Web Form\" /><br /><span id=\"x-webform\">Launch</span></a>\n";
			}
		else 
			{
            document.getElementById("WebFormSpan").innerHTML = "<a href=\"" + TEMP_VDIC_web_form_address + "\" target=\"" + web_form_target + "\" onMouseOut=\"WebFormRefresH('OUT');\"><img src=\"./images/x-webform.png\" border=\"0\" alt=\"Web Form\" /><br /><span id=\"x-webform\">Launch</span></a>\n";
			}
		}


// ################################################################################
// refresh the content of the second web form URL
	function WebFormTwoRefresH(taskrefresh,submittask) 
		{
		if (VDCL_group_id.length > 1)
			{var group = VDCL_group_id;}
		else
			{var group = campaign;}
		if ( (dialed_label.length < 2) || (dialed_label=='NONE') ) {dialed_label='MAIN';}

		if (submittask != 'YES')
			{
			if (hide_gender < 1)
				{
				var genderIndex = document.getElementById("gender_list").selectedIndex;
				var genderValue =  document.getElementById('gender_list').options[genderIndex].value;
				document.vicidial_form.gender.value = genderValue;
				}
			}

		var regWFAcustom = new RegExp("^VAR","ig");
		if (VDIC_web_form_address_two.match(regWFAcustom))
			{
			TEMP_VDIC_web_form_address_two = URLDecode(VDIC_web_form_address_two,'YES','CUSTOM');
			TEMP_VDIC_web_form_address_two = TEMP_VDIC_web_form_address_two.replace(regWFAcustom, '');
			}
		else
			{
			TEMP_VDIC_web_form_address_two = URLDecode(VDIC_web_form_address_two,'YES','DEFAULT','2');
			}

		if (enable_second_webform > 0)
			{
			if (taskrefresh == 'OUT')
				{
                document.getElementById("WebFormSpanTwo").innerHTML = "<a href=\"" + TEMP_VDIC_web_form_address_two + "\" target=\"" + web_form_target + "\" onMouseOver=\"WebFormTwoRefresH('IN');\"><img src=\"./images/x-webform.png\" border=\"0\" alt=\"Web Form 2\" /><br /><span id=\"x-webform\">Launch 2nd</span></a>\n";
				}
			else 
				{
                document.getElementById("WebFormSpanTwo").innerHTML = "<a href=\"" + TEMP_VDIC_web_form_address_two + "\" target=\"" + web_form_target + "\" onMouseOut=\"WebFormTwoRefresH('OUT');\"><img src=\"./images/x-webform.png\" border=\"0\" alt=\"Web Form 2\" /><br /><span id=\"x-webform\">Launch 2nd</span></a>\n";
				}
			}
		}


// ################################################################################
// Send hangup a second time from the dispo screen 
	function DispoHanguPAgaiN() 
	{
	form_cust_channel = AgaiNHanguPChanneL;
	document.getElementById("callchannel").innerHTML = AgaiNHanguPChanneL;
	document.vicidial_form.callserverip.value = AgaiNHanguPServeR;
	lastcustchannel = AgaiNHanguPChanneL;
	lastcustserverip = AgaiNHanguPServeR;
	VD_live_call_secondS = AgainCalLSecondS;
	CalLCID = AgaiNCalLCID;

	document.getElementById("DispoSelectHAspan").innerHTML = "";

	dialedcall_send_hangup();
	}


// ################################################################################
// Send leave 3way call a second time from the dispo screen 
	function DispoLeavE3wayAgaiN() 
	{
	XDchannel = DispO3wayXtrAchannel;
	document.vicidial_form.xfernumber.value = DispO3wayCalLxfernumber;
	MDchannel = DispO3waychannel;
	lastcustserverip = DispO3wayCalLserverip;

	document.getElementById("DispoSelectHAspan").innerHTML = "";

	leave_3way_call('SECOND');

	DispO3waychannel = '';
	DispO3wayXtrAchannel = '';
	DispO3wayCalLserverip = '';
	DispO3wayCalLxfernumber = '';
	DispO3wayCalLcamptail = '';
	}

// ################################################################################
// Disable controls
        function DisableControls()
        {
            document.getElementById("WebFormSpan").innerHTML = "<a href=\"#\" style=\"opacity:.15;\" disabled><img class=\"disabled_img\" src=\"./images/x-webform.png\" border=\"0\" alt=\"Web Form\" /><br /><span id=\"x-webform\">Launch</span></a>";
            if (enable_second_webform > 0)
                    {
                    document.getElementById("WebFormSpanTwo").innerHTML = "<a href=\"#\" style=\"opacity:.15;\" disabled><img class=\"disabled_img\" src=\"./images/x-webform.png\" border=\"0\" alt=\"Web Form 2\" /><br /><span id=\"x-webform\">Launch 2nd</span></a>";
                    }
            document.getElementById("ParkControl").innerHTML = "<a href=\"#\" style=\"opacity:.15;\" disabled><img class=\"disabled_img\" src=\"./images/x-hold-on.png\" border=\"0\" alt=\"Park Call\" /><span>Place on Hold</span></a>";
            document.getElementById("ParkControl2").innerHTML = "Park 3rd Party";
            document.getElementById("ParkControl2").className = "button blueinactive";
                        
            if ( (ivr_park_call=='ENABLED') || (ivr_park_call=='ENABLED_PARK_ONLY') )
                    {
                    document.getElementById("ivrParkControl").innerHTML = "<a href=\"#\" style=\"opacity:.15;\" disabled><img class=\"disabled_img\" src=\"./images/x-holdivr.png\" border=\"0\" alt=\"IVR Park Call\" /><span>IVR Hold</span></a>";
                    }
            document.getElementById("XferControl").innerHTML = "<a href=\"#\" style=\"opacity:.15;\" disabled><img class=\"disabled_img\" src=\"./images/x-transfer-on.png\" border=\"0\" alt=\"Transfer - Conference\" /><span>X-fer or Conf.</span></a>";
            
            document.getElementById("LocalCloser").innerHTML = "X-fer to Queue";
            document.getElementById("LocalCloser").className = "button blueinactive";
            
            document.getElementById("DialBlindTransfer").innerHTML = "Blind X-fer";
            document.getElementById("DialBlindTransfer").className = "button blueinactive";
            
            document.getElementById("DialBlindVMail").innerHTML = "<img src=\"./images/vdc_XB_ammessage_OFF.gif\" border=\"0\" alt=\"Blind Transfer VMail Message\" style=\"vertical-align:middle\" />";
            document.getElementById("VolumeUpSpan").innerHTML = "<img class=\"disabled_img\" style=\"opacity:.15;margin-bottom:10px;height:35px;\" src=\"./images/x-volume-up.png\" border=\"0\" />";
            document.getElementById("VolumeDownSpan").innerHTML = "<img class=\"disabled_img\" style=\"opacity:.15;margin-bottom:10px;height:35px;\" src=\"./images/x-volume-down.png\" border=\"0\" />";
            if (quick_transfer_button_enabled > 0)
                {document.getElementById("QuickXfer").innerHTML = "<a href=\"#\" style=\"opacity:.15;\" disabled><img class=\"disabled_img\" src=\"./images/x-transfer-quick.png\" border=\"0\" alt=\"QUICK TRANSFER\" /><br /><span>Quick X-fer</span></a>";}

	    if (custom_3way_button_transfer_enabled > 0)
                {document.getElementById("CustomXfer").innerHTML = "<a href=\"#\" style=\"opacity:.15;\" disabled><img class=\"disabled_img\" src=\"./images/x-transfer-custom.png\" border=\"0\" alt=\"Custom Transfer\" /><br /><span>Custom X-fer</span></a>";}

            if (call_requeue_button > 0)
                    {
                    document.getElementById("ReQueueCall").innerHTML =  "<a href=\"#\" style=\"opacity:.15;\" disabled><img class=\"disabled_img\" src=\"./images/x-requeue-call.png\" border=\"0\" alt=\"Re-Queue Call\" /><br /><span>Re-Queue Call</span></a>";
                    }

        }
        
// ################################################################################
// Start Hangup Functions for both 
	function bothcall_send_hangup() 
		{
		if (lastcustchannel.length > 3)
			{dialedcall_send_hangup();}
		if (lastxferchannel.length > 3)
			{xfercall_send_hangup();}
		}

// ################################################################################
// Send Hangup command for customer call connected to the conference now to Manager
	function dialedcall_send_hangup(dispowindow,hotkeysused,altdispo,nodeletevdac) 
		{
                                
		if (VDCL_group_id.length > 1)
			{var group = VDCL_group_id;}
		else
			{var group = campaign;}
		var form_cust_channel = document.getElementById("callchannel").innerHTML;
		var form_cust_serverip = document.vicidial_form.callserverip.value;
		var customer_channel = lastcustchannel;
		var customer_server_ip = lastcustserverip;
		AgaiNHanguPChanneL = lastcustchannel;
		AgaiNHanguPServeR = lastcustserverip;
		AgainCalLSecondS = VD_live_call_secondS;
		AgaiNCalLCID = CalLCID;
		var process_post_hangup=0;
		if ( (RedirecTxFEr < 1) && ( (MD_channel_look==1) || (auto_dial_level == 0) ) )
			{
			MD_channel_look=0;
			DialTimeHangup('MAIN');
			}
		if (form_cust_channel.length > 3)
			{
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
				var queryCID = "HLvdcW" + epoch_sec + user_abb;
				var hangupvalue = customer_channel;
				//		alert(auto_dial_level + "|" + CalLCID + "|" + customer_server_ip + "|" + hangupvalue + "|" + VD_live_call_secondS);
				custhangup_query = "server_ip=" + server_ip + "&session_name=" + session_name + "&ACTION=Hangup&format=text&user=" + user + "&pass=" + pass + "&channel=" + hangupvalue + "&call_server_ip=" + customer_server_ip + "&queryCID=" + queryCID + "&auto_dial_level=" + auto_dial_level + "&CalLCID=" + CalLCID + "&secondS=" + VD_live_call_secondS + "&exten=" + session_id + "&campaign=" + group + "&stage=CALLHANGUP&nodeletevdac=" + nodeletevdac + "&log_campaign=" + campaign;
				xmlhttp.open('POST', 'manager_send.php'); 
				xmlhttp.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
				xmlhttp.send(custhangup_query); 
				xmlhttp.onreadystatechange = function() 
					{ 
					if (xmlhttp.readyState == 4 && xmlhttp.status == 200) 
						{
						Nactiveext = null;
						Nactiveext = xmlhttp.responseText;

					//		alert(xmlhttp.responseText);
					//	var HU_debug = xmlhttp.responseText;
					//	var HU_debug_array=HU_debug.split(" ");
					//	if (HU_debug_array[0] == 'Call')
					//		{
					//		alert(xmlhttp.responseText);
					//		}

						}
					}
				process_post_hangup=1;
				delete xmlhttp;
				}
			}
			else {process_post_hangup=1;}
			if (process_post_hangup==1)
			{
			VD_live_customer_call = 0;
			VD_live_call_secondS = 0;
			MD_ring_secondS = 0;
			CalLCID = '';
			MDnextCID = '';

		//	UPDATE VICIDIAL_LOG ENTRY FOR THIS CALL PROCESS
			DialLog("end",nodeletevdac);
			conf_dialed=0;
			if (dispowindow == 'NO')
				{
				open_dispo_screen=0;
				}
			else
				{
				if (auto_dial_level == 0)			
					{
					if (document.vicidial_form.DiaLAltPhonE.checked==true)
						{
						reselect_alt_dial = 1;
						open_dispo_screen=0;
						}
					else
						{
						reselect_alt_dial = 0;
						open_dispo_screen=1;
						}
					}
				else
					{
					if (document.vicidial_form.DiaLAltPhonE.checked==true)
						{
						reselect_alt_dial = 1;
						open_dispo_screen=0;
						auto_dial_level=0;
						manual_dial_in_progress=1;
						auto_dial_alt_dial=1;
						}
					else
						{
						reselect_alt_dial = 0;
						open_dispo_screen=1;
						}
					}
				}

		//  DEACTIVATE CHANNEL-DEPENDANT BUTTONS AND VARIABLES
			document.getElementById("callchannel").innerHTML = '';
			document.vicidial_form.callserverip.value = '';
			lastcustchannel='';
			lastcustserverip='';
			MDchannel='';
			if (post_phone_time_diff_alert_message.length > 10)
				{
				document.getElementById("post_phone_time_diff_span_contents").innerHTML = "";
				hideDiv('post_phone_time_diff_span');
				post_phone_time_diff_alert_message='';
				}

			//if( document.images ) { document.images['livecall'].src = image_livecall_OFF.src;}
                        document.getElementById('livecall').innerHTML = 'Not on Call';
                        document.getElementById('livecall').className = 'off';
            
                        DisableControls();
                        
                        document.getElementById("HangupControl").innerHTML = "<a href=\"#\" style=\"opacity:.15;\" disabled><img class=\"disabled_img\" src=\"./images/x-endcall-on.png\" border=\"0\" alt=\"Hangup Customer\" /><span>End Call</span></a>";
            
			document.getElementById("custdatetime").innerHTML = ' &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; ';
                        document.getElementById("MainStatuSSpan").innerHTML = document.getElementById("MainStatuSSpanDefault").innerHTML

			if ( (auto_dial_level == 0) && (dial_method != 'INBOUND_MAN') )
				{
				if (document.vicidial_form.DiaLAltPhonE.checked==true)
					{
					reselect_alt_dial = 1;
					if (altdispo == 'ALTPH2')
						{
						ManualDialOnly('ALTPhonE');
						}
					else
						{
						if (altdispo == 'ADDR3')
							{
							ManualDialOnly('AddresS3');
							}
						else
							{
							if (hotkeysused == 'YES')
								{
								reselect_alt_dial = 0;
								manual_auto_hotkey = 1;
								}
							}
						}
					}
				else
					{
					if (hotkeysused == 'YES')
						{
						manual_auto_hotkey = 1;
						}
					else
						{
                                                document.getElementById("DiaLControl").innerHTML = DiaLControl_auto_HTML_OFF;
                                                document.getElementById("ManualDiaLControl").innerHTML = DiaLControl_manual_HTML;
						}
					reselect_alt_dial = 0;
					}
				}
			else
				{
				if (document.vicidial_form.DiaLAltPhonE.checked==true)
					{
					reselect_alt_dial = 1;
					if (altdispo == 'ALTPH2')
						{
						ManualDialOnly('ALTPhonE');
						}
					else
						{
						if (altdispo == 'ADDR3')
							{
							ManualDialOnly('AddresS3');
							}
						else
							{
							if (hotkeysused == 'YES')
								{
								manual_auto_hotkey = 1;
								alt_dial_active=0;

								//document.getElementById("MainStatuSSpan").style.background = panel_bgcolor;
								//document.getElementById("MainStatuSSpan").innerHTML = document.getElementById("MainStatuSSpanDefault").innerHTML;
								if (dial_method == "INBOUND_MAN")
									{
                                                                        document.getElementById("DiaLControl").innerHTML = DiaLControl_auto_HTML_OFF;
                                                                        document.getElementById("ManualDiaLControl").innerHTML = DiaLControl_manual_HTML_OFF;
									}
								else
									{
									document.getElementById("DiaLControl").innerHTML = DiaLControl_auto_HTML_OFF;
                                                                        document.getElementById("ManualDiaLControl").innerHTML = DiaLControl_manual_HTML_OFF;
									}
								reselect_alt_dial = 0;
								}
							}
						}
					}
				else
					{
					//document.getElementById("MainStatuSSpan").style.background = panel_bgcolor;
					if (dial_method == "INBOUND_MAN")
						{
                                                document.getElementById("DiaLControl").innerHTML = DiaLControl_auto_HTML_OFF;
                                                document.getElementById("ManualDiaLControl").innerHTML = DiaLControl_manual_HTML_OFF;
						}
					else
						{
						document.getElementById("DiaLControl").innerHTML = DiaLControl_auto_HTML_OFF;
                                                document.getElementById("ManualDiaLControl").innerHTML = DiaLControl_manual_HTML_OFF;
						}
					reselect_alt_dial = 0;
					}
				}

			ShoWTransferMain('OFF');

			}

		}


// ################################################################################
// Send Hangup command for 3rd party call connected to the conference now to Manager
	function xfercall_send_hangup() 
		{
		var xferchannel = document.vicidial_form.xferchannel.value;
		var xfer_channel = lastxferchannel;
		var process_post_hangup=0;
		xfer_in_call=0;
		if ( (MD_channel_look==1) && (leaving_threeway < 1) )
			{
			MD_channel_look=0;
			DialTimeHangup('XFER');
			}
		if (xferchannel.length > 3)
			{
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
				var queryCID = "HXvdcW" + epoch_sec + user_abb;
				var hangupvalue = xfer_channel;
				custhangup_query = "server_ip=" + server_ip + "&session_name=" + session_name + "&ACTION=Hangup&format=text&user=" + user + "&pass=" + pass + "&channel=" + hangupvalue + "&queryCID=" + queryCID + "&log_campaign=" + campaign;
				xmlhttp.open('POST', 'manager_send.php'); 
				xmlhttp.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
				xmlhttp.send(custhangup_query); 
				xmlhttp.onreadystatechange = function() 
					{ 
					if (xmlhttp.readyState == 4 && xmlhttp.status == 200) 
						{
						Nactiveext = null;
						Nactiveext = xmlhttp.responseText;
				//		alert(xmlhttp.responseText);
						}
					}
				process_post_hangup=1;
				delete xmlhttp;
				}
			}
		else {process_post_hangup=1;}
		if (process_post_hangup==1)
			{
			XD_live_customer_call = 0;
			XD_live_call_secondS = 0;
			MD_ring_secondS = 0;
			MD_channel_look=0;
			XDnextCID = '';
			XDcheck = '';
			xferchannellive=0;

		//  DEACTIVATE CHANNEL-DEPENDANT BUTTONS AND VARIABLES
			document.vicidial_form.xferchannel.value = "";
			lastxferchannel='';

            document.getElementById("Leave3WayCall").innerHTML ="Leave 3-Way";
            document.getElementById("Leave3WayCall").className = "button blueinactive";

			document.getElementById("ParkControl2").innerHTML = "Park 3rd Party";
                        document.getElementById("ParkControl2").className = "button blueinactive";
				
            document.getElementById("DialWithCustomer").innerHTML ="<a href=\"#\" onclick=\"SendManualDial('YES');return false;\">Warm X-fer</a>";
            document.getElementById("DialWithCustomer").className = "button blue";

            document.getElementById("ParkCustomerDial").innerHTML ="<a href=\"#\" onclick=\"xfer_park_dial();return false;\">Park &amp; Dial</a>";
            document.getElementById("ParkCustomerDial").className = "button blue";

            document.getElementById("HangupXferLine").innerHTML ="Hangup 3rd Party";
            document.getElementById("HangupXferLine").className = "button blueinactive";
            showDivDisplay("x-transfer-options");
            hideDivDisplay("x-transfer-active");

            document.getElementById("HangupBothLines").innerHTML ="<a href=\"#\" onclick=\"bothcall_send_hangup();return false;\">Hangup Both</a>";
            document.getElementById("HangupBothLines").className = "button blue";
			}
		}

// ################################################################################
// Send Hangup command for any Local call that is not in the quiet(7) entry - used to stop manual dials even if no connect
	function DialTimeHangup(tasktypecall) 
		{
		if ( (RedirecTxFEr < 1) && (leaving_threeway < 1) )
			{
	//	alert("RedirecTxFEr|" + RedirecTxFEr);
		MD_channel_look=0;
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
			var queryCID = "HTvdcW" + epoch_sec + user_abb;
			custhangup_query = "server_ip=" + server_ip + "&session_name=" + session_name + "&ACTION=HangupConfDial&format=text&user=" + user + "&pass=" + pass + "&exten=" + session_id + "&ext_context=" + ext_context + "&queryCID=" + queryCID + "&log_campaign=" + campaign;
			xmlhttp.open('POST', 'manager_send.php'); 
			xmlhttp.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
			xmlhttp.send(custhangup_query); 
			xmlhttp.onreadystatechange = function() 
				{
				if (xmlhttp.readyState == 4 && xmlhttp.status == 200) 
					{
					Nactiveext = null;
					Nactiveext = xmlhttp.responseText;
				//	alert(xmlhttp.responseText + "\n" + tasktypecall + "\n" + leaving_threeway);
 					}
				}
			delete xmlhttp;
			}
			}
		}


// ################################################################################
// Update vicidial_list lead record with all altered values from form
	function CustomerData_update()
		{

		var REGcommentsAMP = new RegExp('&',"g");
		var REGcommentsQUES = new RegExp("\\?","g");
		var REGcommentsPOUND = new RegExp("\\#","g");
		var REGcommentsRESULT = document.vicidial_form.comments.value.replace(REGcommentsAMP, "--AMP--");
		REGcommentsRESULT = REGcommentsRESULT.replace(REGcommentsQUES, "--QUES--");
		REGcommentsRESULT = REGcommentsRESULT.replace(REGcommentsPOUND, "--POUND--");

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
			if (hide_gender < 1)
				{
				var genderIndex = document.getElementById("gender_list").selectedIndex;
				var genderValue =  document.getElementById('gender_list').options[genderIndex].value;
				document.vicidial_form.gender.value = genderValue;
				}

			VLupdate_query = "server_ip=" + server_ip + "&session_name=" + session_name + "&campaign=" + campaign +  "&ACTION=updateLEAD&format=text&user=" + user + "&pass=" + pass + 
			"&lead_id=" + document.vicidial_form.lead_id.value + 
			"&vendor_lead_code=" + document.vicidial_form.vendor_lead_code.value + 
			"&phone_number=" + document.vicidial_form.phone_number.value + 
			"&title=" + document.vicidial_form.title.value + 
			"&first_name=" + document.vicidial_form.first_name.value + 
			"&middle_initial=" + document.vicidial_form.middle_initial.value + 
			"&last_name=" + document.vicidial_form.last_name.value + 
			"&address1=" + document.vicidial_form.address1.value + 
			"&address2=" + document.vicidial_form.address2.value + 
			"&address3=" + document.vicidial_form.address3.value + 
			"&city=" + document.vicidial_form.city.value + 
			"&state=" + document.vicidial_form.state.value + 
			"&province=" + document.vicidial_form.province.value + 
			"&postal_code=" + document.vicidial_form.postal_code.value + 
			"&country_code=" + document.vicidial_form.country_code.value + 
			"&gender=" + document.vicidial_form.gender.value + 
			"&date_of_birth=" + document.vicidial_form.date_of_birth.value + 
			"&alt_phone=" + document.vicidial_form.alt_phone.value + 
			"&email=" + document.vicidial_form.email.value + 
			"&security_phrase=" + document.vicidial_form.security_phrase.value + 
			"&comments=" + REGcommentsRESULT;
			xmlhttp.open('POST', 'vdc_db_query.php'); 
			xmlhttp.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
			xmlhttp.send(VLupdate_query); 
			xmlhttp.onreadystatechange = function() 
				{ 
				if (xmlhttp.readyState == 4 && xmlhttp.status == 200) 
					{
				//	alert(xmlhttp.responseText);
					}
				}
			delete xmlhttp;
			}

		}

// ################################################################################
// Generate the Call Disposition Chooser panel
	function DispoSelectContent_create(taskDSgrp,taskDSstage)
		{
		if (customer_3way_hangup_dispo_message.length > 1)
			{
            document.getElementById("Dispo3wayMessage").innerHTML = "<br /><b><font color=\"red\" size=\"3\">" + customer_3way_hangup_dispo_message + "</font></b><br />";
			}
		if (APIManualDialQueue > 0)
			{
            document.getElementById("DispoManualQueueMessage").innerHTML = "<br /><b><font color=\"red\" size=\"3\">Manual Dial Queue Calls Waiting: " + APIManualDialQueue + "</font></b><br />";
			}
		if (per_call_notes == 'ENABLED')
			{
			var test_notes = document.vicidial_form.call_notes_dispo.value;
			if (test_notes.length > 0)
				{document.vicidial_form.call_notes.value = document.vicidial_form.call_notes_dispo.value}
            document.getElementById("PerCallNotesContent").innerHTML = "<br /><b><font size=\"3\">Call Notes: </font></b><br /><textarea name=\"call_notes_dispo\" id=\"call_notes_dispo\" rows=\"4\" cols=\"85\" class=\"cust_form_text\" value=\"\">" + document.vicidial_form.call_notes.value + "</textarea>";
			}
		else
			{
            document.getElementById("PerCallNotesContent").innerHTML = "<input type=\"hidden\" name=\"call_notes_dispo\" id=\"call_notes_dispo\" value=\"\" />";
			}

		HidEGenDerPulldown();
		AgentDispoing = 1;
		var CBflag = '';
                
                var dispo_HTML = "<table cellpadding=\"5\" cellspacing=\"5\" width=\"620px\"><tr><td colspan=\"2\"><h3> Dispositions</h3></td></tr><tr><td bgcolor=\"#eaeaea\" height=\"450px\" width=\"320px\" valign=\"top\"><font class=\"log_text\"><span id=\"DispoSelectA\">";
                var loop_ct = 0;
                var print_ct = 0;
                var VD_statuses_ct_half = 0;
                    
                // added support for dispo's per in-group
                if (VDCL_group_id.length > 1)
                    {
                        // filter statuses by the ingroup (each status description 
                        // must be prefixed with the ingroup id in order for this to work)
                        var tempVARstatuses = [];
                        var tempVARstatusnames = [];
                        var tempVARSELstatuses = [];
                        var tempVARCBstatuses = [];
                        var tempVD_statuses_ct = 0;
                        var tempVARSELstatuses_ct = 0;
                        
                        for (var i in VARstatusnames) {
                            //if (VARstatuscategories[i].toLowerCase() == VDCL_group_id.toLowerCase() || VARstatuscategories[i].toLowerCase() == "system") { //found a match or is a system dispo
                            if (VARstatuscategories[i].toLowerCase() == VDCL_group_id.toLowerCase() ) {
                                tempVARSELstatuses[tempVARSELstatuses.length] = VARSELstatuses[i];
                                tempVARstatuses[tempVARstatuses.length] = VARstatuses[i];
                                tempVARstatusnames[tempVARstatusnames.length] = VARstatusnames[i];
                                tempVARCBstatuses[tempVARCBstatuses.length] = VARCBstatuses[i];
                                
                                if (VARSELstatuses[i] == 'Y') // track count of selectable dispos
                                    tempVARSELstatuses_ct++;
                                
                                tempVD_statuses_ct++;
                            }
                        }
                        
                        // chop to account for two rows when laying out selectable dispos
                        VD_statuses_ct_half = parseInt(tempVARSELstatuses_ct / 2);
                        
                        while (loop_ct < tempVD_statuses_ct)
                            {
                                if (tempVARSELstatuses[loop_ct] == 'Y')
                                    {
                                    if (tempVARCBstatuses[loop_ct] == 'Y')
                                            {CBflag = '*';}
                                    else
                                            {CBflag = '';}
                                    if (taskDSgrp == tempVARstatuses[loop_ct]) 
                                            {
                                            dispo_HTML = dispo_HTML + "<font style=\"BACKGROUND-COLOR: #FFFFFF\"><b><a href=\"#\" onclick=\"DispoSelect_submit();return false;\">" + tempVARstatusnames[loop_ct].replace(VDCL_group_id,"") + " (" + tempVARstatuses[loop_ct] + ")</a> " + CBflag + "</b></font><br /><br />";
                                            }
                                    else
                                            {
                                            dispo_HTML = dispo_HTML + "<a href=\"#\" onclick=\"DispoSelectContent_create('" + tempVARstatuses[loop_ct] + "','ADD');return false;\">" + tempVARstatusnames[loop_ct].replace(VDCL_group_id,"") + " (" + tempVARstatuses[loop_ct] + ")</a> " + CBflag + "<br /><br />";
                                            }
                                    if (print_ct == VD_statuses_ct_half) 
                                            {dispo_HTML = dispo_HTML + "</span></font></td><td bgcolor=\"#eaeaea\" height=\"450px\" width=\"320px\" valign=\"top\"><font class=\"log_text\"><span id=\"DispoSelectB\">";}
                                    print_ct++;
                                    }
                                loop_ct++;
                            }
                    }
		else // this isn't an inbound call, use original vici code
                    {
                
                        VD_statuses_ct_half = parseInt(VARSELstatuses_ct / 2);
                    
                        //make sure we have undefined status categories, which denotes outbound statuses
                        //if none are present, simply use the list we have available
                        var outbound_status_ct = 0;
                        var can_add_status = 0;
                        while (loop_ct < VD_statuses_ct)
                            {
                                if (VARstatuscategories[loop_ct].toLowerCase() == "undefined")
                                {
                                    outbound_status_ct++;
                                }
                                loop_ct++;
                            }
                            
                        loop_ct = 0;
                        while (loop_ct < VD_statuses_ct)
                            {
                                if ( outbound_status_ct > 0 )
                                {
                                    if (VARstatuscategories[loop_ct].toLowerCase() == "undefined" ||
                                        VARstatuscategories[loop_ct].toLowerCase() == "system")
                                    {
                                        can_add_status = 1;
                                    }
                                    else
                                    {
                                        can_add_status = 0;
                                    }
                                }
                                else
                                {
                                    can_add_status = 1;
                                }
                                
                                if (can_add_status == 1)
                                {
                                    if (VARSELstatuses[loop_ct] == 'Y')
                                        {
                                        if (VARCBstatuses[loop_ct] == 'Y')
                                                {CBflag = '*';}
                                        else
                                                {CBflag = '';}
                                        if (taskDSgrp == VARstatuses[loop_ct]) 
                                                {
                                                dispo_HTML = dispo_HTML + "<font style=\"BACKGROUND-COLOR: #FFFFFF\"><b><a href=\"#\" onclick=\"DispoSelect_submit();return false;\">" + VARstatusnames[loop_ct] + " (" + VARstatuses[loop_ct] + ")</a> " + CBflag + "</b></font><br /><br />";
                                                }
                                        else
                                                {
                                                dispo_HTML = dispo_HTML + "<a href=\"#\" onclick=\"DispoSelectContent_create('" + VARstatuses[loop_ct] + "','ADD');return false;\">" + VARstatusnames[loop_ct] + " (" + VARstatuses[loop_ct] + ")</a> " + CBflag + "<br /><br />";
                                                }
                                        if (print_ct == VD_statuses_ct_half) 
                                                {dispo_HTML = dispo_HTML + "</span></font></td><td bgcolor=\"#eaeaea\" height=\"450px\" width=\"320px\" valign=\"top\"><font class=\"log_text\"><span id=\"DispoSelectB\">";}
                                        print_ct++;
                                        }
                                }
                            loop_ct++;
                            }
                    }
                    
                dispo_HTML = dispo_HTML + "</span></font></td></tr></table>";

		if (taskDSstage == 'ReSET') {document.vicidial_form.DispoSelection.value = '';}
		else {document.vicidial_form.DispoSelection.value = taskDSgrp;}
		
		document.getElementById("DispoSelectContent").innerHTML = dispo_HTML;
		if (focus_blur_enabled==1)
			{
			document.inert_form.inert_button.focus();
			document.inert_form.inert_button.blur();
			}
		if (my_callback_option == 'CHECKED')
			{document.vicidial_form.CallBackOnlyMe.checked=true;}
		}


// ################################################################################
// Generate the Pause Code Chooser panel
	function PauseCodeSelectContent_create()
		{
		var move_on=1;
		if ( (AutoDialWaiting == 1) || (VD_live_customer_call==1) || (alt_dial_active==1) || (MD_channel_look==1) || (in_lead_preview_state==1) )
			{
			if ((auto_pause_precall == 'Y') && ( (agent_pause_codes_active=='Y') || (agent_pause_codes_active=='FORCE') ) && (AutoDialWaiting == 1) && (VD_live_customer_call!=1) && (alt_dial_active!=1) && (MD_channel_look!=1) && (in_lead_preview_state!=1) )
				{
				agent_log_id = AutoDial_ReSume_PauSe("VDADpause",'','','','','1','');
				}
			else
				{
				move_on=0;
				alert_box("YOU MUST BE PAUSED TO ENTER A PAUSE CODE IN AUTO-DIAL MODE");
				}
			}
		if (move_on == 1)
			{
			if (APIManualDialQueue > 0)
				{
				PauseCodeSelect_submit('NXDIAL');
				}
			else
				{
				HidEGenDerPulldown();
				showDiv('PauseCodeSelectBox');
				WaitingForNextStep=1;
				PauseCode_HTML = '';
				document.vicidial_form.PauseCodeSelection.value = '';		
				var VD_pause_codes_ct_half = parseInt(VD_pause_codes_ct / 2);
                PauseCode_HTML = "<table cellpadding=\"5\" cellspacing=\"5\" width=\"500px\"><tr><td colspan=\"2\"><b> PAUSE CODE</b></td></tr><tr><td bgcolor=\"#eaeaea\" height=\"300px\" width=\"240px\" valign=\"top\"><font class=\"log_text\"><span id=\"PauseCodeSelectA\">";
				var loop_ct = 0;
				while (loop_ct < VD_pause_codes_ct)
					{
                    PauseCode_HTML = PauseCode_HTML + "<b><a href=\"#\" onclick=\"PauseCodeSelect_submit('" + VARpause_codes[loop_ct] + "');return false;\">" + VARpause_codes[loop_ct] + " - " + VARpause_code_names[loop_ct] + "</a></b><br /><br />";
					loop_ct++;
					if (loop_ct == VD_pause_codes_ct_half) 
                        {PauseCode_HTML = PauseCode_HTML + "</span></font></td><td bgcolor=\"#eaeaea\" height=\"300px\" width=\"240px\" valign=\"top\"><font class=\"log_text\"><span id=PauseCodeSelectB>";}
					}

				if (agent_pause_codes_active=='FORCE')
					{var Go_BacK_LinK = '';}
				else
                    {var Go_BacK_LinK = "<font size=\"3\"><b><a href=\"#\" onclick=\"PauseCodeSelect_submit('');return false;\">Go Back</a>";}

                PauseCode_HTML = PauseCode_HTML + "</span></font></td></tr></table><br /><br />" + Go_BacK_LinK;
				document.getElementById("PauseCodeSelectContent").innerHTML = PauseCode_HTML;
				}
			}
		if (focus_blur_enabled==1)
			{
			document.inert_form.inert_button.focus();
			document.inert_form.inert_button.blur();
			}
		}

// ################################################################################
// Open lead search form panel
	function OpeNSearcHForMDisplaYBox()
		{
		var move_on=1;
		if ( (AutoDialWaiting == 1) || (VD_live_customer_call==1) || (alt_dial_active==1) || (MD_channel_look==1) || (in_lead_preview_state==1) )
			{
			if ((auto_pause_precall == 'Y') && ( (agent_pause_codes_active=='Y') || (agent_pause_codes_active=='FORCE') ) && (AutoDialWaiting == 1) && (VD_live_customer_call!=1) && (alt_dial_active!=1) && (MD_channel_look!=1) && (in_lead_preview_state!=1) )
				{
				agent_log_id = AutoDial_ReSume_PauSe("VDADpause",'','','','','1',auto_pause_precall_code);
				}
			else
				{
				move_on=0;
				alert_box("YOU MUST BE PAUSED TO SEARCH FOR A LEAD");
				}
			}
		if (move_on == 1)
			{
			HidEGenDerPulldown();
			showDiv('SearcHForMDisplaYBox');
			WaitingForNextStep=1;
			}
		}

// ################################################################################
// Generate the Presets Chooser span content
	function generate_presets_pulldown()
		{
		showDiv('PresetsSelectBox');
		Presets_HTML = '';
		document.vicidial_form.PresetSelection.value = '';		
        Presets_HTML = "<table cellpadding=\"5\" cellspacing=\"5\" width=\"400px\"><tr><td bgcolor=\"#CCCCFF\" height=<?php echo $HTheight ?> width=\"400px\" valign=\"bottom\"><font class=\"log_text\">";
		var loop_ct = 0;
		while (loop_ct < VD_preset_names_ct)
			{
            Presets_HTML = Presets_HTML + "<font size=\"3\" style=\"BACKGROUND-COLOR: #FFFFFF\"><b><a href=\"#\" onclick=\"PresetSelect_submit('" + VARpreset_names[loop_ct] + "','" + VARpreset_numbers[loop_ct] + "','" + VARpreset_dtmfs[loop_ct] + "','" + VARpreset_hide_numbers[loop_ct] + "');return false;\">" + VARpreset_names[loop_ct];
			if (VARpreset_hide_numbers[loop_ct]=='N')
				{Presets_HTML = Presets_HTML + " - " + VARpreset_numbers[loop_ct];}
            Presets_HTML = Presets_HTML + "</a></b></font><br />";
			loop_ct++;
			}

		if ( (CalL_XC_a_NuMber.length > 0) || (CalL_XC_a_Dtmf.length > 0) )
			{
            Presets_HTML = Presets_HTML + "<font size=\"3\" style=\"BACKGROUND-COLOR: #FFFFFF\"><b><a href=\"#\" onclick=\"PresetSelect_submit('D1','" + CalL_XC_a_NuMber + "','" + CalL_XC_a_Dtmf + "');return false;\">D1";
			if (hide_xfer_number_to_dial=='DISABLED')
				{Presets_HTML = Presets_HTML + " - " + CalL_XC_a_NuMber;}
            Presets_HTML = Presets_HTML + "</a></b></font><br />";
			}
		if ( (CalL_XC_b_NuMber.length > 0) || (CalL_XC_b_Dtmf.length > 0) )
			{
            Presets_HTML = Presets_HTML + "<font size=\"3\" style=\"BACKGROUND-COLOR: #FFFFFF\"><b><a href=\"#\" onclick=\"PresetSelect_submit('D2','" + CalL_XC_b_NuMber + "','" + CalL_XC_b_Dtmf + "');return false;\">D2";
			if (hide_xfer_number_to_dial=='DISABLED')
				{Presets_HTML = Presets_HTML + " - " + CalL_XC_b_NuMber;}
            Presets_HTML = Presets_HTML + "</a></b></font><br />";
			}
		if (CalL_XC_c_NuMber.length > 0)
			{
            Presets_HTML = Presets_HTML + "<font size=\"3\" style=\"BACKGROUND-COLOR: #FFFFFF\"><b><a href=\"#\" onclick=\"PresetSelect_submit('D3','" + CalL_XC_c_NuMber + "','');return false;\">D3";
			if (hide_xfer_number_to_dial=='DISABLED')
				{Presets_HTML = Presets_HTML + " - " + CalL_XC_c_NuMber;}
            Presets_HTML = Presets_HTML + "</a></b></font><br />";
			}
		if (CalL_XC_d_NuMber.length > 0)
			{
            Presets_HTML = Presets_HTML + "<font size=\"3\" style=\"BACKGROUND-COLOR: #FFFFFF\"><b><a href=\"#\" onclick=\"PresetSelect_submit('D4','" + CalL_XC_d_NuMber + "','');return false;\">D4";
			if (hide_xfer_number_to_dial=='DISABLED')
				{Presets_HTML = Presets_HTML + " - " + CalL_XC_d_NuMber;}
            Presets_HTML = Presets_HTML + "</a></b></font><br />";
			}
		if (CalL_XC_e_NuMber.length > 0)
			{
            Presets_HTML = Presets_HTML + "<font size=\"3\" style=\"BACKGROUND-COLOR: #FFFFFF\"><b><a href=\"#\" onclick=\"PresetSelect_submit('D5','" + CalL_XC_e_NuMber + "','');return false;\">D5";
			if (hide_xfer_number_to_dial=='DISABLED')
				{Presets_HTML = Presets_HTML + " - " + CalL_XC_e_NuMber;}
            Presets_HTML = Presets_HTML + "</a></b></font><br />";
			}

        Presets_HTML = Presets_HTML + "</td></tr></table><br /><br /><table cellpadding=\"0\" cellspacing=\"0\"><tr><td width=\"330px\" align=\"left\"><font size=\"3\" style=\"BACKGROUND-COLOR: #CCCCFF\"><b><a href=\"#\" onclick=\"hideDiv('PresetsSelectBox');return false;\">Close [X]</a></b></font></td></tr></table>";
		document.getElementById("PresetsSelectBoxContent").innerHTML = Presets_HTML;
		}


// ################################################################################
// Submit chosen Preset
	function PresetSelect_submit(taskpresetname,taskpresetnumber,taskpresetdtmf,taskhidenumber)
		{
		hideDiv('PresetsSelectBox');
		document.vicidial_form.conf_dtmf.value = taskpresetdtmf;
		document.vicidial_form.xfername.value = taskpresetname;
		if ( (taskhidenumber=='Y') && (hide_xfer_number_to_dial=='DISABLED') )
			{
			document.vicidial_form.xfernumhidden.value = taskpresetnumber;
			document.vicidial_form.xfernumber.value='';
			}
		else
			{
			document.vicidial_form.xfernumhidden.value = '';
			document.vicidial_form.xfernumber.value = taskpresetnumber;
			}
		scroll(0,0);
		}


// ################################################################################
// Generate the Group Alias Chooser panel
	function GroupAliasSelectContent_create(task3way)
		{
		HidEGenDerPulldown();
		showDiv('GroupAliasSelectBox');
		WaitingForNextStep=1;
		GroupAlias_HTML = '';
		document.vicidial_form.GroupAliasSelection.value = '';		
		var VD_group_aliases_ct_half = parseInt(VD_group_aliases_ct / 2);
        GroupAlias_HTML = "<table cellpadding=\"5\" cellspacing=\"5\" width=\"500px\"><tr><td colspan=\"2\"><b> GROUP ALIAS</b></td></tr><tr><td bgcolor=\"#eaeaea\" height=\"300px\" width=\"240px\" valign=\"top\"><font class=\"log_text\"><span id=\"GroupAliasSelectA\">";
		if (task3way > 0)
			{
			VD_group_aliases_ct_half = (VD_group_aliases_ct_half - 1);
            GroupAlias_HTML = GroupAlias_HTML + "<font size=\"2\"><b><a href=\"#\" onclick=\"GroupAliasSelect_submit('CAMPAIGN','" + campaign_cid + "','0');return false;\">CAMPAIGN - " + campaign_cid + "</a></b></font><br /><br />";
            GroupAlias_HTML = GroupAlias_HTML + "<font size=\"2\"><b><a href=\"#\" onclick=\"GroupAliasSelect_submit('CUSTOMER','" + document.vicidial_form.phone_number.value + "','0');return false;\">CUSTOMER - " + document.vicidial_form.phone_number.value + "</a></b></font><br /><br />";
            GroupAlias_HTML = GroupAlias_HTML + "<font size=\"2\"><b><a href=\"#\" onclick=\"GroupAliasSelect_submit('AGENT_PHONE','" + outbound_cid + "','0');return false;\">AGENT_PHONE - " + outbound_cid + "</a></b></font><br /><br />";
			}
		var loop_ct = 0;
		while (loop_ct < VD_group_aliases_ct)
			{
            GroupAlias_HTML = GroupAlias_HTML + "<font size=\"2\"><b><a href=\"#\" onclick=\"GroupAliasSelect_submit('" + VARgroup_alias_ids[loop_ct] + "','" + VARcaller_id_numbers[loop_ct] + "','1');return false;\">" + VARgroup_alias_ids[loop_ct] + " - " + VARgroup_alias_names[loop_ct] + " - " + VARcaller_id_numbers[loop_ct] + "</a></b></font><br /><br />";
			loop_ct++;
			if (loop_ct == VD_group_aliases_ct_half) 
                {GroupAlias_HTML = GroupAlias_HTML + "</span></font></td><td bgcolor=\"#eaeaea\" height=\"300px\" width=\"240px\" valign=\"top\"><font class=\"log_text\"><span id=GroupAliasSelectB>";}
			}

        var Go_BacK_LinK = "<font size=\"3\"><b><a href=\"#\" onclick=\"GroupAliasSelect_submit('');return false;\">Go Back</a>";

        GroupAlias_HTML = GroupAlias_HTML + "</span></font></td></tr></table><br /><br />" + Go_BacK_LinK;
		document.getElementById("GroupAliasSelectContent").innerHTML = GroupAlias_HTML;
		if (focus_blur_enabled==1)
			{
			document.inert_form.inert_button.focus();
			document.inert_form.inert_button.blur();
			}
		}

// ################################################################################
// open web form, then submit disposition
	function WeBForMDispoSelect_submit()
		{
		leaving_threeway=0;
		blind_transfer=0;
		document.getElementById("callchannel").innerHTML = '';
		document.vicidial_form.callserverip.value = '';
		document.vicidial_form.xferchannel.value = '';
        document.getElementById("DialWithCustomer").innerHTML ="<a href=\"#\" onclick=\"SendManualDial('YES');return false;\">Warm X-fer</a>";
        document.getElementById("DialWithCustomer").className = "button blue";

        document.getElementById("ParkCustomerDial").innerHTML ="<a href=\"#\" onclick=\"xfer_park_dial();return false;\">Park &amp; Dial</a>";
        document.getElementById("ParkCustomerDial").className = "button blue";
        
        document.getElementById("HangupBothLines").innerHTML ="<a href=\"#\" onclick=\"bothcall_send_hangup();return false;\">Hangup Both</a>";
        document.getElementById("HangupBothLines").className = "button blue";

		var DispoChoice = document.vicidial_form.DispoSelection.value;

		if (DispoChoice.length < 1) {alert_box("You Must Select a Disposition");}
		else
			{
			document.getElementById("CusTInfOSpaN").innerHTML = "";
			document.getElementById("CusTInfOSpaN").style.background = panel_bgcolor;

			LeaDDispO = DispoChoice;
	
			WebFormRefresH('NO','YES');

            document.getElementById("WebFormSpan").innerHTML = "<a href=\"#\" style=\"opacity:.15;\" disabled><img class=\"disabled_img\" src=\"./images/x-webform.png\" border=\"0\" alt=\"Web Form\" /><br /><span id=\"x-webform\">Launch</span></a>";
			if (enable_second_webform > 0)
				{
                document.getElementById("WebFormSpanTwo").innerHTML = "<a href=\"#\" style=\"opacity:.15;\" disabled><img class=\"disabled_img\" src=\"./images/x-webform.png\" border=\"0\" alt=\"Web Form 2\" /><br /><span id=\"x-webform\">Launch 2nd</span></a>";
				}
			window.open(TEMP_VDIC_web_form_address, web_form_target, 'toolbar=1,scrollbars=1,location=1,statusbar=1,menubar=1,resizable=1,width=640,height=450');

			DispoSelect_submit();
			}
		}


// ################################################################################
// Update vicidial_list lead record with disposition selection
	function DispoSelect_submit()
		{
		if (VDCL_group_id.length > 1)
			{var group = VDCL_group_id;}
		else
			{var group = campaign;}
		leaving_threeway=0;
		blind_transfer=0;
		CheckDEADcallON=0;
		customer_3way_hangup_counter=0;
		customer_3way_hangup_counter_trigger=0;
		waiting_on_dispo=1;
		document.getElementById("callchannel").innerHTML = '';
		document.vicidial_form.callserverip.value = '';
		document.vicidial_form.xferchannel.value = '';
        document.getElementById("DialWithCustomer").innerHTML ="<a href=\"#\" onclick=\"SendManualDial('YES');return false;\">Warm X-fer</a>";
        document.getElementById("DialWithCustomer").className = "button blue";

        document.getElementById("ParkCustomerDial").innerHTML ="<a href=\"#\" onclick=\"xfer_park_dial();return false;\">Park &amp; Dial</a>";
        document.getElementById("ParkCustomerDial").className = "button blue";
        
        document.getElementById("HangupBothLines").innerHTML ="<a href=\"#\" onclick=\"bothcall_send_hangup();return false;\">Hangup Both</a>";
        document.getElementById("HangupBothLines").className = "button blue";
 
		var DispoChoice = document.vicidial_form.DispoSelection.value;

		if (DispoChoice.length < 1) {alert_box("You Must Select a Disposition");}
		else
			{
			document.getElementById("CusTInfOSpaN").innerHTML = "";
			document.getElementById("CusTInfOSpaN").style.background = panel_bgcolor;
			var regCBstatus = new RegExp(' ' + DispoChoice + ' ',"ig");
			if ( (VARCBstatusesLIST.match(regCBstatus)) && (DispoChoice.length > 0) && (scheduled_callbacks > 0) && (DispoChoice != 'CBHOLD') ) {showDiv('CallBackSelectBox');}
			else
				{
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
					DSupdate_query = "server_ip=" + server_ip + "&session_name=" + session_name + "&ACTION=updateDISPO&format=text&user=" + user + "&pass=" + pass + "&dispo_choice=" + DispoChoice + "&lead_id=" + document.vicidial_form.lead_id.value + "&campaign=" + campaign + "&auto_dial_level=" + auto_dial_level + "&agent_log_id=" + agent_log_id + "&CallBackDatETimE=" + CallBackDatETimE + "&list_id=" + document.vicidial_form.list_id.value + "&recipient=" + CallBackrecipient + "&use_internal_dnc=" + use_internal_dnc + "&use_campaign_dnc=" + use_campaign_dnc + "&MDnextCID=" + LasTCID + "&stage=" + group + "&vtiger_callback_id=" + vtiger_callback_id + "&phone_number=" + document.vicidial_form.phone_number.value + "&phone_code=" + document.vicidial_form.phone_code.value + "&dial_method" + dial_method + "&uniqueid=" + document.vicidial_form.uniqueid.value + "&CallBackLeadStatus=" + CallBackLeadStatus + "&comments=" + CallBackCommenTs + "&custom_field_names=" + custom_field_names + "&call_notes=" + document.vicidial_form.call_notes_dispo.value;
					xmlhttp.open('POST', 'vdc_db_query.php');
					xmlhttp.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
					xmlhttp.send(DSupdate_query); 
					xmlhttp.onreadystatechange = function() 
						{ 
					//	alert(DSupdate_query + "\n" +xmlhttp.responseText);

						if (xmlhttp.readyState == 4 && xmlhttp.status == 200)
							{
					//		alert(xmlhttp.responseText);
							if (auto_dial_level < 1)
								{
								var check_dispo = null;
								check_dispo = xmlhttp.responseText;
								var check_DS_array=check_dispo.split("\n");
								if (check_DS_array[1] == 'Next agent_log_id:')
									{
									agent_log_id = check_DS_array[2];
									}
								}
							waiting_on_dispo=0;
							}
						}
					delete xmlhttp;
					}
				// CLEAR ALL FORM VARIABLES
				document.vicidial_form.lead_id.value		='';
				document.vicidial_form.vendor_lead_code.value='';
				document.vicidial_form.list_id.value		='';
				document.vicidial_form.entry_list_id.value	='';
				document.vicidial_form.gmt_offset_now.value	='';
				document.vicidial_form.phone_code.value		='';
				if ( (disable_alter_custphone=='Y') || (disable_alter_custphone=='HIDE') )
					{
					var tmp_pn = document.getElementById("phone_numberDISP");
					tmp_pn.innerHTML			= ' &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; ';
					}
				document.vicidial_form.phone_number.value	='';
				document.vicidial_form.title.value			='';
				document.vicidial_form.first_name.value		='';
				document.vicidial_form.middle_initial.value	='';
				document.vicidial_form.last_name.value		='';
				document.vicidial_form.address1.value		='';
				document.vicidial_form.address2.value		='';
				document.vicidial_form.address3.value		='';
				document.vicidial_form.city.value			='';
				document.vicidial_form.state.value			='';
				document.vicidial_form.province.value		='';
				document.vicidial_form.postal_code.value	='';
				document.vicidial_form.country_code.value	='';
				document.vicidial_form.gender.value			='';
				document.vicidial_form.date_of_birth.value	='';
				document.vicidial_form.alt_phone.value		='';
				document.vicidial_form.email.value			='';
				document.vicidial_form.security_phrase.value='';
				document.vicidial_form.comments.value		='';
				document.vicidial_form.called_count.value	='';
                                document.getElementById('called_count_desc').innerHTML = '';
				document.vicidial_form.call_notes.value		='';
				document.vicidial_form.call_notes_dispo.value ='';
				VDCL_group_id = '';
				fronter = '';
				inOUT = 'OUT';
				vtiger_callback_id='0';
				recording_filename='';
				recording_id='';
				document.vicidial_form.uniqueid.value='';
				MDuniqueid='';
				XDuniqueid='';
				tmp_vicidial_id='';
				EAphone_code='';
				EAphone_number='';
				EAalt_phone_notes='';
				EAalt_phone_active='';
				EAalt_phone_count='';
				XDnextCID='';
				XDcheck = '';
				MDnextCID='';
				XD_live_customer_call = 0;
				XD_live_call_secondS = 0;
				xfer_in_call=0;
				MD_channel_look=0;
				MD_ring_secondS=0;
				uniqueid_status_display='';
				uniqueid_status_prefix='';
				custom_call_id='';
				API_selected_xfergroup='';
				API_selected_callmenu='';
				timer_action='';
				timer_action_seconds='';
				timer_action_mesage='';
				timer_action_destination='';
				did_pattern='';
				did_id='';
				did_extension='';
				did_description='';
				closecallid='';
				xfercallid='';
				custom_field_names='';
				custom_field_values='';
				custom_field_types='';
				customerparked=0;
				customerparkedcounter=0;
				customerparkedxfer=0;
				customerparkedcounterxfer=0;
				document.getElementById("ParkCounterSpan").innerHTML = '';
				document.getElementById("ParkCounterSpan2").innerHTML = '';
				document.vicidial_form.xfername.value='';
				document.vicidial_form.xfernumhidden.value='';
				document.getElementById("debugbottomspan").innerHTML = '';
				customer_3way_hangup_dispo_message='';
				document.getElementById("Dispo3wayMessage").innerHTML = '';
				document.getElementById("DispoManualQueueMessage").innerHTML = '';
				document.getElementById("ManualQueueNotice").innerHTML = '';
				APIManualDialQueue_last=0;
				document.vicidial_form.FORM_LOADED.value = '0';
				CallBackLeadStatus = '';
				document.vicidial_form.search_phone_number.value='';
				document.vicidial_form.search_lead_id.value='';
				document.vicidial_form.search_vendor_lead_code.value='';
				document.vicidial_form.search_first_name.value='';
				document.vicidial_form.search_last_name.value='';
				document.vicidial_form.search_city.value='';
				document.vicidial_form.search_state.value='';
				document.vicidial_form.search_postal_code.value='';
				document.vicidial_form.MDPhonENumbeR.value = '';
				document.vicidial_form.MDDiaLOverridE.value = '';
				document.vicidial_form.MDLeadID.value = '';
				document.vicidial_form.MDType.value = '';

				if (post_phone_time_diff_alert_message.length > 10)
					{
					document.getElementById("post_phone_time_diff_span_contents").innerHTML = "";
					hideDiv('post_phone_time_diff_span');
					post_phone_time_diff_alert_message='';
					}

				if (manual_dial_in_progress==1)
					{
					manual_dial_finished();
					}
				if (hide_gender < 1)
					{
					document.getElementById("GENDERhideFORieALT").innerHTML = '';
					document.getElementById("GENDERhideFORie").innerHTML = '<select size="1" name="gender_list" class="cust_form" id="gender_list"><option value="U">U - Undefined</option><option value="M">M - Male</option><option value="F">F - Female</option></select>';
					}
				hideDiv('DispoSelectBox');
				hideDiv('DispoButtonHideA');
				hideDiv('DispoButtonHideB');
				hideDiv('DispoButtonHideC');
				document.getElementById("DispoSelectBox").style.top = '1px';  // Firefox error on this line for some reason
				document.getElementById("DispoSelectMaxMin").innerHTML = "<a href=\"#\" onclick=\"DispoMinimize()\"> minimize </a>";
				document.getElementById("DispoSelectHAspan").innerHTML = "<a href=\"#\" onclick=\"DispoHanguPAgaiN()\">Hangup Again</a>";

                                if (dial_method == "MANUAL")
                                {
                                document.getElementById("DiaLControl").innerHTML = DiaLControl_auto_HTML_ready;
                                document.getElementById("ManualDiaLControl").innerHTML = DiaLControl_manual_HTML;
                                }
                                
				CBcommentsBoxhide();
				EAcommentsBoxhide();

				AgentDispoing = 0;

				if (shift_logout_flag < 1)
					{
					if (wrapup_waiting == 0)
						{
						if (document.vicidial_form.DispoSelectStop.checked==true)
							{
							if (auto_dial_level != '0')
								{
								AutoDialWaiting = 0;
								AutoDial_ReSume_PauSe("VDADpause");
								}
							VICIDiaL_pause_calling = 1;
							if (dispo_check_all_pause != '1')
								{
								document.vicidial_form.DispoSelectStop.checked=false;
								}
							}
						else
							{
							if (auto_dial_level != '0' || dial_method == 'MANUAL')
								{
								AutoDialWaiting = 1;
								agent_log_id = AutoDial_ReSume_PauSe("VDADready","NEW_ID");
								}
							else
								{
								// trigger HotKeys manual dial automatically go to next lead
								if (manual_auto_hotkey == '1')
									{
									manual_auto_hotkey = 0;
									ManualDialNext('','','','','','0');
									}
								}
							}
						}
					}
				else
					{
					LogouT('SHIFT');
					}
				if (focus_blur_enabled==1)
					{
					document.inert_form.inert_button.focus();
					document.inert_form.inert_button.blur();
					}
				}
			// scroll back to the top of the page
			scroll(0,0);
			}
		}


// ################################################################################
// Submit the Pause Code 
	function PauseCodeSelect_submit(newpausecode)
		{
		hideDiv('PauseCodeSelectBox');
		ShoWGenDerPulldown();

		WaitingForNextStep=0;

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
			VMCpausecode_query = "server_ip=" + server_ip + "&session_name=" + session_name + "&user=" + user + "&pass=" + pass  + "&ACTION=PauseCodeSubmit&format=text&status=" + newpausecode + "&agent_log_id=" + agent_log_id + "&campaign=" + campaign + "&extension=" + extension + "&protocol=" + protocol + "&phone_ip=" + phone_ip + "&enable_sipsak_messages=" + enable_sipsak_messages + "&stage=" + pause_code_counter + "&campaign_cid=" + LastCallCID + "&auto_dial_level=" + starting_dial_level;
			pause_code_counter++;
			xmlhttp.open('POST', 'vdc_db_query.php'); 
			xmlhttp.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
			xmlhttp.send(VMCpausecode_query); 
			xmlhttp.onreadystatechange = function() 
				{ 
				if (xmlhttp.readyState == 4 && xmlhttp.status == 200) 
					{
					var check_pause_code = null;
					var check_pause_code = xmlhttp.responseText;
					var check_PC_array=check_pause_code.split("\n");
					if (check_PC_array[1] == 'Next agent_log_id:')
						{agent_log_id = check_PC_array[2];}
				//	alert(VMCpausecode_query);
				//	alert(xmlhttp.responseText + "\n|" + check_PC_array[1] + "\n|" + check_PC_array[2] + "|" + agent_log_id + "|" + pause_code_counter);
					}
				}
			delete xmlhttp;
			}
//		return agent_log_id;
		LastCallCID='';
		scroll(0,0);
		}


// ################################################################################
// Submit the Group Alias 
	function GroupAliasSelect_submit(newgroupalias,newgroupcid,newusegroup)
		{
		hideDiv('GroupAliasSelectBox');
		ShoWGenDerPulldown();
		WaitingForNextStep=0;
		
		if (newusegroup > 0)
			{
			active_group_alias = newgroupalias;
            document.getElementById("ManuaLDiaLGrouPSelecteD").innerHTML = "<font size=\"2\" face=\"Arial,Helvetica\">Group Alias: " + active_group_alias + "</font>";
            document.getElementById("XfeRDiaLGrouPSelecteD").innerHTML = "<font size=\"1\" face=\"Arial,Helvetica\">Group Alias: " + active_group_alias + "</font>";
			}
		cid_choice = newgroupcid;
		scroll(0,0);
		}


// ################################################################################
// Populate the dtmf and xfer number for each preset link in xfer-conf frame
	function DtMf_PreSet_a()
		{
		document.vicidial_form.conf_dtmf.value = CalL_XC_a_Dtmf;
		document.vicidial_form.xfernumber.value = CalL_XC_a_NuMber;
		document.vicidial_form.xfername.value = 'D1';
		}
	function DtMf_PreSet_b()
		{
		document.vicidial_form.conf_dtmf.value = CalL_XC_b_Dtmf;
		document.vicidial_form.xfernumber.value = CalL_XC_b_NuMber;
		document.vicidial_form.xfername.value = 'D2';
		}
	function DtMf_PreSet_c()
		{
		document.vicidial_form.xfernumber.value = CalL_XC_c_NuMber;
		document.vicidial_form.xfername.value = 'D3';
		}
	function DtMf_PreSet_d()
		{
		document.vicidial_form.xfernumber.value = CalL_XC_d_NuMber;
		document.vicidial_form.xfername.value = 'D4';
		}
	function DtMf_PreSet_e()
		{
		document.vicidial_form.xfernumber.value = CalL_XC_e_NuMber;
		document.vicidial_form.xfername.value = 'D5';
		}

	function DtMf_PreSet_a_DiaL()
		{
		document.vicidial_form.conf_dtmf.value = CalL_XC_a_Dtmf;
		document.vicidial_form.xfernumber.value = CalL_XC_a_NuMber;
		basic_originate_call(CalL_XC_a_NuMber,'NO','YES',session_id,'YES','','1','0');
		}
	function DtMf_PreSet_b_DiaL()
		{
		document.vicidial_form.conf_dtmf.value = CalL_XC_b_Dtmf;
		document.vicidial_form.xfernumber.value = CalL_XC_b_NuMber;
		basic_originate_call(CalL_XC_b_NuMber,'NO','YES',session_id,'YES','','1','0');
		}
	function DtMf_PreSet_c_DiaL()
		{
		document.vicidial_form.xfernumber.value = CalL_XC_c_NuMber;
		basic_originate_call(CalL_XC_c_NuMber,'NO','YES',session_id,'YES','','1','0');
		}
	function DtMf_PreSet_d_DiaL()
		{
		document.vicidial_form.xfernumber.value = CalL_XC_d_NuMber;
		basic_originate_call(CalL_XC_d_NuMber,'NO','YES',session_id,'YES','','1','0');
		}
	function DtMf_PreSet_e_DiaL()
		{
		document.vicidial_form.xfernumber.value = CalL_XC_e_NuMber;
		basic_originate_call(CalL_XC_e_NuMber,'NO','YES',session_id,'YES','','1','0');
		}
	function hangup_timer_xfer()
		{
		hideDiv('CustomerGoneBox');
		WaitingForNextStep=0;
		custchannellive=0;

		dialedcall_send_hangup();
		}
	function extension_timer_xfer()
		{
		document.vicidial_form.xfernumber.value = timer_action_destination;
		mainxfer_send_redirect('XfeRBLIND',lastcustchannel,lastcustserverip);
		}
	function callmenu_timer_xfer()
		{
		API_selected_callmenu = timer_action_destination;
		document.vicidial_form.xfernumber.value = timer_action_destination;
		mainxfer_send_redirect('XfeRBLIND',lastcustchannel,lastcustserverip);
		}
	function ingroup_timer_xfer()
		{
		API_selected_xfergroup = timer_action_destination;
		document.vicidial_form.xfernumber.value = timer_action_destination;
		mainxfer_send_redirect('XfeRLOCAL',lastcustchannel,lastcustserverip);
		}

// ################################################################################
// Show message that customer has hungup the call before agent has
	function CustomerChanneLGone()
		{
		showDiv('CustomerGoneBox');

		document.getElementById("callchannel").innerHTML = '';
		document.vicidial_form.callserverip.value = '';
		document.getElementById("CustomerGoneChanneL").innerHTML = customer_3way_hangup_custom_message + ' ' + lastcustchannel;
		//if( document.images ) { document.images['livecall'].src = image_livecall_OFF.src;}
                document.getElementById('livecall').innerHTML = 'Not on Call';
                document.getElementById('livecall').className = 'off';
		WaitingForNextStep=1;
		}
	function CustomerGoneOK()
		{
		hideDiv('CustomerGoneBox');
		WaitingForNextStep=0;
		custchannellive=0;
		}
	function CustomerGoneHangup()
		{
		hideDiv('CustomerGoneBox');
		WaitingForNextStep=0;
		custchannellive=0;

		dialedcall_send_hangup();
		}
// ################################################################################
// Show message that there are no voice channels in the VICIDIAL session
        function NoneInSessionCheck(override)
            {
            if ( (check_n > 15) && (no_empty_session_warnings < 1) )
                {
                    if (nochannelinsession > 16)
                    {
                        NoneInSession();
                    }
                    else if (override == 1 && custchannellive == -99) // clicked on agent's name
                    {
                        NoneInSessionCalL();
                        custchannellive = 0;
                    }
                }
            }
	function NoneInSession()
		{
		showDiv('NoneInSessionBox');
		document.getElementById("NoneInSessionID").innerHTML = session_id;
		WaitingForNextStep=1;
		}
	function NoneInSessionOK()
		{
		hideDiv('NoneInSessionBox');
		WaitingForNextStep=0;
		nochannelinsession=0;
		}
	function NoneInSessionCalL()
		{
		hideDiv('NoneInSessionBox');
		WaitingForNextStep=0;
		nochannelinsession=0;

		if ( (protocol == 'EXTERNAL') || (protocol == 'Local') )
			{
			var protodial = 'Local';
			var extendial = extension;
	//		var extendial = extension + "@" + ext_context;
			}
		else
			{
			var protodial = protocol;
			var extendial = extension;
			}
		var originatevalue = protodial + "/" + extendial;
		var queryCID = "ACagcW" + epoch_sec + user_abb;

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
			VMCoriginate_query = "server_ip=" + server_ip + "&session_name=" + session_name + "&user=" + user + "&pass=" + pass  + "&ACTION=OriginateVDRelogin&format=text&channel=" + originatevalue + "&queryCID=" + queryCID + "&exten=" + session_id + "&ext_context=" + ext_context + "&ext_priority=1" + "&extension=" + extension + "&protocol=" + protocol + "&phone_ip=" + phone_ip + "&enable_sipsak_messages=" + enable_sipsak_messages + "&allow_sipsak_messages=" + allow_sipsak_messages + "&campaign=" + campaign + "&outbound_cid=" + campaign_cid;
			xmlhttp.open('POST', 'manager_send.php'); 
			xmlhttp.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
			xmlhttp.send(VMCoriginate_query); 
			xmlhttp.onreadystatechange = function() 
				{ 
				if (xmlhttp.readyState == 4 && xmlhttp.status == 200) 
					{
			//		alert(xmlhttp.responseText);
					}
				}
			delete xmlhttp;
			}
		if (auto_dial_level > 0)
			{
			AutoDial_ReSume_PauSe("VDADpause");
			}
		}


// ################################################################################
// Generate the Closer In Group Chooser panel
	function CloserSelectContent_create()
		{
		HidEGenDerPulldown();
		if ( (VU_agent_choose_ingroups == '1') && (manager_ingroups_set < 1) )
			{
            var live_CSC_HTML = "<table cellpadding=\"5\" cellspacing=\"5\" width=\"820px\"><tr><td><b>Available Inbound Queues</b></td><td><b>Selected Inbound Queues</b></td></tr><tr><td bgcolor=\"#eaeaea\" height=\"300px\" width=\"240px\" valign=\"top\"><font class=\"log_text\"><span id=CloserSelectAdd> &nbsp; <a href=\"#\" onclick=\"CloserSelect_change('-----ADD-ALL-----','ADD');return false;\"><b>--- Add All Queues ---</b><br />";
			var loop_ct = 0;
			while (loop_ct < INgroupCOUNT)
				{
                live_CSC_HTML = live_CSC_HTML + "<a href=\"#\" onclick=\"CloserSelect_change('" + VARingroups[loop_ct] + "','ADD');return false;\">" + VARingroups[loop_ct] + "<br />";
				loop_ct++;
				}
            live_CSC_HTML = live_CSC_HTML + "</span></font></td><td bgcolor=\"#eaeaea\" height=\"300px\" width=\"240px\" valign=\"top\"><font class=\"log_text\"><span id=CloserSelectDelete></span></font></td></tr></table>";

			document.vicidial_form.CloserSelectList.value = '';
			document.getElementById("CloserSelectContent").innerHTML = live_CSC_HTML;
			}
		else
			{
			VU_agent_choose_ingroups_DV = "MGRLOCK";
            var live_CSC_HTML = "Manager has selected groups for you<br />";
			document.vicidial_form.CloserSelectList.value = '';
			document.getElementById("CloserSelectContent").innerHTML = live_CSC_HTML;
			}
		if (focus_blur_enabled==1)
			{
			document.inert_form.inert_button.focus();
			document.inert_form.inert_button.blur();
			}
		}

// ################################################################################
// Move a Closer In Group record to the selected column or reverse
	function CloserSelect_change(taskCSgrp,taskCSchange)
		{
		var CloserSelectListValue = document.vicidial_form.CloserSelectList.value;
		var CSCchange = 0;
		var regCS = new RegExp(" " + taskCSgrp + " ","ig");
		var regCSall = new RegExp("-ALL-----","ig");
		var regCSallADD = new RegExp("-----ADD-ALL-----","ig");
		var regCSallDELETE = new RegExp("-----DELETE-ALL-----","ig");
		if ( (CloserSelectListValue.match(regCS)) && (CloserSelectListValue.length > 3) )
			{
			if (taskCSchange == 'DELETE') {CSCchange = 1;}
			}
		else
			{
			if (taskCSchange == 'ADD') {CSCchange = 1;}
			}
		if (taskCSgrp.match(regCSall))
			{CSCchange = 1;}

	//	alert(taskCSgrp+"|"+taskCSchange+"|"+CloserSelectListValue.length+"|"+CSCchange+"|"+CSCcolumn+"|"+INgroupCOUNT)

		if (CSCchange==1) 
			{
			var loop_ct = 0;
			var CSCcolumn = '';
			var live_CSC_HTML_ADD = '';
			var live_CSC_HTML_DELETE = '';
			var live_CSC_LIST_value = " ";
			while (loop_ct < INgroupCOUNT)
				{
				var regCSL = new RegExp(" " + VARingroups[loop_ct] + " ","ig");
				if (CloserSelectListValue.match(regCSL)) {CSCcolumn = 'DELETE';}
				else {CSCcolumn = 'ADD';}
				if ( ( (VARingroups[loop_ct] == taskCSgrp) && (taskCSchange == 'DELETE') ) || (taskCSgrp.match(regCSallDELETE)) ) {CSCcolumn = 'ADD';}
				if ( ( (VARingroups[loop_ct] == taskCSgrp) && (taskCSchange == 'ADD') ) || (taskCSgrp.match(regCSallADD)) ) {CSCcolumn = 'DELETE';}
					

				if (CSCcolumn == 'DELETE')
					{
                    live_CSC_HTML_DELETE = live_CSC_HTML_DELETE + "<a href=\"#\" onclick=\"CloserSelect_change('" + VARingroups[loop_ct] + "','DELETE');return false;\">" + VARingroups[loop_ct] + "<br />";
					live_CSC_LIST_value = live_CSC_LIST_value + VARingroups[loop_ct] + " ";
					}
				else
					{
                    live_CSC_HTML_ADD = live_CSC_HTML_ADD + "<a href=\"#\" onclick=\"CloserSelect_change('" + VARingroups[loop_ct] + "','ADD');return false;\">" + VARingroups[loop_ct] + "<br />";
					}
				loop_ct++;
				}

			document.vicidial_form.CloserSelectList.value = live_CSC_LIST_value;
            document.getElementById("CloserSelectAdd").innerHTML = " &nbsp; <a href=\"#\" onclick=\"CloserSelect_change('-----ADD-ALL-----','ADD');return false;\"><b>--- Add All Queues ---</b><br />" + live_CSC_HTML_ADD;
            document.getElementById("CloserSelectDelete").innerHTML = " &nbsp; <a href=\"#\" onclick=\"CloserSelect_change('-----DELETE-ALL-----','DELETE');return false;\"><b>--- Delete All Queues ---</b><br />" + live_CSC_HTML_DELETE;
			}
		}

// ################################################################################
// Update vicidial_live_agents record with closer in group choices
	function CloserSelect_submit()
		{
		if (dial_method == "INBOUND_MAN")
			{document.vicidial_form.CloserSelectBlended.checked=false;}
		if (document.vicidial_form.CloserSelectBlended.checked==true)
			{VICIDiaL_closer_blended = 1;}
		else
			{VICIDiaL_closer_blended = 0;}

		var CloserSelectChoices = document.vicidial_form.CloserSelectList.value;

		if (call_requeue_button > 0)
			{
            document.getElementById("ReQueueCall").innerHTML =  "<a href=\"#\" style=\"opacity:.15;\" disabled><img class=\"disabled_img\" src=\"./images/x-requeue-call.png\" border=\"0\" alt=\"Re-Queue Call\" /><br /><span>Re-Queue Call</span></a>";
			}
		//else
		//	{
		//	document.getElementById("ReQueueCall").innerHTML =  "";
		//	}

		if (VU_agent_choose_ingroups_DV == "MGRLOCK")
			{CloserSelectChoices = "MGRLOCK";}

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
			CSCupdate_query = "server_ip=" + server_ip + "&session_name=" + session_name + "&ACTION=regCLOSER&format=text&user=" + user + "&pass=" + pass + "&comments=" + VU_agent_choose_ingroups_DV + "&closer_blended=" + VICIDiaL_closer_blended + "&campaign=" + campaign + "&qm_phone=" + qm_phone + "&dial_method" + dial_method + "&closer_choice=" + CloserSelectChoices + "-";
			xmlhttp.open('POST', 'vdc_db_query.php'); 
			xmlhttp.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
			xmlhttp.send(CSCupdate_query); 
			xmlhttp.onreadystatechange = function() 
				{ 
				if (xmlhttp.readyState == 4 && xmlhttp.status == 200) 
					{
		//			alert(xmlhttp.responseText);
					}
				}
			delete xmlhttp;
			}

		hideDiv('CloserSelectBox');
		MainPanelToFront();
		CloserSelecting = 0;
		scroll(0,0);
		}


// ################################################################################
// Generate the Territory Chooser panel
	function TerritorySelectContent_create()
		{
		if (agent_select_territories == '1')
			{
			HidEGenDerPulldown();
			if (agent_choose_territories > 0)
				{
                var live_TERR_HTML = "<table cellpadding=\"5\" cellspacing=\"5\" width=\"500px\"><tr><td><b>TERRITORIES NOT SELECTED</b></td><td><b>SELECTED TERRITORIES</b></td></tr><tr><td bgcolor=\"#99FF99\" height=\"300px\" width=\"240px\" valign=\"top\"><font class=\"log_text\"><span id=TerritorySelectAdd> &nbsp; <a href=\"#\" onclick=\"TerritorySelect_change('-----ADD-ALL-----','ADD');return false;\"><b>--- ADD ALL ---</b><br />";
				var loop_ct = 0;
				while (loop_ct < territoryCOUNT)
					{
                    live_TERR_HTML = live_TERR_HTML + "<a href=\"#\" onclick=\"TerritorySelect_change('" + VARterritories[loop_ct] + "','ADD');return false;\">" + VARterritories[loop_ct] + "<br />";
					loop_ct++;
					}
                live_TERR_HTML = live_TERR_HTML + "</span></font></td><td bgcolor=\"#99FF99\" height=\"300px\" width=\"240px\" valign=\"top\"><font class=\"log_text\"><span id=TerritorySelectDelete></span></font></td></tr></table>";

				document.vicidial_form.TerritorySelectList.value = '';
				document.getElementById("TerritorySelectContent").innerHTML = live_TERR_HTML;
				}
			else
				{
				agent_select_territories = "MGRLOCK";
                var live_TERR_HTML = "Manager has selected territories for you<br />";
				document.vicidial_form.TerritorySelectList.value = '';
				document.getElementById("TerritorySelectContent").innerHTML = live_TERR_HTML;
				}
			}
		if (focus_blur_enabled==1)
			{
			document.inert_form.inert_button.focus();
			document.inert_form.inert_button.blur();
			}
		}

// ################################################################################
// Move a Territory record to the selected column or reverse
	function TerritorySelect_change(taskTERRgrp,taskTERRchange)
		{
		var TerritorySelectListValue = document.vicidial_form.TerritorySelectList.value;
		var TERRchange = 0;
		var regTERR = new RegExp(" " + taskTERRgrp + " ","ig");
		var regTERRall = new RegExp("-ALL-----","ig");
		var regTERRallADD = new RegExp("-----ADD-ALL-----","ig");
		var regTERRallDELETE = new RegExp("-----DELETE-ALL-----","ig");
		if ( (TerritorySelectListValue.match(regTERR)) && (TerritorySelectListValue.length > 3) )
			{
			if (taskTERRchange == 'DELETE') {TERRchange = 1;}
			}
		else
			{
			if (taskTERRchange == 'ADD') {TERRchange = 1;}
			}
		if (taskTERRgrp.match(regTERRall))
			{TERRchange = 1;}
//	alert("TERR: " + TerritorySelectListValue + "\nCHANGE: " + TERRchange + "\nACTION: " + taskTERRchange + "\nSELECTED: " + taskTERRgrp + "\nTOTAL: " + territoryCOUNT);
		if (TERRchange==1) 
			{
			var loop_ct = 0;
			var TERRcolumn = '';
			var live_TERR_HTML_ADD = '';
			var live_TERR_HTML_DELETE = '';
			var live_TERR_LIST_value = " ";
			while (loop_ct < territoryCOUNT)
				{
				var regTERRL = new RegExp(" " + VARterritories[loop_ct] + " ","ig");
				if (TerritorySelectListValue.match(regTERRL)) {TERRcolumn = 'DELETE';}
				else {TERRcolumn = 'ADD';}
				if ( ( (VARterritories[loop_ct] == taskTERRgrp) && (taskTERRchange == 'DELETE') ) || (taskTERRgrp.match(regTERRallDELETE)) ) 
					{TERRcolumn = 'ADD';}
				if ( ( (VARterritories[loop_ct] == taskTERRgrp) && (taskTERRchange == 'ADD') ) || (taskTERRgrp.match(regTERRallADD)) ) 
					{TERRcolumn = 'DELETE';}

				if (TERRcolumn == 'DELETE')
					{
                    live_TERR_HTML_DELETE = live_TERR_HTML_DELETE + "<a href=\"#\" onclick=\"TerritorySelect_change('" + VARterritories[loop_ct] + "','DELETE');return false;\">" + VARterritories[loop_ct] + "<br />";
					live_TERR_LIST_value = live_TERR_LIST_value + VARterritories[loop_ct] + " ";
					}
				else
					{
                    live_TERR_HTML_ADD = live_TERR_HTML_ADD + "<a href=\"#\" onclick=\"TerritorySelect_change('" + VARterritories[loop_ct] + "','ADD');return false;\">" + VARterritories[loop_ct] + "<br />";
					}
				loop_ct++;
				}

			document.vicidial_form.TerritorySelectList.value = live_TERR_LIST_value;
            document.getElementById("TerritorySelectAdd").innerHTML = " &nbsp; <a href=\"#\" onclick=\"TerritorySelect_change('-----ADD-ALL-----','ADD');return false;\"><b>--- ADD ALL ---</b><br />" + live_TERR_HTML_ADD;
            document.getElementById("TerritorySelectDelete").innerHTML = " &nbsp; <a href=\"#\" onclick=\"TerritorySelect_change('-----DELETE-ALL-----','DELETE');return false;\"><b>--- DELETE ALL ---</b><br />" + live_TERR_HTML_DELETE;
			}
		}

// ################################################################################
// Enable or Disable manual dial queue calls
	function ManualQueueChoiceChange(task_amqc)
		{
		AllowManualQueueCalls = task_amqc;
		var TerritorySelectChoices = document.vicidial_form.TerritorySelectList.value;

		if (AllowManualQueueCalls == '0')
            {document.getElementById("ManualQueueChoice").innerHTML = "<a href=\"#\" onclick=\"ManualQueueChoiceChange('1');return false;\">Manual Queue is Off</a><br />";}
		else
            {document.getElementById("ManualQueueChoice").innerHTML = "<a href=\"#\" onclick=\"ManualQueueChoiceChange('0');return false;\">Manual Queue in On</a><br />";}
		}

// ################################################################################
// Update vicidial_live_agents record with territory choices
	function TerritorySelect_submit()
		{
		var TerritorySelectChoices = document.vicidial_form.TerritorySelectList.value;

		if (agent_select_territories == "MGRLOCK")
			{TerritorySelectChoices = "MGRLOCK";}

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
			TERRupdate_query = "server_ip=" + server_ip + "&session_name=" + session_name + "&ACTION=regTERRITORY&format=text&user=" + user + "&pass=" + pass + "&comments=" + agent_select_territories + "&campaign=" + campaign + "&agent_territories=" + TerritorySelectChoices + "-";
			xmlhttp.open('POST', 'vdc_db_query.php'); 
			xmlhttp.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
			xmlhttp.send(TERRupdate_query); 
			xmlhttp.onreadystatechange = function() 
				{ 
				if (xmlhttp.readyState == 4 && xmlhttp.status == 200) 
					{
		//			alert(xmlhttp.responseText);
					}
				}
			delete xmlhttp;
			}

		hideDiv('TerritorySelectBox');
		MainPanelToFront();
		TerritorySelecting = 0;
		scroll(0,0);
		}


// ################################################################################
// clear api field
	function Clear_API_Field(temp_field)
		{
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
			TERRupdate_query = "server_ip=" + server_ip + "&session_name=" + session_name + "&ACTION=Clear_API_Field&format=text&user=" + user + "&pass=" + pass + "&comments=" + temp_field;
			xmlhttp.open('POST', 'vdc_db_query.php'); 
			xmlhttp.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
			xmlhttp.send(TERRupdate_query); 
			xmlhttp.onreadystatechange = function() 
				{ 
				if (xmlhttp.readyState == 4 && xmlhttp.status == 200) 
					{
		//			alert(xmlhttp.responseText);
					}
				}
			delete xmlhttp;
			}
		}


// ################################################################################
// Log the user out of the system when they close their browser while logged in
	function BrowserCloseLogout()
		{
		if (logout_stop_timeouts < 1)
			{
			if (VDRP_stage != 'PAUSED')
				{
				AutoDial_ReSume_PauSe("VDADpause",'','','',"LOGOUT");
				}
			LogouT('CLOSE');
			alert("PLEASE CLICK THE LOGOUT LINK TO LOG OUT NEXT TIME.\n");
			}
		}


// ################################################################################
// Normal logout with check for pause stage first
	function NormalLogout()
		{
		if (logout_stop_timeouts < 1)
			{
			if (VDRP_stage != 'PAUSED')
				{
				AutoDial_ReSume_PauSe("VDADpause",'','','',"LOGOUT");
				}
			LogouT('NORMAL');
			}
		}


// ################################################################################
// Log the user out of the system, if active call or active dial is occuring, don't let them.
	function LogouT(tempreason)
		{
		if (MD_channel_look==1)
			{alert("You cannot log out during a Dial attempt. \nWait 50 seconds for the dial to timeout if call is not answered");}
		else
			{
			if (VD_live_customer_call==1)
				{
				alert("STILL A LIVE CALL! Hang it up then you can log out.\n" + VD_live_customer_call);
				}
			else
				{
				if (alt_dial_status_display==1)
					{
					alert("You are in ALT dial mode, you must finish the lead before logging out.\n" + reselect_alt_dial);
					}
				else
					{
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
						VDlogout_query = "server_ip=" + server_ip + "&session_name=" + session_name + "&ACTION=userLOGout&format=text&user=" + user + "&pass=" + pass + "&campaign=" + campaign + "&conf_exten=" + session_id + "&extension=" + extension + "&protocol=" + protocol + "&agent_log_id=" + agent_log_id + "&no_delete_sessions=" + no_delete_sessions + "&phone_ip=" + phone_ip + "&enable_sipsak_messages=" + enable_sipsak_messages + "&LogouTKicKAlL=" + LogouTKicKAlL + "&ext_context=" + ext_context;
						xmlhttp.open('POST', 'vdc_db_query.php'); 
						xmlhttp.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
						xmlhttp.send(VDlogout_query); 
						xmlhttp.onreadystatechange = function() 
							{ 
							if (xmlhttp.readyState == 4 && xmlhttp.status == 200) 
								{
					//			alert(xmlhttp.responseText);
								}
							}
						delete xmlhttp;
						}

					hideDiv('MainPanel');
					showDiv('LogouTBox');
					var logout_content='';
					if (tempreason=='SHIFT')
                        {logout_content='Your Shift is over or has changed, you have been logged out of your session<br /><br />';}

					document.getElementById("LogouTBoxLink").innerHTML = logout_content + "<a href=\"" + agcPAGE + "?relogin=YES&session_epoch=" + epoch_sec + "&session_id=" + session_id + "&session_name=" + session_name + "&VD_login=" + user + "&VD_campaign=" + campaign + "&phone_login=" + original_phone_login + "&phone_pass=" + phone_pass + "&VD_pass=" + pass + "\"><img src=\"./images/InktelX-Frontline-Sponsored.png\" border=\"0\" alt=\"X Frontline\" /><br /><br />Click Here to Login Again</a>\n";

					logout_stop_timeouts = 1;
						
					//	window.location= agcPAGE + "?relogin=YES&session_epoch=" + epoch_sec + "&session_id=" + session_id + "&session_name=" + session_name + "&VD_login=" + user + "&VD_campaign=" + campaign + "&phone_login=" + phone_login + "&phone_pass=" + phone_pass + "&VD_pass=" + pass;
					}
				}
			}
		}
<?php
if ($useIE > 0)
{
?>
// ################################################################################
// MSIE-only hotkeypress function to bind hotkeys defined in the campaign to dispositions
	function hotkeypress(evt)
		{
		enter_disable();
		if ( (hot_keys_active==1) && ( (VD_live_customer_call==1) || (MD_ring_secondS > 4) ) )
			{
			var e = evt? evt : window.event;
			if(!e) return;
			var key = 0;
			if (e.keyCode) { key = e.keyCode; } // for moz/fb, if keyCode==0 use 'which'
			else if (typeof(e.which)!= 'undefined') { key = e.which; }

			var HKdispo = hotkeys[String.fromCharCode(key)];
		//	alert("|" + key + "|" + HKdispo + "|");
			if (HKdispo) 
				{
			//	document.vicidial_form.inert_button.focus();
			//	document.vicidial_form.inert_button.blur();
				CustomerData_update();
				var HKdispo_ary = HKdispo.split(" ----- ");
				if ( (HKdispo_ary[0] == 'ALTPH2') || (HKdispo_ary[0] == 'ADDR3') )
					{
					if (document.vicidial_form.DiaLAltPhonE.checked==true)
						{
						dialedcall_send_hangup('NO', 'YES', HKdispo_ary[0]);
						}
					}
				else
					{
					HKdispo_display = 4;
					HKfinish=1;
					document.getElementById("HotKeyDispo").innerHTML = HKdispo_ary[0] + " - " + HKdispo_ary[1];
					showDiv('HotKeyActionBox');
					hideDiv('HotKeyEntriesBox');
					document.vicidial_form.DispoSelection.value = HKdispo_ary[0];
					dialedcall_send_hangup('NO', 'YES', HKdispo_ary[0]);
					if (custom_fields_enabled > 0)
						{
						vcFormIFrame.document.form_custom_fields.submit();
						}
					}
				}
			}
		}

<?php
}
else
{
?>
// ################################################################################
// W3C-compliant hotkeypress function to bind hotkeys defined in the campaign to dispositions
	function hotkeypress(evt)
		{
		enter_disable();
		if ( (hot_keys_active==1) && ( (VD_live_customer_call==1) || (MD_ring_secondS > 4) ) )
			{
			var e = evt? evt : window.event;
			if(!e) return;
			var key = 0;
			if (e.keyCode) { key = e.keyCode; } // for moz/fb, if keyCode==0 use 'which'
			else if (typeof(e.which)!= 'undefined') { key = e.which; }
			//
			var HKdispo = hotkeys[String.fromCharCode(key)];
			if (HKdispo) 
				{
				if (focus_blur_enabled==1)
					{
					document.inert_form.inert_button.focus();
					document.inert_form.inert_button.blur();
					}
				CustomerData_update();
				var HKdispo_ary = HKdispo.split(" ----- ");
				if ( (HKdispo_ary[0] == 'ALTPH2') || (HKdispo_ary[0] == 'ADDR3') )
					{
					if (document.vicidial_form.DiaLAltPhonE.checked==true)
						{
						dialedcall_send_hangup('NO', 'YES', HKdispo_ary[0]);
						}
					}
				else
					{
					HKdispo_display = 4;
					HKfinish=1;
					document.getElementById("HotKeyDispo").innerHTML = HKdispo_ary[0] + " - " + HKdispo_ary[1];
					showDiv('HotKeyActionBox');
					hideDiv('HotKeyEntriesBox');
					document.vicidial_form.DispoSelection.value = HKdispo_ary[0];
					dialedcall_send_hangup('NO', 'YES', HKdispo_ary[0]);
					if (custom_fields_enabled > 0)
						{
						vcFormIFrame.document.form_custom_fields.submit();
						}
					}
			//	DispoSelect_submit();
			//	AutoDialWaiting = 1;
			//	AutoDial_ReSume_PauSe("VDADready");
			//	alert(HKdispo + " - " + HKdispo_ary[0] + " - " + HKdispo_ary[1]);
				}
			}
		}

<?php
}
### end of onkeypress functions
?>
// ################################################################################
// disable enter/return keys to not clear out vars on customer info
	function enter_disable(evt)
		{
		var e = evt? evt : window.event;
		if(!e) return;
		var key = 0;
		if (e.keyCode) { key = e.keyCode; } // for moz/fb, if keyCode==0 use 'which'
		else if (typeof(e.which)!= 'undefined') { key = e.which; }
		return key != 13;
		}


// ################################################################################
// decode the scripttext and scriptname so that it can be displayed
	function URLDecode(encodedvar,scriptformat,urlschema,webformnumber)
	{
   // Replace %ZZ with equivalent character
   // Put [ERR] in output if %ZZ is invalid.
	var HEXCHAR = "0123456789ABCDEFabcdef"; 
	var encoded = encodedvar;
	var decoded = '';
	var web_form_varsX = '';
	var i = 0;
	var RGnl = new RegExp("[\\r]\\n","g");
	var RGtab = new RegExp("\t","g");
	var RGplus = new RegExp(" |\\t|\\n","g");
	var RGiframe = new RegExp("iframe","gi");
	var regWF = new RegExp("\\`|\\~|\\:|\\;|\\#|\\'|\\\"|\\{|\\}|\\(|\\)|\\*|\\^|\\%|\\$|\\!|\\%|\\r|\\t|\\n","ig");

	var xtest;
	xtest=unescape(encoded);
	encoded=utf8_decode(xtest);

	if (urlschema == 'DEFAULT')
		{
		web_form_varsX = 
		"&lead_id=" + document.vicidial_form.lead_id.value + 
		"&vendor_id=" + document.vicidial_form.vendor_lead_code.value + 
		"&list_id=" + document.vicidial_form.list_id.value + 
		"&gmt_offset_now=" + document.vicidial_form.gmt_offset_now.value + 
		"&phone_code=" + document.vicidial_form.phone_code.value + 
		"&phone_number=" + document.vicidial_form.phone_number.value + 
		"&title=" + document.vicidial_form.title.value + 
		"&first_name=" + document.vicidial_form.first_name.value + 
		"&middle_initial=" + document.vicidial_form.middle_initial.value + 
		"&last_name=" + document.vicidial_form.last_name.value + 
		"&address1=" + document.vicidial_form.address1.value + 
		"&address2=" + document.vicidial_form.address2.value + 
		"&address3=" + document.vicidial_form.address3.value + 
		"&city=" + document.vicidial_form.city.value + 
		"&state=" + document.vicidial_form.state.value + 
		"&province=" + document.vicidial_form.province.value + 
		"&postal_code=" + document.vicidial_form.postal_code.value + 
		"&country_code=" + document.vicidial_form.country_code.value + 
		"&gender=" + document.vicidial_form.gender.value + 
		"&date_of_birth=" + document.vicidial_form.date_of_birth.value + 
		"&alt_phone=" + document.vicidial_form.alt_phone.value + 
		"&email=" + document.vicidial_form.email.value + 
		"&security_phrase=" + document.vicidial_form.security_phrase.value + 
		"&comments=" + document.vicidial_form.comments.value + 
		"&user=" + user + 
		"&pass=" + pass + 
		"&campaign=" + campaign + 
		"&phone_login=" + phone_login + 
		"&original_phone_login=" + original_phone_login +
		"&phone_pass=" + phone_pass + 
		"&fronter=" + fronter + 
		"&closer=" + user + 
		"&group=" + group + 
		"&channel_group=" + group + 
		"&SQLdate=" + SQLdate + 
		"&epoch=" + UnixTime + 
		"&uniqueid=" + document.vicidial_form.uniqueid.value + 
		"&customer_zap_channel=" + lastcustchannel + 
		"&customer_server_ip=" + lastcustserverip +
		"&server_ip=" + server_ip + 
		"&SIPexten=" + extension + 
		"&session_id=" + session_id + 
		"&phone=" + document.vicidial_form.phone_number.value + 
		"&parked_by=" + document.vicidial_form.lead_id.value +
		"&dispo=" + LeaDDispO + '' +
		"&dialed_number=" + dialed_number + '' +
		"&dialed_label=" + dialed_label + '' +
		"&source_id=" + source_id + '' +
		"&rank=" + document.vicidial_form.rank.value + '' +
		"&owner=" + document.vicidial_form.owner.value + '' +
		"&camp_script=" + campaign_script + '' +
		"&in_script=" + CalL_ScripT_id + '' +
		"&script_width=" + script_width + '' +
		"&script_height=" + script_height + '' +
		"&fullname=" + LOGfullname + '' +
		"&recording_filename=" + recording_filename + '' +
		"&recording_id=" + recording_id + '' +
		"&user_custom_one=" + VU_custom_one + '' +
		"&user_custom_two=" + VU_custom_two + '' +
		"&user_custom_three=" + VU_custom_three + '' +
		"&user_custom_four=" + VU_custom_four + '' +
		"&user_custom_five=" + VU_custom_five + '' +
		"&preset_number_a=" + CalL_XC_a_NuMber + '' +
		"&preset_number_b=" + CalL_XC_b_NuMber + '' +
		"&preset_number_c=" + CalL_XC_c_NuMber + '' +
		"&preset_number_d=" + CalL_XC_d_NuMber + '' +
		"&preset_number_e=" + CalL_XC_e_NuMber + '' +
		"&preset_dtmf_a=" + CalL_XC_a_Dtmf + '' +
		"&preset_dtmf_b=" + CalL_XC_b_Dtmf + '' +
		"&did_id=" + did_id + '' +
		"&did_extension=" + did_extension + '' +
		"&did_pattern=" + did_pattern + '' +
		"&did_description=" + did_description + '' +
		"&closecallid=" + closecallid + '' +
		"&xfercallid=" + xfercallid + '' +
		"&agent_log_id=" + agent_log_id + '' +
		"&entry_list_id=" + document.vicidial_form.entry_list_id.value + '' +
		"&web_vars=" + LIVE_web_vars + '' +
		webform_session;
		
		if (custom_field_names.length > 2)
			{
			var url_custom_field='';
			var CFN_array=custom_field_names.split('|');
			var CFN_count=CFN_array.length;
			var CFN_tick=0;
			while (CFN_tick < CFN_count)
				{
				var CFN_field = CFN_array[CFN_tick];
				if (CFN_field.length > 0)
					{
					var url_custom_field = url_custom_field + "&" + CFN_field + "=--A--" + CFN_field + "--B--";
					}
				CFN_tick++;
				}
			if (url_custom_field.length > 10)
				{
				url_custom_field = '&CF_uses_custom_fields=Y' + url_custom_field;
				}
			web_form_varsX = web_form_varsX + '' + url_custom_field;
			scriptformat='YES';
			}

		web_form_varsX = web_form_varsX.replace(RGplus, '+');
		web_form_varsX = web_form_varsX.replace(RGnl, '+');
		web_form_varsX = web_form_varsX.replace(regWF, '');

		var regWFAvars = new RegExp("\\?","ig");
		if (encoded.match(regWFAvars))
			{web_form_varsX = '&' + web_form_varsX}
		else
			{web_form_varsX = '?' + web_form_varsX}

		var TEMPX_VDIC_web_form_address = encoded + "" + web_form_varsX;

		var regWFAqavars = new RegExp("\\?&","ig");
		var regWFAaavars = new RegExp("&&","ig");
		TEMPX_VDIC_web_form_address = TEMPX_VDIC_web_form_address.replace(regWFAqavars, '?');
		TEMPX_VDIC_web_form_address = TEMPX_VDIC_web_form_address.replace(regWFAaavars, '&');
		encoded = TEMPX_VDIC_web_form_address;
		}
	if (scriptformat == 'YES')
		{
		// custom fields populate if lead information is sent with custom field names
		if (custom_field_names.length > 2)
			{
			var CFN_array=custom_field_names.split('|');
			var CFV_array=custom_field_values.split('----------');
			var CFT_array=custom_field_types.split('|');
			var CFN_count=CFN_array.length;
			var CFN_tick=0;
			var CFN_debug='';
			var CF_loaded = document.vicidial_form.FORM_LOADED.value;
		//	alert(custom_field_names + "\n" + custom_field_values + "\n" + CFN_count + "\n" + CF_loaded);
			while (CFN_tick < CFN_count)
				{
				var CFN_field = CFN_array[CFN_tick];
				var RG_CFN_field = new RegExp("--A--" + CFN_field + "--B--","g");
				if ( (CFN_field.length > 0) && (encoded.match(RG_CFN_field)) )
					{
					if (CF_loaded=='1')
						{
						var CFN_value='';
						var field_parsed=0;
						if ( (CFT_array[CFN_tick]=='TIME') && (field_parsed < 1) )
							{
							var CFN_field_hour = 'HOUR_' + CFN_field;
							var cIndex_hour = vcFormIFrame.document.form_custom_fields[CFN_field_hour].selectedIndex;
							var CFN_value_hour =  vcFormIFrame.document.form_custom_fields[CFN_field_hour].options[cIndex_hour].value;
							var CFN_field_minute = 'MINUTE_' + CFN_field;
							var cIndex_minute = vcFormIFrame.document.form_custom_fields[CFN_field_minute].selectedIndex;
							var CFN_value_minute =  vcFormIFrame.document.form_custom_fields[CFN_field_minute].options[cIndex_minute].value;
							var CFN_value = CFN_value_hour + ':' + CFN_value_minute + ':00'
							field_parsed=1;
							}
						if ( (CFT_array[CFN_tick]=='SELECT') && (field_parsed < 1) )
							{
							var cIndex = vcFormIFrame.document.form_custom_fields[CFN_field].selectedIndex;
							var CFN_value =  vcFormIFrame.document.form_custom_fields[CFN_field].options[cIndex].value;
							field_parsed=1;
							}
						if ( (CFT_array[CFN_tick]=='MULTI') && (field_parsed < 1) )
							{
							var chosen = '';
							var CFN_field = CFN_field + '[]';
							for (i=0; i<vcFormIFrame.document.form_custom_fields[CFN_field].options.length; i++) 
								{
								if (vcFormIFrame.document.form_custom_fields[CFN_field].options[i].selected) 
									{
									chosen = chosen + '' + vcFormIFrame.document.form_custom_fields[CFN_field].options[i].value + ',';
									}
								}
							var CFN_value = chosen;
							if (CFN_value.length > 0) {CFN_value = CFN_value.slice(0,-1);}
							field_parsed=1;
							}
						if ( ( (CFT_array[CFN_tick]=='RADIO') || (CFT_array[CFN_tick]=='CHECKBOX') ) && (field_parsed < 1) )
							{
							var chosen = '';
							var CFN_field = CFN_field + '[]';
							var len = vcFormIFrame.document.form_custom_fields[CFN_field].length;
							for (i = 0; i <len; i++) 
								{
								if (vcFormIFrame.document.form_custom_fields[CFN_field][i].checked) 
									{
									chosen = chosen + '' + vcFormIFrame.document.form_custom_fields[CFN_field][i].value + ',';
									}
								}
							var CFN_value = chosen;
							if (CFN_value.length > 0) {CFN_value = CFN_value.slice(0,-1);}
							field_parsed=1;
							}
						if (field_parsed < 1)
							{
							var CFN_value = vcFormIFrame.document.form_custom_fields[CFN_field].value;
							field_parsed=1;
							}
						}
					else
						{
						var CFN_value = CFV_array[CFN_tick];
						}
					CFN_value = CFN_value.replace(RGnl,'+');
					CFN_value = CFN_value.replace(RGtab,'+');
					CFN_value = CFN_value.replace(RGplus,'+');
					encoded = encoded.replace(RG_CFN_field, CFN_value);
					web_form_varsX = web_form_varsX.replace(RG_CFN_field, CFN_value);
					CFN_debug = CFN_debug + '|' + CFN_field + '-' + CFN_value;
					}
				CFN_tick++;
				}
			document.getElementById("debugbottomspan").innerHTML = CFN_debug;
			}

		if (webformnumber == '1')
			{web_form_vars = web_form_varsX;}
		if (webformnumber == '2')
			{web_form_vars_two = web_form_varsX;}

		var SCvendor_lead_code = document.vicidial_form.vendor_lead_code.value;
		var SCsource_id = source_id;
		var SClist_id = document.vicidial_form.list_id.value;
		var SCgmt_offset_now = document.vicidial_form.gmt_offset_now.value;
		var SCcalled_since_last_reset = "";
		var SCphone_code = document.vicidial_form.phone_code.value;
		var SCphone_number = document.vicidial_form.phone_number.value;
		var SCtitle = document.vicidial_form.title.value;
		var SCfirst_name = document.vicidial_form.first_name.value;
		var SCmiddle_initial = document.vicidial_form.middle_initial.value;
		var SClast_name = document.vicidial_form.last_name.value;
		var SCaddress1 = document.vicidial_form.address1.value;
		var SCaddress2 = document.vicidial_form.address2.value;
		var SCaddress3 = document.vicidial_form.address3.value;
		var SCcity = document.vicidial_form.city.value;
		var SCstate = document.vicidial_form.state.value;
		var SCprovince = document.vicidial_form.province.value;
		var SCpostal_code = document.vicidial_form.postal_code.value;
		var SCcountry_code = document.vicidial_form.country_code.value;
		var SCgender = document.vicidial_form.gender.value;
		var SCdate_of_birth = document.vicidial_form.date_of_birth.value;
		var SCalt_phone = document.vicidial_form.alt_phone.value;
		var SCemail = document.vicidial_form.email.value;
		var SCsecurity_phrase = document.vicidial_form.security_phrase.value;
		var SCcomments = document.vicidial_form.comments.value;
		var SCfullname = LOGfullname;
		var SCfronter = fronter;
		var SCuser = user;
		var SCpass = pass;
		var SClead_id = document.vicidial_form.lead_id.value;
		var SCcampaign = campaign;
		var SCphone_login = phone_login;
		var SCoriginal_phone_login = original_phone_login;
		var SCgroup = group;
		var SCchannel_group = group;
		var SCSQLdate = SQLdate;
		var SCepoch = UnixTime;
		var SCuniqueid = document.vicidial_form.uniqueid.value;
		var SCcustomer_zap_channel = lastcustchannel;
		var SCserver_ip = server_ip;
		var SCSIPexten = extension;
		var SCsession_id = session_id;
		var SCdispo = LeaDDispO;
		var SCdialed_number = dialed_number;
		var SCdialed_label = dialed_label;
		var SCrank = document.vicidial_form.rank.value;
		var SCowner = document.vicidial_form.owner.value;
		var SCcamp_script = campaign_script;
		var SCin_script = CalL_ScripT_id;
		var SCscript_width = script_width;
		var SCscript_height = script_height;
		var SCrecording_filename = recording_filename;
		var SCrecording_id = recording_id;
		var SCuser_custom_one = VU_custom_one;
		var SCuser_custom_two = VU_custom_two;
		var SCuser_custom_three = VU_custom_three;
		var SCuser_custom_four = VU_custom_four;
		var SCuser_custom_five = VU_custom_five;
		var SCpreset_number_a = CalL_XC_a_NuMber;
		var SCpreset_number_b = CalL_XC_b_NuMber;
		var SCpreset_number_c = CalL_XC_c_NuMber;
		var SCpreset_number_d = CalL_XC_d_NuMber;
		var SCpreset_number_e = CalL_XC_e_NuMber;
		var SCpreset_dtmf_a = CalL_XC_a_Dtmf;
		var SCpreset_dtmf_b = CalL_XC_b_Dtmf;
		var SCdid_id = did_id;
		var SCdid_extension = did_extension;
		var SCdid_pattern = did_pattern;
		var SCdid_description = did_description;
		var SCclosecallid = closecallid;
		var SCxfercallid = xfercallid;
		var SCagent_log_id = agent_log_id;
		var SCweb_vars = LIVE_web_vars;

		if (encoded.match(RGiframe))
			{
			SCvendor_lead_code = SCvendor_lead_code.replace(RGplus,'+');
			SCsource_id = SCsource_id.replace(RGplus,'+');
			SClist_id = SClist_id.replace(RGplus,'+');
			SCgmt_offset_now = SCgmt_offset_now.replace(RGplus,'+');
			SCcalled_since_last_reset = SCcalled_since_last_reset.replace(RGplus,'+');
			SCphone_code = SCphone_code.replace(RGplus,'+');
			SCphone_number = SCphone_number.replace(RGplus,'+');
			SCtitle = SCtitle.replace(RGplus,'+');
			SCfirst_name = SCfirst_name.replace(RGplus,'+');
			SCmiddle_initial = SCmiddle_initial.replace(RGplus,'+');
			SClast_name = SClast_name.replace(RGplus,'+');
			SCaddress1 = SCaddress1.replace(RGplus,'+');
			SCaddress2 = SCaddress2.replace(RGplus,'+');
			SCaddress3 = SCaddress3.replace(RGplus,'+');
			SCcity = SCcity.replace(RGplus,'+');
			SCstate = SCstate.replace(RGplus,'+');
			SCprovince = SCprovince.replace(RGplus,'+');
			SCpostal_code = SCpostal_code.replace(RGplus,'+');
			SCcountry_code = SCcountry_code.replace(RGplus,'+');
			SCgender = SCgender.replace(RGplus,'+');
			SCdate_of_birth = SCdate_of_birth.replace(RGplus,'+');
			SCalt_phone = SCalt_phone.replace(RGplus,'+');
			SCemail = SCemail.replace(RGplus,'+');
			SCsecurity_phrase = SCsecurity_phrase.replace(RGplus,'+');
			SCcomments = SCcomments.replace(RGplus,'+');
			SCfullname = SCfullname.replace(RGplus,'+');
			SCfronter = SCfronter.replace(RGplus,'+');
			SCuser = SCuser.replace(RGplus,'+');
			SCpass = SCpass.replace(RGplus,'+');
			SClead_id = SClead_id.replace(RGplus,'+');
			SCcampaign = SCcampaign.replace(RGplus,'+');
			SCphone_login = SCphone_login.replace(RGplus,'+');
			SCoriginal_phone_login = SCoriginal_phone_login.replace(RGplus,'+');
			SCgroup = SCgroup.replace(RGplus,'+');
			SCchannel_group = SCchannel_group.replace(RGplus,'+');
			SCSQLdate = SCSQLdate.replace(RGplus,'+');
			SCuniqueid = SCuniqueid.replace(RGplus,'+');
			SCcustomer_zap_channel = SCcustomer_zap_channel.replace(RGplus,'+');
			SCserver_ip = SCserver_ip.replace(RGplus,'+');
			SCSIPexten = SCSIPexten.replace(RGplus,'+');
			SCdispo = SCdispo.replace(RGplus,'+');
			SCdialed_number = SCdialed_number.replace(RGplus,'+');
			SCdialed_label = SCdialed_label.replace(RGplus,'+');
			SCrank = SCrank.replace(RGplus,'+');
			SCowner = SCowner.replace(RGplus,'+');
			SCcamp_script = SCcamp_script.replace(RGplus,'+');
			SCin_script = SCin_script.replace(RGplus,'+');
			SCscript_width = SCscript_width.replace(RGplus,'+');
			SCscript_height = SCscript_height.replace(RGplus,'+');
			SCrecording_filename = SCrecording_filename.replace(RGplus,'+');
			SCrecording_id = SCrecording_id.replace(RGplus,'+');
			SCuser_custom_one = SCuser_custom_one.replace(RGplus,'+');
			SCuser_custom_two = SCuser_custom_two.replace(RGplus,'+');
			SCuser_custom_three = SCuser_custom_three.replace(RGplus,'+');
			SCuser_custom_four = SCuser_custom_four.replace(RGplus,'+');
			SCuser_custom_five = SCuser_custom_five.replace(RGplus,'+');
			SCpreset_number_a = SCpreset_number_a.replace(RGplus,'+');
			SCpreset_number_b = SCpreset_number_b.replace(RGplus,'+');
			SCpreset_number_c = SCpreset_number_c.replace(RGplus,'+');
			SCpreset_number_d = SCpreset_number_d.replace(RGplus,'+');
			SCpreset_number_e = SCpreset_number_e.replace(RGplus,'+');
			SCpreset_dtmf_a = SCpreset_dtmf_a.replace(RGplus,'+');
			SCpreset_dtmf_b = SCpreset_dtmf_b.replace(RGplus,'+');
			SCdid_id = SCdid_id.replace(RGplus,'+');
			SCdid_extension = SCdid_extension.replace(RGplus,'+');
			SCdid_pattern = SCdid_pattern.replace(RGplus,'+');
			SCdid_description = SCdid_description.replace(RGplus,'+');
			SCweb_vars = SCweb_vars.replace(RGplus,'+');
			}

		var RGvendor_lead_code = new RegExp("--A--vendor_lead_code--B--","g");
		var RGsource_id = new RegExp("--A--source_id--B--","g");
		var RGlist_id = new RegExp("--A--list_id--B--","g");
		var RGgmt_offset_now = new RegExp("--A--gmt_offset_now--B--","g");
		var RGcalled_since_last_reset = new RegExp("--A--called_since_last_reset--B--","g");
		var RGphone_code = new RegExp("--A--phone_code--B--","g");
		var RGphone_number = new RegExp("--A--phone_number--B--","g");
		var RGtitle = new RegExp("--A--title--B--","g");
		var RGfirst_name = new RegExp("--A--first_name--B--","g");
		var RGmiddle_initial = new RegExp("--A--middle_initial--B--","g");
		var RGlast_name = new RegExp("--A--last_name--B--","g");
		var RGaddress1 = new RegExp("--A--address1--B--","g");
		var RGaddress2 = new RegExp("--A--address2--B--","g");
		var RGaddress3 = new RegExp("--A--address3--B--","g");
		var RGcity = new RegExp("--A--city--B--","g");
		var RGstate = new RegExp("--A--state--B--","g");
		var RGprovince = new RegExp("--A--province--B--","g");
		var RGpostal_code = new RegExp("--A--postal_code--B--","g");
		var RGcountry_code = new RegExp("--A--country_code--B--","g");
		var RGgender = new RegExp("--A--gender--B--","g");
		var RGdate_of_birth = new RegExp("--A--date_of_birth--B--","g");
		var RGalt_phone = new RegExp("--A--alt_phone--B--","g");
		var RGemail = new RegExp("--A--email--B--","g");
		var RGsecurity_phrase = new RegExp("--A--security_phrase--B--","g");
		var RGcomments = new RegExp("--A--comments--B--","g");
		var RGfullname = new RegExp("--A--fullname--B--","g");
		var RGfronter = new RegExp("--A--fronter--B--","g");
		var RGuser = new RegExp("--A--user--B--","g");
		var RGpass = new RegExp("--A--pass--B--","g");
		var RGlead_id = new RegExp("--A--lead_id--B--","g");
		var RGcampaign = new RegExp("--A--campaign--B--","g");
		var RGphone_login = new RegExp("--A--phone_login--B--","g");
		var RGoriginal_phone_login = new RegExp("--A--original_phone_login--B--","g");
		var RGgroup = new RegExp("--A--group--B--","g");
		var RGchannel_group = new RegExp("--A--channel_group--B--","g");
		var RGSQLdate = new RegExp("--A--SQLdate--B--","g");
		var RGepoch = new RegExp("--A--epoch--B--","g");
		var RGuniqueid = new RegExp("--A--uniqueid--B--","g");
		var RGcustomer_zap_channel = new RegExp("--A--customer_zap_channel--B--","g");
		var RGserver_ip = new RegExp("--A--server_ip--B--","g");
		var RGSIPexten = new RegExp("--A--SIPexten--B--","g");
		var RGsession_id = new RegExp("--A--session_id--B--","g");
		var RGdispo = new RegExp("--A--dispo--B--","g");
		var RGdialed_number = new RegExp("--A--dialed_number--B--","g");
		var RGdialed_label = new RegExp("--A--dialed_label--B--","g");
		var RGrank = new RegExp("--A--rank--B--","g");
		var RGowner = new RegExp("--A--owner--B--","g");
		var RGcamp_script = new RegExp("--A--camp_script--B--","g");
		var RGin_script = new RegExp("--A--in_script--B--","g");
		var RGscript_width = new RegExp("--A--script_width--B--","g");
		var RGscript_height = new RegExp("--A--script_height--B--","g");
		var RGrecording_filename = new RegExp("--A--recording_filename--B--","g");
		var RGrecording_id = new RegExp("--A--recording_id--B--","g");
		var RGuser_custom_one = new RegExp("--A--user_custom_one--B--","g");
		var RGuser_custom_two = new RegExp("--A--user_custom_two--B--","g");
		var RGuser_custom_three = new RegExp("--A--user_custom_three--B--","g");
		var RGuser_custom_four = new RegExp("--A--user_custom_four--B--","g");
		var RGuser_custom_five = new RegExp("--A--user_custom_five--B--","g");
		var RGpreset_number_a = new RegExp("--A--preset_number_a--B--","g");
		var RGpreset_number_b = new RegExp("--A--preset_number_b--B--","g");
		var RGpreset_number_c = new RegExp("--A--preset_number_c--B--","g");
		var RGpreset_number_d = new RegExp("--A--preset_number_d--B--","g");
		var RGpreset_number_e = new RegExp("--A--preset_number_e--B--","g");
		var RGpreset_dtmf_a = new RegExp("--A--preset_dtmf_a--B--","g");
		var RGpreset_dtmf_b = new RegExp("--A--preset_dtmf_b--B--","g");
		var RGdid_id = new RegExp("--A--did_id--B--","g");
		var RGdid_extension = new RegExp("--A--did_extension--B--","g");
		var RGdid_pattern = new RegExp("--A--did_pattern--B--","g");
		var RGdid_description = new RegExp("--A--did_description--B--","g");
		var RGclosecallid = new RegExp("--A--closecallid--B--","g");
		var RGxfercallid = new RegExp("--A--xfercallid--B--","g");
		var RGagent_log_id = new RegExp("--A--agent_log_id--B--","g");
		var RGweb_vars = new RegExp("--A--web_vars--B--","g");

		encoded = encoded.replace(RGvendor_lead_code, SCvendor_lead_code);
		encoded = encoded.replace(RGsource_id, SCsource_id);
		encoded = encoded.replace(RGlist_id, SClist_id);
		encoded = encoded.replace(RGgmt_offset_now, SCgmt_offset_now);
		encoded = encoded.replace(RGcalled_since_last_reset, SCcalled_since_last_reset);
		encoded = encoded.replace(RGphone_code, SCphone_code);
		encoded = encoded.replace(RGphone_number, SCphone_number);
		encoded = encoded.replace(RGtitle, SCtitle);
		encoded = encoded.replace(RGfirst_name, SCfirst_name);
		encoded = encoded.replace(RGmiddle_initial, SCmiddle_initial);
		encoded = encoded.replace(RGlast_name, SClast_name);
		encoded = encoded.replace(RGaddress1, SCaddress1);
		encoded = encoded.replace(RGaddress2, SCaddress2);
		encoded = encoded.replace(RGaddress3, SCaddress3);
		encoded = encoded.replace(RGcity, SCcity);
		encoded = encoded.replace(RGstate, SCstate);
		encoded = encoded.replace(RGprovince, SCprovince);
		encoded = encoded.replace(RGpostal_code, SCpostal_code);
		encoded = encoded.replace(RGcountry_code, SCcountry_code);
		encoded = encoded.replace(RGgender, SCgender);
		encoded = encoded.replace(RGdate_of_birth, SCdate_of_birth);
		encoded = encoded.replace(RGalt_phone, SCalt_phone);
		encoded = encoded.replace(RGemail, SCemail);
		encoded = encoded.replace(RGsecurity_phrase, SCsecurity_phrase);
		encoded = encoded.replace(RGcomments, SCcomments);
		encoded = encoded.replace(RGfullname, SCfullname);
		encoded = encoded.replace(RGfronter, SCfronter);
		encoded = encoded.replace(RGuser, SCuser);
		encoded = encoded.replace(RGpass, SCpass);
		encoded = encoded.replace(RGlead_id, SClead_id);
		encoded = encoded.replace(RGcampaign, SCcampaign);
		encoded = encoded.replace(RGphone_login, SCphone_login);
		encoded = encoded.replace(RGoriginal_phone_login, SCoriginal_phone_login);
		encoded = encoded.replace(RGgroup, SCgroup);
		encoded = encoded.replace(RGchannel_group, SCchannel_group);
		encoded = encoded.replace(RGSQLdate, SCSQLdate);
		encoded = encoded.replace(RGepoch, SCepoch);
		encoded = encoded.replace(RGuniqueid, SCuniqueid);
		encoded = encoded.replace(RGcustomer_zap_channel, SCcustomer_zap_channel);
		encoded = encoded.replace(RGserver_ip, SCserver_ip);
		encoded = encoded.replace(RGSIPexten, SCSIPexten);
		encoded = encoded.replace(RGsession_id, SCsession_id);
		encoded = encoded.replace(RGdispo, SCdispo);
		encoded = encoded.replace(RGdialed_number, SCdialed_number);
		encoded = encoded.replace(RGdialed_label, SCdialed_label);
		encoded = encoded.replace(RGrank, SCrank);
		encoded = encoded.replace(RGowner, SCowner);
		encoded = encoded.replace(RGcamp_script, SCcamp_script);
		encoded = encoded.replace(RGin_script, SCin_script);
		encoded = encoded.replace(RGscript_width, SCscript_width);
		encoded = encoded.replace(RGscript_height, SCscript_height);
		encoded = encoded.replace(RGrecording_filename, SCrecording_filename);
		encoded = encoded.replace(RGrecording_id, SCrecording_id);
		encoded = encoded.replace(RGuser_custom_one, SCuser_custom_one);
		encoded = encoded.replace(RGuser_custom_two, SCuser_custom_two);
		encoded = encoded.replace(RGuser_custom_three, SCuser_custom_three);
		encoded = encoded.replace(RGuser_custom_four, SCuser_custom_four);
		encoded = encoded.replace(RGuser_custom_five, SCuser_custom_five);
		encoded = encoded.replace(RGpreset_number_a, SCpreset_number_a);
		encoded = encoded.replace(RGpreset_number_b, SCpreset_number_b);
		encoded = encoded.replace(RGpreset_number_c, SCpreset_number_c);
		encoded = encoded.replace(RGpreset_number_d, SCpreset_number_d);
		encoded = encoded.replace(RGpreset_number_e, SCpreset_number_e);
		encoded = encoded.replace(RGpreset_dtmf_a, SCpreset_dtmf_a);
		encoded = encoded.replace(RGpreset_dtmf_b, SCpreset_dtmf_b);
		encoded = encoded.replace(RGdid_id, SCdid_id);
		encoded = encoded.replace(RGdid_extension, SCdid_extension);
		encoded = encoded.replace(RGdid_pattern, SCdid_pattern);
		encoded = encoded.replace(RGdid_description, SCdid_description);
		encoded = encoded.replace(RGclosecallid, SCclosecallid);
		encoded = encoded.replace(RGxfercallid, SCxfercallid);
		encoded = encoded.replace(RGagent_log_id, SCagent_log_id);
		encoded = encoded.replace(RGweb_vars, SCweb_vars);
		}
	decoded=encoded; // simple no ?
	decoded = decoded.replace(RGnl, '+');
	decoded = decoded.replace(RGplus,'+');
	decoded = decoded.replace(RGtab,'+');

	//	   while (i < encoded.length) {
	//		   var ch = encoded.charAt(i);
	//		   if (ch == "%") {
	//				if (i < (encoded.length-2) 
	//						&& HEXCHAR.indexOf(encoded.charAt(i+1)) != -1 
	//						&& HEXCHAR.indexOf(encoded.charAt(i+2)) != -1 ) {
	//					decoded += unescape( encoded.substr(i,3) );
	//					i += 3;
	//				} else {
	//					alert( 'Bad escape combo near ...' + encoded.substr(i) );
	//					decoded += "%[ERR]";
	//					i++;
	//				}
	//			} else {
	//			   decoded += ch;
	//			   i++;
	//			}
	//		} // while
    //      decoded = decoded.replace(RGnl, "<br />");
	//
	return decoded;
	};


// ################################################################################
// Taken form php.net Angelos
function utf8_decode(utftext) {
        var string = "";
        var i = 0;
        var c = c1 = c2 = 0;

        while ( i < utftext.length ) {

            c = utftext.charCodeAt(i);

            if (c < 128) {
                string += String.fromCharCode(c);
                i++;
            }
            else if((c > 191) && (c < 224)) {
                c2 = utftext.charCodeAt(i+1);
                string += String.fromCharCode(((c & 31) << 6) | (c2 & 63));
                i += 2;
            }
            else {
                c2 = utftext.charCodeAt(i+1);
                c3 = utftext.charCodeAt(i+2);
                string += String.fromCharCode(((c & 15) << 12) | ((c2 & 63) << 6) | (c3 & 63));
                i += 3;
            }

        }

        return string;
    };


// ################################################################################
// phone number format
function phone_number_format(formatphone) {
	// customer_local_time, status date display 9999999999
	//	vdc_header_phone_format
    //  US_DASH 000-000-0000 - USA dash separated phone number<br />
    //  US_PARN (000)000-0000 - USA dash separated number with area code in parenthesis<br />
    //  UK_DASH 00 0000-0000 - UK dash separated phone number with space after city code<br />
    //  AU_SPAC 000 000 000 - Australia space separated phone number<br />
    //  IT_DASH 0000-000-000 - Italy dash separated phone number<br />
    //  FR_SPAC 00 00 00 00 00 - France space separated phone number<br />
	var regUS_DASHphone = new RegExp("US_DASH","g");
	var regUS_PARNphone = new RegExp("US_PARN","g");
	var regUK_DASHphone = new RegExp("UK_DASH","g");
	var regAU_SPACphone = new RegExp("AU_SPAC","g");
	var regIT_DASHphone = new RegExp("IT_DASH","g");
	var regFR_SPACphone = new RegExp("FR_SPAC","g");
	var status_display_number = formatphone;
	var dispnum = formatphone;
	if (disable_alter_custphone == 'HIDE')
		{
		var status_display_number = 'XXXXXXXXXX';
		var dispnum = 'XXXXXXXXXX';
		}
	if (vdc_header_phone_format.match(regUS_DASHphone))
		{
		var status_display_number = dispnum.substring(0,3) + '-' + dispnum.substring(3,6) + '-' + dispnum.substring(6,10);
		}
	if (vdc_header_phone_format.match(regUS_PARNphone))
		{
		var status_display_number = '(' + dispnum.substring(0,3) + ')' + dispnum.substring(3,6) + '-' + dispnum.substring(6,10);
		}
	if (vdc_header_phone_format.match(regUK_DASHphone))
		{
		var status_display_number = dispnum.substring(0,2) + ' ' + dispnum.substring(2,6) + '-' + dispnum.substring(6,10);
		}
	if (vdc_header_phone_format.match(regAU_SPACphone))
		{
		var status_display_number = dispnum.substring(0,3) + ' ' + dispnum.substring(3,6) + ' ' + dispnum.substring(6,9);
		}
	if (vdc_header_phone_format.match(regIT_DASHphone))
		{
		var status_display_number = dispnum.substring(0,4) + '-' + dispnum.substring(4,7) + '-' + dispnum.substring(8,10);
		}
	if (vdc_header_phone_format.match(regFR_SPACphone))
		{
		var status_display_number = dispnum.substring(0,2) + ' ' + dispnum.substring(2,4) + ' ' + dispnum.substring(4,6) + ' ' + dispnum.substring(6,8) + ' ' + dispnum.substring(8,10);
		}

	return status_display_number;
	};


// ################################################################################
// RefresH the agents view sidebar or xfer frame
	function refresh_agents_view(RAlocation,RAcount)
		{
		if (RAcount > 0)
			{
			if (even > 0)
				{
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
					RAview_query = "server_ip=" + server_ip + "&session_name=" + session_name + "&ACTION=AGENTSview&format=text&user=" + user + "&pass=" + pass + "&user_group=" + VU_user_group + "&conf_exten=" + session_id + "&extension=" + extension + "&protocol=" + protocol + "&stage=" + agent_status_view_time + "&campaign=" + campaign + "&comments=" + RAlocation;
					xmlhttp.open('POST', 'vdc_db_query.php'); 
					xmlhttp.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
					xmlhttp.send(RAview_query); 
					xmlhttp.onreadystatechange = function() 
						{ 
						if (xmlhttp.readyState == 4 && xmlhttp.status == 200) 
							{
							var newRAlocationHTML = xmlhttp.responseText;
						//	alert(newRAlocationHTML);

							if (RAlocation == 'AgentXferViewSelect') 
								{
                                document.getElementById(RAlocation).innerHTML = newRAlocationHTML + "\n<br /><br /><a href=\"#\" onclick=\"AgentsXferSelect('0','AgentXferViewSelect');return false;\">Close Window</a>&nbsp;";
								}
							else
								{
								document.getElementById(RAlocation).innerHTML = newRAlocationHTML + "\n";
								}
							}
						}
					delete xmlhttp;
					}
				}
			}
		}


// ################################################################################
// Grab the call in queue and bring it into the session
	function callinqueuegrab(CQauto_call_id)
		{
		if (CQauto_call_id > 0)
			{
			var move_on=1;
			if ( (AutoDialWaiting == 1) || (VD_live_customer_call==1) || (alt_dial_active==1) || (MD_channel_look==1) || (in_lead_preview_state==1) )
				{
				if ((auto_pause_precall == 'Y') && ( (agent_pause_codes_active=='Y') || (agent_pause_codes_active=='FORCE') ) && (AutoDialWaiting == 1) && (VD_live_customer_call!=1) && (alt_dial_active!=1) && (MD_channel_look!=1) && (in_lead_preview_state!=1) )
					{
					agent_log_id = AutoDial_ReSume_PauSe("VDADpause",'','','','','1','GRABCL');
					}
				else
					{
					move_on=0;
					alert_box("YOU MUST BE PAUSED TO GRAB CALLS IN QUEUE");
					}
				}
			if (move_on == 1)
				{
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
					RAview_query = "server_ip=" + server_ip + "&session_name=" + session_name + "&ACTION=CALLSINQUEUEgrab&format=text&user=" + user + "&pass=" + pass + "&conf_exten=" + session_id + "&extension=" + extension + "&protocol=" + protocol + "&campaign=" + campaign + "&stage=" + CQauto_call_id;
					xmlhttp.open('POST', 'vdc_db_query.php'); 
					xmlhttp.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
					xmlhttp.send(RAview_query); 
					xmlhttp.onreadystatechange = function() 
						{ 
						if (xmlhttp.readyState == 4 && xmlhttp.status == 200) 
							{
							var CQgrabresponse = xmlhttp.responseText;
							var regCQerror = new RegExp("ERROR","ig");
							if (CQgrabresponse.match(regCQerror))
								{
								alert_box(CQgrabresponse);
								}
							else
								{
								AutoDial_ReSume_PauSe("VDADready",'','','NO_STATUS_CHANGE');
								AutoDialWaiting=1;
								}
							}
						}
					delete xmlhttp;
					}

				}
			}
		}


// ################################################################################
// RefresH the calls in queue bottombar
	function refresh_calls_in_queue(CQcount)
		{
		if (CQcount > 0)
			{
			if (even > 0)
				{
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
					RAview_query = "server_ip=" + server_ip + "&session_name=" + session_name + "&ACTION=CALLSINQUEUEview&format=text&user=" + user + "&pass=" + pass + "&conf_exten=" + session_id + "&extension=" + extension + "&protocol=" + protocol + "&campaign=" + campaign + "&stage=<?php echo $CQwidth ?>";
					xmlhttp.open('POST', 'vdc_db_query.php'); 
					xmlhttp.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
					xmlhttp.send(RAview_query); 
					xmlhttp.onreadystatechange = function() 
						{ 
						if (xmlhttp.readyState == 4 && xmlhttp.status == 200) 
							{
						//	alert(xmlhttp.responseText);
							document.getElementById('callsinqueuelist').innerHTML = xmlhttp.responseText + "\n";
							}
						}
					delete xmlhttp;
					}

				}
			}
		}


// ################################################################################
// Open or close the callsinqueue view bottombar
	function show_calls_in_queue(CQoperation)
		{
		if (CQoperation=='SHOW')
			{
			document.getElementById("callsinqueuelink").innerHTML = "<a href=\"#\"  onclick=\"show_calls_in_queue('HIDE');\">Hide Calls In Queue</a>";
			view_calls_in_queue_active=1;
			showDiv('callsinqueuedisplay');
			}
		else
			{
			document.getElementById("callsinqueuelink").innerHTML = "<a href=\"#\"  onclick=\"show_calls_in_queue('SHOW');\">Show Calls In Queue</a>";
			view_calls_in_queue_active=0;
			hideDiv('callsinqueuedisplay');
			}
		}


// ################################################################################
// Open or close the agents view sidebar or xfer frame
	function AgentsViewOpen(AVlocation,AVoperation)
		{
		if (AVoperation=='open')
			{
			if (AVlocation=='AgentViewSpan')
				{
				document.getElementById("AgentViewLink").innerHTML = "<a href=\"#\" onclick=\"AgentsViewOpen('AgentViewSpan','close');return false;\"><img style=\"margin-bottom:-15px;\" src=\"./images/x-users-on.png\" border=\"0\" alt=\"Other Agents\" /><br /><span>Hide Agents</span></a>";
                                document.getElementById("placesDiv").style.height = '100%';
				agent_status_view_active=1;
				}
			showDiv(AVlocation);
			}
		else
			{
			if (AVlocation=='AgentViewSpan')
				{
				document.getElementById("AgentViewLink").innerHTML = "<a href=\"#\" onclick=\"AgentsViewOpen('AgentViewSpan','open');return false;\"><img style=\"margin-bottom:-15px;\" src=\"./images/x-users-on.png\" border=\"0\" alt=\"Other Agents\" /><br /><span>Show Agents</span></a>";
				agent_status_view_active=0;
                                document.getElementById("placesDiv").style.height = '180px';
				}
			hideDiv(AVlocation);
			}
		}


// ################################################################################
// Open or close the webphone view sidebar
	function webphoneOpen(WVlocation,WVoperation)
		{
		if (WVoperation=='open')
			{
			document.getElementById("webphoneLink").innerHTML = " &nbsp; <a href=\"#\" onclick=\"webphoneOpen('webphoneSpan','close');return false;\">WebPhone View -</a>";
			showDiv(WVlocation);
			}
		else
			{
			document.getElementById("webphoneLink").innerHTML = " &nbsp; <a href=\"#\" onclick=\"webphoneOpen('webphoneSpan','open');return false;\">WebPhone View +</a>";
			hideDiv(WVlocation);
			}
		}


// ################################################################################
// Populate the number to dial field with the selected user ID
	function AgentsXferSelect(AXuser,AXlocation)
		{
		xfer_select_agents_active=0;
		document.getElementById('AgentXferViewSelect').innerHTML = '';
		hideDiv('AgentXferViewSpan');
		hideDiv(AXlocation);
		document.vicidial_form.xfernumber.value = AXuser;
		}


// ################################################################################
// OnChange function for transfer group select list
	function XferAgentSelectLink()
		{
		var XfeRSelecT = document.getElementById("XfeRGrouP");
		var XScheck = XfeRSelecT.value
		if (XScheck.match(/AGENTDIRECT/))
			{
			showDiv('agentdirectlink');
			}
		else
			{
			hideDiv('agentdirectlink');
			}
		}


// ################################################################################
// function for number to dial for AGENTDIRECT in-group transfers
	function XferAgentSelectLaunch()
		{
		var XfeRSelecT = document.getElementById("XfeRGrouP");
		var XScheck = XfeRSelecT.value
		if (XScheck.match(/AGENTDIRECT/))
			{
			showDiv('AgentXferViewSpan');
			AgentsViewOpen('AgentXferViewSelect','open');
			refresh_agents_view('AgentXferViewSelect',agent_status_view)
			xfer_select_agents_active=1;
			document.vicidial_form.xfername.value='';
			}
		}


// ################################################################################
// Call ReQueue call back to AGENTDIRECT queue launch
	function call_requeue_launch()
		{
		document.vicidial_form.xfernumber.value = user;

		// Build transfer pull-down list
		var loop_ct = 0;
		var live_XfeR_HTML = '';
		var XfeR_SelecT = '';
		while (loop_ct < XFgroupCOUNT)
			{
			if (VARxfergroups[loop_ct] == 'AGENTDIRECT')
				{XfeR_SelecT = 'selected ';}
			else {XfeR_SelecT = '';}
			live_XfeR_HTML = live_XfeR_HTML + "<option " + XfeR_SelecT + "value=\"" + VARxfergroups[loop_ct] + "\">" + VARxfergroups[loop_ct] + " - " + VARxfergroupsnames[loop_ct] + "</option>\n";
			loop_ct++;
			}
        document.getElementById("XfeRGrouPLisT").innerHTML = "<select size=\"1\" name=\"XfeRGrouP\" class=\"cust_form\" id=\"XfeRGrouP\" onchange=\"XferAgentSelectLink();return false;\">" + live_XfeR_HTML + "</select>";

		mainxfer_send_redirect('XfeRLOCAL',lastcustchannel,lastcustserverip,'','NO');

		document.vicidial_form.DispoSelection.value = 'RQXFER';
		DispoSelect_submit();

		AutoDial_ReSume_PauSe("VDADpause",'','','',"REQUEUE",'1','RQUEUE');

//		PauseCodeSelect_submit("RQUEUE");
		}


// ################################################################################
// View Customer lead information
	function VieWLeaDInfO(VLI_lead_id,VLI_cb_id)
		{
		showDiv('LeaDInfOBox');

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
			RAview_query = "server_ip=" + server_ip + "&session_name=" + session_name + "&ACTION=LEADINFOview&format=text&user=" + user + "&pass=" + pass + "&conf_exten=" + session_id + "&extension=" + extension + "&protocol=" + protocol + "&lead_id=" + VLI_lead_id + "&campaign=" + campaign + "&callback_id=" + VLI_cb_id + "&stage=<?php echo $HCwidth ?>";
			xmlhttp.open('POST', 'vdc_db_query.php'); 
			xmlhttp.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
			xmlhttp.send(RAview_query); 
			xmlhttp.onreadystatechange = function() 
				{ 
				if (xmlhttp.readyState == 4 && xmlhttp.status == 200) 
					{
				//	alert(xmlhttp.responseText);
					document.getElementById('LeaDInfOSpan').innerHTML = xmlhttp.responseText + "\n";
					}
				}
			delete xmlhttp;
			}
		}


// ################################################################################
// Refresh the call log display
	function VieWCalLLoG(logdate,formdate)
		{
		var move_on=1;
		if ( (AutoDialWaiting == 1) || (VD_live_customer_call==1) || (alt_dial_active==1) || (MD_channel_look==1) || (in_lead_preview_state==1) )
			{
			if ((auto_pause_precall == 'Y') && ( (agent_pause_codes_active=='Y') || (agent_pause_codes_active=='FORCE') ) && (AutoDialWaiting == 1) && (VD_live_customer_call!=1) && (alt_dial_active!=1) && (MD_channel_look!=1) && (in_lead_preview_state!=1) )
				{
				agent_log_id = AutoDial_ReSume_PauSe("VDADpause",'','','','','1',auto_pause_precall_code);
				}
			else
				{
				move_on=0;
				alert_box("YOU MUST BE PAUSED TO VIEW YOUR CALL LOG");
				}
			}
		if (move_on == 1)
			{
			showDiv('CalLLoGDisplaYBox');

			if (formdate=='form')
				{logdate = document.vicidial_form.calllogdate.value;}

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
				RAview_query = "server_ip=" + server_ip + "&session_name=" + session_name + "&ACTION=CALLLOGview&format=text&user=" + user + "&pass=" + pass + "&conf_exten=" + session_id + "&extension=" + extension + "&protocol=" + protocol + "&date=" + logdate + "&campaign=" + campaign + "&stage=<?php echo $HCwidth ?>";
				xmlhttp.open('POST', 'vdc_db_query.php'); 
				xmlhttp.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
				xmlhttp.send(RAview_query); 
				xmlhttp.onreadystatechange = function() 
					{ 
					if (xmlhttp.readyState == 4 && xmlhttp.status == 200) 
						{
					//	alert(xmlhttp.responseText);
						document.getElementById('CallLogSpan').innerHTML = xmlhttp.responseText + "\n";
						}
					}
				delete xmlhttp;
				}
			}
		}



// ################################################################################
// Gather and display lead search data
	function LeadSearchSubmit()
		{
		if ( (AutoDialWaiting == 1) || (VD_live_customer_call==1) || (alt_dial_active==1) || (MD_channel_look==1) || (in_lead_preview_state==1) )
			{
			alert_box("YOU MUST BE PAUSED TO SEARCH FOR A LEAD");
			}
		else
			{
			showDiv('SearcHResultSDisplaYBox');

			document.getElementById('SearcHResultSSpan').innerHTML = "Searching...\n";

			var phone_search_fields = '';
			if (document.vicidial_form.search_main_phone.checked==true)
				{phone_search_fields = phone_search_fields + "MAIN_";}
			if (document.vicidial_form.search_alt_phone.checked==true)
				{phone_search_fields = phone_search_fields + "ALT_";}
			if (document.vicidial_form.search_addr3_phone.checked==true)
				{phone_search_fields = phone_search_fields + "ADDR3_";}

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
				LSview_query = "server_ip=" + server_ip + "&session_name=" + session_name + "&ACTION=SEARCHRESULTSview&format=text&user=" + user + "&pass=" + pass + "&conf_exten=" + session_id + "&extension=" + extension + "&protocol=" + protocol + "&phone_number=" + document.vicidial_form.search_phone_number.value + "&lead_id=" + document.vicidial_form.search_lead_id.value + "&vendor_lead_code=" + document.vicidial_form.search_vendor_lead_code.value + "&first_name=" + document.vicidial_form.search_first_name.value + "&last_name=" + document.vicidial_form.search_last_name.value + "&city=" + document.vicidial_form.search_city.value + "&state=" + document.vicidial_form.search_state.value + "&postal_code=" + document.vicidial_form.search_postal_code.value + "&search=" + phone_search_fields + "&campaign=" + campaign + "&stage=<?php echo $HCwidth ?>";
				xmlhttp.open('POST', 'vdc_db_query.php'); 
				xmlhttp.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
				xmlhttp.send(LSview_query); 
				xmlhttp.onreadystatechange = function() 
					{ 
					if (xmlhttp.readyState == 4 && xmlhttp.status == 200) 
						{
					//	alert(xmlhttp.responseText);
						document.getElementById('SearcHResultSSpan').innerHTML = xmlhttp.responseText + "\n";
						}
					}
				delete xmlhttp;
				}
			}
		}


// ################################################################################
// Reset lead search form
	function LeadSearchReset()
		{
		document.vicidial_form.search_phone_number.value='';
		document.vicidial_form.search_lead_id.value='';
		document.vicidial_form.search_vendor_lead_code.value='';
		document.vicidial_form.search_first_name.value='';
		document.vicidial_form.search_last_name.value='';
		document.vicidial_form.search_city.value='';
		document.vicidial_form.search_state.value='';
		document.vicidial_form.search_postal_code.value='';
		}


// ################################################################################
// Hide manual dial form
	function ManualDialHide()
		{
		if (auto_resume_precall == 'Y')
			{
			AutoDial_ReSume_PauSe("VDADready");
			}
		hideDiv('NeWManuaLDiaLBox');
		document.vicidial_form.MDPhonENumbeR.value = '';
		document.vicidial_form.MDDiaLOverridE.value = '';
		document.vicidial_form.MDLeadID.value = '';
		document.vicidial_form.MDType.value = '';
		}


// ################################################################################
// Refresh the lead notes display
	function VieWNotesLoG(logframe)
		{
		showDiv('CalLNotesDisplaYBox');

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
			RAview_query = "server_ip=" + server_ip + "&session_name=" + session_name + "&ACTION=LEADINFOview&search=logfirst&format=text&user=" + user + "&pass=" + pass + "&conf_exten=" + session_id + "&extension=" + extension + "&protocol=" + protocol + "&lead_id=" + document.vicidial_form.lead_id.value + "&campaign=" + campaign + "&stage=<?php echo $HCwidth ?>";
			xmlhttp.open('POST', 'vdc_db_query.php'); 
			xmlhttp.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
			xmlhttp.send(RAview_query); 
			xmlhttp.onreadystatechange = function() 
				{ 
				if (xmlhttp.readyState == 4 && xmlhttp.status == 200) 
					{
				//	alert(xmlhttp.responseText);
					document.getElementById('CallNotesSpan').innerHTML = xmlhttp.responseText + "\n";
					}
				}
			delete xmlhttp;
			}
		}



// ################################################################################
// Run the logging process for customer 3way hangup
	function customer_3way_hangup_process(temp_hungup_time,temp_xfer_call_seconds)
		{
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
			CTHPview_query = "server_ip=" + server_ip + "&session_name=" + session_name + "&ACTION=customer_3way_hangup_process&format=text&user=" + user + "&pass=" + pass + "&conf_exten=" + session_id + "&lead_id=" + document.vicidial_form.lead_id.value + "&campaign=" + campaign + "&status=" + temp_hungup_time + "&stage=" + temp_xfer_call_seconds;
			xmlhttp.open('POST', 'vdc_db_query.php'); 
			xmlhttp.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
			xmlhttp.send(CTHPview_query); 
			xmlhttp.onreadystatechange = function() 
				{ 
				if (xmlhttp.readyState == 4 && xmlhttp.status == 200) 
					{
				//	alert(xmlhttp.responseText);
					document.getElementById("debugbottomspan").innerHTML = "CUSTOMER 3WAY HANGUP " + xmlhttp.responseText;
					}
				}
			delete xmlhttp;
			}
		}


// ################################################################################
// Refresh the FORM content
	function FormContentsLoad()
		{
		var form_list_id = document.vicidial_form.list_id.value;
		var form_entry_list_id = document.vicidial_form.entry_list_id.value;
		if (form_entry_list_id.length > 2)
			{form_list_id = form_entry_list_id}
		document.getElementById('vcFormIFrame').src='./vdc_form_display.php?lead_id=' + document.vicidial_form.lead_id.value + '&list_id=' + form_list_id + '&user=' + user + '&pass=' + pass + '&campaign=' + campaign + '&server_ip=' + server_ip + '&session_id=' + '&uniqueid=' + document.vicidial_form.uniqueid.value + '&stage=DISPLAY' + "&campaign=" + campaign + "&phone_login=" + phone_login + "&original_phone_login=" + original_phone_login +"&phone_pass=" + phone_pass + "&fronter=" + fronter + "&closer=" + user + "&group=" + group + "&channel_group=" + group + "&SQLdate=" + SQLdate + "&epoch=" + UnixTime + "&uniqueid=" + document.vicidial_form.uniqueid.value + "&customer_zap_channel=" + lastcustchannel + "&customer_server_ip=" + lastcustserverip +"&server_ip=" + server_ip + "&SIPexten=" + extension + "&session_id=" + session_id + "&phone=" + document.vicidial_form.phone_number.value + "&parked_by=" + document.vicidial_form.lead_id.value +"&dispo=" + LeaDDispO + '' +"&dialed_number=" + dialed_number + '' +"&dialed_label=" + dialed_label + '' +"&camp_script=" + campaign_script + '' +"&in_script=" + CalL_ScripT_id + '' +"&script_width=" + script_width + '' +"&script_height=" + script_height + '' +"&fullname=" + LOGfullname + '' +"&recording_filename=" + recording_filename + '' +"&recording_id=" + recording_id + '' +"&user_custom_one=" + VU_custom_one + '' +"&user_custom_two=" + VU_custom_two + '' +"&user_custom_three=" + VU_custom_three + '' +"&user_custom_four=" + VU_custom_four + '' +"&user_custom_five=" + VU_custom_five + '' +"&preset_number_a=" + CalL_XC_a_NuMber + '' +"&preset_number_b=" + CalL_XC_b_NuMber + '' +"&preset_number_c=" + CalL_XC_c_NuMber + '' +"&preset_number_d=" + CalL_XC_d_NuMber + '' +"&preset_number_e=" + CalL_XC_e_NuMber + '' +"&preset_dtmf_a=" + CalL_XC_a_Dtmf + '' +"&preset_dtmf_b=" + CalL_XC_b_Dtmf + '' +"&did_id=" + did_id + '' +"&did_extension=" + did_extension + '' +"&did_pattern=" + did_pattern + '' +"&did_description=" + did_description + '' +"&closecallid=" + closecallid + '' +"&xfercallid=" + xfercallid + '' +"&agent_log_id=" + agent_log_id + '' +"&web_vars=" + LIVE_web_vars + '';
                //document.getElementById('FormContents').innerHTML = '<iframe src=\"./vdc_form_display.php?lead_id=' + document.vicidial_form.lead_id.value + '&list_id=' + form_list_id + '&user=' + user + '&pass=' + pass + '&campaign=' + campaign + '&server_ip=' + server_ip + '&session_id=' + '&uniqueid=' + document.vicidial_form.uniqueid.value + '&stage=DISPLAY' + "&campaign=" + campaign + "&phone_login=" + phone_login + "&original_phone_login=" + original_phone_login +"&phone_pass=" + phone_pass + "&fronter=" + fronter + "&closer=" + user + "&group=" + group + "&channel_group=" + group + "&SQLdate=" + SQLdate + "&epoch=" + UnixTime + "&uniqueid=" + document.vicidial_form.uniqueid.value + "&customer_zap_channel=" + lastcustchannel + "&customer_server_ip=" + lastcustserverip +"&server_ip=" + server_ip + "&SIPexten=" + extension + "&session_id=" + session_id + "&phone=" + document.vicidial_form.phone_number.value + "&parked_by=" + document.vicidial_form.lead_id.value +"&dispo=" + LeaDDispO + '' +"&dialed_number=" + dialed_number + '' +"&dialed_label=" + dialed_label + '' +"&camp_script=" + campaign_script + '' +"&in_script=" + CalL_ScripT_id + '' +"&script_width=" + script_width + '' +"&script_height=" + script_height + '' +"&fullname=" + LOGfullname + '' +"&recording_filename=" + recording_filename + '' +"&recording_id=" + recording_id + '' +"&user_custom_one=" + VU_custom_one + '' +"&user_custom_two=" + VU_custom_two + '' +"&user_custom_three=" + VU_custom_three + '' +"&user_custom_four=" + VU_custom_four + '' +"&user_custom_five=" + VU_custom_five + '' +"&preset_number_a=" + CalL_XC_a_NuMber + '' +"&preset_number_b=" + CalL_XC_b_NuMber + '' +"&preset_number_c=" + CalL_XC_c_NuMber + '' +"&preset_number_d=" + CalL_XC_d_NuMber + '' +"&preset_number_e=" + CalL_XC_e_NuMber + '' +"&preset_dtmf_a=" + CalL_XC_a_Dtmf + '' +"&preset_dtmf_b=" + CalL_XC_b_Dtmf + '' +"&did_id=" + did_id + '' +"&did_extension=" + did_extension + '' +"&did_pattern=" + did_pattern + '' +"&did_description=" + did_description + '' +"&closecallid=" + closecallid + '' +"&xfercallid=" + xfercallid + '' +"&agent_log_id=" + agent_log_id + '' +"&web_vars=" + LIVE_web_vars + ' \"> </iframe>';
		form_list_id = '';
		form_entry_list_id = '';
		}

// ################################################################################
// Move the Dispo frame out of the way and change the link to maximize
	function DispoMinimize()
		{
		showDiv('DispoButtonHideA');
		showDiv('DispoButtonHideB');
		showDiv('DispoButtonHideC');
		document.getElementById("DispoSelectBox").style.top = '340px';
		document.getElementById("DispoSelectMaxMin").innerHTML = "<a href=\"#\" onclick=\"DispoMaximize()\"> maximize </a>";
		}


// ################################################################################
// Move the Dispo frame to the top and change the link to minimize
	function DispoMaximize()
		{
		document.getElementById("DispoSelectBox").style.top = '1px';
		document.getElementById("DispoSelectMaxMin").innerHTML = "<a href=\"#\" onclick=\"DispoMinimize()\"> minimize </a>";
		hideDiv('DispoButtonHideA');
		hideDiv('DispoButtonHideB');
		hideDiv('DispoButtonHideC');
		}


// ################################################################################
// Show the groups selection span
	function OpeNGrouPSelectioN()
		{
		var move_on=1;
		if ( (AutoDialWaiting == 1) || (VD_live_customer_call==1) || (alt_dial_active==1) || (MD_channel_look==1) || (in_lead_preview_state==1) )
			{
			if ((auto_pause_precall == 'Y') && ( (agent_pause_codes_active=='Y') || (agent_pause_codes_active=='FORCE') ) && (AutoDialWaiting == 1) && (VD_live_customer_call!=1) && (alt_dial_active!=1) && (MD_channel_look!=1) && (in_lead_preview_state!=1) )
				{
				agent_log_id = AutoDial_ReSume_PauSe("VDADpause",'','','','','1',auto_pause_precall_code);
				}
			else
				{
				move_on=0;
				alert_box("YOU MUST BE PAUSED TO CHANGE GROUPS");
				}
			}
		if (move_on == 1)
			{
			if (manager_ingroups_set > 0)
				{
				alert_box("Manager " + external_igb_set_name + " has selected your in-group choices");
				}
			else
				{
				HidEGenDerPulldown();
				showDiv('CloserSelectBox')
				}
			}
		}


// ################################################################################
// Show the territories selection span
	function OpeNTerritorYSelectioN()
		{
		var move_on=1;
		if ( (AutoDialWaiting == 1) || (VD_live_customer_call==1) || (alt_dial_active==1) || (MD_channel_look==1) || (in_lead_preview_state==1) )
			{
			if ((auto_pause_precall == 'Y') && ( (agent_pause_codes_active=='Y') || (agent_pause_codes_active=='FORCE') ) && (AutoDialWaiting == 1) && (VD_live_customer_call!=1) && (alt_dial_active!=1) && (MD_channel_look!=1) && (in_lead_preview_state!=1) )
				{
				agent_log_id = AutoDial_ReSume_PauSe("VDADpause",'','','','','1',auto_pause_precall_code);
				}
			else
				{
				move_on=0;
				alert_box("YOU MUST BE PAUSED TO CHANGE TERRITORIES");
				}
			}
		if (move_on == 1)
			{
			showDiv('TerritorySelectBox')
			}
		}


// ################################################################################
// Hide the CBcommentsBox span upon click
	function CBcommentsBoxhide()
		{
		CBentry_time = '';
		CBcallback_time = '';
		CBuser = '';
		CBcomments = '';
		document.getElementById("CBcommentsBoxA").innerHTML = "";
		document.getElementById("CBcommentsBoxB").innerHTML = "";
		document.getElementById("CBcommentsBoxC").innerHTML = "";
		document.getElementById("CBcommentsBoxD").innerHTML = "";
		hideDiv('CBcommentsBox');
		}


// ################################################################################
// Hide the EAcommentsBox span upon click
	function EAcommentsBoxhide(minimizetask)
		{
		hideDiv('EAcommentsBox');
		if (minimizetask=='YES')
			{showDiv('EAcommentsMinBox');}
		else
			{hideDiv('EAcommentsMinBox');}
		}


// ################################################################################
// Show the EAcommentsBox span upon click
	function EAcommentsBoxshow()
		{
		showDiv('EAcommentsBox');
		hideDiv('EAcommentsMinBox');
		}


// ################################################################################
// Populating the date field in the callback frame prior to submission
	function CB_date_pick(taskdate)
		{
		document.vicidial_form.CallBackDatESelectioN.value = taskdate;
		document.getElementById("CallBackDatEPrinT").innerHTML = taskdate;
		}


// ################################################################################
// Submitting the callback date and time to the system
	function CallBackDatE_submit()
		{
		CallBackDatEForM = document.vicidial_form.CallBackDatESelectioN.value;
		CallBackCommenTs = document.vicidial_form.CallBackCommenTsField.value;
		if (CallBackDatEForM.length < 2)
			{alert_box("You must choose a date");}
		else
			{

	<?php
	if ($useIE > 0)
	{
	?>

			var CallBackTimEHouRFORM = document.getElementById('CBT_hour');
			var CallBackTimEHouR = CallBackTimEHouRFORM[CallBackTimEHouRFORM.selectedIndex].text;
		//	var CallBackTimEHouRIDX = CallBackTimEHouRFORM.value;

			var CallBackTimEMinuteSFORM = document.getElementById('CBT_minute');
			var CallBackTimEMinuteS = CallBackTimEMinuteSFORM[CallBackTimEMinuteSFORM.selectedIndex].text;
		//	var CallBackTimEMinuteSIDX = CallBackTimEMinuteSFORM.value;

			var CallBackTimEAmpMFORM = document.getElementById('CBT_ampm');
			var CallBackTimEAmpM = CallBackTimEAmpMFORM[CallBackTimEAmpMFORM.selectedIndex].text;
		//	var CallBackTimEAmpMIDX = CallBackTimEAmpMFORM.value;

			CallBackLeadStatus = document.vicidial_form.DispoSelection.value;

		//	alert (CallBackTimEHouR + "|" + CallBackTimEHouRFORM + "|" + CallBackTimEHouRIDX + "|");
		//	alert (CallBackTimEMinuteS + "|" + CallBackTimEMinuteSFORM + "|" + CallBackTimEMinuteSIDX + "|");
		//	alert (CallBackTimEAmpM + "|" + CallBackTimEAmpMFORM + "|" + CallBackTimEAmpMIDX + "|");

			CallBackTimEHouRFORM.selectedIndex = '0';
			CallBackTimEMinuteSFORM.selectedIndex = '0';
			CallBackTimEAmpMFORM.selectedIndex = '1';
	<?php
	}
	else
	{
	?>
			CallBackTimEHouR = document.vicidial_form.CBT_hour.value;
			CallBackTimEMinuteS = document.vicidial_form.CBT_minute.value;
			CallBackTimEAmpM = document.vicidial_form.CBT_ampm.value;
			CallBackLeadStatus = document.vicidial_form.DispoSelection.value;

			document.vicidial_form.CBT_hour.value = '01';
			document.vicidial_form.CBT_minute.value = '00';
			document.vicidial_form.CBT_ampm.value = 'PM';

	<?php
	}
	?>
			if (CallBackTimEHouR == '12')
				{
				if (CallBackTimEAmpM == 'AM')
					{
					CallBackTimEHouR = '00';
					}
				}
			else
				{
				if (CallBackTimEAmpM == 'PM')
					{
					CallBackTimEHouR = CallBackTimEHouR * 1;
					CallBackTimEHouR = (CallBackTimEHouR + 12);
					}
				}
			CallBackDatETimE = CallBackDatEForM + " " + CallBackTimEHouR + ":" + CallBackTimEMinuteS + ":00";

			if (document.vicidial_form.CallBackOnlyMe.checked==true)
				{
				CallBackrecipient = 'USERONLY';
				}
			else
				{
				CallBackrecipient = 'ANYONE';
				}
			document.getElementById("CallBackDatEPrinT").innerHTML = "Select a Date Below";
			document.vicidial_form.CallBackOnlyMe.checked=false;
			if (my_callback_option == 'CHECKED')
				{document.vicidial_form.CallBackOnlyMe.checked=true;}
			document.vicidial_form.CallBackDatESelectioN.value = '';
			document.vicidial_form.CallBackCommenTsField.value = '';

		//	alert(CallBackDatETimE + "|" + CallBackCommenTs);
			
			document.vicidial_form.DispoSelection.value = 'CBHOLD';
			hideDiv('CallBackSelectBox');
			DispoSelect_submit();
			}
		}


// ################################################################################
// Finish the wrapup timer early
	function TimerActionRun(taskaction,taskdialalert)
		{
		var next_action=0;
		if (taskaction == 'DiaLAlerT')
			{
            document.getElementById("TimerContentSpan").innerHTML = "<b>DIAL ALERT:<br /><br />" + taskdialalert.replace("\n","<br />") + "</b>";

			showDiv('TimerSpan');
			}
		else
			{
			if ( (timer_action_message.length > 0) || (timer_action == 'MESSAGE_ONLY') )
				{
                document.getElementById("TimerContentSpan").innerHTML = "<b>TIMER NOTIFICATION: " + timer_action_seconds + " seconds<br /><br />" + timer_action_message + "</b>";

				showDiv('TimerSpan');
				}

			if (timer_action == 'WEBFORM')
				{
				WebFormRefresH('NO','YES');
				window.open(TEMP_VDIC_web_form_address, web_form_target, 'toolbar=1,scrollbars=1,location=1,statusbar=1,menubar=1,resizable=1,width=640,height=450');
				}
			if (timer_action == 'WEBFORM2')
				{
				WebFormTwoRefresH('NO','YES');
				window.open(TEMP_VDIC_web_form_address_two, web_form_target, 'toolbar=1,scrollbars=1,location=1,statusbar=1,menubar=1,resizable=1,width=640,height=450');
				}
			if (timer_action == 'D1_DIAL')
				{
				DtMf_PreSet_a_DiaL();
				}
			if (timer_action == 'D2_DIAL')
				{
				DtMf_PreSet_b_DiaL();
				}
			if (timer_action == 'D3_DIAL')
				{
				DtMf_PreSet_c_DiaL();
				}
			if (timer_action == 'D4_DIAL')
				{
				DtMf_PreSet_d_DiaL();
				}
			if (timer_action == 'D5_DIAL')
				{
				DtMf_PreSet_e_DiaL();
				}
			if ( (timer_action == 'HANGUP') && (VD_live_customer_call==1) )
				{
				hangup_timer_xfer();
				}
			if ( (timer_action == 'EXTENSION') && (VD_live_customer_call==1) && (timer_action_destination.length > 0) )
				{
				extension_timer_xfer();
				}
			if ( (timer_action == 'CALLMENU') && (VD_live_customer_call==1) && (timer_action_destination.length > 0) )
				{
				callmenu_timer_xfer();
				}
			if ( (timer_action == 'IN_GROUP') && (VD_live_customer_call==1) && (timer_action_destination.length > 0) )
				{
				ingroup_timer_xfer();
				}
			if (timer_action_destination.length > 0)
				{
				var regNS = new RegExp("nextstep---","ig");
				if (timer_action_destination.match(regNS))
					{
					next_action=1;
					timer_action = 'NONE';
					var next_action_array=timer_action_destination.split("nextstep---");
					var next_action_details_array=next_action_array[1].split("--");
					timer_action = next_action_details_array[0];
					timer_action_seconds = parseInt(next_action_details_array[1]);
					timer_action_seconds = (timer_action_seconds + VD_live_call_secondS);
					timer_action_destination = next_action_details_array[2];
					timer_action_message = next_action_details_array[3];
				//	alert("NEXT: " + timer_action + '|' + timer_action_message + '|' + timer_action_seconds + '|' + timer_action_destination + '|');
					}
				}
			}

		if (next_action < 1)
			{timer_action = 'NONE';}	
		}


// ################################################################################
// Finish the wrapup timer early
	function WrapupFinish()
		{
		wrapup_counter=999;
		}


// ################################################################################
// GLOBAL FUNCTIONS
	function begin_all_refresh()
		{
		<?php if ( ($HK_statuses_camp > 0) && ( ($user_level>=$HKuser_level) or ($VU_hotkeys_active > 0) ) ) {echo "document.onkeypress = hotkeypress;\n";} ?>
		all_refresh();
                document.getElementById("MainStatuSSpan").innerHTML = document.getElementById("MainStatuSSpanDefault").innerHTML
		}
	function start_all_refresh()
		{
		if (VICIDiaL_closer_login_checked==0)
			{
			hideDiv('NothingBox');
			hideDiv('AlertBox');
			hideDiv('NothingBox2');
			hideDiv('CBcommentsBox');
			hideDiv('EAcommentsBox');
			hideDiv('EAcommentsMinBox');
			hideDiv('HotKeyActionBox');
			hideDiv('HotKeyEntriesBox');
			hideDiv('MainPanel');
			hideDiv('ScriptPanel');
			hideDiv('ScriptRefresH');
			hideDiv('FormPanel');
			hideDiv('FormRefresH');
                        hideDiv('ActionPanel');
			hideDiv('DispoSelectBox');
			hideDiv('LogouTBox');
			hideDiv('AgenTDisablEBoX');
			hideDiv('SysteMDisablEBoX');
			hideDiv('CustomerGoneBox');
			hideDiv('NoneInSessionBox');
			hideDiv('WrapupBox');
			hideDiv('TransferMain');
			hideDiv('CallBackSelectBox');
			hideDiv('DispoButtonHideA');
			hideDiv('DispoButtonHideB');
			hideDiv('DispoButtonHideC');
			hideDiv('CallBacKsLisTBox');
			hideDiv('NeWManuaLDiaLBox');
			hideDiv('PauseCodeSelectBox');
			hideDiv('PresetsSelectBox');
			hideDiv('GroupAliasSelectBox');
			hideDiv('AgentViewSpan');
			hideDiv('AgentXferViewSpan');
			hideDiv('TimerSpan');
			hideDiv('CalLLoGDisplaYBox');
			hideDiv('CalLNotesDisplaYBox');
			hideDiv('SearcHForMDisplaYBox');
			hideDiv('SearcHResultSDisplaYBox');
			hideDiv('LeaDInfOBox');
			hideDiv('agentdirectlink');
			hideDiv('blind_monitor_notice_span');
                        hideDiv('blind_monitor_alert_span');
			hideDiv('post_phone_time_diff_span');
			hideDiv('ivrParkControl');
                        hideDivDisplay("x-transfer-active");
			if (is_webphone!='Y')
				{hideDiv('webphoneSpan');}
			if (view_calls_in_queue_launch != '1')
				{hideDiv('callsinqueuedisplay');}
			if (agentonly_callbacks != '1')
				{hideDiv('CallbacksButtons');}
			if (allow_alerts < 1)
				{hideDiv('AgentAlertSpan');}
		//	if ( (agentcall_manual != '1') && (starting_dial_level > 0) )
			if (agentcall_manual != '1')
				{hideDiv('ManuaLDiaLButtons');}
			if (agent_call_log_view != '1')
				{
				hideDiv('CallNotesButtons');
				hideDiv('CallLogButtons');
				}
			if (callholdstatus != '1')
				{hideDiv('AgentStatusCalls');}
			if (agentcallsstatus != '1')
				{hideDiv('AgentStatusSpan');}
			if ( ( (auto_dial_level > 0) && (dial_method != "INBOUND_MAN") ) || (manual_dial_preview < 1) )
				{clearDiv('DiaLLeaDPrevieW');}
			if (alt_phone_dialing != 1)
				{clearDiv('DiaLDiaLAltPhonE');}
			if (volumecontrol_active != '1')
				{hideDiv('VolumeControlSpan');}
			if (DefaulTAlTDiaL == '1')
				{document.vicidial_form.DiaLAltPhonE.checked=true;}
			if (agent_status_view != '1')
				{//document.getElementById("AgentViewLink").innerHTML = "";
                                    hideDiv('AgentViewLink');
                                }
			if (dispo_check_all_pause == '1')
				{document.vicidial_form.DispoSelectStop.checked=true;}
			if (agent_xfer_consultative < 1)
				{hideDiv('consultative_checkbox');}
			if (agent_xfer_dial_override < 1)
				{hideDiv('dialoverride_checkbox');}
			if (agent_xfer_vm_transfer < 1)
				{hideDiv('DialBlindVMail');}
			if (agent_xfer_blind_transfer < 1)
				{hideDiv('DialBlindTransfer');}
			if (agent_xfer_dial_with_customer < 1)
				{hideDiv('DialWithCustomer');}
			if (agent_xfer_park_customer_dial < 1)
				{hideDiv('ParkCustomerDial');}
			if (AllowManualQueueCallsChoice == '1')
                {document.getElementById("ManualQueueChoice").innerHTML = "<a href=\"#\" onclick=\"ManualQueueChoiceChange('1');return false;\">Manual Queue is Off</a><br />";}

			document.vicidial_form.LeadLookuP.checked=true;

			if ( (agent_pause_codes_active=='Y') || (agent_pause_codes_active=='FORCE') )
				{
				document.getElementById("PauseCodeLinkSpan").innerHTML = "<a href=\"#\" onclick=\"PauseCodeSelectContent_create();return false;\"><img  style=\"margin-bottom:-15px;\" src=\"./images/x-pause.png\" border=\"0\" alt=\"Pause Code\" /><br /><span>Pause Codes</span></a>";
				}
			if (VICIDiaL_allow_closers < 1)
				{
				document.getElementById("LocalCloser").style.visibility = 'hidden';
				}
			document.getElementById("sessionIDspan").innerHTML = session_id;
			if ( (LIVE_campaign_recording == 'NEVER') || (LIVE_campaign_recording == 'ALLFORCE') )
				{
                document.getElementById("RecorDControl").innerHTML = "<a href=\"#\" style=\"opacity:.15;\" disabled><img class=\"disabled_img\" src=\"./images/x-record.png\" border=\"0\" alt=\"Start Recording\" /><br /><span id=\"x-recording\">Record</span></a>";
				}
			if (INgroupCOUNT > 0)
				{
				if (VU_closer_default_blended == 1)
					{document.vicidial_form.CloserSelectBlended.checked=true}
				CloserSelectContent_create();
				showDiv('CloserSelectBox');
				var CloserSelecting = 1;
				CloserSelectContent_create();
				}
			else
				{
				hideDiv('CloserSelectBox');
				MainPanelToFront();
				var CloserSelecting = 0;
				if (dial_method == "INBOUND_MAN")
					{
					dial_method = "MANUAL";
					auto_dial_level=0;
					starting_dial_level=0;
					document.getElementById("DiaLControl").innerHTML = DiaLControl_manual_HTML;
                                        document.getElementById("ManualDiaLControl").innerHTML = DiaLControl_manual_HTML_OFF;
					}
				}
			if (territoryCOUNT > 0)
				{
				showDiv('TerritorySelectBox');
				var TerritorySelecting = 1;
				TerritorySelectContent_create();
				}
			else
				{
				hideDiv('TerritorySelectBox');
				MainPanelToFront();
				var TerritorySelecting = 0;
				}
			if ( (VtigeRLogiNScripT == 'Y') && (VtigeREnableD > 0) )
				{
				document.getElementById("ScriptContents").innerHTML = "<iframe src=\"" + VtigeRurl + "/index.php?module=Users&action=Authenticate&return_module=Users&return_action=Login&user_name=" + user + "&user_password=" + pass + "&login_theme=softed&login_language=en_us\" style=\"background-color:transparent;z-index:17;\" scrolling=\"auto\" frameborder=\"0\" allowtransparency=\"true\" id=\"popupFrame\" name=\"popupFrame\" width=\"" + script_width + "px\" height=\"" + script_height + "px\"> </iframe> ";
				}
			if ( (VtigeRLogiNScripT == 'NEW_WINDOW') && (VtigeREnableD > 0) )
				{
				var VtigeRall = VtigeRurl + "/index.php?module=Users&action=Authenticate&return_module=Users&return_action=Login&user_name=" + user + "&user_password=" + pass + "&login_theme=softed&login_language=en_us";
				
				VtigeRwin =window.open(VtigeRall, web_form_target,'toolbar=1,location=1,directories=1,status=1,menubar=1,scrollbars=1,resizable=1,width=700,height=480');

				VtigeRwin.blur();
				}
			if ( (crm_popup_login == 'Y') && (crm_login_address.length > 4) )
				{
				var regWFAcustom = new RegExp("^VAR","ig");
				var TEMP_crm_login_address = URLDecode(crm_login_address,'YES');
				TEMP_crm_login_address = TEMP_crm_login_address.replace(regWFAcustom, '');

				var CRMwin = 'CRMwin';
				CRMwin = window.open(TEMP_crm_login_address, CRMwin,'toolbar=1,location=1,directories=1,status=1,menubar=1,scrollbars=1,resizable=1,width=700,height=480');

				CRMwin.blur();
				}
			if (INgroupCOUNT > 0)
				{
				HidEGenDerPulldown();
				}
			if (is_webphone=='Y')
				{
				NoneInSession();
				document.getElementById("NoneInSessionLink").innerHTML = "<a href=\"#\" onclick=\"NoneInSessionCalL();return false;\" class=\"button blue\" style=\"width: 100px;\">Call Agent Webphone -></a>";
				
				var WebPhonEtarget = 'webphonewindow';

			//	WebPhonEwin =window.open(WebPhonEurl, WebPhonEtarget,'toolbar=1,location=1,directories=1,status=1,menubar=1,scrollbars=1,resizable=1,width=180,height=270');

			//	WebPhonEwin.blur();
				}

			if ( (ivr_park_call=='ENABLED') || (ivr_park_call=='ENABLED_PARK_ONLY') )
				{
				showDiv('ivrParkControl');
				}

			VICIDiaL_closer_login_checked = 1;
			}
		else
			{

			var WaitingForNextStep=0;
			if ( (CloserSelecting==1) || (TerritorySelecting==1) )	{WaitingForNextStep=1;}
			if (open_dispo_screen==1)
				{
				wrapup_counter=0;
				if (wrapup_seconds > 0)	
					{
					showDiv('WrapupBox');
					document.getElementById("WrapupTimer").innerHTML = wrapup_seconds;
					wrapup_waiting=1;
					}
				CustomerData_update();
				if (hide_gender < 1)
					{
					document.getElementById("GENDERhideFORie").innerHTML = '';
					document.getElementById("GENDERhideFORieALT").innerHTML = '<select size="1" name="gender_list" class="cust_form" id="gender_list"><option value="U">U - Undefined</option><option value="M">M - Male</option><option value="F">F - Female</option></select>';
					}
				showDiv('DispoSelectBox');
				DispoSelectContent_create('','ReSET');
				WaitingForNextStep=1;
				open_dispo_screen=0;
				LIVE_default_xfer_group = default_xfer_group;
				LIVE_campaign_recording = campaign_recording;
				LIVE_campaign_rec_filename = campaign_rec_filename;
				if (disable_alter_custphone!='HIDE')
					{document.getElementById("DispoSelectPhonE").innerHTML = dialed_number;}
				else
					{document.getElementById("DispoSelectPhonE").innerHTML = '';}
				if (auto_dial_level == 0)
					{
					if (document.vicidial_form.DiaLAltPhonE.checked==true)
						{
						reselect_alt_dial = 1;
                                                document.getElementById("DiaLControl").innerHTML = DiaLControl_auto_HTML_OFF;
                                                document.getElementById("ManualDiaLControl").innerHTML = DiaLControl_manual_HTML;

						document.getElementById("MainStatuSSpan").innerHTML = "Dial Next Call";
						}
					else
						{
						reselect_alt_dial = 0;
						}
					}

				// Submit custom form if it is custom_fields_enabled
				if (custom_fields_enabled > 0)
					{
				//	alert("IFRAME submitting!");
					vcFormIFrame.document.form_custom_fields.submit();
					}
				}
			if (AgentDispoing > 0)	
				{
				WaitingForNextStep=1;
				check_for_conf_calls(session_id, '0');
				AgentDispoing++;
				}
			if (logout_stop_timeouts==1)	{WaitingForNextStep=1;}
			if ( (custchannellive < -30) && (lastcustchannel.length > 3) && (no_empty_session_warnings < 1) ) {CustomerChanneLGone();}
			if ( (custchannellive < -10) && (lastcustchannel.length > 3) ) {ReChecKCustoMerChaN();}
			
                        NoneInSessionCheck();
                        
			if (WaitingForNextStep==0)
				{
				if (trigger_ready > 0)
					{
					trigger_ready=0;
					if (auto_resume_precall == 'Y')
						{AutoDial_ReSume_PauSe("VDADready");}
					}
				// check for live channels in conference room and get current datetime
				check_for_conf_calls(session_id, '0');
				// refresh agent status view
				if (agent_status_view_active > 0)
					{
					refresh_agents_view('AgentViewStatus',agent_status_view);
					}
				if (view_calls_in_queue_active > 0)
					{
					refresh_calls_in_queue(view_calls_in_queue);
					}
				if (xfer_select_agents_active > 0)
					{
					refresh_agents_view('AgentXferViewSelect',agent_status_view);
					}
				if (agentonly_callbacks == '1')
					{CB_count_check++;}

				if (AutoDialWaiting == 1)
					{
					check_for_auto_incoming();
					}
                                if ((dial_method == "MANUAL" || dial_method == "INBOUND_MAN") && auto_dial_next_number == '1' && MD_channel_look != 1
                                    && VD_live_customer_call != 1 && AutoDialWaiting != 0)
                                    {
                                        AutoDialNextNumber++;
                                        if (AutoDialNextNumber == 10)
                                        {
                                            ManualDialNext('','','','','','0');
                                            AutoDialNextNumber = 0;
                                            AutoDialWaiting = 0;
                                        }
                                    }
				// look for a channel name for the manually dialed call
				if (MD_channel_look==1)
					{
					ManualDialCheckChanneL(XDcheck);
					}
				if ( (CB_count_check > 19) && (agentonly_callbacks == '1') )
					{
					CalLBacKsCounTCheck();
					CB_count_check=0;
					}
				if ( (even > 0) && (agent_display_dialable_leads > 0) )
					{
					DiaLableLeaDsCounT();
					}
				if (VD_live_customer_call==1)
					{
					VD_live_call_secondS++;
					document.vicidial_form.SecondS.value		= VD_live_call_secondS;
                                        
                                        var callsec_mm = Math.floor(VD_live_call_secondS/60);  // The minutes
					var callsec_ss = VD_live_call_secondS % 60;              // The balance of seconds
					if (callsec_ss < 10)
						{callsec_ss = "0" + callsec_ss;}
					var callsec_mmss = callsec_mm + ":" + callsec_ss;
					document.getElementById("SecondSDISP").innerHTML = callsec_mmss;
					}
                                        else
                                        {
                                            //document.getElementById("MainStatuSSpan").innerHTML = document.getElementById("MainStatuSSpanDefault").innerHTML;
                                        }
				if (XD_live_customer_call==1)
					{
					XD_live_call_secondS++;
					document.vicidial_form.xferlength.value		= XD_live_call_secondS;
					}
				if (customerparked==1)
					{
					customerparkedcounter++;
					var parked_mm = Math.floor(customerparkedcounter/60);  // The minutes
					var parked_ss = customerparkedcounter % 60;              // The balance of seconds
					if (parked_ss < 10)
						{parked_ss = "0" + parked_ss;}
					var parked_mmss = parked_mm + ":" + parked_ss;
					document.getElementById("ParkCounterSpan").innerHTML = "Time On Park: " + parked_mmss;
					}
				if (customerparkedxfer==1)
					{
						customerparkedcounterxfer++;
						var parked_mm = Math.floor(customerparkedcounterxfer/60);  // The minutes
						var parked_ss = customerparkedcounterxfer % 60;              // The balance of seconds
						if (parked_ss < 10)
						{parked_ss = "0" + parked_ss;}
						var parked_mmss = parked_mm + ":" + parked_ss;
						document.getElementById("ParkCounterSpan2").innerHTML = "Time On Park: " + parked_mmss;
					}
				if (customer_3way_hangup_counter_trigger > 0)
					{
					if (customer_3way_hangup_counter > customer_3way_hangup_seconds)
						{
						var customer_3way_timer_seconds = (XD_live_call_secondS - customer_3way_hangup_counter);
						customer_3way_hangup_process('DURING_CALL',customer_3way_timer_seconds);

						customer_3way_hangup_counter=0;
						customer_3way_hangup_counter_trigger=0;

						if (customer_3way_hangup_action=='DISPO')
							{
							customer_3way_hangup_dispo_message='Customer Hung-up, 3-way Call Ended Automatically';
							bothcall_send_hangup();
							}
                                                    else
                                                        {
                                                        customer_3way_hangup_custom_message = 'Customer Hung-up During 3-Way:';
                                                        CustomerChanneLGone();
                                                        }
						}
					else
						{
						customer_3way_hangup_counter++;
						document.getElementById("debugbottomspan").innerHTML = "CUSTOMER 3WAY HANGUP " + customer_3way_hangup_counter;
						}
					}
				if ( (update_fields > 0) && (update_fields_data.length > 2) )
					{
					UpdateFieldsData();
					}
				if ( (timer_action != 'NONE') && (timer_action.length > 3) && (timer_action_seconds <= VD_live_call_secondS) && (timer_action_seconds >= 0) )
					{
					TimerActionRun('','');
					}
				if (HKdispo_display > 0)
					{
					if ( (HKdispo_display == 3) && (HKfinish==1) )
						{
						HKfinish=0;
						DispoSelect_submit();
					//	AutoDialWaiting = 1;
					//	AutoDial_ReSume_PauSe("VDADready");
						}
					if (HKdispo_display == 1)
						{
						if (hot_keys_active==1)
							{showDiv('HotKeyEntriesBox');}
						hideDiv('HotKeyActionBox');
						}
					HKdispo_display--;
					}
				if (all_record == 'YES')
					{
					if (all_record_count < allcalls_delay)
						{all_record_count++;}
					else
						{
						conf_send_recording('MonitorConf',session_id ,'');
						all_record = 'NO';
						all_record_count=0;
						}
					}


				if (active_display==1)
					{
					check_s = check_n.toString();
						if ( (check_s.match(/00$/)) || (check_n<2) ) 
							{
						//	check_for_conf_calls();
							}
					}
				if (check_n<2) 
					{
					}
				else
					{
				//	check_for_live_calls();
					check_s = check_n.toString();
					}
				if ( (blind_monitoring_now > 0) && ( (blind_monitor_warning=='ALERT') || (blind_monitor_warning=='NOTICE') ||  (blind_monitor_warning=='AUDIO') || (blind_monitor_warning=='ALERT_NOTICE') || (blind_monitor_warning=='ALERT_AUDIO') || (blind_monitor_warning=='NOTICE_AUDIO') || (blind_monitor_warning=='ALL') ) )
					{
					if ( (blind_monitor_warning=='NOTICE') || (blind_monitor_warning=='ALERT_NOTICE') || (blind_monitor_warning=='NOTICE_AUDIO') || (blind_monitor_warning=='ALL') )
						{
                        document.getElementById("blind_monitor_notice_span_contents").innerHTML = blind_monitor_message + "<br />";
						showDiv('blind_monitor_notice_span');
						}
					if (blind_monitoring_now_trigger > 0)
						{
						if ( (blind_monitor_warning=='ALERT') || (blind_monitor_warning=='ALERT_NOTICE')|| (blind_monitor_warning=='ALERT_AUDIO') || (blind_monitor_warning=='ALL') )
							{
							document.getElementById("blind_monitor_alert_span_contents").innerHTML = blind_monitor_message;
							showDiv('blind_monitor_alert_span');
							}
						if ( (blind_monitor_filename.length > 0) && ( (blind_monitor_warning=='AUDIO') || (blind_monitor_warning=='ALERT_AUDIO')|| (blind_monitor_warning=='NOTICE_AUDIO') || (blind_monitor_warning=='ALL') ) )
							{
							basic_originate_call(blind_monitor_filename,'NO','YES',session_id,'YES','','1','0','1');
							}
						blind_monitoring_now_trigger=0;
						}
					}
				else
					{
					hideDiv('blind_monitor_notice_span');
					document.getElementById("blind_monitor_notice_span_contents").innerHTML = '';
					hideDiv('blind_monitor_alert_span');
					}
				if (wrapup_seconds > 0)	
					{
					document.getElementById("WrapupTimer").innerHTML = (wrapup_seconds - wrapup_counter);
					wrapup_counter++;
					if ( (wrapup_counter > wrapup_seconds) && (document.getElementById("WrapupBox").style.visibility == 'visible') )
						{
						wrapup_waiting=0;
						hideDiv('WrapupBox');
						if (document.vicidial_form.DispoSelectStop.checked==true)
							{
							if (auto_dial_level != '0')
								{
								AutoDialWaiting = 0;
						//		alert('wrapup pause');
								AutoDial_ReSume_PauSe("VDADpause");
						//		document.getElementById("DiaLControl").innerHTML = DiaLControl_auto_HTML;
								}
							VICIDiaL_pause_calling = 1;
							if (dispo_check_all_pause != '1')
								{
								document.vicidial_form.DispoSelectStop.checked=false;
						//		alert("unchecking PAUSE");
								}
							}
						else
							{
							if (auto_dial_level != '0')
								{
								AutoDialWaiting = 1;
						//		alert('wrapup ready');
								AutoDial_ReSume_PauSe("VDADready","NEW_ID","WRAPUP");
						//		document.getElementById("DiaLControl").innerHTML = DiaLControl_auto_HTML_ready;
								}
							}
						}
					}
				}
			}
		setTimeout("all_refresh()", refresh_interval);
		}
	function all_refresh()
		{
		epoch_sec++;
		check_n++;
		even++;
		if (even > 1)
			{even=0;}
		var year= t.getYear()
		var month= t.getMonth()
			month++;
		var daym= t.getDate()
		var hours = t.getHours();
		var min = t.getMinutes();
		var sec = t.getSeconds();
		var regMSdate = new RegExp("MS_","g");
		var regUSdate = new RegExp("US_","g");
		var regEUdate = new RegExp("EU_","g");
		var regALdate = new RegExp("AL_","g");
		var regAMPMdate = new RegExp("AMPM","g");
		if (year < 1000) {year+=1900}
		if (month< 10) {month= "0" + month}
		if (daym< 10) {daym= "0" + daym}
		if (hours < 10) {hours = "0" + hours;}
		if (min < 10) {min = "0" + min;}
		if (sec < 10) {sec = "0" + sec;}
		var Tyear = (year-2000);
		filedate = year + "" + month + "" + daym + "-" + hours + "" + min + "" + sec;
		tinydate = Tyear + "" + month + "" + daym + "" + hours + "" + min + "" + sec;
		SQLdate = year + "-" + month + "-" + daym + " " + hours + ":" + min + ":" + sec;

		var status_date = '';
		var status_time = hours + ":" + min + ":" + sec;
		if (vdc_header_date_format.match(regMSdate))
			{
			status_date = year + "-" + month + "-" + daym;
			}
		if (vdc_header_date_format.match(regUSdate))
			{
			status_date = month + "/" + daym + "/" + year;
			}
		if (vdc_header_date_format.match(regEUdate))
			{
			status_date = daym + "/" + month + "/" + year;
			}
		if (vdc_header_date_format.match(regALdate))
			{
			var statusmon='';
			if (month == 1) {statusmon = "JAN";}
			if (month == 2) {statusmon = "FEB";}
			if (month == 3) {statusmon = "MAR";}
			if (month == 4) {statusmon = "APR";}
			if (month == 5) {statusmon = "MAY";}
			if (month == 6) {statusmon = "JUN";}
			if (month == 7) {statusmon = "JLY";}
			if (month == 8) {statusmon = "AUG";}
			if (month == 9) {statusmon = "SEP";}
			if (month == 10) {statusmon = "OCT";}
			if (month == 11) {statusmon = "NOV";}
			if (month == 12) {statusmon = "DEC";}

			status_date = statusmon + " " + daym;
			}
		if (vdc_header_date_format.match(regAMPMdate))
			{
			var AMPM = 'AM';
			if (hours == 12) {AMPM = 'PM';}
			if (hours == 0) {AMPM = 'AM'; hours = '12';}
			if (hours > 12) {hours = (hours - 12);   AMPM = 'PM';}
			status_time = hours + ":" + min + ":" + sec + " " + AMPM;
			}

		document.getElementById("status").innerHTML = status_date + " " + status_time  + display_message;
		if (VD_live_customer_call==1)
			{
			var customer_gmt = parseFloat(document.vicidial_form.gmt_offset_now.value);
			var AMPM = 'AM';
			var customer_gmt_diff = (customer_gmt - local_gmt);
			var UnixTimec = (UnixTime + (3600 * customer_gmt_diff));
			var UnixTimeMSc = (UnixTimec * 1000);
			c.setTime(UnixTimeMSc);
			var Cyear= c.getYear()
			var Cmon= c.getMonth()
				Cmon++;
			var Cdaym= c.getDate()
			var Chours = c.getHours();
			var Cmin = c.getMinutes();
			var Csec = c.getSeconds();
			if (Cyear < 1000) {Cyear+=1900}
			if (Cmon < 10) {Cmon= "0" + Cmon}
			if (Cdaym < 10) {Cdaym= "0" + Cdaym}
			if (Chours < 10) {Chours = "0" + Chours;}
			if ( (Cmin < 10) && (Cmin.length < 2) ) {Cmin = "0" + Cmin;}
			if ( (Csec < 10) && (Csec.length < 2) ) {Csec = "0" + Csec;}
			if (Cmin < 10) {Cmin = "0" + Cmin;}
			if (Csec < 10) {Csec = "0" + Csec;}

		var customer_date = '';
		var customer_time = Chours + ":" + Cmin + ":" + Csec;
		if (vdc_customer_date_format.match(regMSdate))
			{
			customer_date = Cyear + "-" + Cmon + "-" + Cdaym;
			}
		if (vdc_customer_date_format.match(regUSdate))
			{
			customer_date = Cmon + "/" + Cdaym + "/" + Cyear;
			}
		if (vdc_customer_date_format.match(regEUdate))
			{
			customer_date = Cdaym + "/" + Cmon + "/" + Cyear;
			}
		if (vdc_customer_date_format.match(regALdate))
			{
			var customermon='';
			if (Cmon == 1) {customermon = "JAN";}
			if (Cmon == 2) {customermon = "FEB";}
			if (Cmon == 3) {customermon = "MAR";}
			if (Cmon == 4) {customermon = "APR";}
			if (Cmon == 5) {customermon = "MAY";}
			if (Cmon == 6) {customermon = "JUN";}
			if (Cmon == 7) {customermon = "JLY";}
			if (Cmon == 8) {customermon = "AUG";}
			if (Cmon == 9) {customermon = "SEP";}
			if (Cmon == 10) {customermon = "OCT";}
			if (Cmon == 11) {customermon = "NOV";}
			if (Cmon == 12) {customermon = "DEC";}

			customer_date = customermon + " " + Cdaym + " ";
			}
		if (vdc_customer_date_format.match(regAMPMdate))
			{
			var AMPM = 'AM';
			if (Chours == 12) {AMPM = 'PM';}
			if (Chours == 0) {AMPM = 'AM'; Chours = '12';}
			if (Chours > 12) {Chours = (Chours - 12);   AMPM = 'PM';}
			customer_time = Chours + ":" + Cmin + ":" + Csec + " " + AMPM;
			}

			var customer_local_time = customer_date + " " + customer_time;
			//document.getElementById("custdatetime").innerHTML = customer_local_time;
                        document.getElementById("status").innerHTML = customer_local_time;
			}
		start_all_refresh();

		if (check_n==2)
			{
			hideDiv('LoadingBox');
			}
		}
	function pause()	// Pauses the refreshing of the lists
		{active_display=2;  display_message="  - ACTIVE DISPLAY PAUSED - ";}
	function start()	// resumes the refreshing of the lists
		{active_display=1;  display_message='';}
	function faster()	// lowers by 1000 milliseconds the time until the next refresh
		{
		 if (refresh_interval>1001)
			{refresh_interval=(refresh_interval - 1000);}
		}
	function slower()	// raises by 1000 milliseconds the time until the next refresh
		{
		refresh_interval=(refresh_interval + 1000);
		}

	// activeext-specific functions
	function activeext_force_refresh()	// forces immediate refresh of list content
		{getactiveext();}
	function activeext_order_asc()	// changes order of activeext list to ascending
		{
		activeext_order="asc";   getactiveext();
		desc_order_HTML ='<a href="#" onclick="activeext_order_desc();return false;">ORDER</a>';
		document.getElementById("activeext_order").innerHTML = desc_order_HTML;
		}
	function activeext_order_desc()	// changes order of activeext list to descending
		{
		activeext_order="desc";   getactiveext();
		asc_order_HTML ='<a href="#" onclick="activeext_order_asc();return false;">ORDER</a>';
		document.getElementById("activeext_order").innerHTML = asc_order_HTML;
		}

	// busytrunk-specific functions
	function busytrunk_force_refresh()	// forces immediate refresh of list content
		{getbusytrunk();}
	function busytrunk_order_asc()	// changes order of busytrunk list to ascending
		{
		busytrunk_order="asc";   getbusytrunk();
		desc_order_HTML ='<a href="#" onclick="busytrunk_order_desc();return false;">ORDER</a>';
		document.getElementById("busytrunk_order").innerHTML = desc_order_HTML;
		}
	function busytrunk_order_desc()	// changes order of busytrunk list to descending
		{
		busytrunk_order="desc";   getbusytrunk();
		asc_order_HTML ='<a href="#" onclick="busytrunk_order_asc();return false;">ORDER</a>';
		document.getElementById("busytrunk_order").innerHTML = asc_order_HTML;
		}
	function busytrunkhangup_force_refresh()	// forces immediate refresh of list content
		{busytrunkhangup();}

	// busyext-specific functions
	function busyext_force_refresh()	// forces immediate refresh of list content
		{getbusyext();}
	function busyext_order_asc()	// changes order of busyext list to ascending
		{
		busyext_order="asc";   getbusyext();
		desc_order_HTML ='<a href="#" onclick="busyext_order_desc();return false;">ORDER</a>';
		document.getElementById("busyext_order").innerHTML = desc_order_HTML;
		}
	function busyext_order_desc()	// changes order of busyext list to descending
		{
		busyext_order="desc";   getbusyext();
		asc_order_HTML ='<a href="#" onclick="busyext_order_asc();return false;">ORDER</a>';
		document.getElementById("busyext_order").innerHTML = asc_order_HTML;
		}
	function busylocalhangup_force_refresh()	// forces immediate refresh of list content
		{busylocalhangup();}


	// functions to hide and show different DIVs
	function showDiv(divvar) 
		{
		if (document.getElementById(divvar))
			{
			divref = document.getElementById(divvar).style;
			divref.visibility = 'visible';
                        //divref.display = 'none';
			}
		}
	function hideDiv(divvar)
		{
		if (document.getElementById(divvar))
			{
			divref = document.getElementById(divvar).style;
			divref.visibility = 'hidden';
                        //divref.display = 'block';
			}
		}
        function showDivDisplay(divvar) 
		{
		if (document.getElementById(divvar))
			{
			divref = document.getElementById(divvar).style;
			divref.display = '';
			}
		}
	function hideDivDisplay(divvar)
		{
		if (document.getElementById(divvar))
			{
			divref = document.getElementById(divvar).style;
			divref.display = 'none';
			}
		}
	function clearDiv(divvar)
		{
		if (document.getElementById(divvar))
			{
			document.getElementById(divvar).innerHTML = '';
			if (divvar == 'DiaLLeaDPrevieW')
				{
                var buildDivHTML = "<input type=\"checkbox\" name=\"LeadPreview\" size=\"1\" value=\"0\" /> Preview Dial Lead<br />";
				document.getElementById("DiaLLeaDPrevieWHide").innerHTML = buildDivHTML;
				}
			if (divvar == 'DiaLDiaLAltPhonE')
				{
                var buildDivHTML = "<input type=\"checkbox\" name=\"DiaLAltPhonE\" size=\"1\" value=\"0\" /> Dial Alt Phone<br />";
				document.getElementById("DiaLDiaLAltPhonEHide").innerHTML = buildDivHTML;
				}
			if (DefaulTAlTDiaL == '1')
				{document.vicidial_form.DiaLAltPhonE.checked=true;}
			}
		}
	function buildDiv(divvar)
		{
		if (document.getElementById(divvar))
			{
			var buildDivHTML = "";
			if (divvar == 'DiaLLeaDPrevieW')
				{
				document.getElementById("DiaLLeaDPrevieWHide").innerHTML = '';
                var buildDivHTML = "<input type=\"checkbox\" name=\"LeadPreview\" size=\"1\" value=\"0\" /> Preview Dial Lead<br />";
				document.getElementById(divvar).innerHTML = buildDivHTML;
				if (reselect_preview_dial==1)
					{document.vicidial_form.LeadPreview.checked=true}
				}
			if (divvar == 'DiaLDiaLAltPhonE')
				{
				document.getElementById("DiaLDiaLAltPhonEHide").innerHTML = '';
                var buildDivHTML = "<input type=\"checkbox\" name=\"DiaLAltPhonE\" size=\"1\" value=\"0\" /> Dial Alt Phone<br />";
				document.getElementById(divvar).innerHTML = buildDivHTML;
				if (reselect_alt_dial==1)
					{document.vicidial_form.DiaLAltPhonE.checked=true}
				if (DefaulTAlTDiaL == '1')
					{document.vicidial_form.DiaLAltPhonE.checked=true;}
				}
			}
		}

	function conf_channels_detail(divvar) 
		{
		if (divvar == 'SHOW')
			{
			conf_channels_xtra_display = 1;
			document.getElementById("busycallsdisplay").innerHTML = "<a href=\"#\"  onclick=\"conf_channels_detail('HIDE');\">Hide conference call channel information</a>";
			LMAe[0]=''; LMAe[1]=''; LMAe[2]=''; LMAe[3]=''; LMAe[4]=''; LMAe[5]=''; 
			LMAcount=0;
			}
		else
			{
			conf_channels_xtra_display = 0;
            document.getElementById("busycallsdisplay").innerHTML = "<a href=\"#\" onclick=\"conf_channels_detail('SHOW');\">Show conference call channel information</a>";
			document.getElementById("outboundcallsspan").innerHTML = '';
			LMAe[0]=''; LMAe[1]=''; LMAe[2]=''; LMAe[3]=''; LMAe[4]=''; LMAe[5]=''; 
			LMAcount=0;
			}
		}

	function HotKeys(HKstate) 
		{
		if ( (HKstate == 'ON') && (HKbutton_allowed == 1) )
			{
			showDiv('HotKeyEntriesBox');
			hot_keys_active = 1;
            document.getElementById("hotkeysdisplay").innerHTML = "<a href=\"#\" onclick=\"HotKeys('OFF')\">Turn Off HotKeys</a>";
			}
		else
			{
			hideDiv('HotKeyEntriesBox');
			hot_keys_active = 0;
            document.getElementById("hotkeysdisplay").innerHTML = "<a href=\"#\" onclick=\"HotKeys('ON')\">Turn On HotKeys</a>";
			}
		}

	function ShoWTransferMain(showxfervar,showoffvar)
		{
		if (VU_vicidial_transfers == '1')
			{
			XferAgentSelectLink();

			if (showxfervar == 'ON')
				{
				
                                
                                HKbutton_allowed = 0;
				hideDiv('ScriptPanel');
                                hideDiv('ScriptRefresH');
                                hideDiv('FormPanel');
                                hideDiv('FormRefresH');
                                hideDiv('MainPanelCustInfo');
                                hideDiv('ActionPanel');
                                showDiv('TransferMain');
                                /*
                                 document.getElementById("XferControl").innerHTML = "<a href=\"#\" onclick=\"ShoWTransferMain('OFF','YES');\"><img src=\"./images/x-transfer-on.png\" border=\"0\" alt=\"Transfer - Conference\" /><span>X-fer or Conf.</span></a>";
                                
				if ( (quick_transfer_button_enabled > 0) && (quick_transfer_button_locked < 1) )
                                {document.getElementById("QuickXfer").innerHTML = "<a href=\"#\" style=\"opacity:.15;\" disabled><img class=\"disabled_img\" src=\"./images/x-transfer-quick.png\" border=\"0\" alt=\"QUICK TRANSFER\" /><br /><span>Quick X-fer</span></a>";}
				if (custom_3way_button_transfer_enabled > 0)
                                {document.getElementById("CustomXfer").innerHTML = "<a href=\"#\" style=\"opacity:.15;\" disabled><img class=\"disabled_img\" src=\"./images/x-transfer-custom.png\" border=\"0\" alt=\"Custom Transfer\" /><br /><span>Custom X-fer</span></a>";}
                                */
				}
			else
				{
				HKbutton_allowed = 1;
				hideDiv('ScriptPanel');
                                hideDiv('ScriptRefresH');
                                hideDiv('FormPanel');
                                hideDiv('FormRefresH');
                                showDiv('MainPanelCustInfo');
                                hideDiv('ActionPanel');
                                hideDiv('TransferMain');
				//hideDiv('agentdirectlink');
                                
				//if (showoffvar == 'YES')
					//{
                                        //document.getElementById("XferControl").innerHTML = "<a href=\"#\" onclick=\"ShoWTransferMain('ON');\"><img src=\"./images/x-transfer-on.png\" border=\"0\" alt=\"Transfer - Conference\" /><span>X-fer or Conf.</span></a>";
					//}
				}
                                
                        if (quick_transfer_button_enabled > 0)
                        {
                            if ( (quick_transfer_button == 'IN_GROUP') || (quick_transfer_button == 'LOCKED_IN_GROUP') )
                                    {
                                    document.getElementById("QuickXfer").innerHTML = "<a href=\"#\" onclick=\"mainxfer_send_redirect('XfeRLOCAL','" + lastcustchannel + "','" + lastcustserverip + "','','','" + quick_transfer_button_locked + "',1);return false;\"><img src=\"./images/x-transfer-quick.png\" border=\"0\" alt=\"QUICK TRANSFER\" /><br /><span>Quick X-fer</span></a>";
                                    }
                            if ( (quick_transfer_button == 'PRESET_1') || (quick_transfer_button == 'PRESET_2') || (quick_transfer_button == 'PRESET_3') || (quick_transfer_button == 'PRESET_4') || (quick_transfer_button == 'PRESET_5') || (quick_transfer_button == 'LOCKED_PRESET_1') || (quick_transfer_button == 'LOCKED_PRESET_2') || (quick_transfer_button == 'LOCKED_PRESET_3') || (quick_transfer_button == 'LOCKED_PRESET_4') || (quick_transfer_button == 'LOCKED_PRESET_5') )
                                    {
                                    document.getElementById("QuickXfer").innerHTML = "<a href=\"#\" onclick=\"mainxfer_send_redirect('XfeRBLIND','" + lastcustchannel + "','" + lastcustserverip + "','','','" + quick_transfer_button_locked + "',1);return false;\"><img src=\"./images/x-transfer-quick.png\" border=\"0\" alt=\"QUICK TRANSFER\" /><br /><span>Quick X-fer</span></a>";
                                    }
                        }
                        if (custom_3way_button_transfer_enabled > 0)
                                {
                                document.getElementById("CustomXfer").innerHTML = "<a href=\"#\" onclick=\"custom_button_transfer();return false;\"><img src=\"./images/x-transfer-custom.png\" border=\"0\" alt=\"Custom Transfer\" /><br /><span>Custom X-fer</span></a>";
                                }
                                                
			if (three_way_call_cid == 'AGENT_CHOOSE')
				{
				if ( (active_group_alias.length < 1) && (LIVE_default_group_alias.length > 1) && (LIVE_caller_id_number.length > 3) )
					{
					active_group_alias = LIVE_default_group_alias;
					cid_choice = LIVE_caller_id_number;
					}
                                document.getElementById("XfeRDiaLGrouPSelecteD").innerHTML = "<font size=\"1\" face=\"Arial,Helvetica\">Group Alias: " + active_group_alias + "</font>";
                                document.getElementById("XfeRCID").innerHTML = "<a href=\"#\" onclick=\"GroupAliasSelectContent_create('1');\"><font size=\"1\" face=\"Arial,Helvetica\">Choose Group Alias</font></a>";
				}
			else
				{
				document.getElementById("XfeRCID").innerHTML = "";
				document.getElementById("XfeRDiaLGrouPSelecteD").innerHTML = "";
				}
			}
		else
			{
			if (showxfervar != 'OFF')
				{
				alert_box('YOU DO NOT HAVE PERMISSIONS TO TRANSFER CALLS');
				}
			}
		}

	function MainPanelToFront(resumevar)
		{
		//document.getElementById("MainTable").style.backgroundColor="<?php echo $MAIN_COLOR ?>";
		//document.getElementById("MaiNfooter").style.backgroundColor="<?php echo $MAIN_COLOR ?>";
		
		hideDiv('ScriptPanel');
		hideDiv('ScriptRefresH');
		hideDiv('FormPanel');
		hideDiv('FormRefresH');
		showDiv('MainPanelCustInfo');
                hideDiv('ActionPanel');
                hideDiv('TransferMain');
		ShoWGenDerPulldown();

		if (resumevar != 'NO')
			{
			if (alt_phone_dialing == 1)
				{buildDiv('DiaLDiaLAltPhonE');}
			else
				{clearDiv('DiaLDiaLAltPhonE');}
			if (auto_dial_level == 0)
				{
				if (auto_dial_alt_dial==1)
					{
					auto_dial_alt_dial=0;
					document.getElementById("DiaLControl").innerHTML = DiaLControl_auto_HTML_OFF;
                                        document.getElementById("ManualDiaLControl").innerHTML = DiaLControl_manual_HTML_OFF;
					}
				else
					{
					document.getElementById("DiaLControl").innerHTML = DiaLControl_manual_HTML;
                                        document.getElementById("ManualDiaLControl").innerHTML = DiaLControl_manual_HTML_OFF;
					if (manual_dial_preview == 1)
						{buildDiv('DiaLLeaDPrevieW');}
					}
				}
			else
				{
				if (dial_method == "INBOUND_MAN")
					{
                                        document.getElementById("DiaLControl").innerHTML = DiaLControl_auto_HTML;
                                        document.getElementById("ManualDiaLControl").innerHTML = DiaLControl_manual_HTML;
					if (manual_dial_preview == 1)
						{buildDiv('DiaLLeaDPrevieW');}
					}
				else
					{
					document.getElementById("DiaLControl").innerHTML = DiaLControl_auto_HTML;
                                        document.getElementById("ManualDiaLControl").innerHTML = DiaLControl_manual_HTML_OFF;
					clearDiv('DiaLLeaDPrevieW');
					}
				}
			}
		panel_bgcolor='<?php echo $MAIN_COLOR ?>';
		//document.getElementById("MainStatuSSpan").style.background = panel_bgcolor;
		}

	function ScriptPanelToFront()
		{
		
		showDiv('ScriptPanel');
		showDiv('ScriptRefresH');
		hideDiv('FormPanel');
		hideDiv('FormRefresH');
		hideDiv('MainPanelCustInfo');
                hideDiv('ActionPanel');
                hideDiv('TransferMain');
		//document.getElementById("MainTable").style.backgroundColor="<?php echo $SCRIPT_COLOR ?>";
		//document.getElementById("MaiNfooter").style.backgroundColor="<?php echo $SCRIPT_COLOR ?>";
		panel_bgcolor='<?php echo $SCRIPT_COLOR ?>';
		//document.getElementById("MainStatuSSpan").style.background = panel_bgcolor;

		HidEGenDerPulldown();
		}

	function FormPanelToFront()
		{
		
                hideDiv('ScriptPanel');
		hideDiv('ScriptRefresH');
		showDiv('FormPanel');
		showDiv('FormRefresH');
		hideDiv('MainPanelCustInfo');
                hideDiv('ActionPanel');
                hideDiv('TransferMain');
                
		//document.getElementById("MainTable").style.backgroundColor="<?php echo $FORM_COLOR ?>";
		//document.getElementById("MaiNfooter").style.backgroundColor="<?php echo $FORM_COLOR ?>";
		panel_bgcolor='<?php echo $FORM_COLOR ?>';
		//document.getElementById("MainStatuSSpan").style.background = panel_bgcolor;

		HidEGenDerPulldown();
		}
                
        function ActionPanelToFront()
		{
		hideDiv('ScriptPanel');
		hideDiv('ScriptRefresH');
		hideDiv('FormPanel');
		hideDiv('FormRefresH');
		hideDiv('MainPanelCustInfo');
                showDiv('ActionPanel');
                hideDiv('TransferMain');
		}

	function HidEGenDerPulldown()
		{
		if (hide_gender < 1)
			{
			var gIndex = 0;
			var genderIndex = document.getElementById("gender_list").selectedIndex;
			var genderValue =  document.getElementById('gender_list').options[genderIndex].value;
			if (genderValue == 'M') {var gIndex = 1;}
			if (genderValue == 'F') {var gIndex = 2;}
			document.getElementById("GENDERhideFORieALT").innerHTML = '<select size="1" name="gender_list" class="cust_form" id="gender_list"><option value="U">U - Undefined</option><option value="M">M - Male</option><option value="F">F - Female</option></select>';
			document.getElementById("GENDERhideFORie").innerHTML = '';
			document.getElementById("gender_list").selectedIndex = gIndex;
			}
		}

	function ShoWGenDerPulldown()
		{
		if (hide_gender < 1)
			{
			var gIndex = 0;
			var genderIndex = document.getElementById("gender_list").selectedIndex;
			var genderValue =  document.getElementById('gender_list').options[genderIndex].value;
			if (genderValue == 'M') {var gIndex = 1;}
			if (genderValue == 'F') {var gIndex = 2;}
			document.getElementById("GENDERhideFORie").innerHTML = '<select size="1" name="gender_list" class="cust_form" id="gender_list"><option value="U">U - Undefined</option><option value="M">M - Male</option><option value="F">F - Female</option></select>';
			document.getElementById("GENDERhideFORieALT").innerHTML = '';
			document.getElementById("gender_list").selectedIndex = gIndex;
			}
		}

function numbersonly(myfield, e, dec)
{
	var key;
	var keychar;
	
	if (window.event)
		key = window.event.keyCode;
	else if (e)
		key = e.which;
	else
		return true;
	keychar = String.fromCharCode(key);
	
	// control keys
	if ((key==null) || (key==0) || (key==8) || 
		(key==9) || (key==13) || (key==27) )
		return true;
	
	// numbers
	else if ((("0123456789").indexOf(keychar) > -1))
		return true;
	
	// decimal point jump
	else if (dec && (keychar == "."))
	{
		myfield.form.elements[dec].focus();
		return false;
	}
	else
		return false;
}

	</script>

<?php
echo "</head>\n";

$zi=2;

?>
<body onload="begin_all_refresh();"  onunload="BrowserCloseLogout();">

<form name=vicidial_form id=vicidial_form onsubmit="return false;">
<!--
<nav>
    <ul id="n" class="clearfix">
        <li><img id="x_logo" src="./images/InktelX-Frontline-Sponsored.png" border="0" alt="X Frontline" /></li>
        <li>
            
        </li>
        <li>
            
        </li>
    </ul>
    
</nav>
-->

<div id="content">
    <div id="userStats" class="clearfix">
        <div class="pic">
            <!--<img class="profile" src="./images/InktelX-Frontline-Vici.png" width="120px" height="120px"/><br>-->
            <img class="profile" src="./images/x-user-on.png" width="120px" height="120px"/><br>
            <span id="livecall" class="off">Not on Call</span>
        </div>
        
        <div class="data">
            <h1><a href="#" onclick="NoneInSessionCheck(1);"><?php echo "$LOGfullname"; ?></a></h1>
            <h3><?php echo "$VDcampaign_name <!-- ($VD_campaign)-->"; ?></h3>
            <h4><span id="status">LIVE</span></h4>
            <div class="actionLinks">
                <span id="DiaLControl"><a href="#" onclick="AutoDial_ReSume_PauSe('VDADready');"><img src="./images/x-resume.png" border="0" alt="Resume" /><span id="x-ready">Ready</span></a></span>
                <span id="LogOffControl"><a id="x_logoff_" href="#" onclick="NormalLogout();return false;"><img id="x_logoff" src="./images/x-logoff-on.png" border="0" alt="Log Out" title="Log Out"  /><span>Logout</span></a></span>
            </div>
            <div class="sep"></div>
            <ul class="numbers clearfix">
                <li>Calls Today<strong><span id="AgentCallsToday"></span></strong></li>
                <li>Avg Talk Time<strong><span id="AgentAvgTalkTime"></span> mins</strong></li>
                <li>Avg Wait Time<strong><span id="AgentAvgWaitTime"></span> mins</strong></li>
                <li class="nobrdr">Calls In Queue<strong><span id="AgentStatusCalls"></span></strong></li>
            </ul>
        </div>
    </div>
    <div id="notifications">
        <span id="MainStatuSSpanDefault" style="display: none;">You're currently logged in as <b><?php echo "$VD_login"; ?></b> at extension <?php echo "<b>$phone_login</b>"; if ($on_hook_agent == 'Y'){echo " (<a href=\"#\" onclick=\"NoneInSessionCalL();return false;\">Connect Now</a>)"; } ?></span>
        <span id="MainStatuSSpan"></span>
        <span style="float: right;"><b>Call Duration:</b> <span id="SecondSDISP">0</span></span>
    </div>
</div>

<div id="content" class="clearfix" style="padding-top:0px;">
    <div id="left">
            
            <div class="gcontent">
                    <div class="head"><h1>Actions</h1></div>
                    <div class="boxy">
                        <span id="ParkCounterSpan"> &nbsp; </span>
                        <span class="action" id="ParkControl"><a href="#" style="opacity:.15;" disabled><img src="./images/x-hold-on.png" alt="Park call"  title="Park call" class="disabled_img" /><span>Place on Hold</span></a></span>
                        <?php # feature below is disabled (style=display none) for now, as it isn't necessary
                        if ( ($ivr_park_call=='ENABLED') or ($ivr_park_call=='ENABLED_PARK_ONLY') )
                        {echo "<span id=\"ivrParkControl\" style=\"display:none;\"><a href=\"#\" style=\"opacity:.15;\" disabled><img src=\"./images/x-holdivr.png\" class=\"disabled_img\" border=\"0\" alt=\"IVR Park Call\" /><span>IVR Hold</span></a></span>";}
                        else
                        {echo "<span id=\"ivrParkControl\" style=\"display:none;\"></span>";}
                        ?>
                        <span class="action" id="XferControl">
                            <a href="#" style="opacity:.15;" disabled>
                                <img src="./images/x-transfer-on.png" alt="Transfer call" title="Transfer call" class="disabled_img"/><span>X-fer or Conf.</span>
                            </a>
                        </span>
                        
                        <span class="action" id="HangupControl">
                            <a href="#" style="opacity:.15;" disabled><img src="./images/x-endcall-on.png" alt="End call" title="End call" class="disabled_img" /><span>End Call</span></a>
                        </span>
                        <span class="action">
                            <a href="#" onclick="ActionPanelToFront();"><img src="./images/x-settings-on.png" style="margin-left:-10px;margin-right:-10px" border="0" alt="More Actions" /><span>More Actions...</span></a>
                        </span> 
                    </div>
            </div>
            
            <div class="gcontent">
                <div class="head"><h1>Places</h1></div>
                <div class="boxy" id="placesDiv" style="height: 180px;">
                    <span class="action" id="CustControl">
                        <a href="#" onclick="MainPanelToFront('NO');">
                            <img src="./images/x-contact-on.png" alt="Contact information" title="Contact information" /><span>Contact Information</span>
                        </a>
                    </span>
                    <span class="action" id="ScriptControl">
                        <a href="#" onclick="ScriptPanelToFront();"><img src="./images/x-script.png" alt="Interaction script" title="Interaction script" /><span>Interaction Script</span></a>
                    </span>
                    <!--
                    <span class="action" id="Xffff">
                        <a href="#" onclick="ShoWTransferMain('ON');"><img src="./images/x-script.png" alt="Interaction script" title="Interaction script" /><span>Xfer Main</span></a>
                    </span>
                    -->
                    <?php if ($custom_fields_enabled > 0)
                    {
                    echo "
                    <span class=\"action\" id=\"FormsControl\">
                        <a href=\"#\" onclick=\"FormPanelToFront();\"><img src=\"./images/x-crmforms-on.png\" alt=\"CRM forms\" title=\"CRM forms\" border=\"0\" /><span>Interaction Form</span></a>
                    </span>
                    ";}
                    ?> 
                    <?php
                    if ($agent_lead_search == 'ENABLED')
                            {echo "<span class=\"action\"><a href=\"#\" onclick=\"OpeNSearcHForMDisplaYBox();return false;\"><img src=\"./images/x-addressbook-on.png\" alt=\"Census database\" title=\"Census database\" border=\"0\" /><span>Population Search</span></a></span>";}
                    ?>
                    <?php echo "<span style=\"text-align:center;color:#eee\" class=\"action\">$server_addr</span>"; ?>
                    
                    <span id="AgentViewSpan" style="overflow: scroll;">
                        <table cellpadding="0" cellspacing="0" border="0">
                            <tr>
                                <td width="5px" rowspan="2">&nbsp;</td>
                                <td><b>Other Agents:</b> &nbsp;</td>
                            </tr>
                            <tr>
                                <td colspan="2"><span id="AgentViewStatus">&nbsp;</span></td>
                            </tr>
                        </table>
                    </span>
                </div>
            </div>
            
    </div>
    
    <div id="right">
        <span style="position:absolute;top:255px;width:545px;z-index:<?php $zi++; echo $zi ?>;" id="MainPanelCustInfo">
            <h1>Contact Information<span id="called_count_desc" style="font-size:14.5pt"></span></h1>
            <table>
                <tr>
                    <td colspan="2" align="center">
                        <span id="CusTInfOSpaN"></span>
                    </td>
                </tr>
                <tr>
                    <td align="left" colspan="2">
        
                        <table width="550px">
                            <tr>
                                <td align="right">
                                    <?php
                            
                                    if ($label_title == '---HIDE---')
                                    {echo "</td><td align=\"left\" colspan=\"5\"><input type=\"hidden\" name=\"title\" id=\"title\" value=\"\" />";}
                                    else
                                    {echo "$label_title: </td><td align=\"left\" colspan=\"5\"><input type=\"text\" style=\"width:50px;\" size=\"4\" name=\"title\" id=\"title\" maxlength=\"4\" class=\"cust_form\" value=\"\" />";}
                                
                                echo "</td></tr><tr><td align=\"right\">";
                                
                                    if ($label_first_name == '---HIDE---')
                                    {echo "</td><td align=\"left\" colspan=\"5\">&nbsp; <input type=\"hidden\" name=\"first_name\" id=\"first_name\" value=\"\" />";}
                                    else
                                    {echo "&nbsp; $label_first_name: </td><td align=\"left\" colspan=\"5\"><input type=\"text\" size=\"17\" name=\"first_name\" id=\"first_name\" maxlength=\"30\" class=\"cust_form\" value=\"\" />";}
                                    
                                    if ($label_middle_initial == '---HIDE---')
                                    {echo "&nbsp; <input type=\"hidden\" name=\"middle_initial\" id=\"middle_initial\" value=\"\" />";}
                                    else
                                    {echo "&nbsp; $label_middle_initial: <input type=\"text\" style=\"width:20px;\" size=\"1\" name=\"middle_initial\" id=\"middle_initial\" maxlength=\"1\" class=\"cust_form\" value=\"\" />";}
                                    
                                    if ($label_last_name == '---HIDE---')
                                    {echo "&nbsp; <input type=\"hidden\" name=\"last_name\" id=\"last_name\" value=\"\" />";}
                                    else
                                    {echo "&nbsp; $label_last_name: <input type=\"text\" size=\"23\" name=\"last_name\" id=\"last_name\" maxlength=\"30\" class=\"cust_form\" value=\"\" />";}
                                    
                                echo "</td></tr><tr><td align=\"right\">";
                                    
                                    if ($label_address1 == '---HIDE---')
                                    {echo " </td><td align=\"left\" colspan=\"5\"><input type=\"hidden\" name=\"address1\" id=\"address1\" value=\"\" />";}
                                    else
                                    {echo "$label_address1: </td><td align=\"left\" colspan=5><input type=\"text\" style=\"width:457px;\"  size=\"85\" name=\"address1\" id=\"address1\" maxlength=\"100\" class=\"cust_form\" value=\"\" />";}
                                    
                                echo "</td></tr><tr><td align=\"right\">";
                            
                                    if ($label_address2 == '---HIDE---')
                                    {echo " </td><td align=\"left\"><input type=\"hidden\" name=\"address2\" id=\"address2\" value=\"\" />";}
                                    else
                                    {echo "$label_address2: </td><td align=\"left\"><input type=\"text\" style=\"width:457px;\" size=\"20\" name=\"address2\" id=\"address2\" maxlength=\"100\" class=\"cust_form\" value=\"\" />";}
                            
                                echo "</td></tr><tr><td align=\"right\">";
                                
                                    if ($label_address3 == '---HIDE---')
                                    {echo " </td><td align=\"left\" colspan=\"3\"><input type=\"hidden\" name=\"address3\" id=\"address3\" value=\"\" />";}
                                    else
                                    {echo "$label_address3: </td><td align=\"left\" colspan=\"3\"><input type=\"text\" style=\"width:457px;\" size=\"45\" name=\"address3\" id=\"address3\" maxlength=\"100\" class=\"cust_form\" value=\"\" />";}
                            
                                echo "</td></tr><tr><td align=\"right\">";
                            
                                    if ($label_city == '---HIDE---')
                                    {echo " </td><td align=\"left\"><input type=\"hidden\" name=\"city\" id=\"city\" value=\"\" />";}
                                    else
                                    {echo "$label_city: </td><td align=\"left\" colspan=\"5\"><input type=\"text\" size=\"20\" name=\"city\" id=\"city\" maxlength=\"50\" class=\"cust_form\" value=\"\" />";}
                                
                                    if ($label_state == '---HIDE---')
                                    {echo "&nbsp; <input type=\"hidden\" name=\"state\" id=\"state\" value=\"\" />";}
                                    else
                                    {echo "&nbsp; $label_state: <input type=\"text\" style=\"width:30px;\" size=\"4\" name=\"state\" id=\"state\" maxlength=\"2\" class=\"cust_form\" value=\"\" />";}
                                
                                    if ($label_postal_code == '---HIDE---')
                                    {echo "&nbsp; <input type=\"hidden\" name=\"postal_code\" id=\"postal_code\" value=\"\" />";}
                                    else
                                    {echo "&nbsp; $label_postal_code: <input type=\"text\" style=\"width:104px;\" size=\"14\" name=\"postal_code\" id=\"postal_code\" maxlength=\"10\" class=\"cust_form\" value=\"\" />";}
                            
                                echo "</td></tr><tr><td align=\"right\">";
                                
                                    if ($label_province == '---HIDE---')
                                    {echo " </td><td align=\"left\"><input type=\"hidden\" name=\"province\" id=\"province\" value=\"\" />";}
                                    else
                                    {echo "$label_province: </td><td align=\"left\"><input type=\"text\" size=\"20\" name=\"province\" id=\"province\" maxlength=\"50\" class=\"cust_form\" value=\"\" />";}
                            
                                echo "</td></tr><tr><td align=\"right\">";
                                
                                    if ($label_vendor_lead_code == '---HIDE---')
                                    {echo " </td><td align=\"left\"><input type=\"hidden\" name=\"vendor_lead_code\" id=\"vendor_lead_code\" value=\"\" />";}
                                    else
                                    {echo "$label_vendor_lead_code: </td><td align=\"left\"><input type=\"text\" size=\"15\" name=\"vendor_lead_code\" id=\"vendor_lead_code\" maxlength=\"20\" class=\"cust_form\" value=\"\" />";}
                            
                                echo "</td></tr><tr><td align=\"right\">";
                                
                                    if ($label_gender == '---HIDE---')
                                            {
                                            echo "</td><td align=\"left\"><span id=\"GENDERhideFORie\"><input type=\"hidden\" name=\"gender_list\" id=\"gender_list\" value=\"\" /></span>";
                                            }
                                    else
                                    {
                                            echo "$label_gender: </td><td align=\"left\"><span id=\"GENDERhideFORie\"><select size=\"1\" name=\"gender_list\" class=\"cust_form\" id=\"gender_list\"><option value=\"U\">U - Undefined</option><option value=\"M\">M - Male</option><option value=\"F\">F - Female</option></select></span>";
                                            }
                            
                                echo "</td></tr><tr><td align=\"right\">";
                                
                                    if ($label_phone_code == '---HIDE---')
                                    {echo "&nbsp <input type=\"hidden\" name=\"phone_code\" id=\"phone_code\" value=\"\" />";}
                                    else
                                    {echo "$label_phone_code: </td><td align=\"left\"><input type=\"text\" size=\"4\" name=\"phone_code\" id=\"phone_code\" maxlength=\"10\" class=\"cust_form\" value=\"\" />";}
                                
                                echo "$label_phone_number: </td><td align=\"left\">";
                            
                                    if ( (ereg('Y',$disable_alter_custphone)) or (ereg('HIDE',$disable_alter_custphone)) )
                                            {
                                    echo "<span id=\"phone_numberDISP\"> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; </span></font>";
                                    echo "<input type=\"hidden\" name=\"phone_number\" id=\"phone_number\" value=\"\" />";
                                            }
                                    else
                                            {
                                    echo "<input type=\"text\" size=\"20\" name=\"phone_number\" id=\"phone_number\" maxlength=\"16\" class=\"cust_form\" value=\"\" />";
                                            }
                            
                                echo "</td></tr><tr><td align=\"right\">";
                            
                                    if ($label_alt_phone == '---HIDE---')
                                    {echo " </td><td align=\"left\"><input type=\"hidden\" name=\"alt_phone\" id=\"alt_phone\" value=\"\" />";}
                                    else
                                    {echo "$label_alt_phone: </td><td align=\"left\"><input type=\"text\" size=\"14\" name=\"alt_phone\" id=\"alt_phone\" maxlength=\"16\" class=\"cust_form\" value=\"\" />";}
                            
                                echo "</td></tr><tr><td align=\"right\">";
                            
                                    if ($label_security_phrase == '---HIDE---')
                                    {echo " </td><td align=\"left\"><input type=\"hidden\" name=\"security_phrase\" id=\"security_phrase\" value=\"\" />";}
                                    else
                                    {echo "$label_security_phrase: </td><td align=\"left\"><input type=\"text\" size=\"20\" name=\"security_phrase\" id=\"security_phrase\" maxlength=\"100\" class=\"cust_form\" value=\"\" />";}
                            
                                echo "</td></tr><tr><td align=\"right\">";
                            
                                    if ($label_email == '---HIDE---')
                                    {echo " </td><td align=\"left\" colspan=\"3\"><input type=\"hidden\" name=\"email\" id=\"email\" value=\"\" />";}
                                    else
                                    {echo "$label_email: </td><td align=\"left\" colspan=\"3\"><input type=\"text\" size=\"45\" name=\"email\" id=\"email\" maxlength=\"70\" class=\"cust_form\" value=\"\" />";}
                            
                                echo "</td></tr><tr><td align=\"right\">";
                            
                                    if ($label_comments == '---HIDE---')
                                            {
                                    echo " </td><td align=\"left\" colspan=5><input type=\"hidden\" name=\"comments\" id=\"comments\" value=\"\" />\n";
                                            }
                                    else
                                            {
                                    echo "$label_comments: </td><td align=\"left\" colspan=\"5\">";
                                            if ( ($multi_line_comments) )
                                        {echo "<textarea name=\"comments\" id=\"comments\" rows=\"4\" cols=\"60\" class=\"cust_form_text\" value=\"\"></textarea>\n";}
                                            else
                                        {echo "<input type=\"text\" size=\"65\" name=\"comments\" id=\"comments\" maxlength=\"255\" class=\"cust_form\" value=\"\" />\n";}
                                            }
                            
                                    echo "</font></td></tr><tr><td align=\"right\">\n";
                            
                                    if ($per_call_notes == 'ENABLED')
                                            {
                                    echo "Call Notes: ";
                                            if ($agent_call_log_view == '1')
                                                    {echo "<br /><span id=\"CallNotesButtons\"><a href=\"#\" onclick=\"VieWNotesLoG();return false;\">Previous Call Notes</a></span> ";}
                                    echo "</td><td align=\"left\" colspan=\"5\">";
                                            echo "<textarea name=\"call_notes\" id=\"call_notes\" rows=\"5\" cols=\"60\" class=\"cust_form_text\" value=\"\"></textarea>\n";
                                            }
                                    else
                                            {
                                    echo " </td><td align=\"left\" colspan=5><input type=\"hidden\" name=\"call_notes\" id=\"call_notes\" value=\"\" /><span id=\"CallNotesButtons\"></span>\n";
                                            }
                                    ?>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </span>
    
        <span style="position:absolute;top:255px;width:545px;z-index:<?php $zi++; echo $zi ?>;" id="TransferMain">
            <h1>Transfer / Conference Options</h1>
            <span id="x-transfer-options">
                <div style="float: left; width:260px;">
                    <?php
                    if ($hide_xfer_number_to_dial=='ENABLED')
                            {
                            ?>
                    <input type="hidden" name="xfernumber" id="xfernumber" value="<?php echo $preset_populate ?>" /> 
                            <?php
                            }
                    else
                            {
                            ?>
                    <input type="text" size="20" name="xfernumber" id="xfernumber" onKeyPress="return numbersonly(this, event)" placeholder="(XXX) NXX-XXXX" value="<?php echo $preset_populate ?>" /> 
                            <?php
                            }
                    ?>
                    <span id="dialoverride_checkbox"><input type="checkbox" name="xferoverride" id="xferoverride" size="1" value="0">Dial Override</span>
                    <span id="DialBlindTransfer" class="button blueinactive">Blind X-fer</span>
                    <span id="DialWithCustomer" class="button blue"><a href="#" onclick="SendManualDial('YES');return false;">Warm X-fer</a></span>
                    <span id="ParkCustomerDial" class="button blue"><a href="#" onclick="xfer_park_dial();return false;">Park &amp; Dial</a></span>
                </div>
                <div style="float: right; width:270px;">
                    <input type="hidden" size="2" name="xferlength" id="xferlength" maxlength="4"  />
                    <input type="hidden" size="12" name="xferchannel" id="xferchannel" maxlength="200" />
                    <span id="Leave3WayCall" class="button blueinactive"><a href="#" onclick="leave_3way_call('FIRST');return false;">Leave 3-Way</a></span>
                    
                    <font class="body_small_bold"><span id="ParkCounterSpan2">Time on Park: &nbsp; 0</span></font>
                    <span id="ParkControl2" class="button blueinactive">Park 3rd Party</span>
                    <span id="HangupXferLine" class="button blueinactive">Hangup 3rd Party</span>
                    
                    <span id="HangupBothLines" class="button blue"><a href="#" onclick="bothcall_send_hangup();return false;">Hangup Both</a></span>
                </div>
            </span>
            
            <span id="x-transfer-active">
                
            </span>
            
            <span id="x-transfer-local">
                <span id="XfeRDiaLGrouPSelecteD"></span><span id="XfeRCID"></span>
                <span id="XfeRGrouPLisT">
                    <select size="1" name="XfeRGrouP" id="XfeRGrouP" onChange="XferAgentSelectLink();return false;">
                        <option>-- Select Transfer Queue --</option>
                    </select>
                </span>
                <span id="agentdirectlink"><font class="body_small_bold"><a href="#" onclick="XferAgentSelectLaunch();return false;">Show Agents Available for Transfer</a></font></span>
                <span id="consultative_checkbox"><input type="checkbox" name="consultativexfer" id="consultativexfer" size="1" value="0">Consultative X-fer</span>
                <span id="LocalCloser" class="button blue">X-fer to Queue</span> &nbsp; &nbsp;
            </span>
            
            <span id="x-transfer-inactive">
                
                <?php
                if ($enable_xfer_presets=='ENABLED')
                        {
                        ?>
                <span id="PresetPullDown"><a href="#" onclick="generate_presets_pulldown();return false;"><img src="./images/vdc_XB_presetsbutton.gif" border="0" alt="Presets Button" style="vertical-align:middle" /></a></span>
                        <?php
                        }
                else
                        {
                        ?>
                        <font class="body_tiny">
                        <a href="#" onclick="DtMf_PreSet_a();return false;">D1</a> 
                        <a href="#" onclick="DtMf_PreSet_b();return false;">D2</a>
                        <a href="#" onclick="DtMf_PreSet_c();return false;">D3</a>
                        <a href="#" onclick="DtMf_PreSet_d();return false;">D4</a>
                        <a href="#" onclick="DtMf_PreSet_e();return false;">D5</a>
                        </font>
                        <?php
                        }
                ?>
                &nbsp;
                <span id="DialBlindVMail"><img src="./images/vdc_XB_ammessage_OFF.gif" border="0" alt="Blind Transfer VMail Message" style="vertical-align:middle" /></span>
            </span>
        </span>
        
        <span style="position:absolute;top:255px;width:545px;z-index:<?php $zi++; echo $zi ?>;" id="ScriptPanel">
            <h1>Interaction Script<span id="ScriptRefresH"><a href="#" onclick="RefresHScript()"><font style="font-size:9pt;">&nbsp;&nbsp;(Refresh Script)</font></a></span></h1>
            <table border="0">
                <tr>
                    <td align="left" valign="top">
                        
                        <div id="ScriptContents">Sorry, but an interaction script wasn't setup for this campaign. If you believe this is an error, please contact your supervisor.</div>
                    </td>
                </tr>
            </table>
        </span>
        
        <span style="position:absolute;top:255px;width:545px;z-index:<?php $zi++; echo $zi ?>;" id="ActionPanel">
            <h1>Additional Options</h1>
            
            <div>
                <span id="RecorDingFilename"></span>
                <span id="RecorDID"></span>
                <span id="DiaLLeaDPrevieW"><input type="checkbox" name="LeadPreview" size="1" value="0" /> Preview Dial Lead</span>
                <span id="DiaLDiaLAltPhonE"><input type="checkbox" name="DiaLAltPhonE" size="1" value="0" /> Dial Alt Phone</span>
                <span id="ManualQueueChoice"></span>
                <span id="ManuaLDiaLButtons">
                    <span class="action"><a href="#" onclick="NeWManuaLDiaLCalL('NO');return false;"><img src="./images/x-dialnext.png" style="margin-bottom:-15px;" border="0" alt="Manual Dial" /><br /><span id="x-dialnext">Manual Dial</span></a></span>
                    <!--<span class="action"><a href="#" onclick="NeWManuaLDiaLCalL('FAST');return false;"><img src="./images/x-dialnext.png" style="margin-bottom:-15px;" border="0" alt="Dial Current Contact" /><span id="x-dialnext">Fast Dial</span></a></span> -->
                </span>
                
                <span class="action" id="ManualDiaLControl"><a href="#" onclick="ManualDialNext('','','','','','0');"><img src="./images/x-dialnext.png" style="margin-bottom:-15px;" border="0" alt="Dial Next Number" /><br /><span id="x-dialnext">Dial Next #</span></a></span>
                <span class="action" id="RecorDControl" ><a href="#" onclick="conf_send_recording('MonitorConf',session_id,'');return false;"><img src="./images/x-record.png" border="0" alt="Start Recording" /><br /><span id=\"x-recording\">Record</span></a></span>
                <span class="action" id="WebFormSpan"><a href="#" style="opacity:.15;" disabled><img src="./images/x-webform.png" border="0" alt="Web Form" class=\"disabled_img\" /><br /><span id="x-webform">Launch Webform</span></a></span>
                <span class="action" id="CallLogButtons">
                    <a href="#" onclick="VieWCalLLoG();return false;"><img src="./images/x-contact-on.png" border="0" alt="View Call Log" /><br /><span id="x-calllog">My Call Log</span></a>
                </span>
                <?php
                if ($enable_second_webform > 0)
                {echo "<span class=\"action\" id=\"WebFormSpanTwo\"><a href=\"#\" style=\"opacity:.15;\" disabled><img src=\"./images/x-webform.png\" border=\"0\" alt=\"Web Form 2\" class=\"disabled_img\" /><br /><span id=\"x-webform\">Launch 2nd</span></a></span>";}
                ?>
                <?php
                if ($quick_transfer_button_enabled > 0)
                {echo "<span class=\"action\" id=\"QuickXfer\"><a style=\"opacity:.15;\" disabled><img src=\"./images/x-transfer-quick.png\" border=\"0\" alt=\"Quick Transfer\" class=\"disabled_img\" /><br /><span>Quick X-fer</span></a></span>";}
                
                if ($custom_3way_button_transfer_enabled > 0)
                {echo "<span class=\"action\" id=\"CustomXfer\"><a style=\"opacity:.15;\" disabled><img src=\"./images/x-transfer-custom.png\" border=\"0\" alt=\"Custom Transfer\" class=\"disabled_img\" /><br /><span>Custom X-fer</span></a></span>";}
                
                if ($call_requeue_button > 0)
                {echo "<span class=\"action\" id=\"ReQueueCall\"></span>";}
                ?>
            
                <?php if ($INgrpCT > 0) {echo "<span class=\"action\"><a href=\"#\" onclick=\"OpeNGrouPSelectioN();return false;\"><img src=\"./images/x-transfer-custom.png\" border=\"0\" alt=\"My Queues\" /><br /><span>Inbound Queues</span></a></span>";} ?>
                <?php if ($territoryCT > 0) {echo "<span class=\"action\"><a href=\"#\" onclick=\"OpeNTerritorYSelectioN();return false;\"><img src=\"./images/x-transfer-custom.png\" border=\"0\" alt=\"My Territories\" /><br /><span>Territories</span></a></span>";} ?>
                
                <span id="PauseCodeLinkSpan" class="action" style="display: none;"></span>
                
                <span class="action" id="VolumeControlSpan" style="text-align: center; vertical-align: middle; display:inline-block;">
                    <span id="VolumeUpSpan">
                        <img style="opacity:.15;margin-bottom:10px;height:35px;" class=\"disabled_img\" src="./images/x-volume-up.png" border="0" />
                    </span>
                    <span id="VolumeDownSpan">
                        <img style="opacity:.15;margin-bottom:10px;height:35px;" class=\"disabled_img\" src="./images/x-volume-down.png" border="0" />
                    </span>
                    <span id="AgentMuteSpan"></span>
                </span>
                
                <span class="action" id="SendDTMF">
                    <input type="text" size="5" name="conf_dtmf" class="cust_form" value="" maxlength="50" /><br /><br />
                    <a href="#" onclick="SendConfDTMF(session_id);return false;">
                    <img src="./images/x-volume-unmute.png" border="0" alt="Send DTMF" /><br /><span>Send DTMF</span>
                    </a>
                </span>
                
                <span class="action" id="AgentViewLink">
                    <a href="#" onclick="AgentsViewOpen('AgentViewSpan','open');return false;"><img src="./images/x-users-on.png" style="margin-bottom:-15px;" border="0" alt="Other Agents" /><br /><span>Show Agents</span></a>
                </span>
            
                <span id="ManualQueueNotice" class="notice"></span>
                <span id="blind_monitor_notice_span" class="notice"><b><font color="red"> &nbsp; &nbsp; <span id="blind_monitor_notice_span_contents"></span></font></b></span>
                <span id="post_phone_time_diff_span" class="notice"><b><font color="red"><span id="post_phone_time_diff_span_contents"></span></font></b></span>
                <span id="debugbottomspan" class="notice"></span>
                <br />
                <span id="CallbacksButtons" class="notice">
                    <span id="CBstatusSpan">0 Active Callbacks</span>
                </span>
                
                <span id="dialableleadsspan" class="notice" >
                <?php 
                if ($agent_display_dialable_leads > 0)
                        { 
                    echo "Dialable Leads:&nbsp;0";
                        }
                ?>
                </span>
                
                <span id="AgentAlertSpan" class="notice">
                    <?php
                    if ( (ereg('ON',$VU_alert_enabled)) and ($AgentAlert_allowed > 0) )
                            {echo "<a href=\"#\" onclick=\"alert_control('OFF');return false;\">Alert is ON</a>";}
                    else
                            {echo "<a href=\"#\" onclick=\"alert_control('ON');return false;\">Alert is OFF</a>";}
                    ?>
                </span>
                <span class="notice" id="callsinqueuelink">
                <?php 
                if ($view_calls_in_queue > 0)
                        { 
                        if ($view_calls_in_queue_launch > 0) 
                                {echo "<a href=\"#\" onclick=\"show_calls_in_queue('HIDE');\">Hide Calls In Queue</a>\n";}
                        else 
                                {echo "<a href=\"#\" onclick=\"show_calls_in_queue('SHOW');\">Show Calls In Queue</a>\n";}
                        }
                ?>
                </span>
                <span class="notice" id="callsinqueuedisplay"><table cellpadding="0" cellspacing="0" border="0"><tr><td width="5px" rowspan="2">&nbsp;</td><td align="center"><font class="body_text">Calls In Queue: &nbsp; </font></td></tr><tr><td align="center"><span id="callsinqueuelist">&nbsp;</span></td></tr></table></span>
                <span id="busycallsdisplay" class="notice">
                    <a href="#"  onclick="conf_channels_detail('SHOW');">Show conference call channel information</a>
                </span>
                <span id="outboundcallsspan" class="notice"></span>
                
                <?php if ( ($HK_statuses_camp > 0) && ( ($user_level>=$HKuser_level) or ($VU_hotkeys_active > 0) ) ) { ?>
                <span id="hotkeysdisplay" class="notice" ><a href="#" onclick="HotKeys('ON')">Turn On HotKeys</a></span>
                <?php } ?>
                
                <span  class="notice" id="HotKeyEntriesBox">
                    <table border="0" height="70px">
                    <tr><td width="200px"><font class="sh_text"> Disposition Hot Keys: </font></td><td colspan="2">
                        <font class="body_small">When active, simply press the keyboard key for the desired disposition for this call. The call will then be hungup and dispositioned automatically:</font></td></tr><tr>
                    <td width="200px"><font class="sk_text">
                        <span id="HotKeyBoxA"><?php echo $HKboxA ?></span>
                    </font></td>
                    <td width="200px"><font class="sk_text">
                        <span id="HotKeyBoxB"><?php echo $HKboxB ?></span>
                    </font></td>
                    <td><font class="sk_text">
                        <span id="HotKeyBoxC"><?php echo $HKboxC ?></span>
                    </font></td>
                    </tr></table>
                </span>
                <span id="HotKeyActionBox" class="notice">
                    <table border="0" height="70px">
                    <tr><td height="70px">Lead Dispositioned As:<span id="HotKeyDispo"> - </span></td>
                    </tr>
                    </table>
                </span>
                <span class="action" id="AgentMuteANDPreseTDiaL" style="display:none;">
                    <?php
                    if ($PreseT_DiaL_LinKs)
                            {
                            echo "<a href=\"#\" onclick=\"DtMf_PreSet_a_DiaL();return false;\"><font class=\"body_tiny\">D1 - DIAL</font></a>\n";
                    echo " &nbsp; \n";
                            echo "<a href=\"#\" onclick=\"DtMf_PreSet_b_DiaL();return false;\"><font class=\"body_tiny\">D2 - DIAL</font></a>\n";
                            }
                    else {echo "\n";}
                        ?>
                </span>
                <span  class="notice" id="AgentStatusSpan">
                Your Status: <span id="AgentStatusStatus"></span> <br />
                Calls Dialing: <span id="AgentStatusDiaLs"></span>
                </span>
                
                <span class="notice">
                <font face="Arial,Helvetica" size="1">
                    VERSION: <?php echo $version ?> &nbsp;
                    BUILD: <?php echo $build ?> &nbsp; &nbsp;
                    Server: <?php echo $server_ip ?>  &nbsp; &nbsp;
                </font>
                </span>
            </div>
        </span>
            
        <?php if ($custom_fields_enabled > 0)
            {
                echo "
                <span style=\"position:absolute;top:255px;width:545px;z-index:<?php $zi++; echo $zi ?>;\" id=\"FormPanel\">
                    <h1>Interaction Form <span id=\"FormRefresH\"><a href=\"#\" onclick=\"FormContentsLoad()\"><font style=\"font-size:9pt;\">(Refresh Form)</font></a></span></h1>
                    <span >
                        <table border=\"0\"><tr><td align=\"left\" valign=\"top\">
                        <div id=\"FormContents\">
                        <iframe src=\"./vdc_form_display.php?lead_id=&list_id=&stage=WELCOME\" style=\"background-color:transparent;\" scrolling=\"auto\" frameborder=\"0\" allowtransparency=\"true\" id=\"vcFormIFrame\" name=\"vcFormIFrame\" > </iframe>
                        </div></td></tr></table>
                    </span>
                </span>
        ";}
        ?>
    
    </div>
</div>
        
<!-- Loading gif used to display upon login -->
<span id="LoadingBox">
    <img src="./images/loadingx.gif" alt="Loading X Frontline..." />
</span>

<div id="hiddenFields">
    <input type="hidden" name="extension" id="extension" />
    <input type="hidden" name="custom_field_values" id="custom_field_values" value="" />
    <input type="hidden" name="FORM_LOADED" id="FORM_LOADED" value="0" />
    <input type="hidden" name="lead_id" id="lead_id" value="" />
    <input type="hidden" name="list_id" id="list_id" value="" />
    <input type="hidden" name="entry_list_id" id="entry_list_id" value="" />
    <input type="hidden" name="called_count" id="called_count" value="" />
    <input type="hidden" name="rank" id="rank" value="" />
    <input type="hidden" name="owner" id="owner" value="" />
    <input type="hidden" name="gmt_offset_now" id="gmt_offset_now" value="" />
    <input type="hidden" name="gender" id="gender" value="" />
    <input type="hidden" name="date_of_birth" id="date_of_birth" value="" />
    <input type="hidden" name="country_code" id="country_code" value="" />
    <input type="hidden" name="uniqueid" id="uniqueid" value="" />
    <input type="hidden" name="callserverip" id="callserverip" value="" />
    <input type="hidden" name="SecondS" id="SecondS" value="" />
    
    <input type="hidden" name="xferuniqueid" id="xferuniqueid" />
    <input type="hidden" name="xfername" id="xfername" />
    <input type="hidden" name="xfernumhidden" id="xfernumhidden" />
    <span id="sessionIDspan" style="display: none;"></span>
    <span id="MainPanel" style="display:none;"></span>
    <span name="custdatetime" id="custdatetime" style="display:none;"></span>
    <span name="callchannel" id="callchannel" style="display:none;"></span>
</div>

<div id="content" class="clearfix" style="padding-top:0px;">

<span style="display:block;">

    <?php
    $zi++;
    if ($webphone_location == 'bar')
            {
            echo "<span style=\"visibility:hidden;position:absolute;left:0px;top:46px;overflow:hidden;z-index:$zi;background-color:$SIDEBAR_COLOR;\" id=\"webphoneSpan\"><span id=\"webphonecontent\" style=\"overflow:hidden;\">$webphone_content</span></span>\n";
            }
    else
            {
        echo "<span style=\"visibility:hidden;position:absolute;left:0px;top:15px;height:500px;overflow:scroll;z-index:$zi;background-color:$SIDEBAR_COLOR;\" id=\"webphoneSpan\"><table cellpadding=\"$webphone_pad\" cellspacing=\"0\" border=\"0\"><tr><td width=\"5px\" rowspan=\"2\">&nbsp;</td><td align=\"center\"><font class=\"body_text\">
        Web Phone: &nbsp; </font></td></tr><tr><td align=\"center\"><span id=\"webphonecontent\">$webphone_content</span></td></tr></table></span>\n";
            }
    ?>

    <?php 
    if ($is_webphone=='Y')
            { 
            ?>
    
        <span style="position:absolute;left:0px;top:0px;z-index:<?php $zi++; echo $zi ?>;" id="webphoneLinkSpan"><table cellpadding="0" cellspacing="0" border="0" width="120px"><tr><td align="right"><font class="body_small"><span id="webphoneLink"> &nbsp; <a href="#" onclick="webphoneOpen('webphoneSpan','close');return false;">WebPhone View -</a></span></font></td></tr></table></span>
    
            <?php 
            }
    ?>
    
    

    <span style="z-index:<?php $zi++; echo $zi ?>;" id="AgentXferViewSpan"><center>
    Available Agents Transfer: <span id="AgentXferViewSelect"></span></center></span>
    

    <span style="position:absolute;left:5px;z-index:<?php $zi++; echo $zi ?>;" id="CBcommentsBox">
        <table border="0"  height="70px">
        <tr>
        <td align="left"><font class="sh_text"> Previous Callback Information: </font></td>
        <td align="right"><font class="sk_text"> <a href="#" onclick="CBcommentsBoxhide();return false;">close</a> </font></td>
            </tr><tr>
        <td><font class="sk_text">
        <span id="CBcommentsBoxA"></span><br />
        <span id="CBcommentsBoxB"></span><br />
        <span id="CBcommentsBoxC"></span><br />
        </font></td>
        <td width="320px"><font class="sk_text">
            <span id="CBcommentsBoxD"></span>
        </font></td>
        </tr></table>
    </span>
    
    <span style="position:absolute;left:5px;z-index:<?php $zi++; echo $zi ?>;" id="EAcommentsBox">
        <table border="0" height="70px">
        <tr>
        <td align="left"><font class="sh_text"> Extended Alt Phone Information: </font></td>
        <td align="right"><font class="sk_text"> <a href="#" onclick="EAcommentsBoxhide('YES');return false;"> minimize </a> </font></td>
            </tr><tr>
        <td valign="top"><font class="sk_text">
        <span id="EAcommentsBoxC"></span><br />
        <span id="EAcommentsBoxB"></span><br />
        </font></td>
        <td width="320px" valign="top"><font class="sk_text">
        <span id="EAcommentsBoxA"></span><br />
            <span id="EAcommentsBoxD"></span>
        </font></td>
        </tr></table>
    </span>
    
    <span style="position:absolute;z-index:<?php $zi++; echo $zi ?>;" id="EAcommentsMinBox">
        <table border="0" width="40px" height="20px">
        <tr>
        <td align="left"><font class="sk_text"><a href="#" onclick="EAcommentsBoxshow();return false;"> maximize </a> <br />Alt Phone Info</font></td>
        </tr></table>
    </span>
    
    <span style="z-index:<?php $zi++; echo $zi ?>;" class="promptBox" id="NoneInSessionBox">
        <table class="centerTable"><tr><td align="center"> No one is in your session: <span id="NoneInSessionID"></span><br />
            <a href="#" onclick="NoneInSessionOK();return false;" class="button blue" style="width: 100px;">Go Back</a>
        <br /><br />
            <span id="NoneInSessionLink"><a href="#" onclick="NoneInSessionCalL();return false;" class="button blue" style="width: 150px;">Call Phone Again</a></span>
        </td></tr></table>
    </span>
    
    <span style="z-index:<?php $zi++; echo $zi ?>;" class="promptBox" id="CustomerGoneBox">
        <table class="centerTable"><tr><td align="center"><span id="CustomerGoneChanneL"></span><br />
            <a href="#" onclick="CustomerGoneOK();return false;" class="button blue" style="width: 100px;">Go Back</a>
        <br /><br />
            <a href="#" onclick="CustomerGoneHangup();return false;" class="button blue" style="width: 130px;">Disposition Call</a>
        </td></tr></table>
    </span>
    
    <span style="z-index:<?php $zi++; echo $zi ?>;" class="promptBox" id="WrapupBox">
        <table class="centerTable"><tr><td align="center"> Call Wrapup: <span id="WrapupTimer"></span> seconds remaining in wrapup<br /><br />
            <span id="WrapupMessage"><?php echo $wrapup_message ?></span>
        <br /><br />
            <a href="#" onclick="WrapupFinish();return false;" class="button blue" style="width: 240px;">Finish Wrap and Move On</a>
        </td></tr></table>
    </span>
    
    <span style="z-index:<?php $zi++; echo $zi ?>;" class="promptBox" id="TimerSpan">
        <table  class="centerTable"><tr><td align="center">
        <br /><span id="TimerContentSpan"></span><br /><br />
            <a href="#" onclick="hideDiv('TimerSpan');return false;" class="button blue" style="width: 110px;">Close Message</a>
        </td></tr></table>
    </span>
    
    <span style="z-index:<?php $zi++; echo $zi ?>;" class="promptBox" id="AgenTDisablEBoX">
        <table class="centerTable"><tr><td align="center">Your session has been disabled<br />
        <a href="#" onclick="LogouT('DISABLED');return false;" class="button blue" style="width: 100px;">Log Out</a><br /><br />
        <a href="#" onclick="hideDiv('AgenTDisablEBoX');return false;" class="button blue" style="width: 100px;">Go Back</a>
        </td></tr></table>
    </span>
    
    <span style="z-index:<?php $zi++; echo $zi ?>;" class="promptBox" id="SysteMDisablEBoX">
        <table class="centerTable"><tr><td align="center">
        There is a time synchronization problem with your system, please tell your system administrator
        <br /><br /><br /><a href="#" onclick="hideDiv('SysteMDisablEBoX');return false;" class="button blue" style="width: 100px;">Go Back</a>
        </td></tr></table>
    </span>
    
    <span style="z-index:<?php $zi++; echo $zi ?>;" class="promptBox" id="LogouTBox">
        <table class="centerTable"><tr><td align="center"><br /><span id="LogouTBoxLink">Log Out</span></td></tr></table>
    </span>
    
    <span style="position:absolute;left:0px;top:70px;z-index:<?php $zi++; echo $zi ?>;" id="DispoButtonHideA">
        <table class="centerTable" border="0" width="165px" height="22px"><tr><td align="center" valign="top"></td></tr></table>
    </span>
    
    <span style="position:absolute;left:0px;top:138px;z-index:<?php $zi++; echo $zi ?>;" id="DispoButtonHideB">
        <table class="centerTable" border="0" width="165px" height="250px"><tr><td align="center" valign="top">&nbsp;</td></tr></table>
    </span>
    
    <span style="z-index:<?php $zi++; echo $zi ?>;" class="promptBox" id="DispoButtonHideC">
        <table class="centerTable" border="0" height="47px"><tr><td align="center" valign="top">
        Any changes made to the customer information below at this time will not be comitted,
        You must change customer information before you hangup the call. </td></tr></table>
    </span>
    
    <span style="z-index:<?php $zi++; echo $zi ?>;" class="promptBox" id="DispoSelectBox">
        <table>
        <tr>
        <td align="center" valign="top"> Disposition Call :<span id="DispoSelectPhonE"></span> &nbsp; &nbsp; &nbsp;
        <span id="DispoSelectHAspan"><a href="#" onclick="DispoHanguPAgaiN()">Hangup Again</a></span> &nbsp; &nbsp; &nbsp;
        <span id="DispoSelectMaxMin" style="display: none;"><a href="#" onclick="DispoMinimize()"> Minimize </a></span><br />
        <span id="Dispo3wayMessage"></span>
        <span id="DispoManualQueueMessage"></span>
        
        <span id="DispoSelectContent"> End-of-call Disposition Selection </span>
        <input type="hidden" name="DispoSelection" id="DispoSelection" />
        <span id="PerCallNotesContent"><input type="hidden" name="call_notes_dispo" id="call_notes_dispo" value="" /></span><br /><br />
        <input type="checkbox" name="DispoSelectStop" id="DispoSelectStop" size="1" value="0" /> Go to Pause after dispositioning <br />
            <a href="#" onclick="DispoSelectContent_create('','ReSET');return false;" class="button blue" style="width: 100px;">Reset</a> | 
            <a href="#" onclick="DispoSelect_submit();return false;" class="button blue" style="width: 100px;">Submit</a>
        <br /><br />
            <a href="#" onclick="WeBForMDispoSelect_submit();return false;" class="button blue" style="width: 210px;">Open Web Form &amp; Submit</a>
        <br /><br /> &nbsp;
        </td></tr></table>
    </span>
    
    <span style="z-index:<?php $zi++; echo $zi ?>;" class="promptBox" id="CallBackSelectBox">
        <table><tr><td align="center" valign="top"> <h3>Select a CallBack Date / Time </h3><span id="CallBackDatE"></span><br />
        <input type="hidden" name="CallBackDatESelectioN" id="CallBackDatESelectioN" />
        <input type="hidden" name="CallBackTimESelectioN" id="CallBackTimESelectioN" />
            <span id="CallBackDatEPrinT">Select a Date Below</span> &nbsp;
            <span id="CallBackTimEPrinT"></span> &nbsp; &nbsp;
            Hour: 
        <select size="1" name="CBT_hour" id="CBT_hour">
            <option>01</option>
            <option>02</option>
            <option>03</option>
            <option>04</option>
            <option>05</option>
            <option>06</option>
            <option>07</option>
            <option>08</option>
            <option>09</option>
            <option>10</option>
            <option>11</option>
            <option>12</option>
            </select> &nbsp;
            Minutes: 
        <select size="1" name="CBT_minute" id="CBT_minute">
            <option>00</option>
            <option>05</option>
            <option>10</option>
            <option>15</option>
            <option>20</option>
            <option>25</option>
            <option>30</option>
            <option>35</option>
            <option>40</option>
            <option>45</option>
            <option>50</option>
            <option>55</option>
            </select> &nbsp;
    
        <select size="1" name="CBT_ampm" id="CBT_ampm">
            <option>AM</option>
            <option selected>PM</option>
        </select> &nbsp;<br /><br />
            <span id="CallBackDateContent"><?php echo  "$CCAL_OUT" ?></span>
            <br />&nbsp;<?php
            if ($agentonly_callbacks)
            {echo "<input type=\"checkbox\" name=\"CallBackOnlyMe\" id=\"CallBackOnlyMe\" size=\"1\" value=\"0\" /> My Callback Only <br /><br />";}
            ?>
        Callback Comments: <br>
        <textarea name="CallBackCommenTsField" id="CallBackCommenTsField" rows="4" cols="70"></textarea><br /><br />
        <a href="#" onclick="CallBackDatE_submit();return false;" class="button blue" style="width: 100px;">Submit</a>
        </td></tr></table>
    </span>
    
    <span style="z-index:<?php $zi++; echo $zi ?>;" class="promptBox" id="CallBacKsLisTBox">
        <table>
            <tr>
                <td align="center" valign="top">
                    <h1>Callbacks for <?php echo $VD_login ?></h1>&nbsp;&nbsp;<a href="#" onclick="CalLBacKsLisTClose();return false;">Close</a>
                </td>
            </tr>
            <tr>
                <td>
                    To see information on one of the callbacks below, click on the INFO link. To call the customer back now, click on the DIAL link.
                    If you click on a record below to dial it, it will be removed from the list.
                </td>
            </tr>
            <tr>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td  align="center">
                    <div class="scroll_callback" id="CallBacKsLisT"></div><br /> &nbsp;
                    <a href="#" onclick="CalLBacKsLisTCheck();return false;" class="button blue" style="width: 100px;">Refresh</a>
                     &nbsp;  &nbsp;  &nbsp;  &nbsp;  &nbsp;  &nbsp;  &nbsp;  &nbsp; 
                    <a href="#" onclick="CalLBacKsLisTClose();return false;" class="button blue" style="width: 100px;">Close</a>
                </td>
            </tr>
        </table>
    </span>
    
    <span style="z-index:<?php $zi++; echo $zi ?>;" class="promptBox" id="NeWManuaLDiaLBox">
        <table>
            <tr>
                <td align="center" valign="top">
                    <h1>Manual Dial <!--<?php echo "$VD_login in campaign $VD_campaign" ?>--></h1>
                </td>
            </tr>
            <tr>
                <td>
                    Enter the phone number you wish to call in the field below. Click <i>Dial Now</i> if you're ready to place
                    the call or click <i>Preview Dial</i> to review the contact's information prior to dialing.
                </td>
            </tr>
            <tr>
                <td align="center">
                    <!--
                        <?php 
                        if (!eregi("X",$manual_dial_prefix))
                                {
                        echo "Note: a dial prefix of $manual_dial_prefix will be added to the beginning of this number<br />\n";
                                }
                        ?>
                    Note: all new manual dial leads will go into list <?php echo $manual_dial_list_id ?><br /><br />
                    -->
                    <table>
                        <tr style="display: none;">
                            <td align="right"><font class="body_text"> Dial Code: </font></td>
                            <td align="left"><font class="body_text"><input type="text" size="7" maxlength="10" name="MDDiaLCodE" id="MDDiaLCodE" class="input" value="1" />&nbsp; (This is usually a 1 in the USA-Canada)</font></td>
                        </tr>
                        <tr>
                            <td colspan="2">&nbsp;</td>
                        </tr>
                        <tr>
                            <td align="right"><font class="body_text"> Phone Number: </font></td>
                            <td align="left">
                                <input type="text" size="14" maxlength="15" name="MDPhonENumbeR" id="MDPhonENumbeR" onKeyPress="return numbersonly(this, event)" class="input" value="" />
                                <input type="hidden" name="MDLeadID" id="MDLeadID" value="" />
                                <input type="hidden" name="MDType" id="MDLeadID" value="" />
                            </td>
                        </tr>
                        <tr>
                            <td align="right"><font class="body_text"> <input type="checkbox" name="LeadLookuP" id="LeadLookuP" size="1" value="1" /> </font></td>
                            <td align="left">
                                <font class="body_text">
                                &nbsp; Try to find this person in the system first and associate this call with them</font>
                            </td>
                        </tr>
                        <tr style="display: none;">
                            <td align="left" colspan="2">
                                <br /><br />
                                <CENTER>
                                    <span id="ManuaLDiaLGrouPSelecteD"></span> &nbsp; &nbsp; <span id="ManuaLDiaLGrouP"></span>
                                </CENTER>
                                <br /><br />
                                If you want to dial a number and have it NOT be added as a new lead, enter in the exact dialstring that you want to
                                call in the Dial Override field below. To hangup this call you will have to open the CALLS IN THIS SESSION link at
                                the bottom of the screen and hang it up by clicking on its channel link there.<br /> &nbsp;
                            </td>
                        </tr>
                        <tr  style="display: none;">
                            <td align="right"><font class="body_text"> Dial Override: </font></td>
                            <td align="left">
                                <font class="body_text"><input type="text" size="24" maxlength="20" name="MDDiaLOverridE" id="MDDiaLOverridE" class="input" value="" />&nbsp; (digits only please)</font>
                            </td>
                        </tr>
                    </table>
                    <br />
                    <a href="#" onclick="NeWManuaLDiaLCalLSubmiT('NOW');return false;" class="button blue" style="width: 100px;">Dial Now</a>
                     &nbsp;  &nbsp;  
                    <a href="#" onclick="NeWManuaLDiaLCalLSubmiT('PREVIEW');return false;" class="button blue" style="width: 120px;">Preview Dial</a>
                     &nbsp;  &nbsp; 
                    <a href="#" onclick="ManualDialHide();return false;" class="button blue" style="width: 100px;">Close</a>
                </td>
            </tr>
        </table>
    </span>
    
    <span style="z-index:<?php $zi++; echo $zi ?>;" class="promptBox" id="CloserSelectBox">
        <table><tr><td align="center" valign="top"> <h1>Select Inbound Queues</h1> <br />
            <span id="CloserSelectContent"> Inbound Queues </span>
        <input type="hidden" name="CloserSelectList" id="CloserSelectList" /><br />
            <?php
            if ( ($outbound_autodial_active > 0) and ($disable_blended_checkbox < 1) and ($dial_method != 'INBOUND_MAN') and ($VU_agent_choose_blended > 0) )
                    {
                    ?>
            <input type="checkbox" name="CloserSelectBlended" id="CloserSelectBlended" size="1" value="0" /> Blended (Allows Out/In) <br />
                    <?php
                    }
            ?>
            <a href="#" onclick="CloserSelectContent_create();return false;" class="button blue" style="width: 100px;"> Reset </a> | 
            <a href="#" onclick="CloserSelect_submit();return false;" class="button blue" style="width: 100px;">Submit </a>
        <br /><br /><br /><br /> &nbsp;
        </td></tr></table>
    </span>
    
    <span style="z-index:<?php $zi++; echo $zi ?>;" class="promptBox" id="TerritorySelectBox">
        <table><tr><td align="center" valign="top"> <h1>Select Territories</h1> <br />
            <span id="TerritorySelectContent"> Territories </span>
        <input type="hidden" name="TerritorySelectList" id="TerritorySelectList" /><br />
            <a href="#" onclick="TerritorySelectContent_create();return false;" class="button blue" style="width: 100px;"> Reset </a> | 
            <a href="#" onclick="TerritorySelect_submit();return false;" class="button blue" style="width: 100px;">Submit</a>
        <br /><br /><br /><br /> &nbsp;
        </td></tr></table>
    </span>
    
    <span style="z-index:<?php $zi++; echo $zi ?>;" class="promptBox" id="NothingBox">
            <span id="DiaLLeaDPrevieWHide"> Channel</span>
            <span id="DiaLDiaLAltPhonEHide"> Channel</span>
            <?php
            if (!$agentonly_callbacks)
            {echo "<input type=\"checkbox\" name=\"CallBackOnlyMe\" id=\"CallBackOnlyMe\" size=\"1\" value=\"0\" /> My Callback Only <br />";}
            if ( ($outbound_autodial_active < 1) or ($disable_blended_checkbox > 0) or ($dial_method == 'INBOUND_MAN') or ($VU_agent_choose_blended < 1) )
            {echo "<input type=\"checkbox\" name=\"CloserSelectBlended\" id=\"CloserSelectBlended\" size=\"1\" value=\"0\" /> Blended Calling<br />";}
            ?>
    </span>
    
    <span style="z-index:<?php $zi++; echo $zi ?>;" class="promptBox" id="CalLLoGDisplaYBox">
            <table><tr><td align="center" valign="top"> &nbsp; <h1>My Call Log</h1> &nbsp; &nbsp; &nbsp; &nbsp; <a href="#" onclick="CalLLoGVieWClose();return false;">Close</a><br />
            <div class="scroll_calllog" id="CallLogSpan"> Call Log History </div>
            <br /><br /> &nbsp;
            </td></tr></table>
    </span>
    
    <span style="z-index:<?php $zi++; echo $zi ?>;" class="promptBox" id="SearcHForMDisplaYBox">
            <table><tr><td align="center" valign="top"> &nbsp; <h1>Search for Contact</h1> &nbsp; &nbsp; &nbsp; <a href="#" onclick="LeaDSearcHVieWClose();return false;">Close</a><br />
            <br />
            Quickly search for a contact using one of the fields below. <i>Hint:</i> Phone number is the best and faster search option.
            <br /><br />
            <!--
            Notes: when doing a search for a lead, the phone number, lead ID or <?php echo $label_vendor_lead_code ?> are the best fields to use. <br />
            Using the other fields may be slower. Lead searching does not allow for wildcard or partial search terms. <br />
            Lead search requests are all logged in the system.
            <br /><br />
            -->
            <center>
            <table border="0">
            <tr>
            <td align="right"> Phone Number: </td><td align="left"><input type="text" size="18" maxlength="20" name="search_phone_number" id="search_phone_number" class="input"></td>
            </tr>
            <tr>
            <td align="right"> Phone Fields to Search: </td>
            <td align="left">
            <input type="checkbox" name="search_main_phone" id="search_main_phone" size="1" value="0" checked /> Main Phone
            <input type="checkbox" name="search_alt_phone" id="search_alt_phone" size="1" value="0" checked /> Alt. Phone
            <span style="display:none;"><input type="checkbox" name="search_addr3_phone" id="search_addr3_phone" size="1" value="0" /> Address3 Phone Number</span>
            </td>
            </tr>
            <tr>
            <td align="right"> Lead ID: </td><td align="left"><input type="text" size="11" maxlength="10" name="search_lead_id" id="search_lead_id" class="input"></td>
            </tr>
            <tr>
            <td align="right"> <?php echo $label_vendor_lead_code ?>: </td><td align="left"><input type="text" size="18" maxlength="20" name="search_vendor_lead_code" id="search_vendor_lead_code" class="input"></td>
            </tr>
            <tr>
            <td align="right"> <?php echo $label_first_name ?>: </td><td align="left"><input type="text" size="18" maxlength="20" name="search_first_name" id="search_first_name" class="input"></td>
            </tr>
            <tr>
            <td align="right"> <?php echo $label_last_name ?>: </td><td align="left"><input type="text" size="18" maxlength="20" name="search_last_name" id="search_last_name" class="input"></td>
            </tr>
            <tr>
            <td align="right"> <?php echo $label_city ?>: </td><td align="left"><input type="text" size="18" maxlength="20" name="search_city" id="search_city" class="input"></td>
            </tr>
            <tr>
            <td align="right"> <?php echo $label_state ?>: </td><td align="left"><input type="text" size="18" maxlength="20" name="search_state" id="search_state" class="input"</td>
            </tr>
            <tr>
            <td align="right"> <?php echo $label_postal_code ?>: </td><td align="left"><input type="text" size="10" maxlength="10" name="search_postal_code" id="search_postal_code" class="input"></td>
            </tr>
            <tr>
            <td align="center" colspan="2"><br />
                <a href="#" onclick="LeadSearchSubmit();return false;" class="button blue" style="width: 100px;">Search</a>
                &nbsp; &nbsp; <a href="#" onclick="LeadSearchReset();return false;" class="button blue" style="width: 100px;">Reset</a>
                &nbsp;  &nbsp; <a href="#" onclick="LeaDSearcHVieWClose();return false;" class="button blue" style="width: 100px;">Close</a>
            </td>
            </tr>
            </table>
            <br /><br /> &nbsp;
            </td></tr></table>
    </span>
    
    
    <span style="z-index:<?php $zi++; echo $zi ?>;" class="promptBox" id="SearcHResultSDisplaYBox">
            <table><tr><td align="center" valign="top"> &nbsp; <h1>Search Results</h1>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; <a href="#" onclick="hideDiv('SearcHResultSDisplaYBox');return false;">Close</a><br />
            <div class="scroll_calllog" id="SearcHResultSSpan"> Search Results </div>
            <br /><br /> &nbsp;
            </td></tr></table>
    </span>
    
    <span style="z-index:<?php $zi++; echo $zi ?>;" class="promptBox" id="CalLNotesDisplaYBox">
            <table><tr><td align="center" valign="top"> &nbsp; <h1>Call Notes Log</h1> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; <a href="#" onclick="hideDiv('CalLNotesDisplaYBox');return false;">Close</a><br />
            <div class="scroll_calllog" id="CallNotesSpan"> Call Notes List </div>
            <br /><br /> &nbsp;
            <a href="#" onclick="hideDiv('CalLNotesDisplaYBox');return false;" class="button blue" style="width: 100px;">Close</a>
            </td></tr></table>
    </span>
    
    <span style="z-index:<?php $zi++; echo $zi ?>;" class="promptBox" id="LeaDInfOBox">
            <table><tr><td align="center" valign="top"> &nbsp; <h1>Contact Information</h1> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; <a href="#" onclick="hideDiv('LeaDInfOBox');return false;">Close</a>
            <br />
            <span id="LeaDInfOSpan"> Contact Details </span>
            <br /><br /> &nbsp;
            <a href="#" onclick="hideDiv('LeaDInfOBox');return false;" class="button blue" style="width: 100px;">Close</a>
            </td></tr></table>
    </span>
    
    <span style="z-index:<?php $zi++; echo $zi ?>;" class="promptBox" id="PauseCodeSelectBox">
            <table><tr><td align="center" valign="top"> <h1>Select Pause Code</h1><br />
            <span id="PauseCodeSelectContent"> Pause Code Selection </span>
            <input type="hidden" name="PauseCodeSelection" id="PauseCodeSelection" />
            <br /><br /> &nbsp;
            </td></tr></table>
    </span>
    
    <span style="position:absolute;z-index:<?php $zi++; echo $zi ?>;" id="PresetsSelectBox">
            <table border="0" width="400px" height="<?php echo $HTheight ?>px"><tr><td align="center" valign="top"> Select a Preset :<br />
            <span id="PresetsSelectBoxContent"> Presets Selection </span>
            <input type="hidden" name="PresetSelection" id="PresetSelection" />
            </td></tr></table>
    </span>
    
    <span style="z-index:<?php $zi++; echo $zi ?>;" class="promptBox" id="GroupAliasSelectBox">
            <table><tr><td align="center" valign="top"> Select a Group Alias :<br />
            <span id="GroupAliasSelectContent"> Group Alias Selection </span>
            <input type="hidden" name="GroupAliasSelection" id="GroupAliasSelection" />
            <br /><br /> &nbsp;
            </td></tr></table>
    </span>
    
    <span style="z-index:<?php $zi++; echo $zi ?>;" class="promptBox" id="blind_monitor_alert_span">
            <table><tr><td align="center" valign="top"> Alert :<br />
            <b><font color="red" size="5"> &nbsp; &nbsp; <span id="blind_monitor_alert_span_contents"></span></b></font>
            <br /><br /> <a href="#" onclick="hideDiv('blind_monitor_alert_span');return false;">Close</a>
            </td></tr></table>
    </span>
    
    <span style="position:absolute;left:0px;z-index:<?php $zi++; echo $zi ?>;" id="GENDERhideFORieALT"></span>
    
</span>

</div>
</form>


<form name="inert_form" id="inert_form" onsubmit="return false;">

<span style="position:absolute;left:0px;top:400px;z-index:1;" id="NothingBox2">
<!--  <BUTTON Type=button name="inert_button" id="inert_button" onclick="return false;"><img src="./images/blank.gif" /></BUTTON> -->
<input type="checkbox" name="inert_button" id="inert_button" size="1" value="0" onclick="return false;" />
</span>

</form>

<form name="alert_form" id="alert_form" onsubmit="return false;">
    <div id="content" class="clearfix" style="padding-top:0px;">
        <span style="visibility: hidden;z-index:<?php $zi++; echo $zi ?>;" class="promptBox" id="AlertBox">
            <table class="centerTable" style="margin-left: 25%;" border="2" bgcolor="#666666" cellpadding="2" cellspacing="1">
                <tr><td bgcolor="#f0f0f0" align="left">
                <font face="arial,helvetica" size="2"><b> &nbsp; Agent Alert!</b></font>
                </td></tr>
                <tr><td bgcolor="#E6E6E6">
                <table border="0" bgcolor="#E3E3E3" width="400">
                <tr>
                <td align="center" valign="top" width="50"> &nbsp; 
                <br /><br />
                <img src="./images/alert.gif" alt="alert" border="0">
                </td>
                <td align="center" valign="top"> &nbsp; 
                <br /><br />
                <font face="arial,helvetica" size="2">
                <span id="AlertBoxContent"> Alert Box </span>
                </font>
                <br /><br />
                </td>
                </tr><tr>
                <td align="center" valign="top" colspan="2">
                <button type="button" name="alert_button" id="alert_button" class="button blue" onclick="hideDiv('AlertBox');return false;">OK</BUTTON>
                <br /> &nbsp;
                <!-- <a href="#" onclick="document.alert_form.alert_button.focus();">focus</a> -->
                </td></tr>
                </table>
                </td></tr>
            </table>
        </span>
    </div>
</form>


</body>
</html>

<?php
	
exit; 

?>
