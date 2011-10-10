<?php 
# AST_IVRfilter.php
# 
# Copyright (C) 2009  Matt Florell <vicidial@gmail.com>    LICENSE: AGPLv2
#
# CHANGES
#
# 81030-0432 - First build
# 90310-2054 - Admin header
# 90508-0644 - Changed to PHP long tags
#

require("dbconnect.php");

$PHP_AUTH_USER=$_SERVER['PHP_AUTH_USER'];
$PHP_AUTH_PW=$_SERVER['PHP_AUTH_PW'];
$PHP_SELF=$_SERVER['PHP_SELF'];
if (isset($_GET["query_date"]))				{$query_date=$_GET["query_date"];}
	elseif (isset($_POST["query_date"]))	{$query_date=$_POST["query_date"];}
if (isset($_GET["end_date"]))			{$end_date=$_GET["end_date"];}
	elseif (isset($_POST["end_date"]))	{$end_date=$_POST["end_date"];}
if (isset($_GET["submit"]))				{$submit=$_GET["submit"];}
	elseif (isset($_POST["submit"]))	{$submit=$_POST["submit"];}
if (isset($_GET["ΕΠΙΒΕΒΑΙΩΣΗ"]))				{$ΕΠΙΒΕΒΑΙΩΣΗ=$_GET["ΕΠΙΒΕΒΑΙΩΣΗ"];}
	elseif (isset($_POST["ΕΠΙΒΕΒΑΙΩΣΗ"]))	{$ΕΠΙΒΕΒΑΙΩΣΗ=$_POST["ΕΠΙΒΕΒΑΙΩΣΗ"];}
if (isset($_GET["DB"]))					{$DB=$_GET["DB"];}
	elseif (isset($_POST["DB"]))		{$DB=$_POST["DB"];}

$PHP_AUTH_USER = ereg_replace("[^0-9a-zA-Z]","",$PHP_AUTH_USER);
$PHP_AUTH_PW = ereg_replace("[^0-9a-zA-Z]","",$PHP_AUTH_PW);

#############################################
##### START SYSTEM_SETTINGS LOOKUP #####
$stmt = "SELECT use_non_latin FROM system_settings;";
$rslt=mysql_query($stmt, $link);
if ($DB) {echo "$stmt\n";}
$qm_conf_ct = mysql_num_rows($rslt);
$i=0;
while ($i < $qm_conf_ct)
	{
	$row=mysql_fetch_row($rslt);
	$non_latin =					$row[0];
	$i++;
	}
##### END SETTINGS LOOKUP #####
###########################################

$stmt="SELECT count(*) from vicidial_users where user='$PHP_AUTH_USER' and pass='$PHP_AUTH_PW' and user_level >= 7 and view_reports='1';";
if ($DB) {echo "|$stmt|\n";}
if ($non_latin > 0) {$rslt=mysql_query("SET NAMES 'UTF8'");}
$rslt=mysql_query($stmt, $link);
$row=mysql_fetch_row($rslt);
$auth=$row[0];


  if( (strlen($PHP_AUTH_USER)<2) or (strlen($PHP_AUTH_PW)<2) or (!$auth))
	{
    Header("WWW-Authenticate: Basic realm=\"VICI-PROJECTS\"");
    Header("HTTP/1.0 401 Unauthorized");
    echo "Ακυρο Ονομα Χρήστη/Κωδικός Πρόσβασης: |$PHP_AUTH_USER|$PHP_AUTH_PW|\n";
    exit;
	}

$NOW_DATE = date("Y-m-d");
$NOW_TIME = date("Y-m-d H:i:s");
$STARTtime = date("U");
if (!isset($query_date)) {$query_date = $NOW_DATE;}
if (!isset($end_date)) {$end_date = $NOW_DATE;}

$i=0;

?>

<HTML>
<HEAD>
<STYLE type="text/css">
<!--
   .green {color: white; background-color: green}
   .red {color: white; background-color: red}
   .blue {color: white; background-color: blue}
   .purple {color: white; background-color: purple}
-->
 </STYLE>

<?php 
echo "<META HTTP-EQUIV=\"Content-Type\" CONTENT=\"text/html; charset=utf-8\">\n";
echo "<TITLE>VICIDIAL: VDL IVR Filter Stats</TITLE></HEAD><BODY BGCOLOR=WHITE marginheight=0 marginwidth=0 leftmargin=0 topmargin=0>\n";

	$short_header=1;

	require("admin_header.php");

echo "<TABLE CELLPADDING=4 CELLSPACING=0><TR><TD>";

echo "<FORM ACTION=\"$PHP_SELF\" METHOD=GET>\n";
echo "<TABLE Border=0><TR><TD VALIGN=TOP>\n";
echo "<INPUT TYPE=HIDDEN NAME=DB VALUE=\"$DB\">\n";
echo "Χρονικό διάστημα:<BR>\n";
echo "<INPUT TYPE=TEXT NAME=query_date SIZE=10 MAXLENGTH=10 VALUE=\"$query_date\">\n";
echo " to <INPUT TYPE=TEXT NAME=end_date SIZE=10 MAXLENGTH=10 VALUE=\"$end_date\">\n";
echo "</TD><TD ROWSPAN=2 VALIGN=TOP>\n";
echo "</TD><TD ROWSPAN=2 VALIGN=TOP>\n";
echo "</TD><TD ROWSPAN=2 VALIGN=TOP>\n";
echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; ";
echo "<a href=\"./admin.php?ADD=1000&group_id=$group[0]\">ADMIN</a> | ";
echo "<a href=\"./admin.php?ADD=999999\">ΑΝΑΦΟΡΕΣ</a>\n";
echo "</FONT>\n";

echo "</TD></TR>\n";
echo "<TR><TD>\n";

echo " &nbsp; <INPUT TYPE=submit NAME=ΕΠΙΒΕΒΑΙΩΣΗ VALUE=ΥΠΟΒΑΛΛΩ>\n";
echo "</TD></TR></TABLE>\n";
echo "</FORM>\n\n";

echo "<PRE><FONT SIZE=2>\n\n";

$shift = 'ALL';

if ($shift == 'AM') 
	{
	$time_BEGIN=$AM_shift_BEGIN;
	$time_END=$AM_shift_END;
	if (strlen($time_BEGIN) < 6) {$time_BEGIN = "03:45:00";}   
	if (strlen($time_END) < 6) {$time_END = "15:15:00";}
	}
if ($shift == 'PM') 
	{
	$time_BEGIN=$PM_shift_BEGIN;
	$time_END=$PM_shift_END;
	if (strlen($time_BEGIN) < 6) {$time_BEGIN = "15:15:00";}
	if (strlen($time_END) < 6) {$time_END = "23:15:00";}
	}
if ($shift == 'ALL') 
	{
	if (strlen($time_BEGIN) < 6) {$time_BEGIN = "00:00:00";}
	if (strlen($time_END) < 6) {$time_END = "23:59:59";}
	}
$query_date_BEGIN = "$query_date $time_BEGIN";   
$query_date_END = "$end_date $time_END";



echo "VDL: IVR Filter Stats:           $NOW_TIME\n";


echo "\n";
echo "+----------------------+---------+---------+---------+\n";
echo "| ΚΑΤΗΓΟΡΙΑ             | CALLS   | UNIQUE  |      %  |\n";
echo "+----------------------+---------+---------+---------+\n";


$stmtA="select count(*),count(distinct caller_id) from live_inbound_log where start_time >= '$query_date_BEGIN' and start_time <= '$query_date_END' and comment_a='Εισερχόμενα_IVR_FILTER' and comment_b='CLEAN';";
$rsltA=mysql_query($stmtA, $link);
if ($DB) {echo "$stmtA\n";}
$rowA=mysql_fetch_row($rsltA);

$stmtB="select count(*),count(distinct caller_id) from live_inbound_log where start_time >= '$query_date_BEGIN' and start_time <= '$query_date_END' and comment_a='Εισερχόμενα_IVR_FILTER' and comment_b='NOT_FOUND';";
$rsltB=mysql_query($stmtB, $link);
if ($DB) {echo "$stmtB\n";}
$rowB=mysql_fetch_row($rsltB);

$stmtC="select count(*),count(distinct caller_id) from live_inbound_log where start_time >= '$query_date_BEGIN' and start_time <= '$query_date_END' and comment_a='Εισερχόμενα_IVR_FILTER' and comment_b='EXISTING' and comment_c='DNC';";
$rsltC=mysql_query($stmtC, $link);
if ($DB) {echo "$stmtC\n";}
$rowC=mysql_fetch_row($rsltC);

$stmtD="select count(*),count(distinct caller_id) from live_inbound_log where start_time >= '$query_date_BEGIN' and start_time <= '$query_date_END' and comment_a='Εισερχόμενα_IVR_FILTER' and comment_b='EXISTING' and comment_c='SALE';";
$rsltD=mysql_query($stmtD, $link);
if ($DB) {echo "$stmtD\n";}
$rowD=mysql_fetch_row($rsltD);

$stmtE="select count(*),count(distinct caller_id) from live_inbound_log where start_time >= '$query_date_BEGIN' and start_time <= '$query_date_END' and comment_a='Εισερχόμενα_IVR_FILTER' and comment_b='EXISTING' and comment_c='ARCHIVE';";
$rsltE=mysql_query($stmtE, $link);
if ($DB) {echo "$stmtE\n";}
$rowE=mysql_fetch_row($rsltE);


$total = ($rowA[0] + $rowB[0] + $rowC[0] + $rowD[0] + $rowE[0]);
$Utotal = ($rowA[1] + $rowB[1] + $rowC[1] + $rowD[1] + $rowE[1]);

$agent =	sprintf("%7s", $rowA[0]);
$Uagent =	sprintf("%7s", $rowA[1]);
if ( ($Utotal < 1) or ($rowA[1] < 1) )
	{$UagentPERCENT = '0';}
else
	{$UagentPERCENT = (($rowA[1] / $Utotal) * 100);   $UagentPERCENT = round($UagentPERCENT, 2);}
$UagentPERCENT =	sprintf("%6s", $UagentPERCENT);

$ntfnd =	sprintf("%7s", $rowB[0]);
$Untfnd =	sprintf("%7s", $rowB[1]);
if ( ($Utotal < 1) or ($rowB[1] < 1) )
	{$UntfndPERCENT = '0';}
else
	{$UntfndPERCENT = (($rowB[1] / $Utotal) * 100);   $UntfndPERCENT = round($UntfndPERCENT, 2);}
$UntfndPERCENT =	sprintf("%6s", $UntfndPERCENT);

$prdnc =	sprintf("%7s", $rowC[0]);
$Uprdnc =	sprintf("%7s", $rowC[1]);
if ( ($Utotal < 1) or ($rowC[1] < 1) )
	{$UprdncPERCENT = '0';}
else
	{$UprdncPERCENT = (($rowC[1] / $Utotal) * 100);   $UprdncPERCENT = round($UprdncPERCENT, 2);}
$UprdncPERCENT =	sprintf("%6s", $UprdncPERCENT);

$psale =	sprintf("%7s", $rowD[0]);
$Upsale =	sprintf("%7s", $rowD[1]);
if ( ($Utotal < 1) or ($rowD[1] < 1) )
	{$UpsalePERCENT = '0';}
else
	{$UpsalePERCENT = (($rowD[1] / $Utotal) * 100);   $UpsalePERCENT = round($UpsalePERCENT, 2);}
$UpsalePERCENT =	sprintf("%6s", $UpsalePERCENT);

$archv =	sprintf("%7s", $rowE[0]);
$Uarchv =	sprintf("%7s", $rowE[1]);
if ( ($Utotal < 1) or ($rowE[1] < 1) )
	{$UarchvPERCENT = '0';}
else
	{$UarchvPERCENT = (($rowE[1] / $Utotal) * 100);   $UarchvPERCENT = round($UarchvPERCENT, 2);}
$UarchvPERCENT =	sprintf("%6s", $UarchvPERCENT);

$total =	sprintf("%7s", $total);
$Utotal =	sprintf("%7s", $Utotal);


echo "| CALL SENT TO ΧΕΙΡΙΣΤΗΣ  | $agent | $Uagent | $UagentPERCENT% |\n";
echo "| CALLERID NOT FOUND   | $ntfnd | $Untfnd | $UntfndPERCENT% |\n";
echo "| PREVIOUS DNC         | $prdnc | $Uprdnc | $UprdncPERCENT% |\n";
echo "| PREVIOUS SALE        | $psale | $Upsale | $UpsalePERCENT% |\n";
echo "| ARCHIVE ONLY         | $archv | $Uarchv | $UarchvPERCENT% |\n";
echo "+----------------------+---------+---------+---------+\n";
echo "|               TOTALS:| $total | $Utotal |\n";
echo "+----------------------+---------+---------+\n";




$ENDtime = date("U");
$RUNtime = ($ENDtime - $STARTtime);
echo "\nRun Time: $RUNtime seconds\n";




?>
</PRE>
</TD></TR></TABLE>

</BODY></HTML>
