<?php
# admin_campaign_multi_alt.php
# 
# Copyright (C) 2011  Matt Florell <vicidial@gmail.com>    LICENSE: AGPLv2
#
# this screen will control the campaign settings needed for alternate number 
# dialing using multiple leads with the same account number and different phone 
# numbers. the leads will use different owner values and this script will alter 
# the ranks for those leads as well as alter the special campaign filter and 
# change some campaign settings.
#
# changes:
# 110317-1219 - First Build
# 110406-1818 - Updated logging
#

$admin_version = '2.4-2';
$build = '110406-1818';


require("dbconnect.php");

$PHP_AUTH_USER=$_SERVER['PHP_AUTH_USER'];
$PHP_AUTH_PW=$_SERVER['PHP_AUTH_PW'];
$PHP_SELF=$_SERVER['PHP_SELF'];
if (isset($_GET["DB"]))							{$DB=$_GET["DB"];}
	elseif (isset($_POST["DB"]))				{$DB=$_POST["DB"];}
if (isset($_GET["action"]))						{$action=$_GET["action"];}
	elseif (isset($_POST["action"]))			{$action=$_POST["action"];}
if (isset($_GET["campaign_id"]))				{$campaign_id=$_GET["campaign_id"];}
	elseif (isset($_POST["campaign_id"]))		{$campaign_id=$_POST["campaign_id"];}
if (isset($_GET["lead_order_randomize"]))			{$lead_order_randomize=$_GET["lead_order_randomize"];}
	elseif (isset($_POST["lead_order_randomize"]))	{$lead_order_randomize=$_POST["lead_order_randomize"];}
if (isset($_GET["lead_order_secondary"]))			{$lead_order_secondary=$_GET["lead_order_secondary"];}
	elseif (isset($_POST["lead_order_secondary"]))	{$lead_order_secondary=$_POST["lead_order_secondary"];}
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
	$campaign_id = ereg_replace("[^-_0-9a-zA-Z]","",$campaign_id);
	$lead_order_randomize = ereg_replace("[^-_0-9a-zA-Z]","",$lead_order_randomize);
	$lead_order_secondary = ereg_replace("[^-_0-9a-zA-Z]","",$lead_order_secondary);
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


$stmt="SELECT count(*) from vicidial_users where user='$PHP_AUTH_USER' and pass='$PHP_AUTH_PW' and user_level > 7 and modify_campaigns='1';";
if ($DB) {echo "|$stmt|\n";}
$rslt=mysql_query($stmt, $link);
$row=mysql_fetch_row($rslt);
$auth=$row[0];

$stmt="SELECT count(*) from vicidial_campaigns where campaign_id='$campaign_id' and auto_alt_dial='MULTI_LEAD';";
if ($DB) {echo "|$stmt|\n";}
$rslt=mysql_query($stmt, $link);
$row=mysql_fetch_row($rslt);
$camp_multi=$row[0];

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
		$stmt="SELECT full_name,modify_campaigns,user_level from vicidial_users where user='$PHP_AUTH_USER' and pass='$PHP_AUTH_PW'";
		$rslt=mysql_query($stmt, $link);
		$row=mysql_fetch_row($rslt);
		$LOGfullname =				$row[0];
		$LOGmodify_campaigns =		$row[1];
		$LOGuser_level =			$row[2];

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
<title>ADMINISTRATION: Campaign Multi-Alt-Leads
<?php 


##### BEGIN Set variables to make header show properly #####
$ADD =					'31';
$SUB =					'26';
$hh =					'campaigns';
$sh =					'detail';
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
$campaigns_color =		'#FFFF99';
$campaigns_font =		'BLACK';
$campaigns_color =		'#E6E6E6';
$subcamp_color =	'#C6C6C6';
##### END Set variables to make header show properly #####

require("admin_header.php");

if ( ($LOGast_admin_access < 1) or ($LOGuser_level < 8) )
	{
	echo "You are not authorized to view this section\n";
	exit;
	}
if ($camp_multi < 1)
	{
	echo "This campaign is not set to Auto-Alt-Dial MULTI_LEAD\n";
	exit;
	}

if ($DB > 0)
	{
	echo "$DB,$action,$campaign_id,$lead_order_randomize,$lead_order_secondary\n<BR>";
	}

$stmt="SELECT list_id,active from vicidial_lists where campaign_id='$campaign_id' order by list_id;";
$rslt=mysql_query($stmt, $link);
$lists_to_print = mysql_num_rows($rslt);
$o=0;
while ($lists_to_print > $o)
	{
	$row=mysql_fetch_row($rslt);
	if (ereg('Y',$row[1]))
		{
		$active_lists++;
		$camp_lists .= "'$row[0]',";
		}
	else
		{
		$inactive_lists++;
		$camp_lists .= "'$row[0]',";
		}
	$o++;
	}
$camp_lists = eregi_replace(".$","",$camp_lists);




################################################################################
##### BEGIN multi-lead alt dial submit form
if ($action == "ALT_MULTI_SUBMIT")
	{
	$settings_updated=0;
	if ( (strlen($lead_order_randomize) > 0) and (strlen($lead_order_secondary) > 0) )
		{
		$stmt="SELECT distinct owner from vicidial_list where list_id IN($camp_lists) limit 1000;";
		$rslt=mysql_query($stmt, $link);
		if ($DB) {echo "$stmt\n";}
		$do_values_ct = mysql_num_rows($rslt);
		$o=0;
		while ($do_values_ct > $o)
			{
			$row=mysql_fetch_row($rslt);
			$owners[$o] =	$row[0];
			$o++;
			}

		$o=0;
		$update_stmts='';
		$new_filter_sql='';
		while ($do_values_ct > $o)
			{
			$owner_check='';
			$owner_rank='';
			$owner_raw =		$owners[$o];
			$owner = preg_replace('/ |\n|\r|\t/','',$owner_raw);
			if (isset($_GET["$campaign_id$US$owner"]))				{$owner_check=$_GET["$campaign_id$US$owner"];}
				elseif (isset($_POST["$campaign_id$US$owner"]))		{$owner_check=$_POST["$campaign_id$US$owner"];}
			if (isset($_GET["rank_$campaign_id$US$owner"]))				{$owner_rank=$_GET["rank_$campaign_id$US$owner"];}
				elseif (isset($_POST["rank_$campaign_id$US$owner"]))	{$owner_rank=$_POST["rank_$campaign_id$US$owner"];}

			if ($owner_check=='YES')
				{$new_filter_sql .= "'$owner_raw',";}

			$stmt = "UPDATE vicidial_list SET rank='$owner_rank' where list_id IN($camp_lists) and owner='$owner_raw';";
			$rslt=mysql_query($stmt, $link);
			$affected_rows = mysql_affected_rows($link);
			if ($DB > 0) {echo "OWNER: $o|$campaign_id|$owner|$owner_check|$owner_rank|$affected_rows\n<BR>";}
			$update_stmts .= "|$affected_rows|$owner_check|$stmt";
			$o++;
			}

		$filter_stmt="INSERT IGNORE INTO vicidial_lead_filters SET lead_filter_id='ML$campaign_id',lead_filter_name='DO NOT DELETE MULTI_$campaign_id',lead_filter_comments=NOW(),lead_filter_sql=\"owner IN($new_filter_sql'99827446572348452235')\" ON DUPLICATE KEY UPDATE lead_filter_comments=NOW(),lead_filter_sql=\"owner IN($new_filter_sql'99827446572348452235')\";";
		$rslt=mysql_query($filter_stmt, $link);
		$affected_rows = mysql_affected_rows($link);
		if ($DB > 0) {echo "$affected_rows|$filter_stmt\n<BR>";}

		$stmt = "UPDATE vicidial_campaigns SET lead_order_randomize='$lead_order_randomize',lead_order_secondary='$lead_order_secondary',lead_filter_id='ML$campaign_id',lead_order='DOWN RANK',campaign_changedate=NOW() where campaign_id='$campaign_id';";
		$rslt=mysql_query($stmt, $link);
		$affected_rows = mysql_affected_rows($link);
		if ($DB > 0) {echo "$campaign_id|$lead_order_randomize|$lead_order_secondary|ML$campaign_id|DOWN RANK|$affected_rows|$stmt\n<BR>";}

		if ($affected_rows > 0)
			{
			$phone_alias_entry[$p] .= "$login,";

			### LOG INSERTION Admin Log Table ###
			$SQL_log = "$stmt|$filter_stmt|$update_stmts|";
			$SQL_log = ereg_replace(';','',$SQL_log);
			$SQL_log = addslashes($SQL_log);
			$stmt="INSERT INTO vicidial_admin_log set event_date='$NOW_TIME', user='$PHP_AUTH_USER', ip_address='$ip', event_section='CAMPAIGNS', event_type='MODIFY', record_id='$campaign_id', event_code='MODIFY CAMPAIGN MULTI LEAD', event_sql=\"$SQL_log\", event_notes='';";
			$rslt=mysql_query($stmt, $link);
			if ($DB > 0) {echo "$campaign_id|$stmt\n<BR>";}

			echo "<BR><b>MULTI-LEAD ALT DIAL SETTINGS UPDATED</b><BR><BR>";

			}
		else
			{echo "ERROR: Problem updating campaign:  $affected_rows|$stmt\n<BR>";}

		}
	else
		{echo "ERROR: problem with data: $campaign_id\n<BR>";}

	$action='BLANK';
	}
### END multi-lead alt dial submit form





################################################################################
##### BEGIN multi-lead alt dial control form
if ($action == "BLANK")
	{
	$stmt = "SELECT lead_order_randomize,lead_order_secondary FROM vicidial_campaigns where campaign_id='$campaign_id';";
	$rslt=mysql_query($stmt, $link);
	if ($DB) {echo "$stmt\n";}
	$vc_values_ct = mysql_num_rows($rslt);
	if ($vc_values_ct > 0)
		{
		$row=mysql_fetch_row($rslt);
		$lead_order_randomize =		$row[0];
		$lead_order_secondary =		$row[1];
		}

	$lead_filter_sql='';
	$lead_filter_comments='';
	$stmt = "SELECT lead_filter_sql,lead_filter_comments FROM vicidial_lead_filters where lead_filter_id='ML$campaign_id' and lead_filter_name='DO NOT DELETE MULTI_$campaign_id';";
	$rslt=mysql_query($stmt, $link);
	if ($DB) {echo "$stmt\n";}
	$vlf_values_ct = mysql_num_rows($rslt);
	if ($vlf_values_ct > 0)
		{
		$row=mysql_fetch_row($rslt);
		$lead_filter_sql =			$row[0];
		$lead_filter_comments =		$row[1];
		if ($DB) {echo "$lead_filter_sql|$lead_filter_comments";}
		}

	echo "<TABLE><TR><TD>\n";
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";
	echo "<br>Multi-Lead Auto Alt Dialing Settings Form<form action=$PHP_SELF method=POST>\n";
	echo "<input type=hidden name=DB value=\"$DB\">\n";
	echo "<input type=hidden name=campaign_id value=\"$campaign_id\">\n";
	echo "<input type=hidden name=action value=ALT_MULTI_SUBMIT>\n";
	echo "<center><TABLE width=$section_width cellspacing=3>\n";
	echo "<tr><td align=center colspan=2>\n";


	echo "<br><b>PHONE TYPES WITHIN THE LISTS FOR THIS CAMPAIGN: <a href=\"./admin.php?ADD=31&campaign_id=$campaign_id\">$campaign_id</a></b>\n";
	echo "<TABLE width=700 cellspacing=3>\n";
	echo "<tr><td>PHONE NUMBER TYPE</td><td>CALLED</td><td>NOT CALLED</td><td>SELECTED</td><td>OLD RANK</td><td>NEW RANK</td></tr>\n";

	$leads_in_list = 0;
	$leads_in_list_N = 0;
	$leads_in_list_Y = 0;
	$stmt="SELECT owner,called_since_last_reset,count(*),rank from vicidial_list where list_id IN($camp_lists) group by owner,rank,called_since_last_reset order by rank,owner,called_since_last_reset limit 1000;";
	if ($DB) {echo "$stmt\n";}
	$rslt=mysql_query($stmt, $link);
	$types_to_print = mysql_num_rows($rslt);

	$o=0;
	$lead_list['count'] = 0;
	$lead_list['Y_count'] = 0;
	$lead_list['N_count'] = 0;
	while ($types_to_print > $o) 
		{
		$rowx=mysql_fetch_row($rslt);
		
		$lead_list['count'] = ($lead_list['count'] + $rowx[2]);
		if ($rowx[1] == 'N') 
			{
			$since_reset = 'N';
			$since_resetX = 'Y';
			}
		else 
			{
			$since_reset = 'Y';
			$since_resetX = 'N';
			} 
		$lead_list[$since_reset][$rowx[0]] = ($lead_list[$since_reset][$rowx[0]] + $rowx[2]);
		$lead_list[$since_reset.'_count'] = ($lead_list[$since_reset.'_count'] + $rowx[2]);
		#If opposite side is not set, it may not in the future so give it a value of zero
		if (!isset($lead_list[$since_resetX][$rowx[0]])) 
			{
			$lead_list[$since_resetX][$rowx[0]]=0;
			}
		$o++;
		}
 
	$o=0;
	if ($lead_list['count'] > 0)
		{
		while (list($owner,) = each($lead_list[$since_reset]))
			{
			$owner_var = preg_replace('/ |\n|\r|\t/','',$owner);
			if (eregi("1$|3$|5$|7$|9$", $o))
				{$bgcolor='bgcolor="#B9CBFD"';} 
			else
				{$bgcolor='bgcolor="#9BB9FB"';}

			$o++;
			echo "<tr $bgcolor>";
			echo "<td><font size=1>$owner</td>";
			echo "<td><font size=1>".$lead_list['Y'][$owner]."</td>";
			echo "<td><font size=1>".$lead_list['N'][$owner]." </td>";
			echo "<td><font size=1>";
			if (preg_match("/\\\'$owner\\\'|'$owner'/",$lead_filter_sql))
				{echo "<input type=checkbox name=\"$campaign_id$US$owner_var\" id=\"$campaign_id$US$owner_var\" value=\"YES\" CHECKED>";}
			else
				{echo "<input type=checkbox name=\"$campaign_id$US$owner_var\" id=\"$campaign_id$US$owner_var\" value=\"YES\">";}
			echo "</td>";
			echo "<td><font size=1>$o</td>";
			echo "<td><font size=1><input type=text size=3 maxlength=3 name=\"rank_$campaign_id$US$owner_var\" id=\"rank_$campaign_id$US$owner_var\" value=\"$o\"></td>";
			echo "</tr>\n";
			}
		}

	echo "<tr><td colspan=2><font size=1>SUBTOTALS</td><td><font size=1>$lead_list[Y_count]</td><td><font size=1>$lead_list[N_count]</td></tr>\n";
	echo "<tr bgcolor=\"#9BB9FB\"><td><font size=1>TOTAL</td><td colspan=3 align=center><font size=1>$lead_list[count]</td><td align=center><font size=1>$o</td></tr>\n";

	echo "</table></center><br>\n";
	unset($lead_list);				


	echo "</td></tr>\n";

	echo "<tr bgcolor=#B6D3FC><td align=right>List Order Randomize: </td><td align=left><select size=1 name=lead_order_randomize><option>Y</option><option>N</option><option SELECTED>$lead_order_randomize</option></select></td></tr>\n";
	echo "<tr bgcolor=#B6D3FC><td align=right>List Order Secondary: </td><td align=left><select size=1 name=lead_order_secondary><option>LEAD_ASCEND</option><option>LEAD_DESCEND</option><option>CALLTIME_ASCEND</option><option>CALLTIME_DESCEND</option><option SELECTED>$lead_order_secondary</option></select></td></tr>\n";

	echo "<tr bgcolor=#B6D3FC><td align=center colspan=2><input type=submit name=SUBMIT value=SUBMIT></td></tr>\n";
	echo "</TABLE></center>\n";
	echo "</TD></TR></TABLE>\n";
	}
### END multi-lead alt dial control form










$ENDtime = date("U");
$RUNtime = ($ENDtime - $STARTtime);
echo "\n\n\n<br><br><br>\n<font size=1> runtime: $RUNtime seconds &nbsp; &nbsp; &nbsp; &nbsp; Version: $admin_version &nbsp; &nbsp; Build: $build</font>";

?>

</body>
</html>
