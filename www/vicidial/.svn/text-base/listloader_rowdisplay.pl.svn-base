#!/usr/bin/perl
#
# listloader_rowdisplay.pl    version 2.2.0
# 
# Copyright (C) 2009  Matt Florell,Joe Johnson <vicidial@gmail.com>    LICENSE: AGPLv2
#
#
# CHANGES
# 
# 60811-1232 - Changed to DBI
# 60811-1329 - changed to use /etc/astguiclient.conf for configs
# 90721-1340 - Added rank and owner as vicidial_list fields
#

### begin parsing run-time options ###
if (length($ARGV[0])>1)
{
	$i=0;
	while ($#ARGV >= $i)
	{
	$args = "$args $ARGV[$i]";
	$i++;
	}

	if ($args =~ /--help|-h/i)
	{
	print "allowed run time options:\n  [-forcelistid=1234] = overrides the listID given in the file with the 1234\n  [-h] = this help screen\n\n";

	exit;
	}
	else
	{
		if ($args =~ /-duplicate-check/i)
			{$dupcheck=1;}
		if ($args =~ /-postal-code-gmt/i)
			{$postalgmt=1;}
		if ($args =~ /--lead-file=/i)
		{
		@data_in = split(/--lead-file=/,$args);
			$lead_file = $data_in[1];
			$lead_file =~ s/ .*//gi;
	#	print "\n----- LEAD FILE: $lead_file -----\n\n";
		}
		else
			{$lead_file = './vicidial_temp_file.xls';}
	}
}
### end parsing run-time options ###

use Spreadsheet::ParseExcel;
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


$oBook = Spreadsheet::ParseExcel::Workbook->Parse("$lead_file");
my($iR, $iC, $oWkS, $oWkC);
$var_str="";

foreach $oWkS (@{$oBook->{Worksheet}}) {
	for(my $iC = $oWkS->{MinCol} ; defined $oWkS->{MaxCol} && $iC <= $oWkS->{MaxCol} ; $iC++) {
		$oWkC = $oWkS->{Cells}[0][$iC];
		if ($oWkC) {
			$var_str.=$oWkC->Value."|"; 
		} else {
			$var_str.="|"; 
		}
	}
}

@xls_row=split(/\|/, $var_str);


$stmtA = "select vendor_lead_code, source_id, list_id, phone_code, phone_number, title, first_name, middle_initial, last_name, address1, address2, address3, city, state, province, postal_code, country_code, gender, date_of_birth, alt_phone, email, security_phrase, comments, rank, owner from vicidial_list limit 1;";
$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
$sthArows=$sthA->rows;
$rec_count=0;
while ($sthArows > $rec_count)
	{
	my $names = $sthA->{'NAME'};
	  my $numFields = $sthA->{'NUM_OF_FIELDS'};
	  for (my $i = 0;  $i < $numFields;  $i++) 
		{
	#	printf("%s%s", $i ? "," : "", $$names[$i]);
	#	printf("%s%s", $i ? "," : "", $$ref[$i]);

		$field_name=uc($$names[$i]);
		$field_name=~s/\_/ /g;
		print "  <tr bgcolor=#D9E6FE>\r\n";
		print "    <th><font class=standard>".$field_name.": </font></td>\r\n";
		print "    <th><select name='".$$names[$i]."_field'>\r\n";
		print "     <option value='9999'>---------------------</option>\r\n";

		for ($j=0; $j<scalar(@xls_row); $j++) 
			{
			$xls_row[$j]=~s/\"//g;
			print "     <option value='$j'>\"$xls_row[$j]\"</option>\r\n";
			}

		print "    </select></td>\r\n";
		print "  </tr>\r\n";
		}
	$rec_count++;
	}
$sthA->finish();

exit;
