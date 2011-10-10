<?php
# vdc_call_url_test.php
# 
# Copyright (C) 2010  Matt Florell <vicidial@gmail.com>    LICENSE: AGPLv2
#
# This script is designed display and log the start_call_url and dispo_call_url
# queries if activated
#
# CHANGELOG:
# 100103-1317 - First build of script
# 100113-2023 - Added dispo_name
#

$version = '2.2.0-2';
$build = '100113-2023';

require("dbconnect.php");

$query_string = getenv("QUERY_STRING");

if (isset($_GET["lead_id"]))	{$lead_id=$_GET["lead_id"];}
	elseif (isset($_POST["lead_id"]))	{$lead_id=$_POST["lead_id"];}
if (isset($_GET["vendor_id"]))	{$vendor_id=$_GET["vendor_id"];}
	elseif (isset($_POST["vendor_id"]))	{$vendor_id=$_POST["vendor_id"];}
	$vendor_lead_code = $vendor_id;
if (isset($_GET["list_id"]))	{$list_id=$_GET["list_id"];}
	elseif (isset($_POST["list_id"]))	{$list_id=$_POST["list_id"];}
if (isset($_GET["gmt_offset_now"]))	{$gmt_offset_now=$_GET["gmt_offset_now"];}
	elseif (isset($_POST["gmt_offset_now"]))	{$gmt_offset_now=$_POST["gmt_offset_now"];}
if (isset($_GET["phone_code"]))	{$phone_code=$_GET["phone_code"];}
	elseif (isset($_POST["phone_code"]))	{$phone_code=$_POST["phone_code"];}
if (isset($_GET["phone_number"]))	{$phone_number=$_GET["phone_number"];}
	elseif (isset($_POST["phone_number"]))	{$phone_number=$_POST["phone_number"];}
if (isset($_GET["title"]))	{$title=$_GET["title"];}
	elseif (isset($_POST["title"]))	{$title=$_POST["title"];}
if (isset($_GET["first_name"]))	{$first_name=$_GET["first_name"];}
	elseif (isset($_POST["first_name"]))	{$first_name=$_POST["first_name"];}
if (isset($_GET["middle_initial"]))	{$middle_initial=$_GET["middle_initial"];}
	elseif (isset($_POST["middle_initial"]))	{$middle_initial=$_POST["middle_initial"];}
if (isset($_GET["last_name"]))	{$last_name=$_GET["last_name"];}
	elseif (isset($_POST["last_name"]))	{$last_name=$_POST["last_name"];}
if (isset($_GET["address1"]))	{$address1=$_GET["address1"];}
	elseif (isset($_POST["address1"]))	{$address1=$_POST["address1"];}
if (isset($_GET["address2"]))	{$address2=$_GET["address2"];}
	elseif (isset($_POST["address2"]))	{$address2=$_POST["address2"];}
if (isset($_GET["address3"]))	{$address3=$_GET["address3"];}
	elseif (isset($_POST["address3"]))	{$address3=$_POST["address3"];}
if (isset($_GET["city"]))	{$city=$_GET["city"];}
	elseif (isset($_POST["city"]))	{$city=$_POST["city"];}
if (isset($_GET["state"]))	{$state=$_GET["state"];}
	elseif (isset($_POST["state"]))	{$state=$_POST["state"];}
if (isset($_GET["province"]))	{$province=$_GET["province"];}
	elseif (isset($_POST["province"]))	{$province=$_POST["province"];}
if (isset($_GET["postal_code"]))	{$postal_code=$_GET["postal_code"];}
	elseif (isset($_POST["postal_code"]))	{$postal_code=$_POST["postal_code"];}
if (isset($_GET["country_code"]))	{$country_code=$_GET["country_code"];}
	elseif (isset($_POST["country_code"]))	{$country_code=$_POST["country_code"];}
if (isset($_GET["gender"]))	{$gender=$_GET["gender"];}
	elseif (isset($_POST["gender"]))	{$gender=$_POST["gender"];}
if (isset($_GET["date_of_birth"]))	{$date_of_birth=$_GET["date_of_birth"];}
	elseif (isset($_POST["date_of_birth"]))	{$date_of_birth=$_POST["date_of_birth"];}
if (isset($_GET["alt_phone"]))	{$alt_phone=$_GET["alt_phone"];}
	elseif (isset($_POST["alt_phone"]))	{$alt_phone=$_POST["alt_phone"];}
if (isset($_GET["email"]))	{$email=$_GET["email"];}
	elseif (isset($_POST["email"]))	{$email=$_POST["email"];}
if (isset($_GET["security_phrase"]))	{$security_phrase=$_GET["security_phrase"];}
	elseif (isset($_POST["security_phrase"]))	{$security_phrase=$_POST["security_phrase"];}
if (isset($_GET["comments"]))	{$comments=$_GET["comments"];}
	elseif (isset($_POST["comments"]))	{$comments=$_POST["comments"];}
if (isset($_GET["user"]))	{$user=$_GET["user"];}
	elseif (isset($_POST["user"]))	{$user=$_POST["user"];}
if (isset($_GET["pass"]))	{$pass=$_GET["pass"];}
	elseif (isset($_POST["pass"]))	{$pass=$_POST["pass"];}
if (isset($_GET["campaign"]))	{$campaign=$_GET["campaign"];}
	elseif (isset($_POST["campaign"]))	{$campaign=$_POST["campaign"];}
if (isset($_GET["phone_login"]))	{$phone_login=$_GET["phone_login"];}
	elseif (isset($_POST["phone_login"]))	{$phone_login=$_POST["phone_login"];}
if (isset($_GET["original_phone_login"]))	{$original_phone_login=$_GET["original_phone_login"];}
	elseif (isset($_POST["original_phone_login"]))	{$original_phone_login=$_POST["original_phone_login"];}
if (isset($_GET["phone_pass"]))	{$phone_pass=$_GET["phone_pass"];}
	elseif (isset($_POST["phone_pass"]))	{$phone_pass=$_POST["phone_pass"];}
if (isset($_GET["fronter"]))	{$fronter=$_GET["fronter"];}
	elseif (isset($_POST["fronter"]))	{$fronter=$_POST["fronter"];}
if (isset($_GET["closer"]))	{$closer=$_GET["closer"];}
	elseif (isset($_POST["closer"]))	{$closer=$_POST["closer"];}
if (isset($_GET["group"]))	{$group=$_GET["group"];}
	elseif (isset($_POST["group"]))	{$group=$_POST["group"];}
if (isset($_GET["channel_group"]))	{$channel_group=$_GET["channel_group"];}
	elseif (isset($_POST["channel_group"]))	{$channel_group=$_POST["channel_group"];}
if (isset($_GET["SQLdate"]))	{$SQLdate=$_GET["SQLdate"];}
	elseif (isset($_POST["SQLdate"]))	{$SQLdate=$_POST["SQLdate"];}
if (isset($_GET["epoch"]))	{$epoch=$_GET["epoch"];}
	elseif (isset($_POST["epoch"]))	{$epoch=$_POST["epoch"];}
if (isset($_GET["uniqueid"]))	{$uniqueid=$_GET["uniqueid"];}
	elseif (isset($_POST["uniqueid"]))	{$uniqueid=$_POST["uniqueid"];}
if (isset($_GET["customer_zap_channel"]))	{$customer_zap_channel=$_GET["customer_zap_channel"];}
	elseif (isset($_POST["customer_zap_channel"]))	{$customer_zap_channel=$_POST["customer_zap_channel"];}
if (isset($_GET["customer_server_ip"]))	{$customer_server_ip=$_GET["customer_server_ip"];}
	elseif (isset($_POST["customer_server_ip"]))	{$customer_server_ip=$_POST["customer_server_ip"];}
if (isset($_GET["server_ip"]))	{$server_ip=$_GET["server_ip"];}
	elseif (isset($_POST["server_ip"]))	{$server_ip=$_POST["server_ip"];}
if (isset($_GET["SIPexten"]))	{$SIPexten=$_GET["SIPexten"];}
	elseif (isset($_POST["SIPexten"]))	{$SIPexten=$_POST["SIPexten"];}
if (isset($_GET["session_id"]))	{$session_id=$_GET["session_id"];}
	elseif (isset($_POST["session_id"]))	{$session_id=$_POST["session_id"];}
if (isset($_GET["phone"]))	{$phone=$_GET["phone"];}
	elseif (isset($_POST["phone"]))	{$phone=$_POST["phone"];}
if (isset($_GET["parked_by"]))	{$parked_by=$_GET["parked_by"];}
	elseif (isset($_POST["parked_by"]))	{$parked_by=$_POST["parked_by"];}
if (isset($_GET["dispo"]))	{$dispo=$_GET["dispo"];}
	elseif (isset($_POST["dispo"]))	{$dispo=$_POST["dispo"];}
if (isset($_GET["dialed_number"]))	{$dialed_number=$_GET["dialed_number"];}
	elseif (isset($_POST["dialed_number"]))	{$dialed_number=$_POST["dialed_number"];}
if (isset($_GET["dialed_label"]))	{$dialed_label=$_GET["dialed_label"];}
	elseif (isset($_POST["dialed_label"]))	{$dialed_label=$_POST["dialed_label"];}
if (isset($_GET["source_id"]))	{$source_id=$_GET["source_id"];}
	elseif (isset($_POST["source_id"]))	{$source_id=$_POST["source_id"];}
if (isset($_GET["rank"]))	{$rank=$_GET["rank"];}
	elseif (isset($_POST["rank"]))	{$rank=$_POST["rank"];}
if (isset($_GET["owner"]))	{$owner=$_GET["owner"];}
	elseif (isset($_POST["owner"]))	{$owner=$_POST["owner"];}
if (isset($_GET["camp_script"]))	{$camp_script=$_GET["camp_script"];}
	elseif (isset($_POST["camp_script"]))	{$camp_script=$_POST["camp_script"];}
if (isset($_GET["in_script"]))	{$in_script=$_GET["in_script"];}
	elseif (isset($_POST["in_script"]))	{$in_script=$_POST["in_script"];}
if (isset($_GET["script_width"]))	{$script_width=$_GET["script_width"];}
	elseif (isset($_POST["script_width"]))	{$script_width=$_POST["script_width"];}
if (isset($_GET["script_height"]))	{$script_height=$_GET["script_height"];}
	elseif (isset($_POST["script_height"]))	{$script_height=$_POST["script_height"];}
if (isset($_GET["fullname"]))	{$fullname=$_GET["fullname"];}
	elseif (isset($_POST["fullname"]))	{$fullname=$_POST["fullname"];}
if (isset($_GET["recording_filename"]))	{$recording_filename=$_GET["recording_filename"];}
	elseif (isset($_POST["recording_filename"]))	{$recording_filename=$_POST["recording_filename"];}
if (isset($_GET["recording_id"]))	{$recording_id=$_GET["recording_id"];}
	elseif (isset($_POST["recording_id"]))	{$recording_id=$_POST["recording_id"];}
if (isset($_GET["user_custom_one"]))	{$user_custom_one=$_GET["user_custom_one"];}
	elseif (isset($_POST["user_custom_one"]))	{$user_custom_one=$_POST["user_custom_one"];}
if (isset($_GET["user_custom_two"]))	{$user_custom_two=$_GET["user_custom_two"];}
	elseif (isset($_POST["user_custom_two"]))	{$user_custom_two=$_POST["user_custom_two"];}
if (isset($_GET["user_custom_three"]))	{$user_custom_three=$_GET["user_custom_three"];}
	elseif (isset($_POST["user_custom_three"]))	{$user_custom_three=$_POST["user_custom_three"];}
if (isset($_GET["user_custom_four"]))	{$user_custom_four=$_GET["user_custom_four"];}
	elseif (isset($_POST["user_custom_four"]))	{$user_custom_four=$_POST["user_custom_four"];}
if (isset($_GET["user_custom_five"]))	{$user_custom_five=$_GET["user_custom_five"];}
	elseif (isset($_POST["user_custom_five"]))	{$user_custom_five=$_POST["user_custom_five"];}
if (isset($_GET["talk_time"]))	{$talk_time=$_GET["talk_time"];}
	elseif (isset($_POST["talk_time"]))	{$talk_time=$_POST["talk_time"];}
if (isset($_GET["talk_time_min"]))	{$talk_time_min=$_GET["talk_time_min"];}
	elseif (isset($_POST["talk_time_min"]))	{$talk_time_min=$_POST["talk_time_min"];}
if (isset($_GET["agent_log_id"]))	{$agent_log_id=$_GET["agent_log_id"];}
	elseif (isset($_POST["agent_log_id"]))	{$agent_log_id=$_POST["agent_log_id"];}
if (isset($_GET["dispo_name"]))	{$dispo_name=$_GET["dispo_name"];}
	elseif (isset($_POST["dispo_name"]))	{$dispo_name=$_POST["dispo_name"];}

header ("Content-type: text/html; charset=utf-8");
header ("Cache-Control: no-cache, must-revalidate");  // HTTP/1.1
header ("Pragma: no-cache");                          // HTTP/1.0

$txt = '.txt';
$StarTtime = date("U");
$NOW_DATE = date("Y-m-d");
$NOW_TIME = date("Y-m-d H:i:s");
$CIDdate = date("mdHis");
$ENTRYdate = date("YmdHis");
$MT[0]='';
$agents='@agents';

$IFRAME=0;

#############################################
##### START SYSTEM_SETTINGS LOOKUP #####
$stmt = "SELECT use_non_latin,timeclock_end_of_day,agentonly_callback_campaign_lock FROM system_settings;";
$rslt=mysql_query($stmt, $link);
if ($DB) {echo "$stmt\n";}
$qm_conf_ct = mysql_num_rows($rslt);
if ($qm_conf_ct > 0)
	{
	$row=mysql_fetch_row($rslt);
	$non_latin =							$row[0];
	$timeclock_end_of_day =					$row[1];
	$agentonly_callback_campaign_lock =		$row[2];
	}
##### END SETTINGS LOOKUP #####
###########################################

if ($non_latin < 1)
	{
	$user=ereg_replace("[^-_0-9a-zA-Z]","",$user);
	$pass=ereg_replace("[^-_0-9a-zA-Z]","",$pass);
	$length_in_sec = ereg_replace("[^0-9]","",$length_in_sec);
	$phone_code = ereg_replace("[^0-9]","",$phone_code);
	$phone_number = ereg_replace("[^0-9]","",$phone_number);
	}
else
	{
	$user = ereg_replace("'|\"|\\\\|;","",$user);
	$pass = ereg_replace("'|\"|\\\\|;","",$pass);
	}


# default optional vars if not set
if (!isset($format))   {$format="text";}
	if ($format == 'debug')	{$DB=1;}
if (!isset($ACTION))   {$ACTION="refresh";}
if (!isset($query_date)) {$query_date = $NOW_DATE;}

if ($format=='debug')
	{
	echo "<html>\n";
	echo "<head>\n";
	echo "<!-- VERSION: $version     BUILD: $build    USER: $user   server_ip: $server_ip-->\n";
	echo "<title>VICIDiaL Script Display Script";
	echo "</title>\n";
	echo "</head>\n";
	echo "<BODY BGCOLOR=white marginheight=0 marginwidth=0 leftmargin=0 topmargin=0>\n";
	}

$output = '';
$output .= "$lead_id|";
$output .= "$vendor_id|";
$output .= "$vendor_lead_code|";
$output .= "$list_id|";
$output .= "$gmt_offset_now|";
$output .= "$phone_code|";
$output .= "$phone_number|";
$output .= "$title|";
$output .= "$first_name|";
$output .= "$middle_initial|";
$output .= "$last_name|";
$output .= "$address1|";
$output .= "$address2|";
$output .= "$address3|";
$output .= "$city|";
$output .= "$state|";
$output .= "$province|";
$output .= "$postal_code|";
$output .= "$country_code|";
$output .= "$gender|";
$output .= "$date_of_birth|";
$output .= "$alt_phone|";
$output .= "$email|";
$output .= "$security_phrase|";
$output .= "$comments|";
$output .= "$user|";
$output .= "$pass|";
$output .= "$campaign|";
$output .= "$phone_login|";
$output .= "$original_phone_login|";
$output .= "$phone_pass|";
$output .= "$fronter|";
$output .= "$closer|";
$output .= "$group|";
$output .= "$channel_group|";
$output .= "$SQLdate|";
$output .= "$epoch|";
$output .= "$uniqueid|";
$output .= "$customer_zap_channel|";
$output .= "$customer_server_ip|";
$output .= "$server_ip|";
$output .= "$SIPexten|";
$output .= "$session_id|";
$output .= "$phone|";
$output .= "$parked_by|";
$output .= "$dispo|";
$output .= "$dialed_number|";
$output .= "$dialed_label|";
$output .= "$source_id|";
$output .= "$rank|";
$output .= "$owner|";
$output .= "$camp_script|";
$output .= "$in_script|";
$output .= "$script_width|";
$output .= "$script_height|";
$output .= "$fullname|";
$output .= "$recording_filename|";
$output .= "$recording_id|";
$output .= "$user_custom_one|";
$output .= "$user_custom_two|";
$output .= "$user_custom_three|";
$output .= "$user_custom_four|";
$output .= "$user_custom_five|";
$output .= "$talk_time|";
$output .= "$talk_time_min|";
$output .= "$agent_log_id|";
$output .= "$dispo_name|";

echo "$output\n$query_string\n";

$fp = fopen ("./call_url_log.txt", "a");
fwrite ($fp, "$output|$query_string\n");
fclose($fp);

exit;

?>
