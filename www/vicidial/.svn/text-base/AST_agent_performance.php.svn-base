<?php 
# AST_agent_performance.php
# 
# Copyright (C) 2009  Matt Florell <vicidial@gmail.com>    LICENSE: AGPLv2
#
# CHANGES
#
# 60619-1711 - Added variable filtering to eliminate SQL injection attack threat
#            - Added required user/pass to gain access to this page
# 70201-1203 - Added non_latin UTF8 output code, widened USER ID to 8 chars
# 90508-0644 - Changed to PHP long tags
#

require("dbconnect.php");

$PHP_AUTH_USER=$_SERVER['PHP_AUTH_USER'];
$PHP_AUTH_PW=$_SERVER['PHP_AUTH_PW'];
$PHP_SELF=$_SERVER['PHP_SELF'];
if (isset($_GET["group"]))				{$group=$_GET["group"];}
	elseif (isset($_POST["group"]))		{$group=$_POST["group"];}
if (isset($_GET["query_date"]))				{$query_date=$_GET["query_date"];}
	elseif (isset($_POST["query_date"]))		{$query_date=$_POST["query_date"];}
if (isset($_GET["shift"]))				{$shift=$_GET["shift"];}
	elseif (isset($_POST["shift"]))		{$shift=$_POST["shift"];}
if (isset($_GET["submit"]))				{$submit=$_GET["submit"];}
	elseif (isset($_POST["submit"]))		{$submit=$_POST["submit"];}
if (isset($_GET["SUBMIT"]))				{$SUBMIT=$_GET["SUBMIT"];}
	elseif (isset($_POST["SUBMIT"]))		{$SUBMIT=$_POST["SUBMIT"];}

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

$NOW_DATE = date("Y-m-d");
$NOW_TIME = date("Y-m-d H:i:s");
$STARTtime = date("U");
if (!isset($group)) {$group = '';}
if (!isset($query_date)) {$query_date = $NOW_DATE;}

$stmt="select campaign_id from vicidial_campaigns;";
if ($non_latin > 0) {$rslt=mysql_query("SET NAMES 'UTF8'");}
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
echo "<META HTTP-EQUIV=\"Content-Type\" CONTENT=\"text/html; charset=utf-8\">\n";
echo "<TITLE>VICIDIAL: Agent Performance</TITLE></HEAD><BODY BGCOLOR=WHITE>\n";
echo "<FORM ACTION=\"$PHP_SELF\" METHOD=GET>\n";
echo "<INPUT TYPE=TEXT NAME=query_date SIZE=19 MAXLENGTH=19 VALUE=\"$query_date\">\n";
echo "<SELECT SIZE=1 NAME=group>\n";
	$o=0;
	while ($campaigns_to_print > $o)
	{
		if ($groups[$o] == $group) {echo "<option selected value=\"$groups[$o]\">$groups[$o]</option>\n";}
		  else {echo "<option value=\"$groups[$o]\">$groups[$o]</option>\n";}
		$o++;
	}
echo "</SELECT>\n";
echo "<SELECT SIZE=1 NAME=shift>\n";
echo "<option selected value=\"AM\">AM</option>\n";
echo "<option value=\"PM\">PM</option>\n";
echo "</SELECT>\n";
echo "<INPUT TYPE=SUBMIT NAME=SUBMIT VALUE=SUBMIT>\n";
echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; <a href=\"./admin.php?ADD=34&campaign_id=$group\">MODIFY</a> | <a href=\"./admin.php?ADD=999999\">REPORTS</a> </FONT>\n";
echo "</FORM>\n\n";

echo "<PRE><FONT SIZE=2>\n";


if (!$group)
{
echo "\n";
echo "PLEASE SELECT A SERVER AND DATE-TIME ABOVE AND CLICK SUBMIT\n";
echo " NOTE: stats taken from 6 hour shift specified\n";
}

else
{
if ($shift == 'AM') 
	{
	$query_date_BEGIN = "$query_date 08:45:00";   
	$query_date_END = "$query_date 15:33:00";
	$time_BEGIN = "08:45:00";   
	$time_END = "15:33:00";
	}
if ($shift == 'PM') 
	{
	$query_date_BEGIN = "$query_date 15:33:00";   
	$query_date_END = "$query_date 23:15:00";
	$time_BEGIN = "15:33:00";   
	$time_END = "23:15:00";
	}

echo "VICIDIAL: Agent Performance                             $NOW_TIME\n";

echo "Time range: $query_date_BEGIN to $query_date_END\n\n";
echo "---------- AGENTS Details -------------\n\n";

echo "+-----------------+----------+--------+--------+--------+------+------+------+------+------+------+------+\n";
echo "| USER NAME       | ID       | CALLS  | TALK   | TALKAVG| A    | B    | DC   | DNC  | N    | NI   | SALE |\n";
echo "+-----------------+----------+--------+--------+--------+------+------+------+------+------+------+------+\n";

$stmt="select count(*) as calls,sum(length_in_sec) as talk,full_name,vicidial_users.user,avg(length_in_sec) from vicidial_users,vicidial_log where call_date <= '$query_date_END' and call_date >= '$query_date_BEGIN' and vicidial_users.user=vicidial_log.user and campaign_id='" . mysql_real_escape_string($group) . "' group by full_name order by calls desc limit 1000;";
if ($non_latin > 0) {$rslt=mysql_query("SET NAMES 'UTF8'");}
$rslt=mysql_query($stmt, $link);
if ($DB) {echo "$stmt\n";}
$rows_to_print = mysql_num_rows($rslt);
$i=0;
while ($i < $rows_to_print)
	{
	$row=mysql_fetch_row($rslt);
	$TOTcalls=($TOTcalls + $row[0]);
	$TOTtotTALK=($TOTtotTALK + $row[1]);
	$calls[$i] =	sprintf("%-6s", $row[0]);

	if ($non_latin < 1)
	{
   	 $full_name[$i]=	sprintf("%-15s", $row[2]); 
	 while(strlen($full_name[$i])>15) {$full_name[$i] = substr("$full_name[$i]", 0, -1);}

	 $user[$i] =		sprintf("%-6s", $row[3]);
        while(strlen($user[$i])>6) {$user[$i] = substr("$user[$i]", 0, -1);}
       }
	else
	{	
        $full_name[$i]=	sprintf("%-45s", $row[2]); 
	 while(mb_strlen($full_name[$i],'utf-8')>15) {$full_name[$i] = mb_substr("$full_name[$i]", 0, -1,'utf-8');}

 	 $user[$i] =		sprintf("%-18s", $row[3]);
	 while(mb_strlen($user[$i],'utf-8')>6) {$user[$i] = mb_substr("$user[$i]", 0, -1,'utf-8');}
	}

	$user[$i] =		sprintf("%-8s", $row[3]);
	$USERtotTALK =	$row[1];
	$USERavgTALK =	$row[4];

	$USERtotTALK_M = ($USERtotTALK / 60);
	$USERtotTALK_M = round($USERtotTALK_M, 2);
	$USERtotTALK_M_int = intval("$USERtotTALK_M");
	$USERtotTALK_S = ($USERtotTALK_M - $USERtotTALK_M_int);
	$USERtotTALK_S = ($USERtotTALK_S * 60);
	$USERtotTALK_S = round($USERtotTALK_S, 0);
	if ($USERtotTALK_S < 10) {$USERtotTALK_S = "0$USERtotTALK_S";}
	$USERtotTALK_MS = "$USERtotTALK_M_int:$USERtotTALK_S";
	$pfUSERtotTALK_MS[$i] =		sprintf("%6s", $USERtotTALK_MS);

	$USERavgTALK_M = ($USERavgTALK / 60);
	$USERavgTALK_M = round($USERavgTALK_M, 2);
	$USERavgTALK_M_int = intval("$USERavgTALK_M");
	$USERavgTALK_S = ($USERavgTALK_M - $USERavgTALK_M_int);
	$USERavgTALK_S = ($USERavgTALK_S * 60);
	$USERavgTALK_S = round($USERavgTALK_S, 0);
	if ($USERavgTALK_S < 10) {$USERavgTALK_S = "0$USERavgTALK_S";}
	$USERavgTALK_MS = "$USERavgTALK_M_int:$USERavgTALK_S";
	$pfUSERavgTALK_MS[$i] =		sprintf("%6s", $USERavgTALK_MS);
	$i++;
	}

$k=0;
while($k < $i)
	{
	$ctA[$k]="0   "; $ctB[$k]="0   "; $ctDC[$k]="0   "; $ctDNC[$k]="0   "; $ctN[$k]="0   "; $ctNI[$k]="0   "; $ctSALE[$k]="0   "; 
	$stmt="select count(*),status from vicidial_log where call_date <= '$query_date_END' and call_date >= '$query_date_BEGIN' and user='$user[$k]' and campaign_id='" . mysql_real_escape_string($group) . "' group by status;";
	if ($non_latin > 0)
	{
	$rslt=mysql_query("SET NAMES 'UTF8'");
	}
	$rslt=mysql_query($stmt, $link);
	if ($DB) {echo "$stmt\n";}
	$rows_to_print = mysql_num_rows($rslt);
	$m=0;
	while ($m < $rows_to_print)
		{
		$row=mysql_fetch_row($rslt);
		if ($row[1] == 'A') {$ctA[$k]=sprintf("%-4s", $row[0]);		$TOT_A = ($TOT_A + $row[0]);}
		if ($row[1] == 'B') {$ctB[$k]=sprintf("%-4s", $row[0]);		$TOT_B = ($TOT_B + $row[0]);}
		if ($row[1] == 'DC') {$ctDC[$k]=sprintf("%-4s", $row[0]);	$TOT_DC = ($TOT_DC + $row[0]);}
		if ($row[1] == 'DNC') {$ctDNC[$k]=sprintf("%-4s", $row[0]);	$TOT_DNC = ($TOT_DNC + $row[0]);}
		if ($row[1] == 'N') {$ctN[$k]=sprintf("%-4s", $row[0]);		$TOT_N = ($TOT_N + $row[0]);}
		if ($row[1] == 'NI') {$ctNI[$k]=sprintf("%-4s", $row[0]);	$TOT_NI = ($TOT_NI + $row[0]);}
		if (($row[1] == 'SALE') || ($row[1] == 'XFER') ) {$ctSALE[$k]=sprintf("%-4s", $row[0]);	$TOT_SALE = ($TOT_SALE + $row[0]);}
		$m++;
		}
	echo "| $full_name[$k] | $user[$k] | $calls[$k] | $pfUSERtotTALK_MS[$k] | $pfUSERavgTALK_MS[$k] | $ctA[$k] | $ctB[$k] | $ctDC[$k] | $ctDNC[$k] | $ctN[$k] | $ctNI[$k] | $ctSALE[$k] |\n";



	$k++;
	}

	$TOTcalls =	sprintf("%-7s", $TOTcalls);

	$TOTtotTALK_M = ($TOTtotTALK / 60);
	$TOTtotTALK_M = round($TOTtotTALK_M, 2);
	$TOTtotTALK_M_int = intval("$TOTtotTALK_M");
	$TOTtotTALK_S = ($TOTtotTALK_M - $TOTtotTALK_M_int);
	$TOTtotTALK_S = ($TOTtotTALK_S * 60);
	$TOTtotTALK_S = round($TOTtotTALK_S, 0);
	if ($TOTtotTALK_S < 10) {$TOTtotTALK_S = "0$TOTtotTALK_S";}
	$TOTtotTALK_MS = "$TOTtotTALK_M_int:$TOTtotTALK_S";
	$TOTtotTALK_MS =		sprintf("%7s", $TOTtotTALK_MS);
		while(strlen($TOTtotTALK_MS)>7) {$TOTtotTALK_MS = substr("$TOTtotTALK_MS", 0, -1);}

	$TOT_A = sprintf("%-5s", $TOT_A);
	$TOT_B = sprintf("%-5s", $TOT_B);
	$TOT_DC = sprintf("%-5s", $TOT_DC);
	$TOT_DNC = sprintf("%-5s", $TOT_DNC);
	$TOT_N = sprintf("%-5s", $TOT_N);
	$TOT_NI = sprintf("%-5s", $TOT_NI);
	$TOT_SALE = sprintf("%-5s", $TOT_SALE);

echo "+-----------------+----------+--------+--------+--------+------+------+------+------+------+------+------+\n";
echo "|  TOTALS                    | $TOTcalls| $TOTtotTALK_MS|        | $TOT_A| $TOT_B| $TOT_DC| $TOT_DNC| $TOT_N| $TOT_NI| $TOT_SALE|\n";
echo "+-----------------+----------+--------+--------+--------+------+------+------+------+------+------+------+\n";

echo "\n";

}



?>

</BODY></HTML>