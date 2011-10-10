<?php 
# AST_timeonVDADall.php
# 
# Copyright (C) 2010  Matt Florell <vicidial@gmail.com>    LICENSE: AGPLv2
#
# live real-time stats for the VICIDIAL Auto-Dialer all servers
#
# STOP=4000, SLOW=40, GO=4 seconds refresh interval
# 
# CHANGELOG:
# 50406-0920 - Added Paused agents < 1 min
# 51130-1218 - Modified layout and info to show all servers in a vicidial system
# 60421-1043 - check GET/POST vars lines with isset to not trigger PHP NOTICES
# 60511-1343 - Added leads and drop info at the top of the screen
# 60608-1539 - Fixed CLOSER tallies for active calls
# 60619-1658 - Added variable filtering to eliminate SQL injection attack threat
#            - Added required user/pass to gain access to this page
# 60626-1453 - Added display of system load to bottom (Angelito Manansala)
# 60901-1123 - Changed display elements at the top of the screen
# 60905-1342 - Fixed non INCALL|QUEUE timer column
# 61002-1642 - Added TRUNK SHORT/FILL stats
# 61101-1318 - Added SIP and IAX Listen and Barge links option
# 61101-1647 - Added Usergroup column and user name option as well as sorting
# 61102-1155 - Made display of columns more modular, added ability to hide server info
# 61215-1131 - Added answered calls and drop percent taken from answered calls
# 70111-1600 - Added ability to use BLEND/INBND/*_C/*_B/*_I as closer campaigns
# 70123-1151 - Added non_latin options for substr in display variables, thanks Marin Blu
# 70206-1140 - Added call-type statuses to display(A-Auto, M-Manual, I-Inbound/Closer)
# 70619-1339 - Added Status Category tally display
# 71029-1900 - Changed CLOSER-type to not require campaign_id restriction
# 80227-0418 - Added priority to waiting calls display
# 80311-1550 - Added calls_today on all agents and wait time/in-group for inbound calls
# 80422-0033 - Added phonediaplay option, allow for toggle-sorting on sortable fields
# 80422-1001 - Fixed sort by phone login
# 80424-0515 - Added non_latin lookup from system_settings
# 80525-1040 - Added IVR status display and summary for inbound calls
# 80619-2047 - Added DISPO status for post-call-work while paused
# 80704-0543 - Added DEAD status for agents INCALL with no live call
# 80822-1222 - Added option for display of customer phone number
# 81011-0335 - Fixed remote agent display bug
# 81022-1500 - Added inbound call stats display option
# 81029-1023 - Changed drop percent calculation for multi-stat reports
# 81029-1706 - Added pause code display if enabled per campaign
# 81108-2337 - Added inbound-only section
# 90105-1153 - Changed monitor links to use 0 prefix instead of 6
# 90202-0108 - Changed options to pop-out frame, added outbound_autodial_active option
# 90310-0906 - Added admin header
# 90428-0727 - Changed listen and barge to use the API and manager must enter phone
# 90508-0623 - Changed to PHP long tags
# 90518-0930 - Fixed $CALLSdisplay static assignment bug for some links(bug #210)
# 90524-2231 - Changed to use functions.php for seconds to HH:MM:SS conversion
# 90602-0405 - Added list mix display in statuses and order if active
# 90603-1845 - Fixed color coding bug
# 90627-0608 - Some Formatting changes, added in-group name display
# 90701-0657 - Fixed inbound=No calculation issues
# 90808-0212 - Fixed inbound only non-ALL bug, changed times to use agent last_state_change
# 90907-0915 - Added PARK status
# 90914-1154 - Added AgentOnly display column to waiting calls section
# 91102-2013 - Changed in-group color styles for incoming calls waiting
# 91204-1548 - Added ability to change agent in-groups and blended
# 100214-1127 - Added no-dialable-leads alert and in-groups stats option
# 100301-1229 - Added 3-WAY status for consultative transfer agents
# 100303-0930 - Added carrier stats display option
#

$version = '2.2.0-52';
$build = '100303-0930';

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
if (isset($_GET["ENVIAR"]))				{$ENVIAR=$_GET["ENVIAR"];}
	elseif (isset($_POST["ENVIAR"]))	{$ENVIAR=$_POST["ENVIAR"];}
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


#############################################
##### START SYSTEM_SETTINGS LOOKUP #####
$stmt = "SELECT use_non_latin,outbound_autodial_active FROM system_settings;";
$rslt=mysql_query($stmt, $link);
if ($DB) {echo "$stmt\n";}
$qm_conf_ct = mysql_num_rows($rslt);
if ($qm_conf_ct > 0)
	{
	$row=mysql_fetch_row($rslt);
	$non_latin =					$row[0];
	$outbound_autodial_active =		$row[1];
	}
##### END SETTINGS LOOKUP #####
###########################################

if (!isset($DB))			{$DB=0;}
if (!isset($RR))			{$RR=40;}
if (!isset($group))			{$group='';}
if (!isset($usergroup))		{$usergroup='';}
if (!isset($UGdisplay))		{$UGdisplay=0;}	# 0=no, 1=yes
if (!isset($UidORname))		{$UidORname=1;}	# 0=id, 1=name
if (!isset($orderby))		{$orderby='timeup';}
if (!isset($SERVdisplay))	{$SERVdisplay=0;}	# 0=no, 1=yes
if (!isset($CALLSdisplay))	{$CALLSdisplay=1;}	# 0=no, 1=yes
if (!isset($PHONEdisplay))	{$PHONEdisplay=0;}	# 0=no, 1=yes
if (!isset($CUSTPHONEdisplay))	{$CUSTPHONEdisplay=0;}	# 0=no, 1=yes
if (!isset($PAUSEcodes))	{$PAUSEcodes='N';}  # 0=no, 1=yes
if (!isset($with_inbound))	
	{
	if ($outbound_autodial_active > 0)
		{$with_inbound='Y';}  # N=no, Y=yes, O=only
	else
		{$with_inbound='O';}  # N=no, Y=yes, O=only
	}
$ingroup_detail='';

if (strlen($group)>1) {$groups[0] = $group;  $RR=40;}
else {$group = $groups[0];}

function get_server_load($windows = false) 
	{
	$os = strtolower(PHP_OS);
	if(strpos($os, "win") === false) 
		{
		if(file_exists("/proc/loadavg")) 
			{
			$load = file_get_contents("/proc/loadavg");
			$load = explode(' ', $load);
			return $load[0] . ' ' . $load[1] . ' ' . $load[2];
			}
		elseif(function_exists("shell_exec")) 
			{
			$load = explode(' ', `uptime`);
			return $load[count($load)-3] . ' ' . $load[count($load)-2] . ' ' . $load[count($load)-1];
			}
		else 
			{
		return false;
			}
		}
	elseif($windows) 
		{
		if(class_exists("COM")) 
			{
			$wmi = new COM("WinMgmts:\\\\.");
			$cpus = $wmi->InstancesOf("Win32_Processor");

			$cpuload = 0;
			$i = 0;
			while ($cpu = $cpus->Next()) 
				{
				$cpuload += $cpu->LoadPercentage;
				$i++;
				}

			$cpuload = round($cpuload / $i, 2);
			return "$cpuload%";
			}
		else 
			{
			return false;
			}
		}
	}

$load_ave = get_server_load(true);

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


$PHP_AUTH_USER = ereg_replace("[^0-9a-zA-Z]","",$PHP_AUTH_USER);
$PHP_AUTH_PW = ereg_replace("[^0-9a-zA-Z]","",$PHP_AUTH_PW);

$stmt="SELECT count(*) from vicidial_users where user='$PHP_AUTH_USER' and pass='$PHP_AUTH_PW' and user_level > 6 and view_reports='1' and active='Y';";
if ($DB) {echo "|$stmt|\n";}
if ($non_latin > 0) {$rslt=mysql_query("SET NAMES 'UTF8'");}
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
#  and (preg_match("/MONITOR|BARGE|HIJACK/",$monitor_active) ) )
if ( (!isset($monitor_phone)) or (strlen($monitor_phone)<1) )
	{
	$stmt="select phone_login from vicidial_users where user='$PHP_AUTH_USER' and pass='$PHP_AUTH_PW' and active='Y';";
	$rslt=mysql_query($stmt, $link);
	if ($DB) {echo "$stmt\n";}
	$row=mysql_fetch_row($rslt);
	$monitor_phone = $row[0];
	}

$stmt="select campaign_id,campaign_name from vicidial_campaigns where active='Y' order by campaign_id;";
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
	$i++;
	}

$i=0;
$group_string='|';
$group_ct = count($groups);
while($i < $group_ct)
	{
	$group_string .= "$groups[$i]|";
	$group_SQL .= "'$groups[$i]',";
	$groupQS .= "&groups[]=$groups[$i]";
	$i++;
	}
$group_SQL = eregi_replace(",$",'',$group_SQL);

### if no campaigns selected, display all
if ($group_ct < 1)
	{
	$groups[0] = 'ALL-ACTIVE';
	$group_string = 'ALL-ACTIVE';
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
	$group_SQL = "''";
	$group_SQLand = "";
	$group_SQLwhere = "";
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

$select_list = "<TABLE WIDTH=700 CELLPADDING=5 BGCOLOR=\"#D9E6FE\"><TR><TD VALIGN=TOP>Select Campanhas: <BR>";
$select_list .= "<SELECT SIZE=15 NAME=groups[] multiple>";
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
$select_list .= "<BR><font size=1>(Para selecionar mais de 1 campanha, aperte Ctrl e clique)<font>";
$select_list .= "</TD><TD VALIGN=TOP ALIGN=CENTER>";
$select_list .= "<a href=\"#\" onclick=\"closeDiv(\'campaign_select_list\');\">Fechar Painel</a><BR><BR>";
$select_list .= "<TABLE CELLPADDING=2 CELLSPACING=2 Border=0>";

$select_list .= "<TR><TD align=right>";
$select_list .= "Inbound:  </TD><TD align=left><SELECT SIZE=1 NAME=with_inbound>";
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
$select_list .= "Monitor:  </TD><TD align=left><SELECT SIZE=1 NAME=monitor_active>";
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
$select_list .= "<INPUT type=text size=10 maxlength=20 NAME=monitor_phone VALUE=\"$monitor_phone\">";
$select_list .= "</TD></TR>";
$select_list .= "<TR><TD align=center COLSPAN=2> &nbsp; </TD></TR>";

if ($UGdisplay > 0)
	{
	$select_list .= "<TR><TD align=right>";
	$select_list .= "Select Grupo do Usuário:  </TD><TD align=left>";
	$select_list .= "<SELECT SIZE=1 NAME=usergroup>";
	$select_list .= "<option value=\"\">ALL GRUPOS DE USUÁRIOS</option>";
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
$select_list .= "Dialable Leads Alert:  </TD><TD align=left><SELECT SIZE=1 NAME=NOLEADSalert>";
$select_list .= "<option value=\"\"";
	if (strlen($NOLEADSalert) < 2) {$select_list .= " selected";} 
$select_list .= ">NO</option>";
$select_list .= "<option value=\"YES\"";
	if ($NOLEADSalert=='YES') {$select_list .= " selected";} 
$select_list .= ">YES</option>";
$select_list .= "</SELECT></TD></TR>";

$select_list .= "<TR><TD align=right>";
$select_list .= "Show Drop In-Group Row:  </TD><TD align=left><SELECT SIZE=1 NAME=DROPINGROUPstats>";
$select_list .= "<option value=\"0\"";
	if ($DROPINGROUPstats < 1) {$select_list .= " selected";} 
$select_list .= ">NO</option>";
$select_list .= "<option value=\"1\"";
	if ($DROPINGROUPstats=='1') {$select_list .= " selected";} 
$select_list .= ">YES</option>";
$select_list .= "</SELECT></TD></TR>";

$select_list .= "<TR><TD align=right>";
$select_list .= "Show Carrier Stats:  </TD><TD align=left><SELECT SIZE=1 NAME=CARRIERstats>";
$select_list .= "<option value=\"0\"";
	if ($CARRIERstats < 1) {$select_list .= " selected";} 
$select_list .= ">NO</option>";
$select_list .= "<option value=\"1\"";
	if ($CARRIERstats=='1') {$select_list .= " selected";} 
$select_list .= ">YES</option>";
$select_list .= "</SELECT></TD></TR>";


$select_list .= "</TABLE><BR>";
$select_list .= "<INPUT type=submit NAME=ENVIAR VALUE=ENVIAR><FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2> &nbsp; &nbsp; &nbsp; &nbsp; ";
$select_list .= "</TD></TR>";
$select_list .= "<TR><TD ALIGN=CENTER>";
$select_list .= "<font size=1> &nbsp; </font>";
$select_list .= "</TD>";
$select_list .= "<TD NOWRAP align=right>";
$select_list .= "<font size=1>VERSÃO: $version &nbsp; BUILD: $build</font>";
$select_list .= "</TD></TR></TABLE>";

$open_list = "<TABLE WIDTH=250 CELLPADDING=0 CELLSPACING=0 BGCOLOR=\"#D9E6FE\"><TR><TD ALIGN=CENTER><a href=\"#\" onclick=\"openDiv(\'campaign_select_list\');\"><font size=2>Escolher Opções do Relat.</a></TD></TR></TABLE>";

?>

<HTML>
<HEAD>

<script language="Javascript">

window.onload = startup;

// function to detect the XY position on the page of the mouse
function startup() 
	{
	hide_ingroup_info();
	if (window.Event) 
		{
		document.captureEvents(Event.MOUSEMOVE);
		}
	document.onmousemove = getCursorXY;
	}

function getCursorXY(e) 
	{
	document.getElementById('cursorX').value = (window.Event) ? e.pageX : event.clientX + (document.documentElement.scrollLeft ? document.documentElement.scrollLeft : document.body.scrollLeft);
	document.getElementById('cursorY').value = (window.Event) ? e.pageY : event.clientY + (document.documentElement.scrollTop ? document.documentElement.scrollTop : document.body.scrollTop);
	}

var select_list = '<?php echo $select_list ?>';
var open_list = '<?php echo $open_list ?>';
var monitor_phone = '<?php echo $monitor_phone ?>';
var user = '<?php echo $PHP_AUTH_USER ?>';
var pass = '<?php echo $PHP_AUTH_PW ?>';

// functions to hide and show different DIVs
function openDiv(divvar) 
	{
	document.getElementById(divvar).innerHTML = select_list;
	document.getElementById(divvar).style.left = 0;
	}
function closeDiv(divvar)
	{
	document.getElementById(divvar).innerHTML = open_list;
	document.getElementById(divvar).style.left = 160;
	}
function closeAlert(divvar)
	{
	document.getElementById(divvar).innerHTML = '';
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
echo"<META HTTP-EQUIV=Refresh CONTENT=\"$RR; URL=$PHP_SELF?RR=$RR&DB=$DB$groupQS&adastats=$adastats&SIPmonitorLINK=$SIPmonitorLINK&IAXmonitorLINK=$IAXmonitorLINK&usergroup=$usergroup&UGdisplay=$UGdisplay&UidORname=$UidORname&orderby=$orderby&SERVdisplay=$SERVdisplay&CALLSdisplay=$CALLSdisplay&PHONEdisplay=$PHONEdisplay&CUSTPHONEdisplay=$CUSTPHONEdisplay&with_inbound=$with_inbound&monitor_active=$monitor_active&monitor_phone=$monitor_phone&ALLINGROUPstats=$ALLINGROUPstats&DROPINGROUPstats=$DROPINGROUPstats&NOLEADSalert=$NOLEADSalert&CARRIERstats=$CARRIERstats\">\n";
echo "<TITLE>Tempo Real Report: $group</TITLE></HEAD><BODY BGCOLOR=WHITE marginheight=0 marginwidth=0 leftmargin=0 topmargin=0>\n";

	$short_header=1;

	require("admin_header.php");

echo "<TABLE CELLPADDING=4 CELLSPACING=0><TR><TD>";

echo "<FORM ACTION=\"$PHP_SELF\" METHOD=GET NAME=REALTIMEform ID=REALTIMEform>\n";
echo "<INPUT TYPE=HIDDEN NAME=RR VALUE=\"$RR\">\n";
echo "<INPUT TYPE=HIDDEN NAME=DB VALUE=\"$DB\">\n";
echo "<INPUT TYPE=HIDDEN NAME=cursorX ID=cursorX>\n";
echo "<INPUT TYPE=HIDDEN NAME=cursorY ID=cursorY>\n";
echo "<INPUT TYPE=HIDDEN NAME=adastats VALUE=\"$adastats\">\n";
echo "<INPUT TYPE=HIDDEN NAME=SIPmonitorLINK VALUE=\"$SIPmonitorLINK\">\n";
echo "<INPUT TYPE=HIDDEN NAME=IAXmonitorLINK VALUE=\"$IAXmonitorLINK\">\n";
echo "<INPUT TYPE=HIDDEN NAME=usergroup VALUE=\"$usergroup\">\n";
echo "<INPUT TYPE=HIDDEN NAME=UGdisplay VALUE=\"$UGdisplay\">\n";
echo "<INPUT TYPE=HIDDEN NAME=UidORname VALUE=\"$UidORname\">\n";
echo "<INPUT TYPE=HIDDEN NAME=orderby VALUE=\"$orderby\">\n";
echo "<INPUT TYPE=HIDDEN NAME=SERVdisplay VALUE=\"$SERVdisplay\">\n";
echo "<INPUT TYPE=HIDDEN NAME=CALLSdisplay VALUE=\"$CALLSdisplay\">\n";
echo "<INPUT TYPE=HIDDEN NAME=PHONEdisplay VALUE=\"$PHONEdisplay\">\n";
echo "<INPUT TYPE=HIDDEN NAME=CUSTPHONEdisplay VALUE=\"$CUSTPHONEdisplay\">\n";
echo "<INPUT TYPE=HIDDEN NAME=DROPINGROUPstats VALUE=\"$DROPINGROUPstats\">\n";
echo "<INPUT TYPE=HIDDEN NAME=ALLINGROUPstats VALUE=\"$ALLINGROUPstats\">\n";
echo "<INPUT TYPE=HIDDEN NAME=CARRIERstats VALUE=\"$CARRIERstats\">\n";
echo "Tempo Real Report &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; \n";
echo "<span style=\"position:absolute;left:160px;top:27px;z-index:19;\" id=campaign_select_list>\n";
echo "<TABLE WIDTH=250 CELLPADDING=0 CELLSPACING=0 BGCOLOR=\"#D9E6FE\"><TR><TD ALIGN=CENTER>\n";
echo "<a href=\"#\" onclick=\"openDiv('campaign_select_list');\">Escolher Opções do Relat.</a>";
echo "</TD></TR></TABLE>\n";
echo "</span>\n";
echo "<span style=\"position:absolute;left:10px;top:120px;z-index:18;\" id=agent_ingroup_display>\n";
echo " &nbsp; ";
echo "</span>\n";
echo "<a href=\"$PHP_SELF?RR=4000$groupQS&DB=$DB&adastats=$adastats&SIPmonitorLINK=$SIPmonitorLINK&IAXmonitorLINK=$IAXmonitorLINK&usergroup=$usergroup&UGdisplay=$UGdisplay&UidORname=$UidORname&orderby=$orderby&SERVdisplay=$SERVdisplay&CALLSdisplay=$CALLSdisplay&PHONEdisplay=$PHONEdisplay&CUSTPHONEdisplay=$CUSTPHONEdisplay&with_inbound=$with_inbound&monitor_active=$monitor_active&monitor_phone=$monitor_phone&ALLINGROUPstats=$ALLINGROUPstats&DROPINGROUPstats=$DROPINGROUPstats&NOLEADSalert=$NOLEADSalert&CARRIERstats=$CARRIERstats\">STOP</a> | ";
echo "<a href=\"$PHP_SELF?RR=40$groupQS&DB=$DB&adastats=$adastats&SIPmonitorLINK=$SIPmonitorLINK&IAXmonitorLINK=$IAXmonitorLINK&usergroup=$usergroup&UGdisplay=$UGdisplay&UidORname=$UidORname&orderby=$orderby&SERVdisplay=$SERVdisplay&CALLSdisplay=$CALLSdisplay&PHONEdisplay=$PHONEdisplay&CUSTPHONEdisplay=$CUSTPHONEdisplay&with_inbound=$with_inbound&monitor_active=$monitor_active&monitor_phone=$monitor_phone&ALLINGROUPstats=$ALLINGROUPstats&DROPINGROUPstats=$DROPINGROUPstats&NOLEADSalert=$NOLEADSalert&CARRIERstats=$CARRIERstats\">SLOW</a> | ";
echo "<a href=\"$PHP_SELF?RR=4$groupQS&DB=$DB&adastats=$adastats&SIPmonitorLINK=$SIPmonitorLINK&IAXmonitorLINK=$IAXmonitorLINK&usergroup=$usergroup&UGdisplay=$UGdisplay&UidORname=$UidORname&orderby=$orderby&SERVdisplay=$SERVdisplay&CALLSdisplay=$CALLSdisplay&PHONEdisplay=$PHONEdisplay&CUSTPHONEdisplay=$CUSTPHONEdisplay&with_inbound=$with_inbound&monitor_active=$monitor_active&monitor_phone=$monitor_phone&ALLINGROUPstats=$ALLINGROUPstats&DROPINGROUPstats=$DROPINGROUPstats&NOLEADSalert=$NOLEADSalert&CARRIERstats=$CARRIERstats\">GO</a>";
if (eregi('ALL-ACTIVE',$group_string))
	{
	echo " &nbsp; &nbsp; &nbsp; <a href=\"./admin.php?ADD=10\">ALTERAR</a> | \n";
	}
else
	{
	echo " &nbsp; &nbsp; &nbsp; <a href=\"./admin.php?ADD=34&campaign_id=$group\">ALTERAR</a> | \n";
	}
echo "<a href=\"./AST_timeonVDADallSUMMARY.php?RR=$RR&DB=$DB&adastats=$adastats\">SUMMARY</a> </FONT>\n";
echo "\n\n";


if (!$group) 
	{echo "<BR><BR>please select a campaign from the pulldown above</FORM>\n"; exit;}
else
{
$multi_drop=0;
### Gather list of all Closer group ids for exclusion from stats
$stmt = "select group_id from vicidial_inbound_groups;";
$rslt=mysql_query($stmt, $link);
$ingroups_to_print = mysql_num_rows($rslt);
while ($ingroups_to_print > $c)
	{
	$row=mysql_fetch_row($rslt);
	$ALLcloser_campaignsSQL .= "'$row[0]',";
	$c++;
	}
$ALLcloser_campaignsSQL = preg_replace("/,$/","",$ALLcloser_campaignsSQL);
if (strlen($ALLcloser_campaignsSQL)<2)
	{$ALLcloser_campaignsSQL="''";}
if ($DB > 0) {echo "\n|$ALLcloser_campaignsSQL|$stmt|\n";}


##### INBOUND #####
if ( ( ereg('Y',$with_inbound) or ereg('O',$with_inbound) ) and ($campaign_allow_inbound > 0) )
	{
	### Gather list of Closer group ids
	$stmt = "select closer_campaigns from vicidial_campaigns where active='Y' $group_SQLand;";
	$rslt=mysql_query($stmt, $link);
	$ccamps_to_print = mysql_num_rows($rslt);
	$c=0;
	while ($ccamps_to_print > $c)
		{
		$row=mysql_fetch_row($rslt);
		$closer_campaigns = $row[0];
		$closer_campaigns = preg_replace("/^ | -$/","",$closer_campaigns);
		$closer_campaigns = preg_replace("/ /","','",$closer_campaigns);
		$closer_campaignsSQL .= "'$closer_campaigns',";
		$c++;
		}
	$closer_campaignsSQL = preg_replace("/,$/","",$closer_campaignsSQL);
	}
else
	{
	$closer_campaignsSQL = "''";
	}	
if ($DB > 0) {echo "\n|$closer_campaigns|$closer_campaignsSQL|$stmt|\n";}


##### SHOW IN-GROUP STATS OR INBOUND ONLY WITH VIEW-MORE ###
if ( ($ALLINGROUPstats > 0) or ( (ereg('O',$with_inbound)) and ($adastats > 1) ) )
	{
	$stmtB="select calls_today,drops_today,answers_today,status_category_1,status_category_count_1,status_category_2,status_category_count_2,status_category_3,status_category_count_3,status_category_4,status_category_count_4,hold_sec_stat_one,hold_sec_stat_two,hold_sec_answer_calls,hold_sec_drop_calls,hold_sec_queue_calls,campaign_id from vicidial_campaign_stats where campaign_id IN ($closer_campaignsSQL) order by campaign_id;";

	if ($DB > 0) {echo "\n|$stmtB|\n";}

	$r=0;
	$rslt=mysql_query($stmtB, $link);
	$ingroups_to_print = mysql_num_rows($rslt);
	if ($ingroups_to_print > 0)
		{$ingroup_detail .= "<table cellpadding=0 cellspacing=0>";}
	while ($ingroups_to_print > $r)
		{
		$row=mysql_fetch_row($rslt);
		$callsTODAY =				$row[0];
		$dropsTODAY =				$row[1];
		$answersTODAY =				$row[2];
		$VSCcat1 =					$row[3];
		$VSCcat1tally =				$row[4];
		$VSCcat2 =					$row[5];
		$VSCcat2tally =				$row[6];
		$VSCcat3 =					$row[7];
		$VSCcat3tally =				$row[8];
		$VSCcat4 =					$row[9];
		$VSCcat4tally =				$row[10];
		$hold_sec_stat_one =		$row[11];
		$hold_sec_stat_two =		$row[12];
		$hold_sec_answer_calls =	$row[13];
		$hold_sec_drop_calls =		$row[14];
		$hold_sec_queue_calls =		$row[15];
		$ingroupdetail =			$row[16];
		if ( ($dropsTODAY > 0) and ($answersTODAY > 0) )
			{
			$drpctTODAY = ( ($dropsTODAY / $answersTODAY) * 100);
			$drpctTODAY = round($drpctTODAY, 2);
			$drpctTODAY = sprintf("%01.2f", $drpctTODAY);
			}
		else
			{$drpctTODAY=0;}

		if ($callsTODAY > 0)
			{
			$AVGhold_sec_queue_calls = ($hold_sec_queue_calls / $callsTODAY);
			$AVGhold_sec_queue_calls = round($AVGhold_sec_queue_calls, 0);
			}
		else
			{$AVGhold_sec_queue_calls=0;}

		if ($dropsTODAY > 0)
			{
			$AVGhold_sec_drop_calls = ($hold_sec_drop_calls / $dropsTODAY);
			$AVGhold_sec_drop_calls = round($AVGhold_sec_drop_calls, 0);
			}
		else
			{$AVGhold_sec_drop_calls=0;}

		if ($answersTODAY > 0)
			{
			$PCThold_sec_stat_one = ( ($hold_sec_stat_one / $answersTODAY) * 100);
			$PCThold_sec_stat_one = round($PCThold_sec_stat_one, 2);
			$PCThold_sec_stat_one = sprintf("%01.2f", $PCThold_sec_stat_one);
			$PCThold_sec_stat_two = ( ($hold_sec_stat_two / $answersTODAY) * 100);
			$PCThold_sec_stat_two = round($PCThold_sec_stat_two, 2);
			$PCThold_sec_stat_two = sprintf("%01.2f", $PCThold_sec_stat_two);
			$AVGhold_sec_answer_calls = ($hold_sec_answer_calls / $answersTODAY);
			$AVGhold_sec_answer_calls = round($AVGhold_sec_answer_calls, 0);
			if ($agent_non_pause_sec > 0)
				{
				$AVG_RESPOSTAagent_non_pause_sec = (($answersTODAY / $agent_non_pause_sec) * 60);
				$AVG_RESPOSTAagent_non_pause_sec = round($AVG_RESPOSTAagent_non_pause_sec, 2);
				$AVG_RESPOSTAagent_non_pause_sec = sprintf("%01.2f", $AVG_RESPOSTAagent_non_pause_sec);
				}
			else
				{$AVG_RESPOSTAagent_non_pause_sec=0;}
			}
		else
			{
			$PCThold_sec_stat_one=0;
			$PCThold_sec_stat_two=0;
			$AVGhold_sec_answer_calls=0;
			$AVG_RESPOSTAagent_non_pause_sec=0;
			}

		if (ereg("0$|2$|4$|6$|8$",$r)) {$bgcolor='#E6E6E6';}
		else {$bgcolor='white';}
		$ingroup_detail .= "<TR bgcolor=\"$bgcolor\">";
		$ingroup_detail .= "<TD ALIGN=RIGHT bgcolor=white><font size=2> &nbsp; &nbsp; &nbsp; &nbsp; </TD>";
		$ingroup_detail .= "<TD ALIGN=RIGHT><font size=2><B>$ingroupdetail &nbsp; </B></TD>";
		$ingroup_detail .= "<TD ALIGN=RIGHT><font size=2><B>CALLS TODAY:</B></TD><TD ALIGN=LEFT><font size=2>&nbsp; $callsTODAY&nbsp; &nbsp; </TD>";
		$ingroup_detail .= "<TD ALIGN=RIGHT><font size=2><B>TMA 1:</B></TD><TD ALIGN=LEFT><font size=2>&nbsp; $PCThold_sec_stat_one% &nbsp; &nbsp; </TD>";
		$ingroup_detail .= "<TD ALIGN=RIGHT><font size=2><B>Average Hold time for Answered Calls:</B></TD><TD ALIGN=LEFT><font size=2>&nbsp; $AVGhold_sec_answer_calls &nbsp; </TD>";
		$ingroup_detail .= "</TR>";
		$ingroup_detail .= "<TR bgcolor=\"$bgcolor\">";
		$ingroup_detail .= "<TD ALIGN=RIGHT bgcolor=white><font size=2></TD><TD ALIGN=LEFT><font size=2></TD>";
		$ingroup_detail .= "<TD ALIGN=RIGHT><font size=2><B>DROPS TODAY:</B></TD><TD ALIGN=LEFT><font size=2>&nbsp; $dropsTODAY&nbsp; &nbsp; </TD>";
		$ingroup_detail .= "<TD ALIGN=RIGHT><font size=2><B>TMA 2:</B></TD><TD ALIGN=LEFT><font size=2>&nbsp; $PCThold_sec_stat_two% &nbsp; &nbsp; </TD>";
		$ingroup_detail .= "<TD ALIGN=RIGHT><font size=2><B>Average Hold time for Dropped Calls:</B></TD><TD ALIGN=LEFT><font size=2>&nbsp; $AVGhold_sec_drop_calls &nbsp; </TD>";
		$ingroup_detail .= "</TR>";
		$ingroup_detail .= "<TR bgcolor=\"$bgcolor\">";
		$ingroup_detail .= "<TD ALIGN=RIGHT bgcolor=white><font size=2></TD><TD ALIGN=LEFT><font size=2></TD>";
		$ingroup_detail .= "<TD ALIGN=RIGHT><font size=2><B>RESPOSTAS TODAY:</B></TD><TD ALIGN=LEFT><font size=2>&nbsp; $answersTODAY&nbsp; &nbsp; </TD>";
		$ingroup_detail .= "<TD ALIGN=RIGHT><font size=2><B>DROP PERCENT:</B></TD><TD ALIGN=LEFT><font size=2>&nbsp; $drpctTODAY%&nbsp; &nbsp; </TD>";
		$ingroup_detail .= "<TD ALIGN=RIGHT><font size=2><B>Average Hold time for All Calls:</B></TD><TD ALIGN=LEFT><font size=2>&nbsp; $AVGhold_sec_queue_calls &nbsp; </TD>";
		$ingroup_detail .= "</TR>";

		$r++;
		}

	if ($ingroups_to_print > 0)
		{$ingroup_detail .= "</table>";}
	}


##### DROP IN-GROUP ONLY TOTALS ROW ###
$DROPINGROUPstatsHTML='';
if ( ($DROPINGROUPstats > 0) and (!preg_match("/ALL-ACTIVE/",$group_string)) )
	{
	$DIGcampaigns='';
	$stmtB="select drop_inbound_group from vicidial_campaigns where campaign_id IN($group_SQL) and drop_inbound_group NOT IN('---NONE---','');";
	if ($DB > 0) {echo "\n|$stmtB|\n";}
	$rslt=mysql_query($stmtB, $link);
	$dig_to_print = mysql_num_rows($rslt);
	$dtp=0;
	while ($dig_to_print > $dtp)
		{
		$row=mysql_fetch_row($rslt);
		$DIGcampaigns .=		"'$row[0]',";
		$dtp++;
		}
	$DIGcampaigns = preg_replace("/,$/",'',$DIGcampaigns);
	if (strlen($DIGcampaigns) < 2) {$DIGcampaigns = "''";}

	$stmtB="select sum(calls_today),sum(drops_today),sum(answers_today) from vicidial_campaign_stats where campaign_id IN($DIGcampaigns);";
	if ($DB > 0) {echo "\n|$stmtB|\n";}

	$rslt=mysql_query($stmtB, $link);
	$row=mysql_fetch_row($rslt);
	$callsTODAY =				$row[0];
	$dropsTODAY =				$row[1];
	$answersTODAY =				$row[2];
	if ( ($dropsTODAY > 0) and ($callsTODAY > 0) )
		{
		$drpctTODAY = ( ($dropsTODAY / $callsTODAY) * 100);
		$drpctTODAY = round($drpctTODAY, 2);
		$drpctTODAY = sprintf("%01.2f", $drpctTODAY);
		}
	else
		{$drpctTODAY=0;}

	$DROPINGROUPstatsHTML .= "<TR BGCOLOR=\"#E6E6E6\">";
	$DROPINGROUPstatsHTML .= "<TD ALIGN=RIGHT COLSPAN=2><font size=2><B>DROP IN-GROUP STATS -</B></TD>";
	$DROPINGROUPstatsHTML .= "<TD ALIGN=RIGHT><font size=2><B>DROP PERCENT:</B></TD><TD ALIGN=LEFT><font size=2>&nbsp; $drpctTODAY% &nbsp; &nbsp; </TD>";
	$DROPINGROUPstatsHTML .= "<TD ALIGN=RIGHT><font size=2><B>CALLS:</B></TD><TD ALIGN=LEFT><font size=2>&nbsp; $callsTODAY &nbsp; &nbsp; </TD>";
	$DROPINGROUPstatsHTML .= "<TD ALIGN=RIGHT><font size=2><B>DROPS/RESPOSTAS:</B></TD><TD ALIGN=LEFT><font size=2>&nbsp; $dropsTODAY / $answersTODAY &nbsp; &nbsp; </TD>";
	$DROPINGROUPstatsHTML .= "</TR>";
	}


##### CARRIER STATS TOTALS ###
$CARRIERstatsHTML='';
if ($CARRIERstats > 0)
	{
	$stmtB="select dialstatus,count(*) from vicidial_carrier_log where call_date >= \"$timeTWENTYFOURhoursAGO\" group by dialstatus;";
	if ($DB > 0) {echo "\n|$stmtB|\n";}
	$rslt=mysql_query($stmtB, $link);
	$car_to_print = mysql_num_rows($rslt);
	$ctp=0;
	while ($car_to_print > $ctp)
		{
		$row=mysql_fetch_row($rslt);
		$TFhour_status[$ctp] =	$row[0];
		$TFhour_count[$ctp] =	$row[1];
		$dialstatuses .=		"'$row[0]',";
		$ctp++;
		}
	$dialstatuses = preg_replace("/,$/",'',$dialstatuses);

	$CARRIERstatsHTML .= "<TR BGCOLOR=white><TD ALIGN=left COLSPAN=8>";
	$CARRIERstatsHTML .= "<TABLE CELLPADDING=1 CELLSPACING=1 Border=0 BGCOLOR=white>";
	$CARRIERstatsHTML .= "<TR BGCOLOR=\"#E6E6E6\">";
	$CARRIERstatsHTML .= "<TD ALIGN=LEFT><font size=2><B>CARRIER STATS: &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; </B></TD>";
	$CARRIERstatsHTML .= "<TD ALIGN=LEFT><font size=2><B>&nbsp; HANGUP STATUS &nbsp; </B></TD>";
	$CARRIERstatsHTML .= "<TD ALIGN=CENTER><font size=2><B>&nbsp; 24 HOURS &nbsp; </B></TD>";
	$CARRIERstatsHTML .= "<TD ALIGN=CENTER><font size=2><B>&nbsp; 6 HOURS &nbsp; </B></TD>";
	$CARRIERstatsHTML .= "<TD ALIGN=CENTER><font size=2><B>&nbsp; 1 HOUR &nbsp; </B></TD>";
	$CARRIERstatsHTML .= "<TD ALIGN=CENTER><font size=2><B>&nbsp; 15 MIN &nbsp; </B></TD>";
	$CARRIERstatsHTML .= "<TD ALIGN=CENTER><font size=2><B>&nbsp; 5 MIN &nbsp; </B></TD>";
	$CARRIERstatsHTML .= "<TD ALIGN=CENTER><font size=2><B>&nbsp; 1 MIN &nbsp; </B></TD>";
	$CARRIERstatsHTML .= "</TR>";

	if (strlen($dialstatuses) > 1)
		{
		$stmtB="select dialstatus,count(*) from vicidial_carrier_log where call_date >= \"$timeSIXhoursAGO\" group by dialstatus;";
		if ($DB > 0) {echo "\n|$stmtB|\n";}
		$rslt=mysql_query($stmtB, $link);
		$scar_to_print = mysql_num_rows($rslt);
		$print_sctp=0;
		while ($scar_to_print > $print_sctp)
			{
			$row=mysql_fetch_row($rslt);
			$print_ctp=0;
			while ($print_ctp < $ctp)
				{
				if ($TFhour_status[$print_ctp] == $row[0])
					{$SIXhour_count[$print_ctp] = $row[1];}
				$print_ctp++;
				}
			$print_sctp++;
			}

		$stmtB="select dialstatus,count(*) from vicidial_carrier_log where call_date >= \"$timeONEhourAGO\" group by dialstatus;";
		if ($DB > 0) {echo "\n|$stmtB|\n";}
		$rslt=mysql_query($stmtB, $link);
		$scar_to_print = mysql_num_rows($rslt);
		$print_sctp=0;
		while ($scar_to_print > $print_sctp)
			{
			$row=mysql_fetch_row($rslt);
			$print_ctp=0;
			while ($print_ctp < $ctp)
				{
				if ($TFhour_status[$print_ctp] == $row[0])
					{$ONEhour_count[$print_ctp] = $row[1];}
				$print_ctp++;
				}
			$print_sctp++;
			}

		$stmtB="select dialstatus,count(*) from vicidial_carrier_log where call_date >= \"$timeFIFTEENminutesAGO\" group by dialstatus;";
		if ($DB > 0) {echo "\n|$stmtB|\n";}
		$rslt=mysql_query($stmtB, $link);
		$scar_to_print = mysql_num_rows($rslt);
		$print_sctp=0;
		while ($scar_to_print > $print_sctp)
			{
			$row=mysql_fetch_row($rslt);
			$print_ctp=0;
			while ($print_ctp < $ctp)
				{
				if ($TFhour_status[$print_ctp] == $row[0])
					{$FTminute_count[$print_ctp] = $row[1];}
				$print_ctp++;
				}
			$print_sctp++;
			}

		$stmtB="select dialstatus,count(*) from vicidial_carrier_log where call_date >= \"$timeFIVEminutesAGO\" group by dialstatus;";
		if ($DB > 0) {echo "\n|$stmtB|\n";}
		$rslt=mysql_query($stmtB, $link);
		$scar_to_print = mysql_num_rows($rslt);
		$print_sctp=0;
		while ($scar_to_print > $print_sctp)
			{
			$row=mysql_fetch_row($rslt);
			$print_ctp=0;
			while ($print_ctp < $ctp)
				{
				if ($TFhour_status[$print_ctp] == $row[0])
					{$FIVEminute_count[$print_ctp] = $row[1];}
				$print_ctp++;
				}
			$print_sctp++;
			}

		$stmtB="select dialstatus,count(*) from vicidial_carrier_log where call_date >= \"$timeONEminuteAGO\" group by dialstatus;";
		if ($DB > 0) {echo "\n|$stmtB|\n";}
		$rslt=mysql_query($stmtB, $link);
		$scar_to_print = mysql_num_rows($rslt);
		$print_sctp=0;
		while ($scar_to_print > $print_sctp)
			{
			$row=mysql_fetch_row($rslt);
			$print_ctp=0;
			while ($print_ctp < $ctp)
				{
				if ($TFhour_status[$print_ctp] == $row[0])
					{$ONEminute_count[$print_ctp] = $row[1];}
				$print_ctp++;
				}
			$print_sctp++;
			}


		$print_ctp=0;
		while ($print_ctp < $ctp)
			{
			if (strlen($TFhour_count[$print_ctp])<1) {$TFhour_count[$print_ctp]=0;}
			if (strlen($SIXhour_count[$print_ctp])<1) {$SIXhour_count[$print_ctp]=0;}
			if (strlen($ONEhour_count[$print_ctp])<1) {$ONEhour_count[$print_ctp]=0;}
			if (strlen($FTminute_count[$print_ctp])<1) {$FTminute_count[$print_ctp]=0;}
			if (strlen($FIVEminute_count[$print_ctp])<1) {$FIVEminute_count[$print_ctp]=0;}
			if (strlen($ONEminute_count[$print_ctp])<1) {$ONEminute_count[$print_ctp]=0;}

			$CARRIERstatsHTML .= "<TR>";
			$CARRIERstatsHTML .= "<TD BGCOLOR=white><font size=2>&nbsp;</TD>";
			$CARRIERstatsHTML .= "<TD BGCOLOR=\"#E6E6E6\" ALIGN=LEFT><font size=2>&nbsp; &nbsp; $TFhour_status[$print_ctp]</TD>";
			$CARRIERstatsHTML .= "<TD BGCOLOR=\"#E6E6E6\" ALIGN=CENTER><font size=2> $TFhour_count[$print_ctp] </TD>";
			$CARRIERstatsHTML .= "<TD BGCOLOR=\"#E6E6E6\" ALIGN=CENTER><font size=2> $SIXhour_count[$print_ctp] </TD>";
			$CARRIERstatsHTML .= "<TD BGCOLOR=\"#E6E6E6\" ALIGN=CENTER><font size=2> $ONEhour_count[$print_ctp] </TD>";
			$CARRIERstatsHTML .= "<TD BGCOLOR=\"#E6E6E6\" ALIGN=CENTER><font size=2> $FTminute_count[$print_ctp] </TD>";
			$CARRIERstatsHTML .= "<TD BGCOLOR=\"#E6E6E6\" ALIGN=CENTER><font size=2> $FIVEminute_count[$print_ctp] </TD>";
			$CARRIERstatsHTML .= "<TD BGCOLOR=\"#E6E6E6\" ALIGN=CENTER><font size=2> $ONEminute_count[$print_ctp] </TD>";
			$CARRIERstatsHTML .= "</TR>";
			$print_ctp++;
			}
		}
	else
		{
		$CARRIERstatsHTML .= "<TR><TD BGCOLOR=white colspan=7><font size=2>no log entries</TD></TR>";
		}
	$CARRIERstatsHTML .= "</TABLE>";
	$CARRIERstatsHTML .= "</TD></TR>";
	}

#	http://svn.eflo.net:40080/vicidial/AST_timeonVDADall.php?&groups[]=ALL-ACTIVE&RR=4000&DB=0&adastats=&SIPmonitorLINK=&IAXmonitorLINK=&usergroup=&UGdisplay=1&UidORname=1&orderby=timeup&SERVdisplay=0&CALLSdisplay=1&PHONEdisplay=0&CUSTPHONEdisplay=0&with_inbound=Y&monitor_active=&monitor_phone=350a&ALLINGROUPstats=1&DROPINGROUPstats=0&NOLEADSalert=&CARRIERstats=1

##### INBOUND ONLY ###
if (ereg('O',$with_inbound))
	{
	$multi_drop++;

	$stmt="select agent_pause_codes_active from vicidial_campaigns $group_SQLwhere;";

	$stmtB="select sum(calls_today),sum(drops_today),sum(answers_today),max(status_category_1),sum(status_category_count_1),max(status_category_2),sum(status_category_count_2),max(status_category_3),sum(status_category_count_3),max(status_category_4),sum(status_category_count_4),sum(hold_sec_stat_one),sum(hold_sec_stat_two),sum(hold_sec_answer_calls),sum(hold_sec_drop_calls),sum(hold_sec_queue_calls) from vicidial_campaign_stats where campaign_id IN ($closer_campaignsSQL);";

	if (eregi('ALL-ACTIVE',$group_string))
		{
		$inboundSQL = "where campaign_id IN ($ALLcloser_campaignsSQL)";
		$stmtB="select sum(calls_today),sum(drops_today),sum(answers_today),max(status_category_1),sum(status_category_count_1),max(status_category_2),sum(status_category_count_2),max(status_category_3),sum(status_category_count_3),max(status_category_4),sum(status_category_count_4),sum(hold_sec_stat_one),sum(hold_sec_stat_two),sum(hold_sec_answer_calls),sum(hold_sec_drop_calls),sum(hold_sec_queue_calls) from vicidial_campaign_stats $inboundSQL;";
		}

	$stmtC="select agent_non_pause_sec from vicidial_campaign_stats $group_SQLwhere;";


	if ($DB > 0) {echo "\n|$stmt|$stmtB|$stmtC|\n";}

	$rslt=mysql_query($stmt, $link);
	$row=mysql_fetch_row($rslt);
	$agent_pause_codes_active = $row[0];

	$rslt=mysql_query($stmtC, $link);
	$row=mysql_fetch_row($rslt);
	$agent_non_pause_sec = $row[0];

	$rslt=mysql_query($stmtB, $link);
	$row=mysql_fetch_row($rslt);
	$callsTODAY =				$row[0];
	$dropsTODAY =				$row[1];
	$answersTODAY =				$row[2];
	$VSCcat1 =					$row[3];
	$VSCcat1tally =				$row[4];
	$VSCcat2 =					$row[5];
	$VSCcat2tally =				$row[6];
	$VSCcat3 =					$row[7];
	$VSCcat3tally =				$row[8];
	$VSCcat4 =					$row[9];
	$VSCcat4tally =				$row[10];
	$hold_sec_stat_one =		$row[11];
	$hold_sec_stat_two =		$row[12];
	$hold_sec_answer_calls =	$row[13];
	$hold_sec_drop_calls =		$row[14];
	$hold_sec_queue_calls =		$row[15];
	if ( ($dropsTODAY > 0) and ($answersTODAY > 0) )
		{
		$drpctTODAY = ( ($dropsTODAY / $answersTODAY) * 100);
		$drpctTODAY = round($drpctTODAY, 2);
		$drpctTODAY = sprintf("%01.2f", $drpctTODAY);
		}
	else
		{$drpctTODAY=0;}

	if ($callsTODAY > 0)
		{
		$AVGhold_sec_queue_calls = ($hold_sec_queue_calls / $callsTODAY);
		$AVGhold_sec_queue_calls = round($AVGhold_sec_queue_calls, 0);
		}
	else
		{$AVGhold_sec_queue_calls=0;}

	if ($dropsTODAY > 0)
		{
		$AVGhold_sec_drop_calls = ($hold_sec_drop_calls / $dropsTODAY);
		$AVGhold_sec_drop_calls = round($AVGhold_sec_drop_calls, 0);
		}
	else
		{$AVGhold_sec_drop_calls=0;}

	if ($answersTODAY > 0)
		{
		$PCThold_sec_stat_one = ( ($hold_sec_stat_one / $answersTODAY) * 100);
		$PCThold_sec_stat_one = round($PCThold_sec_stat_one, 2);
		$PCThold_sec_stat_one = sprintf("%01.2f", $PCThold_sec_stat_one);
		$PCThold_sec_stat_two = ( ($hold_sec_stat_two / $answersTODAY) * 100);
		$PCThold_sec_stat_two = round($PCThold_sec_stat_two, 2);
		$PCThold_sec_stat_two = sprintf("%01.2f", $PCThold_sec_stat_two);
		$AVGhold_sec_answer_calls = ($hold_sec_answer_calls / $answersTODAY);
		$AVGhold_sec_answer_calls = round($AVGhold_sec_answer_calls, 0);
		if ($agent_non_pause_sec > 0)
			{
			$AVG_RESPOSTAagent_non_pause_sec = (($answersTODAY / $agent_non_pause_sec) * 60);
			$AVG_RESPOSTAagent_non_pause_sec = round($AVG_RESPOSTAagent_non_pause_sec, 2);
			$AVG_RESPOSTAagent_non_pause_sec = sprintf("%01.2f", $AVG_RESPOSTAagent_non_pause_sec);
			}
		else
			{$AVG_RESPOSTAagent_non_pause_sec=0;}
		}
	else
		{
		$PCThold_sec_stat_one=0;
		$PCThold_sec_stat_two=0;
		$AVGhold_sec_answer_calls=0;
		$AVG_RESPOSTAagent_non_pause_sec=0;
		}

	echo "<BR><table cellpadding=0 cellspacing=0><TR>";
	echo "<TD ALIGN=RIGHT><font size=2><B>CALLS TODAY:</B></TD><TD ALIGN=LEFT><font size=2>&nbsp; $callsTODAY&nbsp; &nbsp; </TD>";
	echo "<TD ALIGN=RIGHT><font size=2><B>TMA 1:</B></TD><TD ALIGN=LEFT><font size=2>&nbsp; $PCThold_sec_stat_one% &nbsp; &nbsp; </TD>";
	echo "<TD ALIGN=RIGHT><font size=2><B>Average Hold time for Answered Calls:</B></TD><TD ALIGN=LEFT><font size=2>&nbsp; $AVGhold_sec_answer_calls &nbsp; </TD>";
	echo "<TD ALIGN=RIGHT><font size=2><B> TIME:</B> &nbsp; </TD><TD ALIGN=LEFT><font size=2> $NOW_TIME </TD>";
	echo "";
	echo "</TR>";
	echo "<TR>";
	echo "<TD ALIGN=RIGHT><font size=2><B>DROPS TODAY:</B></TD><TD ALIGN=LEFT><font size=2>&nbsp; $dropsTODAY&nbsp; &nbsp; </TD>";
	echo "<TD ALIGN=RIGHT><font size=2><B>TMA 2:</B></TD><TD ALIGN=LEFT><font size=2>&nbsp; $PCThold_sec_stat_two% &nbsp; &nbsp; </TD>";
	echo "<TD ALIGN=RIGHT><font size=2><B>Average Hold time for Dropped Calls:</B></TD><TD ALIGN=LEFT><font size=2>&nbsp; $AVGhold_sec_drop_calls &nbsp; </TD>";
	echo "<TD ALIGN=RIGHT><font size=2> </TD><TD ALIGN=LEFT><font size=2> </TD>";
	echo "";
	echo "</TR>";
	echo "<TR>";
	echo "<TD ALIGN=RIGHT><font size=2><B>RESPOSTAS TODAY:</B></TD><TD ALIGN=LEFT><font size=2>&nbsp; $answersTODAY&nbsp; &nbsp; </TD>";
	echo "<TD ALIGN=RIGHT COLSPAN=2><font size=2><B>(Agent non-pause time / Answers)</B></TD>";
	echo "<TD ALIGN=RIGHT><font size=2><B>Average Hold time for All Calls:</B></TD><TD ALIGN=LEFT><font size=2>&nbsp; $AVGhold_sec_queue_calls &nbsp; </TD>";
	echo "<TD ALIGN=RIGHT><font size=2> </TD><TD ALIGN=LEFT><font size=2> </TD>";
	echo "";
	echo "</TR>";
	echo "<TR>";
	echo "<TD ALIGN=RIGHT><font size=2><B>DROP PERCENT:</B></TD><TD ALIGN=LEFT><font size=2>&nbsp; $drpctTODAY%&nbsp; &nbsp; </TD>";
	echo "<TD ALIGN=RIGHT><font size=2><B>PRODUCTIVITY:</B></TD><TD ALIGN=LEFT><font size=2>&nbsp; $AVG_RESPOSTAagent_non_pause_sec &nbsp; &nbsp; </TD>";
	echo "<TD ALIGN=RIGHT><font size=2></TD><TD ALIGN=LEFT><font size=2></TD>";
	echo "<TD ALIGN=RIGHT><font size=2></TD><TD ALIGN=LEFT><font size=2></TD>";
	echo "";
	echo "</TR>";
	}

##### NOT INBOUND ONLY ###
else
	{
	if (eregi('ALL-ACTIVE',$group_string))
		{
		$non_inboundSQL='';
		if (ereg('N',$with_inbound))
			{$non_inboundSQL = "where campaign_id NOT IN ($ALLcloser_campaignsSQL)";}
		$multi_drop++;
		$stmt="select avg(auto_dial_level),min(dial_status_a),min(dial_status_b),min(dial_status_c),min(dial_status_d),min(dial_status_e),min(lead_order),min(lead_filter_id),sum(hopper_level),min(dial_method),avg(adaptive_maximum_level),avg(adaptive_dropped_percentage),avg(adaptive_dl_diff_target),avg(adaptive_intensity),min(available_only_ratio_tally),min(adaptive_latest_server_time),min(local_call_time),avg(dial_timeout),min(dial_statuses),max(agent_pause_codes_active),max(list_order_mix) from vicidial_campaigns where active='Y';";

		$stmtB="select sum(dialable_leads),sum(calls_today),sum(drops_today),avg(drops_answers_today_pct),avg(differential_onemin),avg(agents_average_onemin),sum(balance_trunk_fill),sum(answers_today),max(status_category_1),sum(status_category_count_1),max(status_category_2),sum(status_category_count_2),max(status_category_3),sum(status_category_count_3),max(status_category_4),sum(status_category_count_4) from vicidial_campaign_stats $non_inboundSQL;";
		}
	else
		{
		if ($DB > 0) {echo "\n|$with_inbound|$campaign_allow_inbound|\n";}

		if ( (ereg('Y',$with_inbound)) and ($campaign_allow_inbound > 0) )
			{
			$multi_drop++;
			if ($DB) {echo "with_inbound|$with_inbound|$campaign_allow_inbound\n";}

			$stmt="select auto_dial_level,dial_status_a,dial_status_b,dial_status_c,dial_status_d,dial_status_e,lead_order,lead_filter_id,hopper_level,dial_method,adaptive_maximum_level,adaptive_dropped_percentage,adaptive_dl_diff_target,adaptive_intensity,available_only_ratio_tally,adaptive_latest_server_time,local_call_time,dial_timeout,dial_statuses,agent_pause_codes_active,list_order_mix from vicidial_campaigns where campaign_id IN ($group_SQL,$closer_campaignsSQL);";

			$stmtB="select sum(dialable_leads),sum(calls_today),sum(drops_today),avg(drops_answers_today_pct),avg(differential_onemin),avg(agents_average_onemin),sum(balance_trunk_fill),sum(answers_today),max(status_category_1),sum(status_category_count_1),max(status_category_2),sum(status_category_count_2),max(status_category_3),sum(status_category_count_3),max(status_category_4),sum(status_category_count_4) from vicidial_campaign_stats where campaign_id IN ($group_SQL,$closer_campaignsSQL);";
			}
		else
			{
			$stmt="select avg(auto_dial_level),max(dial_status_a),max(dial_status_b),max(dial_status_c),max(dial_status_d),max(dial_status_e),max(lead_order),max(lead_filter_id),max(hopper_level),max(dial_method),max(adaptive_maximum_level),avg(adaptive_dropped_percentage),avg(adaptive_dl_diff_target),avg(adaptive_intensity),max(available_only_ratio_tally),max(adaptive_latest_server_time),max(local_call_time),max(dial_timeout),max(dial_statuses),max(agent_pause_codes_active),max(list_order_mix) from vicidial_campaigns where campaign_id IN($group_SQL);";

			$stmtB="select sum(dialable_leads),sum(calls_today),sum(drops_today),avg(drops_answers_today_pct),avg(differential_onemin),avg(agents_average_onemin),sum(balance_trunk_fill),sum(answers_today),max(status_category_1),sum(status_category_count_1),max(status_category_2),sum(status_category_count_2),max(status_category_3),sum(status_category_count_3),max(status_category_4),sum(status_category_count_4) from vicidial_campaign_stats where campaign_id IN($group_SQL);";
			}
		}
	if ($DB > 0) {echo "\n|$stmt|$stmtB|\n";}

	$rslt=mysql_query($stmt, $link);
	$row=mysql_fetch_row($rslt);
	$DIALlev =		$row[0];
	$DIALstatusA =	$row[1];
	$DIALstatusB =	$row[2];
	$DIALstatusC =	$row[3];
	$DIALstatusD =	$row[4];
	$DIALstatusE =	$row[5];
	$DIALorder =	$row[6];
	$DIALfilter =	$row[7];
	$HOPlev =		$row[8];
	$DIALmethod =	$row[9];
	$maxDIALlev =	$row[10];
	$DROPmax =		$row[11];
	$targetDIFF =	$row[12];
	$ADAintense =	$row[13];
	$ADAavailonly =	$row[14];
	$TAPERtime =	$row[15];
	$CALLtime =		$row[16];
	$DIALtimeout =	$row[17];
	$DIALstatuses =	$row[18];
	$agent_pause_codes_active = $row[19];
	$DIALmix =		$row[20];


	$stmt="select count(*) from vicidial_hopper $group_SQLwhere;";
	$rslt=mysql_query($stmt, $link);
	$row=mysql_fetch_row($rslt);
	$VDhop = $row[0];

	$rslt=mysql_query($stmtB, $link);
	$row=mysql_fetch_row($rslt);
	$DAleads =		$row[0];
	$callsTODAY =	$row[1];
	$dropsTODAY =	$row[2];
	$drpctTODAY =	$row[3];
	$diffONEMIN =	$row[4];
	$agentsONEMIN = $row[5];
	$balanceFILL =	$row[6];
	$answersTODAY = $row[7];
	if ($multi_drop > 0)
		{
		if ( ($dropsTODAY > 0) and ($answersTODAY > 0) )
			{
			$drpctTODAY = ( ($dropsTODAY / $answersTODAY) * 100);
			$drpctTODAY = round($drpctTODAY, 2);
			$drpctTODAY = sprintf("%01.2f", $drpctTODAY);
			}
		else
			{$drpctTODAY=0;}
		}
	$VSCcat1 =		$row[8];
	$VSCcat1tally = $row[9];
	$VSCcat2 =		$row[10];
	$VSCcat2tally = $row[11];
	$VSCcat3 =		$row[12];
	$VSCcat3tally = $row[13];
	$VSCcat4 =		$row[14];
	$VSCcat4tally = $row[15];

	if ( ($diffONEMIN != 0) and ($agentsONEMIN > 0) )
		{
		$diffpctONEMIN = ( ($diffONEMIN / $agentsONEMIN) * 100);
		$diffpctONEMIN = sprintf("%01.2f", $diffpctONEMIN);
		}
	else {$diffpctONEMIN = '0.00';}

	$stmt="select sum(local_trunk_shortage) from vicidial_campaign_server_stats $group_SQLwhere;";
	$rslt=mysql_query($stmt, $link);
	$row=mysql_fetch_row($rslt);
	$balanceSHORT = $row[0];

	if (ereg('DISABLED',$DIALmix))
		{
		$DIALstatuses = (preg_replace("/ -$|^ /","",$DIALstatuses));
		$DIALstatuses = (ereg_replace(' ',', ',$DIALstatuses));
		}
	else
		{
		$stmt="select vcl_id from vicidial_campaigns_list_mix where status='ACTIVE' $groupSQLand limit 1;";
		$rslt=mysql_query($stmt, $link);
		$Lmix_to_print = mysql_num_rows($rslt);
		if ($Lmix_to_print > 0)
			{
			$row=mysql_fetch_row($rslt);
			$DIALstatuses = "Mesclagem de Lista: $row[0]";
			$DIALorder =	"Mesclagem de Lista: $row[0]";
			}
		}

	echo "<BR><table cellpadding=0 cellspacing=0><TR>";
	echo "<TD ALIGN=RIGHT><font size=2><B>DIAL NÍVEL:</B></TD><TD ALIGN=LEFT><font size=2>&nbsp; $DIALlev&nbsp; &nbsp; </TD>";
	echo "<TD ALIGN=RIGHT><font size=2><B>TRUNK SHORT/FILL:</B></TD><TD ALIGN=LEFT><font size=2>&nbsp; $balanceSHORT / $balanceFILL &nbsp; &nbsp; </TD>";
	echo "<TD ALIGN=RIGHT><font size=2><B>FILTER:</B></TD><TD ALIGN=LEFT><font size=2>&nbsp; $DIALfilter &nbsp; </TD>";
	echo "<TD ALIGN=RIGHT><font size=2><B> TIME:</B> &nbsp; </TD><TD ALIGN=LEFT><font size=2> $NOW_TIME </TD>";
	echo "";
	echo "</TR>";

	if ($adastats>1)
		{
		echo "<TR BGCOLOR=\"#CCCCCC\">";
		echo "<TD ALIGN=RIGHT><a href=\"$PHP_SELF?$groupQS&RR=4&DB=$DB&adastats=1\"><font size=1>- min </font></a><font size=2>&nbsp; <B>MAX NÍVEL:</B></TD><TD ALIGN=LEFT><font size=2>&nbsp; $maxDIALlev &nbsp; </TD>";
		echo "<TD ALIGN=RIGHT><font size=2><B>DROPPED MAX:</B></TD><TD ALIGN=LEFT><font size=2>&nbsp; $DROPmax% &nbsp; &nbsp;</TD>";
		echo "<TD ALIGN=RIGHT><font size=2><B>TARGET DIFF:</B></TD><TD ALIGN=LEFT><font size=2>&nbsp; $targetDIFF &nbsp; &nbsp; </TD>";
		echo "<TD ALIGN=RIGHT><font size=2><B>INTENSITY:</B></TD><TD ALIGN=LEFT><font size=2>&nbsp; $ADAintense &nbsp; &nbsp; </TD>";
		echo "</TR>";

		echo "<TR BGCOLOR=\"#CCCCCC\">";
		echo "<TD ALIGN=RIGHT><font size=2><B>DIAL TIMEOUT:</B></TD><TD ALIGN=LEFT><font size=2>&nbsp; $DIALtimeout &nbsp;</TD>";
		echo "<TD ALIGN=RIGHT><font size=2><B>TAPER TIME:</B></TD><TD ALIGN=LEFT><font size=2>&nbsp; $TAPERtime &nbsp;</TD>";
		echo "<TD ALIGN=RIGHT><font size=2><B>LOCAL TIME:</B></TD><TD ALIGN=LEFT><font size=2>&nbsp; $CALLtime &nbsp;</TD>";
		echo "<TD ALIGN=RIGHT><font size=2><B>AVAIL ONLY:</B></TD><TD ALIGN=LEFT><font size=2>&nbsp; $ADAavailonly &nbsp;</TD>";
		echo "</TR>";
		}

	echo "<TR>";
	echo "<TD ALIGN=RIGHT><font size=2><B>DIALABLE LEADS:</B></TD><TD ALIGN=LEFT><font size=2>&nbsp; $DAleads &nbsp; &nbsp; </TD>";
	echo "<TD ALIGN=RIGHT><font size=2><B>CALLS TODAY:</B></TD><TD ALIGN=LEFT><font size=2>&nbsp; $callsTODAY &nbsp; &nbsp; </TD>";
	echo "<TD ALIGN=RIGHT><font size=2><B>AVG AGENTS:</B></TD><TD ALIGN=LEFT><font size=2>&nbsp; $agentsONEMIN &nbsp; &nbsp; </TD>";
	echo "<TD ALIGN=RIGHT><font size=2><B>MÉTODO DE DISC.:</B></TD><TD ALIGN=LEFT><font size=2>&nbsp; $DIALmethod &nbsp; &nbsp; </TD>";
	echo "</TR>";

	echo "<TR>";
	echo "<TD ALIGN=RIGHT><font size=2><B>HOPPER NÍVEL:</B></TD><TD ALIGN=LEFT><font size=2>&nbsp; $HOPlev &nbsp; &nbsp; </TD>";
	echo "<TD ALIGN=RIGHT><font size=2><B>DROPPED / RESPOSTAED:</B></TD><TD ALIGN=LEFT><font size=2>&nbsp; $dropsTODAY / $answersTODAY &nbsp; </TD>";
	echo "<TD ALIGN=RIGHT><font size=2><B>DL DIFF:</B></TD><TD ALIGN=LEFT><font size=2>&nbsp; $diffONEMIN &nbsp; &nbsp; </TD>";
	echo "<TD ALIGN=RIGHT><font size=2><B>STATUS:</B></TD><TD ALIGN=LEFT><font size=2>&nbsp; $DIALstatuses &nbsp; &nbsp; </TD>";
	echo "</TR>";

	echo "<TR>";
	echo "<TD ALIGN=RIGHT><font size=2><B>LEADS IN HOPPER:</B></TD><TD ALIGN=LEFT><font size=2>&nbsp; $VDhop &nbsp; &nbsp; </TD>";
	echo "<TD ALIGN=RIGHT><font size=2><B>DROPPED PERCENT:</B></TD><TD ALIGN=LEFT><font size=2>&nbsp; ";
	if ($drpctTODAY >= $DROPmax)
		{echo "<font color=red><B>$drpctTODAY%</B></font>";}
	else
		{echo "$drpctTODAY%";}
	echo " &nbsp; &nbsp;</TD>";


	echo "<TD ALIGN=RIGHT><font size=2><B>DIFF:</B></TD><TD ALIGN=LEFT><font size=2>&nbsp; $diffpctONEMIN% &nbsp; &nbsp; </TD>";
	echo "<TD ALIGN=RIGHT><font size=2><B>ORDER:</B></TD><TD ALIGN=LEFT><font size=2>&nbsp; $DIALorder &nbsp; &nbsp; </TD>";
	echo "</TR>";

	echo "$DROPINGROUPstatsHTML\n";
	echo "$CARRIERstatsHTML\n";
	}

echo "<TR>";
echo "<TD ALIGN=LEFT COLSPAN=8>";
if ( (!eregi('NULL',$VSCcat1)) and (strlen($VSCcat1)>0) )
	{echo "<font size=2><B>$VSCcat1:</B> &nbsp; $VSCcat1tally &nbsp;  &nbsp;  &nbsp; \n";}
if ( (!eregi('NULL',$VSCcat2)) and (strlen($VSCcat2)>0) )
	{echo "<font size=2><B>$VSCcat2:</B> &nbsp; $VSCcat2tally &nbsp;  &nbsp;  &nbsp; \n";}
if ( (!eregi('NULL',$VSCcat3)) and (strlen($VSCcat3)>0) )
	{echo "<font size=2><B>$VSCcat3:</B> &nbsp; $VSCcat3tally &nbsp;  &nbsp;  &nbsp; \n";}
if ( (!eregi('NULL',$VSCcat4)) and (strlen($VSCcat4)>0) )
	{echo "<font size=2><B>$VSCcat4:</B> &nbsp; $VSCcat4tally &nbsp;  &nbsp;  &nbsp; \n";}
echo "</TD></TR>";
echo "<TR>";
echo "<TD ALIGN=LEFT COLSPAN=8>";

echo "$ingroup_detail";

if ($adastats<2)
	{
	echo "<a href=\"$PHP_SELF?$groupQS&RR=$RR&DB=$DB&adastats=2&SIPmonitorLINK=$SIPmonitorLINK&IAXmonitorLINK=$IAXmonitorLINK&usergroup=$usergroup&UGdisplay=$UGdisplay&UidORname=$UidORname&orderby=$orderby&SERVdisplay=$SERVdisplay&CALLSdisplay=$CALLSdisplay&PHONEdisplay=$PHONEdisplay&CUSTPHONEdisplay=$CUSTPHONEdisplay&with_inbound=$with_inbound&monitor_active=$monitor_active&monitor_phone=$monitor_phone&ALLINGROUPstats=$ALLINGROUPstats&DROPINGROUPstats=$DROPINGROUPstats&NOLEADSalert=$NOLEADSalert&CARRIERstats=$CARRIERstats\"><font size=1>+ VIEW MORE</font></a>";
	}
else
	{
	echo "<a href=\"$PHP_SELF?$groupQS&RR=$RR&DB=$DB&adastats=1&SIPmonitorLINK=$SIPmonitorLINK&IAXmonitorLINK=$IAXmonitorLINK&usergroup=$usergroup&UGdisplay=$UGdisplay&UidORname=$UidORname&orderby=$orderby&SERVdisplay=$SERVdisplay&CALLSdisplay=$CALLSdisplay&PHONEdisplay=$PHONEdisplay&CUSTPHONEdisplay=$CUSTPHONEdisplay&with_inbound=$with_inbound&monitor_active=$monitor_active&monitor_phone=$monitor_phone&ALLINGROUPstats=$ALLINGROUPstats&DROPINGROUPstats=$DROPINGROUPstats&NOLEADSalert=$NOLEADSalert&CARRIERstats=$CARRIERstats\"><font size=1>+ VIEW LESS</font></a>";
	}
if ($UGdisplay>0)
	{
	echo " &nbsp; &nbsp; &nbsp; <a href=\"$PHP_SELF?$groupQS&RR=$RR&DB=$DB&adastats=$adastats&SIPmonitorLINK=$SIPmonitorLINK&IAXmonitorLINK=$IAXmonitorLINK&usergroup=$usergroup&UGdisplay=0&UidORname=$UidORname&orderby=$orderby&SERVdisplay=$SERVdisplay&CALLSdisplay=$CALLSdisplay&PHONEdisplay=$PHONEdisplay&CUSTPHONEdisplay=$CUSTPHONEdisplay&with_inbound=$with_inbound&monitor_active=$monitor_active&monitor_phone=$monitor_phone&ALLINGROUPstats=$ALLINGROUPstats&DROPINGROUPstats=$DROPINGROUPstats&NOLEADSalert=$NOLEADSalert&CARRIERstats=$CARRIERstats\"><font size=1>ESCONDER GRUPO DE USUÁRIOS</font></a>";
	}
else
	{
	echo " &nbsp; &nbsp; &nbsp; <a href=\"$PHP_SELF?$groupQS&RR=$RR&DB=$DB&adastats=$adastats&SIPmonitorLINK=$SIPmonitorLINK&IAXmonitorLINK=$IAXmonitorLINK&usergroup=$usergroup&UGdisplay=1&UidORname=$UidORname&orderby=$orderby&SERVdisplay=$SERVdisplay&CALLSdisplay=$CALLSdisplay&PHONEdisplay=$PHONEdisplay&CUSTPHONEdisplay=$CUSTPHONEdisplay&with_inbound=$with_inbound&monitor_active=$monitor_active&monitor_phone=$monitor_phone&ALLINGROUPstats=$ALLINGROUPstats&DROPINGROUPstats=$DROPINGROUPstats&NOLEADSalert=$NOLEADSalert&CARRIERstats=$CARRIERstats\"><font size=1>VIEW GRUPO DE USUÁRIOS</font></a>";
	}
if ($SERVdisplay>0)
	{
	echo " &nbsp; &nbsp; &nbsp; <a href=\"$PHP_SELF?$groupQS&RR=$RR&DB=$DB&adastats=$adastats&SIPmonitorLINK=$SIPmonitorLINK&IAXmonitorLINK=$IAXmonitorLINK&usergroup=$usergroup&UGdisplay=$UGdisplay&UidORname=$UidORname&orderby=$orderby&SERVdisplay=0&CALLSdisplay=$CALLSdisplay&PHONEdisplay=$PHONEdisplay&CUSTPHONEdisplay=$CUSTPHONEdisplay&with_inbound=$with_inbound&monitor_active=$monitor_active&monitor_phone=$monitor_phone&ALLINGROUPstats=$ALLINGROUPstats&DROPINGROUPstats=$DROPINGROUPstats&NOLEADSalert=$NOLEADSalert&CARRIERstats=$CARRIERstats\"><font size=1>ESCONDER SERVIDOR INFO</font></a>";
	}
else
	{
	echo " &nbsp; &nbsp; &nbsp; <a href=\"$PHP_SELF?$groupQS&RR=$RR&DB=$DB&adastats=$adastats&SIPmonitorLINK=$SIPmonitorLINK&IAXmonitorLINK=$IAXmonitorLINK&usergroup=$usergroup&UGdisplay=$UGdisplay&UidORname=$UidORname&orderby=$orderby&SERVdisplay=1&CALLSdisplay=$CALLSdisplay&PHONEdisplay=$PHONEdisplay&CUSTPHONEdisplay=$CUSTPHONEdisplay&with_inbound=$with_inbound&monitor_active=$monitor_active&monitor_phone=$monitor_phone&ALLINGROUPstats=$ALLINGROUPstats&DROPINGROUPstats=$DROPINGROUPstats&NOLEADSalert=$NOLEADSalert&CARRIERstats=$CARRIERstats\"><font size=1>MOSTRAR SERVIDOR INFO</font></a>";
	}
if ($CALLSdisplay>0)
	{
	echo " &nbsp; &nbsp; &nbsp; <a href=\"$PHP_SELF?$groupQS&RR=$RR&DB=$DB&adastats=$adastats&SIPmonitorLINK=$SIPmonitorLINK&IAXmonitorLINK=$IAXmonitorLINK&usergroup=$usergroup&UGdisplay=$UGdisplay&UidORname=$UidORname&orderby=$orderby&SERVdisplay=$SERVdisplay&CALLSdisplay=0&PHONEdisplay=$PHONEdisplay&CUSTPHONEdisplay=$CUSTPHONEdisplay&with_inbound=$with_inbound&monitor_active=$monitor_active&monitor_phone=$monitor_phone&ALLINGROUPstats=$ALLINGROUPstats&DROPINGROUPstats=$DROPINGROUPstats&NOLEADSalert=$NOLEADSalert&CARRIERstats=$CARRIERstats\"><font size=1>ESCONDER WAITING CALLS</font></a>";
	}
else
	{
	echo " &nbsp; &nbsp; &nbsp; <a href=\"$PHP_SELF?$groupQS&RR=$RR&DB=$DB&adastats=$adastats&SIPmonitorLINK=$SIPmonitorLINK&IAXmonitorLINK=$IAXmonitorLINK&usergroup=$usergroup&UGdisplay=$UGdisplay&UidORname=$UidORname&orderby=$orderby&SERVdisplay=$SERVdisplay&CALLSdisplay=1&PHONEdisplay=$PHONEdisplay&CUSTPHONEdisplay=$CUSTPHONEdisplay&with_inbound=$with_inbound&monitor_active=$monitor_active&monitor_phone=$monitor_phone&ALLINGROUPstats=$ALLINGROUPstats&DROPINGROUPstats=$DROPINGROUPstats&NOLEADSalert=$NOLEADSalert&CARRIERstats=$CARRIERstats\"><font size=1>MOSTRAR WAITING CALLS</font></a>";
	}

if ($ALLINGROUPstats>0)
	{
	echo " &nbsp; &nbsp; &nbsp; <a href=\"$PHP_SELF?$groupQS&RR=$RR&DB=$DB&adastats=$adastats&SIPmonitorLINK=$SIPmonitorLINK&IAXmonitorLINK=$IAXmonitorLINK&usergroup=$usergroup&UGdisplay=$UGdisplay&UidORname=$UidORname&orderby=$orderby&SERVdisplay=$SERVdisplay&CALLSdisplay=$CALLSdisplay&PHONEdisplay=$PHONEdisplay&CUSTPHONEdisplay=$CUSTPHONEdisplay&with_inbound=$with_inbound&monitor_active=$monitor_active&monitor_phone=$monitor_phone&ALLINGROUPstats=0&DROPINGROUPstats=$DROPINGROUPstats&NOLEADSalert=$NOLEADSalert&CARRIERstats=$CARRIERstats\"><font size=1>ESCONDER IN-GROUP STATS</font></a>";
	}
else
	{
	echo " &nbsp; &nbsp; &nbsp; <a href=\"$PHP_SELF?$groupQS&RR=$RR&DB=$DB&adastats=$adastats&SIPmonitorLINK=$SIPmonitorLINK&IAXmonitorLINK=$IAXmonitorLINK&usergroup=$usergroup&UGdisplay=$UGdisplay&UidORname=$UidORname&orderby=$orderby&SERVdisplay=$SERVdisplay&CALLSdisplay=$CALLSdisplay&PHONEdisplay=$PHONEdisplay&CUSTPHONEdisplay=$CUSTPHONEdisplay&with_inbound=$with_inbound&monitor_active=$monitor_active&monitor_phone=$monitor_phone&ALLINGROUPstats=1&DROPINGROUPstats=$DROPINGROUPstats&NOLEADSalert=$NOLEADSalert&CARRIERstats=$CARRIERstats\"><font size=1>MOSTRAR IN-GROUP STATS</font></a>";
	}
if ($PHONEdisplay>0)
	{
	echo " &nbsp; &nbsp; &nbsp; <a href=\"$PHP_SELF?$groupQS&RR=$RR&DB=$DB&adastats=$adastats&SIPmonitorLINK=$SIPmonitorLINK&IAXmonitorLINK=$IAXmonitorLINK&usergroup=$usergroup&UGdisplay=$UGdisplay&UidORname=$UidORname&orderby=$orderby&SERVdisplay=$SERVdisplay&CALLSdisplay=$CALLSdisplay&PHONEdisplay=0&CUSTPHONEdisplay=$CUSTPHONEdisplay&with_inbound=$with_inbound&monitor_active=$monitor_active&monitor_phone=$monitor_phone&ALLINGROUPstats=$ALLINGROUPstats&DROPINGROUPstats=$DROPINGROUPstats&NOLEADSalert=$NOLEADSalert&CARRIERstats=$CARRIERstats\"><font size=1>ESCONDER PHONES</font></a>";
	}
else
	{
	echo " &nbsp; &nbsp; &nbsp; <a href=\"$PHP_SELF?$groupQS&RR=$RR&DB=$DB&adastats=$adastats&SIPmonitorLINK=$SIPmonitorLINK&IAXmonitorLINK=$IAXmonitorLINK&usergroup=$usergroup&UGdisplay=$UGdisplay&UidORname=$UidORname&orderby=$orderby&SERVdisplay=$SERVdisplay&CALLSdisplay=$CALLSdisplay&PHONEdisplay=1&CUSTPHONEdisplay=$CUSTPHONEdisplay&with_inbound=$with_inbound&monitor_active=$monitor_active&monitor_phone=$monitor_phone&ALLINGROUPstats=$ALLINGROUPstats&DROPINGROUPstats=$DROPINGROUPstats&NOLEADSalert=$NOLEADSalert&CARRIERstats=$CARRIERstats\"><font size=1>MOSTRAR PHONES</font></a>";
	}
if ($CUSTPHONEdisplay>0)
	{
	echo " &nbsp; &nbsp; &nbsp; <a href=\"$PHP_SELF?$groupQS&RR=$RR&DB=$DB&adastats=$adastats&SIPmonitorLINK=$SIPmonitorLINK&IAXmonitorLINK=$IAXmonitorLINK&usergroup=$usergroup&UGdisplay=$UGdisplay&UidORname=$UidORname&orderby=$orderby&SERVdisplay=$SERVdisplay&CALLSdisplay=$CALLSdisplay&PHONEdisplay=$PHONEdisplay&CUSTPHONEdisplay=0&with_inbound=$with_inbound&monitor_active=$monitor_active&monitor_phone=$monitor_phone&ALLINGROUPstats=$ALLINGROUPstats&DROPINGROUPstats=$DROPINGROUPstats&NOLEADSalert=$NOLEADSalert&CARRIERstats=$CARRIERstats\"><font size=1>ESCONDER CUSTPHONES</font></a>";
	}
else
	{
	echo " &nbsp; &nbsp; &nbsp; <a href=\"$PHP_SELF?$groupQS&RR=$RR&DB=$DB&adastats=$adastats&SIPmonitorLINK=$SIPmonitorLINK&IAXmonitorLINK=$IAXmonitorLINK&usergroup=$usergroup&UGdisplay=$UGdisplay&UidORname=$UidORname&orderby=$orderby&SERVdisplay=$SERVdisplay&CALLSdisplay=$CALLSdisplay&PHONEdisplay=$PHONEdisplay&CUSTPHONEdisplay=1&with_inbound=$with_inbound&monitor_active=$monitor_active&monitor_phone=$monitor_phone&ALLINGROUPstats=$ALLINGROUPstats&DROPINGROUPstats=$DROPINGROUPstats&NOLEADSalert=$NOLEADSalert&CARRIERstats=$CARRIERstats\"><font size=1>MOSTRAR CUSTPHONES</font></a>";
	}
echo "</TD>";
echo "</TR>";
echo "</TABLE>";

echo "</FORM>\n\n";

##### check for campaigns with no dialable leads if enabled #####
if ( ($with_inbound != 'O') and ($NOLEADSalert == 'YES') )
	{
	$NDLcampaigns='';
	$stmtB="select campaign_id from vicidial_campaign_stats where campaign_id IN($group_SQL) and dialable_leads < 1 order by campaign_id;";
	if ($DB > 0) {echo "\n|$stmt|$stmtB|\n";}
	$rslt=mysql_query($stmtB, $link);
	$campaigns_to_print = mysql_num_rows($rslt);
	$ctp=0;
	while ($campaigns_to_print > $ctp)
		{
		$row=mysql_fetch_row($rslt);
		$NDLcampaigns .=		" <a href=\"./admin.php?ADD=34&campaign_id=$row[0]\">$row[0]</a> &nbsp; ";
		$ctp++;
		if (preg_match("/0$|5$/",$ctp))
			{$NDLcampaigns .= "<BR>";}
		}
	if ($ctp > 0)
		{
		echo "<span style=\"position:absolute;left:0px;top:47px;z-index:15;\" id=no_dialable_leads_span>\n";
		echo "<TABLE WIDTH=700 CELLPADDING=0 CELLSPACING=0 BGCOLOR=\"#E9E6EE\"><TR><TD ALIGN=CENTER>\n";
		echo "<BR><BR><BR><BR><a href=\"#\" onclick=\"closeAlert('no_dialable_leads_span');\">Close Alert</a>";
		echo "<BR><BR><BR><BR><BR><b>Campanhas with no dialable leads:<BR><BR>$NDLcampaigns<b><BR>";
		echo "<BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR> &nbsp; ";
		echo "</TD></TR></TABLE>\n";
		echo "</span>\n";
		}
	}
}



###################################################################################
###### INBOUND/OUTBOUND CALLS
###################################################################################
if ($campaign_allow_inbound > 0)
	{
	if (eregi('ALL-ACTIVE',$group_string)) 
		{
		$stmt="select closer_campaigns from vicidial_campaigns $group_SQLwhere";
		$rslt=mysql_query($stmt, $link);
		$closer_campaigns="";
		while ($row=mysql_fetch_row($rslt)) 
			{
			$closer_campaigns.="$row[0]";
			}
		$closer_campaigns = preg_replace("/^ | -$/","",$closer_campaigns);
		$closer_campaigns = preg_replace("/ - /"," ",$closer_campaigns);
		$closer_campaigns = preg_replace("/ /","','",$closer_campaigns);
		$closer_campaignsSQL = "'$closer_campaigns'";
		}	
	$stmtB="from vicidial_auto_calls where status NOT IN('XFER') and ( (call_type='IN' and campaign_id IN($closer_campaignsSQL)) or (call_type IN('OUT','OUTBALANCE') $group_SQLand) ) order by queue_priority desc,campaign_id,call_time;";
	}
else
	{
	$stmtB="from vicidial_auto_calls where status NOT IN('XFER') $group_SQLand order by queue_priority desc,campaign_id,call_time;";
	}
if ($CALLSdisplay > 0)
	{
	$stmtA = "SELECT status,campaign_id,phone_number,server_ip,UNIX_TIMESTAMP(call_time),call_type,queue_priority,agent_only";
	}
else
	{
	$stmtA = "SELECT status";
	}


$k=0;
$agentonlycount=0;
$stmt = "$stmtA $stmtB";
$rslt=mysql_query($stmt, $link);
if ($DB) {echo "$stmt\n";}
$parked_to_print = mysql_num_rows($rslt);
	if ($parked_to_print > 0)
	{
	$i=0;
	$out_total=0;
	$out_ring=0;
	$out_live=0;
	$in_ivr=0;
	while ($i < $parked_to_print)
		{
		$row=mysql_fetch_row($rslt);

		if (eregi("LIVE",$row[0])) 
			{
			$out_live++;

			if ($CALLSdisplay > 0)
				{
				$CDstatus[$k] =			$row[0];
				$CDcampaign_id[$k] =	$row[1];
				$CDphone_number[$k] =	$row[2];
				$CDserver_ip[$k] =		$row[3];
				$CDcall_time[$k] =		$row[4];
				$CDcall_type[$k] =		$row[5];
				$CDqueue_priority[$k] =	$row[6];
				$CDagent_only[$k] =		$row[7];
				if (strlen($CDagent_only[$k]) > 0) {$agentonlycount++;}
				$k++;
				}
			}
		else
			{
			if (eregi("IVR",$row[0])) 
				{
				$in_ivr++;

				if ($CALLSdisplay > 0)
					{
					$CDstatus[$k] =			$row[0];
					$CDcampaign_id[$k] =	$row[1];
					$CDphone_number[$k] =	$row[2];
					$CDserver_ip[$k] =		$row[3];
					$CDcall_time[$k] =		$row[4];
					$CDcall_type[$k] =		$row[5];
					$CDqueue_priority[$k] =	$row[6];
					$CDagent_only[$k] =		$row[7];
					if (strlen($CDagent_only[$k]) > 0) {$agentonlycount++;}
					$k++;
					}
				}
			if (eregi("CLOSER",$row[0])) 
				{$nothing=1;}
			else 
				{$out_ring++;}
			}

		$out_total++;
		$i++;
		}

	##### MIDI alert audio file test #####
	#	$test_midi=1;
	#	if ($test_midi > 0)
	#		{
	#	#	echo "<bgsound src=\"../vicidial/up_down.mid\" loop=\"-1\">";
	#	#	echo "<embed src=\"../vicidial/up_down.mid\" loop=\"-1\">";
	#		echo "<object type=\"audio/x-midi\" data=\"../vicidial/up_down.mid\" width=200 height=20>";
	#		echo "  <param name=\"src\" value=\"../vicidial/up_down.mid\">";
	#		echo "  <param name=\"autoplay\" value=\"true\">";
	#		echo "  <param name=\"autoStart\" value=\"1\">";
	#		echo "  <param name=\"loop\" value=\"1\">";
	#		echo "	alt : <a href=\"../vicidial/up_down.mid\">test.mid</a>";
	#		echo "</object>";
	#		}

		if ($out_live > 0) {$F='<FONT class="r1">'; $FG='</FONT>';}
		if ($out_live > 4) {$F='<FONT class="r2">'; $FG='</FONT>';}
		if ($out_live > 9) {$F='<FONT class="r3">'; $FG='</FONT>';}
		if ($out_live > 14) {$F='<FONT class="r4">'; $FG='</FONT>';}

		if ($campaign_allow_inbound > 0)
			{echo "$NFB$out_total$NFE current active calls&nbsp; &nbsp; &nbsp; \n";}
		else
			{echo "$NFB$out_total$NFE calls being placed &nbsp; &nbsp; &nbsp; \n";}
		
		echo "$NFB$out_ring$NFE calls ringing &nbsp; &nbsp; &nbsp; &nbsp; \n";
		echo "$NFB$F &nbsp;$out_live $FG$NFE calls waiting for agents &nbsp; &nbsp; &nbsp; \n";
		echo "$NFB &nbsp;$in_ivr$NFE calls in IVR &nbsp; &nbsp; &nbsp; \n";
		}
	else
	{
	echo "SEM CHAMADAS EM ESPERA\n";
	}



###################################################################################
###### CALLS WAITING
###################################################################################
$agentonlyheader = '';
if ($agentonlycount > 0)
	{$agentonlyheader = 'AGENTONLY';}
$Cecho = '';
$Cecho .= "VICIDIAL: Calls Waiting                      $NOW_TIME\n";
$Cecho .= "+--------+----------------------+--------------+-----------------+---------+------------+----------+\n";
$Cecho .= "| STATUS | CAMPANHA             | PHONE NUMBER | SERVER_IP       | DIALTIME| CALL TYPE  | PRIORIDADE | $agentonlyheader\n";
$Cecho .= "+--------+----------------------+--------------+-----------------+---------+------------+----------+\n";

$p=0;
while($p<$k)
	{
	$Cstatus =			sprintf("%-6s", $CDstatus[$p]);
	$Ccampaign_id =		sprintf("%-20s", $CDcampaign_id[$p]);
	$Cphone_number =	sprintf("%-12s", $CDphone_number[$p]);
	$Cserver_ip =		sprintf("%-15s", $CDserver_ip[$p]);
	$Ccall_type =		sprintf("%-10s", $CDcall_type[$p]);
	$Cqueue_priority =	sprintf("%8s", $CDqueue_priority[$p]);
	$Cagent_only =		sprintf("%8s", $CDagent_only[$p]);

	$Ccall_time_S = ($STARTtime - $CDcall_time[$p]);
	$Ccall_time_MS =		sec_convert($Ccall_time_S,'M'); 
	$Ccall_time_MS =		sprintf("%7s", $Ccall_time_MS);

	$G = '';		$EG = '';
	if ($CDcall_type[$p] == 'IN')
		{
		$G="<SPAN class=\"csc$CDcampaign_id[$p]\"><B>"; $EG='</B></SPAN>';
		}
	if (strlen($CDagent_only[$p]) > 0)
		{$Gcalltypedisplay = "$G$Cagent_only$EG";}
	else
		{$Gcalltypedisplay = '';}

	$Cecho .= "| $G$Cstatus$EG | $G$Ccampaign_id$EG | $G$Cphone_number$EG | $G$Cserver_ip$EG | $G$Ccall_time_MS$EG | $G$Ccall_type$EG | $G$Cqueue_priority$EG | $Gcalltypedisplay \n";

	$p++;
	}
$Cecho .= "+--------+----------------------+--------------+-----------------+---------+------------+----------+\n";

if ($p<1)
	{$Cecho='';}

###################################################################################
###### AGENT TIME ON SYSTEM
###################################################################################

$agent_incall=0;
$agent_ready=0;
$agent_paused=0;
$agent_dead=0;
$agent_total=0;

$phoneord=$orderby;
$userord=$orderby;
$groupord=$orderby;
$timeord=$orderby;
$campaignord=$orderby;

if ($phoneord=='phoneup') {$phoneord='phonedown';}
  else {$phoneord='phoneup';}
if ($userord=='userup') {$userord='userdown';}
  else {$userord='userup';}
if ($groupord=='groupup') {$groupord='groupdown';}
  else {$groupord='groupup';}
if ($timeord=='timeup') {$timeord='timedown';}
  else {$timeord='timeup';}
if ($campaignord=='campaignup') {$campaignord='campaigndown';}
  else {$campaignord='campaignup';}

$Aecho = '';
$Aecho .= "VICIDIAL: Agentes Time On Calls Campanha: $group_string            $NOW_TIME\n";


$HDbegin =			"+";
$HTbegin =			"|";
$HDstation =		"----------------+";
$HTstation =		" STATION        |";
$HDphone =		"-------------+";
$HTphone =		" <a href=\"$PHP_SELF?$groupQS&RR=$RR&DB=$DB&adastats=$adastats&SIPmonitorLINK=$SIPmonitorLINK&IAXmonitorLINK=$IAXmonitorLINK&usergroup=$usergroup&UGdisplay=$UGdisplay&UidORname=$UidORname&orderby=$phoneord&SERVdisplay=$SERVdisplay&CALLSdisplay=$CALLSdisplay&PHONEdisplay=$PHONEdisplay&CUSTPHONEdisplay=$CUSTPHONEdisplay&with_inbound=$with_inbound&monitor_active=$monitor_active&monitor_phone=$monitor_phone&ALLINGROUPstats=$ALLINGROUPstats&DROPINGROUPstats=$DROPINGROUPstats&NOLEADSalert=$NOLEADSalert&CARRIERstats=$CARRIERstats\">PHONE</a>       |";
$HDuser =			"------------------------+";


$HTuser =			" <a href=\"$PHP_SELF?$groupQS&RR=$RR&DB=$DB&adastats=$adastats&SIPmonitorLINK=$SIPmonitorLINK&IAXmonitorLINK=$IAXmonitorLINK&usergroup=$usergroup&UGdisplay=$UGdisplay&UidORname=$UidORname&orderby=$userord&SERVdisplay=$SERVdisplay&CALLSdisplay=$CALLSdisplay&PHONEdisplay=$PHONEdisplay&CUSTPHONEdisplay=$CUSTPHONEdisplay&with_inbound=$with_inbound&monitor_active=$monitor_active&monitor_phone=$monitor_phone&ALLINGROUPstats=$ALLINGROUPstats&DROPINGROUPstats=$DROPINGROUPstats&NOLEADSalert=$NOLEADSalert&CARRIERstats=$CARRIERstats\">USER</a>  ";
if ($UidORname>0)
	{
	$HTuser .= "<a href=\"$PHP_SELF?$groupQS&RR=$RR&DB=$DB&adastats=$adastats&SIPmonitorLINK=$SIPmonitorLINK&IAXmonitorLINK=$IAXmonitorLINK&usergroup=$usergroup&UGdisplay=$UGdisplay&UidORname=0&orderby=$orderby&SERVdisplay=$SERVdisplay&CALLSdisplay=$CALLSdisplay&PHONEdisplay=$PHONEdisplay&CUSTPHONEdisplay=$CUSTPHONEdisplay&with_inbound=$with_inbound&monitor_active=$monitor_active&monitor_phone=$monitor_phone&ALLINGROUPstats=$ALLINGROUPstats&DROPINGROUPstats=$DROPINGROUPstats&NOLEADSalert=$NOLEADSalert&CARRIERstats=$CARRIERstats\">MOSTRAR ID</a>  ";
	}
else
	{
	$HTuser .= "<a href=\"$PHP_SELF?$groupQS&RR=$RR&DB=$DB&adastats=$adastats&SIPmonitorLINK=$SIPmonitorLINK&IAXmonitorLINK=$IAXmonitorLINK&usergroup=$usergroup&UGdisplay=$UGdisplay&UidORname=1&orderby=$orderby&SERVdisplay=$SERVdisplay&CALLSdisplay=$CALLSdisplay&PHONEdisplay=$PHONEdisplay&CUSTPHONEdisplay=$CUSTPHONEdisplay&with_inbound=$with_inbound&monitor_active=$monitor_active&monitor_phone=$monitor_phone&ALLINGROUPstats=$ALLINGROUPstats&DROPINGROUPstats=$DROPINGROUPstats&NOLEADSalert=$NOLEADSalert&CARRIERstats=$CARRIERstats\">MOSTRAR NAME</a>";
	}

$HTuser .= "   INFO |";


$HDusergroup =		"--------------+";
$HTusergroup =		" <a href=\"$PHP_SELF?$groupQS&RR=$RR&DB=$DB&adastats=$adastats&SIPmonitorLINK=$SIPmonitorLINK&IAXmonitorLINK=$IAXmonitorLINK&usergroup=$usergroup&UGdisplay=$UGdisplay&UidORname=$UidORname&orderby=$groupord&SERVdisplay=$SERVdisplay&CALLSdisplay=$CALLSdisplay&PHONEdisplay=$PHONEdisplay&CUSTPHONEdisplay=$CUSTPHONEdisplay&with_inbound=$with_inbound&monitor_active=$monitor_active&monitor_phone=$monitor_phone&ALLINGROUPstats=$ALLINGROUPstats&DROPINGROUPstats=$DROPINGROUPstats&NOLEADSalert=$NOLEADSalert&CARRIERstats=$CARRIERstats\">GRUPO DE USUÁRIOS</a>   |";
$HDsessionid =		"------------------+";
$HTsessionid =		" SESSIONID        |";
$HDbarge =			"-------+";
$HTbarge =			" BARGE |";
$HDstatus =			"----------+";
$HTstatus =			" STATUS   |";
$HDcustphone =		"-------------+";
$HTcustphone =		" CUST PHONE  |";
$HDserver_ip =		"-----------------+";
$HTserver_ip =		" SERVIDOR IP       |";
$HDcall_server_ip =	"-----------------+";
$HTcall_server_ip =	" CALL SERVIDOR IP  |";
$HDtime =			"---------+";
$HTtime =			" <a href=\"$PHP_SELF?$groupQS&RR=$RR&DB=$DB&adastats=$adastats&SIPmonitorLINK=$SIPmonitorLINK&IAXmonitorLINK=$IAXmonitorLINK&usergroup=$usergroup&UGdisplay=$UGdisplay&UidORname=$UidORname&orderby=$timeord&SERVdisplay=$SERVdisplay&CALLSdisplay=$CALLSdisplay&PHONEdisplay=$PHONEdisplay&CUSTPHONEdisplay=$CUSTPHONEdisplay&with_inbound=$with_inbound&monitor_active=$monitor_active&monitor_phone=$monitor_phone&ALLINGROUPstats=$ALLINGROUPstats&DROPINGROUPstats=$DROPINGROUPstats&NOLEADSalert=$NOLEADSalert&CARRIERstats=$CARRIERstats\">MM:SS</a>   |";
$HDcampaign =		"------------+";
$HTcampaign =		" <a href=\"$PHP_SELF?$groupQS&RR=$RR&DB=$DB&adastats=$adastats&SIPmonitorLINK=$SIPmonitorLINK&IAXmonitorLINK=$IAXmonitorLINK&usergroup=$usergroup&UGdisplay=$UGdisplay&UidORname=$UidORname&orderby=$campaignord&SERVdisplay=$SERVdisplay&CALLSdisplay=$CALLSdisplay&PHONEdisplay=$PHONEdisplay&CUSTPHONEdisplay=$CUSTPHONEdisplay&with_inbound=$with_inbound&monitor_active=$monitor_active&monitor_phone=$monitor_phone&ALLINGROUPstats=$ALLINGROUPstats&DROPINGROUPstats=$DROPINGROUPstats&NOLEADSalert=$NOLEADSalert&CARRIERstats=$CARRIERstats\">CAMPANHA</a>   |";
$HDcalls =			"-------+";
$HTcalls =			" CALLS |";
$HDpause =	'';
$HTpause =	'';
$HDigcall =			"------+------------------";
$HTigcall =			" HOLD | IN-GROUP ";

if (!ereg("N",$agent_pause_codes_active))
	{
	$HDstatus =			"----------";
	$HTstatus =			" STATUS   ";
	$HDpause =			"-------+";
	$HTpause =			" PAUSE |";
	}
if ($PHONEdisplay < 1)
	{
	$HDphone =	'';
	$HTphone =	'';
	}
if ($CUSTPHONEdisplay < 1)
	{
	$HDcustphone =	'';
	$HTcustphone =	'';
	}
if ($UGdisplay < 1)
	{
	$HDusergroup =	'';
	$HTusergroup =	'';
	}
if ( ($SIPmonitorLINK<1) and ($IAXmonitorLINK<1) and (!preg_match("/MONITOR|BARGE/",$monitor_active) ) ) 
	{
	$HDsessionid =	"-----------+";
	$HTsessionid =	" SESSIONID |";
	}
if ( ($SIPmonitorLINK<2) and ($IAXmonitorLINK<2) and (!preg_match("/BARGE/",$monitor_active) ) ) 
	{
	$HDbarge =		'';
	$HTbarge =		'';
	}
if ($SERVdisplay < 1)
	{
	$HDserver_ip =		'';
	$HTserver_ip =		'';
	$HDcall_server_ip =	'';
	$HTcall_server_ip =	'';
	}



$Aline  = "$HDbegin$HDstation$HDphone$HDuser$HDusergroup$HDsessionid$HDbarge$HDstatus$HDpause$HDcustphone$HDserver_ip$HDcall_server_ip$HDtime$HDcampaign$HDcalls$HDigcall\n";
$Bline  = "$HTbegin$HTstation$HTphone$HTuser$HTusergroup$HTsessionid$HTbarge$HTstatus$HTpause$HTcustphone$HTserver_ip$HTcall_server_ip$HTtime$HTcampaign$HTcalls$HTigcall\n";
$Aecho .= "$Aline";
$Aecho .= "$Bline";
$Aecho .= "$Aline";

if ($orderby=='timeup') {$orderSQL='vicidial_live_agents.status,last_call_time';}
if ($orderby=='timedown') {$orderSQL='vicidial_live_agents.status desc,last_call_time desc';}
if ($orderby=='campaignup') {$orderSQL='vicidial_live_agents.campaign_id,vicidial_live_agents.status,last_call_time';}
if ($orderby=='campaigndown') {$orderSQL='vicidial_live_agents.campaign_id desc,vicidial_live_agents.status desc,last_call_time desc';}
if ($orderby=='groupup') {$orderSQL='user_group,vicidial_live_agents.status,last_call_time';}
if ($orderby=='groupdown') {$orderSQL='user_group desc,vicidial_live_agents.status desc,last_call_time desc';}
if ($orderby=='phoneup') {$orderSQL='extension,server_ip';}
if ($orderby=='phonedown') {$orderSQL='extension desc,server_ip desc';}
if ($UidORname > 0)
	{
	if ($orderby=='userup') {$orderSQL='full_name,status,last_call_time';}
	if ($orderby=='userdown') {$orderSQL='full_name desc,status desc,last_call_time desc';}
	}
else
	{
	if ($orderby=='userup') {$orderSQL='vicidial_live_agents.user';}
	if ($orderby=='userdown') {$orderSQL='vicidial_live_agents.user desc';}
	}

if (eregi('ALL-ACTIVE',$group_string)) {$UgroupSQL = '';}
else {$UgroupSQL = " and vicidial_live_agents.campaign_id IN($group_SQL)";}
if (strlen($usergroup)<1) {$usergroupSQL = '';}
else {$usergroupSQL = " and user_group='" . mysql_real_escape_string($usergroup) . "'";}

$stmt="select extension,vicidial_live_agents.user,conf_exten,vicidial_live_agents.status,vicidial_live_agents.server_ip,UNIX_TIMESTAMP(last_call_time),UNIX_TIMESTAMP(last_call_finish),call_server_ip,vicidial_live_agents.campaign_id,vicidial_users.user_group,vicidial_users.full_name,vicidial_live_agents.comments,vicidial_live_agents.calls_today,vicidial_live_agents.callerid,lead_id,UNIX_TIMESTAMP(last_state_change) from vicidial_live_agents,vicidial_users where vicidial_live_agents.user=vicidial_users.user $UgroupSQL $usergroupSQL order by $orderSQL;";
$rslt=mysql_query($stmt, $link);
if ($DB) {echo "$stmt\n";}
$talking_to_print = mysql_num_rows($rslt);
	if ($talking_to_print > 0)
	{
	$i=0;
	while ($i < $talking_to_print)
		{
		$row=mysql_fetch_row($rslt);

		$Aextension[$i] =		$row[0];
		$Auser[$i] =			$row[1];
		$Asessionid[$i] =		$row[2];
		$Astatus[$i] =			$row[3];
		$Aserver_ip[$i] =		$row[4];
		$Acall_time[$i] =		$row[5];
		$Acall_finish[$i] =		$row[6];
		$Acall_server_ip[$i] =	$row[7];
		$Acampaign_id[$i] =		$row[8];
		$Auser_group[$i] =		$row[9];
		$Afull_name[$i] =		$row[10];
		$Acomments[$i] = 		$row[11];
		$Acalls_today[$i] =		$row[12];
		$Acallerid[$i] =		$row[13];
		$Alead_id[$i] =			$row[14];
		$Astate_change[$i] =	$row[15];

		### 3-WAY Check ###
		if ($Alead_id[$i]!=0) 
			{
			$threewaystmt="select UNIX_TIMESTAMP(last_call_time) from vicidial_live_agents where lead_id='$Alead_id[$i]' and status='INCALL' order by UNIX_TIMESTAMP(last_call_time) desc";
			$threewayrslt=mysql_query($threewaystmt, $link);
			if (mysql_num_rows($threewayrslt)>1) 
				{
				$Astatus[$i]="3-WAY";
				$srow=mysql_fetch_row($threewayrslt);
				$Acall_mostrecent[$i]=$srow[0];
				}
			}
		### END 3-WAY Check ###

		$i++;
		}

$callerids='';
$pausecode='';
$stmt="select callerid,lead_id,phone_number from vicidial_auto_calls;";
$rslt=mysql_query($stmt, $link);
if ($DB) {echo "$stmt\n";}
$calls_to_list = mysql_num_rows($rslt);
	if ($calls_to_list > 0)
	{
	$i=0;
	while ($i < $calls_to_list)
		{
		$row=mysql_fetch_row($rslt);
		$callerids .=	"$row[0]|";
		$VAClead_ids[$i] =	$row[1];
		$VACphones[$i] =	$row[2];
		$i++;
		}
	}

### Lookup phone logins
	$i=0;
	while ($i < $talking_to_print)
		{
		if (eregi("R/",$Aextension[$i])) 
			{
			$protocol = 'EXTERNAL';
			$dialplan = eregi_replace('R/',"",$Aextension[$i]);
			$dialplan = eregi_replace("\@.*",'',$dialplan);
			$exten = "dialplan_number='$dialplan'";
			}
		if (eregi("Local/",$Aextension[$i])) 
			{
			$protocol = 'EXTERNAL';
			$dialplan = eregi_replace('Local/',"",$Aextension[$i]);
			$dialplan = eregi_replace("\@.*",'',$dialplan);
			$exten = "dialplan_number='$dialplan'";
			}
		if (eregi('SIP/',$Aextension[$i])) 
			{
			$protocol = 'SIP';
			$dialplan = eregi_replace('SIP/',"",$Aextension[$i]);
			$dialplan = eregi_replace("-.*",'',$dialplan);
			$exten = "extension='$dialplan'";
			}
		if (eregi('IAX2/',$Aextension[$i])) 
			{
			$protocol = 'IAX2';
			$dialplan = eregi_replace('IAX2/',"",$Aextension[$i]);
			$dialplan = eregi_replace("-.*",'',$dialplan);
			$exten = "extension='$dialplan'";
			}
		if (eregi('Zap/',$Aextension[$i])) 
			{
			$protocol = 'Zap';
			$dialplan = eregi_replace('Zap/',"",$Aextension[$i]);
			$exten = "extension='$dialplan'";
			}
		if (eregi('DAHDI/',$Aextension[$i])) 
			{
			$protocol = 'Zap';
			$dialplan = eregi_replace('DAHDI/',"",$Aextension[$i]);
			$exten = "extension='$dialplan'";
			}

		$stmt="select login from phones where server_ip='$Aserver_ip[$i]' and $exten and protocol='$protocol';";
		$rslt=mysql_query($stmt, $link);
		if ($DB) {echo "$stmt\n";}
		$phones_to_print = mysql_num_rows($rslt);
		if ($phones_to_print > 0)
			{
			$row=mysql_fetch_row($rslt);
			$Alogin[$i] = "$row[0]-----$i";
			}
		else
			{
			$Alogin[$i] = "$Aextension[$i]-----$i";
			}
		$i++;
		}

### Sort by phone if selected
	if ($orderby=='phoneup')
		{
		sort($Alogin);
		}
	if ($orderby=='phonedown')
		{
		rsort($Alogin);
		}

### Run through the loop to display agents
	$j=0;
	$agentcount=0;
	while ($j < $talking_to_print)
		{
		$n=0;
		$custphone='';
		while ($n < $calls_to_list)
			{
			if ( (ereg("$VAClead_ids[$n]", $Alead_id[$j])) and (strlen($VAClead_ids[$n]) == strlen($Alead_id[$j])) )
				{$custphone = $VACphones[$n];}
			$n++;
			}

		$phone_split = explode("-----",$Alogin[$j]);
		$i = $phone_split[1];

		if (eregi("READY|PAUSED",$Astatus[$i]))
			{
			$Acall_time[$i]=$Astate_change[$i];

			if ($Alead_id[$i] > 0)
				{
				$Astatus[$i] =	'DISPO';
				$Lstatus =		'DISPO';
				$status =		' DISPO';
				}
			}
		if ($non_latin < 1)
			{
			$extension = eregi_replace('Local/',"",$Aextension[$i]);
			$extension =		sprintf("%-14s", $extension);
			while(strlen($extension)>14) {$extension = substr("$extension", 0, -1);}
			}
		else
			{
			$extension = eregi_replace('Local/',"",$Aextension[$i]);
			$extension =		sprintf("%-48s", $extension);
			while(mb_strlen($extension, 'utf-8')>14) {$extension = mb_substr("$extension", 0, -1,'utf8');}
			}

		$phone =			sprintf("%-12s", $phone_split[0]);
		$custphone =		sprintf("%-11s", $custphone);
		$Luser =			$Auser[$i];
		$user =				sprintf("%-20s", $Auser[$i]);
		$Lsessionid =		$Asessionid[$i];
		$sessionid =		sprintf("%-9s", $Asessionid[$i]);
		$Lstatus =			$Astatus[$i];
		$status =			sprintf("%-6s", $Astatus[$i]);
		$Lserver_ip =		$Aserver_ip[$i];
		$server_ip =		sprintf("%-15s", $Aserver_ip[$i]);
		$call_server_ip =	sprintf("%-15s", $Acall_server_ip[$i]);
		$campaign_id =	sprintf("%-10s", $Acampaign_id[$i]);
		$comments=		$Acomments[$i];
		$calls_today =	sprintf("%-5s", $Acalls_today[$i]);

		if (!ereg("N",$agent_pause_codes_active))
			{$pausecode='       ';}
		else
			{$pausecode='';}

		if (eregi("INCALL",$Lstatus)) 
			{
			$stmtP="select count(*) from parked_channels where channel_group='$Acallerid[$i]';";
			$rsltP=mysql_query($stmtP,$link);
			$rowP=mysql_fetch_row($rsltP);
			$parked_channel = $rowP[0];

			if ($parked_channel > 0)
				{
				$Astatus[$i] =	'PARK';
				$Lstatus =		'PARK';
				$status =		' PARK ';
				}
			else
				{
				if (!ereg("$Acallerid[$i]\|",$callerids))
					{
					$Acall_time[$i]=$Astate_change[$i];

					$Astatus[$i] =	'DEAD';
					$Lstatus =		'DEAD';
					$status =		' DEAD ';
					}
				}

			if ( (eregi("AUTO",$comments)) or (strlen($comments)<1) )
				{$CM='A';}
			else
				{
				if (eregi("ENTRANTE",$comments)) 
					{$CM='I';}
				else
					{$CM='M';}
				}
			}
		else {$CM=' ';}

		if ($UGdisplay > 0)
			{
			if ($non_latin < 1)
				{
				$user_group =		sprintf("%-12s", $Auser_group[$i]);
				while(strlen($user_group)>12) {$user_group = substr("$user_group", 0, -1);}
				}
			else
				{
				$user_group =		sprintf("%-40s", $Auser_group[$i]);
				while(mb_strlen($user_group, 'utf-8')>12) {$user_group = mb_substr("$user_group", 0, -1,'utf8');}
				}
			}
		if ($UidORname > 0)
			{
			if ($non_latin < 1)
				{
				$user =		sprintf("%-20s", $Afull_name[$i]);
				while(strlen($user)>20) {$user = substr("$user", 0, -1);}
				}
			else
				{
				$user =		sprintf("%-60s", $Afull_name[$i]);
				while(mb_strlen($user, 'utf-8')>20) {$user = mb_substr("$user", 0, -1,'utf8');}
				}
			}
		if (!eregi("INCALL|QUEUE|PARK|3-WAY",$Astatus[$i]))
			{$call_time_S = ($STARTtime - $Astate_change[$i]);}
		else if (eregi("3-WAY",$Astatus[$i]))
			{$call_time_S = ($STARTtime - $Acall_mostrecent[$i]);}
		else
			{$call_time_S = ($STARTtime - $Acall_time[$i]);}

		$call_time_MS =		sec_convert($call_time_S,'M'); 
		$call_time_MS =		sprintf("%7s", $call_time_MS);
		$G = '';		$EG = '';
		if ( ($Lstatus=='INCALL') or ($Lstatus=='PARK') )
			{
			if ($call_time_S >= 10) {$G='<SPAN class="thistle"><B>'; $EG='</B></SPAN>';}
			if ($call_time_S >= 60) {$G='<SPAN class="violet"><B>'; $EG='</B></SPAN>';}
			if ($call_time_S >= 300) {$G='<SPAN class="purple"><B>'; $EG='</B></SPAN>';}
	#		if ($call_time_S >= 600) {$G='<SPAN class="purple"><B>'; $EG='</B></SPAN>';}
			}
		if ($Lstatus=='3-WAY')
			{
			if ($call_time_S >= 10) {$G='<SPAN class="lime"><B>'; $EG='</B></SPAN>';}
			}
		if ($Lstatus=='DEAD')
			{
			if ($call_time_S >= 21600) 
				{$j++; continue;} 
			else
				{
				$agent_dead++;  $agent_total++;
				$G=''; $EG='';
				if ($call_time_S >= 10) {$G='<SPAN class="black"><B>'; $EG='</B></SPAN>';}
				}
			}
		if ($Lstatus=='DISPO')
			{
			if ($call_time_S >= 21600) 
				{$j++; continue;} 
			else
				{
				$agent_paused++;  $agent_total++;
				$G=''; $EG='';
				if ($call_time_S >= 10) {$G='<SPAN class="khaki"><B>'; $EG='</B></SPAN>';}
				if ($call_time_S >= 60) {$G='<SPAN class="yellow"><B>'; $EG='</B></SPAN>';}
				if ($call_time_S >= 300) {$G='<SPAN class="olive"><B>'; $EG='</B></SPAN>';}
				}
			}
		if ($Lstatus=='PAUSED') 
			{
			if (!ereg("N",$agent_pause_codes_active))
				{
				$stmtC="select sub_status from vicidial_agent_log where user='$Luser' order by agent_log_id desc limit 1;";
				$rsltC=mysql_query($stmtC,$link);
				$rowC=mysql_fetch_row($rsltC);
				$pausecode = sprintf("%-6s", $rowC[0]);
				$pausecode = "$pausecode ";
				}
			else
				{$pausecode='';}

			if ($call_time_S >= 21600) 
				{$j++; continue;} 
			else
				{
				$agent_paused++;  $agent_total++;
				$G=''; $EG='';
				if ($call_time_S >= 10) {$G='<SPAN class="khaki"><B>'; $EG='</B></SPAN>';}
				if ($call_time_S >= 60) {$G='<SPAN class="yellow"><B>'; $EG='</B></SPAN>';}
				if ($call_time_S >= 300) {$G='<SPAN class="olive"><B>'; $EG='</B></SPAN>';}
				}
			}
#		if ( (strlen($Acall_server_ip[$i])> 4) and ($Acall_server_ip[$i] != "$Aserver_ip[$i]") )
#				{$G='<SPAN class="orange"><B>'; $EG='</B></SPAN>';}

		if ( (eregi("INCALL",$status)) or (eregi("QUEUE",$status))  or (eregi("3-WAY",$status)) or (eregi("PARK",$status))) {$agent_incall++;  $agent_total++;}
		if ( (eregi("READY",$status)) or (eregi("CLOSER",$status)) ) {$agent_ready++;  $agent_total++;}
		if ( (eregi("READY",$status)) or (eregi("CLOSER",$status)) ) 
			{
			$G='<SPAN class="lightblue"><B>'; $EG='</B></SPAN>';
			if ($call_time_S >= 60) {$G='<SPAN class="blue"><B>'; $EG='</B></SPAN>';}
			if ($call_time_S >= 300) {$G='<SPAN class="midnightblue"><B>'; $EG='</B></SPAN>';}
			}

		$L='';
		$R='';
		if ($SIPmonitorLINK>0) {$L=" <a href=\"sip:0$Lsessionid@$server_ip\">LISTEN</a>";   $R='';}
		if ($IAXmonitorLINK>0) {$L=" <a href=\"iax:0$Lsessionid@$server_ip\">LISTEN</a>";   $R='';}
		if ($SIPmonitorLINK>1) {$R=" | <a href=\"sip:$Lsessionid@$server_ip\">BARGE</a>";}
		if ($IAXmonitorLINK>1) {$R=" | <a href=\"iax:$Lsessionid@$server_ip\">BARGE</a>";}
		if ( (strlen($monitor_phone)>1) and (preg_match("/MONITOR|BARGE/",$monitor_active) ) )
			{$L=" <a href=\"javascript:send_monitor('$Lsessionid','$Lserver_ip','MONITOR');\">LISTEN</a>";   $R='';}
		if ( (strlen($monitor_phone)>1) and (preg_match("/BARGE/",$monitor_active) ) )
			{$R=" | <a href=\"javascript:send_monitor('$Lsessionid','$Lserver_ip','BARGE');\">BARGE</a>";}

		if ($CUSTPHONEdisplay > 0)	{$CP = " $G$custphone$EG |";}
		else	{$CP = "";}

		if ($UGdisplay > 0)	{$UGD = " $G$user_group$EG |";}
		else	{$UGD = "";}

		if ($SERVdisplay > 0)	{$SVD = "$G$server_ip$EG | $G$call_server_ip$EG | ";}
		else	{$SVD = "";}

		if ($PHONEdisplay > 0)	{$phoneD = "$G$phone$EG | ";}
		else	{$phoneD = " ";}

		$vac_stage='';
		$vac_campaign='';
		$INGRP='';
		if ($CM == 'I') 
			{
			$stmt="select vac.campaign_id,vac.stage,vig.group_name from vicidial_auto_calls vac,vicidial_inbound_groups vig where vac.callerid='$Acallerid[$i]' and vac.campaign_id=vig.group_id LIMIT 1;";
			$rslt=mysql_query($stmt, $link);
			if ($DB) {echo "$stmt\n";}
			$ingrp_to_print = mysql_num_rows($rslt);
				if ($ingrp_to_print > 0)
				{
				$row=mysql_fetch_row($rslt);
				$vac_campaign =	sprintf("%-20s", "$row[0] - $row[2]");
				$row[1] = eregi_replace(".*-",'',$row[1]);
				$vac_stage =	sprintf("%-4s", $row[1]);
				}

			$INGRP = " $G$vac_stage$EG | $G$vac_campaign$EG ";
			}

		$agentcount++;

		$Aecho .= "| $G$extension$EG |$phoneD<a href=\"./user_status.php?user=$Luser\" target=\"_blank\">$G$user$EG</a> <a href=\"javascript:ingroup_info('$Luser','$j');\">+</a> |$UGD $G$sessionid$EG$L$R | $G$status$EG $CM $pausecode| $CP$SVD$G$call_time_MS$EG | $G$campaign_id$EG | $G$calls_today$EG |$INGRP\n";

		$j++;
		}

		$Aecho .= "$Aline";
		$Aecho .= "  $agentcount agents logged in on all servers\n";
		$Aecho .= "  Carga do Sistema Average: $load_ave\n\n";

	#	$Aecho .= "  <SPAN class=\"orange\"><B>          </SPAN> - Balanced call</B>\n";
		$Aecho .= "  <SPAN class=\"lightblue\"><B>          </SPAN> - Agent waiting for call</B>\n";
		$Aecho .= "  <SPAN class=\"blue\"><B>          </SPAN> - Agent waiting for call > 1 minute</B>\n";
		$Aecho .= "  <SPAN class=\"midnightblue\"><B>          </SPAN> - Agent waiting for call > 5 minutes</B>\n";
		$Aecho .= "  <SPAN class=\"thistle\"><B>          </SPAN> - Agent on call > 10 seconds</B>\n";
		$Aecho .= "  <SPAN class=\"violet\"><B>          </SPAN> - Agent on call > 1 minute</B>\n";
		$Aecho .= "  <SPAN class=\"purple\"><B>          </SPAN> - Agent on call > 5 minutes</B>\n";
		$Aecho .= "  <SPAN class=\"khaki\"><B>          </SPAN> - Agent Paused > 10 seconds</B>\n";
		$Aecho .= "  <SPAN class=\"yellow\"><B>          </SPAN> - Agent Paused > 1 minute</B>\n";
		$Aecho .= "  <SPAN class=\"olive\"><B>          </SPAN> - Agent Paused > 5 minutes</B>\n";
		$Aecho .= "  <SPAN class=\"lime\"><B>          </SPAN> - Agent in 3-WAY > 10 seconds</B>\n";
		$Aecho .= "  <SPAN class=\"black\"><B>          </SPAN> - Agent on a dead call</B>\n";

		if ($agent_ready > 0) {$B='<FONT class="b1">'; $BG='</FONT>';}
		if ($agent_ready > 4) {$B='<FONT class="b2">'; $BG='</FONT>';}
		if ($agent_ready > 9) {$B='<FONT class="b3">'; $BG='</FONT>';}
		if ($agent_ready > 14) {$B='<FONT class="b4">'; $BG='</FONT>';}


		echo "\n<BR>\n";

		echo "$NFB$agent_total$NFE agents logged in &nbsp; &nbsp; &nbsp; &nbsp; \n";
		echo "$NFB$agent_incall$NFE agents in calls &nbsp; &nbsp; &nbsp; \n";
		echo "$NFB$B &nbsp;$agent_ready $BG$NFE agents waiting &nbsp; &nbsp; &nbsp; \n";
		echo "$NFB$agent_paused$NFE paused agents &nbsp; &nbsp; &nbsp; \n";
		echo "$NFB$agent_dead$NFE agentes em chamadas perdidas&nbsp; &nbsp; &nbsp; \n";
		
		echo "<PRE><FONT SIZE=2>";
		echo "";
		echo "$Cecho";
		echo "$Aecho";
	}
	else
	{
	echo "AGENTES SEM CHAMADAS\n";
	echo "<PRE>$Cecho";
	}

?>
</PRE>
</TD></TR></TABLE>

</BODY></HTML>
