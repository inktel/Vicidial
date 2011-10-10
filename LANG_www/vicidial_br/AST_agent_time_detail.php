<?php 
# AST_agent_time_detail.php
# 
# Pulls time stats per agent selectable by campaign or user group
# should be most accurate agent stats of all of the reports
#
# Copyright (C) 2010  Matt Florell <vicidial@gmail.com>    LICENSE: AGPLv2
#
# CHANGES
# 90522-0723 - First build
# 90908-1103 - Added DEAD time stats
# 100203-1147 - Added CUSTOMER time statistics
# 100214-1421 - Sort menu alphabetically
# 100216-0042 - Added popup date selector
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
if (strlen($stage)<2) {$stage='NAME';}

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
if (!isset($query_date)) {$query_date = $NOW_DATE;}
if (!isset($end_date)) {$end_date = $NOW_DATE;}

$stmt="select campaign_id from vicidial_campaigns order by campaign_id;";
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
$stmt="select user_group from vicidial_user_groups order by user_group;";
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

$LINKbase = "$PHP_SELF?query_date=$query_date&end_date=$end_date$groupQS$user_groupQS&shift=$shift&DB=$DB";

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

	<?php

	echo "<script language=\"JavaScript\" src=\"calendar_db.js\"></script>\n";
	echo "<link rel=\"stylesheet\" href=\"calendar.css\">\n";

	echo "<META HTTP-EQUIV=\"Content-Type\" CONTENT=\"text/html; charset=utf-8\">\n";
	echo "<TITLE>Agent Time Detail</TITLE></HEAD><BODY BGCOLOR=white marginheight=0 marginwidth=0 leftmargin=0 topmargin=0>\n";
	echo "<span style=\"position:absolute;left:0px;top:0px;z-index:20;\" id=admin_header>";

	$short_header=1;

	require("admin_header.php");

	echo "</span>\n";
	echo "<span style=\"position:absolute;left:3px;top:3px;z-index:19;\" id=agent_status_stats>\n";
	echo "<PRE><FONT SIZE=2>\n";
	}

if ( (strlen($group[0]) < 1) or (strlen($user_group[0]) < 1) )
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
	$query_date_BEGIN = "$query_date $time_BEGIN";   
	$query_date_END = "$end_date $time_END";

	if (strlen($user_group)>0) {$ugSQL="and vicidial_agent_log.user_group='$user_group'";}
	else {$ugSQL='';}

	if ($file_download < 1)
		{
		echo "Agent Time Detail                     $NOW_TIME\n";

		echo "Time range: $query_date_BEGIN to $query_date_END\n\n";
		}
	else
		{
		$file_output .= "Agent Time Detail                     $NOW_TIME\n";
		$file_output .= "Time range: $query_date_BEGIN to $query_date_END\n\n";
		}



	############################################################################
	##### BEGIN gathering information from the database section
	############################################################################

	### BEGIN gather user IDs and names for matching up later
	$stmt="select full_name,user from vicidial_users order by user limit 100000;";
	$rslt=mysql_query($stmt, $link);
	if ($DB) {echo "$stmt\n";}
	$users_to_print = mysql_num_rows($rslt);
	$i=0;
	while ($i < $users_to_print)
		{
		$row=mysql_fetch_row($rslt);
		$ULname[$i] =	$row[0];
		$ULuser[$i] =	$row[1];
		$i++;
		}
	### END gather user IDs and names for matching up later


	### BEGIN gather timeclock records per agent
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
		$i++;
		}
	### END gather timeclock records per agent


	### BEGIN gather pause code information by user IDs
	$sub_statuses='-';
	$sub_statusesTXT='';
	$sub_statusesHEAD='';
	$sub_statusesHTML='';
	$sub_statusesFILE='';
	$sub_statusesARY=$MT;
	$sub_status_count=0;
	$PCusers='-';
	$PCusersARY=$MT;
	$PCuser_namesARY=$MT;
	$user_count=0;
	$stmt="select user,sum(pause_sec),sub_status from vicidial_agent_log where event_time <= '$query_date_END' and event_time >= '$query_date_BEGIN' and pause_sec > 0 and pause_sec < 30000 $group_SQL $user_group_SQL group by user,sub_status order by user,sub_status desc limit 10000000;";
	$rslt=mysql_query($stmt, $link);
	if ($DB) {echo "$stmt\n";}
	$subs_to_print = mysql_num_rows($rslt);
	$i=0;
	while ($i < $subs_to_print)
		{
		$row=mysql_fetch_row($rslt);
		$PCuser[$i] =		$row[0];
		$PCpause_sec[$i] =	$row[1];
		$sub_status[$i] =	$row[2];

		if (!eregi("-$sub_status[$i]-", $sub_statuses))
			{
			$sub_statusesTXT = sprintf("%10s", $sub_status[$i]);
			$sub_statusesHEAD .= "------------+";
			$sub_statusesHTML .= " $sub_statusesTXT |";
			$sub_statusesFILE .= ",$sub_status[$i]";
			$sub_statuses .= "$sub_status[$i]-";
			$sub_statusesARY[$sub_status_count] = $sub_status[$i];
			$sub_status_count++;
			}
		if (!eregi("-$PCuser[$i]-", $PCusers))
			{
			$PCusers .= "$PCuser[$i]-";
			$PCusersARY[$user_count] = $PCuser[$i];
			$user_count++;
			}

		$i++;
		}
	### END gather pause code information by user IDs


	##### BEGIN Gather all agent time records and parse through them in PHP to save on DB load
	$stmt="select user,wait_sec,talk_sec,dispo_sec,pause_sec,lead_id,status,dead_sec from vicidial_agent_log where event_time <= '$query_date_END' and event_time >= '$query_date_BEGIN' $group_SQL $user_group_SQL limit 10000000;";
	$rslt=mysql_query($stmt, $link);
	if ($DB) {echo "$stmt\n";}
	$rows_to_print = mysql_num_rows($rslt);
	$i=0;
	$j=0;
	$k=0;
	$uc=0;
	while ($i < $rows_to_print)
		{
		$row=mysql_fetch_row($rslt);
		$user =			$row[0];
		$wait =			$row[1];
		$talk =			$row[2];
		$dispo =		$row[3];
		$pause =		$row[4];
		$lead =			$row[5];
		$status =		$row[6];
		$dead =			$row[7];
		if ($wait > 30000) {$wait=0;}
		if ($talk > 30000) {$talk=0;}
		if ($dispo > 30000) {$dispo=0;}
		if ($pause > 30000) {$pause=0;}
		if ($dead > 30000) {$dead=0;}
		$customer =		($talk - $dead);
		if ($customer < 1)
			{$customer=0;}
		$TOTwait =	($TOTwait + $wait);
		$TOTtalk =	($TOTtalk + $talk);
		$TOTdispo =	($TOTdispo + $dispo);
		$TOTpause =	($TOTpause + $pause);
		$TOTdead =	($TOTdead + $dead);
		$TOTcustomer =	($TOTcustomer + $customer);
		$TOTALtime = ($TOTALtime + $pause + $dispo + $talk + $wait);
		if ( ($lead > 0) and ((!eregi("NULL",$status)) and (strlen($status) > 0)) ) {$TOTcalls++;}
		
		$user_found=0;
		if ($uc < 1) 
			{
			$Suser[$uc] = $user;
			$uc++;
			}
		$m=0;
		while ( ($m < $uc) and ($m < 50000) )
			{
			if ($user == "$Suser[$m]")
				{
				$user_found++;

				$Swait[$m] =	($Swait[$m] + $wait);
				$Stalk[$m] =	($Stalk[$m] + $talk);
				$Sdispo[$m] =	($Sdispo[$m] + $dispo);
				$Spause[$m] =	($Spause[$m] + $pause);
				$Sdead[$m] =	($Sdead[$m] + $dead);
				$Scustomer[$m] =	($Scustomer[$m] + $customer);
				if ( ($lead > 0) and ((!eregi("NULL",$status)) and (strlen($status) > 0)) ) {$Scalls[$m]++;}
				}
			$m++;
			}
		if ($user_found < 1)
			{
			$Scalls[$uc] =	0;
			$Suser[$uc] =	$user;
			$Swait[$uc] =	$wait;
			$Stalk[$uc] =	$talk;
			$Sdispo[$uc] =	$dispo;
			$Spause[$uc] =	$pause;
			$Sdead[$uc] =	$dead;
			$Scustomer[$uc] =	$customer;
			if ($lead > 0) {$Scalls[$uc]++;}
			$uc++;
			}

		$i++;
		}
	if ($DB) {echo "Done gathering $i records, analyzing...<BR>\n";}
	##### END Gather all agent time records and parse through them in PHP to save on DB load

	############################################################################
	##### END gathering information from the database section
	############################################################################




	##### BEGIN print the output to screen or put into file output variable
	if ($file_download < 1)
		{
		echo "AGENT TIME BREAKDOWN:\n";
		echo "+-----------------+----------+----------+------------+------------+------------+------------+------------+------------+------------+------------+   +$sub_statusesHEAD\n";
		echo "| <a href=\"$LINKbase&stage=NAME\">USER NAME</a>       | <a href=\"$LINKbase&stage=ID\">ID</a>       | <a href=\"$LINKbase&stage=LEADS\">CALLS</a>    | <a href=\"$LINKbase&stage=TCLOCK\">TIME CLOCK</a> | <a href=\"$LINKbase&stage=TIME\">AGENT TIME</a> | WAIT       | TALK       | DISPO      | PAUSE      | DEAD       | CUSTOMER   |   |$sub_statusesHTML\n";
		echo "+-----------------+----------+----------+------------+------------+------------+------------+------------+------------+------------+------------+   +$sub_statusesHEAD\n";
		}
	else
		{
		$file_output .= "USER,ID,CALLS,TIME CLOCK,AGENT TIME,WAIT,TALK,DISPO,PAUSE,DEAD,CUSTOMER$sub_statusesFILE\n";
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
		$SstatusesHTML='';
		$SstatusesFILE='';
		$Stime[$m] = ($Swait[$m] + $Stalk[$m] + $Sdispo[$m] + $Spause[$m]);
		$RAWuser = $Suser[$m];
		$RAWcalls = $Scalls[$m];
		$RAWtimeSEC = $Stime[$m];

		$Swait[$m]=		sec_convert($Swait[$m],'H'); 
		$Stalk[$m]=		sec_convert($Stalk[$m],'H'); 
		$Sdispo[$m]=	sec_convert($Sdispo[$m],'H'); 
		$Spause[$m]=	sec_convert($Spause[$m],'H'); 
		$Sdead[$m]=	sec_convert($Sdead[$m],'H'); 
		$Scustomer[$m]=	sec_convert($Scustomer[$m],'H'); 
		$Stime[$m]=		sec_convert($Stime[$m],'H'); 

		$RAWtime = $Stime[$m];
		$RAWwait = $Swait[$m];
		$RAWtalk = $Stalk[$m];
		$RAWdispo = $Sdispo[$m];
		$RAWpause = $Spause[$m];
		$RAWdead = $Sdead[$m];
		$RAWcustomer = $Scustomer[$m];

		$n=0;
		$user_name_found=0;
		while ($n < $users_to_print)
			{
			if ($Suser[$m] == "$ULuser[$n]")
				{
				$user_name_found++;
				$RAWname = $ULname[$n];
				$Sname[$m] = $ULname[$n];
				}
			$n++;
			}
		if ($user_name_found < 1)
			{
			$RAWname =		"NOT IN SYSTEM";
			$Sname[$m] =	$RAWname;
			}

		$n=0;
		$punches_found=0;
		while ($n < $punches_to_print)
			{
			if ($Suser[$m] == "$TCuser[$n]")
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
		$stmt="select count(*) from vicidial_timeclock_log where event='AUTOLOGOUT' and user='$Suser[$m]' and event_date >= '$query_date_BEGIN' and event_date <= '$query_date_END';";
		$rslt=mysql_query($stmt, $link);
		if ($DB) {echo "$stmt\n";}
		$autologout_results = mysql_num_rows($rslt);
		if ($autologout_results > 0)
			{
			$row=mysql_fetch_row($rslt);
			if ($row[0] > 0)
				{
				$TCuserAUTOLOGOUT =	'*';
				$AUTOLOGOUTflag++;
				}
			}

		### BEGIN loop through each status ###
		$n=0;
		while ($n < $sub_status_count)
			{
			$Sstatus=$sub_statusesARY[$n];
			$SstatusTXT='';
			### BEGIN loop through each stat line ###
			$i=0; $status_found=0;
			while ( ($i < $subs_to_print) and ($status_found < 1) )
				{
				if ( ($Suser[$m]=="$PCuser[$i]") and ($Sstatus=="$sub_status[$i]") )
					{
					$USERcodePAUSE_MS =		sec_convert($PCpause_sec[$i],'H'); 
					$pfUSERcodePAUSE_MS =	sprintf("%10s", $USERcodePAUSE_MS);

					$SstatusTXT = sprintf("%10s", $pfUSERcodePAUSE_MS);
					$SstatusesHTML .= " $SstatusTXT |";
					$SstatusesFILE .= ",$pfUSERcodePAUSE_MS";
					$status_found++;
					}
				$i++;
				}
			if ($status_found < 1)
				{
				$SstatusesHTML .= "       0:00 |";
				}
			### END loop through each stat line ###
			$n++;
			}
		### END loop through each status ###

		$Swait[$m]=		sprintf("%10s", $Swait[$m]); 
		$Stalk[$m]=		sprintf("%10s", $Stalk[$m]); 
		$Sdispo[$m]=	sprintf("%10s", $Sdispo[$m]); 
		$Spause[$m]=	sprintf("%10s", $Spause[$m]); 
		$Sdead[$m]=		sprintf("%10s", $Sdead[$m]); 
		$Scustomer[$m]=		sprintf("%10s", $Scustomer[$m]);
		$Scalls[$m]=	sprintf("%8s", $Scalls[$m]); 
		$Stime[$m]=		sprintf("%10s", $Stime[$m]); 

		if ($non_latin < 1)
			{
			$Sname[$m]=	sprintf("%-15s", $Sname[$m]); 
			while(strlen($Sname[$m])>15) {$Sname[$m] = substr("$Sname[$m]", 0, -1);}
			$Suser[$m] =		sprintf("%-8s", $Suser[$m]);
			while(strlen($Suser[$m])>8) {$Suser[$m] = substr("$Suser[$m]", 0, -1);}
			}
		else
			{	
			$Sname[$m]=	sprintf("%-45s", $Sname[$m]); 
			while(mb_strlen($Sname[$m],'utf-8')>15) {$Sname[$m] = mb_substr("$Sname[$m]", 0, -1,'utf-8');}
			$Suser[$m] =	sprintf("%-24s", $Suser[$m]);
			while(mb_strlen($Suser[$m],'utf-8')>8) {$Suser[$m] = mb_substr("$Suser[$m]", 0, -1,'utf-8');}
			}


		if ($file_download < 1)
			{
			$Toutput = "| $Sname[$m] | <a href=\"./user_stats.php?user=$RAWuser\">$Suser[$m]</a> | $Scalls[$m] | $StimeTC[$m]$TCuserAUTOLOGOUT| $Stime[$m] | $Swait[$m] | $Stalk[$m] | $Sdispo[$m] | $Spause[$m] | $Sdead[$m] | $Scustomer[$m] |   |$SstatusesHTML\n";
			}
		else
			{
			$fileToutput = "$RAWname,$RAWuser,$RAWcalls,$RAWtimeTC,$RAWtime,$RAWwait,$RAWtalk,$RAWdispo,$RAWpause,$RAWdead,$RAWcustomer$SstatusesFILE\n";
			}

		$TOPsorted_output[$m] = $Toutput;
		$TOPsorted_outputFILE[$m] = $fileToutput;

		if ($stage == 'NAME')
			{
			$TOPsort[$m] =	'' . sprintf("%020s", $RAWname) . '-----' . $m . '-----' . sprintf("%020s", $RAWuser);
			$TOPsortTALLY[$m]=$RAWcalls;
			}
		if ($stage == 'ID')
			{
			$TOPsort[$m] =	'' . sprintf("%08s", $RAWuser) . '-----' . $m . '-----' . sprintf("%020s", $RAWuser);
			$TOPsortTALLY[$m]=$RAWcalls;
			}
		if ($stage == 'LEADS')
			{
			$TOPsort[$m] =	'' . sprintf("%08s", $RAWcalls) . '-----' . $m . '-----' . sprintf("%020s", $RAWuser);
			$TOPsortTALLY[$m]=$RAWcalls;
			}
		if ($stage == 'TIME')
			{
			$TOPsort[$m] =	'' . sprintf("%010s", $RAWtimeSEC) . '-----' . $m . '-----' . sprintf("%020s", $RAWuser);
			$TOPsortTALLY[$m]=$RAWtimeSEC;
			}
		if ($stage == 'TCLOCK')
			{
			$TOPsort[$m] =	'' . sprintf("%010s", $RAWtimeTCsec) . '-----' . $m . '-----' . sprintf("%020s", $RAWuser);
			$TOPsortTALLY[$m]=$RAWtimeTCsec;
			}
		if (!ereg("NAME|ID|TIME|LEADS|TCLOCK",$stage))
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

	if ($DB) {echo "Done analyzing...   $TOTwait|$TOTtalk|$TOTdispo|$TOTpause|$TOTdead|$TOTcustomer|$TOTALtime|$TOTcalls|$uc|<BR>\n";}


	### BEGIN sort through output to display properly ###
	if (ereg("NAME|ID|TIME|LEADS|TCLOCK",$stage))
		{
		if (ereg("ID",$stage))
			{sort($TOPsort, SORT_NUMERIC);}
		if (ereg("TIME|LEADS|TCLOCK",$stage))
			{rsort($TOPsort, SORT_NUMERIC);}
		if (ereg("NAME",$stage))
			{rsort($TOPsort, SORT_STRING);}

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
	$SUMstatusesHTML='';
	$SUMstatusesFILE='';
	$TOTtotPAUSE=0;
	$n=0;
	while ($n < $sub_status_count)
		{
		$Scalls=0;
		$Sstatus=$sub_statusesARY[$n];
		$SUMstatusTXT='';
		### BEGIN loop through each stat line ###
		$i=0; $status_found=0;
		while ($i < $subs_to_print)
			{
			if ($Sstatus=="$sub_status[$i]")
				{
				$Scalls =		($Scalls + $PCpause_sec[$i]);
				$status_found++;
				}
			$i++;
			}
		### END loop through each stat line ###
		if ($status_found < 1)
			{
			$SUMstatusesHTML .= "          0 |";
			}
		else
			{
			$TOTtotPAUSE = ($TOTtotPAUSE + $Scalls);

			$USERsumstatPAUSE_MS =		sec_convert($Scalls,'H'); 
			$pfUSERsumstatPAUSE_MS =	sprintf("%11s", $USERsumstatPAUSE_MS);

			$SUMstatusTXT = sprintf("%10s", $pfUSERsumstatPAUSE_MS);
			$SUMstatusesHTML .= "$SUMstatusTXT |";
			$SUMstatusesFILE .= ",$pfUSERsumstatPAUSE_MS";
			}
		$n++;
		}
	### END loop through each status ###

	### call function to calculate and print dialable leads
	$TOTwait = sec_convert($TOTwait,'H');
	$TOTtalk = sec_convert($TOTtalk,'H');
	$TOTdispo = sec_convert($TOTdispo,'H');
	$TOTpause = sec_convert($TOTpause,'H');
	$TOTdead = sec_convert($TOTdead,'H');
	$TOTcustomer = sec_convert($TOTcustomer,'H');
	$TOTALtime = sec_convert($TOTALtime,'H');
	$TOTtimeTC = sec_convert($TOTtimeTC,'H');

	$TOTcalls = sprintf("%8s", $TOTcalls);
	$TOTwait =	sprintf("%11s", $TOTwait);
	$TOTtalk =	sprintf("%11s", $TOTtalk);
	$TOTdispo =	sprintf("%11s", $TOTdispo);
	$TOTpause =	sprintf("%11s", $TOTpause);
	$TOTdead =	sprintf("%11s", $TOTdead);
	$TOTcustomer =	sprintf("%11s", $TOTcustomer);
	$TOTALtime = sprintf("%11s", $TOTALtime);
	$TOTtimeTC = sprintf("%11s", $TOTtimeTC);
	###### END LAST LINE TOTALS FORMATTING ##########



	if ($file_download < 1)
		{
		echo "+-----------------+----------+----------+------------+------------+------------+------------+------------+------------+------------+------------+   +$sub_statusesHEAD\n";
		echo "|  TOTALS        AGENTS:$TOT_AGENTS | $TOTcalls |$TOTtimeTC |$TOTALtime |$TOTwait |$TOTtalk |$TOTdispo |$TOTpause |$TOTdead |$TOTcustomer |   |$SUMstatusesHTML\n";
		echo "+-----------------+----------+----------+------------+------------+------------+------------+------------+------------+------------+------------+   +$sub_statusesHEAD\n";
		if ($AUTOLOGOUTflag > 0)
			{echo "     * denotes AUTOLOGOUT from timeclock\n";}
		echo "\n\n</PRE>";
		}
	else
		{
		$file_output .= "TOTALS,$TOT_AGENTS,$TOTcalls,$TOTtimeTC,$TOTALtime,$TOTwait,$TOTtalk,$TOTdispo,$TOTpause,$TOTdead,$TOTcustomer$SUMstatusesFILE\n";
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

echo "</TD><TD VALIGN=TOP> Campaigns:<BR>";
echo "<SELECT SIZE=5 NAME=group[] multiple>\n";
if  (eregi("--ALL--",$group_string))
	{echo "<option value=\"--ALL--\" selected>-- ALL CAMPAIGNS --</option>\n";}
else
	{echo "<option value=\"--ALL--\">-- ALL CAMPAIGNS --</option>\n";}
$o=0;
while ($campaigns_to_print > $o)
{
	if (eregi("$groups[$o]\|",$group_string)) {echo "<option selected value=\"$groups[$o]\">$groups[$o]</option>\n";}
	  else {echo "<option value=\"$groups[$o]\">$groups[$o]</option>\n";}
	$o++;
}
echo "</SELECT>\n";
echo "</TD><TD VALIGN=TOP>User Groups:<BR>";
echo "<SELECT SIZE=5 NAME=user_group[] multiple>\n";

if  (eregi("--ALL--",$user_group_string))
	{echo "<option value=\"--ALL--\" selected>-- ALL USER GROUPS --</option>\n";}
else
	{echo "<option value=\"--ALL--\">-- ALL USER GROUPS --</option>\n";}
$o=0;
while ($user_groups_to_print > $o)
	{
	if  (eregi("$user_groups[$o]\|",$user_group_string)) {echo "<option selected value=\"$user_groups[$o]\">$user_groups[$o]</option>\n";}
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
echo "<INPUT TYPE=SUBMIT NAME=SUBMIT VALUE=SUBMIT>\n";
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


##### BEGIN horizontal yellow transparent bar graph overlay on top of agent stats
echo "</span>\n";
echo "<span style=\"position:absolute;left:3px;top:3px;z-index:18;\"  id=agent_status_bars>\n";
echo "<PRE><FONT SIZE=2>\n\n\n\n\n\n\n\n";

if ($stage == 'NAME') {$k=0;}
$m=0;
while ($m < $k)
	{
	$sort_split = explode("-----",$TOPsort[$m]);
	$i = $sort_split[1];
	$sort_order[$m] = "$i";

	if ( ($TOPsortTALLY[$i] < 1) or ($TOPsortMAX < 1) )
		{echo "                              \n";}
	else
		{
		echo "                              <SPAN class=\"yellow\">";
		$TOPsortPLOT = ( ($TOPsortTALLY[$i] / $TOPsortMAX) * 110 );
		$h=0;
		while ($h <= $TOPsortPLOT)
			{
			echo " ";
			$h++;
			}
		echo "</SPAN>\n";
		}
	$m++;
	}

echo "</span>\n";
##### END horizontal yellow transparent bar graph overlay on top of agent stats

?>

</BODY></HTML>
