#!/usr/bin/perl
#
# ADMIN_restart_roll_logs.pl    version 2.2.0
#
# script to roll the Asterisk logs on machine restart
#
# have this run on the astersik server 
#
# Copyright (C) 2009  Matt Florell <vicidial@gmail.com>    LICENSE: AGPLv2
#
# CHANGES:
# 90311-0921 - Added /var/log/asterisk/screenlog log rolling
# 90508-0535 - Changes root screenlog to /var/log/astguiclient
#

($sec,$min,$hour,$mday,$mon,$year,$wday,$yday,$isdst) = localtime(time);
$year = ($year + 1900);
$mon++;
if ($mon < 10) {$mon = "0$mon";}
if ($mday < 10) {$mday = "0$mday";}
if ($hour < 10) {$Fhour = "0$hour";}
if ($min < 10) {$min = "0$min";}
if ($sec < 10) {$sec = "0$sec";}

$now_date_epoch = time();
$now_date = "$year-$mon-$mday---$hour$min$sec";


print "rolling Asterisk messages log...\n";
`mv -f /var/log/asterisk/messages /var/log/asterisk/messages.$now_date`;

print "rolling Asterisk event log...\n";
`mv -f /var/log/asterisk/event_log /var/log/asterisk/event_log.$now_date`;

print "rolling Asterisk cdr logs...\n";
`mv -f /var/log/asterisk/cdr-csv/Master.csv /var/log/asterisk/cdr-csv/Master.csv.$now_date`;
`mv -f /var/log/asterisk/cdr-custom/Master.csv /var/log/asterisk/cdr-custom/Master.csv.$now_date`;

print "rolling Asterisk screen log...\n";
`mv -f /var/log/astguiclient/screenlog.0 /var/log/astguiclient/screenlog.0.$now_date`;

print "rolling Asterisk root screen log...\n";
`mv -f /root/screenlog.0 /root/screenlog.0.$now_date`;

print "rolling Asterisk var log screen log...\n";
`mv -f /var/log/asterisk/screenlog.0 /var/log/asterisk/screenlog.0.$now_date`;


print "FINISHED... EXITING\n";

exit;
