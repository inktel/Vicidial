<?php
# voicemail_check.php    version 2.2.0
# 
# Copyright (C) 2009  Matt Florell <vicidial@gmail.com>    LICENSE: AGPLv2
#
# This script is designed purely to check whether the voicemail box on the server defined has new and old messages
# This script depends on the server_ip being sent and also needs to have a valid user/pass from the vicidial_users table
# 
# required variables:
#  - $server_ip
#  - $session_name
#  - $user
#  - $pass
# optional variables:
#  - $format - ('text','debug')
#  - $vmail_box - ('101','1234',...)
# 
#
# changes
# 50422-1147 - First build of script
# 50503-1241 - added session_name checking for extra security
# 50711-1201 - removed HTTP authentication in favor of user/pass vars
# 60421-1147 - check GET/POST vars lines with isset to not trigger PHP NOTICES
# 60619-1204 - Added variable filters to close security holes for login form
# 90508-0727 - Changed to PHP long tags
#

require("dbconnect.php");

### If you have globals turned off uncomment these lines
if (isset($_GET["user"]))					{$user=$_GET["user"];}
	elseif (isset($_POST["user"]))			{$user=$_POST["user"];}
if (isset($_GET["pass"]))					{$pass=$_GET["pass"];}
	elseif (isset($_POST["pass"]))			{$pass=$_POST["pass"];}
if (isset($_GET["server_ip"]))				{$server_ip=$_GET["server_ip"];}
	elseif (isset($_POST["server_ip"]))		{$server_ip=$_POST["server_ip"];}
if (isset($_GET["session_name"]))			{$session_name=$_GET["session_name"];}
	elseif (isset($_POST["session_name"]))	{$session_name=$_POST["session_name"];}
if (isset($_GET["format"]))					{$format=$_GET["format"];}
	elseif (isset($_POST["format"]))		{$format=$_POST["format"];}
if (isset($_GET["vmail_box"]))				{$vmail_box=$_GET["vmail_box"];}
	elseif (isset($_POST["vmail_box"]))		{$vmail_box=$_POST["vmail_box"];}

$user=ereg_replace("[^0-9a-zA-Z]","",$user);
$pass=ereg_replace("[^0-9a-zA-Z]","",$pass);

# default optional vars if not set
if (!isset($format))   {$format="text";}

$version = '0.0.5';
$build = '60619-1204';
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
    echo "Inválido Nombre del usuario/Contraseña: |$user|$pass|\n";
    exit;
	}
  else
	{

	if( (strlen($server_ip)<6) or (!isset($server_ip)) or ( (strlen($session_name)<12) or (!isset($session_name)) ) )
		{
		echo "Inválido server_ip: |$server_ip|  or  Inválido session_name: |$session_name|\n";
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
			echo "Inválido session_name: |$session_name|$server_ip|\n";
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
echo "<!-- VERSIÓN: $version     CONSTRUCCION: $build    VMBOX: $vmail_box   server_ip: $server_ip-->\n";
echo "<title>Comprobar el buzón de voz";
echo "</title>\n";
echo "</head>\n";
echo "<BODY BGCOLOR=white marginheight=0 marginwidth=0 leftmargin=0 topmargin=0>\n";
}

	$MT[0]='';
	$row='';   $rowx='';
	if (strlen($vmail_box)<1)
	{
	$channel_live=0;
	echo "caja del buzón de voz $vmail_box No es válido\n";
	exit;
	}
	else
	{
	$stmt="SELECT messages,old_messages FROM phones where server_ip='$server_ip' and voicemail_id='$vmail_box' limit 1;";
		if ($format=='debug') {echo "\n<!-- $stmt -->";}
	$rslt=mysql_query($stmt, $link);
	$vmails_list = mysql_num_rows($rslt);
	$loop_count=0;
		while ($vmails_list>$loop_count)
		{
		$loop_count++;
		$row=mysql_fetch_row($rslt);
		echo "$row[0]|$row[1]";
		if ($format=='debug') {echo "\n<!-- $row[0]     $row[1] -->";}
		}
	}


if ($format=='debug') 
	{
	$ENDtime = date("U");
	$RUNtime = ($ENDtime - $StarTtime);
	echo "\n<!-- tiempo de ejecución del Script: $RUNtime segundos -->";
	echo "\n</body>\n</html>\n";
	}
	
exit; 

?>
