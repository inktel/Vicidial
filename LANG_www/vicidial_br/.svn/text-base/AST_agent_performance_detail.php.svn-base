<?php 
# AST_agent_performance_detail.php
# 
# Copyright (C) 2010  Matt Florell <vicidial@gmail.com>    LICENSE: AGPLv2
#
# CHANGES
#
# 71119-2359 - First build
# 71121-0144 - Replace existing AST_agent_performance_detail.php script with this one
#            - Fixed zero division bug
# 71218-1155 - added end_date for multi-day reports
# 80428-0144 - UTF8 cleanup
# 80712-1007 - tally bug fixes and time display change
# 81030-0346 - Added pause code stats
# 81030-1924 - Added total non-pause and total logged-in time to pause code section
# 81108-0716 - fixed user same-name bug
# 81110-0056 - fixed pause code display bug
# 90310-2039 - Admin header
# 90508-0644 - Changed to PHP long tags
# 90523-0935 - Rewrite of seconds to minutes and hours conversion
# 90717-1500 - Changed to be multi-campaign, multi-user-group select
# 90908-1058 - Added DEAD time statistics
# 100203-1131 - Added CUSTOMER time statistics
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
if (isset($_GET["DB"]))						{$DB=$_GET["DB"];}
	elseif (isset($_POST["DB"]))			{$DB=$_POST["DB"];}
if (isset($_GET["submit"]))					{$submit=$_GET["submit"];}
	elseif (isset($_POST["submit"]))		{$submit=$_POST["submit"];}
if (isset($_GET["ENVIAR"]))					{$ENVIAR=$_GET["ENVIAR"];}
	elseif (isset($_POST["ENVIAR"]))		{$ENVIAR=$_POST["ENVIAR"];}


if (strlen($shift)<2) {$shift='ALL';}

$LINKbase = "$PHP_SELF?query_date=$query_date&end_date=$end_date&group=$group&user_group=$user_group&shift=$shift&DB=$DB";

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
    echo "Nome ou Senha inválidos: |$PHP_AUTH_USER|$PHP_AUTH_PW|\n";
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
	}

if ($DB) {echo "$user_group_string|$user_group_ct|$user_groupQS|$i<BR>";}
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
echo "<TITLE>Agent Performance Detalhes</TITLE></HEAD><BODY BGCOLOR=WHITE marginheight=0 marginwidth=0 leftmargin=0 topmargin=0>\n";

	$short_header=1;

	require("admin_header.php");

echo "<TABLE CELLPADDING=4 CELLSPACING=0><TR><TD>";

echo "<FORM ACTION=\"$PHP_SELF\" METHOD=GET name=vicidial_report id=vicidial_report>\n";
echo "<TABLE CELLSPACING=3><TR><TD VALIGN=TOP> Datas:<BR>";
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
// o_cal.a_tpl.weekstart = 1; // Segunda week start
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
// o_cal.a_tpl.weekstart = 1; // Segunda week start
</script>
<?php

echo "</TD><TD VALIGN=TOP> Campanhas:<BR>";
echo "<SELECT SIZE=5 NAME=group[] multiple>\n";
if  (eregi("--ALL--",$group_string))
	{echo "<option value=\"--ALL--\" selected>-- ALL CAMPANHAS --</option>\n";}
else
	{echo "<option value=\"--ALL--\">-- ALL CAMPANHAS --</option>\n";}
$o=0;
while ($campaigns_to_print > $o)
{
	if (eregi("$groups[$o]\|",$group_string)) {echo "<option selected value=\"$groups[$o]\">$groups[$o]</option>\n";}
	  else {echo "<option value=\"$groups[$o]\">$groups[$o]</option>\n";}
	$o++;
}
echo "</SELECT>\n";
echo "</TD><TD VALIGN=TOP>Grupos de Usuário:<BR>";
echo "<SELECT SIZE=5 NAME=user_group[] multiple>\n";

if  (eregi("--ALL--",$user_group_string))
	{echo "<option value=\"--ALL--\" selected>-- ALL GRUPOS DE USUÁRIOS --</option>\n";}
else
	{echo "<option value=\"--ALL--\">-- ALL GRUPOS DE USUÁRIOS --</option>\n";}
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
echo "<INPUT TYPE=Submit NAME=ENVIAR VALUE=ENVIAR>\n";
echo "</TD><TD VALIGN=TOP> &nbsp; &nbsp; &nbsp; &nbsp; ";

echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;\n";
echo " <a href=\"./admin.php?ADD=999999\">RELATÓRIOS</a> </FONT>\n";
echo "</FONT>\n";
echo "</TD></TR></TABLE>";

echo "</FORM>\n\n";


echo "<PRE><FONT SIZE=2>\n";


if (!$group)
{
echo "\n";
echo "POR FAVOR SELECIONE UMA CAMPANHA E PERÍODO ACIMA E CLIQUE ENVIAR\n";
echo " NOTE: stats taken from shift specified\n";
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

if (strlen($user_group)>0) {$ugSQL="and vicidial_agent_log.user_group='$user_group'";}
else {$ugSQL='';}

echo "Agent Performance Detalhes                        $NOW_TIME\n";

echo "Time range: $query_date_BEGIN to $query_date_END\n\n";
echo "---------- AGENTS Detalhess -------------\n\n";





$statuses='-';
$statusesTXT='';
$statusesHEAD='';
$statusesHTML='';
$statusesARY[0]='';
$j=0;
$users='-';
$usersARY[0]='';
$user_namesARY[0]='';
$k=0;

$stmt="select count(*) as calls,sum(talk_sec) as talk,full_name,vicidial_users.user,sum(pause_sec),sum(wait_sec),sum(dispo_sec),status,sum(dead_sec) from vicidial_users,vicidial_agent_log where event_time <= '$query_date_END' and event_time >= '$query_date_BEGIN' and vicidial_users.user=vicidial_agent_log.user and pause_sec<36000 and wait_sec<36000 and talk_sec<36000 and dispo_sec<36000  $group_SQL $user_group_SQL group by user,full_name,status order by full_name,user,status desc limit 500000;";
$rslt=mysql_query($stmt, $link);
if ($DB) {echo "$stmt\n";}
$rows_to_print = mysql_num_rows($rslt);
$i=0;
while ($i < $rows_to_print)
	{
	$row=mysql_fetch_row($rslt);
#	$row[0] = ($row[0] - 1);	# subtract 1 for login/logout event compensation
	
	$calls[$i] =		$row[0];
	$talk_sec[$i] =		$row[1];
	$full_name[$i] =	$row[2];
	$user[$i] =			$row[3];
	$pause_sec[$i] =	$row[4];
	$wait_sec[$i] =		$row[5];
	$dispo_sec[$i] =	$row[6];
	$status[$i] =		$row[7];
	$dead_sec[$i] =		$row[8];
	$customer_sec[$i] =	($talk_sec[$i] - $dead_sec[$i]);
	if ($customer_sec[$i] < 1)
		{$customer_sec[$i]=0;}
	if ( (!eregi("-$status[$i]-", $statuses)) and (strlen($status[$i])>0) )
		{
		$statusesTXT = sprintf("%8s", $status[$i]);
		$statusesHEAD .= "----------+";
		$statusesHTML .= " $statusesTXT |";
		$statuses .= "$status[$i]-";
		$statusesARY[$j] = $status[$i];
		$j++;
		}
	if (!eregi("-$user[$i]-", $users))
		{
		$users .= "$user[$i]-";
		$usersARY[$k] = $user[$i];
		$user_namesARY[$k] = $full_name[$i];
		$k++;
		}

	$i++;
	}

echo "CALL STATS BREAKDOWN: (Statistics related to handling of calls only)\n";
echo "+-----------------+----------+--------+-----------+----------+--------+----------+--------+----------+--------+----------+--------+----------+--------+----------+--------+$statusesHEAD\n";
echo "| <a href=\"$LINKbase\">USER NAME</a>       | <a href=\"$LINKbase&stage=ID\">ID</a>       | <a href=\"$LINKbase&stage=LEADS\">CALLS</a>  | <a href=\"$LINKbase&stage=TIME\">TIME</a>      | PAUSE    |PAUSAVG | WAIT     |WAITAVG | TALK     |TALKAVG | DISPO    |DISPAVG | DEAD     |DEADAVG | CUSTOMER |CUSTAVG |$statusesHTML\n";
echo "+-----------------+----------+--------+-----------+----------+--------+----------+--------+----------+--------+----------+--------+----------+--------+----------+--------+$statusesHEAD\n";


### BEGIN loop through each user ###
$m=0;
while ($m < $k)
	{
	$Suser=$usersARY[$m];
	$Sfull_name=$user_namesARY[$m];
	$Stime=0;
	$Scalls=0;
	$Stalk_sec=0;
	$Spause_sec=0;
	$Swait_sec=0;
	$Sdispo_sec=0;
	$Sdead_sec=0;
	$Scustomer_sec=0;
	$SstatusesHTML='';

	### BEGIN loop through each status ###
	$n=0;
	while ($n < $j)
		{
		$Sstatus=$statusesARY[$n];
		$SstatusTXT='';
		### BEGIN loop through each stat line ###
		$i=0; $status_found=0;
		while ($i < $rows_to_print)
			{
			if ( ($Suser=="$user[$i]") and ($Sstatus=="$status[$i]") )
				{
				$Scalls =		($Scalls + $calls[$i]);
				$Stalk_sec =	($Stalk_sec + $talk_sec[$i]);
				$Spause_sec =	($Spause_sec + $pause_sec[$i]);
				$Swait_sec =	($Swait_sec + $wait_sec[$i]);
				$Sdispo_sec =	($Sdispo_sec + $dispo_sec[$i]);
				$Sdead_sec =	($Sdead_sec + $dead_sec[$i]);
				$Scustomer_sec =	($Scustomer_sec + $customer_sec[$i]);
				$SstatusTXT = sprintf("%8s", $calls[$i]);
				$SstatusesHTML .= " $SstatusTXT |";
				$status_found++;
				}
			$i++;
			}
		if ($status_found < 1)
			{
			$SstatusesHTML .= "        0 |";
			}
		### END loop through each stat line ###
		$n++;
		}
	### END loop through each status ###
	$Stime = ($Stalk_sec + $Spause_sec + $Swait_sec + $Sdispo_sec);
	$TOTcalls=($TOTcalls + $Scalls);
	$TOTtime=($TOTtime + $Stime);
	$TOTtotTALK=($TOTtotTALK + $Stalk_sec);
	$TOTtotWAIT=($TOTtotWAIT + $Swait_sec);
	$TOTtotPAUSE=($TOTtotPAUSE + $Spause_sec);
	$TOTtotDISPO=($TOTtotDISPO + $Sdispo_sec);
	$TOTtotDEAD=($TOTtotDEAD + $Sdead_sec);
	$TOTtotCUSTOMER=($TOTtotCUSTOMER + $Scustomer_sec);
	$Stime = ($Stalk_sec + $Spause_sec + $Swait_sec + $Sdispo_sec);
	if ( ($Scalls > 0) and ($Stalk_sec > 0) ) {$Stalk_avg = ($Stalk_sec/$Scalls);}
		else {$Stalk_avg=0;}
	if ( ($Scalls > 0) and ($Spause_sec > 0) ) {$Spause_avg = ($Spause_sec/$Scalls);}
		else {$Spause_avg=0;}
	if ( ($Scalls > 0) and ($Swait_sec > 0) ) {$Swait_avg = ($Swait_sec/$Scalls);}
		else {$Swait_avg=0;}
	if ( ($Scalls > 0) and ($Sdispo_sec > 0) ) {$Sdispo_avg = ($Sdispo_sec/$Scalls);}
		else {$Sdispo_avg=0;}
	if ( ($Scalls > 0) and ($Sdead_sec > 0) ) {$Sdead_avg = ($Sdead_sec/$Scalls);}
		else {$Sdead_avg=0;}
	if ( ($Scalls > 0) and ($Scustomer_sec > 0) ) {$Scustomer_avg = ($Scustomer_sec/$Scalls);}
		else {$Scustomer_avg=0;}

	$RAWuser = $Suser;
	$RAWcalls = $Scalls;
	$Scalls =	sprintf("%6s", $Scalls);

	if ($non_latin < 1)
		{
		$Sfull_name=	sprintf("%-15s", $Sfull_name); 
		while(strlen($Sfull_name)>15) {$Sfull_name = substr("$Sfull_name", 0, -1);}
		$Suser =		sprintf("%-8s", $Suser);
		while(strlen($Suser)>8) {$Suser = substr("$Suser", 0, -1);}
		}
	else
		{	
		$Sfull_name=	sprintf("%-45s", $Sfull_name); 
		while(mb_strlen($Sfull_name,'utf-8')>15) {$Sfull_name = mb_substr("$Sfull_name", 0, -1,'utf-8');}
		$Suser =	sprintf("%-24s", $Suser);
		while(mb_strlen($Suser,'utf-8')>8) {$Suser = mb_substr("$Suser", 0, -1,'utf-8');}
		}

	$pfUSERtime_MS =		sec_convert($Stime,'H'); 
	$pfUSERtotTALK_MS =		sec_convert($Stalk_sec,'H'); 
	$pfUSERavgTALK_MS =		sec_convert($Stalk_avg,'M'); 
	$USERtotPAUSE_MS =		sec_convert($Spause_sec,'H'); 
	$USERavgPAUSE_MS =		sec_convert($Spause_avg,'M'); 
	$USERtotWAIT_MS =		sec_convert($Swait_sec,'H'); 
	$USERavgWAIT_MS =		sec_convert($Swait_avg,'M'); 
	$USERtotDISPO_MS =		sec_convert($Sdispo_sec,'H'); 
	$USERavgDISPO_MS =		sec_convert($Sdispo_avg,'M'); 
	$USERtotDEAD_MS =		sec_convert($Sdead_sec,'H'); 
	$USERavgDEAD_MS =		sec_convert($Sdead_avg,'M'); 
	$USERtotCUSTOMER_MS =	sec_convert($Scustomer_sec,'H'); 
	$USERavgCUSTOMER_MS =	sec_convert($Scustomer_avg,'M'); 

	$pfUSERtime_MS =		sprintf("%9s", $pfUSERtime_MS);
	$pfUSERtotTALK_MS =		sprintf("%8s", $pfUSERtotTALK_MS);
	$pfUSERavgTALK_MS =		sprintf("%6s", $pfUSERavgTALK_MS);
	$pfUSERtotPAUSE_MS =	sprintf("%8s", $USERtotPAUSE_MS);
	$pfUSERavgPAUSE_MS =	sprintf("%6s", $USERavgPAUSE_MS);
	$pfUSERtotWAIT_MS =		sprintf("%8s", $USERtotWAIT_MS);
	$pfUSERavgWAIT_MS =		sprintf("%6s", $USERavgWAIT_MS);
	$pfUSERtotDISPO_MS =	sprintf("%8s", $USERtotDISPO_MS);
	$pfUSERavgDISPO_MS =	sprintf("%6s", $USERavgDISPO_MS);
	$pfUSERtotDEAD_MS =		sprintf("%8s", $USERtotDEAD_MS);
	$pfUSERavgDEAD_MS =		sprintf("%6s", $USERavgDEAD_MS);
	$pfUSERtotCUSTOMER_MS =	sprintf("%8s", $USERtotCUSTOMER_MS);
	$pfUSERavgCUSTOMER_MS =	sprintf("%6s", $USERavgCUSTOMER_MS);
	$PAUSEtotal[$m] = $pfUSERtotPAUSE_MS;

	$Toutput = "| $Sfull_name | <a href=\"./user_stats.php?user=$RAWuser\">$Suser</a> | $Scalls | $pfUSERtime_MS | $pfUSERtotPAUSE_MS | $pfUSERavgPAUSE_MS | $pfUSERtotWAIT_MS | $pfUSERavgWAIT_MS | $pfUSERtotTALK_MS | $pfUSERavgTALK_MS | $pfUSERtotDISPO_MS | $pfUSERavgDISPO_MS | $pfUSERtotDEAD_MS | $pfUSERavgDEAD_MS | $pfUSERtotCUSTOMER_MS | $pfUSERavgCUSTOMER_MS |$SstatusesHTML\n";

	$TOPsorted_output[$m] = $Toutput;

	if ($stage == 'ID')
		{$TOPsort[$m] =	'' . sprintf("%08s", $RAWuser) . '-----' . $m . '-----' . sprintf("%020s", $RAWuser);}
	if ($stage == 'LEADS')
		{$TOPsort[$m] =	'' . sprintf("%08s", $RAWcalls) . '-----' . $m . '-----' . sprintf("%020s", $RAWuser);}
	if ($stage == 'TIME')
		{$TOPsort[$m] =	'' . sprintf("%08s", $Stime) . '-----' . $m . '-----' . sprintf("%020s", $RAWuser);}
	if (!ereg("ID|TIME|LEADS",$stage))
		{echo "$Toutput";}

	$m++;
	}
### END loop through each user ###



### BEGIN sort through output to display properly ###
if (ereg("ID|TIME|LEADS",$stage))
	{
	if (ereg("ID",$stage))
		{sort($TOPsort, SORT_NUMERIC);}
	if (ereg("TIME|LEADS",$stage))
		{rsort($TOPsort, SORT_NUMERIC);}

	$m=0;
	while ($m < $k)
		{
		$sort_split = explode("-----",$TOPsort[$m]);
		$i = $sort_split[1];
		$sort_order[$m] = "$i";
		echo "$TOPsorted_output[$i]";
		$m++;
		}
	}
### END sort through output to display properly ###



###### LAST LINE FORMATTING ##########
### BEGIN loop through each status ###
$SUMstatusesHTML='';
$n=0;
while ($n < $j)
	{
	$Scalls=0;
	$Sstatus=$statusesARY[$n];
	$SUMstatusTXT='';
	### BEGIN loop through each stat line ###
	$i=0; $status_found=0;
	while ($i < $rows_to_print)
		{
		if ($Sstatus=="$status[$i]")
			{
			$Scalls =		($Scalls + $calls[$i]);
			$status_found++;
			}
		$i++;
		}
	### END loop through each stat line ###
	if ($status_found < 1)
		{
		$SUMstatusesHTML .= "        0 |";
		}
	else
		{
		$SUMstatusTXT = sprintf("%8s", $Scalls);
		$SUMstatusesHTML .= " $SUMstatusTXT |";
		}
	$n++;
	}
### END loop through each status ###

$TOTcalls =	sprintf("%7s", $TOTcalls);
$TOT_AGENTS = sprintf("%-4s", $m);

if ($TOTtotTALK < 1) {$TOTavgTALK = '0';}
else {$TOTavgTALK = ($TOTtotTALK / $TOTcalls);}
if ($TOTtotDISPO < 1) {$TOTavgDISPO = '0';}
else {$TOTavgDISPO = ($TOTtotDISPO / $TOTcalls);}
if ($TOTtotDEAD < 1) {$TOTavgDEAD = '0';}
else {$TOTavgDEAD = ($TOTtotDEAD / $TOTcalls);}
if ($TOTtotPAUSE < 1) {$TOTavgPAUSE = '0';}
else {$TOTavgPAUSE = ($TOTtotPAUSE / $TOTcalls);}
if ($TOTtotWAIT < 1) {$TOTavgWAIT = '0';}
else {$TOTavgWAIT = ($TOTtotWAIT / $TOTcalls);}
if ($TOTtotCUSTOMER < 1) {$TOTavgCUSTOMER = '0';}
else {$TOTavgCUSTOMER = ($TOTtotCUSTOMER / $TOTcalls);}

$TOTtime_MS =		sec_convert($TOTtime,'H'); 
$TOTtotTALK_MS =	sec_convert($TOTtotTALK,'H'); 
$TOTtotDISPO_MS =	sec_convert($TOTtotDISPO,'H'); 
$TOTtotDEAD_MS =	sec_convert($TOTtotDEAD,'H'); 
$TOTtotPAUSE_MS =	sec_convert($TOTtotPAUSE,'H'); 
$TOTtotWAIT_MS =	sec_convert($TOTtotWAIT,'H'); 
$TOTtotCUSTOMER_MS =	sec_convert($TOTtotCUSTOMER,'H'); 
$TOTavgTALK_MS =	sec_convert($TOTavgTALK,'M'); 
$TOTavgDISPO_MS =	sec_convert($TOTavgDISPO,'H'); 
$TOTavgDEAD_MS =	sec_convert($TOTavgDEAD,'H'); 
$TOTavgPAUSE_MS =	sec_convert($TOTavgPAUSE,'H'); 
$TOTavgWAIT_MS =	sec_convert($TOTavgWAIT,'H'); 
$TOTavgCUSTOMER_MS =	sec_convert($TOTavgCUSTOMER,'H'); 

$TOTtime_MS =		sprintf("%10s", $TOTtime_MS);
$TOTtotTALK_MS =	sprintf("%10s", $TOTtotTALK_MS);
$TOTtotDISPO_MS =	sprintf("%10s", $TOTtotDISPO_MS);
$TOTtotDEAD_MS =	sprintf("%10s", $TOTtotDEAD_MS);
$TOTtotPAUSE_MS =	sprintf("%10s", $TOTtotPAUSE_MS);
$TOTtotWAIT_MS =	sprintf("%10s", $TOTtotWAIT_MS);
$TOTtotCUSTOMER_MS =	sprintf("%10s", $TOTtotCUSTOMER_MS);
$TOTavgTALK_MS =	sprintf("%6s", $TOTavgTALK_MS);
$TOTavgDISPO_MS =	sprintf("%6s", $TOTavgDISPO_MS);
$TOTavgDEAD_MS =	sprintf("%6s", $TOTavgDEAD_MS);
$TOTavgPAUSE_MS =	sprintf("%6s", $TOTavgPAUSE_MS);
$TOTavgWAIT_MS =	sprintf("%6s", $TOTavgWAIT_MS);
$TOTavgCUSTOMER_MS =	sprintf("%6s", $TOTavgCUSTOMER_MS);

while(strlen($TOTtime_MS)>10) {$TOTtime_MS = substr("$TOTtime_MS", 0, -1);}
while(strlen($TOTtotTALK_MS)>10) {$TOTtotTALK_MS = substr("$TOTtotTALK_MS", 0, -1);}
while(strlen($TOTtotDISPO_MS)>10) {$TOTtotDISPO_MS = substr("$TOTtotDISPO_MS", 0, -1);}
while(strlen($TOTtotDEAD_MS)>10) {$TOTtotDEAD_MS = substr("$TOTtotDEAD_MS", 0, -1);}
while(strlen($TOTtotPAUSE_MS)>10) {$TOTtotPAUSE_MS = substr("$TOTtotPAUSE_MS", 0, -1);}
while(strlen($TOTtotWAIT_MS)>10) {$TOTtotWAIT_MS = substr("$TOTtotWAIT_MS", 0, -1);}
while(strlen($TOTtotCUSTOMER_MS)>10) {$TOTtotCUSTOMER_MS = substr("$TOTtotCUSTOMER_MS", 0, -1);}
while(strlen($TOTavgTALK_MS)>6) {$TOTavgTALK_MS = substr("$TOTavgTALK_MS", 0, -1);}
while(strlen($TOTavgDISPO_MS)>6) {$TOTavgDISPO_MS = substr("$TOTavgDISPO_MS", 0, -1);}
while(strlen($TOTavgDEAD_MS)>6) {$TOTavgDEAD_MS = substr("$TOTavgDEAD_MS", 0, -1);}
while(strlen($TOTavgPAUSE_MS)>6) {$TOTavgPAUSE_MS = substr("$TOTavgPAUSE_MS", 0, -1);}
while(strlen($TOTavgWAIT_MS)>6) {$TOTavgWAIT_MS = substr("$TOTavgWAIT_MS", 0, -1);}
while(strlen($TOTavgCUSTOMER_MS)>6) {$TOTavgCUSTOMER_MS = substr("$TOTavgCUSTOMER_MS", 0, -1);}


echo "+-----------------+----------+--------+-----------+----------+--------+----------+--------+----------+--------+----------+--------+----------+--------+----------+--------+$statusesHEAD\n";
echo "|  TOTALS        AGENTS:$TOT_AGENTS | $TOTcalls| $TOTtime_MS|$TOTtotPAUSE_MS| $TOTavgPAUSE_MS |$TOTtotWAIT_MS| $TOTavgWAIT_MS |$TOTtotTALK_MS| $TOTavgTALK_MS |$TOTtotDISPO_MS| $TOTavgDISPO_MS |$TOTtotDEAD_MS| $TOTavgDEAD_MS |$TOTtotCUSTOMER_MS| $TOTavgCUSTOMER_MS |$SUMstatusesHTML\n";
echo "+-----------------+----------+--------+-----------+----------+--------+----------+--------+----------+--------+----------+--------+----------+--------+----------+--------+$statusesHEAD\n";

echo "\n\n";















$sub_statuses='-';
$sub_statusesTXT='';
$sub_statusesHEAD='';
$sub_statusesHTML='';
$sub_statusesARY=$MT;
$j=0;
$PCusers='-';
$PCusersARY=$MT;
$PCuser_namesARY=$MT;
$k=0;
$stmt="select full_name,vicidial_users.user,sum(pause_sec),sub_status,sum(wait_sec + talk_sec + dispo_sec) from vicidial_users,vicidial_agent_log where event_time <= '$query_date_END' and event_time >= '$query_date_BEGIN' and vicidial_users.user=vicidial_agent_log.user and pause_sec<36000  $group_SQL $user_group_SQL group by user,full_name,sub_status order by user,full_name,sub_status desc limit 100000;";
$rslt=mysql_query($stmt, $link);
if ($DB) {echo "$stmt\n";}
$subs_to_print = mysql_num_rows($rslt);
$i=0;
while ($i < $subs_to_print)
	{
	$row=mysql_fetch_row($rslt);
	$PCfull_name[$i] =	$row[0];
	$PCuser[$i] =		$row[1];
	$PCpause_sec[$i] =	$row[2];
	$sub_status[$i] =	$row[3];
	$PCnon_pause_sec[$i] =	$row[4];

#	echo "$sub_status[$i]|$PCpause_sec[$i]\n";
#	if ( (!eregi("-$sub_status[$i]-", $sub_statuses)) and (strlen($sub_status[$i])>0) )
	if (!eregi("-$sub_status[$i]-", $sub_statuses))
		{
		$sub_statusesTXT = sprintf("%8s", $sub_status[$i]);
		$sub_statusesHEAD .= "----------+";
		$sub_statusesHTML .= " $sub_statusesTXT |";
		$sub_statuses .= "$sub_status[$i]-";
		$sub_statusesARY[$j] = $sub_status[$i];
		$j++;
		}
	if (!eregi("-$PCuser[$i]-", $PCusers))
		{
		$PCusers .= "$PCuser[$i]-";
		$PCusersARY[$k] = $PCuser[$i];
		$PCuser_namesARY[$k] = $PCfull_name[$i];
		$k++;
		}

	$i++;
	}

echo "PAUSE CODE BREAKDOWN:\n";
echo "+-----------------+----------+----------+----------+----------+  +$sub_statusesHEAD\n";
echo "| USER NAME       | ID       | TOTAL    | NONPAUSE | PAUSE    |  |$sub_statusesHTML\n";
echo "+-----------------+----------+----------+----------+----------+  +$sub_statusesHEAD\n";


### BEGIN loop through each user ###
$m=0;
$Suser_ct = count($usersARY);
$TOTtotNONPAUSE = 0;
$TOTtotTOTAL = 0;

while ($m < $k)
	{
	$d=0;
	while ($d < $Suser_ct)
		{
		if ($usersARY[$d] === "$PCusersARY[$m]")
			{$pcPAUSEtotal = $PAUSEtotal[$d];}
		$d++;
		}
	$Suser=$PCusersARY[$m];
	$Sfull_name=$PCuser_namesARY[$m];
	$Spause_sec=0;
	$Snon_pause_sec=0;
	$Stotal_sec=0;
	$SstatusesHTML='';

	### BEGIN loop through each status ###
	$n=0;
	while ($n < $j)
		{
		$Sstatus=$sub_statusesARY[$n];
		$SstatusTXT='';
		### BEGIN loop through each stat line ###
		$i=0; $status_found=0;
		while ($i < $subs_to_print)
			{
			if ( ($Suser=="$PCuser[$i]") and ($Sstatus=="$sub_status[$i]") )
				{
				$Spause_sec =	($Spause_sec + $PCpause_sec[$i]);
				$Snon_pause_sec =	($Snon_pause_sec + $PCnon_pause_sec[$i]);
				$Stotal_sec =	($Stotal_sec + $PCnon_pause_sec[$i] + $PCpause_sec[$i]);

				$USERcodePAUSE_MS =		sec_convert($PCpause_sec[$i],'H'); 
				$pfUSERcodePAUSE_MS =	sprintf("%6s", $USERcodePAUSE_MS);

				$SstatusTXT = sprintf("%8s", $pfUSERcodePAUSE_MS);
				$SstatusesHTML .= " $SstatusTXT |";
				$status_found++;
				}
			$i++;
			}
		if ($status_found < 1)
			{
			$SstatusesHTML .= "        0 |";
			}
		### END loop through each stat line ###
		$n++;
		}
	### END loop through each status ###
	$TOTtotPAUSE=($TOTtotPAUSE + $Spause_sec);

	if ($non_latin < 1)
		{
		$Sfull_name=	sprintf("%-15s", $Sfull_name); 
		while(strlen($Sfull_name)>15) {$Sfull_name = substr("$Sfull_name", 0, -1);}
		$Suser =		sprintf("%-8s", $Suser);
		while(strlen($Suser)>8) {$Suser = substr("$Suser", 0, -1);}
		}
	else
		{
		$Sfull_name=	sprintf("%-45s", $Sfull_name); 
		while(mb_strlen($Sfull_name,'utf-8')>15) {$Sfull_name = mb_substr("$Sfull_name", 0, -1,'utf-8');}
		$Suser =	sprintf("%-24s", $Suser);
		while(mb_strlen($Suser,'utf-8')>8) {$Suser = mb_substr("$Suser", 0, -1,'utf-8');}
		}

	$TOTtotNONPAUSE = ($TOTtotNONPAUSE + $Snon_pause_sec);
	$TOTtotTOTAL = ($TOTtotTOTAL + $Stotal_sec);

	$USERtotPAUSE_MS =		sec_convert($Spause_sec,'H'); 
	$USERtotNONPAUSE_MS =	sec_convert($Snon_pause_sec,'H'); 
	$USERtotTOTAL_MS =		sec_convert($Stotal_sec,'H'); 

	$pfUSERtotPAUSE_MS =		sprintf("%8s", $USERtotPAUSE_MS);
	$pfUSERtotNONPAUSE_MS =		sprintf("%8s", $USERtotNONPAUSE_MS);
	$pfUSERtotTOTAL_MS =		sprintf("%8s", $USERtotTOTAL_MS);

	$BOTTOMoutput = "| $Sfull_name | $Suser | $pfUSERtotTOTAL_MS | $pfUSERtotNONPAUSE_MS | $pfUSERtotPAUSE_MS |  |$SstatusesHTML\n";

	$BOTTOMsorted_output[$m] = $BOTTOMoutput;

	echo "$BOTTOMoutput";

	$m++;
	}
### END loop through each user ###



### BEGIN sort through output to display properly ###
#if (ereg("ID|TIME|LEADS",$stage))
#	{
#	$n=0;
#	while ($n <= $m)
#		{
#		$i = $sort_order[$m];
#		echo "$BOTTOMsorted_output[$i]";
#		$m--;
#		}
#	}
### END sort through output to display properly ###



###### LAST LINE FORMATTING ##########
### BEGIN loop through each status ###
$SUMstatusesHTML='';
$TOTtotPAUSE=0;
$n=0;
while ($n < $j)
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
		$SUMstatusesHTML .= "        0 |";
		}
	else
		{
		$TOTtotPAUSE = ($TOTtotPAUSE + $Scalls);

		$USERsumstatPAUSE_MS =		sec_convert($Scalls,'H'); 
		$pfUSERsumstatPAUSE_MS =	sprintf("%8s", $USERsumstatPAUSE_MS);

		$SUMstatusTXT = sprintf("%8s", $pfUSERsumstatPAUSE_MS);
		$SUMstatusesHTML .= " $SUMstatusTXT |";
		}
	$n++;
	}
### END loop through each status ###

	$TOT_AGENTS = sprintf("%-4s", $m);

	$TOTtotPAUSE_MS =		sec_convert($TOTtotPAUSE,'H'); 
	$TOTtotNONPAUSE_MS =	sec_convert($TOTtotNONPAUSE,'H'); 
	$TOTtotTOTAL_MS =		sec_convert($TOTtotTOTAL,'H'); 

	$TOTtotPAUSE_MS =		sprintf("%10s", $TOTtotPAUSE_MS);
	$TOTtotNONPAUSE_MS =	sprintf("%10s", $TOTtotNONPAUSE_MS);
	$TOTtotTOTAL_MS =		sprintf("%10s", $TOTtotTOTAL_MS);

	while(strlen($TOTtotPAUSE_MS)>10) {$TOTtotPAUSE_MS = substr("$TOTtotPAUSE_MS", 0, -1);}
	while(strlen($TOTtotNONPAUSE_MS)>10) {$TOTtotNONPAUSE_MS = substr("$TOTtotNONPAUSE_MS", 0, -1);}
	while(strlen($TOTtotTOTAL_MS)>10) {$TOTtotTOTAL_MS = substr("$TOTtotTOTAL_MS", 0, -1);}


echo "+-----------------+----------+----------+----------+----------+  +$sub_statusesHEAD\n";
echo "|  TOTALS        AGENTS:$TOT_AGENTS |$TOTtotTOTAL_MS|$TOTtotNONPAUSE_MS|$TOTtotPAUSE_MS|  |$SUMstatusesHTML\n";
echo "+----------------------------+----------+----------+----------+  +$sub_statusesHEAD\n";

echo "\n\n";

}


?>

</TD></TR></TABLE>

</BODY></HTML>
