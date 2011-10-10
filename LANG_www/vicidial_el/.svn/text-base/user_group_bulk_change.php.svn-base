<?php
# user_group_bulk_change.php
# 
# Copyright (C) 2009  Matt Florell <vicidial@gmail.com>    LICENSE: AGPLv2
#
# CHANGES
# 81119-0918 - First build
# 90309-1830 - Added admin_log logging
# 90310-2144 - Added admin header
# 90508-0644 - Changed to PHP long tags
#

header ("Content-type: text/html; charset=utf-8");

require("dbconnect.php");

$PHP_AUTH_USER=$_SERVER['PHP_AUTH_USER'];
$PHP_AUTH_PW=$_SERVER['PHP_AUTH_PW'];
$PHP_SELF=$_SERVER['PHP_SELF'];
if (isset($_GET["old_group"]))			{$old_group=$_GET["old_group"];}
	elseif (isset($_POST["old_group"]))	{$old_group=$_POST["old_group"];}
if (isset($_GET["group"]))				{$group=$_GET["group"];}
	elseif (isset($_POST["group"]))		{$group=$_POST["group"];}
if (isset($_GET["stage"]))				{$stage=$_GET["stage"];}
	elseif (isset($_POST["stage"]))		{$stage=$_POST["stage"];}
if (isset($_GET["DB"]))					{$DB=$_GET["DB"];}
	elseif (isset($_POST["DB"]))		{$DB=$_POST["DB"];}
if (isset($_GET["submit"]))				{$submit=$_GET["submit"];}
	elseif (isset($_POST["submit"]))	{$submit=$_POST["submit"];}
if (isset($_GET["ΕΠΙΒΕΒΑΙΩΣΗ"]))				{$ΕΠΙΒΕΒΑΙΩΣΗ=$_GET["ΕΠΙΒΕΒΑΙΩΣΗ"];}
	elseif (isset($_POST["ΕΠΙΒΕΒΑΙΩΣΗ"]))	{$ΕΠΙΒΕΒΑΙΩΣΗ=$_POST["ΕΠΙΒΕΒΑΙΩΣΗ"];}

#############################################
##### START SYSTEM_SETTINGS LOOKUP #####
$stmt = "SELECT use_non_latin,webroot_writable,outbound_autodial_active FROM system_settings;";
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
    echo "Ακυρο Ονομα Χρήστη/Κωδικός Πρόσβασης: |$PHP_AUTH_USER|$PHP_AUTH_PW|\n";
    exit;
	}
  else
	{

	if($auth>0)
		{
			$stmt="SELECT full_name,change_agent_campaign,modify_timeclock_log from vicidial_users where user='$PHP_AUTH_USER' and pass='$PHP_AUTH_PW'";
			$rslt=mysql_query($stmt, $link);
			$row=mysql_fetch_row($rslt);
			$LOGfullname =				$row[0];
			$change_agent_campaign =	$row[1];
			$modify_timeclock_log =		$row[2];
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
	}

$stmt="select user_group,group_name from vicidial_user_groups order by user_group desc;";
$rslt=mysql_query($stmt, $link);
if ($DB) {echo "$stmt\n";}
$groups_to_print = mysql_num_rows($rslt);
$i=0;
while ($i < $groups_to_print)
	{
	$row=mysql_fetch_row($rslt);
	$groups[$i] =		$row[0];
	$group_names[$i] =	$row[1];
	$i++;
	}



?>
<html>
<head>
<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=utf-8">
<title>ADMINISTRATION: Ομάδα Χρήστη Μαζική Αλλαγή
<?php

##### BEGIN Set variables to make header show properly #####
$ADD =					'311111';
$hh =					'usergroups';
$LOGast_admin_access =	'1';
$ADMIN =				'admin.php';
$page_width='770';
$section_width='750';
$header_font_size='3';
$subheader_font_size='2';
$subcamp_font_size='2';
$header_selected_bold='<b>';
$header_nonselected_bold='';
$usergroups_color =		'#FFFF99';
$usergroups_font =		'BLACK';
$usergroups_color =		'#E6E6E6';
$subcamp_color =	'#C6C6C6';
##### END Set variables to make header show properly #####

require("admin_header.php");




?>

<CENTER>
<TABLE WIDTH=620 BGCOLOR=#D9E6FE cellpadding=2 cellspacing=0><TR BGCOLOR=#015B91><TD ALIGN=LEFT><FONT FACE="ARIAL,HELVETICA" COLOR=WHITE SIZE=2><B> &nbsp; Ομάδα Χρήστη Μαζική Αλλαγή</TD><TD ALIGN=RIGHT><FONT FACE="ARIAL,HELVETICA" COLOR=WHITE SIZE=2><B> &nbsp; </TD></TR>




<?php 

echo "<TR BGCOLOR=\"#F0F5FE\"><TD ALIGN=LEFT COLSPAN=2><FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=3><B> &nbsp; \n";

##### GROUP CHANGE FOR ALL USERS IN A USER GROUP #####
if ($stage == "one_user_group_change")
	{
	$stmt="UPDATE vicidial_users set user_group='" . mysql_real_escape_string($group) . "' where user_group='" . mysql_real_escape_string($old_group) . "';";
	$rslt=mysql_query($stmt, $link);

	echo "All Ομάδα Χρήστη $old_group Χρήστες changed to the $group Ομάδα Χρήστη<BR>\n";
	
	### LOG INSERTION Admin Log Table ###
	$SQL_log = "$stmt|";
	$SQL_log = ereg_replace(';','',$SQL_log);
	$SQL_log = addslashes($SQL_log);
	$stmt="INSERT INTO vicidial_admin_log set event_date='$NOW_TIME', user='$PHP_AUTH_USER', ip_address='$ip', event_section='USERGROUPS', event_type='MODIFY', record_id='$group', event_code='ADMIN BULK ΟΜΑΔΑ ΧΡΗΣΤΩΝ CHANGE', event_sql=\"$SQL_log\", event_notes='Old Ομάδα:$old_group';";
	if ($DB) {echo "|$stmt|\n";}
	$rslt=mysql_query($stmt, $link);

	exit;
	}

##### GROUP CHANGE FOR ALL USERS IN THE SYSTEM EXCEPT FOR LEVEL > 6 AND ADMIN GROUP #####
if ($stage == "all_user_group_change")
	{
	$stmt="UPDATE vicidial_users set user_group='" . mysql_real_escape_string($group) . "' where user_group!='ADMIN' and user_group < 7;";
	$rslt=mysql_query($stmt, $link);

	echo "All non-Admin Χρήστες changed to the $group Ομάδα Χρήστη<BR>\n";
	
	### LOG INSERTION Admin Log Table ###
	$SQL_log = "$stmt|";
	$SQL_log = ereg_replace(';','',$SQL_log);
	$SQL_log = addslashes($SQL_log);
	$stmt="INSERT INTO vicidial_admin_log set event_date='$NOW_TIME', user='$PHP_AUTH_USER', ip_address='$ip', event_section='USERGROUPS', event_type='MODIFY', record_id='$group', event_code='ADMIN BULK ΟΜΑΔΑ ΧΡΗΣΤΩΝ CHANGE', event_sql=\"$SQL_log\", event_notes='ALL NON-ADMIN;";
	if ($DB) {echo "|$stmt|\n";}
	$rslt=mysql_query($stmt, $link);

	exit;
	}

### one user_group change
echo "<form action=$PHP_SELF method=POST>\n";
echo "<input type=hidden name=DB value=\"$DB\">\n";
echo "<input type=hidden name=stage value=\"one_user_group_change\">\n";
echo "Change Χρήστες in this group: <SELECT SIZE=1 NAME=old_group>\n";
$o=0;
while ($groups_to_print > $o)
	{
	echo "<option value=\"$groups[$o]\">$groups[$o] - $group_names[$o]</option>\n";
	$o++;
	}
echo "</SELECT>\n";
echo "<BR> &nbsp; to this group: <SELECT SIZE=1 NAME=group>\n";
$o=0;
while ($groups_to_print > $o)
	{
	echo "<option value=\"$groups[$o]\">$groups[$o] - $group_names[$o]</option>\n";
	$o++;
	}
echo "</SELECT>\n";
echo "<BR><CENTER><input type=submit name=submit value=ΕΠΙΒΕΒΑΙΩΣΗ></CENTER><BR></form>\n";

echo "\n<BR><BR><BR>";



### all user_group change
echo "<form action=$PHP_SELF method=POST>\n";
echo "<input type=hidden name=DB value=\"$DB\">\n";
echo "<input type=hidden name=stage value=\"all_user_group_change\">\n";
echo "Change ALL non-Admin Χρήστες to this group: <BR><SELECT SIZE=1 NAME=group>\n";
$o=0;
while ($groups_to_print > $o)
	{
	echo "<option value=\"$groups[$o]\">$groups[$o] - $group_names[$o]</option>\n";
	$o++;
	}
echo "</SELECT>\n";
echo "<BR><CENTER><input type=submit name=submit value=ΕΠΙΒΕΒΑΙΩΣΗ></CENTER><BR></form>\n";

echo "\n<BR>";



$ENDtime = date("U");

$RUNtime = ($ENDtime - $StarTtimE);

echo "\n\n\n<br><br><br>\n\n";


echo "<font size=0>\n\n\n<br><br><br>\nχρόνος εκτέλεσης διαδικασίας: $RUNtime seconds</font>";

echo "|$stage|$group|";

?>


</TD></TR><TABLE>
</body>
</html>

<?php
	
exit; 



?>

