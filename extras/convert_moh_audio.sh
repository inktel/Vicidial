#! /bin/bash
if -f /var/lib/asterisk/mohmp3/$1.mp3; then
	cd /var/lib/asterisk/mohmp3/
	sox -w -s $1.mp3 -r 8000 -c 1 $1.wav
	sox $1.wav -t gsm -r 8000 -b -c 1 $1.gsm
	sox $1.wav -t ul -r 8000 -b -c 1 $1.ul
	sox $1.wav -t al -r 8000 -b -c 1 $1.al
	mkdir ../orig-mp3
	mv -f *.mp3 ../orig-mp3/
	mkdir ../quiet-mp3
	cd ../quiet-mp3
	cp ../mohmp3/$1.wav full_$1.wav
	sox full_$1.wav $1.wav vol 0.25
	sox $1.wav -t gsm -r 8000 -b -c 1 $1.gsm
	sox $1.wav -t ul -r 8000 -b -c 1 $1.ul
	sox $1 -t al -r 8000 -b -c 1 $1.al
	rm -f full_*.wav
else
if -f /var/lib/asterisk/orig-mp3/$1.mp3; then
	cd /var/lib/asterisk/mohmp3/
	cp /var/lib/asterisk/orig-mp3/$1.mp3 /var/lib/asterisk/mohmp3/
	sox -w -s $1.mp3 -r 8000 -c 1 $1.wav
	sox $1.wav -t gsm -r 8000 -b -c 1 $1.gsm
	sox $1.wav -t ul -r 8000 -b -c 1 $1.ul
	sox $1.wav -t al -r 8000 -b -c 1 $1.al
	rm -f $1.mp3 
	mkdir ../quiet-mp3
	cd ../quiet-mp3
	cp ../mohmp3/$1.wav full_$1.wav
	sox full_$1.wav $1.wav vol 0.25
	sox $1.wav -t gsm -r 8000 -b -c 1 $1.gsm
	sox $1.wav -t ul -r 8000 -b -c 1 $1.ul
	sox $1 -t al -r 8000 -b -c 1 $1.al
	rm -f full_*.wav
else
	echo -e "Syntax $0 filename"
fi
fi

