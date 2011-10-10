<?php
# inbound_popup.php    version 2.2.0
# 
# Copyright (C) 2009  Matt Florell <vicidial@gmail.com>    LICENSE: AGPLv2
#
# This script is designed to open up when a live_inbound call comes in giving the user
#   options of what to do with the call or options to lookup the callerID on various web sites
# This script depends on the server_ip being sent and also needs to have a valid user/pass from the vicidial_users table
# 
# required variables:
#  - $server_ip
#  - $session_name
#  - $uniqueid - ('1234567890.123456',...)
#  - $user
#  - $pass
# optional variables:
#  - $format - ('text','debug')
#  - $vmail_box - ('101','1234',...)
#  - $exten - ('cc101','testphone','49-1','1234','913125551212',...)
#  - $ext_context - ('default','demo',...)
#  - $ext_priority - ('1','2',...)
#  - $voicemail_dump_exten - ('85026666666666')
#  - $local_web_callerID_URL_enc - ( rawurlencoded custom callerid lookup URL)
# 
#
# changes
# 50428-1500 - First build of script display only
# 50429-1241 - some formatting, hangup and Vmail redirect, 30s timeout on actions, and CID web lookup links
# 50503-1244 - added session_name checking for extra security
# 50711-1203 - removed HTTP authentication in favor of user/pass vars
# 60421-1043 - check GET/POST vars lines with isset to not trigger PHP NOTICES
# 60619-1205 - Added variable filters to close security holes for login form
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
if (isset($_GET["uniqueid"]))				{$uniqueid=$_GET["uniqueid"];}
	elseif (isset($_POST["uniqueid"]))		{$uniqueid=$_POST["uniqueid"];}
if (isset($_GET["format"]))					{$format=$_GET["format"];}
	elseif (isset($_POST["format"]))		{$format=$_POST["format"];}
if (isset($_GET["exten"]))					{$exten=$_GET["exten"];}
	elseif (isset($_POST["exten"]))			{$exten=$_POST["exten"];}
if (isset($_GET["vmail_box"]))				{$vmail_box=$_GET["vmail_box"];}
	elseif (isset($_POST["vmail_box"]))		{$vmail_box=$_POST["vmail_box"];}
if (isset($_GET["ext_context"]))			{$ext_context=$_GET["ext_context"];}
	elseif (isset($_POST["ext_context"]))	{$ext_context=$_POST["ext_context"];}
if (isset($_GET["ext_priority"]))			{$ext_priority=$_GET["ext_priority"];}
	elseif (isset($_POST["ext_priority"]))	{$ext_priority=$_POST["ext_priority"];}
if (isset($_GET["voicemail_dump_exten"]))			{$voicemail_dump_exten=$_GET["voicemail_dump_exten"];}
	elseif (isset($_POST["voicemail_dump_exten"]))	{$voicemail_dump_exten=$_POST["voicemail_dump_exten"];}
if (isset($_GET["local_web_callerID_URL_enc"]))			{$local_web_callerID_URL_enc=$_GET["local_web_callerID_URL_enc"];}
	elseif (isset($_POST["local_web_callerID_URL_enc"]))	{$local_web_callerID_URL_enc=$_POST["local_web_callerID_URL_enc"];}
if (isset($_GET["local_web_callerID_URL_enc"]))			{$local_web_callerID_URL = rawurldecode($local_web_callerID_URL_enc);}
	else {$local_web_callerID_URL = '';}

$user=ereg_replace("[^0-9a-zA-Z]","",$user);
$pass=ereg_replace("[^0-9a-zA-Z]","",$pass);

# default optional vars if not set
if (!isset($format))   {$format="text";}

$version = '0.0.6';
$build = '60619-1205';
$StarTtime = date("U");
$NOW_DATE = date("Y-m-d");
$NOW_TIME = date("Y-m-d H:i:s");
if (!isset($query_date)) {$query_date = $NOW_DATE;}
$DO = '-1';
if ( (eregi("^Zap",$channel)) and (!eregi("-",$channel)) ) {$channel = "$channel$DO";}

	$stmt="SELECT count(*) from vicidial_users where user='$user' and pass='$pass' and user_level > 0;";
	if ($DB) {echo "|$stmt|\n";}
	$rslt=mysql_query($stmt, $link);
	$row=mysql_fetch_row($rslt);
	$auth=$row[0];

  if( (strlen($user)<2) or (strlen($pass)<2) or ($auth==0))
	{
    echo "Invalid Username/Password: |$user|$pass|\n";
    exit;
	}
  else
	{

	if( (strlen($server_ip)<6) or (!isset($server_ip)) or ( (strlen($session_name)<12) or (!isset($session_name)) ) )
		{
		echo "Invalid server_ip: |$server_ip|  or  Invalid session_name: |$session_name|\n";
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
			echo "Invalid session_name: |$session_name|$server_ip|\n";
			exit;
			}
		  else
			{
			# do nothing for now
			}
		}
	}

if ($format=='debug')
{
$forever_stop=0;
$user_abb = "$user$user$user$user";
while ( (strlen($user_abb) > 4) and ($forever_stop < 200) )
	{$user_abb = eregi_replace("^.","",$user_abb);   $forever_stop++;}

echo "<html>\n";
echo "<head>\n";
echo "<!-- VERSION: $version     BUILD: $build    UNIQUEID: $uniqueid   server_ip: $server_ip-->\n";
?>
	<script language="Javascript">	
		var server_ip = '<?php echo $server_ip ?>';
		var epoch_sec = '<?php echo $StarTtime ?>';
		var user_abb = '<?php echo $user_abb ?>';
		var vmail_box = '<?php echo $vmail_box ?>';
		var ext_context = '<?php echo $ext_context ?>';
		var ext_priority = '<?php echo $ext_priority ?>';
		var voicemail_dump_exten = '<?php echo $voicemail_dump_exten ?>';
		var session_name = '<?php echo $session_name ?>';
		var user = '<?php echo $user ?>';
		var pass = '<?php echo $pass ?>';


// ################################################################################
// Send Hangup command for Live call connected to phone now to Manager
	function livehangup_send_hangup(taskvar) 
		{
		var xmlhttp=false;
		/*@cc_on @*/
		/*@if (@_jscript_version >= 5)
		// JScript gives us Conditional compilation, we can cope with old IE versions.
		// and security blocked creation of the objects.
		 try {
		  xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
		 } catch (e) {
		  try {
		   xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
		  } catch (E) {
		   xmlhttp = false;
		  }
		 }
		@end @*/
		if (!xmlhttp && typeof XMLHttpRequest!='undefined')
			{
			xmlhttp = new XMLHttpRequest();
			}
		if (xmlhttp) 
			{ 
			var queryCID = "HLagcP" + epoch_sec + user_abb;
			var hangupvalue = taskvar;
			livehangup_query = "server_ip=" + server_ip + "&session_name=" + session_name + "&user=" + user + "&pass=" + pass + "&ACTION=Hangup&format=text&channel=" + hangupvalue + "&queryCID=" + queryCID;
			xmlhttp.open('POST', 'manager_send.php'); 
			xmlhttp.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
			xmlhttp.send(livehangup_query); 
			xmlhttp.onreadystatechange = function() 
				{ 
				if (xmlhttp.readyState == 4 && xmlhttp.status == 200) 
					{
					Nactiveext = null;
					Nactiveext = xmlhttp.responseText;
					alert(xmlhttp.responseText);
					}
				}
			delete xmlhttp;
			}
		call_action_link_clear();
		}


// ################################################################################
// Send Redirect command for ringing call to go directly to your voicemail
	function liveredirect_send_vmail(taskvar,taskbox) 
		{
		var xmlhttp=false;
		/*@cc_on @*/
		/*@if (@_jscript_version >= 5)
		// JScript gives us Conditional compilation, we can cope with old IE versions.
		// and security blocked creation of the objects.
		 try {
		  xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
		 } catch (e) {
		  try {
		   xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
		  } catch (E) {
		   xmlhttp = false;
		  }
		 }
		@end @*/
		if (!xmlhttp && typeof XMLHttpRequest!='undefined')
			{
			xmlhttp = new XMLHttpRequest();
			}
		if (xmlhttp) 
			{ 
			var queryCID = "RVagcP" + epoch_sec + user_abb;
			var hangupvalue = taskvar;
			var mailboxvalue = taskbox;
			liveredirect_query = "server_ip=" + server_ip + "&session_name=" + session_name + "&user=" + user + "&pass=" + pass + "&ACTION=Redirect&format=text&channel=" + hangupvalue + "&queryCID=" + queryCID + "&exten=" + voicemail_dump_exten + "" + mailboxvalue + "&ext_context=" + ext_context + "&ext_priority=" + ext_priority;
			xmlhttp.open('POST', 'manager_send.php'); 
			xmlhttp.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
			xmlhttp.send(liveredirect_query); 
			xmlhttp.onreadystatechange = function() 
				{ 
				if (xmlhttp.readyState == 4 && xmlhttp.status == 200) 
					{
					Nactiveext = null;
					Nactiveext = xmlhttp.responseText;
					alert(xmlhttp.responseText);
					}
				}
			delete xmlhttp;
			}
		call_action_link_clear();
		}


// ################################################################################
// timeout to deactivate the call action links after 30 seconds
	function link_timeout() 
		{
		window.focus();
		setTimeout("call_action_link_clear()", 30000);
		}

// ################################################################################
// deactivates the call action links
	function call_action_link_clear() 
		{
		document.getElementById("callactions").innerHTML = "";		
		}

	</script>

<?php
echo "<title>LIVE INBOUND CALL";
echo "</title>\n";
echo "</head>\n";
echo "<BODY BGCOLOR=\"#CCC2E0\" marginheight=0 marginwidth=0 leftmargin=0 topmargin=0 onload=\"link_timeout();\">\n";
echo "<CENTER><H2>LIVE INBOUND CALL</H2>\n";
echo "<B>$NOW_TIME</B><BR><BR>\n";
}


$MT[0]='';
$row='';   $rowx='';
$channel_live=1;
if (strlen($uniqueid)<9)
	{
	$channel_live=0;
	echo "Uniqueid $uniqueid is not valid\n";
	exit;
	}
else
	{
	$stmt="SELECT uniqueid,channel,server_ip,caller_id,extension,phone_ext,start_time,acknowledged,inbound_number,comment_a,comment_b,comment_c,comment_d,comment_e FROM live_inbound where server_ip = '$server_ip' and uniqueid = '$uniqueid';";
		if ($format=='debug') {echo "\n<!-- $stmt -->";}
	$rslt=mysql_query($stmt, $link);
	$channels_list = mysql_num_rows($rslt);
	if ($channels_list>0)
		{
		$row=mysql_fetch_row($rslt);
#		echo "$LIuniqueid|$LIchannel|$LIcallerid|$LIdatetime|$row[8]|$row[9]|$row[10]|$row[11]|$row[12]|$row[13]|";
#		Zap/73|"V.I.C.I. MARKET" <7275338730>|2005-04-28 14:01:21|7274514936|Inbound direct to Matt|||||
		if ($format=='debug') {echo "\n<!-- $row[0]|$row[1]|$row[2]|$row[3]|$row[4]|$row[5]|$row[6]|$row[7]|$row[8]|$row[9]|$row[10]|$row[11]|$row[12]|$row[13]| -->";}
		echo "<table width=95% cellpadding=1 cellspacing=3>\n";
		echo "<tr bgcolor=\"#DDDDFF\"><td>Channel: </td><td align=left>$row[1]</td></tr>\n";
		echo "<tr bgcolor=\"#DDDDFF\"><td>CallerID: </td><td align=left>$row[3]</td></tr>\n";
		echo "<tr bgcolor=\"#DDDDFF\"><td colspan=2 align=center>\n";

		$phone = eregi_replace(".*\<","",$row[3]);
		$phone = eregi_replace("\>.*","",$phone);
		$NPA = substr($phone, 0, 3);
		$NXX = substr($phone, 3, 3);
		$XXXX = substr($phone, 6, 4);
		$D='-';
		echo "<a href=\"http://www.google.com/search?hl=en&lr=&client=firefox-a&rls=org.mozilla%3Aen-US%3Aofficial_s&q=$NPA+$NXX+$XXXX&btnG=Search\" target=\"_blank\">GOOGLE</a> - \n";
		echo "<a href=\"http://www.anywho.com/qry/wp_rl?npa=$NPA&telephone=$NXX$XXXX\" target=\"_blank\">ANYWHO</a> - \n";
		echo "<a href=\"http://www.switchboard.com/bin/cgirlookup.dll?SR=&MEM=1&LNK=32%3A36&type=BOTH&at=$NPA&e=$NXX&n=$XXXX&search.x=55&search.y=20\" target=\"_blank\">SWITCHBOARD</a> - \n";
		echo "<a href=\"http://yellowpages.superpages.com/listings.jsp?SRC=&STYPE=&PG=L&CB=&C=&N=&E=&T=&S=&Z=&A=727&X=533&P=8730&AXP=$NPA$NXX$XXXX&R=N&PS=15&search=Find+It\" target=\"_blank\">VERIZON</a> - \n";
		echo "<a href=\"http://www.whitepages.com/1014/log_click/search/Reverse_Phone?npa=$NPA&phone=$NXX$XXXX\" target=\"_blank\">WHITEPAGES</a> - \n";
		echo "<a href=\"http://www.411.com/10742/search/Reverse_Phone?phone=%28$NPA%29+$NXX$D$XXXX\" target=\"_blank\">411.COM</a> - \n";
		echo "<a href=\"http://www.phonenumber.com/10006/search/Reverse_Phone?npa=$NPA&phone=$NXX$XXXX\" target=\"_blank\">411.COM</a> - \n";

			$local_web_callerID_QUERY_STRING ='';
			$local_web_callerID_QUERY_STRING.="?callerID_areacode=$NPA";
			$local_web_callerID_QUERY_STRING.="&callerID_prefix=$NXX";
			$local_web_callerID_QUERY_STRING.="&callerID_last4=$XXXX";
			$local_web_callerID_QUERY_STRING.="&callerID_Time=$row[6]";
			$local_web_callerID_QUERY_STRING.="&callerID_Channel=$row[1]";
			$local_web_callerID_QUERY_STRING.="&callerID_uniqueID=$row[0]";
			$local_web_callerID_QUERY_STRING.="&callerID_phone_ext=$row[5]";
			$local_web_callerID_QUERY_STRING.="&callerID_server_ip=$row[2]";
			$local_web_callerID_QUERY_STRING.="&callerID_extension=$row[4]";
			$local_web_callerID_QUERY_STRING.="&callerID_inbound_number=$row[8]";
			$local_web_callerID_QUERY_STRING.="&callerID_comment_a=$row[9]";
			$local_web_callerID_QUERY_STRING.="&callerID_comment_b=$row[10]";
			$local_web_callerID_QUERY_STRING.="&callerID_comment_c=$row[11]";
			$local_web_callerID_QUERY_STRING.="&callerID_comment_d=$row[12]";
			$local_web_callerID_QUERY_STRING.="&callerID_comment_e=$row[13]";
		echo "<a href=\"$local_web_callerID_URL$local_web_callerID_QUERY_STRING\" target=\"_blank\">CUSTOM</a> - \n";

		echo "</td></tr>\n";
		echo "<tr bgcolor=\"#DDDDFF\"><td>Number Dialed: </td><td align=left>$row[8]</td></tr>\n";
		echo "<tr bgcolor=\"#DDDDFF\"><td>Notes: </td><td align=left>$row[9]|$row[10]|$row[11]|$row[12]|$row[13]|</td></tr>\n";
		echo "<tr bgcolor=\"#DDDDFF\"><td colspan=2 align=center>\n<span id=\"callactions\">";
		echo "<a href=\"#\" onclick=\"livehangup_send_hangup('$row[1]');return false;\">HANGUP</a> - \n";
		echo "<a href=\"#\" onclick=\"liveredirect_send_vmail('$row[1]','$vmail_box');return false;\">SEND TO MY VOICEMAIL</a>\n";
		echo "</span></td></tr>\n";
		echo "</table>\n";


		$stmt="UPDATE live_inbound set acknowledged='Y' where server_ip = '$server_ip' and uniqueid = '$uniqueid';";
			if ($format=='debug') {echo "\n<!-- $stmt -->";}
		$rslt=mysql_query($stmt, $link);


		}

	
	}


if ($format=='debug') 
	{
	$ENDtime = date("U");
	$RUNtime = ($ENDtime - $StarTtime);
	echo "\n<!-- script runtime: $RUNtime seconds -->";
	echo "\n</body>\n</html>\n";
	}
	
exit; 

?>
