<?php
# manager_send.php    version 2.2.0
# 
# Copyright (C) 2010  Matt Florell <vicidial@gmail.com>    LICENSE: AGPLv2
#
# This script is designed purely to insert records into the vicidial_manager table to signal Actions to an asterisk server
# This script depends on the server_ip being sent and also needs to have a valid user/pass from the vicidial_users table
# 
# required variables:
#  - $server_ip
#  - $session_name
#  - $user
#  - $pass
# optional variables:
#  - $ACTION - ('Originate','Redirect','Hangup','Command','Monitor','StopMonitor','SysCIDOriginate','RedirectName','RedirectNameVmail','MonitorConf','StopMonitorConf','RedirectXtra','RedirectXtraCX','RedirectVD','HangupConfDial','VolumeControl','OriginateVDRelogin')
#  - $queryCID - ('CN012345678901234567',...)
#  - $format - ('text','debug')
#  - $channel - ('Zap/41-1','SIP/test101-1jut','IAX2/iaxy@iaxy',...)
#  - $exten - ('1234','913125551212',...)
#  - $ext_context - ('default','demo',...)
#  - $ext_priority - ('1','2',...)
#  - $filename - ('20050406-125623_44444',...)
#  - $extenName - ('phone100',...)
#  - $parkedby - ('phone100',...)
#  - $extrachannel - ('Zap/41-1','SIP/test101-1jut','IAX2/iaxy@iaxy',...)
#  - $auto_dial_level - ('0','1','1.1',...)
#  - $campaign - ('CLOSER','TESTCAMP',...)
#  - $uniqueid - ('1120232758.2406800',...)
#  - $lead_id - ('1234',...)
#  - $seconds - ('32',...)
#  - $outbound_cid - ('3125551212','0000000000',...)
#  - $agent_log_id - ('123456',...)
#  - $call_server_ip - ('10.10.10.15',...)
#  - $CalLCID - ('VD01234567890123456',...)
#  - $stage - ('UP','DOWN','2NDXfeR')
#  - $session_id - ('8600051')
#  - $FROMvdc - ('YES','NO')
#  - $agentchannel - ('SIP/cc101-g7yr','Zap/1-1',...)
#  - $usegroupalias - ('0','1')
#  - $account - ('DEFAULT',...)
#  - $agent_dialed_number - ('1','')
#  - $agent_dialed_type - ('MANUAL_OVERRIDE','MANUAL_DIALNOW','MANUAL_PREVIEW',...)
#  - $nodeletevdac - ('0','1')
#
# CHANGELOG:
# 50401-1002 - First build of script, Hangup function only
# 50404-1045 - Redirect basic function enabled
# 50406-1522 - Monitor basic function enabled
# 50407-1647 - Monitor and StopMonitor full functions enabled
# 50422-1120 - basic Originate function enabled
# 50428-1451 - basic SysCIDOriginate function enabled for checking voicemail
# 50502-1539 - basic RedirectName and RedirectNameVmail added
# 50503-1227 - added session_name checking for extra security
# 50523-1341 - added Conference call start/stop recording
# 50523-1421 - added OriginateName and OriginateNameVmail for local calls
# 50524-1602 - added RedirectToPark and RedirectFromPark
# 50531-1203 - added RedirecXtra for dual channel redirection
# 50630-1100 - script changed to not use HTTP login vars, user/pass instead
# 50804-1148 - Added RedirectVD for VICIDIAL blind redirection with logging
# 50815-1204 - Added NEXTAVAILABLE to RedirectXtra function
# 50903-2343 - Added HangupConfDial function to hangup in-dial channels in conf
# 50913-1057 - Added outbound_cid set if present to originate call
# 51020-1556 - Added agent_log_id framework for detailed agent activity logging
# 51118-1204 - Fixed Blind transfer bug from VICIDIAL when in manual dial mode
# 51129-1014 - Added ability to accept calls from other VICIDIAL servers
# 51129-1253 - Fixed Hangups of other agents channels in VICIDIAL AD
# 60310-2022 - Fixed NEXTAVAILABLE bug in leave-3way-call redirect function
# 60421-1413 - check GET/POST vars lines with isset to not trigger PHP NOTICES
# 60619-1158 - Added variable filters to close security holes for login form
# 60809-1544 - Added direct transfers to leave-3ways in consultative transfers
# 61004-1526 - Added parsing of volume control command and lookup or number
# 61130-1617 - Added lead_id to MonitorConf for recording_log
# 61201-1115 - Added user to MonitorConf for recording_log
# 70111-1600 - added ability to use BLEND/INBND/*_C/*_B/*_I as closer campaigns
# 70226-1251 - Added Mute/UnMute to conference volume control
# 70320-1502 - Added option to allow retry of leave-3way-call and debug logging
# 70322-1636 - Added sipsak display ability
# 80331-1433 - Added second transfer try for VICIDIAL transfers on manual dial calls
# 80402-0121 - Fixes for manual dial transfers on some systems
# 80424-0442 - Added non_latin lookup from system_settings
# 80707-2325 - Added vicidial_id to recording_log for tracking of vicidial or closer log to recording
# 80915-1755 - Rewrote leave-3way functions for external calling
# 81011-1404 - Fixed bugs in leave3way when transferring a manual dial call
# 81020-1459 - Fixed bugs in queue_log logging
# 81104-0203 - Added mysql error logging capability
# 90303-1144 - Fixed manual dial live hangup bug
# 90304-1334 - Added account and usegroupalias and user campaign/in-group specific variables
# 90305-1040 - Added agent_dialed_number and type for user_call_log feature
# 90508-0727 - Changed to PHP long tags
# 90511-1019 - Added restriction not allowing dialing into agent sessions from manual dial
# 90913-1410 - Fixed minor logging bug
# 90916-1830 - Added nodeletevdac
# 90924-1555 - Added am_message_exten_override  for list_id option
# 91112-1110 - Added CALLOUTBOUND value to QM entry lookup
# 91205-2103 - Code cleanup
# 91213-1208 - Added queue_position to queue_log COMPLETE... records
# 100327-0846 - Fix for list_id override answering machine message

$version = '2.2.0-47';
$build = '100327-0846';
$mel=1;					# Mysql Error Log enabled = 1
$mysql_log_count=85;
$one_mysql_log=0;

require("dbconnect.php");

### These are variable assignments for PHP globals off
if (isset($_GET["user"]))					{$user=$_GET["user"];}
	elseif (isset($_POST["user"]))			{$user=$_POST["user"];}
if (isset($_GET["pass"]))					{$pass=$_GET["pass"];}
	elseif (isset($_POST["pass"]))			{$pass=$_POST["pass"];}
if (isset($_GET["server_ip"]))				{$server_ip=$_GET["server_ip"];}
	elseif (isset($_POST["server_ip"]))		{$server_ip=$_POST["server_ip"];}
if (isset($_GET["session_name"]))			{$session_name=$_GET["session_name"];}
	elseif (isset($_POST["session_name"]))	{$session_name=$_POST["session_name"];}
if (isset($_GET["ACTION"]))					{$ACTION=$_GET["ACTION"];}
	elseif (isset($_POST["ACTION"]))		{$ACTION=$_POST["ACTION"];}
if (isset($_GET["queryCID"]))				{$queryCID=$_GET["queryCID"];}
	elseif (isset($_POST["queryCID"]))		{$queryCID=$_POST["queryCID"];}
if (isset($_GET["format"]))					{$format=$_GET["format"];}
	elseif (isset($_POST["format"]))		{$format=$_POST["format"];}
if (isset($_GET["channel"]))				{$channel=$_GET["channel"];}
	elseif (isset($_POST["channel"]))		{$channel=$_POST["channel"];}
if (isset($_GET["exten"]))					{$exten=$_GET["exten"];}
	elseif (isset($_POST["exten"]))			{$exten=$_POST["exten"];}
if (isset($_GET["ext_context"]))			{$ext_context=$_GET["ext_context"];}
	elseif (isset($_POST["ext_context"]))	{$ext_context=$_POST["ext_context"];}
if (isset($_GET["ext_priority"]))			{$ext_priority=$_GET["ext_priority"];}
	elseif (isset($_POST["ext_priority"]))	{$ext_priority=$_POST["ext_priority"];}
if (isset($_GET["filename"]))				{$filename=$_GET["filename"];}
	elseif (isset($_POST["filename"]))		{$filename=$_POST["filename"];}
if (isset($_GET["extenName"]))				{$extenName=$_GET["extenName"];}
	elseif (isset($_POST["extenName"]))		{$extenName=$_POST["extenName"];}
if (isset($_GET["parkedby"]))				{$parkedby=$_GET["parkedby"];}
	elseif (isset($_POST["parkedby"]))		{$parkedby=$_POST["parkedby"];}
if (isset($_GET["extrachannel"]))			{$extrachannel=$_GET["extrachannel"];}
	elseif (isset($_POST["extrachannel"]))	{$extrachannel=$_POST["extrachannel"];}
if (isset($_GET["auto_dial_level"]))			{$auto_dial_level=$_GET["auto_dial_level"];}
	elseif (isset($_POST["auto_dial_level"]))	{$auto_dial_level=$_POST["auto_dial_level"];}
if (isset($_GET["campaign"]))				{$campaign=$_GET["campaign"];}
	elseif (isset($_POST["campaign"]))		{$campaign=$_POST["campaign"];}
if (isset($_GET["uniqueid"]))				{$uniqueid=$_GET["uniqueid"];}
	elseif (isset($_POST["uniqueid"]))		{$uniqueid=$_POST["uniqueid"];}
if (isset($_GET["lead_id"]))				{$lead_id=$_GET["lead_id"];}
	elseif (isset($_POST["lead_id"]))		{$lead_id=$_POST["lead_id"];}
if (isset($_GET["secondS"]))				{$secondS=$_GET["secondS"];}
	elseif (isset($_POST["secondS"]))		{$secondS=$_POST["secondS"];}
if (isset($_GET["outbound_cid"]))			{$outbound_cid=$_GET["outbound_cid"];}
	elseif (isset($_POST["outbound_cid"]))	{$outbound_cid=$_POST["outbound_cid"];}
if (isset($_GET["agent_log_id"]))			{$agent_log_id=$_GET["agent_log_id"];}
	elseif (isset($_POST["agent_log_id"]))	{$agent_log_id=$_POST["agent_log_id"];}
if (isset($_GET["call_server_ip"]))				{$call_server_ip=$_GET["call_server_ip"];}
	elseif (isset($_POST["call_server_ip"]))	{$call_server_ip=$_POST["call_server_ip"];}
if (isset($_GET["CalLCID"]))				{$CalLCID=$_GET["CalLCID"];}
	elseif (isset($_POST["CalLCID"]))		{$CalLCID=$_POST["CalLCID"];}
if (isset($_GET["phone_code"]))				{$phone_code=$_GET["phone_code"];}
	elseif (isset($_POST["phone_code"]))	{$phone_code=$_POST["phone_code"];}
if (isset($_GET["phone_number"]))			{$phone_number=$_GET["phone_number"];}
	elseif (isset($_POST["phone_number"]))	{$phone_number=$_POST["phone_number"];}
if (isset($_GET["stage"]))					{$stage=$_GET["stage"];}
	elseif (isset($_POST["stage"]))			{$stage=$_POST["stage"];}
if (isset($_GET["extension"]))				{$extension=$_GET["extension"];}
	elseif (isset($_POST["extension"]))		{$extension=$_POST["extension"];}
if (isset($_GET["protocol"]))				{$protocol=$_GET["protocol"];}
	elseif (isset($_POST["protocol"]))		{$protocol=$_POST["protocol"];}
if (isset($_GET["phone_ip"]))				{$phone_ip=$_GET["phone_ip"];}
	elseif (isset($_POST["phone_ip"]))		{$phone_ip=$_POST["phone_ip"];}
if (isset($_GET["enable_sipsak_messages"]))				{$enable_sipsak_messages=$_GET["enable_sipsak_messages"];}
	elseif (isset($_POST["enable_sipsak_messages"]))	{$enable_sipsak_messages=$_POST["enable_sipsak_messages"];}
if (isset($_GET["allow_sipsak_messages"]))				{$allow_sipsak_messages=$_GET["allow_sipsak_messages"];}
	elseif (isset($_POST["allow_sipsak_messages"]))		{$allow_sipsak_messages=$_POST["allow_sipsak_messages"];}
if (isset($_GET["session_id"]))				{$session_id=$_GET["session_id"];}
	elseif (isset($_POST["session_id"]))	{$session_id=$_POST["session_id"];}
if (isset($_GET["FROMvdc"]))				{$FROMvdc=$_GET["FROMvdc"];}
	elseif (isset($_POST["FROMvdc"]))		{$FROMvdc=$_POST["FROMvdc"];}
if (isset($_GET["agentchannel"]))			{$agentchannel=$_GET["agentchannel"];}
	elseif (isset($_POST["agentchannel"]))	{$agentchannel=$_POST["agentchannel"];}
if (isset($_GET["usegroupalias"]))			{$usegroupalias=$_GET["usegroupalias"];}
	elseif (isset($_POST["usegroupalias"]))	{$usegroupalias=$_POST["usegroupalias"];}
if (isset($_GET["account"]))				{$account=$_GET["account"];}
	elseif (isset($_POST["account"]))		{$account=$_POST["account"];}
if (isset($_GET["agent_dialed_number"]))			{$agent_dialed_number=$_GET["agent_dialed_number"];}
	elseif (isset($_POST["agent_dialed_number"]))	{$agent_dialed_number=$_POST["agent_dialed_number"];}
if (isset($_GET["agent_dialed_type"]))				{$agent_dialed_type=$_GET["agent_dialed_type"];}
	elseif (isset($_POST["agent_dialed_type"]))		{$agent_dialed_type=$_POST["agent_dialed_type"];}
if (isset($_GET["nodeletevdac"]))				{$nodeletevdac=$_GET["nodeletevdac"];}
	elseif (isset($_POST["nodeletevdac"]))		{$nodeletevdac=$_POST["nodeletevdac"];}

header ("Content-type: text/html; charset=utf-8");
header ("Cache-Control: no-cache, must-revalidate");  // HTTP/1.1
header ("Pragma: no-cache");                          // HTTP/1.0

#############################################
##### START SYSTEM_SETTINGS LOOKUP #####
$stmt = "SELECT use_non_latin FROM system_settings;";
$rslt=mysql_query($stmt, $link);
	if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'02001',$user,$server_ip,$session_name,$one_mysql_log);}
if ($DB) {echo "$stmt\n";}
$qm_conf_ct = mysql_num_rows($rslt);
if ($qm_conf_ct > 0)
	{
	$row=mysql_fetch_row($rslt);
	$non_latin =		$row[0];
	}
##### END SETTINGS LOOKUP #####
###########################################

if ($non_latin < 1)
	{
	$user=ereg_replace("[^-_0-9a-zA-Z]","",$user);
	$pass=ereg_replace("[^-_0-9a-zA-Z]","",$pass);
	$secondS = ereg_replace("[^0-9]","",$secondS);
	}
else
	{
	$user = ereg_replace("'|\"|\\\\|;","",$user);
	$pass = ereg_replace("'|\"|\\\\|;","",$pass);
	}


# default optional vars if not set
if (!isset($ACTION))   {$ACTION="Originate";}
if (!isset($format))   {$format="alert";}
if (!isset($ext_priority))   {$ext_priority="1";}

$StarTtime = date("U");
$NOW_DATE = date("Y-m-d");
$NOW_TIME = date("Y-m-d H:i:s");
$NOWnum = date("YmdHis");
if (!isset($query_date)) {$query_date = $NOW_DATE;}

$stmt="SELECT count(*) from vicidial_users where user='$user' and pass='$pass' and user_level > 0;";
if ($DB) {echo "|$stmt|\n";}
if ($non_latin > 0) {$rslt=mysql_query("SET NAMES 'UTF8'");}
$rslt=mysql_query($stmt, $link);
			if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'02002',$user,$server_ip,$session_name,$one_mysql_log);}
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
			if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'02003',$user,$server_ip,$session_name,$one_mysql_log);}
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

if ($format=='debug')
	{
	echo "<html>\n";
	echo "<head>\n";
	echo "<!-- VERSION: $version     SKAPA: $build    ACTION: $ACTION   server_ip: $server_ip-->\n";
	echo "<title>Skicka till manager: ";
	if ($ACTION=="Originate")		{echo "Originate";}
	if ($ACTION=="Redirect")		{echo "Redirect";}
	if ($ACTION=="RedirectName")	{echo "RedirectName";}
	if ($ACTION=="Hangup")			{echo "Hangup";}
	if ($ACTION=="Command")			{echo "Command";}
	if ($ACTION==99999)	{echo "HJÄLP";}
	echo "</title>\n";
	echo "</head>\n";
	echo "<BODY BGCOLOR=white marginheight=0 marginwidth=0 leftmargin=0 topmargin=0>\n";
	}





######################
# ACTION=SysCIDOriginate  - insert Originate Manager statement allowing small CIDs for system calls
######################
if ($ACTION=="SysCIDOriginate")
	{
	if ( (strlen($exten)<1) or (strlen($channel)<1) or (strlen($ext_context)<1) or (strlen($queryCID)<1) )
		{
		echo "Exten $exten är ej giltig or queryCID $queryCID är ej giltig, Originate kommandot skrevs ej\n";
		}
	else
		{
		$stmt="INSERT INTO vicidial_manager values('','','$NOW_TIME','NEW','N','$server_ip','','Originate','$queryCID','Channel: $channel','Context: $ext_context','Exten: $exten','Priority: $ext_priority','Callerid: $queryCID','','','','','');";
			if ($format=='debug') {echo "\n<!-- $stmt -->";}
		$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'02004',$user,$server_ip,$session_name,$one_mysql_log);}
		echo "Originate kommandot skickat till Exten $exten Kanal $channel på $server_ip\n";
		}
	}



######################
# ACTION=Originate, OriginateName, OriginateNameVmail  - insert Originate Manager statement
######################
if ($ACTION=="OriginateName")
	{
	if ( (strlen($channel)<3) or (strlen($queryCID)<15)  or (strlen($extenName)<1)  or (strlen($ext_context)<1)  or (strlen($ext_priority)<1) )
		{
		$channel_live=0;
		echo "En av dessa variabler är ej giltig:\n";
		echo "Channel $channel måste vara mer än 2 tecken\n";
		echo "queryCID $queryCID måste vara mer än 14 tecken\n";
		echo "extenName $extenName måste väljas\n";
		echo "ext_context $ext_context måste väljas\n";
		echo "ext_priority $ext_priority måste väljas\n";
		echo "\nOriginateName Action ej skickat\n";
		}
	else
		{
		$stmt="SELECT dialplan_number FROM phones where server_ip = '$server_ip' and extension='$extenName';";
			if ($format=='debug') {echo "\n<!-- $stmt -->";}
		$rslt=mysql_query($stmt, $link);
			if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'02005',$user,$server_ip,$session_name,$one_mysql_log);}
		$name_count = mysql_num_rows($rslt);
		if ($name_count>0)
			{
			$row=mysql_fetch_row($rslt);
			$exten = $row[0];
			$ACTION="Originate";
			}
		}
	}

if ($ACTION=="OriginateNameVmail")
	{
	if ( (strlen($channel)<3) or (strlen($queryCID)<15)  or (strlen($extenName)<1)  or (strlen($exten)<1)  or (strlen($ext_context)<1)  or (strlen($ext_priority)<1) )
		{
		$channel_live=0;
		echo "En av dessa variabler är ej giltig:\n";
		echo "Channel $channel måste vara mer än 2 tecken\n";
		echo "queryCID $queryCID måste vara mer än 14 tecken\n";
		echo "extenName $extenName måste väljas\n";
		echo "exten $exten måste väljas\n";
		echo "ext_context $ext_context måste väljas\n";
		echo "ext_priority $ext_priority måste väljas\n";
		echo "\nOriginateNameVmail Action ej skickat\n";
		}
	else
		{
		$stmt="SELECT voicemail_id FROM phones where server_ip = '$server_ip' and extension='$extenName';";
			if ($format=='debug') {echo "\n<!-- $stmt -->";}
		$rslt=mysql_query($stmt, $link);
			if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'02006',$user,$server_ip,$session_name,$one_mysql_log);}
		$name_count = mysql_num_rows($rslt);
		if ($name_count>0)
			{
			$row=mysql_fetch_row($rslt);
			$exten = "$exten$row[0]";
			$ACTION="Originate";
			}
		}
	}

if ($ACTION=="OriginateVDRelogin")
	{
	if ( ($enable_sipsak_messages > 0) and ($allow_sipsak_messages > 0) and (eregi("SIP",$protocol)) )
		{
		$CIDdate = date("ymdHis");
		$DS='-';
		$SIPSAK_prefix = 'LIN-';
		print "<!-- sending login sipsak message: $SIPSAK_prefix$VD_campaign -->\n";
		passthru("/usr/local/bin/sipsak -M -O desktop -B \"$SIPSAK_prefix$campaign\" -r 5060 -s sip:$extension@$phone_ip > /dev/null");
		$queryCID = "$SIPSAK_prefix$campaign$DS$CIDdate";

		}
	$ACTION="Originate";
	}

if ($ACTION=="Originate")
	{
	if ( (strlen($exten)<1) or (strlen($channel)<1) or (strlen($ext_context)<1) or (strlen($queryCID)<10) )
		{
		echo "ERROR Exten $exten är ej giltig or queryCID $queryCID är ej giltig, Originate kommandot skrevs ej\n";
		}
	else
		{
		if ( (eregi('MANUAL',$agent_dialed_type)) and ( (preg_match("/^\d860\d\d\d\d$/i",$exten)) or (preg_match("/^860\d\d\d\d$/i",$exten)) ) )
			{
			echo "ERROR Du har inte behörighet att logga in på andra agenters sessions $exten\n";
			exit;
			}

		if (strlen($outbound_cid)>1)
			{$outCID = "\"$queryCID\" <$outbound_cid>";}
		else
			{$outCID = "$queryCID";}
		if ( ($usegroupalias > 0) and (strlen($account)>1) )
			{
			$RAWaccount = $account;
			$account = "Account: $account";
			$variable = "Variable: usegroupalias=1";
			}
		else
			{$account='';   $variable='';}
		$stmt="INSERT INTO vicidial_manager values('','','$NOW_TIME','NEW','N','$server_ip','','Originate','$queryCID','Channel: $channel','Context: $ext_context','Exten: $exten','Priority: $ext_priority','Callerid: $outCID','$account','$variable','','','');";
			if ($format=='debug') {echo "\n<!-- $stmt -->";}
		$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'02007',$user,$server_ip,$session_name,$one_mysql_log);}
		echo "Originate kommandot skickat till Exten $exten Kanal $channel på $server_ip |$account|$variable|\n";

		if ($agent_dialed_number > 0)
			{
			$stmt = "INSERT INTO user_call_log (user,call_date,call_type,server_ip,phone_number,number_dialed,lead_id,callerid,group_alias_id) values('$user','$NOW_TIME','$agent_dialed_type','$server_ip','$exten','$channel','0','$outbound_cid','$RAWaccount')";
			if ($DB) {echo "$stmt\n";}
			$rslt=mysql_query($stmt, $link);
		if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00192',$user,$server_ip,$session_name,$one_mysql_log);}
			}
		}
	}



######################
# ACTION=HangupConfDial  - find the Local channel that is in the conference and needs to be hung up
######################
if ($ACTION=="HangupConfDial")
	{
	$row='';   $rowx='';
	$channel_live=1;
	if ( (strlen($exten)<3) or (strlen($queryCID)<15) or (strlen($ext_context)<1) )
		{
		$channel_live=0;
		echo "conference $exten är ej giltig or ext_context $ext_context or queryCID $queryCID är ej giltig, Hangup kommandot skrevs ej\n";
		}
	else
		{
		$local_DEF = 'Local/';
		$local_AMP = '@';
		$hangup_channel_prefix = "$local_DEF$exten$local_AMP$ext_context";

		$stmt="SELECT count(*) FROM live_sip_channels where server_ip = '$server_ip' and channel LIKE \"$hangup_channel_prefix%\";";
			if ($format=='debug') {echo "\n<!-- $stmt -->";}
		$rslt=mysql_query($stmt, $link);
			if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'02008',$user,$server_ip,$session_name,$one_mysql_log);}
		$row=mysql_fetch_row($rslt);
		if ($row > 0)
			{
			$stmt="SELECT channel FROM live_sip_channels where server_ip = '$server_ip' and channel LIKE \"$hangup_channel_prefix%\";";
				if ($format=='debug') {echo "\n<!-- $stmt -->";}
			$rslt=mysql_query($stmt, $link);
			if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'02009',$user,$server_ip,$session_name,$one_mysql_log);}
			$rowx=mysql_fetch_row($rslt);
			$channel=$rowx[0];
			$ACTION="Hangup";
			$queryCID = eregi_replace("^.","G",$queryCID);  # GTvdcW...
			}
		}
	}



######################
# ACTION=Hangup  - insert Hangup Manager statement
######################
if ($ACTION=="Hangup")
	{
	$stmt="UPDATE vicidial_live_agents SET external_hangup='0' where user='$user';";
		if ($format=='debug') {echo "\n<!-- $stmt -->";}
	$rslt=mysql_query($stmt, $link);
			if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'02010',$user,$server_ip,$session_name,$one_mysql_log);}

	$row='';   $rowx='';
	$channel_live=1;
	if ( (strlen($channel)<3) or (strlen($queryCID)<15) )
		{
		$channel_live=0;
		echo "Channel $channel är ej giltig or queryCID $queryCID är ej giltig, Hangup kommandot skrevs ej\n";
		}
	else
		{
		if (strlen($call_server_ip)<7) {$call_server_ip = $server_ip;}

#		$stmt="SELECT count(*) FROM live_channels where server_ip = '$call_server_ip' and channel='$channel';";
#			if ($format=='debug') {echo "\n<!-- $stmt -->";}
#		$rslt=mysql_query($stmt, $link);
#		$row=mysql_fetch_row($rslt);
#		if ($row[0]==0)
#			{
#			$stmt="SELECT count(*) FROM live_sip_channels where server_ip = '$call_server_ip' and channel='$channel';";
#				if ($format=='debug') {echo "\n<!-- $stmt -->";}
#			$rslt=mysql_query($stmt, $link);
#			$rowx=mysql_fetch_row($rslt);
#			if ($rowx[0]==0)
#				{
#				$channel_live=0;
#				echo "Channel $channel is not live on $call_server_ip, Hangup command not inserted\n";
#				}	
#			}
		if ( ($auto_dial_level > 0) and (strlen($CalLCID)>2) and (strlen($exten)>2) and ($secondS > 0))
			{
			$stmt="SELECT count(*) FROM vicidial_auto_calls where channel='$channel' and callerid='$CalLCID';";
				if ($format=='debug') {echo "\n<!-- $stmt -->";}
			$rslt=mysql_query($stmt, $link);
			if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'02011',$user,$server_ip,$session_name,$one_mysql_log);}
			$rowx=mysql_fetch_row($rslt);
			if ($rowx[0]==0)
				{
				echo "Call $CalLCID $channel är inte aktivt på $call_server_ip, Checking Live Kanal...\n";

				$stmt="SELECT count(*) FROM live_channels where server_ip = '$call_server_ip' and channel='$channel' and extension LIKE \"%$exten\";";
					if ($format=='debug') {echo "\n<!-- $stmt -->";}
				$rslt=mysql_query($stmt, $link);
			if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'02012',$user,$server_ip,$session_name,$one_mysql_log);}
				$row=mysql_fetch_row($rslt);
				if ($row[0]==0)
					{
					$channel_live=0;
					echo "Channel $channel är inte aktivt på $call_server_ip, Hangup kommandot skrevs ej $rowx[0]\n$stmt\n";
					}
				else
					{
					echo "$stmt\n";
					}
				}
			}
		if ( ($auto_dial_level < 1) and (strlen($stage)>2) and (strlen($channel)>2) and (strlen($exten)>2) )
			{
			$stmt="SELECT count(*) FROM live_channels where server_ip = '$call_server_ip' and channel='$channel' and extension NOT LIKE \"%$exten%\";";
				if ($format=='debug') {echo "\n<!-- $stmt -->";}
			$rslt=mysql_query($stmt, $link);
		if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'02083',$user,$server_ip,$session_name,$one_mysql_log);}
			$row=mysql_fetch_row($rslt);
			if ($row[0] > 0)
				{
				$channel_live=0;
				echo "Channel $channel in use by another agent på $call_server_ip, Hangup kommandot skrevs ej $rowx[0]\n$stmt\n";
				if ($WeBRooTWritablE > 0)
					{
					$fp = fopen ("./vicidial_debug.txt", "a");
					fwrite ($fp, "$NOW_TIME|MDCHU|$user|$channel|$call_server_ip|$exten|\n");
					fclose($fp);
					}
				}
			else
				{
				echo "$stmt\n";
				}
			}

		if ($channel_live==1)
			{
			if ( (strlen($CalLCID)>15) and ($secondS > 0))
				{
				$stmt="SELECT count(*) FROM vicidial_auto_calls where callerid='$CalLCID';";
				$rslt=mysql_query($stmt, $link);
			if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'02013',$user,$server_ip,$session_name,$one_mysql_log);}
				$rowx=mysql_fetch_row($rslt);
				if ($format=='debug') {echo "\n<!-- $rowx[0]|$stmt -->";}
				if ($rowx[0] > 0)
					{
					#############################################
					##### START QUEUEMETRICS LOGGING LOOKUP #####
					$stmt = "SELECT enable_queuemetrics_logging,queuemetrics_server_ip,queuemetrics_dbname,queuemetrics_login,queuemetrics_pass,queuemetrics_log_id FROM system_settings;";
					$rslt=mysql_query($stmt, $link);
			if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'02014',$user,$server_ip,$session_name,$one_mysql_log);}
					if ($format=='debug') {echo "\n<!-- $rowx[0]|$stmt -->";}
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
						$linkB=mysql_connect("$queuemetrics_server_ip", "$queuemetrics_login", "$queuemetrics_pass");
						mysql_select_db("$queuemetrics_dbname", $linkB);

						$stmt="SELECT count(*) from queue_log where call_id='$CalLCID' and verb='CONNECT';";
						$rslt=mysql_query($stmt, $linkB);
			if ($mel > 0) {mysql_error_logging($NOW_TIME,$linkB,$mel,$stmt,'02015',$user,$server_ip,$session_name,$one_mysql_log);}
						$VAC_cn_ct = mysql_num_rows($rslt);
						if ($VAC_cn_ct > 0)
							{
							$row=mysql_fetch_row($rslt);
							$caller_connect	= $row[0];
							}
						if ($format=='debug') {echo "\n<!-- $caller_connect|$stmt -->";}
						if ($caller_connect > 0)
							{
							$CLqueue_position='1';
							### grab call lead information needed for QM logging
							$stmt="SELECT auto_call_id,lead_id,phone_number,status,campaign_id,phone_code,alt_dial,stage,callerid,uniqueid,queue_position from vicidial_auto_calls where callerid='$CalLCID' order by call_time limit 1;";
							$rslt=mysql_query($stmt, $link);
			if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'02016',$user,$server_ip,$session_name,$one_mysql_log);}
							$VAC_qm_ct = mysql_num_rows($rslt);
							if ($VAC_qm_ct > 0)
								{
								$row=mysql_fetch_row($rslt);
								$auto_call_id =			$row[0];
								$CLlead_id =			$row[1];
								$CLphone_number =		$row[2];
								$CLstatus =				$row[3];
								$CLcampaign_id =		$row[4];
								$CLphone_code =			$row[5];
								$CLalt_dial =			$row[6];
								$CLstage =				$row[7];
								$CLcallerid =			$row[8];
								$CLuniqueid =			$row[9];
								$CLqueue_position =		$row[10];
								}
							if ($format=='debug') {echo "\n<!-- $CLcampaign_id|$stmt -->";}

							$CLstage = preg_replace("/.*-/",'',$CLstage);
							if (strlen($CLstage) < 1) {$CLstage=0;}

							$stmt="SELECT count(*) from queue_log where call_id='$CalLCID' and verb='COMPLETECALLER' and queue='$CLcampaign_id';";
							$rslt=mysql_query($stmt, $linkB);
			if ($mel > 0) {mysql_error_logging($NOW_TIME,$linkB,$mel,$stmt,'02017',$user,$server_ip,$session_name,$one_mysql_log);}
							$VAC_cc_ct = mysql_num_rows($rslt);
							if ($VAC_cc_ct > 0)
								{
								$row=mysql_fetch_row($rslt);
								$caller_complete	= $row[0];
								}
							if ($format=='debug') {echo "\n<!-- $caller_complete|$stmt -->";}

							if ($caller_complete < 1)
								{
								$time_id=0;
								$stmt="SELECT time_id from queue_log where call_id='$CalLCID' and verb IN('ENTERQUEUE','CALLOUTBOUND') and queue='$CLcampaign_id';";
								$rslt=mysql_query($stmt, $linkB);
			if ($mel > 0) {mysql_error_logging($NOW_TIME,$linkB,$mel,$stmt,'02018',$user,$server_ip,$session_name,$one_mysql_log);}
								$VAC_eq_ct = mysql_num_rows($rslt);
								if ($VAC_eq_ct > 0)
									{
									$row=mysql_fetch_row($rslt);
									$time_id	= $row[0];
									}
								$StarTtime = date("U");
								if ($time_id > 100000) 
									{$secondS = ($StarTtime - $time_id);}

								if ($format=='debug') {echo "\n<!-- $caller_complete|$stmt -->";}
								$stmt = "INSERT INTO queue_log SET partition='P01',time_id='$StarTtime',call_id='$CalLCID',queue='$CLcampaign_id',agent='Agent/$user',verb='COMPLETEAGENT',data1='$CLstage',data2='$secondS',data3='$CLqueue_position',serverid='$queuemetrics_log_id';";
								$rslt=mysql_query($stmt, $linkB);
			if ($mel > 0) {mysql_error_logging($NOW_TIME,$linkB,$mel,$stmt,'02019',$user,$server_ip,$session_name,$one_mysql_log);}
								$affected_rows = mysql_affected_rows($linkB);
								if ($format=='debug') {echo "\n<!-- $affected_rows|$stmt -->";}
								}
							}
						}
					}
				}

			$stmt="INSERT INTO vicidial_manager values('','','$NOW_TIME','NEW','N','$call_server_ip','','Hangup','$queryCID','Channel: $channel','','','','','','','','','');";
				if ($format=='debug') {echo "\n<!-- $stmt -->";}
			$rslt=mysql_query($stmt, $link);
			if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'02020',$user,$server_ip,$session_name,$one_mysql_log);}
			echo "Hangup kommandot skickat till Kanal $channel på $call_server_ip\n";
			}
		}
	}



######################
# ACTION=Redirect, RedirectName, RedirectNameVmail, RedirectToPark, RedirectFromPark, RedirectVD, RedirectXtra, RedirectXtraCX
# - insert Redirect Manager statement using extensions name
######################
if ($ACTION=="RedirectVD")
	{
	if ( (strlen($channel)<3) or (strlen($queryCID)<15) or (strlen($exten)<1) or (strlen($campaign)<1) or (strlen($ext_context)<1) or (strlen($ext_priority)<1) or (strlen($uniqueid)<2) or (strlen($lead_id)<1) )
		{
		$channel_live=0;
		echo "En av dessa variabler är ej giltig:\n";
		echo "Channel $channel måste vara mer än 2 tecken\n";
		echo "queryCID $queryCID måste vara mer än 14 tecken\n";
		echo "exten $exten måste väljas\n";
		echo "ext_context $ext_context måste väljas\n";
		echo "ext_priority $ext_priority måste väljas\n";
		echo "auto_dial_level $auto_dial_level måste väljas\n";
		echo "campaign $campaign måste väljas\n";
		echo "uniqueid $uniqueid måste väljas\n";
		echo "lead_id $lead_id måste väljas\n";
		echo "\nRedirectVD Action ej skickat\n";
		}
	else
		{
		if (strlen($call_server_ip)>6) {$server_ip = $call_server_ip;}
		$stmt = "select count(*) from vicidial_campaigns where campaign_id='$campaign' and campaign_allow_inbound='Y';";
			if ($format=='debug') {echo "\n<!-- $stmt -->";}
		$rslt=mysql_query($stmt, $link);
			if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'02021',$user,$server_ip,$session_name,$one_mysql_log);}
			$row=mysql_fetch_row($rslt);
		if ($row[0] > 0)
			{
			$four_hours_ago = date("Y-m-d H:i:s", mktime(date("H")-4,date("i"),date("s"),date("m"),date("d"),date("Y")));
			$stmt = "UPDATE vicidial_closer_log set end_epoch='$StarTtime', length_in_sec=(queue_seconds + $secondS),status='XFER' where lead_id='$lead_id' and call_date > \"$four_hours_ago\" order by start_epoch desc limit 1;";
				if ($format=='debug') {echo "\n<!-- $stmt -->";}
			$rslt=mysql_query($stmt, $link);
			if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'02022',$user,$server_ip,$session_name,$one_mysql_log);}
			}

		$stmt = "UPDATE vicidial_log set end_epoch='$StarTtime', length_in_sec='$secondS',status='XFER' where uniqueid='$uniqueid';";
			if ($format=='debug') {echo "\n<!-- $stmt -->";}
		$rslt=mysql_query($stmt, $link);
		if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'02023',$user,$server_ip,$session_name,$one_mysql_log);}

		if ($nodeletevdac < 1)
			{
			$stmt = "DELETE from vicidial_auto_calls where uniqueid='$uniqueid';";
				if ($format=='debug') {echo "\n<!-- $stmt -->";}
			$rslt=mysql_query($stmt, $link);
			if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'02024',$user,$server_ip,$session_name,$one_mysql_log);}
			}
		$ACTION="Redirect";
		}
	}

if ($ACTION=="RedirectToPark")
	{
	if ( (strlen($channel)<3) or (strlen($queryCID)<15) or (strlen($exten)<1) or (strlen($extenName)<1) or (strlen($ext_context)<1) or (strlen($ext_priority)<1) or (strlen($parkedby)<1) )
		{
		$channel_live=0;
		echo "En av dessa variabler är ej giltig:\n";
		echo "Channel $channel måste vara mer än 2 tecken\n";
		echo "queryCID $queryCID måste vara mer än 14 tecken\n";
		echo "exten $exten måste väljas\n";
		echo "extenName $extenName måste väljas\n";
		echo "ext_context $ext_context måste väljas\n";
		echo "ext_priority $ext_priority måste väljas\n";
		echo "parkedby $parkedby måste väljas\n";
		echo "\nRedirectToPark Action ej skickat\n";
		}
	else
		{
		if (strlen($call_server_ip)>6) {$server_ip = $call_server_ip;}
		$stmt = "INSERT INTO parked_channels values('$channel','$server_ip','$CalLCID','$extenName','$parkedby','$NOW_TIME');";
			if ($format=='debug') {echo "\n<!-- $stmt -->";}
		$rslt=mysql_query($stmt, $link);
			if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'02025',$user,$server_ip,$session_name,$one_mysql_log);}
		$ACTION="Redirect";

	#	$fp = fopen ("./vicidial_debug.txt", "a");
	#	fwrite ($fp, "$NOW_TIME|MS_LOG_0|$queryCID|$stmt|\n");
	#	fclose($fp);
		}
	}

if ($ACTION=="RedirectFromPark")
	{
	if ( (strlen($channel)<3) or (strlen($queryCID)<15) or (strlen($exten)<1) or (strlen($ext_context)<1) or (strlen($ext_priority)<1) )
		{
		$channel_live=0;
		echo "En av dessa variabler är ej giltig:\n";
		echo "Channel $channel måste vara mer än 2 tecken\n";
		echo "queryCID $queryCID måste vara mer än 14 tecken\n";
		echo "exten $exten måste väljas\n";
		echo "ext_context $ext_context måste väljas\n";
		echo "ext_priority $ext_priority måste väljas\n";
		echo "\nRedirectFromPark Action ej skickat\n";
		}
	else
		{
		if (strlen($call_server_ip)>6) {$server_ip = $call_server_ip;}
		$stmt = "DELETE FROM parked_channels where server_ip='$server_ip' and channel='$channel';";
			if ($format=='debug') {echo "\n<!-- $stmt -->";}
		$rslt=mysql_query($stmt, $link);
			if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'02026',$user,$server_ip,$session_name,$one_mysql_log);}
		$ACTION="Redirect";
		}
	}

if ($ACTION=="RedirectName")
	{
	if ( (strlen($channel)<3) or (strlen($queryCID)<15)  or (strlen($extenName)<1)  or (strlen($ext_context)<1)  or (strlen($ext_priority)<1) )
		{
		$channel_live=0;
		echo "En av dessa variabler är ej giltig:\n";
		echo "Channel $channel måste vara mer än 2 tecken\n";
		echo "queryCID $queryCID måste vara mer än 14 tecken\n";
		echo "extenName $extenName måste väljas\n";
		echo "ext_context $ext_context måste väljas\n";
		echo "ext_priority $ext_priority måste väljas\n";
		echo "\nRedirectName Action ej skickat\n";
		}
	else
		{
		$stmt="SELECT dialplan_number FROM phones where server_ip = '$server_ip' and extension='$extenName';";
			if ($format=='debug') {echo "\n<!-- $stmt -->";}
		$rslt=mysql_query($stmt, $link);
			if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'02027',$user,$server_ip,$session_name,$one_mysql_log);}
		$name_count = mysql_num_rows($rslt);
		if ($name_count>0)
			{
			$row=mysql_fetch_row($rslt);
			$exten = $row[0];
			$ACTION="Redirect";
			}
		}
	}

if ($ACTION=="RedirectNameVmail")
	{
	if ( (strlen($channel)<3) or (strlen($queryCID)<15)  or (strlen($extenName)<1)  or (strlen($exten)<1)  or (strlen($ext_context)<1)  or (strlen($ext_priority)<1) )
		{
		$channel_live=0;
		echo "En av dessa variabler är ej giltig:\n";
		echo "Channel $channel måste vara mer än 2 tecken\n";
		echo "queryCID $queryCID måste vara mer än 14 tecken\n";
		echo "extenName $extenName måste väljas\n";
		echo "exten $exten måste väljas\n";
		echo "ext_context $ext_context måste väljas\n";
		echo "ext_priority $ext_priority måste väljas\n";
		echo "\nRedirectNameVmail Action ej skickat\n";
		}
	else
		{
		$stmt="SELECT voicemail_id FROM phones where server_ip = '$server_ip' and extension='$extenName';";
			if ($format=='debug') {echo "\n<!-- $stmt -->";}
		$rslt=mysql_query($stmt, $link);
			if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'02028',$user,$server_ip,$session_name,$one_mysql_log);}
		$name_count = mysql_num_rows($rslt);
		if ($name_count>0)
			{
			$row=mysql_fetch_row($rslt);
			$exten = "$exten$row[0]";
			$ACTION="Redirect";
			}
		}
	}






if ($ACTION=="RedirectXtraCXNeW")
	{
	$DBout='';
	$row='';   $rowx='';
	$channel_liveX=1;
	$channel_liveY=1;
	if ( (strlen($channel)<3) or (strlen($queryCID)<15) or (strlen($ext_context)<1) or (strlen($ext_priority)<1) or (strlen($session_id)<3) or ( ( (strlen($extrachannel)<3) or (strlen($exten)<1) ) and (!ereg("NEXTAVAILABLE",$exten)) ) )
		{
		$channel_liveX=0;
		$channel_liveY=0;
		echo "En av dessa variabler är ej giltig:\n";
		echo "Channel $channel måste vara mer än 2 tecken\n";
		echo "ExtraChannel $extrachannel måste vara mer än 2 tecken\n";
		echo "queryCID $queryCID måste vara mer än 14 tecken\n";
		echo "exten $exten måste väljas\n";
		echo "ext_context $ext_context måste väljas\n";
		echo "ext_priority $ext_priority måste väljas\n";
		echo "\nRedirect Action ej skickat\n";
		if (ereg("SECOND|FIRST|DEBUG",$filename))
			{
			if ($WeBRooTWritablE > 0)
				{
				$fp = fopen ("./vicidial_debug.txt", "a");
				fwrite ($fp, "$NOW_TIME|RDCXC|$filename|$user|$campaign|$channel|$extrachannel|$queryCID|$exten|$ext_context|ext_priority|\n");
				fclose($fp);
				}
			}
		}
	else
		{
		if (ereg("NEXTAVAILABLE",$exten))
			{
			$stmtA="SELECT count(*) FROM vicidial_conferences where server_ip='$server_ip' and ((extension='') or (extension is null)) and conf_exten != '$session_id';";
				if ($format=='debug') {echo "\n<!-- $stmtA -->";}
			$rslt=mysql_query($stmtA, $link);
			if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmtA,'02029',$user,$server_ip,$session_name,$one_mysql_log);}
			$row=mysql_fetch_row($rslt);
			if ($row[0] > 1)
				{
				$stmtB="UPDATE vicidial_conferences set extension='$protocol/$extension$NOWnum', leave_3way='0' where server_ip='$server_ip' and ((extension='') or (extension is null)) and conf_exten != '$session_id' limit 1;";
					if ($format=='debug') {echo "\n<!-- $stmtB -->";}
				$rslt=mysql_query($stmtB, $link);
			if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmtB,'02030',$user,$server_ip,$session_name,$one_mysql_log);}

				$stmtC="SELECT conf_exten from vicidial_conferences where server_ip='$server_ip' and extension='$protocol/$extension$NOWnum' and conf_exten != '$session_id';";
					if ($format=='debug') {echo "\n<!-- $stmtC -->";}
				$rslt=mysql_query($stmtC, $link);
			if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmtC,'02031',$user,$server_ip,$session_name,$one_mysql_log);}
				$row=mysql_fetch_row($rslt);
				$exten = $row[0];

				if ( (ereg("^8300",$extension)) and ($protocol == 'Local') )
					{
					$extension = "$extension$user";
					}

				$stmtD="UPDATE vicidial_conferences set extension='$protocol/$extension' where server_ip='$server_ip' and conf_exten='$exten' limit 1;";
					if ($format=='debug') {echo "\n<!-- $stmtD -->";}
				$rslt=mysql_query($stmtD, $link);
			if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmtD,'02032',$user,$server_ip,$session_name,$one_mysql_log);}

				$stmtE="UPDATE vicidial_conferences set leave_3way='1', leave_3way_datetime='$NOW_TIME', extension='3WAY_$user' where server_ip='$server_ip' and conf_exten='$session_id';";
					if ($format=='debug') {echo "\n<!-- $stmtE -->";}
				$rslt=mysql_query($stmtE, $link);
			if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmtE,'02033',$user,$server_ip,$session_name,$one_mysql_log);}

				$queryCID = "CXAR24$NOWnum";
				$stmtF="INSERT INTO vicidial_manager values('','','$NOW_TIME','NEW','N','$server_ip','','Redirect','$queryCID','Channel: $agentchannel','Context: $ext_context','Exten: $exten','Priority: 1','CallerID: $queryCID','','','','','');";
					if ($format=='debug') {echo "\n<!-- $stmtF -->";}
				$rslt=mysql_query($stmtF, $link);
			if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmtF,'02034',$user,$server_ip,$session_name,$one_mysql_log);}

				$stmtG="UPDATE vicidial_live_agents set conf_exten='$exten' where server_ip='$server_ip' and user='$user';";
					if ($format=='debug') {echo "\n<!-- $stmtG -->";}
				$rslt=mysql_query($stmtG, $link);
			if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmtG,'02035',$user,$server_ip,$session_name,$one_mysql_log);}

				if ($auto_dial_level < 1)
					{
					$stmtH = "DELETE from vicidial_auto_calls where lead_id='$lead_id' and callerid LIKE \"M%\";";
						if ($format=='debug') {echo "\n<!-- $stmtH -->";}
					$rslt=mysql_query($stmtH, $link);
			if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmtH,'02036',$user,$server_ip,$session_name,$one_mysql_log);}
					}

			//	$fp = fopen ("./vicidial_debug_3way.txt", "a");
			//	fwrite ($fp, "$NOW_TIME|$filename|\n|$stmtA|\n|$stmtB|\n|$stmtC|\n|$stmtD|\n|$stmtE|\n|$stmtF|\n|$stmtG|\n|$stmtH|\n\n");
			//	fclose($fp);

				echo "NeWSessioN|$exten|\n";
				echo "|$stmtG|\n";
				
				exit;
				}
			else
				{
				$channel_liveX=0;
				echo "Cannot find empty vicidial_conference på $server_ip, Redirect kommandot skrevs ej\n|$stmt|";
				if (ereg("SECOND|FIRST|DEBUG",$filename)) {$DBout .= "Hittar ingen tom konferens på $server_ip";}
				}
			}

		if (strlen($call_server_ip)<7) {$call_server_ip = $server_ip;}

		$stmt="SELECT count(*) FROM live_channels where server_ip = '$call_server_ip' and channel='$channel';";
			if ($format=='debug') {echo "\n<!-- $stmt -->";}
		$rslt=mysql_query($stmt, $link);
			if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'02037',$user,$server_ip,$session_name,$one_mysql_log);}
		$row=mysql_fetch_row($rslt);
		if ($row[0]==0)
			{
			$stmt="SELECT count(*) FROM live_sip_channels where server_ip = '$call_server_ip' and channel='$channel';";
				if ($format=='debug') {echo "\n<!-- $stmt -->";}
			$rslt=mysql_query($stmt, $link);
			if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'02038',$user,$server_ip,$session_name,$one_mysql_log);}
			$rowx=mysql_fetch_row($rslt);
			if ($rowx[0]==0)
				{
				$channel_liveX=0;
				echo "Channel $channel är inte aktivt på $call_server_ip, Redirect kommandot skrevs ej\n";
				if (ereg("SECOND|FIRST|DEBUG",$filename)) {$DBout .= "$channel är inte aktivt på $call_server_ip";}
				}	
			}
		$stmt="SELECT count(*) FROM live_channels where server_ip = '$server_ip' and channel='$extrachannel';";
			if ($format=='debug') {echo "\n<!-- $stmt -->";}
		$rslt=mysql_query($stmt, $link);
			if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'02039',$user,$server_ip,$session_name,$one_mysql_log);}
		$row=mysql_fetch_row($rslt);
		if ($row[0]==0)
			{
			$stmt="SELECT count(*) FROM live_sip_channels where server_ip = '$server_ip' and channel='$extrachannel';";
				if ($format=='debug') {echo "\n<!-- $stmt -->";}
			$rslt=mysql_query($stmt, $link);
			if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'02040',$user,$server_ip,$session_name,$one_mysql_log);}
			$rowx=mysql_fetch_row($rslt);
			if ($rowx[0]==0)
				{
				$channel_liveY=0;
				echo "Channel $channel är inte aktivt på $server_ip, Redirect kommandot skrevs ej\n";
				if (ereg("SECOND|FIRST|DEBUG",$filename)) {$DBout .= "$channel är inte aktivt på $server_ip";}
				}	
			}
		if ( ($channel_liveX==1) && ($channel_liveY==1) )
			{
			$stmt="SELECT count(*) FROM vicidial_live_agents where lead_id='$lead_id' and user!='$user';";
				if ($format=='debug') {echo "\n<!-- $stmt -->";}
			$rslt=mysql_query($stmt, $link);
			if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'02041',$user,$server_ip,$session_name,$one_mysql_log);}
			$rowx=mysql_fetch_row($rslt);
			if ($rowx[0] < 1)
				{
				$channel_liveY=0;
				echo "No Local agent to send call to, Redirect kommandot skrevs ej\n";
				if (ereg("SECOND|FIRST|DEBUG",$filename)) {$DBout .= "No Local agent to send call to";}
				}	
			else
				{
				$stmt="SELECT server_ip,conf_exten,user FROM vicidial_live_agents where lead_id='$lead_id' and user!='$user';";
					if ($format=='debug') {echo "\n<!-- $stmt -->";}
				$rslt=mysql_query($stmt, $link);
			if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'02042',$user,$server_ip,$session_name,$one_mysql_log);}
				$rowx=mysql_fetch_row($rslt);
				$dest_server_ip = $rowx[0];
				$dest_session_id = $rowx[1];
				$dest_user = $rowx[2];
				$S='*';

				$D_s_ip = explode('.', $dest_server_ip);
				if (strlen($D_s_ip[0])<2) {$D_s_ip[0] = "0$D_s_ip[0]";}
				if (strlen($D_s_ip[0])<3) {$D_s_ip[0] = "0$D_s_ip[0]";}
				if (strlen($D_s_ip[1])<2) {$D_s_ip[1] = "0$D_s_ip[1]";}
				if (strlen($D_s_ip[1])<3) {$D_s_ip[1] = "0$D_s_ip[1]";}
				if (strlen($D_s_ip[2])<2) {$D_s_ip[2] = "0$D_s_ip[2]";}
				if (strlen($D_s_ip[2])<3) {$D_s_ip[2] = "0$D_s_ip[2]";}
				if (strlen($D_s_ip[3])<2) {$D_s_ip[3] = "0$D_s_ip[3]";}
				if (strlen($D_s_ip[3])<3) {$D_s_ip[3] = "0$D_s_ip[3]";}
				$dest_dialstring = "$D_s_ip[0]$S$D_s_ip[1]$S$D_s_ip[2]$S$D_s_ip[3]$S$dest_session_id$S$lead_id$S$dest_user$S$phone_code$S$phone_number$S$campaign$S";

				$stmt="INSERT INTO vicidial_manager values('','','$NOW_TIME','NEW','N','$call_server_ip','','Redirect','$queryCID','Channel: $channel','Context: $ext_context','Exten: $dest_dialstring','Priority: $ext_priority','CallerID: $queryCID','','','','','');";
					if ($format=='debug') {echo "\n<!-- $stmt -->";}
				$rslt=mysql_query($stmt, $link);
			if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'02043',$user,$server_ip,$session_name,$one_mysql_log);}

				$stmt="INSERT INTO vicidial_manager values('','','$NOW_TIME','NEW','N','$server_ip','','Hangup','$queryCID','Channel: $extrachannel','','','','','','','','','');";
					if ($format=='debug') {echo "\n<!-- $stmt -->";}
				$rslt=mysql_query($stmt, $link);
			if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'02044',$user,$server_ip,$session_name,$one_mysql_log);}

				echo "RedirectXtraCX kommandot skickat till Kanal $channel på $call_server_ip and \nHungup $extrachannel på $server_ip\n";
				if (ereg("SECOND|FIRST|DEBUG",$filename)) {$DBout .= "$channel på $call_server_ip, Hungup $extrachannel på $server_ip";}
				}
			}
		else
			{
			if ($channel_liveX==1)
			{$ACTION="Redirect";   $server_ip = $call_server_ip;}
			if ($channel_liveY==1)
			{$ACTION="Redirect";   $channel=$extrachannel;}
			if (ereg("SECOND|FIRST|DEBUG",$filename)) {$DBout .= "Changed to Redirect: $channel på $server_ip";}
			}

		if (ereg("SECOND|FIRST|DEBUG",$filename))
			{
			if ($WeBRooTWritablE > 0)
				{
				$fp = fopen ("./vicidial_debug.txt", "a");
				fwrite ($fp, "$NOW_TIME|RDCXC|$filename|$user|$campaign|$DBout|\n");
				fclose($fp);
				}
			}
		}
	}










if ($ACTION=="RedirectXtraNeW")
	{
	if ($channel=="$extrachannel")
	{$ACTION="Redirect";}
	else
		{
		$row='';   $rowx='';
		$channel_liveX=1;
		$channel_liveY=1;
		if ( (strlen($channel)<3) or (strlen($queryCID)<15) or (strlen($ext_context)<1) or (strlen($ext_priority)<1) or (strlen($session_id)<3) or ( ( (strlen($extrachannel)<3) or (strlen($exten)<1) ) and (!ereg("NEXTAVAILABLE",$exten)) ) )
			{
			$channel_liveX=0;
			$channel_liveY=0;
			echo "En av dessa variabler är ej giltig:\n";
			echo "Channel $channel måste vara mer än 2 tecken\n";
			echo "ExtraChannel $extrachannel måste vara mer än 2 tecken\n";
			echo "queryCID $queryCID måste vara mer än 14 tecken\n";
			echo "exten $exten måste väljas\n";
			echo "ext_context $ext_context måste väljas\n";
			echo "ext_priority $ext_priority måste väljas\n";
			echo "session_id $session_id måste väljas\n";
			echo "\nRedirect Action ej skickat\n";
			if (ereg("SECOND|FIRST|DEBUG",$filename))
				{
				if ($WeBRooTWritablE > 0)
					{
					$fp = fopen ("./vicidial_debug.txt", "a");
					fwrite ($fp, "$NOW_TIME|RDX|$filename|$user|$campaign|$$channel|$extrachannel|$queryCID|$exten|$ext_context|ext_priority|$session_id|\n");
					fclose($fp);
					}
				}
			}
		else
			{
			if (ereg("NEXTAVAILABLE",$exten))
				{
				$stmt="SELECT count(*) FROM vicidial_conferences where server_ip='$server_ip' and ((extension='') or (extension is null)) and conf_exten != '$session_id';";
					if ($format=='debug') {echo "\n<!-- $stmt -->";}
				$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'02045',$user,$server_ip,$session_name,$one_mysql_log);}
				$row=mysql_fetch_row($rslt);
				if ($row[0] > 1)
					{
					$stmt="UPDATE vicidial_conferences set extension='$protocol/$extension$NOWnum', leave_3way='0' where server_ip='$server_ip' and ((extension='') or (extension is null)) and conf_exten != '$session_id' limit 1;";
						if ($format=='debug') {echo "\n<!-- $stmt -->";}
					$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'02046',$user,$server_ip,$session_name,$one_mysql_log);}

					$stmt="SELECT conf_exten from vicidial_conferences where server_ip='$server_ip' and extension='$protocol/$extension$NOWnum' and conf_exten != '$session_id';";
						if ($format=='debug') {echo "\n<!-- $stmt -->";}
					$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'02047',$user,$server_ip,$session_name,$one_mysql_log);}
					$row=mysql_fetch_row($rslt);
					$exten = $row[0];

					$stmt="UPDATE vicidial_conferences set extension='$protocol/$extension' where server_ip='$server_ip' and conf_exten='$exten' limit 1;";
						if ($format=='debug') {echo "\n<!-- $stmt -->";}
					$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'02048',$user,$server_ip,$session_name,$one_mysql_log);}

					$stmt="UPDATE vicidial_conferences set leave_3way='1', leave_3way_datetime='$NOW_TIME', extension='3WAY_$user' where server_ip='$server_ip' and conf_exten='$session_id';";
						if ($format=='debug') {echo "\n<!-- $stmt -->";}
					$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'02049',$user,$server_ip,$session_name,$one_mysql_log);}

					$queryCID = "CXAR23$NOWnum";
					$stmtB="INSERT INTO vicidial_manager values('','','$NOW_TIME','NEW','N','$server_ip','','Redirect','$queryCID','Channel: $agentchannel','Context: $ext_context','Exten: $exten','Priority: 1','CallerID: $queryCID','','','','','');";
						if ($format=='debug') {echo "\n<!-- $stmt -->";}
					$rslt=mysql_query($stmtB, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'02050',$user,$server_ip,$session_name,$one_mysql_log);}

					$stmt="UPDATE vicidial_live_agents set conf_exten='$exten' where server_ip='$server_ip' and user='$user';";
						if ($format=='debug') {echo "\n<!-- $stmt -->";}
					$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'02051',$user,$server_ip,$session_name,$one_mysql_log);}

					if ($auto_dial_level < 1)
						{
						$stmt = "DELETE from vicidial_auto_calls where lead_id='$lead_id' and callerid LIKE \"M%\";";
							if ($format=='debug') {echo "\n<!-- $stmt -->";}
						$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'02052',$user,$server_ip,$session_name,$one_mysql_log);}
						}

					echo "NeWSessioN|$exten|\n";
					echo "|$stmtB|\n";
					
					exit;
					}
				else
					{
					$channel_liveX=0;
					echo "Cannot find empty vicidial_conference på $server_ip, Redirect kommandot skrevs ej\n|$stmt|";
					if (ereg("SECOND|FIRST|DEBUG",$filename)) {$DBout .= "Hittar ingen tom konferens på $server_ip";}
					}
				}

			if (strlen($call_server_ip)<7) {$call_server_ip = $server_ip;}

			$stmt="SELECT count(*) FROM live_channels where server_ip = '$call_server_ip' and channel='$channel';";
				if ($format=='debug') {echo "\n<!-- $stmt -->";}
			$rslt=mysql_query($stmt, $link);
			if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'02053',$user,$server_ip,$session_name,$one_mysql_log);}
			$row=mysql_fetch_row($rslt);
			if ( ($row[0]==0) && (!ereg("SECOND",$filename)) )
				{
				$stmt="SELECT count(*) FROM live_sip_channels where server_ip = '$call_server_ip' and channel='$channel';";
					if ($format=='debug') {echo "\n<!-- $stmt -->";}
				$rslt=mysql_query($stmt, $link);
			if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'02054',$user,$server_ip,$session_name,$one_mysql_log);}
				$rowx=mysql_fetch_row($rslt);
				if ($rowx[0]==0)
					{
					$channel_liveX=0;
					echo "Channel $channel är inte aktivt på $call_server_ip, Redirect kommandot skrevs ej\n";
					if (ereg("SECOND|FIRST|DEBUG",$filename)) {$DBout .= "$channel är inte aktivt på $call_server_ip";}
					}	
				}
			$stmt="SELECT count(*) FROM live_channels where server_ip = '$server_ip' and channel='$extrachannel';";
				if ($format=='debug') {echo "\n<!-- $stmt -->";}
			$rslt=mysql_query($stmt, $link);
			if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'02055',$user,$server_ip,$session_name,$one_mysql_log);}
			$row=mysql_fetch_row($rslt);
			if ( ($row[0]==0) && (!ereg("SECOND",$filename)) )
				{
				$stmt="SELECT count(*) FROM live_sip_channels where server_ip = '$server_ip' and channel='$extrachannel';";
					if ($format=='debug') {echo "\n<!-- $stmt -->";}
				$rslt=mysql_query($stmt, $link);
			if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'02056',$user,$server_ip,$session_name,$one_mysql_log);}
				$rowx=mysql_fetch_row($rslt);
				if ($rowx[0]==0)
					{
					$channel_liveY=0;
					echo "Channel $channel är inte aktivt på $server_ip, Redirect kommandot skrevs ej\n";
					if (ereg("SECOND|FIRST|DEBUG",$filename)) {$DBout .= "$channel är inte aktivt på $server_ip";}
					}	
				}
			if ( ($channel_liveX==1) && ($channel_liveY==1) )
				{
				if ( ($server_ip=="$call_server_ip") or (strlen($call_server_ip)<7) )
					{
					$stmt="INSERT INTO vicidial_manager values('','','$NOW_TIME','NEW','N','$server_ip','','Redirect','$queryCID','Channel: $channel','ExtraChannel: $extrachannel','Context: $ext_context','Exten: $exten','Priority: $ext_priority','CallerID: $queryCID','','','','');";
						if ($format=='debug') {echo "\n<!-- $stmt -->";}
					$rslt=mysql_query($stmt, $link);
			if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'02057',$user,$server_ip,$session_name,$one_mysql_log);}

					echo "RedirectXtra kommandot skickat till Kanal $channel and \nExtraChannel $extrachannel\n to $exten på $server_ip\n";
					if (ereg("SECOND|FIRST|DEBUG",$filename)) {$DBout .= "$channel and $extrachannel to $exten på $server_ip";}
					}
				else
					{
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
					$dest_dialstring = "$D_s_ip[0]$S$D_s_ip[1]$S$D_s_ip[2]$S$D_s_ip[3]$S$exten";

					$stmt="INSERT INTO vicidial_manager values('','','$NOW_TIME','NEW','N','$call_server_ip','','Redirect','$queryCID','Channel: $channel','Context: $ext_context','Exten: $dest_dialstring','Priority: $ext_priority','CallerID: $queryCID','','','','','');";
						if ($format=='debug') {echo "\n<!-- $stmt -->";}
					$rslt=mysql_query($stmt, $link);
			if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'02058',$user,$server_ip,$session_name,$one_mysql_log);}

					$stmt="INSERT INTO vicidial_manager values('','','$NOW_TIME','NEW','N','$server_ip','','Redirect','$queryCID','Channel: $extrachannel','Context: $ext_context','Exten: $exten','Priority: $ext_priority','CallerID: $queryCID','','','','','');";
						if ($format=='debug') {echo "\n<!-- $stmt -->";}
					$rslt=mysql_query($stmt, $link);
			if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'02059',$user,$server_ip,$session_name,$one_mysql_log);}

					echo "RedirectXtra kommandot skickat till Kanal $channel på $call_server_ip and \nExtraChannel $extrachannel\n to $exten på $server_ip\n";
					if (ereg("SECOND|FIRST|DEBUG",$filename)) {$DBout .= "$channel/$call_server_ip and $extrachannel/$server_ip to $exten";}
					}
				}
			else
				{
				if ($channel_liveX==1)
				{$ACTION="Redirect";   $server_ip = $call_server_ip;}
				if ($channel_liveY==1)
				{$ACTION="Redirect";   $channel=$extrachannel;}
				}

			if (ereg("SECOND|FIRST|DEBUG",$filename))
				{
				if ($WeBRooTWritablE > 0)
					{
					$fp = fopen ("./vicidial_debug.txt", "a");
					fwrite ($fp, "$NOW_TIME|RDX|$filename|$user|$campaign|$DBout|\n");
					fclose($fp);
					}
				}
			}
		}
	}





if ($ACTION=="Redirect")
	{
	### for manual dial VICIDIAL calls send the second attempt to transfer the call
	if ($stage=="2NDXfeR")
		{
		$local_DEF = 'Local/';
		$local_AMP = '@';
		$hangup_channel_prefix = "$local_DEF$session_id$local_AMP$ext_context";

		$stmt="SELECT count(*) FROM live_sip_channels where server_ip = '$server_ip' and channel LIKE \"$hangup_channel_prefix%\";";
			if ($format=='debug') {echo "\n<!-- $stmt -->";}
		$rslt=mysql_query($stmt, $link);
			if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'02060',$user,$server_ip,$session_name,$one_mysql_log);}
		$row=mysql_fetch_row($rslt);
		if ($row > 0)
			{
			$stmt="SELECT channel FROM live_sip_channels where server_ip = '$server_ip' and channel LIKE \"$hangup_channel_prefix%\";";
				if ($format=='debug') {echo "\n<!-- $stmt -->";}
			$rslt=mysql_query($stmt, $link);
			if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'02061',$user,$server_ip,$session_name,$one_mysql_log);}
			$rowx=mysql_fetch_row($rslt);
			$channel=$rowx[0];
			$channel = eregi_replace("1$","2",$channel);
			$queryCID = eregi_replace("^.","Q",$queryCID);
			}
		}

	$row='';   $rowx='';
	$channel_live=1;
	if ( (strlen($channel)<3) or (strlen($queryCID)<15)  or (strlen($exten)<1)  or (strlen($ext_context)<1)  or (strlen($ext_priority)<1) )
		{
		$channel_live=0;
		echo "En av dessa variabler är ej giltig:\n";
		echo "Channel $channel måste vara mer än 2 tecken\n";
		echo "queryCID $queryCID måste vara mer än 14 tecken\n";
		echo "exten $exten måste väljas\n";
		echo "ext_context $ext_context måste väljas\n";
		echo "ext_priority $ext_priority måste väljas\n";
		echo "\nRedirect Action ej skickat\n";
		}
	else
		{
		if (strlen($call_server_ip)>6) {$server_ip = $call_server_ip;}
		$stmt="SELECT count(*) FROM live_channels where server_ip = '$server_ip' and channel='$channel';";
			if ($format=='debug') {echo "\n<!-- $stmt -->";}
		$rslt=mysql_query($stmt, $link);
			if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'02062',$user,$server_ip,$session_name,$one_mysql_log);}
		$row=mysql_fetch_row($rslt);
		if ($row[0]==0)
			{
			$stmt="SELECT count(*) FROM live_sip_channels where server_ip = '$server_ip' and channel='$channel';";
				if ($format=='debug') {echo "\n<!-- $stmt -->";}
			$rslt=mysql_query($stmt, $link);
			if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'02063',$user,$server_ip,$session_name,$one_mysql_log);}
			$rowx=mysql_fetch_row($rslt);
			if ($rowx[0]==0)
				{
				$channel_live=0;
				echo "Channel $channel är inte aktivt på $server_ip, Redirect kommandot skrevs ej\n";
				}	
			}
		if ($channel_live==1)
			{
			$stmt="INSERT INTO vicidial_manager values('','','$NOW_TIME','NEW','N','$server_ip','','Redirect','$queryCID','Channel: $channel','Context: $ext_context','Exten: $exten','Priority: $ext_priority','CallerID: $queryCID','','','','','');";
				if ($format=='debug') {echo "\n<!-- $stmt -->";}
			$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'02064',$user,$server_ip,$session_name,$one_mysql_log);}

			echo "Redirect kommandot skickat till Kanal $channel på $server_ip\n";
			}
		}
	}



######################
# ACTION=Monitor or Stop Monitor  - insert Monitor/StopMonitor Manager statement to start recording on a channel
######################
if ( ($ACTION=="Monitor") || ($ACTION=="StopMonitor") )
	{
	if ($ACTION=="StopMonitor")
		{$SQLfile = "";}
	else
		{$SQLfile = "File: $filename";}

	$row='';   $rowx='';
	$channel_live=1;
	if ( (strlen($channel)<3) or (strlen($queryCID)<15) or (strlen($filename)<8) )
		{
		$channel_live=0;
		echo "Channel $channel är ej giltig or queryCID $queryCID är ej giltig or filename: $filename är ej giltig, $ACTION kommandot skrevs ej\n";
		}
	else
		{
		$stmt="SELECT count(*) FROM live_channels where server_ip = '$server_ip' and channel='$channel';";
			if ($format=='debug') {echo "\n<!-- $stmt -->";}
		$rslt=mysql_query($stmt, $link);
			if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'02065',$user,$server_ip,$session_name,$one_mysql_log);}
		$row=mysql_fetch_row($rslt);
		if ($row[0]==0)
			{
			$stmt="SELECT count(*) FROM live_sip_channels where server_ip = '$server_ip' and channel='$channel';";
				if ($format=='debug') {echo "\n<!-- $stmt -->";}
			$rslt=mysql_query($stmt, $link);
			if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'02066',$user,$server_ip,$session_name,$one_mysql_log);}
			$rowx=mysql_fetch_row($rslt);
			if ($rowx[0]==0)
				{
				$channel_live=0;
				echo "Channel $channel är inte aktivt på $server_ip, $ACTION kommandot skrevs ej\n";
				}	
			}
		if ($channel_live==1)
			{
			$stmt="INSERT INTO vicidial_manager values('','','$NOW_TIME','NEW','N','$server_ip','','$ACTION','$queryCID','Channel: $channel','$SQLfile','','','','','','','','');";
				if ($format=='debug') {echo "\n<!-- $stmt -->";}
			$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'02067',$user,$server_ip,$session_name,$one_mysql_log);}

			if ($ACTION=="Monitor")
				{
				$stmt = "INSERT INTO recording_log (channel,server_ip,extension,start_time,start_epoch,filename,lead_id,user) values('$channel','$server_ip','$exten','$NOW_TIME','$StarTtime','$filename','$lead_id','$user')";
					if ($format=='debug') {echo "\n<!-- $stmt -->";}
				$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'02068',$user,$server_ip,$session_name,$one_mysql_log);}

				$stmt="SELECT recording_id FROM recording_log where filename='$filename'";
				$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'02069',$user,$server_ip,$session_name,$one_mysql_log);}
				if ($DB) {echo "$stmt\n";}
				$row=mysql_fetch_row($rslt);
				$recording_id = $row[0];
				}
			else
				{
				$stmt="SELECT recording_id,start_epoch FROM recording_log where filename='$filename'";
				$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'02070',$user,$server_ip,$session_name,$one_mysql_log);}
				if ($DB) {echo "$stmt\n";}
				$rec_count = mysql_num_rows($rslt);
					if ($rec_count>0)
					{
					$row=mysql_fetch_row($rslt);
					$recording_id = $row[0];
					$start_time = $row[1];
					$length_in_sec = ($StarTtime - $start_time);
					$length_in_min = ($length_in_sec / 60);
					$length_in_min = sprintf("%8.2f", $length_in_min);

					$stmt = "UPDATE recording_log set end_time='$NOW_TIME',end_epoch='$StarTtime',length_in_sec=$length_in_sec,length_in_min='$length_in_min' where filename='$filename'";
						if ($DB) {echo "$stmt\n";}
					$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'02071',$user,$server_ip,$session_name,$one_mysql_log);}
					}
				}
			echo "$ACTION kommandot skickat till Kanal $channel på $server_ip\nFilename: $filename\nRecorDing_ID: $recording_id\n";
			}
		}
	}






######################
# ACTION=MonitorConf or StopMonitorConf  - insert Monitor/StopMonitor Manager statement to start recording on a conference
######################
if ( ($ACTION=="MonitorConf") || ($ACTION=="StopMonitorConf") )
	{
	$row='';   $rowx='';
	$channel_live=1;
	$uniqueidSQL='';

	if ( (strlen($exten)<3) or (strlen($channel)<4) or (strlen($filename)<8) )
		{
		$channel_live=0;
		echo "Channel $channel är ej giltig or exten $exten är ej giltig or filename: $filename är ej giltig, $ACTION kommandot skrevs ej\n";
		}
	else
		{
		$VDvicidial_id='';

		if ($ACTION=="MonitorConf")
			{
			$stmt="INSERT INTO vicidial_manager values('','','$NOW_TIME','NEW','N','$server_ip','','Originate','$filename','Channel: $channel','Context: $ext_context','Exten: $exten','Priority: $ext_priority','Callerid: $filename','','','','','');";
				if ($format=='debug') {echo "\n<!-- $stmt -->";}
			$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'02072',$user,$server_ip,$session_name,$one_mysql_log);}

			$stmt = "INSERT INTO recording_log (channel,server_ip,extension,start_time,start_epoch,filename,lead_id,user) values('$channel','$server_ip','$exten','$NOW_TIME','$StarTtime','$filename','$lead_id','$user')";
				if ($format=='debug') {echo "\n<!-- $stmt -->";}
			$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'02073',$user,$server_ip,$session_name,$one_mysql_log);}
			$RLaffected_rows = mysql_affected_rows($link);
			if ($RLaffected_rows > 0)
				{
				$recording_id = mysql_insert_id($link);
				}

			if ($FROMvdc=='YES')
				{
				##### get call type from vicidial_live_agents table
				$VLA_inOUT='NONE';
				$stmt="SELECT comments FROM vicidial_live_agents where user='$user' order by last_update_time desc limit 1;";
				$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'02074',$user,$server_ip,$session_name,$one_mysql_log);}
				if ($DB) {echo "$stmt\n";}
				$VLA_inOUT_ct = mysql_num_rows($rslt);
				if ($VLA_inOUT_ct > 0)
					{
					$row=mysql_fetch_row($rslt);
					$VLA_inOUT =		$row[0];
					}
				if ($VLA_inOUT == 'INBOUND')
					{
					$four_hours_ago = date("Y-m-d H:i:s", mktime(date("H")-4,date("i"),date("s"),date("m"),date("d"),date("Y")));

					##### look for the vicidial ID in the vicidial_closer_log table
					$stmt="SELECT closecallid FROM vicidial_closer_log where lead_id='$lead_id' and user='$user' and call_date > \"$four_hours_ago\" order by closecallid desc limit 1;";
					}
				else
					{
					##### look for the vicidial ID in the vicidial_log table
					$stmt="SELECT uniqueid FROM vicidial_log where uniqueid='$uniqueid' and lead_id='$lead_id';";
					}
				$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'02075',$user,$server_ip,$session_name,$one_mysql_log);}
				if ($DB) {echo "$stmt\n";}
				$VM_mancall_ct = mysql_num_rows($rslt);
				if ($VM_mancall_ct > 0)
					{
					$row=mysql_fetch_row($rslt);
					$VDvicidial_id =	$row[0];

					$stmt = "UPDATE recording_log SET vicidial_id='$VDvicidial_id' where recording_id='$recording_id';";
						if ($format=='debug') {echo "\n<!-- $stmt -->";}
					$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'02076',$user,$server_ip,$session_name,$one_mysql_log);}
					}
				}
			}

		##### StopMonitorConf steps #####
		else
			{
			if ($uniqueid=='IN')
				{
				$four_hours_ago = date("Y-m-d H:i:s", mktime(date("H")-4,date("i"),date("s"),date("m"),date("d"),date("Y")));

				### find the value to put in the vicidial_id field if this was an inbound call
				$stmt="SELECT closecallid from vicidial_closer_log where lead_id='$lead_id' and call_date > \"$four_hours_ago\" order by call_date desc limit 1;";
				$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'02077',$user,$server_ip,$session_name,$one_mysql_log);}
				$VAC_qm_ct = mysql_num_rows($rslt);
				if ($VAC_qm_ct > 0)
					{
					$row=mysql_fetch_row($rslt);
					$uniqueidSQL	= ",vicidial_id='$row[0]'";
					}
				}
			else
				{
				if (strlen($uniqueid) > 8)
					{$uniqueidSQL	= ",vicidial_id='$uniqueid'";}
				}
			
			$stmt="SELECT recording_id,start_epoch FROM recording_log where filename='$filename'";
			$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'02078',$user,$server_ip,$session_name,$one_mysql_log);}
			if ($DB) {echo "$stmt\n";}
			$rec_count = mysql_num_rows($rslt);
			if ($rec_count>0)
				{
				$row=mysql_fetch_row($rslt);
				$recording_id = $row[0];
				$start_time = $row[1];
				$length_in_sec = ($StarTtime - $start_time);
				$length_in_min = ($length_in_sec / 60);
				$length_in_min = sprintf("%8.2f", $length_in_min);

				$stmt = "UPDATE recording_log set end_time='$NOW_TIME',end_epoch='$StarTtime',length_in_sec=$length_in_sec,length_in_min='$length_in_min' $uniqueidSQL where filename='$filename'";
					if ($DB) {echo "$stmt\n";}
				$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'02079',$user,$server_ip,$session_name,$one_mysql_log);}
				}

			# find and hang up all recordings going på in this conference # and extension = '$exten' 
			$stmt="SELECT channel FROM live_sip_channels where server_ip = '$server_ip' and channel LIKE \"$channel%\" and channel LIKE \"%,1\";";
				if ($format=='debug') {echo "\n<!-- $stmt -->";}
			$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'02080',$user,$server_ip,$session_name,$one_mysql_log);}
		#	$rec_count = intval(mysql_num_rows($rslt) / 2);
			$rec_count = mysql_num_rows($rslt);
			$h=0;
				while ($rec_count>$h)
				{
				$rowx=mysql_fetch_row($rslt);
				$HUchannel[$h] = $rowx[0];
				$h++;
				}
			$i=0;
				while ($h>$i)
				{
				$stmt="INSERT INTO vicidial_manager values('','','$NOW_TIME','NEW','N','$server_ip','','Hangup','RH12345$StarTtime$i','Channel: $HUchannel[$i]','','','','','','','','','');";
					if ($format=='debug') {echo "\n<!-- $stmt -->";}
				$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'02081',$user,$server_ip,$session_name,$one_mysql_log);}
				$i++;
				}
			}
			echo "$ACTION kommandot skickat till Kanal $channel på $server_ip\nFilename: $filename\nRecorDing_ID: $recording_id\n INSPELNINGEN KAN MAX VAAR 60 MINUTER\n";
		}
	}





######################
# ACTION=VolumeControl  - raise or lower the volume of a meetme participant
######################
if ($ACTION=="VolumeControl")
	{
	if ( (strlen($exten)<1) or (strlen($channel)<1) or (strlen($stage)<1) or (strlen($queryCID)<1) )
		{
		echo "Konferens $exten, Stage $stage är ej giltig or queryCID $queryCID är ej giltig, Originate kommandot skrevs ej\n";
		}
	else
		{
		$participant_number='XXYYXXYYXXYYXX';
		if (eregi('UP',$stage)) {$vol_prefix='4';}
		if (eregi('DOWN',$stage)) {$vol_prefix='3';}
		if (eregi('UNMUTE',$stage)) {$vol_prefix='2';}
		if (eregi('MUTING',$stage)) {$vol_prefix='1';}
		$local_DEF = 'Local/';
		$local_AMP = '@';
		$volume_local_channel = "$local_DEF$participant_number$vol_prefix$exten$local_AMP$ext_context";

		$stmt="INSERT INTO vicidial_manager values('','','$NOW_TIME','NEW','N','$server_ip','','Originate','$queryCID','Channel: $volume_local_channel','Context: $ext_context','Exten: 8300','Priority: 1','Callerid: $queryCID','','','','$channel','$exten');";
			if ($format=='debug') {echo "\n<!-- $stmt -->";}
		$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'02082',$user,$server_ip,$session_name,$one_mysql_log);}
		echo "Volume kommandot skickat till Konferens $exten, Stage $stage Kanal $channel på $server_ip\n";
		}
	}












$ENDtime = date("U");
$RUNtime = ($ENDtime - $StarTtime);
if ($format=='debug') {echo "\n<!-- scriptet tog: $RUNtime sekunder -->";}
if ($format=='debug') {echo "\n</body>\n</html>\n";}
	
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
			fwrite ($efp, "$NOW_TIME|manager_send|$query_id|$errno|$error|$stmt|$user|$server_ip|$session_name|\n");
			fclose($efp);
			}
		}
	$one_mysql_log=0;
	return $errno;
	}

?>
