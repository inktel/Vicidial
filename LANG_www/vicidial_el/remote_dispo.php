<?php
# remote_dispo.php
# 
# Copyright (C) 2009  Matt Florell <vicidial@gmail.com>    LICENSE: AGPLv2
#
# this is the remote agent disposition screen for calls sent to remote agents. 
# This allows the remote agent to modify customer information and disposition 
# the call
#
# CHANGES
#
# 60619-1626 - Added variable filtering to eliminate SQL injection attack threat
# 90508-0644 - Changed to PHP long tags
#


require("dbconnect.php");

$PHP_AUTH_USER=$_SERVER['PHP_AUTH_USER'];
$PHP_AUTH_PW=$_SERVER['PHP_AUTH_PW'];
$PHP_SELF=$_SERVER['PHP_SELF'];
if (isset($_GET["address1"]))				{$address1=$_GET["address1"];}
	elseif (isset($_POST["address1"]))		{$address1=$_POST["address1"];}
if (isset($_GET["address2"]))				{$address2=$_GET["address2"];}
	elseif (isset($_POST["address2"]))		{$address2=$_POST["address2"];}
if (isset($_GET["address3"]))				{$address3=$_GET["address3"];}
	elseif (isset($_POST["address3"]))		{$address3=$_POST["address3"];}
if (isset($_GET["alt_phone"]))				{$alt_phone=$_GET["alt_phone"];}
	elseif (isset($_POST["alt_phone"]))		{$alt_phone=$_POST["alt_phone"];}
if (isset($_GET["call_began"]))				{$call_began=$_GET["call_began"];}
	elseif (isset($_POST["call_began"]))		{$call_began=$_POST["call_began"];}
if (isset($_GET["campaign_id"]))				{$campaign_id=$_GET["campaign_id"];}
	elseif (isset($_POST["campaign_id"]))		{$campaign_id=$_POST["campaign_id"];}
if (isset($_GET["channel"]))				{$channel=$_GET["channel"];}
	elseif (isset($_POST["channel"]))		{$channel=$_POST["channel"];}
if (isset($_GET["channel_group"]))				{$channel_group=$_GET["channel_group"];}
	elseif (isset($_POST["channel_group"]))		{$channel_group=$_POST["channel_group"];}
if (isset($_GET["city"]))				{$city=$_GET["city"];}
	elseif (isset($_POST["city"]))		{$city=$_POST["city"];}
if (isset($_GET["comments"]))				{$comments=$_GET["comments"];}
	elseif (isset($_POST["comments"]))		{$comments=$_POST["comments"];}
if (isset($_GET["country_code"]))				{$country_code=$_GET["country_code"];}
	elseif (isset($_POST["country_code"]))		{$country_code=$_POST["country_code"];}
if (isset($_GET["customer_zap_channel"]))				{$customer_zap_channel=$_GET["customer_zap_channel"];}
	elseif (isset($_POST["customer_zap_channel"]))		{$customer_zap_channel=$_POST["customer_zap_channel"];}
if (isset($_GET["DB"]))				{$DB=$_GET["DB"];}
	elseif (isset($_POST["DB"]))		{$DB=$_POST["DB"];}
if (isset($_GET["dispo"]))				{$dispo=$_GET["dispo"];}
	elseif (isset($_POST["dispo"]))		{$dispo=$_POST["dispo"];}
if (isset($_GET["email"]))				{$email=$_GET["email"];}
	elseif (isset($_POST["email"]))		{$email=$_POST["email"];}
if (isset($_GET["end_call"]))				{$end_call=$_GET["end_call"];}
	elseif (isset($_POST["end_call"]))		{$end_call=$_POST["end_call"];}
if (isset($_GET["extension"]))				{$extension=$_GET["extension"];}
	elseif (isset($_POST["extension"]))		{$extension=$_POST["extension"];}
if (isset($_GET["first_name"]))				{$first_name=$_GET["first_name"];}
	elseif (isset($_POST["first_name"]))		{$first_name=$_POST["first_name"];}
if (isset($_GET["group"]))				{$group=$_GET["group"];}
	elseif (isset($_POST["group"]))		{$group=$_POST["group"];}
if (isset($_GET["last_name"]))				{$last_name=$_GET["last_name"];}
	elseif (isset($_POST["last_name"]))		{$last_name=$_POST["last_name"];}
if (isset($_GET["lead_id"]))				{$lead_id=$_GET["lead_id"];}
	elseif (isset($_POST["lead_id"]))		{$lead_id=$_POST["lead_id"];}
if (isset($_GET["list_id"]))				{$list_id=$_GET["list_id"];}
	elseif (isset($_POST["list_id"]))		{$list_id=$_POST["list_id"];}
if (isset($_GET["parked_time"]))				{$parked_time=$_GET["parked_time"];}
	elseif (isset($_POST["parked_time"]))		{$parked_time=$_POST["parked_time"];}
if (isset($_GET["pass"]))				{$pass=$_GET["pass"];}
	elseif (isset($_POST["pass"]))		{$pass=$_POST["pass"];}
if (isset($_GET["phone_code"]))				{$phone_code=$_GET["phone_code"];}
	elseif (isset($_POST["phone_code"]))		{$phone_code=$_POST["phone_code"];}
if (isset($_GET["phone_number"]))				{$phone_number=$_GET["phone_number"];}
	elseif (isset($_POST["phone_number"]))		{$phone_number=$_POST["phone_number"];}
if (isset($_GET["phone"]))				{$phone=$_GET["phone"];}
	elseif (isset($_POST["phone"]))		{$phone=$_POST["phone"];}
if (isset($_GET["postal_code"]))				{$postal_code=$_GET["postal_code"];}
	elseif (isset($_POST["postal_code"]))		{$postal_code=$_POST["postal_code"];}
if (isset($_GET["province"]))				{$province=$_GET["province"];}
	elseif (isset($_POST["province"]))		{$province=$_POST["province"];}
if (isset($_GET["security"]))				{$security=$_GET["security"];}
	elseif (isset($_POST["security"]))		{$security=$_POST["security"];}
if (isset($_GET["server_ip"]))				{$server_ip=$_GET["server_ip"];}
	elseif (isset($_POST["server_ip"]))		{$server_ip=$_POST["server_ip"];}
if (isset($_GET["server_ip"]))				{$server_ip=$_GET["server_ip"];}
	elseif (isset($_POST["server_ip"]))		{$server_ip=$_POST["server_ip"];}
if (isset($_GET["session_id"]))				{$session_id=$_GET["session_id"];}
	elseif (isset($_POST["session_id"]))		{$session_id=$_POST["session_id"];}
if (isset($_GET["state"]))				{$state=$_GET["state"];}
	elseif (isset($_POST["state"]))		{$state=$_POST["state"];}
if (isset($_GET["status"]))				{$status=$_GET["status"];}
	elseif (isset($_POST["status"]))		{$status=$_POST["status"];}
if (isset($_GET["tsr"]))				{$tsr=$_GET["tsr"];}
	elseif (isset($_POST["tsr"]))		{$tsr=$_POST["tsr"];}
if (isset($_GET["user"]))				{$user=$_GET["user"];}
	elseif (isset($_POST["user"]))		{$user=$_POST["user"];}
if (isset($_GET["vendor_id"]))				{$vendor_id=$_GET["vendor_id"];}
	elseif (isset($_POST["vendor_id"]))		{$vendor_id=$_POST["vendor_id"];}
if (isset($_GET["submit"]))				{$submit=$_GET["submit"];}
	elseif (isset($_POST["submit"]))		{$submit=$_POST["submit"];}
if (isset($_GET["ΕΠΙΒΕΒΑΙΩΣΗ"]))				{$ΕΠΙΒΕΒΑΙΩΣΗ=$_GET["ΕΠΙΒΕΒΑΙΩΣΗ"];}
	elseif (isset($_POST["ΕΠΙΒΕΒΑΙΩΣΗ"]))		{$ΕΠΙΒΕΒΑΙΩΣΗ=$_POST["ΕΠΙΒΕΒΑΙΩΣΗ"];}

$STARTtime = date("U");
$TODAY = date("Y-m-d");
$NOW_TIME = date("Y-m-d H:i:s");
$FILE_datetime = $STARTtime;

$ext_context = 'demo';
if (!isset($begin_date)) {$begin_date = $TODAY;}
if (!isset($end_date)) {$end_date = $TODAY;}


$PHP_AUTH_USER = ereg_replace("[^0-9a-zA-Z]","",$PHP_AUTH_USER);
$PHP_AUTH_PW = ereg_replace("[^0-9a-zA-Z]","",$PHP_AUTH_PW);



	$stmt="SELECT count(*) from vicidial_users where user='$PHP_AUTH_USER' and pass='$PHP_AUTH_PW' and user_level > 2;";
		if ($DB) {echo "$stmt\n";}
	$rslt=mysql_query($stmt, $link);
	$row=mysql_fetch_row($rslt);
	$auth=$row[0];

$fp = fopen ("./project_auth_entries.txt", "a");
$date = date("r");
$ip = getenv("REMOTE_ADDR");
$browser = getenv("HTTP_USER_AGENT");

  if( (strlen($PHP_AUTH_USER)<2) or (strlen($PHP_AUTH_PW)<2) or (!$auth))
	{
    Header("WWW-Authenticate: Basic realm=\"VICIDIAL-CLOSER\"");
    Header("HTTP/1.0 401 Unauthorized");
    echo "Ακυρο Ονομα Χρήστη/Κωδικός Πρόσβασης: |$PHP_AUTH_USER|$PHP_AUTH_PW|\n";
    exit;
	}
  else
	{

	if($auth>0)
		{
		$office_no=strtoupper($PHP_AUTH_USER);
		$password=strtoupper($PHP_AUTH_PW);
			$stmt="SELECT full_name from vicidial_users where user='$PHP_AUTH_USER' and pass='$PHP_AUTH_PW'";
			if ($DB) {echo "$stmt\n";}
			$rslt=mysql_query($stmt, $link);
			$row=mysql_fetch_row($rslt);
			$LOGfullname=$row[0];
			$fullname = $row[0];
		fwrite ($fp, "VD_CLOSER|GOOD|$date|$PHP_AUTH_USER|$PHP_AUTH_PW|$ip|$browser|$LOGfullname|\n");
		fclose($fp);

		}
	else
		{
		fwrite ($fp, "VD_CLOSER|FAIL|$date|$PHP_AUTH_USER|$PHP_AUTH_PW|$ip|$browser|\n");
		fclose($fp);
		}
	}

?>
<html>
<head>
<title>Απομακρυσμένο: Τερματισμός Κλήσης</title>
<?php
echo "<META HTTP-EQUIV=\"Content-Type\" CONTENT=\"text/html; charset=utf-8\">\n";
?>
</head>
<BODY BGCOLOR=white marginheight=0 marginwidth=0 leftmargin=0 topmargin=0>
<CENTER><FONT FACE="Courier" COLOR=BLACK SIZE=3>

<?php 

echo "<!-- $call_began $lead_id -->";

if ($end_call > 0)
{

$call_length = ($STARTtime - $call_began);

	### insert a NEW record to the vicidial_closer_log table 
	$stmt="UPDATE vicidial_closer_log set end_epoch='$STARTtime', length_in_sec='" . mysql_real_escape_string($call_length) . "', status='" . mysql_real_escape_string($status) . "', user='$PHP_AUTH_USER' where lead_id='" . mysql_real_escape_string($lead_id) . "' order by start_epoch desc limit 1;";
	if ($DB) {echo "|$stmt|\n";}
	$rslt=mysql_query($stmt, $link);

	### update the lead record in the vicidial_list table 
	$stmt="UPDATE vicidial_list set status='" . mysql_real_escape_string($status) . "',first_name='" . mysql_real_escape_string($first_name) . "',last_name='" . mysql_real_escape_string($last_name) . "',address1='" . mysql_real_escape_string($address1) . "',address2='" . mysql_real_escape_string($address2) . "',address3='" . mysql_real_escape_string($address3) . "',city='" . mysql_real_escape_string($city) . "',state='" . mysql_real_escape_string($state) . "',province='" . mysql_real_escape_string($province) . "',postal_code='" . mysql_real_escape_string($postal_code) . "',country_code='" . mysql_real_escape_string($country_code) . "',alt_phone='" . mysql_real_escape_string($alt_phone) . "',email='" . mysql_real_escape_string($email) . "',security_phrase='" . mysql_real_escape_string($security) . "',comments='" . mysql_real_escape_string($comments) . "',user='$PHP_AUTH_USER' where lead_id='" . mysql_real_escape_string($lead_id) . "'";
	if ($DB) {echo "|$stmt|\n";}
	$rslt=mysql_query($stmt, $link);

	echo "Η κλήση έχει τερματίσει &nbsp; &nbsp; &nbsp; $NOW_TIME\n<BR><BR>\n";

	echo "<form><input type=button value=\"Κλείστε αυτό το Παράθυρο\" onClick=\"javascript:window.close();\"></form>\n";

}
else
{
	$stmt="SELECT count(*) from vicidial_list where lead_id='" . mysql_real_escape_string($lead_id) . "'";
	$rslt=mysql_query($stmt, $link);
	if ($DB) {echo "$stmt\n";}
	$row=mysql_fetch_row($rslt);
	$lead_count = $row[0];

	if ($lead_count > 0)
	{

		$stmt="SELECT lead_id,entry_date,modify_date,status,user,vendor_lead_code,source_id,list_id,gmt_offset_now,called_since_last_reset,phone_code,phone_number,title,first_name,middle_initial,last_name,address1,address2,address3,city,state,province,postal_code,country_code,gender,date_of_birth,alt_phone,email,security_phrase,comments,called_count,last_local_call_time,rank,owner from vicidial_list where lead_id='" . mysql_real_escape_string($lead_id) . "'";
		$rslt=mysql_query($stmt, $link);
		if ($DB) {echo "$stmt\n";}
		$row=mysql_fetch_row($rslt);
		   $lead_id			= "$row[0]";
		   $tsr				= "$row[4]";
		   $vendor_id		= "$row[5]";
		   $list_id			= "$row[7]";
		   $campaign_id		= "$row[8]";
		   $phone_code		= "$row[10]";
		   $phone_number	= "$row[11]";
		   $title			= "$row[12]";
		   $first_name		= "$row[13]";	#
		   $middle_initial	= "$row[14]";
		   $last_name		= "$row[15]";	#
		   $address1		= "$row[16]";	#
		   $address2		= "$row[17]";	#
		   $address3		= "$row[18]";	#
		   $city			= "$row[19]";	#
		   $state			= "$row[20]";	#
		   $province		= "$row[21]";	#
		   $postal_code		= "$row[22]";	#
		   $country_code	= "$row[23]";	#
		   $gender			= "$row[24]";
		   $date_of_birth	= "$row[25]";
		   $alt_phone		= "$row[26]";	#
		   $email			= "$row[27]";	#
		   $security		= "$row[28]";	#
		   $comments		= "$row[29]";	#

		echo "<br>Πληροφορίες Κλήσης: $first_name $last_name - $phone_number<br><br><form action=$PHP_SELF method=POST>\n";
		echo "<input type=hidden name=end_call value=1>\n";
		echo "<input type=hidden name=DB value=\"$DB\">\n";
		echo "<input type=hidden name=lead_id value=\"$lead_id\">\n";
		echo "<input type=hidden name=list_id value=\"$list_id\">\n";
		echo "<input type=hidden name=campaign_id value=\"$campaign_id\">\n";
		echo "<input type=hidden name=phone_code value=\"$phone_code\">\n";
		echo "<input type=hidden name=phone_number value=\"$phone_number\">\n";
		echo "<input type=hidden name=server_ip value=\"$server_ip\">\n";
		echo "<input type=hidden name=extension value=\"$extension\">\n";
		echo "<input type=hidden name=channel value=\"$channel\">\n";
		echo "<input type=hidden name=call_began value=\"$call_began\">\n";
		echo "<input type=hidden name=parked_time value=\"$parked_time\">\n";
		echo "<table cellpadding=1 cellspacing=0>\n";
		echo "<tr><td colspan=2>Vendor ID: $vendor_id &nbsp; &nbsp; ID Εκστρατείας: $campaign_id</td></tr>\n";
		echo "<tr><td colspan=2>Fronter: $tsr &nbsp; &nbsp; ID Λίστας: $list_id</td></tr>\n";
		echo "<tr><td align=right>Ονομα: </td><td align=left><input type=text name=first_name size=15 maxlength=30 value=\"$first_name\"> &nbsp; \n";
		echo " Επίθετο: <input type=text name=last_name size=15 maxlength=30 value=\"$last_name\"> </td></tr>\n";
		echo "<tr><td align=right>Διεύθυνση 1 : </td><td align=left><input type=text name=address1 size=30 maxlength=30 value=\"$address1\"></td></tr>\n";
		echo "<tr><td align=right>Διεύθυνση 2 : </td><td align=left><input type=text name=address2 size=30 maxlength=30 value=\"$address2\"></td></tr>\n";
		echo "<tr><td align=right>Διεύθυνση 3 : </td><td align=left><input type=text name=address3 size=30 maxlength=30 value=\"$address3\"></td></tr>\n";
		echo "<tr><td align=right>Πόλη : </td><td align=left><input type=text name=city size=30 maxlength=30 value=\"$city\"></td></tr>\n";
		echo "<tr><td align=right>Κράτος: </td><td align=left><input type=text name=state size=2 maxlength=2 value=\"$state\"> &nbsp; \n";
		echo "Ταχ.Κωδ. <input type=text name=postal_code size=10 maxlength=10 value=\"$postal_code\"> </td></tr>\n";

		echo "<tr><td align=rightΕπαρχία: </td><td align=left><input type=text name=province size=30 maxlength=30 value=\"$province\"></td></tr>\n";
		echo "<tr><td align=right>Χώρα    : </td><td align=left><input type=text name=country_code size=3 maxlength=3 value=\"$country_code\"></td></tr>\n";
		echo "<tr><td align=right>Εναλ Τηλέφωνο : </td><td align=left><input type=text name=alt_phone size=10 maxlength=10 value=\"$alt_phone\"></td></tr>\n";
		echo "<tr><td align=rightΗλεκτρονικό ταχυδρομείο: </td><td align=left><input type=text name=email size=30 maxlength=50 value=\"$email\"></td></tr>\n";
		echo "<tr><td align=right>Ασφάλεια : </td><td align=left><input type=text name=security size=30 maxlength=100 value=\"$security\"></td></tr>\n";
		echo "<tr><td align=right>Σχόλια : </td><td align=left><input type=text name=comments size=30 maxlength=255 value=\"$comments\"></td></tr>\n";
			echo "<tr bgcolor=#B6D3FC><td align=right>Τερματισμός: </td><td align=left><select size=1 name=status>\n";

				$stmt="SELECT status,status_name from vicidial_statuses where selectable='Y' order by status";
				$rslt=mysql_query($stmt, $link);
				$statuses_to_print = mysql_num_rows($rslt);
				$statuses_list='';

				$o=0;
				while ($statuses_to_print > $o) {
					$rowx=mysql_fetch_row($rslt);
					$statuses_list .= "<option value=\"$rowx[0]\">$rowx[0] - $rowx[1]</option>\n";
					$o++;
				}
			echo "$statuses_list";
			echo "</select></td></tr>\n";


		echo "<tr><td colspan=2><input type=submit name=submit value=\"Τερματισμό Κλήσης\"></td></tr>\n";
		echo "</table></form>\n";
		echo "<BR><BR><BR>\n";

	}
	else
	{
		echo "ΑΠΟΤΥΧΙΑ ψαξίματος καθοδήγησης για lead_id $lead_id &nbsp; &nbsp; &nbsp; $NOW_TIME\n<BR><BR>\n";
#		echo "<a href=\"$PHP_SELF\">Close this window</a>\n<BR><BR>\n";
	}






}


$ENDtime = date("U");

$RUNtime = ($ENDtime - $STARTtime);

echo "\n\n\n<br><br><br>\n\n";


echo "<font size=0>\n\n\n<br><br><br>\nχρόνος εκτέλεσης διαδικασίας: $RUNtime seconds</font>";


?>


</body>
</html>

<?php
	
exit; 



?>





