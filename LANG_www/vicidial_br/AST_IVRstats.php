<?php 
# AST_IVRstats.php
# 
# Copyright (C) 2010  Matt Florell <vicidial@gmail.com>    LICENSE: AGPLv2
#
# CHANGES
# 81026-2026 - First build
# 81107-0341 - Added time range and option and 15-minute increment graph
# 81107-1148 - Added average times and totals
# 81108-0922 - Added no-callerID and unique caller counts
# 90310-2056 - Admin header
# 90508-0644 - Changed to PHP long tags
# 91112-0719 - Added in-group names to select list
# 100214-1421 - Sort menu alphabetically
# 100301-1401 - Added popup date selector
#

require("dbconnect.php");

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
if (isset($_GET["ENVIAR"]))				{$ENVIAR=$_GET["ENVIAR"];}
	elseif (isset($_POST["ENVIAR"]))	{$ENVIAR=$_POST["ENVIAR"];}
if (isset($_GET["DB"]))					{$DB=$_GET["DB"];}
	elseif (isset($_POST["DB"]))		{$DB=$_POST["DB"];}

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
    echo "Nome ou Senha inválidos: |$PHP_AUTH_USER|$PHP_AUTH_PW|\n";
    exit;
	}

$NOW_DATE = date("Y-m-d");
$NOW_TIME = date("Y-m-d H:i:s");
$STARTtime = date("U");
if (!isset($group)) {$group = '';}
if (!isset($query_date)) {$query_date = "$NOW_DATE 00:00:00";}
if (!isset($end_date)) {$end_date = "$NOW_DATE 23:23:59";}

$stmt="select group_id,group_name from vicidial_inbound_groups order by group_id;";
$rslt=mysql_query($stmt, $link);
if ($DB) {echo "$stmt\n";}
$groups_to_print = mysql_num_rows($rslt);
$i=0;
	$LISTgroups[$i]='CALLMENU';
	$LISTgroups_names[$i]='IVR';
	$i++;
	$groups_to_print++;
	$LISTgroups[$i]='XMLPULL';
	$LISTgroups_names[$i]='Dynamic Application';
	$i++;
	$groups_to_print++;
while ($i < $groups_to_print)
	{
	$row=mysql_fetch_row($rslt);
	$LISTgroups[$i] =		$row[0];
	$LISTgroups_names[$i] = $row[1];
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
if ($shift == 'RANGE') 
	{
	$query_date_BEGIN = "$query_date";   
	$query_date_END = "$end_date";
	}
else
	{
	$EXquery_date = explode(' ',$query_date);
	$query_date = "$EXquery_date[0]";   
	$EXend_date = explode(' ',$end_date);
	$end_date = "$EXend_date[0]";   

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
	}

$query_dateARRAY = explode(" ",$query_date_BEGIN);
$query_date_D = $query_dateARRAY[0];
$query_date_T = $query_dateARRAY[1];
$end_dateARRAY = explode(" ",$query_date_END);
$end_date_D = $end_dateARRAY[0];
$end_date_T = $end_dateARRAY[1];


?>

<script language="JavaScript" src="calendar_db.js"></script>
<link rel="stylesheet" href="calendar.css">

<?php

echo "<META HTTP-EQUIV=\"Content-Type\" CONTENT=\"text/html; charset=utf-8\">\n";
echo "<TITLE>IVR Stats Report</TITLE></HEAD><BODY BGCOLOR=WHITE marginheight=0 marginwidth=0 leftmargin=0 topmargin=0>\n";

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
echo "<TABLE Border=0><TR><TD VALIGN=TOP>\n";
echo "<INPUT TYPE=HIDDEN NAME=DB VALUE=\"$DB\">\n";
echo "Período:<BR>\n";

echo "<INPUT TYPE=hidden NAME=query_date ID=query_date VALUE=\"$query_date\">\n";
echo "<INPUT TYPE=hidden NAME=end_date ID=end_date VALUE=\"$end_date\">\n";
echo "<INPUT TYPE=TEXT NAME=query_date_D SIZE=11 MAXLENGTH=10 VALUE=\"$query_date_D\">";

?>
<script language="JavaScript">
var o_cal = new tcal ({
	// form name
	'formname': 'vicidial_report',
	// input name
	'controlname': 'query_date_D'
});
o_cal.a_tpl.yearscroll = false;
// o_cal.a_tpl.weekstart = 1; // Segunda week start
</script>
<?php

echo " &nbsp; <INPUT TYPE=TEXT NAME=query_date_T SIZE=9 MAXLENGTH=8 VALUE=\"$query_date_T\">";

echo "<BR> to <BR><INPUT TYPE=TEXT NAME=end_date_D SIZE=11 MAXLENGTH=10 VALUE=\"$end_date_D\">";

?>
<script language="JavaScript">
var o_cal = new tcal ({
	// form name
	'formname': 'vicidial_report',
	// input name
	'controlname': 'end_date_D'
});
o_cal.a_tpl.yearscroll = false;
// o_cal.a_tpl.weekstart = 1; // Segunda week start
</script>
<?php

echo " &nbsp; <INPUT TYPE=TEXT NAME=end_date_T SIZE=9 MAXLENGTH=8 VALUE=\"$end_date_T\">";


echo "</TD><TD ROWSPAN=2 VALIGN=TOP>\n";
echo "Grupos de Entrada: \n";
echo "</TD><TD ROWSPAN=2 VALIGN=TOP>\n";
echo "<SELECT SIZE=5 NAME=group[] multiple>\n";
$o=0;
	while ($groups_to_print > $o)
	{
	if (ereg("\|$LISTgroups[$o]\|",$group_string)) 
		{echo "<option selected value=\"$LISTgroups[$o]\">$LISTgroups[$o] - $LISTgroups_names[$o]</option>\n";}
	else
		{echo "<option value=\"$LISTgroups[$o]\">$LISTgroups[$o] - $LISTgroups_names[$o]</option>\n";}
	$o++;
	}
echo "</SELECT>\n";
echo "</TD><TD ROWSPAN=2 VALIGN=TOP>\n";
echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; ";
echo "<a href=\"./admin.php?ADD=3111&group_id=$group[0]\">ALTERAR</a> | ";
echo "<a href=\"./admin.php?ADD=999999\">RELATÓRIOS</a> | ";
echo "<a href=\"./AST_CLOSERstats.php?query_date=$query_date&end_date=$end_date&shift=$shift$groupQS\">RELATÓRIO DE FINALIZADOR(CLOSER)</a> \n";
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
echo "<option value=\"RANGE\">RANGE</option>\n";
echo "</SELECT>\n";

?>
<SCRIPT LANGUAGE="JavaScript">

function submit_form()
	{
	document.vicidial_report.end_date.value = document.vicidial_report.end_date_D.value + " " + document.vicidial_report.end_date_T.value;
	document.vicidial_report.query_date.value = document.vicidial_report.query_date_D.value + " " + document.vicidial_report.query_date_T.value;

	document.vicidial_report.submit();
	}

</SCRIPT>

 &nbsp; <input type=button value="ENVIAR" name=smt id=smt onClick="submit_form()">
<?php

echo "</TD></TR></TABLE>\n";
echo "</FORM>\n\n";

echo "<PRE><FONT SIZE=2>\n\n";


if ($groups_to_print < 1)
	{
	echo "\n\n";
	echo "POR FAVOR SELECIONE UM GRUPO DE ENTRADA E PERÍODO ACIMA E CLIQUE ENVIAR\n";
	}

else
	{
	echo "IVR Stats Report: $query_date_BEGIN to $query_date_END               $NOW_TIME\n";
	echo "                  $group_string\n";

	$TOTALcalls=0;
	$NOCALLERIDcalls=0;
	$UNIQUEcallers=0;
	$totFLOWivr_time=0;
	$totFLOWtotal_time=0;

	##### Grab all records for the IVR for the specified time period
	$stmt="select uniqueid,extension,start_time,comment_a,comment_b,comment_d,UNIX_TIMESTAMP(start_time),phone_ext from live_inbound_log where start_time >= '$query_date_BEGIN' and start_time <= '$query_date_END' and comment_a IN($group_SQL) order by uniqueid,start_time;";
	$rslt=mysql_query($stmt, $link);
	if ($DB) {echo "$stmt\n";}
	$logs_to_print = mysql_num_rows($rslt);
	$p=0;
	while ($p < $logs_to_print)
		{
		$row=mysql_fetch_row($rslt);
		$uniqueid[$p] =		$row[0];
		$extension[$p] =	$row[1];
		$start_time[$p] =	$row[2];
		$comment_a[$p] =	$row[3];
		$comment_b[$p] =	$row[4];
		$comment_d[$p] =	$row[5];
		$epoch[$p] =		$row[6];
		$phone_ext[$p] =	$row[7];
		$p++;
		}

	### create the call flow of all calls by uniqueid
	$last_uniqueid='';
	$first_epoch='';
	$last_epoch='';
	$p=0;
	$r=-1;
	while ($p < $logs_to_print)
		{
		if ($DB > 0) {echo "$p|$uniqueid[$p]|$comment_b[$p]";}
		if ($last_uniqueid === "$uniqueid[$p]")
			{
			$unique_calls[$r] .= "----------$comment_b[$p]";
			if ($DB > 0) {echo "   $r|$unique_calls[$r]\n";}
			$last_epoch[$r]=$epoch[$p];
			}
		else
			{
			$r++;
			$caller_id[$r]=$phone_ext[$p];
			if (strlen($phone_ext[$p])<2)
				{$NOCALLERIDcalls++;}
			else
				{
				if (!ereg("_$phone_ext[$p]_",$unique_callerIDs))
					{
					$unique_callerIDs .= "_$phone_ext[$p]_";
					$UNIQUEcallers++;
					}
				}
			$first_epoch[$r]=$epoch[$p];
			$last_epoch[$r]=$epoch[$p];
			$unique_calls[$r] = $comment_b[$p];
			$FLOWuniqueid[$r] = "$uniqueid[$p]";
			$last_uniqueid = "$uniqueid[$p]";
			if ($DB > 0) {echo "   $r|$unique_calls[$r]\n";}
			}
		$p++;
		}

	### sort call flows for counting
	$RAWunique_calls = $unique_calls;
	if ($logs_to_print > 0)
		{sort($unique_calls);}


	### count each unique call flow
	$last_Suniqueid='';
	$p=-1;
	$s=0;
	while ($s <= $r)
		{
		if ($DB > 0) {echo "$s|$unique_calls[$s]\n";}
		if ($last_Suniqueid === "$unique_calls[$s]")
			{
			$STunique_calls_count[$p]++;
			}
		else
			{
			$p++;
			$STunique_calls[$p] = $unique_calls[$s];
			$last_Suniqueid = "$unique_calls[$s]";
			$STunique_calls_count[$p]=1;
			}
		$s++;
		}


	### put call flows and counts together for sorting again
	$TOTALcalls=0;
	$s=0;
	while ($s <= $p)
		{
		$TOTALcalls = ($TOTALcalls + $STunique_calls_count[$s]);
		$STunique_calls_count[$s] = sprintf("%07s", $STunique_calls_count[$s]);
		$FLOWunique_calls[$s] = "$STunique_calls_count[$s]__________$STunique_calls[$s]";
		$s++;
		}

	#### PRINT TOTAL DE CHAMADAS INTO THIS IVR
	echo "\n";
	echo "Calls taken into this IVR:   $TOTALcalls\n";
	echo "Calls with no CallerID:      $NOCALLERIDcalls\n";
	echo "Unique Callers:              $UNIQUEcallers\n";
	echo "\n";

	### sort call flows for counting
	if ($p > 0)
		{rsort($FLOWunique_calls);}


	### put call flows and counts together for sorting again
	$RUC_ct = count($RAWunique_calls);
	$s=0;
	while ($s <= $p)
		{
		$FLOWsummary = explode('__________',$FLOWunique_calls[$s]);
		$FLOWsummary[0] = ($FLOWsummary[0] + 0);

		$t=0;
		while ($t < $RUC_ct)
			{
			if ($FLOWsummary[1] === "$RAWunique_calls[$t]")
				{
				$FLOWunique_calls_list[$s] .= "'$FLOWuniqueid[$t]',";
				if ($last_epoch[$t] <= $first_epoch[$t]) {$last_epoch[$t] = ($first_epoch[$t] + 5);}
				else {$last_epoch[$t] = ($last_epoch[$t] + 10);}
				$FLOWivr_time[$s] = ($FLOWivr_time[$s] + ($last_epoch[$t] - $first_epoch[$t]));
				}
			$t++;
			}

		$s++;
		}


	### put call flows and counts together for sorting again
	$s=0;

	echo "+--------+--------+--------+--------+------+------+\n";
	echo "|        |        | QUEUE  | QUEUE  | IVR  | TOTAL|\n";
	echo "| IVR    | QUEUE  | DROP   | DROP   | AVG  | AVG  |\n";
	echo "| CALLS  | CALLS  | CALLS  | PERCENT| TIME | TIME | CALL PATH\n";
	echo "+--------+--------+--------+--------+------+------+------------\n";

	while ($s <= $p)
		{
		$vcl_statuses = $MT;
		$FLOWdrop[$s]=0;
		$FLOWtotal[$s]=0;
		$FLOWdropPCT[$s]=0;
		$FLOWsummary = explode('__________',$FLOWunique_calls[$s]);
		$FLOWsummary[0] = ($FLOWsummary[0] + 0);

		$FLOWunique_calls_list[$s] = preg_replace("/,$/","",$FLOWunique_calls_list[$s]);


		##### Grab all records for the IVR for the specified time period
		$stmt="select status,length_in_sec from vicidial_closer_log where call_date >= '$query_date_BEGIN' and call_date <= '$query_date_END' and campaign_id IN($group_SQL) and uniqueid IN($FLOWunique_calls_list[$s]);";
		$rslt=mysql_query($stmt, $link);
		if ($DB) {echo "$stmt\n";}
		$vcl_statuses_to_print = mysql_num_rows($rslt);
		$w=0;
		while ($w < $vcl_statuses_to_print)
			{
			$row=mysql_fetch_row($rslt);
			$vcl_statuses[$w] =		$row[0];
			if ( (ereg("DROP",$vcl_statuses[$w])) or (ereg("XDROP",$vcl_statuses[$w])) )
				{$FLOWdrop[$s]++;}
			$FLOWclose_time[$s] = ($FLOWclose_time[$s] + $row[1]);
			$FLOWtotal[$s]++;
			$w++;
			}
		if ( ($FLOWtotal[$s] > 0) and ($FLOWdrop[$s] > 0) )
			{
			$FLOWdropPCT[$s] = ( ($FLOWdrop[$s] / $FLOWtotal[$s]) * 100);
			$FLOWdropPCT[$s] = round($FLOWdropPCT[$s], 2);
			}
		$FLOWsummary[0] =	sprintf("%6s", $FLOWsummary[0]);
		$FLOWtotal[$s] =	sprintf("%6s", $FLOWtotal[$s]);
		$FLOWdrop[$s] =		sprintf("%6s", $FLOWdrop[$s]);
		$FLOWdropPCT[$s] =	sprintf("%6s", $FLOWdropPCT[$s]);
		$FLOWsummary[1] = ereg_replace('----------',' / ',$FLOWsummary[1]);
		$FLOWtotal_time[$s] = ($FLOWivr_time[$s] + $FLOWclose_time[$s]);

		$avgFLOWivr_time[$s] = ($FLOWivr_time[$s] / $FLOWsummary[0]);
		$avgFLOWivr_time[$s] = round($avgFLOWivr_time[$s], 0);
		$avgFLOWivr_time[$s] = sprintf("%4s", $avgFLOWivr_time[$s]);
		$avgFLOWtotal_time[$s] = ($FLOWtotal_time[$s] / $FLOWsummary[0]);
		$avgFLOWtotal_time[$s] = round($avgFLOWtotal_time[$s], 0);
		$avgFLOWtotal_time[$s] = sprintf("%4s", $avgFLOWtotal_time[$s]);

		$totFLOWtotal_time = ($totFLOWtotal_time + $FLOWtotal_time[$s]);
		$totFLOWivr_time = ($totFLOWivr_time + $FLOWivr_time[$s]);
		$totFLOWtotal = ($totFLOWtotal + $FLOWtotal[$s]);
		$totFLOWdrop = ($totFLOWdrop + $FLOWdrop[$s]);

		echo "| $FLOWsummary[0] | $FLOWtotal[$s] | $FLOWdrop[$s] | $FLOWdropPCT[$s]%| $avgFLOWivr_time[$s] | $avgFLOWtotal_time[$s] | $FLOWsummary[1]\n";

		$s++;
		}
	$TOTALcalls = sprintf("%6s", $TOTALcalls);
	$totFLOWtotal = sprintf("%6s", $totFLOWtotal);
	$totFLOWdrop = sprintf("%6s", $totFLOWdrop);
	if ( ($totFLOWivr_time > 0) and ($TOTALcalls > 0) )
		{$TavgFLOWivr_time = ($totFLOWivr_time / $TOTALcalls);}
	$TavgFLOWivr_time = round($TavgFLOWivr_time, 0);
	$TavgFLOWivr_time = sprintf("%4s", $TavgFLOWivr_time);
	if ( ($totFLOWtotal_time > 0) and ($TOTALcalls > 0) )
		{$TavgFLOWtotal_time = ($totFLOWtotal_time / $TOTALcalls);}
	$TavgFLOWtotal_time = round($TavgFLOWtotal_time, 0);
	$TavgFLOWtotal_time = sprintf("%4s", $TavgFLOWtotal_time);
	if ( ($totFLOWtotal < 1) or ($totFLOWdrop < 1) )
		{$totFLOWdropPCT = '0';}
	else
		{
		$totFLOWdropPCT = (($totFLOWdrop / $totFLOWtotal) * 100);
		$totFLOWdropPCT = round($totFLOWdropPCT, 0);
		}
	$totFLOWdropPCT = sprintf("%5s", $totFLOWdropPCT);

	echo "+--------+--------+--------+--------+------+------+------------\n";
	echo "| $TOTALcalls | $totFLOWtotal | $totFLOWdrop | $totFLOWdropPCT% | $TavgFLOWivr_time | $TavgFLOWtotal_time |\n";
	echo "+--------+--------+--------+--------+------+------+\n";


	##############################
	#########  TIME STATS

	echo "\n";
	echo "---------- ESTATÍSTICAS DE TEMPO\n";

	echo "<FONT SIZE=0>\n";

	$hi_hour_count=0;
	$last_full_record=0;
	$i=0;
	$h=0;
	while ($i <= 96)
		{
		$stmt="select count(*) from live_inbound_log where start_time >= '$query_date $h:00:00' and start_time <= '$query_date $h:14:59' and comment_a IN($group_SQL) and comment_b='START';";
		$rslt=mysql_query($stmt, $link);
		if ($DB) {echo "$stmt\n";}
		$row=mysql_fetch_row($rslt);
		$hour_count[$i] = $row[0];
		if ($hour_count[$i] > $hi_hour_count) {$hi_hour_count = $hour_count[$i];}
		if ($hour_count[$i] > 0) {$last_full_record = $i;}
		$i++;


		$stmt="select count(*) from live_inbound_log where start_time >= '$query_date $h:15:00' and start_time <= '$query_date $h:29:59' and comment_a IN($group_SQL) and comment_b='START';";
		$rslt=mysql_query($stmt, $link);
		if ($DB) {echo "$stmt\n";}
		$row=mysql_fetch_row($rslt);
		$hour_count[$i] = $row[0];
		if ($hour_count[$i] > $hi_hour_count) {$hi_hour_count = $hour_count[$i];}
		if ($hour_count[$i] > 0) {$last_full_record = $i;}
		$i++;

		$stmt="select count(*) from live_inbound_log where start_time >= '$query_date $h:30:00' and start_time <= '$query_date $h:44:59' and comment_a IN($group_SQL) and comment_b='START';";
		$rslt=mysql_query($stmt, $link);
		if ($DB) {echo "$stmt\n";}
		$row=mysql_fetch_row($rslt);
		$hour_count[$i] = $row[0];
		if ($hour_count[$i] > $hi_hour_count) {$hi_hour_count = $hour_count[$i];}
		if ($hour_count[$i] > 0) {$last_full_record = $i;}
		$i++;

		$stmt="select count(*) from live_inbound_log where start_time >= '$query_date $h:45:00' and start_time <= '$query_date $h:59:59' and comment_a IN($group_SQL) and comment_b='START';";
		$rslt=mysql_query($stmt, $link);
		if ($DB) {echo "$stmt\n";}
		$row=mysql_fetch_row($rslt);
		$hour_count[$i] = $row[0];
		if ($hour_count[$i] > $hi_hour_count) {$hi_hour_count = $hour_count[$i];}
		if ($hour_count[$i] > 0) {$last_full_record = $i;}
		$i++;
		$h++;
		}

	if ($hi_hour_count < 1)
		{$hour_multiplier = 0;}
	else
		{
		$hour_multiplier = (100 / $hi_hour_count);
		#$hour_multiplier = round($hour_multiplier, 0);
		}

	echo "<!-- HICOUNT: $hi_hour_count|$hour_multiplier -->\n";
	echo "GRÁFICO TOTAL DE CHAMADAS A CADA 15 MINUTOS TAKEN INTO THIS IVR\n";

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


	echo "+------+-------------------------------------------------------------------------------------------------------+-------+\n";
	#echo "| HOUR | GRAPH IN 15 MINUTE INCREMENTS OF TOTAL INCOMING CALLS FOR THIS GROUP                                  | TOTAL |\n";
	echo "| HOUR |$call_scale| TOTAL |\n";
	echo "+------+-------------------------------------------------------------------------------------------------------+-------+\n";

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

			$hour_count[$i] =	sprintf("%-5s", $hour_count[$i]);

			echo "|$time|<SPAN class=\"green\">";
			$k=0;   while ($k <= $Xhour_count) {echo "*";   $k++;   $char_counter++;}
			echo "*X</SPAN>";   $char_counter++;
			$k=0;   while ($k <= $Yhour_count) {echo " ";   $k++;   $char_counter++;}
				while ($char_counter <= 101) {echo " ";   $char_counter++;}
			echo "| $hour_count[$i] |\n";
			}
		
		
		$i++;
		$h++;
		}


	echo "+------+-------------------------------------------------------------------------------------------------------+-------+\n\n";


	$ENDtime = date("U");
	$RUNtime = ($ENDtime - $STARTtime);
	echo "\nRun Time: $RUNtime seconds\n";
	}



?>
</PRE>
</TD></TR></TABLE>

</BODY></HTML>
