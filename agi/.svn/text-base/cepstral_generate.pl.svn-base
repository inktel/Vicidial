#!/usr/bin/perl
#
# cepstral_generate.pl    version 2.2.0
#
# Text-To-Speech files will be cached for later use.
#
# Call this script like this:
# /var/lib/asterisk/agi-bin/cepstral_generate.pl --debug --voice=Allison-8kHz --dialog=Hello\ There
#
# Copyright (C) 2009  Matt Florell, Mike Cargile <vicidial@gmail.com>    LICENSE: AGPLv2
#
# CHANGES:
# 90406-1058 - First Build
# 90410-1458 - Fixed generate bug, added conf path variables
#

# Cepstral paths
$PATHvoices = '/tmp/swift_voices';
$PATHswift = '/opt/swift/bin/swift';

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
	if ( ($line =~ /^PATHagi/) && ($CLIagi < 1) )
		{$PATHagi = $line;   $PATHagi =~ s/.*=//gi;}
	if ( ($line =~ /^PATHsounds/) && ($CLIsounds < 1) )
		{$PATHsounds = $line;   $PATHsounds =~ s/.*=//gi;}
	$i++;
	}


### begin parsing run-time options ###
if (length($ARGV[0])>1)
	{
	$i=0;
	while ($#ARGV >= $i)
		{
		$args = "$args $ARGV[$i]";
		$i++;
		}

	if ($args =~ /--help/i)
		{
		print "allowed run time options:\n";
		print "  [-t] = test\n";
		print "  [-debug] = verbose debug messages\n";
		print "  [-debugX] = Extra-verbose debug messages\n";
		print "  [-voice=Allison-8kHz] = Cepstral voice to use(Allison-8kHz is default)\n\n";
		print "  [-dialog=Hello] = Message to generate\n\n";
		print "   (This must be the LAST option)\n\n";
		}
	else
		{
		if ($args =~ /-debug/i)
			{
			$DB=1; # Debug flag
			}
		if ($args =~ /--debugX/i)
			{
			$DBX=1;
			print "\n----- SUPER-DUPER DEBUGGING -----\n\n";
			}
		if ($args =~ /-t/i)
			{
			$TEST=1;
			$T=1;
			}
		if ($args =~ /-voice=/i)
			{
			@data_in = split(/-voice=/,$args);
				$voice = $data_in[1];
				$voice =~ s/ .*//gi;
			if ($DBX > 0) {print "\n----- VOICE: $voice -----\n\n";}
			}
		else
			{$voice = 'Allison-8kHz';}
		if ($args =~ /-dialog=/i)
			{
			@data_in = split(/-dialog=/,$args);
				$dialog = $data_in[1];
				$dialog =~ s/\n|\r|\l|\t//gi;
			if ($DBX > 0) {print "\n----- DIALOG: $dialog -----\n\n";}
			}
		else
			{$dialog = '';}
		}
	}
else
	{
	#	print "no command line options set\n";
	}
### end parsing run-time options ###

($sec,$min,$hour,$mday,$mon,$year,$wday,$yday,$isdst) = localtime(time);
$year = ($year + 1900);
$mon++;
if ($hour < 10) {$hour = "0$hour";}
if ($min < 10) {$min = "0$min";}
if ($sec < 10) {$sec = "0$sec";}
if ($mon < 10) {$mon = "0$mon";}
if ($mday < 10) {$mday = "0$mday";}
$SQL_date = "$year-$mon-$mday $hour:$min:$sec";

use Digest::MD5 qw(md5_hex);
use Asterisk::AGI;
$AGI = new Asterisk::AGI;

#my @voice = &get_cep_voice(2);

if (length($dialog) > 1)
	{
	$enc = md5_hex("$dialog-$voice");	# the hash
	$enc_ftl = substr($enc, 0, 2);	# first letter of hash
	$enc_file = "tts/" . $enc_ftl . "/tts-" . $enc . ".wav";

	if ($DB > 0)
		{print "$SQL_date - Creating $dialog - $voice   file: $enc_file\n";}
#	&say_cepstral("$dialog","$voice");
	&gen_cepstral("$dialog","$voice");
	}

exit;

# EXAMPLES
#&say_cepstral("Four score and seven years ago our fathers brought forth on this continent, a new nation, conceived in Liberty, and dedicated to the proposition that all men are created equal. Now we are engaged in a great civil war, testing whether that nation, or any nation so conceived and so dedicated, can long endure. We are met on a great battle-field of that war. We have come to dedicate a portion of that field, as a final resting place for those who here gave their lives that that nation might live. It is altogether fitting and proper that we should do this. But, in a larger sense, we can not dedicate -- we can not consecrate -- we can not hallow -- this ground. The brave men, living and dead, who struggled here, have consecrated it, far above our poor power to add or detract. The world will little note, nor long remember what we say here, but it can never forget what they did here. It is for us the living, rather, to be dedicated here to the unfinished work which they who fought here have thus far so nobly advanced. It is rather for us to be here dedicated to the great task remaining before us -- that from these honored dead we take increased devotion to that cause for which they gave the last full measure of devotion -- that we here highly resolve that these dead shall not have died in vain -- that this nation, under God, shall have a new birth of freedom -- and that government of the people, by the people, for the people, shall not perish from the earth.",$voice[0]);
#&say_cepstral("Welcome to the VEECHEE DIAL open source call center suite",$voice[0]);

# function to get a voice that is currently installed
# takes and integer
# the integer needs to be greater than 0
# returns an array with the following info in this order
# Voice
# Version
# Lic?
# Gender
# Age
# Language
# Sample Rate
sub get_cep_voice {
	my $number = $_[0];
	if ($number <= 0) {
		return 0;	# failure
	}

	my $cepstral_app = "$PATHswift";

	my $command = $cepstral_app.' --voices | tail +8 | tr -d " " > '.$PATHvoices;

	system( $command );	# Ask swift for the voices

	my @voice;
	my @voice_lines;
	my $voice_count;

	open(VFILE,"$PATHvoices");	# read in the file with the voices
	@voice_lines = <VFILE>;
	close(VFILE);
	#unlink("/tmp/swift_vioces");	# delete the file with the voices

	# find the correct voice
	foreach my $voice_line(@voice_lines) {
		chomp($voice_line);
		$voice_count++;
		if ($voice_count == $number) {
			@voice = split(/\|/,$voice_line);
		}
	}
	
	# return it
	return @voice;
}

# Function to return the number of voices that are installed
# Returns an int
sub get_cep_num_voices {
	my $cepstral_app = "$PATHswift";

        my $command = $cepstral_app.' --voices | tail +8 | tr -d " " > '.$PATHvoices;

        system( $command );	# Ask swift for the voices

        my @voice_lines;
	my $number = 0;

	# read in the file with the voices
        open(VFILE,"$PATHvoices");
        @voice_lines = <VFILE>;
        close(VFILE);
        #unlink("/tmp/swift_vioces");	# delete it

        foreach (@voice_lines) {
		$number++;
        }	

	return $number;
}

# Function to pregenerate TTS file
# First option is the text
# Second option is the voice
sub gen_cepstral {
	my $text = $_[0];	# the text to play
	my $voice = $_[1];	# voice to use
	
	# hash of the text 
	my $hash = md5_hex("$text-$voice");	# the hash
	my $hash_ftl = substr($hash, 0, 2);	# first two letters of hash
	
	# Directories that hold everything
	my $astsounddir = $PATHsounds; 	# asterisk sound directory
	my $ttssounddir = $astsounddir."/tts";
	my $astsubdir = "tts/".$hash_ftl; 		# sub directory that holds the file
	my $sounddir = $astsounddir . "/" . $astsubdir; 	# full path to the directory

	# Wave File to play
	my $wavefile = "tts-".$hash; 			# wave file without the .wav at the end
	my $wavepath = $sounddir."/".$wavefile.".wav"; 	# full path to the wavefile
	my $astwavpath = $astsubdir."/".$wavefile; 	# asterisk path to .wav file without .wav
	
	# text file of words to say
	my $textfile = "tts-text-".$hash.".txt";	# file to hold the words to say
	my $textpath = $sounddir."/".$textfile;		# full path to the text file

	# generate the tts
	if (!(&real_gen_cepstral($text, $voice, $ttssounddir, $sounddir, $wavepath, $textpath))) {
		#TODO print an error message somewhere
		return 0; # failure
	}	

	return 1; # success
}

# Function to play TTS file
# Will generate TTS file if it is not pregenerated
# First option is the text
# Second option is the voice
sub say_cepstral {
	my $text = $_[0];	# the text to play
	my $voice = $_[1];	# voice to use

	my %input = $AGI->ReadParse(); 

	# hash of the text
        my $hash = md5_hex("$text-$voice");	# the hash
        my $hash_ftl = substr($hash, 0, 2);	# first letter of hash

        # Directories that hold everything
        my $astsounddir = $PATHsounds;   # asterisk sound directory
        my $ttssounddir = $astsounddir."/tts";
		my $astsubdir = "tts/".$hash_ftl;                # sub directory that holds the file
        my $sounddir = $astsounddir."/".$astsubdir;     # full path to the directory

        # Wave File to play
        my $wavefile = "tts-".$hash;                    # wave file without the .wav at the end
        my $wavepath = $sounddir."/".$wavefile.".wav";  # full path to the wavefile
        my $astwavpath = $astsubdir."/".$wavefile;      # asterisk path to .wav file without .wav

        # text file of words to say
        my $textfile = "tts-text-".$hash.".txt";        # file to hold the words to say
        my $textpath = $sounddir."/".$textfile;         # full path to the text file
	
	# generate the tts
	if (!(&real_gen_cepstral($text, $voice, $ttssounddir, $sounddir, $wavepath, $textpath))) { 
		#TODO print an error message somewhere
		return 0; # failure
	}
	# wait up to a second
	# for the sound file to appear
	# and the textfile to be deleted
	# This is for concurrency issues
	my $i = 0;
	while ( ($i < 100) && !(-f $wavepath) && (-f $textpath) ) {
		usleep(10000);
		$i++;
	}

	$AGI->stream_file($astwavpath); 	# play the tts

	return 1; # success
}

# This is a helper function do not call it directly
sub real_gen_cepstral {
	my $text = $_[0];
	my $voice = $_[1];
	my $ttssounddir = $_[2];
	my $sounddir = $_[3];
	my $wavepath = $_[4];
	my $textpath = $_[5];

	
        # Cepstral commnad and options
        my $cepstral_app = "$PATHswift";      # the executable
        my $cepstral_opt = "-p speech/rate=140";                          # the command line options


	# Make sure the tts directory is there
        if (!(-d $ttssounddir)) {
                if (!(mkdir($ttssounddir))) {
                        #TODO print an error message somewhere
                        return 0; # failure - cannot make the tts directory
                }
        }

	# Make sure the sub directory is there
        if (!(-d $sounddir)) {
                if (!(mkdir($sounddir))) {
                        #TODO print an error message somewhere
                        return 0; # failure - cannot make the sub directory
                }
        }

        # check if both the wav file and the text file do not exists
        if (!( -f $wavepath ) && !( -f $textpath )) {
                # write to the text file whats to be said
                open(fileOUT, ">$textpath");
                print fileOUT "$text";
                close(fileOUT);

                # build the command to call cepstral and give it the text file to generate
                my $command=$cepstral_app." ".$cepstral_opt." -n ".$voice." -o ".$wavepath." -f ".$textpath." > /dev/null";

                # execute the command
                system($command);

                # delete the text file
                unlink($textpath);
        }

        return 1; # success
}
