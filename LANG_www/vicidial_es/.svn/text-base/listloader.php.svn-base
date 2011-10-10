<?
# listloader.php
# 
# Copyright (C) 2007  Matt Florell,Joe Johnson <vicidial@gmail.com>    LICENSE: GPLv2
#
# AST Web GUI lead loader from formatted file
# 
# CHANGES
# 50602-1640 - First version created by Joe Johnson
# 51128-1108 - Removed PHP global vars requirement
# 60421-1043 - check GET/POST vars lines with isset to not trigger PHP NOTICES
# 60616-1006 - added listID override and gmt_offset lookup while loading
# 60619-1652 - Added variable filtering to eliminate SQL injection attack threat
# 60822-1105 - fixed for nonwritable directories
# 60906-1059 - added filter of non-digits in alt_phone field
# 61110-1222 - added new USA-Canada DST scheme and Brazil DST scheme
# 61128-1046 - added postal code GMT lookup and duplicate check options
# 70205-1703 - Defaulted phone_code to 1 if not populated
# 70417-1059 - Fixed default phone_code bug
#
# make sure vicidial_list exists and that your file follows the formatting correctly. This page does not dedupe or do any other lead filtering actions yet at this time.
#

$version = '2.0.3';
$build = '770417-1059';

header ("Content-type: text/html; charset=utf-8");

require("dbconnect.php");

$PHP_AUTH_USER=$_SERVER['PHP_AUTH_USER'];
$PHP_AUTH_PW=$_SERVER['PHP_AUTH_PW'];
$PHP_SELF=$_SERVER['PHP_SELF'];
$leadfile=$_FILES["leadfile"];
	$LF_orig = $_FILES['leadfile']['name'];
	$LF_path = $_FILES['leadfile']['tmp_name'];
$submit_file=$_GET["submit_file"];				if (!$submit_file) {$submit_file=$_POST["submit_file"];}
$list_id_override=$_GET["list_id_override"];	if (!$list_id_override) {$list_id_override=$_POST["list_id_override"];}
	$list_id_override = (preg_replace("/\D/","",$list_id_override));
$submit=$_GET["submit"];						if (!$submit) {$submit=$_POST["submit"];}
$ENVIAR=$_GET["ENVIAR"];						if (!$ENVIAR) {$ENVIAR=$_POST["ENVIAR"];}
if (isset($_GET["dupcheck"]))				{$dupcheck=$_GET["dupcheck"];}
	elseif (isset($_POST["dupcheck"]))		{$dupcheck=$_POST["dupcheck"];}
if (isset($_GET["postalgmt"]))				{$postalgmt=$_GET["postalgmt"];}
	elseif (isset($_POST["postalgmt"]))		{$postalgmt=$_POST["postalgmt"];}


#$DB=1;
#$DBX=1;

$PHP_AUTH_USER = ereg_replace("[^0-9a-zA-Z]","",$PHP_AUTH_USER);
$PHP_AUTH_PW = ereg_replace("[^0-9a-zA-Z]","",$PHP_AUTH_PW);
$list_id_override = ereg_replace("[^0-9]","",$list_id_override);


$script_name = getenv("SCRIPT_NAME");
$server_name = getenv("SERVER_NAME");
$server_port = getenv("SERVER_PORT");
if (eregi("443",$server_port)) {$HTTPprotocol = 'https://';}
  else {$HTTPprotocol = 'http://';}
$admDIR = "$HTTPprotocol$server_name$script_name";
$admDIR = eregi_replace('listloader.php','',$admDIR);
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


?>


<script language="JavaScript1.2">
function openNewWindow(url) {
  window.open (url,"",'width=500,height=300,scrollbars=yes,menubar=yes,address=yes');
}
function ShowProgress(good, bad, total, dup, post) {
	parent.lead_count.document.open();
	parent.lead_count.document.write('<html><body><table border=0 width=200 cellpadding=10 cellspacing=0 align=center valign=top><tr bgcolor="#000000"><th colspan=2><font face="arial, helvetica" size=3 color=white>Estado actual del archivo:</font></th></tr><tr bgcolor="#009900"><td align=right><font face="arial, helvetica" size=2 color=white><B>Good:</B></font></td><td align=left><font face="arial, helvetica" size=2 color=white><B>'+good+'</B></font></td></tr><tr bgcolor="#990000"><td align=right><font face="arial, helvetica" size=2 color=white><B>Bad:</B></font></td><td align=left><font face="arial, helvetica" size=2 color=white><B>'+bad+'</B></font></td></tr><tr bgcolor="#000099"><td align=right><font face="arial, helvetica" size=2 color=white><B>Total:</B></font></td><td align=left><font face="arial, helvetica" size=2 color=white><B>'+total+'</B></font></td></tr><tr bgcolor="#009900"><td align=right><font face="arial, helvetica" size=2 color=white><B> &nbsp; </B></font></td><td align=left><font face="arial, helvetica" size=2 color=white><B> &nbsp; </B></font></td></tr><tr bgcolor="#009900"><td align=right><font face="arial, helvetica" size=2 color=white><B>Duplicate:</B></font></td><td align=left><font face="arial, helvetica" size=2 color=white><B>'+dup+'</B></font></td></tr><tr bgcolor="#009900"><td align=right><font face="arial, helvetica" size=2 color=white><B>Postal Match:</B></font></td><td align=left><font face="arial, helvetica" size=2 color=white><B>'+post+'</B></font></td></tr></table><body></html>');
	parent.lead_count.document.close();
}
</script>
</head>
<body>
<form action=<?=$PHP_SELF ?> method=post enctype="multipart/form-data">
<table align=center width="500" border=0 cellpadding=5 cellspacing=0 bgcolor=#D9E6FE>
  <tr>
	<td align=right width="25%"><font face="arial, helvetica" size=2>Cargar Leads de este archivo:</font></td>
	<td align=left width="75%"><input type=file name="leadfile" value="<?=$leadfile ?>"> <? echo "$NWB#vicidial_list_loader$NWE"; ?></td>
  </tr>
  <tr>
	<td align=right width="25%"><font face="arial, helvetica" size=2>ID De la Lista Override: </font></td>
	<td align=left width="75%"><font face="arial, helvetica" size=1><input type=text value="" name='list_id_override' size=10 maxlength=8> (Solamente números or leave blank for values in the file)</td>
  </tr>
  <tr>
	<td align=right width="25%"><font face="arial, helvetica" size=2>Cheque Del Duplicado Del Plomo:</font></td>
	<td align=left width="75%"><font face="arial, helvetica" size=1><select size=1 name=dupcheck><option selected value="NONE">NO DUPLICATE CHECK</option><option value="DUP">CHECK FOR DUPLICATES BY PHONE IN ID DE LA LISTA</option></select></td>
  </tr>
  <tr>
	<td align=right width="25%"><font face="arial, helvetica" size=2>Operaciones de búsqueda De la Zona De Tiempo De Plomo:</font></td>
	<td align=left width="75%"><font face="arial, helvetica" size=1><select size=1 name=postalgmt><option selected value="AREA">COUNTRY CODE AND AREA CODE ONLY</option><option value="POSTAL">POSTAL CODE FIRST</option></select></td>
  </tr>
  <tr>
	<td align=center><input type=submit value="ENVIAR" name='submit_file'></td>
	<td align=center><input type=button onClick="javascript:document.location='listloader.php'" value="RECARGAR" name='reload_page'></td>
  </tr>
  <tr><td colspan=2><font size=1><a href="new_listloader_superL.php" target="_parent">CHASQUE AQUÍ PARA IR AL CARGADOR ESTUPENDO DEL PLOMO (BETA VERSIÓN)</a> &nbsp; &nbsp; <a href="admin.php" target="_parent">DE NUEVO AL ADMIN</a></font></td></tr>
</table>
</form>
<?

#echo "|$LF_orig|$LF_path|\n";

if ($leadfile and filesize($LF_path)<=8388608) {
	print "<script language='JavaScript1.2'>document.forms[0].leadfile.disabled=true; document.forms[0].submit_file.disabled=true; document.forms[0].reload_page.disabled=true;</script>";
	flush();
	if ($WeBRooTWritablE > 0)
		{
		copy($LF_path, "$WeBServeRRooT/vicidial/vicidial_temp_file.txt");
		$lead_file = "./vicidial_temp_file.txt";
		}
	else
		{
		$lead_file = "$LF_path";
		}
	$file=fopen("$lead_file", "r");
	if ($WeBRooTWritablE > 0)
		{$stmt_file=fopen("$WeBServeRRooT/vicidial/listloader_stmts.txt", "w");}
	$pulldate=date("Y-m-d H:i:s");

	$buffer=fgets($file, 4096);
	$tab_count=substr_count($buffer, "\t");
	$pipe_count=substr_count($buffer, "|");

	if ($tab_count>$pipe_count) {$delimiter="\t";  $delim_name="tab";} else {$delimiter="|";  $delim_name="pipe";}
	$field_check=explode($delimiter, $buffer);

	if (count($field_check)>=5) {
		flush();
		$file=fopen("$lead_file", "r");
		$total=0; $good=0; $bad=0; $dup=0; $post=0; $phone_list='';
		print "<center><font face='arial, helvetica' size=3 color='#009900'><B>Procesando$delim_name-delimited file... ($tab_count|$pipe_count)\n";

	if (strlen($list_id_override)>0) 
		{
		print "<BR><BR>ID DE LA LISTA OVERRIDE FOR THIS FILE: $list_id_override<BR><BR>";
		}
		
		while (!feof($file)) {
			$record++;
			$buffer=rtrim(fgets($file, 4096));
			$buffer=stripslashes($buffer);

			if (strlen($buffer)>0) {
				$row=explode($delimiter, eregi_replace("[\'\"]", "", $buffer));
				$vendor_lead_code =		$row[0];
				$source_code =			$row[1];
				$source_id=$source_code;
				$list_id =				$row[2];
				$phone_code =			eregi_replace("[^0-9]", "", $row[3]);
				$phone_number =			eregi_replace("[^0-9]", "", $row[4]);
					$USarea = 			substr($phone_number, 0, 3);
				$title =				$row[5];
				$first_name =			$row[6];
				$middle_initial =		$row[7];
				$last_name =			$row[8];
				$address1 =				$row[9];
				$address2 =				$row[10];
				$address3 =				$row[11];
				$city =					$row[12];
				$state =				$row[13];
				$province =				$row[14];
				$postal_code =			$row[15];
				$country =				$row[16];
				$gender =				$row[17];
				$date_of_birth =		$row[18];
				$alt_phone =			eregi_replace("[^0-9]", "", $row[19]);
				$email =				$row[20];
				$security_phrase =		$row[21];
				$comments =				trim($row[22]);

				if (strlen($list_id_override)>0) 
					{
				#	print "<BR><BR>ID DE LA LISTA OVERRIDE FOR THIS FILE: $list_id_override<BR><BR>";
					$list_id = $list_id_override;
					}

				##### Check for duplicate phone numbers in vicidial_list table #####
				if (eregi("DUP",$dupcheck))
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

				if ( (strlen($phone_number)>6) and ($dup_lead<1) )
					{
					$US='_';
					$entry_date =			"$pulldate";
					$modify_date =			"";
					$status =				"NEW";
					$user =					"";
					$gmt_offset =			'0';
					$called_since_last_reset='N';
					$phone_list .= "$phone_number$US$list_id|";

					if (strlen($phone_code)<1) {$phone_code = '1';}

					$postalgmt_found=0;
					if ( (eregi("POSTAL",$postalgmt)) && (strlen($postal_code)>4) )
						{
						if (preg_match('/^1$/', $phone_code))
							{
							$stmt="select * from vicidial_postal_codes where country_code='$phone_code' and postal_code LIKE \"$postal_code%\";";
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
							$stmt="select * from vicidial_phone_codes where country_code='$phone_code' and areacode='$USarea';";
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
							$stmt="select * from vicidial_phone_codes where country_code='$phone_code' and areacode='$USarea';";
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
							$stmt="select * from vicidial_phone_codes where country_code='$phone_code' and state='$state';";
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
							$stmt="select * from vicidial_phone_codes where country_code='$phone_code';";
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
						if ($DBX) {print "     Second Sunday March to First Sunday November\n";}
						#**********************************************************************
						# SSM-FSN
						#     This is returns 1 if Daylight Savings Time is in effect and 0 if 
						#       Standard time is in effect.
						#     Based on Second Sunday March to First Sunday November at 2 am.
						#     INPUTS:
						#       mm              INTEGER       Month.
						#       dd              INTEGER       Day of the month.
						#       ns              INTEGER       Seconds into the day.
						#       dow             INTEGER       Day of week (0=Sunday, to 6=Saturday)
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
						if ($DBX) {print "     First Sunday April to Last Sunday October\n";}
						#**********************************************************************
						# FSA-LSO
						#     This is returns 1 if Daylight Savings Time is in effect and 0 if 
						#       Standard time is in effect.
						#     Based on first Sunday in April and last Sunday in October at 2 am.
						#**********************************************************************
							
							$NA_DST=0;
							$mm = $mon;
							$dd = $mday;
							$ns = $dsec;
							$dow= $wday;

							if ($mm < 4 || $mm > 10) {
							$NA_DST=0;
							} elseif ($mm >= 5 and $mm <= 9) {
							$NA_DST=1;
							} elseif ($mm == 4) {
							if ($dd > 7) {
								$NA_DST=1;
							} elseif ($dd >= ($dow+1)) {
								if ($timezone) {
								if ($dow == 0 and $ns < (7200+$timezone*3600)) {
									$NA_DST=0;
								} else {
									$NA_DST=1;
								}
								} else {
								if ($dow == 0 and $ns < 7200) {
									$NA_DST=0;
								} else {
									$NA_DST=1;
								}
								}
							} else {
								$NA_DST=0;
							}
							} elseif ($mm == 10) {
							if ($dd < 25) {
								$NA_DST=1;
							} elseif ($dd < ($dow+25)) {
								$NA_DST=1;
							} elseif ($dow == 0) {
								if ($timezone) { # UTC calculations
								if ($ns < (7200+($timezone-1)*3600)) {
									$NA_DST=1;
								} else {
									$NA_DST=0;
								}
								} else { # tiempo local calculations
								if ($ns < 7200) {
									$NA_DST=1;
								} else {
									$NA_DST=0;
								}
								}
							} else {
								$NA_DST=0;
							}
							} # end of month checks

						if ($DBX) {print "     DST: $NA_DST\n";}
						if ($NA_DST) {$gmt_offset++;}
						$AC_processed++;
						}

					if ( (!$AC_processed) and ($dst_range == 'LSM-LSO') )
						{
						if ($DBX) {print "     Last Sunday March to Last Sunday October\n";}
						#**********************************************************************
						#     This is s 1 if Daylight Savings Time is in effect and 0 if 
						#       Standard time is in effect.
						#     Based on last Sunday in March and last Sunday in October at 1 am.
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
						if ($DBX) {print "     Last Sunday October to Last Sunday March\n";}
						#**********************************************************************
						#     This is s 1 if Daylight Savings Time is in effect and 0 if 
						#       Standard time is in effect.
						#     Based on last Sunday in October and last Sunday in March at 1 am.
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
						if ($DBX) {print "     First Sunday October to Last Sunday March\n";}
						#**********************************************************************
						#   TASMANIA ONLY
						#     This is s 1 if Daylight Savings Time is in effect and 0 if 
						#       Standard time is in effect.
						#     Based on first Sunday in October and last Sunday in March at 1 am.
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
					if ( (!$AC_processed) and ($dst_range == 'FSO-TSM') )
						{
						if ($DBX) {print "     First Sunday October to Third Sunday March\n";}
						#**********************************************************************
						#     This is s 1 if Daylight Savings Time is in effect and 0 if 
						#       Standard time is in effect.
						#     Based on first Sunday in October and third Sunday in March at 1 am.
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

					if ( (!$AC_processed) and ($dst_range == 'TSO-LSF') )
						{
						if ($DBX) {print "     Third Sunday October to Last Sunday February\n";}
						#**********************************************************************
						# TSO-LSF
						#     This is returns 1 if Daylight Savings Time is in effect and 0 if 
						#       Standard time is in effect. Brazil
						#     Based on Third Sunday October to Last Sunday February at 1 am.
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

					if ($multi_insert_counter > 8) {
						### insert good deal into pending_transactions table ###
						$stmtZ = "INSERT INTO vicidial_list values$multistmt('','$entry_date','$modify_date','$status','$user','$vendor_lead_code','$source_id','$list_id','$gmt_offset','$called_since_last_reset','$phone_code','$phone_number','$title','$first_name','$middle_initial','$last_name','$address1','$address2','$address3','$city','$state','$province','$postal_code','$country','$gender','$date_of_birth','$alt_phone','$email','$security_phrase','$comments',0);";
						$rslt=mysql_query($stmtZ, $link);
							if ($WeBRooTWritablE > 0)
								{fwrite($stmt_file, $stmtZ."\r\n");}
						$multistmt='';
						$multi_insert_counter=0;

					} else {
						$multistmt .= "('','$entry_date','$modify_date','$status','$user','$vendor_lead_code','$source_id','$list_id','$gmt_offset','$called_since_last_reset','$phone_code','$phone_number','$title','$first_name','$middle_initial','$last_name','$address1','$address2','$address3','$city','$state','$province','$postal_code','$country','$gender','$date_of_birth','$alt_phone','$email','$security_phrase','$comments',0),";
						$multi_insert_counter++;
					}

					$good++;
				} 
			else 
				{
					if ($bad < 10) {print "<BR></b><font size=1 color=red>record $total BAD- PHONE: $phone_number ROW: |$row[0]| DUP: $dup_lead</font><b>\n";}
					$bad++;
				}
				$total++;
			if ($total%100==0) 
				{
					print "<script language='JavaScript1.2'>ShowProgress($good, $bad, $total, $dup, $post)</script>";
					usleep(1000);
					flush();
				}
			}
		}
		if ($multi_insert_counter!=0) {
			$stmtZ = "INSERT INTO vicidial_list values".substr($multistmt, 0, -1).";";
			mysql_query($stmtZ, $link);
			if ($WeBRooTWritablE > 0)
				{fwrite($stmt_file, $stmtZ."\r\n");}
		}

		print "<BR><BR>Done</B> GOOD: $good &nbsp; &nbsp; &nbsp; BAD: $bad &nbsp; &nbsp; &nbsp; TOTAL: $total<BR>DUPLICATE: $dup &nbsp; &nbsp; &nbsp; POSTAL MATCH: $post</font></center>";

	} else {
		print "<center><font face='arial, helvetica' size=3 color='#990000'><B>ERROR: El archivo no tiene el número requerido de los campos para procesarlo.</B></font></center>";
	}
	print "<script language='JavaScript1.2'>document.forms[0].leadfile.disabled=false; document.forms[0].submit_file.disabled=false; document.forms[0].reload_page.disabled=false;</script>";
} else if (filesize($leadfile)>8388608) {
		print "<center><font face='arial, helvetica' size=3 color='#990000'><B>ERROR: El archivo excede el límite 8MB.</B></font></center>";
}
?>
</body>
</html>
