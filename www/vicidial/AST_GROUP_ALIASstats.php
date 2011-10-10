<?php 
# AST_GROUP_ALIASstats.php
# 
# Copyright (C) 2009  Matt Florell <vicidial@gmail.com>    LICENSE: AGPLv2
#
# If you are going to run this report I would recommend adding the following in MySQL:
# CREATE INDEX extension on call_log (extension);
#
# CHANGES
#
# 90914-1003 - First build
#

require("dbconnect.php");
require("functions.php");

$PHP_AUTH_USER=$_SERVER['PHP_AUTH_USER'];
$PHP_AUTH_PW=$_SERVER['PHP_AUTH_PW'];
$PHP_SELF=$_SERVER['PHP_SELF'];
if (isset($_GET["query_date"]))				{$query_date=$_GET["query_date"];}
	elseif (isset($_POST["query_date"]))	{$query_date=$_POST["query_date"];}
if (isset($_GET["end_date"]))				{$end_date=$_GET["end_date"];}
	elseif (isset($_POST["end_date"]))		{$end_date=$_POST["end_date"];}
if (isset($_GET["submit"]))					{$submit=$_GET["submit"];}
	elseif (isset($_POST["submit"]))		{$submit=$_POST["submit"];}
if (isset($_GET["SUBMIT"]))					{$SUBMIT=$_GET["SUBMIT"];}
	elseif (isset($_POST["SUBMIT"]))		{$SUBMIT=$_POST["SUBMIT"];}
if (isset($_GET["DB"]))						{$DB=$_GET["DB"];}
	elseif (isset($_POST["DB"]))			{$DB=$_POST["DB"];}

$PHP_AUTH_USER = ereg_replace("[^0-9a-zA-Z]","",$PHP_AUTH_USER);
$PHP_AUTH_PW = ereg_replace("[^0-9a-zA-Z]","",$PHP_AUTH_PW);

if (strlen($shift)<2) {$shift='ALL';}

#############################################
##### START SYSTEM_SETTINGS LOOKUP #####
$stmt = "SELECT use_non_latin FROM system_settings;";
$rslt=mysql_query($stmt, $link);
if ($DB) {echo "$stmt\n";}
$qm_conf_ct = mysql_num_rows($rslt);
if ($qm_conf_ct > 0)
	{
	$row=mysql_fetch_row($rslt);
	$non_latin =					$row[0];
	}
##### END SETTINGS LOOKUP #####
###########################################

$stmt="SELECT count(*) from vicidial_users where user='$PHP_AUTH_USER' and pass='$PHP_AUTH_PW' and user_level >= 6 and view_reports='1';";
if ($DB) {echo "|$stmt|\n";}
if ($non_latin > 0) {$rslt=mysql_query("SET NAMES 'UTF8'");}
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
if (!isset($group)) {$group = '';}
if (!isset($query_date)) {$query_date = "$NOW_DATE 00:00:00";}
if (!isset($end_date)) {$end_date = "$NOW_DATE 23:59:59";}


?>

<HTML>
<HEAD>
<STYLE type="text/css">
<!--
   .green {color: black; background-color: #99FF99}
   .red {color: black; background-color: #FF9999}
   .orange {color: black; background-color: #FFCC99}
-->
 </STYLE>

<?php 
echo "<META HTTP-EQUIV=\"Content-Type\" CONTENT=\"text/html; charset=utf-8\">\n";
echo "<TITLE>Group Alias Report</TITLE></HEAD><BODY BGCOLOR=WHITE marginheight=0 marginwidth=0 leftmargin=0 topmargin=0>\n";

	$short_header=1;

	require("admin_header.php");

if ($DB > 0)
	{
	echo "<BR>\n";
	echo "$query_date|$end_date\n";
	echo "<BR>\n";
	}

echo "<TABLE CELLPADDING=4 CELLSPACING=0><TR><TD>";

echo "<FORM ACTION=\"$PHP_SELF\" METHOD=GET>\n";
echo "<TABLE BORDER=0 CELLPADDING=2 CELLSPACING=2><TR><TD align=center valign=top>\n";
echo "<INPUT TYPE=TEXT NAME=query_date SIZE=20 MAXLENGTH=20 VALUE=\"$query_date\">\n";
echo "<BR> to <BR><INPUT TYPE=TEXT NAME=end_date SIZE=20 MAXLENGTH=20 VALUE=\"$end_date\">\n";
echo "</TD><TD align=center valign=top>\n";
echo "</TD><TD align=center valign=top>\n";
echo "</TD><TD align=center valign=top>\n";
echo "<INPUT TYPE=hidden NAME=DB VALUE=\"$DB\">\n";
echo "<INPUT TYPE=submit NAME=SUBMIT VALUE=SUBMIT>\n";
echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;  <a href=\"./admin.php?ADD=999999\">REPORTS</a> </FONT>\n";
echo "</TD></TR></TABLE>\n";
echo "</FORM>\n";

echo "<PRE><FONT SIZE=2>";


if (!$query_date)
{
echo "\n\n";
echo "PLEASE SELECT A DATE RANGE ABOVE AND CLICK SUBMIT\n";
}

else
{
echo "Group Alias Report                      $NOW_TIME\n";
echo "\n";
echo "Time range $query_date to $end_date\n\n";


### GRAB ALL RECORDS WITHIN RANGE FROM THE DATABASE ###
echo "Group Alias Summary:\n";
echo "+------------------------------------------------------------------------+------------+----------+\n";
echo "| GROUP ALIAS                                                            | MINUTES    | CALLS    |\n";
echo "+------------------------------------------------------------------------+------------+----------+\n";

$stmt="SELECT count(*),ucl.group_alias_id,group_alias_name from user_call_log ucl,groups_alias ga where call_date >= '$query_date' and call_date <= '$end_date' and ucl.group_alias_id=ga.group_alias_id group by ucl.group_alias_id order by ucl.group_alias_id;";
$rslt=mysql_query($stmt, $link);
if ($DB) {echo "$stmt\n";}
$records_to_grab = mysql_num_rows($rslt);
$i=0;
while ($i < $records_to_grab)
	{
	$row=mysql_fetch_row($rslt);
	$TOTcount = ($TOTcount + $row[0]);
	$count[$i] =	sprintf("%-8s", $row[0]);
	$group_alias_id[$i] =	$row[1];
	$group_alias_name[$i] =	sprintf("%-70s", "$row[1] - $row[2]");
	$i++;
	}

$total_sec=0;
$i=0;
while ($i < $records_to_grab)
	{
	$stmt="SELECT UNIX_TIMESTAMP(call_date),call_type,phone_number,number_dialed from user_call_log where group_alias_id='$group_alias_id[$i]' and call_date >= '$query_date' and call_date <= '$end_date';";
	$rslt=mysql_query($stmt, $link);
	if ($DB) {echo "$stmt\n";}
	$records_to_grabC = mysql_num_rows($rslt);
	$k=0;
	while ($k < $records_to_grabC)
		{
		$row=mysql_fetch_row($rslt);
		$call_dateS[$k] =		($row[0] - 5);
		$call_dateE[$k] =		($row[0] + 5);
		$call_type[$k] =		$row[1];
		$phone_number[$k] =		$row[2];
		$number_dialed[$k] =	$row[3];
		$k++;
		}

	$found_sec=0;
	$group_sec=0;
	$k=0;
	while ($k < $records_to_grabC)
		{
		$stmt="SELECT length_in_sec from call_log where extension='$phone_number[$k]'";
		if (eregi("MANUAL_OVERRIDE",$call_type[$k]))
			{$stmt="SELECT length_in_sec from call_log where extension='$phone_number[$k]'";}
		if (eregi("XFER_OVERRIDE",$call_type[$k]))
			{
			$number_dialed[$k] = eregi_replace("Local/",'',$number_dialed[$k]);
			$number_dialed[$k] = eregi_replace("@default",'',$number_dialed[$k]);
			$stmt="SELECT length_in_sec from call_log where extension='$number_dialed[$k]'";
			}
		if (eregi("XFER_3WAY",$call_type[$k]))
			{
			$number_dialed[$k] = eregi_replace("Local/",'',$number_dialed[$k]);
			$number_dialed[$k] = eregi_replace("@default",'',$number_dialed[$k]);
			$stmt="SELECT length_in_sec from call_log where extension='$number_dialed[$k]'";
			}
		if (eregi("MANUAL_DIALNOW",$call_type[$k]))
			{$stmt="SELECT length_in_sec from call_log where extension='$phone_number[$k]'";}
		if (eregi("MANUAL_DIALFAST",$call_type[$k]))
			{$stmt="SELECT length_in_sec from call_log where extension='$phone_number[$k]'";}

		$stmt .= " and start_epoch >= $call_dateS[$k] and start_epoch <= $call_dateE[$k];";
		$rslt=mysql_query($stmt, $link);
		if ($DB) {echo "$stmt\n";}
		$records_to_grabCL = mysql_num_rows($rslt);
		if ($records_to_grabCL > 0)
			{
			$row=mysql_fetch_row($rslt);
			$length_in_sec[$k] =		$row[0];
			$group_sec = ($group_sec + $row[0]);
			$total_sec = ($total_sec + $row[0]);
			$found_sec++;
			}
		$k++;
		}

	$Ccall_time_MS =		sec_convert($group_sec,'M'); 
	$Ccall_time_MS =		sprintf("%10s", $Ccall_time_MS);
	echo "| $group_alias_name[$i] | $Ccall_time_MS | $count[$i] |";
	if ($DB) {echo "$found_sec";}
	echo "\n";
	$i++;
	}

$TOTcount =	sprintf("%-8s", $TOTcount);

$Tcall_time_MS =		sec_convert($total_sec,'M'); 
$Tcall_time_MS =		sprintf("%10s", $Tcall_time_MS);

echo "+------------------------------------------------------------------------+------------+----------+\n";
echo "                                                                         | $Tcall_time_MS | $TOTcount |\n";
echo "                                                                         +------------+----------+\n";
echo "\n</PRE>\n";



$ENDtime = date("U");
$RUNtime = ($ENDtime - $STARTtime);
echo "<BR><BR>\nRun Time: $RUNtime seconds\n";

}

?>

</TD></TR></TABLE>

</BODY></HTML>
