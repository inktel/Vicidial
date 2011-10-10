<?php 
# AST_OUTBOUNDsummary_interval.php
# 
# Copyright (C) 2010  Matt Florell <vicidial@gmail.com>    LICENSE: AGPLv2
#
# CHANGES
#
# 091128-0311 - First build
# 091129-0017 - Added Sales-type and DNC-type tallies
# 100214-1421 - Sort menu alphabetically
# 100216-0042 - Added popup date selector
#

require("dbconnect.php");
require("functions.php");

$PHP_AUTH_USER=$_SERVER['PHP_AUTH_USER'];
$PHP_AUTH_PW=$_SERVER['PHP_AUTH_PW'];
$PHP_SELF=$_SERVER['PHP_SELF'];

if (isset($_GET["time_interval"]))			{$time_interval=$_GET["time_interval"];}
	elseif (isset($_POST["time_interval"]))	{$time_interval=$_POST["time_interval"];}
if (isset($_GET["print_calls"]))			{$print_calls=$_GET["print_calls"];}
	elseif (isset($_POST["print_calls"]))	{$print_calls=$_POST["print_calls"];}
if (isset($_GET["include_rollover"]))			{$include_rollover=$_GET["include_rollover"];}
	elseif (isset($_POST["include_rollover"]))	{$include_rollover=$_POST["include_rollover"];}
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
if (strlen($include_rollover)<2) {$include_rollover='NO';}

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

$auth=0;
$stmt="SELECT full_name,user_level,user_group from vicidial_users where user='$PHP_AUTH_USER' and pass='$PHP_AUTH_PW' and user_level >= 7 and view_reports='1' and active='Y';";
if ($DB) {echo "|$stmt|\n";}
if ($non_latin > 0) {$rslt=mysql_query("SET NAMES 'UTF8'");}
$rslt=mysql_query($stmt, $link);
$records_to_print = mysql_num_rows($rslt);
if ($records_to_print > 0)
	{
	$row=mysql_fetch_row($rslt);
	$full_name =	$row[0];
	$user_level =	$row[1];
	$user_group =	$row[2];
	$auth++;
	}

if( (strlen($PHP_AUTH_USER)<2) or (strlen($PHP_AUTH_PW)<2) or (!$auth))
	{
    Header("WWW-Authenticate: Basic realm=\"VICI-PROJECTS\"");
    Header("HTTP/1.0 401 Unauthorized");
    echo "Invalid Username/Password: |$PHP_AUTH_USER|$PHP_AUTH_PW|\n";
    exit;
	}

$LOGallowed_campaignsSQL='';
$whereLOGallowed_campaignsSQL='';
if ($user_level < 9)
	{
	$stmt="SELECT allowed_campaigns from vicidial_user_groups where user_group='$user_group';";
	$rslt=mysql_query($stmt, $link);
	$records_to_print = mysql_num_rows($rslt);
	if ($records_to_print > 0)
		{
		$row=mysql_fetch_row($rslt);
		if ( (!eregi("ALL-CAMPAIGNS",$row[0])) )
			{
			$rawLOGallowed_campaignsSQL = eregi_replace(' -','',$row[0]);
			$rawLOGallowed_campaignsSQL = eregi_replace(' ',"','",$rawLOGallowed_campaignsSQL);
			$LOGallowed_campaignsSQL = "and campaign_id IN('$rawLOGallowed_campaignsSQL')";
			$whereLOGallowed_campaignsSQL = "where campaign_id IN('$rawLOGallowed_campaignsSQL')";
			}
		}
	else
		{
		echo "Campaigns Permissions Error: |$PHP_AUTH_USER|$user_group|\n";
		exit;
		}
	}

$NOW_DATE = date("Y-m-d");
$NOW_TIME = date("Y-m-d H:i:s");
$STARTtime = date("U");
if (!isset($group)) {$group = '';}
if (!isset($query_date)) {$query_date = $NOW_DATE;}
if (!isset($end_date)) {$end_date = $NOW_DATE;}

$i=0;
$group_string='|';
$group_ct = count($group);
while($i < $group_ct)
	{
	$group_string .= "$group[$i]|";
	$i++;
	}

$stmt="select campaign_id,campaign_name from vicidial_campaigns $whereLOGallowed_campaignsSQL order by campaign_id;";
$rslt=mysql_query($stmt, $link);
if ($DB) {echo "$stmt\n";}
$campaigns_to_print = mysql_num_rows($rslt);
$i=0;
while ($i < $campaigns_to_print)
	{
	$row=mysql_fetch_row($rslt);
	$groups[$i] =		$row[0];
	$group_names[$i] =	$row[1];
	if (eregi('--ALL--',$group_string))
		{$group[$i] =	$row[0];}
	$i++;
	}

if ($DB) {echo "$group_string|$i\n";}

$rollover_groups_count=0;
$i=0;
$group_string='|';
$group_ct = count($group);
while($i < $group_ct)
	{
	$group_string .= "$group[$i]|";
	$group_SQL .= "'$group[$i]',";
	$groupQS .= "&group[]=$group[$i]";

	$stmt="select campaign_name from vicidial_campaigns where campaign_id='$group[$i]' $LOGallowed_campaignsSQL;";
	$rslt=mysql_query($stmt, $link);
	$campaign_names_to_print = mysql_num_rows($rslt);
	if ($campaign_names_to_print > 0)
		{
		$row=mysql_fetch_row($rslt);
		$group_cname[$i] =	$row[0];
		}

	if (eregi("YES",$include_rollover))
		{
		$stmt="select drop_inbound_group from vicidial_campaigns where campaign_id='$group[$i]' $LOGallowed_campaignsSQL and drop_inbound_group NOT LIKE \"%NONE%\" and drop_inbound_group is NOT NULL and drop_inbound_group != '';";
		$rslt=mysql_query($stmt, $link);
		if ($DB) {echo "$stmt\n";}
		$in_groups_to_print = mysql_num_rows($rslt);
		if ($in_groups_to_print > 0)
			{
			$row=mysql_fetch_row($rslt);
			$group_drop_SQL .= "'$row[0]',";

			$rollover_groups_count++;
			}
		}

	$i++;
	}
if ( (ereg("--ALL--",$group_string) ) or ($group_ct < 1) )
	{
	$group_SQL = "";
	$group_drop_SQL = "";
	}
else
	{
	$group_SQL = eregi_replace(",$",'',$group_SQL);
	$group_drop_SQL = eregi_replace(",$",'',$group_drop_SQL);
	$both_group_SQLand = "and ( (campaign_id IN($group_drop_SQL)) or (campaign_id IN($group_SQL)) )";
	$both_group_SQL = "where ( (campaign_id IN($group_drop_SQL)) or (campaign_id IN($group_SQL)) )";
	$group_SQLand = "and campaign_id IN($group_SQL)";
	$group_SQL = "where campaign_id IN($group_SQL)";
	$group_drop_SQLand = "and campaign_id IN($group_drop_SQL)";
	$group_drop_SQL = "where campaign_id IN($group_drop_SQL)";
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

$customer_interactive_statuses='';
$stmt="select status from vicidial_statuses where human_answered='Y';";
$rslt=mysql_query($stmt, $link);
if ($DB) {echo "$stmt\n";}
$statha_to_print = mysql_num_rows($rslt);
$i=0;
while ($i < $statha_to_print)
	{
	$row=mysql_fetch_row($rslt);
	$customer_interactive_statuses .= "'$row[0]',";
	$i++;
	}
$stmt="select status from vicidial_campaign_statuses where human_answered='Y';";
$rslt=mysql_query($stmt, $link);
if ($DB) {echo "$stmt\n";}
$statha_to_print = mysql_num_rows($rslt);
$i=0;
while ($i < $statha_to_print)
	{
	$row=mysql_fetch_row($rslt);
	$customer_interactive_statuses .= "'$row[0]',";
	$i++;
	}
if (strlen($customer_interactive_statuses)>2)
	{$customer_interactive_statuses = substr("$customer_interactive_statuses", 0, -1);}
else
	{$customer_interactive_statuses="''";}

$stmt="select status from vicidial_statuses where sale='Y';";
$rslt=mysql_query($stmt, $link);
if ($DB) {echo "$stmt\n";}
$statsale_to_print = mysql_num_rows($rslt);
$i=0;
$sale_ct=0;
while ($i < $statsale_to_print)
	{
	$row=mysql_fetch_row($rslt);
	$sale_statusesLIST[$sale_ct] = $row[0];
	$sale_ct++;
	$i++;
	}
$stmt="select status from vicidial_campaign_statuses where sale='Y';";
$rslt=mysql_query($stmt, $link);
if ($DB) {echo "$stmt\n";}
$statsale_to_print = mysql_num_rows($rslt);
$i=0;
while ($i < $statsale_to_print)
	{
	$row=mysql_fetch_row($rslt);
	$sale_statusesLIST[$sale_ct] = $row[0];
	$sale_ct++;
	$i++;
	}

$stmt="select status from vicidial_statuses where dnc='Y';";
$rslt=mysql_query($stmt, $link);
if ($DB) {echo "$stmt\n";}
$statdnc_to_print = mysql_num_rows($rslt);
$i=0;
$dnc_ct=0;
while ($i < $statdnc_to_print)
	{
	$row=mysql_fetch_row($rslt);
	$dnc_statusesLIST[$dnc_ct] = $row[0];
	$dnc_ct++;
	$i++;
	}
$stmt="select status from vicidial_campaign_statuses where dnc='Y';";
$rslt=mysql_query($stmt, $link);
if ($DB) {echo "$stmt\n";}
$statdnc_to_print = mysql_num_rows($rslt);
$i=0;
while ($i < $statdnc_to_print)
	{
	$row=mysql_fetch_row($rslt);
	$dnc_statusesLIST[$dnc_ct] = $row[0];
	$dnc_ct++;
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
echo "<TITLE>Outbound Summary Interval Report</TITLE></HEAD><BODY BGCOLOR=WHITE marginheight=0 marginwidth=0 leftmargin=0 topmargin=0>\n";

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

	echo "</TD><TD VALIGN=TOP ROWSPAN=2> Campaigns:<BR>";
	echo "<SELECT SIZE=5 NAME=group[] multiple>\n";
	if  (eregi("--ALL--",$group_string))
		{echo "<option value=\"--ALL--\" selected>-- ALL CAMPAIGNS --</option>\n";}
	else
		{echo "<option value=\"--ALL--\">-- ALL CAMPAIGNS --</option>\n";}
	$o=0;
	while ($campaigns_to_print > $o)
		{
		if (eregi("$groups[$o]\|",$group_string)) {echo "<option selected value=\"$groups[$o]\">$groups[$o] - $group_names[$o]</option>\n";}
		  else {echo "<option value=\"$groups[$o]\">$groups[$o] - $group_names[$o]</option>\n";}
		$o++;
		}
	echo "</SELECT>\n";
	echo "</TD><TD VALIGN=TOP ROWSPAN=2>";
	echo "Include Drop &nbsp; <BR>Rollover:<BR>";
	echo "<SELECT SIZE=1 NAME=include_rollover>\n";
	echo "<option selected value=\"$include_rollover\">$include_rollover</option>\n";
	echo "<option value=\"YES\">YES</option>\n";
	echo "<option value=\"NO\">NO</option>\n";
	echo "</SELECT><BR>\n";
	echo "Time Interval:<BR>";
	echo "<SELECT SIZE=1 NAME=time_interval>\n";
	if ($time_interval <= 900)
		{
		$interval_count = 96;
		$hf=45;
		echo "<option selected value=\"900\">15 Minutes</option>\n";
		}
	else
		{echo "<option value=\"900\">15 Minutes</option>\n";}
	if ( ($time_interval > 900) and ($time_interval <= 1800) )
		{
		$interval_count = 48;
		$hf=30;
		echo "<option selected value=\"1800\">30 Minutes</option>\n";
		}
	else
		{echo "<option value=\"1800\">30 Minutes</option>\n";}
	if ($time_interval > 1800)
		{
		$interval_count = 24;
		echo "<option selected value=\"3600\">1 Hour</option>\n";
		}
	else
		{echo "<option value=\"3600\">1 Hour</option>\n";}
	echo "</SELECT>\n";
	echo "</TD><TD VALIGN=TOP ALIGN=LEFT ROWSPAN=2>\n";
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2> &nbsp; &nbsp; &nbsp; &nbsp; ";
	echo "<a href=\"./admin.php?ADD=3111&group_id=$group[0]\">MODIFY</a> | ";
	echo "<a href=\"./admin.php?ADD=999999\">REPORTS</a>";
	echo "</FONT><BR><BR>\n";
	echo "<BR> &nbsp; &nbsp; &nbsp; &nbsp; ";
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

if ($group_ct < 1)
	{
	echo "\n\n";
	echo "PLEASE SELECT A CAMPAIGN AND DATE RANGE ABOVE AND CLICK SUBMIT\n";
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
	$hh=0;
	while ($h < $interval_count)
		{
		if ($interval_count>=96)
			{
			if ($hf < 45)
				{
				$hf = ($hf + 15);
				}
			else
				{
				$hf = "00";
				if ($h > 0)
					{$hh++;}
				}
			$H_test = "$hh$hf";
			}
		if ($interval_count==48)
			{
			if ($hf < 30)
				{
				$hf = ($hf + 30);
				}
			else
				{
				$hf = "00";
				if ($h > 0)
					{$hh++;}
				}
			$H_test = "$hh$hf";
			}
		if ($interval_count<=24)
			{
			$H_test = $h . "00";
			}
		if ( ($H_test >= $Gct_default_start) and ($H_test <= $Gct_default_stop) )
			{
			$Hcalltime[$h]++;
			$Hcalltime_HHMM[$h] = "$H_test";
			}
		if ($DB)
			{echo "( ($H_test >= $Gct_default_start) and ($H_test <= $Gct_default_stop) ) $hh $hf\n";}
		$h++;
		}

	$query_date_BEGIN = "$query_date 00:00:00";   
	$query_date_END = "$end_date 23:59:59";


	$MAIN .= "Outbound Summary Interval Report: $group_string          $NOW_TIME\n";




	##### Loop through each campaign and gether stats
	if ($group_ct > 0)
		{
		$MAIN .= "\n";
		$MAIN .= "---------- MULTI-CAMPAIGN BREAKDOWN:\n";
		$MAIN .= "+------------------------------------------+--------+--------+--------+--------+--------+--------+--------+------------+------------+\n";
		$MAIN .= "|                                          |        | SYSTEM | AGENT  |        |        | NO     |        | AGENT      | AGENT      |\n";
		$MAIN .= "|                                          | TOTAL  | RELEASE| RELEASE| SALE   | DNC    | ANSWER | DROP   | LOGIN      | PAUSE      |\n";
		$MAIN .= "| CAMPAIGN                                 | CALLS  | CALLS  | CALLS  | CALLS  | CALLS  | PERCENT| PERCENT| TIME(H:M:S)| TIME(H:M:S)|\n";
		$MAIN .= "+------------------------------------------+--------+--------+--------+--------+--------+--------+--------+------------+------------+\n";

		$i=0;
		$TOTcalls_count=0;
		$TOTsystem_count=0;
		$TOTagent_count=0;
		$TOTptp_count=0;
		$TOTrtp_count=0;
		$TOTna_count=0;
		$TOTdrop_count=0;
		$TOTagent_login_sec=0;
		$TOTagent_pause_sec=0;
		$SUBoutput='';

		while($i < $group_ct)
			{
			$u=0;

			##### Gather Agent time records
			$stmt="select event_time,UNIX_TIMESTAMP(event_time),campaign_id,pause_sec,wait_sec,talk_sec,dispo_sec from vicidial_agent_log where event_time >= '$query_date_BEGIN' and event_time <= '$query_date_END' and campaign_id IN('$group_drop[$i]','$group[$i]');";
			$rslt=mysql_query($stmt, $link);
			if ($DB) {echo "$stmt\n";}
			$AGENTtime_to_print = mysql_num_rows($rslt);
			$s=0;
			while ($s < $AGENTtime_to_print)
				{
				$row=mysql_fetch_row($rslt);
				$inTOTALsec =		($row[3] + $row[4] + $row[5] + $row[6]);	
				$ATcall_date[$s] =		$row[0];
				$ATepoch[$s] =			$row[1];
				$ATcampaign_id[$s] =	$row[2];
				$ATpause_sec[$s] =		$row[3];
				$ATagent_sec[$s] =		$inTOTALsec;
				$s++;
				}

			##### Gather outbound calls
			$stmt = "SELECT status,length_in_sec,call_date,UNIX_TIMESTAMP(call_date),phone_number,campaign_id,uniqueid,lead_id from vicidial_log where call_date >= '$query_date_BEGIN' and call_date <= '$query_date_END' and campaign_id='$group[$i]';";
			$rslt=mysql_query($stmt, $link);
			if ($DB) {echo "$stmt\n";}
			$calls_to_parse = mysql_num_rows($rslt);
			$p=0;
			while ($p < $calls_to_parse)
				{
				$row=mysql_fetch_row($rslt);
				$CPstatus[$u] =			$row[0];
				$CPlength_in_sec[$u] =	$row[1];
				$CPcall_date[$u] =		$row[2];
				$CPepoch[$u] =			$row[3];
				$CPphone_number[$u] =	$row[4];
				$CPcampaign_id[$u] =	$row[5];
				$CPvicidial_id[$u] =	$row[6];
				$CPlead_id[$u] =		$row[7];
				$TESTlead_id[$u] =		$row[7];
				$TESTuniqueid[$u] =		$row[6];
				$CPin_out[$u] =			'OUT';
				$p++;
				$u++;
				}

			$group_drop[$i]='';
			if (eregi("YES",$include_rollover))
				{
				##### Gather inbound calls from drop inbound group if selected
				$stmt="select drop_inbound_group from vicidial_campaigns where campaign_id='$group[$i]' $LOGallowed_campaignsSQL and drop_inbound_group NOT LIKE \"%NONE%\" and drop_inbound_group is NOT NULL and drop_inbound_group != '';";
				$rslt=mysql_query($stmt, $link);
				if ($DB) {echo "$stmt\n";}
				$in_groups_to_print = mysql_num_rows($rslt);
				if ($in_groups_to_print > 0)
					{
					$row=mysql_fetch_row($rslt);
					$group_drop[$i] = $row[0];
					$rollover_groups_count++;
					}

				$length_in_secZ=0;
				$queue_secondsZ=0;
				$agent_alert_delayZ=0;
				$stmt="select status,length_in_sec,queue_seconds,agent_alert_delay,call_date,UNIX_TIMESTAMP(call_date),phone_number,campaign_id,closecallid,lead_id,uniqueid from vicidial_closer_log,vicidial_inbound_groups where call_date >= '$query_date_BEGIN' and call_date <= '$query_date_END' and group_id=campaign_id and campaign_id='$group_drop[$i]';";
				$rslt=mysql_query($stmt, $link);
				if ($DB) {echo "$stmt\n";}
				$INallcalls_to_printZ = mysql_num_rows($rslt);
				$y=0;
				while ($y < $INallcalls_to_printZ)
					{
					$row=mysql_fetch_row($rslt);

					$k=0;
					$front_call_found=0;
					while($k < $p)
						{
						if ($TESTuniqueid[$k] == $row[10])
							{$front_call_found++;}
						$k++;
						}
					if ($front_call_found > 0)
						{
						$length_in_secZ = $row[1];
						$queue_secondsZ = $row[2];
						$agent_alert_delayZ = $row[3];

						$TOTALdelay =		round($agent_alert_delayZ / 1000);
						$thiscallsec = (($length_in_secZ - $queue_secondsZ) - $TOTALdelay);
						if ($thiscallsec < 0)
							{$thiscallsec = 0;}
						$inTOTALsec =	($inTOTALsec + $thiscallsec);	

						$CPstatus[$u] =			$row[0];
						$CPlength_in_sec[$u] =	$inTOTALsec;
						$CPcall_date[$u] =		$row[4];
						$CPepoch[$u] =			$row[5];
						$CPphone_number[$u] =	$row[6];
						$CPcampaign_id[$u] =	$row[7];
						$CPvicidial_id[$u] =	$row[8];
						$CPlead_id[$u] =		$row[9];
						$CPin_out[$u] =			'IN';
						$u++;
						}
					$y++;
					}
				}


			$out_of_call_time=0;
			$length_in_sec[$i]=0;
			$queue_seconds[$i]=0;
			$agent_sec[$i]=0;
			$pause_sec[$i]=0;
			$talk_sec[$i]=0;
			$calls_count[$i]=0;
			$calls_count_IN[$i]=0;
			$drop_count[$i]=0;
			$drop_count_OUT[$i]=0;
			$system_count[$i]=0;
			$agent_count[$i]=0;
			$ptp_count[$i]=0;
			$rtp_count[$i]=0;
			$na_count[$i]=0;
			$answer_count[$i]=0;
			$max_queue_seconds[$i]=0;
			$Hlength_in_sec=$MT;
			$Hqueue_seconds=$MT;
			$Hagent_sec=$MT;
			$Hpause_sec=$MT;
			$Htalk_sec=$MT;
			$Hcalls_count=$MT;
			$Hcalls_count_IN=$MT;
			$Hdrop_count=$MT;
			$Hdrop_count_OUT=$MT;
			$Hsystem_count=$MT;
			$Hagent_count=$MT;
			$Hptp_count=$MT;
			$Hrtp_count=$MT;
			$Hna_count=$MT;
			$Hanswer_count=$MT;
			$Hmax_queue_seconds=$MT;
			$hTOTALcalls =	0;
			$hANSWERcalls =	0;
			$hSUMagent =	0;
			$hSUMpause =	0;
			$hSUMtalk =		0;
			$hAVGtalk =		0;
			$hSUMqueue =	0;
			$hAVGqueue =	0;
			$hMAXqueue =	0;
			$hDROPcalls =	0;
			$hPRINT =		0;
			$hTOTcalls_count =			0;
			$hTOTsystem_count =			0;
			$hTOTagent_count =			0;
			$hTOTptp_count =			0;
			$hTOTrtp_count =			0;
			$hTOTna_count =				0;
			$hTOTanswer_count =			0;
			$hTOTagent_sec =			0;
			$hTOTpause_sec =			0;
			$hTOTtalk_sec =				0;
			$hTOTtalk_avg =				0;
			$hTOTqueue_seconds =		0;
			$hTOTqueue_avg =			0;
			$hTOTmax_queue_seconds =	0;
			$hTOTdrop_count =			0;

			##### Parse through the agent time records to tally the time
			$p=0;
			while ($p < $s)
				{
				$call_date = explode(" ", $ATcall_date[$p]);
				$call_time = ereg_replace("[^0-9]","",$call_date[1]);
				$epoch = $ATepoch[$p];
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
				$Cmin = date("i", $epoch);
				if ($interval_count==96)
					{
					$ChourX = ($Chour * 4);
					if ($Cmin < 15) {$Cmin = "00"; $CminX = 0;}
					if ( ($Cmin >= 15) and ($Cmin < 30) ) {$Cmin = "15"; $CminX = 1;}
					if ( ($Cmin >= 30) and ($Cmin < 45) ) {$Cmin = "30"; $CminX = 2;}
					if ($Cmin >= 45) {$Cmin = "45"; $CminX = 3;}
					$Chour = ($ChourX + $CminX);
					}
				if ($interval_count==48)
					{
					$ChourX = ($Chour * 2);
					if ($Cmin < 30) {$Cmin = "00"; $CminX = 0;}
					if ($Cmin >= 30) {$Cmin = "30"; $CminX = 1;}
					$Chour = ($ChourX + $CminX);
					}

				if ( ($call_time > $CTstart) and ($call_time < $CTstop) )
					{
					$agent_sec[$i] = ($agent_sec[$i] + $ATagent_sec[$p]);
					$Hagent_sec[$Chour] = ($Hagent_sec[$Chour] + $ATagent_sec[$p]);
					$pause_sec[$i] = ($pause_sec[$i] + $ATpause_sec[$p]);
					$Hpause_sec[$Chour] = ($Hpause_sec[$Chour] + $ATpause_sec[$p]);

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
					{echo "$Hcalltime[$Chour] | AGENT: $agent_sec[$i] PAUSE: $pause_sec[$i]\n";}
				$p++;
				}






			##### Parse through call records to tally the counts
			$p=0;
			while ($p < $u)
				{
				$call_date = explode(" ", $CPcall_date[$p]);
				$call_time = ereg_replace("[^0-9]","",$call_date[1]);
				$epoch = $CPepoch[$p];
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
				$Cmin = date("i", $epoch);
				if ($interval_count==96)
					{
					$ChourX = ($Chour * 4);
					if ($Cmin < 15) {$Cmin = "00"; $CminX = 0;}
					if ( ($Cmin >= 15) and ($Cmin < 30) ) {$Cmin = "15"; $CminX = 1;}
					if ( ($Cmin >= 30) and ($Cmin < 45) ) {$Cmin = "30"; $CminX = 2;}
					if ($Cmin >= 45) {$Cmin = "45"; $CminX = 3;}
					$Chour = ($ChourX + $CminX);
					}
				if ($interval_count==48)
					{
					$ChourX = ($Chour * 2);
					if ($Cmin < 30) {$Cmin = "00"; $CminX = 0;}
					if ($Cmin >= 30) {$Cmin = "30"; $CminX = 1;}
					$Chour = ($ChourX + $CminX);
					}

				if ( ($call_time > $CTstart) and ($call_time < $CTstop) )
					{
					$calls_count[$i]++;
					$length_in_sec[$i] =	($length_in_sec[$i] + $CPlength_in_sec[$p]);
					$Hlength_in_sec[$Chour] =	($Hlength_in_sec[$Chour] + $row[1]);
					$Hqueue_seconds[$Chour] =	($Hqueue_seconds[$Chour] + $row[2]);
					$TEMPtalk = $CPlength_in_sec[$p];
					if ($TEMPtalk < 0) {$TEMPtalk = 0;}
					$talk_sec[$i] =	($talk_sec[$i] + $TEMPtalk);
					$Htalk_sec[$Chour] =	($Htalk_sec[$Chour] + $TEMPtalk);

					$Hcalls_count[$Chour]++;
					if (eregi("DROP",$CPstatus[$p]))
						{
						if ($CPin_out[$p] == 'OUT')
							{
							$drop_count_OUT[$i]++;
							$Hdrop_count_OUT[$Chour]++;
							}
						$drop_count[$i]++;
						$Hdrop_count[$Chour]++;
						}
					else
						{
						$answer_count[$i]++;
						$Hanswer_count[$Chour]++;
						}
					if (eregi("\|$CPstatus[$p]\|",'|NA|NEW|QUEUE|INCALL|DROP|XDROP|AA|AM|AL|AFAX|AB|ADC|DNCL|DNCC|PU|PM|SVYEXT|SVYHU|SVYVM|SVYREC|QVMAIL|'))
						{
						$system_count[$i]++;
						$Hsystem_count[$Chour]++;
						}
					else
						{
						$agent_count[$i]++;
						$Hagent_count[$Chour]++;
						}
					if ($CPstatus[$p] == 'NA')
						{
						$na_count[$i]++;
						$Hna_count[$Chour]++;
						}
					if ($CPin_out[$p] == 'IN')
						{
						$calls_count_IN[$i]++;
						$Hcalls_count_IN[$Chour]++;
						}

					$k=0;
					while($k < $sale_ct)
						{
						if ($sale_statusesLIST[$k] == $CPstatus[$p])
							{
							$ptp_count[$i]++;
							$Hptp_count[$Chour]++;
							}
						$k++;
						}

					$k=0;
					while($k < $dnc_ct)
						{
						if ($dnc_statusesLIST[$k] == $CPstatus[$p])
							{
							$rtp_count[$i]++;
							$Hrtp_count[$Chour]++;
							}
						$k++;
						}

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

			if ( ($calls_count_IN[$i] > 0) and ($drop_count_OUT[$i] > 0) )
				{
				$drop_count[$i] = ($drop_count[$i] - $calls_count_IN[$i]);
				$calls_count[$i] = ($calls_count[$i] - $calls_count_IN[$i]);
				$system_count[$i] = ($system_count[$i] - $calls_count_IN[$i]);
				if ($drop_count[$i] < 0)
					{$drop_count[$i] = 0;}
				}
			$TOTcalls_count =			($TOTcalls_count + $calls_count[$i]);
			$TOTsystem_count =			($TOTsystem_count + $system_count[$i]);
			$TOTagent_count =			($TOTagent_count + $agent_count[$i]);
			$TOTptp_count =				($TOTptp_count + $ptp_count[$i]);
			$TOTrtp_count =				($TOTrtp_count + $rtp_count[$i]);
			$TOTna_count =				($TOTna_count + $na_count[$i]);
			$TOTanswer_count =			($TOTanswer_count + $answer_count[$i]);
			$TOTagent_sec =				($TOTagent_sec + $agent_sec[$i]);
			$TOTpause_sec =				($TOTpause_sec + $pause_sec[$i]);
			$TOTtalk_sec =				($TOTtalk_sec + $talk_sec[$i]);
			$TOTqueue_seconds =			($TOTqueue_seconds + $queue_seconds[$i]);
			$TOTdrop_count =			($TOTdrop_count + $drop_count[$i]);
			if ($max_queue_seconds[$i] > $TOTmax_queue_seconds)
				{$TOTmax_queue_seconds = $max_queue_seconds[$i];}

			$agent_sec[$i] =			sec_convert($agent_sec[$i],'H'); 
			$pause_sec[$i] =			sec_convert($pause_sec[$i],'H'); 
			$talk_sec[$i] =				sec_convert($talk_sec[$i],'H'); 
			$talk_avg[$i] =				sec_convert($talk_avg[$i],'H'); 
			$queue_seconds[$i] =		sec_convert($queue_seconds[$i],'H'); 
			$queue_avg[$i] =			sec_convert($queue_avg[$i],'H'); 
			$max_queue_seconds[$i] =	sec_convert($max_queue_seconds[$i],'H'); 


			$groupDISPLAY =	sprintf("%-40s", "$group[$i] - $group_cname[$i]");
			$gTOTALcalls =	sprintf("%6s", $calls_count[$i]);
			$gSYSTEMcalls =	sprintf("%6s", $system_count[$i]);
			$gAGENTcalls =	sprintf("%6s", $agent_count[$i]);
			$gPTPcalls =	sprintf("%6s", $ptp_count[$i]);
			$gRTPcalls =	sprintf("%6s", $rtp_count[$i]);
			if ( ($calls_count[$i] < 1) or ($na_count[$i] < 1) )
				{$gNApercent=0;}
			else
				{$gNApercent = ( ($na_count[$i] / $calls_count[$i]) * 100);}
			$gNApercent =	sprintf("%6.2f",$gNApercent);
			$gNAcalls =		sprintf("%6s", $na_count[$i]);
			$gANSWERcalls =	sprintf("%6s", $answer_count[$i]);
			$gSUMagent =	sprintf("%10s", $agent_sec[$i]);
			$gSUMpause =	sprintf("%10s", $pause_sec[$i]);
			$gSUMtalk =		sprintf("%9s", $talk_sec[$i]);
			$gAVGtalk =		sprintf("%7s", $talk_avg[$i]);
			$gSUMqueue =	sprintf("%9s", $queue_seconds[$i]);
			$gAVGqueue =	sprintf("%7s", $queue_avg[$i]);
			$gMAXqueue =	sprintf("%7s", $max_queue_seconds[$i]);
			if ( ($calls_count[$i] < 1) or ($drop_count[$i] < 1) )
				{$gDROPpercent=0;}
			else
				{$gDROPpercent = ( ($drop_count[$i] / $calls_count[$i]) * 100);}
			$gDROPpercent =		sprintf("%6.2f",$gDROPpercent);
			$gDROPcalls =	sprintf("%6s", $drop_count[$i]);

			while(strlen($groupDISPLAY)>40) {$groupDISPLAY = substr("$groupDISPLAY", 0, -1);}

			$MAIN .= "| $groupDISPLAY | $gTOTALcalls | $gSYSTEMcalls | $gAGENTcalls | $gPTPcalls | $gRTPcalls | $gNApercent%| $gDROPpercent%| $gSUMagent | $gSUMpause |";
			if ($DB) {$MAIN .= " $gDROPcalls($calls_count_IN[$i]/$drop_count_OUT[$i]) |";}
			$MAIN .= "<!-- OUT OF CALLTIME: $out_of_call_time -->\n";

			### hour by hour sumaries
			$SUBoutput .= "\n---------- $group[$i] - $group_cname[$i]     INTERVAL BREAKDOWN:\n";
			$SUBoutput .= "+---------------------+--------+--------+--------+--------+--------+--------+--------+------------+------------+\n";
			$SUBoutput .= "|                     |        | SYSTEM | AGENT  |        |        | NO     |        | AGENT      | AGENT      |\n";
			$SUBoutput .= "|                     | TOTAL  | RELEASE| RELEASE| SALE   | DNC    | ANSWER | DROP   | LOGIN      | PAUSE      |\n";
			$SUBoutput .= "| INTERVAL            | CALLS  | CALLS  | CALLS  | CALLS  | CALLS  | PERCENT| PERCENT| TIME(H:M:S)| TIME(H:M:S)|\n";
			$SUBoutput .= "+---------------------+--------+--------+--------+--------+--------+--------+--------+------------+------------+\n";

			$h=0;
			while ($h < $interval_count)
				{
				if ($Hcalltime[$h] > 0)
					{
					if (strlen($Hcalls_count[$h]) < 1)			{$Hcalls_count[$h] = 0;}
					if (strlen($Hsystem_count[$h]) < 1)			{$Hsystem_count[$h] = 0;}
					if (strlen($Hagent_count[$h]) < 1)			{$Hagent_count[$h] = 0;}
					if (strlen($Hptp_count[$h]) < 1)			{$Hptp_count[$h] = 0;}
					if (strlen($Hrtp_count[$h]) < 1)			{$Hrtp_count[$h] = 0;}
					if (strlen($Hna_count[$h]) < 1)				{$Hna_count[$h] = 0;}
					if (strlen($Hanswer_count[$h]) < 1)			{$Hanswer_count[$h] = 0;}
					if (strlen($Hagent_sec[$h]) < 1)			{$Hagent_sec[$h] = 0;}
					if (strlen($Hpause_sec[$h]) < 1)			{$Hpause_sec[$h] = 0;}
					if (strlen($Htalk_sec[$h]) < 1)				{$Htalk_sec[$h] = 0;}
					if (strlen($Hqueue_seconds[$h]) < 1)		{$Hqueue_seconds[$h] = 0;}
					if (strlen($Hmax_queue_seconds[$h]) < 1)	{$Hmax_queue_seconds[$h] = 0;}
					if (strlen($Hdrop_count[$h]) < 1)			{$Hdrop_count[$h] = 0;}

					if ( ($Hcalls_count_IN[$h] > 0) and ($Hdrop_count_OUT[$h] > 0) )
						{
						$Hdrop_count[$h] = ($Hdrop_count[$h] - $Hcalls_count_IN[$h]);
						$Hcalls_count[$h] = ($Hcalls_count[$h] - $Hcalls_count_IN[$h]);
						$Hsystem_count[$h] = ($Hsystem_count[$h] - $Hcalls_count_IN[$h]);
						if ($Hdrop_count[$h] < 0)
							{$Hdrop_count[$h] = 0;}
						}
					$hTOTcalls_count =			($hTOTcalls_count + $Hcalls_count[$h]);
					$hTOTsystem_count =			($hTOTsystem_count + $Hsystem_count[$h]);
					$hTOTagent_count =			($hTOTagent_count + $Hagent_count[$h]);
					$hTOTptp_count =			($hTOTptp_count + $Hptp_count[$h]);
					$hTOTrtp_count =			($hTOTrtp_count + $Hrtp_count[$h]);
					$hTOTna_count =				($hTOTna_count + $Hna_count[$h]);
					$hTOTanswer_count =			($hTOTanswer_count + $Hanswer_count[$h]);
					$hTOTagent_sec =			($hTOTagent_sec + $Hagent_sec[$h]);
					$hTOTpause_sec =			($hTOTpause_sec + $Hpause_sec[$h]);
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

					$Hagent_sec[$h] =			sec_convert($Hagent_sec[$h],'H'); 
					$Hpause_sec[$h] =			sec_convert($Hpause_sec[$h],'H'); 
					$Htalk_sec[$h] =			sec_convert($Htalk_sec[$h],'H'); 
					$Htalk_avg[$h] =			sec_convert($Htalk_avg[$h],'H'); 
					$Hqueue_seconds[$h] =		sec_convert($Hqueue_seconds[$h],'H'); 
					$Hqueue_avg[$h] =			sec_convert($Hqueue_avg[$h],'H'); 
					$Hmax_queue_seconds[$h] =	sec_convert($Hmax_queue_seconds[$h],'H');
					
					$hTOTALcalls =	sprintf("%6s", $Hcalls_count[$h]);
					$hSYSTEMcalls =	sprintf("%6s", $Hsystem_count[$h]);
					$hAGENTcalls =	sprintf("%6s", $Hagent_count[$h]);
					$hPTPcalls =	sprintf("%6s", $Hptp_count[$h]);
					$hRTPcalls =	sprintf("%6s", $Hrtp_count[$h]);
					if ( ($Hcalls_count[$h] < 1) or ($Hna_count[$h] < 1) )
						{$hNApercent=0;}
					else
						{$hNApercent = ( ($Hna_count[$h] / $Hcalls_count[$h]) * 100);}
					$hNApercent =		sprintf("%6.2f",$hNApercent);
					$hNAcalls =		sprintf("%6s", $Hna_count[$h]);
					$hANSWERcalls =	sprintf("%6s", $Hanswer_count[$h]);
					$hSUMagent =	sprintf("%10s", $Hagent_sec[$h]);
					$hSUMpause =	sprintf("%10s", $Hpause_sec[$h]);
					$hSUMtalk =		sprintf("%9s", $Htalk_sec[$h]);
					$hAVGtalk =		sprintf("%7s", $Htalk_avg[$h]);
					$hSUMqueue =	sprintf("%9s", $Hqueue_seconds[$h]);
					$hAVGqueue =	sprintf("%7s", $Hqueue_avg[$h]);
					$hMAXqueue =	sprintf("%7s", $Hmax_queue_seconds[$h]);
					if ( ($Hcalls_count[$h] < 1) or ($Hdrop_count[$h] < 1) )
						{$hDROPpercent=0;}
					else
						{$hDROPpercent = ( ($Hdrop_count[$h] / $Hcalls_count[$h]) * 100);}
					$hDROPpercent =		sprintf("%6.2f",$hDROPpercent);
					$hDROPcalls =	sprintf("%6s", $Hdrop_count[$h]);
					$hPRINT =		sprintf("%19s", $Hcalltime_HHMM[$h]);

					$SUBoutput .= "| $hPRINT | $hTOTALcalls | $hSYSTEMcalls | $hAGENTcalls | $hPTPcalls | $hRTPcalls | $hNApercent%| $hDROPpercent%| $hSUMagent | $hSUMpause |\n";
					if ($DB) {$SUBoutput .= " $hDROPcalls($Hcalls_count_IN[$h]/$Hdrop_count_OUT[$h]) |\n";}
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

			$hTOTagent_sec =			sec_convert($hTOTagent_sec,'H'); 
			$hTOTpause_sec =			sec_convert($hTOTpause_sec,'H'); 
			$hTOTtalk_sec =				sec_convert($hTOTtalk_sec,'H'); 
			$hTOTtalk_avg =				sec_convert($hTOTtalk_avg,'H'); 
			$hTOTqueue_seconds =		sec_convert($hTOTqueue_seconds,'H'); 
			$hTOTqueue_avg =			sec_convert($hTOTqueue_avg,'H'); 
			$hTOTmax_queue_seconds =	sec_convert($hTOTmax_queue_seconds,'H'); 

			$hTOTcalls_count =			sprintf("%6s", $hTOTcalls_count);
			$hTOTsystem_count =			sprintf("%6s", $hTOTsystem_count);
			$hTOTagent_count =			sprintf("%6s", $hTOTagent_count);
			$hTOTptp_count =			sprintf("%6s", $hTOTrtp_count);
			$hTOTrtp_count =			sprintf("%6s", $hTOTptp_count);
			if ( ($hTOTcalls_count < 1) or ($hTOTna_count < 1) )
				{$hTOTna_percent=0;}
			else
				{$hTOTna_percent = ( ($hTOTna_count / $hTOTcalls_count) * 100);}
			$hTOTna_percent =			sprintf("%6.2f",$hTOTna_percent);
			$hTOTna_count =				sprintf("%6s", $hTOTna_count);
			$hTOTanswer_count =			sprintf("%6s", $hTOTanswer_count);
			$hTOTagent_sec =			sprintf("%10s", $hTOTagent_sec);
			$hTOTpause_sec =			sprintf("%10s", $hTOTpause_sec);
			$hTOTtalk_sec =				sprintf("%9s", $hTOTtalk_sec);
			$hTOTtalk_avg =				sprintf("%7s", $hTOTtalk_avg);
			$hTOTqueue_seconds =		sprintf("%9s", $hTOTqueue_seconds);
			$hTOTqueue_avg =			sprintf("%7s", $hTOTqueue_avg);
			$hTOTmax_queue_seconds =	sprintf("%7s", $hTOTmax_queue_seconds);
			if ( ($hTOTcalls_count < 1) or ($hTOTdrop_count < 1) )
				{$hTOTdrop_percent=0;}
			else
				{$hTOTdrop_percent = ( ($hTOTdrop_count / $hTOTcalls_count) * 100);}
			$hTOTdrop_percent =			sprintf("%6.2f",$hTOTdrop_percent);
			$hTOTdrop_count =			sprintf("%6s", $hTOTdrop_count);

			$SUBoutput .= "+---------------------+--------+--------+--------+--------+--------+--------+--------+------------+------------+\n";
			$SUBoutput .= "| TOTALS              | $hTOTcalls_count | $hTOTsystem_count | $hTOTagent_count | $hTOTptp_count | $hTOTrtp_count | $hTOTna_percent%| $hTOTdrop_percent%| $hTOTagent_sec | $hTOTpause_sec |\n";
			$SUBoutput .= "+---------------------+--------+--------+--------+--------+--------+--------+--------+------------+------------+\n";

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

		$TOTagent_sec =			sec_convert($TOTagent_sec,'H'); 
		$TOTpause_sec =			sec_convert($TOTpause_sec,'H'); 
		$TOTtalk_sec =			sec_convert($TOTtalk_sec,'H'); 
		$TOTtalk_avg =			sec_convert($TOTtalk_avg,'H'); 
		$TOTqueue_seconds =		sec_convert($TOTqueue_seconds,'H'); 
		$TOTqueue_avg =			sec_convert($TOTqueue_avg,'H'); 
		$TOTmax_queue_seconds =	sec_convert($TOTmax_queue_seconds,'H'); 

		$i =					sprintf("%4s", $i);
		$TOTcalls_count =		sprintf("%6s", $TOTcalls_count);
		$TOTsystem_count =		sprintf("%6s", $TOTsystem_count);
		$TOTagent_count =		sprintf("%6s", $TOTagent_count);
		$TOTptp_count =			sprintf("%6s", $TOTptp_count);
		$TOTrtp_count =			sprintf("%6s", $TOTrtp_count);
		if ( ($TOTcalls_count < 1) or ($TOTna_count < 1) )
			{$TOTna_percent=0;}
		else
			{$TOTna_percent = ( ($TOTna_count / $TOTcalls_count) * 100);}
		$TOTna_percent =		sprintf("%6.2f",$TOTna_percent);
		$TOTna_count =			sprintf("%6s", $TOTna_count);
		$TOTanswer_count =		sprintf("%6s", $TOTanswer_count);
		$TOTagent_sec =			sprintf("%10s", $TOTagent_sec);
		$TOTpause_sec =			sprintf("%10s", $TOTpause_sec);
		$TOTtalk_sec =			sprintf("%9s", $TOTtalk_sec);
		$TOTtalk_avg =			sprintf("%7s", $TOTtalk_avg);
		$TOTqueue_seconds =		sprintf("%9s", $TOTqueue_seconds);
		$TOTqueue_avg =			sprintf("%7s", $TOTqueue_avg);
		$TOTmax_queue_seconds =	sprintf("%7s", $TOTmax_queue_seconds);
		if ( ($TOTcalls_count < 1) or ($TOTdrop_count < 1) )
			{$TOTdrop_percent=0;}
		else
			{$TOTdrop_percent = ( ($TOTdrop_count / $TOTcalls_count) * 100);}
		$TOTdrop_percent =		sprintf("%6.2f",$TOTdrop_percent);
		$TOTdrop_count =		sprintf("%6s", $TOTdrop_count);

		$MAIN .= "+------------------------------------------+--------+--------+--------+--------+--------+--------+--------+------------+------------+\n";
		$MAIN .= "| TOTALS       Campaigns: $i             | $TOTcalls_count | $TOTsystem_count | $TOTagent_count | $TOTptp_count | $TOTrtp_count | $TOTna_percent%| $TOTdrop_percent%| $TOTagent_sec | $TOTpause_sec |\n";
		$MAIN .= "+------------------------------------------+--------+--------+--------+--------+--------+--------+--------+------------+------------+\n";
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
