#!/bin/sh

#
# perl_modules.sh
#
# Developed by Vivian Alan <vivian@inttel.net>
# Copyright (c) 2008 International Telnet Inc.
# Licensed under terms of GNU General Public License.
# All rights reserved.
#
# Changelog:
# 2008-01-23 - created
#

# $Platon$

echo "Step 1 : Install Perl Modules Accept defaults in most cases"
echo ""

cpan -if MD5
cpan -if Digest::MD5
cpan -if Digest::SHA1
cpan -if readline
cpan -if Bundle::CPAN
# reload CPAN
# cpan ??
cpan reload
cpan -if DBI
cpan -if DBD::mysql
cpan -if Net::Telnet
cpan -if Time::HiRes
cpan -if Net::Server
cpan -if Unicode::Map
cpan -if Jcode
cpan -if Spreadsheet::WriteExcel
cpan -if OLE::Storage_Lite
cpan -if Proc::ProcessTable
cpan -if IO::Scalar
cpan -if Spreadsheet::ParseExcel

echo "Download and install asterisk-perl......"
echo ""

cd /usr/src/vicidial

wget http://asterisk.gnuinter.net/files/asterisk-perl-0.10.tar.gz

tar -xvzf asterisk-perl-0.10.tar.gz

cd asterisk-perl-0.10

perl Makefile.PL

make

make install

cd /usr/src/vicidial


echo "Download and install sox mix source......"
echo ""

wget http://easynews.dl.sourceforge.net/sourceforge/sox/sox-12.17.9.tar.gz

tar -xvzf sox-12.17.9.tar.gz

cd sox-12.17.9

./configure

make

make install

cd /usr/src/vicidial

echo "Dowload and install ttyload...."
echo ""

wget http://www.daveltd.com/src/util/ttyload/ttyload-0.4.4.tar.gz

tar -xvzf ttyload-0.4.4.tar.gz

cd ttyload-0.4.4

make

make install

cd /usr/src/vicidial

echo "Update ntpd libraries....."
echo ""

yum install ntp.i386

echo "Download and install iftop and dependencies...."
echo ""

wget http://www.tcpdump.org/release/libpcap-0.9.8.tar.gz

tar -xvzf libpcap-0.9.8.tar.gz

cd libpcap-0.9.8

./configure

make

make install

cd /usr/src/vicidial

wget http://www.ex-parrot.com/~pdw/iftop/download/iftop-0.17.tar.gz


tar -xvzf iftop-0.17.tar.gz

cd iftop-0.17

./configure

make

make install

echo "Download and install ploticus, needs X11 installed....."
echo ""

cd /usr/src/vicidial

wget http://internap.dl.sourceforge.net/sourceforge/ploticus/pl240src.tar.gz

tar -xvzf pl240src.tar.gz

cd pl240src/src

make clean

make

make install

echo "Download and install balance from inlab.de........"
echo ""

cd /usr/src/vicidial

wget http://www.inlab.de/balance-3.40.tar.gz

tar -xvzf balance-3.40.tar.gz

cd balance-3.40

make

make install
