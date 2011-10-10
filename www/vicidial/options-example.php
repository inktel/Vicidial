<?php
# options.php - manually defined options for vicidial admin scripts
# 
# Copyright (C) 2011  Matt Florell <vicidial@gmail.com>    LICENSE: AGPLv2
#
# rename this file to options.php for the settings here to go into effect
#
# CHANGELOG
# 101216-1043 - First Build 
# 110307-1039 - Added upper-case/lower-case user setting
#

# used by the realtime_report.php script
$webphone_width =	'460';
$webphone_height =	'500';
$webphone_left =	'600';
$webphone_top =		'27';
$webphone_bufw =	'250';
$webphone_bufh =	'1';
$webphone_pad =		'10';
$webphone_clpos =	"<BR>  &nbsp; <a href=\"#\" onclick=\"hideDiv('webphone_content');\">webphone -</a>";

# example using thin webphone
#$webphone_width =       '1135';
#$webphone_height =      '36';
#$webphone_left =        '0';
#$webphone_top =         '50';
#$webphone_bufw =        '1300';
#$webphone_bufh =        '37';
#$webphone_pad =         '0';
#$webphone_clpos =       ' ';

# used by the realtime report
$RS_DB =				0;		# 1=debug on, 0=debug off
$RS_RR =				40;		# refresh rate
$RS_group =				'ALL-ACTIVE';	# selected campaign(s)
$RS_usergroup =			'';		# user group defined
$RS_UGdisplay =			0;		# 0=no, 1=yes
$RS_UidORname =			1;		# 0=id, 1=name
$RS_orderby =			'timeup';
$RS_SERVdisplay =		0;	# 0=no, 1=yes
$RS_CALLSdisplay =		1;	# 0=no, 1=yes
$RS_PHONEdisplay =		0;	# 0=no, 1=yes
$RS_CUSTPHONEdisplay =	0;	# 0=no, 1=yes
$RS_PAUSEcodes =		'N';
$RS_with_inbound =		'Y';
$RS_CARRIERstats =		0;	# 0=no, 1=yes
$RS_PRESETstats =		0;	# 0=no, 1=yes
$RS_AGENTtimeSTATS =	0;	# 0=no, 1=yes

# used by agent reports
$user_case =			0;		# 1=upper-case, 2-lower-case, 0-no-case-change

?>
