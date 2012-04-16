<?php
# vdc_script_display.php
# 
# Copyright (C) 2011  Matt Florell <vicidial@gmail.com>    LICENSE: AGPLv2
#
# This script is designed display the contents of the SCRIPT tab in the agent interface
#
# CHANGELOG:
# 90824-1435 - First build of script
# 90827-1548 - Added list override script option
# 91204-1913 - Added recording_filename and recording_id variables
# 91211-1103 - Added user_custom_... variables
# 100116-0702 - Added preset variables
# 100127-1611 - Added ignore_list_script_override option
# 100823-1644 - Added DID variables
# 100902-1344 - Added closecallid, xfercallid, agent_log_id variables
# 110420-1201 - Added web_vars variable
#

$version = '2.4-9';
$build = '110420-1201';

require("dbconnect.php");


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
if (isset($_GET["preset_number_a"]))	{$preset_number_a=$_GET["preset_number_a"];}
	elseif (isset($_POST["preset_number_a"]))	{$preset_number_a=$_POST["preset_number_a"];}
if (isset($_GET["preset_number_b"]))	{$preset_number_b=$_GET["preset_number_b"];}
	elseif (isset($_POST["preset_number_b"]))	{$preset_number_b=$_POST["preset_number_b"];}
if (isset($_GET["preset_number_c"]))	{$preset_number_c=$_GET["preset_number_c"];}
	elseif (isset($_POST["preset_number_c"]))	{$preset_number_c=$_POST["preset_number_c"];}
if (isset($_GET["preset_number_d"]))	{$preset_number_d=$_GET["preset_number_d"];}
	elseif (isset($_POST["preset_number_d"]))	{$preset_number_d=$_POST["preset_number_d"];}
if (isset($_GET["preset_number_e"]))	{$preset_number_e=$_GET["preset_number_e"];}
	elseif (isset($_POST["preset_number_e"]))	{$preset_number_e=$_POST["preset_number_e"];}
if (isset($_GET["preset_number_f"]))	{$preset_number_f=$_GET["preset_number_f"];}
	elseif (isset($_POST["preset_number_f"]))	{$preset_number_f=$_POST["preset_number_f"];}
if (isset($_GET["preset_dtmf_a"]))	{$preset_dtmf_a=$_GET["preset_dtmf_a"];}
	elseif (isset($_POST["preset_dtmf_a"]))	{$preset_dtmf_a=$_POST["preset_dtmf_a"];}
if (isset($_GET["preset_dtmf_b"]))	{$preset_dtmf_b=$_GET["preset_dtmf_b"];}
	elseif (isset($_POST["preset_dtmf_b"]))	{$preset_dtmf_b=$_POST["preset_dtmf_b"];}
if (isset($_GET["did_id"]))				{$did_id=$_GET["did_id"];}
	elseif (isset($_POST["did_id"]))	{$did_id=$_POST["did_id"];}
if (isset($_GET["did_extension"]))			{$did_extension=$_GET["did_extension"];}
	elseif (isset($_POST["did_extension"]))	{$did_extension=$_POST["did_extension"];}
if (isset($_GET["did_pattern"]))			{$did_pattern=$_GET["did_pattern"];}
	elseif (isset($_POST["did_pattern"]))	{$did_pattern=$_POST["did_pattern"];}
if (isset($_GET["did_description"]))			{$did_description=$_GET["did_description"];}
	elseif (isset($_POST["did_description"]))	{$did_description=$_POST["did_description"];}
if (isset($_GET["closecallid"]))			{$closecallid=$_GET["closecallid"];}
	elseif (isset($_POST["closecallid"]))	{$closecallid=$_POST["closecallid"];}
if (isset($_GET["xfercallid"]))				{$xfercallid=$_GET["xfercallid"];}
	elseif (isset($_POST["xfercallid"]))	{$xfercallid=$_POST["xfercallid"];}
if (isset($_GET["agent_log_id"]))			{$agent_log_id=$_GET["agent_log_id"];}
	elseif (isset($_POST["agent_log_id"]))	{$agent_log_id=$_POST["agent_log_id"];}
if (isset($_GET["ScrollDIV"]))			{$ScrollDIV=$_GET["ScrollDIV"];}
	elseif (isset($_POST["ScrollDIV"]))	{$ScrollDIV=$_POST["ScrollDIV"];}
if (isset($_GET["ignore_list_script"]))				{$ignore_list_script=$_GET["ignore_list_script"];}
	elseif (isset($_POST["ignore_list_script"]))	{$ignore_list_script=$_POST["ignore_list_script"];}
if (isset($_GET["CF_uses_custom_fields"]))			{$CF_uses_custom_fields=$_GET["CF_uses_custom_fields"];}
	elseif (isset($_POST["CF_uses_custom_fields"]))	{$CF_uses_custom_fields=$_POST["CF_uses_custom_fields"];}
if (isset($_GET["entry_list_id"]))			{$entry_list_id=$_GET["entry_list_id"];}
	elseif (isset($_POST["entry_list_id"]))	{$entry_list_id=$_POST["entry_list_id"];}
if (isset($_GET["web_vars"]))			{$web_vars=$_GET["web_vars"];}
	elseif (isset($_POST["web_vars"]))	{$web_vars=$_POST["web_vars"];}


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
$script_height = ($script_height - 20);

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
else
	{
	# do nothing for now
	}

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

if (strlen($in_script) < 1)
	{$call_script = $camp_script;}
else
	{$call_script = $in_script;}

$ignore_list_script_override='N';
$stmt = "SELECT ignore_list_script_override FROM vicidial_inbound_groups where group_id='$group';";
$rslt=mysql_query($stmt, $link);
if ($DB) {echo "$stmt\n";}
$ilso_ct = mysql_num_rows($rslt);
if ($ilso_ct > 0)
	{
	$row=mysql_fetch_row($rslt);
	$ignore_list_script_override =		$row[0];
	}
if ($ignore_list_script_override=='Y')
	{$ignore_list_script=1;}

if ($ignore_list_script < 1)
	{
	$stmt="SELECT agent_script_override from vicidial_lists where list_id='$list_id';";
	$rslt=mysql_query($stmt, $link);
	$row=mysql_fetch_row($rslt);
	$agent_script_override =		$row[0];
	if (strlen($agent_script_override) > 0)
		{$call_script = $agent_script_override;}
	}

$stmt="SELECT list_name,list_description from vicidial_lists where list_id='$list_id';";
$rslt=mysql_query($stmt, $link);
$row=mysql_fetch_row($rslt);
$list_name =			$row[0];
$list_description =		$row[1];

$stmt="SELECT script_name,script_text from vicidial_scripts where script_id='$call_script';";
$rslt=mysql_query($stmt, $link);
$row=mysql_fetch_row($rslt);
$script_name =		$row[0];
$script_text =		stripslashes($row[1]);

if (eregi("iframe src",$script_text))
	{
	$IFRAME=1;
	$lead_id = eregi_replace(' ','+',$lead_id);
	$vendor_id = eregi_replace(' ','+',$vendor_id);
	$vendor_lead_code = eregi_replace(' ','+',$vendor_lead_code);
	$list_id = eregi_replace(' ','+',$list_id);
	$list_name = eregi_replace(' ','+',$list_name);
	$list_description = eregi_replace(' ','+',$list_description);
	$gmt_offset_now = eregi_replace(' ','+',$gmt_offset_now);
	$phone_code = eregi_replace(' ','+',$phone_code);
	$phone_number = eregi_replace(' ','+',$phone_number);
	$title = eregi_replace(' ','+',$title);
	$first_name = eregi_replace(' ','+',$first_name);
	$middle_initial = eregi_replace(' ','+',$middle_initial);
	$last_name = eregi_replace(' ','+',$last_name);
	$address1 = eregi_replace(' ','+',$address1);
	$address2 = eregi_replace(' ','+',$address2);
	$address3 = eregi_replace(' ','+',$address3);
	$city = eregi_replace(' ','+',$city);
	$state = eregi_replace(' ','+',$state);
	$province = eregi_replace(' ','+',$province);
	$postal_code = eregi_replace(' ','+',$postal_code);
	$country_code = eregi_replace(' ','+',$country_code);
	$gender = eregi_replace(' ','+',$gender);
	$date_of_birth = eregi_replace(' ','+',$date_of_birth);
	$alt_phone = eregi_replace(' ','+',$alt_phone);
	$email = eregi_replace(' ','+',$email);
	$security_phrase = eregi_replace(' ','+',$security_phrase);
	$comments = eregi_replace(' ','+',$comments);
	$user = eregi_replace(' ','+',$user);
	$pass = eregi_replace(' ','+',$pass);
	$campaign = eregi_replace(' ','+',$campaign);
	$phone_login = eregi_replace(' ','+',$phone_login);
	$original_phone_login = eregi_replace(' ','+',$original_phone_login);
	$phone_pass = eregi_replace(' ','+',$phone_pass);
	$fronter = eregi_replace(' ','+',$fronter);
	$closer = eregi_replace(' ','+',$closer);
	$group = eregi_replace(' ','+',$group);
	$channel_group = eregi_replace(' ','+',$channel_group);
	$SQLdate = eregi_replace(' ','+',$SQLdate);
	$epoch = eregi_replace(' ','+',$epoch);
	$uniqueid = eregi_replace(' ','+',$uniqueid);
	$customer_zap_channel = eregi_replace(' ','+',$customer_zap_channel);
	$customer_server_ip = eregi_replace(' ','+',$customer_server_ip);
	$server_ip = eregi_replace(' ','+',$server_ip);
	$SIPexten = eregi_replace(' ','+',$SIPexten);
	$session_id = eregi_replace(' ','+',$session_id);
	$phone = eregi_replace(' ','+',$phone);
	$parked_by = eregi_replace(' ','+',$parked_by);
	$dispo = eregi_replace(' ','+',$dispo);
	$dialed_number = eregi_replace(' ','+',$dialed_number);
	$dialed_label = eregi_replace(' ','+',$dialed_label);
	$source_id = eregi_replace(' ','+',$source_id);
	$rank = eregi_replace(' ','+',$rank);
	$owner = eregi_replace(' ','+',$owner);
	$camp_script = eregi_replace(' ','+',$camp_script);
	$in_script = eregi_replace(' ','+',$in_script);
	$script_width = eregi_replace(' ','+',$script_width);
	$script_height = eregi_replace(' ','+',$script_height);
	$fullname = eregi_replace(' ','+',$fullname);
	$recording_filename = eregi_replace(' ','+',$recording_filename);
	$recording_id = eregi_replace(' ','+',$recording_id);
	$user_custom_one = eregi_replace(' ','+',$user_custom_one);
	$user_custom_two = eregi_replace(' ','+',$user_custom_two);
	$user_custom_three = eregi_replace(' ','+',$user_custom_three);
	$user_custom_four = eregi_replace(' ','+',$user_custom_four);
	$user_custom_five = eregi_replace(' ','+',$user_custom_five);
	$preset_number_a = eregi_replace(' ','+',$preset_number_a);
	$preset_number_b = eregi_replace(' ','+',$preset_number_b);
	$preset_number_c = eregi_replace(' ','+',$preset_number_c);
	$preset_number_d = eregi_replace(' ','+',$preset_number_d);
	$preset_number_e = eregi_replace(' ','+',$preset_number_e);
	$preset_number_f = eregi_replace(' ','+',$preset_number_f);
	$preset_dtmf_a = eregi_replace(' ','+',$preset_dtmf_a);
	$preset_dtmf_b = eregi_replace(' ','+',$preset_dtmf_b);
	$did_id = eregi_replace(' ','+',$did_id);
	$did_extension = eregi_replace(' ','+',$did_extension);
	$did_pattern = eregi_replace(' ','+',$did_pattern);
	$did_description = eregi_replace(' ','+',$did_description);
	$web_vars = eregi_replace(' ','+',$web_vars);
	}

$script_text = eregi_replace('--A--lead_id--B--',"$lead_id",$script_text);
$script_text = eregi_replace('--A--vendor_id--B--',"$vendor_id",$script_text);
$script_text = eregi_replace('--A--vendor_lead_code--B--',"$vendor_lead_code",$script_text);
$script_text = eregi_replace('--A--list_id--B--',"$list_id",$script_text);
$script_text = eregi_replace('--A--list_name--B--',"$list_name",$script_text);
$script_text = eregi_replace('--A--list_description--B--',"$list_description",$script_text);
$script_text = eregi_replace('--A--gmt_offset_now--B--',"$gmt_offset_now",$script_text);
$script_text = eregi_replace('--A--phone_code--B--',"$phone_code",$script_text);
$script_text = eregi_replace('--A--phone_number--B--',"$phone_number",$script_text);
$script_text = eregi_replace('--A--title--B--',"$title",$script_text);
$script_text = eregi_replace('--A--first_name--B--',"$first_name",$script_text);
$script_text = eregi_replace('--A--middle_initial--B--',"$middle_initial",$script_text);
$script_text = eregi_replace('--A--last_name--B--',"$last_name",$script_text);
$script_text = eregi_replace('--A--address1--B--',"$address1",$script_text);
$script_text = eregi_replace('--A--address2--B--',"$address2",$script_text);
$script_text = eregi_replace('--A--address3--B--',"$address3",$script_text);
$script_text = eregi_replace('--A--city--B--',"$city",$script_text);
$script_text = eregi_replace('--A--state--B--',"$state",$script_text);
$script_text = eregi_replace('--A--province--B--',"$province",$script_text);
$script_text = eregi_replace('--A--postal_code--B--',"$postal_code",$script_text);
$script_text = eregi_replace('--A--country_code--B--',"$country_code",$script_text);
$script_text = eregi_replace('--A--gender--B--',"$gender",$script_text);
$script_text = eregi_replace('--A--date_of_birth--B--',"$date_of_birth",$script_text);
$script_text = eregi_replace('--A--alt_phone--B--',"$alt_phone",$script_text);
$script_text = eregi_replace('--A--email--B--',"$email",$script_text);
$script_text = eregi_replace('--A--security_phrase--B--',"$security_phrase",$script_text);
$script_text = eregi_replace('--A--comments--B--',"$comments",$script_text);
$script_text = eregi_replace('--A--user--B--',"$user",$script_text);
$script_text = eregi_replace('--A--pass--B--',"$pass",$script_text);
$script_text = eregi_replace('--A--campaign--B--',"$campaign",$script_text);
$script_text = eregi_replace('--A--phone_login--B--',"$phone_login",$script_text);
$script_text = eregi_replace('--A--original_phone_login--B--',"$original_phone_login",$script_text);
$script_text = eregi_replace('--A--phone_pass--B--',"$phone_pass",$script_text);
$script_text = eregi_replace('--A--fronter--B--',"$fronter",$script_text);
$script_text = eregi_replace('--A--closer--B--',"$closer",$script_text);
$script_text = eregi_replace('--A--group--B--',"$group",$script_text);
$script_text = eregi_replace('--A--channel_group--B--',"$channel_group",$script_text);
$script_text = eregi_replace('--A--SQLdate--B--',"$SQLdate",$script_text);
$script_text = eregi_replace('--A--epoch--B--',"$epoch",$script_text);
$script_text = eregi_replace('--A--uniqueid--B--',"$uniqueid",$script_text);
$script_text = eregi_replace('--A--customer_zap_channel--B--',"$customer_zap_channel",$script_text);
$script_text = eregi_replace('--A--customer_server_ip--B--',"$customer_server_ip",$script_text);
$script_text = eregi_replace('--A--server_ip--B--',"$server_ip",$script_text);
$script_text = eregi_replace('--A--SIPexten--B--',"$SIPexten",$script_text);
$script_text = eregi_replace('--A--session_id--B--',"$session_id",$script_text);
$script_text = eregi_replace('--A--phone--B--',"$phone",$script_text);
$script_text = eregi_replace('--A--parked_by--B--',"$parked_by",$script_text);
$script_text = eregi_replace('--A--dispo--B--',"$dispo",$script_text);
$script_text = eregi_replace('--A--dialed_number--B--',"$dialed_number",$script_text);
$script_text = eregi_replace('--A--dialed_label--B--',"$dialed_label",$script_text);
$script_text = eregi_replace('--A--source_id--B--',"$source_id",$script_text);
$script_text = eregi_replace('--A--rank--B--',"$rank",$script_text);
$script_text = eregi_replace('--A--owner--B--',"$owner",$script_text);
$script_text = eregi_replace('--A--camp_script--B--',"$camp_script",$script_text);
$script_text = eregi_replace('--A--in_script--B--',"$in_script",$script_text);
$script_text = eregi_replace('--A--script_width--B--',"$script_width",$script_text);
$script_text = eregi_replace('--A--script_height--B--',"$script_height",$script_text);
$script_text = eregi_replace('--A--fullname--B--',"$fullname",$script_text);
$script_text = eregi_replace('--A--recording_filename--B--',"$recording_filename",$script_text);
$script_text = eregi_replace('--A--recording_id--B--',"$recording_id",$script_text);
$script_text = eregi_replace('--A--user_custom_one--B--',"$user_custom_one",$script_text);
$script_text = eregi_replace('--A--user_custom_two--B--',"$user_custom_two",$script_text);
$script_text = eregi_replace('--A--user_custom_three--B--',"$user_custom_three",$script_text);
$script_text = eregi_replace('--A--user_custom_four--B--',"$user_custom_four",$script_text);
$script_text = eregi_replace('--A--user_custom_five--B--',"$user_custom_five",$script_text);
$script_text = eregi_replace('--A--preset_number_a--B--',"$preset_number_a",$script_text);
$script_text = eregi_replace('--A--preset_number_b--B--',"$preset_number_b",$script_text);
$script_text = eregi_replace('--A--preset_number_c--B--',"$preset_number_c",$script_text);
$script_text = eregi_replace('--A--preset_number_d--B--',"$preset_number_d",$script_text);
$script_text = eregi_replace('--A--preset_number_e--B--',"$preset_number_e",$script_text);
$script_text = eregi_replace('--A--preset_number_f--B--',"$preset_number_f",$script_text);
$script_text = eregi_replace('--A--preset_dtmf_a--B--',"$preset_dtmf_a",$script_text);
$script_text = eregi_replace('--A--preset_dtmf_b--B--',"$preset_dtmf_b",$script_text);
$script_text = eregi_replace('--A--did_id--B--',"$did_id",$script_text);
$script_text = eregi_replace('--A--did_extension--B--',"$did_extension",$script_text);
$script_text = eregi_replace('--A--did_pattern--B--',"$did_pattern",$script_text);
$script_text = eregi_replace('--A--did_description--B--',"$did_description",$script_text);
$script_text = eregi_replace('--A--closecallid--B--',"$closecallid",$script_text);
$script_text = eregi_replace('--A--xfercallid--B--',"$xfercallid",$script_text);
$script_text = eregi_replace('--A--agent_log_id--B--',"$agent_log_id",$script_text);
$script_text = eregi_replace('--A--entry_list_id--B--',"$entry_list_id",$script_text);
$script_text = eregi_replace('--A--web_vars--B--',"$web_vars",$script_text);

if ($CF_uses_custom_fields=='Y')
	{
	### find the names of all custom fields, if any
	$stmt = "SELECT field_label,field_type FROM vicidial_lists_fields where list_id='$entry_list_id' and field_type NOT IN('SCRIPT','DISPLAY') and field_label NOT IN('vendor_lead_code','source_id','list_id','gmt_offset_now','called_since_last_reset','phone_code','phone_number','title','first_name','middle_initial','last_name','address1','address2','address3','city','state','province','postal_code','country_code','gender','date_of_birth','alt_phone','email','security_phrase','comments','called_count','last_local_call_time','rank','owner');";
	$rslt=mysql_query($stmt, $link);
	if ($DB) {echo "$stmt\n";}
	$cffn_ct = mysql_num_rows($rslt);
	$d=0;
	while ($cffn_ct > $d)
		{
		$row=mysql_fetch_row($rslt);
		$field_name_id = $row[0];
		$field_name_tag = "--A--" . $field_name_id . "--B--";
		if (isset($_GET["$field_name_id"]))				{$form_field_value=$_GET["$field_name_id"];}
			elseif (isset($_POST["$field_name_id"]))	{$form_field_value=$_POST["$field_name_id"];}
		$script_text = eregi_replace("$field_name_tag","$form_field_value",$script_text);
			if ($DB) {echo "$d|$field_name_id|$field_name_tag|$form_field_value|<br>\n";}
		$d++;
		}
	}

$script_text = eregi_replace("\n","<BR>",$script_text);
$script_text = stripslashes($script_text);


echo "<!-- IFRAME$IFRAME -->\n";
echo "<!-- $script_id -->\n";
echo "<TABLE WIDTH=$script_width><TR><TD>\n";
if ( ($IFRAME < 1) and ($ScrollDIV > 0) )
	{ echo "<div class=\"scroll_script\" id=\"NewScriptContents\">";}
##echo "<center><B>$script_name</B><BR></center>\n";
echo "$script_text\n";
if ( ($IFRAME < 1) and ($ScrollDIV > 0) )
	{ echo "</div>";}
echo "</TD></TR></TABLE>\n";

exit;

?>
