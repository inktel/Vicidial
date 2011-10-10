<?php 
# fcstats.php
# 
# Copyright (C) 2010  Matt Florell <vicidial@gmail.com>    LICENSE: AGPLv2
#
# CHANGES
#
# 70813-1526 - First Build
# 71008-1436 - Added shift to be defined in dbconnect.php
# 71217-1128 - Changed method for calculating stats
# 71228-1140 - added percentages, cross-day start/stop
# 80328-1139 - adapted for basic fronter/closer stats
# 90310-2132 - Added admin header
# 90508-0644 - Changed to PHP long tags
# 100214-1421 - Sort menu alphabetically
# 100216-0042 - Added popup date selector
#

require("dbconnect.php");

$PHP_AUTH_USER=$_SERVER['PHP_AUTH_USER'];
$PHP_AUTH_PW=$_SERVER['PHP_AUTH_PW'];
$PHP_SELF=$_SERVER['PHP_SELF'];
if (isset($_GET["DB"]))				{$DB=$_GET["DB"];}
	elseif (isset($_POST["DB"]))		{$DB=$_POST["DB"];}
if (isset($_GET["group"]))				{$group=$_GET["group"];}
	elseif (isset($_POST["group"]))		{$group=$_POST["group"];}
if (isset($_GET["query_date"]))				{$query_date=$_GET["query_date"];}
	elseif (isset($_POST["query_date"]))		{$query_date=$_POST["query_date"];}
if (isset($_GET["shift"]))				{$shift=$_GET["shift"];}
	elseif (isset($_POST["shift"]))		{$shift=$_POST["shift"];}
if (isset($_GET["submit"]))				{$submit=$_GET["submit"];}
	elseif (isset($_POST["submit"]))		{$submit=$_POST["submit"];}
if (isset($_GET["ΕΠΙΒΕΒΑΙΩΣΗ"]))				{$ΕΠΙΒΕΒΑΙΩΣΗ=$_GET["ΕΠΙΒΕΒΑΙΩΣΗ"];}
	elseif (isset($_POST["ΕΠΙΒΕΒΑΙΩΣΗ"]))		{$ΕΠΙΒΕΒΑΙΩΣΗ=$_POST["ΕΠΙΒΕΒΑΙΩΣΗ"];}

$PHP_AUTH_USER = ereg_replace("[^0-9a-zA-Z]","",$PHP_AUTH_USER);
$PHP_AUTH_PW = ereg_replace("[^0-9a-zA-Z]","",$PHP_AUTH_PW);

if (strlen($shift)<2) {$shift='ALL';}

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

$NOW_DATE = date("Y-m-d");
$NOW_TIME = date("Y-m-d H:i:s");
$STARTtime = date("U");
if (!isset($group)) {$group = 'CL_TEST_L';}
if (!isset($query_date)) {$query_date = $NOW_DATE;}

$stmt="select group_id from vicidial_inbound_groups order by group_id;";
$rslt=mysql_query($stmt, $link);
if ($DB) {echo "$stmt\n";}
$groups_to_print = mysql_num_rows($rslt);
$i=0;
while ($i < $groups_to_print)
	{
	$row=mysql_fetch_row($rslt);
	$groups[$i] =$row[0];
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

<script language="JavaScript" src="calendar_db.js"></script>
<link rel="stylesheet" href="calendar.css">

<?php 
echo "<META HTTP-EQUIV=\"Content-Type\" CONTENT=\"text/html; charset=utf-8\">\n";
echo "<TITLE>In-Group Fronter-Closer Stats Report</TITLE></HEAD><BODY BGCOLOR=WHITE marginheight=0 marginwidth=0 leftmargin=0 topmargin=0>\n";

	$short_header=1;

	require("admin_header.php");

echo "<TABLE CELLPADDING=4 CELLSPACING=0><TR><TD>";

echo "<FORM ACTION=\"$PHP_SELF\" METHOD=GET name=vicidial_report id=vicidial_report>\n";
echo "<INPUT TYPE=TEXT NAME=query_date SIZE=10 MAXLENGTH=10 VALUE=\"$query_date\">";

?>
<script language="JavaScript">
var o_cal = new tcal ({
	// form name
	'formname': 'vicidial_report',
	// input name
	'controlname': 'query_date'
});
o_cal.a_tpl.yearscroll = false;
// o_cal.a_tpl.weekstart = 1; // Δευτέρα week start
</script>
<?php

echo "<SELECT SIZE=1 NAME=group>\n";
	$o=0;
	while ($groups_to_print > $o)
	{
		if ($groups[$o] == $group) {echo "<option selected value=\"$groups[$o]\">$groups[$o]</option>\n";}
		  else {echo "<option value=\"$groups[$o]\">$groups[$o]</option>\n";}
		$o++;
	}
echo "</SELECT>\n";
echo "<SELECT SIZE=1 NAME=shift>\n";
echo "<option selected value=\"$shift\">$shift</option>\n";
echo "<option value=\"\">--</option>\n";
echo "<option value=\"AM\">AM</option>\n";
echo "<option value=\"PM\">PM</option>\n";
echo "<option value=\"ALL\">ALL</option>\n";
echo "</SELECT>\n";
echo "<INPUT TYPE=hidden NAME=DB VALUE=\"$DB\">\n";
echo "<INPUT TYPE=submit NAME=ΕΠΙΒΕΒΑΙΩΣΗ VALUE=ΥΠΟΒΑΛΛΩ>\n";
echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; <a href=\"./admin.php?ADD=3111&group_id=$group\">ΤΡΟΠΟΠΟΙΗΣΗ</a> | <a href=\"./admin.php?ADD=999999\">ΑΝΑΦΟΡΕΣ</a> </FONT>\n";
echo "</FORM>\n\n";

echo "<PRE><FONT SIZE=2>\n\n";


if (!$group)
{
echo "\n\n";
echo "PLEASE SELECT AN IN-GROUP AND DATE ABOVE THEN CLICK ΕΠΙΒΕΒΑΙΩΣΗ\n";
}

else
{
#	$time_BEGIN=$AM_shift_BEGIN;
#	$time_END=$AM_shift_END;
#$query_date_BEGIN = "$query_date $time_BEGIN";   
#$query_date_END = "$query_date $time_END";

$Cqdate = explode('-',$query_date);

if ($shift == 'AM') 
	{
	$query_date_BEGIN = date("Y-m-d H:i:s", mktime(1, 0, 0, $Cqdate[1], $Cqdate[2], $Cqdate[0]));
	$query_date_END = date("Y-m-d H:i:s", mktime(17, 45, 0, $Cqdate[1], $Cqdate[2], $Cqdate[0]));
	}
if ($shift == 'PM') 
	{
	$query_date_BEGIN = date("Y-m-d H:i:s", mktime(17, 45, 1, $Cqdate[1], $Cqdate[2], $Cqdate[0]));
	$query_date_END = date("Y-m-d H:i:s", mktime(24, 59, 59, $Cqdate[1], $Cqdate[2], $Cqdate[0]));
	}
if ($shift == 'ALL') 
	{
	$query_date_BEGIN = date("Y-m-d H:i:s", mktime(1, 0, 0, $Cqdate[1], $Cqdate[2], $Cqdate[0]));
	$query_date_END = date("Y-m-d H:i:s", mktime(24, 59, 59, $Cqdate[1], $Cqdate[2], $Cqdate[0]));
	}

echo "In-Group Fronter-Closer Stats Report                      $NOW_TIME\n";

echo "\n";
echo "---------- TOTALS FOR $query_date_BEGIN to $query_date_END\n";

$stmt="select count(*) from vicidial_closer_log where call_date >= '$query_date_BEGIN' and call_date <= '$query_date_END' and campaign_id='" . mysql_real_escape_string($group) . "' and status = 'SALE';";
$rslt=mysql_query($stmt, $link);
if ($DB) {echo "$stmt\n";}
$row=mysql_fetch_row($rslt);
$A1_points = ($row[0] * 1);
$A1_points =	sprintf("%10s", $A1_points);
$A1_tally =	sprintf("%10s", $row[0]);

$TOT_tally = ($A1_tally + $A2_tally + $A3_tally + $A4_tally);
$TOT_points = ($A1_points + $A2_points + $A3_points + $A4_points);
$TOT_tally =	sprintf("%10s", $TOT_tally);
$TOT_points =	sprintf("%10s", $TOT_points);

echo "STATUS   CUSTOMERS\n";
echo "SALES:   $A1_tally\n";

echo "\n";









##############################
#########  FRONTER STATS

$TOTagents=0;
$TOTcalls=0;
$TOTsales=0;
$totA1=0;
$totA2=0;
$totA3=0;
$totA4=0;
$totA5=0;
$totA6=0;
$totA7=0;
$totA8=0;
$totA9=0;
$totDROP=0;
$totOTHER=0;

echo "\n";
echo "---------- FRONTER STATS\n";
echo "+--------------------------+-------+--------+--------+------+------+------+\n";
echo "| ΧΕΙΡΙΣΤΗΣ                   |SUCCESS| XFERS  |SUCCESS%| SALE | DROP |OTHER |\n";
echo "+--------------------------+-------+--------+--------+------+------+------+\n";

#$stmt="select vicidial_xfer_log.user,full_name,count(*) from vicidial_xfer_log,vicidial_users where call_date >= '$query_date_BEGIN' and call_date <= '$query_date_END' and  campaign_id='" . mysql_real_escape_string($group) . "' and vicidial_xfer_log.user is not null and vicidial_xfer_log.user=vicidial_users.user group by vicidial_xfer_log.user;";
$stmt="select user,count(distinct lead_id) from vicidial_xfer_log where call_date >= '$query_date_BEGIN' and call_date <= '$query_date_END' and  campaign_id='" . mysql_real_escape_string($group) . "' and user is not null group by user;";
if ($non_latin > 0) {$rslt=mysql_query("SET NAMES 'UTF8'");}
$rslt=mysql_query($stmt, $link);
if ($DB) {echo "$stmt\n";}
$users_to_print = mysql_num_rows($rslt);
$i=0;
while ($i < $users_to_print)
	{
	$row=mysql_fetch_row($rslt);

	$TOTcalls = ($TOTcalls + $row[1]);

	$userRAW[$i]=$row[0];
	$user[$i] =	sprintf("%-6s", $row[0]);while(strlen($user[$i])>6) {$user[$i] = substr("$user[$i]", 0, -1);}
	$USERcallsRAW[$i] =	$row[1];
	$USERcalls[$i] =	sprintf("%6s", $row[1]);

	$i++;
	}

$i=0;
while ($i < $users_to_print)
	{
	$stmt="select full_name from vicidial_users where user='$userRAW[$i]';";
	if ($non_latin > 0) {$rslt=mysql_query("SET NAMES 'UTF8'");}
	$rslt=mysql_query($stmt, $link);
	if ($DB) {echo "$stmt\n";}
	$names_to_print = mysql_num_rows($rslt);
	if ($names_to_print > 0)
		{
		$row=mysql_fetch_row($rslt);
		if ($non_latin < 1)
			{
			 $full_name[$i] =	sprintf("%-15s", $row[0]); while(strlen($full_name[$i])>15) {$full_name[$i] = substr("$full_name[$i]", 0, -1);}	
			}
		else
			{
			 $full_name[$i] =	sprintf("%-45s", $row[0]); while(mb_strlen($full_name[$i],'utf-8')>15) {$full_name[$i] = mb_substr("$full_name[$i]", 0, -1,'utf-8');}	
			}
		}
	else
		{$full_name[$i] = '               ';}

	$A1=0; $A2=0; $A3=0; $A4=0; $A5=0; $A6=0; $A7=0; $A8=0; $A9=0; $DROP=0; $OTHER=0; $sales=0; 
	$stmt="select vc.status,count(distinct vc.lead_id) from vicidial_xfer_log vx, vicidial_closer_log vc where vx.call_date >= '$query_date_BEGIN' and vx.call_date <= '$query_date_END' and vc.call_date >= '$query_date_BEGIN' and vc.call_date <= '$query_date_END' and  vc.campaign_id='" . mysql_real_escape_string($group) . "' and vx.campaign_id='" . mysql_real_escape_string($group) . "' and vx.user='$userRAW[$i]' and vc.lead_id=vx.lead_id and vc.xfercallid=vx.xfercallid group by vc.status;";
	if ($non_latin > 0) {$rslt=mysql_query("SET NAMES 'UTF8'");}
	$rslt=mysql_query($stmt, $link);
	if ($DB) {echo "$stmt\n";}
	$lead_ids_to_print = mysql_num_rows($rslt);
	$j=0;
	while ($j < $lead_ids_to_print)
		{
		$row=mysql_fetch_row($rslt);
		$recL=0;
		if ( ($row[0]=='SALE') and ($recL < 1) ) {$A1=$row[1]; $recL++; $sales=($sales + $row[1]);}
	#	if ( ($row[0]=='A2') and ($recL < 1) ) {$A2=$row[1]; $recL++; $sales=($sales + $row[1]);}
	#	if ( ($row[0]=='A3') and ($recL < 1) ) {$A3=$row[1]; $recL++; $sales=($sales + $row[1]);}
	#	if ( ($row[0]=='A4') and ($recL < 1) ) {$A4=$row[1]; $recL++; $sales=($sales + $row[1]);}
	#	if ( ($row[0]=='A5') and ($recL < 1) ) {$A5=$row[1]; $recL++;}
	#	if ( ($row[0]=='A6') and ($recL < 1) ) {$A6=$row[1]; $recL++;}
	#	if ( ($row[0]=='A7') and ($recL < 1) ) {$A7=$row[1]; $recL++;}
	#	if ( ($row[0]=='A8') and ($recL < 1) ) {$A8=$row[1]; $recL++;}
	#	if ( ($row[0]=='A9') and ($recL < 1) ) {$A9=$row[1]; $recL++;}
		if ( ($row[0]=='DROP') and ($recL < 1) ) {$DROP=$row[1]; $recL++;}
		if ($recL < 1) {$OTHER=($row[1] + $OTHER); $recL++;}
		$j++;
		}

	$totA1 = ($totA1 + $A1);
	$totA2 = ($totA2 + $A2);
	$totA3 = ($totA3 + $A3);
	$totA4 = ($totA4 + $A4);
	$totA5 = ($totA5 + $A5);
	$totA6 = ($totA6 + $A6);
	$totA7 = ($totA7 + $A7);
	$totA8 = ($totA8 + $A8);
	$totA9 = ($totA9 + $A9);
	$totDROP = ($totDROP + $DROP);
	$totOTHER = ($totOTHER + $OTHER);
	$TOTsales = ($TOTsales + $sales);

	if ( ($USERcallsRAW[$i] > 0) and ($sales > 0) ) {$Spct = ( ($sales / $USERcallsRAW[$i]) * 100);}
		else {$Spct=0;}
	$Spct = round($Spct, 2);
	$Spct =	sprintf("%01.2f", $Spct);
	

	$A1 =	sprintf("%4s", $A1);
	$A2 =	sprintf("%4s", $A2);
	$A3 =	sprintf("%4s", $A3);
	$A4 =	sprintf("%4s", $A4);
	$A5 =	sprintf("%4s", $A5);
	$A6 =	sprintf("%4s", $A6);
	$A7 =	sprintf("%4s", $A7);
	$A8 =	sprintf("%4s", $A8);
	$A9 =	sprintf("%4s", $A9);
	$DROP =	sprintf("%4s", $DROP);
	$OTHER =	sprintf("%4s", $OTHER);
	$sales =	sprintf("%5s", $sales);
	$Spct =	sprintf("%6s", $Spct);

	echo "| $user[$i] - $full_name[$i] | $sales | $USERcalls[$i] | $Spct%| $A1 | $DROP | $OTHER |\n";

	$i++;
	}


if ( ($TOTcalls > 0) and ($TOTsales > 0) ) {$totSpct = ( ($TOTsales / $TOTcalls) * 100);}
	else {$totSpct=0;}
$totSpct = round($totSpct, 2);
$totSpct =	sprintf("%01.2f", $totSpct);
$totSpct =	sprintf("%6s", $totSpct);
	
$TOTagents =	sprintf("%6s", $i);
$TOTcalls =		sprintf("%6s", $TOTcalls);
$TOTsales =		sprintf("%5s", $TOTsales);
$totA1 =		sprintf("%5s", $totA1);
$totA2 =		sprintf("%5s", $totA2);
$totA3 =		sprintf("%5s", $totA3);
$totA4 =		sprintf("%5s", $totA4);
$totA5 =		sprintf("%5s", $totA5);
$totA6 =		sprintf("%5s", $totA6);
$totA7 =		sprintf("%5s", $totA7);
$totA8 =		sprintf("%5s", $totA8);
$totA9 =		sprintf("%5s", $totA9);
$totDROP =		sprintf("%5s", $totDROP);
$totOTHER =		sprintf("%5s", $totOTHER);


$stmt="select avg(queue_seconds) from vicidial_closer_log where call_date >= '$query_date_BEGIN' and call_date <= '$query_date_END' and campaign_id='" . mysql_real_escape_string($group) . "';";
$rslt=mysql_query($stmt, $link);
if ($DB) {echo "$stmt\n";}
$row=mysql_fetch_row($rslt);

$AVGwait = $row[0];
$AVGwait_M = ($AVGwait / 60);
$AVGwait_M = round($AVGwait_M, 2);
$AVGwait_M_int = intval("$AVGwait_M");
$AVGwait_S = ($AVGwait_M - $AVGwait_M_int);
$AVGwait_S = ($AVGwait_S * 60);
$AVGwait_S = round($AVGwait_S, 0);
if ($AVGwait_S < 10) {$AVGwait_S = "0$AVGwait_S";}
$AVGwait_MS = "$AVGwait_M_int:$AVGwait_S";
$AVGwait =		sprintf("%6s", $AVGwait_MS);


echo "+--------------------------+-------+--------+--------+------+------+------+\n";
echo "| TOTAL FRONTERS: $TOTagents   | $TOTsales | $TOTcalls | $totSpct%|$totA1 |$totDROP |$totOTHER |\n";
echo "+--------------------------+-------+--------+--------+------+------+------+\n";
echo "|                          Average time in Queue for customers:    $AVGwait |\n";
echo "+--------------------------+-------+--------+--------+------+------+------+\n";





##############################
#########  CLOSER STATS

$TOTagents=0;
$TOTcalls=0;
$totA1=0;
$totA2=0;
$totA3=0;
$totA4=0;
$totA5=0;
$totA6=0;
$totA7=0;
$totA8=0;
$totA9=0;
$totDROP=0;
$totOTHER=0;
$TOTsales=0;

echo "\n";
echo "---------- CLOSER STATS\n";
echo "+--------------------------+--------+------+------+------+------+-------+\n";
echo "| ΧΕΙΡΙΣΤΗΣ                   | CALLS  | SALE | DROP |OTHER | SALE | CONV %|\n";
echo "+--------------------------+--------+------+------+------+------+-------+\n";

$stmt="select user,count(*) from vicidial_closer_log where call_date >= '$query_date_BEGIN' and call_date <= '$query_date_END' and  campaign_id='" . mysql_real_escape_string($group) . "' and user is not null group by user;";
if ($non_latin > 0) {$rslt=mysql_query("SET NAMES 'UTF8'");}
$rslt=mysql_query($stmt, $link);
if ($DB) {echo "$stmt\n";}
$users_to_print = mysql_num_rows($rslt);
$i=0;
while ($i < $users_to_print)
	{
	$row=mysql_fetch_row($rslt);

	$TOTcalls = ($TOTcalls + $row[1]);
	$userRAW[$i]=$row[0];
	$user[$i] =	sprintf("%-6s", $row[0]);while(strlen($user[$i])>6) {$user[$i] = substr("$user[$i]", 0, -1);}
	$USERcalls[$i] =	sprintf("%6s", $row[1]);
	$USERcallsRAW[$i] =	$row[1];

	$i++;
	}

$i=0;
while ($i < $users_to_print)
	{
	$stmt="select full_name from vicidial_users where user='$userRAW[$i]';";
	if ($non_latin > 0) {$rslt=mysql_query("SET NAMES 'UTF8'");}
	$rslt=mysql_query($stmt, $link);
	if ($DB) {echo "$stmt\n";}
	$names_to_print = mysql_num_rows($rslt);
	if ($names_to_print > 0)
		{
		$row=mysql_fetch_row($rslt);
		if ($non_latin < 1)
			{
			 $full_name[$i] =	sprintf("%-15s", $row[0]); while(strlen($full_name[$i])>15) {$full_name[$i] = substr("$full_name[$i]", 0, -1);}	
			}
		else
			{
			 $full_name[$i] =	sprintf("%-45s", $row[0]); while(mb_strlen($full_name[$i],'utf-8')>15) {$full_name[$i] = mb_substr("$full_name[$i]", 0, -1,'utf-8');}	
			}
		}
	else
		{$full_name[$i] = '               ';}

	$A1=0; $A2=0; $A3=0; $A4=0; $A5=0; $A6=0; $A7=0; $A8=0; $A9=0; $DROP=0; $OTHER=0; $sales=0; $uTOP=0; $uBOT=0; $points=0;
	$stmt="select status,count(*) from vicidial_closer_log where call_date >= '$query_date_BEGIN' and call_date <= '$query_date_END' and  campaign_id='" . mysql_real_escape_string($group) . "' and user='$userRAW[$i]' group by status;";
	if ($non_latin > 0) {$rslt=mysql_query("SET NAMES 'UTF8'");}
	$rslt=mysql_query($stmt, $link);
	if ($DB) {echo "$stmt\n";}
	$lead_ids_to_print = mysql_num_rows($rslt);
	$j=0;
	while ($j < $lead_ids_to_print)
		{
		$row=mysql_fetch_row($rslt);
		$recL=0;
		if ( ($row[0]=='SALE') and ($recL < 1) ) 
			{
			$A1=$row[1]; $recL++; 
			$sales=($sales + $row[1]);
			$points = ($points + ($row[1] * 1) );
			}
		if ( ($row[0]=='A2') and ($recL < 1) ) 
			{
			$A2=$row[1]; $recL++; 
			$sales=($sales + $row[1]); 
			$uTOP=($uTOP + $row[1]);
			$points = ($points + ($row[1] * 2) );
			}
		if ( ($row[0]=='A3') and ($recL < 1) ) 
			{
			$A3=$row[1]; $recL++; 
			$sales=($sales + $row[1]); 
			$uBOT=($uBOT + $row[1]);
			$points = ($points + ($row[1] * 2) );
			}
		if ( ($row[0]=='A4') and ($recL < 1) ) 
			{
			$A4=$row[1]; $recL++; 
			$sales=($sales + $row[1]); 
			$uTOP=($uTOP + $row[1]); 
			$uBOT=($uBOT + $row[1]);
			$points = ($points + ($row[1] * 3) );
			}
#		if ( ($row[0]=='A5') and ($recL < 1) ) {$A5=$row[1]; $recL++;}
#		if ( ($row[0]=='A6') and ($recL < 1) ) {$A6=$row[1]; $recL++;}
#		if ( ($row[0]=='A7') and ($recL < 1) ) {$A7=$row[1]; $recL++;}
#		if ( ($row[0]=='A8') and ($recL < 1) ) {$A8=$row[1]; $recL++;}
#		if ( ($row[0]=='A9') and ($recL < 1) ) {$A9=$row[1]; $recL++;}
		if ( ($row[0]=='DROP') and ($recL < 1) ) {$DROP=$row[1]; $recL++;}
		if ($recL < 1) {$OTHER=($row[1] + $OTHER); $recL++;}
		
		$j++;
		}

	$totA1 = ($totA1 + $A1);	$TOTsales = ($TOTsales + $A1);
	$totA2 = ($totA2 + $A2);	$TOTsales = ($TOTsales + $A2);	$totTOP = ($totTOP + $A2);
	$totA3 = ($totA3 + $A3);	$TOTsales = ($TOTsales + $A3);	$totBOT = ($totBOT + $A3);
	$totA4 = ($totA4 + $A4);	$TOTsales = ($TOTsales + $A4);	$totTOP = ($totTOP + $A4);	$totBOT = ($totBOT + $A4);
	$totA5 = ($totA5 + $A5);
	$totA6 = ($totA6 + $A6);
	$totA7 = ($totA7 + $A7);
	$totA8 = ($totA8 + $A8);
	$totA9 = ($totA9 + $A9);
	$totDROP = ($totDROP + $DROP);
	$totOTHER = ($totOTHER + $OTHER);
	$totPOINTS = ($totPOINTS + $points);

	if ( ($USERcallsRAW[$i] > 0) and ($sales > 0) ) {$Cpct = ( ($sales / ( ($USERcallsRAW[$i] - 0) - $DROP) ) * 100);}
		else {$Cpct=0;}
	$Cpct = round($Cpct, 2);
	$Cpct =	sprintf("%01.2f", $Cpct);
	$Cpct =	sprintf("%6s", $Cpct);

	if ( ($sales > 0) and ($uTOP > 0) ) {$TOP = ( ($uTOP / $sales) * 100);}
		else {$TOP=0;}
	$TOP = round($TOP, 0);
	$TOP =	sprintf("%01.0f", $TOP);
	$TOP =	sprintf("%3s", $TOP);

	if ( ($sales > 0) and ($uBOT > 0) ) {$BOT = ( ($uBOT / $sales) * 100);}
		else {$BOT=0;}
	$BOT = round($BOT, 0);
	$BOT =	sprintf("%01.0f", $BOT);
	$BOT =	sprintf("%3s", $BOT);

	if ( ($USERcallsRAW[$i] > 0) and ($points > 0) ) {$ppc = ($points / ( ($USERcallsRAW[$i] - 0) - $DROP) );}
		else {$ppc=0;}
	$ppc = round($ppc, 2);
	$ppc =	sprintf("%01.2f", $ppc);
	$ppc =	sprintf("%4s", $ppc);


	$A1 =	sprintf("%4s", $A1);
	$A2 =	sprintf("%4s", $A2);
	$A3 =	sprintf("%4s", $A3);
	$A4 =	sprintf("%4s", $A4);
	$A5 =	sprintf("%4s", $A5);
	$A6 =	sprintf("%4s", $A6);
	$A7 =	sprintf("%4s", $A7);
	$A8 =	sprintf("%4s", $A8);
	$A9 =	sprintf("%4s", $A9);
	$DROP =	sprintf("%4s", $DROP);
	$OTHER =	sprintf("%4s", $OTHER);
	$sales =	sprintf("%4s", $sales);

	echo "| $user[$i] - $full_name[$i] | $USERcalls[$i] | $A1 | $DROP | $OTHER | $sales |$Cpct%|\n";

	$i++;
	}


if ( ($TOTcalls > 0) and ($TOTsales > 0) ) {$totCpct = ( ($TOTsales / ( ($TOTcalls - 0) - $totDROP) ) * 100);}
	else {$totCpct=0;}
$totCpct = round($totCpct, 2);
$totCpct =	sprintf("%01.2f", $totCpct);
$totCpct =	sprintf("%6s", $totCpct);
		
if ( ($TOTcalls > 0) and ($totPOINTS > 0) ) {$ppc = ($totPOINTS / ( ($TOTcalls - $totOTHER) - $totDROP) );}
	else {$ppc=0;}
$ppc = round($ppc, 2);
$ppc =	sprintf("%01.2f", $ppc);
$ppc =	sprintf("%4s", $ppc);
		
if ( ($TOTsales > 0) and ($totTOP > 0) ) {$TOP = ( ($totTOP / $TOTsales) * 100);}
	else {$TOP=0;}
$TOP = round($TOP, 0);
$TOP =	sprintf("%01.0f", $TOP);
$TOP =	sprintf("%3s", $TOP);

if ( ($TOTsales > 0) and ($totBOT > 0) ) {$BOT = ( ($totBOT / $TOTsales) * 100);}
	else {$BOT=0;}
$BOT = round($BOT, 0);
$BOT =	sprintf("%01.0f", $BOT);
$BOT =	sprintf("%3s", $BOT);

$TOTagents =	sprintf("%6s", $i);
$TOTcalls =		sprintf("%6s", $TOTcalls);
$totA1 =		sprintf("%5s", $totA1);
$totA2 =		sprintf("%5s", $totA2);
$totA3 =		sprintf("%5s", $totA3);
$totA4 =		sprintf("%5s", $totA4);
$totA5 =		sprintf("%5s", $totA5);
$totA6 =		sprintf("%5s", $totA6);
$totA7 =		sprintf("%5s", $totA7);
$totA8 =		sprintf("%5s", $totA8);
$totA9 =		sprintf("%5s", $totA9);
$totDROP =		sprintf("%5s", $totDROP);
$totOTHER =		sprintf("%5s", $totOTHER);
$TOTsales =		sprintf("%5s", $TOTsales);

echo "+--------------------------+--------+------+------+------+------+-------+\n";
echo "| TOTAL CLOSERS:  $TOTagents   | $TOTcalls |$totA1 |$totDROP |$totOTHER |$TOTsales |$totCpct%|\n";
echo "+--------------------------+--------+------+------+------+------+-------+\n";









$ENDtime = date("U");
$RUNtime = ($ENDtime - $STARTtime);
if ($DB) {echo "\nRun Time: $RUNtime seconds\n";}
}





?>
</PRE>
</TD></TR></TABLE>

</BODY></HTML>

