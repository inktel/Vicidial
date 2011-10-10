<?php 
# AST_DIDstats.php
# 
# Copyright (C) 2010  Matt Florell <vicidial@gmail.com>    LICENSE: AGPLv2
#
# CHANGES
#
# 90601-1443 - First build
# 100116-0620 - Bug fixes
# 100214-1421 - Sort menu alphabetically
# 100216-0042 - Added popup date selector
# 100712-1324 - Added system setting slave server option
# 100802-2347 - Added User Group Allowed Reports option validation
# 100914-1326 - Added lookup for user_level 7 users to set to reports only which will remove other admin links
#

require("dbconnect.php");
require("functions.php");

$PHP_AUTH_USER=$_SERVER['PHP_AUTH_USER'];
$PHP_AUTH_PW=$_SERVER['PHP_AUTH_PW'];
$PHP_SELF=$_SERVER['PHP_SELF'];
if (isset($_GET["group"]))					{$group=$_GET["group"];}
	elseif (isset($_POST["group"]))			{$group=$_POST["group"];}
if (isset($_GET["query_date"]))				{$query_date=$_GET["query_date"];}
	elseif (isset($_POST["query_date"]))	{$query_date=$_POST["query_date"];}
if (isset($_GET["end_date"]))				{$end_date=$_GET["end_date"];}
	elseif (isset($_POST["end_date"]))		{$end_date=$_POST["end_date"];}
if (isset($_GET["shift"]))					{$shift=$_GET["shift"];}
	elseif (isset($_POST["shift"]))			{$shift=$_POST["shift"];}
if (isset($_GET["submit"]))					{$submit=$_GET["submit"];}
	elseif (isset($_POST["submit"]))		{$submit=$_POST["submit"];}
if (isset($_GET["SUBMIT"]))					{$SUBMIT=$_GET["SUBMIT"];}
	elseif (isset($_POST["SUBMIT"]))		{$SUBMIT=$_POST["SUBMIT"];}
if (isset($_GET["DB"]))						{$DB=$_GET["DB"];}
	elseif (isset($_POST["DB"]))			{$DB=$_POST["DB"];}

$PHP_AUTH_USER = ereg_replace("[^-_0-9a-zA-Z]","",$PHP_AUTH_USER);
$PHP_AUTH_PW = ereg_replace("[^-_0-9a-zA-Z]","",$PHP_AUTH_PW);

if (strlen($shift)<2) {$shift='ALL';}

$report_name = 'Inbound DID Report';
$db_source = 'M';

#############################################
##### START SYSTEM_SETTINGS LOOKUP #####
$stmt = "SELECT use_non_latin,outbound_autodial_active,slave_db_server,reports_use_slave_db FROM system_settings;";
$rslt=mysql_query($stmt, $link);
if ($DB) {echo "$stmt\n";}
$qm_conf_ct = mysql_num_rows($rslt);
if ($qm_conf_ct > 0)
	{
	$row=mysql_fetch_row($rslt);
	$non_latin =					$row[0];
	$outbound_autodial_active =		$row[1];
	$slave_db_server =				$row[2];
	$reports_use_slave_db =			$row[3];
	}
##### END SETTINGS LOOKUP #####
###########################################

if ( (strlen($slave_db_server)>5) and (preg_match("/$report_name/",$reports_use_slave_db)) )
	{
	mysql_close($link);
	$use_slave_server=1;
	$db_source = 'S';
	require("dbconnect.php");
	echo "<!-- Using slave server $slave_db_server $db_source -->\n";
	}

$stmt="SELECT count(*) from vicidial_users where user='$PHP_AUTH_USER' and pass='$PHP_AUTH_PW' and user_level >= 7 and view_reports='1' and active='Y';";
if ($DB) {echo "|$stmt|\n";}
if ($non_latin > 0) {$rslt=mysql_query("SET NAMES 'UTF8'");}
$rslt=mysql_query($stmt, $link);
$row=mysql_fetch_row($rslt);
$auth=$row[0];

$stmt="SELECT count(*) from vicidial_users where user='$PHP_AUTH_USER' and pass='$PHP_AUTH_PW' and user_level='7' and view_reports='1' and active='Y';";
if ($DB) {echo "|$stmt|\n";}
$rslt=mysql_query($stmt, $link);
$row=mysql_fetch_row($rslt);
$reports_only_user=$row[0];

if( (strlen($PHP_AUTH_USER)<2) or (strlen($PHP_AUTH_PW)<2) or (!$auth))
	{
    Header("WWW-Authenticate: Basic realm=\"VICI-PROJECTS\"");
    Header("HTTP/1.0 401 Unauthorized");
    echo "Invalid Username/Password: |$PHP_AUTH_USER|$PHP_AUTH_PW|\n";
    exit;
	}

$stmt="SELECT user_group from vicidial_users where user='$PHP_AUTH_USER' and pass='$PHP_AUTH_PW' and user_level > 6 and view_reports='1' and active='Y';";
if ($DB) {echo "|$stmt|\n";}
$rslt=mysql_query($stmt, $link);
$row=mysql_fetch_row($rslt);
$LOGuser_group =			$row[0];

$stmt="SELECT allowed_campaigns,allowed_reports from vicidial_user_groups where user_group='$LOGuser_group';";
if ($DB) {echo "|$stmt|\n";}
$rslt=mysql_query($stmt, $link);
$row=mysql_fetch_row($rslt);
$LOGallowed_campaigns = $row[0];
$LOGallowed_reports =	$row[1];

if ( (!preg_match("/$report_name/",$LOGallowed_reports)) and (!preg_match("/ALL REPORTS/",$LOGallowed_reports)) )
	{
    Header("WWW-Authenticate: Basic realm=\"VICI-PROJECTS\"");
    Header("HTTP/1.0 401 Unauthorized");
    echo "You are not allowed to view this report: |$PHP_AUTH_USER|$report_name|\n";
    exit;
	}

$NOW_DATE = date("Y-m-d");
$NOW_TIME = date("Y-m-d H:i:s");
$STARTtime = date("U");
if (!isset($group)) {$group = '';}
if (!isset($query_date)) {$query_date = $NOW_DATE;}
if (!isset($end_date)) {$end_date = $NOW_DATE;}

$stmt="select did_id,did_pattern,did_description from vicidial_inbound_dids order by did_pattern;";
$rslt=mysql_query($stmt, $link);
if ($DB) {echo "$stmt\n";}
$groups_to_print = mysql_num_rows($rslt);
$i=0;
while ($i < $groups_to_print)
	{
	$row=mysql_fetch_row($rslt);
	$groups[$i] =			$row[0];
	$group_patterns[$i] =	$row[1];
	$group_names[$i] =		$row[2];
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

echo "<script language=\"JavaScript\" src=\"calendar_db.js\"></script>\n";
echo "<link rel=\"stylesheet\" href=\"calendar.css\">\n";

echo "<META HTTP-EQUIV=\"Content-Type\" CONTENT=\"text/html; charset=utf-8\">\n";
echo "<TITLE>$report_name</TITLE></HEAD><BODY BGCOLOR=WHITE marginheight=0 marginwidth=0 leftmargin=0 topmargin=0>\n";

$short_header=1;

require("admin_header.php");

if ($DB > 0)
	{
	echo "<BR>\n";
	echo "$group_ct|$group_string|$group_SQL\n";
	echo "<BR>\n";
	echo "$shift|$query_date|$end_date\n";
	echo "<BR>\n";
	}

echo "<TABLE CELLPADDING=4 CELLSPACING=0><TR><TD>";

echo "<FORM ACTION=\"$PHP_SELF\" METHOD=GET name=vicidial_report id=vicidial_report>\n";
echo "<TABLE BORDER=0 CELLPADDING=2 CELLSPACING=2><TR><TD align=center valign=top>\n";
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
// o_cal.a_tpl.weekstart = 1; // Monday week start
</script>
<?php

echo "</TD><TD align=center valign=top>\n";
echo "<SELECT SIZE=5 NAME=group[] multiple>\n";
$o=0;
while ($groups_to_print > $o)
	{
	if (ereg("\|$groups[$o]\|",$group_string)) 
		{echo "<option selected value=\"$groups[$o]\">$group_patterns[$o] - $group_names[$o]</option>\n";}
	else
		{echo "<option value=\"$groups[$o]\">$group_patterns[$o] - $group_names[$o]</option>\n";}
	$o++;
	}
echo "</SELECT>\n";
echo "</TD><TD align=center valign=top>\n";
echo "<SELECT SIZE=1 NAME=shift>\n";
echo "<option selected value=\"$shift\">$shift</option>\n";
echo "<option value=\"\">--</option>\n";
echo "<option value=\"AM\">AM</option>\n";
echo "<option value=\"PM\">PM</option>\n";
echo "<option value=\"ALL\">ALL</option>\n";
echo "<option value=\"DAYTIME\">DAYTIME</option>\n";
echo "<option value=\"10AM-6PM\">10AM-6PM</option>\n";
echo "<option value=\"9AM-1AM\">9AM-1AM</option>\n";
echo "<option value=\"845-1745\">845-1745</option>\n";
echo "<option value=\"1745-100\">1745-100</option>\n";
echo "</SELECT>\n";
echo "</TD><TD align=center valign=top>\n";
echo "<INPUT TYPE=hidden NAME=DB VALUE=\"$DB\">\n";
echo "<INPUT TYPE=submit NAME=SUBMIT VALUE=SUBMIT>\n";
echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; <a href=\"./admin.php?ADD=3311&did_id=$group[0]\">MODIFY</a> | <a href=\"./admin.php?ADD=999999\">REPORTS</a> </FONT>\n";
echo "</TD></TR></TABLE>\n";
echo "</FORM>\n";

echo "<PRE><FONT SIZE=2>";


if (!$group)
	{
	echo "\n\n";
	echo "PLEASE SELECT A DID AND DATE RANGE ABOVE AND CLICK SUBMIT\n";
	}

else
	{
	### FOR SHIFTS IT IS BEST TO STICK TO 15-MINUTE INCREMENTS FOR START TIMES ###

	if ($shift == 'AM') 
		{
	#	$time_BEGIN=$AM_shift_BEGIN;
	#	$time_END=$AM_shift_END;
	#	if (strlen($time_BEGIN) < 6) {$time_BEGIN = "03:45:00";}   
	#	if (strlen($time_END) < 6) {$time_END = "15:15:00";}
		if (strlen($time_BEGIN) < 6) {$time_BEGIN = "00:00:00";}   
		if (strlen($time_END) < 6) {$time_END = "11:59:59";}
	#	if (strlen($time_BEGIN) < 6) {$time_BEGIN = "12:00:00";}   
	#	if (strlen($time_END) < 6) {$time_END = "11:59:59";}
		}
	if ($shift == 'PM') 
		{
	#	$time_BEGIN=$PM_shift_BEGIN;
	#	$time_END=$PM_shift_END;
	#	if (strlen($time_BEGIN) < 6) {$time_BEGIN = "15:15:00";}
	#	if (strlen($time_END) < 6) {$time_END = "23:15:00";}
		if (strlen($time_BEGIN) < 6) {$time_BEGIN = "12:00:00";}
		if (strlen($time_END) < 6) {$time_END = "23:59:59";}
		}
	if ($shift == 'ALL') 
		{
		if (strlen($time_BEGIN) < 6) {$time_BEGIN = "00:00:00";}
		if (strlen($time_END) < 6) {$time_END = "23:59:59";}
		}
	if ($shift == 'DAYTIME') 
		{
		if (strlen($time_BEGIN) < 6) {$time_BEGIN = "08:45:00";}
		if (strlen($time_END) < 6) {$time_END = "00:59:59";}
		}
	if ($shift == '10AM-6PM') 
		{
		if (strlen($time_BEGIN) < 6) {$time_BEGIN = "10:00:00";}
		if (strlen($time_END) < 6) {$time_END = "17:59:59";}
		}
	if ($shift == '9AM-1AM') 
		{
		if (strlen($time_BEGIN) < 6) {$time_BEGIN = "09:00:00";}
		if (strlen($time_END) < 6) {$time_END = "00:59:59";}
		}
	if ($shift == '845-1745') 
		{
		if (strlen($time_BEGIN) < 6) {$time_BEGIN = "08:45:00";}
		if (strlen($time_END) < 6) {$time_END = "17:44:59";}
		}
	if ($shift == '1745-100') 
		{
		if (strlen($time_BEGIN) < 6) {$time_BEGIN = "17:45:00";}
		if (strlen($time_END) < 6) {$time_END = "00:59:59";}
		}

	$query_date_BEGIN = "$query_date $time_BEGIN";   
	$query_date_END = "$end_date $time_END";

	$SQdate_ARY =	explode(' ',$query_date_BEGIN);
	$SQday_ARY =	explode('-',$SQdate_ARY[0]);
	$SQtime_ARY =	explode(':',$SQdate_ARY[1]);
	$EQdate_ARY =	explode(' ',$query_date_END);
	$EQday_ARY =	explode('-',$EQdate_ARY[0]);
	$EQtime_ARY =	explode(':',$EQdate_ARY[1]);

	$SQepochDAY = mktime(0, 0, 0, $SQday_ARY[1], $SQday_ARY[2], $SQday_ARY[0]);
	$SQepoch = mktime($SQtime_ARY[0], $SQtime_ARY[1], $SQtime_ARY[2], $SQday_ARY[1], $SQday_ARY[2], $SQday_ARY[0]);
	$EQepoch = mktime($EQtime_ARY[0], $EQtime_ARY[1], $EQtime_ARY[2], $EQday_ARY[1], $EQday_ARY[2], $EQday_ARY[0]);

	$SQsec = ( ($SQtime_ARY[0] * 3600) + ($SQtime_ARY[1] * 60) + ($SQtime_ARY[2] * 1) );
	$EQsec = ( ($EQtime_ARY[0] * 3600) + ($EQtime_ARY[1] * 60) + ($EQtime_ARY[2] * 1) );

	$DURATIONsec = ($EQepoch - $SQepoch);
	$DURATIONday = intval( ($DURATIONsec / 86400) + 1 );

	if ( ($EQsec < $SQsec) and ($DURATIONday < 1) )
		{
		$EQepoch = ($SQepochDAY + ($EQsec + 86400) );
		$query_date_END = date("Y-m-d H:i:s", $EQepoch);
		$DURATIONday++;
		}

	echo "Inbound DID Report                      $NOW_TIME\n";
	echo "\n";
	echo "Time range $DURATIONday days: $query_date_BEGIN to $query_date_END\n\n";
	#echo "Time range day sec: $SQsec - $EQsec   Day range in epoch: $SQepoch - $EQepoch   Start: $SQepochDAY\n";

	$d=0;
	while ($d < $DURATIONday)
		{
		$dSQepoch = ($SQepoch + ($d * 86400) );
		$dEQepoch = ($SQepochDAY + ($EQsec + ($d * 86400) ) );

		if ($EQsec < $SQsec)
			{
			$dEQepoch = ($dEQepoch + 86400);
			}

		$daySTART[$d] = date("Y-m-d H:i:s", $dSQepoch);
		$dayEND[$d] = date("Y-m-d H:i:s", $dEQepoch);

		$d++;
		}

	##########################################################################
	#########  CALCULATE ALL OF THE 15-MINUTE PERIODS NEEDED FOR ALL DAYS ####

	### BUILD HOUR:MIN DISPLAY ARRAY ###
	$i=0;
	$h=4;
	$j=0;
	$Zhour=1;
	$active_time=0;
	$hour =		($SQtime_ARY[0] - 1);
	$startSEC = ($SQsec - 900);
	$endSEC =	($SQsec - 1);
	if ($SQtime_ARY[1] > 14) 
		{
		$h=1;
		$hour++;
		if ($hour < 10) {$hour = "0$hour";}
		}
	if ($SQtime_ARY[1] > 29) {$h=2;}
	if ($SQtime_ARY[1] > 44) {$h=3;}
	while ($i < 96)
		{
		$startSEC = ($startSEC + 900);
		$endSEC = ($endSEC + 900);
		$time = '      ';
		if ($h >= 4)
			{
			$hour++;
			if ($Zhour == '00') 
				{
				$startSEC=0;
				$endSEC=899;
				}
			$h=0;
			if ($hour < 10) {$hour = "0$hour";}
			$Stime="$hour:00";
			$Etime="$hour:15";
			$time = "+$Stime-$Etime+";
			}
		if ($h == 1)
			{
			$Stime="$hour:15";
			$Etime="$hour:30";
			$time = " $Stime-$Etime ";
			}
		if ($h == 2)
			{
			$Stime="$hour:30";
			$Etime="$hour:45";
			$time = " $Stime-$Etime ";
			}
		if ($h == 3)
			{
			$Zhour=$hour;
			$Zhour++;
			if ($Zhour < 10) {$Zhour = "0$Zhour";}
			if ($Zhour == 24) {$Zhour = "00";}
			$Stime="$hour:45";
			$Etime="$Zhour:00";
			$time = " $Stime-$Etime ";
			if ($Zhour == '00') 
				{$hour = ($Zhour - 1);}
			}

		if ( ( ($startSEC >= $SQsec) and ($endSEC <= $EQsec) and ($EQsec > $SQsec) ) or 
			( ($startSEC >= $SQsec) and ($EQsec < $SQsec) ) or 
			( ($endSEC <= $EQsec) and ($EQsec < $SQsec) ) )
			{
			$HMdisplay[$j] =	$time;
			$HMstart[$j] =		$Stime;
			$HMend[$j] =		$Etime;
			$HMSepoch[$j] =		$startSEC;
			$HMEepoch[$j] =		$endSEC;

			$j++;
			}

		$h++;
		$i++;
		}

	$TOTintervals = $j;


	### GRAB ALL RECORDS WITHIN RANGE FROM THE DATABASE ###
	$stmt="select UNIX_TIMESTAMP(call_date),extension from vicidial_did_log where call_date >= '$query_date_BEGIN' and call_date <= '$query_date_END' and  did_id IN($group_SQL);";
	$rslt=mysql_query($stmt, $link);
	if ($DB) {echo "$stmt\n";}
	$records_to_grab = mysql_num_rows($rslt);
	$i=0;
	$extension[0]='';
	while ($i < $records_to_grab)
		{
		$row=mysql_fetch_row($rslt);
		$dt[$i] =			0;
		$ut[$i] =			($row[0] - $SQepochDAY);
		$extension[$i] =	$row[1];
		while($ut[$i] >= 86400) 
			{
			$ut[$i] = ($ut[$i] - 86400);
			$dt[$i]++;
			}
		if ( ($ut[$i] <= $EQsec) and ($EQsec < $SQsec) )
			{
			$dt[$i] = ($dt[$i] - 1);
			}

		$i++;
		}

	### Find default route
	$default_route='';
	$stmt="select did_route from vicidial_inbound_dids where did_pattern='default';";
	$rslt=mysql_query($stmt, $link);
	if ($DB) {echo "$stmt\n";}
	$Drecords_to_grab = mysql_num_rows($rslt);
	if ($Drecords_to_grab > 0)
		{
		$row=mysql_fetch_row($rslt);
		$default_route =	$row[0];
		}

	###################################################
	### TOTALS DID SUMMARY SECTION ###
	if (strlen($extension[0]) > 0)
		{
		echo "DID Summary:\n";
		echo "+--------------------+--------------------------------+------------+------------+\n";
		echo "| DID                | DESCRIPTION                    | ROUTE      | CALLS      |\n";
		echo "+--------------------+--------------------------------+------------+------------+\n";


		$stats_array = array_group_count($extension, 'desc');
		$stats_array_ct = count($stats_array);

		$d=0;
		while ($d < $stats_array_ct)
			{
			$stat_description =		' *** default *** ';
			$stat_route =			$default_route;

			$stat_record_array = explode(' ',$stats_array[$d]);
			$stat_count = ($stat_record_array[0] + 0);
			$stat_pattern = $stat_record_array[1];

			$stmt="select did_description,did_route from vicidial_inbound_dids where did_pattern='$stat_pattern';";
			$rslt=mysql_query($stmt, $link);
			$details_to_grab = mysql_num_rows($rslt);
			if ($details_to_grab > 0)
				{
				$row=mysql_fetch_row($rslt);
				$stat_description =		$row[0];
				$stat_route =			$row[1];
				}

			$totCALLS =			($totCALLS + $stat_count);
			$stat_pattern =		sprintf("%-18s", $stat_pattern);
			$stat_description =	sprintf("%-30s", $stat_description);
			while (strlen($stat_description) > 30) {$stat_description = eregi_replace(".$",'',$stat_description);}
			$stat_route =		sprintf("%-10s", $stat_route);
			$stat_count =		sprintf("%10s", $stat_count);

			$stat_pattern = "<a href=\"admin.php?ADD=3311&did_pattern=$stat_pattern\">$stat_pattern</a>";

			echo "| $stat_pattern | $stat_description | $stat_route | $stat_count |\n";
			$d++;
			}

			$FtotCALLS =	sprintf("%10s", $totCALLS);

		echo "+--------------------+--------------------------------+------------+------------+\n";
		echo "|                                                           TOTALS | $FtotCALLS |\n";
		echo "+------------------------------------------------------------------+------------+\n";
		}



	### PARSE THROUGH ALL RECORDS AND GENERATE STATS ###
	$MT[0]='0';
	$totCALLS=0;
	$totCALLSmax=0;
	$totCALLSdate=$MT;
	$qrtCALLS=$MT;
	$qrtCALLSavg=$MT;
	$qrtCALLSmax=$MT;
	$j=0;
	while ($j < $TOTintervals)
		{
		$jd__0[$j]=0; $jd_20[$j]=0; $jd_40[$j]=0; $jd_60[$j]=0; $jd_80[$j]=0; $jd100[$j]=0; $jd120[$j]=0; $jd121[$j]=0;
		$Phd__0[$j]=0; $Phd_20[$j]=0; $Phd_40[$j]=0; $Phd_60[$j]=0; $Phd_80[$j]=0; $Phd100[$j]=0; $Phd120[$j]=0; $Phd121[$j]=0;
		$qrtCALLS[$j]=0; $qrtCALLSmax[$j]=0;
		$i=0;
		while ($i < $records_to_grab)
			{
			if ( ($ut[$i] >= $HMSepoch[$j]) and ($ut[$i] <= $HMEepoch[$j]) )
				{
				$totCALLS++;
				$qrtCALLS[$j]++;
				$dtt = $dt[$i];
				$totCALLSdate[$dtt]++;
				if ($totCALLSmax < $ls[$i]) {$totCALLSmax = $ls[$i];}
				if ($qrtCALLSmax[$j] < $ls[$i]) {$qrtCALLSmax[$j] = $ls[$i];}

				if ($qs[$i] == 0) {$hd__0[$j]++;}
				if ( ($qs[$i] > 0) and ($qs[$i] <= 20) ) {$hd_20[$j]++;}
				if ( ($qs[$i] > 20) and ($qs[$i] <= 40) ) {$hd_40[$j]++;}
				if ( ($qs[$i] > 40) and ($qs[$i] <= 60) ) {$hd_60[$j]++;}
				if ( ($qs[$i] > 60) and ($qs[$i] <= 80) ) {$hd_80[$j]++;}
				if ( ($qs[$i] > 80) and ($qs[$i] <= 100) ) {$hd100[$j]++;}
				if ( ($qs[$i] > 100) and ($qs[$i] <= 120) ) {$hd120[$j]++;}
				if ($qs[$i] > 120) {$hd121[$j]++;}
				}
			
			$i++;
			}

		$j++;
		}




	###################################################
	### TOTALS DATE SUMMARY SECTION ###
	echo "\nDate Summary:\n";
	echo "+-------------------------------------------+--------+\n";
	echo "| SHIFT                                     |        |\n";
	echo "| DATE-TIME RANGE                           | CALLS  |\n";
	echo "+-------------------------------------------+--------+\n";

	$d=0;
	while ($d < $DURATIONday)
		{
		if ($totCALLSdate[$d] < 1) {$totCALLSdate[$d]=0;}

		if ($totCALLSsecDATE[$d] > 0)
			{
			$totCALLSavgDATE[$d] = ($totCALLSsecDATE[$d] / $totCALLSdate[$d]);

			$totTIME_M = ($totCALLSsecDATE[$d] / 60);
			$totTIME_M_int = round($totTIME_M, 2);
			$totTIME_M_int = intval("$totTIME_M");
			$totTIME_S = ($totTIME_M - $totTIME_M_int);
			$totTIME_S = ($totTIME_S * 60);
			$totTIME_S = round($totTIME_S, 0);
			if ($totTIME_S < 10) {$totTIME_S = "0$totTIME_S";}
			$totTIME_MS = "$totTIME_M_int:$totTIME_S";
			$totTIME_MS =		sprintf("%8s", $totTIME_MS);
			}
		else 
			{
			$totCALLSavgDATE[$d] = 0;
			$totTIME_MS='        ';
			}

		if ($totCALLSdate[$d] < 1) 
			{$totCALLSdate[$d]='';}
		$totCALLSdate[$d] =	sprintf("%6s", $totCALLSdate[$d]);

		echo "| $daySTART[$d] - $dayEND[$d] | $totCALLSdate[$d] |\n";
		$d++;
		}

	$FtotCALLS =	sprintf("%6s", $totCALLS);

	echo "+-------------------------------------------+--------+\n";
	echo "|                                    TOTALS | $FtotCALLS |\n";
	echo "+-------------------------------------------+--------+\n";


	## FORMAT OUTPUT ##
	$i=0;
	$hi_hour_count=0;
	$hi_hold_count=0;

	while ($i < $TOTintervals)
		{
		if ($qrtCALLS[$i] > 0)
			{$qrtCALLSavg[$i] = ($qrtCALLSsec[$i] / $qrtCALLS[$i]);}

		if ($qrtCALLS[$i] > $hi_hour_count) 
			{$hi_hour_count = $qrtCALLS[$i];}

		$i++;
		}

	if ($hi_hour_count < 1)
		{$hour_multiplier = 0;}
	else
		{$hour_multiplier = (70 / $hi_hour_count);}
	if ($hi_hold_count < 1)
		{$hold_multiplier = 0;}
	else
		{$hold_multiplier = (70 / $hi_hold_count);}




	###################################################################
	#########  HOLD TIME, CALL AND DROP STATS 15-MINUTE INCREMENTS ####

	echo "\n";
	echo "---------- HOLD TIME, CALL AND DROP STATS\n";

	echo "<FONT SIZE=0>";

	echo "<!-- HICOUNT CALLS: $hi_hour_count|$hour_multiplier -->";
	echo "<!-- HICOUNT HOLD:  $hi_hold_count|$hold_multiplier -->\n";
	echo "GRAPH IN 15 MINUTE INCREMENTS OF AVERAGE HOLD TIME FOR CALLS TAKEN INTO THIS IN-GROUP\n";


	$k=1;
	$Mk=0;
	$call_scale = '0';
	while ($k <= 72) 
		{
		if ( ($k < 1) or ($hour_multiplier <= 0) )
			{$scale_num = 70;}
		else
			{
			$TMPscale_num=(73 / $hour_multiplier);
			$TMPscale_num = round($TMPscale_num, 0);
			$scale_num=($k / $hour_multiplier);
			$scale_num = round($scale_num, 0);
			}
		$tmpscl = "$call_scale$TMPscale_num";

		if ( ($Mk >= 4) or (strlen($tmpscl)==73) )
			{
			$Mk=0;
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
	$k=1;
	$Mk=0;
	$hold_scale = '0';
	while ($k <= 72) 
		{
		if ( ($k < 1) or ($hold_multiplier <= 0) )
			{$scale_num = 70;}
		else
			{
			$TMPscale_num=(73 / $hold_multiplier);
			$TMPscale_num = round($TMPscale_num, 0);
			$scale_num=($k / $hold_multiplier);
			$scale_num = round($scale_num, 0);
			}
		$tmpscl = "$hold_scale$TMPscale_num";

		if ( ($Mk >= 4) or (strlen($tmpscl)==73) )
			{
			$Mk=0;
			$LENscale_num = (strlen($scale_num));
			$k = ($k + $LENscale_num);
			$hold_scale .= "$scale_num";
			}
		else
			{
			$hold_scale .= " ";
			$k++;   $Mk++;
			}
		}


	echo "+-------------+-------------------------------------------------------------------------+-------+\n";
	echo "|    TIME     |    CALLS HANDLED                                                        |       |\n";
	echo "| 15 MIN INT  |$call_scale| TOTAL |\n";
	echo "+-------------+-------------------------------------------------------------------------+-------+\n";

	$i=0;
	while ($i < $TOTintervals)
		{
		$char_counter=0;
		### BEGIN HOLD TIME TOTALS GRAPH ###
			$Ghour_count = $qrtCALLS[$i];
		if ($Ghour_count > 0) {$no_lines_yet=0;}

		$Gavg_hold = $qrtQUEUEavg[$i];
		if ($Gavg_hold < 1) 
			{
			if ($i < 0)
				{
				$do_nothing=1;
				}
			else
				{
				$TOT_lines++;
				$qrtQUEUEavg[$i] =	sprintf("%5s", $qrtQUEUEavg[$i]);
				$qrtQUEUEmax[$i] =	sprintf("%5s", $qrtQUEUEmax[$i]);
				echo "|$HMdisplay[$i]|";
			#	$k=0;   while ($k <= 22) {echo " ";   $k++;}
			#	echo "| $qrtQUEUEavg[$i] | $qrtQUEUEmax[$i] |";
				}
			}
		else
			{
			$TOT_lines++;
			$no_lines_yet=0;
			$Xavg_hold = ($Gavg_hold * $hold_multiplier);
			$Yavg_hold = (19 - $Xavg_hold);

			$qrtQUEUEavg[$i] =	sprintf("%5s", $qrtQUEUEavg[$i]);
			$qrtQUEUEmax[$i] =	sprintf("%5s", $qrtQUEUEmax[$i]);

		#	echo "|$HMdisplay[$i]|<SPAN class=\"orange\">";
			echo "|$HMdisplay[$i]|";
		#	$k=0;   while ($k <= $Xavg_hold) {echo "*";   $k++;   $char_counter++;}
		#	if ($char_counter >= 22) {echo "H</SPAN>";   $char_counter++;}
		#	else {echo "*H</SPAN>";   $char_counter++;   $char_counter++;}
		#	$k=0;   while ($k <= $Yavg_hold) {echo " ";   $k++;   $char_counter++;}
		#		while ($char_counter <= 22) {echo " ";   $char_counter++;}
		#	echo "| $qrtQUEUEavg[$i] | $qrtQUEUEmax[$i] |";
			}
		### END HOLD TIME TOTALS GRAPH ###

		$char_counter=0;
		### BEGIN CALLS TOTALS GRAPH ###
		$Ghour_count = $qrtCALLS[$i];
		if ($Ghour_count < 1) 
			{
			if ($i < 0)
				{
				$do_nothing=1;
				}
			else
				{
				if ($qrtCALLS[$i] < 1) {$qrtCALLS[$i]='';}
				$qrtCALLS[$i] =	sprintf("%5s", $qrtCALLS[$i]);
			#	echo "  |";
				$k=0;   while ($k <= 72) {echo " ";   $k++;}
				echo "| $qrtCALLS[$i] |\n";
				}
			}
		else
			{
			$no_lines_yet=0;
			$Xhour_count = ($Ghour_count * $hour_multiplier);
			$Yhour_count = (69 - $Xhour_count);

			$Gdrop_count = $qrtDROPS[$i];
			if ($Gdrop_count < 1) 
				{
				if ($qrtCALLS[$i] < 1) {$qrtCALLS[$i]='';}
				$qrtCALLS[$i] =	sprintf("%5s", $qrtCALLS[$i]);

				echo "<SPAN class=\"green\">";
				$k=0;   while ($k <= $Xhour_count) {echo "*";   $k++;   $char_counter++;}
				if ($char_counter > 71) {echo "C</SPAN>";   $char_counter++;}
				else {echo "*C</SPAN>";   $char_counter++;   $char_counter++;}
				$k=0;   while ($k <= $Yhour_count) {echo " ";   $k++;   $char_counter++;}
					while ($char_counter <= 72) {echo " ";   $char_counter++;}
				echo "| $qrtCALLS[$i] |\n";
				}
			else
				{
				$Xdrop_count = ($Gdrop_count * $hour_multiplier);

			#	if ($Xdrop_count >= $Xhour_count) {$Xdrop_count = ($Xdrop_count - 1);}

				$XXhour_count = ( ($Xhour_count - $Xdrop_count) - 1 );

				if ($qrtCALLS[$i] < 1) {$qrtCALLS[$i]='';}
				$qrtCALLS[$i] =	sprintf("%5s", $qrtCALLS[$i]);
				$qrtDROPS[$i] =	sprintf("%5s", $qrtDROPS[$i]);

				echo "<SPAN class=\"red\">";
				$k=0;   while ($k <= $Xdrop_count) {echo ">";   $k++;   $char_counter++;}
				echo "D</SPAN><SPAN class=\"green\">";   $char_counter++;
				$k=0;   while ($k <= $XXhour_count) {echo "*";   $k++;   $char_counter++;}
				echo "C</SPAN>";   $char_counter++;
				$k=0;   while ($k <= $Yhour_count) {echo " ";   $k++;   $char_counter++;}
				while ($char_counter <= 72) {echo " ";   $char_counter++;}

				echo "| $qrtCALLS[$i] |\n";
				}
			}
		### END CALLS TOTALS GRAPH ###

		$i++;
		}


	if ($totQUEUEsec > 0)
		{$totQUEUEavgRAW = ($totCALLS / $totQUEUEsec);}
	else
		{$totQUEUEavgRAW = 0;}
	$totQUEUEavg =	sprintf("%5s", $totQUEUEavg); 
	while (strlen($totQUEUEavg)>5) {$totQUEUEavg = ereg_replace(".$",'',$totQUEUEavg);}
	$totQUEUEmax =	sprintf("%5s", $totQUEUEmax);
	while (strlen($totQUEUEmax)>5) {$totQUEUEmax = ereg_replace(".$",'',$totQUEUEmax);}
	$totDROPS =	sprintf("%5s", $totDROPS);
	$totCALLS =	sprintf("%5s", $totCALLS);


	echo "+-------------+-------------------------------------------------------------------------+-------+\n";
	echo "| TOTAL       |                                                                         | $totCALLS |\n";
	echo "+-------------+-------------------------------------------------------------------------+-------+\n";


	$ENDtime = date("U");
	$RUNtime = ($ENDtime - $STARTtime);
	echo "\nRun Time: $RUNtime seconds|$db_source\n";
	}



?>
</PRE>
</TD></TR></TABLE>

</BODY></HTML>
