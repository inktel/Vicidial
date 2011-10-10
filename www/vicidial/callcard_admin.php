<?php
# callcard_admin.php
# 
# Copyright (C) 2010  Matt Florell <vicidial@gmail.com>    LICENSE: AGPLv2
#
# This callcard script is to administer the callcard accounts in ViciDial
# it is separate from the standard admin.php script. callcard_enabled in
# the system_settings table must be active for this script to work.
# 
# CHANGES
# 100311-2325 - first build
# 100525-1824 - Added generate option
# 100616-0847 - Fixed batch issue
# 100823-1342 - Added Search option and display for level 7 users, added pin number search
#

$version = '2.4-4';
$build = '100823-1342';

$MT[0]='';

require("dbconnect.php");

$PHP_SELF=$_SERVER['PHP_SELF'];
if (isset($_GET["action"]))					{$action=$_GET["action"];}
	elseif (isset($_POST["action"]))		{$action=$_POST["action"];}
if (isset($_GET["DB"]))						{$DB=$_GET["DB"];}
	elseif (isset($_POST["DB"]))			{$DB=$_POST["DB"];}
if (isset($_GET["run"]))					{$run=$_GET["run"];}
	elseif (isset($_POST["run"]))			{$run=$_POST["run"];}
if (isset($_GET["batch"]))					{$batch=$_GET["batch"];}
	elseif (isset($_POST["batch"]))			{$batch=$_POST["batch"];}
if (isset($_GET["starting_batch"]))				{$starting_batch=$_GET["starting_batch"];}
	elseif (isset($_POST["starting_batch"]))	{$starting_batch=$_POST["starting_batch"];}
if (isset($_GET["pack"]))					{$pack=$_GET["pack"];}
	elseif (isset($_POST["pack"]))			{$pack=$_POST["pack"];}
if (isset($_GET["sequence"]))				{$sequence=$_GET["sequence"];}
	elseif (isset($_POST["sequence"]))		{$sequence=$_POST["sequence"];}
if (isset($_GET["card_id"]))				{$card_id=$_GET["card_id"];}
	elseif (isset($_POST["card_id"]))		{$card_id=$_POST["card_id"];}
if (isset($_GET["pin"]))					{$pin=$_GET["pin"];}
	elseif (isset($_POST["pin"]))			{$pin=$_POST["pin"];}
if (isset($_GET["status"]))					{$status=$_GET["status"];}
	elseif (isset($_POST["status"]))		{$status=$_POST["status"];}
if (isset($_GET["total"]))					{$total=$_GET["total"];}
	elseif (isset($_POST["total"]))			{$total=$_POST["total"];}
if (isset($_GET["comment"]))				{$comment=$_GET["comment"];}
	elseif (isset($_POST["comment"]))		{$comment=$_POST["comment"];}
if (isset($_GET["balance_minutes"]))			{$balance_minutes=$_GET["balance_minutes"];}
	elseif (isset($_POST["balance_minutes"]))	{$balance_minutes=$_POST["balance_minutes"];}
if (isset($_GET["initial_value"]))			{$initial_value=$_GET["initial_value"];}
	elseif (isset($_POST["initial_value"]))	{$initial_value=$_POST["initial_value"];}
if (isset($_GET["initial_minutes"]))			{$initial_minutes=$_GET["initial_minutes"];}
	elseif (isset($_POST["initial_minutes"]))	{$initial_minutes=$_POST["initial_minutes"];}
if (isset($_GET["note_purchase_order"]))			{$note_purchase_order=$_GET["note_purchase_order"];}
	elseif (isset($_POST["note_purchase_order"]))	{$note_purchase_order=$_POST["note_purchase_order"];}
if (isset($_GET["note_printer"]))			{$note_printer=$_GET["note_printer"];}
	elseif (isset($_POST["note_printer"]))	{$note_printer=$_POST["note_printer"];}
if (isset($_GET["note_did"]))				{$note_did=$_GET["note_did"];}
	elseif (isset($_POST["note_did"]))		{$note_did=$_POST["note_did"];}
if (isset($_GET["inbound_group_id"]))			{$inbound_group_id=$_GET["inbound_group_id"];}
	elseif (isset($_POST["inbound_group_id"]))	{$inbound_group_id=$_POST["inbound_group_id"];}
if (isset($_GET["note_language"]))			{$note_language=$_GET["note_language"];}
	elseif (isset($_POST["note_language"]))	{$note_language=$_POST["note_language"];}
if (isset($_GET["note_name"]))				{$note_name=$_GET["note_name"];}
	elseif (isset($_POST["note_name"]))		{$note_name=$_POST["note_name"];}
if (isset($_GET["note_comments"]))			{$note_comments=$_GET["note_comments"];}
	elseif (isset($_POST["note_comments"]))	{$note_comments=$_POST["note_comments"];}
if (isset($_GET["user"]))					{$user=$_GET["user"];}
	elseif (isset($_POST["user"]))			{$user=$_POST["user"];}
if (isset($_GET["SUBMIT"]))					{$SUBMIT=$_GET["SUBMIT"];}
	elseif (isset($_POST["SUBMIT"]))		{$SUBMIT=$_POST["SUBMIT"];}


header ("Content-type: text/html; charset=utf-8");
header ("Cache-Control: no-cache, must-revalidate");  // HTTP/1.1
header ("Pragma: no-cache");                          // HTTP/1.0

$report_name = 'CallCard Search';
$SEARCHONLY=0;

#############################################
##### START SYSTEM_SETTINGS LOOKUP #####
$stmt = "SELECT use_non_latin,callcard_enabled FROM system_settings;";
$rslt=mysql_query($stmt, $link);
if ($DB) {echo "$stmt\n";}
$ss_conf_ct = mysql_num_rows($rslt);
if ($ss_conf_ct > 0)
	{
	$row=mysql_fetch_row($rslt);
	$non_latin =						$row[0];
	$callcard_enabled =					$row[1];
	}
##### END SETTINGS LOOKUP #####
###########################################


if ($callcard_enabled < 1)
	{
	echo "ERROR: CallCard is not active on this system\n";
	exit;
	}

if ($non_latin < 1)
	{
	### Clean Variable Values ###
	$DB = ereg_replace("[^0-9]","",$DB);
	$action = ereg_replace("[^\_0-9a-zA-Z]","",$action);
	$card_id = ereg_replace("[^-\_0-9]","",$card_id);
	$run = ereg_replace("[^0-9]","",$run);
	$batch = ereg_replace("[^0-9]","",$batch);
	$pack = ereg_replace("[^0-9]","",$pack);
	$sequence = ereg_replace("[^0-9]","",$sequence);
	$territory_description = ereg_replace("[^ -\_\.\,0-9a-zA-Z]","",$territory_description);
	$user = ereg_replace("[^-\_0-9a-zA-Z]","",$user);
	$old_territory = ereg_replace("[^-\_0-9a-zA-Z]","",$old_territory);
	$old_user = ereg_replace("[^-\_0-9a-zA-Z]","",$old_user);
	$accountid = ereg_replace("[^-\_0-9a-zA-Z]","",$accountid);
	}

if (eregi("YES",$batch))
	{
	$USER='batch';
	$PASS='batch';
	}
else
	{
	$USER=$_SERVER['PHP_AUTH_USER'];
	$PASS=$_SERVER['PHP_AUTH_PW'];
	$USER = ereg_replace("[^0-9a-zA-Z]","",$USER);
	$PASS = ereg_replace("[^0-9a-zA-Z]","",$PASS);

	$stmt="SELECT count(*) from vicidial_users where user='$USER' and pass='$PASS' and user_level > 7 and callcard_admin='1' and active='Y';";
	if ($DB) {echo "|$stmt|\n";}
	if ($non_latin > 0) { $rslt=mysql_query("SET NAMES 'UTF8'");}
	$rslt=mysql_query($stmt, $link);
	$row=mysql_fetch_row($rslt);
	$auth=$row[0];

	if( (strlen($USER)<2) or (strlen($PASS)<2) or (!$auth))
		{
		$stmt="SELECT count(*) from vicidial_users where user='$USER' and pass='$PASS' and user_level > 6 and view_reports='1' and active='Y';";
		if ($DB) {echo "|$stmt|\n";}
		$rslt=mysql_query($stmt, $link);
		$row=mysql_fetch_row($rslt);
		$authreport=$row[0];

		if ($authreport > 0)
			{
			$stmt="SELECT full_name,user_group from vicidial_users where user='$USER' and pass='$PASS';";
			$rslt=mysql_query($stmt, $link);
			$row=mysql_fetch_row($rslt);
			$LOGfullname =		$row[0];
			$LOGuser_group =	$row[1];

			$stmt="SELECT allowed_reports from vicidial_user_groups where user_group='$LOGuser_group';";
			if ($DB) {echo "|$stmt|\n";}
			$rslt=mysql_query($stmt, $link);
			$row=mysql_fetch_row($rslt);
			$LOGallowed_reports =	$row[0];

			if ( (!preg_match("/$report_name/",$LOGallowed_reports)) and (!preg_match("/ALL REPORTS/",$LOGallowed_reports)) )
				{
				Header("WWW-Authenticate: Basic realm=\"VICI-PROJECTS\"");
				Header("HTTP/1.0 401 Unauthorized");
				echo "You are not allowed to view this report: |$PHP_AUTH_USER|$report_name|\n";
				exit;
				}
			else
				{
				$SEARCHONLY=1;
				}
			}
		else
			{
			Header("WWW-Authenticate: Basic realm=\"VICI-PROJECTS\"");
			Header("HTTP/1.0 401 Unauthorized");
			echo "Invalid Username/Password: |$USER|$PASS|\n";
			exit;
			}
		}
	}

if ($SEARCHONLY > 0)
	{
	if ( ($action != 'SEARCH') and ($action != 'SEARCH_RESULTS') and ($action != 'CALLCARD_DETAIL') )
		{$action = 'SEARCH';}
	}

if (strlen($action) < 1)
	{$action = 'CALLCARD_SUMMARY';}



?>
<html>
<head>
<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=utf-8">
<!-- VERSION: <?php echo $version ?>     BUILD: <?php echo $build ?> -->
<title>ADMINISTRATION: CallCard Admin
<?php



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

$colspan='3';

?>
<TABLE WIDTH=<?php echo $page_width ?> BGCOLOR=#E6E6E6 cellpadding=2 cellspacing=0>

<?php 

echo "<TR BGCOLOR=\"#F0F5FE\"><TD ALIGN=LEFT COLSPAN=$colspan><FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=3><B> &nbsp; \n";

$STARTtime = date("U");
$TODAY = date("Y-m-d");
$NOW_TIME = date("Y-m-d H:i:s");
$FILE_datetime = $STARTtime;

$ip = getenv("REMOTE_ADDR");
$date = date("r");
$browser = getenv("HTTP_USER_AGENT");
$script_name = getenv("SCRIPT_NAME");
$server_name = getenv("SERVER_NAME");
$server_port = getenv("SERVER_PORT");
if (eregi("443",$server_port)) {$HTTPprotocol = 'https://';}
  else {$HTTPprotocol = 'http://';}
$admDIR = "$HTTPprotocol$server_name:$server_port$script_name";
$admDIR = eregi_replace('audio_store.php','',$admDIR);
$admSCR = 'admin.php';
$NWB = " &nbsp; <a href=\"javascript:openNewWindow('$admDIR$admSCR?ADD=99999";
$NWE = "')\"><IMG SRC=\"help.gif\" WIDTH=20 HEIGHT=20 BORDER=0 ALT=\"HELP\" ALIGN=TOP></A>";

$secX = date("U");
$pulldate0 = "$year-$mon-$mday $hour:$min:$sec";



### BEGIN modify card status page
if ($action == "CALLCARD_STATUS")
	{
	if ( (strlen($card_id)<1) or (strlen($status)<1) )
		{
		echo "ERROR: Card ID and Status must be filled in<BR>\n";
		}
	else
		{
		$stmt="SELECT count(*) from callcard_accounts_details where card_id='$card_id';";
		$rslt=mysql_query($stmt, $link);
		$row=mysql_fetch_row($rslt);
		if ($row[0] < 1)
			{
			echo "ERROR: This card_id is not in the system<BR>\n";
			}
		else
			{
			if ($status == 'RESET')
				{
				$initial_minutes=1;
				$stmt="SELECT initial_minutes FROM callcard_accounts_details where card_id='$card_id';";
				$rslt=mysql_query($stmt, $link);
				if ($DB) {echo "$stmt\n";}
				$im_ct = mysql_num_rows($rslt);
				if ($im_ct > 0)
					{
					$row=mysql_fetch_row($rslt);
					$initial_minutes = $row[0];
					}

				$stmt="UPDATE callcard_accounts_details SET status='ACTIVE',balance_minutes='$initial_minutes',used_time=NOW(),used_user='$USER' where card_id='$card_id';";
				$stmtB="UPDATE callcard_accounts SET status='ACTIVE',balance_minutes='$initial_minutes' where card_id='$card_id';";
				}
			if ($status == 'VOID')
				{
				$stmt="UPDATE callcard_accounts_details SET status='VOID',void_time=NOW(),void_user='$USER' where card_id='$card_id';";
				$stmtB="UPDATE callcard_accounts SET status='VOID' where card_id='$card_id';";
				}
			if ($status == 'ACTIVE')
				{
				$stmt="UPDATE callcard_accounts_details SET status='ACTIVE',activate_time=NOW(),activate_user='$USER' where card_id='$card_id';";
				$stmtB="UPDATE callcard_accounts SET status='ACTIVE' where card_id='$card_id';";
				}
			$rslt=mysql_query($stmt, $link);
			$rslt=mysql_query($stmtB, $link);
			if ($DB) {echo "|$stmt|$stmtB|\n";}

			echo "Card ID Status Modified: $card_id - $status<BR>\n";

			### LOG INSERTION Admin Log Table ###
			$SQL_log = "$stmt|";
			$SQL_log = ereg_replace(';','',$SQL_log);
			$SQL_log = addslashes($SQL_log);
			$stmt="INSERT INTO vicidial_admin_log set event_date='$NOW_TIME', user='$USER', ip_address='$ip', event_section='CALLCARD', event_type='MODIFY', record_id='$card_id', event_code='ADMIN MODIFY CALLCARD', event_sql=\"$SQL_log\", event_notes='$status';";
			if ($DB) {echo "|$stmt|\n";}
			$rslt=mysql_query($stmt, $link);
			}
		}
	$action = "CALLCARD_DETAIL";
	}
### END modify card status page



### BEGIN CallCard record Detail page
if ($action == "CALLCARD_DETAIL")
	{
	$stmt="SELECT card_id,run,batch,pack,sequence,status,balance_minutes,initial_value,initial_minutes,note_purchase_order,note_printer,note_did,inbound_group_id,note_language,note_name,note_comments,create_user,activate_user,used_user,void_user,create_time,activate_time,used_time,void_time from callcard_accounts_details where card_id='$card_id';";
	$rslt=mysql_query($stmt, $link);
	$details_to_print = mysql_num_rows($rslt);
	if ($details_to_print > 0) 
		{
		$rowx=mysql_fetch_row($rslt);
		$card_id =				$rowx[0];
		$run =					$rowx[1];
		$batch =				$rowx[2];
		$pack =					$rowx[3];
		$sequence =				$rowx[4];
		$status =				$rowx[5];
		$balance_minutes =		$rowx[6];
		$initial_value =		$rowx[7];
		$initial_minutes =		$rowx[8];
		$note_purchase_order =	$rowx[9];
		$note_printer =			$rowx[10];
		$note_did =				$rowx[11];
		$inbound_group_id =		$rowx[12];
		$note_language =		$rowx[13];
		$note_name =			$rowx[14];
		$note_comments =		$rowx[15];
		$create_user =			$rowx[16];
		$activate_user =		$rowx[17];
		$used_user =			$rowx[18];
		$void_user =			$rowx[19];
		$create_time =			$rowx[20];
		$activate_time =		$rowx[21];
		$used_time =			$rowx[22];
		$void_time =			$rowx[23];

		$stmt="SELECT pin from callcard_accounts where card_id='$card_id';";
		$rslt=mysql_query($stmt, $link);
		$pin_to_print = mysql_num_rows($rslt);
		if ($pin_to_print > 0) 
			{
			$rowx=mysql_fetch_row($rslt);
			$pin =				$rowx[0];
			}

		echo "<TABLE><TR><TD>\n";
		echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";

		echo "<br>CallCard Detail<form action=$PHP_SELF method=POST>\n";
		echo "<input type=hidden name=action value=PROCESS_MODIFY_CALLCARD>\n";
		echo "<input type=hidden name=card_id value=\"$card_id\">\n";
		echo "<input type=hidden name=DB value=\"$DB\">\n";
		echo "<center><TABLE width=$section_width cellspacing=3>\n";
		echo "<tr bgcolor=#B6D3FC><td align=right>Card ID: </td><td align=left><B>$card_id</B></td></tr>\n";
		echo "<tr bgcolor=#B6D3FC><td align=right>PIN: </td><td align=left><B>$pin</B></td></tr>\n";
		echo "<tr bgcolor=#B6D3FC><td align=right>Status: </td><td align=left><B>$status</B></td></tr>\n";
		echo "<tr bgcolor=#B6D3FC><td align=right>Run: </td><td align=left><B>$run</B></td></tr>\n";
		echo "<tr bgcolor=#B6D3FC><td align=right>Batch: </td><td align=left><B>$batch</B></td></tr>\n";
		echo "<tr bgcolor=#B6D3FC><td align=right>Pack: </td><td align=left><B>$pack</B></td></tr>\n";
		echo "<tr bgcolor=#B6D3FC><td align=right>Sequence: </td><td align=left><B>$sequence</B></td></tr>\n";
		echo "<tr bgcolor=#B6D3FC><td align=right>Balance Minutes: </td><td align=left><B>$balance_minutes</B></td></tr>\n";
		echo "<tr bgcolor=#B6D3FC><td align=right>Initial Minutes: </td><td align=left><B>$initial_minutes</B></td></tr>\n";
		echo "<tr bgcolor=#B6D3FC><td align=right>Initial Value: </td><td align=left><B>$initial_value</B></td></tr>\n";
		echo "<tr bgcolor=#B6D3FC><td align=right>Purchase Order: </td><td align=left><B>$note_purchase_order</B></td></tr>\n";
		echo "<tr bgcolor=#B6D3FC><td align=right>Printer: </td><td align=left><B>$note_printer</B></td></tr>\n";
		echo "<tr bgcolor=#B6D3FC><td align=right>DID: </td><td align=left><B>$note_did</B></td></tr>\n";
		echo "<tr bgcolor=#B6D3FC><td align=right>In-Group: </td><td align=left><B>$inbound_group_id</B></td></tr>\n";
		echo "<tr bgcolor=#B6D3FC><td align=right>Language: </td><td align=left><B>$note_language</B></td></tr>\n";
		echo "<tr bgcolor=#B6D3FC><td align=right>Name: </td><td align=left><B>$note_name</B></td></tr>\n";
		echo "<tr bgcolor=#B6D3FC><td align=right>Comments: </td><td align=left><B>$note_comments</B></td></tr>\n";
		echo "<tr bgcolor=#B6D3FC><td align=right>Create User/Date: </td><td align=left><B>$create_user / $create_time</B></td></tr>\n";
		echo "<tr bgcolor=#B6D3FC><td align=right>Activate User/Date: </td><td align=left><B>$activate_user / $activate_time</B></td></tr>\n";
		echo "<tr bgcolor=#B6D3FC><td align=right>Used User/Date: </td><td align=left><B>$used_user / $used_time</B></td></tr>\n";
		echo "<tr bgcolor=#B6D3FC><td align=right>Void User/Date: </td><td align=left><B>$void_user / $void_time</B></td></tr>\n";
		echo "</TABLE>\n";
		echo "<BR><BR><BR>\n";

		if ($SEARCHONLY < 1)
			{
			echo "<a href=\"$PHP_SELF?action=CALLCARD_STATUS&status=VOID&card_id=$card_id&DB=$DB\">Void This Card ID</a><BR><BR>\n";
			echo "<a href=\"$PHP_SELF?action=CALLCARD_STATUS&status=ACTIVE&card_id=$card_id&DB=$DB\">Activate This Card ID</a><BR><BR>\n";
			echo "<a href=\"$PHP_SELF?action=CALLCARD_STATUS&status=RESET&card_id=$card_id&DB=$DB\">Reset Minutes on This Card ID</a>\n";
			echo "<BR><BR>\n";
			}

		### call log
		echo "<TABLE><TR><TD>\n";
		echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";
		echo "<br>Call Log for this Card ID:\n";
		echo "<center><TABLE width=740 cellspacing=0 cellpadding=1>\n";
		echo "<TR BGCOLOR=BLACK>";
		echo "<TD><B><FONT FACE=\"Arial,Helvetica\" size=2 color=white>#</B></TD>";
		echo "<TD><B><FONT FACE=\"Arial,Helvetica\" size=2 color=white>DATE</B></TD>";
		echo "<TD><B><FONT FACE=\"Arial,Helvetica\" size=2 color=white>CallerID</B></TD>";
		echo "<TD><B><FONT FACE=\"Arial,Helvetica\" size=2 color=white>DID</B></TD>";
		echo "<TD><B><FONT FACE=\"Arial,Helvetica\" size=2 color=white>START MIN</B></TD>";
		echo "<TD><B><FONT FACE=\"Arial,Helvetica\" size=2 color=white>TALK MIN</B></TD>";
		echo "<TD><B><FONT FACE=\"Arial,Helvetica\" size=2 color=white>AGENT</B></TD>";
		echo "<TD><B><FONT FACE=\"Arial,Helvetica\" size=2 color=white>AGENT DATE</B></TD>";
		echo "<TD><B><FONT FACE=\"Arial,Helvetica\" size=2 color=white>AGENT DISPO</B></TD>";
		echo "</TR>\n";

		$stmt = "SELECT call_time,phone_number,inbound_did,balance_minutes_start,agent_talk_min,agent,agent_time,agent_dispo FROM callcard_log where card_id='$card_id' order by call_time;";
		$rslt=mysql_query($stmt, $link);
		if ($DB) {echo "$stmt\n";}
		$vt_ct = mysql_num_rows($rslt);
		$i=0;
		while ($vt_ct > $i)
			{
			$row=mysql_fetch_row($rslt);
			if (eregi("1$|3$|5$|7$|9$", $i))
				{$bgcolor='bgcolor="#9BB9FB"';}
			else
				{$bgcolor='bgcolor="#B9CBFD"';} 
			echo "<tr $bgcolor>\n";
			echo "<td><font size=1> $i </td>";
			echo "<td><font size=1> $row[0] </td>";
			echo "<td><font size=1> $row[1] </td>";
			echo "<td><font size=1> $row[2] </td>";
			echo "<td><font size=1> $row[3] </td>";
			echo "<td><font size=1> $row[4] </td>";
			echo "<td><font size=1> $row[5] </td>";
			echo "<td><font size=1> $row[6] </td>";
			echo "<td><font size=1> $row[7] </td>";
			echo "</tr>\n";

			$i++;
			}
		echo "</TABLE><BR><BR>\n";

		if ($SEARCHONLY < 1)
			{
			echo "<a href=\"admin.php?ADD=720000000000000&category=CALLCARD&stage=$card_id&DB=$DB\">Admin Log for this Card ID</a><BR><BR>\n";
			}
		}
	else
		{
		echo "ERROR: Card ID not found: $card_id<BR>\n";
		}
	}
### END card id detail page



### BEGIN callcard summary
if ($action == "CALLCARD_SUMMARY")
	{
	echo "<TABLE><TR><TD>\n";
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";
	echo "<br>CallCard Summary:\n";
	echo "<center><TABLE width=400 cellspacing=0 cellpadding=1>\n";
	echo "<TR BGCOLOR=BLACK>";
	echo "<TD><B><FONT FACE=\"Arial,Helvetica\" size=1 color=white>STATUS</B></TD>";
	echo "<TD><B><FONT FACE=\"Arial,Helvetica\" size=1 color=white>COUNT</B></TD>";
	echo "</TR>\n";

	$stmt = "SELECT count(*),status FROM callcard_accounts_details group by status order by status;";
	$rslt=mysql_query($stmt, $link);
	if ($DB) {echo "$stmt\n";}
	$vt_ct = mysql_num_rows($rslt);
	$i=0;
	while ($vt_ct > $i)
		{
		$row=mysql_fetch_row($rslt);
		$Lcount[$i] =		$row[0];
		$Lstatus[$i] =		$row[1];

		if (eregi("1$|3$|5$|7$|9$", $i))
			{$bgcolor='bgcolor="#9BB9FB"';}
		else
			{$bgcolor='bgcolor="#B9CBFD"';} 
		echo "<tr $bgcolor>\n";
		echo "<td><font size=1> $Lstatus[$i] </td>";
		echo "<td><font size=1> $Lcount[$i] </td>";
		echo "</tr>\n";

		$i++;
		}
	echo "</TABLE><BR><BR>\n";

	$stmt = "SELECT count(distinct batch) FROM callcard_accounts_details;";
	$rslt=mysql_query($stmt, $link);
	if ($DB) {echo "$stmt\n";}
	$db_ct = mysql_num_rows($rslt);
	if ($db_ct > 0)
		{
		$row=mysql_fetch_row($rslt);
		$DBcount =		$row[0];

		echo "<b>There are $DBcount batches in the system\n";
		}
	echo "<BR><BR>\n";

		### call log
		echo "<TABLE><TR><TD>\n";
		echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";
		echo "<br>Last 10 Calls:\n";
		echo "<center><TABLE width=740 cellspacing=0 cellpadding=1>\n";
		echo "<TR BGCOLOR=BLACK>";
		echo "<TD><B><FONT FACE=\"Arial,Helvetica\" size=2 color=white>#</B></TD>";
		echo "<TD><B><FONT FACE=\"Arial,Helvetica\" size=2 color=white>CARD ID</B></TD>";
		echo "<TD><B><FONT FACE=\"Arial,Helvetica\" size=2 color=white>DATE</B></TD>";
		echo "<TD><B><FONT FACE=\"Arial,Helvetica\" size=2 color=white>CallerID</B></TD>";
		echo "<TD><B><FONT FACE=\"Arial,Helvetica\" size=2 color=white>DID</B></TD>";
		echo "<TD><B><FONT FACE=\"Arial,Helvetica\" size=2 color=white>START MIN</B></TD>";
		echo "<TD><B><FONT FACE=\"Arial,Helvetica\" size=2 color=white>TALK MIN</B></TD>";
		echo "<TD><B><FONT FACE=\"Arial,Helvetica\" size=2 color=white>AGENT</B></TD>";
		echo "<TD><B><FONT FACE=\"Arial,Helvetica\" size=2 color=white>AGENT DATE</B></TD>";
		echo "<TD><B><FONT FACE=\"Arial,Helvetica\" size=2 color=white>DISPO</B></TD>";
		echo "</TR>\n";

		$stmt = "SELECT card_id,call_time,phone_number,inbound_did,balance_minutes_start,agent_talk_min,agent,agent_time,agent_dispo FROM callcard_log order by call_time desc limit 10;";
		$rslt=mysql_query($stmt, $link);
		if ($DB) {echo "$stmt\n";}
		$vt_ct = mysql_num_rows($rslt);
		$i=0;
		while ($vt_ct > $i)
			{
			$row=mysql_fetch_row($rslt);
			if (eregi("1$|3$|5$|7$|9$", $i))
				{$bgcolor='bgcolor="#9BB9FB"';}
			else
				{$bgcolor='bgcolor="#B9CBFD"';} 
			echo "<tr $bgcolor>\n";
			echo "<td><font size=1> $i </td>";
			echo "<td><font size=1> <a href=\"$PHP_SELF?action=CALLCARD_DETAIL&card_id=$row[0]&DB=$DB\"><font color=black>$row[0]</font></a> </td>";
			echo "<td><font size=1> $row[1] </td>";
			echo "<td><font size=1> $row[2] </td>";
			echo "<td><font size=1> $row[3] </td>";
			echo "<td><font size=1> $row[4] </td>";
			echo "<td><font size=1> $row[5] </td>";
			echo "<td><font size=1> $row[6] </td>";
			echo "<td><font size=1> $row[7] </td>";
			echo "<td><font size=1> $row[8] </td>";
			echo "</tr>\n";

			$i++;
			}
		echo "</TABLE><BR><BR>\n";

	echo "\n";
	echo "</center>\n";
	}
### END callcard summary



### BEGIN list runs
if ($action == "CALLCARD_RUNS")
	{
	echo "<TABLE><TR><TD>\n";
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";
	echo "<br>CallCard Batches:\n";
	echo "<center><TABLE width=400 cellspacing=0 cellpadding=1>\n";
	echo "<TR BGCOLOR=BLACK>";
	echo "<TD><B><FONT FACE=\"Arial,Helvetica\" size=1 color=white>RUN</B></TD>";
	echo "<TD><B><FONT FACE=\"Arial,Helvetica\" size=1 color=white>COUNT</B></TD>";
	echo "<TD><B><FONT FACE=\"Arial,Helvetica\" size=1 color=white>LIST</B></TD>";
	echo "</TR>\n";

	$stmt = "SELECT count(*),run FROM callcard_accounts_details group by run order by run;";
	$rslt=mysql_query($stmt, $link);
	if ($DB) {echo "$stmt\n";}
	$vt_ct = mysql_num_rows($rslt);
	$i=0;
	while ($vt_ct > $i)
		{
		$row=mysql_fetch_row($rslt);
		$Lcount[$i] =		$row[0];
		$Lrun[$i] =		$row[1];

		if (eregi("1$|3$|5$|7$|9$", $i))
			{$bgcolor='bgcolor="#9BB9FB"';}
		else
			{$bgcolor='bgcolor="#B9CBFD"';} 
		echo "<tr $bgcolor>\n";
		echo "<td><font size=1> $Lrun[$i] </td>";
		echo "<td><font size=1> $Lcount[$i] </td>";
		echo "<td><font size=1> <a href=\"$PHP_SELF?action=SEARCH_RESULTS&run=$Lrun[$i]&DB=$DB\">LIST</a> </td>";
		echo "</tr>\n";

		$i++;
		}
	echo "</TABLE><BR><BR>\n";

	echo "\n";
	echo "</center>\n";
	}
### END list runs



### BEGIN list batches
if ($action == "CALLCARD_BATCHES")
	{
	echo "<TABLE><TR><TD>\n";
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";
	echo "<br>CallCard Batches:\n";
	echo "<center><TABLE width=400 cellspacing=0 cellpadding=1>\n";
	echo "<TR BGCOLOR=BLACK>";
	echo "<TD><B><FONT FACE=\"Arial,Helvetica\" size=1 color=white>BATCH</B></TD>";
	echo "<TD><B><FONT FACE=\"Arial,Helvetica\" size=1 color=white>COUNT</B></TD>";
	echo "<TD><B><FONT FACE=\"Arial,Helvetica\" size=1 color=white>LIST</B></TD>";
	echo "</TR>\n";

	$stmt = "SELECT count(*),batch FROM callcard_accounts_details group by batch order by batch;";
	$rslt=mysql_query($stmt, $link);
	if ($DB) {echo "$stmt\n";}
	$vt_ct = mysql_num_rows($rslt);
	$i=0;
	while ($vt_ct > $i)
		{
		$row=mysql_fetch_row($rslt);
		$Lcount[$i] =		$row[0];
		$Lbatch[$i] =		$row[1];

		if (eregi("1$|3$|5$|7$|9$", $i))
			{$bgcolor='bgcolor="#9BB9FB"';}
		else
			{$bgcolor='bgcolor="#B9CBFD"';} 
		echo "<tr $bgcolor>\n";
		echo "<td><font size=1> $Lbatch[$i] </td>";
		echo "<td><font size=1> $Lcount[$i] </td>";
		echo "<td><font size=1> <a href=\"$PHP_SELF?action=SEARCH_RESULTS&batch=$Lbatch[$i]&DB=$DB\">LIST</a> </td>";
		echo "</tr>\n";

		$i++;
		}
	echo "</TABLE><BR><BR>\n";

	echo "\n";
	echo "</center>\n";
	}
### END list batches



### BEGIN generate results
if ($action == "GENERATE_RESULTS")
	{
	if ( (strlen($sequence) < 1) or (strlen($starting_batch) < 1) or (strlen($batch) < 1) or (strlen($run) < 1) or (strlen($total) < 1) )
		{
		echo "you must enter in all fields<BR><BR>\n";
		$action = 'GENERATE';
		}
	else
		{
		echo "<TABLE><TR><TD>\n";
		echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";
		echo "<br>CallCard GENERATE:\n";
		echo "<center><TABLE width=$section_width cellspacing=0 cellpadding=1>\n";
		echo "<TR BGCOLOR=BLACK>";
		echo "<TD><B><FONT FACE=\"Arial,Helvetica\" size=1 color=white>CARD ID</B></TD>";
		echo "<TD><B><FONT FACE=\"Arial,Helvetica\" size=1 color=white>PIN</B></TD>";
		echo "</TR>\n";

		$i=0;
		$Gbatch=$starting_batch;
		$Gbatch_count=-1;
		$Gsequence=$sequence;
		while ($i < $total)
			{
			$Gbatch_count++;
			$Gsequence++;
			if ($Gbatch_count >= $batch)
				{
				$Gbatch_count = 0;
				$Gbatch++;
				}

			$Pbatch =		sprintf("%05s", $Gbatch);
			$Psequence =	sprintf("%05s", $Gsequence);
			$Pcard_id = "$run-$Pbatch-$Psequence";

			$stmt = "SELECT count(*) FROM callcard_accounts_details where card_id='$Pcard_id';";
			$rslt=mysql_query($stmt, $link);
			if ($DB) {echo "$stmt\n";}
			$vt_ct = mysql_num_rows($rslt);
			if ($vt_ct > 0)
				{
				$row=mysql_fetch_row($rslt);
				$duplicate_count =	$row[0];
				if ($duplicate_count < 1)
					{
					$duplicate_pin=1;
					$pin_give_up=0;
					while ( ($duplicate_pin > 0) and ($pin_give_up < 100000) )
						{
						$Ppin = rand(10000000, 99999999);
						$stmt = "SELECT count(*) FROM callcard_accounts where pin='$Ppin';";
						$rslt=mysql_query($stmt, $link);
						if ($DB) {echo "$stmt\n";}
						$pin_ct = mysql_num_rows($rslt);
						if ($pin_ct > 0)
							{
							$row=mysql_fetch_row($rslt);
							$duplicate_pin =	$row[0];
							}
						else
							{
							echo "ERROR - cannot query system<BR>\n";
							}
						$pin_give_up++;
						}

					### insert card_id and pin into tables
					$stmt="INSERT INTO callcard_accounts SET card_id='$Pcard_id',pin='$Ppin',status='GENERATE',balance_minutes='$balance_minutes';";
					$rslt=mysql_query($stmt, $link);

					$stmt="INSERT INTO callcard_accounts_details SET card_id='$Pcard_id',status='GENERATE',run='$run',pack='',batch='$Pbatch',sequence='$Psequence',balance_minutes='$balance_minutes',initial_value='$initial_value',initial_minutes='$initial_minutes',note_purchase_order='$note_purchase_order',note_printer='$note_printer',note_did='$note_did',inbound_group_id='$inbound_group_id',note_language='$note_language',note_name='$note_name',note_comments='$note_comments',create_time='$NOW_TIME',create_user='$USER';";
					$rslt=mysql_query($stmt, $link);

					if (eregi("1$|3$|5$|7$|9$", $i))
						{$bgcolor='bgcolor="#9BB9FB"';}
					else
						{$bgcolor='bgcolor="#B9CBFD"';} 
					echo "<tr $bgcolor>\n";
					echo "<td><font size=1> <a href=\"$PHP_SELF?action=CALLCARD_DETAIL&card_id=$Pcard_id&DB=$DB\"><font color=black>$Pcard_id</font></a> </td>";
					echo "<td><font size=1> $Ppin </td>";
					echo "</tr>\n";
					}
				else
					{
					echo "ERROR - card_id already exists: $Pcard_id<BR>\n";
					}
				}
			else
				{
				echo "ERROR - cannot query system<BR>\n";
				}
			$i++;
			}
		echo "</TABLE><BR><BR>\n";

		echo "\n";
		echo "</center>\n";
		}
	}
### END generate results



### GENERATE search
if ($action == "GENERATE")
	{
	echo "<TABLE><TR><TD>\n";
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";
	echo "<br>CallCard Generate IDs<form action=$PHP_SELF method=POST>\n";
	echo "<input type=hidden name=action value=GENERATE_RESULTS>\n";
	echo "<input type=hidden name=DB value=$DB>\n";
	echo "<center><TABLE width=$section_width cellspacing=3>\n";
	echo "<tr bgcolor=#B6D3FC><td align=right>Run: </td><td align=left><input type=text name=run size=5 maxlength=4></td></tr>\n";
	echo "<tr bgcolor=#B6D3FC><td align=right>Starting Batch: </td><td align=left><input type=text name=starting_batch size=6 maxlength=5></td></tr>\n";
	echo "<tr bgcolor=#B6D3FC><td align=right>IDs in Batch: </td><td align=left><input type=text name=batch size=6 maxlength=5></td></tr>\n";
	echo "<tr bgcolor=#B6D3FC><td align=right>Starting Sequence: </td><td align=left><input type=text name=sequence size=6 maxlength=5></td></tr>\n";
	echo "<tr bgcolor=#B6D3FC><td align=right>Total: </td><td align=left><input type=text name=total size=8 maxlength=7></td></tr>\n";
	echo "<tr bgcolor=#B6D3FC><td align=right>Balance Minutes: </td><td align=left><input type=text name=balance_minutes size=3 maxlength=5></td></tr>\n";
	echo "<tr bgcolor=#B6D3FC><td align=right>Initial Value: </td><td align=left><input type=text name=initial_value size=7 maxlength=6></td></tr>\n";
	echo "<tr bgcolor=#B6D3FC><td align=right>Initial Minutes: </td><td align=left><input type=text name=initial_minutes size=3 maxlength=5></td></tr>\n";
	echo "<tr bgcolor=#B6D3FC><td align=right>Purchase Order: </td><td align=left><input type=text name=note_purchase_order size=20 maxlength=20></td></tr>\n";
	echo "<tr bgcolor=#B6D3FC><td align=right>Printer: </td><td align=left><input type=text name=note_printer size=20 maxlength=20></td></tr>\n";
	echo "<tr bgcolor=#B6D3FC><td align=right>DID: </td><td align=left><input type=text name=note_did size=18 maxlength=18></td></tr>\n";
	echo "<tr bgcolor=#B6D3FC><td align=right>In-Group: </td><td align=left><input type=text name=inbound_group_id size=20 maxlength=20></td></tr>\n";
	echo "<tr bgcolor=#B6D3FC><td align=right>Language: </td><td align=left><input type=text name=note_language size=10 maxlength=10></td></tr>\n";
	echo "<tr bgcolor=#B6D3FC><td align=right>Name: </td><td align=left><input type=text name=note_name size=20 maxlength=20></td></tr>\n";
	echo "<tr bgcolor=#B6D3FC><td align=right>Comment: </td><td align=left><input type=text name=note_comments size=50 maxlength=255></td></tr>\n";

	echo "<tr bgcolor=#B6D3FC><td align=center colspan=2><input type=submit name=SUBMIT value=SUBMIT></td></tr>\n";
	echo "</TABLE></center>\n";
	echo "\n";
	echo "</center>\n";
	}
### GENERATE search



### BEGIN search results
if ($action == "SEARCH_RESULTS")
	{
	if (strlen($pin) > 1)
		{
		$searchSQL = "pin='$pin'";

		$stmt = "SELECT card_id FROM callcard_accounts where $searchSQL;";
		$rslt=mysql_query($stmt, $link);
		if ($DB) {echo "$stmt\n";}
		$vt_ct = mysql_num_rows($rslt);
		if ($vt_ct > 0)
			{
			$row=mysql_fetch_row($rslt);
			$card_id =			$row[0];
			}
		}
	if (strlen($card_id) > 1)
		{$searchSQL = "card_id='$card_id'";}
	if (strlen($run) > 1)
		{
		$and='';
		if (strlen($searchSQL) > 1) {$and=' and';}
		$searchSQL .= "$and run='$run'";
		}
	if (strlen($batch) > 1)
		{
		$and='';
		if (strlen($searchSQL) > 1) {$and=' and';}
		$searchSQL .= "$and batch='$batch'";
		}
	if (strlen($pack) > 1)
		{
		$and='';
		if (strlen($searchSQL) > 1) {$and=' and';}
		$searchSQL .= "$and pack='$pack'";
		}
	if (strlen($sequence) > 1)
		{
		$and='';
		if (strlen($searchSQL) > 1) {$and=' and';}
		$searchSQL .= "$and sequence='$sequence'";
		}

	if (strlen($searchSQL) < 5)
		{
		echo "you must enter something to search for<BR><BR>\n";
		$action = 'SEARCH';
		}
	else
		{
		echo "<TABLE><TR><TD>\n";
		echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";
		echo "<br>CallCard Batches:\n";
		echo "<center><TABLE width=$section_width cellspacing=0 cellpadding=1>\n";
		echo "<TR BGCOLOR=BLACK>";
		echo "<TD><B><FONT FACE=\"Arial,Helvetica\" size=1 color=white>CARD ID</B></TD>";
		echo "<TD><B><FONT FACE=\"Arial,Helvetica\" size=1 color=white>STATUS</B></TD>";
		echo "<TD><B><FONT FACE=\"Arial,Helvetica\" size=1 color=white>BALANCE</B></TD>";
		echo "<TD><B><FONT FACE=\"Arial,Helvetica\" size=1 color=white>CREATE</B></TD>";
		echo "<TD><B><FONT FACE=\"Arial,Helvetica\" size=1 color=white>ACTIVATE</B></TD>";
		echo "<TD><B><FONT FACE=\"Arial,Helvetica\" size=1 color=white>LAST USED</B></TD>";
		echo "<TD><B><FONT FACE=\"Arial,Helvetica\" size=1 color=white>VOID</B></TD>";
		echo "<TD><B><FONT FACE=\"Arial,Helvetica\" size=1 color=white>DETAIL</B></TD>";
		echo "</TR>\n";

		$stmt = "SELECT card_id,status,balance_minutes,create_time,activate_time,used_time,void_time FROM callcard_accounts_details where $searchSQL;";
		$rslt=mysql_query($stmt, $link);
		if ($DB) {echo "$stmt\n";}
		$vt_ct = mysql_num_rows($rslt);
		$i=0;
		while ($vt_ct > $i)
			{
			$row=mysql_fetch_row($rslt);
			$Lcard_id[$i] =			$row[0];
			$Lstatus[$i] =			$row[1];
			$Lbalance_minutes[$i] =	$row[2];
			$Lcreate_time[$i] =		$row[3];
			$Lactivate_time[$i] =	$row[4];
			$Lused_time[$i] =		$row[5];
			$Lvoid_time[$i] =		$row[6];

			if (eregi("1$|3$|5$|7$|9$", $i))
				{$bgcolor='bgcolor="#9BB9FB"';}
			else
				{$bgcolor='bgcolor="#B9CBFD"';} 
			echo "<tr $bgcolor>\n";
			echo "<td><font size=1> <a href=\"$PHP_SELF?action=CALLCARD_DETAIL&card_id=$Lcard_id[$i]&DB=$DB\"><font color=black>$Lcard_id[$i]</font></a> </td>";
			echo "<td><font size=1> $Lstatus[$i] </td>";
			echo "<td><font size=1> $Lbalance_minutes[$i] </td>";
			echo "<td><font size=1> $Lcreate_time[$i] </td>";
			echo "<td><font size=1> $Lactivate_time[$i] </td>";
			echo "<td><font size=1> $Lused_time[$i] </td>";
			echo "<td><font size=1> $Lvoid_time[$i] </td>";
			echo "<td><font size=1> <a href=\"$PHP_SELF?action=CALLCARD_DETAIL&card_id=$Lcard_id[$i]&DB=$DB\">DETAILS</a> </td>";
			echo "</tr>\n";

			$i++;
			}
		echo "</TABLE><BR><BR>\n";

		echo "\n";
		echo "</center>\n";
		}
	}
### END search results



### BEGIN search
if ($action == "SEARCH")
	{
	echo "<TABLE><TR><TD>\n";
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";

	echo "<br>CallCard Search<form action=$PHP_SELF method=POST>\n";
	echo "<input type=hidden name=action value=SEARCH_RESULTS>\n";
	echo "<input type=hidden name=DB value=$DB>\n";
	echo "<center><TABLE width=$section_width cellspacing=3>\n";
	echo "<tr bgcolor=#B6D3FC><td align=right>PIN: </td><td align=left><input type=text name=pin size=20 maxlength=20></td></tr>\n";
	echo "<tr bgcolor=#B6D3FC><td align=right>Card ID: </td><td align=left><input type=text name=card_id size=20 maxlength=20></td></tr>\n";
	echo "<tr bgcolor=#B6D3FC><td align=right>Run: </td><td align=left><input type=text name=run size=5 maxlength=4></td></tr>\n";
	echo "<tr bgcolor=#B6D3FC><td align=right>Batch: </td><td align=left><input type=text name=batch size=6 maxlength=5></td></tr>\n";
	echo "<tr bgcolor=#B6D3FC><td align=right>Sequence: </td><td align=left><input type=text name=sequence size=6 maxlength=5></td></tr>\n";
	echo "<tr bgcolor=#B6D3FC><td align=center colspan=2><input type=submit name=SUBMIT value=SUBMIT></td></tr>\n";
	echo "</TABLE></center>\n";
	echo "\n";
	echo "</center>\n";
	}
### END search





?>



<BR><font size=1>CallCard &nbsp; &nbsp; VERSION: <?php echo $version ?> &nbsp; &nbsp; BUILD: <?php echo $build ?> &nbsp; &nbsp; </td></tr>
</TD></TR></TABLE>
