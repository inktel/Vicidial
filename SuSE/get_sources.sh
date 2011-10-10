#!/bin/bash

cd /usr/src

wget http://www.daveltd.com/src/util/ttyload/ttyload-0.4.4.tar.gz
wget http://superb-west.dl.sourceforge.net/sourceforge/ploticus/pl240src.tar.gz
wget http://bart.eaccelerator.net/source/0.9.5.2/eaccelerator-0.9.5.2.tar.bz2
wget http://superb-east.dl.sourceforge.net/sourceforge/mtop/mtop-0.6.6.tar.gz
wget http://download.berlios.de/sipsak/sipsak-0.9.6-1.tar.gz
wget http://ftp.digium.com/pub/asterisk/releases/asterisk-1.2.29.tar.gz
wget http://ftp.digium.com/pub/zaptel/releases/zaptel-1.2.24.tar.gz
#wget http://ftp.digium.com/pub/zaptel/releases/zaptel-1.2.26.tar.gz
wget http://ftp.digium.com/pub/libpri/releases/libpri-1.2.7.tar.gz
#wget http://downloads.digium.com/pub/asterisk/releases/asterisk-1.4.19.1.tar.gz
#wget http://downloads.digium.com/pub/libpri/libpri-1.4.3.tar.gz
#wget http://downloads.digium.com/pub/zaptel/zaptel-1.4.10.tar.gz

mkdir patches
cd patches

wget http://www.eflo.net/files/app_amd2.c
wget http://www.eflo.net/files/amd2.conf
wget http://www.eflo.net/files/meetme_DTMF_passthru-1.2.23.patch
wget http://www.eflo.net/files/meetme_volume_control_1.2.16.patch
wget http://www.eflo.net/files/cli_chan_concise_delimiter.patch
wget http://www.eflo.net/files/app_waitforsilence.c
wget http://www.eflo.net/files/enter.h
wget http://www.eflo.net/files/leave.h

