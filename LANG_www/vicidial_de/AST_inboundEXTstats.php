<?php 
# AST_inboundEXTstats.php
# 
# Copyright (C) 2009  Matt Florell <vicidial@gmail.com>    LICENSE: AGPLv2
#
# CHANGES
# 60421-1450 - check GET/POST vars lines with isset to not trigger PHP NOTICES
# 60620-1322 - Added variable filtering to eliminate SQL injection attack threat
#            - Added required user/pass to gain access to this page
# 90508-0644 - Changed to PHP long tags
#

require("dbconnect.php");

$PHP_AUTH_USER=$_SERVER['PHP_AUTH_USER'];
$PHP_AUTH_PW=$_SERVER['PHP_AUTH_PW'];
$PHP_SELF=$_SERVER['PHP_SELF'];
if (isset($_GET["group"]))					{$group=$_GET["group"];}
	elseif (isset($_POST["group"]))			{$group=$_POST["group"];}
if (isset($_GET["query_date"]))				{$query_date=$_GET["query_date"];}
	elseif (isset($_POST["query_date"]))	{$query_date=$_POST["query_date"];}
if (isset($_GET["server_ip"]))				{$server_ip=$_GET["server_ip"];}
	elseif (isset($_POST["server_ip"]))		{$server_ip=$_POST["server_ip"];}
if (isset($_GET["submit"]))					{$submit=$_GET["submit"];}
	elseif (isset($_POST["submit"]))		{$submit=$_POST["submit"];}
if (isset($_GET["SUBMIT"]))					{$SUBMIT=$_GET["SUBMIT"];}
	elseif (isset($_POST["SUBMIT"]))		{$SUBMIT=$_POST["SUBMIT"];}

$PHP_AUTH_USER = ereg_replace("[^0-9a-zA-Z]","",$PHP_AUTH_USER);
$PHP_AUTH_PW = ereg_replace("[^0-9a-zA-Z]","",$PHP_AUTH_PW);

	$stmt="SELECT count(*) from vicidial_users where user='$PHP_AUTH_USER' and pass='$PHP_AUTH_PW' and user_level > 6 and view_reports='1';";
	if ($DB) {echo "|$stmt|\n";}
	$rslt=mysql_query($stmt, $link);
	$row=mysql_fetch_row($rslt);
	$auth=$row[0];

  if( (strlen($PHP_AUTH_USER)<2) or (strlen($PHP_AUTH_PW)<2) or (!$auth))
	{
    Header("WWW-Authenticate: Basic realm=\"VICI-PROJECTS\"");
    Header("HTTP/1.0 401 Unauthorized");
    echo "Unzulässiges Username/Kennwort:|$PHP_AUTH_USER|$PHP_AUTH_PW|\n";
    exit;
	}


$NOW_DATE = date("Y-m-d");
$NOW_TIME = date("Y-m-d H:i:s");
$STARTtime = date("U");
if (!isset($query_date)) {$query_date = $NOW_DATE;}
if (!isset($server_ip)) {$server_ip = '10.10.11.20';}

$stmt="select extension,full_number,inbound_name from inbound_numbers where server_ip='" . mysql_real_escape_string($server_ip) . "';";
$rslt=mysql_query($stmt, $link);
if ($DB) {echo "$stmt\n";}
$inbound_to_print = mysql_num_rows($rslt);
$i=0;
while ($i < $inbound_to_print)
	{
	$row=mysql_fetch_row($rslt);
	$inbound[$i] =$row[0];
	$fullnum[$i] =$row[1];
	$inbname[$i] =$row[2];
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
echo"<META HTTP-EQUIV=\"Content-Type\" CONTENT=\"text/html; charset=utf-8\">\n";
#echo"<META HTTP-EQUIV=Refresh CONTENT=\"7; URL=$PHP_SELF?server_ip=$server_ip&DB=$DB\">\n";
echo "<TITLE>ASTERISK: Inbound Anruf-Notfall</TITLE></HEAD><BODY BGCOLOR=WHITE>\n";
echo "<FORM ACTION=\"$PHP_SELF\" METHOD=GET>\n";
echo "<INPUT TYPE=HIDDEN NAME=server_ip VALUE=\"$server_ip\">\n";
echo "<INPUT TYPE=TEXT NAME=query_date SIZE=10 MAXLENGTH=10 VALUE=\"$query_date\">\n";
echo "<SELECT SIZE=1 NAME=group>\n";
	$o=0;
	while ($inbound_to_print > $o)
	{
		if ($inbound[$o] == $group) {echo "<option selected value=\"$inbound[$o]\">$fullnum[$o] - $inbname[$o]</option>\n";}
		  else {echo "<option value=\"$inbound[$o]\">$fullnum[$o] - $inbname[$o]</option>\n";}
		$o++;
	}
echo "</SELECT>\n";
echo "<INPUT TYPE=Submit NAME=SUBMIT VALUE=SUBMIT>\n";
echo "</FORM>\n\n";

echo "<PRE><FONT SIZE=2>\n\n";


if (!$group)
{
echo "\n\n";
echo "WÄHLEN Sie BITTE Eine ZAHL Vor UND DATUM OBEN UND KLICKEN REICHT Ein\n";
}

else
{


echo "ASTERISK: Inbound Anruf-Notfall                      $NOW_TIME\n";

echo "\n";
echo "---------- TOTALS\n";

$extenSQL = "and extension='" . mysql_real_escape_string($group) . "'";
if (eregi("\*",$group))
	{$extenSQL = "and extension LIKE \"%$group\"";}
$stmt="select count(*),sum(length_in_sec) from call_log where start_time >= '" . mysql_real_escape_string($query_date) . " 00:00:01' and start_time <= '" . mysql_real_escape_string($query_date) . " 23:59:59' and server_ip='" . mysql_real_escape_string($server_ip) . "' $extenSQL ;";
$rslt=mysql_query($stmt, $link);
if ($DB) {echo "$stmt\n";}
$row=mysql_fetch_row($rslt);

$TOTALcalls =	sprintf("%10s", $row[0]);
$average_hold_seconds = ($row[1] / $row[0]);
$average_hold_seconds = round($average_hold_seconds, 0);
$average_hold_seconds =	sprintf("%10s", $average_hold_seconds);

echo "Gesamtanrufe, die in diese Zahl kamen:       $TOTALcalls\n";
echo "Average Call Length(seconds) for all Calls:   $average_hold_seconds\n";

echo "\n";
echo "---------- DROPS\n";

$stmt="select count(*),sum(length_in_sec) from call_log where start_time >= '" . mysql_real_escape_string($query_date) . " 00:00:01' and start_time <= '" . mysql_real_escape_string($query_date) . " 23:59:59' and server_ip='" . mysql_real_escape_string($server_ip) . "' $extenSQL and (length_in_sec <= 10 or length_in_sec is null);";
$rslt=mysql_query($stmt, $link);
if ($DB) {echo "$stmt\n";}
$row=mysql_fetch_row($rslt);

$DROPcalls =	sprintf("%10s", $row[0]);
$DROPpercent = (($DROPcalls / $TOTALcalls) * 100);
$DROPpercent = round($DROPpercent, 0);

if ($row[0])
	{
	$average_hold_seconds = ($row[1] / $row[0]);
	$average_hold_seconds = round($average_hold_seconds, 0);
	$average_hold_seconds =	sprintf("%10s", $average_hold_seconds);
	}
else {$DROPpercent=0;   $average_hold_seconds=0;}
echo "Total DROP Calls:   (less than 10 seconds)    $DROPcalls  $DROPpercent%\n";
echo "Average Call Length(seconds) for DROP Calls:  $average_hold_seconds\n";


##############################
#########  CALLS STATS

echo "\n";
echo "---------- CALL LISTINGS\n";
echo "+----------------------+----------------------+--------+---------------------+\n";
echo "| CALLERID             | CALLERIDNAME         | LENGTH | DATE TIME           |\n";
echo "+----------------------+----------------------+--------+---------------------+\n";

$stmt="select number_dialed,caller_code,length_in_sec,start_time from call_log where start_time >= '" . mysql_real_escape_string($query_date) . " 00:00:01' and start_time <= '" . mysql_real_escape_string($query_date) . " 23:59:59' and server_ip='" . mysql_real_escape_string($server_ip) . "' $extenSQL ;";
$rslt=mysql_query($stmt, $link);
if ($DB) {echo "$stmt\n";}
$users_to_print = mysql_num_rows($rslt);
$i=0;
while ($i < $users_to_print)
	{
	$row=mysql_fetch_row($rslt);

	$CID =			sprintf("%-20s", $row[0]);
	$CIDname =		sprintf("%-20s", $row[1]); while(strlen($full_name)>15) {$full_name = substr("$full_name", 0, -1);}
	$datetime =		sprintf("%-19s", $row[3]);
	$USERavgTALK =	$row[2];

	$USERavgTALK_M = ($USERavgTALK / 60);
	$USERavgTALK_M = round($USERavgTALK_M, 2);
	$USERavgTALK_M_int = intval("$USERavgTALK_M");
	$USERavgTALK_S = ($USERavgTALK_M - $USERavgTALK_M_int);
	$USERavgTALK_S = ($USERavgTALK_S * 60);
	$USERavgTALK_S = round($USERavgTALK_S, 0);
	if ($USERavgTALK_S < 10) {$USERavgTALK_S = "0$USERavgTALK_S";}
	$USERavgTALK_MS = "$USERavgTALK_M_int:$USERavgTALK_S";
	$USERavgTALK_MS =		sprintf("%6s", $USERavgTALK_MS);

	echo "| $CID | $CIDname | $USERavgTALK_MS | $datetime |\n";

	$i++;
	}

echo "+----------------------+----------------------+--------+---------------------+\n";

##############################
#########  TIME STATS

if ($output == 'FULL')
	{

	echo "\n";
	echo "---------- TIME STATS\n";

	echo "<FONT SIZE=0>\n";

	$hi_hour_count=0;
	$last_full_record=0;
	$i=0;
	$h=0;
	while ($i <= 96)
		{
		$stmt="select count(*) from call_log where start_time >= '" . mysql_real_escape_string($query_date) . " $h:00:00' and start_time <= '" . mysql_real_escape_string($query_date) . " $h:14:59' and server_ip='" . mysql_real_escape_string($server_ip) . "' $extenSQL ;";
		$rslt=mysql_query($stmt, $link);
		if ($DB) {echo "$stmt\n";}
		$row=mysql_fetch_row($rslt);
		$hour_count[$i] = $row[0];
		if ($hour_count[$i] > $hi_hour_count) {$hi_hour_count = $hour_count[$i];}
		if ($hour_count[$i] > 0) {$last_full_record = $i;}
		$stmt="select count(*) from call_log where start_time >= '" . mysql_real_escape_string($query_date) . " $h:00:00' and start_time <= '" . mysql_real_escape_string($query_date) . " $h:14:59' and server_ip='" . mysql_real_escape_string($server_ip) . "' $extenSQL and (length_in_sec <= 10 or length_in_sec is null);";
		$rslt=mysql_query($stmt, $link);
		if ($DB) {echo "$stmt\n";}
		$row=mysql_fetch_row($rslt);
		$drop_count[$i] = $row[0];
		$i++;


		$stmt="select count(*) from call_log where start_time >= '" . mysql_real_escape_string($query_date) . " $h:15:00' and start_time <= '" . mysql_real_escape_string($query_date) . " $h:29:59' and server_ip='" . mysql_real_escape_string($server_ip) . "' $extenSQL ;";
		$rslt=mysql_query($stmt, $link);
		if ($DB) {echo "$stmt\n";}
		$row=mysql_fetch_row($rslt);
		$hour_count[$i] = $row[0];
		if ($hour_count[$i] > $hi_hour_count) {$hi_hour_count = $hour_count[$i];}
		if ($hour_count[$i] > 0) {$last_full_record = $i;}
		$stmt="select count(*) from call_log where start_time >= '" . mysql_real_escape_string($query_date) . " $h:15:00' and start_time <= '" . mysql_real_escape_string($query_date) . " $h:29:59' and server_ip='" . mysql_real_escape_string($server_ip) . "' $extenSQL and (length_in_sec <= 10 or length_in_sec is null);";
		$rslt=mysql_query($stmt, $link);
		if ($DB) {echo "$stmt\n";}
		$row=mysql_fetch_row($rslt);
		$drop_count[$i] = $row[0];
		$i++;

		$stmt="select count(*) from call_log where start_time >= '" . mysql_real_escape_string($query_date) . " $h:30:00' and start_time <= '" . mysql_real_escape_string($query_date) . " $h:44:59' and server_ip='" . mysql_real_escape_string($server_ip) . "' $extenSQL ;";
		$rslt=mysql_query($stmt, $link);
		if ($DB) {echo "$stmt\n";}
		$row=mysql_fetch_row($rslt);
		$hour_count[$i] = $row[0];
		if ($hour_count[$i] > $hi_hour_count) {$hi_hour_count = $hour_count[$i];}
		if ($hour_count[$i] > 0) {$last_full_record = $i;}
		$stmt="select count(*) from call_log where start_time >= '" . mysql_real_escape_string($query_date) . " $h:30:00' and start_time <= '" . mysql_real_escape_string($query_date) . " $h:44:59' and server_ip='" . mysql_real_escape_string($server_ip) . "' $extenSQL and (length_in_sec <= 10 or length_in_sec is null);";
		$rslt=mysql_query($stmt, $link);
		if ($DB) {echo "$stmt\n";}
		$row=mysql_fetch_row($rslt);
		$drop_count[$i] = $row[0];
		$i++;

		$stmt="select count(*) from call_log where start_time >= '" . mysql_real_escape_string($query_date) . " $h:45:00' and start_time <= '" . mysql_real_escape_string($query_date) . " $h:59:59' and server_ip='" . mysql_real_escape_string($server_ip) . "' $extenSQL ;";
		$rslt=mysql_query($stmt, $link);
		if ($DB) {echo "$stmt\n";}
		$row=mysql_fetch_row($rslt);
		$hour_count[$i] = $row[0];
		if ($hour_count[$i] > $hi_hour_count) {$hi_hour_count = $hour_count[$i];}
		if ($hour_count[$i] > 0) {$last_full_record = $i;}
		$stmt="select count(*) from call_log where start_time >= '" . mysql_real_escape_string($query_date) . " $h:45:00' and start_time <= '" . mysql_real_escape_string($query_date) . " $h:59:59' and server_ip='" . mysql_real_escape_string($server_ip) . "' $extenSQL and (length_in_sec <= 10 or length_in_sec is null);";
		$rslt=mysql_query($stmt, $link);
		if ($DB) {echo "$stmt\n";}
		$row=mysql_fetch_row($rslt);
		$drop_count[$i] = $row[0];
		$i++;
		$h++;
		}

	$hour_multiplier = (100 / $hi_hour_count);
	#$hour_multiplier = round($hour_multiplier, 0);

	echo "<!-- HICOUNT: $hi_hour_count|$hour_multiplier -->\n";
	echo "DIAGRAMM IN 15 MINUZIÖSEN STUFENSPRÜNGEN VON GESAMTANRUFEN\n";

	$k=1;
	$Mk=0;
	$call_scale = '0';
	while ($k <= 102) 
		{
		if ($Mk >= 5) 
			{
			$Mk=0;
			$scale_num=($k / $hour_multiplier);
			$scale_num = round($scale_num, 0);
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


	}




}



?>
</PRE>

</BODY></HTML>