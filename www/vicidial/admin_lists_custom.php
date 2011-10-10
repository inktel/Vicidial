<?php
# admin_lists_custom.php
# 
# Copyright (C) 2010  Matt Florell <vicidial@gmail.com>    LICENSE: AGPLv2
#
# this screen manages the custom lists fields in ViciDial
#
# changes:
# 100506-1801 - First Build
# 100507-1027 - Added name position and options position, added extra space for name and help
# 100508-1855 - Added field_order to allow for multiple fields on the same line
# 100509-0922 - Added copy fields options
# 100510-1130 - Added DISPLAY field type option
# 100629-0200 - Added SCRIPT field type option
# 100722-1313 - Added field validation for label and name
# 100728-1724 - Added field validation for select lists and checkbox/radio buttons
# 100916-1754 - Do not show help in example form if help is empty
# 101228-2049 - Fixed missing PHP long tag
#

$admin_version = '2.4-10';
$build = '101228-2049';


require("dbconnect.php");

$PHP_AUTH_USER=$_SERVER['PHP_AUTH_USER'];
$PHP_AUTH_PW=$_SERVER['PHP_AUTH_PW'];
$PHP_SELF=$_SERVER['PHP_SELF'];
if (isset($_GET["DB"]))							{$DB=$_GET["DB"];}
	elseif (isset($_POST["DB"]))				{$DB=$_POST["DB"];}
if (isset($_GET["action"]))						{$action=$_GET["action"];}
	elseif (isset($_POST["action"]))			{$action=$_POST["action"];}
if (isset($_GET["list_id"]))					{$list_id=$_GET["list_id"];}
	elseif (isset($_POST["list_id"]))			{$list_id=$_POST["list_id"];}
if (isset($_GET["field_id"]))					{$field_id=$_GET["field_id"];}
	elseif (isset($_POST["field_id"]))			{$field_id=$_POST["field_id"];}
if (isset($_GET["field_label"]))				{$field_label=$_GET["field_label"];}
	elseif (isset($_POST["field_label"]))		{$field_label=$_POST["field_label"];}
if (isset($_GET["field_name"]))					{$field_name=$_GET["field_name"];}
	elseif (isset($_POST["field_name"]))		{$field_name=$_POST["field_name"];}
if (isset($_GET["field_description"]))			{$field_description=$_GET["field_description"];}
	elseif (isset($_POST["field_description"]))	{$field_description=$_POST["field_description"];}
if (isset($_GET["field_rank"]))					{$field_rank=$_GET["field_rank"];}
	elseif (isset($_POST["field_rank"]))		{$field_rank=$_POST["field_rank"];}
if (isset($_GET["field_help"]))					{$field_help=$_GET["field_help"];}
	elseif (isset($_POST["field_help"]))		{$field_help=$_POST["field_help"];}
if (isset($_GET["field_type"]))					{$field_type=$_GET["field_type"];}
	elseif (isset($_POST["field_type"]))		{$field_type=$_POST["field_type"];}
if (isset($_GET["field_options"]))				{$field_options=$_GET["field_options"];}
	elseif (isset($_POST["field_options"]))		{$field_options=$_POST["field_options"];}
if (isset($_GET["field_size"]))					{$field_size=$_GET["field_size"];}
	elseif (isset($_POST["field_size"]))		{$field_size=$_POST["field_size"];}
if (isset($_GET["field_max"]))					{$field_max=$_GET["field_max"];}
	elseif (isset($_POST["field_max"]))			{$field_max=$_POST["field_max"];}
if (isset($_GET["field_default"]))				{$field_default=$_GET["field_default"];}
	elseif (isset($_POST["field_default"]))		{$field_default=$_POST["field_default"];}
if (isset($_GET["field_cost"]))					{$field_cost=$_GET["field_cost"];}
	elseif (isset($_POST["field_cost"]))		{$field_cost=$_POST["field_cost"];}
if (isset($_GET["field_required"]))				{$field_required=$_GET["field_required"];}
	elseif (isset($_POST["field_required"]))	{$field_required=$_POST["field_required"];}
if (isset($_GET["name_position"]))				{$name_position=$_GET["name_position"];}
	elseif (isset($_POST["name_position"]))		{$name_position=$_POST["name_position"];}
if (isset($_GET["multi_position"]))				{$multi_position=$_GET["multi_position"];}
	elseif (isset($_POST["multi_position"]))	{$multi_position=$_POST["multi_position"];}
if (isset($_GET["field_order"]))				{$field_order=$_GET["field_order"];}
	elseif (isset($_POST["field_order"]))		{$field_order=$_POST["field_order"];}
if (isset($_GET["source_list_id"]))				{$source_list_id=$_GET["source_list_id"];}
	elseif (isset($_POST["source_list_id"]))	{$source_list_id=$_POST["source_list_id"];}
if (isset($_GET["copy_option"]))				{$copy_option=$_GET["copy_option"];}
	elseif (isset($_POST["copy_option"]))		{$copy_option=$_POST["copy_option"];}
if (isset($_GET["ConFiRm"]))					{$ConFiRm=$_GET["ConFiRm"];}
	elseif (isset($_POST["ConFiRm"]))			{$ConFiRm=$_POST["ConFiRm"];}
if (isset($_GET["SUBMIT"]))						{$SUBMIT=$_GET["SUBMIT"];}
	elseif (isset($_POST["SUBMIT"]))			{$SUBMIT=$_POST["SUBMIT"];}


if ( (strlen($action) < 2) and ($list_id > 99) )
	{$action = 'MODIFY_CUSTOM_FIELDS';}
if (strlen($action) < 2)
	{$action = 'LIST';}
if (strlen($DB) < 1)
	{$DB=0;}
if ($field_size > 100)
	{$field_size = 100;}
if ( (strlen($field_size) < 1) or ($field_size < 1) )
	{$field_size = 1;}
if ( (strlen($field_max) < 1) or ($field_max < 1) )
	{$field_max = 1;}


if ($non_latin < 1)
	{
	$PHP_AUTH_USER = ereg_replace("[^-_0-9a-zA-Z]","",$PHP_AUTH_USER);
	$PHP_AUTH_PW = ereg_replace("[^-_0-9a-zA-Z]","",$PHP_AUTH_PW);

	$list_id = ereg_replace("[^0-9]","",$list_id);
	$field_id = ereg_replace("[^0-9]","",$field_id);
	$field_rank = ereg_replace("[^0-9]","",$field_rank);
	$field_size = ereg_replace("[^0-9]","",$field_size);
	$field_max = ereg_replace("[^0-9]","",$field_max);
	$field_order = ereg_replace("[^0-9]","",$field_order);
	$source_list_id = ereg_replace("[^0-9]","",$source_list_id);

	$field_required = ereg_replace("[^NY]","",$field_required);

	$field_type = ereg_replace("[^0-9a-zA-Z]","",$field_type);
	$ConFiRm = ereg_replace("[^0-9a-zA-Z]","",$ConFiRm);
	$name_position = ereg_replace("[^0-9a-zA-Z]","",$name_position);
	$multi_position = ereg_replace("[^0-9a-zA-Z]","",$multi_position);

	$field_label = ereg_replace("[^_0-9a-zA-Z]","",$field_label);
	$copy_option = ereg_replace("[^_0-9a-zA-Z]","",$copy_option);

	$field_name = ereg_replace("[^ \.\,-\_0-9a-zA-Z]","",$field_name);
	$field_description = ereg_replace("[^ \.\,-\_0-9a-zA-Z]","",$field_description);
	$field_options = ereg_replace("[^ \.\n\,-\_0-9a-zA-Z]","",$field_options);
	$field_default = ereg_replace("[^ \.\n\,-\_0-9a-zA-Z]","",$field_default);
	}	# end of non_latin
else
	{
	$PHP_AUTH_USER = ereg_replace("'|\"|\\\\|;","",$PHP_AUTH_USER);
	$PHP_AUTH_PW = ereg_replace("'|\"|\\\\|;","",$PHP_AUTH_PW);
	}

$STARTtime = date("U");
$TODAY = date("Y-m-d");
$NOW_TIME = date("Y-m-d H:i:s");

$vicidial_list_fields = '|lead_id|vendor_lead_code|source_id|list_id|gmt_offset_now|called_since_last_reset|phone_code|phone_number|title|first_name|middle_initial|last_name|address1|address2|address3|city|state|province|postal_code|country_code|gender|date_of_birth|alt_phone|email|security_phrase|comments|called_count|last_local_call_time|rank|owner|';

#############################################
##### START SYSTEM_SETTINGS LOOKUP #####
$stmt = "SELECT use_non_latin,custom_fields_enabled FROM system_settings;";
$rslt=mysql_query($stmt, $link);
if ($DB) {echo "$stmt\n";}
$ss_conf_ct = mysql_num_rows($rslt);
if ($ss_conf_ct > 0)
	{
	$row=mysql_fetch_row($rslt);
	$non_latin =						$row[0];
	$SScustom_fields_enabled =			$row[1];
	}
##### END SETTINGS LOOKUP #####
###########################################


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
		$stmt="SELECT full_name,modify_leads,custom_fields_modify,user_level from vicidial_users where user='$PHP_AUTH_USER' and pass='$PHP_AUTH_PW'";
		$rslt=mysql_query($stmt, $link);
		$row=mysql_fetch_row($rslt);
		$LOGfullname =				$row[0];
		$LOGmodify_leads =			$row[1];
		$LOGcustom_fields_modify =	$row[2];
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

<?php
if ($action != "HELP")
	{
?>
<script language="JavaScript" src="calendar_db.js"></script>
<link rel="stylesheet" href="calendar.css">

<script language="Javascript">
function open_help(taskspan,taskhelp) 
	{
	document.getElementById("P_" + taskspan).innerHTML = " &nbsp; <a href=\"javascript:close_help('" + taskspan + "','" + taskhelp + "');\">help-</a><BR> &nbsp; ";
	document.getElementById(taskspan).innerHTML = "<B>" + taskhelp + "</B>";
	document.getElementById(taskspan).style.background = "#FFFF99";
	}
function close_help(taskspan,taskhelp) 
	{
	document.getElementById("P_" + taskspan).innerHTML = "";
	document.getElementById(taskspan).innerHTML = " &nbsp; <a href=\"javascript:open_help('" + taskspan + "','" + taskhelp + "');\">help+</a>";
	document.getElementById(taskspan).style.background = "white";
	}
</script>

<?php
	}
?>

<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=utf-8">
<title>ADMINISTRATION: Lists Custom Fields
<?php 

################################################################################
##### BEGIN help section
if ($action == "HELP")
	{
	?>
	</title>
	</head>
	<body bgcolor=white>
	<center>
	<TABLE WIDTH=98% BGCOLOR=#E6E6E6 cellpadding=2 cellspacing=4><TR><TD ALIGN=LEFT><FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>
	<BR>
	<B>ViciDial Lists Custom Fields Help</B>
	<BR><BR>

	<A NAME="vicidial_lists_fields-field_label">
	<BR>
	 <B>Field Label -</B> This is the database field identifier for this field. This needs to be a unique identifier within the custom fields for this list. Do not use any spaces or punctuation for this field. max 50 characters, minimum of 2 characters. You can also include the default ViciDial fields in a custom field setup, and you will see them in red in the list. These fields will not be added to the custom list database table, the agent interface will instead reference the vicidial_list table directly. The labels that you can use to include the default fieds are - 
	lead_id, vendor_lead_code, source_id, list_id, gmt_offset_now, called_since_last_reset, phone_code, phone_number, title, first_name, middle_initial, last_name, address1, address2, address3, city, state, province, postal_code, country_code, gender, date_of_birth, alt_phone, email, security_phrase, comments, called_count, last_local_call_time, rank, owner
	<BR><BR>

	<A NAME="vicidial_lists_fields-field_name">
	<BR>
	 <B>Field Name -</B> This is the name of the field as it will appear to an agent through their interface. You can use spaces in this field, but no punctuation characters, maximum of 50 characters and minimum of 2 characters.
	<BR><BR>

	<A NAME="vicidial_lists_fields-field_description">
	<BR>
	 <B>Field Description -</B> The description of this field as it will appear in the administration interface. This is an optional field with a maximum of 100 characters.
	<BR><BR>

	<A NAME="vicidial_lists_fields-field_rank">
	<BR>
	 <B>Field Rank -</B> The order in which these fields is displayed to the agent from lowest on top to highest on the bottom.
	<BR><BR>

	<A NAME="vicidial_lists_fields-field_order">
	<BR>
	 <B>Field Order -</B> If more than one field has the same rank, they will be placed on the same line and they will be placed in order by this value from lowest to highest, left to right.
	<BR><BR>

	<A NAME="vicidial_lists_fields-field_help">
	<BR>
	 <B>Field Help -</B> Optional field, if you fill it in, the agent will be able to see this text when they click on a help link next to the field in their agent interface.
	<BR><BR>

	<A NAME="vicidial_lists_fields-field_type">
	<BR>
	 <B>Field Type -</B> This option defines the type of field that will be displayed. TEXT is a standard single-line entry form, AREA is a multi-line text box, SELECT is a single-selection pull-down menu, MULTI is a multiple-select box, RADIO is a list of radio buttons where only one option can be selected, CHECKBOX is a list of checkboxes where multiple options can be selected, DATE is a year month day calendar popup where the agent can select the date and TIME is a time selection box. The default is TEXT. For the SELECT, MULTI, RADIO and CHECKBOX options you must define the option values below in the Field Options box. DISPLAY will display only and not allow for modification by the agent. SCRIPT will also display only, but you are able to use script variables just like in the Scripts feature. SCRIPT fields will also only display the content in the Options, and not the field name like the DISPLAY type does.
	<BR><BR>

	<A NAME="vicidial_lists_fields-field_options">
	<BR>
	 <B>Field Options -</B> For the SELECT, MULTI, RADIO and CHECKBOX field types, you must define the option values in this box. You must put a list of comma separated option label and option text here with each option one its own line. The first value should have no spaces in it, and neither values should have any punctuation. For example - electric_meter, Electric Meter
	<BR><BR>

	<A NAME="vicidial_lists_fields-multi_position">
	<BR>
	 <B>Option Position -</B> For CHECKBOX and RADIO field types only, if set to HORIZONTAL the options will appear on the same line possibly wrapping to the line below if there are many options. If set to VERTICAL there will be only one option per line. Default is HORIZONTAL.
	<BR><BR>

	<A NAME="vicidial_lists_fields-field_size">
	<BR>
	 <B>Field Size -</B> This setting will mean different things depending on what the field type is. For TEXT fields, the size is the number of characters that will show in the field. For AREA fields, the size is the width of the text box in characters. For MULTI fields, this setting defines the number of options to be shown in the multi select list. For SELECT, RADIO, CHECKBOX, DATE and TIME this setting is ignored.
	<BR><BR>

	<A NAME="vicidial_lists_fields-field_max">
	<BR>
	 <B>Field Max -</B> This setting will mean different things depending on what the field type is. For TEXT fields, the size is the maximum number of characters that are allowed in the field. For AREA fields, this field defines the number of rows of text visible in the text box. For MULTI, SELECT, RADIO, CHECKBOX, DATE and TIME this setting is ignored.
	<BR><BR>

	<A NAME="vicidial_lists_fields-field_default">
	<BR>
	 <B>Field Default -</B> This optional field lets you define what value to assign to a field if nothing is loaded into that field. Default is NULL which disables the default function. For DATE field types, the default is always set to today unless a number is put in in which case the date will be that many days plus or minus today. For TIME field types, the default is always set to the current server time unless a number is put in in which case the time will be that many minutes plus or minus current time.
	<BR><BR>

	<A NAME="vicidial_lists_fields-field_cost">
	<BR>
	 <B>Field Cost -</B> This read only field tells you what the cost of this field is in the custom field table for this list. There is no hard limit for the number of custom fields you can have in a list, but the total of the cost of all fields for the list must be below 65000. This typically allows for hundreds of fields, but if you specify several TEXT fields that are hundreds or thousands of characters in length then you may hit this limit quickly. If you need that much text in a field you should choose an AREA type, which are stored differently and do not use as much table space.
	<BR><BR>

	<A NAME="vicidial_lists_fields-field_required">
	<BR>
	 <B>Field Required -</B> If set to Y, this field will force the agent to enter text or select an option for this field. Default is N.
	<BR><BR>

	<A NAME="vicidial_lists_fields-name_position">
	<BR>
	 <B>Field Name Position -</B> If set to LEFT, this field name will appear to the left of the field, if set to TOP the field name will take up the entire line and appear above the field. Default is LEFT.
	<BR><BR>

	<A NAME="vicidial_lists_fields-copy_option">
	<BR>
	 <B>Copy Option -</B> When copying field definitions from one list to another, you have a few options for how the copying process works. APPEND will add the fields that are not present in the destination list, if there are matching field labels those will remained untouched, no custom field data will be deleted or modified using this option. UPDATE will update the common field_label fields in the destination list to the field definitions from the source list. custom field data may be modified or lost using this option. REPLACE will remove all existing custom fields in the destination list and replace them with the custom fields from the source list, all custom field data will be deleted using this option.
	<BR><BR>

	</TD></TR></TABLE>
	</BODY>
	</HTML>
	<?php
	exit;
	}
### END help section





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

if ( ($LOGcustom_fields_modify < 1) or ($LOGuser_level < 8) )
	{
	echo "You are not authorized to view this section\n";
	exit;
	}

if ($SScustom_fields_enabled < 1)
	{
	echo "ERROR: Custom Fields are not active on this system\n";
	exit;
	}


$NWB = " &nbsp; <a href=\"javascript:openNewWindow('$PHP_SELF?action=HELP";
$NWE = "')\"><IMG SRC=\"help.gif\" WIDTH=20 HEIGHT=20 BORDER=0 ALT=\"HELP\" ALIGN=TOP></A>";


if ($DB > 0)
{
echo "$DB,$action,$ip,$user,$copy_option,$field_id,$list_id,$source_list_id,$field_label,$field_name,$field_description,$field_rank,$field_help,$field_type,$field_options,$field_size,$field_max,$field_default,$field_required,$field_cost,$multi_position,$name_position,$field_order";
}





################################################################################
##### BEGIN copy fields to a list form
if ($action == "COPY_FIELDS_FORM")
	{
	##### get lists listing for dynamic pulldown
	$stmt="SELECT list_id,list_name from vicidial_lists order by list_id";
	$rsltx=mysql_query($stmt, $link);
	$lists_to_print = mysql_num_rows($rsltx);
	$lists_list='';
	$o=0;
	while ($lists_to_print > $o)
		{
		$rowx=mysql_fetch_row($rsltx);
		$lists_list .= "<option value=\"$rowx[0]\">$rowx[0] - $rowx[1]</option>\n";
		$o++;
		}

	echo "<TABLE><TR><TD>\n";
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";
	echo "<br>Copy Fields to Another List<form action=$PHP_SELF method=POST>\n";
	echo "<input type=hidden name=DB value=\"$DB\">\n";
	echo "<input type=hidden name=action value=COPY_FIELDS_SUBMIT>\n";
	echo "<center><TABLE width=$section_width cellspacing=3>\n";
	echo "<tr bgcolor=#B6D3FC><td align=right>List ID to Copy Fields From: </td><td align=left><select size=1 name=source_list_id>\n";
	echo "$lists_list";
	echo "</select></td></tr>\n";
	echo "<tr bgcolor=#B6D3FC><td align=right>List ID to Copy Fields to: </td><td align=left><select size=1 name=list_id>\n";
	echo "$lists_list";
	echo "</select></td></tr>\n";
	echo "<tr bgcolor=#B6D3FC><td align=right>Copy Option: </td><td align=left><select size=1 name=copy_option>\n";
	echo "<option selected>APPEND</option>";
	echo "<option>UPDATE</option>";
	echo "<option>REPLACE</option>";
	echo "</select> $NWB#vicidial_lists_fields-copy_option$NWE</td></tr>\n";
	echo "<tr bgcolor=#B6D3FC><td align=center colspan=2><input type=submit name=SUBMIT value=SUBMIT></td></tr>\n";
	echo "</TABLE></center>\n";
	echo "</TD></TR></TABLE>\n";
	}
### END copy fields to a list form




################################################################################
##### BEGIN copy list fields submit
if ( ($action == "COPY_FIELDS_SUBMIT") and ($list_id > 99) and ($source_list_id > 99) and (strlen($copy_option) > 2) )
	{
	if ($list_id=="$source_list_id")
		{echo "ERROR: You cannot copy fields to the same list: $list_id|$source_list_id";}
	else
		{
		$table_exists=0;
		$linkCUSTOM=mysql_connect("$VARDB_server:$VARDB_port", "$VARDB_custom_user","$VARDB_custom_pass");
		if (!$linkCUSTOM) {die("Could not connect: $VARDB_server|$VARDB_port|$VARDB_database|$VARDB_custom_user|$VARDB_custom_pass" . mysql_error());}
		mysql_select_db("$VARDB_database", $linkCUSTOM);

		$stmt="SELECT count(*) from vicidial_lists_fields where list_id='$source_list_id';";
		if ($DB>0) {echo "$stmt";}
		$rslt=mysql_query($stmt, $link);
		$fieldscount_to_print = mysql_num_rows($rslt);
		if ($fieldscount_to_print > 0) 
			{
			$rowx=mysql_fetch_row($rslt);
			$source_field_exists =	$rowx[0];
			}
		
		$stmt="SELECT count(*) from vicidial_lists_fields where list_id='$list_id';";
		if ($DB>0) {echo "$stmt";}
		$rslt=mysql_query($stmt, $link);
		$fieldscount_to_print = mysql_num_rows($rslt);
		if ($fieldscount_to_print > 0) 
			{
			$rowx=mysql_fetch_row($rslt);
			$field_exists =	$rowx[0];
			}
		
		$stmt="SHOW TABLES LIKE \"custom_$list_id\";";
		$rslt=mysql_query($stmt, $link);
		$tablecount_to_print = mysql_num_rows($rslt);
		if ($tablecount_to_print > 0) 
			{$table_exists =	1;}
		if ($DB>0) {echo "$stmt|$tablecount_to_print|$table_exists";}
		
		if ($source_field_exists < 1)
			{echo "ERROR: Source list has no custom fields\n<BR>";}
		else
			{
			##### REPLACE option #####
			if ($copy_option=='REPLACE')
				{
				if ($DB > 0) {echo "Starting REPLACE copy\n<BR>";}
				if ($table_exists > 0)
					{
					$stmt="SELECT field_id,field_label from vicidial_lists_fields where list_id='$list_id' order by field_rank,field_order,field_label;";
					$rslt=mysql_query($stmt, $link);
					$fields_to_print = mysql_num_rows($rslt);
					$fields_list='';
					$o=0;
					while ($fields_to_print > $o) 
						{
						$rowx=mysql_fetch_row($rslt);
						$A_field_id[$o] =			$rowx[0];
						$A_field_label[$o] =		$rowx[1];
						$o++;
						}

					$o=0;
					while ($fields_to_print > $o) 
						{
						### delete field function
						delete_field_function($DB,$link,$linkCUSTOM,$ip,$user,$table_exists,$A_field_id[$o],$list_id,$A_field_label[$o],$A_field_name[$o],$A_field_description[$o],$A_field_rank[$o],$A_field_help[$o],$A_field_type[$o],$A_field_options[$o],$A_field_size[$o],$A_field_max[$o],$A_field_default[$o],$A_field_required[$o],$A_field_cost[$o],$A_multi_position[$o],$A_name_position[$o],$A_field_order[$o],$vicidial_list_fields);

						echo "SUCCESS: Custom Field Deleted - $list_id|$A_field_label[$o]\n<BR>";
						$o++;
						}
					}
				$copy_option='APPEND';
				}
			##### APPEND option #####
			if ($copy_option=='APPEND')
				{
				if ($DB > 0) {echo "Starting APPEND copy\n<BR>";}
				$stmt="SELECT field_id,field_label,field_name,field_description,field_rank,field_help,field_type,field_options,field_size,field_max,field_default,field_cost,field_required,multi_position,name_position,field_order from vicidial_lists_fields where list_id='$source_list_id' order by field_rank,field_order,field_label;";
				$rslt=mysql_query($stmt, $link);
				$fields_to_print = mysql_num_rows($rslt);
				$fields_list='';
				$o=0;
				while ($fields_to_print > $o) 
					{
					$rowx=mysql_fetch_row($rslt);
					$A_field_id[$o] =			$rowx[0];
					$A_field_label[$o] =		$rowx[1];
					$A_field_name[$o] =			$rowx[2];
					$A_field_description[$o] =	$rowx[3];
					$A_field_rank[$o] =			$rowx[4];
					$A_field_help[$o] =			$rowx[5];
					$A_field_type[$o] =			$rowx[6];
					$A_field_options[$o] =		$rowx[7];
					$A_field_size[$o] =			$rowx[8];
					$A_field_max[$o] =			$rowx[9];
					$A_field_default[$o] =		$rowx[10];
					$A_field_cost[$o] =			$rowx[11];
					$A_field_required[$o] =		$rowx[12];
					$A_multi_position[$o] =		$rowx[13];
					$A_name_position[$o] =		$rowx[14];
					$A_field_order[$o] =		$rowx[15];

					$o++;
					$rank_select .= "<option>$o</option>";
					}

				$o=0;
				while ($fields_to_print > $o) 
					{
					$new_field_exists=0;
					if ($table_exists > 0)
						{
						$stmt="SELECT count(*) from vicidial_lists_fields where list_id='$list_id' and field_label='$A_field_label[$o]';";
						if ($DB>0) {echo "$stmt";}
						$rslt=mysql_query($stmt, $link);
						$fieldscount_to_print = mysql_num_rows($rslt);
						if ($fieldscount_to_print > 0) 
							{
							$rowx=mysql_fetch_row($rslt);
							$new_field_exists =	$rowx[0];
							}
						}
					if ($new_field_exists < 1)
						{
						### add field function
						add_field_function($DB,$link,$linkCUSTOM,$ip,$user,$table_exists,$A_field_id[$o],$list_id,$A_field_label[$o],$A_field_name[$o],$A_field_description[$o],$A_field_rank[$o],$A_field_help[$o],$A_field_type[$o],$A_field_options[$o],$A_field_size[$o],$A_field_max[$o],$A_field_default[$o],$A_field_required[$o],$A_field_cost[$o],$A_multi_position[$o],$A_name_position[$o],$A_field_order[$o],$vicidial_list_fields);

						echo "SUCCESS: Custom Field Added - $list_id|$A_field_label[$o]\n<BR>";

						if ($table_exists < 1) {$table_exists=1;}
						}
					$o++;
					}
				}
			##### UPDATE option #####
			if ($copy_option=='UPDATE')
				{
				if ($DB > 0) {echo "Starting UPDATE copy\n<BR>";}
				if ($table_exists < 1)
					{echo "ERROR: Table does not exist custom_$list_id\n<BR>";}
				else
					{
					$stmt="SELECT field_id,field_label,field_name,field_description,field_rank,field_help,field_type,field_options,field_size,field_max,field_default,field_cost,field_required,multi_position,name_position,field_order from vicidial_lists_fields where list_id='$source_list_id' order by field_rank,field_order,field_label;";
					$rslt=mysql_query($stmt, $link);
					$fields_to_print = mysql_num_rows($rslt);
					$fields_list='';
					$o=0;
					while ($fields_to_print > $o) 
						{
						$rowx=mysql_fetch_row($rslt);
						$A_field_id[$o] =			$rowx[0];
						$A_field_label[$o] =		$rowx[1];
						$A_field_name[$o] =			$rowx[2];
						$A_field_description[$o] =	$rowx[3];
						$A_field_rank[$o] =			$rowx[4];
						$A_field_help[$o] =			$rowx[5];
						$A_field_type[$o] =			$rowx[6];
						$A_field_options[$o] =		$rowx[7];
						$A_field_size[$o] =			$rowx[8];
						$A_field_max[$o] =			$rowx[9];
						$A_field_default[$o] =		$rowx[10];
						$A_field_cost[$o] =			$rowx[11];
						$A_field_required[$o] =		$rowx[12];
						$A_multi_position[$o] =		$rowx[13];
						$A_name_position[$o] =		$rowx[14];
						$A_field_order[$o] =		$rowx[15];
						$o++;
						}

					$o=0;
					while ($fields_to_print > $o) 
						{
						$stmt="SELECT field_id from vicidial_lists_fields where list_id='$list_id' and field_label='$A_field_label[$o]';";
						if ($DB>0) {echo "$stmt";}
						$rslt=mysql_query($stmt, $link);
						$fieldscount_to_print = mysql_num_rows($rslt);
						if ($fieldscount_to_print > 0) 
							{
							$rowx=mysql_fetch_row($rslt);
							$current_field_id =	$rowx[0];

							### modify field function
							modify_field_function($DB,$link,$linkCUSTOM,$ip,$user,$table_exists,$current_field_id,$list_id,$A_field_label[$o],$A_field_name[$o],$A_field_description[$o],$A_field_rank[$o],$A_field_help[$o],$A_field_type[$o],$A_field_options[$o],$A_field_size[$o],$A_field_max[$o],$A_field_default[$o],$A_field_required[$o],$A_field_cost[$o],$A_multi_position[$o],$A_name_position[$o],$A_field_order[$o],$vicidial_list_fields);

							echo "SUCCESS: Custom Field Modified - $list_id|$A_field_label[$o]\n<BR>";
							}
						$o++;
						}
					}
				}
			}

		$action = "MODIFY_CUSTOM_FIELDS";
		}
	}
### END copy list fields submit





################################################################################
##### BEGIN delete custom field confirmation
if ( ($action == "DELETE_CUSTOM_FIELD_CONFIRMATION") and ($list_id > 99) and ($field_id > 0) and (strlen($field_label) > 0) )
	{
	$stmt="SELECT count(*) from vicidial_lists_fields where list_id='$list_id' and field_label='$field_label';";
	if ($DB>0) {echo "$stmt";}
	$rslt=mysql_query($stmt, $link);
	$fieldscount_to_print = mysql_num_rows($rslt);
	if ($fieldscount_to_print > 0) 
		{
		$rowx=mysql_fetch_row($rslt);
		$field_exists =	$rowx[0];
		}
	
	$stmt="SHOW TABLES LIKE \"custom_$list_id\";";
	$rslt=mysql_query($stmt, $link);
	$tablecount_to_print = mysql_num_rows($rslt);
	if ($tablecount_to_print > 0) 
		{$table_exists =	1;}
	if ($DB>0) {echo "$stmt|$tablecount_to_print|$table_exists";}
	
	if ($field_exists < 1)
		{echo "ERROR: Field does not exist\n<BR>";}
	else
		{
		if ($table_exists < 1)
			{echo "ERROR: Table does not exist custom_$list_id\n<BR>";}
		else
			{
			echo "<BR><BR><B><a href=\"$PHP_SELF?action=DELETE_CUSTOM_FIELD&list_id=$list_id&field_id=$field_id&field_label=$field_label&ConFiRm=YES&DB=$DB\">CLICK HERE TO CONFIRM DELETION OF THIS CUSTOM FIELD: $field_label - $field_id - $list_id</a></B><BR><BR>";
			}
		}

	$action = "MODIFY_CUSTOM_FIELDS";
	}
### END delete custom field confirmation




################################################################################
##### BEGIN delete custom field
if ( ($action == "DELETE_CUSTOM_FIELD") and ($list_id > 99) and ($field_id > 0) and (strlen($field_label) > 0) and ($ConFiRm=='YES') )
	{
	$table_exists=0;
	$linkCUSTOM=mysql_connect("$VARDB_server:$VARDB_port", "$VARDB_custom_user","$VARDB_custom_pass");
	if (!$linkCUSTOM) {die("Could not connect: $VARDB_server|$VARDB_port|$VARDB_database|$VARDB_custom_user|$VARDB_custom_pass" . mysql_error());}
	mysql_select_db("$VARDB_database", $linkCUSTOM);

	$stmt="SELECT count(*) from vicidial_lists_fields where list_id='$list_id' and field_label='$field_label';";
	if ($DB>0) {echo "$stmt";}
	$rslt=mysql_query($stmt, $link);
	$fieldscount_to_print = mysql_num_rows($rslt);
	if ($fieldscount_to_print > 0) 
		{
		$rowx=mysql_fetch_row($rslt);
		$field_exists =	$rowx[0];
		}
	
	$stmt="SHOW TABLES LIKE \"custom_$list_id\";";
	$rslt=mysql_query($stmt, $link);
	$tablecount_to_print = mysql_num_rows($rslt);
	if ($tablecount_to_print > 0) 
		{$table_exists =	1;}
	if ($DB>0) {echo "$stmt|$tablecount_to_print|$table_exists";}
	
	if ($field_exists < 1)
		{echo "ERROR: Field does not exist\n<BR>";}
	else
		{
		if ($table_exists < 1)
			{echo "ERROR: Table does not exist custom_$list_id\n<BR>";}
		else
			{
			### delete field function
			delete_field_function($DB,$link,$linkCUSTOM,$ip,$user,$table_exists,$field_id,$list_id,$field_label,$field_name,$field_description,$field_rank,$field_help,$field_type,$field_options,$field_size,$field_max,$field_default,$field_required,$field_cost,$multi_position,$name_position,$field_order,$vicidial_list_fields);

			echo "SUCCESS: Custom Field Deleted - $list_id|$field_label\n<BR>";
			}
		}

	$action = "MODIFY_CUSTOM_FIELDS";
	}
### END delete custom field




################################################################################
##### BEGIN add new custom field
if ( ($action == "ADD_CUSTOM_FIELD") and ($list_id > 99) )
	{
	$stmt="SELECT count(*) from vicidial_lists_fields where list_id='$list_id' and field_label='$field_label';";
	if ($DB>0) {echo "$stmt";}
	$rslt=mysql_query($stmt, $link);
	$fieldscount_to_print = mysql_num_rows($rslt);
	if ($fieldscount_to_print > 0) 
		{
		$rowx=mysql_fetch_row($rslt);
		$field_exists =	$rowx[0];
		}
	
	if ( (strlen($field_label)<1) or (strlen($field_name)<2) or (strlen($field_size)<1) )
		{echo "ERROR: You must enter a field label, field name and field size - $list_id|$field_label|$field_name|$field_size\n<BR>";}
	else
		{
		$TEST_valid_options=0;
		if ( ($field_type=='SELECT') or ($field_type=='MULTI') or ($field_type=='RADIO') or ($field_type=='CHECKBOX') )
			{
			$TESTfield_options_array = explode("\n",$field_options);
			$TESTfield_options_count = count($TESTfield_options_array);
			$te=0;
			while ($te < $TESTfield_options_count)
				{
				if (preg_match("/,/",$TESTfield_options_array[$te]))
					{
					$TESTfield_options_value_array = explode(",",$TESTfield_options_array[$te]);
					if ( (strlen($TESTfield_options_value_array[0]) > 0) and (strlen($TESTfield_options_value_array[1]) > 0) )
						{$TEST_valid_options++;}
					}
				$te++;
				}
			$field_options_ENUM = preg_replace("/.$/",'',$field_options_ENUM);
			}

		if ( ( ($field_type=='SELECT') or ($field_type=='MULTI') or ($field_type=='RADIO') or ($field_type=='CHECKBOX') ) and ( (!preg_match("/,/",$field_options)) or (!preg_match("/\n/",$field_options)) or (strlen($field_options)<6) or ($TEST_valid_options < 1) ) )
			{echo "ERROR: You must enter field options when adding a SELECT, MULTI, RADIO or CHECKBOX field type  - $list_id|$field_label|$field_type|$field_options\n<BR>";}
		else
			{
			if ($field_exists > 0)
				{echo "ERROR: Field already exists for this list - $list_id|$field_label\n<BR>";}
			else
				{
				$table_exists=0;
				$linkCUSTOM=mysql_connect("$VARDB_server:$VARDB_port", "$VARDB_custom_user","$VARDB_custom_pass");
				if (!$linkCUSTOM) {die("Could not connect: $VARDB_server|$VARDB_port|$VARDB_database|$VARDB_custom_user|$VARDB_custom_pass" . mysql_error());}
				mysql_select_db("$VARDB_database", $linkCUSTOM);

				$stmt="SHOW TABLES LIKE \"custom_$list_id\";";
				$rslt=mysql_query($stmt, $link);
				$tablecount_to_print = mysql_num_rows($rslt);
				if ($tablecount_to_print > 0) 
					{$table_exists =	1;}
				if ($DB>0) {echo "$stmt|$tablecount_to_print|$table_exists";}
			
				### add field function
				add_field_function($DB,$link,$linkCUSTOM,$ip,$user,$table_exists,$field_id,$list_id,$field_label,$field_name,$field_description,$field_rank,$field_help,$field_type,$field_options,$field_size,$field_max,$field_default,$field_required,$field_cost,$multi_position,$name_position,$field_order,$vicidial_list_fields);

				echo "SUCCESS: Custom Field Added - $list_id|$field_label\n<BR>";
				}
			}
		}

	$action = "MODIFY_CUSTOM_FIELDS";
	}
### END add new custom field




################################################################################
##### BEGIN modify custom field submission
if ( ($action == "MODIFY_CUSTOM_FIELD_SUBMIT") and ($list_id > 99) and ($field_id > 0) )
	{
	### connect to your vtiger database
	$linkCUSTOM=mysql_connect("$VARDB_server:$VARDB_port", "$VARDB_custom_user","$VARDB_custom_pass");
	if (!$linkCUSTOM) {die("Could not connect: $VARDB_server|$VARDB_port|$VARDB_database|$VARDB_custom_user|$VARDB_custom_pass" . mysql_error());}
	mysql_select_db("$VARDB_database", $linkCUSTOM);

	$stmt="SELECT count(*) from vicidial_lists_fields where list_id='$list_id' and field_id='$field_id';";
	if ($DB>0) {echo "$stmt";}
	$rslt=mysql_query($stmt, $link);
	$fieldscount_to_print = mysql_num_rows($rslt);
	if ($fieldscount_to_print > 0) 
		{
		$rowx=mysql_fetch_row($rslt);
		$field_exists =	$rowx[0];
		}
	
	$stmt="SHOW TABLES LIKE \"custom_$list_id\";";
	$rslt=mysql_query($stmt, $link);
	$tablecount_to_print = mysql_num_rows($rslt);
	if ($tablecount_to_print > 0) 
		{$table_exists =	1;}
	if ($DB>0) {echo "$stmt|$tablecount_to_print|$table_exists";}

	if ($field_exists < 1)
		{echo "ERROR: Field does not exist\n<BR>";}
	else
		{
		if ($table_exists < 1)
			{echo "ERROR: Table does not exist\n<BR>";}
		else
			{
			$TEST_valid_options=0;
			if ( ($field_type=='SELECT') or ($field_type=='MULTI') or ($field_type=='RADIO') or ($field_type=='CHECKBOX') )
				{
				$TESTfield_options_array = explode("\n",$field_options);
				$TESTfield_options_count = count($TESTfield_options_array);
				$te=0;
				while ($te < $TESTfield_options_count)
					{
					if (preg_match("/,/",$TESTfield_options_array[$te]))
						{
						$TESTfield_options_value_array = explode(",",$TESTfield_options_array[$te]);
						if ( (strlen($TESTfield_options_value_array[0]) > 0) and (strlen($TESTfield_options_value_array[1]) > 0) )
							{$TEST_valid_options++;}
						}
					$te++;
					}
				$field_options_ENUM = preg_replace("/.$/",'',$field_options_ENUM);
				}

			if ( ( ($field_type=='SELECT') or ($field_type=='MULTI') or ($field_type=='RADIO') or ($field_type=='CHECKBOX') ) and ( (!preg_match("/,/",$field_options)) or (!preg_match("/\n/",$field_options)) or (strlen($field_options)<6) or ($TEST_valid_options < 1) ) )
				{echo "ERROR: You must enter field options when updating a SELECT, MULTI, RADIO or CHECKBOX field type  - $list_id|$field_label|$field_type|$field_options\n<BR>";}
			else
				{
				### modify field function
				modify_field_function($DB,$link,$linkCUSTOM,$ip,$user,$table_exists,$field_id,$list_id,$field_label,$field_name,$field_description,$field_rank,$field_help,$field_type,$field_options,$field_size,$field_max,$field_default,$field_required,$field_cost,$multi_position,$name_position,$field_order,$vicidial_list_fields);

				echo "SUCCESS: Custom Field Modified - $list_id|$field_label\n<BR>";
				}
			}
		}

	$action = "MODIFY_CUSTOM_FIELDS";
	}
### END modify custom field submission





################################################################################
##### BEGIN modify custom fields for list
if ( ($action == "MODIFY_CUSTOM_FIELDS") and ($list_id > 99) )
	{
	echo "</TITLE></HEAD><BODY BGCOLOR=white>\n";
	echo "<TABLE><TR><TD>\n";

	$custom_records_count=0;
	$stmt="SHOW TABLES LIKE \"custom_$list_id\";";
	$rslt=mysql_query($stmt, $link);
	$tablecount_to_print = mysql_num_rows($rslt);
	if ($tablecount_to_print > 0) 
		{$table_exists =	1;}
	if ($DB>0) {echo "$stmt|$tablecount_to_print|$table_exists";}
	
	if ($table_exists > 0)
		{
		$stmt="SELECT count(*) from custom_$list_id;";
		if ($DB>0) {echo "$stmt";}
		$rslt=mysql_query($stmt, $link);
		$fieldscount_to_print = mysql_num_rows($rslt);
		if ($fieldscount_to_print > 0) 
			{
			$rowx=mysql_fetch_row($rslt);
			$custom_records_count =	$rowx[0];
			}
		}

	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";
	echo "<br>Modify Custom Fields: List ID $list_id   &nbsp; &nbsp; &nbsp; &nbsp; ";
	echo "Records in this custom table: $custom_records_count<br>\n";
	echo "<center><TABLE width=$section_width cellspacing=3>\n";

	$stmt="SELECT field_id,field_label,field_name,field_description,field_rank,field_help,field_type,field_options,field_size,field_max,field_default,field_cost,field_required,multi_position,name_position,field_order from vicidial_lists_fields where list_id='$list_id' order by field_rank,field_order,field_label;";
	$rslt=mysql_query($stmt, $link);
	$fields_to_print = mysql_num_rows($rslt);
	$fields_list='';
	$o=0;
	while ($fields_to_print > $o) 
		{
		$rowx=mysql_fetch_row($rslt);
		$A_field_id[$o] =			$rowx[0];
		$A_field_label[$o] =		$rowx[1];
		$A_field_name[$o] =			$rowx[2];
		$A_field_description[$o] =	$rowx[3];
		$A_field_rank[$o] =			$rowx[4];
		$A_field_help[$o] =			$rowx[5];
		$A_field_type[$o] =			$rowx[6];
		$A_field_options[$o] =		$rowx[7];
		$A_field_size[$o] =			$rowx[8];
		$A_field_max[$o] =			$rowx[9];
		$A_field_default[$o] =		$rowx[10];
		$A_field_cost[$o] =			$rowx[11];
		$A_field_required[$o] =		$rowx[12];
		$A_multi_position[$o] =		$rowx[13];
		$A_name_position[$o] =		$rowx[14];
		$A_field_order[$o] =		$rowx[15];

		$o++;
		$rank_select .= "<option>$o</option>";
		}
	$o++;
	$rank_select .= "<option>$o</option>";
	$last_rank = $o;

	### SUMMARY OF FIELDS ###
	echo "<br>SUMMARY OF FIELDS:\n";
	echo "<center><TABLE cellspacing=0 cellpadding=1>\n";
	echo "<TR BGCOLOR=BLACK>";
	echo "<TD align=right><B><FONT FACE=\"Arial,Helvetica\" size=2 color=white>RANK &nbsp; &nbsp; </B></TD>";
	echo "<TD align=right><B><FONT FACE=\"Arial,Helvetica\" size=2 color=white>LABEL &nbsp; &nbsp; </B></TD>";
	echo "<TD align=right><B><FONT FACE=\"Arial,Helvetica\" size=2 color=white>NAME &nbsp; &nbsp; </B></TD>";
	echo "<TD align=right><B><FONT FACE=\"Arial,Helvetica\" size=2 color=white>TYPE &nbsp; &nbsp; </B></TD>";
	echo "<TD align=right><B><FONT FACE=\"Arial,Helvetica\" size=2 color=white>COST &nbsp; &nbsp; </B></TD>\n";
	echo "</TR>\n";

	$o=0;
	while ($fields_to_print > $o) 
		{
		$LcolorB='';   $LcolorE='';
		if (preg_match("/\|$A_field_label[$o]\|/",$vicidial_list_fields))
			{
			$LcolorB='<font color=red>';
			$LcolorE='</font>';
			}
		if (eregi("1$|3$|5$|7$|9$", $o))
			{$bgcolor='bgcolor="#B9CBFD"';} 
		else
			{$bgcolor='bgcolor="#9BB9FB"';}
		echo "<tr $bgcolor align=right><td><font size=1>$A_field_rank[$o] - $A_field_order[$o] &nbsp; &nbsp; </td>";
		echo "<td align=right><font size=2> <a href=\"#ANCHOR_$A_field_label[$o]\">$LcolorB$A_field_label[$o]$LcolorE</a> &nbsp; &nbsp; </td>";
		echo "<td align=right><font size=2> $A_field_name[$o] &nbsp; &nbsp; </td>";
		echo "<td align=right><font size=2> $A_field_type[$o] &nbsp; &nbsp; </td>";
		echo "<td align=right><font size=2> $A_field_cost[$o] &nbsp; &nbsp; </td></tr>\n";

		$total_cost = ($total_cost + $A_field_cost[$o]);
		$o++;
		}

	if ($fields_to_print < 1) 
		{echo "<tr bgcolor=white align=center><td colspan=5><font size=1>There are no custom fields for this list</td></tr>";}
	else
		{
		echo "<tr bgcolor=white align=right><td><font size=2>TOTALS: </td>";
		echo "<td align=right><font size=2> $o &nbsp; &nbsp; </td>";
		echo "<td align=right><font size=2> &nbsp; &nbsp; </td>";
		echo "<td align=right><font size=2> &nbsp; &nbsp; </td>";
		echo "<td align=right><font size=2> $total_cost &nbsp; &nbsp; </td></tr>\n";
		}
	echo "</table></center><BR><BR>\n";


	### EXAMPLE OF CUSTOM FORM ###
	echo "<form action=$PHP_SELF method=POST name=form_custom_$list_id id=form_custom_$list_id>\n";
	echo "<br>EXAMPLE OF CUSTOM FORM:\n";
	echo "<center><TABLE cellspacing=2 cellpadding=2>\n";
	if ($fields_to_print < 1) 
		{echo "<tr bgcolor=white align=center><td colspan=4><font size=1>There are no custom fields for this list</td></tr>";}

	$o=0;
	$last_field_rank=0;
	while ($fields_to_print > $o) 
		{
		if ($last_field_rank=="$A_field_rank[$o]")
			{echo " &nbsp; &nbsp; &nbsp; &nbsp; ";}
		else
			{
			echo "</td></tr>\n";
			echo "<tr bgcolor=white><td align=";
			if ($A_name_position[$o]=='TOP') 
				{echo "left colspan=2";}
			else
				{echo "right";}
			echo "><font size=2>";
			}
		echo "<a href=\"#ANCHOR_$A_field_label[$o]\"><B>$A_field_name[$o]</B></a>";
		if ($A_name_position[$o]=='TOP') 
			{
			$helpHTML = "<a href=\"javascript:open_help('HELP_$A_field_label[$o]','$A_field_help[$o]');\">help+</a>";
			if (strlen($A_field_help[$o])<1)
				{$helpHTML = '';}
			echo " &nbsp; <span style=\"position:static;\" id=P_HELP_$A_field_label[$o]></span><span style=\"position:static;background:white;\" id=HELP_$A_field_label[$o]> &nbsp; $helpHTML</span><BR>";
			}
		else
			{
			if ($last_field_rank=="$A_field_rank[$o]")
				{echo " &nbsp;";}
			else
				{echo "</td><td align=left><font size=2>";}
			}
		$field_HTML='';

		if ($A_field_type[$o]=='SELECT')
			{
			$field_HTML .= "<select size=1 name=$A_field_label[$o] id=$A_field_label[$o]>\n";
			}
		if ($A_field_type[$o]=='MULTI')
			{
			$field_HTML .= "<select MULTIPLE size=$A_field_size[$o] name=$A_field_label[$o] id=$A_field_label[$o]>\n";
			}
		if ( ($A_field_type[$o]=='SELECT') or ($A_field_type[$o]=='MULTI') or ($A_field_type[$o]=='RADIO') or ($A_field_type[$o]=='CHECKBOX') )
			{
			$field_options_array = explode("\n",$A_field_options[$o]);
			$field_options_count = count($field_options_array);
			$te=0;
			while ($te < $field_options_count)
				{
				if (preg_match("/,/",$field_options_array[$te]))
					{
					$field_selected='';
					$field_options_value_array = explode(",",$field_options_array[$te]);
					if ( ($A_field_type[$o]=='SELECT') or ($A_field_type[$o]=='MULTI') )
						{
						if ($A_field_default[$o] == "$field_options_value_array[0]") {$field_selected = 'SELECTED';}
						$field_HTML .= "<option value=\"$field_options_value_array[0]\" $field_selected>$field_options_value_array[1]</option>\n";
						}
					if ( ($A_field_type[$o]=='RADIO') or ($A_field_type[$o]=='CHECKBOX') )
						{
						if ($A_multi_position[$o]=='VERTICAL') 
							{$field_HTML .= " &nbsp; ";}
						if ($A_field_default[$o] == "$field_options_value_array[0]") {$field_selected = 'CHECKED';}
						$field_HTML .= "<input type=$A_field_type[$o] name=$A_field_label[$o][] id=$A_field_label[$o][] value=\"$field_options_value_array[0]\" $field_selected> $field_options_value_array[1]\n";
						if ($A_multi_position[$o]=='VERTICAL') 
							{$field_HTML .= "<BR>\n";}
						}
					}
				$te++;
				}
			}
		if ( ($A_field_type[$o]=='SELECT') or ($A_field_type[$o]=='MULTI') )
			{
			$field_HTML .= "</select>\n";
			}
		if ($A_field_type[$o]=='TEXT') 
			{
			if ($A_field_default[$o]=='NULL') {$A_field_default[$o]='';}
			$field_HTML .= "<input type=text size=$A_field_size[$o] maxlength=$A_field_max[$o] name=$A_field_label[$o] id=$A_field_label[$o] value=\"$A_field_default[$o]\">\n";
			}
		if ($A_field_type[$o]=='AREA') 
			{
			$field_HTML .= "<textarea name=$A_field_label[$o] id=$A_field_label[$o] ROWS=$A_field_max[$o] COLS=$A_field_size[$o]></textarea>";
			}
		if ($A_field_type[$o]=='DISPLAY')
			{
			if ($A_field_default[$o]=='NULL') {$A_field_default[$o]='';}
			$field_HTML .= "$A_field_default[$o]\n";
			}
		if ($A_field_type[$o]=='SCRIPT')
			{
			if ($A_field_default[$o]=='NULL') {$A_field_default[$o]='';}
			$field_HTML .= "$A_field_options[$o]\n";
			}
		if ($A_field_type[$o]=='DATE') 
			{
			if ( (strlen($A_field_default[$o])<1) or ($A_field_default[$o]=='NULL') ) {$A_field_default[$o]=0;}
			$day_diff = $A_field_default[$o];
			$default_date = date("Y-m-d", mktime(date("H"),date("i"),date("s"),date("m"),date("d")+$day_diff,date("Y")));

			$field_HTML .= "<input type=text size=11 maxlength=10 name=$A_field_label[$o] id=$A_field_label[$o] value=\"$default_date\">\n";
			$field_HTML .= "<script language=\"JavaScript\">\n";
			$field_HTML .= "var o_cal = new tcal ({\n";
			$field_HTML .= "	'formname': 'form_custom_$list_id',\n";
			$field_HTML .= "	'controlname': '$A_field_label[$o]'});\n";
			$field_HTML .= "o_cal.a_tpl.yearscroll = false;\n";
			$field_HTML .= "</script>\n";
			}
		if ($A_field_type[$o]=='TIME') 
			{
			$minute_diff = $A_field_default[$o];
			$default_time = date("H:i:s", mktime(date("H"),date("i")+$minute_diff,date("s"),date("m"),date("d"),date("Y")));
			$default_hour = date("H", mktime(date("H"),date("i")+$minute_diff,date("s"),date("m"),date("d"),date("Y")));
			$default_minute = date("i", mktime(date("H"),date("i")+$minute_diff,date("s"),date("m"),date("d"),date("Y")));
			$field_HTML .= "<input type=hidden name=$A_field_label[$o] id=$A_field_label[$o] value=\"$default_time\">";
			$field_HTML .= "<SELECT name=HOUR_$A_field_label[$o] id=HOUR_$A_field_label[$o]>";
			$field_HTML .= "<option>00</option>";
			$field_HTML .= "<option>01</option>";
			$field_HTML .= "<option>02</option>";
			$field_HTML .= "<option>03</option>";
			$field_HTML .= "<option>04</option>";
			$field_HTML .= "<option>05</option>";
			$field_HTML .= "<option>06</option>";
			$field_HTML .= "<option>07</option>";
			$field_HTML .= "<option>08</option>";
			$field_HTML .= "<option>09</option>";
			$field_HTML .= "<option>10</option>";
			$field_HTML .= "<option>11</option>";
			$field_HTML .= "<option>12</option>";
			$field_HTML .= "<option>13</option>";
			$field_HTML .= "<option>14</option>";
			$field_HTML .= "<option>15</option>";
			$field_HTML .= "<option>16</option>";
			$field_HTML .= "<option>17</option>";
			$field_HTML .= "<option>18</option>";
			$field_HTML .= "<option>19</option>";
			$field_HTML .= "<option>20</option>";
			$field_HTML .= "<option>21</option>";
			$field_HTML .= "<option>22</option>";
			$field_HTML .= "<option>23</option>";
			$field_HTML .= "<OPTION value=\"$default_hour\" selected>$default_hour</OPTION>";
			$field_HTML .= "</SELECT>";
			$field_HTML .= "<SELECT name=MINUTE_$A_field_label[$o] id=MINUTE_$A_field_label[$o]>";
			$field_HTML .= "<option>00</option>";
			$field_HTML .= "<option>05</option>";
			$field_HTML .= "<option>10</option>";
			$field_HTML .= "<option>15</option>";
			$field_HTML .= "<option>20</option>";
			$field_HTML .= "<option>25</option>";
			$field_HTML .= "<option>30</option>";
			$field_HTML .= "<option>35</option>";
			$field_HTML .= "<option>40</option>";
			$field_HTML .= "<option>45</option>";
			$field_HTML .= "<option>50</option>";
			$field_HTML .= "<option>55</option>";
			$field_HTML .= "<OPTION value=\"$default_minute\" selected>$default_minute</OPTION>";
			$field_HTML .= "</SELECT>";
			}

		if ($A_name_position[$o]=='LEFT') 
			{
			$helpHTML = "<a href=\"javascript:open_help('HELP_$A_field_label[$o]','$A_field_help[$o]');\">help+</a>";
			if (strlen($A_field_help[$o])<1)
				{$helpHTML = '';}
			echo " $field_HTML <span style=\"position:static;\" id=P_HELP_$A_field_label[$o]></span><span style=\"position:static;background:white;\" id=HELP_$A_field_label[$o]> &nbsp; $helpHTML</span>";
			}
		else
			{
			echo " $field_HTML\n";
			}

		$last_field_rank=$A_field_rank[$o];
		$o++;
		}
	echo "</td></tr></table></form></center><BR><BR>\n";


	### MODIFY FIELDS ###
	echo "<br>MODIFY EXISTING FIELDS:\n";
	$o=0;
	while ($fields_to_print > $o) 
		{
		$LcolorB='';   $LcolorE='';
		if (preg_match("/\|$A_field_label[$o]\|/",$vicidial_list_fields))
			{
			$LcolorB='<font color=red>';
			$LcolorE='</font>';
			}
		if (eregi("1$|3$|5$|7$|9$", $o))
			{$bgcolor='bgcolor="#B9CBFD"';} 
		else
			{$bgcolor='bgcolor="#9BB9FB"';}
		echo "<form action=$PHP_SELF method=POST>\n";
		echo "<input type=hidden name=action value=MODIFY_CUSTOM_FIELD_SUBMIT>\n";
		echo "<input type=hidden name=list_id value=$list_id>\n";
		echo "<input type=hidden name=DB value=$DB>\n";
		echo "<input type=hidden name=field_id value=\"$A_field_id[$o]\">\n";
		echo "<input type=hidden name=field_label value=\"$A_field_label[$o]\">\n";
		echo "<a name=\"ANCHOR_$A_field_label[$o]\">\n";
		echo "<center><TABLE width=$section_width cellspacing=3 cellpadding=1>\n";
		echo "<tr $bgcolor><td align=right>Field Label $A_field_rank[$o]: </td><td align=left> $LcolorB<B>$A_field_label[$o]</B>$LcolorE $NWB#vicidial_lists_fields-field_label$NWE </td></tr>\n";
		echo "<tr $bgcolor><td align=right>Field Rank $A_field_rank[$o]: </td><td align=left><select size=1 name=field_rank>\n";
		echo "$rank_select\n";
		echo "<option selected>$A_field_rank[$o]</option>\n";
		echo "</select> &nbsp; $NWB#vicidial_lists_fields-field_rank$NWE \n";
		echo " &nbsp; &nbsp; &nbsp; &nbsp; Field Order: <select size=1 name=field_order>\n";
		echo "<option>1</option>\n";
		echo "<option>2</option>\n";
		echo "<option>3</option>\n";
		echo "<option>4</option>\n";
		echo "<option>5</option>\n";
		echo "<option selected>$A_field_order[$o]</option>\n";
		echo "</select> &nbsp; $NWB#vicidial_lists_fields-field_order$NWE </td></tr>\n";
		echo "<tr $bgcolor><td align=right>Field Name $A_field_rank[$o]: </td><td align=left><textarea name=field_name rows=2 cols=60>$A_field_name[$o]</textarea> $NWB#vicidial_lists_fields-field_name$NWE </td></tr>\n";
		echo "<tr $bgcolor><td align=right>Field Name Position $A_field_rank[$o]: </td><td align=left><select size=1 name=name_position>\n";
		echo "<option value=\"LEFT\">LEFT</option>\n";
		echo "<option value=\"TOP\">TOP</option>\n";
		echo "<option selected>$A_name_position[$o]</option>\n";
		echo "</select>  $NWB#vicidial_lists_fields-name_position$NWE </td></tr>\n";
		echo "<tr $bgcolor><td align=right>Field Description $A_field_rank[$o]: </td><td align=left><input type=text name=field_description size=70 maxlength=100 value=\"$A_field_description[$o]\"> $NWB#vicidial_lists_fields-field_description$NWE </td></tr>\n";
		echo "<tr $bgcolor><td align=right>Field Help $A_field_rank[$o]: </td><td align=left><textarea name=field_help rows=2 cols=60>$A_field_help[$o]</textarea> $NWB#vicidial_lists_fields-field_help$NWE </td></tr>\n";
		echo "<tr $bgcolor><td align=right>Field Type $A_field_rank[$o]: </td><td align=left><select size=1 name=field_type>\n";
		echo "<option>TEXT</option>\n";
		echo "<option>AREA</option>\n";
		echo "<option>SELECT</option>\n";
		echo "<option>MULTI</option>\n";
		echo "<option>RADIO</option>\n";
		echo "<option>CHECKBOX</option>\n";
		echo "<option>DATE</option>\n";
		echo "<option>TIME</option>\n";
		echo "<option>DISPLAY</option>\n";
		echo "<option>SCRIPT</option>\n";
		echo "<option selected>$A_field_type[$o]</option>\n";
		echo "</select>  $NWB#vicidial_lists_fields-field_type$NWE </td></tr>\n";
		echo "<tr $bgcolor><td align=right>Field Options $A_field_rank[$o]: </td><td align=left><textarea name=field_options ROWS=5 COLS=60>$A_field_options[$o]</textarea>  $NWB#vicidial_lists_fields-field_options$NWE </td></tr>\n";
		echo "<tr $bgcolor><td align=right>Option Position $A_field_rank[$o]: </td><td align=left><select size=1 name=multi_position>\n";
		echo "<option value=\"HORIZONTAL\">HORIZONTAL</option>\n";
		echo "<option value=\"VERTICAL\">VERTICAL</option>\n";
		echo "<option selected>$A_multi_position[$o]</option>\n";
		echo "</select>  $NWB#vicidial_lists_fields-multi_position$NWE </td></tr>\n";
		echo "<tr $bgcolor><td align=right>Field Size $A_field_rank[$o]: </td><td align=left><input type=text name=field_size size=5 maxlength=3 value=\"$A_field_size[$o]\">  $NWB#vicidial_lists_fields-field_size$NWE </td></tr>\n";
		echo "<tr $bgcolor><td align=right>Field Max $A_field_rank[$o]: </td><td align=left><input type=text name=field_max size=5 maxlength=3 value=\"$A_field_max[$o]\">  $NWB#vicidial_lists_fields-field_max$NWE </td></tr>\n";
		echo "<tr $bgcolor><td align=right>Field Default $A_field_rank[$o]: </td><td align=left><input type=text name=field_default size=50 maxlength=255 value=\"$A_field_default[$o]\">  $NWB#vicidial_lists_fields-field_default$NWE </td></tr>\n";
		echo "<tr $bgcolor><td align=right>Field Required $A_field_rank[$o]: </td><td align=left><select size=1 name=field_required>\n";
		echo "<option value=\"Y\">YES</option>\n";
		echo "<option value=\"N\">NO</option>\n";
		echo "<option selected>$A_field_required[$o]</option>\n";
		echo "</select>  $NWB#vicidial_lists_fields-field_required$NWE </td></tr>\n";
		echo "<tr $bgcolor><td align=center colspan=2><input type=submit name=submit value=\"SUBMIT\"> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;\n";
		echo "<B><a href=\"$PHP_SELF?action=DELETE_CUSTOM_FIELD_CONFIRMATION&list_id=$list_id&field_id=$A_field_id[$o]&field_label=$A_field_label[$o]&DB=$DB\">DELETE THIS CUSTOM FIELD</a></B>";
		echo "</td></tr>\n";
		echo "</table></center></form><BR><BR>\n";

		$o++;
		}

	$bgcolor = ' bgcolor=#BDFFBD';

	echo "<form action=$PHP_SELF method=POST>\n";
	echo "<center><TABLE width=$section_width cellspacing=3 cellpadding=1>\n";
	echo "<tr bgcolor=white><td align=center colspan=2>ADD A NEW CUSTOM FIELD FOR THIS LIST:</td></tr>\n";
	echo "<tr $bgcolor>\n";
	echo "<input type=hidden name=action value=ADD_CUSTOM_FIELD>\n";
	echo "<input type=hidden name=list_id value=$list_id>\n";
	echo "<input type=hidden name=DB value=$DB>\n";
	echo "<tr $bgcolor><td align=right>New Field Rank: </td><td align=left><select size=1 name=field_rank>\n";
	echo "$rank_select\n";
	echo "<option selected>$last_rank</option>\n";
	echo "</select> &nbsp; $NWB#vicidial_lists_fields-field_rank$NWE \n";
	echo " &nbsp; &nbsp; &nbsp; &nbsp; Field Order: <select size=1 name=field_order>\n";
	echo "<option selected>1</option>\n";
	echo "<option>2</option>\n";
	echo "<option>3</option>\n";
	echo "<option>4</option>\n";
	echo "<option>5</option>\n";
	echo "</select> &nbsp; $NWB#vicidial_lists_fields-field_order$NWE </td></tr>\n";
	echo "<tr $bgcolor><td align=right>Field Label: </td><td align=left><input type=text name=field_label size=20 maxlength=50> $NWB#vicidial_lists_fields-field_label$NWE </td></tr>\n";
	echo "<tr $bgcolor><td align=right>Field Name: </td><td align=left><textarea name=field_name rows=2 cols=60></textarea> $NWB#vicidial_lists_fields-field_name$NWE </td></tr>\n";
	echo "<tr $bgcolor><td align=right>Field Name Position: </td><td align=left><select size=1 name=name_position>\n";
	echo "<option value=\"LEFT\">LEFT</option>\n";
	echo "<option value=\"TOP\">TOP</option>\n";
	echo "</select>  $NWB#vicidial_lists_fields-name_position$NWE </td></tr>\n";
	echo "<tr $bgcolor><td align=right>Field Description: </td><td align=left><input name=field_description type=text size=70 maxlength=100> $NWB#vicidial_lists_fields-field_description$NWE </td></tr>\n";
	echo "<tr $bgcolor><td align=right>Field Help: </td><td align=left><textarea name=field_help rows=2 cols=60></textarea> $NWB#vicidial_lists_fields-field_help$NWE </td></tr>\n";
	echo "<tr $bgcolor><td align=right>Field Type: </td><td align=left><select size=1 name=field_type>\n";
	echo "<option>TEXT</option>\n";
	echo "<option>AREA</option>\n";
	echo "<option>SELECT</option>\n";
	echo "<option>MULTI</option>\n";
	echo "<option>RADIO</option>\n";
	echo "<option>CHECKBOX</option>\n";
	echo "<option>DATE</option>\n";
	echo "<option>TIME</option>\n";
	echo "<option>DISPLAY</option>\n";
	echo "<option>SCRIPT</option>\n";
	echo "<option selected>TEXT</option>\n";
	echo "</select>  $NWB#vicidial_lists_fields-field_type$NWE </td></tr>\n";
	echo "<tr $bgcolor><td align=right>Field Options: </td><td align=left><textarea name=field_options ROWS=5 COLS=60></textarea>  $NWB#vicidial_lists_fields-field_options$NWE </td></tr>\n";
	echo "<tr $bgcolor><td align=right>Option Position: </td><td align=left><select size=1 name=multi_position>\n";
	echo "<option selected value=\"HORIZONTAL\">HORIZONTAL</option>\n";
	echo "<option value=\"VERTICAL\">VERTICAL</option>\n";
	echo "</select>  $NWB#vicidial_lists_fields-multi_position$NWE </td></tr>\n";
	echo "<tr $bgcolor><td align=right>Field Size: </td><td align=left><input type=text name=field_size size=5 maxlength=3>  $NWB#vicidial_lists_fields-field_size$NWE </td></tr>\n";
	echo "<tr $bgcolor><td align=right>Field Max: </td><td align=left><input type=text name=field_max size=5 maxlength=3>  $NWB#vicidial_lists_fields-field_max$NWE </td></tr>\n";
	echo "<tr $bgcolor><td align=right>Field Default: </td><td align=left><input type=text name=field_default size=50 maxlength=255 value=\"NULL\">  $NWB#vicidial_lists_fields-field_default$NWE </td></tr>\n";
	echo "<tr $bgcolor><td align=right>Field Required: </td><td align=left><select size=1 name=field_required>\n";
	echo "<option value=\"Y\">YES</option>\n";
	echo "<option value=\"N\" SELECTED>NO</option>\n";
	echo "</select>  $NWB#vicidial_lists_fields-field_required$NWE </td></tr>\n";
	echo "<tr $bgcolor><td align=center colspan=2><input type=submit name=submit value=\"Submit\"></td></tr>\n";
	echo "</table></center></form><BR><BR>\n";
	echo "</table></center><BR><BR>\n";
	echo "</TABLE>\n";

	echo "&nbsp; <a href=\"./admin.php?ADD=311&list_id=$list_id\">Go to the list modification page for this list</a><BR><BR>\n";

	echo "&nbsp; <a href=\"$PHP_SELF?action=ADMIN_LOG&list_id=$list_id\">Click here to see Admin changes to this lists custom fields</a><BR><BR><BR> </center> &nbsp; \n";
	}
### END modify custom fields for list




################################################################################
##### BEGIN list lists as well as the number of custom fields in each list
if ($action == "LIST")
	{
	$stmt="SELECT list_id,list_name,active,campaign_id from vicidial_lists order by list_id;";
	$rslt=mysql_query($stmt, $link);
	$lists_to_print = mysql_num_rows($rslt);
	$o=0;
	while ($lists_to_print > $o) 
		{
		$rowx=mysql_fetch_row($rslt);
		$A_list_id[$o] =		$rowx[0];
		$A_list_name[$o] =		$rowx[1];
		$A_active[$o] =			$rowx[2];
		$A_campaign_id[$o] =	$rowx[3];
		$o++;
		}

	echo "<br>LIST LISTINGS WITH CUSTOM FIELDS COUNT:\n";
	echo "<center><TABLE width=$section_width cellspacing=0 cellpadding=1>\n";
	echo "<TR BGCOLOR=BLACK>";
	echo "<TD align=right><B><FONT FACE=\"Arial,Helvetica\" size=1 color=white>LIST ID</B></TD>";
	echo "<TD align=right><B><FONT FACE=\"Arial,Helvetica\" size=1 color=white>LIST NAME</B></TD>";
	echo "<TD align=right><B><FONT FACE=\"Arial,Helvetica\" size=1 color=white>ACTIVE</B></TD>";
	echo "<TD align=right><B><FONT FACE=\"Arial,Helvetica\" size=1 color=white>CAMPAIGN</B></TD>\n";
	echo "<TD align=right><B><FONT FACE=\"Arial,Helvetica\" size=1 color=white>CUSTOM FIELDS</B></TD>\n";
	echo "<TD align=right><B><FONT FACE=\"Arial,Helvetica\" size=1 color=white>MODIFY</TD>\n";
	echo "</TR>\n";

	$o=0;
	while ($lists_to_print > $o) 
		{
		$A_list_fields_count[$o]=0;
		$stmt="SELECT count(*) from vicidial_lists_fields where list_id='$A_list_id[$o]';";
		if ($DB>0) {echo "$stmt";}
		$rslt=mysql_query($stmt, $link);
		$fieldscount_to_print = mysql_num_rows($rslt);
		if ($fieldscount_to_print > 0) 
			{
			$rowx=mysql_fetch_row($rslt);
			$A_list_fields_count[$o] =	$rowx[0];
			}
		if (eregi("1$|3$|5$|7$|9$", $o))
			{$bgcolor='bgcolor="#B9CBFD"';} 
		else
			{$bgcolor='bgcolor="#9BB9FB"';}
		echo "<tr $bgcolor align=right><td><font size=1><a href=\"admin.php?ADD=311&list_id=$A_list_id[$o]\">$A_list_id[$o]</a></td>";
		echo "<td align=right><font size=1> $A_list_name[$o]</td>";
		echo "<td align=right><font size=1> $A_active[$o]</td>";
		echo "<td align=right><font size=1> $A_campaign_id[$o]</td>";
		echo "<td align=right><font size=1> $A_list_fields_count[$o]</td>";
		echo "<td align=right><font size=1><a href=\"$PHP_SELF?action=MODIFY_CUSTOM_FIELDS&list_id=$A_list_id[$o]\">MODIFY FIELDS</a></td></tr>\n";

		$o++;
		}

	echo "</TABLE></center>\n";
	}
### END list lists as well as the number of custom fields in each list





################################################################################
##### BEGIN admin log display
if ($action == "ADMIN_LOG")
	{
	if ($LOGuser_level >= 9)
		{
		echo "<TABLE><TR><TD>\n";
		echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";

		$stmt="SELECT admin_log_id,event_date,user,ip_address,event_section,event_type,record_id,event_code from vicidial_admin_log where event_section='CUSTOM_FIELDS' and record_id='$list_id' order by event_date desc limit 10000;";
		$rslt=mysql_query($stmt, $link);
		$logs_to_print = mysql_num_rows($rslt);

		echo "<br>ADMIN CHANGE LOG: Section Records - $category - $stage\n";
		echo "<center><TABLE width=$section_width cellspacing=0 cellpadding=1>\n";
		echo "<TR BGCOLOR=BLACK>";
		echo "<TD><B><FONT FACE=\"Arial,Helvetica\" size=1 color=white>ID</B></TD>";
		echo "<TD><B><FONT FACE=\"Arial,Helvetica\" size=1 color=white>DATE TIME</B></TD>";
		echo "<TD><B><FONT FACE=\"Arial,Helvetica\" size=1 color=white>USER</B></TD>\n";
		echo "<TD><B><FONT FACE=\"Arial,Helvetica\" size=1 color=white>IP</TD>\n";
		echo "<TD><B><FONT FACE=\"Arial,Helvetica\" size=1 color=white>SECTION</TD>\n";
		echo "<TD><B><FONT FACE=\"Arial,Helvetica\" size=1 color=white>TYPE</TD>\n";
		echo "<TD><B><FONT FACE=\"Arial,Helvetica\" size=1 color=white>RECORD ID</TD>\n";
		echo "<TD><B><FONT FACE=\"Arial,Helvetica\" size=1 color=white>DESCRIPTION</TD>\n";
		echo "<TD><B><FONT FACE=\"Arial,Helvetica\" size=1 color=white>GOTO</TD>\n";
		echo "</TR>\n";

		$logs_printed = '';
		$o=0;
		while ($logs_to_print > $o)
			{
			$row=mysql_fetch_row($rslt);

			if (eregi("USER|AGENT",$row[4])) {$record_link = "ADD=3&user=$row[6]";}
			if (eregi('CAMPAIGN',$row[4])) {$record_link = "ADD=31&campaign_id=$row[6]";}
			if (eregi('LIST',$row[4])) {$record_link = "ADD=311&list_id=$row[6]";}
			if (eregi('SCRIPT',$row[4])) {$record_link = "ADD=3111111&script_id=$row[6]";}
			if (eregi('FILTER',$row[4])) {$record_link = "ADD=31111111&lead_filter_id=$row[6]";}
			if (eregi('INGROUP',$row[4])) {$record_link = "ADD=3111&group_id=$row[6]";}
			if (eregi('DID',$row[4])) {$record_link = "ADD=3311&did_id=$row[6]";}
			if (eregi('USERGROUP',$row[4])) {$record_link = "ADD=311111&user_group=$row[6]";}
			if (eregi('REMOTEAGENT',$row[4])) {$record_link = "ADD=31111&remote_agent_id=$row[6]";}
			if (eregi('PHONE',$row[4])) {$record_link = "ADD=10000000000";}
			if (eregi('CALLTIME',$row[4])) {$record_link = "ADD=311111111&call_time_id=$row[6]";}
			if (eregi('SHIFT',$row[4])) {$record_link = "ADD=331111111&shift_id=$row[6]";}
			if (eregi('CONFTEMPLATE',$row[4])) {$record_link = "ADD=331111111111&template_id=$row[6]";}
			if (eregi('CARRIER',$row[4])) {$record_link = "ADD=341111111111&carrier_id=$row[6]";}
			if (eregi('SERVER',$row[4])) {$record_link = "ADD=311111111111&server_id=$row[6]";}
			if (eregi('CONFERENCE',$row[4])) {$record_link = "ADD=1000000000000";}
			if (eregi('SYSTEM',$row[4])) {$record_link = "ADD=311111111111111";}
			if (eregi('CATEGOR',$row[4])) {$record_link = "ADD=331111111111111";}
			if (eregi('GROUPALIAS',$row[4])) {$record_link = "ADD=33111111111&group_alias_id=$row[6]";}

			if (eregi("1$|3$|5$|7$|9$", $o))
				{$bgcolor='bgcolor="#B9CBFD"';} 
			else
				{$bgcolor='bgcolor="#9BB9FB"';}
			echo "<tr $bgcolor><td><font size=1><a href=\"admin.php?ADD=730000000000000&stage=$row[0]\">$row[0]</a></td>";
			echo "<td><font size=1> $row[1]</td>";
			echo "<td><font size=1> <a href=\"admin.php?ADD=710000000000000&stage=$row[2]\">$row[2]</a></td>";
			echo "<td><font size=1> $row[3]</td>";
			echo "<td><font size=1> $row[4]</td>";
			echo "<td><font size=1> $row[5]</td>";
			echo "<td><font size=1> $row[6]</td>";
			echo "<td><font size=1> $row[7]</td>";
			echo "<td><font size=1> <a href=\"admin.php?$record_link\">GOTO</a></td>";
			echo "</tr>\n";
			$logs_printed .= "'$row[0]',";
			$o++;
			}
		echo "</TABLE><BR><BR>\n";
		echo "\n";
		echo "</center>\n";
		}
	else
		{
		echo "You do not have permission to view this page\n";
		exit;
		}
	}





$ENDtime = date("U");
$RUNtime = ($ENDtime - $STARTtime);
echo "\n\n\n<br><br><br>\n<font size=1> runtime: $RUNtime seconds &nbsp; &nbsp; &nbsp; &nbsp; Version: $admin_version &nbsp; &nbsp; Build: $build</font>";

?>

</body>
</html>


<?php
################################################################################
################################################################################
##### Functions
################################################################################
################################################################################




################################################################################
##### BEGIN add field function
function add_field_function($DB,$link,$linkCUSTOM,$ip,$user,$table_exists,$field_id,$list_id,$field_label,$field_name,$field_description,$field_rank,$field_help,$field_type,$field_options,$field_size,$field_max,$field_default,$field_required,$field_cost,$multi_position,$name_position,$field_order,$vicidial_list_fields)
	{
	$table_exists=0;
	$stmt="SHOW TABLES LIKE \"custom_$list_id\";";
	$rslt=mysql_query($stmt, $link);
	$tablecount_to_print = mysql_num_rows($rslt);
	if ($tablecount_to_print > 0) 
		{$table_exists =	1;}
	if ($DB>0) {echo "$stmt|$tablecount_to_print|$table_exists";}

	if ($table_exists < 1)
		{$field_sql = "CREATE TABLE custom_$list_id (lead_id INT(9) UNSIGNED PRIMARY KEY NOT NULL, $field_label ";}
	else
		{$field_sql = "ALTER TABLE custom_$list_id ADD $field_label ";}

	$field_options_ENUM='';
	$field_cost=1;
	if ( ($field_type=='SELECT') or ($field_type=='RADIO') )
		{
		$field_options_array = explode("\n",$field_options);
		$field_options_count = count($field_options_array);
		$te=0;
		while ($te < $field_options_count)
			{
			if (preg_match("/,/",$field_options_array[$te]))
				{
				$field_options_value_array = explode(",",$field_options_array[$te]);
				$field_options_ENUM .= "'$field_options_value_array[0]',";
				}
			$te++;
			}
		$field_options_ENUM = preg_replace("/.$/",'',$field_options_ENUM);
		$field_sql .= "ENUM($field_options_ENUM) ";
		$field_cost = strlen($field_options_ENUM);
		}
	if ( ($field_type=='MULTI') or ($field_type=='CHECKBOX') )
		{
		$field_options_array = explode("\n",$field_options);
		$field_options_count = count($field_options_array);
		$te=0;
		while ($te < $field_options_count)
			{
			if (preg_match("/,/",$field_options_array[$te]))
				{
				$field_options_value_array = explode(",",$field_options_array[$te]);
				$field_options_ENUM .= "'$field_options_value_array[0]',";
				}
			$te++;
			}
		$field_options_ENUM = preg_replace("/.$/",'',$field_options_ENUM);
		$field_cost = strlen($field_options_ENUM);
		if ($field_cost < 1) {$field_cost=1;};
		$field_sql .= "VARCHAR($field_cost) ";
		}
	if ($field_type=='TEXT') 
		{
		if ($field_max < 1) {$field_max=1;};
		$field_sql .= "VARCHAR($field_max) ";
		$field_cost = ($field_max + $field_cost);
		}
	if ($field_type=='AREA') 
		{
		$field_sql .= "TEXT ";
		$field_cost = 15;
		}
	if ($field_type=='DATE') 
		{
		$field_sql .= "DATE ";
		$field_cost = 10;
		}
	if ($field_type=='TIME') 
		{
		$field_sql .= "TIME ";
		$field_cost = 8;
		}
	$field_cost = ($field_cost * 3); # account for utf8 database

	if ( ($field_default != 'NULL') and ($field_type!='AREA') and ($field_type!='DATE') and ($field_type!='TIME') )
		{$field_sql .= "default '$field_default'";}

	if ($table_exists < 1)
		{$field_sql .= ");";}
	else
		{$field_sql .= ";";}

	if ( ($field_type=='DISPLAY') or ($field_type=='SCRIPT') or (preg_match("/\|$field_label\|/",$vicidial_list_fields)) )
		{
		if ($DB) {echo "Non-DB $field_type field type, $field_label\n";} 
		}
	else
		{
		$stmtCUSTOM="$field_sql";
		$rsltCUSTOM=mysql_query($stmtCUSTOM, $linkCUSTOM);
		$table_update = mysql_affected_rows($linkCUSTOM);
		if ($DB) {echo "$table_update|$stmtCUSTOM\n";}
		if (!$rsltCUSTOM) {echo('Could not execute: ' . mysql_error());}
		}

	$stmt="INSERT INTO vicidial_lists_fields set field_label='$field_label',field_name='$field_name',field_description='$field_description',field_rank='$field_rank',field_help='$field_help',field_type='$field_type',field_options='$field_options',field_size='$field_size',field_max='$field_max',field_default='$field_default',field_required='$field_required',field_cost='$field_cost',list_id='$list_id',multi_position='$multi_position',name_position='$name_position',field_order='$field_order';";
	$rslt=mysql_query($stmt, $link);
	$field_update = mysql_affected_rows($link);
	if ($DB) {echo "$field_update|$stmt\n";}
	if (!$rslt) {echo('Could not execute: ' . mysql_error());}

	### LOG INSERTION Admin Log Table ###
	$SQL_log = "$stmt|$stmtCUSTOM";
	$SQL_log = ereg_replace(';','',$SQL_log);
	$SQL_log = addslashes($SQL_log);
	$stmt="INSERT INTO vicidial_admin_log set event_date=NOW(), user='$user', ip_address='$ip', event_section='CUSTOM_FIELDS', event_type='ADD', record_id='$list_id', event_code='ADMIN ADD CUSTOM LIST FIELD', event_sql=\"$SQL_log\", event_notes='';";
	if ($DB) {echo "|$stmt|\n";}
	$rslt=mysql_query($stmt, $link);
	}
##### END add field function





################################################################################
##### BEGIN modify field function
function modify_field_function($DB,$link,$linkCUSTOM,$ip,$user,$table_exists,$field_id,$list_id,$field_label,$field_name,$field_description,$field_rank,$field_help,$field_type,$field_options,$field_size,$field_max,$field_default,$field_required,$field_cost,$multi_position,$name_position,$field_order,$vicidial_list_fields)
	{
	$field_sql = "ALTER TABLE custom_$list_id MODIFY $field_label ";
	$field_options_ENUM='';
	$field_cost=1;
	if ( ($field_type=='SELECT') or ($field_type=='RADIO') )
		{
		$field_options_array = explode("\n",$field_options);
		$field_options_count = count($field_options_array);
		$te=0;
		while ($te < $field_options_count)
			{
			if (preg_match("/,/",$field_options_array[$te]))
				{
				$field_options_value_array = explode(",",$field_options_array[$te]);
				$field_options_ENUM .= "'$field_options_value_array[0]',";
				}
			$te++;
			}
		$field_options_ENUM = preg_replace("/.$/",'',$field_options_ENUM);
		$field_sql .= "ENUM($field_options_ENUM) ";
		$field_cost = strlen($field_options_ENUM);
		}
	if ( ($field_type=='MULTI') or ($field_type=='CHECKBOX') )
		{
		$field_options_array = explode("\n",$field_options);
		$field_options_count = count($field_options_array);
		$te=0;
		while ($te < $field_options_count)
			{
			if (preg_match("/,/",$field_options_array[$te]))
				{
				$field_options_value_array = explode(",",$field_options_array[$te]);
				$field_options_ENUM .= "'$field_options_value_array[0]',";
				}
			$te++;
			}
		$field_options_ENUM = preg_replace("/.$/",'',$field_options_ENUM);
		$field_cost = strlen($field_options_ENUM);
		$field_sql .= "VARCHAR($field_cost) ";
		}
	if ($field_type=='TEXT') 
		{
		$field_sql .= "VARCHAR($field_max) ";
		$field_cost = ($field_max + $field_cost);
		}
	if ($field_type=='AREA') 
		{
		$field_sql .= "TEXT ";
		$field_cost = 15;
		}
	if ($field_type=='DATE') 
		{
		$field_sql .= "DATE ";
		$field_cost = 10;
		}
	if ($field_type=='TIME') 
		{
		$field_sql .= "TIME ";
		$field_cost = 8;
		}
	$field_cost = ($field_cost * 3); # account for utf8 database

	if ( ($field_default == 'NULL') or ($field_type=='AREA') or ($field_type=='DATE') or ($field_type=='TIME') )
		{$field_sql .= ";";}
	else
		{$field_sql .= "default '$field_default';";}

	if ( ($field_type=='DISPLAY') or ($field_type=='SCRIPT') or (preg_match("/\|$field_label\|/",$vicidial_list_fields)) )
		{
		if ($DB) {echo "Non-DB $field_type field type, $field_label\n";} 
		}
	else
		{
		$stmtCUSTOM="$field_sql";
		$rsltCUSTOM=mysql_query($stmtCUSTOM, $linkCUSTOM);
		$field_update = mysql_affected_rows($linkCUSTOM);
		if ($DB) {echo "$field_update|$stmtCUSTOM\n";}
		if (!$rsltCUSTOM) {echo('Could not execute: ' . mysql_error());}
		}

	$stmt="UPDATE vicidial_lists_fields set field_label='$field_label',field_name='$field_name',field_description='$field_description',field_rank='$field_rank',field_help='$field_help',field_type='$field_type',field_options='$field_options',field_size='$field_size',field_max='$field_max',field_default='$field_default',field_required='$field_required',field_cost='$field_cost',multi_position='$multi_position',name_position='$name_position',field_order='$field_order' where list_id='$list_id' and field_id='$field_id';";
	$rslt=mysql_query($stmt, $link);
	$field_update = mysql_affected_rows($link);
	if ($DB) {echo "$field_update|$stmt\n";}
	if (!$rslt) {echo('Could not execute: ' . mysql_error());}

	### LOG INSERTION Admin Log Table ###
	$SQL_log = "$stmt|$stmtCUSTOM";
	$SQL_log = ereg_replace(';','',$SQL_log);
	$SQL_log = addslashes($SQL_log);
	$stmt="INSERT INTO vicidial_admin_log set event_date=NOW(), user='$user', ip_address='$ip', event_section='CUSTOM_FIELDS', event_type='MODIFY', record_id='$list_id', event_code='ADMIN MODIFY CUSTOM LIST FIELD', event_sql=\"$SQL_log\", event_notes='';";
	if ($DB) {echo "|$stmt|\n";}
	$rslt=mysql_query($stmt, $link);
	}
##### END modify field function





################################################################################
##### BEGIN delete field function
function delete_field_function($DB,$link,$linkCUSTOM,$ip,$user,$table_exists,$field_id,$list_id,$field_label,$field_name,$field_description,$field_rank,$field_help,$field_type,$field_options,$field_size,$field_max,$field_default,$field_required,$field_cost,$multi_position,$name_position,$field_order,$vicidial_list_fields)
	{
	if ( ($field_type=='DISPLAY') or ($field_type=='SCRIPT') or (preg_match("/\|$field_label\|/",$vicidial_list_fields)) )
		{
		if ($DB) {echo "Non-DB $field_type field type, $field_label\n";} 
		}
	else
		{
		$stmtCUSTOM="ALTER TABLE custom_$list_id DROP $field_label;";
		$rsltCUSTOM=mysql_query($stmtCUSTOM, $linkCUSTOM);
		$table_update = mysql_affected_rows($linkCUSTOM);
		if ($DB) {echo "$table_update|$stmtCUSTOM\n";}
		if (!$rsltCUSTOM) {echo('Could not execute: ' . mysql_error());}
		}

	$stmt="DELETE FROM vicidial_lists_fields WHERE field_label='$field_label' and field_id='$field_id' and list_id='$list_id' LIMIT 1;";
	$rslt=mysql_query($stmt, $link);
	$field_update = mysql_affected_rows($link);
	if ($DB) {echo "$field_update|$stmt\n";}
	if (!$rslt) {echo('Could not execute: ' . mysql_error());}

	### LOG INSERTION Admin Log Table ###
	$SQL_log = "$stmt|$stmtCUSTOM";
	$SQL_log = ereg_replace(';','',$SQL_log);
	$SQL_log = addslashes($SQL_log);
	$stmt="INSERT INTO vicidial_admin_log set event_date=NOW(), user='$user', ip_address='$ip', event_section='CUSTOM_FIELDS', event_type='DELETE', record_id='$list_id', event_code='ADMIN DELETE CUSTOM LIST FIELD', event_sql=\"$SQL_log\", event_notes='';";
	if ($DB) {echo "|$stmt|\n";}
	$rslt=mysql_query($stmt, $link);
	}
##### END delete field function
