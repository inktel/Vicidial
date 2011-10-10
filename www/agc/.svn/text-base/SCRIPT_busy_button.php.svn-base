<?php
### SCRIPT_busy_button.php - logging of a button click
#
# To be inserted into a SCRIPT in VICIDIAL with the following code:
#
# <iframe src="/agc/SCRIPT_busy_button.php?lead_id=--A--lead_id--B--&list_id=--A--list_id--B--&user=--A--user--B--&campaign_id=--A--campaign_id--B--&phone_number=--A--phone_number--B--&vendor_id=--A--vendor_lead_code--B--" style="width:400;height:40;background-color:transparent;" scrolling="no" frameborder="0" allowtransparency="true" id="popupFrame" name="popupFrame" width="400" height="30" STYLE="z-index:17"> </iframe>
#
# Copyright (C) 2009  Matt Florell <vicidial@gmail.com>    LICENSE: AGPLv2
#
########## DB SCHEMA
#	CREATE TABLE qr_busy_button_log (
#	button_id INT(9) UNSIGNED AUTO_INCREMENT PRIMARY KEY NOT NULL,
#	lead_id INT(9) UNSIGNED,
#	event_time DATETIME,
#	user VARCHAR(20),
#	stage ENUM('DISPLAY','CLICK'),
#	campaign_id VARCHAR(8),	
#	phone_number VARCHAR(18),
#	vendor_lead_code VARCHAR(20),
#	list_id BIGINT(14) UNSIGNED,
#	click_seconds INT(5) UNSIGNED,
#	index (lead_id)
#	);
# 
# CHANGES:
# 90408-0618 - First Build
# 90508-0727 - Changed to PHP long tags
#

if (isset($_GET["button_id"]))				{$button_id=$_GET["button_id"];}
	elseif (isset($_POST["button_id"]))		{$button_id=$_POST["button_id"];}
if (isset($_GET["lead_id"]))				{$lead_id=$_GET["lead_id"];}
	elseif (isset($_POST["lead_id"]))		{$lead_id=$_POST["lead_id"];}
if (isset($_GET["vendor_id"]))				{$vendor_id=$_GET["vendor_id"];}
	elseif (isset($_POST["vendor_id"]))		{$vendor_id=$_POST["vendor_id"];}
if (isset($_GET["list_id"]))				{$list_id=$_GET["list_id"];}
	elseif (isset($_POST["list_id"]))		{$list_id=$_POST["list_id"];}
if (isset($_GET["phone_number"]))			{$phone_number=$_GET["phone_number"];}
	elseif (isset($_POST["phone_number"]))	{$phone_number=$_POST["phone_number"];}
if (isset($_GET["user"]))					{$user=$_GET["user"];}
	elseif (isset($_POST["user"]))			{$user=$_POST["user"];}
if (isset($_GET["campaign"]))				{$campaign=$_GET["campaign"];}
	elseif (isset($_POST["campaign"]))		{$campaign=$_POST["campaign"];}
if (isset($_GET["stage"]))					{$stage=$_GET["stage"];}
	elseif (isset($_POST["stage"]))			{$stage=$_POST["stage"];}
if (isset($_GET["epoch"]))					{$epoch=$_GET["epoch"];}
	elseif (isset($_POST["epoch"]))			{$epoch=$_POST["epoch"];}

### security strip all non-alphanumeric characters out of the variables ###
$button_id=ereg_replace("[^0-9]","",$button_id);
$lead_id=ereg_replace("[^0-9]","",$lead_id);
$list_id=ereg_replace("[^0-9]","",$list_id);
$phone_number=ereg_replace("[^0-9]","",$phone_number);
$vendor_id = ereg_replace("[^- \:\/\_0-9a-zA-Z]","",$vendor_id);
$user=ereg_replace("[^0-9a-zA-Z]","",$user);
$campaign = ereg_replace("[^-\_0-9a-zA-Z]","",$campaign);
$stage = ereg_replace("[^-\_0-9a-zA-Z]","",$stage);
$epoch = ereg_replace("[^0-9]","",$epoch);

require("dbconnect.php");

if (eregi("CLICK",$stage))
	{
	$epochNOW = date("U");
	$click_seconds = ($epochNOW - $epoch);
	if ( ($click_seconds < 0) or ($click_seconds > 3600) )
		{$click_seconds=0;}

	$stmt="UPDATE qr_busy_button_log SET stage='$stage',click_seconds='$click_seconds' where button_id='$button_id';";
	if ($DB) {echo "|$stmt|\n";}
	$rslt=mysql_query($stmt, $link);

	echo "Thank you for clicking the button\n";

	exit;
	}

else
	{
	$CL=':';
	$script_name = getenv("SCRIPT_NAME");
	$server_name = getenv("SERVER_NAME");
	$server_port = getenv("SERVER_PORT");
	if (eregi("443",$server_port)) {$HTTPprotocol = 'https://';}
	  else {$HTTPprotocol = 'http://';}
	if (($server_port == '80') or ($server_port == '443') ) {$server_port='';}
	else {$server_port = "$CL$server_port";}
	$agcPAGE = "$HTTPprotocol$server_name$server_port$script_name";

	$epoch = date("U");
	$SQLdate = date("Y-m-d H:i:s");

	$stmt="INSERT INTO qr_busy_button_log SET lead_id='$lead_id',list_id='$list_id',phone_number='$phone_number',vendor_lead_code='$vendor_id',user='$user',campaign_id='$campaign',stage='DISPLAY',event_time='$SQLdate';";
	if ($DB) {echo "|$stmt|\n";}
	$rslt=mysql_query($stmt, $link);
	$button_id = mysql_insert_id($link);

	echo "<FORM NAME=button_form ID=button_form ACTION=\"$agcPAGE\" METHOD=POST>\n";
	echo "<INPUT TYPE=HIDDEN NAME=button_id VALUE=\"$button_id\">\n";
	echo "<INPUT TYPE=HIDDEN NAME=stage VALUE=\"CLICK\">\n";
	echo "<INPUT TYPE=HIDDEN NAME=epoch VALUE=\"$epoch\">\n";
	echo "<INPUT TYPE=SUBMIT NAME=SUBMIT VALUE=\"PLEASE CLICK THIS TEST BUTTON\">\n";
	echo "</FORM>\n\n";
	}

?>

