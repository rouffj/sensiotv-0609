#!/bin/sh
php build/build_phar.php
chmod +x build/imdb.phar
cp build/imdb.phar /usr/local/bin/imdb
echo "imdb.phar file compiled + accessible from commandline as /usr/local/bin/imdb !"
