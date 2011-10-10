<?php
# user_stats.php
# 
# Copyright (C) 2010  Matt Florell <vicidial@gmail.com>    LICENSE: AGPLv2
#
# CHANGES
#
# 60619-1743 - Added variable filtering to eliminate SQL injection attack threat
# 61201-1136 - Added recordings display and changed calls to time range with 10000 limit
# 70118-1605 - Added user group column to login/out and calls lists
# 70702-1231 - Added recording location link and truncation
# 80117-0316 - Added vicidial_user_closer_log entries to display
# 80501-0506 - Added Hangup Reason to logs display
# 80523-2012 - Added vicidial timeclock records display
# 80617-1402 - Fixed timeclock total logged-in time
# 81210-1634 - Added server recording display options
# 90208-0504 - Added link to multi-day report and fixed call status summary section
# 90305-1226 - Added user_call_log manual dial logs
# 90310-0734 - Added admin header
# 90508-0644 - Changed to PHP long tags
# 90524-2009 - Changed time display to use functions.php
# 91130-2037 - Added user closer log manager flag display
# 100203-1008 - Added agent activity log section
# 100216-0042 - Added popup date selector
#

header ("Content-type: text/html; charset=utf-8");

require("dbconnect.php");
require("functions.php");

#############################################
##### START SYSTEM_SETTINGS LOOKUP #####
$stmt = "SELECT use_non_latin,outbound_autodial_active,user_territories_active FROM system_settings;";
$rslt=mysql_query($stmt, $link);
if ($DB) {echo "$stmt\n";}
$ss_conf_ct = mysql_num_rows($rslt);
if ($ss_conf_ct > 0)
	{
	$row=mysql_fetch_row($rslt);
	$non_latin =						$row[0];
	$SSoutbound_autodial_active =		$row[1];
	$user_territories_active =			$row[2];
	}
##### END SETTINGS LOOKUP #####
###########################################

$PHP_AUTH_USER=$_SERVER['PHP_AUTH_USER'];
$PHP_AUTH_PW=$_SERVER['PHP_AUTH_PW'];
$PHP_SELF=$_SERVER['PHP_SELF'];
if (isset($_GET["begin_date"]))				{$begin_date=$_GET["begin_date"];}
	elseif (isset($_POST["begin_date"]))	{$begin_date=$_POST["begin_date"];}
if (isset($_GET["end_date"]))				{$end_date=$_GET["end_date"];}
	elseif (isset($_POST["end_date"]))		{$end_date=$_POST["end_date"];}
if (isset($_GET["user"]))					{$user=$_GET["user"];}
	elseif (isset($_POST["user"]))			{$user=$_POST["user"];}
if (isset($_GET["campaign"]))				{$campaign=$_GET["campaign"];}
	elseif (isset($_POST["campaign"]))		{$campaign=$_POST["campaign"];}
if (isset($_GET["DB"]))						{$DB=$_GET["DB"];}
	elseif (isset($_POST["DB"]))			{$DB=$_POST["DB"];}
if (isset($_GET["submit"]))					{$submit=$_GET["submit"];}
	elseif (isset($_POST["submit"]))		{$submit=$_POST["submit"];}
if (isset($_GET["VALIDER"]))					{$VALIDER=$_GET["VALIDER"];}
	elseif (isset($_POST["VALIDER"]))		{$VALIDER=$_POST["VALIDER"];}

$PHP_AUTH_USER = ereg_replace("[^0-9a-zA-Z]","",$PHP_AUTH_USER);
$PHP_AUTH_PW = ereg_replace("[^0-9a-zA-Z]","",$PHP_AUTH_PW);

$STARTtime = date("U");
$TODAY = date("Y-m-d");

if (!isset($begin_date)) {$begin_date = $TODAY;}
if (!isset($end_date)) {$end_date = $TODAY;}

$stmt="SELECT count(*) from vicidial_users where user='$PHP_AUTH_USER' and pass='$PHP_AUTH_PW' and user_level > 7 and view_reports='1';";
if ($non_latin > 0) { $rslt=mysql_query("SET NAMES 'UTF8'");}
$rslt=mysql_query($stmt, $link);
$row=mysql_fetch_row($rslt);
$auth=$row[0];

$fp = fopen ("./project_auth_entries.txt", "a");
$date = date("r");
$ip = getenv("REMOTE_ADDR");
$browser = getenv("HTTP_USER_AGENT");

if( (strlen($PHP_AUTH_USER)<2) or (strlen($PHP_AUTH_PW)<2) or (!$auth))
	{
    Header("WWW-Authenticate: Basic realm=\"VICI-PROJECTS\"");
    Header("HTTP/1.0 401 Unauthorized");
    echo "Login ou mot de passe invalide: |$PHP_AUTH_USER|$PHP_AUTH_PW|\n";
    exit;
	}
else
	{

	if($auth>0)
		{
		$stmt="SELECT full_name from vicidial_users where user='$PHP_AUTH_USER' and pass='$PHP_AUTH_PW'";
		$rslt=mysql_query($stmt, $link);
		$row=mysql_fetch_row($rslt);
		$LOGfullname=$row[0];

		fwrite ($fp, "VICIDIAL|GOOD|$date|$PHP_AUTH_USER|$PHP_AUTH_PW|$ip|$browser|$LOGfullname|\n");
		fclose($fp);
		}
	else
		{
		fwrite ($fp, "VICIDIAL|FAIL|$date|$PHP_AUTH_USER|$PHP_AUTH_PW|$ip|$browser|\n");
		fclose($fp);
		echo "Login ou mot de passe invalide: |$PHP_AUTH_USER|$PHP_AUTH_PW|\n";
		exit;
		}

	$stmt="SELECT full_name from vicidial_users where user='$user';";
	$rslt=mysql_query($stmt, $link);
	$row=mysql_fetch_row($rslt);
	$full_name = $row[0];
	}




?>
<html>
<head>

<script language="JavaScript" src="calendar_db.js"></script>
<link rel="stylesheet" href="calendar.css">

<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=utf-8">
<title>ADMINISTRATION: Statistiques utilisateur
<?php


##### BEGIN Set variables to make header show properly #####
$ADD =					'3';
$hh =					'users';
$LOGast_admin_access =	'1';
$ADMIN =				'admin.php';
$page_width='770';
$section_width='750';
$header_font_size='3';
$subheader_font_size='2';
$subcamp_font_size='2';
$header_selected_bold='<b>';
$header_nonselected_bold='';
$users_color =		'#FFFF99';
$users_font =		'BLACK';
$users_color =		'#E6E6E6';
$subcamp_color =	'#C6C6C6';
##### END Set variables to make header show properly #####

require("admin_header.php");



?>
<TABLE WIDTH=770 BGCOLOR=#E6E6E6 cellpadding=2 cellspacing=0><TR BGCOLOR=#E6E6E6><TD ALIGN=LEFT><FONT FACE="ARIAL,HELVETICA" SIZE=2><B> &nbsp; Statistiques utilisateur for <?php echo $user ?></TD><TD ALIGN=RIGHT><FONT FACE="ARIAL,HELVETICA" SIZE=2> &nbsp; </TD></TR>




<?php 

echo "<TR BGCOLOR=\"#F0F5FE\"><TD ALIGN=LEFT COLSPAN=2><FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2><B> &nbsp; \n";

echo "<form action=$PHP_SELF method=POST name=vicidial_report id=vicidial_report>\n";
echo "<input type=hidden name=DB value=\"$DB\">\n";
echo "<input type=text name=begin_date value=\"$begin_date\" size=10 maxsize=10>";

?>
<script language="JavaScript">
var o_cal = new tcal ({
	// form name
	'formname': 'vicidial_report',
	// input name
	'controlname': 'begin_date'
});
o_cal.a_tpl.yearscroll = false;
// o_cal.a_tpl.weekstart = 1; // Lundi week start
</script>
<?php

echo " to <input type=text name=end_date value=\"$end_date\" size=10 maxsize=10>";

?>
<script language="JavaScript">
var o_cal = new tcal ({
	// form name
	'formname': 'vicidial_report',
	// input name
	'controlname': 'end_date'
});
o_cal.a_tpl.yearscroll = false;
// o_cal.a_tpl.weekstart = 1; // Lundi week start
</script>
<?php

if (strlen($user)>1)
	{echo " &nbsp;<input type=hidden name=user value=\"$user\">\n";}
else
	{echo " &nbsp;<input type=text name=user size=12 maxlength=10>\n";}
echo "<input type=submit name=submit value=submit>\n";


echo " &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; $user - $full_name<BR><BR>\n";

echo "<center>\n";
echo "<a href=\"./AST_agent_time_sheet.php?agent=$user\">Agent Feuille de temps</a>\n";
echo " | <a href=\"./user_status.php?user=$user\">Utilisateur Status</a>\n";
echo " | <a href=\"./admin.php?ADD=3&user=$user\">Modifier un utilisateur</a>\n";
echo " | <a href=\"./AST_agent_days_detail.php?user=$user&query_date=$begin_date&end_date=$end_date&group[]=--ALL--&shift=ALL\">Utilisateur multiple day status detail report</a>";
echo "</center>\n";


echo "</B></TD></TR>\n";
echo "<TR><TD ALIGN=LEFT COLSPAN=2>\n";

echo "<br><center>\n";

##### vicidial agent talk time and status #####

echo "<B>AGENT TEMPS DE CONVERSATION ET ETAT:</B>\n";

echo "<center><TABLE width=300 cellspacing=0 cellpadding=1>\n";
echo "<tr><td><font size=2>ETAT</td><td align=right><font size=2>NOMBRE</td><td align=right><font size=2>HOURS:MM:SS</td></tr>\n";

$stmt="SELECT count(*),status, sum(length_in_sec) from vicidial_log where user='" . mysql_real_escape_string($user) . "' and call_date >= '" . mysql_real_escape_string($begin_date) . " 0:00:01'  and call_date <= '" . mysql_real_escape_string($end_date) . " 23:59:59' group by status order by status";
$rslt=mysql_query($stmt, $link);
$VLstatuses_to_print = mysql_num_rows($rslt);
$total_calls=0;
$o=0;   $p=0;
while ($VLstatuses_to_print > $o) 
	{
	$row=mysql_fetch_row($rslt);
	$counts[$p] =		$row[0];
	$status[$p] =		$row[1];
	$call_sec[$p] =		$row[2];
	$p++;
	$o++;
	}

$stmt="SELECT count(*),status, sum(length_in_sec) from vicidial_closer_log where user='" . mysql_real_escape_string($user) . "' and call_date >= '" . mysql_real_escape_string($begin_date) . " 0:00:01'  and call_date <= '" . mysql_real_escape_string($end_date) . " 23:59:59' group by status order by status";
$rslt=mysql_query($stmt, $link);
$VCLstatuses_to_print = mysql_num_rows($rslt);
$o=0;
while ($VCLstatuses_to_print > $o) 
	{
	$status_match=0;
	$r=0;
	$row=mysql_fetch_row($rslt);
	while ($VLstatuses_to_print > $r) 
		{
		if ($status[$r] == $row[1])
			{
			$counts[$r] = ($counts[$r] + $row[0]);
			$call_sec[$r] = ($call_sec[$r] + $row[2]);
			$status_match++;
			}
		$r++;
		}
	if ($status_match < 1)
		{
		$counts[$p] =		$row[0];
		$status[$p] =		$row[1];
		$call_sec[$p] =		$row[2];
		$VLstatuses_to_print++;
		$p++;
		}
	$o++;
	}

$o=0;
$total_sec=0;
while ($o < $p)
	{
	if (eregi("1$|3$|5$|7$|9$", $o))
		{$bgcolor='bgcolor="#B9CBFD"';} 
	else
		{$bgcolor='bgcolor="#9BB9FB"';}

	$call_hours_minutes =		sec_convert($call_sec[$o],'H'); 

	echo "<tr $bgcolor><td><font size=2>$status[$o]</td>";
	echo "<td align=right><font size=2> $counts[$o]</td>\n";
	echo "<td align=right><font size=2> $call_hours_minutes</td></tr>\n";
	$total_calls = ($total_calls + $counts[$o]);
	$total_sec = ($total_sec + $call_sec[$o]);
	$call_seconds=0;
	$o++;
	}

$call_hours_minutes =		sec_convert($total_sec,'H'); 

echo "<tr><td><font size=2>TOTAL DES APPELS</td><td align=right><font size=2> $total_calls</td><td align=right><font size=2> $call_hours_minutes</td></tr>\n";
echo "</TABLE></center>\n";


##### Login and Logout time from vicidial agent interface #####

echo "<br><br>\n";

echo "<center>\n";

echo "<B>AGENT HEURE DE CONNECTION/DECONNECTION:</B>\n";
echo "<TABLE width=500 cellspacing=0 cellpadding=1>\n";
echo "<tr><td><font size=2>EVENEMENT</td><td align=right><font size=2> DATE</td><td align=right><font size=2> CAMPAGNE</td><td align=right><font size=2> GROUP</td><td align=right><font size=2>HOURS:MM:SS</td></tr>\n";

	$stmt="SELECT event,event_epoch,event_date,campaign_id,user_group from vicidial_user_log where user='" . mysql_real_escape_string($user) . "' and event_date >= '" . mysql_real_escape_string($begin_date) . " 0:00:01'  and event_date <= '" . mysql_real_escape_string($end_date) . " 23:59:59'";
	$rslt=mysql_query($stmt, $link);
	$events_to_print = mysql_num_rows($rslt);

	$total_calls=0;
	$o=0;
	$event_start_seconds='';
	$event_stop_seconds='';
	while ($events_to_print > $o) {
		$row=mysql_fetch_row($rslt);
		if (eregi("LOGIN", $row[0]))
			{$bgcolor='bgcolor="#B9CBFD"';} 
		else
			{$bgcolor='bgcolor="#9BB9FB"';}

		if (ereg("LOGIN", $row[0]))
			{
			$event_start_seconds = $row[1];
			echo "<tr $bgcolor><td><font size=2>$row[0]</td>";
			echo "<td align=right><font size=2> $row[2]</td>\n";
			echo "<td align=right><font size=2> $row[3]</td>\n";
			echo "<td align=right><font size=2> $row[4]</td>\n";
			echo "<td align=right><font size=2> </td></tr>\n";
			}
		if (ereg("LOGOUT", $row[0]))
			{
			if ($event_start_seconds)
				{

				$event_stop_seconds = $row[1];
				$event_seconds = ($event_stop_seconds - $event_start_seconds);
				$total_login_time = ($total_login_time + $event_seconds);
				$event_hours_minutes =		sec_convert($event_seconds,'H'); 

				echo "<tr $bgcolor><td><font size=2>$row[0]</td>";
				echo "<td align=right><font size=2> $row[2]</td>\n";
				echo "<td align=right><font size=2> $row[3]</td>\n";
				echo "<td align=right><font size=2> $row[4]</td>\n";
				echo "<td align=right><font size=2> $event_hours_minutes</td></tr>\n";
				$event_start_seconds='';
				$event_stop_seconds='';
				}
			else
				{
				echo "<tr $bgcolor><td><font size=2>$row[0]</td>";
				echo "<td align=right><font size=2> $row[2]</td>\n";
				echo "<td align=right><font size=2> $row[3]</td>\n";
				echo "<td align=right><font size=2> </td></tr>\n";
				}
			}

		$total_calls = ($total_calls + $row[0]);

		$call_seconds=0;
		$o++;
	}

$total_login_hours_minutes =		sec_convert($total_login_time,'H'); 

echo "<tr><td><font size=2>TOTAL</td>";
echo "<td align=right><font size=2> </td>\n";
echo "<td align=right><font size=2> </td>\n";
echo "<td align=right><font size=2> </td>\n";
echo "<td align=right><font size=2> $total_login_hours_minutes</td></tr>\n";

echo "</TABLE></center>\n";





##### vicidial_timeclock log records for user #####

$total_login_time=0;
$SQday_ARY =	explode('-',$begin_date);
$EQday_ARY =	explode('-',$end_date);
$SQepoch = mktime(0, 0, 0, $SQday_ARY[1], $SQday_ARY[2], $SQday_ARY[0]);
$EQepoch = mktime(23, 59, 59, $EQday_ARY[1], $EQday_ARY[2], $EQday_ARY[0]);

echo "<br><br>\n";

echo "<center>\n";

echo "<B>TIMECLOCK HEURE DE CONNECTION/DECONNECTION:</B>\n";
echo "<TABLE width=550 cellspacing=0 cellpadding=1>\n";
echo "<tr><td><font size=2>ID </td><td><font size=2>EDIT </td><td align=right><font size=2>EVENEMENT</td><td align=right><font size=2> DATE</td><td align=right><font size=2> IP ADDRESS</td><td align=right><font size=2> GROUP</td><td align=right><font size=2>HOURS:MM:SS</td></tr>\n";

	$stmt="SELECT event,event_epoch,user_group,login_sec,ip_address,timeclock_id,manager_user from vicidial_timeclock_log where user='" . mysql_real_escape_string($user) . "' and event_epoch >= '$SQepoch'  and event_epoch <= '$EQepoch';";
	if ($DB>0) {echo "|$stmt|";}
	$rslt=mysql_query($stmt, $link);
	$events_to_print = mysql_num_rows($rslt);

	$total_logs=0;
	$o=0;
	while ($events_to_print > $o) {
		$row=mysql_fetch_row($rslt);
		if ( ($row[0]=='START') or ($row[0]=='LOGIN') )
			{$bgcolor='bgcolor="#B9CBFD"';} 
		else
			{$bgcolor='bgcolor="#9BB9FB"';}

		$TC_log_date = date("Y-m-d H:i:s", $row[1]);

		$manager_edit='';
		if (strlen($row[6])>0) {$manager_edit = ' * ';}

		if (ereg("LOGIN", $row[0]))
			{
			$login_sec='';
			echo "<tr $bgcolor><td><font size=2><A HREF=\"./timeclock_edit.php?timeclock_id=$row[5]\">$row[5]</A></td>";
			echo "<td align=right><font size=2>$manager_edit</td>";
			echo "<td align=right><font size=2>$row[0]</td>";
			echo "<td align=right><font size=2> $TC_log_date</td>\n";
			echo "<td align=right><font size=2> $row[4]</td>\n";
			echo "<td align=right><font size=2> $row[2]</td>\n";
			echo "<td align=right><font size=2> </td></tr>\n";
			}
		if (ereg("LOGOUT", $row[0]))
			{
			$login_sec = $row[3];
			$total_login_time = ($total_login_time + $login_sec);
			$event_hours_minutes =		sec_convert($login_sec,'H'); 

			echo "<tr $bgcolor><td><font size=2><A HREF=\"./timeclock_edit.php?timeclock_id=$row[5]\">$row[5]</A></td>";
			echo "<td align=right><font size=2>$manager_edit</td>";
			echo "<td align=right><font size=2>$row[0]</td>";
			echo "<td align=right><font size=2> $TC_log_date</td>\n";
			echo "<td align=right><font size=2> $row[4]</td>\n";
			echo "<td align=right><font size=2> $row[2]</td>\n";
			echo "<td align=right><font size=2> $event_hours_minutes";
			if ($DB) {echo " - $total_login_time - $login_sec";}
			echo "</td></tr>\n";
			}
		$o++;
	}
if (strlen($login_sec)<1)
	{
	$login_sec = ($STARTtime - $row[1]);
	$total_login_time = ($total_login_time + $login_sec);
		if ($DB) {echo "LOGIN ONLY - $total_login_time - $login_sec";}
	}
$total_login_hours_minutes =		sec_convert($total_login_time,'H'); 

if ($DB) {echo " - $total_login_time - $login_sec";}

echo "<tr><td align=right><font size=2> </td>";
echo "<td align=right><font size=2> </td>\n";
echo "<td align=right><font size=2> </td>\n";
echo "<td align=right><font size=2> </td>\n";
echo "<td align=right><font size=2><font size=2>TOTAL </td>\n";
echo "<td align=right><font size=2> $total_login_hours_minutes  </td></tr>\n";

echo "</TABLE></center>\n";



##### closer in-group selection logs #####

echo "<br><br>\n";

echo "<center>\n";

echo "<B>CLOSER IN-GROUP SELECTION LOGS:</B>\n";
echo "<TABLE width=670 cellspacing=0 cellpadding=1>\n";
echo "<tr><td><font size=1># </td><td><font size=2>DATE/TIME </td><td align=left><font size=2> CAMPAGNE</td><td align=left><font size=2>BLEND</td><td align=left><font size=2> GROUPS</td><td align=left><font size=2> MANAGER</td></tr>\n";

$stmt="select user,campaign_id,event_date,blended,closer_campaigns,manager_change from vicidial_user_closer_log where user='" . mysql_real_escape_string($user) . "' and event_date >= '" . mysql_real_escape_string($begin_date) . " 0:00:01'  and event_date <= '" . mysql_real_escape_string($end_date) . " 23:59:59' order by event_date desc limit 1000;";
$rslt=mysql_query($stmt, $link);
$logs_to_print = mysql_num_rows($rslt);

$u=0;
while ($logs_to_print > $u) 
	{
	$row=mysql_fetch_row($rslt);
	if (eregi("1$|3$|5$|7$|9$", $u))
		{$bgcolor='bgcolor="#B9CBFD"';} 
	else
		{$bgcolor='bgcolor="#9BB9FB"';}

	$u++;
	echo "<tr $bgcolor>";
	echo "<td><font size=1>$u</td>";
	echo "<td><font size=2>$row[2]</td>";
	echo "<td align=left><font size=2> $row[1]</td>\n";
	echo "<td align=left><font size=2> $row[3]</td>\n";
	echo "<td align=left><font size=2> $row[4] </td>\n";
	echo "<td align=left><font size=2> $row[5]</td>\n";
	echo "</tr>\n";
	}


echo "</TABLE><BR><BR>\n";


##### vicidial agent outbound calls for this time period #####

echo "<B>SORTANT CALLS FOR THIS TIME PERIOD: (10000 record limit)</B>\n";
echo "<TABLE width=670 cellspacing=0 cellpadding=1>\n";
echo "<tr><td><font size=1># </td><td><font size=2>DATE/TIME </td><td align=left><font size=2>LENGTH</td><td align=left><font size=2> STATUS</td><td align=left><font size=2> PHONE</td><td align=right><font size=2> CAMPAGNE</td><td align=right><font size=2> GROUP</td><td align=right><font size=2> LIST</td><td align=right><font size=2> LEAD</td><td align=right><font size=2> HANGUP REASON</td></tr>\n";

$stmt="select uniqueid,lead_id,list_id,campaign_id,call_date,start_epoch,end_epoch,length_in_sec,status,phone_code,phone_number,user,comments,processed,user_group,term_reason,alt_dial from vicidial_log where user='" . mysql_real_escape_string($user) . "' and call_date >= '" . mysql_real_escape_string($begin_date) . " 0:00:01'  and call_date <= '" . mysql_real_escape_string($end_date) . " 23:59:59' order by call_date desc limit 10000;";
$rslt=mysql_query($stmt, $link);
$logs_to_print = mysql_num_rows($rslt);

$u=0;
while ($logs_to_print > $u) 
	{
	$row=mysql_fetch_row($rslt);
	if (eregi("1$|3$|5$|7$|9$", $u))
		{$bgcolor='bgcolor="#B9CBFD"';} 
	else
		{$bgcolor='bgcolor="#9BB9FB"';}

	$u++;
	echo "<tr $bgcolor>";
	echo "<td><font size=1>$u</td>";
	echo "<td><font size=2>$row[4]</td>";
	echo "<td align=left><font size=2> $row[7]</td>\n";
	echo "<td align=left><font size=2> $row[8]</td>\n";
	echo "<td align=left><font size=2> $row[10] </td>\n";
	echo "<td align=right><font size=2> $row[3] </td>\n";
	echo "<td align=right><font size=2> $row[14] </td>\n";
	echo "<td align=right><font size=2> $row[2] </td>\n";
	echo "<td align=right><font size=2> <A HREF=\"admin_modify_lead.php?lead_id=$row[1]\" target=\"_blank\">$row[1]</A> </td>\n";
	echo "<td align=right><font size=2> $row[15] </td></tr>\n";
	}


echo "</TABLE><BR><BR>\n";


##### vicidial agent inbound calls for this time period #####

echo "<B>ENTRANT/CLOSER CALLS FOR THIS TIME PERIOD: (10000 record limit)</B>\n";
echo "<TABLE width=750 cellspacing=0 cellpadding=1>\n";
echo "<tr><td><font size=1># </td><td><font size=2>DATE/TIME </td><td align=left><font size=2>LENGTH</td><td align=left><font size=2> STATUS</td><td align=left><font size=2> PHONE</td><td align=right><font size=2> CAMPAGNE</td><td align=right><font size=2> WAIT (S)</td><td align=right><font size=2> AGENT (S)</td><td align=right><font size=2> LIST</td><td align=right><font size=2> LEAD</td><td align=right><font size=2> HANGUP REASON</td></tr>\n";

$stmt="select call_date,length_in_sec,status,phone_number,campaign_id,queue_seconds,list_id,lead_id,term_reason from vicidial_closer_log where user='" . mysql_real_escape_string($user) . "' and call_date >= '" . mysql_real_escape_string($begin_date) . " 0:00:01'  and call_date <= '" . mysql_real_escape_string($end_date) . " 23:59:59' order by call_date desc limit 10000;";
$rslt=mysql_query($stmt, $link);
$logs_to_print = mysql_num_rows($rslt);

$u=0;
$TOTALinSECONDS=0;
$TOTALagentSECONDS=0;
while ($logs_to_print > $u) 
	{
	$row=mysql_fetch_row($rslt);
	if (eregi("1$|3$|5$|7$|9$", $u))
		{$bgcolor='bgcolor="#B9CBFD"';} 
	else
		{$bgcolor='bgcolor="#9BB9FB"';}

	$TOTALinSECONDS = ($TOTALinSECONDS + $row[1]);
	$AGENTseconds = ($row[1] - $row[5]);
	if ($AGENTseconds < 0)
		{$AGENTseconds=0;}

	$TOTALagentSECONDS = ($TOTALagentSECONDS + $AGENTseconds);

	$u++;
	echo "<tr $bgcolor>";
	echo "<td><font size=1>$u</td>";
	echo "<td><font size=2>$row[0]</td>";
	echo "<td align=left><font size=2> $row[1]</td>\n";
	echo "<td align=left><font size=2> $row[2]</td>\n";
	echo "<td align=left><font size=2> $row[3] </td>\n";
	echo "<td align=right><font size=2> $row[4] </td>\n";
	echo "<td align=right><font size=2> $row[5] </td>\n";
	echo "<td align=right><font size=2> $AGENTseconds </td>\n";
	echo "<td align=right><font size=2> $row[6] </td>\n";
	echo "<td align=right><font size=2> <A HREF=\"admin_modify_lead.php?lead_id=$row[7]\" target=\"_blank\">$row[7]</A> </td>\n";
	echo "<td align=right><font size=2> $row[8] </td></tr>\n";
	}

echo "<tr bgcolor=white>";
echo "<td colspan=2><font size=2>TOTALS</td>";
echo "<td align=left><font size=2> $TOTALinSECONDS</td>\n";
echo "<td colspan=4><font size=2> &nbsp; </td>\n";
echo "<td align=right><font size=2> $TOTALagentSECONDS</td>\n";
echo "<td colspan=3><font size=2> &nbsp; </td></tr>\n";
echo "</TABLE></center><BR><BR>\n";


##### vicidial agent activity records for this time period #####
echo "<B>AGENT ACTIVITY FOR THIS TIME PERIOD: (10000 record limit)</B>\n";
echo "<TABLE width=750 cellspacing=0 cellpadding=1>\n";
echo "<tr><td colspan=2><font size=1> &nbsp; </td><td colspan=6 align=center bgcolor=white><font size=1>these fields are in seconds </td><td colspan=4><font size=1> &nbsp; </td></tr>\n";
echo "<tr><td><font size=1># </td><td><font size=2>DATE/TIME </td><td align=left><font size=2>PAUSE</td><td align=left><font size=2> WAIT</td><td align=left><font size=2> TALK</td><td align=right><font size=2> DISPO</td><td align=right><font size=2> DEAD</td><td align=right><font size=2> CUSTOMER</td><td align=right><font size=2> STATUS</td><td align=right><font size=2> LEAD</td><td align=right><font size=2> CAMPAGNE</td><td align=right><font size=2> PAUSE CODE</td></tr>\n";

$stmt="select event_time,lead_id,campaign_id,pause_sec,wait_sec,talk_sec,dispo_sec,dead_sec,status,sub_status,user_group from vicidial_agent_log where user='" . mysql_real_escape_string($user) . "' and event_time >= '" . mysql_real_escape_string($begin_date) . " 0:00:01'  and event_time <= '" . mysql_real_escape_string($end_date) . " 23:59:59' and ( (pause_sec > 0) or (wait_sec > 0) or (talk_sec > 0) or (dispo_sec > 0) ) order by event_time desc limit 10000;";
$rslt=mysql_query($stmt, $link);
$logs_to_print = mysql_num_rows($rslt);

$u=0;
$TOTALpauseSECONDS=0;
$TOTALwaitSECONDS=0;
$TOTALtalkSECONDS=0;
$TOTALdispoSECONDS=0;
$TOTALdeadSECONDS=0;
$TOTALcustomerSECONDS=0;
while ($logs_to_print > $u) 
	{
	$row=mysql_fetch_row($rslt);
	$event_time =	$row[0];
	$lead_id =		$row[1];
	$campaign_id =	$row[2];
	$pause_sec =	$row[3];
	$wait_sec =		$row[4];
	$talk_sec =		$row[5];
	$dispo_sec =	$row[6];
	$dead_sec =		$row[7];
	$status =		$row[8];
	$pause_code =	$row[9];
	$user_group =	$row[10];
	$customer_sec = ($talk_sec - $dead_sec);
	if ($customer_sec < 0)
		{$customer_sec=0;}

	if (eregi("1$|3$|5$|7$|9$", $u))
		{$bgcolor='bgcolor="#B9CBFD"';}
	else
		{$bgcolor='bgcolor="#9BB9FB"';}

	$TOTALpauseSECONDS = ($TOTALpauseSECONDS + $pause_sec);
	$TOTALwaitSECONDS = ($TOTALwaitSECONDS + $wait_sec);
	$TOTALtalkSECONDS = ($TOTALtalkSECONDS + $talk_sec);
	$TOTALdispoSECONDS = ($TOTALdispoSECONDS + $dispo_sec);
	$TOTALdeadSECONDS = ($TOTALdeadSECONDS + $dead_sec);
	$TOTALcustomerSECONDS = ($TOTALcustomerSECONDS + $customer_sec);


	$u++;
	echo "<tr $bgcolor>";
	echo "<td><font size=1>$u</td>";
	echo "<td><font size=2>$event_time</td>";
	echo "<td align=right><font size=2> $pause_sec</td>\n";
	echo "<td align=right><font size=2> $wait_sec</td>\n";
	echo "<td align=right><font size=2> $talk_sec </td>\n";
	echo "<td align=right><font size=2> $dispo_sec </td>\n";
	echo "<td align=right><font size=2> $dead_sec </td>\n";
	echo "<td align=right><font size=2> $customer_sec </td>\n";
	echo "<td align=right><font size=2> $status </td>\n";
	echo "<td align=right><font size=2> <A HREF=\"admin_modify_lead.php?lead_id=$lead_id\" target=\"_blank\">$lead_id</A> </td>\n";
	echo "<td align=right><font size=2> $campaign_id </td>\n";
	echo "<td align=right><font size=2> $pause_code </td></tr>\n";
	}

echo "<tr bgcolor=white>";
echo "<td colspan=2><font size=2>TOTALS</td>";
echo "<td align=right><font size=2> $TOTALpauseSECONDS</td>\n";
echo "<td align=right><font size=2> $TOTALwaitSECONDS</td>\n";
echo "<td align=right><font size=2> $TOTALtalkSECONDS</td>\n";
echo "<td align=right><font size=2> $TOTALdispoSECONDS</td>\n";
echo "<td align=right><font size=2> $TOTALdeadSECONDS</td>\n";
echo "<td align=right><font size=2> $TOTALcustomerSECONDS</td>\n";
echo "<td colspan=4><font size=2> &nbsp; </td></tr>\n";

$TOTALpauseSECONDShh =	sec_convert($TOTALpauseSECONDS,'H'); 
$TOTALwaitSECONDShh =	sec_convert($TOTALwaitSECONDS,'H'); 
$TOTALtalkSECONDShh =	sec_convert($TOTALtalkSECONDS,'H'); 
$TOTALdispoSECONDShh =	sec_convert($TOTALdispoSECONDS,'H'); 
$TOTALdeadSECONDShh =	sec_convert($TOTALdeadSECONDS,'H'); 
$TOTALcustomerSECONDShh =	sec_convert($TOTALcustomerSECONDS,'H'); 

echo "<tr bgcolor=white>";
echo "<td colspan=2><font size=1>(in HH:MM:SS)</td>";
echo "<td align=right><font size=2> $TOTALpauseSECONDShh</td>\n";
echo "<td align=right><font size=2> $TOTALwaitSECONDShh</td>\n";
echo "<td align=right><font size=2> $TOTALtalkSECONDShh</td>\n";
echo "<td align=right><font size=2> $TOTALdispoSECONDShh</td>\n";
echo "<td align=right><font size=2> $TOTALdeadSECONDShh</td>\n";
echo "<td align=right><font size=2> $TOTALcustomerSECONDShh</td>\n";
echo "<td colspan=4><font size=2> &nbsp; </td></tr>\n";

echo "</TABLE></center><BR><BR>\n";



##### vicidial recordings for this time period #####

echo "<B>RECORDINGS FOR THIS TIME PERIOD: (10000 record limit)</B>\n";
echo "<TABLE width=750 cellspacing=0 cellpadding=1>\n";
echo "<tr><td><font size=1># </td><td align=left><font size=2> LEAD</td><td><font size=2>DATE/TIME </td><td align=left><font size=2>SECONDS </td><td align=left><font size=2> &nbsp; RECID</td><td align=center><font size=2>FILENAME</td><td align=center><font size=2>LOCATION &nbsp; </td></tr>\n";

	$stmt="select recording_id,channel,server_ip,extension,start_time,start_epoch,end_time,end_epoch,length_in_sec,length_in_min,filename,location,lead_id,user,vicidial_id from recording_log where user='" . mysql_real_escape_string($user) . "' and start_time >= '" . mysql_real_escape_string($begin_date) . " 0:00:01'  and start_time <= '" . mysql_real_escape_string($end_date) . " 23:59:59' order by recording_id desc limit 10000;";
	$rslt=mysql_query($stmt, $link);
	$logs_to_print = mysql_num_rows($rslt);

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
		echo "<td align=left><font size=2> <A HREF=\"admin_modify_lead.php?lead_id=$row[12]\" target=\"_blank\">$row[12]</A> </td>";
		echo "<td align=left><font size=2> $row[4] </td>\n";
		echo "<td align=left><font size=2> $row[8] </td>\n";
		echo "<td align=left><font size=2> $row[0] </td>\n";
		echo "<td align=center><font size=2> $row[10] </td>\n";
		echo "<td align=right><font size=2> $location &nbsp; </td>\n";
		echo "</tr>\n";

		}


echo "</TABLE><BR><BR>\n";


##### vicidial agent outbound user manual calls for this time period #####

echo "<B>MANUAL SORTANT CALLS FOR THIS TIME PERIOD: (10000 record limit)</B>\n";
echo "<TABLE width=750 cellspacing=0 cellpadding=1>\n";
echo "<tr><td><font size=1># </td><td><font size=2>DATE/TIME </td><td align=left><font size=2> CALL TYPE</td><td align=left><font size=2> SERVER</td><td align=left><font size=2> PHONE</td><td align=right><font size=2> DIALED</td><td align=right><font size=2> LEAD</td><td align=right><font size=2> CALLERID</td><td align=right><font size=2> ALIAS</td></tr>\n";

	$stmt="select call_date,call_type,server_ip,phone_number,number_dialed,lead_id,callerid,group_alias_id from user_call_log where user='" . mysql_real_escape_string($user) . "' and call_date >= '" . mysql_real_escape_string($begin_date) . " 0:00:01'  and call_date <= '" . mysql_real_escape_string($end_date) . " 23:59:59' order by call_date desc limit 10000;";
	$rslt=mysql_query($stmt, $link);
	$logs_to_print = mysql_num_rows($rslt);

	$u=0;
	while ($logs_to_print > $u) {
		$row=mysql_fetch_row($rslt);
		if (eregi("1$|3$|5$|7$|9$", $u))
			{$bgcolor='bgcolor="#B9CBFD"';} 
		else
			{$bgcolor='bgcolor="#9BB9FB"';}

			$u++;
			echo "<tr $bgcolor>";
			echo "<td><font size=1>$u</td>";
			echo "<td><font size=2>$row[0]</td>";
			echo "<td align=left><font size=2> $row[1]</td>\n";
			echo "<td align=left><font size=2> $row[2]</td>\n";
			echo "<td align=left><font size=2> $row[3] </td>\n";
			echo "<td align=right><font size=2> $row[4] </td>\n";
			echo "<td align=right><font size=2> <A HREF=\"admin_modify_lead.php?lead_id=$row[5]\" target=\"_blank\">$row[5]</A> </td>\n";
			echo "<td align=right><font size=2> $row[6] </td>\n";
			echo "<td align=right><font size=2> $row[7] </td></tr>\n";

	}


echo "</TABLE><BR><BR>\n";



$ENDtime = date("U");

$RUNtime = ($ENDtime - $STARTtime);

echo "\n\n\n<br><br><br>\n\n";


echo "<font size=0>\n\n\n<br><br><br>\ntemps d'exécution du script: $RUNtime seconds</font>";


?>


</TD></TR><TABLE>
</body>
</html>

<?php
	
exit; 



?>





