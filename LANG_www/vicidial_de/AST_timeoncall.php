<? 
### AST_timeoncall.php
### 
### Copyright (C) 2006  Matt Florell <vicidial@gmail.com>    LICENSE: GPLv2
###

require("dbconnect.php");

$PHP_AUTH_USER=$_SERVER['PHP_AUTH_USER'];
$PHP_AUTH_PW=$_SERVER['PHP_AUTH_PW'];
$PHP_SELF=$_SERVER['PHP_SELF'];
if (isset($_GET["server_ip"]))				{$server_ip=$_GET["server_ip"];}
	elseif (isset($_POST["server_ip"]))		{$server_ip=$_POST["server_ip"];}
if (isset($_GET["reset_counter"]))				{$reset_counter=$_GET["reset_counter"];}
	elseif (isset($_POST["reset_counter"]))		{$reset_counter=$_POST["reset_counter"];}
if (isset($_GET["submit"]))				{$submit=$_GET["submit"];}
	elseif (isset($_POST["submit"]))		{$submit=$_POST["submit"];}
if (isset($_GET["SUBMIT"]))				{$SUBMIT=$_GET["SUBMIT"];}
	elseif (isset($_POST["SUBMIT"]))		{$SUBMIT=$_POST["SUBMIT"];}

$NOW_TIME = date("Y-m-d H:i:s");
$STARTtime = date("U");

?>

<HTML>
<HEAD>
<STYLE type="text/css">
<!--
   .green {color: white; background-color: green}
   .red {color: white; background-color: red}
-->
 </STYLE>

<? 
echo "<META HTTP-EQUIV=\"Content-Type\" CONTENT=\"text/html; charset=utf-8\">\n";
echo"<META HTTP-EQUIV=Refresh CONTENT=\"15; URL=$PHP_SELF?server_ip=$server_ip&DB=$DB\">\n";
echo "<TITLE>VICIDIAL: Time On Call</TITLE></HEAD><BODY BGCOLOR=WHITE>\n";
echo "<PRE>\n\n";

echo "VICIDIAL: Time On Call                                          $NOW_TIME\n\n";
echo "+------------+-----------+-----------+------------------+---------------------+---------+\n";
echo "| STATION    | SESSIONID | CHANNEL   | NUMBER DIALED    | START TIME          | MINUTES |\n";
echo "+------------+-----------+-----------+------------------+---------------------+---------+\n";

$stmt="SELECT count(*) from live_sip_channels where server_ip='$server_ip';";
$rslt=mysql_query($stmt, $link);
if ($DB) {echo "$stmt\n";}
$row=mysql_fetch_row($rslt);
$parked_count = $row[0];
	if ($parked_count > 0)
	{
	$stmt="select extension from live_channels where server_ip='$server_ip';";
	$rslt=mysql_query($stmt, $link);
	if ($DB) {echo "$stmt\n";}
	$ext_to_print = mysql_num_rows($rslt);
	$i=0;
	$live_zap_counter=0;
	$sessions = '';
	while ($i < $ext_to_print)
		{
		$row=mysql_fetch_row($rslt);
		if (preg_match("/^8600/i",$row[0]))
			{
			$sessions .= "$row[0]|";
			}
		$i++;
		}
	$sessions = preg_replace("/\|$/",'',$sessions);

	if ($DB) {echo "SESSIONS: -$sessions-\n";}

	$stmt="select * from live_sip_channels where server_ip='$server_ip' order by channel;";
	$rslt=mysql_query($stmt, $link);
	if ($DB) {echo "$stmt\n";}
	$parked_to_print = mysql_num_rows($rslt);
	$i=0;
	$live_calls_counter=0;
	while ($i < $parked_to_print)
		{
		$row=mysql_fetch_row($rslt);
		if ( (preg_match("/Zap\/|internal|ring/i",$row[3])) or (preg_match("/Local\//i",$row[0])) )
			{
			if ($DB) {echo "NOT STATION |$row[0]|$row[3]|\n";}
			$session_id = '';
			}
		else 
			{
			if (preg_match("/$row[3]/i",$sessions))
				{
				if ($DB) {echo "LIVE STATION|$row[0]|$row[3]|\n";}
				$session_id = $row[3];
				$station = preg_replace("/SIP\//",'',$row[0]);
				$station = preg_replace("/-.*/",'',$station);
			
				$LIVE_sessions[$live_calls_counter] =			"$session_id";
				$LIVE_sessions_FORMAT[$live_calls_counter] =	sprintf("%-9s", $session_id);
				$LIVE_stations[$live_calls_counter] =			"$station";
				$LIVE_stations_FORMAT[$live_calls_counter] =	sprintf("%-10s", $station);

		#		echo "| $station | $session_id | \n";

				$live_calls_counter++;
				}
			else
				{
				if ($DB) {echo "NOT STATIONZ|$row[0]|$row[3]|\n";}
				$session_id = '';
				}
			}

		$i++;
		}

		if ($live_calls_counter > 0)
		{
		$i=0;
		$LC_counter = $live_calls_counter;

		while ($i < $live_calls_counter)
			{
			$stmt="select channel,extension,number_dialed,start_time,start_epoch from call_log where extension='$LIVE_stations[$i]' and server_ip='$server_ip' order by uniqueid desc LIMIT 1;";
			$rslt=mysql_query($stmt, $link);
			if ($DB) {echo "$stmt\n";}
			$parked_to_print = mysql_num_rows($rslt);
			$row=mysql_fetch_row($rslt);

			$channel =			sprintf("%-9s", $row[0]);
			$number_dialed =	sprintf("%-16s", $row[2]);
			$start_time =		sprintf("%-19s", $row[3]);
			$call_time_S = ($STARTtime - $row[4]);

			$call_time_M = ($call_time_S / 60);
			$call_time_M = round($call_time_M, 2);
			$call_time_M_int = intval("$call_time_M");
			$call_time_SEC = ($call_time_M - $call_time_M_int);
			$call_time_SEC = ($call_time_SEC * 60);
			$call_time_SEC = round($call_time_SEC, 0);
			if ($call_time_SEC < 10) {$call_time_SEC = "0$call_time_SEC";}
			$call_time_MS = "$call_time_M_int:$call_time_SEC";
			$call_time_MS =		sprintf("%7s", $call_time_MS);
			$G = '';		$EG = '';
			if ($call_time_M_int >= 12) {$G='<SPAN class="green"><B>'; $EG='</B></SPAN>';}
			if ($call_time_M_int >= 31) {$G='<SPAN class="red"><B>'; $EG='</B></SPAN>';}

			echo "| $G$LIVE_stations_FORMAT[$i]$EG | $G$LIVE_sessions_FORMAT[$i]$EG | $G$channel$EG | $G$number_dialed$EG | $G$start_time$EG | $G$call_time_MS$EG |\n";

			$i++;
			}

		echo "+------------+-----------+-----------+------------------+---------------------+---------+\n";
		echo "  $i stations on live calls on server $server_ip\n\n";

		echo "  <SPAN class=\"green\"><B>          </SPAN> - 12 minutes or more on live call\n";
		echo "  <SPAN class=\"red\"><B>          </SPAN> - Over 30 minutes on live call\n";

		}
		else
		{
		echo "*****************************************************************************************\n";
		echo "*****************************************************************************************\n";
		echo "*************************************** NO LIVE STATIONS ********************************\n";
		echo "*****************************************************************************************\n";
		echo "*****************************************************************************************\n";
		}

	}
	else
	{
	echo "*****************************************************************************************\n";
	echo "*****************************************************************************************\n";
	echo "*************************************** NO LIVE STATIONS ********************************\n";
	echo "*****************************************************************************************\n";
	echo "*****************************************************************************************\n";
	}

?>
</PRE>

</BODY></HTML>