<?php
# timeclock_edit.php
# 
# Copyright (C) 2009  Matt Florell <vicidial@gmail.com>    LICENSE: AGPLv2
#
# CHANGES
#
# 80624-1342 - First build
# 80701-1323 - functional beta version done
# 90310-2109 - Added admin header
# 90508-0644 - Changed to PHP long tags
#

header ("Content-type: text/html; charset=utf-8");

require("dbconnect.php");

$PHP_AUTH_USER=$_SERVER['PHP_AUTH_USER'];
$PHP_AUTH_PW=$_SERVER['PHP_AUTH_PW'];
$PHP_SELF=$_SERVER['PHP_SELF'];
if (isset($_GET["oldLOGINepoch"]))				{$oldLOGINepoch=$_GET["oldLOGINepoch"];}
	elseif (isset($_POST["oldLOGINepoch"]))		{$oldLOGINepoch=$_POST["oldLOGINepoch"];}
if (isset($_GET["oldLOGOUTepoch"]))				{$oldLOGOUTepoch=$_GET["oldLOGOUTepoch"];}
	elseif (isset($_POST["oldLOGOUTepoch"]))	{$oldLOGOUTepoch=$_POST["oldLOGOUTepoch"];}
if (isset($_GET["oldLOGINdate"]))				{$oldLOGINdate=$_GET["oldLOGINdate"];}
	elseif (isset($_POST["oldLOGINdate"]))		{$oldLOGINdate=$_POST["oldLOGINdate"];}
if (isset($_GET["oldLOGOUTdate"]))				{$oldLOGOUTdate=$_GET["oldLOGOUTdate"];}
	elseif (isset($_POST["oldLOGOUTdate"]))		{$oldLOGOUTdate=$_POST["oldLOGOUTdate"];}
if (isset($_GET["LOGINepoch"]))					{$LOGINepoch=$_GET["LOGINepoch"];}
	elseif (isset($_POST["LOGINepoch"]))		{$LOGINepoch=$_POST["LOGINepoch"];}
if (isset($_GET["LOGOUTepoch"]))				{$LOGOUTepoch=$_GET["LOGOUTepoch"];}
	elseif (isset($_POST["LOGOUTepoch"]))		{$LOGOUTepoch=$_POST["LOGOUTepoch"];}
if (isset($_GET["notes"]))						{$notes=$_GET["notes"];}
	elseif (isset($_POST["notes"]))				{$notes=$_POST["notes"];}
if (isset($_GET["LOGINevent_id"]))				{$LOGINevent_id=$_GET["LOGINevent_id"];}
	elseif (isset($_POST["LOGINevent_id"]))		{$LOGINevent_id=$_POST["LOGINevent_id"];}
if (isset($_GET["LOGOUTevent_id"]))				{$LOGOUTevent_id=$_GET["LOGOUTevent_id"];}
	elseif (isset($_POST["LOGOUTevent_id"]))	{$LOGOUTevent_id=$_POST["LOGOUTevent_id"];}
if (isset($_GET["user"]))						{$user=$_GET["user"];}
	elseif (isset($_POST["user"]))				{$user=$_POST["user"];}
if (isset($_GET["stage"]))						{$stage=$_GET["stage"];}
	elseif (isset($_POST["stage"]))				{$stage=$_POST["stage"];}
if (isset($_GET["timeclock_id"]))				{$timeclock_id=$_GET["timeclock_id"];}
	elseif (isset($_POST["timeclock_id"]))		{$timeclock_id=$_POST["timeclock_id"];}
if (isset($_GET["DB"]))							{$DB=$_GET["DB"];}
	elseif (isset($_POST["DB"]))				{$DB=$_POST["DB"];}
if (isset($_GET["submit"]))						{$submit=$_GET["submit"];}
	elseif (isset($_POST["submit"]))			{$submit=$_POST["submit"];}
if (isset($_GET["VALIDER"]))						{$VALIDER=$_GET["VALIDER"];}
	elseif (isset($_POST["VALIDER"]))			{$VALIDER=$_POST["VALIDER"];}

#############################################
##### START SYSTEM_SETTINGS LOOKUP #####
$stmt = "SELECT use_non_latin,webroot_writable,outbound_autodial_active,user_territories_active FROM system_settings;";
$rslt=mysql_query($stmt, $link);
if ($DB) {echo "$stmt\n";}
$qm_conf_ct = mysql_num_rows($rslt);
$i=0;
while ($i < $qm_conf_ct)
	{
	$row=mysql_fetch_row($rslt);
	$non_latin =					$row[0];
	$webroot_writable =				$row[1];
	$SSoutbound_autodial_active =	$row[2];
	$user_territories_active =		$row[3];
	$i++;
	}
##### END SETTINGS LOOKUP #####
###########################################

$PHP_AUTH_USER = ereg_replace("[^0-9a-zA-Z]","",$PHP_AUTH_USER);
$PHP_AUTH_PW = ereg_replace("[^0-9a-zA-Z]","",$PHP_AUTH_PW);

$StarTtimE = date("U");
$TODAY = date("Y-m-d");
$NOW_TIME = date("Y-m-d H:i:s");
$ip = getenv("REMOTE_ADDR");
$invalid_record=0;

if (!isset($begin_date)) {$begin_date = $TODAY;}
if (!isset($end_date)) {$end_date = $TODAY;}

$stmt="SELECT count(*) from vicidial_users where user='$PHP_AUTH_USER' and pass='$PHP_AUTH_PW' and user_level > 7 and view_reports='1';";
if ($non_latin > 0) { $rslt=mysql_query("SET NAMES 'UTF8'");}
$rslt=mysql_query($stmt, $link);
$row=mysql_fetch_row($rslt);
$auth=$row[0];

$fp = fopen ("./project_auth_entries.txt", "a");
$date = date("r");
$ip = getenv("REMOTE_ADDR");
$browser = getenv("HTTP_USER_AGENT");

  if( (strlen($PHP_AUTH_USER)<2) or (strlen($PHP_AUTH_PW)<2) or (!$auth))
	{
    Header("WWW-Authenticate: Basic realm=\"VICI-PROJECTS\"");
    Header("HTTP/1.0 401 Unauthorized");
    echo "Login ou mot de passe invalide: |$PHP_AUTH_USER|$PHP_AUTH_PW|\n";
    exit;
	}
  else
	{

	if($auth>0)
		{
			$stmt="SELECT full_name,modify_timeclock_log from vicidial_users where user='$PHP_AUTH_USER' and pass='$PHP_AUTH_PW'";
			$rslt=mysql_query($stmt, $link);
			$row=mysql_fetch_row($rslt);
			$LOGfullname =				$row[0];
			$modify_timeclock_log =		$row[1];
		if ($webroot_writable > 0)
			{
			fwrite ($fp, "VICIDIAL|GOOD|$date|$PHP_AUTH_USER|$PHP_AUTH_PW|$ip|$browser|$LOGfullname|\n");
			fclose($fp);
			}
		}
	else
		{
		if ($webroot_writable > 0)
			{
			fwrite ($fp, "VICIDIAL|FAIL|$date|$PHP_AUTH_USER|$PHP_AUTH_PW|$ip|$browser|\n");
			fclose($fp);
			}
		}

	$stmt="SELECT full_name,user_group from vicidial_users where user='" . mysql_real_escape_string($user) . "';";
	$rslt=mysql_query($stmt, $link);
	$row=mysql_fetch_row($rslt);
	$full_name = $row[0];
	$user_group = $row[1];

	$stmt="SELECT event,tcid_link from vicidial_timeclock_log where timeclock_id='" . mysql_real_escape_string($timeclock_id) . "';";
	$rslt=mysql_query($stmt, $link);
	if ($DB) {echo "$stmt\n";}
	$tc_logs_to_print = mysql_num_rows($rslt);
	if ($tc_logs_to_print > 0)
		{
		$row=mysql_fetch_row($rslt);
		$event =		$row[0];
		$tcid_link =	$row[1];
		}
	if (ereg("LOGIN",$event))
		{
		$LOGINevent_id =	$timeclock_id;
		$LOGOUTevent_id =	$tcid_link;
		if ( (ereg('NULL',$LOGOUTevent_id)) or (strlen($LOGOUTevent_id)<1) )
			{$invalid_record++;}
		}
	if (ereg("LOGOUT",$event))
		{
		$LOGOUTevent_id =	$timeclock_id;
		$stmt="SELECT timeclock_id from vicidial_timeclock_log where tcid_link='" . mysql_real_escape_string($timeclock_id) . "';";
		$rslt=mysql_query($stmt, $link);
		if ($DB) {echo "$stmt\n";}
		$tc_logs_to_print = mysql_num_rows($rslt);
		if ($tc_logs_to_print > 0)
			{
			$row=mysql_fetch_row($rslt);
			$LOGINevent_id =		$row[0];
			}
		if ( (ereg('NULL',$LOGOUTevent_id)) or (strlen($LOGOUTevent_id)<1) )
			{$invalid_record++;}
		}
	if (strlen($LOGOUTevent_id)<1)
		{$invalid_record++;}

	### 
	if ($invalid_record < 1)
		{
		$stmt="SELECT event_epoch,event_date,login_sec,event,user,user_group,ip_address,shift_id,notes,manager_user,manager_ip,event_datestamp from vicidial_timeclock_log where timeclock_id='$LOGINevent_id';";
		$rslt=mysql_query($stmt, $link);
		if ($DB) {echo "$stmt\n";}
		$tc_logs_to_print = mysql_num_rows($rslt);
		if ($tc_logs_to_print > 0)
			{
			$row=mysql_fetch_row($rslt);
			$LOGINevent_epoch =		$row[0];
			$LOGINevent_date =		$row[1];
			$LOGINlogin_sec =		$row[2];
			$LOGINevent =			$row[3];
			$LOGINuser =			$row[4];
			$LOGINuser_group =		$row[5];
			$LOGINip_address =		$row[6];
			$LOGINshift_id =		$row[7];
			$LOGINnotes =			$row[8];
			$LOGINmanager_user =	$row[9];
			$LOGINmanager_ip =		$row[10];
			$LOGINevent_datestamp =	$row[11];
			}
		$stmt="SELECT event_epoch,event_date,login_sec,event,user,user_group,ip_address,shift_id,notes,manager_user,manager_ip,event_datestamp from vicidial_timeclock_log where timeclock_id='$LOGOUTevent_id';";
		$rslt=mysql_query($stmt, $link);
		if ($DB) {echo "$stmt\n";}
		$tc_logs_to_print = mysql_num_rows($rslt);
		if ($tc_logs_to_print > 0)
			{
			$row=mysql_fetch_row($rslt);
			$LOGOUTevent_epoch =	$row[0];
			$LOGOUTevent_date =		$row[1];
			$LOGOUTlogin_sec =		$row[2];
			$LOGOUTevent =			$row[3];
			$LOGOUTuser =			$row[4];
			$LOGOUTuser_group =		$row[5];
			$LOGOUTip_address =		$row[6];
			$LOGOUTshift_id =		$row[7];
			$LOGOUTnotes =			$row[8];
			$LOGOUTmanager_user =	$row[9];
			$LOGOUTmanager_ip =		$row[10];
			$LOGOUTevent_datestamp =$row[11];
			}

		$user=$LOGINuser;
		}
	}



?>
<html>
<head>
<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=utf-8">
<title>ADMINISTRATION:Horloge en temps Record Edit
<?php

##### BEGIN Set variables to make header show properly #####
$ADD =					'3';
$hh =					'users';
$TCedit_javascript =	'1';
$LOGast_admin_access =	'1';
$ADMIN =				'admin.php';
$page_width='770';
$section_width='750';
$header_font_size='3';
$subheader_font_size='2';
$subcamp_font_size='2';
$header_selected_bold='<b>';
$header_nonselected_bold='';
$users_color =		'#FFFF99';
$users_font =		'BLACK';
$users_color =		'#E6E6E6';
$subcamp_color =	'#C6C6C6';
##### END Set variables to make header show properly #####

require("admin_header.php");

?>


<CENTER>
<TABLE WIDTH=720 BGCOLOR=#D9E6FE cellpadding=2 cellspacing=0><TR BGCOLOR=#015B91><TD ALIGN=LEFT><FONT FACE="ARIAL,HELVETICA" COLOR=WHITE SIZE=2><B>Horloge en temps Record Edit for <?php echo $user ?></TD><TD ALIGN=RIGHT> &nbsp; </TD></TR>

<?php 

echo "<TR BGCOLOR=\"#F0F5FE\"><TD ALIGN=LEFT COLSPAN=2><FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=3><B> &nbsp; \n";





##### BEGIN TIMECLOCK RECORD MODIFY #####

if ( ($invalid_record < 1) or (strlen($timeclock_id)<1) )
{

if ($stage == "edit_TC_log")
	{
	$log_time = ($LOGOUTepoch - $LOGINepoch);
	$NEXTevent_epoch = $StarTtimE;
	$PREVevent_epoch = 0;

	$stmt="SELECT event_epoch,timeclock_id from vicidial_timeclock_log where timeclock_id > '$LOGOUTevent_id' and user='$user' order by timeclock_id limit 1;";
	$rslt=mysql_query($stmt, $link);
	if ($DB) {echo "$stmt\n";}
	$tc_logs_to_print = mysql_num_rows($rslt);
	if ($tc_logs_to_print > 0)
		{
		$row=mysql_fetch_row($rslt);
		$NEXTevent_epoch =	$row[0];
		$NEXTevent_id =		$row[1];
		}
	$stmt="SELECT event_epoch,timeclock_id from vicidial_timeclock_log where timeclock_id < '$LOGINevent_id' and user='$user' order by timeclock_id desc limit 1;";
	$rslt=mysql_query($stmt, $link);
	if ($DB) {echo "$stmt\n";}
	$tc_logs_to_print = mysql_num_rows($rslt);
	if ($tc_logs_to_print > 0)
		{
		$row=mysql_fetch_row($rslt);
		$PREVevent_epoch =	$row[0];
		$PREVevent_id =		$row[1];
		}

	if ( ($LOGINepoch <= $PREVevent_epoch) || ($LOGOUTepoch >= $NEXTevent_epoch) )
		{
		echo "ERREUR-Il ya un problème avec les données que vous avez entré, s'il vous plaît revenir<BR>\n";
		echo "A timeclock session ne peuvent pas se chevaucher un autre timeclock session<BR>\n";
		echo "$LOGINepoch<BR>\n";
		echo "$LOGOUTepoch<BR>\n";
		echo "$LOGINevent_id<BR>\n";
		echo "$LOGOUTevent_id<BR>\n";
		echo "$LOGINuser<BR>\n";
		echo "$PREVevent_epoch<BR>\n";
		echo "$PREVevent_id<BR>\n";
		echo "$NEXTevent_epoch<BR>\n";
		echo "$NEXTevent_id<BR>\n";
		exit;
		}
	if ( ($LOGINepoch > $StarTtimE) || ($LOGOUTepoch > $StarTtimE) || ($log_time > 86400) || ($log_time < 1) )
		{
		echo "ERREUR-Il ya un problème avec les données que vous avez entré, s'il vous plaît revenir<BR>\n";
		echo "$LOGINepoch<BR>\n";
		echo "$LOGOUTepoch<BR>\n";
		echo "$notes<BR>\n";
		echo "$LOGINevent_id<BR>\n";
		echo "$LOGOUTevent_id<BR>\n";
		echo "$LOGINuser<BR>\n";
		exit;
		}
	else
		{
		$LOGINdatetime = date("Y-m-d H:i:s", $LOGINepoch);
		$LOGOUTdatetime = date("Y-m-d H:i:s", $LOGOUTepoch);

		### update LOGIN record in the timeclock log
		$stmtA="UPDATE vicidial_timeclock_log set event_epoch='$LOGINepoch', event_date='$LOGINdatetime', manager_user='$PHP_AUTH_USER', manager_ip='$ip', notes='Manager MODIFY', login_sec='$log_time' where timeclock_id='$LOGINevent_id';";
		if ($DB) {echo "$stmtA\n";}
		$rslt=mysql_query($stmtA, $link);
		$affected_rows = mysql_affected_rows($link);
		$timeclock_id = mysql_insert_id($link);
		print "<!-- UPDATE vicidial_timeclock_log record updated for $user:   |$affected_rows|$timeclock_id| -->\n";

		### Add a record to the vicidial_admin_log
		$SQL_log = "$stmtA|";
		$SQL_log = ereg_replace(';','',$SQL_log);
		$SQL_log = addslashes($SQL_log);
		$stmt="INSERT INTO vicidial_admin_log set event_date='$NOW_TIME', user='$PHP_AUTH_USER', ip_address='$ip', event_section='TIMECLOCK', event_type='MODIFY', record_id='$LOGINevent_id', event_code='MANAGER MODIFY TIMECLOCK LOG', event_sql=\"$SQL_log\", event_notes='user: $user|$oldLOGINepoch|$oldLOGINdate|sec: $log_time|';";
		if ($DB) {echo "$stmt\n";}
		$rslt=mysql_query($stmt, $link);
		$affected_rows = mysql_affected_rows($link);
		print "<!-- NEW vicidial_admin_log record inserted for $PHP_AUTH_USER:   |$affected_rows| -->\n";

		### update LOGOUT record in the timeclock log
		$stmtB="UPDATE vicidial_timeclock_log set event_epoch='$LOGOUTepoch', event_date='$LOGOUTdatetime', manager_user='$PHP_AUTH_USER', manager_ip='$ip', notes='Manager MODIFY', login_sec='$log_time' where timeclock_id='$LOGOUTevent_id';";
		if ($DB) {echo "$stmtB\n";}
		$rslt=mysql_query($stmtB, $link);
		$affected_rows = mysql_affected_rows($link);
		$timeclock_id = mysql_insert_id($link);
		print "<!-- UPDATE vicidial_timeclock_log record updated for $user:   |$affected_rows|$timeclock_id| -->\n";

		### Add a record to the vicidial_admin_log
		$SQL_log = "$stmtB|";
		$SQL_log = ereg_replace(';','',$SQL_log);
		$SQL_log = addslashes($SQL_log);
		$stmt="INSERT INTO vicidial_admin_log set event_date='$NOW_TIME', user='$PHP_AUTH_USER', ip_address='$ip', event_section='TIMECLOCK', event_type='MODIFY', record_id='$LOGOUTevent_id', event_code='MANAGER MODIFY TIMECLOCK LOG', event_sql=\"$SQL_log\", event_notes='user: $user|$oldLOGOUTepoch|$oldLOGOUTdate|sec: $log_time|';";
		if ($DB) {echo "$stmt\n";}
		$rslt=mysql_query($stmt, $link);
		$affected_rows = mysql_affected_rows($link);
		print "<!-- NEW vicidial_admin_log record inserted for $PHP_AUTH_USER:   |$affected_rows| -->\n";

		echo "The timeclock session has been updated. <A HREF=\"$PHP_SELF?timeclock_id=$LOGINevent_id\">Click here to view</A>.<BR>\n";
		exit;
		}
	}
##### END TIMECLOCK RECORD MODIFY #####




echo "\n<BR>";

if ($modify_timeclock_log > 0)
	{
#	$LOGINevent_id =	$timeclock_id;
#	$LOGOUTevent_id =	$tcid_link;

	$event_hours = ($LOGINlogin_sec / 3600);
	$event_hours_int = round($event_hours, 2);
	$event_hours_int = intval("$event_hours_int");
	$event_minutes = ($event_hours - $event_hours_int);
	$event_minutes = ($event_minutes * 60);
	$event_minutes_int = round($event_minutes, 0);
	if ($event_minutes_int < 10) {$event_minutes_int = "0$event_minutes_int";}

	$stmt="SELECT full_name from vicidial_users where user='$LOGINuser';";
	if ($DB) {echo "|$stmt|\n";}
	$rslt=mysql_query($stmt, $link);
	$row=mysql_fetch_row($rslt);
	$full_name =		$row[0];

	echo "<BR><BR>\n";
	echo "<form action=$PHP_SELF method=POST name=edit_log id=edit_log>\n";
	echo "<input type=hidden name=DB value=\"$DB\">\n";
	echo "<input type=hidden name=user value=\"$user\">\n";
	echo "<input type=hidden name=stage value=edit_TC_log>\n";
	echo "<input type=hidden name=oldLOGINepoch id=oldLOGINepoch value=\"$LOGINevent_epoch\">\n";
	echo "<input type=hidden name=oldLOGOUTepoch id=oldLOGOUTepoch value=\"$LOGOUTevent_epoch\">\n";
	echo "<input type=hidden name=oldLOGINdate id=oldLOGINdate value=\"$LOGINevent_date\">\n";
	echo "<input type=hidden name=oldLOGOUTdate id=oldLOGOUTdate value=\"$LOGOUTevent_date\">\n";
	echo "<input type=hidden name=LOGINepoch id=LOGINepoch value=\"$LOGINevent_epoch\">\n";
	echo "<input type=hidden name=LOGOUTepoch id=LOGOUTepoch value=\"$LOGOUTevent_epoch\">\n";
	echo "<input type=hidden name=LOGINevent_id id=LOGINevent_id value=\"$LOGINevent_id\">\n";
	echo "<input type=hidden name=LOGOUTevent_id id=LOGOUTevent_id value=\"$LOGOUTevent_id\">\n";
	echo "<input type=hidden name=stage value=edit_TC_log>\n";
	echo "<TABLE BORDER=0><TR><TD COLSPAN=3 ALIGN=LEFT>\n";
	echo " &nbsp; &nbsp; &nbsp; &nbsp;USER: $LOGINuser ($full_name) &nbsp; &nbsp; &nbsp; &nbsp; \n";
	echo "HOURS: <span name=login_time id=login_time> $event_hours_int:$event_minutes_int </span>\n";
	echo "</TD></TR>\n";
	echo "<TR><TD>\n";
	echo "<TABLE BORDER=0>\n";
	echo "<TR><TD ALIGN=RIGHT>LOGIN TIME: </TD><TD ALIGN=RIGHT><input type=text name=LOGINbegin_date id=LOGINbegin_date value=\"$LOGINevent_date\" size=20 maxlength=20 onchange=\"calculate_hours();\"></TD></TR>\n";
	echo "<TR><TD ALIGN=RIGHT>TIMECLOCK ID: </TD><TD ALIGN=RIGHT>$LOGINevent_id</TD></TR>\n";
	echo "<TR><TD ALIGN=RIGHT>GROUPE D'UTILISATEURS: </TD><TD ALIGN=RIGHT>$LOGINuser_group</TD></TR>\n";
	echo "<TR><TD ALIGN=RIGHT>IP ADDRESS: </TD><TD ALIGN=RIGHT>$LOGINip_address</TD></TR>\n";
	echo "<TR><TD ALIGN=RIGHT>MANAGER USER: </TD><TD ALIGN=RIGHT>$LOGINmanager_user</TD></TR>\n";
	echo "<TR><TD ALIGN=RIGHT>MANAGER IP: </TD><TD ALIGN=RIGHT>$LOGINmanager_ip</TD></TR>\n";
	echo "<TR><TD ALIGN=RIGHT>NOTES: </TD><TD ALIGN=RIGHT>$LOGINnotes</TD></TR>\n";
	echo "<TR><TD ALIGN=RIGHT>LAST CHANGE: </TD><TD ALIGN=RIGHT>$LOGINevent_datestamp</TD></TR>\n";
	echo "</TABLE>\n";

	echo "</TD><TD> &nbsp; &nbsp; &nbsp; &nbsp; \n";
	echo "</TD><TD>\n";
	echo "<TABLE BORDER=0>\n";
	echo "<TR><TD ALIGN=RIGHT>LOGOUT TIME: </TD><TD ALIGN=RIGHT><input type=text name=LOGOUTbegin_date id=LOGOUTbegin_date value=\"$LOGOUTevent_date\" size=20 maxlength=20 onchange=\"calculate_hours();\"></TD></TR>\n";
	echo "<TR><TD ALIGN=RIGHT>TIMECLOCK ID: </TD><TD ALIGN=RIGHT>$LOGOUTevent_id</TD></TR>\n";
	echo "<TR><TD ALIGN=RIGHT>GROUPE D'UTILISATEURS: </TD><TD ALIGN=RIGHT>$LOGOUTuser_group</TD></TR>\n";
	echo "<TR><TD ALIGN=RIGHT>IP ADDRESS: </TD><TD ALIGN=RIGHT>$LOGOUTip_address</TD></TR>\n";
	echo "<TR><TD ALIGN=RIGHT>MANAGER USER: </TD><TD ALIGN=RIGHT>$LOGOUTmanager_user</TD></TR>\n";
	echo "<TR><TD ALIGN=RIGHT>MANAGER IP: </TD><TD ALIGN=RIGHT>$LOGOUTmanager_ip</TD></TR>\n";
	echo "<TR><TD ALIGN=RIGHT>NOTES: </TD><TD ALIGN=RIGHT>$LOGOUTnotes</TD></TR>\n";
	echo "<TR><TD ALIGN=RIGHT>LAST CHANGE: </TD><TD ALIGN=RIGHT>$LOGOUTevent_datestamp</TD></TR>\n";
	echo "</TABLE>\n";
	echo "</TD></TR>\n";

	echo "<TR><TD COLSPAN=3 ALIGN=LEFT>\n";
	echo "NEW NOTES: <input type=text name=notes value='' size=80 maxlength=255>\n";
	echo "</TD></TR>\n";
	echo "<TR><TD COLSPAN=3 ALIGN=CENTER>\n";
	echo "<input type=button name=go_submit id=go_submit value=VALIDER onclick=\"run_submit();\"><BR></form>\n";
	echo "</TD></TR></TABLE>\n";
	echo "<BR><BR>\n";
	}


echo "<a href=\"./AST_agent_time_sheet.php?agent=$user\">Agent Feuille de temps</a>\n";
echo " - <a href=\"./user_stats.php?user=$user\">Statistiques utilisateur</a>\n";
echo " - <a href=\"./admin.php?ADD=3&user=$user\">Modifier un utilisateur</a>\n";

echo "</B></TD></TR>\n";
echo "<TR><TD ALIGN=LEFT COLSPAN=2>\n";


$ENDtime = date("U");

$RUNtime = ($ENDtime - $StarTtimE);

echo "\n\n\n<br><br><br>\n\n";


echo "<font size=0>\n\n\n<br><br><br>\ntemps d'exécution du script: $RUNtime seconds</font>";

echo "|$stage|$group|";

}
else
{

echo "ERROR! You cannot edit this timeclock record: $timeclock_id\n";
}
?>


</TD></TR><TABLE>
</body>
</html>

<?php
	
exit; 



?>

