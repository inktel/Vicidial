<?php 
# AST_agent_days_detail.php
# 
# Copyright (C) 2010  Matt Florell <vicidial@gmail.com>    LICENSE: AGPLv2
#
# CHANGES
#
# 90206-2202 - First build
# 90225-1051 - Added CSV download option
# 90310-0752 - Added admin header
# 90508-0644 - Changed to PHP long tags
# 100214-1421 - Sort menu alphabetically
# 100216-0042 - Added popup date selector
#


require("dbconnect.php");

$PHP_AUTH_USER=$_SERVER['PHP_AUTH_USER'];
$PHP_AUTH_PW=$_SERVER['PHP_AUTH_PW'];
$PHP_SELF=$_SERVER['PHP_SELF'];
if (isset($_GET["query_date"]))				{$query_date=$_GET["query_date"];}
	elseif (isset($_POST["query_date"]))	{$query_date=$_POST["query_date"];}
if (isset($_GET["end_date"]))				{$end_date=$_GET["end_date"];}
	elseif (isset($_POST["end_date"]))		{$end_date=$_POST["end_date"];}
if (isset($_GET["group"]))					{$group=$_GET["group"];}
	elseif (isset($_POST["group"]))			{$group=$_POST["group"];}
if (isset($_GET["user"]))					{$user=$_GET["user"];}
	elseif (isset($_POST["user"]))			{$user=$_POST["user"];}
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
if (isset($_GET["ENVIAR"]))					{$ENVIAR=$_GET["ENVIAR"];}
	elseif (isset($_POST["ENVIAR"]))		{$ENVIAR=$_POST["ENVIAR"];}

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

$customer_interactive_statuses='';
$stmt="select status from vicidial_statuses where human_answered='Y';";
$rslt=mysql_query($stmt, $link);
if ($DB) {echo "$stmt\n";}
$statha_to_print = mysql_num_rows($rslt);
$i=0;
while ($i < $statha_to_print)
	{
	$row=mysql_fetch_row($rslt);
	$customer_interactive_statuses .= "|$row[0]";
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
	$customer_interactive_statuses .= "|$row[0]";
	$i++;
	}
if (strlen($customer_interactive_statuses)>0)
	{$customer_interactive_statuses .= '|';}

#$customer_interactive_statuses = '|NI|DNC|CALLBK|AP|SALE|COMP|HAP1|HAP2|HBED|DIED|';
#$customer_interactive_statuses = '|NI|DNC|CALLBK|XFER|C2|B7|B8|C1|';

$LINKbase = "$PHP_SELF?query_date=$query_date&end_date=$end_date&shift=$shift&DB=$DB&user=$user$groupQS";

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
	echo "<TITLE>Agent Status Diário Report</TITLE></HEAD><BODY BGCOLOR=white marginheight=0 marginwidth=0 leftmargin=0 topmargin=0>\n";
	echo "<span style=\"position:absolute;left:0px;top:0px;z-index:20;\"  id=admin_header>";

	$short_header=1;

	require("admin_header.php");

	echo "</span>\n";
	echo "<span style=\"position:absolute;left:3px;top:3px;z-index:19;\"  id=agent_status_stats>\n";
	echo "<PRE><FONT SIZE=2>\n";
	}

if (strlen($group[0]) < 1)
	{
	echo "\n";
	echo "POR FAVOR SELECIONE UM USUÁRIO E PERÍODO ACIMA E CLIQUE ENVIAR\n";
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

	if ($file_download < 1)
		{
		echo "Agent Status Diário Report: $user                     $NOW_TIME\n";

		echo "Time range: $query_date_BEGIN to $query_date_END\n\n";
		echo "---------- AGENTE Detalhess -------------\n\n";
		}
	else
		{
		$file_output .= "Agent Status Diário Report: $user                     $NOW_TIME\n";
		$file_output .= "Time range: $query_date_BEGIN to $query_date_END\n\n";
		}

	$statuses='-';
	$statusesTXT='';
	$statusesHEAD='';
	$statusesHTML='';
	$statusesFILE='';
	$statusesARY[0]='';
	$j=0;
	$dates='-';
	$datesARY[0]='';
	$date_namesARY[0]='';
	$k=0;

	$stmt="select date_format(event_time, '%Y-%m-%d') as date,count(*) as calls,status from vicidial_users,vicidial_agent_log where event_time <= '$query_date_END' and event_time >= '$query_date_BEGIN' and vicidial_users.user=vicidial_agent_log.user and vicidial_agent_log.user='$user' $group_SQL $user_group_SQL group by date,status order by date,status desc limit 500000;";
	$rslt=mysql_query($stmt, $link);
	if ($DB) {echo "$stmt\n";}
	$rows_to_print = mysql_num_rows($rslt);
	$i=0;
	while ($i < $rows_to_print)
		{
		$row=mysql_fetch_row($rslt);

		if ( ($row[1] > 0) and (strlen($row[2]) > 0) )
			{
			$date[$i] =			$row[0];
			$calls[$i] =		$row[1];
			$status[$i] =		$row[2];
			if ( (!eregi("-$status[$i]-", $statuses)) and (strlen($status[$i])>0) )
				{
				$statusesTXT = sprintf("%8s", $status[$i]);
				$statusesHEAD .= "----------+";
				$statusesHTML .= " $statusesTXT |";
				$statusesFILE .= "$statusesTXT,";
				$statuses .= "$status[$i]-";
				$statusesARY[$j] = $status[$i];
				$j++;
				}
			if (!eregi("-$date[$i]-", $dates))
				{
				$dates .= "$date[$i]-";
				$datesARY[$k] = $date[$i];
				$k++;
				}
			}
		$i++;
		}

	if ($file_download < 1)
		{
		echo "LEAD STATS BREAKDOWN:\n";
		echo "+------------+--------+--------+--------+$statusesHEAD\n";
		echo "| <a href=\"$LINKbase\">DATE</a>       | <a href=\"$LINKbase&stage=LEADS\">CALLS</a>  | <a href=\"$LINKbase&stage=CI\">CIcalls</a>| <a href=\"$LINKbase&stage=DNCCI\">DNC/CI%</a>|$statusesHTML\n";
		echo "+------------+--------+--------+--------+$statusesHEAD\n";
		}
	else
		{
		$file_output .= "DATE,CALLS,CIcalls,DNC-CI%,$statusesFILE\n";
		}

	### BEGIN loop through each user ###
	$m=0;
	$CIScountTOT=0;
	$DNCcountTOT=0;
	while ($m < $k)
		{
		$Sdate=$datesARY[$m];
		$Scalls=$calls[$m];
		$SstatusesHTML='';
		$SstatusesFILE='';
		$CIScount=0;
		$DNCcount=0;

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
	#			if ( (eregi("$date[$i]", $Sdate)) and ($Sstatus=="$status[$i]") )
				if ( ($Sdate=="$date[$i]") and ($Sstatus=="$status[$i]") )
					{
					$Scalls =		($Scalls + $calls[$i]);
					if (eregi("\|$status[$i]\|",$customer_interactive_statuses))
						{
						$CIScount =	($CIScount + $calls[$i]);
						$CIScountTOT =	($CIScountTOT + $calls[$i]);
						}
					if (eregi("DNC", $status[$i]))
						{
						$DNCcount =	($DNCcount + $calls[$i]);
						$DNCcountTOT =	($DNCcountTOT + $calls[$i]);
						}
					$SstatusTXT = sprintf("%8s", $calls[$i]);
					$SstatusesHTML .= " $SstatusTXT |";
					$SstatusesFILE .= "$SstatusTXT,";
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
		$TOTcalls=($TOTcalls + $Scalls);

		$RAWdate = $Sdate;
		$RAWcalls = $Scalls;
		$RAWcis = $CIScount;
		$Scalls =	sprintf("%6s", $Scalls);
		$CIScount =	sprintf("%6s", $CIScount);

		$Sdate =		sprintf("%-10s", $Sdate);
			while(strlen($Suser)>10) {$Suser = substr("$Sdate", 0, -1);}

		if ( ($DNCcount < 1) or ($CIScount < 1) )
			{$DNCcountPCTs=0;}
		else
			{
			$DNCcountPCTs = ( ($DNCcount / $CIScount) * 100);
			}
		$RAWdncPCT = $DNCcountPCTs;
	#	$DNCcountPCTs = round($DNCcountPCTs,2);
		$DNCcountPCTs = round($DNCcountPCTs);
		$rawDNCcountPCTs = $DNCcountPCTs;
	#	$DNCcountPCTs = sprintf("%3.2f", $DNCcountPCTs);
		$DNCcountPCTs = sprintf("%6s", $DNCcountPCTs);

		if ($file_download < 1)
			{
			$Toutput = "| <a href=\"./user_stats.php?user=$user&start_date=$RAWdate\">$Sdate</a> | $Scalls | $CIScount | $DNCcountPCTs%|$SstatusesHTML\n";
			}
		else
			{
			$fileToutput = "$RAWdate,$RAWcalls,$RAWcis,$rawDNCcountPCTs%,$SstatusesFILE\n";
			}

		$TOPsorted_output[$m] = $Toutput;
		$TOPsorted_outputFILE[$m] = $fileToutput;

		if ($stage == 'ID')
			{
			$TOPsort[$m] =	'' . sprintf("%08s", $RAWdate) . '-----' . $m . '-----' . sprintf("%020s", $RAWdate);
			$TOPsortTALLY[$m]=$RAWcalls;
			}
		if ($stage == 'LEADS')
			{
			$TOPsort[$m] =	'' . sprintf("%08s", $RAWcalls) . '-----' . $m . '-----' . sprintf("%020s", $RAWdate);
			$TOPsortTALLY[$m]=$RAWcalls;
			}
		if ($stage == 'TIME')
			{
			$TOPsort[$m] =	'' . sprintf("%08s", $Stime) . '-----' . $m . '-----' . sprintf("%020s", $RAWdate);
			$TOPsortTALLY[$m]=$Stime;
			}
		if ($stage == 'CI')
			{
			$TOPsort[$m] =	'' . sprintf("%08s", $RAWcis) . '-----' . $m . '-----' . sprintf("%020s", $RAWdate);
			$TOPsortTALLY[$m]=$RAWcis;
			}
		if ($stage == 'DNCCI')
			{
			$TOPsort[$m] =	'' . sprintf("%08s", $RAWdncPCT) . '-----' . $m . '-----' . sprintf("%020s", $RAWdate);
			$TOPsortTALLY[$m]=$RAWdncPCT;
			}
		if (!ereg("ID|TIME|LEADS|CI|DNCCI",$stage))
			{
			if ($file_download < 1)
				{echo "$Toutput";}
			else
				{$file_output .= "$fileToutput";}
			}

		if ($TOPsortMAX < $TOPsortTALLY[$m]) {$TOPsortMAX = $TOPsortTALLY[$m];}

		$m++;
		}
	### END loop through each user ###

	$TOT_AGENTS = sprintf("%4s", $m);


	### BEGIN sort through output to display properly ###
	if (ereg("ID|TIME|LEADS|CI|DNCCI",$stage))
		{
		if (ereg("ID",$stage))
			{sort($TOPsort, SORT_NUMERIC);}
		if (ereg("TIME|LEADS|CI|DNCCI",$stage))
			{rsort($TOPsort, SORT_NUMERIC);}

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
			$SUMstatusesFILE .= "$SUMstatusTXT,";
			}
		$n++;
		}
	### END loop through each status ###

	$TOTcalls = sprintf("%7s", $TOTcalls);
	$CIScountTOT = sprintf("%7s", $CIScountTOT);
	if ( ($DNCcountTOT < 1) or ($CIScountTOT < 1) )
		{$DNCcountPCT=0;}
	else
		{
		$DNCcountPCT = ( ($DNCcountTOT / $CIScountTOT) * 100);
		}
	#$DNCcountPCT = round($DNCcountPCT,2);
	$DNCcountPCT = round($DNCcountPCT);
	#$DNCcountPCT = sprintf("%3.2f", $DNCcountPCT);
	$DNCcountPCT = sprintf("%6s", $DNCcountPCT);


	if ($file_download < 1)
		{
		echo "+------------+--------+--------+--------+$statusesHEAD\n";
		echo "| TOTALS     | $TOTcalls| $CIScountTOT| $DNCcountPCT%|$SUMstatusesHTML\n";
		echo "+------------+--------+--------+--------+$statusesHEAD\n";

		echo "\n\n</PRE>";
		}
	else
		{
		$file_output .= "TOTALS,$TOTcalls,$CIScountTOT,$DNCcountPCT%,$SUMstatusesFILE\n";
		}
	}


if ($file_download > 0)
	{
	$US='_';
	$FILE_TIME = date("Ymd-His");
	$CSVfilename = "AGENT_DAYS_$user$US$FILE_TIME.csv";

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
echo "</TD><TD VALIGN=TOP>Usuário:<BR>";
echo "<INPUT TYPE=TEXT SIZE=10 NAME=user value=\"$user\">\n";
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
echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";
if (strlen($user) > 1)
	{
	echo " <a href=\"$LINKbase&stage=$stage&file_download=1\">DOWNLOAD</a> | \n";
	echo " <a href=\"./admin.php?ADD=3&user=$user\">USER</a> | \n";
	echo " <a href=\"./user_stats.php?user=$user&begin_date=$query_date&end_date=$end_date\">USER STATS</a> | \n";
	}
else
	{echo " <a href=\"./admin.php?ADD=0\">USUÁRIOS</a> | \n";}
echo "<a href=\"./admin.php?ADD=999999\">RELATÓRIOS</a> </FONT>\n";
echo "</TD></TR></TABLE>";

echo "</FORM>\n\n";

echo "</span>\n";
echo "<span style=\"position:absolute;left:3px;top:3px;z-index:18;\"  id=agent_status_bars>\n";
echo "<PRE><FONT SIZE=2>\n\n\n\n\n\n\n\n\n\n";

$m=0;
while ($m < $k)
	{
	$sort_split = explode("-----",$TOPsort[$m]);
	$i = $sort_split[1];
	$sort_order[$m] = "$i";

	if ( ($TOPsortTALLY[$i] < 1) or ($TOPsortMAX < 1) )
		{echo "              \n";}
	else
		{
		echo "              <SPAN class=\"yellow\">";
		$TOPsortPLOT = ( ($TOPsortTALLY[$i] / $TOPsortMAX) * 120 );
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

?>

</BODY></HTML>
