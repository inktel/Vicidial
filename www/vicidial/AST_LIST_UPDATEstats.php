<?php 
# AST_LIST_UPDATEstats.php
# 
# Copyright (C) 2010  Matt Florell <vicidial@gmail.com>    LICENSE: AGPLv2
#
# CHANGES
#
# 90627-2055 - First build
# 90907-0636 - Added list id to results
# 100712-1324 - Added system setting slave server option
# 100802-2347 - Added User Group Allowed Reports option validation
# 100914-1326 - Added lookup for user_level 7 users to set to reports only which will remove other admin links
#

require("dbconnect.php");
require("functions.php");

$PHP_AUTH_USER=$_SERVER['PHP_AUTH_USER'];
$PHP_AUTH_PW=$_SERVER['PHP_AUTH_PW'];
$PHP_SELF=$_SERVER['PHP_SELF'];
if (isset($_GET["query_date"]))				{$query_date=$_GET["query_date"];}
	elseif (isset($_POST["query_date"]))	{$query_date=$_POST["query_date"];}
if (isset($_GET["end_date"]))				{$end_date=$_GET["end_date"];}
	elseif (isset($_POST["end_date"]))		{$end_date=$_POST["end_date"];}
if (isset($_GET["submit"]))					{$submit=$_GET["submit"];}
	elseif (isset($_POST["submit"]))		{$submit=$_POST["submit"];}
if (isset($_GET["SUBMIT"]))					{$SUBMIT=$_GET["SUBMIT"];}
	elseif (isset($_POST["SUBMIT"]))		{$SUBMIT=$_POST["SUBMIT"];}
if (isset($_GET["DB"]))						{$DB=$_GET["DB"];}
	elseif (isset($_POST["DB"]))			{$DB=$_POST["DB"];}

$PHP_AUTH_USER = ereg_replace("[^0-9a-zA-Z]","",$PHP_AUTH_USER);
$PHP_AUTH_PW = ereg_replace("[^0-9a-zA-Z]","",$PHP_AUTH_PW);

if (strlen($shift)<2) {$shift='ALL';}

$report_name = 'List Update Stats';
$db_source = 'M';

#############################################
##### START SYSTEM_SETTINGS LOOKUP #####
$stmt = "SELECT use_non_latin,outbound_autodial_active,slave_db_server,reports_use_slave_db FROM system_settings;";
$rslt=mysql_query($stmt, $link);
if ($DB) {echo "$stmt\n";}
$qm_conf_ct = mysql_num_rows($rslt);
if ($qm_conf_ct > 0)
	{
	$row=mysql_fetch_row($rslt);
	$non_latin =					$row[0];
	$outbound_autodial_active =		$row[1];
	$slave_db_server =				$row[2];
	$reports_use_slave_db =			$row[3];
	}
##### END SETTINGS LOOKUP #####
###########################################

if ( (strlen($slave_db_server)>5) and (preg_match("/$report_name/",$reports_use_slave_db)) )
	{
	mysql_close($link);
	$use_slave_server=1;
	$db_source = 'S';
	require("dbconnect.php");
	echo "<!-- Using slave server $slave_db_server $db_source -->\n";
	}

$stmt="SELECT count(*) from vicidial_users where user='$PHP_AUTH_USER' and pass='$PHP_AUTH_PW' and user_level >= 7 and view_reports='1' and active='Y';";
if ($DB) {echo "|$stmt|\n";}
if ($non_latin > 0) {$rslt=mysql_query("SET NAMES 'UTF8'");}
$rslt=mysql_query($stmt, $link);
$row=mysql_fetch_row($rslt);
$auth=$row[0];

$stmt="SELECT count(*) from vicidial_users where user='$PHP_AUTH_USER' and pass='$PHP_AUTH_PW' and user_level='7' and view_reports='1' and active='Y';";
if ($DB) {echo "|$stmt|\n";}
$rslt=mysql_query($stmt, $link);
$row=mysql_fetch_row($rslt);
$reports_only_user=$row[0];

if ( (strlen($PHP_AUTH_USER)<2) or (strlen($PHP_AUTH_PW)<2) or (!$auth))
	{
    Header("WWW-Authenticate: Basic realm=\"VICI-PROJECTS\"");
    Header("HTTP/1.0 401 Unauthorized");
    echo "Invalid Username/Password: |$PHP_AUTH_USER|$PHP_AUTH_PW|\n";
    exit;
	}

$stmt="SELECT user_group from vicidial_users where user='$PHP_AUTH_USER' and pass='$PHP_AUTH_PW' and user_level > 6 and view_reports='1' and active='Y';";
if ($DB) {echo "|$stmt|\n";}
$rslt=mysql_query($stmt, $link);
$row=mysql_fetch_row($rslt);
$LOGuser_group =			$row[0];

$stmt="SELECT allowed_campaigns,allowed_reports from vicidial_user_groups where user_group='$LOGuser_group';";
if ($DB) {echo "|$stmt|\n";}
$rslt=mysql_query($stmt, $link);
$row=mysql_fetch_row($rslt);
$LOGallowed_campaigns = $row[0];
$LOGallowed_reports =	$row[1];

if ( (!preg_match("/$report_name/",$LOGallowed_reports)) and (!preg_match("/ALL REPORTS/",$LOGallowed_reports)) )
	{
    Header("WWW-Authenticate: Basic realm=\"VICI-PROJECTS\"");
    Header("HTTP/1.0 401 Unauthorized");
    echo "You are not allowed to view this report: |$PHP_AUTH_USER|$report_name|\n";
    exit;
	}

$NOW_DATE = date("Y-m-d");
$NOW_TIME = date("Y-m-d H:i:s");
$STARTtime = date("U");
if (!isset($group)) {$group = '';}
if (!isset($query_date)) {$query_date = "$NOW_DATE 00:00:00";}
if (!isset($end_date)) {$end_date = "$NOW_DATE 23:59:59";}


?>

<HTML>
<HEAD>
<STYLE type="text/css">
<!--
   .green {color: black; background-color: #99FF99}
   .red {color: black; background-color: #FF9999}
   .orange {color: black; background-color: #FFCC99}
-->
 </STYLE>

<?php 
echo "<META HTTP-EQUIV=\"Content-Type\" CONTENT=\"text/html; charset=utf-8\">\n";
echo "<TITLE>$report_name</TITLE></HEAD><BODY BGCOLOR=WHITE marginheight=0 marginwidth=0 leftmargin=0 topmargin=0>\n";

	$short_header=1;

	require("admin_header.php");

if ($DB > 0)
	{
	echo "<BR>\n";
	echo "$query_date|$end_date\n";
	echo "<BR>\n";
	}

echo "<TABLE CELLPADDING=4 CELLSPACING=0><TR><TD>";

echo "<FORM ACTION=\"$PHP_SELF\" METHOD=GET>\n";
echo "<TABLE BORDER=0 CELLPADDING=2 CELLSPACING=2><TR><TD align=center valign=top>\n";
echo "<INPUT TYPE=TEXT NAME=query_date SIZE=20 MAXLENGTH=20 VALUE=\"$query_date\">\n";
echo "<BR> to <BR><INPUT TYPE=TEXT NAME=end_date SIZE=20 MAXLENGTH=20 VALUE=\"$end_date\">\n";
echo "</TD><TD align=center valign=top>\n";
echo "</TD><TD align=center valign=top>\n";
echo "</TD><TD align=center valign=top>\n";
echo "<INPUT TYPE=hidden NAME=DB VALUE=\"$DB\">\n";
echo "<INPUT TYPE=submit NAME=SUBMIT VALUE=SUBMIT>\n";
echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;  <a href=\"./admin.php?ADD=999999\">REPORTS</a> </FONT>\n";
echo "</TD></TR></TABLE>\n";
echo "</FORM>\n";

echo "<PRE><FONT SIZE=2>";


if (!$query_date)
{
echo "\n\n";
echo "PLEASE SELECT A DATE RANGE ABOVE AND CLICK SUBMIT\n";
}

else
{
echo "List Update Process Report                      $NOW_TIME\n";
echo "\n";
echo "Time range $query_date to $end_date\n\n";


### GRAB ALL RECORDS WITHIN RANGE FROM THE DATABASE ###
echo "List Update Summary:\n";
echo "+----------------------------------------------------+------------+----------+----------+\n";
echo "| FILENAME                                           | RESULT     | RECORDS  | UPDATES  |\n";
echo "+----------------------------------------------------+------------+----------+----------+\n";

$stmt="SELECT count(*),filename,result,sum(result_rows) from vicidial_list_update_log where event_date >= '$query_date' and event_date <= '$end_date' group by filename,result order by filename,result;";
$rslt=mysql_query($stmt, $link);
if ($DB) {echo "$stmt\n";}
$records_to_grab = mysql_num_rows($rslt);
$i=0;
while ($i < $records_to_grab)
	{
	$row=mysql_fetch_row($rslt);
	$TOTcount = ($TOTcount + $row[0]);
	$TOTupdated = ($TOTupdated + $row[3]);
	$count =	sprintf("%-8s", $row[0]);
	$filename =	sprintf("%-50s", trim($row[1]));
	$result =	sprintf("%-10s", $row[2]);
	$updated =	sprintf("%-8s", $row[3]);
	echo "| $filename | $result | $count | $updated |\n";

	$i++;
	}
$TOTcount =	sprintf("%-8s", $TOTcount);
$TOTupdated =	sprintf("%-8s", $TOTupdated);

echo "+----------------------------------------------------+------------+----------+----------+\n";
echo "                                                                  | $TOTcount | $TOTupdated |\n";
echo "                                                                  +----------+----------+\n";
echo "\n</PRE>\n";



### GRAB ALL RECORDS WITHIN RANGE FROM THE DATABASE ###
echo "List Update Detail:<BR>\n";
echo "<TABLE CELLPADDING=1 CELLSPACING=1 BORDER=1>\n";
echo "<TR><TD><B>DATE/TIME</B></TD><TD><B>LEAD_ID</B></TD><TD><B>VENDOR_ID</B></TD><TD><B>PHONE</B></TD><TD><B>STATUS</B></TD><TD><B>OLD_STATUS</B></TD><TD><B>OLD_LIST</B></TD><TD><B>FILENAME</B></TD><TD><B>RESULT</B></TD><TD><B>UPDATED_ROWS</B></TD>\n";
echo "</TR>\n";


$stmt="SELECT event_date,lead_id,vendor_id,phone_number,status,old_status,filename,result,result_rows,list_id from vicidial_list_update_log where event_date >= '$query_date' and event_date <= '$end_date' order by event_date,filename;";
$rslt=mysql_query($stmt, $link);
if ($DB) {echo "$stmt\n";}
$records_to_grab = mysql_num_rows($rslt);
$i=0;
while ($i < $records_to_grab)
	{
	$row=mysql_fetch_row($rslt);
	$row[6] = trim($row[6]);
	echo "<TR><TD>$row[0]</TD><TD>$row[1]</TD><TD>$row[2]</TD><TD>$row[3]</TD><TD>$row[4]</TD><TD>$row[5]</TD><TD>$row[9]</TD><TD>$row[6]</TD><TD>$row[7]</TD><TD>$row[8]</TD></TR>\n";
	$i++;
	}




$ENDtime = date("U");
$RUNtime = ($ENDtime - $STARTtime);
echo "</TABLE><BR>\nRun Time: $RUNtime seconds|$db_source\n";
}



?>

</TD></TR></TABLE>

</BODY></HTML>
