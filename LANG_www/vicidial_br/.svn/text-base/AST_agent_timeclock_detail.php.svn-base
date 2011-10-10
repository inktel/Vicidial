<?php 
# AST_agent_timeclock_detail.php
# 
# Pulls all timeclock records for an agent
#
# Copyright (C) 2010  Matt Florell <vicidial@gmail.com>    LICENSE: AGPLv2
#
# CHANGES
# 90602-2244 - First build
# 100301-1401 - Added popup date selector
#


require("dbconnect.php");
require("functions.php");

$PHP_AUTH_USER=$_SERVER['PHP_AUTH_USER'];
$PHP_AUTH_PW=$_SERVER['PHP_AUTH_PW'];
$PHP_SELF=$_SERVER['PHP_SELF'];
if (isset($_GET["query_date"]))				{$query_date=$_GET["query_date"];}
	elseif (isset($_POST["query_date"]))	{$query_date=$_POST["query_date"];}
if (isset($_GET["end_date"]))				{$end_date=$_GET["end_date"];}
	elseif (isset($_POST["end_date"]))		{$end_date=$_POST["end_date"];}
if (isset($_GET["group"]))					{$group=$_GET["group"];}
	elseif (isset($_POST["group"]))			{$group=$_POST["group"];}
if (isset($_GET["user_group"]))				{$user_group=$_GET["user_group"];}
	elseif (isset($_POST["user_group"]))	{$user_group=$_POST["user_group"];}
if (isset($_GET["shift"]))					{$shift=$_GET["shift"];}
	elseif (isset($_POST["shift"]))			{$shift=$_POST["shift"];}
if (isset($_GET["stage"]))					{$stage=$_GET["stage"];}
	elseif (isset($_POST["stage"]))			{$stage=$_POST["stage"];}
if (isset($_GET["file_download"]))			{$file_download=$_GET["file_download"];}
	elseif (isset($_POST["file_download"]))	{$file_download=$_POST["file_download"];}
if (isset($_GET["DB"]))						{$DB=$_GET["DB"];}
	elseif (isset($_POST["DB"]))			{$DB=$_POST["DB"];}
if (isset($_GET["submit"]))					{$submit=$_GET["submit"];}
	elseif (isset($_POST["submit"]))		{$submit=$_POST["submit"];}
if (isset($_GET["SUBMIT"]))					{$SUBMIT=$_GET["SUBMIT"];}
	elseif (isset($_POST["SUBMIT"]))		{$SUBMIT=$_POST["SUBMIT"];}

if (strlen($shift)<2) {$shift='ALL';}
if (strlen($stage)<2) {$stage='ID';}

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

$MT[0]='';
$NOW_DATE = date("Y-m-d");
$NOW_TIME = date("Y-m-d H:i:s");
$STARTtime = date("U");
if (!isset($group)) {$group = '';}
if (!isset($query_date)) {$query_date = "$NOW_DATE 00:00:00";}
if (!isset($end_date)) {$end_date = "$NOW_DATE 23:59:59";}
$query_dateURL = ereg_replace(' ','+',$query_date);
$end_dateURL = ereg_replace(' ','+',$end_date);

$query_dateARRAY = explode(" ",$query_date);
$query_date_D = $query_dateARRAY[0];
$query_date_T = $query_dateARRAY[1];
$end_dateARRAY = explode(" ",$end_date);
$end_date_D = $end_dateARRAY[0];
$end_date_T = $end_dateARRAY[1];

$stmt="select campaign_id from vicidial_campaigns;";
$rslt=mysql_query($stmt, $link);
if ($DB) {echo "$stmt\n";}
$campaigns_to_print = mysql_num_rows($rslt);
$i=0;
while ($i < $campaigns_to_print)
	{
	$row=mysql_fetch_row($rslt);
	$groups[$i] =$row[0];
	$i++;
	}
$stmt="select user_group from vicidial_user_groups;";
$rslt=mysql_query($stmt, $link);
if ($DB) {echo "$stmt\n";}
$user_groups_to_print = mysql_num_rows($rslt);
$i=0;
while ($i < $user_groups_to_print)
	{
	$row=mysql_fetch_row($rslt);
	$user_groups[$i] =$row[0];
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
if ( (ereg("--ALL--",$group_string) ) or ($group_ct < 1) )
	{$group_SQL = "";}
else
	{
	$group_SQL = eregi_replace(",$",'',$group_SQL);
	$group_SQL = "and campaign_id IN($group_SQL)";
	}

$i=0;
$user_group_string='|';
$user_group_ct = count($user_group);
while($i < $user_group_ct)
	{
	$user_group_string .= "$user_group[$i]|";
	$user_group_SQL .= "'$user_group[$i]',";
	$user_groupQS .= "&user_group[]=$user_group[$i]";
	$i++;
	}
if ( (ereg("--ALL--",$user_group_string) ) or ($user_group_ct < 1) )
	{$user_group_SQL = "";}
else
	{
	$TCuser_group_SQL = $user_group_SQL;
	$user_group_SQL = eregi_replace(",$",'',$user_group_SQL);
	$user_group_SQL = "and vicidial_agent_log.user_group IN($user_group_SQL)";
	$TCuser_group_SQL = eregi_replace(",$",'',$TCuser_group_SQL);
	$TCuser_group_SQL = "and user_group IN($TCuser_group_SQL)";
	}

if ($DB) {echo "$user_group_string|$user_group_ct|$user_groupQS|$i<BR>";}

$stmt="select distinct pause_code,pause_code_name from vicidial_pause_codes;";
$rslt=mysql_query($stmt, $link);
if ($DB) {echo "$stmt\n";}
$statha_to_print = mysql_num_rows($rslt);
$i=0;
while ($i < $statha_to_print)
	{
	$row=mysql_fetch_row($rslt);
	$pause_code[$i] =		"$row[0]";
	$pause_code_name[$i] =	"$row[1]";
	$i++;
	}

$LINKbase = "$PHP_SELF?query_date=$query_dateURL&end_date=$end_dateURL$groupQS$user_groupQS&shift=$shift&DB=$DB";

if ($file_download < 1)
	{
	?>

	<HTML>
	<HEAD>
	<STYLE type="text/css">
	<!--
	   .yellow {color: white; background-color: yellow}
	   .red {color: white; background-color: red}
	   .blue {color: white; background-color: blue}
	   .purple {color: white; background-color: purple}
	-->
	 </STYLE>

	<script language="JavaScript" src="calendar_db.js"></script>
	<link rel="stylesheet" href="calendar.css">

	<?php
	echo "<META HTTP-EQUIV=\"Content-Type\" CONTENT=\"text/html; charset=utf-8\">\n";
	echo "<TITLE>User Time-Clock Detail</TITLE></HEAD><BODY BGCOLOR=white marginheight=0 marginwidth=0 leftmargin=0 topmargin=0>\n";
	echo "<span style=\"position:absolute;left:0px;top:0px;z-index:20;\" id=admin_header>";

	$short_header=1;

	require("admin_header.php");

	echo "</span>\n";
	echo "<span style=\"position:absolute;left:3px;top:3px;z-index:19;\" id=agent_status_stats>\n";
	echo "<PRE><FONT SIZE=2>\n";
	}

if (strlen($user_group[0]) < 1)
	{
	echo "\n";
	echo "PLEASE SELECT A CAMPAIGN OR USER GROUP AND DATE-TIME ABOVE AND CLICK SUBMIT\n";
	echo " NOTE: stats taken from shift specified\n";
	}

else
	{
	if ($shift == 'TEST') 
		{
		$time_BEGIN = "09:45:00";  
		$time_END = "10:00:00";
		}
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
	$query_date_BEGIN = "$query_date";   
	$query_date_END = "$end_date";

	if (strlen($user_group)>0) {$ugSQL="and vicidial_agent_log.user_group='$user_group'";}
	else {$ugSQL='';}

	if ($file_download < 1)
		{
		echo "User Time-Clock Detail                     $NOW_TIME\n";

		echo "Time range: $query_date_BEGIN to $query_date_END\n\n";
		}
	else
		{
		$file_output .= "User Time-Clock Detail                     $NOW_TIME\n";
		$file_output .= "Time range: $query_date_BEGIN to $query_date_END\n\n";
		}



	############################################################################
	##### BEGIN gathering information from the database section
	############################################################################

	### BEGIN gather user IDs and names for matching up later
	$stmt="select full_name,user,user_group from vicidial_users order by user limit 100000;";
	$rslt=mysql_query($stmt, $link);
	if ($DB) {echo "$stmt\n";}
	$users_to_print = mysql_num_rows($rslt);
	$i=0;
	while ($i < $users_to_print)
		{
		$row=mysql_fetch_row($rslt);
		$ULname[$i] =	$row[0];
		$ULuser[$i] =	$row[1];
		$ULgroup[$i] =	$row[2];
		$i++;
		}
	### END gather user IDs and names for matching up later


	### BEGIN gather timeclock time totals per agent
	$stmt="select user,sum(login_sec) from vicidial_timeclock_log where event IN('LOGIN','START') and event_date >= '$query_date_BEGIN' and event_date <= '$query_date_END' $TCuser_group_SQL group by user limit 10000000;";
	$rslt=mysql_query($stmt, $link);
	if ($DB) {echo "$stmt\n";}
	$punches_to_print = mysql_num_rows($rslt);
	$i=0;
	while ($i < $punches_to_print)
		{
		$row=mysql_fetch_row($rslt);
		$TCuser[$i] =	$row[0];
		$TCtime[$i] =	$row[1];
		$uc++;
		$i++;
		}
	### END gather timeclock records per agent

	############################################################################
	##### END gathering information from the database section
	############################################################################




	##### BEGIN print the output to screen or put into file output variable
	if ($file_download < 1)
		{
		echo "AGENT TIME-CLOCK DETAIL:\n";
		echo "+-----------------+----------+----------------------+------------+--------------------\n";
		echo "| <a href=\"$LINKbase&stage=NAME\">USER NAME</a>       | <a href=\"$LINKbase&stage=ID\">ID</a>       | <a href=\"$LINKbase&stage=GROUP\">USER GROUP</a>           | <a href=\"$LINKbase&stage=TCLOCK\">TIME CLOCK</a> | TIME CLOCK PUNCHES\n";
		echo "+-----------------+----------+----------------------+------------+--------------------\n";
		}
	else
		{
		$file_output .= "USER,ID,GROUP,TIME CLOCK,TIME CLOCK PUNCHES\n";
		}
	##### END print the output to screen or put into file output variable





	############################################################################
	##### BEGIN formatting data for output section
	############################################################################

	##### BEGIN loop through each user formatting data for output
	$AUTOLOGOUTflag=0;
	$m=0;
	while ( ($m < $uc) and ($m < 50000) )
		{
		$TCdetail='';
		$rawTCdetail='';
		$n=0;
		$user_name_found=0;
		$RAWuser=$TCuser[$m];
		while ($n < $users_to_print)
			{
			if ($TCuser[$m] == "$ULuser[$n]")
				{
				$user_name_found++;
				$RAWname = $ULname[$n];
				$RAWgroup = $ULgroup[$n];
				$Sname[$m] = $ULname[$n];
				$Sgroup[$m] = $ULgroup[$n];
				}
			$n++;
			}
		if ($user_name_found < 1)
			{
			$RAWname =		"NOT IN SYSTEM";
			$RAWgroup =		"GROUP NOT IN SYSTEM";
			$Sname[$m] =	$RAWname;
			}

		$n=0;
		$punches_found=0;
		while ($n < $punches_to_print)
			{
			if ($RAWuser == "$TCuser[$n]")
				{
				$punches_found++;
				$RAWtimeTCsec =		$TCtime[$n];
				$TOTtimeTC =		($TOTtimeTC + $TCtime[$n]);
				$StimeTC[$m]=		sec_convert($TCtime[$n],'H'); 
				$RAWtimeTC =		$StimeTC[$m];
				$StimeTC[$m] =		sprintf("%10s", $StimeTC[$m]);
				}
			$n++;
			}
		if ($punches_found < 1)
			{
			$RAWtimeTCsec =		"0";
			$StimeTC[$m]=		"0:00"; 
			$RAWtimeTC =		$StimeTC[$m];
			$StimeTC[$m] =		sprintf("%10s", $StimeTC[$m]);
			}

		### Check if the user had an AUTOLOGOUT timeclock event during the time period
		$TCuserAUTOLOGOUT = ' ';
		$stmt="select event_epoch,event_date,login_sec,event,user_group from vicidial_timeclock_log where event_date <= '$query_date_END' and event_date >= '$query_date_BEGIN' and user='$TCuser[$m]' $TCuser_group_SQL order by event_date limit 10000000;";
		$rslt=mysql_query($stmt, $link);
		if ($DB) {echo "$stmt\n";}
		$TC_results = mysql_num_rows($rslt);
		$k=0;
		while ($TC_results > $k)
			{
			$TCentryAUTOLOGOUT = ' ';
			$row=mysql_fetch_row($rslt);
			$event_epoch =	$row[0];
			$event_date =	$row[1];
			$login_sec =	$row[2];
			$event =		$row[3];
			$user_group =	$row[4];
			$date_detail = explode(' ',$event_date);

			if ($event == 'AUTOLOGOUT')
				{
				$TCentryAUTOLOGOUT = '*';
				$TCuserAUTOLOGOUT =	'*';
				$AUTOLOGOUTflag++;
				}
			$TCdetail .= "$date_detail[1]$TCentryAUTOLOGOUT ";
			$rawTCdetail .= "$date_detail[1],";
			$k++;
			}

		if ($TC_results > 0)
			{$rawTCdetail = ereg_replace(",$",'',$rawTCdetail);}

		$Stime[$m] =	sprintf("%10s", $Stime[$m]); 
		$SORTname =	sprintf("%-20s", $Sname[$m]);
		$SORTgroup =	sprintf("%-20s", $Sgroup[$m]);
		$Sgroup[$m] =	sprintf("%-20s", $Sgroup[$m]); 
		$SORTgroup = ereg_replace(" ",'0',$SORTgroup);
		$SORTname = ereg_replace(" ",'0',$SORTname);

		if ($non_latin < 1)
			{
			$Sname[$m]=	sprintf("%-15s", $Sname[$m]); 
			while(strlen($Sname[$m])>15) {$Sname[$m] = substr("$Sname[$m]", 0, -1);}
			$Suser[$m] =		sprintf("%-8s", $TCuser[$m]);
			while(strlen($Suser[$m])>8) {$Suser[$m] = substr("$Suser[$m]", 0, -1);}
			}
		else
			{	
			$Sname[$m]=	sprintf("%-45s", $Sname[$m]); 
			while(mb_strlen($Sname[$m],'utf-8')>15) {$Sname[$m] = mb_substr("$Sname[$m]", 0, -1,'utf-8');}
			$Suser[$m] =	sprintf("%-24s", $TCuser[$m]);
			while(mb_strlen($Suser[$m],'utf-8')>8) {$Suser[$m] = mb_substr("$Suser[$m]", 0, -1,'utf-8');}
			}


		if ($file_download < 1)
			{
			$Toutput = "| $Sname[$m] | <a href=\"./user_stats.php?user=$RAWuser\">$Suser[$m]</a> | $Sgroup[$m] | $StimeTC[$m]$TCuserAUTOLOGOUT| $TCdetail\n";
			}
		else
			{
			$fileToutput = "$RAWname,$RAWuser,$RAWgroup,$RAWtimeTC,$rawTCdetail\n";
			}

		$TOPsorted_output[$m] = $Toutput;
		$TOPsorted_outputFILE[$m] = $fileToutput;

		if ($stage == 'NAME')
			{
			$TOPsort[$m] =	'' . sprintf("%020s", $SORTname) . '-----' . $m . '-----' . sprintf("%020s", $RAWuser);
			$TOPsortTALLY[$m]=$RAWcalls;
			}
		if ($stage == 'ID')
			{
			$TOPsort[$m] =	'' . sprintf("%08s", $RAWuser) . '-----' . $m . '-----' . sprintf("%020s", $RAWuser);
			$TOPsortTALLY[$m]=$RAWcalls;
			}
		if ($stage == 'TCLOCK')
			{
			$TOPsort[$m] =	'' . sprintf("%010s", $RAWtimeTCsec) . '-----' . $m . '-----' . sprintf("%020s", $RAWuser);
			$TOPsortTALLY[$m]=$RAWtimeTCsec;
			}
		if ($stage == 'GROUP')
			{
			$TOPsort[$m] =	'' . sprintf("%020s", $SORTgroup) . '-----' . $m . '-----' . sprintf("%020s", $RAWuser);
			$TOPsortTALLY[$m]=$SORTgroup;
			}
		if (!ereg("NAME|ID|TCLOCK|GROUP",$stage))
			if ($file_download < 1)
				{echo "$Toutput";}
			else
				{$file_output .= "$fileToutput";}

		if ($TOPsortMAX < $TOPsortTALLY[$m]) {$TOPsortMAX = $TOPsortTALLY[$m];}

#		echo "$Suser[$m]|$Sname[$m]|$Swait[$m]|$Stalk[$m]|$Sdispo[$m]|$Spause[$m]|$Scalls[$m]\n";
		$m++;
		}
	##### END loop through each user formatting data for output


	$TOT_AGENTS = sprintf("%4s", $m);
	$k=$m;

	if ($DB) {echo "Done analyzing...   $TOTwait|$TOTtalk|$TOTdispo|$TOTpause|$TOTALtime|$TOTcalls|$uc|<BR>\n";}


	### BEGIN sort through output to display properly ###
	if ( (ereg("NAME|ID|TCLOCK|GROUP",$stage)) and ($k > 0) )
		{
		if (ereg("ID",$stage))
			{sort($TOPsort, SORT_NUMERIC);}
		if (ereg("TCLOCK",$stage))
			{rsort($TOPsort, SORT_NUMERIC);}
		if (ereg("GROUP",$stage))
			{sort($TOPsort, SORT_REGULAR);}
		if (ereg("NAME",$stage))
			{sort($TOPsort, SORT_STRING);}

		$m=0;
		while ($m < $k)
			{
			$sort_split = explode("-----",$TOPsort[$m]);
			$i = $sort_split[1];
			$sort_order[$m] = "$i";
			if ($file_download < 1)
				{echo "$TOPsorted_output[$i]";}
			else
				{$file_output .= "$TOPsorted_outputFILE[$i]";}
			$m++;
			}
		}
	### END sort through output to display properly ###

	############################################################################
	##### END formatting data for output section
	############################################################################




	############################################################################
	##### BEGIN last line totals output section
	############################################################################

	### call function to calculate and print dialable leads
	$TOTtimeTC = sec_convert($TOTtimeTC,'H');

	$TOTtimeTC = sprintf("%11s", $TOTtimeTC);
	###### END LAST LINE TOTALS FORMATTING ##########



	if ($file_download < 1)
		{
		echo "+-----------------+----------+----------------------+------------+--------------------\n";
		echo "|  TOTALS        AGENTS:$TOT_AGENTS |                      |$TOTtimeTC |\n";
		echo "+----------------------------+                      +------------+\n";
		if ($AUTOLOGOUTflag > 0)
			{echo "     * denotes AUTOLOGOUT from timeclock\n";}
		echo "\n\n</PRE>";
		}
	else
		{
		$file_output .= "TOTALS,$TOT_AGENTS,,$TOTtimeTC\n";
		}
	}

	############################################################################
	##### END formatting data for output section
	############################################################################





if ($file_download > 0)
	{
	$FILE_TIME = date("Ymd-His");
	$CSVfilename = "AGENT_TIME$US$FILE_TIME.csv";

	// We'll be outputting a TXT file
	header('Content-type: application/octet-stream');

	// It will be called LIST_101_20090209-121212.txt
	header("Content-Disposition: attachment; filename=\"$CSVfilename\"");
	header('Expires: 0');
	header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
	header('Pragma: public');
	ob_clean();
	flush();

	echo "$file_output";

	exit;
	}


############################################################################
##### BEGIN HTML form section
############################################################################
echo "<FORM ACTION=\"$PHP_SELF\" METHOD=GET name=vicidial_report id=vicidial_report>\n";
echo "<TABLE CELLSPACING=3><TR><TD VALIGN=TOP> Dates:<BR>";
echo "<INPUT TYPE=hidden NAME=DB VALUE=\"$DB\">\n";
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
// o_cal.a_tpl.weekstart = 1; // Monday week start
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
// o_cal.a_tpl.weekstart = 1; // Monday week start
</script>
<?php

echo " &nbsp; <INPUT TYPE=TEXT NAME=end_date_T SIZE=9 MAXLENGTH=8 VALUE=\"$end_date_T\">";

#	echo "</TD><TD VALIGN=TOP> Campaigns:<BR>";
#	echo "<SELECT SIZE=5 NAME=group[] multiple>\n";
#	if  (eregi("--ALL--",$group_string))
#		{echo "<option value=\"--ALL--\" selected>-- ALL CAMPAIGNS --</option>\n";}
#	else
#		{echo "<option value=\"--ALL--\">-- ALL CAMPAIGNS --</option>\n";}
#	$o=0;
#	while ($campaigns_to_print > $o)
#	{
#		if (eregi("$groups[$o]\|",$group_string)) {echo "<option selected value=\"$groups[$o]\">$groups[$o]</option>\n";}
#		  else {echo "<option value=\"$groups[$o]\">$groups[$o]</option>\n";}
#		$o++;
#	}
#	echo "</SELECT>\n";

echo "</TD><TD VALIGN=TOP>User Groups:<BR>";
echo "<SELECT SIZE=5 NAME=user_group[] multiple>\n";

if  (eregi("--ALL--",$user_group_string))
	{echo "<option value=\"--ALL--\" selected>-- ALL USER GROUPS --</option>\n";}
else
	{echo "<option value=\"--ALL--\">-- ALL USER GROUPS --</option>\n";}
$o=0;
while ($user_groups_to_print > $o)
	{
	if  (eregi("\|$user_groups[$o]\|",$user_group_string)) {echo "<option selected value=\"$user_groups[$o]\">$user_groups[$o]</option>\n";}
	  else {echo "<option value=\"$user_groups[$o]\">$user_groups[$o]</option>\n";}
	$o++;
	}
echo "</SELECT>\n";
echo "</TD><TD VALIGN=TOP>Shift:<BR>";
echo "<SELECT SIZE=1 NAME=shift>\n";
echo "<option selected value=\"$shift\">$shift</option>\n";
echo "<option value=\"\">--</option>\n";
echo "<option value=\"AM\">AM</option>\n";
echo "<option value=\"PM\">PM</option>\n";
echo "<option value=\"ALL\">ALL</option>\n";
echo "</SELECT><BR><BR>\n";


?>
<SCRIPT LANGUAGE="JavaScript">

function submit_form()
	{
	document.vicidial_report.end_date.value = document.vicidial_report.end_date_D.value + " " + document.vicidial_report.end_date_T.value;
	document.vicidial_report.query_date.value = document.vicidial_report.query_date_D.value + " " + document.vicidial_report.query_date_T.value;

	document.vicidial_report.submit();
	}

</SCRIPT>

<input type=button value="SUBMIT" name=smt id=smt onClick="submit_form()">
<?php


echo "</TD><TD VALIGN=TOP> &nbsp; &nbsp; &nbsp; &nbsp; ";

echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;\n";
echo " <a href=\"$LINKbase&stage=$stage&file_download=1\">DOWNLOAD</a> | \n";
echo " <a href=\"./admin.php?ADD=999999\">REPORTS</a> </FONT>\n";
echo "</FONT>\n";
echo "</TD></TR></TABLE>";

echo "</FORM>\n\n";
############################################################################
##### END HTML form section
############################################################################


$ENDtime = date("U");
$RUNtime = ($ENDtime - $STARTtime);
echo "<font size=1 color=white>$RUNtime</font>\n";

?>

</BODY></HTML>
