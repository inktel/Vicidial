<?php 
# sph_report.php
# 
# Copyright (C) 2009  Matt Florell <vicidial@gmail.com>    LICENSE: AGPLv2
#
# CHANGES
#
# 80502-0857 - First build
# 80506-0228 - Added user field to search by
# 90508-0644 - Changed to PHP long tags
#

require("dbconnect.php");

##### Pull values from posted form variables #####
$PHP_AUTH_USER=$_SERVER['PHP_AUTH_USER'];
$PHP_AUTH_PW=$_SERVER['PHP_AUTH_PW'];
$PHP_SELF=$_SERVER['PHP_SELF'];
if (isset($_GET["query_date"]))				{$query_date=$_GET["query_date"];}
	elseif (isset($_POST["query_date"]))	{$query_date=$_POST["query_date"];}
if (isset($_GET["end_date"]))				{$end_date=$_GET["end_date"];}
	elseif (isset($_POST["end_date"]))		{$end_date=$_POST["end_date"];}
if (isset($_GET["campaign"]))				{$campaign=$_GET["campaign"];}
	elseif (isset($_POST["campaign"]))		{$campaign=$_POST["campaign"];}
if (isset($_GET["group"]))				{$group=$_GET["group"];}
	elseif (isset($_POST["group"]))		{$group=$_POST["group"];}
if (isset($_GET["user_group"]))				{$user_group=$_GET["user_group"];}
	elseif (isset($_POST["user_group"]))	{$user_group=$_POST["user_group"];}
if (isset($_GET["shift"]))				{$shift=$_GET["shift"];}
	elseif (isset($_POST["shift"]))		{$shift=$_POST["shift"];}
if (isset($_GET["role"]))				{$role=$_GET["role"];}
	elseif (isset($_POST["role"]))		{$role=$_POST["role"];}
if (isset($_GET["order"]))				{$order=$_GET["order"];}
	elseif (isset($_POST["order"]))		{$order=$_POST["order"];}
if (isset($_GET["user"]))				{$user=$_GET["user"];}
	elseif (isset($_POST["user"]))		{$user=$_POST["user"];}
if (isset($_GET["DB"]))				{$DB=$_GET["DB"];}
	elseif (isset($_POST["DB"]))	{$DB=$_POST["DB"];}
if (isset($_GET["submit"]))				{$submit=$_GET["submit"];}
	elseif (isset($_POST["submit"]))	{$submit=$_POST["submit"];}
if (isset($_GET["SUBMIT"]))				{$SUBMIT=$_GET["SUBMIT"];}
	elseif (isset($_POST["SUBMIT"]))	{$SUBMIT=$_POST["SUBMIT"];}

if (strlen($shift)<2) {$shift='ALL';}
if (strlen($role)<2) {$role='ALL';}
if (strlen($order)<2) {$order='sales_down';}

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
    echo "Invalid Username/Password: |$PHP_AUTH_USER|$PHP_AUTH_PW|\n";
    exit;
	}

$NOW_DATE = date("Y-m-d");
$NOW_TIME = date("Y-m-d H:i:s");
$STARTtime = date("U");
if (!isset($group)) {$group = '';}
if (!isset($query_date)) {$query_date = $NOW_DATE;}
if (!isset($end_date)) {$end_date = $NOW_DATE;}

$stmt="select campaign_id from vicidial_campaigns;";
$rslt=mysql_query($stmt, $link);
if ($DB) {echo "$stmt\n";}
$campaigns_to_print = mysql_num_rows($rslt);
$i=0;
	$LISTcampaigns[$i]='---NONE---';
	$i++;
	$campaigns_to_print++;
while ($i < $campaigns_to_print)
	{
	$row=mysql_fetch_row($rslt);
	$LISTcampaigns[$i] =$row[0];
	$i++;
	}

$stmt="select group_id from vicidial_inbound_groups;";
$rslt=mysql_query($stmt, $link);
if ($DB) {echo "$stmt\n";}
$groups_to_print = mysql_num_rows($rslt);
$i=0;
	$LISTgroups[$i]='---NONE---';
	$i++;
	$groups_to_print++;
while ($i < $groups_to_print)
	{
	$row=mysql_fetch_row($rslt);
	$LISTgroups[$i] =$row[0];
	$i++;
	}

$stmt="select user_group from vicidial_user_groups;";
$rslt=mysql_query($stmt, $link);
if ($DB) {echo "$stmt\n";}
$user_groups_to_print = mysql_num_rows($rslt);
$i=0;
	$LISTuser_groups[$i]='---ALL---';
	$i++;
	$user_groups_to_print++;
while ($i < $user_groups_to_print)
	{
	$row=mysql_fetch_row($rslt);
	$LISTuser_groups[$i] =$row[0];
	$i++;
	}

##### START HTML #####
?>

<HTML>
<HEAD>

<style type="text/css">
<!--
	div.scroll_callback {height: 300px; width: 620px; overflow: scroll;}
	div.scroll_list {height: 400px; width: 140px; overflow: scroll;}
	div.scroll_script {height: 331px; width: 600px; background: #FFF5EC; overflow: scroll; font-size: 12px;  font-family: sans-serif;}
	div.text_input {overflow: auto; font-size: 10px;  font-family: sans-serif;}
   .body_text {font-size: 13px;  font-family: sans-serif;}
   .preview_text {font-size: 13px;  font-family: sans-serif; background: #CCFFCC}
   .preview_text_red {font-size: 13px;  font-family: sans-serif; background: #FFCCCC}
   .body_small {font-size: 11px;  font-family: sans-serif;}
   .body_tiny {font-size: 10px;  font-family: sans-serif;}
   .log_text {font-size: 11px;  font-family: monospace;}
   .log_text_red {font-size: 11px;  font-family: monospace; font-weight: bold; background: #FF3333}
   .sd_text {font-size: 16px;  font-family: sans-serif; font-weight: bold;}
   .sh_text {font-size: 14px;  font-family: sans-serif; font-weight: bold;}
   .sb_text {font-size: 12px;  font-family: sans-serif;}
   .sk_text {font-size: 11px;  font-family: sans-serif;}
   .skb_text {font-size: 13px;  font-family: sans-serif; font-weight: bold;}
   .ON_conf {font-size: 11px;  font-family: monospace; color: black; background: #FFFF99}
   .OFF_conf {font-size: 11px;  font-family: monospace; color: black; background: #FFCC77}
   .cust_form {font-family: sans-serif; font-size: 10px; overflow: auto}

   .select_bold {font-size: 14px;  font-family: sans-serif; font-weight: bold;}
   .header_white {font-size: 14px;  font-family: sans-serif; font-weight: bold; color: white}
   .data_records {font-size: 12px;  font-family: sans-serif; color: black}
   .data_records_fix {font-size: 12px;  font-family: monospace; color: black}
   .data_records_fix_small {font-size: 9px;  font-family: monospace; color: black}

-->
</style>

<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=utf-8">
<TITLE>VICIDIAL: Agent SPH Report</TITLE>

</HEAD><BODY BGCOLOR=WHITE>

<?php
$campaign_ct = count($campaign);
$group_ct = count($group);
$user_group_ct = count($user_group);
$campaign_string='|';
$group_string='|';
$user_group_string='|';

$i=0;
while($i < $campaign_ct)
	{
	$campaign_string .= "$campaign[$i]|";
	$campaign_SQL .= "'$campaign[$i]',";
	$i++;
	}
if ( (ereg("--NONE--",$campaign_string) ) or ($campaign_ct < 1) )
	{
	$campaign_SQL = "campaign_id IN('')";
	}
else
	{
	$campaign_SQL = eregi_replace(",$",'',$campaign_SQL);
	$campaign_SQL = "campaign_id IN($campaign_SQL)";
	}

$i=0;
while($i < $group_ct)
	{
	$group_string .= "$group[$i]|";
	$group_SQL .= "'$group[$i]',";
	$i++;
	}
if ( (ereg("--NONE--",$group_string) ) or ($group_ct < 1) )
	{
	$group_SQL = "''";
#	$group_SQL = "group_id IN('')";
	}
else
	{
	$group_SQL = eregi_replace(",$",'',$group_SQL);
#	$group_SQL = "group_id IN($group_SQL)";
	}

$i=0;
while($i < $user_group_ct)
	{
	$user_group_string .= "$user_group[$i]|";
	$user_group_SQL .= "'$user_group[$i]',";
	$i++;
	}
if ( (ereg("--ALL--",$user_group_string) ) or ($user_group_ct < 1) )
	{
	$user_group_SQL = "";
	}
else
	{
	$user_group_SQL = eregi_replace(",$",'',$user_group_SQL);
	$user_group_SQL = "user_group_id IN($user_group_SQL)";
	}

if ($role == "ALL")
	{
	$role_SQL = "";
	}
else
	{
	$role_SQL = "and role='$role'";
	}


if ($DB > 0)
	{
	echo "<BR>\n";
	echo "$campaign_ct|$campaign_string|$campaign_SQL\n";
	echo "<BR>\n";
	echo "$group_ct|$group_string|$group_SQL\n";
	echo "<BR>\n";
	echo "$user_group_ct|$user_group_string|$user_group_SQL\n";
	echo "<BR>\n";
	echo "$role|$role_SQL\n";
	echo "<BR>\n";

	}

echo "<CENTER>\n";
echo "<FORM ACTION=\"$PHP_SELF\" METHOD=GET>\n";
echo "<INPUT TYPE=HIDDEN NAME=DB VALUE=\"$DB\">";
echo "<TABLE BORDER=0 CELLSPACING=6><TR><TD ALIGN=LEFT VALIGN=TOP ROWSPAN=3>\n";

echo "<font class=\"select_bold\"><B>Date Range:</B></font><BR><CENTER>\n";
echo "<INPUT TYPE=TEXT NAME=query_date SIZE=10 MAXLENGTH=10 VALUE=\"$query_date\">";
echo "<BR>to<BR>\n";
echo "<INPUT TYPE=TEXT NAME=end_date SIZE=10 MAXLENGTH=10 VALUE=\"$end_date\">\n";
/*
echo "</TD><TD ALIGN=LEFT VALIGN=TOP ROWSPAN=2>\n";
echo "<font class=\"select_bold\"><B>Campaigns:</B></font><BR><CENTER>\n";
echo "<SELECT SIZE=5 NAME=campaign[] multiple>\n";
	$o=0;
	while ($campaigns_to_print > $o)
	{
		if (ereg("\|$LISTcampaigns[$o]\|",$campaign_string)) 
			{echo "<option selected value=\"$LISTcampaigns[$o]\">$LISTcampaigns[$o]</option>\n";}
		else 
			{echo "<option value=\"$LISTcampaigns[$o]\">$LISTcampaigns[$o]</option>\n";}
		$o++;
	}
echo "</SELECT>\n";
*/
echo "</TD><TD ALIGN=LEFT VALIGN=TOP ROWSPAN=3>\n";
echo "<font class=\"select_bold\"><B>Inbound Groups:</B></font><BR><CENTER>\n";
echo "<SELECT SIZE=5 NAME=group[] multiple>\n";
	$o=0;
	while ($groups_to_print > $o)
	{
		if (ereg("\|$LISTgroups[$o]\|",$group_string)) 
			{echo "<option selected value=\"$LISTgroups[$o]\">$LISTgroups[$o]</option>\n";}
		else
			{echo "<option value=\"$LISTgroups[$o]\">$LISTgroups[$o]</option>\n";}
		$o++;
	}
echo "</SELECT>\n";
echo "</TD><TD ALIGN=LEFT VALIGN=TOP ROWSPAN=3>\n";
echo "<font class=\"select_bold\"><B>User Groups:</B></font><BR><CENTER>\n";
echo "<SELECT SIZE=5 NAME=user_group[] multiple>\n";
	$o=0;
	while ($user_groups_to_print > $o)
	{
		if (ereg("\|$LISTuser_groups[$o]\|",$user_group_string)) 
			{echo "<option selected value=\"$LISTuser_groups[$o]\">$LISTuser_groups[$o]</option>\n";}
		else 
			{echo "<option value=\"$LISTuser_groups[$o]\">$LISTuser_groups[$o]</option>\n";}
		$o++;
	}
echo "</SELECT>\n";
echo "</TD><TD ALIGN=LEFT VALIGN=TOP>\n";
echo "<font class=\"select_bold\"><B>Shift:</B></font><BR>\n";
echo "<SELECT SIZE=1 NAME=shift>\n";
echo "<option selected value=\"$shift\">$shift</option>\n";
echo "<option value=\"\">--</option>\n";
echo "<option value=\"AM\">AM</option>\n";
echo "<option value=\"PM\">PM</option>\n";
echo "<option value=\"ALL\">ALL</option>\n";
echo "</SELECT>&nbsp;\n";

echo "</TD><TD ALIGN=LEFT VALIGN=TOP COLSPAN=2>\n";
echo "<font class=\"select_bold\"><B>Role:</B></font><BR>\n";
echo "<SELECT SIZE=1 NAME=role>\n";
echo "<option selected value=\"$role\">$role</option>\n";
echo "<option value=\"\">--</option>\n";
echo "<option value=\"FRONTER\">FRONTER</option>\n";
echo "<option value=\"CLOSER\">CLOSER</option>\n";
echo "<option value=\"ALL\">ALL</option>\n";
echo "</SELECT>&nbsp;\n";

echo "</TD><TD ALIGN=CENTER VALIGN=TOP ROWSPAN=3>\n";
echo "<FONT class=\"select_bold\" COLOR=BLACK SIZE=2> &nbsp; &nbsp; <a href=\"./admin.php?ADD=999999\">REPORTS</a> </FONT>\n";

echo "</TD></TR><TR></TD><TD ALIGN=LEFT VALIGN=TOP COLSPAN=2>\n";
echo "<font class=\"select_bold\"><B>Order:</B></font><BR>\n";
echo "<SELECT SIZE=1 NAME=order>\n";
echo "<option selected value=\"$order\">$order</option>\n";
echo "<option value=\"\">--</option>\n";
echo "<option>sph_up</option>\n";
echo "<option>sph_down</option>\n";
echo "<option>hours_up</option>\n";
echo "<option>hours_down</option>\n";
echo "<option>sales_up</option>\n";
echo "<option>sales_down</option>\n";
echo "<option>calls_up</option>\n";
echo "<option>calls_down</option>\n";
echo "<option>user_up</option>\n";
echo "<option>user_down</option>\n";
echo "<option>name_up</option>\n";
echo "<option>name_down</option>\n";
echo "</SELECT><BR><CENTER>\n";

echo "</TD><TD ALIGN=LEFT VALIGN=TOP>\n";
echo "<font class=\"select_bold\"><B>User:</B></font><BR>\n";
echo "<INPUT TYPE=text NAME=user SIZE=7 MAXLENGTH=20 VALUE=\"$user\">\n";

echo "</TD></TR><TR></TD><TD ALIGN=LEFT VALIGN=TOP COLSPAN=3>\n";
echo "<INPUT TYPE=SUBMIT NAME=SUBMIT VALUE=SUBMIT>\n";
echo "</TD></TR></TABLE>\n";
echo "</FORM>\n\n";

echo "<PRE><FONT SIZE=3>\n";


if ($group_ct < 1)
{
echo "\n";
echo "PLEASE SELECT AN IN-GROUP AND DATE RANGE ABOVE AND CLICK SUBMIT\n";
echo " NOTE: stats taken from shift specified\n";
}

else
{
/*
if ($shift == 'AM') 
	{
	$time_BEGIN=$AM_shift_BEGIN;
	$time_END=$AM_shift_END;
	if (strlen($time_BEGIN) < 6) {$time_BEGIN = "03:45:00";}   
	if (strlen($time_END) < 6) {$time_END = "15:15:00";}
	}
if ($shift == 'PM') 
	{
	$time_BEGIN=$PM_shift_BEGIN;
	$time_END=$PM_shift_END;
	if (strlen($time_BEGIN) < 6) {$time_BEGIN = "15:15:00";}
	if (strlen($time_END) < 6) {$time_END = "23:15:00";}
	}
if ($shift == 'ALL') 
	{
	if (strlen($time_BEGIN) < 6) {$time_BEGIN = "00:00:00";}
	if (strlen($time_END) < 6) {$time_END = "23:59:59";}
	}
$query_date_BEGIN = "$query_date $time_BEGIN";   
$query_date_END = "$end_date $time_END";

if (strlen($user_group)>0) {$ugSQL="and vicidial_agent_log.user_group='$user_group'";}
else {$ugSQL='';}
*/

echo "VICIDIAL: Agent SPH Report                        $NOW_TIME\n";

echo "Time range: $query_date to $end_date\n\n";
echo "---------- AGENTS SPH DETAILS -------------\n</PRE>\n";

echo "<TABLE BORDER=0 CELLSPACING=1 CELLPADDING=3><TR BGCOLOR=BLACK>\n";
echo "<TD ALIGN=CENTER><FONT class=\"header_white\">#</TD>\n";
echo "<TD ALIGN=CENTER><FONT class=\"header_white\">&nbsp; USER &nbsp;</TD>\n";
echo "<TD ALIGN=CENTER><FONT class=\"header_white\">&nbsp; NAME &nbsp;</TD>\n";
echo "<TD ALIGN=CENTER><FONT class=\"header_white\">&nbsp; ROLE &nbsp;</TD>\n";
echo "<TD ALIGN=CENTER><FONT class=\"header_white\">&nbsp; GROUP &nbsp;</TD>\n";
echo "<TD ALIGN=CENTER><FONT class=\"header_white\">&nbsp; CALLS &nbsp;</TD>\n";
echo "<TD ALIGN=CENTER><FONT class=\"header_white\">&nbsp; HOURS &nbsp;</TD>\n";
echo "<TD ALIGN=CENTER><FONT class=\"header_white\">&nbsp; SALES &nbsp;</TD>\n";
echo "<TD ALIGN=CENTER><FONT class=\"header_white\">&nbsp; SPH &nbsp;</TD>\n";
echo "</TR>\n";

$order_SQL='';
if ($order == 'sph_up')		{$order_SQL = "order by full_name,campaign_group_id,role";}
if ($order == 'sph_down')	{$order_SQL = "order by full_name,campaign_group_id,role";}
if ($order == 'hours_up')	{$order_SQL = "order by login";}
if ($order == 'hours_down') {$order_SQL = "order by login desc";}
if ($order == 'sales_up')	{$order_SQL = "order by sales, sph desc";}
if ($order == 'sales_down') {$order_SQL = "order by sales desc, sph";}
if ($order == 'calls_up')	{$order_SQL = "order by calls";}
if ($order == 'calls_down') {$order_SQL = "order by calls desc";}
if ($order == 'user_up')	{$order_SQL = "order by vicidial_users.user";}
if ($order == 'user_down')	{$order_SQL = "order by vicidial_users.user desc";}
if ($order == 'name_up')	{$order_SQL = "order by full_name";}
if ($order == 'name_down')	{$order_SQL = "order by full_name desc";}

if (strlen($user) > 0)		{$user_SQL = "and vicidial_agent_sph.user='$user'";}
else {$user_SQL='';}

$stmt="select vicidial_users.user,full_name,role,campaign_group_id,sum(login_sec) as login,sum(calls) as calls,sum(sales) as sales,avg(sph) as sph from vicidial_users,vicidial_agent_sph where stat_date >= '$query_date' and stat_date <= '$end_date' and shift='$shift' and vicidial_users.user=vicidial_agent_sph.user and campaign_group_id IN($group_SQL) $role_SQL $user_SQL group by vicidial_users.user,campaign_group_id,role $order_SQL limit 100000;";
$rslt=mysql_query($stmt, $link);
if ($DB) {echo "$stmt\n";}
$rows_to_print = mysql_num_rows($rslt);
$i=0;
while ($i < $rows_to_print)
	{
	$dbHOURS=0; $dbSPH=0; 
	$row=mysql_fetch_row($rslt);
	$user_id[$i] =		$row[0];
	$full_name[$i] =	$row[1];
	$roleX[$i] =		$row[2];
	$group[$i] =		$row[3];
	$login_sec[$i] =	$row[4];	$TOTlogin_sec = ($TOTlogin_sec + $row[4]);
	$calls[$i] =		$row[5];	$TOTcalls = ($TOTcalls + $row[5]);
	$sales[$i] =		$row[6];	$TOTsales = ($TOTsales + $row[6]);

	if ($login_sec[$i] > 0)
		{
		$dbHOURS = ($login_sec[$i] / 3600);
		if ($sales[$i] > 0)
			{
			$dbSPH = ( $sales[$i] / $dbHOURS );
				$dbSPH = round($dbSPH, 2);
				$dbSPH = sprintf("%01.2f", $dbSPH);
			}
		else
			{$dbSPH='0.00';}
		$dbHOURS = round($dbHOURS, 2);
		$dbHOURS = sprintf("%01.2f", $dbHOURS);
		}
	else
		{$dbHOURS='0.00';}

	$sph[$i] =		$dbSPH;		
	$hours[$i] =	$dbHOURS;		

	$sphSORT[$i] =		"$dbSPH-----$i";		
	$hoursSORT[$i] =	"$dbHOURS-----$i";		

	$i++;
	}

### Sort by sph if selected
if ($order == 'sph_up')
	{
	sort($sphSORT, SORT_NUMERIC);
	}
if ($order == 'sph_down')
	{
	rsort($sphSORT, SORT_NUMERIC);
	}

$j=0;
while ($j < $rows_to_print)
	{

	$sph_split = explode("-----",$sphSORT[$j]);
	$i = $sph_split[1];

	if (eregi("1$|3$|5$|7$|9$", $j))
		{$bgcolor='bgcolor="#B9CBFD"';} 
	else
		{$bgcolor='bgcolor="#9BB9FB"';}

	echo "<TR $bgcolor>\n";
	echo "<TD ALIGN=LEFT><FONT class=\"data_records_fix_small\">$j</TD>\n";
	echo "<TD><FONT class=\"data_records\">$user_id[$i] </TD>\n";
	echo "<TD><FONT class=\"data_records\">$full_name[$i] </TD>\n";
	echo "<TD><FONT class=\"data_records\">$roleX[$i] </TD>\n";
	echo "<TD><FONT class=\"data_records\">$group[$i] </TD>\n";
	echo "<TD ALIGN=RIGHT><FONT class=\"data_records_fix\"> $calls[$i]</TD>\n";
	echo "<TD ALIGN=RIGHT><FONT class=\"data_records_fix\"> $hours[$i]</TD>\n";
	echo "<TD ALIGN=RIGHT><FONT class=\"data_records_fix\"> $sales[$i]</TD>\n";
	echo "<TD ALIGN=RIGHT><FONT class=\"data_records_fix\"> $sph[$i]</TD>\n";
	echo "</TR>\n";


	$j++;
	}


if ($TOTlogin_sec > 0)
	{
	$TOTdbHOURS = ($TOTlogin_sec / 3600);
	if ($TOTsales > 0)
		{
		$TOTdbSPH = ( $TOTsales / $TOTdbHOURS );
			$TOTdbSPH = round($TOTdbSPH, 2);
			$TOTdbSPH = sprintf("%01.2f", $TOTdbSPH);
		}
	else
		{$TOTdbSPH='0.00';}
	$TOTdbHOURS = round($TOTdbHOURS, 0);
	$TOTdbHOURS = sprintf("%01.0f", $TOTdbHOURS);
	}
else
	{$TOTdbHOURS='0.00';}

$TOTsph =	$TOTdbSPH;		
$TOThours =	$TOTdbHOURS;		


echo "<TR BGCOLOR=#E6E6E6>\n";
echo "<TD ALIGN=LEFT COLSPAN=5><FONT class=\"data_records\">TOTALS</TD>\n";
echo "<TD ALIGN=RIGHT><FONT class=\"data_records_fix\"> $TOTcalls</TD>\n";
echo "<TD ALIGN=RIGHT><FONT class=\"data_records_fix\"> $TOThours</TD>\n";
echo "<TD ALIGN=RIGHT><FONT class=\"data_records_fix\"> $TOTsales</TD>\n";
echo "<TD ALIGN=RIGHT><FONT class=\"data_records_fix\"> $TOTsph</TD>\n";
echo "</TR>\n";

echo "</TABLE>\n";

/*
	$TOTavgWAIT_M = ( ($TOTtotWAIT / $TOTcalls) / 60);
	$TOTavgWAIT_M = round($TOTavgWAIT_M, 2);
	$TOTavgWAIT_M_int = intval("$TOTavgWAIT_M");
	$TOTavgWAIT_S = ($TOTavgWAIT_M - $TOTavgWAIT_M_int);
	$TOTavgWAIT_S = ($TOTavgWAIT_S * 60);
	$TOTavgWAIT_S = round($TOTavgWAIT_S, 0);
	if ($TOTavgWAIT_S < 10) {$TOTavgWAIT_S = "0$TOTavgWAIT_S";}
	$TOTavgWAIT_MS = "$TOTavgWAIT_M_int:$TOTavgWAIT_S";
	$TOTavgWAIT_MS =		sprintf("%6s", $TOTavgWAIT_MS);
		while(strlen($TOTavgWAIT_MS)>6) {$TOTavgWAIT_MS = substr("$TOTavgWAIT_MS", 0, -1);}
*/


echo "\n";

}


?>
</CENTER>
</BODY></HTML>