<?php 
# AST_VICIDIAL_hopperlist.php
# 
# Copyright (C) 2009  Matt Florell <vicidial@gmail.com>    LICENSE: AGPLv2
#
# CHANGES
#
# 60619-1654 - Added variable filtering to eliminate SQL injection attack threat
#            - Added required user/pass to gain access to this page
# 70115-1614 - Added ALT field for vicidial_hopper alt_dial column
# 71029-0852 - Added list_id to the output
# 71030-2118 - Added priority to display
# 90508-0644 - Changed to PHP long tags
# 91023-1540 - Changed to only show hopper status of READY
#

require("dbconnect.php");

$PHP_AUTH_USER=$_SERVER['PHP_AUTH_USER'];
$PHP_AUTH_PW=$_SERVER['PHP_AUTH_PW'];
$PHP_SELF=$_SERVER['PHP_SELF'];
if (isset($_GET["group"]))				{$group=$_GET["group"];}
	elseif (isset($_POST["group"]))		{$group=$_POST["group"];}
if (isset($_GET["submit"]))				{$submit=$_GET["submit"];}
	elseif (isset($_POST["submit"]))		{$submit=$_POST["submit"];}
if (isset($_GET["ENVIAR"]))				{$ENVIAR=$_GET["ENVIAR"];}
	elseif (isset($_POST["ENVIAR"]))		{$ENVIAR=$_POST["ENVIAR"];}

$PHP_AUTH_USER = ereg_replace("[^0-9a-zA-Z]","",$PHP_AUTH_USER);
$PHP_AUTH_PW = ereg_replace("[^0-9a-zA-Z]","",$PHP_AUTH_PW);

$stmt="SELECT count(*) from vicidial_users where user='$PHP_AUTH_USER' and pass='$PHP_AUTH_PW' and user_level > 6 and view_reports='1' and modify_campaigns='1';";
if ($DB) {echo "|$stmt|\n";}
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
if (!isset($server_ip)) {$server_ip = '10.10.10.15';}

$stmt="select campaign_id,campaign_name from vicidial_campaigns order by campaign_id;";
$rslt=mysql_query($stmt, $link);
if ($DB) {echo "$stmt\n";}
$campaigns_to_print = mysql_num_rows($rslt);
$i=0;
while ($i < $campaigns_to_print)
	{
	$row=mysql_fetch_row($rslt);
	$campaign_id[$i] =$row[0];
	$campaign_name[$i] =$row[1];
	$i++;
	}
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
echo "<TITLE>Lista De Hopper Report</TITLE></HEAD><BODY BGCOLOR=WHITE>\n";
echo "<FORM ACTION=\"$PHP_SELF\" METHOD=GET>\n";
#echo "<INPUT TYPE=HIDDEN NAME=server_ip VALUE=\"$server_ip\">\n";
#echo "<INPUT TYPE=TEXT NAME=query_date SIZE=10 MAXLENGTH=10 VALUE=\"$query_date\">\n";
echo "<SELECT SIZE=1 NAME=group>\n";
$o=0;
while ($campaigns_to_print > $o)
	{
	if ($campaign_id[$o] == $group) {echo "<option selected value=\"$campaign_id[$o]\">$campaign_id[$o] - $campaign_name[$o]</option>\n";}
	else {echo "<option value=\"$campaign_id[$o]\">$campaign_id[$o] - $campaign_name[$o]</option>\n";}
	$o++;
	}
echo "</SELECT>\n";
echo "<INPUT TYPE=Submit NAME=ENVIAR VALUE=ENVIAR>\n";
echo " &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; <a href=\"./admin.php?ADD=34&campaign_id=$group\">ALTERAR</a> \n";
echo "</FORM>\n\n";

echo "<PRE><FONT SIZE=2>\n\n";


if (!$group)
	{
	echo "\n\n";
	echo "POR FAVOR SELECIONE UMA CAMPANHA ACIMA E CLIQUE EM ENVIAR\n";
	}

else
	{
	echo "Lista Online do Hopper                      $NOW_TIME\n";

	echo "\n";
	echo "---------- TOTALS\n";

	$stmt="select count(*) from vicidial_hopper where campaign_id='" . mysql_real_escape_string($group) . "';";
	$rslt=mysql_query($stmt, $link);
	if ($DB) {echo "$stmt\n";}
	$row=mysql_fetch_row($rslt);

	$TOTALcalls =	sprintf("%10s", $row[0]);

	echo "Total de registros no hopper agora:       $TOTALcalls\n";


	##############################
	#########  LEAD STATS

	echo "\n";
	echo "---------- LEADS IN HOPPER\n";
	echo "+------+--------+-----------+------------+------------+-------+--------+-------+--------+-------+\n";
	echo "|ORDER |PRIORIDADE| LEAD ID   | ID DA LISTA    | PHONE NUM  | STATE | STATUS | COUNT | GMT    | ALT   |\n";
	echo "+------+--------+-----------+------------+------------+-------+--------+-------+--------+-------+\n";

	$stmt="select vicidial_hopper.lead_id,phone_number,vicidial_hopper.state,vicidial_list.status,called_count,vicidial_hopper.gmt_offset_now,hopper_id,alt_dial,vicidial_hopper.list_id,vicidial_hopper.priority from vicidial_hopper,vicidial_list where vicidial_hopper.campaign_id='" . mysql_real_escape_string($group) . "' and vicidial_hopper.status='READY' and vicidial_hopper.lead_id=vicidial_list.lead_id order by priority desc,hopper_id limit 5000;";
	$rslt=mysql_query($stmt, $link);
	if ($DB) {echo "$stmt\n";}
	$users_to_print = mysql_num_rows($rslt);
	$i=0;
	while ($i < $users_to_print)
		{
		$row=mysql_fetch_row($rslt);

		$FMT_i =		sprintf("%-4s", $i);
		$lead_id =		sprintf("%-9s", $row[0]);
		$phone_number =	sprintf("%-10s", $row[1]);
		$state =		sprintf("%-5s", $row[2]);
		$status =		sprintf("%-6s", $row[3]);
		$count =		sprintf("%-5s", $row[4]);
		$gmt =			sprintf("%-6s", $row[5]);
		$hopper_id =	sprintf("%-6s", $row[6]);
		$alt_dial =		sprintf("%-5s", $row[7]);
		$list_id =		sprintf("%-10s", $row[8]);
		$priority =		sprintf("%-6s", $row[9]);

		if ($DB) {echo "| $FMT_i | $priority | $lead_id | $list_id | $phone_number | $state | $status | $count | $gmt | $hopper_id |\n";}
		else {echo "| $FMT_i | $priority | $lead_id | $list_id | $phone_number | $state | $status | $count | $gmt | $alt_dial |\n";}

		$i++;
		}

	echo "+------+--------+-----------+------------+------------+-------+--------+-------+--------+-------+\n";

	}


?>
</PRE>

</BODY></HTML>
