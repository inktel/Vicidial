<?php
# vdc_form_display.php
# 
# Copyright (C) 2010  Matt Florell <vicidial@gmail.com>    LICENSE: AGPLv2
#
# This script is designed display the contents of the FORM tab in the agent 
# interface, as well as take submission of the form submission when the agent 
# dispositions the call
#
# CHANGELOG:
# 100630-1119 - First build of script
# 100703-1124 - Added submit_button,admin_submit fields, which will log to admin log
# 100712-2322 - Added code to log vicidial_list.entry_list_id field if data altered
# 100916-1749 - Added non-lead variable parsing
#

$version = '2.4-4';
$build = '100916-1749';

require("dbconnect.php");
require("functions.php");


if (isset($_GET["lead_id"]))			{$lead_id=$_GET["lead_id"];}
	elseif (isset($_POST["lead_id"]))	{$lead_id=$_POST["lead_id"];}
if (isset($_GET["list_id"]))			{$list_id=$_GET["list_id"];}
	elseif (isset($_POST["list_id"]))	{$list_id=$_POST["list_id"];}
if (isset($_GET["user"]))				{$user=$_GET["user"];}
	elseif (isset($_POST["user"]))		{$user=$_POST["user"];}
if (isset($_GET["pass"]))				{$pass=$_GET["pass"];}
	elseif (isset($_POST["pass"]))		{$pass=$_POST["pass"];}
if (isset($_GET["server_ip"]))			{$server_ip=$_GET["server_ip"];}
	elseif (isset($_POST["server_ip"]))	{$server_ip=$_POST["server_ip"];}
if (isset($_GET["session_id"]))				{$session_id=$_GET["session_id"];}
	elseif (isset($_POST["session_id"]))	{$session_id=$_POST["session_id"];}
if (isset($_GET["uniqueid"]))			{$uniqueid=$_GET["uniqueid"];}
	elseif (isset($_POST["uniqueid"]))	{$uniqueid=$_POST["uniqueid"];}
if (isset($_GET["stage"]))				{$stage=$_GET["stage"];}
	elseif (isset($_POST["stage"]))		{$stage=$_POST["stage"];}
if (isset($_GET["submit_button"]))			{$submit_button=$_GET["submit_button"];}
	elseif (isset($_POST["submit_button"]))	{$submit_button=$_POST["submit_button"];}
if (isset($_GET["admin_submit"]))			{$admin_submit=$_GET["admin_submit"];}
	elseif (isset($_POST["admin_submit"]))	{$admin_submit=$_POST["admin_submit"];}
if (isset($_GET["bgcolor"]))			{$bgcolor=$_GET["bgcolor"];}
	elseif (isset($_POST["bgcolor"]))	{$bgcolor=$_POST["bgcolor"];}

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
if (isset($_GET["dialed_number"]))	{$dialed_number=$_GET["dialed_number"];}
	elseif (isset($_POST["dialed_number"]))	{$dialed_number=$_POST["dialed_number"];}
if (isset($_GET["dialed_label"]))	{$dialed_label=$_GET["dialed_label"];}
	elseif (isset($_POST["dialed_label"]))	{$dialed_label=$_POST["dialed_label"];}
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
if (isset($_GET["web_vars"]))			{$web_vars=$_GET["web_vars"];}
	elseif (isset($_POST["web_vars"]))	{$web_vars=$_POST["web_vars"];}


header ("Content-type: text/html; charset=utf-8");
header ("Cache-Control: no-cache, must-revalidate");  // HTTP/1.1
header ("Pragma: no-cache");                          // HTTP/1.0

if ($stage=='WELCOME')
	{echo "If a form has been setup for this campaign, it will only appear during a live call. Thanks!"; exit;}

$txt = '.txt';
$StarTtime = date("U");
$NOW_DATE = date("Y-m-d");
$NOW_TIME = date("Y-m-d H:i:s");
$CIDdate = date("mdHis");
$ENTRYdate = date("YmdHis");
$MT[0]='';
$agents='@agents';
$script_height = ($script_height - 20);
if (strlen($bgcolor) < 6) {$bgcolor='FFFFFF';}

$vicidial_list_fields = '|lead_id|vendor_lead_code|source_id|list_id|gmt_offset_now|called_since_last_reset|phone_code|phone_number|title|first_name|middle_initial|last_name|address1|address2|address3|city|state|province|postal_code|country_code|gender|date_of_birth|alt_phone|email|security_phrase|comments|called_count|last_local_call_time|rank|owner|';

$IFRAME=0;

#############################################
##### START SYSTEM_SETTINGS LOOKUP #####
$stmt = "SELECT use_non_latin,timeclock_end_of_day,agentonly_callback_campaign_lock,custom_fields_enabled FROM system_settings;";
$rslt=mysql_query($stmt, $link);
if ($DB) {echo "$stmt\n";}
$qm_conf_ct = mysql_num_rows($rslt);
if ($qm_conf_ct > 0)
	{
	$row=mysql_fetch_row($rslt);
	$non_latin =							$row[0];
	$timeclock_end_of_day =					$row[1];
	$agentonly_callback_campaign_lock =		$row[2];
	$custom_fields_enabled =				$row[3];
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

$stmt="SELECT count(*) from vicidial_users where user='$user' and pass='$pass' and modify_leads='1';";
if ($DB) {echo "|$stmt|\n";}
$rslt=mysql_query($stmt, $link);
$row=mysql_fetch_row($rslt);
$VUmodify=$row[0];

$stmt="SELECT count(*) from vicidial_live_agents where user='$user';";
if ($DB) {echo "|$stmt|\n";}
$rslt=mysql_query($stmt, $link);
$row=mysql_fetch_row($rslt);
$LVAactive=$row[0];

if ($custom_fields_enabled < 1)
	{
	echo "Custom Fields Disabled: |$custom_fields_enabled|\n";
	echo "<form action=./vdc_form_display.php method=POST name=form_custom_fields id=form_custom_fields>\n";
	echo "<input type=hidden name=user id=user value=\"$user\">\n";
	echo "</form>\n";
	exit;
	}

if ( (strlen($user)<2) or (strlen($pass)<2) or ($auth==0) or ( ($LVAactive < 1) and ($VUmodify < 1) ) )
	{
	echo "Invalid Username/Password: |$user|$pass|\n";
	echo "<form action=./vdc_form_display.php method=POST name=form_custom_fields id=form_custom_fields>\n";
	echo "<input type=hidden name=user id=user value=\"$user\">\n";
	echo "</form>\n";
	exit;
	}
else
	{
	# do nothing for now
	}


### BEGIN parse submission of the custom fields form ###
if ($stage=='SUBMIT')
	{
	$update_sent=0;
	$CFoutput='';
	$stmt="SHOW TABLES LIKE \"custom_$list_id\";";
	if ($DB>0) {echo "$stmt";}
	$rslt=mysql_query($stmt, $link);
		if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'06001',$user,$server_ip,$session_name,$one_mysql_log);}
	$tablecount_to_print = mysql_num_rows($rslt);
	if ($tablecount_to_print > 0) 
		{
		$update_SQL='';
		$VL_update_SQL='';
		$stmt="SELECT field_id,field_label,field_name,field_description,field_rank,field_help,field_type,field_options,field_size,field_max,field_default,field_cost,field_required,multi_position,name_position,field_order from vicidial_lists_fields where list_id='$list_id' order by field_rank,field_order,field_label;";
		$rslt=mysql_query($stmt, $link);
			if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'06003',$user,$server_ip,$session_name,$one_mysql_log);}
		$fields_to_print = mysql_num_rows($rslt);
		$fields_list='';
		$o=0;
		while ($fields_to_print > $o) 
			{
			$new_field_value='';
			$form_field_value='';
			$rowx=mysql_fetch_row($rslt);
			$A_field_id[$o] =			$rowx[0];
			$A_field_label[$o] =		$rowx[1];
			$A_field_name[$o] =			$rowx[2];
			$A_field_type[$o] =			$rowx[6];
			$A_field_size[$o] =			$rowx[8];
			$A_field_max[$o] =			$rowx[9];
			$A_field_required[$o] =		$rowx[12];
			$A_field_value[$o] =		'';
			$field_name_id =			$A_field_label[$o];

			if (isset($_GET["$field_name_id"]))				{$form_field_value=$_GET["$field_name_id"];}
				elseif (isset($_POST["$field_name_id"]))	{$form_field_value=$_POST["$field_name_id"];}

			if ( ($A_field_type[$o]=='MULTI') or ($A_field_type[$o]=='CHECKBOX') or ($A_field_type[$o]=='RADIO') )
				{
				$k=0;
				$multi_count = count($form_field_value);
				$multi_array = $form_field_value;
				while ($k < $multi_count)
					{
					$new_field_value .= "$multi_array[$k],";
					$k++;
					}
				$form_field_value = preg_replace("/,$/","",$new_field_value);
				}

			if ($A_field_type[$o]=='TIME')
				{
				if (isset($_GET["MINUTE_$field_name_id"]))			{$form_field_valueM=$_GET["MINUTE_$field_name_id"];}
					elseif (isset($_POST["MINUTE_$field_name_id"]))	{$form_field_valueM=$_POST["MINUTE_$field_name_id"];}
				if (isset($_GET["HOUR_$field_name_id"]))			{$form_field_valueH=$_GET["HOUR_$field_name_id"];}
					elseif (isset($_POST["HOUR_$field_name_id"]))	{$form_field_valueH=$_POST["HOUR_$field_name_id"];}
				$form_field_value = "$form_field_valueH:$form_field_valueM:00";
				}

			$A_field_value[$o] = $form_field_value;

			if ( ($A_field_type[$o]=='DISPLAY') or ($A_field_type[$o]=='SCRIPT') )
				{
				$A_field_value[$o]='----IGNORE----';
				}
			else
				{
				if (preg_match("/\|$A_field_label[$o]\|/",$vicidial_list_fields))
					{
					$VL_update_SQL .= "$A_field_label[$o]='$A_field_value[$o]',";
					}
				else
					{
					$update_SQL .= "$A_field_label[$o]='$A_field_value[$o]',";
					}

				$SUBMIT_output .= "<b>$A_field_name[$o]:</b> $A_field_value[$o]<BR>";
				}
			$o++;
			}

		$custom_update_count=0;
		if (strlen($update_SQL)>3)
			{
			$custom_record_lead_count=0;
			$stmt="SELECT count(*) from custom_$list_id where lead_id='$lead_id';";
			if ($DB>0) {echo "$stmt";}
			$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'06004',$user,$server_ip,$session_name,$one_mysql_log);}
			$fieldleadcount_to_print = mysql_num_rows($rslt);
			if ($fieldleadcount_to_print > 0) 
				{
				$rowx=mysql_fetch_row($rslt);
				$custom_record_lead_count =	$rowx[0];
				}
			$update_SQL = preg_replace("/,$/","",$update_SQL);
			$custom_table_update_SQL = "INSERT INTO custom_$list_id SET lead_id='$lead_id',$update_SQL;";
			if ($custom_record_lead_count > 0)
				{$custom_table_update_SQL = "UPDATE custom_$list_id SET $update_SQL where lead_id='$lead_id';";}

			$rslt=mysql_query($custom_table_update_SQL, $link);
			$custom_update_count = mysql_affected_rows($link);
			if ($DB) {echo "$custom_update_count|$custom_table_update_SQL\n";}
			if (!$rslt) {die('Could not execute: ' . mysql_error());}

			$update_sent++;
			}

		if (strlen($VL_update_SQL)>3)
			{
			$custom_update_vl_SQL='';
			if ($custom_update_count > 0)
				{$custom_update_vl_SQL = "entry_list_id='$list_id',";}
			$VL_update_SQL = preg_replace("/,$/","",$VL_update_SQL);
			$list_table_update_SQL = "UPDATE vicidial_list SET $custom_update_vl_SQL $VL_update_SQL where lead_id='$lead_id';";

			$rslt=mysql_query($list_table_update_SQL, $link);
			$list_update_count = mysql_affected_rows($link);
			if ($DB) {echo "$list_update_count|$list_table_update_SQL\n";}
			if (!$rslt) {die('Could not execute: ' . mysql_error());}

			$update_sent++;
			}
		else
			{
			if ($custom_update_count > 0)
				{
				$list_table_update_SQL = "UPDATE vicidial_list SET entry_list_id='$list_id' where lead_id='$lead_id';";
				$rslt=mysql_query($list_table_update_SQL, $link);
				$list_update_count = mysql_affected_rows($link);
				if ($DB) {echo "$list_update_count|$list_table_update_SQL\n";}
				if (!$rslt) {die('Could not execute: ' . mysql_error());}
				}
			}

		if ( ($admin_submit=='YES') and ($update_sent > 0) )
			{
			### LOG INSERTION Admin Log Table ###
			$ip = getenv("REMOTE_ADDR");
			$SQL_log = "$list_table_update_SQL|$custom_table_update_SQL|";
			$SQL_log = ereg_replace(';','',$SQL_log);
			$SQL_log = addslashes($SQL_log);
			$stmt="INSERT INTO vicidial_admin_log set event_date='$NOW_TIME', user='$user', ip_address='$ip', event_section='LEADS', event_type='MODIFY', record_id='$lead_id', event_code='ADMIN MODIFY CUSTOM LEAD', event_sql=\"$SQL_log\", event_notes='$custom_update_count|$list_update_count';";
			if ($DB) {echo "|$stmt|\n";}
			$rslt=mysql_query($stmt, $link);
			}
		}
	else
		{$CFoutput .= "ERROR: no custom list fields table\n";}

	echo "Custom Form Output:\n<BR>\n";

	echo "$SUBMIT_output";

	echo "<form action=./vdc_form_display.php method=POST name=form_custom_fields id=form_custom_fields>\n";
	echo "<input type=hidden name=user id=user value=\"$user\">\n";
	echo "</form>\n";
	}
### END parse submission of the custom fields form ###
else
	{
	echo "<html>\n";
	echo "<head>\n";
	echo "<!-- VERSION: $version     BUILD: $build    USER: $user   server_ip: $server_ip-->\n";
	echo "<title>ViciDial Form Display Script";
	echo "</title>\n";

	echo "<script language=\"JavaScript\" src=\"calendar_db.js\"></script>\n";
	echo "	<link rel=\"stylesheet\" href=\"calendar.css\">\n";
	echo "	<script language=\"Javascript\">\n";
	echo "	function open_help(taskspan,taskhelp) \n";
	echo "		{\n";
	echo "		document.getElementById(\"P_\" + taskspan).innerHTML = \" &nbsp; <a href=\\\"javascript:close_help('\" + taskspan + \"','\" + taskhelp + \"');\\\">help-</a><BR> &nbsp; \";\n";
	echo "		document.getElementById(taskspan).innerHTML = \"<B>\" + taskhelp + \"</B>\";\n";
	echo "		document.getElementById(taskspan).style.background = \"#FFFF99\";\n";
	echo "		}\n";
	echo "	function close_help(taskspan,taskhelp) \n";
	echo "		{\n";
	echo "		document.getElementById(\"P_\" + taskspan).innerHTML = \"\";\n";
	echo "		document.getElementById(taskspan).innerHTML = \" &nbsp; <a href=\\\"javascript:open_help('\" + taskspan + \"','\" + taskhelp + \"');\\\">help+</a>\";\n";
	echo "		document.getElementById(taskspan).style.background = \"white\";\n";
	echo "		}\n";
	echo "	</script>\n";
	echo "	<META HTTP-EQUIV=\"Content-Type\" CONTENT=\"text/html; charset=utf-8\">\n";
	echo "</head>\n";
	echo "<BODY BGCOLOR=\"#" . $bgcolor . "\" marginheight=0 marginwidth=0 leftmargin=0 topmargin=0 onload=\"parent.document.getElementById('FORM_LOADED').value='1';\">";
	echo "\n";
	echo "<form action=./vdc_form_display.php method=POST name=form_custom_fields id=form_custom_fields>\n";
	echo "<input type=hidden name=lead_id id=lead_id value=\"$lead_id\">\n";
	echo "<input type=hidden name=list_id id=list_id value=\"$list_id\">\n";
	echo "<input type=hidden name=user id=user value=\"$user\">\n";
	echo "<input type=hidden name=pass id=pass value=\"$pass\">\n";
	echo "\n";


	require("functions.php");

	$CFoutput = custom_list_fields_values($lead_id,$list_id,$uniqueid,$user);

	echo "$CFoutput";

	if ($submit_button=='YES')
		{
		echo "<input type=hidden name=admin_submit id=admin_submit value=\"YES\">\n";
		echo "<BR><BR><input type=submit name=VCformSubmit id=VCformSubmit value=submit>\n";
		}
	echo "</form></center><BR><BR>\n";
	echo "</BODY></HTML>\n";
	}


exit;

?>
