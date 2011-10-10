<?php 
# AST_timeonVDAD.php
# 
# Copyright (C) 2008  Matt Florell <vicidial@gmail.com>    LICENSE: AGPLv2
#
# live real-time stats for the VICIDIAL Auto-Dialer
#
# CHANGES
#
# 60620-1037 - Added variable filtering to eliminate SQL injection attack threat
#            - Added required user/pass to gain access to this page
# 61114-2004 - Changed to display CLOSER and DEFAULT, added trunk shortage
# 80422-0305 - Added phone login to display, lower font size to 2
# 81013-2227 - Fixed Remote Agent display bug
# 90310-1945 - Admin header
# 90508-0644 - Changed to PHP long tags
#

header ("Content-type: text/html; charset=utf-8");

require("dbconnect.php");

$PHP_AUTH_USER=$_SERVER['PHP_AUTH_USER'];
$PHP_AUTH_PW=$_SERVER['PHP_AUTH_PW'];
$PHP_SELF=$_SERVER['PHP_SELF'];
if (isset($_GET["server_ip"]))				{$server_ip=$_GET["server_ip"];}
	elseif (isset($_POST["server_ip"]))		{$server_ip=$_POST["server_ip"];}
if (isset($_GET["reset_counter"]))			{$reset_counter=$_GET["reset_counter"];}
	elseif (isset($_POST["reset_counter"]))	{$reset_counter=$_POST["reset_counter"];}
if (isset($_GET["submit"]))					{$submit=$_GET["submit"];}
	elseif (isset($_POST["submit"]))		{$submit=$_POST["submit"];}
if (isset($_GET["VALIDER"]))					{$VALIDER=$_GET["VALIDER"];}
	elseif (isset($_POST["VALIDER"]))		{$VALIDER=$_POST["VALIDER"];}
if (isset($_GET["closer_display"]))				{$closer_display=$_GET["closer_display"];}
	elseif (isset($_POST["closer_display"]))	{$closer_display=$_POST["closer_display"];}

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
    echo "Login ou mot de passe invalide: |$PHP_AUTH_USER|$PHP_AUTH_PW|\n";
    exit;
	}

$NOW_TIME = date("Y-m-d H:i:s");
$STARTtime = date("U");
$epochSIXhoursAGO = ($STARTtime - 21600);
$timeSIXhoursAGO = date("Y-m-d H:i:s",$epochSIXhoursAGO);

$reset_counter++;

if ($reset_counter > 7)
	{
	$reset_counter=0;

	$stmt="update park_log set status='HUNGUP' where hangup_time is not null;";
#	$rslt=mysql_query($stmt, $link);
	if ($DB) {echo "$stmt\n";}

	if ($DB)
		{	
		$stmt="delete from park_log where grab_time < '$timeSIXhoursAGO' and (hangup_time is null or hangup_time='');";
#		$rslt=mysql_query($stmt, $link);
		 echo "$stmt\n";
		}
	}

?>

<HTML>
<HEAD>
<?php
echo "<STYLE type=\"text/css\">\n";
echo "<!--\n";

if ($closer_display>0)
{
	$stmt="select group_id,group_color from vicidial_inbound_groups;";
	$rslt=mysql_query($stmt, $link);
	if ($DB) {echo "$stmt\n";}
	$groups_to_print = mysql_num_rows($rslt);
		if ($groups_to_print > 0)
		{
		$g=0;
		while ($g < $groups_to_print)
			{
			$row=mysql_fetch_row($rslt);
			$group_id[$g] = $row[0];
			$group_color[$g] = $row[1];
			echo "   .$group_id[$g] {color: black; background-color: $group_color[$g]}\n";
			$g++;
			}
		}
}
?>
   .DEAD       {color: white; background-color: black}
   .green {color: white; background-color: green}
   .red {color: white; background-color: red}
   .blue {color: white; background-color: blue}
   .purple {color: white; background-color: purple}
   .yellow {color: black; background-color: yellow}
-->
 </STYLE>

<?php 
echo "<META HTTP-EQUIV=\"Content-Type\" CONTENT=\"text/html; charset=utf-8\">\n";
echo"<META HTTP-EQUIV=Refresh CONTENT=\"4; URL=$PHP_SELF?server_ip=$server_ip&DB=$DB&reset_counter=$reset_counter&closer_display=$closer_display\">\n";
echo "<TITLE>Server-Specific Real-Time Report</TITLE></HEAD><BODY BGCOLOR=WHITE marginheight=0 marginwidth=0 leftmargin=0 topmargin=0>\n";

$short_header=1;

require("admin_header.php");

echo "<TABLE CELLPADDING=4 CELLSPACING=0><TR><TD>";

echo "<PRE><FONT SIZE=2>";

###################################################################################
###### SERVER INFORMATION
###################################################################################

$stmt="select sum(local_trunk_shortage) from vicidial_campaign_server_stats where server_ip='" . mysql_real_escape_string($server_ip) . "';";
$rslt=mysql_query($stmt, $link);
$row=mysql_fetch_row($rslt);
$balanceSHORT = $row[0];

echo "SERVER: $server_ip\n";



###################################################################################
###### TIME ON SYSTEM
###################################################################################

if ($closer_display>0) {$closer_display_reverse=0;   $closer_reverse_link='DEFAULT';}
else {$closer_display_reverse=1;   $closer_reverse_link='CLOSER';}

echo "Agents Time On Calls           $NOW_TIME    <a href=\"$PHP_SELF?server_ip=$server_ip&DB=$DB&reset_counter=$reset_counter&closer_display=$closer_display_reverse\">$closer_reverse_link</a> | <a href=\"./admin.php?ADD=999999\">RAPPORTS</a>\n\n";

if ($closer_display>0)
{
echo "+------------+------------+--------+-----------+---------------------+--------+----------+---------+--------------+--------+\n";
echo "| STATION    | PHONE      | USER   | SESSIONID | CHANNEL             | STATUS | CALLTIME | MINUTES | CAMPAIGN     | FRONT  |\n";
echo "+------------+------------+--------+-----------+---------------------+--------+----------+---------+--------------+--------+\n";
}
else
{
echo "+------------+------------+--------+-----------+---------------------+--------+----------+---------+\n";
echo "| STATION    | PHONE      | USER   | SESSIONID | CHANNEL             | STATUS | CALLTIME | MINUTES |\n";
echo "+------------+------------+--------+-----------+---------------------+--------+----------+---------+\n";
}

$stmt="select extension,user,conf_exten,channel,status,last_call_time,UNIX_TIMESTAMP(last_call_time),UNIX_TIMESTAMP(last_call_finish),uniqueid,lead_id from vicidial_live_agents where status NOT IN('PAUSED') and server_ip='" . mysql_real_escape_string($server_ip) . "' order by extension;";
$rslt=mysql_query($stmt, $link);
if ($DB) {echo "$stmt\n";}
$talking_to_print = mysql_num_rows($rslt);
	if ($talking_to_print > 0)
	{
	$i=0;
	while ($i < $talking_to_print)
		{
		$row=mysql_fetch_row($rslt);
		$Sextension[$i] =		$row[0];
		$Suser[$i] =			$row[1];
		$Ssessionid[$i] =		$row[2];
		$Schannel[$i] =			$row[3];
		$Sstatus[$i] =			$row[4];
		$Sstart_time[$i] =		$row[5];
		$Scall_time[$i] =		$row[6];
		$Sfinish_time[$i] =		$row[7];
		$Suniqueid[$i] =		$row[8];
		$Slead_id[$i] =			$row[9];
		$i++;
		}

	$i=0;
	while ($i < $talking_to_print)
		{
		$phone[$i]='          ';
		if (eregi("R/",$Sextension[$i])) 
			{
			$protocol = 'EXTERNAL';
			$dialplan = eregi_replace('R/',"",$Sextension[$i]);
			$dialplan = eregi_replace("\@.*",'',$dialplan);
			$exten = "dialplan_number='$dialplan'";
			}
		if (eregi("Local/",$Sextension[$i])) 
			{
			$protocol = 'EXTERNAL';
			$dialplan = eregi_replace('Local/',"",$Sextension[$i]);
			$dialplan = eregi_replace("\@.*",'',$dialplan);
			$exten = "dialplan_number='$dialplan'";
			}
		if (eregi('SIP/',$Sextension[$i])) 
			{
			$protocol = 'SIP';
			$dialplan = eregi_replace('SIP/',"",$Sextension[$i]);
			$dialplan = eregi_replace("-.*",'',$dialplan);
			$exten = "extension='$dialplan'";
			}
		if (eregi('IAX2/',$Sextension[$i])) 
			{
			$protocol = 'IAX2';
			$dialplan = eregi_replace('IAX2/',"",$Sextension[$i]);
			$dialplan = eregi_replace("-.*",'',$dialplan);
			$exten = "extension='$dialplan'";
			}
		if (eregi('Zap/',$Sextension[$i])) 
			{
			$protocol = 'Zap';
			$dialplan = eregi_replace('Zap/',"",$Sextension[$i]);
			$exten = "extension='$dialplan'";
			}

		$stmt="select login from phones where server_ip='" . mysql_real_escape_string($server_ip) . "' and $exten and protocol='$protocol';";
		$rslt=mysql_query($stmt, $link);
		$row=mysql_fetch_row($rslt);
		$login = $row[0];

		$phone[$i] =			sprintf("%-10s", $login);

		if (eregi("READY|PAUSED|CLOSER",$Sstatus[$i]))
			{
			$Schannel[$i]='';
			$Sstart_time[$i]='- WAIT -';
			$Scall_time[$i]=$Sfinish_time[$i];
			}
		$extension[$i] = eregi_replace('Local/',"",$Sextension[$i]);
		$extension[$i] =		sprintf("%-10s", $extension[$i]);
			while(strlen($extension[$i])>10) {$extension[$i] = substr("$extension[$i]", 0, -1);}
		$user[$i] =				sprintf("%-6s", $Suser[$i]);
		$sessionid[$i] =		sprintf("%-9s", $Ssessionid[$i]);
		$channel[$i] =			sprintf("%-19s", $Schannel[$i]);
			$cc[$i]=0;
		while ( (strlen($channel[$i]) > 19) and ($cc[$i] < 100) )
			{
			$channel[$i] = eregi_replace(".$","",$channel[$i]);   
			$cc[$i]++;
			if (strlen($channel[$i]) <= 19) {$cc[$i]=101;}
			}
		$status[$i] =			sprintf("%-6s", $Sstatus[$i]);
		$start_time[$i] =		sprintf("%-8s", $Sstart_time[$i]);
			$cd[$i]=0;
		while ( (strlen($start_time[$i]) > 8) and ($cd[$i] < 100) )
			{
			$start_time[$i] = eregi_replace("^.","",$start_time[$i]);   
			$cd[$i]++;
			if (strlen($start_time[$i]) <= 8) {$cd[$i]=101;}
			}
		$uniqueid[$i] =			$Suniqueid[$i];
		$lead_id[$i] =			$Slead_id[$i];
		$closer[$i] =			$Suser[$i];
		$call_time_S[$i] = ($STARTtime - $Scall_time[$i]);

		$call_time_M[$i] = ($call_time_S[$i] / 60);
		$call_time_M[$i] = round($call_time_M[$i], 2);
		$call_time_M_int[$i] = intval("$call_time_M[$i]");
		$call_time_SEC[$i] = ($call_time_M[$i] - $call_time_M_int[$i]);
		$call_time_SEC[$i] = ($call_time_SEC[$i] * 60);
		$call_time_SEC[$i] = round($call_time_SEC[$i], 0);
		if ($call_time_SEC[$i] < 10) {$call_time_SEC[$i] = "0$call_time_SEC[$i]";}
		$call_time_MS[$i] = "$call_time_M_int[$i]:$call_time_SEC[$i]";
		$call_time_MS[$i] =		sprintf("%7s", $call_time_MS[$i]);

		if ($closer_display<1)
			{
			$G = '';		$EG = '';
			if ($call_time_M_int[$i] >= 5) {$G='<SPAN class="blue"><B>'; $EG='</B></SPAN>';}
			if ($call_time_M_int[$i] >= 10) {$G='<SPAN class="purple"><B>'; $EG='</B></SPAN>';}
			if (eregi("PAUSED",$Sstatus[$i])) 
				{
				if ($call_time_M_int >= 1) 
					{$i++; continue;} 
				else
					{$G='<SPAN class="yellow"><B>'; $EG='</B></SPAN>';}
				}
			$agentcount++;
			echo "| $G$extension[$i]$EG | $G$phone[$i]$EG | $G$user[$i]$EG | $G$sessionid[$i]$EG | $G$channel[$i]$EG | $G$status[$i]$EG | $G$start_time[$i]$EG | $G$call_time_MS[$i]$EG |\n";
			}
		$i++;
		}

		if ($closer_display>0)
		{

			$ext_count = $i;
			$i=0;
		while ($i < $ext_count)
			{

			$stmt="select campaign_id from vicidial_auto_calls where lead_id='$lead_id[$i]' and server_ip='" . mysql_real_escape_string($server_ip) . "';";
			$rslt=mysql_query($stmt, $link);
			if ($DB) {echo "$stmt\n";}
			$camp_to_print = mysql_num_rows($rslt);
			if ($camp_to_print > 0)
				{
				$row=mysql_fetch_row($rslt);
				$campaign = sprintf("%-12s", $row[0]);
				$camp_color = $row[0];
				}
			else
				{$campaign = 'DEAD        ';   	$camp_color = 'DEAD';}
			if (eregi("READY|PAUSED|CLOSER",$status[$i]))
				{$campaign = '            ';   	$camp_color = '';}

			$stmt="select user from vicidial_xfer_log where lead_id='$lead_id[$i]' and closer='$closer[$i]' order by call_date desc limit 1;";
			$rslt=mysql_query($stmt, $link);
			if ($DB) {echo "$stmt\n";}
			$xfer_to_print = mysql_num_rows($rslt);
			if ($xfer_to_print > 0)
				{
				$row=mysql_fetch_row($rslt);
				$fronter = sprintf("%-6s", $row[0]);
				}
			else
				{$fronter = '      ';}

			$G = '';		$EG = '';
			$G="<SPAN class=\"$camp_color\"><B>"; $EG='</B></SPAN>';
		#	if ($call_time_M_int[$i] >= 5) {$G='<SPAN class="blue"><B>'; $EG='</B></SPAN>';}
		#	if ($call_time_M_int[$i] >= 10) {$G='<SPAN class="purple"><B>'; $EG='</B></SPAN>';}

			echo "| $G$extension[$i]$EG | $G$phone[$i]$EG | $G$user[$i]$EG | $G$sessionid[$i]$EG | $G$channel[$i]$EG | $G$status[$i]$EG | $G$start_time[$i]$EG | $G$call_time_MS[$i]$EG | $G$campaign$EG | $G$fronter$EG |\n";

			$i++;
			}
		echo "+------------+------------+--------+-----------+---------------------+--------+----------+---------+--------------+--------+\n";
		echo "  $i agents connectés sur ce serveur $server_ip\n\n";
	#	echo "  <SPAN class=\"blue\"><B>          </SPAN> - En appel depuis 5 minutes ou plus</B>\n";
	#	echo "  <SPAN class=\"purple\"><B>          </SPAN> - En appel depuis plus de 10 minutes</B>\n";
		}
	else
		{
		echo "+------------+------------+--------+-----------+---------------------+--------+----------+---------+\n";
		echo "  $agentcount agents connectés sur ce serveur $server_ip\n\n";

		echo "  <SPAN class=\"yellow\"><B>          </SPAN> - Agents en pause</B>\n";
		echo "  <SPAN class=\"blue\"><B>          </SPAN> - En appel depuis 5 minutes ou plus</B>\n";
		echo "  <SPAN class=\"purple\"><B>          </SPAN> - En appel depuis plus de 10 minutes</B>\n";
		}

	}
	else
	{
	echo "**************************************************************************************\n";
	echo "**************************************************************************************\n";
	echo "********************************* AUCUN AGENT EN APPEL*********************************\n";
	echo "**************************************************************************************\n";
	echo "**************************************************************************************\n";
	}


###################################################################################
###### OUTBOUND CALLS
###################################################################################
#echo "\n\n";
echo "----------------------------------------------------------------------------------------";
echo "\n\n";
echo "Server-Specific Real-Time Report        TRUNK SHORT: $balanceSHORT          $NOW_TIME\n\n";
echo "+---------------------+--------+--------------+--------------------+----------+---------+\n";
echo "| CHANNEL             | STATUS | CAMPAIGN     | PHONE NUMBER       | CALLTIME | MINUTES |\n";
echo "+---------------------+--------+--------------+--------------------+----------+---------+\n";

$stmt="select channel,status,campaign_id,phone_code,phone_number,call_time,UNIX_TIMESTAMP(call_time) from vicidial_auto_calls where status NOT IN('XFER') and server_ip='" . mysql_real_escape_string($server_ip) . "' order by auto_call_id desc;";
$rslt=mysql_query($stmt, $link);
if ($DB) {echo "$stmt\n";}
$parked_to_print = mysql_num_rows($rslt);
	if ($parked_to_print > 0)
	{
	$i=0;
	while ($i < $parked_to_print)
		{
		$row=mysql_fetch_row($rslt);

		$channel =			sprintf("%-19s", $row[0]);
			$cc=0;
		while ( (strlen($channel) > 19) and ($cc < 100) )
			{
			$channel = eregi_replace(".$","",$channel);   
			$cc++;
			if (strlen($channel) <= 19) {$cc=101;}
			}
		$start_time =		sprintf("%-8s", $row[5]);
			$cd=0;
		while ( (strlen($start_time) > 8) and ($cd < 100) )
			{
			$start_time = eregi_replace("^.","",$start_time);   
			$cd++;
			if (strlen($start_time) <= 8) {$cd=101;}
			}
		$status =			sprintf("%-6s", $row[1]);
		$campaign =			sprintf("%-12s", $row[2]);
			$all_phone = "$row[3]$row[4]";
		$number_dialed =	sprintf("%-18s", $all_phone);
		$call_time_S = ($STARTtime - $row[6]);

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
		if (eregi("LIVE",$status)) {$G='<SPAN class="green"><B>'; $EG='</B></SPAN>';}
	#	if ($call_time_M_int >= 6) {$G='<SPAN class="red"><B>'; $EG='</B></SPAN>';}

		echo "| $G$channel$EG | $G$status$EG | $G$campaign$EG | $G$number_dialed$EG | $G$start_time$EG | $G$call_time_MS$EG |\n";

		$i++;
		}

		echo "+---------------------+--------+--------------+--------------------+----------+---------+\n";
		echo "  $i appels étant placés sur ce serveur $server_ip\n\n";

		echo "  <SPAN class=\"green\"><B>          </SPAN> - APPEL EN ATTENTE</B>\n";
	#	echo "  <SPAN class=\"red\"><B>          </SPAN> - Over 5 minutes on hold</B>\n";

		}
	else
	{
	echo "***************************************************************************************\n";
	echo "***************************************************************************************\n";
	echo "******************************* PAS D'APPEL EN ATTENTE*********************************\n";
	echo "***************************************************************************************\n";
	echo "***************************************************************************************\n";
	}


?>
</PRE>
</TD></TR></TABLE>

</BODY></HTML>