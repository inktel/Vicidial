<?php 
# realtime_report.php
# 
# Copyright (C) 2011  Matt Florell <vicidial@gmail.com>    LICENSE: AGPLv2
#
# live real-time stats for the VICIDIAL Auto-Dialer all servers
#
# Rewritten from AST_timeonVDADall.php report to be AJAX and javascript instead 
# of link-driven
#
# * Requires AST_timeonVDADall.php for AJAX-derived stats information
# 
# CHANGELOG:
# 101216-1355 - First Build
# 101218-1520 - Small time reload bug fix and formatting fixes
# 110111-1557 - Added options.php options, minor bug fixes
# 110113-1736 - Small fix
# 110303-2124 - Added agent on-hook phone indication and RING status and color
# 110316-2216 - Added Agent, Carrier and Preset options.php settings
# 110516-2128 - IE fix
# 110526-1807 - Added webphone_auto_answer option
#

$version = '2.4-8';
$build = '110526-1807';

header ("Content-type: text/html; charset=utf-8");

require("dbconnect.php");
require("functions.php");

$PHP_AUTH_USER=$_SERVER['PHP_AUTH_USER'];
$PHP_AUTH_PW=$_SERVER['PHP_AUTH_PW'];
$PHP_SELF=$_SERVER['PHP_SELF'];
if (isset($_GET["server_ip"]))			{$server_ip=$_GET["server_ip"];}
	elseif (isset($_POST["server_ip"]))	{$server_ip=$_POST["server_ip"];}
if (isset($_GET["RR"]))					{$RR=$_GET["RR"];}
	elseif (isset($_POST["RR"]))		{$RR=$_POST["RR"];}
if (isset($_GET["inbound"]))			{$inbound=$_GET["inbound"];}
	elseif (isset($_POST["inbound"]))	{$inbound=$_POST["inbound"];}
if (isset($_GET["group"]))				{$group=$_GET["group"];}
	elseif (isset($_POST["group"]))		{$group=$_POST["group"];}
if (isset($_GET["groups"]))				{$groups=$_GET["groups"];}
	elseif (isset($_POST["groups"]))	{$groups=$_POST["groups"];}
if (isset($_GET["usergroup"]))			{$usergroup=$_GET["usergroup"];}
	elseif (isset($_POST["usergroup"]))	{$usergroup=$_POST["usergroup"];}
if (isset($_GET["DB"]))					{$DB=$_GET["DB"];}
	elseif (isset($_POST["DB"]))		{$DB=$_POST["DB"];}
if (isset($_GET["adastats"]))			{$adastats=$_GET["adastats"];}
	elseif (isset($_POST["adastats"]))	{$adastats=$_POST["adastats"];}
if (isset($_GET["submit"]))				{$submit=$_GET["submit"];}
	elseif (isset($_POST["submit"]))	{$submit=$_POST["submit"];}
if (isset($_GET["SUBMIT"]))				{$SUBMIT=$_GET["SUBMIT"];}
	elseif (isset($_POST["SUBMIT"]))	{$SUBMIT=$_POST["SUBMIT"];}
if (isset($_GET["SIPmonitorLINK"]))				{$SIPmonitorLINK=$_GET["SIPmonitorLINK"];}
	elseif (isset($_POST["SIPmonitorLINK"]))	{$SIPmonitorLINK=$_POST["SIPmonitorLINK"];}
if (isset($_GET["IAXmonitorLINK"]))				{$IAXmonitorLINK=$_GET["IAXmonitorLINK"];}
	elseif (isset($_POST["IAXmonitorLINK"]))	{$IAXmonitorLINK=$_POST["IAXmonitorLINK"];}
if (isset($_GET["UGdisplay"]))			{$UGdisplay=$_GET["UGdisplay"];}
	elseif (isset($_POST["UGdisplay"]))	{$UGdisplay=$_POST["UGdisplay"];}
if (isset($_GET["UidORname"]))			{$UidORname=$_GET["UidORname"];}
	elseif (isset($_POST["UidORname"]))	{$UidORname=$_POST["UidORname"];}
if (isset($_GET["orderby"]))			{$orderby=$_GET["orderby"];}
	elseif (isset($_POST["orderby"]))	{$orderby=$_POST["orderby"];}
if (isset($_GET["SERVdisplay"]))			{$SERVdisplay=$_GET["SERVdisplay"];}
	elseif (isset($_POST["SERVdisplay"]))	{$SERVdisplay=$_POST["SERVdisplay"];}
if (isset($_GET["CALLSdisplay"]))			{$CALLSdisplay=$_GET["CALLSdisplay"];}
	elseif (isset($_POST["CALLSdisplay"]))	{$CALLSdisplay=$_POST["CALLSdisplay"];}
if (isset($_GET["PHONEdisplay"]))			{$PHONEdisplay=$_GET["PHONEdisplay"];}
	elseif (isset($_POST["PHONEdisplay"]))	{$PHONEdisplay=$_POST["PHONEdisplay"];}
if (isset($_GET["CUSTPHONEdisplay"]))			{$CUSTPHONEdisplay=$_GET["CUSTPHONEdisplay"];}
	elseif (isset($_POST["CUSTPHONEdisplay"]))	{$CUSTPHONEdisplay=$_POST["CUSTPHONEdisplay"];}
if (isset($_GET["NOLEADSalert"]))			{$NOLEADSalert=$_GET["NOLEADSalert"];}
	elseif (isset($_POST["NOLEADSalert"]))	{$NOLEADSalert=$_POST["NOLEADSalert"];}
if (isset($_GET["DROPINGROUPstats"]))			{$DROPINGROUPstats=$_GET["DROPINGROUPstats"];}
	elseif (isset($_POST["DROPINGROUPstats"]))	{$DROPINGROUPstats=$_POST["DROPINGROUPstats"];}
if (isset($_GET["ALLINGROUPstats"]))			{$ALLINGROUPstats=$_GET["ALLINGROUPstats"];}
	elseif (isset($_POST["ALLINGROUPstats"]))	{$ALLINGROUPstats=$_POST["ALLINGROUPstats"];}
if (isset($_GET["with_inbound"]))			{$with_inbound=$_GET["with_inbound"];}
	elseif (isset($_POST["with_inbound"]))	{$with_inbound=$_POST["with_inbound"];}
if (isset($_GET["monitor_active"]))				{$monitor_active=$_GET["monitor_active"];}
	elseif (isset($_POST["monitor_active"]))	{$monitor_active=$_POST["monitor_active"];}
if (isset($_GET["monitor_phone"]))				{$monitor_phone=$_GET["monitor_phone"];}
	elseif (isset($_POST["monitor_phone"]))		{$monitor_phone=$_POST["monitor_phone"];}
if (isset($_GET["CARRIERstats"]))			{$CARRIERstats=$_GET["CARRIERstats"];}
	elseif (isset($_POST["CARRIERstats"]))	{$CARRIERstats=$_POST["CARRIERstats"];}
if (isset($_GET["PRESETstats"]))			{$PRESETstats=$_GET["PRESETstats"];}
	elseif (isset($_POST["PRESETstats"]))	{$PRESETstats=$_POST["PRESETstats"];}
if (isset($_GET["AGENTtimeSTATS"]))				{$AGENTtimeSTATS=$_GET["AGENTtimeSTATS"];}
	elseif (isset($_POST["AGENTtimeSTATS"]))	{$AGENTtimeSTATS=$_POST["AGENTtimeSTATS"];}

$report_name = 'Real-Time Main Report';
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

$webphone_width =	'460';
$webphone_height =	'500';
$webphone_left =	'600';
$webphone_top =		'27';
$webphone_bufw =	'250';
$webphone_bufh =	'1';
$webphone_pad =		'10';
$webphone_clpos =	"<BR>  &nbsp; <a href=\"#\" onclick=\"hideDiv('webphone_content');\">webphone -</a>";

if (file_exists('options.php'))
	{
	require('options.php');
	}

if (!isset($DB)) 
	{
	if (!isset($RS_DB)) {$DB=0;}
	else {$DB = $RS_DB;}
	}
if (!isset($RR)) 
	{
	if (!isset($RS_RR)) {$RR=40;}
	else {$RR = $RS_RR;}
	}
if (!isset($group)) 
	{
	if (!isset($RS_group)) {$group='ALL-ACTIVE';}
	else {$group = $RS_group;}
	}
if (!isset($usergroup)) 
	{
	if (!isset($RS_usergroup)) {$usergroup='';}
	else {$usergroup = $RS_usergroup;}
	}
if (!isset($UGdisplay)) 
	{
	if (!isset($RS_UGdisplay)) {$UGdisplay=0;}
	else {$UGdisplay = $RS_UGdisplay;}
	}
if (!isset($UidORname)) 
	{
	if (!isset($RS_UidORname)) {$UidORname=1;}
	else {$UidORname = $RS_UidORname;}
	}
if (!isset($orderby)) 
	{
	if (!isset($RS_orderby)) {$orderby='statusdown';}
	else {$orderby = $RS_orderby;}
	}
if (!isset($SERVdisplay)) 
	{
	if (!isset($RS_SERVdisplay)) {$SERVdisplay=0;}
	else {$SERVdisplay = $RS_SERVdisplay;}
	}
if (!isset($CALLSdisplay)) 
	{
	if (!isset($RS_CALLSdisplay)) {$CALLSdisplay=1;}
	else {$CALLSdisplay = $RS_CALLSdisplay;}
	}
if (!isset($PHONEdisplay)) 
	{
	if (!isset($RS_PHONEdisplay)) {$PHONEdisplay=0;}
	else {$PHONEdisplay = $RS_PHONEdisplay;}
	}
if (!isset($CUSTPHONEdisplay)) 
	{
	if (!isset($RS_CUSTPHONEdisplay)) {$CUSTPHONEdisplay=0;}
	else {$CUSTPHONEdisplay = $RS_CUSTPHONEdisplay;}
	}
if (!isset($PAUSEcodes)) 
	{
	if (!isset($RS_PAUSEcodes)) {$PAUSEcodes='N';}
	else {$PAUSEcodes = $RS_PAUSEcodes;}
	}
if (!isset($with_inbound)) 
	{
	if (!isset($RS_with_inbound))	
		{
		if ($outbound_autodial_active > 0)
			{$with_inbound='Y';}  # N=no, Y=yes, O=only
		else
			{$with_inbound='O';}  # N=no, Y=yes, O=only
		}
	else {$with_inbound = $RS_with_inbound;}
	}
if (!isset($CARRIERstats)) 
	{
	if (!isset($RS_CARRIERstats)) {$CARRIERstats='0';}
	else {$CARRIERstats = $RS_CARRIERstats;}
	}
if (!isset($PRESETstats)) 
	{
	if (!isset($RS_PRESETstats)) {$PRESETstats='0';}
	else {$PRESETstats = $RS_PRESETstats;}
	}
if (!isset($AGENTtimeSTATS)) 
	{
	if (!isset($RS_AGENTtimeSTATS)) {$AGENTtimeSTATS='0';}
	else {$AGENTtimeSTATS = $RS_AGENTtimeSTATS;}
	}

$ingroup_detail='';

if ( (strlen($group)>1) and (strlen($groups[0])<1) ) {$groups[0] = $group;}
else {$group = $groups[0];}

$NOW_TIME = date("Y-m-d H:i:s");
$NOW_DAY = date("Y-m-d");
$NOW_HOUR = date("H:i:s");
$STARTtime = date("U");
$epochONEminuteAGO = ($STARTtime - 60);
$timeONEminuteAGO = date("Y-m-d H:i:s",$epochONEminuteAGO);
$epochFIVEminutesAGO = ($STARTtime - 300);
$timeFIVEminutesAGO = date("Y-m-d H:i:s",$epochFIVEminutesAGO);
$epochFIFTEENminutesAGO = ($STARTtime - 900);
$timeFIFTEENminutesAGO = date("Y-m-d H:i:s",$epochFIFTEENminutesAGO);
$epochONEhourAGO = ($STARTtime - 3600);
$timeONEhourAGO = date("Y-m-d H:i:s",$epochONEhourAGO);
$epochSIXhoursAGO = ($STARTtime - 21600);
$timeSIXhoursAGO = date("Y-m-d H:i:s",$epochSIXhoursAGO);
$epochTWENTYFOURhoursAGO = ($STARTtime - 86400);
$timeTWENTYFOURhoursAGO = date("Y-m-d H:i:s",$epochTWENTYFOURhoursAGO);
$webphone_content='';

$PHP_AUTH_USER = ereg_replace("[^0-9a-zA-Z]","",$PHP_AUTH_USER);
$PHP_AUTH_PW = ereg_replace("[^0-9a-zA-Z]","",$PHP_AUTH_PW);

$stmt="SELECT count(*) from vicidial_users where user='$PHP_AUTH_USER' and pass='$PHP_AUTH_PW' and user_level > 6 and view_reports='1' and active='Y';";
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
#  and (preg_match("/MONITOR|BARGE|HIJACK/",$monitor_active) ) )
if ( (!isset($monitor_phone)) or (strlen($monitor_phone)<1) )
	{
	$stmt="select phone_login from vicidial_users where user='$PHP_AUTH_USER' and pass='$PHP_AUTH_PW' and active='Y';";
	$rslt=mysql_query($stmt, $link);
	if ($DB) {echo "$stmt\n";}
	$row=mysql_fetch_row($rslt);
	$monitor_phone = $row[0];
	}

$stmt="SELECT realtime_block_user_info,user_group from vicidial_users where user='$PHP_AUTH_USER' and pass='$PHP_AUTH_PW' and user_level > 6 and view_reports='1' and active='Y';";
if ($DB) {echo "|$stmt|\n";}
$rslt=mysql_query($stmt, $link);
$row=mysql_fetch_row($rslt);
$realtime_block_user_info = $row[0];
$LOGuser_group =			$row[1];

$stmt="SELECT allowed_campaigns,allowed_reports,webphone_url_override,webphone_dialpad_override,webphone_systemkey_override from vicidial_user_groups where user_group='$LOGuser_group';";
if ($DB) {echo "|$stmt|\n";}
$rslt=mysql_query($stmt, $link);
$row=mysql_fetch_row($rslt);
$LOGallowed_campaigns =			$row[0];
$LOGallowed_reports =			$row[1];
$webphone_url =					$row[2];
$webphone_dialpad_override =	$row[3];
$system_key =					$row[4];

if ( (!preg_match("/$report_name/",$LOGallowed_reports)) and (!preg_match("/ALL REPORTS/",$LOGallowed_reports)) )
	{
    Header("WWW-Authenticate: Basic realm=\"VICI-PROJECTS\"");
    Header("HTTP/1.0 401 Unauthorized");
    echo "You are not allowed to view this report: |$PHP_AUTH_USER|$report_name|\n";
    exit;
	}

$LOGallowed_campaignsSQL='';
$whereLOGallowed_campaignsSQL='';
if ( (!preg_match("/ALL-/",$LOGallowed_campaigns)) )
	{
	$rawLOGallowed_campaignsSQL = preg_replace("/ -/",'',$LOGallowed_campaigns);
	$rawLOGallowed_campaignsSQL = preg_replace("/ /","','",$rawLOGallowed_campaignsSQL);
	$LOGallowed_campaignsSQL = "and campaign_id IN('$rawLOGallowed_campaignsSQL')";
	$whereLOGallowed_campaignsSQL = "where campaign_id IN('$rawLOGallowed_campaignsSQL')";
	}
$regexLOGallowed_campaigns = " $LOGallowed_campaigns ";

$allactivecampaigns='';
$stmt="select campaign_id,campaign_name from vicidial_campaigns where active='Y' $LOGallowed_campaignsSQL order by campaign_id;";
$rslt=mysql_query($stmt, $link);
if ($DB) {echo "$stmt\n";}
$groups_to_print = mysql_num_rows($rslt);
$i=0;
$LISTgroups[$i]='ALL-ACTIVE';
$i++;
$groups_to_print++;
while ($i < $groups_to_print)
	{
	$row=mysql_fetch_row($rslt);
	$LISTgroups[$i] =$row[0];
	$LISTnames[$i] =$row[1];
	$allactivecampaigns .= "'$LISTgroups[$i]',";
	$i++;
	}
$allactivecampaigns .= "''";

$i=0;
$group_string='|';
$group_ct = count($groups);
while($i < $group_ct)
	{
	if ( (preg_match("/ $groups[$i] /",$regexLOGallowed_campaigns)) or (preg_match("/ALL-/",$LOGallowed_campaigns)) )
		{
		$group_string .= "$groups[$i]|";
		$group_SQL .= "'$groups[$i]',";
		$groupQS .= "&groups[]=$groups[$i]";
		}

	$i++;
	}
$group_SQL = eregi_replace(",$",'',$group_SQL);

### if no campaigns selected, display all
if ( ($group_ct < 1) or (strlen($group_string) < 2) )
	{
	$groups[0] = 'ALL-ACTIVE';
	$group_string = '|ALL-ACTIVE|';
	$group = 'ALL-ACTIVE';
	$groupQS .= "&groups[]=ALL-ACTIVE";
	}

if ( (ereg("--NONE--",$group_string) ) or ($group_ct < 1) )
	{
	$all_active = 0;
	$group_SQL = "''";
	$group_SQLand = "and FALSE";
	$group_SQLwhere = "where FALSE";
	}
elseif ( eregi('ALL-ACTIVE',$group_string) )
	{
	$all_active = 1;
	$group_SQL = $allactivecampaigns;
	$group_SQLand = "and campaign_id IN($allactivecampaigns)";
	$group_SQLwhere = "where campaign_id IN($allactivecampaigns)";
	}
else
	{
	$all_active = 0;
	$group_SQLand = "and campaign_id IN($group_SQL)";
	$group_SQLwhere = "where campaign_id IN($group_SQL)";
	}


$stmt="select user_group from vicidial_user_groups;";
$rslt=mysql_query($stmt, $link);
if (!isset($DB))   {$DB=0;}
if ($DB) {echo "$stmt\n";}
$usergroups_to_print = mysql_num_rows($rslt);
$i=0;
while ($i < $usergroups_to_print)
	{
	$row=mysql_fetch_row($rslt);
	$usergroups[$i] =$row[0];
	$i++;
	}

if (!isset($RR))   {$RR=4;}

$NFB = '<b><font size=6 face="courier">';
$NFE = '</font></b>';
$F=''; $FG=''; $B=''; $BG='';

$select_list = "<TABLE WIDTH=700 CELLPADDING=5 BGCOLOR=\"#D9E6FE\"><TR><TD VALIGN=TOP>Select Campaigns: <BR>";
$select_list .= "<SELECT SIZE=15 NAME=groups[] ID=groups[] multiple>";
$o=0;
while ($groups_to_print > $o)
	{
	if (ereg("\|$LISTgroups[$o]\|",$group_string)) 
		{$select_list .= "<option selected value=\"$LISTgroups[$o]\">$LISTgroups[$o] - $LISTnames[$o]</option>";}
	else
		{$select_list .= "<option value=\"$LISTgroups[$o]\">$LISTgroups[$o] - $LISTnames[$o]</option>";}
	$o++;
	}
$select_list .= "</SELECT>";
$select_list .= "<BR><font size=1>(To select more than 1 campaign, hold down the Ctrl key and click)<font>";
$select_list .= "</TD><TD VALIGN=TOP ALIGN=CENTER>";
$select_list .= "<a href=\"#\" onclick=\"hideDiv(\'campaign_select_list\');\">Close Panel</a><BR><BR>";
$select_list .= "<TABLE CELLPADDING=2 CELLSPACING=2 BORDER=0>";

$select_list .= "<TR><TD align=right>";
$select_list .= "Screen Refresh Rate:  </TD><TD align=left><SELECT SIZE=1 NAME=RR ID=RR>";
$select_list .= "<option value=\"4\"";   if ($RR < 5) {$select_list .= " selected";}    $select_list .= ">4 seconds</option>";
$select_list .= "<option value=\"10\"";   if ( ($RR >= 5) and ($RR <=10) ) {$select_list .= " selected";}    $select_list .= ">10 seconds</option>";
$select_list .= "<option value=\"20\"";   if ( ($RR >= 11) and ($RR <=20) ) {$select_list .= " selected";}    $select_list .= ">20 seconds</option>";
$select_list .= "<option value=\"30\"";   if ( ($RR >= 21) and ($RR <=30) ) {$select_list .= " selected";}    $select_list .= ">30 seconds</option>";
$select_list .= "<option value=\"40\"";   if ( ($RR >= 31) and ($RR <=40) ) {$select_list .= " selected";}    $select_list .= ">40 seconds</option>";
$select_list .= "<option value=\"60\"";   if ( ($RR >= 41) and ($RR <=60) ) {$select_list .= " selected";}    $select_list .= ">60 seconds</option>";
$select_list .= "<option value=\"120\"";   if ( ($RR >= 61) and ($RR <=120) ) {$select_list .= " selected";}    $select_list .= ">2 minutes</option>";
$select_list .= "<option value=\"300\"";   if ( ($RR >= 121) and ($RR <=300) ) {$select_list .= " selected";}    $select_list .= ">5 minutes</option>";
$select_list .= "<option value=\"600\"";   if ( ($RR >= 301) and ($RR <=600) ) {$select_list .= " selected";}    $select_list .= ">10 minutes</option>";
$select_list .= "<option value=\"1200\"";   if ( ($RR >= 601) and ($RR <=1200) ) {$select_list .= " selected";}    $select_list .= ">20 minutes</option>";
$select_list .= "<option value=\"1800\"";   if ( ($RR >= 1201) and ($RR <=1800) ) {$select_list .= " selected";}    $select_list .= ">30 minutes</option>";
$select_list .= "<option value=\"2400\"";   if ( ($RR >= 1801) and ($RR <=2400) ) {$select_list .= " selected";}    $select_list .= ">40 minutes</option>";
$select_list .= "<option value=\"3600\"";   if ( ($RR >= 2401) and ($RR <=3600) ) {$select_list .= " selected";}    $select_list .= ">60 minutes</option>";
$select_list .= "<option value=\"7200\"";   if ( ($RR >= 3601) and ($RR <=7200) ) {$select_list .= " selected";}    $select_list .= ">2 hours</option>";
$select_list .= "<option value=\"63072000\"";   if ($RR >= 7201) {$select_list .= " selected";}    $select_list .= ">2 years</option>";
$select_list .= "</SELECT></TD></TR>";

$select_list .= "<TR><TD align=right>";
$select_list .= "Inbound:  </TD><TD align=left><SELECT SIZE=1 NAME=with_inbound ID=with_inbound>";
$select_list .= "<option value=\"N\"";
	if ($with_inbound=='N') {$select_list .= " selected";} 
$select_list .= ">No</option>";
$select_list .= "<option value=\"Y\"";
	if ($with_inbound=='Y') {$select_list .= " selected";} 
$select_list .= ">Yes</option>";
$select_list .= "<option value=\"O\"";
	if ($with_inbound=='O') {$select_list .= " selected";} 
$select_list .= ">Only</option>";
$select_list .= "</SELECT></TD></TR>";

$select_list .= "<TR><TD align=right>";
$select_list .= "Monitor:  </TD><TD align=left><SELECT SIZE=1 NAME=monitor_active ID=monitor_active>";
$select_list .= "<option value=\"\"";
	if (strlen($monitor_active) < 2) {$select_list .= " selected";} 
$select_list .= ">NONE</option>";
$select_list .= "<option value=\"MONITOR\"";
	if ($monitor_active=='MONITOR') {$select_list .= " selected";} 
$select_list .= ">MONITOR</option>";
$select_list .= "<option value=\"BARGE\"";
	if ($monitor_active=='BARGE') {$select_list .= " selected";} 
$select_list .= ">BARGE</option>";
#$select_list .= "<option value=\"HIJACK\"";
#	if ($monitor_active=='HIJACK') {$select_list .= " selected";} 
#$select_list .= ">HIJACK</option>";
$select_list .= "</SELECT></TD></TR>";

$select_list .= "<TR><TD align=right>";
$select_list .= "Phone:  </TD><TD align=left>";
$select_list .= "<INPUT type=text size=10 maxlength=20 NAME=monitor_phone ID=monitor_phone VALUE=\"$monitor_phone\">";
$select_list .= "</TD></TR>";
$select_list .= "<TR><TD align=center COLSPAN=2> &nbsp; </TD></TR>";

if ($UGdisplay > 0)
	{
	$select_list .= "<TR><TD align=right>";
	$select_list .= "Select User Group:  </TD><TD align=left>";
	$select_list .= "<SELECT SIZE=1 NAME=usergroup ID=usergroup>";
	$select_list .= "<option value=\"\">ALL USER GROUPS</option>";
	$o=0;
	while ($usergroups_to_print > $o)
		{
		if ($usergroups[$o] == $usergroup) {$select_list .= "<option selected value=\"$usergroups[$o]\">$usergroups[$o]</option>";}
		else {$select_list .= "<option value=\"$usergroups[$o]\">$usergroups[$o]</option>";}
		$o++;
		}
	$select_list .= "</SELECT></TD></TR>";
	}

$select_list .= "<TR><TD align=right>";
$select_list .= "Dialable Leads Alert:  </TD><TD align=left><SELECT SIZE=1 NAME=NOLEADSalert ID=NOLEADSalert>";
$select_list .= "<option value=\"\"";
	if (strlen($NOLEADSalert) < 2) {$select_list .= " selected";} 
$select_list .= ">NO</option>";
$select_list .= "<option value=\"YES\"";
	if ($NOLEADSalert=='YES') {$select_list .= " selected";} 
$select_list .= ">YES</option>";
$select_list .= "</SELECT></TD></TR>";

$select_list .= "<TR><TD align=right>";
$select_list .= "Show Drop In-Group Row:  </TD><TD align=left><SELECT SIZE=1 NAME=DROPINGROUPstats ID=DROPINGROUPstats>";
$select_list .= "<option value=\"0\"";
	if ($DROPINGROUPstats < 1) {$select_list .= " selected";} 
$select_list .= ">NO</option>";
$select_list .= "<option value=\"1\"";
	if ($DROPINGROUPstats=='1') {$select_list .= " selected";} 
$select_list .= ">YES</option>";
$select_list .= "</SELECT></TD></TR>";

$select_list .= "<TR><TD align=right>";
$select_list .= "Show Carrier Stats:  </TD><TD align=left><SELECT SIZE=1 NAME=CARRIERstats ID=CARRIERstats>";
$select_list .= "<option value=\"0\"";
	if ($CARRIERstats < 1) {$select_list .= " selected";} 
$select_list .= ">NO</option>";
$select_list .= "<option value=\"1\"";
	if ($CARRIERstats=='1') {$select_list .= " selected";} 
$select_list .= ">YES</option>";
$select_list .= "</SELECT></TD></TR>";

## find if any selected campaigns have presets enabled
$presets_enabled=0;
$stmt="select count(*) from vicidial_campaigns where enable_xfer_presets='ENABLED' $group_SQLand;";
$rslt=mysql_query($stmt, $link);
if ($DB) {$OUToutput .= "$stmt\n";}
$presets_enabled_count = mysql_num_rows($rslt);
if ($presets_enabled_count > 0)
	{
	$row=mysql_fetch_row($rslt);
	$presets_enabled = $row[0];
	}
if ($presets_enabled > 0)
	{
	$select_list .= "<TR><TD align=right>";
	$select_list .= "Show Presets Stats:  </TD><TD align=left><SELECT SIZE=1 NAME=PRESETstats ID=PRESETstats>";
	$select_list .= "<option value=\"0\"";
		if ($PRESETstats < 1) {$select_list .= " selected";} 
	$select_list .= ">NO</option>";
	$select_list .= "<option value=\"1\"";
		if ($PRESETstats=='1') {$select_list .= " selected";} 
	$select_list .= ">YES</option>";
	$select_list .= "</SELECT></TD></TR>";
	}
else
	{
	$select_list .= "<INPUT TYPE=HIDDEN NAME=PRESETstats ID=PRESETstats value=0>";
	}

$select_list .= "<TR><TD align=right>";
$select_list .= "Agent Time Stats:  </TD><TD align=left><SELECT SIZE=1 NAME=AGENTtimeSTATS ID=AGENTtimeSTATS>";
$select_list .= "<option value=\"0\"";
	if ($AGENTtimeSTATS < 1) {$select_list .= " selected";} 
$select_list .= ">NO</option>";
$select_list .= "<option value=\"1\"";
	if ($AGENTtimeSTATS=='1') {$select_list .= " selected";} 
$select_list .= ">YES</option>";
$select_list .= "</SELECT></TD></TR>";

$select_list .= "</TABLE><BR>";
$select_list .= "<INPUT type=button VALUE=SUBMIT onclick=\"update_variables(\'form_submit\',\'\');\"><FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2> &nbsp; &nbsp; &nbsp; &nbsp; ";
$select_list .= "</TD></TR>";
$select_list .= "<TR><TD ALIGN=CENTER>";
$select_list .= "<font size=1> &nbsp; </font>";
$select_list .= "</TD>";
$select_list .= "<TD NOWRAP align=right>";
$select_list .= "<font size=1>VERSION: $version &nbsp; BUILD: $build</font>";
$select_list .= "</TD></TR></TABLE>";

$open_list = "<TABLE WIDTH=250 CELLPADDING=0 CELLSPACING=0 BGCOLOR=\"#D9E6FE\"><TR><TD ALIGN=CENTER><a href=\"#\" onclick=\"showDiv(\'campaign_select_list\');\"><font size=2>Choose Report Display Options</a></TD></TR></TABLE>";





if (strlen($monitor_phone)>1)
	{
	$stmt="SELECT extension,dialplan_number,server_ip,login,pass,protocol,conf_secret,is_webphone,use_external_server_ip,codecs_list,webphone_dialpad,outbound_cid,webphone_auto_answer from phones where login='$monitor_phone' and active = 'Y';";
	if ($DB) {echo "|$stmt|\n";}
	$rslt=mysql_query($stmt, $link);
	$Mph_ct = mysql_num_rows($rslt);
	if ($Mph_ct > 0)
		{
		$row=mysql_fetch_row($rslt);
		$extension =				$row[0];
		$dialplan_number =			$row[1];
		$webphone_server_ip =		$row[2];
		$login =					$row[3];
		$pass =						$row[4];
		$protocol =					$row[5];
		$conf_secret =				$row[6];
		$is_webphone =				$row[7];
		$use_external_server_ip =	$row[8];
		$codecs_list =				$row[9];
		$webphone_dialpad =			$row[10];
		$outbound_cid =				$row[11];
		$webphone_auto_answer =		$row[12];

		if ($is_webphone == 'Y')
			{
			### build Iframe variable content for webphone here
			$codecs_list = preg_replace("/ /",'',$codecs_list);
			$codecs_list = preg_replace("/-/",'',$codecs_list);
			$codecs_list = preg_replace("/&/",'',$codecs_list);

			if ($use_external_server_ip=='Y')
				{
				##### find external_server_ip if enabled for this phone account
				$stmt="SELECT external_server_ip FROM servers where server_ip='$webphone_server_ip' LIMIT 1;";
				$rslt=mysql_query($stmt, $link);
					if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'01065',$VD_login,$server_ip,$session_name,$one_mysql_log);}
				if ($DB) {echo "$stmt\n";}
				$exip_ct = mysql_num_rows($rslt);
				if ($exip_ct > 0)
					{
					$row=mysql_fetch_row($rslt);
					$webphone_server_ip =$row[0];
					}
				}
			if (strlen($webphone_url) < 6)
				{
				##### find webphone_url in system_settings and generate IFRAME code for it #####
				$stmt="SELECT webphone_url FROM system_settings LIMIT 1;";
				$rslt=mysql_query($stmt, $link);
					if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'01066',$VD_login,$server_ip,$session_name,$one_mysql_log);}
				if ($DB) {echo "$stmt\n";}
				$wu_ct = mysql_num_rows($rslt);
				if ($wu_ct > 0)
					{
					$row=mysql_fetch_row($rslt);
					$webphone_url =$row[0];
					}
				}
			if (strlen($system_key) < 1)
				{
				##### find system_key in system_settings if populated #####
				$stmt="SELECT webphone_systemkey FROM system_settings LIMIT 1;";
				$rslt=mysql_query($stmt, $link);
					if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'01068',$VD_login,$server_ip,$session_name,$one_mysql_log);}
				if ($DB) {echo "$stmt\n";}
				$wsk_ct = mysql_num_rows($rslt);
				if ($wsk_ct > 0)
					{
					$row=mysql_fetch_row($rslt);
					$system_key =$row[0];
					}
				}
		#	echo "<!-- debug: $webphone_dialpad|$webphone_dialpad_override|$monitor_phone|$extension -->";
			if ( ($webphone_dialpad_override != 'DISABLED') and (strlen($webphone_dialpad_override) > 0) )
				{$webphone_dialpad = $webphone_dialpad_override;}
			$webphone_options='INITIAL_LOAD';
			if ($webphone_dialpad == 'Y') {$webphone_options .= "--DIALPAD_Y";}
			if ($webphone_dialpad == 'N') {$webphone_options .= "--DIALPAD_N";}
			if ($webphone_dialpad == 'TOGGLE') {$webphone_options .= "--DIALPAD_TOGGLE";}
			if ($webphone_dialpad == 'TOGGLE_OFF') {$webphone_options .= "--DIALPAD_OFF_TOGGLE";}
			if ($webphone_auto_answer == 'Y') {$webphone_options .= "--AUTOANSWER_Y";}
			if ($webphone_auto_answer == 'N') {$webphone_options .= "--AUTOANSWER_N";}

			$session_name='RTS01234561234567890';

			### base64 encode variables
			$b64_phone_login =		base64_encode($extension);
			$b64_phone_pass =		base64_encode($conf_secret);
			$b64_session_name =		base64_encode($session_name);
			$b64_server_ip =		base64_encode($webphone_server_ip);
			$b64_callerid =			base64_encode($outbound_cid);
			$b64_protocol =			base64_encode($protocol);
			$b64_codecs =			base64_encode($codecs_list);
			$b64_options =			base64_encode($webphone_options);
			$b64_system_key =		base64_encode($system_key);

			$WebPhonEurl = "$webphone_url?phone_login=$b64_phone_login&phone_login=$b64_phone_login&phone_pass=$b64_phone_pass&server_ip=$b64_server_ip&callerid=$b64_callerid&protocol=$b64_protocol&codecs=$b64_codecs&options=$b64_options&system_key=$b64_system_key";
			$webphone_content = "<iframe src=\"$WebPhonEurl\" style=\"width:" . $webphone_width . ";height:" . $webphone_height . ";background-color:transparent;z-index:17;\" scrolling=\"auto\" frameborder=\"0\" allowtransparency=\"true\" id=\"webphone\" name=\"webphone\" width=\"" . $webphone_width . "\" height=\"" . $webphone_height . "\"> </iframe>";
			}
		}
	}



?>

<HTML>
<HEAD>

<script language="Javascript">

window.onload = startup;

// functions to detect the XY position on the page of the mouse
function startup() 
	{
	hideDiv('webphone_content');
	document.getElementById('campaign_select_list').innerHTML = select_list;
	hideDiv('campaign_select_list');

	hide_ingroup_info();
	if (window.Event) 
		{
		document.captureEvents(Event.MOUSEMOVE);
		}
	document.onmousemove = getCursorXY;
	realtime_refresh_display();
	}

function getCursorXY(e) 
	{
	document.getElementById('cursorX').value = (window.Event) ? e.pageX : event.clientX + (document.documentElement.scrollLeft ? document.documentElement.scrollLeft : document.body.scrollLeft);
	document.getElementById('cursorY').value = (window.Event) ? e.pageY : event.clientY + (document.documentElement.scrollTop ? document.documentElement.scrollTop : document.body.scrollTop);
	}

var PHP_SELF = '<?php echo $PHP_SELF ?>';
var select_list = '<?php echo $select_list ?>';
var open_list = '<?php echo $open_list ?>';
var monitor_phone = '<?php echo $monitor_phone ?>';
var user = '<?php echo $PHP_AUTH_USER ?>';
var pass = '<?php echo $PHP_AUTH_PW ?>';
var RR = '<?php echo $RR ?>';
var groupQS = '<?php echo $groupQS ?>';
var DB = '<?php echo $DB ?>';
var adastats = '<?php echo $adastats ?>';
var SIPmonitorLINK = '<?php echo $SIPmonitorLINK ?>';
var IAXmonitorLINK = '<?php echo $IAXmonitorLINK ?>';
var usergroup = '<?php echo $usergroup ?>';
var UGdisplay = '<?php echo $UGdisplay ?>';
var UidORname = '<?php echo $UidORname ?>';
var orderby = '<?php echo $orderby ?>';
var SERVdisplay = '<?php echo $SERVdisplay ?>';
var CALLSdisplay = '<?php echo $CALLSdisplay ?>';
var PHONEdisplay = '<?php echo $PHONEdisplay ?>';
var CUSTPHONEdisplay = '<?php echo $CUSTPHONEdisplay ?>';
var with_inbound = '<?php echo $with_inbound ?>';
var monitor_active = '<?php echo $monitor_active ?>';
var monitor_phone = '<?php echo $monitor_phone ?>';
var ALLINGROUPstats = '<?php echo $ALLINGROUPstats ?>';
var DROPINGROUPstats = '<?php echo $DROPINGROUPstats ?>';
var NOLEADSalert = '<?php echo $NOLEADSalert ?>';
var CARRIERstats = '<?php echo $CARRIERstats ?>';
var PRESETstats = '<?php echo $PRESETstats ?>';
var AGENTtimeSTATS = '<?php echo $AGENTtimeSTATS ?>';

// functions to hide and show different DIVs
function showDiv(divvar) 
	{
	if (document.getElementById(divvar))
		{
		divref = document.getElementById(divvar).style;
		divref.visibility = 'visible';
		if (divvar=="campaign_select_list") 
			{
			document.getElementById(divvar).style.zIndex=21;
			}
		}
	}
function hideDiv(divvar)
	{
	if (document.getElementById(divvar))
		{
		divref = document.getElementById(divvar).style;
		divref.visibility = 'hidden';
		if (divvar=="campaign_select_list") 
			{
			document.getElementById(divvar).style.zIndex=-1;
			}
		}
	}

function ShowWebphone(divvis)
	{
	if (divvis == 'show')
		{
		divref = document.getElementById('webphone_content').style;
		divref.visibility = 'visible';
		document.getElementById("webphone_visibility").innerHTML = "<a href=\"#\" onclick=\"ShowWebphone('hide');\">webphone -</a>";
		}
	else
		{
		divref = document.getElementById('webphone_content').style;
		divref.visibility = 'hidden';
		document.getElementById("webphone_visibility").innerHTML = "<a href=\"#\" onclick=\"ShowWebphone('show');\">webphone +</a>";
		}
	}

// function to launch monitoring calls
function send_monitor(session_id,server_ip,stage)
	{
	//	alert(session_id + "|" + server_ip + "|" + monitor_phone + "|" + stage + "|" + user);
	var xmlhttp=false;
	/*@cc_on @*/
	/*@if (@_jscript_version >= 5)
	// JScript gives us Conditional compilation, we can cope with old IE versions.
	// and security blocked creation of the objects.
	 try {
	  xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
	 } catch (e) {
	  try {
	   xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
	  } catch (E) {
	   xmlhttp = false;
	  }
	 }
	@end @*/
	if (!xmlhttp && typeof XMLHttpRequest!='undefined')
		{
		xmlhttp = new XMLHttpRequest();
		}
	if (xmlhttp) 
		{
		var monitorQuery = "source=realtime&function=blind_monitor&user=" + user + "&pass=" + pass + "&phone_login=" + monitor_phone + "&session_id=" + session_id + '&server_ip=' + server_ip + '&stage=' + stage;
		xmlhttp.open('POST', 'non_agent_api.php'); 
		xmlhttp.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
		xmlhttp.send(monitorQuery); 
		xmlhttp.onreadystatechange = function() 
			{ 
			if (xmlhttp.readyState == 4 && xmlhttp.status == 200) 
				{
			//	alert(xmlhttp.responseText);
				var Xoutput = null;
				Xoutput = xmlhttp.responseText;
				var regXFerr = new RegExp("ERROR","g");
				var regXFscs = new RegExp("SUCCESS","g");
				if (Xoutput.match(regXFerr))
					{alert(xmlhttp.responseText);}
				if (Xoutput.match(regXFscs))
					{alert("SUCCESS: calling " + monitor_phone);}
				}
			}
		delete xmlhttp;
		}
	}


// function to change in-groups selected for a specific agent
function submit_ingroup_changes(temp_agent_user)
	{
	var temp_ingroup_add_remove_changeIndex = document.getElementById("ingroup_add_remove_change").selectedIndex;
	var temp_ingroup_add_remove_change =  document.getElementById('ingroup_add_remove_change').options[temp_ingroup_add_remove_changeIndex].value;

	var temp_set_as_defaultIndex = document.getElementById("set_as_default").selectedIndex;
	var temp_set_as_default =  document.getElementById('set_as_default').options[temp_set_as_defaultIndex].value;

	var temp_blendedIndex = document.getElementById("blended").selectedIndex;
	var temp_blended =  document.getElementById('blended').options[temp_blendedIndex].value;

	var temp_ingroup_choices = '';
	var txtSelectedValuesObj = document.getElementById('txtSelectedValues');
	var selectedArray = new Array();
	var selObj = document.getElementById('ingroup_new_selections');
	var i;
	var count = 0;
	for (i=0; i<selObj.options.length; i++) 
		{
		if (selObj.options[i].selected) 
			{
		//	selectedArray[count] = selObj.options[i].value;
			temp_ingroup_choices = temp_ingroup_choices + '+' + selObj.options[i].value;
			count++;
			}
		}

	temp_ingroup_choices = temp_ingroup_choices + '+-';

	//	alert(session_id + "|" + server_ip + "|" + monitor_phone + "|" + stage + "|" + user);
	var xmlhttp=false;
	/*@cc_on @*/
	/*@if (@_jscript_version >= 5)
	// JScript gives us Conditional compilation, we can cope with old IE versions.
	// and security blocked creation of the objects.
	 try {
	  xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
	 } catch (e) {
	  try {
	   xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
	  } catch (E) {
	   xmlhttp = false;
	  }
	 }
	@end @*/
	if (!xmlhttp && typeof XMLHttpRequest!='undefined')
		{
		xmlhttp = new XMLHttpRequest();
		}
	if (xmlhttp) 
		{
		var changeQuery = "source=realtime&function=change_ingroups&user=" + user + "&pass=" + pass + "&agent_user=" + temp_agent_user + "&value=" + temp_ingroup_add_remove_change + '&set_as_default=' + temp_set_as_default + '&blended=' + temp_blended + '&ingroup_choices=' + temp_ingroup_choices;
		xmlhttp.open('POST', '../agc/api.php'); 
		xmlhttp.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
		xmlhttp.send(changeQuery); 
		xmlhttp.onreadystatechange = function() 
			{ 
			if (xmlhttp.readyState == 4 && xmlhttp.status == 200) 
				{
			//	alert(changeQuery);
				var Xoutput = null;
				Xoutput = xmlhttp.responseText;
				var regXFerr = new RegExp("ERROR","g");
				if (Xoutput.match(regXFerr))
					{alert(xmlhttp.responseText);}
				else
					{
					alert(xmlhttp.responseText);
					hide_ingroup_info();
					}
				}
			}
		delete xmlhttp;
		}
	}


// function to display in-groups selected for a specific agent
function ingroup_info(agent_user,count)
	{
	var cursorheight = (document.REALTIMEform.cursorY.value - 0);
	var newheight = (cursorheight + 10);
	document.getElementById("agent_ingroup_display").style.top = newheight;
	//	alert(session_id + "|" + server_ip + "|" + monitor_phone + "|" + stage + "|" + user);
	var xmlhttp=false;
	/*@cc_on @*/
	/*@if (@_jscript_version >= 5)
	// JScript gives us Conditional compilation, we can cope with old IE versions.
	// and security blocked creation of the objects.
	 try {
	  xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
	 } catch (e) {
	  try {
	   xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
	  } catch (E) {
	   xmlhttp = false;
	  }
	 }
	@end @*/
	if (!xmlhttp && typeof XMLHttpRequest!='undefined')
		{
		xmlhttp = new XMLHttpRequest();
		}
	if (xmlhttp) 
		{
		var monitorQuery = "source=realtime&function=agent_ingroup_info&stage=change&user=" + user + "&pass=" + pass + "&agent_user=" + agent_user;
		xmlhttp.open('POST', 'non_agent_api.php'); 
		xmlhttp.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
		xmlhttp.send(monitorQuery); 
		xmlhttp.onreadystatechange = function() 
			{ 
			if (xmlhttp.readyState == 4 && xmlhttp.status == 200) 
				{
			//	alert(xmlhttp.responseText);
				var Xoutput = null;
				Xoutput = xmlhttp.responseText;
				var regXFerr = new RegExp("ERROR","g");
				if (Xoutput.match(regXFerr))
					{alert(xmlhttp.responseText);}
				else
					{
					document.getElementById("agent_ingroup_display").visibility = "visible";
					document.getElementById("agent_ingroup_display").innerHTML = Xoutput;
					}
				}
			}
		delete xmlhttp;
		}
	}


// function to display in-groups selected for a specific agent
function hide_ingroup_info()
	{
	document.getElementById("agent_ingroup_display").visibility = "hidden";
	document.getElementById("agent_ingroup_display").innerHTML = '';
	}

var ar_refresh=<?php echo "$RR;"; ?>
var ar_seconds=<?php echo "$RR;"; ?>
var $start_count=0;

function realtime_refresh_display()
	{
	if ($start_count < 1)
		{
		gather_realtime_content();
		}
	$start_count++;
	if (ar_seconds > 0)
		{
		document.getElementById("refresh_countdown").innerHTML = "" + ar_seconds + "";
		ar_seconds = (ar_seconds - 1);
		setTimeout("realtime_refresh_display()",1000);
		}
	else
		{
		document.getElementById("refresh_countdown").innerHTML = "0";
		ar_seconds = ar_refresh;
		//	window.location.reload();
		gather_realtime_content();
		setTimeout("realtime_refresh_display()",1000);
		}
	}


// function to gather calls and agents statistical content
function gather_realtime_content()
	{
	var xmlhttp=false;
	/*@cc_on @*/
	/*@if (@_jscript_version >= 5)
	// JScript gives us Conditional compilation, we can cope with old IE versions.
	// and security blocked creation of the objects.
	 try {
	  xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
	 } catch (e) {
	  try {
	   xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
	  } catch (E) {
	   xmlhttp = false;
	  }
	 }
	@end @*/
	if (!xmlhttp && typeof XMLHttpRequest!='undefined')
		{
		xmlhttp = new XMLHttpRequest();
		}
	if (xmlhttp) 
		{
		RTupdate_query = "RTajax=1&DB=" + DB + "" + groupQS + "&adastats=" + adastats + "&SIPmonitorLINK=" + SIPmonitorLINK + "&IAXmonitorLINK=" + IAXmonitorLINK + "&usergroup=" + usergroup + "&UGdisplay=" + UGdisplay + "&UidORname=" + UidORname + "&orderby=" + orderby + "&SERVdisplay=" + SERVdisplay + "&CALLSdisplay=" + CALLSdisplay + "&PHONEdisplay=" + PHONEdisplay + "&CUSTPHONEdisplay=" + CUSTPHONEdisplay + "&with_inbound=" + with_inbound + "&monitor_active=" + monitor_active + "&monitor_phone=" + monitor_phone + "&ALLINGROUPstats=" + ALLINGROUPstats + "&DROPINGROUPstats=" + DROPINGROUPstats + "&NOLEADSalert=" + NOLEADSalert + "&CARRIERstats=" + CARRIERstats + "&PRESETstats=" + PRESETstats + "&AGENTtimeSTATS=" + AGENTtimeSTATS + "";

		xmlhttp.open('POST', 'AST_timeonVDADall.php'); 
		xmlhttp.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
		xmlhttp.send(RTupdate_query); 
		xmlhttp.onreadystatechange = function() 
			{ 
			if (xmlhttp.readyState == 4 && xmlhttp.status == 200) 
				{
				document.getElementById("realtime_content").innerHTML = xmlhttp.responseText;
		//		alert(xmlhttp.responseText);
				}
			}
		delete xmlhttp;
		}
	}


// function to update variables based upon on-page links and forms without reload page(in most cases)
function update_variables(task_option,task_choice,force_reload)
	{
	if (task_option == 'SIPmonitorLINK')
		{
		if (SIPmonitorLINK == '1') {SIPmonitorLINK='0';}
		else {SIPmonitorLINK='1';}
		}
	if (task_option == 'IAXmonitorLINK')
		{
		if (IAXmonitorLINK == '1') {IAXmonitorLINK='0';}
		else {IAXmonitorLINK='1';}
		}
	if (task_option == 'UidORname')
		{
		if (UidORname == '1') {UidORname='0';}
		else {UidORname='1';}
		}
	if (task_option == 'orderby')
		{
		if (task_choice == 'phone')
			{
			if (orderby=='phoneup') {orderby='phonedown';}
			else {orderby='phoneup';}
			}
		if (task_choice == 'user')
			{
			if (orderby=='userup') {orderby='userdown';}
			else {orderby='userup';}
			}
		if (task_choice == 'group')
			{
			if (orderby=='groupup') {orderby='groupdown';}
			else {orderby='groupup';}
			}
		if (task_choice == 'time')
			{
			if (orderby=='timeup') {orderby='timedown';}
			else {orderby='timeup';}
			}
		if (task_choice == 'status')
			{
			if (orderby=='statusup') {orderby='statusdown';}
			else {orderby='statusup';}
			}
		if (task_choice == 'campaign')
			{
			if (orderby=='campaignup') {orderby='campaigndown';}
			else {orderby='campaignup';}
			}
		}
	if (task_option == 'adastats')
		{
		if (adastats == '1') {adastats='2';   document.getElementById("adastatsTXT").innerHTML = '- VIEW LESS';}
		else {adastats='1';   document.getElementById("adastatsTXT").innerHTML = '+ VIEW MORE';}
		}
	if (task_option == 'UGdisplay')
		{
		if (UGdisplay == '1') {UGdisplay='0';   document.getElementById("UGdisplayTXT").innerHTML = 'VIEW USER GROUP';}
		else {UGdisplay='1';   document.getElementById("UGdisplayTXT").innerHTML = 'HIDE USER GROUP';}
		}
	if (task_option == 'SERVdisplay')
		{
		if (SERVdisplay == '1') {SERVdisplay='0';   document.getElementById("SERVdisplayTXT").innerHTML = 'SHOW SERVER INFO';}
		else {SERVdisplay='1';   document.getElementById("SERVdisplayTXT").innerHTML = 'HIDE SERVER INFO';}
		}
	if (task_option == 'CALLSdisplay')
		{
		if (CALLSdisplay == '1') {CALLSdisplay='0';   document.getElementById("CALLSdisplayTXT").innerHTML = 'SHOW WAITING CALLS';}
		else {CALLSdisplay='1';   document.getElementById("CALLSdisplayTXT").innerHTML = 'HIDE WAITING CALLS';}
		}
	if (task_option == 'PHONEdisplay')
		{
		if (PHONEdisplay == '1') {PHONEdisplay='0';   document.getElementById("PHONEdisplayTXT").innerHTML = 'SHOW PHONES';}
		else {PHONEdisplay='1';   document.getElementById("PHONEdisplayTXT").innerHTML = 'HIDE PHONES';}
		}
	if (task_option == 'CUSTPHONEdisplay')
		{
		if (CUSTPHONEdisplay == '1') {CUSTPHONEdisplay='0';   document.getElementById("CUSTPHONEdisplayTXT").innerHTML = 'SHOW CUSTPHONES';}
		else {CUSTPHONEdisplay='1';   document.getElementById("CUSTPHONEdisplayTXT").innerHTML = 'HIDE CUSTPHONES';}
		}
	if (task_option == 'ALLINGROUPstats')
		{
		if (ALLINGROUPstats == '1') {ALLINGROUPstats='0';   document.getElementById("ALLINGROUPstatsTXT").innerHTML = 'SHOW IN-GROUP STATS';}
		else {ALLINGROUPstats='1';   document.getElementById("ALLINGROUPstatsTXT").innerHTML = 'HIDE IN-GROUP STATS';}
		}
	if (task_option == 'form_submit')
		{
		var RRFORM = document.getElementById('RR');
		RR = RRFORM[RRFORM.selectedIndex].value;
		ar_refresh=RR;
		ar_seconds=RR;
		var with_inboundFORM = document.getElementById('with_inbound');
		with_inbound = with_inboundFORM[with_inboundFORM.selectedIndex].value;
		var monitor_activeFORM = document.getElementById('monitor_active');
		monitor_active = monitor_activeFORM[monitor_activeFORM.selectedIndex].value;
		var DROPINGROUPstatsFORM = document.getElementById('DROPINGROUPstats');
		DROPINGROUPstats = DROPINGROUPstatsFORM[DROPINGROUPstatsFORM.selectedIndex].value;
		var NOLEADSalertFORM = document.getElementById('NOLEADSalert');
		NOLEADSalert = NOLEADSalertFORM[NOLEADSalertFORM.selectedIndex].value;
		var CARRIERstatsFORM = document.getElementById('CARRIERstats');
		CARRIERstats = CARRIERstatsFORM[CARRIERstatsFORM.selectedIndex].value;
		<?php
		if ($presets_enabled > 0)
			{
			?>
		var PRESETstatsFORM = document.getElementById('PRESETstats');
		PRESETstats = PRESETstatsFORM[PRESETstatsFORM.selectedIndex].value;
			<?php
			}
		else
			{echo "PRESETstats=0;\n";}
		?>
		var AGENTtimeSTATSFORM = document.getElementById('AGENTtimeSTATS');
		AGENTtimeSTATS = AGENTtimeSTATSFORM[AGENTtimeSTATSFORM.selectedIndex].value;
		var temp_monitor_phone = document.REALTIMEform.monitor_phone.value;

		var temp_camp_choices = '';
		var selCampObj = document.getElementById('groups[]');
		var i;
		var count = 0;
		var selected_all=0;
		for (i=0; i<selCampObj.options.length; i++) 
			{
			if ( (selCampObj.options[i].selected) && (selected_all < 1) )
				{
				temp_camp_choices = temp_camp_choices + '&groups[]=' + selCampObj.options[i].value;
				count++;
				if (selCampObj.options[i].value == 'ALL-ACTIVE')
					{selected_all++;}
				}
			}
		groupQS = temp_camp_choices;
		hideDiv('campaign_select_list');

		// force a reload if the phone is changed
		if ( (temp_monitor_phone != monitor_phone) || (force_reload=='YES') )
			{
			reload_url = PHP_SELF + "?RR=" + RR + "&DB=" + DB + "" + groupQS + "&adastats=" + adastats + "&SIPmonitorLINK=" + SIPmonitorLINK + "&IAXmonitorLINK=" + IAXmonitorLINK + "&usergroup=" + usergroup + "&UGdisplay=" + UGdisplay + "&UidORname=" + UidORname + "&orderby=" + orderby + "&SERVdisplay=" + SERVdisplay + "&CALLSdisplay=" + CALLSdisplay + "&PHONEdisplay=" + PHONEdisplay + "&CUSTPHONEdisplay=" + CUSTPHONEdisplay + "&with_inbound=" + with_inbound + "&monitor_active=" + monitor_active + "&monitor_phone=" + temp_monitor_phone + "&ALLINGROUPstats=" + ALLINGROUPstats + "&DROPINGROUPstats=" + DROPINGROUPstats + "&NOLEADSalert=" + NOLEADSalert + "&CARRIERstats=" + CARRIERstats + "&PRESETstats=" + PRESETstats + "&AGENTtimeSTATS=" + AGENTtimeSTATS + "";

		//	alert('|' + temp_monitor_phone + '|' + monitor_phone + '|\n' + reload_url);
			window.location.href = reload_url;
			}

		monitor_phone = document.REALTIMEform.monitor_phone.value;
		}
	gather_realtime_content();
	}


</script>

<STYLE type="text/css">
<!--
	.green {color: white; background-color: green}
	.red {color: white; background-color: red}
	.lightblue {color: black; background-color: #ADD8E6}
	.blue {color: white; background-color: blue}
	.midnightblue {color: white; background-color: #191970}
	.purple {color: white; background-color: purple}
	.violet {color: black; background-color: #EE82EE} 
	.thistle {color: black; background-color: #D8BFD8} 
	.olive {color: white; background-color: #808000}
	.lime {color: white; background-color: #006600}
	.yellow {color: black; background-color: yellow}
	.khaki {color: black; background-color: #F0E68C}
	.orange {color: black; background-color: orange}
	.black {color: white; background-color: black}
	.salmon {color: white; background-color: #FA8072}

	.r1 {color: black; background-color: #FFCCCC}
	.r2 {color: black; background-color: #FF9999}
	.r3 {color: black; background-color: #FF6666}
	.r4 {color: white; background-color: #FF0000}
	.b1 {color: black; background-color: #CCCCFF}
	.b2 {color: black; background-color: #9999FF}
	.b3 {color: black; background-color: #6666FF}
	.b4 {color: white; background-color: #0000FF}
<?php
	$stmt="select group_id,group_color from vicidial_inbound_groups;";
	$rslt=mysql_query($stmt, $link);
	if ($DB) {echo "$stmt\n";}
	$INgroups_to_print = mysql_num_rows($rslt);
		if ($INgroups_to_print > 0)
		{
		$g=0;
		while ($g < $INgroups_to_print)
			{
			$row=mysql_fetch_row($rslt);
			$group_id[$g] = $row[0];
			$group_color[$g] = $row[1];
			echo "   .csc$group_id[$g] {color: black; background-color: $group_color[$g]}\n";
			$g++;
			}
		}

echo "\n-->\n
</STYLE>\n";

$stmt = "select count(*) from vicidial_campaigns where active='Y' and campaign_allow_inbound='Y' $group_SQLand;";
$rslt=mysql_query($stmt, $link);
	if ($DB) {echo "$stmt\n";}
	$row=mysql_fetch_row($rslt);
	$campaign_allow_inbound = $row[0];

echo "<META HTTP-EQUIV=\"Content-Type\" CONTENT=\"text/html; charset=utf-8\">\n";
echo "<TITLE>$report_name: $group</TITLE></HEAD><BODY BGCOLOR=WHITE marginheight=0 marginwidth=0 leftmargin=0 topmargin=0>\n";

	$short_header=1;

	require("admin_header.php");

echo "<TABLE CELLPADDING=4 CELLSPACING=0><TR><TD>";

echo "<FORM ACTION=\"$PHP_SELF\" METHOD=GET NAME=REALTIMEform ID=REALTIMEform>\n";
echo "<INPUT TYPE=HIDDEN NAME=cursorX ID=cursorX>\n";
echo "<INPUT TYPE=HIDDEN NAME=cursorY ID=cursorY>\n";

#echo "<INPUT TYPE=HIDDEN NAME=RR ID=RR VALUE=\"$RR\">\n";
#echo "<INPUT TYPE=HIDDEN NAME=DB ID=DB VALUE=\"$DB\">\n";
#echo "<INPUT TYPE=HIDDEN NAME=adastats ID=adastats VALUE=\"$adastats\">\n";
#echo "<INPUT TYPE=HIDDEN NAME=SIPmonitorLINK ID=SIPmonitorLINK VALUE=\"$SIPmonitorLINK\">\n";
#echo "<INPUT TYPE=HIDDEN NAME=IAXmonitorLINK ID=IAXmonitorLINK VALUE=\"$IAXmonitorLINK\">\n";
#echo "<INPUT TYPE=HIDDEN NAME=usergroup ID=usergroup VALUE=\"$usergroup\">\n";
#echo "<INPUT TYPE=HIDDEN NAME=UGdisplay ID=UGdisplay VALUE=\"$UGdisplay\">\n";
#echo "<INPUT TYPE=HIDDEN NAME=UidORname ID=UidORname VALUE=\"$UidORname\">\n";
#echo "<INPUT TYPE=HIDDEN NAME=orderby ID=orderby VALUE=\"$orderby\">\n";
#echo "<INPUT TYPE=HIDDEN NAME=SERVdisplay ID=SERVdisplay VALUE=\"$SERVdisplay\">\n";
#echo "<INPUT TYPE=HIDDEN NAME=CALLSdisplay ID=CALLSdisplay VALUE=\"$CALLSdisplay\">\n";
#echo "<INPUT TYPE=HIDDEN NAME=PHONEdisplay ID=PHONEdisplay VALUE=\"$PHONEdisplay\">\n";
#echo "<INPUT TYPE=HIDDEN NAME=CUSTPHONEdisplay ID=CUSTPHONEdisplay VALUE=\"$CUSTPHONEdisplay\">\n";
#echo "<INPUT TYPE=HIDDEN NAME=DROPINGROUPstats ID=DROPINGROUPstats VALUE=\"$DROPINGROUPstats\">\n";
#echo "<INPUT TYPE=HIDDEN NAME=ALLINGROUPstats ID=ALLINGROUPstats VALUE=\"$ALLINGROUPstats\">\n";
#echo "<INPUT TYPE=HIDDEN NAME=CARRIERstats ID=CARRIERstats VALUE=\"$CARRIERstats\">\n";
#echo "<INPUT TYPE=HIDDEN NAME=PRESETstats ID=PRESETstats VALUE=\"$PRESETstats\">\n";
#echo "<INPUT TYPE=HIDDEN NAME=AGENTtimeSTATS ID=AGENTtimeSTATS VALUE=\"$AGENTtimeSTATS\">\n";

echo "Real-Time Report &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; \n";
echo "<span style=\"position:absolute;left:160px;top:27px;z-index:20;\" id=campaign_select_list_link>\n";
echo "<TABLE WIDTH=250 CELLPADDING=0 CELLSPACING=0 BGCOLOR=\"#D9E6FE\"><TR><TD ALIGN=CENTER>\n";
echo "<a href=\"#\" onclick=\"showDiv('campaign_select_list');\">Choose Report Display Options</a>";
echo "</TD></TR></TABLE>\n";
echo "</span>\n";
echo "<span style=\"position:absolute;left:0px;top:27px;z-index:21;\" id=campaign_select_list>\n";
echo "<TABLE WIDTH=0 HEIGHT=0 CELLPADDING=0 CELLSPACING=0 BGCOLOR=\"#D9E6FE\"><TR><TD ALIGN=CENTER>\n";
echo "";
echo "</TD></TR></TABLE>\n";
echo "</span>\n";
echo "<span style=\"position:absolute;left:" . $webphone_left . "px;top:" . $webphone_top . "px;z-index:18;\" id=webphone_content>\n";
echo "<TABLE WIDTH=" . $webphone_bufw . " CELLPADDING=" . $webphone_pad . " CELLSPACING=0 BGCOLOR=\"white\"><TR><TD ALIGN=LEFT>\n";
echo "$webphone_content\n$webphone_clpos\n";
echo "</TD></TR></TABLE>\n";
echo "</span>\n";
echo "<span style=\"position:absolute;left:10px;top:120px;z-index:14;\" id=agent_ingroup_display>\n";
echo " &nbsp; ";
echo "</span>\n";
echo "<a href=\"#\" onclick=\"update_variables('form_submit','','YES')\">RELOAD NOW</a>";
if (eregi('ALL-ACTIVE',$group_string))
	{echo " &nbsp; &nbsp; &nbsp; <a href=\"./admin.php?ADD=10\">MODIFY</a> | \n";}
else
	{echo " &nbsp; &nbsp; &nbsp; <a href=\"./admin.php?ADD=34&campaign_id=$group\">MODIFY</a> | \n";}

echo "<a href=\"./AST_timeonVDADallSUMMARY.php?RR=$RR&DB=$DB&adastats=$adastats\">SUMMARY</a> </FONT>\n";


echo " &nbsp; &nbsp; &nbsp; refresh: <span id=refresh_countdown name=refresh_countdown></span>\n\n";

if ($is_webphone == 'Y')
	{echo " &nbsp; &nbsp; &nbsp; <span id=webphone_visibility name=webphone_visibility><a href=\"#\" onclick=\"ShowWebphone('show');\">webphone +</a></span>\n\n";}
else
	{echo " &nbsp; &nbsp; &nbsp; <span id=webphone_visibility name=webphone_visibility></span>\n\n";}

if ($webphone_bufh > 10)
	{echo "<BR><img src=\"images/pixel.gif\" width=1 height=$webphone_bufh>\n";}
echo "<BR>\n\n";


if ($adastats<2)
	{echo " &nbsp; &nbsp; &nbsp; <a href=\"#\" onclick=\"update_variables('adastats','');\"><font size=1><span id=adastatsTXT>+ VIEW MORE</span></font></a>";}
else
	{echo " &nbsp; &nbsp; &nbsp; <a href=\"#\" onclick=\"update_variables('adastats','');\"><font size=1><span id=adastatsTXT>- VIEW LESS</span></font></a>";}
if ($UGdisplay>0)
	{echo " &nbsp; &nbsp; &nbsp; <a href=\"#\" onclick=\"update_variables('UGdisplay','');\"><font size=1><span id=UGdisplayTXT>HIDE USER GROUP</span></font></a>";}
else
	{echo " &nbsp; &nbsp; &nbsp; <a href=\"#\" onclick=\"update_variables('UGdisplay','');\"><font size=1><span id=UGdisplayTXT>VIEW USER GROUP</span></font></a>";}
if ($SERVdisplay>0)
	{echo " &nbsp; &nbsp; &nbsp; <a href=\"#\" onclick=\"update_variables('SERVdisplay','');\"><font size=1><span id=SERVdisplayTXT>HIDE SERVER INFO</span></font></a>";}
else
	{echo " &nbsp; &nbsp; &nbsp; <a href=\"#\" onclick=\"update_variables('SERVdisplay','');\"><font size=1><span id=SERVdisplayTXT>SHOW SERVER INFO</span></font></a>";}
if ($CALLSdisplay>0)
	{echo " &nbsp; &nbsp; &nbsp; <a href=\"#\" onclick=\"update_variables('CALLSdisplay','');\"><font size=1><span id=CALLSdisplayTXT>HIDE WAITING CALLS</span></font></a>";}
else
	{echo " &nbsp; &nbsp; &nbsp; <a href=\"#\" onclick=\"update_variables('CALLSdisplay','');\"><font size=1><span id=CALLSdisplayTXT>SHOW WAITING CALLS</span></font></a>";}
if ($ALLINGROUPstats>0)
	{echo " &nbsp; &nbsp; &nbsp; <a href=\"#\" onclick=\"update_variables('ALLINGROUPstats','');\"><font size=1><span id=ALLINGROUPstatsTXT>HIDE IN-GROUP STATS</span></font></a>";}
else
	{echo " &nbsp; &nbsp; &nbsp; <a href=\"#\" onclick=\"update_variables('ALLINGROUPstats','');\"><font size=1><span id=ALLINGROUPstatsTXT>SHOW IN-GROUP STATS</span></font></a>";}
if ($PHONEdisplay>0)
	{echo " &nbsp; &nbsp; &nbsp; <a href=\"#\" onclick=\"update_variables('PHONEdisplay','');\"><font size=1><span id=PHONEdisplayTXT>HIDE PHONES</span></font></a>";}
else
	{echo " &nbsp; &nbsp; &nbsp; <a href=\"#\" onclick=\"update_variables('PHONEdisplay','');\"><font size=1><span id=PHONEdisplayTXT>SHOW PHONES</span></font></a>";}
if ($CUSTPHONEdisplay>0)
	{echo " &nbsp; &nbsp; &nbsp; <a href=\"#\" onclick=\"update_variables('CUSTPHONEdisplay','');\"><font size=1><span id=CUSTPHONEdisplayTXT>HIDE CUSTPHONES</span></font></a>";}
else
	{echo " &nbsp; &nbsp; &nbsp; <a href=\"#\" onclick=\"update_variables('CUSTPHONEdisplay','');\"><font size=1><span id=CUSTPHONEdisplayTXT>SHOW CUSTPHONES</span></font></a>";}

#echo "</TD></TR></TABLE>";
##### END header formatting #####

#echo "<TABLE CELLPADDING=4 CELLSPACING=0><TR><TD>";

echo "<span id=realtime_content name=realtime_content></span>\n";








?>
</TD></TR></TABLE>
</FORM>

</BODY></HTML>
