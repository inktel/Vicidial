<?php
# deactivate_lead.php
# 
# Copyright (C) 2010  Matt Florell <vicidial@gmail.com>    LICENSE: AGPLv2
#
# This script is designed to be used in the "Dispo URL" field of a campaign
# or in-group. It should take in the campaign_id to check for the same source_id
# in order to change it's status to whatever duplicate_status is set to. The
# sale_status field is a list of statuses separated by three dashes each '---'
# which contain the statuses for which the process should be run.
#
# This script is part of the API group and any modifications of data are
# logged to the vicidial_api_log table.
#
# This script limits the number of altered leads to 100 per instance and it will
# not run if the search field of the lead is empty.
# http://server/agc/deactivate_lead.php?search_field=source_id&new_status=INACTIV&dispo=--A--dispo--B--&lead_id=--A--lead_id--B--&campaign_check=TEST2&user=--A--user--B--&pass=--A--pass--B--&sale_status=SALE---SSALE---XSALE&log_to_file=1
#
# Example of what to put in the Dispo URL field:
# VARhttp://192.168.1.1/agc/deactivate_lead.php?search_field=vendor_lead_code&new_status=INACTIV&dispo=--A--dispo--B--&lead_id=--A--lead_id--B--&campaign_check=TESTCAMP&user=--A--user--B--&pass=--A--pass--B--&sale_status=SALE---SSALE---XSALE&log_to_file=1
# VARhttp://192.168.1.1/agc/deactivate_lead.php?search_field=phone_number&new_status=--A--dispo--B--&dispo=--A--dispo--B--&lead_id=--A--lead_id--B--&campaign_check=TESTCAMP&user=--A--user--B--&pass=--A--pass--B--&sale_status=BI---CX&log_to_file=1
# 
#
# CHANGES
# 100304-0354 - First Build
#

$api_script = 'deactivate';

header ("Content-type: text/html; charset=utf-8");

require("dbconnect.php");

$filedate = date("Ymd");
$filetime = date("H:i:s");
$IP = getenv ("REMOTE_ADDR");
$BR = getenv ("HTTP_USER_AGENT");

$PHP_AUTH_USER=$_SERVER['PHP_AUTH_USER'];
$PHP_AUTH_PW=$_SERVER['PHP_AUTH_PW'];
$PHP_SELF=$_SERVER['PHP_SELF'];
if (isset($_GET["lead_id"]))				{$lead_id=$_GET["lead_id"];}
	elseif (isset($_POST["lead_id"]))		{$lead_id=$_POST["lead_id"];}
if (isset($_GET["search_field"]))			{$search_field=$_GET["search_field"];}
	elseif (isset($_POST["search_field"]))	{$search_field=$_POST["search_field"];}
if (isset($_GET["campaign_check"]))				{$campaign_check=$_GET["campaign_check"];}
	elseif (isset($_POST["campaign_check"]))	{$campaign_check=$_POST["campaign_check"];}
if (isset($_GET["sale_status"]))			{$sale_status=$_GET["sale_status"];}
	elseif (isset($_POST["sale_status"]))	{$sale_status=$_POST["sale_status"];}
if (isset($_GET["dispo"]))					{$dispo=$_GET["dispo"];}
	elseif (isset($_POST["dispo"]))			{$dispo=$_POST["dispo"];}
if (isset($_GET["new_status"]))				{$new_status=$_GET["new_status"];}
	elseif (isset($_POST["new_status"]))	{$new_status=$_POST["new_status"];}
if (isset($_GET["user"]))					{$user=$_GET["user"];}
	elseif (isset($_POST["user"]))			{$user=$_POST["user"];}
if (isset($_GET["pass"]))					{$pass=$_GET["pass"];}
	elseif (isset($_POST["pass"]))			{$pass=$_POST["pass"];}
if (isset($_GET["DB"]))						{$DB=$_GET["DB"];}
	elseif (isset($_POST["DB"]))			{$DB=$_POST["DB"];}
if (isset($_GET["log_to_file"]))			{$log_to_file=$_GET["log_to_file"];}
	elseif (isset($_POST["log_to_file"]))	{$log_to_file=$_POST["log_to_file"];}


#$DB = '1';	# DEBUG override
$US = '_';
$TD = '---';
$STARTtime = date("U");
$NOW_TIME = date("Y-m-d H:i:s");
$sale_status = "$TD$sale_status$TD";
$search_value='';

if ($DB>0) {echo "$lead_id|$search_field|$campaign_check|$sale_status|$dispo|$new_status|$user|$pass|$DB|$log_to_file|\n";}

if (preg_match("/$TD$dispo$TD/",$sale_status))
	{
	#############################################
	##### START SYSTEM_SETTINGS INFO LOOKUP #####
	$stmt = "SELECT use_non_latin FROM system_settings;";
	$rslt=mysql_query($stmt, $link);
	if ($DB) {echo "$stmt\n";}
	$ss_conf_ct = mysql_num_rows($rslt);
	if ($ss_conf_ct > 0)
		{
		$row=mysql_fetch_row($rslt);
		$non_latin = $row[0];
		}
	##### END SYSTEM_SETTINGS INFO LOOKUP #####
	###########################################

	if ($non_latin < 1)
		{
		$user=ereg_replace("[^-_0-9a-zA-Z]","",$user);
		$pass=ereg_replace("[^-_0-9a-zA-Z]","",$pass);
		}
	else
		{
		$user = ereg_replace("'|\"|\\\\|;","",$user);
		$pass = ereg_replace("'|\"|\\\\|;","",$pass);
		}

	$stmt="SELECT count(*) from vicidial_users where user='$user' and pass='$pass' and user_level > 0;";
	if ($DB) {echo "|$stmt|\n";}
	if ($non_latin > 0) {$rslt=mysql_query("SET NAMES 'UTF8'");}
	$rslt=mysql_query($stmt, $link);
	$row=mysql_fetch_row($rslt);
	$auth=$row[0];

	if( (strlen($user)<2) or (strlen($pass)<2) or ($auth==0))
		{
		echo "Invalid Username/Password: |$user|$pass|\n";
		exit;
		}


	$stmt = "SELECT $search_field FROM vicidial_list where lead_id='$lead_id';";
	$rslt=mysql_query($stmt, $link);
	if ($DB) {echo "$stmt\n";}
	$sv_ct = mysql_num_rows($rslt);
	if ($sv_ct > 0)
		{
		$row=mysql_fetch_row($rslt);
		$search_value = $row[0];
		}

	if (strlen($search_value) > 0)
		{
		$stmt="select list_id from vicidial_lists where campaign_id='$campaign_check';";
		$rslt=mysql_query($stmt, $link);
		$li_recs = mysql_num_rows($rslt);
		if ($li_recs > 0)
			{
			$L=0;
			while ($li_recs > $L)
				{
				$row=mysql_fetch_row($rslt);
				$duplicate_lists .=	"'$row[0]',";
				$L++;
				}
			$duplicate_lists = eregi_replace(",$",'',$duplicate_lists);
			if (strlen($duplicate_lists) < 2) {$duplicate_lists = "''";}
			}

		if (strlen($duplicate_lists) > 4)
			{
			$search_count=0;
			$stmt = "SELECT count(*) FROM vicidial_list where $search_field='$search_value' and list_id IN($duplicate_lists);";
			$rslt=mysql_query($stmt, $link);
			if ($DB) {echo "$stmt\n";}
			$sc_ct = mysql_num_rows($rslt);
			if ($sc_ct > 0)
				{
				$row=mysql_fetch_row($rslt);
				$search_count = $row[0];
				}

			if ($search_count > 0)
				{
				$stmt="UPDATE vicidial_list SET status='$new_status' where $search_field='$search_value' and list_id IN($duplicate_lists) limit 100;";
				if ($DB) {echo "$stmt\n";}
				$rslt=mysql_query($stmt, $link);
				$affected_rows = mysql_affected_rows($link);

				$SQL_log = "$stmt|";
				$SQL_log = ereg_replace(';','',$SQL_log);
				$SQL_log = addslashes($SQL_log);
				$stmt="INSERT INTO vicidial_api_log set user='$user',agent_user='$user',function='deactivate_lead',value='$lead_id',result='$affected_rows',result_reason='$search_field',source='vdc',data='$SQL_log',api_date='$NOW_TIME',api_script='$api_script';";
				$rslt=mysql_query($stmt, $link);

				$MESSAGE = "DONE: $search_count duplicates found, $affected_rows updated to $new_status from $dispo";
				echo "$MESSAGE\n";
				}
			else
				{
				$MESSAGE = "DONE: no duplicates found within $campaign_check     |$duplicate_lists|";
				echo "$MESSAGE\n";
				}
			}
		else
			{
			$MESSAGE = "DONE: no lists in campaign_check $campaign_check";
			echo "$MESSAGE\n";
			}
		}
	else
		{
		$MESSAGE = "DONE: $search_field is empty for lead $lead_id";
		echo "$MESSAGE\n";
		}
	}
else
	{
	$MESSAGE = "DONE: dispo is not a sale status: $dispo";
	echo "$MESSAGE\n";
	}

if ($log_to_file > 0)
	{
	$fp = fopen ("./deactivate_lead.txt", "a");
	fwrite ($fp, "$NOW_TIME|$lead_id|$search_field|$search_value|$campaign_check|$sale_status|$dispo|$new_status|$user|$pass|$DB|$log_to_file|$MESSAGE|\n");
	fclose($fp);
	}
