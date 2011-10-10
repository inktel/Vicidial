<?php
# new_listloader_superL.php
# 
# Copyright (C) 2010  Matt Florell,Joe Johnson <vicidial@gmail.com>    LICENSE: AGPLv2
#
# AST GUI lead loader from formatted file
# 
# CHANGES
# 50602-1640 - First version created by Joe Johnson
# 51128-1108 - Removed PHP global vars requirement
# 60113-1603 - Fixed a few bugs in Excel import
# 60421-1624 - check GET/POST vars lines with isset to not trigger PHP NOTICES
# 60616-1240 - added listID override
# 60616-1604 - added gmt lookup for each lead
# 60619-1651 - Added variable filtering to eliminate SQL injection attack threat
# 60822-1121 - fixed for nonwritable directories
# 60906-1100 - added filter of non-digits in alt_phone field
# 61110-1222 - added new USA-Canada DST scheme and Brazil DST scheme
# 61128-1149 - added postal code GMT lookup and duplicate check options
# 70417-1059 - Fixed default phone_code bug
# 70510-1518 - Added campaign and system duplicate check and phonecode override
# 80428-0417 - UTF8 changes
# 80514-1030 - removed filesize limit and raised number of errors to be displayed
# 80713-0023 - added last_local_call_time field default of 2008-01-01
# 81011-2009 - a few bug fixes
# 90309-1831 - Added admin_log logging
# 90310-2128 - Added admin header
# 90508-0644 - Changed to PHP long tags
# 90522-0506 - Security fix
# 90721-1339 - Added rank and owner as vicidial_list fields
# 91112-0616 - Added title/alt-phone duplicate checking
# 100118-0543 - Added new Australian and New Zealand DST schemes (FSO-FSA and LSS-FSA)
# 100621-1026 - Added admin_web_directory variable
# 100630-1609 - Added a check for invalid ListIds and filtered out ' " ; ` \ from the field <mikec>
# 100705-1507 - Added custom fields to field chooser, only when liast_id_override is used and only with TXT and CSV file formats
# 100712-1416 - Added entry_list_id field to vicidial_list to preserve link to custom fields if any
#
# make sure vicidial_list exists and that your file follows the formatting correctly. This page does not dedupe or do any other lead filtering actions yet at this time.

$version = '2.4-37';
$build = '100712-1416';


require("dbconnect.php");

### links used for testing
#$link=mysql_connect("10.10.10.15", "cron", "1234");
#mysql_select_db("asterisk");
#$WeBServeRRooT = '/home/www/htdocs';

$US='_';

$PHP_AUTH_USER=$_SERVER['PHP_AUTH_USER'];
$PHP_AUTH_PW=$_SERVER['PHP_AUTH_PW'];
$PHP_SELF=$_SERVER['PHP_SELF'];
$leadfile=$_FILES["leadfile"];
	$LF_orig = $_FILES['leadfile']['name'];
	$LF_path = $_FILES['leadfile']['tmp_name'];
if (isset($_GET["submit_file"]))			{$submit_file=$_GET["submit_file"];}
	elseif (isset($_POST["submit_file"]))	{$submit_file=$_POST["submit_file"];}
if (isset($_GET["submit"]))				{$submit=$_GET["submit"];}
	elseif (isset($_POST["submit"]))	{$submit=$_POST["submit"];}
if (isset($_GET["ENVIAR"]))				{$ENVIAR=$_GET["ENVIAR"];}
	elseif (isset($_POST["ENVIAR"]))	{$ENVIAR=$_POST["ENVIAR"];}
if (isset($_GET["leadfile_name"]))			{$leadfile_name=$_GET["leadfile_name"];}
	elseif (isset($_POST["leadfile_name"]))	{$leadfile_name=$_POST["leadfile_name"];}
if (isset($_FILES["leadfile"]))				{$leadfile_name=$_FILES["leadfile"]['name'];}
if (isset($_GET["file_layout"]))				{$file_layout=$_GET["file_layout"];}
	elseif (isset($_POST["file_layout"]))		{$file_layout=$_POST["file_layout"];}
if (isset($_GET["OK_to_process"]))				{$OK_to_process=$_GET["OK_to_process"];}
	elseif (isset($_POST["OK_to_process"]))		{$OK_to_process=$_POST["OK_to_process"];}
if (isset($_GET["vendor_lead_code_field"]))				{$vendor_lead_code_field=$_GET["vendor_lead_code_field"];}
	elseif (isset($_POST["vendor_lead_code_field"]))	{$vendor_lead_code_field=$_POST["vendor_lead_code_field"];}
if (isset($_GET["source_id_field"]))			{$source_id_field=$_GET["source_id_field"];}
	elseif (isset($_POST["source_id_field"]))	{$source_id_field=$_POST["source_id_field"];}
if (isset($_GET["list_id_field"]))				{$list_id_field=$_GET["list_id_field"];}
	elseif (isset($_POST["list_id_field"]))		{$list_id_field=$_POST["list_id_field"];}
if (isset($_GET["phone_code_field"]))			{$phone_code_field=$_GET["phone_code_field"];}
	elseif (isset($_POST["phone_code_field"]))	{$phone_code_field=$_POST["phone_code_field"];}
if (isset($_GET["phone_number_field"]))				{$phone_number_field=$_GET["phone_number_field"];}
	elseif (isset($_POST["phone_number_field"]))	{$phone_number_field=$_POST["phone_number_field"];}
if (isset($_GET["title_field"]))				{$title_field=$_GET["title_field"];}
	elseif (isset($_POST["title_field"]))		{$title_field=$_POST["title_field"];}
if (isset($_GET["first_name_field"]))			{$first_name_field=$_GET["first_name_field"];}
	elseif (isset($_POST["first_name_field"]))	{$first_name_field=$_POST["first_name_field"];}
if (isset($_GET["middle_initial_field"]))			{$middle_initial_field=$_GET["middle_initial_field"];}
	elseif (isset($_POST["middle_initial_field"]))	{$middle_initial_field=$_POST["middle_initial_field"];}
if (isset($_GET["last_name_field"]))			{$last_name_field=$_GET["last_name_field"];}
	elseif (isset($_POST["last_name_field"]))	{$last_name_field=$_POST["last_name_field"];}
if (isset($_GET["address1_field"]))				{$address1_field=$_GET["address1_field"];}
	elseif (isset($_POST["address1_field"]))	{$address1_field=$_POST["address1_field"];}
if (isset($_GET["address2_field"]))				{$address2_field=$_GET["address2_field"];}
	elseif (isset($_POST["address2_field"]))	{$address2_field=$_POST["address2_field"];}
if (isset($_GET["address3_field"]))				{$address3_field=$_GET["address3_field"];}
	elseif (isset($_POST["address3_field"]))	{$address3_field=$_POST["address3_field"];}
if (isset($_GET["city_field"]))					{$city_field=$_GET["city_field"];}
	elseif (isset($_POST["city_field"]))		{$city_field=$_POST["city_field"];}
if (isset($_GET["state_field"]))				{$state_field=$_GET["state_field"];}
	elseif (isset($_POST["state_field"]))		{$state_field=$_POST["state_field"];}
if (isset($_GET["province_field"]))				{$province_field=$_GET["province_field"];}
	elseif (isset($_POST["province_field"]))		{$province_field=$_POST["province_field"];}
if (isset($_GET["postal_code_field"]))				{$postal_code_field=$_GET["postal_code_field"];}
	elseif (isset($_POST["postal_code_field"]))		{$postal_code_field=$_POST["postal_code_field"];}
if (isset($_GET["country_code_field"]))				{$country_code_field=$_GET["country_code_field"];}
	elseif (isset($_POST["country_code_field"]))	{$country_code_field=$_POST["country_code_field"];}
if (isset($_GET["gender_field"]))				{$gender_field=$_GET["gender_field"];}
	elseif (isset($_POST["gender_field"]))		{$gender_field=$_POST["gender_field"];}
if (isset($_GET["date_of_birth_field"]))			{$date_of_birth_field=$_GET["date_of_birth_field"];}
	elseif (isset($_POST["date_of_birth_field"]))	{$date_of_birth_field=$_POST["date_of_birth_field"];}
if (isset($_GET["alt_phone_field"]))			{$alt_phone_field=$_GET["alt_phone_field"];}
	elseif (isset($_POST["alt_phone_field"]))	{$alt_phone_field=$_POST["alt_phone_field"];}
if (isset($_GET["email_field"]))				{$email_field=$_GET["email_field"];}
	elseif (isset($_POST["email_field"]))		{$email_field=$_POST["email_field"];}
if (isset($_GET["security_phrase_field"]))			{$security_phrase_field=$_GET["security_phrase_field"];}
	elseif (isset($_POST["security_phrase_field"]))	{$security_phrase_field=$_POST["security_phrase_field"];}
if (isset($_GET["comments_field"]))				{$comments_field=$_GET["comments_field"];}
	elseif (isset($_POST["comments_field"]))	{$comments_field=$_POST["comments_field"];}
if (isset($_GET["rank_field"]))					{$rank_field=$_GET["rank_field"];}
	elseif (isset($_POST["rank_field"]))		{$rank_field=$_POST["rank_field"];}
if (isset($_GET["owner_field"]))				{$owner_field=$_GET["owner_field"];}
	elseif (isset($_POST["owner_field"]))		{$owner_field=$_POST["owner_field"];}
if (isset($_GET["list_id_override"]))			{$list_id_override=$_GET["list_id_override"];}
	elseif (isset($_POST["list_id_override"]))	{$list_id_override=$_POST["list_id_override"];}
	$list_id_override = (preg_replace("/\D/","",$list_id_override));
if (isset($_GET["lead_file"]))					{$lead_file=$_GET["lead_file"];}
	elseif (isset($_POST["lead_file"]))			{$lead_file=$_POST["lead_file"];}
if (isset($_GET["dupcheck"]))				{$dupcheck=$_GET["dupcheck"];}
	elseif (isset($_POST["dupcheck"]))		{$dupcheck=$_POST["dupcheck"];}
if (isset($_GET["postalgmt"]))				{$postalgmt=$_GET["postalgmt"];}
	elseif (isset($_POST["postalgmt"]))		{$postalgmt=$_POST["postalgmt"];}
if (isset($_GET["phone_code_override"]))			{$phone_code_override=$_GET["phone_code_override"];}
	elseif (isset($_POST["phone_code_override"]))	{$phone_code_override=$_POST["phone_code_override"];}
	$phone_code_override = (preg_replace("/\D/","",$phone_code_override));
if (isset($_GET["DB"]))					{$DB=$_GET["DB"];}
	elseif (isset($_POST["DB"]))		{$DB=$_POST["DB"];}

# $country_field=$_GET["country_field"];					if (!$country_field) {$country_field=$_POST["country_field"];}

### REGEX to prevent weird characters from ending up in the fields
$field_regx = "['\"`\\;]";

$vicidial_list_fields = '|lead_id|vendor_lead_code|source_id|list_id|gmt_offset_now|called_since_last_reset|phone_code|phone_number|title|first_name|middle_initial|last_name|address1|address2|address3|city|state|province|postal_code|country_code|gender|date_of_birth|alt_phone|email|security_phrase|comments|called_count|last_local_call_time|rank|owner|entry_list_id|';

#############################################
##### START SYSTEM_SETTINGS LOOKUP #####
$stmt = "SELECT use_non_latin,admin_web_directory,custom_fields_enabled FROM system_settings;";
$rslt=mysql_query($stmt, $link);
if ($DB) {echo "$stmt\n";}
$qm_conf_ct = mysql_num_rows($rslt);
if ($qm_conf_ct > 0)
	{
	$row=mysql_fetch_row($rslt);
	$non_latin =				$row[0];
	$admin_web_directory =		$row[1];
	$custom_fields_enabled =	$row[2];
	}
##### END SETTINGS LOOKUP #####
###########################################

if ($non_latin < 1)
	{
	$PHP_AUTH_USER = ereg_replace("[^0-9a-zA-Z]","",$PHP_AUTH_USER);
	$PHP_AUTH_PW = ereg_replace("[^0-9a-zA-Z]","",$PHP_AUTH_PW);
	$list_id_override = ereg_replace("[^0-9]","",$list_id_override);
	}
else
	{
	$PHP_AUTH_PW = ereg_replace("'|\"|\\\\|;","",$PHP_AUTH_PW);
	$PHP_AUTH_USER = ereg_replace("'|\"|\\\\|;","",$PHP_AUTH_USER);
	}

$STARTtime = date("U");
$TODAY = date("Y-m-d");
$NOW_TIME = date("Y-m-d H:i:s");
$FILE_datetime = $STARTtime;

$stmt="SELECT count(*) from vicidial_users where user='$PHP_AUTH_USER' and pass='$PHP_AUTH_PW' and user_level > 7;";
if ($DB) {echo "|$stmt|\n";}
if ($non_latin > 0) {$rslt=mysql_query("SET NAMES 'UTF8'");}
$rslt=mysql_query($stmt, $link);
$row=mysql_fetch_row($rslt);
$auth=$row[0];

if ($WeBRooTWritablE > 0) {$fp = fopen ("./project_auth_entries.txt", "a");}
$date = date("r");
$ip = getenv("REMOTE_ADDR");
$browser = getenv("HTTP_USER_AGENT");

  if( (strlen($PHP_AUTH_USER)<2) or (strlen($PHP_AUTH_PW)<2) or (!$auth))
	{
    Header("WWW-Authenticate: Basic realm=\"VICIDIAL-LEAD-LOADER\"");
    Header("HTTP/1.0 401 Unauthorized");
    echo "Nombre y contraseña inválidos del usuario: |$PHP_AUTH_USER|$PHP_AUTH_PW|\n";
    exit;
	}
  else
	{
	header ("Content-type: text/html; charset=utf-8");
	header ("Cache-Control: no-cache, must-revalidate");  // HTTP/1.1
	header ("Pragma: no-cache");                          // HTTP/1.0

	if($auth>0)
		{
		$office_no=strtoupper($PHP_AUTH_USER);
		$password=strtoupper($PHP_AUTH_PW);
			$stmt="SELECT load_leads from vicidial_users where user='$PHP_AUTH_USER' and pass='$PHP_AUTH_PW'";
			$rslt=mysql_query($stmt, $link);
			$row=mysql_fetch_row($rslt);
			$LOGload_leads				=$row[0];

		if ($LOGload_leads < 1)
			{
			echo "You do not have permissions to load leads\n";
			exit;
			}
		if ($WeBRooTWritablE > 0) 
			{
			fwrite ($fp, "LIST_LOAD|GOOD|$date|$PHP_AUTH_USER|$PHP_AUTH_PW|$ip|$browser|$LOGfullname|\n");
			fclose($fp);
			}
		}
	else
		{
		if ($WeBRooTWritablE > 0) 
			{
			fwrite ($fp, "LIST_LOAD|FAIL|$date|$PHP_AUTH_USER|$PHP_AUTH_PW|$ip|$browser|\n");
			fclose($fp);
			}
		}
	}


$script_name = getenv("SCRIPT_NAME");
$server_name = getenv("SERVER_NAME");
$server_port = getenv("SERVER_PORT");
if (eregi("443",$server_port)) {$HTTPprotocol = 'https://';}
  else {$HTTPprotocol = 'http://';}
$admDIR = "$HTTPprotocol$server_name$script_name";
$admDIR = eregi_replace('new_listloader_superL.php','',$admDIR);
$admSCR = 'admin.php';
$NWB = " &nbsp; <a href=\"javascript:openNewWindow('$admDIR$admSCR?ADD=99999";
$NWE = "')\"><IMG SRC=\"help.gif\" WIDTH=20 HEIGHT=20 Border=0 ALT=\"AYUDA\" ALIGN=TOP></A>";

$secX = date("U");
$hour = date("H");
$min = date("i");
$sec = date("s");
$mon = date("m");
$mday = date("d");
$year = date("Y");
$isdst = date("I");
$Shour = date("H");
$Smin = date("i");
$Ssec = date("s");
$Smon = date("m");
$Smday = date("d");
$Syear = date("Y");
$pulldate0 = "$year-$mon-$mday $hour:$min:$sec";
$inSD = $pulldate0;
$dsec = ( ( ($hour * 3600) + ($min * 60) ) + $sec );

	### Grab Server GMT value from the database
	$stmt="SELECT local_gmt FROM servers where server_ip = '$server_ip';";
	$rslt=mysql_query($stmt, $link);
	$gmt_recs = mysql_num_rows($rslt);
	if ($gmt_recs > 0)
		{
		$row=mysql_fetch_row($rslt);
		$DBSERVER_GMT		=		"$row[0]";
		if (strlen($DBSERVER_GMT)>0)	{$SERVER_GMT = $DBSERVER_GMT;}
		if ($isdst) {$SERVER_GMT++;} 
		}
	else
		{
		$SERVER_GMT = date("O");
		$SERVER_GMT = eregi_replace("\+","",$SERVER_GMT);
		$SERVER_GMT = ($SERVER_GMT + 0);
		$SERVER_GMT = ($SERVER_GMT / 100);
		}

	$LOCAL_GMT_OFF = $SERVER_GMT;
	$LOCAL_GMT_OFF_STD = $SERVER_GMT;

#if ($DB) {print "SEED TIME  $secX      :   $year-$mon-$mday $hour:$min:$sec  LOCAL GMT OFFSET NOW: $LOCAL_GMT_OFF\n";}


echo "<html>\n";
echo "<head>\n";
echo "<!-- VERSIÓN: $version     CONSTRUCCION: $build -->\n";
echo "<!-- SEED TIME  $secX:   $year-$mon-$mday $hour:$min:$sec  LOCAL DESPLAZAMIENTO GMT AHORA: $LOCAL_GMT_OFF  DST: $isdst -->\n";

function macfontfix($fontsize) {

  $browser = getenv("HTTP_USER_AGENT");
  $pctype = explode("(", $browser);
  if (ereg("Mac",$pctype[1])) {
   /* Browser is a Mac.  If not Netscape 6, raise fonts */

    $blownbrowser = explode('/', $browser);
    $ver = explode(' ', $blownbrowser[1]);
    $ver = $ver[0];
    if ($ver >= 5.0) return $fontsize; else return ($fontsize+2);

  } else return $fontsize;	/* Browser is not a Mac - don't touch fonts */
}

echo "<style type=\"text/css\">\n
<!--\n
.title {  font-family: Arial, Helvetica, sans-serif; font-size: ".macfontfix(18)."pt}\n
.standard {  font-family: Arial, Helvetica, sans-serif; font-size: ".macfontfix(10)."pt}\n
.small_standard {  font-family: Arial, Helvetica, sans-serif; font-size: ".macfontfix(8)."pt}\n
.tiny_standard {  font-family: Arial, Helvetica, sans-serif; font-size: ".macfontfix(6)."pt}\n
.standard_bold {  font-family: Arial, Helvetica, sans-serif; font-size: ".macfontfix(10)."pt; font-weight: bold}\n
.standard_header {  font-family: Arial, Helvetica, sans-serif; font-size: ".macfontfix(14)."pt; font-weight: bold}\n
.standard_bold_highlight {  font-family: Arial, Helvetica, sans-serif; font-size: ".macfontfix(10)."pt; font-weight: bold; color: white; BACKGROUND-COLOR: black}\n
.standard_bold_blue_highlight {  font-family: Arial, Helvetica, sans-serif; font-size: 10pt; font-weight: bold; BACKGROUND-COLOR: blue}\n
A.employee_standard {  font-family: garamond, sans-serif; font-size: ".macfontfix(10)."pt; font-style: normal; font-variant: normal; font-weight: bold; text-decoration: none}\n
.employee_standard {  font-family: garamond, sans-serif; font-size: ".macfontfix(10)."pt; font-weight: bold}\n
.employee_title {  font-family: Garamond, sans-serif; font-size: ".macfontfix(14)."pt; font-weight: bold}\n
\\\\-->\n
</style>\n";

?>


<script language="JavaScript1.2">
function openNewWindow(url) {
  window.open (url,"",'width=700,height=300,scrollbars=yes,menubar=yes,address=yes');
}
function ShowProgress(good, bad, total, dup, post) {
	parent.lead_count.document.open();
	parent.lead_count.document.write('<html><body><table border=0 width=200 cellpadding=10 cellspacing=0 align=center valign=top><tr bgcolor="#000000"><th colspan=2><font face="arial, helvetica" size=3 color=white>Estado actual del archivo:</font></th></tr><tr bgcolor="#009900"><td align=right><font face="arial, helvetica" size=2 color=white><B>Good:</B></font></td><td align=left><font face="arial, helvetica" size=2 color=white><B>'+good+'</B></font></td></tr><tr bgcolor="#990000"><td align=right><font face="arial, helvetica" size=2 color=white><B>Bad:</B></font></td><td align=left><font face="arial, helvetica" size=2 color=white><B>'+bad+'</B></font></td></tr><tr bgcolor="#000099"><td align=right><font face="arial, helvetica" size=2 color=white><B>Total:</B></font></td><td align=left><font face="arial, helvetica" size=2 color=white><B>'+total+'</B></font></td></tr><tr bgcolor="#009900"><td align=right><font face="arial, helvetica" size=2 color=white><B> &nbsp; </B></font></td><td align=left><font face="arial, helvetica" size=2 color=white><B> &nbsp; </B></font></td></tr><tr bgcolor="#009900"><td align=right><font face="arial, helvetica" size=2 color=white><B>Duplicate:</B></font></td><td align=left><font face="arial, helvetica" size=2 color=white><B>'+dup+'</B></font></td></tr><tr bgcolor="#009900"><td align=right><font face="arial, helvetica" size=2 color=white><B>Postal Match:</B></font></td><td align=left><font face="arial, helvetica" size=2 color=white><B>'+post+'</B></font></td></tr></table><body></html>');
	parent.lead_count.document.close();
}
function ParseFileName() {
	if (!document.forms[0].OK_to_process) {	
		var endstr=document.forms[0].leadfile.value.lastIndexOf('\\');
		if (endstr>-1) {
			endstr++;
			var filename=document.forms[0].leadfile.value.substring(endstr);
			document.forms[0].leadfile_name.value=filename;
		}
	}
}
</script>
<title>ADMINISTRATION: Lead Loader</title>
</head>
<BODY BGCOLOR=WHITE marginheight=0 marginwidth=0 leftmargin=0 topmargin=0>

<?php
	$short_header=1;

	require("admin_header.php");

echo "<TABLE CELLPADDING=4 CELLSPACING=0><TR><TD>";
?>


<form action=<?php echo $PHP_SELF ?> method=post onSubmit="ParseFileName()" enctype="multipart/form-data">
<input type=hidden name='leadfile_name' value="<?php echo $leadfile_name ?>">
<input type=hidden name='DB' value="<?php echo $DB ?>">
<?php if ($file_layout!="custom") { ?>
<table align=center width="700" border=0 cellpadding=5 cellspacing=0 bgcolor=#D9E6FE>
  <tr>
	<td align=right width="35%"><B><font face="arial, helvetica" size=2>Cargar Leads de este archivo:</font></B></td>
	<td align=left width="65%"><input type=file name="leadfile" value="<?php echo $leadfile ?>"> <?php echo "$NWB#vicidial_list_loader$NWE"; ?></td>
  </tr>
  <tr>
	<td align=right width="25%"><font face="arial, helvetica" size=2>ID De la Lista Override: </font></td>
	<td align=left width="75%"><font face="arial, helvetica" size=1><input type=text value="<?php echo $list_id_override ?>" name='list_id_override' size=10 maxlength=8> (Solamente números or leave blank for values in the file)</td>
  </tr>
  <tr>
	<td align=right width="25%"><font face="arial, helvetica" size=2>Teléfono Código Sobrescribir: </font></td>
	<td align=left width="75%"><font face="arial, helvetica" size=1><input type=text value="<?php echo $phone_code_override ?>" name='phone_code_override' size=8 maxlength=6> (Solamente números or leave blank for values in the file)</td>
  </tr>
  <tr>
	<td align=right><B><font face="arial, helvetica" size=2>Disposición de archivo a utilizar:</font></B></td>
	<td align=left><font face="arial, helvetica" size=2><input type=radio name="file_layout" value="standard" checked>Standard Format&nbsp;&nbsp;&nbsp;&nbsp;<input type=radio name="file_layout" value="custom">Disposición de encargo</td>
  </tr>
    <tr>
	<td align=right width="25%"><font face="arial, helvetica" size=2>Cheque Del Duplicado Del Plomo:</font></td>
	<td align=left width="75%"><font face="arial, helvetica" size=1><select size=1 name=dupcheck>
	<option selected value="NONE">NO DUPLICATE CHECK</option>
	<option value="DUPLIST">CHECK FOR DUPLICATES BY PHONE IN ID DE LA LISTA</option>
	<option value="DUPCAMP">COMPRUEBE PARA SABER SI HAY DUPLICADOS DE PHONE IN TODAS LAS LISTAS DELA CAMPAÑA</option>
	<option value="DUPSYS">COMPRUEBE PARA SABER SI HAY DUPLICADOS POR EL SISTEMA ENTERO DE PHONEIN</option>
	<option value="DUPTITLEALTPHONELIST">CHECK FOR DUPLICATES BY TITLE/ALT-PHONE IN ID DE LA LISTA</option>
	<option value="DUPTITLEALTPHONESYS">CHECK FOR DUPLICATES BY TITLE/ALT-PHONE IN ENTIRE SYSTEM</option>
	</select></td>
  </tr>
  <tr>
	<td align=right width="25%"><font face="arial, helvetica" size=2>Operaciones de búsqueda De la Zona De Tiempo De Plomo:</font></td>
	<td align=left width="75%"><font face="arial, helvetica" size=1><select size=1 name=postalgmt><option selected value="AREA">COUNTRY CODE AND AREA CODE ONLY</option><option value="POSTAL">POSTAL CODE FIRST</option></select></td>
  </tr>
<tr>
	<td align=center colspan=2><input type=submit value="ENVIAR" name='submit_file'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type=button onClick="javascript:document.location='new_listloader_superL.php'" value="RECARGAR" name='reload_page'></td>
  </tr>
  <tr><td align=left><font size=1> &nbsp; &nbsp; &nbsp; &nbsp; <a href="admin.php?ADD=100" target="_parent">DE NUEVO AL ADMIN</a> &nbsp; &nbsp; &nbsp; &nbsp; <a href="./admin_listloader_third_gen.php">3rd Gen Lead Loader</a> &nbsp; &nbsp; </font></td><td align=right><font size=1>LIST LOADER- &nbsp; &nbsp; VERSIÓN: <?php echo $version ?> &nbsp; &nbsp; CONSTRUCCION: <?php echo $build ?> &nbsp; &nbsp; </td></tr>
</table>
<?php } ?>

<?php

	if ($OK_to_process) {
		print "<script language='JavaScript1.2'>document.forms[0].leadfile.disabled=true;document.forms[0].list_id_override.disabled=true;document.forms[0].phone_code_override.disabled=true; document.forms[0].submit_file.disabled=true; document.forms[0].reload_page.disabled=true;</script>";
		flush();
		$total=0; $good=0; $bad=0; $dup=0; $post=0; $phone_list='';

		if (!eregi(".csv", $leadfile_name) && !eregi(".xls", $leadfile_name)) {
			# copy($leadfile, "./vicidial_temp_file.txt");
			$file=fopen("$lead_file", "r");
			if ($WeBRooTWritablE > 0)
				{
				$stmt_file=fopen("listloader_stmts.txt", "w");
				}
			$buffer=fgets($file, 4096);
			$tab_count=substr_count($buffer, "\t");
			$pipe_count=substr_count($buffer, "|");

			if ($tab_count>$pipe_count) {$delimiter="\t";  $delim_name="tab";} else {$delimiter="|";  $delim_name="pipe";}
			$field_check=explode($delimiter, $buffer);

			if (count($field_check)>=2) {
				flush();
				$file=fopen("$lead_file", "r");
				print "<center><font face='arial, helvetica' size=3 color='#009900'><B>Procesando$delim_name-delimited file...\n";

			if (strlen($list_id_override)>0) 
				{
				print "<BR><BR>ID DE LA LISTA OVERRIDE FOR THIS FILE: $list_id_override<BR><BR>";
				}

			if (strlen($phone_code_override)>0) 
				{
				print "<BR><BR>INVALIDACIÓN DEL CÓDIGO DEL TELÉFONO PARA ESTE ARCHIVO: $phone_code_override<BR><BR>";
				}

				while (!feof($file)) {
					$record++;
					$buffer=rtrim(fgets($file, 4096));
					$buffer=stripslashes($buffer);

					if (strlen($buffer)>0) {
						$row=explode($delimiter, eregi_replace("[\'\"]", "", $buffer));

						$pulldate=date("Y-m-d H:i:s");
						$entry_date =			"$pulldate";
						$modify_date =			"";
						$status =				"NEW";
						$user ="";
						$vendor_lead_code =		$row[$vendor_lead_code_field];
						$source_code =			$row[$source_id_field];
						$source_id=$source_code;
						$list_id =				$row[$list_id_field];
						$gmt_offset =			'0';
						$called_since_last_reset='N';
						$phone_code =			eregi_replace("[^0-9]", "", $row[$phone_code_field]);
						$phone_number =			eregi_replace("[^0-9]", "", $row[$phone_number_field]);
						$title =				$row[$title_field];
						$first_name =			$row[$first_name_field];
						$middle_initial =		$row[$middle_initial_field];
						$last_name =			$row[$last_name_field];
						$address1 =				$row[$address1_field];
						$address2 =				$row[$address2_field];
						$address3 =				$row[$address3_field];
						$city =$row[$city_field];
						$state =				$row[$state_field];
						$province =				$row[$province_field];
						$postal_code =			$row[$postal_code_field];
						$country_code =			$row[$country_code_field];
						$gender =				$row[$gender_field];
						$date_of_birth =		$row[$date_of_birth_field];
						$alt_phone =			eregi_replace("[^0-9]", "", $row[$alt_phone_field]);
						$email =				$row[$email_field];
						$security_phrase =		$row[$security_phrase_field];
						$comments =				trim($row[$comments_field]);
						$rank =					$row[$rank_field];
						$owner =				$row[$owner_field];
						
						# replace ' " ` \ ; with nothing
						$vendor_lead_code =		eregi_replace($field_regx, "", $vendor_lead_code);
						$source_code =			eregi_replace($field_regx, "", $source_code);
						$source_id = 			eregi_replace($field_regx, "", $source_id);
						$list_id =				eregi_replace($field_regx, "", $list_id);
						$phone_code =			eregi_replace($field_regx, "", $phone_code);
						$phone_number =			eregi_replace($field_regx, "", $phone_number);
						$title =				eregi_replace($field_regx, "", $title);
						$first_name =			eregi_replace($field_regx, "", $first_name);
						$middle_initial =		eregi_replace($field_regx, "", $middle_initial);
						$last_name =			eregi_replace($field_regx, "", $last_name);
						$address1 =				eregi_replace($field_regx, "", $address1);
						$address2 =				eregi_replace($field_regx, "", $address2);
						$address3 =				eregi_replace($field_regx, "", $address3);
						$city =					eregi_replace($field_regx, "", $city);
						$state =				eregi_replace($field_regx, "", $state);
						$province =				eregi_replace($field_regx, "", $province);
						$postal_code =			eregi_replace($field_regx, "", $postal_code);
						$country_code =			eregi_replace($field_regx, "", $country_code);
						$gender =				eregi_replace($field_regx, "", $gender);
						$date_of_birth =		eregi_replace($field_regx, "", $date_of_birth);
						$alt_phone =			eregi_replace($field_regx, "", $alt_phone);
						$email =				eregi_replace($field_regx, "", $email);
						$security_phrase =		eregi_replace($field_regx, "", $security_phrase);
						$comments =				eregi_replace($field_regx, "", $comments);
						$rank =					eregi_replace($field_regx, "", $rank);
						$owner =				eregi_replace($field_regx, "", $owner);
						
						$USarea = 			substr($phone_number, 0, 3);

						if (strlen($list_id_override)>0) 
							{
						#	print "<BR><BR>ID DE LA LISTA OVERRIDE FOR THIS FILE: $list_id_override<BR><BR>";
							$list_id = $list_id_override;
							}
						if (strlen($phone_code_override)>0) 
							{
							$phone_code = $phone_code_override;
							}

						##### BEGIN custom fields columns list ###
						$custom_SQL='';
						if ($custom_fields_enabled > 0)
							{
							$stmt="DEMOSTRACIÓN TABLAS LIKE \"custom_$list_id_override\";";
							if ($DB>0) {echo "$stmt\n";}
							$rslt=mysql_query($stmt, $link);
							$tablecount_to_print = mysql_num_rows($rslt);
							if ($tablecount_to_print > 0) 
								{
								$stmt="SELECT count(*) from vicidial_lists_fields where list_id='$list_id_override';";
								if ($DB>0) {echo "$stmt\n";}
								$rslt=mysql_query($stmt, $link);
								$fieldscount_to_print = mysql_num_rows($rslt);
								if ($fieldscount_to_print > 0) 
									{
									$rowx=mysql_fetch_row($rslt);
									$custom_records_count =	$rowx[0];
									
									$stmt="SELECT field_id,field_label,field_name,field_description,field_rank,field_help,field_type,field_options,field_size,field_max,field_default,field_cost,field_required,multi_position,name_position,field_order from vicidial_lists_fields where list_id='$list_id_override' order by field_rank,field_order,field_label;";
									if ($DB>0) {echo "$stmt\n";}
									$rslt=mysql_query($stmt, $link);
									$fields_to_print = mysql_num_rows($rslt);
									$fields_list='';
									$o=0;
									while ($fields_to_print > $o) 
										{
										$rowx=mysql_fetch_row($rslt);
										$A_field_label[$o] =	$rowx[1];
										$A_field_type[$o] =		$rowx[6];
										$A_field_value[$o] =	'';

										$field_name_id = $A_field_label[$o] . "_field";

										if ($DB>0) {echo "$A_field_label[$o]|$A_field_type[$o]\n";}

										if ( ($A_field_type[$o]!='DISPLAY') and ($A_field_type[$o]!='SCRIPT') )
											{
											if (!preg_match("/\|$A_field_label[$o]\|/",$vicidial_list_fields))
												{
												if (isset($_GET["$field_name_id"]))				{$form_field_value=$_GET["$field_name_id"];}
													elseif (isset($_POST["$field_name_id"]))	{$form_field_value=$_POST["$field_name_id"];}

												if ($form_field_value >= 0)
													{
													$A_field_value[$o] =	$row[$form_field_value];
													# replace ' " ` \ ; with nothing
													$A_field_value[$o] =	eregi_replace($field_regx, "", $A_field_value[$o]);

													$custom_SQL .= "$A_field_label[$o]='$A_field_value[$o]',";
													}
												}
											}
										$o++;
										}
									}
								}
							}
						##### END custom fields columns list ###

						$custom_SQL = preg_replace("/,$/","",$custom_SQL);


						##### Check for duplicate phone numbers in vicidial_list table for all lists in a campaign #####
						if (eregi("DUPCAMP",$dupcheck))
							{
								$dup_lead=0;
								$dup_lists='';
							$stmt="select campaign_id from vicidial_lists where list_id='$list_id';";
							$rslt=mysql_query($stmt, $link);
							$ci_recs = mysql_num_rows($rslt);
							if ($ci_recs > 0)
								{
								$row=mysql_fetch_row($rslt);
								$dup_camp =			$row[0];

								$stmt="select list_id from vicidial_lists where campaign_id='$dup_camp';";
								$rslt=mysql_query($stmt, $link);
								$li_recs = mysql_num_rows($rslt);
								if ($li_recs > 0)
									{
									$L=0;
									while ($li_recs > $L)
										{
										$row=mysql_fetch_row($rslt);
										$dup_lists .=	"'$row[0]',";
										$L++;
										}
									$dup_lists = eregi_replace(",$",'',$dup_lists);

									$stmt="select list_id from vicidial_list where phone_number='$phone_number' and list_id IN($dup_lists) limit 1;";
									$rslt=mysql_query($stmt, $link);
									$pc_recs = mysql_num_rows($rslt);
									if ($pc_recs > 0)
										{
										$dup_lead=1;
										$row=mysql_fetch_row($rslt);
										$dup_lead_list =	$row[0];
										}
									if ($dup_lead < 1)
										{
										if (eregi("$phone_number$US$list_id",$phone_list))
											{$dup_lead++; $dup++;}
										}
									}
								}
							}

						##### Check for duplicate phone numbers in vicidial_list table entire database #####
						if (eregi("DUPSYS",$dupcheck))
							{
							$dup_lead=0;
							$stmt="select list_id from vicidial_list where phone_number='$phone_number';";
							$rslt=mysql_query($stmt, $link);
							$pc_recs = mysql_num_rows($rslt);
							if ($pc_recs > 0)
								{
								$dup_lead=1;
								$row=mysql_fetch_row($rslt);
								$dup_lead_list =	$row[0];
								}
							if ($dup_lead < 1)
								{
								if (eregi("$phone_number$US$list_id",$phone_list))
									{$dup_lead++; $dup++;}
								}
							}

						##### Check for duplicate phone numbers in vicidial_list table for one list_id #####
						if (eregi("DUPLIST",$dupcheck))
							{
							$dup_lead=0;
							$stmt="select count(*) from vicidial_list where phone_number='$phone_number' and list_id='$list_id';";
							$rslt=mysql_query($stmt, $link);
							$pc_recs = mysql_num_rows($rslt);
							if ($pc_recs > 0)
								{
								$row=mysql_fetch_row($rslt);
								$dup_lead =			$row[0];
								$dup_lead_list =	$list_id;
								}
							if ($dup_lead < 1)
								{
								if (eregi("$phone_number$US$list_id",$phone_list))
									{$dup_lead++; $dup++;}
								}
							}

						##### Check for duplicate title and alt-phone in vicidial_list table for one list_id #####
						if (eregi("DUPTITLEALTPHONELIST",$dupcheck))
							{
							$dup_lead=0;
							$stmt="select count(*) from vicidial_list where title='$title' and alt_phone='$alt_phone' and list_id='$list_id';";
							$rslt=mysql_query($stmt, $link);
							$pc_recs = mysql_num_rows($rslt);
							if ($pc_recs > 0)
								{
								$row=mysql_fetch_row($rslt);
								$dup_lead =			$row[0];
								$dup_lead_list =	$list_id;
								}
							if ($dup_lead < 1)
								{
								if (eregi("$alt_phone$title$US$list_id",$phone_list))
									{$dup_lead++; $dup++;}
								}
							}

						##### Check for duplicate phone numbers in vicidial_list table entire database #####
						if (eregi("DUPTITLEALTPHONESYS",$dupcheck))
							{
							$dup_lead=0;
							$stmt="select list_id from vicidial_list where title='$title' and alt_phone='$alt_phone';";
							$rslt=mysql_query($stmt, $link);
							$pc_recs = mysql_num_rows($rslt);
							if ($pc_recs > 0)
								{
								$dup_lead=1;
								$row=mysql_fetch_row($rslt);
								$dup_lead_list =	$row[0];
								}
							if ($dup_lead < 1)
								{
								if (eregi("$alt_phone$title$US$list_id",$phone_list))
									{$dup_lead++; $dup++;}
								}
							}


						if ( (strlen($phone_number)>6) and ($dup_lead<1) and ($list_id >= 100 ))
							{
							if (strlen($phone_code)<1) {$phone_code = '1';}

							if (eregi("TITLEALTPHONE",$dupcheck))
								{$phone_list .= "$alt_phone$title$US$list_id|";}
							else
								{$phone_list .= "$phone_number$US$list_id|";}

							$gmt_offset = lookup_gmt($phone_code,$USarea,$state,$LOCAL_GMT_OFF_STD,$Shour,$Smin,$Ssec,$Smon,$Smday,$Syear,$postalgmt,$postal_code);

							if (strlen($custom_SQL)>3)
								{
								$stmtZ = "INSERT INTO vicidial_list (lead_id,entry_date,modify_date,status,user,vendor_lead_code,source_id,list_id,gmt_offset_now,called_since_last_reset,phone_code,phone_number,title,first_name,middle_initial,last_name,address1,address2,address3,city,state,province,postal_code,country_code,gender,date_of_birth,alt_phone,email,security_phrase,comments,called_count,last_local_call_time,rank,owner,entry_list_id) values('','$entry_date','$modify_date','$status','$user','$vendor_lead_code','$source_id','$list_id','$gmt_offset','$called_since_last_reset','$phone_code','$phone_number','$title','$first_name','$middle_initial','$last_name','$address1','$address2','$address3','$city','$state','$province','$postal_code','$country_code','$gender','$date_of_birth','$alt_phone','$email','$security_phrase','$comments',0,'2008-01-01 00:00:00','$rank','$owner','$list_id');";
								$rslt=mysql_query($stmtZ, $link);
								$affected_rows = mysql_affected_rows($link);
								$lead_id = mysql_insert_id($link);
								if ($DB > 0) {echo "<!-- $affected_rows|$lead_id|$stmtZ -->";}
								if ($WeBRooTWritablE > 0) 
									{fwrite($stmt_file, $stmtZ."\r\n");}
								$multistmt='';

								$custom_SQL_query = "INSERT INTO custom_$list_id_override SET lead_id='$lead_id',$custom_SQL;";
								$rslt=mysql_query($custom_SQL_query, $link);
								$affected_rows = mysql_affected_rows($link);
								if ($DB > 0) {echo "<!-- $affected_rows|$custom_SQL_query -->";}
								}
							else
								{
								if ($multi_insert_counter > 8) 
									{
									### insert good record into vicidial_list table ###
									$stmtZ = "INSERT INTO vicidial_list (lead_id,entry_date,modify_date,status,user,vendor_lead_code,source_id,list_id,gmt_offset_now,called_since_last_reset,phone_code,phone_number,title,first_name,middle_initial,last_name,address1,address2,address3,city,state,province,postal_code,country_code,gender,date_of_birth,alt_phone,email,security_phrase,comments,called_count,last_local_call_time,rank,owner,entry_list_id) values$multistmt('','$entry_date','$modify_date','$status','$user','$vendor_lead_code','$source_id','$list_id','$gmt_offset','$called_since_last_reset','$phone_code','$phone_number','$title','$first_name','$middle_initial','$last_name','$address1','$address2','$address3','$city','$state','$province','$postal_code','$country_code','$gender','$date_of_birth','$alt_phone','$email','$security_phrase','$comments',0,'2008-01-01 00:00:00','$rank','$owner','0');";
									$rslt=mysql_query($stmtZ, $link);
									if ($WeBRooTWritablE > 0) 
										{fwrite($stmt_file, $stmtZ."\r\n");}
									$multistmt='';
									$multi_insert_counter=0;
									}
								else
									{
									$multistmt .= "('','$entry_date','$modify_date','$status','$user','$vendor_lead_code','$source_id','$list_id','$gmt_offset','$called_since_last_reset','$phone_code','$phone_number','$title','$first_name','$middle_initial','$last_name','$address1','$address2','$address3','$city','$state','$province','$postal_code','$country_code','$gender','$date_of_birth','$alt_phone','$email','$security_phrase','$comments',0,'2008-01-01 00:00:00','$rank','$owner','0'),";
									$multi_insert_counter++;
									}
								}

							$good++;
						} else {
							if ($bad < 1000000)
								{
								if ( $list_id < 100 )
									{
									print "<BR></b><font size=1 color=red>record $total BAD- PHONE: $phone_number ROW: |$row[0]| INVALID ID DE LA LISTA</font><b>\n";
									}
								else
									{
									print "<BR></b><font size=1 color=red>record $total BAD- PHONE: $phone_number ROW: |$row[0]| DUP: $dup_lead  $dup_lead_list</font><b>\n";
									}
								}
							$bad++;
						}
						$total++;
						if ($total%100==0) {
							print "<script language='JavaScript1.2'>ShowProgress($good, $bad, $total, $dup, $post)</script>";
							usleep(1000);
							flush();
						}
					}
				}
				if ($multi_insert_counter!=0) {
					$stmtZ = "INSERT INTO vicidial_list (lead_id,entry_date,modify_date,status,user,vendor_lead_code,source_id,list_id,gmt_offset_now,called_since_last_reset,phone_code,phone_number,title,first_name,middle_initial,last_name,address1,address2,address3,city,state,province,postal_code,country_code,gender,date_of_birth,alt_phone,email,security_phrase,comments,called_count,last_local_call_time,rank,owner,entry_list_id) values".substr($multistmt, 0, -1).";";
					mysql_query($stmtZ, $link);
					if ($WeBRooTWritablE > 0) 
						{fwrite($stmt_file, $stmtZ."\r\n");}
				}

				print "<BR><BR>Done</B> GOOD: $good &nbsp; &nbsp; &nbsp; BAD: $bad &nbsp; &nbsp; &nbsp; TOTAL: $total</font></center>";

			} else {
				print "<center><font face='arial, helvetica' size=3 color='#990000'><B>ERROR: El archivo no tiene el número requerido de los campos para procesarlo.</B></font></center>";
			}
		} else if (!eregi(".csv", $leadfile_name)) {
			# copy($leadfile, "./vicidial_temp_file.xls");
			$file=fopen("$lead_file", "r");

			print "<center><font face='arial, helvetica' size=3 color='#009900'><B>ProcesandoExcel file... \n";
			if (strlen($list_id_override)>0) 
			{
			print "<BR><BR>ID DE LA LISTA OVERRIDE FOR THIS FILE: $list_id_override<BR><BR>\n";
			}
			if (strlen($phone_code_override)>0) 
			{
			print "<BR><BR>INVALIDACIÓN DEL CÓDIGO DEL TELÉFONO PARA ESTE ARCHIVO: $phone_code_override<BR><BR>\n";
			}
		# print "|$WeBServeRRooT/$admin_web_directory/listloader_super.pl $vendor_lead_code_field,$source_id_field,$list_id_field,$phone_code_field,$phone_number_field,$title_field,$first_name_field,$middle_initial_field,$last_name_field,$address1_field,$address2_field,$address3_field,$city_field,$state_field,$province_field,$postal_code_field,$country_code_field,$gender_field,$date_of_birth_field,$alt_phone_field,$email_field,$security_phrase_field,$comments_field,$rank_field,$owner_field, --forcelistid=$list_id_override --lead_file=$lead_file|";
			$dupcheckCLI=''; $postalgmtCLI='';
			if (eregi("DUPLIST",$dupcheck)) {$dupcheckCLI='--duplicate-check';}
			if (eregi("DUPCAMP",$dupcheck)) {$dupcheckCLI='--duplicate-campaign-check';}
			if (eregi("DUPSYS",$dupcheck)) {$dupcheckCLI='--duplicate-system-check';}
			if (eregi("DUPTITLEALTPHONELIST",$dupcheck)) {$dupcheckCLI='--duplicate-tap-list-check';}
			if (eregi("DUPTITLEALTPHONESYS",$dupcheck)) {$dupcheckCLI='--duplicate-tap-system-check';}
			if (eregi("POSTAL",$postalgmt)) {$postalgmtCLI='--postal-code-gmt';}
			passthru("$WeBServeRRooT/$admin_web_directory/listloader_super.pl $vendor_lead_code_field,$source_id_field,$list_id_field,$phone_code_field,$phone_number_field,$title_field,$first_name_field,$middle_initial_field,$last_name_field,$address1_field,$address2_field,$address3_field,$city_field,$state_field,$province_field,$postal_code_field,$country_code_field,$gender_field,$date_of_birth_field,$alt_phone_field,$email_field,$security_phrase_field,$comments_field,$rank_field,$owner_field, --forcelistid=$list_id_override --forcephonecode=$phone_code_override --lead-file=$lead_file $postalgmtCLI $dupcheckCLI");
		} else {
			# copy($leadfile, "./vicidial_temp_file.csv");
			$file=fopen("$lead_file", "r");

			if ($WeBRooTWritablE > 0)
				{$stmt_file=fopen("$WeBServeRRooT/$admin_web_directory/listloader_stmts.txt", "w");}
			
			print "<center><font face='arial, helvetica' size=3 color='#009900'><B>ProcesandoCSV file... \n";
			if (strlen($list_id_override)>0) 
				{
				print "<BR><BR>ID DE LA LISTA OVERRIDE FOR THIS FILE: $list_id_override<BR><BR>";
				}

			if (strlen($phone_code_override)>0) 
				{
				print "<BR><BR>INVALIDACIÓN DEL CÓDIGO DEL TELÉFONO PARA ESTE ARCHIVO: $phone_code_override<BR><BR>";
				}

			while($row=fgetcsv($file, 1000, ",")) {

				$pulldate=date("Y-m-d H:i:s");
				$entry_date =			"$pulldate";
				$modify_date =			"";
				$status =				"NEW";
				$user ="";
				$vendor_lead_code =		$row[$vendor_lead_code_field];
				$source_code =			$row[$source_id_field];
				$source_id=$source_code;
				$list_id =				$row[$list_id_field];
				$gmt_offset =			'0';
				$called_since_last_reset='N';
				$phone_code =			eregi_replace("[^0-9]", "", $row[$phone_code_field]);
				$phone_number =			eregi_replace("[^0-9]", "", $row[$phone_number_field]);
				$title =				$row[$title_field];
				$first_name =			$row[$first_name_field];
				$middle_initial =		$row[$middle_initial_field];
				$last_name =			$row[$last_name_field];
				$address1 =				$row[$address1_field];
				$address2 =				$row[$address2_field];
				$address3 =				$row[$address3_field];
				$city =$row[$city_field];
				$state =				$row[$state_field];
				$province =				$row[$province_field];
				$postal_code =			$row[$postal_code_field];
				$country_code =			$row[$country_code_field];
				$gender =				$row[$gender_field];
				$date_of_birth =		$row[$date_of_birth_field];
				$alt_phone =			eregi_replace("[^0-9]", "", $row[$alt_phone_field]);
				$email =				$row[$email_field];
				$security_phrase =		$row[$security_phrase_field];
				$comments =				trim($row[$comments_field]);
				$rank =					$row[$rank_field];
				$owner =				$row[$owner_field];
				
				# replace ' " ` \ ; with nothing
				$vendor_lead_code =		eregi_replace($field_regx, "", $vendor_lead_code);
				$source_code =			eregi_replace($field_regx, "", $source_code);
				$source_id = 			eregi_replace($field_regx, "", $source_id);
				$list_id =				eregi_replace($field_regx, "", $list_id);
				$phone_code =			eregi_replace($field_regx, "", $phone_code);
				$phone_number =			eregi_replace($field_regx, "", $phone_number);
				$title =				eregi_replace($field_regx, "", $title);
				$first_name =			eregi_replace($field_regx, "", $first_name);
				$middle_initial =		eregi_replace($field_regx, "", $middle_initial);
				$last_name =			eregi_replace($field_regx, "", $last_name);
				$address1 =				eregi_replace($field_regx, "", $address1);
				$address2 =				eregi_replace($field_regx, "", $address2);
				$address3 =				eregi_replace($field_regx, "", $address3);
				$city =					eregi_replace($field_regx, "", $city);
				$state =				eregi_replace($field_regx, "", $state);
				$province =				eregi_replace($field_regx, "", $province);
				$postal_code =			eregi_replace($field_regx, "", $postal_code);
				$country_code =			eregi_replace($field_regx, "", $country_code);
				$gender =				eregi_replace($field_regx, "", $gender);
				$date_of_birth =		eregi_replace($field_regx, "", $date_of_birth);
				$alt_phone =			eregi_replace($field_regx, "", $alt_phone);
				$email =				eregi_replace($field_regx, "", $email);
				$security_phrase =		eregi_replace($field_regx, "", $security_phrase);
				$comments =				eregi_replace($field_regx, "", $comments);
				$rank =					eregi_replace($field_regx, "", $rank);
				$owner =				eregi_replace($field_regx, "", $owner);
				
				$USarea = 			substr($phone_number, 0, 3);


				if (strlen($rank)<1) {$rank='0';}

				if (strlen($list_id_override)>0) 
					{
					$list_id = $list_id_override;
					}
				if (strlen($phone_code_override)>0) 
					{
					$phone_code = $phone_code_override;
					}

					##### BEGIN custom fields columns list ###
					$custom_SQL='';
					if ($custom_fields_enabled > 0)
						{
						$stmt="DEMOSTRACIÓN TABLAS LIKE \"custom_$list_id_override\";";
						if ($DB>0) {echo "$stmt\n";}
						$rslt=mysql_query($stmt, $link);
						$tablecount_to_print = mysql_num_rows($rslt);
						if ($tablecount_to_print > 0) 
							{
							$stmt="SELECT count(*) from vicidial_lists_fields where list_id='$list_id_override';";
							if ($DB>0) {echo "$stmt\n";}
							$rslt=mysql_query($stmt, $link);
							$fieldscount_to_print = mysql_num_rows($rslt);
							if ($fieldscount_to_print > 0) 
								{
								$rowx=mysql_fetch_row($rslt);
								$custom_records_count =	$rowx[0];

								$stmt="SELECT field_id,field_label,field_name,field_description,field_rank,field_help,field_type,field_options,field_size,field_max,field_default,field_cost,field_required,multi_position,name_position,field_order from vicidial_lists_fields where list_id='$list_id_override' order by field_rank,field_order,field_label;";
								if ($DB>0) {echo "$stmt\n";}
								$rslt=mysql_query($stmt, $link);
								$fields_to_print = mysql_num_rows($rslt);
								$fields_list='';
								$o=0;
								while ($fields_to_print > $o) 
									{
									$rowx=mysql_fetch_row($rslt);
									$A_field_label[$o] =	$rowx[1];
									$A_field_type[$o] =		$rowx[6];
									$A_field_value[$o] =	'';

									$field_name_id = $A_field_label[$o] . "_field";

									if ($DB>0) {echo "$A_field_label[$o]|$A_field_type[$o]\n";}

									if ( ($A_field_type[$o]!='DISPLAY') and ($A_field_type[$o]!='SCRIPT') )
										{
										if (!preg_match("/\|$A_field_label[$o]\|/",$vicidial_list_fields))
											{
											if (isset($_GET["$field_name_id"]))				{$form_field_value=$_GET["$field_name_id"];}
												elseif (isset($_POST["$field_name_id"]))	{$form_field_value=$_POST["$field_name_id"];}

											if ($form_field_value >= 0)
												{
												$A_field_value[$o] =	$row[$form_field_value];
												# replace ' " ` \ ; with nothing
												$A_field_value[$o] =	eregi_replace($field_regx, "", $A_field_value[$o]);

												$custom_SQL .= "$A_field_label[$o]='$A_field_value[$o]',";
												}
											}
										}
									$o++;
									}
								}
							}
						}
					##### END custom fields columns list ###

					$custom_SQL = preg_replace("/,$/","",$custom_SQL);

					
					##### Check for duplicate phone numbers in vicidial_list table for all lists in a campaign #####
					if (eregi("DUPCAMP",$dupcheck))
						{
							$dup_lead=0;
							$dup_lists='';
						$stmt="select campaign_id from vicidial_lists where list_id='$list_id';";
						$rslt=mysql_query($stmt, $link);
						$ci_recs = mysql_num_rows($rslt);
						if ($ci_recs > 0)
							{
							$row=mysql_fetch_row($rslt);
							$dup_camp =			$row[0];

							$stmt="select list_id from vicidial_lists where campaign_id='$dup_camp';";
							$rslt=mysql_query($stmt, $link);
							$li_recs = mysql_num_rows($rslt);
							if ($li_recs > 0)
								{
								$L=0;
								while ($li_recs > $L)
									{
									$row=mysql_fetch_row($rslt);
									$dup_lists .=	"'$row[0]',";
									$L++;
									}
								$dup_lists = eregi_replace(",$",'',$dup_lists);

								$stmt="select list_id from vicidial_list where phone_number='$phone_number' and list_id IN($dup_lists) limit 1;";
								$rslt=mysql_query($stmt, $link);
								$pc_recs = mysql_num_rows($rslt);
								if ($pc_recs > 0)
									{
									$dup_lead=1;
									$row=mysql_fetch_row($rslt);
									$dup_lead_list =	$row[0];
									}
								if ($dup_lead < 1)
									{
									if (eregi("$phone_number$US$list_id",$phone_list))
										{$dup_lead++; $dup++;}
									}
								}
							}
						}

					##### Check for duplicate phone numbers in vicidial_list table entire database #####
					if (eregi("DUPSYS",$dupcheck))
						{
						$dup_lead=0;
						$stmt="select list_id from vicidial_list where phone_number='$phone_number';";
						$rslt=mysql_query($stmt, $link);
						$pc_recs = mysql_num_rows($rslt);
						if ($pc_recs > 0)
							{
							$dup_lead=1;
							$row=mysql_fetch_row($rslt);
							$dup_lead_list =	$row[0];
							}
						if ($dup_lead < 1)
							{
							if (eregi("$phone_number$US$list_id",$phone_list))
								{$dup_lead++; $dup++;}
							}
						}

					##### Check for duplicate phone numbers in vicidial_list table for one list_id #####
					if (eregi("DUPLIST",$dupcheck))
						{
						$dup_lead=0;
						$stmt="select count(*) from vicidial_list where phone_number='$phone_number' and list_id='$list_id';";
						$rslt=mysql_query($stmt, $link);
						$pc_recs = mysql_num_rows($rslt);
						if ($pc_recs > 0)
							{
							$row=mysql_fetch_row($rslt);
							$dup_lead =			$row[0];
							}
						if ($dup_lead < 1)
							{
							if (eregi("$phone_number$US$list_id",$phone_list))
								{$dup_lead++; $dup++;}
							}
						}

					##### Check for duplicate title and alt-phone in vicidial_list table for one list_id #####
					if (eregi("DUPTITLEALTPHONELIST",$dupcheck))
						{
						$dup_lead=0;
						$stmt="select count(*) from vicidial_list where title='$title' and alt_phone='$alt_phone' and list_id='$list_id';";
						$rslt=mysql_query($stmt, $link);
						$pc_recs = mysql_num_rows($rslt);
						if ($pc_recs > 0)
							{
							$row=mysql_fetch_row($rslt);
							$dup_lead =			$row[0];
							$dup_lead_list =	$list_id;
							}
						if ($dup_lead < 1)
							{
							if (eregi("$alt_phone$title$US$list_id",$phone_list))
								{$dup_lead++; $dup++;}
							}
						}

					##### Check for duplicate phone numbers in vicidial_list table entire database #####
					if (eregi("DUPTITLEALTPHONESYS",$dupcheck))
						{
						$dup_lead=0;
						$stmt="select list_id from vicidial_list where title='$title' and alt_phone='$alt_phone';";
						$rslt=mysql_query($stmt, $link);
						$pc_recs = mysql_num_rows($rslt);
						if ($pc_recs > 0)
							{
							$dup_lead=1;
							$row=mysql_fetch_row($rslt);
							$dup_lead_list =	$row[0];
							}
						if ($dup_lead < 1)
							{
							if (eregi("$alt_phone$title$US$list_id",$phone_list))
								{$dup_lead++; $dup++;}
							}
						}

					if ( (strlen($phone_number)>6) and ($dup_lead<1) and ($list_id >= 100 ))
						{
						if (strlen($phone_code)<1) {$phone_code = '1';}

						if (eregi("TITLEALTPHONE",$dupcheck))
							{$phone_list .= "$alt_phone$title$US$list_id|";}
						else
							{$phone_list .= "$phone_number$US$list_id|";}


						$gmt_offset = lookup_gmt($phone_code,$USarea,$state,$LOCAL_GMT_OFF_STD,$Shour,$Smin,$Ssec,$Smon,$Smday,$Syear,$postalgmt,$postal_code);


						if (strlen($custom_SQL)>3)
							{
							$stmtZ = "INSERT INTO vicidial_list (lead_id,entry_date,modify_date,status,user,vendor_lead_code,source_id,list_id,gmt_offset_now,called_since_last_reset,phone_code,phone_number,title,first_name,middle_initial,last_name,address1,address2,address3,city,state,province,postal_code,country_code,gender,date_of_birth,alt_phone,email,security_phrase,comments,called_count,last_local_call_time,rank,owner,entry_list_id) values('','$entry_date','$modify_date','$status','$user','$vendor_lead_code','$source_id','$list_id','$gmt_offset','$called_since_last_reset','$phone_code','$phone_number','$title','$first_name','$middle_initial','$last_name','$address1','$address2','$address3','$city','$state','$province','$postal_code','$country_code','$gender','$date_of_birth','$alt_phone','$email','$security_phrase','$comments',0,'2008-01-01 00:00:00','$rank','$owner','$list_id');";
							$rslt=mysql_query($stmtZ, $link);
							$affected_rows = mysql_affected_rows($link);
							$lead_id = mysql_insert_id($link);
							if ($DB > 0) {echo "<!-- $affected_rows|$lead_id|$stmtZ -->";}
							if ($WeBRooTWritablE > 0) 
								{fwrite($stmt_file, $stmtZ."\r\n");}
							$multistmt='';

							$custom_SQL_query = "INSERT INTO custom_$list_id_override SET lead_id='$lead_id',$custom_SQL;";
							$rslt=mysql_query($custom_SQL_query, $link);
							$affected_rows = mysql_affected_rows($link);
							if ($DB > 0) {echo "<!-- $affected_rows|$custom_SQL_query -->";}
							}
						else
							{
							if ($multi_insert_counter > 8) 
								{
								### insert good deal into pending_transactions table ###
								$stmtZ = "INSERT INTO vicidial_list (lead_id,entry_date,modify_date,status,user,vendor_lead_code,source_id,list_id,gmt_offset_now,called_since_last_reset,phone_code,phone_number,title,first_name,middle_initial,last_name,address1,address2,address3,city,state,province,postal_code,country_code,gender,date_of_birth,alt_phone,email,security_phrase,comments,called_count,last_local_call_time,rank,owner,entry_list_id) values$multistmt('','$entry_date','$modify_date','$status','$user','$vendor_lead_code','$source_id','$list_id','$gmt_offset','$called_since_last_reset','$phone_code','$phone_number','$title','$first_name','$middle_initial','$last_name','$address1','$address2','$address3','$city','$state','$province','$postal_code','$country_code','$gender','$date_of_birth','$alt_phone','$email','$security_phrase','$comments',0,'2008-01-01 00:00:00','$rank','$owner','0');";
								$rslt=mysql_query($stmtZ, $link);
								if ($WeBRooTWritablE > 0) 
									{fwrite($stmt_file, $stmtZ."\r\n");}
								$multistmt='';
								$multi_insert_counter=0;
								}
							else
								{
								$multistmt .= "('','$entry_date','$modify_date','$status','$user','$vendor_lead_code','$source_id','$list_id','$gmt_offset','$called_since_last_reset','$phone_code','$phone_number','$title','$first_name','$middle_initial','$last_name','$address1','$address2','$address3','$city','$state','$province','$postal_code','$country_code','$gender','$date_of_birth','$alt_phone','$email','$security_phrase','$comments',0,'2008-01-01 00:00:00','$rank','$owner','0'),";
								$multi_insert_counter++;
								}
							}
					$good++;
				} else {
					if ($bad < 1000000)
						{
						if ( $list_id < 100 )
							{
							print "<BR></b><font size=1 color=red>record $total BAD- PHONE: $phone_number ROW: |$row[0]| INVALID ID DE LA LISTA</font><b>\n";
							}
						else
							{
							print "<BR></b><font size=1 color=red>record $total BAD- PHONE: $phone_number ROW: |$row[0]| DUP: $dup_lead  $dup_lead_list</font><b>\n";
							}
						}
					$bad++;
				}
				$total++;
				if ($total%100==0) {
					print "<script language='JavaScript1.2'>ShowProgress($good, $bad, $total, $dup, $post)</script>";
					usleep(1000);
					flush();
				}
			}
			if ($multi_insert_counter!=0) {
				$stmtZ = "INSERT INTO vicidial_list (lead_id,entry_date,modify_date,status,user,vendor_lead_code,source_id,list_id,gmt_offset_now,called_since_last_reset,phone_code,phone_number,title,first_name,middle_initial,last_name,address1,address2,address3,city,state,province,postal_code,country_code,gender,date_of_birth,alt_phone,email,security_phrase,comments,called_count,last_local_call_time,rank,owner,entry_list_id) values".substr($multistmt, 0, -1).";";
				mysql_query($stmtZ, $link);
				if ($WeBRooTWritablE > 0) 
					{fwrite($stmt_file, $stmtZ."\r\n");}
			}
			print "<BR><BR>Done</B> GOOD: $good &nbsp; &nbsp; &nbsp; BAD: $bad &nbsp; &nbsp; &nbsp; TOTAL: $total</font></center>";
		}
		print "<script language='JavaScript1.2'>document.forms[0].leadfile.disabled=false; document.forms[0].submit_file.disabled=false; document.forms[0].reload_page.disabled=false;</script>";
	} 

if ($leadfile) {
		$total=0; $good=0; $bad=0; $dup=0; $post=0; $phone_list='';

		### LOG INSERTION Admin Log Table ###
		$stmt="INSERT INTO vicidial_admin_log set event_date='$NOW_TIME', user='$PHP_AUTH_USER', ip_address='$ip', event_section='LISTAS', event_type='LOAD', record_id='$list_id_override', event_code='ADMIN LOAD LIST', event_sql='', event_notes='File Name: $leadfile_name';";
		if ($DB) {echo "|$stmt|\n";}
		$rslt=mysql_query($stmt, $link);

		if ($file_layout=="standard") {

	print "<script language='JavaScript1.2'>document.forms[0].leadfile.disabled=true; document.forms[0].submit_file.disabled=true; document.forms[0].reload_page.disabled=true;</script>";
	flush();

	if (!eregi(".csv", $leadfile_name) && !eregi(".xls", $leadfile_name)) {

		if ($WeBRooTWritablE > 0)
			{
			copy($LF_path, "$WeBServeRRooT/$admin_web_directory/vicidial_temp_file.txt");
			$lead_file = "./vicidial_temp_file.txt";
			}
		else
			{
			copy($LF_path, "/tmp/vicidial_temp_file.txt");
			$lead_file = "/tmp/vicidial_temp_file.txt";
			}
		$file=fopen("$lead_file", "r");
		if ($WeBRooTWritablE > 0)
			{$stmt_file=fopen("$WeBServeRRooT/$admin_web_directory/listloader_stmts.txt", "w");}

		$buffer=fgets($file, 4096);
		$tab_count=substr_count($buffer, "\t");
		$pipe_count=substr_count($buffer, "|");

		if ($tab_count>$pipe_count) {$delimiter="\t";  $delim_name="tab";} else {$delimiter="|";  $delim_name="pipe";}
		$field_check=explode($delimiter, $buffer);

		if (count($field_check)>=2) {
			flush();
			$file=fopen("$lead_file", "r");
			$total=0; $good=0; $bad=0; $dup=0; $post=0; $phone_list='';
			print "<center><font face='arial, helvetica' size=3 color='#009900'><B>Procesando$delim_name-delimited file... ($tab_count|$pipe_count)\n";
			if (strlen($list_id_override)>0) 
			{
			print "<BR><BR>ID DE LA LISTA OVERRIDE FOR THIS FILE: $list_id_override<BR><BR>";
			}
			if (strlen($phone_code_override)>0) 
			{
			print "<BR><BR>INVALIDACIÓN DEL CÓDIGO DEL TELÉFONO PARA ESTE ARCHIVO: $phone_code_override<BR><BR>\n";
			}
		while (!feof($file)) {
				$record++;
				$buffer=rtrim(fgets($file, 4096));
				$buffer=stripslashes($buffer);

				if (strlen($buffer)>0) {
					$row=explode($delimiter, eregi_replace("[\'\"]", "", $buffer));

					$pulldate=date("Y-m-d H:i:s");
					$entry_date =			"$pulldate";
					$modify_date =			"";
					$status =				"NEW";
					$user ="";
					$vendor_lead_code =		$row[0];
					$source_code =			$row[1];
					$source_id=$source_code;
					$list_id =				$row[2];
					$gmt_offset =			'0';
					$called_since_last_reset='N';
					$phone_code =			eregi_replace("[^0-9]", "", $row[3]);
					$phone_number =			eregi_replace("[^0-9]", "", $row[4]);
					$title =				$row[5];
					$first_name =			$row[6];
					$middle_initial =		$row[7];
					$last_name =			$row[8];
					$address1 =				$row[9];
					$address2 =				$row[10];
					$address3 =				$row[11];
					$city =$row[12];
					$state =				$row[13];
					$province =				$row[14];
					$postal_code =			$row[15];
					$country_code =			$row[16];
					$gender =				$row[17];
					$date_of_birth =		$row[18];
					$alt_phone =			eregi_replace("[^0-9]", "", $row[19]);
					$email =				$row[20];
					$security_phrase =		$row[21];
					$comments =				trim($row[22]);
					$rank =					$row[23];
					$owner =				$row[24];
						
					# replace ' " ` \ ; with nothing
					$vendor_lead_code =		eregi_replace($field_regx, "", $vendor_lead_code);
					$source_code =			eregi_replace($field_regx, "", $source_code);
					$source_id = 			eregi_replace($field_regx, "", $source_id);
					$list_id =				eregi_replace($field_regx, "", $list_id);
					$phone_code =			eregi_replace($field_regx, "", $phone_code);
					$phone_number =			eregi_replace($field_regx, "", $phone_number);
					$title =				eregi_replace($field_regx, "", $title);
					$first_name =			eregi_replace($field_regx, "", $first_name);
					$middle_initial =		eregi_replace($field_regx, "", $middle_initial);
					$last_name =			eregi_replace($field_regx, "", $last_name);
					$address1 =				eregi_replace($field_regx, "", $address1);
					$address2 =				eregi_replace($field_regx, "", $address2);
					$address3 =				eregi_replace($field_regx, "", $address3);
					$city =					eregi_replace($field_regx, "", $city);
					$state =				eregi_replace($field_regx, "", $state);
					$province =				eregi_replace($field_regx, "", $province);
					$postal_code =			eregi_replace($field_regx, "", $postal_code);
					$country_code =			eregi_replace($field_regx, "", $country_code);
					$gender =				eregi_replace($field_regx, "", $gender);
					$date_of_birth =		eregi_replace($field_regx, "", $date_of_birth);
					$alt_phone =			eregi_replace($field_regx, "", $alt_phone);
					$email =				eregi_replace($field_regx, "", $email);
					$security_phrase =		eregi_replace($field_regx, "", $security_phrase);
					$comments =				eregi_replace($field_regx, "", $comments);
					$rank =					eregi_replace($field_regx, "", $rank);
					$owner =				eregi_replace($field_regx, "", $owner);
					
					$USarea = 			substr($phone_number, 0, 3);

					if (strlen($list_id_override)>0) 
						{
						$list_id = $list_id_override;
						}
					if (strlen($phone_code_override)>0) 
						{
						$phone_code = $phone_code_override;
						}

					##### Check for duplicate phone numbers in vicidial_list table for all lists in a campaign #####
					if (eregi("DUPCAMP",$dupcheck))
						{
							$dup_lead=0;
							$dup_lists='';
						$stmt="select campaign_id from vicidial_lists where list_id='$list_id';";
						$rslt=mysql_query($stmt, $link);
						$ci_recs = mysql_num_rows($rslt);
						if ($ci_recs > 0)
							{
							$row=mysql_fetch_row($rslt);
							$dup_camp =			$row[0];

							$stmt="select list_id from vicidial_lists where campaign_id='$dup_camp';";
							$rslt=mysql_query($stmt, $link);
							$li_recs = mysql_num_rows($rslt);
							if ($li_recs > 0)
								{
								$L=0;
								while ($li_recs > $L)
									{
									$row=mysql_fetch_row($rslt);
									$dup_lists .=	"'$row[0]',";
									$L++;
									}
								$dup_lists = eregi_replace(",$",'',$dup_lists);

								$stmt="select list_id from vicidial_list where phone_number='$phone_number' and list_id IN($dup_lists) limit 1;";
								$rslt=mysql_query($stmt, $link);
								$pc_recs = mysql_num_rows($rslt);
								if ($pc_recs > 0)
									{
									$dup_lead=1;
									$row=mysql_fetch_row($rslt);
									$dup_lead_list =	$row[0];
									}
								if ($dup_lead < 1)
									{
									if (eregi("$phone_number$US$list_id",$phone_list))
										{$dup_lead++; $dup++;}
									}
								}
							}
						}

					##### Check for duplicate phone numbers in vicidial_list table entire database #####
					if (eregi("DUPSYS",$dupcheck))
						{
						$dup_lead=0;
						$stmt="select list_id from vicidial_list where phone_number='$phone_number';";
						$rslt=mysql_query($stmt, $link);
						$pc_recs = mysql_num_rows($rslt);
						if ($pc_recs > 0)
							{
							$dup_lead=1;
							$row=mysql_fetch_row($rslt);
							$dup_lead_list =	$row[0];
							}
						if ($dup_lead < 1)
							{
							if (eregi("$phone_number$US$list_id",$phone_list))
								{$dup_lead++; $dup++;}
							}
						}

					##### Check for duplicate phone numbers in vicidial_list table for one list_id #####
					if (eregi("DUPLIST",$dupcheck))
						{
						$dup_lead=0;
						$stmt="select count(*) from vicidial_list where phone_number='$phone_number' and list_id='$list_id';";
						$rslt=mysql_query($stmt, $link);
						$pc_recs = mysql_num_rows($rslt);
						if ($pc_recs > 0)
							{
							$row=mysql_fetch_row($rslt);
							$dup_lead =			$row[0];
							}
						if ($dup_lead < 1)
							{
							if (eregi("$phone_number$US$list_id",$phone_list))
								{$dup_lead++; $dup++;}
							}
						}

					##### Check for duplicate title and alt-phone in vicidial_list table for one list_id #####
					if (eregi("DUPTITLEALTPHONELIST",$dupcheck))
						{
						$dup_lead=0;
						$stmt="select count(*) from vicidial_list where title='$title' and alt_phone='$alt_phone' and list_id='$list_id';";
						$rslt=mysql_query($stmt, $link);
						$pc_recs = mysql_num_rows($rslt);
						if ($pc_recs > 0)
							{
							$row=mysql_fetch_row($rslt);
							$dup_lead =			$row[0];
							$dup_lead_list =	$list_id;
							}
						if ($dup_lead < 1)
							{
							if (eregi("$alt_phone$title$US$list_id",$phone_list))
								{$dup_lead++; $dup++;}
							}
						}

					##### Check for duplicate phone numbers in vicidial_list table entire database #####
					if (eregi("DUPTITLEALTPHONESYS",$dupcheck))
						{
						$dup_lead=0;
						$stmt="select list_id from vicidial_list where title='$title' and alt_phone='$alt_phone';";
						$rslt=mysql_query($stmt, $link);
						$pc_recs = mysql_num_rows($rslt);
						if ($pc_recs > 0)
							{
							$dup_lead=1;
							$row=mysql_fetch_row($rslt);
							$dup_lead_list =	$row[0];
							}
						if ($dup_lead < 1)
							{
							if (eregi("$alt_phone$title$US$list_id",$phone_list))
								{$dup_lead++; $dup++;}
							}
						}

					if ( (strlen($phone_number)>6) and ($dup_lead<1) and ($list_id >= 100 ))
						{
						if (strlen($phone_code)<1) {$phone_code = '1';}

						if (eregi("TITLEALTPHONE",$dupcheck))
							{$phone_list .= "$alt_phone$title$US$list_id|";}
						else
							{$phone_list .= "$phone_number$US$list_id|";}


						$gmt_offset = lookup_gmt($phone_code,$USarea,$state,$LOCAL_GMT_OFF_STD,$Shour,$Smin,$Ssec,$Smon,$Smday,$Syear,$postalgmt,$postal_code);


						if ($multi_insert_counter > 8) {
							### insert good deal into pending_transactions table ###
							$stmtZ = "INSERT INTO vicidial_list (lead_id,entry_date,modify_date,status,user,vendor_lead_code,source_id,list_id,gmt_offset_now,called_since_last_reset,phone_code,phone_number,title,first_name,middle_initial,last_name,address1,address2,address3,city,state,province,postal_code,country_code,gender,date_of_birth,alt_phone,email,security_phrase,comments,called_count,last_local_call_time,rank,owner,entry_list_id) values$multistmt('','$entry_date','$modify_date','$status','$user','$vendor_lead_code','$source_id','$list_id','$gmt_offset','$called_since_last_reset','$phone_code','$phone_number','$title','$first_name','$middle_initial','$last_name','$address1','$address2','$address3','$city','$state','$province','$postal_code','$country_code','$gender','$date_of_birth','$alt_phone','$email','$security_phrase','$comments',0,'2008-01-01 00:00:00','$rank','$owner','0');";
							$rslt=mysql_query($stmtZ, $link);
							if ($WeBRooTWritablE > 0) 
								{fwrite($stmt_file, $stmtZ."\r\n");}
							$multistmt='';
							$multi_insert_counter=0;

						} else {
							$multistmt .= "('','$entry_date','$modify_date','$status','$user','$vendor_lead_code','$source_id','$list_id','$gmt_offset','$called_since_last_reset','$phone_code','$phone_number','$title','$first_name','$middle_initial','$last_name','$address1','$address2','$address3','$city','$state','$province','$postal_code','$country_code','$gender','$date_of_birth','$alt_phone','$email','$security_phrase','$comments',0,'2008-01-01 00:00:00','$rank','$owner','0'),";
							$multi_insert_counter++;
						}

						$good++;
					} else {
						if ($bad < 1000000)
							{
							if ( $list_id < 100 )
								{
								print "<BR></b><font size=1 color=red>record $total BAD- PHONE: $phone_number ROW: |$row[0]| INVALID ID DE LA LISTA</font><b>\n";
								}
							else
								{
								print "<BR></b><font size=1 color=red>record $total BAD- PHONE: $phone_number ROW: |$row[0]| DUP: $dup_lead  $dup_lead_list</font><b>\n";
								}
							}
						$bad++;
					}
					$total++;
					if ($total%100==0) {
						print "<script language='JavaScript1.2'>ShowProgress($good, $bad, $total, $dup, $post)</script>";
						usleep(1000);
						flush();
					}
				}
			}
			if ($multi_insert_counter!=0) {
				$stmtZ = "INSERT INTO vicidial_list (lead_id,entry_date,modify_date,status,user,vendor_lead_code,source_id,list_id,gmt_offset_now,called_since_last_reset,phone_code,phone_number,title,first_name,middle_initial,last_name,address1,address2,address3,city,state,province,postal_code,country_code,gender,date_of_birth,alt_phone,email,security_phrase,comments,called_count,last_local_call_time,rank,owner,entry_list_id) values".substr($multistmt, 0, -1).";";
				mysql_query($stmtZ, $link);
				if ($WeBRooTWritablE > 0) 
					{fwrite($stmt_file, $stmtZ."\r\n");}
			}

			print "<BR><BR>Done</B> GOOD: $good &nbsp; &nbsp; &nbsp; BAD: $bad &nbsp; &nbsp; &nbsp; TOTAL: $total</font></center>";

		} else {
			print "<center><font face='arial, helvetica' size=3 color='#990000'><B>ERROR: El archivo no tiene el número requerido de los campos para procesarlo.</B></font></center>";
		}
	} else if (!eregi(".csv", $leadfile_name)) 
		{
		if ($WeBRooTWritablE > 0)
			{
			copy($LF_path, "$WeBServeRRooT/$admin_web_directory/vicidial_temp_file.xls");
			$lead_file = "$WeBServeRRooT/$admin_web_directory/vicidial_temp_file.xls";
			}
		else
			{
			copy($LF_path, "/tmp/vicidial_temp_file.xls");
			$lead_file = "/tmp/vicidial_temp_file.xls";
			}
		$file=fopen("$lead_file", "r");

	#	echo "|$WeBServeRRooT/$admin_web_directory/listloader.pl --forcelistid=$list_id_override --lead-file=$lead_file|";
		$dupcheckCLI=''; $postalgmtCLI='';
		if (eregi("DUPLIST",$dupcheck)) {$dupcheckCLI='--duplicate-check';}
		if (eregi("DUPCAMP",$dupcheck)) {$dupcheckCLI='--duplicate-campaign-check';}
		if (eregi("DUPSYS",$dupcheck)) {$dupcheckCLI='--duplicate-system-check';}
		if (eregi("DUPTITLEALTPHONELIST",$dupcheck)) {$dupcheckCLI='--duplicate-tap-list-check';}
		if (eregi("DUPTITLEALTPHONESYS",$dupcheck)) {$dupcheckCLI='--duplicate-tap-system-check';}
		if (eregi("POSTAL",$postalgmt)) {$postalgmtCLI='--postal-code-gmt';}
		passthru("$WeBServeRRooT/$admin_web_directory/listloader.pl --forcelistid=$list_id_override --forcephonecode=$phone_code_override --lead-file=$lead_file  $postalgmtCLI $dupcheckCLI");
	
		}
		else 
		{
		if ($WeBRooTWritablE > 0)
			{
			copy($LF_path, "$WeBServeRRooT/$admin_web_directory/vicidial_temp_file.csv");
			$lead_file = "$WeBServeRRooT/$admin_web_directory/vicidial_temp_file.csv";
			}
		else
			{
			copy($LF_path, "/tmp/vicidial_temp_file.csv");
			$lead_file = "/tmp/vicidial_temp_file.csv";
			}
		$file=fopen("$lead_file", "r");
		if ($WeBRooTWritablE > 0)
			{$stmt_file=fopen("$WeBServeRRooT/$admin_web_directory/listloader_stmts.txt", "w");}
		
		print "<center><font face='arial, helvetica' size=3 color='#009900'><B>ProcesandoCSV file... \n";

		if (strlen($list_id_override)>0) 
			{
			print "<BR><BR>ID DE LA LISTA OVERRIDE FOR THIS FILE: $list_id_override<BR><BR>";
			}
		if (strlen($phone_code_override)>0) 
			{
			print "<BR><BR>INVALIDACIÓN DEL CÓDIGO DEL TELÉFONO PARA ESTE ARCHIVO: $phone_code_override<BR><BR>";
			}

		while($row=fgetcsv($file, 1000, ",")) {
				$pulldate=date("Y-m-d H:i:s");
				$entry_date =			"$pulldate";
				$modify_date =			"";
				$status =				"NEW";
				$user ="";
				$vendor_lead_code =		$row[0];
				$source_code =			$row[1];
				$source_id=$source_code;
				$list_id =				$row[2];
				$gmt_offset =			'0';
				$called_since_last_reset='N';
				$phone_code =			eregi_replace("[^0-9]", "", $row[3]);
				$phone_number =			eregi_replace("[^0-9]", "", $row[4]);
				$title =				$row[5];
				$first_name =			$row[6];
				$middle_initial =		$row[7];
				$last_name =			$row[8];
				$address1 =				$row[9];
				$address2 =				$row[10];
				$address3 =				$row[11];
				$city =$row[12];
				$state =				$row[13];
				$province =				$row[14];
				$postal_code =			$row[15];
				$country_code =			$row[16];
				$gender =				$row[17];
				$date_of_birth =		$row[18];
				$alt_phone =			eregi_replace("[^0-9]", "", $row[19]);
				$email =				$row[20];
				$security_phrase =		$row[21];
				$comments =				trim($row[22]);
				$rank =					$row[23];
				$owner =				$row[24];
				
				# replace ' " ` \ ; with nothing
				$vendor_lead_code =		eregi_replace($field_regx, "", $vendor_lead_code);
				$source_code =			eregi_replace($field_regx, "", $source_code);
				$source_id = 			eregi_replace($field_regx, "", $source_id);
				$list_id =				eregi_replace($field_regx, "", $list_id);
				$phone_code =			eregi_replace($field_regx, "", $phone_code);
				$phone_number =			eregi_replace($field_regx, "", $phone_number);
				$title =				eregi_replace($field_regx, "", $title);
				$first_name =			eregi_replace($field_regx, "", $first_name);
				$middle_initial =		eregi_replace($field_regx, "", $middle_initial);
				$last_name =			eregi_replace($field_regx, "", $last_name);
				$address1 =				eregi_replace($field_regx, "", $address1);
				$address2 =				eregi_replace($field_regx, "", $address2);
				$address3 =				eregi_replace($field_regx, "", $address3);
				$city =					eregi_replace($field_regx, "", $city);
				$state =				eregi_replace($field_regx, "", $state);
				$province =				eregi_replace($field_regx, "", $province);
				$postal_code =			eregi_replace($field_regx, "", $postal_code);
				$country_code =			eregi_replace($field_regx, "", $country_code);
				$gender =				eregi_replace($field_regx, "", $gender);
				$date_of_birth =		eregi_replace($field_regx, "", $date_of_birth);
				$alt_phone =			eregi_replace($field_regx, "", $alt_phone);
				$email =				eregi_replace($field_regx, "", $email);
				$security_phrase =		eregi_replace($field_regx, "", $security_phrase);
				$comments =				eregi_replace($field_regx, "", $comments);
				$rank =					eregi_replace($field_regx, "", $rank);
				$owner =				eregi_replace($field_regx, "", $owner);
				
				$USarea = 			substr($phone_number, 0, 3);

					if (strlen($list_id_override)>0) 
						{
						$list_id = $list_id_override;
						}
					if (strlen($phone_code_override)>0) 
						{
						$phone_code = $phone_code_override;
						}

					##### Check for duplicate phone numbers in vicidial_list table for all lists in a campaign #####
					if (eregi("DUPCAMP",$dupcheck))
						{
							$dup_lead=0;
							$dup_lists='';
						$stmt="select campaign_id from vicidial_lists where list_id='$list_id';";
						$rslt=mysql_query($stmt, $link);
						$ci_recs = mysql_num_rows($rslt);
						if ($ci_recs > 0)
							{
							$row=mysql_fetch_row($rslt);
							$dup_camp =			$row[0];

							$stmt="select list_id from vicidial_lists where campaign_id='$dup_camp';";
							$rslt=mysql_query($stmt, $link);
							$li_recs = mysql_num_rows($rslt);
							if ($li_recs > 0)
								{
								$L=0;
								while ($li_recs > $L)
									{
									$row=mysql_fetch_row($rslt);
									$dup_lists .=	"'$row[0]',";
									$L++;
									}
								$dup_lists = eregi_replace(",$",'',$dup_lists);

								$stmt="select list_id from vicidial_list where phone_number='$phone_number' and list_id IN($dup_lists) limit 1;";
								$rslt=mysql_query($stmt, $link);
								$pc_recs = mysql_num_rows($rslt);
								if ($pc_recs > 0)
									{
									$dup_lead=1;
									$row=mysql_fetch_row($rslt);
									$dup_lead_list =	$row[0];
									}
								if ($dup_lead < 1)
									{
									if (eregi("$phone_number$US$list_id",$phone_list))
										{$dup_lead++; $dup++;}
									}
								}
							}
						}

					##### Check for duplicate phone numbers in vicidial_list table entire database #####
					if (eregi("DUPSYS",$dupcheck))
						{
						$dup_lead=0;
						$stmt="select list_id from vicidial_list where phone_number='$phone_number';";
						$rslt=mysql_query($stmt, $link);
						$pc_recs = mysql_num_rows($rslt);
						if ($pc_recs > 0)
							{
							$dup_lead=1;
							$row=mysql_fetch_row($rslt);
							$dup_lead_list =	$row[0];
							}
						if ($dup_lead < 1)
							{
							if (eregi("$phone_number$US$list_id",$phone_list))
								{$dup_lead++; $dup++;}
							}
						}

					##### Check for duplicate phone numbers in vicidial_list table for one list_id #####
					if (eregi("DUPLIST",$dupcheck))
						{
						$dup_lead=0;
						$stmt="select count(*) from vicidial_list where phone_number='$phone_number' and list_id='$list_id';";
						$rslt=mysql_query($stmt, $link);
						$pc_recs = mysql_num_rows($rslt);
						if ($pc_recs > 0)
							{
							$row=mysql_fetch_row($rslt);
							$dup_lead =			$row[0];
							}
						if ($dup_lead < 1)
							{
							if (eregi("$phone_number$US$list_id",$phone_list))
								{$dup_lead++; $dup++;}
							}
						}

					##### Check for duplicate title and alt-phone in vicidial_list table for one list_id #####
					if (eregi("DUPTITLEALTPHONELIST",$dupcheck))
						{
						$dup_lead=0;
						$stmt="select count(*) from vicidial_list where title='$title' and alt_phone='$alt_phone' and list_id='$list_id';";
						$rslt=mysql_query($stmt, $link);
						$pc_recs = mysql_num_rows($rslt);
						if ($pc_recs > 0)
							{
							$row=mysql_fetch_row($rslt);
							$dup_lead =			$row[0];
							$dup_lead_list =	$list_id;
							}
						if ($dup_lead < 1)
							{
							if (eregi("$alt_phone$title$US$list_id",$phone_list))
								{$dup_lead++; $dup++;}
							}
						}

					##### Check for duplicate phone numbers in vicidial_list table entire database #####
					if (eregi("DUPTITLEALTPHONESYS",$dupcheck))
						{
						$dup_lead=0;
						$stmt="select list_id from vicidial_list where title='$title' and alt_phone='$alt_phone';";
						$rslt=mysql_query($stmt, $link);
						$pc_recs = mysql_num_rows($rslt);
						if ($pc_recs > 0)
							{
							$dup_lead=1;
							$row=mysql_fetch_row($rslt);
							$dup_lead_list =	$row[0];
							}
						if ($dup_lead < 1)
							{
							if (eregi("$alt_phone$title$US$list_id",$phone_list))
								{$dup_lead++; $dup++;}
							}
						}

					if ( (strlen($phone_number)>6) and ($dup_lead<1) and ($list_id >= 100 ))
						{
						if (strlen($phone_code)<1) {$phone_code = '1';}

						if (eregi("TITLEALTPHONE",$dupcheck))
							{$phone_list .= "$alt_phone$title$US$list_id|";}
						else
							{$phone_list .= "$phone_number$US$list_id|";}

						$gmt_offset = lookup_gmt($phone_code,$USarea,$state,$LOCAL_GMT_OFF_STD,$Shour,$Smin,$Ssec,$Smon,$Smday,$Syear,$postalgmt,$postal_code);


					if ($multi_insert_counter > 8) {
						### insert good deal into pending_transactions table ###
						$stmtZ = "INSERT INTO vicidial_list (lead_id,entry_date,modify_date,status,user,vendor_lead_code,source_id,list_id,gmt_offset_now,called_since_last_reset,phone_code,phone_number,title,first_name,middle_initial,last_name,address1,address2,address3,city,state,province,postal_code,country_code,gender,date_of_birth,alt_phone,email,security_phrase,comments,called_count,last_local_call_time,rank,owner,entry_list_id) values$multistmt('','$entry_date','$modify_date','$status','$user','$vendor_lead_code','$source_id','$list_id','$gmt_offset','$called_since_last_reset','$phone_code','$phone_number','$title','$first_name','$middle_initial','$last_name','$address1','$address2','$address3','$city','$state','$province','$postal_code','$country_code','$gender','$date_of_birth','$alt_phone','$email','$security_phrase','$comments',0,'2008-01-01 00:00:00','$rank','$owner','0');";
						$rslt=mysql_query($stmtZ, $link);
						if ($WeBRooTWritablE > 0) 
							{fwrite($stmt_file, $stmtZ."\r\n");}
						$multistmt='';
						$multi_insert_counter=0;

					} else {
						$multistmt .= "('','$entry_date','$modify_date','$status','$user','$vendor_lead_code','$source_id','$list_id','$gmt_offset','$called_since_last_reset','$phone_code','$phone_number','$title','$first_name','$middle_initial','$last_name','$address1','$address2','$address3','$city','$state','$province','$postal_code','$country_code','$gender','$date_of_birth','$alt_phone','$email','$security_phrase','$comments',0,'2008-01-01 00:00:00','$rank','$owner','0'),";
						$multi_insert_counter++;
					}

					$good++;
				} else {
					if ($bad < 1000000)
						{
						if ( $list_id < 100 )
							{
							print "<BR></b><font size=1 color=red>record $total BAD- PHONE: $phone_number ROW: |$row[0]| INVALID ID DE LA LISTA</font><b>\n";
							}
						else
							{
							print "<BR></b><font size=1 color=red>record $total BAD- PHONE: $phone_number ROW: |$row[0]| DUP: $dup_lead  $dup_lead_list</font><b>\n";
							}
						}
					$bad++;
				}
				$total++;
				if ($total%100==0) {
					print "<script language='JavaScript1.2'>ShowProgress($good, $bad, $total, $dup, $post)</script>";
					usleep(1000);
					flush();
				}
			}
			if ($multi_insert_counter!=0) {
				$stmtZ = "INSERT INTO vicidial_list (lead_id,entry_date,modify_date,status,user,vendor_lead_code,source_id,list_id,gmt_offset_now,called_since_last_reset,phone_code,phone_number,title,first_name,middle_initial,last_name,address1,address2,address3,city,state,province,postal_code,country_code,gender,date_of_birth,alt_phone,email,security_phrase,comments,called_count,last_local_call_time,rank,owner,entry_list_id) values".substr($multistmt, 0, -1).";";
				mysql_query($stmtZ, $link);
				if ($WeBRooTWritablE > 0) 
					{fwrite($stmt_file, $stmtZ."\r\n");}
			}

			print "<BR><BR>Done</B> GOOD: $good &nbsp; &nbsp; &nbsp; BAD: $bad &nbsp; &nbsp; &nbsp; TOTAL: $total</font></center>";

		}
		print "<script language='JavaScript1.2'>document.forms[0].leadfile.disabled=false; document.forms[0].submit_file.disabled=false; document.forms[0].reload_page.disabled=false;</script>";

		} else {
			##### BEGIN field chooser #####
			print "<script language='JavaScript1.2'>document.forms[0].leadfile.disabled=true; document.forms[0].submit_file.disabled=true; document.forms[0].reload_page.disabled=true;</script><HR>";
			flush();
			print "<table border=0 cellpadding=3 cellspacing=0 width=700 align=center>\r\n";
			print "  <tr bgcolor='#330099'>\r\n";
			print "    <th align=right><font class='standard' color='white'>VICIDIAL Columna</font></th>\r\n";
			print "    <th><font class='standard' color='white'>Datos del archivo</font></th>\r\n";
			print "  </tr>\r\n";

			$fields_stmt = "SELECT vendor_lead_code, source_id, list_id, phone_code, phone_number, title, first_name, middle_initial, last_name, address1, address2, address3, city, state, province, postal_code, country_code, gender, date_of_birth, alt_phone, email, security_phrase, comments, rank, owner from vicidial_list limit 1";

			##### BEGIN custom fields columns list ###
			if ($custom_fields_enabled > 0)
				{
				$stmt="DEMOSTRACIÓN TABLAS LIKE \"custom_$list_id_override\";";
				if ($DB>0) {echo "$stmt\n";}
				$rslt=mysql_query($stmt, $link);
				$tablecount_to_print = mysql_num_rows($rslt);
				if ($tablecount_to_print > 0) 
					{
					$stmt="SELECT count(*) from vicidial_lists_fields where list_id='$list_id_override';";
					if ($DB>0) {echo "$stmt\n";}
					$rslt=mysql_query($stmt, $link);
					$fieldscount_to_print = mysql_num_rows($rslt);
					if ($fieldscount_to_print > 0) 
						{
						$rowx=mysql_fetch_row($rslt);
						$custom_records_count =	$rowx[0];

						$custom_SQL='';
						$stmt="SELECT field_id,field_label,field_name,field_description,field_rank,field_help,field_type,field_options,field_size,field_max,field_default,field_cost,field_required,multi_position,name_position,field_order from vicidial_lists_fields where list_id='$list_id_override' order by field_rank,field_order,field_label;";
						if ($DB>0) {echo "$stmt\n";}
						$rslt=mysql_query($stmt, $link);
						$fields_to_print = mysql_num_rows($rslt);
						$fields_list='';
						$o=0;
						while ($fields_to_print > $o) 
							{
							$rowx=mysql_fetch_row($rslt);
							$A_field_label[$o] =	$rowx[1];
							$A_field_type[$o] =		$rowx[6];

							if ($DB>0) {echo "$A_field_label[$o]|$A_field_type[$o]\n";}

							if ( ($A_field_type[$o]!='DISPLAY') and ($A_field_type[$o]!='SCRIPT') )
								{
								if (!preg_match("/\|$A_field_label[$o]\|/",$vicidial_list_fields))
									{
									$custom_SQL .= ",$A_field_label[$o]";
									}
								}
							$o++;
							}

						$fields_stmt = "SELECT vendor_lead_code, source_id, list_id, phone_code, phone_number, title, first_name, middle_initial, last_name, address1, address2, address3, city, state, province, postal_code, country_code, gender, date_of_birth, alt_phone, email, security_phrase, comments, rank, owner $custom_SQL from vicidial_list, custom_$list_id_override limit 1";

						}
					}
				}
			##### END custom fields columns list ###


			$rslt=mysql_query("$fields_stmt", $link);

			if (!eregi(".csv", $leadfile_name) && !eregi(".xls", $leadfile_name)) 
				{
				if ($WeBRooTWritablE > 0)
					{
					copy($LF_path, "$WeBServeRRooT/$admin_web_directory/vicidial_temp_file.txt");
					$lead_file = "$WeBServeRRooT/$admin_web_directory/vicidial_temp_file.txt";
					}
				else
					{
					copy($LF_path, "/tmp/vicidial_temp_file.txt");
					$lead_file = "/tmp/vicidial_temp_file.txt";
					}
				$file=fopen("$lead_file", "r");
				if ($WeBRooTWritablE > 0)
					{$stmt_file=fopen("$WeBServeRRooT/$admin_web_directory/listloader_stmts.txt", "w");}

				$buffer=fgets($file, 4096);
				$tab_count=substr_count($buffer, "\t");
				$pipe_count=substr_count($buffer, "|");

				if ($tab_count>$pipe_count) {$delimiter="\t";  $delim_name="tab";} else {$delimiter="|";  $delim_name="pipe";}
				$field_check=explode($delimiter, $buffer);
				flush();
				$file=fopen("$lead_file", "r");
				print "<center><font face='arial, helvetica' size=3 color='#009900'><B>Procesando$delim_name-delimited file...\n";

				if (strlen($list_id_override)>0) 
					{
					print "<BR><BR>ID DE LA LISTA OVERRIDE FOR THIS FILE: $list_id_override<BR><BR>";
					}
				if (strlen($phone_code_override)>0) 
					{
					print "<BR><BR>INVALIDACIÓN DEL CÓDIGO DEL TELÉFONO PARA ESTE ARCHIVO: $phone_code_override<BR><BR>";
					}
				$buffer=rtrim(fgets($file, 4096));
				$buffer=stripslashes($buffer);
				$row=explode($delimiter, eregi_replace("[\'\"]", "", $buffer));
				
				for ($i=0; $i<mysql_num_fields($rslt); $i++) {

					if ( (mysql_field_name($rslt, $i)=="list_id" and $list_id_override!="") or (mysql_field_name($rslt, $i)=="phone_code" and $phone_code_override!="") ) {
						print "<!-- skipping " . mysql_field_name($rslt, $i) . " -->\n";
					} else {
						print "  <tr bgcolor=#D9E6FE>\r\n";
						print "    <td align=right><font class=standard>".strtoupper(eregi_replace("_", " ", mysql_field_name($rslt, $i))).": </font></td>\r\n";
						print "    <td align=center><select name='".mysql_field_name($rslt, $i)."_field'>\r\n";
						print "     <option value='-1'>(none)</option>\r\n";
	
						for ($j=0; $j<count($row); $j++) {
							eregi_replace("\"", "", $row[$j]);
							print "     <option value='$j'>\"$row[$j]\"</option>\r\n";
						}
	
						print "    </select></td>\r\n";
						print "  </tr>\r\n";
					}

				}
			} 
			else if (!eregi(".csv", $leadfile_name)) 
			{
				if ($WeBRooTWritablE > 0)
					{
					copy($LF_path, "$WeBServeRRooT/$admin_web_directory/vicidial_temp_file.xls");
					$lead_file = "$WeBServeRRooT/$admin_web_directory/vicidial_temp_file.xls";
					}
				else
					{
					copy($LF_path, "/tmp/vicidial_temp_file.xls");
					$lead_file = "/tmp/vicidial_temp_file.xls";
					}

			#	echo "|$WeBServeRRooT/$admin_web_directory/listloader_rowdisplay.pl --lead-file=$lead_file|";
				$dupcheckCLI=''; $postalgmtCLI='';
				if (eregi("DUPLIST",$dupcheck)) {$dupcheckCLI='--duplicate-check';}
				if (eregi("DUPCAMP",$dupcheck)) {$dupcheckCLI='--duplicate-campaign-check';}
				if (eregi("DUPSYS",$dupcheck)) {$dupcheckCLI='--duplicate-system-check';}
				if (eregi("DUPTITLEALTPHONELIST",$dupcheck)) {$dupcheckCLI='--duplicate-tap-list-check';}
				if (eregi("DUPTITLEALTPHONESYS",$dupcheck)) {$dupcheckCLI='--duplicate-tap-system-check';}
				if (eregi("POSTAL",$postalgmt)) {$postalgmtCLI='--postal-code-gmt';}
				passthru("$WeBServeRRooT/$admin_web_directory/listloader_rowdisplay.pl --lead-file=$lead_file $postalgmtCLI $dupcheckCLI");
			} 
			else 
			{
				if ($WeBRooTWritablE > 0)
					{
					copy($LF_path, "$WeBServeRRooT/$admin_web_directory/vicidial_temp_file.csv");
					$lead_file = "$WeBServeRRooT/$admin_web_directory/vicidial_temp_file.csv";
					}
				else
					{
					copy($LF_path, "/tmp/vicidial_temp_file.csv");
					$lead_file = "/tmp/vicidial_temp_file.csv";
					}
				$file=fopen("$lead_file", "r");

				if ($WeBRooTWritablE > 0)
					{$stmt_file=fopen("$WeBServeRRooT/$admin_web_directory/listloader_stmts.txt", "w");}
				
				print "<center><font face='arial, helvetica' size=3 color='#009900'><B>ProcesandoCSV file... \n";
				
				if (strlen($list_id_override)>0) 
					{
					print "<BR><BR>ID DE LA LISTA OVERRIDE FOR THIS FILE: $list_id_override<BR><BR>";
					}
				if (strlen($phone_code_override)>0) 
					{
					print "<BR><BR>INVALIDACIÓN DEL CÓDIGO DEL TELÉFONO PARA ESTE ARCHIVO: $phone_code_override<BR><BR>";
					}

				$total=0; $good=0; $bad=0; $dup=0; $post=0; $phone_list='';
				$row=fgetcsv($file, 1000, ",");
				for ($i=0; $i<mysql_num_fields($rslt); $i++) {
					if ( (mysql_field_name($rslt, $i)=="list_id" and $list_id_override!="") or (mysql_field_name($rslt, $i)=="phone_code" and $phone_code_override!="") ) {
						print "<!-- skipping " . mysql_field_name($rslt, $i) . " -->\n";
					} else {
						print "  <tr bgcolor=#D9E6FE>\r\n";
						print "    <td align=right><font class=standard>".strtoupper(eregi_replace("_", " ", mysql_field_name($rslt, $i))).": </font></td>\r\n";
						print "    <td align=center><select name='".mysql_field_name($rslt, $i)."_field'>\r\n";
						print "     <option value='-1'>(none)</option>\r\n";

						for ($j=0; $j<count($row); $j++) {
							eregi_replace("\"", "", $row[$j]);
							print "     <option value='$j'>\"$row[$j]\"</option>\r\n";
						}

						print "    </select></td>\r\n";
						print "  </tr>\r\n";
					}
				}
			}
			print "  <tr bgcolor='#330099'>\r\n";
			print "  <input type=hidden name=dupcheck value=\"$dupcheck\">\r\n";
			print "  <input type=hidden name=postalgmt value=\"$postalgmt\">\r\n";
			print "  <input type=hidden name=lead_file value=\"$lead_file\">\r\n";
			print "  <input type=hidden name=list_id_override value=\"$list_id_override\">\r\n";
			print "  <input type=hidden name=phone_code_override value=\"$phone_code_override\">\r\n";
			print "    <th colspan=2><input type=submit name='OK_to_process' value='ACEPTABLE PROCESAR'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type=button onClick=\"javascript:document.location='new_listloader_superL.php'\" value=\"RECARGAR\" name='reload_page'></th>\r\n";
			print "  </tr>\r\n";
			print "</table>\r\n";
	# }
		print "<script language='JavaScript1.2'>document.forms[0].leadfile.disabled=false; document.forms[0].submit_file.disabled=false; document.forms[0].reload_page.disabled=false;</script>";

		##### END field chooser #####
	}
#} else if (filesize($leadfile)>8388608) {
#		print "<center><font face='arial, helvetica' size=3 color='#990000'><B>ERROR: File exceeds the 8MB limit.</B></font></center>";
}
?>
</form>
</body>
</html>





<?php

exit;



function lookup_gmt($phone_code,$USarea,$state,$LOCAL_GMT_OFF_STD,$Shour,$Smin,$Ssec,$Smon,$Smday,$Syear,$postalgmt,$postal_code)
{
global $link;

$postalgmt_found=0;
if ( (eregi("POSTAL",$postalgmt)) && (strlen($postal_code)>4) )
	{
	if (preg_match('/^1$/', $phone_code))
		{
		$stmt="select postal_code,state,GMT_offset,DST,DST_range,country,country_code from vicidial_postal_codes where country_code='$phone_code' and postal_code LIKE \"$postal_code%\";";
		$rslt=mysql_query($stmt, $link);
		$pc_recs = mysql_num_rows($rslt);
		if ($pc_recs > 0)
			{
			$row=mysql_fetch_row($rslt);
			$gmt_offset =	$row[2];	 $gmt_offset = eregi_replace("\+","",$gmt_offset);
			$dst =			$row[3];
			$dst_range =	$row[4];
			$PC_processed++;
			$postalgmt_found++;
			$post++;
			}
		}
	}
if ($postalgmt_found < 1)
	{
	$PC_processed=0;
	### UNITED STATES ###
	if ($phone_code =='1')
		{
		$stmt="select country_code,country,areacode,state,GMT_offset,DST,DST_range,geographic_description from vicidial_phone_codes where country_code='$phone_code' and areacode='$USarea';";
		$rslt=mysql_query($stmt, $link);
		$pc_recs = mysql_num_rows($rslt);
		if ($pc_recs > 0)
			{
			$row=mysql_fetch_row($rslt);
			$gmt_offset =	$row[4];	 $gmt_offset = eregi_replace("\+","",$gmt_offset);
			$dst =			$row[5];
			$dst_range =	$row[6];
			$PC_processed++;
			}
		}
	### MEXICO ###
	if ($phone_code =='52')
		{
		$stmt="select country_code,country,areacode,state,GMT_offset,DST,DST_range,geographic_description from vicidial_phone_codes where country_code='$phone_code' and areacode='$USarea';";
		$rslt=mysql_query($stmt, $link);
		$pc_recs = mysql_num_rows($rslt);
		if ($pc_recs > 0)
			{
			$row=mysql_fetch_row($rslt);
			$gmt_offset =	$row[4];	 $gmt_offset = eregi_replace("\+","",$gmt_offset);
			$dst =			$row[5];
			$dst_range =	$row[6];
			$PC_processed++;
			}
		}
	### AUSTRALIA ###
	if ($phone_code =='61')
		{
		$stmt="select country_code,country,areacode,state,GMT_offset,DST,DST_range,geographic_description from vicidial_phone_codes where country_code='$phone_code' and state='$state';";
		$rslt=mysql_query($stmt, $link);
		$pc_recs = mysql_num_rows($rslt);
		if ($pc_recs > 0)
			{
			$row=mysql_fetch_row($rslt);
			$gmt_offset =	$row[4];	 $gmt_offset = eregi_replace("\+","",$gmt_offset);
			$dst =			$row[5];
			$dst_range =	$row[6];
			$PC_processed++;
			}
		}
	### ALL OTHER COUNTRY CODES ###
	if (!$PC_processed)
		{
		$PC_processed++;
		$stmt="select country_code,country,areacode,state,GMT_offset,DST,DST_range,geographic_description from vicidial_phone_codes where country_code='$phone_code';";
		$rslt=mysql_query($stmt, $link);
		$pc_recs = mysql_num_rows($rslt);
		if ($pc_recs > 0)
			{
			$row=mysql_fetch_row($rslt);
			$gmt_offset =	$row[4];	 $gmt_offset = eregi_replace("\+","",$gmt_offset);
			$dst =			$row[5];
			$dst_range =	$row[6];
			$PC_processed++;
			}
		}
	}

### Find out if DST to raise the gmt offset ###
$AC_GMT_diff = ($gmt_offset - $LOCAL_GMT_OFF_STD);
$AC_localtime = mktime(($Shour + $AC_GMT_diff), $Smin, $Ssec, $Smon, $Smday, $Syear);
	$hour = date("H",$AC_localtime);
	$min = date("i",$AC_localtime);
	$sec = date("s",$AC_localtime);
	$mon = date("m",$AC_localtime);
	$mday = date("d",$AC_localtime);
	$wday = date("w",$AC_localtime);
	$year = date("Y",$AC_localtime);
$dsec = ( ( ($hour * 3600) + ($min * 60) ) + $sec );

$AC_processed=0;
if ( (!$AC_processed) and ($dst_range == 'SSM-FSN') )
	{
	if ($DBX) {print "     Second Domingo March to First Domingo November\n";}
	#**********************************************************************
	# SSM-FSN
	#     This is returns 1 if Daylight Savings Time is in effect and 0 if 
	#       Standard time is in effect.
	#     Based on Second Domingo March to First Domingo November at 2 am.
	#     INPUTS:
	#       mm              INTEGER       Month.
	#       dd              INTEGER       Day of the month.
	#       ns              INTEGER       Seconds into the day.
	#       dow             INTEGER       Day of week (0=Domingo, to 6=Sábado)
	#     OPTIONAL INPUT:
	#       timezone        INTEGER       hour difference UTC - local standard time
	#                                      (DEFAULT is blank)
	#                                     make calculations based on UTC time, 
	#                                     which means shift at 10:00 UTC in April
	#                                     and 9:00 UTC in October
	#     OUTPUT: 
	#                       INTEGER       1 = DST, 0 = not DST
	#
	# S  M  T  W  T  F  S
	# 1  2  3  4  5  6  7
	# 8  9 10 11 12 13 14
	#15 16 17 18 19 20 21
	#22 23 24 25 26 27 28
	#29 30 31
	# 
	# S  M  T  W  T  F  S
	#    1  2  3  4  5  6
	# 7  8  9 10 11 12 13
	#14 15 16 17 18 19 20
	#21 22 23 24 25 26 27
	#28 29 30 31
	# 
	#**********************************************************************

		$USACAN_DST=0;
		$mm = $mon;
		$dd = $mday;
		$ns = $dsec;
		$dow= $wday;

		if ($mm < 3 || $mm > 11) {
		$USACAN_DST=0;   
		} elseif ($mm >= 4 and $mm <= 10) {
		$USACAN_DST=1;   
		} elseif ($mm == 3) {
		if ($dd > 13) {
			$USACAN_DST=1;   
		} elseif ($dd >= ($dow+8)) {
			if ($timezone) {
			if ($dow == 0 and $ns < (7200+$timezone*3600)) {
				$USACAN_DST=0;   
			} else {
				$USACAN_DST=1;   
			}
			} else {
			if ($dow == 0 and $ns < 7200) {
				$USACAN_DST=0;   
			} else {
				$USACAN_DST=1;   
			}
			}
		} else {
			$USACAN_DST=0;   
		}
		} elseif ($mm == 11) {
		if ($dd > 7) {
			$USACAN_DST=0;   
		} elseif ($dd < ($dow+1)) {
			$USACAN_DST=1;   
		} elseif ($dow == 0) {
			if ($timezone) { # UTC calculations
			if ($ns < (7200+($timezone-1)*3600)) {
				$USACAN_DST=1;   
			} else {
				$USACAN_DST=0;   
			}
			} else { # tiempo local calculations
			if ($ns < 7200) {
				$USACAN_DST=1;   
			} else {
				$USACAN_DST=0;   
			}
			}
		} else {
			$USACAN_DST=0;   
		}
		} # end of month checks
	if ($DBX) {print "     DST: $USACAN_DST\n";}
	if ($USACAN_DST) {$gmt_offset++;}
	$AC_processed++;
	}

if ( (!$AC_processed) and ($dst_range == 'FSA-LSO') )
	{
	if ($DBX) {print "     First Domingo April to Last Domingo October\n";}
	#**********************************************************************
	# FSA-LSO
	#     This is returns 1 if Daylight Savings Time is in effect and 0 if 
	#       Standard time is in effect.
	#     Based on first Domingo in April and last Domingo in October at 2 am.
	#**********************************************************************
		
		$USA_DST=0;
		$mm = $mon;
		$dd = $mday;
		$ns = $dsec;
		$dow= $wday;

		if ($mm < 4 || $mm > 10) {
		$USA_DST=0;
		} elseif ($mm >= 5 and $mm <= 9) {
		$USA_DST=1;
		} elseif ($mm == 4) {
		if ($dd > 7) {
			$USA_DST=1;
		} elseif ($dd >= ($dow+1)) {
			if ($timezone) {
			if ($dow == 0 and $ns < (7200+$timezone*3600)) {
				$USA_DST=0;
			} else {
				$USA_DST=1;
			}
			} else {
			if ($dow == 0 and $ns < 7200) {
				$USA_DST=0;
			} else {
				$USA_DST=1;
			}
			}
		} else {
			$USA_DST=0;
		}
		} elseif ($mm == 10) {
		if ($dd < 25) {
			$USA_DST=1;
		} elseif ($dd < ($dow+25)) {
			$USA_DST=1;
		} elseif ($dow == 0) {
			if ($timezone) { # UTC calculations
			if ($ns < (7200+($timezone-1)*3600)) {
				$USA_DST=1;
			} else {
				$USA_DST=0;
			}
			} else { # tiempo local calculations
			if ($ns < 7200) {
				$USA_DST=1;
			} else {
				$USA_DST=0;
			}
			}
		} else {
			$USA_DST=0;
		}
		} # end of month checks

	if ($DBX) {print "     DST: $USA_DST\n";}
	if ($USA_DST) {$gmt_offset++;}
	$AC_processed++;
	}

if ( (!$AC_processed) and ($dst_range == 'LSM-LSO') )
	{
	if ($DBX) {print "     Last Domingo March to Last Domingo October\n";}
	#**********************************************************************
	#     This is s 1 if Daylight Savings Time is in effect and 0 if 
	#       Standard time is in effect.
	#     Based on last Domingo in March and last Domingo in October at 1 am.
	#**********************************************************************
		
		$GBR_DST=0;
		$mm = $mon;
		$dd = $mday;
		$ns = $dsec;
		$dow= $wday;

		if ($mm < 3 || $mm > 10) {
		$GBR_DST=0;
		} elseif ($mm >= 4 and $mm <= 9) {
		$GBR_DST=1;
		} elseif ($mm == 3) {
		if ($dd < 25) {
			$GBR_DST=0;
		} elseif ($dd < ($dow+25)) {
			$GBR_DST=0;
		} elseif ($dow == 0) {
			if ($timezone) { # UTC calculations
			if ($ns < (3600+($timezone-1)*3600)) {
				$GBR_DST=0;
			} else {
				$GBR_DST=1;
			}
			} else { # tiempo local calculations
			if ($ns < 3600) {
				$GBR_DST=0;
			} else {
				$GBR_DST=1;
			}
			}
		} else {
			$GBR_DST=1;
		}
		} elseif ($mm == 10) {
		if ($dd < 25) {
			$GBR_DST=1;
		} elseif ($dd < ($dow+25)) {
			$GBR_DST=1;
		} elseif ($dow == 0) {
			if ($timezone) { # UTC calculations
			if ($ns < (3600+($timezone-1)*3600)) {
				$GBR_DST=1;
			} else {
				$GBR_DST=0;
			}
			} else { # tiempo local calculations
			if ($ns < 3600) {
				$GBR_DST=1;
			} else {
				$GBR_DST=0;
			}
			}
		} else {
			$GBR_DST=0;
		}
		} # end of month checks
		if ($DBX) {print "     DST: $GBR_DST\n";}
	if ($GBR_DST) {$gmt_offset++;}
	$AC_processed++;
	}
if ( (!$AC_processed) and ($dst_range == 'LSO-LSM') )
	{
	if ($DBX) {print "     Last Domingo October to Last Domingo March\n";}
	#**********************************************************************
	#     This is s 1 if Daylight Savings Time is in effect and 0 if 
	#       Standard time is in effect.
	#     Based on last Domingo in October and last Domingo in March at 1 am.
	#**********************************************************************
		
		$AUS_DST=0;
		$mm = $mon;
		$dd = $mday;
		$ns = $dsec;
		$dow= $wday;

		if ($mm < 3 || $mm > 10) {
		$AUS_DST=1;
		} elseif ($mm >= 4 and $mm <= 9) {
		$AUS_DST=0;
		} elseif ($mm == 3) {
		if ($dd < 25) {
			$AUS_DST=1;
		} elseif ($dd < ($dow+25)) {
			$AUS_DST=1;
		} elseif ($dow == 0) {
			if ($timezone) { # UTC calculations
			if ($ns < (3600+($timezone-1)*3600)) {
				$AUS_DST=1;
			} else {
				$AUS_DST=0;
			}
			} else { # tiempo local calculations
			if ($ns < 3600) {
				$AUS_DST=1;
			} else {
				$AUS_DST=0;
			}
			}
		} else {
			$AUS_DST=0;
		}
		} elseif ($mm == 10) {
		if ($dd < 25) {
			$AUS_DST=0;
		} elseif ($dd < ($dow+25)) {
			$AUS_DST=0;
		} elseif ($dow == 0) {
			if ($timezone) { # UTC calculations
			if ($ns < (3600+($timezone-1)*3600)) {
				$AUS_DST=0;
			} else {
				$AUS_DST=1;
			}
			} else { # tiempo local calculations
			if ($ns < 3600) {
				$AUS_DST=0;
			} else {
				$AUS_DST=1;
			}
			}
		} else {
			$AUS_DST=1;
		}
		} # end of month checks						
	if ($DBX) {print "     DST: $AUS_DST\n";}
	if ($AUS_DST) {$gmt_offset++;}
	$AC_processed++;
	}

if ( (!$AC_processed) and ($dst_range == 'FSO-LSM') )
	{
	if ($DBX) {print "     First Domingo October to Last Domingo March\n";}
	#**********************************************************************
	#   TASMANIA ONLY
	#     This is s 1 if Daylight Savings Time is in effect and 0 if 
	#       Standard time is in effect.
	#     Based on first Domingo in October and last Domingo in March at 1 am.
	#**********************************************************************
		
		$AUST_DST=0;
		$mm = $mon;
		$dd = $mday;
		$ns = $dsec;
		$dow= $wday;

		if ($mm < 3 || $mm > 10) {
		$AUST_DST=1;
		} elseif ($mm >= 4 and $mm <= 9) {
		$AUST_DST=0;
		} elseif ($mm == 3) {
		if ($dd < 25) {
			$AUST_DST=1;
		} elseif ($dd < ($dow+25)) {
			$AUST_DST=1;
		} elseif ($dow == 0) {
			if ($timezone) { # UTC calculations
			if ($ns < (3600+($timezone-1)*3600)) {
				$AUST_DST=1;
			} else {
				$AUST_DST=0;
			}
			} else { # tiempo local calculations
			if ($ns < 3600) {
				$AUST_DST=1;
			} else {
				$AUST_DST=0;
			}
			}
		} else {
			$AUST_DST=0;
		}
		} elseif ($mm == 10) {
		if ($dd > 7) {
			$AUST_DST=1;
		} elseif ($dd >= ($dow+1)) {
			if ($timezone) {
			if ($dow == 0 and $ns < (7200+$timezone*3600)) {
				$AUST_DST=0;
			} else {
				$AUST_DST=1;
			}
			} else {
			if ($dow == 0 and $ns < 3600) {
				$AUST_DST=0;
			} else {
				$AUST_DST=1;
			}
			}
		} else {
			$AUST_DST=0;
		}
		} # end of month checks						
	if ($DBX) {print "     DST: $AUST_DST\n";}
	if ($AUST_DST) {$gmt_offset++;}
	$AC_processed++;
	}

if ( (!$AC_processed) and ($dst_range == 'FSO-FSA') )
	{
	if ($DBX) {print "     Domingo in October to First Domingo in April\n";}
	#**********************************************************************
	# FSO-FSA
	#   2008+ AUSTRALIA ONLY (country code 61)
	#     This is returns 1 if Daylight Savings Time is in effect and 0 if 
	#       Standard time is in effect.
	#     Based on first Domingo in October and first Domingo in April at 1 am.
	#**********************************************************************
    
	$AUSE_DST=0;
	$mm = $mon;
	$dd = $mday;
	$ns = $dsec;
	$dow= $wday;

    if ($mm < 4 or $mm > 10) {
	$AUSE_DST=1;   
    } elseif ($mm >= 5 and $mm <= 9) {
	$AUSE_DST=0;   
    } elseif ($mm == 4) {
	if ($dd > 7) {
	    $AUSE_DST=0;   
	} elseif ($dd >= ($dow+1)) {
	    if ($timezone) {
		if ($dow == 0 and $ns < (3600+$timezone*3600)) {
		    $AUSE_DST=1;   
		} else {
		    $AUSE_DST=0;   
		}
	    } else {
		if ($dow == 0 and $ns < 7200) {
		    $AUSE_DST=1;   
		} else {
		    $AUSE_DST=0;   
		}
	    }
	} else {
	    $AUSE_DST=1;   
	}
    } elseif ($mm == 10) {
	if ($dd >= 8) {
	    $AUSE_DST=1;   
	} elseif ($dd >= ($dow+1)) {
	    if ($timezone) {
		if ($dow == 0 and $ns < (7200+$timezone*3600)) {
		    $AUSE_DST=0;   
		} else {
		    $AUSE_DST=1;   
		}
	    } else {
		if ($dow == 0 and $ns < 3600) {
		    $AUSE_DST=0;   
		} else {
		    $AUSE_DST=1;   
		}
	    }
	} else {
	    $AUSE_DST=0;   
	}
    } # end of month checks
	if ($DBX) {print "     DST: $AUSE_DST\n";}
	if ($AUSE_DST) {$gmt_offset++;}
	$AC_processed++;
	}

if ( (!$AC_processed) and ($dst_range == 'FSO-TSM') )
	{
	if ($DBX) {print "     First Domingo October to Third Domingo March\n";}
	#**********************************************************************
	#     This is s 1 if Daylight Savings Time is in effect and 0 if 
	#       Standard time is in effect.
	#     Based on first Domingo in October and third Domingo in March at 1 am.
	#**********************************************************************
		
		$NZL_DST=0;
		$mm = $mon;
		$dd = $mday;
		$ns = $dsec;
		$dow= $wday;

		if ($mm < 3 || $mm > 10) {
		$NZL_DST=1;
		} elseif ($mm >= 4 and $mm <= 9) {
		$NZL_DST=0;
		} elseif ($mm == 3) {
		if ($dd < 14) {
			$NZL_DST=1;
		} elseif ($dd < ($dow+14)) {
			$NZL_DST=1;
		} elseif ($dow == 0) {
			if ($timezone) { # UTC calculations
			if ($ns < (3600+($timezone-1)*3600)) {
				$NZL_DST=1;
			} else {
				$NZL_DST=0;
			}
			} else { # tiempo local calculations
			if ($ns < 3600) {
				$NZL_DST=1;
			} else {
				$NZL_DST=0;
			}
			}
		} else {
			$NZL_DST=0;
		}
		} elseif ($mm == 10) {
		if ($dd > 7) {
			$NZL_DST=1;
		} elseif ($dd >= ($dow+1)) {
			if ($timezone) {
			if ($dow == 0 and $ns < (7200+$timezone*3600)) {
				$NZL_DST=0;
			} else {
				$NZL_DST=1;
			}
			} else {
			if ($dow == 0 and $ns < 3600) {
				$NZL_DST=0;
			} else {
				$NZL_DST=1;
			}
			}
		} else {
			$NZL_DST=0;
		}
		} # end of month checks						
	if ($DBX) {print "     DST: $NZL_DST\n";}
	if ($NZL_DST) {$gmt_offset++;}
	$AC_processed++;
	}

if ( (!$AC_processed) and ($dst_range == 'LSS-FSA') )
	{
	if ($DBX) {print "     Last Domingo in September to First Domingo in April\n";}
	#**********************************************************************
	# LSS-FSA
	#   2007+ NEW ZEALAND (country code 64)
	#     This is returns 1 if Daylight Savings Time is in effect and 0 if 
	#       Standard time is in effect.
	#     Based on last Domingo in September and first Domingo in April at 1 am.
	#**********************************************************************
    
	$NZLN_DST=0;
	$mm = $mon;
	$dd = $mday;
	$ns = $dsec;
	$dow= $wday;

    if ($mm < 4 || $mm > 9) {
	$NZLN_DST=1;   
    } elseif ($mm >= 5 && $mm <= 9) {
	$NZLN_DST=0;   
    } elseif ($mm == 4) {
	if ($dd > 7) {
	    $NZLN_DST=0;   
	} elseif ($dd >= ($dow+1)) {
	    if ($timezone) {
		if ($dow == 0 && $ns < (3600+$timezone*3600)) {
		    $NZLN_DST=1;   
		} else {
		    $NZLN_DST=0;   
		}
	    } else {
		if ($dow == 0 && $ns < 7200) {
		    $NZLN_DST=1;   
		} else {
		    $NZLN_DST=0;   
		}
	    }
	} else {
	    $NZLN_DST=1;   
	}
    } elseif ($mm == 9) {
	if ($dd < 25) {
	    $NZLN_DST=0;   
	} elseif ($dd < ($dow+25)) {
	    $NZLN_DST=0;   
	} elseif ($dow == 0) {
	    if ($timezone) { # UTC calculations
		if ($ns < (3600+($timezone-1)*3600)) {
		    $NZLN_DST=0;   
		} else {
		    $NZLN_DST=1;   
		}
	    } else { # tiempo local calculations
		if ($ns < 3600) {
		    $NZLN_DST=0;   
		} else {
		    $NZLN_DST=1;   
		}
	    }
	} else {
	    $NZLN_DST=1;   
	}
    } # end of month checks
	if ($DBX) {print "     DST: $NZLN_DST\n";}
	if ($NZLN_DST) {$gmt_offset++;}
	$AC_processed++;
	}

if ( (!$AC_processed) and ($dst_range == 'TSO-LSF') )
	{
	if ($DBX) {print "     Third Domingo October to Last Domingo February\n";}
	#**********************************************************************
	# TSO-LSF
	#     This is returns 1 if Daylight Savings Time is in effect and 0 if 
	#       Standard time is in effect. Brazil
	#     Based on Third Domingo October to Last Domingo February at 1 am.
	#**********************************************************************
		
		$BZL_DST=0;
		$mm = $mon;
		$dd = $mday;
		$ns = $dsec;
		$dow= $wday;

		if ($mm < 2 || $mm > 10) {
		$BZL_DST=1;   
		} elseif ($mm >= 3 and $mm <= 9) {
		$BZL_DST=0;   
		} elseif ($mm == 2) {
		if ($dd < 22) {
			$BZL_DST=1;   
		} elseif ($dd < ($dow+22)) {
			$BZL_DST=1;   
		} elseif ($dow == 0) {
			if ($timezone) { # UTC calculations
			if ($ns < (3600+($timezone-1)*3600)) {
				$BZL_DST=1;   
			} else {
				$BZL_DST=0;   
			}
			} else { # tiempo local calculations
			if ($ns < 3600) {
				$BZL_DST=1;   
			} else {
				$BZL_DST=0;   
			}
			}
		} else {
			$BZL_DST=0;   
		}
		} elseif ($mm == 10) {
		if ($dd < 22) {
			$BZL_DST=0;   
		} elseif ($dd < ($dow+22)) {
			$BZL_DST=0;   
		} elseif ($dow == 0) {
			if ($timezone) { # UTC calculations
			if ($ns < (3600+($timezone-1)*3600)) {
				$BZL_DST=0;   
			} else {
				$BZL_DST=1;   
			}
			} else { # tiempo local calculations
			if ($ns < 3600) {
				$BZL_DST=0;   
			} else {
				$BZL_DST=1;   
			}
			}
		} else {
			$BZL_DST=1;   
		}
		} # end of month checks
	if ($DBX) {print "     DST: $BZL_DST\n";}
	if ($BZL_DST) {$gmt_offset++;}
	$AC_processed++;
	}

if (!$AC_processed)
	{
	if ($DBX) {print "     No DST Method Found\n";}
	if ($DBX) {print "     DST: 0\n";}
	$AC_processed++;
	}

return $gmt_offset;
}

?>
</TD></TR></TABLE>
