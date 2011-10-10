<?php
# active_list_refresh.php    version 2.2.0
# 
# Copyright (C) 2009  Matt Florell <vicidial@gmail.com>    LICENSE: AGPLv2
#
# This script is designed purely to serve updates of the live data to the display scripts
# This script depends on the server_ip being sent and also needs to have a valid user/pass from the vicidial_users table
# 
# required variables:
#  - $server_ip
#  - $session_name
#  - $user
#  - $pass
# optional variables:
#  - $ADD - ('1','2','3','4','5')
#  - $order - ('asc','desc')
#  - $format - ('text','table','menu','selectlist','textarea')
#  - $bgcolor - ('#123456','white','black','etc...')
#  - $txtcolor - ('#654321','black','white','etc...')
#  - $txtsize - ('1','2','3','etc...')
#  - $selectsize - ('2','3','4','etc...')
#  - $selectfontsize - ('8','10','12','etc...')
#  - $selectedext - ('cc100')
#  - $selectedtrunk - ('Zap/25-1')
#  - $selectedlocal - ('SIP/cc100')
#  - $textareaheight - ('8','10','12','etc...')
#  - $textareawidth - ('8','10','12','etc...')
#  - $field_name - ('extension','busyext','extension_xfer','etc...')
# 
#
# changes
# 50323-1147 - First build of script
# 50401-1132 - small formatting changes
# 50502-1402 - added field_name as modifiable variable
# 50503-1213 - added session_name checking for extra security
# 50503-1311 - added conferences list
# 50610-1155 - Added NULL check on MySQL results to reduced errors
# 50711-1209 - removed HTTP authentication in favor of user/pass vars
# 60421-1155 - check GET/POST vars lines with isset to not trigger PHP NOTICES
# 60619-1118 - Added variable filters to close security holes for login form
# 90508-0727 - Changed to PHP long tags
#

require("dbconnect.php");

### If you have globals turned off uncomment these lines
if (isset($_GET["user"]))					{$user=$_GET["user"];}
	elseif (isset($_POST["user"]))			{$user=$_POST["user"];}
if (isset($_GET["pass"]))					{$pass=$_GET["pass"];}
	elseif (isset($_POST["pass"]))			{$pass=$_POST["pass"];}
if (isset($_GET["server_ip"]))				{$server_ip=$_GET["server_ip"];}
	elseif (isset($_POST["server_ip"]))		{$server_ip=$_POST["server_ip"];}
if (isset($_GET["session_name"]))			{$session_name=$_GET["session_name"];}
	elseif (isset($_POST["session_name"]))	{$session_name=$_POST["session_name"];}
if (isset($_GET["format"]))					{$format=$_GET["format"];}
	elseif (isset($_POST["format"]))		{$format=$_POST["format"];}
if (isset($_GET["ADD"]))					{$ADD=$_GET["ADD"];}
	elseif (isset($_POST["ADD"]))			{$ADD=$_POST["ADD"];}
if (isset($_GET["order"]))					{$order=$_GET["order"];}
	elseif (isset($_POST["order"]))			{$order=$_POST["order"];}
if (isset($_GET["bgcolor"]))				{$bgcolor=$_GET["bgcolor"];}
	elseif (isset($_POST["bgcolor"]))		{$bgcolor=$_POST["bgcolor"];}
if (isset($_GET["txtcolor"]))				{$txtcolor=$_GET["txtcolor"];}
	elseif (isset($_POST["txtcolor"]))		{$txtcolor=$_POST["txtcolor"];}
if (isset($_GET["txtsize"]))				{$txtsize=$_GET["txtsize"];}
	elseif (isset($_POST["txtsize"]))		{$txtsize=$_POST["txtsize"];}
if (isset($_GET["selectsize"]))				{$selectsize=$_GET["selectsize"];}
	elseif (isset($_POST["selectsize"]))	{$selectsize=$_POST["selectsize"];}
if (isset($_GET["selectfontsize"]))				{$selectfontsize=$_GET["selectfontsize"];}
	elseif (isset($_POST["selectfontsize"]))	{$selectfontsize=$_POST["selectfontsize"];}
if (isset($_GET["selectedext"]))			{$selectedext=$_GET["selectedext"];}
	elseif (isset($_POST["selectedext"]))	{$selectedext=$_POST["selectedext"];}
if (isset($_GET["selectedtrunk"]))			{$selectedtrunk=$_GET["selectedtrunk"];}
	elseif (isset($_POST["selectedtrunk"]))	{$selectedtrunk=$_POST["selectedtrunk"];}
if (isset($_GET["selectedlocal"]))			{$selectedlocal=$_GET["selectedlocal"];}
	elseif (isset($_POST["selectedlocal"]))	{$selectedlocal=$_POST["selectedlocal"];}
if (isset($_GET["textareaheight"]))				{$textareaheight=$_GET["textareaheight"];}
	elseif (isset($_POST["textareaheight"]))	{$textareaheight=$_POST["textareaheight"];}
if (isset($_GET["textareawidth"]))			{$textareawidth=$_GET["textareawidth"];}
	elseif (isset($_POST["textareawidth"]))	{$textareawidth=$_POST["textareawidth"];}
if (isset($_GET["field_name"]))				{$field_name=$_GET["field_name"];}
	elseif (isset($_POST["field_name"]))	{$field_name=$_POST["field_name"];}

### security strip all non-alphanumeric characters out of the variables ###
	$user=ereg_replace("[^0-9a-zA-Z]","",$user);
	$pass=ereg_replace("[^0-9a-zA-Z]","",$pass);
	$ADD=ereg_replace("[^0-9]","",$ADD);
	$order=ereg_replace("[^0-9a-zA-Z]","",$order);
	$format=ereg_replace("[^0-9a-zA-Z]","",$format);
	$bgcolor=ereg_replace("[^\#0-9a-zA-Z]","",$bgcolor);
	$txtcolor=ereg_replace("[^\#0-9a-zA-Z]","",$txtcolor);
	$txtsize=ereg_replace("[^0-9a-zA-Z]","",$txtsize);
	$selectsize=ereg_replace("[^0-9a-zA-Z]","",$selectsize);
	$selectfontsize=ereg_replace("[^0-9a-zA-Z]","",$selectfontsize);
	$selectedext=ereg_replace("[^ \#\*\:\/\@\.\-\_0-9a-zA-Z]","",$selectedext);
	$selectedtrunk=ereg_replace("[^ \#\*\:\/\@\.\-\_0-9a-zA-Z]","",$selectedtrunk);
	$selectedlocal=ereg_replace("[^ \#\*\:\/\@\.\-\_0-9a-zA-Z]","",$selectedlocal);
	$textareaheight=ereg_replace("[^0-9a-zA-Z]","",$textareaheight);
	$textareawidth=ereg_replace("[^0-9a-zA-Z]","",$textareawidth);
	$field_name=ereg_replace("[^ \#\*\:\/\@\.\-\_0-9a-zA-Z]","",$field_name);

# default optional vars if not set
if (!isset($ADD))				{$ADD="1";}
if (!isset($order))				{$order='desc';}
if (!isset($format))			{$format="text";}
if (!isset($bgcolor))			{$bgcolor='white';}
if (!isset($txtcolor))			{$txtcolor='black';}
if (!isset($txtsize))			{$txtsize='2';}
if (!isset($selectsize))		{$selectsize='4';}
if (!isset($selectfontsize))	{$selectfontsize='10';}
if (!isset($textareaheight))	{$textareaheight='10';}
if (!isset($textareawidth))		{$textareawidth='20';}

$version = '0.0.8';
$build = '60619-1118';
$StarTtime = date("U");
$NOW_DATE = date("Y-m-d");
$NOW_TIME = date("Y-m-d H:i:s");
if (!isset($query_date)) {$query_date = $NOW_DATE;}

	$stmt="SELECT count(*) from vicidial_users where user='$user' and pass='$pass' and user_level > 0;";
	if ($DB) {echo "|$stmt|\n";}
	$rslt=mysql_query($stmt, $link);
	$row=mysql_fetch_row($rslt);
	$auth=$row[0];

  if( (strlen($user)<2) or (strlen($pass)<2) or ($auth==0))
	{
    echo "Недопустимо Username/Пароль: |$user|$pass|\n";
    exit;
	}
  else
	{

	if( (strlen($server_ip)<6) or (!isset($server_ip)) or ( (strlen($session_name)<12) or (!isset($session_name)) ) )
		{
		echo "Недопустимо server_ip: |$server_ip|  or  Недопустимо session_name: |$session_name|\n";
		exit;
		}
	else
		{
		$stmt="SELECT count(*) from web_client_sessions where session_name='$session_name' and server_ip='$server_ip';";
		if ($DB) {echo "|$stmt|\n";}
		$rslt=mysql_query($stmt, $link);
		$row=mysql_fetch_row($rslt);
		$SNauth=$row[0];
		  if($SNauth==0)
			{
			echo "Недопустимо session_name: |$session_name|$server_ip|\n";
			exit;
			}
		  else
			{
			# do nothing for now
			}
		}
	}

if ($format=='table')
{
echo "<html>\n";
echo "<head>\n";
echo "<!-- ВЕРСИЯ: $version     СБОРКА: $build    ADD: $ADD   server_ip: $server_ip-->\n";
echo "<title>Показывать Списки: ";
if ($ADD==1)		{echo "Активные расширения";}
if ($ADD==2)		{echo "Занятые Расширения";}
if ($ADD==3)		{echo "Внешние Линии";}
if ($ADD==4)		{echo "Локальные Расширения";}
if ($ADD==5)		{echo "Конференции";}
if ($ADD==99999)	{echo "ПОМОЩЬ";}
echo "</title>\n";
echo "</head>\n";
echo "<BODY BGCOLOR=white marginheight=0 marginwidth=0 leftmargin=0 topmargin=0>\n";
}





######################
# ADD=1 display all live extensions on a server
######################
if ($ADD==1)
{
	$pt='pt';
	if (!$field_name) {$field_name = 'extension';}
	if ($format=='table') {echo "<TABLE WIDTH=120 BGCOLOR=$bgcolor cellpadding=0 cellspacing=0>\n";}
	if ($format=='menu') {echo "<SELECT SIZE=1 name=\"$field_name\">\n";}
	if ($format=='selectlist') 
		{
		echo "<SELECT SIZE=$selectsize name=\"$field_name\" STYLE=\"font-family : sans-serif; font-size : $selectfontsize$pt\">\n";
		}
	if ($format=='textarea') 
		{
		echo "<TEXTAREA ROWS=$textareaheight COLS=$textareawidth NAME=extension WRAP=off STYLE=\"font-family : sans-serif; font-size : $selectfontsize$pt\">";
		}

	$stmt="SELECT extension,fullname FROM phones where server_ip = '$server_ip' order by extension $order";
		if ($format=='table') {echo "\n<!-- $stmt -->";}
	$rslt=mysql_query($stmt, $link);
	if ($rslt) {$phones_to_print = mysql_num_rows($rslt);}
	$o=0;
	while ($phones_to_print > $o) {
		$row=mysql_fetch_row($rslt);
		if ($format=='table')
			{
			echo "<TR><TD ALIGN=LEFT NOWRAP><FONT FACE=\"ARIAL,HELVETICA\" COLOR=$txtcolor SIZE=$txtsize>";
			echo "$row[0] - $row[1]";
			echo "</TD></TR>\n";
			}
		if ( ($format=='text') or ($format=='textarea') )
			{
			echo "$row[0] - $row[1]\n";
			}
		if ( ($format=='menu') or ($format=='selectlist') )
			{
			echo "<OPTION ";
			if ($row[0]=="$selectedext") {echo "SELECTED ";}
			echo "VALUE=\"$row[0]\">";
			echo "$row[0] - $row[1]";
			echo "</OPTION>\n";
			}
		$o++;
	}

	if ($format=='table') {echo "</TABLE>\n";}
	if ($format=='menu') {echo "</SELECT>\n";}
	if ($format=='selectlist') {echo "</SELECT>\n";}
	if ($format=='textarea') {echo "</TEXTAREA>\n";}
}







######################
# ADD=2 display all busy extensions on a server
######################
if ($ADD==2)
{
	if (!$field_name) {$field_name = 'busyext';}
	if ($format=='table') {echo "<TABLE WIDTH=120 BGCOLOR=$bgcolor cellpadding=0 cellspacing=0>\n";}
	if ($format=='menu') {echo "<SELECT SIZE=1 name=\"$field_name\">\n";}
	if ($format=='selectlist') 
		{
		echo "<SELECT SIZE=$selectsize name=\"$field_name\" STYLE=\"font-family : sans-serif; font-size : $selectfontsize$pt\">\n";
		}
	if ($format=='textarea') 
		{
		echo "<TEXTAREA ROWS=$textareaheight COLS=$textareawidth NAME=extension WRAP=off STYLE=\"font-family : sans-serif; font-size : $selectfontsize$pt\">";
		}

	$stmt="SELECT extension FROM live_channels where server_ip = '$server_ip' order by extension $order";
		if ($format=='table') {echo "\n<!-- $stmt -->";}
	$rslt=mysql_query($stmt, $link);
	if ($rslt) {$busys_to_print = mysql_num_rows($rslt);}
	$o=0;
	while ($busys_to_print > $o) {
		$row=mysql_fetch_row($rslt);
		if ($format=='table')
			{
			echo "<TR><TD ALIGN=LEFT NOWRAP><FONT FACE=\"ARIAL,HELVETICA\" COLOR=$txtcolor SIZE=$txtsize>";
			echo "$row[0]";
			echo "</TD></TR>\n";
			}
		if ( ($format=='text') or ($format=='textarea') )
			{
			echo "$row[0]\n";
			}
		if ( ($format=='menu') or ($format=='selectlist') )
			{
			echo "<OPTION ";
			if ($row[0]=="$selectedext") {echo "SELECTED ";}
			echo "VALUE=\"$row[0]\">";
			echo "$row[0]";
			echo "</OPTION>\n";
			}
		$o++;
	}

	if ($format=='table') {echo "</TABLE>\n";}
	if ($format=='menu') {echo "</SELECT>\n";}
	if ($format=='selectlist') {echo "</SELECT>\n";}
	if ($format=='textarea') {echo "</TEXTAREA>\n";}
}






######################
# ADD=3 display all busy outside lines(trunks) on a server
######################
if ($ADD==3)
{
	if (!$field_name) {$field_name = 'trunk';}
	if ($format=='table') {echo "<TABLE WIDTH=120 BGCOLOR=$bgcolor cellpadding=0 cellspacing=0>\n";}
	if ($format=='menu') {echo "<SELECT SIZE=1 name=\"$field_name\">\n";}
	if ($format=='selectlist') 
		{
		echo "<SELECT SIZE=$selectsize name=\"$field_name\" STYLE=\"font-family : sans-serif; font-size : $selectfontsize$pt\">\n";
		}
	if ($format=='textarea') 
		{
		echo "<TEXTAREA ROWS=$textareaheight COLS=$textareawidth NAME=extension WRAP=off STYLE=\"font-family : sans-serif; font-size : $selectfontsize$pt\">";
		}

	$stmt="SELECT channel, extension FROM live_channels where server_ip = '$server_ip' order by channel $order";
		if ($format=='table') {echo "\n<!-- $stmt -->";}
	$rslt=mysql_query($stmt, $link);
	if ($rslt) {$busys_to_print = mysql_num_rows($rslt);}
	$o=0;
	while ($busys_to_print > $o) {
		$row=mysql_fetch_row($rslt);
		if ($format=='table')
			{
			echo "<TR><TD ALIGN=LEFT NOWRAP><FONT FACE=\"ARIAL,HELVETICA\" COLOR=$txtcolor SIZE=$txtsize>";
			echo "$row[0] - $row[1]";
			echo "</TD></TR>\n";
			}
		if ( ($format=='text') or ($format=='textarea') )
			{
			echo "$row[0] - $row[1]\n";
			}
		if ( ($format=='menu') or ($format=='selectlist') )
			{
			echo "<OPTION ";
			if ($row[0]=="$selectedtrunk") {echo "SELECTED ";}
			echo "VALUE=\"$row[0]\">";
			echo "$row[0] - $row[1]";
			echo "</OPTION>\n";
			}
		$o++;
	}

	if ($format=='table') {echo "</TABLE>\n";}
	if ($format=='menu') {echo "</SELECT>\n";}
	if ($format=='selectlist') {echo "</SELECT>\n";}
	if ($format=='textarea') {echo "</TEXTAREA>\n";}
}






######################
# ADD=4 display all busy Local lines on a server
######################
if ($ADD==4)
{
	if (!$field_name) {$field_name = 'local';}
	if ($format=='table') {echo "<TABLE WIDTH=120 BGCOLOR=$bgcolor cellpadding=0 cellspacing=0>\n";}
	if ($format=='menu') {echo "<SELECT SIZE=1 name=\"$field_name\">\n";}
	if ($format=='selectlist') 
		{
		echo "<SELECT SIZE=$selectsize name=\"$field_name\" STYLE=\"font-family : sans-serif; font-size : $selectfontsize$pt\">\n";
		}
	if ($format=='textarea') 
		{
		echo "<TEXTAREA ROWS=$textareaheight COLS=$textareawidth NAME=extension WRAP=off STYLE=\"font-family : sans-serif; font-size : $selectfontsize$pt\">";
		}

	$stmt="SELECT channel, extension FROM live_sip_channels where server_ip = '$server_ip' order by channel $order";
		if ($format=='table') {echo "\n<!-- $stmt -->";}
	$rslt=mysql_query($stmt, $link);
	if ($rslt) {$busys_to_print = mysql_num_rows($rslt);}
	$o=0;
	while ($busys_to_print > $o) {
		$row=mysql_fetch_row($rslt);
		if ($format=='table')
			{
			echo "<TR><TD ALIGN=LEFT NOWRAP><FONT FACE=\"ARIAL,HELVETICA\" COLOR=$txtcolor SIZE=$txtsize>";
			echo "$row[0] - $row[1]";
			echo "</TD></TR>\n";
			}
		if ( ($format=='text') or ($format=='textarea') )
			{
			echo "$row[0] - $row[1]\n";
			}
		if ( ($format=='menu') or ($format=='selectlist') )
			{
			echo "<OPTION ";
			if ($row[0]=="$selectedlocal") {echo "SELECTED ";}
			echo "VALUE=\"$row[0]\">";
			echo "$row[0] - $row[1]";
			echo "</OPTION>\n";
			}
		$o++;
	}

	if ($format=='table') {echo "</TABLE>\n";}
	if ($format=='menu') {echo "</SELECT>\n";}
	if ($format=='selectlist') {echo "</SELECT>\n";}
	if ($format=='textarea') {echo "</TEXTAREA>\n";}
}






######################
# ADD=5 display all agc-usable conferences on a server
######################
if ($ADD==5)
{
	$pt='pt';
	if (!$field_name) {$field_name = 'conferences';}
	if ($format=='table') {echo "<TABLE WIDTH=120 BGCOLOR=$bgcolor cellpadding=0 cellspacing=0>\n";}
	if ($format=='menu') {echo "<SELECT SIZE=1 name=\"$field_name\">\n";}
	if ($format=='selectlist') 
		{
		echo "<SELECT SIZE=$selectsize name=\"$field_name\" STYLE=\"font-family : sans-serif; font-size : $selectfontsize$pt\">\n";
		}
	if ($format=='textarea') 
		{
		echo "<TEXTAREA ROWS=$textareaheight COLS=$textareawidth NAME=extension WRAP=off STYLE=\"font-family : sans-serif; font-size : $selectfontsize$pt\">";
		}

	$stmt="SELECT conf_exten,extension FROM conferences where server_ip = '$server_ip' order by conf_exten $order";
		if ($format=='table') {echo "\n<!-- $stmt -->";}
	$rslt=mysql_query($stmt, $link);
	if ($rslt) {$phones_to_print = mysql_num_rows($rslt);}
	$o=0;
	while ($phones_to_print > $o) {
		$row=mysql_fetch_row($rslt);
		if ($format=='table')
			{
			echo "<TR><TD ALIGN=LEFT NOWRAP><FONT FACE=\"ARIAL,HELVETICA\" COLOR=$txtcolor SIZE=$txtsize>";
			echo "$row[0] - $row[1]";
			echo "</TD></TR>\n";
			}
		if ( ($format=='text') or ($format=='textarea') )
			{
			echo "$row[0] - $row[1]\n";
			}
		if ( ($format=='menu') or ($format=='selectlist') )
			{
			echo "<OPTION ";
			if ($row[0]=="$selectedext") {echo "SELECTED ";}
			echo "VALUE=\"$row[0]\">";
			echo "$row[0] - $row[1]";
			echo "</OPTION>\n";
			}
		$o++;
	}

	if ($format=='table') {echo "</TABLE>\n";}
	if ($format=='menu') {echo "</SELECT>\n";}
	if ($format=='selectlist') {echo "</SELECT>\n";}
	if ($format=='textarea') {echo "</TEXTAREA>\n";}
}














$ENDtime = date("U");
$RUNtime = ($ENDtime - $StarTtime);
if ($format=='table') {echo "\n<!-- время выполнения скрипта: $RUNtime секунды -->";}
if ($format=='table') {echo "\n</body>\n</html>\n";}
	
exit; 

?>





