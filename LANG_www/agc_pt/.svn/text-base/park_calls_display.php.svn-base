<?php
# park_calls_display.php    version 2.2.0
# 
# Copyright (C) 2009  Matt Florell <vicidial@gmail.com>    LICENSE: AGPLv2
#
# This script is designed purely to send the details on the parked calls on the server
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
# 50524-1515 - First build of script
# 50711-1208 - removed HTTP authentication in favor of user/pass vars
# 60421-1111 - check GET/POST vars lines with isset to not trigger PHP NOTICES
# 60619-1205 - Added variable filters to close security holes for login form
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
if (isset($_GET["exten"]))					{$exten=$_GET["exten"];}
	elseif (isset($_POST["exten"]))			{$exten=$_POST["exten"];}
if (isset($_GET["protocol"]))				{$protocol=$_GET["protocol"];}
	elseif (isset($_POST["protocol"]))		{$protocol=$_POST["protocol"];}

$user=ereg_replace("[^0-9a-zA-Z]","",$user);
$pass=ereg_replace("[^0-9a-zA-Z]","",$pass);

# default optional vars if not set
if (!isset($format))		{$format="text";}
if (!isset($park_limit))	{$park_limit="1000";}

$version = '0.0.4';
$build = '60619-1205';
$StarTtime = date("U");
$NOW_DATE = date("Y-m-d");
$NOW_TIME = date("Y-m-d H:i:s");
if (!isset($query_date)) {$query_date = $NOW_DATE;}

	$stmt="SELECT count(*) from vicidial_users where user='$user' and pass='$pass' and user_level > 0;";
	if ($DB) {echo "|$stmt|\n";}
	$rslt=mysql_query($stmt, $link);
	$row=mysql_fetch_row($rslt);
	$auth=$row[0];

  if( (strlen($user)<2) or (strlen($pass)<2) or ($auth==0) )
	{
    echo "Inválido Username/Senha: |$user|$pass|\n";
    exit;
	}
  else
	{

	if( (strlen($server_ip)<6) or (!isset($server_ip)) or ( (strlen($session_name)<12) or (!isset($session_name)) ) ) 		{
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
echo "<!-- VERSÃO: $version     CONFIGURAÇÃO: $build    EXTEN: $exten   server_ip: $server_ip-->\n";
echo "<title>Exposição Estacionada Das Chamadas";
echo "</title>\n";
echo "</head>\n";
echo "<BODY BGCOLOR=white marginheight=0 marginwidth=0 leftmargin=0 topmargin=0>\n";
}


	$row='';   $rowx='';
	$channel_live=1;
	if ( (strlen($exten)<1) or (strlen($protocol)<3) )
	{
	$channel_live=0;
	echo "Exten $exten é inválido ou protocolo $protocol é inválido\n";
	exit;
	}
	else
	{
	##### print parked calls from the parked_channels table
	$stmt="SELECT channel,server_ip,channel_group,extension,parked_by,parked_time from parked_channels where server_ip = '$server_ip' order by parked_time limit $park_limit;";
		if ($format=='debug') {echo "\n<!-- $stmt -->";}
	$rslt=mysql_query($stmt, $link);
	$park_calls_count = mysql_num_rows($rslt);
	echo "$park_calls_count\n";
	$loop_count=0;
		while ($park_calls_count>$loop_count)
		{
		$loop_count++;
		$row=mysql_fetch_row($rslt);
		echo "$row[0] ~$row[2] ~$row[3] ~$row[4] ~$row[5]|";
		}
	echo "\n";
	}



if ($format=='debug') 
	{
	$ENDtime = date("U");
	$RUNtime = ($ENDtime - $StarTtime);
	echo "\n<!-- runtime do certificado: $RUNtime segundos -->";
	echo "\n</body>\n</html>\n";
	}
	
exit; 

?>
