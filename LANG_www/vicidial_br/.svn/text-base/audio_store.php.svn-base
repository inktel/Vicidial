<?php
# audio_store.php
# 
# Copyright (C) 2010  Matt Florell <vicidial@gmail.com>    LICENSE: AGPLv2
#
# Central Audio Storage script
# 
# CHANGES
# 90511-1325 - First build
# 90618-0640 - Fix for users going through proxy or tunnel
# 100401-1037 - remove spaces and special characters from filenames, admin log uploads
#

$version = '2.2.0-3';
$build = '100401-1037';

$MT[0]='';

require("dbconnect.php");

$server_name = getenv("SERVER_NAME");
$PHP_SELF=$_SERVER['PHP_SELF'];
$audiofile=$_FILES["audiofile"];
	$AF_orig = $_FILES['audiofile']['name'];
	$AF_path = $_FILES['audiofile']['tmp_name'];
if (isset($_GET["submit_file"]))			{$submit_file=$_GET["submit_file"];}
	elseif (isset($_POST["submit_file"]))	{$submit_file=$_POST["submit_file"];}
if (isset($_GET["DB"]))						{$DB=$_GET["DB"];}
	elseif (isset($_POST["DB"]))			{$DB=$_POST["DB"];}
if (isset($_GET["overwrite"]))				{$overwrite=$_GET["overwrite"];}
	elseif (isset($_POST["overwrite"]))		{$overwrite=$_POST["overwrite"];}
if (isset($_GET["action"]))					{$action=$_GET["action"];}
	elseif (isset($_POST["action"]))		{$action=$_POST["action"];}
if (isset($_GET["audio_server_ip"]))			{$audio_server_ip=$_GET["audio_server_ip"];}
	elseif (isset($_POST["audio_server_ip"]))	{$audio_server_ip=$_POST["audio_server_ip"];}
if (isset($_GET["SUBMIT"]))					{$SUBMIT=$_GET["SUBMIT"];}
	elseif (isset($_POST["SUBMIT"]))		{$SUBMIT=$_POST["SUBMIT"];}
if (isset($_GET["audiofile_name"]))				{$audiofile_name=$_GET["audiofile_name"];}
	elseif (isset($_POST["audiofile_name"]))	{$audiofile_name=$_POST["audiofile_name"];}
if (isset($_FILES["audiofile"]))			{$audiofile_name=$_FILES["audiofile"]['name'];}
if (isset($_GET["lead_file"]))				{$lead_file=$_GET["lead_file"];}
	elseif (isset($_POST["lead_file"]))		{$lead_file=$_POST["lead_file"];}


header ("Content-type: text/html; charset=utf-8");
header ("Cache-Control: no-cache, must-revalidate");  // HTTP/1.1
header ("Pragma: no-cache");                          // HTTP/1.0

#############################################
##### START SYSTEM_SETTINGS LOOKUP #####
$stmt = "SELECT use_non_latin,sounds_central_control_active,sounds_web_server,sounds_web_directory,outbound_autodial_active FROM system_settings;";
$rslt=mysql_query($stmt, $link);
if ($DB) {echo "$stmt\n";}
$ss_conf_ct = mysql_num_rows($rslt);
if ($ss_conf_ct > 0)
	{
	$row=mysql_fetch_row($rslt);
	$non_latin =						$row[0];
	$sounds_central_control_active =	$row[1];
	$sounds_web_server =				$row[2];
	$sounds_web_directory =				$row[3];
	$SSoutbound_autodial_active =		$row[4];
	}
##### END SETTINGS LOOKUP #####
###########################################


### check if sounds server matches this server IP, if not then exit with an error
if ( ( (strlen($sounds_web_server)) != (strlen($server_name)) ) or (!eregi("$sounds_web_server",$server_name) ) )
	{
	echo "ERROR: server($server_name) does not match sounds web server ip($sounds_web_server)\n";
	exit;
	}


### check if web directory exists, if not generate one
if (strlen($sounds_web_directory) < 30)
	{
	$sounds_web_directory = '';
	$possible = "0123456789cdfghjkmnpqrstvwxyz";  
	$i = 0; 
	$length = 30;
	while ($i < $length) 
		{ 
		$char = substr($possible, mt_rand(0, strlen($possible)-1), 1);
		$sounds_web_directory .= $char;
		$i++;
		}
	mkdir("$WeBServeRRooT/$sounds_web_directory");
	chmod("$WeBServeRRooT/$sounds_web_directory", 0766);
	if ($DB > 0) {echo "$WeBServeRRooT/$sounds_web_directory\n";}

	$stmt="UPDATE system_settings set sounds_web_directory='$sounds_web_directory';";
	$rslt=mysql_query($stmt, $link);
	echo "NOTICE: new web directory created\n";
	}


### get list of all servers, if not one of them, then force authentication check
$stmt = "SELECT server_ip FROM servers;";
$rslt=mysql_query($stmt, $link);
if ($DB) {echo "$stmt\n";}
$sv_conf_ct = mysql_num_rows($rslt);
$i=0;
$server_ips ='|';
while ($sv_conf_ct > $i)
	{
	$row=mysql_fetch_row($rslt);
	$server_ips .=	"$row[0]|";
	$i++;
	}

$user_set=0;
$formIPvalid=0;
if (strlen($audio_server_ip) > 6)
	{
	if (preg_match("/\|$audio_server_ip\|/", $server_ips))
		{$formIPvalid=1;}
	}
$ip = getenv("REMOTE_ADDR");
if ( (!preg_match("/\|$ip\|/", $server_ips)) and ($formIPvalid < 1) )
	{
	$user_set=1;
	$PHP_AUTH_USER=$_SERVER['PHP_AUTH_USER'];
	$PHP_AUTH_PW=$_SERVER['PHP_AUTH_PW'];
	$PHP_AUTH_USER = ereg_replace("[^-_0-9a-zA-Z]","",$PHP_AUTH_USER);
	$PHP_AUTH_PW = ereg_replace("[^-_0-9a-zA-Z]","",$PHP_AUTH_PW);

	$stmt="SELECT count(*) from vicidial_users where user='$PHP_AUTH_USER' and pass='$PHP_AUTH_PW' and user_level > 7 and modify_campaigns='1'";
	if ($DB) {echo "|$stmt|\n";}
	$rslt=mysql_query($stmt, $link);
	$row=mysql_fetch_row($rslt);
	$auth=$row[0];

	if( (strlen($PHP_AUTH_USER)<2) or (strlen($PHP_AUTH_PW)<2) or (!$auth))
		{
		Header("WWW-Authenticate: Basic realm=\"VICI-PROJECTS\"");
		Header("HTTP/1.0 401 Unauthorized");
		echo "Invalid Username/Password: |$PHP_AUTH_USER|$PHP_AUTH_PW|$ip|\n";
		exit;
		}
	}


### list all files in sounds web directory
if ($action == "LIST")
	{
	$i=0;
	$filename_sort=$MT;
	$dirpath = "$WeBServeRRooT/$sounds_web_directory";
	$dh = opendir($dirpath);
	while (false !== ($file = readdir($dh))) 
		{
		# Do not list subdirectories
		if ( (!is_dir("$dirpath/$file")) and (preg_match('/\.wav$|\.gsm$/', $file)) )
			{
			if (file_exists("$dirpath/$file")) 
				{
				$file_names[$i] = $file;
				$file_epoch[$i] = filemtime("$dirpath/$file");
				$file_dates[$i] = date ("Y-m-d H:i:s.", filemtime("$dirpath/$file"));
				$file_sizes[$i] = filesize("$dirpath/$file");
				$filename_sort[$i] = $file . "----------" . $i . "----------" . $file_sizes[$i];
				$i++;
				}
			}
		}
	closedir($dh);

	sort($filename_sort);

	sleep(1);

	$k=0;
	while($k < $i)
		{
		$filename_split = explode('----------',$filename_sort[$k]);
		$m = $filename_split[1];
		$size = $filename_split[2];
		$NOWsize = filesize("$dirpath/$file_names[$m]");
		if ($size == $NOWsize)
			{
			echo "$k\t$file_names[$m]\t$file_dates[$m]\t$file_sizes[$m]\t$file_epoch[$m]\n";
			}
		$k++;
		}
	exit;
	}


### upload audio file from server to webserver
# curl 'http://10.0.0.4/vicidial/audio_store.php?action=AUTOUPLOAD' -F "audiofile=@/var/lib/asterisk/sounds/beep.gsm"
if ($action == "AUTOUPLOAD")
	{
	if ($audiofile)
		{
		$AF_path = preg_replace("/ /",'\ ',$AF_path);
		$AF_path = preg_replace("/@/",'\@',$AF_path);
		$audiofile_name = preg_replace("/ /",'',$audiofile_name);
		$audiofile_name = preg_replace("/@/",'',$audiofile_name);
		copy($AF_path, "$WeBServeRRooT/$sounds_web_directory/$audiofile_name");
		chmod("$WeBServeRRooT/$sounds_web_directory/$audiofile_name", 0766);

		echo "SUCCESS: $audiofile_name uploaded     size:" . filesize("$WeBServeRRooT/$sounds_web_directory/$audiofile_name") . "\n";
		exit;
		}
	else
		{
		echo "ERROR: no file uploaded\n";
		}
	exit;
	}




?>
<html>
<head>
<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=utf-8">
<!-- VERSION: <?php echo $version ?>     BUILD: <?php echo $build ?> -->
<title>ADMINISTRATION: Audio Store
<?php


if ($user_set < 1)
	{
	$PHP_AUTH_USER=$_SERVER['PHP_AUTH_USER'];
	$PHP_AUTH_PW=$_SERVER['PHP_AUTH_PW'];
	$PHP_AUTH_USER = ereg_replace("[^-_0-9a-zA-Z]","",$PHP_AUTH_USER);
	$PHP_AUTH_PW = ereg_replace("[^-_0-9a-zA-Z]","",$PHP_AUTH_PW);
	}
##### BEGIN Set variables to make header show properly #####
$ADD =					'311111111111111';
$hh =					'admin';
$LOGast_admin_access =	'1';
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

?>
<TABLE WIDTH=<?php echo $page_width ?> BGCOLOR=#E6E6E6 cellpadding=2 cellspacing=0><TR BGCOLOR=#E6E6E6><TD ALIGN=LEFT><FONT FACE="ARIAL,HELVETICA" SIZE=2><B> &nbsp; Audio Store</TD><TD ALIGN=RIGHT><FONT FACE="ARIAL,HELVETICA" SIZE=2><B> &nbsp; </TD></TR>

<?php 

echo "<TR BGCOLOR=\"#F0F5FE\"><TD ALIGN=LEFT COLSPAN=2><FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=3><B> &nbsp; \n";

$STARTtime = date("U");
$TODAY = date("Y-m-d");
$NOW_TIME = date("Y-m-d H:i:s");
$FILE_datetime = $STARTtime;

$date = date("r");
$browser = getenv("HTTP_USER_AGENT");
$script_name = getenv("SCRIPT_NAME");
$server_name = getenv("SERVER_NAME");
$server_port = getenv("SERVER_PORT");
if (eregi("443",$server_port)) {$HTTPprotocol = 'https://';}
  else {$HTTPprotocol = 'http://';}
$admDIR = "$HTTPprotocol$server_name:$server_port$script_name";
$admDIR = eregi_replace('audio_store.php','',$admDIR);
$admSCR = 'admin.php';
$NWB = " &nbsp; <a href=\"javascript:openNewWindow('$admDIR$admSCR?ADD=99999";
$NWE = "')\"><IMG SRC=\"help.gif\" WIDTH=20 HEIGHT=20 BORDER=0 ALT=\"HELP\" ALIGN=TOP></A>";

$secX = date("U");
$pulldate0 = "$year-$mon-$mday $hour:$min:$sec";

if ($action == "MANUALUPLOAD")
	{
	if ($audiofile) 
		{
		$AF_path = preg_replace("/ /",'\ ',$AF_path);
		$AF_path = preg_replace("/@/",'\@',$AF_path);
		$audiofile_name = preg_replace("/ /",'',$audiofile_name);
		$audiofile_name = preg_replace("/@/",'',$audiofile_name);
		copy($AF_path, "$WeBServeRRooT/$sounds_web_directory/$audiofile_name");
		chmod("$WeBServeRRooT/$sounds_web_directory/$audiofile_name", 0766);
		
		echo "SUCCESS: $audiofile_name uploaded     size:" . filesize("$WeBServeRRooT/$sounds_web_directory/$audiofile_name") . "\n";

		$stmt="UPDATE servers SET sounds_update='Y';";
		$rslt=mysql_query($stmt, $link);

		### LOG INSERTION Admin Log Table ###
		$SQL_log = "$stmt|";
		$SQL_log = ereg_replace(';','',$SQL_log);
		$SQL_log = addslashes($SQL_log);
		$stmt="INSERT INTO vicidial_admin_log set event_date=NOW(), user='$PHP_AUTH_USER', ip_address='$ip', event_section='AUDIOSTORE', event_type='LOAD', record_id='manualupload', event_code='$audiofile_name " . filesize("$WeBServeRRooT/$sounds_web_directory/$audiofile_name") . "', event_sql=\"$SQL_log\", event_notes='$audiofile_name $AF_path $AF_orig';";
		if ($DB) {echo "|$stmt|\n";}
		$rslt=mysql_query($stmt, $link);
		}
	else
		{
		echo "ERROR: no file uploaded\n";
		}
	}

?>


<form action=<?php echo $PHP_SELF ?> method=post enctype="multipart/form-data">
<input type=hidden name=action value="MANUALUPLOAD">
<input type=hidden name=sample_prompt id=sample_prompt value="">

<table align=center width="700" border=0 cellpadding=5 cellspacing=0 bgcolor=#D9E6FE>
  <tr>
	<td align=right width="35%"><B><font face="arial, helvetica" size=2>Audio File to Upload:</font></B></td>
	<td align=left width="65%"><input type=file name="audiofile" value=""> <?php echo "$NWB#audio_store$NWE"; ?></td>
  </tr>
  <tr>
	<td align=center colspan=2><input type=submit name=submit value=submit></td>
  </tr>
  <tr><td align=left><font size=1> &nbsp; </font></td><td align=right><font size=1>Audio Store- &nbsp; &nbsp; VERSION: <?php echo $version ?> &nbsp; &nbsp; BUILD: <?php echo $build ?> &nbsp; &nbsp; </td></tr>
</table>
<BR><BR>
<CENTER><B>We STRONGLY recommend uploading only 16bit 8k PCM WAV audio files(.wav)</B>
<BR><BR><font size=1>All spaces will be stripped from uploaded audio file names</font><BR><BR>
<B><a href="javascript:launch_chooser('sample_prompt','date',30);">audio file list</a></CENTER>



<?php

echo "<BR><BR><BR><BR><BR><BR>\n";

echo "</B></B><br><br><a href=\"admin.php?ADD=720000000000000&category=AUDIOSTORE&stage=manualupload\">Click here to see a log of the uploads to the audio store</FONT>\n";

?>

</TD></TR></TABLE>
