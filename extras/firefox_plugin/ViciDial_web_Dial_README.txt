ViciDial Web Dial Firefox Plugin				2010-10-08

This is an XPI-type plugin for Firefox web browsers that allows you to highlight
on a phone number on a web page and right-click to dial that phone number 
through an agent session in ViciDial.

The right-click menu also allows you to login to the ViciDial agent interface.

There is also an input form in the bottom right corner of your Firefox web 
browser that you can put a phone number into and click to dial.

This has been tested on Firefox version 3.0 and higher only.

To install this plugin, just open the vicidial_webdial_plugin.xpi file with
Firefox and it will ask you if you want to install ViciDial Web Dial. Click OK
and then you will be prompted to restart Firefox. The plugin will not function
until you restart Firefox.

For Configuration, you need to fill in the following fields:

Web Connection Info:
   ViciDial API URL:	example- http://server/agc/api.php
      (The agent API url)
   ViciDial AGC URL:	example- http://server/agc/vicidial.php
      (The agent web interface)

User Connection Info: (what the agent would use to login to ViciDial)
   Campaign:
   Phone Login:
   Phone Password:
   User:
   Passwd:

Your System Settings must have Agent API enabled.
Your user must have API enabled.


NOTE: If for any reason you want to wipe all traces of this plugin from your
system, first uninstall the plugin, then close Firefox, then erase the 
associated preferences in the prefs.js file for that Firefox profile (This is
in different places depending on the version of Firefox and your Operating 
System).
