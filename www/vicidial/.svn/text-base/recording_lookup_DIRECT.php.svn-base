<?php
### recording_lookup_DIRECT.php
#
#	REQUIRED! - check all paths and directory names, need to create a temp directory
#
#	On the normal audio recording interface you now have the option of
#	downloading the WAV or GSM file:
#	http://1.1.1.1/vicidial/recording_lookup.php
#	 - user/pass: VDC
#
#	I have also added a new download page that works by query string(URL)
#	only. Simply goto and address like this one and the audio will
#	download immediately:
#	http://1.1.1.1/vicidial/recording_lookup_DIRECT.php?phone=7275551212&format=GSM&auth=VDC1234593JH654398722
#
#	The variables are "phone", "format" and "auth".
#	 - phone: 10 digit phone number of the customer
#	 - format: either GSM or WAV
#	 - auth: should always be VDC1234593JH654398722
#
#	This should work well for a direct link from your CRM system, or
#	possibly through a bulk downloading script on your side.
#
#
### remove temp files
# 1 7 * * * /usr/bin/find /usr/local/apache2/htdocs/vicidial/temp/ -maxdepth 1 -type f -mtime +1 -print | xargs rm -f
#
# Copyright (C) 2008  Matt Florell <vicidial@gmail.com>    LICENSE: AGPLv2
#

# CHANGES
# 71112-1409 - First Build
# 90508-0644 - Changed to PHP long tags
#

$STARTtime = date("U");
$TODAYstart = date("H/i/s 00:00:00");

$linkAST=mysql_connect("10.10.10.15", "cron", "1234");
mysql_select_db("asterisk");

$PHP_AUTH_USER=$_SERVER['PHP_AUTH_USER'];
$PHP_AUTH_PW=$_SERVER['PHP_AUTH_PW'];
$PHP_SELF=$_SERVER['PHP_SELF'];
if (isset($_GET["phone"]))				{$phone=$_GET["phone"];}
	elseif (isset($_POST["phone"]))		{$phone=$_POST["phone"];}
if (isset($_GET["format"]))				{$format=$_GET["format"];}
	elseif (isset($_POST["format"]))	{$format=$_POST["format"];}
if (isset($_GET["auth"]))				{$auth=$_GET["auth"];}
	elseif (isset($_POST["auth"]))		{$auth=$_POST["auth"];}

$US='_';

  if(eregi("VDC1234593JH654398722",$auth))
	{$nothing=1;}
	else
	{
    echo "auth code: |$auth|\n";
    exit;
	}

		$fp = fopen ("/usr/local/apache2/htdocs/vicidial/auth_entries.txt", "a");
		$date = date("r");
		$ip = getenv("REMOTE_ADDR");
		$browser = getenv("HTTP_USER_AGENT");
		fwrite ($fp, "AUTH|VDC   |$date|$auth|$ip|$phone|$format|$browser|\n");
		fclose($fp);

if (strlen($format)<3) {$format='WAV';}
if ( (strlen($phone)<10) or (strlen($phone)>10) ) 
	{
	echo "<html>\n";
	echo "<head>\n";
	echo "<title>Recording ID Lookup: </title>\n";
	echo "</head>\n";
	echo "<body bgcolor=white>\n";
	echo "<br><br>\n";
	echo "You need to use only a 10-digit phone number<BR>\n";
	echo "recording_lookup_DIRECT.php?format=WAV&phone=7275551212&auth=VDC1234593JH654398722\n<BR>";
	exit;
	}
else
	{
	$stmt="select recording_id,filename,location,start_time from recording_log where filename LIKE \"%$phone%\" order by recording_id desc LIMIT 1;";
	$rslt=mysql_query($stmt, $linkAST);
	$logs_to_print = mysql_num_rows($rslt);

	$u=0;
	if ($logs_to_print)
		{
		$row=mysql_fetch_row($rslt);
		$recording_id = $row[0]; 
		$filename =		$row[1];
		$location =		$row[2];
		$start_time =	$row[3];
			$AUDname =	explode("/",$location);
			$AUDnamect =	(count($AUDname)) - 1;
		
		eregi_replace('10.10.10.16','10.10.10.16',$AUDname[$AUDnamect]);

		$fileGSM=$AUDname[$AUDnamect];
		$locationGSM=$location;
		$fileGSM = eregi_replace('.wav','.gsm',$fileGSM);
		if (!eregi('gsm',$locationGSM))
		{
			$locationGSM = eregi_replace('10.10.10.16','10.10.10.16/GSM',$locationGSM);
			$locationGSM = eregi_replace('.wav','.gsm',$locationGSM);
		}
		if ($format == 'WAV')
			{
			exec("/usr/local/apache2/htdocs/vicidial/wget --output-document=/usr/local/apache2/htdocs/vicidial/temp/$AUDname[$AUDnamect] $location\n");

			$AUDIOfile = "/usr/local/apache2/htdocs/vicidial/temp/$AUDname[$AUDnamect]";
			$AUDIOfilename = "$AUDname[$AUDnamect]";
			// We'll be outputting a PDF
			header('Content-type: audio/wav');
			// It will be named properly
			header("Content-Disposition: attachment; filename=\"$AUDIOfilename\"");
			// The PDF source is in original.pdf
			readfile($AUDIOfile);
			}

		if ($format == 'GSM')
			{
			passthru("/usr/local/apache2/htdocs/vicidial/wget --output-document=/usr/local/apache2/htdocs/vicidial/temp/$fileGSM $locationGSM\n");

			$AUDIOfile = "/usr/local/apache2/htdocs/vicidial/temp/$fileGSM";
			$AUDIOfilename = "$fileGSM";
			// We'll be outputting a PDF
			header('Content-type: audio/gsm');
			// It will be named properly
			header("Content-Disposition: attachment; filename=\"$AUDIOfilename\"");
			// The PDF source is in original.pdf
			readfile($AUDIOfile);
			}
		}

	else
		{
		echo "ERROR:        $phone|$format\n";
		}

	}
?>
