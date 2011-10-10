#!/usr/bin/perl
#
# sheet2tab.pl - Convert spreadsheet to tab-delimited text file   version 2.4
#
# Copyright (C) 2010  Matt Florell & Michael Cargile <vicidial@gmail.com>    LICENSE: AGPLv2
#
# Lead file conversion and scrubbing script.  This is the first stage in the lead loading process.
# 
# *Stage 1 - Convert file to a tab delimited format (DONE)
#  Stage 2 - Prompt the user for the field mapping (TBD)
#  Stage 3 - Schedule a List Load by the command line list loader (TBD)
#
# This particular script converts csv xls xlsx ods sxc files to a tab delimited file. In the
# process it scrubs out ' " ; ` \ characters which could cause problems with db insertion of
# lead data.  It also replaces pipes, tabs, carrage returns, and line feeds with spaces to prevent
# stage 3 from miscounting the number of fields on a line.
#
# ARG1 = File to Convert
# ARG2 = Name of the output file
#
# This file requires the Spreadsheet::Read and Spreadsheet::XLSX perl modules from CPAN
#
# cpan> install Spreadsheet::Read
# cpan> install Spreadsheet::XLSX
#
# CHANGES
# 100706-0833 - Initial build <mikec>
# 100706-1244 - Reformat and add comments
#

# disable when not debugging
#use strict;
#use warnings;

use Spreadsheet::Read;

sub scrub_lead_field 
	{
	my $lead_field = $_[0];

	# remove bad characters
	$lead_field	=~ s/\'|\\|\"|;|\`|\224//gi;
	
	# replace tabs and newlines with spaces
	$lead_field	=~ s/\n|\r|\t|\174/ /gi;

	return $lead_field;
	}

my $infile;
my $outfile;

if ( $#ARGV == 1 ) 
	{
	$infile = $ARGV[0]; 
	$outfile = $ARGV[1];
	} 
else
	{
	print STDERR "Incorrect number of arguments\n";
	exit(1);
	}

open( OUTFILE , ">$outfile" ) or die $!;

my $debug = 0;

my $out_delim = "\t";

my $count = 0;

my $colPos = 0;
my $rowPos = 0;

# parse the csv file
my $parser = ReadData ( "$infile" );

my $maxCol = $parser->[1]{maxcol};
my $maxRow = $parser->[1]{maxrow};

if ($debug) { print STDERR "maxCol = '$maxCol'\n"; };
if ($debug) { print STDERR "maxRow = '$maxRow'\n"; };

# loop through the rows
for ( $rowPos = 1; $rowPos <= $maxRow; $rowPos++  ) 
	{
	# loop through the cols
	for ( $colPos = 1; $colPos <= $maxCol; $colPos++  ) 
		{
		my $cell = cr2cell( $colPos, $rowPos );

		if ($debug) { print STDERR "cell = '$cell'\n"; };

		my $field;

		# make sure the field has a value
		if ( $parser->[1]{$cell} ) 
			{
			$field = $parser->[1]{$cell};
			} 
		else 
			{
			$field = "";
			}

		if ($debug) { print STDERR "field = '$field'\n"; };

		$field = scrub_lead_field( $field );

		print OUTFILE $field;

		if ( $colPos < $maxCol ) 
			{
			print OUTFILE $out_delim;
			} 
		else 
			{
			print OUTFILE "\n";
			}
		}
	}

exit;
