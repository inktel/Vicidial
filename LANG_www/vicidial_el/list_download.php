<?php
# list_download.php
# 
# downloads the entire contents of a vicidial list ID to a flat text file
# that is tab delimited
#
# Copyright (C) 2010  Matt Florell <vicidial@gmail.com>    LICENSE: AGPLv2
#
# CHANGES
#
# 90209-1310 - First build
# 90508-0644 - Changed to PHP long tags
# 90721-1238 - Added rank and owner as vicidial_list fields
# 100119-1039 - Filtered comments for \n newlines
#

require("dbconnect.php");

$PHP_AUTH_USER=$_SERVER['PHP_AUTH_USER'];
$PHP_AUTH_PW=$_SERVER['PHP_AUTH_PW'];
$PHP_SELF=$_SERVER['PHP_SELF'];
if (isset($_GET["list_id"]))				{$list_id=$_GET["list_id"];}
	elseif (isset($_POST["list_id"]))		{$list_id=$_POST["list_id"];}
if (isset($_GET["DB"]))						{$DB=$_GET["DB"];}
	elseif (isset($_POST["DB"]))			{$DB=$_POST["DB"];}
if (isset($_GET["submit"]))					{$submit=$_GET["submit"];}
	elseif (isset($_POST["submit"]))		{$submit=$_POST["submit"];}
if (isset($_GET["ΕΠΙΒΕΒΑΙΩΣΗ"]))					{$ΕΠΙΒΕΒΑΙΩΣΗ=$_GET["ΕΠΙΒΕΒΑΙΩΣΗ"];}
	elseif (isset($_POST["ΕΠΙΒΕΒΑΙΩΣΗ"]))		{$ΕΠΙΒΕΒΑΙΩΣΗ=$_POST["ΕΠΙΒΕΒΑΙΩΣΗ"];}

if (strlen($shift)<2) {$shift='ALL';}

#############################################
##### START SYSTEM_SETTINGS LOOKUP #####
$stmt = "SELECT use_non_latin FROM system_settings;";
$rslt=mysql_query($stmt, $link);
if ($DB) {echo "$stmt\n";}
$qm_conf_ct = mysql_num_rows($rslt);
if ($qm_conf_ct > 0)
	{
	$row=mysql_fetch_row($rslt);
	$non_latin =					$row[0];
	}
##### END SETTINGS LOOKUP #####
###########################################

$PHP_AUTH_USER = ereg_replace("[^-_0-9a-zA-Z]","",$PHP_AUTH_USER);
$PHP_AUTH_PW = ereg_replace("[^-_0-9a-zA-Z]","",$PHP_AUTH_PW);

$stmt="SELECT count(*) from vicidial_users where user='$PHP_AUTH_USER' and pass='$PHP_AUTH_PW' and user_level > 7 and download_lists='1';";
if ($DB) {echo "|$stmt|\n";}
if ($non_latin > 0) { $rslt=mysql_query("SET NAMES 'UTF8'");}
$rslt=mysql_query($stmt, $link);
$row=mysql_fetch_row($rslt);
$auth=$row[0];

if( (strlen($PHP_AUTH_USER)<2) or (strlen($PHP_AUTH_PW)<2) or (!$auth))
	{
#	Header("WWW-Authenticate: Basic realm=\"VICI-PROJECTS\"");
#	Header("HTTP/1.0 401 Unauthorized");
    echo "Ακυρο Ονομα Χρήστη/Κωδικός Πρόσβασης or no list download permission: |$PHP_AUTH_USER|\n";
    exit;
	}

$stmt="select count(*) from vicidial_list where list_id='$list_id';";
$rslt=mysql_query($stmt, $link);
if ($DB) {echo "$stmt\n";}
$count_to_print = mysql_num_rows($rslt);
if ($count_to_print > 0)
	{
	$row=mysql_fetch_row($rslt);
	$leads_count =$row[0];
	$i++;
	}

if ($leads_count < 1)
	{
	echo "There are no leads in list_id: $list_id\n";
	exit;
	}

$US='_';
$MT[0]='';
$ip = getenv("REMOTE_ADDR");
$NOW_DATE = date("Y-m-d");
$NOW_TIME = date("Y-m-d H:i:s");
$FILE_TIME = date("Ymd-His");
$STARTtime = date("U");
if (!isset($group)) {$group = '';}
if (!isset($query_date)) {$query_date = $NOW_DATE;}
if (!isset($end_date)) {$end_date = $NOW_DATE;}

### LOG INSERTION Admin Log Table ###
$SQL_log = "$stmt|$stmtA|";
$SQL_log = ereg_replace(';','',$SQL_log);
$SQL_log = addslashes($SQL_log);
$stmt="INSERT INTO vicidial_admin_log set event_date='$NOW_TIME', user='$PHP_AUTH_USER', ip_address='$ip', event_section='LEADS', event_type='EXPORT', record_id='$list_id', event_code='ADMIN EXPORT LIST', event_sql=\"$SQL_log\", event_notes='';";
if ($DB) {echo "|$stmt|\n";}
$rslt=mysql_query($stmt, $link);


$TXTfilename = "LIST_$list_id$US$FILE_TIME.txt";

// We'll be outputting a TXT file
header('Content-type: application/octet-stream');

// It will be called LIST_101_20090209-121212.txt
header("Content-Disposition: attachment; filename=\"$TXTfilename\"");
header('Expires: 0');
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
header('Pragma: public');
ob_clean();
flush();

$stmt="select lead_id,entry_date,modify_date,status,user,vendor_lead_code,source_id,list_id,gmt_offset_now,called_since_last_reset,phone_code,phone_number,title,first_name,middle_initial,last_name,address1,address2,address3,city,state,province,postal_code,country_code,gender,date_of_birth,alt_phone,email,security_phrase,comments,called_count,last_local_call_time,rank,owner from vicidial_list where list_id='$list_id';";
$rslt=mysql_query($stmt, $link);
if ($DB) {echo "$stmt\n";}
$leads_to_print = mysql_num_rows($rslt);
$i=0;
while ($i < $leads_to_print)
	{
	$row=mysql_fetch_row($rslt);

	$row[29] = preg_replace("/\n|\r/",'!N',$row[29]);

	echo "$row[0]\t$row[1]\t$row[2]\t$row[3]\t$row[4]\t$row[5]\t$row[6]\t$row[7]\t$row[8]\t$row[9]\t$row[10]\t$row[11]\t$row[12]\t$row[13]\t$row[14]\t$row[15]\t$row[16]\t$row[17]\t$row[18]\t$row[19]\t$row[20]\t$row[21]\t$row[22]\t$row[23]\t$row[24]\t$row[25]\t$row[26]\t$row[27]\t$row[28]\t$row[29]\t$row[30]\t$row[31]\t$row[32]\t$row[33]\r\n";

	$i++;
	}

exit;

?>