<?php 
# AST_CLOSERstats.php
# 
# Copyright (C) 2010  Matt Florell <vicidial@gmail.com>    LICENSE: AGPLv2
#
# CHANGES
#
# 60619-1714 - Added variable filtering to eliminate SQL injection attack threat
#            - Added required user/pass to gain access to this page
# 60905-1326 - Added queue time stats
# 71008-1436 - Added shift to be defined in dbconnect.php
# 71025-0021 - Added status breakdown
# 71218-1155 - Added end_date for multi-day reports
# 80430-1920 - Added Customer hangup cause stats
# 80709-0331 - Added time stats to call statuses
# 80722-2149 - Added Status Category stats
# 81015-0705 - Added IVR calls count
# 81024-0037 - Added multi-select inbound-groups
# 81105-2118 - Added Answered calls 15-minute breakdown
# 81109-2340 - Added custom indicators section
# 90116-1040 - Rewrite of the 15-minute sections to speed it up and allow multi-day calculations
# 90310-2037 - Admin header
# 90508-0644 - Changed to PHP long tags
# 90524-2231 - Changed to use functions.php for seconds to HH:MM:SS conversion
# 90801-0921 - Added in-group name to pulldown
# 91214-0955 - Added INITIAL QUEUE POSITION BREAKDOWN
# 100206-1454 - Fixed TMR(service level) calculation
# 100214-1421 - Sort menu alphabetically
# 100216-0042 - Added popup date selector
#

require("dbconnect.php");
require("functions.php");

$PHP_AUTH_USER=$_SERVER['PHP_AUTH_USER'];
$PHP_AUTH_PW=$_SERVER['PHP_AUTH_PW'];
$PHP_SELF=$_SERVER['PHP_SELF'];
if (isset($_GET["group"]))				{$group=$_GET["group"];}
	elseif (isset($_POST["group"]))		{$group=$_POST["group"];}
if (isset($_GET["query_date"]))				{$query_date=$_GET["query_date"];}
	elseif (isset($_POST["query_date"]))	{$query_date=$_POST["query_date"];}
if (isset($_GET["end_date"]))			{$end_date=$_GET["end_date"];}
	elseif (isset($_POST["end_date"]))	{$end_date=$_POST["end_date"];}
if (isset($_GET["shift"]))				{$shift=$_GET["shift"];}
	elseif (isset($_POST["shift"]))		{$shift=$_POST["shift"];}
if (isset($_GET["submit"]))				{$submit=$_GET["submit"];}
	elseif (isset($_POST["submit"]))	{$submit=$_POST["submit"];}
if (isset($_GET["VALIDER"]))				{$VALIDER=$_GET["VALIDER"];}
	elseif (isset($_POST["VALIDER"]))	{$VALIDER=$_POST["VALIDER"];}
if (isset($_GET["DB"]))					{$DB=$_GET["DB"];}
	elseif (isset($_POST["DB"]))		{$DB=$_POST["DB"];}

$PHP_AUTH_USER = ereg_replace("[^0-9a-zA-Z]","",$PHP_AUTH_USER);
$PHP_AUTH_PW = ereg_replace("[^0-9a-zA-Z]","",$PHP_AUTH_PW);

$MT[0]='0';
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

$stmt = "SELECT local_gmt FROM servers where active='Y' limit 1;";
$rslt=mysql_query($stmt, $link);
if ($DB) {echo "$stmt\n";}
$gmt_conf_ct = mysql_num_rows($rslt);
$dst = date("I");
if ($gmt_conf_ct > 0)
	{
	$row=mysql_fetch_row($rslt);
	$local_gmt =		$row[0];
	$epoch_offset =		(($local_gmt + $dst) * 3600);
	}

$stmt="SELECT count(*) from vicidial_users where user='$PHP_AUTH_USER' and pass='$PHP_AUTH_PW' and user_level >= 7 and view_reports='1';";
if ($DB) {echo "|$stmt|\n";}
if ($non_latin > 0) {$rslt=mysql_query("SET NAMES 'UTF8'");}
$rslt=mysql_query($stmt, $link);
$row=mysql_fetch_row($rslt);
$auth=$row[0];


if( (strlen($PHP_AUTH_USER)<2) or (strlen($PHP_AUTH_PW)<2) or (!$auth))
	{
    Header("WWW-Authenticate: Basic realm=\"VICI-PROJECTS\"");
    Header("HTTP/1.0 401 Unauthorized");
    echo "Login ou mot de passe invalide: |$PHP_AUTH_USER|$PHP_AUTH_PW|\n";
    exit;
	}

$NOW_DATE = date("Y-m-d");
$NOW_TIME = date("Y-m-d H:i:s");
$STARTtime = date("U");
if (!isset($group)) {$group = '';}
if (!isset($query_date)) {$query_date = $NOW_DATE;}
if (!isset($end_date)) {$end_date = $NOW_DATE;}

$stmt="select group_id,group_name from vicidial_inbound_groups order by group_id;";
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
	$LISTgroups[$i] =		$row[0];
	$LISTgroup_names[$i] =	$row[1];
	$i++;
	}

$i=0;
$group_string='|';
$group_ct = count($group);
while($i < $group_ct)
	{
	$group_string .= "$group[$i]|";
	$group_SQL .= "'$group[$i]',";
	$groupQS .= "&group[]=$group[$i]";
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


$stmt="select vsc_id,vsc_name from vicidial_status_categories;";
$rslt=mysql_query($stmt, $link);
if ($DB) {echo "$stmt\n";}
$statcats_to_print = mysql_num_rows($rslt);
$i=0;
while ($i < $statcats_to_print)
	{
	$row=mysql_fetch_row($rslt);
	$vsc_id[$i] =	$row[0];
	$vsc_name[$i] =	$row[1];
	$vsc_count[$i] = 0;
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

echo "<script language=\"JavaScript\" src=\"calendar_db.js\"></script>\n";
echo "<link rel=\"stylesheet\" href=\"calendar.css\">\n";

echo "<META HTTP-EQUIV=\"Content-Type\" CONTENT=\"text/html; charset=utf-8\">\n";
echo "<TITLE>Inbound Stats</TITLE></HEAD><BODY BGCOLOR=WHITE marginheight=0 marginwidth=0 leftmargin=0 topmargin=0>\n";

$short_header=1;

require("admin_header.php");

echo "<TABLE CELLPADDING=4 CELLSPACING=0><TR><TD>";

if ($DB > 0)
	{
	echo "<BR>\n";
	echo "$group_ct|$group_string|$group_SQL\n";
	echo "<BR>\n";
	echo "$shift|$query_date|$end_date\n";
	echo "<BR>\n";
	}

echo "<FORM ACTION=\"$PHP_SELF\" METHOD=GET name=vicidial_report id=vicidial_report>\n";
echo "<TABLE BORDER=0><TR><TD VALIGN=TOP>\n";
echo "<INPUT TYPE=HIDDEN NAME=DB VALUE=\"$DB\">\n";
echo "Date Range:<BR>\n";
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
// o_cal.a_tpl.weekstart = 1; // Lundi week start
</script>
<?php

echo " to <INPUT TYPE=TEXT NAME=end_date SIZE=10 MAXLENGTH=10 VALUE=\"$end_date\">";

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

echo "</TD><TD ROWSPAN=2 VALIGN=TOP>\n";
echo "Groupes entrants: \n";
echo "</TD><TD ROWSPAN=2 VALIGN=TOP>\n";
echo "<SELECT SIZE=5 NAME=group[] multiple>\n";
$o=0;
while ($groups_to_print > $o)
	{
	if (ereg("\|$LISTgroups[$o]\|",$group_string)) 
		{echo "<option selected value=\"$LISTgroups[$o]\">$LISTgroups[$o] - $LISTgroup_names[$o]</option>\n";}
	else
		{echo "<option value=\"$LISTgroups[$o]\">$LISTgroups[$o] - $LISTgroup_names[$o]</option>\n";}
	$o++;
	}
echo "</SELECT>\n";
echo "</TD><TD ROWSPAN=2 VALIGN=TOP>\n";
echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; ";
echo "<a href=\"./admin.php?ADD=3111&group_id=$group[0]\">MODIFIER</a> | ";
echo "<a href=\"./admin.php?ADD=999999\">RAPPORTS</a> | ";
echo "<a href=\"./AST_IVRstats.php?query_date=$query_date&end_date=$end_date&shift=$shift$groupQS\">IVR REPORT</a> \n";
echo "</FONT>\n";

echo "</TD></TR>\n";
echo "<TR><TD>\n";

#echo "<SELECT SIZE=1 NAME=group>\n";
#	$o=0;
#	while ($groups_to_print > $o)
#	{
#		if ($groups[$o] == $group) {echo "<option selected value=\"$groups[$o]\">$groups[$o]</option>\n";}
#		  else {echo "<option value=\"$groups[$o]\">$groups[$o]</option>\n";}
#		$o++;
#	}
#echo "</SELECT>\n";
echo "Shift: <SELECT SIZE=1 NAME=shift>\n";
echo "<option selected value=\"$shift\">$shift</option>\n";
echo "<option value=\"\">--</option>\n";
echo "<option value=\"AM\">AM</option>\n";
echo "<option value=\"PM\">PM</option>\n";
echo "<option value=\"ALL\">ALL</option>\n";
echo "</SELECT>\n";
echo " &nbsp; <INPUT TYPE=submit NAME=VALIDER VALUE=VALIDER>\n";
echo "</TD></TR></TABLE>\n";
echo "</FORM>\n\n";

echo "<PRE><FONT SIZE=2>\n\n";


if ($groups_to_print < 1)
{
echo "\n\n";
echo "S'IL VOUS PLAÎT CHOISIR UN GROUPE EN DATE ET-dessus et cliquez sur Soumettre\n";
}

else
{
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



echo "Inbound Call Stats: $group_string          $NOW_TIME\n";






if ($group_ct > 1)
	{
	echo "\n";
	echo "---------- MULTI-GROUP BREAKDOWN:\n";
	echo "+----------------------+---------+---------+---------+---------+\n";
	echo "| IN-GROUP             | CALLS   | DROPS   | DROP %  | IVR     |\n";
	echo "+----------------------+---------+---------+---------+---------+\n";

	$i=0;
	while($i < $group_ct)
		{
		$stmt="select count(*),sum(length_in_sec) from vicidial_closer_log where call_date >= '$query_date_BEGIN' and call_date <= '$query_date_END' and campaign_id='$group[$i]';";
		$rslt=mysql_query($stmt, $link);
		if ($DB) {echo "$stmt\n";}
		$row=mysql_fetch_row($rslt);

		$stmt="select count(*) from live_inbound_log where start_time >= '$query_date_BEGIN' and start_time <= '$query_date_END' and comment_a='$group[$i]' and comment_b='START';";
		$rslt=mysql_query($stmt, $link);
		if ($DB) {echo "$stmt\n";}
		$rowx=mysql_fetch_row($rslt);

		$stmt="select count(*),sum(length_in_sec) from vicidial_closer_log where call_date >= '$query_date_BEGIN' and call_date <= '$query_date_END' and campaign_id='$group[$i]' and status IN('DROP','XDROP') and (length_in_sec <= 49999 or length_in_sec is null);";
		$rslt=mysql_query($stmt, $link);
		if ($DB) {echo "$stmt\n";}
		$rowy=mysql_fetch_row($rslt);

		$groupDISPLAY =	sprintf("%20s", $group[$i]);
		$gTOTALcalls =	sprintf("%7s", $row[0]);
		$gIVRcalls =	sprintf("%7s", $rowx[0]);
		$gDROPcalls =	sprintf("%7s", $rowy[0]);
		if ( ($gDROPcalls < 1) or ($gTOTALcalls < 1) )
			{$gDROPpercent = '0';}
		else
			{
			$gDROPpercent = (($gDROPcalls / $gTOTALcalls) * 100);
			$gDROPpercent = round($gDROPpercent, 2);
			}
		$gDROPpercent =	sprintf("%6s", $gDROPpercent);

		echo "| $groupDISPLAY | $gTOTALcalls | $gDROPcalls | $gDROPpercent% | $gIVRcalls |\n";
		$i++;
		}

	echo "+----------------------+---------+---------+---------+---------+\n";

	}


echo "\n";
echo "Time range: $query_date_BEGIN to $query_date_END\n\n";
echo "---------- TOTALS\n";

$stmt="select count(*),sum(length_in_sec) from vicidial_closer_log where call_date >= '$query_date_BEGIN' and call_date <= '$query_date_END' and campaign_id IN($group_SQL);";
$rslt=mysql_query($stmt, $link);
if ($DB) {echo "$stmt\n";}
$row=mysql_fetch_row($rslt);

$stmt="select count(*),sum(queue_seconds) from vicidial_closer_log where call_date >= '$query_date_BEGIN' and call_date <= '$query_date_END' and campaign_id IN($group_SQL) and status NOT IN('DROP','XDROP','HXFER','QVMAIL','HOLDTO','LIVE','QUEUE','TIMEOT','AFTHRS','NANQUE','INBND');";
$rslt=mysql_query($stmt, $link);
if ($DB) {echo "$stmt\n";}
$rowy=mysql_fetch_row($rslt);

$stmt="select count(*) from live_inbound_log where start_time >= '$query_date_BEGIN' and start_time <= '$query_date_END' and comment_a IN($group_SQL) and comment_b='START';";
$rslt=mysql_query($stmt, $link);
if ($DB) {echo "$stmt\n";}
$rowx=mysql_fetch_row($rslt);

$TOTALcalls =	sprintf("%10s", $row[0]);
$IVRcalls =	sprintf("%10s", $rowx[0]);
$TOTALsec =		$row[1];
if ( ($row[0] < 1) or ($TOTALsec < 1) )
	{$average_call_seconds = '         0';}
else
	{
	$average_call_seconds = ($TOTALsec / $row[0]);
	$average_call_seconds = round($average_call_seconds, 0);
	$average_call_seconds =	sprintf("%10s", $average_call_seconds);
	}
$REPONSEEDcalls  =	sprintf("%10s", $rowy[0]);
if ( ($REPONSEEDcalls < 1) or ($TOTALcalls < 1) )
	{$REPONSEEDpercent = '0';}
else
	{
	$REPONSEEDpercent = (($REPONSEEDcalls / $TOTALcalls) * 100);
	$REPONSEEDpercent = round($REPONSEEDpercent, 0);
	}
if ( ($rowy[0] < 1) or ($REPONSEEDcalls < 1) )
	{$average_answer_seconds = '         0';}
else
	{
	$average_answer_seconds = ($rowy[1] / $rowy[0]);
	$average_answer_seconds = round($average_answer_seconds, 2);
	$average_answer_seconds =	sprintf("%10s", $average_answer_seconds);
	}


echo "Total des appels pris en Dans ce groupe:        $TOTALcalls\n";
echo "Average Call Length for all Calls:            $average_call_seconds seconds\n";
echo "Answered Calls:                               $REPONSEEDcalls  $REPONSEEDpercent%\n";
echo "Average queue time for Answered Calls:        $average_answer_seconds seconds\n";
echo "Calls taken into the IVR for this In-Groupe:   $IVRcalls\n";


echo "\n";
echo "---------- ABANDONNES\n";

$stmt="select count(*),sum(length_in_sec) from vicidial_closer_log where call_date >= '$query_date_BEGIN' and call_date <= '$query_date_END' and campaign_id IN($group_SQL) and status IN('DROP','XDROP') and (length_in_sec <= 49999 or length_in_sec is null);";
$rslt=mysql_query($stmt, $link);
if ($DB) {echo "$stmt\n";}
$row=mysql_fetch_row($rslt);

$DROPcalls =	sprintf("%10s", $row[0]);
if ( ($DROPcalls < 1) or ($TOTALcalls < 1) )
	{$DROPpercent = '0';}
else
	{
	$DROPpercent = (($DROPcalls / $TOTALcalls) * 100);
	$DROPpercent = round($DROPpercent, 0);
	}

if ( ($row[0] < 1) or ($row[1] < 1) )
	{
	$average_hold_seconds = '         0';
	}
else
	{
	$average_hold_seconds = ($row[1] / $row[0]);
	$average_hold_seconds = round($average_hold_seconds, 0);
	$average_hold_seconds =	sprintf("%10s", $average_hold_seconds);
	}
if ( ($REPONSEEDcalls < 1) or ($DROPcalls < 1) )
	{$DROP_REPONSEEDpercent = '0';}
else
	{
	$DROP_REPONSEEDpercent = (($DROPcalls / $REPONSEEDcalls) * 100);
	$DROP_REPONSEEDpercent = round($DROP_REPONSEEDpercent, 0);
	}

echo "Total des appels abandonnés: $DROPcalls  $DROPpercent%               drop/answered: $DROP_REPONSEEDpercent%\n";
echo "Average hold time for DROP Calls:             $average_hold_seconds seconds\n";




if (strlen($group_SQL)>3)
	{
	$stmt = "SELECT answer_sec_pct_rt_stat_one,answer_sec_pct_rt_stat_two from vicidial_inbound_groups where group_id IN($group_SQL) order by answer_sec_pct_rt_stat_one desc limit 1;";
	$rslt=mysql_query($stmt, $link);
	if ($DB) {echo "$stmt\n";}
	$row=mysql_fetch_row($rslt);
	$Sanswer_sec_pct_rt_stat_one = $row[0];
	$Sanswer_sec_pct_rt_stat_two = $row[1];

	$stmt = "SELECT count(*) from vicidial_closer_log where campaign_id IN($group_SQL) and call_date >= '$query_date_BEGIN' and call_date <= '$query_date_END' and queue_seconds <= $Sanswer_sec_pct_rt_stat_one and status NOT IN('DROP','XDROP','HXFER','QVMAIL','HOLDTO','LIVE','QUEUE','TIMEOT','AFTHRS','NANQUE','INBND');";
	$rslt=mysql_query($stmt, $link);
	if ($DB) {echo "$stmt\n";}
	$row=mysql_fetch_row($rslt);
	$answer_sec_pct_rt_stat_one = $row[0];

	$stmt = "SELECT count(*) from vicidial_closer_log where campaign_id IN($group_SQL) and call_date >= '$query_date_BEGIN' and call_date <= '$query_date_END' and queue_seconds <= $Sanswer_sec_pct_rt_stat_two and status NOT IN('DROP','XDROP','HXFER','QVMAIL','HOLDTO','LIVE','QUEUE','TIMEOT','AFTHRS','NANQUE','INBND');";
	$rslt=mysql_query($stmt, $link);
	if ($DB) {echo "$stmt\n";}
	$row=mysql_fetch_row($rslt);
	$answer_sec_pct_rt_stat_two = $row[0];

	if ( ($REPONSEEDcalls > 0) and ($answer_sec_pct_rt_stat_one > 0) and ($answer_sec_pct_rt_stat_two > 0) )
		{
		$PCTanswer_sec_pct_rt_stat_one = (($answer_sec_pct_rt_stat_one / $REPONSEEDcalls) * 100);
		$PCTanswer_sec_pct_rt_stat_one = round($PCTanswer_sec_pct_rt_stat_one, 0);
		#$PCTanswer_sec_pct_rt_stat_one = sprintf("%10s", $PCTanswer_sec_pct_rt_stat_one);
		$PCTanswer_sec_pct_rt_stat_two = (($answer_sec_pct_rt_stat_two / $REPONSEEDcalls) * 100);
		$PCTanswer_sec_pct_rt_stat_two = round($PCTanswer_sec_pct_rt_stat_two, 0);
		#$PCTanswer_sec_pct_rt_stat_two = sprintf("%10s", $PCTanswer_sec_pct_rt_stat_two);
		}
	}
echo "\n";
echo "---------- CUSTOM INDICATORS\n";
echo "GDE (Answered/Total des appels pris in to this In-Group):  $REPONSEEDpercent%\n";
echo "ACR (Dropped/Answered):                                $DROP_REPONSEEDpercent%\n";
echo "TMR1 (Answered within $Sanswer_sec_pct_rt_stat_one seconds/Answered):            $PCTanswer_sec_pct_rt_stat_one%\n";
echo "TMR2 (Answered within $Sanswer_sec_pct_rt_stat_two seconds/Answered):            $PCTanswer_sec_pct_rt_stat_two%\n";


# GET LIST OF ALL STATUSES and create SQL from human_answered statuses
$q=0;
$stmt = "SELECT status,status_name,human_answered,category from vicidial_statuses;";
$rslt=mysql_query($stmt, $link);
if ($DB) {echo "$stmt\n";}
$statuses_to_print = mysql_num_rows($rslt);
$p=0;
while ($p < $statuses_to_print)
	{
	$row=mysql_fetch_row($rslt);
	$status[$q] =			$row[0];
	$status_name[$q] =		$row[1];
	$human_answered[$q] =	$row[2];
	$category[$q] =			$row[3];
	$statname_list["$status[$q]"] = "$status_name[$q]";
	$statcat_list["$status[$q]"] = "$category[$q]";
	$q++;
	$p++;
	}
$stmt = "SELECT status,status_name,human_answered,category from vicidial_campaign_statuses;";
$rslt=mysql_query($stmt, $link);
if ($DB) {echo "$stmt\n";}
$statuses_to_print = mysql_num_rows($rslt);
$p=0;
while ($p < $statuses_to_print)
	{
	$row=mysql_fetch_row($rslt);
	$status[$q] =			$row[0];
	$status_name[$q] =		$row[1];
	$human_answered[$q] =	$row[2];
	$category[$q] =			$row[3];
	$statname_list["$status[$q]"] = "$status_name[$q]";
	$statcat_list["$status[$q]"] = "$category[$q]";
	$q++;
	$p++;
	}

##############################
#########  CALL QUEUE STATS
echo "\n";
echo "---------- QUEUE STATS\n";

$stmt="select count(*),sum(queue_seconds) from vicidial_closer_log where call_date >= '$query_date_BEGIN' and call_date <= '$query_date_END' and campaign_id IN($group_SQL) and (queue_seconds > 0);";
$rslt=mysql_query($stmt, $link);
if ($DB) {echo "$stmt\n";}
$row=mysql_fetch_row($rslt);

$QUEUEcalls =	sprintf("%10s", $row[0]);
if ( ($QUEUEcalls < 1) or ($TOTALcalls < 1) )
	{$QUEUEpercent = '0';}
else
	{
	$QUEUEpercent = (($QUEUEcalls / $TOTALcalls) * 100);
	$QUEUEpercent = round($QUEUEpercent, 0);
	}

if ( ($row[0] < 1) or ($row[1] < 1) )
	{$average_queue_seconds = '         0';}
else
	{
	$average_queue_seconds = ($row[1] / $row[0]);
	$average_queue_seconds = round($average_queue_seconds, 2);
	$average_queue_seconds = sprintf("%10.2f", $average_queue_seconds);
	}

if ( ($TOTALcalls < 1) or ($row[1] < 1) )
	{$average_total_queue_seconds = '         0';}
else
	{
	$average_total_queue_seconds = ($row[1] / $TOTALcalls);
	$average_total_queue_seconds = round($average_total_queue_seconds, 2);
	$average_total_queue_seconds = sprintf("%10.2f", $average_total_queue_seconds);
	}

echo "Total Calls That entered Queue:               $QUEUEcalls  $QUEUEpercent%\n";
echo "Average QUEUE Length for queue calls:         $average_queue_seconds seconds\n";
echo "Average QUEUE Length across all calls:        $average_total_queue_seconds seconds\n";



##############################
#########  CALL HOLD TIME BREAKDOWN IN SECONDS

$TOTALcalls = 0;

echo "\n";
echo "---------- CALL TIME HOLD RÉPARTITION EN SECONDES\n";
echo "+-------------------------------------------------------------------------------------------+------------+\n";
echo "|     0     5    10    15    20    25    30    35    40    45    50    55    60    90   +90 | TOTAL      |\n";
echo "+-------------------------------------------------------------------------------------------+------------+\n";

$stmt="select count(*),queue_seconds from vicidial_closer_log where call_date >= '$query_date_BEGIN' and call_date <= '$query_date_END' and  campaign_id IN($group_SQL) group by queue_seconds;";
$rslt=mysql_query($stmt, $link);
if ($DB) {echo "$stmt\n";}
$reasons_to_print = mysql_num_rows($rslt);
$i=0;
while ($i < $reasons_to_print)
	{
	$row=mysql_fetch_row($rslt);

	$TOTALcalls = ($TOTALcalls + $row[0]);

	if ($row[1] == 0) {$hd_0 = ($hd_0 + $row[0]);}
	if ( ($row[1] > 0) and ($row[1] <= 5) ) {$hd_5 = ($hd_5 + $row[0]);}
	if ( ($row[1] > 5) and ($row[1] <= 10) ) {$hd10 = ($hd10 + $row[0]);}
	if ( ($row[1] > 10) and ($row[1] <= 15) ) {$hd15 = ($hd15 + $row[0]);}
	if ( ($row[1] > 15) and ($row[1] <= 20) ) {$hd20 = ($hd20 + $row[0]);}
	if ( ($row[1] > 20) and ($row[1] <= 25) ) {$hd25 = ($hd25 + $row[0]);}
	if ( ($row[1] > 25) and ($row[1] <= 30) ) {$hd30 = ($hd30 + $row[0]);}
	if ( ($row[1] > 30) and ($row[1] <= 35) ) {$hd35 = ($hd35 + $row[0]);}
	if ( ($row[1] > 35) and ($row[1] <= 40) ) {$hd40 = ($hd40 + $row[0]);}
	if ( ($row[1] > 40) and ($row[1] <= 45) ) {$hd45 = ($hd45 + $row[0]);}
	if ( ($row[1] > 45) and ($row[1] <= 50) ) {$hd50 = ($hd50 + $row[0]);}
	if ( ($row[1] > 50) and ($row[1] <= 55) ) {$hd55 = ($hd55 + $row[0]);}
	if ( ($row[1] > 55) and ($row[1] <= 60) ) {$hd60 = ($hd60 + $row[0]);}
	if ( ($row[1] > 60) and ($row[1] <= 90) ) {$hd90 = ($hd90 + $row[0]);}
	if ($row[1] > 90) {$hd99 = ($hd99 + $row[0]);}
	$i++;
	}

$hd_0 =	sprintf("%5s", $hd_0);
$hd_5 =	sprintf("%5s", $hd_5);
$hd10 =	sprintf("%5s", $hd10);
$hd15 =	sprintf("%5s", $hd15);
$hd20 =	sprintf("%5s", $hd20);
$hd25 =	sprintf("%5s", $hd25);
$hd30 =	sprintf("%5s", $hd30);
$hd35 =	sprintf("%5s", $hd35);
$hd40 =	sprintf("%5s", $hd40);
$hd45 =	sprintf("%5s", $hd45);
$hd50 =	sprintf("%5s", $hd50);
$hd55 =	sprintf("%5s", $hd55);
$hd60 =	sprintf("%5s", $hd60);
$hd90 =	sprintf("%5s", $hd90);
$hd99 =	sprintf("%5s", $hd99);

$TOTALcalls =		sprintf("%10s", $TOTALcalls);

echo "| $hd_0 $hd_5 $hd10 $hd15 $hd20 $hd25 $hd30 $hd35 $hd40 $hd45 $hd50 $hd55 $hd60 $hd90 $hd99 | $TOTALcalls |\n";
echo "+-------------------------------------------------------------------------------------------+------------+\n";



##############################
#########  CALL DROP TIME BREAKDOWN IN SECONDS

$BDdropCALLS = 0;

echo "\n";
echo "---------- CALL DROP TIME BREAKDOWN IN SECONDS\n";
echo "+-------------------------------------------------------------------------------------------+------------+\n";
echo "|     0     5    10    15    20    25    30    35    40    45    50    55    60    90   +90 | TOTAL      |\n";
echo "+-------------------------------------------------------------------------------------------+------------+\n";

$stmt="select count(*),queue_seconds from vicidial_closer_log where call_date >= '$query_date_BEGIN' and call_date <= '$query_date_END' and  campaign_id IN($group_SQL) and status IN('DROP','XDROP') group by queue_seconds;";
$rslt=mysql_query($stmt, $link);
if ($DB) {echo "$stmt\n";}
$reasons_to_print = mysql_num_rows($rslt);
$i=0;
while ($i < $reasons_to_print)
	{
	$row=mysql_fetch_row($rslt);

	$BDdropCALLS = ($BDdropCALLS + $row[0]);

	if ($row[1] == 0) {$dd_0 = ($dd_0 + $row[0]);}
	if ( ($row[1] > 0) and ($row[1] <= 5) ) {$dd_5 = ($dd_5 + $row[0]);}
	if ( ($row[1] > 5) and ($row[1] <= 10) ) {$dd10 = ($dd10 + $row[0]);}
	if ( ($row[1] > 10) and ($row[1] <= 15) ) {$dd15 = ($dd15 + $row[0]);}
	if ( ($row[1] > 15) and ($row[1] <= 20) ) {$dd20 = ($dd20 + $row[0]);}
	if ( ($row[1] > 20) and ($row[1] <= 25) ) {$dd25 = ($dd25 + $row[0]);}
	if ( ($row[1] > 25) and ($row[1] <= 30) ) {$dd30 = ($dd30 + $row[0]);}
	if ( ($row[1] > 30) and ($row[1] <= 35) ) {$dd35 = ($dd35 + $row[0]);}
	if ( ($row[1] > 35) and ($row[1] <= 40) ) {$dd40 = ($dd40 + $row[0]);}
	if ( ($row[1] > 40) and ($row[1] <= 45) ) {$dd45 = ($dd45 + $row[0]);}
	if ( ($row[1] > 45) and ($row[1] <= 50) ) {$dd50 = ($dd50 + $row[0]);}
	if ( ($row[1] > 50) and ($row[1] <= 55) ) {$dd55 = ($dd55 + $row[0]);}
	if ( ($row[1] > 55) and ($row[1] <= 60) ) {$dd60 = ($dd60 + $row[0]);}
	if ( ($row[1] > 60) and ($row[1] <= 90) ) {$dd90 = ($dd90 + $row[0]);}
	if ($row[1] > 90) {$dd99 = ($dd99 + $row[0]);}
	$i++;
	}

$dd_0 =	sprintf("%5s", $dd_0);
$dd_5 =	sprintf("%5s", $dd_5);
$dd10 =	sprintf("%5s", $dd10);
$dd15 =	sprintf("%5s", $dd15);
$dd20 =	sprintf("%5s", $dd20);
$dd25 =	sprintf("%5s", $dd25);
$dd30 =	sprintf("%5s", $dd30);
$dd35 =	sprintf("%5s", $dd35);
$dd40 =	sprintf("%5s", $dd40);
$dd45 =	sprintf("%5s", $dd45);
$dd50 =	sprintf("%5s", $dd50);
$dd55 =	sprintf("%5s", $dd55);
$dd60 =	sprintf("%5s", $dd60);
$dd90 =	sprintf("%5s", $dd90);
$dd99 =	sprintf("%5s", $dd99);

$BDdropCALLS =		sprintf("%10s", $BDdropCALLS);

echo "| $dd_0 $dd_5 $dd10 $dd15 $dd20 $dd25 $dd30 $dd35 $dd40 $dd45 $dd50 $dd55 $dd60 $dd90 $dd99 | $BDdropCALLS |\n";
echo "+-------------------------------------------------------------------------------------------+------------+\n";




##############################
#########  CALL ANSWERED TIME AND PERCENT BREAKDOWN IN SECONDS

$BDansweredCALLS = 0;

echo "\n";
echo "           CALL REPONSEED TIME AND PERCENT BREAKDOWN IN SECONDS\n";
echo "          +-------------------------------------------------------------------------------------------+------------+\n";
echo "          |     0     5    10    15    20    25    30    35    40    45    50    55    60    90   +90 | TOTAL      |\n";
echo "----------+-------------------------------------------------------------------------------------------+------------+\n";

$stmt="select count(*),queue_seconds from vicidial_closer_log where call_date >= '$query_date_BEGIN' and call_date <= '$query_date_END' and  campaign_id IN($group_SQL) and status NOT IN('DROP','XDROP','HXFER','QVMAIL','HOLDTO','LIVE','QUEUE','TIMEOT','AFTHRS','NANQUE','INBND') group by queue_seconds;";
$rslt=mysql_query($stmt, $link);
if ($DB) {echo "$stmt\n";}
$reasons_to_print = mysql_num_rows($rslt);
$i=0;
while ($i < $reasons_to_print)
	{
	$row=mysql_fetch_row($rslt);

	$BDansweredCALLS = ($BDansweredCALLS + $row[0]);
	
	### Get interval totals
	if ($row[1] == 0) {$ad_0 = ($ad_0 + $row[0]);}
	if ( ($row[1] > 0) and ($row[1] <= 5) ) {$ad_5 = ($ad_5 + $row[0]);}
	if ( ($row[1] > 5) and ($row[1] <= 10) ) {$ad10 = ($ad10 + $row[0]);}
	if ( ($row[1] > 10) and ($row[1] <= 15) ) {$ad15 = ($ad15 + $row[0]);}
	if ( ($row[1] > 15) and ($row[1] <= 20) ) {$ad20 = ($ad20 + $row[0]);}
	if ( ($row[1] > 20) and ($row[1] <= 25) ) {$ad25 = ($ad25 + $row[0]);}
	if ( ($row[1] > 25) and ($row[1] <= 30) ) {$ad30 = ($ad30 + $row[0]);}
	if ( ($row[1] > 30) and ($row[1] <= 35) ) {$ad35 = ($ad35 + $row[0]);}
	if ( ($row[1] > 35) and ($row[1] <= 40) ) {$ad40 = ($ad40 + $row[0]);}
	if ( ($row[1] > 40) and ($row[1] <= 45) ) {$ad45 = ($ad45 + $row[0]);}
	if ( ($row[1] > 45) and ($row[1] <= 50) ) {$ad50 = ($ad50 + $row[0]);}
	if ( ($row[1] > 50) and ($row[1] <= 55) ) {$ad55 = ($ad55 + $row[0]);}
	if ( ($row[1] > 55) and ($row[1] <= 60) ) {$ad60 = ($ad60 + $row[0]);}
	if ( ($row[1] > 60) and ($row[1] <= 90) ) {$ad90 = ($ad90 + $row[0]);}
	if ($row[1] > 90) {$ad99 = ($ad99 + $row[0]);}
	$i++;
	}

### Calculate cumulative totals
$Cad_0 =$ad_0;
$Cad_5 =($Cad_0 + $ad_5);
$Cad10 =($Cad_5 + $ad10);
$Cad15 =($Cad10 + $ad15);
$Cad20 =($Cad15 + $ad20);
$Cad25 =($Cad20 + $ad25);
$Cad30 =($Cad25 + $ad30);
$Cad35 =($Cad30 + $ad35);
$Cad40 =($Cad35 + $ad40);
$Cad45 =($Cad40 + $ad45);
$Cad50 =($Cad45 + $ad50);
$Cad55 =($Cad50 + $ad55);
$Cad60 =($Cad55 + $ad60);
$Cad90 =($Cad60 + $ad90);
$Cad99 =($Cad90 + $ad99);

### Calculate interval percentages
$pad_0=0; $pad_5=0; $pad10=0; $pad15=0; $pad20=0; $pad25=0; $pad30=0; $pad35=0; $pad40=0; $pad45=0; $pad50=0; $pad55=0; $pad60=0; $pad90=0; $pad99=0; 
$pCad_0=0; $pCad_5=0; $pCad10=0; $pCad15=0; $pCad20=0; $pCad25=0; $pCad30=0; $pCad35=0; $pCad40=0; $pCad45=0; $pCad50=0; $pCad55=0; $pCad60=0; $pCad90=0; $pCad99=0; 
if ( ($BDansweredCALLS > 0) and ($TOTALcalls > 0) )
	{
	if ($ad_0 > 0) {$pad_0 = (($ad_0 / $TOTALcalls) * 100);	$pad_0 = round($pad_0, 0);}
	if ($ad_5 > 0) {$pad_5 = (($ad_5 / $TOTALcalls) * 100);	$pad_5 = round($pad_5, 0);}
	if ($ad10 > 0) {$pad10 = (($ad10 / $TOTALcalls) * 100);	$pad10 = round($pad10, 0);}
	if ($ad15 > 0) {$pad15 = (($ad15 / $TOTALcalls) * 100);	$pad15 = round($pad15, 0);}
	if ($ad20 > 0) {$pad20 = (($ad20 / $TOTALcalls) * 100);	$pad20 = round($pad20, 0);}
	if ($ad25 > 0) {$pad25 = (($ad25 / $TOTALcalls) * 100);	$pad25 = round($pad25, 0);}
	if ($ad30 > 0) {$pad30 = (($ad30 / $TOTALcalls) * 100);	$pad30 = round($pad30, 0);}
	if ($ad35 > 0) {$pad35 = (($ad35 / $TOTALcalls) * 100);	$pad35 = round($pad35, 0);}
	if ($ad40 > 0) {$pad40 = (($ad40 / $TOTALcalls) * 100);	$pad40 = round($pad40, 0);}
	if ($ad45 > 0) {$pad45 = (($ad45 / $TOTALcalls) * 100);	$pad45 = round($pad45, 0);}
	if ($ad50 > 0) {$pad50 = (($ad50 / $TOTALcalls) * 100);	$pad50 = round($pad50, 0);}
	if ($ad55 > 0) {$pad55 = (($ad55 / $TOTALcalls) * 100);	$pad55 = round($pad55, 0);}
	if ($ad60 > 0) {$pad60 = (($ad60 / $TOTALcalls) * 100);	$pad60 = round($pad60, 0);}
	if ($ad90 > 0) {$pad90 = (($ad90 / $TOTALcalls) * 100);	$pad90 = round($pad90, 0);}
	if ($ad99 > 0) {$pad99 = (($ad99 / $TOTALcalls) * 100);	$pad99 = round($pad99, 0);}

	if ($Cad_0 > 0) {$pCad_0 = (($Cad_0 / $TOTALcalls) * 100);	$pCad_0 = round($pCad_0, 0);}
	if ($Cad_5 > 0) {$pCad_5 = (($Cad_5 / $TOTALcalls) * 100);	$pCad_5 = round($pCad_5, 0);}
	if ($Cad10 > 0) {$pCad10 = (($Cad10 / $TOTALcalls) * 100);	$pCad10 = round($pCad10, 0);}
	if ($Cad15 > 0) {$pCad15 = (($Cad15 / $TOTALcalls) * 100);	$pCad15 = round($pCad15, 0);}
	if ($Cad20 > 0) {$pCad20 = (($Cad20 / $TOTALcalls) * 100);	$pCad20 = round($pCad20, 0);}
	if ($Cad25 > 0) {$pCad25 = (($Cad25 / $TOTALcalls) * 100);	$pCad25 = round($pCad25, 0);}
	if ($Cad30 > 0) {$pCad30 = (($Cad30 / $TOTALcalls) * 100);	$pCad30 = round($pCad30, 0);}
	if ($Cad35 > 0) {$pCad35 = (($Cad35 / $TOTALcalls) * 100);	$pCad35 = round($pCad35, 0);}
	if ($Cad40 > 0) {$pCad40 = (($Cad40 / $TOTALcalls) * 100);	$pCad40 = round($pCad40, 0);}
	if ($Cad45 > 0) {$pCad45 = (($Cad45 / $TOTALcalls) * 100);	$pCad45 = round($pCad45, 0);}
	if ($Cad50 > 0) {$pCad50 = (($Cad50 / $TOTALcalls) * 100);	$pCad50 = round($pCad50, 0);}
	if ($Cad55 > 0) {$pCad55 = (($Cad55 / $TOTALcalls) * 100);	$pCad55 = round($pCad55, 0);}
	if ($Cad60 > 0) {$pCad60 = (($Cad60 / $TOTALcalls) * 100);	$pCad60 = round($pCad60, 0);}
	if ($Cad90 > 0) {$pCad90 = (($Cad90 / $TOTALcalls) * 100);	$pCad90 = round($pCad90, 0);}
	if ($Cad99 > 0) {$pCad99 = (($Cad99 / $TOTALcalls) * 100);	$pCad99 = round($pCad99, 0);}

	if ($Cad_0 > 0) {$ApCad_0 = (($Cad_0 / $BDansweredCALLS) * 100);	$ApCad_0 = round($ApCad_0, 0);}
	if ($Cad_5 > 0) {$ApCad_5 = (($Cad_5 / $BDansweredCALLS) * 100);	$ApCad_5 = round($ApCad_5, 0);}
	if ($Cad10 > 0) {$ApCad10 = (($Cad10 / $BDansweredCALLS) * 100);	$ApCad10 = round($ApCad10, 0);}
	if ($Cad15 > 0) {$ApCad15 = (($Cad15 / $BDansweredCALLS) * 100);	$ApCad15 = round($ApCad15, 0);}
	if ($Cad20 > 0) {$ApCad20 = (($Cad20 / $BDansweredCALLS) * 100);	$ApCad20 = round($ApCad20, 0);}
	if ($Cad25 > 0) {$ApCad25 = (($Cad25 / $BDansweredCALLS) * 100);	$ApCad25 = round($ApCad25, 0);}
	if ($Cad30 > 0) {$ApCad30 = (($Cad30 / $BDansweredCALLS) * 100);	$ApCad30 = round($ApCad30, 0);}
	if ($Cad35 > 0) {$ApCad35 = (($Cad35 / $BDansweredCALLS) * 100);	$ApCad35 = round($ApCad35, 0);}
	if ($Cad40 > 0) {$ApCad40 = (($Cad40 / $BDansweredCALLS) * 100);	$ApCad40 = round($ApCad40, 0);}
	if ($Cad45 > 0) {$ApCad45 = (($Cad45 / $BDansweredCALLS) * 100);	$ApCad45 = round($ApCad45, 0);}
	if ($Cad50 > 0) {$ApCad50 = (($Cad50 / $BDansweredCALLS) * 100);	$ApCad50 = round($ApCad50, 0);}
	if ($Cad55 > 0) {$ApCad55 = (($Cad55 / $BDansweredCALLS) * 100);	$ApCad55 = round($ApCad55, 0);}
	if ($Cad60 > 0) {$ApCad60 = (($Cad60 / $BDansweredCALLS) * 100);	$ApCad60 = round($ApCad60, 0);}
	if ($Cad90 > 0) {$ApCad90 = (($Cad90 / $BDansweredCALLS) * 100);	$ApCad90 = round($ApCad90, 0);}
	if ($Cad99 > 0) {$ApCad99 = (($Cad99 / $BDansweredCALLS) * 100);	$ApCad99 = round($ApCad99, 0);}
	}

### Format variables
$ad_0 = sprintf("%5s", $ad_0);
$ad_5 = sprintf("%5s", $ad_5);
$ad10 = sprintf("%5s", $ad10);
$ad15 = sprintf("%5s", $ad15);
$ad20 = sprintf("%5s", $ad20);
$ad25 = sprintf("%5s", $ad25);
$ad30 = sprintf("%5s", $ad30);
$ad35 = sprintf("%5s", $ad35);
$ad40 = sprintf("%5s", $ad40);
$ad45 = sprintf("%5s", $ad45);
$ad50 = sprintf("%5s", $ad50);
$ad55 = sprintf("%5s", $ad55);
$ad60 = sprintf("%5s", $ad60);
$ad90 = sprintf("%5s", $ad90);
$ad99 = sprintf("%5s", $ad99);
$Cad_0 = sprintf("%5s", $Cad_0);
$Cad_5 = sprintf("%5s", $Cad_5);
$Cad10 = sprintf("%5s", $Cad10);
$Cad15 = sprintf("%5s", $Cad15);
$Cad20 = sprintf("%5s", $Cad20);
$Cad25 = sprintf("%5s", $Cad25);
$Cad30 = sprintf("%5s", $Cad30);
$Cad35 = sprintf("%5s", $Cad35);
$Cad40 = sprintf("%5s", $Cad40);
$Cad45 = sprintf("%5s", $Cad45);
$Cad50 = sprintf("%5s", $Cad50);
$Cad55 = sprintf("%5s", $Cad55);
$Cad60 = sprintf("%5s", $Cad60);
$Cad90 = sprintf("%5s", $Cad90);
$Cad99 = sprintf("%5s", $Cad99);
$pad_0 = sprintf("%4s", $pad_0) . '%';
$pad_5 = sprintf("%4s", $pad_5) . '%';
$pad10 = sprintf("%4s", $pad10) . '%';
$pad15 = sprintf("%4s", $pad15) . '%';
$pad20 = sprintf("%4s", $pad20) . '%';
$pad25 = sprintf("%4s", $pad25) . '%';
$pad30 = sprintf("%4s", $pad30) . '%';
$pad35 = sprintf("%4s", $pad35) . '%';
$pad40 = sprintf("%4s", $pad40) . '%';
$pad45 = sprintf("%4s", $pad45) . '%';
$pad50 = sprintf("%4s", $pad50) . '%';
$pad55 = sprintf("%4s", $pad55) . '%';
$pad60 = sprintf("%4s", $pad60) . '%';
$pad90 = sprintf("%4s", $pad90) . '%';
$pad99 = sprintf("%4s", $pad99) . '%';
$pCad_0 = sprintf("%4s", $pCad_0) . '%';
$pCad_5 = sprintf("%4s", $pCad_5) . '%';
$pCad10 = sprintf("%4s", $pCad10) . '%';
$pCad15 = sprintf("%4s", $pCad15) . '%';
$pCad20 = sprintf("%4s", $pCad20) . '%';
$pCad25 = sprintf("%4s", $pCad25) . '%';
$pCad30 = sprintf("%4s", $pCad30) . '%';
$pCad35 = sprintf("%4s", $pCad35) . '%';
$pCad40 = sprintf("%4s", $pCad40) . '%';
$pCad45 = sprintf("%4s", $pCad45) . '%';
$pCad50 = sprintf("%4s", $pCad50) . '%';
$pCad55 = sprintf("%4s", $pCad55) . '%';
$pCad60 = sprintf("%4s", $pCad60) . '%';
$pCad90 = sprintf("%4s", $pCad90) . '%';
$pCad99 = sprintf("%4s", $pCad99) . '%';
$ApCad_0 = sprintf("%4s", $ApCad_0) . '%';
$ApCad_5 = sprintf("%4s", $ApCad_5) . '%';
$ApCad10 = sprintf("%4s", $ApCad10) . '%';
$ApCad15 = sprintf("%4s", $ApCad15) . '%';
$ApCad20 = sprintf("%4s", $ApCad20) . '%';
$ApCad25 = sprintf("%4s", $ApCad25) . '%';
$ApCad30 = sprintf("%4s", $ApCad30) . '%';
$ApCad35 = sprintf("%4s", $ApCad35) . '%';
$ApCad40 = sprintf("%4s", $ApCad40) . '%';
$ApCad45 = sprintf("%4s", $ApCad45) . '%';
$ApCad50 = sprintf("%4s", $ApCad50) . '%';
$ApCad55 = sprintf("%4s", $ApCad55) . '%';
$ApCad60 = sprintf("%4s", $ApCad60) . '%';
$ApCad90 = sprintf("%4s", $ApCad90) . '%';
$ApCad99 = sprintf("%4s", $ApCad99) . '%';

$BDansweredCALLS =		sprintf("%10s", $BDansweredCALLS);

### Format and output
$answeredTOTALs = "$ad_0 $ad_5 $ad10 $ad15 $ad20 $ad25 $ad30 $ad35 $ad40 $ad45 $ad50 $ad55 $ad60 $ad90 $ad99 | $BDansweredCALLS |";
$answeredCUMULATIVE = "$Cad_0 $Cad_5 $Cad10 $Cad15 $Cad20 $Cad25 $Cad30 $Cad35 $Cad40 $Cad45 $Cad50 $Cad55 $Cad60 $Cad90 $Cad99 | $BDansweredCALLS |";
$answeredINT_PERCENT = "$pad_0 $pad_5 $pad10 $pad15 $pad20 $pad25 $pad30 $pad35 $pad40 $pad45 $pad50 $pad55 $pad60 $pad90 $pad99 |            |";
$answeredCUM_PERCENT = "$pCad_0 $pCad_5 $pCad10 $pCad15 $pCad20 $pCad25 $pCad30 $pCad35 $pCad40 $pCad45 $pCad50 $pCad55 $pCad60 $pCad90 $pCad99 |            |";
$answeredCUM_ANS_PERCENT = "$ApCad_0 $ApCad_5 $ApCad10 $ApCad15 $ApCad20 $ApCad25 $ApCad30 $ApCad35 $ApCad40 $ApCad45 $ApCad50 $ApCad55 $ApCad60 $ApCad90 $ApCad99 |            |";
echo "INTERVAL  | $answeredTOTALs\n";
echo "INT %     | $answeredINT_PERCENT\n";
echo "CUMULATIVE| $answeredCUMULATIVE\n";
echo "CUM %     | $answeredCUM_PERCENT\n";
echo "CUM ANS % | $answeredCUM_ANS_PERCENT\n";
echo "----------+-------------------------------------------------------------------------------------------+------------+\n";





##############################
#########  CALL HANGUP REASON STATS

$TOTALcalls = 0;

echo "\n";
echo "---------- FAIT APPEL Hangup STATS\n";
echo "+----------------------+------------+\n";
echo "| HANGUP REASON        | CALLS      |\n";
echo "+----------------------+------------+\n";

$stmt="select count(*),term_reason from vicidial_closer_log where call_date >= '$query_date_BEGIN' and call_date <= '$query_date_END' and  campaign_id IN($group_SQL) group by term_reason;";
$rslt=mysql_query($stmt, $link);
if ($DB) {echo "$stmt\n";}
$reasons_to_print = mysql_num_rows($rslt);
$i=0;
while ($i < $reasons_to_print)
	{
	$row=mysql_fetch_row($rslt);

	$TOTALcalls = ($TOTALcalls + $row[0]);

	$REASONcount =	sprintf("%10s", $row[0]);while(strlen($REASONcount)>10) {$REASONcount = substr("$REASONcount", 0, -1);}
	$reason =	sprintf("%-20s", $row[1]);while(strlen($reason)>20) {$reason = substr("$reason", 0, -1);}
#	if (ereg("NONE",$reason)) {$reason = 'NO ANSWER           ';}

	echo "| $reason | $REASONcount |\n";

	$i++;
	}

$TOTALcalls =		sprintf("%10s", $TOTALcalls);

echo "+----------------------+------------+\n";
echo "| TOTAL:               | $TOTALcalls |\n";
echo "+----------------------+------------+\n";




##############################
#########  CALL STATUS STATS

$TOTALcalls = 0;

echo "\n";
echo "---------- CALL STATUS STATS\n";
echo "+--------+----------------------+----------------------+------------+------------+----------+----------+\n";
echo "| STATUS | DESCRIPTION          | CATÉGORIE             | CALLS      | TOTAL TIME | AVG TIME |CALLS/HOUR|\n";
echo "+--------+----------------------+----------------------+------------+------------+----------+----------+\n";


## get counts and time totals for all statuses in this campaign
$stmt="select count(*),status,sum(length_in_sec) from vicidial_closer_log where call_date >= '$query_date_BEGIN' and call_date <= '$query_date_END' and  campaign_id IN($group_SQL) group by status;";
$rslt=mysql_query($stmt, $link);
if ($DB) {echo "$stmt\n";}
$statuses_to_print = mysql_num_rows($rslt);
$i=0;
while ($i < $statuses_to_print)
	{
	$row=mysql_fetch_row($rslt);

	$STATUScount =	$row[0];
	$RAWstatus =	$row[1];
	$r=0;  $foundstat=0;
	while ($r < $statcats_to_print)
		{
		if ( ($statcat_list[$RAWstatus] == "$vsc_id[$r]") and ($foundstat < 1) )
			{
			$vsc_count[$r] = ($vsc_count[$r] + $STATUScount);
			}
		$r++;
		}

	$TOTALcalls =	($TOTALcalls + $row[0]);
	if ( ($STATUScount < 1) or ($TOTALsec < 1) )
		{$STATUSrate = 0;}
	else
		{$STATUSrate =	($STATUScount / ($TOTALsec / 3600) );}
	$STATUSrate =	sprintf("%.2f", $STATUSrate);

	$STATUShours =		sec_convert($row[2],'H'); 
	$STATUSavg_sec =	($row[2] / $STATUScount); 
	$STATUSavg =		sec_convert($STATUSavg_sec,'H'); 

	$STATUScount =	sprintf("%10s", $row[0]);while(strlen($STATUScount)>10) {$STATUScount = substr("$STATUScount", 0, -1);}
	$status =	sprintf("%-6s", $row[1]);while(strlen($status)>6) {$status = substr("$status", 0, -1);}
	$STATUShours =	sprintf("%10s", $STATUShours);while(strlen($STATUShours)>10) {$STATUShours = substr("$STATUShours", 0, -1);}
	$STATUSavg =	sprintf("%8s", $STATUSavg);while(strlen($STATUSavg)>8) {$STATUSavg = substr("$STATUSavg", 0, -1);}
	$STATUSrate =	sprintf("%8s", $STATUSrate);while(strlen($STATUSrate)>8) {$STATUSrate = substr("$STATUSrate", 0, -1);}

	if ($non_latin < 1)
		{
		$status_name =	sprintf("%-20s", $statname_list[$RAWstatus]); 
		while(strlen($status_name)>20) {$status_name = substr("$status_name", 0, -1);}	
		$statcat =	sprintf("%-20s", $statcat_list[$RAWstatus]); 
		while(strlen($statcat)>20) {$statcat = substr("$statcat", 0, -1);}	
		}
	else
		{
		$status_name =	sprintf("%-60s", $statname_list[$RAWstatus]); 
		while(mb_strlen($status_name,'utf-8')>20) {$status_name = mb_substr("$status_name", 0, -1,'utf-8');}	
		$statcat =	sprintf("%-60s", $statcat_list[$RAWstatus]); 
		while(mb_strlen($statcat,'utf-8')>20) {$statcat = mb_substr("$statcat", 0, -1,'utf-8');}	
		}


	echo "| $status | $status_name | $statcat | $STATUScount | $STATUShours | $STATUSavg | $STATUSrate |\n";

	$i++;
	}

if ($TOTALcalls < 1)
	{
	$TOTALhours =	'0:00:00';
	$TOTALavg =		'0:00:00';
	$TOTALrate =	'0.00';
	}
else
	{
	if ( ($TOTALcalls < 1) or ($TOTALsec < 1) )
		{$TOTALrate = 0;}
	else
		{$TOTALrate =	($TOTALcalls / ($TOTALsec / 3600) );}
	$TOTALrate =	sprintf("%.2f", $TOTALrate);

	$TOTALhours =		sec_convert($TOTALsec,'H'); 
	$TOTALavg_sec =		($TOTALsec / $TOTALcalls);
	$TOTALavg =			sec_convert($TOTALavg_sec,'H'); 
	}
$TOTALcalls =	sprintf("%10s", $TOTALcalls);
$TOTALhours =	sprintf("%10s", $TOTALhours);while(strlen($TOTALhours)>10) {$TOTALhours = substr("$TOTALhours", 0, -1);}
$TOTALavg =	sprintf("%8s", $TOTALavg);while(strlen($TOTALavg)>8) {$TOTALavg = substr("$TOTALavg", 0, -1);}
$TOTALrate =	sprintf("%8s", $TOTALrate);while(strlen($TOTALrate)>8) {$TOTALrate = substr("$TOTALrate", 0, -1);}

echo "+--------+----------------------+----------------------+------------+------------+----------+----------+\n";
echo "| TOTAL:                                               | $TOTALcalls | $TOTALhours | $TOTALavg | $TOTALrate |\n";
echo "+------------------------------------------------------+------------+------------+----------+----------+\n";


##############################
#########  STATUS CATEGORY STATS

echo "\n";
echo "---------- CUSTOM STATUS CATÉGORIE STATS\n";
echo "+----------------------+------------+--------------------------------+\n";
echo "| CATÉGORIE             | CALLS      | DESCRIPTION                    |\n";
echo "+----------------------+------------+--------------------------------+\n";

$TOTCATcalls=0;
$r=0;
while ($r < $statcats_to_print)
	{
	if ($vsc_id[$r] != 'UNDEFINED')
		{
		$TOTCATcalls = ($TOTCATcalls + $vsc_count[$r]);
		$category =	sprintf("%-20s", $vsc_id[$r]); while(strlen($category)>20) {$category = substr("$category", 0, -1);}
		$CATcount =	sprintf("%10s", $vsc_count[$r]); while(strlen($CATcount)>10) {$CATcount = substr("$CATcount", 0, -1);}
		$CATname =	sprintf("%-30s", $vsc_name[$r]); while(strlen($CATname)>30) {$CATname = substr("$CATname", 0, -1);}

		echo "| $category | $CATcount | $CATname |\n";
		}

	$r++;
	}

$TOTCATcalls =	sprintf("%10s", $TOTCATcalls); while(strlen($TOTCATcalls)>10) {$TOTCATcalls = substr("$TOTCATcalls", 0, -1);}

echo "+----------------------+------------+--------------------------------+\n";
echo "| TOTAL                | $TOTCATcalls |\n";
echo "+----------------------+------------+\n";


##############################
#########  CALL INITIAL QUEUE POSITION BREAKDOWN

$TOTALcalls = 0;

echo "\n";
echo "---------- CALL INITIAL QUEUE POSITION BREAKDOWN\n";
echo "+-------------------------------------------------------------------------------------+------------+\n";
echo "|     1     2     3     4     5     6     7     8     9    10    15    20    25   +25 | TOTAL      |\n";
echo "+-------------------------------------------------------------------------------------+------------+\n";

$stmt="select count(*),queue_position from vicidial_closer_log where call_date >= '$query_date_BEGIN' and call_date <= '$query_date_END' and  campaign_id IN($group_SQL) group by queue_position;";
$rslt=mysql_query($stmt, $link);
if ($DB) {echo "$stmt\n";}
$positions_to_print = mysql_num_rows($rslt);
$i=0;
while ($i < $positions_to_print)
	{
	$row=mysql_fetch_row($rslt);

	$TOTALcalls = ($TOTALcalls + $row[0]);

	if ( ($row[1] > 0) and ($row[1] <= 1) ) {$qp_1 = ($qp_1 + $row[0]);}
	if ( ($row[1] > 1) and ($row[1] <= 2) ) {$qp_2 = ($qp_2 + $row[0]);}
	if ( ($row[1] > 2) and ($row[1] <= 3) ) {$qp_3 = ($qp_3 + $row[0]);}
	if ( ($row[1] > 3) and ($row[1] <= 4) ) {$qp_4 = ($qp_4 + $row[0]);}
	if ( ($row[1] > 4) and ($row[1] <= 5) ) {$qp_5 = ($qp_5 + $row[0]);}
	if ( ($row[1] > 5) and ($row[1] <= 6) ) {$qp_6 = ($qp_6 + $row[0]);}
	if ( ($row[1] > 6) and ($row[1] <= 7) ) {$qp_7 = ($qp_7 + $row[0]);}
	if ( ($row[1] > 7) and ($row[1] <= 8) ) {$qp_8 = ($qp_8 + $row[0]);}
	if ( ($row[1] > 8) and ($row[1] <= 9) ) {$qp_9 = ($qp_9 + $row[0]);}
	if ( ($row[1] > 9) and ($row[1] <= 10) ) {$qp10 = ($qp10 + $row[0]);}
	if ( ($row[1] > 10) and ($row[1] <= 15) ) {$qp15 = ($qp15 + $row[0]);}
	if ( ($row[1] > 15) and ($row[1] <= 20) ) {$qp20 = ($qp20 + $row[0]);}
	if ( ($row[1] > 20) and ($row[1] <= 25) ) {$qp25 = ($qp25 + $row[0]);}
	if ($row[1] > 25) {$qp99 = ($qp99 + $row[0]);}
	$i++;
	}

$qp_1 =	sprintf("%5s", $qp_1);
$qp_2 =	sprintf("%5s", $qp_2);
$qp_3=	sprintf("%5s", $qp_3);
$qp_4 =	sprintf("%5s", $qp_4);
$qp_5 =	sprintf("%5s", $qp_5);
$qp_6 =	sprintf("%5s", $qp_6);
$qp_7 =	sprintf("%5s", $qp_7);
$qp_8 =	sprintf("%5s", $qp_8);
$qp_9 =	sprintf("%5s", $qp_9);
$qp10 =	sprintf("%5s", $qp10);
$qp15 =	sprintf("%5s", $qp15);
$qp20 =	sprintf("%5s", $qp20);
$qp25 =	sprintf("%5s", $qp25);
$qp99 =	sprintf("%5s", $qp99);

$TOTALcalls =		sprintf("%10s", $TOTALcalls);

echo "| $qp_1 $qp_2 $qp_3 $qp_4 $qp_5 $qp_6 $qp_7 $qp_8 $qp_9 $qp10 $qp15 $qp20 $qp25 $qp99 | $TOTALcalls |\n";
echo "+-------------------------------------------------------------------------------------+------------+\n";



##############################
#########  USER STATS

$TOTagents=0;
$TOTcalls=0;
$TOTtime=0;
$TOTavg=0;

echo "\n";
echo "---------- AGENT STATS\n";
echo "+--------------------------+------------+------------+--------+\n";
echo "| AGENT                    | CALLS      | TIME H:M:S |AVERAGE |\n";
echo "+--------------------------+------------+------------+--------+\n";

$stmt="select vicidial_closer_log.user,full_name,count(*),sum(length_in_sec),avg(length_in_sec) from vicidial_closer_log,vicidial_users where call_date >= '$query_date_BEGIN' and call_date <= '$query_date_END' and  campaign_id IN($group_SQL) and vicidial_closer_log.user is not null and length_in_sec is not null and length_in_sec > 0 and vicidial_closer_log.user=vicidial_users.user group by vicidial_closer_log.user;";
$rslt=mysql_query($stmt, $link);
if ($DB) {echo "$stmt\n";}
$users_to_print = mysql_num_rows($rslt);
$i=0;
while ($i < $users_to_print)
	{
	$row=mysql_fetch_row($rslt);

	$TOTcalls = ($TOTcalls + $row[2]);
	$TOTtime = ($TOTtime + $row[3]);

	$user =			sprintf("%-6s", $row[0]);
	if ($non_latin < 1)
		{
		$full_name =	sprintf("%-15s", $row[1]); while(strlen($full_name)>15) {$full_name = substr("$full_name", 0, -1);}	
		}
	else
		{
		$full_name =	sprintf("%-45s", $row[1]); while(mb_strlen($full_name,'utf-8')>15) {$full_name = mb_substr("$full_name", 0, -1,'utf-8');}	
		}
	$USERcalls =	sprintf("%10s", $row[2]);
	$USERtotTALK =	$row[3];
	$USERavgTALK =	$row[4];

	$USERtotTALK_MS =	sec_convert($USERtotTALK,'H'); 
	$USERavgTALK_MS =	sec_convert($USERavgTALK,'H'); 

	$USERtotTALK_MS =	sprintf("%9s", $USERtotTALK_MS);
	$USERavgTALK_MS =	sprintf("%6s", $USERavgTALK_MS);

	echo "| $user - $full_name | $USERcalls |  $USERtotTALK_MS | $USERavgTALK_MS |\n";

	$i++;
	}

if ($TOTcalls < 1) {$TOTcalls = 0; $TOTavg=0;}
else
	{
	$TOTavg = ($TOTtime / $TOTcalls);
	$TOTavg_MS =	sec_convert($TOTavg,'H'); 
	$TOTavg =		sprintf("%6s", $TOTavg_MS);
	}

$TOTtime_MS =	sec_convert($TOTtime,'H'); 
$TOTtime =		sprintf("%10s", $TOTtime_MS);

$TOTagents =		sprintf("%10s", $i);
$TOTcalls =			sprintf("%10s", $TOTcalls);
$TOTtime =			sprintf("%8s", $TOTtime);
$TOTavg =			sprintf("%6s", $TOTavg);

echo "+--------------------------+------------+------------+--------+\n";
echo "| TOTAL Agents: $TOTagents | $TOTcalls | $TOTtime | $TOTavg |\n";
echo "+--------------------------+------------+------------+--------+\n";


##############################
#########  TIME STATS

echo "\n";
echo "---------- STATISTIQUE TEMPORELLES\n";

echo "<FONT SIZE=0>\n";


##############################
#########  15-minute increment breakdowns of total calls and drops, then answered table
$BDansweredCALLS = 0;
$stmt="SELECT status,queue_seconds,UNIX_TIMESTAMP(call_date),call_date from vicidial_closer_log where call_date >= '$query_date_BEGIN' and call_date <= '$query_date_END' and campaign_id IN($group_SQL);";
$rslt=mysql_query($stmt, $link);
if ($DB) {echo "$stmt\n";}
$calls_to_print = mysql_num_rows($rslt);
$j=0;
while ($j < $calls_to_print)
	{
	$row=mysql_fetch_row($rslt);
	$Cstatus[$j] =	$row[0];
	$Cqueue[$j] =	$row[1];
	$Cepoch[$j] =	$row[2];
	$Cdate[$j] =	$row[3];
	$Crem[$j] = ( ($Cepoch[$j] + $epoch_offset) % 86400); # find the remainder(Modulus) of seconds since start of the day
#	echo "|$Cepoch[$j]|$Crem[$j]|$Cdate[$j]|\n";
	$j++;
	}

### Loop through all call records and gather stats for total call/drop report and answered report
$j=0;
while ($j < $calls_to_print)
	{
	$i=0; $sec=0; $sec_end=900;
	while ($i <= 96)
		{
		if ( ($Crem[$j] >= $sec) and ($Crem[$j] < $sec_end) ) 
			{
			$Ftotal[$i]++;
			if (ereg("DROP",$Cstatus[$j])) {$Fdrop[$i]++;}
			if (!ereg("DROP|XDROP|HXFER|QVMAIL|HOLDTO|LIVE|QUEUE|TIMEOT|AFTHRS|NANQUE|INBND",$Cstatus[$j]))
				{
				$BDansweredCALLS++;
				$Fanswer[$i]++;

				if ($Cqueue[$j] == 0)								{$adB_0[$i]++;}
				if ( ($Cqueue[$j] > 0) and ($Cqueue[$j] <= 5) )		{$adB_5[$i]++;}
				if ( ($Cqueue[$j] > 5) and ($Cqueue[$j] <= 10) )	{$adB10[$i]++;}
				if ( ($Cqueue[$j] > 10) and ($Cqueue[$j] <= 15) )	{$adB15[$i]++;}
				if ( ($Cqueue[$j] > 15) and ($Cqueue[$j] <= 20) )	{$adB20[$i]++;}
				if ( ($Cqueue[$j] > 20) and ($Cqueue[$j] <= 25) )	{$adB25[$i]++;}
				if ( ($Cqueue[$j] > 25) and ($Cqueue[$j] <= 30) )	{$adB30[$i]++;}
				if ( ($Cqueue[$j] > 30) and ($Cqueue[$j] <= 35) )	{$adB35[$i]++;}
				if ( ($Cqueue[$j] > 35) and ($Cqueue[$j] <= 40) )	{$adB40[$i]++;}
				if ( ($Cqueue[$j] > 40) and ($Cqueue[$j] <= 45) )	{$adB45[$i]++;}
				if ( ($Cqueue[$j] > 45) and ($Cqueue[$j] <= 50) )	{$adB50[$i]++;}
				if ( ($Cqueue[$j] > 50) and ($Cqueue[$j] <= 55) )	{$adB55[$i]++;}
				if ( ($Cqueue[$j] > 55) and ($Cqueue[$j] <= 60) )	{$adB60[$i]++;}
				if ( ($Cqueue[$j] > 60) and ($Cqueue[$j] <= 90) )	{$adB90[$i]++;}
				if ($Cqueue[$j] > 90)								{$adB99[$i]++;}
				}

			}
		$sec = ($sec + 900);
		$sec_end = ($sec_end + 900);
		$i++;
		}
	$j++;
	}	##### END going through all records







##### 15-minute total and drops graph
$hi_hour_count=0;
$last_full_record=0;
$i=0;
$h=0;
while ($i <= 96)
	{
	$hour_count[$i] = $Ftotal[$i];
	if ($hour_count[$i] > $hi_hour_count) {$hi_hour_count = $hour_count[$i];}
	if ($hour_count[$i] > 0) {$last_full_record = $i;}
	$drop_count[$i] = $Fdrop[$i];
	$i++;
	}

if ($hi_hour_count < 1)
	{$hour_multiplier = 0;}
else
	{
	$hour_multiplier = (100 / $hi_hour_count);
	#$hour_multiplier = round($hour_multiplier, 0);
	}

echo "<!-- HICOUNT: $hi_hour_count|$hour_multiplier -->\n";
echo "GRAPHIQUE INCREMENTE PAR TRANCHE DE 15 MINUTES DU TOTAL DES APPELS TAKEN INTO THIS IN-GROUP\n";

$k=1;
$Mk=0;
$call_scale = '0';
while ($k <= 102) 
	{
	if ($Mk >= 5) 
		{
		$Mk=0;
		if ( ($k < 1) or ($hour_multiplier <= 0) )
			{$scale_num = 100;}
		else
			{
			$scale_num=($k / $hour_multiplier);
			$scale_num = round($scale_num, 0);
			}
		$LENscale_num = (strlen($scale_num));
		$k = ($k + $LENscale_num);
		$call_scale .= "$scale_num";
		}
	else
		{
		$call_scale .= " ";
		$k++;   $Mk++;
		}
	}


echo "+------+-------------------------------------------------------------------------------------------------------+-------+-------+\n";
#echo "| HOUR | GRAPH IN 15 MINUTE INCREMENTS OF TOTAL INCOMING CALLS FOR THIS GROUP                                  | DROPS | TOTAL |\n";
echo "| HOUR |$call_scale| DROPS | TOTAL |\n";
echo "+------+-------------------------------------------------------------------------------------------------------+-------+-------+\n";

$ZZ = '00';
$i=0;
$h=4;
$hour= -1;
$no_lines_yet=1;

while ($i <= 96)
	{
	$char_counter=0;
	$time = '      ';
	if ($h >= 4) 
		{
		$hour++;
		$h=0;
		if ($hour < 10) {$hour = "0$hour";}
		$time = "+$hour$ZZ+";
		}
	if ($h == 1) {$time = "   15 ";}
	if ($h == 2) {$time = "   30 ";}
	if ($h == 3) {$time = "   45 ";}
	$Ghour_count = $hour_count[$i];
	if ($Ghour_count < 1) 
		{
		if ( ($no_lines_yet) or ($i > $last_full_record) )
			{
			$do_nothing=1;
			}
		else
			{
			$hour_count[$i] =	sprintf("%-5s", $hour_count[$i]);
			echo "|$time|";
			$k=0;   while ($k <= 102) {echo " ";   $k++;}
			echo "| $hour_count[$i] |\n";
			}
		}
	else
		{
		$no_lines_yet=0;
		$Xhour_count = ($Ghour_count * $hour_multiplier);
		$Yhour_count = (99 - $Xhour_count);

		$Gdrop_count = $drop_count[$i];
		if ($Gdrop_count < 1) 
			{
			$hour_count[$i] =	sprintf("%-5s", $hour_count[$i]);

			echo "|$time|<SPAN class=\"green\">";
			$k=0;   while ($k <= $Xhour_count) {echo "*";   $k++;   $char_counter++;}
			echo "*X</SPAN>";   $char_counter++;
			$k=0;   while ($k <= $Yhour_count) {echo " ";   $k++;   $char_counter++;}
				while ($char_counter <= 101) {echo " ";   $char_counter++;}
			echo "| 0     | $hour_count[$i] |\n";

			}
		else
			{
			$Xdrop_count = ($Gdrop_count * $hour_multiplier);

		#	if ($Xdrop_count >= $Xhour_count) {$Xdrop_count = ($Xdrop_count - 1);}

			$XXhour_count = ( ($Xhour_count - $Xdrop_count) - 1 );

			$hour_count[$i] =	sprintf("%-5s", $hour_count[$i]);
			$drop_count[$i] =	sprintf("%-5s", $drop_count[$i]);

			echo "|$time|<SPAN class=\"red\">";
			$k=0;   while ($k <= $Xdrop_count) {echo ">";   $k++;   $char_counter++;}
			echo "D</SPAN><SPAN class=\"green\">";   $char_counter++;
			$k=0;   while ($k <= $XXhour_count) {echo "*";   $k++;   $char_counter++;}
			echo "X</SPAN>";   $char_counter++;
			$k=0;   while ($k <= $Yhour_count) {echo " ";   $k++;   $char_counter++;}
				while ($char_counter <= 102) {echo " ";   $char_counter++;}
			echo "| $drop_count[$i] | $hour_count[$i] |\n";
			}
		}
	
	
	$i++;
	$h++;
	}

echo "+------+-------------------------------------------------------------------------------------------------------+-------+-------+\n\n";






##### Answered wait time breakdown
echo "\n";
echo "---------- CALL REPONSEED TIME BREAKDOWN IN SECONDS\n";
echo "+------+-------------------------------------------------------------------------------------------+------------+\n";
echo "| HOUR |     0     5    10    15    20    25    30    35    40    45    50    55    60    90   +90 | TOTAL      |\n";
echo "+------+-------------------------------------------------------------------------------------------+------------+\n";

$ZZ = '00';
$i=0;
$h=4;
$hour= -1;
$no_lines_yet=1;
while ($i <= 96)
	{
	$char_counter=0;
	$time = '      ';
	if ($h >= 4) 
		{
		$hour++;
		$h=0;
		if ($hour < 10) {$hour = "0$hour";}
		$time = "+$hour$ZZ+";
		$SQLtime = "$hour:$ZZ:00";
		$SQLtimeEND = "$hour:15:00";
		}
	if ($h == 1) {$time = "   15 ";   $SQLtime = "$hour:15:00";   $SQLtimeEND = "$hour:30:00";}
	if ($h == 2) {$time = "   30 ";   $SQLtime = "$hour:30:00";   $SQLtimeEND = "$hour:45:00";}
	if ($h == 3) 
		{
		$time = "   45 ";
		$SQLtime = "$hour:45:00";
		$hourEND = ($hour + 1);
		if ($hourEND < 10) {$hourEND = "0$hourEND";}
		if ($hourEND > 23) {$SQLtimeEND = "23:59:59";}
		else {$SQLtimeEND = "$hourEND:00:00";}
		}


	if (strlen($adB_0[$i]) < 1)  {$adB_0[$i]='-';}
	if (strlen($adB_5[$i]) < 1)  {$adB_5[$i]='-';}
	if (strlen($adB10[$i]) < 1)  {$adB10[$i]='-';}
	if (strlen($adB15[$i]) < 1)  {$adB15[$i]='-';}
	if (strlen($adB20[$i]) < 1)  {$adB20[$i]='-';}
	if (strlen($adB25[$i]) < 1)  {$adB25[$i]='-';}
	if (strlen($adB30[$i]) < 1)  {$adB30[$i]='-';}
	if (strlen($adB35[$i]) < 1)  {$adB35[$i]='-';}
	if (strlen($adB40[$i]) < 1)  {$adB40[$i]='-';}
	if (strlen($adB45[$i]) < 1)  {$adB45[$i]='-';}
	if (strlen($adB50[$i]) < 1)  {$adB50[$i]='-';}
	if (strlen($adB55[$i]) < 1)  {$adB55[$i]='-';}
	if (strlen($adB60[$i]) < 1)  {$adB60[$i]='-';}
	if (strlen($adB90[$i]) < 1)  {$adB90[$i]='-';}
	if (strlen($adB99[$i]) < 1)  {$adB99[$i]='-';}
	if (strlen($Fanswer[$i]) < 1)  {$Fanswer[$i]='0';}

	$adB_0[$i] = sprintf("%5s", $adB_0[$i]);
	$adB_5[$i] = sprintf("%5s", $adB_5[$i]);
	$adB10[$i] = sprintf("%5s", $adB10[$i]);
	$adB15[$i] = sprintf("%5s", $adB15[$i]);
	$adB20[$i] = sprintf("%5s", $adB20[$i]);
	$adB25[$i] = sprintf("%5s", $adB25[$i]);
	$adB30[$i] = sprintf("%5s", $adB30[$i]);
	$adB35[$i] = sprintf("%5s", $adB35[$i]);
	$adB40[$i] = sprintf("%5s", $adB40[$i]);
	$adB45[$i] = sprintf("%5s", $adB45[$i]);
	$adB50[$i] = sprintf("%5s", $adB50[$i]);
	$adB55[$i] = sprintf("%5s", $adB55[$i]);
	$adB60[$i] = sprintf("%5s", $adB60[$i]);
	$adB90[$i] = sprintf("%5s", $adB90[$i]);
	$adB99[$i] = sprintf("%5s", $adB99[$i]);
	$Fanswer[$i] = sprintf("%10s", $Fanswer[$i]);

	echo "|$time| $adB_0[$i] $adB_5[$i] $adB10[$i] $adB15[$i] $adB20[$i] $adB25[$i] $adB30[$i] $adB35[$i] $adB40[$i] $adB45[$i] $adB50[$i] $adB55[$i] $adB60[$i] $adB90[$i] $adB99[$i] | $Fanswer[$i] |\n";

	$i++;
	$h++;
	}

$BDansweredCALLS =		sprintf("%10s", $BDansweredCALLS);

echo "+------+-------------------------------------------------------------------------------------------+------------+\n";
echo "|TOTALS|                                                                                           | $BDansweredCALLS |\n";
echo "+------+-------------------------------------------------------------------------------------------+------------+\n";



$ENDtime = date("U");
$RUNtime = ($ENDtime - $STARTtime);
echo "\nRun Time: $RUNtime seconds\n";
}




?>
</PRE>
</TD></TR></TABLE>

</BODY></HTML>
