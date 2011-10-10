<?php 
# AST_agent_time_sheet_archive.php
# 
# Copyright (C) 2009  Matt Florell <vicidial@gmail.com>    LICENSE: AGPLv2
#
# CHANGES
#
# 60619-1721 - Added variable filtering to eliminate SQL injection attack threat
#            - Added required user/pass to gain access to this page
# 90508-0644 - Changed to PHP long tags
#

require("dbconnect.php");

$PHP_AUTH_USER=$_SERVER['PHP_AUTH_USER'];
$PHP_AUTH_PW=$_SERVER['PHP_AUTH_PW'];
$PHP_SELF=$_SERVER['PHP_SELF'];
if (isset($_GET["agent"]))				{$agent=$_GET["agent"];}
	elseif (isset($_POST["agent"]))		{$agent=$_POST["agent"];}
if (isset($_GET["query_date"]))				{$query_date=$_GET["query_date"];}
	elseif (isset($_POST["query_date"]))		{$query_date=$_POST["query_date"];}
if (isset($_GET["calls_summary"]))				{$calls_summary=$_GET["calls_summary"];}
	elseif (isset($_POST["calls_summary"]))		{$calls_summary=$_POST["calls_summary"];}
if (isset($_GET["submit"]))				{$submit=$_GET["submit"];}
	elseif (isset($_POST["submit"]))		{$submit=$_POST["submit"];}
if (isset($_GET["SUBMIT"]))				{$SUBMIT=$_GET["SUBMIT"];}
	elseif (isset($_POST["SUBMIT"]))		{$SUBMIT=$_POST["SUBMIT"];}

$PHP_AUTH_USER = ereg_replace("[^0-9a-zA-Z]","",$PHP_AUTH_USER);
$PHP_AUTH_PW = ereg_replace("[^0-9a-zA-Z]","",$PHP_AUTH_PW);

	$stmt="SELECT count(*) from vicidial_users where user='$PHP_AUTH_USER' and pass='$PHP_AUTH_PW' and user_level > 6 and view_reports='1';";
	if ($DB) {echo "|$stmt|\n";}
	$rslt=mysql_query($stmt, $link);
	$row=mysql_fetch_row($rslt);
	$auth=$row[0];

  if( (strlen($PHP_AUTH_USER)<2) or (strlen($PHP_AUTH_PW)<2) or (!$auth))
	{
    Header("WWW-Authenticate: Basic realm=\"VICI-PROJECTS\"");
    Header("HTTP/1.0 401 Unauthorized");
    echo "Invalid Username/Password: |$PHP_AUTH_USER|$PHP_AUTH_PW|\n";
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
echo "<TITLE>VICIDIAL: Agent Time Sheet</TITLE></HEAD><BODY BGCOLOR=WHITE>\n";
echo "<a href=\"./admin.php\">VICIDIAL ADMIN</a>: Agent Time Sheet\n";
echo " - <a href=\"./user_stats.php?user=$agent\">User Stats</a>\n";
echo " - <a href=\"./user_status.php?user=$agent\">User Status</a>\n";
echo " - <a href=\"./admin.php?ADD=3&user=$agent\">Modify User</a>\n";
echo "<BR>\n";
echo "<FORM ACTION=\"$PHP_SELF\" METHOD=GET> &nbsp; \n";
echo "Date: <INPUT TYPE=TEXT NAME=query_date SIZE=19 MAXLENGTH=19 VALUE=\"$query_date\">\n";
echo "User ID: <INPUT TYPE=TEXT NAME=agent SIZE=10 MAXLENGTH=20 VALUE=\"$agent\">\n";
echo "<INPUT TYPE=SUBMIT NAME=SUBMIT VALUE=SUBMIT>\n";
echo "</FORM>\n\n";

echo "<PRE><FONT SIZE=3>\n";


if (!$agent)
{
echo "\n";
echo "PLEASE SELECT AN AGENT ID AND DATE-TIME ABOVE AND CLICK SUBMIT\n";
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

echo "VICIDIAL: Agent Time Sheet                             $NOW_TIME\n";

echo "Time range: $query_date_BEGIN to $query_date_END\n\n";
echo "---------- AGENT TIME SHEET: $agent - $full_name -------------\n\n";

if ($calls_summary)
	{
	$stmt="select count(*) as calls,sum(talk_sec) as talk,avg(talk_sec),sum(pause_sec),avg(pause_sec),sum(wait_sec),avg(wait_sec),sum(dispo_sec),avg(dispo_sec) from vicidial_agent_log_archive where event_time <= '" . mysql_real_escape_string($query_date_END) . "' and event_time >= '" . mysql_real_escape_string($query_date_BEGIN) . "' and user='" . mysql_real_escape_string($agent) . "' and pause_sec<48800 and wait_sec<48800 and talk_sec<48800 and dispo_sec<48800 limit 1;";
	$rslt=mysql_query($stmt, $link);
	if ($DB) {echo "$stmt\n";}
	$row=mysql_fetch_row($rslt);

	$TOTAL_TIME = ($row[1] + $row[3] + $row[5] + $row[7]);

		$TOTAL_TIME_H = ($TOTAL_TIME / 3600);
		$TOTAL_TIME_H = round($TOTAL_TIME_H, 2);
		$TOTAL_TIME_H_int = intval("$TOTAL_TIME_H");
		$TOTAL_TIME_M = ($TOTAL_TIME_H - $TOTAL_TIME_H_int);
		$TOTAL_TIME_M = ($TOTAL_TIME_M * 60);
		$TOTAL_TIME_M = round($TOTAL_TIME_M, 2);
		$TOTAL_TIME_M_int = intval("$TOTAL_TIME_M");
		$TOTAL_TIME_S = ($TOTAL_TIME_M - $TOTAL_TIME_M_int);
		$TOTAL_TIME_S = ($TOTAL_TIME_S * 60);
		$TOTAL_TIME_S = round($TOTAL_TIME_S, 0);
		if ($TOTAL_TIME_S < 10) {$TOTAL_TIME_S = "0$TOTAL_TIME_S";}
		if ($TOTAL_TIME_M_int < 10) {$TOTAL_TIME_M_int = "0$TOTAL_TIME_M_int";}
		$TOTAL_TIME_HMS = "$TOTAL_TIME_H_int:$TOTAL_TIME_M_int:$TOTAL_TIME_S";
		$pfTOTAL_TIME_HMS =		sprintf("%8s", $TOTAL_TIME_HMS);

		$TALK_TIME_H = ($row[1] / 3600);
		$TALK_TIME_H = round($TALK_TIME_H, 2);
		$TALK_TIME_H_int = intval("$TALK_TIME_H");
		$TALK_TIME_M = ($TALK_TIME_H - $TALK_TIME_H_int);
		$TALK_TIME_M = ($TALK_TIME_M * 60);
		$TALK_TIME_M = round($TALK_TIME_M, 2);
		$TALK_TIME_M_int = intval("$TALK_TIME_M");
		$TALK_TIME_S = ($TALK_TIME_M - $TALK_TIME_M_int);
		$TALK_TIME_S = ($TALK_TIME_S * 60);
		$TALK_TIME_S = round($TALK_TIME_S, 0);
		if ($TALK_TIME_S < 10) {$TALK_TIME_S = "0$TALK_TIME_S";}
		if ($TALK_TIME_M_int < 10) {$TALK_TIME_M_int = "0$TALK_TIME_M_int";}
		$TALK_TIME_HMS = "$TALK_TIME_H_int:$TALK_TIME_M_int:$TALK_TIME_S";
		$pfTALK_TIME_HMS =		sprintf("%8s", $TALK_TIME_HMS);

		$PAUSE_TIME_H = ($row[3] / 3600);
		$PAUSE_TIME_H = round($PAUSE_TIME_H, 2);
		$PAUSE_TIME_H_int = intval("$PAUSE_TIME_H");
		$PAUSE_TIME_M = ($PAUSE_TIME_H - $PAUSE_TIME_H_int);
		$PAUSE_TIME_M = ($PAUSE_TIME_M * 60);
		$PAUSE_TIME_M = round($PAUSE_TIME_M, 2);
		$PAUSE_TIME_M_int = intval("$PAUSE_TIME_M");
		$PAUSE_TIME_S = ($PAUSE_TIME_M - $PAUSE_TIME_M_int);
		$PAUSE_TIME_S = ($PAUSE_TIME_S * 60);
		$PAUSE_TIME_S = round($PAUSE_TIME_S, 0);
		if ($PAUSE_TIME_S < 10) {$PAUSE_TIME_S = "0$PAUSE_TIME_S";}
		if ($PAUSE_TIME_M_int < 10) {$PAUSE_TIME_M_int = "0$PAUSE_TIME_M_int";}
		$PAUSE_TIME_HMS = "$PAUSE_TIME_H_int:$PAUSE_TIME_M_int:$PAUSE_TIME_S";
		$pfPAUSE_TIME_HMS =		sprintf("%8s", $PAUSE_TIME_HMS);

		$WAIT_TIME_H = ($row[5] / 3600);
		$WAIT_TIME_H = round($WAIT_TIME_H, 2);
		$WAIT_TIME_H_int = intval("$WAIT_TIME_H");
		$WAIT_TIME_M = ($WAIT_TIME_H - $WAIT_TIME_H_int);
		$WAIT_TIME_M = ($WAIT_TIME_M * 60);
		$WAIT_TIME_M = round($WAIT_TIME_M, 2);
		$WAIT_TIME_M_int = intval("$WAIT_TIME_M");
		$WAIT_TIME_S = ($WAIT_TIME_M - $WAIT_TIME_M_int);
		$WAIT_TIME_S = ($WAIT_TIME_S * 60);
		$WAIT_TIME_S = round($WAIT_TIME_S, 0);
		if ($WAIT_TIME_S < 10) {$WAIT_TIME_S = "0$WAIT_TIME_S";}
		if ($WAIT_TIME_M_int < 10) {$WAIT_TIME_M_int = "0$WAIT_TIME_M_int";}
		$WAIT_TIME_HMS = "$WAIT_TIME_H_int:$WAIT_TIME_M_int:$WAIT_TIME_S";
		$pfWAIT_TIME_HMS =		sprintf("%8s", $WAIT_TIME_HMS);

		$WRAPUP_TIME_H = ($row[7] / 3600);
		$WRAPUP_TIME_H = round($WRAPUP_TIME_H, 2);
		$WRAPUP_TIME_H_int = intval("$WRAPUP_TIME_H");
		$WRAPUP_TIME_M = ($WRAPUP_TIME_H - $WRAPUP_TIME_H_int);
		$WRAPUP_TIME_M = ($WRAPUP_TIME_M * 60);
		$WRAPUP_TIME_M = round($WRAPUP_TIME_M, 2);
		$WRAPUP_TIME_M_int = intval("$WRAPUP_TIME_M");
		$WRAPUP_TIME_S = ($WRAPUP_TIME_M - $WRAPUP_TIME_M_int);
		$WRAPUP_TIME_S = ($WRAPUP_TIME_S * 60);
		$WRAPUP_TIME_S = round($WRAPUP_TIME_S, 0);
		if ($WRAPUP_TIME_S < 10) {$WRAPUP_TIME_S = "0$WRAPUP_TIME_S";}
		if ($WRAPUP_TIME_M_int < 10) {$WRAPUP_TIME_M_int = "0$WRAPUP_TIME_M_int";}
		$WRAPUP_TIME_HMS = "$WRAPUP_TIME_H_int:$WRAPUP_TIME_M_int:$WRAPUP_TIME_S";
		$pfWRAPUP_TIME_HMS =		sprintf("%8s", $WRAPUP_TIME_HMS);

		$TALK_AVG_M = ($row[2] / 60);
		$TALK_AVG_M = round($TALK_AVG_M, 2);
		$TALK_AVG_M_int = intval("$TALK_AVG_M");
		$TALK_AVG_S = ($TALK_AVG_M - $TALK_AVG_M_int);
		$TALK_AVG_S = ($TALK_AVG_S * 60);
		$TALK_AVG_S = round($TALK_AVG_S, 0);
		if ($TALK_AVG_S < 10) {$TALK_AVG_S = "0$TALK_AVG_S";}
		$TALK_AVG_MS = "$TALK_AVG_M_int:$TALK_AVG_S";
		$pfTALK_AVG_MS =		sprintf("%6s", $TALK_AVG_MS);

		$PAUSE_AVG_M = ($row[4] / 60);
		$PAUSE_AVG_M = round($PAUSE_AVG_M, 2);
		$PAUSE_AVG_M_int = intval("$PAUSE_AVG_M");
		$PAUSE_AVG_S = ($PAUSE_AVG_M - $PAUSE_AVG_M_int);
		$PAUSE_AVG_S = ($PAUSE_AVG_S * 60);
		$PAUSE_AVG_S = round($PAUSE_AVG_S, 0);
		if ($PAUSE_AVG_S < 10) {$PAUSE_AVG_S = "0$PAUSE_AVG_S";}
		$PAUSE_AVG_MS = "$PAUSE_AVG_M_int:$PAUSE_AVG_S";
		$pfPAUSE_AVG_MS =		sprintf("%6s", $PAUSE_AVG_MS);

		$WAIT_AVG_M = ($row[6] / 60);
		$WAIT_AVG_M = round($WAIT_AVG_M, 2);
		$WAIT_AVG_M_int = intval("$WAIT_AVG_M");
		$WAIT_AVG_S = ($WAIT_AVG_M - $WAIT_AVG_M_int);
		$WAIT_AVG_S = ($WAIT_AVG_S * 60);
		$WAIT_AVG_S = round($WAIT_AVG_S, 0);
		if ($WAIT_AVG_S < 10) {$WAIT_AVG_S = "0$WAIT_AVG_S";}
		$WAIT_AVG_MS = "$WAIT_AVG_M_int:$WAIT_AVG_S";
		$pfWAIT_AVG_MS =		sprintf("%6s", $WAIT_AVG_MS);

		$WRAPUP_AVG_M = ($row[8] / 60);
		$WRAPUP_AVG_M = round($WRAPUP_AVG_M, 2);
		$WRAPUP_AVG_M_int = intval("$WRAPUP_AVG_M");
		$WRAPUP_AVG_S = ($WRAPUP_AVG_M - $WRAPUP_AVG_M_int);
		$WRAPUP_AVG_S = ($WRAPUP_AVG_S * 60);
		$WRAPUP_AVG_S = round($WRAPUP_AVG_S, 0);
		if ($WRAPUP_AVG_S < 10) {$WRAPUP_AVG_S = "0$WRAPUP_AVG_S";}
		$WRAPUP_AVG_MS = "$WRAPUP_AVG_M_int:$WRAPUP_AVG_S";
		$pfWRAPUP_AVG_MS =		sprintf("%6s", $WRAPUP_AVG_MS);

	echo "TOTAL CALLS TAKEN: $row[0]\n";
	echo "TALK TIME:               $pfTALK_TIME_HMS     AVERAGE: $pfTALK_AVG_MS\n";
	echo "PAUSE TIME:              $pfPAUSE_TIME_HMS     AVERAGE: $pfPAUSE_AVG_MS\n";
	echo "WAIT TIME:               $pfWAIT_TIME_HMS     AVERAGE: $pfWAIT_AVG_MS\n";
	echo "WRAPUP TIME:             $pfWRAPUP_TIME_HMS     AVERAGE: $pfWRAPUP_AVG_MS\n";
	echo "----------------------------------------------------------------\n";
	echo "TOTAL ACTIVE AGENT TIME: $pfTOTAL_TIME_HMS\n";

	echo "\n";
	}
else
	{
	echo "<a href=\"$PHP_SELF?calls_summary=1&agent=$agent&query_date=$query_date\">Call Activity Summary</a>\n\n";

	}

$stmt="select event_time,UNIX_TIMESTAMP(event_time) from vicidial_agent_log_archive where event_time <= '" . mysql_real_escape_string($query_date_END) . "' and event_time >= '" . mysql_real_escape_string($query_date_BEGIN) . "' and user='" . mysql_real_escape_string($agent) . "' order by event_time limit 1;";
$rslt=mysql_query($stmt, $link);
if ($DB) {echo "$stmt\n";}
$row=mysql_fetch_row($rslt);

echo "FIRST LOGIN:          $row[0]\n";
$start = $row[1];

$stmt="select event_time,UNIX_TIMESTAMP(event_time) from vicidial_agent_log_archive where event_time <= '" . mysql_real_escape_string($query_date_END) . "' and event_time >= '" . mysql_real_escape_string($query_date_BEGIN) . "' and user='" . mysql_real_escape_string($agent) . "' order by event_time desc limit 1;";
$rslt=mysql_query($stmt, $link);
if ($DB) {echo "$stmt\n";}
$row=mysql_fetch_row($rslt);

echo "LAST LOG ACTIVITY:    $row[0]\n";
$end = $row[1];

$login_time = ($end - $start);
	$LOGIN_TIME_H = ($login_time / 3600);
	$LOGIN_TIME_H = round($LOGIN_TIME_H, 2);
	$LOGIN_TIME_H_int = intval("$LOGIN_TIME_H");
	$LOGIN_TIME_M = ($LOGIN_TIME_H - $LOGIN_TIME_H_int);
	$LOGIN_TIME_M = ($LOGIN_TIME_M * 60);
	$LOGIN_TIME_M = round($LOGIN_TIME_M, 2);
	$LOGIN_TIME_M_int = intval("$LOGIN_TIME_M");
	$LOGIN_TIME_S = ($LOGIN_TIME_M - $LOGIN_TIME_M_int);
	$LOGIN_TIME_S = ($LOGIN_TIME_S * 60);
	$LOGIN_TIME_S = round($LOGIN_TIME_S, 0);
	if ($LOGIN_TIME_S < 10) {$LOGIN_TIME_S = "0$LOGIN_TIME_S";}
	if ($LOGIN_TIME_M_int < 10) {$LOGIN_TIME_M_int = "0$LOGIN_TIME_M_int";}
	$LOGIN_TIME_HMS = "$LOGIN_TIME_H_int:$LOGIN_TIME_M_int:$LOGIN_TIME_S";
	$pfLOGIN_TIME_HMS =		sprintf("%8s", $LOGIN_TIME_HMS);

echo "-----------------------------------------\n";
echo "TOTAL LOGGED-IN TIME:    $pfLOGIN_TIME_HMS\n";

}



?>

</BODY></HTML>