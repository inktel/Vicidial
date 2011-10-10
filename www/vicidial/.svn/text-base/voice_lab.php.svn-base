<?php
# voice_lab.php
# 
# This script is designed to broadcast a recorded message or allow a person to
# speak to all agents logged into a VICIDIAL campaign.
# 
# Copyright (C) 2009  Matt Florell <vicidial@gmail.com>    LICENSE: AGPLv2
#
#
# CHANGES
#
# 61220-1050 - First Build
# 70115-1246 - Added ability to define an exten to play
# 90508-0644 - Changed to PHP long tags
#

require("dbconnect.php");

$PHP_AUTH_USER=$_SERVER['PHP_AUTH_USER'];
$PHP_AUTH_PW=$_SERVER['PHP_AUTH_PW'];
$PHP_SELF=$_SERVER['PHP_SELF'];
if (isset($_GET["server_ip"]))				{$server_ip=$_GET["server_ip"];}
	elseif (isset($_POST["server_ip"]))		{$server_ip=$_POST["server_ip"];}
if (isset($_GET["message"]))				{$message=$_GET["message"];}
	elseif (isset($_POST["message"]))		{$message=$_POST["message"];}
if (isset($_GET["session_id"]))				{$session_id=$_GET["session_id"];}
	elseif (isset($_POST["session_id"]))	{$session_id=$_POST["session_id"];}
if (isset($_GET["campaign_id"]))			{$campaign_id=$_GET["campaign_id"];}
	elseif (isset($_POST["campaign_id"]))	{$campaign_id=$_POST["campaign_id"];}
if (isset($_GET["NEW_VOICE_LAB"]))			{$NEW_VOICE_LAB=$_GET["NEW_VOICE_LAB"];}
	elseif (isset($_POST["NEW_VOICE_LAB"]))	{$NEW_VOICE_LAB=$_POST["NEW_VOICE_LAB"];}
if (isset($_GET["KILL_VOICE_LAB"]))				{$KILL_VOICE_LAB=$_GET["KILL_VOICE_LAB"];}
	elseif (isset($_POST["KILL_VOICE_LAB"]))	{$KILL_VOICE_LAB=$_POST["KILL_VOICE_LAB"];}
if (isset($_GET["PLAY_MESSAGE"]))			{$PLAY_MESSAGE=$_GET["PLAY_MESSAGE"];}
	elseif (isset($_POST["PLAY_MESSAGE"]))	{$PLAY_MESSAGE=$_POST["PLAY_MESSAGE"];}
if (isset($_GET["submit"]))				{$submit=$_GET["submit"];}
	elseif (isset($_POST["submit"]))	{$submit=$_POST["submit"];}
if (isset($_GET["SUBMIT"]))				{$SUBMIT=$_GET["SUBMIT"];}
	elseif (isset($_POST["SUBMIT"]))	{$SUBMIT=$_POST["SUBMIT"];}

$PHP_AUTH_USER = ereg_replace("[^0-9a-zA-Z]","",$PHP_AUTH_USER);
$PHP_AUTH_PW = ereg_replace("[^0-9a-zA-Z]","",$PHP_AUTH_PW);

$STARTtime = date("U");
$TODAY = date("Y-m-d");
$MYSQL_datetime = date("Y-m-d H:i:s");
$FILE_datetime = date("Ymd-His_");
$secX = $STARTtime;

$local_DEF = 'Local/';
$local_AMP = '@';
$ext_context = 'demo';

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


##### get server listing for dynamic pulldown
$stmt="SELECT server_ip,server_description from servers order by server_ip";
$rslt=mysql_query($stmt, $link);
$servers_to_print = mysql_num_rows($rslt);
$servers_list='';

$o=0;
while ($servers_to_print > $o)
	{
	$rowx=mysql_fetch_row($rslt);
	$servers_list .= "<option value=\"$rowx[0]\">$rowx[0] - $rowx[1]</option>\n";
	$o++;
	}

##### get campaigns listing for dynamic pulldown
$stmt="SELECT campaign_id,campaign_name from vicidial_campaigns order by campaign_id";
$rslt=mysql_query($stmt, $link);
$campaigns_to_print = mysql_num_rows($rslt);
$campaigns_list='';

$o=0;
while ($campaigns_to_print > $o)
	{
	$rowx=mysql_fetch_row($rslt);
	$campaigns_list .= "<option value=\"$rowx[0]\">$rowx[0] - $rowx[1]</option>\n";
	$o++;
	}




?>
<html>
<head>
<title>VICIDIAL VOICE LAB: Admin</title>
<?php
echo "<META HTTP-EQUIV=\"Content-Type\" CONTENT=\"text/html; charset=utf-8\">\n";
?>
</head>
<BODY BGCOLOR=white marginheight=0 marginwidth=0 leftmargin=0 topmargin=0>
<CENTER>

<?php 
if ($NEW_VOICE_LAB > 0)
{
	if ( (strlen($server_ip) > 6) && (strlen($session_id) > 6) && (strlen($campaign_id) > 2) )
	{
	echo "<br><br><br>TO START YOUR VOICE LAB, DIAL 9$session_id ON YOUR PHONE NOW<br>\n";

	echo "<br>or, you can enter an extension that you want played below<form action=$PHP_SELF method=POST>\n";
	echo "<input type=hidden name=PLAY_MESSAGE value=2>\n";
	echo "<input type=hidden name=session_id value=8600900>\n";
	echo "<input type=hidden name=server_ip value=$server_ip>\n";
	echo "<input type=hidden name=campaign_id value=$campaign_id>\n";
	echo "Message Extension<input type=text name=message>\n";
	echo "<input type=submit name=submit value='PLAY THIS MESSAGE'>\n";
	echo "</form><BR><BR><BR>\n";
	
	$S='*';
	$D_s_ip = explode('.', $server_ip);
	if (strlen($D_s_ip[0])<2) {$D_s_ip[0] = "0$D_s_ip[0]";}
	if (strlen($D_s_ip[0])<3) {$D_s_ip[0] = "0$D_s_ip[0]";}
	if (strlen($D_s_ip[1])<2) {$D_s_ip[1] = "0$D_s_ip[1]";}
	if (strlen($D_s_ip[1])<3) {$D_s_ip[1] = "0$D_s_ip[1]";}
	if (strlen($D_s_ip[2])<2) {$D_s_ip[2] = "0$D_s_ip[2]";}
	if (strlen($D_s_ip[2])<3) {$D_s_ip[2] = "0$D_s_ip[2]";}
	if (strlen($D_s_ip[3])<2) {$D_s_ip[3] = "0$D_s_ip[3]";}
	if (strlen($D_s_ip[3])<3) {$D_s_ip[3] = "0$D_s_ip[3]";}
	$remote_dialstring = "$D_s_ip[0]$S$D_s_ip[1]$S$D_s_ip[2]$S$D_s_ip[3]$S$session_id";

	$thirty_minutes_old = mktime(date("H"), date("i"), date("s")-30, date("m"), date("d"),  date("Y"));
	$past_thirty = date("Y-m-d H:i:s",$thirty_minutes_old);

	$stmt="SELECT conf_exten,server_ip,user from vicidial_live_agents where last_update_time > '$past_thirty' and campaign_id='$campaign_id';";
	$rslt=mysql_query($stmt, $link);
	$agents_to_loop = mysql_num_rows($rslt);
	$agents_sessions[0]='';
	$agents_servers[0]='';
	$agents_users[0]='';

	$o=0;
	while ($agents_to_loop > $o)
		{
		$rowx=mysql_fetch_row($rslt);
		$agents_sessions[$o] = "$rowx[0]";
		$agents_servers[$o] = "$rowx[1]";
		$agents_users[$o] = "$rowx[2]";
		$o++;
		}

	$o=0;
	while ($agents_to_loop > $o)
		{
		if ($agents_servers[$o] == "$server_ip") 
			{$dial_string = $session_id;}
		else
			{$dial_string = $remote_dialstring;}

		$stmt="INSERT INTO vicidial_manager values('','','$MYSQL_datetime','NEW','N','$agents_servers[$o]','','Originate','VL$FILE_datetime$o','Channel: $local_DEF$dial_string$local_AMP$ext_context','Context: $ext_context','Exten: $agents_sessions[$o]','Priority: 1','Callerid: VL$FILE_datetime$o','','','','','')";
		echo "|$stmt|\n<BR><BR>\n";
		$rslt=mysql_query($stmt, $link);

		echo "LOGGED IN USER $agents_users[$o] at session $agents_sessions[$o] on server $agents_servers[$o]\n";

		$o++;
		}



	echo "<br>Kill a Voice Lab Session: 8600900<form action=$PHP_SELF method=POST>\n";
	echo "<input type=hidden name=KILL_VOICE_LAB value=2>\n";
	echo "<input type=hidden name=session_id value=8600900>\n";
	echo "<input type=hidden name=server_ip value=$server_ip>\n";
	echo "<input type=hidden name=campaign_id value=$campaign_id>\n";
	echo "<input type=submit name=submit value='KILL THIS VOICE LAB'>\n";
	echo "</form><BR><BR><BR>\n";
	}
	else
	{
	echo "ERROR!!!!    Not all info entered properly\n<BR><BR>\n";
	echo "|$server_ip| |$session_id| |$campaign_id|\n<BR><BR>\n";
	echo "<a href=\"$PHP_SELF\">Back to main voicelab screen</a>\n<BR><BR>\n";
	}
}
else
{
	if ($PLAY_MESSAGE > 0)
	{
		if ( (strlen($server_ip) > 6) && (strlen($session_id) > 6) && (strlen($campaign_id) > 2) && (strlen($message) > 0) )
		{
		echo "<br><br><br>TO START YOUR VOICE LAB, DIAL 9$session_id ON YOUR PHONE NOW<br>\n";

		echo "<br>or, you can enter an extension that you want played below<form action=$PHP_SELF method=POST>\n";
		echo "<input type=hidden name=PLAY_MESSAGE value=2>\n";
		echo "<input type=hidden name=session_id value=8600900>\n";
		echo "<input type=hidden name=server_ip value=$server_ip>\n";
		echo "<input type=hidden name=campaign_id value=$campaign_id>\n";
		echo "Message Extension<input type=text name=message>\n";
		echo "<input type=submit name=submit value='PLAY THIS MESSAGE'>\n";
		echo "</form><BR><BR><BR>\n";
		
		$nn='99';
		$n='9';

		$stmt="INSERT INTO vicidial_manager values('','','$MYSQL_datetime','NEW','N','$server_ip','','Originate','VL$FILE_datetime$nn','Channel: $local_DEF$n$session_id$local_AMP$ext_context','Context: $ext_context','Exten: $message','Priority: 1','Callerid: VL$FILE_datetime$nn','','','','','')";
		echo "|$stmt|\n<BR><BR>\n";
		$rslt=mysql_query($stmt, $link);

		echo "MESSAGE $message played at session $session_id on server $server_ip\n";

		echo "<br>Kill a Voice Lab Session: 8600900<form action=$PHP_SELF method=POST>\n";
		echo "<input type=hidden name=KILL_VOICE_LAB value=2>\n";
		echo "<input type=hidden name=session_id value=8600900>\n";
		echo "<input type=hidden name=server_ip value=$server_ip>\n";
		echo "<input type=hidden name=campaign_id value=$campaign_id>\n";
		echo "<input type=submit name=submit value='KILL THIS VOICE LAB'>\n";
		echo "</form><BR><BR><BR>\n";
		}
		else
		{
		echo "ERROR!!!!    Not all info entered properly\n<BR><BR>\n";
		echo "|$server_ip| |$session_id| |$campaign_id|\n<BR><BR>\n";
		echo "<a href=\"$PHP_SELF\">Back to main voicelab screen</a>\n<BR><BR>\n";
		}
	}
else
	{
	echo "<br>Start a Voice Lab Session: 8600900<form action=$PHP_SELF method=POST>\n";
	echo "<input type=hidden name=NEW_VOICE_LAB value=2>\n";
	echo "<input type=hidden name=session_id value=8600900>\n";
	echo "Your Server: <select size=1 name=server_ip>$servers_list</select>\n";
	echo "<BR>\n";
	echo "Campaign: <select size=1 name=campaign_id>$campaigns_list</select>";
	echo "<BR>\n";
	echo "<input type=submit name=submit value=submit>\n";
	echo "</form><BR><BR><BR>\n";


	echo "<br>Kill a Voice Lab Session: 8600900<form action=$PHP_SELF method=POST>\n";
	echo "<input type=hidden name=KILL_VOICE_LAB value=2>\n";
	echo "<input type=hidden name=session_id value=8600900>\n";
	echo "Your Server: <select size=1 name=server_ip>$servers_list</select>\n";
	echo "<BR>\n";
	echo "Campaign: <select size=1 name=campaign_id>$campaigns_list</select>";
	echo "<BR>\n";
	echo "<input type=submit name=submit value=submit>\n";
	echo "</form><BR><BR><BR>\n";
	}
}


if ($KILL_VOICE_LAB > 1)
{
	if ( (strlen($server_ip) > 6) && (strlen($session_id) > 6) && (strlen($campaign_id) > 2) )
	{
	$kill_dial_string = "5555$session_id";
	$hangup_exten='8300';
	$stmt="INSERT INTO vicidial_manager values('','','$MYSQL_datetime','NEW','N','$server_ip','','Originate','VLK$FILE_datetime','Channel: $local_DEF$kill_dial_string$local_AMP$ext_context','Context: $ext_context','Exten: $hangup_exten','Priority: 1','Callerid: VLK$FILE_datetime','','','','','')";
	echo "|$stmt|\n<BR><BR>\n";
	$rslt=mysql_query($stmt, $link);

	echo "VOICELAB SESSION KILLED: $session_id at $server_ip | $KILL_VOICE_LAB\n";
	}
	else
	{
	echo "ERROR!!!!    Not all info entered properly\n<BR><BR>\n";
	echo "|$server_ip| |$session_id| |$campaign_id|\n<BR><BR>\n";
	echo "<a href=\"$PHP_SELF\">Back to main voicelab screen</a>\n<BR><BR>\n";
	}

}

?>

</BODY></HTML>