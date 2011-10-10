<?php
# live_exten_check.php    version 2.2.0
# 
# Copyright (C) 2009  Matt Florell <vicidial@gmail.com>    LICENSE: AGPLv2
#
# This script is designed purely to send whether the client channel is live and to what channel it is connected
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
# 
#
# changes
# 50404-1249 - First build of script
# 50406-1402 - added connected trunk lookup
# 50428-1452 - added live_inbound check for exten on 2nd line of output
# 50503-1233 - added session_name checking for extra security
# 50524-1429 - added parked calls count
# 50610-1204 - Added NULL check on MySQL results to reduced errors
# 50711-1204 - removed HTTP authentication in favor of user/pass vars
# 60103-1541 - added favorite extens status display
# 60421-1359 - check GET/POST vars lines with isset to not trigger PHP NOTICES
# 60619-1203 - Added variable filters to close security holes for login form
# 60825-1029 - Fixed translation variable issue ChannelA
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
if (isset($_GET["favorites_count"]))				{$favorites_count=$_GET["favorites_count"];}
	elseif (isset($_POST["favorites_count"]))		{$favorites_count=$_POST["favorites_count"];}
if (isset($_GET["favorites_list"]))				{$favorites_list=$_GET["favorites_list"];}
	elseif (isset($_POST["favorites_list"]))		{$favorites_list=$_POST["favorites_list"];}

$user=ereg_replace("[^0-9a-zA-Z]","",$user);
$pass=ereg_replace("[^0-9a-zA-Z]","",$pass);

# default optional vars if not set
if (!isset($format))   {$format="text";}

$version = '2.0.1';
$build = '60825-1029';
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
    echo "Ongeldig Gebruikersnaam/Wachtwoord: |$user|$pass|\n";
    exit;
	}
  else
	{

	if( (strlen($server_ip)<6) or (!isset($server_ip)) or ( (strlen($session_name)<12) or (!isset($session_name)) ) )
		{
		echo "Ongeldig server_ip: |$server_ip|  or  Ongeldig session_name: |$session_name|\n";
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
			echo "Ongeldig session_name: |$session_name|$server_ip|\n";
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
echo "<!-- VERSIE: $version     BUILD: $build    EXTEN: $exten   server_ip: $server_ip-->\n";
echo "<title>Live Extentie Controle";
echo "</title>\n";
echo "</head>\n";
echo "<BODY BGCOLOR=white marginheight=0 marginwidth=0 leftmargin=0 topmargin=0>\n";
}


echo "DateTime: $NOW_TIME|";
echo "UnixTime: $StarTtime|";

$stmt="SELECT count(*) FROM parked_channels where server_ip = '$server_ip';";
	if ($format=='debug') {echo "\n<!-- $stmt -->";}
$rslt=mysql_query($stmt, $link);
$row=mysql_fetch_row($rslt);
echo "$row[0]|";

	$MT[0]='';
	$row='';   $rowx='';
	$channel_live=1;
	if ( (strlen($exten)<1) or (strlen($protocol)<3) )
	{
	$channel_live=0;
	echo "Exten $exten is niet geldig of protocol $protocol is niet geldig\n";
	exit;
	}
	else
	{
	$stmt="SELECT channel,extension FROM live_sip_channels where server_ip = '$server_ip' and channel LIKE \"$protocol/$exten%\";";
		if ($format=='debug') {echo "\n<!-- $stmt -->";}
	$rslt=mysql_query($stmt, $link);
	if ($rslt) {$channels_list = mysql_num_rows($rslt);}
	echo "$channels_list|";
	$loop_count=0;
		while ($channels_list>$loop_count)
		{
		$loop_count++;
		$row=mysql_fetch_row($rslt);
		$ChanneLA[$loop_count] = "$row[0]";
		$ChanneLB[$loop_count] = "$row[1]";
		if ($format=='debug') {echo "\n<!-- $row[0]     $row[1] -->";}
		}
	}

	$counter=0;
	while($loop_count > $counter)
	{
		$counter++;
	$stmt="SELECT channel FROM live_channels where server_ip = '$server_ip' and channel_data = '$ChanneLA[$counter]';";
		if ($format=='debug') {echo "\n<!-- $stmt -->";}
	$rslt=mysql_query($stmt, $link);
	if ($rslt) {$trunk_count = mysql_num_rows($rslt);}
		if ($trunk_count>0)
		{
		$row=mysql_fetch_row($rslt);
		echo "Conversation: $counter ~";
		echo "ChannelA: $ChanneLA[$counter] ~";
		echo "ChannelB: $ChanneLB[$counter] ~";
		echo "ChannelBtrunk: $row[0]|";
		}
		else
		{
		$stmt="SELECT channel FROM live_sip_channels where server_ip = '$server_ip' and channel_data = '$ChanneLA[$counter]';";
			if ($format=='debug') {echo "\n<!-- $stmt -->";}
		$rslt=mysql_query($stmt, $link);
		if ($rslt) {$trunk_count = mysql_num_rows($rslt);}
			if ($trunk_count>0)
			{
			$row=mysql_fetch_row($rslt);
			echo "Conversation: $counter ~";
			echo "ChannelA: $ChanneLA[$counter] ~";
			echo "ChannelB: $ChanneLB[$counter] ~";
			echo "ChannelBtrunk: $row[0]|";
			}
			else
			{
			$stmt="SELECT channel FROM live_sip_channels where server_ip = '$server_ip' and channel LIKE \"$ChanneLB[$counter]%\";";
				if ($format=='debug') {echo "\n<!-- $stmt -->";}
			$rslt=mysql_query($stmt, $link);
			if ($rslt) {$trunk_count = mysql_num_rows($rslt);}
				if ($trunk_count>0)
				{
				$row=mysql_fetch_row($rslt);
				echo "Conversation: $counter ~";
				echo "ChannelA: $ChanneLA[$counter] ~";
				echo "ChannelB: $ChanneLB[$counter] ~";
				echo "ChannelBtrunk: $row[0]|";
				}
				else
				{
				$stmt="SELECT channel FROM live_channels where server_ip = '$server_ip' and channel LIKE \"$ChanneLB[$counter]%\";";
					if ($format=='debug') {echo "\n<!-- $stmt -->";}
				$rslt=mysql_query($stmt, $link);
				if ($rslt) {$trunk_count = mysql_num_rows($rslt);}
					if ($trunk_count>0)
					{
					$row=mysql_fetch_row($rslt);
					echo "Conversation: $counter ~";
					echo "ChannelA: $ChanneLA[$counter] ~";
					echo "ChannelB: $ChanneLB[$counter] ~";
					echo "ChannelBtrunk: $row[0]|";
					}
					else
					{
					echo "Conversation: $counter ~";
					echo "ChannelA: $ChanneLA[$counter] ~";
					echo "ChannelB: $ChanneLB[$counter] ~";
					echo "ChannelBtrunk: $ChanneLA[$counter]|";
					}
				}
			}
		}
	}

echo "\n";

### check for live_inbound entry
$stmt="select * from live_inbound where server_ip = '$server_ip' and phone_ext = '$exten' and acknowledged='N';";
	if ($format=='debug') {echo "\n<!-- $stmt -->";}
$rslt=mysql_query($stmt, $link);
if ($rslt) {$channels_list = mysql_num_rows($rslt);}
	if ($channels_list>0)
	{
	$row=mysql_fetch_row($rslt);
	$LIuniqueid = "$row[0]";
	$LIchannel = "$row[1]";
	$LIcallerid = "$row[3]";
	$LIdatetime = "$row[6]";
	echo "$LIuniqueid|$LIchannel|$LIcallerid|$LIdatetime|$row[8]|$row[9]|$row[10]|$row[11]|$row[12]|$row[13]|";
	if ($format=='debug') {echo "\n<!-- $row[0]|$row[1]|$row[2]|$row[3]|$row[4]|$row[5]|$row[6]|$row[7]|$row[8]|$row[9]|$row[10]|$row[11]|$row[12]|$row[13]| -->";}
	}

echo "\n";


### if favorites are present do a lookup to see if any are active
if ($favorites_count > 0)
	{
	$favorites = explode(',',$favorites_list);
	$h=0;
	$favs_print='';
	while ($favorites_count > $h)
		{
		$fav_extension = explode('/',$favorites[$h]);
		$stmt="SELECT count(*) FROM live_sip_channels where server_ip = '$server_ip' and channel LIKE \"$favorites[$h]%\";";
			if ($format=='debug') {echo "\n<!-- $stmt -->";}
		$rslt=mysql_query($stmt, $link);
		$row=mysql_fetch_row($rslt);
		$favs_print .= "$fav_extension[1]: $row[0] ~";
		$h++;
		}
	echo "$favs_print\n";
	}

if ($format=='debug') {echo "\n<!-- |$favorites_count|$favorites_list| -->";}


if ($format=='debug') 
	{
	$ENDtime = date("U");
	$RUNtime = ($ENDtime - $StarTtime);
	echo "\n<!-- script looptijd: $RUNtime seconden -->";
	echo "\n</body>\n</html>\n";
	}
	
exit; 

?>
