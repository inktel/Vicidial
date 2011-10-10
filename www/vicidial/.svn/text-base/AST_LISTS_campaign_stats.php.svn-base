<?php 
# AST_LISTS_campaign_stats.php
# 
# Copyright (C) 2010  Matt Florell <vicidial@gmail.com>    LICENSE: AGPLv2
#
# This is a list inventory report, not a calling report. This report will show
# statistics for all of the lists in the selected campaigns
#
# CHANGES
# 100916-0928 - First build
#

header ("Content-type: text/html; charset=utf-8");

require("dbconnect.php");
require("functions.php");

$PHP_AUTH_USER=$_SERVER['PHP_AUTH_USER'];
$PHP_AUTH_PW=$_SERVER['PHP_AUTH_PW'];
$PHP_SELF=$_SERVER['PHP_SELF'];
if (isset($_GET["group"]))				{$group=$_GET["group"];}
	elseif (isset($_POST["group"]))		{$group=$_POST["group"];}
if (isset($_GET["DB"]))					{$DB=$_GET["DB"];}
	elseif (isset($_POST["DB"]))		{$DB=$_POST["DB"];}
if (isset($_GET["submit"]))				{$submit=$_GET["submit"];}
	elseif (isset($_POST["submit"]))	{$submit=$_POST["submit"];}
if (isset($_GET["SUBMIT"]))				{$SUBMIT=$_GET["SUBMIT"];}
	elseif (isset($_POST["SUBMIT"]))	{$SUBMIT=$_POST["SUBMIT"];}

$PHP_AUTH_USER = ereg_replace("[^0-9a-zA-Z]","",$PHP_AUTH_USER);
$PHP_AUTH_PW = ereg_replace("[^0-9a-zA-Z]","",$PHP_AUTH_PW);

$report_name = 'Lists Campaign Statuses Report';
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

if( (strlen($PHP_AUTH_USER)<2) or (strlen($PHP_AUTH_PW)<2) or (!$auth))
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

$i=0;
$group_string='|';
$group_ct = count($group);
while($i < $group_ct)
	{
	$group_string .= "$group[$i]|";
	$i++;
	}

$LOGallowed_campaignsSQL='';
$whereLOGallowed_campaignsSQL='';
if ( (!eregi("-ALL",$LOGallowed_campaigns)) )
	{
	$rawLOGallowed_campaignsSQL = preg_replace("/ -/",'',$LOGallowed_campaigns);
	$rawLOGallowed_campaignsSQL = preg_replace("/ /","','",$rawLOGallowed_campaignsSQL);
	$LOGallowed_campaignsSQL = "and campaign_id IN('$rawLOGallowed_campaignsSQL')";
	$whereLOGallowed_campaignsSQL = "where campaign_id IN('$rawLOGallowed_campaignsSQL')";
	}
$regexLOGallowed_campaigns = " $LOGallowed_campaigns ";

$stmt="select campaign_id,campaign_name from vicidial_campaigns $whereLOGallowed_campaignsSQL order by campaign_id;";
$rslt=mysql_query($stmt, $link);
if ($DB) {echo "$stmt\n";}
$campaigns_to_print = mysql_num_rows($rslt);
$i=0;
while ($i < $campaigns_to_print)
	{
	$row=mysql_fetch_row($rslt);
	$groups[$i] =		$row[0];
	$group_names[$i] =	$row[1];
	if (ereg("-ALL",$group_string) )
		{$group[$i] = $groups[$i];}
	$i++;
	}

$rollover_groups_count=0;
$i=0;
$group_string='|';
$group_ct = count($group);
while($i < $group_ct)
	{
	if ( (preg_match("/ $group[$i] /",$regexLOGallowed_campaigns)) or (preg_match("/-ALL/",$LOGallowed_campaigns)) )
		{
		$group_string .= "$group[$i]|";
		$group_SQL .= "'$group[$i]',";
		$groupQS .= "&group[]=$group[$i]";
		}
	$i++;
	}
if (strlen($group_drop_SQL) < 2)
	{$group_drop_SQL = "''";}
if ( (ereg("--ALL--",$group_string) ) or ($group_ct < 1) or (strlen($group_string) < 2) )
	{
	$group_SQL = "$LOGallowed_campaignsSQL";
	}
else
	{
	$group_SQL = eregi_replace(",$",'',$group_SQL);
	$both_group_SQLand = "and ( (campaign_id IN($group_drop_SQL)) or (campaign_id IN($group_SQL)) )";
	$both_group_SQL = "where ( (campaign_id IN($group_drop_SQL)) or (campaign_id IN($group_SQL)) )";
	$group_SQLand = "and campaign_id IN($group_SQL)";
	$group_SQL = "where campaign_id IN($group_SQL)";
	}

$stmt="select vsc_id,vsc_name from vicidial_status_categories;";
$rslt=mysql_query($stmt, $link);
if ($DB) {echo "$stmt\n";}
$statcats_to_print = mysql_num_rows($rslt);
$i=0;
while ($i < $statcats_to_print)
	{
	$row=mysql_fetch_row($rslt);
	$vsc_id[$i] =	$row[0];
	$vsc_name[$i] =	$row[1];
	$vsc_count[$i] = 0;
	$i++;
	}




### BEGIN gather all statuses that are in status flags  ###
$human_answered_statuses='';
$sale_statuses='';
$dnc_statuses='';
$customer_contact_statuses='';
$not_interested_statuses='';
$unworkable_statuses='';
$stmt="select status,human_answered,sale,dnc,customer_contact,not_interested,unworkable,status_name from vicidial_statuses;";
$rslt=mysql_query($stmt, $link);
if ($DB) {echo "$stmt\n";}
$statha_to_print = mysql_num_rows($rslt);
$i=0;
while ($i < $statha_to_print)
	{
	$row=mysql_fetch_row($rslt);
	$temp_status = $row[0];
	$statname_list["$temp_status"] = "$row[7]";
	if ($row[1]=='Y') {$human_answered_statuses .= "'$temp_status',";}
	if ($row[2]=='Y') {$sale_statuses .= "'$temp_status',";}
	if ($row[3]=='Y') {$dnc_statuses .= "'$temp_status',";}
	if ($row[4]=='Y') {$customer_contact_statuses .= "'$temp_status',";}
	if ($row[5]=='Y') {$not_interested_statuses .= "'$temp_status',";}
	if ($row[6]=='Y') {$unworkable_statuses .= "'$temp_status',";}
	$i++;
	}
$stmt="select status,human_answered,sale,dnc,customer_contact,not_interested,unworkable,status_name from vicidial_campaign_statuses where selectable IN('Y','N') $group_SQLand;";
$rslt=mysql_query($stmt, $link);
if ($DB) {echo "$stmt\n";}
$statha_to_print = mysql_num_rows($rslt);
$i=0;
while ($i < $statha_to_print)
	{
	$row=mysql_fetch_row($rslt);
	$temp_status = $row[0];
	$statname_list["$temp_status"] = "$row[7]";
	if ( ($row[1]=='Y') and (!preg_match("/'$temp_status'/",$human_answered_statuses)) ) {$human_answered_statuses .= "'$temp_status',";}
	if ($row[2]=='Y') {$sale_statuses .= "'$temp_status',";}
	if ($row[3]=='Y') {$dnc_statuses .= "'$temp_status',";}
	if ($row[4]=='Y') {$customer_contact_statuses .= "'$temp_status',";}
	if ($row[5]=='Y') {$not_interested_statuses .= "'$temp_status',";}
	if ($row[6]=='Y') {$unworkable_statuses .= "'$temp_status',";}
	$i++;
	}
if (strlen($human_answered_statuses)>2)		{$human_answered_statuses = substr("$human_answered_statuses", 0, -1);}
else {$human_answered_statuses="''";}
if (strlen($sale_statuses)>2)				{$sale_statuses = substr("$sale_statuses", 0, -1);}
else {$sale_statuses="''";}
if (strlen($dnc_statuses)>2)				{$dnc_statuses = substr("$dnc_statuses", 0, -1);}
else {$dnc_statuses="''";}
if (strlen($customer_contact_statuses)>2)	{$customer_contact_statuses = substr("$customer_contact_statuses", 0, -1);}
else {$customer_contact_statuses="''";}
if (strlen($not_interested_statuses)>2)		{$not_interested_statuses = substr("$not_interested_statuses", 0, -1);}
else {$not_interested_statuses="''";}
if (strlen($unworkable_statuses)>2)			{$unworkable_statuses = substr("$unworkable_statuses", 0, -1);}
else {$unworkable_statuses="''";}

?>

<HTML>
<HEAD>
<STYLE type="text/css">
<!--
   .green {color: white; background-color: green}
   .red {color: white; background-color: red}
   .blue {color: white; background-color: blue}
   .purple {color: white; background-color: purple}
-->
 </STYLE>

<?php 

echo "<META HTTP-EQUIV=\"Content-Type\" CONTENT=\"text/html; charset=utf-8\">\n";
echo "<TITLE>$report_name</TITLE></HEAD><BODY BGCOLOR=WHITE marginheight=0 marginwidth=0 leftmargin=0 topmargin=0>\n";

$short_header=1;

require("admin_header.php");

echo "<TABLE CELLPADDING=4 CELLSPACING=0><TR><TD>";

echo "<FORM ACTION=\"$PHP_SELF\" METHOD=GET name=vicidial_report id=vicidial_report>\n";
echo "<TABLE CELLSPACING=3><TR><TD VALIGN=TOP>";
echo "<INPUT TYPE=HIDDEN NAME=DB VALUE=\"$DB\">\n";

echo "</TD><TD VALIGN=TOP> Campaigns:<BR>";
echo "<SELECT SIZE=5 NAME=group[] multiple>\n";
if  (eregi("--ALL--",$group_string))
	{echo "<option value=\"--ALL--\" selected>-- ALL CAMPAIGNS --</option>\n";}
else
	{echo "<option value=\"--ALL--\">-- ALL CAMPAIGNS --</option>\n";}
$o=0;
while ($campaigns_to_print > $o)
	{
	if (eregi("$groups[$o]\|",$group_string)) {echo "<option selected value=\"$groups[$o]\">$groups[$o] - $group_names[$o]</option>\n";}
	  else {echo "<option value=\"$groups[$o]\">$groups[$o] - $group_names[$o]</option>\n";}
	$o++;
	}
echo "</SELECT>\n";
echo "</TD><TD VALIGN=TOP><BR>";
echo "<BR><BR>\n";
echo "<INPUT type=submit NAME=SUBMIT VALUE=SUBMIT>\n";
echo "</TD><TD VALIGN=TOP> &nbsp; &nbsp; &nbsp; &nbsp; ";
echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";
if (strlen($group[0]) > 1)
	{
	echo " <a href=\"./admin.php?ADD=34&campaign_id=$group[0]\">MODIFY</a> | \n";
	echo " <a href=\"./admin.php?ADD=999999\">REPORTS</a> </FONT>\n";
	}
else
	{
	echo " <a href=\"./admin.php?ADD=10\">CAMPAIGNS</a> | \n";
	echo " <a href=\"./admin.php?ADD=999999\">REPORTS</a> </FONT>\n";
	}
echo "</TD></TR></TABLE>";
echo "</FORM>\n\n";

echo "<PRE><FONT SIZE=2>\n\n";


if (strlen($group[0]) < 1)
	{
	echo "\n\n";
	echo "PLEASE SELECT A CAMPAIGN AND DATE ABOVE AND CLICK SUBMIT\n";
	}

else
	{
	$OUToutput = '';
	$OUToutput .= "Lists Campaign Status Stats                             $NOW_TIME\n";

	$OUToutput .= "\n";

	##############################
	#########  LIST ID BREAKDOWN STATS

	$TOTALleads = 0;

	$OUToutput .= "\n";
	$OUToutput .= "---------- LIST ID SUMMARY\n";
	$OUToutput .= "+------------------------------------------+------------+----------+\n";
	$OUToutput .= "| LIST                                     | LEADS      | ACTIVE   |\n";
	$OUToutput .= "+------------------------------------------+------------+----------+\n";

	$stmt="select count(*),list_id from vicidial_list where list_id IN( SELECT list_id from vicidial_lists where active IN('Y','N') $group_SQLand) group by list_id;";
	$rslt=mysql_query($stmt, $link);
	if ($DB) {echo "$stmt\n";}
	$listids_to_print = mysql_num_rows($rslt);
	$i=0;
	while ($i < $listids_to_print)
		{
		$row=mysql_fetch_row($rslt);
		$LISTIDcalls[$i] =	$row[0];
		$LISTIDlists[$i] =	$row[1];
		$list_id_SQL .=		"'$row[1]',";
		$i++;
		}
	if (strlen($list_id_SQL)>2)		{$list_id_SQL = substr("$list_id_SQL", 0, -1);}
	else {$list_id_SQL="''";}

	$i=0;
	while ($i < $listids_to_print)
		{
		$stmt="select list_name,active from vicidial_lists where list_id='$LISTIDlists[$i]';";
		$rslt=mysql_query($stmt, $link);
		if ($DB) {echo "$stmt\n";}
		$list_name_to_print = mysql_num_rows($rslt);
		if ($list_name_to_print > 0)
			{
			$row=mysql_fetch_row($rslt);
			$LISTIDlist_names[$i] =	$row[0];
			if ($row[1]=='Y')
				{$LISTIDlist_active[$i] = 'ACTIVE  ';}
			else
				{$LISTIDlist_active[$i] = 'INACTIVE';}
			}

		$TOTALleads = ($TOTALleads + $LISTIDcalls[$i]);

		$LISTIDcount =	sprintf("%10s", $LISTIDcalls[$i]);while(strlen($LISTIDcount)>10) {$LISTIDcount = substr("$LISTIDcount", 0, -1);}
		$LISTIDname =	sprintf("%-40s", "$LISTIDlists[$i] - $LISTIDlist_names[$i]");while(strlen($LISTIDname)>40) {$LISTIDname = substr("$LISTIDname", 0, -1);}

		$OUToutput .= "| $LISTIDname | $LISTIDcount | $LISTIDlist_active[$i] |\n";

		$i++;
		}

	$TOTALleads =		sprintf("%10s", $TOTALleads);

	$OUToutput .= "+------------------------------------------+------------+----------+\n";
	$OUToutput .= "| TOTAL:                                   | $TOTALleads |\n";
	$OUToutput .= "+------------------------------------------+------------+\n";


	##############################
	#########  STATUS FLAGS STATS

	$HA_count=0;
	$HA_percent=0;
	$SALE_count=0;
	$SALE_percent=0;
	$DNC_count=0;
	$DNC_percent=0;
	$CC_count=0;
	$CC_percent=0;
	$NI_count=0;
	$NI_percent=0;
	$UW_count=0;
	$UW_percent=0;

	$stmt="select count(*) from vicidial_list where status IN($human_answered_statuses) and list_id IN($list_id_SQL);";
	$rslt=mysql_query($stmt, $link);
	if ($DB) {echo "$stmt\n";}
	$HA_results = mysql_num_rows($rslt);
	if ($HA_results > 0)
		{
		$row=mysql_fetch_row($rslt);
		$HA_count = $row[0];
		if ($HA_count > 0)
			{
			$HA_percent = ( ($HA_count / $TOTALleads) * 100);
			}
		}
	$stmt="select count(*) from vicidial_list where status IN($sale_statuses) and list_id IN($list_id_SQL);";
	$rslt=mysql_query($stmt, $link);
	if ($DB) {echo "$stmt\n";}
	$SALE_results = mysql_num_rows($rslt);
	if ($SALE_results > 0)
		{
		$row=mysql_fetch_row($rslt);
		$SALE_count = $row[0];
		if ($SALE_count > 0)
			{
			$SALE_percent = ( ($SALE_count / $TOTALleads) * 100);
			}
		}
	$stmt="select count(*) from vicidial_list where status IN($dnc_statuses) and list_id IN($list_id_SQL);";
	$rslt=mysql_query($stmt, $link);
	if ($DB) {echo "$stmt\n";}
	$DNC_results = mysql_num_rows($rslt);
	if ($DNC_results > 0)
		{
		$row=mysql_fetch_row($rslt);
		$DNC_count = $row[0];
		if ($DNC_count > 0)
			{
			$DNC_percent = ( ($DNC_count / $TOTALleads) * 100);
			}
		}
	$stmt="select count(*) from vicidial_list where status IN($customer_contact_statuses) and list_id IN($list_id_SQL);";
	$rslt=mysql_query($stmt, $link);
	if ($DB) {echo "$stmt\n";}
	$CC_results = mysql_num_rows($rslt);
	if ($CC_results > 0)
		{
		$row=mysql_fetch_row($rslt);
		$CC_count = $row[0];
		if ($CC_count > 0)
			{
			$CC_percent = ( ($CC_count / $TOTALleads) * 100);
			}
		}
	$stmt="select count(*) from vicidial_list where status IN($not_interested_statuses) and list_id IN($list_id_SQL);";
	$rslt=mysql_query($stmt, $link);
	if ($DB) {echo "$stmt\n";}
	$NI_results = mysql_num_rows($rslt);
	if ($NI_results > 0)
		{
		$row=mysql_fetch_row($rslt);
		$NI_count = $row[0];
		if ($NI_count > 0)
			{
			$NI_percent = ( ($NI_count / $TOTALleads) * 100);
			}
		}
	$stmt="select count(*) from vicidial_list where status IN($unworkable_statuses) and list_id IN($list_id_SQL);";
	$rslt=mysql_query($stmt, $link);
	if ($DB) {echo "$stmt\n";}
	$UW_results = mysql_num_rows($rslt);
	if ($UW_results > 0)
		{
		$row=mysql_fetch_row($rslt);
		$UW_count = $row[0];
		if ($UW_count > 0)
			{
			$UW_percent = ( ($UW_count / $TOTALleads) * 100);
			}
		}

	$HA_percent =	sprintf("%6.2f", "$HA_percent"); while(strlen($HA_percent)>6) {$HA_percent = substr("$HA_percent", 0, -1);}
	$SALE_percent =	sprintf("%6.2f", "$SALE_percent"); while(strlen($SALE_percent)>6) {$SALE_percent = substr("$SALE_percent", 0, -1);}
	$DNC_percent =	sprintf("%6.2f", "$DNC_percent"); while(strlen($DNC_percent)>6) {$DNC_percent = substr("$DNC_percent", 0, -1);}
	$CC_percent =	sprintf("%6.2f", "$CC_percent"); while(strlen($CC_percent)>6) {$CC_percent = substr("$CC_percent", 0, -1);}
	$NI_percent =	sprintf("%6.2f", "$NI_percent"); while(strlen($NI_percent)>6) {$NI_percent = substr("$NI_percent", 0, -1);}
	$UW_percent =	sprintf("%6.2f", "$UW_percent"); while(strlen($UW_percent)>6) {$UW_percent = substr("$UW_percent", 0, -1);}

	$HA_count =	sprintf("%10s", "$HA_count"); while(strlen($HA_count)>10) {$HA_count = substr("$HA_count", 0, -1);}
	$SALE_count =	sprintf("%10s", "$SALE_count"); while(strlen($SALE_count)>10) {$SALE_count = substr("$SALE_count", 0, -1);}
	$DNC_count =	sprintf("%10s", "$DNC_count"); while(strlen($DNC_count)>10) {$DNC_count = substr("$DNC_count", 0, -1);}
	$CC_count =	sprintf("%10s", "$CC_count"); while(strlen($CC_count)>10) {$CC_count = substr("$CC_count", 0, -1);}
	$NI_count =	sprintf("%10s", "$NI_count"); while(strlen($NI_count)>10) {$NI_count = substr("$NI_count", 0, -1);}
	$UW_count =	sprintf("%10s", "$UW_count"); while(strlen($UW_count)>10) {$UW_count = substr("$UW_count", 0, -1);}

	$OUToutput .= "\n";
	$OUToutput .= "\n";
	$OUToutput .= "---------- STATUS FLAGS SUMMARY:    (and % of leads in selected lists)\n";
	$OUToutput .= "+------------------+------------+----------+\n";
	$OUToutput .= "| Human Answer     | $HA_count |  $HA_percent% |\n";
	$OUToutput .= "| Sale             | $SALE_count |  $SALE_percent% |\n";
	$OUToutput .= "| DNC              | $DNC_count |  $DNC_percent% |\n";
	$OUToutput .= "| Customer Contact | $CC_count |  $CC_percent% |\n";
	$OUToutput .= "| Not Interested   | $NI_count |  $NI_percent% |\n";
	$OUToutput .= "| Unworkable       | $UW_count |  $UW_percent% |\n";
	$OUToutput .= "+------------------+------------+----------+\n";
	$OUToutput .= "\n";


	##############################
	#########  STATUS CATEGORY STATS

	$OUToutput .= "\n";
	$OUToutput .= "---------- CUSTOM STATUS CATEGORY STATS\n";
	$OUToutput .= "+----------------------+------------+--------------------------------+\n";
	$OUToutput .= "| CATEGORY             | CALLS      | DESCRIPTION                    |\n";
	$OUToutput .= "+----------------------+------------+--------------------------------+\n";


	$TOTCATcalls=0;
	$r=0;
	while ($r < $statcats_to_print)
		{
		if ($vsc_id[$r] != 'UNDEFINED')
			{
			$TOTCATcalls = ($TOTCATcalls + $vsc_count[$r]);
			$category =	sprintf("%-20s", $vsc_id[$r]); while(strlen($category)>20) {$category = substr("$category", 0, -1);}
			$CATcount =	sprintf("%10s", $vsc_count[$r]); while(strlen($CATcount)>10) {$CATcount = substr("$CATcount", 0, -1);}
			$CATname =	sprintf("%-30s", $vsc_name[$r]); while(strlen($CATname)>30) {$CATname = substr("$CATname", 0, -1);}

			$OUToutput .= "| $category | $CATcount | $CATname |\n";
			}
		$r++;
		}

	$TOTCATcalls =	sprintf("%10s", $TOTCATcalls); while(strlen($TOTCATcalls)>10) {$TOTCATcalls = substr("$TOTCATcalls", 0, -1);}

	$OUToutput .= "+----------------------+------------+--------------------------------+\n";
	$OUToutput .= "| TOTAL                | $TOTCATcalls |\n";
	$OUToutput .= "+----------------------+------------+\n";



	##############################
	#########  PER LIST DETAIL STATS


	$TOTALleads = 0;
	$OUToutput .= "\n";
	$OUToutput .= "---------- PER LIST DETAIL STATS\n";
	$OUToutput .= "\n";

	$i=0;
	while ($i < $listids_to_print)
		{
		$TOTALleads=0;
		$header_list_id = "$LISTIDlists[$i] - $LISTIDlist_names[$i]";
		$header_list_id =	sprintf("%-51s", $header_list_id); while(strlen($header_list_id)>51) {$header_list_id = substr("$header_list_id", 0, -1);}
		$header_list_count =	sprintf("%10s", $LISTIDcalls[$i]); while(strlen($header_list_count)>10) {$header_list_count = substr("$header_list_count", 0, -1);}

		$OUToutput .= "\n";
		$OUToutput .= "+--------------------------------------------------------------+\n";
		$OUToutput .= "| $header_list_id $LISTIDlist_active[$i] |\n";
		$OUToutput .= "|    TOTAL LEADS: $header_list_count                                   |\n";
		$OUToutput .= "+--------------------------------------------------------------+\n";

		$HA_count=0;
		$HA_percent=0;
		$SALE_count=0;
		$SALE_percent=0;
		$DNC_count=0;
		$DNC_percent=0;
		$CC_count=0;
		$CC_percent=0;
		$NI_count=0;
		$NI_percent=0;
		$UW_count=0;
		$UW_percent=0;

		$stmt="select count(*) from vicidial_list where list_id='$LISTIDlists[$i]' and status IN($human_answered_statuses);";
		$rslt=mysql_query($stmt, $link);
		if ($DB) {echo "$stmt\n";}
		$HA_results = mysql_num_rows($rslt);
		if ($HA_results > 0)
			{
			$row=mysql_fetch_row($rslt);
			$HA_count = $row[0];
			if ($HA_count > 0)
				{
				$HA_percent = ( ($HA_count / $LISTIDcalls[$i]) * 100);
				}
			}
		$stmt="select count(*) from vicidial_list where list_id='$LISTIDlists[$i]' and status IN($sale_statuses);";
		$rslt=mysql_query($stmt, $link);
		if ($DB) {echo "$stmt\n";}
		$SALE_results = mysql_num_rows($rslt);
		if ($SALE_results > 0)
			{
			$row=mysql_fetch_row($rslt);
			$SALE_count = $row[0];
			if ($SALE_count > 0)
				{
				$SALE_percent = ( ($SALE_count / $LISTIDcalls[$i]) * 100);
				}
			}
		$stmt="select count(*) from vicidial_list where list_id='$LISTIDlists[$i]' and status IN($dnc_statuses);";
		$rslt=mysql_query($stmt, $link);
		if ($DB) {echo "$stmt\n";}
		$DNC_results = mysql_num_rows($rslt);
		if ($DNC_results > 0)
			{
			$row=mysql_fetch_row($rslt);
			$DNC_count = $row[0];
			if ($DNC_count > 0)
				{
				$DNC_percent = ( ($DNC_count / $LISTIDcalls[$i]) * 100);
				}
			}
		$stmt="select count(*) from vicidial_list where list_id='$LISTIDlists[$i]' and status IN($customer_contact_statuses);";
		$rslt=mysql_query($stmt, $link);
		if ($DB) {echo "$stmt\n";}
		$CC_results = mysql_num_rows($rslt);
		if ($CC_results > 0)
			{
			$row=mysql_fetch_row($rslt);
			$CC_count = $row[0];
			if ($CC_count > 0)
				{
				$CC_percent = ( ($CC_count / $LISTIDcalls[$i]) * 100);
				}
			}
		$stmt="select count(*) from vicidial_list where list_id='$LISTIDlists[$i]' and status IN($not_interested_statuses);";
		$rslt=mysql_query($stmt, $link);
		if ($DB) {echo "$stmt\n";}
		$NI_results = mysql_num_rows($rslt);
		if ($NI_results > 0)
			{
			$row=mysql_fetch_row($rslt);
			$NI_count = $row[0];
			if ($NI_count > 0)
				{
				$NI_percent = ( ($NI_count / $LISTIDcalls[$i]) * 100);
				}
			}
		$stmt="select count(*) from vicidial_list where list_id='$LISTIDlists[$i]' and status IN($unworkable_statuses);";
		$rslt=mysql_query($stmt, $link);
		if ($DB) {echo "$stmt\n";}
		$UW_results = mysql_num_rows($rslt);
		if ($UW_results > 0)
			{
			$row=mysql_fetch_row($rslt);
			$UW_count = $row[0];
			if ($UW_count > 0)
				{
				$UW_percent = ( ($UW_count / $LISTIDcalls[$i]) * 100);
				}
			}

		$HA_percent =	sprintf("%6.2f", "$HA_percent"); while(strlen($HA_percent)>6) {$HA_percent = substr("$HA_percent", 0, -1);}
		$SALE_percent =	sprintf("%6.2f", "$SALE_percent"); while(strlen($SALE_percent)>6) {$SALE_percent = substr("$SALE_percent", 0, -1);}
		$DNC_percent =	sprintf("%6.2f", "$DNC_percent"); while(strlen($DNC_percent)>6) {$DNC_percent = substr("$DNC_percent", 0, -1);}
		$CC_percent =	sprintf("%6.2f", "$CC_percent"); while(strlen($CC_percent)>6) {$CC_percent = substr("$CC_percent", 0, -1);}
		$NI_percent =	sprintf("%6.2f", "$NI_percent"); while(strlen($NI_percent)>6) {$NI_percent = substr("$NI_percent", 0, -1);}
		$UW_percent =	sprintf("%6.2f", "$UW_percent"); while(strlen($UW_percent)>6) {$UW_percent = substr("$UW_percent", 0, -1);}

		$HA_count =	sprintf("%9s", "$HA_count"); while(strlen($HA_count)>9) {$HA_count = substr("$HA_count", 0, -1);}
		$SALE_count =	sprintf("%9s", "$SALE_count"); while(strlen($SALE_count)>9) {$SALE_count = substr("$SALE_count", 0, -1);}
		$DNC_count =	sprintf("%9s", "$DNC_count"); while(strlen($DNC_count)>9) {$DNC_count = substr("$DNC_count", 0, -1);}
		$CC_count =	sprintf("%9s", "$CC_count"); while(strlen($CC_count)>9) {$CC_count = substr("$CC_count", 0, -1);}
		$NI_count =	sprintf("%9s", "$NI_count"); while(strlen($NI_count)>9) {$NI_count = substr("$NI_count", 0, -1);}
		$UW_count =	sprintf("%9s", "$UW_count"); while(strlen($UW_count)>9) {$UW_count = substr("$UW_count", 0, -1);}

		$OUToutput .= "| STATUS FLAGS BREAKDOWN:  (and % of total leads in the list)  |\n";
		$OUToutput .= "|   Human Answer:       $HA_count    $HA_percent%                   |\n";
		$OUToutput .= "|   Sale:               $SALE_count    $SALE_percent%                   |\n";
		$OUToutput .= "|   DNC:                $DNC_count    $DNC_percent%                   |\n";
		$OUToutput .= "|   Customer Contact:   $CC_count    $CC_percent%                   |\n";
		$OUToutput .= "|   Not Interested:     $NI_count    $NI_percent%                   |\n";
		$OUToutput .= "|   Unworkable:         $UW_count    $UW_percent%                   |\n";
		$OUToutput .= "+----+--------------------------------------------+------------+\n";
		$OUToutput .= "     |    STATUS BREAKDOWN:                       |    COUNT   |\n";
		$OUToutput .= "     +--------+-----------------------------------+------------+\n";




		$stmt="select status,count(*) from vicidial_list where list_id='$LISTIDlists[$i]' group by status order by status;";
		$rslt=mysql_query($stmt, $link);
		if ($DB) {echo "$stmt\n";}
		$liststatussum_to_print = mysql_num_rows($rslt);
		$r=0;
		while ($r < $liststatussum_to_print)
			{
			$row=mysql_fetch_row($rslt);
			$LISTIDstatus[$r] =	$row[0];
			$LISTIDcounts[$r] =	$row[1];
				if ($DB) {echo "$r|$LISTIDstatus[$r]|$LISTIDcounts[$r]|    |$row[0]|$row[1]|<BR>\n";}
			$r++;
			}

		$r=0;
		while ($r < $liststatussum_to_print)
			{
			$LIDstatus = $LISTIDstatus[$r];
			$LIDstatus_format = sprintf("%6s", $LIDstatus);
			$TOTALleads = ($TOTALleads + $LISTIDcounts[$r]);

			$LISTID_status_count =	sprintf("%10s", $LISTIDcounts[$r]); while(strlen($LISTID_status_count)>10) {$LISTID_status_count = substr("$LISTID_status_count", 0, -1);}
			$LISTIDname =	sprintf("%-42s", "$LIDstatus_format | $statname_list[$LIDstatus]"); while(strlen($LISTIDname)>42) {$LISTIDname = substr("$LISTIDname", 0, -1);}

			$OUToutput .= "     | $LISTIDname | $LISTID_status_count |\n";

			$r++;
			}
		$TOTALleads =		sprintf("%10s", $TOTALleads);

		$OUToutput .= "     +--------+-----------------------------------+------------+\n";
		$OUToutput .= "     | TOTAL:                                     | $TOTALleads |\n";
		$OUToutput .= "     +--------------------------------------------+------------+\n";

		$i++;
		}







	echo "$OUToutput";

	$ENDtime = date("U");
	$RUNtime = ($ENDtime - $STARTtime);
	echo "\nRun Time: $RUNtime seconds|$db_source\n";
	}



?>
</PRE>
</TD></TR></TABLE>

</BODY></HTML>
