+------------------------------------------------------------------------------+
|    ViciDial Call Center - astguiclient - thirtieth public release 2.0.5      |
|              created by ViciDial group <vicidial@gmail.com>                  |
|  project started 2003-10-06   http://sourceforge.net/projects/astguiclient/  |
+------------------------------------------------------------------------------+

This suite of programs is designed to work with the Asterisk Open-source PBX
(http://www.asterisk.org) as a cross-platform GUI client and the supporting 
server side applications necessary for the features of the GUI application to 
work with Asterisk. The client-side GUI apps are available as web pages running
AJAX scripted web pages(Firefox recommended)

End-user Manuals for Agents and Managers are available from http://www.eflo.net

 ***** Description, Notes and Changelog below *****

Included in this distribution of the Asterisk GUI client are:
- agi/ - the Asterisk agi-bin files needed for astguiclient
- bin/ - the astGUIclient server-side perl script applications
- docs/ - astGUIclient installation and other documentation
- docs/conf_examples/ - example Asterisk conf files for astGUIclient
- extras/ - data files, sql files and other extra files needed for ViciDial
- LANG_www/ - ViciDial admin and user web pages translated into other languages
- sounds/ - Asterisk sounds needed by ViciDial
- translations/ - UTF8 language translation files for other language versions
- www/ - the pages and scripts of the user and admin astGUIclient web interface


DESCRIPTION:
This program was designed as a GUI client for the Asterisk PBX with Zaptel 
trunks or IAX or SIP VOIP trunks and SIP/IAX/Zap hard or softphones or devices
as extensions, it could be adapted to other functions, but it was designed for 
Zap/IAX/SIP users. The client programs are web-based and will run on most 
modern web browsers.


KNOWN BUGS:
- With ViciDial(and Asterisk in general) you cannot record a native bridge 
  VOIP-only conversation unless you go into a conference, you also cannot 
  monitor a native-bridge VOIP-only conversation unless you use a separate 
  specialized application like OrecX


VERSION HISTORY:

0.7 - First public release - 2003-11-18
This is the first release of the Asterisk GUI Client code, it is entirely 
written in Perl with portability and rapid development/testing in mind. The perl 
code is NOT strict, it was written loose and fast and has been functioning 
rather well in a production environment of 60 clients for one month now.

0.8 - Second public release - 2003-12-09
- Several bug fixes
- New button for monitoring live extensions on Zap channels
- Changed the method that the live channels/phone were populated on the 
listboxes of the client app.
- Changed the Asterisk/Manager commands to work correctly with new Asterisk CVS 
versions requirements. 
- A new routine was enabled to allow for making sure that the updater is   
running and bringing up a popup alert window on the client if the updater has 
not updated in the last 6 seconds. (this also added a new MySQL table)
- Changed the updater to run every 450 milliseconds instead of every 333 
milliseconds. 
- Updater changed to allow for ringing channels to appear in the live_channels 
table. 

0.9 - Third public release - 2004-02-05
The majority of the work in this release it to make it more stable and fix some 
pretty bad bugs. We created the Asterisk Central Queue System to address the 
problem with buffer-overflows in the manager interface of Asterisk causing total 
system deadlocks. We also completed and touched-up many other features that we 
didn't finish in previous releases. Here is the list of changes:
- Several bug fixes
- Inclusion of listing for active SIP/Local channels and ability to hang them up
- Completely changed the method of conferencing to be more fluid
- Added HELP popup screen
- Added intrasystem calling funtionality
- Updater changed to allow for SIP/Local channels
- Recording for conferences is now able to record all audio in and out
- Added ability to send DTMF tones within a conference
- Changed alert window for updater being down timeout to 20 seconds
- Added an option for using the new Asterisk Central Queue System(ACQS) that 
reduces the risk of deadlocks that occur with buffer-overflows on remote manager 
interface connections
- Included new script to run at boot time and rotate the logs as well as a 
keepalive script for the new ACQS 
- Changed non-AGI server-side scripts to allow for a single config file
- Detailed activity logging to text file option added
- Activity logging added to all non-AGI server applications

0.9.2 - Fourth public release - 2004-03-08
- several bug fixes for the GUI client and ACQS applications
- addition of the new VICIDIAL auto-dialer application and admin web pages
- added new script to reset MySQL tables
- added new script to kill CONGEST Local/ channels

0.9.4 - Fifth public release - 2004-03-12
- a few bug fixes for the GUI and server apps
- addition of callerID popup for the GUI
- callerID buttons to launch web pages from callerID popup
- addition of voicemail count display to GUI
- addition of button to directly connect to voicemail
- addition of voicemail counter updater
- MySQL table "phones" modified and "inbound" table added(see CONF_MySQL.txt)
- new extensions.conf entries need to be added to use callerid and vmail GUI

1.0.0 - Sixth public release - 2004-03-26
- a few bug fixes for the GUI and server apps
- Major documentation changes with the addition of the SCRATCH_INSTALL.txt file 
that goes step-by-step through the entire install process of an asterisk server 
from blank hardware to astguiclient installation and configuration.
- added pretty buttons to the WINphoneAPP
- added blind transfer to voicemail, blind transfer to another extension and 
blind external transfer
- new install script created to put server files in default positions and set 
permissions to execute
- new sql file to create all tables by running a script execute command in MySQL

1.0.1 - Seventh public release - 2004-04-27
- a few bug fixes for the GUI and server apps
- minor changes to WINphoneAPP
- major changes to VICIDIAL client code:
   + rearranged VICIDIAL buttons and added some color to DIAL and HANGUP buttons
   + allowed for call parking with different park music per-campaign
   + allowed for webpage forms launched from GUI with call info
   + allowed for different web form web page per-campaign
   + allowed agents using VICIDIAL client to send calls with data to a closer
   + new call-closer functionality is web-based for flexibility
   + added new DTMF dialpad for sending tones quickly
- fixed bugs in VICIDIAL web admin pages
- fixed bugs in ASTGUICLIENT web admin pages
- added code from Paul Concepcion to allow admin pages to work with globals off
- added common database connection file for each set of PHP admin pages
- made small changes to the MySQL database Schema
  (NOTE: if upgrading from 1.0.0 run the upgrade_1.0.1.sql script in MySQL)

1.0.2 - Eighth public release - 2004-06-03
- a few bug fixes for the GUI apps
- corrected documentation errors in SCRATCH_INSTALL instructions
- updated SCRATCH_INSTALL instructions for some newer software
- corrected errors in SQL install queries
- corrected errors in server installation scripts
- WINphoneAPP config option to allow one persistant mysql DB connection
- WINphoneAPP config option to allow modification of info refresh interval
- VICIDIAL modified to allow for auto-dial of next number
  (NOTE: if upgrading from 1.0.1 you do not need to update any web pages or 
   server apps, this update is only a client GUI and docs update. Note that the 
   AST_VICI_conf.pl has been updated, so you may need to modify your client 
   configs to enable new features.)

1.0.3 - Ninth public release - 2004-07-21
- a few bug fixes for the GUI apps
- changed the recording to show recording ID when recording is started
- VICIDIAL overhauled:
  + added Time::HiRes module requirement to better control time increments
  + changed to dial from a small hopper of pre-ordered leads per campaign
  + added cron script to always keep leads in the hopper every minute
  + added a counter to see how many times a lead is called
  + added ability to dial campaign by how many times lead called
  + added some new stats to the admin web pages
  + created a limited predictive-dialer that will dial a certain amount of leads
  per logged in agent and direct the calls that are picked up to the next agent
  + ability to transfer the called line and the 3rd party call into a separate 
  meetme room and continue on dialing
 UPGRADE NOTES:
  * if upgrading from 1.0.2 you need to update the web pages and all 
   server apps. 
  * if upgrading from 1.0.2 run the upgrade_1.0.3.sql script in MySQL
  * if upgrading from 1.0.2 you may want to run AST_upgrade_1.0.3.pl to update
   your called counts of your leads in vicidial_list.
  * AST_SERVER_conf.pl has been updated, so you may need to modify your server 
   configs to enable new features.
  * client app names have been changed to astVICIDIAL and astGUIclient

1.0.4 - Tenth public release - 2004-09-21
- several minor bug fixes for GUI apps and server apps
- fixed recording bug in astGUIclient that would rarely stop recording
- added timezone dialing in VICIDIAL
- added new client GUI app for inserting leads into the VICIDIAL leads table
- adjusted timings in the VICIDIAL autodialer for better performance
- added streamlined manager interface logins to help scripts run more smoothly
 UPGRADE NOTES:
  * if upgrading from 1.0.3 you need to update the web pages and all 
   server apps. 
  * if upgrading from 1.0.3 run the upgrade_1.0.4.sql script in MySQL
  * if upgrading from 1.0.3 you may want to run ADMIN_adjust_GMTnow_on_leads.pl
   to update the timezone in the vicidial_list table
  * AST_SERVER_conf.pl on the server has been updated, so you may need to modify
   your server configs to enable new features(like timezone dialing).
  * AST_VICI_conf.pl for clients has been updated, so you may need to modify
   your client configs to enable new features(like timezone dialing).
  * run this script if you plan on doing timezone restrictions on VICIDIAL:
   ADMIN_area_code_populate.pl

1.0.5 - Eleventh public release - 2004-10-28
- many minor bug fixes in most of the server and client scripts
- fixed multiple login bug on VICIDIAL
- fixed the manual-dial/auto-dial switching bug on VICIDIAL
- fixed a few time zone calling bugs on VICIDIAL server apps
- all apps now allow for IAX trunks to be used in addition to the usual Zap
- added more options to install script
- changed CLI lead loader to use Net::MySQL instead of DBI
- modified several scripts that handle CallerID because of the early October
  change in the CVS of outputting CID info as two separate fields(CallerID and 
  CallerIDname) instead of everything being sent in CallerID.
UPGRADE NOTES:
 * if upgrading from 1.0.4 you need to update the web pages and all 
   server and client apps. 
 * if upgrading from 1.0.4 run the upgrade_1.0.5.sql script in MySQL
 * if using a CVS version 2004-10-07 or newer or release 1.0.3 or newer 
   change the line in extensions.conf
	exten => 8309,2,Monitor(wav,${CALLERIDNUM})
		change to
	exten => 8309,2,Monitor(wav,${CALLERIDNAME})

1.0.6 - Twelfth public release - 2004-12-23
- many minor bug fixes in most of the server and client scripts
- optimized the ACQS and restructured its keepalive scripts
- added hijack line feature to astGUIclient
- added inbound call handling for VICIDIAL
- added blended in-outbound calling within VICIDIAL
- added new closer stats report
- updated the SCRATCH_INSTALL instructions and corrected errors
- see the TODO.txt file for other items DONE in this release
UPGRADE NOTES:
 * if upgrading from 1.0.5 you need to update the web pages and all 
   server and client apps. 
 * if upgrading from 1.0.5 run the upgrade_1.0.6.sql script in MySQL
 * if upgrading from 1.0.5 add a campaign "CLOSER" to enable closers and
   inbound call taking through the VICIDIAL application
     (see the SCRATCH_INSTALL document SUBPHASE 6.2 for more information)

1.1.0 - Thirteenth public release - 2005-03-09
- several small bug fixes in most of the server and client scripts
- added IAX and Zap client/agent compatibility
- implemented some major database optimizations
- ability to specify MySQL port across all apps
- moved almost all client config variables from client files to the Database
- created a meetme room validator that will allow faster reassignment of conferences
- created a new remote_agent login system for VICIDIAL in/outbound calls
- restructuring of AST_update script to cope better with manager output hiccups
- added dial timeout field to VICIDIAL to specify the timeout per campaign
- added help popups to every page of the admin web pages to explain fields
- see the TODO.txt file for other items DONE in this release
UPGRADE NOTES:
 * if upgrading from 1.0.6 you need to update the web pages and all 
   server and client apps. 
 * if upgrading from 1.0.6 run the upgrade_1.1.0.sql script in MySQL
 * if upgrading from 1.0.6 you will need to configure your client parameters on
   the admin web page and remove them from the client computers (except for the 
   fields that are in the sample AST_VICI_conf.pl file)
 * older client apps are not compatible with this version, you must upgrade

1.1.1 - Fourteenth public release - 2005-06-03
- several small bug fixes in most of the server and client scripts
- added astGUIclient web-only client
- added campaign-custom disposition statuses to VICIDIAL
- added campaign-custom CallerID to VICIDIAL
- added fronter-display-disable option to VICIDIAL Inbound Groups
- added ability to separate vicidial users by groups
- added performance stats gathering ability to AST_update script
- fixed the send-to-vmail portion of inbound AGI scripts
- added simple web-based lead file loader for VICIDIAL
UPGRADE NOTES:
 * if upgrading from 1.1.0 you need to update the web pages and all 
   server apps. 
 * if upgrading from 1.1.0 run the upgrade_1.1.1.sql script in MySQL

1.1.2 - Fifteenth public release - 2005-06-06
- fixed fatal bug in AST_update.pl script
- added instructions for ploticus install to SCRATCH_INSTALL
UPGRADE NOTES:
 * if upgrading from 1.1.0 you need to:
  - download the new version from the project website
  - unzip the zip file into the astguiclientastguiclient directory
  - and either:
    + copy the web pages and all server apps to their proper locations manually
    + or run the install_server_files.pl script to put all items in their
      default places
 * if upgrading from 1.1.0 run the upgrade_1.1.1.sql script in MySQL
   (\. astguiclientastguiclient/upgrade_1.1.1.sql)

1.1.3 - Sixteenth public release - 2005-06-10
- fixed config bug in AST_VDauto_dial.pl script
- added null query result checks to scripts in the agc web-only client
- added documentation for the inbound and transfer AGI script variables
- added documentation and tested install with MySQL 4.1 tree (4.1.12)
UPGRADE NOTES:
 * if upgrading from 1.1.0 - 1.1.2 you need to:
  - download the new version from the project website
  - unzip the zip file into the astguiclientastguiclient directory
  - and either:
    + copy the web pages and all server apps to their proper locations manually
    + or run the install_server_files.pl script to put all items in their
      default places
 * if upgrading from 1.1.0 run the upgrade_1.1.1.sql script in MySQL
   (\. astguiclientastguiclient/upgrade_1.1.1.sql)

1.1.4 - Seventeenth public release - 2005-06-24
- fixed new Local channel bugs caused by changes in Asterisk 1.0.8 and CVS
- added beta support for SIP trunks
- added ability to send VICIDIAL outbound calls to custom AGI script per 
   campaign and included new survey sample AGI script
- added better Asterisk shutdown detection to AST_manager_listen script
UPGRADE NOTES:
 * if upgrading from 1.1.3 you need to:
  - download the new version from the project website
  - unzip the zip file into the astguiclientastguiclient directory
  - and either:
    + copy the web pages and all server apps to their proper locations manually
    + or run the install_server_files.pl script to put all items in their
      default places
  - if using VICIDIAL download the new client script to all client stations
  - make sure you populate the new $AST_ver variable in AST_SERVER_conf.pl
 * if upgrading from 1.1.3 run the upgrade_1.1.4.sql script in MySQL
   (\. astguiclientastguiclient/upgrade_1.1.4.sql)

1.1.5 - Eighteenth public release - 2005-08-12
- fixed new Local channel bugs caused by changes in Asterisk 1.0.8 and 1.0.9
- moved many server variables to the database, editable through admin.php
- added ability to limit number of outbound trunks used by VICIDIAL auto dialer
- many small changes and bug fixes to the astguiclient web client
- added alpha vicidial web-client (click-to-dial only)
UPGRADE NOTES:
 * if upgrading from 1.1.4 you need to:
  - download the new version from the project website
  - unzip the zip file into the astguiclientastguiclient directory
  - and either:
    + copy the web pages and all server apps to their proper locations manually
    + or run the install_server_files.pl script to put all items in their
      default places
  - VICIDIAL perl/TK client is unchanged from 1.1.4
  - make sure you check the astguiclient admin.php server parameters
 * if upgrading from 1.1.4 run the upgrade_1.1.5.sql script in MySQL
   (\. astguiclientastguiclient/upgrade_1.1.5.sql)

1.1.6 - Nineteenth public release - 2005-08-29
- streamlined many of the server apps and added command-line debug capability
- finished the vicidial web-client and tested in prduction
- modified server-side scripts to function with Asterisk CVS_HEAD(soon to be 1.2)
- modified server performance logging and graphing, added user/system proc %
UPGRADE NOTES:
 * if upgrading from 1.1.5 you need to:
  - download the new version from the project website
  - unzip the zip file into the astguiclientastguiclient directory
  - and either:
    + copy the web pages and all server apps to their proper locations manually
    + or run the install_server_files.pl script to put all items in their
      default places
   (make sure AST_SERVER_conf.pl and dbconnect.php files are config'd properly)
  - perl/TK clients are unchanged from 1.1.5
  - make sure you check the astguiclient admin.php server parameters
  - make the agi-VDADtransfer.agi changes in extensions.conf (mentioned in SCRATCH_INSTALL doc)
 * if upgrading from 1.1.5 run the upgrade_1.1.6.sql script in MySQL
   (\. astguiclientastguiclient/upgrade_1.1.6.sql)

1.1.7 - Twentieth public release - 2005-10-05
- Added HotKeys key binding for VICIDIAL web-client fast dispositioning
- Internationalization(multi-language) of web-clients and admin pages
- Added ability in manual VICIDIAL to call alternate lead phone numbers
- Added option of pull-down of active campaigns to VICIDIAL web login
- Fixed some issues with scripts interacting with Asterisk 1.2
- other small changes listed in the TODO.txt file
UPGRADE NOTES:
 * if upgrading from 1.1.6 you need to:
  - download the new version from the project website
  - unzip the zip file into the astguiclientastguiclient directory
  - and either:
    + copy the web pages and all server apps to their proper locations manually
    + or run the install_server_files.pl script to put all items in their
      default places
   (make sure AST_SERVER_conf.pl and dbconnect.php files are config'd properly)
  - perl/TK clients have been slightly updated(will be unsupported soon)
  - multi-language versions of web-clients and admin pages are available in the 
    LANG_agc.zip and LANG_admin.zip files and can be unzipped into your webroot
    directory. Make sure you check the dbconnect.php file in each directory.
 * if upgrading from 1.1.6 run the upgrade_1.1.7.sql script in MySQL
   (\. astguiclientastguiclient/upgrade_1.1.7.sql)

1.1.8 - Twenty-First public release - 2005-11-10
- Added per-campaign call recording options to VICIDIAL
- Added detailed logging for VICIDIAL agent time
- Fixed some issues with scripts interacting with Asterisk 1.2
- other small changes listed in the TODO.txt file
UPGRADE NOTES:
 * if upgrading from 1.1.7 you need to:
  - download the new version from the project website
  - unzip the zip file into the astguiclientastguiclient directory
  - and either:
    + copy the web pages and all server apps to their proper locations manually
    + or run the install_server_files.pl script to put all items in their
      default places
   (make sure AST_SERVER_conf.pl and dbconnect.php files are config'd properly)
  - multi-language versions of web-clients and admin pages are available in the 
    LANG_agc.zip and LANG_admin.zip files and can be unzipped into your webroot
    directory. Make sure you check the dbconnect.php file in each directory.
 * if upgrading from 1.1.7 run the upgrade_1.1.8.sql script in MySQL
   (\. astguiclientastguiclient/upgrade_1.1.8.sql)

1.1.9 - Twenty-Second public release - 2006-01-19
- Added Load Balancing of Inbound and Outbound calls across multiple Asterisk
   servers in the same setup
- Removed all PHP globals requirements, can now use register_globals=Off in PHP
- Add fully translated Greek admin and agc scripts(agc images to come)
- Added security elements to admin pages and added record deletion functions
- Added a script tab to VICIDIAL to have a variable-populated agent script
- Added new Super lead loader with field chooser and CVS/XLS-format capability
- Added favorites panel to astguiclient.php that shows off/on hook
- Many other small changes listed in the TODO.txt file
UPGRADE NOTES:
 * if upgrading from 1.1.8 you need to:
  - download the new version from the project website
  - unzip the zip file into the astguiclientastguiclient directory
  - and either:
    + copy the web pages and all server apps to their proper locations manually
    + or run the install_server_files.pl script to put all items in their
      default places
   (make sure AST_SERVER_conf.pl and dbconnect.php files are config'd properly)
  - multi-language versions of web-clients and admin pages are available in the 
    LANG_agc.zip and LANG_admin.zip files and can be unzipped into your webroot
    directory. Make sure you check the dbconnect.php file in each directory.
 * if upgrading from 1.1.8 run the upgrade_1.1.9.sql script in MySQL
   (\. astguiclientastguiclient/upgrade_1.1.9.sql)

1.1.10 - Twenty-Third public release - 2006-03-17
- Added capability to work with app_amd(answering machine detection)
- Added transfer-conference number and DTMF presets per campaign
- Added sphinx ring-time analysis disposition package(beta)
- Added scheduled callbacks to VICIDIAL
- Added more user-permission options/restrictions
- Several small bug fixes for better Asterisk 1.2.4 compatibility
- Many other small changes listed in the TODO.txt file
UPGRADE NOTES:
 * if upgrading from 1.1.9 you need to:
  - download the new version from the project website
  - unzip the zip file into the astguiclientastguiclient directory
  - and either:
    + copy the web pages and all server apps to their proper locations manually
    + or run the install_server_files.pl script to put all items in their
      default places
   (make sure AST_SERVER_conf.pl and dbconnect.php files are config'd properly)
  - add "exten => h,2,DeadAGI(VD_hangup.agi,${EXTEN})" to extensions.conf
  - multi-language versions of web-clients and admin pages are available in the 
    LANG_agc.zip and LANG_admin.zip files and can be unzipped into your webroot
    directory. Make sure you check the dbconnect.php file in each directory.
 * if upgrading from 1.1.9 run the upgrade_1.1.10.sql script in MySQL
   (\. astguiclientastguiclient/upgrade_1.1.10.sql)

1.1.11 - Twenty-Fourth public release - 2006-04-28
- Added Agent-only Scheduled Callbacks
- Added Lead Filters for campaigns to use custom SQL to narrow calling scope
- Added Agent manual-lead-insertion capability
- Fixed PHP variable checking in all PHP scripts to be more compliant
- Added more user-permission options/restrictions for both agents and admins
- Several small bug fixes for better Asterisk 1.2.6 and SVN compatibility
- Many other small changes listed in the TODO.txt file
UPGRADE NOTES:
 * if upgrading from 1.1.10 you need to:
  - download the new version from the project website
  - unzip the zip file into the astguiclientastguiclient directory
  - and either:
    + copy the web pages and all server apps to their proper locations manually
    + or run the install_server_files.pl script to put all items in their
      default places
   (make sure AST_SERVER_conf.pl and dbconnect.php files are config'd properly)
  - multi-language versions of web-clients and admin pages are available in the 
    LANG_agc.zip and LANG_admin.zip files and can be unzipped into your webroot
    directory. Make sure you check the dbconnect.php file in each directory.
 * if upgrading from 1.1.10 run the upgrade_1.1.11.sql script in MySQL
   (\. astguiclient/upgrade_1.1.11.sql)

1.1.12 - Twenty-Fifth public release - 2006-06-22
- Security enhancements of PHP scripts to reduce SQL-injection and other threats
- Completely redesigned local call time system in VICIDIAL for more flexibility
- Added Drop-call and safe-harbor options to campaigns and inbound groups
- Added option for allowing wrapup time between calls in vicidial.php
- Multi-language translation rewritten for more flexibility and easy of use
- Added more user-permission options/restrictions for both agents and admins
- Added option of using app_conference instead of meetme engine
- Added internal DNC list for Do-Not-Call entries across the system
- Added automatic lead recycling to call back Busy calls at definable intervals
- Added listID override and timezone lookup to all lead importing scripts
- Added easy-prompt-recording AGI and playback: agi-record_prompts.agi
- Many other changes and bug fixes listed in the TODO.txt file
UPGRADE NOTES:
 * if upgrading from 1.1.11 you need to:
  - download the new version from the project website
  - unzip the zip file into the astguiclientastguiclient directory
  - and either:
    + copy the web pages and all server apps to their proper locations manually
    + or run the install_server_files.pl script to put all items in their
      default places
   (make sure AST_SERVER_conf.pl and dbconnect.php files are config'd properly)
  - multi-language versions of web-clients and admin pages are available in the 
    LANG_agc.zip and LANG_admin.zip files and can be unzipped into your webroot
    directory. Make sure you check the dbconnect.php file in each directory.
 * if upgrading from 1.1.11 run the upgrade_1.1.12.sql script in MySQL
   (\. astguiclientastguiclient/upgrade_1.1.12.sql)
 * if upgrading from older version you will need to run this query for each 
   campaign in VICIDIAL in MySQL:
   (INSERT INTO vicidial_campaign_stats (campaign_id) values('campaignname');)

2.0.1 - Twenty-Sixth public release - 2006-09-15
- Changed all Perl script to use DBI instead of Net::MySQL
- Completely new interactive install script with new astguiclient.conf file
- Ability to turn off or on logging of server scripts
- Ability to redirect AGI output to file, STDERR, both or nowhere.
- Code and process optimization of many of the server-side scripts
- Added a Predictive outbound dialing algorithm with many user-defined settings
- Wrote new BASE_INSTALL.txt doc for simple install and added sample conf files
- Many other changes and bug fixes listed in the TODO Wiki webpage:
     http://www.eflo.net/VICIDIALwiki/index.php/TODO:2.0.1
UPGRADE NOTES:
 * if upgrading from 1.1.12 you need to follow instructions in the UPGRADE doc

2.0.2 - Twenty-Seventh public release - 2006-12-22
- Added FastAGI replacement for call_log and VDhangup scripts
- Added new USA-Canada Daylight Savings time scheme and DST for Brazil
- astguiclient/admin.php removed and merged into vicidial/admin.php
- Ability for agent to alter volume levels of participants
- Keepalives consolidated into single script
- New all-active-campaigns realtime summary screen
- New script added to make it easier to change IP address of servers in VICIDIAL
- Added ability to reserver trunk lines for specific VICIDIAL campaigns
- Added ability for balance dialing and dialing-only servers in VICIDIAL
- Many other changes and bug fixes listed in the TODO Wiki webpage:
     http://www.eflo.net/VICIDIALwiki/index.php/TODO:2.0.2
UPGRADE NOTES:
 * if upgrading from 2.0.1 you need to follow instructions in the UPGRADE doc

2.0.3 - Twenty-Eighth public release - 2007-04-27
- Added optimized manager sending scripts
- Made several changes to admin.php and made it more customizable
- Made changes to HotKeys to allow for manual auto dial and auto alt number dial
- Added ability to dial alternate lead phone numbers in auto-dial modes
- Added ability to send more calls to agents at once, helping large installations
- Added more levels of access control to the administrative interfaces
- Added Agent Pause codes to alow agents to categorize their PAUSE time
- Added VICIDIAL outbound compatibility with QueueMetrics
- Many other changes and bug fixes listed in the TODO Wiki webpage:
     http://www.eflo.net/VICIDIALwiki/index.php/TODO:2.0.3
UPGRADE NOTES:
 * if upgrading from 2.0.2 you need to follow instructions in the UPGRADE doc

2.0.4 - Twenty-Ninth public release - 2007-12-03
- Easy control over System Statuses, including the addition of Status Categories
- Several new additional In-Group features including music-on-hold, alert messages and call times
- Skills based ranking and call routing for inbound and outbound
- Agent call routing based on the number of calls taken today per in-group or campaign
- Added the ability to copy the settings of an existing User/Campaign/In-Group into a new one
- Changing the Transfer feature in the agent screen to allow for more transfer In-Group choices
- Dynamic display of available campaigns for an agent in the agent login screen
- Many other changes and bug fixes listed in the TODO Wiki webpage:
     http://www.eflo.net/VICIDIALwiki/index.php/TODO:2.0.4
UPGRADE NOTES:
 * if upgrading from 2.0.3 you need to follow instructions in the UPGRADE doc


2.0.5 - Thirtieth public release - 2009-04-03
UPGRADE NOTES:
 * if upgrading from 2.0.4 you need to follow instructions in the UPGRADE doc
CHANGES:
1. Note the license change from GPLv2 to AGPLv2. This change was made to close the ASP loophole for code distribution that was present in the GPLv2 license. Please read the docs/LICENSE.txt file for more information 
2. Several of the MySQL tables have been changed to a HEAP type to reside in memory to improve performance. 
3. Added the ability for inbound and blended agents to choose their selected in-groups without logging out. The new GROUPS link at the top of the agent screen(vicidial.php) can be used when an agent is Paused to change their selected groups and whether they are set to do Blended calling or not. There is also a new log with the details of what groups an agent has selected each time in the user stats page. 
4. Queue Priority has been added allowing for the prioritization of one in-group over another in-group or outbound campaign(when in blended mode) 
5. Drop call action has been added to both inbound and outbound calling. You can now send a call that would drop into an inbound group. The call will still be recorded as a DROP in it's original in-group or campaign, but the lead information will move on to the drop in-group. 
6. Audio recording mixing/compression/ftping scripts have been completely rewritten and separated into different scripts for better fault tolerance. Also, you have the option of compression in to the following formats: WAV, GSM, MP3 and OGG - AST_CRON_audio_1_move_mix.pl - AST_CRON_audio_1_move_VDonly.pl - AST_CRON_audio_2_compress.pl - AST_CRON_audio_3_ftp.pl 
7. Created a new script to backup a VICIDIAL setup, including the Asterisk conf files, prompt recordings, bin, agi and web files: ADMIN_backup.pl 
8. Added ability to override recording options of a campaign for individual inbound-groups 
9. The 8 outbound agi-VDADtransfer scripts are in process of being consolidated into a single AGI script: agi-VDAD_ALL_outbound.agi All older outbound scripts are depricated and do not function any more 
10. Added the ability to load balance the logins to vicidial.php for phones that are registered to multiple servers on a multi-server system. Simply put multiple phone_login values in the phone login field at login separated by a comma and the vicidial.php script will pick the server that has the fewest agents logged into it at the moment of login. 
11. Added ability for vicidial.php to populate the phones.computer_ip field upon agent login. Options set in the vicidial.php file to allow for overwrite every login or only if field is empty. 
12. Several code changes for better UTF8 compatibility with the database as well as special character leads in the agent screen, lead loading 
13. Added after hours redirect of calls to an in-group to another in-group 
14. Added collection of hangup reasons for inbound and outbound calls in the vicidial_log and vicidial_closer_log tables 
15. Added a new timeclock feature w/ audit and log, including the addition of shifts to VICIDIAL user groups and the ability to auto-generate user IDs as you are adding users to the system. timeclock.php user login page created and linked to from admin/agent/welcome screens 
16. Added a new inbound/closer(queues) report for showing service levels 
17. Added Calls in Queue counter to agent vicidial.php screen 
18. Added ability to use calltime scheme for calls not yet in an in-group(queue) as well as display in real-time stats. See Inbound_VDAC_IVR.txt doc for more information 
19. Auto-dial survey broadcast campaigns can now be fully configured within the admin.php interface and they have new options shown in the Survey sub- section 
20. Changed add-to-DNC list to allow for multiple phone numbers per submission 
21. Date/time and phone formats that appear in the VICIDIAL agent screen are now customizable as defined in the Admin -> System Settings screen. 
22. Added manual dial and inbound queue_log logging capability for better QueueMetrics compatibility 
23. Added some basic agent interface API functions and an agc/api.php script 
24. A field has been added to vicidial_list(last_local_call_time) to sort by the last time a lead was called as well as for lead recycling changes. 
25. IMPORTANT FOR RECORDINGS!!! The recording extensions have been changed within VICIDIAL so you need to add the following lines to your extensions.conf ; quiet monitor-only entry and leaving conferences for VICIDIAL (recording) exten => _58600XXX,1,Meetme,${EXTEN:1}|Fmq 
26. Added option (for Internet Explorer users only) to copy a field to the clipboard of the agent computer upon a call being sent to the agent. 
27. Added new alternate number dialing method which allows for over 65,000 alt phone numbers per lead. Currently these extra numbers are only available for auto dialing, not manual dial. These extra alternate number leads must be imported either with the CLI lead loader(VICIDIAL_IN_new_leads_file.pl) or the non-agent API script. 
28. Added campaign-specific DNC filtering lists to the system 
29. Added optional dialplan entries to sample extensions.conf to play a not-in- service message for invalid phone numbers that cannot be dialing in North America 
30. Rewrote Leave-3way function for conference calling to work more reliably on high load systems. Need to add more vicidial_conferences entries and meetme.conf entries for this, see the meetme.conf.sample file and the first_server_install.sql file for more information 
31. Added several new features to inbound call handling including: - announce place in line - announce estimated hold time - welcome message options - options for call routing when estimated hold time is too high - press 1 to leave a voicemail for customers if wait time too high 
32. Added DID call routing to allow for basic routing of calls to phones, extensions, voicemail and VICIDIAL inbound groups without editing the extensions.conf dialplan. *requires initial adding one line to dialplan of inbound context: exten => _X.,1,AGI(agi-DID_route.agi) *as well as a few more lines in your VICIDIAL default context: ; DID forwarded calls exten => _99909*.,1,Answer exten => _99909*.,2,AGI(agi-VDAD_ALL_inbound.agi) exten => _99909*.,3,Hangup 
33. Added ability to set callerID number on outgoing conference calls to CAMPAIGN, CUSTOMER or AGENT_PHONE callerID settings. 
34. New Slovak translation for the agent screen, as well as new German agent screen translation and new German buttons for the agent screen 
35. New Welcome demo and languages screens with links to all translations of agent and admin screens 
36. Added INBOUND_MAN dial method to allow for inbound call handling by agents that are also placing manual dial list calls through their campaign lists 
37. Added option to see the inbound group call stats in the Real-time screen 
38. Added ability for agents to dial in to an AGI script(agi-AGENT_dial_in.agi) to log their phone into the session instead of having VICIDIAL call them. The user ID and password for the agent must be digits only for this to work! This requires the following to be added to the dialplan on the dial-in DID or extension that you want to use: exten => 2345,1,Answer ; Answer the line exten => 2345,2,AGI(agi-AGENT_dial_in.agi) exten => 2345,3,Hangup 
39. Added pause codes display to the Real-time report as well as pause code statistics to the agent performance detail report. Also added an option to force agents to enter a pause code(this is a campaign option) 
40. Added full MySQL error logging to vicidial agent web interface. Logs are written to the webserver. 
41. Added new inbound-only reporting to Real-time report as well as many new features to existing inbound report. Created IVR stats report to show stats on Asterisk IVRs 
42. Updated Vtiger integration to version 5.0.4 and allow searching by leads/accounts/vendors. Also added user synchronization with vicidial_users. Also added click-to-dial from Vtiger sending call to vicidial agent screen. Also added batch synchronization of Accounts between Vtiger and VICIDIAL. 
43. Added notification of time synchronization problems in the vicidial.php page 
44. Added AGENTDIRECT queueing to be able to send calls to only one agent without having to create a new in-group for each agent. You can configure DIDs to use AGENTDIRECT, and you can have VICIDIAL agents send calls to specific agents using the AGENTDIRECT transfer group with LOCAL CLOSER button and putting the agent to send the call to in the Number To Dial field. 
45. Added agent alert option to agent screen. sends browser alert prompt when a call is send to the agent screen. 
46. Added option for using North American NANPA prefix timezone encoding for more accurate time zone encoding. The data is NOT included but can be purchased from the VICIDIAL Group. 
47. Added System Settings option to disable auto-dialing(list dialing) from the system to simplify administration for inbound-only call centers 
48. Added patch and configuration options to allow VICIDIAL to work with Sangoma ParaXip CPD(Call Progress Detection) for Answering Maching and Fax detection 
49. Added list_download.php that allows you to download the entire contents of vicidial lists from the admin.php interface. You must set the download_lists permission in vicidial_users to 1 for this to work. 
50. Small redesign of the agent interface to use grey backgrounds and new tabs 
51. Added Asterisk version prompt to the install.pl script to allow for the copying of the proper conf files for the installed Asterisk version 
52. Added auto-generation of SIP and IAX phones and carriers into the Asterisk conf files so that you can fully configure them using only the admin.php web interface. See the ASTERISK_CONF-GEN.txt file for more information 
53. Added group alias option for selecting callerID on manual and 3rd-party calls 
54. Added options for custom web variables for each agent per in-group and camaign. This is definable in the user modification page 
55. Added Agent Shift enforcement options to restrict logins and vicidial agent activity 
56. Added Server vital statistics(load/CPU/disk) to the reports screen and the server modification screen 
57. Removed the admin_changes flat text log file and now put all administrative actions into the vicidial_admin_log table in MySQL. These logs are displayed to user_level 9 users in admin.php through a link on the REPORTS screen, and at the bottom of the other screens (USERS, CAMPAIGNS, LISTS, etc...)you can click on the "Click here to see Admin chages to this record" link to find out the most recent changes to that record. 
58. Added uniform admin headers to all administrative pages and reports 
59. Added Calls Export Utility allowing exporting of call and lead data by defining the following parameters: date range, campaigns, in-groups, statuses, lists and user groups. A flat tab-delimited text file is exported. 
60. Added new monitoring functionality to allow monitoring from one agent to another without hanging up
61. Added Russian and Dutch agent interface translations
