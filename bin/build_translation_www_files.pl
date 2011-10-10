#!/usr/bin/perl
#
# build_translation_www_files.pl    version 2.2.0
#
# converts web pages to other language
#
# Copyright (C) 2010  Matt Florell <vicidial@gmail.com>    LICENSE: AGPLv2
#
# changes:
# 60808-1029 - Changed to use /etc/astguiclient.conf for configs
# 70402-1305 - Added error log generation for not found phrases
# 90317-0127 - Changed internationalization link for admin
# 100203-1331 - Added PATHweb override
#

############################################
# grab variables from /etc/astguiclient.conf

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

############################################

$secX = time();

# constants
$DB=1;  # Debug flag, set to 0 for no debug messages, lots of output
$US='_';
$MT[0]='';
$language_admin_file = 'language_admin.txt';
$language_file = 'language.txt';
$error_file = "language_error.txt";
$admin_error_file = "language_admin_error.txt";

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
		print "allowed run time options:\n  [-t] = test\n  [-debug] = verbose debug messages\n[--admin-only] = only translate admin pages\n[--client-only] = only translate client pages\n[--without-en] = only translate non-english\n[--language=] = which language to build, 2 letter code, defaults to es-spanish\n[--PATHweb=] = override to use this directory as source\n\n";

		exit;
		}
	else
		{
		if ($args =~ /-debug/i)
			{
			$DB=1; # Debug flag
			}
		if ($args =~ /--language/i)
			{
			$LANG=1;
			print "\n----- LANGUAGE WEB SCRIPT ONLY BUILD -----\n\n";
			}
		if ($args =~ /--language=/i)
			{
			@data_in = split(/--language=/,$args);
			@CLIlanguageX = split(/ /,$data_in[1]);
			$CLIlanguage = $CLIlanguageX[0];
			}
		else
			{$CLIlanguage = 'es';}	# default to build all languages
		if ($args =~ /--PATHweb=/i)
			{
			@PATHwebA = split(/--PATHweb=/,$args);
			@PATHwebB = split(/ /,$PATHwebA[1]);
			$PATHweb = $PATHwebB[0];
			print "\n----- WEB PATH OVERRIDE: $PATHweb -----\n\n";
			}
		if ($args =~ /-admin-only/i)
			{
			$admin_only=1; # Admin flag
			print "\n----- ADMIN PAGES ONLY BUILD -----\n\n";
			}
		if ($args =~ /-client-only/i)
			{
			$client_only=1; # Client flag
			print "\n----- CLIENT PAGES ONLY BUILD -----\n\n";
			}
		if ($args =~ /-without-en/i)
			{
			print "\n----- NON-ENGLISH ONLY BUILD -----\n\n";
			$without_en=1;
			}
		if ($args =~ /-t/i)
			{
			$TEST=1;
			$T=1;
			}
		}
	}
else
	{
	#	print "no command line options set\n";
	}
### end parsing run-time options ###

#
# path to home directory: (assumes it already exists)
$home =		$PATHhome;

# path to web root directory: (assumes it already exists)
$webroot =	$PATHweb;


if ($LANG)
	{
	if ( (!$admin_only) && (!$client_only) )
		{$admin_only=1; $client_only=1;}

	if ($admin_only==1) 
		{
		print "\n----- LANGUAGE BUILD: $CLIlanguage -----\n\n";
		$LANG_FILE_ERROR = "$PATHlogs/$CLIlanguage$US$admin_error_file";
		$LANG_FILE_ADMIN = "./translations/$CLIlanguage$US$language_admin_file";
		open(lang, "$LANG_FILE_ADMIN") || die "can't open $LANG_FILE_ADMIN: $!\n";
		@lang = <lang>;
		close(lang);
			&translate_pages;
		}

	@lang=@MT;
	@LANGUAGES=@MT;
	@FILES=@MT;
	@TRANSLATIONS=@MT;
	@TRANSLATIONS_RAW=@MT;

	if ($client_only==1) 
		{
		$LANG_FILE_ERROR = "$PATHlogs/$CLIlanguage$US$error_file";
		$LANG_FILE = "./translations/$CLIlanguage$US$language_file";
		open(lang, "$LANG_FILE") || die "can't open $LANG_FILE: $!\n";
		@lang = <lang>;
		close(lang);
			&translate_pages;
		}
	}

$secy = time();		$secz = ($secy - $secX);		$minz = ($secz/60);		# calculate script runtime so far
print "\n     - process runtime      ($secz sec) ($minz minutes)\n";
print "\n\nDONE and EXITING\n";


exit;



sub translate_pages
	{
	#***LANGUAGES***
	#en-English|es-Espaol|
	#***FILES***
	#agc/astguiclient.php
	#agc/vicidial.php
	#***TRANSLATIONS***
	#English|Ingls|
	#Spanish|Espaol|

	##### PARSE THE LANGUAGES SETTING FILE #####
	$section='';
	$i=0;
	$Lct=0; $Fct=0; $Tct=0;
	foreach(@lang)
		{
		if ($lang[$i] !~ /^\#/)
			{
			if ($lang[$i] =~ /^\*\*\*/) 
				{
				$section = $lang[$i];
				$section =~ s/\*|\n|\r//gi;
			#	print "section heading: $section: $lang[$i]";
				}
			else
				{
				if ($section =~ /LANGUAGES/)
					{
					$LANGUAGES = $lang[$i];
					$Lct++;
					}
				else
					{
					if ($section =~ /FILES/)
						{
					#	print "section: $section    line: $lang[$i]";
						$FILES[$Fct] = $lang[$i];
						$Fct++;
						}
					else
						{
						if ($section =~ /TRANSLATIONS/)
							{
						#	print "section: $section    line: $lang[$i]";
							@TRANSlineX = split(/\|/, $lang[$i]);
							$ORIGtextX = "$TRANSlineX[0]";
							$lengthX = length($ORIGtextX);
							$lengthX = sprintf("%04d",$lengthX);
							$TRANSLATIONS_RAW[$Tct] = "$lengthX|$lang[$i]";
							$Tct++;
							}
						}
					}
				}
			}
		$i++;
		}

	if ($DB)
		{
		print "LANGUAGE FILE PARSE RESULTS:   $#lang lines in file\n";
		print "LANGUAGES:    $LANGUAGES\n";
		print "FILES:        $Fct\n";
		print "TRANSLATIONS: $Tct\n";
		}

	#@TRANSLATIONS = sort { length($b) <=> length($a) } @TRANSLATIONS_RAW;
	@TRANSLATIONS = sort { $b <=> $a } @TRANSLATIONS_RAW;

	#$k=0;
	#foreach(@TRANSLATIONS)
	#	{
	#	$length = length($TRANSLATIONS[$k]);
	#	print "$TRANSLATIONS[$k]|$length|\n";  
	#	$k++;
	#	}

	##### LOOP THROUGH THE LANGUAGES
	@lang_list = split(/\|/, $LANGUAGES);
	@lang_link_list = @lang_list;
	$i=0;
	$gif = '.gif';
	foreach(@lang_list)
		{
		if (length($lang_list[$i]) > 2) 
			{
			@lang_detail  = split(/-/, $lang_list[$i]);
			$lang_abb = $lang_detail[0];
			$lang_name = $lang_detail[1];

			if ( ($without_en==1) && ($lang_abb =~ /en/) )
				{
				print "SKIPPING ENGLISH COPYING: $lang_abb|$i\n\n";
				}
			else
				{
				##### LOOP THROUGH THE FILES
				$Fct=0;
				foreach(@FILES)
					{
					$a=0;
					@file_detail  = split(/\|/, $FILES[$Fct]);
					$file_path = $file_detail[0];
					$file_name = $file_detail[1];
					$file_name =~ s/\t|\r|\n//gi;
					$file_passthru = $file_detail[2];
					$file_passthru =~ s/\t|\r|\n//gi;
					if (-e "$webroot/$file_path/$file_name")
						{
						open(file, "$webroot/$file_path/$file_name") || die "can't open $webroot/$file_path/$file_name: $!\n";
						@file = @MT;
						@file = <file>;
						close(file);

						print "File exists: ./$file_path/$file_name\n";
						if (-e "$webroot/$file_path$US$lang_abb")
							{
							print "Lang Directory exists: $webroot/$file_path$US$lang_abb\n";
							}
						else
							{
							`mkdir $webroot/$file_path$US$lang_abb`;
							`chmod 0777 $webroot/$file_path$US$lang_abb`;
							print "Lang Directory created: $webroot/$file_path$US$lang_abb\n";
							}

						if ($file_passthru < 1)
							{
							##### LOOP THROUGH THE TRANSLATIONS
							$Tct=0;
							foreach(@TRANSLATIONS)
								{
								@TRANSline = split(/\|/, $TRANSLATIONS[$Tct]);
								$ORIGtext = "$TRANSline[1]";
								$TRANStext = "$TRANSline[$i+1]";
								$LINESct=0;
								foreach(@file)
									{
									if ($file[$LINESct] !~ /^\#|^\s*function |'INCALL'|'PAUSED'|'READY'|'NEW'|xmlhttp|ILPV |ILPA |SELECT cmd_line_f|=\"SELECT|Header\(\"/)
										{
										$phrase_found_counter=0;
										if ($file[$LINESct] =~ /INTERNATIONALIZATION-LINKS-PLACEHOLDER-VICIDIAL/)
											{
											$file[$LINESct] =~ s/INTERNATIONALIZATION-LINKS-PLACEHOLDER-VICIDIAL/ILPV/g;
											$e=0;
											$gif = '.gif';
											foreach(@lang_link_list)
												{
												if (length($lang_link_list[$e]) > 2) 
													{
													@lang_list_detail  = split(/-/, $lang_link_list[$e]);
													$lang_list_abb = $lang_list_detail[0];
													$lang_list_name = $lang_list_detail[1];
													if ($lang_list_abb =~ /$lang_abb/) {$list_bgcolor = ' BGCOLOR=\"#CCFFCC\"';}
													else {$list_bgcolor = '';}
													if ($file_name =~ /admin/) {$link_file_name = 'admin.php';}
													else {$link_file_name = $file_name;}
													$file[$LINESct] .= "echo \"<TD WIDTH=100 ALIGN=RIGHT VALIGN=TOP $list_bgcolor NOWRAP><a href=\\\"../$file_path$US$lang_list_abb/$link_file_name?relogin=YES&VD_login=\$VD_login&VD_campaign=\$VD_campaign&phone_login=\$phone_login&phone_pass=\$phone_pass&VD_pass=\$VD_pass\\\">$lang_list_name <img src=\\\"../agc/images/$lang_list_abb$gif\\\" BORDER=0 HEIGHT=14 WIDTH=20></a></TD>\\n\";";
													}
												$e++;
												}
											}
										else
											{
											if ($file[$LINESct] =~ /INTERNATIONALIZATION-LINKS-PLACEHOLDER-AGC/)
												{
												$file[$LINESct] =~ s/INTERNATIONALIZATION-LINKS-PLACEHOLDER-AGC/ILPA/g;
												$e=0;
												$gif = '.gif';
												foreach(@lang_link_list)
													{
													if (length($lang_link_list[$e]) > 2) 
														{
														@lang_list_detail  = split(/-/, $lang_link_list[$e]);
														$lang_list_abb = $lang_list_detail[0];
														$lang_list_name = $lang_list_detail[1];
														if ($lang_list_abb =~ /$lang_abb/) {$list_bgcolor = ' BGCOLOR=\"#CCFFCC\"';}
														else {$list_bgcolor = '';}
														$file[$LINESct] .= "echo \"<TD WIDTH=100 ALIGN=RIGHT VALIGN=TOP $list_bgcolor NOWRAP><a href=\\\"../$file_path$US$lang_list_abb/$file_name?relogin=YES&user=\$user&pass=\$pass&phone_login=\$phone_login&phone_pass=\$phone_pass\\\">$lang_list_name <img src=\\\"../agc/images/$lang_list_abb$gif\\\" BORDER=0 HEIGHT=14 WIDTH=20></a></TD>\\n\";";
														}
													$e++;
													}
												}
											else
												{
												if ( ($lang_abb =~ /^en/) && ($Tct > 2) )
													{
													$file[$LINESct] =~ s/\.\/images\//\.\.\/agc\/images\//g;
													$file[$LINESct] =~ s/\"help.gif/\"..\/astguiclient\/help.gif/g;
													$phrase_found_counter++;
													}
												if ($lang_abb !~ /^en/)
													{
													$file[$LINESct] =~ s/$ORIGtext/$TRANStext/g;
													$phrase_found_counter++;
													}
												}
											}
										}

									if ($phrase_found_counter < 1)
										{
										open(ERRout, ">>$LANG_FILE_ERROR")
												|| die "Can't open $LANG_FILE_ERROR: $!\n";
										print ERRout "|$ORIGtext|\n";
										close(ERRout);
										}

									$LINESct++;

									if ($a =~ /00000$/i) {print "$a     $file[$LINESct]\n|$ORIGtext|$TRANStext|";}
									$a++;
									}
								$Tct++;
								}
							}
						### open the translation result file for writing ###
						open(out, ">$webroot/$file_path$US$lang_abb/$file_name")
								|| die "Can't open $webroot/$file_path$US$lang_abb/$file_name: $!\n";
				#		print out "Header(\"Content-type: text/html; charset=utf-8\");\n";
						$LINESct=0;
						foreach(@file)
							{
							print out "$file[$LINESct]";
							$LINESct++;
							}
						close(out);
						print "\n";
						print "File Written: $webroot/$file_path$US$lang_abb/$file_name\n";
						}
					else
						{
						print "File does not exist: $webroot/$file_path/$file_name\n";
						}
					$Fct++;
					}
				}
			}
		$i++;
		}
	}
