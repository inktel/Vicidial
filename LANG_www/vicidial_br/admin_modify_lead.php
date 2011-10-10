<?php
# admin_modify_lead.php
# 
# ViciDial database administration modify lead in vicidial_list
# admin_modify_lead.php
#
# this is the administration lead information modifier screen, the administrator 
# just needs to enter the leadID and then they can view and modify the 
# information in the record for that lead
#
# Copyright (C) 2010  Matt Florell <vicidial@gmail.com>    LICENSE: AGPLv2
#
# CHANGES
#
# 60419-1705 - Added ability to change lead callback record from USERONLY to ANYONE or USERONLY-user
# 60421-1459 - check GET/POST vars lines with isset to not trigger PHP NOTICES
# 60609-1112 - Added DNC list addition if status changed to DNC
# 60619-1539 - Added variable filtering to eliminate SQL injection attack threat
# 61130-1639 - Added recording_log lookup and list for this lead_id
# 61201-1136 - Added recording_log user(TSR) display and link
# 70305-1133 - Changed to default CHECKED modify logs upon status change
# 70424-1128 - Added campaign-specific statuses, reformatted recordings list
# 70702-1259 - Added recording location link and truncation
# 70906-2132 - Added closer_log records display
# 80428-0144 - UTF8 cleanup
# 80501-0454 - Added Hangup Reason to logs display
# 80516-0936 - Cleanup of logging changes, added vicidial_agent_log display
# 80701-0832 - Changed to allow for altering of main phone number
# 80805-2106 - Changed comments to TEXTAREA
# 81210-1529 - Added server recording display options
# 90309-1829 - Added admin_log logging
# 90508-0644 - Changed to PHP long tags
# 90708-1549 - Added phone number dialed to outbound log
# 90721-1246 - Added rank and owner as vicidial_list fields
# 90917-2355 - Added extended alt phone entries
# 100405-1333 - Changed to show logs of non-found leads
#

require("dbconnect.php");

$PHP_AUTH_USER=$_SERVER['PHP_AUTH_USER'];
$PHP_AUTH_PW=$_SERVER['PHP_AUTH_PW'];
$PHP_SELF=$_SERVER['PHP_SELF'];
if (isset($_GET["vendor_id"]))				{$vendor_id=$_GET["vendor_id"];}
	elseif (isset($_POST["vendor_id"]))		{$vendor_id=$_POST["vendor_id"];}
if (isset($_GET["phone"]))				{$phone=$_GET["phone"];}
	elseif (isset($_POST["phone"]))		{$phone=$_POST["phone"];}
if (isset($_GET["old_phone"]))				{$old_phone=$_GET["old_phone"];}
	elseif (isset($_POST["old_phone"]))		{$old_phone=$_POST["old_phone"];}
if (isset($_GET["lead_id"]))				{$lead_id=$_GET["lead_id"];}
	elseif (isset($_POST["lead_id"]))		{$lead_id=$_POST["lead_id"];}
if (isset($_GET["first_name"]))				{$first_name=$_GET["first_name"];}
	elseif (isset($_POST["first_name"]))		{$first_name=$_POST["first_name"];}
if (isset($_GET["last_name"]))				{$last_name=$_GET["last_name"];}
	elseif (isset($_POST["last_name"]))		{$last_name=$_POST["last_name"];}
if (isset($_GET["phone_number"]))				{$phone_number=$_GET["phone_number"];}
	elseif (isset($_POST["phone_number"]))		{$phone_number=$_POST["phone_number"];}
if (isset($_GET["end_call"]))				{$end_call=$_GET["end_call"];}
	elseif (isset($_POST["end_call"]))		{$end_call=$_POST["end_call"];}
if (isset($_GET["DB"]))				{$DB=$_GET["DB"];}
	elseif (isset($_POST["DB"]))		{$DB=$_POST["DB"];}
if (isset($_GET["dispo"]))				{$dispo=$_GET["dispo"];}
	elseif (isset($_POST["dispo"]))		{$dispo=$_POST["dispo"];}
if (isset($_GET["list_id"]))				{$list_id=$_GET["list_id"];}
	elseif (isset($_POST["list_id"]))		{$list_id=$_POST["list_id"];}
if (isset($_GET["campaign_id"]))				{$campaign_id=$_GET["campaign_id"];}
	elseif (isset($_POST["campaign_id"]))		{$campaign_id=$_POST["campaign_id"];}
if (isset($_GET["phone_code"]))				{$phone_code=$_GET["phone_code"];}
	elseif (isset($_POST["phone_code"]))		{$phone_code=$_POST["phone_code"];}
if (isset($_GET["server_ip"]))				{$server_ip=$_GET["server_ip"];}
	elseif (isset($_POST["server_ip"]))		{$server_ip=$_POST["server_ip"];}
if (isset($_GET["extension"]))				{$extension=$_GET["extension"];}
	elseif (isset($_POST["extension"]))		{$extension=$_POST["extension"];}
if (isset($_GET["channel"]))				{$channel=$_GET["channel"];}
	elseif (isset($_POST["channel"]))		{$channel=$_POST["channel"];}
if (isset($_GET["call_began"]))				{$call_began=$_GET["call_began"];}
	elseif (isset($_POST["call_began"]))		{$call_began=$_POST["call_began"];}
if (isset($_GET["parked_time"]))				{$parked_time=$_GET["parked_time"];}
	elseif (isset($_POST["parked_time"]))		{$parked_time=$_POST["parked_time"];}
if (isset($_GET["tsr"]))				{$tsr=$_GET["tsr"];}
	elseif (isset($_POST["tsr"]))		{$tsr=$_POST["tsr"];}
if (isset($_GET["address1"]))				{$address1=$_GET["address1"];}
	elseif (isset($_POST["address1"]))		{$address1=$_POST["address1"];}
if (isset($_GET["address2"]))				{$address2=$_GET["address2"];}
	elseif (isset($_POST["address2"]))		{$address2=$_POST["address2"];}
if (isset($_GET["address3"]))				{$address3=$_GET["address3"];}
	elseif (isset($_POST["address3"]))		{$address3=$_POST["address3"];}
if (isset($_GET["city"]))				{$city=$_GET["city"];}
	elseif (isset($_POST["city"]))		{$city=$_POST["city"];}
if (isset($_GET["state"]))				{$state=$_GET["state"];}
	elseif (isset($_POST["state"]))		{$state=$_POST["state"];}
if (isset($_GET["postal_code"]))				{$postal_code=$_GET["postal_code"];}
	elseif (isset($_POST["postal_code"]))		{$postal_code=$_POST["postal_code"];}
if (isset($_GET["province"]))				{$province=$_GET["province"];}
	elseif (isset($_POST["province"]))		{$province=$_POST["province"];}
if (isset($_GET["country_code"]))				{$country_code=$_GET["country_code"];}
	elseif (isset($_POST["country_code"]))		{$country_code=$_POST["country_code"];}
if (isset($_GET["alt_phone"]))				{$alt_phone=$_GET["alt_phone"];}
	elseif (isset($_POST["alt_phone"]))		{$alt_phone=$_POST["alt_phone"];}
if (isset($_GET["email"]))				{$email=$_GET["email"];}
	elseif (isset($_POST["email"]))		{$email=$_POST["email"];}
if (isset($_GET["security"]))				{$security=$_GET["security"];}
	elseif (isset($_POST["security"]))		{$security=$_POST["security"];}
if (isset($_GET["comments"]))				{$comments=$_GET["comments"];}
	elseif (isset($_POST["comments"]))		{$comments=$_POST["comments"];}
if (isset($_GET["status"]))				{$status=$_GET["status"];}
	elseif (isset($_POST["status"]))		{$status=$_POST["status"];}
if (isset($_GET["rank"]))				{$rank=$_GET["rank"];}
	elseif (isset($_POST["rank"]))		{$rank=$_POST["rank"];}
if (isset($_GET["owner"]))				{$owner=$_GET["owner"];}
	elseif (isset($_POST["owner"]))		{$owner=$_POST["owner"];}
if (isset($_GET["submit"]))				{$submit=$_GET["submit"];}
	elseif (isset($_POST["submit"]))		{$submit=$_POST["submit"];}
if (isset($_GET["ENVIAR"]))				{$ENVIAR=$_GET["ENVIAR"];}
	elseif (isset($_POST["ENVIAR"]))		{$ENVIAR=$_POST["ENVIAR"];}
if (isset($_GET["CBchangeUSERtoANY"]))				{$CBchangeUSERtoANY=$_GET["CBchangeUSERtoANY"];}
	elseif (isset($_POST["CBchangeUSERtoANY"]))		{$CBchangeUSERtoANY=$_POST["CBchangeUSERtoANY"];}
if (isset($_GET["CBchangeUSERtoUSER"]))				{$CBchangeUSERtoUSER=$_GET["CBchangeUSERtoUSER"];}
	elseif (isset($_POST["CBchangeUSERtoUSER"]))		{$CBchangeUSERtoUSER=$_POST["CBchangeUSERtoUSER"];}
if (isset($_GET["CBchangeANYtoUSER"]))				{$CBchangeANYtoUSER=$_GET["CBchangeANYtoUSER"];}
	elseif (isset($_POST["CBchangeANYtoUSER"]))		{$CBchangeANYtoUSER=$_POST["CBchangeANYtoUSER"];}
if (isset($_GET["callback_id"]))				{$callback_id=$_GET["callback_id"];}
	elseif (isset($_POST["callback_id"]))		{$callback_id=$_POST["callback_id"];}
if (isset($_GET["CBuser"]))				{$CBuser=$_GET["CBuser"];}
	elseif (isset($_POST["CBuser"]))		{$CBuser=$_POST["CBuser"];}
if (isset($_GET["modify_logs"]))			{$modify_logs=$_GET["modify_logs"];}
	elseif (isset($_POST["modify_logs"]))	{$modify_logs=$_POST["modify_logs"];}
if (isset($_GET["modify_closer_logs"]))			{$modify_closer_logs=$_GET["modify_closer_logs"];}
	elseif (isset($_POST["modify_closer_logs"]))	{$modify_closer_logs=$_POST["modify_closer_logs"];}
if (isset($_GET["modify_agent_logs"]))			{$modify_agent_logs=$_GET["modify_agent_logs"];}
	elseif (isset($_POST["modify_agent_logs"]))	{$modify_agent_logs=$_POST["modify_agent_logs"];}
if (isset($_GET["add_closer_record"]))			{$add_closer_record=$_GET["add_closer_record"];}
	elseif (isset($_POST["add_closer_record"]))	{$add_closer_record=$_POST["add_closer_record"];}

$PHP_AUTH_USER = ereg_replace("[^0-9a-zA-Z]","",$PHP_AUTH_USER);
$PHP_AUTH_PW = ereg_replace("[^0-9a-zA-Z]","",$PHP_AUTH_PW);

$STARTtime = date("U");
$TODAY = date("Y-m-d");
$NOW_TIME = date("Y-m-d H:i:s");

#############################################
##### START SYSTEM_SETTINGS LOOKUP #####
$stmt = "SELECT use_non_latin FROM system_settings;";
$rslt=mysql_query($stmt, $link);
if ($DB) {echo "$stmt\n";}
$qm_conf_ct = mysql_num_rows($rslt);
$i=0;
while ($i < $qm_conf_ct)
	{
	$row=mysql_fetch_row($rslt);
	$non_latin =					$row[0];
	$i++;
	}
##### END SETTINGS LOOKUP #####
###########################################

if ($non_latin < 1)
	{
	$old_phone = ereg_replace("[^0-9]","",$old_phone);
	$phone_number = ereg_replace("[^0-9]","",$phone_number);
	$alt_phone = ereg_replace("[^0-9]","",$alt_phone);
	}
if (strlen($phone_number)<6) {$phone_number=$old_phone;}

$stmt="SELECT count(*) from vicidial_users where user='$PHP_AUTH_USER' and pass='$PHP_AUTH_PW' and user_level > 7 and modify_leads='1';";
if ($DB) {echo "|$stmt|\n";}
if ($non_latin > 0) {$rslt=mysql_query("SET NAMES 'UTF8'");}
$rslt=mysql_query($stmt, $link);
$row=mysql_fetch_row($rslt);
$auth=$row[0];

if ($WeBRooTWritablE > 0)
	{$fp = fopen ("./project_auth_entries.txt", "a");}

$date = date("r");
$ip = getenv("REMOTE_ADDR");
$browser = getenv("HTTP_USER_AGENT");

if( (strlen($PHP_AUTH_USER)<2) or (strlen($PHP_AUTH_PW)<2) or (!$auth))
	{
    Header("WWW-Authenticate: Basic realm=\"VICI-PROJECTS\"");
    Header("HTTP/1.0 401 Unauthorized");
    echo "Nome ou Senha inválidos: |$PHP_AUTH_USER|$PHP_AUTH_PW|\n";
    exit;
	}
else
	{

	if($auth>0)
		{
		$stmt="SELECT full_name,modify_leads from vicidial_users where user='$PHP_AUTH_USER' and pass='$PHP_AUTH_PW'";
		$rslt=mysql_query($stmt, $link);
		$row=mysql_fetch_row($rslt);
		$LOGfullname				=$row[0];
		$LOGmodify_leads			=$row[1];

		if ($WeBRooTWritablE > 0)
			{
			fwrite ($fp, "VICIDIAL|GOOD|$date|$PHP_AUTH_USER|$PHP_AUTH_PW|$ip|$browser|$LOGfullname|\n");
			fclose($fp);
			}
		}
	else
		{
		if ($WeBRooTWritablE > 0)
			{
			fwrite ($fp, "VICIDIAL|FAIL|$date|$PHP_AUTH_USER|$PHP_AUTH_PW|$ip|$browser|\n");
			fclose($fp);
			}
		}
	}

?>
<html>
<head>
<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=utf-8">
<title>ADMINISTRATION: Registro Alterado</title>
</head>
<BODY BGCOLOR=white marginheight=0 marginwidth=0 leftmargin=0 topmargin=0>
<CENTER><FONT FACE="Courier" COLOR=BLACK SIZE=3>
<?php 
echo "<a href=\"./admin.php?ADD=100\">ADMINISTRATION</a>: Registro Alterado<BR>\n";

if ($end_call > 0)
	{
	### update the lead record in the vicidial_list table 
	$stmt="UPDATE vicidial_list set status='" . mysql_real_escape_string($status) . "',first_name='" . mysql_real_escape_string($first_name) . "',last_name='" . mysql_real_escape_string($last_name) . "',address1='" . mysql_real_escape_string($address1) . "',address2='" . mysql_real_escape_string($address2) . "',address3='" . mysql_real_escape_string($address3) . "',city='" . mysql_real_escape_string($city) . "',state='" . mysql_real_escape_string($state) . "',province='" . mysql_real_escape_string($province) . "',postal_code='" . mysql_real_escape_string($postal_code) . "',country_code='" . mysql_real_escape_string($country_code) . "',alt_phone='" . mysql_real_escape_string($alt_phone) . "',phone_number='$phone_number',email='" . mysql_real_escape_string($email) . "',security_phrase='" . mysql_real_escape_string($security) . "',comments='" . mysql_real_escape_string($comments) . "',rank='" . mysql_real_escape_string($rank) . "',owner='" . mysql_real_escape_string($owner) . "' where lead_id='" . mysql_real_escape_string($lead_id) . "'";
	if ($DB) {echo "|$stmt|\n";}
	$rslt=mysql_query($stmt, $link);

	echo "Informação Alterada<BR><BR>\n";
	echo "<form><input type=button value=\"Fechar a Janela\" onClick=\"javascript:window.close();\"></form>\n";
	
	### LOG INSERTION Admin Log Table ###
	$SQL_log = "$stmt|";
	$SQL_log = ereg_replace(';','',$SQL_log);
	$SQL_log = addslashes($SQL_log);
	$stmt="INSERT INTO vicidial_admin_log set event_date='$NOW_TIME', user='$PHP_AUTH_USER', ip_address='$ip', event_section='LEADS', event_type='MODIFY', record_id='$lead_id', event_code='ADMIN MODIFY LEAD', event_sql=\"$SQL_log\", event_notes='';";
	if ($DB) {echo "|$stmt|\n";}
	$rslt=mysql_query($stmt, $link);

	if ( ($dispo != $status) and ($dispo == 'CBHOLD') )
		{
		### inactivate vicidial_callbacks record for this lead 
		$stmt="UPDATE vicidial_callbacks set status='INACTIVE' where lead_id='" . mysql_real_escape_string($lead_id) . "' and status='ACTIVE';";
		if ($DB) {echo "|$stmt|\n";}
		$rslt=mysql_query($stmt, $link);

		echo "<BR>vicidial_callback record inactivated: $lead_id<BR>\n";
		}
	if ( ($dispo != $status) and ($dispo == 'CALLBK') )
		{
		### inactivate vicidial_callbacks record for this lead 
		$stmt="UPDATE vicidial_callbacks set status='INACTIVE' where lead_id='" . mysql_real_escape_string($lead_id) . "' and status IN('ACTIVE','LIVE');";
		if ($DB) {echo "|$stmt|\n";}
		$rslt=mysql_query($stmt, $link);

		echo "<BR>vicidial_callback record inactivated: $lead_id<BR>\n";
		}

	if ( ($dispo != $status) and ($status == 'DNC') )
		{
		### add lead to the internal DNC list 
		$stmt="INSERT INTO vicidial_dnc (phone_number) values('" . mysql_real_escape_string($phone_number) . "');";
		if ($DB) {echo "|$stmt|\n";}
		$rslt=mysql_query($stmt, $link);

		echo "<BR>Lead added to DNC List: $lead_id - $phone_number<BR>\n";
		}
	### update last record in vicidial_log table
       if (($dispo != $status) and ($modify_logs > 0)) 
		{
		$stmt="UPDATE vicidial_log set status='" . mysql_real_escape_string($status) . "' where lead_id='" . mysql_real_escape_string($lead_id) . "' order by call_date desc limit 1";
		if ($DB) {echo "|$stmt|\n";}
		$rslt=mysql_query($stmt, $link);
		}

	### update last record in vicidial_closer_log table
       if (($dispo != $status) and ($modify_closer_logs > 0)) 
		{
		$stmt="UPDATE vicidial_closer_log set status='" . mysql_real_escape_string($status) . "' where lead_id='" . mysql_real_escape_string($lead_id) . "' order by call_date desc limit 1";
		if ($DB) {echo "|$stmt|\n";}
		$rslt=mysql_query($stmt, $link);
		}

	### update last record in vicidial_agent_log table
       if (($dispo != $status) and ($modify_agent_logs > 0)) 
		{
		$stmt="UPDATE vicidial_agent_log set status='" . mysql_real_escape_string($status) . "' where lead_id='" . mysql_real_escape_string($lead_id) . "' order by agent_log_id desc limit 1";
		if ($DB) {echo "|$stmt|\n";}
		$rslt=mysql_query($stmt, $link);
		}

	if ($add_closer_record > 0)
		{
		### insert a NEW record to the vicidial_closer_log table 
		$stmt="INSERT INTO vicidial_closer_log (lead_id,list_id,campaign_id,call_date,start_epoch,end_epoch,length_in_sec,status,phone_code,phone_number,user,comments,processed) values('" . mysql_real_escape_string($lead_id) . "','" . mysql_real_escape_string($list_id) . "','" . mysql_real_escape_string($campaign_id) . "','" . mysql_real_escape_string($parked_time) . "','$NOW_TIME','$STARTtime','1','" . mysql_real_escape_string($status) . "','" . mysql_real_escape_string($phone_code) . "','" . mysql_real_escape_string($phone_number) . "','$PHP_AUTH_USER','" . mysql_real_escape_string($comments) . "','Y')";
		if ($DB) {echo "|$stmt|\n";}
		$rslt=mysql_query($stmt, $link);
		}


	}
else
	{

	if ($CBchangeUSERtoANY == 'YES')
		{
		### inactivate vicidial_callbacks record for this lead 
		$stmt="UPDATE vicidial_callbacks set recipient='ANYONE' where callback_id='" . mysql_real_escape_string($callback_id) . "';";
		if ($DB) {echo "|$stmt|\n";}
		$rslt=mysql_query($stmt, $link);

		echo "<BR>vicidial_callback record changed to ANYONE<BR>\n";
		}
	if ($CBchangeUSERtoUSER == 'YES')
		{
		### inactivate vicidial_callbacks record for this lead 
		$stmt="UPDATE vicidial_callbacks set user='" . mysql_real_escape_string($CBuser) . "' where callback_id='" . mysql_real_escape_string($callback_id) . "';";
		if ($DB) {echo "|$stmt|\n";}
		$rslt=mysql_query($stmt, $link);

		echo "<BR>vicidial_callback record user changed to $CBuser<BR>\n";
		}	
	if ($CBchangeANYtoUSER == 'YES')
		{
		### inactivate vicidial_callbacks record for this lead 
		$stmt="UPDATE vicidial_callbacks set user='" . mysql_real_escape_string($CBuser) . "',recipient='USERONLY' where callback_id='" . mysql_real_escape_string($callback_id) . "';";
		if ($DB) {echo "|$stmt|\n";}
		$rslt=mysql_query($stmt, $link);

		echo "<BR>vicidial_callback record changed to USERONLY, user: $CBuser<BR>\n";
		}	
	
	

	$stmt="SELECT count(*) from vicidial_list where lead_id='" . mysql_real_escape_string($lead_id) . "'";
	$rslt=mysql_query($stmt, $link);
	if ($DB) {echo "$stmt\n";}
	$row=mysql_fetch_row($rslt);
	$lead_count = $row[0];

	if ($lead_count > 0)
		{
		##### grab vicidial_list_alt_phones records #####
		$stmt="select phone_code,phone_number,alt_phone_note,alt_phone_count,active from vicidial_list_alt_phones where lead_id='" . mysql_real_escape_string($lead_id) . "' order by alt_phone_count limit 500;";
		$rslt=mysql_query($stmt, $link);
		$alts_to_print = mysql_num_rows($rslt);

		$c=0;
		$alts_output = '';
		while ($alts_to_print > $c) 
			{
			$row=mysql_fetch_row($rslt);
			if (eregi("1$|3$|5$|7$|9$", $c))
				{$bgcolor='bgcolor="#B9CBFD"';} 
			else
				{$bgcolor='bgcolor="#9BB9FB"';}

			$c++;
			$alts_output .= "<tr $bgcolor>";
			$alts_output .= "<td><font size=1>$c</td>";
			$alts_output .= "<td><font size=2>$row[0] $row[1]</td>";
			$alts_output .= "<td align=left><font size=2> $row[2]</td>\n";
			$alts_output .= "<td align=left><font size=2> $row[3]</td>\n";
			$alts_output .= "<td align=left><font size=2> $row[4] </td></tr>\n";
			}

		}
	else
		{
		echo "procura de registro FALHOU para este lead_id $lead_id &nbsp; &nbsp; &nbsp; $NOW_TIME\n<BR><BR>\n";
#		echo "<a href=\"$PHP_SELF\">Close this window</a>\n<BR><BR>\n";
		}

	##### grab vicidial_log records #####
	$stmt="select uniqueid,lead_id,list_id,campaign_id,call_date,start_epoch,end_epoch,length_in_sec,status,phone_code,phone_number,user,comments,processed,user_group,term_reason,alt_dial from vicidial_log where lead_id='" . mysql_real_escape_string($lead_id) . "' order by uniqueid desc limit 500;";
	$rslt=mysql_query($stmt, $link);
	$logs_to_print = mysql_num_rows($rslt);

	$u=0;
	$call_log = '';
	$log_campaign = '';
	while ($logs_to_print > $u) 
		{
		$row=mysql_fetch_row($rslt);
		if (strlen($log_campaign)<1) {$log_campaign = $row[3];}
		if (eregi("1$|3$|5$|7$|9$", $u))
			{$bgcolor='bgcolor="#B9CBFD"';} 
		else
			{$bgcolor='bgcolor="#9BB9FB"';}

		$u++;
		$call_log .= "<tr $bgcolor>";
		$call_log .= "<td><font size=1>$u</td>";
		$call_log .= "<td><font size=2>$row[4]</td>";
		$call_log .= "<td align=left><font size=2> $row[7]</td>\n";
		$call_log .= "<td align=left><font size=2> $row[8]</td>\n";
		$call_log .= "<td align=left><font size=2> <A HREF=\"user_stats.php?user=$row[11]\" target=\"_blank\">$row[11]</A> </td>\n";
		$call_log .= "<td align=right><font size=2> $row[3] </td>\n";
		$call_log .= "<td align=right><font size=2> $row[2] </td>\n";
		$call_log .= "<td align=right><font size=2> $row[1] </td>\n";
		$call_log .= "<td align=right><font size=2> $row[15] </td>\n";
		$call_log .= "<td align=right><font size=2>&nbsp; $row[10] </td></tr>\n";

		$campaign_id = $row[3];
		}

	##### grab vicidial_agent_log records #####
	$stmt="select agent_log_id,user,server_ip,event_time,lead_id,campaign_id,pause_epoch,pause_sec,wait_epoch,wait_sec,talk_epoch,talk_sec,dispo_epoch,dispo_sec,status,user_group,comments,sub_status from vicidial_agent_log where lead_id='" . mysql_real_escape_string($lead_id) . "' order by agent_log_id desc limit 500;";
	$rslt=mysql_query($stmt, $link);
	$Alogs_to_print = mysql_num_rows($rslt);

	$y=0;
	$agent_log = '';
	$Alog_campaign = '';
	while ($Alogs_to_print > $y) 
		{
		$row=mysql_fetch_row($rslt);
		if (strlen($Alog_campaign)<1) {$Alog_campaign = $row[5];}
		if (eregi("1$|3$|5$|7$|9$", $y))
			{$bgcolor='bgcolor="#B9CBFD"';} 
		else
			{$bgcolor='bgcolor="#9BB9FB"';}

		$y++;
		$agent_log .= "<tr $bgcolor>";
		$agent_log .= "<td><font size=1>$y</td>";
		$agent_log .= "<td><font size=2>$row[3]</td>";
		$agent_log .= "<td align=left><font size=2> $row[5]</td>\n";
		$agent_log .= "<td align=left><font size=2> <A HREF=\"user_stats.php?user=$row[1]\" target=\"_blank\">$row[1]</A> </td>\n";
		$agent_log .= "<td align=right><font size=2> $row[7]</td>\n";
		$agent_log .= "<td align=right><font size=2> $row[9] </td>\n";
		$agent_log .= "<td align=right><font size=2> $row[11] </td>\n";
		$agent_log .= "<td align=right><font size=2> $row[13] </td>\n";
		$agent_log .= "<td align=right><font size=2> &nbsp; $row[14] </td>\n";
		$agent_log .= "<td align=right><font size=2> &nbsp; $row[15] </td>\n";
		$agent_log .= "<td align=right><font size=2> &nbsp; $row[17] </td></tr>\n";

		$campaign_id = $row[5];
		}

	##### grab vicidial_closer_log records #####
	$stmt="select closecallid,lead_id,list_id,campaign_id,call_date,start_epoch,end_epoch,length_in_sec,status,phone_code,phone_number,user,comments,processed,queue_seconds,user_group,xfercallid,term_reason,uniqueid,agent_only from vicidial_closer_log where lead_id='" . mysql_real_escape_string($lead_id) . "' order by closecallid desc limit 500;";
	$rslt=mysql_query($stmt, $link);
	$Clogs_to_print = mysql_num_rows($rslt);

	$y=0;
	$closer_log = '';
	$Clog_campaign = '';
	while ($Clogs_to_print > $y) 
		{
		$row=mysql_fetch_row($rslt);
		if (strlen($Clog_campaign)<1) {$Clog_campaign = $row[3];}
		if (eregi("1$|3$|5$|7$|9$", $y))
			{$bgcolor='bgcolor="#B9CBFD"';} 
		else
			{$bgcolor='bgcolor="#9BB9FB"';}

		$y++;
		$closer_log .= "<tr $bgcolor>";
		$closer_log .= "<td><font size=1>$y</td>";
		$closer_log .= "<td><font size=2>$row[4]</td>";
		$closer_log .= "<td align=left><font size=2> $row[7]</td>\n";
		$closer_log .= "<td align=left><font size=2> $row[8]</td>\n";
		$closer_log .= "<td align=left><font size=2> <A HREF=\"user_stats.php?user=$row[11]\" target=\"_blank\">$row[11]</A> </td>\n";
		$closer_log .= "<td align=right><font size=2> $row[3] </td>\n";
		$closer_log .= "<td align=right><font size=2> $row[2] </td>\n";
		$closer_log .= "<td align=right><font size=2> $row[1] </td>\n";
		$closer_log .= "<td align=right><font size=2> &nbsp; $row[14] </td>\n";
		$closer_log .= "<td align=right><font size=2> &nbsp; $row[17] </td></tr>\n";

		$campaign_id = $row[3];
		}

	##### grab vicidial_list data for lead #####
	$stmt="SELECT lead_id,entry_date,modify_date,status,user,vendor_lead_code,source_id,list_id,gmt_offset_now,called_since_last_reset,phone_code,phone_number,title,first_name,middle_initial,last_name,address1,address2,address3,city,state,province,postal_code,country_code,gender,date_of_birth,alt_phone,email,security_phrase,comments,called_count,last_local_call_time,rank,owner from vicidial_list where lead_id='" . mysql_real_escape_string($lead_id) . "'";
	$rslt=mysql_query($stmt, $link);
	if ($DB) {echo "$stmt\n";}
	$row=mysql_fetch_row($rslt);
	if (strlen($row[0]) > 0)
		{$lead_id		= $row[0];}
	$dispo				= $row[3];
	$tsr				= $row[4];
	$vendor_id			= $row[5];
	$list_id			= $row[7];
	$gmt_offset_now		= $row[8];
	$phone_code			= $row[10];
	$phone_number		= $row[11];
	$title				= $row[12];
	$first_name			= $row[13];
	$middle_initial		= $row[14];
	$last_name			= $row[15];
	$address1			= $row[16];
	$address2			= $row[17];
	$address3			= $row[18];
	$city				= $row[19];
	$state				= $row[20];
	$province			= $row[21];
	$postal_code		= $row[22];
	$country_code		= $row[23];
	$gender				= $row[24];
	$date_of_birth		= $row[25];
	$alt_phone			= $row[26];
	$email				= $row[27];
	$security			= $row[28];
	$comments			= $row[29];
	$called_count		= $row[30];
	$last_local_call_time = $row[31];
	$rank				= $row[32];
	$owner				= $row[33];

	echo "<br>Informações da Chamada: $first_name $last_name - $phone_number<br><br><form action=$PHP_SELF method=POST>\n";
	echo "<input type=hidden name=end_call value=1>\n";
	echo "<input type=hidden name=DB value=\"$DB\">\n";
	echo "<input type=hidden name=lead_id value=\"$lead_id\">\n";
	echo "<input type=hidden name=dispo value=\"$dispo\">\n";
	echo "<input type=hidden name=list_id value=\"$list_id\">\n";
	echo "<input type=hidden name=campaign_id value=\"$campaign_id\">\n";
	echo "<input type=hidden name=phone_code value=\"$phone_code\">\n";
	echo "<input type=hidden name=old_phone value=\"$phone_number\">\n";
	echo "<input type=hidden name=server_ip value=\"$server_ip\">\n";
	echo "<input type=hidden name=extension value=\"$extension\">\n";
	echo "<input type=hidden name=channel value=\"$channel\">\n";
	echo "<input type=hidden name=call_began value=\"$call_began\">\n";
	echo "<input type=hidden name=parked_time value=\"$parked_time\">\n";
	echo "<table cellpadding=1 cellspacing=0>\n";
	echo "<tr><td colspan=2>Vendor ID: $vendor_id &nbsp; &nbsp; Lead ID: $lead_id</td></tr>\n";
	echo "<tr><td colspan=2>Fronter: <A HREF=\"user_stats.php?user=$tsr\">$tsr</A> &nbsp; &nbsp; ID da Lista: $list_id</td></tr>\n";
	echo "<tr><td align=right>Nome: </td><td align=left><input type=text name=first_name size=15 maxlength=30 value=\"$first_name\"> &nbsp; \n";
	echo " Sobrenome: <input type=text name=last_name size=15 maxlength=30 value=\"$last_name\"> </td></tr>\n";
	echo "<tr><td align=right>Endereço 1 : </td><td align=left><input type=text name=address1 size=30 maxlength=30 value=\"$address1\"></td></tr>\n";
	echo "<tr><td align=right>Endereço 2 : </td><td align=left><input type=text name=address2 size=30 maxlength=30 value=\"$address2\"></td></tr>\n";
	echo "<tr><td align=right>Endereço 3 : </td><td align=left><input type=text name=address3 size=30 maxlength=30 value=\"$address3\"></td></tr>\n";
	echo "<tr><td align=right>Cidade : </td><td align=left><input type=text name=city size=30 maxlength=30 value=\"$city\"></td></tr>\n";
	echo "<tr><td align=right>Estado: </td><td align=left><input type=text name=state size=2 maxlength=2 value=\"$state\"> &nbsp; \n";
	echo " CEP: <input type=text name=postal_code size=10 maxlength=10 value=\"$postal_code\"> </td></tr>\n";

	echo "<tr><td align=right>Província: </td><td align=left><input type=text name=province size=30 maxlength=30 value=\"$province\"></td></tr>\n";
	echo "<tr><td align=right>País    : </td><td align=left><input type=text name=country_code size=3 maxlength=3 value=\"$country_code\"></td></tr>\n";
	echo "<tr><td align=right>Main Phone : </td><td align=left><input type=text name=phone_number size=20 maxlength=20 value=\"$phone_number\"></td></tr>\n";
	echo "<tr><td align=right>Tel. Alt. : </td><td align=left><input type=text name=alt_phone size=20 maxlength=20 value=\"$alt_phone\"></td></tr>\n";
	echo "<tr><td align=right>Email : </td><td align=left><input type=text name=email size=30 maxlength=50 value=\"$email\"></td></tr>\n";
	echo "<tr><td align=right>Segurança: </td><td align=left><input type=text name=security size=30 maxlength=100 value=\"$security\"></td></tr>\n";
	echo "<tr><td align=right>Rank : </td><td align=left><input type=text name=rank size=7 maxlength=5 value=\"$rank\"></td></tr>\n";
	echo "<tr><td align=right>Owner : </td><td align=left><input type=text name=owner size=22 maxlength=20 value=\"$owner\"></td></tr>\n";
	echo "<tr><td align=right>Comentários : </td><td align=left><TEXTAREA name=comments ROWS=3 COLS=65>$comments</TEXTAREA></td></tr>\n";
	echo "<tr bgcolor=#B6D3FC><td align=right>Finalização: </td><td align=left><select size=1 name=status>\n";

	$stmt="SELECT status,status_name,selectable,human_answered,category,sale,dnc,customer_contact,not_interested,unworkable from vicidial_statuses where selectable='Y' order by status";
	$rslt=mysql_query($stmt, $link);
	$statuses_to_print = mysql_num_rows($rslt);
	$statuses_list='';

	$o=0;
	$DS=0;
	while ($statuses_to_print > $o) 
		{
		$rowx=mysql_fetch_row($rslt);
		if ( (strlen($dispo) ==  strlen($rowx[0])) and (eregi($dispo,$rowx[0])) )
			{$statuses_list .= "<option SELECTED value=\"$rowx[0]\">$rowx[0] - $rowx[1]</option>\n"; $DS++;}
		else
			{$statuses_list .= "<option value=\"$rowx[0]\">$rowx[0] - $rowx[1]</option>\n";}
		$o++;
		}

	$stmt="SELECT status,status_name,selectable,campaign_id,human_answered,category,sale,dnc,customer_contact,not_interested,unworkable from vicidial_campaign_statuses where selectable='Y' and campaign_id='$log_campaign' order by status";
	$rslt=mysql_query($stmt, $link);
	$CAMPstatuses_to_print = mysql_num_rows($rslt);

	$o=0;
	while ($CAMPstatuses_to_print > $o) 
		{
		$rowx=mysql_fetch_row($rslt);
		if ( (strlen($dispo) ==  strlen($rowx[0])) and (eregi($dispo,$rowx[0])) )
			{$statuses_list .= "<option SELECTED value=\"$rowx[0]\">$rowx[0] - $rowx[1]</option>\n"; $DS++;}
		else
			{$statuses_list .= "<option value=\"$rowx[0]\">$rowx[0] - $rowx[1]</option>\n";}
		$o++;
		}


	if ($DS < 1) {$statuses_list .= "<option SELECTED value=\"$dispo\">$dispo</option>\n";}
	echo "$statuses_list";
	echo "</select> <i>(with $log_campaign statuses)</i></td></tr>\n";


	echo "<tr bgcolor=#B6D3FC><td align=left>Alterarvicidial log </td><td align=left><input type=checkbox name=modify_logs value=\"1\" CHECKED></td></tr>\n";
	echo "<tr bgcolor=#B6D3FC><td align=left>Alteraragent log </td><td align=left><input type=checkbox name=modify_agent_logs value=\"1\" CHECKED></td></tr>\n";
	echo "<tr bgcolor=#B6D3FC><td align=left>Alterarcloser log </td><td align=left><input type=checkbox name=modify_closer_logs value=\"1\"></td></tr>\n";
	echo "<tr bgcolor=#B6D3FC><td align=left>Add closer log record </td><td align=left><input type=checkbox name=add_closer_record value=\"1\"></td></tr>\n";


	echo "<tr><td colspan=2 align=center><input type=submit name=submit value=\"ENVIAR\"></td></tr>\n";
	echo "</table></form>\n";
	echo "<BR><BR><BR>\n";

	if ( ($dispo == 'CALLBK') or ($dispo == 'CBHOLD') )
		{
		### find any vicidial_callback records for this lead 
		$stmt="select callback_id,lead_id,list_id,campaign_id,status,entry_time,callback_time,modify_date,user,recipient,comments,user_group from vicidial_callbacks where lead_id='" . mysql_real_escape_string($lead_id) . "' and status IN('ACTIVE','LIVE') order by callback_id desc LIMIT 1;";
		if ($DB) {echo "|$stmt|\n";}
		$rslt=mysql_query($stmt, $link);
		$CB_to_print = mysql_num_rows($rslt);
		$rowx=mysql_fetch_row($rslt);

		if ($CB_to_print>0)
			{
			if ($rowx[9] == 'USERONLY')
				{
				echo "<br><form action=$PHP_SELF method=POST>\n";
				echo "<input type=hidden name=CBchangeUSERtoANY value=\"YES\">\n";
				echo "<input type=hidden name=DB value=\"$DB\">\n";
				echo "<input type=hidden name=lead_id value=\"$lead_id\">\n";
				echo "<input type=hidden name=callback_id value=\"$rowx[0]\">\n";
				echo "<input type=submit name=submit value=\"CHANGE TO ANYONE CALLBACK\"></form><BR>\n";

				echo "<br><form action=$PHP_SELF method=POST>\n";
				echo "<input type=hidden name=CBchangeUSERtoUSER value=\"YES\">\n";
				echo "<input type=hidden name=DB value=\"$DB\">\n";
				echo "<input type=hidden name=lead_id value=\"$lead_id\">\n";
				echo "<input type=hidden name=callback_id value=\"$rowx[0]\">\n";
				echo "New Callback Owner UsuárioID: <input type=text name=CBuser size=8 maxlength=10 value=\"$rowx[8]\"> \n";
				echo "<input type=submit name=submit value=\"CHANGE USERONLY CALLBACK USER\"></form><BR>\n";
				}
			else
				{
				echo "<br><form action=$PHP_SELF method=POST>\n";
				echo "<input type=hidden name=CBchangeANYtoUSER value=\"YES\">\n";
				echo "<input type=hidden name=DB value=\"$DB\">\n";
				echo "<input type=hidden name=lead_id value=\"$lead_id\">\n";
				echo "<input type=hidden name=callback_id value=\"$rowx[0]\">\n";
				echo "New Callback Owner UsuárioID: <input type=text name=CBuser size=8 maxlength=10 value=\"$rowx[8]\"> \n";
				echo "<input type=submit name=submit value=\"CHANGE TO USERONLY CALLBACK\"></form><BR>\n";
				}
			}
		else
			{
			echo "<BR>No Callback records found<BR>\n";
			}
		}


	echo "<br><br>\n";

	echo "<center>\n";

	if ($c > 0)
		{
		echo "<B>EXTENDED SUPLENTE NÚMEROS DE TELÉFONO PARA ESTE DE PLOMO:</B>\n";
		echo "<TABLE width=550 cellspacing=0 cellpadding=1>\n";
		echo "<tr><td><font size=1># </td><td><font size=2>ALT PHONE </td><td align=left><font size=2>ALT NOTE</td><td align=left><font size=2> ALT COUNT</td><td align=left><font size=2> ACTIVE</td></tr>\n";

		echo "$alts_output\n";

		echo "</TABLE>\n";
		echo "<BR><BR>\n";
		}

	echo "<B>CHAMADAS PARA ESSE REGISTRO:</B>\n";
	echo "<TABLE width=750 cellspacing=0 cellpadding=1>\n";
	echo "<tr><td><font size=1># </td><td><font size=2>DATE/TIME </td><td align=left><font size=2>LENGTH</td><td align=left><font size=2> STATUS</td><td align=left><font size=2> TSR</td><td align=right><font size=2> CAMPANHA</td><td align=right><font size=2> LIST</td><td align=right><font size=2> LEAD</td><td align=right><font size=2> HANGUP REASON</td><td align=right><font size=2> PHONE</td></tr>\n";

	echo "$call_log\n";

	echo "</TABLE>\n";
	echo "<BR><BR>\n";

	echo "<B>CLOSER RECORDS FOR THIS LEAD:</B>\n";
	echo "<TABLE width=750 cellspacing=0 cellpadding=1>\n";
	echo "<tr><td><font size=1># </td><td><font size=2>DATE/TIME </td><td align=left><font size=2>LENGTH</td><td align=left><font size=2> STATUS</td><td align=left><font size=2> TSR</td><td align=right><font size=2> CAMPANHA</td><td align=right><font size=2> LIST</td><td align=right><font size=2> LEAD</td><td align=right><font size=2> WAIT</td><td align=right><font size=2> HANGUP REASON</td></tr>\n";

	echo "$closer_log\n";

	echo "</TABLE></center>\n";
	echo "<BR><BR>\n";


	echo "<B>AGENTE LOG RECORDS FOR THIS LEAD:</B>\n";
	echo "<TABLE width=750 cellspacing=0 cellpadding=1>\n";
	echo "<tr><td><font size=1># </td><td><font size=2>DATE/TIME </td><td align=left><font size=2>CAMPANHA</td><td align=left><font size=2> TSR</td><td align=left><font size=2> PAUSE</td><td align=right><font size=2> WAIT</td><td align=right><font size=2> TALK</td><td align=right><font size=2> DISPO</td><td align=right><font size=2> STATUS</td><td align=right><font size=2> GROUP</td><td align=right><font size=2> SUB</td></tr>\n";

		echo "$agent_log\n";

	echo "</TABLE>\n";
	echo "<BR><BR>\n";


	echo "<B>RECORDINGS FOR THIS LEAD:</B>\n";
	echo "<TABLE width=750 cellspacing=1 cellpadding=1>\n";
	echo "<tr><td><font size=1># </td><td align=left><font size=2> LEAD</td><td><font size=2>DATE/TIME </td><td align=left><font size=2>SECONDS </td><td align=left><font size=2> &nbsp; RECID</td><td align=center><font size=2>FILENAME</td><td align=left><font size=2>LOCATION</td><td align=left><font size=2>TSR</td></tr>\n";

	$stmt="select recording_id,channel,server_ip,extension,start_time,start_epoch,end_time,end_epoch,length_in_sec,length_in_min,filename,location,lead_id,user,vicidial_id from recording_log where lead_id='" . mysql_real_escape_string($lead_id) . "' order by recording_id desc limit 500;";
	$rslt=mysql_query($stmt, $link);
	$logs_to_print = mysql_num_rows($rslt);
	if ($DB) {echo "$logs_to_print|$stmt|\n";}

	$u=0;
	while ($logs_to_print > $u) 
		{
		$row=mysql_fetch_row($rslt);
		if (eregi("1$|3$|5$|7$|9$", $u))
			{$bgcolor='bgcolor="#B9CBFD"';} 
		else
			{$bgcolor='bgcolor="#9BB9FB"';}

		$location = $row[11];

		if (strlen($location)>2)
			{
			$URLserver_ip = $location;
			$URLserver_ip = eregi_replace('http://','',$URLserver_ip);
			$URLserver_ip = eregi_replace('https://','',$URLserver_ip);
			$URLserver_ip = eregi_replace("\/.*",'',$URLserver_ip);
			$stmt="select count(*) from servers where server_ip='$URLserver_ip';";
			$rsltx=mysql_query($stmt, $link);
			$rowx=mysql_fetch_row($rsltx);
			
			if ($rowx[0] > 0)
				{
				$stmt="select recording_web_link,alt_server_ip from servers where server_ip='$URLserver_ip';";
				$rsltx=mysql_query($stmt, $link);
				$rowx=mysql_fetch_row($rsltx);
				
				if (eregi("ALT_IP",$rowx[0]))
					{
					$location = eregi_replace($URLserver_ip, $rowx[1], $location);
					}
				}
			}

		if (strlen($location)>30)
			{$locat = substr($location,0,27);  $locat = "$locat...";}
		else
			{$locat = $location;}
		if (eregi("http",$location))
			{$location = "<a href=\"$location\">$locat</a>";}
		else
			{$location = $locat;}
		$u++;
		echo "<tr $bgcolor>";
		echo "<td><font size=1>$u</td>";
		echo "<td align=left><font size=2> $row[12] </td>";
		echo "<td align=left><font size=1> $row[4] </td>\n";
		echo "<td align=left><font size=2> $row[8] </td>\n";
		echo "<td align=left><font size=2> $row[0] &nbsp;</td>\n";
		echo "<td align=center><font size=1> $row[10] </td>\n";
		echo "<td align=left><font size=2> $location </td>\n";
		echo "<td align=left><font size=2> <A HREF=\"user_stats.php?user=$row[13]\" target=\"_blank\">$row[13]</A> </td>";
		echo "</tr>\n";

		}


	echo "</TABLE></center>\n";
	}


$ENDtime = date("U");

$RUNtime = ($ENDtime - $STARTtime);

echo "\n\n\n<br><br><br>\n\n";


echo "<font size=0>\n\n\n<br><br><br>\nScript runtime: $RUNtime seconds</font>";


?>


</body>
</html>

<?php
	
exit; 


?>
