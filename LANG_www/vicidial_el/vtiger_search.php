<?php
# vtiger_search.php
# 
# Copyright (C) 2009  Matt Florell <vicidial@gmail.com>    LICENSE: AGPLv2
#
# This page does a search against a standard vtiger CRM system. If the record 
# is not present, it will create a new one and send the agent's screen to that new page.
#
# This code is tested against vtiger 5.0.4 and 5.1.0
#
# CHANGES
# 60719-1615 - First version
# 60801-2304 - Added mysql debug and auto-forward
# 60802-1111 - Added insertion of not-found record into vtiger system
# 71220-0000 - Modified by I. Taushanov for VTiger 5.03- search/create lead
# 80120-1934 - Added changes for compatibility with vtiger 5.0.3
# 81229-1017 - Added usage of system_settings connection settings for vtiger database
# 81229-1441 - Added options for searching by ACCTID, ACCOUNT, VENDOR and LEAD
# 90111-1451 - Added logging of call as activity for account/lead
# 90112-0336 - Added create call and create lead options
# 90323-2104 - Added deleted account/lead check and reactivation from campaign option
# 91228-1751 - Added UNIFIED_CONTACT search option
#

header ("Content-type: text/html; charset=utf-8");

require("dbconnect.php");

$PHP_AUTH_USER=$_SERVER['PHP_AUTH_USER'];
$PHP_AUTH_PW=$_SERVER['PHP_AUTH_PW'];
$PHP_SELF=$_SERVER['PHP_SELF'];
if (isset($_GET["address1"]))				{$address1=$_GET["address1"];}
	elseif (isset($_POST["address1"]))		{$address1=$_POST["address1"];}
if (isset($_GET["address2"]))				{$address2=$_GET["address2"];}
	elseif (isset($_POST["address2"]))		{$address2=$_POST["address2"];}
if (isset($_GET["address3"]))				{$address3=$_GET["address3"];}
	elseif (isset($_POST["address3"]))		{$address3=$_POST["address3"];}
if (isset($_GET["alt_phone"]))				{$alt_phone=$_GET["alt_phone"];}
	elseif (isset($_POST["alt_phone"]))		{$alt_phone=$_POST["alt_phone"];}
if (isset($_GET["call_began"]))				{$call_began=$_GET["call_began"];}
	elseif (isset($_POST["call_began"]))	{$call_began=$_POST["call_began"];}
if (isset($_GET["campaign"]))				{$campaign=$_GET["campaign"];}
	elseif (isset($_POST["campaign"]))		{$campaign=$_POST["campaign"];}
if (isset($_GET["channel"]))				{$channel=$_GET["channel"];}
	elseif (isset($_POST["channel"]))		{$channel=$_POST["channel"];}
if (isset($_GET["channel_group"]))			{$channel_group=$_GET["channel_group"];}
	elseif (isset($_POST["channel_group"]))	{$channel_group=$_POST["channel_group"];}
if (isset($_GET["city"]))					{$city=$_GET["city"];}
	elseif (isset($_POST["city"]))			{$city=$_POST["city"];}
if (isset($_GET["comments"]))				{$comments=$_GET["comments"];}
	elseif (isset($_POST["comments"]))		{$comments=$_POST["comments"];}
if (isset($_GET["country_code"]))			{$country_code=$_GET["country_code"];}
	elseif (isset($_POST["country_code"]))	{$country_code=$_POST["country_code"];}
if (isset($_GET["customer_zap_channel"]))			{$customer_zap_channel=$_GET["customer_zap_channel"];}
	elseif (isset($_POST["customer_zap_channel"]))	{$customer_zap_channel=$_POST["customer_zap_channel"];}
if (isset($_GET["DB"]))						{$DB=$_GET["DB"];}
	elseif (isset($_POST["DB"]))			{$DB=$_POST["DB"];}
if (isset($_GET["dispo"]))					{$dispo=$_GET["dispo"];}
	elseif (isset($_POST["dispo"]))			{$dispo=$_POST["dispo"];}
if (isset($_GET["email"]))					{$email=$_GET["email"];}
	elseif (isset($_POST["email"]))			{$email=$_POST["email"];}
if (isset($_GET["end_call"]))				{$end_call=$_GET["end_call"];}
	elseif (isset($_POST["end_call"]))		{$end_call=$_POST["end_call"];}
if (isset($_GET["extension"]))				{$extension=$_GET["extension"];}
	elseif (isset($_POST["extension"]))		{$extension=$_POST["extension"];}
if (isset($_GET["first_name"]))				{$first_name=$_GET["first_name"];}
	elseif (isset($_POST["first_name"]))	{$first_name=$_POST["first_name"];}
if (isset($_GET["group"]))					{$group=$_GET["group"];}
	elseif (isset($_POST["group"]))			{$group=$_POST["group"];}
if (isset($_GET["last_name"]))				{$last_name=$_GET["last_name"];}
	elseif (isset($_POST["last_name"]))		{$last_name=$_POST["last_name"];}
if (isset($_GET["lead_id"]))				{$lead_id=$_GET["lead_id"];}
	elseif (isset($_POST["lead_id"]))		{$lead_id=$_POST["lead_id"];}
if (isset($_GET["list_id"]))				{$list_id=$_GET["list_id"];}
	elseif (isset($_POST["list_id"]))		{$list_id=$_POST["list_id"];}
if (isset($_GET["parked_time"]))			{$parked_time=$_GET["parked_time"];}
	elseif (isset($_POST["parked_time"]))	{$parked_time=$_POST["parked_time"];}
if (isset($_GET["pass"]))					{$pass=$_GET["pass"];}
	elseif (isset($_POST["pass"]))			{$pass=$_POST["pass"];}
if (isset($_GET["phone_code"]))				{$phone_code=$_GET["phone_code"];}
	elseif (isset($_POST["phone_code"]))	{$phone_code=$_POST["phone_code"];}
if (isset($_GET["phone_number"]))			{$phone_number=$_GET["phone_number"];}
	elseif (isset($_POST["phone_number"]))	{$phone_number=$_POST["phone_number"];}
if (isset($_GET["phone"]))					{$phone=$_GET["phone"];}
	elseif (isset($_POST["phone"]))			{$phone=$_POST["phone"];}
if (isset($_GET["postal_code"]))			{$postal_code=$_GET["postal_code"];}
	elseif (isset($_POST["postal_code"]))	{$postal_code=$_POST["postal_code"];}
if (isset($_GET["province"]))				{$province=$_GET["province"];}
	elseif (isset($_POST["province"]))		{$province=$_POST["province"];}
if (isset($_GET["security"]))				{$security=$_GET["security"];}
	elseif (isset($_POST["security"]))		{$security=$_POST["security"];}
if (isset($_GET["server_ip"]))				{$server_ip=$_GET["server_ip"];}
	elseif (isset($_POST["server_ip"]))		{$server_ip=$_POST["server_ip"];}
if (isset($_GET["server_ip"]))				{$server_ip=$_GET["server_ip"];}
	elseif (isset($_POST["server_ip"]))		{$server_ip=$_POST["server_ip"];}
if (isset($_GET["session_id"]))				{$session_id=$_GET["session_id"];}
	elseif (isset($_POST["session_id"]))	{$session_id=$_POST["session_id"];}
if (isset($_GET["state"]))					{$state=$_GET["state"];}
	elseif (isset($_POST["state"]))			{$state=$_POST["state"];}
if (isset($_GET["status"]))					{$status=$_GET["status"];}
	elseif (isset($_POST["status"]))		{$status=$_POST["status"];}
if (isset($_GET["tsr"]))					{$tsr=$_GET["tsr"];}
	elseif (isset($_POST["tsr"]))			{$tsr=$_POST["tsr"];}
if (isset($_GET["user"]))					{$user=$_GET["user"];}
	elseif (isset($_POST["user"]))			{$user=$_POST["user"];}
if (isset($_GET["vendor_id"]))				{$vendor_id=$_GET["vendor_id"];}
	elseif (isset($_POST["vendor_id"]))		{$vendor_id=$_POST["vendor_id"];}
if (isset($_GET["submit"]))					{$submit=$_GET["submit"];}
	elseif (isset($_POST["submit"]))		{$submit=$_POST["submit"];}
if (isset($_GET["SUBMIT"]))					{$SUBMIT=$_GET["SUBMIT"];}
	elseif (isset($_POST["SUBMIT"]))		{$SUBMIT=$_POST["SUBMIT"];}

#$DB = '1';	# DEBUG override
$US = '_';
$STARTtime = date("U");
$TODAY = date("Y-m-d");
$HHMMnow = date("H:i");
$minute_old = mktime(date("H"), date("i")+5, date("s"), date("m"), date("d"),  date("Y"));
$HHMMend = date("H:i",$minute_old);
$NOW_TIME = date("Y-m-d H:i:s");
$REC_TIME = date("Ymd-His");
$FILE_datetime = $STARTtime;
$parked_time = $STARTtime;

###############################################################
##### START SYSTEM_SETTINGS VTIGER CONNECTION INFO LOOKUP #####
$stmt = "SELECT enable_vtiger_integration,vtiger_server_ip,vtiger_dbname,vtiger_login,vtiger_pass,vtiger_url FROM system_settings;";
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
	}
##### END SYSTEM_SETTINGS VTIGER CONNECTION INFO LOOKUP #####
#############################################################


echo "<html>\n";
echo "<head>\n";
echo "<title>VICIDIAL Vtiger Lookup</title>\n";
echo "<META HTTP-EQUIV=\"Content-Type\" CONTENT=\"text/html; charset=utf-8\">\n";


if ($enable_vtiger_integration < 1)
	{
	echo "<B>ERROR! - Vtiger integration is disabled in the VICIDIAL system_settings";
	exit;
	}

$stmt = "SELECT vtiger_search_category,vtiger_create_call_record,vtiger_create_lead_record,vtiger_search_dead FROM vicidial_campaigns where campaign_id='$campaign';";
$rslt=mysql_query($stmt, $link);
if ($DB) {echo "$stmt\n";}
$vtc_conf_ct = mysql_num_rows($rslt);
if ($vtc_conf_ct > 0)
	{
	$row=mysql_fetch_row($rslt);
	$vtiger_search_category =		$row[0];
	$vtiger_create_call_record =	$row[1];
	$vtiger_create_lead_record =	$row[2];
	$vtiger_search_dead =			$row[3];
	}
if (strlen($vtiger_search_category)<1) 
	{$vtiger_search_category = 'LEAD';}

### connect to your vtiger database
$linkV=mysql_connect("$vtiger_server_ip", "$vtiger_login","$vtiger_pass");
if (!$linkV) {die("Could not connect: $vtiger_server_ip|$vtiger_dbname|$vtiger_login|$vtiger_pass" . mysql_error());}
echo 'Connected successfully';
mysql_select_db("$vtiger_dbname", $linkV);

# Methods of searching for records:
#
# ACCTID:
# $stmt="SELECT count(*) from vtiger_account where accountid='$vendor_id';";
#
# ACCOUNT:
# $stmt="SELECT count(*) from vtiger_account where phone='$phone' or otherphone='$phone' or fax='$phone';";
# $stmt="SELECT count(*) from vtiger_contactdetails where phone='$phone' or mobile='$phone' or fax='$phone';";
# $stmt="SELECT count(*) from vtiger_contactsubdetails where homephone='$phone' or otherphone='$phone' or assistantphone='$phone';";
#
# VENDOR:
# $stmt="SELECT count(*) from vtiger_vendor where phone='$phone';";
#
# LEAD:
# $stmt="SELECT count(*) from vtiger_leadaddress where phone='$phone' or mobile='$phone' or fax='$phone';";

$lead_search=0; $account_search=0; $vendor_search=0; $acctid_search=0; $unified_contact=0;

if (ereg('ACCTID',$vtiger_search_category))				{$acctid_search=1;}
if (ereg('ACCOUNT',$vtiger_search_category))			{$account_search=1;}
if (ereg('VENDOR',$vtiger_search_category))				{$vendor_search=1;}
if (ereg('LEAD',$vtiger_search_category))				{$lead_search=1;}
if (ereg('UNIFIED_CONTACT',$vtiger_search_category))	{$unified_contact=1;}



##########################################################################
##### BEGIN - UNIFIED_CONTACT -  Search using beta 5.1.0 unified search feature
##########################################################################
if ($unified_contact > 0)
	{
	$unified_contact_URL = "$vtiger_url/index.php?action=UnifiedSearch&module=Home&search_module=Contacts&query_string=$phone&_service=vicidial";

	echo "<META HTTP-EQUIV=Refresh CONTENT=\"0; URL=$unified_contact_URL\">\n";
	echo "</head>\n";
	echo "<BODY BGCOLOR=white marginheight=0 marginwidth=0 leftmargin=0 topmargin=0\">\n";
	echo "<CENTER><FONT FACE=\"Courier\" COLOR=BLACK SIZE=3>\n";

	echo "<PRE>";
	echo "Forwarding to Vtiger Unified Contact Search page...\n";
	echo "phone number:   <a href=\"$unified_contact_URL\">$phone</a>\n";
	echo "</PRE><BR>";
	exit;
	}
##########################################################################
##### END - UNIFIED_CONTACT
##########################################################################



##########################################################################
##### BEGIN - ACCTID -  Search in the account records for accountid number
##########################################################################
if ($acctid_search > 0)
	{
	$stmt="SELECT count(*) from vtiger_account where accountid='$vendor_id';";
	$rslt=mysql_query($stmt, $linkV);
	if ($DB) {echo "$stmt\n";}
	if (!$rslt) {die('Could not execute: ' . mysql_error());}
	$row=mysql_fetch_row($rslt);
	$found_count = $row[0];

	if ($DB) {echo "<BR>\nACCTID|$vendor_id|$found_count|\n";}

	if ($found_count < 1)
		{
		echo "<!-- ACCTID not found $vendor_id -->\n";
		}
	else
		{
		$stmt="SELECT count(*) from vtiger_crmentity where crmid='$vendor_id' and deleted='1';";
		$rslt=mysql_query($stmt, $linkV);
		if ($DB) {echo "$stmt\n";}
		if (!$rslt) {die('Could not execute: ' . mysql_error());}
		$row=mysql_fetch_row($rslt);
		$deleted_count = $row[0];
		if ( ($deleted_count > 0) and (ereg('DISABLED',$vtiger_search_dead)) )
			{
			echo "<!-- ACCTID found but deleted $vendor_id -->\n";
			}
		else
			{
			if ( ($deleted_count > 0) and ( (ereg('RESURRECT',$vtiger_search_dead)) or (ereg('ASK',$vtiger_search_dead)) ) )
				{
				# un-delete the record
				$stmt="UPDATE vtiger_crmentity SET deleted='0' where crmid='$vendor_id';";
				if ($DB) {echo "$stmt\n";}
				$rslt=mysql_query($stmt, $linkV);
				if (!$rslt) {die('Could not execute: ' . mysql_error());}
				
				echo "<!-- ACCTID deleted but resurrected $vendor_id -->\n";
				}

			if (ereg('Y',$vtiger_create_call_record))
				{
				### Log the call in Vtiger

				#Get logged in user ID
				$stmt="SELECT id from vtiger_users where user_name='$user';";
				if ($DB) {echo "$stmt\n";}
				$rslt=mysql_query($stmt, $linkV);
				$row=mysql_fetch_row($rslt);
				$user_id = $row[0];
				if (!$rslt) {die('Could not execute: ' . mysql_error());}
				
				# Get next aviable id from vtiger_crmentity_seq to use as activityid in vtiger_crmentity	
				$stmt="SELECT id from vtiger_crmentity_seq ;";
				if ($DB) {echo "$stmt\n";}
				$rslt=mysql_query($stmt, $linkV);
				$row=mysql_fetch_row($rslt);
				$activityid = ($row[0] + 1);
				if (!$rslt) {die('Could not execute: ' . mysql_error());}

				# Increase next aviable crmid with 1 so next record gets proper id
				$stmt="UPDATE vtiger_crmentity_seq SET id = '$activityid';";
				if ($DB) {echo "$stmt\n";}
				$rslt=mysql_query($stmt, $linkV);
				if (!$rslt) {die('Could not execute: ' . mysql_error());}
				
				#Insert values into vtiger_salesmanactivityrel
				$stmt = "INSERT INTO vtiger_salesmanactivityrel SET smid='$user_id',activityid='$activityid';";
				if ($DB) {echo "|$stmt|\n";}
				$rslt=mysql_query($stmt, $linkV);
				if ($DB) {echo "|$leadid|\n";}
				if (!$rslt) {die('Could not execute: ' . mysql_error());}
				
				#Insert values into vtiger_seactivityrel
				$stmt = "INSERT INTO vtiger_seactivityrel SET crmid='$vendor_id',activityid='$activityid';";
				if ($DB) {echo "|$stmt|\n";}
				$rslt=mysql_query($stmt, $linkV);
				if ($DB) {echo "|$leadid|\n";}
				if (!$rslt) {die('Could not execute: ' . mysql_error());}
				
				#Insert values into vtiger_crmentity
				$stmt = "INSERT INTO vtiger_crmentity (crmid, smcreatorid, smownerid, modifiedby, setype, description, createdtime, modifiedtime, viewedtime, status, version, presence, deleted) VALUES ('$activityid', '$user_id', '$user_id','$user_id', 'Calendar', 'VICIDIAL Call user $user', '$NOW_TIME', '$NOW_TIME', '$NOW_TIME', NULL, '0', '1', '0');";
				if ($DB) {echo "|$stmt|\n";}
				$rslt=mysql_query($stmt, $linkV);
				if ($DB) {echo "|$leadid|\n";}
				if (!$rslt) {die('Could not execute: ' . mysql_error());}

				#Insert values into vtiger_activity
				$stmt = "INSERT INTO vtiger_activity SET activityid='$activityid',subject='VICIDIAL Account call $vendor_id',activitytype='Call',date_start='$TODAY',due_date='$TODAY',time_start='$HHMMnow',time_end='$HHMMend',sendnotification='0',duration_hours='0',duration_minutes='1',status='',eventstatus='Held',priority='Medium',location='VICIDIAL User $user',notime='0',visibility='Public',recurringtype='--None--';";
				if ($DB) {echo "|$stmt|\n";}
				$rslt=mysql_query($stmt, $linkV);
				if ($DB) {echo "|$leadid|\n";}
				if (!$rslt) {die('Could not execute: ' . mysql_error());}

				# http://mysite.com/vtigercrm/index.php?module=Calendar&action=EditView&return_module=Accounts&return_action=DetailView&record=16&activity_mode=Events&return_id=9&parenttab=Sales
				$account_URL = "$vtiger_url/index.php?module=Calendar&action=EditView&return_module=Accounts&return_action=DetailView&record=$activityid&activity_mode=Events&return_id=$vendor_id&parenttab=Sales";
				}
			else
				{
				# http://mysite.com/vtigercrm/index.php?module=Accounts&action=DetailView&record=2&parenttab=Sales
				$account_URL = "$vtiger_url/index.php?module=Accounts&action=DetailView&record=$vendor_id&parenttab=Sales";
				}
			echo "<META HTTP-EQUIV=Refresh CONTENT=\"0; URL=$account_URL\">\n";
			echo "</head>\n";
			echo "<BODY BGCOLOR=white marginheight=0 marginwidth=0 leftmargin=0 topmargin=0\">\n";
			echo "<CENTER><FONT FACE=\"Courier\" COLOR=BLACK SIZE=3>\n";

			echo "<PRE>";
			echo "account found! ACCTID\n";
			echo "accountid:   <a href=\"$account_URL\">$vendor_id</a>\n";
			echo "</PRE><BR>";
			exit;
			}
		}
	}
##########################################################################
##### END - ACCTID -  Search in the account records for accountid number
##########################################################################


##########################################################################
##### BEGIN - ACCOUNT -   Search in the account records for phone number
##########################################################################
if ($account_search > 0)
	{
	$stmt="SELECT count(*) from vtiger_account where phone='$phone' or otherphone='$phone' or fax='$phone';";
	$rslt=mysql_query($stmt, $linkV);
	if ($DB) {echo "$stmt\n";}
	if (!$rslt) {die('Could not execute: ' . mysql_error());}
	$row=mysql_fetch_row($rslt);
	$found_count = $row[0];

	if ($DB) {echo "<BR>\nACCOUNT|$phone|$found_count|vtiger_account\n";}

	if ($found_count < 1)
		{
		echo "<!-- ACCOUNT vtiger_account not found $phone -->\n";

		$stmt="SELECT count(*) from vtiger_contactdetails where phone='$phone' or mobile='$phone' or fax='$phone';";
		$rslt=mysql_query($stmt, $linkV);
		if ($DB) {echo "$stmt\n";}
		if (!$rslt) {die('Could not execute: ' . mysql_error());}
		$row=mysql_fetch_row($rslt);
		$found_count = $row[0];

		if ($DB) {echo "<BR>\nACCOUNT|$phone|$found_count|vtiger_contactdetails\n";}

		if ($found_count < 1)
			{
			echo "<!-- ACCOUNT vtiger_contactdetails not found $phone -->\n";

			$stmt="SELECT count(*) from vtiger_contactsubdetails where homephone='$phone' or otherphone='$phone' or assistantphone='$phone';";
			$rslt=mysql_query($stmt, $linkV);
			if ($DB) {echo "$stmt\n";}
			if (!$rslt) {die('Could not execute: ' . mysql_error());}
			$row=mysql_fetch_row($rslt);
			$found_count = $row[0];

			if ($DB) {echo "<BR>\nACCOUNT|$phone|$found_count|vtiger_contactsubdetails\n";}

			if ($found_count < 1)
				{
				echo "<!-- ACCOUNT vtiger_contactsubdetails not found $phone -->\n";
				}
			else
				{
				# find vtiger_contact
				$stmt="SELECT contactsubscriptionid from vtiger_contactsubdetails where homephone='$phone' or otherphone='$phone' or assistantphone='$phone';";
				$rslt=mysql_query($stmt, $linkV);
				if ($DB) {echo "$stmt\n";}
				$row=mysql_fetch_row($rslt);
				$contactid = $row[0];

				# find vtiger_account
				$stmt="SELECT accountid from vtiger_contactdetails where contactid='$contactid';";
				$rslt=mysql_query($stmt, $linkV);
				if ($DB) {echo "$stmt\n";}
				$row=mysql_fetch_row($rslt);
				$accountid = $row[0];
				}
			}
		else
			{
			# find vtiger_account
			$stmt="SELECT accountid from vtiger_contactdetails where phone='$phone' or mobile='$phone' or fax='$phone';";
			$rslt=mysql_query($stmt, $linkV);
			if ($DB) {echo "$stmt\n";}
			$row=mysql_fetch_row($rslt);
			$accountid = $row[0];
			}
		}
	else
		{
		# find vtiger_account
		$stmt="SELECT accountid from vtiger_account where phone='$phone' or otherphone='$phone' or fax='$phone';";
		$rslt=mysql_query($stmt, $linkV);
		if ($DB) {echo "$stmt\n";}
		$row=mysql_fetch_row($rslt);
		$accountid = $row[0];
		}
	if (strlen($accountid) > 0)
		{
		$stmt="SELECT count(*) from vtiger_crmentity where crmid='$accountid' and deleted='1';";
		$rslt=mysql_query($stmt, $linkV);
		if ($DB) {echo "$stmt\n";}
		if (!$rslt) {die('Could not execute: ' . mysql_error());}
		$row=mysql_fetch_row($rslt);
		$deleted_count = $row[0];
		if ( ($deleted_count > 0) and (ereg('DISABLED',$vtiger_search_dead)) )
			{
			echo "<!-- ACCTID found but deleted $vendor_id -->\n";
			}
		else
			{
			if ( ($deleted_count > 0) and ( (ereg('RESURRECT',$vtiger_search_dead)) or (ereg('ASK',$vtiger_search_dead)) ) )
				{
				# un-delete the record
				$stmt="UPDATE vtiger_crmentity SET deleted='0' where crmid='$accountid';";
				if ($DB) {echo "$stmt\n";}
				$rslt=mysql_query($stmt, $linkV);
				if (!$rslt) {die('Could not execute: ' . mysql_error());}
				
				echo "<!-- ACCTID deleted but resurrected $accountid -->\n";
				}
			if (ereg('Y',$vtiger_create_call_record))
				{
				### Log the call in Vtiger

				#Get logged in user ID
				$stmt="SELECT id from vtiger_users where user_name='$user';";
				if ($DB) {echo "$stmt\n";}
				$rslt=mysql_query($stmt, $linkV);
				$row=mysql_fetch_row($rslt);
				$user_id = $row[0];
				if (!$rslt) {die('Could not execute: ' . mysql_error());}
				
				# Get next aviable id from vtiger_crmentity_seq to use as activityid in vtiger_crmentity	
				$stmt="SELECT id from vtiger_crmentity_seq ;";
				if ($DB) {echo "$stmt\n";}
				$rslt=mysql_query($stmt, $linkV);
				$row=mysql_fetch_row($rslt);
				$activityid = ($row[0] + 1);
				if (!$rslt) {die('Could not execute: ' . mysql_error());}

				# Increase next aviable crmid with 1 so next record gets proper id
				$stmt="UPDATE vtiger_crmentity_seq SET id = '$activityid';";
				if ($DB) {echo "$stmt\n";}
				$rslt=mysql_query($stmt, $linkV);
				if (!$rslt) {die('Could not execute: ' . mysql_error());}
				
				#Insert values into vtiger_salesmanactivityrel
				$stmt = "INSERT INTO vtiger_salesmanactivityrel SET smid='$user_id',activityid='$activityid';";
				if ($DB) {echo "|$stmt|\n";}
				$rslt=mysql_query($stmt, $linkV);
				if ($DB) {echo "|$leadid|\n";}
				if (!$rslt) {die('Could not execute: ' . mysql_error());}
				
				#Insert values into vtiger_seactivityrel
				$stmt = "INSERT INTO vtiger_seactivityrel SET crmid='$accountid',activityid='$activityid';";
				if ($DB) {echo "|$stmt|\n";}
				$rslt=mysql_query($stmt, $linkV);
				if ($DB) {echo "|$leadid|\n";}
				if (!$rslt) {die('Could not execute: ' . mysql_error());}
				
				#Insert values into vtiger_crmentity
				$stmt = "INSERT INTO vtiger_crmentity (crmid, smcreatorid, smownerid, modifiedby, setype, description, createdtime, modifiedtime, viewedtime, status, version, presence, deleted) VALUES ('$activityid', '$user_id', '$user_id','$user_id', 'Calendar', 'VICIDIAL Call user $user', '$NOW_TIME', '$NOW_TIME', '$NOW_TIME', NULL, '0', '1', '0');";
				if ($DB) {echo "|$stmt|\n";}
				$rslt=mysql_query($stmt, $linkV);
				if ($DB) {echo "|$leadid|\n";}
				if (!$rslt) {die('Could not execute: ' . mysql_error());}

				#Insert values into vtiger_activity
				$stmt = "INSERT INTO vtiger_activity SET activityid='$activityid',subject='VICIDIAL Account call $phone',activitytype='Call',date_start='$TODAY',due_date='$TODAY',time_start='$HHMMnow',time_end='$HHMMend',sendnotification='0',duration_hours='0',duration_minutes='1',status='',eventstatus='Held',priority='Medium',location='VICIDIAL User $user',notime='0',visibility='Public',recurringtype='--None--';";
				if ($DB) {echo "|$stmt|\n";}
				$rslt=mysql_query($stmt, $linkV);
				if ($DB) {echo "|$leadid|\n";}
				if (!$rslt) {die('Could not execute: ' . mysql_error());}

				# http://mysite.com/vtigercrm/index.php?module=Calendar&action=EditView&return_module=Accounts&return_action=DetailView&record=16&activity_mode=Events&return_id=9&parenttab=Sales
				$account_URL = "$vtiger_url/index.php?module=Calendar&action=EditView&return_module=Accounts&return_action=DetailView&record=$activityid&activity_mode=Events&return_id=$accountid&parenttab=Sales";
				}
			else
				{
				# http://mysite.com/vtigercrm/index.php?module=Accounts&action=DetailView&record=2&parenttab=Sales
				$account_URL = "$vtiger_url/index.php?module=Accounts&action=DetailView&record=$accountid&parenttab=Sales";
				}
			echo "<META HTTP-EQUIV=Refresh CONTENT=\"0; URL=$account_URL\">\n";
			echo "</head>\n";
			echo "<BODY BGCOLOR=white marginheight=0 marginwidth=0 leftmargin=0 topmargin=0\">\n";
			echo "<CENTER><FONT FACE=\"Courier\" COLOR=BLACK SIZE=3>\n";

			echo "<PRE>";
			echo "account found! ACCOUNT\n";
			echo "accountid:   <a href=\"$account_URL\">$accountid</a>\n";
			echo "phone:       $phone\n";
			echo "</PRE><BR>";
			exit;
			}
		}
	}
##########################################################################
##### END - ACCOUNT -   Search in the account records for phone number
##########################################################################


##########################################################################
##### BEGIN - VENDOR -  Search in the vendor records for phone number
##########################################################################
if ($vendor_search > 0)
	{
	$stmt="SELECT count(*) from vtiger_vendor where phone='$phone';";
	$rslt=mysql_query($stmt, $linkV);
	if ($DB) {echo "$stmt\n";}
	if (!$rslt) {die('Could not execute: ' . mysql_error());}
	$row=mysql_fetch_row($rslt);
	$found_count = $row[0];

	if ($DB) {echo "<BR>\nVENDOR|$phone|$found_count|\n";}

	if ($found_count < 1)
		{
		echo "<!-- VENDOR not found $phone -->\n";
		}
	else
		{
		# find vtiger_vendor
		$stmt="SELECT vendorid from vtiger_vendor where phone='$phone';";
		$rslt=mysql_query($stmt, $linkV);
		if ($DB) {echo "$stmt\n";}
		$row=mysql_fetch_row($rslt);
		$vendorid = $row[0];

		# http://mysite.com/vtigercrm/index.php?module=Vendors&action=DetailView&record=2&parenttab=Inventory
		$account_URL = "$vtiger_url/index.php?module=Vendors&action=DetailView&record=$vendorid&parenttab=Inventory";
		echo "<META HTTP-EQUIV=Refresh CONTENT=\"0; URL=$account_URL\">\n";
		echo "</head>\n";
		echo "<BODY BGCOLOR=white marginheight=0 marginwidth=0 leftmargin=0 topmargin=0\">\n";
		echo "<CENTER><FONT FACE=\"Courier\" COLOR=BLACK SIZE=3>\n";

		echo "<PRE>";
		echo "account found! VENDOR\n";
		echo "vendorid:   <a href=\"$account_URL\">$vendorid</a>\n";
		echo "phone:       $phone\n";
		echo "</PRE><BR>";
		exit;
		}
	}
##########################################################################
##### END - VENDOR -  Search in the vendor records for phone number
##########################################################################


##########################################################################
##### BEGIN - LEAD -     Search in the leads records for phone number
##########################################################################
if ($lead_search > 0)
	{
	$stmt="SELECT count(*) from vtiger_leadaddress where phone='$phone' or mobile='$phone' or fax='$phone';";
	$rslt=mysql_query($stmt, $linkV);
	if ($DB) {echo "$stmt\n";}
	if (!$rslt) {die('Could not execute: ' . mysql_error());}
	$row=mysql_fetch_row($rslt);
	$found_count = $row[0];

	if ($DB) {echo "<BR>\nLEAD|$phone|$found_count|\n";}

	if ($found_count < 1)
		{
		echo "<!-- LEAD not found $phone -->\n";
		if (ereg('Y',$vtiger_create_lead_record))
			{
			echo "</head>\n";
			echo "<BODY BGCOLOR=white marginheight=0 marginwidth=0 leftmargin=0 topmargin=0\">\n";
			echo "<CENTER><FONT FACE=\"Courier\" COLOR=BLACK SIZE=3>\n";
			echo "$phone not found, creating account...\n";

			$DB=1;

			#Get logged in user ID
			if ($DB) {echo "<PRE>";}
			$stmt="SELECT id from vtiger_users where user_name='$user';";
			if ($DB) {echo "$stmt\n";}
			$rslt=mysql_query($stmt, $linkV);
			$row=mysql_fetch_row($rslt);
			$user_id = $row[0];
			if (!$rslt) {die('Could not execute: ' . mysql_error());}
			
			#Vtiger no longer use auto increment for vtiger_crmentity crmid, vtiger_crmentity_seq is used instead to list next aviable entity ID
			# Get next aviable id to use as  crmid in vtiger_crmentity	
			$stmt="SELECT id from vtiger_crmentity_seq ;";
			if ($DB) {echo "$stmt\n";}
			$rslt=mysql_query($stmt, $linkV);
			$row=mysql_fetch_row($rslt);
			$leadid = ($row[0] + 1);
			if (!$rslt) {die('Could not execute: ' . mysql_error());}

			# Increase 	next aviable crmid with 1 so next record gets proper id
			$stmt="UPDATE vtiger_crmentity_seq SET id = '$leadid';";
			if ($DB) {echo "$stmt\n";}
			$rslt=mysql_query($stmt, $linkV);
			if (!$rslt) {die('Could not execute: ' . mysql_error());}
			
			#Insert values into vtiger_crmentity
			$stmt = "INSERT INTO vtiger_crmentity (crmid, smcreatorid, smownerid, modifiedby, setype, description, createdtime, modifiedtime, viewedtime, status, version, presence, deleted) VALUES ('$leadid', '$user_id', '$user_id','$user_id', 'Leads', '(Memo)', '$NOW_TIME', '$NOW_TIME', '$NOW_TIME', NULL, '0', '1', '0');";
			if ($DB) {echo "|$stmt|\n";}
			$rslt=mysql_query($stmt, $linkV);
			if ($DB) {echo "|$leadid|\n";}
			if (!$rslt) {die('Could not execute: ' . mysql_error());}

			#Insert values into vtiger_leaddetails	
			$stmt = "INSERT INTO vtiger_leaddetails (leadid,firstname,lastname,company) values('$leadid','$first_name','$last_name','$first_name $last_name');";
			if ($DB) {echo "|$stmt|\n";}
			$rslt=mysql_query($stmt, $linkV);
			if (!$rslt) {die('Could not execute: ' . mysql_error());}

			#Insert values into vtiger_leaddetails
			$stmt = "INSERT INTO vtiger_leadaddress (leadaddressid,city,code,state,country,phone,mobile,lane) values('$leadid','$city','$postal_code','$province','$country','$phone','$alt_phone','$address1 $address2');";
			if ($DB) {echo "|$stmt|\n";}
			$rslt=mysql_query($stmt, $linkV);
			if (!$rslt) {die('Could not execute: ' . mysql_error());}

			#Insert values into vtiger_leadsubdetails	
			$stmt = "INSERT INTO vtiger_leadsubdetails (leadsubscriptionid) VALUES ('$leadid');";
			if ($DB) {echo "|$stmt|\n";}
			$rslt=mysql_query($stmt, $linkV);
			if (!$rslt) {die('Could not execute: ' . mysql_error());}
			
			#Insert values into vtiger_leadscf, these are custom created fields example	
			$stmt = "INSERT INTO vtiger_leadscf (leadid) VALUES ('$leadid');";
			if ($DB) {echo "|$stmt|\n";}
			$rslt=mysql_query($stmt, $linkV);
			if (!$rslt) {die('Could not execute: ' . mysql_error());}

			if ($DB) {echo "DONE creating lead records\n";}

			if (ereg('Y',$vtiger_create_call_record))
				{
				### Log the call in Vtiger

				#Get logged in user ID
				$stmt="SELECT id from vtiger_users where user_name='$user';";
				if ($DB) {echo "$stmt\n";}
				$rslt=mysql_query($stmt, $linkV);
				$row=mysql_fetch_row($rslt);
				$user_id = $row[0];
				if (!$rslt) {die('Could not execute: ' . mysql_error());}
				
				# Get next aviable id from vtiger_crmentity_seq to use as activityid in vtiger_crmentity	
				$stmt="SELECT id from vtiger_crmentity_seq ;";
				if ($DB) {echo "$stmt\n";}
				$rslt=mysql_query($stmt, $linkV);
				$row=mysql_fetch_row($rslt);
				$activityid = ($row[0] + 1);
				if (!$rslt) {die('Could not execute: ' . mysql_error());}

				# Increase next aviable crmid with 1 so next record gets proper id
				$stmt="UPDATE vtiger_crmentity_seq SET id = '$activityid';";
				if ($DB) {echo "$stmt\n";}
				$rslt=mysql_query($stmt, $linkV);
				if (!$rslt) {die('Could not execute: ' . mysql_error());}
				
				#Insert values into vtiger_salesmanactivityrel
				$stmt = "INSERT INTO vtiger_salesmanactivityrel SET smid='$user_id',activityid='$activityid';";
				if ($DB) {echo "|$stmt|\n";}
				$rslt=mysql_query($stmt, $linkV);
				if ($DB) {echo "|$leadid|\n";}
				if (!$rslt) {die('Could not execute: ' . mysql_error());}
				
				#Insert values into vtiger_seactivityrel
				$stmt = "INSERT INTO vtiger_seactivityrel SET crmid='$leadid',activityid='$activityid';";
				if ($DB) {echo "|$stmt|\n";}
				$rslt=mysql_query($stmt, $linkV);
				if ($DB) {echo "|$leadid|\n";}
				if (!$rslt) {die('Could not execute: ' . mysql_error());}
				
				#Insert values into vtiger_crmentity
				$stmt = "INSERT INTO vtiger_crmentity (crmid, smcreatorid, smownerid, modifiedby, setype, description, createdtime, modifiedtime, viewedtime, status, version, presence, deleted) VALUES ('$activityid', '$user_id', '$user_id','$user_id', 'Calendar', 'VICIDIAL Call user $user', '$NOW_TIME', '$NOW_TIME', '$NOW_TIME', NULL, '0', '1', '0');";
				if ($DB) {echo "|$stmt|\n";}
				$rslt=mysql_query($stmt, $linkV);
				if ($DB) {echo "|$leadid|\n";}
				if (!$rslt) {die('Could not execute: ' . mysql_error());}

				#Insert values into vtiger_activity
				$stmt = "INSERT INTO vtiger_activity SET activityid='$activityid',subject='VICIDIAL Lead call $phone',activitytype='Call',date_start='$TODAY',due_date='$TODAY',time_start='$HHMMnow',time_end='$HHMMend',sendnotification='0',duration_hours='0',duration_minutes='1',status='',eventstatus='Held',priority='Medium',location='VICIDIAL user $user',notime='0',visibility='Public',recurringtype='--None--';";
				if ($DB) {echo "|$stmt|\n";}
				$rslt=mysql_query($stmt, $linkV);
				if ($DB) {echo "|$leadid|\n";}
				if (!$rslt) {die('Could not execute: ' . mysql_error());}


				# http://mysite.com/vtigercrm/index.php?module=Calendar&action=EditView&return_module=Accounts&return_action=DetailView&record=16&activity_mode=Events&return_id=9&parenttab=Sales
				$account_URL = "$vtiger_url/index.php?module=Calendar&action=EditView&return_module=Leads&return_action=DetailView&record=$activityid&activity_mode=Events&return_id=$leadid&parenttab=Sales";
				}
			else
				{
				# http://mysite.com/vtigercrm/index.php?module=Accounts&action=EditView&record=2&parenttab=Sales
				$account_URL = "$vtiger_url/index.php?module=Leads&action=EditView&record=$leadid&parenttab=Sales";
				}

			echo "<META HTTP-EQUIV=Refresh CONTENT=\"0; URL=$account_URL\">\n";
			echo "</head>\n";
			echo "<BODY BGCOLOR=white marginheight=0 marginwidth=0 leftmargin=0 topmargin=0\">\n";
			echo "<CENTER><FONT FACE=\"Courier\" COLOR=BLACK SIZE=3>\n";

			echo "<PRE>";
			echo "account created! LEAD\n";
			echo "leadid:   <a href=\"$account_URL\">$leadid</a>\n";
			echo "phone:       $phone\n";
			echo "</PRE><BR>";
			exit;
			}
		}
	else
		{
		$stmt="SELECT leadaddressid from vtiger_leadaddress where phone='$phone' or mobile='$phone' or fax='$phone';";
		$rslt=mysql_query($stmt, $linkV);
		if ($DB) {echo "$stmt\n";}
		$row=mysql_fetch_row($rslt);
		$leadid = $row[0];

		$stmt="SELECT count(*) from vtiger_crmentity where crmid='$leadid' and deleted='1';";
		$rslt=mysql_query($stmt, $linkV);
		if ($DB) {echo "$stmt\n";}
		if (!$rslt) {die('Could not execute: ' . mysql_error());}
		$row=mysql_fetch_row($rslt);
		$deleted_count = $row[0];
		if ( ($deleted_count > 0) and (ereg('DISABLED',$vtiger_search_dead)) )
			{
			echo "<!-- LEADID found but deleted $leadid -->\n";
			}
		else
			{
			if ( ($deleted_count > 0) and ( (ereg('RESURRECT',$vtiger_search_dead)) or (ereg('ASK',$vtiger_search_dead)) ) )
				{
				# un-delete the record
				$stmt="UPDATE vtiger_crmentity SET deleted='0' where crmid='$leadid';";
				if ($DB) {echo "$stmt\n";}
				$rslt=mysql_query($stmt, $linkV);
				if (!$rslt) {die('Could not execute: ' . mysql_error());}
				
				echo "<!-- LEADID deleted but resurrected $leadid -->\n";
				}

			if (ereg('Y',$vtiger_create_call_record))
				{
				### Log the call in Vtiger

				#Get logged in user ID
				$stmt="SELECT id from vtiger_users where user_name='$user';";
				if ($DB) {echo "$stmt\n";}
				$rslt=mysql_query($stmt, $linkV);
				$row=mysql_fetch_row($rslt);
				$user_id = $row[0];
				if (!$rslt) {die('Could not execute: ' . mysql_error());}
				
				# Get next aviable id from vtiger_crmentity_seq to use as activityid in vtiger_crmentity	
				$stmt="SELECT id from vtiger_crmentity_seq ;";
				if ($DB) {echo "$stmt\n";}
				$rslt=mysql_query($stmt, $linkV);
				$row=mysql_fetch_row($rslt);
				$activityid = ($row[0] + 1);
				if (!$rslt) {die('Could not execute: ' . mysql_error());}

				# Increase next aviable crmid with 1 so next record gets proper id
				$stmt="UPDATE vtiger_crmentity_seq SET id = '$activityid';";
				if ($DB) {echo "$stmt\n";}
				$rslt=mysql_query($stmt, $linkV);
				if (!$rslt) {die('Could not execute: ' . mysql_error());}
				
				#Insert values into vtiger_salesmanactivityrel
				$stmt = "INSERT INTO vtiger_salesmanactivityrel SET smid='$user_id',activityid='$activityid';";
				if ($DB) {echo "|$stmt|\n";}
				$rslt=mysql_query($stmt, $linkV);
				if ($DB) {echo "|$leadid|\n";}
				if (!$rslt) {die('Could not execute: ' . mysql_error());}
				
				#Insert values into vtiger_seactivityrel
				$stmt = "INSERT INTO vtiger_seactivityrel SET crmid='$leadid',activityid='$activityid';";
				if ($DB) {echo "|$stmt|\n";}
				$rslt=mysql_query($stmt, $linkV);
				if ($DB) {echo "|$leadid|\n";}
				if (!$rslt) {die('Could not execute: ' . mysql_error());}
				
				#Insert values into vtiger_crmentity
				$stmt = "INSERT INTO vtiger_crmentity (crmid, smcreatorid, smownerid, modifiedby, setype, description, createdtime, modifiedtime, viewedtime, status, version, presence, deleted) VALUES ('$activityid', '$user_id', '$user_id','$user_id', 'Calendar', 'VICIDIAL Call user $user', '$NOW_TIME', '$NOW_TIME', '$NOW_TIME', NULL, '0', '1', '0');";
				if ($DB) {echo "|$stmt|\n";}
				$rslt=mysql_query($stmt, $linkV);
				if ($DB) {echo "|$leadid|\n";}
				if (!$rslt) {die('Could not execute: ' . mysql_error());}

				#Insert values into vtiger_activity
				$stmt = "INSERT INTO vtiger_activity SET activityid='$activityid',subject='VICIDIAL Lead call $phone',activitytype='Call',date_start='$TODAY',due_date='$TODAY',time_start='$HHMMnow',time_end='$HHMMend',sendnotification='0',duration_hours='0',duration_minutes='1',status='',eventstatus='Held',priority='Medium',location='VICIDIAL user $user',notime='0',visibility='Public',recurringtype='--None--';";
				if ($DB) {echo "|$stmt|\n";}
				$rslt=mysql_query($stmt, $linkV);
				if ($DB) {echo "|$leadid|\n";}
				if (!$rslt) {die('Could not execute: ' . mysql_error());}


				# http://mysite.com/vtigercrm/index.php?module=Calendar&action=EditView&return_module=Accounts&return_action=DetailView&record=16&activity_mode=Events&return_id=9&parenttab=Sales
				$account_URL = "$vtiger_url/index.php?module=Calendar&action=EditView&return_module=Leads&return_action=DetailView&record=$activityid&activity_mode=Events&return_id=$leadid&parenttab=Sales";
				}
			else
				{
				# http://mysite.com/vtigercrm/index.php?module=Accounts&action=DetailView&record=2&parenttab=Sales
				$account_URL = "$vtiger_url/index.php?module=Leads&action=DetailView&record=$leadid&parenttab=Sales";
				}
			echo "<META HTTP-EQUIV=Refresh CONTENT=\"0; URL=$account_URL\">\n";
			echo "</head>\n";
			echo "<BODY BGCOLOR=white marginheight=0 marginwidth=0 leftmargin=0 topmargin=0\">\n";
			echo "<CENTER><FONT FACE=\"Courier\" COLOR=BLACK SIZE=3>\n";

			echo "<PRE>";
			echo "lead found! LEAD\n";
			echo "leadid:   <a href=\"$account_URL\">$leadid</a>\n";
			echo "phone:       $phone\n";
			echo "</PRE><BR>";
			exit;
			}
		}
	}
##########################################################################
##### END - LEAD -     Search in the leads records for phone number
##########################################################################




$ENDtime = date("U");

$RUNtime = ($ENDtime - $STARTtime);

echo "\n\n\n<br>$phone NOT FOUND<br><br>\n\n";
echo "<a href=\"$vtiger_url\">Click here to go to the Vtiger home page</a>\n";

# echo "<font size=0>\n\n\n<br><br><br>\nscript runtime: $RUNtime seconds</font>";


?>


</body>
</html>

<?php
	
exit; 



?>





