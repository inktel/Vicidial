<?php
# 
# functions.php    version 2.4
#
# functions for agent scripts
#
# Copyright (C) 2010  Matt Florell <vicidial@gmail.com>    LICENSE: AGPLv2
#
#
# CHANGES:
# 100629-1201 - First Build
#



##### BEGIN gather values for display of custom list fields for a lead #####
function custom_list_fields_values($lead_id,$list_id,$uniqueid,$user)
	{
	$STARTtime = date("U");
	$TODAY = date("Y-m-d");
	$NOW_TIME = date("Y-m-d H:i:s");

	$vicidial_list_fields = '|lead_id|vendor_lead_code|source_id|list_id|gmt_offset_now|called_since_last_reset|phone_code|phone_number|title|first_name|middle_initial|last_name|address1|address2|address3|city|state|province|postal_code|country_code|gender|date_of_birth|alt_phone|email|security_phrase|comments|called_count|last_local_call_time|rank|owner|';

	require("dbconnect.php");

	$CFoutput='';
	$stmt="SHOW TABLES LIKE \"custom_$list_id\";";
	if ($DB>0) {echo "$stmt";}
	$rslt=mysql_query($stmt, $link);
		if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'05002',$user,$server_ip,$session_name,$one_mysql_log);}
	$tablecount_to_print = mysql_num_rows($rslt);
	if ($tablecount_to_print > 0) 
		{
		$stmt="SELECT count(*) from custom_$list_id;";
		if ($DB>0) {echo "$stmt";}
		$rslt=mysql_query($stmt, $link);
			if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'05003',$user,$server_ip,$session_name,$one_mysql_log);}
		$fieldscount_to_print = mysql_num_rows($rslt);
		if ($fieldscount_to_print > 0) 
			{
			$rowx=mysql_fetch_row($rslt);
			$custom_records_count =	$rowx[0];

			$select_SQL='';
			$stmt="SELECT field_id,field_label,field_name,field_description,field_rank,field_help,field_type,field_options,field_size,field_max,field_default,field_cost,field_required,multi_position,name_position,field_order from vicidial_lists_fields where list_id='$list_id' order by field_rank,field_order,field_label;";
			$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'05004',$user,$server_ip,$session_name,$one_mysql_log);}
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
				$A_field_value[$o] =		'';

				if (!preg_match("/\|$A_field_label[$o]\|/",$vicidial_list_fields))
					{
					if ( ($A_field_type[$o]=='DISPLAY') or ($A_field_type[$o]=='SCRIPT') )
						{
						$select_SQL .= "8,";
						$A_field_select[$o]='----EMPTY----';
						}
					else
						{
						$select_SQL .= "$A_field_label[$o],";
						$A_field_select[$o]=$A_field_label[$o];
						}
					}
				else
					{
					$select_SQL .= "8,";
					$A_field_value[$o] = '--A--' . $A_field_label[$o] . '--B--';
					}
				$o++;
				$rank_select .= "<option>$o</option>";
				}
			$o++;
			$rank_select .= "<option>$o</option>";
			$last_rank = $o;
			$select_SQL = preg_replace("/.$/",'',$select_SQL);

			$list_lead_ct=0;
			if (strlen($select_SQL)>0)
				{
				##### BEGIN grab the data from custom table for the lead_id
				$stmt="SELECT $select_SQL FROM custom_$list_id where lead_id='$lead_id' LIMIT 1;";
				$rslt=mysql_query($stmt, $link);
					if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'05005',$user,$server_ip,$session_name,$one_mysql_log);}
				if ($DB) {echo "$stmt\n";}
				$list_lead_ct = mysql_num_rows($rslt);
				}
			if ($list_lead_ct > 0)
				{
				$row=mysql_fetch_row($rslt);
				$o=0;
				while ($fields_to_print >= $o) 
					{
					$A_field_value[$o]		= trim("$row[$o]");
					if ($A_field_select[$o]=='----EMPTY----')
						{$A_field_value[$o]='';}
					if (preg_match("/\|$A_field_label[$o]\|/",$vicidial_list_fields))
						{$A_field_value[$o] = '--A--' . $A_field_label[$o] . '--B--';}
					$o++;
					}
				}
			else
				{
				if ($DB) {echo "ERROR: no custom data for this lead: $lead_id\n";}
				}
			##### END grab the data from custom table for the lead_id


			$CFoutput .= "<input type=hidden name=stage id=stage value=\"SUBMIT\">\n";
			$CFoutput .= "<center><TABLE cellspacing=2 cellpadding=2>\n";
			if ($fields_to_print < 1) 
				{$CFoutput .= "<tr bgcolor=white align=center><td colspan=4><font size=1>There are no custom fields for this list</td></tr>";}

			$o=0;
			$last_field_rank=0;
			while ($fields_to_print > $o) 
				{
				$helpHTML='';
				if (strlen($A_field_help[$o])>0)
					{$helpHTML="&nbsp; <a href=\"javascript:open_help('HELP_$A_field_label[$o]','$A_field_help[$o]');\">help+</a>";}
				if ($last_field_rank=="$A_field_rank[$o]")
					{$CFoutput .= " &nbsp; &nbsp; &nbsp; &nbsp; ";}
				else
					{
					$CFoutput .= "</td></tr>\n";
					$CFoutput .= "<tr bgcolor=white><td align=";
					if ( ($A_name_position[$o]=='TOP') or ($A_field_type[$o]=='SCRIPT') )
						{$CFoutput .= "left colspan=2";}
					else
						{$CFoutput .= "right";}
					$CFoutput .= "><font size=2>";
					}
				if ($A_field_type[$o]!='SCRIPT')
					{$CFoutput .= "<B>$A_field_name[$o]</B>";}
				if ( ($A_name_position[$o]=='TOP') or ($A_field_type[$o]=='SCRIPT') )
					{$CFoutput .= " &nbsp; <span style=\"position:static;\" id=P_HELP_$A_field_label[$o]></span><span style=\"position:static;background:white;\" id=HELP_$A_field_label[$o]> $helpHTML</span><BR>";}
				else
					{
					if ($last_field_rank=="$A_field_rank[$o]")
						{$CFoutput .= " &nbsp;";}
					else
						{$CFoutput .= "</td><td align=left><font size=2>";}
					}
				$field_HTML='';

				if ($A_field_type[$o]=='SELECT')
					{
					$field_HTML .= "<select size=1 name=$A_field_label[$o] id=$A_field_label[$o]>\n";
					}
				if ($A_field_type[$o]=='MULTI')
					{
					$field_HTML .= "<select MULTIPLE size=$A_field_size[$o] name=$A_field_label[$o][] id=$A_field_label[$o][]>\n";
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
								if (strlen($A_field_value[$o]) > 0) 
									{
									if (preg_match("/$field_options_value_array[0]/",$A_field_value[$o]))
										{$field_selected = 'SELECTED';}
									}
								else
									{
									if ($A_field_default[$o] == "$field_options_value_array[0]") {$field_selected = 'SELECTED';}
									}
								$field_HTML .= "<option value=\"$field_options_value_array[0]\" $field_selected>$field_options_value_array[1]</option>\n";
								}
							if ( ($A_field_type[$o]=='RADIO') or ($A_field_type[$o]=='CHECKBOX') )
								{
								if ($A_multi_position[$o]=='VERTICAL') 
									{$field_HTML .= " &nbsp; ";}
								if (strlen($A_field_value[$o]) > 0) 
									{
									if (preg_match("/$field_options_value_array[0]/",$A_field_value[$o]))
										{$field_selected = 'CHECKED';}
									}
								else
									{
									if ($A_field_default[$o] == "$field_options_value_array[0]") {$field_selected = 'CHECKED';}
									}
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
					if (strlen($A_field_value[$o]) < 1) {$A_field_value[$o] = $A_field_default[$o];}
					$field_HTML .= "<input type=text size=$A_field_size[$o] maxlength=$A_field_max[$o] name=$A_field_label[$o] id=$A_field_label[$o] value=\"$A_field_value[$o]\">\n";
					}
				if ($A_field_type[$o]=='AREA') 
					{
					if ($A_field_default[$o]=='NULL') {$A_field_default[$o]='';}
					if (strlen($A_field_value[$o]) < 1) {$A_field_value[$o] = $A_field_default[$o];}
					$field_HTML .= "<textarea name=$A_field_label[$o] id=$A_field_label[$o] ROWS=$A_field_max[$o] COLS=$A_field_size[$o]>$A_field_value[$o]</textarea>";
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
					if (strlen($A_field_value[$o]) > 0) {$default_date = $A_field_value[$o];}

					$field_HTML .= "<input type=text size=11 maxlength=10 name=$A_field_label[$o] id=$A_field_label[$o] value=\"$default_date\" onclick=\"f_tcalToggle()\">\n";
					$field_HTML .= "<script language=\"JavaScript\">\n";
					$field_HTML .= "var o_cal = new tcal ({\n";
					$field_HTML .= "	'formname': 'form_custom_fields',\n";
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
					if (strlen($A_field_value[$o]) > 2) 
						{
						$default_time = $A_field_value[$o];
						$time_field_value = explode(':',$default_time);
						$default_hour = $time_field_value[0];
						$default_minute = $time_field_value[1];
						}
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

				if ( ($A_name_position[$o]=='LEFT') and ($A_field_type[$o]!='SCRIPT') )
					{
					$CFoutput .= " $field_HTML <span style=\"position:static;\" id=P_HELP_$A_field_label[$o]></span><span style=\"position:static;background:white;\" id=HELP_$A_field_label[$o]> $helpHTML</span>";
					}
				else
					{
					$CFoutput .= " $field_HTML\n";
					}

				$last_field_rank=$A_field_rank[$o];
				$o++;
				}
			$CFoutput .= "</td></tr></table>\n";
			}
		else
			{$CFoutput .= "ERROR: no custom list fields\n";}
		}
	else
		{$CFoutput .= "ERROR: no custom list fields table\n";}


	##### BEGIN parsing for vicidial variables #####
	if (preg_match("/--A--/",$CFoutput))
		{
		if ( (eregi('--A--user_custom_',$CFoutput)) or (eregi('--A--fullname',$CFoutput)) )
			{
			$stmt = "select custom_one,custom_two,custom_three,custom_four,custom_five,full_name from vicidial_users where user='$user';";
			if ($DB) {echo "$stmt\n";}
			$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'05006',$user,$server_ip,$session_name,$one_mysql_log);}
			$VUC_ct = mysql_num_rows($rslt);
			if ($VUC_ct > 0)
				{
				$row=mysql_fetch_row($rslt);
				$user_custom_one	=		trim($row[0]);
				$user_custom_two	=		trim($row[1]);
				$user_custom_three	=		trim($row[2]);
				$user_custom_four	=		trim($row[3]);
				$user_custom_five	=		trim($row[4]);
				$fullname	=				trim($row[5]);
				}
			}

		if (eregi('--A--dialed_',$CFoutput))
			{
			$dialed_number =	$phone_number;
			$dialed_label =		'NONE';

			### find the dialed number and label for this call
			$stmt = "SELECT phone_number,alt_dial from vicidial_log where uniqueid='$uniqueid';";
			if ($DB) {echo "$stmt\n";}
			$rslt=mysql_query($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'05008',$user,$server_ip,$session_name,$one_mysql_log);}
			$vl_dialed_ct = mysql_num_rows($rslt);
			if ($vl_dialed_ct > 0)
				{
				$row=mysql_fetch_row($rslt);
				$dialed_number =	$row[0];
				$dialed_label =		$row[1];
				}
			}

		##### grab the data from vicidial_list for the lead_id
		$stmt="SELECT lead_id,entry_date,modify_date,status,user,vendor_lead_code,source_id,list_id,gmt_offset_now,called_since_last_reset,phone_code,phone_number,title,first_name,middle_initial,last_name,address1,address2,address3,city,state,province,postal_code,country_code,gender,date_of_birth,alt_phone,email,security_phrase,comments,called_count,last_local_call_time,rank,owner FROM vicidial_list where lead_id='$lead_id' LIMIT 1;";
		$rslt=mysql_query($stmt, $link);
			if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'05007',$user,$server_ip,$session_name,$one_mysql_log);}
		if ($DB) {echo "$stmt\n";}
		$list_lead_ct = mysql_num_rows($rslt);
		if ($list_lead_ct > 0)
			{
			$row=mysql_fetch_row($rslt);
			$dispo				= trim($row[3]);
			$tsr				= trim($row[4]);
			$vendor_id			= trim($row[5]);
			$vendor_lead_code	= trim($row[5]);
			$source_id			= trim($row[6]);
			$list_id			= trim($row[7]);
			$gmt_offset_now		= trim($row[8]);
			$phone_code			= trim($row[10]);
			$phone_number		= trim($row[11]);
			$title				= trim($row[12]);
			$first_name			= trim($row[13]);
			$middle_initial		= trim($row[14]);
			$last_name			= trim($row[15]);
			$address1			= trim($row[16]);
			$address2			= trim($row[17]);
			$address3			= trim($row[18]);
			$city				= trim($row[19]);
			$state				= trim($row[20]);
			$province			= trim($row[21]);
			$postal_code		= trim($row[22]);
			$country_code		= trim($row[23]);
			$gender				= trim($row[24]);
			$date_of_birth		= trim($row[25]);
			$alt_phone			= trim($row[26]);
			$email				= trim($row[27]);
			$security			= trim($row[28]);
			$comments			= trim($row[29]);
			$called_count		= trim($row[30]);
			$rank				= trim($row[32]);
			$owner				= trim($row[33]);
			}

		$CFoutput = eregi_replace('--A--lead_id--B--',"$lead_id",$CFoutput);
		$CFoutput = eregi_replace('--A--vendor_id--B--',"$vendor_id",$CFoutput);
		$CFoutput = eregi_replace('--A--vendor_lead_code--B--',"$vendor_lead_code",$CFoutput);
		$CFoutput = eregi_replace('--A--list_id--B--',"$list_id",$CFoutput);
		$CFoutput = eregi_replace('--A--gmt_offset_now--B--',"$gmt_offset_now",$CFoutput);
		$CFoutput = eregi_replace('--A--phone_code--B--',"$phone_code",$CFoutput);
		$CFoutput = eregi_replace('--A--phone_number--B--',"$phone_number",$CFoutput);
		$CFoutput = eregi_replace('--A--title--B--',"$title",$CFoutput);
		$CFoutput = eregi_replace('--A--first_name--B--',"$first_name",$CFoutput);
		$CFoutput = eregi_replace('--A--middle_initial--B--',"$middle_initial",$CFoutput);
		$CFoutput = eregi_replace('--A--last_name--B--',"$last_name",$CFoutput);
		$CFoutput = eregi_replace('--A--address1--B--',"$address1",$CFoutput);
		$CFoutput = eregi_replace('--A--address2--B--',"$address2",$CFoutput);
		$CFoutput = eregi_replace('--A--address3--B--',"$address3",$CFoutput);
		$CFoutput = eregi_replace('--A--city--B--',"$city",$CFoutput);
		$CFoutput = eregi_replace('--A--state--B--',"$state",$CFoutput);
		$CFoutput = eregi_replace('--A--province--B--',"$province",$CFoutput);
		$CFoutput = eregi_replace('--A--postal_code--B--',"$postal_code",$CFoutput);
		$CFoutput = eregi_replace('--A--country_code--B--',"$country_code",$CFoutput);
		$CFoutput = eregi_replace('--A--gender--B--',"$gender",$CFoutput);
		$CFoutput = eregi_replace('--A--date_of_birth--B--',"$date_of_birth",$CFoutput);
		$CFoutput = eregi_replace('--A--alt_phone--B--',"$alt_phone",$CFoutput);
		$CFoutput = eregi_replace('--A--email--B--',"$email",$CFoutput);
		$CFoutput = eregi_replace('--A--security_phrase--B--',"$security_phrase",$CFoutput);
		$CFoutput = eregi_replace('--A--comments--B--',"$comments",$CFoutput);
		$CFoutput = eregi_replace('--A--user--B--',"$user",$CFoutput);
		$CFoutput = eregi_replace('--A--pass--B--',"$pass",$CFoutput);
		$CFoutput = eregi_replace('--A--campaign--B--',"$campaign",$CFoutput);
		$CFoutput = eregi_replace('--A--server_ip--B--',"$server_ip",$CFoutput);
		$CFoutput = eregi_replace('--A--session_id--B--',"$session_id",$CFoutput);
		$CFoutput = eregi_replace('--A--dialed_number--B--',"$dialed_number",$CFoutput);
		$CFoutput = eregi_replace('--A--dialed_label--B--',"$dialed_label",$CFoutput);
		$CFoutput = eregi_replace('--A--source_id--B--',"$source_id",$CFoutput);
		$CFoutput = eregi_replace('--A--rank--B--',"$rank",$CFoutput);
		$CFoutput = eregi_replace('--A--owner--B--',"$owner",$CFoutput);
		$CFoutput = eregi_replace('--A--fullname--B--',"$fullname",$CFoutput);
		$CFoutput = eregi_replace('--A--uniqueid--B--',"$uniqueid",$CFoutput);
		$CFoutput = eregi_replace('--A--user_custom_one--B--',"$user_custom_one",$CFoutput);
		$CFoutput = eregi_replace('--A--user_custom_two--B--',"$user_custom_two",$CFoutput);
		$CFoutput = eregi_replace('--A--user_custom_three--B--',"$user_custom_three",$CFoutput);
		$CFoutput = eregi_replace('--A--user_custom_four--B--',"$user_custom_four",$CFoutput);
		$CFoutput = eregi_replace('--A--user_custom_five--B--',"$user_custom_five",$CFoutput);
		$CFoutput = eregi_replace('--A--preset_number_a--B--',"$preset_number_a",$CFoutput);
		$CFoutput = eregi_replace('--A--preset_number_b--B--',"$preset_number_b",$CFoutput);
		$CFoutput = eregi_replace('--A--preset_number_c--B--',"$preset_number_c",$CFoutput);
		$CFoutput = eregi_replace('--A--preset_number_d--B--',"$preset_number_d",$CFoutput);
		$CFoutput = eregi_replace('--A--preset_number_e--B--',"$preset_number_e",$CFoutput);
		$CFoutput = eregi_replace('--A--preset_dtmf_a--B--',"$preset_dtmf_a",$CFoutput);
		$CFoutput = eregi_replace('--A--preset_dtmf_b--B--',"$preset_dtmf_b",$CFoutput);
		$CFoutput = eregi_replace('--A--did_id--B--',"$did_id",$CFoutput);
		$CFoutput = eregi_replace('--A--did_extension--B--',"$did_extension",$CFoutput);
		$CFoutput = eregi_replace('--A--did_pattern--B--',"$did_pattern",$CFoutput);
		$CFoutput = eregi_replace('--A--did_description--B--',"$did_description",$CFoutput);
		$CFoutput = eregi_replace('--A--closecallid--B--',"$closecallid",$CFoutput);
		$CFoutput = eregi_replace('--A--xfercallid--B--',"$xfercallid",$CFoutput);
		$CFoutput = eregi_replace('--A--agent_log_id--B--',"$agent_log_id",$CFoutput);

		# custom fields replacement
		$o=0;
		while ($fields_to_print > $o) 
			{
			$CFoutput = eregi_replace("--A--$A_field_label[$o]--B--","$A_field_value[$o]",$CFoutput);
			$o++;
			}

		if ($DB > 0) {echo "$CFoutput<BR>\n";}
		}
	##### END parsing for vicidial variables #####


	return $CFoutput;
	}
##### END gather values for display of custom list fields for a lead #####





##### MySQL Error Logging #####
function mysql_error_logging($NOW_TIME,$link,$mel,$stmt,$query_id,$user,$server_ip,$session_name,$one_mysql_log)
	{
	$NOW_TIME = date("Y-m-d H:i:s");
	#	mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00001',$user,$server_ip,$session_name,$one_mysql_log);
	$errno='';   $error='';
	if ( ($mel > 0) or ($one_mysql_log > 0) )
		{
		$errno = mysql_errno($link);
		if ( ($errno > 0) or ($mel > 1) or ($one_mysql_log > 0) )
			{
			$error = mysql_error($link);
			$efp = fopen ("./vicidial_mysql_errors.txt", "a");
			fwrite ($efp, "$NOW_TIME|vdc_db_query|$query_id|$errno|$error|$stmt|$user|$server_ip|$session_name|\n");
			fclose($efp);
			}
		}
	$one_mysql_log=0;
	return $errno;
	}


?>