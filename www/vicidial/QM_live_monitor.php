<?php 
# QM_live_monitor.php
# 
# Copyright (C) 2009  Matt Florell <vicidial@gmail.com>    LICENSE: AGPLv2
#
# Script to initiate live monitoring from QueueMetrics link
#
# CHANGELOG:
# 90529-2115 - First Build
#

$version = '2.2.0-1';
$build = '90529-2115';

header ("Content-type: text/html; charset=utf-8");

require("dbconnect.php");
require("functions.php");


if (isset($_GET["DB"]))					{$DB=$_GET["DB"];}
	elseif (isset($_POST["DB"]))		{$DB=$_POST["DB"];}
if (isset($_GET["campaign"]))			{$campaign=$_GET["campaign"];}
	elseif (isset($_POST["campaign"]))	{$campaign=$_POST["campaign"];}
if (isset($_GET["user"]))				{$user=$_GET["user"];}
	elseif (isset($_POST["user"]))		{$user=$_POST["user"];}
if (isset($_GET["server_ip"]))			{$server_ip=$_GET["server_ip"];}
	elseif (isset($_POST["server_ip"]))	{$server_ip=$_POST["server_ip"];}
if (isset($_GET["session"]))			{$session=$_GET["session"];}
	elseif (isset($_POST["session"]))	{$session=$_POST["session"];}
if (isset($_GET["phone"]))				{$phone=$_GET["phone"];}
	elseif (isset($_POST["phone"]))		{$phone=$_POST["phone"];}
if (isset($_GET["type"]))				{$type=$_GET["type"];}
	elseif (isset($_POST["type"]))		{$type=$_POST["type"];}
if (isset($_GET["call"]))				{$call=$_GET["call"];}
	elseif (isset($_POST["call"]))		{$call=$_POST["call"];}
if (isset($_GET["QMuser"]))				{$QMuser=$_GET["QMuser"];}
	elseif (isset($_POST["QMuser"]))	{$QMuser=$_POST["QMuser"];}
if (isset($_GET["extension"]))			{$extension=$_GET["extension"];}
	elseif (isset($_POST["extension"]))	{$extension=$_POST["extension"];}
if (isset($_GET["stage"]))				{$stage=$_GET["stage"];}
	elseif (isset($_POST["stage"]))		{$stage=$_POST["stage"];}


$ERR=0;
$ERRstring='';

#############################################
##### START QUEUEMETRICS LOGGING LOOKUP #####
$stmt = "SELECT enable_queuemetrics_logging FROM system_settings;";
$rslt=mysql_query($stmt, $link);
if ($DB) {echo "$stmt\n";}
$qm_conf_ct = mysql_num_rows($rslt);
if ($qm_conf_ct > 0)
	{
	$row=mysql_fetch_row($rslt);
	$enable_queuemetrics_logging =	$row[0];
	}
##### END QUEUEMETRICS LOGGING LOOKUP #####
###########################################
if ($enable_queuemetrics_logging > 0)
	{
	$stmt = "SELECT user,server_ip,conf_exten,comments FROM vicidial_live_agents where callerid='$call';";
	$rslt=mysql_query($stmt, $link);
	if ($DB) {echo "$stmt\n";}
	$vla_conf_ct = mysql_num_rows($rslt);
	if ($vla_conf_ct > 0)
		{
		$row=mysql_fetch_row($rslt);
		$VLAuser =			$row[0];
		$VLAserver_ip =		$row[1];
		$VLAconf_exten =	$row[2];

		$stmt = "SELECT campaign_id,phone_number,call_type FROM vicidial_auto_calls where callerid='$call';";
		$rslt=mysql_query($stmt, $link);
		if ($DB) {echo "$stmt\n";}
		$vla_conf_ct = mysql_num_rows($rslt);
		if ($vla_conf_ct > 0)
			{
			$row=mysql_fetch_row($rslt);
			$VACcampaign =	$row[0];
			$VACphone =		$row[1];
			$VACtype =		$row[2];

			if ($stage == 'MONITOR')
				{
				$script_name = getenv("SCRIPT_NAME");
				$server_name = getenv("SERVER_NAME");
				$server_port = getenv("SERVER_PORT");
				if (eregi("443",$server_port)) {$HTTPprotocol = 'https://';}
				  else {$HTTPprotocol = 'http://';}
				$admDIR = "$HTTPprotocol$server_name$script_name";
				$admDIR = eregi_replace('QM_live_monitor.php','',$admDIR);
				$monitor_script = 'non_agent_api.php';

				$monitorURL = "$admDIR$monitor_script?source=queuemetrics&function=blind_monitor&user=$user&pass=$call&phone_login=$extension&session_id=$session&server_ip=$server_ip&stage=$stage";
				$monitorCONTENTS = file("$monitorURL");
	
				if (eregi('SUCCESS',$monitorCONTENTS[0]))
					{
					echo "<HTML><BODY BGCOLOR=\"E6E6E6\" onLoad=\"javascript:window.close();\">\n";
					}
				else
					{
					echo "<HTML><BODY BGCOLOR=\"E6E6E6\">\n";
					echo "$monitorCONTENTS[0]<BR>\n";
					}

				echo "<!-- $monitorURL -->\n";
				echo "<!-- $monitorCONTENTS[0] -->\n";
				echo "<FORM ACTION=\"$PHP_SELF\" METHOD=POST>\n";
				echo "<INPUT TYPE=BUTTON VALUE=\"Close Window\"  onClick=\"javascript:window.close();\">";
				echo "\n";
				echo "</FORM>\n";
				echo "</BODY></HTML>\n";

				exit;
				}
			else
				{
				echo "<HTML><BODY BGCOLOR=\"E6E6E6\">";
				echo "<FORM ACTION=\"$PHP_SELF\" METHOD=POST>\n";
				echo "<INPUT TYPE=HIDDEN NAME=stage VALUE=\"MONITOR\">\n";
				echo "<INPUT TYPE=HIDDEN NAME=campaign VALUE=\"$campaign\">\n";
				echo "<INPUT TYPE=HIDDEN NAME=user VALUE=\"$user\">\n";
				echo "<INPUT TYPE=HIDDEN NAME=server_ip VALUE=\"$server_ip\">\n";
				echo "<INPUT TYPE=HIDDEN NAME=session VALUE=\"$session\">\n";
				echo "<INPUT TYPE=HIDDEN NAME=phone VALUE=\"$phone\">\n";
				echo "<INPUT TYPE=HIDDEN NAME=type VALUE=\"$type\">\n";
				echo "<INPUT TYPE=HIDDEN NAME=call VALUE=\"$call\">\n";
				echo "<INPUT TYPE=HIDDEN NAME=QMuser VALUE=\"$QMuser\">\n";
				echo "Remote Monitor:<BR>";
				echo "<TABLE CELLPADDING=4 CELLSPACING=1 BORDER=2>";
				echo "<TR><TD ALIGN=RIGHT>Agent: </TD><TD>$user</TD></TR>";
				echo "<TR><TD ALIGN=RIGHT>Customer: </TD><TD>$phone</TD></TR>";
				echo "<TR><TD ALIGN=RIGHT>Call Type: </TD><TD>$type</TD></TR>";
				echo "<TR><TD ALIGN=RIGHT>Queue: </TD><TD>$campaign</TD></TR>";
				echo "<TR><TD ALIGN=RIGHT>Extension: </TD>";
				echo "<TD><INPUT TYPE=TEXT NAME=extension SIZE=20 MAXLENGTH=20></TD></TR>\n";
				echo "<TR><TD ALIGN=CENTER COLSPAN=2><INPUT TYPE=BUTTON VALUE=\"Close Window\"  onClick=\"javascript:window.close();\">";
				echo "<INPUT TYPE=SUBMIT NAME=SUBMIT VALUE=SUBMIT></TD></TR>\n";
				echo "</FORM>";
				echo "</BODY></HTML>";
				exit;
				}
			}
		else
			{
			$ERR++;
			$ERRstring = "Call not found";
			}
		}
	else
		{
		$ERR++;
		$ERRstring = "Agent not found";
		}
	}
else
	{
	$ERR++;
	$ERRstring = "QueueMetrics is not enabled on this system";
	}

if ($ERR > 0)
	{
	echo "ERROR: $ERRstring\n";
	echo "<!-- Agent: $user -->\n";
	echo "<!-- Call:  $call -->\n";
	exit;
	}

exit;




