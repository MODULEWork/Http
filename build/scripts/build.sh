#!/bin/bash
#################
#Build Script
#This will build the project.
#NOTE: You need to have composer and phpunit installed for this
#################

# Colors:
txtgrn=$(tput setaf 2) # Green
txtylw=$(tput setaf 3) # Yellow
txtblu=$(tput setaf 4) # Blue
txtpur=$(tput setaf 5) # Purple
txtcyn=$(tput setaf 6) # Cyan
txtwht=$(tput setaf 7) # White
txtrst=$(tput sgr0) # Text reset

# Actual build
composer install --prefer-source --no-interaction --dev
phpunit
if [ ! -d "apigen" ]; then
	#Is not installed
	echo "Installing ApiGen..."
	wget https://github.com/downloads/apigen/apigen/ApiGen-2.8.0-standalone.zip --no-check-certificate
	unzip ApiGen-2.8.0-standalone.zip
	rm ApiGen-2.8.0-standalone.zip
	echo "${txtgrn}Done installing ApiGen{txtrst}"
fi
if [ ! -d "apigen" ]; then
	#Folder doesn' t exist after installation
	echo "${txtred}ERROR: Couldn' t find ApiGen installation${txtrst}"
	exit 1
else
	php apigen/apigen.php --source src/ --destination build/result/docs/
fi
if [ ! -f "phploc.phar" ]; then
	wget http://pear.phpunit.de/get/phploc.phar
fi
php phploc.phar src/ > build/result/phploc.txt
clear
echo "${txtgrn}Build is done.${txtrst}"

