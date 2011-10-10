<?php
# zoiperweb.php - the web-based Zoiper softphone 
# 
# Copyright (C) 2011  Matt Florell <vicidial@gmail.com>    LICENSE: AGPLv2
#
# variables sent to this script:
# $phone_login - phone login
# $phone_pass - phone password
# $server_ip - the server that you are to login to
# $callerid - the callerid number
# $protocol - IAX or SIP
# $codecs - list of codecs to use
# $options - optional webphone settings
# $system_key - key optionally used by the webphone service to validate the user
#
# CHANGELOG
# 100130-2359 - First Build of VICIDIAL web client basic login process finished
# 100424-2102 - Added codecs option and updated zoiperweb code reference
# 100827-1417 - Added system_key variable
# 101227-1313 - Added DIALPLAN_OFF_TOGGLE option
# 110526-1726 - Added AUTOANSWER option
#

if (isset($_GET["DB"]))							{$DB=$_GET["DB"];}
        elseif (isset($_POST["DB"]))			{$DB=$_POST["DB"];}
if (isset($_GET["phone_login"]))				{$phone_login=$_GET["phone_login"];}
        elseif (isset($_POST["phone_login"]))	{$phone_login=$_POST["phone_login"];}
if (isset($_GET["phone_pass"]))					{$phone_pass=$_GET["phone_pass"];}
        elseif (isset($_POST["phone_pass"]))    {$phone_pass=$_POST["phone_pass"];}
if (isset($_GET["server_ip"]))					{$server_ip=$_GET["server_ip"];}
        elseif (isset($_POST["server_ip"]))		{$server_ip=$_POST["server_ip"];}
if (isset($_GET["callerid"]))					{$callerid=$_GET["callerid"];}
        elseif (isset($_POST["callerid"]))		{$callerid=$_POST["callerid"];}
if (isset($_GET["protocol"]))					{$protocol=$_GET["protocol"];}
        elseif (isset($_POST["protocol"]))		{$protocol=$_POST["protocol"];}
if (isset($_GET["codecs"]))						{$codecs=$_GET["codecs"];}
        elseif (isset($_POST["codecs"]))		{$codecs=$_POST["codecs"];}
if (isset($_GET["options"]))					{$options=$_GET["options"];}
        elseif (isset($_POST["options"]))		{$options=$_POST["options"];}
if (isset($_GET["system_key"]))					{$system_key=$_GET["system_key"];}
        elseif (isset($_POST["system_key"]))	{$system_key=$_POST["system_key"];}

$b64_phone_login =		base64_decode($phone_login);
$b64_phone_pass =		base64_decode($phone_pass);
$b64_server_ip =		base64_decode($server_ip);
$b64_callerid =			base64_decode($callerid);
$b64_protocol =			base64_decode($protocol);
$b64_codecs =			base64_decode($codecs);
$b64_options =			base64_decode($options);
$b64_system_key =		base64_decode($system_key);
if ($b64_protocol != 'SIP')
	{$b64_protocol = 'IAX';}

?>

<html>
<head>
<title>Zoiper web</title>
</head>
<body onload="Init()" onunload="Quit()">
<script type="text/javascript">
var Zoiper;
var ActiveCall;
function GetValue(name)
	{
	return document.getElementById(name).value;
	}
function Init()
	{
	document.getElementById("Status").style.visibility = 'hidden';
	}
function Quit()
	{
	Zoiper.DelContact("web demo");
	Zoiper.DelAccount("Sample");

	// On firefox new instance of plugin is created before old one is destroyed
	// Because of this we must manually destroy existing plugin
	document.getElementById('ZoiperA').innerHTML = "";
	}
function Hang()
	{
	if (null != ActiveCall)
	ActiveCall.Hang();
	ActiveCall = null;
	}
function Dial()
	{
	ActiveCall = Zoiper.Dial(GetValue("number"));
	}
function Hold()
	{
	if (null != ActiveCall)
	ActiveCall.Hold();
	}
function Login()
	{
	var user = GetValue("user");
	var pass = GetValue("pass");
	Zoiper.Login(user,pass);
	}
function Logout()
	{
	Zoiper.Logout();
	}
function Status(text)
	{
	var node = document.getElementById("Status");
	//        node.innerHTML = text;
	node.innerHTML = node.innerHTML + "<br/>" + text;
	}
function OnZoiperReady(phone)
	{
	Zoiper = phone;
	var Config = Zoiper.GetConfig();
	Config.PopupMenuOnIncomingCall = "false";

	var Account = Zoiper.GetAccount("Sample");
	if (null == Account)
	Account = Zoiper.AddAccount("Your Phone","<?php echo $b64_protocol ?>");
	Account.Domain = "<?php echo $b64_server_ip ?>";
	Account.Host = "<?php echo $b64_server_ip ?>";
	Account.CallerID = "<?php echo $b64_callerid ?>";
	Account.UserName = "<?php echo $b64_phone_login ?>";
	Account.Password = "<?php echo $b64_phone_pass ?>";


	<?php
	if (preg_match("/gsm/i",$b64_codecs))	{echo "Account.AddCodec(\"GSM\")\n";}
	if (preg_match("/ulaw/i",$b64_codecs))	{echo "Account.AddCodec(\"u-law\")\n";}
	if (preg_match("/alaw/i",$b64_codecs))	{echo "Account.AddCodec(\"a-law\")\n";}
	if (preg_match("/speex/i",$b64_codecs)) {echo "Account.AddCodec(\"Speex\")\n";}
	if (preg_match("/ilbc/i",$b64_codecs))	{echo "Account.AddCodec(\"iLBC 30\")\n";}
	if (preg_match("/ilbc/i",$b64_codecs))	{echo "Account.AddCodec(\"iLBC 20\")\n";}
	?>

	Account.Apply();
	Account.Register();

	Zoiper.UseAccount("Sample");

	var Contact         = Zoiper.AddContact("web demo");
	Contact.Account     = "Sample";
	Contact.Display     = "web demo display";
	Contact.FirstName   = "John";
	Contact.MiddleName  = "Q";
	Contact.LastName    = "Public";
	Contact.Country     = "USA";
	Contact.City        = "Washington";
	Contact.WorkPhone   = "work";
	Contact.HomePhone   = "home";
	Contact.CellPhone   = "cell";
	Contact.FaxNumber   = "fax";
	Contact.Apply();


	<?php
	if (preg_match("/DIALPAD_Y|DIALPAD_TOGGLE/i",$b64_options))	
		{echo "Zoiper.ShowDialPad(\"true\");\n";}
	if (preg_match("/DIALPAD_N|DIALPAD_OFF_TOGGLE/i",$b64_options))	
		{echo "Zoiper.ShowDialPad(\"false\");\n";}
	?>

	}
function OnZoiperCallFail(call)
	{
	Status(call.Phone + " failed");
	}
function OnZoiperCallRing(call)
	{
	Status(call.Phone + " ring");
	}
function OnZoiperCallHang(call)
	{
	Status(call.Phone + " hang");
	}
function OnZoiperCallHold(call)
	{
	Status(call.Phone + " hold");
	}
function OnZoiperCallUnhold(call)
	{
	Status(call.Phone + " unhold");
	}
function OnZoiperCallAccept(call)
	{
	Status(call.Phone + " accept");
	}
function OnZoiperCallReject(call)
	{
	Status(call.Phone + " reject");
	}
function OnZoiperCallIncoming(call)
	{
	Status(call.Phone + " incoming");
	<?php
	if (preg_match("/AUTOANSWER_N/i",$b64_options))	
		{echo "// autoanswer disabled;\n";}
	else
		{echo "call.Accept();\n\tActiveCall = call;\n";}
	?>

	}
function OnZoiperAccountRegister(account)
	{
	Status(account.Name + " is registered");
	}
function OnZoiperAccountUnregister(account)
	{
	Status(account.Name + " is unregister");
	}
function OnZoiperAccountRegisterFail(account)
	{
	Status(account.Name + " failed to register");
	}
function OnZoiperContactStatus(contact,status)
	{
	Status(contact.Name + " is " + status);
	}
function dialpad_inactive()
	{
	document.getElementById("Dialpad_toggle").innerHTML = "<a href=\"#\" onclick=\"dialpad_active();return false;\">DIALPAD +</a>\n";
	Zoiper.ShowDialPad("false");
	}
function dialpad_active()
	{ 
	document.getElementById("Dialpad_toggle").innerHTML = "<a href=\"#\" onclick=\"dialpad_inactive();return false;\">DIALPAD -</a>\n";
	Zoiper.ShowDialPad("true");
	}      

</script>

<table>
<!-- 
<tr>
<td>Number</td>
</tr>
<tr>
<td><input id="number" type="text"/></td>
</tr><tr>
<td>
<button onclick="Dial()">Dial</button><br>
<button onclick="Hold()">Hold</button><br>
<button onclick="Hang()">Hang up</button>
</td>
</tr>
<tr>
<td><input id="user" type="text"/></td>
<td><input id="pass" type="text"/></td>
<td><button onclick="Login()">Login</button></td>
<td><button onclick="Logout()">Logout</button></td>
</tr>
-->
<tr>
<td>

<div id="supportedBrowsersScreen" style="display:block;">	   
        <!--CODEBASE="Activextest.ocx"    -->
		<div style="background:url(./loader.gif) 50% 50% no-repeat;width:434px;height:236px;float:left;"> 
		<object id="ZoiperA" classid="clsid:BCCA9B64-41B3-4A20-8D8B-E69FE61F1F8B" align="center" width="434" height="236" CODEBASE="http://www.zoiper.com/webphone/InstallerWeb.cab#Version=1,17,0,6802">
			<embed id="ZoiperN" type="application/x-zoiper-plugin" align="center" width="434" height="236" />
		</object> 
		</div>
	</div> 
</div><!-- END phone_holder -->

</td>
</tr>
</table>
<BR>
<span id="Status">Ready</span>

<span id="Dialpad_toggle">
<?php
if (preg_match("/DIALPAD_TOGGLE/i",$b64_options))	
	{echo "<a href=\"#\" onclick=\"dialpad_inactive();return false;\">DIALPAD -</a>\n";}
if (preg_match("/DIALPAD_OFF_TOGGLE/i",$b64_options))	
	{echo "<a href=\"#\" onclick=\"dialpad_active();return false;\">DIALPAD +</a>\n";}
?>
</span>

</body>
</html>
