<?php
# conf_exten_check.php    version 2.2.0
# 
# Copyright (C) 2009  Matt Florell <vicidial@gmail.com>    LICENSE: AGPLv2
#
# This script is designed purely to send whether the meetme conference has live channels connected and which they are
# This script depends on the server_ip being sent and also needs to have a valid user/pass from the vicidial_users table
# 
# required variables:
#  - $server_ip
#  - $session_name
#  - $user
#  - $pass
# optional variables:
#  - $format - ('text','debug')
#  - $ACTION - ('refresh','register')
#  - $client - ('agc','vdc')
#  - $conf_exten - ('8600011',...)
#  - $exten - ('123test',...)
#  - $auto_dial_level - ('0','1','1.2',...)
#  - $campagentstdisp - ('YES',...)
# 

# changes
# 50509-1054 - First build of script
# 50511-1112 - Added ability to register a conference room
# 50610-1159 - Added NULL check on MySQL results to reduced errors
# 50706-1429 - script changed to not use HTTP login vars, user/pass instead
# 50706-1525 - Added date-time display for vicidial client display
# 50816-1500 - Added random update to vicidial_live_agents table for vdc users
# 51121-1353 - Altered echo statements for several small PHP speed optimizations
# 60410-1424 - Added ability to grab calls-being-placed and agent status
# 60421-1405 - check GET/POST vars lines with isset to not trigger PHP NOTICES
# 60619-1201 - Added variable filters to close security holes for login form
# 61128-2255 - Added update for manual dial vicidial_live_agents
# 70319-1542 - Added agent disabled display function
# 71122-0205 - Added vicidial_live_agent status output
# 80424-0442 - Added non_latin lookup from system_settings
# 80519-1425 - Added calls-in-queue tally
# 80703-1106 - Added API functionality for Hangup and Dispo
# 81104-0229 - Added mysql error logging capability
# 81104-1409 - Added multi-retry for some vicidial_live_agents table MySQL queries
# 90102-1402 - Added check for system and database time synchronization
# 90120-1720 - Added compatibility for API pause/resume and dial a number
# 90307-1855 - Added shift enforcement to send logout flag if outside of shift hours
# 90408-0020 - Added API vtiger specific callback activity record ability
# 90508-0727 - Changed to PHP long tags
# 90706-1430 - Fixed AGENTDIRECT calls in queue display count
# 90908-1037 - Added DEAD call logging
# 91130-2022 - Added code for manager override of in-group selection
# 91228-1341 - Added API fields update functions
# 100109-1337 - Fixed Manual dial live call detection
#

$version = '2.2.0-25';
$build = '100109-1337';
$mel=1;					# Mysql Error Log enabled = 1
$mysql_log_count=32;
$one_mysql_log=0;

require("dbconnect.php");

### If you have globals turned off uncomment these lines
if (isset($_GET["user"]))					{$user=$_GET["user"];}
	elseif (isset($_POST["user"]))			{$user=$_POST["user"];}
if (isset($_GET["pass"]))					{$pass=$_GET["pass"];}
	elseif (isset($_POST["pass"]))			{$pass=$_POST["pass"];}
if (isset($_GET["server_ip"]))				{$server_ip=$_GET["server_ip"];}
	elseif (isset($_POST["server_ip"]))		{$server_ip=$_POST["server_ip"];}
if (isset($_GET["session_name"]))			{$session_name=$_GET["session_name"];}
	elseif (isset($_POST["session_name"]))	{$session_name=$_POST["session_name"];}
if (isset($_GET["format"]))					{$format=$_GET["format"];}
	elseif (isset($_POST["format"]))		{$format=$_POST["format"];}
if (isset($_GET["ACTION"]))					{$ACTION=$_GET["ACTION"];}
	elseif (isset($_POST["ACTION"]))		{$ACTION=$_POST["ACTION"];}
if (isset($_GET["client"]))					{$client=$_GET["client"];}
	elseif (isset($_POST["client"]))		{$client=$_POST["client"];}
if (isset($_GET["conf_exten"]))				{$conf_exten=$_GET["conf_exten"];}
	elseif (isset($_POST["conf_exten"]))	{$conf_exten=$_POST["conf_exten"];}
if (isset($_GET["exten"]))					{$exten=$_GET["exten"];}
	elseif (isset($_POST["exten"]))			{$exten=$_POST["exten"];}
if (isset($_GET["auto_dial_level"]))			{$auto_dial_level=$_GET["auto_dial_level"];}
	elseif (isset($_POST["auto_dial_level"]))	{$auto_dial_level=$_POST["auto_dial_level"];}
if (isset($_GET["campagentstdisp"]))			{$campagentstdisp=$_GET["campagentstdisp"];}
	elseif (isset($_POST["campagentstdisp"]))	{$campagentstdisp=$_POST["campagentstdisp"];}

header ("Content-type: text/html; charset=utf-8");
header ("Cache-Control: no-cache, must-revalidate");  // HTTP/1.1
header ("Pragma: no-cache");                          // HTTP/1.0


#############################################
##### START SYSTEM_SETTINGS LOOKUP #####
$stmt = "SELECT use_non_latin FROM system_settings;";
$rslt=mysql_query($stmt, $link);
	if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'03001',$user,$server_ip,$session_name,$one_mysql_log);}
if ($DB) {echo "$stmt\n";}
$qm_conf_ct = mysql_num_rows($rslt);
if ($qm_conf_ct > 0)
	{
	$row=mysql_fetch_row($rslt);
	$non_latin =					$row[0];
	}
##### END SETTINGS LOOKUP #####
###########################################

if ($non_latin < 1)
{
$user=ereg_replace("[^0-9a-zA-Z]","",$user);
$pass=ereg_replace("[^0-9a-zA-Z]","",$pass);
}

# default optional vars if not set
if (!isset($format))   {$format="text";}
if (!isset($ACTION))   {$ACTION="refresh";}
if (!isset($client))   {$client="agc";}

$Alogin='N';
$RingCalls='N';
$DiaLCalls='N';

$StarTtime = date("U");
$NOW_DATE = date("Y-m-d");
$NOW_TIME = date("Y-m-d H:i:s");
$FILE_TIME = date("Ymd_His");
if (!isset($query_date)) {$query_date = $NOW_DATE;}
$random = (rand(1000000, 9999999) + 10000000);


$stmt="SELECT count(*) from vicidial_users where user='$user' and pass='$pass' and user_level > 0;";
if ($DB) {echo "|$stmt|\n";}
if ($non_latin > 0) {$rslt=mysql_query("SET NAMES 'UTF8'");}
$rslt=mysql_query($stmt, $link);
			if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'03002',$user,$server_ip,$session_name,$one_mysql_log);}
$row=mysql_fetch_row($rslt);
$auth=$row[0];

  if( (strlen($user)<2) or (strlen($pass)<2) or ($auth==0))
	{
    echo "Недопустимо Username/Пароль: |$user|$pass|\n";
    exit;
	}
  else
	{

	if( (strlen($server_ip)<6) or (!isset($server_ip)) or ( (strlen($session_name)<12) or (!isset($session_name)) ) )
		{
		echo "Недопустимо server_ip: |$server_ip|  or  Недопустимо session_name: |$session_name|\n";
		exit;
		}
	else
		{
		$stmt="SELECT count(*) from web_client_sessions where session_name='$session_name' and server_ip='$server_ip';";
		if ($DB) {echo "|$stmt|\n";}
		$rslt=mysql_query($stmt, $link);
			if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'03002',$user,$server_ip,$session_name,$one_mysql_log);}
		$row=mysql_fetch_row($rslt);
		$SNauth=$row[0];
		  if($SNauth==0)
			{
			echo "Недопустимо session_name: |$session_name|$server_ip|\n";
			exit;
			}
		  else
			{
			# do nothing for now
			}
		}
	}

if ($format=='debug')
{
echo "<html>\n";
echo "<head>\n";
echo "<!-- ВЕРСИЯ: $version     СБОРКА: $build    MEETME: $conf_exten   server_ip: $server_ip-->\n";
echo "<title>Conf Extension Check";
echo "</title>\n";
echo "</head>\n";
echo "<BODY BGCOLOR=white marginheight=0 marginwidth=0 leftmargin=0 topmargin=0>\n";
}

	if ($ACTION == 'refresh')
	{
		$MT[0]='';
		$row='';   $rowx='';
		$channel_live=1;
		if (strlen($conf_exten)<1)
		{
		$channel_live=0;
		echo "Conf Exten $conf_exten это неправильно\n";
		exit;
		}
		else
		{

		if ($client == 'vdc')
			{
			$Acount=0;
			$AexternalDEAD=0;
			$Aagent_log_id='';
			$Acallerid='';
			$DEADcustomer=0;

			### see if the agent has a record in the vicidial_live_agents table
			$stmt="SELECT count(*) from vicidial_live_agents where user='$user' and server_ip='$server_ip';";
			if ($DB) {echo "|$stmt|\n";}
			$rslt=mysql_query($stmt, $link);
			if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'03003',$user,$server_ip,$session_name,$one_mysql_log);}
			$row=mysql_fetch_row($rslt);
			$Acount=$row[0];

			if ($Acount > 0)
				{
				$stmt="SELECT status,callerid,agent_log_id,campaign_id from vicidial_live_agents where user='$user' and server_ip='$server_ip';";
				if ($DB) {echo "|$stmt|\n";}
				$rslt=mysql_query($stmt, $link);
			if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'03004',$user,$server_ip,$session_name,$one_mysql_log);}
				$row=mysql_fetch_row($rslt);
				$Astatus =			$row[0];
				$Acallerid =		$row[1];
				$Aagent_log_id =	$row[2];
				$Acampaign_id =		$row[3];
				}
		#	### find out if external table shows agent should be disabled
		#	$stmt="SELECT count(*) from another_table where user='$user' and status='DEAD';";
		#	if ($DB) {echo "|$stmt|\n";}
		#	$rslt=mysql_query($stmt, $link);
		#	$row=mysql_fetch_row($rslt);
		#	$AexternalDEAD=$row[0];

			if ($auto_dial_level > 0)
				{
				### update the vicidial_live_agents every second with a new random number so it is shown to be alive
				$stmt="UPDATE vicidial_live_agents set random_id='$random' where user='$user' and server_ip='$server_ip';";
					if ($format=='debug') {echo "\n<!-- $stmt -->";}
				$rslt=mysql_query($stmt, $link);
			if ($mel > 0) {$errno = mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'03005',$user,$server_ip,$session_name,$one_mysql_log);}
				$retry_count=0;
				while ( ($errno > 0) and ($retry_count < 5) )
					{
					$rslt=mysql_query($stmt, $link);
					$one_mysql_log=1;
					$errno = mysql_error_logging($NOW_TIME,$link,$mel,$stmt,"9305$retry_count",$user,$server_ip,$session_name,$one_mysql_log);
					$one_mysql_log=0;
					$retry_count++;
					}

				##### BEGIN DEAD logging section #####
				### find whether the call the agent is на is hung up
				$stmt="SELECT count(*) from vicidial_auto_calls where callerid='$Acallerid';";
				if ($DB) {echo "|$stmt|\n";}
				$rslt=mysql_query($stmt, $link);
			if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'03018',$user,$server_ip,$session_name,$one_mysql_log);}
				$row=mysql_fetch_row($rslt);
				$AcalleridCOUNT=$row[0];

				if ( ($AcalleridCOUNT < 1) and (eregi("INCALL",$Astatus)) and (strlen($Aagent_log_id) > 0) )
					{
					$DEADcustomer++;
					### find whether the agent log record has already logged DEAD
					$stmt="SELECT count(*) from vicidial_agent_log where agent_log_id='$Aagent_log_id' and ( (dead_epoch IS NOT NULL) or (dead_epoch > 10000) );";
					if ($DB) {echo "|$stmt|\n";}
					$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'03019',$user,$server_ip,$session_name,$one_mysql_log);}
					$row=mysql_fetch_row($rslt);
					$Aagent_log_idCOUNT=$row[0];
					
					if ($Aagent_log_idCOUNT < 1)
						{
						$NEWdead_epoch = date("U");
						$deadNOW_TIME = date("Y-m-d H:i:s");
						$stmt="UPDATE vicidial_agent_log set dead_epoch='$NEWdead_epoch' where agent_log_id='$Aagent_log_id';";
							if ($format=='debug') {echo "\n<!-- $stmt -->";}
						$rslt=mysql_query($stmt, $link);
							if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'03020',$user,$server_ip,$session_name,$one_mysql_log);}

						$stmt="UPDATE vicidial_live_agents set last_state_change='$deadNOW_TIME' where agent_log_id='$Aagent_log_id';";
							if ($format=='debug') {echo "\n<!-- $stmt -->";}
						$rslt=mysql_query($stmt, $link);
							if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'03021',$user,$server_ip,$session_name,$one_mysql_log);}
						}
					}
				##### END DEAD logging section #####

				if ($campagentstdisp == 'YES')
					{
					$ADsql='';
					### grab the status of this agent to display
					$stmt="SELECT status,campaign_id,closer_campaigns from vicidial_live_agents where user='$user' and server_ip='$server_ip';";
					if ($DB) {echo "|$stmt|\n";}
					$rslt=mysql_query($stmt, $link);
			if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'03006',$user,$server_ip,$session_name,$one_mysql_log);}
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

					### grab the number of calls being placed from this server and campaign
					$stmt="SELECT count(*) from vicidial_auto_calls where status IN('LIVE') and ( (campaign_id='$Acampaign') or (campaign_id IN('$AccampSQL')) $ADsql);";
					if ($DB) {echo "|$stmt|\n";}
					$rslt=mysql_query($stmt, $link);
			if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'03007',$user,$server_ip,$session_name,$one_mysql_log);}
					$row=mysql_fetch_row($rslt);
					$RingCalls=$row[0];
					if ($RingCalls > 0) {$RingCalls = "<font class=\"queue_text_red\">Звонки в очереди: $RingCalls</font>";}
					else {$RingCalls = "<font class=\"queue_text\">Звонки в очереди: $RingCalls</font>";}

					### grab the number of calls being placed from this server and campaign
					$stmt="SELECT count(*) from vicidial_auto_calls where status NOT IN('XFER') and ( (campaign_id='$Acampaign') or (campaign_id IN('$AccampSQL')) );";
					if ($DB) {echo "|$stmt|\n";}
					$rslt=mysql_query($stmt, $link);
			if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'03008',$user,$server_ip,$session_name,$one_mysql_log);}
					$row=mysql_fetch_row($rslt);
					$DiaLCalls=$row[0];

					}
				else
					{
					$Alogin='N';
					$RingCalls='N';
					$DiaLCalls='N';
					}
				}
			else
				{
				$Alogin='N';
				$RingCalls='N';
				$DiaLCalls='N';

				### update the vicidial_live_agents every second with a new random number so it is shown to be alive
				$stmt="UPDATE vicidial_live_agents set random_id='$random' where user='$user' and server_ip='$server_ip';";
					if ($format=='debug') {echo "\n<!-- $stmt -->";}
				$rslt=mysql_query($stmt, $link);
			if ($mel > 0) {$errno = mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'03009',$user,$server_ip,$session_name,$one_mysql_log);}
				$retry_count=0;
				while ( ($errno > 0) and ($retry_count < 5) )
					{
					$rslt=mysql_query($stmt, $link);
					$one_mysql_log=1;
					$errno = mysql_error_logging($NOW_TIME,$link,$mel,$stmt,"9309$retry_count",$user,$server_ip,$session_name,$one_mysql_log);
					$one_mysql_log=0;
					$retry_count++;
					}
				##### BEGIN DEAD logging section #####
				### find whether the call the agent is на is hung up
				$stmt="SELECT count(*) from vicidial_auto_calls where callerid='$Acallerid';";
				if ($DB) {echo "|$stmt|\n";}
				$rslt=mysql_query($stmt, $link);
			if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'03029',$user,$server_ip,$session_name,$one_mysql_log);}
				$row=mysql_fetch_row($rslt);
				$AcalleridCOUNT=$row[0];

				if ( ($AcalleridCOUNT < 1) and (eregi("INCALL",$Astatus)) and (strlen($Aagent_log_id) > 0) )
					{
					$DEADcustomer++;
					### find whether the agent log record has already logged DEAD
					$stmt="SELECT count(*) from vicidial_agent_log where agent_log_id='$Aagent_log_id' and ( (dead_epoch IS NOT NULL) or (dead_epoch > 10000) );";
					if ($DB) {echo "|$stmt|\n";}
					$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'03030',$user,$server_ip,$session_name,$one_mysql_log);}
					$row=mysql_fetch_row($rslt);
					$Aagent_log_idCOUNT=$row[0];
					
					if ($Aagent_log_idCOUNT < 1)
						{
						$NEWdead_epoch = date("U");
						$deadNOW_TIME = date("Y-m-d H:i:s");
						$stmt="UPDATE vicidial_agent_log set dead_epoch='$NEWdead_epoch' where agent_log_id='$Aagent_log_id';";
							if ($format=='debug') {echo "\n<!-- $stmt -->";}
						$rslt=mysql_query($stmt, $link);
							if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'03031',$user,$server_ip,$session_name,$one_mysql_log);}

						$stmt="UPDATE vicidial_live_agents set last_state_change='$deadNOW_TIME' where agent_log_id='$Aagent_log_id';";
							if ($format=='debug') {echo "\n<!-- $stmt -->";}
						$rslt=mysql_query($stmt, $link);
							if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'03032',$user,$server_ip,$session_name,$one_mysql_log);}
						}
					}
				##### END DEAD logging section #####
				}

			### grab the API hangup and API dispo fields in vicidial_live_agents
			$stmt="SELECT external_hangup,external_status,external_pause,external_dial,external_update_fields,external_update_fields_data,external_timer_action,external_timer_action_message,external_timer_action_seconds from vicidial_live_agents where user='$user' and server_ip='$server_ip';";
			if ($DB) {echo "|$stmt|\n";}
			$rslt=mysql_query($stmt, $link);
			if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'03010',$user,$server_ip,$session_name,$one_mysql_log);}
			$row=mysql_fetch_row($rslt);
			$external_hangup =				$row[0];
			$external_status =				$row[1];
			$external_pause =				$row[2];
			$external_dial =				$row[3];
			$external_update_fields =		$row[4];
			$external_update_fields_data =	$row[5];
			$timer_action =					$row[6];
			$timer_action_message =			$row[7];
			$timer_action_seconds =			$row[8];

			if (strlen($external_status)<1) {$external_status = '::::::::::';}

			$web_epoch = date("U");
			$stmt="SELECT UNIX_TIMESTAMP(last_update),UNIX_TIMESTAMP(db_time) from server_updater where server_ip='$server_ip';";
			if ($DB) {echo "|$stmt|\n";}
			$rslt=mysql_query($stmt, $link);
			if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'03014',$user,$server_ip,$session_name,$one_mysql_log);}
			$row=mysql_fetch_row($rslt);
			$server_epoch =	$row[0];
			$db_epoch =	$row[1];
			$time_diff = ($server_epoch - $db_epoch);
			$web_diff = ($db_epoch - $web_epoch);

			##### check for in-group change details
			$InGroupChangeDetails = '0|||';
			$manager_ingroup_set=0;
			$stmt="SELECT count(*) FROM vicidial_live_agents where user='$user' and manager_ingroup_set='SET';";
			$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'03022',$user,$server_ip,$session_name,$one_mysql_log);}
			if ($DB) {echo "$stmt\n";}
			$mis_record_ct = mysql_num_rows($rslt);
			if ($mis_record_ct > 0)
				{
				$row=mysql_fetch_row($rslt);
				$manager_ingroup_set =		$row[0];
				}
			if ($manager_ingroup_set > 0)
				{
				$stmt="UPDATE vicidial_live_agents SET closer_campaigns=external_ingroups, manager_ingroup_set='Y' where user='$user' and manager_ingroup_set='SET';";
				if ($DB) {echo "$stmt\n";}
				$rslt=mysql_query($stmt, $link);
					if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'03023',$VD_login,$server_ip,$session_name,$one_mysql_log);}
				$VLAMISaffected_rows_update = mysql_affected_rows($link);
				if ($VLAMISaffected_rows_update > 0)
					{
					$stmt="SELECT external_ingroups,external_blended,external_igb_set_user,outbound_autodial,dial_method FROM vicidial_live_agents vla, vicidial_campaigns vc where user='$user' and manager_ingroup_set='Y' and vla.campaign_id=vc.campaign_id;";
					$rslt=mysql_query($stmt, $link);
						if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'03024',$user,$server_ip,$session_name,$one_mysql_log);}
					if ($DB) {echo "$stmt\n";}
					$migs_record_ct = mysql_num_rows($rslt);
					if ($migs_record_ct > 0)
						{
						$row=mysql_fetch_row($rslt);
						$external_ingroups =		$row[0];
						$external_blended =			$row[1];
						$external_igb_set_user =	$row[2];
						$outbound_autodial =		$row[3];
						$dial_method =				$row[4];

						$stmt="SELECT full_name FROM vicidial_users where user='$external_igb_set_user';";
						$rslt=mysql_query($stmt, $link);
							if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'03025',$user,$server_ip,$session_name,$one_mysql_log);}
						if ($DB) {echo "$stmt\n";}
						$mign_record_ct = mysql_num_rows($rslt);
						if ($mign_record_ct > 0)
							{
							$row=mysql_fetch_row($rslt);
							$external_igb_set_name =		$row[0];
							}
						
						$NEWoutbound_autodial='N';
						if ( ($external_blended > 0) and ($dial_method != "INBOUND_MAN") and ($dial_method != "MANUAL") )
							{$NEWoutbound_autodial='Y';}

						$stmt="UPDATE vicidial_live_agents SET outbound_autodial='$NEWoutbound_autodial' where user='$user';";
						if ($DB) {echo "$stmt\n";}
						$rslt=mysql_query($stmt, $link);
							if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'03026',$VD_login,$server_ip,$session_name,$one_mysql_log);}
						$VLAMIBaffected_rows_update = mysql_affected_rows($link);

						$InGroupChangeDetails = "1|$external_blended|$external_igb_set_user|$external_igb_set_name";

						$stmt="INSERT INTO vicidial_user_closer_log set user='$user',campaign_id='$Acampaign_id',event_date='$NOW_TIME',blended='$external_blended',closer_campaigns='$external_ingroups',manager_change='$external_igb_set_user';";
							if ($format=='debug') {echo "\n<!-- $stmt -->";}
						$rslt=mysql_query($stmt, $link);
							if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'03027',$user,$server_ip,$session_name,$one_mysql_log);}
						}
					}
				}

			##### grab the shift information the agent
			$stmt="SELECT user_group,agent_shift_enforcement_override from vicidial_users where user='$user';";
			$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'03015',$VD_login,$server_ip,$session_name,$one_mysql_log);}
			$row=mysql_fetch_row($rslt);
			$VU_user_group =						$row[0];
			$VU_agent_shift_enforcement_override =	$row[1];

			### Gather timeclock and shift enforcement restriction settings
			$stmt="SELECT shift_enforcement,group_shifts from vicidial_user_groups where user_group='$VU_user_group';";
			$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'03016',$VD_login,$server_ip,$session_name,$one_mysql_log);}
			$row=mysql_fetch_row($rslt);
			$shift_enforcement =		$row[0];
			$LOGgroup_shiftsSQL = eregi_replace('  ','',$row[1]);
			$LOGgroup_shiftsSQL = eregi_replace(' ',"','",$LOGgroup_shiftsSQL);
			$LOGgroup_shiftsSQL = "shift_id IN('$LOGgroup_shiftsSQL')";

			### CHECK TO SEE IF AGENT IS WITHIN THEIR SHIFT IF RESTRICTED, IF NOT, OUTPUT ERROR
			$Ashift_logout=0;
			if ( ( (ereg("ALL",$shift_enforcement)) and (!ereg("OFF|START",$VU_agent_shift_enforcement_override)) ) or (ereg("ALL",$VU_agent_shift_enforcement_override)) )
				{
				$shift_ok=0;
				if (strlen($LOGgroup_shiftsSQL) < 3)
					{$Ashift_logout++;}
				else
					{
					$HHMM = date("Hi");
					$wday = date("w");

					$stmt="SELECT shift_id,shift_start_time,shift_length,shift_weekdays from vicidial_shifts where $LOGgroup_shiftsSQL order by shift_id";
					$rslt=mysql_query($stmt, $link);
						if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'3017',$user,$server_ip,$session_name,$one_mysql_log);}
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

					if ($shift_ok < 1)
						{$Ashift_logout++;}
					}
				}


			if ( ( ($time_diff > 8) or ($time_diff < -8) or ($web_diff > 8) or ($web_diff < -8) ) and (eregi("0$",$StarTtime)) ) 
				{$Alogin='TIME_SYNC';}
			if ($Acount < 1) 
				{$Alogin='DEAD_VLA';}
			if ($AexternalDEAD > 0) 
				{$Alogin='DEAD_EXTERNAL';}
			if ($Ashift_logout > 0)
				{$Alogin='SHIFT_LOGOUT';}

			echo 'DateTime: ' . $NOW_TIME . '|UnixTime: ' . $StarTtime . '|Logged-in: ' . $Alogin . '|CampCalls: ' . $RingCalls . '|Статус: ' . $Astatus . '|DiaLCalls: ' . $DiaLCalls . '|APIHanguP: ' . $external_hangup . '|APIStatuS: ' . $external_status . '|APIPausE: ' . $external_pause . '|APIDiaL: ' . $external_dial . '|DEADcall: ' . $DEADcustomer . '|InGroupChange: ' . $InGroupChangeDetails . '|APIFields: ' . $external_update_fields . '|APIFieldsData: ' . $external_update_fields_data . '|APITimerAction: ' . $timer_action . '|APITimerMessage: ' . $timer_action_message . '|APITimerSeconds: ' . $timer_action_seconds . "\n";

			if (strlen($timer_action) > 3)
				{
				$stmt="UPDATE vicidial_live_agents SET external_timer_action='' where user='$user';";
				if ($DB) {echo "$stmt\n";}
				$rslt=mysql_query($stmt, $link);
					if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'03028',$VD_login,$server_ip,$session_name,$one_mysql_log);}
				$VLAETAaffected_rows_update = mysql_affected_rows($link);
				}
			}
		$total_conf=0;
		$stmt="SELECT channel FROM live_sip_channels where server_ip = '$server_ip' and extension = '$conf_exten';";
			if ($format=='debug') {echo "\n<!-- $stmt -->";}
		$rslt=mysql_query($stmt, $link);
			if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'03011',$user,$server_ip,$session_name,$one_mysql_log);}
		if ($rslt) {$sip_list = mysql_num_rows($rslt);}
	#	echo "$sip_list|";
		$loop_count=0;
			while ($sip_list>$loop_count)
			{
			$loop_count++; $total_conf++;
			$row=mysql_fetch_row($rslt);
			$ChannelA[$total_conf] = "$row[0]";
			if ($format=='debug') {echo "\n<!-- $row[0] -->";}
			}
		$stmt="SELECT channel FROM live_channels where server_ip = '$server_ip' and extension = '$conf_exten';";
			if ($format=='debug') {echo "\n<!-- $stmt -->";}
		$rslt=mysql_query($stmt, $link);
			if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'03012',$user,$server_ip,$session_name,$one_mysql_log);}
		if ($rslt) {$channels_list = mysql_num_rows($rslt);}
	#	echo "$channels_list|";
		$loop_count=0;
		while ($channels_list>$loop_count)
			{
			$loop_count++; $total_conf++;
			$row=mysql_fetch_row($rslt);
			$ChannelA[$total_conf] = "$row[0]";
			if ($format=='debug') {echo "\n<!-- $row[0] -->";}
			}
		}
		$channels_list = ($channels_list + $sip_list);
		echo "$channels_list|";

		$counter=0;
		$countecho='';
		while($total_conf > $counter)
		{
			$counter++;
			$countecho = "$countecho$ChannelA[$counter] ~";
		#	echo "$ChannelA[$counter] ~";
		}

	echo "$countecho\n";
	}

	if ($ACTION == 'register')
	{
		$MT[0]='';
		$row='';   $rowx='';
		$channel_live=1;
		if ( (strlen($conf_exten)<1) || (strlen($exten)<1) )
		{
		$channel_live=0;
		echo "Conf Exten $conf_exten это неправильно or Exten $exten это неправильно\n";
		exit;
		}
		else
		{
		$stmt="UPDATE conferences set extension='$exten' where server_ip = '$server_ip' and conf_exten = '$conf_exten';";
			if ($format=='debug') {echo "\n<!-- $stmt -->";}
		$rslt=mysql_query($stmt, $link);
			if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'03013',$user,$server_ip,$session_name,$one_mysql_log);}
		}
		echo "Конференция $conf_exten был зарегистрирован на $exten\n";
	}



if ($format=='debug') 
	{
	$ENDtime = date("U");
	$RUNtime = ($ENDtime - $StarTtime);
	echo "\n<!-- время выполнения скрипта: $RUNtime секунды -->";
	echo "\n</body>\n</html>\n";
	}
	
exit; 


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
		fwrite ($efp, "$NOW_TIME|conf_check  |$query_id|$errno|$error|$stmt|$user|$server_ip|$session_name|\n");
		fclose($efp);
		}
	}
$one_mysql_log=0;
return $errno;
}

?>
