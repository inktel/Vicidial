<?php
# send_CID_call.php
#
# Send calls with custom callerID numbers from web form
# 
# Copyright (C) 2009  Matt Florell <vicidial@gmail.com>    LICENSE: GPLv2
#
# CHANGES
#
# 90714-1355 - First Build
#

require("dbconnect.php");

$PHP_AUTH_USER=$_SERVER['PHP_AUTH_USER'];
$PHP_AUTH_PW=$_SERVER['PHP_AUTH_PW'];
$PHP_SELF=$_SERVER['PHP_SELF'];
$ip = getenv("REMOTE_ADDR");

if (isset($_GET["DB"]))						{$DB=$_GET["DB"];}
	elseif (isset($_POST["DB"]))			{$DB=$_POST["DB"];}
if (isset($_GET["sender"]))					{$sender=$_GET["sender"];}
	elseif (isset($_POST["sender"]))		{$sender=$_POST["sender"];}
if (isset($_GET["receiver"]))				{$receiver=$_GET["receiver"];}
	elseif (isset($_POST["receiver"]))		{$receiver=$_POST["receiver"];}
if (isset($_GET["cid_number"]))				{$cid_number=$_GET["cid_number"];}
	elseif (isset($_POST["cid_number"]))	{$cid_number=$_POST["cid_number"];}
if (isset($_GET["server_ip"]))				{$server_ip=$_GET["server_ip"];}
	elseif (isset($_POST["server_ip"]))		{$server_ip=$_POST["server_ip"];}
if (isset($_GET["SUBMIT"]))					{$SUBMIT=$_GET["SUBMIT"];}
	elseif (isset($_POST["SUBMIT"]))		{$SUBMIT=$_POST["SUBMIT"];}

$PHP_AUTH_USER = ereg_replace("[^-_0-9a-zA-Z]","",$PHP_AUTH_USER);
$PHP_AUTH_PW = ereg_replace("[^-_0-9a-zA-Z]","",$PHP_AUTH_PW);

$sender = ereg_replace("[^0-9]","",$sender);
$receiver = ereg_replace("[^0-9]","",$receiver);
$cid_number = ereg_replace("[^0-9]","",$cid_number);
$server_ip = ereg_replace("[^\.0-9]","",$server_ip);

$stmt="SELECT count(*) from vicidial_users where user='$PHP_AUTH_USER' and pass='$PHP_AUTH_PW' and user_level > 6 and view_reports='1';";
if ($DB) {echo "|$stmt|\n";}
$rslt=mysql_query($stmt, $link);
$row=mysql_fetch_row($rslt);
$auth=$row[0];

  if( (strlen($PHP_AUTH_USER)<2) or (strlen($PHP_AUTH_PW)<2) or (!$auth))
	{
    Header("WWW-Authenticate: Basic realm=\"VICI-PROJECTS\"");
    Header("HTTP/1.0 401 Unauthorized");
    echo "Invalid Username/Password: |$PHP_AUTH_USER|$PHP_AUTH_PW|\n";
    exit;
	}

$NOW_DATE = date("Y-m-d");
$NOW_TIME = date("Y-m-d H:i:s");
$STARTtime = date("U");


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

<? 
$stmt="select server_ip,server_id from servers;";
$rslt=mysql_query($stmt, $link);
if ($DB) {echo "$stmt\n";}
$servers_to_print = mysql_num_rows($rslt);
$i=0;
while ($i < $servers_to_print)
	{
	$row=mysql_fetch_row($rslt);
	$groups[$i] =		$row[0];
	$group_names[$i] =	$row[1];
	$i++;
	}

echo "<META HTTP-EQUIV=\"Content-Type\" CONTENT=\"text/html; charset=utf-8\">\n";
echo "<TITLE>VICIDIAL: Manual CID call</TITLE></HEAD><BODY BGCOLOR=WHITE>\n";
echo "<FORM ACTION=\"$PHP_SELF\" METHOD=GET>\n";
echo "Sender: <INPUT TYPE=TEXT NAME=sender SIZE=12 MAXLENGTH=10 VALUE=\"$sender\"> &nbsp; &nbsp; \n";
echo "Receiver: <INPUT TYPE=TEXT NAME=receiver SIZE=12 MAXLENGTH=10 VALUE=\"$receiver\"> &nbsp; &nbsp; \n";
echo "CID Number: <INPUT TYPE=TEXT NAME=cid_number SIZE=12 MAXLENGTH=10 VALUE=\"$cid_number\"> &nbsp; &nbsp; \n";
echo "Server: <SELECT SIZE=1 NAME=server_ip>\n";
$o=0;
while ($servers_to_print > $o)
	{
	if ($groups[$o] == $server_ip) {echo "<option selected value=\"$groups[$o]\">$groups[$o] - $group_names[$o]</option>\n";}
	  else {echo "<option value=\"$groups[$o]\">$groups[$o] - $group_names[$o]</option>\n";}
	$o++;
	}
echo "</SELECT> &nbsp; \n";
echo "<INPUT TYPE=hidden NAME=DB VALUE=\"$DB\">\n";
echo "<INPUT TYPE=submit NAME=SUBMIT VALUE=SUBMIT>\n";
echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; <a href=\"./admin.php?ADD=999999\">REPORTS</a> </FONT>\n";
echo "</FORM><BR>\n\n";


if ( (strlen($sender) < 6) or (strlen($receiver) < 6) or (strlen($cid_number) < 6) or (strlen($server_ip) < 7) )
	{
	echo "\n\n";
	echo "PLEASE ENTER A CALLER, RECEIVER AND CALLERID NUMBER ABOVE AND CLICK SUBMIT\n";
	}

else
	{
	$stmt = "INSERT INTO user_call_log (user,call_date,call_type,server_ip,phone_number,number_dialed,lead_id,callerid,group_alias_id) values('$PHP_AUTH_USER','$NOW_TIME','CID','$server_ip','$sender','$receiver','0','$cid_number','$ip')";
	if ($DB) {echo "$stmt\n";}
	$rslt=mysql_query($stmt, $link);

	$Local_end = '@default';

	$stmt = "INSERT INTO vicidial_manager values('','','$NOW_TIME','NEW','N','$server_ip','','Originate','TESTCIDCALL098765432','Exten: 91$receiver','Context: default','Channel: Local/91$sender$Local_end','Priority: 1','Callerid: \"$cid_number\" <$cid_number>','','','','','');";
	if ($DB) {echo "$stmt\n";}
	$rslt=mysql_query($stmt, $link);

	echo "<B>Call sent from $sender to $receiver using CIDnumber: $cid_number</B>";
	}

?>

</BODY></HTML>

