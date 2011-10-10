<?php
# vicidial_redirect.php - forwards agents to another URL for vicidial.php login
# 
# Copyright (C) 2009  Matt Florell <vicidial@gmail.com>    LICENSE: AGPLv2
#
# CHANGELOG
# 71202-1546 - First Build 
# 90508-0727 - Changed to PHP long tags
#

if (isset($_GET["DB"]))						    {$DB=$_GET["DB"];}
        elseif (isset($_POST["DB"]))            {$DB=$_POST["DB"];}
if (isset($_GET["phone_login"]))                {$phone_login=$_GET["phone_login"];}
        elseif (isset($_POST["phone_login"]))   {$phone_login=$_POST["phone_login"];}
if (isset($_GET["phone_pass"]))					{$phone_pass=$_GET["phone_pass"];}
        elseif (isset($_POST["phone_pass"]))    {$phone_pass=$_POST["phone_pass"];}
if (isset($_GET["VD_login"]))					{$VD_login=$_GET["VD_login"];}
        elseif (isset($_POST["VD_login"]))      {$VD_login=$_POST["VD_login"];}
if (isset($_GET["VD_pass"]))					{$VD_pass=$_GET["VD_pass"];}
        elseif (isset($_POST["VD_pass"]))       {$VD_pass=$_POST["VD_pass"];}
if (isset($_GET["VD_campaign"]))                {$VD_campaign=$_GET["VD_campaign"];}
        elseif (isset($_POST["VD_campaign"]))   {$VD_campaign=$_POST["VD_campaign"];}
if (isset($_GET["relogin"]))					{$relogin=$_GET["relogin"];}
        elseif (isset($_POST["relogin"]))       {$relogin=$_POST["relogin"];}

if (!isset($phone_login)) 
	{
	if (isset($_GET["pl"]))                {$phone_login=$_GET["pl"];}
			elseif (isset($_POST["pl"]))   {$phone_login=$_POST["pl"];}
	}
if (!isset($phone_pass))
	{
	if (isset($_GET["pp"]))                {$phone_pass=$_GET["pp"];}
			elseif (isset($_POST["pp"]))   {$phone_pass=$_POST["pp"];}
	}

$URL = "http://192.168.1.60/agc/vicidial.php?phone_login=$phone_login&phone_pass=$phone_pass&DB=$DB&VD_login=$VD_login&VD_pass=$VD_pass&VD_campaign=$VD_campaign&relogin=$relogin";

echo"<TITLE>Agent Redirect</TITLE>\n";
echo"<META HTTP-EQUIV=\"Content-Type\" CONTENT=\"text/html; charset=iso-8859-1\">\n";
echo"<META HTTP-EQUIV=Refresh CONTENT=\"0; URL=$URL\">\n";
echo"</HEAD>\n";
echo"<BODY BGCOLOR=#FFFFFF marginheight=0 marginwidth=0 leftmargin=0 topmargin=0>\n";
echo"<a href=\"$URL\">click here to continue. . .</a>\n";
exit;

?>
