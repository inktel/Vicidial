<?php
# user_territories.php
# 
# Copyright (C) 2009  Matt Florell <vicidial@gmail.com>    LICENSE: AGPLv2
#
# This territories script is for use with custom tables in Vtiger which is why
# it is separate from the standard admin.php script. user_territories_active in
# the system_settings table must be active for this script to work.
# 
# CHANGES
# 90520-1928 - first build
# 90717-0651 - Added batch
# 90726-2302 - Added vicidial_list user owner update option
# 91012-0310 - Added vicidial_list counts for territory as owner
#

$version = '2.2.0-4';
$build = '91012-0310';

$MT[0]='';

require("dbconnect.php");

$PHP_SELF=$_SERVER['PHP_SELF'];
if (isset($_GET["action"]))					{$action=$_GET["action"];}
	elseif (isset($_POST["action"]))		{$action=$_POST["action"];}
if (isset($_GET["DB"]))						{$DB=$_GET["DB"];}
	elseif (isset($_POST["DB"]))			{$DB=$_POST["DB"];}
if (isset($_GET["territory"]))				{$territory=$_GET["territory"];}
	elseif (isset($_POST["territory"]))		{$territory=$_POST["territory"];}
if (isset($_GET["territory_description"]))			{$territory_description=$_GET["territory_description"];}
	elseif (isset($_POST["territory_description"]))	{$territory_description=$_POST["territory_description"];}
if (isset($_GET["user"]))					{$user=$_GET["user"];}
	elseif (isset($_POST["user"]))			{$user=$_POST["user"];}
if (isset($_GET["level"]))					{$level=$_GET["level"];}
	elseif (isset($_POST["level"]))			{$level=$_POST["level"];}
if (isset($_GET["accountid"]))				{$accountid=$_GET["accountid"];}
	elseif (isset($_POST["accountid"]))		{$accountid=$_POST["accountid"];}
if (isset($_GET["batch"]))					{$batch=$_GET["batch"];}
	elseif (isset($_POST["batch"]))			{$batch=$_POST["batch"];}
if (isset($_GET["vl_owner"]))				{$vl_owner=$_GET["vl_owner"];}
	elseif (isset($_POST["vl_owner"]))		{$vl_owner=$_POST["vl_owner"];}
if (isset($_GET["SUBMIT"]))					{$SUBMIT=$_GET["SUBMIT"];}
	elseif (isset($_POST["SUBMIT"]))		{$SUBMIT=$_POST["SUBMIT"];}


header ("Content-type: text/html; charset=utf-8");
header ("Cache-Control: no-cache, must-revalidate");  // HTTP/1.1
header ("Pragma: no-cache");                          // HTTP/1.0

#############################################
##### START SYSTEM_SETTINGS LOOKUP #####
$stmt = "SELECT use_non_latin,user_territories_active,enable_vtiger_integration,outbound_autodial_active,vtiger_server_ip,vtiger_dbname,vtiger_login,vtiger_pass,vtiger_url FROM system_settings;";
$rslt=mysql_query($stmt, $link);
if ($DB) {echo "$stmt\n";}
$ss_conf_ct = mysql_num_rows($rslt);
if ($ss_conf_ct > 0)
	{
	$row=mysql_fetch_row($rslt);
	$non_latin =						$row[0];
	$user_territories_active =			$row[1];
	$enable_vtiger_integration =		$row[2];
	$SSoutbound_autodial_active =		$row[3];
	$vtiger_server_ip	=				$row[4];
	$vtiger_dbname =					$row[5];
	$vtiger_login =						$row[6];
	$vtiger_pass =						$row[7];
	$vtiger_url =						$row[8];
	}
##### END SETTINGS LOOKUP #####
###########################################



if ($user_territories_active < 1)
	{
	echo "ERROR: User Territories are not active on this system\n";
	exit;
	}

if ($non_latin < 1)
	{
	### Clean Variable Values ###
	$DB = ereg_replace("[^0-9]","",$DB);
	$action = ereg_replace("[^\_0-9a-zA-Z]","",$action);
	$territory = ereg_replace("[^-\_0-9a-zA-Z]","",$territory);
	$territory_description = ereg_replace("[^ -\_\.\,0-9a-zA-Z]","",$territory_description);
	$user = ereg_replace("[^-\_0-9a-zA-Z]","",$user);
	$level = ereg_replace("[^\_A-Z]","",$level);
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

	$stmt="SELECT count(*) from vicidial_users where user='$USER' and pass='$PASS' and user_level > 7 and modify_users='1'";
	if ($DB) {echo "|$stmt|\n";}
	if ($non_latin > 0) { $rslt=mysql_query("SET NAMES 'UTF8'");}
	$rslt=mysql_query($stmt, $link);
	$row=mysql_fetch_row($rslt);
	$auth=$row[0];

	if( (strlen($USER)<2) or (strlen($PASS)<2) or (!$auth))
		{
		Header("WWW-Authenticate: Basic realm=\"VICI-PROJECTS\"");
		Header("HTTP/1.0 401 Unauthorized");
		echo "Invalid Username/Password: |$USER|$PASS|\n";
		exit;
		}
	}

if ($enable_vtiger_integration > 0)
	{
	### connect to your vtiger database
	$linkV=mysql_connect("$vtiger_server_ip", "$vtiger_login","$vtiger_pass");
	if (!$linkV) {die("Could not connect: $vtiger_server_ip|$vtiger_dbname|$vtiger_login|$vtiger_pass" . mysql_error());}
	mysql_select_db("$vtiger_dbname", $linkV);
	}


if (strlen($action) < 1)
	{$action = 'LIST_ALL_TERRITORIES';}



?>
<html>
<head>
<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=utf-8">
<!-- VERSION: <?php echo $version ?>     BUILD: <?php echo $build ?> -->
<title>ADMINISTRATION: User Territories
<?php






### BEGIN change territory owner for one account
if ( ($action == "CHANGE_TERRITORY_OWNER_ACCOUNT") and ($enable_vtiger_integration > 0) )
	{
	echo "</TITLE></HEAD><BODY BGCOLOR=white>\n";
	echo "<TABLE><TR><TD>\n";
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";

	echo "<br>Vtiger Change Territory Owner<form action=$PHP_SELF method=POST>\n";
	echo "<input type=hidden name=action value=PROCESS_CHANGE_TERRITORY_OWNER_ACCOUNT>\n";
	echo "<center><TABLE width=$section_width cellspacing=3>\n";
	echo "<tr bgcolor=#B6D3FC><td align=right>Account ID: </td><td align=left><input type=text name=accountid value=\"$accountid\"></td></tr>\n";
	echo "<tr bgcolor=#B6D3FC><td align=right>New Owner: </td><td align=left><select size=1 name=user>\n";

	$stmt="SELECT user,full_name from vicidial_users where active='Y' order by user";
	$rslt=mysql_query($stmt, $link);
	$users_to_print = mysql_num_rows($rslt);
	$users_list='';

	$o=0;
	while ($users_to_print > $o) 
		{
		$rowx=mysql_fetch_row($rslt);
		$users_list .= "<option value=\"$rowx[0]\">$rowx[0] - $rowx[1]</option>\n";
		$o++;
		}
	echo "$users_list";
	echo "</select></td></tr>\n";

	echo "<tr bgcolor=#B6D3FC><td align=right>Update ViciDial List Owner: </td><td align=left><select size=1 name=vl_owner>\n";
	echo "<option SELECTED>YES</option>\n";
	echo "<option>NO</option>\n";
	echo "</select></td></tr>\n";

	echo "<tr bgcolor=#B6D3FC><td align=center colspan=2><input type=submit name=SUBMIT value=SUBMIT></td></tr>\n";
	echo "</TABLE></center>\n";
	exit;
	}
### END change territory owner for one account


### BEGIN process change territory owner for one account
if ( ($action == "PROCESS_CHANGE_TERRITORY_OWNER_ACCOUNT") and ($enable_vtiger_integration > 0) )
	{
	echo "</TITLE></HEAD><BODY BGCOLOR=white>\n";

	if ( (strlen($accountid)<1) or (strlen($user)<1) )
		{
		echo "ERROR: Account ID and User must be filled in<BR>\n";
		}
	else
		{
		if (eregi("YES",$batch))
			{$AID_lookupSQL = "website='$accountid'";}
		else
			{$AID_lookupSQL = "accountid='$accountid'";}
		$stmtV="SELECT tickersymbol,accountid from vtiger_account where $AID_lookupSQL;";
		$rsltV=mysql_query($stmtV, $linkV);
		if ($DB) {echo "$stmtV\n";}
		$vat_ct = mysql_num_rows($rsltV);
		if ($vat_ct > 0)
			{
			$row=mysql_fetch_row($rsltV);
			$territory =		$row[0];
			$accountid =		$row[1];

			$stmt="SELECT count(*) from vicidial_user_territories where territory='$territory' and user='$user';";
			$rslt=mysql_query($stmt, $link);
			$row=mysql_fetch_row($rslt);
			if ($row[0] < 1)
				{
				$stmt="INSERT INTO vicidial_user_territories SET territory='$territory',user='$user',level='STANDARD_AGENT';";
				$rslt=mysql_query($stmt, $link);

				echo "NOTICE: Had to add user territory: $user $territory<BR>\n";

				### LOG INSERTION Admin Log Table ###
				$SQL_log = "$stmt|$stmtB|";
				$SQL_log = ereg_replace(';','',$SQL_log);
				$SQL_log = addslashes($SQL_log);
				$stmt="INSERT INTO vicidial_admin_log set event_date='$NOW_TIME', user='$USER', ip_address='$ip', event_section='TERRITORIES', event_type='ADD', record_id='$territory', event_code='ADMIN ADD USER TERRITORY', event_sql=\"$SQL_log\", event_notes='';";
				if ($DB) {echo "|$stmt|\n";}
				$rslt=mysql_query($stmt, $link);
				}

			$stmtV="SELECT id from vtiger_users where user_name='$user';";
			$rsltV=mysql_query($stmtV, $linkV);
			if ($DB) {echo "$stmtV\n";}
			$vtu_ct = mysql_num_rows($rsltV);
			if ($vtu_ct > 0)
				{
				$row=mysql_fetch_row($rsltV);
				$user_id =		$row[0];

				$stmt="UPDATE vtiger_crmentity SET smownerid='$user_id',smcreatorid='$user_id',modifiedby='$user_id' where crmid='$accountid';";
				$rsltV=mysql_query($stmt, $linkV);
				$changed = mysql_affected_rows($linkV);

				$stmtB="UPDATE vtiger_tracker SET user_id='$user_id' where item_id='$accountid';";
				$rsltV=mysql_query($stmtB, $linkV);

				if ( ($vl_owner == 'YES') and ($accountid > 0) )
					{
					$stmtB="UPDATE vicidial_list SET owner='$user' where vendor_lead_code='$accountid';";
					$rsltV=mysql_query($stmtB, $link);
					}

				echo "Vtiger Territory Owner Changed: $user $territory<BR>\n";
				echo "Records Changed: $changed<BR>\n";

				### LOG INSERTION Admin Log Table ###
				$SQL_log = "$stmt|$stmtB|";
				$SQL_log = ereg_replace(';','',$SQL_log);
				$SQL_log = addslashes($SQL_log);
				$stmt="INSERT INTO vicidial_admin_log set event_date='$NOW_TIME', user='$USER', ip_address='$ip', event_section='VTIGER', event_type='MODIFY', record_id='$accountid', event_code='VTIGER MODIFY TERRITORY OWNER ACCOUNT', event_sql=\"$SQL_log\", event_notes='';";
				if ($DB) {echo "|$stmt|\n";}
				$rslt=mysql_query($stmt, $link);
				}
			}
		}
	exit;
	}
### END process change territory owner for one account





##### BEGIN Set variables to make header show properly #####
$ADD =					'0';
$hh =					'users';
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

$colspan='3';

?>
<TABLE WIDTH=<?php echo $page_width ?> BGCOLOR=#E6E6E6 cellpadding=2 cellspacing=0>
<TR BGCOLOR=#E6E6E6>
<TD ALIGN=LEFT><FONT FACE="ARIAL,HELVETICA" SIZE=2><B> &nbsp; <a href="<?php echo "$PHP_SELF" ?>">List Territories</a></TD>
<TD ALIGN=RIGHT><FONT FACE="ARIAL,HELVETICA" SIZE=2><B> &nbsp; <a href="<?php echo "$PHP_SELF" ?>?action=ADD_TERRITORY">Add Territory</a></TD>
<TD ALIGN=RIGHT><FONT FACE="ARIAL,HELVETICA" SIZE=2><B> &nbsp; <a href="<?php echo "$PHP_SELF" ?>?action=ADD_USER_TERRITORY">Add User Territory</a></TD>
<?
if ($enable_vtiger_integration > 0)
	{ ?>
<TD ALIGN=RIGHT><FONT FACE="ARIAL,HELVETICA" SIZE=2><B> &nbsp; <a href="<?php echo "$PHP_SELF" ?>?action=CHANGE_TERRITORY_OWNER">Change Vtiger Territory Owner</a></TD>
<?
	$colspan='4';
	} ?>

</TR>

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







### BEGIN change territory owner in the system
if ( ($action == "CHANGE_TERRITORY_OWNER") and ($enable_vtiger_integration > 0) )
	{
	echo "<TABLE><TR><TD>\n";
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";

	echo "<br>Vtiger Change Territory Owner<form action=$PHP_SELF method=POST>\n";
	echo "<input type=hidden name=action value=PROCESS_CHANGE_TERRITORY_OWNER>\n";
	echo "<center><TABLE width=$section_width cellspacing=3>\n";
	echo "<tr bgcolor=#B6D3FC><td align=right>Territory: </td><td align=left><select size=1 name=territory>\n";

	$stmt="SELECT territory,territory_description from vicidial_territories order by territory";
	$rslt=mysql_query($stmt, $link);
	$territories_to_print = mysql_num_rows($rslt);
	$territories_list='';

	$o=0;
	while ($territories_to_print > $o) 
		{
		$rowx=mysql_fetch_row($rslt);
		$territories_list .= "<option value=\"$rowx[0]\">$rowx[0] - $rowx[1]</option>\n";
		$o++;
		}
	echo "$territories_list";
	echo "</select></td></tr>\n";

	echo "<tr bgcolor=#B6D3FC><td align=right>New Owner: </td><td align=left><select size=1 name=user>\n";

	$stmt="SELECT user,full_name from vicidial_users where active='Y' order by user";
	$rslt=mysql_query($stmt, $link);
	$users_to_print = mysql_num_rows($rslt);
	$users_list='';

	$o=0;
	while ($users_to_print > $o) 
		{
		$rowx=mysql_fetch_row($rslt);
		$users_list .= "<option value=\"$rowx[0]\">$rowx[0] - $rowx[1]</option>\n";
		$o++;
		}
	echo "$users_list";
	echo "</select></td></tr>\n";

	echo "<tr bgcolor=#B6D3FC><td align=right>Update ViciDial List Owner: </td><td align=left><select size=1 name=vl_owner>\n";
	echo "<option SELECTED>YES</option>\n";
	echo "<option>NO</option>\n";
	echo "</select></td></tr>\n";

	echo "<tr bgcolor=#B6D3FC><td align=center colspan=2><input type=submit name=SUBMIT value=SUBMIT></td></tr>\n";
	echo "</TABLE></center>\n";
	}
### END change territory owner in the system


### BEGIN process change territory owner in the system
if ( ($action == "PROCESS_CHANGE_TERRITORY_OWNER") and ($enable_vtiger_integration > 0) )
	{
	if ( (strlen($territory)<1) or (strlen($user)<1) )
		{
		echo "ERROR: Territory and User must be filled in<BR>\n";
		}
	else
		{
		$stmt="SELECT count(*) from vicidial_user_territories where territory='$territory' and user='$user';";
		$rslt=mysql_query($stmt, $link);
		$row=mysql_fetch_row($rslt);
		if ($row[0] < 1)
			{
			$stmt="INSERT INTO vicidial_user_territories SET territory='$territory',user='$user',level='TOP_AGENT';";
			$rslt=mysql_query($stmt, $link);

			$stmtB="UPDATE vicidial_user_territories SET level='STANDARD_AGENT' where territory='$territory' and user!='$user' and level='TOP_AGENT';";
			$rslt=mysql_query($stmtB, $link);

			echo "NOTICE: Had to add user territory: $user $territory<BR>\n";

			### LOG INSERTION Admin Log Table ###
			$SQL_log = "$stmt|$stmtB|";
			$SQL_log = ereg_replace(';','',$SQL_log);
			$SQL_log = addslashes($SQL_log);
			$stmt="INSERT INTO vicidial_admin_log set event_date='$NOW_TIME', user='$USER', ip_address='$ip', event_section='TERRITORIES', event_type='ADD', record_id='$territory', event_code='ADMIN ADD USER TERRITORY', event_sql=\"$SQL_log\", event_notes='';";
			if ($DB) {echo "|$stmt|\n";}
			$rslt=mysql_query($stmt, $link);
			}

		$stmtV="SELECT id from vtiger_users where user_name='$user';";
		$rsltV=mysql_query($stmtV, $linkV);
		if ($DB) {echo "$stmtV\n";}
		$vtu_ct = mysql_num_rows($rsltV);
		if ($vtu_ct > 0)
			{
			$row=mysql_fetch_row($rsltV);
			$user_id =		$row[0];

			$stmtV="SELECT accountid from vtiger_account where tickersymbol='$territory';";
			$rsltV=mysql_query($stmtV, $linkV);
			if ($DB) {echo "$stmtV\n";}
			$vat_ct = mysql_num_rows($rsltV);
			$account_ids[0]='';
			$p=0;
			while ($vat_ct > $p)
				{
				$row=mysql_fetch_row($rsltV);
				$account_ids[$p] =		$row[0];
				$p++;
				}

			$p=0;
			while ($vat_ct > $p)
				{
				$stmt="UPDATE vtiger_crmentity SET smownerid='$user_id',smcreatorid='$user_id',modifiedby='$user_id' where crmid='$account_ids[$p]';";
				$rsltV=mysql_query($stmt, $linkV);
				$changedX = mysql_affected_rows($linkV);
				if ($DB) {echo "$stmt|$changedX\n";}
				$changed = ($changed + $changedX);

				$stmtV="select activityid from vtiger_seactivityrel where crmid='$account_ids[$p]';";
				$rsltV=mysql_query($stmtV, $linkV);
				if ($DB) {echo "$stmtV\n";}
				$vact_ct = mysql_num_rows($rsltV);
				$activity_ids[0]='';
				$r=0;
				while ($vact_ct > $r)
					{
					$row=mysql_fetch_row($rsltV);
					$activity_ids[$r] =		$row[0];
					$r++;
					}

				$r=0;
				while ($vact_ct > $r)
					{
					$stmt="UPDATE vtiger_crmentity SET smownerid='$user_id' where crmid='$activity_ids[$r]';";
					$rsltV=mysql_query($stmt, $linkV);
					$AchangedX = mysql_affected_rows($linkV);
					if ($DB) {echo "$stmt|$AchangedX\n";}
					$Achanged = ($Achanged + $AchangedX);
					$r++;
					}

				if ( ($vl_owner == 'YES') and ($account_ids[$p] > 0) )
					{
					$stmtB="UPDATE vicidial_list SET owner='$user' where vendor_lead_code='$account_ids[$p]';";
					$rslt=mysql_query($stmtB, $link);
					$VchangedX = mysql_affected_rows($link);
					if ($DB) {echo "$stmtB|$VchangedX\n";}
					$Vchanged = ($Vchanged + $VchangedX);
					}

				$p++;
				}


			$stmt="UPDATE vtiger_crmentity SET smownerid='$user_id',smcreatorid='$user_id',modifiedby='$user_id' where crmid IN(SELECT accountid from vtiger_account where tickersymbol='$territory');";
			$rsltV=mysql_query($stmt, $linkV);
			$Cchanged = mysql_affected_rows($linkV);
			if ($DB) {echo "$stmt|$Cchanged\n";}

			$stmtB="UPDATE vtiger_tracker vt, vtiger_account va SET user_id='$user_id' where vt.item_id=va.accountid and va.tickersymbol='$territory';";
			$rsltV=mysql_query($stmtB, $linkV);

			echo "Vtiger Territory Owner Changed: $user $territory &nbsp; &nbsp; &nbsp; Records Changed: $changed - $Achanged - $Cchanged - $Vchanged<BR>\n";

			### LOG INSERTION Admin Log Table ###
			$SQL_log = "$stmt|$stmtB|";
			$SQL_log = ereg_replace(';','',$SQL_log);
			$SQL_log = addslashes($SQL_log);
			$stmt="INSERT INTO vicidial_admin_log set event_date='$NOW_TIME', user='$USER', ip_address='$ip', event_section='VTIGER', event_type='MODIFY', record_id='$territory', event_code='VTIGER MODIFY TERRITORY OWNER', event_sql=\"$SQL_log\", event_notes='';";
			if ($DB) {echo "|$stmt|\n";}
			$rslt=mysql_query($stmt, $link);
			}
		}
	$action = "MODIFY_TERRITORY";
	}
### END process change territory owner in the system





### BEGIN add user territory page
if ($action == "ADD_USER_TERRITORY")
	{
	echo "<TABLE><TR><TD>\n";
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";

	echo "<br>Add User Territory<form action=$PHP_SELF method=POST>\n";
	echo "<input type=hidden name=action value=PROCESS_ADD_USER_TERRITORY>\n";
	echo "<center><TABLE width=$section_width cellspacing=3>\n";
	echo "<tr bgcolor=#B6D3FC><td align=right>Agent: </td><td align=left><select size=1 name=user>\n";

	$stmt="SELECT user,full_name from vicidial_users where active='Y' order by user";
	$rslt=mysql_query($stmt, $link);
	$users_to_print = mysql_num_rows($rslt);
	$users_list='';

	$o=0;
	while ($users_to_print > $o) 
		{
		$rowx=mysql_fetch_row($rslt);
		$users_list .= "<option value=\"$rowx[0]\">$rowx[0] - $rowx[1]</option>\n";
		$o++;
		}
	echo "$users_list";
	echo "</select></td></tr>\n";

	echo "<tr bgcolor=#B6D3FC><td align=right>Territory: </td><td align=left><select size=1 name=territory>\n";

	$stmt="SELECT territory,territory_description from vicidial_territories order by territory";
	$rslt=mysql_query($stmt, $link);
	$territories_to_print = mysql_num_rows($rslt);
	$territories_list='';

	$o=0;
	while ($territories_to_print > $o) 
		{
		$rowx=mysql_fetch_row($rslt);
		$territories_list .= "<option value=\"$rowx[0]\">$rowx[0] - $rowx[1]</option>\n";
		$o++;
		}
	echo "$territories_list";
	echo "</select></td></tr>\n";
	echo "<tr bgcolor=#B6D3FC><td align=right>Level: </td><td align=left><select size=1 name=level><option>TOP_AGENT</option><option>STANDARD_AGENT</option><option>BOTTOM_AGENT</option></select></td></tr>\n";
	echo "<tr bgcolor=#B6D3FC><td align=center colspan=2><input type=submit name=SUBMIT value=SUBMIT></td></tr>\n";
	echo "</TABLE></center>\n";
	}
### END add user territory page


### BEGIN process add user territory page
if ($action == "PROCESS_ADD_USER_TERRITORY")
	{
	if ( (strlen($territory)<1) or (strlen($user)<1) or (strlen($level)<1) )
		{
		echo "ERROR: Territory, User and Level must be filled in<BR>\n";
		}
	else
		{
		$stmt="SELECT count(*) from vicidial_user_territories where territory='$territory' and user='$user';";
		$rslt=mysql_query($stmt, $link);
		$row=mysql_fetch_row($rslt);
		if ($row[0] > 0)
			{
			echo "ERROR: this territory user is already in the system<BR>\n";
			}
		else
			{
			$stmt="INSERT INTO vicidial_user_territories SET territory='$territory',user='$user',level='$level';";
			$rslt=mysql_query($stmt, $link);

			if ($level == "TOP_AGENT")
				{
				$stmtB="UPDATE vicidial_user_territories SET level='STANDARD_AGENT' where territory='$territory' and user!='$user' and level='TOP_AGENT';";
				$rslt=mysql_query($stmtB, $link);
				}

			echo "User Territory Added: $user $territory<BR>\n";

			### LOG INSERTION Admin Log Table ###
			$SQL_log = "$stmt|$stmtB|";
			$SQL_log = ereg_replace(';','',$SQL_log);
			$SQL_log = addslashes($SQL_log);
			$stmt="INSERT INTO vicidial_admin_log set event_date='$NOW_TIME', user='$USER', ip_address='$ip', event_section='TERRITORIES', event_type='ADD', record_id='$territory', event_code='ADMIN ADD USER TERRITORY', event_sql=\"$SQL_log\", event_notes='';";
			if ($DB) {echo "|$stmt|\n";}
			$rslt=mysql_query($stmt, $link);
			}
		}
	$action = "MODIFY_TERRITORY";
	}
### END process add user territory page


### BEGIN process modify user territory page
if ($action == "PROCESS_MODIFY_USER_TERRITORY")
	{
	if ( (strlen($territory)<1) or (strlen($user)<1) or (strlen($level)<1) )
		{
		echo "ERROR: Territory, User and Level must be filled in<BR>\n";
		}
	else
		{
		$stmt="SELECT count(*) from vicidial_user_territories where territory='$territory' and user='$user';";
		$rslt=mysql_query($stmt, $link);
		$row=mysql_fetch_row($rslt);
		if ($row[0] < 0)
			{
			echo "ERROR: this territory user is not in the system<BR>\n";
			}
		else
			{
			$stmt="UPDATE vicidial_user_territories SET level='$level' where territory='$territory' and user='$user';";
			$rslt=mysql_query($stmt, $link);

			if ($level == "TOP_AGENT")
				{
				$stmtB="UPDATE vicidial_user_territories SET level='STANDARD_AGENT' where territory='$territory' and user!='$user' and level='TOP_AGENT';";
				$rslt=mysql_query($stmtB, $link);
				}

			echo "User Territory Modified: $user $territory<BR>\n";

			### LOG INSERTION Admin Log Table ###
			$SQL_log = "$stmt|$stmtB|";
			$SQL_log = ereg_replace(';','',$SQL_log);
			$SQL_log = addslashes($SQL_log);
			$stmt="INSERT INTO vicidial_admin_log set event_date='$NOW_TIME', user='$USER', ip_address='$ip', event_section='TERRITORIES', event_type='MODIFY', record_id='$territory', event_code='ADMIN MODIFY USER TERRITORY', event_sql=\"$SQL_log\", event_notes='';";
			if ($DB) {echo "|$stmt|\n";}
			$rslt=mysql_query($stmt, $link);
			}
		}
	$action = "MODIFY_TERRITORY";
	}
### END process modify user territory page


### BEGIN delete user territory page
if ($action == "DELETE_USER_TERRITORY")
	{
	if ( (strlen($territory)<1) or (strlen($user)<1) )
		{
		echo "ERROR: Territory and User must be filled in<BR>\n";
		}
	else
		{
		$stmt="SELECT count(*) from vicidial_user_territories where territory='$territory' and user='$user';";
		$rslt=mysql_query($stmt, $link);
		$row=mysql_fetch_row($rslt);
		if ($row[0] < 0)
			{
			echo "ERROR: this territory user is not in the system<BR>\n";
			}
		else
			{
			$stmt="DELETE from vicidial_user_territories where territory='$territory' and user='$user';";
			$rslt=mysql_query($stmt, $link);

			echo "User Territory Deleted: $user $territory<BR>\n";

			### LOG INSERTION Admin Log Table ###
			$SQL_log = "$stmt|$stmtB|";
			$SQL_log = ereg_replace(';','',$SQL_log);
			$SQL_log = addslashes($SQL_log);
			$stmt="INSERT INTO vicidial_admin_log set event_date='$NOW_TIME', user='$USER', ip_address='$ip', event_section='TERRITORIES', event_type='DELETE', record_id='$territory', event_code='ADMIN DELETE USER TERRITORY', event_sql=\"$SQL_log\", event_notes='';";
			if ($DB) {echo "|$stmt|\n";}
			$rslt=mysql_query($stmt, $link);
			}
		}
	$action = "MODIFY_TERRITORY";
	}
### END delete user territory page






### BEGIN add territory page
if ($action == "ADD_TERRITORY")
	{
	echo "<TABLE><TR><TD>\n";
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";

	echo "<br>Add Territory<form action=$PHP_SELF method=POST>\n";
	echo "<input type=hidden name=action value=PROCESS_ADD_TERRITORY>\n";
	echo "<center><TABLE width=$section_width cellspacing=3>\n";
	echo "<tr bgcolor=#B6D3FC><td align=right>Territory: </td><td align=left><input type=text name=territory size=30 maxlength=100></td></tr>\n";
	echo "<tr bgcolor=#B6D3FC><td align=right>Territory Description: </td><td align=left><input type=text name=territory_description size=50 maxlength=255></td></tr>\n";
	echo "<tr bgcolor=#B6D3FC><td align=center colspan=2><input type=submit name=SUBMIT value=SUBMIT></td></tr>\n";
	echo "</TABLE></center>\n";
	}
### END add territory page


### BEGIN process add territory page
if ($action == "PROCESS_ADD_TERRITORY")
	{
	if ( (strlen($territory)<1) or (strlen($territory_description)<1) )
		{
		echo "ERROR: Territory and Territory Description must be filled in<BR>\n";
		}
	else
		{
		$stmt="SELECT count(*) from vicidial_territories where territory='$territory';";
		$rslt=mysql_query($stmt, $link);
		$row=mysql_fetch_row($rslt);
		if ($row[0] > 0)
			{
			echo "ERROR: there is already a territory in the system with this name<BR>\n";
			}
		else
			{
			$stmt="INSERT INTO vicidial_territories SET territory='$territory',territory_description='$territory_description';";
			$rslt=mysql_query($stmt, $link);

			echo "Territory Added: $territory<BR>\n";

			### LOG INSERTION Admin Log Table ###
			$SQL_log = "$stmt|";
			$SQL_log = ereg_replace(';','',$SQL_log);
			$SQL_log = addslashes($SQL_log);
			$stmt="INSERT INTO vicidial_admin_log set event_date='$NOW_TIME', user='$USER', ip_address='$ip', event_section='TERRITORIES', event_type='ADD', record_id='$territory', event_code='ADMIN ADD TERRITORY', event_sql=\"$SQL_log\", event_notes='';";
			if ($DB) {echo "|$stmt|\n";}
			$rslt=mysql_query($stmt, $link);
			}
		}
	$action = "LIST_ALL_TERRITORIES";
	}
### END process add territory page


### BEGIN delete territory page
if ($action == "DELETE_TERRITORY")
	{
	if (strlen($territory)<1)
		{
		echo "ERROR: Territory must be filled in<BR>\n";
		}
	else
		{
		$stmt="SELECT count(*) from vicidial_territories where territory='$territory';";
		$rslt=mysql_query($stmt, $link);
		$row=mysql_fetch_row($rslt);
		if ($row[0] < 0)
			{
			echo "ERROR: This territory is not in the system with this name<BR>\n";
			}
		else
			{
			$stmt="DELETE from vicidial_territories where territory='$territory';";
			$rslt=mysql_query($stmt, $link);

			echo "Territory Deleted: $territory<BR>\n";

			### LOG INSERTION Admin Log Table ###
			$SQL_log = "$stmt|";
			$SQL_log = ereg_replace(';','',$SQL_log);
			$SQL_log = addslashes($SQL_log);
			$stmt="INSERT INTO vicidial_admin_log set event_date='$NOW_TIME', user='$USER', ip_address='$ip', event_section='TERRITORIES', event_type='DELETE', record_id='$territory', event_code='ADMIN DELETE TERRITORY', event_sql=\"$SQL_log\", event_notes='';";
			if ($DB) {echo "|$stmt|\n";}
			$rslt=mysql_query($stmt, $link);
			}
		}
	$action = "LIST_ALL_TERRITORIES";
	}
### END delete territory page


### BEGIN process modify territory page
if ($action == "PROCESS_MODIFY_TERRITORY")
	{
	if ( (strlen($territory)<1) or (strlen($territory_description)<1) )
		{
		echo "ERROR: Territory and Territory Description must be filled in<BR>\n";
		}
	else
		{
		$stmt="SELECT count(*) from vicidial_territories where territory='$territory';";
		$rslt=mysql_query($stmt, $link);
		$row=mysql_fetch_row($rslt);
		if ($row[0] < 1)
			{
			echo "ERROR: This territory is not in the system with this name<BR>\n";
			}
		else
			{
			$stmt="UPDATE vicidial_territories SET territory_description='$territory_description' where territory='$territory';";
			$rslt=mysql_query($stmt, $link);

			echo "Territory Modified: $territory<BR>\n";

			### LOG INSERTION Admin Log Table ###
			$SQL_log = "$stmt|";
			$SQL_log = ereg_replace(';','',$SQL_log);
			$SQL_log = addslashes($SQL_log);
			$stmt="INSERT INTO vicidial_admin_log set event_date='$NOW_TIME', user='$USER', ip_address='$ip', event_section='TERRITORIES', event_type='MODIFY', record_id='$territory', event_code='ADMIN MODIFY TERRITORY', event_sql=\"$SQL_log\", event_notes='';";
			if ($DB) {echo "|$stmt|\n";}
			$rslt=mysql_query($stmt, $link);
			}
		}
	$action = "MODIFY_TERRITORY";
	}
### END process modify territory page


### BEGIN modify territory page
if ($action == "MODIFY_TERRITORY")
	{
	$stmt="SELECT territory,territory_description from vicidial_territories where territory='$territory';";
	$rslt=mysql_query($stmt, $link);
	$territories_to_print = mysql_num_rows($rslt);
	if ($territories_to_print > 0) 
		{
		$rowx=mysql_fetch_row($rslt);

		echo "<TABLE><TR><TD>\n";
		echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";

		echo "<br>Modify Territory<form action=$PHP_SELF method=POST>\n";
		echo "<input type=hidden name=action value=PROCESS_MODIFY_TERRITORY>\n";
		echo "<input type=hidden name=territory value=\"$territory\">\n";
		echo "<center><TABLE width=$section_width cellspacing=3>\n";
		echo "<tr bgcolor=#B6D3FC><td align=right>Territory: </td><td align=left><B>$rowx[0]</B></td></tr>\n";
		echo "<tr bgcolor=#B6D3FC><td align=right>Territory Description: </td><td align=left><input type=text name=territory_description size=50 maxlength=255 value=\"$rowx[1]\"></td></tr>\n";

		$stmt = "SELECT count(*) FROM vicidial_user_territories where territory='$territory';";
		$rslt=mysql_query($stmt, $link);
		if ($DB) {echo "$stmt\n";}
		$vut_ct = mysql_num_rows($rslt);
		if ($vut_ct > 0)
			{
			$row=mysql_fetch_row($rslt);
			$user_count =			$row[0];
			}
		echo "<tr bgcolor=#B6D3FC><td align=right>Agents: </td><td align=left><B>$user_count</B></td></tr>";

		$owner_count=0;
		$stmt = "SELECT count(*) FROM vicidial_list where owner='$territory';";
		$rslt=mysql_query($stmt, $link);
		if ($DB) {echo "$stmt\n";}
		$VLo_ct = mysql_num_rows($rslt);
		if ($VLo_ct > 0)
			{
			$row=mysql_fetch_row($rslt);
			$owner_count =			$row[0];
			}
		echo "<tr bgcolor=#B6D3FC><td align=right>Accounts: </td><td align=left><B>$owner_count</B></td></tr>";

		if ($enable_vtiger_integration > 0)
			{
			$stmtV = "SELECT count(*) FROM vtiger_account where tickersymbol='$territory';";
			$rsltV=mysql_query($stmtV, $linkV);
			if ($DB) {echo "$stmtV\n";}
			$vta_ct = mysql_num_rows($rsltV);
			if ($vta_ct > 0)
				{
				$row=mysql_fetch_row($rsltV);
				$vtiger_count =			$row[0];
				}
			echo "<tr bgcolor=#B6D3FC><td align=right>Vtiger Accounts: </td><td align=left><B>$vtiger_count</B></td></tr>";
			}
		echo "<tr bgcolor=#B6D3FC><td align=center colspan=2><input type=submit name=SUBMIT value=SUBMIT></form></td></tr>\n";
		echo "</TABLE></center>\n";
		echo "<BR><BR>\n";

		echo "<TABLE CELLPADDING=3 CELLSPACING=1 WIDTH=600><TR><TD COLSPAN=5><FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>Users in this Territory:</TD></TR>\n";

		$stmt="SELECT vut.user,level,full_name from vicidial_user_territories vut,vicidial_users vu where vut.territory='$territory' and vut.user=vu.user order by vu.user;";
		$rslt=mysql_query($stmt, $link);
		$territories_to_print = mysql_num_rows($rslt);
		$o=0;
		while ($territories_to_print > $o) 
			{
			$rowx=mysql_fetch_row($rslt);
			$Tuser[$o] =		$rowx[0];
			$Tlevel[$o] =		$rowx[1];
			$Tfull_name[$o] =	$rowx[2];
			$o++;
			}
		$o=0;
		while ($territories_to_print > $o) 
			{
			$rowx=mysql_fetch_row($rslt);
			$p++;

			if (eregi("1$|3$|5$|7$|9$", $p))
				{$bgcolor='bgcolor="#B9CBFD"';} 
			else
				{$bgcolor='bgcolor="#9BB9FB"';}
			echo "<TR $bgcolor><TD><font size=1>$p</TD>";
			echo "<TD><a href=\"admin.php?ADD=3&user=$Tuser[$o]\">$Tuser[$o]</a></TD>";
			echo "<TD>$Tfull_name[$o]</TD>";
			if ($enable_vtiger_integration > 0)
				{
				$stmtV="SELECT id from vtiger_users where user_name='$Tuser[$o]';";
				$rsltV=mysql_query($stmtV, $linkV);
				if ($DB) {echo "$stmtV\n";}
				$vtu_ct = mysql_num_rows($rsltV);
				if ($vtu_ct > 0)
					{
					$row=mysql_fetch_row($rsltV);
					$user_id =		$row[0];

					$stmtV = "SELECT count(*) FROM vtiger_account where tickersymbol='$territory' and accountid IN(SELECT crmid from vtiger_crmentity where smownerid='$user_id');";
					$rsltV=mysql_query($stmtV, $linkV);
					if ($DB) {echo "$stmtV\n";}
					$vca_ct = mysql_num_rows($rsltV);
					if ($vca_ct > 0)
						{
						$row=mysql_fetch_row($rsltV);
						echo "<TD>VT Accounts: $row[0]</TD>";
						}
					}
				}

		#	$owner_count=0;
		#	$stmt = "SELECT count(*) FROM vicidial_list where owner='$territory';";
		#	$rslt=mysql_query($stmt, $link);
		#	if ($DB) {echo "$stmt\n";}
		#	$VLo_ct = mysql_num_rows($rslt);
		#	if ($VLo_ct > 0)
		#		{
		#		$row=mysql_fetch_row($rslt);
		#		$owner_count =			$row[0];
		#		}
		#	echo "<TD>Accounts: $owner_count</TD>";

			echo "<TD>";
			echo "<form action=$PHP_SELF method=POST>";
			echo "<input type=hidden name=action value=PROCESS_MODIFY_USER_TERRITORY>";
			echo "<input type=hidden name=territory value=\"$territory\">";
			echo "<input type=hidden name=user value=\"$Tuser[$o]\">";
			echo "<select size=1 name=level><option SELECTED>$Tlevel[$o]</option><option>TOP_AGENT</option><option>STANDARD_AGENT</option><option>BOTTOM_AGENT</option></select> ";
			echo "<input type=submit name=submit value=submit>";
			echo "</form>";
			echo "</TD>";
			echo "<TD><a href=\"$PHP_SELF?action=DELETE_USER_TERRITORY&territory=$territory&user=$Tuser[$o]\">DELETE</a></TD>";
			echo "</TR>\n";
			$o++;
			}

		echo "</TABLE>\n";
		echo "<BR><BR><BR>\n";

		echo "<a href=\"$PHP_SELF?action=DELETE_TERRITORY&territory=$territory\">Delete This Territory</a>\n";
		echo "<BR><BR>\n";
		}
	else
		{
		echo "ERROR: Territory not found: $territory<BR>\n";
		}
	}
### END modify territory page





### BEGIN list all territories in the system
if ($action == "LIST_ALL_TERRITORIES")
	{
	echo "<TABLE><TR><TD>\n";
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";
	echo "<br>List All Territories:\n";
	echo "<center><TABLE width=$section_width cellspacing=0 cellpadding=1>\n";
	echo "<TR BGCOLOR=BLACK>";
	echo "<TD><B><FONT FACE=\"Arial,Helvetica\" size=1 color=white>#</B></TD>";
	echo "<TD><B><FONT FACE=\"Arial,Helvetica\" size=1 color=white>ID</B></TD>";
	echo "<TD><B><FONT FACE=\"Arial,Helvetica\" size=1 color=white>TERRITORY</B></TD>";
	echo "<TD><B><FONT FACE=\"Arial,Helvetica\" size=1 color=white>DESCRIPTION</B></TD>\n";
	echo "<TD><B><FONT FACE=\"Arial,Helvetica\" size=1 color=white>AGENTS</TD>\n";
	echo "<TD><B><FONT FACE=\"Arial,Helvetica\" size=1 color=white>ACCOUNTS</TD>\n";
	if ($enable_vtiger_integration > 0)
		{
		echo "<TD><B><FONT FACE=\"Arial,Helvetica\" size=1 color=white>VT ACCOUNTS</TD>\n";
		}
	echo "</TR>\n";

	$stmt = "SELECT territory_id,territory,territory_description FROM vicidial_territories order by territory;";
	$rslt=mysql_query($stmt, $link);
	if ($DB) {echo "$stmt\n";}
	$vt_ct = mysql_num_rows($rslt);
	$i=0;
	while ($vt_ct > $i)
		{
		$row=mysql_fetch_row($rslt);
		$Lterritory_id[$i] =			$row[0];
		$Lterritory[$i] =				$row[1];
		$Lterritory_description[$i] =	$row[2];
		$i++;
		}
	$i=0;
	while ($vt_ct > $i)
		{
		$stmt = "SELECT count(*) FROM vicidial_user_territories where territory='$Lterritory[$i]';";
		$rslt=mysql_query($stmt, $link);
		if ($DB) {echo "$stmt\n";}
		$vut_ct = mysql_num_rows($rslt);
		if ($vut_ct > 0)
			{
			$row=mysql_fetch_row($rslt);
			$Lterritory_count[$i] =			$row[0];
			}

		$stmt = "SELECT count(*) FROM vicidial_list where owner='$Lterritory[$i]';";
		$rslt=mysql_query($stmt, $link);
		if ($DB) {echo "$stmt\n";}
		$VLo_ct = mysql_num_rows($rslt);
		if ($VLo_ct > 0)
			{
			$row=mysql_fetch_row($rslt);
			$Lterritory_owner_count[$i] =	$row[0];
			}

		if ($enable_vtiger_integration > 0)
			{
			$stmtV = "SELECT count(*) FROM vtiger_account where tickersymbol='$Lterritory[$i]';";
			$rsltV=mysql_query($stmtV, $linkV);
			if ($DB) {echo "$stmtV\n";}
			$va_ct = mysql_num_rows($rsltV);
			if ($va_ct > 0)
				{
				$row=mysql_fetch_row($rsltV);
				$Lvtiger_count[$i] =			$row[0];
				}
			}

		if (eregi("1$|3$|5$|7$|9$", $i))
			{$bgcolor='bgcolor="#B9CBFD"';} 
		else
			{$bgcolor='bgcolor="#9BB9FB"';}
		echo "<tr $bgcolor><td><font size=1>$i</td>";
		echo "<td><font size=1> $Lterritory_id[$i] </td>";
		echo "<td><font size=1> <a href=\"$PHP_SELF?action=MODIFY_TERRITORY&territory=$Lterritory[$i]\">$Lterritory[$i]</a></td>";
		echo "<td><font size=1> $Lterritory_description[$i]</td>";
		echo "<td><font size=1> $Lterritory_count[$i]</td>";
		echo "<td><font size=1> $Lterritory_owner_count[$i]</td>";
		if ($enable_vtiger_integration > 0)
			{
			echo "<td><font size=1> $Lvtiger_count[$i]</td>";
			}
		echo "</tr>\n";

		$i++;
		}
	echo "</TABLE><BR><BR>\n";
	echo "\n";
	echo "</center>\n";
	}
### END list all territories in the system








?>



<BR><font size=1>User Territories &nbsp; &nbsp; VERSION: <?php echo $version ?> &nbsp; &nbsp; BUILD: <?php echo $build ?> &nbsp; &nbsp; </td></tr>
</TD></TR></TABLE>
