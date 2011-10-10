<?php
# admin_search_lead.php
# 
# Copyright (C) 2010  Matt Florell <vicidial@gmail.com>    LICENSE: AGPLv2
#
# AST GUI database administration search for lead info
# admin_modify_lead.php
#
# this is the administration lead information search screen, the administrator 
# just needs to enter the leadID and then they can view and modify the information
# in the record for that lead
#
# changes:
# 60620-1055 - Added variable filtering to eliminate SQL injection attack threat
#            - Added required user/pass to gain access to this page
#            - Changed results to multi-record
# 80710-0023 - Added searching by list, user, status
# 90121-0500 - Added filter for phone to remove non-digits
# 90309-1828 - Added admin_log logging
# 90310-2146 - Added admin header
# 90508-0644 - Changed to PHP long tags
# 90917-2307 - Added alternate phone number searching option
# 90921-0713 - Removed SELECT STAR
# 100224-1621 - Added first/last name search and changed format of the page
# 100405-1331 - Added log search ability
#

require("dbconnect.php");

$PHP_AUTH_USER=$_SERVER['PHP_AUTH_USER'];
$PHP_AUTH_PW=$_SERVER['PHP_AUTH_PW'];
$PHP_SELF=$_SERVER['PHP_SELF'];
if (isset($_GET["vendor_id"]))			{$vendor_id=$_GET["vendor_id"];}
	elseif (isset($_POST["vendor_id"]))	{$vendor_id=$_POST["vendor_id"];}
if (isset($_GET["first_name"]))				{$first_name=$_GET["first_name"];}
	elseif (isset($_POST["first_name"]))	{$first_name=$_POST["first_name"];}
if (isset($_GET["last_name"]))			{$last_name=$_GET["last_name"];}
	elseif (isset($_POST["last_name"]))	{$last_name=$_POST["last_name"];}
if (isset($_GET["phone"]))				{$phone=$_GET["phone"];}
	elseif (isset($_POST["phone"]))		{$phone=$_POST["phone"];}
if (isset($_GET["lead_id"]))			{$lead_id=$_GET["lead_id"];}
	elseif (isset($_POST["lead_id"]))	{$lead_id=$_POST["lead_id"];}
if (isset($_GET["log_phone"]))				{$log_phone=$_GET["log_phone"];}
	elseif (isset($_POST["log_phone"]))		{$log_phone=$_POST["log_phone"];}
if (isset($_GET["log_lead_id"]))			{$log_lead_id=$_GET["log_lead_id"];}
	elseif (isset($_POST["log_lead_id"]))	{$log_lead_id=$_POST["log_lead_id"];}
if (isset($_GET["submit"]))				{$submit=$_GET["submit"];}
	elseif (isset($_POST["submit"]))	{$submit=$_POST["submit"];}
if (isset($_GET["ENVIAR"]))				{$ENVIAR=$_GET["ENVIAR"];}
	elseif (isset($_POST["ENVIAR"]))	{$ENVIAR=$_POST["ENVIAR"];}
if (isset($_GET["DB"]))					{$DB=$_GET["DB"];}
	elseif (isset($_POST["DB"]))		{$DB=$_POST["DB"];}
if (isset($_GET["status"]))				{$status=$_GET["status"];}
	elseif (isset($_POST["status"]))	{$status=$_POST["status"];}
if (isset($_GET["user"]))				{$user=$_GET["user"];}
	elseif (isset($_POST["user"]))		{$user=$_POST["user"];}
if (isset($_GET["list_id"]))			{$list_id=$_GET["list_id"];}
	elseif (isset($_POST["list_id"]))	{$list_id=$_POST["list_id"];}
if (isset($_GET["alt_phone_search"]))			{$alt_phone_search=$_GET["alt_phone_search"];}
	elseif (isset($_POST["alt_phone_search"]))	{$alt_phone_search=$_POST["alt_phone_search"];}

$PHP_AUTH_USER = ereg_replace("[^0-9a-zA-Z]","",$PHP_AUTH_USER);
$PHP_AUTH_PW = ereg_replace("[^0-9a-zA-Z]","",$PHP_AUTH_PW);
$phone = ereg_replace("[^0-9]","",$phone);
if (strlen($alt_phone_search) < 2) {$alt_phone_search='No';}

$STARTtime = date("U");
$TODAY = date("Y-m-d");
$NOW_TIME = date("Y-m-d H:i:s");

$vicidial_list_fields = 'lead_id,entry_date,modify_date,status,user,vendor_lead_code,source_id,list_id,gmt_offset_now,called_since_last_reset,phone_code,phone_number,title,first_name,middle_initial,last_name,address1,address2,address3,city,state,province,postal_code,country_code,gender,date_of_birth,alt_phone,email,security_phrase,comments,called_count,last_local_call_time,rank,owner';

$stmt="SELECT count(*) from vicidial_users where user='$PHP_AUTH_USER' and pass='$PHP_AUTH_PW' and user_level > 7 and modify_leads='1';";
if ($DB) {echo "|$stmt|\n";}
$rslt=mysql_query($stmt, $link);
$row=mysql_fetch_row($rslt);
$auth=$row[0];

if ($WeBRooTWritablE > 0)
	{$fp = fopen ("./project_auth_entries.txt", "a");}

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
	if ($auth>0)
		{
		$office_no=strtoupper($PHP_AUTH_USER);
		$password=strtoupper($PHP_AUTH_PW);
		$stmt="SELECT full_name,modify_leads from vicidial_users where user='$PHP_AUTH_USER' and pass='$PHP_AUTH_PW'";
		$rslt=mysql_query($stmt, $link);
		$row=mysql_fetch_row($rslt);
		$LOGfullname =		$row[0];
		$LOGmodify_leads =	$row[1];

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
<title>ADMINISTRATION: Pesquisar Registro
<?php 

##### BEGIN Set variables to make header show properly #####
$ADD =					'100';
$hh =					'lists';
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
$lists_color =		'#FFFF99';
$lists_font =		'BLACK';
$lists_color =		'#E6E6E6';
$subcamp_color =	'#C6C6C6';
##### END Set variables to make header show properly #####

require("admin_header.php");


echo " Lead search: $vendor_id $phone $lead_id $status $list_id $user\n";
echo date("l F j, Y G:i:s A");
echo "<BR>\n";

if ( (!$vendor_id) and (!$phone)  and (!$lead_id) and (!$log_phone)  and (!$log_lead_id) and ( (strlen($status)<1) and (strlen($list_id)<1) and (strlen($user)<1) ) and ( (strlen($first_name)<1) and (strlen($last_name)<1) )) 
	{
	### Lead search
	echo "<br><center>\n";
	echo "<form method=post name=search action=\"$PHP_SELF\">\n";
	echo "<input type=hidden name=DB value=\"$DB\">\n";
	echo "<TABLE CELLPADDING=3 CELLSPACING=3>";
	echo "<TR>";
	echo "<TD colspan=3 align=center><b>Pesquisar Registro Opçãos:</TD>";
	echo "</TR><TR bgcolor=#B9CBFD>";

	echo "<TD ALIGN=right>Vendor ID(código do fornecedor): &nbsp; </TD><TD ALIGN=left><input type=text name=vendor_id size=10 maxlength=10></TD>";
	echo "<TD><input type=submit name=submit value=ENVIAR></TD>\n";
	echo "</TR><TR>";
	echo "<TD colspan=3 align=center> &nbsp; </TD>";
	echo "</TR><TR bgcolor=#B9CBFD>";

	echo "<TD ALIGN=right>Home Número do Telefone: &nbsp; </TD><TD ALIGN=left><input type=text name=phone size=14 maxlength=18></TD>";
	echo "<TD rowspan=2><input type=submit name=submit value=ENVIAR></TD>\n";
	echo "</TR><TR bgcolor=#B9CBFD>";
	echo "<TD ALIGN=right>Alt phone search: &nbsp; </TD><TD ALIGN=left><select size=1 name=alt_phone_search><option>No</option><option>Yes</option><option SELECTED>$alt_phone_search</option></select></TD>";
	echo "</TR><TR>";
	echo "<TD colspan=3 align=center> &nbsp; </TD>";
	echo "</TR><TR bgcolor=#B9CBFD>";

	echo "<TD ALIGN=right>Lead ID: &nbsp; </TD><TD ALIGN=left><input type=text name=lead_id size=10 maxlength=10></TD>";
	echo "<TD><input type=submit name=submit value=ENVIAR></TD>\n";
	echo "</TR><TR>";
	echo "<TD colspan=3 align=center> &nbsp; </TD>";
	echo "</TR><TR bgcolor=#B9CBFD>";

	echo "<TD ALIGN=right>Status: &nbsp; </TD><TD ALIGN=left><input type=text name=status size=7 maxlength=6></TD>";
	echo "<TD rowspan=3><input type=submit name=submit value=ENVIAR></TD>\n";
	echo "</TR><TR bgcolor=#B9CBFD>";
	echo "<TD ALIGN=right>ID da Lista: &nbsp; </TD><TD ALIGN=left><input type=text name=list_id size=15 maxlength=14></TD>";
	echo "</TR><TR bgcolor=#B9CBFD>";
	echo "<TD ALIGN=right>Usuário: &nbsp; </TD><TD ALIGN=left><input type=text name=user size=15 maxlength=20></TD>";
	echo "</TR><TR>";
	echo "<TD colspan=3 align=center> &nbsp; </TD>";
	echo "</TR><TR bgcolor=#B9CBFD>";

	echo "<TD ALIGN=right>Nome: &nbsp; </TD><TD ALIGN=left><input type=text name=first_name size=15 maxlength=30></TD>";
	echo "<TD rowspan=2><input type=submit name=submit value=ENVIAR></TD>\n";
	echo "</TR><TR bgcolor=#B9CBFD>";
	echo "<TD ALIGN=right>Sobrenome: &nbsp; </TD><TD ALIGN=left><input type=text name=last_name size=15 maxlength=30></TD>";
	echo "</TR>";


	### Log search
	echo "<br><center>\n";
	echo "<TD colspan=3 align=center> &nbsp; </TD>";
	echo "</TR><TR>";
	echo "<TD colspan=3 align=center><b>Log Search Opçãos:</TD>";
	echo "</TR><TR bgcolor=#B9CBFD>";

	echo "<TD ALIGN=right>Lead ID: &nbsp; </TD><TD ALIGN=left><input type=text name=log_lead_id size=10 maxlength=10></TD>";
	echo "<TD><input type=submit name=submit value=ENVIAR></TD>\n";
	echo "</TR><TR>";
	echo "<TD colspan=3 align=center> &nbsp; </TD>";
	echo "</TR><TR bgcolor=#B9CBFD>";

	echo "<TD ALIGN=right>Número do Telefone Dialed: &nbsp; </TD><TD ALIGN=left><input type=text name=log_phone size=18 maxlength=18></TD>";
	echo "<TD><input type=submit name=submit value=ENVIAR></TD>\n";
	echo "</TR><TR>";
	echo "<TD colspan=3 align=center> &nbsp; </TD>";
	echo "</TR><TR bgcolor=#B9CBFD>";


	echo "</TABLE>\n";
	echo "</form>\n</center>\n";
	echo "</body></html>\n";
	exit;
	}

else
	{
	##### BEGIN Log search #####
	if ( (strlen($log_lead_id)>0) or (strlen($log_phone)>0) )
		{
		if (strlen($log_lead_id)>0)
			{
			$stmtA="SELECT lead_id,phone_number,campaign_id,call_date,status,user,list_id,length_in_sec,alt_dial from vicidial_log where lead_id='" . mysql_real_escape_string($log_lead_id) . "'";
			$stmtB="SELECT lead_id,phone_number,campaign_id,call_date,status,user,list_id,length_in_sec from vicidial_closer_log where lead_id='" . mysql_real_escape_string($log_lead_id) . "'";
			}
		if (strlen($log_phone)>0)
			{
			$stmtA="SELECT lead_id,phone_number,campaign_id,call_date,status,user,list_id,length_in_sec,alt_dial from vicidial_log where phone_number='" . mysql_real_escape_string($log_phone) . "'";
			$stmtB="SELECT lead_id,phone_number,campaign_id,call_date,status,user,list_id,length_in_sec from vicidial_closer_log where phone_number='" . mysql_real_escape_string($log_phone) . "'";
			$stmtC="SELECT extension,caller_id_number,did_id,call_date from vicidial_did_log where caller_id_number='" . mysql_real_escape_string($log_phone) . "'";
			}
		
		$rslt=mysql_query("$stmtA", $link);
		$results_to_print = mysql_num_rows($rslt);
		if ( ($results_to_print < 1) and ($results_to_printX < 1) )
			{
			echo "\n<br><br><center>\n";
			echo "<b>There are no outbound calls matching your search criteria</b><br><br>\n";
			echo "</center>\n";
			}
		else
			{
			echo "<BR><b>SAINTE LOG RESULTS: $results_to_print</b><BR>\n";
			echo "<TABLE BGCOLOR=WHITE CELLPADDING=1 CELLSPACING=0 WIDTH=770>\n";
			echo "<TR BGCOLOR=BLACK>\n";
			echo "<TD ALIGN=LEFT VALIGN=TOP><FONT FACE=\"ARIAL,HELVETICA\" COLOR=WHITE><B>#</B></FONT></TD>\n";
			echo "<TD ALIGN=CENTER VALIGN=TOP><FONT FACE=\"ARIAL,HELVETICA\" COLOR=WHITE><B>LEAD ID</B> &nbsp;</FONT></TD>\n";
			echo "<TD ALIGN=CENTER VALIGN=TOP><FONT FACE=\"ARIAL,HELVETICA\" COLOR=WHITE><B>PHONE</B> &nbsp;</FONT></TD>\n";
			echo "<TD ALIGN=CENTER VALIGN=TOP><FONT FACE=\"ARIAL,HELVETICA\" COLOR=WHITE><B>CAMPANHA</B> &nbsp;</FONT></TD>\n";
			echo "<TD ALIGN=CENTER VALIGN=TOP><FONT FACE=\"ARIAL,HELVETICA\" COLOR=WHITE><B>CALL DATA</B> &nbsp;</FONT></TD>\n";
			echo "<TD ALIGN=CENTER VALIGN=TOP><FONT FACE=\"ARIAL,HELVETICA\" COLOR=WHITE><B>STATUS</B> &nbsp;</FONT></TD>\n";
			echo "<TD ALIGN=CENTER VALIGN=TOP><FONT FACE=\"ARIAL,HELVETICA\" COLOR=WHITE><B>USER</B> &nbsp;</FONT></TD>\n";
			echo "<TD ALIGN=CENTER VALIGN=TOP><FONT FACE=\"ARIAL,HELVETICA\" COLOR=WHITE><B>ID DA LISTA</B> &nbsp;</FONT></TD>\n";
			echo "<TD ALIGN=CENTER VALIGN=TOP><FONT FACE=\"ARIAL,HELVETICA\" COLOR=WHITE><B>LENGTH</B> &nbsp;</FONT></TD>\n";
			echo "<TD ALIGN=CENTER VALIGN=TOP><FONT FACE=\"ARIAL,HELVETICA\" COLOR=WHITE><B>DIAL</B></FONT></TD>\n";
			echo "</TR>\n";
			$o=0;
			while ($results_to_print > $o)
				{
				$row=mysql_fetch_row($rslt);
				$o++;
				$search_lead = $row[0];
				if (eregi("1$|3$|5$|7$|9$", $o))
					{$bgcolor='bgcolor="#B9CBFD"';} 
				else
					{$bgcolor='bgcolor="#9BB9FB"';}
				echo "<TR $bgcolor>\n";
				echo "<TD ALIGN=LEFT><FONT FACE=\"ARIAL,HELVETICA\" SIZE=1>$o</FONT></TD>\n";
				echo "<TD ALIGN=CENTER><FONT FACE=\"ARIAL,HELVETICA\" SIZE=1><a href=\"admin_modify_lead.php?lead_id=$row[0]\" target=\"_blank\">$row[0]</a></FONT></TD>\n";
				echo "<TD ALIGN=CENTER><FONT FACE=\"ARIAL,HELVETICA\" SIZE=1>$row[1]</FONT></TD>\n";
				echo "<TD ALIGN=CENTER><FONT FACE=\"ARIAL,HELVETICA\" SIZE=1>$row[2]</FONT></TD>\n";
				echo "<TD ALIGN=CENTER><FONT FACE=\"ARIAL,HELVETICA\" SIZE=1>$row[3]</FONT></TD>\n";
				echo "<TD ALIGN=CENTER><FONT FACE=\"ARIAL,HELVETICA\" SIZE=1>$row[4]</FONT></TD>\n";
				echo "<TD ALIGN=CENTER><FONT FACE=\"ARIAL,HELVETICA\" SIZE=1>$row[5]</FONT></TD>\n";
				echo "<TD ALIGN=CENTER><FONT FACE=\"ARIAL,HELVETICA\" SIZE=1>$row[6]</FONT></TD>\n";
				echo "<TD ALIGN=CENTER><FONT FACE=\"ARIAL,HELVETICA\" SIZE=1>$row[7]</FONT></TD>\n";
				echo "<TD ALIGN=CENTER><FONT FACE=\"ARIAL,HELVETICA\" SIZE=1>$row[8]</FONT></TD>\n";
				echo "</TR>\n";
				}
			echo "</TABLE>\n";
			}

		$rslt=mysql_query("$stmtB", $link);
		$results_to_print = mysql_num_rows($rslt);
		if ( ($results_to_print < 1) and ($results_to_printX < 1) )
			{
			echo "\n<br><br><center>\n";
			echo "<b>There are no inbound calls matching your search criteria</b><br><br>\n";
			echo "</center>\n";
			}
		else
			{
			echo "<BR><b>ENTRANTE LOG RESULTS: $results_to_print</b><BR>\n";
			echo "<TABLE BGCOLOR=WHITE CELLPADDING=1 CELLSPACING=0 WIDTH=770>\n";
			echo "<TR BGCOLOR=BLACK>\n";
			echo "<TD ALIGN=LEFT VALIGN=TOP><FONT FACE=\"ARIAL,HELVETICA\" COLOR=WHITE><B>#</B></FONT></TD>\n";
			echo "<TD ALIGN=CENTER VALIGN=TOP><FONT FACE=\"ARIAL,HELVETICA\" COLOR=WHITE><B>LEAD ID</B> &nbsp;</FONT></TD>\n";
			echo "<TD ALIGN=CENTER VALIGN=TOP><FONT FACE=\"ARIAL,HELVETICA\" COLOR=WHITE><B>PHONE</B> &nbsp;</FONT></TD>\n";
			echo "<TD ALIGN=CENTER VALIGN=TOP><FONT FACE=\"ARIAL,HELVETICA\" COLOR=WHITE><B>INGROUP</B> &nbsp;</FONT></TD>\n";
			echo "<TD ALIGN=CENTER VALIGN=TOP><FONT FACE=\"ARIAL,HELVETICA\" COLOR=WHITE><B>CALL DATA</B> &nbsp;</FONT></TD>\n";
			echo "<TD ALIGN=CENTER VALIGN=TOP><FONT FACE=\"ARIAL,HELVETICA\" COLOR=WHITE><B>STATUS</B> &nbsp;</FONT></TD>\n";
			echo "<TD ALIGN=CENTER VALIGN=TOP><FONT FACE=\"ARIAL,HELVETICA\" COLOR=WHITE><B>USER</B> &nbsp;</FONT></TD>\n";
			echo "<TD ALIGN=CENTER VALIGN=TOP><FONT FACE=\"ARIAL,HELVETICA\" COLOR=WHITE><B>ID DA LISTA</B> &nbsp;</FONT></TD>\n";
			echo "<TD ALIGN=CENTER VALIGN=TOP><FONT FACE=\"ARIAL,HELVETICA\" COLOR=WHITE><B>LENGTH</B> &nbsp;</FONT></TD>\n";
			echo "</TR>\n";
			$o=0;
			while ($results_to_print > $o)
				{
				$row=mysql_fetch_row($rslt);
				$o++;
				$search_lead = $row[0];
				if (eregi("1$|3$|5$|7$|9$", $o))
					{$bgcolor='bgcolor="#B9CBFD"';} 
				else
					{$bgcolor='bgcolor="#9BB9FB"';}
				echo "<TR $bgcolor>\n";
				echo "<TD ALIGN=LEFT><FONT FACE=\"ARIAL,HELVETICA\" SIZE=1>$o</FONT></TD>\n";
				echo "<TD ALIGN=CENTER><FONT FACE=\"ARIAL,HELVETICA\" SIZE=1><a href=\"admin_modify_lead.php?lead_id=$row[0]\" target=\"_blank\">$row[0]</a></FONT></TD>\n";
				echo "<TD ALIGN=CENTER><FONT FACE=\"ARIAL,HELVETICA\" SIZE=1>$row[1]</FONT></TD>\n";
				echo "<TD ALIGN=CENTER><FONT FACE=\"ARIAL,HELVETICA\" SIZE=1>$row[2]</FONT></TD>\n";
				echo "<TD ALIGN=CENTER><FONT FACE=\"ARIAL,HELVETICA\" SIZE=1>$row[3]</FONT></TD>\n";
				echo "<TD ALIGN=CENTER><FONT FACE=\"ARIAL,HELVETICA\" SIZE=1>$row[4]</FONT></TD>\n";
				echo "<TD ALIGN=CENTER><FONT FACE=\"ARIAL,HELVETICA\" SIZE=1>$row[5]</FONT></TD>\n";
				echo "<TD ALIGN=CENTER><FONT FACE=\"ARIAL,HELVETICA\" SIZE=1>$row[6]</FONT></TD>\n";
				echo "<TD ALIGN=CENTER><FONT FACE=\"ARIAL,HELVETICA\" SIZE=1>$row[7]</FONT></TD>\n";
				echo "</TR>\n";
				}
			echo "</TABLE>\n";
			}

		if (strlen($stmtC) > 10)
			{
			$rslt=mysql_query("$stmtC", $link);
			$results_to_print = mysql_num_rows($rslt);
			if ( ($results_to_print < 1) and ($results_to_printX < 1) )
				{
				echo "\n<br><br><center>\n";
				echo "<b>There are no inbound did calls matching your search criteria</b><br><br>\n";
				echo "</center>\n";
				}
			else
				{
				echo "<BR><b>ENTRANTE DID LOG RESULTS: $results_to_print</b><BR>\n";
				echo "<TABLE BGCOLOR=WHITE CELLPADDING=1 CELLSPACING=0 WIDTH=770>\n";
				echo "<TR BGCOLOR=BLACK>\n";
				echo "<TD ALIGN=LEFT VALIGN=TOP><FONT FACE=\"ARIAL,HELVETICA\" COLOR=WHITE><B>#</B></FONT></TD>\n";
				echo "<TD ALIGN=CENTER VALIGN=TOP><FONT FACE=\"ARIAL,HELVETICA\" COLOR=WHITE><B>DID</B> &nbsp;</FONT></TD>\n";
				echo "<TD ALIGN=CENTER VALIGN=TOP><FONT FACE=\"ARIAL,HELVETICA\" COLOR=WHITE><B>PHONE</B> &nbsp;</FONT></TD>\n";
				echo "<TD ALIGN=CENTER VALIGN=TOP><FONT FACE=\"ARIAL,HELVETICA\" COLOR=WHITE><B>DID ID</B> &nbsp;</FONT></TD>\n";
				echo "<TD ALIGN=CENTER VALIGN=TOP><FONT FACE=\"ARIAL,HELVETICA\" COLOR=WHITE><B>CALL DATA</B> &nbsp;</FONT></TD>\n";
				echo "</TR>\n";
				$o=0;
				while ($results_to_print > $o)
					{
					$row=mysql_fetch_row($rslt);
					$o++;
					$search_lead = $row[0];
					if (eregi("1$|3$|5$|7$|9$", $o))
						{$bgcolor='bgcolor="#B9CBFD"';} 
					else
						{$bgcolor='bgcolor="#9BB9FB"';}
					echo "<TR $bgcolor>\n";
					echo "<TD ALIGN=LEFT><FONT FACE=\"ARIAL,HELVETICA\" SIZE=1>$o</FONT></TD>\n";
					echo "<TD ALIGN=CENTER><FONT FACE=\"ARIAL,HELVETICA\" SIZE=1>$row[0]</FONT></TD>\n";
					echo "<TD ALIGN=CENTER><FONT FACE=\"ARIAL,HELVETICA\" SIZE=1>$row[1]</FONT></TD>\n";
					echo "<TD ALIGN=CENTER><FONT FACE=\"ARIAL,HELVETICA\" SIZE=1>$row[2]</FONT></TD>\n";
					echo "<TD ALIGN=CENTER><FONT FACE=\"ARIAL,HELVETICA\" SIZE=1>$row[3]</FONT></TD>\n";
					echo "</TR>\n";
					}
				echo "</TABLE>\n";
				}
			}

		### LOG INSERTION Admin Log Table ###
		$SQL_log = "$stmt|";
		$SQL_log = ereg_replace(';','',$SQL_log);
		$SQL_log = addslashes($SQL_log);
		$stmt="INSERT INTO vicidial_admin_log set event_date='$NOW_TIME', user='$PHP_AUTH_USER', ip_address='$ip', event_section='LEADS', event_type='SEARCH', record_id='$search_lead', event_code='ADMIN SEARCH LEAD', event_sql=\"$SQL_log\", event_notes='';";
		if ($DB) {echo "|$stmt|\n";}
		$rslt=mysql_query($stmt, $link);

		$ENDtime = date("U");

		$RUNtime = ($ENDtime - $STARTtime);

		echo "\n\n\n<br><br><br>\n<a href=\"$PHP_SELF\">NOVA PESQUISA</a>";

		echo "\n\n\n<br><br><br>\nScript runtime: $RUNtime seconds";

		echo "\n\n\n</body></html>";

		exit;
		}
	##### END Log search #####





	##### BEGIN Lead search #####
	if ($vendor_id)
		{
		$stmt="SELECT $vicidial_list_fields from vicidial_list where vendor_lead_code='" . mysql_real_escape_string($vendor_id) . "'";
		}
	else
		{
		if ($phone)
			{
			if ($alt_phone_search=="Yes")
				{
				$stmt="SELECT $vicidial_list_fields from vicidial_list where phone_number='" . mysql_real_escape_string($phone) . "' or alt_phone='" . mysql_real_escape_string($phone) . "' or address3='" . mysql_real_escape_string($phone) . "'";
				}
			else
				{
				$stmt="SELECT $vicidial_list_fields from vicidial_list where phone_number='" . mysql_real_escape_string($phone) . "'";
				}
			}
		else
			{
			if ($lead_id)
				{
				$stmt="SELECT $vicidial_list_fields from vicidial_list where lead_id='" . mysql_real_escape_string($lead_id) . "'";
				}
			else
				{
				if ( (strlen($status)>0) or (strlen($list_id)>0) or (strlen($user)>0) )
					{
					$statusSQL = '';
					$list_idSQL = '';
					$userSQL = '';
					if (strlen($status)>0)	
						{
						$statusSQL = "status='" . mysql_real_escape_string($status) . "'"; $SQLctA++;
						}
					if (strlen($list_id)>0) 
						{
						if ($SQLctA > 0) {$andA = 'and';}
						$list_idSQL = "$andA list_id='" . mysql_real_escape_string($list_id) . "'"; $SQLctB++;
						}
					if (strlen($user)>0)	
						{
						if ( ($SQLctA > 0) or ($SQLctB > 0) ) {$andB = 'and';}
						$userSQL = "$andB user='" . mysql_real_escape_string($user) . "'";
						}
					$stmt="SELECT $vicidial_list_fields from vicidial_list where $statusSQL $list_idSQL $userSQL";
					}
				else
					{
					if ( (strlen($first_name)>0) or (strlen($last_name)>0) )
						{
						$first_nameSQL = '';
						$last_nameSQL = '';
						if (strlen($first_name)>0)	
							{
							$first_nameSQL = "first_name='" . mysql_real_escape_string($first_name) . "'"; $SQLctA++;
							}
						if (strlen($last_name)>0) 
							{
							if ($SQLctA > 0) {$andA = 'and';}
							$last_nameSQL = "$andA last_name='" . mysql_real_escape_string($last_name) . "'";
							}
						$stmt="SELECT $vicidial_list_fields from vicidial_list where $first_nameSQL $last_nameSQL";
						}
					else
						{
						print "ERROR: you must search for something! Go back and search for something";
						exit;
						}
					}
				}
			}
		}

	$stmt_alt='';
	$results_to_printX=0;
	if ( ($alt_phone_search=="Yes") and (strlen($phone) > 4) )
		{
		$stmtX="SELECT lead_id from vicidial_list_alt_phones where phone_number='" . mysql_real_escape_string($phone) . "' limit 1000;";
		$rsltX=mysql_query($stmtX, $link);
		$results_to_printX = mysql_num_rows($rsltX);
		if ($DB)
			{echo "\n\n$results_to_printX|$stmtX\n\n";}
		$o=0;
		while ($results_to_printX > $o)
			{
			$row=mysql_fetch_row($rsltX);
			if ($o > 0) {$stmt_alt .= ",";}
			$stmt_alt .= "'$row[0]'";
			$o++;
			}
		if (strlen($stmt_alt) > 2)
			{$stmt_alt = "or lead_id IN($stmt_alt)";}
		}

	$stmt = "$stmt$stmt_alt order by modify_date desc limit 1000;";

	if ($DB)
		{
		echo "\n\n$stmt\n\n";
		}

	$rslt=mysql_query("$stmt", $link);
	$results_to_print = mysql_num_rows($rslt);
	if ( ($results_to_print < 1) and ($results_to_printX < 1) )
		{
		echo date("l F j, Y G:i:s A");
		echo "\n<br><br><center>\n";
		echo "<b>As variáveis de pesquisa digitadas não estão ativas no sistema</b><br><br>\n";
		echo "<b>Por favor volte e verifique as informações digitadas, então envie novamente</b>\n";
		echo "</center>\n";
		echo "</body></html>\n";
		exit;
		}
	else
		{
		echo "<b>RESULTS: $results_to_print</b><BR><BR>\n";
		echo "<TABLE BGCOLOR=WHITE CELLPADDING=1 CELLSPACING=0 WIDTH=770>\n";
		echo "<TR BGCOLOR=BLACK>\n";
		echo "<TD ALIGN=LEFT VALIGN=TOP><FONT FACE=\"ARIAL,HELVETICA\" COLOR=WHITE><B>#</B></FONT></TD>\n";
		echo "<TD ALIGN=CENTER VALIGN=TOP><FONT FACE=\"ARIAL,HELVETICA\" COLOR=WHITE><B>LEAD ID</B> &nbsp;</FONT></TD>\n";
		echo "<TD ALIGN=CENTER VALIGN=TOP><FONT FACE=\"ARIAL,HELVETICA\" COLOR=WHITE><B>STATUS</B> &nbsp;</FONT></TD>\n";
		echo "<TD ALIGN=CENTER VALIGN=TOP><FONT FACE=\"ARIAL,HELVETICA\" COLOR=WHITE><B>VENDOR ID</B> &nbsp;</FONT></TD>\n";
		echo "<TD ALIGN=CENTER VALIGN=TOP><FONT FACE=\"ARIAL,HELVETICA\" COLOR=WHITE><B>LAST AGENT</B> &nbsp;</FONT></TD>\n";
		echo "<TD ALIGN=CENTER VALIGN=TOP><FONT FACE=\"ARIAL,HELVETICA\" COLOR=WHITE><B>ID DA LISTA</B> &nbsp;</FONT></TD>\n";
		echo "<TD ALIGN=CENTER VALIGN=TOP><FONT FACE=\"ARIAL,HELVETICA\" COLOR=WHITE><B>PHONE</B> &nbsp;</FONT></TD>\n";
		echo "<TD ALIGN=CENTER VALIGN=TOP><FONT FACE=\"ARIAL,HELVETICA\" COLOR=WHITE><B>NOME</B> &nbsp;</FONT></TD>\n";
		echo "<TD ALIGN=CENTER VALIGN=TOP><FONT FACE=\"ARIAL,HELVETICA\" COLOR=WHITE><B>CITY</B> &nbsp;</FONT></TD>\n";
		echo "<TD ALIGN=CENTER VALIGN=TOP><FONT FACE=\"ARIAL,HELVETICA\" COLOR=WHITE><B>SECURITY</B> &nbsp;</FONT></TD>\n";
		echo "<TD ALIGN=CENTER VALIGN=TOP><FONT FACE=\"ARIAL,HELVETICA\" COLOR=WHITE><B>LAST CALL</B></FONT></TD>\n";
		echo "</TR>\n";
		$o=0;
		while ($results_to_print > $o)
			{
			$row=mysql_fetch_row($rslt);
			$o++;
			$search_lead = $row[0];
			if (eregi("1$|3$|5$|7$|9$", $o))
				{$bgcolor='bgcolor="#B9CBFD"';} 
			else
				{$bgcolor='bgcolor="#9BB9FB"';}
			echo "<TR $bgcolor>\n";
			echo "<TD ALIGN=LEFT><FONT FACE=\"ARIAL,HELVETICA\" SIZE=1>$o</FONT></TD>\n";
			echo "<TD ALIGN=CENTER><FONT FACE=\"ARIAL,HELVETICA\" SIZE=1><a href=\"admin_modify_lead.php?lead_id=$row[0]\" target=\"_blank\">$row[0]</a></FONT></TD>\n";
			echo "<TD ALIGN=CENTER><FONT FACE=\"ARIAL,HELVETICA\" SIZE=1>$row[3]</FONT></TD>\n";
			echo "<TD ALIGN=CENTER><FONT FACE=\"ARIAL,HELVETICA\" SIZE=1>$row[5]</FONT></TD>\n";
			echo "<TD ALIGN=CENTER><FONT FACE=\"ARIAL,HELVETICA\" SIZE=1>$row[4]</FONT></TD>\n";
			echo "<TD ALIGN=CENTER><FONT FACE=\"ARIAL,HELVETICA\" SIZE=1>$row[7]</FONT></TD>\n";
			echo "<TD ALIGN=CENTER><FONT FACE=\"ARIAL,HELVETICA\" SIZE=1>$row[11]</FONT></TD>\n";
			echo "<TD ALIGN=CENTER><FONT FACE=\"ARIAL,HELVETICA\" SIZE=1>$row[13] $row[15]</FONT></TD>\n";
			echo "<TD ALIGN=CENTER><FONT FACE=\"ARIAL,HELVETICA\" SIZE=1>$row[19]</FONT></TD>\n";
			echo "<TD ALIGN=CENTER><FONT FACE=\"ARIAL,HELVETICA\" SIZE=1>$row[28]</FONT></TD>\n";
			echo "<TD ALIGN=CENTER><FONT FACE=\"ARIAL,HELVETICA\" SIZE=1>$row[31]</FONT></TD>\n";
			echo "</TR>\n";
			}
		echo "</TABLE>\n";
		}

	### LOG INSERTION Admin Log Table ###
	$SQL_log = "$stmt|";
	$SQL_log = ereg_replace(';','',$SQL_log);
	$SQL_log = addslashes($SQL_log);
	$stmt="INSERT INTO vicidial_admin_log set event_date='$NOW_TIME', user='$PHP_AUTH_USER', ip_address='$ip', event_section='LEADS', event_type='SEARCH', record_id='$search_lead', event_code='ADMIN SEARCH LEAD', event_sql=\"$SQL_log\", event_notes='';";
	if ($DB) {echo "|$stmt|\n";}
	$rslt=mysql_query($stmt, $link);
	}
	##### END Lead search #####




$ENDtime = date("U");

$RUNtime = ($ENDtime - $STARTtime);

echo "\n\n\n<br><br><br>\n<a href=\"$PHP_SELF\">NOVA PESQUISA</a>";


echo "\n\n\n<br><br><br>\nScript runtime: $RUNtime seconds";


?>



</body>
</html>
