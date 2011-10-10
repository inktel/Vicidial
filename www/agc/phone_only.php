<?php
# phone_only.php - the web-based web-phone-only client application
# 
# Copyright (C) 2011  Matt Florell <vicidial@gmail.com>    LICENSE: AGPLv2
#
# CHANGELOG
# 110511-1336 - First Build
# 110526-1757 - Added webphone_auto_answer option
#

$version = '2.4-2p';
$build = '110526-1757';
$mel=1;					# Mysql Error Log enabled = 1
$mysql_log_count=72;
$one_mysql_log=0;

require("dbconnect.php");
require("functions.php");

if (isset($_GET["DB"]))						    {$DB=$_GET["DB"];}
        elseif (isset($_POST["DB"]))            {$DB=$_POST["DB"];}
if (isset($_GET["phone_login"]))                {$phone_login=$_GET["phone_login"];}
        elseif (isset($_POST["phone_login"]))   {$phone_login=$_POST["phone_login"];}
if (isset($_GET["phone_pass"]))					{$phone_pass=$_GET["phone_pass"];}
        elseif (isset($_POST["phone_pass"]))    {$phone_pass=$_POST["phone_pass"];}
if (isset($_GET["VD_login"]))					{$VD_login=$_GET["VD_login"];}
        elseif (isset($_POST["VD_login"]))      {$VD_login=$_POST["VD_login"];}
if (isset($_GET["VD_pass"]))					{$VD_pass=$_GET["VD_pass"];}
        elseif (isset($_POST["VD_pass"]))       {$VD_pass=$_POST["VD_pass"];}
if (isset($_GET["relogin"]))					{$relogin=$_GET["relogin"];}
        elseif (isset($_POST["relogin"]))       {$relogin=$_POST["relogin"];}
if (!isset($phone_login)) 
	{
	if (isset($_GET["pl"]))                {$phone_login=$_GET["pl"];}
		elseif (isset($_POST["pl"]))   {$phone_login=$_POST["pl"];}
	}
if (!isset($phone_pass))
	{
	if (isset($_GET["pp"]))                {$phone_pass=$_GET["pp"];}
		elseif (isset($_POST["pp"]))   {$phone_pass=$_POST["pp"];}
	}
if (!isset($flag_channels))
	{
	$flag_channels=0;
	$flag_string='';
	}

### security strip all non-alphanumeric characters out of the variables ###
$DB=ereg_replace("[^0-9a-z]","",$DB);
$phone_login=ereg_replace("[^\,0-9a-zA-Z]","",$phone_login);
$phone_pass=ereg_replace("[^0-9a-zA-Z]","",$phone_pass);
$VD_login=ereg_replace("[^-_0-9a-zA-Z]","",$VD_login);
$VD_pass=ereg_replace("[^-_0-9a-zA-Z]","",$VD_pass);


$forever_stop=0;

if ($force_logout)
	{
    echo "You have now logged out. Thank you\n";
    exit;
	}

$isdst = date("I");
$StarTtimE = date("U");
$NOW_TIME = date("Y-m-d H:i:s");
$tsNOW_TIME = date("YmdHis");
$FILE_TIME = date("Ymd-His");
$loginDATE = date("Ymd");
$CIDdate = date("ymdHis");
$month_old = mktime(11, 0, 0, date("m"), date("d")-2,  date("Y"));
$past_month_date = date("Y-m-d H:i:s",$month_old);
$minutes_old = mktime(date("H"), date("i")-2, date("s"), date("m"), date("d"),  date("Y"));
$past_minutes_date = date("Y-m-d H:i:s",$minutes_old);
$webphone_width = 460;
$webphone_height = 500;
$PHP_SELF=$_SERVER['PHP_SELF'];

$random = (rand(1000000, 9999999) + 10000000);

#############################################
##### START SYSTEM_SETTINGS LOOKUP #####
$stmt = "SELECT use_non_latin,vdc_header_date_format,vdc_customer_date_format,vdc_header_phone_format,webroot_writable,timeclock_end_of_day,vtiger_url,enable_vtiger_integration,outbound_autodial_active,enable_second_webform,user_territories_active,static_agent_url,custom_fields_enabled FROM system_settings;";
$rslt=mysql_query($stmt, $link);
	if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'09001',$VD_login,$server_ip,$session_name,$one_mysql_log);}
if ($DB) {echo "$stmt\n";}
$qm_conf_ct = mysql_num_rows($rslt);
if ($qm_conf_ct > 0)
	{
	$row=mysql_fetch_row($rslt);
	$non_latin =					$row[0];
	$vdc_header_date_format =		$row[1];
	$vdc_customer_date_format =		$row[2];
	$vdc_header_phone_format =		$row[3];
	$WeBRooTWritablE =				$row[4];
	$timeclock_end_of_day =			$row[5];
	$vtiger_url =					$row[6];
	$enable_vtiger_integration =	$row[7];
	$outbound_autodial_active =		$row[8];
	$enable_second_webform =		$row[9];
	$user_territories_active =		$row[10];
	$static_agent_url =				$row[11];
	$custom_fields_enabled =		$row[12];
	}
##### END SETTINGS LOOKUP #####
###########################################


##### DEFINABLE SETTINGS AND OPTIONS
###########################################

# set defaults for hard-coded variables
$clientDST				= '1';	# set to 1 to check for DST on server for agent time
$PhonESComPIP			= '1';	# set to 1 to log computer IP to phone if blank, set to 2 to force log each login
$hide_timeclock_link	= '0';	# set to 1 to hide the timeclock link on the agent login screen

$stretch_dimensions		= '1';	# sets the vicidial screen to the size of the browser window
$BROWSER_HEIGHT			= 500;	# set to the minimum browser height, default=500
$BROWSER_WIDTH			= 770;	# set to the minimum browser width, default=770
$webphone_width			= 460;	# set the webphone frame width
$webphone_height		= 500;	# set the webphone frame height
$webphone_pad			= 0;	# set the table cellpadding for the webphone
$webphone_location		= 'right';	# set the location on the agent screen 'right' or 'bar'
$MAIN_COLOR				= '#CCCCCC';	# old default is E0C2D6
$SCRIPT_COLOR			= '#E6E6E6';	# old default is FFE7D0
$FORM_COLOR				= '#EFEFEF';
$SIDEBAR_COLOR			= '#F6F6F6';

# if options file exists, use the override values for the above variables
#   see the options-example.php file for more information
if (file_exists('options.php'))
	{
	require('options.php');
	}

$US='_';
$CL=':';
$AT='@';
$DS='-';
$date = date("r");
$ip = getenv("REMOTE_ADDR");
$browser = getenv("HTTP_USER_AGENT");
$script_name = getenv("SCRIPT_NAME");
$server_name = getenv("SERVER_NAME");
$server_port = getenv("SERVER_PORT");
if (eregi("443",$server_port)) {$HTTPprotocol = 'https://';}
  else {$HTTPprotocol = 'http://';}
if (($server_port == '80') or ($server_port == '443') ) {$server_port='';}
else {$server_port = "$CL$server_port";}
$agcPAGE = "$HTTPprotocol$server_name$server_port$script_name";
$agcDIR = eregi_replace('phone_only.php','',$agcPAGE);
if (strlen($static_agent_url) > 5)
	{$agcPAGE = $static_agent_url;}


header ("Content-type: text/html; charset=utf-8");
header ("Cache-Control: no-cache, must-revalidate");  // HTTP/1.1
header ("Pragma: no-cache");                          // HTTP/1.0
echo '<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" type="text/css" href="css/style.css" />
<link rel="stylesheet" type="text/css" href="css/custom.css" />
';
echo "<!-- VERSION: $version     BUILD: $build -->\n";
echo "<!-- BROWSER: $BROWSER_WIDTH x $BROWSER_HEIGHT     $JS_browser_width x $JS_browser_height -->\n";


$stmt="SELECT user_group from vicidial_users where user='$VD_login' and pass='$VD_pass';";
if ($non_latin > 0) {$rslt=mysql_query("SET NAMES 'UTF8'");}
$rslt=mysql_query($stmt, $link);
		if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'09002',$VD_login,$server_ip,$session_name,$one_mysql_log);}
$row=mysql_fetch_row($rslt);
$VU_user_group=$row[0];


if ($relogin == 'YES')
	{
	echo "<title>Phone web client: Login</title>\n";
	echo "</head>\n";
    echo "<body bgcolor=\"white\">\n";
	if ($hide_timeclock_link < 1)
        {echo "<a href=\"./timeclock.php?referrer=agent&amp;pl=$phone_login&amp;pp=$phone_pass&amp;VD_login=$VD_login&amp;VD_pass=$VD_pass\"> Timeclock</a><br />\n";}
    echo "<table width=\"100%\"><tr><td></td>\n";
	echo "<!-- INTERNATIONALIZATION-LINKS-PLACEHOLDER-VICIDIAL -->\n";
    echo "</tr></table>\n";
    echo "<form name=\"vicidial_form\" id=\"vicidial_form\" action=\"$agcPAGE\" method=\"post\">\n";
    echo "<input type=\"hidden\" name=\"DB\" id=\"DB\" value=\"$DB\" />\n";
    echo "<br /><br /><br /><center><table width=\"460px\" cellpadding=\"0\" cellspacing=\"0\" bgcolor=\"$MAIN_COLOR\"><tr bgcolor=\"white\">";
    echo "<td align=\"left\" valign=\"bottom\"><img src=\"./images/vdc_tab_vicidial.gif\" border=\"0\" alt=\"VICIdial\" /></td>";
    echo "<td align=\"center\" valign=\"middle\"> Phone-Only Login </td>";
    echo "</tr>\n";
    echo "<tr><td align=\"left\" colspan=\"2\"><font size=\"1\"> &nbsp; </font></td></tr>\n";
    echo "<tr><td align=\"right\">Phone Login: </td>";
    echo "<td align=\"left\"><input type=\"text\" name=\"phone_login\" size=\"10\" maxlength=\"20\" value=\"$phone_login\" /></td></tr>\n";
    echo "<tr><td align=\"right\">Phone Password:  </td>";
    echo "<td align=\"left\"><input type=\"password\" name=\"phone_pass\" size=\"10\" maxlength=\"20\" value=\"$phone_pass\" /></td></tr>\n";
    echo "<tr><td align=\"right\">User Login:  </td>";
    echo "<td align=\"left\"><input type=\"text\" name=\"VD_login\" size=\"10\" maxlength=\"20\" value=\"$VD_login\" /></td></tr>\n";
    echo "<tr><td align=\"right\">User Password:  </td>";
    echo "<td align=\"left\"><input type=\"password\" name=\"VD_pass\" size=\"10\" maxlength=\"20\" value=\"$VD_pass\" /></td></tr>\n";
    echo "<tr><td align=\"center\" colspan=\"2\"><input type=\"submit\" name=\"SUBMIT\" value=\"Submit\" /> &nbsp; \n";
    echo "<tr><td align=\"left\" colspan=\"2\"><font size=\"1\"><br />VERSION: $version &nbsp; &nbsp; &nbsp; BUILD: $build</font></td></tr>\n";
    echo "</table></center>\n";
    echo "</form>\n\n";
	echo "</body>\n\n";
	echo "</html>\n\n";
	exit;
	}


if ( (strlen($phone_login)<2) or (strlen($phone_pass)<2) )
	{
	echo "<title>Phone web client: Phone Login</title>\n";
	echo "</head>\n";
    echo "<body bgcolor=\"white\">\n";
	if ($hide_timeclock_link < 1)
        {echo "<a href=\"./timeclock.php?referrer=agent&amp;pl=$phone_login&amp;pp=$phone_pass&amp;VD_login=$VD_login&amp;VD_pass=$VD_pass\"> Timeclock</a><br />\n";}
    echo "<table width=100%><tr><td></td>\n";
	echo "<!-- INTERNATIONALIZATION-LINKS-PLACEHOLDER-VICIDIAL -->\n";
    echo "</tr></table>\n";
    echo "<form name=\"vicidial_form\" id=\"vicidial_form\" action=\"$agcPAGE\" method=\"post\">\n";
    echo "<input type=\"hidden\" name=\"DB\" value=\"$DB\" />\n";
    echo "<br /><br /><br /><center><table width=\"460px\" cellpadding=\"0\" cellspacing=\"0\" bgcolor=\"$MAIN_COLOR\"><tr bgcolor=\"white\">";
    echo "<td align=\"left\" valign=\"bottom\"><img src=\"./images/vdc_tab_vicidial.gif\" border=\"0\" alt=\"VICIdial\" /></td>";
    echo "<td align=\"center\" valign=\"middle\"> Phone-Only Login </td>";
    echo "</tr>\n";
    echo "<tr><td align=\"left\" colspan=\"2\"><font size=\"1\"> &nbsp; </font></td></tr>\n";
    echo "<tr><td align=\"right\">Phone Login: </td>";
    echo "<td align=\"left\"><input type=\"text\" name=\"phone_login\" size=\"10\" maxlength=\"20\" value=\"\" /></td></tr>\n";
    echo "<tr><td align=\"right\">Phone Password:  </td>";
    echo "<td align=\"left\"><input type=\"password\" name=\"phone_pass\" size=\"10\" maxlength=\"20\" value=\"\" /></td></tr>\n";
    echo "<tr><td align=\"center\" colspan=\"2\"><input type=\"submit\" name=\"SUBMIT\" value=\"Submit\" /> &nbsp; \n";
    echo "<span id=\"LogiNReseT\"></span></td></tr>\n";
    echo "<tr><td align=\"left\" colspan=\"2\"><font size=\"1\"><br />VERSION: $version &nbsp; &nbsp; &nbsp; BUILD: $build</font></td></tr>\n";
    echo "</table></center>\n";
    echo "</form>\n\n";
	echo "</body>\n\n";
	echo "</html>\n\n";
	exit;
	}
else
	{
	if ($WeBRooTWritablE > 0)
		{$fp = fopen ("./vicidial_auth_entries.txt", "a");}
	$VDloginDISPLAY=0;

	if ( (strlen($VD_login)<2) or (strlen($VD_pass)<2) )
		{
		$VDloginDISPLAY=1;
		}
	else
		{
		$stmt="SELECT count(*) from vicidial_users where user='$VD_login' and pass='$VD_pass' and user_level > 0 and active='Y';";
		if ($DB) {echo "|$stmt|\n";}
		$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'09003',$VD_login,$server_ip,$session_name,$one_mysql_log);}
		$row=mysql_fetch_row($rslt);
		$auth=$row[0];

		if($auth>0)
			{
			$login=strtoupper($VD_login);
			$password=strtoupper($VD_pass);
			##### grab the full name of the agent
			$stmt="SELECT full_name,user_level,hotkeys_active,agent_choose_ingroups,scheduled_callbacks,agentonly_callbacks,agentcall_manual,vicidial_recording,vicidial_transfers,closer_default_blended,user_group,vicidial_recording_override,alter_custphone_override,alert_enabled,agent_shift_enforcement_override,shift_override_flag,allow_alerts,closer_campaigns,agent_choose_territories,custom_one,custom_two,custom_three,custom_four,custom_five,agent_call_log_view_override,agent_choose_blended,agent_lead_search_override from vicidial_users where user='$VD_login' and pass='$VD_pass'";
			$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'09004',$VD_login,$server_ip,$session_name,$one_mysql_log);}
			$row=mysql_fetch_row($rslt);
			$LOGfullname =					$row[0];
			$user_level =					$row[1];
			$VU_user_group =				$row[10];

			### Gather timeclock and shift enforcement restriction settings
			$stmt="SELECT forced_timeclock_login,shift_enforcement,group_shifts,agent_status_viewable_groups,agent_status_view_time,agent_call_log_view,agent_xfer_consultative,agent_xfer_dial_override,agent_xfer_vm_transfer,agent_xfer_blind_transfer,agent_xfer_dial_with_customer,agent_xfer_park_customer_dial,agent_fullscreen,webphone_url_override,webphone_dialpad_override,webphone_systemkey_override from vicidial_user_groups where user_group='$VU_user_group';";
			$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'09005',$VD_login,$server_ip,$session_name,$one_mysql_log);}
			$row=mysql_fetch_row($rslt);
			$agent_fullscreen =				$row[12];
			$webphone_url =					$row[13];
			$webphone_dialpad_override =	$row[14];
			$system_key =					$row[15];
			if ( ($webphone_dialpad_override != 'DISABLED') and (strlen($webphone_dialpad_override) > 0) )
				{$webphone_dialpad = $webphone_dialpad_override;}

			if ($WeBRooTWritablE > 0)
				{
				fwrite ($fp, "vdweb|GOOD|$date|$VD_login|$VD_pass|$ip|$browser|$LOGfullname|\n");
				fclose($fp);
				}
			$user_abb = "$VD_login$VD_login$VD_login$VD_login";
			while ( (strlen($user_abb) > 4) and ($forever_stop < 200) )
				{$user_abb = eregi_replace("^.","",$user_abb);   $forever_stop++;}

			}
		else
			{
			if ($WeBRooTWritablE > 0)
				{
				fwrite ($fp, "vdweb|FAIL|$date|$VD_login|$VD_pass|$ip|$browser|\n");
				fclose($fp);
				}
			$VDloginDISPLAY=1;
            $VDdisplayMESSAGE = "Login incorrect, please try again<br />";
			}
		}
	if ($VDloginDISPLAY)
		{
		echo "<title>Phone web client: Login</title>\n";
		echo "</head>\n";
        echo "<body bgcolor=\"white\">\n";
		if ($hide_timeclock_link < 1)
            {echo "<a href=\"./timeclock.php?referrer=agent&amp;pl=$phone_login&amp;pp=$phone_pass&amp;VD_login=$VD_login&amp;VD_pass=$VD_pass\"> Timeclock</a><br />\n";}
        echo "<table width=\"100%\"><tr><td></td>\n";
		echo "<!-- INTERNATIONALIZATION-LINKS-PLACEHOLDER-VICIDIAL -->\n";
        echo "</tr></table>\n";
        echo "<form name=\"vicidial_form\" id=\"vicidial_form\" action=\"$agcPAGE\" method=\"post\">\n";
        echo "<input type=\"hidden\" name=\"DB\" value=\"$DB\" />\n";
        echo "<input type=\"hidden\" name=\"phone_login\" value=\"$phone_login\" />\n";
        echo "<input type=\"hidden\" name=\"phone_pass\" value=\"$phone_pass\" />\n";
        echo "<center><br /><b>$VDdisplayMESSAGE</b><br /><br />";
        echo "<table width=\"460px\" cellpadding=\"0\" cellspacing=\"0\" bgcolor=\"$MAIN_COLOR\"><tr bgcolor=\"white\">";
        echo "<td align=\"left\" valign=\"bottom\"><img src=\"./images/vdc_tab_vicidial.gif\" border=\"0\" alt=\"VICIdial\" /></td>";
        echo "<td align=\"center\" valign=\"middle\"> Phone-Only Login </td>";
        echo "</tr>\n";
        echo "<tr><td align=\"left\" colspan=\"2\"><font size=\"1\"> &nbsp; </font></td></tr>\n";
        echo "<tr><td align=\"right\">User Login:  </td>";
        echo "<td align=\"left\"><input type=\"text\" name=\"VD_login\" size=\"10\" maxlength=\"20\" value=\"$VD_login\" /></td></tr>\n";
        echo "<tr><td align=\"right\">User Password:  </td>";
        echo "<td align=\"left\"><input type=\"password\" name=\"VD_pass\" size=\"10\" maxlength=\"20\" value=\"$VD_pass\" /></td></tr>\n";
        echo "<tr><td align=\"center\" colspan=\"2\"><input type=\"submit\" name=\"SUBMIT\" value=\"Submit\" /> &nbsp; \n";
        echo "<span id=\"LogiNReseT\"></span></td></tr>\n";
        echo "<tr><td align=\"left\" colspan=\"2\"><font size=\"1\"><br />VERSION: $version &nbsp; &nbsp; &nbsp; BUILD: $build</font></td></tr>\n";
        echo "</table>\n";
        echo "</form>\n\n";
		echo "</body>\n\n";
		echo "</html>\n\n";
		exit;
		}

	$original_phone_login = $phone_login;

	# code for parsing load-balanced agent phone allocation where agent interface
	# will send multiple phones-table logins so that the script can determine the
	# server that has the fewest agents logged into it.
	#   login: ca101,cb101,cc101
		$alias_found=0;
	$stmt="select count(*) from phones_alias where alias_id = '$phone_login';";
	$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'09006',$VD_login,$server_ip,$session_name,$one_mysql_log);}
	$alias_ct = mysql_num_rows($rslt);
	if ($alias_ct > 0)
		{
		$row=mysql_fetch_row($rslt);
		$alias_found = "$row[0]";
		}
	if ($alias_found > 0)
		{
		$stmt="select alias_name,logins_list from phones_alias where alias_id = '$phone_login' limit 1;";
		$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'09007',$VD_login,$server_ip,$session_name,$one_mysql_log);}
		$alias_ct = mysql_num_rows($rslt);
		if ($alias_ct > 0)
			{
			$row=mysql_fetch_row($rslt);
			$alias_name = "$row[0]";
			$phone_login = "$row[1]";
			}
		}

	$pa=0;
	if ( (eregi(',',$phone_login)) and (strlen($phone_login) > 2) )
		{
		$phoneSQL = "(";
		$phones_auto = explode(',',$phone_login);
		$phones_auto_ct = count($phones_auto);
		while($pa < $phones_auto_ct)
			{
			if ($pa > 0)
				{$phoneSQL .= " or ";}
			$desc = ($phones_auto_ct - $pa); # traverse in reverse order
			$phoneSQL .= "(login='$phones_auto[$desc]' and pass='$phone_pass')";
			$pa++;
			}
		$phoneSQL .= ")";
		}
	else {$phoneSQL = "login='$phone_login' and pass='$phone_pass'";}

	$authphone=0;
	#$stmt="SELECT count(*) from phones where $phoneSQL and active = 'Y';";
	$stmt="SELECT count(*) from phones,servers where $phoneSQL and phones.active = 'Y' and active_asterisk_server='Y' and phones.server_ip=servers.server_ip;";
	if ($DB) {echo "|$stmt|\n";}
	$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'09008',$VD_login,$server_ip,$session_name,$one_mysql_log);}
	$row=mysql_fetch_row($rslt);
	$authphone=$row[0];
	if (!$authphone)
		{
		echo "<title>Phone web client: Phone Login Error</title>\n";
		echo "</head>\n";
        echo "<body bgcolor=\"white\">\n";
		if ($hide_timeclock_link < 1)
            {echo "<a href=\"./timeclock.php?referrer=agent&amp;pl=$phone_login&amp;pp=$phone_pass&amp;VD_login=$VD_login&amp;VD_pass=$VD_pass\"> Timeclock</a><br />\n";}
        echo "<table width=\"100%\"><tr><td></td>\n";
		echo "<!-- INTERNATIONALIZATION-LINKS-PLACEHOLDER-VICIDIAL -->\n";
        echo "</tr></table>\n";
        echo "<form name=\"vicidial_form\" id=\"vicidial_form\" action=\"$agcPAGE\" method=\"post\">\n";
        echo "<input type=\"hidden\" name=\"DB\" value=\"$DB\">\n";
        echo "<input type=\"hidden\" name=\"VD_login\" value=\"$VD_login\" />\n";
        echo "<input type=\"hidden\" name=\"VD_pass\" value=\"$VD_pass\" />\n";
        echo "<br /><br /><br /><center><table width=\"460px\" cellpadding=\"0\" cellspacing=\"0\" bgcolor=\"$MAIN_COLOR\"><tr bgcolor=\"white\">";
        echo "<td align=\"left\" valign=\"bottom\"><img src=\"./images/vdc_tab_vicidial.gif\" border=\"0\" alt=\"VICIdial\" /></td>";
        echo "<td align=\"center\" valign=\"middle\"> Phone-Only Login Error</td>";
        echo "</tr>\n";
        echo "<tr><td align=\"center\" colspan=\"2\"><font size=\"1\"> &nbsp; <br /><font size=\"3\">Sorry, your phone login and password are not active in this system, please try again: <br /> &nbsp;</font></td></tr>\n";
        echo "<tr><td align=\"right\">Phone Login: </td>";
        echo "<td align=\"left\"><input type=\"text\" name=\"phone_login\" size=\"10\" maxlength=\"20\" value=\"$phone_login\"></td></tr>\n";
        echo "<tr><td align=\"right\">Phone Password:  </td>";
        echo "<td align=\"left\"><input type=\"password\" name=\"phone_pass\" size=10 maxlength=20 value=\"$phone_pass\"></td></tr>\n";
        echo "<tr><td align=\"center\" colspan=\"2\"><input type=\"submit\" name=\"SUBMIT\" value=\"Submit\" /></td></tr>\n";
        echo "<tr><td align=\"left\" colspan=\"2\"><font size=\"1\"><br />VERSION: $version &nbsp; &nbsp; &nbsp; BUILD: $build</font></td></tr>\n";
        echo "</table></center>\n";
        echo "</form>\n\n";
		echo "</body>\n\n";
		echo "</html>\n\n";
		exit;
		}
	else
		{
	### go through the entered phones to figure out which server has fewest agents
	### logged in and use that phone login account
		if ($pa > 0)
			{
			$pb=0;
			$pb_login='';
			$pb_server_ip='';
			$pb_count=0;
			$pb_log='';
			while($pb < $phones_auto_ct)
				{
				### find the server_ip of each phone_login
				$stmtx="SELECT server_ip from phones where login = '$phones_auto[$pb]';";
				if ($DB) {echo "|$stmtx|\n";}
				if ($non_latin > 0) {$rslt=mysql_query("SET NAMES 'UTF8'");}
				$rslt=mysql_query($stmtx, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'09009',$VD_login,$server_ip,$session_name,$one_mysql_log);}
				$rowx=mysql_fetch_row($rslt);

				### get number of agents logged in to each server
				$stmt="SELECT count(*) from web_client_sessions where server_ip = '$rowx[0]';";
				if ($DB) {echo "|$stmt|\n";}
				$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'09010',$VD_login,$server_ip,$session_name,$one_mysql_log);}
				$row=mysql_fetch_row($rslt);
				
				### find out whether the server is set to active
				$stmt="SELECT count(*) from servers where server_ip = '$rowx[0]' and active='Y' and active_asterisk_server='Y';";
				if ($DB) {echo "|$stmt|\n";}
				$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'09011',$VD_login,$server_ip,$session_name,$one_mysql_log);}
				$rowy=mysql_fetch_row($rslt);

				### find out if this server has a twin
				$twin_not_live=0;
				$stmt="SELECT active_twin_server_ip from servers where server_ip = '$rowx[0]';";
				if ($DB) {echo "|$stmt|\n";}
				$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'09012',$VD_login,$server_ip,$session_name,$one_mysql_log);}
				$rowyy=mysql_fetch_row($rslt);
				if (strlen($rowyy[0]) > 4)
					{
					### find out whether the twin server_updater is running
					$stmt="SELECT count(*) from server_updater where server_ip = '$rowyy[0]' and last_update > '$past_minutes_date';";
					if ($DB) {echo "|$stmt|\n";}
					$rslt=mysql_query($stmt, $link);
					if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'09013',$VD_login,$server_ip,$session_name,$one_mysql_log);}
					$rowyz=mysql_fetch_row($rslt);
					if ($rowyz[0] < 1) {$twin_not_live=1;}
					}

				### find out whether the server_updater is running
				$stmt="SELECT count(*) from server_updater where server_ip = '$rowx[0]' and last_update > '$past_minutes_date';";
				if ($DB) {echo "|$stmt|\n";}
				$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'09014',$VD_login,$server_ip,$session_name,$one_mysql_log);}
				$rowz=mysql_fetch_row($rslt);

				$pb_log .= "$phones_auto[$pb]|$rowx[0]|$row[0]|$rowy[0]|$rowz[0]|$twin_not_live|   ";

				if ( ($rowy[0] > 0) and ($rowz[0] > 0) and ($twin_not_live < 1) )
					{
					if ( ($pb_count >= $row[0]) or (strlen($pb_server_ip) < 4) )
						{
						$pb_count=$row[0];
						$pb_server_ip=$rowx[0];
						$phone_login=$phones_auto[$pb];
						}
					}
				$pb++;
				}
			echo "<!-- Phones balance selection: $phone_login|$pb_server_ip|$past_minutes_date|     |$pb_log -->\n";
			}
		echo "<title>Phone web client</title>\n";
		$stmt="SELECT extension,dialplan_number,voicemail_id,phone_ip,computer_ip,server_ip,login,pass,status,active,phone_type,fullname,company,picture,messages,old_messages,protocol,local_gmt,ASTmgrUSERNAME,ASTmgrSECRET,login_user,login_pass,login_campaign,park_on_extension,conf_on_extension,VICIDIAL_park_on_extension,VICIDIAL_park_on_filename,monitor_prefix,recording_exten,voicemail_exten,voicemail_dump_exten,ext_context,dtmf_send_extension,call_out_number_group,client_browser,install_directory,local_web_callerID_URL,VICIDIAL_web_URL,AGI_call_logging_enabled,user_switching_enabled,conferencing_enabled,admin_hangup_enabled,admin_hijack_enabled,admin_monitor_enabled,call_parking_enabled,updater_check_enabled,AFLogging_enabled,QUEUE_ACTION_enabled,CallerID_popup_enabled,voicemail_button_enabled,enable_fast_refresh,fast_refresh_rate,enable_persistant_mysql,auto_dial_next_number,VDstop_rec_after_each_call,DBX_server,DBX_database,DBX_user,DBX_pass,DBX_port,DBY_server,DBY_database,DBY_user,DBY_pass,DBY_port,outbound_cid,enable_sipsak_messages,email,template_id,conf_override,phone_context,phone_ring_timeout,conf_secret,is_webphone,use_external_server_ip,codecs_list,webphone_dialpad,phone_ring_timeout,on_hook_agent,webphone_auto_answer from phones where login='$phone_login' and pass='$phone_pass' and active = 'Y';";
		if ($DB) {echo "|$stmt|\n";}
		$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'09015',$VD_login,$server_ip,$session_name,$one_mysql_log);}
		$row=mysql_fetch_row($rslt);
		$extension=$row[0];
		$dialplan_number=$row[1];
		$voicemail_id=$row[2];
		$phone_ip=$row[3];
		$computer_ip=$row[4];
		$server_ip=$row[5];
		$login=$row[6];
		$pass=$row[7];
		$status=$row[8];
		$active=$row[9];
		$phone_type=$row[10];
		$fullname=$row[11];
		$company=$row[12];
		$picture=$row[13];
		$messages=$row[14];
		$old_messages=$row[15];
		$protocol=$row[16];
		$local_gmt=$row[17];
		$ASTmgrUSERNAME=$row[18];
		$ASTmgrSECRET=$row[19];
		$login_user=$row[20];
		$login_pass=$row[21];
		$login_campaign=$row[22];
		$park_on_extension=$row[23];
		$conf_on_extension=$row[24];
		$VICIDiaL_park_on_extension=$row[25];
		$VICIDiaL_park_on_filename=$row[26];
		$monitor_prefix=$row[27];
		$recording_exten=$row[28];
		$voicemail_exten=$row[29];
		$voicemail_dump_exten=$row[30];
		$ext_context=$row[31];
		$dtmf_send_extension=$row[32];
		$call_out_number_group=$row[33];
		$client_browser=$row[34];
		$install_directory=$row[35];
		$local_web_callerID_URL=$row[36];
		$VICIDiaL_web_URL=$row[37];
		$AGI_call_logging_enabled=$row[38];
		$user_switching_enabled=$row[39];
		$conferencing_enabled=$row[40];
		$admin_hangup_enabled=$row[41];
		$admin_hijack_enabled=$row[42];
		$admin_monitor_enabled=$row[43];
		$call_parking_enabled=$row[44];
		$updater_check_enabled=$row[45];
		$AFLogging_enabled=$row[46];
		$QUEUE_ACTION_enabled=$row[47];
		$CallerID_popup_enabled=$row[48];
		$voicemail_button_enabled=$row[49];
		$enable_fast_refresh=$row[50];
		$fast_refresh_rate=$row[51];
		$enable_persistant_mysql=$row[52];
		$auto_dial_next_number=$row[53];
		$VDstop_rec_after_each_call=$row[54];
		$DBX_server=$row[55];
		$DBX_database=$row[56];
		$DBX_user=$row[57];
		$DBX_pass=$row[58];
		$DBX_port=$row[59];
		$outbound_cid=$row[65];
		$enable_sipsak_messages=$row[66];
		$conf_secret=$row[72];
		$is_webphone=$row[73];
		$use_external_server_ip=$row[74];
		$codecs_list=$row[75];
		$webphone_dialpad=$row[76];
		$phone_ring_timeout=$row[77];
		$on_hook_agent=$row[78];
		$webphone_auto_answer=$row[79];

		$no_empty_session_warnings=0;
		if ( ($phone_login == 'nophone') or ($on_hook_agent == 'Y') )
			{
			$no_empty_session_warnings=1;
			}
		if ($PhonESComPIP == '1')
			{
			if (strlen($computer_ip) < 4)
				{
				$stmt="UPDATE phones SET computer_ip='$ip' where login='$phone_login' and pass='$phone_pass' and active = 'Y';";
				if ($DB) {echo "|$stmt|\n";}
				$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'09016',$VD_login,$server_ip,$session_name,$one_mysql_log);}
				}
			}
		if ($PhonESComPIP == '2')
			{
			$stmt="UPDATE phones SET computer_ip='$ip' where login='$phone_login' and pass='$phone_pass' and active = 'Y';";
			if ($DB) {echo "|$stmt|\n";}
			$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'09017',$VD_login,$server_ip,$session_name,$one_mysql_log);}
			}
		if ($clientDST)
			{
			$local_gmt = ($local_gmt + $isdst);
			}
		if ($protocol == 'EXTERNAL')
			{
			$protocol = 'Local';
			$extension = "$dialplan_number$AT$ext_context";
			}
		$SIP_user = "$protocol/$extension";
		$SIP_user_DiaL = "$protocol/$extension";
		if ( (ereg('8300',$dialplan_number)) and (strlen($dialplan_number)<5) and ($protocol == 'Local') )
			{
			$SIP_user = "$protocol/$extension$VD_login";
			}


		$session_ext = eregi_replace("[^a-z0-9]", "", $extension);
		if (strlen($session_ext) > 10) {$session_ext = substr($session_ext, 0, 10);}
		$session_rand = (rand(1,9999999) + 10000000);
		$session_name = "$StarTtimE$US$session_ext$session_rand";

		if ($webform_sessionname)
			{$webform_sessionname = "&session_name=$session_name";}
		else
			{$webform_sessionname = '';}

		$stmt="DELETE from web_client_sessions where start_time < '$past_month_date' and extension='$extension' and server_ip = '$server_ip' and program = 'phone';";
		if ($DB) {echo "|$stmt|\n";}
		$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'09018',$VD_login,$server_ip,$session_name,$one_mysql_log);}

		$stmt="INSERT INTO web_client_sessions values('$extension','$server_ip','phone','$NOW_TIME','$session_name');";
		if ($DB) {echo "|$stmt|\n";}
		$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'09019',$VD_login,$server_ip,$session_name,$one_mysql_log);}


		$VICIDiaL_is_logged_in=1;

		$webphone_content='';
		### build Iframe variable content for webphone here
		$codecs_list = preg_replace("/ /",'',$codecs_list);
		$codecs_list = preg_replace("/-/",'',$codecs_list);
		$codecs_list = preg_replace("/&/",'',$codecs_list);
		$webphone_server_ip = $server_ip;
		if ($use_external_server_ip=='Y')
			{
			##### find external_server_ip if enabled for this phone account
			$stmt="SELECT external_server_ip FROM servers where server_ip='$server_ip' LIMIT 1;";
			$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'09020',$VD_login,$server_ip,$session_name,$one_mysql_log);}
			if ($DB) {echo "$stmt\n";}
			$exip_ct = mysql_num_rows($rslt);
			if ($exip_ct > 0)
				{
				$row=mysql_fetch_row($rslt);
				$webphone_server_ip =$row[0];
				}
			}
		if (strlen($webphone_url) < 6)
			{
			##### find webphone_url in system_settings and generate IFRAME code for it #####
			$stmt="SELECT webphone_url FROM system_settings LIMIT 1;";
			$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'09021',$VD_login,$server_ip,$session_name,$one_mysql_log);}
			if ($DB) {echo "$stmt\n";}
			$wu_ct = mysql_num_rows($rslt);
			if ($wu_ct > 0)
				{
				$row=mysql_fetch_row($rslt);
				$webphone_url =$row[0];
				}
			}
		if (strlen($system_key) < 1)
			{
			##### find system_key in system_settings if populated #####
			$stmt="SELECT webphone_systemkey FROM system_settings LIMIT 1;";
			$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'09022',$VD_login,$server_ip,$session_name,$one_mysql_log);}
			if ($DB) {echo "$stmt\n";}
			$wsk_ct = mysql_num_rows($rslt);
			if ($wsk_ct > 0)
				{
				$row=mysql_fetch_row($rslt);
				$system_key =$row[0];
				}
			}
		$webphone_options='INITIAL_LOAD';
		if ($webphone_dialpad == 'Y') {$webphone_options .= "--DIALPAD_Y";}
		if ($webphone_dialpad == 'N') {$webphone_options .= "--DIALPAD_N";}
		if ($webphone_dialpad == 'TOGGLE') {$webphone_options .= "--DIALPAD_TOGGLE";}
		if ($webphone_dialpad == 'TOGGLE_OFF') {$webphone_options .= "--DIALPAD_OFF_TOGGLE";}
		if ($webphone_auto_answer == 'Y') {$webphone_options .= "--AUTOANSWER_Y";}
		if ($webphone_auto_answer == 'N') {$webphone_options .= "--AUTOANSWER_N";}

		### base64 encode variables
		$b64_phone_login =		base64_encode($extension);
		$b64_phone_pass =		base64_encode($conf_secret);
		$b64_session_name =		base64_encode($session_name);
		$b64_server_ip =		base64_encode($webphone_server_ip);
		$b64_callerid =			base64_encode($outbound_cid);
		$b64_protocol =			base64_encode($protocol);
		$b64_codecs =			base64_encode($codecs_list);
		$b64_options =			base64_encode($webphone_options);
		$b64_system_key =		base64_encode($system_key);

		$WebPhonEurl = "$webphone_url?phone_login=$b64_phone_login&phone_login=$b64_phone_login&phone_pass=$b64_phone_pass&server_ip=$b64_server_ip&callerid=$b64_callerid&protocol=$b64_protocol&codecs=$b64_codecs&options=$b64_options&system_key=$b64_system_key";
		if ($webphone_location == 'bar')
			{
			$webphone_content = "<iframe src=\"$WebPhonEurl\" style=\"width:1100px;height:500px;background-color:transparent;z-index:17;\" scrolling=\"no\" frameborder=\"0\" allowtransparency=\"true\" id=\"webphone\" name=\"webphone\" width=\"" . $webphone_width . "px\" height=\"" . $webphone_height . "px\"> </iframe>";
			}
		else
			{
			$webphone_content = "<iframe src=\"$WebPhonEurl\" style=\"width:1100px;height:500px;background-color:transparent;z-index:17;\" scrolling=\"auto\" frameborder=\"0\" allowtransparency=\"true\" id=\"webphone\" name=\"webphone\" width=\"" . $webphone_width . "px\" height=\"" . $webphone_height . "px\"> </iframe>";
			}

		if (ereg('MSIE',$browser)) 
			{
			$useIE=1;
			echo "<!-- client web browser used: MSIE |$browser|$useIE| -->\n";
			}
		else 
			{
			$useIE=0;
			echo "<!-- client web browser used: W3C-Compliant |$browser|$useIE| -->\n";
			}

		}
	}


### SCREEN WIDTH AND HEIGHT CALCULATIONS ###
### DO NOT EDIT! ###
if ($stretch_dimensions > 0)
	{
	if ($agent_status_view < 1)
		{
		if ($JS_browser_width >= 510)
			{$BROWSER_WIDTH = ($JS_browser_width - 80);}
		}
	else
		{
		if ($JS_browser_width >= 730)
			{$BROWSER_WIDTH = ($JS_browser_width - 300);}
		}
	if ($JS_browser_height >= 340)
		{$BROWSER_HEIGHT = ($JS_browser_height - 40);}
	}
if ($agent_fullscreen=='Y')
	{
	$BROWSER_WIDTH = ($JS_browser_width - 10);
	$BROWSER_HEIGHT = $JS_browser_height;
	}
$MASTERwidth=($BROWSER_WIDTH - 340);
$MASTERheight=($BROWSER_HEIGHT - 200);
if ($MASTERwidth < 430) {$MASTERwidth = '430';} 
if ($MASTERheight < 300) {$MASTERheight = '300';} 
if ($webphone_location == 'bar') {$MASTERwidth = ($MASTERwidth + $webphone_height);}

$CAwidth =  ($MASTERwidth + 340);	# 770 - cover all (none-in-session, customer hunngup, etc...)
$SBwidth =	($MASTERwidth + 331);	# 761 - SideBar starting point
$MNwidth =  ($MASTERwidth + 330);	# 760 - main frame
$XFwidth =  ($MASTERwidth + 320);	# 750 - transfer/conference
$HCwidth =  ($MASTERwidth + 310);	# 740 - hotkeys and callbacks
$CQwidth =  ($MASTERwidth + 300);	# 730 - calls in queue listings
$AMwidth =  ($MASTERwidth + 270);	# 700 - refresh links
$SCwidth =  ($MASTERwidth + 230);	# 670 - live call seconds counter, sidebar link
$PDwidth =  ($MASTERwidth + 210);	# 650 - preset-dial links
$MUwidth =  ($MASTERwidth + 180);	# 610 - agent mute
$SSwidth =  ($MASTERwidth + 176);	# 606 - scroll script
$SDwidth =  ($MASTERwidth + 170);	# 600 - scroll script, customer data and calls-in-session
$HKwidth =  ($MASTERwidth + 20);	# 450 - Hotkeys button
$HSwidth =  ($MASTERwidth + 1);		# 431 - Header spacer
$PBwidth =  ($MASTERwidth + 0);		# 430 - Presets list
$CLwidth =  ($MASTERwidth - 120);	# 310 - Calls in queue link


$GHheight =  ($MASTERheight + 1260);# 1560 - Gender Hide span
$DBheight =  ($MASTERheight + 260);	# 560 - Debug span
$WRheight =  ($MASTERheight + 160);	# 460 - Warning boxes
$CQheight =  ($MASTERheight + 140);	# 440 - Calls in queue section
$SLheight =  ($MASTERheight + 122);	# 422 - SideBar link, Agents view link
$QLheight =  ($MASTERheight + 112);	# 412 - Calls in queue link
$HKheight =  ($MASTERheight + 105);	# 405 - HotKey active Button
$AMheight =  ($MASTERheight + 100);	# 400 - Agent mute buttons
$PBheight =  ($MASTERheight + 90);	# 390 - preset dial links
$MBheight =  ($MASTERheight + 65);	# 365 - Manual Dial Buttons
$CBheight =  ($MASTERheight + 50);	# 350 - Agent Callback, pause code, volume control Buttons and agent status
$SSheight =  ($MASTERheight + 31);	# 331 - script content
$HTheight =  ($MASTERheight + 10);	# 310 - transfer frame, callback comments and hotkey
$BPheight =  ($MASTERheight - 250);	# 50 - bottom buffer, Agent Xfer Span
$SCheight =	 49;	# 49 - seconds on call display
$SFheight =	 65;	# 65 - height of the script and form contents
$SRheight =	 69;	# 69 - height of the script and form refrech links
if ($webphone_location == 'bar') 
	{
	$SCheight = ($SCheight + $webphone_height);
#	$SFheight = ($SFheight + $webphone_height);
	$SRheight = ($SRheight + $webphone_height);
	}
$AVTheight = '0';
if ($is_webphone) {$AVTheight = '20';}




echo "</head>\n";

$zi=2;

echo "<body bgcolor=\"white\">\n";

echo " Phone: $original_phone_login - $server_ip &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; <a href=\"$PHP_SELF?relogin=YES&session_epoch=1234567890&session_id=&session_name=$session_name&VD_login=$VD_login&phone_login=$original_phone_login&phone_pass=$phone_pass&VD_pass=$VD_pass\">Logout</a><BR>\n";

if ($webphone_location == 'bar')
	{
	echo "<span style=\"position:absolute;left:0px;top:30px;height:500px;width=".$webphone_width."px;overflow:hidden;z-index:$zi;background-color:$SIDEBAR_COLOR;\" id=\"webphoneSpanBAR\"><span id=\"webphonecontent\" style=\"overflow:hidden;\">$webphone_content</span></span>\n";
	}
else
	{
    echo "<span style=\"position:absolute;left:0px;top:30px;height:500px;overflow:scroll;z-index:$zi;background-color:$SIDEBAR_COLOR;\" id=\"webphoneSpanDEFAULT\"><table cellpadding=\"$webphone_pad\" cellspacing=\"0\" border=\"0\"><tr><td width=\"5px\" rowspan=\"2\">&nbsp;</td><td align=\"center\"><font class=\"body_text\">
    Web Phone: &nbsp; </font></td></tr><tr><td align=\"center\"><span id=\"webphonecontent\">$webphone_content</span></td></tr></table></span>\n";
	}
?>

</body>
</html>

<?php
	
exit; 

?>

