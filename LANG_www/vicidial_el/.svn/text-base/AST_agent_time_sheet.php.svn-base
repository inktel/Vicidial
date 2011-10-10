<?php 
# AST_agent_time_sheet.php
# 
# Copyright (C) 2009  Matt Florell <vicidial@gmail.com>    LICENSE: AGPLv2
#
# CHANGES
#
# 60619-1729 - Added variable filtering to eliminate SQL injection attack threat
#            - Added required user/pass to gain access to this page
# 80624-0132 - Added vicidial_timeclock entries
# 90310-0745 - Added admin header
# 90508-0644 - Changed to PHP long tags
# 90524-2231 - Changed to use functions.php for seconds to HH:MM:SS conversion
#

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
if (isset($_GET["agent"]))				{$agent=$_GET["agent"];}
	elseif (isset($_POST["agent"]))		{$agent=$_POST["agent"];}
if (isset($_GET["query_date"]))				{$query_date=$_GET["query_date"];}
	elseif (isset($_POST["query_date"]))	{$query_date=$_POST["query_date"];}
if (isset($_GET["calls_summary"]))			{$calls_summary=$_GET["calls_summary"];}
	elseif (isset($_POST["calls_summary"]))	{$calls_summary=$_POST["calls_summary"];}
if (isset($_GET["submit"]))				{$submit=$_GET["submit"];}
	elseif (isset($_POST["submit"]))	{$submit=$_POST["submit"];}
if (isset($_GET["ΕΠΙΒΕΒΑΙΩΣΗ"]))				{$ΕΠΙΒΕΒΑΙΩΣΗ=$_GET["ΕΠΙΒΕΒΑΙΩΣΗ"];}
	elseif (isset($_POST["ΕΠΙΒΕΒΑΙΩΣΗ"]))	{$ΕΠΙΒΕΒΑΙΩΣΗ=$_POST["ΕΠΙΒΕΒΑΙΩΣΗ"];}

$user=$agent;

$PHP_AUTH_USER = ereg_replace("[^0-9a-zA-Z]","",$PHP_AUTH_USER);
$PHP_AUTH_PW = ereg_replace("[^0-9a-zA-Z]","",$PHP_AUTH_PW);

$stmt="SELECT count(*) from vicidial_users where user='$PHP_AUTH_USER' and pass='$PHP_AUTH_PW' and user_level > 6 and view_reports='1';";
if ($DB) {echo "|$stmt|\n";}
if ($non_latin > 0) { $rslt=mysql_query("SET NAMES 'UTF8'");}
$rslt=mysql_query($stmt, $link);
$row=mysql_fetch_row($rslt);
$auth=$row[0];

  if( (strlen($PHP_AUTH_USER)<2) or (strlen($PHP_AUTH_PW)<2) or (!$auth))
	{
    Header("WWW-Authenticate: Basic realm=\"VICI-PROJECTS\"");
    Header("HTTP/1.0 401 Unauthorized");
    echo "Ακυρο Ονομα Χρήστη/Κωδικός Πρόσβασης: |$PHP_AUTH_USER|$PHP_AUTH_PW|\n";
    exit;
	}

$NOW_DATE = date("Y-m-d");
$NOW_TIME = date("Y-m-d H:i:s");
$STARTtime = date("U");
if (!isset($query_date)) {$query_date = $NOW_DATE;}

?>

<HTML>
<HEAD>
<STYLE type="text/css">
<!--
   .green {color: white; background-color: green}
   .red {color: white; background-color: red}
   .blue {color: white; background-color: blue}
   .purple {color: white; background-color: purple}
-->
 </STYLE>

<?php 
echo "<META HTTP-EQUIV=\"Content-Type\" CONTENT=\"text/html; charset=utf-8\">\n";
echo "<TITLE>Agent Ώρα Φύλλο";


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

echo "<TABLE WIDTH=$page_width BGCOLOR=\"#F0F5FE\" cellpadding=2 cellspacing=0><TR BGCOLOR=\"#F0F5FE\"><TD>\n";

echo "AgentΏρα Φύλλοfor: $user\n";
echo "<BR>\n";
echo "<FORM ACTION=\"$PHP_SELF\" METHOD=GET> &nbsp; \n";
echo "Date: <INPUT TYPE=TEXT NAME=query_date SIZE=19 MAXLENGTH=19 VALUE=\"$query_date\">\n";
echo "Χρήστης ID: <INPUT TYPE=TEXT NAME=agent SIZE=10 MAXLENGTH=20 VALUE=\"$agent\">\n";
echo "<INPUT TYPE=Submit NAME=ΕΠΙΒΕΒΑΙΩΣΗ VALUE=ΥΠΟΒΑΛΛΩ>\n";
echo "</FORM>\n\n";

echo "<PRE><FONT SIZE=3>\n";


if (!$agent)
{
echo "\n";
echo "PLEASE SELECT AN ΧΕΙΡΙΣΤΗΣID AND DATE-TIME ABOVE AND CLICK ΕΠΙΒΕΒΑΙΩΣΗ\n";
echo " NOTE: stats taken from available agent log data\n";
}

else
{
$query_date_BEGIN = "$query_date 00:00:00";   
$query_date_END = "$query_date 23:59:59";
$time_BEGIN = "00:00:00";   
$time_END = "23:59:59";

$stmt="select full_name from vicidial_users where user='$agent';";
$rslt=mysql_query($stmt, $link);
if ($DB) {echo "$stmt\n";}
$row=mysql_fetch_row($rslt);
$full_name = $row[0];

echo "AgentΏρα Φύλλο                            $NOW_TIME\n";

echo "Time range: $query_date_BEGIN to $query_date_END\n\n";
echo "---------- ΧΕΙΡΙΣΤΗΣTIME SHEET: $agent - $full_name -------------\n\n";

if ($calls_summary)
	{
	$stmt="select count(*) as calls,sum(talk_sec) as talk,avg(talk_sec),sum(pause_sec),avg(pause_sec),sum(wait_sec),avg(wait_sec),sum(dispo_sec),avg(dispo_sec) from vicidial_agent_log where event_time <= '" . mysql_real_escape_string($query_date_END) . "' and event_time >= '" . mysql_real_escape_string($query_date_BEGIN) . "' and user='" . mysql_real_escape_string($agent) . "' and pause_sec<48800 and wait_sec<48800 and talk_sec<48800 and dispo_sec<48800 limit 1;";
	$rslt=mysql_query($stmt, $link);
	if ($DB) {echo "$stmt\n";}
	$row=mysql_fetch_row($rslt);

	$TOTAL_TIME = ($row[1] + $row[3] + $row[5] + $row[7]);

	$TOTAL_TIME_HMS =		sec_convert($TOTAL_TIME,'H'); 
	$TALK_TIME_HMS =		sec_convert($row[1],'H'); 
	$PAUSE_TIME_HMS =		sec_convert($row[3],'H'); 
	$WAIT_TIME_HMS =		sec_convert($row[5],'H'); 
	$WRAPUP_TIME_HMS =		sec_convert($row[7],'H'); 
	$TALK_AVG_MS =			sec_convert($row[2],'H'); 
	$PAUSE_AVG_MS =			sec_convert($row[4],'H'); 
	$WAIT_AVG_MS =			sec_convert($row[6],'H'); 
	$WRAPUP_AVG_MS =		sec_convert($row[8],'H'); 

	$pfTOTAL_TIME_HMS =		sprintf("%8s", $TOTAL_TIME_HMS);
	$pfTALK_TIME_HMS =		sprintf("%8s", $TALK_TIME_HMS);
	$pfPAUSE_TIME_HMS =		sprintf("%8s", $PAUSE_TIME_HMS);
	$pfWAIT_TIME_HMS =		sprintf("%8s", $WAIT_TIME_HMS);
	$pfWRAPUP_TIME_HMS =	sprintf("%8s", $WRAPUP_TIME_HMS);
	$pfTALK_AVG_MS =		sprintf("%6s", $TALK_AVG_MS);
	$pfPAUSE_AVG_MS =		sprintf("%6s", $PAUSE_AVG_MS);
	$pfWAIT_AVG_MS =		sprintf("%6s", $WAIT_AVG_MS);
	$pfWRAPUP_AVG_MS =		sprintf("%6s", $WRAPUP_AVG_MS);

	echo "ΣΥΝΟΛΙΚΕΣ ΚΛΗΣΕΙΣTAKEN: $row[0]\n";
	echo "TALK TIME:               $pfTALK_TIME_HMS     AVERAGE: $pfTALK_AVG_MS\n";
	echo "PAUSE TIME:              $pfPAUSE_TIME_HMS     AVERAGE: $pfPAUSE_AVG_MS\n";
	echo "WAIT TIME:               $pfWAIT_TIME_HMS     AVERAGE: $pfWAIT_AVG_MS\n";
	echo "WRAPUP TIME:             $pfWRAPUP_TIME_HMS     AVERAGE: $pfWRAPUP_AVG_MS\n";
	echo "----------------------------------------------------------------\n";
	echo "TOTAL ACTIVE ΧΕΙΡΙΣΤΗΣTIME: $pfTOTAL_TIME_HMS\n";

	echo "\n";
	}
else
	{
	echo "<a href=\"$PHP_SELF?calls_summary=1&agent=$agent&query_date=$query_date\">Call Activity Summary</a>\n\n";

	}

$stmt="select event_time,UNIX_TIMESTAMP(event_time) from vicidial_agent_log where event_time <= '" . mysql_real_escape_string($query_date_END) . "' and event_time >= '" . mysql_real_escape_string($query_date_BEGIN) . "' and user='" . mysql_real_escape_string($agent) . "' order by event_time limit 1;";
$rslt=mysql_query($stmt, $link);
if ($DB) {echo "$stmt\n";}
$row=mysql_fetch_row($rslt);

echo "FIRST LOGIN:          $row[0]\n";
$start = $row[1];

$stmt="select event_time,UNIX_TIMESTAMP(event_time) from vicidial_agent_log where event_time <= '" . mysql_real_escape_string($query_date_END) . "' and event_time >= '" . mysql_real_escape_string($query_date_BEGIN) . "' and user='" . mysql_real_escape_string($agent) . "' order by event_time desc limit 1;";
$rslt=mysql_query($stmt, $link);
if ($DB) {echo "$stmt\n";}
$row=mysql_fetch_row($rslt);

echo "LAST LOG ACTIVITY:    $row[0]\n";
$end = $row[1];

$login_time = ($end - $start);
$LOGIN_TIME_HMS =		sec_convert($login_time,'H'); 
$pfLOGIN_TIME_HMS =		sprintf("%8s", $LOGIN_TIME_HMS);

echo "-----------------------------------------\n";
echo "TOTAL LOGGED-IN TIME:            $pfLOGIN_TIME_HMS\n";


### timeclock records


##### vicidial_timeclock log records for user #####

$total_login_time=0;
$SQday_ARY =	explode('-',$query_date_BEGIN);
$EQday_ARY =	explode('-',$query_date_END);
$SQepoch = mktime(0, 0, 0, $SQday_ARY[1], $SQday_ARY[2], $SQday_ARY[0]);
$EQepoch = mktime(23, 59, 59, $EQday_ARY[1], $EQday_ARY[2], $EQday_ARY[0]);

echo "\n";

echo "<B>TIMECLOCK  ΧΡΟΝΟΣ ΣYΝΔΕΣΗΣ/ΑΠΟΣΥΝΔΕΣΗΣ:</B>\n";
echo "<TABLE width=550 cellspacing=0 cellpadding=1>\n";
echo "<tr><td><font size=2>ID </td><td><font size=2>EDIT </td><td align=right><font size=2 >ΣΥΜΒΑΝ</td><td align=right><font size=2> ΗΜΕΡΑ</td><td align=right><font size=2> IP ADDRESS</td><td align=right><font size=2> GROUP</td><td align=right><font size=2>ΩΡΕΣ:ΛΕΠΤΑ</td></tr>\n";

	$stmt="SELECT event,event_epoch,user_group,login_sec,ip_address,timeclock_id,manager_user from vicidial_timeclock_log where user='$agent' and event_epoch >= '$SQepoch'  and event_epoch <= '$EQepoch';";
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
echo "<td align=right colspan=2><font size=2><font size=2>TOTAL </td>\n";
echo "<td align=right><font size=2> $total_login_hours_minutes  </td></tr>\n";

echo "</TABLE>\n";



}



?>

</BODY></HTML>
