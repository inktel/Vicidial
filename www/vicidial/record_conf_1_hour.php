<?php
# record_conf_1_hour.php
# 
# Copyright (C) 2009  Matt Florell <vicidial@gmail.com>    LICENSE: AGPLv2
#
# grab: $server_ip $station $session_id
#
# CHANGES
#
# 60620-1011 - Added variable filtering to eliminate SQL injection attack threat
#            - Added required user/pass to gain access to this page
# 90508-0644 - Changed to PHP long tags
#

require("dbconnect.php");

$PHP_AUTH_USER=$_SERVER['PHP_AUTH_USER'];
$PHP_AUTH_PW=$_SERVER['PHP_AUTH_PW'];
$PHP_SELF=$_SERVER['PHP_SELF'];
if (isset($_GET["server_ip"]))				{$server_ip=$_GET["server_ip"];}
	elseif (isset($_POST["server_ip"]))		{$server_ip=$_POST["server_ip"];}
if (isset($_GET["station"]))				{$station=$_GET["station"];}
	elseif (isset($_POST["station"]))		{$station=$_POST["station"];}
if (isset($_GET["session_id"]))				{$session_id=$_GET["session_id"];}
	elseif (isset($_POST["session_id"]))		{$session_id=$_POST["session_id"];}
if (isset($_GET["NEW_RECORDING"]))				{$NEW_RECORDING=$_GET["NEW_RECORDING"];}
	elseif (isset($_POST["NEW_RECORDING"]))		{$NEW_RECORDING=$_POST["NEW_RECORDING"];}
if (isset($_GET["submit"]))				{$submit=$_GET["submit"];}
	elseif (isset($_POST["submit"]))		{$submit=$_POST["submit"];}
if (isset($_GET["SUBMIT"]))				{$SUBMIT=$_GET["SUBMIT"];}
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
    echo "Invalid Username/Password: |$PHP_AUTH_USER|$PHP_AUTH_PW|\n";
    exit;
	}

$STARTtime = date("U");
$TODAY = date("Y-m-d");
$MYSQL_datetime = date("Y-m-d H:i:s");
$FILE_datetime = date("Ymd-His_");
$secX = $STARTtime;

	$stmt="SELECT count(*) from vicidial_users where user='$PHP_AUTH_USER' and pass='$PHP_AUTH_PW' and user_level > 7;";
	$rslt=mysql_query($stmt, $link);
	$row=mysql_fetch_row($rslt);
	$auth=$row[0];

$fp = fopen ("./project_auth_entries.txt", "a");
$date = date("r");
$ip = getenv("REMOTE_ADDR");
$browser = getenv("HTTP_USER_AGENT");

  if( (strlen($PHP_AUTH_USER)<2) or (strlen($PHP_AUTH_PW)<2) or (!$auth))
	{
    Header("WWW-Authenticate: Basic realm=\"VICI-PROJECTS\"");
    Header("HTTP/1.0 401 Unauthorized");
    echo "Invalid Username/Password: |$PHP_AUTH_USER|$PHP_AUTH_PW|\n";
    exit;
	}
  else
	{

	if($auth>0)
		{
		$office_no=strtoupper($PHP_AUTH_USER);
		$password=strtoupper($PHP_AUTH_PW);
			$stmt="SELECT full_name from vicidial_users where user='$PHP_AUTH_USER' and pass='$PHP_AUTH_PW'";
			$rslt=mysql_query($stmt, $link);
			$row=mysql_fetch_row($rslt);
			$LOGfullname=$row[0];
		fwrite ($fp, "VICIDIAL|GOOD|$date|$PHP_AUTH_USER|$PHP_AUTH_PW|$ip|$browser|$LOGfullname|\n");
		fclose($fp);
		}
	else
		{
		fwrite ($fp, "VICIDIAL|FAIL|$date|$PHP_AUTH_USER|$PHP_AUTH_PW|$ip|$browser|\n");
		fclose($fp);
		}

	$stmt="SELECT full_name from vicidial_users where user='$user';";
	$rslt=mysql_query($stmt, $link);
	$row=mysql_fetch_row($rslt);
	$full_name = $row[0];

	}




?>
<html>
<head>
<title>VICIDIAL RECORD CONFERENCE: 1 hour</title>
<?php
echo "<META HTTP-EQUIV=\"Content-Type\" CONTENT=\"text/html; charset=utf-8\">\n";
?>
</head>
<BODY BGCOLOR=white marginheight=0 marginwidth=0 leftmargin=0 topmargin=0>
<CENTER>

<?php 
if ($NEW_RECORDING)
{
	if ( (strlen($server_ip) > 8) && (strlen($session_id) > 3) && (strlen($station) > 3) )
	{
	$local_DEF = 'Local/';
	$local_AMP = '@';
	$conf_silent_prefix = '7';
	$ext_context = 'demo';

	$stmt="INSERT INTO vicidial_manager values('','','$MYSQL_datetime','NEW','N','" . mysql_real_escape_string($server_ip) . "','','Originate','RB$FILE_datetime" . mysql_real_escape_string($station) . "','Channel: $local_DEF$conf_silent_prefix" . mysql_real_escape_string($session_id) . "$local_AMP$ext_context','Context: $ext_context','Exten: 8309','Priority: 1','Callerid: $FILE_datetime" . mysql_real_escape_string($station) . "','','','','','')";
	echo "|$stmt|\n<BR><BR>\n";
	$rslt=mysql_query($stmt, $link);

	$stmt="INSERT INTO recording_log (channel,server_ip,extension,start_time,start_epoch,filename) values('" . mysql_real_escape_string($session_id) . "','" . mysql_real_escape_string($server_ip) . "','" . mysql_real_escape_string($station) . "','$MYSQL_datetime','$secX','$FILE_datetime" . mysql_real_escape_string($station) . "')";
	echo "|$stmt|\n<BR><BR>\n";
	$rslt=mysql_query($stmt, $link);

	echo "Recording started\n<BR><BR>\n";
	echo "<a href=\"$PHP_SELF\">Back to main recording screen</a>\n<BR><BR>\n";
	}
	else
	{
	echo "ERROR!!!!    Not all info entered properly\n<BR><BR>\n";
	echo "|$server_ip| |$session_id| |$station|\n<BR><BR>\n";
	echo "<a href=\"$PHP_SELF\">Back to main recording screen</a>\n<BR><BR>\n";
	}
}
else
{
echo "<br>Start recording a conference for 1 hour: <form action=$PHP_SELF method=POST>\n";
echo "<input type=hidden name=NEW_RECORDING value=1>\n";
echo "server_ip: <input type=text name=server_ip size=15 maxlength=15> | \n";
echo "session_id: <input type=text name=session_id size=7 maxlength=7> | \n";
echo "station: <input type=text name=station size=5 maxlength=5> | \n";
echo "<input type=submit name=submit value=submit>\n";
echo "<BR><BR><BR>\n";



}
?>

</BODY></HTML>