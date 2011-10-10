<? 
# AST_admin_log_display.php
# 
# Copyright (C) 2008  Matt Florell <vicidial@gmail.com>    LICENSE: AGPLv2
#

require("dbconnect.php");

$PHP_AUTH_USER=$_SERVER['PHP_AUTH_USER'];
$PHP_AUTH_PW=$_SERVER['PHP_AUTH_PW'];
$PHP_SELF=$_SERVER['PHP_SELF'];
if (isset($_GET["query_date"]))				{$query_date=$_GET["query_date"];}
	elseif (isset($_POST["query_date"]))		{$query_date=$_POST["query_date"];}
if (isset($_GET["submit"]))				{$submit=$_GET["submit"];}
	elseif (isset($_POST["submit"]))		{$submit=$_POST["submit"];}
if (isset($_GET["SUBMIT"]))				{$SUBMIT=$_GET["SUBMIT"];}
	elseif (isset($_POST["SUBMIT"]))		{$SUBMIT=$_POST["SUBMIT"];}

$PHP_AUTH_USER = ereg_replace("[^0-9a-zA-Z]","",$PHP_AUTH_USER);
$PHP_AUTH_PW = ereg_replace("[^0-9a-zA-Z]","",$PHP_AUTH_PW);
$query_date = ereg_replace("[^ \.\,-\_0-9a-zA-Z]","",$query_date);

# AST GUI database administration
# AST_admin_log_display.php
# 
# CHANGES
# 50325-0932 - First build
# 51123-1443 - removed globals=on requirement
# 60421-1229 - check GET/POST vars lines with isset to not trigger PHP NOTICES
# 60620-1044 - Added variable filtering to eliminate SQL injection attack threat
#

$version = '0.0.4';
$build = '60620-1044';

$STARTtime = date("U");

	$stmt="SELECT count(*) from vicidial_users where user='$PHP_AUTH_USER' and pass='$PHP_AUTH_PW' and user_level > 8 and view_reports='1';";
	if ($DB) {echo "|$stmt|\n";}
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
    echo "Unzulässiges Username/Kennwort:|$PHP_AUTH_USER|$PHP_AUTH_PW|\n";
    exit;
	}
  else
	{

	if($auth>0)
		{
		$office_no=strtoupper($PHP_AUTH_USER);
		$password=strtoupper($PHP_AUTH_PW);
			$stmt="SELECT full_name from vicidial_users where user='$PHP_AUTH_USER' and pass='$PHP_AUTH_PW'";
			$rslt=mysql_query($stmt, $link);
			$row=mysql_fetch_row($rslt);
			$LOGfullname=$row[0];
		fwrite ($fp, "VICIDIAL|GOOD|$date|$PHP_AUTH_USER|$PHP_AUTH_PW|$ip|$browser|$LOGfullname|\n");
		fclose($fp);
		}
	else
		{
		fwrite ($fp, "VICIDIAL|FAIL|$date|$PHP_AUTH_USER|$PHP_AUTH_PW|$ip|$browser|\n");
		fclose($fp);
		}
	}

echo "<html>\n";
echo "<head>\n";
echo "<META HTTP-EQUIV=\"Content-Type\" CONTENT=\"text/html; charset=utf-8\">\n";
echo "<!-- VERSION: $version     BAU: $build      ADD: $ADD-->\n";
echo "<title>VICIDIAL ADMIN LOG DISPLAY</title>";
echo "</head>\n";

# Fri, 25 Mar 2005

$DAY_DATE = date("j");
if ($DAY_DATE < 10)
#	{$GREP_DATE = date("D,  j M Y");}
	{$GREP_DATE = date("D, 0j M Y");}
else
	{$GREP_DATE = date("D, j M Y");}

$NOW_DATE = date("Y-m-d");
$NOW_TIME = date("Y-m-d H:i:s");
$STARTtime = date("U");
if (!$query_date) {$query_date = $GREP_DATE;}

$Gquery_date = eregi_replace(" ",'\ ',$query_date);
echo "<!-- |$query_date|$Gquery_date| -->\n";

echo "<FORM ACTION=\"$PHP_SELF\" METHOD=GET>\n";
echo "<INPUT TYPE=TEXT NAME=query_date SIZE=20 MAXLENGTH=20 VALUE=\"$query_date\">\n";
echo "<INPUT TYPE=Submit NAME=SUBMIT VALUE=SUBMIT>\n";
echo "</FORM>\n\n";

echo "<PRE><FONT SIZE=2>\n\n";


echo "VICIDIAL Änderung der Registrierung ADMIN                             $NOW_TIME\n";

#passthru("grep $Gquery_date /home/www/htdocs/vicidial/admin_changes_log.txt");
passthru("grep $Gquery_date $WeBServeRRooT/vicidial/admin_changes_log.txt");


?>
</PRE>

</BODY></HTML>