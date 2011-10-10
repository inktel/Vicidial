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
# 100508-1439 - Added header row to output
# 100702-1335 - Added custom fields
# 100712-1324 - Added system setting slave server option
# 100802-2347 - Added User Group Allowed Reports option validation
# 100804-1745 - Added option to download DNC and FPGN lists
# 100924-1609 - Added ALL-LISTS option for downloading everything
# 100929-1919 - Fixed ALL-LISTS download option to include custom fields
# 101004-2108 - Added generic custom column headers for custom fields
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
if (isset($_GET["ENVIAR"]))					{$ENVIAR=$_GET["ENVIAR"];}
	elseif (isset($_POST["ENVIAR"]))		{$ENVIAR=$_POST["ENVIAR"];}
if (isset($_GET["group_id"]))				{$group_id=$_GET["group_id"];}
	elseif (isset($_POST["group_id"]))		{$group_id=$_POST["group_id"];}
if (isset($_GET["download_type"]))			{$download_type=$_GET["download_type"];}
	elseif (isset($_POST["download_type"]))	{$download_type=$_POST["download_type"];}

if (strlen($shift)<2) {$shift='ALL';}
if ($group_id=='SYSTEM_INTERNAL') {$download_type='systemdnc';}

$report_name = 'Download List';
$db_source = 'M';

#############################################
##### START SYSTEM_SETTINGS LOOKUP #####
$stmt = "SELECT use_non_latin,outbound_autodial_active,slave_db_server,reports_use_slave_db,custom_fields_enabled FROM system_settings;";
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
	$custom_fields_enabled =		$row[4];
	}
##### END SETTINGS LOOKUP #####
###########################################

if ( (strlen($slave_db_server)>5) and (preg_match("/$report_name/",$reports_use_slave_db)) )
	{
	mysql_close($link);
	$use_slave_server=1;
	$db_source = 'S';
	require("dbconnect.php");
#	echo "<!-- Using slave server $slave_db_server $db_source -->\n";
	}

$PHP_AUTH_USER = ereg_replace("[^-_0-9a-zA-Z]","",$PHP_AUTH_USER);
$PHP_AUTH_PW = ereg_replace("[^-_0-9a-zA-Z]","",$PHP_AUTH_PW);

$stmt="SELECT count(*) from vicidial_users where user='$PHP_AUTH_USER' and pass='$PHP_AUTH_PW' and user_level > 7 and download_lists='1' and active='Y';";
if ($DB) {echo "|$stmt|\n";}
if ($non_latin > 0) { $rslt=mysql_query("SET NAMES 'UTF8'");}
$rslt=mysql_query($stmt, $link);
$row=mysql_fetch_row($rslt);
$auth=$row[0];

if( (strlen($PHP_AUTH_USER)<2) or (strlen($PHP_AUTH_PW)<2) or (!$auth))
	{
#	Header("WWW-Authenticate: Basic realm=\"VICI-PROJECTS\"");
#	Header("HTTP/1.0 401 Unauthorized");
    echo "Nombre y contraseña inválidos del usuario or no list download permission: |$PHP_AUTH_USER|\n";
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

if ( (!preg_match("/$report_name/",$LOGallowed_reports)) and (!preg_match("/ALL INFORMES/",$LOGallowed_reports)) )
	{
 #   Header("WWW-Authenticate: Basic realm=\"VICI-PROJECTS\"");
 #   Header("HTTP/1.0 401 Unauthorized");
    echo "You are not allowed to view this report: |$PHP_AUTH_USER|$report_name|\n";
    exit;
	}

$LOGallowed_campaignsSQL='';
if ( (!eregi("-ALL",$LOGallowed_campaigns)) )
	{
	$rawLOGallowed_campaignsSQL = preg_replace("/ -/",'',$LOGallowed_campaigns);
	$rawLOGallowed_campaignsSQL = preg_replace("/ /","','",$rawLOGallowed_campaignsSQL);
	$LOGallowed_campaignsSQL = "and campaign_id IN('$rawLOGallowed_campaignsSQL')";
	}

if ($download_type == 'systemdnc')
	{
	##### System DNC list validation #####
	$event_code_type='SYSTEM INTERNAL DNC';
	if (strlen($LOGallowed_campaignsSQL) > 2)
		{
		echo "You are not allowed to download this list: $list_id\n";
		exit;
		}

	$stmt="select count(*) from vicidial_dnc;";
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
		echo "There are no phone numbers in list: SYSTEM INTERNAL DNC\n";
		exit;
		}
	}
elseif ($download_type == 'dnc')
	{
	##### Campaña DNC list validation #####
	$event_code_type='CAMPAIGN DNC';
	$stmt="select count(*) from vicidial_campaigns where campaign_id='$group_id' $LOGallowed_campaignsSQL;";
	$rslt=mysql_query($stmt, $link);
	if ($DB) {echo "$stmt\n";}
	$count_to_print = mysql_num_rows($rslt);
	if ($count_to_print > 0)
		{
		$row=mysql_fetch_row($rslt);
		$lists_allowed =$row[0];
		$i++;
		}

	if ($lists_allowed < 1)
		{
		echo "You are not allowed to download this list: $group_id\n";
		exit;
		}

	$stmt="select count(*) from vicidial_campaign_dnc where campaign_id='$group_id';";
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
		echo "There are no leads in Campaña DNC list: $group_id\n";
		exit;
		}
	}
elseif ($download_type == 'fpgn')
	{
	##### Filter Phone Group list validation #####
	$event_code_type='FILTER PHONE GROUP';
	$stmt="select count(*) from vicidial_filter_phone_groups where filter_phone_group_id='$group_id';";
	$rslt=mysql_query($stmt, $link);
	if ($DB) {echo "$stmt\n";}
	$count_to_print = mysql_num_rows($rslt);
	if ($count_to_print > 0)
		{
		$row=mysql_fetch_row($rslt);
		$lists_allowed =$row[0];
		$i++;
		}

	if ($lists_allowed < 1)
		{
		echo "You are not allowed to download this list: $group_id\n";
		exit;
		}

	$stmt="select count(*) from vicidial_filter_phone_numbers where filter_phone_group_id='$group_id';";
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
		echo "There are no leads in this filter phone group: $group_id\n";
		exit;
		}
	}
else
	{
	##### list download validation #####
	$event_code_type='LIST';
	$stmt="select count(*) from vicidial_lists where list_id='$list_id' $LOGallowed_campaignsSQL;";
	if ($list_id=='ALL-LISTAS')
		{$stmt="select count(*) from vicidial_lists where list_id > 0 $LOGallowed_campaignsSQL;";}
	$rslt=mysql_query($stmt, $link);
	if ($DB) {echo "$stmt\n";}
	$count_to_print = mysql_num_rows($rslt);
	if ($count_to_print > 0)
		{
		$row=mysql_fetch_row($rslt);
		$lists_allowed =$row[0];
		$i++;
		}

	if ($lists_allowed < 1)
		{
		echo "You are not allowed to download this list: $list_id\n";
		exit;
		}

	$stmt="select count(*) from vicidial_list where list_id='$list_id';";
	if ($list_id=='ALL-LISTAS')
		{$stmt="select count(*) from vicidial_list where list_id > 0;";}
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


if ($download_type == 'systemdnc')
	{
	$TXTfilename = "SYSTEM_DNC_$FILE_TIME.txt";
	$header_row = "phone_number";
	$header_columns='';
	$stmt="select phone_number from vicidial_dnc;";
	}
elseif ($download_type == 'dnc')
	{
	$TXTfilename = "DNC_$group_id$US$FILE_TIME.txt";
	$header_row = "phone_number";
	$header_columns='';
	$stmt="select phone_number from vicidial_campaign_dnc where campaign_id='$group_id';";
	}
elseif ($download_type == 'fpgn')
	{
	$TXTfilename = "FPGN_$group_id$US$FILE_TIME.txt";
	$header_row = "phone_number";
	$header_columns='';
	$stmt="select phone_number from vicidial_filter_phone_numbers where filter_phone_group_id='$group_id';";
	}
else
	{
	$TXTfilename = "LIST_$list_id$US$FILE_TIME.txt";
	$list_id_header='';
	$stmt="select lead_id,entry_date,modify_date,status,user,vendor_lead_code,source_id,list_id,gmt_offset_now,called_since_last_reset,phone_code,phone_number,title,first_name,middle_initial,last_name,address1,address2,address3,city,state,province,postal_code,country_code,gender,date_of_birth,alt_phone,email,security_phrase,comments,called_count,last_local_call_time,rank,owner from vicidial_list where list_id='$list_id';";
	if ($list_id=='ALL-LISTAS')
		{
		$list_id_header="list_id\t";   
		$stmt="select list_id,lead_id,entry_date,modify_date,status,user,vendor_lead_code,source_id,list_id,gmt_offset_now,called_since_last_reset,phone_code,phone_number,title,first_name,middle_initial,last_name,address1,address2,address3,city,state,province,postal_code,country_code,gender,date_of_birth,alt_phone,email,security_phrase,comments,called_count,last_local_call_time,rank,owner from vicidial_list where list_id > 0;";
		}
	$header_row = $list_id_header . "lead_id\tentry_date\tmodify_date\tstatus\tuser\tvendor_lead_code\tsource_id\tlist_id\tgmt_offset_now\tcalled_since_last_reset\tphone_code\tphone_number\ttitle\tfirst_name\tmiddle_initial\tlast_name\taddress1\taddress2\taddress3\tcity\tstate\tprovince\tpostal_code\tcountry_code\tgender\tdate_of_birth\talt_phone\temail\tsecurity_phrase\tcomments\tcalled_count\tlast_local_call_time\trank\towner";
	$header_columns='';
	}

// We'll be outputting a TXT file
header('Content-type: application/octet-stream');

// It will be called LIST_101_20090209-121212.txt
header("Content-Disposition: attachment; filename=\"$TXTfilename\"");
header('Expires: 0');
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
header('Pragma: public');
ob_clean();
flush();



$rslt=mysql_query($stmt, $link);
if ($DB) {echo "$stmt\n";}
$leads_to_print = mysql_num_rows($rslt);
$i=0;
while ($i < $leads_to_print)
	{
	$row=mysql_fetch_row($rslt);

	if ( ($download_type == 'systemdnc') or ($download_type == 'dnc') or ($download_type == 'fpgn') )
		{
		$row_data[$i] .= "$row[0]";
		}
	else
		{
		$row[29] = preg_replace("/\n|\r/",'!N',$row[29]);

		if ($list_id=='ALL-LISTAS')
			{
			$row_data[$i] .= "$row[0]\t$row[1]\t$row[2]\t$row[3]\t$row[4]\t$row[5]\t$row[6]\t$row[7]\t$row[8]\t$row[9]\t$row[10]\t$row[11]\t$row[12]\t$row[13]\t$row[14]\t$row[15]\t$row[16]\t$row[17]\t$row[18]\t$row[19]\t$row[20]\t$row[21]\t$row[22]\t$row[23]\t$row[24]\t$row[25]\t$row[26]\t$row[27]\t$row[28]\t$row[29]\t$row[30]\t$row[31]\t$row[32]\t$row[33]\t$row[34]";
			$export_list_id[$i] = $row[0];
			$export_lead_id[$i] = $row[1];
			}
		else
			{
			$row_data[$i] .= "$row[0]\t$row[1]\t$row[2]\t$row[3]\t$row[4]\t$row[5]\t$row[6]\t$row[7]\t$row[8]\t$row[9]\t$row[10]\t$row[11]\t$row[12]\t$row[13]\t$row[14]\t$row[15]\t$row[16]\t$row[17]\t$row[18]\t$row[19]\t$row[20]\t$row[21]\t$row[22]\t$row[23]\t$row[24]\t$row[25]\t$row[26]\t$row[27]\t$row[28]\t$row[29]\t$row[30]\t$row[31]\t$row[32]\t$row[33]";
			$export_list_id[$i] = $list_id;
			$export_lead_id[$i] = $row[0];
			}
		}
	$i++;
	}

$ch=0;
if ( ($custom_fields_enabled > 0) and ($event_code_type=='LIST') )
	{
	$valid_custom_table=0;
	if ($list_id=='ALL-LISTAS')
		{
		$stmtA = "SELECT list_id from vicidial_lists;";
		$rslt=mysql_query($stmtA, $link);
		if ($DB) {echo "$stmtA\n";}
		$lists_ct = mysql_num_rows($rslt);
		$u=0;
		while ($lists_ct > $u)
			{
			$row=mysql_fetch_row($rslt);
			$custom_list_id[$u] =	$row[0];
			$u++;
			}
		$u=0;
		while ($lists_ct > $u)
			{
			$stmt="DEMOSTRACIÓN TABLAS LIKE \"custom_$custom_list_id[$u]\";";
			if ($DB>0) {echo "$stmt";}
			$rslt=mysql_query($stmt, $link);
			$tablecount_to_print = mysql_num_rows($rslt);
			$custom_tablecount[$u] = $tablecount_to_print;
			$u++;
			}
		$u=0;
		while ($lists_ct > $u)
			{
			$custom_columns[$u]=0;
			if ($custom_tablecount[$u] > 0)
				{
				$stmtA = "describe custom_$custom_list_id[$u];";
				$rslt=mysql_query($stmtA, $link);
				if ($DB) {echo "$stmtA\n";}
				$columns_ct = mysql_num_rows($rslt);
				$custom_columns[$u] = $columns_ct;
				}
			if ($DB) {echo "$custom_list_id[$u]|$custom_tablecount[$u]|$custom_columns[$u]\n";}
			$u++;
			}
		$valid_custom_table=1;
		}
	else
		{
		$stmt="DEMOSTRACIÓN TABLAS LIKE \"custom_$list_id\";";
		if ($DB>0) {echo "$stmt";}
		$rslt=mysql_query($stmt, $link);
		$tablecount_to_print = mysql_num_rows($rslt);
		if ($tablecount_to_print > 0) 
			{
			$stmtA = "describe custom_$list_id;";
			$rslt=mysql_query($stmtA, $link);
			if ($DB) {echo "$stmtA\n";}
			$columns_ct = mysql_num_rows($rslt);
			$u=0;
			while ($columns_ct > $u)
				{
				$row=mysql_fetch_row($rslt);
				$column =	$row[0];
				if ($u > 0)
					{$header_columns .= "\t$column";}
				$u++;
				}
			if ($columns_ct > 1)
				{
				$valid_custom_table=1;
				}
			}
		}
	if ($valid_custom_table > 0)
		{
		$i=0;
		while ($i < $leads_to_print)
			{
			if ($list_id=='ALL-LISTAS')
				{
				$valid_custom_table=0;
				$u=0;
				while ($lists_ct > $u)
					{
					if ( ($export_list_id[$i] == "$custom_list_id[$u]") and ($custom_columns[$u] > 1) )
						{
						$valid_custom_table=1;
						$columns_ct = $custom_columns[$u];
						}
					$u++;
					}
				}
			if ($valid_custom_table > 0)
				{
				$stmtA = "SELECT * from custom_$export_list_id[$i] where lead_id='$export_lead_id[$i]' limit 1;";
				$rslt=mysql_query($stmtA, $link);
				if ($DB) {echo "$columns_ct|$stmtA\n";}
				$customfield_ct = mysql_num_rows($rslt);
				if ($customfield_ct > 0)
					{
					$row=mysql_fetch_row($rslt);
					$t=1;
					while ($columns_ct > $t)
						{
						$custom_data[$i] .= "\t$row[$t]";
						if ($ch <= $t)
							{
							$ch++;
							$header_columns .= "\tcustom$ch";
							}
						$t++;
						}
					}

				$custom_data[$i] = preg_replace("/\r\n/",'!N',$custom_data[$i]);
				$custom_data[$i] = preg_replace("/\n/",'!N',$custom_data[$i]);
				}
			$i++;
			}
		}
	}


### LOG INSERTION Admin Log Table ###
$SQL_log = "$stmt|$stmtA|";
$SQL_log = ereg_replace(';','',$SQL_log);
$SQL_log = addslashes($SQL_log);
$stmt="INSERT INTO vicidial_admin_log set event_date='$NOW_TIME', user='$PHP_AUTH_USER', ip_address='$ip', event_section='LEADS', event_type='EXPORT', record_id='$list_id', event_code='ADMIN EXPORT $event_code_type', event_sql=\"$SQL_log\", event_notes='';";
if ($DB) {echo "|$stmt|\n";}
$rslt=mysql_query($stmt, $link);


echo "$header_row$header_columns\r\n";

$i=0;
while ($i < $leads_to_print)
	{
	echo "$row_data[$i]$custom_data[$i]\r\n";

	$i++;
	}

exit;

?>