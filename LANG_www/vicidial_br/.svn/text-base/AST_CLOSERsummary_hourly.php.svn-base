<?php 
# AST_CLOSERsummary_hourly.php
# 
# Copyright (C) 2010  Matt Florell <vicidial@gmail.com>    LICENSE: AGPLv2
#
# CHANGES
#
# 90801-0910 - First build
# 90809-0216 - Added Exclude Outbound Drop Group option
# 100214-1421 - Sort menu alphabetically
# 100216-0042 - Added popup date selector
#

require("dbconnect.php");
require("functions.php");

$PHP_AUTH_USER=$_SERVER['PHP_AUTH_USER'];
$PHP_AUTH_PW=$_SERVER['PHP_AUTH_PW'];
$PHP_SELF=$_SERVER['PHP_SELF'];

if (isset($_GET["print_calls"]))			{$print_calls=$_GET["print_calls"];}
	elseif (isset($_POST["print_calls"]))	{$print_calls=$_POST["print_calls"];}
if (isset($_GET["exclude_rollover"]))			{$exclude_rollover=$_GET["exclude_rollover"];}
	elseif (isset($_POST["exclude_rollover"]))	{$exclude_rollover=$_POST["exclude_rollover"];}
if (isset($_GET["inbound_rate"]))			{$inbound_rate=$_GET["inbound_rate"];}
	elseif (isset($_POST["inbound_rate"]))	{$inbound_rate=$_POST["inbound_rate"];}
if (isset($_GET["outbound_rate"]))			{$outbound_rate=$_GET["outbound_rate"];}
	elseif (isset($_POST["outbound_rate"]))	{$outbound_rate=$_POST["outbound_rate"];}
if (isset($_GET["bareformat"]))				{$bareformat=$_GET["bareformat"];}
	elseif (isset($_POST["bareformat"]))	{$bareformat=$_POST["bareformat"];}
if (isset($_GET["costformat"]))				{$costformat=$_GET["costformat"];}
	elseif (isset($_POST["costformat"]))	{$costformat=$_POST["costformat"];}
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
if (isset($_GET["SUBMIT"]))				{$SUBMIT=$_GET["SUBMIT"];}
	elseif (isset($_POST["SUBMIT"]))	{$SUBMIT=$_POST["SUBMIT"];}
if (isset($_GET["DB"]))					{$DB=$_GET["DB"];}
	elseif (isset($_POST["DB"]))		{$DB=$_POST["DB"];}

$PHP_AUTH_USER = ereg_replace("[^0-9a-zA-Z]","",$PHP_AUTH_USER);
$PHP_AUTH_PW = ereg_replace("[^0-9a-zA-Z]","",$PHP_AUTH_PW);

$MT[0]='0';
if (strlen($shift)<2) {$shift='ALL';}
if (strlen($exclude_rollover)<2) {$exclude_rollover='NO';}

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
    echo "Invalid Username/Password: |$PHP_AUTH_USER|$PHP_AUTH_PW|\n";
    exit;
	}

$NOW_DATE = date("Y-m-d");
$NOW_TIME = date("Y-m-d H:i:s");
$STARTtime = date("U");
if (!isset($group)) {$group = '';}
if (!isset($query_date)) {$query_date = $NOW_DATE;}
if (!isset($end_date)) {$end_date = $NOW_DATE;}

$exclude_rolloverSQL='';
if (eregi("YES",$exclude_rollover))
	{$exclude_rolloverSQL = " where group_id NOT IN(SELECT drop_inbound_group from vicidial_campaigns)";}
$stmt="select group_id,group_name from vicidial_inbound_groups $exclude_rolloverSQL order by group_id;";
$rslt=mysql_query($stmt, $link);
if ($DB) {echo "$stmt\n";}
$groups_to_print = mysql_num_rows($rslt);
$i=0;
#$LISTgroups[$i]='---NONE---';
#$i++;
#$groups_to_print++;
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

$stmt="select call_time_id,call_time_name from vicidial_call_times order by call_time_id;";
$rslt=mysql_query($stmt, $link);
if ($DB) {echo "$stmt\n";}
$times_to_print = mysql_num_rows($rslt);
$i=0;
while ($i < $times_to_print)
	{
	$row=mysql_fetch_row($rslt);
	$call_times[$i] =		$row[0];
	$call_time_names[$i] =	$row[1];
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
echo "<TITLE>Inbound Summary Hourly Report</TITLE></HEAD><BODY BGCOLOR=WHITE marginheight=0 marginwidth=0 leftmargin=0 topmargin=0>\n";

if ($bareformat < 1)
	{
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
	echo "<INPUT TYPE=HIDDEN NAME=inbound_rate VALUE=\"$inbound_rate\">\n";
	echo "<INPUT TYPE=HIDDEN NAME=outbound_rate VALUE=\"$outbound_rate\">\n";
	echo "<INPUT TYPE=HIDDEN NAME=costformat VALUE=\"$costformat\">\n";
	echo "<INPUT TYPE=HIDDEN NAME=print_calls VALUE=\"$print_calls\">\n";
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
	// o_cal.a_tpl.weekstart = 1; // Monday week start
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
	// o_cal.a_tpl.weekstart = 1; // Monday week start
	</script>
	<?php

	echo "</TD><TD VALIGN=TOP> &nbsp; \n";
	echo "</TD><TD ROWSPAN=2 VALIGN=TOP>\n";
	echo "Inbound Groups: <BR>\n";
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
	echo "<a href=\"./admin.php?ADD=3111&group_id=$group[0]\">MODIFY</a> | ";
	echo "<a href=\"./admin.php?ADD=999999\">REPORTS</a>";
	echo "</FONT><BR><BR>\n";
	echo " &nbsp; Exclude Outbound Drop Groups: <BR>";
	echo " &nbsp; <SELECT SIZE=1 NAME=exclude_rollover>\n";
	echo "<option selected value=\"$exclude_rollover\">$exclude_rollover</option>\n";
	echo "<option value=\"YES\">YES</option>\n";
	echo "<option value=\"NO\">NO</option>\n";
	echo "</SELECT>\n";
	echo "<BR> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; ";
	echo "<INPUT TYPE=submit NAME=SUBMIT VALUE=SUBMIT>\n";

	echo "</TD></TR>\n";
	echo "<TR><TD>\n";

	echo "Call Time:<BR>\n";
	echo "<SELECT SIZE=1 NAME=shift>\n";
	$o=0;
	while ($times_to_print > $o)
		{
		if ($call_times[$o] == $shift) {echo "<option selected value=\"$call_times[$o]\">$call_times[$o] - $call_time_names[$o]</option>\n";}
		else {echo "<option value=\"$call_times[$o]\">$call_times[$o] - $call_time_names[$o]</option>\n";}
		$o++;
		}
	echo "</SELECT>\n";
	echo "</TD><TD>\n";
	echo "</TD></TR></TABLE>\n";
	echo "</FORM>\n\n";

	echo "<PRE><FONT SIZE=2>\n\n";
	}

if ($groups_to_print < 1)
	{
	echo "\n\n";
	echo "PLEASE SELECT AN IN-GROUP AND DATE RANGE ABOVE AND CLICK SUBMIT\n";
	}

else
	{
	if ($shift == 'ALL') 
		{
		$Gct_default_start = "0";
		$Gct_default_stop = "2400";
		}
	else 
		{
		$stmt="SELECT call_time_id,call_time_name,call_time_comments,ct_default_start,ct_default_stop,ct_sunday_start,ct_sunday_stop,ct_monday_start,ct_monday_stop,ct_tuesday_start,ct_tuesday_stop,ct_wednesday_start,ct_wednesday_stop,ct_thursday_start,ct_thursday_stop,ct_friday_start,ct_friday_stop,ct_saturday_start,ct_saturday_stop,ct_state_call_times FROM vicidial_call_times where call_time_id='$shift';";
		$rslt=mysql_query($stmt, $link);
		if ($DB) {echo "$stmt\n";}
		$calltimes_to_print = mysql_num_rows($rslt);
		if ($calltimes_to_print > 0)
			{
			$row=mysql_fetch_row($rslt);
			$Gct_default_start =	$row[3];
			$Gct_default_stop =		$row[4];
			$Gct_sunday_start =		$row[5];
			$Gct_sunday_stop =		$row[6];
			$Gct_monday_start =		$row[7];
			$Gct_monday_stop =		$row[8];
			$Gct_tuesday_start =	$row[9];
			$Gct_tuesday_stop =		$row[10];
			$Gct_wednesday_start =	$row[11];
			$Gct_wednesday_stop =	$row[12];
			$Gct_thursday_start =	$row[13];
			$Gct_thursday_stop =	$row[14];
			$Gct_friday_start =		$row[15];
			$Gct_friday_stop =		$row[16];
			$Gct_saturday_start =	$row[17];
			$Gct_saturday_stop =	$row[18];
			}
		else
			{
			$Gct_default_start = "0";
			$Gct_default_stop = "2400";
			}
		}
	$h=0;
	while ($h < 24)
		{
		$H_test = $h . "00";
		if ( ($H_test >= $Gct_default_start) and ($H_test <= $Gct_default_stop) )
			{
			$Hcalltime[$h]++;
			}
		$h++;
		}

	$query_date_BEGIN = "$query_date 00:00:00";   
	$query_date_END = "$end_date 23:59:59";


	$MAIN .= "Inbound Summary Hourly Report: $group_string          $NOW_TIME\n";


	if ($group_ct > 0)
		{
		$MAIN .= "\n";
		$MAIN .= "---------- MULTI-GROUP BREAKDOWN:\n";
		$MAIN .= "+------------------------------------------+--------+--------+-----------+---------+-----------+---------+---------+--------+\n";
		$MAIN .= "|                                          |        |        |           |         | TOTAL     | AVERAGE | MAXIMUM | TOTAL  |\n";
		$MAIN .= "|                                          | TOTAL  | TOTAL  | TOTAL     | AVERAGE | QUEUE     | QUEUE   | QUEUE   | ABANDON|\n";
		$MAIN .= "| IN-GROUP                                 | CALLS  | ANSWER | TALK      | TALK    | TIME      | TIME    | TIME    | CALLS  |\n";
		$MAIN .= "+------------------------------------------+--------+--------+-----------+---------+-----------+---------+---------+--------+\n";

		$i=0;
		$TOTcalls_count=0;
		$TOTanswer_count=0;
		$TOTtalk_sec=0;
		$TOTtalk_avg=0;
		$TOTqueue_seconds=0;
		$TOTqueue_avg=0;
		$TOTmax_queue_seconds=0;
		$TOTdrop_count=0;
		$SUBoutput='';

		while($i < $group_ct)
			{
			$stmt="select group_name,agent_alert_delay from vicidial_inbound_groups where group_id='$group[$i]';";
			$rslt=mysql_query($stmt, $link);
			if ($DB) {echo "$stmt\n";}
			$row=mysql_fetch_row($rslt);
			$group_name[$i] =			$row[0];
			$agent_alert_delay[$i] =	round($row[1] / 1000);

			$out_of_call_time=0;
			$length_in_sec[$i]=0;
			$queue_seconds[$i]=0;
			$talk_sec[$i]=0;
			$calls_count[$i]=0;
			$drop_count[$i]=0;
			$answer_count[$i]=0;
			$max_queue_seconds[$i]=0;
			$Hlength_in_sec=$MT;
			$Hqueue_seconds=$MT;
			$Htalk_sec=$MT;
			$Hcalls_count=$MT;
			$Hdrop_count=$MT;
			$Hanswer_count=$MT;
			$Hmax_queue_seconds=$MT;
			$hTOTALcalls =	0;
			$hANSWERcalls =	0;
			$hSUMtalk =		0;
			$hAVGtalk =		0;
			$hSUMqueue =	0;
			$hAVGqueue =	0;
			$hMAXqueue =	0;
			$hDROPcalls =	0;
			$hPRINT =		0;
			$hTOTcalls_count =			0;
			$hTOTanswer_count =			0;
			$hTOTtalk_sec =				0;
			$hTOTtalk_avg =				0;
			$hTOTqueue_seconds =		0;
			$hTOTqueue_avg =			0;
			$hTOTmax_queue_seconds =	0;
			$hTOTdrop_count =			0;

			$stmt = "SELECT status,length_in_sec,queue_seconds,call_date,UNIX_TIMESTAMP(call_date),phone_number,campaign_id from vicidial_closer_log where call_date >= '$query_date_BEGIN' and call_date <= '$query_date_END' and campaign_id='$group[$i]';";
			$rslt=mysql_query($stmt, $link);
			if ($DB) {echo "$stmt\n";}
			$calls_to_parse = mysql_num_rows($rslt);
			$p=0;
			while ($p < $calls_to_parse)
				{
				$row=mysql_fetch_row($rslt);
				$call_date = explode(" ", $row[3]);
				$call_time = ereg_replace("[^0-9]","",$call_date[1]);
				$epoch = $row[4];
				$Cwday = date("w", $epoch);

				$CTstart = $Gct_default_start . "00";
				$CTstop = $Gct_default_stop . "59";

				if ( ($Cwday == 0) and ( ($Gct_sunday_start > 0) and ($Gct_sunday_stop > 0) ) )
					{$CTstart = $Gct_sunday_start . "00";   $CTstop = $Gct_sunday_stop . "59";}
				if ( ($Cwday == 1) and ( ($Gct_monday_start > 0) and ($Gct_monday_stop > 0) ) )
					{$CTstart = $Gct_monday_start . "00";   $CTstop = $Gct_monday_stop . "59";}
				if ( ($Cwday == 2) and ( ($Gct_tuesday_start > 0) and ($Gct_tuesday_stop > 0) ) )
					{$CTstart = $Gct_tuesday_start . "00";   $CTstop = $Gct_tuesday_stop . "59";}
				if ( ($Cwday == 3) and ( ($Gct_wednesday_start > 0) and ($Gct_wednesday_stop > 0) ) )
					{$CTstart = $Gct_wednesday_start . "00";   $CTstop = $Gct_wednesday_stop . "59";}
				if ( ($Cwday == 4) and ( ($Gct_thursday_start > 0) and ($Gct_thursday_stop > 0) ) )
					{$CTstart = $Gct_thursday_start . "00";   $CTstop = $Gct_thursday_stop . "59";}
				if ( ($Cwday == 5) and ( ($Gct_friday_start > 0) and ($Gct_friday_stop > 0) ) )
					{$CTstart = $Gct_friday_start . "00";   $CTstop = $Gct_friday_stop . "59";}
				if ( ($Cwday == 6) and ( ($Gct_saturday_start > 0) and ($Gct_saturday_stop > 0) ) )
					{$CTstart = $Gct_saturday_start . "00";   $CTstop = $Gct_saturday_stop . "59";}

				$Chour = date("G", $epoch);
				if ( ($call_time > $CTstart) and ($call_time < $CTstop) )
					{
					$calls_count[$i]++;
					$length_in_sec[$i] =	($length_in_sec[$i] + $row[1]);
					$queue_seconds[$i] =	($queue_seconds[$i] + $row[2]);
					$TEMPtalk = ( ($row[1] - $row[2]) - $agent_alert_delay[$i]);
					if ($TEMPtalk < 0) {$TEMPtalk = 0;}
					$talk_sec[$i] =	($talk_sec[$i] + $TEMPtalk);
					if ($max_queue_seconds[$i] < $row[2])
						{$max_queue_seconds[$i] = $row[2];}
					if (eregi("DROP",$row[0]))
						{$drop_count[$i]++;}
					else
						{$answer_count[$i]++;}

					$Hcalls_count[$Chour]++;
					$Hlength_in_sec[$Chour] =	($Hlength_in_sec[$Chour] + $row[1]);
					$Hqueue_seconds[$Chour] =	($Hqueue_seconds[$Chour] + $row[2]);
					$Htalk_sec[$Chour] =	($Htalk_sec[$Chour] + $TEMPtalk);
					if ($Hmax_queue_seconds[$Chour] < $row[2])
						{$Hmax_queue_seconds[$Chour] = $row[2];}
					if (eregi("DROP",$row[0]))
						{$Hdrop_count[$Chour]++;}
					else
						{$Hanswer_count[$Chour]++;}
					$Hcalltime[$Chour]++;

					if ($print_calls > 0)
						{
						echo "$row[5]\t$row[6]\t$TEMPtalk\n";
						$PCtemptalk = ($PCtemptalk + $TEMPtalk);
						}
					$q++;
					}
				else
					{$out_of_call_time++;}
				if ($DB)
					{echo "$call_time > $CTstart | $call_time < $CTstop | $Cwday | $Chour | $Hcalltime[$Chour] | $talk_sec[$i]\n";}
				$p++;
				}
			if ( ($answer_count[$i] > 0) and ($talk_sec[$i] > 0) )
				{$talk_avg[$i] = ($talk_sec[$i] / $answer_count[$i]);}
			else
				{$talk_avg[$i] = 0;}
			if ( ($calls_count[$i] > 0) and ($queue_seconds[$i] > 0) )
				{$queue_avg[$i] = ($queue_seconds[$i] / $calls_count[$i]);}
			else
				{$queue_avg[$i] = 0;}

			if ($print_calls > 0)
				{
				$PCtemptalkmin = ($PCtemptalk  / 60);
				echo "$q\t$PCtemptalk\t$PCtemptalkmin\n";
				}

			$TOTcalls_count =			($TOTcalls_count + $calls_count[$i]);
			$TOTanswer_count =			($TOTanswer_count + $answer_count[$i]);
			$TOTtalk_sec =				($TOTtalk_sec + $talk_sec[$i]);
			$TOTqueue_seconds =			($TOTqueue_seconds + $queue_seconds[$i]);
			$TOTdrop_count =			($TOTdrop_count + $drop_count[$i]);
			if ($max_queue_seconds[$i] > $TOTmax_queue_seconds)
				{$TOTmax_queue_seconds = $max_queue_seconds[$i];}

			$talk_sec[$i] =				sec_convert($talk_sec[$i],'H'); 
			$talk_avg[$i] =				sec_convert($talk_avg[$i],'H'); 
			$queue_seconds[$i] =		sec_convert($queue_seconds[$i],'H'); 
			$queue_avg[$i] =			sec_convert($queue_avg[$i],'H'); 
			$max_queue_seconds[$i] =	sec_convert($max_queue_seconds[$i],'H'); 

			$groupDISPLAY =	sprintf("%-40s", "$group[$i] - $group_name[$i]");
			$gTOTALcalls =	sprintf("%6s", $calls_count[$i]);
			$gANSWERcalls =	sprintf("%6s", $answer_count[$i]);
			$gSUMtalk =		sprintf("%9s", $talk_sec[$i]);
			$gAVGtalk =		sprintf("%7s", $talk_avg[$i]);
			$gSUMqueue =	sprintf("%9s", $queue_seconds[$i]);
			$gAVGqueue =	sprintf("%7s", $queue_avg[$i]);
			$gMAXqueue =	sprintf("%7s", $max_queue_seconds[$i]);
			$gDROPcalls =	sprintf("%6s", $drop_count[$i]);

			while(strlen($groupDISPLAY)>40) {$groupDISPLAY = substr("$groupDISPLAY", 0, -1);}

			$MAIN .= "| $groupDISPLAY | $gTOTALcalls | $gANSWERcalls | $gSUMtalk | $gAVGtalk | $gSUMqueue | $gAVGqueue | $gMAXqueue | $gDROPcalls |";
			$MAIN .= "<!-- OUT OF CALLTIME: $out_of_call_time -->\n";

			### hour by hour sumaries
			$SUBoutput .= "\n---------- $group[$i] - $group_name[$i]     HOURLY BREAKDOWN:\n";
			$SUBoutput .= "+------+--------+--------+-----------+---------+-----------+---------+---------+--------+\n";
			$SUBoutput .= "|      |        |        |           |         | TOTAL     | AVERAGE | MAXIMUM | TOTAL  |\n";
			$SUBoutput .= "|      | TOTAL  | TOTAL  | TOTAL     | AVERAGE | QUEUE     | QUEUE   | QUEUE   | ABANDON|\n";
			$SUBoutput .= "| HOUR | CALLS  | ANSWER | TALK      | TALK    | TIME      | TIME    | TIME    | CALLS  |\n";
			$SUBoutput .= "+------+--------+--------+-----------+---------+-----------+---------+---------+--------+\n";

			$h=0;
			while ($h < 24)
				{
				if ($Hcalltime[$h] > 0)
					{
					if (strlen($Hcalls_count[$h]) < 1)			{$Hcalls_count[$h] = 0;}
					if (strlen($Hanswer_count[$h]) < 1)			{$Hanswer_count[$h] = 0;}
					if (strlen($Htalk_sec[$h]) < 1)				{$Htalk_sec[$h] = 0;}
					if (strlen($Hqueue_seconds[$h]) < 1)		{$Hqueue_seconds[$h] = 0;}
					if (strlen($Hmax_queue_seconds[$h]) < 1)	{$Hmax_queue_seconds[$h] = 0;}
					if (strlen($Hdrop_count[$h]) < 1)			{$Hdrop_count[$h] = 0;}

					$hTOTcalls_count =			($hTOTcalls_count + $Hcalls_count[$h]);
					$hTOTanswer_count =			($hTOTanswer_count + $Hanswer_count[$h]);
					$hTOTtalk_sec =				($hTOTtalk_sec + $Htalk_sec[$h]);
					$hTOTqueue_seconds =		($hTOTqueue_seconds + $Hqueue_seconds[$h]);
					$hTOTdrop_count =			($hTOTdrop_count + $Hdrop_count[$h]);
					if ($Hmax_queue_seconds[$h] > $hTOTmax_queue_seconds)
						{$hTOTmax_queue_seconds = $Hmax_queue_seconds[$h];}

					if ( ($Hanswer_count[$h] > 0) and ($Htalk_sec[$h] > 0) )
						{$Htalk_avg[$h] = ($Htalk_sec[$h] / $Hanswer_count[$h]);}
					else
						{$Htalk_avg[$h] = 0;}
					if ( ($Hcalls_count[$h] > 0) and ($Hqueue_seconds[$h] > 0) )
						{$Hqueue_avg[$h] = ($Hqueue_seconds[$h] / $Hcalls_count[$h]);}
					else
						{$Hqueue_avg[$h] = 0;}

					$Htalk_sec[$h] =			sec_convert($Htalk_sec[$h],'H'); 
					$Htalk_avg[$h] =			sec_convert($Htalk_avg[$h],'H'); 
					$Hqueue_seconds[$h] =		sec_convert($Hqueue_seconds[$h],'H'); 
					$Hqueue_avg[$h] =			sec_convert($Hqueue_avg[$h],'H'); 
					$Hmax_queue_seconds[$h] =	sec_convert($Hmax_queue_seconds[$h],'H');
					
					$hTOTALcalls =	sprintf("%6s", $Hcalls_count[$h]);
					$hANSWERcalls =	sprintf("%6s", $Hanswer_count[$h]);
					$hSUMtalk =		sprintf("%9s", $Htalk_sec[$h]);
					$hAVGtalk =		sprintf("%7s", $Htalk_avg[$h]);
					$hSUMqueue =	sprintf("%9s", $Hqueue_seconds[$h]);
					$hAVGqueue =	sprintf("%7s", $Hqueue_avg[$h]);
					$hMAXqueue =	sprintf("%7s", $Hmax_queue_seconds[$h]);
					$hDROPcalls =	sprintf("%6s", $Hdrop_count[$h]);
					$hPRINT =		sprintf("%2s", $h);

					$SUBoutput .= "| $hPRINT   | $hTOTALcalls | $hANSWERcalls | $hSUMtalk | $hAVGtalk | $hSUMqueue | $hAVGqueue | $hMAXqueue | $hDROPcalls |\n";
					}

				$h++;
				}

			if ( ($hTOTanswer_count > 0) and ($hTOTtalk_sec > 0) )
				{$hTOTtalk_avg = ($hTOTtalk_sec / $hTOTanswer_count);}
			else
				{$hTOTtalk_avg = 0;}
			if ( ($hTOTcalls_count > 0) and ($hTOTqueue_seconds > 0) )
				{$hTOTqueue_avg = ($hTOTqueue_seconds / $hTOTcalls_count);}
			else
				{$hTOTqueue_avg = 0;}

			$hTOTtalk_sec =			sec_convert($hTOTtalk_sec,'H'); 
			$hTOTtalk_avg =			sec_convert($hTOTtalk_avg,'H'); 
			$hTOTqueue_seconds =		sec_convert($hTOTqueue_seconds,'H'); 
			$hTOTqueue_avg =			sec_convert($hTOTqueue_avg,'H'); 
			$hTOTmax_queue_seconds =	sec_convert($hTOTmax_queue_seconds,'H'); 

			$hTOTcalls_count =			sprintf("%6s", $hTOTcalls_count);
			$hTOTanswer_count =			sprintf("%6s", $hTOTanswer_count);
			$hTOTtalk_sec =				sprintf("%9s", $hTOTtalk_sec);
			$hTOTtalk_avg =				sprintf("%7s", $hTOTtalk_avg);
			$hTOTqueue_seconds =		sprintf("%9s", $hTOTqueue_seconds);
			$hTOTqueue_avg =			sprintf("%7s", $hTOTqueue_avg);
			$hTOTmax_queue_seconds =	sprintf("%7s", $hTOTmax_queue_seconds);
			$hTOTdrop_count =			sprintf("%6s", $hTOTdrop_count);

			$SUBoutput .= "+------+--------+--------+-----------+---------+-----------+---------+---------+--------+\n";
			$SUBoutput .= "|TOTALS| $hTOTcalls_count | $hTOTanswer_count | $hTOTtalk_sec | $hTOTtalk_avg | $hTOTqueue_seconds | $hTOTqueue_avg | $hTOTmax_queue_seconds | $hTOTdrop_count |\n";
			$SUBoutput .= "+------+--------+--------+-----------+---------+-----------+---------+---------+--------+\n";

			$i++;
			}

		$rawTOTtalk_sec = $TOTtalk_sec;
		$rawTOTtalk_min = round($rawTOTtalk_sec / 60);

		if ( ($TOTanswer_count > 0) and ($TOTtalk_sec > 0) )
			{$TOTtalk_avg = ($TOTtalk_sec / $TOTanswer_count);}
		else
			{$TOTtalk_avg = 0;}
		if ( ($TOTcalls_count > 0) and ($TOTqueue_seconds > 0) )
			{$TOTqueue_avg = ($TOTqueue_seconds / $TOTcalls_count);}
		else
			{$TOTqueue_avg = 0;}

		$TOTtalk_sec =			sec_convert($TOTtalk_sec,'H'); 
		$TOTtalk_avg =			sec_convert($TOTtalk_avg,'H'); 
		$TOTqueue_seconds =		sec_convert($TOTqueue_seconds,'H'); 
		$TOTqueue_avg =			sec_convert($TOTqueue_avg,'H'); 
		$TOTmax_queue_seconds =	sec_convert($TOTmax_queue_seconds,'H'); 

		$i =					sprintf("%4s", $i);
		$TOTcalls_count =		sprintf("%6s", $TOTcalls_count);
		$TOTanswer_count =		sprintf("%6s", $TOTanswer_count);
		$TOTtalk_sec =			sprintf("%9s", $TOTtalk_sec);
		$TOTtalk_avg =			sprintf("%7s", $TOTtalk_avg);
		$TOTqueue_seconds =		sprintf("%9s", $TOTqueue_seconds);
		$TOTqueue_avg =			sprintf("%7s", $TOTqueue_avg);
		$TOTmax_queue_seconds =	sprintf("%7s", $TOTmax_queue_seconds);
		$TOTdrop_count =		sprintf("%6s", $TOTdrop_count);

		$MAIN .= "+------------------------------------------+--------+--------+-----------+---------+-----------+---------+---------+--------+\n";
		$MAIN .= "| TOTALS       In-Groups: $i             | $TOTcalls_count | $TOTanswer_count | $TOTtalk_sec | $TOTtalk_avg | $TOTqueue_seconds | $TOTqueue_avg | $TOTmax_queue_seconds | $TOTdrop_count |\n";
		$MAIN .= "+------------------------------------------+--------+--------+-----------+---------+-----------+---------+---------+--------+\n";
		}

	if ($costformat > 0)
		{
		echo "</PRE>\n<B>";
		$inbound_cost = ($rawTOTtalk_min * $inbound_rate);
		$inbound_cost =		sprintf("%8.2f", $inbound_cost);

		echo "INBOUND $query_date to $end_date, &nbsp; $rawTOTtalk_min minutes at \$$inbound_rate = \$$inbound_cost\n";

		exit;
		}


	echo "$MAIN";
	echo "$SUBoutput";



	$ENDtime = date("U");
	$RUNtime = ($ENDtime - $STARTtime);
	echo "\n\nRun Time: $RUNtime seconds\n";
	}




?>
</PRE>
</TD></TR></TABLE>

</BODY></HTML>
