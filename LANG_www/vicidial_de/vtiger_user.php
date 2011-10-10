<?php
# vtiger_user.php - script used to synchronize the users from the VICIDIAL
#                   vicidial_users table into the Vtiger system as well as
#                   the groups from VICIDIAL to Vtiger
#
# Copyright (C) 2009  Matt Florell <vicidial@gmail.com>    LICENSE: AGPLv2
#
# CHANGES
# 81231-1307 - First build
# 90228-2152 - Added Groups support
# 90508-0644 - Changed to PHP long tags
#

header ("Content-type: text/html; charset=utf-8");

require("dbconnect.php");

$PHP_AUTH_USER=$_SERVER['PHP_AUTH_USER'];
$PHP_AUTH_PW=$_SERVER['PHP_AUTH_PW'];
$PHP_SELF=$_SERVER['PHP_SELF'];


#$DB = '1';	# DEBUG override
$US = '_';
$STARTtime = date("U");
$TODAY = date("Y-m-d");
$NOW_TIME = date("Y-m-d H:i:s");
$REC_TIME = date("Ymd-His");
$FILE_datetime = $STARTtime;
$parked_time = $STARTtime;

###############################################################
##### START SYSTEM_SETTINGS VTIGER CONNECTION INFO LOOKUP #####
$stmt = "SELECT enable_vtiger_integration,vtiger_server_ip,vtiger_dbname,vtiger_login,vtiger_pass,vtiger_url FROM system_settings;";
$rslt=mysql_query($stmt, $link);
if ($DB) {echo "$stmt\n";}
$ss_conf_ct = mysql_num_rows($rslt);
if ($ss_conf_ct > 0)
	{
	$row=mysql_fetch_row($rslt);
	$enable_vtiger_integration =	$row[0];
	$vtiger_server_ip	=			$row[1];
	$vtiger_dbname =				$row[2];
	$vtiger_login =					$row[3];
	$vtiger_pass =					$row[4];
	$vtiger_url =					$row[5];
	}
##### END SYSTEM_SETTINGS VTIGER CONNECTION INFO LOOKUP #####
#############################################################

echo "<html>\n";
echo "<head>\n";
echo "<title>VICIDIAL Vtiger user synchronization utility</title>\n";
echo "<META HTTP-EQUIV=\"Content-Type\" CONTENT=\"text/html; charset=utf-8\">\n";

if ($enable_vtiger_integration < 1)
	{
	echo "<B>ERROR! - Vtiger integration is disabled in the VICIDIAL system_settings";
	exit;
	}

##### grab the existing user_groups in the vicidial_user_groups table
$stmt="SELECT user_group,group_name FROM vicidial_user_groups;";
$rslt=mysql_query($stmt, $link);
if ($DB) {echo "$stmt\n";}
$VD_groups_ct = mysql_num_rows($rslt);
$i=0;
while ($i < $VD_groups_ct)
	{
	$row=mysql_fetch_row($rslt);
	$UGid[$i] =		$row[0];
	$UGname[$i] =	$row[1];
	$i++;
	}

##### grab the existing users in the vicidial_users table
$stmt="SELECT user,pass,full_name,user_level,active,user_group FROM vicidial_users;";
$rslt=mysql_query($stmt, $link);
if ($DB) {echo "$stmt\n";}
$VD_users_ct = mysql_num_rows($rslt);
$i=0;
while ($i < $VD_users_ct)
	{
	$row=mysql_fetch_row($rslt);
	$user[$i] =			$row[0];
	$pass[$i] =			$row[1];
	$full_name[$i] =	$row[2];   while (strlen($full_name[$i])>30) {$full_name[$i] = eregi_replace(".$",'',$full_name[$i]);}
	$user_level[$i] =	$row[3];
	$active[$i] =		$row[4];
	$user_group[$i] =	$row[5];
	$i++;
	}


### connect to your vtiger database
$linkV=mysql_connect("$vtiger_server_ip", "$vtiger_login","$vtiger_pass");
if (!$linkV) {die("Could not connect: $vtiger_server_ip|$vtiger_dbname|$vtiger_login|$vtiger_pass" . mysql_error());}
echo "Connected successfully\n<BR>\n";
mysql_select_db("$vtiger_dbname", $linkV);


##########################
### BEGIN Group export
$i=0;
while ($i < $VD_groups_ct)
	{
	$VTgroup_name =			$UGid[$i];
	$VTgroup_description =	$UGname[$i];

	$stmt="SELECT count(*) from vtiger_groups where groupname='$VTgroup_name';";
	$rslt=mysql_query($stmt, $linkV);
	if ($DB) {echo "$stmt\n";}
	if (!$rslt) {die('Could not execute: ' . mysql_error());}
	$row=mysql_fetch_row($rslt);
	$group_found_count = $row[0];

	### group exists in vtiger, grab groupid, update description
	if ($group_found_count > 0)
		{
		$stmt="SELECT groupid from vtiger_groups where groupname='$VTgroup_name';";
		$rslt=mysql_query($stmt, $linkV);
		if ($DB) {echo "$stmt\n";}
		if (!$rslt) {die('Could not execute: ' . mysql_error());}
		$row=mysql_fetch_row($rslt);
		$groupid = $row[0];
		$VTugID[$i] = $groupid;

		$stmtA = "UPDATE vtiger_groups SET description='$VTgroup_description' where groupid='$groupid';";
		if ($DB) {echo "|$stmtA|\n";}
		$rslt=mysql_query($stmtA, $linkV);
		if (!$rslt) {die('Could not execute: ' . mysql_error());}

		echo "GROUP- $VTgroup_name: $groupid<BR>\n";
		echo "<BR>\n";
		}

	### group doesn't exist in vtiger, insert it
	else
		{
		#### BEGIN CREATE NEW GROUP RECORD IN VTIGER

		# Get next available id from vtiger_users_seq to use as groupid
		$stmt="SELECT id from vtiger_users_seq;";
		if ($DB) {echo "$stmt\n";}
		$rslt=mysql_query($stmt, $linkV);
		$row=mysql_fetch_row($rslt);
		$groupid = ($row[0] + 1);
		if (!$rslt) {die('Could not execute: ' . mysql_error());}
		$VTugID[$i] = $groupid;

		# Increase next available groupid with 1 so next record gets proper id
		$stmt="UPDATE vtiger_users_seq SET id = '$groupid';";
		if ($DB) {echo "$stmt\n";}
		$rslt=mysql_query($stmt, $linkV);
		if (!$rslt) {die('Could not execute: ' . mysql_error());}

		$stmtA = "INSERT INTO vtiger_groups SET groupid='$groupid',groupname='$VTgroup_name',description='$VTgroup_description';";
		if ($DB) {echo "|$stmtA|\n";}
		$rslt=mysql_query($stmtA, $linkV);
		if (!$rslt) {die('Could not execute: ' . mysql_error());}

		echo "GROUP- $VTgroup_name: $groupid<BR>\n";
		echo "<BR>\n";
		#### END CREATE NEW GROUP RECORD IN VTIGER
		}
	$i++;
	}
### END Group export
##########################



##########################
### BEGIN User export
$i=0;
while ($i < $VD_users_ct)
	{
	$user_name =		$user[$i];
	$VUgroup =			$user_group[$i];
	$user_password =	$pass[$i];
	$last_name =		$full_name[$i];
	$is_admin =			'off';
	$roleid =			'H5';
	$status =			'Active';
	$groupid =			'1';
		if ($user_level[$i] >= 7) {$roleid = 'H4';}
		if ($user_level[$i] >= 8) {$roleid = 'H3';}
		if ($user_level[$i] >= 9) {$roleid = 'H2';}
		if ($user_level[$i] >= 9) {$is_admin = 'on';}
		if (ereg('N',$active[$i])) {$status = 'Inactive';}
	$salt = substr($user_name, 0, 2);
	$salt = '$1$' . $salt . '$';
	$encrypted_password = crypt($user_password, $salt);
	$i++;

	$j=0;
	$all_VICIDIAL_groups_SQL='';
	while ($j < $VD_groups_ct)
		{
		if ( (eregi("$UGid[$j]",$VUgroup)) and ( (strlen($UGid[$j]))==(strlen($VUgroup)) ) )
			{
			$groupid =				$VTugID[$j];
			$VTgroup_name =			$UGid[$j];
			$VTgroup_description =	$UGname[$j];
			}
		else
			{$all_VICIDIAL_groups_SQL .= "'$VTugID[$j]',";}
		$j++;
		}
	$all_VICIDIAL_groups_SQL = preg_replace("/.$/",'',$all_VICIDIAL_groups_SQL);

	$stmt="SELECT count(*) from vtiger_users where user_name='$user_name';";
	$rslt=mysql_query($stmt, $linkV);
	if ($DB) {echo "$stmt\n";}
	if (!$rslt) {die('Could not execute: ' . mysql_error());}
	$row=mysql_fetch_row($rslt);
	$found_count = $row[0];

	### user exists in vtiger, update it
	if ($found_count > 0)
		{
		$stmt="SELECT id from vtiger_users where user_name='$user_name';";
		$rslt=mysql_query($stmt, $linkV);
		if ($DB) {echo "$stmt\n";}
		if (!$rslt) {die('Could not execute: ' . mysql_error());}
		$row=mysql_fetch_row($rslt);
		$userid = $row[0];

		$stmt="SELECT count(*) from vtiger_users2group WHERE userid='$userid' and groupid='$groupid';";
		$rslt=mysql_query($stmt, $linkV);
		if ($DB) {echo "$stmt\n";}
		if (!$rslt) {die('Could not execute: ' . mysql_error());}
		$row=mysql_fetch_row($rslt);
		$usergroupcount = $row[0];

		$stmtA = "UPDATE vtiger_users SET user_password='$encrypted_password',last_name='$last_name',is_admin='$is_admin',status='$status' where id='$userid';";
		if ($DB) {echo "|$stmtA|\n";}
		$rslt=mysql_query($stmtA, $linkV);
		if (!$rslt) {die('Could not execute: ' . mysql_error());}

		$stmtB = "UPDATE vtiger_user2role SET roleid='$roleid' where userid='$userid';";
		if ($DB) {echo "|$stmtB|\n";}
		$rslt=mysql_query($stmtB, $linkV);
		if (!$rslt) {die('Could not execute: ' . mysql_error());}

		if ($usergroupcount < 1)
			{
			$stmtC = "DELETE FROM vtiger_users2group WHERE userid='$userid' and groupid IN($all_VICIDIAL_groups_SQL);";
			if ($DB) {echo "|$stmtC|\n";}
			$rslt=mysql_query($stmtC, $linkV);
			if (!$rslt) {die('Could not execute: ' . mysql_error());}

			$stmtD = "INSERT INTO vtiger_users2group SET userid='$userid',groupid='$groupid';";
			if ($DB) {echo "|$stmtC|\n";}
			$rslt=mysql_query($stmtD, $linkV);
			if (!$rslt) {die('Could not execute: ' . mysql_error());}
			}
		else
			{$stmtC='';}

		echo "$user_name: $userid<BR>\n";
		echo "$stmtA<BR>\n";
		echo "$stmtB<BR>\n";
		echo "$stmtC<BR>\n";
		echo "$stmtD<BR>\n";
		echo "<BR>\n";

		}

	### user doesn't exist in vtiger, insert it
	else
		{
		#### BEGIN CREATE NEW USER RECORD IN VTIGER
		$stmtA = "INSERT INTO vtiger_users SET user_name='$user_name',user_password='$encrypted_password',last_name='$last_name',is_admin='$is_admin',status='$status',date_format='yyyy-mm-dd',first_name='',reports_to_id='',description='',title='',department='',phone_home='',phone_mobile='',phone_work='',phone_other='',phone_fax='',email1='',email2='',yahoo_id='',signature='',address_street='',address_city='',address_state='',address_country='',address_postalcode='',user_preferences='',imagename='';";
		if ($DB) {echo "|$stmtA|\n";}
		$rslt=mysql_query($stmtA, $linkV);
		if (!$rslt) {die('Could not execute: ' . mysql_error());}
		$userid = mysql_insert_id($linkV);
	
		$stmtB = "INSERT INTO vtiger_user2role SET userid='$userid',roleid='$roleid';";
		if ($DB) {echo "|$stmtB|\n";}
		$rslt=mysql_query($stmtB, $linkV);
		if (!$rslt) {die('Could not execute: ' . mysql_error());}

		$stmtC = "INSERT INTO vtiger_users2group SET userid='$userid',groupid='$groupid';";
		if ($DB) {echo "|$stmtC|\n";}
		$rslt=mysql_query($stmtC, $linkV);
		if (!$rslt) {die('Could not execute: ' . mysql_error());}

		$stmtD = "UPDATE vtiger_users_seq SET id='$userid';";
		if ($DB) {echo "|$stmtD|\n";}
		$rslt=mysql_query($stmtD, $linkV);
		if (!$rslt) {die('Could not execute: ' . mysql_error());}

		echo "$user_name:<BR>\n";
		echo "$stmtA<BR>\n";
		echo "$stmtB<BR>\n";
		echo "$stmtC<BR>\n";
		echo "$stmtD<BR>\n";
		echo "<BR>\n";
		#### END CREATE NEW USER RECORD IN VTIGER
		}


	}
### END User export
##########################





echo "DONE\n";

exit;


