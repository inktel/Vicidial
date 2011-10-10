#!/bin/bash

FILE=$1

cat $FILE | sed "s/\[.;..;..m//g" | tr -d "\033" > $FILE.nocolor