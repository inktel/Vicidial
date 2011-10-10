<?php
# phone_stats.php.php
# 
# Copyright (C) 2009  Matt Florell <vicidial@gmail.com>    LICENSE: AGPLv2
#
# 
# changes:
# 60620-1333 - Added variable filtering to eliminate SQL injection attack threat
#            - Added required user/pass to gain access to this page
# 60927-1548 - Changed to vicidial_users for authentication
# 90508-0644 - Changed to PHP long tags
#

require("dbconnect.php");

$PHP_AUTH_USER=$_SERVER['PHP_AUTH_USER'];
$PHP_AUTH_PW=$_SERVER['PHP_AUTH_PW'];
$PHP_SELF=$_SERVER['PHP_SELF'];
if (isset($_GET["group"]))				{$group=$_GET["group"];}
	elseif (isset($_POST["group"]))		{$group=$_POST["group"];}
if (isset($_GET["query_date"]))				{$query_date=$_GET["query_date"];}
	elseif (isset($_POST["query_date"]))		{$query_date=$_POST["query_date"];}
if (isset($_GET["begin_date"]))				{$begin_date=$_GET["begin_date"];}
	elseif (isset($_POST["begin_date"]))		{$begin_date=$_POST["begin_date"];}
if (isset($_GET["end_date"]))				{$end_date=$_GET["end_date"];}
	elseif (isset($_POST["end_date"]))		{$end_date=$_POST["end_date"];}
if (isset($_GET["extension"]))				{$extension=$_GET["extension"];}
	elseif (isset($_POST["extension"]))		{$extension=$_POST["extension"];}
if (isset($_GET["server_ip"]))				{$server_ip=$_GET["server_ip"];}
	elseif (isset($_POST["server_ip"]))		{$server_ip=$_POST["server_ip"];}
if (isset($_GET["user"]))				{$user=$_GET["user"];}
	elseif (isset($_POST["user"]))		{$user=$_POST["user"];}
if (isset($_GET["full_name"]))				{$full_name=$_GET["full_name"];}
	elseif (isset($_POST["full_name"]))		{$full_name=$_POST["full_name"];}
if (isset($_GET["submit"]))				{$submit=$_GET["submit"];}
	elseif (isset($_POST["submit"]))		{$submit=$_POST["submit"];}
if (isset($_GET["SUBMIT"]))				{$SUBMIT=$_GET["SUBMIT"];}
	elseif (isset($_POST["SUBMIT"]))		{$SUBMIT=$_POST["SUBMIT"];}

$PHP_AUTH_USER = ereg_replace("[^0-9a-zA-Z]","",$PHP_AUTH_USER);
$PHP_AUTH_PW = ereg_replace("[^0-9a-zA-Z]","",$PHP_AUTH_PW);

$STARTtime = date("U");
$TODAY = date("Y-m-d");
$admin_page = './admin.php';

if (!isset($begin_date)) {$begin_date = $TODAY;}
if (!isset($end_date)) {$end_date = $TODAY;}

	$stmt="SELECT count(*) from vicidial_users where user='$PHP_AUTH_USER' and pass='$PHP_AUTH_PW' and user_level > 7 and view_reports='1';";
	$rslt=mysql_query($stmt, $link);
	$row=mysql_fetch_row($rslt);
	$auth=$row[0];

$fp = fopen ("./project_auth_entries.txt", "a");
$date = date("r");
$ip = getenv("REMOTE_ADDR");
$browser = getenv("HTTP_USER_AGENT");

  if( (strlen($PHP_AUTH_USER)<2) or (strlen($PHP_AUTH_PW)<2) or (!$auth))
	{
    Header("WWW-Authenticate: Basic realm=\"VICI-ASTERISK\"");
    Header("HTTP/1.0 401 Unauthorized");
    echo "Invalid Username/Password: |$PHP_AUTH_USER|$PHP_AUTH_PW|\n";
    exit;
	}
  else
	{

	if($auth>0)
		{
		$office_no=strtoupper($PHP_AUTH_USER);
		$password=strtoupper($PHP_AUTH_PW);
			$stmt="SELECT full_name from vicidial_users where user='$PHP_AUTH_USER' and pass='$PHP_AUTH_PW'";
			$rslt=mysql_query($stmt, $link);
			$row=mysql_fetch_row($rslt);
			$LOGfullname=$row[0];
		fwrite ($fp, "ASTERISK|GOOD|$date|$PHP_AUTH_USER|$PHP_AUTH_PW|$ip|$browser|$LOGfullname|\n");
		fclose($fp);

		##### get server listing for dynamic pulldown
		$stmt="SELECT fullname from phones where server_ip='$server_ip' and extension='$extension'";
		$rsltx=mysql_query($stmt, $link);
		$rowx=mysql_fetch_row($rsltx);
		$fullname = $row[0];
		}
	else
		{
		fwrite ($fp, "ASTERISK|FAIL|$date|$PHP_AUTH_USER|$PHP_AUTH_PW|$ip|$browser|\n");
		fclose($fp);
		}
	}

?>
<html>
<head>
<title>VICIDIAL ADMIN: Phone Stats</title>
</head>
<BODY BGCOLOR=white marginheight=0 marginwidth=0 leftmargin=0 topmargin=0>
<CENTER>
<TABLE WIDTH=620 BGCOLOR=#D9E6FE cellpadding=2 cellspacing=0><TR BGCOLOR=#015B91><TD ALIGN=LEFT><FONT FACE="ARIAL,HELVETICA" COLOR=WHITE SIZE=2><B> &nbsp; VICIDIAL ADMIN: Administration</TD><TD ALIGN=RIGHT><FONT FACE="ARIAL,HELVETICA" COLOR=WHITE SIZE=2><B><?php echo date("l F j, Y G:i:s A") ?> &nbsp; </TD></TR>
<TR BGCOLOR=#F0F5FE><TD ALIGN=LEFT COLSPAN=2><FONT FACE="ARIAL,HELVETICA" COLOR=BLACK SIZE=1><B> &nbsp; <a href="<?php echo $admin_page ?>?ADD=10000000000"><FONT FACE="ARIAL,HELVETICA" COLOR=BLACK SIZE=1>LIST ALL PHONES</a> | <a href="<?php echo $admin_page ?>?ADD=11111111111"><FONT FACE="ARIAL,HELVETICA" COLOR=BLACK SIZE=1>ADD A NEW PHONE</a> | <a href="<?php echo $admin_page ?>?ADD=551"><FONT FACE="ARIAL,HELVETICA" COLOR=BLACK SIZE=1>SEARCH FOR A PHONE</a> | <a href="<?php echo $admin_page ?>?ADD=111111111111"><FONT FACE="ARIAL,HELVETICA" COLOR=BLACK SIZE=1>ADD A SERVER</a> | <a href="<?php echo $admin_page ?>?ADD=100000000000"><FONT FACE="ARIAL,HELVETICA" COLOR=BLACK SIZE=1>LIST ALL SERVERS</a></TD></TR>
<TR BGCOLOR=#F0F5FE><TD ALIGN=LEFT COLSPAN=2><FONT FACE="ARIAL,HELVETICA" COLOR=BLACK SIZE=1><B> &nbsp; <a href="<?php echo $admin_page ?>?ADD=1000000000000"><FONT FACE="ARIAL,HELVETICA" COLOR=BLACK SIZE=1>SHOW ALL CONFERENCES</a> | <a href="<?php echo $admin_page ?>?ADD=1111111111111"><FONT FACE="ARIAL,HELVETICA" COLOR=BLACK SIZE=1>ADD A NEW CONFERENCE</a></TD></TR>




<?php 

echo "<TR BGCOLOR=\"#F0F5FE\"><TD ALIGN=LEFT COLSPAN=2><FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2><B> &nbsp; \n";

echo "<form action=$PHP_SELF method=POST>\n";
echo "<input type=hidden name=extension value=\"$extension\">\n";
echo "<input type=hidden name=server_ip value=\"$server_ip\">\n";
echo "<input type=text name=begin_date value=\"$begin_date\" size=10 maxsize=10> to \n";
echo "<input type=text name=end_date value=\"$end_date\" size=10 maxsize=10> &nbsp;\n";
echo "<input type=submit name=submit value=submit>\n";


echo " &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; $user - $full_name\n";

echo "</B></TD></TR>\n";
echo "<TR><TD ALIGN=LEFT COLSPAN=2>\n";


	$stmt="SELECT count(*),channel_group, sum(length_in_sec) from call_log where extension='" . mysql_real_escape_string($extension) . "' and server_ip='" . mysql_real_escape_string($server_ip) . "' and start_time >= '" . mysql_real_escape_string($begin_date) . " 0:00:01'  and start_time <= '" . mysql_real_escape_string($end_date) . " 23:59:59' group by channel_group order by channel_group";
	$rslt=mysql_query($stmt, $link);
	$statuses_to_print = mysql_num_rows($rslt);
#	echo "|$stmt|\n";

echo "<br><center>\n";

echo "<B>CALL TIME AND CHANNELS:</B>\n";

echo "<center><TABLE width=300 cellspacing=0 cellpadding=1>\n";
echo "<tr><td><font size=2>CHANNEL GROUP </td><td align=right><font size=2>COUNT</td><td align=right><font size=2> HOURS:MINUTES</td></tr>\n";

	$total_calls=0;
	$o=0;
	while ($statuses_to_print > $o) {
		$row=mysql_fetch_row($rslt);
		if (eregi("1$|3$|5$|7$|9$", $o))
			{$bgcolor='bgcolor="#B9CBFD"';} 
		else
			{$bgcolor='bgcolor="#9BB9FB"';}

		$call_seconds = $row[2];
		$call_hours = ($call_seconds / 3600);
		$call_hours = round($call_hours, 2);
		$call_hours_int = intval("$call_hours");
		$call_minutes = ($call_hours - $call_hours_int);
		$call_minutes = ($call_minutes * 60);
		$call_minutes_int = round($call_minutes, 0);
		if ($call_minutes_int < 10) {$call_minutes_int = "0$call_minutes_int";}

		echo "<tr $bgcolor><td><font size=2>$row[1]</td>";
		echo "<td align=right><font size=2> $row[0]</td>\n";
		echo "<td align=right><font size=2> $call_hours_int:$call_minutes_int</td></tr>\n";
		$total_calls = ($total_calls + $row[0]);

		$call_seconds=0;
		$o++;
	}

	$stmt="SELECT sum(length_in_sec) from call_log where extension='" . mysql_real_escape_string($extension) . "' and server_ip='" . mysql_real_escape_string($server_ip) . "' and start_time >= '" . mysql_real_escape_string($begin_date) . " 0:00:01'  and start_time <= '" . mysql_real_escape_string($end_date) . " 23:59:59'";
	$rslt=mysql_query($stmt, $link);
	$counts_to_print = mysql_num_rows($rslt);
		$row=mysql_fetch_row($rslt);
	$call_seconds = $row[0];
	$call_hours = ($call_seconds / 3600);
	$call_hours = round($call_hours, 2);
	$call_hours_int = intval("$call_hours");
	$call_minutes = ($call_hours - $call_hours_int);
	$call_minutes = ($call_minutes * 60);
	$call_minutes_int = round($call_minutes, 0);
	if ($call_minutes_int < 10) {$call_minutes_int = "0$call_minutes_int";}
#	echo "|$stmt|\n";

echo "<tr><td><font size=2>TOTAL CALLS </td><td align=right><font size=2> $total_calls</td><td align=right><font size=2> $call_hours_int:$call_minutes_int</td></tr>\n";
echo "</TABLE></center>\n";
echo "<br><br>\n";

echo "<center>\n";

echo "<B>LAST 1000 CALLS FOR DATE RANGE:</B>\n";
echo "<TABLE width=400 cellspacing=0 cellpadding=1>\n";
echo "<tr><td><font size=2>NUMBER </td><td><font size=2>CHANNEL GROUP </td><td align=right><font size=2> DATE</td><td align=right><font size=2> LENGTH(MIN.)</td></tr>\n";

	$stmt="SELECT number_dialed,channel_group,start_time,length_in_min from call_log where extension='" . mysql_real_escape_string($extension) . "' and server_ip='" . mysql_real_escape_string($server_ip) . "' and start_time >= '" . mysql_real_escape_string($begin_date) . " 0:00:01'  and start_time <= '" . mysql_real_escape_string($end_date) . " 23:59:59' LIMIT 1000";
	$rslt=mysql_query($stmt, $link);
	$events_to_print = mysql_num_rows($rslt);
#	echo "|$stmt|\n";

	$total_calls=0;
	$o=0;
	$event_start_seconds='';
	$event_stop_seconds='';
	while ($events_to_print > $o) {
		$row=mysql_fetch_row($rslt);
		if (eregi("1$|3$|5$|7$|9$", $o))
			{$bgcolor='bgcolor="#B9CBFD"';} 
		else
			{$bgcolor='bgcolor="#9BB9FB"';}
			echo "<tr $bgcolor><td><font size=2>$row[0]</td>";
			echo "<td align=right><font size=2> $row[1]</td>\n";
			echo "<td align=right><font size=2> $row[2]</td>\n";
			echo "<td align=right><font size=2> $row[3]</td></tr>\n";


		$call_seconds=0;
		$o++;
	}


echo "</TABLE></center>\n";


$ENDtime = date("U");

$RUNtime = ($ENDtime - $STARTtime);

echo "\n\n\n<br><br><br>\n\n";


echo "<font size=0>\n\n\n<br><br><br>\nscript runtime: $RUNtime seconds</font>";


?>


</TD></TR><TABLE>
</body>
</html>

<?php
	
exit; 



?>





