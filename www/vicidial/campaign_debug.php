<?php 
# campaign_debug.php
# 
# Copyright (C) 2011  Matt Florell <vicidial@gmail.com>    LICENSE: AGPLv2
#
# CHANGES
# 110514-1231 - First build
#

require("dbconnect.php");

$PHP_AUTH_USER=$_SERVER['PHP_AUTH_USER'];
$PHP_AUTH_PW=$_SERVER['PHP_AUTH_PW'];
$PHP_SELF=$_SERVER['PHP_SELF'];
if (isset($_GET["DB"]))					{$DB=$_GET["DB"];}
	elseif (isset($_POST["DB"]))		{$DB=$_POST["DB"];}
if (isset($_GET["group"]))				{$group=$_GET["group"];}
	elseif (isset($_POST["group"]))		{$group=$_POST["group"];}
if (isset($_GET["submit"]))				{$submit=$_GET["submit"];}
	elseif (isset($_POST["submit"]))	{$submit=$_POST["submit"];}
if (isset($_GET["SUBMIT"]))				{$SUBMIT=$_GET["SUBMIT"];}
	elseif (isset($_POST["SUBMIT"]))	{$SUBMIT=$_POST["SUBMIT"];}

$PHP_AUTH_USER = ereg_replace("[^0-9a-zA-Z]","",$PHP_AUTH_USER);
$PHP_AUTH_PW = ereg_replace("[^0-9a-zA-Z]","",$PHP_AUTH_PW);

$stmt="SELECT count(*) from vicidial_users where user='$PHP_AUTH_USER' and pass='$PHP_AUTH_PW' and user_level > 7 and view_reports='1' and modify_campaigns='1';";
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
if (!isset($group)) {$group = '';}
if (!isset($query_date)) {$query_date = $NOW_DATE;}
if (!isset($server_ip)) {$server_ip = '10.10.10.15';}

$stmt="select campaign_id,campaign_name from vicidial_campaigns order by campaign_id;";
$rslt=mysql_query($stmt, $link);
if ($DB) {echo "$stmt\n";}
$campaigns_to_print = mysql_num_rows($rslt);
$i=0;
while ($i < $campaigns_to_print)
	{
	$row=mysql_fetch_row($rslt);
	$campaign_id[$i] =$row[0];
	$campaign_name[$i] =$row[1];
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
echo "<META HTTP-EQUIV=\"Content-Type\" CONTENT=\"text/html; charset=utf-8\">\n";
echo "<TITLE>Campaign Debug</TITLE></HEAD><BODY BGCOLOR=WHITE marginheight=0 marginwidth=0 leftmargin=0 topmargin=0>\n";

	$short_header=1;

	require("admin_header.php");

echo "<TABLE CELLPADDING=4 CELLSPACING=0><TR><TD>";
echo "<FORM ACTION=\"$PHP_SELF\" METHOD=GET>\n";
echo "<SELECT SIZE=1 NAME=group>\n";
$o=0;
while ($campaigns_to_print > $o)
	{
	if ($campaign_id[$o] == $group) {echo "<option selected value=\"$campaign_id[$o]\">$campaign_id[$o] - $campaign_name[$o]</option>\n";}
	else {echo "<option value=\"$campaign_id[$o]\">$campaign_id[$o] - $campaign_name[$o]</option>\n";}
	$o++;
	}
echo "</SELECT>\n";
echo "<INPUT TYPE=SUBMIT NAME=SUBMIT VALUE=SUBMIT>\n";
echo " &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; <a href=\"./admin.php?ADD=34&campaign_id=$group\">MODIFY</a> \n";
echo "</FORM>\n\n";

echo "<PRE><FONT SIZE=2>\n\n";


if (!$group)
	{
	echo "\n\n";
	echo "PLEASE SELECT A CAMPAIGN ABOVE AND CLICK SUBMIT\n";
	}

else
	{

	$stmt="select count(*) from vicidial_hopper where campaign_id='" . mysql_real_escape_string($group) . "';";
	$rslt=mysql_query($stmt, $link);
	if ($DB) {echo "$stmt\n";}
	$row=mysql_fetch_row($rslt);
	$TOTALcalls =	sprintf("%10s", $row[0]);

	echo "\n";
	echo "---------- ADAPT DEBUG\n";
	echo "\n";

	$stmt="select campaign_name,closer_campaigns from vicidial_campaigns where campaign_id='" . mysql_real_escape_string($group) . "' limit 1;";
	$rslt=mysql_query($stmt, $link);
	if ($DB) {echo "$stmt\n";}
	$camps_to_print = mysql_num_rows($rslt);
	if ($camps_to_print > 0)
		{
		$row=mysql_fetch_row($rslt);
		$closer_campaigns = $row[1];

		echo "Campaign Debug: $group - $row[0]           $NOW_TIME\n\n";
		echo "Total leads in hopper right now:       $TOTALcalls\n\n";
		}

	$stmt="select update_time,debug_output,adapt_output from vicidial_campaign_stats_debug where campaign_id='" . mysql_real_escape_string($group) . "' and server_ip='ADAPT' limit 1;";
	$rslt=mysql_query($stmt, $link);
	if ($DB) {echo "$stmt\n";}
	$debugs_to_print = mysql_num_rows($rslt);
	$i=0;
	while ($debugs_to_print > $i)
		{
		$row=mysql_fetch_row($rslt);

		echo "Adapt Debug:     $row[0]\n";
		echo "$row[1]\n";
		echo "$row[2]\n";

		$i++;
		}

	$stmt="select update_time,server_ip,debug_output,adapt_output from vicidial_campaign_stats_debug where campaign_id='" . mysql_real_escape_string($group) . "' and server_ip!='ADAPT' order by server_ip limit 100;";
	$rslt=mysql_query($stmt, $link);
	if ($DB) {echo "$stmt\n";}
	$debugs_to_print = mysql_num_rows($rslt);
	$i=0;
	while ($debugs_to_print > $i)
		{
		$row=mysql_fetch_row($rslt);

		echo "$row[1] Debug:     $row[0]\n";
		echo "$row[2]\n";
		echo "$row[3]\n";

		$i++;
		}

	$closer_groupsSQL = preg_replace("/^ | -$/","",$closer_campaigns);
	$closer_groupsSQL = preg_replace("/ /","','",$closer_groupsSQL);

	$stmt="select update_time,campaign_id,debug_output,adapt_output from vicidial_campaign_stats_debug where campaign_id IN('$closer_groupsSQL') and server_ip='INBOUND' order by campaign_id limit 10000;";
	$rslt=mysql_query($stmt, $link);
	if ($DB) {echo "$stmt\n";}
	$debugs_to_print = mysql_num_rows($rslt);
	$i=0;
	while ($debugs_to_print > $i)
		{
		$row=mysql_fetch_row($rslt);

		echo "Inbound Debug: $row[1]    $row[0]\n";
		echo "$row[2]\n";
		echo "$row[3]\n";

		$i++;
		}

	}


?>

</PRE>

</TD></TR></TABLE>

</BODY></HTML>
