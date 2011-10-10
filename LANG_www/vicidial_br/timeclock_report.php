<?php 
# timeclock_report.php
# 
# Copyright (C) 2010  Matt Florell <vicidial@gmail.com>    LICENSE: AGPLv2
#
# CHANGES
#
# 80529-0055 - First build
# 80617-1416 - Fixed totals tally bug
# 80707-0754 - Fixed groups bug, changed formatting
# 90310-2059 - Added admin header
# 90508-0644 - Changed to PHP long tags
# 100214-1421 - Sort menu alphabetically
# 100216-0042 - Added popup date selector
#

require("dbconnect.php");

##### Pull values from posted form variables #####
$PHP_AUTH_USER=$_SERVER['PHP_AUTH_USER'];
$PHP_AUTH_PW=$_SERVER['PHP_AUTH_PW'];
$PHP_SELF=$_SERVER['PHP_SELF'];
if (isset($_GET["query_date"]))				{$query_date=$_GET["query_date"];}
	elseif (isset($_POST["query_date"]))	{$query_date=$_POST["query_date"];}
if (isset($_GET["end_date"]))				{$end_date=$_GET["end_date"];}
	elseif (isset($_POST["end_date"]))		{$end_date=$_POST["end_date"];}
if (isset($_GET["user_group"]))				{$user_group=$_GET["user_group"];}
	elseif (isset($_POST["user_group"]))	{$user_group=$_POST["user_group"];}
if (isset($_GET["shift"]))				{$shift=$_GET["shift"];}
	elseif (isset($_POST["shift"]))		{$shift=$_POST["shift"];}
if (isset($_GET["order"]))				{$order=$_GET["order"];}
	elseif (isset($_POST["order"]))		{$order=$_POST["order"];}
if (isset($_GET["user"]))				{$user=$_GET["user"];}
	elseif (isset($_POST["user"]))		{$user=$_POST["user"];}
if (isset($_GET["DB"]))					{$DB=$_GET["DB"];}
	elseif (isset($_POST["DB"]))		{$DB=$_POST["DB"];}
if (isset($_GET["submit"]))				{$submit=$_GET["submit"];}
	elseif (isset($_POST["submit"]))	{$submit=$_POST["submit"];}
if (isset($_GET["ENVIAR"]))				{$ENVIAR=$_GET["ENVIAR"];}
	elseif (isset($_POST["ENVIAR"]))	{$ENVIAR=$_POST["ENVIAR"];}

if (strlen($shift)<2) {$shift='ALL';}
if (strlen($order)<2) {$order='hours_down';}

#############################################
##### START SYSTEM_SETTINGS LOOKUP #####
$stmt = "SELECT use_non_latin,webroot_writable,outbound_autodial_active FROM system_settings;";
$rslt=mysql_query($stmt, $link);
if ($DB) {echo "$stmt\n";}
$qm_conf_ct = mysql_num_rows($rslt);
if ($qm_conf_ct > 0)
	{
	$row=mysql_fetch_row($rslt);
	$non_latin =					$row[0];
	$webroot_writable =				$row[1];
	$SSoutbound_autodial_active =	$row[2];
	}
##### END SETTINGS LOOKUP #####
###########################################

$PHP_AUTH_USER = ereg_replace("[^0-9a-zA-Z]","",$PHP_AUTH_USER);
$PHP_AUTH_PW = ereg_replace("[^0-9a-zA-Z]","",$PHP_AUTH_PW);

$stmt="SELECT count(*) from vicidial_users where user='$PHP_AUTH_USER' and pass='$PHP_AUTH_PW' and user_level > 6 and view_reports='1';";
if ($DB) {echo "|$stmt|\n";}
if ($non_latin > 0) { $rslt=mysql_query("SET NAMES 'UTF8'");}
$rslt=mysql_query($stmt, $link);
$row=mysql_fetch_row($rslt);
$auth=$row[0];

if( (strlen($PHP_AUTH_USER)<2) or (strlen($PHP_AUTH_PW)<2) or (!$auth))
	{
    Header("WWW-Authenticate: Basic realm=\"VICI-PROJECTS\"");
    Header("HTTP/1.0 401 Unauthorized");
    echo "Nome ou Senha inválidos: |$PHP_AUTH_USER|$PHP_AUTH_PW|\n";
    exit;
	}

$NOW_DATE = date("Y-m-d");
$NOW_TIME = date("Y-m-d H:i:s");
$STARTtime = date("U");
if (!isset($group)) {$group = '';}
if (!isset($query_date)) {$query_date = $NOW_DATE;}
if (!isset($end_date)) {$end_date = $NOW_DATE;}

$stmt="select user_group from vicidial_user_groups order by user_group;";
$rslt=mysql_query($stmt, $link);
if ($DB) {echo "$stmt\n";}
$user_groups_to_print = mysql_num_rows($rslt);
$i=0;
	$LISTuser_groups[$i]='---ALL---';
	$i++;
	$user_groups_to_print++;
while ($i < $user_groups_to_print)
	{
	$row=mysql_fetch_row($rslt);
	$LISTuser_groups[$i] =$row[0];
	$i++;
	}

##### START HTML #####
?>

<HTML>
<HEAD>

<style type="text/css">
<!--
	div.scroll_callback {height: 300px; width: 620px; overflow: scroll;}
	div.scroll_list {height: 400px; width: 140px; overflow: scroll;}
	div.scroll_script {height: 331px; width: 600px; background: #FFF5EC; overflow: scroll; font-size: 12px;  font-family: sans-serif;}
	div.text_input {overflow: auto; font-size: 10px;  font-family: sans-serif;}
   .body_text {font-size: 13px;  font-family: sans-serif;}
   .preview_text {font-size: 13px;  font-family: sans-serif; background: #CCFFCC}
   .preview_text_red {font-size: 13px;  font-family: sans-serif; background: #FFCCCC}
   .body_small {font-size: 11px;  font-family: sans-serif;}
   .body_tiny {font-size: 10px;  font-family: sans-serif;}
   .log_text {font-size: 11px;  font-family: monospace;}
   .log_text_red {font-size: 11px;  font-family: monospace; font-weight: bold; background: #FF3333}
   .sd_text {font-size: 16px;  font-family: sans-serif; font-weight: bold;}
   .sh_text {font-size: 14px;  font-family: sans-serif; font-weight: bold;}
   .sb_text {font-size: 12px;  font-family: sans-serif;}
   .sk_text {font-size: 11px;  font-family: sans-serif;}
   .skb_text {font-size: 13px;  font-family: sans-serif; font-weight: bold;}
   .ON_conf {font-size: 11px;  font-family: monospace; color: black; background: #FFFF99}
   .OFF_conf {font-size: 11px;  font-family: monospace; color: black; background: #FFCC77}
   .cust_form {font-family: sans-serif; font-size: 10px; overflow: auto}

   .select_bold {font-size: 14px;  font-family: sans-serif; font-weight: bold;}
   .header_white {font-size: 14px;  font-family: sans-serif; font-weight: bold; color: white}
   .data_records {font-size: 12px;  font-family: sans-serif; color: black}
   .data_records_fix {font-size: 12px;  font-family: monospace; color: black}
   .data_records_fix_small {font-size: 9px;  font-family: monospace; color: black}

-->
</style>

<script language="JavaScript" src="calendar_db.js"></script>
<link rel="stylesheet" href="calendar.css">

<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=utf-8">
<TITLE>UsuárioRelógio Ponto Report

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



$user_group_ct = count($user_group);
$user_group_string='|';

$i=0;
while($i < $user_group_ct)
	{
	$user_group_string .= "$user_group[$i]|";
	$user_group_SQL .= "'$user_group[$i]',";
	$i++;
	}
if ( (ereg("--ALL--",$user_group_string) ) or ($user_group_ct < 1) )
	{
	$user_group_SQL = "";
	}
else
	{
	$user_group_SQL = eregi_replace(",$",'',$user_group_SQL);
	$user_group_SQL = "and vicidial_timeclock_log.user_group IN($user_group_SQL)";
	}

if ($DB > 0)
	{
	echo "<BR>\n";
	echo "$user_group_ct|$user_group_string|$user_group_SQL\n";
	echo "<BR>\n";
	}

echo "<CENTER>\n";
echo "<FORM ACTION=\"$PHP_SELF\" METHOD=GET name=vicidial_report id=vicidial_report>\n";
echo "<INPUT TYPE=HIDDEN NAME=DB VALUE=\"$DB\">";
echo "<TABLE Border=0 CELLSPACING=6><TR><TD ALIGN=LEFT VALIGN=TOP>\n";

echo "<font class=\"select_bold\"><B>Período:</B></font><BR><CENTER>\n";
echo "<INPUT TYPE=TEXT NAME=query_date SIZE=10 MAXLENGTH=10 VALUE=\"$query_date\">";

?>
<script language="JavaScript">
var o_cal = new tcal ({
	// form name
	'formname': 'vicidial_report',
	// input name
	'controlname': 'query_date'
});
o_cal.a_tpl.yearscroll = false;
// o_cal.a_tpl.weekstart = 1; // Segunda week start
</script>
<?php

echo "<BR>to<BR>\n";
echo "<INPUT TYPE=TEXT NAME=end_date SIZE=10 MAXLENGTH=10 VALUE=\"$end_date\">";

?>
<script language="JavaScript">
var o_cal = new tcal ({
	// form name
	'formname': 'vicidial_report',
	// input name
	'controlname': 'end_date'
});
o_cal.a_tpl.yearscroll = false;
// o_cal.a_tpl.weekstart = 1; // Segunda week start
</script>
<?php

echo "</TD><TD ALIGN=LEFT VALIGN=TOP>\n";
echo "<font class=\"select_bold\"><B>Grupos de Usuário:</B></font><BR><CENTER>\n";
echo "<SELECT SIZE=5 NAME=user_group[] multiple>\n";
	$o=0;
	while ($user_groups_to_print > $o)
	{
		if (ereg("\|$LISTuser_groups[$o]\|",$user_group_string)) 
			{echo "<option selected value=\"$LISTuser_groups[$o]\">$LISTuser_groups[$o]</option>\n";}
		else 
			{echo "<option value=\"$LISTuser_groups[$o]\">$LISTuser_groups[$o]</option>\n";}
		$o++;
	}
echo "</SELECT>\n";

echo "</TD></TD><TD ALIGN=LEFT VALIGN=TOP>\n";
echo "<font class=\"select_bold\"><B>Order:</B></font><BR>\n";
echo "<SELECT SIZE=1 NAME=order>\n";
echo "<option selected value=\"$order\">$order</option>\n";
echo "<option value=\"\">--</option>\n";
echo "<option>hours_up</option>\n";
echo "<option>hours_down</option>\n";
echo "<option>user_up</option>\n";
echo "<option>user_down</option>\n";
echo "<option>name_up</option>\n";
echo "<option>name_down</option>\n";
echo "<option>group_up</option>\n";
echo "<option>group_down</option>\n";
echo "</SELECT><BR><CENTER>\n";

echo "</TD><TD ALIGN=LEFT VALIGN=TOP>\n";
echo "<font class=\"select_bold\"><B>Usuário:</B></font><BR>\n";
echo "<INPUT TYPE=text NAME=user SIZE=7 MAXLENGTH=20 VALUE=\"$user\">\n";

echo "<BR><BR><INPUT TYPE=Submit NAME=ENVIAR VALUE=ENVIAR>\n";
echo "</TD></TD><TD ALIGN=LEFT VALIGN=TOP>\n";
echo "</TD><TD ALIGN=CENTER VALIGN=TOP ROWSPAN=3>\n";
echo "<FONT class=\"select_bold\" COLOR=BLACK SIZE=2> &nbsp; &nbsp; <a href=\"./admin.php?ADD=999999\">RELATÓRIOS</a> </FONT>\n";

echo "</TD></TR></TABLE>\n";
echo "</FORM>\n\n";

echo "<PRE><FONT SIZE=3>\n";


echo "UsuárioRelógio Ponto Report                        $NOW_TIME\n";

echo "Time range: $query_date to $end_date\n\n";
echo "---------- USER TIMECLOCK DETAILS -------------\n";
echo "These totals do NOT include any active sessions\n</PRE>\n";

echo "<TABLE Border=0 CELLSPACING=1 CELLPADDING=3><TR BGCOLOR=BLACK>\n";
echo "<TD ALIGN=CENTER><FONT class=\"header_white\">#</TD>\n";
echo "<TD ALIGN=CENTER><FONT class=\"header_white\">&nbsp; USER &nbsp;</TD>\n";
echo "<TD ALIGN=CENTER><FONT class=\"header_white\">&nbsp; NAME &nbsp;</TD>\n";
echo "<TD ALIGN=CENTER><FONT class=\"header_white\">&nbsp; GROUP &nbsp;</TD>\n";
echo "<TD ALIGN=CENTER><FONT class=\"header_white\">&nbsp; HOURS &nbsp;</TD>\n";
echo "</TR>\n";

$order_SQL='';
if ($order == 'hours_up')	{$order_SQL = "order by login";}
if ($order == 'hours_down') {$order_SQL = "order by login desc";}
if ($order == 'user_up')	{$order_SQL = "order by vicidial_users.user";}
if ($order == 'user_down')	{$order_SQL = "order by vicidial_users.user desc";}
if ($order == 'name_up')	{$order_SQL = "order by full_name";}
if ($order == 'name_down')	{$order_SQL = "order by full_name desc";}
if ($order == 'group_up')	{$order_SQL = "order by vicidial_timeclock_log.user_group";}
if ($order == 'group_down')	{$order_SQL = "order by vicidial_timeclock_log.user_group desc";}

if (strlen($user) > 0)		{$user_SQL = "and vicidial_timeclock_log.user='$user'";}
else {$user_SQL='';}

$stmt="select vicidial_users.user,full_name,sum(login_sec) as login,vicidial_timeclock_log.user_group from vicidial_users,vicidial_timeclock_log where event IN('LOGIN','START') and event_date >= '$query_date 00:00:00' and event_date <= '$end_date 23:59:59' and vicidial_users.user=vicidial_timeclock_log.user $user_SQL $user_group_SQL group by vicidial_users.user,vicidial_timeclock_log.user_group $order_SQL limit 100000;";
$rslt=mysql_query($stmt, $link);
if ($DB) {echo "$stmt\n";}
$rows_to_print = mysql_num_rows($rslt);
$i=0;
while ($i < $rows_to_print)
	{
	$dbHOURS=0;
	$row=mysql_fetch_row($rslt);
	$user_id[$i] =		$row[0];
	$full_name[$i] =	$row[1];
	$login_sec[$i] =	$row[2];	$TOTlogin_sec = ($TOTlogin_sec + $row[2]);
	$u_group[$i] =		$row[3];

	if ($login_sec[$i] > 0)
		{
		$dbHOURS = ($login_sec[$i] / 3600);
		$dbHOURS = round($dbHOURS, 2);
		$dbHOURS = sprintf("%01.2f", $dbHOURS);
		}
	else
		{$dbHOURS='0.00';}

	$hours[$i] =	$dbHOURS;		
	$hoursSORT[$i] =	"$dbHOURS-----$i";		

	$i++;
	}


$j=0;
while ($j < $rows_to_print)
	{

	$hours_split = explode("-----",$hoursSORT[$j]);
	$i = $hours_split[1];

	if (eregi("1$|3$|5$|7$|9$", $j))
		{$bgcolor='bgcolor="#B9CBFD"';} 
	else
		{$bgcolor='bgcolor="#9BB9FB"';}

	echo "<TR $bgcolor>\n";
	echo "<TD ALIGN=LEFT><FONT class=\"data_records_fix_small\">$j</TD>\n";
	echo "<TD><FONT class=\"data_records\"><A HREF=\"user_status.php?user=$user_id[$i]\">$user_id[$i]</A> </TD>\n";
	echo "<TD><FONT class=\"data_records\">$full_name[$i] </TD>\n";
	echo "<TD><FONT class=\"data_records\">$u_group[$i] </TD>\n";
	echo "<TD ALIGN=RIGHT><FONT class=\"data_records_fix\"> $hours[$i]</TD>\n";
	echo "</TR>\n";

	$j++;
	}


if ($TOTlogin_sec > 0)
	{
	$TOTdbHOURS = ($TOTlogin_sec / 3600);
	$TOTdbHOURS = round($TOTdbHOURS, 0);
	$TOTdbHOURS = sprintf("%01.0f", $TOTdbHOURS);
	}
else
	{$TOTdbHOURS='0.00';}

$TOThours =	$TOTdbHOURS;		


echo "<TR BGCOLOR=#E6E6E6>\n";
echo "<TD ALIGN=LEFT COLSPAN=4><FONT class=\"data_records\">TOTALS</TD>\n";
echo "<TD ALIGN=RIGHT><FONT class=\"data_records_fix\"> $TOThours</TD>\n";
echo "</TR>\n";

echo "</TABLE>\n";

echo "\n";

/*
	$TOTavgWAIT_M = ( ($TOTtotWAIT / $TOTcalls) / 60);
	$TOTavgWAIT_M = round($TOTavgWAIT_M, 2);
	$TOTavgWAIT_M_int = intval("$TOTavgWAIT_M");
	$TOTavgWAIT_S = ($TOTavgWAIT_M - $TOTavgWAIT_M_int);
	$TOTavgWAIT_S = ($TOTavgWAIT_S * 60);
	$TOTavgWAIT_S = round($TOTavgWAIT_S, 0);
	if ($TOTavgWAIT_S < 10) {$TOTavgWAIT_S = "0$TOTavgWAIT_S";}
	$TOTavgWAIT_MS = "$TOTavgWAIT_M_int:$TOTavgWAIT_S";
	$TOTavgWAIT_MS =		sprintf("%6s", $TOTavgWAIT_MS);
		while(strlen($TOTavgWAIT_MS)>6) {$TOTavgWAIT_MS = substr("$TOTavgWAIT_MS", 0, -1);}
*/
?>
</CENTER>
</BODY></HTML>
