<?php
# closer.php
# 
# Copyright (C) 2009  Matt Florell <vicidial@gmail.com>    LICENSE: AGPLv2
#
# the purpose of this script and webpage is to allow for remote or local users of the system to log in and grab phone calls that are coming inbound into the Asterisk server and being put in the parked_channels table while they hear a soundfile for a limited amount of time before being forwarded on to either a set extension or a voicemail box. This gives remote or local agents a way to grab calls without tying up their phone lines all day. The agent sees the refreshing screen of calls on park and when they want to take one they just click on it, and a small window opens that will allow them to grab the call and/or look up more information on the caller through the callerID that is given(if available)
# CHANGES
#
# 60620-1032 - Added variable filtering to eliminate SQL injection attack threat
#            - Added required user/pass to gain access to this page
# 90508-0644 - Changed to PHP long tags
#

require("dbconnect.php");

$PHP_AUTH_USER=$_SERVER['PHP_AUTH_USER'];
$PHP_AUTH_PW=$_SERVER['PHP_AUTH_PW'];
$PHP_SELF=$_SERVER['PHP_SELF'];
if (isset($_GET["group"]))				{$group=$_GET["group"];}
	elseif (isset($_POST["group"]))		{$group=$_POST["group"];}
if (isset($_GET["group_selected"]))				{$group_selected=$_GET["group_selected"];}
	elseif (isset($_POST["group_selected"]))		{$group_selected=$_POST["group_selected"];}
if (isset($_GET["dialplan_number"]))				{$dialplan_number=$_GET["dialplan_number"];}
	elseif (isset($_POST["dialplan_number"]))		{$dialplan_number=$_POST["dialplan_number"];}
if (isset($_GET["extension"]))				{$extension=$_GET["extension"];}
	elseif (isset($_POST["extension"]))		{$extension=$_POST["extension"];}
if (isset($_GET["groupselect"]))				{$groupselect=$_GET["groupselect"];}
	elseif (isset($_POST["groupselect"]))		{$groupselect=$_POST["groupselect"];}
if (isset($_GET["PHONE_LOGIN"]))				{$PHONE_LOGIN=$_GET["PHONE_LOGIN"];}
	elseif (isset($_POST["PHONE_LOGIN"]))		{$PHONE_LOGIN=$_POST["PHONE_LOGIN"];}
if (isset($_GET["server_ip"]))				{$server_ip=$_GET["server_ip"];}
	elseif (isset($_POST["server_ip"]))		{$server_ip=$_POST["server_ip"];}
if (isset($_GET["DB"]))				{$DB=$_GET["DB"];}
	elseif (isset($_POST["DB"]))		{$DB=$_POST["DB"];}
if (isset($_GET["user"]))				{$user=$_GET["user"];}
	elseif (isset($_POST["user"]))		{$user=$_POST["user"];}
if (isset($_GET["channel"]))				{$channel=$_GET["channel"];}
	elseif (isset($_POST["channel"]))		{$channel=$_POST["channel"];}
if (isset($_GET["parked_time"]))				{$parked_time=$_GET["parked_time"];}
	elseif (isset($_POST["parked_time"]))		{$parked_time=$_POST["parked_time"];}
if (isset($_GET["channel_group"]))				{$channel_group=$_GET["channel_group"];}
	elseif (isset($_POST["channel_group"]))		{$channel_group=$_POST["channel_group"];}
if (isset($_GET["debugvars"]))				{$debugvars=$_GET["debugvars"];}
	elseif (isset($_POST["debugvars"]))		{$debugvars=$_POST["debugvars"];}
if (isset($_GET["parked_by"]))				{$parked_by=$_GET["parked_by"];}
	elseif (isset($_POST["parked_by"]))		{$parked_by=$_POST["parked_by"];}
if (isset($_GET["submit"]))				{$submit=$_GET["submit"];}
	elseif (isset($_POST["submit"]))		{$submit=$_POST["submit"];}
if (isset($_GET["SUBMIT"]))				{$SUBMIT=$_GET["SUBMIT"];}
	elseif (isset($_POST["SUBMIT"]))		{$SUBMIT=$_POST["SUBMIT"];}

$PHP_AUTH_USER = ereg_replace("[^0-9a-zA-Z]","",$PHP_AUTH_USER);
$PHP_AUTH_PW = ereg_replace("[^0-9a-zA-Z]","",$PHP_AUTH_PW);

$STARTtime = date("U");
$TODAY = date("Y-m-d");
$NOW_TIME = date("Y-m-d H:i:s");
$popup_page = './closer_popup.php';

	$stmt="SELECT count(*) from vicidial_users where user='$PHP_AUTH_USER' and pass='$PHP_AUTH_PW' and user_level > 2;";
	$rslt=mysql_query($stmt, $link);
	$row=mysql_fetch_row($rslt);
	$auth=$row[0];

$fp = fopen ("./project_auth_entries.txt", "a");
$date = date("r");
$ip = getenv("REMOTE_ADDR");
$browser = getenv("HTTP_USER_AGENT");

  if( (strlen($PHP_AUTH_USER)<2) or (strlen($PHP_AUTH_PW)<2) or (!$auth))
	{
    Header("WWW-Authenticate: Basic realm=\"VICIDIAL-CLOSER\"");
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
			if ($DB) {echo "$stmt\n";}
			$rslt=mysql_query($stmt, $link);
			$row=mysql_fetch_row($rslt);
			$LOGfullname=$row[0];
			$fullname = $row[0];
		fwrite ($fp, "VD_CLOSER|GOOD|$date|$PHP_AUTH_USER|$PHP_AUTH_PW|$ip|$browser|$LOGfullname|\n");
		fclose($fp);

		}
	else
		{
		fwrite ($fp, "VD_CLOSER|FAIL|$date|$PHP_AUTH_USER|$PHP_AUTH_PW|$ip|$browser|\n");
		fclose($fp);
		}
	}


$color_class[0] = 'green';
$color_class[1] = 'red';
$color_class[2] = 'blue';
$color_class[3] = 'purple';
$color_class[4] = 'orange';
$color_class[5] = 'green';
$color_class[6] = 'red';
$color_class[7] = 'blue';
$color_class[8] = 'purple';
$color_class[9] = 'orange';
?>

<HTML>
<HEAD>
<STYLE type="text/css">
<!--
   A:link {color: yellow}
   .green {color: white; background-color: green}
   .red {color: white; background-color: red}
   .blue {color: white; background-color: blue}
   .purple {color: white; background-color: purple}
   .orange {color: white; background-color: orange}
-->
 </STYLE>

<TITLE>VICIDIAL CLOSER: Main</TITLE></HEAD>
</HEAD>
<BODY BGCOLOR=white marginheight=0 marginwidth=0 leftmargin=0 topmargin=0>
<CENTER><FONT FACE="Courier" COLOR=BLACK SIZE=3>

<?php 
$group_selected = count($groupselect);

if (!$dialplan_number)
{

	if ($extension)
	{
	$stmt="SELECT count(*) from phones where extension='" . mysql_real_escape_string($extension) . "';";
	$rslt=mysql_query($stmt, $link);
	$row=mysql_fetch_row($rslt);
	$ext_found=$row[0];
		if ($ext_found > 0)
		{
		$stmt="SELECT dialplan_number,server_ip from phones where extension='" . mysql_real_escape_string($extension) . "';";
		$rslt=mysql_query($stmt, $link);
		$row=mysql_fetch_row($rslt);
		$dialplan_number=$row[0];
		$server_ip=$row[1];

		if (!$group_selected)
			{
			$stmt="select distinct channel_group from park_log;";
			$rslt=mysql_query($stmt, $link);
			if ($DB) {echo "$stmt\n";}
			$groups_to_print = mysql_num_rows($rslt);
			$i=0;
			while ($i < $groups_to_print)
				{
				$row=mysql_fetch_row($rslt);
				$groups[$i] =$row[0];
				$i++;
				}
			echo "<br>Please select the groups you want to pick calls up from: <form action=$PHP_SELF method=GET>\n";
			echo "<table border=0><tr><td align=left>\n";
			echo "<input type=hidden name=PHONE_LOGIN value=1>\n";
			echo "<input type=hidden name=dialplan_number value=\"$dialplan_number\">\n";
			echo "<input type=hidden name=extension value=\"$extension\">\n";
			echo "<input type=hidden name=server_ip value=\"$server_ip\">\n";
			echo "<input type=hidden name=DB value=\"$DB\">\n";
			echo "Inbound Call Groups: <br>\n";
				$o=0;
				while ($groups_to_print > $o)
				{
					$group_form_print =		sprintf("%-20s", $groups[$o]);
					echo "<input name=\"groupselect[]\" type=checkbox value=\"$groups[$o]\">$group_form_print <BR>\n";
					$o++;
				}
			echo "</td></tr></table>\n";
			echo "<input type=submit name=submit value=submit>\n";
			echo "<BR><BR><BR>\n";
			}
		else
			{
			$o=0;
			while($o < $group_selected)
			{
				$form_groups = "$form_groups&groupselect[$o]=$groupselect[$o]";
				$o++;
			}

			$stmt="INSERT INTO vicidial_user_log values('','" . mysql_real_escape_string($user) . "','LOGIN','CLOSER','$NOW_TIME','$STARTtime');";
			$rslt=mysql_query($stmt, $link);

			echo "<META HTTP-EQUIV=\"Content-Type\" CONTENT=\"text/html; charset=utf-8\">\n";
			echo"<META HTTP-EQUIV=Refresh CONTENT=\"3; URL=$PHP_SELF?dialplan_number=$dialplan_number&server_ip=$server_ip&extension=$extension&DB=$DB$form_groups\">\n";

			echo "<font=green><b>extension found, forwarding you to closer page, please wait 3 seconds...</b></font>\n";
			}

		}
		else
		{
		echo "<font=red><b>The extension you entered does not exist, please try again</b></font>\n";
		echo "<br>Please enter your phone_ID: <form action=$PHP_SELF method=POST>\n";
		echo "<input type=hidden name=PHONE_LOGIN value=1>\n";
		echo "phone station ID: <input type=text name=extension size=10 maxlength=10 value=\"$extension\"> &nbsp; \n";
		echo "<input type=submit name=submit value=submit>\n";
		echo "<BR><BR><BR>\n";
		}

	}
	else
	{
	echo "<br>Please enter your phone_ID: <form action=$PHP_SELF method=POST>\n";
	echo "<input type=hidden name=PHONE_LOGIN value=1>\n";
	echo "phone station ID: <input type=text name=extension size=10 maxlength=10> &nbsp; \n";
	echo "<input type=submit name=submit value=submit>\n";
	echo "<BR><BR><BR>\n";
	}

exit;

}

$o=0;
while($o < $group_selected)
{
	$listen_groups = "$listen_groups &nbsp; &nbsp; $groupselect[$o]";
	$form_groups = "$form_groups&groupselect[$o]=$groupselect[$o]";
	$o++;
}


echo "<META HTTP-EQUIV=\"Content-Type\" CONTENT=\"text/html; charset=utf-8\">\n";
echo"<META HTTP-EQUIV=Refresh CONTENT=\"5; URL=$PHP_SELF?dialplan_number=$dialplan_number&server_ip=$server_ip&extension=$extension&DB=$DB$form_groups\">\n";

#CHANNEL    SERVER        CHANNEL_GROUP   EXTENSION    PARKED_BY            PARKED_TIME        
#----------------------------------------------------------------------------------------------
#Zap/73-1   10.10.11.11   IN_800_TPP_CS   TPPpark      7275338730           2004-04-22 12:41:00



echo "$NOW_TIME &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; $PHP_AUTH_USER - $fullname -  CALLS SENT TO: $dialplan_number<BR><BR>\n";

echo "Click on the channel below that you would like to have directed to your phone<BR>\n";

echo "<PRE>\n";
echo "CHANNEL    SERVER        CHANNEL_GROUP   EXTENSION    PARKED_BY            PARKED_TIME         \n";
echo "---------------------------------------------------------------------------------------------- \n";





$stmt="SELECT count(*) from parked_channels where server_ip='" . mysql_real_escape_string($server_ip) . "'";
$rslt=mysql_query($stmt, $link);
if ($DB) {echo "$stmt\n";}
$row=mysql_fetch_row($rslt);
$parked_count = $row[0];
	if ($parked_count > 0)
	{
	$stmt="SELECT channel,server_ip,channel_group,extension,parked_by,parked_time from parked_channels where server_ip='" . mysql_real_escape_string($server_ip) . "' and channel_group LIKE \"CL_%\" order by channel_group,parked_time";
	$rslt=mysql_query($stmt, $link);
	if ($DB) {echo "$stmt\n";}
	$parked_to_print = mysql_num_rows($rslt);
	$i=0;
	while ($i < $parked_to_print)
		{
		$row=mysql_fetch_row($rslt);
		$channel =			sprintf("%-11s", $row[0]);
		$server =			sprintf("%-14s", $row[1]);
		$channel_group =	sprintf("%-16s", $row[2]);
		$park_extension =	sprintf("%-13s", $row[3]);
		$parked_by =		sprintf("%-21s", $row[4]);
		$parked_time =		sprintf("%-19s", $row[5]);

		$G='';   $EG='';
		$o=0;
		$with_color=0;
		while($o < $group_selected)
			{
				if (eregi("$groupselect[$o]",$channel_group))
					{
					$G="<SPAN class=\"$color_class[$o]\"><B>"; $EG='</B></SPAN>';
					$with_color++;
					}
				$o++;
			}
		if ($with_color)
			{
			if (eregi('CL_GAL',$row[2]))
				{
				echo "$G<A HREF=\"$popup_page?channel=$row[0]&server_ip=$server_ip&parked_time=$row[5]&dialplan_number=$dialplan_number&extension=$extension&channel_group=$row[2]&DB=$DB&debugvars=asdfuiywer786sdg786sfg7sdsgjhg352j3452hg45f2j3h4g5f2j3h4g5f2jhg4f5j3fg45jh23f5j4h23gf&parked_by=$row[4]&debugvars=asdfuiywer786sdg786sfg7sdsgjhg352j3452hg45f2j3h4g5f2j3h4g5f2jhg4f5j3fg45jh23f5j4h23gf\" target=\"_blank\">$channel</A>$server$channel_group$park_extension                     $parked_time $EG\n";
				}
			else
				{
				echo "$G<A HREF=\"$popup_page?channel=$row[0]&server_ip=$server_ip&parked_time=$row[5]&dialplan_number=$dialplan_number&extension=$extension&channel_group=$row[2]&DB=$DB&debugvars=asdfuiywer786sdg786sfg7sdsgjhg352j3452hg45f2j3h4g5f2j3h4g5f2jhg4f5j3fg45jh23f5j4h23gf&parked_by=$row[4]\" target=\"_blank\">$channel</A>$server$channel_group$park_extension$parked_by$parked_time $EG\n";
				}
			}
#	
		$i++;
		}

	}
	else
	{
	echo "********************************************************************************************** \n";
	echo "********************************************************************************************** \n";
	echo "*************************************** NO PARKED CALLS ************************************** \n";
	echo "********************************************************************************************** \n";
	echo "********************************************************************************************** \n";
	}

echo "  \n\n";
echo "looking for calls on these groups:\n";
$o=0;
while($o < $group_selected)
{
	$group_print =		sprintf("%-20s", $groupselect[$o]);
	echo "  <SPAN class=\"$color_class[$o]\"><B>          </SPAN> - $group_print</B>\n";
	$o++;
}


$ENDtime = date("U");

$RUNtime = ($ENDtime - $STARTtime);

echo "</PRE>\n\n\n<br><br><br>\n\n";


echo "<font size=0>\n\n\n<br><br><br>\nscript runtime: $RUNtime seconds</font>";


?>


</body>
</html>

<?php
	
exit; 



?>





