<?php 
# AST_timeonpark.php
# 
# Copyright (C) 2009  Matt Florell <vicidial@gmail.com>    LICENSE: AGPLv2
#
# CHANGES
#
# 60620-1042 - Added variable filtering to eliminate SQL injection attack threat
#            - Added required user/pass to gain access to this page
# 90508-0644 - Changed to PHP long tags
#

require("dbconnect.php");

$PHP_AUTH_USER=$_SERVER['PHP_AUTH_USER'];
$PHP_AUTH_PW=$_SERVER['PHP_AUTH_PW'];
$PHP_SELF=$_SERVER['PHP_SELF'];
if (isset($_GET["server_ip"]))				{$server_ip=$_GET["server_ip"];}
	elseif (isset($_POST["server_ip"]))		{$server_ip=$_POST["server_ip"];}
if (isset($_GET["reset_counter"]))				{$reset_counter=$_GET["reset_counter"];}
	elseif (isset($_POST["reset_counter"]))		{$reset_counter=$_POST["reset_counter"];}
if (isset($_GET["submit"]))				{$submit=$_GET["submit"];}
	elseif (isset($_POST["submit"]))		{$submit=$_POST["submit"];}
if (isset($_GET["ΕΠΙΒΕΒΑΙΩΣΗ"]))				{$ΕΠΙΒΕΒΑΙΩΣΗ=$_GET["ΕΠΙΒΕΒΑΙΩΣΗ"];}
	elseif (isset($_POST["ΕΠΙΒΕΒΑΙΩΣΗ"]))		{$ΕΠΙΒΕΒΑΙΩΣΗ=$_POST["ΕΠΙΒΕΒΑΙΩΣΗ"];}

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
    echo "Ακυρο Ονομα Χρήστη/Κωδικός Πρόσβασης: |$PHP_AUTH_USER|$PHP_AUTH_PW|\n";
    exit;
	}

$NOW_TIME = date("Y-m-d H:i:s");
$STARTtime = date("U");
$timeONEhoursAGO = ($STARTtime - 3600);
$epochHALFhoursAGO = ($STARTtime - 1860);
$timeONEhoursAGO = date("Y-m-d H:i:s",$timeONEhoursAGO);
$timeHALFhoursAGO = date("Y-m-d H:i:s",$epochHALFhoursAGO);

$reset_counter++;

if ($reset_counter > 7)
	{
	$reset_counter=0;

	$stmt="update park_log set status='HUNGUP' where hangup_time is not null;";
	$rslt=mysql_query($stmt, $link);
	if ($DB) {echo "$stmt\n";}

	if ($DB)
		{	
		$stmt="delete from park_log where status='TALKING' and grab_time < '$timeONEhoursAGO' and (hangup_time is null or hangup_time='');";
		$rslt=mysql_query($stmt, $link);
		 echo "$stmt\n";

		$stmt="delete from park_log where status='PARKED' and parked_time < '$timeHALFhoursAGO' and (hangup_time is null or hangup_time='');";
		$rslt=mysql_query($stmt, $link);
		 echo "$stmt\n";

		}
	}

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
echo"<META HTTP-EQUIV=Refresh CONTENT=\"7; URL=$PHP_SELF?server_ip=$server_ip&DB=$DB&reset_counter=$reset_counter\">\n";
echo "<TITLE>VICIDIAL: Time On Park</TITLE></HEAD><BODY BGCOLOR=WHITE>\n";
echo "<PRE><FONT SIZE=3>\n\n";

echo "VICIDIAL: Time On Park         $NOW_TIME    <a href=\"./admin.php?ADD=999999\">ΑΝΑΦΟΡΕΣ</a>\n\n";
echo "+------------+-----------------+---------------------+---------+\n";
echo "| CHANNEL    | GROUP           | START TIME          | MINUTES |\n";
echo "+------------+-----------------+---------------------+---------+\n";

#$link=mysql_connect("localhost", "cron", "1234");
# $linkX=mysql_connect("localhost", "cron", "1234");
#mysql_select_db("asterisk");

$stmt="select extension,user,channel,channel_group,parked_time,UNIX_TIMESTAMP(parked_time) from park_log where status ='PARKED' and server_ip='" . mysql_real_escape_string($server_ip) . "' order by uniqueid;";
$rslt=mysql_query($stmt, $link);
if ($DB) {echo "$stmt\n";}
$parked_to_print = mysql_num_rows($rslt);
	if ($parked_to_print > 0)
	{
	$i=0;
	while ($i < $parked_to_print)
		{
		$row=mysql_fetch_row($rslt);

		$channel =			sprintf("%-10s", $row[2]);
		$number_dialed =	sprintf("%-15s", $row[3]);
		$start_time =		sprintf("%-19s", $row[4]);
		$call_time_S = ($STARTtime - $row[5]);

		$call_time_M = ($call_time_S / 60);
		$call_time_M = round($call_time_M, 2);
		$call_time_M_int = intval("$call_time_M");
		$call_time_SEC = ($call_time_M - $call_time_M_int);
		$call_time_SEC = ($call_time_SEC * 60);
		$call_time_SEC = round($call_time_SEC, 0);
		if ($call_time_SEC < 10) {$call_time_SEC = "0$call_time_SEC";}
		$call_time_MS = "$call_time_M_int:$call_time_SEC";
		$call_time_MS =		sprintf("%7s", $call_time_MS);
		$G = '';		$EG = '';
		if ($call_time_M_int >= 1) {$G='<SPAN class="green"><B>'; $EG='</B></SPAN>';}
		if ($call_time_M_int >= 6) {$G='<SPAN class="red"><B>'; $EG='</B></SPAN>';}

		echo "| $G$channel$EG | $G$number_dialed$EG | $G$start_time$EG | $G$call_time_MS$EG |\n";

		$i++;
		}

		echo "+------------+-----------------+---------------------+---------+\n";
		echo "  $i callers waiting on server $server_ip\n\n";

		echo "  <SPAN class=\"green\"><B>          </SPAN> - 1 minute or more on hold</B>\n";
		echo "  <SPAN class=\"red\"><B>          </SPAN> - Over 5 minutes on hold</B>\n";

		}
	else
	{
	echo "****************************************************************\n";
	echo "****************************************************************\n";
	echo "********************ΔΕΝ ΥΠΑΡΧΟΥΝ ΕΝΕΡΓΕΣ ΚΛΗΣΕΙΣ ΣΕ ΑΝΑΜΟΝΗ *********************\n";
	echo "****************************************************************\n";
	echo "****************************************************************\n";
	}

###################################################################################
###### TIME ON INBOUND CALLS
###################################################################################
echo "\n\n";
echo "----------------------------------------------------------------------------------------";
echo "\n\n";
echo "VICIDIAL: Πράκτορες Time On Inbound Calls                             $NOW_TIME\n\n";
echo "+------------|--------+------------+-----------------+---------------------+---------+\n";
echo "| STATION    | USER   | CHANNEL    | GROUP           | START TIME          | MINUTES |\n";
echo "+------------|--------+------------+-----------------+---------------------+---------+\n";


$stmt="select extension,user,channel,channel_group,grab_time,UNIX_TIMESTAMP(grab_time) from park_log where status ='TALKING' and server_ip='" . mysql_real_escape_string($server_ip) . "' order by uniqueid;";
$rslt=mysql_query($stmt, $link);
if ($DB) {echo "$stmt\n";}
$talking_to_print = mysql_num_rows($rslt);
	if ($talking_to_print > 0)
	{
	$i=0;
	while ($i < $talking_to_print)
		{
		$row=mysql_fetch_row($rslt);

		$extension =		sprintf("%-10s", $row[0]);
		$user =				sprintf("%-6s", $row[1]);
		$channel =			sprintf("%-10s", $row[2]);
		$number_dialed =	sprintf("%-15s", $row[3]);
		$start_time =		sprintf("%-19s", $row[4]);
		$call_time_S = ($STARTtime - $row[5]);

		$call_time_M = ($call_time_S / 60);
		$call_time_M = round($call_time_M, 2);
		$call_time_M_int = intval("$call_time_M");
		$call_time_SEC = ($call_time_M - $call_time_M_int);
		$call_time_SEC = ($call_time_SEC * 60);
		$call_time_SEC = round($call_time_SEC, 0);
		if ($call_time_SEC < 10) {$call_time_SEC = "0$call_time_SEC";}
		$call_time_MS = "$call_time_M_int:$call_time_SEC";
		$call_time_MS =		sprintf("%7s", $call_time_MS);
		$G = '';		$EG = '';
		if ($call_time_M_int >= 12) {$G='<SPAN class="blue"><B>'; $EG='</B></SPAN>';}
		if ($call_time_M_int >= 31) {$G='<SPAN class="purple"><B>'; $EG='</B></SPAN>';}

		echo "| $G$extension$EG | $G$user$EG | $G$channel$EG | $G$number_dialed$EG | $G$start_time$EG | $G$call_time_MS$EG |\n";

		$i++;
		}

		echo "+------------|--------+------------+-----------------+---------------------+---------+\n";
		echo "  $i agents on calls on server $server_ip\n\n";

		echo "  <SPAN class=\"blue\"><B>          </SPAN> - 12 minutes or more on call</B>\n";
		echo "  <SPAN class=\"purple\"><B>          </SPAN> - Over 30 minutes on call</B>\n";

	}
	else
	{
	echo "**************************************************************************************\n";
	echo "**************************************************************************************\n";
	echo "********************************* ΚΑΝΕΝΑΣ ΧΕΙΡ. ΣΕ ΚΛΗΣΕΙΣ *********************************\n";
	echo "**************************************************************************************\n";
	echo "**************************************************************************************\n";
	}


?>
</PRE>

</BODY></HTML>