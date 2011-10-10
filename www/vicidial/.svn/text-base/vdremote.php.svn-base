<?php
# vdremote.php
# 
# make sure you have added a user to the vicidial_users MySQL table with at 
# least user_level 4 to access this page the first time
#
# Copyright (C) 2009  Matt Florell <vicidial@gmail.com>    LICENSE: AGPLv2
#
# Changes
# 50307-1721 - First version
# 51123-1502 - removed requirement of PHP Globals=on
# 60421-1229 - check GET/POST vars lines with isset to not trigger PHP NOTICES
# 60619-1603 - Added variable filtering to eliminate SQL injection attack threat
# 90508-0644 - Changed to PHP long tags
# 91129-2249 - Replaced SELECT STAR in SQL queries, formatting fixes
#

$version = '2.2.0';
$build = '91129-2249';

require("dbconnect.php");

$PHP_AUTH_USER=$_SERVER['PHP_AUTH_USER'];
$PHP_AUTH_PW=$_SERVER['PHP_AUTH_PW'];
$PHP_SELF=$_SERVER['PHP_SELF'];
if (isset($_GET["ADD"]))				{$ADD=$_GET["ADD"];}
	elseif (isset($_POST["ADD"]))		{$ADD=$_POST["ADD"];}
if (isset($_GET["DB"]))				{$DB=$_GET["DB"];}
	elseif (isset($_POST["DB"]))		{$DB=$_POST["DB"];}
if (isset($_GET["user"]))				{$user=$_GET["user"];}
	elseif (isset($_POST["user"]))		{$user=$_POST["user"];}
if (isset($_GET["force_logout"]))				{$force_logout=$_GET["force_logout"];}
	elseif (isset($_POST["force_logout"]))		{$force_logout=$_POST["force_logout"];}
if (isset($_GET["groups"]))				{$groups=$_GET["groups"];}
	elseif (isset($_POST["groups"]))		{$groups=$_POST["groups"];}
if (isset($_GET["remote_agent_id"]))				{$remote_agent_id=$_GET["remote_agent_id"];}
	elseif (isset($_POST["remote_agent_id"]))		{$remote_agent_id=$_POST["remote_agent_id"];}
if (isset($_GET["user_start"]))				{$user_start=$_GET["user_start"];}
	elseif (isset($_POST["user_start"]))		{$user_start=$_POST["user_start"];}
if (isset($_GET["number_of_lines"]))				{$number_of_lines=$_GET["number_of_lines"];}
	elseif (isset($_POST["number_of_lines"]))		{$number_of_lines=$_POST["number_of_lines"];}
if (isset($_GET["server_ip"]))				{$server_ip=$_GET["server_ip"];}
	elseif (isset($_POST["server_ip"]))		{$server_ip=$_POST["server_ip"];}
if (isset($_GET["conf_exten"]))				{$conf_exten=$_GET["conf_exten"];}
	elseif (isset($_POST["conf_exten"]))		{$conf_exten=$_POST["conf_exten"];}
if (isset($_GET["status"]))				{$status=$_GET["status"];}
	elseif (isset($_POST["status"]))		{$status=$_POST["status"];}
if (isset($_GET["campaign_id"]))				{$campaign_id=$_GET["campaign_id"];}
	elseif (isset($_POST["campaign_id"]))		{$campaign_id=$_POST["campaign_id"];}
if (isset($_GET["groups"]))				{$groups=$_GET["groups"];}
	elseif (isset($_POST["groups"]))		{$groups=$_POST["groups"];}
if (isset($_GET["submit"]))				{$submit=$_GET["submit"];}
	elseif (isset($_POST["submit"]))		{$submit=$_POST["submit"];}
if (isset($_GET["SUBMIT"]))				{$SUBMIT=$_GET["SUBMIT"];}
	elseif (isset($_POST["SUBMIT"]))		{$SUBMIT=$_POST["SUBMIT"];}

if (!isset($force_logout)) {$force_logout = 0;}

if ($force_logout)
	{
	if( (strlen($PHP_AUTH_USER)>0) or (strlen($PHP_AUTH_PW)>0) )
		{
		Header("WWW-Authenticate: Basic realm=\"VICI-PROJECTS\"");
		Header("HTTP/1.0 401 Unauthorized");
		}
    echo "You have now logged out. Thank you\n";
    exit;
	}

$PHP_AUTH_USER = ereg_replace("[^0-9a-zA-Z]","",$PHP_AUTH_USER);
$PHP_AUTH_PW = ereg_replace("[^0-9a-zA-Z]","",$PHP_AUTH_PW);


$popup_page = './closer_popup.php';
$STARTtime = date("U");
$NOW_DATE = date("Y-m-d");
$NOW_TIME = date("Y-m-d H:i:s");
if (!isset($query_date)) {$query_date = $NOW_DATE;}

$stmt="SELECT count(*) from vicidial_users where user='$PHP_AUTH_USER' and pass='$PHP_AUTH_PW' and user_level > 3;";
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
    echo "Invalid Username/Password: |$PHP_AUTH_USER|$PHP_AUTH_PW|\n";
    exit;
	}
else
	{
	header ("Content-type: text/html; charset=utf-8");

	if($auth>0)
		{
		$office_no=strtoupper($PHP_AUTH_USER);
		$password=strtoupper($PHP_AUTH_PW);
		$stmt="SELECT full_name from vicidial_users where user='$PHP_AUTH_USER' and pass='$PHP_AUTH_PW'";
		$rslt=mysql_query($stmt, $link);
		$row=mysql_fetch_row($rslt);
		$LOGfullname=$row[0];

		$stmt="SELECT count(*) from vicidial_remote_agents where user_start='$PHP_AUTH_USER';";
		if ($DB) {echo "|$stmt|\n";}
		$rslt=mysql_query($stmt, $link);
		$row=mysql_fetch_row($rslt);
		$authx=$row[0];

		if($authx>0)
			{
			$stmt="SELECT remote_agent_id,server_ip,number_of_lines from vicidial_remote_agents where user_start='$PHP_AUTH_USER';";
			if ($DB) {echo "|$stmt|\n";}
			$rslt=mysql_query($stmt, $link);
			$row=mysql_fetch_row($rslt);
			$remote_agent_id=$row[0];
			$server_ip=$row[1];
			if (!$number_of_lines) {$number_of_lines=$row[2];}

			fwrite ($fp, "VDremote|GOOD|$date|$PHP_AUTH_USER|$PHP_AUTH_PW|$ip|$browser|$LOGfullname|\n");
			fclose($fp);
			}
		else
			{
			fwrite ($fp, "VDremote|FAIL|$date|$PHP_AUTH_USER|$PHP_AUTH_PW|$ip|$browser|$LOGfullname|\n");
			fclose($fp);
			echo "This remote agent does not exist: |$PHP_AUTH_USER|\n";
			exit;
			}
		}
	else
		{
		fwrite ($fp, "VDremote|FAIL|$date|$PHP_AUTH_USER|$PHP_AUTH_PW|$ip|$browser|\n");
		fclose($fp);
		}
	}

echo "<html>\n";
echo "<head>\n";
echo "<META HTTP-EQUIV=\"Content-Type\" CONTENT=\"text/html; charset=utf-8\">\n";
if ($ADD==61111)
	{
	echo"<META HTTP-EQUIV=Refresh CONTENT=\"5; URL=$PHP_SELF?ADD=61111&user=$user&DB=$DB\">\n";
	}
echo "<!-- VERSION: $version     BUILD: $build -->\n";
?>
<STYLE type="text/css">
<!--
   .green {color: white; background-color: green}
   .red {color: white; background-color: red}
   .blue {color: white; background-color: blue}
   .purple {color: white; background-color: purple}
   .yellow {color: black; background-color: yellow}
   .orange {color: black; background-color: orange}
-->
 </STYLE>
<?php
echo "<title>REMOTE AGENTS: $LOGfullname - $PHP_AUTH_USER   ";

if (!$ADD)			{$ADD="31111";}
if ($ADD==31111)	{echo "Modify Remote Agents";}
if ($ADD==41111)	{echo "Modify Remote Agents";}
if ($ADD==61111)	{echo "Remote Agent Status";}
if ($ADD==71111)	{echo "Remote Agent Closer Stats";}


if (strlen($ADD)>4)
	{
	##### get server listing for dynamic pulldown
	$stmt="SELECT server_ip,server_description from servers order by server_ip";
	$rslt=mysql_query($stmt, $link);
	$servers_to_print = mysql_num_rows($rslt);
	$servers_list='';

	$o=0;
	while ($servers_to_print > $o)
		{
		$rowx=mysql_fetch_row($rslt);
		$servers_list .= "<option value=\"$rowx[0]\">$rowx[0] - $rowx[1]</option>\n";
		$o++;
		}

	##### get campaigns listing for dynamic pulldown
	$stmt="SELECT campaign_id,campaign_name from vicidial_campaigns order by campaign_id";
	$rslt=mysql_query($stmt, $link);
	$campaigns_to_print = mysql_num_rows($rslt);
	$campaigns_list='';

	$o=0;
	while ($campaigns_to_print > $o)
		{
		$rowx=mysql_fetch_row($rslt);
		$campaigns_list .= "<option value=\"$rowx[0]\">$rowx[0] - $rowx[1]</option>\n";
		$o++;
		}

	##### get inbound groups listing for checkboxes
	if ( (($ADD==31111) or ($ADD==31111)) and (count($groups)<1) )
		{
		$stmt="SELECT closer_campaigns from vicidial_remote_agents where remote_agent_id='" . mysql_real_escape_string($remote_agent_id) . "';";
		$rslt=mysql_query($stmt, $link);
		$row=mysql_fetch_row($rslt);
		$closer_campaigns =	$row[0];
		$closer_campaigns = preg_replace("/ -$/","",$closer_campaigns);
		$groups = explode(" ", $closer_campaigns);
		}

	$stmt="SELECT group_id,group_name from vicidial_inbound_groups order by group_id";
	$rslt=mysql_query($stmt, $link);
	$groups_to_print = mysql_num_rows($rslt);
	$groups_list='';
	$groups_value='';

	$o=0;
	while ($groups_to_print > $o)
		{
		$rowx=mysql_fetch_row($rslt);
		$group_id_value = $rowx[0];
		$group_name_value = $rowx[1];
		$groups_list .= "<input type=\"checkbox\" name=\"groups[]\" value=\"$group_id_value\"";
		$p=0;
		while ($p<50)
			{
			if ($group_id_value == $groups[$p]) 
				{
				$groups_list .= " CHECKED";
				$groups_value .= " $group_id_value";
				}
			$p++;
			}
		$groups_list .= "> $group_id_value - $group_name_value<BR>\n";
		$o++;
		}
	if (strlen($groups_value)>2) {$groups_value .= " -";}
	}

?>
</title>
</head>
<BODY BGCOLOR=white marginheight=0 marginwidth=0 leftmargin=0 topmargin=0>
<CENTER>
<TABLE WIDTH=620 BGCOLOR=#D9E6FE cellpadding=2 cellspacing=0><TR BGCOLOR=#015B91><TD ALIGN=LEFT><FONT FACE="ARIAL,HELVETICA" COLOR=WHITE SIZE=2><B> &nbsp; REMOTE AGENTS: <?php echo "$PHP_AUTH_USER " ?> &nbsp; <a href="<?php echo $PHP_SELF ?>?force_logout=1"><FONT FACE="ARIAL,HELVETICA" COLOR=WHITE SIZE=1>Logout</a></TD><TD ALIGN=RIGHT><FONT FACE="ARIAL,HELVETICA" COLOR=WHITE SIZE=2><B><?php echo date("l F j, Y G:i:s A") ?> &nbsp; </TD></TR>
<TR BGCOLOR=#F0F5FE><TD ALIGN=LEFT COLSPAN=2><FONT FACE="ARIAL,HELVETICA" COLOR=BLACK SIZE=1> &nbsp; <a href="./vdremote.php"><FONT FACE="ARIAL,HELVETICA" COLOR=BLACK SIZE=1>MODIFY</a> | <a href="./vdremote.php?ADD=61111&user=<?php echo "$PHP_AUTH_USER" ?>"><FONT FACE="ARIAL,HELVETICA" COLOR=BLACK SIZE=1>STATUS</a> | <a href="./vdremote.php?ADD=71111&user=<?php echo "$PHP_AUTH_USER" ?>"><FONT FACE="ARIAL,HELVETICA" COLOR=BLACK SIZE=1>INBOUND STATS</a></TD></TR>


<TR><TD ALIGN=LEFT COLSPAN=2>
<?php 



######################
# ADD=31111 modify remote agents info in the system
######################

if ($ADD==31111)
	{
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";

	$stmt="SELECT remote_agent_id,user_start,number_of_lines,server_ip,conf_exten,status,campaign_id,closer_campaigns from vicidial_remote_agents where remote_agent_id='" . mysql_real_escape_string($remote_agent_id) . "';";
	$rslt=mysql_query($stmt, $link);
	$row=mysql_fetch_row($rslt);
	$remote_agent_id =	$row[0];
	$user_start =		$row[1];
	$number_of_lines =	$row[2];
	$server_ip =		$row[3];
	$conf_exten =		$row[4];
	$status =			$row[5];
	$campaign_id =		$row[6];

	echo "<br>MODIFY A REMOTE AGENTS ENTRY: $row[0]<form action=$PHP_SELF method=POST>\n";
	echo "<input type=hidden name=ADD value=41111>\n";
	echo "<input type=hidden name=remote_agent_id value=\"$row[0]\">\n";
	echo "<center><TABLE width=600 cellspacing=3>\n";
	echo "<tr bgcolor=#B6D3FC><td align=right>User ID Start: </td><td align=left>$user_start</td></tr>\n";
	echo "<tr bgcolor=#B6D3FC><td align=right>Number of Lines: </td><td align=left><input type=text name=number_of_lines size=3 maxlength=3 value=\"$number_of_lines\"> (numbers only)</td></tr>\n";
	echo "<tr bgcolor=#B6D3FC><td align=right>Server IP: </td><td align=left>$row[3]</td></tr>\n";
	echo "<tr bgcolor=#B6D3FC><td align=right>External Extension: </td><td align=left><input type=text name=conf_exten size=20 maxlength=20 value=\"$conf_exten\"> (number dialed to reach agents [i.e. 913125551212])</td></tr>\n";
	echo "<tr bgcolor=#B6D3FC><td align=right>Status: </td><td align=left><select size=1 name=status><option SELECTED>ACTIVE</option><option>INACTIVE</option><option SELECTED>$status</option></select></td></tr>\n";
	echo "<tr bgcolor=#B6D3FC><td align=right>Campaign: </td><td align=left>$campaign_id</td></tr>\n";
	echo "<tr bgcolor=#B6D3FC><td align=right>Inbound Groups: </td><td align=left>\n";
	echo "$groups_list";
	echo "</td></tr>\n";
	echo "<tr bgcolor=#B6D3FC><td align=center colspan=2><input type=submit name=submit value=submit></td></tr>\n";
	echo "</TABLE></center>\n";
	echo "NOTE: It can take up to 30 seconds for changes submitted on this screen to go live\n";
	}



######################
# ADD=41111 modify remote agents info in the system
######################

if ($ADD==41111)
	{
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";

	if ( (strlen($number_of_lines) < 1) or (strlen($conf_exten) < 2) )
		{echo "<br>REMOTE AGENTS NOT MODIFIED - Please go back and look at the data you entered\n";}
	else
		{
		$stmt="UPDATE vicidial_remote_agents set number_of_lines='" . mysql_real_escape_string($number_of_lines) . "', conf_exten='" . mysql_real_escape_string($conf_exten) . "', status='" . mysql_real_escape_string($status) . "', closer_campaigns='" . mysql_real_escape_string($groups_value) . "' where remote_agent_id='" . mysql_real_escape_string($remote_agent_id) . "';";
		$rslt=mysql_query($stmt, $link);

#		echo "$stmt\n";
		echo "<br>REMOTE AGENTS MODIFIED\n";

		### LOG CHANGES TO LOG FILE ###
		$fp = fopen ("./admin_changes_log.txt", "a");
		fwrite ($fp, "$date|MODIFY REMOTE AGENTS ENTRY     |$PHP_AUTH_USER|$ip|$stmt|\n");
		fclose($fp);
		}

	$stmt="SELECT remote_agent_id,user_start,number_of_lines,server_ip,conf_exten,status,campaign_id,closer_campaigns from vicidial_remote_agents where remote_agent_id='" . mysql_real_escape_string($remote_agent_id) . "';";
	$rslt=mysql_query($stmt, $link);
	$row=mysql_fetch_row($rslt);
	$remote_agent_id =	$row[0];
	$user_start =		$row[1];
	$number_of_lines =	$row[2];
	$server_ip =		$row[3];
	$conf_exten =		$row[4];
	$status =			$row[5];
	$campaign_id =		$row[6];

	echo "<br>MODIFY A REMOTE AGENTS ENTRY: $row[0]<form action=$PHP_SELF method=POST>\n";
	echo "<input type=hidden name=ADD value=41111>\n";
	echo "<input type=hidden name=remote_agent_id value=\"$row[0]\">\n";
	echo "<center><TABLE width=600 cellspacing=3>\n";
	echo "<tr bgcolor=#B6D3FC><td align=right>User ID Start: </td><td align=left>$user_start</td></tr>\n";
	echo "<tr bgcolor=#B6D3FC><td align=right>Number of Lines: </td><td align=left><input type=text name=number_of_lines size=3 maxlength=3 value=\"$number_of_lines\"> (numbers only)</td></tr>\n";
	echo "<tr bgcolor=#B6D3FC><td align=right>Server IP: </td><td align=left>$row[3]</td></tr>\n";
	echo "<tr bgcolor=#B6D3FC><td align=right>External Extension: </td><td align=left><input type=text name=conf_exten size=20 maxlength=20 value=\"$conf_exten\"> (number dialed to reach agents [i.e. 913125551212])</td></tr>\n";
	echo "<tr bgcolor=#B6D3FC><td align=right>Status: </td><td align=left><select size=1 name=status><option SELECTED>ACTIVE</option><option>INACTIVE</option><option SELECTED>$status</option></select></td></tr>\n";
	echo "<tr bgcolor=#B6D3FC><td align=right>Campaign: </td><td align=left>$campaign_id</td></tr>\n";
	echo "<tr bgcolor=#B6D3FC><td align=right>Inbound Groups: </td><td align=left>\n";
	echo "$groups_list";
	echo "</td></tr>\n";
	echo "<tr bgcolor=#B6D3FC><td align=center colspan=2><input type=submit name=submit value=submit></td></tr>\n";
	echo "</TABLE></center>\n";
	echo "NOTE: It can take up to 30 seconds for changes submitted on this screen to go live\n";
	}



######################
# ADD=61111 status of remote agent in the system and active calls and queue
######################

if ($ADD==61111)
	{
	echo "<FONT FACE=\"Courier\" COLOR=BLACK SIZE=2><PRE>";

	if ( (strlen($server_ip) < 2) or (strlen($user) < 2) )
		{echo "<br>REMOTE AGENTS ERROR - Please go back and look at the data you entered\n";}
	else
		{
		$users_list = '';
		$k=0;
		while($k < $number_of_lines)
			{
			$nextuser=($user + $k);
			$users_list .= "'" . mysql_real_escape_string($nextuser) . "',";
			$k++;
			}
		$users_list = preg_replace("/.$/","",$users_list);

		echo "Remote Agent Time On Calls                                        $NOW_TIME\n\n";
		echo "+------------|--------+--------------+------------+--------+---------------------+---------+\n";
		echo "| STATION    | USER   | LEADID       | CHANNEL    | STATUS | START TIME          | MINUTES |\n";
		echo "+------------|--------+--------------+------------+--------+---------------------+---------+\n";


		$stmt="select extension,user,lead_id,channel,status,last_call_time,UNIX_TIMESTAMP(last_call_time),UNIX_TIMESTAMP(last_call_finish) from vicidial_live_agents where status NOT IN('PAUSED') and server_ip='" . mysql_real_escape_string($server_ip) . "' and user IN($users_list) order by extension;";
		$rslt=mysql_query($stmt, $link);
		if ($DB) {echo "$stmt\n";}
		$talking_to_print = mysql_num_rows($rslt);
		if ($talking_to_print > 0)
			{
			$i=0;
			while ($i < $talking_to_print)
				{
				$leadlink=0;
				$row=mysql_fetch_row($rslt);
				if (eregi("READY|PAUSED",$row[4]))
					{
					$row[3]='';
					$row[5]=' - WAITING - ';
					$row[6]=$row[7];
					}
				$extension =		sprintf("%-10s", $row[0]);
				$user =				sprintf("%-6s", $row[1]);
				$leadid =			sprintf("%-12s", $row[2]);
				if ($row[2] > 0) 
					{
					$leadidLINK=$row[2];
					$leadlink++;
					if ( eregi("QUEUE",$row[4]) ) {$row[6]=$STARTtime;}
					$leadid = "<a href=\"./remote_dispo.php?lead_id=$row[2]&call_began=$row[6]\" target=\"_blank\">$leadid</a>";
					}
				$channel =			sprintf("%-10s", $row[3]);
				$cc=0;
				while ( (strlen($channel) > 10) and ($cc < 100) )
					{
					$channel = eregi_replace(".$","",$channel);   
					$cc++;
					if (strlen($channel) <= 10) {$cc=101;}
					}
				$status =			sprintf("%-6s", $row[4]);
				$start_time =		sprintf("%-19s", $row[5]);
				$call_time_S = ($STARTtime - $row[6]);

				$call_time_M = ($call_time_S / 60);
				$call_time_M = round($call_time_M, 2);
				$call_time_M_int = intval("$call_time_M");
				$call_time_SEC = ($call_time_M - $call_time_M_int);
				$call_time_SEC = ($call_time_SEC * 60);
				$call_time_SEC = round($call_time_SEC, 0);
				if ($call_time_SEC < 10) {$call_time_SEC = "0$call_time_SEC";}
				$call_time_MS = "$call_time_M_int:$call_time_SEC";
				$call_time_MS =		sprintf("%7s", $call_time_MS);
				$G = '';		$EG = '';
				if ($call_time_M_int >= 5) {$G='<SPAN class="yellow"><B>'; $EG='</B></SPAN>';}
				if ($call_time_M_int >= 10) {$G='<SPAN class="orange"><B>'; $EG='</B></SPAN>';}

				echo "| $G$extension$EG | $G$user$EG | $G$leadid$EG | $G$channel$EG | $G$status$EG | $G$start_time$EG | $G$call_time_MS$EG |\n";

				$i++;
				}

			echo "+------------|--------+--------------+------------+--------+---------------------+---------+\n";
			echo "  $i agents logged in on server $server_ip\n\n";

			echo "  <SPAN class=\"yellow\"><B>          </SPAN> - 5 minutes or more on call</B>\n";
			echo "  <SPAN class=\"orange\"><B>          </SPAN> - Over 10 minutes on call</B>\n";
			}
		else
			{
			echo "**************************************************************************************\n";
			echo "********************************* NO AGENTS ON CALLS *********************************\n";
			echo "**************************************************************************************\n";
			}
		}

	echo "</PRE>\n\n";
	}


######################
# ADD=71111 stats for remote agents from closer logs(vicidial_closer_log)
######################

if ($ADD==71111)
	{
	echo "<FORM ACTION=\"$PHP_SELF\" METHOD=GET>\n";
	echo "<INPUT TYPE=TEXT NAME=query_date SIZE=10 MAXLENGTH=10 VALUE=\"$query_date\">\n";
	echo "<INPUT TYPE=HIDDEN NAME=ADD VALUE=\"71111\">\n";
	echo "<INPUT TYPE=HIDDEN NAME=user VALUE=\"$user\">\n";
	echo "<INPUT TYPE=SUBMIT NAME=SUBMIT VALUE=SUBMIT>\n";
	echo "</FORM>\n\n";
	echo "<FONT FACE=\"Courier\" COLOR=BLACK SIZE=2><PRE>";

	if ( (strlen($server_ip) < 2) or (strlen($user) < 2) )
		{echo "<br>REMOTE AGENTS ERROR - Please go back and look at the data you entered\n";}
	else
		{
		$users_list = '';
		$k=0;
		while($k < $number_of_lines)
			{
			$nextuser=($user + $k);
			$users_list .= "'$nextuser',";
			$k++;
			}
		$users_list = preg_replace("/.$/","",$users_list);

		$stmt="select count(*),sum(length_in_sec) from vicidial_closer_log where call_date >= '$query_date 00:00:01' and call_date <= '$query_date 23:59:59' and user IN($users_list);";
		$rslt=mysql_query($stmt, $link);
		$row=mysql_fetch_row($rslt);
		$in_calls =		$row[0];
		$in_time =		$row[1];
		$in_time_M = ($in_time / 60);
		$in_time_M = round($in_time_M, 2);
		$in_time_M_int = intval("$in_time_M");
		$in_time_SEC = ($in_time_M - $in_time_M_int);
		$in_time_SEC = ($in_time_SEC * 60);
		$in_time_SEC = round($in_time_SEC, 0);
		if ($in_time_SEC < 10) {$in_time_SEC = "0$in_time_SEC";}
		$in_time_MS = "$in_time_M_int:$in_time_SEC";
		$in_time_MS =		sprintf("%7s", $in_time_MS);

		echo "Remote Agent inbound stats                                        $NOW_TIME\n\n";
		echo "\n";
		echo "Total calls taken $query_date:    $in_calls\n";
		echo "Total talk time on $query_date:   $in_time_MS (minutes:seconds)\n";
		echo "\n";
		echo "\n";
		echo "Call list for $query_date:\n";
		echo "+----------+------------+----------------+------------+--------+---------------------+---------+\n";
		echo "| USER     | LEADID     | GROUP          | PHONE NUM  | STATUS | CALL TIME           | MINUTES |\n";
		echo "+----------+------------+----------------+------------+--------+---------------------+---------+\n";


		$stmt="select user,lead_id,campaign_id,phone_number,status,call_date,length_in_sec from vicidial_closer_log where call_date >= '$query_date 00:00:01' and call_date <= '$query_date 23:59:59' and user IN($users_list) order by call_date;";
		$rslt=mysql_query($stmt, $link);
		if ($DB) {echo "$stmt\n";}
		$talking_to_print = mysql_num_rows($rslt);
		if ($talking_to_print > 0)
			{
			$i=0;
			while ($i < $talking_to_print)
				{
				$row=mysql_fetch_row($rslt);
				$user =				sprintf("%-8s", $row[0]);
				$leadid =			sprintf("%-10s", $row[1]);
				$group =			sprintf("%-14s", $row[2]);
				$phone =			sprintf("%-10s", $row[3]);
				$status =			sprintf("%-6s", $row[4]);
				$start_time =		sprintf("%-19s", $row[5]);
				$call_time_S =		$row[6];

				$call_time_M = ($call_time_S / 60);
				$call_time_M = round($call_time_M, 2);
				$call_time_M_int = intval("$call_time_M");
				$call_time_SEC = ($call_time_M - $call_time_M_int);
				$call_time_SEC = ($call_time_SEC * 60);
				$call_time_SEC = round($call_time_SEC, 0);
				if ($call_time_SEC < 10) {$call_time_SEC = "0$call_time_SEC";}
				$call_time_MS = "$call_time_M_int:$call_time_SEC";
				$call_time_MS =		sprintf("%7s", $call_time_MS);
				$G = '';		$EG = '';
	#			if ($call_time_M_int >= 5) {$G='<SPAN class="yellow"><B>'; $EG='</B></SPAN>';}
	#			if ($call_time_M_int >= 10) {$G='<SPAN class="orange"><B>'; $EG='</B></SPAN>';}

				echo "| $G$user$EG | $G$leadid$EG | $G$group$EG | $G$phone$EG | $G$status$EG | $G$start_time$EG | $G$call_time_MS$EG |\n";

				$i++;
				}

			echo "+----------+------------+----------------+------------+--------+---------------------+---------+\n";
#			echo "  $i agents logged in on server $server_ip\n\n";

#			echo "  <SPAN class=\"yellow\"><B>          </SPAN> - 5 minutes or more on call</B>\n";
#			echo "  <SPAN class=\"orange\"><B>          </SPAN> - Over 10 minutes on call</B>\n";
			}
		else
			{
			echo "**************************************************************************************\n";
			echo "********************************* NO CALLS ON THIS DAY *******************************\n";
			echo "**************************************************************************************\n";
			}
		}

	echo "</PRE>\n\n";
	}





$ENDtime = date("U");

$RUNtime = ($ENDtime - $STARTtime);

echo "\n\n\n<br><br><br>\n\n";


echo "<font size=0>\n\n\n<br><br><br>\nscript runtime: $RUNtime seconds</font>";


?>


</TD></TR><TABLE>
</body>
</html>

<?php
	
exit; 



?>

