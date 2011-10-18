
<style type="text/css">
<!--
	div.scroll_calllog {height: <?php echo $CQheight ?>px; width: <?php echo $MNwidth ?>px; overflow: scroll;}
	div.scroll_callback {height: 300px; width: <?php echo $MNwidth ?>px; overflow: scroll;}
	div.scroll_script {height: <?php echo $SSheight ?>px; width: <?php echo $SDwidth ?>px; background: #FFF5EC; overflow: auto; font-size: 12px;  font-family: sans-serif;}
	div.noscroll_script {height: <?php echo $SSheight ?>px; width: <?php echo $SDwidth ?>px; background: #FFF5EC; overflow: hidden; font-size: 12px;  font-family: sans-serif;}
-->
</style>
<?php
echo "</head>\n";

$zi=2;

?>
<body onload="begin_all_refresh();"  onunload="BrowserCloseLogout();">

<form name=vicidial_form id=vicidial_form onsubmit="return false;">

<span style="position:absolute;left:0px;top:0px;z-index:300;" id="LoadingBox">
    <table border="0" bgcolor="white" width="<?php echo $JS_browser_width ?>px" height="<?php echo $JS_browser_height ?>px"><tr><td align="left" valign="top">
 <br />
 <br />
 <br />
 <br />
 <br />
 <br />
    &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; <img src="./images/loading.gif" height="90px" width="545px" alt="Loading" />
 <br />
 <br />
    </td></tr></table>
</span>


<span style="position:absolute;left:0px;top:0px;z-index:<?php $zi++; echo $zi ?>;" id="Header">
    <table border="0" cellpadding="0" cellspacing="0" bgcolor="white" width="<?php echo $MNwidth ?>px" marginwidth="0" marginheight="0" leftmargin="0" topmargin="0" valign="top" align="left">
    <tr valign="top" align="left"><td colspan="3" valign="top" align="left">
    <input type="hidden" name="extension" id="extension" />
    <input type="hidden" name="custom_field_values" id="custom_field_values" value="" />
    <input type="hidden" name="FORM_LOADED" id="FORM_LOADED" value="0" />
	<font class="queue_text">
	<a href="#" onclick="start_all_refresh();"><font class="queue_text">Logged in as User</font></a>
	<?php 
	echo ": $VD_login on Phone: $SIP_user"; 
	if ($on_hook_agent == 'Y')
		{echo "(<a href=\"#\" onclick=\"NoneInSessionCalL();return false;\">ring</a>)";}
	echo "&nbsp; to campaign: $VD_campaign&nbsp; \n"; 
	?>
	 &nbsp; &nbsp; <span id="agentchannelSPAN"></span></font>
    </td><td colspan="3" valign="top" align="right"><font class="body_text">
	<?php if ($territoryCT > 0) {echo "<a href=\"#\" onclick=\"OpeNTerritorYSelectioN();return false;\">TERRITORIES</a> &nbsp; &nbsp; \n";} ?>
	<?php if ($INgrpCT > 0) {echo "<a href=\"#\" onclick=\"OpeNGrouPSelectioN();return false;\">GROUPS</a> &nbsp; &nbsp; \n";} ?>
	<?php	echo "<a href=\"#\" onclick=\"NormalLogout();return false;\">LOGOUT</a>\n"; ?>
    </font></td></tr>
    </table>
</span>

<span style="position:absolute;left:0px;top:13px;z-index:<?php $zi++; echo $zi ?>;" id="Tabs">
    <table border="0" bgcolor="#FFFFFF" width="<?php echo $MNwidth ?>px" height="30px">
    <tr valign="top" align="left">
    <td align="left" width="115px"><a href="#" onclick="MainPanelToFront('NO');"><img src="./images/vdc_tab_vicidial.gif" alt="MAIN" width="115px" height="30px" border="0" /></a></td>
    <td align="left" width="90px"><a href="#" onclick="ScriptPanelToFront();"><img src="./images/vdc_tab_script.gif" alt="SCRIPT" width="90px" height="30px" border="0" /></a></td>
	<?php if ($custom_fields_enabled > 0)
    {echo "<td align=\"left\" width=\"67px\"><a href=\"#\" onclick=\"FormPanelToFront();\"><img src=\"./images/vdc_tab_form.gif\" alt=\"FORM\" width=\"67px\" height=\"30px\" border=\"0\" /></a></td>\n";}
	?>
    <td width="<?php echo $HSwidth ?>px" valign="middle" align="center"><font class="body_text">&nbsp; <span id="status">LIVE</span>&nbsp; &nbsp;session ID: <span id="sessionIDspan"></span>&nbsp; &nbsp;<span id="AgentStatusCalls"></span></font></td>
    <td width="109px"><img src="./images/agc_live_call_OFF.gif" name="livecall" alt="Live Call" width="109px" height="30px" border="0" /></td>
    </tr>
 </table>
</span>

<span style="position:absolute;left:0px;top:0px;z-index:<?php $zi++; echo $zi ?>;" id="WelcomeBoxA">
    <table border="0" bgcolor="#FFFFFF" width="<?php echo $CAwidth ?>px" height="<?php echo $HKwidth ?>px"><tr><td align="center"><br /><span id="WelcomeBoxAt">Agent Screen</span></td></tr></table>
</span>


<!-- BEGIN *********   Here is the main VICIDIAL display panel -->
<span style="position:absolute;left:0px;top:46px;z-index:<?php $zi++; echo $zi ?>;" id="MainPanel">
    <table border="0" bgcolor="<?php echo $MAIN_COLOR ?>" width="<?php echo $MNwidth ?>px" id="MainTable">
    <tr><td colspan="3">
	<?php
	if ($webphone_location == 'bar')
		{
        echo "<img src=\"images/pixel.gif\" width=\"1px\" height=\"".$webphone_height."px\" /><br />\n";
		}
	?>	
	<span id="post_phone_time_diff_span"><b><font color="red"><span id="post_phone_time_diff_span_contents"></span></font></b></span>
    <font class="body_text"> STATUS: <span id="MainStatuSSpan"></span></font></td></tr>
    <tr><td colspan="3"><span id="busycallsdebug"></span></td></tr>
    <tr><td width="150px" align="left" valign="top">
	<font class="body_text"><center>
    <span style="background-color: #CCFFCC" id="DiaLControl"><a href="#" onclick="ManualDialNext('','','','','','0');"><img src="./images/vdc_LB_dialnextnumber_OFF.gif" border="0" alt="Dial Next Number" /></a></span><br />
	<span id="ManualQueueNotice"></span>
	<span id="ManualQueueChoice"></span>
    <span id="DiaLLeaDPrevieW"><font class="preview_text"> <input type="checkbox" name="LeadPreview" size="1" value="0" /> LEAD PREVIEW<br /></font></span>
    <span id="DiaLDiaLAltPhonE"><font class="preview_text"> <input type="checkbox" name="DiaLAltPhonE" size="1" value="0" /> ALT PHONE DIAL<br /></font></span>
	<!--
	<?php
	if ( ($manual_dial_preview) and ($auto_dial_level==0) )
        {echo "<font class=\"preview_text\"> <input type=\"checkbox\" name=\"LeadPreview\" size=\"1\" value=\"0\" /> LEAD PREVIEW<br /></font>";}
	if ( ($alt_phone_dialing) and ($auto_dial_level==0) )
        {echo "<font class=\"preview_text\"> <input type=\"checkbox\" name=\"DiaLAltPhonE\" size=\"1\" value=\"0\" /> ALT PHONE DIAL<br /></font>";}
	?> -->
    RECORDING FILE:<br />
	</center>
    <font class="body_tiny"><span id="RecorDingFilename"></span></font><br />
    RECORD ID: <font class="body_small"><span id="RecorDID"></span></font><br />
	<center>
	<!-- <a href=\"#\" onclick=\"conf_send_recording('MonitorConf','" + head_conf + "','');return false;\">Record</a> -->
    <span style="background-color: <?php echo $MAIN_COLOR ?>" id="RecorDControl"><a href="#" onclick="conf_send_recording('MonitorConf',session_id,'');return false;"><img src="./images/vdc_LB_startrecording.gif" border="0" alt="Start Recording" /></a></span><br />
    <span id="SpacerSpanA"><img src="./images/blank.gif" width="145px" height="16px" border="0" /></span><br />
    <span style="background-color: #FFFFFF" id="WebFormSpan"><img src="./images/vdc_LB_webform_OFF.gif" border="0" alt="Web Form" /></span><br />
	<?php
	if ($enable_second_webform > 0)
        {echo "<span style=\"background-color: #FFFFFF\" id=\"WebFormSpanTwo\"><img src=\"./images/vdc_LB_webform_two_OFF.gif\" border=\"0\" alt=\"Web Form 2\" /></span><br />\n";}
	?>
    <font class="body_small_bold"><span id="ParkCounterSpan"> &nbsp; </span></font><br />
    <span style="background-color: <?php echo $MAIN_COLOR ?>" id="ParkControl"><img src="./images/vdc_LB_parkcall_OFF.gif" border="0" alt="Park Call" /></span><br />
	<?php
	if ( ($ivr_park_call=='ENABLED') or ($ivr_park_call=='ENABLED_PARK_ONLY') )
        {echo "<span style=\"background-color: $MAIN_COLOR\" id=\"ivrParkControl\"><img src=\"./images/vdc_LB_ivrparkcall_OFF.gif\" border=\"0\" alt=\"IVR Park Call\" /></span><br />\n";}
	else
		{echo "<span style=\"background-color: $MAIN_COLOR\" id=\"ivrParkControl\"></span>\n";}
	?>
    <span style="background-color: <?php echo $MAIN_COLOR ?>" id="XferControl"><img src="./images/vdc_LB_transferconf_OFF.gif" border="0" alt="Transfer - Conference" /></span><br />

	<?php
	if ($quick_transfer_button_enabled > 0)
        {echo "<span style=\"background-color: $MAIN_COLOR\" id=\"QuickXfer\"><img src=\"./images/vdc_LB_quickxfer_OFF.gif\" border=\"0\" alt=\"Quick Transfer\" /></span><br />\n";}
	if ($custom_3way_button_transfer_enabled > 0)
        {echo "<span style=\"background-color: $MAIN_COLOR\" id=\"CustomXfer\"><img src=\"./images/vdc_LB_customxfer_OFF.gif\" border=\"0\" alt=\"Custom Transfer\" /></span><br />\n";}
	?>

	<span id="ReQueueCall"></span>

	<?php
	if ($call_requeue_button > 0)
        {echo "<br />\n";}
	?>

    <span id="SpacerSpanC"><img src="./images/blank.gif" width="145px" height="16px" border="0" /></span><br />
    <span style="background-color: #FFCCFF" id="HangupControl"><img src="./images/vdc_LB_hangupcustomer_OFF.gif" border="0" alt="Hangup Customer" /></span><br />
    <span id="SpacerSpanD"><img src="./images/blank.gif" width="145px" height="16px" border="0" /></span><br />
    <div class="text_input" id="SendDTMFdiv"><span style="background-color: <?php echo $MAIN_COLOR ?>" id="SendDTMF"><a href="#" onclick="SendConfDTMF(session_id);return false;"><img src="./images/vdc_LB_senddtmf.gif" border="0" alt="Send DTMF" align="bottom" /></a>  <input type="text" size="5" name="conf_dtmf" class="cust_form" value="" maxlength="50" /></div></span><br />
	</center>
	</font>
	</td>
    <td width="<?php echo $SDwidth ?>px" align="left" valign="top">
    <input type="hidden" name="lead_id" id="lead_id" value="" />
    <input type="hidden" name="list_id" id="list_id" value="" />
    <input type="hidden" name="entry_list_id" id="entry_list_id" value="" />
    <input type="hidden" name="called_count" id="called_count" value="" />
    <input type="hidden" name="rank" id="rank" value="" />
    <input type="hidden" name="owner" id="owner" value="" />
    <input type="hidden" name="gmt_offset_now" id="gmt_offset_now" value="" />
    <input type="hidden" name="gender" id="gender" value="" />
    <input type="hidden" name="date_of_birth" id="date_of_birth" value="" />
    <input type="hidden" name="country_code" id="country_code" value="" />
    <input type="hidden" name="uniqueid" id="uniqueid" value="" />
    <input type="hidden" name="callserverip" id="callserverip" value="" />
    <input type="hidden" name="SecondS" id="SecondS" value="" />
	<span class="text_input" id="MainPanelCustInfo">
    <table><tr>
    <td align="right"></td>
    <td align="left"><font class="body_text">&nbsp; Customer Time: <span name="custdatetime" id="custdatetime" class="log_title"> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; </span> &nbsp; &nbsp; Channel: <span name="callchannel" id="callchannel" class="cust_form"> </span></font></td>
	</tr><tr>
    <td colspan="2" align="center"> Customer Information: <span id="CusTInfOSpaN"></span> &nbsp; &nbsp; &nbsp; &nbsp; 
	<?php
	if ($agent_lead_search == 'ENABLED')
		{echo "<font class=\"body_text\"><a href=\"#\" onclick=\"OpeNSearcHForMDisplaYBox();return false;\">LEAD SEARCH</a></font>";}
	?>
	</td>
	</tr><tr>
    <td align="left" colspan="2">

    <table width="550px"><tr>
    <td align="right"><font class="body_text">
	<?php

	if ($label_title == '---HIDE---')
        {echo "</td><td align=\"left\" colspan=\"5\"><input type=\"hidden\" name=\"title\" id=\"title\" value=\"\" />";}
	else
        {echo "$label_title: </td><td align=\"left\" colspan=\"5\"><font class=\"body_text\"><input type=\"text\" size=\"4\" name=\"title\" id=\"title\" maxlength=\"4\" class=\"cust_form\" value=\"\" />";}
	if ($label_first_name == '---HIDE---')
        {echo "&nbsp; <input type=\"hidden\" name=\"first_name\" id=\"first_name\" value=\"\" />";}
	else
        {echo "&nbsp; $label_first_name: <input type=\"text\" size=\"17\" name=\"first_name\" id=\"first_name\" maxlength=\"30\" class=\"cust_form\" value=\"\" />";}
	if ($label_middle_initial == '---HIDE---')
        {echo "&nbsp; <input type=\"hidden\" name=\"middle_initial\" id=\"middle_initial\" value=\"\" />";}
	else
        {echo "&nbsp; $label_middle_initial: <input type=\"text\" size=\"1\" name=\"middle_initial\" id=\"middle_initial\" maxlength=\"1\" class=\"cust_form\" value=\"\" />";}
	if ($label_last_name == '---HIDE---')
        {echo "&nbsp; <input type=\"hidden\" name=\"last_name\" id=\"last_name\" value=\"\" />";}
	else
        {echo "&nbsp; $label_last_name: <input type=\"text\" size=\"23\" name=\"last_name\" id=\"last_name\" maxlength=\"30\" class=\"cust_form\" value=\"\" />";}
	
    echo "</td></tr><tr><td align=\"right\"><font class=\"body_text\">";
	
	if ($label_address1 == '---HIDE---')
        {echo " </td><td align=\"left\" colspan=\"5\"><input type=\"hidden\" name=\"address1\" id=\"address1\" value=\"\" />";}
	else
        {echo "$label_address1: </td><td align=\"left\" colspan=5><font class=\"body_text\"><input type=\"text\" size=\"85\" name=\"address1\" id=\"address1\" maxlength=\"100\" class=\"cust_form\" value=\"\" />";}
	
    echo "</td></tr><tr><td align=\"right\"><font class=\"body_text\">";

	if ($label_address2 == '---HIDE---')
        {echo " </td><td align=\"left\"><input type=\"hidden\" name=\"address2\" id=\"address2\" value=\"\" />";}
	else
        {echo "$label_address2: </td><td align=\"left\"><font class=\"body_text\"><input type=\"text\" size=\"20\" name=\"address2\" id=\"address2\" maxlength=\"100\" class=\"cust_form\" value=\"\" />";}

    echo "</td><td align=\"right\"><font class=\"body_text\">";

	if ($label_address3 == '---HIDE---')
        {echo " </td><td align=\"left\" colspan=\"3\"><input type=\"hidden\" name=\"address3\" id=\"address3\" value=\"\" />";}
	else
        {echo "$label_address3: </td><td align=\"left\" colspan=\"3\"><font class=\"body_text\"><input type=\"text\" size=\"45\" name=\"address3\" id=\"address3\" maxlength=\"100\" class=\"cust_form\" value=\"\" />";}

    echo "</td></tr><tr><td align=\"right\"><font class=\"body_text\">";

	if ($label_city == '---HIDE---')
        {echo " </td><td align=\"left\"><input type=\"hidden\" name=\"city\" id=\"city\" value=\"\" />";}
	else
        {echo "$label_city: </td><td align=\"left\"><font class=\"body_text\"><input type=\"text\" size=\"20\" name=\"city\" id=\"city\" maxlength=\"50\" class=\"cust_form\" value=\"\" />";}

    echo "</td><td align=\"right\"><font class=\"body_text\">";

	if ($label_state == '---HIDE---')
        {echo " </td><td align=\"left\"><input type=\"hidden\" name=\"state\" id=\"state\" value=\"\" />";}
	else
        {echo "$label_state: </td><td align=\"left\"><font class=\"body_text\"><input type=\"text\" size=\"4\" name=\"state\" id=\"state\" maxlength=\"2\" class=\"cust_form\" value=\"\" />";}

    echo "</td><td align=\"right\"><font class=\"body_text\">";

	if ($label_postal_code == '---HIDE---')
        {echo " </td><td align=\"left\"><input type=\"hidden\" name=\"postal_code\" id=\"postal_code\" value=\"\" />";}
	else
        {echo "$label_postal_code: </td><td align=\"left\"><font class=\"body_text\"><input type=\"text\" size=\"14\" name=\"postal_code\" id=\"postal_code\" maxlength=\"10\" class=\"cust_form\" value=\"\" />";}

    echo "</td></tr><tr><td align=\"right\"><font class=\"body_text\">";

	if ($label_province == '---HIDE---')
        {echo " </td><td align=\"left\"><input type=\"hidden\" name=\"province\" id=\"province\" value=\"\" />";}
	else
        {echo "$label_province: </td><td align=\"left\"><font class=\"body_text\"><input type=\"text\" size=\"20\" name=\"province\" id=\"province\" maxlength=\"50\" class=\"cust_form\" value=\"\" />";}

    echo "</td><td align=\"right\"><font class=\"body_text\">";

	if ($label_vendor_lead_code == '---HIDE---')
        {echo " </td><td align=\"left\"><input type=\"hidden\" name=\"vendor_lead_code\" id=\"vendor_lead_code\" value=\"\" />";}
	else
        {echo "$label_vendor_lead_code: </td><td align=\"left\"><font class=\"body_text\"><input type=\"text\" size=\"15\" name=\"vendor_lead_code\" id=\"vendor_lead_code\" maxlength=\"20\" class=\"cust_form\" value=\"\" />";}

    echo "</td><td align=\"right\"><font class=\"body_text\">";

	if ($label_gender == '---HIDE---')
		{
		echo "</td><td align=\"left\"><font class=\"body_text\"><span id=\"GENDERhideFORie\"><input type=\"hidden\" name=\"gender_list\" id=\"gender_list\" value=\"\" /></span>";
		}
	else
        {
		echo "$label_gender: </td><td align=\"left\"><font class=\"body_text\"><span id=\"GENDERhideFORie\"><select size=\"1\" name=\"gender_list\" class=\"cust_form\" id=\"gender_list\"><option value=\"U\">U - Undefined</option><option value=\"M\">M - Male</option><option value=\"F\">F - Female</option></select></span>";
		}

    echo "</td></tr><tr><td align=\"right\"><font class=\"body_text\">";

    echo "$label_phone_number: </td><td align=\"left\"><font class=\"body_text\">";

	if ( (ereg('Y',$disable_alter_custphone)) or (ereg('HIDE',$disable_alter_custphone)) )
		{
        echo "<font class=\"body_text\"><span id=\"phone_numberDISP\"> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; </span></font>";
        echo "<input type=\"hidden\" name=\"phone_number\" id=\"phone_number\" value=\"\" />";
		}
	else
		{
        echo "<input type=\"text\" size=\"20\" name=\"phone_number\" id=\"phone_number\" maxlength=\"16\" class=\"cust_form\" value=\"\" />";
		}

    echo "</td><td align=\"right\"><font class=\"body_text\">";

	if ($label_phone_code == '---HIDE---')
        {echo " </td><td align=\"left\"><input type=\"hidden\" name=\"phone_code\" id=\"phone_code\" value=\"\" />";}
	else
        {echo "$label_phone_code: </td><td align=\"left\"><font class=\"body_text\"><input type=\"text\" size=\"4\" name=\"phone_code\" id=\"phone_code\" maxlength=\"10\" class=\"cust_form\" value=\"\" />";}

    echo "</td><td align=\"right\"><font class=\"body_text\">";

	if ($label_alt_phone == '---HIDE---')
        {echo " </td><td align=\"left\"><input type=\"hidden\" name=\"alt_phone\" id=\"alt_phone\" value=\"\" />";}
	else
        {echo "$label_alt_phone: </td><td align=\"left\"><font class=\"body_text\"><input type=\"text\" size=\"14\" name=\"alt_phone\" id=\"alt_phone\" maxlength=\"16\" class=\"cust_form\" value=\"\" />";}

    echo "</td></tr><tr><td align=\"right\"><font class=\"body_text\">";

	if ($label_security_phrase == '---HIDE---')
        {echo " </td><td align=\"left\"><input type=\"hidden\" name=\"security_phrase\" id=\"security_phrase\" value=\"\" />";}
	else
        {echo "$label_security_phrase: </td><td align=\"left\"><font class=\"body_text\"><input type=\"text\" size=\"20\" name=\"security_phrase\" id=\"security_phrase\" maxlength=\"100\" class=\"cust_form\" value=\"\" />";}

    echo "</td><td align=\"right\"><font class=\"body_text\">";

	if ($label_email == '---HIDE---')
        {echo " </td><td align=\"left\" colspan=\"3\"><input type=\"hidden\" name=\"email\" id=\"email\" value=\"\" />";}
	else
        {echo "$label_email: </td><td align=\"left\" colspan=\"3\"><font class=\"body_text\"><input type=\"text\" size=\"45\" name=\"email\" id=\"email\" maxlength=\"70\" class=\"cust_form\" value=\"\" />";}

    echo "</td></tr><tr><td align=\"right\"><font class=\"body_text\">";

	if ($label_comments == '---HIDE---')
		{
        echo " </td><td align=\"left\" colspan=5><input type=\"hidden\" name=\"comments\" id=\"comments\" value=\"\" />\n";
		}
	else
		{
        echo "$label_comments: </td><td align=\"left\" colspan=\"5\"><font class=\"body_text\">";
		if ( ($multi_line_comments) )
            {echo "<textarea name=\"comments\" id=\"comments\" rows=\"2\" cols=\"85\" class=\"cust_form_text\" value=\"\"></textarea>\n";}
		else
            {echo "<input type=\"text\" size=\"65\" name=\"comments\" id=\"comments\" maxlength=\"255\" class=\"cust_form\" value=\"\" />\n";}
		}

	echo "</font></td></tr><tr><td align=\"right\"><font class=\"body_text\">\n";

	if ($per_call_notes == 'ENABLED')
		{
        echo "Call Notes: ";
		if ($agent_call_log_view == '1')
			{echo "<br /><span id=\"CallNotesButtons\"><a href=\"#\" onclick=\"VieWNotesLoG();return false;\">view notes</a></span> ";}
        echo "</td><td align=\"left\" colspan=\"5\"><font class=\"body_text\">";
		echo "<textarea name=\"call_notes\" id=\"call_notes\" rows=\"2\" cols=\"85\" class=\"cust_form_text\" value=\"\"></textarea>\n";
		}
	else
		{
        echo " </td><td align=\"left\" colspan=5><input type=\"hidden\" name=\"call_notes\" id=\"call_notes\" value=\"\" /><span id=\"CallNotesButtons\"></span>\n";
		}


	?>
	</font>
	</td>

    </tr></table></td>
    </tr></table>
	</span>
	</font>
	</td>
    <td width="1" align="center">
	</td>
	</tr>
    <tr><td align="left" colspan="3" height="<?php echo $BPheight ?>px">
	&nbsp;</td></tr>
    <tr><td align="left" colspan="3">
	&nbsp;</td></tr>
 </table>
	</td></tr>
 </table>
</span>
<!-- END *********   Here is the main VICIDIAL display panel -->

<span style="position:absolute;left:0px;top:<?php echo $DBheight ?>px;z-index:<?php $zi++; echo $zi ?>;" id="debugbottomspan"></span>

<span style="position:absolute;left:300px;top:<?php echo $MBheight ?>px;z-index:<?php $zi++; echo $zi ?>;" id="ManuaLDiaLButtons"><font class="body_text">
<span id="MDstatusSpan"><a href="#" onclick="NeWManuaLDiaLCalL('NO');return false;">MANUAL DIAL</a></span> &nbsp; &nbsp; &nbsp; <a href="#" onclick="NeWManuaLDiaLCalL('FAST');return false;">FAST DIAL</a><br />
</font></span>

<span style="position:absolute;left:500px;top:<?php echo $CBheight ?>px;z-index:<?php $zi++; echo $zi ?>;" id="PauseCodeButtons"><font class="body_text">
<span id="PauseCodeLinkSpan"></span> <br />
</font></span>

<span style="position:absolute;left:500px;top:<?php echo $MBheight ?>px;z-index:<?php $zi++; echo $zi ?>;" id="CallLogButtons"><font class="body_text">
<span id="CallLogLinkSpan"><a href="#" onclick="VieWCalLLoG();return false;">VIEW CALL LOG</a></span> <br />
</font></span>

<span style="position:absolute;left:0px;top:<?php echo $PBheight ?>px;z-index:<?php $zi++; echo $zi ?>;" id="MaiNfooterspan">
<span id="blind_monitor_notice_span"><b><font color="red"> &nbsp; &nbsp; <span id="blind_monitor_notice_span_contents"></span></font></b></span>
    <table bgcolor="<?php echo $MAIN_COLOR ?>" id="MaiNfooter" width="<?php echo $MNwidth ?>px"><tr height="32px"><td height="32px"><font face="Arial,Helvetica" size="1">VERSION: <?php echo $version ?> &nbsp; BUILD: <?php echo $build ?> &nbsp; &nbsp; Server: <?php echo $server_ip ?>  &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;</font><br />
	<font class="body_small">
	<span id="busycallsdisplay"><a href="#"  onclick="conf_channels_detail('SHOW');">Show conference call channel information</a>
    <br /><br />&nbsp;</span></font></td><td align="right" height="32px">
	</td></tr>
    <tr><td colspan="3"><span id="outboundcallsspan"></span></td></tr>
    <tr><td colspan="3"><font class="body_small"><span id="AgentAlertSpan">
	<?php
	if ( (ereg('ON',$VU_alert_enabled)) and ($AgentAlert_allowed > 0) )
		{echo "<a href=\"#\" onclick=\"alert_control('OFF');return false;\">Alert is ON</a>";}
	else
		{echo "<a href=\"#\" onclick=\"alert_control('ON');return false;\">Alert is OFF</a>";}
	?>
	</span></font></td></tr>
    <tr><td colspan="3">
	<font class="body_small">
	</font>
    </td></tr></table>
</span>

<?php if ( ($HK_statuses_camp > 0) && ( ($user_level>=$HKuser_level) or ($VU_hotkeys_active > 0) ) ) { ?>
<span style="position:absolute;left:<?php echo $HKwidth ?>px;top:<?php echo $HKheight ?>px;z-index:<?php $zi++; echo $zi ?>;" id="hotkeysdisplay"><a href="#" onMouseOver="HotKeys('ON')"><img src="./images/vdc_XB_hotkeysactive_OFF.gif" border="0" alt="HOT KEYS INACTIVE" /></a></span>
<?php } ?>

<span style="position:absolute;left:<?php echo $SCwidth ?>px;top:<?php echo $SCheight ?>px;z-index:<?php $zi++; echo $zi ?>;" id="SecondSspan"><font class="body_text"> seconds: 
<span id="SecondSDISP"> &nbsp; &nbsp; </span></font>
</font></span>

<span style="position:absolute;left:5px;top:<?php echo $CBheight ?>px;z-index:<?php $zi++; echo $zi ?>;" id="VolumeControlSpan"><span id="VolumeUpSpan"><img src="./images/vdc_volume_up_off.gif" border="0" /></span><br /><span id="VolumeDownSpan"><img src="./images/vdc_volume_down_off.gif" border="0" /></span>
</font></span>

<span style="position:absolute;left:35px;top:<?php echo $CBheight ?>px;z-index:<?php $zi++; echo $zi ?>;" id="AgentStatusSpan"><font class="body_text">
Your Status: <span id="AgentStatusStatus"></span> <br />Calls Dialing: <span id="AgentStatusDiaLs"></span>
</font></span>

<span style="position:absolute;left:<?php echo $PDwidth ?>px;top:<?php echo $AMheight ?>px;z-index:<?php $zi++; echo $zi ?>;" id="AgentMuteANDPreseTDiaL"><font class="body_text">
	<?php
	if ($PreseT_DiaL_LinKs)
		{
		echo "<a href=\"#\" onclick=\"DtMf_PreSet_a_DiaL();return false;\"><font class=\"body_tiny\">D1 - DIAL</font></a>\n";
        echo " &nbsp; \n";
		echo "<a href=\"#\" onclick=\"DtMf_PreSet_b_DiaL();return false;\"><font class=\"body_tiny\">D2 - DIAL</font></a>\n";
		}
    else {echo "<br />\n";}
	?>
    <br /><br /> &nbsp; <br />
</font></span>

<span style="position:absolute;left:0px;top:<?php echo $CQheight ?>px;width:<?php echo $MNwidth ?>px;overflow:scroll;z-index:<?php $zi++; echo $zi ?>;background-color:<?php echo $SIDEBAR_COLOR ?>;" id="callsinqueuedisplay"><table cellpadding="0" cellspacing="0" border="0"><tr><td width="5px" rowspan="2">&nbsp;</td><td align="center"><font class="body_text">Calls In Queue: &nbsp; </font></td></tr><tr><td align="center"><span id="callsinqueuelist">&nbsp;</span></td></tr></table></span>

<font class="body_small"><span style="position:absolute;left:<?php echo $CLwidth ?>px;top:<?php echo $QLheight ?>px;z-index:<?php $zi++; echo $zi ?>;" id="callsinqueuelink">
<?php 
if ($view_calls_in_queue > 0)
	{ 
	if ($view_calls_in_queue_launch > 0) 
		{echo "<a href=\"#\" onclick=\"show_calls_in_queue('HIDE');\">Hide Calls In Queue</a>\n";}
	else 
		{echo "<a href=\"#\" onclick=\"show_calls_in_queue('SHOW');\">Show Calls In Queue</a>\n";}
	}
?>
</span></font>

<span style="position:absolute;left:300px;top:<?php echo $CBheight ?>px;z-index:<?php $zi++; echo $zi ?>;" id="CallbacksButtons"><font class="body_text">
<span id="CBstatusSpan">X ACTIVE CALLBACKS</span> <br />
</font></span>

<span style="position:absolute;left:<?php echo $SBwidth ?>px;top:<?php echo $AVTheight ?>px;height:500px;overflow:scroll;z-index:<?php $zi++; echo $zi ?>;background-color:<?php echo $SIDEBAR_COLOR ?>;" id="AgentViewSpan"><table cellpadding="0" cellspacing="0" border="0"><tr><td width="5px" rowspan="2">&nbsp;</td><td align="center"><font class="body_text">
Other Agents Status: &nbsp; </font></td></tr><tr><td align="center"><span id="AgentViewStatus">&nbsp;</span></td></tr></table></span>

<?php
$zi++;
if ($webphone_location == 'bar')
	{
	echo "<span style=\"position:absolute;left:0px;top:46px;height:".$webphone_height."px;width=".$webphone_width."px;overflow:hidden;z-index:$zi;background-color:$SIDEBAR_COLOR;\" id=\"webphoneSpan\"><span id=\"webphonecontent\" style=\"overflow:hidden;\">$webphone_content</span></span>\n";
	}
else
	{
    echo "<span style=\"position:absolute;left:" . $SBwidth . "px;top:15px;height:500px;overflow:scroll;z-index:$zi;background-color:$SIDEBAR_COLOR;\" id=\"webphoneSpan\"><table cellpadding=\"$webphone_pad\" cellspacing=\"0\" border=\"0\"><tr><td width=\"5px\" rowspan=\"2\">&nbsp;</td><td align=\"center\"><font class=\"body_text\">
    Web Phone: &nbsp; </font></td></tr><tr><td align=\"center\"><span id=\"webphonecontent\">$webphone_content</span></td></tr></table></span>\n";
	}
?>


<span style="position:absolute;left:<?php echo $SCwidth ?>px;top:<?php echo $SLheight ?>px;z-index:<?php $zi++; echo $zi ?>;" id="AgentViewLinkSpan"><table cellpadding="0" cellspacing="0" border="0" width="91px"><tr><td align="right"><font class="body_small"><span id="AgentViewLink"><a href="#" onclick="AgentsViewOpen('AgentViewSpan','open');return false;">Agents View +</a></span></font></td></tr></table></span>

<?php 
if ($is_webphone=='Y')
	{ 
	?>

    <span style="position:absolute;left:<?php echo $SBwidth ?>px;top:0px;z-index:<?php $zi++; echo $zi ?>;" id="webphoneLinkSpan"><table cellpadding="0" cellspacing="0" border="0" width="120px"><tr><td align="right"><font class="body_small"><span id="webphoneLink"> &nbsp; <a href="#" onclick="webphoneOpen('webphoneSpan','close');return false;">WebPhone View -</a></span></font></td></tr></table></span>

	<?php 
	}
?>

<font class="body_small"><span style="position:absolute;left:200px;top:<?php echo $CBheight ?>px;z-index:<?php $zi++; echo $zi ?>;" id="dialableleadsspan">
<?php 
if ($agent_display_dialable_leads > 0)
	{ 
    echo "Dialable Leads:<br /> &nbsp;\n";
	}
?>
</span></font>

<span style="position:absolute;left:<?php echo $MUwidth ?>px;top:<?php echo $SLheight ?>px;z-index:<?php $zi++; echo $zi ?>;" id="AgentMuteSpan"></span>

<span style="position:absolute;left:154px;top:<?php echo $SFheight ?>px;z-index:<?php $zi++; echo $zi ?>;" id="ScriptPanel">
	<?php
	if ($webphone_location == 'bar')
        {echo "<img src=\"images/pixel.gif\" width=\"1px\" height=\"".$webphone_height."px\" /><br />\n";}
	?>
    <table border="0" bgcolor="<?php echo $SCRIPT_COLOR ?>" width="<?php echo $SSwidth ?>px" height="<?php echo $SSheight ?>px"><tr><td align="left" valign="top"><font class="sb_text"><div class="noscroll_script" id="ScriptContents">AGENT SCRIPT</div></font></td></tr></table>
</span>

<span style="position:absolute;left:<?php echo $AMwidth ?>px;top:<?php echo $SRheight ?>px;z-index:<?php $zi++; echo $zi ?>;" id="ScriptRefresH">
<a href="#" onclick="RefresHScript()"><font class="body_small">refresh</font></a>
</span>

<span style="position:absolute;left:154px;top:<?php echo $SFheight ?>px;z-index:<?php $zi++; echo $zi ?>;" id="FormPanel">
	<?php
	if ($webphone_location == 'bar')
        {echo "<img src=\"images/pixel.gif\" width=\"1px\" height=\"".$webphone_height."px\" /><br />\n";}
	?>
    <table border="0" bgcolor="<?php echo $SCRIPT_COLOR ?>" width="<?php echo $SSwidth ?>px" height="<?php echo $SSheight ?>px"><tr><td align="left" valign="top"><font class="sb_text"><div class="noscroll_script" id="FormContents"><iframe src="./vdc_form_display.php?lead_id=&list_id=&stage=WELCOME" style="background-color:transparent;" scrolling="auto" frameborder="0" allowtransparency="true" id="vcFormIFrame" name="vcFormIFrame" width="<?php echo $SDwidth ?>px" height="<?php echo $SSheight ?>px" STYLE="z-index:18"> </iframe></div></font></td></tr></table>
</span>

<span style="position:absolute;left:<?php echo $AMwidth ?>px;top:<?php echo $SRheight ?>px;z-index:<?php $zi++; echo $zi ?>;" id="FormRefresH">
<a href="#" onclick="FormContentsLoad()"><font class="body_small">refresh</font></a>
</span>


<span style="position:absolute;left:157px;top:<?php echo $HTheight ?>px;z-index:<?php $zi++; echo $zi ?>;" id="TransferMain">
    <table bgcolor="#CCCCFF" width="<?php echo $SDwidth ?>px">
    <tr valign="top">
    <td align="left" height="30px">
	<span class="text_input" id="TransferMaindiv">
	<font class="body_text">
    <img src="./images/vdc_XB_header.gif" border="0" alt="Transfer - Conference" style="vertical-align:middle" /> &nbsp; &nbsp; &nbsp; &nbsp; <span id="XfeRDiaLGrouPSelecteD"></span> &nbsp; &nbsp; <span id="XfeRCID"></span><br />

    <table cellpadding="0" cellspacing="1" border="0">
    <tr>
    <td align="left" colspan="3">
    <span id="XfeRGrouPLisT"><select size="1" name="XfeRGrouP" id="XfeRGrouP" class="cust_form" onChange="XferAgentSelectLink();return false;"><option>-- SELECT A GROUP TO SEND YOUR CALL TO --</option></select></span>
	 
    <span style="background-color: <?php echo $MAIN_COLOR ?>" id="LocalCloser"><img src="./images/vdc_XB_localcloser_OFF.gif" border="0" alt="LOCAL CLOSER" style="vertical-align:middle" /></span> &nbsp; &nbsp;
 </td>
    <td align="left">
    <span style="background-color: <?php echo $MAIN_COLOR ?>" id="HangupXferLine"><img src="./images/vdc_XB_hangupxferline_OFF.gif" border="0" alt="Hangup Xfer Line" style="vertical-align:middle" /></span>
 </td>
 </tr>

    <tr>
    <td align="left" colspan="2">
    <img src="./images/vdc_XB_seconds.gif" border="0" alt="seconds" style="vertical-align:middle" /><input type="text" size="2" name="xferlength" id="xferlength" maxlength="4" class="cust_form" readonly="readonly" />
	&nbsp; 
    <img src="./images/vdc_XB_channel.gif" border="0" alt="channel" style="vertical-align:middle" /><input type="text" size="12" name="xferchannel" id="xferchannel" maxlength="200" class="cust_form" readonly="readonly" />
 </td>
    <td align="left">
    <span id="consultative_checkbox"><input type="checkbox" name="consultativexfer" id="consultativexfer" size="1" value="0"><font class="body_tiny"> CONSULTATIVE &nbsp;</font></span>
 </td>
    <td align="left">
    <span style="background-color: <?php echo $MAIN_COLOR ?>" id="HangupBothLines"><a href="#" onclick="bothcall_send_hangup();return false;"><img src="./images/vdc_XB_hangupbothlines.gif" border="0" alt="Hangup Both Lines" style="vertical-align:middle" /></a></span>
 </td>
 </tr>

    <tr>
    <td align="left" colspan="2">
    <img src="./images/vdc_XB_number.gif" border="0" alt="Number to call" style="vertical-align:middle" />
	&nbsp; 
	<?php
	if ($hide_xfer_number_to_dial=='ENABLED')
		{
		?>
        <input type="hidden" name="xfernumber" id="xfernumber" value="<?php echo $preset_populate ?>" /> &nbsp;  &nbsp;  &nbsp;  &nbsp;  &nbsp;  &nbsp;  &nbsp;  &nbsp;  &nbsp;  &nbsp;
		<?php
		}
	else
		{
		?>
        <input type="text" size="20" name="xfernumber" id="xfernumber" maxlength="25" class="cust_form" value="<?php echo $preset_populate ?>" /> &nbsp;
		<?php
		}
	?>
    <span id="agentdirectlink"><font class="body_small_bold"><a href="#" onclick="XferAgentSelectLaunch();return false;">AGENTS</a></font></span>
    <input type="hidden" name="xferuniqueid" id="xferuniqueid" />
    <input type="hidden" name="xfername" id="xfername" />
    <input type="hidden" name="xfernumhidden" id="xfernumhidden" />
 </td>
    <td align="left">
    <span id="dialoverride_checkbox"><input type="checkbox" name="xferoverride" id="xferoverride" size="1" value="0"><font class="body_tiny" /> DIAL OVERRIDE</font></span>
 </td>
    <td align="left">
    <span style="background-color: <?php echo $MAIN_COLOR ?>" id="Leave3WayCall"><a href="#" onclick="leave_3way_call('FIRST');return false;"><img src="./images/vdc_XB_leave3waycall.gif" border="0" alt="LEAVE 3-WAY CALL" style="vertical-align:middle" /></a></span>
 </td>
 </tr>

    <tr>
    <td align="left" COLSPAN="4">
    <span style="background-color: <?php echo $MAIN_COLOR ?>" id="DialBlindTransfer"><img src="./images/vdc_XB_blindtransfer_OFF.gif" border="0" alt="Dial Blind Transfer" style="vertical-align:middle" /></span>
	&nbsp;
    <span style="background-color: <?php echo $MAIN_COLOR ?>" id="DialWithCustomer"><a href="#" onclick="SendManualDial('YES');return false;"><img src="./images/vdc_XB_dialwithcustomer.gif" border="0" alt="Dial With Customer" style="vertical-align:middle" /></a></span>
	&nbsp;
    <span style="background-color: <?php echo $MAIN_COLOR ?>" id="ParkCustomerDial"><a href="#" onclick="xfer_park_dial();return false;"><img src="./images/vdc_XB_parkcustomerdial.gif" border="0" alt="Park Customer Dial" style="vertical-align:middle" /></a></span>
	&nbsp;
	<?php
	if ($enable_xfer_presets=='ENABLED')
		{
		?>
        <span style="background-color: <?php echo $MAIN_COLOR ?>" id="PresetPullDown"><a href="#" onclick="generate_presets_pulldown();return false;"><img src="./images/vdc_XB_presetsbutton.gif" border="0" alt="Presets Button" style="vertical-align:middle" /></a></span>
		<?php
		}
	else
		{
		?>
		<font class="body_tiny">
		<a href="#" onclick="DtMf_PreSet_a();return false;">D1</a> 
		<a href="#" onclick="DtMf_PreSet_b();return false;">D2</a>
		<a href="#" onclick="DtMf_PreSet_c();return false;">D3</a>
		<a href="#" onclick="DtMf_PreSet_d();return false;">D4</a>
		<a href="#" onclick="DtMf_PreSet_e();return false;">D5</a>
		</font>
		<?php
		}
	?>
	&nbsp;
    <span style="background-color: <?php echo $MAIN_COLOR ?>" id="DialBlindVMail"><img src="./images/vdc_XB_ammessage_OFF.gif" border="0" alt="Blind Transfer VMail Message" style="vertical-align:middle" /></span>
 </td>
 </tr>

 </table>

	</font>
	</span>
	</td>
    </tr></table>
</span>

<span style="position:absolute;left:0px;top:0px;width:<?php echo $JS_browser_width ?>px;height:<?php echo $JS_browser_height ?>px;overflow:scroll;z-index:<?php $zi++; echo $zi ?>;background-color:<?php echo $SIDEBAR_COLOR ?>;" id="AgentXferViewSpan"><center><font class="body_text">
Available Agents Transfer: <span id="AgentXferViewSelect"></span></center></font></span>


<span style="position:absolute;left:5px;top:<?php echo $HTheight ?>px;z-index:<?php $zi++; echo $zi ?>;" id="HotKeyActionBox">
    <table border="0" bgcolor="#FFDD99" width="<?php echo $HCwidth ?>px" height="70px">
    <tr bgcolor="#FFEEBB"><td height="70px"><font class="sh_text"> Lead Dispositioned As: </font><br /><br /><center>
    <font class="sd_text"><span id="HotKeyDispo"> - </span></font></center>
 </td>
    </tr></table>
</span>

<span style="position:absolute;left:5px;top:<?php echo $HTheight ?>px;z-index:<?php $zi++; echo $zi ?>;" id="HotKeyEntriesBox">
    <table border="0" bgcolor="#FFDD99" width="<?php echo $HCwidth ?>px" height="70px">
    <tr bgcolor="#FFEEBB"><td width="200px"><font class="sh_text"> Disposition Hot Keys: </font></td><td colspan="2">
	<font class="body_small">When active, simply press the keyboard key for the desired disposition for this call. The call will then be hungup and dispositioned automatically:</font></td></tr><tr>
    <td width="200px"><font class="sk_text">
	<span id="HotKeyBoxA"><?php echo $HKboxA ?></span>
    </font></td>
    <td width="200px"><font class="sk_text">
	<span id="HotKeyBoxB"><?php echo $HKboxB ?></span>
    </font></td>
    <td><font class="sk_text">
	<span id="HotKeyBoxC"><?php echo $HKboxC ?></span>
    </font></td>
    </tr></table>
</span>

<span style="position:absolute;left:5px;top:<?php echo $HTheight ?>px;z-index:<?php $zi++; echo $zi ?>;" id="CBcommentsBox">
    <table border="0" bgcolor="#FFFFCC" width="<?php echo $HCwidth ?>px" height="70px">
    <tr bgcolor="#FFFF66">
    <td align="left"><font class="sh_text"> Previous Callback Information: </font></td>
    <td align="right"><font class="sk_text"> <a href="#" onclick="CBcommentsBoxhide();return false;">close</a> </font></td>
	</tr><tr>
    <td><font class="sk_text">
    <span id="CBcommentsBoxA"></span><br />
    <span id="CBcommentsBoxB"></span><br />
    <span id="CBcommentsBoxC"></span><br />
    </font></td>
    <td width="320px"><font class="sk_text">
	<span id="CBcommentsBoxD"></span>
    </font></td>
    </tr></table>
</span>

<span style="position:absolute;left:5px;top:<?php echo $HTheight ?>px;z-index:<?php $zi++; echo $zi ?>;" id="EAcommentsBox">
    <table border="0" bgcolor="#FFFFCC" width="<?php echo $HCwidth ?>px" height="70px">
    <tr bgcolor="#FFFF66">
    <td align="left"><font class="sh_text"> Extended Alt Phone Information: </font></td>
    <td align="right"><font class="sk_text"> <a href="#" onclick="EAcommentsBoxhide('YES');return false;"> minimize </a> </font></td>
	</tr><tr>
    <td valign="top"><font class="sk_text">
    <span id="EAcommentsBoxC"></span><br />
    <span id="EAcommentsBoxB"></span><br />
    </font></td>
    <td width="320px" valign="top"><font class="sk_text">
    <span id="EAcommentsBoxA"></span><br />
	<span id="EAcommentsBoxD"></span>
    </font></td>
    </tr></table>
</span>

<span style="position:absolute;left:695px;top:<?php echo $HTheight ?>px;z-index:<?php $zi++; echo $zi ?>;" id="EAcommentsMinBox">
    <table border="0" bgcolor="#FFFFCC" width="40px" height="20px">
    <tr bgcolor="#FFFF66">
    <td align="left"><font class="sk_text"><a href="#" onclick="EAcommentsBoxshow();return false;"> maximize </a> <br />Alt Phone Info</font></td>
    </tr></table>
</span>

<span style="position:absolute;left:0px;top:12px;z-index:<?php $zi++; echo $zi ?>;" id="NoneInSessionBox">
    <table border="1" bgcolor="#CCFFFF" width="<?php echo $CAwidth ?>px" height="<?php echo $WRheight ?>px"><tr><td align="center"> No one is in your session: <span id="NoneInSessionID"></span><br />
	<a href="#" onclick="NoneInSessionOK();return false;">Go Back</a>
    <br /><br />
	<span id="NoneInSessionLink"><a href="#" onclick="NoneInSessionCalL();return false;">Call Agent Again</a></span>
    </td></tr></table>
</span>

<span style="position:absolute;left:0px;top:0px;z-index:<?php $zi++; echo $zi ?>;" id="CustomerGoneBox">
    <table border="1" bgcolor="#CCFFFF" width="<?php echo $CAwidth ?>px" height="<?php echo $WRheight ?>px"><tr><td align="center"> Customer has hung up: <span id="CustomerGoneChanneL"></span><br />
	<a href="#" onclick="CustomerGoneOK();return false;">Go Back</a>
    <br /><br />
	<a href="#" onclick="CustomerGoneHangup();return false;">Finish and Disposition Call</a>
    </td></tr></table>
</span>

<span style="position:absolute;left:0px;top:0px;z-index:<?php $zi++; echo $zi ?>;" id="WrapupBox">
    <table border="1" bgcolor="#CCFFCC" width="<?php echo $CAwidth ?>px" height="<?php echo $WRheight ?>px"><tr><td align="center"> Call Wrapup: <span id="WrapupTimer"></span> seconds remaining in wrapup<br /><br />
	<span id="WrapupMessage"><?php echo $wrapup_message ?></span>
    <br /><br />
	<a href="#" onclick="WrapupFinish();return false;">Finish Wrapup and Move On</a>
    </td></tr></table>
</span>

<span style="position:absolute;left:200px;top:150px;z-index:<?php $zi++; echo $zi ?>;" id="TimerSpan">
    <table border="1" bgcolor="#CCFFCC" width="400px" height="200px"><tr><td align="center">
    <br /><span id="TimerContentSpan"></span><br /><br />
	<a href="#" onclick="hideDiv('TimerSpan');return false;">Close Message</a>
    </td></tr></table>
</span>

<span style="position:absolute;left:0px;top:0px;z-index:<?php $zi++; echo $zi ?>;" id="AgenTDisablEBoX">
    <table border="1" bgcolor="#FFFFFF" width="<?php echo $CAwidth ?>px" height="<?php echo $WRheight ?>px"><tr><td align="center">Your session has been disabled<br /><a href="#" onclick="LogouT('DISABLED');return false;">LOGOUT</a><br /><br /><a href="#" onclick="hideDiv('AgenTDisablEBoX');return false;">Go Back</a>
    </td></tr></table>
</span>

<span style="position:absolute;left:0px;top:0px;z-index:<?php $zi++; echo $zi ?>;" id="SysteMDisablEBoX">
    <table border="1" bgcolor="#FFFFFF" width="<?php echo $CAwidth ?>px" height="<?php echo $WRheight ?>px"><tr><td align="center">There is a time synchronization problem with your system, please tell your system administrator<br /><br /><br /><a href="#" onclick="hideDiv('SysteMDisablEBoX');return false;">Go Back</a>
    </td></tr></table>
</span>

<span style="position:absolute;left:0px;top:0px;z-index:<?php $zi++; echo $zi ?>;" id="LogouTBox">
    <table border="1" bgcolor="#FFFFFF" width="<?php echo $CAwidth ?>px" height="<?php echo $WRheight ?>px"><tr><td align="center"><br /><span id="LogouTBoxLink">LOGOUT</span></td></tr></table>
</span>

<span style="position:absolute;left:0px;top:70px;z-index:<?php $zi++; echo $zi ?>;" id="DispoButtonHideA">
    <table border="0" bgcolor="#CCFFCC" width="165px" height="22px"><tr><td align="center" valign="top"></td></tr></table>
</span>

<span style="position:absolute;left:0px;top:138px;z-index:<?php $zi++; echo $zi ?>;" id="DispoButtonHideB">
    <table border="0" bgcolor="#CCFFCC" width="165px" height="250px"><tr><td align="center" valign="top">&nbsp;</td></tr></table>
</span>

<span style="position:absolute;left:0px;top:0px;z-index:<?php $zi++; echo $zi ?>;" id="DispoButtonHideC">
    <table border="0" bgcolor="#CCFFCC" width="<?php echo $CAwidth ?>px" height="47px"><tr><td align="center" valign="top">Any changes made to the customer information below at this time will not be comitted, You must change customer information before you Hangup the call. </td></tr></table>
</span>

<span style="position:absolute;left:0px;top:0px;z-index:<?php $zi++; echo $zi ?>;" id="DispoSelectBox">
    <table border="1" bgcolor="#CCFFCC" width="<?php echo $CAwidth ?>px" height="<?php echo $WRheight ?>px"><tr><td align="center" valign="top"> DISPOSITION CALL :<span id="DispoSelectPhonE"></span> &nbsp; &nbsp; &nbsp; <span id="DispoSelectHAspan"><a href="#" onclick="DispoHanguPAgaiN()">Hangup Again</a></span> &nbsp; &nbsp; &nbsp; <span id="DispoSelectMaxMin"><a href="#" onclick="DispoMinimize()"> minimize </a></span><br />
	<?php
	if ($webphone_location == 'bar')
        {echo "<br /><img src=\"images/pixel.gif\" width=\"1px\" height=\"".$webphone_height."px\" /><br />\n";}
	?>
	<span id="Dispo3wayMessage"></span>
	<span id="DispoManualQueueMessage"></span>
	<span id="PerCallNotesContent"><input type="hidden" name="call_notes_dispo" id="call_notes_dispo" value="" /></span>
	<span id="DispoSelectContent"> End-of-call Disposition Selection </span>
    <input type="hidden" name="DispoSelection" id="DispoSelection" /><br />
    <input type="checkbox" name="DispoSelectStop" id="DispoSelectStop" size="1" value="0" /> PAUSE AGENT DIALING <br />
	<a href="#" onclick="DispoSelectContent_create('','ReSET');return false;">CLEAR FORM</a> | 
	<a href="#" onclick="DispoSelect_submit();return false;">SUBMIT</a>
    <br /><br />
	<a href="#" onclick="WeBForMDispoSelect_submit();return false;">WEB FORM SUBMIT</a>
    <br /><br /> &nbsp;
    </td></tr></table>
</span>

<span style="position:absolute;left:0px;top:0px;z-index:<?php $zi++; echo $zi ?>;" id="CallBackSelectBox">
    <table border="1" bgcolor="#CCFFCC" width="<?php echo $CAwidth ?>px" height="<?php echo $WRheight ?>px"><tr><td align="center" valign="top"> Select a CallBack Date :<span id="CallBackDatE"></span><br />
	<?php
	if ($webphone_location == 'bar')
        {echo "<br /><img src=\"images/pixel.gif\" width=\"1px\" height=\"".$webphone_height."px\" /><br />\n";}
	?>
    <input type="hidden" name="CallBackDatESelectioN" id="CallBackDatESelectioN" />
    <input type="hidden" name="CallBackTimESelectioN" id="CallBackTimESelectioN" />
	<span id="CallBackDatEPrinT">Select a Date Below</span> &nbsp;
	<span id="CallBackTimEPrinT"></span> &nbsp; &nbsp;
	Hour: 
    <select size="1" name="CBT_hour" id="CBT_hour">
	<option>01</option>
	<option>02</option>
	<option>03</option>
	<option>04</option>
	<option>05</option>
	<option>06</option>
	<option>07</option>
	<option>08</option>
	<option>09</option>
	<option>10</option>
	<option>11</option>
	<option>12</option>
	</select> &nbsp;
	Minutes: 
    <select size="1" name="CBT_minute" id="CBT_minute">
	<option>00</option>
	<option>05</option>
	<option>10</option>
	<option>15</option>
	<option>20</option>
	<option>25</option>
	<option>30</option>
	<option>35</option>
	<option>40</option>
	<option>45</option>
	<option>50</option>
	<option>55</option>
	</select> &nbsp;

    <select size="1" name="CBT_ampm" id="CBT_ampm">
	<option>AM</option>
	<option selected>PM</option>
    </select> &nbsp;<br />
	<?php
	if ($agentonly_callbacks)
        {echo "<input type=\"checkbox\" name=\"CallBackOnlyMe\" id=\"CallBackOnlyMe\" size=\"1\" value=\"0\" /> MY CALLBACK ONLY <br />";}
	?>
    CB Comments: <input type="text" name="CallBackCommenTsField" id="CallBackCommenTsField" size="50" maxlength="255" /><br /><br />

    <a href="#" onclick="CallBackDatE_submit();return false;">SUBMIT</a><br /><br />
	<span id="CallBackDateContent"><?php echo  "$CCAL_OUT" ?></span>
    <br /><br /> &nbsp;
    </td></tr></table>
</span>

<span style="position:absolute;left:0px;top:0px;z-index:<?php $zi++; echo $zi ?>;" id="CallBacKsLisTBox">
    <table border="1" bgcolor="#CCFFCC" width="<?php echo $CAwidth ?>px" height="<?php echo $WRheight ?>px"><tr><td align="center" valign="top"> CALLBACKS FOR AGENT <?php echo $VD_login ?>:<br />To see information on one of the callbacks below, click on the INFO link. To call the customer back now, click on the DIAL link. If you click on a record below to dial it, it will be removed from the list.
 <br />
	<?php
	if ($webphone_location == 'bar')
        {echo "<br /><img src=\"images/pixel.gif\" width=\"1px\" height=\"".$webphone_height."px\" /><br />\n";}
	?>
	<div class="scroll_callback" id="CallBacKsLisT"></div>
    <br /> &nbsp;
	<a href="#" onclick="CalLBacKsLisTCheck();return false;">Refresh</a>
	 &nbsp;  &nbsp;  &nbsp;  &nbsp;  &nbsp;  &nbsp;  &nbsp;  &nbsp; 
	<a href="#" onclick="CalLBacKsLisTClose();return false;">Go Back</a>
    </td></tr></table>
</span>

<span style="position:absolute;left:0px;top:0px;z-index:<?php $zi++; echo $zi ?>;" id="NeWManuaLDiaLBox">
    <table border="1" bgcolor="#CCFFCC" width="<?php echo $CAwidth ?>px" height="<?php echo $WRheight ?>px"><tr><td align="center" valign="top"> NEW MANUAL DIAL LEAD FOR <?php echo "$VD_login in campaign $VD_campaign" ?>:<br /><br />Enter information below for the new lead you wish to call.
 <br />
	<?php 
	if (!eregi("X",$manual_dial_prefix))
		{
        echo "Note: a dial prefix of $manual_dial_prefix will be added to the beginning of this number<br />\n";
		}
	?>
    Note: all new manual dial leads will go into list <?php echo $manual_dial_list_id ?><br /><br />
    <table><tr>
    <td align="right"><font class="body_text"> Dial Code: </font></td>
    <td align="left"><font class="body_text"><input type="text" size="7" maxlength="10" name="MDDiaLCodE" id="MDDiaLCodE" class="cust_form" value="1" />&nbsp; (This is usually a 1 in the USA-Canada)</font></td>
	</tr><tr>
    <td align="right"><font class="body_text"> Phone Number: </font></td>
    <td align="left"><font class="body_text">
    <input type="text" size="14" maxlength="18" name="MDPhonENumbeR" id="MDPhonENumbeR" class="cust_form" value="" />&nbsp; (digits only)</font>
	<input type="hidden" name="MDLeadID" id="MDLeadID" value="" />
	<input type="hidden" name="MDType" id="MDLeadID" value="" />
	</td>
	</tr><tr>
    <td align="right"><font class="body_text"> Search Existing Leads: </font></td>
    <td align="left"><font class="body_text"><input type="checkbox" name="LeadLookuP" id="LeadLookuP" size="1" value="0" />&nbsp; (This option if checked will attempt to find the phone number in the system before inserting it as a new lead)</font></td>
	</tr><tr>

    <td align="left" colspan="2">
    <br /><br /><CENTER>
	<span id="ManuaLDiaLGrouPSelecteD"></span> &nbsp; &nbsp; <span id="ManuaLDiaLGrouP"></span>
	</CENTER>
    <br /><br />If you want to dial a number and have it NOT be added as a new lead, enter in the exact dialstring that you want to call in the Dial Override field below. To hangup this call you will have to open the CALLS IN THIS SESSION link at the bottom of the screen and hang it up by clicking on its channel link there.<br /> &nbsp; </td>
	</tr><tr>
    <td align="right"><font class="body_text"> Dial Override: </font></td>
    <td align="left"><font class="body_text"><input type="text" size="24" maxlength="20" name="MDDiaLOverridE" id="MDDiaLOverridE" class="cust_form" value="" />&nbsp; (digits only please)</font>
	</td>
    </tr></table>
 <br />
	<a href="#" onclick="NeWManuaLDiaLCalLSubmiT('NOW');return false;">Dial Now</a>
	 &nbsp;  &nbsp;  &nbsp;  &nbsp;  &nbsp;  &nbsp;  &nbsp;  &nbsp; 
	<a href="#" onclick="NeWManuaLDiaLCalLSubmiT('PREVIEW');return false;">Preview Call</a>
	 &nbsp;  &nbsp;  &nbsp;  &nbsp;  &nbsp;  &nbsp;  &nbsp;  &nbsp; 
	<a href="#" onclick="ManualDialHide();return false;">Go Back</a>
    </td></tr></table>
</span>

<span style="position:absolute;left:0px;top:0px;z-index:<?php $zi++; echo $zi ?>;" id="CloserSelectBox">
    <table border="1" bgcolor="#CCFFCC" width="<?php echo $CAwidth ?>px" height="<?php echo $WRheight ?>px"><tr><td align="center" valign="top"> CLOSER INBOUND GROUP SELECTION <br />
	<?php
	if ($webphone_location == 'bar')
        {echo "<br /><img src=\"images/pixel.gif\" width=\"1px\" height=\"".$webphone_height."px\" /><br />\n";}
	?>
	<span id="CloserSelectContent"> Closer Inbound Group Selection </span>
    <input type="hidden" name="CloserSelectList" id="CloserSelectList" /><br />
	<?php
	if ( ($outbound_autodial_active > 0) and ($disable_blended_checkbox < 1) and ($dial_method != 'INBOUND_MAN') and ($VU_agent_choose_blended > 0) )
		{
		?>
        <input type="checkbox" name="CloserSelectBlended" id="CloserSelectBlended" size="1" value="0" /> BLENDED CALLING(outbound activated) <br />
		<?php
		}
	?>
	<a href="#" onclick="CloserSelectContent_create();return false;"> RESET </a> | 
	<a href="#" onclick="CloserSelect_submit();return false;">SUBMIT</a>
    <br /><br /><br /><br /> &nbsp;
    </td></tr></table>
</span>

<span style="position:absolute;left:0px;top:0px;z-index:<?php $zi++; echo $zi ?>;" id="TerritorySelectBox">
    <table border="1" bgcolor="#CCFFCC" width="<?php echo $CAwidth ?>px" height="<?php echo $WRheight ?>px"><tr><td align="center" valign="top"> TERRITORY SELECTION <br />
	<?php
	if ($webphone_location == 'bar')
        {echo "<br /><img src=\"images/pixel.gif\" width=\"1px\" height=\"".$webphone_height."px\" /><br />\n";}
	?>
	<span id="TerritorySelectContent"> Territory Selection </span>
    <input type="hidden" name="TerritorySelectList" id="TerritorySelectList" /><br />
	<a href="#" onclick="TerritorySelectContent_create();return false;"> RESET </a> | 
	<a href="#" onclick="TerritorySelect_submit();return false;">SUBMIT</a>
    <br /><br /><br /><br /> &nbsp;
    </td></tr></table>
</span>

<span style="position:absolute;left:0px;top:0px;z-index:<?php $zi++; echo $zi ?>;" id="NothingBox">
	<span id="DiaLLeaDPrevieWHide"> Channel</span>
	<span id="DiaLDiaLAltPhonEHide"> Channel</span>
	<?php
	if (!$agentonly_callbacks)
        {echo "<input type=\"checkbox\" name=\"CallBackOnlyMe\" id=\"CallBackOnlyMe\" size=\"1\" value=\"0\" /> MY CALLBACK ONLY <br />";}
	if ( ($outbound_autodial_active < 1) or ($disable_blended_checkbox > 0) or ($dial_method == 'INBOUND_MAN') or ($VU_agent_choose_blended < 1) )
        {echo "<input type=\"checkbox\" name=\"CloserSelectBlended\" id=\"CloserSelectBlended\" size=\"1\" value=\"0\" /> BLENDED CALLING<br />";}
	?>
</span>

<span style="position:absolute;left:0px;top:0px;z-index:<?php $zi++; echo $zi ?>;" id="CalLLoGDisplaYBox">
	<table border="1" bgcolor="#CCFFCC" width="<?php echo $CAwidth ?>px" height="<?php echo $WRheight ?>px"><tr><td align="center" valign="top"> &nbsp; &nbsp; &nbsp; AGENT CALL LOG: &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; <a href="#" onclick="CalLLoGVieWClose();return false;">close [X]</a><br />
	<?php
	if ($webphone_location == 'bar')
		{echo "<br /><img src=\"images/pixel.gif\" width=\"1px\" height=\"".$webphone_height."px\" /><br />\n";}
	?>
	<div class="scroll_calllog" id="CallLogSpan"> Call log List </div>
	<br /><br /> &nbsp;
	</td></tr></table>
</span>

<span style="position:absolute;left:0px;top:0px;z-index:<?php $zi++; echo $zi ?>;" id="SearcHForMDisplaYBox">
	<table border="1" bgcolor="#CCFFCC" width="<?php echo $CAwidth ?>px" height="<?php echo $WRheight ?>px"><tr><td align="center" valign="top"> &nbsp; &nbsp; &nbsp; SEARCH FOR A LEAD: &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; <a href="#" onclick="LeaDSearcHVieWClose();return false;">close [X]</a><br />
	<?php
	if ($webphone_location == 'bar')
		{echo "<br /><img src=\"images/pixel.gif\" width=\"1px\" height=\"".$webphone_height."px\" /><br />\n";}
	?>
	<br /><br />
	Notes: when doing a search for a lead, the phone number, lead ID or <?php echo $label_vendor_lead_code ?> are the best fields to use. <br />Using the other fields may be slower. Lead searching does not allow for wildcard or partial search terms. <br />Lead search requests are all logged in the system.
	<br /><br />
	<center>
	<table border="0">
	<tr>
	<td align="right"> Phone Number: </td><td align="left"><input type="text" size="18" maxlength="20" name="search_phone_number" id="search_phone_number"></td>
	</tr>
	<tr>
	<td align="right"> Phone Number Fields: </td>
	<td align="left">
	<input type="checkbox" name="search_main_phone" id="search_main_phone" size="1" value="0" checked /> Main Phone Number
	<input type="checkbox" name="search_alt_phone" id="search_alt_phone" size="1" value="0" /> Alternate Phone Number
	<input type="checkbox" name="search_addr3_phone" id="search_addr3_phone" size="1" value="0" /> Address3 Phone Number
	</td>
	</tr>
	<tr>
	<td align="right"> Lead ID: </td><td align="left"><input type="text" size="11" maxlength="10" name="search_lead_id" id="search_lead_id"></td>
	</tr>
	<tr>
	<td align="right"> <?php echo $label_vendor_lead_code ?>: </td><td align="left"><input type="text" size="18" maxlength="20" name="search_vendor_lead_code" id="search_vendor_lead_code"></td>
	</tr>
	<tr>
	<td align="right"> <?php echo $label_first_name ?>: </td><td align="left"><input type="text" size="18" maxlength="20" name="search_first_name" id="search_first_name"></td>
	</tr>
	<tr>
	<td align="right"> <?php echo $label_last_name ?>: </td><td align="left"><input type="text" size="18" maxlength="20" name="search_last_name" id="search_last_name"></td>
	</tr>
	<tr>
	<td align="right"> <?php echo $label_city ?>: </td><td align="left"><input type="text" size="18" maxlength="20" name="search_city" id="search_city"></td>
	</tr>
	<tr>
	<td align="right"> <?php echo $label_state ?>: </td><td align="left"><input type="text" size="18" maxlength="20" name="search_state" id="search_state"></td>
	</tr>
	<tr>
	<td align="right"> <?php echo $label_postal_code ?>: </td><td align="left"><input type="text" size="10" maxlength="10" name="search_postal_code" id="search_postal_code"></td>
	</tr>
	<tr>
	<td align="center" colspan="2"><br /> <a href="#" onclick="LeadSearchSubmit();return false;">SUBMIT SEARCH</a> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; <a href="#" onclick="LeadSearchReset();return false;">reset form</a></td>
	</tr>
	</table>
	<br /><br /> &nbsp;
	</td></tr></table>
</span>

<span style="position:absolute;left:0px;top:0px;z-index:<?php $zi++; echo $zi ?>;" id="SearcHResultSDisplaYBox">
	<table border="1" bgcolor="#CCFFCC" width="<?php echo $CAwidth ?>px" height="<?php echo $WRheight ?>px"><tr><td align="center" valign="top"> &nbsp; &nbsp; &nbsp; SEARCH RESULTS: &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; <a href="#" onclick="hideDiv('SearcHResultSDisplaYBox');return false;">close [X]</a><br />
	<?php
	if ($webphone_location == 'bar')
		{echo "<br /><img src=\"images/pixel.gif\" width=\"1px\" height=\"".$webphone_height."px\" /><br />\n";}
	?>
	<div class="scroll_calllog" id="SearcHResultSSpan"> Search Results </div>
	<br /><br /> &nbsp;
	</td></tr></table>
</span>

<span style="position:absolute;left:0px;top:0px;z-index:<?php $zi++; echo $zi ?>;" id="CalLNotesDisplaYBox">
	<table border="1" bgcolor="#CCFFCC" width="<?php echo $CAwidth ?>px" height="<?php echo $WRheight ?>px"><tr><td align="center" valign="top"> &nbsp; &nbsp; &nbsp; CALL NOTES LOG: &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; <a href="#" onclick="hideDiv('CalLNotesDisplaYBox');return false;">close [X]</a><br />
	<?php
	if ($webphone_location == 'bar')
		{echo "<br /><img src=\"images/pixel.gif\" width=\"1px\" height=\"".$webphone_height."px\" /><br />\n";}
	?>
	<div class="scroll_calllog" id="CallNotesSpan"> Call Notes List </div>
	<br /><br /> &nbsp;
	<a href="#" onclick="hideDiv('CalLNotesDisplaYBox');return false;">Close Info Box</a>
	</td></tr></table>
</span>

<span style="position:absolute;left:0px;top:0px;z-index:<?php $zi++; echo $zi ?>;" id="LeaDInfOBox">
	<table border="1" bgcolor="#CCFFCC" width="<?php echo $CAwidth ?>px" height="<?php echo $WRheight ?>px"><tr><td align="center" valign="top"> &nbsp; &nbsp; &nbsp; Customer Information: &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; <a href="#" onclick="hideDiv('LeaDInfOBox');return false;">close [X]</a>
	<br />
	<?php
	if ($webphone_location == 'bar')
		{echo "<br /><img src=\"images/pixel.gif\" width=\"1px\" height=\"".$webphone_height."px\" /><br />\n";}
	?>
	<span id="LeaDInfOSpan"> Lead Info </span>
	<br /><br /> &nbsp;
	<a href="#" onclick="hideDiv('LeaDInfOBox');return false;">Close Info Box</a>
	</td></tr></table>
</span>

<span style="position:absolute;left:0px;top:0px;z-index:<?php $zi++; echo $zi ?>;" id="PauseCodeSelectBox">
	<table border="1" bgcolor="#CCFFCC" width="<?php echo $CAwidth ?>px" height="<?php echo $WRheight ?>px"><tr><td align="center" valign="top"> SELECT A PAUSE CODE :<br />
	<?php
	if ($webphone_location == 'bar')
		{echo "<br /><img src=\"images/pixel.gif\" width=\"1px\" height=\"".$webphone_height."px\" /><br />\n";}
	?>
	<span id="PauseCodeSelectContent"> Pause Code Selection </span>
	<input type="hidden" name="PauseCodeSelection" id="PauseCodeSelection" />
	<br /><br /> &nbsp;
	</td></tr></table>
</span>

<span style="position:absolute;left:<?php echo $PBwidth ?>px;top:40px;z-index:<?php $zi++; echo $zi ?>;" id="PresetsSelectBox">
	<table border="0" bgcolor="#9999FF" width="400px" height="<?php echo $HTheight ?>px"><tr><td align="center" valign="top"> SELECT A PRESET :<br />
	<?php
	if ($webphone_location == 'bar')
		{echo "<br /><img src=\"images/pixel.gif\" width=\"1px\" height=\"".$webphone_height."px\" /><br />\n";}
	?>
	<span id="PresetsSelectBoxContent"> Presets Selection </span>
	<input type="hidden" name="PresetSelection" id="PresetSelection" />
	</td></tr></table>
</span>

<span style="position:absolute;left:0px;top:0px;z-index:<?php $zi++; echo $zi ?>;" id="GroupAliasSelectBox">
	<table border="1" bgcolor="#CCFFCC" width="<?php echo $CAwidth ?>px" height="<?php echo $WRheight ?>px"><tr><td align="center" valign="top"> SELECT A GROUP ALIAS :<br />
	<?php
	if ($webphone_location == 'bar')
		{echo "<br /><img src=\"images/pixel.gif\" width=\"1px\" height=\"".$webphone_height."px\" /><br />\n";}
	?>
	<span id="GroupAliasSelectContent"> Group Alias Selection </span>
	<input type="hidden" name="GroupAliasSelection" id="GroupAliasSelection" />
	<br /><br /> &nbsp;
	</td></tr></table>
</span>

<span style="position:absolute;left:0px;top:0px;z-index:<?php $zi++; echo $zi ?>;" id="blind_monitor_alert_span">
	<table border="1" bgcolor="#CCFFCC" width="<?php echo $CAwidth ?>px" height="<?php echo $WRheight ?>px"><tr><td align="center" valign="top"> ALERT :<br />
	<b><font color="red" size="5"> &nbsp; &nbsp; <span id="blind_monitor_alert_span_contents"></span></b></font>
	<br /><br /> <a href="#" onclick="hideDiv('blind_monitor_alert_span');return false;">Go Back</a>
	</td></tr></table>
</span>


<span style="position:absolute;left:0px;top:<?php echo $GHheight ?>px;z-index:<?php $zi++; echo $zi ?>;" id="GENDERhideFORieALT"></span>

</form>


<form name="inert_form" id="inert_form" onsubmit="return false;">

<span style="position:absolute;left:0px;top:400px;z-index:1;" id="NothingBox2">
<!--  <BUTTON Type=button name="inert_button" id="inert_button" onclick="return false;"><img src="./images/blank.gif" /></BUTTON> -->
<input type="checkbox" name="inert_button" id="inert_button" size="1" value="0" onclick="return false;" />
</span>

</form>

<form name="alert_form" id="alert_form" onsubmit="return false;">

<span style="position:absolute;left:200px;top:200px;z-index:<?php $zi++; echo $zi ?>;" id="AlertBox">
<table border="2" bgcolor="#666666" cellpadding="2" cellspacing="1">
<tr><td bgcolor="#f0f0f0" align="left">
<font face="arial,helvetica" size="2"><b> &nbsp; Agent Alert!</b></font>
</td></tr>
<tr><td bgcolor="#E6E6E6">
<table border="0" bgcolor="#E3E3E3" width="400">
<tr>
<td align="center" valign="top" width="50"> &nbsp; 
<br /><br />
<img src="./images/alert.gif" alt="alert" border="0">
</td>
<td align="center" valign="top"> &nbsp; 
<br /><br />
<font face="arial,helvetica" size="2">
<span id="AlertBoxContent"> Alert Box </span>
</font>
<br /><br />
</td>
</tr><tr>
<td align="center" valign="top" colspan="2">
<button type="button" name="alert_button" id="alert_button" onclick="hideDiv('AlertBox');return false;">OK</BUTTON>
<br /> &nbsp;
<!-- <a href="#" onclick="document.alert_form.alert_button.focus();">focus</a> -->
</td></tr>
</table>
</td></tr>
</table>
</span>

</form>


</body>
</html>

<?php
	
exit; 

?>
