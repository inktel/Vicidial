<?php
# vtiger_phone_match.php
# 
# Copyright (C) 2010  Matt Florell <vicidial@gmail.com>    LICENSE: AGPLv2
#
# This script searches Vtiger contacts for a matching phone number and returns 
# the number of matches, it was designed to be used with the ViciDial Inbound 
# DID Filter Phone Group feature in the URL search type with the following URL:
#  VARhttp://server/vicidial/vtiger_phone_match.php?phone=--A--phone_number--B--
#
# This code is tested against vtiger 5.1.0
#
# CHANGES
# 100806-0653 - First Build
#

header ("Content-type: text/html; charset=utf-8");

require("dbconnect.php");

if (isset($_GET["phone"]))				{$phone=$_GET["phone"];}
	elseif (isset($_POST["phone"]))		{$phone=$_POST["phone"];}
if (isset($_GET["DB"]))					{$DB=$_GET["DB"];}
	elseif (isset($_POST["DB"]))		{$DB=$_POST["DB"];}

###############################################################
##### START SYSTEM_SETTINGS VTIGER CONNECTION INFO LOOKUP #####
$stmt = "SELECT enable_vtiger_integration,vtiger_server_ip,vtiger_dbname,vtiger_login,vtiger_pass,vtiger_url,use_non_latin FROM system_settings;";
$rslt=mysql_query($stmt, $link);
if ($DB) {echo "$stmt\n";}
$ss_conf_ct = mysql_num_rows($rslt);
if ($ss_conf_ct > 0)
	{
	$row=mysql_fetch_row($rslt);
	$enable_vtiger_integration =	$row[0];
	$vtiger_server_ip	=			$row[1];
	$vtiger_dbname =				$row[2];
	$vtiger_login =					$row[3];
	$vtiger_pass =					$row[4];
	$vtiger_url =					$row[5];
	$non_latin =					$row[6];
	}
##### END SYSTEM_SETTINGS VTIGER CONNECTION INFO LOOKUP #####
#############################################################

if ($non_latin < 1)
	{
	$phone=ereg_replace("[^-_0-9a-zA-Z]","",$phone);
	}
else
	{
	$phone = ereg_replace("'|\"|\\\\|;","",$phone);
	}

$phone_count=0;

if ( ($enable_vtiger_integration > 0) and (strlen($vtiger_server_ip) > 5) and (strlen($phone) > 6) )
	{
	### connect to your vtiger database
	$linkV=mysql_connect("$vtiger_server_ip", "$vtiger_login","$vtiger_pass");
	if (!$linkV) {die("Could not connect: $vtiger_server_ip|$vtiger_dbname|$vtiger_login|$vtiger_pass" . mysql_error());}
	if ($DB) {echo 'Connected successfully';}
	mysql_select_db("$vtiger_dbname", $linkV);

	$stmt="SELECT count(*) from vtiger_contactdetails where phone='$phone';";
	if ($DB) {echo "$stmt\n";}
	$rslt=mysql_query($stmt, $linkV);
	$row=mysql_fetch_row($rslt);
	$phone_count = $row[0];
	if (!$rslt) {die("Could not execute: $stmt" . mysql_error());}

	if ($phone_count < 1)
		{
		$stmt="SELECT count(*) from vtiger_contactsubdetails where homephone='$phone';";
		if ($DB) {echo "$stmt\n";}
		$rslt=mysql_query($stmt, $linkV);
		$row=mysql_fetch_row($rslt);
		$phone_count = $row[0];
		if (!$rslt) {die("Could not execute: $stmt" . mysql_error());}
		}
	}

echo "$phone_count\n";

?>
