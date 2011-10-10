<?php
# astguiclient.php - the web-based version of the astGUIclient client application
# 
# Copyright (C) 2009  Matt Florell <vicidial@gmail.com>    LICENSE: AGPLv2
#
# make sure you have added a user to the vicidial_users MySQL table with at least
# user_level 1 or greater to access this page. Also you need to have the login
# and pass of a phone listed in the asterisk.phones table. The page grabs the 
# server info and other details from this login and pass
#
# Other scripts that this application depends on:
# - active_list_refresh.php: displays active/live channels and phones
# - manager_send.php: sends Actions to be executed on Asterisk servers
# - live_exten_check.php: checks to see if user's phone is on a live call
# - call_log_display.php: retrieves log of inbound and outbound calls
# - voicemail_check.php: retrieves counts of new and old messages
# - inbound_popup.php: opens upon live_inbound call coming in
# - conf_exten_check.php: checks to see if and calls are in a specific conf
# - park_calls_display.php: retrieves list of parked calls
# - vdc_db_query.php: Changes values in the DB for non-calling records
#
# CHANGES
# 50215-1356 - Proof-of-concept test of XMLHttpRequest for astGUIclient web
# 50323-1411 - First build version display-only
# 50331-1040 - Second-build, added phone login and hangup/hijack display
# 50401-1006 - Trunk/Local Hangup functions enabled
# 50404-1056 - Trunk/Local Hijack functions enabled
# 50404-1459 - Simple live calls display and grabs updated time from server
# 50405-1221 - Reorganized the display and layers, added some images
# 50406-1005 - Added In/Out call log display to MAIN panel
# 50407-1254 - Start/Stop Recording on live calls enabled
# 50422-1101 - Activated Check Voicemail button and new/old messages count
# 50428-1449 - Added dial from log and basic live_inbound call popup
# 50429-1455 - Modified inbound popup code for IE and to add more functions
# 50502-1442 - Added basic method to transfer live calls somewhere else
# 50503-1205 - Added web_client_sessions entry for more security of subscripts
# 50503-1537 - Added basic conferences display
# 50509-1132 - Added conference connected list and hangup/xfer for them
# 50511-1129 - Added registration of conference rooms and added manual dial
# 50523-1342 - Added Conference recording and send DTMF
# 50523-1622 - Added Local Dial window frame for calling local extensions
# 50524-1456 - Added ability to park call and display number of parked calls
# 50524-1600 - Added ability to display and pickup/hangup/xfer parked calls
# 50525-1224 - Added ability to place outbound call from within conferene
# 50531-1225 - Added ability to do dual transfers to meetme rooms from Main
# 50711-1229 - removed HTTP authentication in favor of user/pass vars
# 50711-1610 - Added Zap monitoring to Trunk/Local Action screens
# 50804-1604 - Minor bug fix in inbound_popup functions
# 50818-1715 - Added pretty login section
# 50913-1137 - Added custom outbound_cid from phones table
# 51110-1430 - Fixed non-standard http port issue
# 60103-1421 - Added code for favorite extensions chooser
# 60104-1347 - Added basic layout for favorites editing frame
# 60105-1124 - Finished Favorites frame and added DB submission
# 60112-1622 - Several formatting changes
# 60421-1357 - check GET/POST vars lines with isset to not trigger PHP NOTICES
# 60619-1103 - Added variable filters to close security holes for login form
# 60829-1528 - Made compatible with WeBRooTWritablE setting in dbconnect.php
# 90508-0727 - Changed to PHP long tags
# 91129-2211 - Replaced SELECT STAR in SQL query
# 

require("dbconnect.php");

### If you have globals turned off uncomment these lines
if (isset($_GET["user"]))					{$user=$_GET["user"];}
	elseif (isset($_POST["user"]))			{$user=$_POST["user"];}
if (isset($_GET["pass"]))					{$pass=$_GET["pass"];}
	elseif (isset($_POST["pass"]))			{$pass=$_POST["pass"];}
if (isset($_GET["phone_login"]))			{$phone_login=$_GET["phone_login"];}
	elseif (isset($_POST["phone_login"]))	{$phone_login=$_POST["phone_login"];}
if (isset($_GET["phone_pass"]))				{$phone_pass=$_GET["phone_pass"];}
	elseif (isset($_POST["phone_pass"]))	{$phone_pass=$_POST["phone_pass"];}
if (isset($_GET["relogin"]))				{$relogin=$_GET["relogin"];}
	elseif (isset($_POST["relogin"]))		{$relogin=$_POST["relogin"];}
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

$forever_stop=0;
$user_abb = "$user$user$user$user";
while ( (strlen($user_abb) > 4) and ($forever_stop < 200) )
	{$user_abb = eregi_replace("^.","",$user_abb);   $forever_stop++;}

$version = '2.2.0';
$build = '91129-2211';

### security strip all non-alphanumeric characters out of the variables ###
	$DB=ereg_replace("[^0-9a-z]","",$DB);
	$phone_login=ereg_replace("[^0-9a-zA-Z]","",$phone_login);
	$phone_pass=ereg_replace("[^0-9a-zA-Z]","",$phone_pass);
	$user=ereg_replace("[^0-9a-zA-Z]","",$user);
	$pass=ereg_replace("[^0-9a-zA-Z]","",$pass);


if ($force_logout)
	{
	if( (strlen($_SERVER['user'])>0) or (strlen($_SERVER['pass'])>0) )
		{
		Header("WWW-Authenticate: Basic realm=\"VICI-PROJECTS\"");
		Header("HTTP/1.0 401 Unauthorized");
		}
    echo "You have now logged out. Thank you\n";
    exit;
	}

$StarTtime = date("U");
$NOW_TIME = date("Y-m-d H:i:s");
$FILE_TIME = date("Ymd-His");
	$month_old = mktime(0, 0, 0, date("m"), date("d")-7,  date("Y"));
	$past_month_date = date("Y-m-d H:i:s",$month_old);

	$stmt="SELECT count(*) from vicidial_users where user='$user' and pass='$pass' and user_level > 0;";
	if ($DB) {echo "|$stmt|\n";}
	$rslt=mysql_query($stmt, $link);
	$row=mysql_fetch_row($rslt);
	$auth=$row[0];

$US='_';
$CL=':';
if ($WeBRooTWritablE > 0)
	{$fp = fopen ("./astguiclient_auth_entries.txt", "a");}
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
$agcDIR = $agcPAGE;
$agcDIR = eregi_replace('astguiclient.php','',$agcDIR);

if( (strlen($user)<2) or (strlen($pass)<2) or (!$auth) or ($relogin == 'YES') )
	{
	header ("Content-type: text/html; charset=utf-8");

	echo "<title>astGUIclient web client: Login</title>\n";
	echo "</head>\n";
	echo "<BODY BGCOLOR=WHITE MARGINHEIGHT=0 MARGINWIDTH=0>\n";
	echo "<TABLE><TR><TD></TD>\n";
	echo "<!-- INTERNATIONALIZATION-LINKS-PLACEHOLDER-AGC -->\n";
	echo "</TR></TABLE>\n";
	echo "<FORM ACTION=\"$agcPAGE\" METHOD=POST>\n";
	echo "<INPUT TYPE=HIDDEN NAME=DB VALUE=\"$DB\">\n";
	echo "<BR><BR><BR><CENTER><TABLE WIDTH=360 CELLPADDING=0 CELLSPACING=0 BGCOLOR=\"#CCC2E0\"><TR BGCOLOR=WHITE>";
	echo "<TD ALIGN=LEFT VALIGN=BOTTOM><IMG SRC=\"./images/agc_tab_astguiclient.gif\" BORDER=0></TD>";
	echo "<TD ALIGN=CENTER VALIGN=MIDDLE> Login </TD>";
	echo "</TR>\n";
	echo "<TR><TD ALIGN=LEFT COLSPAN=2><font size=1> &nbsp; </TD></TR>\n";
	echo "<TR><TD ALIGN=RIGHT>User Login:  </TD>";
	echo "<TD ALIGN=LEFT><INPUT TYPE=TEXT NAME=user SIZE=10 MAXLENGTH=20 VALUE=\"$user\"></TD></TR>\n";
	echo "<TR><TD ALIGN=RIGHT>User Password:  </TD>";
	echo "<TD ALIGN=LEFT><INPUT TYPE=PASSWORD NAME=pass SIZE=10 MAXLENGTH=20 VALUE=\"$pass\"></TD></TR>\n";
	echo "<TR><TD ALIGN=RIGHT>Phone Login: </TD>";
	echo "<TD ALIGN=LEFT><INPUT TYPE=TEXT NAME=phone_login SIZE=10 MAXLENGTH=20 VALUE=\"$phone_login\"></TD></TR>\n";
	echo "<TR><TD ALIGN=RIGHT>Phone Password:  </TD>";
	echo "<TD ALIGN=LEFT><INPUT TYPE=PASSWORD NAME=phone_pass SIZE=10 MAXLENGTH=20 VALUE=\"$phone_pass\"></TD></TR>\n";
	echo "<TR><TD ALIGN=CENTER COLSPAN=2><INPUT TYPE=SUBMIT NAME=SUBMIT VALUE=SUBMIT></TD></TR>\n";
	echo "<TR><TD ALIGN=LEFT COLSPAN=2><font size=1><BR>VERSION: $version &nbsp; &nbsp; &nbsp; BUILD: $build</TD></TR>\n";
	echo "</TABLE>\n";
	echo "</FORM>\n\n";
	echo "</body>\n\n";
	echo "</html>\n\n";
	exit;
	}
else
	{

	if($auth>0)
		{
		$office_no=strtoupper($user);
		$password=strtoupper($pass);
			$stmt="SELECT full_name,user_level from vicidial_users where user='$user' and pass='$pass'";
			$rslt=mysql_query($stmt, $link);
			$row=mysql_fetch_row($rslt);
			$LOGfullname=$row[0];
		if ($WeBRooTWritablE > 0)
			{
			fwrite ($fp, "VICIDIAL|GOOD|$date|$user|$pass|$ip|$browser|$LOGfullname|\n");
			fclose($fp);
			}
		}
	else
		{
		if ($WeBRooTWritablE > 0)
			{
			fwrite ($fp, "VICIDIAL|FAIL|$date|$user|$pass|$ip|$browser|$LOGfullname|\n");
			fclose($fp);
			}
		}
	}

header ("Content-type: text/html; charset=utf-8");
header ("Cache-Control: no-cache, must-revalidate");  // HTTP/1.1
header ("Pragma: no-cache");                          // HTTP/1.0
echo "<html>\n";
echo "<head>\n";
echo "<!-- VERSION: $version     BUILD: $build      ADD: $ADD-->\n";

if ( (strlen($phone_login)<2) or (strlen($phone_pass)<2) )
{
echo "<title>astGUIclient web client: Phone Login</title>\n";
echo "</head>\n";
echo "<BODY BGCOLOR=WHITE MARGINHEIGHT=0 MARGINWIDTH=0>\n";
echo "<TABLE><TR><TD></TD>\n";
echo "<!-- INTERNATIONALIZATION-LINKS-PLACEHOLDER-AGC -->\n";
echo "</TR></TABLE>\n";
echo "<FORM ACTION=\"$agcPAGE\" METHOD=POST>\n";
echo "<INPUT TYPE=HIDDEN NAME=DB VALUE=\"$DB\">\n";
echo "<INPUT TYPE=HIDDEN NAME=user VALUE=\"$user\">\n";
echo "<INPUT TYPE=HIDDEN NAME=pass VALUE=\"$pass\">\n";
echo "<BR><BR><BR><CENTER><TABLE WIDTH=360 CELLPADDING=0 CELLSPACING=0 BGCOLOR=\"#CCC2E0\"><TR BGCOLOR=WHITE>";
echo "<TD ALIGN=LEFT VALIGN=BOTTOM><IMG SRC=\"./images/agc_tab_astguiclient.gif\" BORDER=0></TD>";
echo "<TD ALIGN=CENTER VALIGN=MIDDLE> Phone Login </TD>";
echo "</TR>\n";
echo "<TR><TD ALIGN=LEFT COLSPAN=2><font size=1> &nbsp; </TD></TR>\n";
echo "<TR><TD ALIGN=RIGHT>Phone Login: </TD>";
echo "<TD ALIGN=LEFT><INPUT TYPE=TEXT NAME=phone_login SIZE=10 MAXLENGTH=20 VALUE=\"$phone_login\"></TD></TR>\n";
echo "<TR><TD ALIGN=RIGHT>Phone Password:  </TD>";
echo "<TD ALIGN=LEFT><INPUT TYPE=PASSWORD NAME=phone_pass SIZE=10 MAXLENGTH=20 VALUE=\"$phone_pass\"></TD></TR>\n";
echo "<TR><TD ALIGN=CENTER COLSPAN=2><INPUT TYPE=SUBMIT NAME=SUBMIT VALUE=SUBMIT></TD></TR>\n";
echo "<TR><TD ALIGN=LEFT COLSPAN=2><font size=1><BR>VERSION: $version &nbsp; &nbsp; &nbsp; BUILD: $build</TD></TR>\n";
echo "</TABLE>\n";
echo "</FORM>\n\n";
echo "</body>\n\n";
echo "</html>\n\n";
exit;
}
else
{
$authphone=0;
$stmt="SELECT count(*) from phones where login='$phone_login' and pass='$phone_pass' and active = 'Y';";
if ($DB) {echo "|$stmt|\n";}
$rslt=mysql_query($stmt, $link);
$row=mysql_fetch_row($rslt);
$authphone=$row[0];
if (!$authphone)
	{
	echo "<title>astGUIclient web client: Phone Login</title>\n";
	echo "</head>\n";
	echo "<BODY BGCOLOR=WHITE MARGINHEIGHT=0 MARGINWIDTH=0>\n";
	echo "<TABLE><TR><TD></TD>\n";
	echo "<!-- INTERNATIONALIZATION-LINKS-PLACEHOLDER-AGC -->\n";
	echo "</TR></TABLE>\n";
	echo "<FORM ACTION=\"$agcPAGE\" METHOD=POST>\n";
	echo "<INPUT TYPE=HIDDEN NAME=DB VALUE=\"$DB\">\n";
	echo "<INPUT TYPE=HIDDEN NAME=user VALUE=\"$user\">\n";
	echo "<INPUT TYPE=HIDDEN NAME=pass VALUE=\"$pass\">\n";
	echo "<BR><BR><BR><CENTER><TABLE WIDTH=360 CELLPADDING=0 CELLSPACING=0 BGCOLOR=\"#CCC2E0\"><TR BGCOLOR=WHITE>";
	echo "<TD ALIGN=LEFT VALIGN=BOTTOM><IMG SRC=\"./images/agc_tab_astguiclient.gif\" BORDER=0></TD>";
	echo "<TD ALIGN=CENTER VALIGN=MIDDLE> Phone Login </TD>";
	echo "</TR>\n";
	echo "<TR><TD ALIGN=CENTER COLSPAN=2><font size=1> &nbsp; <BR><FONT SIZE=3>Sorry, your phone login and password are not active in this system, please try again: <BR> &nbsp; </TD></TR>\n";
	echo "<TR><TD ALIGN=RIGHT>Phone Login: </TD>";
	echo "<TD ALIGN=LEFT><INPUT TYPE=TEXT NAME=phone_login SIZE=10 MAXLENGTH=20 VALUE=\"$phone_login\"></TD></TR>\n";
	echo "<TR><TD ALIGN=RIGHT>Phone Password:  </TD>";
	echo "<TD ALIGN=LEFT><INPUT TYPE=PASSWORD NAME=phone_pass SIZE=10 MAXLENGTH=20 VALUE=\"$phone_pass\"></TD></TR>\n";
	echo "<TR><TD ALIGN=CENTER COLSPAN=2><INPUT TYPE=SUBMIT NAME=SUBMIT VALUE=SUBMIT></TD></TR>\n";
	echo "<TR><TD ALIGN=LEFT COLSPAN=2><font size=1><BR>VERSION: $version &nbsp; &nbsp; &nbsp; BUILD: $build</TD></TR>\n";
	echo "</TABLE>\n";
	echo "</FORM>\n\n";
	echo "</body>\n\n";
	echo "</html>\n\n";
	exit;
	}
else
	{
	echo "<title>astGUIclient web client</title>\n";
	$stmt="SELECT extension,dialplan_number,voicemail_id,phone_ip,computer_ip,server_ip,login,pass,status,active,phone_type,fullname,company,picture,messages,old_messages,protocol,local_gmt,ASTmgrUSERNAME,ASTmgrSECRET,login_user,login_pass,login_campaign,park_on_extension,conf_on_extension,VICIDIAL_park_on_extension,VICIDIAL_park_on_filename,monitor_prefix,recording_exten,voicemail_exten,voicemail_dump_exten,ext_context,dtmf_send_extension,call_out_number_group,client_browser,install_directory,local_web_callerID_URL,VICIDIAL_web_URL,AGI_call_logging_enabled,user_switching_enabled,conferencing_enabled,admin_hangup_enabled,admin_hijack_enabled,admin_monitor_enabled,call_parking_enabled,updater_check_enabled,AFLogging_enabled,QUEUE_ACTION_enabled,CallerID_popup_enabled,voicemail_button_enabled,enable_fast_refresh,fast_refresh_rate,enable_persistant_mysql,auto_dial_next_number,VDstop_rec_after_each_call,DBX_server,DBX_database,DBX_user,DBX_pass,DBX_port,DBY_server,DBY_database,DBY_user,DBY_pass,DBY_port,outbound_cid,enable_sipsak_messages,email,template_id,conf_override,phone_context,phone_ring_timeout,conf_secret from phones where login='$phone_login' and pass='$phone_pass' and active = 'Y';";
	if ($DB) {echo "|$stmt|\n";}
	$rslt=mysql_query($stmt, $link);
	$row=mysql_fetch_row($rslt);
	$extension=$row[0];
	$dialplan_number=$row[1];
	$voicemail_id=$row[2];
	$phone_ip=$row[3];
	$computer_ip=$row[4];
	$server_ip=$row[5];
	$phone_login=$row[6];
	$phone_pass=$row[7];
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
	$CaLLerID_popup_enabled=$row[48];
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

	$local_web_callerID_URL_enc = rawurlencode($local_web_callerID_URL);

	$session_ext = eregi_replace("[^a-z0-9]", "", $extension);
	if (strlen($session_ext) > 10) {$session_ext = substr($session_ext, 0, 10);}
	$session_rand = (rand(1,9999999) + 10000000);
	$session_name = "$StarTtime$US$session_ext$session_rand";

	$stmt="DELETE from web_client_sessions where start_time < '$past_month_date' and extension='$extension' and server_ip = '$server_ip' and program = 'agc';";
	if ($DB) {echo "|$stmt|\n";}
	$rslt=mysql_query($stmt, $link);

	$stmt="INSERT INTO web_client_sessions values('$extension','$server_ip','agc','$NOW_TIME','$session_name');";
	if ($DB) {echo "|$stmt|\n";}
	$rslt=mysql_query($stmt, $link);

	$stmt="SELECT count(*) from phone_favorites where extension='$extension' and server_ip = '$server_ip';";
	if ($DB) {echo "|$stmt|\n";}
	$rslt=mysql_query($stmt, $link);
	$row=mysql_fetch_row($rslt);
	$favorites_present=$row[0];
	if ($favorites_present > 0)
		{
		$stmt="SELECT extensions_list from phone_favorites where extension='$extension' and server_ip = '$server_ip';";
		if ($DB) {echo "|$stmt|\n";}
		$rslt=mysql_query($stmt, $link);
		$row=mysql_fetch_row($rslt);
		$favorites_list=$row[0];
		$h=0;
		$favorites_listX = eregi_replace("'",'',$favorites_list);
		$favorites = explode(',',$favorites_listX);
		$favorites_count = count($favorites);
		$favorites_listX='';

		$o=0;
		while ($favorites_count > $o) 
			{
			$stmt="SELECT fullname,protocol from phones where extension = '$favorites[$o]' and server_ip='$server_ip';";
			$rslt=mysql_query($stmt, $link);
			$rowx=mysql_fetch_row($rslt);
			$favorites_names[$o] =	$rowx[0];
			$favorites_listX .= "$rowx[1]/$favorites[$o],";
			$o++;
			}

		echo "<!-- |$favorites_list| -->\n";
		echo "<!-- |$favorites_listX| -->\n";
		}
	else
		{
		echo "<!-- No Extension Favorites Present -->\n";
		}

### gather phone extensions and fullnames for favorites editor ###
	$stmt="SELECT extension,fullname from phones where server_ip = '$server_ip';";
	if ($DB) {echo "|$stmt|\n";}
	$rslt=mysql_query($stmt, $link);
	$exten_ct = mysql_num_rows($rslt);
	$favlistCT=0;
	$nofavlistCT=0;
	while ($favlistCT < $exten_ct)
		{
		$row=mysql_fetch_row($rslt);
		$favlist[$favlistCT]= "$row[0] - $row[1]";
		$favlistCT++;
		}

	}
}


?>
	<script language="Javascript">	
	var MTvar;
	var loop_ct = 0;
	var t = new Date();
	LCAe = new Array('','','','','','');
	LCAc = new Array('','','','','','');
	LCAt = new Array('','','','','','');
	LMAe = new Array('','','','','','');
	var recLIST = '';
	var filename = '';
	var last_filename = '';
	var LCAcount = 0;
	var LMAcount = 0;
	var filedate = '<?php echo $FILE_TIME ?>';
	var agcDIR = '<?php echo $agcDIR ?>';
	var agcPAGE = '<?php echo $agcPAGE ?>';
	var extension = '<?php echo $extension ?>';
	var extension_xfer = '<?php echo $extension ?>';
	var dialplan_number = '<?php echo $dialplan_number ?>';
	var ext_context = '<?php echo $ext_context ?>';
	var protocol = '<?php echo $protocol ?>';
	var server_ip = '<?php echo $server_ip ?>';
<?php
if ($enable_fast_refresh < 1) {echo "var refresh_interval = 1000;\n";}
	else {echo "\tvar refresh_interval = $fast_refresh_rate;\n";}
?>
	var user_abb = '<?php echo $user_abb ?>';
	var voicemail_id = '<?php echo $voicemail_id ?>';
	var voicemail_exten = '<?php echo $voicemail_exten ?>';
	var voicemail_dump_exten = '<?php echo $voicemail_dump_exten ?>';
	var new_messages = 999;
	var old_messages = 999;
	var outbound_cid = '<?php echo $outbound_cid ?>';
	var local_web_callerID_URL_enc = '<?php echo $local_web_callerID_URL_enc ?>';
	var epoch_sec = <?php echo $StarTtime ?>;
	var dtmf_send_extension = '<?php echo $dtmf_send_extension ?>';
	var recording_exten = '<?php echo $recording_exten ?>';
	var park_on_extension = '<?php echo $park_on_extension ?>';
	var park_count=0;
	var park_refresh=0;
	var check_n = 0;
	var conf_check_recheck = 0;
	var logout_stop_timeouts = 0;
	var lastconf='';
	var agc_dial_prefix = '91';
	var monitor_prefix = '<?php echo $monitor_prefix ?>';
	var menuheight = 30;
	var menuwidth = 30;
	var menufontsize = 8;
	var textareafontsize = 10;
	var check_s;
	var active_display = 1;
	var display_message = '';
	var Nactiveext;
	var Nbusytrunk;
	var Nbusyext;
	var extvalue = extension;
	var activeext_query;
	var busytrunk_query;
	var busyext_query;
	var busytrunkhangup_query;
	var busylocalhangup_query;
	var activeext_order='asc';
	var busytrunk_order='asc';
	var busyext_order='asc';
	var busytrunkhangup_order='asc';
	var busylocalhangup_order='asc';
	var xmlhttp=false;
	var admin_hangup_enabled = '<?php echo $admin_hangup_enabled ?>';
	var admin_hijack_enabled = '<?php echo $admin_hijack_enabled ?>';
	var admin_monitor_enabled = '<?php echo $admin_monitor_enabled ?>';
	var XfeR_channel = '';
	var user = '<?php echo $user ?>';
	var pass = '<?php echo $pass ?>';
	var phone_login = '<?php echo $phone_login ?>';
	var phone_pass = '<?php echo $phone_pass ?>';
	var session_name = '<?php echo $session_name ?>';
	var image_livecall_OFF = new Image();
	image_livecall_OFF.src="./images/agc_live_call_OFF.gif";
	var image_livecall_ON = new Image();
	image_livecall_ON.src="./images/agc_live_call_ON.gif";
	var image_voicemail_OFF = new Image();
	image_voicemail_OFF.src="./images/agc_check_voicemail_OFF.gif";
	var image_voicemail_ON = new Image();
	image_voicemail_ON.src="./images/agc_check_voicemail_ON.gif";
	var image_voicemail_BLINK = new Image();
	image_voicemail_BLINK.src="./images/agc_check_voicemail_BLINK.gif";
	var favorites = new Array();
	var favorites_names = new Array();
	var favorites_busy = new Array();
	var favoritesEDIT = new Array();
	var favorites_namesEDIT = new Array();
	var favlist = new Array();
	var favlistCT = '<?php echo $favlistCT ?>';
	var favorites_count = '<?php echo $favorites_count ?>';
	var favorites_countEDIT = '<?php echo $favorites_count ?>';
	var favorites_listX = '<?php echo $favorites_listX ?>';
	var favorites_list = "<?php echo $favorites_list ?>";
	var favorites_listEDIT = "<?php echo $favorites_list ?>";
	<?php $h=0;
	while ($favorites_count > $h)
	{
	echo "favorites['$h'] = \"$favorites[$h]\";\n";
	echo "favorites_names['$h'] = \"$favorites_names[$h]\";\n";
	echo "favorites_busy['$h'] = \"0\";\n";
	echo "favoritesEDIT['$h'] = \"$favorites[$h]\";\n";
	echo "favorites_namesEDIT['$h'] = \"$favorites_names[$h]\";\n";
	$h++;
	}
	 $h=0;
	while ($favlistCT > $h)
	{
	echo "favlist['$h'] = \"$favlist[$h]\";\n";
	$h++;
	}

	?>

// ################################################################################
// ACTIVE EXTENSIONS LIST REFRESH FUNCTIONS
	function refresh_activeext()
		{
		document.getElementById("activeext").innerHTML = Nactiveext;
		}
	function refresh_activeext_xfer()
		{
		document.getElementById("MainXfeRContent").innerHTML = Nactiveext;
		}
	function refresh_activeext_dial()
		{
		document.getElementById("LocalDialContent").innerHTML = Nactiveext;
		}
	function getactiveext(taskwindow) 
		{
		var getactiveext_window = taskwindow;
		if (getactiveext_window == "MainXfeRBox")
			{
	//		extvalue = document.extensions_list.extension_xfer.value;
			var ext_field = 'extension_xfer';
			}
		else
			{
			if (getactiveext_window == "LocalDialBox")
				{
				var ext_field = 'extension_dial';
				}
			else
				{
				if (check_n>3)
					{
					extvalue = '';
						extvalue = document.extensions_list.extension.value;
						var ext_field = 'extension';
					}
				}
			}
		var xmlhttp=false;
		/*@cc_on @*/
		/*@if (@_jscript_version >= 5)
		// JScript gives us Conditional compilation, we can cope with old IE versions.
		// and security blocked creation of the objects.
		 try {
		  xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
		 } catch (e) {
		  try {
		   xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
		  } catch (E) {
		   xmlhttp = false;
		  }
		 }
		@end @*/
		if (!xmlhttp && typeof XMLHttpRequest!='undefined')
			{
			xmlhttp = new XMLHttpRequest();
			}
		if (xmlhttp) 
			{ 
			activeext_query = "server_ip=" + server_ip + "&session_name=" + session_name + "&user=" + user + "&pass=" + pass + "&ADD=1&order=" + activeext_order + "&format=selectlist&bgcolor=#E6E6E6&selectsize=" + menuheight + "&selectedext=" + extension + "&selectfontsize=" + menufontsize + "&field_name=" + ext_field;
		//	alert(activeext_query);
			xmlhttp.open('POST', 'active_list_refresh.php'); 
			xmlhttp.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
			xmlhttp.send(activeext_query); 
			xmlhttp.onreadystatechange = function() 
				{ 
				if (xmlhttp.readyState == 4 && xmlhttp.status == 200) 
					{
			//		alert(xmlhttp.responseText);
			//		alert(getactiveext_window);
					Nactiveext = null;
					Nactiveext = xmlhttp.responseText;
					if (getactiveext_window == "MainXfeRBox")
						{refresh_activeext_xfer();}
					else
						{
						if (getactiveext_window == "LocalDialBox")
							{
							refresh_activeext_dial();
							}
						else
							{
							refresh_activeext();
							}
						}
					}
				}
			delete xmlhttp;
			}
		}

// ################################################################################
// BUSY TRUNK LIST REFRESH FUNCTIONS
	function refresh_busytrunk()
		{
		document.getElementById("busytrunk").innerHTML = Nbusytrunk;
	//	setTimeout("getbusyext()", 1000);
		}
	function getbusytrunk() 
		{
		var xmlhttp=false;
		/*@cc_on @*/
		/*@if (@_jscript_version >= 5)
		// JScript gives us Conditional compilation, we can cope with old IE versions.
		// and security blocked creation of the objects.
		 try {
		  xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
		 } catch (e) {
		  try {
		   xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
		  } catch (E) {
		   xmlhttp = false;
		  }
		 }
		@end @*/
		if (!xmlhttp && typeof XMLHttpRequest!='undefined')
			{
			xmlhttp = new XMLHttpRequest();
			}
		if (xmlhttp) 
			{ 
			busytrunk_query = "server_ip=" + server_ip + "&session_name=" + session_name + "&user=" + user + "&pass=" + pass + "&ADD=3&order=" + busytrunk_order + "&format=textarea&bgcolor=#E6E6E6&textareaheight=" + menuheight + "&textareawidth=40&selectfontsize=" + textareafontsize;
			xmlhttp.open('POST', 'active_list_refresh.php'); 
			xmlhttp.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
			xmlhttp.send(busytrunk_query); 
			xmlhttp.onreadystatechange = function() 
				{ 
				if (xmlhttp.readyState == 4 && xmlhttp.status == 200) 
					{
					Nbusytrunk = null;
					Nbusytrunk = xmlhttp.responseText;
					refresh_busytrunk();
					}
				}
			delete xmlhttp;
			}
		}

// ################################################################################
// BUSY EXTENSIONS LIST REFRESH FUNCTIONS
	function refresh_busyext()
		{
		document.getElementById("busyext").innerHTML = Nbusyext;
	//	setTimeout("getbusyext()", 1000);
		}
	function getbusyext() 
		{
		var xmlhttp=false;
		/*@cc_on @*/
		/*@if (@_jscript_version >= 5)
		// JScript gives us Conditional compilation, we can cope with old IE versions.
		// and security blocked creation of the objects.
		 try {
		  xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
		 } catch (e) {
		  try {
		   xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
		  } catch (E) {
		   xmlhttp = false;
		  }
		 }
		@end @*/
		if (!xmlhttp && typeof XMLHttpRequest!='undefined')
			{
			xmlhttp = new XMLHttpRequest();
			}
		if (xmlhttp) 
			{ 
			busyext_query = "server_ip=" + server_ip + "&session_name=" + session_name + "&user=" + user + "&pass=" + pass + "&ADD=4&order=" + busyext_order + "&format=textarea&bgcolor=#E6E6E6&textareaheight=" + menuheight + "&textareawidth=40&selectfontsize=" + textareafontsize;
			xmlhttp.open('POST', 'active_list_refresh.php'); 
			xmlhttp.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
			xmlhttp.send(busyext_query); 
			xmlhttp.onreadystatechange = function() 
				{ 
				if (xmlhttp.readyState == 4 && xmlhttp.status == 200) 
					{
					Nbusyext = null;
					Nbusyext = xmlhttp.responseText;
					refresh_busyext();
					}
				}
			delete xmlhttp;
			}
		}

// ################################################################################
// LIVE TRUNK LIST FOR HANGUP/HIJACK MENU FUNCTIONS
	function refresh_busytrunkhangup()
		{
		document.getElementById("TrunkHangupContent").innerHTML = Nactiveext;
		}
	function busytrunkhangup() 
		{
		var xmlhttp=false;
		/*@cc_on @*/
		/*@if (@_jscript_version >= 5)
		// JScript gives us Conditional compilation, we can cope with old IE versions.
		// and security blocked creation of the objects.
		 try {
		  xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
		 } catch (e) {
		  try {
		   xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
		  } catch (E) {
		   xmlhttp = false;
		  }
		 }
		@end @*/
		if (!xmlhttp && typeof XMLHttpRequest!='undefined')
			{
			xmlhttp = new XMLHttpRequest();
			}
		if (xmlhttp) 
			{ 
			busytrunkhangup_query = "server_ip=" + server_ip + "&session_name=" + session_name + "&user=" + user + "&pass=" + pass + "&ADD=3&order=" + busytrunkhangup_order + "&format=selectlist&bgcolor=#E6E6E6&selectsize=" + menuheight + "&selectfontsize=10";
		//	alert(activeext_query);
			xmlhttp.open('POST', 'active_list_refresh.php'); 
			xmlhttp.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
			xmlhttp.send(busytrunkhangup_query); 
			xmlhttp.onreadystatechange = function() 
				{ 
				if (xmlhttp.readyState == 4 && xmlhttp.status == 200) 
					{
					Nactiveext = null;
					Nactiveext = xmlhttp.responseText;
					refresh_busytrunkhangup();
					}
				}
			delete xmlhttp;
			}
		}

// ################################################################################
// LIVE LOCAL LIST FOR HANGUP/HIJACK MENU FUNCTIONS
	function refresh_busylocalhangup()
		{
		document.getElementById("LocalHangupContent").innerHTML = Nactiveext;
		}
	function busylocalhangup() 
		{
		var xmlhttp=false;
		/*@cc_on @*/
		/*@if (@_jscript_version >= 5)
		// JScript gives us Conditional compilation, we can cope with old IE versions.
		// and security blocked creation of the objects.
		 try {
		  xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
		 } catch (e) {
		  try {
		   xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
		  } catch (E) {
		   xmlhttp = false;
		  }
		 }
		@end @*/
		if (!xmlhttp && typeof XMLHttpRequest!='undefined')
			{
			xmlhttp = new XMLHttpRequest();
			}
		if (xmlhttp) 
			{ 
			busylocalhangup_query = "server_ip=" + server_ip + "&session_name=" + session_name + "&user=" + user + "&pass=" + pass + "&ADD=4&order=" + busylocalhangup_order + "&format=selectlist&bgcolor=#E6E6E6&selectsize=" + menuheight + "&selectfontsize=10";
			xmlhttp.open('POST', 'active_list_refresh.php'); 
			xmlhttp.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
			xmlhttp.send(busylocalhangup_query); 
			xmlhttp.onreadystatechange = function() 
				{ 
				if (xmlhttp.readyState == 4 && xmlhttp.status == 200) 
					{
					Nactiveext = null;
					Nactiveext = xmlhttp.responseText;
					refresh_busylocalhangup();
					}
				}
			delete xmlhttp;
			}
		}

// ################################################################################
// Send Hangup command for Trunk/Local to Manager
	function busyhangup_send_hangup(taskvar) 
		{
		var xmlhttp=false;
		/*@cc_on @*/
		/*@if (@_jscript_version >= 5)
		// JScript gives us Conditional compilation, we can cope with old IE versions.
		// and security blocked creation of the objects.
		 try {
		  xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
		 } catch (e) {
		  try {
		   xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
		  } catch (E) {
		   xmlhttp = false;
		  }
		 }
		@end @*/
		if (!xmlhttp && typeof XMLHttpRequest!='undefined')
			{
			xmlhttp = new XMLHttpRequest();
			}
		if (xmlhttp) 
			{ 
			if (taskvar == 'Trunk')
				{
				var queryCID = "HUagcW" + epoch_sec + user_abb;
				var hangupvalue = document.extensions_list.trunk.value;
				busyhangup_query = "server_ip=" + server_ip + "&session_name=" + session_name + "&user=" + user + "&pass=" + pass + "&ACTION=Hangup&format=text&channel=" + hangupvalue + "&queryCID=" + queryCID;
				}
			if (taskvar == 'Local')
				{
				var queryCID = "HUagcW" + epoch_sec + user_abb;
				var hangupvalue = document.extensions_list.local.value;
				busyhangup_query = "server_ip=" + server_ip + "&session_name=" + session_name + "&user=" + user + "&pass=" + pass + "&ACTION=Hangup&format=text&channel=" + hangupvalue + "&queryCID=" + queryCID;
				}
			xmlhttp.open('POST', 'manager_send.php'); 
			xmlhttp.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
			xmlhttp.send(busyhangup_query); 
			xmlhttp.onreadystatechange = function() 
				{ 
				if (xmlhttp.readyState == 4 && xmlhttp.status == 200) 
					{
					Nactiveext = null;
					Nactiveext = xmlhttp.responseText;
					alert(xmlhttp.responseText);
					busylocalhangup_force_refresh();
					busytrunkhangup_force_refresh();
					}
				}
			delete xmlhttp;
			}
		}

// ################################################################################
// Send Redirect command for Trunk/Local to Manager
	function busyhangup_send_redirect(taskvar,tasktrunkaction) 
		{
		var bsr_continue=1;
		if (tasktrunkaction == 'LISTEN')
			{
			var listenvalueEXT = '';
			var listenvalue = document.extensions_list.trunk.value;
			var regLa = new RegExp("Zap\\/","ig");
			if (listenvalue.match(regLa))
				{
				var listenvalueEXT = listenvalue.replace(regLa, '');
				var regLb = new RegExp("-.*","ig");
				listenvalueEXT = listenvalueEXT.replace(regLb, '');
				if (listenvalueEXT.length == 1) {listenvalueEXT = "00" + listenvalueEXT;}
				if (listenvalueEXT.length == 2) {listenvalueEXT = "0" + listenvalueEXT;}
				listenvalueEXT = monitor_prefix + "" + listenvalueEXT;
				}
			else
				{
				bsr_continue=0;
				alert("You can only monitor Zap channels:\n" + listenvalue);
				}
			}
		if (bsr_continue == '1')
			{
			var xmlhttp=false;
			/*@cc_on @*/
			/*@if (@_jscript_version >= 5)
			// JScript gives us Conditional compilation, we can cope with old IE versions.
			// and security blocked creation of the objects.
			 try {
			  xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
			 } catch (e) {
			  try {
			   xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
			  } catch (E) {
			   xmlhttp = false;
			  }
			 }
			@end @*/
			if (!xmlhttp && typeof XMLHttpRequest!='undefined')
				{
				xmlhttp = new XMLHttpRequest();
				}
			if (xmlhttp) 
				{ 
				if (taskvar == 'Trunk')
					{
					if (tasktrunkaction == 'LISTEN')
						{
						var queryCID = "ZMagcW" + epoch_sec + user_abb;
						busyredirect_query = "server_ip=" + server_ip + "&session_name=" + session_name + "&user=" + user + "&pass=" + pass + "&ACTION=Originate&format=text&channel=" + protocol + "/" + extension + "&queryCID=" + queryCID + "&exten=" + listenvalueEXT + "&ext_context=" + ext_context + "&ext_priority=1";
						}
					else
						{
						var queryCID = "HJagcW" + epoch_sec + user_abb;
						var redirectvalue = document.extensions_list.trunk.value;
						busyredirect_query = "server_ip=" + server_ip + "&session_name=" + session_name + "&user=" + user + "&pass=" + pass + "&ACTION=Redirect&format=text&channel=" + redirectvalue + "&queryCID=" + queryCID + "&exten=" + dialplan_number + "&ext_context=" + ext_context + "&ext_priority=1";
						}
					}
				if (taskvar == 'Local')
					{
					var queryCID = "HJagcW" + epoch_sec + user_abb;
					var redirectvalue = document.extensions_list.local.value;
					busyredirect_query = "server_ip=" + server_ip + "&session_name=" + session_name + "&user=" + user + "&pass=" + pass + "&ACTION=Redirect&format=text&channel=" + redirectvalue + "&queryCID=" + queryCID + "&exten=" + dialplan_number + "&ext_context=" + ext_context + "&ext_priority=1";
					}
				xmlhttp.open('POST', 'manager_send.php'); 
				xmlhttp.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
				xmlhttp.send(busyredirect_query); 
				xmlhttp.onreadystatechange = function() 
					{ 
					if (xmlhttp.readyState == 4 && xmlhttp.status == 200) 
						{
						Nactiveext = null;
						Nactiveext = xmlhttp.responseText;
						alert(xmlhttp.responseText);
						busylocalhangup_force_refresh();
						busytrunkhangup_force_refresh();
						}
					}
				delete xmlhttp;
				}
			}
		}

// ################################################################################
// Send Redirect command for live call to Manager sends phone name where call is going to
	function mainxfer_send_redirect(taskvar,taskxferconf) 
		{
		var xmlhttp=false;
		/*@cc_on @*/
		/*@if (@_jscript_version >= 5)
		// JScript gives us Conditional compilation, we can cope with old IE versions.
		// and security blocked creation of the objects.
		 try {
		  xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
		 } catch (e) {
		  try {
		   xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
		  } catch (E) {
		   xmlhttp = false;
		  }
		 }
		@end @*/
		if (!xmlhttp && typeof XMLHttpRequest!='undefined')
			{
			xmlhttp = new XMLHttpRequest();
			}
		if (xmlhttp) 
			{ 
			var redirectvalue = document.extensions_list.H_XfeR_channel.value;
			if (taskvar == 'XfeR')
				{
				var queryCID = "LRagcW" + epoch_sec + user_abb;
				var redirectdestination = document.extensions_list.extension_xfer.value;
				xferredirect_query = "server_ip=" + server_ip + "&session_name=" + session_name + "&user=" + user + "&pass=" + pass + "&ACTION=RedirectName&format=text&channel=" + redirectvalue + "&queryCID=" + queryCID + "&extenName=" + redirectdestination + "&ext_context=" + ext_context + "&ext_priority=1";
				}
			if (taskvar == 'VMAIL')
				{
				var queryCID = "LVagcW" + epoch_sec + user_abb;
				var redirectdestination = document.extensions_list.extension_xfer.value;
				xferredirect_query = "server_ip=" + server_ip + "&session_name=" + session_name + "&user=" + user + "&pass=" + pass + "&ACTION=RedirectNameVmail&format=text&channel=" + redirectvalue + "&queryCID=" + queryCID + "&exten=" + voicemail_dump_exten + "&extenName=" + redirectdestination + "&ext_context=" + ext_context + "&ext_priority=1";
				}
			if (taskvar == 'ENTRY')
				{
				var queryCID = "LEagcW" + epoch_sec + user_abb;
				var redirectdestination = document.extensions_list.extension_xfer_entry.value;
				xferredirect_query = "server_ip=" + server_ip + "&session_name=" + session_name + "&user=" + user + "&pass=" + pass + "&ACTION=Redirect&format=text&channel=" + redirectvalue + "&queryCID=" + queryCID + "&exten=" + redirectdestination + "&ext_context=" + ext_context + "&ext_priority=1";
				}
			if (taskvar == 'CONF')
				{
				if (document.extensions_list.MainXfeRconfXTRA.checked==true)
					{
					var queryCID = "LXagcW" + epoch_sec + user_abb;
					var redirectdestination = taskxferconf;
					var redirectXTRAvalue = document.extensions_list.M_XfeR_channel.value;
					xferredirect_query = "server_ip=" + server_ip + "&session_name=" + session_name + "&user=" + user + "&pass=" + pass + "&ACTION=RedirectXtra&format=text&channel=" + redirectvalue + "&queryCID=" + queryCID + "&exten=" + redirectdestination + "&ext_context=" + ext_context + "&ext_priority=1&extrachannel=" + redirectXTRAvalue;
					}
				else
					{
					var queryCID = "LCagcW" + epoch_sec + user_abb;
					var redirectdestination = taskxferconf;
					xferredirect_query = "server_ip=" + server_ip + "&session_name=" + session_name + "&user=" + user + "&pass=" + pass + "&ACTION=Redirect&format=text&channel=" + redirectvalue + "&queryCID=" + queryCID + "&exten=" + redirectdestination + "&ext_context=" + ext_context + "&ext_priority=1";
					}
				}
			if (taskvar == 'ParK')
				{
				var queryCID = "LPagcW" + epoch_sec + user_abb;
				var redirectdestination = taskxferconf;
				var parkedby = protocol + "/" + extension;
				xferredirect_query = "server_ip=" + server_ip + "&session_name=" + session_name + "&user=" + user + "&pass=" + pass + "&ACTION=RedirectToPark&format=text&channel=" + redirectdestination + "&queryCID=" + queryCID + "&exten=" + park_on_extension + "&ext_context=" + ext_context + "&ext_priority=1&extenName=park&parkedby=" + parkedby;
				}
			if (taskvar == 'FROMParK')
				{
				var queryCID = "FPagcW" + epoch_sec + user_abb;
				var redirectdestination = taskxferconf;
				xferredirect_query = "server_ip=" + server_ip + "&session_name=" + session_name + "&user=" + user + "&pass=" + pass + "&ACTION=RedirectFromPark&format=text&channel=" + redirectdestination + "&queryCID=" + queryCID + "&exten=" + dialplan_number + "&ext_context=" + ext_context + "&ext_priority=1";
				}


			xmlhttp.open('POST', 'manager_send.php'); 
			xmlhttp.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
			xmlhttp.send(xferredirect_query); 
			xmlhttp.onreadystatechange = function() 
				{ 
				if (xmlhttp.readyState == 4 && xmlhttp.status == 200) 
					{
					Nactiveext = null;
					Nactiveext = xmlhttp.responseText;
					alert(xmlhttp.responseText);
					hideMainXfeR();
					}
				}
			delete xmlhttp;
			}
		}


// ################################################################################
// Send Originate command for local dial to Manager sends phone name where call is going to
	function mainxfer_send_originate(taskvar,taskxferconf,taskentrypop) 
		{
		var xmlhttp=false;
		/*@cc_on @*/
		/*@if (@_jscript_version >= 5)
		// JScript gives us Conditional compilation, we can cope with old IE versions.
		// and security blocked creation of the objects.
		 try {
		  xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
		 } catch (e) {
		  try {
		   xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
		  } catch (E) {
		   xmlhttp = false;
		  }
		 }
		@end @*/
		if (!xmlhttp && typeof XMLHttpRequest!='undefined')
			{
			xmlhttp = new XMLHttpRequest();
			}
		if (xmlhttp) 
			{ 
			var originatevalue = protocol + "/" + extension;
			if (taskvar == 'DiaL')
				{
				var queryCID = "LDagcW" + epoch_sec + user_abb;
				if (taskentrypop.length>1)
					{
					var originatedestination = taskentrypop;
					}
				else
					{
					var originatedestination = document.extensions_list.extension_dial.value;
					}
				var localdial_query = "server_ip=" + server_ip + "&session_name=" + session_name + "&user=" + user + "&pass=" + pass + "&ACTION=OriginateName&format=text&channel=" + originatevalue + "&queryCID=" + queryCID + "&extenName=" + originatedestination + "&ext_context=" + ext_context + "&ext_priority=1&outbound_cid=" + outbound_cid;
				}
			if (taskvar == 'VMAIL')
				{
				var queryCID = "LDagcW" + epoch_sec + user_abb;
				var originatedestination = document.extensions_list.extension_dial.value;
				var localdial_query = "server_ip=" + server_ip + "&session_name=" + session_name + "&user=" + user + "&pass=" + pass + "&ACTION=OriginateNameVmail&format=text&channel=" + originatevalue + "&queryCID=" + queryCID + "&exten=" + voicemail_dump_exten + "&extenName=" + originatedestination + "&ext_context=" + ext_context + "&ext_priority=1";
				}
			if (taskvar == 'ENTRY')
				{
				var queryCID = "LEagcW" + epoch_sec + user_abb;
				var originatedestination = document.extensions_list.extension_dial_entry.value;
				var localdial_query = "server_ip=" + server_ip + "&session_name=" + session_name + "&user=" + user + "&pass=" + pass + "&ACTION=Originate&format=text&channel=" + originatevalue + "&queryCID=" + queryCID + "&exten=" + originatedestination + "&ext_context=" + ext_context + "&ext_priority=1&outbound_cid=" + outbound_cid;
				}
			if (taskvar == 'CONF')
				{
				var queryCID = "LCagcW" + epoch_sec + user_abb;
				var originatedestination = taskxferconf;
				var localdial_query = "server_ip=" + server_ip + "&session_name=" + session_name + "&user=" + user + "&pass=" + pass + "&ACTION=Originate&format=text&channel=" + originatevalue + "&queryCID=" + queryCID + "&exten=" + originatedestination + "&ext_context=" + ext_context + "&ext_priority=1&outbound_cid=" + outbound_cid;
				}

			xmlhttp.open('POST', 'manager_send.php'); 
			xmlhttp.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
			xmlhttp.send(localdial_query); 
			xmlhttp.onreadystatechange = function() 
				{ 
				if (xmlhttp.readyState == 4 && xmlhttp.status == 200) 
					{
					Nactiveext = null;
					Nactiveext = xmlhttp.responseText;
					alert(xmlhttp.responseText);
					hideLocalDial();
					}
				}
			delete xmlhttp;
			}
		}


// ################################################################################
// Send Hangup command for Live call connected to phone now to Manager
	function livehangup_send_hangup(taskvar) 
		{
		var xmlhttp=false;
		/*@cc_on @*/
		/*@if (@_jscript_version >= 5)
		// JScript gives us Conditional compilation, we can cope with old IE versions.
		// and security blocked creation of the objects.
		 try {
		  xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
		 } catch (e) {
		  try {
		   xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
		  } catch (E) {
		   xmlhttp = false;
		  }
		 }
		@end @*/
		if (!xmlhttp && typeof XMLHttpRequest!='undefined')
			{
			xmlhttp = new XMLHttpRequest();
			}
		if (xmlhttp) 
			{ 
			var queryCID = "HLagcW" + epoch_sec + user_abb;
			var hangupvalue = taskvar;
			livehangup_query = "server_ip=" + server_ip + "&session_name=" + session_name + "&user=" + user + "&pass=" + pass + "&ACTION=Hangup&format=text&channel=" + hangupvalue + "&queryCID=" + queryCID;
			xmlhttp.open('POST', 'manager_send.php'); 
			xmlhttp.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
			xmlhttp.send(livehangup_query); 
			xmlhttp.onreadystatechange = function() 
				{ 
				if (xmlhttp.readyState == 4 && xmlhttp.status == 200) 
					{
					Nactiveext = null;
					Nactiveext = xmlhttp.responseText;
					alert(xmlhttp.responseText);
					}
				}
			delete xmlhttp;
			}
		}

// ################################################################################
// Send Monitor/StopMonitor command for recording of calls
	function liverecording_send_recording(taskvar,taskchan,taskspan,taskfile) 
		{
		var xmlhttp=false;
		/*@cc_on @*/
		/*@if (@_jscript_version >= 5)
		// JScript gives us Conditional compilation, we can cope with old IE versions.
		// and security blocked creation of the objects.
		 try {
		  xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
		 } catch (e) {
		  try {
		   xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
		  } catch (E) {
		   xmlhttp = false;
		  }
		 }
		@end @*/
		if (!xmlhttp && typeof XMLHttpRequest!='undefined')
			{
			xmlhttp = new XMLHttpRequest();
			}
		if (xmlhttp) 
			{ 
			var queryCID = "MRagcW" + epoch_sec + user_abb;
			var monitorvalue = taskvar;
			var monitorchannelvalue = taskchan;
			if (taskvar == 'Monitor')
				{
				recLIST = recLIST + "|" + monitorchannelvalue;
				filename = filedate + "_" + user_abb;
				var rec_start_html = "<a href=\"#\" onclick=\"liverecording_send_recording('StopMonitor','" + monitorchannelvalue + "','" + taskspan + "','" + filename + "');return false;\">Stop Record";
				document.getElementById(taskspan).innerHTML = rec_start_html;

			}
			if (taskvar == 'StopMonitor')
				{
				var regy = new RegExp("\\|"+monitorchannelvalue,"ig");
				recLIST = recLIST.replace(regy, '');
				
				filename = taskfile;
				var rec_stop_html = "<a href=\"#\" onclick=\"liverecording_send_recording('Monitor','" + monitorchannelvalue + "','" + taskspan + "','');return false;\">Record";
				document.getElementById(taskspan).innerHTML = rec_stop_html;
				}
			livemonitor_query = "server_ip=" + server_ip + "&session_name=" + session_name + "&user=" + user + "&pass=" + pass + "&ACTION=" + monitorvalue + "&format=text&channel=" + monitorchannelvalue + "&queryCID=" + queryCID + "&filename=" + filename + "&exten=" + protocol + "/" + extension;
			xmlhttp.open('POST', 'manager_send.php'); 
			xmlhttp.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
			xmlhttp.send(livemonitor_query); 
			xmlhttp.onreadystatechange = function() 
				{ 
				if (xmlhttp.readyState == 4 && xmlhttp.status == 200) 
					{
					Nactiveext = null;
					Nactiveext = xmlhttp.responseText;
					alert(xmlhttp.responseText);
					}
				}
			delete xmlhttp;
			}
		}



// ################################################################################
// Send MonitorConf/StopMonitorConf command for recording of conferences
	function conf_send_recording(taskconfrectype,taskconfspan,taskconfrec,taskconffile) 
		{
		var xmlhttp=false;
		/*@cc_on @*/
		/*@if (@_jscript_version >= 5)
		// JScript gives us Conditional compilation, we can cope with old IE versions.
		// and security blocked creation of the objects.
		 try {
		  xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
		 } catch (e) {
		  try {
		   xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
		  } catch (E) {
		   xmlhttp = false;
		  }
		 }
		@end @*/
		if (!xmlhttp && typeof XMLHttpRequest!='undefined')
			{
			xmlhttp = new XMLHttpRequest();
			}
		if (xmlhttp) 
			{ 
			if (taskconfrectype == 'MonitorConf')
				{
				recLIST = recLIST + "|" + taskconfrec;
				filename = filedate + "_" + user_abb;
				var channelrec = "Local/" + taskconfrec + "@" + ext_context;
				var conf_rec_start_html = "<a href=\"#\" onclick=\"conf_send_recording('StopMonitorConf','" + taskconfspan + "','" + taskconfrec + "','" + filename + "');return false;\">Stop Record</a>";
				document.getElementById(taskconfspan).innerHTML = conf_rec_start_html;

			}
			if (taskconfrectype == 'StopMonitorConf')
				{
				var regy = new RegExp("\\|"+taskconfrec,"ig");
				recLIST = recLIST.replace(regy, '');
				
				filename = taskconffile;
				var channelrec = "Local/" + taskconfrec + "@" + ext_context;
				var conf_rec_start_html = "<a href=\"#\" onclick=\"conf_send_recording('MonitorConf','" + taskconfspan + "','" + taskconfrec + "','');return false;\">Record</a>";
				document.getElementById(taskconfspan).innerHTML = conf_rec_start_html;
				}
			confmonitor_query = "server_ip=" + server_ip + "&session_name=" + session_name + "&user=" + user + "&pass=" + pass + "&ACTION=" + taskconfrectype + "&format=text&channel=" + channelrec + "&filename=" + filename + "&exten=" + recording_exten + "&ext_context=" + ext_context + "&ext_priority=1";
			xmlhttp.open('POST', 'manager_send.php'); 
			xmlhttp.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
			xmlhttp.send(confmonitor_query); 
			xmlhttp.onreadystatechange = function() 
				{ 
				if (xmlhttp.readyState == 4 && xmlhttp.status == 200) 
					{
					Nactiveext = null;
					Nactiveext = xmlhttp.responseText;
					alert(xmlhttp.responseText);
					}
				}
			delete xmlhttp;
			}
		}



// ################################################################################
// Check to see if user's extension is on a call right now, if it is, format and print it
// Also grab the current server time and the status of the favorite extens
	function check_for_live_calls()
		{
		var xmlhttp=false;
		/*@cc_on @*/
		/*@if (@_jscript_version >= 5)
		// JScript gives us Conditional compilation, we can cope with old IE versions.
		// and security blocked creation of the objects.
		 try {
		  xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
		 } catch (e) {
		  try {
		   xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
		  } catch (E) {
		   xmlhttp = false;
		  }
		 }
		@end @*/
		if (!xmlhttp && typeof XMLHttpRequest!='undefined')
			{
			xmlhttp = new XMLHttpRequest();
			}
		if (xmlhttp) 
			{ 
			checklive_query = "server_ip=" + server_ip + "&session_name=" + session_name + "&user=" + user + "&pass=" + pass + "&protocol=" + protocol + "&exten=" + extension + "&favorites_count=" + favorites_count + "&favorites_list=" + favorites_listX;
			xmlhttp.open('POST', 'live_exten_check.php'); 
			xmlhttp.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
			xmlhttp.send(checklive_query); 
			xmlhttp.onreadystatechange = function() 
				{ 
				if (xmlhttp.readyState == 4 && xmlhttp.status == 200) 
					{
					var check_live = null;
					check_live = xmlhttp.responseText;
				//	alert(xmlhttp.responseText);
				//	document.getElementById("busycallsdebug").innerHTML = checklive_query + "<BR>" + xmlhttp.responseText;
				//	pause();

					var check_live_line=check_live.split("\n");
					var check_live_array=check_live_line[0].split("|");
					var inbound_call_array=check_live_line[1].split("|");
					var favorite_array=check_live_line[2].split(" ~");
					var UnixTime_array = check_live_array[1].split("UnixTime: ");
					 UnixTime = UnixTime_array[1];
					 UnixTime = parseInt(UnixTime);
					var UnixTimeMS = (UnixTime * 1000);
					t.setTime(UnixTimeMS);
					var pop_in_window_name = '';
					   if (inbound_call_array[0].length > 8)
						{
						  var UNIQUEidINT = inbound_call_array[0];
						  UNIQUEidINT = parseInt(UNIQUEidINT * 10000);
						pop_in_window_url = agcDIR + "inbound_popup.php?format=debug&uniqueid=" + inbound_call_array[0] + "&server_ip=" + server_ip + "&exten=" + extension + "&vmail_box=" + voicemail_id + "&ext_context=" + ext_context + "&ext_priority=1&voicemail_dump_exten=" + voicemail_dump_exten + "&session_name=" + session_name + "&user=" + user + "&pass=" + pass + "&local_web_callerID_URL_enc=" + local_web_callerID_URL_enc;
						pop_in_window_name = "inboundpop" + UNIQUEidINT;
						eval("pop_in_win" + UNIQUEidINT + " = window.open(pop_in_window_url, '" + pop_in_window_name + "', 'toolbar=0,scrollbars=0,location=0,statusbar=0,menubar=1,resizable=1,width=450,height=350                    ');");
						}
					var parked_calls = check_live_array[2];
					if ( (parked_calls < park_count) || (parked_calls > park_count) )
						{
						document.getElementById("parked_calls_count").innerHTML = parked_calls;
						park_count = parked_calls;
						}
					var live_calls = check_live_array[3];
					 live_calls = parseInt(live_calls);

					var loop_ct=0;
					if (favorites_count > 0)
						{
						while (loop_ct < favorites_count)
							{
							var favorite_line=favorite_array[loop_ct].split(": ");

							if (favorites_busy[loop_ct] != favorite_line[1])
								{
								favorites_busy[loop_ct] = favorite_line[1];
								if (favorite_line[1] == '0')
									{document.getElementById(favorite_line[0]).style.backgroundColor="#90EE90";}
								else
									{document.getElementById(favorite_line[0]).style.backgroundColor="#FFC8CB";}
								}
							loop_ct++;
							}
						}

					var loop_ct=0;
					var ARY_ct=0;
					var LCAalter=0;
					var LCAcontent_change=0;
					var LCAcontent_match=0;
					var conv_start=3;
					if (live_calls > 0)
						{
						var live_calls_HTML = "<font face=\"Arial,Helvetica\"><B>LIVE CALLS ON THIS PHONE:</B></font><BR><table width=100%><tr bgcolor=#E6E6E6><td><font class=\"log_title\">#</td><td><font class=\"log_title\">CLIENT CHANNEL</td><td><font class=\"log_title\">REMOTE CHANNEL</td><td><font class=\"log_title\">RECORD</td><td><font class=\"log_title\">HANGUP</td><td><font class=\"log_title\">XFER</td><td><font class=\"log_title\">PARK</td></tr>";
						if ( (LCAcount > live_calls)  || (LCAcount < live_calls) )
							{
							LCAe[0]=''; LCAe[1]=''; LCAe[2]=''; LCAe[3]=''; LCAe[4]=''; LCAe[5]=''; 
							LCAc[0]=''; LCAc[1]=''; LCAc[2]=''; LCAc[3]=''; LCAc[4]=''; LCAc[5]=''; 
							LCAt[0]=''; LCAt[1]=''; LCAt[2]=''; LCAt[3]=''; LCAt[4]=''; LCAt[5]=''; 
							LCAcount=0;   LCAcontent_change++;
							}
						while (loop_ct < live_calls)
							{
							loop_ct++;
							loop_s = loop_ct.toString();
							if (loop_s.match(/1$|3$|5$|7$|9$/)) 
								{var row_color = '#DDDDFF';}
							else
								{var row_color = '#CCCCFF';}
							var Recordbutton = 'Monitor';
							var conv_ct = (loop_ct + conv_start);
							var conversation_array = check_live_array[conv_ct].split(" ~");
							var channelfieldA_array = conversation_array[1].split(": ");
							var channelfieldB_array = conversation_array[2].split(": ");
							var channelfieldBtrunk_array = conversation_array[3].split(": ");
							live_calls_HTML = live_calls_HTML + "<tr bgcolor=\"" + row_color + "\"><td><font class=\"log_text\">" + loop_ct + "</td><td><font class=\"log_text\">" + channelfieldA_array[1] + "</td><td><font class=\"log_text\">" + channelfieldB_array[1] + "</td><td><font class=\"log_text\">";

							var regx = new RegExp("\\|"+channelfieldBtrunk_array[1],"ig");
							if (recLIST.match(regx)) 
								{live_calls_HTML = live_calls_HTML + "<span id=\"recordlive" + loop_ct + "\"><a href=\"#\" onclick=\"liverecording_send_recording('StopMonitor','" + channelfieldBtrunk_array[1] + "','recordlive" + loop_ct + "');return false;\">Stop Record</span>";}
							else 
								{live_calls_HTML = live_calls_HTML + "<span id=\"recordlive" + loop_ct + "\"><a href=\"#\" onclick=\"liverecording_send_recording('Monitor','" + channelfieldBtrunk_array[1] + "','recordlive" + loop_ct + "');return false;\">Record</span>";}

							live_calls_HTML = live_calls_HTML + "</td><td><font class=\"log_text\"><a href=\"#\" onclick=\"livehangup_send_hangup('" + channelfieldBtrunk_array[1] + "');return false;\">HANGUP</td><td><font class=\"log_text\"><a href=\"#\" onclick=\"showMainXfeR('MainXfeRBox','" + channelfieldBtrunk_array[1] + "','" + channelfieldA_array[1] + "');return false;\">XFER</a></td><td><font class=\"log_text\"><a href=\"#\" onclick=\"mainxfer_send_redirect('ParK','" + channelfieldBtrunk_array[1] + "');return false;\">PARK</td></tr>";

							if (LCAe[ARY_ct].length < 1) 
								{LCAe[ARY_ct] = channelfieldA_array[1];   LCAcontent_change++;  LCAalter++;}
							else
								{
								if (LCAe[ARY_ct] == channelfieldA_array[1]) {LCAcontent_match++;}
								 else {LCAcontent_change++;   LCAe[ARY_ct] = channelfieldA_array[1];}
								}
							if (LCAc[ARY_ct].length < 1) 
								{LCAc[ARY_ct] = channelfieldB_array[1];   LCAcontent_change++; LCAalter++;}
							else
								{
								if (LCAc[ARY_ct] == channelfieldB_array[1]) {LCAcontent_match++;}
								 else {LCAcontent_change++;   LCAc[ARY_ct] = channelfieldB_array[1];}
								}
							if (LCAt[ARY_ct].length < 1) 
								{LCAt[ARY_ct] = channelfieldBtrunk_array[1];   LCAcontent_change++; LCAalter++;}
							else
								{
								if (LCAt[ARY_ct] == channelfieldBtrunk_array[1]) {LCAcontent_match++;}
								 else {LCAcontent_change++;   LCAt[ARY_ct] = channelfieldBtrunk_array[1];}
								}

							if (LCAalter > 0) {LCAcount++;}
							
							ARY_ct++;
							}
//	var debug_LCA = regx+"|"+recLIST+"|"+LCAcontent_match+"|"+LCAcontent_change+"|"+LCAcount+"|"+live_calls+"|"+LCAe[0]+LCAe[1]+LCAe[2]+LCAe[3]+LCAe[4]+LCAe[5]+"|"+LCAc[0]+LCAc[1]+LCAc[2]+LCAc[3]+LCAc[4]+LCAc[5]+"|"+LCAt[0]+LCAt[1]+LCAt[2]+LCAt[3]+LCAt[4]+LCAt[5];
//							document.getElementById("busycallsdebug").innerHTML = debug_LCA + "<BR>" + UnixTime;

						live_calls_HTML = live_calls_HTML + "</table>";

						if (LCAcontent_change > 0)
							{
							document.getElementById("busycallsspan").innerHTML = live_calls_HTML;
							if( document.images ) { document.images['livecall'].src = image_livecall_ON.src;}
							}
						}
					else
						{
						LCAe[0]=''; LCAe[1]=''; LCAe[2]=''; LCAe[3]=''; LCAe[4]=''; LCAe[5]=''; 
						LCAc[0]=''; LCAc[1]=''; LCAc[2]=''; LCAc[3]=''; LCAc[4]=''; LCAc[5]=''; 
						LCAt[0]=''; LCAt[1]=''; LCAt[2]=''; LCAt[3]=''; LCAt[4]=''; LCAt[5]=''; 
						LCAcount=0;
						recLIST='';
						if (document.getElementById("busycallsspan").innerHTML.length > 2)
							{
							document.getElementById("busycallsspan").innerHTML = '';
							if( document.images ) { document.images['livecall'].src = image_livecall_OFF.src;}
							call_logs_display_refresh();
							}
						}
					}
				}
			delete xmlhttp;
			}
		}


// ################################################################################
// build the display for the favorites list at the right of the window
	function favorites_list_initialize()
		{
		


		}


// ################################################################################
// Grab the inbound and outbound call logs and display
	function call_logs_display_refresh()
		{
		var xmlhttp=false;
		/*@cc_on @*/
		/*@if (@_jscript_version >= 5)
		// JScript gives us Conditional compilation, we can cope with old IE versions.
		// and security blocked creation of the objects.
		 try {
		  xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
		 } catch (e) {
		  try {
		   xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
		  } catch (E) {
		   xmlhttp = false;
		  }
		 }
		@end @*/
		if (!xmlhttp && typeof XMLHttpRequest!='undefined')
			{
			xmlhttp = new XMLHttpRequest();
			}
		if (xmlhttp) 
			{ 
			call_logs_query = "server_ip=" + server_ip + "&session_name=" + session_name + "&user=" + user + "&pass=" + pass + "&protocol=" + protocol + "&exten=" + extension;
			xmlhttp.open('POST', 'call_log_display.php'); 
			xmlhttp.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
			xmlhttp.send(call_logs_query); 
			xmlhttp.onreadystatechange = function() 
				{ 
				if (xmlhttp.readyState == 4 && xmlhttp.status == 200) 
					{
					var all_logs = null;
					all_logs = xmlhttp.responseText;
				//	alert(xmlhttp.responseText);
					var all_logs_array=all_logs.split("\n");
					var out_log = all_logs_array[0];
					var out_log_array=out_log.split("|");
					var out_calls = out_log_array[0];
					var in_log = all_logs_array[1];
					var in_log_array=in_log.split("|");
					var in_calls = in_log_array[0];
					var loop_ct=0;
					var conv_start=0;
					if (out_calls > 0)
						{
						var out_log_HTML = "<table width=580><tr bgcolor=#E6E6E6><td><font class=\"log_title\">#</td><td><font class=\"log_title\"> CALL DATE/TIME</td><td><font class=\"log_title\">NUMBER</td><td align=right><font class=\"log_title\">LENGTH (M:SS)</td><td><font class=\"log_title\"> </td></tr>"
						while (loop_ct < out_calls)
							{
							loop_ct++;
							loop_s = loop_ct.toString();
							if (loop_s.match(/1$|3$|5$|7$|9$/)) 
								{var row_color = '#DDDDFF';}
							else
								{var row_color = '#CCCCFF';}
							var conv_ct = (loop_ct + conv_start);
							var call_array = out_log_array[conv_ct].split(" ~");
							var call_out_datetime = call_array[1];
							var call_out_number = call_array[2];
							var call_out_length = call_array[3];
							out_log_HTML = out_log_HTML + "<tr bgcolor=\"" + row_color + "\"><td><font class=\"log_text\">" + loop_ct + "</td><td><font class=\"log_text\">" + call_out_datetime + "</td><td><font class=\"log_text\">" + call_out_number + "</td><td align=right><font class=\"log_text\">" + call_out_length + "&nbsp;</td><td><font class=\"log_text\"><a href=\"#\" onclick=\"basic_originate_call('" + call_out_number + "');return false;\">DIAL</a></td></tr>";
					
							}
						out_log_HTML = out_log_HTML + "</table>";
						document.getElementById("outboundcallsspan").innerHTML = out_log_HTML;
						}
					else
						{
						document.getElementById("outboundcallsspan").innerHTML = '';
						}
					var loop_ct=0;
					var conv_start=0;
					if (in_calls > 0)
						{
						var in_log_HTML = "<table width=580><tr bgcolor=#E6E6E6><td><font class=\"log_title\">#</td><td><font class=\"log_title\"> CALL DATE/TIME</td><td><font class=\"log_title\">IN-NUMBER</td><td COLSPAN=2><font class=\"log_title\">CALLERID</td><td align=right><font class=\"log_title\">LENGTH</td><td><font class=\"log_title\"> </td></tr>"
						while (loop_ct < in_calls)
							{
							loop_ct++;
							loop_s = loop_ct.toString();
							if (loop_s.match(/1$|3$|5$|7$|9$/)) 
								{var row_color = '#DDDDFF';}
							else
								{var row_color = '#CCCCFF';}
							var conv_ct = (loop_ct + conv_start);
							var call_array = in_log_array[conv_ct].split(" ~");
							var call_in_datetime = call_array[1];
							var call_in_number = call_array[2];
							var call_in_idnum = call_array[3];
							var call_in_idname = call_array[4];
							var call_in_length = call_array[5];
							in_log_HTML = in_log_HTML + "<tr bgcolor=\"" + row_color + "\"><td><font class=\"log_text\">" + loop_ct + "</td><td><font class=\"log_text\">" + call_in_datetime + "</td><td><font class=\"log_text\">" + call_in_number + "</td><td><font class=\"log_text\">" + call_in_idnum + "</td><td><font class=\"log_text\">" + call_in_idname + "</td><td align=right><font class=\"log_text\">" + call_in_length + "&nbsp;</td><td><font class=\"log_text\"><a href=\"#\" onclick=\"basic_originate_call('" + call_in_idnum + "');return false;\">DIAL</a></td></tr>";
					
							}
						in_log_HTML = in_log_HTML + "</table>";
						document.getElementById("inboundcallsspan").innerHTML = in_log_HTML;
						}
					else
						{
						document.getElementById("inboundcallsspan").innerHTML = '';
						}
					}
				}
			delete xmlhttp;
			}
		}

// ################################################################################
// Grab the current parked calls and display
	function parked_calls_display_refresh()
		{
		var xmlhttp=false;
		/*@cc_on @*/
		/*@if (@_jscript_version >= 5)
		// JScript gives us Conditional compilation, we can cope with old IE versions.
		// and security blocked creation of the objects.
		 try {
		  xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
		 } catch (e) {
		  try {
		   xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
		  } catch (E) {
		   xmlhttp = false;
		  }
		 }
		@end @*/
		if (!xmlhttp && typeof XMLHttpRequest!='undefined')
			{
			xmlhttp = new XMLHttpRequest();
			}
		if (xmlhttp) 
			{ 
			park_calls_query = "server_ip=" + server_ip + "&session_name=" + session_name + "&user=" + user + "&pass=" + pass + "&protocol=" + protocol + "&exten=" + extension;
			xmlhttp.open('POST', 'park_calls_display.php'); 
			xmlhttp.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
			xmlhttp.send(park_calls_query); 
			xmlhttp.onreadystatechange = function() 
				{ 
				if (xmlhttp.readyState == 4 && xmlhttp.status == 200) 
					{
					var all_park = null;
					all_park = xmlhttp.responseText;
				//	alert(xmlhttp.responseText);
					var all_park_array=all_park.split("\n");
					var parked_count = all_park_array[0];
					var parked_calls_array=all_park_array[1].split("|");
					var loop_ct=0;
					var conv_start=-1;
					if (parked_count > 0)
						{
						var park_HTML = "<table width=600><tr bgcolor=#E6E6E6><td><font class=\"log_title\">#</td><td><font class=\"log_title\">CHANNEL<BR>&nbsp; CALL ID</td><td><font class=\"log_title\">PARKED BY<BR>&nbsp; PARKED TIME</td><td><font class=\"log_title\">HANGUP</td><td><font class=\"log_title\">XFER</td><td><font class=\"log_title\">PICKUP</td></tr>"
						while (loop_ct < parked_count)
							{
							loop_ct++;
							loop_s = loop_ct.toString();
							if (loop_s.match(/1$|3$|5$|7$|9$/)) 
								{var row_color = '#DDDDFF';}
							else
								{var row_color = '#CCCCFF';}
							var conv_ct = (loop_ct + conv_start);
							var park_array = parked_calls_array[conv_ct].split(" ~");

							var park_channel = park_array[0];
							var park_call_id = park_array[1];
							var parked_by = park_array[3];
							var parked_time = park_array[4];
							park_HTML = park_HTML + "<tr bgcolor=\"" + row_color + "\"><td><font class=\"log_text\">" + loop_ct + "</td><td><font class=\"log_text\">" + park_channel + "<BR>&nbsp; " + park_call_id + "</td><td><font class=\"log_text\">" + parked_by + "<BR>&nbsp; " + parked_time + "</td><td><font class=\"log_text\"><a href=\"#\" onclick=\"livehangup_send_hangup('" + park_channel + "');return false;\">HANGUP</a></td><td><font class=\"log_text\"><a href=\"#\" onclick=\"showMainXfeR('MainXfeRBox','" + park_channel + "');return false;\">XFER</a></td><td><font class=\"log_text\"><a href=\"#\" onclick=\"mainxfer_send_redirect('FROMParK','" + park_channel + "');return false;\">PICKUP</a></td></tr>";
					
							}
						park_HTML = park_HTML + "</table>";
						document.getElementById("ParkDisplayContents").innerHTML = park_HTML;
						}
					else
						{
						document.getElementById("ParkDisplayContents").innerHTML = '';
						}
					}
				}
			delete xmlhttp;
			}
		}

// ################################################################################
// Grab the conferences available and if they are registered, then display list
	function conference_list_display_refresh(taskplace)
		{
		var xmlhttp=false;
		/*@cc_on @*/
		/*@if (@_jscript_version >= 5)
		// JScript gives us Conditional compilation, we can cope with old IE versions.
		// and security blocked creation of the objects.
		 try {
		  xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
		 } catch (e) {
		  try {
		   xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
		  } catch (E) {
		   xmlhttp = false;
		  }
		 }
		@end @*/
		if (!xmlhttp && typeof XMLHttpRequest!='undefined')
			{
			xmlhttp = new XMLHttpRequest();
			}
		if (xmlhttp) 
			{
			conferences_list_query = "server_ip=" + server_ip + "&session_name=" + session_name + "&user=" + user + "&pass=" + pass + "&ADD=5&order=asc&format=text";
			xmlhttp.open('POST', 'active_list_refresh.php'); 
			xmlhttp.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
			xmlhttp.send(conferences_list_query); 
			xmlhttp.onreadystatechange = function() 
				{ 
				if (xmlhttp.readyState == 4 && xmlhttp.status == 200) 
					{
					var all_conf = null;
					all_conf = xmlhttp.responseText;
				//	alert(xmlhttp.responseText);
					var all_conf_array=all_conf.split("\n");
					var all_conf_array_rows=all_conf_array.length;
					var loop_ct=1;
					var conf_start=-2
					if (all_conf_array_rows > 0)
						{
						var conf_list_HTML = "<font class=\"log_title\">CONF #</font><BR>";
						while (all_conf_array_rows > loop_ct)
							{
							loop_ct++;
				//			loop_s = loop_ct.toString();
							var conf_ct = (loop_ct + conf_start);
							var conf_array = all_conf_array[conf_ct].split(" - ");
							var conf_exten = conf_array[0];
							var conf_reg = conf_array[1];
							var conf_reg_length = conf_reg.length;
							if (conf_reg_length > 0) 
								{var row_color = 'ON_conf';}
							else
								{var row_color = 'OFF_conf';}
							if (taskplace == "MainXfeRconfContent")
								{
								conf_list_HTML = conf_list_HTML + "<font class=\"" + row_color + "\"><a href=\"#\" onclick=\"mainxfer_send_redirect('CONF','" + conf_exten + "');return false;\">" + conf_exten + " " + conf_reg + " </a></font><BR>";
								}
							else
								{
								conf_list_HTML = conf_list_HTML + "<font class=\"" + row_color + "\"><a href=\"#\" onclick=\"conference_header_display('" + conf_exten + "','" + conf_reg + "','2');return false;\">" + conf_exten + " " + conf_reg + " </a></font><BR>";
								}
					
							}
						conf_list_HTML = conf_list_HTML + "<BR>";
						if (taskplace == "MainXfeRconfContent")
							{document.getElementById("MainXfeRconfContent").innerHTML = conf_list_HTML;}
						else
							{document.getElementById("ConfereNcesListContent").innerHTML = conf_list_HTML;}
						}
					else
						{
						document.getElementById("ConfereNcesListContent").innerHTML = '';
						}
					}
				}
			delete xmlhttp;
			}
		}

// ################################################################################
// display general conference meetme room header info and refresh detail content
	function conference_header_display(taskconf,taskreg,taskrefresh)
		{
		var head_conf = taskconf;
		var head_reg = taskreg;
		var show_reglink=0;
		var reglink='';
		if (taskreg.length<1) 
			{
			head_reg = 'NO ONE';
			show_reglink=1;
			reglink = "<a href=\"#\" onclick=\"conf_register_room('" + head_conf + "');return false;\">Register</a>";
			}
		var conf_head_HTML = "<font class=\"sh_text\">CONFERENCE " + head_conf + "</b></font><font class=\"sb_text\">&nbsp; &nbsp; Registered to: " + head_reg + " &nbsp; " + reglink + " &nbsp; &nbsp; &nbsp; &nbsp; <a href=\"#\" onclick=\"basic_originate_call('" + head_conf + "','NO','NO');return false;\">Enter Conference </a><BR><a href=\"#\" onclick=\"check_for_conf_calls('" + head_conf + "','1');return false;\">Refresh </a> &nbsp; &nbsp; <span id=\"conf_rec_link\"><a href=\"#\" onclick=\"conf_send_recording('MonitorConf','conf_rec_link','" + head_conf + "');return false;\">Record</a></span> &nbsp; &nbsp; &nbsp; &nbsp; <input TYPE=TEXT SIZE=15 NAME=conf_dtmf STYLE=\"font-family : sans-serif; font-size : 10px\"> <A HREF=\"#\" onclick=\"SendConfDTMF(" + head_conf + ");\">Send DTMF</A> &nbsp; &nbsp; &nbsp; &nbsp; <input TYPE=TEXT SIZE=15 NAME=conf_dial STYLE=\"font-family : sans-serif; font-size : 10px\"> <A HREF=\"#\" onclick=\"SendManualDial('YES'," + head_conf + ");\">Dial From Conf</A><BR></font>";
	
		document.getElementById("ConfereNceHeaderContent").innerHTML = conf_head_HTML;
		check_for_conf_calls(head_conf,taskrefresh);
		if (taskrefresh==2) 
			{
			check_for_conf_calls(head_conf,'1');
			lastconf = head_conf;
			conf_check_recheck=1;
			}
		}

// ################################################################################
// Check to see if there are any channels live in a conference room
	function check_for_conf_calls(taskconfnum,taskforce)
		{
		var xmlhttp=false;
		/*@cc_on @*/
		/*@if (@_jscript_version >= 5)
		// JScript gives us Conditional compilation, we can cope with old IE versions.
		// and security blocked creation of the objects.
		 try {
		  xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
		 } catch (e) {
		  try {
		   xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
		  } catch (E) {
		   xmlhttp = false;
		  }
		 }
		@end @*/
		if (!xmlhttp && typeof XMLHttpRequest!='undefined')
			{
			xmlhttp = new XMLHttpRequest();
			}
		if (xmlhttp) 
			{ 
			checkconf_query = "server_ip=" + server_ip + "&session_name=" + session_name + "&user=" + user + "&pass=" + pass + "&conf_exten=" + taskconfnum;
			xmlhttp.open('POST', 'conf_exten_check.php'); 
			xmlhttp.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
			xmlhttp.send(checkconf_query); 
			xmlhttp.onreadystatechange = function() 
				{ 
				if (xmlhttp.readyState == 4 && xmlhttp.status == 200) 
					{
					var check_conf = null;
					var LMAforce = taskforce;
					check_conf = xmlhttp.responseText;
				//	alert(checkconf_query);
				//	alert(xmlhttp.responseText);
					var check_conf_array=check_conf.split("|");
					var live_conf_calls = check_conf_array[0];
					var conf_chan_array = check_conf_array[1].split(" ~");
					   if (live_conf_calls > 0)
						{
						var loop_ct=0;
						var ARY_ct=0;
						var LMAalter=0;
						var LMAcontent_change=0;
						var LMAcontent_match=0;
						var conv_start=-1;
						var live_conf_HTML = "<font face=\"Arial,Helvetica\"><B>LIVE CALLS IN THIS CONFERENCE:</B></font><BR><TABLE WIDTH=500><TR BGCOLOR=#E6E6E6><TD><font class=\"log_title\">#</TD><TD><font class=\"log_title\">REMOTE CHANNEL</TD><TD><font class=\"log_title\">HANGUP</TD><TD><font class=\"log_title\">XFER</TD></TR>";
						if ( (LMAcount > live_conf_calls)  || (LMAcount < live_conf_calls) || (LMAforce > 0))
							{
							LMAe[0]=''; LMAe[1]=''; LMAe[2]=''; LMAe[3]=''; LMAe[4]=''; LMAe[5]=''; 
							LMAcount=0;   LMAcontent_change++;
							}
						while (loop_ct < live_conf_calls)
							{
							loop_ct++;
							loop_s = loop_ct.toString();
							if (loop_s.match(/1$|3$|5$|7$|9$/)) 
								{var row_color = '#DDDDFF';}
							else
								{var row_color = '#CCCCFF';}
							var conv_ct = (loop_ct + conv_start);
							var channelfieldA = conf_chan_array[conv_ct];
							live_conf_HTML = live_conf_HTML + "<tr bgcolor=\"" + row_color + "\"><td><font class=\"log_text\">" + loop_ct + "</td><td><font class=\"log_text\">" + channelfieldA + "</td><td><font class=\"log_text\"><a href=\"#\" onclick=\"livehangup_send_hangup('" + channelfieldA + "');return false;\">HANGUP</td><td><font class=\"log_text\"><a href=\"#\" onclick=\"showMainXfeR('MainXfeRBox','" + channelfieldA + "');return false;\">XFER</td></tr>";

							if (!LMAe[ARY_ct]) 
								{LMAe[ARY_ct] = channelfieldA;   LMAcontent_change++;  LMAalter++;}
							else
								{
								if (LMAe[ARY_ct].length < 1) 
									{LMAe[ARY_ct] = channelfieldA;   LMAcontent_change++;  LMAalter++;}
								else
									{
									if (LMAe[ARY_ct] == channelfieldA) {LMAcontent_match++;}
									 else {LMAcontent_change++;   LMAe[ARY_ct] = channelfieldA;}
									}
								}
							if (LMAalter > 0) {LMAcount++;}
							
							ARY_ct++;
							}
//	var debug_LMA = LMAcontent_match+"|"+LMAcontent_change+"|"+LMAcount+"|"+live_conf_calls+"|"+LMAe[0]+LMAe[1]+LMAe[2]+LMAe[3]+LMAe[4]+LMAe[5];
//							document.getElementById("confdebug").innerHTML = debug_LMA + "<BR>";

						live_conf_HTML = live_conf_HTML + "</table>";

						if (LMAcontent_change > 0)
							{
							document.getElementById("ConfereNceDetailContent").innerHTML = live_conf_HTML;
							}
						}
					else
						{
						LMAe[0]=''; LMAe[1]=''; LMAe[2]=''; LMAe[3]=''; LMAe[4]=''; LMAe[5]=''; 
						LMAcount=0;
						if (document.getElementById("ConfereNceDetailContent").innerHTML.length > 2)
							{
							document.getElementById("ConfereNceDetailContent").innerHTML = '';
							}
						}
					}
				}
			delete xmlhttp;
			}
		}

// ################################################################################
// Send Registration request for meetme conference room
	function conf_register_room(taskconfreg) 
		{
		var xmlhttp=false;
		/*@cc_on @*/
		/*@if (@_jscript_version >= 5)
		// JScript gives us Conditional compilation, we can cope with old IE versions.
		// and security blocked creation of the objects.
		 try {
		  xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
		 } catch (e) {
		  try {
		   xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
		  } catch (E) {
		   xmlhttp = false;
		  }
		 }
		@end @*/
		if (!xmlhttp && typeof XMLHttpRequest!='undefined')
			{
			xmlhttp = new XMLHttpRequest();
			}
		if (xmlhttp) 
			{ 
			reg_conf_query = "server_ip=" + server_ip + "&session_name=" + session_name + "&user=" + user + "&pass=" + pass + "&conf_exten=" + taskconfreg + "&exten=" + extension + "&ACTION=register";
			xmlhttp.open('POST', 'conf_exten_check.php'); 
			xmlhttp.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
			xmlhttp.send(reg_conf_query); 
			xmlhttp.onreadystatechange = function() 
				{ 
				if (xmlhttp.readyState == 4 && xmlhttp.status == 200) 
					{
				//	alert(xmlhttp.responseText);
					}
				}
			delete xmlhttp;
			}
		conference_list_display_refresh();
		conference_header_display(taskconfreg,extension,'1');
		}

// ################################################################################
// filter manual dialstring and pass on to originate call
	function SendManualDial(taskFromConf,taskconfexten)
		{
		if (taskFromConf == 'YES')
			{
			var manual_number = document.extensions_list.conf_dial.value;
			var manual_string = manual_number.toString();
			var dial_conf_exten = taskconfexten;
			}
		else
			{
			var manual_number = document.extensions_list.manual_dial.value;
			var manual_string = manual_number.toString();
			}
		if (manual_string.length=='11')
			{manual_string = "9" + manual_string;}
		 else
			{
			if (manual_string.length=='10')
				{manual_string = "91" + manual_string;}
			 else
				{
				if (manual_string.length=='7')
					{manual_string = "9" + manual_string;}
				}
			}
		if (taskFromConf == 'YES')
			{basic_originate_call(manual_string,'NO','YES',dial_conf_exten);}
		else
			{basic_originate_call(manual_string,'NO','NO');}
		}

// ################################################################################
// filter conf_dtmf send string and pass on to originate call
	function SendConfDTMF(taskconfdtmf)
		{
		var dtmf_number = document.extensions_list.conf_dtmf.value;
		var dtmf_string = dtmf_number.toString();
		var conf_dtmf_room = taskconfdtmf;

		var xmlhttp=false;
		/*@cc_on @*/
		/*@if (@_jscript_version >= 5)
		// JScript gives us Conditional compilation, we can cope with old IE versions.
		// and security blocked creation of the objects.
		 try {
		  xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
		 } catch (e) {
		  try {
		   xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
		  } catch (E) {
		   xmlhttp = false;
		  }
		 }
		@end @*/
		if (!xmlhttp && typeof XMLHttpRequest!='undefined')
			{
			xmlhttp = new XMLHttpRequest();
			}
		if (xmlhttp) 
			{ 
			var queryCID = dtmf_string;
			VMCoriginate_query = "server_ip=" + server_ip + "&session_name=" + session_name + "&user=" + user + "&pass=" + pass + "&ACTION=SysCIDOriginate&format=text&channel=" + dtmf_send_extension + "&queryCID=" + queryCID + "&exten=" + conf_dtmf_room + "&ext_context=" + ext_context + "&ext_priority=1";
			xmlhttp.open('POST', 'manager_send.php'); 
			xmlhttp.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
			xmlhttp.send(VMCoriginate_query); 
			xmlhttp.onreadystatechange = function() 
				{ 
				if (xmlhttp.readyState == 4 && xmlhttp.status == 200) 
					{
		//			alert(xmlhttp.responseText);
					}
				}
			delete xmlhttp;
			}
		}

// ################################################################################
// Send Originate command to manager to place a phone call
	function basic_originate_call(tasknum,taskprefix,taskreverse,taskdialvalue) 
		{
		var xmlhttp=false;
		/*@cc_on @*/
		/*@if (@_jscript_version >= 5)
		// JScript gives us Conditional compilation, we can cope with old IE versions.
		// and security blocked creation of the objects.
		 try {
		  xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
		 } catch (e) {
		  try {
		   xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
		  } catch (E) {
		   xmlhttp = false;
		  }
		 }
		@end @*/
		if (!xmlhttp && typeof XMLHttpRequest!='undefined')
			{
			xmlhttp = new XMLHttpRequest();
			}
		if (xmlhttp) 
			{ 

			if (taskprefix == 'NO') {var orig_prefix = '';}
			  else {var orig_prefix = agc_dial_prefix;}
			if (taskreverse == 'YES')
				{
				if (taskdialvalue.length > 0)
					{var dialnum = dialplan_number;}
				else
					{var dialnum = taskdialvalue;}
				var originatevalue = "Local/" + tasknum + "@" + ext_context;
				}
			  else 
				{
				var dialnum = tasknum;
				var originatevalue = protocol + "/" + extension;
				}
			var queryCID = "DOagcW" + epoch_sec + user_abb;

			VMCoriginate_query = "server_ip=" + server_ip + "&session_name=" + session_name + "&user=" + user + "&pass=" + pass + "&ACTION=Originate&format=text&channel=" + originatevalue + "&queryCID=" + queryCID + "&exten=" + orig_prefix + "" + dialnum + "&ext_context=" + ext_context + "&ext_priority=1&outbound_cid=" + outbound_cid;
			xmlhttp.open('POST', 'manager_send.php'); 
			xmlhttp.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
			xmlhttp.send(VMCoriginate_query); 
			xmlhttp.onreadystatechange = function() 
				{ 
				if (xmlhttp.readyState == 4 && xmlhttp.status == 200) 
					{
			//		alert(xmlhttp.responseText);
					}
				}
			delete xmlhttp;
			}
		}

// ################################################################################
// Send Originate command to manager to direct user to voicemail box
	function SendCheckVoiceMail() 
		{
		var xmlhttp=false;
		/*@cc_on @*/
		/*@if (@_jscript_version >= 5)
		// JScript gives us Conditional compilation, we can cope with old IE versions.
		// and security blocked creation of the objects.
		 try {
		  xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
		 } catch (e) {
		  try {
		   xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
		  } catch (E) {
		   xmlhttp = false;
		  }
		 }
		@end @*/
		if (!xmlhttp && typeof XMLHttpRequest!='undefined')
			{
			xmlhttp = new XMLHttpRequest();
			}
		if (xmlhttp) 
			{ 
			var queryCID = voicemail_id;
			var originatevalue = protocol + "/" + extension;
			VMCoriginate_query = "server_ip=" + server_ip + "&session_name=" + session_name + "&user=" + user + "&pass=" + pass + "&ACTION=SysCIDOriginate&format=text&channel=" + originatevalue + "&queryCID=" + queryCID + "&exten=" + voicemail_exten + "&ext_context=" + ext_context + "&ext_priority=1";
			xmlhttp.open('POST', 'manager_send.php'); 
			xmlhttp.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
			xmlhttp.send(VMCoriginate_query); 
			xmlhttp.onreadystatechange = function() 
				{ 
				if (xmlhttp.readyState == 4 && xmlhttp.status == 200) 
					{
		//			alert(xmlhttp.responseText);
					}
				}
			delete xmlhttp;
			}
		}

// ################################################################################
// Query database to find out how many new and old voicemail messages there are
	function GetVoiceMailCounts() 
		{
		var xmlhttp=false;
		/*@cc_on @*/
		/*@if (@_jscript_version >= 5)
		// JScript gives us Conditional compilation, we can cope with old IE versions.
		// and security blocked creation of the objects.
		 try {
		  xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
		 } catch (e) {
		  try {
		   xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
		  } catch (E) {
		   xmlhttp = false;
		  }
		 }
		@end @*/
		if (!xmlhttp && typeof XMLHttpRequest!='undefined')
			{
			xmlhttp = new XMLHttpRequest();
			}
		if (xmlhttp) 
			{ 
			VMCount_query = "server_ip=" + server_ip + "&session_name=" + session_name + "&user=" + user + "&pass=" + pass + "&format=text&vmail_box=" + voicemail_id;
			xmlhttp.open('POST', 'voicemail_check.php'); 
			xmlhttp.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
			xmlhttp.send(VMCount_query); 
			xmlhttp.onreadystatechange = function() 
				{ 
				if (xmlhttp.readyState == 4 && xmlhttp.status == 200) 
					{
		//			alert(xmlhttp.responseText);
					var vmail_counts = null;
					vmail_counts = xmlhttp.responseText;
					var vmail_counts_array=vmail_counts.split("|");
					var new_messages_count = parseInt(vmail_counts_array[0]);
					var old_messages_count = parseInt(vmail_counts_array[1]);
					if (old_messages != old_messages_count)
						{
						document.getElementById("old_vmail_span").innerHTML = old_messages_count;
						old_messages = old_messages_count;
						}
					if (new_messages != new_messages_count)
						{
						document.getElementById("new_vmail_span").innerHTML = new_messages_count;
						new_messages = new_messages_count;
						}				

					if ( (new_messages_count != 0) && (document.images['voicemail'].src != image_voicemail_BLINK.src) )
						{
						if( document.images ) { document.images['voicemail'].src = image_voicemail_BLINK.src;}
						}
					if ( (new_messages_count == 0) && (document.images['voicemail'].src != image_voicemail_OFF.src) )
						{
						if( document.images ) { document.images['voicemail'].src = image_voicemail_OFF.src;}
						}
					new_messages_count = 0;
					old_messages_count = 0;

					}
				}
			delete xmlhttp;
			}
		}


// ################################################################################
// Log the user out of the system, if active call or active dial is occuring, don't let them.
//('BuilD','ADD','REMOVE','UP','DOWN')
	function favorites_editor(taskbuildedit,taskfavext,taskfavrow)
		{
		if (taskbuildedit == 'BuilD')
			{
			favoritesEDIT		= favorites;
			favorites_namesEDIT	= favorites_names;
			favorites_listEDIT	= favorites_list;
			favorites_countEDIT	= favorites_count;
			if (favorites_countEDIT < 1)
				{
				favorites_listEDIT = '';
				favorites_countEDIT++;
				}
			}

		if (taskbuildedit == 'ADD')
			{
			var favlistEXTEN=taskfavext.split(" - ");
			favoritesEDIT[favorites_countEDIT]			= favlistEXTEN[0];
			favorites_namesEDIT[favorites_countEDIT]	= favlistEXTEN[1];
			favorites_listEDIT = favorites_listEDIT + ",'" + favlistEXTEN[0] + "'";
			favorites_countEDIT++;
			}

		if (taskbuildedit == 'REMOVE')
			{
			var favlistEXTEN=taskfavext.split(" - ");
			var WORKfavoritesEDIT		= new Array();
			var WORKfavorites_namesEDIT = new Array();
			var WORKfavorites_listEDIT	= '';
			var WORKfavorites_countEDIT = 0;
			loop_ct = 0;

			while (loop_ct < favorites_countEDIT)
				{
				if (favoritesEDIT[loop_ct] != favlistEXTEN[0])
					{
					WORKfavoritesEDIT[WORKfavorites_countEDIT] = favoritesEDIT[loop_ct];
					WORKfavorites_namesEDIT[WORKfavorites_countEDIT] = favorites_namesEDIT[loop_ct];
					WORKfavorites_countEDIT++;
					}
				loop_ct++;
				}

			var regFAVlist = new RegExp(",'" + favlistEXTEN[0] + "'","ig");
			favorites_listEDIT = favorites_listEDIT.replace(regFAVlist,'');

			favoritesEDIT		= WORKfavoritesEDIT;
			favorites_namesEDIT	= WORKfavorites_namesEDIT;
			favorites_countEDIT	= WORKfavorites_countEDIT;
			}

		if (taskbuildedit == 'UP')
			{
			if (taskfavrow > 1)
				{
				var favlistEXTEN	= taskfavext.split(" - ");
				var taskfavrowprev	= (taskfavrow - 1);
				var holdingEXTEN	= favoritesEDIT[taskfavrowprev];
				var holdingNAME		= favorites_namesEDIT[taskfavrowprev];
				
				favoritesEDIT[taskfavrowprev]		= favlistEXTEN[0];
				favorites_namesEDIT[taskfavrowprev] = favlistEXTEN[1];
				favoritesEDIT[taskfavrow]			= holdingEXTEN;
				favorites_namesEDIT[taskfavrow]		= holdingNAME;

				favorites_listEDIT = '';
				loop_ct = 0;
				while (loop_ct < favorites_countEDIT)
					{
					if (loop_ct > 0)
						{
						favorites_listEDIT = favorites_listEDIT + ",'" + favoritesEDIT[loop_ct] + "'";
						}
					loop_ct++;
					}
				}
			}

		if (taskbuildedit == 'DOWN')
			{
			var FLOORfavorites_countEDIT = (favorites_countEDIT - 1);
			if ( (taskfavrow > 0) && (taskfavrow < FLOORfavorites_countEDIT) )
				{
				var favlistEXTEN	= taskfavext.split(" - ");
				var taskfavrownext	= taskfavrow;
				    taskfavrownext++;
				var holdingEXTEN	= favoritesEDIT[taskfavrownext];
				var holdingNAME		= favorites_namesEDIT[taskfavrownext];

				favoritesEDIT[taskfavrownext]		= favlistEXTEN[0];
				favorites_namesEDIT[taskfavrownext] = favlistEXTEN[1];
				favoritesEDIT[taskfavrow]			= holdingEXTEN;
				favorites_namesEDIT[taskfavrow]		= holdingNAME;

				favorites_listEDIT = '';
				loop_ct = 0;
				while (loop_ct < favorites_countEDIT)
					{
					if (loop_ct > 0)
						{
						favorites_listEDIT = favorites_listEDIT + ",'" + favoritesEDIT[loop_ct] + "'";
						}
					loop_ct++;
					}
				}
			}

	//		alert(favorites_listEDIT);

		showDiv('FavoriteSEdiT');

		var VD_favlist_ct_half = parseInt(favlistCT / 2);
		if (VD_favlist_ct_half < 30) {VD_favlist_ct_half = 30;}
		var favlist_sec_col = 0;
		var favedit_HTML = "<center><table cellpadding=5 cellspacing=5 width=750><tr><td colspan=2 align=center bgcolor=\"#DDDD99\"><B>AVAILABLE EXTENSIONS</B></td><td align=center bgcolor=\"#CCCC99\"><B> FAVORITES</B></td></tr><tr><td bgcolor=\"#DDDD99\" height=380 width=200 valign=top><font class=\"ss_text\"><span id=FavSelectA>";
		loop_ct = 0;
		while (loop_ct < favlistCT)
			{
			var favlistEXTEN=favlist[loop_ct].split(" - ");
			var regFAVlist = new RegExp("'" + favlistEXTEN[0] + "'","ig");
			if (favorites_listEDIT.match(regFAVlist))
				{
				// do not print extension, it's on the favorites list
				}
			else
				{
				if (favlist[loop_ct].length > 4)
					{
					favedit_HTML = favedit_HTML + "<font class=\"ss_text\"><b><a href=\"#" + favlist[loop_ct] + "\" onclick=\"favorites_editor('ADD','" + favlist[loop_ct] + "');return false;\">" + favlist[loop_ct] + "</a></b></font><BR>";
					}
				}
			if (loop_ct == VD_favlist_ct_half) 
				{
				favedit_HTML = favedit_HTML + "</span></font></td><td bgcolor=\"#DDDD99\" height=380 width=200 valign=top><font class=\"ss_text\"><span id=FavSelectB>";
				favlist_sec_col = 1;
				}
			loop_ct++;
			}
		if (favlist_sec_col == 0)
			{
			favedit_HTML = favedit_HTML + " &nbsp; </span></font></td><td bgcolor=\"#DDDD99\" height=380 width=200 valign=top><font class=\"ss_text\"><span id=FavSelectB> &nbsp; ";
			}
		favedit_HTML = favedit_HTML + "</span></font></td><td bgcolor=\"#CCCC99\" height=380 width=200 valign=top><font class=\"ss_text\"><span id=FavSelected>";

		loop_ct = 0;
		while (loop_ct < favorites_countEDIT)
			{
			if (loop_ct > 0)
				{
				favedit_HTML = favedit_HTML + "<font class=\"sb_text\"><b><a href=\"#" + favoritesEDIT[loop_ct] + " - " + favorites_namesEDIT[loop_ct] + " - " + loop_ct + "\" onclick=\"favorites_editor('UP','" + favoritesEDIT[loop_ct] + " - " + favorites_namesEDIT[loop_ct] + "','" + loop_ct + "');return false;\"><img src=\"./images/up.gif\" BORDER=0></a>  <a href=\"#" + favoritesEDIT[loop_ct] + " - " + favorites_namesEDIT[loop_ct] + " - " + loop_ct + "\" onclick=\"favorites_editor('DOWN','" + favoritesEDIT[loop_ct] + " - " + favorites_namesEDIT[loop_ct] + "','" + loop_ct + "');return false;\"><img src=\"./images/down.gif\" BORDER=0></a>  " + favoritesEDIT[loop_ct] + " - " + favorites_namesEDIT[loop_ct] + " &nbsp; <a href=\"#" + favoritesEDIT[loop_ct] + " - " + favorites_namesEDIT[loop_ct] + "\" onclick=\"favorites_editor('REMOVE','" + favoritesEDIT[loop_ct] + " - " + favorites_namesEDIT[loop_ct] + "','" + loop_ct + "');return false;\"><img src=\"./images/remove.gif\" BORDER=0></a></b></font><BR>";
				}
			loop_ct++;
			}

		favedit_HTML = favedit_HTML + "</span></font></td></tr></table>";
		
		document.getElementById("FavoriteSEditContent").innerHTML = favedit_HTML;
		}


// ################################################################################
// Submit changes to the favorites list and logout
	function SubmiT_FavoritE_ChangEs()
		{
		var xmlhttp=false;
		/*@cc_on @*/
		/*@if (@_jscript_version >= 5)
		// JScript gives us Conditional compilation, we can cope with old IE versions.
		// and security blocked creation of the objects.
		 try {
		  xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
		 } catch (e) {
		  try {
		   xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
		  } catch (E) {
		   xmlhttp = false;
		  }
		 }
		@end @*/
		if (!xmlhttp && typeof XMLHttpRequest!='undefined')
			{
			xmlhttp = new XMLHttpRequest();
			}
		if (xmlhttp) 
			{ 
			FCquery = "server_ip=" + server_ip + "&session_name=" + session_name + "&user=" + user + "&pass=" + pass + "&ACTION=UpdatEFavoritEs&format=text&exten=" + extension + "&favorites_list=" + favorites_listEDIT;
			xmlhttp.open('POST', 'vdc_db_query.php'); 
			xmlhttp.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
			xmlhttp.send(FCquery); 
			xmlhttp.onreadystatechange = function() 
				{ 
				if (xmlhttp.readyState == 4 && xmlhttp.status == 200) 
					{
		//			alert(xmlhttp.responseText);
					}
				}
			delete xmlhttp;
			}
		hideDiv('FavoriteSEdiT');
		LogouT();
		}


// ################################################################################
// Log the user out of the system, if active call or active dial is occuring, don't let them.
	function LogouT()
		{

			hideDiv('MainPanel');
			showDiv('LogouTBox');

		document.getElementById("LogouTBoxLink").innerHTML = "<a href=\"" + agcPAGE + "?relogin=YES&session_epoch=" + epoch_sec + "&session_name=" + session_name + "&user=" + user + "&pass=" + pass + "&user=" + user + "&phone_login=" + phone_login + "&phone_pass=" + phone_pass + "\">CLICK HERE TO LOG IN AGAIN</a>\n";

		logout_stop_timeouts = 1;

		}


// ################################################################################
// GLOBAL FUNCTIONS
	function start_all_refresh()
		{
		if (logout_stop_timeouts == 0)
			{
			if (active_display==1)
				{
				check_s = check_n.toString();
				if (document.getElementById("activeext").style.visibility == 'visible')
					{
					if ( (check_s.match(/00$/)) || (check_n<2) ) {getactiveext();}
					}
				if (document.getElementById("busytrunk").style.visibility == 'visible')
					{getbusytrunk();}
				if (document.getElementById("busyext").style.visibility == 'visible')
					{getbusyext();}
				if ( (document.getElementById("ConfereNcesPanel").style.visibility == 'visible') && (lastconf.length > 0) )
					{check_for_conf_calls(lastconf);}
				}
			if (check_n<2) 
				{
				document.getElementById("refresh_rate").innerHTML = refresh_interval + " ms";
				hideTrunkHangup();
				hideLocalHangup();
				hideDiv('TrunkHangupLink');
				hideDiv('LocalHangupLink');
				hideDiv('ActiveLinesPanel');
				hideDiv('ConfereNcesPanel');
				hideDiv('activeext');
				hideDiv('busytrunk');
				hideDiv('busyext');
				hideDiv('TrunkHangupLink');
				hideDiv('LocalHangupLink');
				hideDiv('MainXfeRBox');
				hideDiv('LocalDialBox');
				hideDiv('ParkDisplayBox');
				hideDiv('LogouTBox');
				hideDiv('FavoriteSEdiT');
				call_logs_display_refresh();
				favorites_list_initialize();
				}
			else
				{
				check_for_live_calls();
				check_s = check_n.toString();
				if ( (check_s.match(/0$/)) || (check_n == 2)) {GetVoiceMailCounts();}
				if ( (park_refresh > 0) && (check_s.match(/0$|5$/)) ) {parked_calls_display_refresh();}
				}
			setTimeout("all_refresh()", refresh_interval);
			}
		}
	function all_refresh()
		{
		epoch_sec++;
		check_n++;
		var year= t.getYear()
		var month= t.getMonth()
			month++;
		var daym= t.getDate()
		var hours = t.getHours();
		var min = t.getMinutes();
		var sec = t.getSeconds();
		if (year < 1000) {year+=1900}
		if (month< 10) {month= "0" + month}
		if (daym< 10) {daym= "0" + daym}
		if (hours < 10) {hours = "0" + hours;}
		if (min < 10) {min = "0" + min;}
		if (sec < 10) {sec = "0" + sec;}
		filedate = year + month + daym + "-" + hours + min + sec;
		document.getElementById("status").innerHTML = year + "-" + month + "-" + daym + " " + hours + ":" + min + ":" + sec  + display_message;
		start_all_refresh();
		}
	function pause()	// Pauses the refreshing of the lists
		{active_display=2;  display_message="  - ACTIVE DISPLAY PAUSED -";}
	function start()	// resumes the refreshing of the lists
		{active_display=1;  display_message='';}
	function faster()	// lowers by 1000 milliseconds the time until the next refresh
		{
		 if (refresh_interval>1001)
			{refresh_interval=(refresh_interval - 1000);}
 		document.getElementById("refresh_rate").innerHTML = refresh_interval + " ms";
		}
	function slower()	// raises by 1000 milliseconds the time until the next refresh
		{
		refresh_interval=(refresh_interval + 1000);
 		document.getElementById("refresh_rate").innerHTML = refresh_interval + " ms";
		}

	// activeext-specific functions
	function activeext_force_refresh()	// forces immediate refresh of list content
		{getactiveext();}
	function activeext_order_asc()	// changes order of activeext list to ascending
		{
		activeext_order="asc";   getactiveext();
		desc_order_HTML ='<a href="#" onclick="activeext_order_desc();return false;">ORDER</a>';
		document.getElementById("activeext_order").innerHTML = desc_order_HTML;
		}
	function activeext_order_desc()	// changes order of activeext list to descending
		{
		activeext_order="desc";   getactiveext();
		asc_order_HTML ='<a href="#" onclick="activeext_order_asc();return false;">ORDER</a>';
		document.getElementById("activeext_order").innerHTML = asc_order_HTML;
		}

	// busytrunk-specific functions
	function busytrunk_force_refresh()	// forces immediate refresh of list content
		{getbusytrunk();}
	function busytrunk_order_asc()	// changes order of busytrunk list to ascending
		{
		busytrunk_order="asc";   getbusytrunk();
		desc_order_HTML ='<a href="#" onclick="busytrunk_order_desc();return false;">ORDER</a>';
		document.getElementById("busytrunk_order").innerHTML = desc_order_HTML;
		}
	function busytrunk_order_desc()	// changes order of busytrunk list to descending
		{
		busytrunk_order="desc";   getbusytrunk();
		asc_order_HTML ='<a href="#" onclick="busytrunk_order_asc();return false;">ORDER</a>';
		document.getElementById("busytrunk_order").innerHTML = asc_order_HTML;
		}
	function busytrunkhangup_force_refresh()	// forces immediate refresh of list content
		{busytrunkhangup();}

	// busyext-specific functions
	function busyext_force_refresh()	// forces immediate refresh of list content
		{getbusyext();}
	function busyext_order_asc()	// changes order of busyext list to ascending
		{
		busyext_order="asc";   getbusyext();
		desc_order_HTML ='<a href="#" onclick="busyext_order_desc();return false;">ORDER</a>';
		document.getElementById("busyext_order").innerHTML = desc_order_HTML;
		}
	function busyext_order_desc()	// changes order of busyext list to descending
		{
		busyext_order="desc";   getbusyext();
		asc_order_HTML ='<a href="#" onclick="busyext_order_asc();return false;">ORDER</a>';
		document.getElementById("busyext_order").innerHTML = asc_order_HTML;
		}
	function busylocalhangup_force_refresh()	// forces immediate refresh of list content
		{busylocalhangup();}


	// functions to hide and show different DIVs
	function showDiv(divvar) 
		{
		if (document.getElementById(divvar))
			{
			divref = document.getElementById(divvar).style;
			divref.visibility = 'visible';
			}
		}
	function hideDiv(divvar)
		{
		if (document.getElementById(divvar))
			{
			divref = document.getElementById(divvar).style;
			divref.visibility = 'hidden';
			}
		}

	function showTrunkHangup(divvar) 
		{
		document.getElementById("TrunkHangupBox").style.visibility = 'visible';
		pause();
		hideDiv('activeext');
		hideDiv('busytrunk');
		hideDiv('busyext');
		hideDiv('TrunkHangupLink');
		hideDiv('LocalHangupLink');
		if (admin_hangup_enabled == 0) {hideDiv('TrunkHangup_HUlink');}
		if (admin_hijack_enabled == 0) {hideDiv('TrunkHangup_HJlink');}
		if (admin_monitor_enabled == 0) {hideDiv('TrunkHangup_ZMlink');}
		busytrunkhangup();
		}
	function hideTrunkHangup(divvar) 
		{
		document.getElementById("TrunkHangupBox").style.visibility = 'hidden';
		start();
		showDiv('activeext');
		showDiv('busytrunk');
		showDiv('busyext');
		showDiv('TrunkHangupLink');
		showDiv('LocalHangupLink');
		}
	function showLocalHangup(divvar) 
		{
		document.getElementById("LocalHangupBox").style.visibility = 'visible';
		pause();
		hideDiv('activeext');
		hideDiv('busytrunk');
		hideDiv('busyext');
		hideDiv('TrunkHangupLink');
		hideDiv('LocalHangupLink');
		if (admin_hangup_enabled == 0) {hideDiv('LocalHangup_HUlink');}
		if (admin_hijack_enabled == 0) {hideDiv('LocalHangup_HJlink');}
		if (admin_monitor_enabled == 0) {hideDiv('LocalHangup_ZMlink');}
		busylocalhangup();
		}
	function hideLocalHangup(divvar) 
		{
		document.getElementById("LocalHangupBox").style.visibility = 'hidden';
		start();
		showDiv('activeext');
		showDiv('busytrunk');
		showDiv('busyext');
		showDiv('TrunkHangupLink');
		showDiv('LocalHangupLink');
		}

	function showMainXfeR(divvar,taskxferchan,taskxferchanmain) 
		{
		document.getElementById("MainXfeRBox").style.visibility = 'visible';
		getactiveext("MainXfeRBox");
		conference_list_display_refresh("MainXfeRconfContent");
		var XfeR_channel = taskxferchan;
		document.extensions_list.H_XfeR_channel.value = XfeR_channel;
		document.extensions_list.M_XfeR_channel.value = taskxferchanmain;
		document.getElementById("MainXfeRChanneL").innerHTML = XfeR_channel;
		}
	function showLocalDial(divvar,taskxferchan) 
		{
		document.getElementById("LocalDialBox").style.visibility = 'visible';
		getactiveext("LocalDialBox");
		document.getElementById("LocalDialChanneL").innerHTML = protocol + "/" + extension;
		}
	function showParkDisplay(divvar,taskxferchan) 
		{
		document.getElementById("ParkDisplayBox").style.visibility = 'visible';
		parked_calls_display_refresh();
		park_refresh=1;
		}
	function hideMainXfeR(divvar) 
		{
		document.getElementById("MainXfeRBox").style.visibility = 'hidden';
		var XfeR_channel = '';
		document.extensions_list.H_XfeR_channel.value = '';
		document.extensions_list.M_XfeR_channel.value = '';
		document.getElementById("MainXfeRChanneL").innerHTML = '';
		}
	function hideLocalDial(divvar) 
		{
		document.getElementById("LocalDialBox").style.visibility = 'hidden';
		var XfeR_channel = '';
		document.extensions_list.H_XfeR_channel.value = '';
		document.getElementById("LocalDialChanneL").innerHTML = '';
		}
	function hideParkDisplay(divvar) 
		{
		document.getElementById("ParkDisplayBox").style.visibility = 'hidden';
		park_refresh=0;
		}


	function MainPanelToFront()
		{
		showDiv('MainPanel');
		call_logs_display_refresh();
		hideDiv('ActiveLinesPanel');
		hideDiv('ConfereNcesPanel');
		hideDiv('activeext');
		hideDiv('busytrunk');
		hideDiv('busyext');
		hideDiv('TrunkHangupLink');
		hideDiv('LocalHangupLink');
		}
	function ActiveLinesPanelToFront()
		{
		showDiv('ActiveLinesPanel');
		showDiv('activeext');
		showDiv('busytrunk');
		showDiv('busyext');
		showDiv('TrunkHangupLink');
		showDiv('LocalHangupLink');
		hideDiv('MainPanel');
		hideDiv('ConfereNcesPanel');
		getactiveext();
		}
	function ConfereNcesPanelToFront()
		{
		showDiv('ConfereNcesPanel');
		hideDiv('MainPanel');
		hideDiv('ActiveLinesPanel');
		hideDiv('activeext');
		hideDiv('busytrunk');
		hideDiv('busyext');
		hideDiv('TrunkHangupLink');
		hideDiv('LocalHangupLink');
		conference_list_display_refresh();
		}

	</script>

    <STYLE type="text/css">
    </STYLE>


<style type="text/css">
<!--
	div.scroll_log {height: 135px; width: 600px; overflow: scroll;}
	div.scroll_park {height: 400px; width: 620px; overflow: scroll;}
	div.scroll_list {height: 400px; width: 140px; overflow: scroll;}
   .body_text {font-size: 13px;  font-family: sans-serif;}
   .log_text {font-size: 11px;  font-family: monospace;}
   .log_title {font-size: 12px;  font-family: monospace; font-weight: bold;}
   .sh_text {font-size: 14px;  font-family: sans-serif; font-weight: bold;}
   .sb_text {font-size: 12px;  font-family: sans-serif; color: black ;}
   .ss_text {font-size: 10px;  font-family: sans-serif; color: black ;}
   .ON_conf {font-size: 11px;  font-family: monospace; color: black ; background: #FFFF99}
   .OFF_conf {font-size: 11px;  font-family: monospace; color: black ; background: #FFCC77}

-->
</style>
<?php
echo "</head>\n";


?>
<BODY onload="all_refresh();">
<FORM name=extensions_list>
<span style="position:absolute;left:0px;top:0px;z-index:1;" id="Header">
<TABLE BORDER=0 CELLPADDING=0 CELLSPACING=0 BGCOLOR=white WIDTH=640 MARGINWIDTH=0 MARGINHEIGHT=0 LEFTMARGIN=0 TOPMARGIN=0 VALIGN=TOP ALIGN=LEFT>
<TR VALIGN=TOP ALIGN=LEFT><TD COLSPAN=5 VALIGN=TOP ALIGN=LEFT>
<INPUT TYPE=HIDDEN NAME=extension>
<font class="body_text">
<?php	echo "Welcome $LOGfullname, you are logged into this phone: $fullname - $protocol/$extension on $server_ip &nbsp; <a href=\"#\" onclick=\"LogouT();return false;\">LOGOUT</a><BR>\n"; ?>
</TD></TR>
<TR VALIGN=TOP ALIGN=LEFT>
<TD><A HREF="#" onclick="MainPanelToFront();"><IMG SRC="./images/agc_tab_main.gif" ALT="Main Panel" WIDTH=83 HEIGHT=30 BORDER=0></A></TD>
<TD><A HREF="#" onclick="ActiveLinesPanelToFront();"><IMG SRC="./images/agc_tab_active_lines.gif" ALT="Active Lines Panel" WIDTH=139 HEIGHT=30 BORDER=0></A></TD>
<TD><A HREF="#" onclick="ConfereNcesPanelToFront();"><IMG SRC="./images/agc_tab_conferences.gif" ALT="Conferences Panel" WIDTH=139 HEIGHT=30 BORDER=0></A></TD>
<TD><A HREF="#" onclick="SendCheckVoiceMail();"><IMG SRC="./images/agc_check_voicemail_ON.gif" NAME=voicemail ALT="Check Voicemail" WIDTH=170 HEIGHT=30 BORDER=0></A></TD>
<TD><IMG SRC="./images/agc_live_call_OFF.gif" NAME=livecall ALT="Live Call" WIDTH=109 HEIGHT=30 BORDER=0></TD>
</TR></TABLE>
</SPAN>

<span style="position:absolute;left:0px;top:0px;z-index:26;" id="LogouTBox">
    <table border=1 bgcolor="#FFFFFF" width=750 height=500><TR><TD align=center><BR><span id="LogouTBoxLink">Logout</span></TD></TR></TABLE>
</span>

<span style="position:absolute;left:0px;top:12px;z-index:29;" id="TrunkHangupBox">
    <table border=1 bgcolor="#CDE0C2" width=600 height=500><TR><TD> TRUNK HANGUP <BR><BR>
	<span id="TrunkHangupContent"> Active Trunks Menu </span><BR>
	<span id="TrunkHangup_HUlink"><a href="#" onclick="busyhangup_send_hangup('Trunk');return false;">Hangup Trunk</a> &nbsp; | &nbsp; </span>
	<span id="TrunkHangup_HJlink"><a href="#" onclick="busyhangup_send_redirect('Trunk','HIJACK');return false;">Hijack Trunk</a> &nbsp; | &nbsp; </span>
	<span id="TrunkHangup_ZMlink"><a href="#" onclick="busyhangup_send_redirect('Trunk','LISTEN');return false;">Listen Trunk</a> &nbsp; | &nbsp; </span>
	<a href="#" onclick="busytrunkhangup_force_refresh();return false;">Refresh</a> &nbsp; | &nbsp; 
	<a href="#" onclick="hideTrunkHangup('TrunkHangupBox');">Back to Main Window</a>
	</TD></TR></TABLE>
</span>

<span style="position:absolute;left:0px;top:12px;z-index:28;" id="LocalHangupBox">
    <table border=1 bgcolor="#CDE0C2" width=600 height=500><TR><TD> LOCAL HANGUP <BR><BR>
	<span id="LocalHangupContent"> Active Local Menu </span><BR>
	<span id="LocalHangup_HUlink"><a href="#" onclick="busyhangup_send_hangup('Local');return false;">Hangup Local</a> &nbsp; | &nbsp; </span>
	<span id="LocalHangup_HJlink"><a href="#" onclick="busyhangup_send_redirect('Local');return false;">Hijack Local</a> &nbsp; | &nbsp; </span>
	<span id="LocalHangup_ZMlink"><a href="#" onclick="busyhangup_send_redirect('Local','LISTEN');return false;">Listen Local</a> &nbsp; | &nbsp; </span>
	<a href="#" onclick="busylocalhangup_force_refresh();return false;">Refresh</a> &nbsp; | &nbsp; 
	<a href="#" onclick="hideLocalHangup('LocalHangupBox');">Back to Main Window</a>
	</TD></TR></TABLE>
</span>

<span style="position:absolute;left:80px;top:12px;z-index:42;" id="MainXfeRBox">
	<input type=hidden name=H_XfeR_channel>
	<input type=hidden name=M_XfeR_channel>
    <table border=0 bgcolor="#FFFFCC" width=600 height=500 cellpadding=3><TR><TD COLSPAN=3 ALIGN=CENTER><b> LIVE CALL TRANSFER</b> <BR>Channel to be transferred: <span id="MainXfeRChanneL">Channel</span><BR></tr>
	<tr><td>Extensions:<BR><span id="MainXfeRContent"> Extensions Menu </span></td>
	<td>
	<BR>
	<a href="#" onclick="mainxfer_send_redirect('XfeR');return false;">Send to selected extension</a> <BR><BR>
	<a href="#" onclick="mainxfer_send_redirect('VMAIL');return false;">Send to selected vmail box</a> <BR><BR>
	<a href="#" onclick="mainxfer_send_redirect('ENTRY');return false;">Send to this number</a>:<BR><input type=text name=extension_xfer_entry size=20 maxlength=50> <BR><BR>
	<a href="#" onclick="getactiveext('MainXfeRBox');return false;">Refresh</a> <BR><BR><BR>
	<a href="#" onclick="hideMainXfeR('MainXfeRBox');">Back to Main Window</a> <BR><BR>
	</TD>
	<TD>Conferences:<BR><font size=1>(click on a number below to send to a conference)<BR><input type=checkbox name=MainXfeRconfXTRA size=1 value="1"> Send my channel too<div class="scroll_list" id="MainXfeRconfContent"> Conferences Menu </div></td></TR></TABLE>
</span>

<span style="position:absolute;left:80px;top:12px;z-index:43;" id="LocalDialBox">
    <table border=0 bgcolor="#FFFFCC" width=600 height=500 cellpadding=3><TR><TD COLSPAN=3 ALIGN=CENTER><b> LOCAL Extensions Dial</b> <BR>Phone calling from: <span id="LocalDialChanneL">Channel</span><BR></tr>
	<tr><td>Extensions:<BR><span id="LocalDialContent"> Extensions Menu </span></td>
	<td>
	<BR>
	<a href="#" onclick="mainxfer_send_originate('DiaL','','');return false;">Call selected extension</a> <BR><BR>
	<a href="#" onclick="mainxfer_send_originate('VMAIL');return false;">Call selected vmail box</a> <BR><BR>
	<a href="#" onclick="getactiveext('LocalDialBox');return false;">Refresh</a> <BR><BR><BR>
	<a href="#" onclick="hideLocalDial('LocalDialBox');">Back to Main Window</a> <BR><BR>
	</TD>
	<TD></td></TR></TABLE>
</span>

<span style="position:absolute;left:40px;top:12px;z-index:41;" id="ParkDisplayBox">
    <table border=0 bgcolor="#FFFFCC" width=640 height=500 cellpadding=3><TR><TD COLSPAN=3 ALIGN=CENTER><b> PARKED CALLS:</b> <div class="scroll_park" id="ParkDisplayContents"></div>
	<a href="#" onclick="hideParkDisplay('ParkDisplayBox');">Back to Main Window</a> <BR><BR>
	</td></TR></TABLE>
</span>

<span  style="position:absolute;left:0px;top:46px;z-index:10;" id="MainPanel">
<TABLE border=0 BGCOLOR="#CCC2E0" width=640>
<TR><TD> &nbsp; 
</TD></TR>
<tr><td><span id="busycallsspan"></span></td></tr>
<tr><td><span id="busycallsdebug"></span></td></tr>
<tr><td align=center><font face="Arial,Helvetica"><B>VOICEMAIL &nbsp; &nbsp; </B></font> NEW: <span id="new_vmail_span"></span> &nbsp; &nbsp; OLD: <span id="old_vmail_span"></span> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; <font face="Arial,Helvetica"><B>MANUAL DIAL &nbsp; &nbsp; &nbsp; </B></font><input TYPE=TEXT SIZE=20 NAME=manual_dial STYLE="font-family : sans-serif; font-size : 10px"> <A HREF="#" onclick="SendManualDial();">DIAL</A></td></tr>

<tr><td align=center><a href="#" onclick="showParkDisplay('ParkDisplayBox');return false;"><font face="Arial,Helvetica"><B>PARKED CALLS</B></a>:  <span id="parked_calls_count">0</span> &nbsp; &nbsp;  &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;<a href="#" onclick="showLocalDial('LocalDialBox');return false;"><font face="Arial,Helvetica"><B>LOCAL DIAL EXTENSIONS</a></td></tr>

<tr><td align=center><font face="Arial,Helvetica"><B>OUTBOUND CALLS:</B></font></td></tr>
<tr><td align=center><div class="scroll_log" id="outboundcallsspan"></div></td></tr>
<tr><td align=center><font face="Arial,Helvetica"><B>INBOUND CALLS:</B></font></td></tr>
<tr><td align=center><div class="scroll_log" id="inboundcallsspan"></div></td></tr>
<tr><td align=left><font face="Arial,Helvetica" size=1>astGUIclient web-client VERSION:<?php echo $version ?> BUILD:<?php echo $build ?></font></td></tr>
</TABLE>

<span style="position:absolute;left:640px;top:0px;z-index:33;" id="FavoriteSBox">
    <table border=0 bgcolor="#DDDDFF" width=200 height=400 cellpadding=2 ALIGN=TOP><TR><TD align=center><span id="FavoriteSContent"><font class="sh_text"> FAVORITES</font><font class="sb_text"> &nbsp; &nbsp; &nbsp; <a href="#" onclick="favorites_editor('BuilD');return false;"> edit</a></span></TD></TR>
<?php
	$h=0;
	while ($favorites_count > $h)
	{
		if (strlen($favorites[$h])>1)
		{
		echo "<TR id=\"$favorites[$h]\" bgcolor=\"#90EE90\"><TD> <A HREF=\"#\" onclick=\"mainxfer_send_originate('DiaL','','$favorites[$h]');return false;\"><font class=\"sb_text\">$favorites[$h] - $favorites_names[$h]</font></A> </TD></TR>\n";
		}
	$h++;
	}
?>
	<TR HEIGHT=100%><TD HEIGHT=100%><span id="FavoriteSContentXtrA"> &nbsp; </span></TD></TR>
	</TABLE>
</span>


<span style="position:absolute;left:5px;top:5px;z-index:34;" id="FavoriteSEdiT">
    <table border=0 bgcolor="#DDDDFF" width=800 height=450 cellpadding=2 ALIGN=TOP><TR HEIGHT=95%><TD align=center HEIGHT=95%><span id="FavoriteSEditContent"> FAVORITES</span></TD></TR>
	<TR><TD ALIGN=CENTER><BR> &nbsp; </TD></TR>
	<TR VALIGN=BOTTOM><TD VALIGN=BOTTOM ALIGN=CENTER BGCOLOR="#FFFFCC"><a href="#" onclick="SubmiT_FavoritE_ChangEs();return false;">SUBMIT FAVORITES CHANGES - requires logout</a></TD></TR>
	<TR VALIGN=BOTTOM><TD VALIGN=BOTTOM ALIGN=CENTER BGCOLOR="#FFFFCC"><a href="#" onclick="hideDiv('FavoriteSEdiT');return false;">BACK TO MAIN WINDOW - ignore changes made</a></TD></TR>
	</TABLE>
</span>

</span>


<span style="position:absolute;left:0px;top:46px;z-index:20;" id="ActiveLinesPanel">
<table border=0 BGCOLOR="#CDE0C2" width=640>
<tr><td colspan=3>
<a href="#" onclick="pause();return false;">STOP</a> | <a href="#" onclick="start();return false;">START</a> &nbsp; &nbsp; Refresh rate: <span id="refresh_rate">1000 ms</span> <a href="#" onclick="faster();return false;">Faster</a> | <a href="#" onclick="slower();return false;">Slower</a></p>
	<div id="status"><em>Initializing..</em></div>
</td></tr>
<tr><td>Active Extensions <BR> 
<a href="#" onclick="activeext_force_refresh();return false;">REFRESH</a> | 
<span id="activeext_order"><a href="#" onclick="activeext_order_desc();return false;">ORDER</a> | </span>
</td>

<td>Outside Lines <BR>
<a href="#" onclick="busytrunk_force_refresh();return false;">REFRESH</a> | 
<span id="busytrunk_order"><a href="#" onclick="busytrunk_order_desc();return false;">ORDER</a> | </span>
</td>

<td>Local Extensions <BR>
<a href="#" onclick="busyext_force_refresh();return false;">REFRESH</a> | 
<span id="busyext_order"><a href="#" onclick="busyext_order_desc();return false;">ORDER</a> | </span>
</td></tr>

<tr><td VALIGN=TOP>
	<span id="activeext"><em>Data Goes Here</em></span><BR><BR>
</td><td VALIGN=TOP>
	<span id="busytrunk"><em>Data Goes Here</em></span><BR><BR><span id="TrunkHangupLink"><a href="#" onclick="showTrunkHangup('TrunkHangupBox');return false;">Trunk Action</a></span>
</td><td VALIGN=TOP>
	<span id="busyext"><em>Data Goes Here</em></span><BR><BR><span id="LocalHangupLink"><a href="#" onclick="showLocalHangup('LocalHangupBox');return false;">Local Action</a></span>
</td></tr>

</table>
</span>


<span style="position:absolute;left:0px;top:46px;z-index:30;color:#F3D6B9;width:640;" id="ConfereNcesPanel">
<TABLE border=0 BGCOLOR="#F3D6B9" width=640 height=500>
<TR><TD></TD></TR></TABLE>

<span style="position:absolute;left:0px;top:0px;z-index:31;color:black;" id="ConfereNcesListSpan">
<span id="ConfereNcesListContent">Conferences List </span>
</span>

<span style="position:absolute;left:140px;top:0px;z-index:32;color:black;width:500;" id="ConfereNceHeaderSpan">
<span id="ConfereNceHeaderContent"> </span><BR>
<span style="width:540;height:400;" id="ConfereNceDetailContent">Click on a conference room number on the left for info on that conference </span>
</span>



</TD></TR></TABLE>

</FORM>



</body>
</html>

<?php
	
exit; 



?>





