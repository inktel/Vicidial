<?php
# callcard_report_export.php
# 
# displays options to select for downloading of callcard logs selecting by run,
# batch, pack and date range. 
# downloads to a flat text file that is tab delimited
#
# Copyright (C) 2010  Matt Florell <vicidial@gmail.com>    LICENSE: AGPLv2
#
# CHANGES
#
# 100312-2127 - First build
#

require("dbconnect.php");

$PHP_AUTH_USER=$_SERVER['PHP_AUTH_USER'];
$PHP_AUTH_PW=$_SERVER['PHP_AUTH_PW'];
$PHP_SELF=$_SERVER['PHP_SELF'];
if (isset($_GET["query_date"]))				{$query_date=$_GET["query_date"];}
	elseif (isset($_POST["query_date"]))	{$query_date=$_POST["query_date"];}
if (isset($_GET["end_date"]))				{$end_date=$_GET["end_date"];}
	elseif (isset($_POST["end_date"]))		{$end_date=$_POST["end_date"];}
if (isset($_GET["run"]))					{$run=$_GET["run"];}
	elseif (isset($_POST["run"]))			{$run=$_POST["run"];}
if (isset($_GET["batch"]))					{$batch=$_GET["batch"];}
	elseif (isset($_POST["batch"]))			{$batch=$_POST["batch"];}
if (isset($_GET["pack"]))					{$pack=$_GET["pack"];}
	elseif (isset($_POST["pack"]))			{$pack=$_POST["pack"];}
if (isset($_GET["agent"]))					{$agent=$_GET["agent"];}
	elseif (isset($_POST["agent"]))			{$agent=$_POST["agent"];}
if (isset($_GET["did"]))					{$did=$_GET["did"];}
	elseif (isset($_POST["did"]))			{$did=$_POST["did"];}
if (isset($_GET["callerid"]))				{$callerid=$_GET["callerid"];}
	elseif (isset($_POST["callerid"]))		{$callerid=$_POST["callerid"];}
if (isset($_GET["DB"]))						{$DB=$_GET["DB"];}
	elseif (isset($_POST["DB"]))			{$DB=$_POST["DB"];}
if (isset($_GET["run_export"]))				{$run_export=$_GET["run_export"];}
	elseif (isset($_POST["run_export"]))	{$run_export=$_POST["run_export"];}
if (isset($_GET["submit"]))					{$submit=$_GET["submit"];}
	elseif (isset($_POST["submit"]))		{$submit=$_POST["submit"];}
if (isset($_GET["SUBMIT"]))					{$SUBMIT=$_GET["SUBMIT"];}
	elseif (isset($_POST["SUBMIT"]))		{$SUBMIT=$_POST["SUBMIT"];}

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
	$non_latin =	$row[0];
	}
##### END SETTINGS LOOKUP #####
###########################################

$PHP_AUTH_USER = ereg_replace("[^-_0-9a-zA-Z]","",$PHP_AUTH_USER);
$PHP_AUTH_PW = ereg_replace("[^-_0-9a-zA-Z]","",$PHP_AUTH_PW);

$stmt="SELECT count(*) from vicidial_users where user='$PHP_AUTH_USER' and pass='$PHP_AUTH_PW' and user_level > 7 and callcard_admin='1';";
if ($DB) {echo "|$stmt|\n";}
if ($non_latin > 0) { $rslt=mysql_query("SET NAMES 'UTF8'");}
$rslt=mysql_query($stmt, $link);
$row=mysql_fetch_row($rslt);
$auth=$row[0];

if( (strlen($PHP_AUTH_USER)<2) or (strlen($PHP_AUTH_PW)<2) or (!$auth))
	{
#	Header("WWW-Authenticate: Basic realm=\"VICI-PROJECTS\"");
#	Header("HTTP/1.0 401 Unauthorized");
    echo "Invalid Username/Password or no CallCard permission: |$PHP_AUTH_USER|\n";
    exit;
	}


##### START RUN THE EXPORT AND OUTPUT FLAT DATA FILE #####
if ($run_export > 0)
	{
	$US='_';
	$MT[0]='';
	$ip = getenv("REMOTE_ADDR");
	$NOW_DATE = date("Y-m-d");
	$NOW_TIME = date("Y-m-d H:i:s");
	$FILE_TIME = date("Ymd-His");
	$STARTtime = date("U");
	if (!isset($query_date)) {$query_date = $NOW_DATE;}
	if (!isset($end_date)) {$end_date = $NOW_DATE;}

	$inbound_to_print=0;
	$export_rows='';
	$k=0;

	if (strlen($card_id) > 1)
		{$searchSQL = "and card_id='$card_id'";}
	if (strlen($run) > 1)
		{
		$searchSQL .= "and card_id IN(SELECT card_id from callcard_accounts_details where run='$run')";
		}
	if (strlen($batch) > 1)
		{
		$searchSQL .= "and card_id IN(SELECT card_id from callcard_accounts_details where batch='$batch')";
		}
	if (strlen($pack) > 1)
		{
		$searchSQL .= "and card_id IN(SELECT card_id from callcard_accounts_details where pack='$pack')";
		}
	if (strlen($caller_id) > 1)
		{
		$searchSQL .= "and card_id IN(SELECT card_id from callcard_accounts_details where caller_id='$caller_id')";
		}
	if (strlen($agent) > 1)
		{
		$searchSQL .= "and agent='$agent'";
		}
	if (strlen($did) > 1)
		{
		$searchSQL .= "and inbound_did='$did'";
		}
	if (strlen($caller_id) > 1)
		{
		$searchSQL .= "and phone_number='$caller_id'";
		}

	$stmt = "SELECT call_time,phone_number,inbound_did,balance_minutes_start,agent_talk_min,agent_talk_sec,agent,agent_time,agent_dispo FROM callcard_log where call_time >= \"$query_date 00:00:00\" and call_time <= \"$end_date 23:59:59\" $searchSQL order by call_time;";
	$rslt=mysql_query($stmt, $link);
	if ($DB) {echo "$stmt\n";}
	$vt_ct = mysql_num_rows($rslt);
	$i=0;
	while ($vt_ct > $i)
		{
		$row=mysql_fetch_row($rslt);

		$export_rows[$k] = "$row[0]\t$row[1]\t$row[2]\t$row[3]\t$row[4]\t$row[5]\t$row[6]\t$row[7]\t$row[8]";
		$i++;
		$k++;
		$inbound_to_print++;
		}


	if ($inbound_to_print > 0)
		{
		### LOG INSERTION Admin Log Table ###
		$SQL_log = "$stmt|$stmtA|";
		$SQL_log = ereg_replace(';','',$SQL_log);
		$SQL_log = addslashes($SQL_log);
		$stmt="INSERT INTO vicidial_admin_log set event_date='$NOW_TIME', user='$PHP_AUTH_USER', ip_address='$ip', event_section='CALLCARD', event_type='EXPORT', record_id='', event_code='ADMIN EXPORT CALLCARD REPORT', event_sql=\"$SQL_log\", event_notes='';";
		if ($DB) {echo "|$stmt|\n";}
		$rslt=mysql_query($stmt, $link);

		$TXTfilename = "EXPORT_CALLCARD_REPORT_$FILE_TIME.txt";

		// We'll be outputting a TXT file
		header('Content-type: application/octet-stream');

		// It will be called LIST_101_20090209-121212.txt
		header("Content-Disposition: attachment; filename=\"$TXTfilename\"");
		header('Expires: 0');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Pragma: public');
		ob_clean();
		flush();

		$i=0;
		while ($k > $i)
			{
			echo "$export_rows[$i]\r\n";
			$i++;
			}
		}
	else
		{
		echo "There are no calls during this time period for these parameters\n";
		exit;
		}
	}
##### END RUN THE EXPORT AND OUTPUT FLAT DATA FILE #####


else
	{
	$NOW_DATE = date("Y-m-d");
	$NOW_TIME = date("Y-m-d H:i:s");
	$STARTtime = date("U");
	if (!isset($group)) {$group = '';}
	if (!isset($query_date)) {$query_date = $NOW_DATE;}
	if (!isset($end_date)) {$end_date = $NOW_DATE;}

	echo "<HTML><HEAD>\n";

	echo "<script language=\"JavaScript\" src=\"calendar_db.js\"></script>\n";
	echo "<link rel=\"stylesheet\" href=\"calendar.css\">\n";

	echo "<META HTTP-EQUIV=\"Content-Type\" CONTENT=\"text/html; charset=utf-8\">\n";
	echo "<TITLE>ADMINISTRATION: Export CallCard Calls Report";

	##### BEGIN Set variables to make header show properly #####
	$ADD =					'0';
	$hh =					'admin';
	$sh =					'cc';
	$LOGast_admin_access =	'1';
	$ADMIN =				'admin.php';
	$page_width='770';
	$section_width='750';
	$header_font_size='3';
	$subheader_font_size='2';
	$subcamp_font_size='2';
	$header_selected_bold='<b>';
	$header_nonselected_bold='';
	$admin_color =		'#FFFF99';
	$admin_font =		'BLACK';
	$admin_color =		'#E6E6E6';
	$cc_color =		'#FFFF99';
	$cc_font =		'BLACK';
	$cc_color =		'#C6C6C6';
	$subcamp_color =	'#C6C6C6';
	##### END Set variables to make header show properly #####

	require("admin_header.php");


	echo "<CENTER><BR>\n";
	echo "<FONT SIZE=3 FACE=\"Arial,Helvetica\"><B>Export CallCard Calls Report</B></FONT><BR><BR>\n";
	echo "<FORM ACTION=\"$PHP_SELF\" METHOD=GET name=vicidial_report id=vicidial_report>\n";
	echo "<INPUT TYPE=HIDDEN NAME=DB VALUE=\"$DB\">";
	echo "<INPUT TYPE=HIDDEN NAME=run_export VALUE=\"1\">";
	echo "<TABLE BORDER=0 CELLSPACING=8><TR><TD ALIGN=LEFT VALIGN=TOP ROWSPAN=3  bgcolor=#B6D3FC>\n";

	echo "<font class=\"select_bold\"><B>Date Range:</B></font><BR><CENTER>\n";
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
	// o_cal.a_tpl.weekstart = 1; // Monday week start
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
	// o_cal.a_tpl.weekstart = 1; // Monday week start
	</script>
	<?php

	echo "</TD><TD ALIGN=LEFT VALIGN=TOP ROWSPAN=2>\n";


	echo "<center><TABLE width=400 cellspacing=3>\n";
	echo "<tr bgcolor=#B6D3FC><td align=right>Card ID: </td><td align=left><input type=text name=card_id size=20 maxlength=20></td></tr>\n";
	echo "<tr bgcolor=#B6D3FC><td align=right>Agent: </td><td align=left><input type=text name=agent size=20 maxlength=20></td></tr>\n";
	echo "<tr bgcolor=#B6D3FC><td align=right>DID: </td><td align=left><input type=text name=did size=18 maxlength=18></td></tr>\n";
	echo "<tr bgcolor=#B6D3FC><td align=right>CallerID: </td><td align=left><input type=text name=callerid size=18 maxlength=18></td></tr>\n";
	echo "<tr bgcolor=#B6D3FC><td align=right>Run: </td><td align=left><input type=text name=run size=5 maxlength=4></td></tr>\n";
	echo "<tr bgcolor=#B6D3FC><td align=right>Batch: </td><td align=left><input type=text name=batch size=6 maxlength=5></td></tr>\n";
	echo "<tr bgcolor=#B6D3FC><td align=right>Sequence: </td><td align=left><input type=text name=sequence size=6 maxlength=5></td></tr>\n";

	echo "</TABLE></center>\n";


	echo "</TD></TR><TR></TD><TD ALIGN=LEFT VALIGN=TOP COLSPAN=3>\n";
	echo "<INPUT TYPE=SUBMIT NAME=SUBMIT VALUE=SUBMIT>\n";
	echo "</TD></TR></TABLE>\n";
	echo "</FORM>\n\n";
	}
exit;

?>