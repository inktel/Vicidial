<?php
# zoiperweb_redirect.php - used for load balance forwarding with variables
# 
# Copyright (C) 2010  Matt Florell <vicidial@gmail.com>    LICENSE: AGPLv2
#
# CHANGELOG
# 100827-1419 - First Build 
#

if (isset($_GET["DB"]))							{$DB=$_GET["DB"];}
        elseif (isset($_POST["DB"]))			{$DB=$_POST["DB"];}
if (isset($_GET["phone_login"]))				{$phone_login=$_GET["phone_login"];}
        elseif (isset($_POST["phone_login"]))	{$phone_login=$_POST["phone_login"];}
if (isset($_GET["phone_pass"]))					{$phone_pass=$_GET["phone_pass"];}
        elseif (isset($_POST["phone_pass"]))    {$phone_pass=$_POST["phone_pass"];}
if (isset($_GET["server_ip"]))					{$server_ip=$_GET["server_ip"];}
        elseif (isset($_POST["server_ip"]))		{$server_ip=$_POST["server_ip"];}
if (isset($_GET["callerid"]))					{$callerid=$_GET["callerid"];}
        elseif (isset($_POST["callerid"]))		{$callerid=$_POST["callerid"];}
if (isset($_GET["protocol"]))					{$protocol=$_GET["protocol"];}
        elseif (isset($_POST["protocol"]))		{$protocol=$_POST["protocol"];}
if (isset($_GET["codecs"]))						{$codecs=$_GET["codecs"];}
        elseif (isset($_POST["codecs"]))		{$codecs=$_POST["codecs"];}
if (isset($_GET["options"]))					{$options=$_GET["options"];}
        elseif (isset($_POST["options"]))		{$options=$_POST["options"];}
if (isset($_GET["system_key"]))					{$system_key=$_GET["system_key"];}
        elseif (isset($_POST["system_key"]))	{$system_key=$_POST["system_key"];}

$query_string = "/agc/webphone/zoiperweb.php?DB=$DB&phone_login=$phone_login&phone_pass=$phone_pass&server_ip=$server_ip&callerid=$callerid&protocol=$protocol&codecs=$codecs&options=$options&system_key=$system_key";

$servers = array("sslagent1.server.net","sslagent2.server.net");
$server = $servers[array_rand($servers)];
$URL = "https://$server$query_string";

header("Location: $URL");

echo"<TITLE>Webphone Redirect</TITLE>\n";
echo"<META HTTP-EQUIV=\"Content-Type\" CONTENT=\"text/html; charset=iso-8859-1\">\n";
echo"<META HTTP-EQUIV=Refresh CONTENT=\"0; URL=$URL\">\n";
echo"</HEAD>\n";
echo"<BODY BGCOLOR=#FFFFFF marginheight=0 marginwidth=0 leftmargin=0 topmargin=0>\n";
echo"<a href=\"$URL\">click here to continue. . .</a>\n";
exit;

?>
