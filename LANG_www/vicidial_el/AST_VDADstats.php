<?php 
# AST_VDADstats.php
# 
# Copyright (C) 2010  Matt Florell <vicidial@gmail.com>    LICENSE: AGPLv2
#
# CHANGES
# 60619-1718 - Added variable filtering to eliminate SQL injection attack threat
#            - Added required user/pass to gain access to this page
# 61215-1139 - Added drop percentage of answered and round-2 decimal
# 71008-1436 - Added shift to be defined in dbconnect.php
# 71218-1155 - Added end_date for multi-day reports
# 80430-1920 - Added Customer hangup cause stats
# 80620-0031 - Fixed human answered calculation for drop perfentage
# 80709-0230 - Added time stats to call statuses
# 80717-2118 - Added calls/hour out of agent login time in status summary
# 80722-2049 - Added Status Category stats
# 81109-2341 - Added Productivity Rating
# 90225-1140 - Changed to multi-campaign capability
# 90310-2034 - Admin header
# 90508-0644 - Changed to PHP long tags
# 90524-2231 - Changed to use functions.php for seconds to HH:MM:SS conversion
# 90608-0251 - Added optional carrier codes stats, made graph at bottom optional
# 90806-0001 - Added CI(Customer Interaction/Human Answered) stats, added option to add inbound rollover stats to these
# 90827-1154 - Added List ID breakdown of calls
# 91222-0843 - Fixed ALL-CAMPAIGNS inbound rollover issue(bug #262), and some other bugs
# 100202-1034 - Added statuses to no-answer section
# 100214-1421 - Sort menu alphabetically
# 100216-0042 - Added popup date selector
#

header ("Content-type: text/html; charset=utf-8");

require("dbconnect.php");
require("functions.php");

$PHP_AUTH_USER=$_SERVER['PHP_AUTH_USER'];
$PHP_AUTH_PW=$_SERVER['PHP_AUTH_PW'];
$PHP_SELF=$_SERVER['PHP_SELF'];
if (isset($_GET["print_calls"]))			{$print_calls=$_GET["print_calls"];}
	elseif (isset($_POST["print_calls"]))	{$print_calls=$_POST["print_calls"];}
if (isset($_GET["outbound_rate"]))			{$outbound_rate=$_GET["outbound_rate"];}
	elseif (isset($_POST["outbound_rate"]))	{$outbound_rate=$_POST["outbound_rate"];}
if (isset($_GET["costformat"]))				{$costformat=$_GET["costformat"];}
	elseif (isset($_POST["costformat"]))	{$costformat=$_POST["costformat"];}
if (isset($_GET["include_rollover"]))			{$include_rollover=$_GET["include_rollover"];}
	elseif (isset($_POST["include_rollover"]))	{$include_rollover=$_POST["include_rollover"];}
if (isset($_GET["carrier_stats"]))			{$carrier_stats=$_GET["carrier_stats"];}
	elseif (isset($_POST["carrier_stats"]))	{$carrier_stats=$_POST["carrier_stats"];}
if (isset($_GET["bottom_graph"]))			{$bottom_graph=$_GET["bottom_graph"];}
	elseif (isset($_POST["bottom_graph"]))	{$bottom_graph=$_POST["bottom_graph"];}
if (isset($_GET["agent_hours"]))			{$agent_hours=$_GET["agent_hours"];}
	elseif (isset($_POST["agent_hours"]))	{$agent_hours=$_POST["agent_hours"];}
if (isset($_GET["group"]))				{$group=$_GET["group"];}
	elseif (isset($_POST["group"]))		{$group=$_POST["group"];}
if (isset($_GET["query_date"]))				{$query_date=$_GET["query_date"];}
	elseif (isset($_POST["query_date"]))	{$query_date=$_POST["query_date"];}
if (isset($_GET["end_date"]))				{$end_date=$_GET["end_date"];}
	elseif (isset($_POST["end_date"]))		{$end_date=$_POST["end_date"];}
if (isset($_GET["shift"]))				{$shift=$_GET["shift"];}
	elseif (isset($_POST["shift"]))		{$shift=$_POST["shift"];}
if (isset($_GET["DB"]))					{$DB=$_GET["DB"];}
	elseif (isset($_POST["DB"]))		{$DB=$_POST["DB"];}
if (isset($_GET["submit"]))				{$submit=$_GET["submit"];}
	elseif (isset($_POST["submit"]))	{$submit=$_POST["submit"];}
if (isset($_GET["ΕΠΙΒΕΒΑΙΩΣΗ"]))				{$ΕΠΙΒΕΒΑΙΩΣΗ=$_GET["ΕΠΙΒΕΒΑΙΩΣΗ"];}
	elseif (isset($_POST["ΕΠΙΒΕΒΑΙΩΣΗ"]))	{$ΕΠΙΒΕΒΑΙΩΣΗ=$_POST["ΕΠΙΒΕΒΑΙΩΣΗ"];}

$PHP_AUTH_USER = ereg_replace("[^0-9a-zA-Z]","",$PHP_AUTH_USER);
$PHP_AUTH_PW = ereg_replace("[^0-9a-zA-Z]","",$PHP_AUTH_PW);

if (strlen($shift)<2) {$shift='ALL';}
if (strlen($bottom_graph)<2) {$bottom_graph='NO';}
if (strlen($carrier_stats)<2) {$carrier_stats='NO';}
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

##### SERVER CARRIER LOGGING LOOKUP #####
$stmt = "SELECT count(*) FROM servers where carrier_logging_active='Y' and max_vicidial_trunks > 0;";
$rslt=mysql_query($stmt, $link);
if ($DB) {echo "$stmt\n";}
$srv_conf_ct = mysql_num_rows($rslt);
if ($srv_conf_ct > 0)
	{
	$row=mysql_fetch_row($rslt);
	$carrier_logging_active =		$row[0];
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
    echo "Ακυρο Ονομα Χρήστη/Κωδικός Πρόσβασης: |$PHP_AUTH_USER|$PHP_AUTH_PW|\n";
    exit;
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

$stmt="select campaign_id,campaign_name from vicidial_campaigns order by campaign_id;";
$rslt=mysql_query($stmt, $link);
if ($DB) {echo "$stmt\n";}
$campaigns_to_print = mysql_num_rows($rslt);
$i=0;
while ($i < $campaigns_to_print)
	{
	$row=mysql_fetch_row($rslt);
	$groups[$i] =		$row[0];
	$group_names[$i] =	$row[1];
	if (ereg("--ALL",$group_string) )
		{$group[$i] = $groups[$i];}
	$i++;
	}

$rollover_groups_count=0;
$i=0;
$group_string='|';
$group_ct = count($group);
while($i < $group_ct)
	{
	$group_string .= "$group[$i]|";
	$group_SQL .= "'$group[$i]',";
	$groupQS .= "&group[]=$group[$i]";

	if (eregi("YES",$include_rollover))
		{
		$stmt="select drop_inbound_group from vicidial_campaigns where campaign_id='$group[$i]' and drop_inbound_group NOT LIKE \"%NONE%\" and drop_inbound_group is NOT NULL and drop_inbound_group != '';";
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
if (strlen($group_drop_SQL) < 2)
	{$group_drop_SQL = "''";}
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
echo "<TITLE>Outbound Stats</TITLE></HEAD><BODY BGCOLOR=WHITE marginheight=0 marginwidth=0 leftmargin=0 topmargin=0>\n";

$short_header=1;

require("admin_header.php");

echo "<TABLE CELLPADDING=4 CELLSPACING=0><TR><TD>";

echo "<FORM ACTION=\"$PHP_SELF\" METHOD=GET name=vicidial_report id=vicidial_report>\n";
echo "<TABLE CELLSPACING=3><TR><TD VALIGN=TOP> Ημερομηνίες:<BR>";
echo "<INPUT TYPE=HIDDEN NAME=agent_hours VALUE=\"$agent_hours\">\n";
echo "<INPUT TYPE=HIDDEN NAME=DB VALUE=\"$DB\">\n";
echo "<INPUT TYPE=HIDDEN NAME=outbound_rate VALUE=\"$outbound_rate\">\n";
echo "<INPUT TYPE=HIDDEN NAME=costformat VALUE=\"$costformat\">\n";
echo "<INPUT TYPE=HIDDEN NAME=print_calls VALUE=\"$print_calls\">\n";
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

echo "<BR> to <BR><INPUT TYPE=TEXT NAME=end_date SIZE=10 MAXLENGTH=10 VALUE=\"$end_date\">";

?>
<script language="JavaScript">
var o_cal = new tcal ({
	// form name
	'formname': 'vicidial_report',
	// input name
	'controlname': 'end_date'
});
o_cal.a_tpl.yearscroll = false;
// o_cal.a_tpl.weekstart = 1; // Δευτέρα week start
</script>
<?php

echo "</TD><TD VALIGN=TOP> Εκστρατείες:<BR>";
echo "<SELECT SIZE=5 NAME=group[] multiple>\n";
if  (eregi("--ALL--",$group_string))
	{echo "<option value=\"--ALL--\" selected>-- ALL ΕΚΣΤΡΑΤΕΙΕΣ --</option>\n";}
else
	{echo "<option value=\"--ALL--\">-- ALL ΕΚΣΤΡΑΤΕΙΕΣ --</option>\n";}
$o=0;
while ($campaigns_to_print > $o)
	{
	if (eregi("$groups[$o]\|",$group_string)) {echo "<option selected value=\"$groups[$o]\">$groups[$o] - $group_names[$o]</option>\n";}
	  else {echo "<option value=\"$groups[$o]\">$groups[$o] - $group_names[$o]</option>\n";}
	$o++;
	}
echo "</SELECT>\n";
echo "</TD><TD VALIGN=TOP>";
echo "Include Drop &nbsp; <BR>Rollover:<BR>";
echo "<SELECT SIZE=1 NAME=include_rollover>\n";
echo "<option selected value=\"$include_rollover\">$include_rollover</option>\n";
echo "<option value=\"YES\">YES</option>\n";
echo "<option value=\"NO\">NO</option>\n";
echo "</SELECT>\n";
echo "<BR>Bottom Graph: &nbsp; <BR>\n";
echo "<SELECT SIZE=1 NAME=bottom_graph>\n";
echo "<option selected value=\"$bottom_graph\">$bottom_graph</option>\n";
echo "<option value=\"YES\">YES</option>\n";
echo "<option value=\"NO\">NO</option>\n";
echo "</SELECT><BR>\n";
if ($carrier_logging_active > 0)
	{
	echo "</TD><TD VALIGN=TOP>Carrier Stats: &nbsp; <BR>";
	echo "<SELECT SIZE=1 NAME=carrier_stats>\n";
	echo "<option selected value=\"$carrier_stats\">$carrier_stats</option>\n";
	echo "<option value=\"YES\">YES</option>\n";
	echo "<option value=\"NO\">NO</option>\n";
	echo "</SELECT>\n";
	}
echo "</TD><TD VALIGN=TOP>Shift: &nbsp; <BR>";
echo "<SELECT SIZE=1 NAME=shift>\n";
echo "<option selected value=\"$shift\">$shift</option>\n";
echo "<option value=\"\">--</option>\n";
echo "<option value=\"AM\">AM</option>\n";
echo "<option value=\"PM\">PM</option>\n";
echo "<option value=\"ALL\">ALL</option>\n";
echo "</SELECT><BR><BR>\n";
echo "<INPUT type=submit NAME=ΕΠΙΒΕΒΑΙΩΣΗ VALUE=ΥΠΟΒΑΛΛΩ>\n";
echo "</TD><TD VALIGN=TOP> &nbsp; &nbsp; &nbsp; &nbsp; ";
echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";
if (strlen($group[0]) > 1)
	{
	echo " <a href=\"./admin.php?ADD=34&campaign_id=$group[0]\">ΤΡΟΠΟΠΟΙΗΣΗ</a> | \n";
	echo " <a href=\"./admin.php?ADD=999999\">ΑΝΑΦΟΡΕΣ</a> </FONT>\n";
	}
else
	{
	echo " <a href=\"./admin.php?ADD=10\">CAMPAIGNS</a> | \n";
	echo " <a href=\"./admin.php?ADD=999999\">ΑΝΑΦΟΡΕΣ</a> </FONT>\n";
	}
echo "</TD></TR></TABLE>";
echo "</FORM>\n\n";

echo "<PRE><FONT SIZE=2>\n\n";


if (strlen($group[0]) < 1)
	{
	echo "\n\n";
	echo "ΠΑΡΑΚΑΛΩ ΕΠΙΛΕΞΤΕ ΜΙΑ ΕΚΣΤΡΑΤΕΙΑ ΚΑΙ ΜΙΑ ΗΜΕΡΟΜΗΝΙΑ ΑΝΩΤΕΡΩ, ΚΑΙ ΠΑΤΗΣΤΕ ΕΠΙΒΕΒΑΙΩΣΗ\n";
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


	$OUToutput = '';
	$OUToutput .= "Outbound Calling Stats                             $NOW_TIME\n";

	$OUToutput .= "\n";
	$OUToutput .= "Time range: $query_date_BEGIN to $query_date_END\n\n";
	$OUToutput .= "---------- TOTALS\n";

	$stmt="select count(*),sum(length_in_sec) from vicidial_log where call_date >= '$query_date_BEGIN' and call_date <= '$query_date_END' $group_SQLand;";
	$rslt=mysql_query($stmt, $link);
	if ($DB) {$OUToutput .= "$stmt\n";}
	$row=mysql_fetch_row($rslt);

	$TOTALcallsRAW = $row[0];
	$TOTALsec =		$row[1];
	$inTOTALcallsRAW=0;
	if (eregi("YES",$include_rollover))
		{
		$length_in_secZ=0;
		$queue_secondsZ=0;
		$agent_alert_delayZ=0;
		$stmt="select length_in_sec,queue_seconds,agent_alert_delay from vicidial_closer_log,vicidial_inbound_groups where call_date >= '$query_date_BEGIN' and call_date <= '$query_date_END' and group_id=campaign_id $group_drop_SQLand;";
		$rslt=mysql_query($stmt, $link);
		if ($DB) {echo "$stmt\n";}
		$INallcalls_to_printZ = mysql_num_rows($rslt);
		$y=0;
		while ($y < $INallcalls_to_printZ)
			{
			$row=mysql_fetch_row($rslt);

			$length_in_secZ = $row[0];
			$queue_secondsZ = $row[1];
			$agent_alert_delayZ = $row[2];

			$TOTALdelay =		round($agent_alert_delayZ / 1000);
			$thiscallsec = (($length_in_secZ - $queue_secondsZ) - $TOTALdelay);
			if ($thiscallsec < 0)
				{$thiscallsec = 0;}
			$inTOTALsec =	($inTOTALsec + $thiscallsec);	

			$y++;
			}

		$inTOTALcallsRAW =	$y;
		$TOTALsec = ($TOTALsec + $inTOTALsec);
		$inTOTALcalls =	sprintf("%10s", $inTOTALcallsRAW);
		}

	$TOTALcalls =	sprintf("%10s", $TOTALcallsRAW);
	if ( ($row[0] < 1) or ($TOTALsec < 1) )
		{$average_call_seconds = '         0';}
	else
		{
		$average_call_seconds = ($TOTALsec / $TOTALcallsRAW);
		$average_call_seconds = round($average_call_seconds, 2);
		$average_call_seconds =	sprintf("%10s", $average_call_seconds);
		}

	$OUToutput .= "Συνολικά κλήσεις που τοποθετήθηκαν από την Εκστρατεία: $TOTALcalls\n";
	$OUToutput .= "Μέσος όρος σε δευτερόλεπτα για όλες τις κλήσεις: $average_call_seconds\n";
	if (eregi("YES",$include_rollover))
		{$OUToutput .= "Calls that went to rollover In-Ομάδα:        $inTOTALcalls\n";}


	$OUToutput .= "\n";
	$OUToutput .= "---------- ΑΝΘΡΩΠΙΝΗ ΑΠΑΝΤΗΣΗS\n";

	$stmt="select count(*),sum(length_in_sec) from vicidial_log where call_date >= '$query_date_BEGIN' and call_date <= '$query_date_END' and status IN($customer_interactive_statuses) $group_SQLand;";
	$rslt=mysql_query($stmt, $link);
	if ($DB) {$OUToutput .= "$stmt\n";}
	$row=mysql_fetch_row($rslt);
	$CIcallsRAW =	$row[0];
	$CIsec =		$row[1];

	if (eregi("YES",$include_rollover))
		{
		$length_in_secZ=0;
		$queue_secondsZ=0;
		$agent_alert_delayZ=0;
		$stmt="select length_in_sec,queue_seconds,agent_alert_delay from vicidial_closer_log,vicidial_inbound_groups where call_date >= '$query_date_BEGIN' and call_date <= '$query_date_END' and group_id=campaign_id and vicidial_closer_log.status IN($customer_interactive_statuses) $group_drop_SQLand;";
		$rslt=mysql_query($stmt, $link);
		if ($DB) {echo "$stmt\n";}
		$INallcalls_to_printZ = mysql_num_rows($rslt);
		$y=0;
		while ($y < $INallcalls_to_printZ)
			{
			$row=mysql_fetch_row($rslt);

			$length_in_secZ = $row[0];
			$queue_secondsZ = $row[1];
			$agent_alert_delayZ = $row[2];

			$CIdelay =		round($agent_alert_delayZ / 1000);
			$thiscallsec = (($length_in_secZ - $queue_secondsZ) - $CIdelay);
			if ($thiscallsec < 0)
				{$thiscallsec = 0;}
			$inCIsec =	($inCIsec + $thiscallsec);	

			$y++;
			}

		$inCIcallsRAW =	$y;
		$CIsec = ($CIsec + $inCIsec);
		$CIcallsRAW = ($CIcallsRAW + $inCIcallsRAW);
		}

	$CIcalls =	sprintf("%10s", $CIcallsRAW);
	if ( ($CIcallsRAW < 1) or ($CIsec < 1) )
		{$average_ci_seconds = '         0';}
	else
		{
		$average_ci_seconds = ($CIsec / $CIcallsRAW);
		$average_ci_seconds = round($average_ci_seconds, 2);
		$average_ci_seconds =	sprintf("%10s", $average_ci_seconds);
		}
	$CIsec =		sec_convert($CIsec,'H'); 


	$OUToutput .= "Total Ανθρώπινα Απάντησηed calls for this Εκστρατεία: $CIcalls\n";
	$OUToutput .= "Average Call Length for all HA in seconds:    $average_ci_seconds     Total Time: $CIsec\n";


	$OUToutput .= "\n";
	$OUToutput .= "---------- ΕΓΚΑΤΑΛ\n";

	$stmt="select count(*),sum(length_in_sec) from vicidial_log where call_date >= '$query_date_BEGIN' and call_date <= '$query_date_END' $group_SQLand and status='DROP' and (length_in_sec <= 6000 or length_in_sec is null);";
	$rslt=mysql_query($stmt, $link);
	if ($DB) {$OUToutput .= "$stmt\n";}
	$row=mysql_fetch_row($rslt);
	$DROPcalls =	sprintf("%10s", $row[0]);
	$DROPcallsRAW =	$row[0];
	$DROPseconds =	$row[1];


	# GET LIST OF ALL STATUSES and create SQL from human_answered statuses
	$q=0;
	$stmt = "SELECT status,status_name,human_answered,category from vicidial_statuses;";
	$rslt=mysql_query($stmt, $link);
	if ($DB) {$OUToutput .= "$stmt\n";}
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
		if ($human_answered[$q]=='Y')
			{$camp_ANS_STAT_SQL .=	 "'$row[0]',";}
		$q++;
		$p++;
		}

	$stmt = "SELECT distinct status,status_name,human_answered,category from vicidial_campaign_statuses $group_SQL;";
	$rslt=mysql_query($stmt, $link);
	if ($DB) {$OUToutput .= "$stmt\n";}
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
		if ($human_answered[$q]=='Y')
			{$camp_ANS_STAT_SQL .=	 "'$row[0]',";}
		$q++;
		$p++;
		}
	$camp_ANS_STAT_SQL = eregi_replace(",$",'',$camp_ANS_STAT_SQL);


	$stmt="select count(*) from vicidial_log where call_date >= '$query_date_BEGIN' and call_date <= '$query_date_END' $group_SQLand and status IN($camp_ANS_STAT_SQL);";
	$rslt=mysql_query($stmt, $link);
	if ($DB) {$OUToutput .= "$stmt\n";}
	$row=mysql_fetch_row($rslt);
	$ΑΠΑΝΤΗΣΗcalls =	$row[0];

	if ( ($DROPcalls < 1) or ($TOTALcalls < 1) )
		{$DROPpercent = '0';}
	else
		{
		$DROPpercent = (($DROPcallsRAW / $TOTALcalls) * 100);
		$DROPpercent = round($DROPpercent, 2);
		}

	if ( ($DROPcalls < 1) or ($ΑΠΑΝΤΗΣΗcalls < 1) )
		{$DROPΑΠΑΝΤΗΣΗpercent = '0';}
	else
		{
		$DROPΑΠΑΝΤΗΣΗpercent = (($DROPcallsRAW / $ΑΠΑΝΤΗΣΗcalls) * 100);
		$DROPΑΠΑΝΤΗΣΗpercent = round($DROPΑΠΑΝΤΗΣΗpercent, 2);
		}

	if ( ($DROPseconds < 1) or ($DROPcallsRAW < 1) )
		{$average_hold_seconds = '         0';}
	else
		{
		$average_hold_seconds = ($DROPseconds / $DROPcallsRAW);
		$average_hold_seconds = round($average_hold_seconds, 2);
		$average_hold_seconds =	sprintf("%10s", $average_hold_seconds);
		}

	$OUToutput .= "Total Outbound DROP Calls:                    $DROPcalls  $DROPpercent%\n";
	$OUToutput .= "Percent of DROP Calls taken out of Answers:   $DROPcalls / $ΑΠΑΝΤΗΣΗcalls  $DROPΑΠΑΝΤΗΣΗpercent%\n";

	if (eregi("YES",$include_rollover))
		{
		if ( ($DROPcalls < 1) or ($CIcallsRAW < 1) )
			{$inDROPΑΠΑΝΤΗΣΗpercent = '0';}
		else
			{
			$inDROPΑΠΑΝΤΗΣΗpercent = (($DROPcallsRAW / $CIcallsRAW) * 100);
			$inDROPΑΠΑΝΤΗΣΗpercent = round($inDROPΑΠΑΝΤΗΣΗpercent, 2);
			}

		$OUToutput .= "Percent of DROP/Answer Calls with Rollover:   $DROPcalls / $CIcallsRAW  $inDROPΑΠΑΝΤΗΣΗpercent%\n";
		}

	$OUToutput .= "Μέσος όρος σε δευτερόλεπτα για ΕΓΚΑΤΑΕΙΜΕΝΕΣ κλήσεις: $average_hold_seconds\n";

	$stmt = "select closer_campaigns from vicidial_campaigns $group_SQL;";
	$rslt=mysql_query($stmt, $link);
	$ccamps_to_print = mysql_num_rows($rslt);
	$c=0;
	while ($ccamps_to_print > $c)
		{
		$row=mysql_fetch_row($rslt);
		$closer_campaigns = $row[0];
		$closer_campaigns = preg_replace("/^ | -$/","",$closer_campaigns);
		$closer_campaigns = preg_replace("/ /","','",$closer_campaigns);
		$closer_campaignsSQL .= "'$closer_campaigns',";
		$c++;
		}
	$closer_campaignsSQL = eregi_replace(",$",'',$closer_campaignsSQL);

	$stmt="select count(*) from vicidial_closer_log where call_date >= '$query_date_BEGIN' and call_date <= '$query_date_END' and  campaign_id IN($closer_campaignsSQL) and status NOT IN('DROP','XDROP','HXFER','QVMAIL','HOLDTO','LIVE','QUEUE');";
	$rslt=mysql_query($stmt, $link);
	if ($DB) {$OUToutput .= "$stmt\n";}
	$row=mysql_fetch_row($rslt);
	$TOTALanswers = ($row[0] + $ΑΠΑΝΤΗΣΗcalls);


	$stmt = "SELECT sum(wait_sec + talk_sec + dispo_sec) from vicidial_agent_log where event_time >= '$query_date_BEGIN' and event_time <= '$query_date_END' $group_SQLand;";
	$rslt=mysql_query($stmt, $link);
	if ($DB) {$OUToutput .= "$stmt\n";}
	$row=mysql_fetch_row($rslt);
	$agent_non_pause_sec = $row[0];

	if ($agent_non_pause_sec > 0)
		{
		$AVG_ΑΠΑΝΤΗΣΗagent_non_pause_sec = (($TOTALanswers / $agent_non_pause_sec) * 60);
		$AVG_ΑΠΑΝΤΗΣΗagent_non_pause_sec = round($AVG_ΑΠΑΝΤΗΣΗagent_non_pause_sec, 2);
		}
	else
		{$AVG_ΑΠΑΝΤΗΣΗagent_non_pause_sec=0;}
	$AVG_ΑΠΑΝΤΗΣΗagent_non_pause_sec = sprintf("%10s", $AVG_ΑΠΑΝΤΗΣΗagent_non_pause_sec);

	$OUToutput .= "Productivity Rating:                          $AVG_ΑΠΑΝΤΗΣΗagent_non_pause_sec\n";




	$OUToutput .= "\n";
	$OUToutput .= "---------- NO ΑΠΑΝΤΗΣΗS\n";

	$stmt="select count(*),sum(length_in_sec) from vicidial_log where call_date >= '$query_date_BEGIN' and call_date <= '$query_date_END' $group_SQLand and status IN('NA','ADC','AB','CPDB','CPDUK','CPDATB','CPDNA','CPDREJ','CPDINV','CPDSUA','CPDSI','CPDSNC','CPDSR','CPDSUK','CPDSV','CPDERR') and (length_in_sec <= 60 or length_in_sec is null);";
	$rslt=mysql_query($stmt, $link);
	if ($DB) {$OUToutput .= "$stmt\n";}
	$row=mysql_fetch_row($rslt);
	$autoNAcalls =	sprintf("%10s", $row[0]);

	$stmt="select count(*),sum(length_in_sec) from vicidial_log where call_date >= '$query_date_BEGIN' and call_date <= '$query_date_END' $group_SQLand and status IN('B','DC','N') and (length_in_sec <= 60 or length_in_sec is null);";
	$rslt=mysql_query($stmt, $link);
	if ($DB) {$OUToutput .= "$stmt\n";}
	$row=mysql_fetch_row($rslt);
	$manualNAcalls =	sprintf("%10s", $row[0]);

	$totalNAcalls = ($autoNAcalls + $manualNAcalls);
	$totalNAcalls =	sprintf("%10s", $totalNAcalls);

	if ( ($totalNAcalls < 1) or ($TOTALcalls < 1) )
		{$NApercent = '0';}
	else
		{
		$NApercent = (($totalNAcalls / $TOTALcalls) * 100);
		$NApercent = round($NApercent, 2);
		}

	if ( ($row[0] < 1) or ($row[1] < 1) )
		{$average_na_seconds = '         0';}
	else
		{
		$average_na_seconds = ($row[1] / $row[0]);
		$average_na_seconds = round($average_na_seconds, 2);
		$average_na_seconds =	sprintf("%10s", $average_na_seconds);
		}

	$OUToutput .= "Total NA calls -Busy,Disconnect,RingNoAnswer: $totalNAcalls  $NApercent%\n";
	$OUToutput .= "Total auto NA calls -system-set:              $autoNAcalls\n";
	$OUToutput .= "Total manual NA calls -agent-set:             $manualNAcalls\n";
	$OUToutput .= "Μέσος όρος σε δευτερόλεπτα για ΝΑ κλήσεις: $average_na_seconds\n";


	##############################
	#########  CALL HANGUP REASON STATS

	$TOTALcalls = 0;

	$OUToutput .= "\n";
	$OUToutput .= "---------- CALL HANGUP REASON STATS\n";
	$OUToutput .= "+----------------------+------------+\n";
	$OUToutput .= "| HANGUP REASON        | CALLS      |\n";
	$OUToutput .= "+----------------------+------------+\n";

	$stmt="select count(*),term_reason from vicidial_log where call_date >= '$query_date_BEGIN' and call_date <= '$query_date_END' $group_SQLand group by term_reason;";
	if ($non_latin > 0) {$rslt=mysql_query("SET NAMES 'UTF8'");}
	$rslt=mysql_query($stmt, $link);
	if ($DB) {$OUToutput .= "$stmt\n";}
	$reasons_to_print = mysql_num_rows($rslt);
	$i=0;
	while ($i < $reasons_to_print)
		{
		$row=mysql_fetch_row($rslt);

		$TOTALcalls = ($TOTALcalls + $row[0]);

		$REASONcount =	sprintf("%10s", $row[0]);while(strlen($REASONcount)>10) {$REASONcount = substr("$REASONcount", 0, -1);}
		$reason =	sprintf("%-20s", $row[1]);while(strlen($reason)>20) {$reason = substr("$reason", 0, -1);}
		if (ereg("NONE",$reason))	{$reason = 'NO ΑΠΑΝΤΗΣΗ           ';}
		if (ereg("CALLER",$reason)) {$reason = 'CUSTOMER            ';}

		$OUToutput .= "| $reason | $REASONcount |\n";

		$i++;
		}

	$TOTALcalls =		sprintf("%10s", $TOTALcalls);

	$OUToutput .= "+----------------------+------------+\n";
	$OUToutput .= "| TOTAL:               | $TOTALcalls |\n";
	$OUToutput .= "+----------------------+------------+\n";





	##############################
	#########  CALL STATUS STATS

	$TOTALcalls = 0;

	$OUToutput .= "\n";
	$OUToutput .= "---------- CALL STATUS STATS\n";
	$OUToutput .= "+--------+----------------------+----------------------+------------+----------------------------------+----------+\n";
	$OUToutput .= "|        |                      |                      |            |      CALL TIME                   |ΧΕΙΡΙΣΤΗΣTIME|\n";
	$OUToutput .= "| STATUS | DESCRIPTION          | ΚΑΤΗΓΟΡΙΑ             | CALLS      | TOTAL TIME | AVG TIME |CALLS/HOUR|CALLS/HOUR|\n";
	$OUToutput .= "+--------+----------------------+----------------------+------------+------------+----------+----------+----------+\n";

	$campaignSQL = "$group_SQLand";
	if (eregi("YES",$include_rollover))
		{$campaignSQL = "$both_group_SQLand";}
	## Pull the count of agent seconds for the total tally
	$stmt="SELECT sum(pause_sec + wait_sec + talk_sec + dispo_sec) from vicidial_agent_log where event_time >= '$query_date_BEGIN' and event_time <= '$query_date_END' $campaignSQL and pause_sec<36000 and wait_sec<36000 and talk_sec<36000 and dispo_sec<36000;";
	$rslt=mysql_query($stmt, $link);
	$Ctally_to_print = mysql_num_rows($rslt);
	if ($Ctally_to_print > 0) 
		{
		$rowx=mysql_fetch_row($rslt);
		$AGENTsec = "$rowx[0]";
		}
	if ($DB) {$OUToutput .= "$AGENTsec|$Ctally_to_print|$stmt\n";}


	## get counts and time totals for all statuses in this campaign
	$rollover_exclude_dropSQL='';
	if (eregi("YES",$include_rollover))
		{$rollover_exclude_dropSQL = "and status NOT IN('DROP')";}
	$stmt="select count(*),status,sum(length_in_sec) from vicidial_log where call_date >= '$query_date_BEGIN' and call_date <= '$query_date_END' $rollover_exclude_dropSQL $group_SQLand group by status;";

	if ($non_latin > 0) {$rslt=mysql_query("SET NAMES 'UTF8'");}
	$rslt=mysql_query($stmt, $link);
	if ($DB) {$OUToutput .= "$stmt\n";}
	$statuses_to_print = mysql_num_rows($rslt);
	$i=0;
	while ($i < $statuses_to_print)
		{
		$row=mysql_fetch_row($rslt);
		$STATUScountARY[$i] =	$row[0];
		$RAWstatusARY[$i] =		$row[1];
		$RAWhoursARY[$i] =		$row[2];
		$statusSQL .=			"'$row[1]',";
		$i++;
		}
	if (eregi("YES",$include_rollover))
		{
		if (strlen($statusSQL) < 2)
			{$statusSQL = "''";}
		else
			{
			$statusSQL = eregi_replace(",$",'',$statusSQL);
			}
		$stmt="select distinct status from vicidial_closer_log where call_date >= '$query_date_BEGIN' and call_date <= '$query_date_END' and status NOT IN($statusSQL) $group_drop_SQLand;";
		$rslt=mysql_query($stmt, $link);
		$inS_statuses_to_print = mysql_num_rows($rslt);
		$n=0;
		while ($inS_statuses_to_print > $n) 
			{
			$rowx=mysql_fetch_row($rslt);
			$STATUScountARY[$i] =	0;
			$RAWstatusARY[$i] =		$rowx[0];
			$RAWhoursARY[$i] =		0;
			$i++;
			$n++;
			$statuses_to_print++;
			}
		}


	$i=0;
	while ($i < $statuses_to_print)
		{
		$STATUScount = $STATUScountARY[$i];
		$RAWstatus = $RAWstatusARY[$i];
		$RAWhours = $RAWhoursARY[$i];

		if (eregi("YES",$include_rollover))
			{
			$stmt="select count(*),sum(length_in_sec) from vicidial_closer_log where call_date >= '$query_date_BEGIN' and call_date <= '$query_date_END' and status='$RAWstatus' $group_drop_SQLand;";
			$rslt=mysql_query($stmt, $link);
			$in_statuses_to_print = mysql_num_rows($rslt);
			if ($in_statuses_to_print > 0) 
				{
				$rowx=mysql_fetch_row($rslt);
				$inSTATUScount =	$rowx[0];
				$inRAWhours =		$rowx[1];

				$STATUScount = ($STATUScount + $inSTATUScount);
				$RAWhours = ($RAWhours + $inRAWhours);
				}
			}

		$r=0;
		while ($r < $statcats_to_print)
			{
			if ($statcat_list[$RAWstatus] == "$vsc_id[$r]")
				{
				$vsc_count[$r] = ($vsc_count[$r] + $STATUScount);
				}
			$r++;
			}
		if ($AGENTsec < 1) {$AGENTsec=1;}
		$TOTALcalls =	($TOTALcalls + $STATUScount);
		$TOTALtimeS =	($TOTALtimeS + $RAWhours);
		$STATUSrate =	($STATUScount / ($TOTALsec / 3600) );
			$STATUSrate =	sprintf("%.2f", $STATUSrate);
		$AGENTrate =	($STATUScount / ($AGENTsec / 3600) );
			$AGENTrate =	sprintf("%.2f", $AGENTrate);

		$STATUShours =		sec_convert($RAWhours,'H'); 
		$STATUSavg_sec =	($RAWhours / $STATUScount); 
		$STATUSavg =		sec_convert($STATUSavg_sec,'H'); 

		$STATUScount =	sprintf("%10s", $STATUScount);while(strlen($STATUScount)>10) {$STATUScount = substr("$STATUScount", 0, -1);}
		$status =	sprintf("%-6s", $RAWstatus);while(strlen($status)>6) {$status = substr("$status", 0, -1);}
		$STATUShours =	sprintf("%10s", $STATUShours);while(strlen($STATUShours)>10) {$STATUShours = substr("$STATUShours", 0, -1);}
		$STATUSavg =	sprintf("%8s", $STATUSavg);while(strlen($STATUSavg)>8) {$STATUSavg = substr("$STATUSavg", 0, -1);}
		$STATUSrate =	sprintf("%8s", $STATUSrate);while(strlen($STATUSrate)>8) {$STATUSrate = substr("$STATUSrate", 0, -1);}
		$AGENTrate =	sprintf("%8s", $AGENTrate);while(strlen($AGENTrate)>8) {$AGENTrate = substr("$AGENTrate", 0, -1);}

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

		$OUToutput .= "| $status | $status_name | $statcat | $STATUScount | $STATUShours | $STATUSavg | $STATUSrate | $AGENTrate |\n";

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
		$TOTALrate =	($TOTALcalls / ($TOTALsec / 3600) );
			$TOTALrate =	sprintf("%.2f", $TOTALrate);
		$aTOTALrate =	($TOTALcalls / ($AGENTsec / 3600) );
			$aTOTALrate =	sprintf("%.2f", $aTOTALrate);

		$aTOTALhours =		sec_convert($AGENTsec,'H'); 
		$TOTALhours =		sec_convert($TOTALtimeS,'H'); 
		$TOTALavg_sec =		($TOTALtimeS / $TOTALcalls);
		$TOTALavg =			sec_convert($TOTALavg_sec,'H'); 
		}
	$TOTALcalls =	sprintf("%10s", $TOTALcalls);
	$TOTALhours =	sprintf("%10s", $TOTALhours);while(strlen($TOTALhours)>10) {$TOTALhours = substr("$TOTALhours", 0, -1);}
	$aTOTALhours =	sprintf("%10s", $aTOTALhours);while(strlen($aTOTALhours)>10) {$aTOTALhours = substr("$aTOTALhours", 0, -1);}
	$TOTALavg =	sprintf("%8s", $TOTALavg);while(strlen($TOTALavg)>8) {$TOTALavg = substr("$TOTALavg", 0, -1);}
	$TOTALrate =	sprintf("%8s", $TOTALrate);while(strlen($TOTALrate)>8) {$TOTALrate = substr("$TOTALrate", 0, -1);}
	$aTOTALrate =	sprintf("%8s", $aTOTALrate);while(strlen($aTOTALrate)>8) {$aTOTALrate = substr("$aTOTALrate", 0, -1);}

	$OUToutput .= "+--------+----------------------+----------------------+------------+------------+----------+----------+----------+\n";
	$OUToutput .= "| TOTAL:                                               | $TOTALcalls | $TOTALhours | $TOTALavg | $TOTALrate |          |\n";
#	$OUToutput .= "|   AGENT TIME                                                      | $aTOTALhours |                     | $aTOTALrate |\n";
	$OUToutput .= "+------------------------------------------------------+------------+------------+---------------------+----------+\n";





	##############################
	#########  ID ΛΙΣΤΑΣ BREAKDOWN STATS

	$TOTALcalls = 0;

	$OUToutput .= "\n";
	$OUToutput .= "---------- ID ΛΙΣΤΑΣ STATS\n";
	$OUToutput .= "+------------------------------------------+------------+\n";
	$OUToutput .= "| LIST                                     | CALLS      |\n";
	$OUToutput .= "+------------------------------------------+------------+\n";

	$stmt="select count(*),list_id from vicidial_log where call_date >= '$query_date_BEGIN' and call_date <= '$query_date_END' $group_SQLand group by list_id;";
	if ($non_latin > 0) {$rslt=mysql_query("SET NAMES 'UTF8'");}
	$rslt=mysql_query($stmt, $link);
	if ($DB) {$OUToutput .= "$stmt\n";}
	$listids_to_print = mysql_num_rows($rslt);
	$i=0;
	while ($i < $listids_to_print)
		{
		$row=mysql_fetch_row($rslt);
		$LISTIDcalls[$i] =	$row[0];
		$LISTIDlists[$i] =	$row[1];
		$i++;
		}

	$i=0;
	while ($i < $listids_to_print)
		{
		$stmt="select list_name from vicidial_lists where list_id='$LISTIDlists[$i]';";
		$rslt=mysql_query($stmt, $link);
		if ($DB) {$OUToutput .= "$stmt\n";}
		$list_name_to_print = mysql_num_rows($rslt);
		if ($list_name_to_print > 0)
			{
			$row=mysql_fetch_row($rslt);
			$LISTIDlist_names[$i] =	$row[0];
			}

		$TOTALcalls = ($TOTALcalls + $LISTIDcalls[$i]);

		$LISTIDcount =	sprintf("%10s", $LISTIDcalls[$i]);while(strlen($LISTIDcount)>10) {$LISTIDcount = substr("$LISTIDcount", 0, -1);}
		$LISTIDname =	sprintf("%-40s", "$LISTIDlists[$i] - $LISTIDlist_names[$i]");while(strlen($LISTIDname)>40) {$LISTIDname = substr("$LISTIDname", 0, -1);}

		$OUToutput .= "| $LISTIDname | $LISTIDcount |\n";

		$i++;
		}

	$TOTALcalls =		sprintf("%10s", $TOTALcalls);

	$OUToutput .= "+------------------------------------------+------------+\n";
	$OUToutput .= "| TOTAL:                                   | $TOTALcalls |\n";
	$OUToutput .= "+------------------------------------------+------------+\n";





	if ( ($carrier_logging_active > 0) and ($carrier_stats == 'YES') )
		{
		##############################
		#########  STATUS ΚΑΤΗΓΟΡΙΑ STATS

		$OUToutput .= "\n";
		$OUToutput .= "---------- CARRIER CALL STATUSES\n";
		$OUToutput .= "+----------------------+------------+\n";
		$OUToutput .= "| STATUS               | CALLS      |\n";
		$OUToutput .= "+----------------------+------------+\n";

		## get counts and time totals for all statuses in this campaign
		$stmt="select dialstatus,count(*) from vicidial_carrier_log vcl,vicidial_log vl where vcl.uniqueid=vl.uniqueid and vcl.call_date > \"$query_date_BEGIN\" and vcl.call_date < \"$query_date_END\" and vl.call_date > \"$query_date_BEGIN\" and vl.call_date < \"$query_date_END\" $group_SQLand group by dialstatus order by dialstatus;";
		if ($non_latin > 0) {$rslt=mysql_query("SET NAMES 'UTF8'");}
		$rslt=mysql_query($stmt, $link);
		if ($DB) {$OUToutput .= "$stmt\n";}
		$carrierstatuses_to_print = mysql_num_rows($rslt);
		$i=0;
		while ($i < $carrierstatuses_to_print)
			{
			$row=mysql_fetch_row($rslt);
			$TOTCARcalls = ($TOTCARcalls + $row[1]);
			$CARstatus =	sprintf("%-20s", $row[0]); while(strlen($CARstatus)>20) {$CARstatus = substr("$CARstatus", 0, -1);}
			$CARcount =		sprintf("%10s", $row[1]); while(strlen($CARcount)>10) {$CARcount = substr("$CARcount", 0, -1);}

			$OUToutput .= "| $CARstatus | $CARcount |\n";

			$i++;
			}

		$TOTCARcalls =	sprintf("%10s", $TOTCARcalls); while(strlen($TOTCARcalls)>10) {$TOTCARcalls = substr("$TOTCARcalls", 0, -1);}

		$OUToutput .= "+----------------------+------------+\n";
		$OUToutput .= "| TOTAL                | $TOTCARcalls |\n";
		$OUToutput .= "+----------------------+------------+\n";
		}


	##############################
	#########  STATUS ΚΑΤΗΓΟΡΙΑ STATS

	$OUToutput .= "\n";
	$OUToutput .= "---------- CUSTOM STATUS ΚΑΤΗΓΟΡΙΑ STATS\n";
	$OUToutput .= "+----------------------+------------+--------------------------------+\n";
	$OUToutput .= "| ΚΑΤΗΓΟΡΙΑ             | CALLS      | DESCRIPTION                    |\n";
	$OUToutput .= "+----------------------+------------+--------------------------------+\n";


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

			$OUToutput .= "| $category | $CATcount | $CATname |\n";
			}
		$r++;
		}

	$TOTCATcalls =	sprintf("%10s", $TOTCATcalls); while(strlen($TOTCATcalls)>10) {$TOTCATcalls = substr("$TOTCATcalls", 0, -1);}

	$OUToutput .= "+----------------------+------------+--------------------------------+\n";
	$OUToutput .= "| TOTAL                | $TOTCATcalls |\n";
	$OUToutput .= "+----------------------+------------+\n";



	##############################
	#########  USER STATS

	$TOTagents=0;
	$TOTcalls=0;
	$TOTtime=0;
	$TOTavg=0;

	$OUToutput .= "\n";
	$OUToutput .= "---------- ΧΕΙΡΙΣΤΗΣSTATS\n";
	$OUToutput .= "+--------------------------+------------+------------+--------+\n";
	$OUToutput .= "| ΧΕΙΡΙΣΤΗΣ                   | CALLS      | TIME H:M:S |AVERAGE |\n";
	$OUToutput .= "+--------------------------+------------+------------+--------+\n";

	$stmt="select vicidial_log.user,full_name,count(*),sum(length_in_sec),avg(length_in_sec) from vicidial_log,vicidial_users where call_date >= '$query_date_BEGIN' and call_date <= '$query_date_END' $group_SQLand and vicidial_log.user is not null and length_in_sec is not null and length_in_sec > 0 and vicidial_log.user=vicidial_users.user group by vicidial_log.user;";
	if ($non_latin > 0) {$rslt=mysql_query("SET NAMES 'UTF8'");}
	$rslt=mysql_query($stmt, $link);
	if ($DB) {$OUToutput .= "$stmt\n";}
	$users_to_print = mysql_num_rows($rslt);
	$i=0;
	while ($i < $users_to_print)
		{
		$row=mysql_fetch_row($rslt);

		$RAWuser[$i] =			$row[0];
		$RAWfull_name[$i] =		$row[1];
		$RAWuser_calls[$i] =	$row[2];
		$RAWuser_talk[$i] =		$row[3];
		$RAWuser_average[$i] =	$row[4];

		$TOTcalls = ($TOTcalls + $row[2]);
		$TOTtime = ($TOTtime + $row[3]);

		$i++;
		}

	$i=0;
	while ($i < $users_to_print)
		{
		$user =	sprintf("%-6s", $RAWuser[$i]);while(strlen($user)>6) {$user = substr("$user", 0, -1);}
		if ($non_latin < 1)
			{
			$full_name =	sprintf("%-15s", $RAWfull_name[$i]); while(strlen($full_name)>15) {$full_name = substr("$full_name", 0, -1);}	
			}
		else
			{
			$full_name =	sprintf("%-45s", $RAWfull_name[$i]); while(mb_strlen($full_name,'utf-8')>15) {$full_name = mb_substr("$full_name", 0, -1,'utf-8');}	
			}
		if (eregi("YES",$include_rollover))
			{
			$length_in_secZ=0;
			$queue_secondsZ=0;
			$agent_alert_delayZ=0;
			$stmt="select length_in_sec,queue_seconds,agent_alert_delay from vicidial_closer_log,vicidial_inbound_groups where call_date >= '$query_date_BEGIN' and call_date <= '$query_date_END' and group_id=campaign_id and user='$RAWuser[$i]' $group_drop_SQLand;";
			$rslt=mysql_query($stmt, $link);
			if ($DB) {echo "$stmt\n";}
			$INallcalls_to_printZ = mysql_num_rows($rslt);
			$y=0;
			while ($y < $INallcalls_to_printZ)
				{
				$row=mysql_fetch_row($rslt);

				$length_in_secZ = $row[0];
				$queue_secondsZ = $row[1];
				$agent_alert_delayZ = $row[2];

				$CIdelay =		round($agent_alert_delayZ / 1000);
				$thiscallsec = (($length_in_secZ - $queue_secondsZ) - $CIdelay);
				if ($thiscallsec < 0)
					{$thiscallsec = 0;}
				$inCIsec =	($inCIsec + $thiscallsec);	

				$y++;
				}

			$inCIcallsRAW =	$y;
			$RAWuser_talk[$i] = ($RAWuser_talk[$i] + $inCIsec);
			$RAWuser_calls[$i] = ($RAWuser_calls[$i] + $inCIcallsRAW);

			$TOTcalls = ($TOTcalls + $inCIcallsRAW);
			$TOTtime = ($TOTtime + $inCIsec);
			}

		$USERcalls =	sprintf("%10s", $RAWuser_calls[$i]);
		$USERtotTALK =	$RAWuser_talk[$i];
		$USERavgTALK =	round($RAWuser_talk[$i] / $RAWuser_calls[$i]);

		$USERtotTALK_MS =	sec_convert($USERtotTALK,'H'); 
		$USERavgTALK_MS =	sec_convert($USERavgTALK,'H'); 

		$USERtotTALK_MS =	sprintf("%9s", $USERtotTALK_MS);
		$USERavgTALK_MS =	sprintf("%6s", $USERavgTALK_MS);

		$OUToutput .= "| $user - $full_name | $USERcalls |  $USERtotTALK_MS | $USERavgTALK_MS |\n";

		$i++;
		}

	$rawTOTtime = $TOTtime;

	if (!$TOTcalls) {$TOTcalls = 1;}
	$TOTavg = ($TOTtime / $TOTcalls);

	$TOTavg_MS =	sec_convert($TOTavg,'H'); 
	$TOTtime_MS =	sec_convert($TOTtime,'H'); 

	$TOTavg =		sprintf("%6s", $TOTavg_MS);
	$TOTtime =		sprintf("%10s", $TOTtime_MS);

	$TOTagents =		sprintf("%10s", $i);
	$TOTcalls =			sprintf("%10s", $TOTcalls);
	$TOTtime =			sprintf("%8s", $TOTtime);
	$TOTavg =			sprintf("%6s", $TOTavg);

	$stmt="select avg(wait_sec) from vicidial_agent_log where event_time >= '$query_date_BEGIN' and event_time <= '$query_date_END' $group_SQLand;";
	$rslt=mysql_query($stmt, $link);
	if ($DB) {$OUToutput .= "$stmt\n";}
	$row=mysql_fetch_row($rslt);

	$AVGwait = $row[0];
	$AVGwait_MS =	sec_convert($AVGwait,'H'); 
	$AVGwait =		sprintf("%6s", $AVGwait_MS);

	$OUToutput .= "+--------------------------+------------+------------+--------+\n";
	$OUToutput .= "| TOTAL Πράκτορες: $TOTagents | $TOTcalls | $TOTtime | $TOTavg |\n";
	$OUToutput .= "+--------------------------+------------+------------+--------+\n";
	$OUToutput .= "| Average Wait time between calls                      $AVGwait |\n";
	$OUToutput .= "+-------------------------------------------------------------+\n";



	if ($costformat > 0)
		{
		$stmt="select campaign_id,phone_number,length_in_sec from vicidial_log,vicidial_users where call_date >= '$query_date_BEGIN' and call_date <= '$query_date_END' $group_SQLand and vicidial_log.user=vicidial_users.user;";
		$rslt=mysql_query($stmt, $link);
		if ($DB) {echo "$stmt\n";}
		$allcalls_to_print = mysql_num_rows($rslt);
		$w=0;
		while ($w < $allcalls_to_print)
			{
			$row=mysql_fetch_row($rslt);

			if ($print_calls > 0)
				{echo "$row[0]\t$row[1]\t$row[2]\n";}
			$tempTALK = ($tempTALK + $row[2]);
			$w++;
			}
		if (eregi("YES",$include_rollover))
			{
			$stmt="select campaign_id,phone_number,length_in_sec,queue_seconds,agent_alert_delay from vicidial_closer_log,vicidial_inbound_groups where call_date >= '$query_date_BEGIN' and call_date <= '$query_date_END' and group_id=campaign_id $group_drop_SQLand;";
			$rslt=mysql_query($stmt, $link);
			if ($DB) {echo "$stmt\n";}
			$INallcalls_to_print = mysql_num_rows($rslt);
			$w=0;
			while ($w < $INallcalls_to_print)
				{
				$row=mysql_fetch_row($rslt);

				if ($print_calls > 0)
				{	echo "$row[0]\t$row[1]\t$row[2]\t$row[3]\t$row[4]\n";}
				$newTALK = ($row[2] - $row[3] - ($row[4] / 1000) );
				if ($newTALK < 0) {$newTALK = 0;}
				$tempTALK = ($tempTALK + $newTALK);
				$w++;
				}
			}
		$tempTALKmin = ($tempTALK  / 60);
		if ($print_calls > 0)
			{echo "$w\t$tempTALK\t$tempTALKmin\n";}

		echo "</PRE>\n<B>";
		$rawTOTtalk_min = round($tempTALK / 60);
		$outbound_cost =	($rawTOTtalk_min * $outbound_rate);
		$outbound_cost =	sprintf("%8.2f", $outbound_cost);

		echo "ΕΞΕΡΧΟΜΕΝΟ $query_date to $end_date, &nbsp; $rawTOTtalk_min minutes at \$$outbound_rate = \$$outbound_cost</B>\n";

		exit;
		}


	echo "$OUToutput";




	if ($bottom_graph == 'YES')
		{
		##############################
		#########  TIME STATS

		echo "\n";
		echo "---------- ΣΤΑΤ.ΧΡΟΝ.\n";

		echo "<FONT SIZE=0>\n";

		$hi_hour_count=0;
		$last_full_record=0;
		$i=0;
		$h=0;
		while ($i <= 96)
			{
			$stmt="select count(*) from vicidial_log where call_date >= '$query_date $h:00:00' and call_date <= '$query_date $h:14:59' $group_SQLand;";
			$rslt=mysql_query($stmt, $link);
			if ($DB) {echo "$stmt\n";}
			$row=mysql_fetch_row($rslt);
			$hour_count[$i] = $row[0];
			if ($hour_count[$i] > $hi_hour_count) {$hi_hour_count = $hour_count[$i];}
			if ($hour_count[$i] > 0) {$last_full_record = $i;}
			$stmt="select count(*) from vicidial_log where call_date >= '$query_date $h:00:00' and call_date <= '$query_date $h:14:59' $group_SQLand and status='DROP';";
			$rslt=mysql_query($stmt, $link);
			if ($DB) {echo "$stmt\n";}
			$row=mysql_fetch_row($rslt);
			$drop_count[$i] = $row[0];
			$i++;


			$stmt="select count(*) from vicidial_log where call_date >= '$query_date $h:15:00' and call_date <= '$query_date $h:29:59' $group_SQLand;";
			$rslt=mysql_query($stmt, $link);
			if ($DB) {echo "$stmt\n";}
			$row=mysql_fetch_row($rslt);
			$hour_count[$i] = $row[0];
			if ($hour_count[$i] > $hi_hour_count) {$hi_hour_count = $hour_count[$i];}
			if ($hour_count[$i] > 0) {$last_full_record = $i;}
			$stmt="select count(*) from vicidial_log where call_date >= '$query_date $h:15:00' and call_date <= '$query_date $h:29:59' $group_SQLand and status='DROP';";
			$rslt=mysql_query($stmt, $link);
			if ($DB) {echo "$stmt\n";}
			$row=mysql_fetch_row($rslt);
			$drop_count[$i] = $row[0];
			$i++;

			$stmt="select count(*) from vicidial_log where call_date >= '$query_date $h:30:00' and call_date <= '$query_date $h:44:59' $group_SQLand;";
			$rslt=mysql_query($stmt, $link);
			if ($DB) {echo "$stmt\n";}
			$row=mysql_fetch_row($rslt);
			$hour_count[$i] = $row[0];
			if ($hour_count[$i] > $hi_hour_count) {$hi_hour_count = $hour_count[$i];}
			if ($hour_count[$i] > 0) {$last_full_record = $i;}
			$stmt="select count(*) from vicidial_log where call_date >= '$query_date $h:30:00' and call_date <= '$query_date $h:44:59' $group_SQLand and status='DROP';";
			$rslt=mysql_query($stmt, $link);
			if ($DB) {echo "$stmt\n";}
			$row=mysql_fetch_row($rslt);
			$drop_count[$i] = $row[0];
			$i++;

			$stmt="select count(*) from vicidial_log where call_date >= '$query_date $h:45:00' and call_date <= '$query_date $h:59:59' $group_SQLand;";
			$rslt=mysql_query($stmt, $link);
			if ($DB) {echo "$stmt\n";}
			$row=mysql_fetch_row($rslt);
			$hour_count[$i] = $row[0];
			if ($hour_count[$i] > $hi_hour_count) {$hi_hour_count = $hour_count[$i];}
			if ($hour_count[$i] > 0) {$last_full_record = $i;}
			$stmt="select count(*) from vicidial_log where call_date >= '$query_date $h:45:00' and call_date <= '$query_date $h:59:59' $group_SQLand and status='DROP';";
			$rslt=mysql_query($stmt, $link);
			if ($DB) {echo "$stmt\n";}
			$row=mysql_fetch_row($rslt);
			$drop_count[$i] = $row[0];
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
		echo "ΓΡΑΦΙΚΗ ΠΑΡΑΣΤΑΣΗ ΜΕ 15ΛΕΠΤΕΣ ΑΥΞΗΣΕΙΣ ΤΩΝ ΣΥΝΟΛΙΚΩΝ ΚΛΗΣΕΩΝ ΠΟΥ ΤΟΠΟΘΕΤΟΥΝΤΑΙ ΑΠΟ ΑΥΤΗΝ ΤΗΝ ΕΚΣΤΡΑΤΕΙΑ\n";

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


		echo "+------+-------------------------------------------------------------------------------------------------------+-------+-------+\n";

		### END bottom graph
		}




	$ENDtime = date("U");
	$RUNtime = ($ENDtime - $STARTtime);
	echo "\nRun Time: $RUNtime seconds\n";
	}



?>
</PRE>
</TD></TR></TABLE>

</BODY></HTML>
