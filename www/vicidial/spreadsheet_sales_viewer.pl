#!/usr/bin/perl
#
# spreadsheet_sales_viewer.pl    version 2.0.5
# 
# Copyright (C) 2008  Joe Johnson,Matt Florell <vicidial@gmail.com>    LICENSE: AGPLv2
#
#
# CHANGES
# 70521-1734 - First build
# 70522-1554 - Changed to use DBI instead of Net::MySQL
#

use Spreadsheet::WriteExcel;
use Time::Local;
use DBI;

# default path to astguiclient configuration file:
$PATHconf =		'/etc/astguiclient.conf';

open(conf, "$PATHconf") || die "can't open $PATHconf: $!\n";
@conf = <conf>;
close(conf);
$i=0;
foreach(@conf)
	{
	$line = $conf[$i];
	$line =~ s/ |>|\n|\r|\t|\#.*|;.*//gi;
	if ( ($line =~ /^PATHhome/) && ($CLIhome < 1) )
		{$PATHhome = $line;   $PATHhome =~ s/.*=//gi;}
	if ( ($line =~ /^PATHlogs/) && ($CLIlogs < 1) )
		{$PATHlogs = $line;   $PATHlogs =~ s/.*=//gi;}
	if ( ($line =~ /^PATHagi/) && ($CLIagi < 1) )
		{$PATHagi = $line;   $PATHagi =~ s/.*=//gi;}
	if ( ($line =~ /^PATHweb/) && ($CLIweb < 1) )
		{$PATHweb = $line;   $PATHweb =~ s/.*=//gi;}
	if ( ($line =~ /^PATHsounds/) && ($CLIsounds < 1) )
		{$PATHsounds = $line;   $PATHsounds =~ s/.*=//gi;}
	if ( ($line =~ /^PATHmonitor/) && ($CLImonitor < 1) )
		{$PATHmonitor = $line;   $PATHmonitor =~ s/.*=//gi;}
	if ( ($line =~ /^VARserver_ip/) && ($CLIserver_ip < 1) )
		{$VARserver_ip = $line;   $VARserver_ip =~ s/.*=//gi;}
	if ( ($line =~ /^VARDB_server/) && ($CLIDB_server < 1) )
		{$VARDB_server = $line;   $VARDB_server =~ s/.*=//gi;}
	if ( ($line =~ /^VARDB_database/) && ($CLIDB_database < 1) )
		{$VARDB_database = $line;   $VARDB_database =~ s/.*=//gi;}
	if ( ($line =~ /^VARDB_user/) && ($CLIDB_user < 1) )
		{$VARDB_user = $line;   $VARDB_user =~ s/.*=//gi;}
	if ( ($line =~ /^VARDB_pass/) && ($CLIDB_pass < 1) )
		{$VARDB_pass = $line;   $VARDB_pass =~ s/.*=//gi;}
	if ( ($line =~ /^VARDB_port/) && ($CLIDB_port < 1) )
		{$VARDB_port = $line;   $VARDB_port =~ s/.*=//gi;}
	$i++;
	}

# Customized Variables
$server_ip = $VARserver_ip;		# Asterisk server IP

if (!$VARDB_port) {$VARDB_port='3306';}

$dbhA = DBI->connect("DBI:mysql:$VARDB_database:$VARDB_server:$VARDB_port", "$VARDB_user", "$VARDB_pass")
 or die "Couldn't connect to database: " . DBI->errstr;

$list_ids=$ARGV[0];
$sales_number=$ARGV[1];
$timestamp=$ARGV[2];
$forc=$ARGV[3];
$now=$ARGV[4];
$dcampaign=$ARGV[5];

$list_id_clause="and v.list_id in (";
@lists=split(/\,/, $list_ids);
for ($i=0; $i<scalar(@lists); $i++) {
	if (length($lists[$i]>0)) {	$list_id_clause.="$lists[$i], "; }
}
$list_id_clause=substr($list_id_clause, 0, -2);
$list_id_clause.=")";


if ($sales_number==0) {undef $sales_number;}


$xl = Spreadsheet::WriteExcel->new("$PATHweb/vicidial/vicidial_closer_report_$now.xls");
$xlsheet = $xl->add_worksheet();
$xlsheet->set_landscape();
$rptheader = $xl->add_format(); # Add a format
$rptheader->set_bold();
$rptheader->set_size('8');
$rptheader->set_color('10');
$rptheader->set_align('center');

$rptheaderLEFT = $xl->add_format(); # Add a format
$rptheaderLEFT->set_bold();
$rptheaderLEFT->set_size('8');
$rptheaderLEFT->set_color('10');
$rptheaderLEFT->set_align('left');

$normcell = $xl->add_format(); # Add a format
$normcell->set_size('8');
$normcell->set_align('center');
$normcell->set_bg_color('22');

$boldcell = $xl->add_format(); # Add a format
$boldcell->set_bold();
$boldcell->set_size('8');
$boldcell->set_align('center');
$boldcell->set_bg_color('22');

$dollarformat = $xl->add_format(); # Add a format
$dollarformat->set_size('8');
$dollarformat->set_num_format('$0.00');

$intformat = $xl->add_format(); # Add a format
$intformat->set_size('8');
$intformat->set_num_format('0');

$numberformat = $xl->add_format(); # Add a format
$numberformat->set_size('8');
$numberformat->set_num_format('0.00');

$pformat = $xl->add_format(); # Add a format
$pformat->set_size('8');
$pformat->set_num_format('0.00%');

$p2format = $xl->add_format(); # Add a format
$p2format->set_size('8');
$p2format->set_num_format('0%');

$statcell = $xl->add_format(num_format => '@'); # Add a format
$statcell->set_size('8');

$countcell = $xl->add_format(); # Add a format
$countcell->set_size('8');
$countcell->set_bg_color('35');

$terminateheader = $xl->add_format(); # Add a format
$terminateheader->set_bold();
$terminateheader->set_size('8');
$terminateheader->set_color('10');
$terminateheader->set_num_format('0.00');

$xlsheet->write("B1", "Closer stats", $rptheader);
$xlsheet->write("D1", "$dcampaign", $rptheaderLEFT);
$xlsheet->write("F1", "$list_ids", $rptheaderLEFT);
$xlsheet->write("A2", "Closer Name", $rptheader);
$xlsheet->write("B2", "# of calls", $rptheader);
$xlsheet->write("C2", "# of sales", $rptheader);
$xlsheet->write("D2", "Sales percentage", $rptheader);
$xlsheet->write("E2", "Calls per hour", $rptheader);
$xlsheet->write("F2", "Sales per hour", $rptheader);
$xlsheet->write("G2", "Total time in system", $rptheader);

%closers=();
%fronters=();



# Non-transfer
$sales=0;
$stmtA = "select v.status, u.full_name, u.user from vicidial_users u, vicidial_list v, vicidial_log vl where vl.call_date>='$timestamp' and vl.call_date<='$now' and vl.lead_id=v.lead_id $list_id_clause and vl.user=u.user;";
$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
$sthArows=$sthA->rows;
$rec_count=0;
while ($sthArows > $rec_count)
	{
	@aryA = $sthA->fetchrow_array;
	if (length($sales_number)==0 || $sales<$sales_number) 
		{
		$closers{"$aryA[1]"}[0]++;
		$fronters{"$aryA[1]"}[0]++;
		if ($aryA[0] eq "SALE") 
			{
			$sales++;
			$closers{"$aryA[1]"}[1]++;
			$fronters{"$aryA[1]"}[1]++;
			}
		}
	$closers{"$aryA[1]"}[2]=$aryA[2];
	$fronters{"$aryA[1]"}[2]=$aryA[2];
	$rec_count++;
	}
$sthA->finish();


if ($forc eq "F") {
	%closers=();

	$sales=0;
	$stmtA = "select v.status, u.full_name, u.user from vicidial_list v, vicidial_users u, vicidial_xfer_log vl where vl.call_date>='$timestamp' and vl.call_date<='$now' and vl.lead_id=v.lead_id $list_id_clause and vl.closer=u.user;";
	$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
	$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
	$sthArows=$sthA->rows;
	$rec_count=0;
	while ($sthArows > $rec_count)
		{
		@aryA = $sthA->fetchrow_array;
		if (!$sales_number || $sales<$sales_number) 
			{
			$closers{"$aryA[1]"}[0]++;
			if ($aryA[0] eq "SALE") 
				{
				$sales++;
				$closers{"$aryA[1]"}[1]++;
				}
			}
		$closers{"$aryA[1]"}[2]=$aryA[2];
		$rec_count++;
		}
	$sthA->finish();
}


$x=3;
$grand_total_time=0;
foreach $closername (sort(keys(%closers))) 
{
	$closers{$closername}[0]+=0;
	$closers{$closername}[1]+=0;

	$stmtA = "select sum(pause_sec+wait_sec+talk_sec), sec_to_time(sum(pause_sec+wait_sec+talk_sec)) from vicidial_agent_log where user=".$closers{$closername}[2]." and event_time>='$timestamp' and event_time<='$now' and pause_sec<28800 and wait_sec<28800 and talk_sec<28800;";
	$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
	$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
	$sthArows=$sthA->rows;
	$rec_count=0;
	while ($sthArows > $rec_count)
		{
		@aryA = $sthA->fetchrow_array;
		$hours=($aryA[0]/3600);
		$grand_total_time+=$aryA[0];
		$total_time=$aryA[1];
		$rec_count++;
		}
	$sthA->finish();
	$xlsheet->write("A$x", "$closername", $normcell);
	$xlsheet->write("B$x", "=$closers{$closername}[0]+0", $intformat);
	$xlsheet->write("C$x", "=$closers{$closername}[1]+0", $intformat);
	$xlsheet->write("D$x", "=C$x/B$x", $pformat);
	$xlsheet->write("E$x", "=B$x/$hours", $numberformat);
	$xlsheet->write("F$x", "=C$x/$hours", $numberformat);
	$xlsheet->write("G$x", "$total_time", $normcell);
	$x++;
}
$hours=($grand_total_time/3600);

$stmtA = "select sec_to_time($grand_total_time)";
$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
$sthArows=$sthA->rows;
$rec_count=0;
while ($sthArows > $rec_count)
	{
	@aryA = $sthA->fetchrow_array;
	$grand_total_time=$aryA[0];
	$rec_count++;
	}
$sthA->finish();


$y=($x+1);
$xlsheet->write("A$y", "Grand Total", $boldcell);
$xlsheet->write("B$y", "=SUM(B3:B$x)", $intformat);
$xlsheet->write("C$y", "=SUM(C3:C$x)", $intformat);
$xlsheet->write("D$y", "=C$y/B$y", $pformat);
$xlsheet->write("E$y", "=B$y/$hours", $numberformat);
$xlsheet->write("F$y", "=C$y/$hours", $numberformat);
$xlsheet->write("G$y", "$grand_total_time", $normcell);
$xl->close();


if ($forc eq "F") {
	$xl = Spreadsheet::WriteExcel->new("$PATHweb/vicidial/vicidial_fronter_report_$now.xls");
	$xlsheet = $xl->add_worksheet();
	$xlsheet->set_landscape();
	$rptheader = $xl->add_format(); # Add a format
	$rptheader->set_bold();
	$rptheader->set_size('8');
	$rptheader->set_color('10');
	$rptheader->set_align('center');

	$normcell = $xl->add_format(); # Add a format
	$normcell->set_size('8');
	$normcell->set_align('center');
	$normcell->set_bg_color('22');

	$boldcell = $xl->add_format(); # Add a format
	$boldcell->set_bold();
	$boldcell->set_size('8');
	$boldcell->set_align('center');
	$boldcell->set_bg_color('22');

	$dollarformat = $xl->add_format(); # Add a format
	$dollarformat->set_size('8');
	$dollarformat->set_num_format('$0.00');

	$intformat = $xl->add_format(); # Add a format
	$intformat->set_size('8');
	$intformat->set_num_format('0');

	$numberformat = $xl->add_format(); # Add a format
	$numberformat->set_size('8');
	$numberformat->set_num_format('0.00');

	$pformat = $xl->add_format(); # Add a format
	$pformat->set_size('8');
	$pformat->set_num_format('0.00%');

	$statcell = $xl->add_format(num_format => '@'); # Add a format
	$statcell->set_size('8');

	$countcell = $xl->add_format(); # Add a format
	$countcell->set_size('8');
	$countcell->set_bg_color('35');

	$terminateheader = $xl->add_format(); # Add a format
	$terminateheader->set_bold();
	$terminateheader->set_size('8');
	$terminateheader->set_color('10');
	$terminateheader->set_num_format('0.00');

	$xlsheet->write("D1", "Fronter stats", $rptheader);
	$xlsheet->write("A2", "Fronter Name", $rptheader);	
	$xlsheet->write("B2", "# of calls", $rptheader);
	$xlsheet->write("C2", "# of sales", $rptheader);
	$xlsheet->write("D2", "Sales percentage", $rptheader);
	$xlsheet->write("E2", "Calls per hour", $rptheader);
	$xlsheet->write("F2", "Sales per hour", $rptheader);
	$xlsheet->write("G2", "Total time in system", $rptheader);

	$x=3;
	$grand_total_time=0;
	foreach $tsrname (sort(keys(%fronters))) {
		$fronters{$tsrname}[0]+=0;
		$fronters{$tsrname}[1]+=0;
		$stmtA = "select sum(pause_sec+wait_sec+talk_sec), sec_to_time(sum(pause_sec+wait_sec+talk_sec)) from vicidial_agent_log where user=".$fronters{$tsrname}[2]." and event_time>='$timestamp' and event_time<='$now' and pause_sec<28800 and wait_sec<28800 and talk_sec<28800;";
		$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
		$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
		$sthArows=$sthA->rows;
		$rec_count=0;
		while ($sthArows > $rec_count)
			{
			@aryA = $sthA->fetchrow_array;
			$hours=($aryA[0]/3600);
			$grand_total_time+=$aryA[0];
			$total_time=$aryA[1];
			$rec_count++;
			}
		$sthA->finish();

		$xlsheet->write("A$x", "$tsrname", $normcell);
	    $xlsheet->write("B$x", "=$fronters{$tsrname}[0]+0", $intformat);
	    $xlsheet->write("C$x", "=$fronters{$tsrname}[1]+0", $intformat);
	    $xlsheet->write("D$x", "=C$x/B$x", $pformat);
	    $xlsheet->write("E$x", "=B$x/$hours", $numberformat);
	    $xlsheet->write("F$x", "=C$x/$hours", $numberformat);
	    $xlsheet->write("G$x", "$total_time", $normcell);
	    $x++;
	}
	$hours=($grand_total_time/3600);

	$stmtA = "select sec_to_time($grand_total_time)";
	$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
	$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
	$sthArows=$sthA->rows;
	$rec_count=0;
	while ($sthArows > $rec_count)
		{
		@aryA = $sthA->fetchrow_array;
		$grand_total_time=$aryA[0];
		$rec_count++;
		}
	$sthA->finish();

	$y=($x+1);
	$xlsheet->write("A$y", "Grand Total", $boldcell);
	$xlsheet->write("B$y", "=SUM(B3:B$x)", $intformat);
	$xlsheet->write("C$y", "=SUM(C3:C$x)", $intformat);
	$xlsheet->write("D$y", "=C$y/B$y", $pformat);
	$xlsheet->write("E$y", "=B$y/$hours", $numberformat);
	$xlsheet->write("F$y", "=C$y/$hours", $numberformat);
	$xlsheet->write("G$y", "$grand_total_time", $normcell);

	$xl->close();
}

