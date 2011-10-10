<?php
# web_form_forward.php - custom script forward agent to web page and alter vars
# 
# Copyright (C) 2009  Matt Florell <vicidial@gmail.com>    LICENSE: AGPLv2
#
# You will need to customize this to your needs
#
# CHANGELOG:
# 80626-1121 - First Build
# 90508-0644 - Changed to PHP long tags
#

if (isset($_GET["phone_number"]))	{$phone_number=$_GET["phone_number"];}
if (isset($_GET["source_id"]))		{$source_id=$_GET["source_id"];}
if (isset($_GET["user"]))			{$user=$_GET["user"];}

require("dbconnect.php");

$stmt="SELECT full_name from vicidial_users where user='$user';";
$rslt=mysql_query($stmt, $link);
$row=mysql_fetch_row($rslt);
$fullname=$row[0];

$URL = "http://astguiclient.sf.net/test.php?userid=$user&phone=$phone_number&Rep=$fullname&source_id=$source_id";

header("Location: $URL");
#exit;
echo"<HTML><HEAD>\n";
echo"<TITLE>Group1</TITLE>\n";
echo"<META HTTP-EQUIV=\"Content-Type\" CONTENT=\"text/html; charset=iso-8859-1\">\n";
echo"<META HTTP-EQUIV=Refresh CONTENT=\"0; URL=$URL\">\n";
echo"</HEAD>\n";
echo"<BODY BGCOLOR=#FFFFFF marginheight=0 marginwidth=0 leftmargin=0 topmargin=0>\n";
echo"<a href=\"$URL\">click here to continue. . .</a>\n";
echo"</BODY></HTML>\n";


?>
