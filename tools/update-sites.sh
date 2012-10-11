#!/bin/sh
for file in $(find . -name "*.php"); do
	if test "$file" == "./lib/quoted_printable.php"; then continue; fi;
	php -l $file || exit
done

yui-compressor --type js -o static/script.js static/script_src.js
yui-compressor --type css -o static/style.css static/style_src.css

git commit -a
echo -n "git push/pull ? [Enter|Ctrl-C]"

read || exit
git pull || exit
git push
#ssh rg42.org 'cd /var/sites/lac2013; git pull'
ssh lac@linuxaudio.org 'cd /home/sites/lac.linuxaudio.org/2013/docroot; git pull'
