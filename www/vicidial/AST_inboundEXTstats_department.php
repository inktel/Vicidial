<?php 
# AST_inboundEXTstats_department.php
# 
# Copyright (C) 2009  Matt Florell <vicidial@gmail.com>    LICENSE: AGPLv2
#
# CHANGES
# 70201-1710 - First Build
# 90508-0644 - Changed to PHP long tags
#

require("dbconnect.php");

$PHP_AUTH_USER=$_SERVER['PHP_AUTH_USER'];
$PHP_AUTH_PW=$_SERVER['PHP_AUTH_PW'];
$PHP_SELF=$_SERVER['PHP_SELF'];
if (isset($_GET["group"]))					{$group=$_GET["group"];}
	elseif (isset($_POST["group"]))			{$group=$_POST["group"];}
if (isset($_GET["query_date"]))				{$query_date=$_GET["query_date"];}
	elseif (isset($_POST["query_date"]))	{$query_date=$_POST["query_date"];}
if (isset($_GET["end_query_date"]))				{$end_query_date=$_GET["end_query_date"];}
	elseif (isset($_POST["end_query_date"]))	{$end_query_date=$_POST["end_query_date"];}
if (isset($_GET["server_ip"]))				{$server_ip=$_GET["server_ip"];}
	elseif (isset($_POST["server_ip"]))		{$server_ip=$_POST["server_ip"];}
if (isset($_GET["submit"]))					{$submit=$_GET["submit"];}
	elseif (isset($_POST["submit"]))		{$submit=$_POST["submit"];}
if (isset($_GET["SUBMIT"]))					{$SUBMIT=$_GET["SUBMIT"];}
	elseif (isset($_POST["SUBMIT"]))		{$SUBMIT=$_POST["SUBMIT"];}

$PHP_AUTH_USER = ereg_replace("[^0-9a-zA-Z]","",$PHP_AUTH_USER);
$PHP_AUTH_PW = ereg_replace("[^0-9a-zA-Z]","",$PHP_AUTH_PW);

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

$stmt="SELECT count(*) from vicidial_users where user='$PHP_AUTH_USER' and pass='$PHP_AUTH_PW' and user_level > 6 and view_reports='1';";
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
if (!isset($query_date)) {$query_date = $NOW_DATE;}
if (!isset($end_query_date)) {$end_query_date = $NOW_DATE;}
if (!isset($server_ip)) {$server_ip = '10.10.11.20';}

$stmt="select distinct department from inbound_numbers;";
$rslt=mysql_query($stmt, $link);
if ($DB) {echo "$stmt\n";}
$dept_to_print = mysql_num_rows($rslt);
$i=0;
while ($i < $dept_to_print)
	{
	$row=mysql_fetch_row($rslt);
	$dept[$i] =$row[0];
	$i++;
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
echo"<META HTTP-EQUIV=\"Content-Type\" CONTENT=\"text/html; charset=utf-8\">\n";
#echo"<META HTTP-EQUIV=Refresh CONTENT=\"7; URL=$PHP_SELF?server_ip=$server_ip&DB=$DB\">\n";
echo "<TITLE>ASTERISK: Inbound Calls Stats - By Department</TITLE></HEAD><BODY BGCOLOR=WHITE>\n";
echo "<FORM ACTION=\"$PHP_SELF\" METHOD=GET>\n";
echo "<INPUT TYPE=HIDDEN NAME=server_ip VALUE=\"$server_ip\">\n";
echo "<INPUT TYPE=TEXT NAME=query_date SIZE=10 MAXLENGTH=10 VALUE=\"$query_date\">\n";
echo "<INPUT TYPE=TEXT NAME=end_query_date SIZE=10 MAXLENGTH=10 VALUE=\"$end_query_date\">\n";
echo "<SELECT SIZE=1 NAME=group>\n";
	$o=0;
	while ($dept_to_print > $o)
	{
		if ($dept[$o] == $group) {echo "<option selected value=\"$dept[$o]\">$dept[$o]</option>\n";}
		  else {echo "<option value=\"$dept[$o]\">$dept[$o]</option>\n";}
		$o++;
	}
echo "</SELECT>\n";
echo "<INPUT TYPE=SUBMIT NAME=SUBMIT VALUE=SUBMIT>\n";
echo "</FORM>\n\n";

echo "<PRE><FONT SIZE=2>\n\n";


if (!$group)
{
echo "\n\n";
echo "PLEASE SELECT A DEPARTMENT AND DATE RANGE ABOVE AND CLICK SUBMIT\n";
}

else
{
$extSQL='';
$stmt="select extension from inbound_numbers where department='$group';";
$rslt=mysql_query($stmt, $link);
if ($DB) {echo "$stmt\n";}
$inbound_to_print = mysql_num_rows($rslt);
$i=0;
while ($i < $inbound_to_print)
	{
	if (strlen($extSQL)> 1) {$extSQL .= ",";}
	$row=mysql_fetch_row($rslt);
	$extensions[$i] =$row[0];
	$extSQL .= "'$extensions[$i]'";
	$i++;
	}

echo "ASTERISK: Inbound Calls Stats For $group   from $query_date to $end_query_date\n";

echo "\n";
echo "---------- TOTALS\n";
echo "\n";

echo "+----------------------+------------+------------+\n";
echo "| NUMBER               | CALLS      | AVG TIME   |\n";
echo "+----------------------+------------+------------+\n";

$k=0;
while ($k < $inbound_to_print)
	{
	$stmt="select count(*),sum(length_in_sec) from call_log where start_time >= '" . mysql_real_escape_string($query_date) . " 00:00:01' and start_time <= '" . mysql_real_escape_string($end_query_date) . " 23:59:59' and server_ip='" . mysql_real_escape_string($server_ip) . "' and extension='$extensions[$k]' ;";
	$rslt=mysql_query($stmt, $link);
	if ($DB) {echo "$stmt\n";}
	$row=mysql_fetch_row($rslt);

	$extensions[$k] = sprintf("%20s", $extensions[$k]);
	$TOTALcalls =	sprintf("%10s", $row[0]);
	if ( ($row[0]<1) or ($row[1]<1) )
		{
		$average_hold_seconds = "         0";
		}
	else
		{
		$average_hold_seconds = ($row[1] / $row[0]);
		$average_hold_seconds = round($average_hold_seconds, 0);
		$average_hold_seconds =	sprintf("%10s", $average_hold_seconds);
		}

	$calls = ($TOTALcalls + $calls);
	$seconds = ($row[1] + $seconds);

	echo "| $extensions[$k] | $TOTALcalls | $average_hold_seconds |\n";
	$k++;
	}

$calls =	sprintf("%10s", $calls);
$seconds = ($seconds / $calls);
$seconds = round($seconds, 0);
$seconds =	sprintf("%5s", $seconds);
echo "+----------------------+------------+------------+\n";
echo "| TOTALS               | $calls | AVG: $seconds |\n";
echo "+----------------------+------------+------------+\n";


}



?>
</PRE>

</BODY></HTML>