<?php
# admin_phones_bulk_insert.php
# 
# Copyright (C) 2010  Matt Florell <vicidial@gmail.com>    LICENSE: AGPLv2
#
# this screen will insert phones into your multi-server system with aliases
#
# changes:
# 101230-0501 - First Build
#

$admin_version = '2.4-1';
$build = '101230-0501';


require("dbconnect.php");

$PHP_AUTH_USER=$_SERVER['PHP_AUTH_USER'];
$PHP_AUTH_PW=$_SERVER['PHP_AUTH_PW'];
$PHP_SELF=$_SERVER['PHP_SELF'];
if (isset($_GET["DB"]))							{$DB=$_GET["DB"];}
	elseif (isset($_POST["DB"]))				{$DB=$_POST["DB"];}
if (isset($_GET["action"]))						{$action=$_GET["action"];}
	elseif (isset($_POST["action"]))			{$action=$_POST["action"];}
if (isset($_GET["servers"]))					{$servers=$_GET["servers"];}
	elseif (isset($_POST["servers"]))			{$servers=$_POST["servers"];}
if (isset($_GET["phones"]))						{$phones=$_GET["phones"];}
	elseif (isset($_POST["phones"]))			{$phones=$_POST["phones"];}
if (isset($_GET["conf_secret"]))				{$conf_secret=$_GET["conf_secret"];}
	elseif (isset($_POST["conf_secret"]))		{$conf_secret=$_POST["conf_secret"];}
if (isset($_GET["pass"]))						{$pass=$_GET["pass"];}
	elseif (isset($_POST["pass"]))				{$pass=$_POST["pass"];}
if (isset($_GET["alias_option"]))				{$alias_option=$_GET["alias_option"];}
	elseif (isset($_POST["alias_option"]))		{$alias_option=$_POST["alias_option"];}
if (isset($_GET["protocol"]))					{$protocol=$_GET["protocol"];}
	elseif (isset($_POST["protocol"]))			{$protocol=$_POST["protocol"];}
if (isset($_GET["local_gmt"]))					{$local_gmt=$_GET["local_gmt"];}
	elseif (isset($_POST["local_gmt"]))			{$local_gmt=$_POST["local_gmt"];}
if (isset($_GET["alias_suffix"]))				{$alias_suffix=$_GET["alias_suffix"];}
	elseif (isset($_POST["alias_suffix"]))		{$alias_suffix=$_POST["alias_suffix"];}
if (isset($_GET["SUBMIT"]))						{$SUBMIT=$_GET["SUBMIT"];}
	elseif (isset($_POST["SUBMIT"]))			{$SUBMIT=$_POST["SUBMIT"];}


if (strlen($action) < 2)
	{$action = 'BLANK';}
if (strlen($DB) < 1)
	{$DB=0;}


if ($non_latin < 1)
	{
	$PHP_AUTH_USER = ereg_replace("[^-_0-9a-zA-Z]","",$PHP_AUTH_USER);
	$PHP_AUTH_PW = ereg_replace("[^-_0-9a-zA-Z]","",$PHP_AUTH_PW);

	$servers = ereg_replace("'|\"|\\\\|;","",$servers);
	$phones = ereg_replace("'|\"|\\\\|;","",$phones);
	$action = ereg_replace("[^-_0-9a-zA-Z]","",$action);
	$conf_secret = ereg_replace("[^-_0-9a-zA-Z]","",$conf_secret);
	$pass = ereg_replace("[^-_0-9a-zA-Z]","",$pass);
	$alias_option = ereg_replace("[^-_0-9a-zA-Z]","",$alias_option);
	$alias_suffix = ereg_replace("[^0-9a-zA-Z]","",$alias_suffix);
	$protocol = ereg_replace("[^-_0-9a-zA-Z]","",$protocol);
	$local_gmt = ereg_replace("[^ \.\,-\_0-9a-zA-Z]","",$local_gmt);
	}	# end of non_latin
else
	{
	$PHP_AUTH_USER = ereg_replace("'|\"|\\\\|;","",$PHP_AUTH_USER);
	$PHP_AUTH_PW = ereg_replace("'|\"|\\\\|;","",$PHP_AUTH_PW);
	}

$STARTtime = date("U");
$TODAY = date("Y-m-d");
$NOW_TIME = date("Y-m-d H:i:s");

#############################################
##### START SYSTEM_SETTINGS LOOKUP #####
$stmt = "SELECT use_non_latin FROM system_settings;";
$rslt=mysql_query($stmt, $link);
if ($DB) {echo "$stmt\n";}
$ss_conf_ct = mysql_num_rows($rslt);
if ($ss_conf_ct > 0)
	{
	$row=mysql_fetch_row($rslt);
	$non_latin =						$row[0];
	}
##### END SETTINGS LOOKUP #####
###########################################


$stmt="SELECT count(*) from vicidial_users where user='$PHP_AUTH_USER' and pass='$PHP_AUTH_PW' and user_level > 7 and ast_delete_phones='1';";
if ($DB) {echo "|$stmt|\n";}
$rslt=mysql_query($stmt, $link);
$row=mysql_fetch_row($rslt);
$auth=$row[0];

if ($WeBRooTWritablE > 0)
	{$fp = fopen ("./project_auth_entries.txt", "a");}

$date = date("r");
$ip = getenv("REMOTE_ADDR");
$browser = getenv("HTTP_USER_AGENT");
$user = $PHP_AUTH_USER;

if( (strlen($PHP_AUTH_USER)<2) or (strlen($PHP_AUTH_PW)<2) or (!$auth))
	{
    Header("WWW-Authenticate: Basic realm=\"VICI-PROJECTS\"");
    Header("HTTP/1.0 401 Unauthorized");
    echo "Invalid Username/Password: |$PHP_AUTH_USER|$PHP_AUTH_PW|\n";
    exit;
	}
else
	{
	if ($auth>0)
		{
		$office_no=strtoupper($PHP_AUTH_USER);
		$password=strtoupper($PHP_AUTH_PW);
		$stmt="SELECT full_name,ast_delete_phones,ast_admin_access,user_level from vicidial_users where user='$PHP_AUTH_USER' and pass='$PHP_AUTH_PW'";
		$rslt=mysql_query($stmt, $link);
		$row=mysql_fetch_row($rslt);
		$LOGfullname =				$row[0];
		$LOGast_delete_phones =		$row[1];
		$LOGast_admin_access =		$row[2];
		$LOGuser_level =			$row[3];

		if ($WeBRooTWritablE > 0)
			{
			fwrite ($fp, "VICIDIAL|GOOD|$date|$PHP_AUTH_USER|$PHP_AUTH_PW|$ip|$browser|$LOGfullname|\n");
			fclose($fp);
			}
		}
	else
		{
		if ($WeBRooTWritablE > 0)
			{
			fwrite ($fp, "VICIDIAL|FAIL|$date|$PHP_AUTH_USER|$PHP_AUTH_PW|$ip|$browser|\n");
			fclose($fp);
			}
		}
	}

?>
<html>
<head>

<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=utf-8">
<title>ADMINISTRATION: Phones Bulk Insert
<?php 


##### BEGIN Set variables to make header show properly #####
$ADD =					'999998';
$hh =					'admin';
$LOGast_admin_access =	'1';
$SSoutbound_autodial_active = '1';
$ADMIN =				'admin.php';
$page_width='770';
$section_width='750';
$header_font_size='3';
$subheader_font_size='2';
$subcamp_font_size='2';
$header_selected_bold='<b>';
$header_nonselected_bold='';
$admin_color =		'#FFFF99';
$admin_font =		'BLACK';
$admin_color =		'#E6E6E6';
$subcamp_color =	'#C6C6C6';
##### END Set variables to make header show properly #####

require("admin_header.php");

if ( ($LOGast_admin_access < 1) or ($LOGuser_level < 8) )
	{
	echo "You are not authorized to view this section\n";
	exit;
	}


if ($DB > 0)
{
echo "$DB,$action,$servers,$phones,$conf_secret,$pass,$alias_option,$protocol,$logal_gmt,$alias_suffix\n<BR>";
}





################################################################################
##### BEGIN blank add phones form
if ($action == "BLANK")
	{
	echo "<TABLE><TR><TD>\n";
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";
	echo "<br>Add Multi-Server Phones Form<form action=$PHP_SELF method=POST>\n";
	echo "<input type=hidden name=DB value=\"$DB\">\n";
	echo "<input type=hidden name=action value=ADD_PHONES_SUBMIT>\n";
	echo "<center><TABLE width=$section_width cellspacing=3>\n";
	echo "<tr bgcolor=#B6D3FC><td align=right>Servers: <BR><BR> (one server_ip per line only)<BR></td><td align=left><TEXTAREA name=servers ROWS=10 COLS=20></TEXTAREA></td></tr>\n";
	echo "<tr bgcolor=#B6D3FC><td align=right>Phones: <BR><BR> (one extension per line only)<BR></td><td align=left><TEXTAREA name=phones ROWS=20 COLS=20></TEXTAREA></td></tr>\n";
	echo "<tr bgcolor=#B6D3FC><td align=right>Registration Password: </td><td align=left><input type=text name=conf_secret size=20 maxlength=20></td></tr>\n";
	echo "<tr bgcolor=#B6D3FC><td align=right>Login Password: </td><td align=left><input type=text name=pass size=20 maxlength=20></td></tr>\n";
	echo "<tr bgcolor=#B6D3FC><td align=right>Create Alias Entries: </td><td align=left><select size=1 name=alias_option>\n";
	echo "<option selected>YES</option>";
	echo "<option>NO</option>";
	echo "</select></td></tr>\n";
	echo "<tr bgcolor=#B6D3FC><td align=right>Alias Suffix: </td><td align=left><input type=text name=alias_suffix size=2 maxlength=4></td></tr>\n";
	echo "<tr bgcolor=#B6D3FC><td align=right>Client Protocol: </td><td align=left><select size=1 name=protocol><option>SIP</option><option>Zap</option><option>IAX2</option><option>EXTERNAL</option></select></td></tr>\n";
	echo "<tr bgcolor=#B6D3FC><td align=right>Local GMT: </td><td align=left><select size=1 name=local_gmt><option>12.75</option><option>12.00</option><option>11.00</option><option>10.00</option><option>9.50</option><option>9.00</option><option>8.00</option><option>7.00</option><option>6.50</option><option>6.00</option><option>5.75</option><option>5.50</option><option>5.00</option><option>4.50</option><option>4.00</option><option>3.50</option><option>3.00</option><option>2.00</option><option>1.00</option><option>0.00</option><option>-1.00</option><option>-2.00</option><option>-3.00</option><option>-3.50</option><option>-4.00</option><option selected>-5.00</option><option>-6.00</option><option>-7.00</option><option>-8.00</option><option>-9.00</option><option>-10.00</option><option>-11.00</option><option>-12.00</option></select> (Do NOT Adjust for DST)</td></tr>\n";
	echo "<tr bgcolor=#B6D3FC><td align=center colspan=2><input type=submit name=SUBMIT value=SUBMIT></td></tr>\n";
	echo "</TABLE></center>\n";
	echo "</TD></TR></TABLE>\n";
	}
### END blank add phones form




################################################################################
##### BEGIN add phones submit
if ($action == "ADD_PHONES_SUBMIT")
	{
	$phones_inserted=0;
	$phone_alias_inserted=0;
	if (strlen($phones) > 2)
		{
		$PN = explode("\n",$phones);
		$PNct = count($PN);

		if (strlen($servers) > 6)
			{
			$SN = explode("\n",$servers);
			$SNct = count($SN);

			$s=0;
			while ($s < $SNct)
				{
				$SN[$s] = preg_replace('/\n|\r|\t| /','',$SN[$s]);
				$server_exists=0;
				$stmt="SELECT count(*) from servers where server_ip='$SN[$s]';";
				if ($DB>0) {echo "$stmt";}
				$rslt=mysql_query($stmt, $link);
				$servercount_to_print = mysql_num_rows($rslt);
				if ($servercount_to_print > 0) 
					{
					$rowx=mysql_fetch_row($rslt);
					$server_exists =	$rowx[0];
					}
				if ($server_exists > 0)
					{
					$p=0;
					while ($p < $PNct)
						{
						$PN[$p] = preg_replace('/\n|\r|\t| /','',$PN[$p]);
						$phone_exists=0;
						$stmt="SELECT count(*) from phones where server_ip='$SN[$s]' and extension='$PN[$p]';";
						if ($DB>0) {echo "$stmt";}
						$rslt=mysql_query($stmt, $link);
						$phonecount_to_print = mysql_num_rows($rslt);
						if ($phonecount_to_print > 0) 
							{
							$rowx=mysql_fetch_row($rslt);
							$phone_exists =	$rowx[0];
							}
						if ( ($phone_exists < 1) and (strlen($PN[$p]) > 1) )
							{
							if ($s < 1) {$dialplan_prefix='';   $login_suffix = 'a';}
							else {$dialplan_prefix = $s;}
							if ($s == '1') {$login_suffix = 'b';}
							if ($s == '2') {$login_suffix = 'c';}
							if ($s == '3') {$login_suffix = 'd';}
							if ($s == '4') {$login_suffix = 'e';}
							if ($s == '5') {$login_suffix = 'f';}
							if ($s == '6') {$login_suffix = 'g';}
							if ($s == '7') {$login_suffix = 'h';}
							if ($s == '8') {$login_suffix = 'i';}
							if ($s == '9') {$login_suffix = 'j';}
							if ($s == '10') {$login_suffix = 'k';}
							if ($s == '11') {$login_suffix = 'l';}
							if ($s == '12') {$login_suffix = 'm';}
							if ($s == '13') {$login_suffix = 'n';}
							if ($s == '14') {$login_suffix = 'o';}
							if ($s == '15') {$login_suffix = 'p';}
							if ($s == '16') {$login_suffix = 'q';}
							if ($s == '17') {$login_suffix = 'r';}
							if ($s == '18') {$login_suffix = 's';}
							if ($s == '19') {$login_suffix = 't';}
							if ($s == '20') {$login_suffix = 'u';}
							if ($s == '21') {$login_suffix = 'v';}
							if ($s == '22') {$login_suffix = 'w';}
							if ($s == '23') {$login_suffix = 'x';}
							if ($s == '24') {$login_suffix = 'y';}
							if ($s == '25') {$login_suffix = 'z';}
							if ($s == '26') {$login_suffix = 'aa';}
							if ($s == '27') {$login_suffix = 'ab';}
							if ($s == '28') {$login_suffix = 'ac';}
							if ($s == '29') {$login_suffix = 'ad';}
							if ($s == '30') {$login_suffix = 'ae';}
							if ($s == '31') {$login_suffix = 'af';}
							if ($s == '32') {$login_suffix = 'ag';}
							if ($s == '33') {$login_suffix = 'ah';}
							if ($s == '34') {$login_suffix = 'ai';}
							if ($s == '35') {$login_suffix = 'aj';}
							if ($s == '36') {$login_suffix = 'ak';}
							if ($s == '37') {$login_suffix = 'al';}
							if ($s == '38') {$login_suffix = 'am';}
							if ($s == '39') {$login_suffix = 'an';}
							if ($s == '40') {$login_suffix = 'ao';}
							if ($s == '41') {$login_suffix = 'ap';}
							if ($s == '42') {$login_suffix = 'aq';}
							if ($s == '43') {$login_suffix = 'ar';}
							if ($s == '44') {$login_suffix = 'as';}
							if ($s == '45') {$login_suffix = 'at';}
							if ($s == '46') {$login_suffix = 'au';}
							if ($s == '47') {$login_suffix = 'av';}
							if ($s == '48') {$login_suffix = 'aw';}
							if ($s >= 49) {$login_suffix = 'ax';}

							$extension =		$PN[$p];
							$dialplan_number =	"$dialplan_prefix$PN[$p]";	$dialplan_number = preg_replace('/\D/', '', $dialplan_number);
							$voicemail_id =		$PN[$p];	$voicemail_id = preg_replace('/\D/', '', $voicemail_id);
							$phone_server_ip =	$SN[$s];
							$login =			"$PN[$p]$login_suffix";
							$phone_type =		"CCagent";
							$fullname =			"ext $PN[$p]";

							$stmt = "INSERT INTO phones (extension,dialplan_number,voicemail_id,server_ip,login,pass,status,active,phone_type,fullname,protocol,local_gmt,outbound_cid,conf_secret) values('$extension','$dialplan_number','$voicemail_id','$phone_server_ip','$login','$pass','ACTIVE','Y','$phone_type','$fullname','$protocol','$local_gmt','0000000000','$conf_secret');";
							$rslt=mysql_query($stmt, $link);
							$affected_rows = mysql_affected_rows($link);
							if ($DB > 0) {echo "$s|$p|$SN[$s]|$PN[$p]|$affected_rows|$stmt\n<BR>";}

							if ($affected_rows > 0)
								{
								$phone_alias_entry[$p] .= "$login,";

								### LOG INSERTION Admin Log Table ###
								$SQL_log = "$stmt|";
								$SQL_log = ereg_replace(';','',$SQL_log);
								$SQL_log = addslashes($SQL_log);
								$stmt="INSERT INTO vicidial_admin_log set event_date='$SQLdate', user='$PHP_AUTH_USER', ip_address='$ip', event_section='PHONES', event_type='ADD', record_id='$PN[$p]', event_code='ADMIN BULK ADD PHONE', event_sql=\"$SQL_log\", event_notes='$SN[$s]|$PN[$p]';";
							#	if ($DB) {echo "|$stmt|\n";}
								$rslt=mysql_query($stmt, $link);

								$phones_inserted++;
								}
							else
								{echo "ERROR: Problem inserting phone:  $affected_rows|$stmt\n<BR>";}
							}
						else
							{echo "ERROR: Phone already exists:  $SN[$s]|$PN[$p]|$phone_exists\n<BR>";}
						$p++;
						}
					}
				else
					{echo "ERROR: Server does not exist: $SN[$s]|$server_exists\n<BR>";}
				$s++;
				}

			if ( ($phones_inserted > 0) and ($alias_option == 'YES') )
				{
				$p=0;
				while ($p < $PNct)
					{
					if ( (strlen($phone_alias_entry[$p]) > 1) and (strlen($PN[$p]) > 1) )
						{
						$phone_alias_entry[$p] = preg_replace('/,$/','',$phone_alias_entry[$p]);

						$stmt="INSERT INTO phones_alias (alias_id,alias_name,logins_list) values('$PN[$p]$alias_suffix','$PN[$p]','$phone_alias_entry[$p]');";
						$rslt=mysql_query($stmt, $link);
						$affected_rows = mysql_affected_rows($link);
						if ($DB > 0) {echo "$p|$phone_alias_entry[$p]|$PN[$p]|$affected_rows|$stmt\n<BR>";}

						if ($affected_rows > 0)
							{
							### LOG INSERTION Admin Log Table ###
							$SQL_log = "$stmt|";
							$SQL_log = ereg_replace(';','',$SQL_log);
							$SQL_log = addslashes($SQL_log);
							$stmt="INSERT INTO vicidial_admin_log set event_date='$SQLdate', user='$PHP_AUTH_USER', ip_address='$ip', event_section='PHONEALIASES', event_type='ADD', record_id='$alias_id', event_code='ADMIN ADD BULK PHONE ALIAS', event_sql=\"$SQL_log\", event_notes='';";
						#	if ($DB) {echo "|$stmt|\n";}
							$rslt=mysql_query($stmt, $link);

							$phone_alias_inserted++;
							}
						else
							{echo "ERROR: Problem inserting phone alias:  $affected_rows|$stmt\n<BR>";}
						}
					$p++;
					}
				}
			
			echo "Phones Inserted: $phones_inserted\n<BR>";
			echo "Phones Aliases Inserted: $phone_alias_inserted\n<BR>";
			echo "<BR><a href=\"$PHP_SELF\">Start Over</a><BR>\n";
			}
		else
			{echo "ERROR: You must enter servers: $servers\n<BR>";}
		}
	else
		{echo "ERROR: You must enter extensions: $phones\n<BR>";}
	}
### END add phones submit







$ENDtime = date("U");
$RUNtime = ($ENDtime - $STARTtime);
echo "\n\n\n<br><br><br>\n<font size=1> runtime: $RUNtime seconds &nbsp; &nbsp; &nbsp; &nbsp; Version: $admin_version &nbsp; &nbsp; Build: $build</font>";

?>

</body>
</html>
