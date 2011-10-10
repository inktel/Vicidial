<?php
# timeclock.php - VICIDIAL system user timeclock
# 
# Copyright (C) 2009  Matt Florell <vicidial@gmail.com>    LICENSE: AGPLv2
#
# CHANGELOG
# 80523-0134 - First Build 
# 80524-0225 - Changed event_date to DATETIME, added timestamp field and tcid_link field
# 80525-2351 - Added an audit log that is not to be editable
# 80602-0641 - Fixed status update bug
# 90508-0727 - Changed to PHP long tags
#

$version = '2.2.0-5';
$build = '90508-0727';

$StarTtimE = date("U");
$NOW_TIME = date("Y-m-d H:i:s");
	$last_action_date = $NOW_TIME;

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
$agcDIR = eregi_replace('timeclock.php','',$agcPAGE);


if (isset($_GET["DB"]))							{$DB=$_GET["DB"];}
        elseif (isset($_POST["DB"]))			{$DB=$_POST["DB"];}
if (isset($_GET["phone_login"]))				{$phone_login=$_GET["phone_login"];}
        elseif (isset($_POST["phone_login"]))	{$phone_login=$_POST["phone_login"];}
if (isset($_GET["phone_pass"]))					{$phone_pass=$_GET["phone_pass"];}
        elseif (isset($_POST["phone_pass"]))	{$phone_pass=$_POST["phone_pass"];}
if (isset($_GET["VD_login"]))					{$VD_login=$_GET["VD_login"];}
        elseif (isset($_POST["VD_login"]))		{$VD_login=$_POST["VD_login"];}
if (isset($_GET["VD_pass"]))					{$VD_pass=$_GET["VD_pass"];}
        elseif (isset($_POST["VD_pass"]))		{$VD_pass=$_POST["VD_pass"];}
if (isset($_GET["VD_campaign"]))				{$VD_campaign=$_GET["VD_campaign"];}
        elseif (isset($_POST["VD_campaign"]))	{$VD_campaign=$_POST["VD_campaign"];}
if (isset($_GET["stage"]))						{$stage=$_GET["stage"];}
        elseif (isset($_POST["stage"]))			{$stage=$_POST["stage"];}
if (isset($_GET["commit"]))						{$commit=$_GET["commit"];}
        elseif (isset($_POST["commit"]))		{$commit=$_POST["commit"];}
if (isset($_GET["referrer"]))					{$referrer=$_GET["referrer"];}
        elseif (isset($_POST["referrer"]))		{$referrer=$_POST["referrer"];}
if (isset($_GET["user"]))						{$user=$_GET["user"];}
        elseif (isset($_POST["user"]))			{$user=$_POST["user"];}
if (isset($_GET["pass"]))						{$pass=$_GET["pass"];}
        elseif (isset($_POST["pass"]))			{$pass=$_POST["pass"];}

if (!isset($phone_login)) 
	{
	if (isset($_GET["pl"]))					{$phone_login=$_GET["pl"];}
			elseif (isset($_POST["pl"]))	{$phone_login=$_POST["pl"];}
	}
if (!isset($phone_pass))
	{
	if (isset($_GET["pp"]))					{$phone_pass=$_GET["pp"];}
			elseif (isset($_POST["pp"]))	{$phone_pass=$_POST["pp"];}
	}

### security strip all non-alphanumeric characters out of the variables ###
	$DB=ereg_replace("[^0-9a-z]","",$DB);
	$phone_login=ereg_replace("[^\,0-9a-zA-Z]","",$phone_login);
	$phone_pass=ereg_replace("[^0-9a-zA-Z]","",$phone_pass);
	$VD_login=ereg_replace("[^0-9a-zA-Z]","",$VD_login);
	$VD_pass=ereg_replace("[^0-9a-zA-Z]","",$VD_pass);
	$VD_campaign=ereg_replace("[^0-9a-zA-Z_]","",$VD_campaign);
	$user=ereg_replace("[^0-9a-zA-Z]","",$user);
	$pass=ereg_replace("[^0-9a-zA-Z]","",$pass);
	$stage=ereg_replace("[^0-9a-zA-Z]","",$stage);
	$commit=ereg_replace("[^0-9a-zA-Z]","",$commit);
	$referrer=ereg_replace("[^0-9a-zA-Z]","",$referrer);

require("dbconnect.php");

#############################################
##### START SYSTEM_SETTINGS LOOKUP #####
$stmt = "SELECT use_non_latin,admin_home_url FROM system_settings;";
$rslt=mysql_query($stmt, $link);
if ($DB) {echo "$stmt\n";}
$qm_conf_ct = mysql_num_rows($rslt);
$i=0;
while ($i < $qm_conf_ct)
	{
	$row=mysql_fetch_row($rslt);
	$non_latin =	$row[0];
	$welcomeURL =	$row[1];
	$i++;
	}
##### END SETTINGS LOOKUP #####
###########################################


header ("Content-type: text/html; charset=utf-8");
header ("Cache-Control: no-cache, must-revalidate");  // HTTP/1.1
header ("Pragma: no-cache");                          // HTTP/1.0

if ( ($stage == 'login') or ($stage == 'logout') )
	{
	### see if user/pass exist for this user in vicidial_users table
	$stmt="SELECT count(*) from vicidial_users where user='$user' and pass='$pass' and user_level > 0;";
	if ($DB) {echo "|$stmt|\n";}
	$rslt=mysql_query($stmt, $link);
	$row=mysql_fetch_row($rslt);
	$valid_user=$row[0];
	print "<!-- vicidial_users active count for $user:   |$valid_user| -->\n";

	if ($valid_user < 1)
		{
		### NOT A VALID USER/PASS
		$VDdisplayMESSAGE = "您所輸入的帳號和密碼並未在系統中被啟動<BR>請重試:";

		echo"<HTML><HEAD>\n";
		echo"<TITLE>Agent Timeclock</TITLE>\n";
		echo"<META HTTP-EQUIV=\"Content-Type\" CONTENT=\"text/html; charset=utf-8\">\n";
		echo"</HEAD>\n";
		echo "<BODY BGCOLOR=WHITE MARGINHEIGHT=0 MARGINWIDTH=0>\n";
		echo "<FORM  NAME=vicidial_form ID=vicidial_form ACTION=\"$agcPAGE\" METHOD=POST>\n";
		echo "<INPUT TYPE=HIDDEN NAME=referrer VALUE=\"$referrer\">\n";
		echo "<INPUT TYPE=HIDDEN NAME=stage VALUE=\"login\">\n";
		echo "<INPUT TYPE=HIDDEN NAME=DB VALUE=\"$DB\">\n";
		echo "<INPUT TYPE=HIDDEN NAME=phone_login VALUE=\"$phone_login\">\n";
		echo "<INPUT TYPE=HIDDEN NAME=phone_pass VALUE=\"$phone_pass\">\n";
		echo "<INPUT TYPE=HIDDEN NAME=VD_login VALUE=\"$VD_login\">\n";
		echo "<INPUT TYPE=HIDDEN NAME=VD_pass VALUE=\"$VD_pass\">\n";
		echo "<CENTER><BR><B>$VDdisplayMESSAGE</B><BR><BR>";
		echo "<TABLE WIDTH=460 CELLPADDING=0 CELLSPACING=0 BGCOLOR=\"#CCFFCC\"><TR BGCOLOR=WHITE>";
		echo "<TD ALIGN=LEFT VALIGN=BOTTOM><IMG SRC=\"../agc/images/vtc_tab_vicidial.gif\" 邊界=0></TD>";
		echo "<TD ALIGN=CENTER VALIGN=MIDDLE><B> Timeclock </B></TD>";
		echo "</TR>\n";
		echo "<TR><TD ALIGN=LEFT COLSPAN=2><font size=1> &nbsp; </TD></TR>\n";
		echo "<TR><TD ALIGN=RIGHT>使用者登入:  </TD>";
		echo "<TD ALIGN=LEFT><INPUT TYPE=TEXT NAME=user SIZE=10 maxlength=20 VALUE=\"$VD_login\"></TD></TR>\n";
		echo "<TR><TD ALIGN=RIGHT>使用者密碼:  </TD>";
		echo "<TD ALIGN=LEFT><INPUT TYPE=PASSWORD NAME=pass SIZE=10 maxlength=20 VALUE=''></TD></TR>\n";
		echo "<TR><TD ALIGN=CENTER COLSPAN=2><INPUT TYPE=Submit NAME=確認送出 VALUE=確認送出> &nbsp; </TD></TR>\n";
		echo "<TR><TD ALIGN=LEFT COLSPAN=2><font size=1><BR>版本: $version &nbsp; &nbsp; &nbsp; 版次: $build</TD></TR>\n";
		echo "</TABLE>\n";
		echo "</FORM>\n\n";
		echo "</body>\n\n";
		echo "</html>\n\n";

		exit;
		}
	else
		{
		### VALID USER/PASS, CONTINUE

		### get name and group for this user
		$stmt="SELECT full_name,user_group from vicidial_users where user='$user' and pass='$pass';";
		if ($DB) {echo "|$stmt|\n";}
		$rslt=mysql_query($stmt, $link);
		$row=mysql_fetch_row($rslt);
		$full_name =	$row[0];
		$user_group =	$row[1];
		print "<!-- vicidial_users name and group for $user:   |$full_name|$user_group| -->\n";

		### get vicidial_timeclock_status record count for this user
		$stmt="SELECT count(*) from vicidial_timeclock_status where user='$user';";
		if ($DB) {echo "|$stmt|\n";}
		$rslt=mysql_query($stmt, $link);
		$row=mysql_fetch_row($rslt);
		$vts_count =	$row[0];

		$last_action_sec=99;

		if ($vts_count > 0)
			{
			### vicidial_timeclock_status record found, grab status and date of last activity
			$stmt="SELECT status,event_epoch from vicidial_timeclock_status where user='$user';";
			if ($DB) {echo "|$stmt|\n";}
			$rslt=mysql_query($stmt, $link);
			$row=mysql_fetch_row($rslt);
			$status =		$row[0];
			$event_epoch =	$row[1];
			$last_action_date = date("Y-m-d H:i:s", $event_epoch);
			$last_action_sec = ($StarTtimE - $event_epoch);
			if ($last_action_sec > 0)
				{
				$totTIME_H = ($last_action_sec / 3600);
				$totTIME_H_int = round($totTIME_H, 2);
				$totTIME_H_int = intval("$totTIME_H");
				$totTIME_M = ($totTIME_H - $totTIME_H_int);
				$totTIME_M = ($totTIME_M * 60);
				$totTIME_M_int = round($totTIME_M, 2);
				$totTIME_M_int = intval("$totTIME_M");
				$totTIME_S = ($totTIME_M - $totTIME_M_int);
				$totTIME_S = ($totTIME_S * 60);
				$totTIME_S = round($totTIME_S, 0);
				if (strlen($totTIME_H_int) < 1) {$totTIME_H_int = "0";}
				if ($totTIME_M_int < 10) {$totTIME_M_int = "0$totTIME_M_int";}
				if ($totTIME_S < 10) {$totTIME_S = "0$totTIME_S";}
				$totTIME_HMS = "$totTIME_H_int:$totTIME_M_int:$totTIME_S";
				}
			else 
				{
				$totTIME_HMS='0:00:00';
				}

			print "<!-- vicidial_timeclock_status previous status for $user:   |$status|$event_epoch|$last_action_sec| -->\n";
			}
		else
			{
			### No vicidial_timeclock_status record found, insert one
			$stmt="INSERT INTO vicidial_timeclock_status set status='START', user='$user', user_group='$user_group', event_epoch='$StarTtimE', ip_address='$ip';";
			if ($DB) {echo "$stmt\n";}
			$rslt=mysql_query($stmt, $link);
				$status='START';
				$totTIME_HMS='0:00:00';
			$affected_rows = mysql_affected_rows($link);
			print "<!-- 新 vicidial_timeclock_status record inserted for $user:   |$affected_rows| -->\n";
			}
		if ( ($last_action_sec < 30) and ($status != 'START') )
			{
			### You cannot log in or out within 30 秒 of your last login/logout
			$VDdisplayMESSAGE = "您不能在前次登出或登入後30秒內再次登出或登入";

			echo"<HTML><HEAD>\n";
			echo"<TITLE>Agent Timeclock</TITLE>\n";
			echo"<META HTTP-EQUIV=\"Content-Type\" CONTENT=\"text/html; charset=utf-8\">\n";
			echo"</HEAD>\n";
			echo "<BODY BGCOLOR=WHITE MARGINHEIGHT=0 MARGINWIDTH=0>\n";
			echo "<FORM  NAME=vicidial_form ID=vicidial_form ACTION=\"$agcPAGE\" METHOD=POST>\n";
			echo "<INPUT TYPE=HIDDEN NAME=stage VALUE=\"login\">\n";
			echo "<INPUT TYPE=HIDDEN NAME=referrer VALUE=\"$referrer\">\n";
			echo "<INPUT TYPE=HIDDEN NAME=DB VALUE=\"$DB\">\n";
			echo "<INPUT TYPE=HIDDEN NAME=phone_login VALUE=\"$phone_login\">\n";
			echo "<INPUT TYPE=HIDDEN NAME=phone_pass VALUE=\"$phone_pass\">\n";
			echo "<INPUT TYPE=HIDDEN NAME=VD_login VALUE=\"$VD_login\">\n";
			echo "<INPUT TYPE=HIDDEN NAME=VD_pass VALUE=\"$VD_pass\">\n";
			echo "<CENTER><BR><B>$VDdisplayMESSAGE</B><BR><BR>";
			echo "<TABLE WIDTH=460 CELLPADDING=0 CELLSPACING=0 BGCOLOR=\"#CCFFCC\"><TR BGCOLOR=WHITE>";
			echo "<TD ALIGN=LEFT VALIGN=BOTTOM><IMG SRC=\"../agc/images/vtc_tab_vicidial.gif\" 邊界=0></TD>";
			echo "<TD ALIGN=CENTER VALIGN=MIDDLE><B> Timeclock </B></TD>";
			echo "</TR>\n";
			echo "<TR><TD ALIGN=LEFT COLSPAN=2><font size=1> &nbsp; </TD></TR>\n";
			echo "<TR><TD ALIGN=RIGHT>使用者登入:  </TD>";
			echo "<TD ALIGN=LEFT><INPUT TYPE=TEXT NAME=user SIZE=10 maxlength=20 VALUE=\"$VD_login\"></TD></TR>\n";
			echo "<TR><TD ALIGN=RIGHT>使用者密碼:  </TD>";
			echo "<TD ALIGN=LEFT><INPUT TYPE=PASSWORD NAME=pass SIZE=10 maxlength=20 VALUE=''></TD></TR>\n";
			echo "<TR><TD ALIGN=CENTER COLSPAN=2><INPUT TYPE=Submit NAME=確認送出 VALUE=確認送出> &nbsp; </TD></TR>\n";
			echo "<TR><TD ALIGN=LEFT COLSPAN=2><font size=1><BR>版本: $version &nbsp; &nbsp; &nbsp; 版次: $build</TD></TR>\n";
			echo "</TABLE>\n";
			echo "</FORM>\n\n";
			echo "</body>\n\n";
			echo "</html>\n\n";

			exit;
			}

		if ($commit == 'YES')
			{
			if ( ( ($status=='AUTOLOGOUT') or ($status=='START') or ($status=='LOGOUT') ) and ($stage=='login') )
				{
				$VDdisplayMESSAGE = "You have now logged-in";
				$LOGtimeMESSAGE = "You logged in at $NOW_TIME";

				### Add a record to the timeclock log
				$stmt="INSERT INTO vicidial_timeclock_log set event='LOGIN', user='$user', user_group='$user_group', event_epoch='$StarTtimE', ip_address='$ip', event_date='$NOW_TIME';";
				if ($DB) {echo "$stmt\n";}
				$rslt=mysql_query($stmt, $link);
				$affected_rows = mysql_affected_rows($link);
				$timeclock_id = mysql_insert_id($link);
				print "<!-- 新 vicidial_timeclock_log record inserted for $user:   |$affected_rows|$timeclock_id| -->\n";

				### Update the user's timeclock status record
				$stmt="UPDATE vicidial_timeclock_status set status='LOGIN', user_group='$user_group', event_epoch='$StarTtimE', ip_address='$ip' where user='$user';";
				if ($DB) {echo "$stmt\n";}
				$rslt=mysql_query($stmt, $link);
				$affected_rows = mysql_affected_rows($link);
				print "<!-- vicidial_timeclock_status record updated for $user:   |$affected_rows| -->\n";

				### Add a record to the timeclock audit log
				$stmt="INSERT INTO vicidial_timeclock_audit_log set timeclock_id='$timeclock_id', event='LOGIN', user='$user', user_group='$user_group', event_epoch='$StarTtimE', ip_address='$ip', event_date='$NOW_TIME';";
				if ($DB) {echo "$stmt\n";}
				$rslt=mysql_query($stmt, $link);
				$affected_rows = mysql_affected_rows($link);
				print "<!-- 新 vicidial_timeclock_audit_log record inserted for $user:   |$affected_rows| -->\n";
				}

			if ( ($status=='LOGIN') and ($stage=='logout') )
				{
				$VDdisplayMESSAGE = "您目前已登出";
				$LOGtimeMESSAGE = "您登出在$NOW_TIME<BR>您登入的時間(多久時間): $totTIME_HMS";

				### Add a record to the timeclock log
				$stmt="INSERT INTO vicidial_timeclock_log set event='LOGOUT', user='$user', user_group='$user_group', event_epoch='$StarTtimE', ip_address='$ip', login_sec='$last_action_sec', event_date='$NOW_TIME';";
				if ($DB) {echo "$stmt\n";}
				$rslt=mysql_query($stmt, $link);
				$affected_rows = mysql_affected_rows($link);
				$timeclock_id = mysql_insert_id($link);
				print "<!-- 新 vicidial_timeclock_log record inserted for $user:   |$affected_rows|$timeclock_id| -->\n";

				### Update last login record in the timeclock log
				$stmt="UPDATE vicidial_timeclock_log set login_sec='$last_action_sec',tcid_link='$timeclock_id' where event='LOGIN' and user='$user' order by timeclock_id desc limit 1;";
				if ($DB) {echo "$stmt\n";}
				$rslt=mysql_query($stmt, $link);
				$affected_rows = mysql_affected_rows($link);
				print "<!-- vicidial_timeclock_log record updated for $user:   |$affected_rows| -->\n";

				### Update the user's timeclock status record
				$stmt="UPDATE vicidial_timeclock_status set status='LOGOUT', user_group='$user_group', event_epoch='$StarTtimE', ip_address='$ip' where user='$user';";
				if ($DB) {echo "$stmt\n";}
				$rslt=mysql_query($stmt, $link);
				$affected_rows = mysql_affected_rows($link);
				print "<!-- vicidial_timeclock_status record updated for $user:   |$affected_rows| -->\n";

				### Add a record to the timeclock audit log
				$stmt="INSERT INTO vicidial_timeclock_audit_log set timeclock_id='$timeclock_id', event='LOGOUT', user='$user', user_group='$user_group', event_epoch='$StarTtimE', ip_address='$ip', login_sec='$last_action_sec', event_date='$NOW_TIME';";
				if ($DB) {echo "$stmt\n";}
				$rslt=mysql_query($stmt, $link);
				$affected_rows = mysql_affected_rows($link);
				print "<!-- 新 vicidial_timeclock_audit_log record inserted for $user:   |$affected_rows| -->\n";

				### Update last login record in the timeclock audit log
				$stmt="UPDATE vicidial_timeclock_audit_log set login_sec='$last_action_sec',tcid_link='$timeclock_id' where event='LOGIN' and user='$user' order by timeclock_id desc limit 1;";
				if ($DB) {echo "$stmt\n";}
				$rslt=mysql_query($stmt, $link);
				$affected_rows = mysql_affected_rows($link);
				print "<!-- vicidial_timeclock_audit_log record updated for $user:   |$affected_rows| -->\n";
				}

			if ( ( ( ($status=='AUTOLOGOUT') or ($status=='START') or ($status=='LOGOUT') ) and ($stage=='logout') ) or ( ($status=='LOGIN') and ($stage=='login') ) )
				{echo "ERROR: timeclock log entry already made: $status|$stage";  exit;}

			if ($referrer=='agent') 
				{$BACKlink = "<A HREF=\"./vicidial.php?pl=$phone_login&pp=$phone_pass&VD_login=$user\"><font color=\"#003333\">回到值機員登入畫面</font></A>";}
			if ($referrer=='admin') 
				{$BACKlink = "<A HREF=\"../vicidial/admin.php\"><font color=\"#003333\">回到系統管理</font></A>";}
			if ($referrer=='welcome') 
				{$BACKlink = "<A HREF=\"$welcomeURL\"><font color=\"#003333\">回到歡迎畫面</font></A>";}

			echo"<HTML><HEAD>\n";
			echo"<TITLE>Agent Timeclock</TITLE>\n";
			echo"<META HTTP-EQUIV=\"Content-Type\" CONTENT=\"text/html; charset=utf-8\">\n";
			echo"</HEAD>\n";
			echo "<BODY BGCOLOR=WHITE MARGINHEIGHT=0 MARGINWIDTH=0>\n";
			echo "<CENTER><BR><B>$VDdisplayMESSAGE</B><BR><BR>";
			echo "<TABLE WIDTH=460 CELLPADDING=0 CELLSPACING=0 BGCOLOR=\"#CCFFCC\"><TR BGCOLOR=WHITE>";
			echo "<TD ALIGN=LEFT VALIGN=BOTTOM><IMG SRC=\"../agc/images/vtc_tab_vicidial.gif\" 邊界=0></TD>";
			echo "<TD ALIGN=CENTER VALIGN=MIDDLE><B> Timeclock </B></TD>";
			echo "</TR>\n";
			echo "<TR><TD ALIGN=LEFT COLSPAN=2><font size=1> &nbsp; </TD></TR>\n";
			echo "<TR><TD ALIGN=CENTER COLSPAN=2><font size=3><B> $LOGtimeMESSAGE<BR>&nbsp; </B></TD></TR>\n";
			echo "<TR><TD ALIGN=CENTER COLSPAN=2><B> $BACKlink <BR>&nbsp; </B></TD></TR>\n";
			echo "<TR><TD ALIGN=LEFT COLSPAN=2><font size=1><BR>版本: $version &nbsp; &nbsp; &nbsp; 版次: $build</TD></TR>\n";
			echo "</TABLE>\n";
			echo "</body>\n\n";
			echo "</html>\n\n";

			exit;
			}




		if ( ($status=='AUTOLOGOUT') or ($status=='START') or ($status=='LOGOUT') )
			{
			$VDdisplayMESSAGE = "自從您上次登入已經有(多久時間): $totTIME_HMS";
			$log_action = 'login';
			$button_name = 'LOGIN';
			$LOGtimeMESSAGE = "您上次登出在: $last_action_date<BR><BR>點選下方登入進行登入";
			}
		if ($status=='LOGIN')
			{
			$VDdisplayMESSAGE = "您已經登入的時間(多久時間): $totTIME_HMS";
			$log_action = 'logout';
			$button_name = 'LOGOUT';
			$LOGtimeMESSAGE = "您登入在: $last_action_date<BR>您已經登入的時間(多久時間): $totTIME_HMS<BR><BR>點選下方登出進行登出";
			}

		echo"<HTML><HEAD>\n";
		echo"<TITLE>Agent Timeclock</TITLE>\n";
		echo"<META HTTP-EQUIV=\"Content-Type\" CONTENT=\"text/html; charset=utf-8\">\n";
		echo"</HEAD>\n";
		echo "<BODY BGCOLOR=WHITE MARGINHEIGHT=0 MARGINWIDTH=0>\n";
		echo "<FORM  NAME=vicidial_form ID=vicidial_form ACTION=\"$agcPAGE\" METHOD=POST>\n";
		echo "<INPUT TYPE=HIDDEN NAME=stage VALUE=\"$log_action\">\n";
		echo "<INPUT TYPE=HIDDEN NAME=commit VALUE=\"YES\">\n";
		echo "<INPUT TYPE=HIDDEN NAME=referrer VALUE=\"$referrer\">\n";
		echo "<INPUT TYPE=HIDDEN NAME=DB VALUE=\"$DB\">\n";
		echo "<INPUT TYPE=HIDDEN NAME=phone_login VALUE=\"$phone_login\">\n";
		echo "<INPUT TYPE=HIDDEN NAME=phone_pass VALUE=\"$phone_pass\">\n";
		echo "<INPUT TYPE=HIDDEN NAME=VD_login VALUE=\"$VD_login\">\n";
		echo "<INPUT TYPE=HIDDEN NAME=VD_pass VALUE=\"$VD_pass\">\n";
		echo "<INPUT TYPE=HIDDEN NAME=user VALUE=\"$user\">\n";
		echo "<INPUT TYPE=HIDDEN NAME=pass VALUE=\"$pass\">\n";
		echo "<CENTER><BR><B>$VDdisplayMESSAGE</B><BR><BR>";
		echo "<TABLE WIDTH=460 CELLPADDING=0 CELLSPACING=0 BGCOLOR=\"#CCFFCC\"><TR BGCOLOR=WHITE>";
		echo "<TD ALIGN=LEFT VALIGN=BOTTOM><IMG SRC=\"../agc/images/vtc_tab_vicidial.gif\" 邊界=0></TD>";
		echo "<TD ALIGN=CENTER VALIGN=MIDDLE><B> Timeclock </B></TD>";
		echo "</TR>\n";
		echo "<TR><TD ALIGN=LEFT COLSPAN=2><font size=1> &nbsp; </TD></TR>\n";
		echo "<TR><TD ALIGN=CENTER COLSPAN=2><font size=3><B> $LOGtimeMESSAGE<BR>&nbsp; </B></TD></TR>\n";
		echo "<TR><TD ALIGN=CENTER COLSPAN=2><INPUT TYPE=Submit NAME=$button_name VALUE=$button_name> &nbsp; </TD></TR>\n";
		echo "<TR><TD ALIGN=LEFT COLSPAN=2><font size=1><BR>版本: $version &nbsp; &nbsp; &nbsp; 版次: $build</TD></TR>\n";
		echo "</TABLE>\n";
		echo "</FORM>\n\n";
		echo "</body>\n\n";
		echo "</html>\n\n";

		exit;
		}



	}

else
	{
	echo"<HTML><HEAD>\n";
	echo"<TITLE>Agent Timeclock</TITLE>\n";
	echo"<META HTTP-EQUIV=\"Content-Type\" CONTENT=\"text/html; charset=utf-8\">\n";
	echo"</HEAD>\n";
	echo "<BODY BGCOLOR=WHITE MARGINHEIGHT=0 MARGINWIDTH=0>\n";
	echo "<FORM  NAME=vicidial_form ID=vicidial_form ACTION=\"$agcPAGE\" METHOD=POST>\n";
	echo "<INPUT TYPE=HIDDEN NAME=stage VALUE=\"login\">\n";
	echo "<INPUT TYPE=HIDDEN NAME=referrer VALUE=\"$referrer\">\n";
	echo "<INPUT TYPE=HIDDEN NAME=DB VALUE=\"$DB\">\n";
	echo "<INPUT TYPE=HIDDEN NAME=phone_login VALUE=\"$phone_login\">\n";
	echo "<INPUT TYPE=HIDDEN NAME=phone_pass VALUE=\"$phone_pass\">\n";
	echo "<INPUT TYPE=HIDDEN NAME=VD_login VALUE=\"$VD_login\">\n";
	echo "<INPUT TYPE=HIDDEN NAME=VD_pass VALUE=\"$VD_pass\">\n";
	echo "<CENTER><BR><B>$VDdisplayMESSAGE</B><BR><BR>";
	echo "<TABLE WIDTH=460 CELLPADDING=0 CELLSPACING=0 BGCOLOR=\"#CCFFCC\"><TR BGCOLOR=WHITE>";
	echo "<TD ALIGN=LEFT VALIGN=BOTTOM><IMG SRC=\"../agc/images/vtc_tab_vicidial.gif\" 邊界=0></TD>";
	echo "<TD ALIGN=CENTER VALIGN=MIDDLE><B> Timeclock </B></TD>";
	echo "</TR>\n";
	echo "<TR><TD ALIGN=LEFT COLSPAN=2><font size=1> &nbsp; </TD></TR>\n";
	echo "<TR><TD ALIGN=RIGHT>使用者登入:  </TD>";
	echo "<TD ALIGN=LEFT><INPUT TYPE=TEXT NAME=user SIZE=10 maxlength=20 VALUE=\"$VD_login\"></TD></TR>\n";
	echo "<TR><TD ALIGN=RIGHT>使用者密碼:  </TD>";
	echo "<TD ALIGN=LEFT><INPUT TYPE=PASSWORD NAME=pass SIZE=10 maxlength=20 VALUE=''></TD></TR>\n";
	echo "<TR><TD ALIGN=CENTER COLSPAN=2><INPUT TYPE=Submit NAME=確認送出 VALUE=確認送出> &nbsp; </TD></TR>\n";
	echo "<TR><TD ALIGN=LEFT COLSPAN=2><font size=1><BR>版本: $version &nbsp; &nbsp; &nbsp; 版次: $build</TD></TR>\n";
	echo "</TABLE>\n";
	echo "</FORM>\n\n";
	echo "</body>\n\n";
	echo "</html>\n\n";
	}

exit;

?>
