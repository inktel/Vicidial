<?php
# call_log_display.php    version 2.2.0
# 
# Copyright (C) 2009  Matt Florell <vicidial@gmail.com>    LICENSE: AGPLv2
#
# This script is designed purely to send the inbound and outbound calls for a specific phone
# This script depends on the server_ip being sent and also needs to have a valid user/pass from the vicidial_users table
# 
# required variables:
#  - $server_ip
#  - $session_name
#  - $user
#  - $pass
# optional variables:
#  - $format - ('text','debug')
#  - $exten - ('cc101','testphone','49-1','1234','913125551212',...)
#  - $protocol - ('SIP','Zap','IAX2',...)
#  - $in_limit - ('10','20','50','100',...)
#  - $out_limit - ('10','20','50','100',...)
# 
#
# changes
# 50406-1013 - First build of script
# 50407-1452 - Added definable limits
# 50503-1236 - added session_name checking for extra security
# 50610-1158 - Added NULL check on MySQL results to reduced errors
# 50711-1202 - removed HTTP authentication in favor of user/pass vars
# 60323-1550 - added option for showing different number dialed in log
# 60421-1401 - check GET/POST vars lines with isset to not trigger PHP NOTICES
# 60619-1202 - Added variable filters to close security holes for login form
# 90508-0727 - Changed to PHP long tags
# 

require("dbconnect.php");

### If you have globals turned off uncomment these lines
if (isset($_GET["user"]))				{$user=$_GET["user"];}
	elseif (isset($_POST["user"]))		{$user=$_POST["user"];}
if (isset($_GET["pass"]))				{$pass=$_GET["pass"];}
	elseif (isset($_POST["pass"]))		{$pass=$_POST["pass"];}
if (isset($_GET["server_ip"]))				{$server_ip=$_GET["server_ip"];}
	elseif (isset($_POST["server_ip"]))		{$server_ip=$_POST["server_ip"];}
if (isset($_GET["session_name"]))				{$session_name=$_GET["session_name"];}
	elseif (isset($_POST["session_name"]))		{$session_name=$_POST["session_name"];}
if (isset($_GET["format"]))				{$format=$_GET["format"];}
	elseif (isset($_POST["format"]))		{$format=$_POST["format"];}
if (isset($_GET["exten"]))				{$exten=$_GET["exten"];}
	elseif (isset($_POST["exten"]))		{$exten=$_POST["exten"];}
if (isset($_GET["protocol"]))				{$protocol=$_GET["protocol"];}
	elseif (isset($_POST["protocol"]))		{$protocol=$_POST["protocol"];}

$user=ereg_replace("[^0-9a-zA-Z]","",$user);
$pass=ereg_replace("[^0-9a-zA-Z]","",$pass);

# default optional vars if not set
if (!isset($format))   {$format="text";}
if (!isset($in_limit))   {$in_limit="100";}
if (!isset($out_limit))   {$out_limit="100";}
$number_dialed = 'number_dialed';
#$number_dialed = 'extension';

$version = '0.0.8';
$build = '60619-1202';
$StarTtime = date("U");
$NOW_DATE = date("Y-m-d");
$NOW_TIME = date("Y-m-d H:i:s");
if (!isset($query_date)) {$query_date = $NOW_DATE;}

	$stmt="SELECT count(*) from vicidial_users where user='$user' and pass='$pass' and user_level > 0;";
	if ($DB) {echo "|$stmt|\n";}
	$rslt=mysql_query($stmt, $link);
	$row=mysql_fetch_row($rslt);
	$auth=$row[0];

  if( (strlen($user)<2) or (strlen($pass)<2) or ($auth==0))
	{
    echo "無效 Username/密碼: |$user|$pass|\n";
    exit;
	}
  else
	{

	if( (strlen($server_ip)<6) or (!isset($server_ip)) or ( (strlen($session_name)<12) or (!isset($session_name)) ) )
		{
		echo "無效 server_ip: |$server_ip|  or  無效 session_name: |$session_name|\n";
		exit;
		}
	else
		{
		$stmt="SELECT count(*) from web_client_sessions where session_name='$session_name' and server_ip='$server_ip';";
		if ($DB) {echo "|$stmt|\n";}
		$rslt=mysql_query($stmt, $link);
		$row=mysql_fetch_row($rslt);
		$SNauth=$row[0];
		  if($SNauth==0)
			{
			echo "無效 session_name: |$session_name|$server_ip|\n";
			exit;
			}
		  else
			{
			# do nothing for now
			}
		}
	}

if ($format=='debug')
{
echo "<html>\n";
echo "<head>\n";
echo "<!-- 版本: $version     版次: $build    EXTEN: $exten   server_ip: $server_ip-->\n";
echo "<title>通話記錄顯示";
echo "</title>\n";
echo "</head>\n";
echo "<BODY BGCOLOR=white marginheight=0 marginwidth=0 leftmargin=0 topmargin=0>\n";
}


	$row='';   $rowx='';
	$channel_live=1;
	if ( (strlen($exten)<1) or (strlen($protocol)<3) )
	{
	$channel_live=0;
	echo "Exten $exten 是無效的 或協定 $protocol 是無效的\n";
	exit;
	}
	else
	{
	##### print outbound calls from the call_log table
	$stmt="SELECT uniqueid,start_time,$number_dialed,length_in_sec FROM call_log where server_ip = '$server_ip' and channel LIKE \"$protocol/$exten%\" order by start_time desc limit $out_limit;";
		if ($format=='debug') {echo "\n<!-- $stmt -->";}
	$rslt=mysql_query($stmt, $link);
	if ($rslt) {$out_calls_count = mysql_num_rows($rslt);}
	echo "$out_calls_count|";
	$loop_count=0;
		while ($out_calls_count>$loop_count)
		{
		$loop_count++;
		$row=mysql_fetch_row($rslt);

		$call_time_M = ($row[3] / 60);
		$call_time_M = round($call_time_M, 2);
		$call_time_M_int = intval("$call_time_M");
		$call_time_SEC = ($call_time_M - $call_time_M_int);
		$call_time_SEC = ($call_time_SEC * 60);
		$call_time_SEC = round($call_time_SEC, 0);
		if ($call_time_SEC < 10) {$call_time_SEC = "0$call_time_SEC";}
		$call_time_MS = "$call_time_M_int:$call_time_SEC";

		if ($number_dialed == 'extension') {$row[2] = substr($row[2],-10);}
		echo "$row[0] ~$row[1] ~$row[2] ~$call_time_MS|";
		}
	echo "\n";

	##### print inbound calls from the live_inbound_log table
	$stmt="SELECT call_log.uniqueid,live_inbound_log.start_time,live_inbound_log.extension,caller_id,length_in_sec from live_inbound_log,call_log where phone_ext='$exten' and live_inbound_log.server_ip = '$server_ip' and call_log.uniqueid=live_inbound_log.uniqueid order by start_time desc limit $in_limit;";
		if ($format=='debug') {echo "\n<!-- $stmt -->";}
	$rslt=mysql_query($stmt, $link);
	if ($rslt) {$in_calls_count = mysql_num_rows($rslt);}
	echo "$in_calls_count|";
	$loop_count=0;
		while ($in_calls_count>$loop_count)
		{
		$loop_count++;
		$row=mysql_fetch_row($rslt);

		$call_time_M = ($row[4] / 60);
		$call_time_M = round($call_time_M, 2);
		$call_time_M_int = intval("$call_time_M");
		$call_time_SEC = ($call_time_M - $call_time_M_int);
		$call_time_SEC = ($call_time_SEC * 60);
		$call_time_SEC = round($call_time_SEC, 0);
		if ($call_time_SEC < 10) {$call_time_SEC = "0$call_time_SEC";}
		$call_time_MS = "$call_time_M_int:$call_time_SEC";
		$callerIDnum = $row[3];   $callerIDname = $row[3];
		$callerIDnum = preg_replace("/.*<|>.*/","",$callerIDnum);
		$callerIDname = preg_replace("/\"| <\d*>/","",$callerIDname);

		echo "$row[0] ~$row[1] ~$row[2] ~$callerIDnum ~$callerIDname ~$call_time_MS|";
		}
	echo "\n";

	}



if ($format=='debug') 
	{
	$ENDtime = date("U");
	$RUNtime = ($ENDtime - $StarTtime);
	echo "\n<!-- 運行腳本: $RUNtime 秒 -->";
	echo "\n</body>\n</html>\n";
	}
	
exit; 

?>
