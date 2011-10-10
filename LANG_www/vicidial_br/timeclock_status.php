<?php
# timeclock_status.php
# 
# Copyright (C) 2010  Matt Florell <vicidial@gmail.com>    LICENSE: AGPLv2
#
# CHANGES
#
# 80602-0201 - First Build
# 80603-1500 - formatting changes
# 90310-2103 - Added admin header
# 90508-0644 - Changed to PHP long tags
# 100214-1421 - Sort menu alphabetically
#

header ("Content-type: text/html; charset=utf-8");

require("dbconnect.php");

$PHP_AUTH_USER=$_SERVER['PHP_AUTH_USER'];
$PHP_AUTH_PW=$_SERVER['PHP_AUTH_PW'];
$PHP_SELF=$_SERVER['PHP_SELF'];
if (isset($_GET["begin_date"]))				{$begin_date=$_GET["begin_date"];}
	elseif (isset($_POST["begin_date"]))	{$begin_date=$_POST["begin_date"];}
if (isset($_GET["end_date"]))				{$end_date=$_GET["end_date"];}
	elseif (isset($_POST["end_date"]))		{$end_date=$_POST["end_date"];}
if (isset($_GET["user"]))					{$user=$_GET["user"];}
	elseif (isset($_POST["user"]))			{$user=$_POST["user"];}
if (isset($_GET["user_group"]))				{$user_group=$_GET["user_group"];}
	elseif (isset($_POST["user_group"]))	{$user_group=$_POST["user_group"];}
if (isset($_GET["DB"]))						{$DB=$_GET["DB"];}
	elseif (isset($_POST["DB"]))			{$DB=$_POST["DB"];}
if (isset($_GET["submit"]))					{$submit=$_GET["submit"];}
	elseif (isset($_POST["submit"]))		{$submit=$_POST["submit"];}
if (isset($_GET["ENVIAR"]))					{$ENVIAR=$_GET["ENVIAR"];}
	elseif (isset($_POST["ENVIAR"]))		{$ENVIAR=$_POST["ENVIAR"];}


#############################################
##### START SYSTEM_SETTINGS LOOKUP #####
$stmt = "SELECT use_non_latin,webroot_writable,timeclock_end_of_day,outbound_autodial_active FROM system_settings;";
$rslt=mysql_query($stmt, $link);
if ($DB) {echo "$stmt\n";}
$qm_conf_ct = mysql_num_rows($rslt);
if ($qm_conf_ct > 0)
	{
	$row=mysql_fetch_row($rslt);
	$non_latin =					$row[0];
	$webroot_writable =				$row[1];
	$timeclock_end_of_day =			$row[2];
	$SSoutbound_autodial_active =	$row[3];
	}
##### END SETTINGS LOOKUP #####
###########################################

$PHP_AUTH_USER = ereg_replace("[^0-9a-zA-Z]","",$PHP_AUTH_USER);
$PHP_AUTH_PW = ereg_replace("[^0-9a-zA-Z]","",$PHP_AUTH_PW);

$STARTtime = date("U");
$TODAY = date("Y-m-d");
$HHMM = date("Hi");
$HHteod = substr($timeclock_end_of_day,0,2);
$MMteod = substr($timeclock_end_of_day,2,2);

if ($HHMM < $timeclock_end_of_day)
	{$EoD = mktime($HHteod, $MMteod, 10, date("m"), date("d")-1, date("Y"));}
else
	{$EoD = mktime($HHteod, $MMteod, 10, date("m"), date("d"), date("Y"));}

$EoDdate = date("Y-m-d H:i:s", $EoD);

$stmt="SELECT count(*) from vicidial_users where user='$PHP_AUTH_USER' and pass='$PHP_AUTH_PW' and user_level > 7 and view_reports='1';";
if ($non_latin > 0) { $rslt=mysql_query("SET NAMES 'UTF8'");}
$rslt=mysql_query($stmt, $link);
$row=mysql_fetch_row($rslt);
$auth=$row[0];

$fp = fopen ("./project_auth_entries.txt", "a");
$date = date("r");
$ip = getenv("REMOTE_ADDR");
$browser = getenv("HTTP_USER_AGENT");

if( (strlen($PHP_AUTH_USER)<2) or (strlen($PHP_AUTH_PW)<2) or (!$auth))
	{
    Header("WWW-Authenticate: Basic realm=\"VICI-PROJECTS\"");
    Header("HTTP/1.0 401 Unauthorized");
    echo "Nome ou Senha inválidos: |$PHP_AUTH_USER|$PHP_AUTH_PW|\n";
    exit;
	}
else
	{
	if($auth>0)
		{
			$stmt="SELECT full_name from vicidial_users where user='$PHP_AUTH_USER' and pass='$PHP_AUTH_PW'";
			$rslt=mysql_query($stmt, $link);
			$row=mysql_fetch_row($rslt);
			$LOGfullname=$row[0];
		if ($webroot_writable > 0)
			{
			fwrite ($fp, "VICIDIAL|GOOD|$date|$PHP_AUTH_USER|$PHP_AUTH_PW|$ip|$browser|$LOGfullname|\n");
			fclose($fp);
			}
		}
	else
		{
		if ($webroot_writable > 0)
			{
			fwrite ($fp, "VICIDIAL|FAIL|$date|$PHP_AUTH_USER|$PHP_AUTH_PW|$ip|$browser|\n");
			fclose($fp);
			}
		}

	}


$stmt="select user_group from vicidial_user_groups order by user_group;";
$rslt=mysql_query($stmt, $link);
if ($DB) {echo "$stmt\n";}
$user_groups_to_print = mysql_num_rows($rslt);
	$i=0;
	$user_groups_to_print++;
while ($i < $user_groups_to_print)
	{
	$row=mysql_fetch_row($rslt);
	$LISTuser_groups[$i] =$row[0];
	if ($row[0]==$user_group)
		{$FORMuser_groups.="<option value=\"$row[0]\" SELECTED>$row[0]</option>";}
	else
		{$FORMuser_groups.="<option value=\"$row[0]\">$row[0]</option>";}
	$i++;
	}

if (strlen($user_group) > 0)
	{
	$stmt="SELECT group_name from vicidial_user_groups where user_group='$user_group';";
	$rslt=mysql_query($stmt, $link);
	$row=mysql_fetch_row($rslt);
	$group_name = $row[0];
	}

?>
<html>
<head>
<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=utf-8">
<title>ADMINISTRATION:Relógio Ponto Status
<?php

##### BEGIN Set variables to make header show properly #####
$ADD =					'311111';
$hh =					'usergroups';
$LOGast_admin_access =	'1';
$ADMIN =				'admin.php';
$page_width='770';
$section_width='750';
$header_font_size='3';
$subheader_font_size='2';
$subcamp_font_size='2';
$header_selected_bold='<b>';
$header_nonselected_bold='';
$usergroups_color =		'#FFFF99';
$usergroups_font =		'BLACK';
$usergroups_color =		'#E6E6E6';
$subcamp_color =	'#C6C6C6';
##### END Set variables to make header show properly #####

require("admin_header.php");



?>
<CENTER>
<TABLE WIDTH=750 BGCOLOR=#D9E6FE cellpadding=2 cellspacing=0><TR BGCOLOR=#015B91><TD ALIGN=LEFT>
<FONT FACE="ARIAL,HELVETICA" COLOR=WHITE SIZE=2><B>Relógio Ponto Status for <?php echo $user_group ?></TD><TD ALIGN=RIGHT> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
<?php 
echo "<a href=\"./timeclock_report.php\"><FONT FACE=\"ARIAL,HELVETICA\" COLOR=WHITE SIZE=2><B>TIMECLOCK REPORT</a> | ";
echo "<a href=\"./admin.php?ADD=311111&user_group=$user_group\"><FONT FACE=\"ARIAL,HELVETICA\" COLOR=WHITE SIZE=2><B>GRUPO DE USUÁRIOS</a>\n";
?>
</TD></TR>




<?php 

echo "<TR BGCOLOR=\"#F0F5FE\"><TD ALIGN=LEFT COLSPAN=2><FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2><B> &nbsp; \n";

echo "<form action=$PHP_SELF method=POST>\n";
echo "<input type=hidden name=DB value=\"$DB\">\n";
echo "<select size=1 name=user_group>$FORMuser_groups</select>";
echo "<input type=submit name=submit value=submit>\n";

echo "</B></TD></TR>\n";
echo "<TR><TD ALIGN=LEFT COLSPAN=2>\n";
echo "<br><center>\n";

if (strlen($user_group) < 1)
	{
	exit;
	}


##### grab all users in this user_group #####
$stmt="SELECT user,full_name from vicidial_users where user_group='" . mysql_real_escape_string($user_group) . "' order by full_name;";
if ($DB>0) {echo "|$stmt|";}
$rslt=mysql_query($stmt, $link);
$users_to_print = mysql_num_rows($rslt);
$o=0;
while ($users_to_print > $o) 
	{
	$row=mysql_fetch_row($rslt);
	$users[$o] =		$row[0];
	$full_name[$o] =	$row[1];
	$Vevent_time[$o] =	'';
	$Vevent_epoch[$o] =	0;
	$Vcampaign[$o] =	'';
	$Tevent_epoch[$o] =	'';
	$Tevent_date[$o] =	'';
	$Tstatus[$o] =		'';
	$Tip_address[$o] =	'';
	$Tlogin_time[$o] =	'';
	$Tlogin_sec[$o] =	0;

	$o++;
	}

$o=0;
while ($users_to_print > $o) 
	{
	$total_login_time = 0;
	##### grab timeclock status record for this user #####
	$stmt="SELECT event_epoch,event_date,status,ip_address from vicidial_timeclock_status where user='$users[$o]' and event_epoch >= '$EoD';";
	if ($DB>0) {echo "|$stmt|";}
	$rslt=mysql_query($stmt, $link);
	$stats_to_print = mysql_num_rows($rslt);
	if ($stats_to_print > 0) 
		{
		$row=mysql_fetch_row($rslt);
		$Tevent_epoch[$o] =	$row[0];
		$Tevent_date[$o] =	$row[1];
		$Tstatus[$o] =		$row[2];
		$Tip_address[$o] =	$row[3];

		if ( ($row[2]=='START') or ($row[2]=='LOGIN') )
			{$bgcolor[$o]='bgcolor="#B9CBFD"';} 
		else
			{$bgcolor[$o]='bgcolor="#9BB9FB"';}
		}

	##### grab timeclock logged-in time for each user #####
	$stmt="SELECT event,event_epoch,login_sec from vicidial_timeclock_log where user='$users[$o]' and event_epoch >= '$EoD';";
	if ($DB>0) {echo "|$stmt|";}
	$rslt=mysql_query($stmt, $link);
	$logs_to_parse = mysql_num_rows($rslt);
	$p=0;
	while ($logs_to_parse > $p) 
		{
		$row=mysql_fetch_row($rslt);
		if ( (ereg("LOGIN", $row[0])) or (ereg("START", $row[0])) )
			{
			$login_sec='';
			$Tevent_time[$o] = date("Y-m-d H:i:s", $row[1]);
			}
		if (ereg("LOGOUT", $row[0]))
			{
			$login_sec = $row[2];
			$total_login_time = ($total_login_time + $login_sec);
			}
		$p++;
		}
	if ( (strlen($login_sec)<1) and ($logs_to_parse > 0) )
		{
		$login_sec = ($STARTtime - $row[1]);
		$total_login_time = ($total_login_time + $login_sec);
		}
	if ($logs_to_parse > 0)
		{
		$total_login_hours = ($total_login_time / 3600);
		$total_login_hours_int = round($total_login_hours, 2);
		$total_login_hours_int = intval("$total_login_hours");
		$total_login_minutes = ($total_login_hours - $total_login_hours_int);
		$total_login_minutes = ($total_login_minutes * 60);
		$total_login_minutes_int = round($total_login_minutes, 0);
		if ($total_login_minutes_int < 10) {$total_login_minutes_int = "0$total_login_minutes_int";}

		$Tlogin_time[$o] = "$total_login_hours_int:$total_login_minutes_int";
		$Tlogin_sec[$o] = $total_login_time;
		}
	else
		{
		$total_login_time = 0;
		$Tlogin_time[$o] = "0:00";
		$Tlogin_sec[$o] = $total_login_time;
		}

	if ($DB>0) {echo "|$Tlogin_sec[$o]|$Tlogin_time[$o]|";}

	##### grab vicidial_agent_log records in this user_group #####
	$stmt="SELECT event_time,UNIX_TIMESTAMP(event_time),campaign_id from vicidial_agent_log where user='$users[$o]' and event_time >= '$EoDdate' order by agent_log_id desc limit 1;";
	if ($DB>0) {echo "|$stmt|";}
	$rslt=mysql_query($stmt, $link);
	$vals_to_print = mysql_num_rows($rslt);
	if ($vals_to_print > 0) 
		{
		$row=mysql_fetch_row($rslt);
		$Vevent_time[$o] =	$row[0];
		$Vevent_epoch[$o] =	$row[1];
		$Vcampaign[$o] =	$row[2];
		}

	$o++;
	}


##### print each user that has any activity for today #####
echo "<br>\n";
echo "<center>\n";

echo "<TABLE width=720 cellspacing=0 cellpadding=1>\n";
echo "<TR>\n";
echo "<TD bgcolor=\"#99FF33\"> &nbsp; &nbsp; </TD><TD align=left> TC Logged in and VICI active</TD>\n"; # bright green
echo "<TD bgcolor=\"#FFFF33\"> &nbsp; &nbsp; </TD><TD align=left> TC Logged in only</TD>\n"; # bright yellow
echo "<TD bgcolor=\"#FF6666\"> &nbsp; &nbsp; </TD><TD align=left> VICI active only</TD>\n"; # bright red
echo "</TR><TR>\n";
echo "<TD bgcolor=\"#66CC66\"> &nbsp; &nbsp; </TD><TD align=left> TC Logged out and VICI active</TD>\n"; # dull green
echo "<TD bgcolor=\"#CCCC00\"> &nbsp; &nbsp; </TD><TD align=left> TC Logged out only</TD>\n"; # dull yellow
echo "<TD> &nbsp; &nbsp; </TD><TD align=left> &nbsp; </TD>\n";
echo "</TR></TABLE><BR>\n";

echo "<B>USER STATUS FOR GRUPO DE USUÁRIOS: $user_group</B>\n";
echo "<TABLE width=700 cellspacing=0 cellpadding=1>\n";
echo "<tr><td><font size=2># </td><td><font size=2>USER </td><td align=left><font size=2>NAME </td><td align=right><font size=2> IP ADDRESS</td><td align=right><font size=2> TC TIME</td><td align=right><font size=2>TC LOGIN</td><td align=right><font size=2> VICI LAST LOG</td><td align=right><font size=2> VICI CAMPANHA</td></tr>\n";

$o=0;
$s=0;
while ($users_to_print > $o) 
	{
	if ( ($Tlogin_sec[$o] > 0) or (strlen($Vevent_time[$o]) > 0) )
		{
		if ( ($Tstatus[$o]=='START') or ($Tstatus[$o]=='LOGIN') )
			{
			if ($Tlogin_sec[$o] > 0)
				{$bgcolor[$o]='bgcolor="#FFFF33"';} # yellow
			if ( ($Tlogin_sec[$o] > 0) and (strlen($Vevent_time[$o]) > 0) )
				{$bgcolor[$o]='bgcolor="#99FF33"';} # green
			}
		else
			{
			if ($Tlogin_sec[$o] > 0)
				{$bgcolor[$o]='bgcolor="#CCCC00"';} # yellow
			if (strlen($Vevent_time[$o]) > 0)
				{$bgcolor[$o]='bgcolor="#FF6666"';} # red
			if ( ($Tlogin_sec[$o] > 0) and (strlen($Vevent_time[$o]) > 0) )
				{$bgcolor[$o]='bgcolor="#66CC66"';} # green
			}

		$s++;
		echo "<tr $bgcolor[$o]>";
		echo "<td><font size=1>$s</td>";
		echo "<td><font size=2><a href=\"./user_status.php?user=$users[$o]\">$users[$o]</a></td>";
		echo "<td><font size=2>$full_name[$o]</td>";
		echo "<td><font size=2>$Tip_address[$o]</td>";
		echo "<td align=right><font size=2>$Tlogin_time[$o]</td>";
		echo "<td align=right><font size=2>$Tevent_time[$o]</td>";
		echo "<td align=right><font size=2>$Vevent_time[$o]</td>";
		echo "<td align=right><font size=2>$Vcampaign[$o]</td>";
		echo "</tr>";

		if (strlen($Tstatus[$o])>0)
			{$TOTlogin_sec = ($TOTlogin_sec + $Tlogin_sec[$o]);}
		}
	$o++;
	}



$total_login_hours = ($TOTlogin_sec / 3600);
$total_login_hours_int = round($total_login_hours, 2);
$total_login_hours_int = intval("$total_login_hours");
$total_login_minutes = ($total_login_hours - $total_login_hours_int);
$total_login_minutes = ($total_login_minutes * 60);
$total_login_minutes_int = round($total_login_minutes, 0);
if ($total_login_minutes_int < 10) {$total_login_minutes_int = "0$total_login_minutes_int";}

echo "<tr bgcolor=white>";
echo "<td colspan=4><font size=2>TOTALS</td>";
echo "<td align=right><font size=2>$total_login_hours_int:$total_login_minutes_int</td>";
echo "<td align=right><font size=2></td>";
echo "<td align=right><font size=2></td>";
echo "<td align=right><font size=2></td>";
echo "</tr>";
echo "</table>";




$ENDtime = date("U");

$RUNtime = ($ENDtime - $STARTtime);

echo "\n\n\n<br><br><br>\n\n";


echo "<font size=0>\n\n\n<br><br><br>\nScript runtime: $RUNtime seconds</font>";


?>


</TD></TR><TABLE>
</body>
</html>

<?php
exit;











##### vicidial_timeclock log records for user #####

$SQday_ARY =	explode('-',$begin_date);
$EQday_ARY =	explode('-',$end_date);
$SQepoch = mktime(0, 0, 0, $SQday_ARY[1], $SQday_ARY[2], $SQday_ARY[0]);
$EQepoch = mktime(23, 59, 59, $EQday_ARY[1], $EQday_ARY[2], $EQday_ARY[0]);

echo "<br><br>\n";

echo "<center>\n";

echo "<B>TIMECLOCK HORÁRIO DE LOGIN/LOGOUT:</B>\n";
echo "<TABLE width=550 cellspacing=0 cellpadding=1>\n";
echo "<tr><td><font size=2>ID </td><td><font size=2>EDIT </td><td align=right><font size=2>EVENTO</td><td align=right><font size=2> DATA</td><td align=right><font size=2> IP ADDRESS</td><td align=right><font size=2> GROUP</td><td align=right><font size=2>HORAS:MINUTOS</td></tr>\n";

	$stmt="SELECT event,event_epoch,user_group,login_sec,ip_address,timeclock_id,manager_user from vicidial_timeclock_log where user='" . mysql_real_escape_string($user) . "' and event_epoch >= '$SQepoch'  and event_epoch <= '$EQepoch';";
	if ($DB>0) {echo "|$stmt|";}
	$rslt=mysql_query($stmt, $link);
	$events_to_print = mysql_num_rows($rslt);

	$total_logs=0;
	$o=0;
	while ($events_to_print > $o) {
		$row=mysql_fetch_row($rslt);
		if ( ($row[0]=='START') or ($row[0]=='LOGIN') )
			{$bgcolor='bgcolor="#B9CBFD"';} 
		else
			{$bgcolor='bgcolor="#9BB9FB"';}

		$TC_log_date = date("Y-m-d H:i:s", $row[1]);

		$manager_edit='';
		if (strlen($row[6])>0) {$manager_edit = ' * ';}

		if (ereg("LOGIN", $row[0]))
			{
			$login_sec='';
			echo "<tr $bgcolor><td><font size=2>$row[5]</td>";
			echo "<td align=right><font size=2>$manager_edit</td>";
			echo "<td align=right><font size=2>$row[0]</td>";
			echo "<td align=right><font size=2> $TC_log_date</td>\n";
			echo "<td align=right><font size=2> $row[4]</td>\n";
			echo "<td align=right><font size=2> $row[2]</td>\n";
			echo "<td align=right><font size=2> </td></tr>\n";
			}
		if (ereg("LOGOUT", $row[0]))
			{
			$login_sec = $row[3];
			$total_login_time = ($total_login_time + $login_sec);
			$event_hours = ($login_sec / 3600);
			$event_hours_int = round($event_hours, 2);
			$event_hours_int = intval("$event_hours_int");
			$event_minutes = ($event_hours - $event_hours_int);
			$event_minutes = ($event_minutes * 60);
			$event_minutes_int = round($event_minutes, 0);
			if ($event_minutes_int < 10) {$event_minutes_int = "0$event_minutes_int";}
			echo "<tr $bgcolor><td><font size=2>$row[5]</td>";
			echo "<td align=right><font size=2>$manager_edit</td>";
			echo "<td align=right><font size=2>$row[0]</td>";
			echo "<td align=right><font size=2> $TC_log_date</td>\n";
			echo "<td align=right><font size=2> $row[4]</td>\n";
			echo "<td align=right><font size=2> $row[2]</td>\n";
			echo "<td align=right><font size=2> $event_hours_int:$event_minutes_int</td></tr>\n";
			}
		$o++;
	}
if (strlen($login_sec)<1)
	{
	$login_sec = ($STARTtime - $row[1]);
	$total_login_time = ($total_login_time + $login_sec);
	}
$total_login_hours = ($total_login_time / 3600);
$total_login_hours_int = round($total_login_hours, 2);
$total_login_hours_int = intval("$total_login_hours");
$total_login_minutes = ($total_login_hours - $total_login_hours_int);
$total_login_minutes = ($total_login_minutes * 60);
$total_login_minutes_int = round($total_login_minutes, 0);
if ($total_login_minutes_int < 10) {$total_login_minutes_int = "0$total_login_minutes_int";}

echo "<tr><td align=right><font size=2> </td>";
echo "<td align=right><font size=2> </td>\n";
echo "<td align=right><font size=2> </td>\n";
echo "<td align=right><font size=2> </td>\n";
echo "<td align=right><font size=2><font size=2>TOTAL </td>\n";
echo "<td align=right><font size=2> $total_login_hours_int:$total_login_minutes_int  </td></tr>\n";

echo "</TABLE></center>\n";







$ENDtime = date("U");

$RUNtime = ($ENDtime - $STARTtime);

echo "\n\n\n<br><br><br>\n\n";


echo "<font size=0>\n\n\n<br><br><br>\nScript runtime: $RUNtime seconds</font>";


?>


</TD></TR><TABLE>
</body>
</html>

<?php
	
exit; 



?>





