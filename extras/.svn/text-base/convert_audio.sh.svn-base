#! /bin/bash
cd /var/lib/asterisk/sounds/

if [ -f /var/lib/asterisk/sounds/$1.mp3 ] ; then
	sox -w -s $1.mp3 -r 8000 -c 1 $1.wav
	sox $1.wav -t gsm -r 8000 -b -c 1 $1.gsm
	sox $1.wav -t ul -r 8000 -b -c 1 $1.ul
	sox $1.wav -t al -r 8000 -b -c 1 $1.al
else
if [ -f /var/lib/asterisk/sounds/$1.wav ] ; then
	cp -f $1.wav ___$1.wav
	sox -w -s ___$1.wav -r 8000 -c 1 $1.wav
	rm -f ___$1.wav
	sox $1.wav -t gsm -r 8000 -b -c 1 $1.gsm
	sox $1.wav -t ul -r 8000 -b -c 1 $1.ul
	sox $1.wav -t al -r 8000 -b -c 1 $1.al
else
if [ -f /var/lib/asterisk/sounds/$1.gsm ] ; then
	sox -w -s $1.gsm -r 8000 -c 1 $1.wav
	sox $1.wav -t ul -r 8000 -b -c 1 $1.ul
	sox $1.wav -t al -r 8000 -b -c 1 $1.al
fi
fi
fi


