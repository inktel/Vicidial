#!/bin/bash

#######################################################################
#                                                                     #
# convert_latin1_db_to_utf8.sh  Version 0.0.1                         #
#                                                                     #
# Converts an existing latin1 mysql database into utf8                #
#                                                                     #
# Copyright (C) 2008  Michael Cargile, Vicidial Group Inc.            #
#                                                                     #
# This program is free software; you can redistribute it and/or       #
# modify it under the terms of the GNU General Public License         #
# as published by the Free Software Foundation; either version 2      #
# of the License, or (at your option) any later version.              #
#                                                                     #
# This program is distributed in the hope that it will be useful,     #
# but WITHOUT ANY WARRANTY; without even the implied warranty of      #
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the       #
# GNU General Public License for more details.                        #
#                                                                     #
# You should have received a copy of the GNU General Public License   #
# along with this program; if not, write to:                          #
#        Free Software Foundation, Inc.                               #
#        51 Franklin Street                                           #
#        Fifth Floor                                                  #
#        Boston, MA  02110-1301, USA.                                 #
#                                                                     #
#######################################################################

if [ "$1" == "--help" ]
then
	echo "Converts an existing latin1 mysql database into utf8"
	echo "usage:"
	echo "convert_latin1_db_to_utf8.sh database database_host data_user database_password"

	exit
fi

DBNAME=$1
DBHOST=$2
DBUSER=$3
DBPASS=$4

MYDUMP=`which mysqldump`
MYSQL=`which mysql`
ICONV=`which iconv`

FILE="back_up.sql"
FILEUTF8="back_up_uft8.sql"

`$MYSQLDUMP -h $DBHOST -u $DBUSER --password=$DBPASS --default-character-set=latin1 -c --insert-ignore --skip-set-charset $DBNAME > $FILE`

`$ICONV -f ISO-8859-1 -t UTF-8 $FILE > $FILEUTF8`

`$MYSQL -h $DBHOST -u $DBUSER --password=$DBPASS --execute="DROP DATABASE $DBNAME; CREATE DATABASE $DBNAME CHARACTER SET utf8 COLLATE utf8_general_ci;"`

`$MYSQL -h $DBHOST -u $DBUSER --password=$DBPASS --max_allowed_packet=16M -p --default-character-set=utf8 $DBNAME < $FILEUTF8`

# Uncomment the next two lines to delete the backup and the converted backup
#rm $FILE
#rm $FILEUTF8
