package vicidial;

# viciagi.pm version 2.0.5
#
# Experimental library for general vicidial things
#
# This library has not been tested in a production level enviroment.
#
# Copyright (C) 2008  VICIDIAL-GROUP  <info@vicidial-group.com> LICENSE: AGPLv2
#
# CHANGELOG:
# 80326-1115 - First test build

# Export our symbols
require Exporter;
@ISA = qw(Exporter);
@EXPORT = qw(agi_log);

# USE FLAGS
use 5.008;
use strict;
use warnings;

use Asterisk::AGI;

# function to perform logging for agi scripts
#    ARG0 is the logging level of the script (0-3)
#    ARG1 is the time stamp to go along with this log
#    ARG2 is the name of the script that is currently logging
#    ARG3 is the stage that script is currently in
#    ARG4 is the log file's name
#    ARG5 is the string that is going into the log
#    this function does not return a value
sub agi_output {
	my $agi_log_level       = $_[0];
	my $now_date            = $_[1];
	my $script_name         = $_[2];
	my $process_stage       = $_[3];
	my $log_file_name       = $_[4];
	my $log_string          = $_[5];

	my $write_string = "$now_date|$script_name|$process_stage|$log_string";

	# loggin to STDERR
	if ( ( $agi_log_level == '1' ) || ( $agi_log_level == '3' ) ) {
		print STDERR "$write_string\n";
	}

	# logging to file
	if ( $agi_log_level >= 2 ) {
		
		# open the log file for writing
		my $file_opened = 1;
		(open( Lout, ">>$log_file_name" )) || ($file_opened = 0);
		if ( $file_opened ) {	# if the file was opened print to it and then close
			print Lout "$write_string\n";
	                close(Lout);
		} else {	# else print a message to STDERR
			print STDERR "Can't open $log_file_name to write $write_string: $!\n";
	        }
	}
}
